<?php
include_once M_ROOT."./include/adminm.fun.php";
$tplurl = "{$cms_abs}template/{$templatedir}/"; 

$forward = empty($forward) ? M_REFERER : $forward;
$forwardstr = '&forward='.rawurlencode($forward);
$action = empty($action) ? "qiuzu" : $action;
$chids = array('qiugou'=>10,'qiuzu'=>9);
$caids = array('qiugou'=>10,'qiuzu'=>9);
$names = array('qiugou'=>'��','qiuzu'=>'����');


if(!in_array($action,array('qiugou','qiuzu'))) cls_Parse::Message('��������!');
$chid = $chids[$action];
$caid = $caids[$action];
cls_env::SetG('chid',$chid);
cls_env::SetG('caid',$caid);

$mchid = empty($curuser->info['mchid']) ? 0 : $curuser->info['mchid'];
if(in_array($mchid,array(1,2))){ // ��ͨ��Ա�뾭���˽����Ա���ķ���
	header("location:{$cms_abs}adminm.php?action=xuqiuarchive&chid=$chids[$action]");
}elseif(!empty($close_gpub)){
	cls_Parse::Message('����������ע���Ϊ��ͨ��Ա�򾭼��ˣ�','');	
}elseif(!empty($mchid)){
	$curuser->info['mid'] = 0;
}

$oA = new cls_archive();
$isadd = $oA->isadd = 1;
$oA->read_data();

/* ����ǰ�Ĵ���ļ���,�ڲ��ֶ��ƴ����У���ֱ��ʹ���������� */
$chid = &$oA->chid;
$arc = &$oA->arc;
$channel = &$oA->channel;
$fields = &$oA->fields;

$sms = new cls_sms();


if(submitcheck('bsubmit')){
	
	$smskey = 'arcxqpub'; $ckkey = 'smscode_'.$smskey; 
	if($sms->smsEnable($smskey)){
		@$pass = smscode_pass($smskey,$msgcode,$fmdata['lxdh']); 
		if(!$pass){
			cls_message::show('�ֻ�ȷ��������', M_REFERER);
		}
		msetcookie($ckkey, '', -3600);
		$tel_checked = 1;
	}else{ //�贫����֤�����ͣ�����Ĭ��Ϊ'archive' 
		$oA->sv_regcode("archive_xq");
		$tel_checked = 0;
	}
	
	//������������
	$style = " style='font-weight:bold;color:#F00'";
	$count_gpub = empty($count_gpub) ? 3 : $count_gpub;
	$validday = empty($validday) ? 30 : $validday;
	$sql = "SELECT count(*) FROM {$tblprefix}".atbl($chid)." a INNER JOIN {$tblprefix}archives_$chid c ON c.aid=a.aid WHERE a.mid='0' AND c.lxdh='$fmdata[lxdh]' AND a.createdate>'".($timestamp-85400)."' ";
	$all_gpub = $db->result_one($sql); $all_gpub = empty($all_gpub) ? 0 : $all_gpub;
	if($all_gpub>=$count_gpub){
		$oA->message("��������췢��<span$style>�޶�����</span>,�����ٷ�������");
	}
	
	//���ʱԤ������Ŀ���ɴ�$coids��array(1,2)
	$oA->sv_pre_cns(array());
   
	//����һ���ĵ�
	//if(!$oA->sv_addarc()){ 
	empty($oA->arc) && $oA->arc = new cls_arcedit;
	$oA->aid = $oA->arc->arcadd($oA->chid,$oA->predata['caid']);
	if(!$oA->aid){ 
		//���ʧ�ܴ���
		$oA->sv_fail();
	} 
//	die();
	//��Ŀ�����ɴ�$coids��array(1,2)
	$oA->sv_cns(array());

	//�ֶδ����ɴ�$nos��array('ename1','ename2')
	$oA->sv_fields(array());
	
	//��ѡ��array('jumpurl','ucid','createdate','clicks','arctpls','customurl','dpmid','relate_ids',)
	//����������������̨Ĭ��Ϊarray('createdate')����Ա����Ĭ��Ϊarray('ucid')
	$oA->sv_params(array());			

	// �ֻ�������֤��Ĭ�����
	$tel_checked && $oA->arc->updatefield('checked',$tel_checked);
	
	$oA->sv_enddate();
	//ִ���Զ��������������ϱ��
	$oA->sv_update();
	
	//�ϴ�����
	$oA->sv_upload();

	//Ҫָ���ϼ�id������$pidkey���ϼ���Ŀ$arid
	$oA->sv_album('pid3',3);
	
	//�Զ����ɾ�̬
	$oA->sv_static();
	
	//����ʱ��Ҫ�����񣬰����Զ����ɾ�̬��������¼���ɹ���ʾ
	//$oA->sv_finish();
	
	$curuser = cls_UserMain::CurUser();
	$checked = $curuser->pmautocheck($channel['autocheck']);
	$cmsg = ($checked || $tel_checked) ? "����Ϣ�Ѿ���ϵͳ<span style='color:green;'>�Զ����</span>��" : "<br>����Ϣ<span style='color:red;'>��Ҫ����Ա���</span>������ǰ̨��ʾ"; 
	//_tmp_sendok($cmsg,$action,$cms_abs,$tplurl);	
	if(empty($ismob)){
		cls_message::show("{$names[$action]} �����ɣ�$cmsg",array('[����]'=>"?fid=$fid&action=$action"));
	}else{
		cls_message::show("{$names[$caid]} �����ɣ�$cmsg",array('[����]'=>"?caid=$caid&addno=$addno"));	
	}
}





function addqzqg($oA,$caid,$chid,$action,$sms,$channel){		
		//$oA->fm_header("$channel[cname] - ����","?fid=112&action=$action&chid=$chid");		
		trhidden('fmdata[caid]',$caid);
		$oA->fm_fields(array('subject'),0);
		$oA->fm_ccids(array(1)); 
	
		$oA->fm_fields(array('mj','zj','jtyq'));
		$oA->fm_fields(array('lxdh','xingming'));
		$oA->fm_fields(array(),0);	
		
		/* ǰ̨����
		if(!$sms->isClosed()){        
    		echo '<tr><td width="150px" class="item1"><b><font color="red"> * </font>�ֻ�ȷ����</b></td><td class="item2">';
    		echo '<span id="alert_msgcode" style="color:red"></span>';
    		echo '<input  type="text" size="20" id="msgcode" name="msgcode" value="" rule="text" must="1" mode="" regx="/^\s*\d{6}\s*$/" min="" offset="2" max="" rev="ȷ����"><a href="javascript:" onclick="sendCerCode(\'fmdata[lxdh]\',\'1\');"> ��������ȷ���롿</a>';
    		echo '</td></tr>';
		}else{
			$oA->fm_regcode('archive_xq');
		}*/
	
		//$oA->fm_footer('bsubmit');	
}
?>