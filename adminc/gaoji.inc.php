<?php
!defined('M_COM') && exit('No Permission');
$ngtid = 14;$nchid = 2;$nugid = 8;
$mchid = $curuser->info['mchid'];
if($mchid != $nchid) cls_message::show('����Ҫ��ע��Ϊ�����˻�Ա�ſ���������');
$exconfigs = cls_cache::cacRead('exconfigs',_08_EXTEND_SYSCACHE_PATH);
if(!($rules = @$exconfigs['gaoji'])) cls_message::show('ϵͳû�ж��徭������������');
if(!submitcheck('bsubmit')){
	tabheader('����������','gtexchagne',"?action=$action");
	trbasic('��Ŀǰ������','',($curuser->info["grouptype$ngtid"] == $nugid ? '�߼�������' : '��ͨ������').' &nbsp;����ʱ�䣺'.($curuser->info["grouptype{$ngtid}date"] ? date('Y-m-d H:i',$curuser->info["grouptype{$ngtid}date"]) : '����'),'');
	trbasic('��Դ�ö����','',$curuser->info['freezds'].' ��','');
	trbasic('ԤԼˢ�����','',$curuser->info['freeyys'].' ��','');
	trbasic('�ֽ��ʻ����','',$curuser->info['currency0']." Ԫ &nbsp; &nbsp;<a href=\"?action=payonline\" target=\"_blank\">>>����֧��</a>",'');
	if(($curuser->info["grouptype$ngtid"] == $nugid) && !$curuser->info["grouptype{$ngtid}date"]){
		trbasic('����˵��','','�������õĸ߼������ˣ�����Ҫ������','');
		tabfooter();
	}else{
		$str = '';foreach($rules as $k => $v) $v['available'] && $str .= "<input class=\"radio\" type=\"radio\" name=\"exchangekey\" value=\"$k\" checked> &nbsp;$v[title] &nbsp;�۸�<b>$v[price]</b> Ԫ &nbsp;��Ч�ڣ�<b>$v[month]</b> ���� &nbsp;���� <b>$v[zds]</b> �췿Դ�ö� &nbsp;���� <b>$v[yys]</b> ����ԴԤԼˢ������<br>";
		trbasic('����������','',"<br>$str<br>",'');
		tabfooter('bsubmit');
		$mgdes = @$exconfigs['upmemberhelp'][$ngtid];
		$mgdes['des'] = implode('<p>',explode("\r\n",$mgdes['des']));
		empty($mgdes) ? '' : m_guide($mgdes['des'],'fix');
	}
}else{
	$exchangekey = max(0,intval($exchangekey));
	if(!($rule = @$rules[$exchangekey])) cls_message::show('��ָ������Ϊ���ָ߼������ˡ�',M_REFERER);
	if($curuser->info['currency0'] < $rule['price']) cls_message::show('�����ֽ��ʻ����㣬���ֵ��',M_REFERER);
	$curuser->updatefield('freezds',$curuser->info['freezds'] + $rule['zds']);
	$curuser->updatefield('freeyys',$curuser->info['freeyys'] + $rule['yys']);
	$curuser->updatefield("grouptype{$ngtid}date",($curuser->info["grouptype$ngtid"] == $nugid ? $curuser->info["grouptype{$ngtid}date"] : $timestamp) + $rule['month'] * 30 * 86400);
	$curuser->updatefield("grouptype$ngtid",$nugid);
	$curuser->updatecrids(array(0 => -$rule['price']),1,'�����˻�Ա������');
	cls_message::show('�����������ɹ���',M_REFERER);

}
?>
