<?php
(!defined('M_COM') || !defined('M_ADMIN')) && exit('No Permission');
if(!$modeSave){
	#$mtag = _tag_merge(@$mtag,@$mtagnew);
	templatebox('��ʶ��ģ��','mtagnew[template]',empty($mtag['template']) ? '' : $mtag['template'],10,110);
	trbasic('* ָ��������Դ','mtagnew[setting][tname]',isset($mtag['setting']['tname']) ? $mtag['setting']['tname'] : '','text',array('guide' => '�����ʽ���ֶ���aa������$a[b]�ȡ�'));
	trbasic('��Ϣ���õ��������','mtagnew[setting][val]',empty($mtag['setting']['val']) ? 'v' : $mtag['setting']['val'],'text',array('guide' => 'ϵͳĬ��Ϊv������ʶ����Ƕ��ʱ���ñ��Ҫ��ͬ�����¼���ʶ��[PHP����]�������ݵ���������<br> �ڱ���ʶģ����{xxx}��{$v[xxx]}���ɵ���xxx���ϣ���ʶ�����ֻ��ʹ��{$v[xxx]}��'));
	trbasic('��������','mtagnew[setting][limits]',isset($mtag['setting']['limits']) ? $mtag['setting']['limits'] : '');
	trbasic('ͼƬ�������','mtagnew[setting][maxwidth]',isset($mtag['setting']['maxwidth']) ? $mtag['setting']['maxwidth'] : '');
	trbasic('ͼƬ�߶�����','mtagnew[setting][maxheight]',isset($mtag['setting']['maxheight']) ? $mtag['setting']['maxheight'] : '');
	$arr = array(0 => '����������ͼ',1 => '��ѻ�����ͼƬ',2 => '��������ͼƬ',);
	trbasic('���趨�ߴ���������ͼ','',makeradio('mtagnew[setting][thumb]',$arr,isset($mtag['setting']['thumb']) ? $mtag['setting']['thumb'] : 0),'');
	trbasic('����ͼ�Ƿ񲹰�','',makeradio('mtagnew[setting][padding]',array(1=>'��',0=>'��'),isset($mtag['setting']['padding']) ? $mtag['setting']['padding'] : 1),'',array('guide'=>'Ĭ�ϲ���(��������ͼƬ)��'));
	trspecial('��ȱͼƬurl',specialarr(array('type' => 'image','varname' => 'mtagnew[setting][emptyurl]','value' => isset($mtag['setting']['emptyurl']) ? $mtag['setting']['emptyurl'] : '',)));
	trbasic('��ȱͼƬ˵��','mtagnew[setting][emptytitle]',isset($mtag['setting']['emptytitle']) ? $mtag['setting']['emptytitle'] : '');
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
	$mtagnew['setting']['tname'] = trim($mtagnew['setting']['tname']);
	if(empty($mtagnew['setting']['tname']) || !preg_match("/^[a-zA-Z_\$][a-zA-Z0-9_\[\]]*$/",$mtagnew['setting']['tname'])){
		mtag_error('������Դ���ò��Ϲ淶');
	}
	$mtagnew['setting']['maxwidth'] = max(0,intval($mtagnew['setting']['maxwidth']));
	$mtagnew['setting']['maxheight'] = max(0,intval($mtagnew['setting']['maxheight']));
	$mtagnew['setting']['limits'] = empty($mtagnew['setting']['limits']) ? 10 : max(0,intval($mtagnew['setting']['limits']));
	$mtagnew['setting']['alimits'] = max(0,intval($mtagnew['setting']['alimits']));
	$mtagnew['setting']['length'] = max(0,intval($mtagnew['setting']['length']));
	$c_upload = cls_upload::OneInstance();	
	$mtagnew['setting']['emptyurl'] = upload_s($mtagnew['setting']['emptyurl'],isset($mtag['setting']['emptyurl']) ? $mtag['setting']['emptyurl'] : '','image');
	if($k = strpos($mtagnew['setting']['emptyurl'],'#')) $mtagnew['setting']['emptyurl'] = substr($mtagnew['setting']['emptyurl'],0,$k);
	$c_upload->closure(2);
	$c_upload->saveuptotal(1);
}
?>
