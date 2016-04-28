<?php
$cotypes = cls_cache::Read('cotypes');
$vcps = cls_cache::Read('vcps');
$catalogs = cls_cache::Read('catalogs');

//����ģ�Ͷ��弰Ȩ��
$chid = empty($chid) ? 0 : max(0,intval($chid));
if(!($channel = cls_cache::Read('channel',$chid))) cls_message::show('��ָ���ĵ����͡�');
$forward = empty($forward) ? M_REFERER : $forward;
$forwardstr = '&forward='.rawurlencode($forward);
$fields = cls_cache::Read('fields',$chid);

if(!submitcheck('bsubmit')){
	$pre_cns = array();
	$pre_cns['caid'] = empty($caid) ? 0 : max(0,intval($caid));
	foreach($cotypes as $k => $v) if(!$v['self_reg']) $pre_cns['ccid'.$k] = empty(${'ccid'.$k}) ? '' : trim(${'ccid'.$k});
	if(($pid = empty($pid) ? 0 : max(0,intval($pid))) && $p_album = $db->fetch_one("SELECT * FROM {$tblprefix}".atbl($pid,2)." WHERE aid='$pid'")){
		//�Ƿ�̳кϼ�����Ŀ������//���ֻ���ĵ��ڵĺϼ���������ǻ�Ա�ϼ����д���
		#$pre_cns['caid'] = $p_album['caid'];
		foreach($cotypes as $k => $v) if(!$v['self_reg'] && $p_album['ccid'.$k]){
			#$pre_cns['ccid'.$k] = $p_album['ccid'.$k];
		}
	}else $pid = 0;

	foreach($pre_cns as $k => $v) if(!$v) unset($pre_cns[$k]);
	if(!$curuser->allow_arcadd($chid,$pre_cns)) cls_message::show('������ָ������Ŀ�������û�з���Ȩ�ޡ�');
	$catalogs = cls_cache::Read('catalogs');

	tabheader($channel['cname'].'&nbsp; -&nbsp; ����ĵ�','archiveadd',"?action=archiveadd&chid=$chid$forwardstr",2,1,1);

	if($pid){//ָ���ϼ�������ĵ�����Ϣ��ʾ
		trhidden('fmdata[pid]',$pid);
		trbasic('�����ϼ�','',"<a href=\"".cls_ArcMain::Url($p_album)."\" target=\"_blank\">".mhtmlspecialchars($p_album['subject'])."</a>",'');
	}
	tr_cns('������Ŀ','fmdata[caid]',array('value' => @$pre_cns['caid'],'chid' => $chid,'hidden' => empty($pre_cns['caid']) ? 0 : 1,'notblank' => 1,));
	foreach($cotypes as $k => $v){
		if(!$v['self_reg']){
			tr_cns($v['cname'],"fmdata[ccid$k]",array('value' => empty($pre_cns['ccid'.$k]) ? 0 : $pre_cns['ccid'.$k],'coid' => $k,'chid' => $chid,'max' => $v['asmode'],'hidden' => empty($pre_cns['ccid'.$k]) ? 0 : 1,'notblank' => $v['notblank'],'emode' => $v['emode'],'evarname' => "fmdata[ccid{$k}date]",));
		}
	}

	$a_field = new cls_field;
	$subject_table = atbl($chid);
	foreach($fields as $k => $field){
		if($field['available']){
			$a_field->init($field);
			$a_field->isadd = 1;
			$a_field->trfield('fmdata');
		}
	}
	unset($a_field);
	
	trbasic('����ĵ��ۼ�','fmdata[salecp]',makeoption(array('' => '���') + $vcps['sale']),'select');
	trbasic('���������ۼ�','fmdata[fsalecp]',makeoption(array('' => '���') + $vcps['fsale']),'select');
	//�����ĵ��ĸ��˷���
	$uclasses = cls_Mspace::LoadUclasses($curuser->info['mid']);
	$ucidsarr = array(0 => '��ѡ��');
	foreach($uclasses as $k => $v) if(!$v['cuid']) $ucidsarr[$k] = $v['title'];
	trbasic('�ҵķ���','fmdata[ucid]',makeoption($ucidsarr),'select');
	
	tr_regcode('archive');
	tabfooter('bsubmit','���');
}else{
	if(!regcode_pass('archive',empty($regcode) ? '' : trim($regcode))) cls_message::show('��֤�����',axaction(2,M_REFERER));
	if(empty($fmdata['caid']) || !($catalog = @$catalogs[$fmdata['caid']])) cls_message::show('��ָ����ȷ����Ŀ',axaction(2,M_REFERER));
	
	$pre_cns = array();
	$pre_cns['caid'] = $fmdata['caid'];
	//��������Ķ��弰Ȩ��
	foreach($cotypes as $k => $v){
		if(!$v['self_reg'] && isset($fmdata["ccid$k"])){
			$fmdata["ccid$k"] = empty($fmdata["ccid$k"]) ? '' : $fmdata["ccid$k"];
			if($v['notblank'] && !$fmdata["ccid$k"]) cls_message::show("������ $v[cname] ����",axaction(2,M_REFERER));
			if($fmdata["ccid$k"]) $pre_cns['ccid'.$k] = $fmdata["ccid$k"];
			if($v['emode']){
				$fmdata["ccid{$k}date"] = !cls_string::isDate($fmdata["ccid{$k}date"]) ? 0 : trim($fmdata["ccid{$k}date"]);
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
		foreach(array('salecp','fsalecp','ucid',) as $k){
			isset($fmdata[$k]) && $arc->updatefield($k,trim($fmdata[$k]));
		}
		$arc->auto();
		$arc->autocheck();
		$arc->updatedb();
		
		$c_upload->closure(1,$aid);
		$c_upload->saveuptotal(1);
		
		if(isset($fmdata['pid']) && !empty($fmdata['pid'])){
			$db->query("INSERT INTO {$tblprefix}aalbums set arid='1',inid = '$aid',pid = '$fmdata[pid]',incheck='1',shoudong='1'");
		}
		
		//if(!empty($fmdata['pid'])) $arc->set_album($fmdata['pid'],$arid);//�鼭����,���ĵ����ݿ��޹�
		$arc->autostatic();//����ִ���Զ���̬
		
		cls_message::show('�ܱ�������',axaction(6,M_REFERER));
	}else{
		$c_upload->closure(1);
		cls_message::show('����ܱ�ʧ��',axaction(2,M_REFERER));
	}
}
?>

