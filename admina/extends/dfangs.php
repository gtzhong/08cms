<?php

$cuid = 8; //�����ⲿ��chid����Ҫ��������
$caid = 5;
$chid = 5;

$admadd = empty($admadd) ? '' : $admadd;
$aid = empty($aid) ? 0 : max(0,intval($aid));
$cid = empty($cid) ? 0 : max(0,intval($cid));
$aid_url = empty($aid)?'':"&aid=$aid";

$aid_sql = empty($aid)?'':" AND cu.aid= '$aid'";
$class = empty($cid) ? 'cls_culist' : 'cls_cuedit'; 
$isreply = empty($isreply) ? 0 : 1;

$_init = array(
	'cuid' => $cuid,//����ģ��id
	'ptype' => 'a',
	'pchid' => $chid,
	'caid' => $caid,
	'url' => "$aid_url", //��url���������Ҫ����mchid
	'select'=>' ,b.subject as loupan ',
	'from'=>" LEFT JOIN {$tblprefix}archives15 b ON b.aid=a.pid3 ",
	'where' => " $aid_sql ", //��������,ǰ����Ҫ[ AND ]
);

if($admadd){ 
	
	$_init = array(
		'cuid' => $cuid,//����ģ��id
		'ptype' => 'a',
		'pchid' => $chid,
		'caid' => $caid,
		'url' => "$aid_url", //��url���������Ҫ����mchid
		'select'=>'',
		'from'=>'',
		'where' => "", //��������,ǰ����Ҫ[ AND ]
	);
	
	$oA = new cls_cuedit($_init);
	$oA->top_head(array('setCols'=>1));
	
	if(!submitcheck('bsubmit')){
		$oA->fm_header("�·��Ź�  -  ��ӽ���","?entry=extend$extend_str$aid_url&admadd=$admadd");		
		$oA->fm_dghx($aid);//��������
		$oA->fm_items('',array('dghx'));		
		$oA->fm_footer('bsubmit');
		$oA->guide_bm('','0');
	}else{
		$oA->sv_set_fmdata();//����$this->fmdata�е�ֵ
		$oA->sv_items();//�������ݵ����飬��ʱδִ�����ݿ����
		$oA->sv_insert(array('aid'=>$aid,'ip'=>$onlineip,'istrue'=>0));//ִ��insert, ���Ӳ���
		$oA->sv_upload();//�ϴ�����
		$oA->sv_finish(array('message'=>'��ӳɹ�'));
	}

}elseif($cid){
	$_init['cid'] = $cid;
	$oA = new $class($_init);
	$oA->top_head(array('chkData'=>1,'setCols'=>1));

	if(!submitcheck('bsubmit')){
		$oA->fm_header("");		
		$oA->fm_dghx();//��������
		$oA->fm_items('',array('dghx'));		
		$oA->fm_footer('bsubmit');
		$oA->guide_bm('','0');
	}else{
		//�ύ��Ĵ���
		$oA->sv_all_common();
	}
	
}else if($aid){
	$oL = new $class($_init); 
	$oL->top_head();
    !isset($istrue) && $istrue = -1;

	//������Ŀ ****************************
	$oL->s_additem('keyword',array('fields' => array('a.subject'=>'�Ź��','b.subject' => '¥������',),'custom'=>1));
    //ɸѡ�����Ϣ
    $oL->s_additem('istrue');
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
		//echo $oL->sqlall;
	//	$oL->s_footer();
        $oL->s_footer_ex("?entry=extend&extend=export_excel&chid=$chid&cuid=$cuid&filename=dfangs");
		
		//��ʾ�б���ͷ�� ***************
		$oL->m_header('', $aid, $aid ? " &nbsp; <a href='?entry=extend&extend=$extend='>ȫ���Ź�&gt;&gt;</a>" : '');
		$oL->m_additem('selectid');	
		$oL->m_additem('mname',array('title'=>'��Ա'));
		$oL->m_additem('lxren',array('title'=>'������'));
		$oL->m_additem('lxdh',array('title'=>'��ϵ�绰'));
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
}else{
	$oL = new $class($_init); 
	$oL->top_head();   
    !isset($istrue) && $istrue = -1;

	//������Ŀ ****************************
	$oL->s_additem('keyword',array('fields' => array('a.subject'=>'�Ź��','b.subject' => '¥������',),'custom'=>1));
    //ɸѡ�����Ϣ
    $oL->s_additem('istrue');
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
		$oL->s_footer_ex("?entry=extend&extend=export_excel&chid=$chid&cuid=$cuid&filename=dfangs");
		
		//��ʾ�б���ͷ�� ***************
		$oL->m_header( );
		$oL->m_additem('selectid'); 
		$oL->m_additem('subject',array('len'=>40,'title'=>'�����Ź��')); 
        $oL->m_additem('loupan',array('title'=>'¥��'));	
		$oL->m_additem('mname',array('title'=>'��Ա'));
		$oL->m_additem('lxren',array('title'=>'������'));
		$oL->m_additem('xinbie',array('title'=>'�Ա�'));
		$oL->m_additem('lxdh',array('title'=>'��ϵ�绰'));
		$oL->m_additem('ip',array('type'=>'other','title'=>'��ԴIP'));
        $oL->m_additem('istrue',array('type'=>'bool','title'=>'��ʵ����'));
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