<?php
 
$cuid = 37; //�����ⲿ��chid����Ҫ��������
$caid = empty($caid) ? 0 : max(1,intval($caid));
$chid = 106;
$cid = empty($cid) ? 0 : max(0,intval($cid));
$aid = empty($aid)?0:max(1,intval($aid));
$aid_sql = empty($aid)?'':" AND a.aid='$aid'  ";
$answertype = empty($answertype)?1:max(1,intval($answertype));
$filterstr = empty($filterstr)?'':trim($filterstr);
$page = empty($page)?1:max(1,intval($page));
$answerTypeSql = '';
switch($answertype){
    case 1:
        $answerTypeSql = " AND cu.toaid=0 AND cu.tocid=0 ";
    break;
    case 2:
        $answerTypeSql = "  AND cu.toaid=0 AND cu.tocid>0 ";
    break;
    case 3:
        $answerTypeSql = " AND cu.toaid>0 AND cu.tocid=0";
    break;
}

$class = empty($cid) ? 'cls_culist' : 'cls_cuedit'; 
$_init = array(
	'cuid' => $cuid,//����ģ��id
	'ptype' => 'u',
	'pchid' => $chid,
	'caid' => $caid,
	'url' => "&aid=$aid&answertype=$answertype&page=$page", //��url���������Ҫ����mchid
	'select'=>" SELECT cu.*,cu.createdate AS cucreate,cu.mid as cu_mid,cu.mname as cu_mname ,a.aid,a.chid,a.caid,a.createdate,a.initdate,a.customurl,a.nowurl,a.subject,a.mid as twmid ",
	'from'=>" FROM {$tblprefix}commu_answers cu INNER JOIN {$tblprefix}archives22 a ON a.aid=cu.aid ",
	'where' => " $aid_sql $answerTypeSql ", //��������,ǰ����Ҫ[ AND ]
);


if($cid){
	$_init['cid'] = $cid;
	$oA = new $class($_init);
	$oA->top_head(array('chkData'=>1,'setCols'=>1));

	if(!submitcheck('bsubmit')){
		$oA->fm_header("");		
		$oA->fm_items('',array(),array('noaddinfo'=>1));			
		$oA->fm_footer('');		
	}else{
		//�ύ��Ĵ���
		$oA->sv_all_common();
	}
	
}else{
	$oL = new $class($_init); 
	
	$oL->top_head();
    
	//������Ŀ ****************************
	$oL->s_additem('keyword',array('fields' => array('a.subject'=>'��������','cu.content' => '����',),'custom'=>1));
	$oL->s_additem('indays');
	$oL->s_additem('outdays');   
    
	//����sql��filter�ִ�����
	$oL->s_deal_str(); 
	
	//����������Ŀ ********************
	$oL->o_additem('deleteAnswer',array('answertype'=>$answertype));
	$oL->o_additem('checkAnswer',array('answertype'=>$answertype));
	$oL->o_additem('uncheckAnswer',array('answertype'=>$answertype));	
    $oL->o_additem('isanswer',array('answertype'=>$answertype));
    $oL->o_additem('noanswer',array('answertype'=>$answertype));    

	if(!submitcheck('bsubmit')){
		
		//�������� ******************
		$oL->s_header();
		$oL->s_view_array();
        $oL->s_footer();	
		
		//��ʾ�б���ͷ�� ***************
        $oL->m_header_ex($answertype,$entry,$extend_str,$filterstr,$aid);
		$oL->m_additem('selectid'); 
		$oL->m_additem('subject',array('len' => 40,'title'=>'��������','type'=>'url')); 
        $oL->m_additem('content',array('len' => 40,'title'=>'����','side'=>'L'));
		$oL->m_additem('isanswer',array('type'=>'bool','title'=>'��Ѵ�'));
        $oL->m_additem('ask_type',array('title'=>'�ʴ���ʽ','width'=>80));        
		$oL->m_additem('checked',array('type'=>'bool','title'=>'���'));
        $oL->m_additem('ip',array('type'=>'other','title'=>'��ԴIP'));
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