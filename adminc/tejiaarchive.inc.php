<?PHP

/* ������ʼ������ */
$chid = 107;//ָ��chid
$caid = 559;
$pchid = 4;
$arid = 1;

cls_env::SetG('chid',$chid);
cls_env::SetG('caid',$caid);
cls_env::SetG('pchid',$pchid);
cls_env::SetG('arid',$arid);

#-----------------

$oA = new cls_archive();

//0Ϊ����༭��1Ϊ�ĵ����
$isadd = $oA->isadd;

//�ļ�ͷ��
$oA->top_head();


/* ��ȡ���п������ϣ���ģ�͡��ֶΡ����ĵ� */
$oA->read_data();
$pchid = 4; //���ʱ-ѡ�������ϼ�(¥��)
/* ��Ա����ֻ�ܱ༭���˷������ĵ� */
$oA->allow_self();

/* ���ñ�������������������Ĭ��Ϊfmdata */
//$oA->setvar('fmdata','archivenew');

/* �������������ϵ������������������ϵ */
$oA->setvar('coids',array(1,2,3,14,18));

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
		backnav('tejia','add');
		$oA->fm_pre_cns();
	}
	
	//������ǰ��Ա��Ȩ��
	$oA->fm_allow();
	
	//($title,$url)��url�пɲ�ָ��chid��aid
	$oA->fm_header("�ؼ۷� - ������Ϣ","?action=$action");
	if($isadd){ $oA->fm_mylpSelect(); } //@$arc->archive['xxx']
	//echo '<input type="hidden" id="fmdata[pid]" name="fmdata[pid]" value="589868">';
	
	//����
	$oA->fm_fields(array('subject','keywords','abstract'),0);
	
	//������Ŀ��ͨ���������飬��ָ���ر��չʾ������array('topid' => 5,'hidden' => 1)��
	$oA->fm_caid(array('hidden' => 1));
	
	//($coids)��������࣬$coids��array(3,4,5)
	//$oA->fm_ccids();
	$oA->fm_rccid1(); // 1,2,
	$oA->fm_rccid3(); // 3,14
	$oA->fm_fields(array('lh','fh'));	
	$oA->fm_footer();	

	
	//($title)��$title�ֶ����ñ���
	$oA->fm_header('��������');
	//�ܼۡ����������
	$oA->fm_cprice();	
	$oA->fm_chuxing(); // ���� ѡ���ֶ�
	$oA->fm_clouceng(); // ¥�����: (2��һ���)
	$oA->fm_fields(array('louxing',),0); // ¥��,���ݽṹ	
	$oA->fm_fields(array('zxcd','cx'));
	$oA->fm_ccids(array('18')); 	
	//($arr,$noinc)��$arr�ֶα�ʶ���飬Ϊ���������У�$noinc=1�ų�ģʽ
	
	
	$oA->fm_footer();
	
	//($title)��$title�ֶ����ñ���
	$oA->fm_header('����');
	$oA->fm_fields(array(),0);
	
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
	
	$oA->sv_enddate();//�ϼ�
	/*
	if($isadd){ 
		if($sendtype){
			$oA->sv_enddate();//�ϼ�
		}else{
			$oA->arc->setend(0);//�¼�
		}
	}*/
	
	//ִ���Զ��������������ϱ��
	$oA->sv_update();
	
	//�ϴ�����
	$oA->sv_upload();

	//Ҫָ���ϼ�id������$pidkey���ϼ���Ŀ$arid
	if($isadd){ 
		$oA->sv_album('pid',1);
	}
	
	//�Զ����ɾ�̬
	$oA->sv_static();
	
	//����ʱ��Ҫ�����񣬰����Զ����ɾ�̬��������¼���ɹ���ʾ
	$oA->sv_finish();
}
?>

