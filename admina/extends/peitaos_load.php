<?PHP
/*
** �ϼ������ĵ����б����archives_load.php������ʾ��������������Ͷ����ʽʹ��
** ע�����ֺϼ���ϵ��1���ĵ����¼pid 2���ϼ���ϵ���ر�������select��from������ 
** �ϼ��ڵĹ����ٷ����Ƿ������Ŀ����Ȩ��
*/ 
/* ������ʼ������ */
$chid = 8;//�ű��̶����ĳ��ģ��
//$chid = empty($chid) ? 0 : max(0,intval($chid));//�����ⲿ��chid����Ҫ��������

$pid = empty($pid) ? 0 : max(0,intval($pid));//��ʼ���ϼ�id���п���ʹ������id��ʽ����������$hejiid�ȣ�ҪתΪʹ��pid

$_arc = new cls_arcedit; //��ҵ�ز�-�ϼ�����
$_arc->set_aid($pid,array('au'=>0,'ch'=>0)); 

$pchid = $_arc->archive['chid']; 
if(!in_array($pchid,array(4,115,116))) cls_message::show("Error:$pid");
$arid = $pchid==4 ? 1 : 35;//ָ���ϼ���Ŀid
$artab = $pchid==4 ? 'aalbums' : 'aalbums_arcs';//ָ���ϼ���Ŀid

//$circum_km��̨�����������õ��ܱ߷�Χ
$maps = '';
$circum_km = empty($circum_km)? 3 : $circum_km;
$circum_km = $circum_km*2;
$_lpmap = $db->result_one("SELECT dt FROM {$tblprefix}".atbl($pchid)." where aid = '$pid'");

if(!empty($_lpmap)){
	$_dt = explode(',',$_lpmap );
	$maps = cls_dbother::MapSql($_dt[0], $_dt[1],$circum_km, 1, 'dt'); 
}
$wherestr = empty($maps)? '': " AND ".$maps;

$_init = array(
'chid' => $chid,//ģ��id������
'url' => "?entry=$entry$extend_str",//��url���������Ҫ����chid��pid

'cols' => 0,//Ĭ��Ϊ0����Ϊ����1��Ϊ�����ĵ�ģʽ����ͼƬ�б�(�趨һ��Ԫ�ز���Ҫ������)
//'coids' => array(1),//�ֶ�����������ϵ���ڻ�Ա�����ر���Ҫָ��
//'fields' => array(),//�������װ�����ֶλ���
'where'=>"a.aid NOT IN(SELECT DISTINCT inid FROM {$tblprefix}$artab WHERE pid='$pid') $wherestr",
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
$oL->s_additem('keyword',array('fields' => array('a.subject' => '����','a.aid' => '�ĵ�ID'),));//keys������Ĭ��Ϊarray('a.subject' => '����','a.mname' => '��Ա','a.aid' => '�ĵ�ID')
$oL->s_additem('caid',array());

//����sql��filter�ִ�����
$oL->s_deal_str(); //echo $oL->sqlall;

if(!submitcheck('bsubmit')){
	
	//������ʾ���� ****************************
	$oL->s_header();
	$oL->s_view_array();
	$oL->s_footer();
	
	//�����б��� **************************
	$oL->m_header();
	
	//�����б���Ŀ
	//���飬���ȳ��ֵ��������м��룺'group' =>'item,���ݷָ���,�����ָ���',���ݷָ�������ֱ������,�����б���ķָ���������ֻʹ�õ�һ�����
	$oL->m_additem('selectid');
	$oL->m_additem('subject',array('len' => 40,));
	$oL->m_additem('caid');
	$oL->m_additem('clicks',array('title'=>'���',));
	$oL->m_additem('createdate',array('type'=>'date',));
	
	//��ʾ�����У����ж���չʾ�Ļ�����Ҫ
	$oL->m_view_top();
	
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