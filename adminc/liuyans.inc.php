<?php
 
$cuid = 5; //�����ⲿ��chid����Ҫ��������
$caid = empty($caid) ? 0 : max(1,intval($caid));

$cid = empty($cid) ? 0 : max(0,intval($cid));
$mid = $curuser->info['mid'];
$class = empty($cid) ? 'cls_culist' : 'cls_cuedit'; 
$_init = array(
	'cuid' => $cuid,//����ģ��id
	'ptype' => 'u',
	'pchid' => "",
	'caid' => $caid,
	'url' => "", //��url���������Ҫ����mchid
	'select'=>" SELECT cu.* ",
	'from'=>"  FROM {$tblprefix}commu_liuyan cu ",
	'where' => " AND cu.tomid=$mid ", //��������,ǰ����Ҫ[ AND ]
);


if($cid){
	$_init['cid'] = $cid;
	$oA = new $class($_init);
	$oA->top_head(array('chkData'=>1,'setCols'=>1));

	if(!submitcheck('bsubmit')){
		$oA->fm_header();		
		$oA->fm_items('',array(),array('noaddinfo'=>1));			
		$oA->fm_footer('bsubmit');
        $oA->guide_bm("<font color='red'>****С��ʾ****��</font>��������ֻ���޸Ļظ����ݣ����������޸���Ч",0);
	}else{
	    //�ύ��Ĵ���
		$oA->sv_set_fmdata();//����$this->fmdata�е�ֵ
		$oA->sv_items();//�������µ�������Ŀ������ʱδִ�����ݿ����
		$oA->sv_retime('replydate','reply');
		$oA->sv_update();//ִ���Զ��������������ϱ��
		$oA->sv_finish(array());//����ʱ��Ҫ�����񣬰���������¼���ɹ���ʾ��

	}
	
}else{
	$oL = new $class($_init); 
	
	
	$oL->top_head();
    
	//������Ŀ ****************************
	$oL->s_additem('keyword',array('fields' => array('cu.mname'=>'������'),'custom'=>1));
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
		$oL->m_header();
		$oL->m_additem('selectid'); 
		$oL->m_additem('mname',array('len' => 40,'title'=>'������')); 
        $oL->m_additem('checked',array('title'=>'���','type'=>'bool'));
		$oL->m_additem('createdate',array('type'=>'date','title'=>'����ʱ��'));    
        $oL->m_additem('replydate',array('type'=>'date','title'=>'�ظ�ʱ��'));    
		$oL->m_additem('detail',array('type'=>'url','title'=>'�ظ�','mtitle'=>'�ظ�','url'=>"?action=$action&cuid=$cuid&cid={cid}",'width'=>40,));
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