<?php

/**
 * @author lyq2014
 * @copyright 2014
 */ 
$cuid = 50; //�����ⲿ��chid����Ҫ������

$_init = array(
	'cuid' => $cuid,//����ģ��id
	'ptype' => 'a',
	'pchid' => '',
	'caid' => '',
	'url' => '', //��url���������Ҫ����mchid
	'select'=>'',
	'from'=>'',
	'where' => "", //��������,ǰ����Ҫ[ AND ]
);

$oA = new cls_cuedit($_init);

$oA->top_head(array('setCols'=>1));
//$oA->items_did[] = 'tjdqsj';
$oA->items_did[] = 'valid';


if(!submitcheck('bsubmit')){
    backnav('distribution','add');
	$oA->fm_header("","");
    $oA->fm_items('xingming');
    $oA->fm_items('lxdh');//�绰Ψһ
    $oA->fm_items('xingbie');
    $oA->fm_items('valid'); 
	$oA->fm_items('');		
	$oA->fm_footer('bsubmit');
	$oA->guide_bm('','0');
}else{
	$oA->sv_set_fmdata();//����$this->fmdata�е�ֵ
	$oA->sv_items();//�������ݵ����飬��ʱδִ�����ݿ����
	$oA->sv_insert();//ִ��insert
	$oA->sv_upload();//�ϴ�����
    $oA->sv_finish(array('message'=>'��ӳɹ�'));
}
	
?>