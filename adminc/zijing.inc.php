<?php
!defined('M_COM') && exit('No Permission');

$arid = 4;$schid = 2;$tchid = 3;
if(!($abrel = cls_cache::Read('abrel',$arid)) || empty($abrel['available'])) cls_message::show('�����ڻ�رյĺϼ���Ŀ��');
$cuid = 10;
if(!($commu = cls_cache::Read('commu',$cuid)) || !$commu['available']) cls_message::show('��˾�ʽ�������ѹرա�');
$curuser->detail_data();

if(empty($deal)){//��˾�鿴�����¼�������˲鿴�������¼��
	backnav('company','cash');
	if($curuser->info['mchid'] == $tchid){
		$page = !empty($page) ? max(1, intval($page)) : 1;
		submitcheck('bfilter') && $page = 1;
		$mode = isset($mode) ? $mode : -1;
		$indays = empty($indays) ? 0 : max(0,intval($indays));
		$outdays = empty($outdays) ? 0 : max(0,intval($outdays));
		$keyword = empty($keyword) ? '' : $keyword;
		$wheresql = "WHERE mid='$memberid'";
		$fromsql = "FROM {$tblprefix}$commu[tbl]";
		
		if($mode != -1) $wheresql .= " AND zj".($mode ? '<' : '>')."0";
		$indays && $wheresql .= " AND createdate>'".($timestamp - 86400 * $indays)."'";
		$outdays && $wheresql .= " AND createdate<'".($timestamp - 86400 * $outdays)."'";
		$keyword && $wheresql .= " AND tomname LIKE '%".str_replace(array(' ','*'),'%',addcslashes($keyword,'%_'))."%'";
		$filterstr = '';
		foreach(array('keyword','indays','outdays',) as $k) $$k && $filterstr .= "&$k=".rawurlencode(stripslashes($$k));
		foreach(array('mode',) as $k) $$k != -1 && $filterstr .= "&$k=".$$k;
	
		echo form_str($action.'newform',"?action=$action&page=$page");
		tabheader_e();
		echo "<tr><td class=\"item2\">";
		echo "<div clas='filter'><input class=\"text\" name=\"keyword\" type=\"text\" value=\"$keyword\" size=\"8\" style=\"vertical-align: middle;\" title=\"����������\">&nbsp; ";
		echo "<select style=\"vertical-align: middle;\" name=\"mode\">".makeoption(array('-1' => '���޷�ʽ','0' => '����','1' => '��ȡ',),$mode)."</select>&nbsp; ";
		echo "<input class=\"text\" name=\"outdays\" type=\"text\" value=\"$outdays\" size=\"4\" style=\"vertical-align: middle;\">��ǰ&nbsp; ";
		echo "<input class=\"text\" name=\"indays\" type=\"text\" value=\"$indays\" size=\"4\" style=\"vertical-align: middle;\">����&nbsp; ";
		echo strbutton('bfilter','ɸѡ');
		echo "</div></td></tr>";
		tabfooter();
		//�б���
		$sum = $db->result_one("SELECT SUM(zj) $fromsql $wheresql");
		$sum = $sum ? $sum : 0;
		tabheader("��˾�ʽ���־ &nbsp;(֧��С��:$sum)",'','',10);
		
		$pagetmp = $page;
		do{
			$query = $db->query("SELECT * $fromsql $wheresql ORDER BY cid DESC LIMIT ".(($pagetmp - 1) * $mrowpp).",$mrowpp");
			$pagetmp--;
		} while(!$db->num_rows($query) && $pagetmp);
	
		$itemstr = '';
		$sn = $pagetmp * $atpp;
		while($r = $db->fetch_array($query)){
			$sn ++;
			$modestr = $r['zj'] < 0 ? '��ȡ' : '����';
			$createdatestr = date("$dateformat $timeformat", $r['createdate']);
			$valuestr = abs($r['zj']);
			$remarkstr = $r['remark'] ? "<a id=\"{$action}_info_$r[cid]\" href=\"?action=$action&deal=remark&cid=$r[cid]\" onclick=\"return showInfo(this.id,this.href)\">�鿴</a>" : '-';
			$itemstr .= "<tr><td class=\"item\">$sn</td>\n".
				"<td class=\"item2\">$r[tomname]</td>\n".
				"<td class=\"item\">$modestr</td>\n".
				"<td class=\"item2\">$valuestr</td>\n".
				"<td class=\"item\">$createdatestr</td>\n".
				"<td class=\"item\">$remarkstr</td>\n".
				"</tr>\n";
		}
		trcategory(array('���',array('������','left'),'��ʽ',array('����','left'),'��������','��ע'));
		echo $itemstr;
		tabfooter();
		echo multi($db->result_one("SELECT count(*) $fromsql $wheresql"),$mrowpp,$page, "?action=$action$filterstr");
	}else{
		$page = !empty($page) ? max(1, intval($page)) : 1;
		submitcheck('bfilter') && $page = 1;
		$mode = isset($mode) ? $mode : -1;
		$indays = empty($indays) ? 0 : max(0,intval($indays));
		$outdays = empty($outdays) ? 0 : max(0,intval($outdays));
		$wheresql = "WHERE tomid='$memberid'";
		$fromsql = "FROM {$tblprefix}$commu[tbl]";
		
		if($mode != -1) $wheresql .= " AND zj".($mode ? '<' : '>')."0";
		$indays && $wheresql .= " AND createdate>'".($timestamp - 86400 * $indays)."'";
		$outdays && $wheresql .= " AND createdate<'".($timestamp - 86400 * $outdays)."'";
		$filterstr = '';
		foreach(array('indays','outdays',) as $k) $$k && $filterstr .= "&$k=".rawurlencode(stripslashes($$k));
		foreach(array('mode',) as $k) $$k != -1 && $filterstr .= "&$k=".$$k;
	
		echo form_str($action.'newform',"?action=$action&page=$page");
		tabheader_e();
		echo "<tr><td class=\"item2\">";
		echo "<select style=\"vertical-align: middle;\" name=\"mode\">".makeoption(array('-1' => '���޷�ʽ','0' => '����','1' => '��ȡ',),$mode)."</select>&nbsp; ";
		echo "<input class=\"text\" name=\"outdays\" type=\"text\" value=\"$outdays\" size=\"4\" style=\"vertical-align: middle;\">��ǰ&nbsp; ";
		echo "<input class=\"text\" name=\"indays\" type=\"text\" value=\"$indays\" size=\"4\" style=\"vertical-align: middle;\">����&nbsp; ";
		echo strbutton('bfilter','ɸѡ');
		echo "</td></tr>";
		tabfooter();
		//�б���
		$sum = $db->result_one("SELECT SUM(zj) $fromsql $wheresql");
		$sum = $sum ? $sum : 0;
		tabheader("��˾�ʽ���־ &nbsp;(����С��:$sum)",'','',10);
		
		$pagetmp = $page;
		do{
			$query = $db->query("SELECT * $fromsql $wheresql ORDER BY cid DESC LIMIT ".(($pagetmp - 1) * $mrowpp).",$mrowpp");
			$pagetmp--;
		} while(!$db->num_rows($query) && $pagetmp);
	
		$itemstr = '';
		$sn = $pagetmp * $atpp;
		while($r = $db->fetch_array($query)){
			$sn ++;
			$modestr = $r['zj'] < 0 ? '��ȡ' : '����';
			$createdatestr = date("$dateformat $timeformat", $r['createdate']);
			$valuestr = abs($r['zj']);
			$remarkstr = $r['remark'] ? "<a id=\"{$action}_info_$r[cid]\" href=\"?action=$action&deal=remark&cid=$r[cid]\" onclick=\"return showInfo(this.id,this.href)\">�鿴</a>" : '-';
			$itemstr .= "<tr><td class=\"item\">$sn</td>\n".
				"<td class=\"item2\">$r[mname]</td>\n".
				"<td class=\"item\">$modestr</td>\n".
				"<td class=\"item2\">$valuestr</td>\n".
				"<td class=\"item\">$createdatestr</td>\n".
				"<td class=\"item\">$remarkstr</td>\n".
				"</tr>\n";
		}
		trcategory(array('���',array('���͹�˾','left'),'��ʽ',array('����','left'),'��������','��ע'));
		echo $itemstr;
		tabfooter();
		echo multi($db->result_one("SELECT count(*) $fromsql $wheresql"),$mrowpp,$page, "?action=$action$filterstr");
	}
}elseif($deal == 'fp'){//����
	if($curuser->info['mchid'] != $tchid) cls_message::show('����ע��Ϊ��˾��Ա��');
	if(!($mid = empty($mid) ? 0 : max(0,intval($mid)))) cls_message::show('��ָ���˴���Ĺ�˾�����ˡ�');
	$curuser->info['currency0'] || cls_message::show("���ֽ��ʻ������Ϊ0��&nbsp; &nbsp;<a href=\"?action=payonline\" target=\"_blank\">>>����֧��</a>");
	$au = new cls_userinfo;
	$au->activeuser($mid);
	if(!$au->info['mid'] || $au->info["pid$arid"] != $memberid || !$au->info["incheck$arid"]) cls_message::show('��ָ���˴���Ĺ�˾�����ˡ�');
	if(!submitcheck('bsubmit')){
		tabheader("��˾�ڲ��ʽ�-{$curuser->info['cmane']}","newform","?action=$action&deal=$deal&mid=$mid",2,0,1);
		trbasic('��˾�ֽ��ʻ�','',$curuser->info['currency0']." &nbsp; &nbsp;<a href=\"?action=payonline\" target=\"_blank\">>>����֧��</a>",'');
		trbasic("{$au->info['mname']}�ֽ��ʻ�",'',$au->info['currency0'],'');
		trbasic("��{$au->info['mname']}�����ʽ�",'fmdata[zj]','','text',array('validate' => " rule=\"int\" min=\"1\" max=\"{$curuser->info['currency0']}\"",'w' => 10,));
		trbasic('����˵��','fmdata[remark]','','textarea');
		tabfooter('bsubmit','����');
	}else{
		if(!($fmdata['zj'] = max(0,intval($fmdata['zj'])))) cls_message::show('����������');
		$fmdata['zj'] = min($fmdata['zj'],$curuser->info['currency0']);
		$fmdata['remark'] = empty($fmdata['remark']) ? '' : trim(strip_tags($fmdata['remark']));
		$curuser->updatecrids(array(0 => -$fmdata['zj']),1,"��˾��{$au->info['mname']}�����ʽ�",2);
		$au->updatecrids(array(0 => $fmdata['zj']),1,'��˾������ʽ�',2);
		$zj = $fmdata['zj'];
		$db->query("INSERT INTO {$tblprefix}$commu[tbl] SET tomid='$mid',tomname='{$au->info['mname']}',mid='$memberid',mname='{$curuser->info['mname']}',createdate='$timestamp',zj='$zj',remark='$fmdata[remark]'");
		cls_message::show('�ʽ����ɹ���',axaction(6,"?action=agents"));
	}
}elseif($deal == 'cq'){//��ȡ
	if($curuser->info['mchid'] != $tchid) cls_message::show('����ע��Ϊ��˾��Ա��');
	if(!($mid = empty($mid) ? 0 : max(0,intval($mid)))) cls_message::show('��ָ���˴���Ĺ�˾�����ˡ�');
	$au = new cls_userinfo;
	$au->activeuser($mid);
	if(!$au->info['mid'] || $au->info["pid$arid"] != $memberid || !$au->info["incheck$arid"]) cls_message::show('��ָ���˴���Ĺ�˾�����ˡ�');
	if(!submitcheck('bsubmit')){
		tabheader("��˾�ڲ��ʽ�-{$curuser->info['cmane']}","newform","?action=$action&deal=$deal&mid=$mid",2,0,1);
		trbasic('��˾�ֽ��ʻ�','',$curuser->info['currency0']." &nbsp; &nbsp;<a href=\"?action=payonline\" target=\"_blank\">>>����֧��</a>",'');
		trbasic("{$au->info['mname']}�ֽ��ʻ�",'',$au->info['currency0'],'');
		trbasic("��ȡ�ʽ𵽹�˾",'fmdata[zj]','','text',array('validate' => " rule=\"int\" min=\"1\" max=\"{$au->info['currency0']}\"",'w' => 10,));
		trbasic('����˵��','fmdata[remark]','','textarea');
		tabfooter('bsubmit','����');
	}else{
		if(!($fmdata['zj'] = max(0,intval($fmdata['zj'])))) cls_message::show('��������ȡ��');
		$fmdata['zj'] = min($fmdata['zj'],$au->info['currency0']);
		$fmdata['remark'] = empty($fmdata['remark']) ? '' : trim(strip_tags($fmdata['remark']));
		$au->updatecrids(array(0 => -$fmdata['zj']),1,'��˾��ȡ���ʽ�',2);
		$curuser->updatecrids(array(0 => $fmdata['zj']),1,"��˾��{$au->info['mname']}��ȡ�ʽ�",2);
		$zj = -$fmdata['zj'];
		$db->query("INSERT INTO {$tblprefix}$commu[tbl] SET tomid='$mid',tomname='{$au->info['mname']}',mid='$memberid',mname='{$curuser->info['mname']}',createdate='$timestamp',zj='$zj',remark='$fmdata[remark]'");
		cls_message::show('�ʽ���ȡ�ɹ���',axaction(6,"?action=agents"));
	}
}elseif($deal == 'remark' && $cid){
	!($remark = $db->result_one("SELECT remark FROM {$tblprefix}$commu[tbl] WHERE cid='$cid' AND ".($curuser->info['mchid'] == $tchid ? 'mid' : 'tomid')."='$memberid'")) && cls_message::show('��ָ����˾�ʽ�֧���¼��');
	tabheader('��˾�ʽ�֧�䱸ע');
	trbasic('��ע˵��','',$remark,'textarea');
	tabfooter();
}
?>
