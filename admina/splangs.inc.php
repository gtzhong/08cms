<?
(!defined('M_COM') || !defined('M_ADMIN')) && exit('No Permission');
aheader();
if($re = $curuser->NoBackFunc('affix')) cls_message::show($re);
$types = array('email' => 'Email','pm' => 'վ�ڶ���',);
if($action == 'splangsedit'){
	backnav('otherset','email');
	$ftype = empty($ftype) ? '' : $ftype;
	$splangs = fetch_arr($ftype);
	if(!submitcheck('bsplangsedit')) {
		tabheader('��������ģ�����','','','7');
		trcategory(array('���',array('������������','txtL'),'����','����'));
		$sn = 0;
		foreach($splangs as $slid => $splang){
			if(empty($ftype) || $ftype == $splang['type']){
			$sn ++;
			echo "<tr class=\"txt\">".
				"<td class=\"txtC w40\">$sn</td>\n".
				"<td class=\"txtL\">".$splang['cname']."</td>\n".
				"<td class=\"txtC w120\">".$types[$splang['type']]."</td>\n".
				"<td class=\"txtC w40\"><a href=\"?entry=splangs&action=splangdetail&slid=$slid\" onclick=\"return floatwin('open_splang',this)\">�༭</a></td></tr>\n";
			}
		}
		tabfooter();
		a_guide('splangsedit');
	}
}elseif($action == 'splangdetail' && $slid){
	$splang = fetch_one($slid);
	if(!submitcheck('bsplangdetail')){
		tabheader('������������','splangsdetail',"?entry=splangs&action=splangdetail&slid=$slid");
		trbasic('������������','',$splang['cname'],'');
		trbasic('������������','',$types[$splang['type']],'');
		trbasic('������������','splangnew[content]',$splang['content'],'textarea',array('w' => 500,'h' => 300,));
		tabfooter('bsplangdetail');
		a_guide('splangdetail');
	}
	else{
		if(empty($splangnew['content'])) cls_message::show('���ϲ���ȫ',M_REFERER);
		$db->query("UPDATE {$tblprefix}splangs SET content='$splangnew[content]' WHERE slid='$slid'");
		cls_CacheFile::Update('splangs');
		adminlog('��ϸ�޸Ĺ�������');
		cls_message::show('���������޸����',axaction(6,"entry=splangs&action=splangsedit"));
	}
}
function fetch_arr($type){
	global $db,$tblprefix;
	$items = array();
	$query = $db->query("SELECT * FROM {$tblprefix}splangs ".($type ? "WHERE type='$type'" : '')." ORDER BY vieworder,slid");
	while($item = $db->fetch_array($query)){
		$items[$item['slid']] = $item;
	}
	return $items;
}
function fetch_one($slid){
	global $db,$tblprefix;
	$item = $db->fetch_one("SELECT * FROM {$tblprefix}splangs WHERE slid='$slid'");
	return $item;
}

?>
