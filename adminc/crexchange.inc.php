<?php
!defined('M_COM') && exit('No Permission');
foreach(array('crprojects','currencys') as $k) $$k = cls_cache::Read($k);
backnav('currency','exchange');
if($curuser->getTrusteeshipInfo()) cls_message::show('���Ǵ����û�����ǰ������ԭ�û�������Ȩ�ޣ�');
if($enable_uc){ 
	$outextcredits = @unserialize($outextcredits);
	$outextcredits || $outextcredits = array();
}
if(!submitcheck('bcrexchange')){
	$cridsarr = cridsarr(1);
	foreach($crprojects as $crpid => $crproject){
		tabheader($cridsarr[$crproject['scrid']].'&nbsp;&nbsp;�һ�Ϊ&nbsp;&nbsp;'.$cridsarr[$crproject['ecrid']],'crexchagne'.$crpid,"?action=crexchange");
		trbasic('��ӵ�е� '.$cridsarr[$crproject['scrid']].' ����Ϊ','',$curuser->info['currency'.$crproject['scrid']],'');
		trbasic('��ӵ�е� '.$cridsarr[$crproject['ecrid']].' ����Ϊ','',$curuser->info['currency'.$crproject['ecrid']],'');
		trbasic('�һ�����','',$crproject['scurrency'].'&nbsp; '.$cridsarr[$crproject['scrid']].'&nbsp; :&nbsp; '.$crproject['ecurrency'].'&nbsp; '.$cridsarr[$crproject['ecrid']],'');
		trbasic('�һ�����'.'('.$cridsarr[$crproject['scrid']].')','exchangesource');
		echo "<input type=\"hidden\" name=\"crpid\" value=\"$crpid\">";
		tabfooter('bcrexchange','�һ�');
		$_list = 1;
	}
	if($enable_uc){
		foreach($outextcredits as $k => $v){
			tabheader($cridsarr[$v['creditsrc']].'&nbsp;&nbsp;�һ�Ϊ&nbsp;&nbsp;'.$v['title'],'ocrexchagne'.$k,"?action=crexchange");
			trbasic('��ӵ�е� '.$cridsarr[$v['creditsrc']].' ����Ϊ','',$curuser->info['currency'.$v['creditsrc']],'');
			trbasic('�һ�����','',$v['ratiosrc' ].'&nbsp; :&nbsp; '.$v['ratiodesc' ],'');
			trbasic('�һ�����'.'('.$cridsarr[$v['creditsrc']].')','exchangesource');
			echo "<input type=\"hidden\" name=\"ocrpid\" value=\"$k\">";
			echo "<input type=\"hidden\" name=\"isout\" value=\"1\">";
			tabfooter('bcrexchange','�һ�');
			$_list = 1;
		}
	}
	empty($_list) && cls_message::show('û�п��õĻ��ֶһ���Ŀ');
	m_guide("cur_notes",'fix');
}else{
	if(empty($isout)){
		(empty($crpid) || empty($crprojects[$crpid])) && cls_message::show('��ָ����ǰ�һ�����');
		$exchangesource = max(0,intval($exchangesource));
		!$exchangesource && cls_message::show('������һ�����');
		$crproject = $crprojects[$crpid];
		($exchangesource < $crproject['scurrency']) && cls_message::show('�һ��������ڶһ�����');
		if($exchangesource > $curuser->info['currency'.$crproject['scrid']]) cls_message::show('�һ���������ӵ������');
		$num = floor($exchangesource / $crproject['scurrency']);
		$curuser->updatecrids(array($crproject['scrid'] => -$crproject['scurrency'] * $num),0,'���ֶһ�����');
		$curuser->updatecrids(array($crproject['ecrid'] => $crproject['ecurrency'] * $num),0,'���ֶһ�����');
		$curuser->updatedb();
		cls_message::show('���ֶһ����',"?action=crexchange");
	}else{
		empty($outextcredits[$ocrpid]) && cls_message::show('��ָ��UCenter���ֶһ���Ŀ');
		$exchangesource = max(0,intval($exchangesource));
		!$exchangesource && cls_message::show('������һ�����');
		$outcredit = $outextcredits[$ocrpid];
		($exchangesource < $outcredit['ratiosrc']) && cls_message::show('�һ��������ڶһ�����');
		if($exchangesource > $curuser->info['currency'.$outcredit['creditsrc']]) cls_message::show('�һ���������ӵ������');
		$num = floor($exchangesource / $outcredit['ratiosrc']);
		
		cls_ucenter::init();
		$ucresult = uc_get_user($curuser->info['mname']);
		if(!is_array($ucresult)) cls_message::show('UCenter��û�е�ǰ��Ա���ϣ�');
		$uid = $ucresult[0];
		$ucresult = uc_credit_exchange_request($uid,$outcredit['creditsrc'],$outcredit['creditdesc'],$outcredit['appiddesc'],$outcredit['ratiodesc'] * $num);
		if(!$ucresult) cls_message::show('���ֶһ�ʧ��',"?action=crexchange");
		
		$curuser->updatecrids(array($outcredit['creditsrc'] => -$outcredit['ratiosrc'] * $num),0,'���Ҷһ�����');
		$curuser->updatedb();
		cls_message::show('���ֶһ����',"?action=crexchange");
	}
}
?>
