<?php
(!defined('M_COM') || !defined('M_ADMIN')) && exit('No Permission');
if(!$modeSave){
	#$mtag = _tag_merge(@$mtag,@$mtagnew);
	templatebox('��ʶ��ģ��','mtagnew[template]',empty($mtag['template']) ? '' : $mtag['template'],10,110);
	trbasic('��Ϣ���õ��������','mtagnew[setting][val]',empty($mtag['setting']['val']) ? 'v' : $mtag['setting']['val'],'text',array('guide' => 'ϵͳĬ��Ϊv������ʶ����Ƕ��ʱ���ñ��Ҫ��ͬ�����¼���ʶ��[PHP����]�������ݵ���������<br> �ڱ���ʶģ����{xxx}��{$v[xxx]}���ɵ���xxx���ϣ���ʶ�����ֻ��ʹ��{$v[xxx]}��'));
	$typearr = array(
	'chid' => '�ĵ�ģ��',
	'mchid' => '��Աģ��',
	'caid' => '��Ŀ',
	'mctid' => '��֤����',
	);
	foreach($cotypes as $k => $v) $typearr['ccid'.$k] = '����-'.$v['cname'];
	foreach($grouptypes as $k => $v) $typearr['grouptype'.$k] = '��Ա��-'.$v['cname'];
	trbasic('ָ��ID��Դ����','mtagnew[setting][type]',makeoption($typearr,empty($mtag['setting']['type']) ? '' : $mtag['setting']['type']),'select');
	trbasic('ָ��ID����Ϊ����ID��','mtagnew[setting][id]',isset($mtag['setting']['id']) ? $mtag['setting']['id'] : 0,'text');
	tabfooter();
}else{
	if(empty($mtagnew['template'])) mtag_error('�������ʶģ��');
}
?>
