<?php
/*
** ���ű����ڻ�Ա�������
*/
include_once dirname(dirname(__FILE__)).'/include/general.inc.php';
include_once M_ROOT.'include/adminm.fun.php';
$forward = mhtmlspecialchars(empty($forward) ? M_REFERER : $forward);
$action = mhtmlspecialchars(empty($action) ? 'sendemail' : $action);

if($action == 'sendemail'){//���·��ͻ�Ա����ĵ����ʼ�
	_header();
	$mname = empty($mname) ? '' : $mname;
	$email = empty($email) || !cls_string::isEmail($email) ? '' : $email;
	if(empty($mname) || empty($email) || !cls_string::isEmail($email)){//�ֶ�������Ϸ��ͼ����ʼ�
		tabheader('�ط������ʼ�','newemail',"?action=$action&forward=$forward",2,0,1);
		trbasic('��Ա����','mname',$mname);
		trbasic('�ʼ���ַ','email',$email);//�����Ƿ�ע������
		tabfooter('bsubmit');
		mexit();
	}else{
		if(!($info = $db->fetch_one("SELECT mid,mname FROM {$tblprefix}members WHERE mname='$mname' AND checked=2"))) cls_message::show('ָ����Ա�����ڻ���Ҫ�ʼ�����');
		cls_userinfo::SendActiveEmail(array('mid' => $info['mid'],'mname' => $info['mname'],'email' => $email));
		cls_message::show('��Ա�����ʼ��ѷ��͵��������䣬��������伤��', $forward);
	}
}elseif($action == 'emailactive'){//ͨ������ʼ��е�url������ע��Ļ�Ա�ļ����
	_header();
	if(!($mid = max(0,intval(@$mid))) || !($confirmid = trim(@$confirmid))) cls_message::show('��Ч����');
	if(!($info = $db->fetch_one("SELECT m.mid,s.confirmstr FROM {$tblprefix}members m,{$tblprefix}members_sub s WHERE m.mid='$mid' AND s.mid='$mid' AND m.checked=2"))) cls_message::show('ָ����Ա�����ڻ���Ҫ�ʼ�����');
	if(!$info['confirmstr']) cls_message::show('����ȷ���벻��ȷ');
	
	list($_dateline,$_type,$_confirmid) = explode("\t", $info['confirmstr']);
	if($_type == 2 && $_confirmid == $confirmid){
		$db->query("UPDATE {$tblprefix}members SET checked=1 WHERE mid='$mid'");
		$db->query("UPDATE {$tblprefix}members_sub SET confirmstr='' WHERE mid='$mid'");
		cls_message::show('��Ա����ɹ�',$cms_abs);
	}else cls_message::show('����ȷ���벻��ȷ');
	
}elseif($action == 'memcert'){//������֤
	_header();
	$memcerts = cls_cache::Read('memcerts');
	(empty($crid) || empty($confirm) || !($record = $db->fetch_one("SELECT mcid,certdata FROM {$tblprefix}mcrecords WHERE crid='$crid' AND checktime=0 LIMIT 0,1"))) && cls_message::show('��Ч������');
	$certdata = unserialize($record['certdata']);
	if(!($k = $memcerts[0][$record['mcid']]['email']) || $certdata['codes'][$k]['v'] != $confirm || $certdata['codes'][$k]['e'] >= 3){
		$k && $certdata['codes'][$k]['e']++ < 3 && $db->query("UPDATE {$tblprefix}mcrecords SET certdata='".addslashes(serialize($certdata))."' WHERE crid=$crid");
		cls_message::show($k && $certdata['codes'][$k]['e'] >= 3 ? '�������̫��' : '��Ч������');
	}else{
		if(empty($certdata['flags'][$k])){
			$certdata['flags'][$k] = 1;
			$db->query("UPDATE {$tblprefix}mcrecords SET certdata='".addslashes(serialize($certdata))."' WHERE crid=$crid");
		}
		cls_message::show('������֤�ɹ�');
	}
}