<?PHP

//�ĵ�ģ��chid�ĳ�ʼ�����������ֶ�ȷ��ĳ��id
$chid = 121;
//$chid = empty($chid) ? 0 : max(0,intval($chid));//�����ⲿ��chid����Ҫ��������

//��ʼ���ϼ�id��ֻ����pid��������id��ʽ��������ҪתΪpid
$setthumb = empty($setthumb) ? '' : $setthumb;
$pid = empty($pid) ? 0 : max(0,intval($pid));

$_arc = new cls_arcedit; //��ҵ�ز�-�ϼ�����
$_arc->set_aid($pid,array('au'=>0,'ch'=>0)); 

$chid_fy = $_arc->archive['chid'];
$arid = in_array($chid_fy,array(2,3)) ? 38 : 36;//ָ���ϼ���Ŀid //$arid = 1;
if(!in_array($_arc->archive['chid'],array(4,115,116))); 

$_init = array(
'chid' => $chid,//ģ��id������
'url' => "?action=$action",//��url���������Ҫ����chid��pid

'cols' => 3,//Ĭ��Ϊ0����Ϊ����1��Ϊ�����ĵ�ģʽ����ͼƬ�б�(�趨һ��Ԫ�ز���Ҫ������)
//'coids' => array(1),//�ֶ�����������ϵ���ڻ�Ա�����ر���Ҫָ��
//'fields' => array(),//�������װ�����ֶλ���
'select' => "a.* ",//sql�е�SELECT����
'from' => " {$tblprefix}".atbl($chid)." a ",//sql�е�FROM����
'where' => " a.chid='$chid' AND a.pid$arid='$pid' ",
'isab' => 1,//*** �Ƿ�ϼ��ڹ���0Ϊ��ͨ�����б�1Ϊ���ڹ����б�2Ϊ���������б�
'pid' => $pid,//�ϼ�id
'arid' => $arid,//*** ָ���ϼ���Ŀid
'orderby' => "a.aid DESC",//�ϼ���ָ������,�ĵ���ϼ���¼��Ϊ"a.inorderxx DESC"��xxΪ�ϼ���Ŀid
//'pids_allow' => 'self',//*** pid����ķ�Χ���ڻ�Ա���ı����������ǰ��Ա�Ƿ���иúϼ��Ĺ���Ȩ��
'pids_allow' => '-1',//����
);


#-----------------

$oL = new cls_archives($_init);
//ͷ���ļ����������
$oL->top_head();

//������Ŀ ****************************
//s_additem($key,$cfg)
$oL->s_additem('keyword',array('fields' => array(),));//keys������Ĭ��Ϊarray('a.subject' => '����','a.mname' => '��Ա','a.aid' => '�ĵ�ID')
$oL->s_additem('caid',array('hidden' => 1,));
$oL->s_additem('orderby');
//$oL->s_additem('shi',array('type'=>'field',));
//$oL->s_additem('ting',array('type'=>'field',));
$oL->s_additem('valid');
//$oL->s_additem("ccid$k",array());
$oL->s_additem('indays');
//$oL->s_additem('outdays');

//����sql��filter�ִ�����
$oL->s_deal_str();
//echo $oL->sqlall;

if($setthumb && $pid){
	$thumbarc = new cls_arcedit;
	$thumbarc->set_aid($setthumb);
	$upthumb = $thumbarc->archive['thumb'];
	$thumbarc->set_aid($pid);
	$thumbarc->updatefield('thumb',$upthumb);
	$thumbarc->updatedb();
	unset($thumbarc);
	$url = $oL->A['url']."&page={$oL->A['page']}".$oL->filterstr;
	$oL->message('����ͼ���óɹ���',$url);
}

//����������Ŀ ********************
$oL->o_additem('delete');//ɾ��
//$oL->o_additem('readd');//ˢ��
//$oL->o_additem('valid',array('days' => 30));//�ϼܣ�days�����ϼܵ�������0��Ϊ������
//$oL->o_additem('unvalid');//�¼�
//$oL->o_additem('incheck');//������Ч
//$oL->o_additem('unincheck');//������Ч
// $oL->o_additem('inclear');//�˳��ϼ�
//$oL->o_additem('caid');
//$oL->o_additem("ccid$k");

if(!submitcheck('bsubmit')){
	
	//�������� ******************
	$oL->s_header();
	$oL->s_view_array();
	$oL->s_footer();
	
	//�����б��� **************************
	$oL->m_header(" &nbsp;<a style=\"color:#C00\" href=\"?action=xiangceadd_fy&pid=$pid\" onclick=\"return floatwin('open_arcexit',this)\">>>������</a>",1);
	$oL->m_additem('lx',array('type'=>'field',));
	$oL->m_additem('subject',array('len' => 20));
	$oL->m_additem('thumb',array('type'=>'image','width'=>'100%','height'=>180));
	$oL->m_additem('detail',array('type'=>'url','title'=>'�༭','mtitle'=>'[�༭]','url'=>"?action=xiangceadd_fy&aid={aid}",'width'=>40));
// 	$oL->m_additem('editself',array('title'=>'�༭','mtitle'=>'[�༭]','url'=>"?action=xiangceadd_fy&aid={aid}",'width'=>40));
	$oL->m_additem('littlethumb',array('type'=>'url','title'=>'��Ϊ����ͼ','mtitle'=>'[��Ϊ����ͼ]','url'=>"?action=$action&pid=$pid&setthumb={aid}",'width'=>40));
	$oL->m_mcols_style("{thumb}<div style=\"clear:both;\"></div>{subject}({lx}) &nbsp;{detail}<br>{littlethumb}");//�����ĵ�ģʽ������ʾ��Ŀ�������ʽ,Ĭ��Ϊ��"{selectid} 
	
	//ȫ���б������������Ҫ���ƣ�����ʹ�����е�ϸ�ַ���
	$oL->m_view_main();
	
	//��ʾ�б���β��
	$oL->m_footer();
	
	//��ʾ����������*******************************
	$oL->o_header();
	
	//��ʾ��ѡ��
	$oL->o_view_bools('�ϼ����� ',array('inclear','incheck','unincheck',));
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
//	$oL->sv_e_additem('clicks',array());
//	$oL->sv_e_all();
	
	//��������������ݴ���
	$oL->sv_o_all();
	
	//��������
	$oL->sv_footer();
}
?>