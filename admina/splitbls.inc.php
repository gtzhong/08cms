<?php
(!defined('M_COM') || !defined('M_ADMIN')) && exit('No Permission');
aheader();
if($re = $curuser->NoBackFunc('channel')) cls_message::show($re);
foreach(array('channels','cotypes',) as $k) $$k = cls_cache::Read($k);
$splitbls = fetch_arr();
if(empty($action)){
	backnav('channel','dbsplit');
	tabheader("�ĵ�������� &nbsp; &nbsp;>><a href=\"?entry=$entry&action=splitbls\" onclick=\"return floatwin('open_splitbls',this)\">��������</a>",'',"",'10');
	trcategory(array('ģ��ID',array('�ĵ�ģ��','txtL'),array('����ID','txtL'),array('��������','txtL'),array('���ݱ�����','txtL'),modpro('�ֶ�')));
	foreach($channels as $k => $v){
		$splitstr = empty($v['stid']) ? 'ϵͳ��' : $splitbls[$v['stid']]['cname'];
		$tblstr = 'archives'.(empty($v['stid']) ? '' : $v['stid']);
		echo "<tr class=\"txt\">".
			"<td class=\"txtC w60\">$k</td>\n".
			"<td class=\"txtL\">".mhtmlspecialchars($v['cname'])."</td>\n".
			"<td class=\"txtL w60\">$v[stid]</td>\n".
			"<td class=\"txtL\">$splitstr</td>\n".
			"<td class=\"txtL\">$tblstr</td>\n".
			modpro("<td class=\"txtC w40\"><a href=\"?entry=$entry&action=clearfields&stid=$v[stid]&chid=$k\" onclick=\"return floatwin('open_splitbls',this)\">����</a></td>\n").
			"</tr>\n";
	}
	tabfooter();
	a_guide('channelsplit');
}elseif($action == 'splitbls'){
	if(!submitcheck('bsubmit')){
		tabheader("�ֱ����",'splitbls',"?entry=$entry&action=$action",'10');
		trcategory(array('�ֱ�ID',array('�ֱ�����','txtL'),array('���ݱ�','txtL'),'����','�رվ�̬',array('�ĵ�ģ��','txtL'),'ɾ��',));
		foreach($splitbls as $k => $v){
			$channelstr = '';foreach($v['chids'] as $x) @$channels[$x]['cname'] && $channelstr .= $channels[$x]['cname']."($x),";
			echo "<tr class=\"txt\">".
				"<td class=\"txtC w60\">$k</td>\n".
				"<td class=\"txtL\"><input type=\"text\" size=\"15\" maxlength=\"30\" name=\"fmdata[$k][cname]\" value=\"$v[cname]\"></td>\n".
				"<td class=\"txtL\">archives$k</td>\n".
				"<td class=\"txtC w80\"><input type=\"text\" size=\"4\" maxlength=\"4\" name=\"fmdata[$k][vieworder]\" value=\"$v[vieworder]\"></td>\n".
				"<td class=\"txtC w60\"><input class=\"checkbox\" type=\"checkbox\" name=\"fmdata[$k][nostatic]\" value=\"1\"".($v['nostatic'] ? " checked" : "")."></td>\n".
				"<td class=\"txtL\">".($channelstr ? $channelstr : '��')."</td>\n".
				"<td class=\"txtC w30\">".($channelstr ? '-' : "<a onclick=\"return deltip()\" href=\"?entry=$entry&action=del&stid=$k\">ɾ��</a>")."</td>\n".
				"</tr>\n";
		}
		tabfooter('bsubmit');
		a_guide('splitbls');
	}else{
		if(isset($fmdata)){
			foreach($fmdata as $k => $v){
				$v['cname'] = trim(strip_tags($v['cname']));
				$v['cname'] = $v['cname'] ? $v['cname'] : $splitbls[$k]['cname'];
				$v['vieworder'] = max(0,intval($v['vieworder']));
				$v['nostatic'] = empty($v['nostatic']) ? 0 : 1;
				$db->query("UPDATE {$tblprefix}splitbls SET cname='$v[cname]',vieworder='$v[vieworder]',nostatic='$v[nostatic]' WHERE stid='$k'");
			}
			adminlog('�༭�ֱ�����б�');
			cls_CacheFile::Update('splitbls');
		}
		cls_message::show('�ֱ����༭���',"?entry=$entry&action=$action");
	}
}elseif($action == 'del' && $stid){
	$splitbl = $splitbls[$stid];
	echo "<title>ɾ���ĵ����� - ����ID��$stid</title>";
	modpro() || cls_message::show('����ϵ��ʼ�˿��Ŷ��ο���ģʽ');
	if(!submitcheck('confirm')){
		$message = "ɾ�����ָܻ���ȷ��ɾ����ѡ��Ŀ?<br><br>";
		$message .= "ȷ������>><a href=?entry=$entry&action=$action&stid=$stid&confirm=ok>ɾ��</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
		$message .= "��������>><a href=?entry=$entry&action=splitbls>����</a>";
		cls_message::show($message);
	}
	foreach($splitbl['chids'] as $k => $v) if(empty($channels[$v])) unset($splitbl['chids'][$k]); 
	empty($splitbl['chids']) || cls_message::show('��ǰ�ֱ�������ĵ�ģ�ͣ�����ɾ����',"?entry=$entry&action=splitbls");
	if($db->result_one("SELECT COUNT(*) FROM {$tblprefix}archives$stid")) cls_message::show('ɾ���ֱ�����ɾ���ֱ��������ĵ���Ϣ��',"?entry=$entry&action=splitbls");
	$db->query("DROP TABLE IF EXISTS {$tblprefix}archives$stid",'SILENT');
	$db->query("DELETE FROM {$tblprefix}splitbls WHERE stid='$stid'",'SILENT');
	//�����ػ���
	adminlog('ɾ���ĵ��ֱ�-'.$splitbl['cname']);
	cls_CacheFile::Update('splitbls');
	cls_message::show('ָ�����ĵ��ֱ��ѳɹ�ɾ����',"?entry=$entry&action=splitbls");
}elseif($action == 'clearfields' && $stid && $chid){
	//��������һЩ����Ҫ�õ������ֶΣ�ֱ�Ӳ������ݿ⣬��̨�ܹ����ֶ�����ϵ����Щ��Χ
	modpro() || cls_message::show('����ϵ��ʼ�˿��Ŷ��ο���ģʽ');
	if(!($splitbl = $splitbls[$stid])) cls_message::show('ָ�����ĵ��������ڡ�');
	if(!($fields = cls_cache::Read('fields',$chid))) cls_message::show('��ָ���ĵ�ģ�͡�');
	echo "<title>��������ֶ� - �ĵ�����archives$stid</title>";
	$nowtbl = "archives$stid";
	
	$fieldsarr = array();
	$query = $db->query("SHOW FULL COLUMNS FROM {$tblprefix}$nowtbl");
	while($field = $db->fetch_array($query)) $fieldsarr[] = $field;	
	
	$nodels = array('aid','arctpls','caid','chid','clicks','checked','color','createdate','customurl',
	'dpmid','editor','editorid','enddate','fsalecp','initdate','jumpurl','letter','from_mid','from_mname',
	'mclicks','mid','mname','needstatics','nowurl','downs','mdowns','wdowns','plays','mplays','wplays',
	'refreshdate','relatedaid','rpmid','salecp','subject','tid','ucid','updatedate','vieworder','wclicks',
	);
	// ����afields�����ֶ�
	$qaf = $db->query("SELECT ename FROM {$tblprefix}afields WHERE tbl='".atbl($stid)."'");
	while($field = $db->fetch_array($qaf)) $nodels[] = $field['ename']; //print_r($nodels);	 
	foreach($cotypes as $k => $v){
		$nodels[] = "ccid$k";
		$nodels[] = "ccid{$k}date";
	}
	foreach($fields as $k => $v){
		$nodels[] = $k;
		if($v['datatype'] == 'map'){
			$nodels[] = "{$k}_0";
			$nodels[] = "{$k}_1";
		}
	}
	if(!submitcheck('bsubmit')){
		tabheader($splitbl['cname']." - �ֶ����� - ��������$nowtbl",'channeldetail',"?entry=$entry&action=$action&stid=$stid&chid=$chid");
		trcategory(array('ɾ��','���',array('��ʶ','txtL'),array('�ֶ�����','txtL')));
		foreach($fieldsarr as $k => $v){
			echo "<tr class=\"txt\">\n".
				"<td class=\"txtC w40\"><input class=\"checkbox\" type=\"checkbox\" name=\"delete[$v[Field]]\" value=\"$v[Field]\"".(in_array($v['Field'],$nodels) ? ' disabled' : '')." onclick=\"deltip()\"></td>\n".
				"<td class=\"txtC w35\">$k</td>\n".
				"<td class=\"txtL w120\">".mhtmlspecialchars($v['Field'])."</td>\n".
				"<td class=\"txtL\">$v[Type]</td>\n".
				"</tr>";
		}
		tabfooter('bsubmit','ɾ��');
		a_guide('clearfields');
	}else{
		if(!empty($delete)){
			foreach($delete as $k){
				if(!in_array($k,$nodels)){
					$db->query("ALTER TABLE {$tblprefix}$nowtbl DROP $k",'SILENT');
				}
			}
		}
		adminlog('����'.$nowtbl.'���ݱ��ֶ�');
		cls_message::show('�ֶ��������!',"?entry=$entry&action=$action&stid=$stid&chid=$chid");
	}
}
function fetch_arr(){
	$do = cls_cache::exRead('cachedos',1);
	return cls_DbOther::CacheArray($do['splitbls']);
}
?>
