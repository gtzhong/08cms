<?php
(!defined('M_COM') || !defined('M_ADMIN')) && exit('No Permission');
if(!$modeSave){
	#$mtag = _tag_merge(@$mtag,@$mtagnew);
	templatebox('��ʶ��ģ��','mtagnew[template]',empty($mtag['template']) ? '' : $mtag['template'],10,110);
	trbasic('��Ϣ���õ��������','mtagnew[setting][val]',empty($mtag['setting']['val']) ? 'v' : $mtag['setting']['val'],'text',array('guide' => 'ϵͳĬ��Ϊv������ʶ����Ƕ��ʱ���ñ��Ҫ��ͬ�����¼���ʶ��[PHP����]�������ݵ���������<br> �ڱ���ʶģ����{xxx}��{$v[xxx]}���ɵ���xxx���ϣ���ʶ�����ֻ��ʹ��{$v[xxx]}��'));
	trbasic('�б�����ʾ����������','mtagnew[setting][limits]',empty($mtag['setting']['limits']) ? '10' : $mtag['setting']['limits']);
	trbasic('�ӵڼ�����¼��ʼ��ʾ','mtagnew[setting][startno]',empty($mtag['setting']['startno']) ? '' : $mtag['setting']['startno'],'text',array('guide'=>'���ð���ǰ���õĵڼ�����¼��ʼ��Ĭ��Ϊ0��'));
	$arr = array(
		'archives' => '�ĵ�',
		'members' => '��Ա',
		'farchives' => '����',
		'catalogs' => '��Ŀ',
		'coclass' => '����',
	);
	echo "<tr class=\"txt\"><td class=\"txt txtright fB borderright\">ͶƱ����</td>\n";
	echo "<td class=\"txtL\">\n";
	echo "<input class=\"radio\" type=\"radio\" name=\"mtagnew[setting][type]\" value=\"\" onclick=\"\$id('vote_type1').style.display = '';\$id('vote_type2').style.display = 'none';\"".(empty($mtag['setting']['type']) ? ' checked' : '').">".'����ͶƱ'."\n";
	foreach($arr as $k => $v){
		echo "<input class=\"radio\" type=\"radio\" name=\"mtagnew[setting][type]\" value=\"$k\" onclick=\"\$id('vote_type1').style.display = 'none';\$id('vote_type2').style.display = '';\"".(@$mtag['setting']['type'] == $k ? ' checked' : '').">$v\n";
	}
	echo "</td></tr>\n";
	echo "<tbody id=\"vote_type1\" style=\"display:".(empty($mtag['setting']['type']) ? '' : 'none')."\">";
	$sourcearr = array('0' => '���޷���') + vcaidsarr();
	trbasic('ͶƱ��������','mtagnew[setting][vsource]',makeoption($sourcearr,empty($mtag['setting']['vsource']) ? '0' : $mtag['setting']['vsource']),'select');
	trbasic('�ֶ�ָ��ͶƱ','mtagnew[setting][vids]',empty($mtag['setting']['vids']) ? '' : $mtag['setting']['vids'],'text',array('guide' => '���ͶƱid�ö��ŷָ���'));
	echo "</tbody>";
	echo "<tbody id=\"vote_type2\" style=\"display:".(!empty($mtag['setting']['type']) ? '' : 'none')."\">";
	trbasic('*ָ��ͶƱ�ֶα�ʶ','mtagnew[setting][fname]',isset($mtag['setting']['fname']) ? $mtag['setting']['fname'] : '','text');
	trbasic('*������Դ��¼id','mtagnew[setting][id]',isset($mtag['setting']['id']) ? $mtag['setting']['id'] : '','text',array('guide' => '���������Ϣ��id�����ĵ�id��'));
	echo "</tbody>";	
	trbasic('�Ƿ�����JS��̬���ݵ���','mtagnew[setting][js]',empty($mtag['setting']['js']) ? 0 : $mtag['setting']['js'],'radio');
	trbasic('��ѯ��������(��)','mtagnew[setting][ttl]',empty($mtag['setting']['ttl']) ? 0 : $mtag['setting']['ttl'],'text',array('guide' => '��λ���롣����չ���濪����ģ�����ģʽ�رյ��������Ч��'));
	tabfooter();
	if(empty($_infragment)){
		tabheader('��ʶ��ҳ����');
		trbasic('�����б��ҳ','mtagnew[setting][mp]',empty($mtag['setting']['mp']) ? 0 : $mtag['setting']['mp'],'radio',array('guide' => 'ֻ�ж���ͶƱ��ҳ����Ч��'));
		trbasic('�ܽ����(��Ϊ����)','mtagnew[setting][alimits]',isset($mtag['setting']['alimits']) ? $mtag['setting']['alimits'] : '');
		trbasic('�Ƿ���׵ķ�ҳ����','mtagnew[setting][simple]',empty($mtag['setting']['simple']) ? '0' : $mtag['setting']['simple'],'radio');
		trbasic('��ҳ������ҳ�볤��','mtagnew[setting][length]',isset($mtag['setting']['length']) ? $mtag['setting']['length'] : '');
		tabfooter();
	}	
}else{
	$mtagnew['setting']['fname'] = trim($mtagnew['setting']['fname']);
	if(empty($mtagnew['template'])) mtag_error('�������ʶģ��');
	if(!empty($mtagnew['setting']['type']) && (empty($mtagnew['setting']['id']) || empty($mtagnew['setting']['fname']) || !preg_match("/^[a-zA-Z_][a-zA-Z0-9_]*$/",$mtagnew['setting']['fname']))){
		mtag_error('����������������');
	}
	$mtagnew['setting']['startno'] = trim($mtagnew['setting']['startno']);
	$mtagnew['setting']['limits'] = empty($mtagnew['setting']['limits']) ? 10 : max(0,intval($mtagnew['setting']['limits']));
	$mtagnew['setting']['alimits'] = max(0,intval($mtagnew['setting']['alimits']));
	$mtagnew['setting']['length'] = max(0,intval($mtagnew['setting']['length']));
	$mtagnew['setting']['vids'] = empty($mtagnew['setting']['vids']) ? '' : trim($mtagnew['setting']['vids']);
	$mtagnew['setting']['ttl'] = max(0,intval($mtagnew['setting']['ttl']));
	!empty($mtagnew['setting']['type']) && $mtagnew['setting']['mp'] = 0;
	if($mtagnew['setting']['vids']){
		$vids = array_filter(explode(',',$mtagnew['setting']['vids']));
		$mtagnew['setting']['vids'] = empty($vids) ? '' : implode(',',$vids);
	}
}
?>
