<?php
(!defined('M_COM') || !defined('M_ADMIN')) && exit('No Permission');
if(!$modeSave){
	#$mtag = _tag_merge(@$mtag,@$mtagnew);
	$datearr = array(
		''			=> '����ʾ����',
		'y-m-d'		=> '��-��-�գ�09-04-07',
		'Y-m-d'		=> '��-��-�գ�2009-04-07',
		'm-d-y'		=> '��-��-�꣺04-07-09',
		'm-d-Y'		=> '��-��-�꣺04-07-2009',
		'y-m'		=> '��-�¡� ��09-04',
		'Y-m'		=> '��-�¡� ��2009-04',
		'm-d'		=> '�� ��-�գ�04-07',
		'M-d'		=> '�� ��-�գ�Apr-07',
		'F-d'		=> '�� ��-�գ�April-07',
		'M-d-Y'		=> '��-��-�꣺Apr-07-09',
		'M-d-y'		=> '��-��-�꣺Apr-07-2009',
		'Y��m��d��'	=> 'XXXX��XX��XX�գ�2012��02��22��',
		'Y��m��'	=> 'XXXX��XX�¡��� ��2012��02��',
		'm��d��'	=> '������ XX��XX�� ��04��07��',
	);
	$timearr = array(
		''			=> '����ʾʱ��',
		'H:i:s'		=> 'ʱ:��:�룺14:07:05',
		'h:i:s a'	=> 'ʱ:��:�룺02:07:05 pm',
		'H:i'		=> 'ʱ:�֡� ��14:07',
		'A h:i'		=> 'ʱ:�֡� ��PM 02:07',
		'i:s'		=> '�� ��:�룺07:05',
	);
	trbasic('*ָ��������Դ','mtagnew[setting][tname]',isset($mtag['setting']['tname']) ? $mtag['setting']['tname'] : '','text',array('guide' => '�����ʽ���ֶ���aa������$a[b]�ȡ�'));
	trbasic('������ʾ��ʽ','mtagnew[setting][date]',makeoption($datearr,empty($mtag['setting']['date']) ? '0' : $mtag['setting']['date']),'select');
	trbasic('ʱ����ʾ��ʽ','mtagnew[setting][time]',makeoption($timearr,empty($mtag['setting']['time']) ? '0' : $mtag['setting']['time']),'select');
	tabfooter();
}else{
	$mtagnew['setting']['tname'] = trim($mtagnew['setting']['tname']);
	if(empty($mtagnew['setting']['tname']) || !preg_match("/^[a-zA-Z_\$][a-zA-Z0-9_\[\]]*$/",$mtagnew['setting']['tname'])){
		mtag_error('������Դ���ò��Ϲ淶');
	}
}
?>
