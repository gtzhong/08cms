<?PHP
/*
** �����̨�ű����������ĵ����������༭�����������߽ű�����������ű���ȥ�����ר�ò��ֵĴ���
** ��ͨ��url����$chid���ɻ������ݲ�ͬģ�͵��ĵ�����
*/
/* ������ʼ������ */
$chid = 111;//ָ��chid
#-----------------
$caid = 599;
cls_env::SetG('chid',$chid);
cls_env::SetG('caid',$caid);
$pid = empty($pid)?'':max(1,intval($pid));//��ʼ���ϼ�id���п���ʹ������id��ʽ����������$hejiid�ȣ�ҪתΪʹ��pid


$oA = new cls_archive();

/* 0Ϊ����༭��1Ϊ�ĵ����ϵ */
$isadd = $oA->isadd;
$pchid = 4; //���ʱ-ѡ�������ϼ�(¥��)
!empty($isadd) && empty($pid) && $pid = -1;//¥�̺ϼ������¥��ʱ$pidΪ����ֵ

$oA->top_head();//�ļ�ͷ��

/* ��ȡ���п������ϣ���ģ�͡��ֶΡ����ĵ� */
$oA->read_data();

/* ����ǰ�Ĵ���ļ���,�ڲ��ֶ��ƴ����У���ֱ��ʹ���������� */
$chid = &$oA->chid;
$arc = &$oA->arc;
$channel = &$oA->channel;
$fields = &$oA->fields;
#-----------------
$oA->fields_did[] = 'shapan';//��ʱ��������޸ı�ע����¥������
if(!submitcheck('bsubmit')){
	
	if($isadd){//��Ӳ���Ҫ
		//���ʱԤ������Ŀ
		$oA->fm_pre_cns();
	}
	
	//������ǰ��Ա��Ȩ��
	$oA->fm_allow();
	
	//($title,$url)��url�пɲ�ָ��chid��aid
	$oA->fm_header("��������","?entry=extend$extend_str&pid=$pid");
	
	//����ϼ�����ָ���ϼ�id������������Ĭ��Ϊpid
	if($pid)$oA->fm_album('pid');
	else $oA->fm_album('pid',0,"ajax=exarc_list"); 
	
	//������Ŀ��ͨ���������飬��ָ���ر��չʾ������array('topid' => 5,'hidden' => 1)��
	$oA->fm_caid(array('hidden' => 1));
	
	//($coids)��������࣬$coids��array(3,4,5)
	$oA->fm_ccids(array());	

	//($arr,$noinc)��$arr�ֶα�ʶ���飬Ϊ���������У�$noinc=1�ų�ģʽ
	$oA->fm_fields(array('subject'),0);
	$oA->fm_fields(array('unit','floor','hushu'),0);
	$oA->fm_fields(array('zxcd','xszt'),0);	
	$oA->fm_fields(array('sgjd','kpsj','jfsj'),0);
	$oA->fm_fields(array('shapan'),0);
	$oA->fm_fields(array('xkzh','dongtai'),0);
	$oA->fm_fields(array(),0);
	
	$oA->fm_footer();
	
	//($title)��$title�ֶ����ñ���
	$oA->fm_header('��չ����','',array('hidden'=>1));
	
	//��ѡ��array('jumpurl','ucid','createdate','clicks','arctpls','customurl','dpmid','relate_ids')
	//չʾ�������������̨Ĭ��Ϊarray('createdate','clicks','jumpurl','customurl','relate_ids')����Ա����Ĭ��Ϊarray('jumpurl','ucid')
	$oA->fm_params(array('createdate','clicks','jumpurl','customurl'));
	
	//�����submitcheck(��ť����)��ͬ��ֵ
	$oA->fm_footer('bsubmit');
	
	//�����̨��������ʽ($str,$type)��$typeĬ��Ϊ0ʱ$strΪ���������ǣ�1��ʾ$strΪ�ı�����
	//��Ա���ģ�������ʽ($str,$type)��$str���������Ա���İ�����ʶ��ֱ�ӵ��ı����ݣ�$typeĬ��Ϊ0ֱ����ʾ���ݣ�tip-�����ص���ʾ��fix-�̶�����ʾ��
	$oA->fm_guide_bm('','0');
	
}else{
	/*
	** ע�⣺���ݴ����ͬ��Ҫ�ϸ�ָ����Щ����Ҫ������ֶλ���ϵ!
	** 
	** 
	*/
	if($isadd){
		//�贫����֤�����ͣ�����Ĭ��Ϊ'archive'
		$oA->sv_regcode('archive');
		
		//���ʱԤ������Ŀ���ɴ�$coids��array(1,2)
		$oA->sv_pre_cns(array());
		
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
	$oA->sv_params(array('createdate','clicks','jumpurl','customurl'));
	//$oA->sv_param('arctpls');
	
	//ִ���Զ��������������ϱ��
	$oA->sv_update();
	
	//�ϴ�����
	$oA->sv_upload();
	
	//Ҫָ���ϼ�id������$pidkey���ϼ���Ŀ$arid
	//Ҫָ���ϼ�id������$pidkey���ϼ���Ŀ$arid
    $_arc = new cls_arcedit; //��ҵ�ز�-�ϼ�����
    $_arc->set_aid($fmdata['pid'],array('au'=>0,'ch'=>0)); 
    $_arid = $_arc->archive['chid']==4 ? 1 : 35;//ָ���ϼ���Ŀid
	$oA->sv_album('pid',$_arid); 
	//$oA->sv_album('pid',1);
	
	//�Զ����ɾ�̬
	$oA->sv_static();
	
	//����ʱ��Ҫ�����񣬰����Զ����ɾ�̬��������¼���ɹ���ʾ
	$oA->sv_finish();
}
?>
