<?PHP
/*
** ����λ�����ĵ����б��������ͨ�õ��ĵ������Ƽ�λ����ʽʹ�ýű�
** 
** 
*/ 
/* ������ʼ������ */
$paid = cls_PushArea::InitID(@$paid);//��ʼ���Ƽ�λID
if(!($pusharea = cls_PushArea::Config($paid))) exit('��ָ����ȷ������λ');
if($pusharea['sourcetype'] != 'archives') exit('����λ��ԴӦΪ�ĵ�����');

$chid = $pusharea['sourceid'];//�ĵ�ģ��chid
//�ж�-����������Ŀ
$idarr = array();
$pusharea['smallids'] && $idarr = array_filter(explode(',',$pusharea['smallids']));
//�ж�-����ģ�ͱ�
$from = (isset($pusharea['sourceadv']) && !empty($pusharea['sourceadv']))?"{$tblprefix}".atbl($chid)." a INNER JOIN {$tblprefix}archives_$chid c ON a.aid = c.aid":"";
$_init = array(
'chid' => $chid,//ģ��id������
'url' => "?entry=$entry$extend_str",//��url���������Ҫ����chid��paid
'isab' => 3,//*** ����ģʽ���ã�0Ϊ��ͨ�����б�1Ϊ���ڹ����б�2Ϊ���������б�3Ϊ����λ���ع���
'paid' => $paid,//*** ָ���Ƽ�λid
'from' => $from,
);


/******************/

$oL = new cls_archives($_init);

//ͷ���ļ����������
$oL->top_head();

//������Ŀ ****************************
//���������Ŀ��s_additem($key,$cfg)
$oL->s_additem('keyword',array('fields' => array(),));//keys������Ĭ��Ϊarray('a.subject' => '����','a.mname' => '��Ա','a.aid' => '�ĵ�ID')
$oL->s_additem('caid',array('ids'=>$idarr));
foreach($oL->A['coids'] as $k){
	$oL->s_additem("ccid$k",array());
}
$oL->s_additem('orderby');

//����sqlall��acount��filter�ִ�����
$oL->s_deal_str();

if(!submitcheck('bsubmit')){
	
	//������ʾ���� ****************************
	$oL->s_header();
	$oL->s_view_array();
	$oL->s_footer();
	

	//�����б��� **************************
	$oL->m_header();
	
	//�����б���Ŀ
	$oL->m_additem('selectid');
	$urlfrom = @$pusharea['sourcefields']['url']['from'];
	$ismcurl = strstr($urlfrom,'{marcurl}') ? 1 : 0; //������urlΪmarcurl,����marcurl��Ϊ����; �ڴ˹���֮��,����չ�ű���
	$oL->m_additem('subject',array('len' => 40,'mc'=>$ismcurl));
	$oL->m_additem('caid');
	$oL->m_additem('clicks',array('title'=>'���',));
	foreach($oL->A['coids'] as $k){
		$oL->m_additem("ccid$k",array('view'=>'H',));
	}
	
	$oL->m_additem('createdate',array('type'=>'date',));
	$oL->m_additem('mname',array('title'=>'��Ա',));
	$oL->m_additem('info',array('type'=>'url','title'=>'����','mtitle'=>'����','url'=>"?entry=extend&extend=archiveinfo&aid={aid}",'width'=>40,));
	
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
	$oL->sv_o_pushload();
}
?>