<?php
!defined('M_COM') && exit('No Permission');
if($curuser->getTrusteeshipInfo()) cls_message::show('���Ǵ����û�����ǰ������ԭ�û�������Ȩ�ޣ�');
$poids = _08_factory::getInstance(_08_Loader::MODEL_PREFIX . 'PayGate_Pays')->getPays();
!isset($poids['alipay_direct']) && cls_message::show('֧������ʱ���˹����ѹر�');
$db = _08_factory::getDBO();
if(!submitcheck('bsubmit')){
	$row = $db->select()->from('#__pays_account')->where(array('id' => $curuser->info['mid']))->limit(1)->exec()->fetch();
	tabheader("֧������ʱ��������",'myform',"?action=$action",2,1,1);
	trbasic('֧�����ʻ�','fmdata[alipay_seller_account]',$row['alipay_seller_account'] ? $row['alipay_seller_account'] : '','text',array('validate'=>'rule="text" must=1'));
	trbasic('���������(PID) 	','fmdata[alipay_partnerid]',$row['alipay_partnerid'] ? $row['alipay_partnerid'] : '','text',array('validate'=>'rule="text" must=1'));
	trbasic('��ȫУ����(Key)','fmdata[alipay_partnerkey]',$row['alipay_partnerkey'] ? authcode($row['alipay_partnerkey'], 'DECODE', $curuser->info['salt']) : '','password',array('validate'=>'rule="text" must=1','guide'=>'������������ĸ��������'));
	tabfooter('bsubmit');
}else{
	if(!empty($fmdata['alipay_seller_account']) && !empty($fmdata['alipay_partnerid']) && !empty($fmdata['alipay_partnerkey'])){
		$alipay_partnerkey = authcode($fmdata['alipay_partnerkey'], 'ENCODE', $curuser->info['salt']); #��ȫУ��KEY
		$db->insert( '#__pays_account', 
        	array(
            	'alipay_seller_account' => $fmdata['alipay_seller_account'], 
            	'alipay_partnerid' => $fmdata['alipay_partnerid'], 
            		'id' => $curuser->info['mid'],
				'alipay_partnerkey'=> $alipay_partnerkey     
        	)
    	)->exec();
	}
	cls_message::show('���óɹ�!',"?action=$action");
}

		

?>