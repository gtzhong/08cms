<?PHP
/* ������ʼ������ */
$chid = 4;//���붨�壬�����ܴ�url�Ĵ���
$caid_str = empty($caid)?'':"&caid=".max(1,intval($caid));
//$chid = empty($chid) ? 0 : max(0,intval($chid));//�����ⲿ��chid����Ҫ��������
$oL = new cls_archives(array(
'chid' => $chid,//ģ��id������
'url' => "?entry=$entry$extend_str$caid_str",//��url���������Ҫ����chid��pid
'pre' => 'a.',//Ĭ�ϵ�����ǰ׺
'from' => $tblprefix.atbl($chid)." a INNER JOIN {$tblprefix}archives_$chid c ON c.aid=a.aid ",//sql�е�FROM����
'select' => "",//sql�е�SELECT����
'where' => "(c.leixing='0' OR c.leixing='1')", //¥������
'cols' => 0,//Ĭ��Ϊ0����Ϊ����1��Ϊ�����ĵ�ģʽ����ͼƬ�б�(�趨һ��Ԫ�ز���Ҫ������)
//'orderby' => "a.refreshdate DESC ",
'orderby' => "a.ccid41 DESC,a.vieworder,a.aid DESC", //,a.aid DESC
//'fields' => array(),//�������װ�����ֶλ���
));
//ͷ���ļ����������
$oL->top_head();
$oL->resetCoids($oL->A['coids']); //���� ������������,������ϵ

//������Ŀ ****************************
$oL->s_additem('keyword',array('fields' => array('a.subject' => '¥������','a.keywords'=>'�ؼ���','a.mname' => '��Ա�˺�','a.aid' => '�ĵ�ID')));
$oL->s_additem('checked');
foreach($oL->A['coids'] as $k){
	$oL->s_additem("ccid$k",array());
	if($k==3) $oL->s_additem("ccid14",array());
}
$oL->s_additem('caid',array('hidden' => 1,));
$oL->s_additem('orderby_e');
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
$oL->o_additem('readd');
$oL->o_additem("ccid18");
$oL->o_additem("ccid41");
$oL->o_additem("ccid1");
$oL->o_addpushs();//������Ŀ
$oL->o_additem("leixing");

if(!submitcheck('bsubmit')){
	
	//�������� ******************
	$oL->s_header();
	//$oL->s_view_array(array('keyword','orderby','checked','ccid41'));//�̶���ʾ��
	//$oL->s_adv_point();//����������
	$oL->s_view_array();
	$oL->s_footer();
	if(empty($fcdisabled2)) RelCcjs($chid,1,2,1);
	if(empty($fcdisabled3)) RelCcjs($chid,3,14,2);

	//��ʾ�б���ͷ�� ***************
	$oL->m_header();
	
	//�����б���Ŀ
	//���飬���ȳ��ֵ��������м��룺'group' =>'item,���ݷָ���,�����ָ���',���ݷָ�������ֱ������,�����б���ķָ���������ֻʹ�õ�һ�����
	
	$oL->m_additem('selectid');
    $oL->m_additem('aid',array('type'=>'other','title'=>'ID'));
	$oL->m_additem('subject',array('len' => 60,));
	foreach($oL->A['coids'] as $k){		
		//if(in_array($k,array(7,8))) $icon = 1;
		//else                        $icon = 0;
		if(in_array($k,array(1)))   $view = '';
		else                        $view = 'H';
		$oL->m_additem("ccid$k",array('view'=>$view));
	}	
	$oL->m_additem('checked',array('type'=>'bool','title'=>'���','view'=>'H',));	
	$oL->m_additem('azxs',array('type'=>'ucount','title'=>'��Ѷ','url'=>"?entry=extend&extend=zixuns_pid&pid={aid}",'func'=>'gethjnum','arid'=>'1','chid'=>1,'width'=>28,));
	$oL->m_additem('atps',array('type'=>'ucount','title'=>'���','url'=>"?entry=extend&extend=xiangces_pid&pid={aid}",'func'=>'gethjnum','arid'=>'3','chid'=>7,'width'=>28,));
	$oL->m_additem('ahds',array('type'=>'ucount','title'=>'�Ź�','url'=>"?entry=extend&extend=huodongs_pid&pid={aid}",'func'=>'gethjnum','arid'=>'3','chid'=>5,'width'=>28,));
	$oL->m_additem('ahss',array('type'=>'ucount','title'=>'����','url'=>"?entry=extend&extend=huxings_pid&pid={aid}",'func'=>'gethjnum','arid'=>'3','chid'=>11,'width'=>28,));	
	$oL->m_additem('ayss',array('type'=>'url','title'=>'����','mtitle'=>'[{ayss}]','url'=>"?entry=extend&extend=yixiangs&aid={aid}",'width'=>28,));	
	$oL->m_additem('liuyan',array('type'=>'ucount','title'=>'����','url'=>"?entry=extend&extend=comments&aid={aid}&chid=4",'func'=>'getjhnum','cuid'=>'1','chid'=>4,'width'=>28,));	
	$oL->m_additem('awds',array('type'=>'url','title'=>'�ʴ�','mtitle'=>'[{awds}]','url'=>"?entry=extend&extend=wendas_pid&pid={aid}",'width'=>28,));
	$oL->m_additem('atjs',array('type'=>'ucount','title'=>'�ؼ۷�','url'=>"?entry=extend&extend=tejias_pid&pid={aid}",'func'=>'gethjnum','arid'=>'1','chid'=>107,'width'=>40,));
	$oL->m_additem('azbs',array('type'=>'ucount','title'=>'�ܱ�','url'=>"?entry=extend&extend=peitaos_pid&pid={aid}",'func'=>'gethjnum','arid'=>'1','chid'=>8,'width'=>28,));		
	$oL->m_additem('ayxs',array('type'=>'ucount','title'=>'ӡ��','url'=>"?entry=extend&extend=yinxiangheji&aid={aid}",'func'=>'getjhnum','cuid'=>'44','width'=>28,));			
	$oL->m_additem('weixin',array('type'=>'url','title'=>'΢��','mtitle'=>'����', 'url'=>"?entry=weixin_property&aid={aid}&cache_id=property",'width'=>28,));	
	$oL->m_additem('adps',array('type'=>'url','title'=>'����','mtitle'=>'�鿴','url'=>"?entry=extend&extend=lp_pingfen&aid={aid}",'width'=>28,));
	$oL->m_additem('stpic',array('type'=>'url','title'=>'ɳ��','mtitle'=>'ɳ��','url'=>"?entry=extend&extend=sandtable&pid={aid}",'width'=>28,));
	$oL->m_additem('refreshdate',array('type'=>'date','view'=>'H',));
	$oL->m_additem('updatedate',array('type'=>'date','view'=>'H',));
	$oL->m_additem('clicks',array('title'=>'�����','type'=>'input','width'=>50,'w' => 3,));
	$oL->m_additem('vieworder',array('type' => 'input','title'=>'����','w' => 3,));
	
    $oL->m_additem('info',array('type'=>'url','title'=>'����','mtitle'=>'����','url'=>"?entry=extend&extend=archiveinfo&aid={aid}",'width'=>30,'view'=>'H',));
	$oL->m_additem('dj',array('type'=>'url','mtitle'=>'�۸�','url'=>"?entry=extend&extend=jiagearchive&aid={aid}&isnew=1",'width'=>60));
	$oL->m_additem('detail',array('type'=>'url','mtitle'=>'����','url'=>"?entry=extend&extend=loupanadd&aid={aid}",'width'=>60));
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
	$oL->sv_e_all();
	
	//��������������ݴ���
	$oL->sv_o_all();

	//��������
	$oL->sv_footer();
}
?>