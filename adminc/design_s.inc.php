<?php
$chid = 101;$caid = 510;

#-----------------

$oL = new cls_archives(array(
'chid' => $chid,//ģ��id������
'url' => "?action=$action",//��url���������Ҫ����chid��pid
'pre' => 'a.',//Ĭ�ϵ�����ǰ׺
'where' => "a.mid='{$curuser->info['mid']}'",//sql�еĳ�ʼ��where���޶�Ϊ���ѵ��ĵ�
'from' => "",//sql�е�FROM����
'select' => "",//sql�е�SELECT����
'cols' => 0,//Ĭ��Ϊ0����Ϊ����1��Ϊ�����ĵ�ģʽ����ͼƬ�б�(�趨һ��Ԫ�ز���Ҫ������)
'coids' => array(19,32),//�ֶ�����������ϵ
//'fields' => array(),//�������װ�����ֶλ���
));
//ͷ���ļ����������
$oL->top_head();

//������Ŀ ****************************
$oL->s_additem('keyword',array('fields' => array(),));//keys������Ĭ��Ϊarray('a.subject' => '����','a.mname' => '��Ա','a.aid' => '�ĵ�ID')
$oL->s_additem('caid',array('hidden' => 1,));
$oL->s_additem('orderby');
$oL->s_additem('valid');
foreach($oL->A['coids'] as $k){
	$atitle = $k==32 ? array('title'=>'���') : array();
	$oL->s_additem("ccid$k",$atitle);
}
$oL->s_additem('indays');
$oL->s_additem('outdays');

//����sql��filter�ִ�����
$oL->s_deal_str();

$style = " style='font-weight:bold;color:#F00'"; $valid_msg = "";
//������������
$exconfigs = cls_cache::cacRead('exconfigs',_08_EXTEND_SYSCACHE_PATH);
$total_refresh =@ $exconfigs['gssendrules'][$curuser->info['grouptype31']]['refresh'];
$exconfigs = @$exconfigs['gssendrules'][$curuser->info['grouptype31']][$chid];

$ntotal = cls_DbOther::ArcLimitCount($chid, '');
empty($exconfigs['total']) && $exconfigs['total'] = 999999;


$refresh = $db->result_one("SELECT refreshes FROM {$tblprefix}members WHERE mid = '$memberid'");
$refresh = empty($refresh)?'0':$refresh;
$re_refresh = $total_refresh - $refresh; 
$re_refresh = $re_refresh<0 ? 0 : $re_refresh;


$msgstr = "����ˢ��:<span$style>$refresh/$total_refresh</span>����";
$msgstr .= "�ѷ���:<span$style>$ntotal/$exconfigs[total]</span>����";


//����������Ŀ ********************
$oL->o_additem('delete');//ɾ��
$oL->o_additem('readd',array('limit'=>$re_refresh,'time'=>0,'fieldname'=>'refreshes'));//ˢ�� ,'time'=>1440
$oL->o_additem('valid',array('days'=>0));//�ϼܣ�days�����ϼܵ�������0��Ϊ������ 'days' => 30
$oL->o_additem('unvalid');//�¼�

$oL->o_additem("ccid19");

if(!submitcheck('bsubmit')){
	
	//�������� ******************
	backnav('design','manage');
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
	$oL->m_additem('subject',array('len' => 40,'mc'=>1,));
	$oL->m_additem('clicks',array('title'=>'���',));
	foreach($oL->A['coids'] as $k){
		$oL->m_additem("ccid$k",array('view'=>'',));
	}
	$oL->m_additem('valid');
	$oL->m_additem('refreshdate',array('type'=>'date','view'=>'',));	
	$oL->m_additem('anlinum',array('type'=>'url','title'=>'����','mtitle'=>'[{anlinum}]','url'=>"?action=dc_pid&pid={aid}",'width'=>40,));
	$oL->m_additem('info',array('type'=>'url','title'=>'����','mtitle'=>'����','url'=>"?action=archiveinfo&aid={aid}",'width'=>40,));
	$oL->m_additem('detail',array('type'=>'url','title'=>'�༭','mtitle'=>'����','url'=>"?action=design_a&aid={aid}",'width'=>40,));
	
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