<?
include_once dirname(dirname(__FILE__)).'/include/general.inc.php';
include_once M_ROOT."include/adminm.fun.php";
if(!$curuser->isadmin()) cls_message::show('��û������Ȩ�ޣ�');
$author = cls_cache::cacRead('myauthor');
if(!submitcheck('bsubmit')){
	_header(' ���������߹���');
	tabheader('�����������ð�Ƕ���","�ֿ�','edit_mysource');
	trbasic('����','myauthor',implode(",",$author),'textarea',array('w'=>'300','h'=>300));
	tabfooter('bsubmit','����');
	_footer();
}else{
	_header();
	empty($myauthor) && cls_message::show('��Դ����Ϊ��',axaction(6,M_REFERER));
	$myauthor = str_replace("��",',',$myauthor);
	$myauthor = array_unique(explode(",",$myauthor));
	$myauthor = array_diff($myauthor,array(null,'null','',' '));	
	cls_CacheFile::cacSave($myauthor,'myauthor');
	cls_message::show('����ɹ�',axaction(2,M_REFERER));
}
?>

