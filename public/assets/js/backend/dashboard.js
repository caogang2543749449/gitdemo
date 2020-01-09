define(['jquery', 'bootstrap', 'backend', 'addtabs', 'table', 'echarts', 'echarts-theme', 'template'], function ($, undefined, Backend, Datatable, Table, Echarts, undefined, Template) {
    var Controller = {
        index: function () {
            // 基于准备好的dom，初始化echarts实例
            var myChart = Echarts.init(document.getElementById('echart'), 'walden');

            // 指定图表的配置项和数据
            var option = {
                title: {
                    text: '',
                    subtext: ''
                },
                tooltip: {
                    trigger: 'axis'
                },
                legend: {
                    data: [__('User Signup'), __('Access Count')]
                },
                toolbox: {
                    show: false,
                    feature: {
                        magicType: {show: true, type: ['stack', 'tiled']},
                        saveAsImage: {show: true}
                    }
                },
                xAxis: {
                    type: 'category',
                    boundaryGap: false,
                    data: Userdata.column,
                },
                yAxis: [{
                    type: 'value',
                    max: Math.ceil(Math.max.apply(null,Userdata.weekuserlist)/5)*5,
                    interval: Math.ceil(Math.max.apply(null,Userdata.weekuserlist)/5),
                    axisLabel : {
                        show: false
                    }
                }, {
                    type: 'value',
                    max: Math.ceil(Math.max.apply(null,Userdata.weekaccesslist)/5)*5,
                    interval: Math.ceil(Math.max.apply(null,Userdata.weekaccesslist)/5),
                    axisLabel : {
                        show: false
                    }
                }],
                grid: [{
                    left: 20,
                    top: 'top',
                    right: 30,
                    bottom: 30
                }],
                series: [{
                    name: __('User Signup'),
                    type: 'line',
                    smooth: true,
                    areaStyle: {
                        normal: {}
                    },
                    lineStyle: {
                        normal: {
                            width: 1.5
                        }
                    },
                    data: Userdata.weekuserlist
                }, {
                    name: __('Access Count'),
                    type: 'line',
                    smooth: true,
                    areaStyle: {
                        normal: {}
                    },
                    lineStyle: {
                        normal: {
                            width: 1.5
                        }
                    },
                    yAxisIndex: 1,
                    data: Userdata.weekaccesslist
                }]
            };

            // 使用刚指定的配置项和数据显示图表。
            myChart.setOption(option);

            //动态添加数据，可以通过Ajax获取数据然后填充
            setInterval(function () {

                Fast.api.ajax({
                    type: "GET",
                    url: "dashboard/getlastuserdata",
                    loading: false,
                }, function (data, ret) {
                    if ( Userdata.column[Userdata.column.length] == data.dayList[data.dayList]) {
                        //不跨天，更新最后一天
                        Userdata.weekuserlist.pop();
                        Userdata.weekuserlist.push(data.userList.pop());
                        Userdata.weekaccesslist.pop();
                        Userdata.weekaccesslist.push(data.accessList.pop());
                    } else {
                        //跨天， 删除第一天，更新最后一天，结尾添加新的一天
                        Userdata.column.shift();
                        Userdata.column.pop();
                        Userdata.column.concat(data.dayList);
                        Userdata.weekuserlist.shift();
                        Userdata.weekuserlist.pop();
                        Userdata.weekuserlist.concat(data.userList);
                        Userdata.weekaccesslist.shift();
                        Userdata.weekaccesslist.pop();
                        Userdata.weekaccesslist.concat(data.accessList);
                    }

                    myChart.setOption({
                        xAxis: {
                            data: Userdata.column
                        },
                        series: [{
                            name: __('User Signup'),
                            data: Userdata.weekuserlist
                        }, {
                            name: __('Access Count'),
                            data: Userdata.weekaccesslist
                        }]
                    });

                    //设置menu的问询数量高亮显示
                    Backend.api.sidebar({
                        'merchant/message': [data.newMsg, 'red', 'badge']
                    });

                    return false;
                });

                if ($("#echart").width() != $("#echart canvas").width() && $("#echart canvas").width() < $("#echart").width()) {
                    myChart.resize();
                }
            }, 60000);
            $(window).resize(function () {
                myChart.resize();
            });

            $(document).on("click", ".btn-checkversion", function () {
                top.window.$("[data-toggle=checkupdate]").trigger("click");
            });

            parent.$("#con_1", parent.document).on('cssClassChanged', function () {
                myChart.resize();
            });
        }
    };

    return Controller;
});