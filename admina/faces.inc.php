<?
(!defined('M_COM') || !defined('M_ADMIN')) && exit('No Permission');
aheader();
if($re = $curuser->NoBackFunc('other')) cls_message::show($re);
$action = empty($action) ? 'facetypes' : $action;
$facedir = M_ROOT.'images/face/';
if($action == 'facetypes'){
	backnav('faces','face');
	if(!submitcheck('bfacetypes')){
		tabheader('�������','facetypes',"?entry=faces&action=facetypes",'7');
		trcategory(array('ID',"<input class=\"checkbox\" type=\"checkbox\" name=\"chkallv\" onclick=\"checkall(this.form, 'facesnew', 'chkallv')\">��Ч",array('������','txtL'),'����','����',array('����·��','txtL'),"<input class=\"checkbox\" type=\"checkbox\" name=\"chkall\" onclick=\"deltip(this,0,checkall,this.form, 'delete', 'chkall')\">ɾ?",'�༭'));
		$dirarr = face_arr($facedir);
		$query = $db->query("SELECT * FROM {$tblprefix}facetypes ORDER BY vieworder,ftid");
		while($row = $db->fetch_array($query)){
			$ftid = $row['ftid'];
			$num = $db->result_one("SELECT COUNT(*) FROM {$tblprefix}faces WHERE ftid=$ftid");
			echo "<tr class=\"txt\">".
			"<td class=\"txtC w30\">$ftid</td>\n".
			"<td class=\"txtC w50\"><input class=\"checkbox\" type=\"checkbox\" name=\"facesnew[$ftid][available]\" value=\"1\"".($row['available'] ? " checked" : "")."></td>\n".
			"<td class=\"txtL\"><input type=\"text\" size=\"25\" name=\"facesnew[$ftid][cname]\" value=\"$row[cname]\"></td>\n".
			"<td class=\"txtC\"><input type=\"text\" size=\"3\" name=\"facesnew[$ftid][vieworder]\" value=\"$row[vieworder]\"></td>\n".
			"<td class=\"txtC w30\">$num</td>\n".
			"<td class=\"txtL\">./images/face/$row[facedir]</td>\n".
			"<td class=\"txtC w40\"><input class=\"checkbox\" type=\"checkbox\"".(in_array($row['facedir'],$dirarr) ? " disabled" : " name=\"delete[$ftid]\" value=\"$ftid\" onclick=\"deltip()\"")."></td>\n".
			"<td class=\"txtC w30\"><a href=\"?entry=faces&action=facesedit&ftid=$ftid\" onclick=\"return floatwin('open_facesedit',this)\">����</a></td>\n".
			"</tr>\n";
		}
		tabfooter('bfacetypes');
		a_guide('facetypes');
	}else{
		if(!empty($delete)){
			$db->query("DELETE FROM {$tblprefix}faces WHERE ftid ".multi_str($delete),'SILENT');
			$db->query("DELETE FROM {$tblprefix}facetypes WHERE ftid ".multi_str($delete),'SILENT');
			foreach($delete as $k) unset($facesnew[$k]);
		}
		if(!empty($facesnew)){
			foreach($facesnew as $k => $v){
				$sqlstr = 'available='.(isset($v['available']) ? $v['available'] : 0);
				if($v['cname'] = trim(strip_tags($v['cname']))) $sqlstr .= ",cname='$v[cname]'";
				$sqlstr .= ',vieworder='.max(0,intval($v['vieworder']));
				$db->query("UPDATE {$tblprefix}facetypes SET $sqlstr WHERE ftid='$k'");
			}
		}
		cls_CacheFile::Update('faces');
		cls_message::show('������������ɣ�',"?entry=faces&action=facetypes");
	}
}elseif($action == 'facesedit'){
	if(empty($ftid) || !($facetype = $db->fetch_one("SELECT * FROM {$tblprefix}facetypes WHERE ftid='$ftid'"))) cls_message::show('��ѡ����ȷ�ı����飡');
	$facedir .= $facetype['facedir'];
	if(!submitcheck('bfacesedit')){
		tabheader('['.$facetype['cname'].']'.'�������','facesedit',"?entry=faces&action=facesedit&ftid=$ftid",'7');
		trcategory(array('ID',"<input class=\"checkbox\" type=\"checkbox\" name=\"chkallv\" onclick=\"checkall(this.form, 'facesnew', 'chkallv')\">��Ч",'Ԥ��',array('�ļ�����','txtL'),'����','�������',"<input class=\"checkbox\" type=\"checkbox\" name=\"chkall\" onclick=\"deltip(this,0,checkall,this.form, 'delete', 'chkall')\">ɾ?",));
		$filearr = face_files($facedir);
		$query = $db->query("SELECT * FROM {$tblprefix}faces WHERE ftid='$ftid' ORDER BY vieworder,id");
		while($row = $db->fetch_array($query)){
			$id = $row['id'];
			$thumbsrc = './images/face/'.$facetype['facedir'].'/'.$row['url'];
			echo "<tr class=\"txt\">".
			"<td class=\"txtC w30\">$id</td>\n".
			"<td class=\"txtC w50\"><input class=\"checkbox\" type=\"checkbox\" name=\"facesnew[$id][available]\" value=\"1\"".($row['available'] ? " checked" : "")."></td>\n".
			"<td class=\"txtC\"><img src=\"$thumbsrc\" border=\"0\" onload=\"if(this.height>25) {this.resized=true; this.height=25;}\" onmouseover=\"if(this.resized) this.style.cursor='pointer';\" onclick=\"if(!this.resized) {return false;} else {window.open(this.src);}\"></td>\n".
			"<td class=\"txtL\">$row[url]</td>\n".
			"<td class=\"txtC\"><input type=\"text\" size=\"3\" name=\"facesnew[$id][vieworder]\" value=\"$row[vieworder]\"></td>\n".
			"<td class=\"txtC\"><input type=\"text\" size=\"20\" name=\"facesnew[$id][ename]\" value=\"$row[ename]\"></td>\n".
			"<td class=\"txtC w40\"><input class=\"checkbox\" type=\"checkbox\"".(in_array($row['url'],$filearr) ? " disabled" : " name=\"delete[$id]\" value=\"$id\"")."></td>\n".
			"</tr>\n";
		}
		tabfooter('bfacesedit');
		a_guide('bfacesedit');
	}else{
		if(!empty($delete)){
			$db->query("DELETE FROM {$tblprefix}faces WHERE id ".multi_str($delete),'SILENT');
			foreach($delete as $k) unset($facesnew[$k]);
		}
		if(!empty($facesnew)){
			foreach($facesnew as $k => $v){
				$sqlstr = 'available='.(isset($v['available']) ? $v['available'] : 0);
				if($v['ename'] = trim(strip_tags($v['ename']))) $sqlstr .= ",ename='$v[ename]'";
				$sqlstr .= ',vieworder='.max(0,intval($v['vieworder']));
				$db->query("UPDATE {$tblprefix}faces SET $sqlstr WHERE id='$k'");
			}
		}
		cls_CacheFile::Update('faces');
		cls_message::show('����������ɣ�',"?entry=faces&action=facesedit&ftid=$ftid");
	}
}elseif($action == 'update'){
	backnav('faces','update');
	if(!submitcheck('confirm')){
		$message = '���±��齫����image/face/�µ����б������ϡ�'."<br><br>";
		$message .= "ȷ������>><a href=?entry=faces&action=update&confirm=ok><b>".'����'."</b></a><br>";
		$message .= "��������>><a href=?entry=faces&action=facetypes>����</a>";
		cls_message::show($message);
	}
	$dirarr = face_arr($facedir);
	foreach($dirarr as $k){
		if(!($ftid = $db->result_one("SELECT ftid FROM {$tblprefix}facetypes WHERE facedir='$k'"))){
			$db->query("INSERT INTO {$tblprefix}facetypes SET cname='$k',facedir='$k'",'SILENT');
			$ftid = $db->insert_id();
		}
		$filearr = face_files($facedir.$k);
		foreach($filearr as $v){
			if(!$db->result_one("SELECT COUNT(*) FROM {$tblprefix}faces WHERE ftid=$ftid AND url='$v'")){
				$db->query("INSERT INTO {$tblprefix}faces SET ftid=$ftid,ename='[:".($ftid.'_'.substr($v,0,strrpos($v,'.'))).":]',url='$v'",'SILENT');
			}
		}
	}
	cls_message::show('���������ɣ�',"?entry=faces&action=facetypes");
}
function face_arr($absdir){
	$rets = array();
	if(is_dir($absdir)){
		if($tempdir = opendir($absdir)){
			while(($tempfile = readdir($tempdir)) !== false){
				if(!in_array($tempfile,array('.','..')) && filetype($absdir."/".$tempfile) == 'dir') $rets[] = $tempfile;
			}
			closedir($tempdir);
		}
	}
	return $rets;
}
function face_files($absdir){
	$rets = array();
	if(is_dir($absdir)){
		if($tempdir = opendir($absdir)){
			while(($tempfile = readdir($tempdir)) !== false){
				if((filetype($absdir."/".$tempfile) == 'file') && in_array(strtolower(mextension($tempfile)),array('gif','jpg','png',))) $rets[] = $tempfile;
			}
			closedir($tempdir);
		}
	}
	return $rets;
}

?>
