<?php
(!defined('M_COM') || !defined('M_ADMIN')) && exit('No Permission');
if(!$modeSave){
	#$mtag = _tag_merge(@$mtag,@$mtagnew);
	templatebox('��ʶ��ģ��','mtagnew[template]',empty($mtag['template']) ? '' : $mtag['template'],10,110);
	trbasic('��Ϣ���õ��������','mtagnew[setting][val]',empty($mtag['setting']['val']) ? 'v' : $mtag['setting']['val'],'text',array('guide' => 'ϵͳĬ��Ϊv������ʶ����Ƕ��ʱ���ñ��Ҫ��ͬ�����¼���ʶ��[PHP����]�������ݵ���������<br> �ڱ���ʶģ����{xxx}��{$v[xxx]}���ɵ���xxx���ϣ���ʶ�����ֻ��ʹ��{$v[xxx]}��'));
	trbasic('�б�����ʾ����������','mtagnew[setting][limits]',empty($mtag['setting']['limits']) ? '10' : $mtag['setting']['limits']);
	$arr = array('js' => 'ʹ��JS��̬���õ�ǰ��ʶ��������������',);
	$str = '';foreach($arr as $k => $v) $str .= "<input class=\"checkbox\" type=\"checkbox\" name=\"mtagnew[setting][$k]\" value=\"1\" ".(empty($mtag['setting'][$k]) ? '' : 'checked')."> &nbsp;$v<br>";
	trbasic('��ǰ��ʶ�ĸ�������','',$str,'');
	tabfooter();
	tabheader('�����б���Ŀ');
	$sourcearr = array('0' => 'ȫ���ռ���Ŀ','2' => '������Ŀ�µ����з���','1' => '�ֶ�ָ����Ŀ',);
	sourcemodule("�ռ���Ŀ<input class=\"radio\" type=\"radio\" name=\"mtagnew[setting][listby]\" value=\"0\"".(empty($mtag['setting']['listby']) ? " checked" : "").">��Ϊ�б���",
				'mtagnew[setting][casource]',
				$sourcearr,
				empty($mtag['setting']['casource']) ? '0' : $mtag['setting']['casource'],
				'1',
				'mtagnew[setting][caids][]',
				cls_mcatalog::mcaidsarr(),
				(!empty($mtag['setting']['caids']) ? explode(',',$mtag['setting']['caids']) : array())
				);
	$sourcearr = array('0' => '��Ŀ��ȫ������',);
	trbasic("���˷���<input class=\"radio\" type=\"radio\" name=\"mtagnew[setting][listby]\" value=\"1\"".(!empty($mtag['setting']['listby']) ? " checked" : "").">��Ϊ�б���",'mtagnew[setting][ucsource]',empty($mtag['setting']['ucsource']) ? '' : $mtag['setting']['ucsource'],'text',array('guide' => '����Ϊ������Ŀ�����пռ���࣬������ָ��һ���ռ���Ŀ��'));
	tabfooter();
}else{
	if(empty($mtagnew['template'])) mtag_error('�������ʶģ��');
	$mtagnew['setting']['limits'] = empty($mtagnew['setting']['limits']) ? 10 : max(0,intval($mtagnew['setting']['limits']));
	//��������Ĵ���
	$idvars = array('caids');
	foreach($idvars as $k){
		if(empty($mtagnew['setting'][$k])){
			unset($mtagnew['setting'][$k]);
		}else $mtagnew['setting'][$k] = implode(',',$mtagnew['setting'][$k]);
	}
}
?>
