<?PHP
/*
** �����̨�ű��������ĵ��б����archives.php��Ϊ��������������������Ͷ����ʽʹ��
** chid�����ֶ���ʼ�������Ը��ݷ������Ŀ��ͬ����ʼ����ͬ��chid
** 
*/

/* ������ʼ������ */
$chid = 111;//���붨�壬�����ܴ�url�Ĵ���

$x_arid = empty($x_arid) ? 1 : $x_arid; 
$_abtab = $x_arid==1 ? "aalbums" : "aalbums_arcs";//ָ���ϼ���Ŀid
$x_chid = empty($x_chid) ? 4 : $x_chid; //echo "<br>$_abtab,$_arid,$_chid";
if(!in_array($x_chid,array(4,115,116))) die('��������!');

#-----------------

$oL = new cls_archives(array(
'chid' => $chid,//ģ��id������
'url' => "?entry=$entry$extend_str&x_arid=$x_arid&x_chid=$x_chid",//��url���������Ҫ����chid��pid
'pre' => 'a.',//Ĭ�ϵ�����ǰ׺
'from' => "{$tblprefix}".atbl($chid)." a INNER JOIN {$tblprefix}$_abtab b ON a.aid = b.inid INNER JOIN {$tblprefix}".atbl($x_chid)." c ON b.pid = c.aid",
'select' => "a.*,c.subject as lpname,c.ccid1,c.aid as lpaid,c.ahss",//sql�е�SELECT����
'cols' => 0,//Ĭ��Ϊ0����Ϊ����1��Ϊ�����ĵ�ģʽ����ͼƬ�б�(�趨һ��Ԫ�ز���Ҫ������)
//'fields' => array(),//�������װ�����ֶλ���
));

//ͷ���ļ����������
$oL->top_head();

//������Ŀ ****************************
//s_additem($key,$cfg)
$oL->s_additem('keyword',array('fields' => array(),));//fields������Ĭ��Ϊarray('a.subject' => '����','a.mname' => '��Ա','a.aid' => '�ĵ�ID')
#$oL->s_additem('caid',array('ids'=>array(2),)); //idsΪ�����г���ָ��id��Ŀ������Ŀ
$oL->s_additem('caid',array('hidden' => 1,));
$oL->s_additem('checked');

foreach($oL->A['coids'] as $k){
	$oL->s_additem("ccid$k",array());
}
$oL->s_additem('indays');
$oL->s_additem('outdays');

//����sql��filter�ִ�����
$oL->s_deal_str();

//����������Ŀ ********************
$oL->o_addpushs();//������Ŀ

$oL->o_additem('delete');
$oL->o_additem('check');
$oL->o_additem('uncheck');


foreach($oL->A['coids'] as $k){
	$oL->o_additem("ccid$k");
}


if(!submitcheck('bsubmit')){
	
	//�������� ******************
	$oL->s_header();
	$oL->s_view_array();
	$oL->s_footer();
	

	//��ʾ�б���ͷ�� ***************
	$oL->m_header();
	
	//�����б���Ŀ
	//���飬���ȳ��ֵ��������м��룺'group' =>'item,���ݷָ���,�����ָ���',���ݷָ�������ֱ������,�����б���ķָ���������ֻʹ�õ�һ�����
	
	$oL->m_additem('selectid');
	$oL->m_additem('subject',array('len' => 40,'url'=>'#','title'=>'¥��'));
	//$oL->m_additem('lpname',array('title'=>'����¥��','mtitle'=>'{lpname}','len' => 40,));
	$oL->m_additem('lpname',array('title'=>'����¥��','type'=>'lpname','len' => 20,'width'=>150,));
    $oL->m_additem('ahss',array('title'=>'����','mtitle'=>'[{ahss}]','len' => 40,));

	foreach($oL->A['coids'] as $k){
		$oL->m_additem("ccid$k",array('view'=>'H',));
	}
	$oL->m_additem('valid');


	$oL->m_additem('checked',array('type'=>'bool','title'=>'���',));
	$oL->m_additem('createdate',array('type'=>'date',));
	
	$oL->m_additem('info',array('type'=>'url','title'=>'����','mtitle'=>'����','url'=>"?entry=extend&extend=archiveinfo&aid={aid}",'width'=>40,));
	$oL->m_additem('detail',array('type'=>'url','title'=>'�༭','mtitle'=>'����','url'=>"?entry=extend&extend=ldarchive&aid={aid}",'width'=>40,));
	
	$oL->m_addgroup('{shi}/{ting}/{chu}','{shi}/{ting}/{chu}');//��ע����鲻��Ƕ�ף�ÿ��ֻ�ܲ���һ�η���

	//��ʾ�����У����ж���չʾ�Ļ�����Ҫ
	$oL->m_view_top();
	
	//ȫ���б������������Ҫ���ƣ�����ʹ�����е�ϸ�ַ���
	$oL->m_view_main(); // �ɴ�trclass���е�css���� array('trclass'=>'bg bg2')
	
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