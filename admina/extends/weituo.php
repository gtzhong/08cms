<?php
 
$cuid = 36;
$caid = empty($caid)?3:max(1,intval($caid));
$chid = $caid==3 ? 3 : 2;
$cid = empty($cid) ? 0 : max(0,intval($cid));
$aid = empty($aid) ? 0 : max(0,intval($aid));




$class = empty($cid) ? 'cls_culist' : 'cls_cuedit'; 
$isreply = empty($isreply) ? 0 : 1;
$_init = array(
	'cuid' => $cuid,//����ģ��id
	'ptype' => 'u',
	'pchid' => $chid,
	'caid' => $caid,
	'url' => "", //��url���������Ҫ����mchid
	'select'=>" SELECT cu.*,cu.createdate AS cucreate,cu.chid as cu_chid,a.aid,a.createdate,a.initdate,a.caid,a.chid,a.customurl,a.nowurl,a.subject as ex_subject,a.mid ",
	'from'=>" FROM {$tblprefix}commu_weituo cu LEFT JOIN {$tblprefix}".atbl(4)." a ON a.aid=cu.pid ",
	'where' => " AND cu.chid='$chid' ", //��������,ǰ����Ҫ[ AND ]
);


if($cid){
	$_init['cid'] = $cid;
	$oA = new $class($_init);
	$oA->top_head(array('chkData'=>1,'setCols'=>1));

	if(!submitcheck('bsubmit')){
		$oA->fm_header("");			
		$oA->fm_items('');
		$oA->fm_footer('bsubmit');
        $oA->fm_header("ί�м�¼�鿴");	
        $oA->fm_wt_info($cid);
        $oA->fm_footer('');
		$oA->guide_bm('','0');
	}else{
		//�ύ��Ĵ���
		$oA->sv_all_common();
	}
	
}else{
	$oL = new $class($_init); 
	$oL->top_head();

	//������Ŀ ****************************
	$oL->s_additem('keyword',array('fields' => array('a.subject'=>'С������','cu.mname' => 'ί����',),'custom'=>1)); 
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
		$oL->m_header( );
		$oL->m_additem('selectid'); 
		$oL->m_additem('ex_subject',array('len'=>40,'title'=>'С������'));        
		$oL->m_additem('mname',array('title'=>'��Ա'));
		$oL->m_additem('wtlx',array('title'=>'ί������','width'=>90));
        $oL->m_additem('mj',array('title'=>'���','mtitle'=>"{mj}ƽ����"));
        $oL->m_additem('shi',array('title'=>'��','mtitle'=>"{shi}��"));
        $oL->m_additem('ting',array('title'=>'��','mtitle'=>"{ting}��"));
        $oL->m_additem('wei',array('title'=>'��','mtitle'=>"{wei}��"));
		
		if($chid==2){//����
			$oL->m_additem('zj',array('title'=>'���','mtitle'=>"{zj}Ԫ/��"));
		}elseif($chid==3){//����
			$oL->m_additem('zj',array('title'=>'�ܼ�','mtitle'=>"{zj}��Ԫ"));
		}
		

        $oL->m_addgroup('{mj}/{shi}/{ting}/{wei}/{zj}','������Ϣ');//��ע����鲻��Ƕ�ף�ÿ��ֻ�ܲ���һ�η���
		$oL->m_additem('cucreate',array('type'=>'date','title'=>'ί��ʱ��'));        
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