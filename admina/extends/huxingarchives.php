<?PHP

$chid = 11;

$oL = new cls_archives(array(
'chid' => $chid,//ģ��id������
'url' => "?entry=$entry$extend_str",//��url���������Ҫ����chid��pid
'pre' => 'a.',//Ĭ�ϵ�����ǰ׺
'from' => " {$tblprefix}".atbl($chid)." a LEFT JOIN {$tblprefix}".atbl(4)." b ON a.pid3 = b.aid ",//sql�е�FROM����
'select' => "a.*,b.subject as lpname,b.aid as lpaid,b.ccid1 ",//sql�е�SELECT����
'cols' => 5,//Ĭ��Ϊ0����Ϊ����1��Ϊ�����ĵ�ģʽ����ͼƬ�б�(�趨һ��Ԫ�ز���Ҫ������)
//'fields' => array(),//�������װ�����ֶλ���
));

//ͷ���ļ����������
$oL->top_head();

//������Ŀ ****************************
//s_additem($key,$cfg)
$oL->s_additem('keyword',array('fields' => array('a.aid' => '����ID','a.subject' => '���ͱ��','b.aid' => '¥��ID','b.subject' => '¥������','a.mname'=>'��Ա�˺�'),));//fields������Ĭ��Ϊarray('a.subject' => '����','a.mname' => '��Ա','a.aid' => '�ĵ�ID')

$oL->s_additem("ccid1",array());
$oL->s_additem('checked');
$oL->s_additem('indays');
$oL->s_additem('outdays');

//����sql��filter�ִ�����
$oL->s_deal_str();

//����������Ŀ ********************
$oL->o_addpushs();//������Ŀ

$oL->o_additem('delete');
$oL->o_additem('static');
$oL->o_additem('nstatic');

$oL->o_additem("ccid1");
$oL->o_additem('check');
$oL->o_additem('uncheck');
$oL->o_additem('maintype');
$oL->o_additem('unmaintype');

if(!submitcheck('bsubmit')){
	
	//�������� ******************
	$oL->s_header();	
	$oL->s_view_array();
	$oL->s_footer();
	

	//��ʾ�б���ͷ�� ***************
	$oL->m_header("����&nbsp;&nbsp;���ݹ���&nbsp;&nbsp;&nbsp;<input class='checkbox' type='checkbox' onclick='chooseall(this)' value=''>ȫѡ");
	
	//�����б���Ŀ
	//���飬���ȳ��ֵ��������м��룺'group' =>'item,���ݷָ���,�����ָ���',���ݷָ�������ֱ������,�����б���ķָ���������ֻʹ�õ�һ�����
	$oL->m_additem('thumb',array('type'=>'image','width'=>'100%','height'=>180));
	$oL->m_additem('selectid');
	$oL->m_additem('subject',array('len' => 20,));
    $oL->m_additem('ccid1');
    $oL->m_additem('lpname',array('type'=>'lpname','len' => 20));    
	$oL->m_additem('checked',array('type'=>'bool','title'=>'���',));
	$oL->m_additem('detail',array('type'=>'url','title'=>'�༭','mtitle'=>'[����]','color'=>'red','url'=>"?entry=extend&extend=huxingarchive&aid={aid}",'width'=>40,));
	$oL->m_mcols_style("{thumb}<div style=\"clear:both;\"></div>{selectid} {subject} &nbsp;���({checked}) &nbsp;{detail}<br/>[{ccid1}]{lpname}<br>");
	
	//��ʾ�����У����ж���չʾ�Ļ�����Ҫ
	//$oL->m_view_top();
	
	//ȫ���б������������Ҫ���ƣ�����ʹ�����е�ϸ�ַ���
    $oL->m_view_main(array('divclass'=>'maintype'));
    
	$oL->m_footer();
	//��ʾ�б���β��
	
	
	//��ʾ����������************
	$oL->o_header();
	
	//��ʾ��ѡ��	
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