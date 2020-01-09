define(['jquery', 'bootstrap', 'backend', 'form'], function ($, undefined, Backend, Form) {

    var Controller = {
        index: function () {
            Controller.api.bindevent();
        },
        edit: function () {
            Controller.api.bindevent();
        },
        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"), null, null, function () {
                    Form.api.submit(
                        $("form[role=form]"),
                        function (data, ret) {
                            location.href = ret.url;
                            return false;
                        });
                    return false;
                });
                $(".btn-cancel").on('click', function () {
                    Backend.api.addtabs('wifi/policy');
                })
            },
        }
    };
    return Controller;
});