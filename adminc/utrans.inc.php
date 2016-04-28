<?php
!defined('M_COM') && exit('No Permission');
foreach(array('mchannels','uprojects','grouptypes',) as $k) $$k = cls_cache::Read($k);
if(!isset($utran['toid'])){
	$notranspro = true;
	foreach($grouptypes as $gtid => $grouptype){
		if(!$grouptype['issystem'] && $grouptype['mode'] == 1){
			$toidsarr = array();
			$usergroups = cls_cache::Read('usergroups',$gtid);
			foreach($uprojects as $k => $v){
				if(($v['sugid'] == $curuser->info["grouptype$gtid"]) && ($v['gtid'] == $gtid)){
					if($v['tugid'] && empty($usergroups[$v['tugid']])) continue;
					$toidsarr[$v['tugid']] = $v['tugid'] ? $usergroups[$v['tugid']]['cname'] : '�����Ա';
				}
			}
			if($toidsarr){
				$isold = $db->result_one("SELECT COUNT(*) FROM {$tblprefix}utrans WHERE mid='$memberid' AND checked='0' AND gtid='$gtid'");
				$nowugstr = '&nbsp; '.'��������'.'&nbsp;:&nbsp;'.($curuser->info["grouptype$gtid"] ? $usergroups[$curuser->info["grouptype$gtid"]]['cname'] : '�����Ա');
				tabheader("[$grouptype[cname]]�������$nowugstr","utrans$gtid","?action=utrans");
				trhidden('gtid',$gtid);
				trbasic('���Ŀ���Ա��','utran[toid]',makeoption($toidsarr),'select');
				tabfooter('bsubmit', $isold ? '�޸�' : '����');
				$notranspro = false;
			}
		}
	}	
	$notranspro && cls_message::show('û�������õı��������');
}else{
	if(empty($gtid)) cls_message::show('��ָ����ȷ�Ļ�Ա����ϵ!');
	foreach($uprojects as $k => $v){
		if($v['ename'] == $curuser->info["grouptype$gtid"].'_'.$utran['toid']) $uproject = $v;
	}
	if(empty($uproject)) cls_message::show('����������ָ���Ļ�Ա��!');
	$sugid = $curuser->info["grouptype$gtid"];
	$tugid = $utran['toid'];
	$mchid = $curuser->info['mchid'];
	if(in_array($mchid,explode(',',$grouptypes[$gtid]['mchids']))) cls_message::show('�������Ļ�Աģ�Ͳ�������˻�Ա��!');
	if($tugid && (!($usergroup = cls_cache::Read('usergroup',$gtid,$tugid)) || !in_array($mchid,explode(',',$usergroup['mchids'])))) cls_message::show('�������Ļ�Աģ�Ͳ�������˻�Ա��!');
	//���������и������뻹���µ�����
	$isold = false;
	//����Ҫ�����ϴ�����ʱ�䣬��ע��ظ�����
	if($minfos = $db->fetch_one("SELECT * FROM {$tblprefix}utrans WHERE mid='$memberid' AND checked='0' AND gtid='$gtid'")){
		$isold = true;
	}
	$minfos['fromid'] = $curuser->info["grouptype$gtid"];
	$minfos['toid'] = $utran['toid'];
	if(!submitcheck('butran')){
		$usergroups = cls_cache::Read('usergroups',$gtid);
		$submitstr = '';
		tabheader('��Ա�����뷽ʽ'.'&nbsp; -&nbsp; '.$grouptypes[$gtid]['cname'],'utrans',"?action=utrans",2,1,1);
		trbasic('��Ա�������ʽ','',(!$sugid ? '�����Ա': $usergroups[$sugid]['cname']).'&nbsp; ->&nbsp; '.(!$tugid ? '�����Ա': $usergroups[$tugid]['cname']),'');
		trhidden('utran[toid]',$tugid);
		trhidden('gtid',$gtid);
		trbasic('����ʱ��','',date("Y-m-d H:i",$isold ? $minfos['createdate'] : $timestamp),'');
		trbasic('��ע','utran[remark]',empty($minfos['remark']) ? '' : $minfos['remark'],'textarea');
		$isold && trbasic('����Ա�ظ�'.@noedit(1),'',$minfos['reply'],'textarea');
		tabfooter('butran');
	}else{
		//��Ҫ���һ�£���ǰ��Ա�Ƿ�������뵽�µĻ�Ա��
		$omchid = $curuser->info['mchid'];//ԭģ��
		if($uproject['autocheck']){
			$curuser->updatefield("grouptype$gtid",$tugid);
			$curuser->updatedb();
			if($isold){
				$db->query("UPDATE {$tblprefix}utrans SET toid='$tugid',fromid='$sugid',remark='',reply='',checked='1' WHERE mid='$memberid' AND checked='0' AND gtid='$gtid'");
			}else{
				$db->query("INSERT INTO {$tblprefix}utrans SET mid='$memberid',mname='".$curuser->info['mname']."',gtid='$gtid',toid='$tugid',fromid='$sugid',remark='',checked='1',createdate='$timestamp'");
			}
		}else{
			$utran['remark'] = trim($utran['remark']);
			if($isold){
				$db->query("UPDATE {$tblprefix}utrans SET toid='$tugid',fromid='$sugid',remark='$utran[remark]' WHERE mid='$memberid' AND checked='0' AND gtid='$gtid'");
			}else{
				$db->query("INSERT INTO {$tblprefix}utrans SET mid='$memberid',mname='".$curuser->info['mname']."',gtid='$gtid',toid='$tugid',fromid='$sugid',remark='$utran[remark]',checked='0',createdate='$timestamp'");
			}
		}
		cls_message::show($uproject['autocheck'] ? '��Ա�����óɹ�' : '��ȴ�����Ա��ˣ�',"?action=utrans");
	}
}
?>
