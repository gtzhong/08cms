<?php
(!defined('M_COM') || !defined('M_ADMIN')) && exit('No Permission');
if(!$modeSave){
	templatebox('��ʶ��ģ��','mtagnew[template]',empty($mtag['template']) ? '' : $mtag['template'],10,110);
	trbasic('��Ϣ���õ��������','mtagnew[setting][val]',empty($mtag['setting']['val']) ? 'v' : $mtag['setting']['val'],'text',array('guide' => 'ϵͳĬ��Ϊv������ʶ����Ƕ��ʱ���ñ��Ҫ��ͬ�����¼���ʶ��[PHP����]�������ݵ���������<br> �ڱ���ʶģ����{xxx}��{$v[xxx]}���ɵ���xxx���ϣ���ʶ�����ֻ��ʹ��{$v[xxx]}��'));			
    if($sclass === 'ca')
    {
        $sourcearr = array(
			'active' => '������Ŀ',
		);
    }
    else
    {
        $sourcearr = array(
			'0' => '�ǹ�����',
			'active' => '������Ŀ',
		);
    }
	$sourcearr = $sourcearr + cls_catalog::ccidsarr(0);
	trbasic('��Ŀ' . ($sclass === 'ca' ? '<span style="color:red">(��Ϊ�б���)</span>' : ''),
	'',"<select onchange=\"setIdWithS(this)\" id=\"mselect_mtagnew[setting][casource]\" style=\"vertical-align: middle;\">" . makeoption($sourcearr,(empty($_POST) ? @$mtag['setting']['casource'] : 0)) . "</select><input type=\"text\" value=\"".(empty($_POST) ? @$mtag['setting']['casource'] : 0)."\" onfocus=\"setIdWithI(this)\" name=\"mtagnew[setting][casource]\" id=\"mtagnew[setting][casource]\"/>",'');

	foreach($cotypes as $k => $cotype) {
		if($cotype['sortable']){
            if($sclass === ('co'.$k))
            {
                $sourcearr = array(
    				'active' => '������Ŀ',
    			);
            }
            else
            {
                $sourcearr = array(
    				'0' => '�ǹ�����',
    				'active' => '������Ŀ',
    			);
            }
			$sourcearr = $sourcearr + cls_catalog::ccidsarr($k);	
            isset($mtag['setting']['cosource'.$k]) || $mtag['setting']['cosource'.$k] = '0';
			trbasic($cotype['cname'] . ($sclass === ('co'.$k) ? '<span style="color:red">(�ڵ�չʾ��)</span>' : ''),
			'',"<select onchange=\"setIdWithS(this)\" id=\"mselect_mtagnew[setting][cosource$k]\" style=\"vertical-align: middle;\">" . makeoption($sourcearr,empty($_POST) ? @$mtag['setting']['cosource'.$k] : 0) . "</select><input type=\"text\" value=\"".(empty($_POST) ? @$mtag['setting']['cosource'.$k] : 0)."\" onfocus=\"setIdWithI(this)\" name=\"mtagnew[setting][cosource$k]\" id=\"mtagnew[setting][cosource$k]\"/>",'');
		}
	}
	$levelarr = array('0' => '��׷��','1' => 'һ��','2' => '����','3' => '����',);
	trbasic('׷��ָ���б���Ŀ���ϼ���Ŀ','',makeradio('mtagnew[setting][level]',$levelarr,isset($mtag['setting']['level']) ? $mtag['setting']['level'] : '0'),'');
	$arr = array('js' => 'ʹ��JS��̬���õ�ǰ��ʶ��������������',);
	$str = '';foreach($arr as $k => $v) $str .= "<input class=\"checkbox\" type=\"checkbox\" name=\"mtagnew[setting][$k]\" value=\"1\" ".(empty($mtag['setting'][$k]) ? '' : 'checked')."> &nbsp;$v<br>";
	trbasic('��ǰ��ʶ�ĸ�������','',$str,'');
	tabfooter();
}else{
	if(empty($mtagnew['template'])) mtag_error('�������ʶģ��');
	if($mtagnew['setting']['listby'] == 'ca' && empty($mtagnew['setting']['casource'])){
		$mtagnew['setting']['casource'] = 'active';
	}elseif(preg_match("/^co(\d+)/is",$mtagnew['setting']['listby'],$matches)){
		if(empty($mtagnew['setting']['cosource'.$matches[1]])) $mtagnew['setting']['cosource'.$matches[1]] = 'active';
	}
}
?>
