<?PHP
/**
* ��Ա���ĵ��̼ҿռ侲̬����
* ����ϵͳ����Ҫ�������롢����
*/


/* ������ʼ������ */
@set_time_limit(0);
$_init = array(//��Ա���Ŀ��Բ������κβ���
);

#-----------------
$oA = new cls_member($_init);

$oA->TopHead();//�ļ�ͷ��

$oA->TopAllow();//��������Ȩ��

/*��ʼ��������Ŀ*/
$oA->additem('mtcid');//�ռ�ģ�巽��
$oA->additem('static_state');//��Ա�ռ侲̬״̬
$oA->additem('mspacepath');//�ռ侲̬Ŀ¼

if(!submitcheck('bsubmit')){
	
	//($title,$url)��url�пɲ�ָ��mchid��mid
	$oA->fm_header("��Ա��̬�ռ�","?action=static");
	
	$oA->fm_items();
	
	//�����submitcheck(��ť����)��ͬ��ֵ
	$oA->fm_footer('bsubmit');
	
	//�����̨��������ʽ($str,$type)��$typeĬ��Ϊ0ʱ$strΪ���������ǣ�1��ʾ$strΪ�ı�����
	//��Ա���ģ�������ʽ($str,$type)��$str���������Ա���İ�����ʶ��ֱ�ӵ��ı����ݣ�$typeĬ��Ϊ0ֱ����ʾ���ݣ�tip-�����ص���ʾ��fix-�̶�����ʾ��
	$oA->fm_guide_bm('mem_static','fix');
	
}else{
	//�ύ��Ĵ���
	$oA->sv_all_static();
}
?>