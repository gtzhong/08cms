<?php
$chid = 106;
$cuid = 38; 
$caid = empty($caid) ? 0 : max(1,intval($caid));
$cid = empty($cid) ? 0 : max(0,intval($cid));
$aid = empty($aid)?0:max(1,intval($aid));
$aid_sql = empty($aid)?'':" AND a.aid='$aid'  ";

$class = empty($cid) ? 'cls_culist' : 'cls_cuedit'; 
$_init = array(
	'cuid' => $cuid,//����ģ��id
	'ptype' => 'a',
	'pchid' => $chid,
	'caid' => $caid,
	'url' => "&aid=$aid", //��url���������Ҫ����mchid
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
		$oA->fm_footer('');		
	}
	
}else{
	$oL = new $class($_init); 	
	
	$oL->top_head();    
	//������Ŀ ****************************
	$oL->s_additem('keyword',array('fields' => array('a.subject'=>'��������','cu.content' => '����','cu.mname'=>'�ٱ���'),'custom'=>1));
	$oL->s_additem('indays');
	$oL->s_additem('outdays');   
    
	//����sql��filter�ִ�����
	$oL->s_deal_str(); //echo $oL->sqlall;
	
	//����������Ŀ ********************
	$oL->o_additem('delete',array('title'=>"����ɾ��"));//����ɾ��
    $oL->o_additem('deleteVicious');//ɾ������
	$oL->o_additem('check');
	$oL->o_additem('uncheck');

	if(!submitcheck('bsubmit')){
		
		//�������� ******************
		$oL->s_header();
		$oL->s_view_array();
        $oL->s_footer();	
		
		//��ʾ�б���ͷ�� ***************
		$oL->m_header('', $aid, $aid ? " &nbsp; <a href='?entry=extend&extend=$extend='>ȫ���ٱ�&gt;&gt;</a>" : '');
		$oL->m_additem('selectid'); 
		if(empty($aid)) $oL->m_additem('subject',array('len' => 40,'title'=>'��������','type'=>'url')); 
	
		$oL->m_additem('content',array('title'=>'����','len'=>30));
        $oL->m_additem('mname',array('title'=>'�ٱ���'));     
		$oL->m_additem('checked',array('type'=>'bool','title'=>'���'));
        $oL->m_additem('ip',array('title'=>'��ԴIP'));
		$oL->m_additem('cucreate',array('type'=>'date','title'=>'��������'));        
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