<?PHP
/*
** �����̨�ű��������ĵ��б����archives.php��Ϊ��������������������Ͷ����ʽʹ��
** chid�����ֶ���ʼ�������Ը��ݷ������Ŀ��ͬ����ʼ����ͬ��chid
** 
*/
/* ������ʼ������ */
$chid = 107;//���붨�壬�����ܴ�url�Ĵ���

#-----------------

$oL = new cls_archives(array(
'chid' => $chid,//ģ��id������
'url' => "?action=$action",//��url���������Ҫ����chid��pid
'pre' => 'a.',//Ĭ�ϵ�����ǰ׺
'where' => "a.mid='{$curuser->info['mid']}' ",//sql�еĳ�ʼ��where���޶�Ϊ���ѵ��ĵ�
'from' => "{$tblprefix}".atbl($chid)." a INNER JOIN {$tblprefix}aalbums b ON a.aid = b.inid INNER JOIN {$tblprefix}".atbl(4)." c ON b.pid = c.aid ",//sql�е�FROM����
'select' => " a.* ,c.subject as lpname ",//sql�е�SELECT����
'cols' => 0,//Ĭ��Ϊ0����Ϊ����1��Ϊ�����ĵ�ģʽ����ͼƬ�б�(�趨һ��Ԫ�ز���Ҫ������)
//'coids' => array(1,19,12),//�ֶ�����������ϵ
//'fields' => array(),//�������װ�����ֶλ���
));
//ͷ���ļ����������
$oL->top_head();

//������Ŀ ****************************
//s_additem($key,$cfg)
$oL->s_additem('keyword',array('fields' => array(),));//keys������Ĭ��Ϊarray('a.subject' => '����','a.mname' => '��Ա','a.aid' => '�ĵ�ID')
$oL->s_additem('caid',array('hidden' => 1,));
$oL->s_additem('checked');
$oL->s_additem('valid');
foreach($oL->A['coids'] as $k){
	in_array($k,array(1,9,18)) && $oL->s_additem("ccid$k",array());
}
$oL->s_additem('indays');
$oL->s_additem('outdays');

//����sql��filter�ִ�����
$oL->s_deal_str();

cls_cache::Load('mconfigs');
$total_refreshes = $mconfigs['salesrefreshes'];
$refresh = $db->result_one("SELECT refreshes FROM {$tblprefix}members WHERE mid = '$memberid'");
$refresh = empty($refresh)?'0':$refresh;
$style = " style='font-weight:bold;color:#F00'";
$msgstr = "����ˢ��:<span$style>$refresh/$total_refreshes</span>��";
$re_refresh = $total_refreshes - $refresh; $re_refresh = $re_refresh<0 ? 0 : $re_refresh;

//����������Ŀ ********************
$oL->o_additem('delete');//ɾ��
$oL->o_additem('readd',array('limit'=>$re_refresh,'time'=>0,'fieldname'=>'refreshes'));
$oL->o_additem('valid',array('days' => 30));//�ϼܣ�days�����ϼܵ�������0��Ϊ������
$oL->o_additem('unvalid');//�¼�

if(!submitcheck('bsubmit')){
	backnav('tejia','manage');
	$oL->guide_bm($msgstr,'fix');
	//�������� ******************
	$oL->s_header();
	$oL->s_view_array();
	$oL->s_footer();
	

	//��ʾ�б���ͷ�� ***************
	$oL->m_header();
	
	//�����б���Ŀ������б����а������������Ҫ�����ݴ���ʱ������������Ĵ���
	//���飬���ȳ��ֵ��������м��룺'group' =>'item,���ݷָ���,�����ָ���',���ݷָ�������ֱ������,�����б���ķָ���������ֻʹ�õ�һ�����
	
	$oL->m_additem('selectid');
	$oL->m_additem('subject',array('len' => 40,));
	$oL->m_additem('lpname',array('title'=>'����¥��','mtitle'=>'{lpname}','len' => 40,));	
	$oL->m_additem("ccid1",array('view'=>'S'));
	$oL->m_additem('checked',array('type'=>'bool','title'=>'���',));
	$oL->m_additem('valid');
	foreach($oL->A['coids'] as $k){
		in_array($k,array(1,18)) && $oL->m_additem("ccid$k",array('view'=>'S',));
		in_array($k,array(9)) && $oL->m_additem("ccid9",array('url'=>'?action=zding&aid={aid}'));
	}
	$oL->m_additem('clicks',array('type'=>'bool','title'=>'�����','width'=>50,'view'=>'H','w' => 3,));
	$oL->m_additem('createdate',array('type'=>'date',));	
	$oL->m_additem('info',array('type'=>'url','title'=>'����','mtitle'=>'����','url'=>"?action=archiveinfo&aid={aid}",'width'=>40,));
	$oL->m_additem('detail',array('type'=>'url','title'=>'�༭','mtitle'=>'����','url'=>"?action=tejiaarchive&aid={aid}",'width'=>40,));
	
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