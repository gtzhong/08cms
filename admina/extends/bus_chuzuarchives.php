<?PHP
$//chid = 2;//���붨�壬�����ܴ�url�Ĵ���
$caid = in_array($caid,array(614,618)) ? $caid : 614;
$chid = $caid==618 ? 120 : 119; //echo "$caid,$chid";
#-----------------

$oL = new cls_archives(array(
'chid' => $chid,//ģ��id������
'url' => "?entry=$entry$extend_str",//��url���������Ҫ����chid��pid
'pre' => 'a.',//Ĭ�ϵ�����ǰ׺
'from' => " {$tblprefix}".atbl($chid)." a INNER JOIN {$tblprefix}archives_$chid c ON a.aid=c.aid ",//sql�е�FROM���֣�����ͨ������JOIN������
'select' => "",//sql�е�SELECT����
'cols' => 0,//Ĭ��Ϊ0����Ϊ����1��Ϊ�����ĵ�ģʽ����ͼƬ�б�(�趨һ��Ԫ�ز���Ҫ������)
//'fields' => array(),//�������װ�����ֶλ���
));


# ���CK���ID�����ƣ���������ýű�ʱ��̳���ȥ
cleanCookies(array('fyid', 'lpmc'), true);

//ͷ���ļ����������
$oL->top_head();
$oL->resetCoids($oL->A['coids']); //���� ������������,������ϵ

//������Ŀ ****************************
//s_additem($key,$cfg)
$oL->s_additem('keyword',array('fields' => array('a.subject' => '����','a.keywords' => '�ؼ���','a.mname' => '��Ա�˺�','a.aid' => '�ĵ�ID','c.lxdh'=>'��ϵ�绰'),));
$oL->s_additem('caid',array('hidden' => 1,));
$oL->s_additem('checked');
$oL->s_additem('valid');
foreach($oL->A['coids'] as $k){
	if(in_array($k,array(19))) continue;
	$oL->s_additem("ccid$k",array());
	if($k==3) $oL->s_additem("ccid14",array());
}
$oL->s_additem('mchid',array('pre'=>'a.'));
$oL->s_additem('orderby');
$oL->s_additem('indays');
$oL->s_additem('outdays');

//����sql��filter�ִ�����
$oL->s_deal_str();

//����������Ŀ ********************
$oL->o_addpushs();//������Ŀ

$oL->o_additem('delete');
$oL->o_additem('check');
$oL->o_additem('uncheck');
$oL->o_additem('valid');
$oL->o_additem('unvalid');
$oL->o_additem('readd');
$oL->o_additem('static');
$oL->o_additem('nstatic');

$oL->o_additem("ccid9");
//$oL->o_additem("ccid19",array('guide'=>'ֻ�Ծ����˷����ķ�Դ��Ч��'));


if(!submitcheck('bsubmit')){
	
	//�������� ******************
	$oL->s_header();
    $oL->s_view_array();
	$oL->s_footer_ex("?entry=extend&extend=export_excel&chid=$chid&filename=chuzu");
	if(empty($fcdisabled2)) RelCcjs($chid,1,2,1);
	if(empty($fcdisabled3)) RelCcjs($chid,3,14,2);

	//��ʾ�б���ͷ�� ***************
	$oL->m_header();
	
	//�����б���Ŀ
	//���飬���ȳ��ֵ��������м��룺'group' =>'item,���ݷָ���,�����ָ���',���ݷָ�������ֱ������,�����б���ķָ���������ֻʹ�õ�һ�����
	
	$oL->m_additem('selectid');
    $oL->m_additem('aid',array('type'=>'other','title'=>'ID'));
    $oL->m_additem('subject',array('len' => 40,));
	//$oL->m_additem('caid');
	$oL->m_additem('clicks',array('type'=>'input','title'=>'�����','width'=>50,'view'=>'S','w' => 3,));
	foreach($oL->A['coids'] as $k){
		if(in_array($k,array(19))) continue;
		$a = in_array($k,array(1,9)) ? array() : array('view'=>'H',);
		$oL->m_additem("ccid$k",$a);
		$k == 9 && $oL->m_additem('ccid9date',array('title'=>'�ö�����ʱ��','type'=>'date','view'=>'H','width'=>100));
	}
    $oL->m_additem('yixiang',array('type'=>'ucount','title'=>'����','url'=>"?entry=extend&extend=commu_yixiang&aid={aid}&caid=$caid",'func'=>'getjhnum','cuid'=>'46','width'=>28,));
	$oL->m_additem('checked',array('type'=>'bool','title'=>'���',));
	$oL->m_additem('createdate',array('type'=>'date','view'=>'H',));
	$oL->m_additem('refreshdate',array('type'=>'date',));	
	$oL->m_additem('enddate',array('type'=>'date',));
	$oL->m_additem('info',array('type'=>'url','view'=>'H','title'=>'����','mtitle'=>'����','url'=>"?entry=extend&extend=archiveinfo&aid={aid}",'width'=>40,));
	$oL->m_additem('xingming',array('title'=>'����','width'=>40,));
	$oL->m_additem('mchid',array('title'=>'��Ա����','width'=>80,)); 
	$oL->m_additem('detail',array('type'=>'url','title'=>'�༭','mtitle'=>'����','url'=>"?entry=extend&extend=bus_chuzuarchive&aid={aid}&caid={caid}",'width'=>40,));

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
	$oL->o_view_bools('', array(), 8);
	
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