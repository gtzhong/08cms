<?PHP
$chid = empty($chid) ? 2 : max(0,intval($chid));
if(!in_array($chid,array(2,3))) $chid = 2;


#-----------------

$oL = new cls_archives(array(
'chid' => $chid,//ģ��id������
'url' => "?action=$action",//��url���������Ҫ����chid��pid
'pre' => 'a.',//Ĭ�ϵ�����ǰ׺
'where' => "a.mid='{$curuser->info['mid']}'",//sql�еĳ�ʼ��where���޶�Ϊ���ѵ��ĵ�
'from' => "",//sql�е�FROM����
'select' => "",//sql�е�SELECT����
'cols' => 0,//Ĭ��Ϊ0����Ϊ����1��Ϊ�����ĵ�ģʽ����ͼƬ�б�(�趨һ��Ԫ�ز���Ҫ������)
'coids' => array(19,),//�ֶ�����������ϵ
//'fields' => array(),//�������װ�����ֶλ���
));
//ͷ���ļ����������
$oL->top_head();

//������Ŀ ****************************
//s_additem($key,$cfg)
$oL->s_additem('keyword',array('fields' => array(),));//keys������Ĭ��Ϊarray('a.subject' => '����','a.mname' => '��Ա','a.aid' => '�ĵ�ID')
$oL->s_additem('caid',array('hidden' => 1,));
$oL->s_additem('orderby');

$oL->s_additem('valid');
foreach($oL->A['coids'] as $k){
	$oL->s_additem("ccid$k",array());
}
$oL->s_additem('indays');
$oL->s_additem('outdays');

//����sql��filter�ִ�����
$oL->s_deal_str();

//����������Ŀ ********************


// �޶���ƣ�
$style = " style='font-weight:bold;color:#F00'";
$exconfigs = cls_cache::cacRead('exconfigs',_08_EXTEND_SYSCACHE_PATH);
if(!($rules = @$exconfigs['yysx'])) cls_message::show('ϵͳû��ԤԼˢ�¹���');
if(empty($curuser->info['grouptype14'])){
	$exconfigs = $exconfigs['fanyuan'][0];	
}else{
	$exconfigs = $exconfigs['fanyuan'][$curuser->info['grouptype14']];	
}
$msgstr = "�������Ƽ�����Ϣ���ڻ�Ա�ռ���ҳ��ʾ��";
$oL->o_additem("ccid19",array('title'=>'�����Ƽ�')); 


if(!submitcheck('bsubmit')){
	
	//�������� ******************
	backnav('tuijian',"tjchid$chid");
	$oL->guide_bm($msgstr,'fix');
	$oL->s_header();
	//$oL->s_view_array(array('keyword','orderby','checked',));//�̶���ʾ��
	//$oL->s_adv_point();//����������
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
	foreach($oL->A['coids'] as $k){
		$oL->m_additem("ccid$k",array());
	}
	$oL->m_additem('valid');
	$oL->m_additem('refreshdate',array('type'=>'date',));	 
	$oL->m_additem('enddate',array('type'=>'date',));
	$oL->m_additem('info',array('type'=>'url','title'=>'����','mtitle'=>'����','url'=>"?action=archiveinfo&aid={aid}",'width'=>40,));
	$oL->m_additem('detail',array('type'=>'url','title'=>'�༭','mtitle'=>'����','url'=>"?action=".($chid=='2' ? 'chuzu' : 'chushou')."add&aid={aid}",'width'=>40,));
	
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
	
	//��������������ݴ���
	$oL->sv_o_all();
	
	//��������
	$oL->sv_footer();
}
?>