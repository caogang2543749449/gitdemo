<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:95:"D:\phpStudy\PHPTutorial\WWW\fastadmin3\public/../application/admin\view\service\goods\edit.html";i:1578532677;s:81:"D:\phpStudy\PHPTutorial\WWW\fastadmin3\application\admin\view\layout\default.html";i:1577093820;s:78:"D:\phpStudy\PHPTutorial\WWW\fastadmin3\application\admin\view\common\meta.html";i:1577093820;s:80:"D:\phpStudy\PHPTutorial\WWW\fastadmin3\application\admin\view\common\script.html";i:1577093820;}*/ ?>
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

    <input id="c-id" name="row[id]" type="hidden" value="<?php echo $row['id']; ?>">
    <input id="c-merchant_id" name="row[merchant_id]" type="hidden" value="<?php echo $row['merchant_id']; ?>">
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2 required"><?php echo __('Name'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-name" data-rule="required;length(~100)" class="form-control" name="row[name]" type="text" value="<?php echo htmlentities($row['name']); ?>">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2 required"><?php echo __('Local name'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-local-name" data-rule="required;length(~200)"  class="form-control" name="row[local_name]" type="text" value="<?php echo htmlentities($row['local_name']); ?>">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2 required"><?php echo __('Category.name'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <select  id="c-category_id" class="form-control selectpicker" name="row[category_id]">
                <?php if(is_array($categoryList) || $categoryList instanceof \think\Collection || $categoryList instanceof \think\Paginator): if( count($categoryList)==0 ) : echo "" ;else: foreach($categoryList as $key=>$vo): ?>
                <option value="<?php echo $key; ?>" <?php if(in_array(($key), is_array($row['category_id'])?$row['category_id']:explode(',',$row['category_id']))): ?>selected<?php endif; ?>><?php echo $vo; ?></option>
                <?php endforeach; endif; else: echo "" ;endif; ?>
            </select>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2 required"><?php echo __('Thumb_image'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <div class="input-group">
                <input id="c-thumb_image" class="form-control" data-rule="length(~255)" size="50" name="row[thumb_image]" type="text" value="<?php echo htmlentities($row['thumb_image']); ?>">
                <div class="input-group-addon no-border no-padding">
                    <span><button type="button" id="plupload-thumb_image" class="btn btn-danger plupload" data-input-id="c-thumb_image" data-mimetype="image/gif,image/jpeg,image/png,image/jpg,image/bmp" data-multiple="false" data-preview-id="p-thumb_image"><i class="fa fa-upload"></i> <?php echo __('Upload'); ?></button></span>
                    <span><button type="button" id="fachoose-thumb_image" class="btn btn-primary fachoose" data-input-id="c-thumb_image" data-mimetype="image/*" data-multiple="false"><i class="fa fa-list"></i> <?php echo __('Choose'); ?></button></span>
                </div>
                <span class="msg-box n-right" for="c-thumb_image"></span>
            </div>
            <ul class="row list-inline plupload-preview" id="p-thumb_image"></ul>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Desc_images'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <div class="input-group">
                <input id="c-desc_images" class="form-control" size="50" data-rule="length(~4000)" name="row[desc_images]" type="text" value="<?php echo htmlentities($row['desc_images']); ?>">
                <div class="input-group-addon no-border no-padding">
                    <span><button type="button" id="plupload-desc_images" class="btn btn-danger plupload" data-input-id="c-desc_images" data-mimetype="image/gif,image/jpeg,image/png,image/jpg,image/bmp" data-multiple="true" data-preview-id="p-desc_images" data-maxcount="9"><i class="fa fa-upload"></i> <?php echo __('Upload'); ?></button></span>
                    <span><button type="button" id="fachoose-desc_images" class="btn btn-primary fachoose" data-input-id="c-desc_images" data-mimetype="image/*" data-multiple="true" data-maxcount="9"><i class="fa fa-list"></i> <?php echo __('Choose'); ?></button></span>
                </div>
                <span class="msg-box n-right" for="c-desc_images"></span>
            </div>
            <ul class="row list-inline plupload-preview" id="p-desc_images"></ul>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2 required"><?php echo __('Goods_sn'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-goods_sn" class="form-control" data-rule="length(~64)"  name="row[goods_sn]" type="text" data-rule="required" value="<?php echo htmlentities($row['goods_sn']); ?>">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2 required"><?php echo __('Price'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-price" data-rule="required" class="form-control" name="row[price]" type="number" value="<?php echo (int)($row['price']); ?>">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2 required"><?php echo __('Quota'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <select id="c-quota" class="form-control selectpicker" name="row[quota]">
                <?php $__FOR_START_334753204__=0;$__FOR_END_334753204__=11;for($i=$__FOR_START_334753204__;$i < $__FOR_END_334753204__;$i+=1){ ?>
                <option value="<?php echo $i; ?>" <?php if($i==$row['quota']): ?>selected<?php endif; ?>><?php if($i==0): ?><?php echo __('No limit'); else: ?><?php echo $i; endif; ?></option>
                <?php } ?>
            </select>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Original_price'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-original_price" class="form-control" name="row[original_price]" type="number" value="<?php echo !empty($row['original_price'])?(int)$row['original_price']:''; ?>">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Unit'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-unit" class="form-control" name="row[unit]" type="text" value="<?php echo htmlentities($row['unit']); ?>">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Brief'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <textarea id="c-brief" class="form-control " rows="5" name="row[brief]" cols="50" maxlength="300"><?php echo htmlentities($row['brief']); ?></textarea>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Is_hot'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            
            <input  id="c-is_hot" name="row[is_hot]" type="hidden" value="<?php echo $row['is_hot']; ?>">
            <a href="javascript:;" data-toggle="switcher" class="btn-switcher" data-input-id="c-is_hot" data-yes="1" data-no="0" >
                <i class="fa fa-toggle-on text-success <?php if($row['is_hot'] == '0'): ?>fa-flip-horizontal text-gray<?php endif; ?> fa-2x"></i>
            </a>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Is_for_sale'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <?php echo build_radios('row[is_for_sale]', ['0'=>__('Not For Sale'), '1'=>__('For Sale')], $row['is_for_sale']); ?>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Labels'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-labels" class="form-control" name="row[labels]" type="text" value="<?php echo htmlentities($row['labels']); ?>">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Attributes'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <table id="attr-table" class="table table-striped table-bordered table-hover table-nowrap" width="100%">
            </table>
        </div>
        <div class="input-group-btn no-border no-padding">
            <span><button type="button" id="add-attribute" class="btn btn-success btn-attribute"><?php echo __('Add Attribute'); ?></button></span>
        </div>
        <input id="c-attributes" name="row[attributes]" type="hidden" value="<?php echo htmlentities($row['attributes']); ?>">
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Description'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <textarea id="c-description" class="form-control summernote" name="row[description]" cols="50"><?php echo htmlentities($row['description']); ?></textarea>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Notice'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <textarea id="c-Notice" class="form-control summernote" data-maxlength="1000"  data-height="250" data-onlytext="true" name="row[notice]" cols="50"><?php echo htmlentities($row['notice']); ?></textarea>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Weigh'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-weigh" data-rule="required" class="form-control" name="row[weigh]" type="number" value="<?php echo $row['weigh']; ?>">
        </div>
    </div>
    <div class="form-group layer-footer">
        <label class="control-label col-xs-12 col-sm-2"></label>
        <div class="col-xs-12 col-sm-7">
            <button type="submit" class="btn btn-success btn-embossed disabled"><?php echo __('OK'); ?></button>
            <button type="reset" class="btn btn-default btn-embossed"><?php echo __('Reset'); ?></button>
        </div>
        <div class="col-xs-12 col-sm-1">
            <button type="button" class="btn btn-primary btn-embossed btn-close"><?php echo __('Close'); ?></button>
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