<?php
(!defined('M_COM') || !defined('M_ADMIN')) && exit('No Permission');
if(!$modeSave){
	templatebox('��ʶ��ģ��','mtagnew[template]',empty($mtag['template']) ? '' : $mtag['template'],10,110);
	trbasic('��Ϣ���õ��������','mtagnew[setting][val]',empty($mtag['setting']['val']) ? 'v' : $mtag['setting']['val'],'text',array('guide'=>'ϵͳĬ��Ϊv������ʶ����Ƕ��ʱ���ñ��Ҫ��ͬ�����¼���ʶ��[PHP����]�������ݵ���������<br> �ڱ���ʶģ����{xxx}��{$v[xxx]}���ɵ���xxx���ϣ���ʶ�����ֻ��ʹ��{$v[xxx]}��'));
	$arr = array(0 => '������',-1 => '����ģ��',) + cls_channel::chidsarr(1);
/*	trbasic('ָ���ĵ�ģ��','mtagnew[setting][chid]',makeoption($arr,empty($mtag['setting']['chid']) ? 0 : $mtag['setting']['chid']),'select');*/
	trbasic('ָ����Դid','mtagnew[setting][id]',empty($mtag['setting']['id']) ? '' : $mtag['setting']['id'],'text',array('guide' => '�ֶ������ĵ�aid,����Ϊ��Ĭ��Ϊ�����ĵ�'));
	$arr = array(0 => '������',);foreach($abrels as $k => $v) $arr[$k] = $v['cname'];
	trbasic('ָ�������ϼ�','mtagnew[setting][arid]',makeoption($arr,empty($mtag['setting']['arid']) ? 0 : $mtag['setting']['arid']),'select');
	$arr = array();
	empty($_infragment) && $arr['js'] = 'ʹ��JS��̬���õ�ǰ��ʶ��������������';
	$arr['detail'] = '��Ҫģ���ֶα������';
	$str = '';foreach($arr as $k => $v) $str .= "<input class=\"checkbox\" type=\"checkbox\" name=\"mtagnew[setting][$k]\" value=\"1\" ".(empty($mtag['setting'][$k]) ? '' : 'checked')."> &nbsp;$v<br>";
	trbasic('��ǰ��ʶ�ĸ�������','',$str,'');
	empty($_infragment) && setPermBar('���Ȩ������', 'mtagnew[setting][pmid]', empty($mtag['setting']['pmid']) ? 0 : $mtag['setting']['pmid'], $source='tpl', $soext='open', '�ڱ�ʶģ������[#pm#]�ָ���ǰ����Ϊ��Ȩ����ʾģ�壬�󲿷�Ϊ��Ȩ����ʾģ�塣');
    trbasic('��ѯ��������(��)','mtagnew[setting][ttl]',empty($mtag['setting']['ttl']) ? 0 : $mtag['setting']['ttl'],'text',array('guide' => '��λ���롣����չ���濪����ģ�����ģʽ�رյ��������Ч��'));
	tabfooter();
}else{
	if(empty($mtagnew['template'])) mtag_error('�������ʶģ��');
	$mtagnew['setting']['ttl'] = max(0,intval($mtagnew['setting']['ttl']));
}
?>
