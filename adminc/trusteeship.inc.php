<?PHP
/**
* ��Ա���ĵ��й��������ýű�
*/


/* ������ʼ������ */
$_init = array(//��Ա���Ŀ��Բ������κβ���
	'noTrustee' => 1,//��ֹ�����˲���
);
#-----------------

$oA = new cls_member($_init);

$oA->TopHead();//�ļ�ͷ��

$oA->TopAllow();//��������Ȩ��

/*��ʼ��������Ŀ-->*/
$oA->additem('trusteeship');//�����й�����
#-----------------

if(!submitcheck('bsubmit')){
	
	//($title,$url)��url�пɲ�ָ��mchid��mid
	$oA->fm_header("�����й�����","?action=$action");
	
	$oA->fm_items();
	
	//�����submitcheck(��ť����)��ͬ��ֵ
	$oA->fm_footer('bsubmit');
	
	//�����̨��������ʽ($str,$type)��$typeĬ��Ϊ0ʱ$strΪ���������ǣ�1��ʾ$strΪ�ı�����
	//��Ա���ģ�������ʽ($str,$type)��$str���������Ա���İ�����ʶ��ֱ�ӵ��ı����ݣ�$typeĬ��Ϊ0ֱ����ʾ���ݣ�tip-�����ص���ʾ��fix-�̶�����ʾ��
	$oA->fm_guide_bm('trusteeship','fix');
	
}else{
	//�ύ��Ĵ���
	$oA->sv_all_common();
}
?>