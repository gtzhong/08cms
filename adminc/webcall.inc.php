<?php
!defined('M_COM') && exit('No Permission');
empty($webcall_enable) && cls_message::show('��������400�绰');
if(empty($webcallpmid) || !$curuser->pmbypmid($webcallpmid)) cls_message::show('��û��400�绰������Ȩ�ޡ�');

$id = empty($id) ? 0 : max(0,intval($id));
$page = !empty($page) ? max(1, intval($page)) : 1;
$wheresql = "WHERE a.mid='$memberid'";
$fromsql = "FROM {$tblprefix}webcall a";
$red_doc = "<font color='red'> * </font>";

if(!isset($option)){
	if(!submitcheck('bsubmit')){
		$id && $wheresql .= " AND a.id='$id'";
		$r = $db->fetch_one("SELECT a.* $fromsql $wheresql");
		empty($r) && cls_message::show('��������400�ֻ�����',axaction(2, "?action=$action&option=apply"));
		tabheader("400�绰����".(empty($webcall_small_admin) ? '' : "&nbsp;>><a target=\"_blank\" href=\"$webcall_small_admin)\" >400�ֻ�����</a>"),'webcalladd',"?action=$action&id=$id",2,1,1);
		switch($r['state']){
			case -1:
				$statestr = 'δͨ��';
				break;
			case 0:
				$statestr = '�����';
				break;
			case 1:
				$statestr = '�����';
				break;
		}
		$createdatestr = $r['createdate'] ? date('Y-m-d',$r['createdate']) : '-';
		$r['state']!=0 && $checkdatestr = $r['checkdate'] ? date('Y-m-d',$r['checkdate']) : '-';

		trhidden('fmdata[id]', $r['id']);
		trhidden('fmdata[state]', $r['state']);
		$r['state']==1 && trbasic('400�绰����','',$webcall_big.'-'.$r['extcode'],'');
		$r['state']==1 && trbasic('��Ѳ���url','',$r['webcallurl'],'');
		trbasic($red_doc.'��ҵ����','fmdata[suppliername]',$r['suppliername'],'text',array('validate' => makesubmitstr('fmdata[suppliername]',1,0,'',20,'text')));
		trbasic($red_doc.'��ҵ��ַ','fmdata[address]',$r['address'],'text',array('validate' => makesubmitstr('fmdata[address]',1,0,'',100,'text')));
		trbasic('�ʱ�','fmdata[postcode]',$r['postcode'],'text',array('validate' => makesubmitstr('fmdata[postcode]',0,'int',6,6,'text')));
		trbasic('�����ʺ�','fmdata[username]',$r['username'],'');
		trbasic($red_doc.'��ϵ��','fmdata[contactman]',$r['contactman'],'text',array('validate' => makesubmitstr('fmdata[contactman]',1,0,'',20,'text')));
		trbasic('�Ա�','',makeradio('fmdata[sex]',array(1 => '��',0 => 'Ů'),$r['sex']),'');
		trbasic($red_doc.'���֤','fmdata[contactidcard]',$r['contactidcard'],'text',array('validate' => makesubmitstr('fmdata[contactidcard]',1,0,'',20,'text','/^([1-9][0-9]{13,16}[0-9A-Z])$/')));
		trbasic($red_doc.'��ϵ�绰','fmdata[phone]',$r['phone'],'text',array('validate' => makesubmitstr('fmdata[phone]',1,0,'',20,'text')));
		trbasic('��ϵ�ֻ�','fmdata[mobilephone]',$r['mobilephone'],'text',array('validate' => makesubmitstr('fmdata[mobilephone]',0,'int',11,11,'text')));
		trbasic($red_doc.'��������','fmdata[contactmail]',$r['contactmail'],'text',array('validate' => makesubmitstr('fmdata[contactmail]',1,0,'',100,'text','/^[\w\-\.]+@[\w\-\.]+(\.\w+)+$/')));
		trbasic('����','fmdata[artiperson]',$r['artiperson'],'text',array('validate' => makesubmitstr('fmdata[artiperson]',0,0,'',50,'text')));
		trbasic('Ӫҵִ�պ���','fmdata[licence]',$r['licence'],'text',array('validate' => makesubmitstr('fmdata[licence]',0,0,'',20,'text')));
		trbasic('˰��ǼǺ�','fmdata[taxnumber]',$r['taxnumber'],'text',array('validate' => makesubmitstr('fmdata[taxnumber]',0,0,'',20,'text')));
		trbasic('״̬','',$statestr,'');
		$r['state']==-1 && trbasic('��ע','',$r['remark'],'');
		trbasic('��������','',$createdatestr,'');
		$r['state']!=0 && trbasic('�������','',$checkdatestr,'');
		$r['state']!=1 && tabfooter('bsubmit','�ύ');
		$r['state']==1 && tabfooter();
	} else {
		//�����޸� ����� �ļ�¼
		$fmdata['state']==1 && cls_message::show('�����޸�״̬Ϊ����˵ļ�¼');

		//!is_numeric($fmdata['postcode']) && cls_message::show('�ʱ����������',axaction(2,M_REFERER));
		//!is_numeric($fmdata['contactidcard']) && cls_message::show('���֤����������',axaction(2,M_REFERER));
		if(!preg_match('/([1-9][0-9]{13,16}[0-9A-Z])/',$fmdata['contactidcard'])) cls_message::show('���֤���벻�Ϸ�',axaction(2,M_REFERER)); 

		$db->query("UPDATE {$tblprefix}webcall SET 
		suppliername='{$fmdata['suppliername']}',
		address='{$fmdata['address']}',
		contactman='{$fmdata['contactman']}',
		phone='{$fmdata['phone']}',
		mobilephone='{$fmdata['mobilephone']}',
		artiperson='{$fmdata['artiperson']}',
		licence='{$fmdata['licence']}',
		taxnumber='{$fmdata['taxnumber']}',
		contactidcard='{$fmdata['contactidcard']}',
		contactmail='{$fmdata['contactmail']}',
		postcode='{$fmdata['postcode']}',
		sex='{$fmdata['sex']}'
		WHERE id='".(int)$fmdata['id']."' AND mid='$memberid'");

		cls_message::show('�޸ĳɹ�',axaction(6,M_REFERER));
	}
} elseif($option=='apply') {
	$isapply = $db->result_one("SELECT 1 FROM {$tblprefix}webcall WHERE mid='$memberid'");

	$isapply==1 && cls_message::show('�Ѿ������400�绰�ֻ�������������',axaction(2,M_REFERER));

	if(!submitcheck('bsubmit')){

		tabheader('400�绰&nbsp; -&nbsp; ����ֻ�','webcalladd',"?action=$action&option=$option",2,1,1);

		trbasic($red_doc.'��ҵ����','fmdata[suppliername]','','text',array('validate' => makesubmitstr('fmdata[suppliername]',1,0,'',20,'text')));
		trbasic($red_doc.'��ҵ��ַ','fmdata[address]','','text',array('validate' => makesubmitstr('fmdata[address]',1,0,'',100,'text')));
		trbasic('�ʱ�','fmdata[postcode]','','text',array('validate' => makesubmitstr('fmdata[postcode]',0,'int',6,6,'text')));
		trbasic($red_doc.'�����ʺ�','fmdata[username]','','text',array('guide'=>'4-20λ(���֣���ĸ���»���)','validate' => makesubmitstr('fmdata[username]',1,0,4,20,'text')));
		trbasic($red_doc.'��������','fmdata[pwd]','','password',array('guide'=>'������μ��ʺ����룬�ɹ������ϵͳ��ɾ��������Ϣ','validate' => makesubmitstr('fmdata[pwd]',1,0,'','','text')));
		trbasic($red_doc.'ȷ������','fmdata[cfmpwd]','','password', array('validate' => ' rule="comp" must="1" vid="fmdata[pwd]"'));

		trbasic($red_doc.'��ϵ��','fmdata[contactman]','','text',array('validate' => makesubmitstr('fmdata[contactman]',1,0,'',20,'text')));
		trbasic('�Ա�','',makeradio('fmdata[sex]',array(1 => '��',0 => 'Ů'),1),'');
		trbasic($red_doc.'���֤','fmdata[contactidcard]','','text',array('validate' => makesubmitstr('fmdata[contactidcard]',1,0,'',20,'text','/^([1-9][0-9]{13,16}[0-9A-Z])$/')));
		trbasic($red_doc.'��ϵ�绰','fmdata[phone]','','text',array('validate' => makesubmitstr('fmdata[phone]',1,0,'',20,'text')));
		trbasic('��ϵ�ֻ�','fmdata[mobilephone]','','text',array('validate' => makesubmitstr('fmdata[mobilephone]',0,'int',11,11,'text')));
		trbasic($red_doc.'��������','fmdata[contactmail]','','text',array('validate' => makesubmitstr('fmdata[contactmail]',1,0,'',100,'text','/^[\w\-\.]+@[\w\-\.]+(\.\w+)+$/')));

		trbasic('����','fmdata[artiperson]','','text',array('validate' => makesubmitstr('fmdata[artiperson]',0,0,'',50,'text')));
		trbasic('Ӫҵִ�պ���','fmdata[licence]','','text',array('validate' => makesubmitstr('fmdata[licence]',0,0,'',20,'text')));
		trbasic('˰��ǼǺ�','fmdata[taxnumber]','','text',array('validate' => makesubmitstr('fmdata[taxnumber]',0,0,'',20,'text')));

		tabfooter('bsubmit','�ύ');

	} else {
		foreach($fmdata as $k=>$v){
			if(in_array($k, array('mobilephone','artiperson','licence','taxnumber','postcode'))) continue;
			empty($v) && cls_message::show('��������д����',axaction(2,M_REFERER));
		}
		//!is_numeric($fmdata['postcode']) && cls_message::show('�ʱ����������',axaction(2,M_REFERER));
		//!is_numeric($fmdata['contactidcard']) && cls_message::show('���֤����������',axaction(2,M_REFERER));
		if(!preg_match('/([1-9][0-9]{13,16}[0-9A-Z])/',$fmdata['contactidcard'])) cls_message::show('���֤���벻�Ϸ�',axaction(2,M_REFERER));
		
		(4>strlen($fmdata['username']) || strlen($fmdata['username'])>20) && cls_message::show('�����ʺű�����4-20λ',axaction(2,M_REFERER));
		($fmdata['pwd']!=$fmdata['cfmpwd']) && cls_message::show('������������벻һ��',axaction(2,M_REFERER));

		$db->query("INSERT INTO {$tblprefix}webcall (mid,mname,suppliername,address,username,pwd,contactman,phone,mobilephone,artiperson,licence,taxnumber,contactidcard,contactmail,postcode,sex,createdate,state) 
		VALUES ('$memberid','{$curuser->info['mname']}','{$fmdata['suppliername']}','{$fmdata['address']}','{$fmdata['username']}','{$fmdata['pwd']}','{$fmdata['contactman']}','{$fmdata['phone']}','{$fmdata['mobilephone']}','{$fmdata['artiperson']}','{$fmdata['licence']}','{$fmdata['taxnumber']}','{$fmdata['contactidcard']}','{$fmdata['contactmail']}','{$fmdata['postcode']}','{$fmdata['sex']}','$timestamp','0')");

		cls_message::show('�����ύ�ɹ�',axaction(6, "?action=$action"));
	}
}
?>