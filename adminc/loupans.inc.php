<?php

$chid = $lpchid = empty($lpchid) ? 4 : $lpchid;
$chid = in_array($chid,array(4,115,116)) ? $chid : cls_messag::show('��������');
$mchid = $curuser->info['mchid'];
$lpfields = array(4=>'loupan',115=>'xiezilou',116=>'shaopu');
$lpcoids = array(4=>array(1,6,12,18,41),115=>array(1,46,47),116=>array(1,48,49));
$lpfield = $lpfields[$chid];
//*
$sql_ids = "SELECT $lpfield FROM {$tblprefix}members_$mchid WHERE mid='$memberid'"; 
$loupanids = $db->result_one($sql_ids); if($loupanids) $loupanids = substr($loupanids,1); 
if(empty($loupanids)) $loupanids = 0;

#-----------------

$oL = new cls_archives(array(
'chid' => $chid,//ģ��id������
'url' => "?action=$action&lpchid=$chid",//��url���������Ҫ����chid��pid
'pre' => 'a.',//Ĭ�ϵ�����ǰ׺
'where' => "a.aid IN($loupanids) ",//sql�еĳ�ʼ��where���޶�Ϊ���ѵ��ĵ�
'from' => $tblprefix.atbl($chid)." a INNER JOIN {$tblprefix}archives_$chid c ON c.aid=a.aid ",//sql�е�FROM����
'select' => "",//sql�е�SELECT����
'cols' => 0,//Ĭ��Ϊ0����Ϊ����1��Ϊ�����ĵ�ģʽ����ͼƬ�б�(�趨һ��Ԫ�ز���Ҫ������)
'coids' => $lpcoids[$lpchid],//�ֶ�����������ϵ 2,3,14,
//'fields' => array(),//�������װ�����ֶλ���
));
//ͷ���ļ����������
$oL->top_head();

//������Ŀ ****************************
$oL->s_additem('keyword',array('fields' => array(),));//keys������Ĭ��Ϊarray('a.subject' => '����','a.mname' => '��Ա','a.aid' => '�ĵ�ID')
$oL->s_additem('indays');
$oL->s_additem('outdays');



cls_cache::Load('mconfigs');
$total_refreshes = $mconfigs['salesrefreshes'];
$refresh = $db->result_one("SELECT refreshes FROM {$tblprefix}members WHERE mid = '$memberid'");
$refresh = empty($refresh)?'0':$refresh;
$style = " style='font-weight:bold;color:#F00'";
$msgstr = "����ˢ��:<span$style>$refresh/$total_refreshes</span>��";
$re_refresh = $total_refreshes - $refresh; $re_refresh = $re_refresh<0 ? 0 : $re_refresh;




//����sql��filter�ִ�����
$oL->s_deal_str();

$oL->o_additem('readd',array('limit'=>$re_refresh,'time'=>0,'fieldname'=>'refreshes'));
$oL->o_additem('static',array('title'=>'���ɾ�̬')); //��̬,��������Ч

if(!submitcheck('bsubmit')){
	
	//�������� ******************
	backnav($chid==4 ? 'loupanbar' : 'loupanbus','loupan');
	$oL->guide_bm($msgstr,'fix');
	$oL->s_header();
	$oL->s_view_array();
	$oL->s_footer();

	//��ʾ�б���ͷ�� ***************

	$oL->m_header();
	
	//�����б���Ŀ������б����а������������Ҫ�����ݴ���ʱ������������Ĵ���
	//���飬���ȳ��ֵ��������м��룺'group' =>'item,���ݷָ���,�����ָ���',���ݷָ�������ֱ������,�����б���ķָ���������ֻʹ�õ�һ�����
	
	$oL->m_additem('selectid');
	$oL->m_additem('subject',array('len' => 40,));

	foreach($oL->A['coids'] as $k){
		$view = in_array($k,array(18,41,46,47,48)) ? '' : 'H';
		$oL->m_additem("ccid$k",array('view'=>$view,));
	}
	$oL->m_additem('valid');

	$oL->m_additem('azxs',array('type'=>'ucount','title'=>'��Ѷ','url'=>"?action=zixuns_pid&pid={aid}",'func'=>'gethjnum','arid'=>($chid==4 ? '1' : 35),'chid'=>1,'width'=>28,));
	$oL->m_additem('atps',array('type'=>'ucount','title'=>'���','url'=>"?action=xiangces_pid&pid={aid}",'func'=>'gethjnum','arid'=>($chid==4 ? '3' : 36),'chid'=>7,'width'=>28,));
	
    if($chid==4){
        $oL->m_additem('ahss',array('type'=>'ucount','title'=>'����','url'=>"?action=huxings_pid&pid={aid}",'func'=>'gethjnum','arid'=>'3','chid'=>11,'width'=>28,));
	   $oL->m_additem('ahds',array('type'=>'url','title'=>'�','mtitle'=>'[{ahds}]','url'=>"?action=loupanhd&pid={aid}",'width'=>30,));
    }
	
    $oL->m_additem('ayss',array('type'=>'url','title'=>'����','mtitle'=>'[{ayss}]','url'=>"?action=louyx&aid={aid}&chid=$chid",'width'=>30,));
	//$oL->m_additem('adps',array('type'=>'url','title'=>'����','mtitle'=>'����','url'=>"?action=loupandp&pid={aid}",'width'=>30,));
	$oL->m_additem('liuyan',array('type'=>'ucount','title'=>'����','url'=>"?action=loupandp&pid={aid}",'func'=>'getjhnum','cuid'=>'1','chid'=>4,'width'=>28,));	
	$oL->m_additem('pinfen',array('type'=>'url','title'=>'����','mtitle'=>'�鿴','url'=>"?action=loupan_pinfen&aid={aid}&chid=$chid",'width'=>30,));
	$oL->m_additem('weixin',array('type'=>'url','title'=>'΢��','mtitle'=>'����', 'url'=>"?action=weixin_property&aid={aid}&cache_id=property",'func'=>'getjhnum','cuid'=>'44','width'=>28,));

	$oL->m_additem('refreshdate',array('type'=>'date',));
	$oL->m_additem('info',array('type'=>'url','title'=>'����','mtitle'=>'����','url'=>"?action=archiveinfo&aid={aid}",'width'=>30,'view'=>'H'));
	$oL->m_additem('dj',array('type'=>'url','mtitle'=>'�۸�','url'=>"?action=jiagearchive&aid={aid}&isnew=1",'width'=>60));
	$oL->m_additem('detail',array('type'=>'url','title'=>'�༭','mtitle'=>'����','url'=>"?action=loupane&aid={aid}",'width'=>60,));
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

