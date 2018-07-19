window.UEDITOR_HOME_URL = Fast.api.cdnurl("/assets/addons/ueditor/");
require.config({
    paths: {
        'ueditor.config': '../addons/ueditor/ueditor.config',
        'ueditor': '../addons/ueditor/ueditor.all.min',
        'ueditor.zh': '../addons/ueditor/lang/zh-cn/zh-cn',
        'zeroclipboard': '../addons/ueditor/third-party/zeroclipboard/ZeroClipboard.min',
    },
    shim: {
        'ueditor': {
            deps: ['zeroclipboard', 'ueditor.config'],
            exports: 'UE',
            init: function (ZeroClipboard) {
                //导出到全局变量，供ueditor使用
                window.ZeroClipboard = ZeroClipboard;
            },
        },
        'ueditor.zh': ['ueditor']
    }
});
require(['ueditor', 'ueditor.zh'], function (UE, undefined) {
    $(".editor").each(function () {
        var id = $(this).attr("id");
        $(this).removeClass('form-control');
        UE.list[id] = UE.getEditor(id, {
            serverUrl: Fast.api.fixurl('/addons/ueditor/api/'),
            allowDivTransToP: false, //阻止div自动转p标签
            initialFrameWidth: '100%',
            zIndex: 90,
            xssFilterRules: false,
            outputXssFilter: false,
            inputXssFilter: false
        });
    });

});
