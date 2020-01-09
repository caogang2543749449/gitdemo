define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'general/module/index',
                    add_url: 'general/module/add',
                    edit_url: 'general/module/edit',
                    del_url: 'general/module/del',
                    multi_url: 'general/module/multi',
                    dragsort_url: 'general/module/sort',
                    table: 'module',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                columns: [
                    {
                        title: __('Location'),
                        align: 'left',
                        formatter: function (value, row, index) {
                            return Table.api.formatter.flag.call(this, row, row, index);
                        }
                    },
                ],
                sortable: false,
                pagination: false,
                search: false,
                commonSearch: false,
                showToggle: false,
                showColumns: false,
                showExport: false,
                detailView: true,
                detailViewByClick: true,
                detailViewIcon: false,
                detailFormatter: function(index, row, element) {
                    var subTable = element.html('<table id="table-'+index+'"></table>').find('table');
                    $(subTable).bootstrapTable({
                        url: $.fn.bootstrapTable.defaults.extend.index_url + "?location=" +row,
                        pk: "id",
                        columns: [
                            {field: 'id', title: __('Id')},
                            {field: 'key', title: __('Key')},
                            {field: 'merchant.merchant_name', title: __('Merchant.merchant_name')},
                            {field: 'name', title: __('Name')},
                            {field: 'target_type', title: __('Target_type'), searchList: JSON.parse(Config.targetTypeList), formatter: Table.api.formatter.normal, operate: false},
                            {field: 'is_standard', title: __('Standard'), searchList: {"1":__('Standard'),"0":__('Option')}, formatter: Table.api.formatter.normal, operate: false},
                            {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime, visible: false},
                            {field: 'updatetime', title: __('Updatetime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                            {
                                field: 'operate',
                                title: __('Operate'),
                                table: table,
                                events: Table.api.events.operate,
                                formatter: Table.api.formatter.operate,
                                buttons: [
                                    {
                                        // 排序按钮，只有标准功能的才能排序
                                        name: 'dragsort',
                                        icon: 'fa fa-arrows',
                                        title: __('Drag to sort'),
                                        extend: 'data-toggle="tooltip"',
                                        classname: 'btn btn-xs btn-primary btn-dragsort',
                                        visible: function(value,row,index) {
                                            if(value.is_standard) return true;
                                            return false;
                                        },
                                    },
                                ]
                            }
                        ],
                        toolbar: false,
                        showRefresh: false,
                        pagination: false,
                        clickToSelect: false,
                        search: false,
                        commonSearch: false,
                        showToggle: false,
                        showColumns: false,
                        showExport: false,
                    });
                    // 子表格绑定事件
                    Table.api.bindevent(subTable);
                },
            });

            //自动展开子表格
            Table.api.bindevent(table);
            table.on('post-body.bs.table', function (e, settings, json, xhr) {
                $(this).bootstrapTable('expandAllRows');
            });
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
        }
    };
    return Controller;
});