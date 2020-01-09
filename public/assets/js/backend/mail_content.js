define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'mail_content/index' + location.search,
                    add_url: 'mail_content/add',
                    edit_url: 'mail_content/edit',
                    del_url: 'mail_content/del',
                    multi_url: 'mail_content/multi',
                    table: 'mail_content',
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
                        {field: 'id', title: __('Id')},
                        {field: 'merchant_id', title: __('Merchant_id')},
                        {field: 'type', title: __('Type'), searchList: {"message_system_receiving":__('Message_system_receiving'),"message_normal":__('Message_normal'),"":__('')}, formatter: Table.api.formatter.normal},
                        {field: 'title', title: __('Title')},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
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
    // add && edit
    $("#translate").click(function(){
        let content = $("#c-content").val();
        $.ajax({
            method: "POST",
            url: "/api/translate",
            data: { sourceText: content, source: 'jp', target:'zh' }
          })
        .done(function( res ) {
            if(!res.content){return;}
            $("#c-content_trans").val(res.content.targetText);
        });
    });
    return Controller;
});