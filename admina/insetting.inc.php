<?
(!defined('M_COM') || !defined('M_ADMIN')) && exit('No Permission');
aheader();
$curuser->info['isfounder'] || cls_message::show('ֻ�д�ʼ�˲ſ���ִ�е�ǰ������');
if(!submitcheck('bsubsit')){
	tabheader('�ٷ��ܹ�����ģʽ','newform',"?entry=$entry");
	trbasic('ϵͳ����ģʽ','',makeradio('mconfigsnew[cms_idkeep]',array(0 => '�����ͻ�ģʽ',2 => '���ο���ģʽ',1 => '�ٷ�����ģʽ'),@$mconfigs['cms_idkeep']),'',
	array('guide' => '�����ͻ�ģʽ�����β�����Ҫ�ܹ���������ɾ�������ݱ�Ĳ�����<br>
	�ܹ�����ģʽ��������Ҫ�ܹ���������ɾ�������ݱ�Ĳ���������ʹ����������id�Ρ�<br>
	�ٷ�����ģʽ����߼�ģʽ��������Ҫ�ܹ���������ɾ�������ݱ�Ĳ���������ܹ�����ʹ�ùٷ���������id�Ρ�
	'));
	tabfooter();
	
	tabheader('�ܹ�����ģʽ����');
	trbasic('�ܹ�����ģʽ���ܱ�������Ŀ','mconfigsnew[deep_caids]',$mconfigs['deep_caids'],'text',array('w' => 60,'guide'=>'���ŷָ����id'));
	trbasic('�ܹ�����ģʽ���ܱ�������ϵ','mconfigsnew[deep_coids]',$mconfigs['deep_coids'],'text',array('w' => 60,'guide'=>'���ŷָ����id'));
	trbasic('�ܹ�����ģʽ���ܱ�������ϵ','mconfigsnew[deep_gtids]',$mconfigs['deep_gtids'],'text',array('w' => 60,'guide'=>'���ŷָ����id'));
	tabfooter('bsubsit');
}else{
	$mconfigsnew['deep_caids'] = trim($mconfigsnew['deep_caids']);
	$mconfigsnew['deep_coids'] = trim($mconfigsnew['deep_coids']);
	$mconfigsnew['deep_gtids'] = trim($mconfigsnew['deep_gtids']);
	saveconfig('view');
	cls_message::show('��վ�������',"?entry=$entry");
}
