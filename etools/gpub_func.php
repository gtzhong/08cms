<?php
include_once _08_INCLUDE_PATH."adminm.fun.php";

/*
�οͷ���(guest_publish) ��Դ: 
1. ��һЩ�����tpl�Ƶ��������ģ���ļ��зźܶ�php����
2. �ֻ���,��ҳ�湲��һЩ����
3. ע�⣺������ˣ�����ҳ �������ɾ�̬��
 - init
 - form
 - save
*/

$forward = cls_Parse::Get('_da.forward');
empty($forward) && $forward = M_REFERER;
$aid = cls_Parse::Get('_da.aid');

// ��ʼ��:[��ҳ/�ֻ�]�湲��
//if(empty($actdo)){
	$actdo = empty($actdo) ? "" : $actdo; //null,save,
	$caid = empty($caid) ? "" : $caid;
	$action = empty($action) ? "chushou" : $action;
	if(!empty($ismob)){ //�ֻ��淢��
		if(!in_array($caid,array('3','4'))) cls_message::show('��������!');
		$chid = $caid==3 ? 3 : 2;
		$names = array('3'=>'���ַ�','4'=>'����'); 
	}else{ 
		$chids = array('chushou'=>3,'chuzu'=>2);
		$caids = array('chushou'=>3,'chuzu'=>4);
		$names = array('chushou'=>'���ַ�','chuzu'=>'����');
		if(!in_array($action,array('chushou','chuzu'))) cls_message::show('��������!');
		$chid = $chids[$action];
		$caid = $caids[$action];
	}

	cls_env::SetG('chid',$chid);
	cls_env::SetG('caid',$caid);
	
	$isadd = $actdo=='edit' ? 0 : 1;
	if($aid && $ismob){ 
		$curuser = cls_UserMain::CurUser();
		$arc = new cls_arcedit;
		$arc->set_aid($aid,array('au'=>0,'ch'=>1));
		$data = $arc->archive;
		if($data['caid']!=$caid || $data['mid']!=$curuser->info['mid']){
			cls_message::show("��������[aid=$aid]! ");
		}
		$actname = '�༭';
		$f2dis = cls_env::mconfig('fcdisabled2');
		$f3dis = cls_env::mconfig('fcdisabled3');
	}else{
		$actname = '����';	
	}
	
	$mchid = empty($curuser->info['mchid']) ? 0 : $curuser->info['mchid'];
	if(in_array($mchid,array(1,2))){ // ��ͨ��Ա�뾭���˽����Ա���ķ���
		if(empty($ismob)){
			header("location:{$cms_abs}adminm.php?action={$action}add");
		#}else{
			#cls_message::show('�����ֻ����οͷ�����Դ��','');		
		}
	}elseif(!empty($close_gpub)){
		cls_message::show('������Դ����ע���Ϊ��ͨ��Ա�򾭼��ˣ�','');	
	}elseif(!empty($mchid)){
		$curuser->info['mid'] = 0;
	}
	
	if ( empty($ck_plugins_enable) )
	{
		$ck_ = new _08House_Archive(); 
		// ����CKҪ�����Ĳ����ע����ֵ��CK���������ͬ������ö��ŷָ�����������ýű�ʱ��̳���ȥ
		$ck_plugins_enable = ""; //{$ck_->__ck_plot_pigure},{$ck_->__ck_size_chart}
		cls_env::SetG('ck_plugins_enable',$ck_plugins_enable);
		unset($ck_);
	}
	
	$oA = new cls_archive();
	$oA->isadd = $isadd;
	//$oA->message("��������췢��<span$style>�޶�����</span>,�����ٷ�����Դ��");

	$oA->read_data();
	resetCoids($oA->coids, array(9,19)); 
	
	/* ����ǰ�Ĵ���ļ���,�ڲ��ֶ��ƴ����У���ֱ��ʹ���������� */
	$chid = &$oA->chid;
	$arc = &$oA->arc;
	$channel = &$oA->channel;
	$fields = &$oA->fields;
	$oA->fields['content']['mode'] = 1;
	
	// 
	$count_gpub = cls_env::mconfig('count_gpub'); //�οͷ�������
	$count_gpub = empty($count_gpub) ? 3 : $count_gpub;
	
	$exconfigs = cls_cache::cacRead('exconfigs',_08_EXTEND_SYSCACHE_PATH); 
	$fyvalid = empty($exconfigs['fanyuan']['fyvalid']) ? 30 : $exconfigs['fanyuan']['fyvalid']; //������Ч����
	$sms = new cls_sms();
	
//}

// ======== �ֻ���:�༭�ĵ� - ����
if(@$actdo=='edit'){ 

	//��Ŀ�����ɴ�$coids��array(1,2)
	$oA->sv_cns(array());
	//�ֶδ����ɴ�$nos��array('ename1','ename2')
	$oA->sv_fields(array());
	//
	$oA->sv_params(array('subjectstr'));
	// 
	#$oA->sv_fyext($fmdata,$chid);
	//�����ֶ�mchid����Ż�Ա��ģ��ID�������Ǹ��˷������Ǿ����˷���
	#$oA->arc->updatefield('mchid',$curuser->info['mchid']);	
	//ִ���Զ��������������ϱ��
	$oA->sv_update();
	//�ϴ�����
	$oA->sv_upload();
	//Ҫָ���ϼ�id������$pidkey���ϼ���Ŀ$arid
	$oA->sv_album('pid3',3);
	//�Զ����ɾ�̬
	$oA->sv_static();
	echo $forward;
	cls_message::show('�޸ĳɹ���',$forward);	
}

// ======== ����ĵ� - ����
if(@$actdo=='save'){  
	
	/*echo "<pre>:::\n";
	print_r($_POST['fmdata']['fythumb']);
	echo "\n\n fmdata:\n";
	print_r($fmdata['fythumb']);
	echo "\n\n _da.xx:\n";
	print_r(cls_env::GetG('fmdata.fythumb'));
	die('xxxx');*/
	
	$smskey = 'arcfypub'; $ckkey = 'smscode_'.$smskey; 
	if(empty($ismob) && $sms->smsEnable($smskey)){
		@$pass = smscode_pass($smskey,$msgcode,$fmdata['lxdh']);
		if(!$pass){
			cls_message::show('�ֻ�ȷ��������', M_REFERER);
		}
		msetcookie($ckkey, '', -3600);
		$tel_checked = 1;
	}else{ //�贫����֤�����ͣ�����Ĭ��Ϊ'archive' 
		$oA->sv_regcode("archive_fy");
		$tel_checked = 0;
	}
	
	//*/������������
	$style = " style='font-weight:bold;color:#F00'";
	$sql = "SELECT count(*) FROM {$tblprefix}".atbl($chid)." a INNER JOIN {$tblprefix}archives_$chid c ON c.aid=a.aid WHERE a.mid='0' AND c.lxdh='$fmdata[lxdh]' AND a.createdate>'".($timestamp-85400)."' ";
	$all_gpub = $db->result_one($sql); $all_gpub = empty($all_gpub) ? 0 : $all_gpub;
	if($all_gpub>=$count_gpub){
		$oA->message("��������췢��<span$style>�޶�����</span>,�����ٷ�����Դ��");
	}//*/
	
	if(!empty($ismob)){ //�ֻ���ǰ̨Ϊtext,��̨Ϊhtml
		$fmdata = &$GLOBALS[$oA->fmdata];
		$fmdata['content'] = nl2br($fmdata['content']);
	}
	//���ʱԤ������Ŀ���ɴ�$coids��array(1,2)
	$oA->sv_pre_cns(array());
	
	//����Ȩ�ޣ����Ȩ�޻��̨����Ȩ��
	//$oA->sv_allow();
	
	//����һ���ĵ�
	//if(!$oA->sv_addarc()){ 
	empty($oA->arc) && $oA->arc = new cls_arcedit;
	$add_aid = $oA->aid = $oA->arc->arcadd($oA->chid,$oA->predata['caid']);
	if(!$oA->aid){ 
		//���ʧ�ܴ���
		$oA->sv_fail();
	} 
	
	//��Ŀ�����ɴ�$coids��array(1,2)
	$oA->sv_cns(array());
	
	//�ֶδ����ɴ�$nos��array('ename1','ename2')
	$oA->sv_fields(array());
	
	//��ѡ��array('jumpurl','ucid','createdate','clicks','arctpls','customurl','dpmid','relate_ids',)
	//����������������̨Ĭ��Ϊarray('createdate','clicks','jumpurl','customurl','relate_ids')����Ա����Ĭ��Ϊarray('jumpurl','ucid')
	$oA->sv_params(array('createdate','enddate',));
	
	$oA->arc->updatefield('enddate',$timestamp+$fyvalid*86400); //�����ϼ�
	// - �οͷ�������Ҫ���
	//$oA->sv_fyext();
	
	// �ֻ�������֤��Ĭ�����
	$tel_checked && $oA->arc->updatefield('checked',$tel_checked);

	//��Ч��
	$oA->sv_enddate();

	//�����ֶ�mchid����Ż�Ա��ģ��ID�������Ǹ��˷������Ǿ����˷���
	$oA->arc->updatefield('mchid',@$curuser->info['mchid']); 
	
	$oA->sv_update();
	
	//�ϴ�����
	#$oA->sv_upload();
	
	//Ҫָ���ϼ�id������$pidkey���ϼ���Ŀ$arid
	$oA->sv_album('pid3',3);
	
	//����ͼƬ
	$fmdata['fythumb'] = cls_env::GetG('fmdata.fythumb'); 
	$imgscfg = array('chid'=>121,'caid'=>623,'pid'=>$add_aid,'arid'=>38,);
	$imgscfg['props'] = array(1=>'subject',2=>'lx');
	$oA->sv_images2arcs($fmdata,'thumb',$imgscfg,'fythumb');
	
	$oA->sv_fyext($fmdata,$chid);
	//�Զ����ɾ�̬
	$oA->sv_static();
	
	//����ʱ��Ҫ�����񣬰����Զ����ɾ�̬��������¼���ɹ���ʾ
	//$oA->sv_finish();
	
	$curuser = cls_UserMain::CurUser();
	$checked = $curuser->pmautocheck($channel['autocheck']);
	$cmsg = ($checked || $tel_checked) ? "����Ϣ�Ѿ���ϵͳ<span style='color:green;'>�Զ����</span>��" : "<br>����Ϣ<span style='color:red;'>��Ҫ����Ա���</span>������ǰ̨��ʾ"; 

	if(empty($ismob)){
		cls_message::show("{$names[$action]} �����ɣ�$cmsg",array('[����]'=>"?fid=111&action=$action"));
	}else{
		cls_message::show("{$names[$caid]} �����ɣ�$cmsg",array('[����]'=>"?caid=$caid&addno=$addno"));	
	}
	//mclearcookie($ckkey);

}

//�ֻ���-�����html
function form_item($cfg,$val=''){
	$a_field = new cls_field;
	$a_field->init($cfg,$val); //$a_field->isadd = 0;
	$varr = $a_field->varr('fmdata','addtitle');
	unset($a_field); //print_r($varr);
	return @$varr['frmcell'];
}

//��ҳ��-�οͷ�����
function form_page($oA,$caid,$action,$sms){ 
	
	$channel = &$oA->channel;
	$chid = $oA->chid;
	$fmdata = $oA->fmdata;
	$fields = &$oA->fields;
	// ǰ̨����
	//$oA->fm_header("$channel[cname] - ��������","info.php?fid=111&action=$action");
	//trhidden('tel_checked','');
	trhidden('actdo','save');	
	trhidden('fmdata[caid]',$caid);		
	$oA->fm_clpmc(1,1); // С������ 'lpmc'
	$oA->fm_chuxing(); // ���� ѡ���ֶ�
	$oA->fm_rccid1(); // ����-��Ȧ1,2,
	$oA->fm_rccid3(); // ����-վ��3,14
	if($chid==2) $oA->fm_czumode(); // ���޷�ʽ,���ʽ
	$oA->fm_cprice(); // ���,�۸�
	$oA->fm_footer();
	
	$oA->fm_header('��������');
	$oA->fm_fields(array('subject')); //����
	$oA->fm_fields(array('address','dt'),0); //��ַ/��ͼ
	$oA->fm_ctypes(); // ���/����(fwjg-���ݽṹ,zxcd-װ�޳̶�,cx-����,fl-����)
	$oA->fm_clouceng(); // ¥��/¥��,
	$oA->fm_fields(array('louxing')); // ¥��
	$oA->fm_ccids(array('12')); //(��ҵ����)
	$oA->fm_fields(array('fwpt')); // ¥��

	$oA->fm_fields(array('content'));
	// echo form_item($fields['content'],'-aavvbb-'); ������޸ľ�������
	
	$fythumb = $fields['thumb'];
	$fythumb['cname'] = '��ԴͼƬ';  
	$fythumb['ename'] = 'fythumb';   
	$fythumb['datatype'] = 'images'; 
	$fythumb['issearch'] = '0'; //ͼƬ����2:0-�ر�,1-����
	//$fythumb['imgComment'] = ''; //title_for_prop2
	$fythumb['min'] = '0';
	$fythumb['max'] = '2';
	$fythumb['guide'] = '';
	$fields['fythumb'] = $fythumb;

	$oA->fm_fields(array('fythumb'));
	
	$oA->fm_footer();
	
	$oA->fm_header('��������');			
	$oA->fm_fields(array('lxdh','xingming'),0);  //��ϵ��,������Ϣ
	
	/* ǰ̨����
	if(!$sms->isClosed()){
		echo '<tr><td width="150px" class="item1"><b><font color="red"> * </font>��֤��</b></td><td class="item2">';
		echo '<span id="alert_msgcode" style="color:red"></span>';
		echo '<input  type="text" size="20" id="msgcode" name="msgcode" value="" rule="text" must="1" mode="" regx="/^\s*\d{6}\s*$/" min="" offset="2" max="" rev="ȷ����"><a href="javascript:" onclick="sendCerCode(\'fmdata[lxdh]\',\'1\');"> ��������ȷ���롿</a>';
		echo '</td></tr>';
	}else{
		$oA->fm_regcode('archive_fy');
	}
	$oA->fm_footer('bsubmit','ȷ��������');
	//*/
	$oA->fm_fyext(); //��չ��js,��������-ȫѡ
	$oA->fm_jsSetImgtype('fythumb');

}

?>