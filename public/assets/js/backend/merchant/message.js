define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {
    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'merchant/message/index' + location.search,
                    add_url: '',
                    edit_url: '',
                    del_url: '',
                    multi_url: '',
                    table: 'merchant_msg',
                }
            });

            var table = $("#table");
            //检索框提示
            $.fn.bootstrapTable.locales[Table.defaults.locale]['formatSearch'] = function(){
                return __('Nickname')+" "+__('Content')+"...";
            };
            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'createtime',
                columns: [
                    [
                        {field: 'id', title: __('Id')},
                        {field: 'merchant_id', title: __('Merchant_id'), visible: false},
                        {field: 'merchant.merchant_name', title: __('Merchant.merchant_name'), visible: false},
                        {field: 'user_id', title: __('User_id'), visible: false},
                        {field: 'user.nickname', title: __('Nickname')},
                        {field: 'msg_type', title: __('Msg_type'), searchList: {"msg":__('Msg_type msg'),"lost":__('Msg_type lost')}, formatter: Table.api.formatter.normal},
                        {field: 'checkin_name', title: __('Checkin_name')},
                        {field: 'checkout_date', title: __('Checkout_date'), operate:'RANGE', addclass:'datetimerange'},
                        {field: 'status', title: __('Status'), searchList: {"1":__('Status 1'),"2":__('Status 2'),"3":__('Status 3'),"9":__('Status 9')}, formatter: Controller.api.formatter.msg_stat},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'updatetime', title: __('Updatetime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {
                            field: 'operate',
                            title: __('Operate'),
                            table: table,
                            events: Table.api.events.operate,
                            formatter: Table.api.formatter.operate,
                            buttons: [
                                {
                                    // 打开详情页面
                                    name: 'Detail',
                                    title: __('Detail'),
                                    classname: 'btn btn-xs btn-success btn-dialog',
                                    icon: 'fa fa-edit',
                                    url: 'merchant/message/edit',
                                },
                            ]
                        }
                    ]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
        },
        edit: function () {
            Controller.api.bindevent();
            $('.submit').on('click', function() {
                $('#c-status').val($(this).data("value"));
                $('#c-sendmail').val("0");
                $("form[role=form]").submit();
            });
            $('#sendmailsubmit').on('click', function() {
                $('#c-status').val($(this).data("value"));
                $('#c-sendmail').val("1");
                $("form[role=form]").submit();
            });
            
            var sent = false;
            $('#sendmail').on('click', function() {
                if(sent) {return false;}
                sent=true;
                //parent.
                let mail_content_id = $("#c-mail_content_id").val();
                if(!mail_content_id) {
                    alert(__('pls select mail content'));
                    return false;
                }
                let messageId = $("#message_id").text();
                $.ajax({
                    method: "POST",
                    url: "/merchant/message/sendmail",
                    data: { message_id: messageId, mail_content_id: mail_content_id }
                  })
                .done(function( res ) {
                    console.log(res);
                    parent.window.frames[0].location.reload();
                });
                return false;
            });
        },
        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"));
            },
            formatter: {
                msg_stat: function (value, row, index) {
                    this.custom = {'1': 'danger', '2': 'info', '3': 'success', '9': 'gray'};
                    return Table.api.formatter.status.call(this, value, row, index);
                }
            }
        }
    };
    return Controller;
});