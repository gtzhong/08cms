<?PHP
 
/* ������ʼ������ */
$chid = 11;//�ű��̶����ĳ��ģ��
//$chid = empty($chid) ? 0 : max(0,intval($chid));//�����ⲿ��chid����Ҫ��������

$pid = empty($pid) ? 0 : max(0,intval($pid));//��ʼ���ϼ�id���п���ʹ������id��ʽ����������$hejiid�ȣ�ҪתΪʹ��pid

//$arid = 3;//ָ���ϼ���Ŀid
$arid = empty($arid) ? 0 : max(0,intval($arid));//�����ⲿ��chid����Ҫ��������
//echo "\$arid=".$arid;

$_init = array(
'chid' => $chid,//ģ��id������
'url' => "?entry=$entry$extend_str&arid=$arid",//��url���������Ҫ����chid��pid

'cols' => 3,//Ĭ��Ϊ0����Ϊ����1��Ϊ�����ĵ�ģʽ����ͼƬ�б�(�趨һ��Ԫ�ز���Ҫ������)
//'coids' => array(1),//�ֶ�����������ϵ���ڻ�Ա�����ر���Ҫָ��
//'fields' => array(),//�������װ�����ֶλ���

'where'=>"a.pid3 in (SELECT pid3 FROM {$tblprefix}".atbl(5)." WHERE aid = '$pid') AND a.aid NOT IN(SELECT inid FROM {$tblprefix}aalbums WHERE arid='2' AND pid = '$pid')",
//'select' => "a.*,b.subject as lpname,b.ccid1 ",
// 'from' => " {$tblprefix}".atbl(11)." a INNER JOIN  {$tblprefix}".atbl(4)." b ON a.pid3=b.aid ",

'isab' => 2,//*** �Ƿ�ϼ��ڹ���0Ϊ��ͨ�����б�1Ϊ���ڹ����б�2Ϊ���������б�
'pid' => $pid,//�ϼ�id
'arid' => $arid,//*** ָ���ϼ���Ŀid
);

/******************/

$oL = new cls_archives($_init);

//ͷ���ļ����������
$oL->top_head();

//������Ŀ ****************************
//���������Ŀ��s_additem($key,$cfg)
$oL->s_additem('keyword',array('fields' => array(),));//keys������Ĭ��Ϊarray('a.subject' => '����','a.mname' => '��Ա','a.aid' => '�ĵ�ID')
$oL->s_additem('caid',array());
//$oL->s_additem("ccid$k",array());
$oL->s_additem('orderby');

//����sql��filter�ִ�����
$oL->s_deal_str();

if(!submitcheck('bsubmit')){
	
	//������ʾ���� ****************************
	$oL->s_header();
	$oL->s_view_array();
	$oL->s_footer();
	
	//�����б��� **************************
	$oL->m_header();
	
	$oL->m_additem('thumb',array('type'=>'image','width'=>210,'height'=>180));
	$oL->m_additem('selectid',array('id'=>'s{aid}'));
	$oL->m_additem('subject',array('len' => 20,));
	$oL->m_additem('shi',array('type'=>'field',));
	$oL->m_additem('ting',array('type'=>'field',));
	$oL->m_additem('chu',array('type'=>'field',));	
	//$oL->m_additem('ccid1');	
	//$oL->m_additem('lpname',array('len'=>'20'));	
	$oL->m_additem('detail',array('type'=>'url','title'=>'�༭','mtitle'=>'[�༭]','color'=>'red','url'=>"?entry=extend&extend=huxingarchive&aid={aid}",'width'=>40,));
	//$oL->m_addgroup('{shi}/{ting}/{chu}','{shi}/{ting}/{chu}');//��ע����鲻��Ƕ�ף�ÿ��ֻ�ܲ���һ�η���
	//$oL->m_mcols_style("{thumb}<br>{selectid} &nbsp;{shi}/{ting}/{chu}&nbsp;{detail}<br>{subject}<br/>[{ccid1}]{lpname}");
	$oL->m_mcols_style("{thumb}<br>{selectid} &nbsp;{shi}/{ting}/{chu}&nbsp;{detail}<br>{subject}");
	
	//ȫ���б������������Ҫ���ƣ�����ʹ�����е�ϸ�ַ���
	$oL->m_view_main();
	
	//��ʾ�б���β��
	$oL->m_footer();
	
	$oL->o_end_form('bsubmit','����');
	$oL->guide_bm('','0');
	
}else{
	//ר����Լ��صĲ���
	$oL->sv_o_load();
}
?>