<?php
 
$cuid = 46; //�����ⲿ��chid����Ҫ��������
$caid = empty($caid) ? 0 : max(1,intval($caid));
$chid = empty($chid) ? 3 : max(2,intval($chid)); 
$cid = empty($cid) ? 0 : max(0,intval($cid));
$mid = $curuser->info['mid'];
$class = empty($cid) ? 'cls_culist' : 'cls_cuedit'; 
$_init = array(
	'cuid' => $cuid,//����ģ��id
	'ptype' => 'u',
	'pchid' => $chid,
	'caid' => $caid,
	'url' => "", //��url���������Ҫ����mchid
	'select'=>" SELECT cu.*,a.aid,a.chid,a.caid,a.createdate,a.initdate,a.customurl,a.nowurl,a.subject,a.mid as fy_title,a.color ,cu.createdate AS cucreate ",
	'from'=>"  FROM {$tblprefix}commu_fyyx cu INNER JOIN {$tblprefix}".atbl($chid)." a ON a.aid=cu.aid ",
	'where' => " AND a.mid=$mid ", //��������,ǰ����Ҫ[ AND ]
);


if($cid){
	$_init['cid'] = $cid;
	$oA = new $class($_init);
	$oA->top_head(array('chkData'=>1,'setCols'=>1));

	if(!submitcheck('bsubmit')){
		$oA->fm_header();		
		$oA->fm_items('',array(),array('noaddinfo'=>1));			
		$oA->fm_footer('bsubmit');
	}else{
	    //�ύ��Ĵ���
		$oA->sv_all_common();
	}
	
}else{
	$oL = new $class($_init); 
	
	$oL->top_head();
    
	//������Ŀ ****************************
	$oL->s_additem('keyword',array('fields' => array('a.subject'=>'����Դ'),'custom'=>1));
	$oL->s_additem('indays');
	$oL->s_additem('outdays');   
    
	//����sql��filter�ִ�����
	$oL->s_deal_str(); 
	
	//����������Ŀ ********************
	$oL->o_additem('delete');


	if(!submitcheck('bsubmit')){
	    if(empty($tmp)){
        	$cfgs = array(
				'2'=>array('zufang','chuzu'),
				'3'=>array('maifang','chushou'),
				'117'=>array('maifang','bussell_office'),
				'118'=>array('maifang','bussell_shop'),
				'119'=>array('zufang','busrent_office'),
				'120'=>array('zufang','busrent_shop'),
			);
        	backnav($cfgs[$chid][1],$cfgs[$chid][0]);
        }
		//�������� ******************
		$oL->s_header();
		$oL->s_view_array();        
        $oL->s_footer_ex("?action=export_excel_items&chid=$chid&cuid=$cuid&filename=usedhouse".($chid==2?'chuzu_':'userhouse_')."yixiang");
        
		
		//��ʾ�б���ͷ�� ***************
		$oL->m_header();
		$oL->m_additem('selectid'); 
		$oL->m_additem('subject',array('len' => 40,'title'=>'����Դ')); 
	
		$oL->m_additem('uname',array('title'=>'��ϵ��'));
        $oL->m_additem('utel',array('title'=>'��ϵ�绰'));
        
		
		$oL->m_additem('cucreate',array('type'=>'date','title'=>'��������'));        
		$oL->m_additem('detail',array('type'=>'url','title'=>'�༭','mtitle'=>'����','url'=>"?action=$action&cuid=$cuid&cid={cid}&chid=$chid",'width'=>40,));
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