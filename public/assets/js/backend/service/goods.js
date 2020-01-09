define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'service/goods/index' + location.search,
                    add_url: 'service/goods/add',
                    edit_url: 'service/goods/edit',
                    del_url: 'service/goods/del',
                    multi_url: 'service/goods/multi',
                    copy_url: 'service/goods/copy',
                    table: 'goods',
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
                        {field: 'thumb_image', title: __('Thumb_image'), events: Table.api.events.image, formatter: Table.api.formatter.image, operate:false},
                        {field: 'name', title: __('Name'), formatter: Controller.api.formatter.goodsName},
                        {field: 'category_id', title: __('Category.name'), searchList: Config.categoryList, formatter: Table.api.formatter.normal},
                        {field: 'goods_sn', title: __('Goods_sn')},
                        {field: 'price', title: __('Price'), operate:'BETWEEN'},
                        {field: 'is_for_sale', title: __('Is_for_sale'), searchList: {0: __('Not For Sale'), 1: __('For Sale')}, formatter: Table.api.formatter.normal},
                        {
                            field: 'operate',
                            title: __('Operate'),
                            table: table,
                            events: Table.api.events.operate,
                            formatter: Table.api.formatter.operate,
                            buttons: [
                                {
                                    name: 'edit',
                                    icon: 'fa fa-pencil',
                                    title: __('Edit'),
                                    extend: 'data-toggle="tooltip" data-area=\'["95%", "95%"]\'',
                                    classname: 'btn btn-xs btn-success btn-editone',
                                    url: $.fn.bootstrapTable.defaults.extend.edit_url
                                },
                                {
                                    name: 'copy',
                                    icon: 'fa fa-copy',
                                    title: __('Copy'),
                                    extend: 'data-toggle="tooltip" data-area=\'["95%", "95%"]\'',
                                    classname: 'btn btn-xs btn-success btn-dialog',
                                    url: $.fn.bootstrapTable.defaults.extend.copy_url
                                },
                            ]
                        }
                    ]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);

            // 其他按钮事件
            $('.btn-put-on').on('click', function () {
                
            })
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
                url: 'service/goods/recyclebin' + location.search,
                pk: 'id',
                sortName: 'id',
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id')},
                        {field: 'name', title: __('Name'), align: 'left'},
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
                                    url: 'service/goods/restore',
                                    refresh: true
                                },
                                {
                                    name: 'Destroy',
                                    text: __('Destroy'),
                                    classname: 'btn btn-xs btn-danger btn-ajax btn-destroyit',
                                    icon: 'fa fa-times',
                                    url: 'service/goods/destroy',
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
            Controller.api.bindevent([]);
        },
        edit: function () {
            Controller.api.edit_method();
        },
        copy: function () {
            Controller.api.edit_method();
        },
        api: {
            bindevent: function (data) {
                // 商品属性表格初始化
                var attrTable = $("#attr-table");
                attrTable.bootstrapTable({
                    data: data,
                    uniqueId: 'id',
                    columns: [
                        [
                            {field: 'id', title: __('Id'), visible: false},
                            {field: 'name', title: __('Attribute name')+'*', formatter: Controller.api.formatter.attribute},
                            {field: 'value', title: __('Attribute value')+'*', formatter: Controller.api.formatter.attribute},
                            {
                                field: 'operate',
                                title: __('Operate'),
                                formatter: function() {
                                    return '<button type="button" class="btn btn-xs btn-danger btn-remove_attr">' + __('Del') + '</button>';
                                },
                                events: {
                                    'click .btn-remove_attr': function(e, value, row) {
                                        attrTable.bootstrapTable('removeByUniqueId', row.id);
                                    }
                                },
                            }
                        ]
                    ],
                    // 点击编辑
                    onClickCell : function(field,value,row,$element) {
                        var upIndex = $element[0].parentElement.rowIndex - 1;
                        if (field != 'operate' && $element[0].innerHTML.indexOf("inputCell") == -1 ) {
                            $element[0].innerHTML = "<input id='inputCell' type='text' name='inputCell' style='width 70%' maxlength='100' value='" + value + "'>";
                            inputElement = $("#inputCell");
                            // 屏蔽回车提交事件
                            inputElement.keydown( function(e) {
                                var key = window.event?e.keyCode:e.which;
                                if(key.toString() == "13"){
                                    inputElement.blur();
                                    return false;
                                }
                            });
                            inputElement.blur(function () {
                                var newValue = $("#inputCell").val();
                                row[field] = newValue;
                                $(this).remove();
                                attrTable.bootstrapTable('updateCell', {
                                    index: upIndex,
                                    field: field,
                                    value: newValue
                                });
                            });
                            inputElement.focus();
                        }
                    }
                });

                // 为表格绑定事件
                $('.btn-attribute').on('click', function () {
                    var data = attrTable.bootstrapTable('getData');
                    if(data.length >= 10 ) {
                        Backend.api.msg(__("Only 10 Attribute can be added."));
                        return false;
                    }
                    id = 0;
                    if(data.slice(-1)[0]!==undefined) {
                        id = data.slice(-1)[0].id + 1;
                    }
                    attrTable.bootstrapTable('append', [{
                        id: id,
                        name: '',
                        value: '',
                    }]);
                });

                Form.api.bindevent($("form[role=form]"), null, null, function () {
                    $("#c-attributes").val(JSON.stringify(attrTable.bootstrapTable('getData')));
                    return true;
                });

            },
            formatter: {
                attribute: function (value) {
                    if( value == '' ) {
                        return '<span class="text-gray">' + __('Click to edit') + '</span>';
                    } else {
                        return value;
                    }
                },
                goodsName: function (value, row) {
                    return '<span>' + value + '</span><br><span class="text-blue">' + row.local_name + '</span>';
                }
            },
            edit_method: function () {
                var data;
                if($("#c-attributes").val() !== '') {
                    data = JSON.parse($("#c-attributes").val());
                } else {
                    data = [];
                }
                Controller.api.bindevent(data);
            }
        }
    };
    return Controller;
});