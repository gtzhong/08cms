<?PHP
//�ĵ�ģ��chid�ĳ�ʼ�����������ֶ�ȷ��ĳ��id
$chid = 1;
//$chid = empty($chid) ? 0 : max(0,intval($chid));//�����ⲿ��chid����Ҫ��������

//��ʼ���ϼ�id��ֻ����pid��������id��ʽ��������ҪתΪpid
$pid = empty($pid) ? 0 : max(0,intval($pid));

$_arc = new cls_arcedit; //��ҵ�ز�-�ϼ�����
$_arc->set_aid($pid,array('au'=>0,'ch'=>0)); 

$arid = $_arc->archive['chid']==4 ? 1 : 35;//ָ���ϼ���Ŀid
//echo ":::$arid";

$_init = array(
'chid' => $chid,//ģ��id������
'url' => "?action=$action&pid=$pid",//����url���������Ҫ����chid��pid

'cols' => 0,//Ĭ��Ϊ0����Ϊ����1��Ϊ�����ĵ�ģʽ����ͼƬ�б�(�趨һ��Ԫ�ز���Ҫ������)
//'coids' => array(1),//�ֶ�����������ϵ���ڻ�Ա�����ر���Ҫָ��
//'fields' => array(),//���������װ�����ֶλ���

'isab' => 2,//*** �Ƿ�ϼ��ڹ�����0Ϊ��ͨ�����б���1Ϊ���ڹ����б���2Ϊ���������б�
'pid' => $pid,//�ϼ�id
'arid' => $arid,//*** ָ���ϼ���Ŀid
//'pids_allow' => 'self',//*** pid�����ķ�Χ���ڻ�Ա���ı����������ǰ��Ա�Ƿ���иúϼ��Ĺ���Ȩ��
'pids_allow' => '-1',//����
);

#-----------------

$oL = new cls_archives($_init);
//ͷ���ļ����������
$oL->top_head();

//������Ŀ ****************************
//s_additem($key,$cfg)
$oL->s_additem('keyword',array('fields' => array(),));//keys������Ĭ��Ϊarray('a.subject' => '����','a.mname' => '��Ա','a.aid' => '�ĵ�ID')
$oL->s_additem('caid',array('ids' => array(502,504)));
$oL->s_additem('orderby');
//$oL->s_additem('shi',array('type'=>'field',));
//$oL->s_additem('ting',array('type'=>'field',));
$oL->s_additem('valid');
//$oL->s_additem("ccid$k",array());
$oL->s_additem('indays');
//$oL->s_additem('outdays');

//����sql��filter�ִ�����
$oL->s_deal_str();

if(!submitcheck('bsubmit')){
	
	//�������� ******************
	$oL->s_header();
	$oL->s_view_array();
	$oL->s_footer();
	

	//��ʾ�б���ͷ�� ***************
	$oL->m_header();
	
	//�����б���Ŀ������б����а������������Ҫ�����ݴ���ʱ������������Ĵ���
	//���飬���ȳ��ֵ��������м��룺'group' =>'item,���ݷָ���,�����ָ���',���ݷָ�������ֱ������,�����б���ķָ���������ֻʹ�õ�һ�����
	
	$oL->m_additem('selectid');
	$oL->m_additem('subject',array('len' => 40,));
	//$oL->m_additem('caid');
	$oL->m_additem('clicks',array('title'=>'���',));
	//$oL->m_additem("ccid$k",array('view'=>'H',));
	$oL->m_additem('valid');
	//$oL->m_additem('shi',array('type'=>'field',));
	$oL->m_additem('createdate',array('type'=>'date',));
	//$oL->m_additem('refreshdate',array('type'=>'date','view'=>'H',));	
	//$oL->m_additem('enddate',array('type'=>'date',));
	$oL->m_additem('info',array('type'=>'url','title'=>'����','mtitle'=>'����','url'=>"?action=archiveinfo&aid={aid}",'width'=>40,));
	
	//$oL->m_addgroup('{shi}/{ting}','{shi}/{ting}');//��ע����鲻��Ƕ�ף�ÿ��ֻ�ܲ���һ�η���
	//$oL->m_mcols_style("{selectid} &nbsp;{subject}<br>{shi}/{ting]/{chu}");//�����ĵ�ģʽ������ʾ��Ŀ�������ʽ,Ĭ��Ϊ��"{selectid} &nbsp;{subject}"
	
	//��ʾ�����У����ж���չʾ�Ļ�����Ҫ
	$oL->m_view_top();
	
	//ȫ���б��������������Ҫ���ƣ�����ʹ�����е�ϸ�ַ���
	$oL->m_view_main();
	
	//��ʾ�б���β��
	$oL->m_footer();
	
	$oL->o_end_form('bsubmit','����');
	
	$oL->guide_bm('','0');
	
}else{
	//ר����Լ��صĲ���
	$oL->sv_o_load();
}
?>