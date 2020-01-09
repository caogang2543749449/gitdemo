<?php

namespace app\admin\controller;

use app\common\controller\Backend;
use fast\Http;
use think\Db;
use think\Exception;
use think\exception\PDOException;
use think\exception\ValidateException;

/**
 * 地图显示
 * @internal
 */
class ThirdService extends Backend
{

    public function _initialize()
    {
        parent::_initialize();

    }

    /**
     * 查找地图
     * @throws \think\Exception
     */
    public function yahooMap()
    {
        $appid = 'dj00aiZpPXBHMG5NTk5JOGo2eSZzPWNvbnN1bWVyc2VjcmV0Jng9MGI-';
        $appService = 'https://map.yahooapis.jp/geocode/V1/geoCoder?appid=%s&output=json&recursive=true&query=%s';

        $address = $this->request->request('address');
        $coordApiUrl = sprintf($appService, $appid, $address);

        $res = Http::get($coordApiUrl);
        if (!$res) {
            $this->error(__('Unknown data format'));
        }
        $resArray = json_decode($res, true);
        if( array_key_exists('Error', $resArray ) ) {
            $this->error($resArray['Error']['Message']);
        }
        if(isset($resArray['Feature'])) {
            $coordInfo = $resArray['Feature'][0]['Geometry']['Coordinates'];
            $this->assignconfig("coordinfo", explode(',', $coordInfo));
        } else {
            $this->assignconfig("coordinfo", [139.75358630,35.69404120]);
        }

        $this->assignconfig("address", $address);
        $this->assignconfig("appid", $appid);
        return $this->view->fetch('common/map');
    }

    /**
     * 查找邮编
     * @throws \think\Exception
     */
    public function zipSearch()
    {
        $zipService = 'http://zipcloud.ibsnet.co.jp/api/search?zipcode=';

        $zipcode = $this->request->request('zipcode');

        $res = Http::get($zipService.$zipcode);
        if (!$res) {
            $this->error(__('Unknown data format'));
        }

        $data = json_decode($res, true);
        if( $data['status']===200 ) {
            if( $data['results'] === null ) {
                $this->error(__('No results were found'));
            }
            $this->success('', null, $data['results']);
        } else {
            $this->error($data['message']);
        }
    }

}
