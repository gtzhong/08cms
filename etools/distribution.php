<?php
include_once _08_INCLUDE_PATH."admin.fun.php";

//��չ����
$exfenxiao = get_fxcfgs();

$cuid = 49;
$_init = array(
	'cuid' => $cuid,//����ģ��id
	'ptype' => 'e',
);
$oA = new cls_cuedit($_init);

$oA->add_init('','',array('setCols'=>1));

// ��¼�ľ�����,������,�Ƽ�����
$exinfo = $oA->fm_fenxiao_check($exfenxiao);

if(submitcheck('bsubmit')){

	$oA->sv_regcode("commu$cuid");
	$oA->sv_repeat(array(), 'both'); //check
	$oA->sv_set_fmdata();//����$this->fmdata�е�ֵ
	$svinfo = $oA->sv_fenxiao_check($exfenxiao); //�绰����,������Դ��
	$oA->sv_items();//�������ݵ����飬��ʱδִ�����ݿ����
	$oA->sv_insert(array('aids'=>",$svinfo[said],",'ayjs'=>",$svinfo[sayj],"));//ִ��insert, ���Ӳ��� ,'ip'=>$onlineip,
	$oA->sv_upload();//�ϴ�����
	//���Ӳ���, ������, �Զ������..... 
	//$oA->sv_repeat(array('aid'=>$aid,'tocid'=>$tocid), 'save');
	
	//���Ƽ� ����(�ɱ༭)
	$oA->db->query("UPDATE {$tblprefix}".atbl(113)." SET tjs = tjs + 1 WHERE aid IN($svinfo[said])");
	
	$oA->sv_finish(array('message'=>'�Ƽ��ɹ���'));//����ʱ��Ҫ�����񣬰���������¼���ɹ���ʾ��		
}

?>

