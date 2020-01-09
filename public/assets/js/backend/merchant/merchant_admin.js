define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'merchant/merchant_admin/index' + location.search,
                    add_url: 'merchant/merchant_admin/add',
                    edit_url: '',
                    del_url: 'merchant/merchant_admin/del',
                    multi_url: 'merchant/merchant_admin/multi',
                    table: 'merchant_admin',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'merchant_id',
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id'), visible: false},
                        {field: 'merchant_id', title: __('Merchant_id')},
                        {field: 'merchant.merchant_name', title: __('Merchant_name')},
                        {field: 'admin.username', title: __('Admin.Username')},
                        {field: 'admin.nickname', title: __('Admin.Nickname')},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
        },
        recyclebin: function () {
        },
        add: function () {
            Controller.api.bindevent();
        },
        edit: function () {
        },
        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"));
            }
        }
    };
    return Controller;
});