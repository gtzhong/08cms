<?php
(!defined('M_COM') || !defined('M_ADMIN')) && exit('No Permission');
if(!$modeSave){
	#$mtag = _tag_merge(@$mtag,@$mtagnew);
	templatebox('��ʶ��ģ��','mtagnew[template]',empty($mtag['template']) ? '' : $mtag['template'],10,110);
	trbasic('��Ϣ���õ��������','mtagnew[setting][val]',empty($mtag['setting']['val']) ? 'v' : $mtag['setting']['val'],'text',array('guide' => 'ϵͳĬ��Ϊv������ʶ����Ƕ��ʱ���ñ��Ҫ��ͬ�����¼���ʶ��[PHP����]�������ݵ���������<br> �ڱ���ʶģ����{xxx}��{$v[xxx]}���ɵ���xxx���ϣ���ʶ�����ֻ��ʹ��{$v[xxx]}��'));
	tabfooter();

	tabheader('����ɸѡ����');
	foreach($grouptypes as $gtid => $grouptype){
		$ugidsarr = array('0' => '���޻�Ա��') + ugidsarr($grouptype['gtid']);
		trbasic("$grouptype[cname]".'ɸѡ','mtagnew[setting][ugid'.$gtid.']',makeoption($ugidsarr,empty($mtag['setting']['ugid'.$gtid]) ? 0 : $mtag['setting']['ugid'.$gtid]),'select');
	}
/*
	$chsourcearr = array('0' => '����ģ��','1' => '����ģ��','2' => '�ֶ�ָ��',);
	sourcemodule('��Աģ������',
				'mtagnew[setting][chsource]',
				$chsourcearr,
				empty($mtag['setting']['chsource']) ? '' : $mtag['setting']['chsource'],
				'2',
				'mtagnew[setting][chids][]',
				cls_mchannel::mchidsarr(),
				!empty($mtag['setting']['chids']) ? (is_array($mtag['setting']['chids']) ? $mtag['setting']['chids'] : explode(',',$mtag['setting']['chids'])) : array()
				);*/
	tabfooter();
	
	tabheader('��������');
	$arr = array('' => '��ͨ�б�','in' => 'ָ��id�ļ����б�','belong' => 'ָ��id�������ϼ��б�',);
	trbasic('ָ���б�ģʽ','mtagnew[setting][mode]',makeoption($arr,empty($mtag['setting']['mode']) ? 0 : $mtag['setting']['mode']),'select');
	$arr = array(0 => '������',);foreach($abrels as $k => $v) $arr[$k] = $v['cname'];
	trbasic('ָ���ϼ���Ŀ','mtagnew[setting][arid]',makeoption($arr,empty($mtag['setting']['arid']) ? 0 : $mtag['setting']['arid']),'select',array('guide' => '��ģʽΪ�����б�������ϼ��б�ʱ��Ҫָ��'));
	trbasic('ָ�����id','mtagnew[setting][id]',empty($mtag['setting']['id']) ? '' : $mtag['setting']['id'],'text',array('guide' => '�ֶ������Աmid,����Ϊ��Ĭ��Ϊ�����Ա'));
	tabfooter();
	
	tabheader('�߼�ѡ��');
	$arr = array('js' => 'ʹ��JS��̬���õ�ǰ��ʶ��������������','detail' => '��Ҫģ���ֶα������(�����б�ֻ������ģ��ʱѡ�����Ч)',);
	$str = '';foreach($arr as $k => $v) $str .= "<input class=\"checkbox\" type=\"checkbox\" name=\"mtagnew[setting][$k]\" value=\"1\" ".(empty($mtag['setting'][$k]) ? '' : 'checked')."> &nbsp;$v<br>";
	trbasic('��ǰ��ʶ�ĸ�������','',$str,'');
	$addstr = "&nbsp; >><a href=\"?entry=liststr&action=members\" target=\"_blank\">����</a>";
	$addstr .= "<br><input class=\"checkbox\" type=\"checkbox\" id=\"mtagnew[setting][isfunc]\" name=\"mtagnew[setting][isfunc]\"".(empty($mtag['setting']['isfunc']) ? '' : ' checked').">�ִ����Ժ���";
	$addstr .= "<br><input class=\"checkbox\" type=\"checkbox\" id=\"mtagnew[setting][isall]\" name=\"mtagnew[setting][isall]\"".(empty($mtag['setting']['isall']) ? '' : ' checked').">������ѯ�ִ�";
	trbasic('ɸѡ��ѯ�ִ�'.$addstr,'mtagnew[setting][wherestr]',empty($mtag['setting']['wherestr']) ? '' : $mtag['setting']['wherestr'],'textarea',array('guide' => '������ʽ��������(\'����1\',\'����2\')��������ѯ�ִ�����select��from��where,��Ҫ��order��limit��'));
	trbasic('��ѯ��������(��)','mtagnew[setting][ttl]',empty($mtag['setting']['ttl']) ? 0 : $mtag['setting']['ttl'],'text',array('guide' => '��λ���롣����չ���濪����ģ�����ģʽ�رյ��������Ч��'));
	tabfooter();
	
}else{
	if(empty($mtagnew['template'])) mtag_error('�������ʶģ��');
	$mtagnew['setting']['wherestr'] = empty($mtagnew['setting']['wherestr']) ? '' : trim($mtagnew['setting']['wherestr']);
	$mtagnew['setting']['isfunc'] = empty($mtagnew['setting']['isfunc']) || empty($mtagnew['setting']['wherestr']) ? 0 : 1;
	$mtagnew['setting']['ttl'] = max(0,intval($mtagnew['setting']['ttl']));

	//��������Ĵ���
	$idvars = array('chids',);
	foreach($idvars as $k){
		if(empty($mtagnew['setting'][$k])){
			unset($mtagnew['setting'][$k]);
		}else $mtagnew['setting'][$k] = implode(',',$mtagnew['setting'][$k]);
	}
}
?>
