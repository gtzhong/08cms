<?php
$chid = 104; //$caid = 513;

if($curuser->info['mchid'] == 3){//���ھ��͹�˾
	//$caid = 554; //�ҵ���ĿID
}else{
	//$caid = 512;
}

#-----------------

$oL = new cls_archives(array(
'chid' => $chid,//ģ��id������
'url' => "?action=$action",//��url���������Ҫ����chid��pid
'pre' => 'a.',//Ĭ�ϵ�����ǰ׺
'where' => "a.mid='{$curuser->info['mid']}'",//sql�еĳ�ʼ��where���޶�Ϊ���ѵ��ĵ�
'from' => "",//sql�е�FROM����
'select' => "",//sql�е�SELECT����
'cols' => 0,//Ĭ��Ϊ0����Ϊ����1��Ϊ�����ĵ�ģʽ����ͼƬ�б�(�趨һ��Ԫ�ز���Ҫ������)
//'coids' => array(19,31),//�ֶ�����������ϵ
//'fields' => array(),//�������װ�����ֶλ���
));
//ͷ���ļ����������
$oL->top_head();

//������Ŀ ****************************
//s_additem($key,$cfg)
$oL->s_additem('keyword',array('fields' => array(),));//keys������Ĭ��Ϊarray('a.subject' => '����','a.mname' => '��Ա','a.aid' => '�ĵ�ID')
$oL->s_additem('caid',array('hidden' => 1,));
$oL->s_additem('orderby');
//$oL->s_additem('shi',array('type'=>'field',));
$oL->s_additem('valid');
foreach($oL->A['coids'] as $k){
	$oL->s_additem("ccid$k",array());
}
$oL->s_additem('indays');
$oL->s_additem('outdays');

//����sql��filter�ִ�����
$oL->s_deal_str();

$style = " style='font-weight:bold;color:#F00'"; $valid_msg = "";
//������������
$exconfigs = cls_cache::cacRead('exconfigs',_08_EXTEND_SYSCACHE_PATH);
if($curuser->info['grouptype31']) $exconfigs = $exconfigs['gssendrules'][$curuser->info['grouptype31']][$chid];
if($curuser->info['grouptype32']) $exconfigs = $exconfigs['sjsendrules'][$curuser->info['grouptype32']][$chid];

$ntotal = cls_DbOther::ArcLimitCount($chid, '');
empty($exconfigs['total']) && $exconfigs['total'] = 999999;

$msgstr = "�ѷ���:<span$style>$ntotal/$exconfigs[total]</span>����";


//����������Ŀ ********************
$oL->o_additem('delete');//ɾ��

$oL->o_additem('valid',array('days'=>0));//�ϼܣ�days�����ϼܵ�������0��Ϊ������ 'days' => 30
$oL->o_additem('unvalid');//�¼�
//$oL->o_additem('caid');

//$oL->o_additem("ccid19");

if(!submitcheck('bsubmit')){
	
	//�������� ******************
	backnav('designNews','manage');
	$oL->guide_bm($msgstr,'fix');
	$oL->s_header();
	$oL->s_view_array(array('keyword','orderby','checked',));//�̶���ʾ��
	//$oL->s_adv_point();//����������
	$oL->s_view_array();
	$oL->s_footer();
	

	//��ʾ�б���ͷ�� ***************
	$oL->m_header();
	
	//�����б���Ŀ������б����а������������Ҫ�����ݴ���ʱ������������Ĵ���
	//���飬���ȳ��ֵ��������м��룺'group' =>'item,���ݷָ���,�����ָ���',���ݷָ�������ֱ������,�����б���ķָ���������ֻʹ�õ�һ�����
	
	$oL->m_additem('selectid');
	$oL->m_additem('subject',array('len' => 40,'mc'=>1));
	//$oL->m_additem('caid');
	$oL->m_additem('clicks',array('title'=>'���',));
	foreach($oL->A['coids'] as $k){
		$oL->m_additem("ccid$k",array('view'=>'',));
	}
	$oL->m_additem('valid',array('title'=>'�ϼ�'));
//	$oL->m_additem('shi',array('type'=>'field',));
//	$oL->m_additem('ting',array('type'=>'field',));
//	$oL->m_additem('createdate',array('type'=>'date',));
	$oL->m_additem('refreshdate',array('type'=>'date','view'=>'',));	
	//$oL->m_additem('enddate',array('type'=>'date',));
	$oL->m_additem('info',array('type'=>'url','title'=>'����','mtitle'=>'����','url'=>"?action=archiveinfo&aid={aid}",'width'=>40,));
	$oL->m_additem('detail',array('type'=>'url','title'=>'�༭','mtitle'=>'����','url'=>"?action=designNews_a&aid={aid}",'width'=>40,));
	
	//$oL->m_addgroup('{shi}/{ting}','{shi}/{ting}');//��ע����鲻��Ƕ�ף�ÿ��ֻ�ܲ���һ�η���
	//$oL->m_mcols_style("{selectid} &nbsp;{subject}<br>{shi}/{ting]/{chu}");//�����ĵ�ģʽ������ʾ��Ŀ�������ʽ,Ĭ��Ϊ��"{selectid} &nbsp;{subject}"
	
	//��ʾ�����У����ж���չʾ�Ļ�����Ҫ
	$oL->m_view_top();
	
	//ȫ���б������������Ҫ���ƣ�����ʹ�����е�ϸ�ַ���
	$oL->m_view_main();
	
	//��ʾ�б���β��
	$oL->m_footer();
	
	//��ʾ����������************
	$oL->o_header();
	
	//��ʾ��ѡ��
	//$oL->o_view_bools('���б���',array('bool1','bool2',));
	$oL->o_view_bools();
	
	//��ʾ������
	$oL->o_view_rows();
	
	$oL->o_footer('bsubmit');
	$oL->guide_bm('','0');
	
}else{
	//Ԥ����δѡ�����ʾ
	$oL->sv_header();
	
	//�б���������������ݴ���
//	$oL->sv_e_additem('clicks',array());
//	$oL->sv_e_all();
	
	//��������������ݴ���
	$oL->sv_o_all();
	
	//��������
	$oL->sv_footer();
}
?>