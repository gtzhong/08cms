<?php

// �ο�admina.php, ����file,cache;
define('M_ADMIN', TRUE);
define('NOROBOT', TRUE);
define('M_UPSEN', TRUE);
include_once dirname(dirname(__FILE__)).'/general.inc.php';
include_once M_ROOT.'include/admina.fun.php';

// �����Ȩ���߲ſ�ִ�д˲���
if($re = $curuser->NoBackFunc('smsapi')) cls_message::show($re);
//print_r($a_funcs);

// sms-api-load
// $class = "sms_$sms_cfg_api";
$smsdo = new cls_sms();
$baseurl = $smsdo->smsdo->baseurl;

// ��½ע�����-����emay+http�ӿ��д˲���
if($sms_cfg_api=='emhttp' && $act=='login'){
		aheader(); 
		$userid = $smsdo->smsdo->userid;
		$userpw = $smsdo->smsdo->userpw;
		echo "<div class='mainBox'><div class='itemtitle'><h3>����(http)�ӿ� : ��¼ע��ҳ(����һ��ʹ����Ҫ�˲���)</h3></div></div>";
		tabheader(' ��һ�������к�ע��','fm_smsreg',"{$baseurl}registdetailinfo.action",2,0,1);
		trbasic('����ұ� ���к�ע��','',"<a href='{$baseurl}regist.action?cdkey=$userid&password=$userpw' target='_blank'>������[���к�ע��]</a>",'');
		tabfooter();
		
		tabheader(' �ڶ�������ҵ��Ϣע��'); //  rule="text" must="1" mode="" regx="" min="3" max="50" rev="��ϵ��">
		trbasic('�������к�','cdkey'    ,$userid ,'text', array('w'=>30,'guide'=>'��Ӷ��Ź�Ӧ�̻�ȡ!'      ,'validate'=>' rule="text" must="1" regx="" min="6" max="24" '));
		trbasic('���к�����','password' ,$userpw ,'text', array('w'=>30,'guide'=>'��Ӷ��Ź�Ӧ�̻�ȡ!'      ,'validate'=>' rule="text" must="1" regx="" min="4" max="12" '));
		trbasic('��ҵ����'  ,'ename'    ,''      ,'text', array('w'=>50,'guide'=>'(���60�ֽ�)���������룡' ,'validate'=>' rule="text" must="1" regx="" min="2" max="60" '));
		trbasic('��ϵ������','linkman'  ,''      ,'text', array(        'guide'=>'(���20�ֽ�)���������룡' ,'validate'=>' rule="text" must="1" regx="" min="2" max="20" '));
		trbasic('��ϵ�绰'  ,'phonenum' ,''      ,'text', array(        'guide'=>'(���20�ֽ�)���������룡' ,'validate'=>' rule="text" must="1" regx="" min="2" max="20" '));
		trbasic('��ϵ�ֻ�'  ,'mobile'   ,''      ,'text', array(        'guide'=>'(���15�ֽ�)���������룡' ,'validate'=>' rule="text" must="1" regx="" min="2" max="15" '));
		trbasic('��ϵ����'  ,'fax'      ,''      ,'text', array(        'guide'=>'(���20�ֽ�)���������룡' ,'validate'=>' rule="text" must="1" regx="" min="2" max="20" '));
		trbasic('�����ʼ�'  ,'email'    ,''      ,'text', array('w'=>50,'guide'=>'(���60�ֽ�)���������룡' ,'validate'=>' rule="text" must="1" regx="" min="2" max="60" '));
		trbasic('��˾��ַ'  ,'address'  ,''      ,'text', array('w'=>50,'guide'=>'(���60�ֽ�)���������룡' ,'validate'=>' rule="text" must="1" regx="" min="6" max="60" '));
		trbasic('��������'  ,'postcode' ,''      ,'text', array(        'guide'=>'(���6�ֽ�)�� �������룡' ,'validate'=>' rule="text" must="1" regx="" min="6" max="6"  '));
		tabfooter('btn_smsreg');
		
}


// ��½����-����emay�ӿ��д˲���
if($sms_cfg_api=='emay' && $act=='login'){
	$msg = $smsdo->smsdo->login();
	if($msg[0]=='1'){
		echo "��½�ɹ���<br>�������£�";
	}else{
		echo "��������<br>�������£�";
	}
	print_r($msg);	
}
/*
 �� ������ͨ �ӿ��ṩ�����Ƚ�Ƶ����login,logout�����������sessionKey����ʹ�ã���ֻ�ǵ�һ�����ɵ�sessionKey��Ч��
 ��������ֻ��һ��login����������login,logout����������ϵ���������Ա��
*/
// ע������-����emay�ӿ��д˲���
if($sms_cfg_api=='emay' && $act=='logout'){
	die('�����½/ע������������ϵ���������Ա��'); //[<a href="include/sms/extra_act.php?act=logout" target="_blank">ע��(logout)</a>]������,�����ٷ�������,�����ٽ���login(��¼)
	$msg = $smsdo->smsdo->logout();
	if($msg[0]=='1'){
		echo "ע���ɹ���<br>�������£�";
	}else{
		echo "��������<br>�������£�";
	}
	print_r($msg);	
}
// ��ֵ-��Ҫ�ǲ���apiʹ��
if($sms_cfg_api=='0test' && $act=='chargeUp'){
	if(!empty($charge)){
		$msg = $smsdo->chargeUp($charge);
		if($msg[0]=='1'){
			echo "��ֵ�ɹ���<br>�������£�";
		}else{
			echo "��������<br>�������£�";
		}
		print_r($msg);	
	}
}

?>
