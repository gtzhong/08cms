<?php
(!defined('M_COM') || !defined('M_ADMIN')) && exit('No Permission');
if(!submitcheck('bmtagadd') && !submitcheck('bmtagsdetail')){
	$template = cls_tpl::load(@$mtag['template'],0);
	trbasic('*ģ���ļ�����','mtagnew[template]',empty($mtag['template']) ? '' : ((empty($iscopy) ? '' : 'cp_').$mtag['template']),'text',array('validate' => makesubmitstr('mtagnew[template]',1,0,3,30,'text','/^[a-zA-Z]{1}[a-zA-Z0-9-_.]+(\.html|\.htm)$/'),'guide' => "�ļ�����ֻ���������ĸ�����֡��»���(-_)����(.)���ַ�,������ĸ��ͷ����htm��htmlΪ��չ��"));               
    $older = empty($iscopy)?(empty($mtag['template'])?'':$mtag['template']):'';
	$ajaxURL = $cms_abs . _08_Http_Request::uri2MVC("ajax=check_mtagtemplate&val=%1&older={$older}/");
	echo _08_HTML::AjaxCheckInput('mtagnew[template]', $ajaxURL);
    templatebox('ҳ��ģ��','templatenew',$template,30,110);
   	
	tabfooter();
}else{
	$mtagnew['template'] = trim($mtagnew['template']);
	if($re = _08_FilesystemFile::CheckFileName($mtagnew['template'])) cls_message::show($re,M_REFERER);
	cls_Array::array_stripslashes($templatenew);
	// �����Ƿ�����չģ��,���ﶼ��cls_tpl::rel_pathĬ�϶�λ����ǰģ��Ŀ¼; ���url��isbase=1��λ������ģ��
	if(@!str2file($templatenew,cls_tpl::rel_path($mtagnew['template'],'get'))) cls_message::show('ģ�屣�治�ɹ���',M_REFERER);
}
