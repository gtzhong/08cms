<?php
/**
 * ֱ����Ϣ���
 *
 * @author icms <icms@foxmail.com>
 * @copyright (C) 2008 - 2014 08CMS, Inc. All rights reserved.
 *
 */ 
$cuid = 101; //�����ⲿ��chid����Ҫ��������
$caid = 606;
$chid = 114;

$aid = empty($aid) ? 0 : max(0,intval($aid));
$aid_url = empty($aid)?'':"&aid=$aid";

$_init = array(
	'cuid' => $cuid,//����ģ��id
	'ptype' => 'a',
	'pchid' => 0,
	'caid' => $caid,
	'url' => "$aid_url", //��url���������Ҫ����mchid
	'select'=>'',
	'from'=>'',
	'where' => "", //��������,ǰ����Ҫ[ AND ]
);

$oA = new cls_cuedit($_init);
$oA->top_head(array('setCols'=>1));

if(!submitcheck('bsubmit')){
	$oA->fm_header("","&entry=extend&extend=liveinfo_add$aid_url");
	$oA->fm_items('');		
	$oA->fm_footer('bsubmit');
	$oA->guide_bm('','0');
}else{
	$oA->sv_set_fmdata();//����$this->fmdata�е�ֵ
	$oA->sv_items();//�������ݵ����飬��ʱδִ�����ݿ����
	$oA->sv_insert(array('aid'=>$aid,'checked'=>1));//ִ��insert, ���Ӳ���
	$oA->sv_upload();//�ϴ�����
    $oA->sv_finish(array('message'=>'��ӳɹ�'));
}
	
?>
