<?php


namespace app\admin\controller;


use app\common\controller\Backend;
use fast\Date;
use think\Config;
use think\Db;
use think\exception\HttpResponseException;
use think\Response;

class DataStatistics extends Backend
{

    //统计维度 - 时间线环比
    const TYPE_TIMELINE = "1";
    //统计维度 - 时间段同比
    const TYPE_PERIOD = "2";

    //时间线统计间隔 - 天
    const SCALE_TIMELINE_DAY = "1";
    //时间线统计间隔 - 周
    const SCALE_TIMELINE_WEEK = "2";
    //时间线统计间隔 - 月
    const SCALE_TIMELINE_MONTH = "3";
    //时间线统计间隔 - 年
    const SCALE_TIMELINE_YEAR = "4";

    //时间段统计间隔 - 小时段
    const SCALE_PERIOD_HOUR = "1";
    //时间段统计间隔 - 周中日
    const SCALE_PERIOD_WEEKDAY = "2";
    //时间段统计间隔 - 月份
    const SCALE_PERIOD_MONTH = "3";

    //统计种类
    const ITEM_USER_JOIN    = "user_join";
    const ITEM_NORMAL_OPEN  = "normal_open";
    const ITEM_SCAN_OPEN    = "scan_open";
    const ITEM_SHARE        = "share";
    const ITEM_COUPON_USED_COUNT        = "coupon_used_count";
    const ITEM_BUTTON_CLICK = "button_click";


    //时区
    private $timezone = null;
    //商家
    private $merchant_id = 0;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = model('data_statistics');

        $timezoneStr = Config::get('site')['timezone'];
        if( empty($timezoneStr) ) {
            $timezoneStr = 'Asia/Tokyo';
        }
        $this->timezone = timezone_open($timezoneStr);

        if (session('?merchant')) {
            $this->merchant_id = session('merchant')['id'];
            $this->view->assign('merchant_id', $this->merchant_id);
        } else {
            $this->view->assign('merchant_id', "0");
        }
    }

    /**
     * platform统计
     */
    public function platform()
    {
        //设置过滤方法
        $this->request->filter(['strip_tags']);

        if ($this->request->isAjax()) {
            $search = $this->request->post("search/a");

            $merchant_id = $search["merchant_id"];

            $type = $search["type"];
            $baseTime = empty($search["basetime"])?
                date_create("now")->setTime(0,0,0)->getTimestamp():strtotime($search["basetime"]);
            $termLength = $search["term_length"];
            $item = $search["item"];

            $result = [];
            if(self::TYPE_TIMELINE === $type) {
                $scale = $search["scale_timeline"];
                $this->getTimeLineData($result, $merchant_id, $baseTime, $scale, $termLength, $item);
            } else {
                $scale = $search["scale_period"];
                $this->getTimePeriodData($result, $merchant_id, $baseTime, $scale, $termLength, $item);
            }

            //总用户数
            $totaluser = $this->model
                ->whereRaw($merchant_id==0?'1=1':'merchant_id='.$merchant_id)
                ->where('item', '=', self::ITEM_USER_JOIN)
                ->group('item')
                ->field('SUM(count) as count')
                ->find()['count'];

            //扫码打开数
            $scanopen = $this->model
                ->whereRaw($merchant_id==0?'1=1':'merchant_id='.$merchant_id)
                ->where('item', '=', self::ITEM_SCAN_OPEN)
                ->group('item')
                ->field('SUM(count) as count')
                ->find()['count'];

            //二次打开数
            $normalopen = $this->model
                ->whereRaw($merchant_id==0?'1=1':'merchant_id='.$merchant_id)
                ->where('item', '=', self::ITEM_NORMAL_OPEN)
                ->group('item')
                ->field('SUM(count) as count')
                ->find()['count'];

            //总用户数
            $share = $this->model
                ->whereRaw($merchant_id==0?'1=1':'merchant_id='.$merchant_id)
                ->where('item', '=', self::ITEM_SHARE)
                ->group('item')
                ->field('SUM(count) as count')
                ->find()['count'];

            $head = [''=>0];
            $result = array_merge($result, [' '=>0]);
            $result = array_merge($head, $result);
            $legend = null;
            switch ($item) {
                case self::ITEM_USER_JOIN:
                    $legend = __("User sign-up");
                    break;
                case self::ITEM_SCAN_OPEN:
                    $legend = __("Scan open");
                    break;
                case self::ITEM_NORMAL_OPEN:
                    $legend = __("Normal open");
                    break;
                case self::ITEM_SHARE:
                    $legend = __("Share");
                    break;
                case self::ITEM_COUPON_USED_COUNT:
                    $legend = __("Coupon used count");
                    break;
                default:
                    $legend = __("Function usage");
                    break;
            }
            $this->success('', null, [
                'columns'    => json_encode(array_keys($result)),
                'values'     => json_encode(array_values($result)),
                'totaluser'  => empty($totaluser)?0:$totaluser,
                'scanopen'   => empty($scanopen)?0:$scanopen,
                'normalopen' => empty($normalopen)?0:$normalopen,
                'share'      => empty($share)?0:$share,
                'legend'     => $legend
            ]);

        }
        return $this->view->fetch();
    }

    /**
     * 获取时间线用户统计
     * @param $result
     * @param $merchant_id
     * @param $baseTime
     * @param $scale
     * @param $termLength
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    private function getTimeLineData(&$result, $merchant_id, $baseTime, $scale, $termLength, $item)
    {
        $interval = 0;
        $outputFormat = "Y-m-d";
        switch ($scale) {
            case self::SCALE_TIMELINE_DAY:
                $interval = Date::DAY;
                break;
            case self::SCALE_TIMELINE_WEEK:
                $interval = Date::WEEK;
                $baseTime = date_modify(date_create(date($outputFormat, $baseTime)), 'monday this week')->getTimestamp();
                break;
            case self::SCALE_TIMELINE_MONTH:
                $interval = Date::MONTH;
                $outputFormat = "Y-m";
                break;
            case self::SCALE_TIMELINE_YEAR:
                $interval = Date::YEAR;
                $outputFormat = "Y";
                break;
            default:
                $this->error('Invalid time scale');
        }
        $multiSearch = false;
        if(self::ITEM_BUTTON_CLICK === $item) {
            $multiSearch = true;
        }
        $startTime = $baseTime+$interval-($interval*$termLength);
        for ($i=0; $i<$termLength; $i++) {
            $endTime = $startTime + $interval;
            $rows = $this->model
                ->whereRaw( $merchant_id == '0'?'1=1':'merchant_id='.$merchant_id)
                ->whereRaw($multiSearch?'item like "'.$item.'%"':'item = "'.$item.'"')
                ->where('s_time', 'BETWEEN', [$startTime, $endTime])
                ->select();
            $key = date($outputFormat, $startTime);
            if(!$rows) {
                $result[$key] = 0;
            } else {
                $count = 0;
                foreach ($rows as $row) {
                    $count += $row['count'];
                }
                $result[$key] = $count;
            }
            $startTime = $endTime;
        }
    }

    private function getTimePeriodData(&$result, $merchant_id, $baseTime, $scale, $termLength, $item)
    {
        $interval = 0;
        $groupBy = 's_hour';
        $outputFormat = 'Hour %s';
        $isWeekday = false;
        $outputFlg = '%2d';
        $minScale = 0;
        $maxScale = 23;
        switch ($scale) {
            case self::SCALE_PERIOD_HOUR:
                $interval = Date::DAY;
                break;
            case self::SCALE_PERIOD_WEEKDAY:
                $interval = Date::WEEK;
                $groupBy = 's_weekday';
                $outputFormat = 'Weekday %s';
                $outputFlg = '%s';
                $isWeekday = true;
                $maxScale = 6;
                break;
            case self::SCALE_PERIOD_MONTH:
                $interval = Date::MONTH;
                $groupBy = 's_month';
                $outputFormat = 'Month %s';
                $minScale = 1;
                $maxScale = 12;
                break;
            default:
                $this->error('Invalid time scale');
        }
        $multiSearch = false;
        if(self::ITEM_BUTTON_CLICK === $item) {
            $multiSearch = true;
        }
        $startTime = $baseTime+$interval-($interval*$termLength);
        $rows = $this->model
            ->whereRaw( $merchant_id == '0'?'1=1':'merchant_id='.$merchant_id)
            ->whereRaw($multiSearch?'item like "'.$item.'%"':'item = "'.$item.'"')
//            ->where('s_time', 'BETWEEN', [$startTime, $baseTime])
            ->group($groupBy)
            ->field($groupBy.', SUM(count) as count')
            ->select();
        $resultTmp = [];
        foreach ($rows as $row) {
            $resultTmp[$row[$groupBy]] = 0+$row['count']; //str to int
        }
        // 补全不存在的列
        for ($i=$minScale; $i<=$maxScale; $i++) {
            if(!array_key_exists($i, $resultTmp)) {
                $resultTmp[$i] = 0;
            }
        }
        // 按key大小排列
        ksort($resultTmp);
        // 转换key为文字
        foreach ( $resultTmp as $k=>$v) {
            $keyVal = $isWeekday?__('Weekday '.$k):$k;
            $key = __($outputFormat, sprintf($outputFlg, $keyVal));
            $result[$key] = $v;
        }
        unset($resultTmp);
    }

    /**
     * regist 统计
     */
    public function regist() {
        $this->request->filter(['strip_tags']);

        if ($this->request->isAjax()) {
            $search = $this->request->post("search/a");
            $date = date("Y-m-d");
            if(isset($search["basetime"]) && !empty($search["basetime"])) {
                $date = date("Y-m-d", strtotime($search["basetime"]));
            }
            $sql = <<<EOL
        select
            md.id as 'id'
            , md.merchant_name as 'merchant_name'
            , md.s_date as 'date'
            , case
                when d.s_count is not null then s_count
                else 0
            end as 'Regist Count'
        from 
            (
                select 
                    m.id, m.merchant_name,s_date 
                from (
                    select id, merchant_name from `eb_merchant` 
                    union 
                    select 0 as id, 'その他チャンネル' as localname from dual
                ) m
                inner join (
                    select CONCAT (s_year, '-', LPAD (s_month, 2, 0), '-', LPAD (s_day, 2, 0)) as s_date from `eb_data_statistics` group by s_year, s_month, s_day
                ) dt
                where dt.s_date = '$date'
            ) md  -- 酒店每天日期
        left join
            (
                select
                    merchant_id
                    , CONCAT (s_year, '-', LPAD (s_month, 2, 0), '-', LPAD (s_day, 2, 0)) as s_date
                    , SUM(COUNT) as s_count
                from
                    `eb_data_statistics`
                where 
                    item = 'user_join'
                    group by merchant_id, s_year, s_month, s_day
            ) d  -- 每天数据统计
        on 
            md.id = d.merchant_id
            and md.s_date = d.s_date
        order by 
            md.s_date desc
            , id

EOL;
            $results = Db::query($sql);
            // for 下载
            if(!empty($search["download"])) {
//                $fileName = 'register_'.$date.'csv';
//                $herder = [
//                    'Content-Type'=>'application/vnd.ms-excel',
//                    'Content-Disposition'=>'attachment;filename=' . $fileName,
//                    'Cache-Control'=>'max-age=0',
//                ];
                $content = '"id","'.__('merchant_name').'","'.__('Base time').'","'.__('Regist Count')."\"\r\n";
                foreach ($results as $result) {
                    $content .= $result["id"] . ',"' . $result["merchant_name"] . '","' . $result["date"] . '",' . $result["Regist Count"] . "\r\n";
                }
                $this->success('', null, [$results, $content]);
            }
            $this->success('', null, [$results]);
        }
        return $this->view->fetch();
    }

}