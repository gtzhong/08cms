<?php
if(!$modeSave){
	#$mtag = _tag_merge(@$mtag,@$mtagnew);
	trbasic('��Ƭ������ȡurl','mtagnew[setting][url]',empty($mtag['setting']['url']) ? '' : $mtag['setting']['url'],'text',array('guide' => 'ͨ����Ƭ����ϵͳ����Ƭ�����в鿴����url','w' => 80));
	trbasic('���ݻ�������(��)','mtagnew[setting][ttl]',empty($mtag['setting']['ttl']) ? 0 : $mtag['setting']['ttl'],'text',array('guide' => '��λ���롣����չ���濪����ģ�����ģʽ�رյ��������Ч��'));
	trbasic('������ȡ��ʱʱ��','mtagnew[setting][timeout]',empty($mtag['setting']['timeout']) ? 0 : $mtag['setting']['timeout'],'text',array('guide' => '��λ���룬��������ʱ���������ȡ��'));
	tabfooter();
}else{
	if(empty($mtagnew['setting']['url'])) mtag_error('��������Ƭ������ȡurl');
	$mtagnew['setting']['ttl'] = max(0,intval($mtagnew['setting']['ttl']));
}
?>
