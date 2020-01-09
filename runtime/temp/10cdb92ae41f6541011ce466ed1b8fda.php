<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:98:"D:\phpStudy\PHPTutorial\WWW\fastadmin3\public/../application/admin\view\merchant\message\edit.html";i:1577093820;s:81:"D:\phpStudy\PHPTutorial\WWW\fastadmin3\application\admin\view\layout\default.html";i:1577093820;s:78:"D:\phpStudy\PHPTutorial\WWW\fastadmin3\application\admin\view\common\meta.html";i:1577093820;s:80:"D:\phpStudy\PHPTutorial\WWW\fastadmin3\application\admin\view\common\script.html";i:1577093820;}*/ ?>
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
    <span id="message_id" style="display:none;"><?php echo $row['id']; ?></span>
    <input id="c-merchant_id" data-source="merchant/index" class="form-control" name="row[merchant_id]" type="hidden" value="<?php echo htmlentities($row['merchant_id']); ?>">
    <input id="c-sendmail" name="row[sendmail]" type="hidden" value="0">
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Nickname'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <span id="c-user_id" class="form-control no-border"><?php echo htmlentities($row['user']['nickname']); ?></span>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Msg_type'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <span id="c-msg_type" class="form-control no-border"><?php echo __('Msg_type '.$row['msg_type']); ?></span>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Checkin_name'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <span id="c-checkin_name" class="form-control no-border"><?php echo htmlentities($row['checkin_name']); ?></span>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Checkout_date'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <span id="c-checkout_date" class="form-control no-border"><?php echo $row['checkout_date']; ?></span>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Phone'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <span id="c-phone" class="form-control no-border"><?php echo htmlentities($row['phone']); ?></span>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Email'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <span id="c-email" class="form-control no-border"><?php echo htmlentities($row['email']); ?></span>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Content'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <span id="c-content" class="form-control no-border" style="height: auto"><?php echo htmlentities($row['content']); ?></span>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Local_content'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <span id="c-local_content" class="form-control no-border" style="height: auto"><?php echo htmlentities($row['local_content']); ?></span>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Status'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <span id="c-status-label" class="form-control no-border"><?php echo __('Status '.$row['status']); ?></span>
            <input id="c-status" data-source="merchant/index" class="form-control" name="row[status]" type="hidden" value="<?php echo htmlentities($row['status']); ?>">
        </div>
    </div>

    <?php if(($row['status']!=1 && $row['status']!=9)): ?>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2 required"><?php echo __('MailContentLabel'); ?>:</label>
        <div class="col-xs-12 col-sm-9">
            <div class="col-sm-6">
                <select  id="c-mail_content_id" class="form-control selectpicker" name="row[mail_content_id]" <?php if(empty($row['email'])): ?>disabled<?php endif; ?>>
                    <option value=""><?php echo __('pls select mail content'); ?></option>
                    <?php if(is_array($contentList) || $contentList instanceof \think\Collection || $contentList instanceof \think\Paginator): if( count($contentList)==0 ) : echo "" ;else: foreach($contentList as $key=>$vo): ?>
                    <option value="<?php echo $vo['id']; ?>"><?php echo $vo['title']; ?></option>
                    <?php endforeach; endif; else: echo "" ;endif; ?>
                </select>
            </div>
            <div class="col-sm-6">
                <?php if(!empty($row['email'])): ?>
                <a id="sendmail" href="javascript:;" class="btn btn-success btn-embossed"><?php echo __('Send Mail'); ?></a>
                <?php else: ?>
                <a href="javascript:;" class="btn btn-success btn-embossed disabled"><?php echo __('Send Mail'); ?></a>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endif; if($mailList != null): ?>
    <h3><?php echo __('MailContentTitle'); ?></h3>
    <table id="table" class="table table-striped table-bordered table-hover table-nowrap" width="100%">
        <thead>
            <tr>
                <th style="text-align: center; vertical-align: middle; " data-field="id">
                    <div class="th-inner ">ID</div>
                    <div class="fht-cell"></div>
                </th>
                <th style="text-align: center; vertical-align: middle; " data-field="user.nickname">
                    <div class="th-inner "><?php echo __('MailContent'); ?></div>
                    <div class="fht-cell"></div>
                </th>
                <th style="text-align: center; vertical-align: middle; " data-field="msg_type">
                    <div class="th-inner "><?php echo __('MailContent_trans'); ?></div>
                    <div class="fht-cell"></div>
                </th>
                <th style="text-align: center; vertical-align: middle; " data-field="checkin_name">
                    <div class="th-inner "><?php echo __('MailIs_sent'); ?></div>
                    <div class="fht-cell"></div>
                </th>
                <th style="text-align: center; vertical-align: middle; " data-field="checkout_date">
                    <div class="th-inner "><?php echo __('MailCreatetime'); ?></div>
                    <div class="fht-cell"></div>
                </th>
            </tr>
        </thead>
        <tbody data-listidx="0">
            <?php foreach($mailList as $vo): ?> 
                <tr data-index="0">
                    <td style="text-align: center; vertical-align: middle; "><?php echo $vo['id']; ?></td>
                    <td style="text-align: center; vertical-align: middle; "><?php echo $vo['content']; ?></td>
                    <td style="text-align: center; vertical-align: middle; "><?php echo $vo['content_trans']; ?></td>
                    <td style="text-align: center; vertical-align: middle; "><?php echo $vo['is_sent']; ?></td>
                    <td style="text-align: center; vertical-align: middle; "><?php echo $vo['createtime']; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>

    <div class="form-group layer-footer">
        <label class="control-label col-xs-12 col-sm-2"></label>
        <div class="col-xs-12 col-sm-7">
            <button type="button" class="btn btn-info btn-embossed submit" data-value="<?php echo $next_step; ?>"><?php echo __('To Status '.$next_step); ?></button>
            <?php if(($row['status']==1 && !empty($row['email']))): ?>
            <button type="button" class="btn btn-success btn-embossed" id="sendmailsubmit" data-value="<?php echo $next_step; ?>"><?php echo __('To Status '.$next_step); ?>(<?php echo __('System Send Mail'); ?>)</button>
            <?php endif; ?>
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