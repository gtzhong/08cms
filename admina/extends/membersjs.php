<?PHP

/* ������ʼ������ */
$mchid = empty($mchid) ? 0 : max(0,intval($mchid));//���ֶ�ָ�����������ⲿ����
if(!in_array($mchid,array(3,11,12))) $mchid = 3;
$corp_name = array(
	 3=>'cmane',     //���͹�˾
	11=>'companynm', //װ�޹�˾
	12=>'companynm', //Ʒ���̼�
);
$_init = array(
'mchid' => $mchid,//��Աģ��id�������Ϊ0
'url' => "?entry=$entry$extend_str",//��url���������Ҫ����mchid
'select' => "m.*,m.mid as mmid,s.*,t.*",
'from' => "{$tblprefix}members m INNER JOIN {$tblprefix}members_sub s ON m.mid= s.mid
						INNER JOIN {$tblprefix}members_$mchid t ON t.mid=m.mid ",//sql��FROM֮����������֣�����ͨ������JOIN����������members m LEFT JOIN members_sub s ON (s.mid=m.mid) LEFT JOIN members_$mchid c ON (c.mid=m.mid)  
);
/******************/

$oL = new cls_members($_init);

//ͷ���ļ����������
$oL->top_head();

//������Ŀ ****************************
$oL->s_additem('keyword',array('fields' => array('m.mname' => '�û���','m.mid' => '��ԱID','s.ming' => 'ר������','s.lxdh' => '��ϵ�绰'),));//fields������Ĭ��Ϊarray('m.mname' => '��Ա�ʺ�','m.mid' => '��ԱID')
$oL->s_additem('nchid');//ʹ��nchid����ģ��ɸѡ������ʹ��mchid��mchid�ǹ̶��ģ���ǰ�ű�ָ��mchidʱ�������Զ����ء�
$oL->s_additem('checked');//���
$oL->s_additem('mctid');//��֤��������Ч��֤���ͣ����Զ�����
$oL->s_additem('orderby');//����
$oL->s_additem('indays',array('title'=>'����ע��'));//��������ע��
$oL->s_additem('outdays',array('title'=>'��ǰע��'));//������ǰע��
if(in_array($mchid,array(11,12))){    
    $groupnum = $mchid == 11 ? 31 : 32;//31װ�޹�˾  32Ʒ���̼�
    $oL->s_additem('gtype_enddate',array('groupnum'=>$groupnum,'title'=>'����ʧЧ'));//��������ʧЧ
}

$grouptypes = cls_cache::Read('grouptypes');
foreach($grouptypes as $k => $v){//����ʱ����ָ��id��ʾ��Ա������������������ϵ
	$oL->s_additem("ugid$k");//��Ա��
}
# $oL->s_additem('shi',array('type'=>'field',));//ʹ�ÿ�ѡ�ֶ�����,����ģ��ʱֻ����ͨ���ֶ�

//����sql��filter�ִ����� ****************
$oL->s_deal_str();

//����������Ŀ ********************
$oL->o_addpushs();//������Ŀ

$oL->o_additem('delete');
$oL->o_additem('check');
$oL->o_additem('uncheck');
$oL->o_additem('static');
$oL->o_additem('unstatic');
foreach($grouptypes as $k => $v){//����ʱ����ָ��id������������������������ϵ
	$oL->o_additem("ugid$k");//��Ա��
}

if(!submitcheck('bsubmit')){
	
	//�������� ******************
	$oL->s_header();
    if(in_array($mchid,array(11,12))){
	   $oL->s_view_array(array('keyword','checked','mctid','indays','outdays','gtype_enddate'));
       //������ʾ�����ʱ����Ҫ�Ļ�Ա����ʾ����
    }else{
       $oL->s_view_array(array('keyword','orderby','checked','mctid','indays','outdays'));
    }
	$oL->s_adv_point();//����������
	$oL->s_view_array();//����������ʾ������
	$oL->s_footer();
	
	//�б��� ***************
	$oL->m_header();
	//�����б���Ŀ
	$oL->m_additem('selectid');
	//$oL->m_additem('subject',array('len' => 40,'field' => 'mname'));//�����˻�Ա��Ǽ��ռ�url�ı��⣬nourl��ʾ����Ҫ�ռ�url
	$oL->m_additem('mname',array('type'=>'other','title'=>'�û���',));
	$oL->m_additem('subject',array('len' => 28,'field' => $corp_name[$mchid],'title' => '��˾����'));
   	$oL->m_additem('employee',array('title'=>'��˾Ա��','url'=>"?entry=extend&extend=employees_pid&pid={mmid}"));
	$oL->m_additem('regip',array('type'=>'regip','title'=>'ע��IP','len' => 40,'view'=>'H'));
	$oL->m_additem('szqy',array('type'=>'szqy','title'=>'��������'));
	$oL->m_additem('mchid');//��Ա����
	$oL->m_additem('checked',array('type'=>'bool','title'=>'���',));
	foreach($grouptypes as $k => $v){//���ƽű�ʱ����ָ��id������ʾ��ʽ��λ��
		$oL->m_additem("ugid$k");//��Ա�飬��view��Ϊ����Ĭ����ʾ
	}

	$mctypes = cls_cache::Read('mctypes');
	foreach($mctypes as $k => $v){//���ƽű�ʱ����ָ��id������ʾ��ʽ��λ��
		$oL->m_additem("mctid$k");//��Ա��֤����view��Ϊ����Ĭ����ʾ
	}

	$oL->m_additem('regdate',array('type'=>'date',));//ע��ʱ��
    if(in_array($mchid,array(11,12))){
        $filename = 'grouptype'.$groupnum.'date';
        $oL->m_additem($filename,array('title'=>'ʧЧ����','type'=>'date',));//ע��ʱ��
    }
	$oL->m_additem('lastvisit',array('type'=>'date','view'=>'H',));//�ϴε�¼ʱ��
	$oL->m_additem('info',array('type'=>'url','title'=>'����','mtitle'=>'����','url'=>"?entry=extend&extend=memberinfo&mid={mid}",'width'=>30));
	$oL->m_additem('group',array('type'=>'url','title'=>'��Ա��','mtitle'=>'��Ա��','url'=>"?entry=extend&extend=membergroup&mid={mid}",'width'=>40));
	$oL->m_additem('detail',array('type'=>'url','title'=>'�༭','mtitle'=>'����','url'=>"?entry=extend&extend=memberedit&mid={mid}",'width'=>30,));
	$oL->m_additem('static');//��Ա�ռ侲̬
	$oL->m_additem('trustee',array('title'=>'����','width'=>30));//��Ա���Ĵ���

	
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
