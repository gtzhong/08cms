<?PHP
/*
** ���������б����
** 
** 
*/
/* ������ʼ������ */
$paid = cls_PushArea::InitID(@$paid);//�����ⲿ��paid

$pidval = empty($$pidid) ? 0 : $$pidid;
$pidstr = "";
if(!empty($pid3)){
	$pid3 = intval($pid3);
	$pidstr = empty($pid3) ? "" : "p.pid3=".intval($pid3)."";
	$pchid = 4;
	$pfield = 'pid3';
}elseif(!empty($pid36)){
	$pid36 = intval($pid36);
	$pidstr = empty($pid36) ? "" : "p.pid36=".intval($pid36)."";
	$pchid = '115,116';
	$pfield = 'pid36';
}else{
	$pchid = 4;	
	$pfield = 'pid3';
}

#-----------------

$oL = new cls_pushs(array(
'paid' => $paid,//����λid������
'url' => "?entry=$entry$extend_str",//��url���������Ҫ����chid��pid
'pre' => '',//Ĭ�ϵ�������Ϣ��ǰ׺
'from' => "",//sql�е�FROM���� {$tblprefix}$paid p LEFT JOIN {$tblprefix}$atab a ON p.$pfield = a.aid 
'select' => "",//sql�е�SELECT���� p.*,a.subject as lpsubject
'rowpp' => 30,
'where' => "$pidstr",
));

//ͷ���ļ����������
$oL->top_head();

//������Ŀ ****************************
//s_additem($key,$cfg)
$oL->s_additem('keyword',array('fields' => array('p.subject' => '����','p.mname' => '�Ƽ����û���','p.pushid' => '��ϢID','p.fromid' => '��ԴID','p.pid3' => '¥��ID','a.subject' => '¥������'),));//keys������Ĭ��Ϊarray('a.subject' => '����','a.mname' => '��Ա','a.aid' => '�ĵ�ID')
$oL->s_additem('classid1',array('type'=>'field',));
$oL->s_additem('classid2',array('type'=>'field',));
$oL->s_additem('checked');
$oL->s_additem('valid');
$oL->s_additem('loadtype');
$oL->s_additem('orderby');
$oL->s_additem('indays');
$oL->s_additem('outdays');

//����sqlall��filter�ִ�����
if(!empty($oL->oS->wheres['keyword']) && strstr($oL->oS->wheres['keyword'],'a.subject')){
	$oL->oS->wheres['keyword'] = cls_pushsearchs::get_subAids($pchid,$oL->oS->wheres['keyword'],$pfield);
}
$oL->s_deal_str(); //echo $oL->oS->wheres['keyword'];

//����������Ŀ ********************
$oL->o_additem('check');
$oL->o_additem('uncheck');
$oL->o_additem('refresh');//ͬ����Դ
$oL->o_additem('delete');
$oL->o_additem("classid1");
$oL->o_additem("classid2");

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
	$oL->m_additem('selectid');
	$oL->m_additem('subject',array('len' => 40,));
	$oL->m_additem('pid36',array('type'=>'lpname','len' => 40,));
	$oL->m_additem("classid1",array('type'=>'field',));
	$oL->m_additem("classid2",array('type'=>'field',));
	$oL->m_additem('valid');
	$oL->m_additem('fixedorder');
	$oL->m_additem('vieworder');
	$oL->m_additem('checked',array('type' => 'bool','title'=>'���',));
	$oL->m_additem('createdate',array('type'=>'date','title'=>'��������','view' => 'H',));
	$oL->m_additem('startdate',array('type'=>'date','title'=>'��Ч����',));
	$oL->m_additem('enddate',array('type'=>'date','title'=>'��������',));
	$oL->m_additem('loadtype');
	$oL->m_additem('share');
	$oL->m_additem('detail');
//	$oL->m_additem('detail',array('type'=>'url','title'=>'�༭','mtitle'=>'����','url'=>"?entry=extend&extend=push&paid={$paid}&pushid={pushid}",'width'=>40,));
	
	//��ʾ�����У����ж���չʾ�Ļ�����Ҫ
	$oL->m_view_top();
	
	//ȫ���б������������Ҫ���ƣ�����ʹ�����е�ϸ�ַ���
	$oL->m_view_main();
	
	//��ʾ�б���β��
	$oL->m_footer();
	
	//��ʾ����������************
	$oL->o_header();
	
	//��ʾ��ѡ��
	$oL->o_view_bools();
	
	//��ʾ������
	$oL->o_view_rows();
	
	$oL->o_footer('bsubmit');
	$oL->guide_bm('pushs_list','0');
	
}else{
	//Ԥ����δѡ�����ʾ
	$oL->sv_header();
	
	//�б���������������ݴ���
	$oL->sv_e_additem('fixedorder',array());
	$oL->sv_e_additem('vieworder',array());
	$oL->sv_e_all();
	
	//��������������ݴ���
	$oL->sv_o_all();
	
	//��������
	$oL->sv_footer();
}
?>