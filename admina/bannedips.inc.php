<?php
(!defined('M_COM') || !defined('M_ADMIN')) && exit('No Permission');
aheader();
if($re = $curuser->NoBackFunc('bannedip')) cls_message::show($re);
$bannedips = cls_cache::Read('bannedips');
if(empty($action)) $action = 'bannedipsedit';
if($action == 'bannedipsedit'){
	backnav('bannedip','ip');
	if(!submitcheck('bsubmit')){
		tabheader('��ֹIP����'."&nbsp; &nbsp; >><a href=\"?entry=$entry&action=bannedipadd\" onclick=\"return floatwin('open_bannedips',this)\">���</a>",$actionid.'arcsedit',"?entry=$entry&action=$action");
		trcategory(array("<input class=\"checkbox\" type=\"checkbox\" name=\"chkall\" onclick=\"deltip(this,0,checkall,this.form, 'delete', 'chkall')\">ɾ?",array('IP��ַ', 'txtL'),'��ʼ����','��������'));
		$query = $db->query("SELECT * FROM {$tblprefix}bannedips ORDER BY bid DESC");
		while($r = $db->fetch_array($query)){
			$ipstr = '';for($i = 1;$i < 5;$i ++) $ipstr .= ($ipstr ? '.' : '').($r["ip$i"] == -1 ? '*' : $r["ip$i"]);
			echo "<tr class=\"txt\">".
			"<td class=\"txtC w40\"><input class=\"checkbox\" type=\"checkbox\" name=\"delete[$r[bid]]\" value=\"$r[bid]\" onclick=\"deltip()\"></td>\n".
			"<td class=\"txtL\">$ipstr</td>\n".
			"<td class=\"txtC\">".date('Y-m-d',$r['createdate'])."</td>\n".
			"<td class=\"txtC w200\"><input type=\"text\" size=\"15\" id=\"fmdata[$r[bid]][enddate]\" name=\"fmdata[$r[bid]][enddate]\" value=\"".date('Y-m-d',$r['enddate'])."\"></td>\n".
			"</tr>\n";
		}
		tabfooter('bsubmit');
		a_guide('bannedipsedit');
	}else{
		if(!empty($delete)){
			foreach($delete as $k){
				$db->query("DELETE FROM {$tblprefix}bannedips WHERE bid='$k'");
				unset($fmdata[$k]);
			}
		}
		if(!empty($fmdata)){
			foreach($fmdata as $k => $v){
				$v['enddate'] = trim($v['enddate']);
				$v['enddate'] = !cls_string::isDate($v['enddate']) ? 0 : strtotime($v['enddate']);
				$db->query("UPDATE {$tblprefix}bannedips SET enddate='$v[enddate]' WHERE bid='$k'");
			}
		}
		adminlog('�༭��ֹIP�б�');
		cls_CacheFile::Update('bannedips'); 
		cls_message::show('��ֹIP�༭��ɣ�', "?entry=$entry&action=$action");
	}

}elseif($action == 'bannedipadd'){
	if(!submitcheck('bbannedipadd')){
		tabheader('��ӽ�ֹIP','bannedipadd',"?entry=$entry&action=$action",2,0,1);
		$str = '';
		for($i = 1;$i < 5;$i ++){
			$str .= ($str ? '.' : '')."<input type=\"text\" size=\"3\" id=\"fmdata[ip$i]\" name=\"fmdata[ip$i]\" value=\"\" rule=\"int\" min=\"-1\" max=\"255\" />\n"	;
		}
		trbasic('IP��ַ','',$str,'',array('guide' => '���������������-1��255�����֣�-1��ʾ�ö����е�ַ'));
		trbasic('��ֹ��������','fmdata[enddate]',date('Y-m-d',$timestamp + 30 * 24 * 3600),'calendar',array('guide' => '���ձ�ʾ���ý�ֹ'));
		tabfooter('bbannedipadd');
	}else{
		$sqlstr = "createdate='$timestamp'";
		for($i = 1;$i < 5;$i ++){
			$fmdata["ip$i"] = empty($fmdata["ip$i"]) ? 0 : max(-1,min(255,intval($fmdata["ip$i"])));
			$sqlstr .= ",ip$i='".$fmdata["ip$i"]."'";
		}
		$fmdata['enddate'] = trim($fmdata['enddate']);
		$fmdata['enddate'] = !cls_string::isDate($fmdata['enddate']) ? 0 : strtotime($fmdata['enddate']);
		$sqlstr .= ",enddate='".$fmdata['enddate']."'";
		$db->query("INSERT INTO {$tblprefix}bannedips SET $sqlstr");
		adminlog('��ӽ�ֹIP');
		cls_CacheFile::Update('bannedips');
		cls_message::show('��ֹIP��ӳɹ���', axaction(6,"?entry=$entry&action=bannedipsedit"));
	}
}elseif($action == 'vsconfig'){
	if(!submitcheck('bmconfigs')){
		tabheader('�������ʼ�¼','cfdebug',"?entry=$entry&action=$action");
		trbasic('��¼������ʼ�¼','mconfigsnew[vs_holdtime]',empty($mconfigs['vs_holdtime']) ? 0 : $mconfigs['vs_holdtime'],'text',array('guide' => '��λ�����ӣ�����0-300֮������֣����ջ�0Ϊ����¼��������¼������ϵͳ������������ϵͳ״��ʱ���á�'));
		trbasic('������м�¼','vsclear',0,'radio',array('guide' => '����ǰ���еķ��ʼ�¼���'));
		tabfooter('bmconfigs');
	}else{
		$mconfigsnew['vs_holdtime'] = max(0,min(300,intval($mconfigsnew['vs_holdtime'])));
		if(!empty($vsclear)) $db->query("TRUNCATE TABLE {$tblprefix}visitors");
		saveconfig('debug');
		cls_message::show('���ʼ�¼�������',axaction(6,"?entry=$entry&action=visitors"));
	}
}elseif($action == 'visitors'){
	backnav('bannedip','cfg');
	$page = empty($page) ? 1 : max(1, intval($page));
	submitcheck('bfilter') && $page = 1;
	$keyword = empty($keyword) ? '' : $keyword;
	$robot = !isset($robot)? '-1' : max(-1, intval($robot));

	$fromsql = "FROM {$tblprefix}visitors";
	$wheresql = "";
	$robot != '-1' && $wheresql .= " AND robot='$robot'";
	$keyword && $wheresql .= " AND (url ".sqlkw($keyword)." OR onlineip ".sqlkw($keyword).")";

	$filterstr = '';
	foreach(array('keyword',) as $k) $$k && $filterstr .= "&$k=".rawurlencode(stripslashes($$k));
	foreach(array('robot',) as $k) $$k != -1 && $filterstr .= "&$k=".$$k;
	$wheresql = $wheresql ? 'WHERE '.substr($wheresql,5) : '';
	
	echo form_str('visitors',"?entry=$entry&action=$action&page=$page");
	tabheader_e();
	echo "<tr><td colspan=\"2\" class=\"txt txtleft\">";
	echo "<input class=\"text\" name=\"keyword\" type=\"text\" value=\"$keyword\" size=\"8\" style=\"vertical-align: middle;\" title=\"�������ӻ�IP\">&nbsp; ";
	$arr = array('-1' => '��������','0' => '��','1' => '��',);
	echo "<select name=\"robot\">".makeoption($arr,$robot)."</select>&nbsp; ";
	echo strbutton('bfilter','ɸѡ');
	echo "</td></tr>";
	tabfooter();

	tabheader("������ʼ�¼ [".(empty($vs_holdtime) ? '��¼ͳ���ѹر�' : "����ͳ��$vs_holdtime �����ڼ�¼")."]&nbsp; &nbsp; >><a href=\"?entry=$entry&action=vsconfig\" onclick=\"return floatwin('open_visitors',this)\">����</a>",'','',12);
	$cy_arr = array('���',array('�ܷ�ҳ��','txtL'),'IP','��ַ','����','������Ϣ','����ʱ��');
	trcategory($cy_arr);

	$pagetmp = $page;
	do{
		$query = $db->query("SELECT * $fromsql $wheresql ORDER BY createdate DESC LIMIT ".(($pagetmp - 1) * $atpp).",$atpp");
		$pagetmp--;
	} while(!$db->num_rows($query) && $pagetmp);
	$count = $db->result_one("SELECT count(*) $fromsql $wheresql");
	$ii = $count - $pagetmp * $atpp + 1;
	while($r = $db->fetch_array($query)){
		$ii --;
		$urlstr = "<a href=\"$r[url]\" target=\"_blank\" title=\"$r[url]\">".cls_string::CutStr(($u = preg_replace(u_regcode($cms_abs),'',$r['url'])) ? $u : $cms_abs,50)."</a>";
		$ugstr = $r['useragent'];
		$address = cls_ipAddr::conv($r['onlineip']);
		$robotstr = empty($r['robot']) ? '-' : 'Y';
		$createdate = $r['createdate'] ? date('H:i:s',$r['createdate']) : '-';
		echo "<tr class=\"txt\"><td class=\"txtC\">$ii</td>\n";
		echo "<td class=\"txtL\">$urlstr</td>\n";
		echo "<td class=\"txtC\">$r[onlineip]</td>\n";
		echo "<td class=\"txtC\">$address</td>\n";
		echo "<td class=\"txtC\">$robotstr</td>\n";
		echo "<td class=\"txtC\"><input type=\"text\" size=\"25\" value=\"$ugstr\"></td>\n";
		echo "<td class=\"txtC\">$createdate</td>\n";
		echo "</tr>\n";
	}
	tabfooter();
	echo multi($count, $atpp, $page, "?entry=$entry&action=$action$filterstr");
}

?>