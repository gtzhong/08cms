<?PHP
/*
** �����̨�ű��������ĵ��б����archives.php��Ϊ��������������������Ͷ����ʽʹ��
** chid�����ֶ���ʼ�������Ը��ݷ������Ŀ��ͬ����ʼ����ͬ��chid
** 
*/

/* ������ʼ������ */
$chid = 107;//���붨�壬�����ܴ�url�Ĵ���
//$chid = empty($chid) ? 0 : max(0,intval($chid));//�����ⲿ��chid����Ҫ��������

#-----------------

$oL = new cls_archives(array(
'chid' => $chid,//ģ��id������
'url' => "?entry=$entry$extend_str",//��url���������Ҫ����chid��pid
'pre' => 'a.',//Ĭ�ϵ�����ǰ׺
'from' => "{$tblprefix}archives30 a INNER JOIN {$tblprefix}aalbums b ON a.aid = b.inid INNER JOIN {$tblprefix}archives15 c ON b.pid = c.aid",//sql�е�FROM���֣�����ͨ������JOIN������
'select' => "a.*,c.subject as lpname,c.ccid1,c.aid as lpaid",//sql�е�SELECT����
'cols' => 0,//Ĭ��Ϊ0����Ϊ����1��Ϊ�����ĵ�ģʽ����ͼƬ�б�(�趨һ��Ԫ�ز���Ҫ������)
//'fields' => array(),//�������װ�����ֶλ���
));

//ͷ���ļ����������
$oL->top_head();

//������Ŀ ****************************
//s_additem($key,$cfg)
$oL->s_additem('keyword',array('fields' => array('a.subject' => '����','a.aid' => '�ĵ�ID','c.subject'=>'¥������','a.mname'=>'��Ա�˺�'),));//fields������Ĭ��Ϊarray('a.subject' => '����','a.mname' => '��Ա','a.aid' => '�ĵ�ID')
$oL->s_additem('checked');
$oL->s_additem('caid',array('hidden' => 1,));
$oL->s_additem('indays');
$oL->s_additem('outdays');

//����sql��filter�ִ�����
$oL->s_deal_str();

//����������Ŀ ********************
$oL->o_addpushs();//������Ŀ

$oL->o_additem('delete');
$oL->o_additem('check');
$oL->o_additem('uncheck');
$oL->o_additem('static');
$oL->o_additem('nstatic');
$oL->o_additem("ccid9");


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
	$oL->m_additem('subject',array('len' => 40,));
	$oL->m_additem('lpname',array('title'=>'����¥��','mtitle'=>'{lpname}','len' => 40,));	
	$oL->m_additem("ccid1",array('view'=>'S'));
	$oL->m_additem('checked',array('type'=>'bool','title'=>'���',));
	foreach($oL->A['coids'] as $k){
		if(in_array($k,array(9,18))) $oL->m_additem("ccid$k",array('view'=>'S',));
	}
	$oL->m_additem('clicks',array('type'=>'input','title'=>'�����','width'=>50,'view'=>'H','w' => 3,));
	$oL->m_additem('createdate',array('type'=>'date',));
	$oL->m_additem('info',array('type'=>'url','title'=>'����','mtitle'=>'����','url'=>"?entry=extend&extend=archiveinfo&aid={aid}",'width'=>40,));
	$oL->m_additem('detail',array('type'=>'url','title'=>'�༭','mtitle'=>'����','url'=>"?entry=extend&extend=tejiaarchive&aid={aid}",'width'=>40,));
	
	
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