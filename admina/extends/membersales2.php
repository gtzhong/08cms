<?PHP

/* ������ʼ������ */
$mid = empty($mid) ? 0 : max(0,intval($mid));
$mchid = 13;//������ģ�Ͷ��ƣ����ֶ�ָ��ģ��id

if($mid){
	$_init = array(
		'mid' => $mid,//����һ����Ҫ����mid
	);
}else{
	$_init = array(
		'mchid' => $mchid,//���һ����Ҫ����mchid
	);
}

#-----------------
$oA = new cls_member($_init);

$oA->TopHead();//�ļ�ͷ��

$oA->TopAllow();//��������Ȩ��

/*��ʼ��������Ŀ*/
$oA->additem('mname');//�ʺ�
$oA->additem('password');//����
$oA->additem('email');//�����ʼ�
$oA->additem('loupan');//loupan,�����¥��
$oA->additem('xiezilou');//loupan,�����д��¥
$oA->additem('shaopu');//loupan,���������

$mfexp = array('dantu','ming','danwei','quaere');
foreach($oA->fields as $k => $v){
	if(!in_array($k,$mfexp))
	$oA->additem($k,array('_type' => 'field'));//��̨�ܹ��ֶ�
}

if(!submitcheck('bsubmit')){
	
	//($title,$url)��url�пɲ�ָ��mchid��mid
	$oA->fm_header("","?entry=extend$extend_str");
	
	$oA->fm_items('mname,password,email');//ָ��������
	
	//$oA->fm_footer();
	
	//$oA->fm_header("��������");
	
	$oA->fm_items('xingming,loupan,xiezilou,shaopu');
	//trbasic('�����¥��','',getArchives('4',$actuser->info['loupan'],8,'loupan[]','¥��'),'');
	$oA->fm_items();//ʣ����
	
	//�����submitcheck(��ť����)��ͬ��ֵ
	$oA->fm_footer('bsubmit');
	
	//�����̨��������ʽ($str,$type)��$typeĬ��Ϊ0ʱ$strΪ���������ǣ�1��ʾ$strΪ�ı�����
	//��Ա���ģ�������ʽ($str,$type)��$str���������Ա���İ�����ʶ��ֱ�ӵ��ı����ݣ�$typeĬ��Ϊ0ֱ����ʾ���ݣ�tip-�����ص���ʾ��fix-�̶�����ʾ��
	$oA->fm_guide_bm('','0');
	
}else{
	//�ύ��Ĵ���
	$oA->sv_all_common();
	
	//$actuser->updatefield('loupan',$loupan,"members_$mchid");
}
