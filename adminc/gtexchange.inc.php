<?php
!defined('M_COM') && exit('No Permission');
$currencys = cls_cache::Read('currencys');
$grouptypes = cls_cache::Read('grouptypes');

$mchid = $curuser->info['mchid'];
$cashgtids = array();
foreach($grouptypes as $k => $v){
	if($v['mode'] == 3 && !in_array($mchid,explode(',',$v['mchids']))){
		if(empty($gtid) || $gtid == $k) $cashgtids[$k] = $v;
	}
}
empty($cashgtids) && cls_message::show('���������Ч�Ļ��ֶһ���Ա��');
if(!submitcheck('bgtexchange')){
	foreach($cashgtids as $k => $v){
		$usergroups = cls_cache::Read('usergroups',$k);
		$ugidsarr = array();
		foreach($usergroups as $x => $y){
			if(in_array($mchid,explode(',',$y['mchids']))){
				$ugidsarr[$x] = $y['cname'].'('.$y['currency'].')';
				if($x == $curuser->info['grouptype'.$k]){
					if(!$curuser->info['grouptype'.$k.'date']) unset($ugidsarr[$x]);
					break;
				}
			}
		}
		$crname = empty($v['crid']) ? '�ֽ�': $currencys[$v['crid']]['cname'];
		tabheader('ʹ�� '.$crname.' ���� '.$v['cname'].' �еĻ�Ա��','gtexchagne'.$k,"?action=gtexchange&gtid=$k");
		trbasic('��ӵ�е� '.$crname.' ����Ϊ','',$curuser->info['currency'.$v['crid']],'');
		trbasic('�������Ļ�Ա����','',$curuser->info['grouptype'.$k] ? $usergroups[$curuser->info['grouptype'.$k]]['cname'] : '-','');
		trbasic('��ǰ��Ա���������','',$curuser->info['grouptype'.$k.'date'] ? date($dateformat,$curuser->info['grouptype'.$k.'date']) : '-','');
		$ugidsarr && trbasic('��ѡ��Ҫ�������','exchangeugid',makeoption($ugidsarr),'select');
		$ugidsarr ? tabfooter('bgtexchange','�һ�') : tabfooter();
	}
}else{
	(empty($gtid) || empty($grouptypes[$gtid]) || in_array($mchid,explode(',',$grouptypes[$gtid]['mchids']))) && cls_message::show('��ָ����Ա����ϵ',M_REFERER);
	$grouptype = $grouptypes[$gtid];
	$crid = $grouptype['crid']; 
	$usergroups = cls_cache::Read('usergroups',$gtid);
	(empty($exchangeugid) || empty($usergroups[$exchangeugid]) || !in_array($mchid,explode(',',$usergroups[$exchangeugid]['mchids']))) && cls_message::show('��ָ����Ա��',M_REFERER);
	$curuser->info['currency'.$crid] < $usergroups[$exchangeugid]['currency'] && cls_message::show('û���㹻����',M_REFERER);
	$usergroup = cls_cache::Read('usergroup',$gtid,$exchangeugid);
	if($curuser->info['grouptype'.$gtid] == $exchangeugid){//����
		if($usergroup['limitday'] && $curuser->info['grouptype'.$gtid.'date']){
			$curuser->updatefield('grouptype'.$gtid.'date',$curuser->info['grouptype'.$gtid.'date'] + $usergroup['limitday'] * 86400);
		}else{
			$curuser->updatefield('grouptype'.$gtid.'date',0);
		}
	}else{//���
		$curuser->updatefield('grouptype'.$gtid,$exchangeugid);
		if($usergroup['limitday']){
			$curuser->updatefield('grouptype'.$gtid.'date',$timestamp + $usergroup['limitday'] * 86400);
		}else{
			$curuser->updatefield('grouptype'.$gtid.'date',0);
		}
	}
	$curuser->updatecrids(array($crid => -$usergroup['currency']),1,'���ֶһ���Ա��');
	cls_message::show('���ֶһ���Ա�����',M_REFERER);
}
?>
