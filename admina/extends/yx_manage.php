<?php
 
$cuid = 44; //�����ⲿ��chid����Ҫ��������
$caid = empty($caid) ? 0 : max(0,intval($caid));
$chid = cls_cubasic::caid2chid($caid);
$cid = empty($cid) ? 0 : max(0,intval($cid));
$class = empty($cid) ? 'cls_culist' : 'cls_cuedit'; 

$_init = array(
	'cuid' => $cuid,//����ģ��id
	'ptype' => 'a',
	'pchid' => $chid,
	'caid' => $caid,
	'url' => "", //��url���������Ҫ����mchid
	'select'=>'',
	'from'=>'',
	'where' => "  ", //��������,ǰ����Ҫ[ AND ]
);


if($cid){

	$_init['cid'] = $cid;
	$oA = new $class($_init);
	$oA->top_head(array('chkData'=>1,'setCols'=>1));
	
	if(!submitcheck('bsubmit')){
		$oA->fm_header("","&caid=$caid");
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
	$oL->s_additem('keyword',array('fields' => array('a.subject' => '¥������'),'custom'=>1));
	$oL->s_additem('checked');
	$oL->s_additem('indays');
	$oL->s_additem('outdays');
	//����sql��filter�ִ�����
	$oL->s_deal_str(); 
	
	//����������Ŀ ********************
	$oL->o_additem('delete');
	$oL->o_additem('check');
	$oL->o_additem('uncheck');

	if(!submitcheck('bsubmit')){
		
		//�������� ******************
		$oL->s_header();
		$oL->s_view_array();
		$oL->s_footer();
		
		//��ʾ�б���ͷ�� ***************
		$oL->m_header( );
		$oL->m_additem('selectid'); 
		$oL->m_additem('subject',array('len'=>40,'title'=>'ӡ��¥��')); 
        $oL->m_additem('impression',array('title'=>'ӡ��'));	
    
		$oL->m_additem('renshu',array('title'=>'ӡ������'));
		$oL->m_additem('checked',array('type'=>'bool','title'=>'���',));
		$oL->m_additem('ip',array('type'=>'other','title'=>'��ԴIP'));
		$oL->m_additem('cucreate',array('type'=>'date','title'=>'���ʱ��'));        
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