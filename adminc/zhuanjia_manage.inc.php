<?PHP
/* ������ʼ������ */
$_init = array(//��Ա���Ŀ��Բ������κβ���
);
#-----------------
$cuid = 42; 
$commu = cls_cache::Read('commu',$cuid); 

$memid = $curuser->info['mid'];
$mchid = $curuser->info['mchid'];
$mname = $curuser->info['mname'];
$grp34 = $curuser->info['grouptype34'];

$fadd = 0; // ���ӽ���
$fedt = 1; // �޸ı��(ר������)
if($grp34){
	$title = "ר�������޸�";	
	$fedt = 0;
}else{
	$title = 'ר������';
	$val = $db->result_one("SELECT mid FROM {$tblprefix}$commu[tbl] WHERE mid='$memid'");
	if($val){ // 
		$title .= ' --- (���ϴ����,�ɼ����޸�����)';
	}else{
		$title .= ' --- (������)';
		$fadd = 1;
	}
} 


$oA = new cls_member($_init);
$oA->TopHead();//�ļ�ͷ��
$oA->TopAllow();//��������Ȩ��

$mfexp = array('dantu','ming','danwei','quaere');
foreach($oA->fields as $k => $v){//��̨�ܹ��ֶ�
	if(in_array($k,$mfexp)){
		$oA->additem($k,array('_type' => 'field'));
	}
}

if(!submitcheck('bsubmit')){
	
	//($title,$url)��url�пɲ�ָ��mchid��mid
	$oA->fm_header($title,"?action=$action");
	$oA->fm_items();
	//�����submitcheck(��ť����)��ͬ��ֵ
	$oA->fm_footer('bsubmit');
	if(!$fedt) echo "<script type='text/javascript'>\$id('fmdata[ming]').readOnly = true;\$id('fmdata[ming]').style.border=0;</script>";
	
	//�����̨��������ʽ($str,$type)��$typeĬ��Ϊ0ʱ$strΪ���������ǣ�1��ʾ$strΪ�ı�����
	//��Ա���ģ�������ʽ($str,$type)��$str���������Ա���İ�����ʶ��ֱ�ӵ��ı����ݣ�$typeĬ��Ϊ0ֱ����ʾ���ݣ�tip-�����ص���ʾ��fix-�̶�����ʾ��
	$oA->fm_guide_bm('','0');
	
}else{
	
	//�ύ��Ĵ���
	//$oA->sv_all_common(array('message'=>'ר������������ɣ�'));
	
	/*
		if(empty($fmdata['dantu'])){
			$member_image = $db->result_one("select image from {$tblprefix}members_sub where mid = '$memberid'");
			$fmdata['dantu'] = 	$member_image;
		}	
		$fmdata['ming']=empty($fmdata['ming'])?$mname:$fmdata['ming'];	
	*/
	
	//����$this->fmdata�е�ֵ
	$oA->sv_set_fmdata();
	
	//�����Ҫ��mname,password,email֮��ִ��
	$oA->sv_add_init();
	
	//�������µ�������Ŀ������ʱδִ�����ݿ����
	$oA->sv_items();
	
	//ִ���Զ��������������ϱ��
	$oA->sv_update();
	
	//�ϴ�����
	$oA->sv_upload();
	//���ӽ���
	if($fadd){
		$sqlins = "mid='$memid',mname='{$curuser->info['mname']}',createdate='$timestamp',checked='".@$commu['autocheck']."'";		
		$db->query("INSERT INTO {$tblprefix}$commu[tbl] SET $sqlins"); // ip='$onlineip',
	}
	//����ʱ��Ҫ�����񣬰���������¼���ɹ���ʾ��
	$oA->sv_finish(array('message'=>'ר������������ɣ�'));	
}
?>
