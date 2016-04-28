<?php
(!defined('M_COM') || !defined('M_ADMIN')) && exit('No Permission');
aheader();
if($re = $curuser->NoBackFunc('affix')) cls_message::show($re);
$domains = cls_cache::Read('domains');
if(empty($action)) $action = 'domainsedit';
if($action == 'domainsedit'){
	backnav('otherset','domain');
	if(!submitcheck('bdomainsedit')){
		tabheader('��������'."&nbsp; &nbsp; >><a href=\"?entry=$entry&action=domainadd\" onclick=\"return floatwin('open_domains',this)\">".'�������'.'</a>',$actionid.'arcsedit',"?entry=$entry&action=$action");
		trcategory(array("<input class=\"checkbox\" type=\"checkbox\" name=\"chkall\" onclick=\"deltip(this,$no_deepmode,checkall,this.form, 'delete', 'chkall')\">ɾ?",'ϵͳ·��|L','ָ������|L','�Ƿ�����','����'));
		$query = $db->query("SELECT * FROM {$tblprefix}domains ORDER BY vieworder,id");
		while($item = $db->fetch_array($query)){
			$id = $item['id'];
			echo "<tr class=\"txt\">".
			"<td class=\"txtC w40\"><input class=\"checkbox\" type=\"checkbox\" name=\"delete[$id]\" value=\"$id\" onclick=\"deltip(this,$no_deepmode)\"></td>\n".
			"<td class=\"txtL\"><input type=\"text\" size=\"40\" name=\"domainsnew[$id][folder]\" value=\"$item[folder]\"></td>\n".
			"<td class=\"txtL\"><input type=\"text\" size=\"40\" name=\"domainsnew[$id][domain]\" value=\"$item[domain]\"></td>\n".
			"<td class=\"txtC w60\"><input class=\"checkbox\" type=\"checkbox\" name=\"domainsnew[$id][isreg]\" value=\"1\" ".(empty($item['isreg']) ? '' : 'checked')."></td>\n".
			"<td class=\"txtC w40\"><input type=\"text\" size=\"4\" name=\"domainsnew[$id][vieworder]\" value=\"$item[vieworder]\"></td>\n".
			"</tr>\n";
		}
		tabfooter('bdomainsedit');
		a_guide('domainsedit');
	}else{
		if(!empty($delete) && deep_allow($no_deepmode)){
			foreach($delete as $k){
				$db->query("DELETE FROM {$tblprefix}domains WHERE id='$k'");
				unset($domainsnew[$k]);
			}
		}
		if(!empty($domainsnew)){
			foreach($domainsnew as $k => $v){
				$v['folder'] = trim(strip_tags($v['folder']));
				$v['domain'] = trim(strip_tags($v['domain']));
				$v['vieworder'] = max(0,intval($v['vieworder']));
				$v['isreg'] = empty($v['isreg']) ? 0 : 1;
				if(!$v['folder'] || !$v['domain']) continue;
				$db->query("UPDATE {$tblprefix}domains SET domain='$v[domain]',folder='$v[folder]',isreg='$v[isreg]',vieworder='$v[vieworder]' WHERE id='$k'");
			}
		}
		adminlog('�༭�����б�');
		cls_CacheFile::Update('domains'); 
		cls_message::show('�����༭��ɣ�', "?entry=$entry&action=$action");
	}

}elseif($action == 'domainadd'){
	if(!submitcheck('bdomainadd')){
		tabheader('�������','domainadd',"?entry=$entry&action=$action");
		trbasic('ϵͳ·��','domainnew[folder]','','text',array('w'=>50));
		trbasic('ָ������','domainnew[domain]','','text',array('w'=>50));
		trbasic('�Ƿ�����','domainnew[isreg]',0,'radio');
		tabfooter('bdomainadd');
		a_guide('domainsedit');
	}else{
		$domainnew['folder'] = trim(strip_tags($domainnew['folder']));
		$domainnew['domain'] = trim(strip_tags($domainnew['domain']));
		//if(!preg_match("/^(?:[A-Z0-9-]+\\.)?[A-Z0-9-]+\\.[A-Z]{2,4}$/i",$domainnew['domain'])) a|message('domainillegal',"?entry=$entry&action=domainsedit");
		//if(in_array($domainnew['domain'],array_keys($domains))) ame|ssage('domainrepeat',"?entry=$entry&action=domainsedit");
		if(!$domainnew['folder'] || !$domainnew['domain']) cls_message::show('���ϲ���ȫ',M_REFERER);
		$db->query("INSERT INTO {$tblprefix}domains SET 
					domain='$domainnew[domain]', 
					folder='$domainnew[folder]',
					isreg='$domainnew[isreg]'
					");
		adminlog('�������');
		cls_CacheFile::Update('domains');
		cls_message::show('������ӳɹ���', axaction(6,"?entry=$entry&action=domainsedit"));
	}
}

?>