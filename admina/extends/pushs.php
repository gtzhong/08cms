<?PHP
/*
** ������Ϣ�������ڽű�����������λ���õ���չ�ű�
** 
*/
(!defined('M_COM') || !defined('M_ADMIN')) && exit('No Permission');
if(!($pusharea = cls_PushArea::Config(@$paid))) exit('��ָ������λ');
if(!empty($pusharea['script_admin'])){
	_08_FilesystemFile::filterFileParam($pusharea['script_admin']);
	include dirname(__FILE__).DS."{$pusharea['script_admin']}";
}else{
	include dirname(__FILE__).DS."pushs_com.php";
}
