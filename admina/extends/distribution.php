<?PHP
/*
** �����̨�ű����������ĵ����������༭�����������߽ű�����������ű���ȥ�����ר�ò��ֵĴ���
** ��ͨ��url����$chid���ɻ������ݲ�ͬģ�͵��ĵ�����
*/
/* ������ʼ������ */
$chid = 113;//ָ��chid
$arid = 33;
$pchid = 4; //���ʱ-ѡ�������ϼ�(¥��)

cls_env::SetG('chid',$chid);
cls_env::SetG('arid',$arid);

$oA = new cls_archive();

/* 0Ϊ����༭��1Ϊ�ĵ����ϵ */
$isadd = $oA->isadd;

$oA->top_head();//�ļ�ͷ��

/* ��ȡ���п������ϣ���ģ�͡��ֶΡ����ĵ� */
$oA->read_data();

/* ����ǰ�Ĵ���ļ���,�ڲ��ֶ��ƴ����У���ֱ��ʹ���������� */
$chid = &$oA->chid;
$arc = &$oA->arc;
$channel = &$oA->channel;
$fields = &$oA->fields;
#-----------------

if(!submitcheck('bsubmit')){
	
	if($isadd){//��Ӳ���Ҫ
		//���ʱԤ������Ŀ
		$oA->fm_pre_cns();
	}
	
	//������ǰ��Ա��Ȩ��
	$oA->fm_allow();
	
	//($title,$url)��url�пɲ�ָ��chid��aid
	$oA->fm_header("¥�̷���-�������","?entry=extend$extend_str");
	
	//������Ŀ��ͨ���������飬��ָ���ر��չʾ������array('topid' => 5,'hidden' => 1)��
	$oA->lpfx_to_building(); // С������ 'lpmc'
	//$oA->fm_album('pid');
	$oA->fm_caid(array('hidden'=>1));	
	$oA->fm_fields(array('subject'),0);
	$oA->fm_fields(array('keywords'),0);
	$oA->fm_fields(array('abstract'),0);
	$oA->fm_fields(array('thumb'),0);	
	$oA->fm_ccids(array());
	$oA->fm_footer();
	
	//($title)��$title�ֶ����ñ���
	$oA->fm_header('¥�̷���-����');
	$oA->fm_fields(array('kprq'),0);
    $oA->fm_enddate('���������ʱ��');//����ʱ��
	$oA->fm_fields(array('yhsm'),0);
	$oA->fm_fields(array('yj'),0);
	$oA->fm_fields(array('tel'),0);
	$oA->fm_fields(array('yds'),0);
	$oA->fm_fields(array('tjs'),0);
	$oA->fm_fields(array('deal'),0);
	$oA->fm_fields(array(),0);	
	$oA->fm_footer();	

	$oA->fm_header('��չ����','',array('hidden'=>1));	
	$oA->fm_params(array());	
	$oA->fm_footer('bsubmit');
	$oA->fm_guide_bm('','0');
}else{
	if($isadd){
		//�贫����֤�����ͣ�����Ĭ��Ϊ'archive'
		$oA->sv_regcode('archive');
		//���ʱԤ������Ŀ���ɴ�$coids��array(1,2)
		$oA->sv_pre_cns(array());
		if(empty(${$oA->fmdata}['pid33'])) $oA->message("��ѡ����Ч��¥��",M_REFERER);
	}
	
	//����Ȩ�ޣ����Ȩ�޻��̨����Ȩ��
	$oA->sv_allow();
	
	if($isadd){
		//����һ���ĵ�
		if(!$oA->sv_addarc()){
			//���ʧ�ܴ���
			$oA->sv_fail();
		}
	}
	
	//��Ŀ�����ɴ�$coids��array(1,2)
	$oA->sv_cns(array());
	
	//�ֶδ����ɴ�$nos��array('ename1','ename2')
	$oA->sv_fields(array());
	
	//��ѡ��array('jumpurl','ucid','createdate','clicks','arctpls','customurl','dpmid','relate_ids',)
	//����������������̨Ĭ��Ϊarray('createdate','clicks','jumpurl','customurl','relate_ids')����Ա����Ĭ��Ϊarray('jumpurl','ucid')
	$oA->sv_params(array('enddate','createdate','clicks','jumpurl','customurl','relate_ids'));
	$oA->sv_param('arctpls');
	
	//ִ���Զ��������������ϱ��
	$oA->sv_update();
	
	//�ϴ�����
	$oA->sv_upload();
	
	//Ҫָ���ϼ�id������$pidkey���ϼ���Ŀ$arid
	$oA->sv_album('pid33',33);
	
	//�Զ����ɾ�̬
	$oA->sv_static();
	
	//����ʱ��Ҫ�����񣬰����Զ����ɾ�̬��������¼���ɹ���ʾ
	$oA->sv_finish();
}
?>
