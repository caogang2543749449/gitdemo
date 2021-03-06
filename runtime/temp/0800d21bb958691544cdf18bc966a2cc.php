<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:103:"D:\phpStudy\PHPTutorial\WWW\fastadmin3\public/../application/admin\view\general\mail_template\edit.html";i:1578469010;s:81:"D:\phpStudy\PHPTutorial\WWW\fastadmin3\application\admin\view\layout\default.html";i:1577093820;s:78:"D:\phpStudy\PHPTutorial\WWW\fastadmin3\application\admin\view\common\meta.html";i:1577093820;s:80:"D:\phpStudy\PHPTutorial\WWW\fastadmin3\application\admin\view\common\script.html";i:1577093820;}*/ ?>
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
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Merchant.merchant_name'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <select  id="c-merchant_id" class="form-control selectpicker" name="row[merchant_id]">
                <option value="0">-</option>
                <?php if(is_array($merchantList) || $merchantList instanceof \think\Collection || $merchantList instanceof \think\Paginator): if( count($merchantList)==0 ) : echo "" ;else: foreach($merchantList as $key=>$vo): ?>
                <option value="<?php echo $vo['id']; ?>" <?php if(in_array(($vo['id']), is_array($row['merchant_id'])?$row['merchant_id']:explode(',',$row['merchant_id']))): ?>selected<?php endif; ?>><?php echo __($vo['merchant_name']); ?></option>
                <?php endforeach; endif; else: echo "" ;endif; ?>
            </select>

        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Name'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <?php if($row['merchant_id'] == '0'): ?>
                <span><?php echo $row['name']; ?></span>
            <?php else: ?>
                <input id="c-name" data-rule="required;length(~30)" class="form-control" name="row[name]" type="text" value="<?php echo htmlentities($row['name']); ?>">
            <?php endif; ?>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Is_html'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            
            <input  id="c-is_html" name="row[is_html]" type="hidden" value="<?php echo $row['is_html']; ?>">
            <a href="javascript:;" data-toggle="switcher" class="btn-switcher" data-input-id="c-is_html" data-yes="1" data-no="0" >
                <i class="fa fa-toggle-on text-success <?php if($row['is_html'] == '0'): ?>fa-flip-horizontal text-gray<?php endif; ?> fa-2x"></i>
            </a>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Title'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-title" data-rule="required;length(~200)" class="form-control" name="row[title]" type="text" value="<?php echo htmlentities($row['title']); ?>">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Template'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <textarea id="c-template" data-rule="required" class="form-control editor" rows="5" name="row[template]" cols="50"><?php echo htmlentities($row['template']); ?></textarea>
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