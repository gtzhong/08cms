<?PHP
/**
* �����̨�Ļ�Ա��ӽű�
* ԭ���Ͽ��Լ��ݸ��ֻ�Աģ�ͣ���Ϊ�˾�ϸ������������ʹ�ö��Ʋ�ͬģ�͵Ľű�
*/

/* ������ʼ������ */
//$mchid = 3;//������ģ�Ͷ��ƣ����ֶ�ָ��ģ��id
$mchid = empty($mchid) ? 0 : max(0,intval($mchid));//�����ⲿ��mchid
$_init = array(
	'mchid' => $mchid,//���һ����Ҫ����mchid
);


#-----------------
$oA = new cls_member($_init);

$oA->TopHead();//�ļ�ͷ��

$oA->TopAllow();//��������Ȩ��

/*��ʼ��������Ŀ*/
$oA->additem('mname');//�ʺ�
$oA->additem('password');//����
$oA->additem('email');//�����ʼ�

foreach($oA->fields as $k => $v){
	$oA->additem($k,array('_type' => 'field'));//��̨�ܹ��ֶ�
}

if(!submitcheck('bsubmit')){
	
	//($title,$url)��url�пɲ�ָ��mchid��mid
	$oA->fm_header("","?entry=extend$extend_str");
	
	$oA->fm_items('mname,password,email');//ָ��������
	
	$oA->fm_footer();
	
	$oA->fm_header("��������");
	
	$oA->fm_items();//ʣ����
	
	//�����submitcheck(��ť����)��ͬ��ֵ
	$oA->fm_footer('bsubmit');
	
	//�����̨��������ʽ($str,$type)��$typeĬ��Ϊ0ʱ$strΪ���������ǣ�1��ʾ$strΪ�ı�����
	//��Ա���ģ�������ʽ($str,$type)��$str���������Ա���İ�����ʶ��ֱ�ӵ��ı����ݣ�$typeĬ��Ϊ0ֱ����ʾ���ݣ�tip-�����ص���ʾ��fix-�̶�����ʾ��
	$oA->fm_guide_bm('','0');
	
}else{
	//�ύ��Ĵ���
	$oA->sv_all_common();
}
