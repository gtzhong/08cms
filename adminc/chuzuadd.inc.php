<?PHP
$chid = isset($chid) ? max(0,intval($chid)) : 2;
$caid = isset($caid) ? max(0,intval($caid)) : 4;


cls_env::SetG('chid',$chid);
cls_env::SetG('caid',$caid);

$oA = new cls_archive();
//0Ϊ����༭��1Ϊ�ĵ����
$isadd = $oA->isadd;

//�ļ�ͷ��
$oA->top_head();

$type = 'chuzu';
$isadd && backnav($type,'czfabu');

/* ��ȡ���п������ϣ���ģ�͡��ֶΡ����ĵ� */
$oA->read_data();

$ispid4 = empty($ispid4) ? 0 : 1; // ispid4����ж�Ϊ�����͹�˾�鿴���¾����˷�Դ��ش���
if($ispid4){ //�ҵ��þ��͹�˾�����о�����    
    //��ǰ�û��Ƿ���Ȩ�޲鿴/�޸��ĵ�
    hasPermissionCheckHouse($curuser,$oA);
}else{ /* ��Ա����ֻ�ܱ༭���˷������ĵ� */
	$oA->allow_self(); 
}

/* �������������ϵ������������������ϵ */
resetCoids($oA->coids, array(9,19)); 

if($isadd){ 
    //��ԱҪ��д�˱���Ļ�Ա��Ϣ���������ֻ���֤���ֻ�����ͨ����֤���ܷ�����Դ
    publishAfterCheckUserInfo($curuser,$chid);
    
	// �������ⷿԴ�޶���ƣ�
    $returnInfo = publishLimit($curuser,$chid,$oA);
    if(!empty($returnInfo['limitMessageStr'])) $oA->message($returnInfo['limitMessageStr']);
}

/* ����ǰ�Ĵ���ļ���,�ڲ��ֶ��ƴ����У���ֱ��ʹ���������� */
$chid = &$oA->chid;
$arc = &$oA->arc;
$channel = &$oA->channel;
$fields = &$oA->fields;
#-----------------

if(!submitcheck('bsubmit')){

	if($isadd){//��Ӳ���Ҫ
		$oA->fm_pre_cns();
		$oA->fm_guide_bm(empty($returnInfo['message'])?'':$returnInfo['message'],'fix'); 
		$oA->fm_phpSetImgtype($fields); //��ͼ����Ϊ��ͼ	
	}
	//������ǰ��Ա��Ȩ��
	$oA->fm_allow();
	
	$oA->fm_header("$channel[cname] - ��������","?action=$action&chid=$chid&ispid4=$ispid4");
	//$oA->fm_album('pid3'); //����ϼ�����ָ���ϼ�id������������Ĭ��Ϊpid
	$oA->fm_caid(array('hidden' => 1)); //������Ŀ��
    $oA->fm_clpmc(); // С������ 'lpmc'
    $oA->fm_chuxing(); // ���� ѡ���ֶ�
	$oA->fm_rccid1(); // ����-��Ȧ1,2,
	$oA->fm_rccid3(); // ����-վ��3,14
	$oA->fm_czumode(); // ���޷�ʽ,���ʽ
	$oA->fm_cprice(); // ���,�۸�
	$oA->fm_footer();
	
	$oA->fm_header('��������');
	$oA->fm_fields(array('subject')); //����
	$oA->fm_fields(array('address','dt'),0); //��ַ/��ͼ
	$oA->fm_ctypes(); // ���/����(fwjg-���ݽṹ,zxcd-װ�޳̶�,cx-����,fl-����)
	$oA->fm_clouceng(); // ¥��/¥��,
	$oA->fm_fields(array('louxing')); // ¥��
	$oA->fm_ccids($oA->coids); //������ϵ(��ҵ����)
	
	$skip1 = array('content','fythumb'); //ͼ����Ϣ array(content,thumb) +����ͼ,С��ͼ
	$skip2 = array('lxdh','xingming','fdname','fdtel','fdnote'); //��ϵ��,������Ϣ
	$skip3 = array('keywords','abstract'); //�ؼ��֣�ժҪ  
	$oA->fields_did[] = 'thumb';
	$oA->fm_fields_other(array_merge($skip1,$skip2,$skip3)); //����ʣ�����Ч�ֶΣ����Դ����ų��ֶ�$nos
	$oA->fm_fields($skip1,0); // ͼ����Ϣ
	$oA->fm_footer();
	
	$oA->fm_header('��������');
	$oA->fm_cfanddong(array('lxdh','xingming'));
	$oA->fm_fields(array('keywords','abstract'),0);
	$oA->fm_fields($skip2,0); //������Ϣ
	//��ѡ��array('jumpurl','ucid','createdate','clicks','arctpls','customurl','dpmid')
	$oA->fm_params(array('ucid','subjectstr'));
	if($isadd){ //�贫����֤�����ͣ�����Ĭ��Ϊ'archive'
		$oA->fm_regcode('archive');
		$oA->fm_footer('bsubmit','��������');
		$oA->fm_jsSetImgtype('fythumb');	//js����ͼƬ�����չ
	}else{
		$oA->fm_footer('bsubmit');
	}
	$oA->fm_fyext(); //��չ��js,��������-ȫѡ
	
	$oA->fm_guide_bm('���ʷ�Դ��׼����Դ��������4��������ͼ + ���� + ���� + ¥��+ 30�������ϵķ�Դ�������������ʷ�Դ�������Եõ��ر�ӷ֡�','fix');
	
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
		if(!$add_aid=$oA->sv_addarc()){
			//���ʧ�ܴ���
			$oA->sv_fail();
		}
	}
	
	//��Ŀ�����ɴ�$coids��array(1,2)
	$oA->sv_cns(array());

	//�ֶδ����ɴ�$nos��array('ename1','ename2')
	$oA->sv_fields(array());
	
	$oA->sv_params(array('ucid','subjectstr'));
	
	if($isadd){ 
		if($sendtype){
			//$oA->arc->setend(-1); //�ϼ�
			$oA->sv_enddate();
		}else{
			$oA->arc->setend(0);//�¼�
		}
	}
	
	//�����ֶ�mchid����Ż�Ա��ģ��ID�������Ǹ��˷������Ǿ����˷���
	$oA->arc->updatefield('mchid',$curuser->info['mchid']);	
	
	//ִ���Զ��������������ϱ��
	$oA->sv_update();
	
	//�ϴ����� (���ʱͼƬ���������£�fythumb�д���)
	if(!$isadd) $oA->sv_upload(); 

	//Ҫָ���ϼ�id������$pidkey���ϼ���Ŀ$arid
	$oA->sv_album('pid3',3);

	if($isadd){
		//����ͼƬ
		$fmdata['fythumb'] = cls_env::GetG('fmdata.fythumb'); 
		$imgscfg = array('chid'=>121,'caid'=>623,'pid'=>$add_aid,'arid'=>38,);
		$imgscfg['props'] = array(1=>'subject',2=>'lx');
		$mre = $oA->sv_images2arcs($fmdata,'thumb',$imgscfg,'fythumb');
		$db->update('#__'.atbl($chid), array('thumb' => $mre[1]))->where("aid = $add_aid")->exec();
	}
	
	$oA->sv_fyext($fmdata,$chid);
	//�Զ����ɾ�̬
	$oA->sv_static();
		
	//����ʱ��Ҫ�����񣬰����Զ����ɾ�̬��������¼���ɹ���ʾ
	$oA->sv_finish();
}
?>

