<?PHP
/*
** �����̨�ű��������ĵ��б����archives.php��Ϊ��������������������Ͷ����ʽʹ��
** chid�����ֶ���ʼ�������Ը��ݷ������Ŀ��ͬ����ʼ����ͬ��chid
** 
*/

/* ������ʼ������ */
$chid = 106;//���붨�壬�����ܴ�url�Ĵ���
//$chid = empty($chid) ? 0 : max(0,intval($chid));//�����ⲿ��chid����Ҫ��������

#-----------------

$oL = new cls_archives(array(
'chid' => $chid,//ģ��id������
'url' => "?entry=$entry$extend_str",//��url���������Ҫ����chid��pid
'pre' => '',//Ĭ�ϵ�����ǰ׺
'from' => "{$tblprefix}".atbl($chid)." a INNER JOIN {$tblprefix}archives_$chid b ON a.aid = b.aid ",//sql�е�FROM���֣�����ͨ������JOIN������
'select' => " a.*, b.currency ",//sql�е�SELECT����
'cols' => 0,//Ĭ��Ϊ0����Ϊ����1��Ϊ�����ĵ�ģʽ����ͼƬ�б�(�趨һ��Ԫ�ز���Ҫ������)
//'fields' => array(),//�������װ�����ֶλ���

));

//ͷ���ļ����������
$oL->top_head();

//������Ŀ ****************************
//s_additem($key,$cfg)
$oL->s_additem('keyword',array('fields' => array('a.subject' => '����','a.mname' => '��Ա�˺�','a.aid' => '�ĵ�ID'),));//fields������Ĭ��Ϊarray('a.subject' => '����','a.mname' => '��Ա','a.aid' => '�ĵ�ID')
#$oL->s_additem('caid',array('ids'=>array(2),)); //idsΪ�����г���ָ��id��Ŀ������Ŀ

$oL->s_additem('checked');
foreach($oL->A['coids'] as $k){
	#$oL->s_additem("ccid$k",array('ids'=>array(),));   //idsΪ�����г���ָ��id������ӷ���
	#$oL->s_additem("ccid3",array('skip'=>1)); //skip�����html�����������
	if($k == 1) continue;
	$oL->s_additem("ccid$k",array());
}
$oL->s_additem('caid',array('hidden' => 1,));
$oL->s_additem('indays');
$oL->s_additem('outdays');

//����sql��filter�ִ�����
$oL->s_deal_str();

//����������Ŀ ********************


$oL->o_additem('delete');
$oL->o_additem('check');
$oL->o_additem('uncheck');
$oL->o_additem('static');
$oL->o_additem('nstatic');
foreach($oL->A['coids'] as $k){
	if($k == 1 || $k == 35) continue;
	$oL->o_additem("ccid$k");
}
$oL->o_additem('caid');
$oL->o_additem('validperiod',array('value' => 30));
$oL->o_addpushs();//������Ŀ


if(!submitcheck('bsubmit')){
	
	//�������� ******************
	$oL->s_header();
	//$oL->s_view_array(array('keyword','orderby','checked',));//�̶���ʾ��
	//$oL->s_adv_point();//����������
	$oL->s_view_array();
	$oL->s_footer();
	

	//��ʾ�б���ͷ�� ***************
	$oL->m_header();
	
	//�����б���Ŀ
	//���飬���ȳ��ֵ��������м��룺'group' =>'item,���ݷָ���,�����ָ���',���ݷָ�������ֱ������,�����б���ķָ���������ֻʹ�õ�һ�����
	
	$oL->m_additem('selectid');
    $oL->m_additem('aid',array('type'=>'other','title'=>'ID'));
    $oL->m_additem('subject',array('len' => 40,));
	//$oL->m_additem('caid');
	
	foreach($oL->A['coids'] as $k){
		if(in_array($k,array('1'))) continue;
		$oL->m_additem("ccid$k");
	}
	$oL->m_additem('currency',array('title'=>'���ͷ�','width'=>40,));
	$oL->m_additem('createdate',array('type'=>'date',));
	$oL->m_additem('checked',array('type'=>'bool','title'=>'���',));
	$oL->m_additem('clicks',array('type'=>'input','title'=>'�����','width'=>50,'w' => 3,'view'=>'S'));
	
	$oL->m_additem('close',array('type'=>'close','title'=>'״̬','width'=>35,'ajax'=>1));//ajax�����ж��Ƿ�ͨ��ajax�ı�״̬
	$oL->m_additem('stat0',array('type'=>'url','title'=>'��','mtitle'=>'[{stat0}]','url'=>"?entry=extend&extend=commu_answers&aid={aid}",'width'=>35,));
	$oL->m_additem('jubao',array('type'=>'ucount','title'=>'�ٱ�','mtitle'=>'[{stat0}]','url'=>"?entry=extend&extend=commu_jbask&aid={aid}",'func'=>'getjhnum','cuid'=>38,'width'=>35,));
	$oL->m_additem('mname',array('title'=>'��Ա','mtitle'=>'{mname}'));
	$oL->m_additem('info',array('type'=>'url','title'=>'����','mtitle'=>'�鿴','url'=>"?entry=extend&extend=archiveinfo&aid={aid}",'width'=>30,));
	$oL->m_additem('detail',array('type'=>'url','title'=>'�༭','mtitle'=>'����','url'=>"?entry=extend&extend=qa_a&aid={aid}",'width'=>30,));
	
	//$oL->m_mcols_style("{selectid} &nbsp;{subject}<br>{shi}/{ting}/{chu}");//�����ĵ�ģʽ������ʾ��Ŀ�������ʽ,Ĭ��Ϊ��"{selectid} &nbsp;{subject}"
	
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
	
	//��ʾ����λ
	$oL->o_view_pushs();
	
	//��ʾ������
	$oL->o_view_rows();
	
	$oL->o_footer('bsubmit');

	$oL->guide_bm('','0');
	
}else{
	//Ԥ����δѡ�����ʾ
	$oL->sv_header();
	
	//�б���������������ݴ���
	$oL->sv_e_additem('clicks',array());
	$oL->sv_e_additem('vieworder',array());
	$oL->sv_e_additem('checked',array('type' => 'bool'));
	$oL->sv_e_all();
	
	//��������������ݴ���
	$oL->sv_o_all();
	
	//��������
	$oL->sv_footer();
}
?>