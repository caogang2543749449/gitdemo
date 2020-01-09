<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:101:"D:\phpStudy\PHPTutorial\WWW\fastadmin3\public/../application/admin\view\merchant\facilities\edit.html";i:1578479186;s:81:"D:\phpStudy\PHPTutorial\WWW\fastadmin3\application\admin\view\layout\default.html";i:1577093820;s:78:"D:\phpStudy\PHPTutorial\WWW\fastadmin3\application\admin\view\common\meta.html";i:1577093820;s:80:"D:\phpStudy\PHPTutorial\WWW\fastadmin3\application\admin\view\common\script.html";i:1577093820;}*/ ?>
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
                                <link href="/assets/libs/tagsinput/bootstrap-tagsinput.css" rel="stylesheet" />
<form id="edit-form" class="form-horizontal" role="form" data-toggle="validator" method="POST" action="">

    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Merchant_name'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <span class="form-control"><?php echo $merchant['merchant_name']; ?></span>
            <input id="c-merchant_id" data-source="merchant/index" name="row[merchant_id]" type="hidden" value="<?php echo $row['merchant_id']; ?>">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Parent'); ?>:</label>
        <div class="col-xs-12 col-sm-8">

            <select  id="c-pid" class="form-control selectpicker" name="row[pid]">
                <option value="0">-</option>
                <?php if(is_array($parentFacilities) || $parentFacilities instanceof \think\Collection || $parentFacilities instanceof \think\Paginator): if( count($parentFacilities)==0 ) : echo "" ;else: foreach($parentFacilities as $key=>$vo): if($row['id'] != $key): ?>
                <option value="<?php echo $key; ?>" <?php if(in_array(($key), is_array($row['pid'])?$row['pid']:explode(',',$row['pid']))): ?>selected<?php endif; ?>><?php echo __($vo); ?></option>
                <?php endif; endforeach; endif; else: echo "" ;endif; ?>
            </select>

        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2 required"><?php echo __('Facility_name'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-facility_name" data-rule="required;length(~20)" class="form-control" name="row[facility_name]" type="text" value="<?php echo htmlentities($row['facility_name']); ?>">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Tag'); ?>:</label>
        <div class="col-xs-12 col-sm-8">

            <select  id="c-tag" class="form-control selectpicker" name="row[tag]">
                <option value="">-</option>
                <?php if(is_array($tagList) || $tagList instanceof \think\Collection || $tagList instanceof \think\Paginator): if( count($tagList)==0 ) : echo "" ;else: foreach($tagList as $key=>$vo): ?>
                <option value="<?php echo $key; ?>" <?php if(in_array(($key), is_array($row['tag'])?$row['tag']:explode(',',$row['tag']))): ?>selected<?php endif; ?>><?php echo __($vo); ?></option>
                <?php endforeach; endif; else: echo "" ;endif; ?>
            </select>

        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('label'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-label" class="form-control" data-role="tagsinput"  name="row[label]" type="text" value="<?php echo htmlentities($row['label']); ?>">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Facilityimages'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <div class="input-group">
                <input id="c-facilityimages" class="form-control" size="50" name="row[facilityimages]" type="text" value="<?php echo htmlentities($row['facilityimages']); ?>">
                <div class="input-group-addon no-border no-padding">
                    <span><button type="button" id="plupload-facilityimages" class="btn btn-danger plupload" data-input-id="c-facilityimages" data-mimetype="image/gif,image/jpeg,image/png,image/jpg,image/bmp" data-multiple="true" data-maxcount="9" data-preview-id="p-facilityimages"><i class="fa fa-upload"></i> <?php echo __('Upload'); ?></button></span>
                    <span><button type="button" id="fachoose-facilityimages" class="btn btn-primary fachoose" data-input-id="c-facilityimages" data-mimetype="image/*" data-multiple="true" data-maxcount="9"><i class="fa fa-list"></i> <?php echo __('Choose'); ?></button></span>
                </div>
                <span class="msg-box n-right" for="c-facilityimages"></span>
            </div>
            <ul class="row list-inline plupload-preview" id="p-facilityimages"></ul>
        </div>
    </div>
    <div class="form-group <?php if($hotelType==1): ?>hidden<?php endif; ?>">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Index Vision'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <?php echo build_radios('row[show_cover_flg]', ['0'=>__('Show List'), '1'=>__('Show Cover')], $row['show_cover_flg']); ?>
        </div>
    </div>
    <div class="form-group <?php if($hotelType==1): ?>hidden<?php endif; ?>">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Detail Vision'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <?php echo build_radios('row[customize_flg]', ['0'=>__('Normal'), '1'=>__('Customize')], $row['customize_flg']); ?>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Description'); ?>:</label>
        <div id="d-description0" class="col-xs-12 col-sm-8 <?php if($row['customize_flg']=='1'): ?>hidden<?php endif; ?>">
            <textarea id="c-description0" class="form-control" rows="10" name="row[description0]" cols="50"><?php if($row['customize_flg']=='0'): ?><?php echo $row['description']; endif; ?></textarea>
        </div>
        <div id="d-description1" class="col-xs-12 col-sm-8 <?php if($row['customize_flg']=='0'): ?>hidden<?php endif; ?>">
            <textarea id="c-description1" class="form-control summernote hidden" rows="10" name="row[description1]" cols="50"><?php if($row['customize_flg']=='1'): ?><?php echo $row['description']; endif; ?></textarea>
        </div>
        <textarea id="c-description" class="form-control hidden" rows="10" name="row[description]" cols="50"><?php echo $row['description']; ?></textarea>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Service_time'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-service_stime" size="20" class="form-control-inline datetimepicker" data-date-format="HH:mm" data-date-show-today-button="false" data-date-show-close="false" name="row[service_stime]" type="text" value="<?php echo substr($row['service_stime'],0,5); ?>">
            -
            <input id="c-service_etime" size="20" class="form-control-inline datetimepicker" data-date-format="HH:mm" data-date-show-today-button="false" data-date-show-close="false" name="row[service_etime]" type="text" value="<?php echo substr($row['service_etime'],0,5); ?>">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Tel'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-tel" class="form-control" name="row[tel]"  data-rule="length(~11);tel"   type="text" value="<?php echo htmlentities($row['tel']); ?>">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Address'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-address" class="form-control" data-rule="length(~200)" name="row[address]" type="text" value="<?php echo htmlentities($row['address']); ?>">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Inner_tel'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-inner_tel" class="form-control"  data-rule="length(~6)" name="row[inner_tel]" type="text" value="<?php echo htmlentities($row['inner_tel']); ?>">
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