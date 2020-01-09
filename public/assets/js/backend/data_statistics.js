define(['jquery', 'bootstrap', 'backend', 'addtabs', 'table', 'echarts', 'echarts-theme', 'template', 'form'], function ($, undefined, Backend, Datatable, Table, Echarts, undefined, Template, Form) {
    var option = {
        title: {
            text: '',
            subtext: ''
        },
        legend: {
            textStyle: {
                color: '#c33',
                fontSize: 20,
            }
        },
        tooltip : {
            trigger: 'axis',
            axisPointer: {
                type: 'cross',
                label: {
                    backgroundColor: '#6a7985'
                }
            }
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
        },
        yAxis: [{
            type: 'value',
            axisLabel : {
                show: true,
            }
        }],
        grid: [{
            left: 30,
            top: 30,
            right: 30,
            bottom: 30
        }],
        series: [{
            type: 'bar',
            smooth: true,
            areaStyle: {
                normal: {}
            },
            lineStyle: {
                normal: {
                    width: 1.5
                }
            },
            label: {
                show: true,
                position: 'insideTop',
                offset: [0, 5]
            },
        }]
    };
    var Controller = {
        platform: function () {
            // 基于准备好的dom，初始化echarts实例
            var myChart = Echarts.init(document.getElementById('echart'), 'walden');
            myChart.setOption(option);

            Form.api.bindevent($("form[role=form]"), null, null, function () {
                Form.api.submit(
                    $("form[role=form]"),
                    function (data, ret) {
                        // set total user
                        $("#t-user").text(data.totaluser);
                        // set total scan open
                        $("#t-scan").text(data.scanopen);
                        // set total normal open
                        $("#t-normal").text(data.normalopen);
                        // set total share
                        $("#t-share").text(data.share);

                        // set result
                        var Userdata = {
                            columns: JSON.parse(data.columns),
                            plainValues: JSON.parse(data.values),
                            values: []
                        };
                        var count = 0;
                        jQuery.each(Userdata.plainValues, function (i, item) {
                            count += item;
                            if (item == 0) {
                                Userdata.values[i] = {
                                    value: item,
                                    label: {
                                        show: false,
                                    },
                                };
                            } else {
                                Userdata.values[i] = {
                                    value: item,
                                    label: {
                                        show: true,
                                    },
                                };
                            }
                        });
                        var option = {
                            xAxis: {
                                data: Userdata.columns,
                            },
                            yAxis: [{
                                max: Math.ceil(Math.max.apply(null,Userdata.plainValues)/5)*5,
                                interval: Math.ceil(Math.max.apply(null,Userdata.plainValues)/5),
                            }],
                            series: [{
                                name: data.legend,
                                data: Userdata.values
                            }],
                            legend: {
                                formatter: '{name} ' + __('Total') + ':' + count
                            }
                        };

                        myChart.setOption(option);
                    });
                return false;
            });

            $("[name='search[type]']").on("change", function () {
                if($(this).val()=="1") {
                    $(".timeline-row").toggleClass("hidden", false);
                    $(".period-row").toggleClass("hidden", true);
                } else {
                    $(".timeline-row").toggleClass("hidden", true);
                    $(".period-row").toggleClass("hidden", false);
                }
            });

            $("form[role=form]").ready(function () {
                $("button[type=submit]").click();
            });

            $(window).resize(function () {
                myChart.resize();
            });

        },
        regist: function() {
            //成功的回调
            var myChart = Echarts.init(document.getElementById('echart'), 'walden');
            Form.api.bindevent($("form[role=form]"), null, null, function () {
                Form.api.submit(
                    $("form[role=form]"),
                    function (data, ret) {
                        let legendData = [__('Regist Count')];
                        let xAxisData = [];
                        let barData = [];
                        data[0].forEach(element => {
                            if(element['id'] < 1) {return;}
                            xAxisData.push(element.merchant_name);
                            barData.push(element['Regist Count']);
                        });
                        $('#echart').css("height", (data[0].length*40)+"px");
                        myChart.resize();
                        if( data[0].length > 0 ) {
                            $('#nodata').toggleClass("hidden", true);
                        } else {
                            $('#nodata').toggleClass("hidden", false);
                        }

                        let seriesData = [{
                            name: __('Regist Count'),
                            type:'bar',
                            stack: 'all',
                            label: {
                                normal: {
                                    position: 'right',
                                    show: true
                                }
                            },
                            data:barData
                        }];
                        myChart.setOption(Object.assign(option, {
                            grid: [{
                                left: 150,
                                top: 80,
                                right: 30,
                                bottom: 30
                            }],
                            legend: {
                                data:legendData
                            },
                            xAxis: [{
                                type: 'value',
                                axisLabel : {
                                    show: true,
                                    color: 'black',
                                }
                            }],
                            yAxis : [
                                {
                                    type : 'category',
                                    boundaryGap : true,
                                    inverse : true,
                                    data : xAxisData
                                }
                            ],
                            series : Object.values(seriesData)
                        }));

                        if( data.hasOwnProperty(1) ) {
                            var aLink = document.createElement('a');
                            aLink.download = "register_" + $('#c-basetime').val() + ".csv";
                            aLink.href = "data:text/plain," + data[1];
                            aLink.click();
                            return true;
                        }
                        return false;
                    });
                return false;
            });


            $("form[role=form]").ready(function () {
                $("form[role=form]").submit();
            });
            $(window).resize(function () {
                myChart.resize();
            });

            $(".btn-download").on('click', function () {
                $("#download").val('1');
                $("form[role=form]").submit();
            });

            $(".btn-search").on('click', function () {
                $("#download").val('0');
                $("form[role=form]").submit();
            });
            
        }
    };

    return Controller;
});