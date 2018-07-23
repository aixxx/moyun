define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
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

function download() {
    var lottoTime = $("#c-lottoTime").val(),
        lottoTime2 = $("#c-lottoTime2").val();
    window.location.href = 'download?lottoTime='+ lottoTime + '&lottoTime2=' + lottoTime2;
}