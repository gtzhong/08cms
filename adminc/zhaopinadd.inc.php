<?
$chid = 108; $caid =558;

cls_env::SetG('chid',$chid);
cls_env::SetG('caid',$caid);
#-----------------

$oA = new cls_archive();

//0Ϊ����༭��1Ϊ�ĵ����
$isadd = $oA->isadd;

//�ļ�ͷ��
$oA->top_head();

/* ��ȡ���п������ϣ���ģ�͡��ֶΡ����ĵ� */
$oA->read_data();

/* ��Ա����ֻ�ܱ༭���˷������ĵ� */
$oA->allow_self();

/* ���ñ�������������������Ĭ��Ϊfmdata */
//$oA->setvar('fmdata','archivenew');

/* �������������ϵ������������������ϵ */
//$oA->setvar('coids',array(2,3,4));

$style = " style='font-weight:bold;color:#F00'"; $valid_msg = "";
//������������
$exconfigs = cls_cache::cacRead('exconfigs',_08_EXTEND_SYSCACHE_PATH);
/*if(empty($curuser->info['grouptype14'])){
	$exconfigs = $exconfigs['fanyuan'][0];
}else{
	$exconfigs = $exconfigs['fanyuan'][$curuser->info['grouptype14']];
}*/

if($isadd){ 
	if(empty($curuser->info['lxdh'])){
		 m_guide(in_array($curuser->info['mchid'],array(2,3)) ? "mchidx_cert" : "mchidx_info",'fix');
		 die();
	}

}

/* ����ǰ�Ĵ���ļ���,�ڲ��ֶ��ƴ����У���ֱ��ʹ���������� */
$chid = &$oA->chid;
$arc = &$oA->arc;
$channel = &$oA->channel;
$fields = &$oA->fields;
#-----------------

if(!submitcheck('bsubmit')){
	
	
	if($isadd){//��Ӳ���Ҫ
		//���ʱԤ������Ŀ
		//��Ա����������ʾ��Ϣ,����a,b��ʽ��limit,valid���ݣ����ȼ������
		//a: $madd_msg = $oA->getmtips(array('check'=>1,'limit'=>array($rules['total'],$total),'valid'=>array($rules['valid'],$valid),),'');
		//   $oA->fm_guide_bm("madd_ch02",'fix'); //madd_ch02������ռλ����{$madd_msg},��$madd_msg���Զ��ӵ�madd_ch02��ȥ��
		//b: $msg = $oA->getmtips(array('check'=>1,'limit'=>array($rules['total'],$total),),'');
		//   $oA->fm_guide_bm($msg,'fix');
		backnav('zhaopin','fubuzp');
		$oA->fm_pre_cns();
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
	$oA->fm_ccids(array(1));

	//($arr,$noinc)��$arr�ֶα�ʶ���飬Ϊ���������У�$noinc=1�ų�ģʽ
	$fext = array('telphone','fabuzhe');
	$skip = array('keywords','abstract');
	$oA->fm_fields(array_merge($fext,$skip),1);
	$oA->fm_cfanddong($fext);
	$oA->fm_fields(array('keywords','abstract'),0);
	
	//��ѡ��array('jumpurl','ucid','createdate','clicks','arctpls','customurl','dpmid')
	//չʾ�������������̨Ĭ��Ϊarray('createdate')����Ա����Ĭ��Ϊarray('ucid')
	$oA->fm_params(array());
	
	//����ʣ�����Ч�ֶΣ����Դ����ų��ֶ�$nos
	//$oA->fm_fields_other(array());
	
	if($isadd){
		//�贫����֤�����ͣ�����Ĭ��Ϊ'archive'
		$oA->fm_regcode('archive');
		$oA->fm_footer('bsubmit','��������');
	}else{	
		//�����submitcheck(��ť����)��ͬ��ֵ
		$oA->fm_footer('bsubmit');
	}
	
	//�����̨��������ʽ($str,$type)��$typeĬ��Ϊ0ʱ$strΪ���������ǣ�1��ʾ$strΪ�ı�����
	//��Ա���ģ�������ʽ($str,$type)��$str���������Ա���İ�����ʶ��ֱ�ӵ��ı����ݣ�$typeĬ��Ϊ0ֱ����ʾ���ݣ�tip-�����ص���ʾ��fix-�̶�����ʾ��
	$oA->fm_guide_bm('','0');
	
}else{

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
	//����������������̨Ĭ��Ϊarray('createdate')����Ա����Ĭ��Ϊarray('ucid')
	$oA->sv_params(array());
	
	$oA->sv_enddate();
	//ִ���Զ��������������ϱ��
	$oA->sv_update();
	
	//�ϴ�����
	$oA->sv_upload();

	//Ҫָ���ϼ�id������$pidkey���ϼ���Ŀ$arid
	$oA->sv_album('pid',0);
	
	//�Զ����ɾ�̬
	$oA->sv_static();
	
	//����ʱ��Ҫ�����񣬰����Զ����ɾ�̬��������¼���ɹ���ʾ
	$oA->sv_finish();
}
?>


