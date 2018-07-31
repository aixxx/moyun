define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'product/index',
                    add_url: 'product/add',
                    //edit_url: 'product/edit',
                    //del_url: 'product/del',
                    multi_url: 'product/setstatus',
                    table: 'product',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'id',
                search: false,
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id')},
                        {field: 'oauth_id', title: __('Oauth_id'), visible:false},
                        {field: 'oauth_text', title: __('Oauth_text'), operate:false},
                        {field: 'image', title: __('Image'), operate:false, formatter: return_image},
                        {field: 'status', title: __('Status'), visible:false, searchList: {"0":__('Status 0'),"1":__('Status 1'),"2":__('Status 2')}},
                        {field: 'status_text', title: __('Status'), operate:false, custom:{"已通过": 'success', "审核中": 'info', "未通过": 'danger'}, formatter: Table.api.formatter.flag},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate,
                            buttons: [
                                {
                                    name: 'ajax',
                                    text: __('通过'),
                                    title: __('通过'),
                                    classname: 'btn btn-xs btn-primary btn-ajax btn-success',
                                    icon: 'fa fa-check-square-o',
                                    url: 'product/setStatus/params/1',
                                    success: function (data, ret) {
                                        //Layer.alert(ret.msg);
                                        $('.btn-refresh').click(); //重新加载数据
                                        //如果需要阻止成功提示，则必须使用return false;
                                        //return false;
                                    },
                                    error: function (data, ret) {
                                        console.log(data, ret);
                                    }
                                },
                                {
                                    name: 'ajax',
                                    text: __('不通过'),
                                    title: __('不通过'),
                                    classname: 'btn btn-xs btn-primary btn-ajax btn-danger',
                                    icon: 'fa fa-times-circle',
                                    url: 'product/setStatus/params/2',
                                    success: function (data, ret) {
                                        $('.btn-refresh').click(); //重新加载数据
                                        //如果需要阻止成功提示，则必须使用return false;
                                        //return false;
                                    },
                                    error: function (data, ret) {
                                        console.log(data, ret);
                                    }
                                }
                            ],
                            events: Table.api.events.operate, formatter: Table.api.formatter.operate
                        }
                    ]
                ]
            });

            table.on('post-body.bs.table', function (e, settings, json, xhr) {
                $(".columns").hide();
            });

            $(document.body).on("click", ".btn-primary-multi", function (e) {
                e.preventDefault();
                var that = this;
                var ids = Table.api.selectedids(table);
                Layer.confirm(
                    __('Are you sure you want to action the %s selected item?', ids.length),
                    {icon: 3, title: __('Warning'), shadeClose: true},
                    function (index) {
                        Table.api.multi("", ids, table, that);
                        Layer.close(index);
                    }
                );
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
    return Controller;
});

function return_image(value, row, index) {
    value = value ? value : '/assets/img/blank.gif';
    return '<a href="' + value + '"  class="dialogit" data-width="600px" title="'+ row.oauth_text +'"><img class="img-sm img-center" src="' + value + '" /></a>';
}