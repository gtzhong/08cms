<?PHP
/* ������ʼ������ */
$chid = 4;//���붨�壬�����ܴ�url�Ĵ���
$caid = empty($caid) ? 0 : $caid;
//$chid = empty($chid) ? 0 : max(0,intval($chid));//�����ⲿ��chid����Ҫ��������

$oL = new cls_archives(array(
'chid' => $chid,//ģ��id������
'url' => "?entry=$entry$extend_str",//��url���������Ҫ����chid��pid
'pre' => 'a.',//Ĭ�ϵ�����ǰ׺
'from' => $tblprefix.atbl($chid)." a INNER JOIN {$tblprefix}archives_$chid c ON c.aid=a.aid ",//sql�е�FROM����
'select' => "",//sql�е�SELECT����
'where' => "(c.leixing='0' OR c.leixing='2')", //¥������
'cols' => 0,//Ĭ��Ϊ0����Ϊ����1��Ϊ�����ĵ�ģʽ����ͼƬ�б�(�趨һ��Ԫ�ز���Ҫ������)
'orderby' => "", //a.refreshdate DESC 
//'fields' => array(),//�������װ�����ֶλ���
));

//ͷ���ļ����������
$oL->top_head();
$oL->resetCoids($oL->A['coids']); //���� ������������,������ϵ

//������Ŀ ****************************
$oL->s_additem('keyword');
$oL->s_additem('checked');
foreach($oL->A['coids'] as $k){
	if(in_array($k,array(18,41))) continue;
	$oL->s_additem("ccid$k",array());
	if($k==3) $oL->s_additem("ccid14",array());
}
$oL->s_additem('orderby');
$oL->s_additem('indays');
$oL->s_additem('outdays');

//����sql��filter�ִ�����
$oL->s_deal_str(); //echo $oL->sqlall;

//����������Ŀ ********************
$oL->o_additem('delete');
$oL->o_additem('check');
$oL->o_additem('uncheck');
$oL->o_additem('static');
$oL->o_additem('nstatic');
$oL->o_additem('readd');
//$oL->o_additem("ccid18");
//$oL->o_additem("ccid41");
$oL->o_addpushs();//������Ŀ
$oL->o_additem("leixing");

if(!submitcheck('bsubmit')){
	
	//�������� ******************
	$oL->s_header();
	//$oL->s_view_array(array('keyword','orderby','checked','ccid41',));//�̶���ʾ��
	//$oL->s_adv_point();//����������
	$oL->s_view_array();
	$oL->s_footer();
	

	//��ʾ�б���ͷ�� ***************
	$oL->m_header('С�� ���ݹ���');
	
	//�����б���Ŀ
	//���飬���ȳ��ֵ��������м��룺'group' =>'item,���ݷָ���,�����ָ���',���ݷָ�������ֱ������,�����б���ķָ���������ֻʹ�õ�һ�����
	
	$oL->m_additem('selectid');
	$oL->m_additem('subject',array('len'=>60,'addno'=>7));
	foreach($oL->A['coids'] as $k){		
		if(in_array($k,array(18,41))) continue;
		//if(in_array($k,array(7,8))) $icon = 1;
		//else                        $icon = 0;
		if(in_array($k,array(1)))   $view = '';
		else                        $view = 'H';
		$oL->m_additem("ccid$k",array('view'=>$view));
	}	
	$oL->m_additem('checked',array('type'=>'bool','title'=>'���',));	

	$oL->m_additem('azxs',array('type'=>'ucount','title'=>'��Ѷ','url'=>"?entry=extend&extend=zixuns_pid&pid={aid}",'func'=>'gethjnum','arid'=>'1','chid'=>1,'width'=>28,));
    $oL->m_additem('atps',array('type'=>'ucount','title'=>'���','url'=>"?entry=extend&extend=xiangces_pid&pid={aid}",'func'=>'gethjnum','arid'=>'3','chid'=>7,'width'=>28,));
	$oL->m_additem('ahss',array('type'=>'ucount','title'=>'����','url'=>"?entry=extend&extend=huxings_pid&pid={aid}",'func'=>'gethjnum','arid'=>'3','chid'=>11,'width'=>28,));
	$oL->m_additem('aesfys',array('type'=>'url','title'=>'����','mtitle'=>'[{lpesfsl}]','url'=>"?entry=extend&extend=usedhouseheji&pid={aid}",'width'=>28,));
	$oL->m_additem('aczfys',array('type'=>'url','title'=>'����','mtitle'=>'[{lpczsl}]','url'=>"?entry=extend&extend=chuzuheji&pid={aid}",'width'=>28,));
	$oL->m_additem('azbs',array('type'=>'ucount','title'=>'�ܱ�','url'=>"?entry=extend&extend=peitaos_pid&pid={aid}",'func'=>'gethjnum','arid'=>'1','chid'=>8,'width'=>28,));

	$oL->m_additem('refreshdate',array('type'=>'date',));	
	$oL->m_additem('updatedate',array('type'=>'date','view'=>'H',));
	//$oL->m_additem('createdate',array('type'=>'date',));
	
	$oL->m_additem('info',array('type'=>'url','title'=>'����','mtitle'=>'����','url'=>"?entry=extend&extend=archiveinfo&aid={aid}",'width'=>30,'view'=>'H',));
	$oL->m_additem('dj',array('type'=>'url','mtitle'=>'�۸�','url'=>"?entry=extend&extend=jiagearchive&aid={aid}&isnew=0",'width'=>60));
	$oL->m_additem('detail',array('type'=>'url','mtitle'=>'����','url'=>"?entry=extend&extend=xiaoquadd&aid={aid}",'width'=>60));
	$oL->m_addgroup('{detail}&nbsp;{dj}','�༭');
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
	//trbasic('<input type="checkbox" value="1" name="arcdeal[leixing]" class="checkbox">&nbsp;¥��С������','','<select style="vertical-align: middle;" name="arcleixing">'.makeoption(array('0'=>'¥����С��','1'=>'¥��','2'=>'С��')).'</select>','');

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