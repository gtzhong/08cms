<?php
if(!$modeSave){
	#$mtag = _tag_merge(@$mtag,@$mtagnew);
	templatebox('��ʶ��ģ��','mtagnew[template]',empty($mtag['template']) ? '' : $mtag['template'],10,110);
	trbasic('��Ϣ���õ��������','mtagnew[setting][val]',empty($mtag['setting']['val']) ? 'v' : $mtag['setting']['val'],'text',array('guide' => 'ϵͳĬ��Ϊv������ʶ����Ƕ��ʱ���ñ��Ҫ��ͬ�����¼���ʶ��[PHP����]�������ݵ���������<br> �ڱ���ʶģ����{xxx}��{$v[xxx]}���ɵ���xxx���ϣ���ʶ�����ֻ��ʹ��{$v[xxx]}��'));
	trbasic('ָ����Դid','mtagnew[setting][id]',empty($mtag['setting']['id']) ? '' : $mtag['setting']['id'],'text',array('guide' => '����Ϊ��Ĭ��Ϊ������Ϣ'));
	if(empty($_infragment)){
		$arr = array('js' => 'ʹ��JS��̬���õ�ǰ��ʶ��������������',);
		$str = '';foreach($arr as $k => $v) $str .= "<input class=\"checkbox\" type=\"checkbox\" name=\"mtagnew[setting][$k]\" value=\"1\" ".(empty($mtag['setting'][$k]) ? '' : 'checked')."> &nbsp;$v<br>";
		trbasic('��ǰ��ʶ�ĸ�������','',$str,'');
		trbasic('���Ȩ������','mtagnew[setting][pmid]',makeoption(pmidsarr('tpl'),empty($mtag['setting']['pmid']) ? 0 : $mtag['setting']['pmid']),'select',array('guide' => '�ڱ�ʶģ������[#pm#]�ָ���ǰ����Ϊ��Ȩ����ʾģ�壬�󲿷�Ϊ��Ȩ����ʾģ�塣ϵͳ����=>��������=>Ȩ�޷���=>ģ�塣'));
	}
	trbasic('��ѯ��������(��)','mtagnew[setting][ttl]',empty($mtag['setting']['ttl']) ? 0 : $mtag['setting']['ttl'],'text',array('guide' => '��λ���롣����չ���濪����ģ�����ģʽ�رյ��������Ч��'));
	tabfooter();
}else{
	if(empty($mtagnew['template'])) mtag_error('�������ʶģ��');
	$mtagnew['setting']['ttl'] = max(0,intval($mtagnew['setting']['ttl']));
}
?>
