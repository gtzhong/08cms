<?php
 
$cuid = 3; //�����ⲿ��chid����Ҫ��������
$caid = 2;
$chid = empty($chid) ? 4 : max(0,intval($chid)); //4;

$cid = empty($cid) ? 0 : max(0,intval($cid));
$class = empty($cid) ? 'cls_culist' : 'cls_cuedit'; 
$aid = empty($aid) ? 0 : max(1,intval($aid));
$aid_str = empty($aid)?'':" AND cu.aid='$aid' ";
$aid_url = empty($aid)?'':"&aid=$aid&chid=$chid";

$_init = array(
	'cuid' => $cuid,//����ģ��id
	'ptype' => 'a',
	'pchid' => $chid,
	//'caid' => $caid,
	'url' => $aid_url, //��url���������Ҫ����mchid
	'select'=>' ',
	'from'=>'',
	'where' => $aid_str, //��������,ǰ����Ҫ[ AND ]
);


if($cid){

	$_init['cid'] = $cid;
	$oA = new $class($_init);
	$oA->top_head(array('chkData'=>1,'setCols'=>1));
	
	if(!submitcheck('bsubmit')){
		$oA->fm_header("","&caid=$caid&chid=$chid");
		$oA->fm_items();
		$oA->fm_footer('bsubmit');
		$oA->guide_bm('','0');
	}else{
		//�ύ��Ĵ���
		$oA->sv_all_common();
	}
	
}else{
	$oL = new $class($_init); 
	$oL->top_head();

	//������Ŀ ****************************
	$oL->s_additem('keyword',array('fields' => array('cu.ndxm'=>'������','a.subject' => '����¥��',)));
	$oL->s_additem('checked');
	$oL->s_additem('lpdyfl',array());
	$oL->s_additem('indays');
	$oL->s_additem('outdays');
	//����sql��filter�ִ�����
	$oL->s_deal_str(); 

	//����������Ŀ ********************
	$oL->o_additem('delete');
	$oL->o_additem('check');
	$oL->o_additem('uncheck');
	$oL->o_additem('issms');
	
	if(!submitcheck('bsubmit')){
		
		//�������� ******************
		$oL->s_header();
		$oL->s_view_array();
		//$oL->s_footer();
		$oL->s_footer_ex("?entry=extend&extend=export_excel&chid=$chid&cuid=$cuid&filename=kfhdbm$aid_url");
		
		//��ʾ�б���ͷ�� ***************
		$oL->m_header('', $aid, $aid ? " &nbsp; <a href='?entry=extend&extend=$extend&chid=$chid'>ȫ������&gt;&gt;</a>" : '');
		$oL->m_additem('selectid'); 
		$oL->m_additem('subject',array('len'=>40,'title'=>'����¥��')); 
	    $oL->m_additem('ndxm',array('title'=>'������','side'=>'L'));
		$oL->m_additem('xinbie',array('title'=>'�Ա�','side'=>'L'));	
		$oL->m_additem('sjhm',array('title'=>'��ϵ�绰','side'=>'L'));
		$oL->m_additem('checked',array('type'=>'bool','title'=>'���',));
		$oL->m_additem('ip',array('type'=>'other','title'=>'��ԴIP'));
		$oL->m_additem('cucreate',array('type'=>'date','title'=>'���ʱ��'));        
		$oL->m_additem('detail',array('type'=>'url','title'=>'�༭','mtitle'=>'����','url'=>"?entry=extend$extend_str&cuid=$cuid&caid=$caid&cid={cid}&chid=$chid",'width'=>40,));
		
		$oL->m_view_top(); //��ʾ�����У����ж���չʾ�Ļ�����Ҫ
		$oL->m_view_main(); 
		$oL->m_footer(); //��ʾ�б���β��
		
		$oL->o_header(); //��ʾ����������************
		$oL->o_view_bools(); //��ʾ��ѡ��
		$oL->o_view_rows();
		
		$oL->o_footer('bsubmit');
		$oL->guide_bm('','0');
		
	}else{
		
		$oL->sv_header(); //Ԥ����δѡ�����ʾ
		$oL->sv_o_all(); //��������������ݴ���
		$oL->sv_footer(); //��������
		
	}
			
}

?>