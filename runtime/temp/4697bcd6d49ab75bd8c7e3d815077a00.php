<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:105:"D:\phpStudy\PHPTutorial\WWW\fastadmin3\public/../application/admin\view\merchant\merchant_prop\index.html";i:1577093820;s:81:"D:\phpStudy\PHPTutorial\WWW\fastadmin3\application\admin\view\layout\default.html";i:1577093820;s:78:"D:\phpStudy\PHPTutorial\WWW\fastadmin3\application\admin\view\common\meta.html";i:1577093820;s:80:"D:\phpStudy\PHPTutorial\WWW\fastadmin3\application\admin\view\common\script.html";i:1577093820;}*/ ?>
<!DOCTYPE html>
<html lang="<?php echo $config['language']; ?>">
    <head>
        <meta charset="utf-8">
<title><?php echo (isset($title) && ($title !== '')?$title:''); ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
<meta name="renderer" content="webkit">

<link rel="shortcut icon" href="/assets/img/favicon.ico" />
<!-- Loading Bootstrap -->
<link href="/assets/css/backend<?php echo \think\Config::get('app_debug')?'':'.min'; ?>.css?v=<?php echo \think\Config::get('site.version'); ?>" rel="stylesheet">

<!-- HTML5 shim, for IE6-8 support of HTML5 elements. All other JS at the end of file. -->
<!--[if lt IE 9]>
  <script src="/assets/js/html5shiv.js"></script>
  <script src="/assets/js/respond.min.js"></script>
<![endif]-->
<script type="text/javascript">
    var require = {
        config:  <?php echo json_encode($config); ?>
    };
</script>
    </head>

    <body class="inside-header inside-aside <?php echo defined('IS_DIALOG') && IS_DIALOG ? 'is-dialog' : ''; ?>">
        <div id="main" role="main">
            <div class="tab-content tab-addtabs">
                <div id="content">
                    <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                            <section class="content-header hide">
                                <h1>
                                    <?php echo __('Dashboard'); ?>
                                    <small><?php echo __('Control panel'); ?></small>
                                </h1>
                            </section>
                            <?php if(!IS_DIALOG && !$config['fastadmin']['multiplenav']): ?>
                            <!-- RIBBON -->
                            <div id="ribbon">
                                <ol class="breadcrumb pull-left">
                                    <li><a href="dashboard" class="addtabsit"><i class="fa fa-dashboard"></i> <?php echo __('Dashboard'); ?></a></li>
                                </ol>
                                <ol class="breadcrumb pull-right">
                                    <?php foreach($breadcrumb as $vo): ?>
                                    <li><a href="javascript:;" data-url="<?php echo $vo['url']; ?>"><?php echo $vo['title']; ?></a></li>
                                    <?php endforeach; ?>
                                </ol>
                            </div>
                            <!-- END RIBBON -->
                            <?php endif; ?>
                            <div class="content">
                                <div class="panel panel-default panel-intro">
    <div class="panel-heading input-group">
        <ul class="nav nav-tabs ">
            <li class="active"><a href="#prop" data-value="" data-toggle="tab"><?php echo __('Merchant Properties'); ?></a></li>
            <li><a href="#qrcode" data-value="" data-toggle="tab"><?php echo __('Wxapp qr code'); ?></a></li>
        </ul>
        <div class="input-group-btn">
            <a href="/merchant/merchant_prop/edit?ids=<?php echo $row['id']; ?>" class="btn btn-success btn-edit <?php echo $auth->check('merchant/merchant_prop/edit')?'':'hide'; ?>" title="<?php echo __('Edit'); ?>" ><i class="fa fa-pencil"></i> <?php echo __('Edit'); ?></a>
        </div>
    </div>


    <div class="panel-body">
        <div id="myTabContent" class="tab-content">
            <div class="tab-pane fade active in" id="prop">
                <div class="form-horizontal">
                    <input id="c-id" name="row[id]" type="hidden" value="<?php echo htmlentities($row['id']); ?>">
                    <div class="form-group">
                        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Merchant_name'); ?>:</label>
                        <div class="col-xs-12 col-sm-8">
                            <span id="c-merchant_name" class="form-control"><?php echo htmlentities($row['merchant_name']); ?></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Merchant_localname'); ?>:</label>
                        <div class="col-xs-12 col-sm-8">
                            <span id="c-merchant_localname" class="form-control"><?php echo htmlentities($row['merchant_localname']); ?></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-xs-12 col-sm-2"><?php echo __('MerchantProperties.Logoimage'); ?>:</label>
                        <div class="col-xs-12 col-sm-8">
                            <ul class="row list-inline plupload-preview" id="p-logoimage">
                                <li class="col-xs-3">
                                    <a href="<?php echo $row['MerchantProperties']['logoimage']; ?>" data-url="<?php echo $row['MerchantProperties']['logoimage']; ?>" target="_blank" class="thumbnail">
                                        <img src="<?php echo $row['MerchantProperties']['logoimage']; ?>" onerror="this.src='https://tool.fastadmin.net/icon/'+'<?php echo $row['MerchantProperties']['logoimage']; ?>'.split('.').pop()+'.png';this.onerror=null;" class="img-responsive">
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-xs-12 col-sm-2"><?php echo __('MerchantProperties.Url'); ?>:</label>
                        <div class="col-xs-12 col-sm-8">
                            <span id="c-url" class="form-control"><?php echo htmlentities($row['MerchantProperties']['url']); ?></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-xs-12 col-sm-2"><?php echo __('MerchantProperties.Descimages'); ?>:</label>
                        <div class="col-xs-12 col-sm-8">
                            <ul class="row list-inline plupload-preview" id="p-descimages">
                                <?php if(is_array($descImages) || $descImages instanceof \think\Collection || $descImages instanceof \think\Paginator): if( count($descImages)==0 ) : echo "" ;else: foreach($descImages as $key=>$vo): ?>
                                <li class="col-xs-3">
                                    <a href="<?php echo $vo; ?>" data-url="<?php echo $vo; ?>" target="_blank" class="thumbnail">
                                        <img src="<?php echo $vo; ?>" onerror="this.src='https://tool.fastadmin.net/icon/'+'<?php echo $vo; ?>'.split('.').pop()+'.png';this.onerror=null;" class="img-responsive">
                                    </a>
                                </li>
                                <?php endforeach; endif; else: echo "" ;endif; ?>
                            </ul>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-xs-12 col-sm-2"><?php echo __('MerchantProperties.Tel'); ?>:</label>
                        <div class="col-xs-12 col-sm-8">
                            <span id="c-tel" class="form-control"><?php echo htmlentities($row['MerchantProperties']['tel']); ?></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-xs-12 col-sm-2"><?php echo __('MerchantProperties.Fax'); ?>:</label>
                        <div class="col-xs-12 col-sm-8">
                            <span id="c-fax" class="form-control"><?php echo htmlentities($row['MerchantProperties']['fax']); ?></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-xs-12 col-sm-2"><?php echo __('MerchantProperties.Email'); ?>:</label>
                        <div class="col-xs-12 col-sm-8">
                            <span id="c-email" class="form-control"><?php echo htmlentities($row['MerchantProperties']['email']); ?></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-xs-12 col-sm-2"><?php echo __('MerchantProperties.Postcode'); ?>:</label>
                        <div class="col-xs-12 col-sm-8">
                            <span id="c-postcode" class="form-control"><?php echo htmlentities($row['MerchantProperties']['postcode']); ?></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-xs-12 col-sm-2"><?php echo __('MerchantProperties.Address1'); ?>:</label>
                        <div class="col-xs-12 col-sm-8">
                            <span id="c-address1" class="form-control"><?php echo htmlentities($row['MerchantProperties']['address1']); ?></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-xs-12 col-sm-2"><?php echo __('MerchantProperties.Address2'); ?>:</label>
                        <div class="col-xs-12 col-sm-8">
                            <span id="c-address2" class="form-control"><?php echo htmlentities($row['MerchantProperties']['address2']); ?></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-xs-12 col-sm-2"><?php echo __('MerchantProperties.Address3'); ?>:</label>
                        <div class="col-xs-12 col-sm-8">
                            <span id="c-address3" class="form-control"><?php echo htmlentities($row['MerchantProperties']['address3']); ?></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-xs-12 col-sm-2"><?php echo __('MerchantProperties.Address4'); ?>:</label>
                        <div class="col-xs-12 col-sm-8">
                            <span id="c-address4" class="form-control"><?php echo htmlentities($row['MerchantProperties']['address4']); ?></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-xs-12 col-sm-2"><?php echo __('MerchantProperties.Gis_coord'); ?>:</label>
                        <div class="col-xs-12 col-sm-8">
                            <span id="c-gis_coord" class="form-control"><?php echo htmlentities($row['MerchantProperties']['gis_coord']); ?></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Service time'); ?>:</label>
                        <div class="col-xs-12 col-sm-8">
                            <span id="c-service_stime" size="20" class="form-control"><?php echo substr($row['MerchantProperties']['service_stime'],0,5); ?> - <?php echo substr($row['MerchantProperties']['service_etime'],0,5); ?></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-xs-12 col-sm-2"><?php echo __('MerchantProperties.Notes'); ?>:</label>
                        <div class="col-xs-12 col-sm-8">
                            <div id="c-notes" class="form-control" <?php if($row['MerchantProperties']['notes']): ?>style="height: auto;"<?php endif; ?>><?php echo str_replace(PHP_EOL,'<br>',$row['MerchantProperties']['notes']); ?></div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-xs-12 col-sm-2"><?php echo __('MerchantPropertiesHotel.Checkin_time'); ?>:</label>
                        <div class="col-xs-12 col-sm-8">
                            <span id="c-checkin_stime" size="20" class="form-control"><?php echo substr($row['MerchantPropertiesHotel']['checkin_stime'],0,5); ?> - <?php echo substr($row['MerchantPropertiesHotel']['checkin_etime'],0,5); ?></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-xs-12 col-sm-2"><?php echo __('MerchantPropertiesHotel.Checkout_time'); ?>:</label>
                        <div class="col-xs-12 col-sm-8">
                            <div id="c-checkout_stime" size="20" class="form-control"><?php echo substr($row['MerchantPropertiesHotel']['checkout_stime'],0,5); ?> - <?php echo substr($row['MerchantPropertiesHotel']['checkout_etime'],0,5); ?></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-xs-12 col-sm-2"><?php echo __('MerchantPropertiesHotel.Services'); ?>:</label>
                        <div class="col-xs-12 col-sm-8">
                            <span  id="c-services" class="form-control">
                                <?php if(is_array($hotelServices) || $hotelServices instanceof \think\Collection || $hotelServices instanceof \think\Paginator): if( count($hotelServices)==0 ) : echo "" ;else: foreach($hotelServices as $key=>$vo): if($vo): ?>
                                <?php echo __('MerchantPropertiesHotel.Services '.$vo); ?>&nbsp;&nbsp;
                                <?php endif; endforeach; endif; else: echo "" ;endif; ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade in" id="qrcode">
                <div class="form-horizontal">
                    <div class="form-group">
                        <div class="col-xs-12 col-sm-8">
                            <?php if(isset($scancode)): ?>
                            <img src="<?php echo $scancode; ?>" alt="<?php echo __('Wxapp qr code'); ?>" style="height: 500px;width: 500px">
                            <?php else: ?>
                            <span class="text-red"><?php echo __("No_wechat"); ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script src="/assets/js/require<?php echo \think\Config::get('app_debug')?'':'.min'; ?>.js" data-main="/assets/js/require-backend<?php echo \think\Config::get('app_debug')?'':'.min'; ?>.js?v=<?php echo $site['version']; ?>"></script>
    </body>
</html>