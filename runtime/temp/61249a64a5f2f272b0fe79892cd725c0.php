<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:87:"D:\phpStudy\PHPTutorial\WWW\fastadmin3\public/../application/admin\view\third\edit.html";i:1578466830;s:81:"D:\phpStudy\PHPTutorial\WWW\fastadmin3\application\admin\view\layout\default.html";i:1577093820;s:78:"D:\phpStudy\PHPTutorial\WWW\fastadmin3\application\admin\view\common\meta.html";i:1577093820;s:80:"D:\phpStudy\PHPTutorial\WWW\fastadmin3\application\admin\view\common\script.html";i:1577093820;}*/ ?>
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
    <input id="c-id" name="row[id]" type="hidden" value="<?php echo htmlentities($row['id']); ?>">
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2 required"><?php echo __('Product'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <select  id="c-product" data-rule="required" class="form-control selectpicker" name="row[product]">
                <?php if(is_array($productList) || $productList instanceof \think\Collection || $productList instanceof \think\Paginator): if( count($productList)==0 ) : echo "" ;else: foreach($productList as $key=>$vo): ?>
                <option value="<?php echo $key; ?>" <?php if(in_array(($key), is_array($row['product'])?$row['product']:explode(',',$row['product']))): ?>selected<?php endif; ?>><?php echo $vo; ?></option>
                <?php endforeach; endif; else: echo "" ;endif; ?>
            </select>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2 required"><?php echo __('Third_type'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
                        
            <select  id="c-third_type" data-rule="required" class="form-control selectpicker" name="row[third_type]">
                <?php if(is_array($thirdTypeList) || $thirdTypeList instanceof \think\Collection || $thirdTypeList instanceof \think\Paginator): if( count($thirdTypeList)==0 ) : echo "" ;else: foreach($thirdTypeList as $key=>$vo): ?>
                    <option value="<?php echo $key; ?>" <?php if(in_array(($key), is_array($row['third_type'])?$row['third_type']:explode(',',$row['third_type']))): ?>selected<?php endif; ?>><?php echo $vo; ?></option>
                <?php endforeach; endif; else: echo "" ;endif; ?>
            </select>

        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('App_name'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-app_name" class="form-control" name="row[app_name]" data-rule="length(~100)"  type="text" value="<?php echo htmlentities($row['app_name']); ?>">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Appurl'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-appurl" class="form-control" name="row[appurl]" type="text"  data-rule="length(~200)" value="<?php echo htmlentities($row['appurl']); ?>">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Appid'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-appid" class="form-control" name="row[appid]" type="text"  data-rule="length(~32)"  value="<?php echo htmlentities($row['appid']); ?>">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('App_secret'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-app_secret" class="form-control" name="row[app_secret]" type="text" data-rule="length(~100)"  value="<?php echo htmlentities($row['app_secret']); ?>">
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