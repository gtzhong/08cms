<?PHP
/* ҳ�������ʼ�� *************************************/
$chid = 102; $arid = 31;
//��ʼ���ϼ�id��ֻ����pid��������id��ʽ��������ҪתΪpid
$pid = empty($pid) ? 0 : max(0,intval($pid));
$isab = empty($isab) ? 1 : max(1,intval($isab));

$_init = array(
'chid' => $chid,//ģ��id������
'url' => "?action=$action&isab=$isab",//��url���������Ҫ����chid��pid
'cols' => 0,//Ĭ��Ϊ0����Ϊ����1��Ϊ�����ĵ�ģʽ����ͼƬ�б�(�趨һ��Ԫ�ز���Ҫ������)
'isab' => $isab,//*** �Ƿ�ϼ��ڹ���0Ϊ��ͨ�����б�1Ϊ���ڹ����б�2Ϊ���������б�
'pid' => $pid,//�ϼ�id
'arid' => $arid,//*** ָ���ϼ���Ŀid
'pids_allow' => 'self',//*** pid����ķ�Χ���ڻ�Ա���ı����������ǰ��Ա�Ƿ���иúϼ��Ĺ���Ȩ��
//'pids_allow' => '55,56,57',//������
);

#-----------------

$oL = new cls_archives($_init);
//ͷ���ļ����������
$oL->top_head();

if($isab==1){

	//������Ŀ ****************************
	$oL->s_additem('keyword',array('fields' => array(),));//keys������Ĭ��Ϊarray('a.subject' => '����','a.mname' => '��Ա','a.aid' => '�ĵ�ID')
	$oL->s_additem('caid',array('hidden' => 1,));
	$oL->s_additem('orderby');
	$oL->s_additem('valid');
	$oL->s_additem('indays');
	
	//����sql��filter�ִ�����
	$oL->s_deal_str();
	
	//����������Ŀ ********************

	$oL->o_additem('readd');//ˢ��	//$oL->o_additem('valid',array('days' => 30));//�ϼܣ�days�����ϼܵ�������0��Ϊ������
	$oL->o_additem('inclear');//�˳��ϼ�
	
	$channels = cls_cache::Read('channels');
	if(!submitcheck('bsubmit')){
		
		//�������� ******************
		$oL->s_header();
		$oL->s_view_array();
		$oL->s_footer();
		
		//��ʾ�б���ͷ�� ***************
		$_title = "[{$oL->album['subject']}] �ڵ� ".$oL->channel['cname'];		
		$_link2 = "<a style=\"color:#C00\" href=\"?action=designCase_a&arid=$arid&pid31=$pid\" onclick=\"return floatwin('open_arcexit',this)\">���{$channels[$chid]['cname']}</a>";
		$oL->m_header("$_title - $_link2");
		
		$oL->m_additem('selectid');
		$oL->m_additem('subject',array('len' => 40,'mc'=>'1'));
		$oL->m_additem('clicks',array('title'=>'���',));	
		$oL->m_additem('valid');
		$oL->m_additem('createdate',array('type'=>'date',));
		$oL->m_additem('enddate',array('type'=>'date',));
		$oL->m_additem('info',array('type'=>'url','title'=>'����','mtitle'=>'����','url'=>"?action=archiveinfo&aid={aid}",'width'=>40,));
		$oL->m_additem('detail',array('type'=>'url','title'=>'�༭','mtitle'=>'����','url'=>"?action=designCase_a&aid={aid}",'width'=>40,));
		
		//��ʾ�����У����ж���չʾ�Ļ�����Ҫ
		$oL->m_view_top();
		
		//ȫ���б������������Ҫ���ƣ�����ʹ�����е�ϸ�ַ���
		$oL->m_view_main();
		
		//��ʾ�б���β��
		$oL->m_footer();
		
		//��ʾ����������************
		$oL->o_header();
		
		//��ʾ��ѡ��
		$oL->o_view_bools();
		
		//��ʾ������
		$oL->o_view_rows();
		
		$oL->o_footer('bsubmit');
		$oL->guide_bm('','0');
		
	}else{
		//Ԥ����δѡ�����ʾ
		$oL->sv_header();
		
		//��������������ݴ���
		$oL->sv_o_all();
		
		//��������
		$oL->sv_footer();
	}
}else{
	//������Ŀ ****************************
	//s_additem($key,$cfg)
	$oL->s_additem('keyword',array('fields' => array(),));//keys������Ĭ��Ϊarray('a.subject' => '����','a.mname' => '��Ա','a.aid' => '�ĵ�ID')
	$oL->s_additem('caid',array());
	$oL->s_additem('orderby');
	$oL->s_additem('valid');
	$oL->s_additem('indays');

	
	//����sql��filter�ִ�����
	$oL->s_deal_str();
	
	if(!submitcheck('bsubmit')){
		
		//�������� ******************
		$oL->s_header();
		$oL->s_view_array();
		$oL->s_footer();
	
		//��ʾ�б���ͷ�� ***************
		$oL->m_header();
	
		$oL->m_additem('selectid');
		$oL->m_additem('subject',array('len' => 40,));
		$oL->m_additem('clicks',array('title'=>'���',));
		$oL->m_additem('valid');
		$oL->m_additem('createdate',array('type'=>'date',));
		$oL->m_additem('info',array('type'=>'url','title'=>'����','mtitle'=>'����','url'=>"?action=archiveinfo&aid={aid}",'width'=>40,));
			
		//��ʾ�����У����ж���չʾ�Ļ�����Ҫ
		$oL->m_view_top();
		
		//ȫ���б������������Ҫ���ƣ�����ʹ�����е�ϸ�ַ���
		$oL->m_view_main();
		
		//��ʾ�б���β��
		$oL->m_footer();
		
		$oL->o_end_form('bsubmit','����');
		
		$oL->guide_bm('','0');
		
	}else{
		//ר����Լ��صĲ���
		$oL->sv_o_load();
	}	
}
?>