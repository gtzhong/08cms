<?php
(!defined('M_COM') || !defined('M_ADMIN')) && exit('No Permission');
aheader();
$updatepage = '<br/><br/><div>�Ƿ�ˢ�´���Ӧ�ø��º�Ĳ˵���&nbsp;&nbsp;<a href="' . "?isframe=1&entry=$entry&action=menusedit"  . '" target="_top">��</a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:" onclick="return floatwin(\'close_\')">��</a></div>';
if($action == 'mtypeadd'){
	if($re = $curuser->NoBackFunc('bkconfig')) cls_message::show($re);
	if(!submitcheck('bmtypeadd')){
		tabheader('��Ӳ˵�����','mtypeadd',"?entry=menus&action=mtypeadd");
		trbasic('��������','mtypenew[title]','','text');
		trbasic('����Ĭ������','mtypenew[url]','','text',array('w'=>50));
		trbasic('��������','mtypenew[vieworder]','','text');
		tabfooter('bmtypeadd');
		a_guide('mtypeadd');
	}else{
		$mtypenew['title'] = trim(strip_tags($mtypenew['title']));
		$mtypenew['url'] = trim(strip_tags($mtypenew['url']));
		$mtypenew['vieworder'] = max(0,intval($mtypenew['vieworder']));
		!$mtypenew['title'] && cls_message::show('������˵��������!');
		$db->query("INSERT INTO {$tblprefix}mtypes SET 
					mtid=".auto_insert_id('mtypes').",
					title='$mtypenew[title]', 
					url='$mtypenew[url]', 
					vieworder='$mtypenew[vieworder]'
					");
	
		adminlog('��Ӳ˵�����');
		cls_CacheFile::Update('menus');
		cls_message::show("��̨�˵�����������$updatepage", axaction(6,"?entry=menus&action=menusedit"), $updatepage ? 2000 : 1250);
	}
}elseif($action == 'menuadd' && $mtid){
	if($re = $curuser->NoBackFunc('bkconfig')) cls_message::show($re);
	$mtid = max(0,intval($mtid));
	$mtidsarr = array();
	$query = $db->query("SELECT * FROM {$tblprefix}mtypes WHERE fixed=0 ORDER BY vieworder,mtid");
	while($row = $db->fetch_array($query)) $mtidsarr[$row['mtid']] = $row['title'];
	if(!submitcheck('bmenuadd')){
		tabheader('��Ӳ˵���','menuadd',"?entry=menus&action=menuadd&mtid=$mtid");
		trbasic('��������','menunew[mtid]',makeoption($mtidsarr,$mtid),'select');
		trbasic('�˵�������','menunew[title]','','text');
		trbasic('�˵�������','menunew[url]','','text',array('w'=>50));
		trbasic('�˵�������','menunew[vieworder]','','text');
		trbasic('�˵��ע','menunew[remark]','','textarea');
		tabfooter('bmenuadd');
		a_guide('menuadd');
	}else{
		$menunew['title'] = trim(strip_tags($menunew['title']));
		$menunew['url'] = trim(strip_tags($menunew['url']));
		$menunew['vieworder'] = max(0,intval($menunew['vieworder']));
		(!$menunew['title'] || !$menunew['url']) && cls_message::show('������˵�����������!');
		!$menunew['mtid'] && cls_message::show('��ָ���˵���������!');
		$db->query("INSERT INTO {$tblprefix}menus SET 
					mnid=".auto_insert_id('menus').",
					title='$menunew[title]', 
					url='$menunew[url]', 
					remark='$menunew[remark]', 
					mtid='$menunew[mtid]', 
					vieworder='$menunew[vieworder]'
					");
	
		adminlog('��Ӻ�̨�˵���');
		cls_CacheFile::Update('menus');
		cls_message::show("��̨�˵���������$updatepage", axaction(6,"?entry=menus&action=menusedit"), $updatepage ? 2000 : 1250);
	}
}elseif($action == 'menusedit'){
	backnav('backarea','m');
	if($re = $curuser->NoBackFunc('bkconfig')) cls_message::show($re);
	if(!submitcheck('bmenusedit')){
		tabheader("��̨�˵�����&nbsp; &nbsp; >><a href=\"?entry=menus&action=mtypeadd\" onclick=\"return floatwin('open_menusedit',this)\">��ӷ���</a>",'menusedit',"?entry=menus&action=menusedit",'8');
		trcategory(array('�˵�ID','����','����','����','���','�༭','ɾ��'));
		$i = 0;
		$query = $db->query("SELECT * FROM {$tblprefix}mtypes ORDER BY vieworder");
		while($mtype = $db->fetch_array($query)){
			$mtid = $mtype['mtid'];
			$i ++;
			echo "<tr class=\"txt\">\n".
				"<td class=\"txtC w50\">[$mtid]</td>\n".
				"<td class=\"txtL\"><input type=\"text\" name=\"mtypesnew[$mtid][title]\" value=\"$mtype[title]\" size=\"25\"></td>\n".
				"<td class=\"txtC w30\"></td>\n".
				"<td class=\"txtC w40\"><input type=\"text\" name=\"mtypesnew[$mtid][vieworder]\" value=\"$mtype[vieworder]\" size=\"4\"></td>\n".
				"<td class=\"txtC w40\">".($mtype['fixed'] ? '' : "<a href=\"?entry=menus&action=menuadd&mtid=$mtid\" onclick=\"return floatwin('open_menusedit',this)\">+�˵�</a>")."</td>\n".
				"<td class=\"txtC w40\">".($mtype['fixed'] ? '-' : ("<a href=\"?entry=menus&action=mtypedetail&mtid=$mtid\" onclick=\"return floatwin('open_menusedit',this)\">����</a>"))."</td>\n".
				"<td class=\"txtC w40\">".($mtype['fixed'] ? '-' : ("<a onclick=\"return deltip()\" href=\"?entry=menus&action=mtypedel&mtid=$mtid\">ɾ��</a>"))."</td>\n".
				"</tr>";
			$query1 = $db->query("SELECT * FROM {$tblprefix}menus WHERE mtid='$mtid' AND isbk=0 ORDER BY vieworder");
			while($row = $db->fetch_array($query1)){
				$mnid = $row['mnid'];
				$i ++;
				echo "<tr class=\"txt\">\n".
					"<td class=\"txtC w50\">$mnid</td>\n".
					"<td class=\"txtL\">&nbsp; &nbsp; &nbsp; &nbsp; <input type=\"text\" name=\"menusnew[$mnid][title]\" value=\"$row[title]\" size=\"25\"></td>\n".
					"<td class=\"txtC w30\"><input class=\"checkbox\" type=\"checkbox\" name=\"menusnew[$mnid][available]\" value=\"1\"".($row['available'] ? " checked" : "")."></td>\n".
					"<td class=\"txtC w40\"><input type=\"text\" name=\"menusnew[$mnid][vieworder]\" value=\"$row[vieworder]\" size=\"4\"></td>\n".
					"<td class=\"txtC w40\">-</td>\n".
					"<td class=\"txtC w40\">".($row['fixed'] ? '-' : "<a href=\"?entry=menus&action=menudetail&mnid=$mnid\" onclick=\"return floatwin('open_menusedit',this)\">����</a>")."</td>\n".
					"<td class=\"txtC w40\">".($row['fixed'] ? '-' : "<a onclick=\"return deltip()\" href=\"?entry=menus&action=menudel&mnid=$mnid\">ɾ��</a>")."</td>\n".
					"</tr>";
			}
		}
		tabfooter('bmenusedit');
		a_guide('menusedit');
	}else{
		if(!empty($mtypesnew)){
			foreach($mtypesnew as $k => $v){
				$v['title'] = trim(strip_tags($v['title']));
				$v['vieworder'] = empty($v['vieworder']) ? 0 : max(0,intval($v['vieworder']));
				$sqlstr = "vieworder='$v[vieworder]'";
				$v['title'] && $sqlstr .= ",title='$v[title]'";
				$db->query("UPDATE {$tblprefix}mtypes SET $sqlstr WHERE mtid='$k'");
			}
		}
		if(!empty($menusnew)){
			foreach($menusnew as $k => $v){
				$v['title'] = trim(strip_tags($v['title']));
				$v['vieworder'] = max(0,intval($v['vieworder']));
				$v['available'] = empty($v['available']) ? 0 : 1;
				$sqlstr = "vieworder='$v[vieworder]',available='$v[available]'";
				$v['title'] && $sqlstr .= ",title='$v[title]'";
				$db->query("UPDATE {$tblprefix}menus SET $sqlstr WHERE mnid='$k'");
			}
		}
		adminlog('�༭�˵����б�');
		cls_CacheFile::Update('menus');
		cls_message::show("�˵���༭���$updatepage", "?entry=menus&action=menusedit");
	}
}elseif($action == 'mtypedetail' && $mtid){
	if($re = $curuser->NoBackFunc('bkconfig')) cls_message::show($re);
	if(!($mtype = $db->fetch_one("SELECT * FROM {$tblprefix}mtypes WHERE mtid='$mtid'"))) cls_message::show('��ָ����ȷ�Ĳ˵�����');
	if(!submitcheck('bmtypedetail')){
		tabheader('�༭�˵�����','mtypedetail',"?entry=menus&action=mtypedetail&mtid=$mtid");
		trbasic('��������','mtypenew[title]',$mtype['title'],'text');
		trbasic('����Ĭ������','mtypenew[url]',$mtype['url'],'text',array('w'=>50));
		trbasic('��������','mtypenew[vieworder]',$mtype['vieworder'],'text');
		tabfooter('bmtypedetail');
		a_guide('mtypedetail');
	}else{
		$mtypenew['title'] = trim(strip_tags($mtypenew['title']));
		$mtypenew['url'] = trim(strip_tags($mtypenew['url']));
		$mtypenew['vieworder'] = max(0,intval($mtypenew['vieworder']));
		!$mtypenew['title'] && cls_message::show('������˵��������!');
		$db->query("UPDATE {$tblprefix}mtypes SET 
					title='$mtypenew[title]', 
					url='$mtypenew[url]', 
					vieworder='$mtypenew[vieworder]'
					WHERE mtid='$mtid'");
	
		adminlog('�༭�˵���������');
		cls_CacheFile::Update('menus');
		cls_message::show("�˵������޸����$updatepage", axaction(6,"?entry=menus&action=menusedit"), $updatepage ? 2000 : 1250);
	}
}elseif($action == 'menudetail' && $mnid){
	if($re = $curuser->NoBackFunc('bkconfig')) cls_message::show($re);
	if(!($menu = $db->fetch_one("SELECT * FROM {$tblprefix}menus WHERE mnid='$mnid'"))) cls_message::show('��ָ����ȷ�Ĳ˵���');
	if(!submitcheck('bmenudetail')){
		tabheader('�༭�˵���','menudetail',"?entry=menus&action=menudetail&mnid=$mnid");
		$mtidsarr = array();
		$query = $db->query("SELECT * FROM {$tblprefix}mtypes WHERE fixed=0 ORDER BY vieworder");
		while($row = $db->fetch_array($query)) $mtidsarr[$row['mtid']] = $row['title'];
		trbasic('��������','menunew[mtid]',makeoption($mtidsarr,$menu['mtid']),'select');
		trbasic('�˵�������','menunew[title]',$menu['title'],'text');
		trbasic('�˵�������','menunew[url]',$menu['url'],'text',array('w'=>50));
		trbasic('�˵�������','menunew[vieworder]',$menu['vieworder'],'text');
		trbasic('�˵��ע','menunew[remark]',$menu['remark'],'textarea');
		tabfooter('bmenudetail');
		a_guide('menudetail');
	}else{
		$menunew['title'] = trim(strip_tags($menunew['title']));
		$menunew['url'] = trim(strip_tags($menunew['url']));
		$menunew['vieworder'] = max(0,intval($menunew['vieworder']));
		$menunew['mtid'] = empty($menunew['mtid']) ? 0 : max(0,intval($menunew['mtid']));
		(!$menunew['title'] || !$menunew['url']) && cls_message::show('������˵�����������!');
		!$menunew['mtid'] && cls_message::show('��ָ���˵���������!');
		$db->query("UPDATE {$tblprefix}menus SET 
					title='$menunew[title]', 
					url='$menunew[url]', 
					remark='$menunew[remark]', 
					mtid='$menunew[mtid]', 
					vieworder='$menunew[vieworder]'
					WHERE mnid='$mnid'");
		adminlog('�༭�˵�������');
		cls_CacheFile::Update('menus');
		cls_message::show("�˵����޸����$updatepage", axaction(6,"?entry=menus&action=menusedit"), $updatepage ? 2000 : 1250);
	}
}elseif($action == 'mtypedel' && $mtid){
	if($re = $curuser->NoBackFunc('bkconfig')) cls_message::show($re);
	if($db->result_one("SELECT COUNT(*) FROM {$tblprefix}menus WHERE mtid='$mtid'")){
		cls_message::show('ֻ��ɾ���յĲ˵����ࡣ', "?entry=menus&action=menusedit");
	}
	$db->query("DELETE FROM {$tblprefix}mtypes WHERE mtid='$mtid' AND fixed='0'");
	adminlog('ɾ���˵�����');
	cls_CacheFile::Update('menus');
	cls_message::show("�˵�����ɾ�����$updatepage", axaction(6,"?entry=menus&action=menusedit"), $updatepage ? 2000 : 1250);
}elseif($action == 'menudel' && $mnid){
	if($re = $curuser->NoBackFunc('bkconfig')) cls_message::show($re);
	$db->query("DELETE FROM {$tblprefix}menus WHERE mnid='$mnid' AND fixed='0'");
	adminlog('ɾ���˵���');
	cls_CacheFile::Update('menus');
	cls_message::show("�˵���ɾ�����$updatepage", axaction(6,"?entry=menus&action=menusedit"), $updatepage ? 2000 : 1250);
}
?>