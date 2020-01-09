<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:98:"D:\phpStudy\PHPTutorial\WWW\fastadmin3\public/../application/admin\view\merchant\merchant\add.html";i:1578471849;s:81:"D:\phpStudy\PHPTutorial\WWW\fastadmin3\application\admin\view\layout\default.html";i:1577093820;s:78:"D:\phpStudy\PHPTutorial\WWW\fastadmin3\application\admin\view\common\meta.html";i:1577093820;s:80:"D:\phpStudy\PHPTutorial\WWW\fastadmin3\application\admin\view\common\script.html";i:1577093820;}*/ ?>
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
                                <form id="add-form" class="form-horizontal" role="form" data-toggle="validator" method="POST" action="">

    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Pid'); ?><?php echo __('Merchant_name'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <span class="form-control"><?php echo isset($row['merchant_name'])?$row['merchant_name']:""; ?></span>
            <input id="c-pid" class="form-control" name="row[pid]" type="hidden" value="<?php echo isset($row['id'])?$row['id']:0; ?>">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2 required"><?php echo __('Merchant_name'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-merchant_name" data-rule="required;length(~100)" class="form-control" name="row[merchant_name]" type="text">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2 required"><?php echo __('Merchant_localname'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-merchant_localname" data-rule="required;length(~200)" class="form-control" name="row[merchant_localname]" type="text">
        </div>
    </div>
    <?php if($is_platform): ?>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Merchant_type'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <?php if(isset($row)): ?>
            <span class="form-control">
                <?php if(is_array($merchantTypeList) || $merchantTypeList instanceof \think\Collection || $merchantTypeList instanceof \think\Paginator): if( count($merchantTypeList)==0 ) : echo "" ;else: foreach($merchantTypeList as $key=>$vo): if(in_array(($key), is_array($row['merchant_type'])?$row['merchant_type']:explode(',',$row['merchant_type']))): ?><?php echo $vo; endif; endforeach; endif; else: echo "" ;endif; ?>
            </span>
            <input id="c-merchant_type-1" name="row[merchant_type]" type="hidden" value="<?php echo $row['merchant_type']; ?>">
            <?php else: ?>
            <select  id="c-merchant_type" class="form-control selectpicker" name="row[merchant_type]">
                <?php if(is_array($merchantTypeList) || $merchantTypeList instanceof \think\Collection || $merchantTypeList instanceof \think\Paginator): if( count($merchantTypeList)==0 ) : echo "" ;else: foreach($merchantTypeList as $key=>$vo): ?>
                <option value="<?php echo $key; ?>" <?php if(in_array(($key), explode(',',"1"))): ?>selected<?php endif; ?>><?php echo $vo; ?></option>
                <?php endforeach; endif; else: echo "" ;endif; ?>
            </select>
            <?php endif; ?>

        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Status'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            
            <div class="radio">
            <?php if(is_array($statusList) || $statusList instanceof \think\Collection || $statusList instanceof \think\Paginator): if( count($statusList)==0 ) : echo "" ;else: foreach($statusList as $key=>$vo): ?>
            <label for="row[status]-<?php echo $key; ?>"><input id="row[status]-<?php echo $key; ?>" name="row[status]" type="radio" value="<?php echo $key; ?>" <?php if(in_array(($key), explode(',',"1"))): ?>checked<?php endif; ?> /> <?php echo $vo; ?></label>
            <?php endforeach; endif; else: echo "" ;endif; ?>
            </div>

        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Merchantpropertieshotel.Hotel_type'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <select  id="c-hotel_type" class="form-control selectpicker" name="[MerchantPropertiesHotel][hotel_type]">
                <?php if(is_array($hotelTypeList) || $hotelTypeList instanceof \think\Collection || $hotelTypeList instanceof \think\Paginator): if( count($hotelTypeList)==0 ) : echo "" ;else: foreach($hotelTypeList as $key=>$vo): ?>
                <option value="<?php echo $key; ?>" <?php if(in_array(($key), explode(',',"1"))): ?>selected<?php endif; ?>><?php echo $vo; ?></option>
                <?php endforeach; endif; else: echo "" ;endif; ?>
            </select>
        </div>
    </div>
    <?php endif; ?>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Merchantproperties.Logoimage'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <div class="input-group">
                <input id="c-logoimage" class="form-control" size="50"  data-rule="length(~255)"  name="row[MerchantProperties][logoimage]" type="text">
                <div class="input-group-addon no-border no-padding">
                    <span><button type="button" id="plupload-logoimage" class="btn btn-danger plupload" data-input-id="c-logoimage" data-mimetype="image/gif,image/jpeg,image/png,image/jpg,image/bmp" data-multiple="false" data-preview-id="p-logoimage"><i class="fa fa-upload"></i> <?php echo __('Upload'); ?></button></span>
                    <span><button type="button" id="fachoose-logoimage" class="btn btn-primary fachoose" data-input-id="c-logoimage" data-mimetype="image/*" data-multiple="false"><i class="fa fa-list"></i> <?php echo __('Choose'); ?></button></span>
                </div>
                <span class="msg-box n-right" for="c-logoimage"></span>
            </div>
            <ul class="row list-inline plupload-preview" id="p-logoimage"></ul>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Merchantproperties.Url'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-url" class="form-control" name="row[MerchantProperties][url]" data-rule="length(~255)"  type="text" data-rule="url">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Merchantproperties.Descimages'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <div class="input-group">
                <input id="c-descimages" class="form-control" size="50" name="row[MerchantProperties][descimages]" data-rule="length(~4000)" type="text">
                <div class="input-group-addon no-border no-padding">
                    <span><button type="button" id="plupload-descimages" class="btn btn-danger plupload" data-input-id="c-descimages" data-mimetype="image/gif,image/jpeg,image/png,image/jpg,image/bmp" data-multiple="true" data-maxcount="9" data-preview-id="p-descimages"><i class="fa fa-upload"></i> <?php echo __('Upload'); ?></button></span>
                    <span><button type="button" id="fachoose-descimages" class="btn btn-primary fachoose" data-input-id="c-descimages" data-mimetype="image/*" data-multiple="true" data-maxcount="9"><i class="fa fa-list"></i> <?php echo __('Choose'); ?></button></span>
                </div>
                <span class="msg-box n-right" for="c-descimages"></span>
            </div>
            <ul class="row list-inline plupload-preview" id="p-descimages"></ul>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Merchantproperties.Tel'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-tel" class="form-control" name="row[MerchantProperties][tel]"  data-rule="length(~20);tel" type="text" data-rule="tel">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Merchantproperties.Fax'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-fax" class="form-control" name="row[MerchantProperties][fax]" data-rule="length(~20)"  type="text" data-rule="tel">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Merchantproperties.Email'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-email" class="form-control" name="row[MerchantProperties][email]" type="text" data-rule="email">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Merchantproperties.Postcode'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <div class="input-group">
                <input id="c-postcode" class="form-control" name="row[MerchantProperties][postcode]" type="text" data-rule="digits;length(~7)" data-target=".postcode">
                <div class="input-group-addon no-border no-padding postcode">
                    <span><button type="button" class="btn btn-info jpzipsearch" data-output-id="c-address1|c-address2|c-address3" data-input-id="c-postcode"><i class="fa fa-search"></i> <?php echo __('Search'); ?></button></span>
                </div>
            </div>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Merchantproperties.Address1'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-address1" class="form-control" name="row[MerchantProperties][address1]" data-rule="length(~100)" type="text">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Merchantproperties.Address2'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-address2" class="form-control" name="row[MerchantProperties][address2]" data-rule="length(~100)" type="text">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Merchantproperties.Address3'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-address3" class="form-control" name="row[MerchantProperties][address3]" data-rule="length(~100)" type="text">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Merchantproperties.Address4'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-address4" class="form-control" name="row[MerchantProperties][address4]" data-rule="length(~100)"  type="text">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Merchantproperties.Gis_coord'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <div class="input-group">
                <input id="c-gis_coord" class="form-control" name="row[MerchantProperties][gis_coord]" type="text">
                <div class="input-group-addon no-border no-padding">
                    <span><button type="button" class="btn btn-info showmap" data-output-id="c-gis_coord" data-input-id="c-address1|c-address2|c-address3"><i class="fa fa-map-marker"></i> <?php echo __('Search'); ?></button></span>
                </div>
            </div>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Service time'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-service_stime" size="20" class="form-control-inline datetimepicker" data-date-format="HH:mm" data-date-show-today-button="false" data-date-show-close="false" name="row[MerchantProperties][service_stime]" type="text" value="06:00">
            -
            <input id="c-service_etime" size="20" class="form-control-inline datetimepicker" data-date-format="HH:mm" data-date-show-today-button="false" data-date-show-close="false" name="row[MerchantProperties][service_etime]" type="text" value="23:00">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Merchantproperties.Notes'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <textarea id="c-notes" class="form-control " rows="5" name="row[MerchantProperties][notes]" cols="50"></textarea>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Merchantproperties.Attention'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-attention" class="form-control" name="row[MerchantProperties][attention]" type="text" maxlength="30" >
        </div>
    </div>

    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Merchantpropertieshotel.Checkin_time'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-checkin_stime" size="20" class="form-control-inline datetimepicker" data-date-format="HH:mm" data-date-show-today-button="false" data-date-show-close="false" name="row[MerchantPropertiesHotel][checkin_stime]" type="text" value="14:00">
            -
            <input id="c-checkin_etime" size="20" class="form-control-inline datetimepicker" data-date-format="HH:mm" data-date-show-today-button="false" data-date-show-close="false" name="row[MerchantPropertiesHotel][checkin_etime]" type="text" value="23:00">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Merchantpropertieshotel.Checkout_time'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-checkout_stime" size="20" class="form-control-inline datetimepicker" data-date-format="HH:mm" data-date-show-today-button="false" data-date-show-close="false" name="row[MerchantPropertiesHotel][checkout_stime]" type="text" value="04:00">
            -
            <input id="c-checkout_etime" size="20" class="form-control-inline datetimepicker" data-date-format="HH:mm" data-date-show-today-button="false" data-date-show-close="false" name="row[MerchantPropertiesHotel][checkout_etime]" type="text" value="12:00">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Merchantpropertieshotel.Services'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <select  id="c-services" class="form-control selectpicker" multiple="" name="row[MerchantPropertiesHotel][services][]">
                <?php if(is_array($servicesList) || $servicesList instanceof \think\Collection || $servicesList instanceof \think\Paginator): if( count($servicesList)==0 ) : echo "" ;else: foreach($servicesList as $key=>$vo): ?>
                    <option value="<?php echo $key; ?>" <?php if(in_array(($key), explode(',',""))): ?>selected<?php endif; ?>><?php echo __('Merchantpropertieshotel.'.$vo); ?></option>
                <?php endforeach; endif; else: echo "" ;endif; ?>
            </select>

        </div>
    </div>
    <div class="form-group layer-footer">
        <label class="control-label col-xs-12 col-sm-2"></label>
        <div class="col-xs-12 col-sm-8">
            <button type="submit" class="btn btn-success btn-embossed disabled"><?php echo __('OK'); ?></button>
            <button type="reset" class="btn btn-default btn-embossed"><?php echo __('Reset'); ?></button>
        </div>
    </div>
</form>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script src="/assets/js/require<?php echo \think\Config::get('app_debug')?'':'.min'; ?>.js" data-main="/assets/js/require-backend<?php echo \think\Config::get('app_debug')?'':'.min'; ?>.js?v=<?php echo $site['version']; ?>"></script>
    </body>
</html>