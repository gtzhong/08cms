<?php
$cuid = 4; 
$caid = empty($caid)?3:max(1,intval($caid));
$chid = $caid==3 ? 3 : 2;
$cid = empty($cid) ? 0 : max(0,intval($cid));
$aid = empty($aid)?0:max(1,intval($aid));

$class = empty($cid) ? 'cls_culist' : 'cls_cuedit'; 
$isreply = empty($isreply) ? 0 : 1;
$aid_sql = empty($aid)?'':" AND a.aid='$aid'  ";

$_init = array(
	'cuid' => $cuid,//����ģ��id
	'ptype' => 'a',
	'pchid' => $chid,
	'caid' => $caid,
	'url' => "&aid=$aid", //��url���������Ҫ����mchid
	'select'=>'',
	'from'=>'',
	'where' => " $aid_sql ", //��������,ǰ����Ҫ[ AND ]
);


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

	//������Ŀ ****************************
	$oL->s_additem('keyword',array('fields' => array('a.subject'=>'��Դ����','cu.mname' => '�ٱ���',),'custom'=>1));
    $oL->s_additem('leixing');
	$oL->s_additem('indays');
	$oL->s_additem('outdays');
	//����sql��filter�ִ�����
	$oL->s_deal_str(); 
	
	//����������Ŀ ********************
	$oL->o_additem('delete');

	if(!submitcheck('bsubmit')){
		
		//�������� ******************
		$oL->s_header();
		$oL->s_view_array();
		$oL->s_footer();
		
		//��ʾ�б���ͷ�� ***************
		$oL->m_header('', $aid, $aid ? " &nbsp; <a href='?entry=extend&extend=$extend='>ȫ���ٱ�&gt;&gt;</a>" : '');
		$oL->m_additem('selectid'); 
		if(empty($aid)) $oL->m_additem('subject',array('len'=>40,'title'=>'���ٱ���Դ')); 
 	
		$oL->m_additem('mname',array('title'=>'�ٱ���'));
		$oL->m_additem('leixing',array('title'=>'�ٱ�����','width'=>90));
		$oL->m_additem('ip',array('type'=>'other','title'=>'��ԴIP'));
		$oL->m_additem('cucreate',array('type'=>'date','title'=>'�ٱ�ʱ��'));        
		$oL->m_additem('detail',array('type'=>'url','title'=>'�༭','mtitle'=>'����','url'=>"?entry=extend$extend_str&cuid=$cuid&caid=$caid&cid={cid}",'width'=>40,));
	    $oL->m_additem('detail2',array('type'=>'url','title'=>'����','mtitle'=>'��Դ','url'=>"?entry=extend&extend=".($caid == 4 ? 'chuzuarchive' : 'usedhousearchive')."&aid={aid}",'width'=>40,));
		
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