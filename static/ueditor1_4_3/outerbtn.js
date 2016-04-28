
(function(){

function renderUploader(id, ue) {

    var uploader = WebUploader.create({
        pick: {
            id: "#" + id,
            multiple: false
        },
        accept: {
            title: "word文档",
            extensions: "doc,docx",
            mimeTypes: "application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document"
        },
        fileSingleSizeLimit: 5120000,
        swf: CMS_ABS + 'images/common/ueditor1_4_3/Uploader.swf',
        server: 'http://convert.wenku.baidu.com/rtcs/convert?pn=1&rn=-1',
        fileVal: 'file',
        duplicate: true,

        // 强制 flash  采用 URLStream 上传文件, 默认是 fileReference
        forceURLStream: true
    });

    uploader.on('filesQueued', function(files){
        uploader.upload();
        uploader.disable();
    });

    uploader.on('uploadFinished', function(files){
        setTimeout(function () {
            uploader.enable();
        }, 2000);
    });

    uploader.on('all', function(){
        var args = UE.utils.clone([], arguments);
        args[0] = 'uploader_' + args[0];
        return ue.fireEvent.apply(ue, args);
    });

    //错误文件转存
    uploader.on('uploadError', function(file){
        if (!file.__sendErrorFile) {
            file.__sendErrorFile = true;
            var server = uploader.option('server');
            var N = ['00', '01', '02', '03', '04', '05', '06', '07', '08', '09'],
                d = new Date(),
                filePrefix = '' + d.getFullYear() + (N[d.getMonth()+1] || d.getMonth()+1) + (N[d.getDate()] || d.getDate());

            uploader.option('server', 'http://wordfile.duapp.com/uf-server.php?cmd=upload&target=/' + filePrefix + '/');
            uploader.retry();
            setTimeout(function (){
                uploader.option('server', server);
            },100);
        }
    });

    //创建提示的dialog
    var dialog = new UE.ui.Dialog({
        //指定弹出层中页面的路径，这里只能支持页面,因为跟addCustomizeDialog.js相同目录，所以无需加路径
        iframeUrl:'docparser/dochelp.html',
        //需要指定当前的编辑器实例
        editor:ue,
        //指定dialog的名字
        name:'dochelp',
        //dialog的标题
        title:"导入word功能介绍",
        //指定dialog的外围样式
        cssRules:"width:500px;height:248px;",
        //如果给出了buttons就代表dialog有确定和取消
        buttons:[
            {
                className:'edui-okbutton',
                label:'确定',
                onclick:function () {
                    dialog.close(true);
                }
            }
        ]
    });
    $('.uploadhelp').click(function (){
        dialog.render();
        dialog.open();
    });

}
renderUploader('uploadbtn', ue);

})();