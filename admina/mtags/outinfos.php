<?php
(!defined('M_COM') || !defined('M_ADMIN')) && exit('No Permission');
if(!$modeSave){
	#$mtag = _tag_merge(@$mtag,@$mtagnew);
	templatebox('��ʶ��ģ��','mtagnew[template]',empty($mtag['template']) ? '' : $mtag['template'],10,110);
	trbasic('��Ϣ���õ��������','mtagnew[setting][val]',empty($mtag['setting']['val']) ? 'v' : $mtag['setting']['val'],'text',array('guide' => 'ϵͳĬ��Ϊv������ʶ����Ƕ��ʱ���ñ��Ҫ��ͬ�����¼���ʶ��[PHP����]�������ݵ���������<br> �ڱ���ʶģ����{xxx}��{$v[xxx]}���ɵ���xxx���ϣ���ʶ�����ֻ��ʹ��{$v[xxx]}��'));
	trbasic('�б�����ʾ����������','mtagnew[setting][limits]',empty($mtag['setting']['limits']) ? 10 : $mtag['setting']['limits']);
	trbasic('�ӵڼ�����¼��ʼ��ʾ','mtagnew[setting][startno]',empty($mtag['setting']['startno']) ? '' : $mtag['setting']['startno'],'text',array('guide'=>'���ð���ǰ���õĵڼ�����¼��ʼ��Ĭ��Ϊ0��'));
	$arr = array('js' => 'ʹ��JS��̬���õ�ǰ��ʶ��������������',);
	$str = '';foreach($arr as $k => $v) $str .= "<input class=\"checkbox\" type=\"checkbox\" name=\"mtagnew[setting][$k]\" value=\"1\" ".(empty($mtag['setting'][$k]) ? '' : 'checked')."> &nbsp;$v<br>";
	trbasic('��ǰ��ʶ�ĸ�������','',$str,'');
	tabfooter();

	tabheader('��������');
	$dsidsarr = array(0 => '��ǰϵͳ');
	foreach($dbsources as $k => $v) $dsidsarr[$k] = $v['cname'];
	$str = "<select style=\"vertical-align: middle;\" name=\"mtagnew[setting][dsid]\" onchange=\"\$id('link_mtagnew_setting_dsid').innerHTML='>><a href=\'?entry=dbsources&action=viewconfigs&dsid=' + this.options[this.selectedIndex].value + '\' target=\'_blank\'>".'�鿴�ṹ'."</a>';\">".
		makeoption($dsidsarr,empty($mtag['setting']['dsid']) ? 0 : $mtag['setting']['dsid']).
		"</select>&nbsp; &nbsp; <span id=\"link_mtagnew_setting_dsid\">>><a href=\"?entry=dbsources&action=viewconfigs&dsid=".(empty($mtag['setting']['dsid']) ? 0 : $mtag['setting']['dsid'])."\" target=\"_blank\">�鿴�ṹ</a></span>";
	trbasic('�ⲿ����Դ','',$str,'',array('guide'=>">><a href=\"?entry=dbsources&action=dbsourcesedit&isframe=1\" target=\"_blank\">�����ⲿ����Դ</a>"));
	trbasic('* ɸѡ��ѯ�ִ�','mtagnew[setting][wherestr]',empty($mtag['setting']['wherestr']) ? '' : $mtag['setting']['wherestr'],'textarea',array('guide' => '��ѯ�ִ�����select��from��where��order,��Ҫ��limit��'));
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
}else{
	if(empty($mtagnew['template'])) mtag_error('�������ʶģ��');
	$mtagnew['setting']['startno'] = trim($mtagnew['setting']['startno']);
	$mtagnew['setting']['limits'] = empty($mtagnew['setting']['limits']) ? 10 : max(0,intval($mtagnew['setting']['limits']));
	$mtagnew['setting']['alimits'] = max(0,intval($mtagnew['setting']['alimits']));
	$mtagnew['setting']['length'] = max(0,intval($mtagnew['setting']['length']));
	$mtagnew['setting']['wherestr'] = empty($mtagnew['setting']['wherestr']) ? '' : trim($mtagnew['setting']['wherestr']);
	$mtagnew['setting']['ttl'] = max(0,intval($mtagnew['setting']['ttl']));
	if(empty($mtagnew['setting']['wherestr'])) mtag_error('�������ѯ�ִ�');
}
?>
