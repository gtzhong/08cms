<?PHP
/*
 * �����̨�ű��������ĵ��б����archives.php��Ϊ��������������������Ͷ����ʽʹ��
 * chid�����ֶ���ʼ�������Ը��ݷ������Ŀ��ͬ����ʼ����ͬ��chid
 * @author icms <icms@foxmail.com>
 * @copyright (C) 2008 - 2014 08CMS, Inc. All rights reserved.
*/

/* ������ʼ������ */
$chid = 114;//���붨�壬�����ܴ�url�Ĵ���
$chid = empty($chid) ? 0 : max(0,intval($chid));//�����ⲿ��chid����Ҫ��������

#-----------------

$oL = new cls_archives(array(
'chid' => $chid,//ģ��id������
'url' => "?entry=$entry$extend_str",//��url���������Ҫ����chid��pid
'pre' => 'a.',//Ĭ�ϵ�����ǰ׺
'from' => " {$tblprefix}".atbl($chid)." a INNER JOIN {$tblprefix}archives_$chid c ON a.aid=c.aid ",//sql�е�FROM���֣�����ͨ������JOIN������
'select' => " a.* ",//sql�е�SELECT����
'cols' => 0,//Ĭ��Ϊ0����Ϊ����1��Ϊ�����ĵ�ģʽ����ͼƬ�б�(�趨һ��Ԫ�ز���Ҫ������)
//'fields' => array(),//�������װ�����ֶλ���
));

//ͷ���ļ����������
$oL->top_head();

//������Ŀ ****************************
//s_additem($key,$cfg)
$oL->s_additem('keyword',array('fields' => array('a.subject' => '����','a.aid' => '�ĵ�ID','a.keywords'=>'�ؼ���','a.mname'=>'��Ա�˺�'),));//fields������Ĭ��Ϊarray('a.subject' => '����','a.mname' => '��Ա','a.aid' => '�ĵ�ID')
$oL->s_additem('checked');
$oL->s_additem('caid',array('hidden' => 1,));
$oL->s_additem('indays');
$oL->s_additem('outdays');
$oL->s_additem('caid');
$oL->s_additem('ccid1');

//����sql��filter�ִ�����
$oL->s_deal_str();

//����������Ŀ ********************
$oL->o_addpushs();//������Ŀ

$oL->o_additem('delete');

$oL->o_additem('check');
$oL->o_additem('uncheck');
$oL->o_additem('readd');
$oL->o_additem('caid');
foreach($oL->A['coids'] as $k){
	$oL->o_additem("ccid$k");
}
$oL->o_additem('static');
$oL->o_additem('nstatic');

if(!submitcheck('bsubmit')){
	
	//�������� ******************
	$oL->s_header();
	$oL->s_view_array();
	$oL->s_footer();
	

	//��ʾ�б���ͷ�� ***************
	$oL->m_header();
	
	//�����б���Ŀ
	//���飬���ȳ��ֵ��������м��룺'group' =>'item,���ݷָ���,�����ָ���',���ݷָ�������ֱ������,�����б���ķָ���������ֻʹ�õ�һ�����
	
	$oL->m_additem('selectid');
	$oL->m_additem('subject',array('len' => 40,));
	$oL->m_additem('ccid1');
	$oL->m_additem('checked',array('type'=>'bool','title'=>'���',));
	$oL->m_additem('clicks',array('type'=>'input','view'=>'H','title'=>'�����','width'=>50,'w' => 3,));	
	//$oL->m_additem('ucount',array('type'=>'ucount','title'=>'����','url'=>"?entry=extend&extend=commu_live&aid={aid}",'func'=>'getjhnum','cuid'=>'1','width'=>35,));	

	$oL->m_additem('glzx',array('type'=>'ucount','title'=>'������Ѷ','url'=>"?entry=extend&extend=live_pid&pid={aid}&chid=1",'func'=>'gethjnum','chid'=>1,'arid'=>'34','width'=>60,));
	$oL->m_additem('gllp',array('type'=>'ucount','title'=>'����¥��','url'=>"?entry=extend&extend=live_pid&pid={aid}&chid=4",'func'=>'gethjnum','chid'=>4,'arid'=>'34','width'=>60,));
	$oL->m_additem('glsp',array('type'=>'ucount','title'=>'������Ƶ','url'=>"?entry=extend&extend=live_pid&pid={aid}&chid=12",'func'=>'gethjnum','chid'=>12,'arid'=>'34','width'=>60,));

	$oL->m_additem('ucount1',array('type'=>'ucount','title'=>'����','url'=>"?entry=extend&extend=liveinfo_list&aid={aid}",'func'=>'getjhnum','cuid'=>'101','width'=>65,));
	//������ht�����ں�̨��ӱ���ʱ������ȥ����֤��
	$oL->m_additem('add',array('type'=>'url','title'=>'ֱ����Ϣ','mtitle'=>'���','url'=>"?entry=extend&extend=liveinfo_add&aid={aid}",'width'=>65,));
	$oL->m_addgroup('{ucount1}&nbsp;{add}','ֱ����Ϣ');
	$oL->m_additem('info',array('type'=>'url','title'=>'����','mtitle'=>'�鿴','url'=>"?entry=extend&extend=archiveinfo&aid={aid}",'width'=>40,));
	$oL->m_additem('detail',array('type'=>'url','title'=>'�༭','mtitle'=>'����','url'=>"?entry=extend&extend=liveadd&aid={aid}&caid=$caid",'width'=>40,));

	
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