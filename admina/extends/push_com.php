<?PHP
/*
** �����̨�ű����������������������༭
** ͨ��urlָ��paid��ָ��pushidΪ�༭������Ϊ���
*/

$oA = new cls_push();

$oA->top_head();//�ļ�ͷ��

/* Ԥ�����ϵ������Լ�Ȩ�� */
$oA->pre_check();

/* ����ʼ */
if(!submitcheck('bsubmit')){
	
	//($title,$url)��url�пɲ�ָ��paid��pushid
	$oA->fm_header("","?entry=extend$extend_str");
	
	//($arr,$noinc)��$arr�ֶα�ʶ���飬Ϊ���������У�$noinc=1�ų�ģʽ
	$oA->fm_fields(array(),0);
	
	$oA->fm_footer();
	
	//($title)��$title�ֶ����ñ���
	$oA->fm_header('��������');
	
	//չʾ���������
	//��ѡ��Ŀarray('startdate','enddate',)
	$oA->fm_params(array());
	
	
	//�����submitcheck(��ť����)��ͬ��ֵ
	$oA->fm_footer('bsubmit');
	
	//�����̨��������ʽ($str,$type)��$typeĬ��Ϊ0ʱ$strΪ���������ǣ�1��ʾ$strΪ�ı�����
	//��Ա���ģ�������ʽ($str,$type)��$str���������Ա���İ�����ʶ��ֱ�ӵ��ı����ݣ�$typeĬ��Ϊ0ֱ����ʾ���ݣ�tip-�����ص���ʾ��fix-�̶�����ʾ��
	$oA->fm_guide_bm('','0');
	
}else{
	
	//����Ȩ�ޣ����Ȩ�޻��̨����Ȩ��
	$oA->sv_allow();
	
	//�ֶδ����ɴ�$nos��array('ename1','ename2')
	$oA->sv_fields(array());
	
	//������������
	//��ѡ��Ŀarray('startdate','enddate','fixedorder',)
	$oA->sv_params(array());
	
	//ִ���Զ��������������ϱ��
	$oA->sv_update();
	
	//�ϴ�����
	$oA->sv_upload();
		
	//����ʱ��Ҫ�����񣬲�����¼���ɹ���ʾ
	$oA->sv_finish();
	
}
?>