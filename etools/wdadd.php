<?
include_once dirname(dirname(__FILE__)).'/include/general.inc.php';
include_once M_ROOT."./include/adminm.fun.php";
$catalogs = cls_cache::Read('catalogs');
$chid = 106;
_header('��������');
?>
<style type="text/css">
	.msgbox a{display: none;}	
</style>
<?
if($ore = cls_Safefillter::refCheck('',0)){ // die("��������{$cms_abs}������");
	cls_message::show('��ֹ�ⲿ��ҳ�ύ');
}

$aid = empty($aid) ? 0 : max(0,intval($aid));
if(!($channel = cls_cache::Read('channel',$chid))) cls_message::show('��ָ���ĵ����͡�');
if(!$memberid) cls_message::show('���ȵ�½��Ա��');
if($memberid==@$fmdata["tomid"]) cls_message::show('���ܸ��Լ����ʰ���');
$forward = empty($forward) ? M_REFERER : $forward;
$forwardstr = '&forward='.rawurlencode($forward);
$fields = cls_cache::Read('fields',$chid);
$caid = empty($caid)?516:max(1,intval($caid));
$caid_arr = array($caid);
$cotypes = cls_cache::Read('cotypes');

if(!submitcheck('bsubmit')){
	$pre_cns = array();
	$pre_cns['caid'] = $caid;
	$tomid = empty($tomid)?'': max(0,intval($tomid));
	$pid = empty($pid)?'': max(0,intval($pid));
	foreach($cotypes as $k => $v) if(!$v['self_reg']) $pre_cns['ccid'.$k] = empty(${'ccid'.$k}) ? '' : trim(${'ccid'.$k});
	foreach($pre_cns as $k => $v) if(!$v) unset($pre_cns[$k]);
	if(!$curuser->allow_arcadd($chid,$pre_cns)) cls_message::show('������ָ������Ŀ�������û�з���Ȩ�ޡ�');

	tabheader('','archiveadd',"?chid=$chid$forwardstr",2,1,1);
	tr_cns('������Ŀ','fmdata[caid]',array('value' => $caid,'ids'=>$caid_arr,'chid' => $chid,'hidden' => !empty($pre_cns['caid']) ? 0 : 1,'notblank' => 1,));
	if($pid){
		trhidden('fmdata[pid]',$pid);
	}
	trhidden('fmdata[tomid]',$tomid);
	$a_field = new cls_field;
	$subject_table = atbl($chid);
	foreach($fields as $k => $field){
		if($field['available']){
			if($k!='currency'){
				$a_field->init($field);
				$a_field->isadd = 1;
				$a_field->trfield('fmdata');
			}else{
				$field['max']=$curuser->info['currency1'];
				$a_field->init($field);
				$curuserfield = $a_field->varr('fmdata');
				trbasic($curuserfield['trname'],'$curuserfield[varname]',$curuserfield['frmcell'],'',array('guide'=>'�㵱ǰ���õ����ͷ�Ϊ<font color="red">'.$curuser->info['currency1'].'��</font>'));
			}
		}
	}
	unset($a_field);
	tr_regcode("archive$chid");
	tabfooter('bsubmit','���');
	_footer();
}else{
	if(!regcode_pass("archive$chid",empty($regcode) ? '' : trim($regcode))) cls_message::show('��֤�����',axaction(2,M_REFERER));
	if(empty($fmdata['caid']) || !($catalog = @$catalogs[$fmdata['caid']])) cls_message::show('��ָ����ȷ����Ŀ',axaction(2,M_REFERER));
	$fmdata['currency'] > $curuser->info['currency1'] && cls_message::show('���ͷֲ��㡣',axaction(2,M_REFERER));
	
	$pre_cns = array();
	$pre_cns['caid'] = $fmdata['caid'];
	//��������Ķ��弰Ȩ��
	//��ϵ�Զ�����
	$fmdata['ccid35'] = 3035;

	foreach($cotypes as $k => $v){
		if(!$v['self_reg'] && isset($fmdata["ccid$k"])){
			$fmdata["ccid$k"] = empty($fmdata["ccid$k"]) ? '' : $fmdata["ccid$k"];
			if($v['notblank'] && !$fmdata["ccid$k"]) cls_message::show("������ $v[cname] ����",axaction(2,M_REFERER));
			if($fmdata["ccid$k"]) $pre_cns['ccid'.$k] = $fmdata["ccid$k"];
			if($v['emode']){
				$fmdata["ccid{$k}date"] = !cls_string::isDate($fmdata["ccid{$k}date"]) ? 0 : strtotime($fmdata["ccid{$k}date"]);
				!$fmdata["ccid$k"] && $fmdata["ccid{$k}date"] = 0;
				if($fmdata["ccid$k"] && !$fmdata["ccid{$k}date"] && $v['emode'] == 2) cls_message::show("������ $v[cname] ��������",axaction(2,M_REFERER));
			}
		}
	}
	if(!$curuser->allow_arcadd($chid,$pre_cns)) cls_message::show('û�з���Ȩ��',axaction(2,M_REFERER),'��ָ����Ŀ');//������Ŀ��ϵķ���Ȩ��
	
	//////////////�ֶε�Ԥ�����쳣����
	$c_upload = new cls_upload;	
	$a_field = new cls_field;
	foreach($fields as $k => $v){
		$a_field->init($v);
		$fmdata[$k] = $a_field->deal('fmdata','mcmessage',axaction(2,M_REFERER));
	}
	unset($a_field);
	if(isset($fmdata['keywords'])) $fmdata['keywords'] = cls_string::keywords($fmdata['keywords']);//�ؼ���Ԥ����
	$arc = new cls_arcedit;
	if($aid = $arc->arcadd($chid,$fmdata['caid'])){
		foreach($cotypes as $k => $v){
			if(!$v['self_reg'] && !empty($fmdata["ccid$k"])){
				$arc->arc_ccid($fmdata["ccid$k"],$k,$v['emode'] ? $fmdata["ccid{$k}date"] : 0);
			}
		}
		foreach($fields as $k => $v){
			if(isset($fmdata[$k])){
				$arc->updatefield($k,$fmdata[$k],$v['tbl']);
				if($arr = multi_val_arr($fmdata[$k],$v)) foreach($arr as $x => $y) $arc->updatefield($k.'_'.$x,$y,$v['tbl']);
			}
		}
		foreach(array('salecp','fsalecp','ucid','tomid') as $k){
			isset($fmdata[$k]) && $arc->updatefield($k,trim($fmdata[$k]));
		}
	
		//����������Чʱ��		
		$arc->auto();
		$arc->autocheck();
		$arc->updatedb();
		//����¥������ҳ��������$fmdata['pid']��������ϼ���¥��
		if(!empty($fmdata['pid'])){
			$_pid = max(0,intval(@$fmdata['pid']));
			$db->query("INSERT INTO {$tblprefix}aalbums set arid='1',inid='$aid',pid='$_pid',incheck='1'");
		}
		
		$c_upload->closure(1,$aid);
		$c_upload->saveuptotal(1);		

		$arc->autostatic();//����ִ���Զ���̬
		$curuser->updatecrids(array(1=>-$fmdata['currency']),$updatedb=1,$remark='�������ͷ�');
		?>
		<script type="text/javascript">
			if (window.parent.$('.jqiframe').length) {
				window.parent.$('.jqiframe').jqModal('hide');
				window.parent.$.jqModal.tip('�ĵ�������', 'succeed');
			}  
		</script>
		<?php
		cls_message::show('�ĵ�������',axaction(1,M_REFERER));
	}else{
		$c_upload->closure(1);
		cls_message::show('����ĵ�ʧ��',axaction(2,M_REFERER));
	}
}
?>
<script type="text/javascript">
	document.body.style.height = '420px';
	document.getElementById('_08_upload_inputIframe_fmdata[thumb]').parentNode.style.height = '144px';
</script>

