<?PHP
/*
** ������̨�ű����������ĵ�����������༭�����������߽ű�����������ű���ȥ������ר�ò��ֵĴ���
** ��ͨ��url����$chid���ɻ������ݲ�ͬģ�͵��ĵ�����
*/
/* ������ʼ������ */
 $chid = 5;//ָ��chid
 $caid = 5;
 cls_env::SetG('chid',$chid);
 cls_env::SetG('caid',$caid);
#-----------------

$oA = new cls_archive();

/* 0Ϊ����༭��1Ϊ�ĵ�����ϵ */
$isadd = $oA->isadd;

$oA->top_head();//�ļ�ͷ��

$pchid = 4; //����ʱ-ѡ�������ϼ�(¥��)

/* ��ȡ���п������ϣ���ģ�͡��ֶΡ����ĵ� */
$oA->read_data();

/* ���ñ���������������������Ĭ��Ϊfmdata */
//$oA->setvar('fmdata','archivenew');

/* ����������������ϵ������������������ϵ */
//$oA->setvar('coids',array(2,3,4));

/* ����ǰ�Ĵ���ļ���,�ڲ��ֶ��ƴ����У���ֱ��ʹ���������� */
$chid = &$oA->chid;
$arc = &$oA->arc;
$channel = &$oA->channel;
$fields = &$oA->fields;
#-----------------


if(!submitcheck('bsubmit')){
	
	if($isadd){//���Ӳ���Ҫ
		//����ʱԤ������Ŀ
		$oA->fm_pre_cns();
	}
	
	//������ǰ��Ա��Ȩ��
	$oA->fm_allow();
	
	//($title,$url)��url�пɲ�ָ��chid��aid
	$oA->fm_header("","?entry=extend$extend_str");
	
	//�����ϼ�����ָ���ϼ�id������������Ĭ��Ϊpid
	$oA->fm_album('pid',0,"ajax=exarc_list"); 
	$oA->fm_ccids(array('1'));
	//������Ŀ��ͨ���������飬��ָ���ر��չʾ������array('topid' => 5,'hidden' => 1)��
	$oA->fm_caid(array('hidden' => 1));
	$oA->fm_fields(array('subject','keywords','abstract'),0);
	
	//($coids)���������࣬$coids��array(3,4,5)
	$oA->fm_ccids();
	$oA->fm_fields(array('hdadr'),0);
	$oA->fm_fields(array('hdtel','ksnum','scj','tgj','hdnum','content'),1);
	
	$oA->fm_footer();
	
	//($title)��$title�ֶ����ñ���
	$oA->fm_header('�Ź���Ϣ');
	$oA->fm_fields(array('hdtel','ksnum','scj','tgj','hdnum'),0);
	$oA->fm_enddate();//����ʱ��
	$oA->fm_footer();
	
	//($title)��$title�ֶ����ñ���
	$oA->fm_header('ͼ����Ϣ');
	$oA->fm_fields(array('content'));
	
	$oA->fm_footer();
	
	//($title)��$title�ֶ����ñ���
	$oA->fm_header('��չ����','',array('hidden'=>1));
	
	//����ʣ�����Ч�ֶΣ����Դ����ų��ֶ�$nos
	//$oA->fm_fields_other(array());
	
	//��ѡ��array('jumpurl','ucid','createdate','clicks','arctpls','customurl','dpmid','relate_ids')
	//չʾ��������������̨Ĭ��Ϊarray('createdate','clicks','jumpurl','customurl','relate_ids')����Ա����Ĭ��Ϊarray('jumpurl','ucid')
	$oA->fm_params(array('createdate','clicks','jumpurl','customurl','relate_ids','arctpls'));
	
	//�����submitcheck(��ť����)��ͬ��ֵ
	$oA->fm_footer('bsubmit');
	
	//������̨��������ʽ($str,$type)��$typeĬ��Ϊ0ʱ$strΪ���������ǣ�1��ʾ$strΪ�ı�����
	//��Ա���ģ�������ʽ($str,$type)��$str���������Ա���İ�����ʶ��ֱ�ӵ��ı����ݣ�$typeĬ��Ϊ0ֱ����ʾ���ݣ�tip-�����ص���ʾ��fix-�̶�����ʾ��
	$oA->fm_guide_bm('','0');
	
}else{
	/*
	** ע�⣺���ݴ�����ͬ��Ҫ�ϸ�ָ����Щ����Ҫ�������ֶλ���ϵ!
	** 
	** 
	*/
	if($isadd){
		//�贫����֤�����ͣ�����Ĭ��Ϊ'archive'
		$oA->sv_regcode('archive');
		
		//����ʱԤ������Ŀ���ɴ�$coids��array(1,2)
		$oA->sv_pre_cns(array());
		
	}
	
	//����Ȩ�ޣ�����Ȩ�޻��̨����Ȩ��
	$oA->sv_allow();

	if($isadd){
		//����һ���ĵ�
		if(!$oA->sv_addarc()){
			//����ʧ�ܴ���
			$oA->sv_fail();
		}
	}	
	
	//��Ŀ�������ɴ�$coids��array(1,2)
	$oA->sv_cns(array());
	
	//�ֶδ������ɴ�$nos��array('ename1','ename2')
	$oA->sv_fields(array());
	
	//��ѡ��array('jumpurl','ucid','createdate','clicks','arctpls','customurl','dpmid','relate_ids',)
	//������������������̨Ĭ��Ϊarray('createdate','clicks','jumpurl','customurl','relate_ids')����Ա����Ĭ��Ϊarray('jumpurl','ucid')
	$oA->sv_params(array('enddate','createdate','clicks','jumpurl','customurl','relate_ids','arctpls'));
	
	//ִ���Զ��������������ϱ��
	
	$oA->sv_update();
	
	//�ϴ�����
	$oA->sv_upload();
	
	//Ҫָ���ϼ�id������$pidkey���ϼ���Ŀ$arid
	$oA->sv_album('pid',3);
	
	//�Զ����ɾ�̬
	$oA->sv_static();
	
	//����ʱ��Ҫ�����񣬰����Զ����ɾ�̬��������¼���ɹ���ʾ
	$oA->sv_finish();
}
?>