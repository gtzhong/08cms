<?php
!defined('M_COM') && exit('No Permission');

$cuid = 34;
$commu = cls_cache::Read('commu',$cuid);
$page = !empty($page) ? max(1, intval($page)) : 1;
submitcheck('bfilter') && $page = 1;

$selectsql = "SELECT cu.* ";
$wheresql = " WHERE cu.tomid='$memberid'";
$fromsql = "FROM {$tblprefix}$commu[tbl] cu";

$cid = empty($cid) ? 0 : max(0,intval($cid));

if($cid){
	if(!($row = $db->fetch_one("SELECT c.* FROM {$tblprefix}$commu[tbl] c WHERE c.cid='$cid'"))) cls_message::show('ָ����¼�����ڡ�');
	$row['mspacehome'] = cls_Mspace::IndexUrl($row);
	$fields = cls_cache::Read('cufields',$cuid);
	tabheader("���Լ�¼",'newform',"?action=$action&cid=$cid",2,1,1);
	$a_field = new cls_field;
	foreach($fields as $k => $v){
		$a_field->init($v,isset($row[$k]) ? $row[$k] : '');
		$a_field->trfield('fmdata');
	}
	unset($a_field);
	tabfooter();
} else {
	if(!submitcheck('bsubmit')){
		tabheader("�����б�",'newform',"?action=$action&page=$page",9);
		$cy_arr = array("<input class=\"checkbox\" type=\"checkbox\" name=\"chkall\" onclick=\"checkall(this.form, 'selectid', 'chkall')\">",array('�ǳ�','left'),);
		$cy_arr[] = '���';
		$cy_arr[] = '��������';
		$cy_arr[] = '��ϸ';
		trcategory($cy_arr);

		$pagetmp = $page;
		do{
			$query = $db->query("$selectsql $fromsql $wheresql ORDER BY cu.cid DESC LIMIT ".(($pagetmp - 1) * $mrowpp).",$mrowpp");
			$pagetmp--;
		} while(!$db->num_rows($query) && $pagetmp);
		$itemstr = '';
		while($r = $db->fetch_array($query)){

			$selectstr = "<input class=\"checkbox\" type=\"checkbox\" name=\"selectid[$r[cid]]\" value=\"$r[cid]\">";
			$checkstr = $r['checked'] ? 'Y' : '-';
			$adddatestr = date('Y-m-d',$r['createdate']);
			$editstr = "<a href=\"?action=$action&cid=$r[cid]\" onclick=\"return floatwin('open_arcexit',this)\">�鿴</a>";
			$itemstr .= "<tr><td class=\"item\" width=\"40\">$selectstr</td><td class=\"item2\">$r[nicheng]</td>\n";
			$itemstr .= "<td class=\"item\">$checkstr</td>\n";
			$itemstr .= "<td class=\"item\" width=\"100\">$adddatestr</td>\n";
			$itemstr .= "<td class=\"item\" width=\"100\">$editstr</td>\n";
			$itemstr .= "</tr>\n";
		}
		echo $itemstr;
		tabfooter();
		echo multi($db->result_one("SELECT count(*) $fromsql $wheresql"),$mrowpp,$page, "?action=$action");

		tabheader('��������');
		trbasic("<input class=\"checkbox\" type=\"checkbox\" name=\"arcdeal[delete]\" value=\"1\"> ɾ������",'','��ѡ�м�¼���б������','');
		tabfooter('bsubmit');
	}else{
		if(empty($arcdeal)) cls_message::show('��ѡ�������Ŀ��',axaction(1,M_REFERER));
		if(empty($selectid)) cls_message::show('��ѡ����Ŀ��',axaction(1,M_REFERER));
		foreach($selectid as $k){
			$k = empty($k)?0:max(0,intval($k));
			if(!empty($arcdeal['delete'])){
				$db->query("DELETE FROM {$tblprefix}$commu[tbl] WHERE cid='$k'",'UNBUFFERED');
				continue;
			}
		}
		cls_message::show('�������������ɹ���',"?action=$action&page=$page");
	}
}

?>