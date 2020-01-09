<?php

namespace app\admin\controller\api;

use app\common\library\TranslateUtil;
use app\admin\common\MerchantBackend;

/**
 * /api/translate/test1
 */
class Translate extends MerchantBackend
{
    public function index()
    {
        if (!$this->request->isPost()) {
            return null;
        }
        if (!$this->request->isAjax())
        {
            return null;
        }
        $params = $this->request->post();
        $res = TranslateUtil::instance()->translate($params);
        return json($res);
    }

}
