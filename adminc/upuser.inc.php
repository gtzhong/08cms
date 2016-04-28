<?php
!defined('M_COM') && exit('No Permission');

$currencys = cls_cache::Read('currencys');
$mctypes = cls_cache::Read('mctypes');

$type = empty($type) ? 'mchid2' : $type;
$mchid = str_replace("mchid","",$type); 
$ochid = $curuser->info['mchid'];
$atype = array(
	'2'=>'������',
	'3'=>'���͹�˾',
	'11'=>'װ�޹�˾',
	'12'=>'Ʒ���̼�'
);
$lnktype = ''; $msgnow = '';
foreach($atype as $k=>$v){
	if($ochid == $k) cls_message::show("���Ѿ���$v, ��������");
	$lnktype .= " | <a href='?action=upuser&type=mchid$k'>$v</a>";
	if($type=="mchid$k") $msgnow = $v;
}

tabheader("<div style='width:420px; float:right'>�л�����:$lnktype</div>����Ϊ$msgnow",'','');
trbasic('˵����','','��ͨ��Ա������תΪ �����ˣ�װ�޹�˾��Ʒ���̼ң�������Ա�������໥ת�����������л��������͡�','');
tabfooter();

$mchannel = cls_cache::Read('mchannel',$mchid);
$mfields = cls_cache::Read('mfields',$mchid);
// �ʴ�ר��-�����ֶ�
$mfexp = array('dantu','ming','danwei','quaere','blacklist');
foreach($mfexp as $k){//��̨�ܹ��ֶ�
	unset($mfields[$k]);
}
// �ų���Ա��֤�ֶ�
foreach($mctypes as $k => $v){
	if(strstr(",$v[mchids],",",$mchid,")){ //����Ļ�Աģ��
		unset($mfields[$v['field']]);
	}
}

$autocheck = $mchannel['autocheck'] == 1 ? 1 : 0;
$row = $db->fetch_one("SELECT * FROM {$tblprefix}mtrans WHERE mid='$memberid' AND checked='0'");
//�ж�
if(empty($row['contentarr'])){
	$contentarr = array();
	$createdate = $timestamp;
}else{
	$contentarr = unserialize($row['contentarr']);
	$createdate = $row['createdate'];
}
$contentarr = empty($row['contentarr']) ? array() : unserialize($row['contentarr']);
unset($row['contentarr']);

$curuser = cls_UserMain::CurUser(); 
$lxdh = $curuser->info['lxdh'];

if(!submitcheck('bsubmit')){
	tabheader("����˵��",$action,"?action=$action",2,1,1);
	trbasic('����ʱ��','',date("Y-m-d H:m",$createdate),'');
	trbasic('����˵��','fmdata[remark]',empty($row['remark']) ? '' : $row['remark'],'textarea');
	empty($row['reply']) || trbasic('����Ա�ظ�','',$row['reply'],'textarea',array('guide' => '���ɸ���'));
	$autocheck && trbasic('����˵��','','<font color="#2255DD">�ύ���Զ�����Ϊ'.$msgnow."</font>",'');
	tabfooter();
	
	tabheader('��ϸ����');
	$a_field = new cls_field;

	foreach($mfields as $k => $field){
		if(!$field['issystem']){
			empty($contentarr[$k]) || $contentarr[$k] = stripslashes($contentarr[$k]);
			$a_field->init($field,empty($contentarr[$k]) ? (empty($curuser->info[$k]) ? '' : $curuser->info[$k]) : $contentarr[$k]);
			$a_field->trfield('fddata');
		}
	}

	trhidden('type',$type);
	tabfooter('bsubmit');
	echo "<script type='text/javascript'>_08cms_validator.init('ajax','fmdata[lxdh]',{url:'{$cms_abs}"._08_Http_Request::uri2MVC("ajax=checkUserPhone&old=$lxdh&val=%1")."'});</script>";

}else{
	$c_upload = new cls_upload;	
	$a_field = new cls_field;
	foreach($mfields as $k => $v){
		if(!$v['issystem'] && isset($fddata[$k])){
			empty($contentarr[$k]) || $contentarr[$k] = stripslashes($contentarr[$k]);
			$a_field->init($v,empty($contentarr[$k]) ? (empty($curuser->info[$k]) ? '' : $curuser->info[$k]) : $contentarr[$k]);
			$fddata[$k] = $a_field->deal('fddata','mcmessage',M_REFERER);
			if($autocheck){
				@$curuser->updatefield($k,$fddata[$k],$v['tbl']);
				if($arr = multi_val_arr($fddata[$k],$v)) foreach($arr as $x => $y) $curuser->updatefield($k.'_'.$x,$y,$v['tbl']);
			}
		}
	}
	unset($a_field);
	
	if($autocheck){
		$db->query("DELETE FROM {$tblprefix}members_$ochid WHERE mid='$memberid'");
		$db->query("INSERT INTO {$tblprefix}members_$mchid SET mid='$memberid'");
		if($mchid == 2){//�������ͨ��Ա����Ϊ�����ˣ�����ַ������ⷿԴ�ж�Ӧ��mid��mchid�ֶ�ҲҪ�޸ĳ�2
			$db->query("UPDATE {$tblprefix}".atbl(2)." SET mchid = '2' WHERE mid = '$memberid'");
			$db->query("UPDATE {$tblprefix}".atbl(3)." SET mchid = '2' WHERE mid = '$memberid'");
		}
		$curuser->updatefield('mchid',$mchid);
		$crids = array();foreach($currencys as $k => $v) $v['available'] && $v['initial'] && $crids[$k] = $v['initial'];
		$crids && $curuser->updatecrids($crids,0,'��Աע���ʼ���֡�');
		$curuser->updatefield('checked',1);
		$curuser->nogroupbymchid();//ģ�ͱ���Ժ�������Ҫ���鶨��
		$curuser->groupinit();			
		$curuser->updatefield('mtcid',($mtcid = array_shift(array_keys($curuser->mtcidsarr()))) ? $mtcid : 0);
		$curuser->autoletter();
		$curuser->updatedb();
		if($row){
			$db->query("UPDATE {$tblprefix}mtrans SET toid='$mchid',fromid='$ochid',contentarr='',remark='',reply='',checked='1' WHERE mid='$memberid' AND checked='0'");
		}else{
			$db->query("INSERT INTO {$tblprefix}mtrans SET mid='$memberid',mname='".$curuser->info['mname']."',toid='$mchid',fromid='$ochid',contentarr='',remark='',checked='1',createdate='$timestamp'");
		}
	}else{
		$fmdata['remark'] = trim($fmdata['remark']);
		$fddata = empty($fddata) ? '' : addslashes(serialize($fddata));
		if($row){
			$db->query("UPDATE {$tblprefix}mtrans SET fromid='$ochid',toid='$mchid',contentarr='$fddata',remark='$fmdata[remark]' WHERE mid='$memberid' AND checked='0'");
		}else{
			$db->query("INSERT INTO {$tblprefix}mtrans SET mid='$memberid',mname='".$curuser->info['mname']."',fromid='$ochid',toid='$mchid',contentarr='$fddata',remark='$fmdata[remark]',checked='0',createdate='$timestamp'");
		}
	}
	$c_upload->closure(1,$memberid,'members');
	$c_upload->saveuptotal(1);
	$autocheck ? cls_message::show('��Ա����,�����Լ�������ΪVIP��Ա',"?action=gaoji") : cls_message::show('��Ա��������ɹ��ύ����ȴ�����Ա���',M_REFERER);
}

?>

