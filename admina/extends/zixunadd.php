<?PHP
/*
** �����̨�ű����������ĵ����������༭�����������߽ű�����������ű���ȥ�����ר�ò��ֵĴ���
** ��ͨ��url����$chid���ɻ������ݲ�ͬģ�͵��ĵ�����
*/
/* ������ʼ������ */
 $chid = 1;//ָ��chid
 cls_env::SetG('chid',$chid);
#-----------------
$oA = new _08House_Archive();

# CK�������
$ck_plugins_enable = "{$oA->__ck_house_info},{$oA->__ck_paging_management}";

/* 0Ϊ����༭��1Ϊ�ĵ����ϵ */
$isadd = $oA->isadd;

$oA->top_head();//�ļ�ͷ��


/* ��ȡ���п������ϣ���ģ�͡��ֶΡ����ĵ� */
$oA->read_data();

/* ���ñ�������������������Ĭ��Ϊfmdata */
//$oA->setvar('fmdata','archivenew');

/* �������������ϵ������������������ϵ */
//$oA->setvar('coids',array(2,3,4));

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
	$oA->fm_header("","?entry=extend$extend_str");
	
	//����ϼ�����ָ���ϼ�id������������Ĭ��Ϊpid
	$oA->fm_album('pid');
	
	//������Ŀ��ͨ���������飬��ָ���ر��չʾ������array('topid' => 5,'hidden' => 1)��
	//��ģ�ͣ��ֲ�������������Ŀ��539-��ҵָ�ϣ�����Ϊ��Ѷ
	if($isadd){
		$oA->fm_caid();
	}elseif(cls_catalog::p_ccid($arc->archive['caid'])=='539'){
		$oA->fm_caid(array()); //'ids'=>array(539)
	}else{ // ��Ѷ
		$oA->fm_caid();
	}
	//
	
	//($coids)��������࣬$coids��array(3,4,5)
	$oA->fm_ccids(array());
	//��Ѷ�ϼ���¥��,��ʱɾ���ò���
    //if($isadd && empty($pid))$oA->fm_info_to_building();//¥������
	
	//($arr,$noinc)��$arr�ֶα�ʶ���飬Ϊ���������У�$noinc=1�ų�ģʽ
	$oA->fm_fields(array('subject','source','keywords','abstract','thumb','content'),0);
	$oA->fm_fields();
	
	
	$oA->fm_footer();
	
	//($title)��$title�ֶ����ñ���
	$oA->fm_header('��չ����','',array('hidden'=>1));
	
	//����ʣ�����Ч�ֶΣ����Դ����ų��ֶ�$nos
	//$oA->fm_fields_other(array());
	
	//��ѡ��array('jumpurl','ucid','createdate','clicks','arctpls','customurl','dpmid','relate_ids')
	//չʾ�������������̨Ĭ��Ϊarray('createdate','clicks','jumpurl','customurl','relate_ids')����Ա����Ĭ��Ϊarray('jumpurl','ucid')
	$oA->fm_params(array('createdate','clicks','arctpls','jumpurl','customurl','relate_ids'));
	
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
	$oA->sv_params(array('createdate','clicks','arctpls','jumpurl','customurl','relate_ids'));
	
	//ִ���Զ��������������ϱ��
	$oA->sv_update();
    
    //��Ѷ����¥�̲���,��ʱɾ���ò���
    //if($isadd && empty($pid))$oA->sv_info_to_building();
	
	//�ϴ�����
	$oA->sv_upload();
	
	//Ҫָ���ϼ�id������$pidkey���ϼ���Ŀ$arid
    $_arc = new cls_arcedit; //��ҵ�ز�-�ϼ�����
	if(!empty($fmdata['pid'])){
    	$_arc->set_aid($fmdata['pid'],array('au'=>0,'ch'=>0)); 
    	$_arid = $_arc->archive['chid']==4 ? 1 : 35;//ָ���ϼ���Ŀid
		$oA->sv_album('pid',$_arid); 
	}
	
	//�Զ����ɾ�̬
	$oA->sv_static();
	
	//����ʱ��Ҫ�����񣬰����Զ����ɾ�̬��������¼���ɹ���ʾ
	$oA->sv_finish();
}
?>
