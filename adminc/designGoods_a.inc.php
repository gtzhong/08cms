<?PHP
$chid = 103; $caid = 513;

cls_env::SetG('chid',$chid);
cls_env::SetG('caid',$caid);

$oA = new cls_archive();

//0Ϊ����༭��1Ϊ�ĵ����
$isadd = $oA->isadd;

//�ļ�ͷ��
$oA->top_head();
$isadd && backnav('designGoods','add');

/* ��ȡ���п������ϣ���ģ�͡��ֶΡ����ĵ� */
$oA->read_data();

$oA->allow_self(); 

/* �������������ϵ������������������ϵ */
//$oA->setvar('coids',array(19,31));
resetCoids($oA->coids, array(42)); 
//print_r($oA->coids);

$style = " style='font-weight:bold;color:#F00'"; $valid_msg = "";
//������������
$exconfigs = cls_cache::cacRead('exconfigs',_08_EXTEND_SYSCACHE_PATH);
$exconfigs = $exconfigs['sjsendrules'][$curuser->info['grouptype32']][$chid];
//if($curuser->info['grouptype31']) $exconfigs = $exconfigs['gssendrules'][$curuser->info['grouptype31']][$chid];
//if($curuser->info['grouptype32']) $exconfigs = $exconfigs['sjsendrules'][$curuser->info['grouptype32']][$chid];

$ntotal = cls_DbOther::ArcLimitCount($chid, '');
empty($exconfigs['total']) && $exconfigs['total'] = 999999;




if($isadd){ 
	
	//print_r($curuser);
	//$curuser->info['companynm'] = '';
	if(empty($curuser->info['companynm'])){
		 m_guide("mchid".$curuser->info['mchid']."_note",'fix');
		 die();
	}
	// �޶���ƣ�
	if(!empty($exconfigs['total']) && $exconfigs['total'] <= $ntotal){
		$oA->message("����<span$style>�޶�����</span>,�����ٷ�����Ϣ��<br>���ķ����޶�Ϊ��<span$style>$exconfigs[total]</span> ��");
	}

}

/* ����ǰ�Ĵ���ļ���,�ڲ��ֶ��ƴ����У���ֱ��ʹ���������� */
$chid = &$oA->chid;
$arc = &$oA->arc;
$channel = &$oA->channel;
$fields = &$oA->fields;
#-----------------

if(!submitcheck('bsubmit')){
	
	echo "<script type='text/javascript'>ck_edit_config.plugins_disable='08cms_paging_management';</script>";
	if($isadd){//��Ӳ���Ҫ
		$oA->fm_pre_cns();
		$madd_msg = $oA->getmtips(array('check'=>1,'limit'=>array($exconfigs['total'],$ntotal)),'');
		$oA->fm_guide_bm($madd_msg,'fix'); 
		
	}
	//������ǰ��Ա��Ȩ��
	$oA->fm_allow();
	
	//($title,$url)��url�пɲ�ָ��chid��aid
	$oA->fm_header("","?action=$action");
	
	//����ϼ�����ָ���ϼ�id������������Ĭ��Ϊpid
	$oA->fm_album('pid');
	
	//������Ŀ��ͨ���������飬��ָ���ر��չʾ������array('topid' => 5,'hidden' => 1)��
	$oA->fm_caid(array('hidden' => 1));
	
	//($coids)��������࣬$coids��array(3,4,5)
	
	$oA->fm_fields(array('subject'),0);
	$oA->fm_ccids($oA->coids);
	$oA->fm_fields(array('spxh','spbm','sppp','zj'),0);
	$oA->fm_fields(array('thumb','tupian','intro'),1);	
	$oA->fm_ccids(array(19));
	$oA->fm_footer();
	
	$oA->fm_header("ͼ����Ϣ");
	$oA->fm_fields(array('thumb','tupian','intro'),0);  
	//��ѡ��array('jumpurl','ucid','createdate','clicks','arctpls','customurl','dpmid')
	//չʾ�������������̨Ĭ��Ϊarray('createdate')����Ա����Ĭ��Ϊarray('ucid')
	$oA->fm_params(array());
	
	//����ʣ�����Ч�ֶΣ����Դ����ų��ֶ�$nos
	//$oA->fm_fields_other(array());
	
	if($isadd){
		//�贫����֤�����ͣ�����Ĭ��Ϊ'archive'
		$oA->fm_regcode('archive');
	}
	
	//�����submitcheck(��ť����)��ͬ��ֵ
	$oA->fm_footer('bsubmit');
	
	//�����̨��������ʽ($str,$type)��$typeĬ��Ϊ0ʱ$strΪ���������ǣ�1��ʾ$strΪ�ı�����
	//��Ա���ģ�������ʽ($str,$type)��$str���������Ա���İ�����ʶ��ֱ�ӵ��ı����ݣ�$typeĬ��Ϊ0ֱ����ʾ���ݣ�tip-�����ص���ʾ��fix-�̶�����ʾ��
	$oA->fm_guide_bm('','fix');
	
}else{

	if($isadd){
		//�贫����֤�����ͣ�����Ĭ��Ϊ'archive'
		$oA->sv_regcode("archive");
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
	//����������������̨Ĭ��Ϊarray('createdate')����Ա����Ĭ��Ϊarray('ucid')
	$oA->sv_params(array());
	
	//ִ���Զ��������������ϱ��
	$oA->sv_update();
	
	//�ϴ�����
	$oA->sv_upload();

	//Ҫָ���ϼ�id������$pidkey���ϼ���Ŀ$arid
	//$oA->sv_album('pid3',3);
	
	//�Զ����ɾ�̬
	$oA->sv_static();
	
	//����ʱ��Ҫ�����񣬰����Զ����ɾ�̬��������¼���ɹ���ʾ
	$oA->sv_finish();
}
?>

