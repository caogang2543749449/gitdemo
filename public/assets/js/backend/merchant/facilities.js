define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {
    require.config({
        paths: {
            "tagsinput": '../libs/tagsinput/bootstrap-tagsinput',
        }
    });
    require(['tagsinput'], function () { });

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'merchant/facilities/index' + location.search,
                    add_url: 'merchant/facilities/add',
                    edit_url: 'merchant/facilities/edit',
                    del_url: 'merchant/facilities/del',
                    multi_url: 'merchant/facilities/multi',
                    table: 'merchant_facilities',
                }
            });

            var table = $("#table");

            $.fn.bootstrapTable.locales[Table.defaults.locale]['formatSearch'] = function () {
                return __('Facility_name') + " " + __('Description');
            };
            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'weigh',
                columns: [
                    [
                        { checkbox: true },
                        { field: 'id', title: __('Id'), visible: false },
                        { field: 'merchant_id', title: __('Merchant_id') },
                        { field: 'merchant.merchant_name', title: __('Merchant.merchant_name') },
                        { field: 'facility_name', title: __('Facility_name') },
                        { field: 'tag', title: __('Tag'), searchList: Config.tagList, formatter: Table.api.formatter.normal, operate: false },
                        {
                            field: 'pid', title: __('Parent'), visible: true, formatter: function (value, row, index) {
                                delete row.pid; // sort without pid for ajax/weigh
                                if (!value) {
                                    return "-";
                                }
                                var item = Config.parentFacilities.find(item => item.id == value);
                                if (item && item) {
                                    return item.id + '.' + item.facility_name;
                                }
                                return "-";
                            }
                        },
                        { field: 'updatetime', title: __('Updatetime'), operate: 'RANGE', addclass: 'datetimerange', formatter: Table.api.formatter.datetime },
                        { field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate }
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
                url: 'merchant/facilities/recyclebin' + location.search,
                pk: 'id',
                sortName: 'id',
                columns: [
                    [
                        { checkbox: true },
                        { field: 'id', title: __('Id'), visible: false },
                        { field: 'merchant_id', title: __('Merchant_id') },
                        { field: 'merchant.merchant_name', title: __('Merchant.merchant_name') },
                        { field: 'facility_name', title: __('Facility_name') },
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
                                    url: 'merchant/facilities/restore',
                                    refresh: true
                                },
                                {
                                    name: 'Destroy',
                                    text: __('Destroy'),
                                    classname: 'btn btn-xs btn-danger btn-ajax btn-destroyit',
                                    icon: 'fa fa-times',
                                    url: 'merchant/facilities/destroy',
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

                var radioName = 'row[customize_flg]';
                $('input[name="'+radioName+'"]:radio').on('change', function () {
                    customize = $(this).val();
                    if(customize=='1') {
                        $('#d-description1').toggleClass('hidden', false);
                        $('#d-description0').toggleClass('hidden', true);
                        $('#c-description').val($('#c-description1').val());
                    } else {
                        $('#d-description1').toggleClass('hidden', true);
                        $('#d-description0').toggleClass('hidden', false);
                        $('#c-description').val($('#c-description0').val());
                    }
                });
                $('#c-description1').on('change', function () {
                    $('#c-description').val($(this).val());
                })
                $('#c-description0').on('change', function () {
                    $('#c-description').val($(this).val());
                })
            }
        }
    };
    return Controller;
});