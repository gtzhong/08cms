<?php
(!defined('M_COM') || !defined('M_ADMIN')) && exit('No Permission');
if(!$modeSave){
	templatebox('��ʶ��ģ��','mtagnew[template]',empty($mtag['template']) ? '' : $mtag['template'],10,110);
	trbasic('��Ϣ���õ��������','mtagnew[setting][val]',empty($mtag['setting']['val']) ? 'v' : $mtag['setting']['val'],'text',array('guide' => 'ϵͳĬ��Ϊv������ʶ����Ƕ��ʱ���ñ��Ҫ��ͬ�����¼���ʶ��[PHP����]�������ݵ���������<br> �ڱ���ʶģ����{xxx}��{$v[xxx]}���ɵ���xxx���ϣ���ʶ�����ֻ��ʹ��{$v[xxx]}��'));
	trbasic('�б�����ʾ����������','mtagnew[setting][limits]',empty($mtag['setting']['limits']) ? 10 : $mtag['setting']['limits']);
	trbasic('�ӵڼ�����¼��ʼ��ʾ','mtagnew[setting][startno]',empty($mtag['setting']['startno']) ? '' : $mtag['setting']['startno'],'text',array('guide'=>'���ð���ǰ���õĵڼ�����¼��ʼ��Ĭ��Ϊ0��'));
	tabfooter();
	
	$pafields = cls_PushArea::Field(empty($mtag['setting']['paid'])?$sclass:$mtag['setting']['paid']);
	$sarr = array();
	for($i = 1;$i < 3;$i++){
		if($v = @$pafields["classid$i"]){
			$sarr[$i]['title'] = $v['cname']." - classid$i";
			$sarr[$i]['options'] = array(0 => '����������',) + cls_field::options_simple($v);
		}
	}
	if($sarr){
		tabheader('��������');
		foreach($sarr as $k => $v){
			$str = "<select onchange=\"setIdWithS(this)\" id=\"mselect_mtagnew[setting][classid$k]\" style=\"vertical-align: middle;\">".makeoption($v['options'],@$mtag['setting']['classid'.$k])."</select>";
			$str .= "<input type=\"text\" value=\"".@$mtag['setting']['classid'.$k]."\" onfocus=\"setIdWithI(this)\" name=\"mtagnew[setting][classid$k]\" id=\"mtagnew[setting][classid$k]\"/>";
			trbasic($v['title'],'',$str,'',array('guide' => '���ֶ�����$xxx��$v[xxx]�ȼ������(ҳ����ϼ���ʶ���ݵĿ��ñ���)'));
		}
		tabfooter();
	}
	
	tabheader('�߼�����');
	$arr = array();
	empty($_infragment) && $arr['js'] = 'ʹ��JS��̬���õ�ǰ��ʶ��������������';
	$str = '';foreach($arr as $k => $v) $str .= "<input class=\"checkbox\" type=\"checkbox\" name=\"mtagnew[setting][$k]\" value=\"1\" ".(empty($mtag['setting'][$k]) ? '' : 'checked')."> &nbsp;$v<br>";
	trbasic('��ǰ��ʶ�ĸ�������','',$str,'');
	$addstr = "&nbsp; >><a href=\"?entry=liststr&action=pushs&typeid=$sclass\" target=\"_blank\">����</a>";
	$addstr .= "<br><input class=\"checkbox\" type=\"checkbox\" id=\"mtagnew[setting][isfunc]\" name=\"mtagnew[setting][isfunc]\"".(empty($mtag['setting']['isfunc']) ? '' : ' checked').">�ִ����Ժ���";
	$addstr .= "<br><input class=\"checkbox\" type=\"checkbox\" id=\"mtagnew[setting][isall]\" name=\"mtagnew[setting][isall]\"".(empty($mtag['setting']['isall']) ? '' : ' checked').">������ѯ�ִ�";
	trbasic('ɸѡ��ѯ�ִ�'.$addstr,'mtagnew[setting][wherestr]',empty($mtag['setting']['wherestr']) ? '' : $mtag['setting']['wherestr'],'textarea',array('guide' => '������ʽ��������(\'����1\',\'����2\')��������ѯ�ִ�����select��from��where,��Ҫ��order��limit��'));
	trbasic('��ѯ��������(��)','mtagnew[setting][ttl]',empty($mtag['setting']['ttl']) ? 0 : $mtag['setting']['ttl'],'text',array('guide' => '��λ���롣����չ���濪����ģ�����ģʽ�رյ��������Ч��'));
	tabfooter();
}else{
	if(empty($mtagnew['template'])) mtag_error('�������ʶģ��');
	if(!isset($mtagnew['setting'][cls_mtags_pushs::SCLASS_VAL]) || $mtagnew['setting'][cls_mtags_pushs::SCLASS_VAL] == '') mtag_error('��ָ����ȷ������λ');
	$mtagnew['setting']['startno'] = trim($mtagnew['setting']['startno']);
	$mtagnew['setting']['limits'] = empty($mtagnew['setting']['limits']) ? 10 : max(0,intval($mtagnew['setting']['limits']));
	$mtagnew['setting']['wherestr'] = empty($mtagnew['setting']['wherestr']) ? '' : trim($mtagnew['setting']['wherestr']);
	$mtagnew['setting']['isfunc'] = empty($mtagnew['setting']['isfunc']) || empty($mtagnew['setting']['wherestr']) ? 0 : 1;
	$mtagnew['setting']['isall'] = empty($mtagnew['setting']['isall']) || empty($mtagnew['setting']['wherestr']) ? 0 : 1;
	$mtagnew['setting']['ttl'] = max(0,intval($mtagnew['setting']['ttl']));
}
