<?php
!defined('M_COM') && exit('No Permission');
$currencys = cls_cache::Read('currencys');
$grouptypes = cls_cache::Read('grouptypes');
$curuser->detail_data();
tabheader('�ҵĻ������');
trbasic('���״̬','',$curuser->info['checked'] ? '����': '�û��ȴ����','');
trbasic('ע��ʱ��','',$curuser->info['regdate'] ? date("$dateformat   $timeformat",$curuser->info['regdate']) : '','');
trbasic('ע��IP','',$curuser->info['regip'] ? $curuser->info['regip'] : '-','');
trbasic('�ϴε�½ʱ��','',$curuser->info['lastvisit'] ? date("$dateformat   $timeformat",$curuser->info['lastvisit']) : '','');
trbasic('�ϴμ���ʱ��','',$curuser->info['lastactive'] ? date("$dateformat   $timeformat",$curuser->info['lastactive']) : '','');
trbasic('�ϴε�½IP','',$curuser->info['lastip'] ? $curuser->info['lastip'] : '-','');
trbasic('�ռ������','',$curuser->info['msclicks'],'');
tabfooter();
tabheader('�Ҳ��������');
trbasic('�ĵ�����','',$curuser->info['archives'],'');
trbasic('�����ĵ�����','',$curuser->info['checks'],'');
trbasic('���ϴ�����','',sizecount(1024 * $curuser->info['uptotal']),'');
$capacity = $curuser->upload_capacity();
trbasic('�ϴ��ռ�����','',$capacity == -1 ? '������' : sizecount(1024 * $capacity),'');
trbasic('�����ظ���','',sizecount(1024 * $curuser->info['downtotal']),'');
tabfooter();
tabheader('�ҵĻ���');
trbasic('�ֽ��ʻ�','',$curuser->info['currency0'].'Ԫ','');
foreach($currencys as $crid => $currency){
	trbasic($currency['cname'],'',$curuser->info['currency'.$crid].$currency['unit'],'');
}
tabfooter();
tabheader('�ҵĻ�Ա��','','',4);
foreach($grouptypes as $k => $v){
	if($curuser->info["grouptype$k"]){
		$usergroups = cls_cache::Read('usergroups',$k);
		$date = !@$curuser->info["grouptype{$k}date"] ? '-' : date('Y-m-d',@$curuser->info["grouptype{$k}date"]);
		echo "<tr>\n".
			"<td width=\"15%\" class=\"item1\"><b>$v[cname]</b></td>\n".
			"<td width=\"35%\" class=\"item2\">".($usergroups[$curuser->info["grouptype$k"]]['cname'])."</td>\n".
			"<td width=\"15%\" class=\"item1\"><b>��������</b></td>\n".
			"<td width=\"35%\" class=\"item2\">$date</td>\n".
			"</tr>";
	}
}
tabfooter();
?>