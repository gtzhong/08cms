<?PHP
$chid = isset($chid) ? intval($chid) : 119;
$ispid4 = empty($ispid4) ? 0 : 1; // ispid4����ж�Ϊ�����͹�˾�鿴���¾����˷�Դ��ش���
$mname = empty($ispid4) ? 0 : intval(@$mname);
$valid = isset($valid) ? intval($valid) : '-1';
switch($chid){
    case 119:
        $type = 'busrent_office';
        $coids = array(1,2,3,9,14,46,47);
        break;
    case 120:
        $type = 'busrent_shop';
        $coids = array(1,2,3,9,14,48,49);
        break;
    default:
        $type = 'busrent_office';
        $coids = array(1,2,3,9,14,46,47);
        break;
}

//�ж��Ǿ����˻��Ǿ��͹�˾������sql��������䣺other_sql��whrstr��
$userInfo = isCompany($ispid4,$curuser);
$other_sql = $userInfo['otherSql'];
$wherestr = $userInfo['whereStr'];
$namearr = $userInfo['agentNameArr'];

# ���CK���ID�����ƣ���������ýű�ʱ��̳���ȥ
cleanCookies(array('fyid', 'lpmc'), true);

$oL = new cls_archives(array(
'chid' => $chid,//ģ��id������
'url' => "?action=$action&ispid4=$ispid4",//��url���������Ҫ����chid��pid
'pre' => 'a.',//Ĭ�ϵ�����ǰ׺
'where' => $wherestr,//sql�еĳ�ʼ��where���޶�Ϊ���ѵ��ĵ�
//'from' => "",//sql�е�FROM����
'from' => $tblprefix.atbl($chid)." a INNER JOIN {$tblprefix}archives_$chid c ON c.aid=a.aid ",//sql�е�FROM����
'select' => "a.*,c.*",//sql�е�SELECT����
'cols' => 0,//Ĭ��Ϊ0����Ϊ����1��Ϊ�����ĵ�ģʽ����ͼƬ�б�(�趨һ��Ԫ�ز���Ҫ������)
'coids' => $coids,//�ֶ�����������ϵ
//'fields' => array(),//�������װ�����ֶλ���
));
//ͷ���ļ����������
$oL->top_head();
$oL->resetCoids($oL->A['coids']); //����/���� ��ϵ����
if($curuser->info['mchid']==1) resetCoids($oA->coids, array(19)); //ȥ��ĳЩ��ϵ

//������Ŀ ****************************
//s_additem($key,$cfg)
$oL->s_additem('keyword',array('fields' => array('a.subject' => '����','a.mname' => '��Ա','a.aid' => '�ĵ�ID','a.lpmc'=>'С������'),));//keys������Ĭ��Ϊarray('a.subject' => '����','a.mname' => '��Ա','a.aid' => '�ĵ�ID')
$oL->s_additem('caid',array('hidden' => 1,));
$oL->s_additem('checked',array());
$oL->s_additem('yuyue',array());//ԤԼɸѡ

$ord_opt = array(
	0 => array('-����ʽ-','refreshdate DESC'),
	1 => array('�ö�(����)','a.ccid9 DESC'),
// 	2 => array('�Ƽ�(����)','a.ccid19 DESC'),
	3 => array('�۸�(��)','a.zj DESC'),
	4 => array('�۸�(��)','a.zj ASC'),
	
	7 => array('���ʱ��(��)','a.aid DESC'),
	8 => array('���ʱ��(��)','a.aid ASC'),
	
	9 => array('���(��)','a.clicks DESC'),
	10 => array('���(��)','a.clicks ASC'),
);

$oL->s_additem('orderby', array('options'=>$ord_opt));
$oL->s_additem('shi',array('type'=>'field',));
$oL->s_additem('ting',array('type'=>'field',));
$oL->s_additem('wei',array('type'=>'field',));
$oL->s_additem('zxcd',array('type'=>'field','pre'=>'c.'));
$oL->s_additem('fl',array('type'=>'field','pre'=>'c.'));
$oL->s_additem('szlc',array('type'=>'field','pre'=>'c.'));
$oL->s_additem('mj',array());
$oL->s_additem('zj',array());
$oL->s_additem('valid');
foreach($oL->A['coids'] as $k){
    if($curuser->info['mchid']==1 && $k==19) continue;
    $oL->s_additem("ccid$k",array());
    if($k==3) $oL->s_additem("ccid14",array());
}
$oL->s_additem('indays');
$oL->s_additem('outdays');

//������ѡ����ʱ��ȥ�������ɸѡ����
if(!empty($fl) && $fl == -1) unset($oL->oS->wheres['fl']);

//����sql��filter�ִ�����
$oL->s_deal_str(); 

//���������·���ʾ��Ϣ������
$usedhouseLimitInfo = userCenterDisplayMes($curuser,$chid);


//����������Ŀ ********************
$oL->o_additem('delete');//ɾ��
if(!$ispid4){
	if(in_array($valid,array(1))) $oL->o_additem('readd',array('limit'=>$usedhouseLimitInfo['otherData']['refreshRemainNum'],'time'=>0,'fieldname'=>'refreshes','title'=>'����ˢ��'));//1440,ˢ�£�timeʱ����Ϊ(����),
	if(in_array($valid,array(0))) $oL->o_additem('valid',array('days'=>0));//�ϼܣ�days�����ϼܵ�������0��Ϊ������
	if(in_array($valid,array(1,-1))) $oL->o_additem('unvalid');//�¼�
	$curuser->info['mchid'] == 2 && $oL->o_additem("ccid19",array('title'=>'�����Ƽ�')); 
}

if(!submitcheck('bsubmit')){
	if(!$ispid4){
	    //�������ҳ��ͷ��������Ŀ
        slidingColumn($type,$valid);
		$oL->guide_bm($usedhouseLimitInfo['message'],'fix');
	}
	
	//��ʾ�б���ͷ�� ***************

	//�������� ******************
	
	$oL->s_header(); 
	$oL->s_view_array(array('keyword',));//�̶���ʾ��
	if($ispid4){
		echo "<select style=\"vertical-align: middle;\" name=\"mname\">".makeoption($namearr,$mname)."</select> ";
	}
	$oL->s_view_array(array('checked','ccid9'));//�̶���ʾ��//ccid19,'yuyue'
	$oL->s_adv_point();//����������
	$oL->s_view_array(array('shi','ting','wei','zxcd','fl','szlc','mj','zj'));
	echo "<br/>";
	$oL->s_view_array();
    
	//������excel
	$oL->s_footer_ex("?action=export_excel_items&chid=$chid&filename=chuzu".(empty($ispid4)?'':"&ispid4=".$ispid4),array('sql'=>$other_sql));
	
	if(empty($fcdisabled2)) RelCcjs($chid,1,2,1);
	if(empty($fcdisabled3)) RelCcjs($chid,3,14,2);
	
	$oL->m_header((empty($mname) ? '' : $namearr[$mname]." - ")."���ⷿԴ����");
	
	//�����б���Ŀ������б����а������������Ҫ�����ݴ���ʱ������������Ĵ���
	//���飬���ȳ��ֵ��������м��룺'group' =>'item,���ݷָ���,�����ָ���',���ݷָ�������ֱ������,�����б���ķָ���������ֻʹ�õ�һ�����
	
	$oL->m_additem('selectid',array('view'=>''));
	
	$oL->m_additem("ccid1",array('side'=>'L'));
	$oL->m_additem('csubject',array('len' => 40,'view'=>'L'));
	$oL->m_addgroup('[{ccid1}]{csubject}','��Դ����/С������<br>������Ϣ');//��ע����鲻��Ƕ�ף�ÿ��ֻ�ܲ���һ�η���


        $oL->m_additem('clicks',array('title'=>'���',));
        $oL->m_additem('enddate',array('type'=>'date','title'=>'����ʱ��',));
        $oL->m_additem("ccid9",array('url'=>'?action=zding&aid={aid}'));

    $oL->m_additem('refreshdate',array('type'=>'date',));
	$oL->m_additem("zxcd",array('type'=>'field',));
	$oL->m_addgroup('{refreshdate}<br>{zxcd}','ˢ������<br>װ�޳̶�');

	$oL->m_additem("zj",array('mtitle'=>'{zj}Ԫ/��'));
	$oL->m_additem("fkfs",array('type'=>'field')); 
	
    $oL->m_additem('zj',array('title'=>'�۸�',));

	
	$oL->m_additem('checked',array('mtitle'=>'���','type'=>'bool',));
	$oL->m_additem('valid',array('mtitle'=>'�ϼ�',));
	$oL->m_addgroup('{checked}<br>{valid}','���<br>�ϼ�');

	$oL->m_additem('info',array('type'=>'url','title'=>'����','mtitle'=>'����','url'=>"?action=archiveinfo&aid={aid}",'width'=>40,));
	$oL->m_additem('detail',array('type'=>'url','title'=>'�༭','mtitle'=>'����','url'=>"?action=bus_chuzuadd&aid={aid}&chid=$chid&ispid4=$ispid4",'width'=>40,));
	$oL->m_addgroup('{detail}<br>{info}','�༭<br>����');
	
	//��ʾ�����У����ж���չʾ�Ļ�����Ҫ
	$oL->m_view_top(); 
	
	//ȫ���б������������Ҫ���ƣ�����ʹ�����е�ϸ�ַ���
	$oL->m_view_main_fy(array('trclass'=>'bg bg2'), $curuser->info['mchid']); 
	
	//��ʾ�б���β��
	$oL->m_footer();
	
	//��ʾ����������************
	$oL->o_header();
	
	//��ʾ��ѡ��
	//$oL->o_view_bools('���б���',array('bool1','bool2',));
	$oL->o_view_bools();
	
	//��ʾ������
	$oL->o_view_rows();
	
	$oL->o_footer('bsubmit');
	$oL->guide_bm('','0');
	
}else{
	
	//Ԥ����δѡ�����ʾ
	$oL->sv_header(array(9,));
	
	//��������������ݴ���
	$oL->sv_o_all();
	
	//��������
	$oL->sv_footer();
}
?>