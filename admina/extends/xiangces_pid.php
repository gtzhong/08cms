<?PHP

$chid = 7;//�ű��̶����ĳ��ģ��
$setthumb = empty($setthumb) ? '' : $setthumb;

$pid = empty($pid) ? 0 : max(0,intval($pid));//��ʼ���ϼ�id���п���ʹ������id��ʽ����������$hejiid�ȣ�ҪתΪʹ��pid

$_arc = new cls_arcedit; //��ҵ�ز�-�ϼ�����
$_arc->set_aid($pid,array('au'=>0,'ch'=>0)); 

$arid = $_arc->archive['chid']==4 ? 3 : 36;//ָ���ϼ���Ŀid //$arid = 1;
if(!in_array($_arc->archive['chid'],array(4,115,116))); 
$paixu="inorder".$arid; //��������

$_init = array(
'chid' => $chid,//ģ��id������
'url' => "?entry=$entry$extend_str",//��url���������Ҫ����chid��pid

'cols' => 3,//Ĭ��Ϊ0����Ϊ����1��Ϊ�����ĵ�ģʽ����ͼƬ�б�(�趨һ��Ԫ�ز���Ҫ������)
//'coids' => array(1),//�ֶ�����������ϵ���ڻ�Ա�����ر���Ҫָ��
//'fields' => array(),//�������װ�����ֶλ���
'select' => "a.*,b.subject as lpname,b.ccid1 ",//sql�е�SELECT����
'from' => " {$tblprefix}".atbl($chid)." a INNER JOIN {$tblprefix}".atbl($_arc->archive['chid'])." b ON a.pid$arid = b.aid",//sql�е�FROM����
'where' => " a.chid='$chid' AND a.pid$arid='$pid' ",
'isab' => 1,//*** �Ƿ�ϼ��ڹ���0Ϊ��ͨ�����б�1Ϊ���ڹ����б�2Ϊ���������б�
'pid' => $pid,//�ϼ�id
'arid' => $arid,//*** ָ���ϼ���Ŀid
//'orderby' => "a.aid DESC",//�ϼ���ָ������,�ĵ���ϼ���¼��Ϊ"a.inorderxx DESC"��xxΪ�ϼ���Ŀid
'orderby' => "a.inorder{$arid} ASC,a.aid DESC",
);



/******************/

$oL = new cls_archives($_init);

//ͷ���ļ����������
$oL->top_head();

//������Ŀ ****************************
//���������Ŀ��s_additem($key,$cfg)
$oL->s_additem('keyword',array('fields' => array(),));//fields������Ĭ��Ϊarray('a.subject' => '����','a.mname' => '��Ա','a.aid' => '�ĵ�ID')
$oL->s_additem('caid',array());
$oL->s_additem('orderby',array(
	'options'=>array(
		0 => array('--����ʽ--','a.aid DESC'),
		1 => array('��������&darr;',"a.{$paixu} ASC,a.aid DESC"),            				
		2 => array('���ʱ��&darr;','a.createdate DESC'),        			
	)
));

//����sql��filter�ִ�����
$oL->s_deal_str();

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
$oL->o_additem('delete');
$oL->o_additem('caid',array('ids'=>array(7)));
$oL->o_addpushs();//������Ŀ


if(!submitcheck('bsubmit')){
	
	//������ʾ���� ****************************
	$oL->s_header();
	$oL->s_view_array();
	$oL->s_footer();

	
	//�����б��� **************************
	$hlinks = " &nbsp; <input class='checkbox' type='checkbox' onclick='chooseall(this)' value=''>ȫѡ";
	$hlinks .= " &nbsp; <a style=\"color:#C00\" href=\"?entry=extend&extend=xiangcearchive&pid=$pid\" onclick=\"return floatwin('open_arcexit',this)\">&gt;&gt;������</a>";
	$hlinks .= " &nbsp; <a style=\"color:#C00\" href=\"?entry=extend&extend=xiangcearchives&isframe=1\" target='_blank'>&gt;&gt;�������</a>";
	$oL->m_header("$hlinks",1);
	$oL->m_additem('selectid');
	$oL->m_additem('caid');
	$oL->m_additem($paixu,array('type' => 'input','title'=>'����','w' => 2));
	$oL->m_additem('subject',array('len' => 20));
	$oL->m_additem('thumb',array('type'=>'image','width'=>'100%','height'=>180));
	$oL->m_additem('detail',array('type'=>'url','title'=>'�༭','mtitle'=>'[�༭]','url'=>"?entry=extend&extend=xiangcearchive&aid={aid}",'width'=>40,'view'=>'H'));
	$oL->m_additem('littlethumb',array('type'=>'url','title'=>'��Ϊ����ͼ','mtitle'=>'[��Ϊ����ͼ]','url'=>"?entry=extend&extend=xiangces_pid&pid=$pid&setthumb={aid}",'width'=>40,'view'=>'H'));
	$oL->m_additem('ccid1');	
	$oL->m_additem('lpname',array('len' => 20,));	
	$oL->m_mcols_style("{thumb}<div style=\"clear:both;\"></div>{selectid}{subject}({caid})<br>����&nbsp;{{$paixu}}&nbsp;{littlethumb}&nbsp;{detail}&nbsp;&nbsp;<br/>[{ccid1}]{lpname}");//�����ĵ�ģʽ������ʾ��Ŀ�������ʽ,Ĭ��Ϊ��"{selectid} 
	
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
	$oL->o_view_upushs();
	
	//��ʾ������
	$oL->o_view_rows();
	
	$oL->o_footer('bsubmit');
	$oL->guide_bm('&nbsp;&nbsp;&nbsp;1.ѡ�������Ŀ>>ɾ�����ò����Ȱѱ�ҳ�������ɾ����Ҳ��������Դ���ĵ���Ķ�Ӧ����ɾ����<br/>&nbsp;&nbsp;&nbsp;&nbsp;(���磺�ڱ�ҳ��ɾ�������A����ô�ڶ�Ӧ����Ŀ"¥�����"�б��е����AҲ�ᱻɾ����)','1');
	
}else{
	//Ԥ����δѡ�����ʾ
	$oL->sv_header();
	
	//�б���������������ݴ���
//	$oL->sv_e_additem('clicks',array());
	$oL->sv_e_additem($paixu,array());
//	$oL->sv_e_additem('incheck',array('type' => 'bool'));
	$oL->sv_e_all();
	
	//��������������ݴ���
	$oL->sv_o_all();
	
	//��������
	$oL->sv_footer();
}
?>