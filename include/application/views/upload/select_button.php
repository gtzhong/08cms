<?php foreach ( array('ftypes', 'otype', 'mconfigs', '_get', 'type', 'maxcount', 'timestamp', 'base_inc_configs') as $var ) { $$var = $this->$var; } ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" >
<head>
<title>�����ϴ� - <?=$mconfigs['hostname']?></title>
<meta http-equiv="Content-Type" content="text/html; charset=<?=$base_inc_configs['mcharset']?>" />
<link href="<?=$mconfigs['cms_abs']?>images/common/upload/upload.css" rel="stylesheet" type="text/css" />
<?php
$_get['field'] = empty($_get['field']) ? '' : preg_replace('/[^\w\[\]]/', '', $_get['field']);
if( !empty($mconfigs['cms_top']) && !empty($_get['domain']) )
{
    echo '<script type="text/javascript"> document.domain = "'.$mconfigs['cms_top'].'"; </script>';
}else{
	 echo '<script type="text/javascript"> document.domain = document.domain; </script>';
}
$noRemote = empty($_get['noRemote']) ? '' : $_get['noRemote']; //�ֻ�ģ���е���(������ҳ��)
?>
<script type="text/javascript">
var CMS_ABS = '<?php echo $mconfigs['cms_abs'];?>', CMS_URL = '<?php echo $mconfigs['cmsurl'];?>', _08_ROUTE_ENTRANCE = '<?php echo _08_ROUTE_ENTRANCE;?>', UPLOAD_URL = CMS_URL + _08_ROUTE_ENTRANCE, parent_wid = <?php echo empty($_get['parent_wid']) ? 0 : (int)$_get['parent_wid']?>, varname = '<?php echo $_get['field'];?>', handlekey = '<?php echo @$_get['handlekey'];?>';
try { var safeVarname = parent._08_uploadData_<?php echo preg_replace('/[^\w]/', '', $_get['field']);?>; } catch(e){}
var swfu, is_ckeditor = <?php echo empty($_get['is_ckeditor']) ? 'false' : 'true';?>;
</script>
<script type="text/javascript" src="<?=$mconfigs['cmsurl']?>include/js/common.js"></script>
<script type="text/javascript" src="<?=$mconfigs['cmsurl']?>include/js/floatwin.js"></script>
<script type="text/javascript" src="<?=$mconfigs['cmsurl']?>images/common/base64.js"></script>
<?php cls_phpToJavascript::loadJQuery(); ?>
<script type="text/javascript" src="<?=$mconfigs['cmsurl']?>images/common/lightbox/jquery.lightbox-0.5.js"></script>
<script type="text/javascript">
var wid = '"' + Cookie('wid') + '"', wmid = '<?php echo @$this->wmid;?>';
var auto_compression_width = <?php echo @intval($_get['auto_compression_width']);?>;

window.onload = function () {
    try { parent.document.getElementById('loading_' + varname).style.display='none'; } catch (err) {}
    var userAuth = Cookie('<?php echo $base_inc_configs['ckpre'];?>userauth');
    if ( !userAuth )
    {
        userAuth = '';
    }
	swfu = new SWFUpload({
		// Backend Settings
		upload_url: CMS_URL + uri2MVC({'upload': 'post', 'mode': 'swf', 'type': '<?=$type?>', 'wmid': wmid, 'auto_compression_width': auto_compression_width}),
		post_params: {<?=@"'msid':Cookie('{$base_inc_configs['ckpre']}msid'),'userauth': userAuth,field: varname"?>},
		file_types : "<?=$ftypes?>",
		file_upload_limit : <?php echo $maxcount;?>,
        file_types_description : '\u5141\u8bb8\u7c7b\u578b', // ��������
		swfupload_preload_handler : preLoad,
		swfupload_load_failed_handler : loadFailed,
		file_queue_error_handler : fileQueueError,      //�ļ�ѡ��ʧ�ܺ󴥷��¼������Ͳ��ԡ���С���Եȵȣ�,
                                                        //Ĭ�Ϸ���fileQueueError(fileObject,errorcode,message)���ɸ�����Ҫ���ط���
                                                        
		file_dialog_complete_handler : fileDialogComplete,  //�ļ�ѡ�񴰿ڹر�ʱ�����¼���Ĭ�Ϸ���
        
		upload_progress_handler : uploadProgress,       //�ļ��ϴ������д����¼���Ĭ�Ϸ���
        
		upload_error_handler : uploadError,             //�ļ���������г������¼���Ĭ�Ϸ���
        
		upload_success_handler : uploadSuccess,         //�ļ�������ɣ������Ƿ��ͣ����ܷ������Ƿ��������
                                                        //Ĭ�Ϸ���uploadSuccess(fileObject,serverdata)��
                                                        //�ɸ�����Ҫ���ط���������serverdata��ʾ������upload_url���ص���Ϣ
                                                        //��Window��������Ҫ����һ���ǿ�ֵ������success��complete����ִ�У�
                                                        
		upload_complete_handler : uploadComplete,       //һ���ļ��ϴ��������ʱ�����������Ƿ��ϴ��ɹ�����ʧ�ܣ����ᴥ����
        
        file_dialog_start_handler : fileDialogStart,    //���ļ�ѡ��ѡ�񴰿�ʱ�������¼�,Ĭ�Ϸ���fileDialogStart,�ɸ�����Ҫ���ط���
        
        swfupload_loaded_handler : swfUploadLoaded,     //Flash��ť�����غõ�ʱ��ִ�еĲ���������Ϊ�ա�

		// ��ť����
		button_placeholder_id : "spanButtonPlaceholder",
        <?php if(empty($_get['is_ckeditor'])) {?>
    		button_width: 85,
    		button_height: 29, 
        <?php } else { ?>
    		button_width: 90,
    		button_height: 21,
            button_text: '&nbsp;&nbsp;&nbsp;<span class="w_local">�ϴ�ͼƬ</span>',
            button_text_style: " .w_local { color: #666666; }",
            button_image_url: CMS_ABS + "images/common/cke_fot.gif",
        <?php } ?>
		button_window_mode: SWFUpload.WINDOW_MODE.TRANSPARENT,
		button_cursor: SWFUpload.CURSOR.HAND,
        <?php if( substr($type, strlen($type) - 1) != 's' ) { // ����ǵ���ͼƬʱ��SWFֻ��ѡ��һ���ļ� ?>
        button_action : SWFUpload.BUTTON_ACTION.SELECT_FILE,
        <?php } ?>
		
		// Flash ����
		flash_url : CMS_ABS + "images/common/upload/swfupload.swf",

		custom_settings : {
			upload_target : "divFileProgressContainer",
            progressTarget: 'imgbox_' + varname,
			thumbnail_height: 80,
			thumbnail_width: 78,
			thumbnail_quality: 100,
            issingle: <?php echo (substr($type, strlen($type) - 1) !== 's' ? 1 : 0); ?>,
            delete_url: UPLOAD_URL + "delete/ufids/",
            varname: varname,
            type: '<?php echo $type;?>',           
            imgsFlag:'<?php echo empty($_get['imgsFlag']) ? '' : $_get['imgsFlag'];?>',
            imgsCom:'<?php echo empty($_get['imgsCom']) ? '' : $_get['imgsCom'];?>',            
            file_types_limit : {<?=$otype?>}
		},
		
		// �������ã������Ϊtrue��ɿ���SWF��ĵ�����Ϣ
		debug: false
	});
    
    // ������޸�״̬���Է����������������ͼ
    if ( safeVarname != undefined )
    {
        var file = safeVarname;
        for ( var i = 0; i < file.length; ++i )
        {
            _init(file[i]);
        }
    }
};
    
function _init(_file)
{
    var progress = new FileProgress(_file, swfu.customSettings.progressTarget);
    swfu.uploadSuccess(_file, null, true);     
}
</script>
<script type="text/javascript" src="<?=$mconfigs['cmsurl']?>images/common/upload/swfupload.js"></script>
<script type="text/javascript" src="<?=$mconfigs['cmsurl']?>images/common/upload/upload.js?t=<?=$timestamp;?>"></script>
<?php if(empty($_get['is_ckeditor'])) { ?>
<style type="text/css">
.mr10 {margin-right: 10px;}
a.w_local, a.w_phone, a.w_localn, a.w_cut, a.w_phonen, .img_selector, .img_selector.hover, a.progressCancel, .drag .img_selector, a.w_local span em { background: url('<?php echo $mconfigs['cms_abs'];?>images/common/upload/image_upload.png') no-repeat ; }
a.w_local, a.w_phone, a.w_localn, a.w_cut, a.w_phonen { position: relative; display: inline-block; font: 12px/29px Arial,"����"; color: rgb(102, 102, 102) ! important; text-align: right; padding-right: 10px; }
a.w_local:hover, a.w_phone:hover, a.w_cut:hover { color: rgb(0, 0, 0) ! important; text-decoration: none; }
a.w_local { width: 75px; background-position: 0 0; height: 29px; }
a.w_cut { width: 75px; background-position: 0 -250px; height: 29px; }
a.w_local:hover { background-position: 0 -30px; }
a.w_local span { position: absolute; width: 100px; line-height: 20px; display: block; background-color: rgb(255, 254, 224); border: 1px solid rgb(245, 228, 147); text-align: left; padding: 0 10px; left: 0; top: 35px; color: rgb(153, 153, 153) ! important; }
</style>
<?php } ?>
</head>
<body style="margin: 0px; padding: 0px;">
<span id="spanButtonPlaceholder"></span>
<?php if(empty($_get['is_ckeditor'])) { ?>
<a class="mr10 w_local" href="javascript:void(0)" style="position: absolute;"><?php echo ($type == 'images' ? '�����ϴ�' : '�ϴ�����');?></a>
    <?php if(in_array($type, array('images', 'files', 'medias', 'flashs'), true) && empty($noRemote)) { ?>
        <a class="mr10 w_local" href="javascript:void(0)" onclick="return uploadwin('edit_image_url', function(images){}, 0, 0, 0, 0, 'undefined', 555, 252, '<?php echo $mconfigs['cmsurl'] . _08_ROUTE_ENTRANCE . 'upload/edit_image_url/field/' . $_get['field'] . '/handlekey/' . @$_get['handlekey'] . '/parent_wid/' . @(int)$_get['parent_wid'];?>', 0);" style="position: absolute; left: 95px;">Զ�̵�ַ</a>
    <?php } ?>
<?php } ?>
<div class="fieldset intable" id="fsUploadProgress" style="display:none">
	<br style="clear: both; display:none" />

	<div id="divLoadingContent" class="content remark" style="display: none;">
		<p>SWFUpload���ڼ����У����Ժ�...</p>
		<p style="color:#999">SWFUpload is loading. Please wait a moment...</p>
	</div>
	<div id="divLongLoading" class="content remark" style="display: none;">
		<p>SWFUpload���س�ʱ�����ʧ�ܡ���ȷ����װ����ȷ�汾��Adobe Flash Player����������Flash�����</p>
		<p style="color:#999">SWFUpload is taking a long time to load or the load has failed.  Please make sure that the Flash Plugin is enabled and that a working version of the Adobe Flash Player is installed.</p>
	</div>
	<div id="divAlternateContent" class="content remark" style="display: none;">
		<p>��Ǹ��SWFUpload�޷����ء���������Ҫ��װ����������Flash Player��</p>
		<p style="color:#999">We're sorry.  SWFUpload could not load.  You may need to install or upgrade Flash Player.</p>
		<p>����<a href="http://www.adobe.com/shockwave/download/download.cgi?P1_Prod_Version=ShockwaveFlash">Adobe��վ</a>���Flash Player��</p>
		<p style="color:#999">Visit the <a href="http://www.adobe.com/shockwave/download/download.cgi?P1_Prod_Version=ShockwaveFlash">Adobe website</a> to get the Flash Player.</p>
	</div>
</div>
</body>
</html>