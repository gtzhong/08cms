<?php
(!defined('M_COM') || !defined('M_ADMIN')) && exit('No Permission');
if(!$modeSave){
	#$mtag = _tag_merge(@$mtag,@$mtagnew);
	templatebox('��ʶ��ģ��','mtagnew[template]',empty($mtag['template']) ? '' : $mtag['template'],10,110);
	trbasic('* ָ��������Դ','mtagnew[setting][tname]',isset($mtag['setting']['tname']) ? $mtag['setting']['tname'] : '','text',array('guide' => '�������ֶ���aa�����$a[b]�����ʶ�������ء�����ҳ��flash.php��(ҳ��ָ����Դ)�������ա�'));
	trbasic('��Ϣ���õ��������','mtagnew[setting][val]',empty($mtag['setting']['val']) ? 'v' : $mtag['setting']['val'],'text',array('guide' => 'ϵͳĬ��Ϊv������ʶ����Ƕ��ʱ���ñ��Ҫ��ͬ�����¼���ʶ��[PHP����]�������ݵ���������<br> �ڱ���ʶģ����{xxx}��{$v[xxx]}���ɵ���xxx���ϣ���ʶ�����ֻ��ʹ��{$v[xxx]}��'));
	tabfooter();
}else{
	if(empty($mtagnew['template'])) mtag_error('�������ʶģ��');
	$mtagnew['setting']['tname'] = trim($mtagnew['setting']['tname']);
	if(!empty($mtagnew['setting']['tname']) && !preg_match("/^[a-zA-Z_\$][a-zA-Z0-9_\[\]]*$/",$mtagnew['setting']['tname'])){
		mtag_error('������Դ���ò��Ϲ淶');
	}
}
?>
