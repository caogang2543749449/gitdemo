define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'merchant/coupon/index' + location.search,
                    add_url: 'merchant/coupon/add',
                    edit_url: 'merchant/coupon/edit',
                    del_url: 'merchant/coupon/del',
                    multi_url: 'merchant/coupon/multi',
                    table: 'merchant_coupon',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'weigh',
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id')},
                        {field: 'merchant.merchant_name', title: __('Merchant.merchant_name')},
                        {field: 'title', title: __('Title')},
                        {field: 'store_name', title: __('Store_name')},
                        // {field: 'store_logo_image', title: __('Store_logo_image'), events: Table.api.events.image, formatter: Table.api.formatter.image},
                        {field: 'rule', title: __('Rule')},
                        // {field: 'store_address', title: __('Store_address')},
                        {field: 'start_time', title: __('Start_time'), operate:'RANGE', addclass:'datetimerange'},
                        {field: 'end_time', title: __('End_time'), operate:'RANGE', addclass:'datetimerange'},
                        // {field: 'qr_image', title: __('Qr_image'), events: Table.api.events.image, formatter: Table.api.formatter.image},
                        {field: 'tax_free_switch', title: __('Tax_free_switch'), searchList: {"1":__('Yes'),"0":__('No')}, formatter: Table.api.formatter.toggle},
                        {field: 'open_switch', title: __('Open_switch'), searchList: {"1":__('Yes'),"0":__('No')}, formatter: Table.api.formatter.toggle},
                        // {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'updatetime', title: __('Updatetime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate},
                        // {field: 'commonsearch_tab_selector', visible: false},
                    ]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);

            //绑定TAB事件
            $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
                // var options = table.bootstrapTable(tableOptions);
                var typeStr = $(this).attr("href").replace('#', '');
                var options = table.bootstrapTable('getOptions');
                options.pageNumber = 1;
                options.queryParams = function (params) {
                    // params.filter = JSON.stringify({type: typeStr});
                    params.valid = typeStr;

                    return params;
                };
                table.bootstrapTable('refresh', {});
                return false;

            });

            // let commonsearch_tab_selector = $("<input>").attr({"id":"commonsearch_tab_selector", "name":"commonsearch_tab_selector"}).hide();
            // // let commonsearch_tab_selector = $("<select>").attr({"id":"commonsearch_tab_selector", "name":"commonsearch_tab_selector"}).hide();
            // // $("<option/>").attr({"value":"0"}).appendTo(commonsearch_tab_selector);
            // // $("<option/>").attr({"value":"1"}).appendTo(commonsearch_tab_selector);
            // $(".form-commonsearch").append(commonsearch_tab_selector);
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
                url: 'merchant/coupon/recyclebin' + location.search,
                pk: 'id',
                sortName: 'id',
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id')},
                        {field: 'title', title: __('Title'), align: 'left'},
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
                                    url: 'merchant/coupon/restore',
                                    refresh: true
                                },
                                {
                                    name: 'Destroy',
                                    text: __('Destroy'),
                                    classname: 'btn btn-xs btn-danger btn-ajax btn-destroyit',
                                    icon: 'fa fa-times',
                                    url: 'merchant/coupon/destroy',
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
            }
        }
    };
    $("#c-valid_switch").change(function(){
        $("#c-effe_days").parent().parent().hide();
        $("#c-start_time").parent().parent().hide();
        $("#c-end_time").parent().parent().hide();
        $("#c-effe_days").prop('disabled', true);
        $("#c-start_time").prop('disabled', true);
        $("#c-end_time").prop('disabled', true);

        if($(this).val() == 0) {
            $("#c-start_time").parent().parent().show();
            $("#c-end_time").parent().parent().show();
            $("#c-start_time").prop('disabled', false);
            $("#c-end_time").prop('disabled', false);
        } else if($(this).val() == 2) {
            $("#c-effe_days").parent().parent().show();
            $("#c-effe_days").prop('disabled', false);
        }
    }).change();
    $("#c-store_address").change(function(){
        if($(this).val().length > 0 ) {
            $("#f-gis_coord").show();
            $("#c-gis_coord").prop('disabled', false);
        } else {
            $("#f-gis_coord").hide();
            $("#c-gis_coord").prop('disabled', true);
        }
    }).change();

    var cCount = $("#c-get_limit").val();
    if(cCount == 0) {
        $("#c-get-limit-selector").val(0);
    } else {
        $("#c-get-limit-selector").val(1);
    }
    $("#c-get-limit-selector").change(function(){
        if($(this).val() == 0 ) {
            cCount = $("#c-get_limit").val();
            if(cCount == 0) {
                cCount = 1;
            }
            $("#c-get_limit").val(0);
            $("#c-get_limit").hide();
        } else {
            $("#c-get_limit").val(cCount);
            $("#c-get_limit").show();
        }
    }).change();
    return Controller;
});