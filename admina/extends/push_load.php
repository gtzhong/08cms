<?PHP
/*
** ���ͼ��ع���
** ��ͬ���͵�����λ�ļ�����ڽű�
** 
*/
(!defined('M_COM') || !defined('M_ADMIN')) && exit('No Permission');
if(!($pusharea = cls_PushArea::Config(@$paid))) exit('��ָ������λ');
if(!empty($pusharea['script_load'])){
	_08_FilesystemFile::filterFileParam($pusharea['script_load']);
	include dirname(__FILE__).DS."{$pusharea['script_load']}";
}else{
	if(empty($pusharea['sourcetype'])) exit('��Դ����δ֪');
	_08_FilesystemFile::filterFileParam($pusharea['sourcetype']);
	include dirname(__FILE__).DS."push_load_{$pusharea['sourcetype']}.php";
}
