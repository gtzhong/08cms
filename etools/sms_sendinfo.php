<?
header('Content-Type: application/x-javascript; charset=$mcharset');
include_once dirname(dirname(__FILE__)).'/include/general.inc.php';
include_once M_ROOT."./include/adminm.fun.php";
$sms = new cls_sms();

$mod = empty($mod) ? '.null.' : $mod;
$act = empty($act) ? 'init' : $act;
$nostr = empty($nostr) ? '' : $nostr; //��Ҫ:document.write
// ����js������modsendsms_falg �ж��Ƿ���ʾ�����͵��ֻ�����ش��룻
if($act=='init'){
	
	/*
	if(cmod('modsendsms')){ // ��ѷ���Ϣ���ֻ�ģ��-�ر�
		die("var modsendsms_falg = 'set_close';");
	}elseif(empty($sms_cfg_api) || ($sms_cfg_api == '(close)')){ // �ֻ����Žӿ�-�ر�
		die("var modsendsms_falg = 'api_close';");
	*/
//var_dump($mod);
	if(!$sms->smsEnable($mod)){
		die("var modsendsms_falg = 'set_close';");
	}else{
		echo "var modsendsms_falg = 'can_send';\n"; // id='vcode'
		//$rep = "<a id='vcode_rep' style='color:#CCC; display:none'><span id='vcode_rep_in'>60</span>������»�ȡ</a> ";
        echo "$('#sendtophone').show();";
?>
//<script>

$('.btn-phone').click(function() {
    sClose = $.modal({
		modalType:'modal'
		,title:'���Ͷ���'
        ,lock:0
        ,target:{
            type:'element'
            ,content:'#pop-phone'
        }
        ,border:0
	})
})
function jeFind(e,tag,type) {
	var f;
	if(type=='prev') f = e.previousSibling;
	else if(type=='next') f = e.nextSibling;
	else f = e.parentNode; 
	try{
		while(f.nodeType==3){
			if(type=='prev') f = f.previousSibling;
			if(type=='next') f = f.nextSibling;
		}
	}catch(ex){ return null; }
	if(f.tagName.toLowerCase()==tag) return f;
	else return jeFind(f,tag,type);
}
/*����¥����Ϣ���ֻ� �ύ*/
function sendmSubmit(fm){
    if(!(/^(1\d{10})$/.test(fm.mob.value))){
        $.modal.tip("�ֻ���ʽ����!��:13800001234",'warn');
		return false;
	}

	var msg = $('#sendm_msg').text(); 
    msg = msg.replace(/^\s+|\s+$/g,"")
	msg = encodeURIComponent(msg); //alert(msg);
    $.get(CMS_ABS+'etools/sms_sendinfo.php'+'?act=send&domain=' + document.domain + '&mod=<?php echo $mod; ?>&mob='+fm.mob.value+'&msg='+msg+'',function(s){
		if(s=='OK!'){
                sClose.closeBox();
                $.modal.tip("���ͳɹ�",'succede');

		}else{
            $.modal.tip("����ʧ��",'error');
		}
	});	
	return false;
}
function sendmCode(ea,repid){
	var fm = jeFind(ea,'form','');
    var mobo = fm.mob; //$id(fmid).mob; //alert(mobo.value); 
	var aj, tmp, step = 1; 
	if(mobo.value.length<10) return layer.msg("�ֻ������ʽ����",1);
	if(!mobo.value.match(/^\d{3,4}[-]?\d{7,8}$/)) return layer.msg("�ֻ������ʽ����",1);
    
    var mobid = mobo.id.toString(); //alert(mobid);
	var ckname = ((typeof($ckpre)=="undefined") ? '_fix_sendmCode_' : $ckpre)+'_'+mobid.replace('[','').replace(']','')+'_'; //console.log(mobid);
	var ckval = parseInt(getcookie(ckname)); //console.log(ckname+':'+ckval);
	if(ckval>0){
		return alert('�벻Ҫ�ظ��ύ�������ĵȴ���');
	}

	$.get(CMS_ABS+'etools/sms_sendinfo.php?act=code&domain=' + document.domain + '&mobile='+mobo.value+'&__rnd='+(new Date).getTime(), function(info){
        
		if(!info.text){
			var now = new Date(); var nowTime = now.getTime();
			setcookie(ckname, 12321, 60*1000);
			alert('ȷ�����ѷ��͵����ֻ�����ע����ա�');
            if(!repid) repid = 'vcode';
			sendDelay(repid);
		}else{ //������Ϣ
			alert(info.text);
		}
        
	});

}

// sendDelay��ʱ���ã�
// (ids)ID�淶��id:ԭʼID,id_rep:�滻��ID,id_rep_in�滻ID�ڵļ���, html�������£�
// <a id="tel_code" href="javascript:" onclick="sendCerCode('$varname','$mctid');">��������ȷ���롿</a>
// <a id="tel_code_rep" style="color:#CCC; display:none"><span id="tel_code_rep_in">60</span>������»�ȡ</a> 
function sendDelay(id){    
	org = $('.'+id); 
	rep = $('.'+id+'_rep');
	rin = $('.'+id+'_rep_in'); //console.log(rin.html());
	sec = parseInt(rin.html()); //rin.innerHTML
	if(sec>0){ //console.log('xxxy1');
		org.hide(); 
		rep.show(); 
		var tmp = rin.html(); //innerHTML--;
        rin.html(parseInt(tmp)-1);
		setTimeout("sendDelay('"+id+"')",1000);
	}else{ //console.log('xxxy2');
		rin.html(60);
        org.show(); 
		rep.hide();
	}    
}


<?php
	}
	//if(!cmod('modoldsms')){	
}elseif($act=='code'){
	
	$info = array();
	$mobile = empty($mobile) ? "" : $mobile;
	//if($option == 'msgcode'){
		if(strlen($mobile)<10){
			$info = array(
				'time' => 0,
				'text' => '�ֻ������ʽ����'
			);
		}elseif(preg_match("/^\d{3,4}[-]?\d{7,8}$/", $mobile)){
			$msgcode = cls_string::Random(6, 1);
			if(empty($sms_cfg_api) || ($sms_cfg_api == '(close)')){
				$info = array(
					'time' => -1,
					'text' => 'ϵͳû�����ö��Žӿ�ƽ̨!'
				);
			}else{
				@list($inittime, $initcode) = maddslashes(explode("\t", @authcode($m_cookie['08cms_msgmcode'],'DECODE')),1);
				if(($timestamp - $inittime) > 60){

					$content = $sms->smsTpl('commtpl');
					$content = str_replace(array('%s','{$smscode}'), $msgcode, $content);
					$msg = $sms->sendSMS($mobile,$content,'ctel');

					if($msg[0]==1){
						msetcookie('08cms_msgmcode', authcode("$timestamp\t$msgcode", 'ENCODE'));
					}else{
						$info = array(
							'time' => -1,
							'text' => '������Ϣʧ�ܣ�����ϵ����Ա��'
						);
					}
				}else{
					$info = array(
						'time' => 1,
						'text' => '�벻Ҫ�ظ��ύ���ȴ�ϵͳ��Ӧ'
					);
				}
			}
		}else{
			$info = array(
				'time' => 0,
				'text' => '�ֻ������ʽ����'
			);
		}
	//}
	cls_message::ajax_info($info);
	break;
	
}elseif($act=='send'){
	$mob = empty($mob) ? '' : $mob;
	$msg = empty($msg) ? '' : cls_string::iconv('utf-8',$mcharset,$msg);
	$msg = str_replace(array("\t","\r","\n","  "),array("","","","��"),$msg);
	//if(empty($sms_cfg_api) || ($sms_cfg_api == '(close)')){ // �ֻ����Žӿ�-�ر�
	if(!$sms->smsEnable($mod)){ 
		die('�ù����Ѿ��ر�!');
	}
	if(!empty($mob)){
			$msg = $sms->sendSMS($mob,$msg,'sadm');	
			//die("var sInfo = 'OK!'");
			die('OK!');
	}else{
		die('��������!');	
	}
}

die();

?>
