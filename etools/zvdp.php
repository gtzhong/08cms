<?php
	include_once dirname(dirname(__FILE__)).'/include/general.inc.php';
	include_once M_ROOT."./include/adminm.fun.php";
	$aid = empty($aid) ? 0 : max(0,intval($aid));
	$inajax = empty($inajax) ? 0 : 1;
	$forward = empty($forward) ? M_REFERER : $forward;
	$forwardstr = '&forward='.urlencode($forward);
	$cuid = 41;
	$fields = cls_cache::Read('cufields',$cuid);
	$ziduan = empty($ziduan) ? '0' : $ziduan;
	if(empty($ziduan) && !isset($fields[$ziduan])){ 
		cls_message::show('��������',$forward);
	}
	foreach($fields as $k => $v){//�ж���aidΪ$aid����Ѷ�Ƿ���ڵ���cookies��ֻҪ�����κ�һ�������cookies���������ٴε����ˣ�����cookiesʧЧ
		if(!empty($m_cookie["08cms_cuid{$cuid}_dp_{$k}_$aid"])){
			$cookie_exit = 1;
		}
	}
	if(empty($cookie_exit)){ //���cookieҪ��ajax/ajax_yuedu_xinqing.phpһ��
		msetcookie("08cms_cuid{$cuid}_dp_{$ziduan}_$aid",1,24 * 86400);
	}else {		
		if(isset($js) && $js) {
			exit('var face = 0;');
		} else {
			cls_message::show('���Ѿ��������ˡ�',$forward);
		}
	}
	if(!$aid || !$db->result_one("SELECT aid FROM {$tblprefix}commu_zxdp WHERE aid='$aid'")){		 
		$db->query("INSERT INTO {$tblprefix}commu_zxdp SET aid = '$aid',".$ziduan."='1'");
	}else{
		$db->query("UPDATE {$tblprefix}commu_zxdp SET  ".$ziduan."= ".$ziduan." + 1 WHERE aid='$aid'");
	}
	if(isset($js) && $js) {
		exit('var face = 1;');
	} else {
		cls_message::show($inajax ? 'succeed' : '�����ɹ���',$forward);
	}

?>