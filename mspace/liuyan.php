<?
include_once dirname(dirname(__FILE__)).'/include/general.inc.php';
include_once M_ROOT."./include/adminm.fun.php";
$forward = empty($forward) ? M_REFERER : $forward;
$forwardstr = '&forward='.urlencode($forward);
$cuid = 5;

$mid = empty($mid) ? 0 : max(0,intval($mid));
if(!$mid) cls_message::show('��ָ����Ҫ���ԵĶ���');
if(!($commu = cls_cache::Read('commu',$cuid)) || !$commu['available']) cls_message::show('���Թ����ѹرա�');
$auser = new cls_userinfo;
$auser->activeuser($mid);
if(!$auser->info['mid'] || !$auser->info['checked'] || !in_array($auser->info['mchid'],$commu['chids'])) cls_message::show('��ָ����Ҫ���ԵĶ���');
$fields = cls_cache::Read('cufields',$cuid);
if(!submitcheck('bsubmit')){
	_header();
	tabheader('����˵����','commuadd',"?mid=$mid$forwardstr",2,1,1);
	$a_field = new cls_field;
	foreach($fields as $k => $v){
		if(in_array($k,array('reply'))) continue;
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
	if(!empty($commu['repeattime']) && !empty($m_cookie["08cms_cuid_{$cuid}_{$mid}"])) cls_message::show('�����벻Ҫ����Ƶ����',axaction(2,M_REFERER));
	#cookie�жϵ�ǰ�Ƿ��Ѿ��������ˡ�
	$sqlstr = "tomid='$mid',tomname='{$auser->info['mname']}',ip='$onlineip',mid='$memberid',mname='{$curuser->info['mname']}',createdate='$timestamp'";
	if($curuser->pmautocheck($commu['autocheck'],'cuadd')) $sqlstr .= ",checked=1";
	$c_upload = new cls_upload;	
	$a_field = new cls_field;
	foreach($fields as $k => $v){
		if(isset($fmdata[$k])){
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
		cls_message::show('���Գɹ���',axaction(10,$forward));
	}else{
		$c_upload->closure(1);
		cls_message::show('���Բ��ɹ���',axaction(10,$forward));
	}
}
		

?>

