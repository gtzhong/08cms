<?php
(!defined('M_COM') || !defined('M_ADMIN')) && exit('No Permission');
if(!$modeSave){
	templatebox('��ʶ��ģ��','mtagnew[template]',empty($mtag['template']) ? '' : $mtag['template'],10,110);
	trbasic('��Ϣ���õ��������','mtagnew[setting][val]',empty($mtag['setting']['val']) ? 'v' : $mtag['setting']['val'],'text',array('guide'=>'ϵͳĬ��Ϊv������ʶ����Ƕ��ʱ���ñ��Ҫ��ͬ�����¼���ʶ��[PHP����]�������ݵ���������<br> �ڱ���ʶģ����{xxx}��{$v[xxx]}���ɵ���xxx���ϣ���ʶ�����ֻ��ʹ��{$v[xxx]}��'));
	tabfooter();

	tabheader('����ɸѡ����');
	$chsourcearr = array('0' => '���ų�','1' => 'ָ���ų�',);
/*	sourcemodule('�ų������ĵ�ģ��',
				'mtagnew[setting][nochsource]',
				$chsourcearr,
				empty($mtag['setting']['nochids'][0]) ? 0 : 1,
				'1',
				'mtagnew[setting][nochids][]',
				cls_channel::chidsarr(1),
				!empty($mtag['setting']['nochids']) ? (is_array($mtag['setting']['nochids']) ? $mtag['setting']['nochids'] : explode(',',$mtag['setting']['nochids'])) : array()
				);*/
	$sourcearr = array('0' => '������Ŀ','2' => '������Ŀ','1' => '�ֶ�ָ��',);
	sourcemodule('������Ŀ����'."&nbsp;&nbsp;&nbsp;<input class=\"checkbox\" type=\"checkbox\" name=\"mtagnew[setting][caidson]\" value=\"1\"".(empty($mtag['setting']['caidson']) ? "" : " checked").">���ӷ���",
				'mtagnew[setting][casource]',
				$sourcearr,
				empty($mtag['setting']['casource']) ? '0' : $mtag['setting']['casource'],
				'1',
				'mtagnew[setting][caids][]',
				cls_catalog::ccidsarr(0,$sclass),
				empty($mtag['setting']['caids']) ? array() : (is_array($mtag['setting']['caids']) ? $mtag['setting']['caids'] : explode(',',$mtag['setting']['caids']))
				);
	foreach($cotypes as $k => $cotype){
		if($sclass && !coid_in_chid($k,$sclass)) continue;
		sourcemodule("$cotype[cname]"."&nbsp;&nbsp;&nbsp;<input class=\"checkbox\" type=\"checkbox\" name=\"mtagnew[setting][ccidson$k]\" value=\"1\"".(empty($mtag['setting']['ccidson'.$k]) ? "" : " checked").">���ӷ���",
					"mtagnew[setting][cosource$k]",
					$sourcearr,
					empty($mtag['setting']['cosource'.$k]) ? '0' : $mtag['setting']['cosource'.$k],
					'1',
					"mtagnew[setting][ccids$k][]",
					cls_catalog::ccidsarr($k,$sclass),
					empty($mtag['setting']['ccids'.$k]) ? array() : (is_array($mtag['setting']['ccids'.$k]) ? $mtag['setting']['ccids'.$k] : explode(',',$mtag['setting']['ccids'.$k]))
					);
	}
	tabfooter();
	tabheader('��������');
	$arr = array('' => '��ͨ�б�','in' => 'ָ��id�ļ����б�','belong' => 'ָ��id�������ϼ��б�','relate' => 'ָ��id�Ĺؼ�������ĵ��б�',);
	trbasic('ָ���б�ģʽ','mtagnew[setting][mode]',makeoption($arr,empty($mtag['setting']['mode']) ? 0 : $mtag['setting']['mode']),'select');
	$arr = array(0 => '������',);foreach($abrels as $k => $v) $arr[$k] = $v['cname'];
	trbasic('ָ���ϼ���Ŀ','mtagnew[setting][arid]',makeoption($arr,empty($mtag['setting']['arid']) ? 0 : $mtag['setting']['arid']),'select',array('guide' => '��ģʽΪ�����б�������ϼ��б�ʱ��Ҫָ��'));
	trbasic('ָ�����id','mtagnew[setting][id]',empty($mtag['setting']['id']) ? '' : $mtag['setting']['id'],'text',array('guide' => '�ֶ������ĵ�aid,����Ϊ��Ĭ��Ϊ�����ĵ�'));
	$arr = array('js' => 'ʹ��JS��̬���õ�ǰ��ʶ��������������',
	'space' => '����ʾ�����Ա���ĵ�',
	'ucsource' => 'ֻ��ʾ������˷�����ĵ�',
	'validperiod' => 'ֻ������Ч���ڵ�����',
	'detail' => '��Ҫģ���ֶα������(�����б�ֻ������ģ��ʱѡ�����Ч)',
	);
	$str = '';foreach($arr as $k => $v) $str .= "<input class=\"checkbox\" type=\"checkbox\" name=\"mtagnew[setting][$k]\" value=\"1\" ".(empty($mtag['setting'][$k]) ? '' : 'checked')."> &nbsp;$v<br>";
	trbasic('��ǰ��ʶ�ĸ�������','',$str,'');
	tabfooter();

	tabheader('�߼�ѡ��');
	$addstr = "&nbsp; >><a href=\"?entry=liststr&action=archives&typeid=$sclass\" target=\"_blank\">����</a>";
	$addstr .= "<br><input class=\"checkbox\" type=\"checkbox\" id=\"mtagnew[setting][isfunc]\" name=\"mtagnew[setting][isfunc]\"".(empty($mtag['setting']['isfunc']) ? '' : ' checked').">�ִ����Ժ���";
	$addstr .= "<br><input class=\"checkbox\" type=\"checkbox\" id=\"mtagnew[setting][isall]\" name=\"mtagnew[setting][isall]\"".(empty($mtag['setting']['isall']) ? '' : ' checked').">������ѯ�ִ�";
	trbasic('ɸѡ��ѯ�ִ�'.$addstr,'mtagnew[setting][wherestr]',empty($mtag['setting']['wherestr']) ? '' : $mtag['setting']['wherestr'],'textarea',array('guide' => '������ʽ��������(\'����1\',\'����2\')��������ѯ�ִ�����select��from��where,��Ҫ��order��limit��'));
	trbasic('��ѯ��������(��)','mtagnew[setting][ttl]',empty($mtag['setting']['ttl']) ? 0 : $mtag['setting']['ttl'],'text',array('guide' => '��λ���롣����չ���濪����ģ�����ģʽ�رյ��������Ч��'));
	tabfooter();
}else{
	if(empty($mtagnew['template'])) mtag_error('�������ʶģ��');
    $mtagnew['setting'][cls_mtags_archives::CHSOURCE] = (empty($mtagnew['setting'][cls_mtags_archives::CHIDS][0]) ? 1 : 2);
	@$mtagnew['setting']['ucsource'] = empty($mtagnew['setting']['space']) ? 0 : $mtagnew['setting']['ucsource'];
	$mtagnew['setting']['wherestr'] = empty($mtagnew['setting']['wherestr']) ? '' : trim($mtagnew['setting']['wherestr']);
	$mtagnew['setting']['isfunc'] = empty($mtagnew['setting']['isfunc']) || empty($mtagnew['setting']['wherestr']) ? 0 : 1;
	$mtagnew['setting']['isall'] = empty($mtagnew['setting']['isall']) || empty($mtagnew['setting']['wherestr']) ? 0 : 1;
	$mtagnew['setting']['ttl'] = max(0,intval($mtagnew['setting']['ttl']));
	
	$idvars = array('caids','nochids');//��������Ĵ���
	foreach($cotypes as $k => $cotype) $idvars[] = 'ccids'.$k;
	foreach($idvars as $k){
		if(empty($mtagnew['setting'][$k])){
			unset($mtagnew['setting'][$k]);
		}else $mtagnew['setting'][$k] = implode(',',$mtagnew['setting'][$k]);
	}
	if(empty($mtagnew['setting']['nochsource']) || !empty($mtagnew['setting'][cls_mtags_archives::CHSOURCE])) unset($mtagnew['setting']['nochids']);
	unset($mtagnew['setting']['nochsource']);
}