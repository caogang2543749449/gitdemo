define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'wifi/wifi/index' + location.search,
                    add_url: 'wifi/wifi/add',
                    edit_url: 'wifi/wifi/edit',
                    del_url: 'wifi/wifi/del',
                    multi_url: 'wifi/wifi/multi',
                    table: 'merchant_wifi',
                }
            });

            var table = $("#table");

            //检索框提示
            $.fn.bootstrapTable.locales[Table.defaults.locale]['formatSearch'] = function(){
                return __('Ssid');
            };
            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'weigh',
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id'), visible: false, operate: false},
                        {field: 'merchant_id', title: __('Merchant_id'), visible: false, operate: false},
                        {field: 'merchant.merchant_name', title: __('Merchant_name'), visible:false, operate: false},
                        {field: 'wifi_type', title: __('Wifi_type'), searchList: {"normal":__('Normal'),"dynamic":__('Dynamic')}, formatter: Table.api.formatter.normal},
                        {field: 'ssid', title: __('Ssid')},
                        {field: 'description', title: __('Description')},
                        {field: 'show_policy_flg', title: __('Customize Policy'), searchList: {"-1":__('Inherit'),"1":__('Show'),"0":__('Not Show')}, formatter: Table.api.formatter.normal, operate: false},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
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
                url: 'wifi/wifi/recyclebin' + location.search,
                pk: 'id',
                sortName: 'id',
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id'), visible: false},
                        {field: 'merchant_id', title: __('Merchant_id'), visible: false},
                        {field: 'merchant.merchant_name', title: __('Merchant_name'), visible: false},
                        {field: 'ssid', title: __('Ssid')},
                        {field: 'description', title: __('Description')},
                        {field: 'show_policy_flg', title: __('Customize Policy'), searchList: {"-1":__('Inherit'),"1":__('Show'),"0":__('Not Show')}, formatter: Table.api.formatter.normal, operate: false},
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
                                    url: 'wifi/wifi/restore',
                                    refresh: true
                                },
                                {
                                    name: 'Destroy',
                                    text: __('Destroy'),
                                    classname: 'btn btn-xs btn-danger btn-ajax btn-destroyit',
                                    icon: 'fa fa-times',
                                    url: 'wifi/wifi/destroy',
                                    refresh: true
                                }
                            ],
                            formatter: Table.api.formatter.operate
                        }
                    ]
                ],
                search: false,
                commonSearch: false,
                showToggle: false,
                showColumns: false,
                showExport: false
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
            }
        }
    };
    $("#c-wifi_type").change(function(v){
        let isDynamic= $(this).val() == 'dynamic';
        if(isDynamic) {
            $("#f-dynamic_content").removeClass('hidden');
            $("#f-dynamic_message").removeClass('hidden');
        } else {
            $("#f-dynamic_content").addClass('hidden');
            $("#f-dynamic_message").addClass('hidden');
        }
    }).change();
    return Controller;
});