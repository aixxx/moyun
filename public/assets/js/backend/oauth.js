define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'oauth/index',
                    add_url: 'oauth/add',
                    edit_url: 'oauth/edit',
                    del_url: 'oauth/del',
                    multi_url: 'oauth/multi',
                    table: 'oauth',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'weigh',
                search: false,
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id'), sortable: true,formatter: return_id},
                        {field: 'name', title: __('Name'), operate: 'LIKE %...%', placeholder: '模糊搜索，*表示任意字符'},
                        {field: 'vote', title: __('Vote'), operate: 'BETWEEN', sortable: true},
                        {field: 'platform', title: __('Platform'), visible:false, searchList: {"weibo":__('platform weibo'),"weixin":__('platform weixin'),"qq":__('platform qq'),"mobu":__('platform mobu')}},
                        {field: 'platform_text', title: __('Platform'), operate:false, custom:{"微博": 'success', "微信": 'info', "QQ": 'danger','mobu':'warning'}, formatter: Table.api.formatter.flag},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime}
                    ]
                ]
            });

            table.on('post-body.bs.table', function (e, settings, json, xhr) {
                $(".columns").hide();
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

function return_id(val) {
    return val - 37;
}