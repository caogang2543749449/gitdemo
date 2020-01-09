define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'merchant/merchant/index' + location.search,
                    add_url: 'merchant/merchant/add',
                    edit_url: 'merchant/merchant/edit',
                    del_url: 'merchant/merchant/del',
                    multi_url: 'merchant/merchant/multi',
                    table: 'merchant',
                }
            });

            var table = $("#table");
            //检索框提示
            $.fn.bootstrapTable.locales[Table.defaults.locale]['formatSearch'] = function(){
                return __('Merchant_id')+" "+__('Merchant_name')+"...";
            };
            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'id',
                columns: [
                    [
                        // {checkbox: true},
                        {
                            field: 'Select Merchant',
                            title: __('Select Merchant'),
                            formatter: Controller.api.formatter.switch_merchant,
                            events: Controller.api.events.switch_merchant,
                        },
                        {field: 'id', title: __('Merchant_id')},
                        {field: 'merchant_name', title: __('Merchant_name')},
                        {field: 'merchant_localname', title: __('Merchant_localname')},
                        {field: 'merchant_type', title: __('Merchant_type'), searchList: Config.merchantTypeList, formatter: Table.api.formatter.normal, operate: false},
                        {field: 'status', title: __('Status'), searchList: {"0":__('Status 0'),"1":__('Status 1'),"2":__('Status 2')}, formatter: Table.api.formatter.normal, operate: false},
                        {field: 'merchantpropertieshotel.hotel_type', title: __('Merchantpropertieshotel.Hotel_type'), searchList: Config.hotelTypeList, formatter: Table.api.formatter.normal},
                        {field: 'pmerchant.id', title: __('Pid'), visible: false, operate: false},
                        {field: 'pmerchant.merchant_name', title: __('Pname'), visible: false},
                        {
                            field: 'operate',
                            title: __('Operate'),
                            table: table,
                            events: Table.api.events.operate,
                            formatter: Table.api.formatter.operate,
                            buttons: [
                                {
                                    // 添加门店按钮
                                    name: 'add',
                                    title: __('Add_store'),
                                    classname: 'btn btn-xs btn-primary btn-dialog',
                                    icon: 'fa fa-plus',
                                    url: 'merchant/merchant/add',
                                    visible: function(value,row,index) {
                                        //不是平台管理员不可添加
                                        if(!Config.is_platform) return false;
                                        //有父商家，则是门店，门店不能添加下属门店
                                        else if(value.pmerchant.id) return false;
                                        return true;
                                    },
                                },
                                {
                                    // 添加管理员按钮
                                    name: 'addAdmin',
                                    title: __('Add_admin'),
                                    classname: 'btn btn-xs btn-primary btn-dialog',
                                    icon: 'fa fa-user-plus',
                                    url: 'merchant/merchant_admin/add',
                                    visible: function(value,row,index) {
                                        //平台管理员可以添加商家管理员
                                        return Config.is_platform;
                                    },
                                },
                                {
                                    // 添加第三方应用设置按钮
                                    name: 'addThird',
                                    title: __('Add_third'),
                                    classname: 'btn btn-xs btn-primary btn-dialog',
                                    icon: 'fa fa-plug',
                                    url: 'merchant/third/add',
                                    visible: function(value,row,index) {
                                        //不是超级管理员，不可添加第三方
                                        if(!Config.is_super) return false;
                                        //有父商家，则是门店，门店不能添加第三方应用
                                        else if(value.pmerchant.id) return false;
                                        return true;
                                    },
                                },
                                {
                                    // 获取微信小程序二维码按钮
                                    name: 'wxacode',
                                    title: __('Wxacode'),
                                    classname: 'btn btn-xs btn-primary btn-dialog',
                                    icon: 'fa fa-qrcode',
                                    url: 'merchant/merchant/wxacode',
                                },
                            ]
                        }
                    ]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
        },
        recyclebin: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    'dragsort_url': ''
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: 'merchant/merchant/recyclebin' + location.search,
                pk: 'id',
                sortName: 'deletetime',
                sortOrder: 'desc',
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Merchant_id')},
                        {field: 'merchant_name', title: __('Merchant_name')},
                        {
                            field: 'deletetime',
                            title: __('Deletetime'),
                            operate: 'RANGE',
                            addclass: 'datetimerange',
                            formatter: Table.api.formatter.datetime
                        },
                        {
                            field: 'operate',
                            width: '130px',
                            title: __('Operate'),
                            table: table,
                            events: Table.api.events.operate,
                            buttons: [
                                {
                                    name: 'Restore',
                                    text: __('Restore'),
                                    classname: 'btn btn-xs btn-info btn-ajax btn-restoreit',
                                    icon: 'fa fa-rotate-left',
                                    url: 'merchant/merchant/restore',
                                    refresh: true
                                }
                            ],
                            formatter: Table.api.formatter.operate
                        }
                    ]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
        },
        add: function () {
            Controller.api.bindevent();
        },
        edit: function () {
            Controller.api.bindevent();
        },
        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"));
            },
            formatter: {//渲染的方法
                switch_merchant: function (value, row, index) {
                    if( Config.merchant_id == 0 ) {
                        return '<span class="btn btn-primary btn-switch"><i class="fa fa-config"></i>'+__('Select')+'</span>';
                    } else if( row.id == Config.merchant_id ) {
                        return '<span class="btn btn-info" style="cursor: default!important;"><i class="fa fa-config"></i>'+__('Current')+'</span>';
                    } else {
                        return '<span class="btn btn-primary btn-switch"><i class="fa fa-config"></i>'+__('Switch')+'</span>';
                    }
                },
            },
            events: {
                switch_merchant: {
                    'click .btn-switch': function (e, value, row, index) {
                        $.get(
                            'merchant/merchant/selectmerchant/ids/'+row.id,
                            function (data) {
                                Layer.alert(data.msg, function () {
                                    top.window.location.reload();
                                });
                            }
                        )
                    }
                },
            }
        }
    };
    return Controller;
});