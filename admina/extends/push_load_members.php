<?PHP
/*
** �����̨�ű������ڻ�Ա�б����
** ���ݲ���ģ�͵Ļ�Ա�б�����ָ��ģ���벻��ģ�͵Ĵ�����
** ����Ա�Ĺ����ų����������������ϵ������
*/
/* ������ʼ������ */
$paid = cls_PushArea::InitID(@$paid);//��ʼ���Ƽ�λID
if(!($pusharea = cls_PushArea::Config($paid))) exit('��ָ����ȷ������λ');
if($pusharea['sourcetype'] != 'members') exit('����λ��ԴӦΪ��Ա����'); 
$grouptypes = cls_cache::Read('grouptypes');

$_init = array(
'mode' => 'pushload',
'paid' => $paid,//����λid������
'url' => "?entry=$entry$extend_str",//��url���������Ҫ����mchid
'from' => "",//sql��FROM֮����������֣�����ͨ������JOIN����������members m LEFT JOIN members_sub s ON (s.mid=m.mid) LEFT JOIN members_$mchid c ON (c.mid=m.mid)  
);
/******************/

$oL = new cls_members($_init);

//ͷ���ļ����������
$oL->top_head();

//������Ŀ ****************************
$oL->s_additem('keyword',array('fields' => array(),));//fields������Ĭ��Ϊarray('m.mname' => '��Ա�ʺ�','m.mid' => '��ԱID')
foreach($grouptypes as $k => $v){//����ʱ����ָ��id��ʾ��Ա������������������ϵ
	$oL->s_additem("ugid$k");//��Ա��
}
$oL->s_additem('orderby');//����
$oL->s_additem('indays');//��������ע��

//����sql��filter�ִ����� ****************
$oL->s_deal_str();

if(!submitcheck('bsubmit')){
	
	//�������� ******************
	$oL->s_header();
	$oL->s_view_array();
	$oL->s_footer();
	
	//�б��� ***************
	$oL->m_header();
	//�����б���Ŀ
	$oL->m_additem('selectid');
	$oL->m_additem('subject',array('len' => 40,'field' => 'mname'));//�����˻�Ա��Ǽ��ռ�url�ı��⣬nourl��ʾ����Ҫ�ռ�url
	foreach($grouptypes as $k => $v){//���ƽű�ʱ����ָ��id������ʾ��ʽ��λ��
		$oL->m_additem("ugid$k");//��Ա�飬��view��Ϊ����Ĭ����ʾ
	}
	$oL->m_additem('regdate',array('type'=>'date',));//ע��ʱ��
	$oL->m_additem('lastvisit',array('type'=>'date','view'=>'H',));//�ϴε�¼ʱ��
	$oL->m_additem('info',array('type'=>'url','title'=>'����','mtitle'=>'����','url'=>"?entry=extend&extend=memberinfo&mid={mid}",'width'=>40,'view'=>'H',));
	
	//��ʾ������
	$oL->m_view_top();
	
	//ȫ���б������������Ҫ���ƣ�����ʹ�����е�ϸ�ַ���
	$oL->m_view_main();
	
	//��ʾ�б���β��
	$oL->m_footer();
	
	$oL->o_end_form('bsubmit','����');
	$oL->guide_bm('','0');
	
	
}else{
	//���ͼ��صĲ���
	$oL->sv_o_pushload();
	
}
