<?php
!defined('M_COM') && exit('No Permission');

$arid = 4;$schid = 2;$tchid = 3;
if(!($abrel = cls_cache::Read('abrel',$arid)) || empty($abrel['available'])) cls_message::show('�����ڻ�رյĺϼ���Ŀ��');
if($curuser->info['mchid'] != $schid) cls_message::show('����ע��Ϊ�����ˡ�');
$curuser->detail_data();
$info = &$curuser->info;

if($info["pid$arid"] && $info["incheck$arid"]){
	
	backnav('company','manage');
	$au = new cls_userinfo;
	$au->activeuser($info["pid$arid"]);
	if($au->info['mid'] && $au->info['checked'] && $au->info['mchid'] == $tchid){
		$au->detail_data();
		tabheader('�ҵľ��͹�˾');
		trbasic('��˾����','',$au->info['cmane'],'');
		trbasic('��ϵ�绰','',$au->info['lxdh'],'');
		trbasic('��˾��ַ','',$au->info['caddress'],'');
		trbasic('��˾����','',"<a href=\"".$au->info['mspacehome']."\" target=\"_blank\">>>��ȥ���</a>",'');
		tabfooter();
	}else{
		$curuser->exit_comp();
		cls_message::show('δ�ҵ��������ľ��͹�˾��',"?action=$action");
	}
}elseif(empty($deal)){
	$page = empty($page) ? 1 : max(1, intval($page));
	submitcheck('bfilter') && $page = 1;
	$szqy = empty($szqy) ? 0 : max(0,intval($szqy));
	$keyword = empty($keyword) ? '' : $keyword;
	$wheresql = " WHERE m.mchid='$tchid' AND m.checked=1";
	$fromsql = " FROM {$tblprefix}members m INNER JOIN {$tblprefix}members_sub s ON s.mid=m.mid INNER JOIN {$tblprefix}members_$tchid c ON c.mid=m.mid";
	$keyword && $wheresql .= " AND (c.cmane LIKE '%".str_replace(array(' ','*'),'%',addcslashes($keyword,'%_'))."%' OR m.mname LIKE '%".str_replace(array(' ','*'),'%',addcslashes($keyword,'%_'))."%')";
	if($szqy && $cnsql = caccsql('s.szqy',sonbycoid($szqy,1))) $wheresql .= " AND $cnsql";
	
	$filterstr = '';
	foreach(array('keyword','szqy',) as $k) $$k && $filterstr .= "&$k=".rawurlencode(stripslashes($$k));
	
	$szqyarr = array();if($arr = cacc_arr('m',$tchid,'szqy')) foreach($arr as $k => $v) $szqyarr[$k] = $v['title'];
	echo form_str($action,"?action=$action&page=$page");
	tabheader_e();
	echo "<tr><td class=\"item2\">";
	echo "&nbsp; <input class=\"text\" name=\"keyword\" type=\"text\" value=\"$keyword\" size=\"8\" style=\"vertical-align: middle;\">&nbsp; ";
	echo "<select style=\"vertical-align: middle;\" name=\"szqy\">".makeoption(array(0 => '���޵���') + $szqyarr,$szqy)."</select>&nbsp; ";
	echo strbutton('bfilter','ɸѡ');
	echo '</td></tr>';
	tabfooter();
	
	$pagetmp = $page;
	do{
		$query = $db->query("SELECT m.*,s.*,c.* $fromsql $wheresql ORDER BY m.mid DESC LIMIT ".(($pagetmp - 1) * $mrowpp).",$mrowpp");
		$pagetmp--;
	}while(!$db->num_rows($query) && $pagetmp);
	
	$addstr = '';
	if($info["pid$arid"] && $ninfo = $db->fetch_one("SELECT m.*,c.* $fromsql WHERE m.mid='".$info["pid$arid"]."' AND m.mchid='$tchid' AND m.checked=1")){
		$info['mspacehome'] = cls_Mspace::IndexUrl($info);
		$addstr = " &nbsp; &nbsp;�����������:<a href=\"".$info['mspacehome']."\" target=\"_blank\">$ninfo[cmane]</a>";
	}
	tabheader('������뾭�͹�˾'.$addstr,'','',10);
	trcategory(array('ID',array('���͹�˾','left'),array('��Ա','left'),'����','��˾����','����'));
	while($row = $db->fetch_array($query)){
		$row['mspacehome'] = cls_Mspace::IndexUrl($row);
		$dpstr = "<a href=\"".$row['mspacehome']."\" target=\"_blank\">���</a>";
		$szqystr = $row['szqy'] ? $szqyarr[$row['szqy']] : '-';
		$jrstr = $info["pid$arid"] == $row['mid'] ? "<a href=\"?action=$action&deal=qx\"><b>ȡ��</b></a>" : "<a href=\"?action=$action&deal=jr&mid=$row[mid]\">����</a>";
		echo "<tr>\n".
			"<td class=\"item\" width=\"40\">$row[mid]</td>\n".
			"<td class=\"item2\">$row[cmane]</td>\n".
			"<td class=\"item2\" width=\"100\">$row[mname]</td>\n".
			"<td class=\"item\" width=\"60\">$szqystr</td>\n".
			"<td class=\"item\" width=\"60\">$dpstr</td>\n".
			"<td class=\"item\" width=\"60\">$jrstr</td>\n".
			"</tr>\n";
	}
	tabfooter();
	echo multi($db->result_one("SELECT count(*) $fromsql $wheresql"),$mrowpp,$page,"?action=$action$filterstr");
}elseif($deal == 'jr'){
	if(!($mid = empty($mid) ? 0 : max(0,intval($mid)))) cls_message::show('��ָ��Ҫ����Ĺ�˾��',M_REFERER);
	$k = $curuser->ag2comp($mid);
	cls_message::show($k ? '����ɹ�����ȴ���˾��ˡ�' : '���벻�ɹ���',M_REFERER);
}elseif($deal == 'qx'){
	$curuser->exit_comp();
	cls_message::show('�ɹ�ȡ�����롣',M_REFERER);
}

?>
