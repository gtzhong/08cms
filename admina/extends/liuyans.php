<?php

// *** �̼����� ���ýű� 
$cuid = 5; //�����ⲿ��chid����Ҫ��������
$mchid = empty($mchid) ? 2 : max(2,intval($mchid));
$cid = empty($cid) ? 0 : max(0,intval($cid));
$pid = empty($pid) ? 0 : max(0,intval($pid));
$class = empty($cid) ? 'cls_culist' : 'cls_cuedit'; 
$corpid = $mchid==2 ? 'xingming' : 'cmane';
//$corpnm = $mchid==2 ? '����' : '��˾��';

$_init = array(
	'cuid' => $cuid,//����ģ��id
	'ptype' => 'm',
	'pchid' => $mchid,
	'url' => "?entry=$entry$extend_str&mchid=$mchid&cuid=$cuid", //��url���������Ҫ����mchid
	'select'=> ",$corpid ", //
	'where' => '', //��������,ǰ����Ҫ[ AND ]
	'from' => " INNER JOIN {$tblprefix}members_sub s ON s.mid=m.mid INNER JOIN {$tblprefix}members_$mchid c ON c.mid=m.mid ", //
);


if($cid){ 

	$_init['cid'] = $cid;
	$oA = new $class($_init);  
	$oA->top_head();
	if(empty($oA->predata)) $oA->message('���������ݣ�'); // print_r($oA->predata);
	
	$oA->additems();
	//���ֳ��̼�,��Ҫ����
	$pinfo = $oA->getPInfo('m',$oA->predata['tomid']); 
	
	if(!submitcheck('bsubmit')){
		$oA->fm_header("","?entry=extend$extend_str&mchid=$mchid&cuid=$cuid&pid=$pid");
		//if($pinfo['mchid']==2){
			//$oA->fm_zychexing();
		//}
		$oA->items_did[] = 'chexing';
		$oA->fm_items();
		$oA->fm_footer('bsubmit');
		$oA->guide_bm('','0');
	}else{
		//�ύ��Ĵ���
		
		$oA->sv_set_fmdata();//����$this->fmdata�е�ֵ
		$oA->sv_items();//�������µ�������Ŀ������ʱδִ�����ݿ����
		$oA->sv_retime('replydate','reply');
		$oA->sv_update();//ִ���Զ��������������ϱ��
		$oA->sv_upload();//�ϴ�����
		$oA->sv_finish(array());//����ʱ��Ҫ�����񣬰���������¼���ɹ���ʾ��

	}
	
}else{
	
	// *** ���н����б�һ�����������µ��б��ظ��б����������༭ ���ýű�
	// ��sql������,����"new $class()"֮ǰ
	if($pid){
		$_init['where']	= " AND cu.tomid='$pid'";
	}else{
			
	}
	$oL = new $class($_init); 
	$oL->top_head();
	if(!in_array($mchid,array(2,3))) $oL->message('����');
	// "new $class()"֮�����ж�title��
	if($pid){
		$pinfo = $oL->getPInfo($oL->ptype,$pid); 
		$link = htmlspecialchars($pinfo['mname']);
		$link = "<a href=\"$pinfo[mspacehome]\" target='_blank'>$link&gt;&gt;</a>";
		$title = "�����б� --- $link ";
	}else{
		$title = "";
	}

	//������Ŀ ****************************
	$oL->s_additem('keyword',array('fields' => array('cu.content'=>'��������',$corpid => '������Ա',)));
	//$oL->s_additem('diyu',array('type'=>'field'));
	$oL->s_additem('checked');
	$oL->s_additem('indays');
	$oL->s_additem('outdays');
	//����sql��filter�ִ�����
	$oL->s_deal_str(); //echo $oL->sqlall;
	
	//����������Ŀ ********************
	$oL->o_additem('delete',array());
	$oL->o_additem('delbad',array()); //ɾ��(�ۻ���)
	$oL->o_additem('check');
	$oL->o_additem('uncheck');

	if(!submitcheck('bsubmit')){
		
		//�������� ******************
		$oL->s_header();
		$oL->s_view_array();
		$oL->s_footer();
		
		//��ʾ�б���ͷ�� ***************
		$oL->m_header($title);
		$oL->m_additem('selectid'); 
		empty($pid) && $oL->m_additem('subject',array('len'=>40,'field'=>$corpid)); // *** pid��Ϊ������ʾ��������
		$oL->m_additem('content',array('len'=>30,'title'=>'��������','side'=>'L'));
		$oL->m_additem('cu_mname',array('title'=>'��Ա','side'=>'L'));
		//$oL->m_additem('diyu',array('type'=>'field','title'=>'����','side'=>''));
		$oL->m_additem('checked',array('type'=>'bool','title'=>'���',));
		$oL->m_additem('cucreate',array('type'=>'date',)); 
		$oL->m_additem('replydate',array('type'=>'date','title'=>'�ظ�ʱ��',));
		$oL->m_additem('detail',array('type'=>'url','title'=>'�༭','mtitle'=>'����','url'=>"?entry=extend$extend_str&cuid=$cuid&cid={cid}",'width'=>40,));
		
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