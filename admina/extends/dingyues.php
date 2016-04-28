<?php
 
$cuid = 7; //�����ⲿ��chid����Ҫ��������
$caid = empty($caid) ? 0 : max(0,intval($caid));
$chid = cls_cubasic::caid2chid($caid);
$mid = empty($mid) ? 0 : max(0,intval($mid));


$select_str = empty($mid) ? " SELECT cu.mid,cu.mname,MAX(cu.senddate) AS senddate,COUNT(cu.cid) AS total,SUM(cu.new) AS newnum,SUM(cu.old) AS oldnum,SUM(cu.rent) AS rentnum " : " SELECT cu.*,cu.createdate AS ucreatedate,a.initdate,a.caid,a.chid,a.customurl,a.nowurl,a.subject " ; 
$from_str = empty($mid) ? " FROM {$tblprefix}commu_gz  cu " : " FROM {$tblprefix}commu_gz cu INNER JOIN {$tblprefix}".atbl(4)." a ON a.aid=cu.aid " ;
$where_str = empty($mid) ? " AND (new!=0 OR old!=0 OR rent!=0)  GROUP BY cu.mid" : " AND cu.mid='$mid' " ;
$orderby_str = empty($mid) ? " cu.mid ASC " : "" ;

$_init = array(
	'cuid' => $cuid,//����ģ��id
	'ptype' => 'u',
	'pchid' => $chid,
	'caid' => $caid,
	'url' => "", //��url���������Ҫ����mchid
	'select'=>$select_str,
	'from'=>$from_str,
	'where' =>$where_str, //��������,ǰ����Ҫ[ AND ]
	'orderby' =>$orderby_str,
);


if($mid){
	aheader();
	$modearr = array('new' => '������̬','old' => '�������ַ�','rent' => '��������',);
	$query = $db->query(" $select_str $from_str $where_str");
	$content = '';
	while($r = $db->fetch_array($query)){
		cls_ArcMain::Url($r,-1);
		$content .= "\n[$r[subject]]";
		foreach($modearr as $k => $v){
			$url = $k == 'new' ? $r['arcurl'] : ($k == 'old' ? $r['arcurl8'].'&fang=mai' : $r['arcurl8'].'&fang=zhu');
			$r[$k] && $content .= "&nbsp; >><a href=\"$url\" target=\"_blank\">$v</a> ";
		}
		$mname = $r['mname'];
	}
	$content || cls_message::show('ָ���Ļ�Աû��¥����Ϣ��');
	$na = array('mid' => $mid,'mname' => $mname,'content' => $content);
	tabheader("¥���ʼ�Ԥ��");
	trbasic('�ʼ�����','',splang('dingyue_subject',$na),'');
	trbasic('�ʼ�����','',nl2br(splang('dingyue_content',$na)),'');
	tabfooter();
}else{
	$oL = new cls_culist($_init); 
	$oL->top_head();

	//������Ŀ ****************************
	$oL->s_additem('keyword',array('fields' => array('cu.mid'=>'��ԱID','cu.mname' => '��Ա����',)));
	$oL->s_additem('checked');
	$oL->s_additem('indays');
	$oL->s_additem('outdays');
	//����sql��filter�ִ�����
	$oL->s_deal_str(); 
	
	//����������Ŀ ********************
	$oL->o_additem('del_lpdy');
	$oL->o_additem('send_email');

	if(!submitcheck('bsubmit')){
		
		//�������� ******************
		$oL->s_header();
		$oL->s_view_array();
		$oL->s_footer();
		
		//��ʾ�б���ͷ�� ***************
		$oL->m_header('',''," &nbsp; &nbsp; &gt;&gt;<a href='?entry=splangs&action=splangsedit'>�༭�ʼ�ģ��</a>" );
		$oL->m_additem('selectmid');
		$oL->m_additem('mid',array('title'=>'��ԱID','side'=>'C'));		
        $oL->m_additem('mname',array('title'=>'��Ա����','side'=>'C'));	
		$oL->m_additem('total',array('title'=>'¥��','side'=>'C'));	
        $oL->m_additem('newnum',array('title'=>'������̬','side'=>'C'));
		$oL->m_additem('oldnum',array('title'=>'�������ַ�Դ','side'=>'C'));
		$oL->m_additem('rentnum',array('title'=>'�������ⷿԴ','side'=>'C'));
		$oL->m_additem('senddate',array('title'=>'�������','type'=>'date','side'=>'C'));
       
		$oL->m_additem('detail',array('type'=>'url','title'=>'�ʼ�Ԥ��','mtitle'=>'Ԥ��','url'=>"?entry=extend$extend_str&cuid=$cuid&caid=$caid&mid={mid}",'width'=>80,));
		
		$oL->m_view_top(); //��ʾ�����У����ж���չʾ�Ļ�����Ҫ
		$oL->m_view_main(); 
		$oL->m_footer(); //��ʾ�б���β��
		
		$oL->o_header(); //��ʾ����������************
		$oL->o_view_bools(); //��ʾ��ѡ��
		
		$oL->o_footer('bsubmit');
		$oL->guide_bm('','0');
		
	}else{
		
		$oL->sv_header(); //Ԥ����δѡ�����ʾ
		$oL->sv_o_all_lpdy(); //��������������ݴ���
		$oL->sv_footer(); //��������
		
	}
			
}

?>