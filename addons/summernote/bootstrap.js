require.config({
    paths: {
        'summernote-ja-JP': '../addons/summernote/lang/summernote-ja-JP.min',
        'summernote-zh-CN': '../addons/summernote/lang/summernote-zh-CN.min',
        'summernote-en': '../addons/summernote/js/summernote.min',
    },
    shim: {
        'summernote-ja-JP': ['../addons/summernote/js/summernote.min', 'css!../addons/summernote/css/summernote.css'],
        'summernote-zh-CN': ['../addons/summernote/js/summernote.min', 'css!../addons/summernote/css/summernote.css'],
        'summernote-en': ['css!../addons/summernote/css/summernote.css'],
    }
});

require(['form', 'upload'], function (Form, Upload) {
    var _bindevent = Form.events.bindevent;
    Form.events.bindevent = function (form) {
        _bindevent.apply(this, [form]);
        try {
            //绑定summernote事件
            if ($(".summernote,.editor", form).size() > 0) {
                require(['summernote-'+langjs], function () {
                    var imageButton = function (context) {
                        var ui = $.summernote.ui;
                        var button = ui.button({
                            contents: '<i class="fa fa-file-image-o"/>',
                            tooltip: __('Choose'),
                            click: function () {
                                parent.Fast.api.open("general/attachment/select?element_id=&multiple=true&mimetype=image/*", __('Choose'), {
                                    callback: function (data) {
                                        var urlArr = data.url.split(/\,/);
                                        $.each(urlArr, function () {
                                            var url = Fast.api.cdnurl(this);
                                            context.invoke('editor.insertImage', url);
                                        });
                                    }
                                });
                                return false;
                            }
                        });
                        return button.render();
                    };
                    var attachmentButton = function (context) {
                        var ui = $.summernote.ui;
                        var button = ui.button({
                            contents: '<i class="fa fa-file"/>',
                            tooltip: __('Choose'),
                            click: function () {
                                parent.Fast.api.open("general/attachment/select?element_id=&multiple=true&mimetype=*", __('Choose'), {
                                    callback: function (data) {
                                        var urlArr = data.url.split(/\,/);
                                        $.each(urlArr, function () {
                                            var url = Fast.api.cdnurl(this);
                                            var node = $("<a href='" + url + "'>" + url + "</a>");
                                            context.invoke('insertNode', node[0]);
                                        });
                                    }
                                });
                                return false;
                            }
                        });
                        return button.render();
                    };

                    $(".summernote,.editor", form).each(function () {
                        var height = $(this).data("height") ? $(this).data("height") : 500;
                        var onlyText = $(this).data("onlytext") ? $(this).data("onlytext") : false;
                        var maxLength = $(this).data("maxlength") ? $(this).data("maxlength") : 0;
                        var toolbar = [];
                        if( onlyText ) {
                            toolbar.push(['style', ['undo', 'redo']]);
                            toolbar.push(['fontname', ['color', 'clear']]);
                            toolbar.push(['para', ['ul', 'ol', 'paragraph']]);
                            toolbar.push(['view', ['fullscreen', 'codeview', 'help']]);
                        } else {
                            toolbar.push(['style', ['style', 'undo', 'redo']]);
                            toolbar.push(['font', ['bold', 'underline', 'strikethrough', 'clear']]);
                            toolbar.push(['fontname', ['color', /*'fontname',*/ 'fontsize']]);
                            toolbar.push(['para', ['ul', 'ol', 'paragraph', 'height']]);
                            toolbar.push(['table', ['table', 'hr']]);
                            toolbar.push(['insert', ['link', 'picture'/*, 'video'*/]]);
                            toolbar.push(['select', ['image', 'attachment']]);
                            toolbar.push(['view', ['fullscreen', 'codeview', 'help']]);
                        }
                        $(this).summernote({
                            height: height,
                            lang: langjs,
                            fontNames: [
                                'Arial', 'Arial Black', 'Serif', 'Sans', 'Courier',
                                'Courier New', 'Comic Sans MS', 'Helvetica', 'Impact', 'Lucida Grande',
                                "Open Sans", "Hiragino Sans GB", "Microsoft YaHei",
                                '微软雅黑', '宋体', '黑体', '仿宋', '楷体', '幼圆',
                            ],
                            fontNamesIgnoreCheck: [
                                "Open Sans", "Microsoft YaHei",
                                '微软雅黑', '宋体', '黑体', '仿宋', '楷体', '幼圆'
                            ],
                            toolbar: toolbar,
                            buttons: {
                                image: imageButton,
                                attachment: attachmentButton,
                            },
                            dialogsInBody: true,
                            followingToolbar: false,
                            callbacks: {
                                onChange: function (contents) {
                                    if(maxLength!=0 && contents.length>maxLength) {
                                        $('.note-status-output', $(this).next()).html(
                                            '<div class="alert alert-danger">' +
                                            __('The content is too long. The max length is %s', maxLength) +
                                            '</div>'
                                        );
                                    } else {
                                        $('.note-status-output', $(this).next()).html('');
                                    }
                                    $(this).val(contents);
                                    $(this).trigger('change');
                                },
                                onInit: function () {
                                },
                                onImageUpload: function (files) {
                                    var that = this;
                                    //依次上传图片
                                    for (var i = 0; i < files.length; i++) {
                                        Upload.api.send(files[i], function (data) {
                                            var url = Fast.api.cdnurl(data.url);
                                            $(that).summernote("insertImage", url, 'filename');
                                        });
                                    }
                                }
                            }
                        });
                    });
                });
            }
        } catch (e) {

        }

    };
});