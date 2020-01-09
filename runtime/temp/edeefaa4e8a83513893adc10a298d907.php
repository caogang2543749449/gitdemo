<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:92:"D:\phpStudy\PHPTutorial\WWW\fastadmin3\public/../application/admin\view\dashboard\index.html";i:1577093820;s:81:"D:\phpStudy\PHPTutorial\WWW\fastadmin3\application\admin\view\layout\default.html";i:1577093820;s:78:"D:\phpStudy\PHPTutorial\WWW\fastadmin3\application\admin\view\common\meta.html";i:1577093820;s:80:"D:\phpStudy\PHPTutorial\WWW\fastadmin3\application\admin\view\common\script.html";i:1577093820;}*/ ?>
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
        width: 90px;
        height: 90px;
        display: inline-block;
        line-height: 90px;
        text-align: center;
        font-size: 45px;
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
    .d-block {
        display: block;
    }
    .font-20 {
        font-size: 20px !important;
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
                                <div><span><?php echo $totaluser; ?></span><?php echo __('Total user'); ?></div>
                                <div><span class="font-20"><?php echo $userRegistByScan; ?></span><?php echo __('Scan user'); ?></div>
                                <div><span class="font-20"><?php echo $totaluser-$userRegistByScan; ?></span><?php echo __('Share user'); ?></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-3 col-xs-6">
                        <div class="sm-st clearfix">
                            <span class="sm-st-icon st-violet"><i class="fa fa-qrcode"></i></span>
                            <div class="sm-st-info">
                                <span class="d-block"><?php echo $totalaccess; ?></span>
                                <?php echo __('Total access'); ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-3 col-xs-6">
                        <div class="sm-st clearfix">
                            <span class="sm-st-icon st-green"><i class="fa fa-share-square-o"></i></span>
                            <div class="sm-st-info">
                                <span class="d-block"><?php echo $totalshare; ?></span>
                                <?php echo __('Total share'); ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-3 col-xs-6">
                        <div class="sm-st clearfix">
                            <span class="sm-st-icon st-blue"><i class="fa fa-money"></i></span>
                            <div class="sm-st-info">
                                <span class="d-block"><?php echo $totalCouponUsed; ?></span>
                                <?php echo __('Total Coupon Used'); ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-11">
                        <div id="echart" style="height:200px;width:100%;"></div>
                    </div>
                    <div class="col-lg-1"></div>
                </div>

                <div class="row">
                    <div class="col-lg-12">
                        <div class="card sameheight-item stats">
                            <div class="card-block">
                                <div class="row row-sm stats-container">
                                    <div class="col-xs-6 stat-col">
                                        <div class="stat-icon"><i class="fa fa-question-circle-o"></i></div>
                                        <div class="stat">
                                            <div class="value"> <?php echo $todaymsg; ?></div>
                                            <div class="name"> <?php echo __('Today new message'); ?></div>
                                        </div>
                                        <div class="progress">
                                            <div class="progress-bar progress-bar-success" style="width: 30%"></div>
                                        </div>
                                    </div>
                                    <div class="col-xs-6 stat-col">
                                        <div class="stat-icon"><i class="fa fa-question-circle"></i></div>
                                        <div class="stat">
                                            <div class="value"> <?php echo $totalopenmsg; ?></div>
                                            <div class="name"> <?php echo __('Total open message'); ?></div>
                                        </div>
                                        <div class="progress">
                                            <div class="progress-bar progress-bar-success" style="width: 30%"></div>
                                        </div>
                                    </div>
                                    <div class="col-xs-6 stat-col">
                                        <div class="stat-icon"><i class="fa fa-user"></i></div>
                                        <div class="stat">
                                            <div class="value"> <?php echo $lastweekuser; ?></div>
                                            <div class="name"> <?php echo __('Last week signup'); ?></div>
                                        </div>
                                        <div class="progress">
                                            <div class="progress-bar progress-bar-success" style="width: 30%"></div>
                                        </div>
                                    </div>
                                    <div class="col-xs-6 stat-col">
                                        <div class="stat-icon"><i class="fa fa-users"></i></div>
                                        <div class="stat">
                                            <div class="value"> <?php echo $lastmonthuser; ?></div>
                                            <div class="name"> <?php echo __('Last month signup'); ?></div>
                                        </div>
                                        <div class="progress">
                                            <div class="progress-bar progress-bar-success" style="width: 25%"></div>
                                        </div>
                                    </div>
                                    <div class="col-xs-6 stat-col">
                                        <div class="stat-icon"><i class="fa fa-line-chart"></i></div>
                                        <div class="stat">
                                            <div class="value"> <?php echo $weekratio; ?></div>
                                            <div class="name"> <?php echo __('Week user ratio'); ?></div>
                                        </div>
                                        <div class="progress">
                                            <div class="progress-bar progress-bar-success" style="width: 25%"></div>
                                        </div>
                                    </div>
                                    <div class="col-xs-6 stat-col">
                                        <div class="stat-icon"><i class="fa fa-bar-chart-o"></i></div>
                                        <div class="stat">
                                            <div class="value"> <?php echo $monthratio; ?></div>
                                            <div class="name"> <?php echo __('Month user ratio'); ?></div>
                                        </div>
                                        <div class="progress">
                                            <div class="progress-bar progress-bar-success" style="width: 25%"></div>
                                        </div>
                                    </div>
                                    <div class="col-xs-6 stat-col">
                                        <div class="stat-icon"><i class="fa fa-bar-chart-o"></i></div>
                                        <div class="stat">
                                            <div class="value"> <?php if($totalCoupon==0): ?>0<?php else: ?><?php echo $totalCouponUsed/$totalCoupon * 100; endif; ?>%</div>
                                            <div class="name"> <?php echo __('Coupon rate'); ?></div>
                                        </div>
                                        <div class="progress">
                                            <div class="progress-bar progress-bar-success" style="width: 25%"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <?php if($is_super): ?>
                <div class="row">
                    <div class="col-lg-4">
                        <div class="box box-info">
                            <div class="box-header"><h3 class="box-title"><?php echo __('Server info'); ?></h3></div>
                            <div class="box-body" style="padding-top:0;">
                                <table class="table table-striped">
                                    <tbody>
                                    <tr>
                                        <td width="140"><?php echo __('FastAdmin version'); ?></td>
                                        <td><?php echo \think\Config::get('fastadmin.version'); ?> <a href="javascript:;" class="btn btn-xs btn-checkversion">检查最新版</a></td>
                                    </tr>
                                    <tr>
                                        <td><?php echo __('FastAdmin addon version'); ?></td>
                                        <td><?php echo $addonversion; ?></td>
                                    </tr>
                                    <tr>
                                        <td><?php echo __('Thinkphp version'); ?></td>
                                        <td><?php echo THINK_VERSION; ?></td>
                                    </tr>
                                    <tr>
                                        <td><?php echo __('Sapi name'); ?></td>
                                        <td><?php echo php_sapi_name(); ?></td>
                                    </tr>
                                    <tr>
                                        <td><?php echo __('Debug mode'); ?></td>
                                        <td><?php echo \think\Config::get('app_debug')?__('Yes'):__('No'); ?></td>
                                    </tr>
                                    <tr>
                                        <td><?php echo __('Software'); ?></td>
                                        <td><?php echo \think\Request::instance()->server('SERVER_SOFTWARE'); ?></td>
                                    </tr>
                                    <tr>
                                        <td><?php echo __('Upload mode'); ?></td>
                                        <td><?php echo $uploadmode; ?></td>
                                    </tr>
                                    <tr>
                                        <td><?php echo __('Upload url'); ?></td>
                                        <td><?php echo $config['upload']['uploadurl']; ?></td>
                                    </tr>
                                    <tr>
                                        <td><?php echo __('Upload Cdn url'); ?></td>
                                        <td><?php echo $config['upload']['cdnurl']; ?></td>
                                    </tr>
                                    <tr>
                                        <td><?php echo __('Timezone'); ?></td>
                                        <td><?php echo date_default_timezone_get(); ?></td>
                                    </tr>
                                    <tr>
                                        <td><?php echo __('Cdn url'); ?></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td><?php echo __('Language'); ?></td>
                                        <td><?php echo $config['language']; ?></td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
</div>
<script>
    var Userdata = {
        column: <?php echo json_encode(array_keys($weekuserlist)); ?>,
        weekuserlist: <?php echo json_encode(array_values($weekuserlist)); ?>,
        weekaccesslist: <?php echo json_encode(array_values($weekaccesslist)); ?>,
    };
</script>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script src="/assets/js/require<?php echo \think\Config::get('app_debug')?'':'.min'; ?>.js" data-main="/assets/js/require-backend<?php echo \think\Config::get('app_debug')?'':'.min'; ?>.js?v=<?php echo $site['version']; ?>"></script>
    </body>
</html>