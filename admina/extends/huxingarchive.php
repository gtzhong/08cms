<?PHP
$chid = 11;//ָ��chid
$caid = 11;
$arid = empty($arid) ? 3 : max(3,intval($arid));//�����ⲿ��arid����Ҫ��������

cls_env::SetG('chid',$chid);
cls_env::SetG('caid',$caid);
cls_env::SetG('arid',$arid);

$oA = new cls_archive();
$isadd = $oA->isadd; /* 0Ϊ����༭��1Ϊ�ĵ����ϵ */

$oA->top_head();//�ļ�ͷ��
/* ��ȡ���п������ϣ���ģ�͡��ֶΡ����ĵ� */
$oA->read_data();

$pchid = 4; //���ʱ-ѡ�������ϼ�(¥��)

/* �������������ϵ������������������ϵ */
$oA->setvar('coids',array(1,12));

/* ����ǰ�Ĵ���ļ���,�ڲ��ֶ��ƴ����У���ֱ��ʹ���������� */
$chid = &$oA->chid;
$arc = &$oA->arc;
$channel = &$oA->channel;
$fields = &$oA->fields;

#-----------------
if(!submitcheck('bsubmit')){
	
	if($isadd){//��Ӳ���Ҫ
		$oA->fm_pre_cns(); //���ʱԤ������Ŀ
		$todiqu=$oA->fm_find_album();
		$oA->predata['ccid1']=$todiqu['ccid1'];
	}
	
	$oA->fm_allow(); //������ǰ��Ա��Ȩ��	
	$oA->fm_header("","?entry=extend$extend_str&arid=$arid"); //($title,$url)��url�пɲ�ָ��chid��aid
    $oA->fm_ccids(array(1));
	$oA->fm_album('pid'); //����ϼ�����ָ���ϼ�id������������Ĭ��Ϊpid
    $oA->fm_caid(array('hidden' => 1)); //������Ŀ��ͨ���������飬��ָ���ر��չʾ������array('topid' => 5,'hidden' => 1)��
	$oA->fm_fields(array('thumb','tujis'),0);
	$oA->fm_fields(array('subject'));
	$oA->fm_chuxing(array(),1); // ���� ѡ���ֶ�
	$oA->fm_ccids(array(12)); //($coids)��������࣬$coids��array(3,4,5)
	$oA->fm_fields(array('abstract'),1); //($arr,$noinc)��$arr�ֶα�ʶ���飬Ϊ���������У�$noinc=1�ų�ģʽ
	$oA->fm_fields(array('abstract'));
	$oA->fm_fields(array(),0); //����ʣ�����Ч�ֶΣ����Դ����ų��ֶ�$nos
	
	$oA->fm_footer('bsubmit');
	
	$oA->fm_guide_bm('','0');
	
}else{	

	if($isadd){
		$oA->sv_regcode('archive');
		$oA->sv_pre_cns(array());
		
	}
	$oA->sv_allow();
	if($isadd){
		if(!$oA->sv_addarc()){
			$oA->sv_fail();
		}
	}
	$oA->sv_cns(array());
	$oA->sv_fields(array());
	$oA->sv_params(array());
	$oA->sv_param('arctpls');
	$oA->sv_update();
	$oA->sv_upload();
	//Ҫָ���ϼ�id������$pidkey���ϼ���Ŀ$arid
	$oA->sv_album('pid',$arid);
	$oA->sv_static();
	$oA->sv_finish();

}
?>
