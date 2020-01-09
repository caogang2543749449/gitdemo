<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:97:"D:\phpStudy\PHPTutorial\WWW\fastadmin3\public/../application/admin\view\merchant\coupon\edit.html";i:1578386196;s:81:"D:\phpStudy\PHPTutorial\WWW\fastadmin3\application\admin\view\layout\default.html";i:1577093820;s:78:"D:\phpStudy\PHPTutorial\WWW\fastadmin3\application\admin\view\common\meta.html";i:1577093820;s:80:"D:\phpStudy\PHPTutorial\WWW\fastadmin3\application\admin\view\common\script.html";i:1577093820;}*/ ?>
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
                                <form id="edit-form" class="form-horizontal" role="form" data-toggle="validator" method="POST" action="">

    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Title'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-title" data-rule="required,length(~100)" class="form-control" name="row[title]" type="text" value="<?php echo htmlentities($row['title']); ?>">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Store_name'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-store_name" data-rule="required,length(~100)" class="form-control" name="row[store_name]" type="text" value="<?php echo htmlentities($row['store_name']); ?>">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Store_logo_image'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <div class="input-group">
                <input id="c-store_logo_image" class="form-control" size="50"  data-rule="required,length(~255)"   name="row[store_logo_image]" type="text" value="<?php echo htmlentities($row['store_logo_image']); ?>">
                <div class="input-group-addon no-border no-padding">
                    <span><button type="button" id="plupload-store_logo_image" class="btn btn-danger plupload" data-input-id="c-store_logo_image" data-mimetype="image/gif,image/jpeg,image/png,image/jpg,image/bmp" data-multiple="false" data-preview-id="p-store_logo_image"><i class="fa fa-upload"></i> <?php echo __('Upload'); ?></button></span>
                    <span><button type="button" id="fachoose-store_logo_image" class="btn btn-primary fachoose" data-input-id="c-store_logo_image" data-mimetype="image/*" data-multiple="false"><i class="fa fa-list"></i> <?php echo __('Choose'); ?></button></span>
                </div>
                <span class="msg-box n-right" for="c-store_logo_image"></span>
            </div>
            <ul class="row list-inline plupload-preview" id="p-store_logo_image"></ul>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Store_address'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-store_address" data-rule="required,length(~255)"  class="form-control" name="row[store_address]" type="text" value="<?php echo htmlentities($row['store_address']); ?>">
        </div>
    </div>
    <div class="form-group" id="f-gis_coord">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Gis_coord'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <div class="input-group">
                <input id="c-gis_coord" data-rule="required" class="form-control" size="50" name="row[gis_coord]" type="text" value="<?php echo htmlentities($row['gis_coord']); ?>">
                <div class="input-group-addon no-border no-padding">
                    <span><button type="button" class="btn btn-info showmap" data-output-id="c-gis_coord" data-input-id="c-store_address"><i class="fa fa-map-marker"></i> <?php echo __('Search'); ?></button></span>
                </div>
            </div>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Get_limit'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <select  id="c-get-limit-selector" class="form-control selectpicker">
                <option value="0"><?php echo __('No limit'); ?></option>
                <option value="1"><?php echo __('Limit'); ?></option>
            </select>
            <input  id="c-get_limit" name="row[get_limit]" type="number" value="<?php echo $row['get_limit']; ?>" style="display: none;margin-top: 5px;">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Get_rule'); ?>:</label>
        <div class="col-xs-12 col-sm-8">

            <select  id="c-get_rule" data-rule="required" class="form-control selectpicker"  multiple="" name="row[get_rule][]">
                <?php if(is_array($getRuleList) || $getRuleList instanceof \think\Collection || $getRuleList instanceof \think\Paginator): if( count($getRuleList)==0 ) : echo "" ;else: foreach($getRuleList as $key=>$vo): ?>
                <option value="<?php echo $key; ?>" <?php if(in_array(($key), is_array($row['get_rule'])?$row['get_rule']:explode(',',$row['get_rule']))): ?>selected<?php endif; ?>><?php echo __($vo); ?></option>
                <?php endforeach; endif; else: echo "" ;endif; ?>
            </select>

        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Use_rule'); ?>:</label>
        <div class="col-xs-12 col-sm-8">

            <select  id="c-use_rule" data-rule="required" class="form-control selectpicker" name="row[use_rule]">
                <?php if(is_array($useRuleList) || $useRuleList instanceof \think\Collection || $useRuleList instanceof \think\Paginator): if( count($useRuleList)==0 ) : echo "" ;else: foreach($useRuleList as $key=>$vo): ?>
                <option value="<?php echo $key; ?>" <?php if(in_array(($key), is_array($row['use_rule'])?$row['use_rule']:explode(',',$row['use_rule']))): ?>selected<?php endif; ?>><?php echo __($vo); ?></option>
                <?php endforeach; endif; else: echo "" ;endif; ?>
            </select>

        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Valid_switch'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
                <select  id="c-valid_switch" class="form-control selectpicker" name="row[valid_switch]">
                    <?php if(is_array($validSwitchList) || $validSwitchList instanceof \think\Collection || $validSwitchList instanceof \think\Paginator): if( count($validSwitchList)==0 ) : echo "" ;else: foreach($validSwitchList as $key=>$vo): ?>
                    <option value="<?php echo $key; ?>" <?php if(in_array(($key), is_array($row['valid_switch'])?$row['valid_switch']:explode(',',$row['valid_switch']))): ?>selected<?php endif; ?>><?php echo __($vo); ?></option>
                    <?php endforeach; endif; else: echo "" ;endif; ?>
                </select>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Start_time'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-start_time" data-rule="required" class="form-control datetimepicker" data-date-format="YYYY-MM-DD 00:00:00" data-use-current="true" name="row[start_time]" type="text" value="<?php echo $row['start_time']; ?>">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('End_time'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-end_time" data-rule="required; match(gt, row[start_time], date)" class="form-control datetimepicker" data-date-format="YYYY-MM-DD 23:59:59" data-use-current="true" name="row[end_time]" type="text" value="<?php echo $row['end_time']; ?>">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Effe_days'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input  id="c-effe_days" name="row[effe_days]" type="number" value="<?php echo $row['effe_days']; ?>" min="1"> <?php echo __('Within Days'); ?>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Qr_image'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <div class="input-group">
                <input id="c-qr_image"  class="form-control"  size="50"  data-rule="required,length(~255)"  name="row[qr_image]" type="text" value="<?php echo htmlentities($row['qr_image']); ?>">
                <div class="input-group-addon no-border no-padding">
                    <span><button type="button" id="plupload-qr_image" class="btn btn-danger plupload" data-input-id="c-qr_image" data-mimetype="image/gif,image/jpeg,image/png,image/jpg,image/bmp" data-multiple="false" data-preview-id="p-qr_image"><i class="fa fa-upload"></i> <?php echo __('Upload'); ?></button></span>
                    <span><button type="button" id="fachoose-qr_image" class="btn btn-primary fachoose" data-input-id="c-qr_image" data-mimetype="image/*" data-multiple="false"><i class="fa fa-list"></i> <?php echo __('Choose'); ?></button></span>
                </div>
                <span class="msg-box n-right" for="c-qr_image"></span>
            </div>
            <ul class="row list-inline plupload-preview" id="p-qr_image"></ul>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Rule'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-rule" data-rule="required" class="form-control" name="row[rule]" type="text" value="<?php echo $row['rule']; ?>"/>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Rule2'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-rule2" data-rule="required" class="form-control" name="row[rule2]" type="text" value="<?php echo $row['rule2']; ?>"/>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Content'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <textarea id="c-content" class="form-control" rows="5" name="row[content]" cols="50"><?php echo htmlentities($row['content']); ?></textarea>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Tax_free_switch'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            
            <input  id="c-tax_free_switch" name="row[tax_free_switch]" type="hidden" value="<?php echo $row['tax_free_switch']; ?>">
            <a href="javascript:;" data-toggle="switcher" class="btn-switcher" data-input-id="c-tax_free_switch" data-yes="1" data-no="0" >
                <i class="fa fa-toggle-on text-success <?php if($row['tax_free_switch'] == '0'): ?>fa-flip-horizontal text-gray<?php endif; ?> fa-2x"></i>
            </a>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Open_switch'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            
            <input  id="c-open_switch" name="row[open_switch]" type="hidden" value="<?php echo $row['open_switch']; ?>">
            <a href="javascript:;" data-toggle="switcher" class="btn-switcher" data-input-id="c-open_switch" data-yes="1" data-no="0" >
                <i class="fa fa-toggle-on text-success <?php if($row['open_switch'] == '0'): ?>fa-flip-horizontal text-gray<?php endif; ?> fa-2x"></i>
            </a>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Weigh'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-weigh" class="form-control" name="row[weigh]" type="number" value="<?php echo htmlentities($row['weigh']); ?>">
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