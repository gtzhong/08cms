<?php

//----------------------------------------------------------------------

// ����-���ں�̨����

//global $sms_cfg_aset,$sms_cfg_api,$sms_cfg_tmieout;
$sms_cfg_tmieout = 3; //http���ӳ�ʱʱ��(��), ���Խӿ�(0test)�������
//global $sms_cfg_upw,$sms_cfg_pr3,$sms_cfg_pr4,$sms_cfg_pr5;
//global $sms_cfg_mchar,$sms_cfg_mtels;

$sms_cfg_aset = array(
	'winic' => array(
		'name' => '�ƶ�����',
		'home' => 'http://www.winic.org/',
		'unit' => 'Ԫ', // ��λ(Ԫ �� ��)
		'admin' => 'http://www.900112.com/', //���޴���ɲ���
		'note' => 'HTTP����,���ݲ�֧�ֿո���',
		'nmem' => 'HTTP����,���ݲ�֧�ֿո���', //��Ա��ʾ
	),
	'cr6868' => array(
		'name' => '����ý',
		'home' => 'http://www.cr6868.com/',
		'unit' => '��', // ��λ(Ԫ �� ��)
		'admin' => 'http://web.cr6868.com/login.aspx', //���޴���ɲ���
		'note' => '��Ϣ�в��ܺ�&#�����ַ���������ѯ���Ź�Ӧ�̡�',
		'nmem' => '', //��Ա��ʾ
	),
	'emhttp' => array(
		'name' => '����(http)',
		'unit' => 'Ԫ', // ��λ(Ԫ �� ��)
		'home' => 'http://www.emay.cn/', 
		'admin' => '', //
		'note' => '������ͨ�ӿ�(http����), ��ע�������û�������ѡ�����÷�ʽ, ��һ��ʹ��ʱ,��ʹ��[<a href="include/sms/extra_act.php?act=login" target="_blank">��¼(login)</a>]����; ������������ϵ���������Աָ��Keyֵ��',
		'nmem' => '', //��Ա��ʾ
		'gray' => 1,
	),
	/*
	'emay' => array(
		'name' => '����(ws)',
		'unit' => 'Ԫ', // ��λ(Ԫ �� ��)
		'home' => 'http://www.emay.cn/', 
		'admin' => '', //http://sdkhttp.eucp.b2m.cn/sdk/SDKService
		'note' => '������ͨ�ӿ�(Services����), ��һ��ʹ��ʱ,��ʹ��[<a href="include/sms/extra_act.php?act=login" target="_blank">��¼(login)</a>]����; ������������ϵ���������Աָ��Keyֵ��<br />',
		'nmem' => '', //��Ա��ʾ
	),*/
	'dxqun' => array(
		'name' => '����Ⱥ',
		'unit' => '��', // ��λ(Ԫ �� ��)
		'home' => 'http://www.dxqun.com/',
		'admin' => 'http://www.dxton.com/', //���޴���ɲ���
		'note' => 'HTTP���ͣ�<span style="color:#F0F">�ϸ��ն����ṩ�̵�[����ģ��]���ݷ��ͣ����򷢲���ȥ</span>����������������[�����ṩ��] �� ѡ�ñ�Ľӿڡ�',
		'nmem' => '', //��Ա��ʾ
		'gray' => 1,
	),
	/* û�и����Զ���,������
	'eshang8' => array(
		'name' => 'E������',
		'unit' => '��', // ��λ(Ԫ �� ��)
		'home' => 'http://www.eshang8.cn',
		'admin' => 'http://sms.eshang8.com/', //���޴���ɲ���
		'note' => '���ų���С�ڵ���70���ַ���',
		'nmem' => '���ų���С�ڵ���70���ַ���', //��Ա��ʾ
	),
	//*/
	'0test' => array(
		'name' => '���̲���',
		'unit' => '��', // ��λ(Ԫ �� ��)
		'home' => '', //���޴���ɲ���
		'admin' => '', //���޴���ɲ���
		'note' => '���Խӿ�,���ڲ���ϵͳ��������,�ṩ[��ֵ<a href="include/sms/extra_act.php?act=chargeUp&charge=20" target="_blank">[+20</a>|<a href="include/sms/extra_act.php?act=chargeUp&charge=-20" target="_blank">-20]</a>����]<br />����������ᷢ����,��дһ���ļ���¼��ʾ������; <br />',
		'nmem' => '���Խӿ�,���ڲ���ϵͳ�������̡�', //��Ա��ʾ
	),
);

// �̶�configs

//��������������ݣ�������������(δ��)
//$sms_cfg_mchar = 70; // һ����Ϣ,���ָ���(С��ͨ65����)
//$sms_cfg_mtels = 200; // һ�η���,���200���ֻ��������

//----------------------------------------------------------------------
