<?PHP
/**
 * ֱ���ϼ��ڵ��ĵ�����
 *
 * @author icms <icms@foxmail.com>
 * @copyright (C) 2008 - 2014 08CMS, Inc. All rights reserved.
 *
 */ 
$chid = empty($chid) ? 1 : max(1,intval($chid));//�����ⲿ��chid����Ҫ��������
#$chidarr = array('1' => '��Ѷ' ,'4' => '¥��',  '12' => '��Ƶ');
$chidarr = array(1,4,12);
if(!in_array($chid,$chidarr)) die("$chid");

$pid = empty($pid) ? 0 : max(0,intval($pid));//��ʼ���ϼ�id���п���ʹ������id��ʽ����������$hejiid�ȣ�ҪתΪʹ��pid

$arid = 34;//ָ���ϼ���Ŀid

$channel = cls_cache::Read('channels');
$model = $channel[$chid]['cname'];

$_init = array(
'chid' => $chid,//ģ��id������
'url' => "?entry=$entry$extend_str",//��url���������Ҫ����chid��pid
'cols' => 0,//Ĭ��Ϊ0����Ϊ����1��Ϊ�����ĵ�ģʽ����ͼƬ�б�(�趨һ��Ԫ�ز���Ҫ������)
//'coids' => array(1),//�ֶ�����������ϵ���ڻ�Ա�����ر���Ҫָ��
//'fields' => array(),//�������װ�����ֶλ���
//'select' => 'caid',
'isab' => 2,//*** �Ƿ�ϼ��ڹ���0Ϊ��ͨ�����б�1Ϊ���ڹ����б�2Ϊ���������б�
'pid' => $pid,//�ϼ�id
'arid' => $arid,//*** ָ���ϼ���Ŀid
);

$oL = new cls_archives($_init);

//ͷ���ļ����������
$oL->top_head();

//������Ŀ ****************************
$oL->s_additem('keyword',array('fields' => array('a.subject' => '����','a.aid' => '�ĵ�ID','a.subject' => '¥������'),));
$oL->s_additem('caid',array());
$chid == 2 && $oL->s_additem("ccid1 ",array());

//����sql��filter�ִ�����
$oL->s_deal_str();

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
	$oL->m_additem('chid',array('title'=>'ģ��',));
	$oL->m_additem('caid');
	$oL->m_additem('clicks',array('title'=>'���',));
	$oL->m_additem('createdate',array('type'=>'date',));	
	//$oL->m_mcols_style();//�����ĵ�ģʽ������ʾ��Ŀ�������ʽ,Ĭ��Ϊ��"{selectid} &nbsp;{subject}"
	
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