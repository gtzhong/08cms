<?php
(!defined('M_COM') || !defined('M_ADMIN')) && exit('No Permission');
empty($mtag['setting']['listby']) && $mtag['setting']['listby'] = '1';
if(!$modeSave){
	#$mtag = _tag_merge(@$mtag,@$mtagnew);
	templatebox('��ʶ��ģ��','mtagnew[template]',empty($mtag['template']) ? '' : $mtag['template'],10,110);
	trbasic('��Ϣ���õ��������','mtagnew[setting][val]',empty($mtag['setting']['val']) ? 'v' : $mtag['setting']['val'],'text',array('guide' => 'ϵͳĬ��Ϊv������ʶ����Ƕ��ʱ���ñ��Ҫ��ͬ�����¼���ʶ��[PHP����]�������ݵ���������<br> �ڱ���ʶģ����{xxx}��{$v[xxx]}���ɵ���xxx���ϣ���ʶ�����ֻ��ʹ��{$v[xxx]}��'));
	trbasic('�б�����ʾ����������','mtagnew[setting][limits]',empty($mtag['setting']['limits']) ? '10' : $mtag['setting']['limits']);
	tabfooter();

	tabheader('�б���Ŀ����');
	$narr = array(
	'0' => array('cname' => '�ĵ�ģ��','arr' => cls_channel::chidsarr(),),
	'1' => array('cname' => '��Աģ��','arr' => cls_mchannel::mchidsarr(),),
	);
	foreach($grouptypes as $k => $v) $narr[10+$k] = array('cname' => $v['cname'],'arr' => ugidsarr($k),);
	$caco_same_fix = 'caco_same_fix_';
	$caco_diff_fix = 'caco_diff_fix_';
	$cacoarr = array();foreach($narr as $k => $v) $cacoarr[$k] = $v['cname'];
	trbasic('��Ϊ�б���','',makeradio('mtagnew[setting][listby]', $cacoarr, $mtag['setting']['listby'],5,"single_list_set(this, '$caco_same_fix')"), '');
	
	$sourcearr = array(0 => 'ȫ��',1 => '�ֶ�ָ��',);
	foreach($narr as $k => $v){
		sourcemodule($v['cname'],"mtagnew[setting][source$k]",$sourcearr,empty($mtag['setting']['source'.$k]) ? 0 : $mtag['setting']['source'.$k],
		'1',
		"mtagnew[setting][ids$k][]",$v['arr'],empty($mtag['setting']['ids'.$k]) ? array() : explode(',',$mtag['setting']['ids'.$k]),
		'25%',
		$mtag['setting']['listby'] == $k,$caco_same_fix.$k);
	}
	tabfooter();
}else{
	if(empty($mtagnew['template'])) mtag_error('�������ʶģ��');
	$mtagnew['setting']['limits'] = empty($mtagnew['setting']['limits']) ? 10 : max(0,intval($mtagnew['setting']['limits']));

	//��������Ĵ���
	$idvars = array('ids0','ids1','ids2',);
	foreach($grouptypes as $k => $v) $idvars[] = 'ids'.(10+$k);
	foreach($idvars as $k){
		if(empty($mtagnew['setting'][$k])){
			unset($mtagnew['setting'][$k]);
		}else $mtagnew['setting'][$k] = implode(',',$mtagnew['setting'][$k]);
	}
}
?>
