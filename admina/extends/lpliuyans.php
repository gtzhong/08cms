<?php
 
$cuid = 48; //�����ⲿ��chid����Ҫ��������
$caid = 2;
$chid = empty($chid) ? 4 : max(0,intval($chid)); //4;

$cid = empty($cid) ? 0 : max(0,intval($cid));
$aid = empty($aid) ? 0 : max(0,intval($aid));
$aid_url = empty($aid)?'':"&aid=$aid&chid=$chid";


$aid_sql = empty($aid)?'':" AND cu.aid= '$aid'";
$class = empty($cid) ? 'cls_culist' : 'cls_cuedit'; 
$isreply = empty($isreply) ? 0 : 1;

$_init = array(
	'cuid' => $cuid,//����ģ��id
	'ptype' => 'a',
	'pchid' => $chid,
	//'caid' => $caid,
	'url' => "$aid_url", //��url���������Ҫ����mchid
	'select'=>'',
	'from'=>'',
	'where' => " $aid_sql AND cu.tocid=0 AND cu.mname !='' ", //��������,ǰ����Ҫ[ AND ]
);


if($cid && empty($isreply)){

	$_init['cid'] = $cid;
	$oA = new $class($_init);
	$oA->top_head(array('chkData'=>1,'setCols'=>1));
	
	if(!submitcheck('bsubmit')){
		$oA->fm_header("");		
		$oA->fm_items('comment');		
		$oA->fm_footer('bsubmit');
		$oA->guide_bm('','0');
	}else{
		//�ύ��Ĵ���
		$oA->sv_all_common();
	}
	
}elseif($cid && $isreply){
	$_init['cid'] = $cid;
	$oA = new $class($_init);
	$oA->top_head(array('chkData'=>1,'setCols'=>1));
	
	if(!submitcheck('bsubmit')){
		$oA->fm_header("","&cid=$cid&isreply=$isreply&chid=$chid");
		$oA->fm_replay($oA->predata);		
		$oA->fm_footer('bsubmit');
		$oA->guide_bm('','0');
	}else{
		//�ύ��Ĵ���
		$oA->sv_replay();
	}
}else{
	$oL = new $class($_init); 
	$oL->top_head();
    

	//������Ŀ ****************************
	$oL->s_additem('keyword',array('fields' => array('cu.mname'=>'������','a.subject' => '����¥��',)));
	$oL->s_additem('checked');
	$oL->s_additem('indays');
	$oL->s_additem('outdays');
	//����sql��filter�ִ�����
	$oL->s_deal_str(); 
	
	//����������Ŀ ********************
	$oL->o_additem('delete',array('exkey'=>'tocid'));
	$oL->o_additem('check');
	$oL->o_additem('uncheck');
	//echo $oL->sqlall;

	if(!submitcheck('bsubmit')){
		
		//�������� ******************
		$oL->s_header();
		$oL->s_view_array();
		$oL->s_footer();
		
		//��ʾ�б���ͷ�� ***************
		$oL->m_header('', $aid, $aid ? " &nbsp; <a onclick=\"return floatwin('open_fnodes',this)\" href='?entry=extend&extend=$extend&chid=$chid'>ȫ������&gt;&gt;</a>" : '');
		$oL->m_additem('selectid'); 
		$oL->m_additem('subject',array('len'=>40,'title'=>'����¥��')); 
        $oL->m_additem('mname',array('title'=>'������','width'=>80));	
        $oL->m_additem('recounts',array('url'=>"?entry=extend&extend=lply_replays&tocid={cid}",'title'=>'����','winsize'=>'930,480','width'=>100)); // �ظ���
        $oL->m_additem('replay',array('type'=>'url','title'=>'�ظ�','mtitle'=>'�ظ�','url'=>"?entry=extend&extend=lpliuyans&aid=$aid&cid={cid}&isreply=1&chid=$chid"));
		$oL->m_addgroup('{recounts}/{replay}','�ظ�');//��ע����鲻��Ƕ�ף�ÿ��ֻ�ܲ���һ�η���        
		$oL->m_additem('checked',array('type'=>'bool','title'=>'���',));
		$oL->m_additem('ip',array('type'=>'other','title'=>'��ԴIP'));
		$oL->m_additem('cucreate',array('type'=>'date','title'=>'���ʱ��'));        
		$oL->m_additem('detail',array('type'=>'url','title'=>'�༭','mtitle'=>'����','url'=>"?entry=extend$extend_str&cuid=$cuid&caid=$caid&cid={cid}&chid=$chid",'width'=>40,));
		
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