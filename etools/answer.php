<?
include_once dirname(dirname(__FILE__)).'/include/general.inc.php';
include_once M_ROOT."./include/adminm.fun.php";

$forward = empty($forward) ? M_REFERER : $forward;
$forwardstr = '&forward='.urlencode($forward);
$cuid = 37; $chid = 106;
$aid = empty($aid) ? 0 : max(0,intval($aid));
$cid = empty($cid) ? 0 : max(0,intval($cid));
$toaid = empty($toaid) ? 0 : max(0,intval($toaid));
$tocid = empty($tocid) ? 0 : max(0,intval($tocid));
if(empty($action)){	
	$mid = empty($mid)?$memberid:$mid;
	if(!$mid) cls_message::show('���ȵ�½��Ա��',$forward); 
	if(!$aid) cls_message::show('��ָ����Ҫ�ش�Ķ���',$forward);
	if(!($commu = cls_cache::Read('commu',$cuid)) || !$commu['available']) cls_message::show('�ʴ����ѹرա�',$forward);
	$arc = new cls_arcedit;
	$arc->set_aid($aid,array('chid'=>$chid));
	$commu['chids'] = empty($commu['chids'])?array():$commu['chids'];
	if(!$arc->aid || !$arc->archive['checked'] || !in_array($arc->archive['chid'],$commu['chids'])) cls_message::show('��ָ����Ҫ�ش�Ķ���',$forward);
	if($arc->archive['mid']==$mid) cls_message::show('���ܶ��Լ���������лش�');
	if($arc->archive['answercid']) cls_message::show('�����Ѿ������');	
	$arc->archive['close'] && cls_message::show('�����Ѿ��رա�');
	if(empty($commu['repeatanswer']) && $db->result_one("select cid from {$tblprefix}$commu[tbl] where aid='$aid' and mid='$mid'")) cls_message::show('�������ظ��ش����⡣',$forward);	
	
	$fields = cls_cache::Read('cufields',$cuid);
	if(!submitcheck('bsubmit')){
		_header('�����ش�����');
		tabheader('�����ش�','commuadd',"?aid=$aid&mid=$mid$forwardstr",2,1,1);
		$a_field = new cls_field;
		foreach($fields as $k => $v){
			$a_field->init($v);
			$a_field->isadd = 1;
			$a_field->trfield('fmdata');
		}
		unset($a_field);
		tr_regcode("commu_$cuid");
		tabfooter('bsubmit');
		_footer();
	}else{//���ݴ���
		_header();
		if(!regcode_pass("commu_$cuid",empty($regcode) ? '' : trim($regcode))) cls_message::show('��֤�����',axaction(2,M_REFERER));
		if(!$curuser->pmbypmid($commu['pmid'])) cls_message::show('��û�лش�Ȩ�ޡ�',axaction(2,M_REFERER));
		//�����й�����£��ش�����Ļش��߲����Ǳ��йܵĻ�Ա���⣬midͨ�������ڻ�Ա���Ĵ��ݹ���
		$mid = empty($mid)?$memberid:$mid;
		$sqlstr = "aid='$aid',ip='$onlineip',mid='$mid',mname='{$curuser->info['mname']}',createdate='$timestamp'";
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
			//��¼�ش�������
			$db->query("update {$tblprefix }".atbl($chid)." set stat0=stat0+1 where aid='$aid'");
			$c_upload->closure(1,$cid,"commu_$cuid");
			$c_upload->saveuptotal(1);
			cls_cubasic::setCridsOuter($cuid);
			cls_message::show('�ش�ɹ���',axaction(6,$forward));
		}else{
			$c_upload->closure(1);
			cls_message::show('�ش𲻳ɹ���',axaction(10,$forward));
		}
	}
		
}elseif($action == 'vote'){
	$inajax = empty($inajax) ? 0 : 1;
	if(!($commu = cls_cache::Read('commu',$cuid)) || !$commu['available']) cls_message::show('�ʴ����ѹرա�',$forward);
	if(!$cid || !$db->result_one("SELECT cid FROM {$tblprefix}$commu[tbl] WHERE cid='$cid'")) cls_message::show('��ѡ����ȷ�Ĳ�������',$forward);
	if(empty($m_cookie["08cms_cuid_{$cuid}_vote_$cid"])){
		msetcookie("08cms_cuid_{$cuid}_vote_$cid",1,365 * 86400);
	}else cls_message::show('���Ѿ�Ͷ��Ʊ�ˡ�',$forward);
	$opt = empty($opt) ? 1 : min(2,max(1,intval($opt)));
	$db->query("UPDATE {$tblprefix}$commu[tbl] SET opt$opt = opt$opt + 1 WHERE cid='$cid'");
	cls_message::show($inajax ? 'succeed' : 'ͶƱ�ɹ���',$forward);
}elseif($action == 'ok'){
	$inajax = empty($inajax) ? 0 : 1;
	$chid = 106;
	if(!$aid) cls_message::show('��ָ����Ҫ���������⡣',$forward);
	$arc = new cls_arcedit;
	$arc->set_aid($aid,array('chid'=>$chid,'ch'=>1)); //print_r($arc); die();
	if(!$memberid) cls_message::show('���ȵ�½��Ա��',$forward); 
	if($arc->archive['mid'] != $memberid) cls_message::show('��û�в���Ȩ�ޣ�',$forward);
	if(!($commu = cls_cache::Read('commu',$cuid)) || !$commu['available']) cls_message::show('�ʴ����ѹرա�',$forward);
	if(!$arc->aid || !$arc->archive['checked'] || !in_array($arc->archive['chid'],$commu['chids'])) cls_message::show('��ָ����Ҫ���������⡣',$forward);
	($arc->archive['close']) && cls_message::show('���Ͳ���ʧ�ܣ������Ѿ��رա�',$forward);
	if(!$cid || !$tomid = $db->result_one("select mid from {$tblprefix}$commu[tbl] where cid='$cid'")) cls_message::show('��ָ�����͵Ķ���',$forward);
	if($arc->archive['answercid'] || $arc->archive['ccid35'] == 3036) cls_message::show('���Ͳ���ʧ�ܣ��Ѿ����͹���',$forward);#��������Ѵ𰸻����Ѿ����������ʱ��
	//���½�����¼
	$db->query("UPDATE {$tblprefix}$commu[tbl] SET isanswer = '1' WHERE cid ='$cid'");
	//��ָ����Ա�ӷ�
	if($tomid && $arc->archive['currency']){
		$crids = array(1=>$arc->archive['currency']);
		$tocuruser = new cls_userinfo;
		$tocuruser->activeuser($tomid);
		$tocuruser->updatecrids($crids,1,'��ѡΪ��Ѵ�');
		unset($tocuruser);
	}
	$arc->updatefield('answercid',$cid);
	//�����ĵ�״̬
	$arc->arc_ccid(3036,35);//�����Ѿ����
    $arc->updatefield('finishdate',$timestamp);
	$arc->updatedb();
	cls_message::show($inajax ? 'succeed' : '�趨��Ѵ𰸳ɹ���',$forward);
	unset($arc);
}elseif($action == 'supplementary'){
	$inajax = empty($inajax) ? 0 : 1;
	if(!$memberid) cls_message::show('���ȵ�½��Ա��',$forward);
	if(!$aid) cls_message::show('��ָ����Ҫ���������⡣',$forward);
	$arc = new cls_arcedit;
	$arc->set_aid($aid,array('chid'=>$chid,'ch'=>1));
	if(!($commu = cls_cache::Read('commu',$cuid)) || !$commu['available']) cls_message::show('�ʴ����ѹرա�',$forward);
	if(!$arc->aid || !$arc->archive['checked'] || !in_array($arc->archive['chid'],$commu['chids'])) cls_message::show('��ָ����Ҫ���������⡣',$forward);
	($arc->archive['close']) && cls_message::show('����ʧ�ܣ������Ѿ��رա�',$forward);
	if($arc->archive['answercid'] || $arc->archive['ccid35'] == 3036) cls_message::show('����ʧ�ܣ������Ѿ������',$forward);
	//�ʴ����µ��ж�
	if($tocid){	//ע���жϵ�ǰ��������Ƿ���ȷ����������������������������������������������������������������������������������������	
		if(!$tomid = $db->result_one("select mid from {$tblprefix}$commu[tbl] where cid='$tocid'")) cls_message::show('��ָ���ظ��򲹳�Ķ���',$forward);
		if(!($memberid == $tomid || $memberid == $arc->archive['mid'])) cls_message::show('��û�в����׷�ʵĲ���Ȩ�ޣ�',$forward);
		if(!empty($selzw)){
			if(empty($fmdata['content'])) cls_message::show('���ݲ���Ϊ�ա�',$forward);
			$db->query("INSERT INTO {$tblprefix}$commu[tbl] SET aid='$aid',tocid='$tocid',content='$fmdata[content]',ip='$onlineip',mid='$memberid',mname='{$curuser->info['mname']}',createdate='$timestamp'".($curuser->pmautocheck($commu['autocheck'],'cuadd')?',checked=1':'')."");				
		}
		cls_message::show($inajax ? 'succeed' : '��������ɹ���',$forward);
	}elseif($toaid){
		$memberid != $arc->archive['mid'] && cls_message::show('����ʧ�ܣ���û�и�����Ĳ���Ȩ�ޣ�',$forward);
		if(!empty($addreward)){
			$rewardpoints < 0 && cls_message::show('׷�ӻ���ʧ�ܣ�׷�Ӳ���Ϊ������',$forward);
			$curuser->detail_data();
			$crids = empty($rewardpoints) ? array():array(1=>-$rewardpoints);
			if($curuser->info['currency1'] - $rewardpoints < 0) cls_message::show('��û���㹻�Ļ��֣�',$forward);
			$curuser->updatecrids($crids,1,'׷�����ͷ�');
			$arc->updatefield('currency',$rewardpoints+$arc->archive['currency'],"archives_$chid");
			$arc->updatedb();
		}
		if(!empty($added)){
			if(empty($fmdata['content'])) cls_message::show('�������ⲻ��Ϊ�ա�',$forward);
			$db->query("INSERT INTO {$tblprefix}$commu[tbl] SET aid='$aid',toaid='$aid',content='$fmdata[content]',ip='$onlineip',mid='$memberid',mname='{$curuser->info['mname']}',createdate='$timestamp'".($curuser->pmautocheck($commu['autocheck'],'cuadd')?',checked=1':'')."");
		}
		cls_message::show($inajax ? 'succeed' : '�����ɹ���',$forward);
		unset($arc);
	}
}

?>

