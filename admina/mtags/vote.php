<?php
(!defined('M_COM') || !defined('M_ADMIN')) && exit('No Permission');
if(!$modeSave){
	#$mtag = _tag_merge(@$mtag,@$mtagnew);
	templatebox('��ʶ��ģ��','mtagnew[template]',empty($mtag['template']) ? '' : $mtag['template'],10,110);
	trbasic('��Ϣ���õ��������','mtagnew[setting][val]',empty($mtag['setting']['val']) ? 'v' : $mtag['setting']['val'],'text',array('guide' => 'ϵͳĬ��Ϊv������ʶ����Ƕ��ʱ���ñ��Ҫ��ͬ�����¼���ʶ��[PHP����]�������ݵ���������<br> �ڱ���ʶģ����{xxx}��{$v[xxx]}���ɵ���xxx���ϣ���ʶ�����ֻ��ʹ��{$v[xxx]}��'));
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
	$i = 1;
	foreach($arr as $k => $v){
		echo "<input class=\"radio\" type=\"radio\" name=\"mtagnew[setting][type]\" value=\"$k\" onclick=\"\$id('vote_type1').style.display = 'none';\$id('vote_type2').style.display = '';\"".(@$mtag['setting']['type'] == $k ? ' checked' : '').">$v\n";
		echo $i % 6 ? '' : '<br>';
		$i ++;

	}
	echo "</td></tr>\n";
	echo "<tbody id=\"vote_type1\" style=\"display:".(empty($mtag['setting']['type']) ? '' : 'none')."\">";
	trbasic('�ֶ�ָ��ͶƱID','mtagnew[setting][vid]',empty($mtag['setting']['vid']) ? '0' : $mtag['setting']['vid'],'text',array('guide' => '����Ϊ����ͶƱ��'));
	echo "</tbody>";
	echo "<tbody id=\"vote_type2\" style=\"display:".(!empty($mtag['setting']['type']) ? '' : 'none')."\">";
	trbasic('*������Դ��¼id','mtagnew[setting][id]',isset($mtag['setting']['id']) ? $mtag['setting']['id'] : '','text');
	trbasic('*ָ��ͶƱ�ֶα�ʶ','mtagnew[setting][fname]',isset($mtag['setting']['fname']) ? $mtag['setting']['fname'] : '','text');
	echo "</tbody>";	
	trbasic('ͶƱѡ���б�������','mtagnew[setting][limits]',empty($mtag['setting']['limits']) ? '10' : $mtag['setting']['limits']);
	trbasic('�Ƿ�����JS��̬���ݵ���','mtagnew[setting][js]',empty($mtag['setting']['js']) ? 0 : $mtag['setting']['js'],'radio');
	trbasic('��ѯ��������(��)','mtagnew[setting][ttl]',empty($mtag['setting']['ttl']) ? 0 : $mtag['setting']['ttl'],'text',array('guide' => '��λ���롣����չ���濪����ģ�����ģʽ�رյ��������Ч��'));
	tabfooter();
}else{
	$mtagnew['setting']['fname'] = trim($mtagnew['setting']['fname']);
	if(empty($mtagnew['template'])) mtag_error('�������ʶģ��');
	if(!empty($mtagnew['setting']['type']) && (empty($mtagnew['setting']['id']) || empty($mtagnew['setting']['fname']) || !preg_match("/^[a-zA-Z_][a-zA-Z0-9_]*$/",$mtagnew['setting']['fname']))){
		mtag_error('����������������');
	}
	$mtagnew['setting']['vid'] = trim($mtagnew['setting']['vid']);
	$mtagnew['setting']['limits'] = empty($mtagnew['setting']['limits']) ? 10 : max(0,intval($mtagnew['setting']['limits']));
	$mtagnew['setting']['ttl'] = max(0,intval($mtagnew['setting']['ttl']));
}
?>
