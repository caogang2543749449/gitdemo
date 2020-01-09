<?php

namespace app\admin\controller;

use app\common\controller\Backend;
use think\Config;
use fast\Date;
use think\Db;

/**
 * 控制台
 *
 * @icon fa fa-dashboard
 * @remark 用于展示当前系统中的统计数据、统计报表及重要实时数据
 */
class Dashboard extends Backend
{

    /**
     * 绑定商家查询语句
     */
    protected $merchantSearch = "1 = 1";
    /**
     * 关系商家查询语句
     */
    protected $relationMerchantSearch = "1 = 1";

    public function _initialize()
    {
        parent::_initialize();

        if( !$this->auth->isSuperAdmin() && session('?org_merchant_id') ) {
            $merchant_id = session('org_merchant_id');
            $ids = model('merchant')
                ->where('id|pid', $merchant_id)
                ->select();
            if( $ids ) {
                $inId = '';
                foreach ($ids as $row) {
                    $inId .= $row->id.',';
                }
                $this->relationMerchantSearch = 'merchant_id in ('.substr($inId, 0, strlen($inId)-1).')';
            }
        }
        if(session('?merchant')){
            $merchant_id = session('merchant')['id'];
            $this->merchantSearch = 'merchant_id = '.$merchant_id;
        }

    }

    private function getScanUserCount() {
        $sql = <<<EOL
SELECT count(1) as ct
FROM (
	SELECT user_id, merchant_id, MIN(action_type) AS action_type
	FROM eb_user_action_log
	where $this->relationMerchantSearch
    AND action_type IN( 'scan_open', 'normal_open')
	GROUP BY user_id, merchant_id
) m
WHERE action_type = 'scan_open'
EOL;
            $results = Db::query($sql);
            if($results && isset($results[0]) && isset($results[0]['ct'])) {

                return $results[0]['ct'];
            }
            return '0';
    }
    /**
     * 查看
     * @throws
     */
    public function index()
    {
        $user_regist_by_scan = $this->getScanUserCount();

        //count of coupon used
        $totalCouponUsed = model('user_coupon')
            ->where(function($query){
                $query->where('merchant_coupon_id', 'in', function($query){
                    $query->table('eb_merchant_coupon')->whereRaw($this->relationMerchantSearch)->field('id');
                });
            })
            ->where('status', '1')
            ->count();
            //count of coupon used
        $totalCoupon = model('user_coupon')
        ->where(function($query){
            $query->where('merchant_coupon_id', 'in', function($query){
                $query->table('eb_merchant_coupon')->whereRaw($this->relationMerchantSearch)->field('id');
            });
        })
            ->count();
        //总用户数
        $totaluser = model('user_merchant')
            ->whereRaw($this->relationMerchantSearch)
            ->group('user_id')
            ->count();
        //总访问数
        $totalAccess = model('user_action_log')
            ->whereRaw($this->relationMerchantSearch)
            ->where('action_type', 'IN', ['normal_open','scan_open'])
            ->count();
        //翻译君小程序打开数
        $totalTransAccess = model('user_action_log')
            ->whereRaw($this->relationMerchantSearch)
            ->where([
                'action_type' => 'button_click',
                'action' => ['like','%译']
            ])
            ->count();
        //分享数
        $totalShare = model('user_action_log')
            ->whereRaw($this->relationMerchantSearch)
            ->where('action_type', '=', 'share')
            ->count();

        //当天问询数
        $todayTime = Date::unixtime('day', 0);
        $todayMessage = model('merchant_msg')
            ->whereRaw($this->relationMerchantSearch)
            ->where('createtime', '>=', $todayTime)
            ->count();

        //未关闭问询数
        $totalOpenMessage = model('merchant_msg')
            ->whereRaw($this->relationMerchantSearch)
            ->where('status', '<>', '9')
            ->count();

        //最近一周用户数
        $lastWeekTime = Date::unixtime('day', -7);
        $lastWeekUser = model('user_merchant')
            ->whereRaw($this->relationMerchantSearch)
            ->where('createtime', '>', $lastWeekTime)
            ->group('user_id')
            ->count();
        //最近一月新用户
        $lastMonthTime = Date::unixtime('month', -1);
        $lastMonthUser = model('user_merchant')
            ->whereRaw($this->relationMerchantSearch)
            ->where('createtime', '>', $lastMonthTime)
            ->group('user_id')
            ->count();
        //最近一周用户增长率
        $lastSecondWeekTime = Date::unixtime('day', -14);
        $lastSecondWeekUser = model('user_merchant')
            ->whereRaw($this->relationMerchantSearch)
            ->where('createtime', 'BETWEEN', [$lastSecondWeekTime, $lastWeekTime,])
            ->group('user_id')
            ->count();
        $weekRatio = $lastSecondWeekUser==0?'-':round($lastWeekUser*100/$lastSecondWeekUser - 100, 1);
        //最近一月用户增长率
        $lastSecondMonthTime = Date::unixtime('month', -2);
        $lastSecondMonthUser = model('user_merchant')
            ->whereRaw($this->relationMerchantSearch)
            ->where('createtime', 'BETWEEN', [$lastSecondMonthTime, $lastMonthTime])
            ->group('user_id')
            ->count();
        $monthRatio = $lastSecondMonthUser==0?'-':round($lastMonthUser*100/$lastSecondMonthUser - 100, 1);

        //最近一周每天新增用户数、每天打开数
        $weekUserList = $weekAccessList = [];
        $dayStime = $lastWeekTime+Date::DAY;
        for ($i = 0; $i < 7; $i++)
        {
            $day = date("m-d", $dayStime);
            $weekUserList[$day] = model('user_merchant')
                ->whereRaw($this->relationMerchantSearch)
                ->where('createtime', 'BETWEEN', [$dayStime, $dayStime+Date::DAY])
                ->group('user_id')
                ->count();
            $weekAccessList[$day] = model('user_action_log')
                ->whereRaw($this->relationMerchantSearch)
                ->where([
                    'action_type' => ['in', ['normal_open','scan_open']],
                    'createtime'  => ['BETWEEN', [$dayStime, $dayStime+Date::DAY]]
                ])
                ->count();
            $dayStime += Date::DAY;
        }

        //超级管理员用系统信息
        $hooks = config('addons.hooks');
        $uploadmode = isset($hooks['upload_config_init']) && $hooks['upload_config_init'] ? implode(',', $hooks['upload_config_init']) : 'local';
        $addonComposerCfg = ROOT_PATH . '/vendor/karsonzhang/fastadmin-addons/composer.json';
        Config::parse($addonComposerCfg, "json", "composer");
        $config = Config::get("composer");
        $addonVersion = isset($config['version']) ? $config['version'] : __('Unknown');

        $this->view->assign([
            'totalCouponUsed'  => $totalCouponUsed,
            'totalCoupon'      => $totalCoupon,
            'totaluser'        => $totaluser,
            'userRegistByScan' => $user_regist_by_scan,
            'totalaccess'      => $totalAccess,
            'totaltrans'       => $totalTransAccess,
            'totalshare'       => $totalShare,
            'todaymsg'         => $todayMessage,
            'totalopenmsg'     => $totalOpenMessage,
            'lastmonthuser'    => $lastMonthUser,
            'lastweekuser'     => $lastWeekUser,
            'weekratio'        => $weekRatio.'%',
            'monthratio'       => $monthRatio.'%',
            'weekuserlist'     => $weekUserList,
            'weekaccesslist'   => $weekAccessList,
            'addonversion'     => $addonVersion,
            'uploadmode'       => $uploadmode
        ]);

        return $this->view->fetch();
    }

    public function getLastUserData() {
        //最近2天每天新增用户数、每天打开数
        $userList = $accessList = $dayList = [];
        $dayStime = \fast\Date::unixtime('day', -1);
        for ($i = 0; $i < 2; $i++)
        {
            $dayList[$i] = date("m-d", $dayStime);
            $userList[$i] = model('user_merchant')
                ->whereRaw($this->relationMerchantSearch)
                ->where('createtime', 'BETWEEN', [$dayStime, $dayStime+86400])
                ->group('user_id')
                ->count();
            $accessList[$i] = model('user_action_log')
                ->whereRaw($this->relationMerchantSearch)
                ->where([
                    'action_type' => ['in', ['normal_open','scan_open']],
                    'createtime'  => ['BETWEEN', [$dayStime, $dayStime+86400]]
                ])
                ->count();
            $dayStime += 86400;
        }

        //未读问询数
        $newMessage = model('merchant_msg')
            ->whereRaw($this->merchantSearch)
            ->where('status', '=', '1')
            ->count();


        $this->success('', null, [
            'dayList'    => $dayList,
            'userList'   => $userList,
            'accessList' => $accessList,
            'newMsg'     => $newMessage,
        ]);

    }

}
