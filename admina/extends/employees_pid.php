<?PHP

/* ������ʼ������ */
$mchid = 2;//���ֶ�ָ�����������ⲿ����
$pid4 = empty($pid)?0:max(1,intval($pid));
if(!in_array($mchid,array(0,1,2))) $mchid = 0;
$_init = array(
'mchid' => $mchid,//��Աģ��id�������Ϊ0
'url' => "?entry=$entry$extend_str&pid=$pid4",//��url���������Ҫ����mchid
'from' => "{$tblprefix}members m INNER JOIN {$tblprefix}members_sub s ON m.mid = s.mid ",
'select'=>"m.*,s.szqy,s.xingming",
'where'=>" m.pid4=$pid4 AND m.incheck4='1' "
);
/******************/

$oL = new cls_members($_init);

//ͷ���ļ����������
$oL->top_head();

//������Ŀ ****************************
$oL->s_additem('keyword',array('fields' => array('m.mname' => '��Ա�ʺ�','m.mid' => '��ԱID')));
$oL->s_additem('checked');//���

//����sql��filter�ִ����� ****************
$oL->s_deal_str();

//����������Ŀ ********************
$oL->o_addpushs();//������Ŀ

$oL->o_additem('delete');
$oL->o_additem('check');
$oL->o_additem('uncheck');
$oL->o_additem('static');
$oL->o_additem('unstatic');

if(!submitcheck('bsubmit')){
	
	//�������� ******************
	$oL->s_header();
	$oL->s_view_array();
	$oL->s_footer();
	
	//�б��� ***************
	$oL->m_header();
	//�����б���Ŀ
	$oL->m_additem('selectid');
	$oL->m_additem('subject',array('len' => 40,'field' => 'mname','title'=>'�û���','nourl'=>array(1,13)));//�����˻�Ա��Ǽ��ռ�url�ı��⣬nourl��ʾ����Ҫ�ռ�url
	$oL->m_additem('regip',array('type'=>'regip','title'=>'ע��IP','len' => 40,'view'=>'H'));
	$oL->m_additem('xingming',array('title'=>'����'));
	$oL->m_additem('mchid');//��Ա����
	$oL->m_additem('szqy',array('type'=>'szqy','title'=>'��������'));    
	$oL->m_additem('checked',array('type'=>'bool','title'=>'���',));
	$oL->m_additem('regdate',array('type'=>'date',));//ע��ʱ��
    $oL->m_additem('grouptype14date',array('title'=>'ʧЧ����','type'=>'date',));//ע��ʱ��
	$oL->m_additem('lastvisit',array('type'=>'date','view'=>'H',));//�ϴε�¼ʱ��
	$oL->m_additem('info',array('type'=>'url','title'=>'����','mtitle'=>'����','url'=>"?entry=extend&extend=memberinfo&mid={mid}",'width'=>30));
	$oL->m_additem('group',array('type'=>'url','title'=>'��Ա��','mtitle'=>'��Ա��','url'=>"?entry=extend&extend=membergroup&mid={mid}",'width'=>40,));
	$oL->m_additem('detail',array('type'=>'url','title'=>'�༭','mtitle'=>'����','url'=>"?entry=extend&extend=memberedit&mid={mid}",'width'=>30,));
	$mchid != 1 && $oL->m_additem('static');//��Ա�ռ侲̬
	$oL->m_additem('trustee',array('title'=>'����'));//��Ա���Ĵ���

	
	//��ʾ������
	$oL->m_view_top();
	
	//ȫ���б������������Ҫ���ƣ�����ʹ�����е�ϸ�ַ���
	$oL->m_view_main();
	
	//��ʾ�б���β��
	$oL->m_footer();
	
	//��ʾ����������************
	$oL->o_header();
	
	//��ʾ��ѡ��
	$oL->o_view_bools('',array(),8);
	
	//��ʾ����λ
	$oL->o_view_pushs();
	
	//��ʾ������
	$oL->o_view_rows();
	
	$oL->o_footer('bsubmit');
	$oL->guide_bm('','0');
	
	
}else{
	//Ԥ����δѡ�����ʾ
	$oL->sv_header();
	
	//��������������ݴ���
	$oL->sv_o_all();
	
	//��������
	$oL->sv_footer();
}
