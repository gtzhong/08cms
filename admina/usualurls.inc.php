<?php
(!defined('M_COM') || !defined('M_ADMIN')) && exit('No Permission');
aheader();
$permissions = cls_cache::Read('permissions');
$ismc = empty($ismc) ? 0 : 1;
$mc_suffix = !$ismc ? '' : '&ismc=1';
$mc_str = $ismc ? '��Ա����': '�����̨';
$bk_func_item = $ismc ? 'mcconfig' : 'bkconfig';
if($action == 'usualurladd'){
	if($re = $curuser->NoBackFunc($bk_func_item)) cls_message::show($re);
	if(!submitcheck('busualurladd')){
		tabheader('���'.$mc_str.'��������','usualurladd',"?entry=usualurls&action=usualurladd$mc_suffix");
		trbasic('�������ӱ���','usualurlnew[title]','','text');
		trbasic('��������URL','usualurlnew[url]','','text',array('w'=>50));
		trbasic('������������','usualurlnew[vieworder]','','text');
		trspecial('����ͼƬ',specialarr(array('type' => 'image','varname' => 'usualurlnew[logo]',)));
		trbasic('�´��ڴ�����','usualurlnew[newwin]',0,'radio');
		$ismc && trbasic('���Ӽ���onclick�ִ�','usualurlnew[onclick]','','text',array('w'=>50));
		setPermBar('������ʾȨ������', 'usualurlnew[pmid]', '', 'menu', 'open', '');
        tabfooter('busualurladd');
		a_guide('usualurladd');
	}else{
		$usualurlnew['title'] = trim(strip_tags($usualurlnew['title']));
		$usualurlnew['url'] = trim(strip_tags($usualurlnew['url']));
		$usualurlnew['vieworder'] = max(0,intval($usualurlnew['vieworder']));
		if(!$usualurlnew['title'] || !$usualurlnew['url']) cls_message::show('�����볣�����ӱ���������!');
		$c_upload = cls_upload::OneInstance();	
		$usualurlnew['logo'] = upload_s($usualurlnew['logo'],'','image');
		$usualurlnew['onclick'] = empty($usualurlnew['onclick']) ? '' : trim($usualurlnew['onclick']);
		$db->query("INSERT INTO {$tblprefix}usualurls SET 
					uid=".auto_insert_id('usualurls').",
					title='$usualurlnew[title]', 
					url='$usualurlnew[url]', 
					logo='$usualurlnew[logo]', 
					pmid='$usualurlnew[pmid]', 
					newwin='$usualurlnew[newwin]',
					onclick='$usualurlnew[onclick]',
					vieworder='$usualurlnew[vieworder]',
					ismc='$ismc'
					");
		adminlog('��ӳ�������');
		$c_upload->closure(1,$db->insert_id(),'usualurls');
		cls_CacheFile::Update('usualurls');
		cls_message::show('��������������',axaction(6,"?entry=usualurls&action=usualurlsedit$mc_suffix"));
	}
}elseif($action == 'usualurlsedit'){
	backnav($ismc ? 'mcenter' : 'backarea',$ismc ? 'musual' : 'ausual');
	if($re = $curuser->NoBackFunc($bk_func_item)) cls_message::show($re);
	$usualurls = array();
	$query = $db->query("SELECT * FROM {$tblprefix}usualurls WHERE ismc='$ismc' ORDER BY vieworder");
	while($row = $db->fetch_array($query)) $usualurls[$row['uid']] = $row;
	if(!submitcheck('busualurlsedit')){
		tabheader($mc_str."��������&nbsp; &nbsp; >><a href=\"?entry=usualurls&action=usualurladd$mc_suffix\" onclick=\"return floatwin('open_usualurlsedit',this)\">���</a>",'usualurlsedit',"?entry=usualurls&action=usualurlsedit$mc_suffix",'8');
		trcategory(array('ID','����','URL','����','����',"<input class=\"checkbox\" type=\"checkbox\" name=\"chkall\" onclick=\"deltip(this,0,checkall,this.form, 'delete', 'chkall')\">ɾ?",'�༭'));
		foreach($usualurls as $k => $v){
			echo "<tr class=\"txt\">\n".
				"<td class=\"txtC w40\">$k</td>\n".
				"<td class=\"txtL\"><input type=\"text\" name=\"usualurlsnew[$k][title]\" value=\"$v[title]\" size=\"30\"></td>\n".
				"<td class=\"txtL\"><input type=\"text\" name=\"usualurlsnew[$k][url]\" value=\"$v[url]\" size=\"50\"></td>\n".
				"<td class=\"txtC w30\"><input class=\"checkbox\" type=\"checkbox\" name=\"usualurlsnew[$k][available]\" value=\"1\"".($v['available'] ? " checked" : "")."></td>\n".
				"<td class=\"txtC w40\"><input type=\"text\" name=\"usualurlsnew[$k][vieworder]\" value=\"$v[vieworder]\" size=\"4\"></td>\n".
				"<td class=\"txtC w40\"><input class=\"checkbox\" type=\"checkbox\" name=\"delete[$k]\" value=\"$k\" onclick=\"deltip()\"></td>\n".
				"<td class=\"txtC w40\"><a href=\"?entry=usualurls&action=usualurldetail&uid=$k$mc_suffix\" onclick=\"return floatwin('open_usualurlsedit',this)\">����</a></td>\n".
				"</tr>";
		}
		tabfooter('busualurlsedit');
		a_guide('usualurlsedit');
	}else{
		if(!empty($delete)){
			foreach($delete as $k){
				$db->query("DELETE FROM {$tblprefix}usualurls WHERE uid='$k'");
				unset($usualurlsnew[$k]);
			}
		}
		if(!empty($usualurlsnew)){
			foreach($usualurlsnew as $k => $v){
				$v['title'] = trim(strip_tags($v['title']));
				$v['url'] = trim(strip_tags($v['url']));
				$v['vieworder'] = max(0,intval($v['vieworder']));
				$v['available'] = empty($v['available']) ? 0 : 1;
				$sqlstr = "vieworder='$v[vieworder]',available='$v[available]'";
				$v['title'] && $sqlstr .= ",title='$v[title]'";
				$v['url'] && $sqlstr .= ",url='$v[url]'";
				$db->query("UPDATE {$tblprefix}usualurls SET $sqlstr WHERE uid='$k'");
			}
		}
		adminlog('�༭���������б�');
		cls_CacheFile::Update('usualurls');
		cls_message::show('�������ӱ༭���', "?entry=usualurls&action=usualurlsedit$mc_suffix");
	}
}elseif($action == 'usualurldetail' && $uid){
	if($re = $curuser->NoBackFunc($bk_func_item)) cls_message::show($re);
	if(!($usualurl = $db->fetch_one("SELECT * FROM {$tblprefix}usualurls WHERE uid='$uid'"))) cls_message::show('��ָ����ȷ�ĳ�������');
	if(!submitcheck('busualurldetail')){
		tabheader($mc_str.'������������','usualurldetail',"?entry=usualurls&action=usualurldetail&uid=$uid$mc_suffix");
		trbasic('�������ӱ���','usualurlnew[title]',$usualurl['title'],'text');
		trbasic('��������URL','usualurlnew[url]',$usualurl['url'],'text',array('w'=>50));
		trbasic('������������','usualurlnew[vieworder]',$usualurl['vieworder'],'text');
		trspecial('����ͼƬ',specialarr(array('type' => 'image','varname' => 'usualurlnew[logo]','value' => $usualurl['logo'],)));
		trbasic('�´��ڴ�����','usualurlnew[newwin]',$usualurl['newwin'],'radio');
		$ismc && trbasic('���Ӽ���onclick�ִ�','usualurlnew[onclick]',$usualurl['onclick'],'text',array('w'=>50));
		setPermBar('������ʾȨ������', 'usualurlnew[pmid]', @$usualurl['pmid'], 'menu', 'open', '');
        tabfooter('busualurldetail');
		a_guide('usualurldetail');
	}else{
		$usualurlnew['title'] = trim(strip_tags($usualurlnew['title']));
		$usualurlnew['url'] = trim(strip_tags($usualurlnew['url']));
		$usualurlnew['vieworder'] = max(0,intval($usualurlnew['vieworder']));
		$usualurlnew['title'] = empty($usualurlnew['title']) ? $usualurl['title'] : $usualurlnew['title'];
		$usualurlnew['url'] = empty($usualurlnew['url']) ? $usualurl['url'] : $usualurlnew['url'];
		$c_upload = cls_upload::OneInstance();	
		$usualurlnew['logo'] = upload_s($usualurlnew['logo'],$usualurl['logo'],'image');
		$usualurlnew['onclick'] = empty($usualurlnew['onclick']) ? '' : trim($usualurlnew['onclick']);
		$db->query("UPDATE {$tblprefix}usualurls SET 
					title='$usualurlnew[title]', 
					url='$usualurlnew[url]', 
					logo='$usualurlnew[logo]', 
					pmid='$usualurlnew[pmid]', 
					newwin='$usualurlnew[newwin]',
					onclick='$usualurlnew[onclick]',
					vieworder='$usualurlnew[vieworder]'
					WHERE uid='$uid'");
		$c_upload->closure(1, $uid, 'usualurls');
		adminlog('�༭������������');
		cls_CacheFile::Update('usualurls');
		cls_message::show('���������޸����', axaction(6,"?entry=usualurls&action=usualurlsedit$mc_suffix"));
	}
}
?>