<?php

//��չ����
$exconfigs = cls_cache::cacRead('exconfigs',_08_EXTEND_SYSCACHE_PATH);
$exfenxiao = $exconfigs['distribution']; // Array ( [num] => 3 [pnum] => 100 [vtime] => 15 [unvnum] => 10 [fxwords] => msg ) 
$exfenxiao['num'] = empty($exfenxiao['num']) ? 3 : max(1,intval($exfenxiao['num']));
$exfenxiao['vtime'] = empty($exfenxiao['vtime']) ? 15 : max(3,intval($exfenxiao['vtime']));

$cuid = 49; $mid = $curuser->info['mid']; //�����ⲿ��chid����Ҫ��������
$chid = 113;
$cid = empty($cid) ? 0 : max(0,intval($cid));
#$aid = empty($aid)?0:max(1,intval($aid));
$aid_url = empty($aid)?'':"&aid=$aid";
$aid_sql = empty($aid)?'':" AND a.aid LIKE '%,$aid,%'  ";

$class = empty($cid) ? 'cls_culist' : 'cls_cuedit'; 
$_init = array(
	'cuid' => $cuid,//����ģ��id
	'ptype' => 'e',
	'pchid' => 0,
	'url' => "$aid_url", //��url���������Ҫ����mchid
	'select'=>"",
	'from'=>"",
	'where' => " AND cu.mid='$mid' ",
	'fnoedit' => array('yxprice'),
);


if($cid){
	$_init['cid'] = $cid;
	$oA = new $class($_init);
	$oA->top_head(array('chkData'=>1,'setCols'=>1));

	if(!submitcheck('bsubmit')){
		$oA->fm_header("");	
		$oA->fm_items('xingming,dianhua',array(),array('noaddinfo'=>1));
		$oA->fma_fxlpnames($exfenxiao);	
		$oA->fma_fxyongjin('yjbase');
		$oA->fma_fxyongjin('yjextra'); 
		$oA->fm_items();
		//*
		if($oA->predata['status']=='3'){
			echo "</table>\n".(defined('M_MCENTER') ? '' : '<br />');
			echo '<div align="center"><input type="button" name="bsubmit" value="(�ɽ�����)״̬�����ύ����" disabled style="background:#999;"></div> ';
			echo "</form>\n";
		}else{
			$oA->fm_footer('bsubmit');		
		}//*/
		#$oA->fm_footer('bsubmit');	
	}else{
		//�ύ��Ĵ���
		$oA->sv_set_fmdata();//����$this->fmdata�е�ֵ
		$oA->sv_items();//�������ݵ����飬��ʱδִ�����ݿ����
		$oA->sv_excom('okaid','okaid',1); 
		if(!empty($oA->fmdata['okayj'])){
			$oA->sv_excom('okayj',$oA->fmdata['okayj']); 
		}
		if($oA->fmdata['status']=='3'){ //ȷ��ʱ��
			$oA->sv_excom('oktime',TIMESTAMP); 
		}
		$oA->sv_update();//ִ���Զ��������������ϱ��
		$oA->sv_upload();//�ϴ�����
		$oA->sv_finish();//����ʱ��Ҫ�����񣬰���������¼���ɹ���ʾ��
	}
	
}else{
	$oL = new $class($_init); 
	
	$oL->top_head();
    
	//������Ŀ **************************** 'a.subject'=>'�����',
	$oL->s_additem('keyword',array('fields' => array('cu.xingming' => '���Ƽ���','cu.dianhua' => '��ϵ�绰'),'custom'=>1));
	$oL->s_additem('status',array('xtype' =>'field'));
	$oL->s_additem('indays');
	$oL->s_additem('outdays');   
    
	//����sql��filter�ִ�����
	$oL->s_deal_str(); 
	
	//����������Ŀ ********************
	//$oL->o_additem('delete');
	//$oL->o_additem('check');
	//$oL->o_additem('uncheck');	

	if(!submitcheck('bsubmit')){
		
		//�������� ******************
		$oL->s_header();
		$oL->s_view_array();
        //$oL->s_footer_ex("?entry=extend&extend=export_excel&chid=$chid&cuid=$cuid&filename=jztgbm");	
		$oL->s_footer();	
		
		//��ʾ�б���ͷ�� ***************
		$oL->m_header();
		//$oL->m_additem('selectid'); 
		$oL->m_additem('xingming',array('title'=>'���Ƽ�������'));
        $oL->m_additem('dianhua',array());
		$oL->m_additem('cucreate',array('type'=>'date','view'=>'H'));        
		$oL->m_additem('fxend',array('type'=>'udate','title'=>'����ʱ��','showEnd'=>1,'dbkey'=>'cucreate','offset'=>$exfenxiao['vtime']*86400));   
		$oL->m_additem('status',array('type'=>'field','title'=>'�Ƽ�״̬','empty'=>'<span style="color:#999">ԤԼ����</span>')); 
		$oL->m_additem('fxlpnames',array('view'=>'')); 
		$oL->m_additem('fxyongjin',array()); //trbasic('�ϼ���ȡ','','��/��','');  
		$oL->m_additem('detail',array('type'=>'url','title'=>'�༭','mtitle'=>'����','url'=>"?action=$action&cuid=$cuid&cid={cid}",));
		$oL->m_view_top(); //��ʾ�����У����ж���չʾ�Ļ�����Ҫ
		$oL->m_view_main(); 
		$oL->m_footer(); //��ʾ�б���β��
		
		$oL->o_header(); //��ʾ����������************
		$oL->o_view_bools(); //��ʾ��ѡ��
		
		$oL->o_footer(''); //bsubmit
		$oL->guide_bm('','0');
		
	}else{
		
		$oL->sv_header(); //Ԥ����δѡ�����ʾ
		$oL->sv_o_all(); //��������������ݴ���
		$oL->sv_footer(); //��������
		
	}
			
}

?>
