<?php
(!defined('M_COM') || !defined('M_ADMIN')) && exit('No Permission');
aheader();
if($re = $curuser->NoBackFunc('mcconfig')) cls_message::show($re);
$permissions = cls_cache::Read('permissions');
if($action == 'mmtypeadd'){
	if(!submitcheck('bmmtypeadd')){
		tabheader('��ӻ�Ա���Ĳ˵�����','mmtypeadd',"?entry=mmenus&action=mmtypeadd");
		trspecial('�˵�Сͼ��',specialarr(array('type' => 'image','varname' => 'mmtypenew[menuimage]')));
		trbasic('��������','mmtypenew[title]','','text');
		trbasic('��������','mmtypenew[vieworder]','','text');
		tabfooter('bmmtypeadd');
		a_guide('mmtypeadd');
	}else{
		$mmtypenew['title'] = trim(strip_tags($mmtypenew['title']));
		$mmtypenew['vieworder'] = max(0,intval($mmtypenew['vieworder']));
		!$mmtypenew['title'] && cls_message::show('�������Ա���Ĳ˵��������!');
		$db->query("INSERT INTO {$tblprefix}mmtypes SET 
					mtid=".auto_insert_id('mmtypes').",
					menuimage='$mmtypenew[menuimage]',
					title='$mmtypenew[title]', 
					vieworder='$mmtypenew[vieworder]'
					");
		$mtid = $db->insert_id();
		if(!empty($mmtypenew['menuimage'])){
			$source = str_replace($cms_abs,'./',$mmtypenew['menuimage']);
			$tofile = './adminm/images/bigmenu'.$mtid.substr($mmtypenew['menuimage'],(strlen($mmtypenew['menuimage'])-4));
			cls_upload::image_resize($source,20,20,$tofile,1);
		}	
		adminlog('��Ա���Ĳ˵��������');
		cls_CacheFile::Update('mmenus');
		cls_message::show('��Ա���Ĳ˵�����������', "?entry=mmenus&action=mmenusedit");
	}
}elseif($action == 'mmenuadd' && $mtid){
	$mtid = max(0,intval($mtid));
	$mtidsarr = array();
	$query = $db->query("SELECT * FROM {$tblprefix}mmtypes ORDER BY vieworder,mtid");
	while($row = $db->fetch_array($query)){
		$mtidsarr[$row['mtid']] = $row['title'];
	}
	if(!submitcheck('bmmenuadd')){
		tabheader('��ӻ�Ա���Ĳ˵���Ŀ','mmenuadd',"?entry=mmenus&action=mmenuadd&mtid=$mtid");
		trbasic('��������','mmenunew[mtid]',makeoption($mtidsarr,$mtid),'select');
		trbasic('�˵���Ŀ����','mmenunew[title]','','text');
		trbasic('�˵���Ŀ����','mmenunew[url]','','text',array('w'=>50));
		//trbasic('�˵���ʾȨ������','mmenunew[pmid]',makeoption(pmidsarr('menu')),'select',array('guide' => 'ϵͳ����=>��������=>Ȩ�޷���=>�˵�'));
		setPermBar('�˵���ʾȨ������', 'mmenunew[pmid]', '', $source='menu', $soext='open', $guide='');
        trbasic('�˵���Ŀ����','mmenunew[vieworder]','','text');
		trbasic('�´��ڴ�����','mmenunew[newwin]',0,'radio');
		trbasic('���Ӽ���onclick�ִ�','mmenunew[onclick]','','text',array('w'=>50));
		trbasic('�˵���ע','mmenunew[remark]','','textarea');
		tabfooter('bmmenuadd');
		a_guide('mmenuadd');
	}else{
		$mmenunew['title'] = trim(strip_tags($mmenunew['title']));
		$mmenunew['url'] = trim(strip_tags($mmenunew['url']));
		$mmenunew['onclick'] = trim($mmenunew['onclick']);
		$mmenunew['vieworder'] = max(0,intval($mmenunew['vieworder']));
		(!$mmenunew['title'] || !$mmenunew['url']) && cls_message::show('������˵�����������!',axaction(1,M_REFERER));
		!$mmenunew['mtid'] && cls_message::show('��ָ����Ա���Ĳ˵���������!');
		$db->query("INSERT INTO {$tblprefix}mmenus SET 
					mnid=".auto_insert_id('mmenus').",
					title='$mmenunew[title]', 
					url='$mmenunew[url]', 
					mtid='$mmenunew[mtid]', 
					pmid='$mmenunew[pmid]', 
					newwin='$mmenunew[newwin]', 
					onclick='$mmenunew[onclick]', 
					vieworder='$mmenunew[vieworder]',
					remark='$mmenunew[remark]'
					");
	
		adminlog('��ӻ�Ա���Ĳ˵���Ŀ');
		cls_CacheFile::Update('mmenus');
		cls_message::show('��Ա���Ĳ˵���Ŀ������', axaction(6,"?entry=mmenus&action=mmenusedit"));
	}
}elseif($action == 'mmenusedit'){
	backnav('mcenter','c');
	
	if(!submitcheck('bmmenusedit')){
		tabheader('��Ա���Ĳ˵�'."&nbsp; &nbsp; >><a href=\"?entry=mmenus&action=mmtypeadd\">".'��Ӳ˵�����'.'</a>','mmenusedit',"?entry=mmenus&action=mmenusedit",'9');
		trcategory(array('�˵�ID',array('����','txtL'),'����','����',array('����','txtL'),'���','�༭','ɾ��'));
		$query = $db->query("SELECT * FROM {$tblprefix}mmtypes ORDER BY vieworder,mtid");
		$i = 0;
		while($mmtype = $db->fetch_array($query)){
			$mtid = $mmtype['mtid'];
			$i ++;
			echo "<tr class=\"txt\">\n".
				"<td class=\"txtC w50\">[$mtid]</td>\n".
				"<td class=\"txtL\"><input type=\"text\" name=\"mmtypesnew[$mtid][title]\" value=\"$mmtype[title]\" size=\"25\"></td>\n".
				"<td class=\"txtC w30\"></td>\n".
				"<td class=\"txtC w40\"><input type=\"text\" name=\"mmtypesnew[$mtid][vieworder]\" value=\"$mmtype[vieworder]\" size=\"4\"></td>\n".
				"<td class=\"txtL\"></td>\n".
				"<td class=\"txtC w40\"><a href=\"?entry=mmenus&action=mmenuadd&mtid=$mtid\" onclick=\"return floatwin('open_mmenusedit',this)\">+�˵�</a></td>\n".
				"<td class=\"txtC w40\">-</td>\n".
				"<td class=\"txtC w40\"><a onclick=\"return deltip()\" href=\"?entry=mmenus&action=mmtypedel&mtid=$mtid\">ɾ��</a></td>\n".
				"</tr>";
			$query1 = $db->query("SELECT * FROM {$tblprefix}mmenus WHERE mtid='$mtid' ORDER BY vieworder,mnid");
			while($row = $db->fetch_array($query1)){
				$mnid = $row['mnid'];
				$i ++;
				echo "<tr class=\"txt\">\n".
					"<td class=\"txtC w50\">$mnid</td>\n".
					"<td class=\"txtL\">&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; <input type=\"text\" name=\"mmenusnew[$mnid][title]\" value=\"$row[title]\" size=\"25\"></td>\n".
					"<td class=\"txtC w30\"><input class=\"checkbox\" type=\"checkbox\" name=\"mmenusnew[$mnid][available]\" value=\"1\"".($row['available'] ? " checked" : "")."></td>\n".
					"<td class=\"txtC w40\"><input type=\"text\" name=\"mmenusnew[$mnid][vieworder]\" value=\"$row[vieworder]\" size=\"4\"></td>\n".
					"<td class=\"txtL\"><input type=\"text\" name=\"mmenusnew[$mnid][description]\" value=\"$row[description]\" size=\"40\"></td>\n".
					"<td class=\"txtC w40\">-</td>\n".
					"<td class=\"txtC w40\"><a href=\"?entry=mmenus&action=mmenudetail&mnid=$mnid\" onclick=\"return floatwin('open_mmenusedit',this)\">����</a></td>\n".
					"<td class=\"txtC w40\"><a onclick=\"return deltip()\" href=\"?entry=mmenus&action=mmenudel&mnid=$mnid\">ɾ��</a></td>\n".
					"</tr>";
			}
		}
		tabfooter('bmmenusedit');
		a_guide('mmenusedit');
	}else{
		if(!empty($mmtypesnew)){
			foreach($mmtypesnew as $k => $v){
				$v['title'] = trim(strip_tags($v['title']));
				$v['vieworder'] = empty($v['vieworder']) ? 0 : max(0,intval($v['vieworder']));
				$sqlstr = "vieworder='$v[vieworder]'";
				$v['title'] && $sqlstr .= ",title='$v[title]'";
				$db->query("UPDATE {$tblprefix}mmtypes SET $sqlstr WHERE mtid='$k'");
			}
		}
		if(!empty($mmenusnew)){
			foreach($mmenusnew as $k => $v){
				$v['title'] = trim(strip_tags($v['title']));
				$v['description'] = trim(strip_tags($v['description']));
				$v['vieworder'] = max(0,intval($v['vieworder']));
				$v['available'] = empty($v['available']) ? 0 : 1;
				$sqlstr = "vieworder='$v[vieworder]',available='$v[available]'";
				$v['title'] && $sqlstr .= ",title='$v[title]'";
				isset($v['description']) && $sqlstr .= ",description='$v[description]'";
				$db->query("UPDATE {$tblprefix}mmenus SET $sqlstr WHERE mnid='$k'");
			}
		}
		adminlog('�༭��Ա���Ĳ˵��б�');
		cls_CacheFile::Update('mmenus');
		cls_message::show('��Ա���Ĳ˵��༭���', "?entry=mmenus&action=mmenusedit");
	}
}elseif($action == 'mmenudetail' && $mnid){
	if(!($mmenu = $db->fetch_one("SELECT * FROM {$tblprefix}mmenus WHERE mnid='$mnid'"))) cls_message::show('��ָ����ȷ�Ļ�Ա���Ĳ˵���Ŀ');
	if(!submitcheck('bmmenudetail')){
		tabheader('�༭��Ա���Ĳ˵���Ŀ����','mmenudetail',"?entry=mmenus&action=mmenudetail&mnid=$mnid");
		$mtidsarr = array();
		$query = $db->query("SELECT * FROM {$tblprefix}mmtypes ORDER BY vieworder,mtid");
		while($row = $db->fetch_array($query)){
			$mtidsarr[$row['mtid']] = $row['title'];
		}
		trbasic('��������','mmenunew[mtid]',makeoption($mtidsarr,$mmenu['mtid']),'select');
		trbasic('�˵���Ŀ����','mmenunew[title]',$mmenu['title'],'text');
		trbasic('�˵���Ŀ����','mmenunew[url]',$mmenu['url'],'text',array('w'=>50));
		setPermBar('�˵���ʾȨ������', 'mmenunew[pmid]', @$mmenu['pmid'], 'menu', 'open', '');
        trbasic('�˵���Ŀ����','mmenunew[vieworder]',$mmenu['vieworder'],'text');
		trbasic('�´��ڴ�����','mmenunew[newwin]',$mmenu['newwin'],'radio');
		trbasic('���Ӽ���onclick�ִ�','mmenunew[onclick]',$mmenu['onclick'],'text',array('w'=>50));
		trbasic('�˵���ע','mmenunew[remark]',$mmenu['remark'],'textarea');
		tabfooter('bmmenudetail');
		a_guide('mmenudetail');
	}else{
		$mmenunew['title'] = trim(strip_tags($mmenunew['title']));
		$mmenunew['url'] = trim(strip_tags($mmenunew['url']));
		$mmenunew['onclick'] = trim($mmenunew['onclick']);
		$mmenunew['vieworder'] = max(0,intval($mmenunew['vieworder']));
		$mmenunew['mtid'] = empty($mmenunew['mtid']) ? 0 : max(0,intval($mmenunew['mtid']));
		(!$mmenunew['title'] || !$mmenunew['url']) && cls_message::show('�������Ա���Ĳ˵�����������!');
		!$mmenunew['mtid'] && cls_message::show('��ָ����Ա���Ĳ˵���������!');
		$db->query("UPDATE {$tblprefix}mmenus SET 
					title='$mmenunew[title]', 
					url='$mmenunew[url]', 
					mtid='$mmenunew[mtid]', 
					pmid='$mmenunew[pmid]', 
					newwin='$mmenunew[newwin]', 
					onclick='$mmenunew[onclick]', 
					vieworder='$mmenunew[vieworder]',
					remark='$mmenunew[remark]'
					WHERE mnid='$mnid'");
		adminlog('�༭��Ա���Ĳ˵���Ŀ����');
		cls_CacheFile::Update('mmenus');
		cls_message::show('�˵���Ŀ�޸����', axaction(6,"?entry=mmenus&action=mmenusedit"));
	}
}elseif($action == 'mmtypedel' && $mtid){
	if($db->result_one("SELECT COUNT(*) FROM {$tblprefix}mmenus WHERE mtid='$mtid'")){
		cls_message::show('�˵�����û��������Ĳ˵���Ŀ����ɾ��', "?entry=mmenus&action=mmenusedit");
	}
	$db->query("DELETE FROM {$tblprefix}mmtypes WHERE mtid='$mtid'");
	adminlog('ɾ����Ա���Ĳ˵�����');
	cls_CacheFile::Update('mmenus');
	cls_message::show('�˵�����ɾ�����', "?entry=mmenus&action=mmenusedit");
}elseif($action == 'mmenudel' && $mnid){
	$db->query("DELETE FROM {$tblprefix}mmenus WHERE mnid='$mnid'");
    $file = _08_FilesystemFile::getInstance();
	$file->delFile(M_ROOT."dynamic/mguides/mguide_$mnid.php");
	adminlog('ɾ����Ա���Ĳ˵���Ŀ');
	cls_CacheFile::Update('mmenus');
	cls_message::show('�˵���Ŀɾ�����', "?entry=mmenus&action=mmenusedit");
}
?>