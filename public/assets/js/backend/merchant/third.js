define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'merchant/third/index' + location.search,
                    add_url: 'merchant/third/add',
                    edit_url: 'merchant/third/edit',
                    del_url: 'merchant/third/del',
                    multi_url: 'merchant/third/multi',
                    table: 'merchant_third',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'id',
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id'), visible: false},
                        {field: 'merchant_id', title: __('Merchant_id')},
                        {field: 'merchant.merchant_name', title: __('Merchant_name')},
                        {field: 'third_type', title: __('Third_type'), searchList: Config.thirdTypeList, formatter: Table.api.formatter.normal, operate: false},
                        {field: 'app_name', title: __('App_name')},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
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
            }
        }
    };
    return Controller;
});