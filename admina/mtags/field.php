<?php
(!defined('M_COM') || !defined('M_ADMIN')) && exit('No Permission');
if(!$modeSave){
	#$mtag = _tag_merge(@$mtag,@$mtagnew);
	trbasic('* ��Դ�ֶ�Ӣ�ı�ʶ','mtagnew[setting][tname]',isset($mtag['setting']['tname']) ? $mtag['setting']['tname'] : '','text',array('guide' => '�����ֶε�Ӣ�ı�ʶ�����ܴ�$��[]��'));
	$arr = array(
		'archive' => '�ĵ�',
		'member' => '��Ա',
		'farchive' => '����',
		'catalog' => '��Ŀ',
		'coclass' => '����',
		'commu' => '����',
		'push' => '����λ', 
	);
	trbasic('�ֶ�����','mtagnew[setting][type]',makeoption($arr,empty($mtag['setting']['type']) ? '0' : $mtag['setting']['type']),'select');
	trbasic('�������ֻ�г�ǰ����','mtagnew[setting][limits]',empty($mtag['setting']['limits']) ? '' : $mtag['setting']['limits'],'text',array('guide' => '���ձ�ʾ�������ȫ���г���'));
	tabfooter();
}else{
	$mtagnew['setting']['tname'] = trim($mtagnew['setting']['tname']);
	$mtagnew['setting']['limits'] = max(0,intval($mtagnew['setting']['limits']));
	if(empty($mtagnew['setting']['tname'])) cls_message::show('��������Դ�ֶ�Ӣ�ı�ʶ');
	if(empty($mtagnew['setting']['tname']) || !preg_match("/^[a-zA-Z_][a-zA-Z0-9_]*$/",$mtagnew['setting']['tname'])){
		mtag_error('������Դ���ò��Ϲ淶');
	}
	$mtagnew['setting']['fname'] = $mtagnew['setting']['tname'];
}
?>
