<?
include_once dirname(dirname(__FILE__)).'/include/general.inc.php';
include_once M_ROOT."include/adminm.fun.php";
if(!$curuser->isadmin()) cls_message::show('��û������Ȩ�ޣ�');
$source = cls_cache::cacRead('mysource');
if(!submitcheck('bsubmit')){
	_header(' ��������Դ����');
	tabheader('ÿ�б���һ����Դ','edit_mysource');
	trbasic('��Դ����','mysource',implode("\r\n",$source),'textarea',array('w'=>'300','h'=>300));
	tabfooter('bsubmit','����');
	_footer();
}else{
	_header();
	empty($mysource) && cls_message::show('��Դ����Ϊ��',axaction(6,M_REFERER));
	$mysource = array_unique(explode("\r\n",$mysource));
	$mysource = array_diff($mysource,array(null,'null','',' '));
	cls_CacheFile::cacSave($mysource,'mysource');
	cls_message::show('����ɹ�',axaction(2,M_REFERER));
}
?>

