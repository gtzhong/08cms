<?php

// *** ������ ���ýű� 
//$cuids = array('1'=>2,'46'=>21,'31'=>41); //��Ѷ����,ר������,��Ƶ����=>array(�ĵ�ģ��s)
$cuid = empty($cuid) ? 1 : (isset($cuids[$cuid]) ? $cuid : 1); //�����ⲿ��chid����Ҫ��������+
$chid = empty($chid) ? 1 : max(1,intval($chid));
if(!atbl($chid)) cls_message::show('��������');
$caid = empty($caid) ? 0 : max(0,intval($caid));
$cid = empty($cid) ? 0 : max(0,intval($cid));
$aid = empty($aid)?0:max(1,intval($aid));
$pid = empty($pid) ? 0 : max(0,intval($pid));
if($aid && empty($pid)) $pid = $aid;
$reid = empty($reid) ? 0 : max(0,intval($reid));
$class = empty($cid) ? 'cls_culist' : 'cls_cuedit'; 
$alllink = "";
if($pid && empty($reid)) $alllink = " &nbsp; ### <a href='?entry=extend&extend=$extend=&chid=$chid'>ȫ������&gt;&gt;</a>";

$_init = array(
	'cuid' => $cuid,//����ģ��id
	'ptype' => 'a',
	'pchid' => $chid,
	'caid' => $caid,
	'url' => "&pid=$pid&reid=$reid&chid=$chid", //��&��ʼ, $action,$entry,$extend_str,$cuid��������
	'select'=> "", //
	'where' => ' AND cu.tocid=0 ', //��������,ǰ����Ҫ[ AND ]
	'from' => "", //
);


if($cid){

	$_init['cid'] = $cid;
	$oA = new $class($_init);
	$oA->top_head(array('chkData'=>1,'setCols'=>1));
	
	if(!submitcheck('bsubmit')){
		$oA->fm_header("","");
		$oA->add_pinfo(array('pid'=>$oA->predata['aid']));
		$oA->fm_items();
		$oA->fm_footer('bsubmit');
		$oA->guide_bm('','0');
	}else{
		//�ύ��Ĵ���
		$oA->sv_all_common();
	}
	
}else{
	
	// *** ���н����б�һ�����������µ��б��ظ��б����������༭ ���ýű�
	// ��sql������,����"new $class()"֮ǰ
	if($pid){
		$_init['where']	= " AND cu.tocid=0 AND cu.aid='$pid'";
	}elseif($reid){
		$_init['where']	= " AND cu.tocid='$reid'";
	}else{
			
	}
	$oL = new $class($_init); 
	$oL->top_head();
	// "new $class()"֮�����ж�title��
	if($pid){
		$title = "�����б� --- &gt;&gt;".$oL->getPLink($pid, array());
	}elseif($reid){
		$title = "�ظ��б�";
	}else{
		$title = "";
	}

	//������Ŀ ****************************
	$oL->s_additem('keyword',array('fields' => array('cu.content'=>'��������','a.subject' => '�����ĵ�',)));
	$oL->s_additem('checked');
	$oL->s_additem('indays');
	$oL->s_additem('outdays');
	//����sql��filter�ִ�����
	$oL->s_deal_str(); 
	
	//����������Ŀ ********************
	$oL->o_additem('delete',array('exkey'=>'tocid'));
	$oL->o_additem('delbad',array('exkey'=>'tocid')); //ɾ��(�ۻ���)
	$oL->o_additem('check');
	$oL->o_additem('uncheck');

	if(!submitcheck('bsubmit')){
		
		//�������� ******************
		$oL->s_header();
		$oL->s_view_array();
		$oL->s_footer();
		
		//��ʾ�б���ͷ�� ***************
		$oL->m_header($title,'',"$alllink");
		$oL->m_additem('selectid'); 
		empty($pid) && empty($reid) && $oL->m_additem('subject',array('len'=>40,)); // *** pid��Ϊ������ʾ��������
		$oL->m_additem('content',array('len'=>30,'title'=>'��������','side'=>'L'));
		$oL->m_additem('mname',array('title'=>'��Ա','side'=>'L'));
		$oL->m_additem('checked',array('type'=>'bool','title'=>'���',));
		empty($reid) && $oL->m_additem('recounts',array('url'=>"?entry=extend$extend_str&cuid=$cuid&reid={cid}&chid=$chid",'winsize'=>'640,480')); // *** reid������ʾ�ظ���
		$oL->m_additem('cucreate',array('type'=>'date',));
		$oL->m_additem('detail',array('type'=>'url','title'=>'�༭','mtitle'=>'����','url'=>"?entry=extend$extend_str&cuid=$cuid&cid={cid}",'width'=>40,));
		
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