<?php
(!defined('M_COM') || !defined('M_ADMIN')) && exit('No Permission');

if(!$modeSave){
	templatebox('��ʶ��ģ��','mtagnew[template]',empty($mtag['template']) ? '' : $mtag['template'],10,110);
	trbasic('��Ϣ���õ��������','mtagnew[setting][val]',empty($mtag['setting']['val']) ? 'v' : $mtag['setting']['val'],'text',array('guide' => 'ϵͳĬ��Ϊv������ʶ����Ƕ��ʱ���ñ��Ҫ��ͬ�����¼���ʶ��[PHP����]�������ݵ���������<br> �ڱ���ʶģ����{xxx}��{$v[xxx]}���ɵ���xxx���ϣ���ʶ�����ֻ��ʹ��{$v[xxx]}��'));
	trbasic('�б�����ʾ����������','mtagnew[setting][limits]',empty($mtag['setting']['limits']) ? 10 : $mtag['setting']['limits']);
	trbasic('�ӵڼ�����¼��ʼ��ʾ','mtagnew[setting][startno]',empty($mtag['setting']['startno']) ? '' : $mtag['setting']['startno'],'text',array('guide' => '���ð���ǰ���õĵڼ�����¼��ʼ��Ĭ��Ϊ0��'));
/*	$chsourcearr = array('0' => '����ģ��','1' => '����ģ��','2' => '�ֶ�ָ��',);
	sourcemodule('��Աģ������',
				'mtagnew[setting][chsource]',
				$chsourcearr,
				empty($mtag['setting']['chsource']) ? '' : $mtag['setting']['chsource'],
				'2',
				'mtagnew[setting][chids][]',
				cls_mchannel::mchidsarr(),
				!empty($mtag['setting']['chids']) ? (is_array($mtag['setting']['chids']) ? $mtag['setting']['chids'] : explode(',',$mtag['setting']['chids'])) : array()
				);*/
                #var_dump($mchannels['mchid']);
	echo "<script>function setdisabled(showid,hideid){var showobj=\$id(showid),hideobj=\$id(hideid),sinput=showobj.getElementsByTagName('input');hinput=hideobj.getElementsByTagName('input');showobj.style.display='';hideobj.style.display='none';for(var i=0;i<sinput.length;i++){sinput[i].disabled=false}for(var i=0;i<hinput.length;i++){hinput[i].disabled=true}}</script>";
	echo "<script>window.onload = function(){setdisabled(".(empty($mtag['setting']['ids'])?"'ids_mod1','ids_mod2'":"'ids_mod2','ids_mod1'").");}</script>";
	$str = "<input class=\"radio\" type=\"radio\" name=\"select_mode\" value=\"0\" onclick=\"setdisabled('ids_mod1','ids_mod2');\"".(empty($mtag['setting']['ids']) ? ' checked' : '').">�������� &nbsp;\n";
	$str .= "<input class=\"radio\" type=\"radio\" name=\"select_mode\" value=\"1\" onclick=\"setdisabled('ids_mod2','ids_mod1');\"".(empty($mtag['setting']['ids']) ? '' : ' checked').">�ֶ�ָ��id<br>\n";
	trbasic('�б��������÷�ʽ>>','',$str,'');
	tabfooter();

	echo "<div id=\"ids_mod2\" style=\"display:".(empty($mtag['setting']['ids']) ? 'none' : '')."\">";
	tabheader('�ֶ�ָ������');
	trbasic('*�ֶ�ָ���б�id','mtagnew[setting][ids]',empty($mtag['setting']['ids']) ? '' : $mtag['setting']['ids'],'text',array('guide' => 'ָ�����idʹ�ð�Ƕ��ŷָ����磺5,80,600','w' => 50,));
	$addstr = "&nbsp; >><a href=\"?entry=liststr&action=archives\" target=\"_blank\">����</a>";
	trbasic('�����ִ�'.$addstr,'mtagnew[setting][orderstr]',empty($mtag['setting']['orderstr']) ? '' : $mtag['setting']['orderstr'],'text',array('w' => 50));
	trbasic('ǿ�������ִ�','mtagnew[setting][forceindex]',empty($mtag['setting']['forceindex']) ? '' : $mtag['setting']['forceindex'],'text',array('guide' => '��ʽ������a.mclicks,������ǰ��ȷ�ϵ�ǰ�Ĳ�ѯ�а���a�����ı����ñ��н���mclicks��������'));
	trbasic('��ѯ��������(��)','mtagnew[setting][ttl]',empty($mtag['setting']['ttl']) ? 0 : $mtag['setting']['ttl'],'text',array('guide' => '��λ���롣����չ���濪����ģ�����ģʽ�رյ��������Ч��'));
	$arr = array();
	empty($_infragment) && $arr['js'] = 'ʹ��JS��̬���õ�ǰ��ʶ��������������';
	$arr['detail'] = '��Ҫģ���ֶα������(�����б�ֻ������ģ��ʱѡ�����Ч)';
	$str = '';foreach($arr as $k => $v) $str .= "<input class=\"checkbox\" type=\"checkbox\" name=\"mtagnew[setting][$k]\" value=\"1\" ".(empty($mtag['setting'][$k]) ? '' : 'checked')."> &nbsp;$v<br>";
	trbasic('��ǰ��ʶ�ĸ�������','',$str,'');
	tabfooter();
	echo '</div>';	

	echo "<div id=\"ids_mod1\" style=\"display:".(empty($mtag['setting']['ids']) ? '' : 'none')."\">";
	tabheader('����ɸѡ����');
	foreach($grouptypes as $gtid => $grouptype){
		$ugidsarr = array('0' => '���޻�Ա��') + ugidsarr($grouptype['gtid']);
		trbasic("$grouptype[cname]".'ɸѡ','mtagnew[setting][ugid'.$gtid.']',makeoption($ugidsarr,empty($mtag['setting']['ugid'.$gtid]) ? 0 : $mtag['setting']['ugid'.$gtid]),'select');
	}
	tabfooter();
	
	tabheader('��������');
	$arr = array('' => '��ͨ�б�','in' => 'ָ��id�ļ����б�','belong' => 'ָ��id�������ϼ��б�',);
	trbasic('ָ���б�ģʽ','mtagnew[setting][mode]',makeoption($arr,empty($mtag['setting']['mode']) ? 0 : $mtag['setting']['mode']),'select');
	$arr = array(0 => '������',);foreach($abrels as $k => $v) $arr[$k] = $v['cname'];
	trbasic('ָ���ϼ���Ŀ','mtagnew[setting][arid]',makeoption($arr,empty($mtag['setting']['arid']) ? 0 : $mtag['setting']['arid']),'select',array('guide' => '��ģʽΪ�����б�������ϼ��б�ʱ��Ҫָ��'));
	trbasic('ָ�����id','mtagnew[setting][id]',empty($mtag['setting']['id']) ? '' : $mtag['setting']['id'],'text',array('guide' => '�ֶ������Աmid,����Ϊ��Ĭ��Ϊ�����Ա'));
	tabfooter();
	
	tabheader('�߼�ѡ��');
	$arr = array();
	empty($_infragment) && $arr['js'] = 'ʹ��JS��̬���õ�ǰ��ʶ��������������';
	$arr['detail'] = '��Ҫģ���ֶα������(�����б�ֻ������ģ��ʱѡ�����Ч)';
	
	$str = '';foreach($arr as $k => $v) $str .= "<input class=\"checkbox\" type=\"checkbox\" name=\"mtagnew[setting][$k]\" value=\"1\" ".(empty($mtag['setting'][$k]) ? '' : 'checked')."> &nbsp;$v<br>";
	trbasic('��ǰ��ʶ�ĸ�������','',$str,'');
	$addstr = "&nbsp; >><a href=\"?entry=liststr&action=members\" target=\"_blank\">����</a>";
	trbasic('�����ִ�'.$addstr,'mtagnew[setting][orderstr]',empty($mtag['setting']['orderstr']) ? '' : $mtag['setting']['orderstr'],'text',array('w' => 50));
	$addstr .= "<br><input class=\"checkbox\" type=\"checkbox\" id=\"mtagnew[setting][isfunc]\" name=\"mtagnew[setting][isfunc]\"".(empty($mtag['setting']['isfunc']) ? '' : ' checked').">�ִ����Ժ���";
	$addstr .= "<br><input class=\"checkbox\" type=\"checkbox\" id=\"mtagnew[setting][isall]\" name=\"mtagnew[setting][isall]\"".(empty($mtag['setting']['isall']) ? '' : ' checked').">������ѯ�ִ�";
	trbasic('ɸѡ��ѯ�ִ�'.$addstr,'mtagnew[setting][wherestr]',empty($mtag['setting']['wherestr']) ? '' : $mtag['setting']['wherestr'],'textarea',array('guide' => '������ʽ��������(\'����1\',\'����2\')��������ѯ�ִ�����select��from��where,��Ҫ��order��limit��'));
	trbasic('ǿ�������ִ�','mtagnew[setting][forceindex]',empty($mtag['setting']['forceindex']) ? '' : $mtag['setting']['forceindex'],'text',array('guide' => '��ʽ������a.mclicks,������ǰ��ȷ�ϵ�ǰ�Ĳ�ѯ�а���a�����ı����ñ��н���mclicks��������'));
	trbasic('��ѯ��������(��)','mtagnew[setting][ttl]',empty($mtag['setting']['ttl']) ? 0 : $mtag['setting']['ttl'],'text',array('guide' => '��λ���롣����չ���濪����ģ�����ģʽ�رյ��������Ч��'));
	tabfooter();
	echo '</div>';	
	
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
	if(!empty($select_mode) && empty($mtagnew['setting']['ids'])) mtag_error('���ֶ�ָ��id');
	$mtagnew['setting']['limits'] = empty($mtagnew['setting']['limits']) ? 10 : max(0,intval($mtagnew['setting']['limits']));
	$mtagnew['setting']['orderstr'] = empty($mtagnew['setting']['orderstr']) ? '' : trim($mtagnew['setting']['orderstr']);
	$mtagnew['setting']['wherestr'] = empty($mtagnew['setting']['wherestr']) ? '' : trim($mtagnew['setting']['wherestr']);
	$mtagnew['setting']['isfunc'] = empty($mtagnew['setting']['isfunc']) || empty($mtagnew['setting']['wherestr']) ? 0 : 1;
	$mtagnew['setting']['startno'] = trim($mtagnew['setting']['startno']);
	$mtagnew['setting']['ttl'] = max(0,intval($mtagnew['setting']['ttl']));
	//��������Ĵ���
	$idvars = array('chids',);
	foreach($idvars as $k){
		if(empty($mtagnew['setting'][$k])){
			unset($mtagnew['setting'][$k]);
		}else $mtagnew['setting'][$k] = implode(',',$mtagnew['setting'][$k]);
	}
	$mtagnew['setting']['forceindex'] = trim($mtagnew['setting']['forceindex']);
	if(empty($mtagnew['setting']['forceindex'])) unset($mtagnew['setting']['forceindex']);
	if(empty($select_mode)){
		unset($mtagnew['setting']['ids']);
	}else{
		$idvars = array('wherestr','isfunc','isall','mode','arid','id',);
		foreach($grouptypes as $k => $v){
			$idvars[] = 'ugid'.$k;
		}
		foreach($idvars as $k) unset($mtagnew['setting'][$k]);
	}
}
?>
