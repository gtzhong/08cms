<?php
 
$cuid = 50; //�����ⲿ��chid����Ҫ��������
$caid = empty($caid) ? 0 : max(1,intval($caid));
$chid = 113; 
$cid = empty($cid) ? 0 : max(0,intval($cid));
$mid = $curuser->info['mid'];
$class = empty($cid) ? 'cls_culist' : 'cls_cuedit'; 
$_init = array(
	'cuid' => $cuid,//����ģ��id
	'ptype' => 'u',
	'pchid' => '',
	'caid' => $caid,
	'url' => "", //��url���������Ҫ����mchid
	'select'=>" SELECT cu.* ",
	'from'=>"  FROM {$tblprefix}commu_customers cu INNER JOIN {$tblprefix}commu_customer b ON cu.lxdh=b.lxdh",
	'where' => " AND b.mid=$mid GROUP BY cu.lxdh", //��������,ǰ����Ҫ[ AND ]
);


if($cid){
	$_init['cid'] = $cid;
	$oA = new $class($_init);
	$oA->top_head(array('chkData'=>1,'setCols'=>1));
    $oA->items_did[] = 'valid';
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
	$oL->s_additem('keyword',array('fields' => array('cu.xingming'=>'���Ƽ�������'),'custom'=>1));
	$oL->s_additem('indays');
	$oL->s_additem('outdays');   
    
	//����sql��filter�ִ�����
	$oL->s_deal_str(); 
	
	//����������Ŀ ********************
	$oL->o_additem('delete');

    echo $oL->sqlall;
	if(!submitcheck('bsubmit')){
        backnav('distribution','manage');
        
		//�������� ******************
		$oL->s_header();
		$oL->s_view_array();        
        $oL->s_footer();
        
		
		//��ʾ�б���ͷ�� ***************
		$oL->m_header();
		$oL->m_additem('selectid'); 
	//	$oL->m_additem('subject',array('len' => 40,'title'=>'¥�̷�������','type'=>'url','url'=>"{$cms_abs}mspace/archive.php?mid={mid}&aid={aid}")); 
	
		$oL->m_additem('xingming',array('title'=>'���Ƽ���'));
        $oL->m_additem('lxdh',array('title'=>'���Ƽ��˵绰'));	
		$oL->m_additem('createdate',array('type'=>'date','title'=>'�Ƽ�����'));        
        //$oL->m_additem('tjdqsj',array('type'=>'date','title'=>'��������'));
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