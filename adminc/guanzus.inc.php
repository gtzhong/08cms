<?php
!defined('M_COM') && exit('No Permission');
$cuid = 7;
if(!($commu = cls_cache::Read('commu',$cuid))) cls_message::show('�����ڵĽ�����Ŀ��');
$modearr = array('new' => '�·���̬','old' => '���ַ�Դ','rent' => '���ⷿԴ',);
$page = !empty($page) ? max(1, intval($page)) : 1;
submitcheck('bfilter') && $page = 1;
$ccid1 = empty($ccid1) ? 0 : max(0,intval($ccid1));
$keyword = empty($keyword) ? '' : $keyword;

$selectsql = "SELECT cu.*,cu.createdate AS ucreatedate,a.createdate,a.initdate,a.caid,a.chid,a.customurl,a.nowurl,a.subject,a.ccid1";
$wheresql = " WHERE cu.mid='$memberid'";
$fromsql = "FROM {$tblprefix}$commu[tbl] cu INNER JOIN {$tblprefix}".atbl(4)." a ON a.aid=cu.aid";

if($ccid1 && $cnsql = cnsql(1,sonbycoid($ccid1,1),'a.')) $wheresql .= " AND $cnsql";
$keyword && $wheresql .= " AND a.subject LIKE '%".str_replace(array(' ','*'),'%',addcslashes($keyword,'%_'))."%'";

$filterstr = '';
foreach(array('keyword',) as $k) $$k && $filterstr .= "&$k=".rawurlencode(stripslashes($$k));
if(!submitcheck('bsubmit')){
	echo form_str('newform',"?action=$action&page=$page");
	tabheader_e();
	echo "<tr><td class=\"item2\">";
	echo "<div class='filter'><input class=\"text\" name=\"keyword\" type=\"text\" value=\"$keyword\" size=\"20\" placeholder=\"���������\" style=\"vertical-align: middle;\" title=\"����¥��\">&nbsp; ";
	echo '<span>'.cn_select("ccid1",array('value' => $ccid1,'coid' => 1,'notip' => 1,'addstr' => '��������','vmode' => 0,'framein' => 1,)).'</span>&nbsp; ';
	echo strbutton('bfilter','ɸѡ');
	echo "</div></td></tr>";
	tabfooter();
	tabheader('�ҹ�ע��¥��','','',9);
	$cy_arr = array("<input class=\"checkbox\" type=\"checkbox\" name=\"chkall\" onclick=\"checkall(this.form, 'selectid', 'chkall')\">",array('¥������','left'),);
	$cy_arr[] = '����';
	$cy_arr[] = array('����¥�̶�̬','left');
	$cy_arr[] = '�����ע';
	trcategory($cy_arr);
	
	$pagetmp = $page;
	do{
		$query = $db->query("$selectsql $fromsql $wheresql ORDER BY cu.cid DESC LIMIT ".(($pagetmp - 1) * $mrowpp).",$mrowpp");
		$pagetmp--;
	} while(!$db->num_rows($query) && $pagetmp);

	$itemstr = '';
	while($r = $db->fetch_array($query)){
		$selectstr = "<input class=\"checkbox\" type=\"checkbox\" name=\"selectid[$r[cid]]\" value=\"$r[cid]\">";
		$subjectstr = "<a href=\"".cls_ArcMain::Url($r)."\" target=\"_blank\">$r[subject]</a>";
		$adddatestr = date('Y-m-d',$r['ucreatedate']);
		$coclasses = cls_cache::Read('coclasses',1);
		$ccid1str = @$coclasses[$r['ccid1']]['title'];
		$gzstr = '';foreach($modearr as $k => $v) $r[$k] && $gzstr .= $v.' &nbsp;';

		$itemstr .= "<tr><td class=\"item\" width=\"40\">$selectstr</td><td class=\"item2\">$subjectstr</td>\n";
		$itemstr .= "<td class=\"item\">$ccid1str</td>\n";
		$itemstr .= "<td class=\"item2\">$gzstr</td>\n";
		$itemstr .= "<td class=\"item\" width=\"100\">$adddatestr</td>\n";
		$itemstr .= "</tr>\n";
	}
	echo $itemstr;
	tabfooter();
	echo multi($db->result_one("SELECT count(*) $fromsql $wheresql"),$mrowpp,$page, "?action=$action$filterstr");
	
	tabheader('��������');
	$s_arr = array();
	$s_arr['delete'] = 'ɾ��';
	foreach($modearr as $k => $v){
		$s_arr[$k] = "����$v";
		$s_arr["un$k"] = "�˶�$v";
	}
	if($s_arr){
		$str = '';
		$i = 1;
		foreach($s_arr as $k => $v){
			$str .= "<label><input class=\"checkbox\" type=\"checkbox\" name=\"arcdeal[$k]\" value=\"1\"".($k=='delete'?' onclick="deltip()"':'').">$v</label> &nbsp;";
			if(!($i % 5)) $str .= '<br>';
			$i ++;
		}
		trbasic('ѡ�������Ŀ','',$str,'');
	}
	tabfooter('bsubmit');
	m_guide('����ָ��¥�̵�������ݺ���������Email���յ�ָ�����ݵ����±仯��Ϣ��');
	
}else{
	
	if(empty($arcdeal)) cls_message::show('��ѡ�������Ŀ��',axaction(1,M_REFERER));
	if(empty($selectid)) cls_message::show('��ѡ���ע��¥�̡�',axaction(1,M_REFERER));
	foreach($selectid as $k){
		$k = empty($k) ? 0 : max(0, intval($k));
		if(!empty($arcdeal['delete'])){
			$db->query("DELETE FROM {$tblprefix}$commu[tbl] WHERE cid='$k'",'UNBUFFERED');
			continue;
		}
		$sqlstr = '';
		foreach($modearr as $x => $y){
			if(!empty($arcdeal[$x])){
				$sqlstr .= ",$x=1";
			}elseif(!empty($arcdeal["un$x"])){
				$sqlstr .= ",$x=0";
			}
		}
		if($sqlstr = substr($sqlstr,1)) $db->query("UPDATE {$tblprefix}$commu[tbl] SET $sqlstr WHERE cid='$k'");
	}
	cls_message::show('��ע¥�����������ɹ���',"?action=$action&page=$page$filterstr");
}

?>