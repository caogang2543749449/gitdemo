<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:95:"D:\phpStudy\PHPTutorial\WWW\fastadmin3\public/../application/admin\view\order\order\detail.html";i:1577093820;s:81:"D:\phpStudy\PHPTutorial\WWW\fastadmin3\application\admin\view\layout\default.html";i:1577093820;s:78:"D:\phpStudy\PHPTutorial\WWW\fastadmin3\application\admin\view\common\meta.html";i:1577093820;s:80:"D:\phpStudy\PHPTutorial\WWW\fastadmin3\application\admin\view\common\script.html";i:1577093820;}*/ ?>
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
    <div class="col-xs-12">
        <input id="c-id" name="row[id]" type="hidden" value="<?php echo htmlentities($row['id']); ?>">
        <div class="form-group col-xs-12 layui-layer-border bg-gray-light">
            <div class="form-group fit-width">
                <div class="form-control no-border"><?php echo __('Order_info'); ?>:</div>
            </div>
            <div class="form-group col-xs-12 col-sm-6">
                <label class="control-label col-xs-12 col-sm-5"><?php echo __('Order_sn'); ?>:</label>
                <div class="col-xs-12 col-sm-7 no-padding">
                    <span class="form-control" style="padding-left: 1px"><?php echo $row['order_sn']; ?></span>
                </div>
            </div>
            <div class="form-group col-xs-12 col-sm-6">
                <label class="control-label col-xs-12 col-sm-5"><?php echo __('Status'); ?>:</label>
                <div class="col-xs-12 col-sm-7 no-padding">
                    <span id="c-status" class="form-control">
                    <?php if(is_array($statusList) || $statusList instanceof \think\Collection || $statusList instanceof \think\Paginator): if( count($statusList)==0 ) : echo "" ;else: foreach($statusList as $key=>$vo): if(in_array(($key), is_array($row['status'])?$row['status']:explode(',',$row['status']))): ?><?php echo $vo; endif; endforeach; endif; else: echo "" ;endif; ?>
                    </span>
                </div>
            </div>
            <div class="form-group col-xs-12 col-sm-6">
                <label class="control-label col-xs-12 col-sm-5"><?php echo __('Order_money'); ?>:</label>
                <div class="col-xs-12 col-sm-7 no-padding">
                    <span class="form-control"><?php echo intval($row['real_money']); ?></span>
                </div>
            </div>
        </div>
        <div class="form-group col-xs-12 layui-layer-border bg-gray-light">
            <div class="form-group fit-width">
                <div class="form-control no-border"><?php echo __('User_info'); ?>:</div>
            </div>
            <div class="form-group col-xs-12 col-sm-6 col-md-4">
                <label class="control-label col-xs-12 col-sm-5"><?php echo __('Checkin_name'); ?>:</label>
                <div class="col-xs-12 col-sm-7 no-padding">
                    <span class="form-control"><?php echo $row['checkin_name']; ?></span>
                </div>
            </div>
            <div class="form-group col-xs-12 col-sm-6 col-md-4">
                <label class="control-label col-xs-12 col-sm-5"><?php echo __('Checkin_room'); ?>:</label>
                <div class="col-xs-12 col-sm-7 no-padding">
                    <span class="form-control"><?php echo $row['checkin_room']; ?></span>
                </div>
            </div>
            <div class="form-group col-xs-12 col-sm-6 col-md-4">
                <label class="control-label col-xs-12 col-sm-5"><?php echo __('Delivery_hope'); ?>:</label>
                <div class="col-xs-12 col-sm-7 no-padding">
                    <span class="form-control">
                        <?php if($row['delivery_hope_date']!=null): ?>
                            <?php echo $row['delivery_hope_date']; ?> <?php echo $row['delivery_hope_time']; ?><?php echo __("O'clock"); ?>~<?php echo $row['delivery_hope_time']+1; ?><?php echo __("O'clock"); else: ?>
                            <?php echo __('Immediately'); endif; ?>
                    </span>
                </div>
            </div>
        </div>
        <?php if($row['message']): ?>
        <div class="form-group col-xs-12 layui-layer-border bg-gray-light">
            <div class="form-group fit-width">
                <div class="form-control no-border"><?php echo __('Message'); ?>:</div>
            </div>
            <div class="form-group col-xs-12">
                <span style="height: auto;"><?php echo str_replace(PHP_EOL,'<br>',$row['message']); ?></span>
            </div>
        </div>
        <?php endif; if($row['paid_id']): ?>
        <div class="form-group col-xs-12 layui-layer-border bg-gray-light">
            <div class="form-group fit-width">
                <div class="form-control no-border"><?php echo __('Paid_info'); ?>:</div>
            </div>
            <div class="form-group col-xs-12 col-sm-6 col-md-4">
                <label class="control-label col-xs-12 col-sm-5"><?php echo __('Paid_id'); ?>:</label>
                <div class="col-xs-12 col-sm-7 no-padding">
                    <span class="form-control"><?php echo $row['paid_id']; ?></span>
                </div>
            </div>
            <div class="form-group col-xs-12 col-sm-6 col-md-4">
                <label class="control-label col-xs-12 col-sm-5"><?php echo __('Paid_time'); ?>:</label>
                <div class="col-xs-12 col-sm-7 no-padding">
                    <span class="form-control"><?php echo $row['paid_time']; ?></span>
                </div>
            </div>
        </div>
        <?php endif; if($row['deliver_time']): ?>
        <div class="form-group col-xs-12 layui-layer-border bg-gray-light">
            <div class="form-group fit-width">
                <div class="form-control no-border"><?php echo __('Delivery_info'); ?>:</div>
            </div>
            <div class="form-group col-xs-12 col-sm-6 col-md-4">
                <label class="control-label col-xs-12 col-sm-5"><?php echo __('Deliver_time'); ?>:</label>
                <div class="col-xs-12 col-sm-7 no-padding">
                    <span class="form-control"><?php echo $row['deliver_time']; ?></span>
                </div>
            </div>
            <div class="form-group col-xs-12 col-sm-6 col-md-4">
                <label class="control-label col-xs-12 col-sm-5"><?php echo __('Receive_time'); ?>:</label>
                <div class="col-xs-12 col-sm-7 no-padding">
                    <span class="form-control"><?php echo $row['receive_time']; ?></span>
                </div>
            </div>
        </div>
        <?php endif; ?>
        <div id="b-refund_info" class="form-group col-xs-12 layui-layer-border bg-gray-light <?php if($row['refunded_money']==0): ?>hidden<?php endif; ?>">
            <div class="form-group fit-width">
                <div class="form-control no-border"><?php echo __('Refund_info'); ?>:</div>
            </div>
            <div class="form-group col-xs-12 col-sm-6 col-md-4">
                <label class="control-label col-xs-12 col-sm-5"><?php echo __('Refunded_money'); ?>:</label>
                <div class="col-xs-12 col-sm-7 no-padding">
                    <span id="c-refunded_money" class="form-control"><?php echo intval($row['refunded_money']); ?></span>
                </div>
            </div>
            <div class="form-group col-xs-12 col-sm-6 col-md-4">
                <label class="control-label col-xs-12 col-sm-5"><?php echo __('Last_refund_time'); ?>:</label>
                <div class="col-xs-12 col-sm-7 no-padding">
                    <span id="c-last_refund_time" class="form-control"><?php echo $row['last_refund_time']; ?></span>
                </div>
            </div>
        </div>
        <div class="form-group col-xs-12">
            <div class="fit-width toolbar">
                <div class="form-control no-border"><?php echo __('Order_goods'); ?>:</div>
                <a href="javascript:;" class="btn btn-primary btn-refresh hidden" title="<?php echo __('Refresh'); ?>" ><i class="fa fa-refresh"></i> </a>
            </div>
            <table id="goods-table" class="table table-striped table-bordered table-hover table-nowrap" width="100%"></table>
        </div>
    </div>
    <div class="form-group layer-footer">
        <label class="control-label col-xs-12 col-sm-2"></label>
        <div class="col-xs-12 col-sm-7">
            <?php if(isset($nextStep)): ?>
            <input id="c-next_step" name="next_step" type="hidden" value="<?php echo key($nextStep); ?>">
            <button type="button" class="btn btn-success btn-embossed btn-nextStep"><?php echo __(current($nextStep)); ?></button>
            <?php endif; ?>
        </div>
        <div class="col-xs-12 col-sm-2">
            <button type="button" class="btn btn-success btn-embossed btn-refresh hidden"><?php echo __('Refresh'); ?></button>
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