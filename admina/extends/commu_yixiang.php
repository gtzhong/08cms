<?php
 
$cuid = 46; 
$caid = empty($caid)?3:max(1,intval($caid));
$chids = array(
	3 => 3,
	4 => 2,
	613 => 117,
	617 => 118,
	614 => 119,
	618 => 120,
);
$chid = isset($chids[$caid]) ? $chids[$caid] : 2; // $caid==3 ? 3 : 2;
$cid = empty($cid) ? 0 : max(0,intval($cid)); 

$class = empty($cid) ? 'cls_culist' : 'cls_cuedit'; 
$isreply = empty($isreply) ? 0 : 1;
$aid = empty($aid)?0:max(1,intval($aid));
$aid_url = empty($aid)?'':"&aid=$aid";
$aid_sql = empty($aid)?'':" AND a.aid='$aid'  ";


$_init = array(
	'cuid' => $cuid,//����ģ��id
	'ptype' => 'a',
	'pchid' => $chid,
	'caid' => '',
	'url' => "$aid_url", //��url���������Ҫ����mchid
	'select'=>'',
	'from'=>'',
	'where' => "$aid_sql", //��������,ǰ����Ҫ[ AND ]
);


if($cid){
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

	//������Ŀ ****************************
	$oL->s_additem('keyword',array('fields' => array('a.subject'=>'��Դ����','cu.uname' => '��ϵ��','cu.mname' => '�û���'),'custom'=>1));
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
        $oL->s_footer_ex("?entry=extend&extend=export_excel&chid=$chid&cuid=$cuid&filename=kfhdbm$aid_url");

		
		//��ʾ�б���ͷ�� ***************
		$oL->m_header('', $aid, $aid ? " &nbsp; <a href='?entry=extend&extend=$extend='>ȫ������&gt;&gt;</a>" : '');
		$oL->m_additem('selectid'); 
		$oL->m_additem('subject',array('len'=>40,'title'=>'����Դ')); 
 	
		$oL->m_additem('uname',array('title'=>'��ϵ��'));
		$oL->m_additem('utel',array('title'=>'��ϵ�绰'));
		$oL->m_additem('cucreate',array('type'=>'date','title'=>'���ʱ��'));       
        $oL->m_additem('mname',array('title'=>'�û���','width'=>80)); 
        $oL->m_additem('ip',array('type'=>'other','title'=>'��ԴIP'));
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