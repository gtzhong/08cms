<?php
 
$cuid = 45; //�����ⲿ��chid����Ҫ��������
$caid = 560;
$chid = 110;

$admadd = empty($admadd) ? '' : $admadd;
$aid = empty($aid) ? 0 : max(0,intval($aid));
$cid = empty($cid) ? 0 : max(0,intval($cid));
$aid_url = empty($aid)?'':"&aid=$aid";

$class = empty($cid) ? 'cls_culist' : 'cls_cuedit'; 
$isreply = empty($isreply) ? 0 : 1;
$_init = array(
	'cuid' => $cuid,//����ģ��id
	'ptype' => 'u',
	'pchid' => $chid,
	'caid' => $caid,
	'url' => "&aid=$aid", //��url���������Ҫ����mchid
	'select'=>' SELECT cu.*,cu.createdate AS cucreate,cu.aid as cuaid,cu.mid as cu_mid,cu.mname as cu_mname ,a.aid,a.chid,a.caid,a.createdate,a.initdate,a.customurl,a.nowurl,a.subject ',
	'from'=>" FROM {$tblprefix}commu_kanfang cu  INNER JOIN {$tblprefix}archives15 a ON cu.yxlp = a.aid ",
	'where' => " AND ".(empty($aid) ? "1=1" : "cu.aid = $aid")." ", //��������,ǰ����Ҫ[ AND ]
);

if($admadd){

	$_init = array(
		'cuid' => $cuid,//����ģ��id
		'ptype' => 'a',
		'pchid' => 0,
		'caid' => $caid,
		'url' => "$aid_url", //��url���������Ҫ����mchid
		'select'=>'',
		'from'=>'',
		'where' => "", //��������,ǰ����Ҫ[ AND ]
	);
	
	$oA = new cls_cuedit($_init);
	$oA->top_head(array('setCols'=>1));
	
	if(!submitcheck('bsubmit')){
		$oA->fm_header("","?entry=extend$extend_str$aid_url&admadd=$admadd");
		$oA->fm_items('');		
		$oA->fm_footer('bsubmit');
		$oA->guide_bm('','0');
	}else{
		$oA->sv_set_fmdata();//����$this->fmdata�е�ֵ
		$oA->sv_items();//�������ݵ����飬��ʱδִ�����ݿ����
		$oA->sv_insert(array('aid'=>$aid,'istrue'=>0,'checked'=>1));//ִ��insert, ���Ӳ���
		$oA->sv_upload();//�ϴ�����
		$oA->sv_finish(array('message'=>'��ӳɹ�'));
	}

}elseif($cid){
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
    !isset($istrue) && $istrue = -1;

	//������Ŀ ****************************
	$oL->s_additem('keyword',array('fields' => array('a.subject'=>'¥������','tel'=>'�绰'),'custom'=>0)); //'b.subject' => '¥������',
    //ɸѡ�����Ϣ
    $oL->s_additem('istrue');
	$oL->s_additem('indays');
	$oL->s_additem('outdays');
    
   	//����������Ŀ ********************
	$oL->o_additem('delete');
    $oL->o_additem('check');
    $oL->o_additem('uncheck');
    
	//����sql��filter�ִ�����
	$oL->s_deal_str(); 
	


	if(!submitcheck('bsubmit')){
		
		//�������� ******************
		$oL->s_header();
		$oL->s_view_array();
		$oL->s_footer_ex("?entry=extend&extend=export_excel&chid=110&cuid=$cuid&aid=$aid&filename=kfhdbm");
		
		//��ʾ�б���ͷ�� ***************
		$oL->m_header('',''," &nbsp; <a href='?entry=extend&extend=$extend='>ȫ������&gt;&gt;</a>"); 
		$oL->m_additem('selectid'); 
		$oL->m_additem('subject',array('title'=>'¥������')); 
		//$oL->m_additem('yxlp'); 
	
		$oL->m_additem('xingming',array('title'=>'����'));

		$oL->m_additem('xingbie',array('title'=>'�Ա�'));
		$oL->m_additem('tel',array('title'=>'�绰'));
		//$oL->m_additem('qq',array('title'=>'QQ'));	
		$oL->m_additem('cucreate',array('type'=>'date','title'=>'����ʱ��'));
        $oL->m_additem('istrue',array('type'=>'bool','title'=>'��ʵ����'));        
		$oL->m_additem('checked',array('type'=>'bool','title'=>'���'));
		$oL->m_additem('detail',array('type'=>'url','title'=>'�༭','mtitle'=>'����','url'=>"?entry=extend$extend_str&cuid=$cuid&caid=$caid&cid={cid}&aid={cuaid}",'width'=>40,));
		
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