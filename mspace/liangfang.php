<?
include_once dirname(dirname(__FILE__)).'/include/general.inc.php';
include_once M_ROOT."./include/adminm.fun.php";
$forward = empty($forward) ? M_REFERER : $forward;
$forwardstr = '&forward='.urlencode($forward);
$cuid = 32;
$mid = empty($mid) ? 0 : max(0,intval($mid));
if(!($commu = cls_cache::Read('commu',$cuid)) || !$commu['available']) cls_message::show('������������ѹرա�');

$fields = cls_cache::Read('cufields',$cuid);
if(!submitcheck('bsubmit')){
	_header();
	tabheader('����д�����������Ϣ','commuadd',"?$forwardstr",2,1,1);
	$a_field = new cls_field;
	foreach($fields as $k => $v){
		$a_field->init($v);
		$a_field->isadd = 1;
		$a_field->trfield('fmdata');
	}
	unset($a_field);
	tr_regcode("commu$cuid");
	tabfooter('bsubmit');
	_footer();
}else{//���ݴ���
	_header();
	if(!regcode_pass("commu$cuid",empty($regcode) ? '' : trim($regcode))) cls_message::show('��֤�����',axaction(2,M_REFERER));
	if(!$curuser->pmbypmid($commu['pmid'])) cls_message::show('��û������Ȩ�ޡ�',axaction(2,M_REFERER));
	$companyIds = empty($companyIds) ? array() : explode(',',$companyIds[0]);
	array_pop($companyIds);
	if(empty($companyIds)) cls_message::show('��ָ��װ�޹�˾��');
	if(!empty($commu['repeattime']) && !empty($m_cookie["08cms_cuid_{$cuid}_{$mid}"])) cls_message::show('�����벻Ҫ����Ƶ����',axaction(2,M_REFERER));
	#cookie�жϵ�ǰ�Ƿ��Ѿ��������ˡ�
	$auser = new cls_userinfo;	
	
	foreach($companyIds as $m){		
		$auser->activeuser($m);	
		if(!$auser->info['mid'] || !$auser->info['checked'] || !in_array($auser->info['mchid'],$commu['chids'])) cls_message::show('��ѡ��װ�޹�˾��');
		$sqlstr = "tomid='$m',tomname='{$auser->info['mname']}',ip='$onlineip',mid='$memberid',mname='{$curuser->info['mname']}',createdate='$timestamp'";
		if($curuser->pmautocheck($commu['autocheck'],'cuadd')) $sqlstr .= ",checked=1";
		$c_upload = new cls_upload;	
		$a_field = new cls_field;
		foreach($fields as $k => $v){
			if(isset($fmdata[$k])){
				if($k=='fengge' && !is_array($fmdata[$k])) $fmdata[$k] = explode("\t",$fmdata[$k]);
				$a_field->init($v);
				$fmdata[$k] = $a_field->deal('fmdata','mcmessage',axaction(2,M_REFERER));
				$sqlstr .= ",$k='$fmdata[$k]'";
				if($arr = multi_val_arr($fmdata[$k],$v)) foreach($arr as $x => $y) $sqlstr .= ",{$k}_x='$y'";
			}
		}
		unset($a_field);		
		$db->query("INSERT INTO {$tblprefix}$commu[tbl] SET $sqlstr");
		if($cid = $db->insert_id()){
			if(!empty($commu['repeattime'])) msetcookie("08cms_cuid_{$cuid}_{$mid}",1,$commu['repeattime'] * 60);
		#���ò����ɹ�������cookie
			$c_upload->closure(1,$cid,"commu$cuid");
			$c_upload->saveuptotal(1);
			unset($c_upload);
		}
	}
	cls_message::show('��������ύ�ɹ���',axaction(10,$forward));
}
		

?>

