<?php
(!defined('M_COM') || !defined('M_ADMIN')) && exit('No Permission');
if(!$modeSave){
	#$mtag = _tag_merge(@$mtag,@$mtagnew);
	templatebox('��ʶ��ģ��','mtagnew[template]',empty($mtag['template']) ? '' : $mtag['template'],10,110);
	trbasic('��Ϣ���õ��������','mtagnew[setting][val]',empty($mtag['setting']['val']) ? 'v' : $mtag['setting']['val'],'text',array('guide' => 'ϵͳĬ��Ϊv������ʶ����Ƕ��ʱ���ñ��Ҫ��ͬ�����¼���ʶ��[PHP����]�������ݵ���������<br> �ڱ���ʶģ����{xxx}��{$v[xxx]}���ɵ���xxx���ϣ���ʶ�����ֻ��ʹ��{$v[xxx]}��'));
	$coidsarr = array('caid' => '��Ŀ');
	foreach($cotypes as $k => $v) $v['sortable'] && $coidsarr["ccid$k"] = $v['cname'];
	trbasic('��Ҫ����ϵ����','',makecheckbox('mtagnew[setting][coids][]',$coidsarr,empty($mtag['setting']['coids']) ? array() : explode(',',$mtag['setting']['coids']),5),'',array('guide' => 'ѡ����Ҫ������ɵ�������ϵ���أ���ѡ�����������ϵ���ء�'));
	$arr = array('js' => 'ʹ��JS��̬���õ�ǰ��ʶ��������������',);
	$str = '';foreach($arr as $k => $v) $str .= "<input class=\"checkbox\" type=\"checkbox\" name=\"mtagnew[setting][$k]\" value=\"1\" ".(empty($mtag['setting'][$k]) ? '' : 'checked')."> &nbsp;$v<br>";
	trbasic('��ǰ��ʶ�ĸ�������','',$str,'');
	tabfooter();
}else{
	if(empty($mtagnew['template'])) mtag_error('�������ʶģ��');
	$mtagnew['setting']['coids'] = empty($mtagnew['setting']['coids']) ? '' : implode(',',$mtagnew['setting']['coids']);
}
?>
