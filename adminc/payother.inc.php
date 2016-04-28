<?php
!defined('M_COM') && exit('No Permission');
$currencys = cls_cache::Read('currencys');
backnav('payonline','other');
if($curuser->getTrusteeshipInfo()) cls_message::show('���Ǵ����û�����ǰ������ԭ�û�������Ȩ�ޣ�');
if(!submitcheck('bpayother')){
	if(!$oldmsg = $db->fetch_one("SELECT * FROM {$tblprefix}pays WHERE mid='$memberid' ORDER BY pid DESC LIMIT 0,1")) $oldmsg = array();
	$pmodearr = array('0' => '����֧��','2' => '����ת��','3' => '�ʾֻ��');
	tabheader("�ֽ�֧����Ϣ֪ͨ����Ա",'payother','?action=payother',2,1,1);
	trbasic('֧����ʽ','',makeradio('paynew[pmode]',$pmodearr),'');
	trbasic('֧�����(Ԫ)','paynew[amount]', '', 'text', array('validate' => makesubmitstr('paynew[amount]',1,'number',0,15)));
	trbasic('��ϵ������','paynew[truename]',empty($oldmsg['truename']) ? '' : $oldmsg['truename'],'text', array('validate' => makesubmitstr('paynew[truename]',0,0,0,80),'w'=>50));
	trbasic('��ϵ�绰','paynew[telephone]',empty($oldmsg['telephone']) ? '' : $oldmsg['telephone'],'text', array('validate' => makesubmitstr('paynew[telephone]',0,0,0,30),'w'=>50));
	trbasic('��ϵEmail','paynew[email]',empty($oldmsg['email']) ? '' : $oldmsg['email'],'text', array('validate' => makesubmitstr('paynew[email]',0,'email',0,100),'w'=>50));
	trbasic('��ע','paynew[remark]',empty($oldmsg['remark']) ? '' : $oldmsg['remark'],'textarea', array('validate' => makesubmitstr('paynew[remark]',0,0,0,200)));
	trspecial('֧��ƾ֤',specialarr(array('type' => 'image','varname' => 'paynew[warrant]','value' => '',)));
	tr_regcode('payonline');
	tabfooter('bpayother');
	m_guide("pay_notes",'fix');
}else{
	if(!regcode_pass('payonline',empty($regcode) ? '' : trim($regcode))) cls_message::show('��֤���������','?action=payother');
	$paynew['amount'] = max(0,round(floatval($paynew['amount']),2));
	empty($paynew['amount']) && cls_message::show('������֧������','?action=payother');
	$paynew['truename'] = trim(strip_tags($paynew['truename']));
	$paynew['telephone'] = trim(strip_tags($paynew['telephone']));
	$paynew['email'] = trim(strip_tags($paynew['email']));
	$c_upload = cls_upload::OneInstance();
	$paynew['warrant'] = upload_s($paynew['warrant'],'','image');
	$c_upload->saveuptotal(1);
	$db->query("INSERT INTO {$tblprefix}pays SET
				 mid='".$memberid."',
				 mname='".$curuser->info['mname']."',
				 pmode='$paynew[pmode]',
				 amount='$paynew[amount]',
				 truename='$paynew[truename]',
				 telephone='$paynew[telephone]',
				 email='$paynew[email]',
				 remark='$paynew[remark]',
				 warrant='$paynew[warrant]',
				 senddate='$timestamp',
				 ip='$onlineip'
				 ");
	$c_upload->closure(1, $db->insert_id(), 'pays');
	cls_message::show('�ֽ��ֵ֪ͨ���ͳɹ�,��ȴ�����Ա����','?action=pays');
}
?>
