<?php
!defined('M_COM') && exit('No Permission');
$cuid = 3;
if(!($commu = cls_cache::Read('commu',$cuid))) cls_message::show('�����ڵĽ�����Ŀ��');

$aid = empty($aid) ? 0 : max(0,intval($aid));
$cid = empty($cid) ? 0 : max(0,intval($cid));
$chid = empty($chid) ? 4 : max(0,intval($chid)); //4;

//��ȡ�������¥��aid
$mchid = $curuser->info['mchid'];
$sql_ids = "SELECT loupan FROM {$tblprefix}members_$mchid WHERE mid='$memberid'"; 
$loupanids = $db->result_one($sql_ids); if($loupanids) $loupanids = substr($loupanids,1); //echo($loupanids);
if(empty($loupanids)) $loupanids = 0;

if($cid){
	if(!($row = $db->fetch_one("SELECT * FROM {$tblprefix}$commu[tbl] WHERE cid='$cid'"))) cls_message::show('ָ���������¼�����ڡ�');
	$fields = cls_cache::Read('cufields',$cuid);
	if(!submitcheck('bsubmit')){
		$arc = new cls_arcedit;
		$arc->set_aid($row['aid'],array('chid'=>$chid,'au'=>0));
		tabheader("�����¼�༭ &nbsp;<a href=\"".cls_ArcMain::Url($arc->archive)."\" target=\"_blank\">>>{$arc->archive['subject']}</a>",'newform',"?action=$action&cid=$cid",2,1,1);
		$a_field = new cls_field;
		foreach($fields as $k => $v){
			$a_field->init($v,isset($row[$k]) ? $row[$k] : '');
			$a_field->trfield('fmdata');
		}
		unset($a_field);
		tabfooter('bsubmit');
	}else{//���ݴ���
		$sqlstr = '';
		$c_upload = new cls_upload;	
		$a_field = new cls_field;
		foreach($fields as $k => $v){
			if(isset($fmdata[$k])){
				$a_field->init($v,isset($row[$k]) ? $row[$k] : '');
				$fmdata[$k] = $a_field->deal('fmdata','mcmessage',axaction(2,M_REFERER));
				$sqlstr .= ",$k='$fmdata[$k]'";
				if($arr = multi_val_arr($fmdata[$k],$v)) foreach($arr as $x => $y) $sqlstr .= ",{$k}_x='$y'";
			}
		}
		unset($a_field);
		$sqlstr = substr($sqlstr,1);
		$sqlstr && $db->query("UPDATE {$tblprefix}$commu[tbl] SET $sqlstr  WHERE cid='$cid'");
		$c_upload->closure(1,$cid,"commu$cuid");
		$c_upload->saveuptotal(1);
		cls_message::show('�����¼�༭���',axaction(6,M_REFERER));
	}
}elseif($aid){
	$arc = new cls_arcedit;
	$arc->set_aid($aid);
	if(!$arc->aid) cls_message::show('ָ�����ĵ������ڡ�');
	$page = !empty($page) ? max(1, intval($page)) : 1;
	submitcheck('bfilter') && $page = 1;
	$checked = isset($checked) ? $checked : '-1';
	$keyword = empty($keyword) ? '' : $keyword;
	$indays = empty($indays) ? 0 : max(0,intval($indays));
	$outdays = empty($outdays) ? 0 : max(0,intval($outdays));
	
	$selectsql = "SELECT cu.*";
	$wheresql = " WHERE cu.aid='$aid'";
	$fromsql = "FROM {$tblprefix}$commu[tbl] cu";
	
	if($checked != -1) $wheresql .= " AND cu.checked='$checked'";
	$keyword && $wheresql .= " AND cu.mname LIKE '%".str_replace(array(' ','*'),'%',addcslashes($keyword,'%_'))."%'";
	$indays && $wheresql .= " AND cu.createdate>'".($timestamp - 86400 * $indays)."'";
	$outdays && $wheresql .= " AND cu.createdate<'".($timestamp - 86400 * $outdays)."'";
	
	$filterstr = '';
	foreach(array('keyword','indays','outdays',) as $k) $$k && $filterstr .= "&$k=".rawurlencode(stripslashes($$k));
	foreach(array('checked',) as $k) $$k != -1 && $filterstr .= "&$k=".$$k;
	if(!submitcheck('bsubmit')){
		echo form_str('arcsedit',"?action=$action&aid=$aid&page=$page&chid=$chid");
		tabheader_e();
		echo "<tr><td colspan=\"2\" class=\"txt txtleft\">";
		echo "<input class=\"text\" name=\"keyword\" type=\"text\" value=\"$keyword\" size=\"8\" style=\"vertical-align: middle;\" title=\"������������\">&nbsp; ";
		echo "<select style=\"vertical-align: middle;\" name=\"checked\">".makeoption(array('-1' => '���״̬','0' => 'δ��','1' => '����'),$checked)."</select>&nbsp; ";
		echo "<input class=\"text\" name=\"outdays\" type=\"text\" value=\"$outdays\" size=\"4\" style=\"vertical-align: middle;\">��ǰ&nbsp; ";
		echo "<input class=\"text\" name=\"indays\" type=\"text\" value=\"$indays\" size=\"4\" style=\"vertical-align: middle;\">����&nbsp; ";
		echo strbutton('bfilter','ɸѡ');
		tabfooter();
		tabheader("�����б�-<a style=\"color:#C00\" href=\"".cls_ArcMain::Url($arc->archive)."\" target=\"_blank\">{$arc->archive['subject']}</a>",'','',9);
		$cy_arr = array("<input class=\"checkbox\" type=\"checkbox\" name=\"chkall\" onclick=\"checkall(this.form, 'selectid', 'chkall')\">",array('��Ա','txtL'),);
		$cy_arr[] = '���';
		$cy_arr[] = '���ʱ��';
		$cy_arr[] = '�༭';
		trcategory($cy_arr);
		
		$pagetmp = $page;
		do{
			$query = $db->query("$selectsql $fromsql $wheresql ORDER BY cu.cid DESC LIMIT ".(($pagetmp - 1) * $atpp).",$atpp");
			$pagetmp--;
		} while(!$db->num_rows($query) && $pagetmp);
	
		$itemstr = '';
		while($r = $db->fetch_array($query)){
			$selectstr = "<input class=\"checkbox\" type=\"checkbox\" name=\"selectid[$r[cid]]\" value=\"$r[cid]\">";
			$mnamestr = $r['mname'];
			$checkstr = $r['checked'] ? 'Y' : '-';
			$adddatestr = date('Y-m-d',$r['createdate']);
			$editstr = "<a href=\"?action=$action&cid=$r[cid]\" onclick=\"return floatwin('open_commentsedit',this)\">����</a>";
			$itemstr .= "<tr class=\"txt\"><td class=\"item\" >$selectstr</td><td class=\"item2\">$mnamestr</td>\n";
			$itemstr .= "<td class=\"item\">$checkstr</td>\n";
			$itemstr .= "<td class=\"item\">$adddatestr</td>\n";
			$itemstr .= "<td class=\"item\">$editstr</td>\n";
			$itemstr .= "</tr>\n";
		}
		echo $itemstr;
		tabfooter();
		echo multi($db->result_one("SELECT count(*) $fromsql $wheresql"),$atpp,$page, "?action=$action&aid=$aid$filterstr&chid=$chid");
		
		tabheader('��������');
		$s_arr = array();
		$s_arr['delete'] = 'ɾ��';
		$s_arr['check'] = '���';
		$s_arr['uncheck'] = '����';
		if($s_arr){
			$str = '';
			$i = 1;
			foreach($s_arr as $k => $v){
				$str .= "<input class=\"checkbox\" type=\"checkbox\" name=\"arcdeal[$k]\" value=\"1\"".($k=='delete'?' onclick="deltip()"':'').">$v &nbsp;";
				if(!($i % 5)) $str .= '<br>';
				$i ++;
			}
			trbasic('ѡ�������Ŀ','',$str,'');
		}
		tabfooter('bsubmit');
	}else{
		if(empty($arcdeal)) cls_message::show('��ѡ�������Ŀ��',axaction(1,M_REFERER));
		if(empty($selectid)) cls_message::show('��ѡ�������¼��',axaction(1,M_REFERER));
		foreach($selectid as $k){
			$k = empty($k) ? 0 : max(0, intval($k));
			if(!empty($arcdeal['delete'])){
				$db->query("DELETE FROM {$tblprefix}$commu[tbl] WHERE cid='$k'",'UNBUFFERED');
				continue;
			}
			if(!empty($arcdeal['check'])){
				$db->query("UPDATE {$tblprefix}$commu[tbl] SET checked='1' WHERE cid='$k'");
			}elseif(!empty($arcdeal['uncheck'])){
				$db->query("UPDATE {$tblprefix}$commu[tbl] SET checked='0' WHERE cid='$k'");
			}
		}
		cls_message::show('�������������ɹ���',axaction(0,M_REFERER));
		
	}
	
	
}else{
	$ccid1 = empty($ccid1) ? 0 : max(0,intval($ccid1));
	$page = !empty($page) ? max(1, intval($page)) : 1;
	submitcheck('bfilter') && $page = 1;
	$checked = isset($checked) ? $checked : '-1';
	$keyword = empty($keyword) ? '' : $keyword;
	$indays = empty($indays) ? 0 : max(0,intval($indays));
	$outdays = empty($outdays) ? 0 : max(0,intval($outdays));
	
	$selectsql = "SELECT cu.*,cu.createdate AS ucreatedate,a.createdate,a.initdate,a.caid,a.chid,a.customurl,a.nowurl,a.subject,a.ccid1";
	$wheresql = "";
	$fromsql = "FROM {$tblprefix}$commu[tbl] cu INNER JOIN {$tblprefix}".atbl(4)." a ON a.aid=cu.aid WHERE cu.aid IN($loupanids) ";
	
	if($ccid1 && $cnsql = cnsql(1,sonbycoid($ccid1,1),'a.')) $wheresql .= " AND $cnsql";
	if($checked != -1) $wheresql .= " AND cu.checked='$checked'";
	$keyword && $wheresql .= " AND (cu.mname LIKE '%".str_replace(array(' ','*'),'%',addcslashes($keyword,'%_'))."%' OR a.subject LIKE '%".str_replace(array(' ','*'),'%',addcslashes($keyword,'%_'))."%')";
	$indays && $wheresql .= " AND cu.createdate>'".($timestamp - 86400 * $indays)."'";
	$outdays && $wheresql .= " AND cu.createdate<'".($timestamp - 86400 * $outdays)."'";	
	
	$filterstr = '';
	foreach(array('ccid1','keyword','indays','outdays',) as $k) $$k && $filterstr .= "&$k=".rawurlencode(stripslashes($$k));
	foreach(array('checked',) as $k) $$k != -1 && $filterstr .= "&$k=".$$k;
	if(!submitcheck('bsubmit')){
		echo form_str('arcsedit',"?action=$action&page=$page");
		tabheader_e();
		echo "<tr><td colspan=\"2\" class=\"txt txtleft\">";
		echo "<input class=\"text\" name=\"keyword\" type=\"text\" value=\"$keyword\" size=\"8\" style=\"vertical-align: middle;\" title=\"����¥�̻���������\">&nbsp; ";
		echo '<span>'.cn_select("ccid1",array('value' => $ccid1,'coid' => 1,'notip' => 1,'addstr' => '���޵���','vmode' => 0,'framein' => 1,)).'</span>&nbsp; ';
		echo "<select style=\"vertical-align: middle;\" name=\"checked\">".makeoption(array('-1' => '���״̬','0' => 'δ��','1' => '����'),$checked)."</select>&nbsp; ";
		echo "<input class=\"text\" name=\"outdays\" type=\"text\" value=\"$outdays\" size=\"4\" style=\"vertical-align: middle;\">��ǰ&nbsp; ";
		echo "<input class=\"text\" name=\"indays\" type=\"text\" value=\"$indays\" size=\"4\" style=\"vertical-align: middle;\">����&nbsp; ";
		echo strbutton('bfilter','ɸѡ');
		tabfooter();
		tabheader('¥�������б�','','',9);
		$cy_arr = array("<input class=\"checkbox\" type=\"checkbox\" name=\"chkall\" onclick=\"checkall(this.form, 'selectid', 'chkall')\">",array('����¥��','txtL'),);
		$cy_arr[] = '����';
		$cy_arr[] = '��Ա';
		$cy_arr[] = '���';
		$cy_arr[] = '���ʱ��';
		$cy_arr[] = '�༭';
		trcategory($cy_arr);
		
		$pagetmp = $page;
		do{
			$query = $db->query("$selectsql $fromsql $wheresql ORDER BY cu.cid DESC LIMIT ".(($pagetmp - 1) * $atpp).",$atpp");
			$pagetmp--;
		} while(!$db->num_rows($query) && $pagetmp);
	
		$itemstr = '';
		while($r = $db->fetch_array($query)){
			$selectstr = "<input class=\"checkbox\" type=\"checkbox\" name=\"selectid[$r[cid]]\" value=\"$r[cid]\">";
			$subjectstr = "<a href=\"".cls_ArcMain::Url($r)."\" target=\"_blank\">$r[subject]</a>";
			$coclasses = cls_cache::Read('coclasses',1);
			$ccid1str = @$coclasses[$r['ccid1']]['title'];
			$mnamestr = $r['mname'];
			$checkstr = $r['checked'] ? 'Y' : '-';
			$adddatestr = date('Y-m-d',$r['ucreatedate']);
			$editstr = "<a href=\"?action=$action&cid=$r[cid]\" onclick=\"return floatwin('open_commentsedit',this)\">����</a>";
	
			$itemstr .= "<tr><td class=\"item\" >$selectstr</td><td class=\"item2\">$subjectstr</td>\n";
			$itemstr .= "<td class=\"item\">$ccid1str</td>\n";
			$itemstr .= "<td class=\"item\">$mnamestr</td>\n";
			$itemstr .= "<td class=\"item\">$checkstr</td>\n";
			$itemstr .= "<td class=\"item\">$adddatestr</td>\n";
			$itemstr .= "<td class=\"item\">$editstr</td>\n";
			$itemstr .= "</tr>\n";
		}
		echo $itemstr;
		tabfooter();
		echo multi($db->result_one("SELECT count(*) $fromsql $wheresql"),$atpp,$page, "?action=$action$filterstr");
		
		tabheader('��������');
		$s_arr = array();
		$s_arr['delete'] = 'ɾ��';
		$s_arr['check'] = '���';
		$s_arr['uncheck'] = '����';
		if($s_arr){
			$str = '';
			$i = 1;
			foreach($s_arr as $k => $v){
				$str .= "<input class=\"checkbox\" type=\"checkbox\" name=\"arcdeal[$k]\" value=\"1\"".($k=='delete'?' onclick="deltip()"':'').">$v &nbsp;";
				if(!($i % 5)) $str .= '<br>';
				$i ++;
			}
			trbasic('ѡ�������Ŀ','',$str,'');
		}
		tabfooter('bsubmit');
	}else{
		if(empty($arcdeal)) cls_message::show('��ѡ�������Ŀ��',axaction(1,M_REFERER));
		if(empty($selectid)) cls_message::show('��ѡ�������¼��',axaction(1,M_REFERER));
		foreach($selectid as $k){
			$k = empty($k) ? 0 : max(0, intval($k));
			if(!empty($arcdeal['delete'])){
				$db->query("DELETE FROM {$tblprefix}$commu[tbl] WHERE cid='$k'",'UNBUFFERED');
				continue;
			}
			if(!empty($arcdeal['check'])){
				$db->query("UPDATE {$tblprefix}$commu[tbl] SET checked='1' WHERE cid='$k'");
			}elseif(!empty($arcdeal['uncheck'])){
				$db->query("UPDATE {$tblprefix}$commu[tbl] SET checked='0' WHERE cid='$k'");
			}
		}
		cls_message::show('�������������ɹ���',axaction(0,M_REFERER));
		
		
	}
}
?>