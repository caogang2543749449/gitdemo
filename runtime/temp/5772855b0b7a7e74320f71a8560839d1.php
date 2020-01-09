<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:101:"D:\phpStudy\PHPTutorial\WWW\fastadmin3\public/../application/admin\view\data_statistics\platform.html";i:1577093820;s:81:"D:\phpStudy\PHPTutorial\WWW\fastadmin3\application\admin\view\layout\default.html";i:1577093820;s:78:"D:\phpStudy\PHPTutorial\WWW\fastadmin3\application\admin\view\common\meta.html";i:1577093820;s:80:"D:\phpStudy\PHPTutorial\WWW\fastadmin3\application\admin\view\common\script.html";i:1577093820;}*/ ?>
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
                                <style type="text/css">
    .sm-st {
        background: #fff;
        padding: 20px;
        -webkit-border-radius: 3px;
        -moz-border-radius: 3px;
        border-radius: 3px;
        margin-bottom: 20px;
        -webkit-box-shadow: 0 1px 0px rgba(0, 0, 0, 0.05);
        box-shadow: 0 1px 0px rgba(0, 0, 0, 0.05);
    }

    .sm-st-icon {
        width: 60px;
        height: 60px;
        display: inline-block;
        line-height: 60px;
        text-align: center;
        font-size: 30px;
        background: #eee;
        -webkit-border-radius: 5px;
        -moz-border-radius: 5px;
        border-radius: 5px;
        float: left;
        margin-right: 10px;
        color: #fff;
    }

    .sm-st-info {
        font-size: 12px;
        padding-top: 2px;
    }

    .sm-st-info span {
        display: block;
        font-size: 24px;
        font-weight: 600;
    }

    .orange {
        background: #fa8564 !important;
    }

    .tar {
        background: #45cf95 !important;
    }

    .sm-st .green {
        background: #86ba41 !important;
    }

    .pink {
        background: #AC75F0 !important;
    }

    .yellow-b {
        background: #fdd752 !important;
    }

    .stat-elem {

        background-color: #fff;
        padding: 18px;
        border-radius: 40px;

    }

    .stat-info {
        text-align: center;
        background-color: #fff;
        border-radius: 5px;
        margin-top: -5px;
        padding: 8px;
        -webkit-box-shadow: 0 1px 0px rgba(0, 0, 0, 0.05);
        box-shadow: 0 1px 0px rgba(0, 0, 0, 0.05);
        font-style: italic;
    }

    .stat-icon {
        text-align: center;
        margin-bottom: 5px;
    }

    .st-red {
        background-color: #F05050;
    }

    .st-green {
        background-color: #27C24C;
    }

    .st-violet {
        background-color: #7266ba;
    }

    .st-blue {
        background-color: #23b7e5;
    }

    .stats .stat-icon {
        color: #28bb9c;
        display: inline-block;
        font-size: 26px;
        text-align: center;
        vertical-align: middle;
        width: 50px;
        float: left;
    }

    .stat {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        display: inline-block;
        margin-right: 10px;
    }

    .stat .value {
        font-size: 20px;
        line-height: 24px;
        overflow: hidden;
        text-overflow: ellipsis;
        font-weight: 500;
    }

    .stat .name {
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .stat.lg .value {
        font-size: 26px;
        line-height: 28px;
    }

    .stat.lg .name {
        font-size: 16px;
    }

    .stat-col .progress {
        height: 2px;
    }

    .stat-col .progress-bar {
        line-height: 2px;
        height: 2px;
    }

    .item {
        padding: 30px 0;
    }
</style>

<div class="panel panel-default panel-intro">
    <div id="myTabContent" class="tab-content">
        <div class="tab-pane fade active in" id="one">

            <div class="row">
                <div class="col-sm-3 col-xs-6">
                    <div class="sm-st clearfix">
                        <span class="sm-st-icon st-red"><i class="fa fa-users"></i></span>
                        <div class="sm-st-info">
                            <span id="t-user"></span>
                            <?php echo __('Total user'); ?>
                        </div>
                    </div>
                </div>
                <div class="col-sm-3 col-xs-6">
                    <div class="sm-st clearfix">
                        <span class="sm-st-icon st-green"><i class="fa fa-users"></i></span>
                        <div class="sm-st-info">
                            <span id="t-scan"></span>
                            <?php echo __('Scan open'); ?>
                        </div>
                    </div>
                </div>
                <div class="col-sm-3 col-xs-6">
                    <div class="sm-st clearfix">
                        <span class="sm-st-icon st-violet"><i class="fa fa-users"></i></span>
                        <div class="sm-st-info">
                            <span id="t-normal"></span>
                            <?php echo __('Normal open'); ?>
                        </div>
                    </div>
                </div>
                <div class="col-sm-3 col-xs-6">
                    <div class="sm-st clearfix">
                        <span class="sm-st-icon st-blue"><i class="fa fa-users"></i></span>
                        <div class="sm-st-info">
                            <span id="t-share"></span>
                            <?php echo __('Share'); ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <form id="edit-form" role=form class="form-horizontal nice-validator n-default n-bootstrap" novalidate="" method="post" action="">
                    <div class="row">
                        <div class="form-group col-sm-6 col-xs-6">
                            <label class="control-label col-xs-4"><?php echo __('Merchant'); ?></label>
                            <div class="col-xs-8">
                                <?php echo build_radios('search[merchant_id]', [$merchant_id=>__('Current Merchant'), '0'=>__('All Merchant')], 0); ?>
                            </div>
                        </div>
                        <div class="form-group col-sm-6 col-xs-6">
                            <label class="control-label col-xs-4"><?php echo __('Statistics Type'); ?></label>
                            <div class="col-xs-8">
                                <?php echo build_radios('search[type]', ['1'=>__('Timeline'), '2'=>__('Time Period')], 1); ?>
                            </div>
                        </div>
                    </div>
                    <div class="row timeline-row">
                        <div class="form-group col-sm-6 col-xs-6">
                            <label class="control-label col-xs-4"><?php echo __('Base Time'); ?></label>
                            <div class="col-xs-8">
                                <input id="c-basetime" class="form-control datetimepicker" data-date-format="YYYY-MM-DD" data-date-show-today-button="true" data-date-show-close="false" name="search[basetime]" type="text" value="<?php echo date('Y-m-d'); ?>">
                            </div>
                        </div>
                        <div class="form-group col-sm-6 col-xs-6">
                            <label class="control-label col-xs-4"><?php echo __('Term Length'); ?></label>
                            <div class="col-xs-2">
                                <select id="c-term" class="form-control selectpicker" name="search[term_length]">
                                    <?php $__FOR_START_1225661094__=1;$__FOR_END_1225661094__=31;for($i=$__FOR_START_1225661094__;$i < $__FOR_END_1225661094__;$i+=1){ ?>
                                    <option value="<?php echo $i; ?>" <?php if($i==7): ?>selected<?php endif; ?>><?php echo $i; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="col-xs-4">
                                <select id="c-timeline-scale" class="form-control selectpicker" style="width: 50px;" name="search[scale_timeline]">
                                    <option value="1"><?php echo __('Day'); ?></option>
                                    <option value="2" selected><?php echo __('Week'); ?></option>
                                    <option value="3"><?php echo __('Month'); ?></option>
                                    <option value="4"><?php echo __('Year'); ?></option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row period-row hidden">
                        <div class="form-group col-sm-6 col-xs-6">
                            <label class="control-label col-xs-4"><?php echo __('Scale'); ?></label>
                            <div class="col-xs-8">
                                <select id="c-period-scale" class="form-control selectpicker" name="search[scale_period]">
                                    <option value="1" selected><?php echo __('Hourly'); ?></option>
                                    <option value="2"><?php echo __('Week-daily'); ?></option>
                                    <option value="3"><?php echo __('Monthly'); ?></option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-sm-6 col-xs-6">
                            <label class="control-label col-xs-4"><?php echo __('Statistics Item'); ?></label>
                            <div class="col-xs-8">
                                <select id="c-item" class="form-control selectpicker" name="search[item]">
                                    <option value="user_join" selected><?php echo __('User Sign-up'); ?></option>
                                    <option value="scan_open"><?php echo __('Scan open'); ?></option>
                                    <option value="normal_open"><?php echo __('Normal open'); ?></option>
                                    <option value="share"><?php echo __('Share'); ?></option>
                                    <option value="coupon_used_count"><?php echo __('Coupon used count'); ?></option>
<!--                                    <option value="button_click"><?php echo __('Function usage'); ?></option>-->
                                </select>
                            </div>
                        </div>
                        <div class="form-group col-sm-6 col-xs-6  layer-footer">
                            <div class="col-sm-8 col-xs-offset-4">
                                <button type="submit" class="btn btn-success btn-embossed disabled"><?php echo __('Search'); ?></button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="row center-block">
                <div class="col-lg-12">
                    <div id="echart" style="height:400px;width:100%;"></div>
                </div>
                <div class="col-lg-1"></div>
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