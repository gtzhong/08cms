<?PHP
$chid = 7;//ָ��chid
$caid = 7;
cls_env::SetG('chid',$chid);
cls_env::SetG('caid',$caid);

//Ҫָ���ϼ�id������$pidkey���ϼ���Ŀ$arid
if(!empty($pid)){
    //$_pid = @$pid || $fmdata['pid'];
    $_arc = new cls_arcedit; //��ҵ�ز�-�ϼ�����
    $_arc->set_aid($pid,array('au'=>0,'ch'=>0)); 
    $arid = @$_arc->archive['chid']==4 ? 3 : 36;//ָ���ϼ���Ŀid
}else{
    $arid = 0;
    $pid = 0;
} 
cls_env::SetG('arid',$arid);
cls_env::SetG('pid',$pid);

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
		$fields['thumb']['datatype'] = 'images';
		$oA->fm_pre_cns();
	}
	
	//������ǰ��Ա��Ȩ��
	$oA->fm_allow();
	
	//($title,$url)��url�пɲ�ָ��chid��aid
	$oA->fm_header("","?action=$action&arid=$arid&pid=$pid");
	
	//����ϼ�����ָ���ϼ�id������������Ĭ��Ϊpid
	$oA->fm_album('pid');
	
	//������Ŀ��ͨ���������飬��ָ���ر��չʾ������array('topid' => 5,'hidden' => 1)��
	$oA->fm_caid(array('hidden' => 0,'ids'=>7));
	
	//($coids)��������࣬$coids��array(3,4,5)
	$oA->fm_ccids(array());
	
	//$oA->fm_footer();
	
	//($title)��$title�ֶ����ñ���
	//$oA->fm_header('��������');
	
	//($arr,$noinc)��$arr�ֶα�ʶ���飬Ϊ���������У�$noinc=1�ų�ģʽ
	$oA->fm_fields(array(),0);
	
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
	$oA->fm_guide_bm('','0');
	
}else{
	$fmdata = &$GLOBALS['fmdata'];
	if(!$isadd){ // Edit
		$oA->sv_allow(); //����Ȩ�ޣ����Ȩ�޻��̨����Ȩ��
		$oA->sv_cns(array()); //��Ŀ�����ɴ�$coids��array(1,2)
		$oA->sv_fields(array()); //�ֶδ����ɴ�$nos��array('ename1','ename2')
		$oA->sv_update();
		$oA->sv_upload(); //�ϴ�����
		//$oA->sv_album('pid',$arid); //Ҫָ���ϼ�id������$pidkey���ϼ���Ŀ$arid
		$oA->sv_static(); //�Զ����ɾ�̬
		$oA->sv_finish(); //����ʱ��Ҫ�����񣬰����Զ����ɾ�̬��������¼���ɹ���ʾ
	}else{ // add		
		$oA->sv_regcode('archive'); 
		$oA->sv_pre_cns(array()); 
		$oA->sv_allow();
		if(!empty($fmdata['thumb'])){			
			$_s = str_replace("\r","\n",$fmdata['thumb']);
			$_a = explode("\n",$fmdata['thumb']);
			foreach($_a as $fmdata['thumb']){
			if(!empty($fmdata['thumb'])){
				if(!$oA->sv_addarc()){
					$_msg[] = $oA->sv_fail(1); 
				}
				$_msg[] = $oA->sv_cns(array(),1); 
				$_msg[] = $oA->sv_fields(array(),1); 
				$_pic = str_replace(array('##'," ","\t","\r","\n"),'',$fmdata['thumb']);
				//echo "($_pic)".strlen($_pic);
				$oA->arc->updatefield('thumb',$_pic,$fields['thumb']['tbl']);
				$oA->sv_update();
				$oA->sv_upload($_pic); //echo ":$arid,";
				$oA->sv_album('pid',$arid); 
				$oA->sv_static(); 
				unset($oA->arc); // ??? 
			}} //die();
			$oA->message("�ĵ�������",axaction(6,M_REFERER));
		}else{
			$oA->message('����');	
		}
	}
	
}
?>

