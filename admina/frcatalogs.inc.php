<?php
(!defined('M_COM') || !defined('M_ADMIN')) && exit('No Permission');
aheader();
if($re = $curuser->NoBackFunc('fragment')) cls_message::show($re);
$frcatalogs = fetch_arr();
empty($action) && $action = 'frcatalogsedit';
backnav('fragment','catalog');
if($action == 'frcatalogsedit'){
	if(!submitcheck('bfrcatalogsedit') && !submitcheck('bfrcatalogadd')){
		tabheader('��Ƭ�������','frcatalogsedit','?entry=frcatalogs&action=frcatalogsedit','7');
		trcategory(array('ID',array('��������','txtL'),'����','ɾ��'));
		foreach($frcatalogs as $k => $v){
			echo "<tr class=\"txt\">\n".
				"<td class=\"txtC w30\">$k</td>\n".
				"<td class=\"txtL\"><input type=\"text\" name=\"frcatalogsnew[$k][title]\" value=\"".mhtmlspecialchars($v['title'])."\" size=\"25\" maxlength=\"30\"></td>\n".
				"<td class=\"txtC w50\"><input type=\"text\" name=\"frcatalogsnew[$k][vieworder]\" value=\"$v[vieworder]\" size=\"2\"></td>\n".
				"<td class=\"txtC w30\"><a onclick=\"return deltip()\" href=\"?entry=frcatalogs&action=frcatalogdel&frcaid=$k\">ɾ��</a></td>\n".
				"</tr>";
		}
		tabfooter('bfrcatalogsedit');
		tabheader('�����Ƭ����','frcatalogadd','?entry=frcatalogs&action=frcatalogsedit');
		trbasic('��������','frcatalognew[title]','','text');
		tabfooter('bfrcatalogadd');
	}elseif(submitcheck('bfrcatalogsedit')){
		if(!empty($frcatalogsnew)){
			foreach($frcatalogsnew as $k => $v){
				$v['title'] = $v['title'] ? $v['title'] : $frcatalogs[$k]['title'];
				$v['vieworder'] = max(0,intval($v['vieworder']));
				$db->query("UPDATE {$tblprefix}frcatalogs SET 
							title='$v[title]', 
							vieworder='$v[vieworder]' 
							WHERE frcaid='$k'
							");
			}
			cls_CacheFile::Update('frcatalogs');
		}
		adminlog('�༭��Ƭ��������б�');
		cls_message::show('����༭���', '?entry=frcatalogs&action=frcatalogsedit');
	}elseif(submitcheck('bfrcatalogadd')){
		$frcatalognew['title'] = trim(strip_tags($frcatalognew['title']));
		if(!$frcatalognew['title']) cls_message::show('�������ϲ���ȫ',M_REFERER);
		$db->query("INSERT INTO {$tblprefix}frcatalogs SET 
				   	frcaid=".auto_insert_id('frcatalogs').",
					title='$frcatalognew[title]'
					");
		cls_CacheFile::Update('frcatalogs');
		adminlog('�����Ƭ����');
		cls_message::show('��Ƭ����������', '?entry=frcatalogs&action=frcatalogsedit');
	}
}elseif($action == 'frcatalogdel' && $frcaid) {
	if(!($frcatalog = $frcatalogs[$frcaid])) cls_message::show('��ָ����ȷ����Ƭ���ࡣ');
	if(!submitcheck('confirm')){
		$message = "ɾ�����ָܻ���ȷ��ɾ����ѡ��Ŀ?<br><br>";
		$message .= "ȷ������>><a href=?entry=frcatalogs&action=frcatalogdel&frcaid=$frcaid&confirm=ok>ɾ��</a><br>";
		$message .= "��������>><a href=?entry=frcatalogs&action=frcatalogsedit>����</a>";
		cls_message::show($message);
	}
	$db->query("UPDATE {$tblprefix}fragments SET frcaid=0 WHERE frcaid='$frcaid'");
	$db->query("DELETE FROM {$tblprefix}frcatalogs WHERE frcaid='$frcaid'");
	cls_CacheFile::Update('frcatalogs');
	adminlog('ɾ����Ƭ����');
	cls_message::show('����ɾ�����', '?entry=frcatalogs&action=frcatalogsedit');
}else cls_message::show('������ļ�����');

function fetch_arr(){
	global $db,$tblprefix;
	$rets = array();
	$query = $db->query("SELECT * FROM {$tblprefix}frcatalogs ORDER BY vieworder,frcaid");
	while($r = $db->fetch_array($query)){
		$rets[$r['frcaid']] = $r;
	}
	return $rets;
}

?>
