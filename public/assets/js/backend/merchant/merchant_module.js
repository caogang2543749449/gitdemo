define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'merchant/merchant_module/index',
                    add_url: '',
                    edit_url: '',
                    del_url: '',
                    multi_url: '',
                    open_url: 'merchant/merchant_module/toggleOpen',
                    dragsort_url: 'merchant/merchant_module/sort',
                    category_url: 'merchant/merchant_module/categories',
                    table: 'merchant_module',
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
                            {field: 'is_selected', title: __('Opened'), formatter: Table.api.formatter.toggle, url: $.fn.bootstrapTable.defaults.extend.open_url},
                            {field: 'id', title: __('Id')},
                            {field: 'key', title: __('Key')},
                            {field: 'name', title: __('Name')},
                            {field: 'target_type', title: __('Target_type'), searchList: JSON.parse(Config.targetTypeList), formatter: Table.api.formatter.normal, operate: false},
                            {field: 'is_standard', title: __('Standard'), searchList: {"1":__('Standard'),"0":__('Option')}, formatter: Table.api.formatter.normal, operate: false},
                            {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime, visible: false},
                            {field: 'updatetime', title: __('Updatetime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                            {
                                title: __('Category'),
                                classname: 'btn btn-info btn-dialog',
                                formatter: Controller.api.formatter.category,
                            },
                            {
                                title: __('Move'),
                                table: table,
                                events: Table.api.events.operate,
                                formatter: Table.api.formatter.operate,
                                buttons: [
                                    {
                                        // 排序按钮，只有选中的才能排序
                                        name: 'dragsort',
                                        icon: 'fa fa-arrows',
                                        title: __('Drag to sort'),
                                        extend: 'data-toggle="tooltip"',
                                        classname: 'btn btn-xs btn-primary btn-dragsort',
                                        visible: function(value,row,index) {
                                            if(value.is_selected) return true;
                                            return false;
                                        },
                                    }
                                ]
                            }
                        ],
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
            table.on('post-body.bs.table', function (e, settings, json, xhr) {
                $(this).bootstrapTable('expandAllRows');
            });

        },
        categories: function () {
            Controller.api.bindevent();
        },
        api: {
            bindevent: function () {
                $(document).on('click', "input[name='row[ismenu]']", function () {
                    var name = $("input[name='row[name]']");
                    name.prop("placeholder", $(this).val() == 1 ? name.data("placeholder-menu") : name.data("placeholder-node"));
                });
                $("input[name='row[ismenu]']:checked").trigger("click");
                Form.api.bindevent($("form[role=form]"));
            },
            formatter: {
                category: function (value, row, index) {
                    if(row.category_url) {
                        return '<a href="'+$.fn.bootstrapTable.defaults.extend.category_url+'?ids=' + row.id + '" title="' + __('Category') + '" class="btn btn-info btn-dialog ">' + __('Setting') + '</a>';
                    }
                    return "";
                }
            },
            events: {
                category: {
                    'click .btn-category': function (e, value, row, index) {
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