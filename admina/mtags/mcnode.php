<?php
(!defined('M_COM') || !defined('M_ADMIN')) && exit('No Permission');
if(!$modeSave){
	#$mtag = _tag_merge(@$mtag,@$mtagnew);
	templatebox('��ʶ��ģ��','mtagnew[template]',empty($mtag['template']) ? '' : $mtag['template'],10,110);
	trbasic('��Ϣ���õ��������','mtagnew[setting][val]',empty($mtag['setting']['val']) ? 'v' : $mtag['setting']['val'],'text',array('guide' => 'ϵͳĬ��Ϊv������ʶ����Ƕ��ʱ���ñ��Ҫ��ͬ�����¼���ʶ��[PHP����]�������ݵ���������<br> �ڱ���ʶģ����{xxx}��{$v[xxx]}���ɵ���xxx���ϣ���ʶ�����ֻ��ʹ��{$v[xxx]}��'));
	$arr = array('caid' => '��Ŀ');
	foreach(array('grouptypes','mctypes',) as $k) $$k = cls_cache::Read($k);
	foreach($cotypes as $k => $v) !$v['self_reg'] && $arr['ccid'.$k] = $v['cname'];
	foreach($grouptypes as $k => $v) !$v['issystem'] && $arr['ugid'.$k] = $v['cname'];
#	$arr['mcnid'] = '�Զ���ڵ�';
#	trbasic('ָ����Ա�ڵ�����','mtagnew[setting][cnsource]',makeoption($arr,isset($mtag['setting']['cnsource']) ? $mtag['setting']['cnsource'] : '0'),'select');
	trbasic('* ָ���ڵ�����id','mtagnew[setting][cnid]',empty($mtag['setting']['cnid']) ? '' : $mtag['setting']['cnid']);
	$arr = array('js' => 'ʹ��JS��̬���õ�ǰ��ʶ��������������',);
	$str = '';foreach($arr as $k => $v) $str .= "<input class=\"checkbox\" type=\"checkbox\" name=\"mtagnew[setting][$k]\" value=\"1\" ".(empty($mtag['setting'][$k]) ? '' : 'checked')."> &nbsp;$v<br>";
	trbasic('��ǰ��ʶ�ĸ�������','',$str,'');
	trbasic('��ѯ��������(��)','mtagnew[setting][ttl]',empty($mtag['setting']['ttl']) ? 0 : $mtag['setting']['ttl'],'text',array('guide' => '��λ���롣����չ���濪����ģ�����ģʽ�رյ��������Ч��'));
	tabfooter();
}else{
	$mtagnew['setting']['cnid'] = trim($mtagnew['setting']['cnid']);
	if(empty($mtagnew['template'])) mtag_error('�������ʶģ��');
	if(empty($mtagnew['setting']['cnid'])) mtag_error('ָ���ڵ�����id');
	$mtagnew['setting']['ttl'] = max(0,intval($mtagnew['setting']['ttl']));
}
?>
