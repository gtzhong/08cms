<?PHP
/**
 * ֱ���ϼ��ڵ��ĵ��б����
 *
 * �ϼ��ڵ��ĵ��б����archives_pid.php������ʾ��������������Ͷ����ʽʹ��
 * ע�����ֺϼ���ϵ��1���ĵ����¼pid 2���ϼ���ϵ���ر�������select��from������ 
 * �ϼ��ڵĹ����ٷ����Ƿ������Ŀ����Ȩ�� 
 * 
 *
 * @author icms <icms@foxmail.com>
 * @copyright (C) 2008 - 2014 08CMS, Inc. All rights reserved.
 *
 */ 

/* ������ʼ������ */
//$chid = 4;//�ű��̶����ĳ��ģ��

$pid = empty($pid) ? 0 : max(0,intval($pid));//��ʼ���ϼ�id���п���ʹ������id��ʽ����������$hejiid�ȣ�ҪתΪʹ��pid
$arid = 34;
$abrel = cls_cache::Read('abrel',$arid);
//var_dump($abrel);
$chid = isset($chid) ? max(0,intval($chid)) : 0;
in_array($chid,array(1,4,12)) || cls_message::show("����ȷָ��ģ�ͣ�");//��Ҫ����ģ�ͷ�Χ
switch($chid){
	case 1:
	$detailExtend = 'zixunadd';
	break;
	case 4:
	$detailExtend = 'loupanadd';
	break;
	case 12:
	$detailExtend = 'shipinadd';
	break;
}

$_init = array(
'chid' => $chid,//ģ��id������
'url' => "?entry=$entry$extend_str",//��url���������Ҫ����chid��pid

'cols' => 0,//Ĭ��Ϊ0����Ϊ����1��Ϊ�����ĵ�ģʽ����ͼƬ�б�(�趨һ��Ԫ�ز���Ҫ������)

'isab' => 1,//*** �Ƿ�ϼ��ڹ���0Ϊ��ͨ�����б�1Ϊ���ڹ����б�2Ϊ���������б�
'pid' => $pid,//�ϼ�id
'arid' => $arid,//*** ָ���ϼ���Ŀid
'orderby' => "b.inorder ASC,a.aid DESC",//�ϼ���ָ������,�ĵ���ϼ���¼��Ϊ"a.inorderxx DESC"��xxΪ�ϼ���Ŀid

);

/******************/

$oL = new cls_archives($_init);

//ͷ���ļ����������
$oL->top_head();

//������Ŀ ****************************
//���������Ŀ��s_additem($key,$cfg)
$oL->s_additem('keyword',array('fields' => array(),));//fields������Ĭ��Ϊarray('a.subject' => '����','a.mname' => '��Ա','a.aid' => '�ĵ�ID')
$oL->s_additem('caid',array());
$oL->s_additem('checked');
$oL->s_additem('inchecked',array('field' => 'b.incheck'));//ָ���ϼ��м�������������ֶΣ����ĵ����¼��Ϊa.incheckxx��xxΪ�ϼ���Ŀid
$oL->s_additem('orderby');

//����sql��filter�ִ�����
$oL->s_deal_str();

//����������Ŀ ********************
$oL->o_additem('inclear');


$oL->o_additem('check');
$oL->o_additem('uncheck');

if(!submitcheck('bsubmit')){
	
	//������ʾ���� ****************************
	$oL->s_header();
	$oL->s_view_array();
	$oL->s_footer();

	//�����б��� **************************
	//$oL->m_header(" &nbsp;<a style=\"color:#C00\" href=\"?entry=extend&extend=loupans_load&pid=$pid\" onclick=\"return floatwin('open_arcexit',this)\">>>��������</a> &nbsp;",1);
    $oL->m_header(" &nbsp; <a style=\"color:#C00\" href=\"?entry=extend&extend=live_load&pid=$pid&chid=$chid\" onclick=\"return floatwin('open_arcexit',this)\">>>��������</a>",1);
	
	//�����б���Ŀ
	//���飬���ȳ��ֵ��������м��룺'group' =>'item,���ݷָ���,�����ָ���',���ݷָ�������ֱ������,�����б���ķָ���������ֻʹ�õ�һ�����
	$oL->m_additem('selectid');
	$oL->m_additem('subject',array('len' => 40,));	
	$oL->m_additem('caid');
	$oL->m_additem('inorder',array('type' => 'input','title'=>'����','w' => 3,));
	$oL->m_additem('checked',array('type'=>'bool','title'=>'���',));
	
	$oL->m_additem('createdate',array('type'=>'date',));
	$oL->m_additem('info',array('type'=>'url','title'=>'����','mtitle'=>'����','url'=>"?entry=extend&extend=archiveinfo&aid={aid}",'width'=>40,));
	$oL->m_additem('detail',array('type'=>'url','title'=>'�༭','mtitle'=>'����','url'=>"?entry=extend&extend=$detailExtend&aid={aid}",'width'=>40,));
	
	//��ʾ�����У����ж���չʾ�Ļ�����Ҫ
	$oL->m_view_top();
	
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
	$oL->guide_bm('&nbsp;&nbsp;&nbsp;1.�ϼ�����>>������ò����ǰ������ڱ�ҳ���б���ɾ��������ͨ��"����"�������ؽ�����<br/>&nbsp;<br/>','1');
	
}else{
	//Ԥ����δѡ�����ʾ
	$oL->sv_header();
	
	//�б���������������ݴ���
	$oL->sv_e_additem('inorder',array());
	$oL->sv_e_all();
	
	//��������������ݴ���
	$oL->sv_o_all();
	
	//��������
	$oL->sv_footer();
}
?>