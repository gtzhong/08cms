<?PHP
/*
** �ϼ��ڵ��ĵ��б����archives_pid.php������ʾ��������������Ͷ����ʽʹ��
** ע�����ֺϼ���ϵ��1���ĵ����¼pid 2���ϼ���ϵ���ر�������select��from������ 
** �ϼ��ڵĹ����ٷ����Ƿ������Ŀ����Ȩ��
*/ 
/* ������ʼ������ */
$chid = 11;//�ű��̶����ĳ��ģ��
//$chid = empty($chid) ? 0 : max(0,intval($chid));//�����ⲿ��chid����Ҫ��������

$pid = empty($pid) ? 0 : max(0,intval($pid));//��ʼ���ϼ�id���п���ʹ������id��ʽ����������$hejiid�ȣ�ҪתΪʹ��pid
$arid = 2;

$_init = array(
'chid' => $chid,//ģ��id������
'url' => "?entry=$entry$extend_str",//��url���������Ҫ����chid��pid

'cols' => 3,//Ĭ��Ϊ0����Ϊ����1��Ϊ�����ĵ�ģʽ����ͼƬ�б�(�趨һ��Ԫ�ز���Ҫ������)
//'coids' => array(1),//�ֶ�����������ϵ���ڻ�Ա�����ر���Ҫָ��
//'fields' => array(),//�������װ�����ֶλ���

'select' => "a.*,d.subject as lpname,d.ccid1 ",//sql�е�SELECT����
'from' => " {$tblprefix}aalbums b INNER JOIN {$tblprefix}".atbl($chid)." a ON a.aid=b.inid INNER JOIN {$tblprefix}".atbl(5)." c ON b.pid = c.aid INNER JOIN {$tblprefix}".atbl(4)." d ON c.pid3 = d.aid",//sql�е�FROM����
'where' => " a.chid='$chid' AND b.pid='$pid' ",
'isab' => 1,//*** �Ƿ�ϼ��ڹ���0Ϊ��ͨ�����б�1Ϊ���ڹ����б�2Ϊ���������б�
'pid' => $pid,//�ϼ�id
'arid' => $arid,//*** ָ���ϼ���Ŀid
'orderby' => "a.aid DESC",//�ϼ���ָ������,�ĵ���ϼ���¼��Ϊ"a.inorderxx DESC"��xxΪ�ϼ���Ŀid

);


/******************/

$oL = new cls_archives($_init);

//ͷ���ļ����������
$oL->top_head();

//������Ŀ ****************************
//���������Ŀ��s_additem($key,$cfg)
$oL->s_additem('keyword',array('fields' => array(),));//fields������Ĭ��Ϊarray('a.subject' => '����','a.mname' => '��Ա','a.aid' => '�ĵ�ID')


//����sql��filter�ִ�����
$oL->s_deal_str();

//����������Ŀ ********************
$oL->o_additem('inclear');

if(!submitcheck('bsubmit')){
	
	//������ʾ���� ****************************
	$oL->s_header();
	$oL->s_view_array();
	$oL->s_footer();

	//�����б��� **************************
	// �˴���Ҫ - ��ӣ�ֻ����ء��������ġ�¥���µ� ���� ���ɡ�
	$oL->m_header("  &nbsp;<a style=\"color:#C00\" href=\"?entry=extend&extend=huxing_load&pid=$pid&arid=2\" onclick=\"return floatwin('open_arcexit',this)\">>>���ػ���</a> ",1);	
	// &nbsp;<a style=\"color:#C00\" href=\"?entry=extend&extend=huxingarchive&pid=$pid&arid=$arid\" onclick=\"return floatwin('open_arcexit',this)\">>>��ӻ���</a>
	
	$oL->m_additem('thumb',array('type'=>'image','width'=>'100%','height'=>180));
	$oL->m_additem('selectid',array('id'=>'s{aid}'));
	$oL->m_additem('subject',array('len' => 20,));
	$oL->m_additem('shi',array('type'=>'field',));
	$oL->m_additem('ting',array('type'=>'field',));
	$oL->m_additem('chu',array('type'=>'field',));	
	$oL->m_additem('ccid1');	
	$oL->m_additem('lpname',array('len'=>'20'));
	$oL->m_additem('detail',array('type'=>'url','title'=>'�༭','mtitle'=>'[�༭]','color'=>'red','url'=>"?entry=extend&extend=huxingarchive&aid={aid}",'width'=>40,));
	//$oL->m_addgroup('{shi}/{ting}/{chu}','{shi}/{ting}/{chu}');//��ע����鲻��Ƕ�ף�ÿ��ֻ�ܲ���һ�η���
	//$oL->m_mcols_style("{thumb}<br>{selectid} &nbsp;{shi}/{ting}/{chu}&nbsp;{detail}<br>{subject}<br/>[{ccid1}]{lpname}");
	$oL->m_mcols_style("{thumb}<div style=\"clear:both;\"></div>{selectid} &nbsp;{shi}/{ting}/{chu}&nbsp;{detail}<br>{subject}");
	
	//��ʾ�����У����ж���չʾ�Ļ�����Ҫ
	//$oL->m_view_top();
	
	//ȫ���б������������Ҫ���ƣ�����ʹ�����е�ϸ�ַ���
	$oL->m_view_main();
	
	//��ʾ�б���β��
	$oL->m_footer();
	
	//��ʾ����������*******************************
	$oL->o_header();
	
	//��ʾ��ѡ��
	$oL->o_view_bools('�ϼ����� ',array('inclear','incheck','unincheck',));
	$oL->o_view_bools();
	
	//��ʾ������
	$oL->o_view_rows();
	
	$oL->o_footer('bsubmit');
	$oL->guide_bm('&nbsp;&nbsp;&nbsp;1.�ϼ�����>>������ò����ǰ������ڱ�ҳ���б���ɾ��������ͨ��"���ػ���"�������ؽ�����<br/>&nbsp;<br/>','1');
	
}else{
	//Ԥ����δѡ�����ʾ
	$oL->sv_header();

	//��������������ݴ���
	$oL->sv_o_all();
	
	//��������
	$oL->sv_footer();
}
?>
