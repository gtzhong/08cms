<?php
 
$cuid = 47; //�����ⲿ��chid����Ҫ��������
$caid = empty($caid) ? 0 : max(0,intval($caid));
$cid = empty($cid) ? 0 : max(0,intval($cid));
$area = empty($area) ? 0 : max(0,intval($area));
$class = empty($cid) ? 'cls_culist' : 'cls_cuedit'; 
$fill_sites = empty($fill_sites) ? 0 : max(0,intval($fill_sites));
$del_month = empty($del_month) ? 0 : max(0,intval($del_month));
$baseurl = "?entry=extend&extend=pricetrends&caid=$caid"; 

switch($caid){
    case 2:
        $chid = 4;//¥��
        $avg_field = 'dj';//����
        $price_unit = 'Ԫ/M<sup>2</sup>';//�۸�λ
        $price_title = '����';
    break;
    case 3:
        $chid = 3;//����
        $avg_field = 'zj';//�ܼ�
        $price_unit = '��Ԫ';//�۸�λ
        $price_title = '�ܼ�';
    break;
    case 4:
        $chid = 2;//����
        $avg_field = 'zj';//����
        $price_unit = 'Ԫ/��';//�۸�λ
        $price_title = '�ܼ�';
    break;
    default:
        $chid = 4;//¥��
        $avg_field = 'dj';//����
        $price_unit = 'Ԫ/M<sup>2</sup>';//�۸�λ
        $price_title = '����';
    break;
}

//�Զ���ǰʮ�����µļ۸���д���
price_trend($chid,$avg_field,$cuid);
$tblprefix = cls_env::getBaseIncConfigs('tblprefix');

$_init = array(
	'cuid' => $cuid,//����ģ��id
	'ptype' => 'u',
	'pchid' => $chid,
	'caid' => $caid,
	'url' => "", //��url���������Ҫ����mchid
	'select'=>' SELECT cu.cid, cu.month, cu.price, cu.area ',
	'from'=>" FROM {$tblprefix}commu_pricetrend cu ",
	'where' => " AND cu.chid = '$chid' AND area='$area' ", //��������,ǰ����Ҫ[ AND ]
	'orderby' => " month DESC "
);

if($cid){
	$_init['cid'] = $cid;
	$oA = new $class($_init);
	$oA->top_head(array('chkData'=>1,'setCols'=>1));
	
	if(!submitcheck('bsubmit')){
		$oA->fm_header("","&caid=$caid");
		$oA->fm_items('','',array('noaddinfo'=>1));
		$oA->fm_reference_price($avg_field,$chid,$oA->predata);//��ǰ�µĲο���
		$oA->fm_footer('bsubmit');
		$oA->guide_bm('','0');
	}else{
		//�ύ��Ĵ���
		$oA->sv_all_common();
	}
}elseif($fill_sites){
	aheader();
	price_sites($chid,$avg_field); 
	cls_message::show('��ȫ��վ������ɣ�',$baseurl);
}elseif($del_month){
	aheader(); 
	$db->query("DELETE FROM {$tblprefix}commu_pricetrend WHERE month='$del_month' ");
	echo "<script>floatwin('close_arcdel',this);</script>";
	cls_message::show('ɾ������������ɣ�',axaction(6,M_REFERER));	
}else{
	$oL = new $class($_init); 
	$oL->top_head(); 

	//������Ŀ ****************************
	//����sql��filter�ִ�����
	$oL->s_deal_str(); //echo $oL->sqlall;
	//����������Ŀ ********************
	$oL->o_additem('delete',array('title'=>'ɾ����ǰ������ѡ��'));
	if(!submitcheck('bsubmit')){
		
		//�������� ******************
		$oL->s_header();
		//$oL->s_view_array();
		$oL->s_footer_area($baseurl);
		
		//��ʾ�б���ͷ�� ***************
		$oL->m_header( );
		$oL->m_additem('selectid'); 
		$oL->m_additem('month',array('type'=>'date','fmt'=>'Y-m','len'=>40,'title'=>'�·�','side'=>'L')); 
		$oL->m_additem('area',array('type'=>'trendarea','title'=>'����','side'=>'L')); 
        $oL->m_additem('price',array('title'=>$price_title,'mtitle'=>"{price}$price_unit",'side'=>'L'));	      
		$oL->m_additem('detail',array('type'=>'url','title'=>'�༭','mtitle'=>'����','url'=>"?entry=extend$extend_str&caid=$caid&cid={cid}",'side'=>'L',));
		$oL->m_additem('del',array('type'=>'url','title'=>'ɾ����������','mtitle'=>'(�����е���)','url'=>"?entry=extend$extend_str&caid=$caid&del_month={month}",'side'=>'L'));
		
		$oL->m_view_top(); //��ʾ�����У����ж���չʾ�Ļ�����Ҫ
		$oL->m_view_main(); 
		$oL->m_footer(); //��ʾ�б���β��
		
		$oL->o_header(); //��ʾ����������************
		$oL->o_view_bools(); //��ʾ��ѡ��
		
		$oL->o_footer('bsubmit');
		$oL->guide_bm('','0');
		
	}else{
		
		$oL->sv_header(); //Ԥ����δѡ�����ʾ
		$oL->sv_o_all(); //��������������ݴ���
		$oL->sv_footer(); //��������
		
	}
			
}

?>