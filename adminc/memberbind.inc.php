<?PHP
/**
* ��Ա���ĵĻ�Ա�޸�����ű�
* ����ϵͳ����Ҫ�������롢����
*/


/* ������ʼ������ */
$_init = array(//��Ա���Ŀ��Բ������κβ���
	'noTrustee' => 1,//��ֹ�����˲���
);
#-----------------

$oA = new cls_member($_init);

backnav('account','bind');

$oA->TopHead();//�ļ�ͷ��

$oA->TopAllow();//��������Ȩ��

# ��QQ������΢��
$oA->additem('openid_sinauid');//openid
#-----------------


//($title,$url)��url�пɲ�ָ��mchid��mid
$oA->fm_header("���ҵ�ͬ���ʺ�",'#');

$oA->fm_items();

//�����submitcheck(��ť����)��ͬ��ֵ
$oA->fm_footer();

//��Ա���ģ�������ʽ($str,$type)��$str���������Ա���İ�����ʶ��ֱ�ӵ��ı����ݣ�$typeĬ��Ϊ0ֱ����ʾ���ݣ�tip-�����ص���ʾ��fix-�̶�����ʾ��
$oA->fm_guide_bm('memberbind','0');

