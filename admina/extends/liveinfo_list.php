<?php
/**
 * ֱ����Ϣ�б�
 *
 * @author icms <icms@foxmail.com>
 * @copyright Copyright (C) 2008 - 2014 08CMS, Inc. All rights reserved.
 *
 */ 
$cuid = 101; //�����ⲿ��chid����Ҫ��������
$caid = 606;
$chid = 114;
$cid = empty($cid) ? 0 : max(0,intval($cid));
$aid = empty($aid) ? 0 : max(1,intval($aid));
$aid_sql = empty($aid)?'':" AND a.aid='$aid'  ";

$class = empty($cid) ? 'cls_culist' : 'cls_cuedit'; 
$isreply = empty($isreply) ? 0 : 1;
$_init = array(
	'cuid' => $cuid,//����ģ��id
	'ptype' => 'a',
	'pchid' => $chid,
	'caid' => $caid,
	'url' => "&aid=$aid", //��url���������Ҫ����mchid
	'select'=>'',
	'from'=>"",
	'where' => " $aid_sql ", //��������,ǰ����Ҫ[ AND ]
);
//echo $_init['from'];

if($cid){
	$_init['cid'] = $cid;
	$oA = new $class($_init);
	$oA->top_head(array('chkData'=>1,'setCols'=>1));

	if(!submitcheck('bsubmit')){
		$oA->fm_header("");		
		$oA->fm_items('');
		$oA->fm_footer('bsubmit');
		$oA->guide_bm('','0');
	}else{
		//�ύ��Ĵ���
		$oA->sv_all_common();
	}
	
}else{
	$oL = new $class($_init); 
	$oL->top_head();
    !isset($istrue) && $istrue = -1;

	//������Ŀ **************************** 'b.subject' => 'ֱ���ĵ�',
	$oL->s_additem('keyword',array('fields' => array('a.subject'=>'ֱ���ĵ�','cu.content' => 'ֱ������',)));

	$oL->s_additem('indays');
	$oL->s_additem('outdays');
    
   	//����������Ŀ ********************
	$oL->o_additem('delete');
    $oL->o_additem('check');
    $oL->o_additem('uncheck');
    
	//����sql��filter�ִ�����
	$oL->s_deal_str(); 

	if(!submitcheck('bsubmit')){
		
		//�������� ******************
		$oL->s_header();
		$oL->s_view_array();
		$oL->s_footer();
		
		//��ʾ�б���ͷ�� ***************
		$oL->m_header( );
		$oL->m_additem('selectid'); 
		$oL->m_additem('subject',array('title'=>'ֱ���ĵ�')); 
	
		$oL->m_additem('speeker',array('title'=>'������'));

		$oL->m_additem('content',array('title'=>'����'));
		$oL->m_additem('checked',array('type'=>'bool','title'=>'���'));
		$oL->m_additem('detail',array('type'=>'url','title'=>'�༭','mtitle'=>'����','url'=>"?entry=extend$extend_str&cuid=$cuid&caid=$caid&cid={cid}&aid={aid}",'width'=>40,));
		
		$oL->m_view_top(); //��ʾ�����У����ж���չʾ�Ļ�����Ҫ
		$oL->m_view_main(); 
		$oL->m_footer(); //��ʾ�б���β��
		
		$oL->o_header(); //��ʾ����������************
		$oL->o_view_bools(); //��ʾ��ѡ��
		
		$oL->o_footer('bsubmit');
		$oL->guide_bm('','0');
		
	}else{
		
		$oL->sv_header(); //Ԥ����δѡ�����ʾ
		$oL->sv_o_all(); //��������������ݴ���
		$oL->sv_footer(); //��������
		
	}
			
}

?>