<?PHP
$chid = isset($chid) ? intval($chid) : 117;
$caid = isset($caid) ? intval($caid) : 613;

in_array($chid,array(117, 118)) || cls_message::show("����ȷָ��ģ�ͣ�",M_REFERER);

cls_env::SetG('chid',$chid);
cls_env::SetG('caid',$caid);

$oA = new cls_archive();
//0Ϊ����༭��1Ϊ�ĵ�����
$isadd = $oA->isadd;

//�ļ�ͷ��
$oA->top_head();
switch($chid){
    case 117:
        $type = 'bussell_office';
        break;
    case 118:
        $type = 'bussell_shop';
        break;
    default:
        $type = 'bussell_office';
        break;
}
$isadd && backnav($type,'ershoufabu');

/* ��ȡ���п������ϣ���ģ�͡��ֶΡ����ĵ� */
$oA->read_data();

$ispid4 = empty($ispid4) ? 0 : 1; // ispid4����ж�Ϊ�����͹�˾�鿴���¾����˷�Դ��ش���
if($ispid4){ //�ҵ��þ��͹�˾�����о�����
    //��ǰ�û��Ƿ���Ȩ�޲鿴/�޸��ĵ�
    hasPermissionCheckHouse($curuser,$oA);
}else{ /* ��Ա����ֻ�ܱ༭���˷������ĵ� */
	$oA->allow_self(); 
}

/* ����������������ϵ������������������ϵ */
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

	if($isadd){//���Ӳ���Ҫ
		$oA->fm_pre_cns();	
		$oA->fm_guide_bm(empty($returnInfo['message'])?'':$returnInfo['message'],'fix'); 		
	}
	//������ǰ��Ա��Ȩ��
	$oA->fm_allow();
	
	$oA->fm_header("$channel[cname] - ��������","?action=$action&chid=$chid&ispid4=$ispid4");	
	$oA->fm_caid(array('hidden' => 1)); //������Ŀ��
    $oA->fm_ulpmc(); // С������ 'lpmc'
// 	if(in_array($chid,array(2,3))) $oA->fm_chuxing(); // ���� ѡ���ֶ�
	$oA->fm_rccid1(); // ����-��Ȧ1,2,
	$oA->fm_rccid3(); // ����-վ��3,14	
	$oA->fm_cprice(); // ���,�۸�
	$oA->fm_footer();
	
	$oA->fm_header('��������');
	$oA->fm_fields(array('subject')); //����
	$oA->fm_fields(array('address','dt'),0); //��ַ/��ͼ
	$oA->fm_ctypes(); // ���/����(fwjg-���ݽṹ,zxcd-װ�޳̶�,cx-����,fl-����)
	$oA->fm_clouceng(); // ¥��/¥��,
	$oA->fm_fields(array('louxing')); // ¥��
	$oA->fm_ccids($oA->coids); //������ϵ(��ҵ����)
	
	$skip1 = array('content','thumb'); //ͼ����Ϣ array(content,thumb) +����ͼ,С��ͼ
	$skip2 = array('lxdh','xingming'); //��ϵ��,������Ϣ //'fdname','fdtel','fdnote'
	$skip3 = array('keywords','abstract'); //�ؼ��֣�ժҪ  
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
	}else{
		$oA->fm_footer('bsubmit');
	}
	
	$oA->fm_fyext(); //��չ��js,��������-ȫѡ
	
	
}else{	
	if($isadd){
		//�贫����֤�����ͣ�����Ĭ��Ϊ'archive'
		$oA->sv_regcode("archive");  
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
	
	$oA->sv_params(array('ucid','subjectstr'));
	
	if($isadd){
		if($sendtype){
			//$oA->arc->setend(-1); //�ϼ�
			$oA->sv_enddate();
		}else{
			$oA->arc->setend(0);//�¼�
		}
	}
	
	// 
	$oA->sv_fyext($fmdata);
	
	//�����ֶ�mchid����Ż�Ա��ģ��ID�������Ǹ��˷������Ǿ����˷���
	$oA->arc->updatefield('mchid',$curuser->info['mchid']);	
	
	//ִ���Զ��������������ϱ��
	$oA->sv_update();
	
	//�ϴ�����
	$oA->sv_upload();

	//Ҫָ���ϼ�id������$pidkey���ϼ���Ŀ$arid
    $pidkey = 36;
	$oA->sv_album('pid'.$pidkey,$pidkey);
	
	//�Զ����ɾ�̬
	$oA->sv_static();
	
	//����ʱ��Ҫ�����񣬰����Զ����ɾ�̬��������¼���ɹ���ʾ
	$oA->sv_finish();
}
?>
