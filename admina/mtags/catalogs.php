<?php
(!defined('M_COM') || !defined('M_ADMIN')) && exit('No Permission');
if(empty($mtag['setting']['listby'])) {
    $mtag['setting']['listby'] = 'ca';
    $sclass = 0;
} else {
    $sclass = ($mtag['setting']['listby'] == 'ca' ? 0 : str_replace('co', '', $mtag['setting']['listby']));
}

if(!$modeSave){
	$_tt = empty($sclass) ? '��Ŀ' : '����';
	$sourcearr = array('0' => 'ȫ������'.$_tt,'4' => 'ȫ������'.$_tt,'5' => 'ȫ������'.$_tt,'1' => '�ֶ�ָ��','2' => '����'.$_tt.'���¼�'.$_tt,'3' => '�Զ���ѯ�ִ�',);
	if(empty($sclass)){
		sourcemodule('�б�������',
			'mtagnew[setting][casource]',
			$sourcearr + ridsarr(0),
			empty($mtag['setting']['casource']) ? '0' : $mtag['setting']['casource'],
			'1',
			'mtagnew[setting][caids][]',
			cls_catalog::ccidsarr(0),
			empty($mtag['setting']['caids']) ? array() : (is_array($mtag['setting']['caids']) ? $mtag['setting']['caids'] : explode(',',$mtag['setting']['caids']))
		);
	}else{
		sourcemodule('�б�������',
			"mtagnew[setting][cosource$sclass]",
			$sourcearr + ridsarr($sclass),
			empty($mtag['setting']['cosource'.$sclass]) ? '0' : $mtag['setting']['cosource'.$sclass],
			'1',
			"mtagnew[setting][ccids$sclass][]",
			cls_catalog::ccidsarr($sclass),
			empty($mtag['setting']['ccids'.$sclass]) ? array() : explode(',',$mtag['setting']['ccids'.$sclass])
		);
	}
	templatebox('��ʶ��ģ��','mtagnew[template]',empty($mtag['template']) ? '' : $mtag['template'],10,110);
	trbasic('��Ϣ���õ��������','mtagnew[setting][val]',empty($mtag['setting']['val']) ? 'v' : $mtag['setting']['val'],'text',array('guide' => 'ϵͳĬ��Ϊv������ʶ����Ƕ��ʱ���ñ��Ҫ��ͬ�����¼���ʶ��[PHP����]�������ݵ���������<br> �ڱ���ʶģ����{xxx}��{$v[xxx]}���ɵ���xxx���ϣ���ʶ�����ֻ��ʹ��{$v[xxx]}��'));
	trbasic('�б�����ʾ����������','mtagnew[setting][limits]',empty($mtag['setting']['limits']) ? '10' : $mtag['setting']['limits']);
	trbasic('�ӵڼ�����¼��ʼ��ʾ','mtagnew[setting][startno]',empty($mtag['setting']['startno']) ? '' : $mtag['setting']['startno'],'text',array('guide' => '���ð���ǰ���õĵڼ�����¼��ʼ��Ĭ��Ϊ0��'));
	tabfooter();
	
	tabheader('�ڵ�Ľ�������');
	if(!empty($sclass)){
		$inheritarr = array('0' => '�ǹ�����','active' => '������Ŀ',);
		$inheritarr = $inheritarr + cls_catalog::ccidsarr(0);
		trbasic('��Ŀ','mtagnew[setting][cainherit]',makeoption($inheritarr,empty($mtag['setting']['cainherit']) ? '0' : $mtag['setting']['cainherit']),'select');
	}
	foreach($cotypes as $k => $v){
		if($v['sortable'] && $sclass != $k) {
			$inheritarr = array('0' => '�ǹ�����','active' => '�������',);
			$inheritarr = $inheritarr + cls_catalog::ccidsarr($k);
			trbasic("$v[cname]","mtagnew[setting][coinherit$k]",makeoption($inheritarr,empty($mtag['setting']['coinherit'.$k]) ? '0' : $mtag['setting']['coinherit'.$k]),'select');
		}
	}
	tabfooter();
	tabheader('�߼�ѡ��');
	if(empty($_infragment)){
		$arr = array('js' => 'ʹ��JS��̬���õ�ǰ��ʶ��������������',);
		$str = '';foreach($arr as $k => $v) $str .= "<input class=\"checkbox\" type=\"checkbox\" name=\"mtagnew[setting][$k]\" value=\"1\" ".(empty($mtag['setting'][$k]) ? '' : 'checked')."> &nbsp;$v<br>";
		trbasic('��ǰ��ʶ�ĸ�������','',$str,'');
	}
	$addstr = "&nbsp; >><a href=\"?entry=liststr&action=catalogs&typeid=$sclass\" target=\"_blank\">����</a>";
	trbasic('�����ִ�'.$addstr,'mtagnew[setting][orderstr]',empty($mtag['setting']['orderstr']) ? '' : $mtag['setting']['orderstr'],'text',array('w' => 50));
	$addstr .= "<br><input class=\"checkbox\" type=\"checkbox\" id=\"mtagnew[setting][isfunc]\" name=\"mtagnew[setting][isfunc]\"".(empty($mtag['setting']['isfunc']) ? '' : ' checked').">�ִ����Ժ���";
	$addstr .= "<br><input class=\"checkbox\" type=\"checkbox\" id=\"mtagnew[setting][isall]\" name=\"mtagnew[setting][isall]\"".(empty($mtag['setting']['isall']) ? '' : ' checked').">������ѯ�ִ�";
	trbasic('ɸѡ��ѯ�ִ�'.$addstr,'mtagnew[setting][wherestr]',empty($mtag['setting']['wherestr']) ? '' : $mtag['setting']['wherestr'],'textarea',array('guide' => '������ʽ��������(\'����1\',\'����2\')��������ѯ�ִ�����select��from��where,��Ҫ��order��limit��'));
	trbasic('ǿ�������ִ�','mtagnew[setting][forceindex]',empty($mtag['setting']['forceindex']) ? '' : $mtag['setting']['forceindex'],'text',array('guide' => '��ʽ������0.mclicks,������ǰ��ȷ�ϵ�ǰ�Ĳ�ѯ���漰���н���mclicks��������'));
	trbasic('��ѯ��������(��)','mtagnew[setting][ttl]',empty($mtag['setting']['ttl']) ? 0 : $mtag['setting']['ttl'],'text',array('guide' => '��λ���롣����չ���濪����ģ�����ģʽ�رյ��������Ч��'));
	tabfooter();
	
	if(empty($_infragment)){
		tabheader('��ʶ��ҳ����');
		trbasic('�����б��ҳ','mtagnew[setting][mp]',empty($mtag['setting']['mp']) ? 0 : $mtag['setting']['mp'],'radio');
		trbasic('�ܽ����(��Ϊ����)','mtagnew[setting][alimits]',isset($mtag['setting']['alimits']) ? $mtag['setting']['alimits'] : '');
		trbasic('�Ƿ���׵ķ�ҳ����','mtagnew[setting][simple]',empty($mtag['setting']['simple']) ? '0' : $mtag['setting']['simple'],'radio');
		trbasic('��ҳ������ҳ�볤��','mtagnew[setting][length]',isset($mtag['setting']['length']) ? $mtag['setting']['length'] : '');
		tabfooter();
	}
}else{//?????????????????????????���˷�listby�Ĳ���
	if(empty($mtagnew['template'])) mtag_error('�������ʶģ��');
	if(!isset($mtagnew['setting'][cls_mtags_catalogs::SCLASS_VAL]) || $mtagnew['setting'][cls_mtags_catalogs::SCLASS_VAL] == '') mtag_error('��ָ����ȷ����Ŀ��Ŀ');
	$mtagnew['setting']['limits'] = empty($mtagnew['setting']['limits']) ? 10 : max(0,intval($mtagnew['setting']['limits']));
	$mtagnew['setting']['alimits'] = max(0,intval(@$mtagnew['setting']['alimits']));
	$mtagnew['setting']['length'] = max(0,intval(@$mtagnew['setting']['length']));
	$mtagnew['setting']['startno'] = trim($mtagnew['setting']['startno']);
	$mtagnew['setting']['orderstr'] = empty($mtagnew['setting']['orderstr']) ? '' : trim($mtagnew['setting']['orderstr']);
	$mtagnew['setting']['wherestr'] = empty($mtagnew['setting']['wherestr']) ? '' : trim($mtagnew['setting']['wherestr']);
	$mtagnew['setting']['isfunc'] = empty($mtagnew['setting']['isfunc']) || empty($mtagnew['setting']['wherestr']) ? 0 : 1;
	$mtagnew['setting']['isall'] = empty($mtagnew['setting']['isall']) || empty($mtagnew['setting']['wherestr']) ? 0 : 1;
	$mtagnew['setting']['ttl'] = max(0,intval($mtagnew['setting']['ttl']));

	//��������Ĵ���
	$idvars = array('caids');
	foreach($cotypes as $k => $cotype) $idvars[] = 'ccids'.$k;
	foreach($idvars as $k){
		if(empty($mtagnew['setting'][$k])){
			unset($mtagnew['setting'][$k]);
		}else $mtagnew['setting'][$k] = implode(',',$mtagnew['setting'][$k]);
	}
	$mtagnew['setting']['forceindex'] = trim($mtagnew['setting']['forceindex']);
	if(empty($mtagnew['setting']['forceindex'])) unset($mtagnew['setting']['forceindex']);
}
function ridsarr($coid = 0){
	$cnrels = cls_cache::Read('cnrels');
	$ret = array();
	foreach($cnrels as $k => $v){
		if(($coid > 0 && in_array($coid,array($v['coid'],$v['coid1']))) || ($coid <= 0 && ($v['coid'] <= 0 || $v['coid1'] <= 0))) $ret[-$k] = '['.'����'.']'.$v['cname'];
	}
	return $ret;
}