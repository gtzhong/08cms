<?php
(!defined('M_COM') || !defined('M_ADMIN')) && exit('No Permission');
if(!$modeSave){
#	$mtag = _tag_merge(@$mtag,@$mtagnew);
	templatebox('��ʶ��ģ��','mtagnew[template]',empty($mtag['template']) ? '' : $mtag['template'],10,110);
	trbasic('��Ϣ���õ��������','mtagnew[setting][val]',empty($mtag['setting']['val']) ? 'v' : $mtag['setting']['val'],'text',array('guide' => 'ϵͳĬ��Ϊv������ʶ����Ƕ��ʱ���ñ��Ҫ��ͬ�����¼���ʶ��[PHP����]�������ݵ���������<br> �ڱ���ʶģ����{xxx}��{$v[xxx]}���ɵ���xxx���ϣ���ʶ�����ֻ��ʹ��{$v[xxx]}��'));
	trbasic('�б�����ʾ����������','mtagnew[setting][limits]',empty($mtag['setting']['limits']) ? 10 : $mtag['setting']['limits']);
	trbasic('* �б���������PHP��������ֵ','mtagnew[setting][func]',empty($mtag['setting']['func']) ? '' : $mtag['setting']['func'],'text',array('w' => 50,'guide' => '��ʽ��������(\'����1\',\'����2\'...)������ֵΪ�������飬��ǰҳ��Ϊ$_mp[\'nowpage\']��'));
	if(empty($_infragment)){
		$arr = array('js' => 'ʹ��JS��̬���õ�ǰ��ʶ��������������',);
		$str = '';foreach($arr as $k => $v) $str .= "<input class=\"checkbox\" type=\"checkbox\" name=\"mtagnew[setting][$k]\" value=\"1\" ".(empty($mtag['setting'][$k]) ? '' : 'checked')."> &nbsp;$v<br>";
		trbasic('��ǰ��ʶ�ĸ�������','',$str,'');
	}
	tabfooter();
	
	if(empty($_infragment)){
		tabheader('��ʶ��ҳ����');
		trbasic('�����б��ҳ','mtagnew[setting][mp]',empty($mtag['setting']['mp']) ? 0 : $mtag['setting']['mp'],'radio');
		trbasic('* �ܽ��������PHP��������ֵ','mtagnew[setting][mpfunc]',empty($mtag['setting']['mpfunc']) ? '' : $mtag['setting']['mpfunc'],'text',array('w' => 50,'guide' => '��ʽ��������(\'����1\',\'����2\'...)������ֵΪ�ܽ��������'));
		trbasic('�ܽ����(��Ϊ����)','mtagnew[setting][alimits]',isset($mtag['setting']['alimits']) ? $mtag['setting']['alimits'] : '');
		trbasic('�Ƿ���׵ķ�ҳ����','mtagnew[setting][simple]',empty($mtag['setting']['simple']) ? '0' : $mtag['setting']['simple'],'radio');
		trbasic('��ҳ������ҳ�볤��','mtagnew[setting][length]',isset($mtag['setting']['length']) ? $mtag['setting']['length'] : '');
		tabfooter();
	}	
}else{
	$mtagnew['setting']['func'] = trim($mtagnew['setting']['func']);
	$mtagnew['setting']['mpfunc'] = trim($mtagnew['setting']['mpfunc']);
	
	$mtagnew['setting']['limits'] = empty($mtagnew['setting']['limits']) ? 10 : max(0,intval($mtagnew['setting']['limits']));
	$mtagnew['setting']['alimits'] = max(0,intval($mtagnew['setting']['alimits']));
	$mtagnew['setting']['length'] = max(0,intval($mtagnew['setting']['length']));
	
	if(empty($mtagnew['template'])) mtag_error('�������ʶģ��');
#	if(empty($mtagnew['setting']['func'])) mtag_error('�������ʶ��������ֵ');
	if(!empty($mtagnew['setting']['mp']) && empty($mtagnew['setting']['mpfunc'])) mtag_error('�������ʶ������');
}
?>
