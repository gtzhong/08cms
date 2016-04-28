<?php
!defined('M_COM') && exit('No Permission');
foreach(array('catalogs','channels',) as $k) $$k = cls_cache::Read($k);

$page = !empty($page) ? max(1, intval($page)) : 1;
submitcheck('bfilter') && $page = 1;
$type = empty($type) ? '' : $type;
isset($table) || $table = -1;
$aids = empty($aids) ? '' : $aids;

$wheresql = "WHERE mid='".$curuser->info['mid']."'";
if(!empty($type)){
	$wheresql .= " AND type='$type'";
}
if(!empty($aids)){
	$aidsarr = array_filter(explode(',',$aids));
	$wheresql .= " AND aid ".multi_str($aidsarr);
}
$table != -1 && $wheresql .= ($wheresql ? " AND " : "")."tid='$table'";

$filterstr = '';
foreach(array('aids','type','table') as $k)$filterstr .= "&$k=".urlencode($$k);
if(!submitcheck('buserfilesedit')){
	//ͬinclude/upload.cls.php��closure������$tids������Ӧ
	$tabsarr = array('-1' => 'ȫ������',1 => '�ĵ�', 2 => '������Ϣ', 3 => '��Ա', 16 => '����', 17 => '�ظ�', 18 => '����', 32 => '��Ա����', 33 => '��Ա�ظ�', '0' => '����');
	$linkarr = array(1 => 'archive&aid=', 2 => 'farchive&aid=', 3 => 'memberinfo&mid=', 4 => 'marchive&maid=', 16 => 'comment&cid=', 17 => 'reply&cid=', 18 => 'offer&cid=', 32 => 'mcomment&cid=', 33 => 'mreply&cid=');
	$typearr = array('0' => 'ȫ������','image' => 'ͼƬ','flash' => 'Flash','media' => '��Ƶ','file' => '����',);
	echo form_str($action.'arcsedit',"?action=userfiles");
	tabheader_e();
	echo "<tr><td class=\"item2\">";
	echo '����ĵ�ID(���ID�ö��Ÿ���)'."&nbsp; <input class=\"text\" name=\"aids\" type=\"text\" value=\"$aids\" style=\"vertical-align: middle;\">&nbsp; ";
	echo "<select style=\"vertical-align: middle;\" name=\"type\">".makeoption($typearr,$type)."</select>&nbsp; ";
	echo "<select style=\"vertical-align: middle;\" name=\"table\">".makeoption($tabsarr,$table)."</select>&nbsp; ";
	echo strbutton('bfilter','ɸѡ').'</td></tr>';
	tabfooter();

	$pagetmp = $page;
	do{
		$query = $db->query("SELECT * FROM {$tblprefix}userfiles $wheresql ORDER BY ufid DESC LIMIT ".(($pagetmp - 1) * $mrowpp).",$mrowpp");
		$pagetmp--;
	} while(!$db->num_rows($query) && $pagetmp);
	$itemstr = '';
	while($item = $db->fetch_array($query)) {
		$item['createdate'] = date("$dateformat", $item['createdate']);
		$item['preview'] = ($item['type'] == 'image') ? "<a href=\"".cls_url::tag2atm($item['url'])."\" target=\"_blank\">".'Ԥ��'."</a>" : "-";
		$item['type'] = $typearr[$item['type']];
		$item['thumbedstr'] = $item['thumbed'] ? 'Y' : '-';
		$item['size'] = ceil($item['size'] / 1024);
		$item['source'] = $item['aid'] && $item['tid'] ? "<a href=\"?action=".$linkarr[$item['tid']]."$item[aid]\" target=\"_blank\" onclick=\"return floatwin('open_editbyatt',this)\">".'�鿴'."</a>" : "-";
		$itemstr .= "<tr><td align=\"center\" class=\"item1\" width=\"40\"><input class=\"checkbox\" type=\"checkbox\" name=\"selectid['$item[ufid]']\" value=\"$item[ufid]\">\n".
			"<td class=\"item2\">$item[filename]</td>\n".
			"<td class=\"item\" width=\"40\">$item[type]</td>\n".
			"<td class=\"item\" width=\"60\">$item[size]</td>\n".
			"<td class=\"item\" width=\"40\">$item[preview]</td>\n".
			"<td class=\"item\" width=\"50\">$item[thumbedstr]</td>\n".
			"<td class=\"item\" width=\"78\">$item[createdate]</td>\n".
			"<td class=\"item\" width=\"40\">$item[source]</td></tr>\n";
	}
	$itemcount = $db->result_one("SELECT count(*) FROM {$tblprefix}userfiles $wheresql");
	$multi = multi($itemcount, $mrowpp, $page, "?action=userfiles$filterstr");

	tabheader('�����б�','','',9);
	trcategory(array("<input class=\"checkbox\" type=\"checkbox\" name=\"chkall\" class=\"category\" onclick=\"checkall(this.form, 'selectid', 'chkall')\">".'ɾ?',array('����','left'),'����','��С(K)','Ԥ��','����ͼ','�ϴ�����','��Դ'));
	echo $itemstr;
	tabfooter();
	echo $multi;
	echo "<br><input class=\"button\" type=\"submit\" name=\"buserfilesedit\" value=\"�ύ\"></form>";
}else{
	empty($selectid) && cls_message::show('��ѡ���ĵ�',"?action=userfiles&page=$page$filterstr");
	$query = $db->query("SELECT * FROM {$tblprefix}userfiles WHERE ufid ".multi_str($selectid)." AND mid='".$curuser->info['mid']."' ORDER BY ufid");
	while($r = $db->fetch_array($query)){
		atm_delete($r['url'],$r['type']);
		$curuser->updateuptotal(ceil($r['size'] / 1024),1);
	}
	$curuser->updatedb();
	$db->query("DELETE FROM {$tblprefix}userfiles WHERE ufid ".multi_str($selectid)." AND mid='".$curuser->info['mid']."'",'UNBUFFERED');
	cls_message::show('�ĵ��������',"?action=userfiles&page=$page$filterstr");
}
?>