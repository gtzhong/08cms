<?PHP

/* ������ʼ������ */
$chid = 11;//���붨�壬�����ܴ�url�Ĵ���
//$chid = empty($chid) ? 0 : max(0,intval($chid));//�����ⲿ��chid����Ҫ��������

#-----------------

$oL = new cls_archives(array(
'chid' => $chid,//ģ��id������
'url' => "?action=$action",//��url���������Ҫ����chid��pid
'pre' => 'a.',//Ĭ�ϵ�����ǰ׺
'where' => "a.mid='{$curuser->info['mid']}'",//sql�еĳ�ʼ��where���޶�Ϊ���ѵ��ĵ�
'from' => "",//sql�е�FROM����
'select' => "",//sql�е�SELECT����
'cols' => 3,//Ĭ��Ϊ0����Ϊ����1��Ϊ�����ĵ�ģʽ����ͼƬ�б�(�趨һ��Ԫ�ز���Ҫ������)
'coids' => array(1,),//�ֶ�����������ϵ
//'fields' => array(),//�������װ�����ֶλ���
));
//ͷ���ļ����������
$oL->top_head();

//������Ŀ ****************************
//s_additem($key,$cfg)
$oL->s_additem('keyword',array('fields' => array(),));//keys������Ĭ��Ϊarray('a.subject' => '����','a.mname' => '��Ա','a.aid' => '�ĵ�ID')
$oL->s_additem('caid',array('ids' => array(502,504)));
$oL->s_additem('orderby');
//$oL->s_additem('shi',array('type'=>'field',));
//$oL->s_additem('valid');
foreach($oL->A['coids'] as $k){
	//$oL->s_additem("ccid$k",array());
}
//$oL->s_additem('checked');
$oL->s_additem('indays');
$oL->s_additem('outdays');

//����sql��filter�ִ�����
$oL->s_deal_str();

//����������Ŀ ********************
$oL->o_additem('delete');//ɾ��
$oL->o_additem('static',array('title'=>'���ɾ�̬'));
//$oL->o_additem('valid',array('days' => 30));//�ϼܣ�days�����ϼܵ�������0��Ϊ������
//$oL->o_additem('unvalid');//�¼�
//$oL->o_additem('caid');

foreach($oL->A['coids'] as $k){
	//$oL->o_additem("ccid$k");
}
// $oL->o_additem("ccid19",array('limit'=>6,'title'=>'�Ƽ�λ')); //��ϵ�޶����

if(!submitcheck('bsubmit')){
	
	//�������� ******************
	backnav('loupanbar','huxing');
	$oL->s_header();
	//$oL->s_view_array(array('keyword'));//�̶���ʾ��
	//$oL->s_adv_point();//����������
	$oL->s_view_array();
	$oL->s_footer();
	

	//��ʾ�б���ͷ�� ***************
	$oL->m_header();
	
	//�����б���Ŀ
	//���飬���ȳ��ֵ��������м��룺'group' =>'item,���ݷָ���,�����ָ���',���ݷָ�������ֱ������,�����б���ķָ���������ֻʹ�õ�һ�����
	
	$oL->m_additem('selectid');
	$oL->m_additem('subject',array('len' => 20));
	$oL->m_additem('thumb',array('type'=>'image','width'=>210,'height'=>180));
	
	$oL->m_additem('detail',array('type'=>'url','title'=>'�༭','mtitle'=>'[�༭]','url'=>"?action=huxingadd&aid={aid}",'width'=>40,'view'=>'H'));
	$oL->m_mcols_style("{thumb}<br>{selectid}{subject} &nbsp;{detail}");//�����ĵ�ģʽ������ʾ��Ŀ�������ʽ,Ĭ��Ϊ��"{selectid} &nbsp;{subject}"
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
	$oL->guide_bm("���б�ֻ��ʾ/�����Լ���ӵ�¥�̻��ͣ��������ڣ�����¥�� - ����ĳ��¥���� ��ӡ�",'fix');
	
}else{
	//Ԥ����δѡ�����ʾ
	$oL->sv_header();
	
	//�б���������������ݴ���
//	$oL->sv_e_additem('clicks',array());
//	$oL->sv_e_all();
	
	//��������������ݴ���
	$oL->sv_o_all();
	
	//��������
	$oL->sv_footer();
}
?>