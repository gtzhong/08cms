<?
(!defined('M_COM') || !defined('M_ADMIN')) && exit('No Permission');
aheader();
if($re = $curuser->NoBackFunc('utrans')) cls_message::show($re);
foreach(array('mchannels','uprojects','grouptypes','currencys',) as $k) $$k = cls_cache::Read($k);
if($action == 'utransedit'){
	$page = !empty($page) ? max(1, intval($page)) : 1;
	submitcheck('bfilter') && $page = 1;
	$checked = isset($checked) ? $checked : '-1';
	$keyword = empty($keyword) ? '' : $keyword;

	$wheresql = '';
	$checked != '-1' && $wheresql .= ($wheresql ? " AND " : "")."checked='$checked'";
	$keyword && $wheresql .= ($wheresql ? " AND " : "")."mname ".sqlkw($keyword);

	$filterstr = '';
	foreach(array('checked','keyword',) as $k) $filterstr .= "&$k=".urlencode($$k);
	$wheresql = $wheresql ? "WHERE ".$wheresql : "";

	if(!submitcheck('butransedit')){
		echo form_str($actionid.'utransedit',"?entry=utrans&action=utransedit&page=$page");
		tabheader_e();
		echo "<tr><td colspan=\"2\" class=\"txt txtleft\">";
		echo '�ؼ���'."&nbsp; <input class=\"text\" name=\"keyword\" type=\"text\" value=\"$keyword\" size=\"8\" style=\"vertical-align: middle;\">&nbsp; ";
		$checkedarr = array('-1' => '������״̬','0' => 'δ����','1' => '������');
		echo "<select style=\"vertical-align: middle;\" name=\"checked\">".makeoption($checkedarr,$checked)."</select>&nbsp; ";
		echo "<input class=\"btn\" type=\"submit\" name=\"bfilter\" id=\"bfilter\" value=\"ɸѡ\">";
		echo "</td></tr>";
		tabfooter();

		$pagetmp = $page;
		do{
			$query = $db->query("SELECT * FROM {$tblprefix}utrans $wheresql ORDER BY trid DESC LIMIT ".(($pagetmp - 1) * $atpp).",$atpp");
			$pagetmp--;
		} while(!$db->num_rows($query) && $pagetmp);
		$itemstr = '';
		while($row = $db->fetch_array($query)){
			$usergroups = cls_cache::Read('usergroups',$row['gtid']);
			$createdatestr = date("$dateformat", $row['createdate']);
			$checkstr = $row['checked'] ? 'Y' : "<input class=\"checkbox\" type=\"checkbox\" name=\"checkid[$row[trid]]\" value=\"$row[trid]\">";
			$detailstr = $row['checked'] ? '-' : "<a href=\"?entry=utrans&action=utrandetail&trid=$row[trid]\" onclick=\"return floatwin('open_transdetail',this)\">����</a>";
			$itemstr .= "<tr class=\"txt\">\n".
			"<td class=\"txtL\">$row[mname]</td>\n".
			"<td class=\"txtC\">".($row['fromid'] ? $usergroups[$row['fromid']]['cname'] : '�����Ա')."</td>\n".
			"<td class=\"txtC\">".($row['toid'] ? $usergroups[$row['toid']]['cname'] : '�����Ա')."</td>\n".
			"<td class=\"txtC w50\">$checkstr</td>\n".
			"<td class=\"txtC w80\">$createdatestr</td>\n".
			"<td class=\"txtC w50\"><input class=\"checkbox\" type=\"checkbox\" name=\"delete[$row[trid]]\" value=\"$row[trid]\" onclick=\"deltip()\"></td>\n".
			"<td class=\"txtC w30\">$detailstr</td>\n".
			"</tr>\n";
		}
		$counts = $db->result_one("SELECT count(*) FROM {$tblprefix}utrans $wheresql");
		$multi = multi($counts,$atpp,$page,"?entry=utrans&action=utransedit$filterstr");

		tabheader('��Ա�����б�','','',8);
		trcategory(array(
		'��Ա����','��Դ��Ա��','Ŀ���Ա��',"<input class=\"checkbox\" type=\"checkbox\" name=\"chkcheck\" onclick=\"checkall(this.form,'checkid','chkcheck')\">".'���','�������',"<input class=\"checkbox\" type=\"checkbox\" name=\"chkdel\" onclick=\"deltip(this,0,checkall,this.form,'delete','chkdel')\">ɾ?",'����'));
		echo $itemstr;
		tabfooter();
		echo $multi;
		echo "<input class=\"button\" type=\"submit\" name=\"butransedit\" value=\"�ύ\">";
	}else{
		if(empty($delete) && empty($checkid)) cls_message::show('��ѡ������¼',"?entry=utrans&action=utransedit&page=$page$filterstr");
		if(!empty($delete)){
			$db->query("DELETE FROM {$tblprefix}utrans WHERE trid ".multi_str($delete));
		}
		if(!empty($checkid)){
			$actuser = new cls_userinfo;
			foreach($checkid as $trid){
				if(empty($delete) || !in_array($trid,$delete)){
					if($minfos = $db->fetch_one("SELECT * FROM {$tblprefix}utrans WHERE trid='$trid' AND checked='0'")){
						$actuser->activeuser($minfos['mid']);
						$gtid = $minfos['gtid'];
						$tugid = $minfos['toid'];
						$mchid = $actuser->info['mchid'];
						if(in_array($mchid,explode(',',$grouptypes[$gtid]['mchids']))) continue;
						if($tugid && (!($usergroup = cls_cache::Read('usergroup',$gtid,$tugid)) || !in_array($mchid,explode(',',$usergroup['mchids'])))) continue;
						$actuser->updatefield("grouptype$gtid",$tugid);
						$actuser->updatedb();
						$db->query("UPDATE {$tblprefix}utrans SET remark='',reply='',checked='1' WHERE trid='$trid'");
						$actuser->init();
					}
				}
			}
			unset($actuser);
		}
		adminlog('��Ա��������','��Ա�����б�������');
		cls_message::show('��Ա�����������',"?entry=utrans&action=utransedit&page=$page$filterstr");
	
	}
}elseif($action == 'utrandetail' && $trid){
	if(!($minfos = $db->fetch_one("SELECT * FROM {$tblprefix}utrans WHERE trid='$trid'"))) cls_message::show('��ָ����ȷ�ı����¼');
	$gtid = $minfos['gtid'];
	$sugid = $minfos['fromid'];
	$tugid = $minfos['toid'];
	if(!submitcheck('butrandetail')){
		$submitstr = '';
		$usergroups = cls_cache::Read('usergroups',$gtid);
		tabheader('��Ա������ѡ��','utrans',"?entry=utrans&action=utrandetail&trid=$trid$forwardstr",2,1,1);
		trbasic('��Ա����','',$minfos['mname'],'');
		trbasic('�����Ա����ϵ','',$grouptypes[$gtid]['cname'],'');
		trbasic('��Ա������ʽ','',(!$sugid ? '�����Ա': $usergroups[$sugid]['cname']).'&nbsp; ->&nbsp; '.(!$tugid ? '�����Ա': $usergroups[$tugid]['cname']),'');
		trbasic('�������ʱ��','',date("Y-m-d H:m",$minfos['createdate']),'');
		trbasic('�����ע','utran[remark]',$minfos['remark'],'textarea');
		trbasic('����Ա�ظ�','utran[reply]',$minfos['reply'],'textarea');
		tabfooter('butrandetail');
		a_guide('utrandetail');
	}else{
		$utran['remark'] = trim($utran['remark']);
		$utran['reply'] = trim($utran['reply']);
		$db->query("UPDATE {$tblprefix}utrans SET remark='$utran[remark]',reply='$utran[reply]' WHERE trid='$trid'");
		adminlog('�޸Ļ�Ա����','��Ա���������޸Ĳ���');
		cls_message::show('��Ա������¼�޸����',axaction(6,M_REFERER));
	}
}
?>
