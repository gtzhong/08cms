<?PHP
/*
** �ϼ��ڵ��ĵ��б����archives_pid.php������ʾ��������������Ͷ����ʽʹ��
** ע�����ֺϼ���ϵ��1���ĵ����¼pid 2���ϼ���ϵ���ر�������select��from������ 
** �ϼ��ڵĹ����ٷ����Ƿ������Ŀ����Ȩ��
*/ 
/* ������ʼ������ */
$chid = 111;//�ű��̶����ĳ��ģ��
//$chid = empty($chid) ? 0 : max(0,intval($chid));//�����ⲿ��chid����Ҫ��������

$pid = empty($pid)?cls_message::show('��ָ��¥��ID'):max(1,intval($pid));//��ʼ���ϼ�id���п���ʹ������id��ʽ����������$hejiid�ȣ�ҪתΪʹ��pid

//Ҫָ���ϼ�id������$pidkey���ϼ���Ŀ$arid
$_arc = new cls_arcedit; //��ҵ�ز�-�ϼ�����
$_arc->set_aid($pid,array('au'=>0,'ch'=>0)); 
$_chid = $_arc->archive['chid'];
$arid = $_chid==4 ? 1 : 35;//ָ���ϼ���Ŀid
//$_abtab = $_chid==4 ? "aalbums" : "aalbums_arcs";//ָ���ϼ���Ŀid
//$arid = 1;

$_init = array(
'chid' => $chid,//ģ��id������
'url' => "?entry=$entry$extend_str",//��url���������Ҫ����chid��pid

'cols' => 0,//Ĭ��Ϊ0����Ϊ����1��Ϊ�����ĵ�ģʽ����ͼƬ�б�(�趨һ��Ԫ�ز���Ҫ������)
//'coids' => array(1),//�ֶ�����������ϵ���ڻ�Ա�����ر���Ҫָ��
//'fields' => array(),//�������װ�����ֶλ���

'isab' => 1,//*** �Ƿ�ϼ��ڹ���0Ϊ��ͨ�����б�1Ϊ���ڹ����б�2Ϊ���������б�
'pid' => $pid,//�ϼ�id
'arid' => $arid,//*** ָ���ϼ���Ŀid
//'orderby' => "b.inorder ASC",//�ϼ���ָ������,�ĵ���ϼ���¼��Ϊ"a.inorderxx DESC"��xxΪ�ϼ���Ŀid

);

/******************/

$oL = new cls_archives($_init);

//ͷ���ļ����������
$oL->top_head();

//������Ŀ ****************************
//���������Ŀ��s_additem($key,$cfg)
$oL->s_additem('keyword',array('fields' => array(),));//fields������Ĭ��Ϊarray('a.subject' => '����','a.mname' => '��Ա','a.aid' => '�ĵ�ID')
$oL->s_additem('caid',array());
//$oL->s_additem("ccid$k",array());
$oL->s_additem('checked');
$oL->s_additem('inchecked',array('field' => 'b.incheck'));//ָ���ϼ��м�������������ֶΣ����ĵ����¼��Ϊa.incheckxx��xxΪ�ϼ���Ŀid
$oL->s_additem('orderby');

//����sql��filter�ִ�����
$oL->s_deal_str();

//����������Ŀ ********************
$oL->o_additem('inclear');
$oL->o_additem('incheck');
$oL->o_additem('unincheck');

$oL->o_additem('delete');
$oL->o_additem('check');
$oL->o_additem('uncheck');
//$oL->o_additem('readd');
$oL->o_additem('static');
$oL->o_additem('nstatic');
//$oL->o_additem("ccid$k");


if(!submitcheck('bsubmit')){
	
	//������ʾ���� ****************************
	$oL->s_header();
	$oL->s_view_array();
	$oL->s_footer();

	//�����б��� **************************
	$hlinks = " &nbsp; <a style=\"color:#C00\" href=\"?entry=extend&extend=ldarchive&pid=$pid\" onclick=\"return floatwin('open_arcexit',this)\">&gt;&gt;���¥��</a>";
	$hlinks .= " &nbsp; <a style=\"color:#C00\" href=\"?entry=extend&extend=ldarchives&x_arid=$arid&x_chid=$_chid&isframe=1\" target='_blank'>&gt;&gt;����¥��</a>";
	$oL->m_header("$hlinks",1);
	
	//�����б���Ŀ
	//���飬���ȳ��ֵ��������м��룺'group' =>'item,���ݷָ���,�����ָ���',���ݷָ�������ֱ������,�����б���ķָ���������ֻʹ�õ�һ�����
	$oL->m_additem('selectid');
	$oL->m_additem('subject',array('len' => 40,));
	$oL->m_additem('caid');
	$oL->m_additem('clicks',array('type' => 'input','title'=>'���','width'=>40,'w' => 3,));
	//$oL->m_additem("ccid$k",array('view'=>'H',));
	$oL->m_additem('inorder',array('type' => 'input','title'=>'����','w' => 3,));
//	$oL->m_additem('incheck',array('type'=>'checkbox','atitle'=>'��Ч','side' => 'L','width'=>50,));
	$oL->m_additem('incheck',array('type'=>'bool','title'=>'��Ч',));
	
	$oL->m_additem('createdate',array('type'=>'date',));
//	$oL->m_additem('refreshdate',array('type'=>'date','view'=>'H',));	
//	$oL->m_additem('updatedate',array('type'=>'date','view'=>'H',));
//	$oL->m_additem('enddate',array('type'=>'date',));
	$oL->m_additem('huxing',array('type'=>'ucount','title'=>'����','url'=>"?entry=extend&extend=loudong_huxing&pid={aid}&lpid=$pid",'func'=>'gethjnum','arid'=>'37','chid'=>11,'width'=>35,));		
	$oL->m_additem('info',array('type'=>'url','title'=>'����','mtitle'=>'����','url'=>"?entry=extend&extend=archiveinfo&aid={aid}",'width'=>40,));
	$oL->m_additem('detail',array('type'=>'url','title'=>'�༭','mtitle'=>'����','url'=>"?entry=extend&extend=ldarchive&aid={aid}",'width'=>40,));
	
	//$oL->m_mcols_style();//�����ĵ�ģʽ������ʾ��Ŀ�������ʽ,Ĭ��Ϊ��"{selectid} &nbsp;{subject}"
	
	//��ʾ�����У����ж���չʾ�Ļ�����Ҫ
	$oL->m_view_top();
	
	//ȫ���б������������Ҫ���ƣ�����ʹ�����е�ϸ�ַ���
	$oL->m_view_main();
	
	//��ʾ�б���β��
	$oL->m_footer();
	
	//��ʾ����������*******************************
	$oL->o_header();
	
	//��ʾ��ѡ��
	$oL->o_view_bools('�ϼ����� ',array('inclear','incheck','unincheck',));
	$oL->o_view_bools();
	
	//��ʾ������
	$oL->o_view_rows();
	
	$oL->o_footer('bsubmit');
	$oL->guide_bm('','0');
	
}else{
	//Ԥ����δѡ�����ʾ
	$oL->sv_header();
	
	//�б���������������ݴ���
	$oL->sv_e_additem('clicks',array());
	$oL->sv_e_additem('inorder',array());
//	$oL->sv_e_additem('incheck',array('type' => 'bool'));
	$oL->sv_e_all();
	
	//��������������ݴ���
	$oL->sv_o_all();
	
	//��������
	$oL->sv_footer();
}
?>