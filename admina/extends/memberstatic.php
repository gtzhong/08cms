<?PHP
/**
* �����̨����̼Ҿ�̬�ռ�Ĳ���
* ����ϵͳ����Ҫ�������롢����
*/


/* ������ʼ������ */
$mid = empty($mid) ? 0 : max(0,intval($mid));
$_init = array(
	'mid' => $mid,//����һ����Ҫ����mid
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
	$oA->fm_header("��Ա��̬�ռ�","?entry=extend$extend_str");
	
	$oA->fm_items();
	
	//�����submitcheck(��ť����)��ͬ��ֵ
	$oA->fm_footer('bsubmit');
	
	//�����̨��������ʽ($str,$type)��$typeĬ��Ϊ0ʱ$strΪ���������ǣ�1��ʾ$strΪ�ı�����
	//��Ա���ģ�������ʽ($str,$type)��$str���������Ա���İ�����ʶ��ֱ�ӵ��ı����ݣ�$typeĬ��Ϊ0ֱ����ʾ���ݣ�tip-�����ص���ʾ��fix-�̶�����ʾ��
	$oA->fm_guide_bm('','0');
	
}else{
	//�ύ��Ĵ���
	$oA->sv_all_static();
}
