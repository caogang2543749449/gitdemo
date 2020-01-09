define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {
    // 初始化表格参数配置
    Table.api.init({
        extend: {
            index_url: 'order/order/index' + location.search,
            detail_url: 'order/order/detail',
            next_url: 'order/order/next_step',
            refund_url: 'order/order/refund',
            table: 'order',
        }
    });

    var Controller = {
        index: function () {
            var table = $("#table");
            //检索框提示
            $.fn.bootstrapTable.locales[Table.defaults.locale]['formatSearch'] = function(){
                return __('Order_sn')+','+__('checkin_room')+','+__('checkin_name');
            };
            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'delivery_hope_date,delivery_hope_time,createtime',
                sortOrder: 'asc',
                columns: [
                    [
                        // {checkbox: true},
                        {field: 'order_sn', title: __('Order_sn')},
                        {field: 'real_money', title: __('Order_money')},
                        {field: 'status', title: __('Status'), searchList: Config.statusList, formatter: Table.api.formatter.status},
                        {field: '', title: __('Delivery_hope'), operate:false, addclass:'datetimerange', formatter: Controller.api.formatter.hopetime},
                        {field: 'checkin_room', title: __('Checkin_room')},
                        {field: 'checkin_name', title: __('Checkin_name')},
                        {field: 'createtime', title: __('Createtime'), operate:false, addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {
                            field: 'operate',
                            title: __('Operate'),
                            table: table,
                            events: Table.api.events.operate,
                            formatter: Table.api.formatter.operate,
                            buttons: [
                                {
                                    // 详情
                                    name: 'detail',
                                    text: __('Detail'),
                                    classname: 'btn btn-info btn-dialog',
                                    extend: 'data-toggle="tooltip" data-area=\'["95%", "95%"]\'',
                                    icon: 'fa fa-edit',
                                    url: $.fn.bootstrapTable.defaults.extend.detail_url
                                },
                            ]
                        }
                    ]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
        },
        detail: function () {
            Controller.api.bindevent();
            Controller.api.initGoodsTable();
            $('.btn-nextStep').on('click', function () {
                var nextStep = $('#c-next_step').val();
                Fast.api.ajax({
                    url: $.fn.bootstrapTable.defaults.extend.next_url+ '/ids/' + $('#c-id').val(),
                    data: {
                        nextStep: nextStep
                    }
                }, function (data) {
                    $('.btn-refresh').trigger("click");
                });
            });
        },
        refund: function () {
            Controller.api.bindevent();
            Controller.api.initRefundTable();
            $("[name='refund[refund_reason_code]']").on('change', function(){
                if($(this).val()=='O') {
                    $(".reason").toggleClass('hidden', false);
                } else {
                    $(".reason").toggleClass('hidden', true);
                }
            });
        },
        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"));
                $('.btn-refresh').on('click', function () {
                    var index = parent.Layer.getFrameIndex(window.name);
                    parent.Layer.iframeSrc(index, Fast.api.fixurl($.fn.bootstrapTable.defaults.extend.detail_url+ '/ids/' + $('#c-id').val()) + '?dialog=1');
                });
            },
            initGoodsTable: function() {
                var goodsTable = $("#goods-table");
                // 初始化表格
                goodsTable.bootstrapTable({
                    url: $.fn.bootstrapTable.defaults.extend.detail_url + '/ids/' + $('#c-id').val(),
                    pk: 'id',
                    columns: [
                        [
                            {field: 'thumb_image', title: __('Thumb_image'), events: Table.api.events.image, formatter: Table.api.formatter.image, operate:false},
                            {field: 'goods_sn', title: __('Goods_sn')},
                            {field: 'goods_name', title: __('Goods_name')},
                            {field: 'price', title: __('Price'), formatter: Controller.api.formatter.number},
                            {field: 'quantity', title: __('Quantity')},
                            {field: 'refunded_quantity', title: __('Refunded_quantity')},
                            {field: '', title: __('Total_money'), formatter: Controller.api.formatter.goodsMoney},
                            {
                                field: 'operate',
                                title: __('Operate'),
                                table: goodsTable,
                                events: Table.api.events.operate,
                                formatter: Table.api.formatter.operate,
                                buttons: [
                                    {
                                        // 详情
                                        name: 'refund',
                                        text: __('Action refund'),
                                        classname: 'btn btn-danger btn-dialog',
                                        extend: 'data-toggle="tooltip"',
                                        url: function(row) {
                                            return $.fn.bootstrapTable.defaults.extend.refund_url + '?ids=' + $('#c-id').val() + '&refund_id=' + row.id;
                                        },
                                        visible: function(row) {
                                            return Config.canRefund && row.quantity > row.refunded_quantity;
                                        },
                                    },
                                ]
                            }
                        ]
                    ],
                    sortable: false,
                    pagination: false,
                    search: false,
                    commonSearch: false,
                    showToggle: false,
                    showColumns: false,
                    showExport: false,
                });
                Table.api.bindevent(goodsTable);
                goodsTable.on('load-success.bs.table', function (e, data) {
                    if(data.status != undefined) {
                        $('#c-status').text(data.status);
                    }
                    if(data.refundedMoney != undefined) {
                        $('#c-refunded_money').text(data.refundedMoney);
                        if( data.refundedMoney > 0) {
                            $('#b-refund_info').toggleClass('hidden', false);
                        }
                    }
                    if(data.lastRefundTime != undefined) {
                        $('#c-last_refund_time').text(data.lastRefundTime);
                    }
                    return true;
                });
            },
            initRefundTable: function() {
                var goodsTable = $("#goods-table");
                // 初始化表格
                goodsTable.bootstrapTable({
                    data: orderGoods,
                    pk: 'id',
                    columns: [
                        [
                            {field: 'thumb_image', title: __('Thumb_image'), events: Table.api.events.image, formatter: Table.api.formatter.image, operate:false},
                            {field: 'goods_sn', title: __('Goods_sn')},
                            {field: 'goods_name', title: __('Goods_name')},
                            {field: 'price', title: __('Price'), formatter: Controller.api.formatter.number},
                            {field: 'quantity', title: __('Quantity')},
                            {field: 'refunded_quantity', title: __('Refunded_quantity')},
                        ]
                    ],
                    sortable: false,
                    pagination: false,
                    search: false,
                    commonSearch: false,
                    showToggle: false,
                    showColumns: false,
                    showExport: false,
                });
                Table.api.bindevent(goodsTable);
            },
            formatter: {
                hopetime: function (value, row, index) {
                    if(!row.delivery_hope_date) {
                        return __('Immediately');
                    } else {
                        var datetimeFormat = 'YYYY-MM-DD';
                        return Moment(row.delivery_hope_date).format(datetimeFormat)+' '+row.delivery_hope_time+__('O\'clock')+'~'+(row.delivery_hope_time+1)+__('O\'clock');
                    }
                },
                goodsMoney: function (value, row, index) {
                    return Math.floor(row.price * (row.quantity - row.refunded_quantity));
                },
                number: function (value) {
                    return Math.floor(value);
                }
            },
        }
    };
    return Controller;
});