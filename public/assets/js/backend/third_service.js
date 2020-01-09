define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        yahoomap: function () {
            Form.api.bindevent($("form[role=form]"));
            require(["https://map.yahooapis.jp/js/V1/jsapi?v=&appid="+Config.appid], function () {
                var point = new Y.LatLng(Config.coordinfo[1], Config.coordinfo[0]);
                var ymap = new Y.Map("map",  {
                    configure : {
                        doubleClickZoom : true,
                        scrollWheelZoom : true,
                    }
                });
                ymap.drawMap(point, 17, Y.LayerSetId.NORMAL);

                //表示地址检索
                var control = new Y.SearchControl();
                ymap.addControl(control);
                //追加当前位置标识
                var marker = new Y.Marker(point);
                ymap.addFeature(marker);

                //地图随窗口改变大小
                $(window).bind("resize", function(e){
                    ymap.updateSize();
                });

                //鼠标点击切换坐标
                $('#map').bind("click", function(e){
                    var pos = $(this).position();
                    var x = e.pageX - pos.left;
                    var y = e.pageY - pos.top;
                    var latlng = ymap.fromContainerPixelToLatLng(new Y.Point(x, y));
                    marker.setLatLng(latlng);
                });

                //点击重置按钮回到打开时的坐标
                $(document).on("click", ".btn-reset", function () {
                    ymap.panTo(point, true);
                    marker.setLatLng(point);
                });

                //点击确认按钮将当前坐标传回
                $(document).on("click", ".btn-confirm", function () {
                    Fast.api.close({coord: marker.getLatLng().toString()});
                });
            });

        },
        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"));
            },
        }
    };
    return Controller;
});

