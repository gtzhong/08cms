<?PHP

$isload = empty($isload) ? 1 : max(1,intval($isload));

$pid = empty($pid) ? 0 : max(0,intval($pid));//��ʼ���ϼ�id���п���ʹ������id��ʽ����������$hejiid�ȣ�ҪתΪʹ��pid
$_arc = new cls_arcedit; //��ҵ�ز�-�ϼ�����
$_arc->set_aid($pid,array('au'=>0,'ch'=>0));
switch ($_arc->archive['chid'])
{
    case 4:
        $chid = 3;
        break;
    case 115:
        $chid = 117;
        break;
    case 116:
        $chid = 118;
        break;
    default:
        echo "��ָ����ȷ��ģ�Ͳ���";
}
$arid = $_arc->archive['chid']==4 ? 3 : 36;//ָ���ϼ���Ŀid //$arid = 1;
if(!in_array($_arc->archive['chid'],array(4,115,116)));
$wherestr = $isload == 1 ? '' : "pid$arid = '0'";

# ���CK���ID�����ƣ���������ýű�ʱ��̳���ȥ
cleanCookies(array('fyid', 'lpmc'), true);
$_init = array(
'chid' => $chid,//ģ��id������
'url' => "?entry=$entry$extend_str&isload=$isload",//����url���������Ҫ����chid��pid
'cols' => 0,//Ĭ��Ϊ0����Ϊ����1��Ϊ�����ĵ�ģʽ����ͼƬ�б�(�趨һ��Ԫ�ز���Ҫ������)
//'coids' => array(1),//�ֶ�����������ϵ���ڻ�Ա�����ر���Ҫָ��
//'fields' => array(),//���������װ�����ֶλ���
'isab' => $isload,//*** �Ƿ�ϼ��ڹ�����0Ϊ��ͨ�����б���1Ϊ���ڹ����б���2Ϊ���������б�
'pid' => $pid,//�ϼ�id
'arid' => $arid,//*** ָ���ϼ���Ŀid
'where' => $wherestr,
//'orderby' => "b.inorder DESC",//�ϼ���ָ������,�ĵ����ϼ���¼��Ϊ"a.inorderxx DESC"��xxΪ�ϼ���Ŀid

);

/******************/

$oL = new cls_archives($_init);
//ͷ���ļ����������
$oL->top_head();

if($isload==1){

	//������Ŀ ****************************
	//����������Ŀ��s_additem($key,$cfg)
	$oL->s_additem('keyword',array('fields' => array(),));//fields������Ĭ��Ϊarray('a.subject' => '����','a.mname' => '��Ա','a.aid' => '�ĵ�ID')
	//$oL->s_additem('caid',array());
	//$oL->s_additem("ccid$k",array());
	$oL->s_additem('checked');
	$oL->s_additem('valid');
	//$oL->s_additem('inchecked',array('field' => 'b.incheck'));//ָ���ϼ��м�������������ֶΣ����ĵ�����¼��Ϊa.incheckxx��xxΪ�ϼ���Ŀid
	$oL->s_additem('orderby');
	
	//����sql��filter�ִ�����
	$oL->s_deal_str();

	//����������Ŀ ********************
	$oL->o_additem('inclear');
	//$oL->o_additem('incheck');
	//$oL->o_additem('unincheck');
	
	$oL->o_additem('delete');
	$oL->o_additem('check');
	$oL->o_additem('uncheck');
	//$oL->o_additem('readd');
	$oL->o_additem('static');
	$oL->o_additem('nstatic');
	//$oL->o_additem("ccid$k");
	
	if(!submitcheck('bsubmit')){
		
		//������ʾ���� ****************************
		$oL->s_header();
		$oL->s_view_array();
		$oL->s_footer();
	
		//�����б��� **************************
		$_tmp_aid = $db->result_one("SELECT aid FROM {$tblprefix}".atbl($chid)." WHERE pid$arid='0'"); //echo $_tmp_aid;
		$_tmp_load = empty($_tmp_aid) ? "" : " &nbsp; <a style=\"color:#C00\" href=\"?entry=extend&extend=$extend&pid=$pid&isload=2\" onclick=\"return floatwin('open_arcexit',this)\">>>��������</a>";
		$oL->m_header("$_tmp_load",1);
		
		//�����б���Ŀ
		//���飬���ȳ��ֵ��������м��룺'group' =>'item,���ݷָ���,�����ָ���',���ݷָ�������ֱ������,�����б���ķָ���������ֻʹ�õ�һ�����
		$oL->m_additem('selectid');
		$oL->m_additem('subject',array('len' => 40,));
		//$oL->m_additem('caid');
		$oL->m_additem('ccid1');
		$oL->m_additem('valid');
		$oL->m_additem('checked',array('type'=>'bool','title'=>'���',));
		$oL->m_additem('inorder',array('type' => 'input','title'=>'����','w' => 3,));
		//$oL->m_additem('incheck',array('type'=>'checkbox','atitle'=>'��Ч','side' => 'L','width'=>50,));
		//$oL->m_additem('incheck',array('type'=>'bool','title'=>'��Ч',));
		$oL->m_additem('createdate',array('type'=>'date',));
		$oL->m_additem('enddate',array('type'=>'date',));
		$oL->m_additem('info',array('type'=>'url','title'=>'����','mtitle'=>'����','url'=>"?entry=extend&extend=archiveinfo&aid={aid}",'width'=>40,));
		$oL->m_additem('detail',array('type'=>'url','title'=>'�༭','mtitle'=>'����','url'=>"?entry=extend&extend=usedhousearchive&aid={aid}",'width'=>40,));
	
		//��ʾ�����У����ж���չʾ�Ļ�����Ҫ
		$oL->m_view_top();
		
		//ȫ���б��������������Ҫ���ƣ�����ʹ�����е�ϸ�ַ���
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
		$oL->guide_bm('','0');
		
	}else{
		//Ԥ������δѡ�����ʾ
		$oL->sv_header();
		//�б���������������ݴ���
		//$oL->sv_e_additem('clicks',array());
		$oL->sv_e_additem('inorder',array());
		$oL->sv_e_all();
		//��������������ݴ���
		$oL->sv_o_all();
		//��������
		$oL->sv_footer();
	}
}else{ // loader

	//������Ŀ ****************************
	//����������Ŀ��s_additem($key,$cfg)
	$oL->s_additem('keyword',array('fields' => array(),));//keys������Ĭ��Ϊarray('a.subject' => '����','a.mname' => '��Ա','a.aid' => '�ĵ�ID')
	//$oL->s_additem('caid',array());
	$oL->s_additem("ccid1",array());
	$oL->s_additem('checked');
	$oL->s_additem('valid');
	$oL->s_additem('orderby');
	
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
		$oL->m_additem('selectid');
		$oL->m_additem('subject',array('len' => 40,));
		$oL->m_additem('ccid1');
		$oL->m_additem('clicks',array('title'=>'���',));
		//$oL->m_additem("ccid$k",array('view'=>'H',));
		
		$oL->m_additem('valid');
		$oL->m_additem('checked',array('type'=>'bool','title'=>'���',));
		$oL->m_additem('createdate',array('type'=>'date',));
		$oL->m_additem('enddate',array('type'=>'date',));

		$oL->m_additem('info',array('type'=>'url','title'=>'����','mtitle'=>'����','url'=>"?entry=extend&extend=archiveinfo&aid={aid}",'width'=>40,));

		//��ʾ�����У����ж���չʾ�Ļ�����Ҫ
		$oL->m_view_top();
		
		//ȫ���б��������������Ҫ���ƣ�����ʹ�����е�ϸ�ַ���
		$oL->m_view_main();
		
		//��ʾ�б���β��
		$oL->m_footer();
		
		$oL->o_end_form('bsubmit','����');
		$oL->guide_bm('','0');
		
	}else{
		//ר����Լ��صĲ���
		$oL->sv_o_load();
	}
	
}
?>