<?php
(!defined('M_COM') || !defined('M_ADMIN')) && exit('No Permission');
if(!$modeSave){
	#$mtag = _tag_merge(@$mtag,@$mtagnew);
	templatebox('��ʶ��ģ��','mtagnew[template]',empty($mtag['template']) ? '' : $mtag['template'],10,110);
	$arr = cls_cache::exRead('cfregcodes');
	trbasic('*��֤������','mtagnew[setting][type]',makeoption($arr,empty($mtag['setting']['type']) ? '' : $mtag['setting']['type']),'select');
	$arr = array();
	$arr['js'] = 'ʹ��JS��̬���õ�ǰ��ʶ��������������';
	$str = '';foreach($arr as $k => $v) $str .= "<input class=\"checkbox\" type=\"checkbox\" name=\"mtagnew[setting][$k]\" value=\"1\" ".(empty($mtag['setting'][$k]) ? '' : 'checked')."> &nbsp;$v<br>";
	trbasic('��ǰ��ʶ�ĸ�������','',$str,'');
	tabfooter();
}else{
	if(empty($mtagnew['template'])) mtag_error('�������ʶģ��');
	if(empty($mtagnew['setting']['type'])){
		mtag_error('��֤�����Ͳ��Ϲ淶');
	}
}
?>
