<?php
 
$cuid = 33; //�����ⲿ��chid����Ҫ��������
$caid = empty($caid) ? 0 : max(1,intval($caid));
$chid = 103;
$cid = empty($cid) ? 0 : max(0,intval($cid));
$state = isset($state) ? $state==-1 ? -1 : max(0,intval($state)) : -1;
$aid = empty($aid)?0:max(1,intval($aid));
$aid_url = empty($aid)?'':"&aid=$aid";
$aid_sql = empty($aid)?'':" AND a.aid='$aid'  ";

$class = empty($cid) ? 'cls_culist' : 'cls_cuedit'; 
$_init = array(
	'cuid' => $cuid,//����ģ��id
	'ptype' => 'a',
	'pchid' => $chid,
	'caid' => $caid,
	'url' => "$aid_url", //��url���������Ҫ����mchid
	'select'=>"",
	'from'=>"",
	'where' => " $aid_sql ", //��������,ǰ����Ҫ[ AND ]
);


if($cid){
	$_init['cid'] = $cid;
	$oA = new $class($_init);
	$oA->top_head(array('chkData'=>1,'setCols'=>1));

	if(!submitcheck('bsubmit')){
		$oA->fm_header("");		
		$oA->fm_items('',array(),array('noaddinfo'=>1));		
        $oA->fm_state();			
		$oA->fm_footer('bsubmit');
		$oA->guide_bm('1. ��������ֻ���޸ġ�����״̬����','fix');
	}else{
       $oA->sv_state();
	}
	
}else{
	$oL = new $class($_init); 
	
	
	$oL->top_head();
    
	//������Ŀ ****************************
	$oL->s_additem('keyword',array('fields' => array('a.subject'=>'��Ʒ','cu.xingming' => '������',),'custom'=>1));
 	$oL->s_additem('state');
	$oL->s_additem('indays');
	$oL->s_additem('outdays');   
    
	//����sql��filter�ִ�����
	$oL->s_deal_str(); 
	
	//����������Ŀ ********************
	$oL->o_additem('delete',array('exkey'=>'tocid'));
	$oL->o_additem('check');
	$oL->o_additem('uncheck');	

	if(!submitcheck('bsubmit')){
		
		//�������� ******************
		$oL->s_header();
		$oL->s_view_array();
		$oL->s_footer();
		
		//��ʾ�б���ͷ�� ***************
		$oL->m_header();
		$oL->m_additem('selectid'); 
		$oL->m_additem('subject',array('len' => 40,'title'=>'��Ʒ','type'=>'url','url'=>"{$cms_abs}mspace/archive.php?mid={mid}&aid={aid}")); 
	
		$oL->m_additem('xingming',array('title'=>'������'));
        $oL->m_additem('tel',array('title'=>'�ֻ�'));
        $oL->m_additem('state',array('title'=>'����״̬','width'=>80));
		$oL->m_additem('checked',array('type'=>'bool','title'=>'���'));
		$oL->m_additem('cucreate',array('type'=>'date','title'=>'����ʱ��'));        
		$oL->m_additem('detail',array('type'=>'url','title'=>'�༭','mtitle'=>'����','url'=>"?entry=extend$extend_str&cuid=$cuid&caid=$caid&cid={cid}",'width'=>40,));
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