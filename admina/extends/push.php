<?PHP
/*
** �������������༭����ڽű�����������λ���õ���չ�ű�
** 
*/
(!defined('M_COM') || !defined('M_ADMIN')) && exit('No Permission');
if(!($pusharea = cls_PushArea::Config(@$paid))) exit('��ָ������λ');
if(!empty($pusharea['script_detail'])){
	_08_FilesystemFile::filterFileParam($pusharea['script_detail']);
	include dirname(__FILE__).DS."{$pusharea['script_detail']}";
}else{
	include dirname(__FILE__).DS."push_com.php";
}
