<?php
!defined('M_COM') && exit('No Permission');

$ngtid = 14;
$nmchid = array(1,2,13);
$nugid = 8;
$ncoid = 9;
$nccid = 204;
$nchids = array(2,3,107,117,118,119,120);

if(!in_array($curuser->info['mchid'],$nmchid)) cls_message::show('��Ȩ��ʹ�ñ����ܣ�');
$exconfigs = cls_cache::cacRead('exconfigs',_08_EXTEND_SYSCACHE_PATH);
if(!($rules = @$exconfigs['zding'])) cls_message::show('ϵͳû���ö�����');

$arc = new cls_arcedit;
$arc->set_aid($aid,array('au'=>0));
!$arc->aid && cls_message::show('ѡ���ĵ�');
$arc->archive['mid'] == $memberid || cls_message::show('��ֻ���ö����ѷ����ķ�Դ��');
$chid = $arc->archive['chid'];
if(!in_array($chid,$nchids) || !$arc->archive['checked']) cls_message::show('ֻ���ö�����ķ�Դ��');

$forward = empty($forward) ? M_REFERER : $forward;
$forwardstr = '&forward='.urlencode($forward);
if(!submitcheck('bsubmit')){
	tabheader("��Դ�ö�-{$arc->archive['subject']}","{$action}newform","?action=$action$forwardstr&aid=$aid",2,0,1);
	trbasic('��ǰ�ö�״̬','',$arc->archive["ccid$ncoid"] == $nccid ? "<font color=\"#FF0000\">�ö�<font>" : 'δ�ö�','');
	trbasic('�ö�����ʱ��','',$arc->archive["ccid{$ncoid}date"] ? date('Y-m-d H:i',$arc->archive["ccid{$ncoid}date"]) : '-','');
	trbasic('��Դ�ö��������','',$curuser->info['freezds'].' ��','');
	trbasic('�ֽ��ʻ����','',$curuser->info['currency0']."Ԫ &nbsp; &nbsp;<a href=\"?action=payonline\" target=\"_blank\">>>����֧��</a>",'');
	if(($arc->archive["ccid$ncoid"] == $nccid) && !$arc->archive["ccid{$ncoid}date"]){
		trbasic('�ö�˵��','','����Դ�������ö���','');
	}else{
		$str = "�����ӵ�з�Դ�ö�������ʹ�ô������ö������۳���<br>";
		$str .= "�ö�һ�β������� $rules[minday] �죬��Դ�ö�ÿ�� $rules[price] Ԫ��<br>";
		$str .= "�������Դ�����ö���Դ����Ϊ���ڡ�<br>";
		trbasic('�ö�˵��','',$str,'');
		trbasic('��Դ�ö�����','zdingday','','text',array('w' => 10,'validate' => makesubmitstr('zdingday',1,0,$rules['minday'],'','int')));
		trbasic('�����ö�Ӧ�۷���','',"<div id='payment_instr'>-</div>",'');
		tabfooter('bsubmit');
	}

}else{
	$zdingday = empty($zdingday) ? 0 : max(0,intval($zdingday));
	if($zdingday < $rules['minday']) cls_message::show("���������������� $rules[minday] �졣",M_REFERER);
	$needfreezds = 0;$needcurrency0 = $zdingday * $rules['price'];
	if($curuser->info['freezds']){
		$needfreezds = min($curuser->info['freezds'],$zdingday);
		$needcurrency0 -= $needfreezds * $rules['price'];
	}
	if($curuser->info['currency0'] < $needcurrency0) cls_message::show('�����ֽ��ʻ����㣬���ֵ��',M_REFERER);
	$curuser->updatefield('freezds',$curuser->info['freezds'] - $needfreezds);
	$curuser->updatecrids(array(0 => -$needcurrency0),1,'����Դ�ö���');
	
	$arc->updatefield("ccid{$ncoid}date",($arc->archive["ccid{$ncoid}"] == $nccid ? $arc->archive["ccid{$ncoid}date"] : $timestamp) + $zdingday * 86400);
	$arc->updatefield("ccid$ncoid",$nccid);
	if($arc->archive["enddate"] && $arc->archive["enddate"] < $arc->archive["ccid{$ncoid}date"]) $arc->updatefield("enddate",$arc->archive["ccid{$ncoid}date"]);
	$arc->updatedb();
	cls_message::show('��Դ�ö��ɹ���',axaction(6,$forward));
}

		//����jq��
		echo cls_phpToJavascript::loadJQuery();
	?>
<script type="text/javascript">
var zd_day = <?php echo empty($curuser->info['freezds'])?0:$curuser->info['freezds'];?>;
var cash = <?php echo empty($curuser->info['currency0'])?0:$curuser->info['currency0'];?>;
var pay_each_day = <?php echo empty($rules['price'])?0:$rules['price'];?>;
$("#zdingday").keyup(function(){
	var pay_instr = '';
	if(isNaN($("#zdingday").val())){
		pay_instr = "<font color=\"#FF0000\">��Դ�ö�����Ӧ��������</font>";
	}else{		
		val = $("#zdingday").val();
		if(zd_day - val >=0){
			pay_instr = 'Ӧ���ö�����' + val + '�죬�ö��������Ϊ' + (zd_day - val) + '��';
		}else{			
			shou_pay = (val - zd_day) * pay_each_day;//����Ҫ�����ֽ�����
			pay_instr = 'Ӧ���ö�����' + zd_day + '�죬�ö��������Ϊ0,Ӧ���ֽ�' + shou_pay + 'Ԫ��ʣ���ֽ�' + (cash - shou_pay) + 'Ԫ��';
			if(cash - shou_pay < 0 ){			
				pay_instr = pay_instr  + "<br/><font color=\"#FF0000\">���ò�����֧�����β��������ֵ�������²�����</font>";
			}
		}
	}

	$("#payment_instr").html(pay_instr);
})
	
</script>
