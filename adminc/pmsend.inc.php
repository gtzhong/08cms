<?php
!defined('M_COM') && exit('No Permission');
backnav('pm','send');
//$enable_uc && include_once M_ROOT.'adminm/pmuc.inc.php';
if(!submitcheck('bpmsend')){
	tabheader("���Ͷ���",'pmsend','?action=pmsend',2,0,1);
	trbasic('����','pmnew[title]','','text', array('validate' => makesubmitstr('pmnew[title]',1,0,0,80),'w'=>50));
	trbasic('������','pmnew[tonames]',empty($tonames) ? '' : $tonames,'text', array('guide' => '�ö��ŷָ������Ա����','validate' => makesubmitstr('pmnew[tonames]',1,0,0,100),'w'=>50));
	trbasic('����','pmnew[content]','','textarea', array('w' => 500,'h' => 300,'validate' => makesubmitstr('pmnew[content]',1,0,0,1000)));
	tr_regcode('pm');
	tabfooter('bpmsend');
	m_guide('sms_insite','fix');
}else{
	if(!regcode_pass('pm',empty($regcode) ? '' : trim($regcode))) cls_message::show('��֤���������',M_REFERER);
	$pmnew['title'] = trim($pmnew['title']);
	$pmnew['tonames'] = trim($pmnew['tonames']);
	$pmnew['content'] = trim($pmnew['content']);
	if(empty($pmnew['title']) || empty($pmnew['content']) || empty($pmnew['tonames'])){
		cls_message::show('�������ϲ���ȫ',M_REFERER);
	}
	$tonames = array_filter(explode(',',$pmnew['tonames']));
	if($tonames){
		$query = $db->query("SELECT mid FROM {$tblprefix}members WHERE mname ".multi_str($tonames)." ORDER BY mid");
		$sqlstr = '';
        $uids = array();
		while($user = $db->fetch_array($query)){
			//�����������Ʒ���
			$sqlstr .= ($sqlstr ? ',' : '')."('$pmnew[title]','$pmnew[content]','$user[mid]','$memberid','".$curuser->info['mname']."','$timestamp')";
            $uids[] = $user['mid'];
		}
		$sqlstr && $db->query("INSERT INTO {$tblprefix}pms (title,content,toid,fromid,fromuser,pmdate) VALUES $sqlstr");
        # ��WINDID�û����Ͷ���
        cls_WindID_Send::getInstance()->send( $uids, $pmnew['content'], $memberid );
	}
	cls_message::show('���ŷ��ͳɹ�','?action=pmsend');
}
?>