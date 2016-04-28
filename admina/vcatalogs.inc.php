<?php
(!defined('M_COM') || !defined('M_ADMIN')) && exit('No Permission');
aheader();
if($re = $curuser->NoBackFunc('other')) cls_message::show($re);
$vcatalogs = cls_cache::Read('vcatalogs');
backnav('vote','vcata');
if($action == 'vcatalogsedit'){
	if(!submitcheck('bvcatalogsedit') && !submitcheck('bvcatalogadd')){
		tabheader('ͶƱ�������','vcatalogsedit','?entry=vcatalogs&action=vcatalogsedit','6');
		trcategory(array('���','��������','����','ɾ��'));
		$k = 0;
		foreach($vcatalogs as $caid => $vcatalog) {
			$k ++;
			echo "<tr class=\"txt\">\n".
				"<td class=\"txtC w40\">$k</td>\n".
				"<td class=\"txtL\"><input type=\"text\" name=\"vcatalogsnew[$caid][title]\" value=\"".mhtmlspecialchars($vcatalog['title'])."\" size=\"25\" maxlength=\"30\"></td>\n".
				"<td class=\"txtC w50\"><input type=\"text\" name=\"vcatalogsnew[$caid][vieworder]\" value=\"$vcatalog[vieworder]\" size=\"2\"></td>\n".
				"<td class=\"txtC w50\"><a onclick=\"return deltip()\" href=\"?entry=vcatalogs&action=vcatalogdelete&caid=$caid\">[ɾ��]</a></td>\n".
				"</tr>";
		}
		tabfooter('bvcatalogsedit');
		tabheader('���ͶƱ����','vcatalogadd','?entry=vcatalogs&action=vcatalogsedit');
		trbasic('��������','vcatalogadd[title]','','text');
		tabfooter('bvcatalogadd','���');
		a_guide('vcatalogsedit');
	}elseif(submitcheck('bvcatalogsedit')){
		if(!empty($vcatalogsnew)){
			foreach($vcatalogsnew as $caid => $vcatalognew){
				$vcatalognew['title'] = $vcatalognew['title'] ? $vcatalognew['title'] : $vcatalogs[$caid]['title'];
				$vcatalognew['vieworder'] = max(0,intval($vcatalognew['vieworder']));
				if(($vcatalognew['title'] != $vcatalogs[$caid]['title']) || ($vcatalognew['vieworder'] != $vcatalogs[$caid]['vieworder'])){
					$db->query("UPDATE {$tblprefix}vcatalogs SET 
								title='$vcatalognew[title]', 
								vieworder='$vcatalognew[vieworder]' 
								WHERE caid='$caid'
								");
				}
			}
			cls_CacheFile::Update('vcatalogs');
		}
		cls_message::show('����༭���', '?entry=vcatalogs&action=vcatalogsedit');
	}elseif(submitcheck('bvcatalogadd')){
		empty($vcatalogadd['title']) && cls_message::show('���ϲ���ȫ','?entry=vcatalogs&action=vcatalogsedit');
		$db->query("INSERT INTO {$tblprefix}vcatalogs SET title='$vcatalogadd[title]'");
		cls_CacheFile::Update('vcatalogs');
		cls_message::show('ͶƱ����������', '?entry=vcatalogs&action=vcatalogsedit');
	}
}elseif($action == 'vcatalogdelete' && $caid) {
	if(!submitcheck('confirm')) {
		$message = "ɾ�����ָܻ���ȷ��ɾ����ѡ��Ŀ?<br><br>";
		$message .= "ȷ������>><a href=?entry=vcatalogs&action=vcatalogdelete&caid=$caid&confirm=ok>ɾ��</a><br>";
		$message .= "��������>><a href=?entry=vcatalogs&action=vcatalogsedit>����</a>";
		cls_message::show($message);
	}
	if($db->result_one("SELECT COUNT(*) FROM {$tblprefix}votes WHERE caid='$caid'")) cls_message::show('����û���������ͶƱ����ɾ��', '?entry=vcatalogs&action=vcatalogsedit');
	$db->query("DELETE FROM {$tblprefix}vcatalogs WHERE caid='$caid'");
	cls_CacheFile::Update('vcatalogs');
	cls_message::show('����ɾ�����', '?entry=vcatalogs&action=vcatalogsedit');
}

?>
