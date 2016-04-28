<?php
(!defined('M_COM') || !defined('M_ADMIN')) && exit('No Permission');
aheader();
if($re = $curuser->NoBackFunc('database')) cls_message::show($re);
if(empty($action)) $action = 'ddrecords';
if($action == 'ddconfig'){
	if(!submitcheck('bmconfigs')){
		tabheader('SQL�������','cfdebug',"?entry=$entry&action=$action");
		trbasic('�Ƿ��ռ�ҳ��SQL��¼','mconfigsnew[debugenabled]',empty($mconfigs['debugenabled']) ? 0 : $mconfigs['debugenabled'],'radio',array('guide' => '���ڷ���ϵͳЧ��ʱʹ�ã��ռ���¼��������ӷ�����������ƽʱ�뱣�ֹر�'));
		trbasic('���������̨����Ա����','mconfigsnew[debugadmin]',empty($mconfigs['debugadmin']) ? 0 : $mconfigs['debugadmin'],'radio',array('guide' => 'Ĭ�ϲ��ռ������̨����Ա���ĵĲ�ѯ��¼'));
		trbasic('�ռ��������ٺ�ʱ�Ĳ�ѯ','mconfigsnew[debuglow]',empty($mconfigs['debuglow']) ? 0 : $mconfigs['debuglow'],'text',array('guide' => '��λ:ms������Ϊ�ռ����в�ѯ'));
		trbasic('��վɲ�ѯ��¼','debugclear',0,'radio',array('guide' => 'ϵͳĬ������ռ�5����SQL��¼���ڿ�ʼ����ǰ�뾡�����֮ǰ�ľɼ�¼'));
		tabfooter('bmconfigs');
	}else{
		$mconfigsnew['debuglow'] = max(0,intval($mconfigsnew['debuglow']));
		saveconfig('debug');
		if(!empty($debugclear)) $db->query("TRUNCATE TABLE {$tblprefix}dbdebugs");
		cls_message::show('SQL����������',axaction(6,"?entry=$entry"));
	}
}elseif($action == 'ddrecords'){
	backnav('data','dbdebug');
	$page = empty($page) ? 1 : max(1, intval($page));
	submitcheck('bfilter') && $page = 1;
	$ddsql = empty($ddsql) ? '' : trim($ddsql);
	$ddurl = empty($ddurl) ? '' : trim($ddurl);
	$ddtpl = empty($ddtpl) ? '' : trim($ddtpl);
	$ddtag = empty($ddtag) ? '' : trim($ddtag);
	$inddused = empty($inddused) ? 0 : max(0,intval($inddused));
	$outddused = empty($outddused) ? 0 : max(0,intval($outddused));

	$fromsql = "FROM {$tblprefix}dbdebugs";
	$wheresql = "";
	$ddsql && $wheresql .= " AND (ddsql ".sqlkw($ddsql)." OR ddtbl ".sqlkw($ddsql).")";
	$ddurl && $wheresql .= " AND ddurl ".sqlkw($ddurl);
	$ddtpl && $wheresql .= " AND ddtpl ".sqlkw($ddtpl);
	$ddtag && $wheresql .= " AND ddtag ".sqlkw($ddtag);
	$inddused && $wheresql .= " AND ddused<'$inddused'";
	$outddused && $wheresql .= " AND ddused>'$outddused'";

	$filterstr = '';
	foreach(array('ddsql','ddurl','ddtpl','ddtag','inddused','outddused',) as $k) $$k && $filterstr .= "&$k=".rawurlencode(stripslashes($$k));
	$wheresql = $wheresql ? 'WHERE '.substr($wheresql,5) : '';
	
	if(!submitcheck('bsubmit')){
		echo form_str('ddrecords',"?entry=$entry&action=$action&page=$page");
		tabheader_e();
		echo "<tr><td colspan=\"2\" class=\"txt txtleft\">";
		echo "��ѯ:<input class=\"text\" name=\"ddsql\" type=\"text\" value=\"$ddsql\" size=\"20\" style=\"vertical-align: middle;\" title=\"������ѯ�����ݿ�\">&nbsp; ";
		echo "URL:<input class=\"text\" name=\"ddurl\" type=\"text\" value=\"$ddurl\" size=\"20\" style=\"vertical-align: middle;\">&nbsp; ";
		echo "ģ��:<input class=\"text\" name=\"ddtpl\" type=\"text\" value=\"$ddtpl\" size=\"15\" style=\"vertical-align: middle;\">&nbsp; ";
		echo "��ʶ:<input class=\"text\" name=\"ddtag\" type=\"text\" value=\"$ddtag\" size=\"15\" style=\"vertical-align: middle;\">&nbsp; ";
		echo "��ѯ��ʱ:<input class=\"text\" name=\"outddused\" type=\"text\" value=\"".($outddused ? $outddused : '')."\" size=\"4\" style=\"vertical-align: middle;\">-";
		echo "<input class=\"text\" name=\"inddused\" type=\"text\" value=\"".($inddused ? $inddused : '')."\" size=\"4\" style=\"vertical-align: middle;\">ms&nbsp; ";
		echo strbutton('bfilter','ɸѡ');
		echo "</td></tr>";
		tabfooter();
	
		tabheader("SQL��Ϸ��� [".(empty($debugenabled) ? 'ͳ�ƹر���' : 'ͳ�ƿ�����')."]&nbsp; &nbsp; >><a href=\"?entry=$entry&action=ddconfig\" onclick=\"return floatwin('open_ddrecords',this)\">����</a>",'','',12);
		$cy_arr = array('���','SQL���|L','�������ݿ�|L|H','��ʱ(ms)','ģ��/��ʶ|L','ҳ��URL|L','�ܷ�ҳ��');
        /**
         * include/debug.cls.php�ﰴ��53��ע�ʹ�������������ʾ��ʾ�����Ϣ
         * ���ӡ��Ϣ�����ڣ�/include/userbase.cls.php : 272 �ܾ�ȷ���ĸ��ļ���һ��
         */ 
		#$cy_arr = array('���','SQL���|L','�������ݿ�|L|H','��ʱ(ms)','ҳ��/����|C','ҳ��URL|L','�ܷ�ҳ��');
		trcategory($cy_arr);
	
		$pagetmp = $page;
		do{
			$query = $db->query("SELECT * $fromsql $wheresql ORDER BY ddid DESC LIMIT ".(($pagetmp - 1) * $atpp).",$atpp");
			$pagetmp--;
		} while(!$db->num_rows($query) && $pagetmp);
		$count = $db->result_one("SELECT count(*) $fromsql $wheresql");
		$ii = $count - $pagetmp * $atpp + 1;
		// ��ʽ����,���ڸ��Ʒ���,����������
		$arr = array("INNER JOIN","WHERE","ORDER BY","GROUP BY","LIMIT","FORCE INDEX",);
		while($r = $db->fetch_array($query)){
			$sql = preg_replace("/\s+/", " ", $r['ddsql']); //���˶���س� 
			foreach($arr as $v) $sql = str_replace($v,"\n$v",$sql);	
			//$sql = str_replace(array("    ","   ","  ")," ",$sql);
			$sql = str_replace(") AND",") \n AND",$sql); 
			if("$r[ddtag]"){
				$ddtag = "$r[ddtag]";
				if(is_file(cls_tpl::TemplateTypeDir('tag')."ctag$ddtag.cac.php")){
					$ddtag = "<a href=\"?entry=mtags&action=mtagsdetail&ttype=ctag&tname=$ddtag\" onclick=\"return floatwin('open_mtagsedit',this)\">$ddtag</a>";
				}
			}else $ddtag = '-';
			$ii --;
			echo "<tr class=\"txt\"><td class=\"txtC\">$ii</td>\n";
			echo "<td class=\"txtL\"><textarea class=\"js-resize\" style=\"width:360px;height:68px\" >$sql</textarea></td>\n";
			echo "<td class=\"txtL\">$r[ddtbl]</td>\n";
			echo "<td class=\"txtC\">$r[ddused]</td>\n";
			echo "<td class=\"txtL\">".($r['ddtpl'] ? "<a href=\"?entry=mtpls&action=mtpldetail&tplname=$r[ddtpl]\" onclick=\"return floatwin('open_mtplsedit',this)\">$r[ddtpl]</a>" : '-')."<br>$ddtag</td>\n"; 
            # ��/include/debug.cls.php�ﰴ��53��ע�ʹ�������������ʾ��ʾ�����Ϣ
			#echo "<td class=\"txtC\">".$r['ddtpl']."</td>\n"; 
			echo "<td class=\"txtL\"><textarea class=\"js-resize\" style=\"width:180px;height:60px\">$r[ddurl]</textarea></td>\n";
			echo "<td class=\"txtC\"><a href=\"$r[ddurl]\" target=\"_blank\">>>�鿴</a><br>".($r['ddate'] ? date('Y-m-d',$r['ddate']) : '-')."<br>".($r['ddate'] ? date('H:i:s',$r['ddate']) : '-')."</td>\n";
			echo "</tr>\n";
		}
		tabfooter();
		echo multi($count, $atpp, $page, "?entry=$entry&action=$action$filterstr");
		echo '<br><br>'.strbutton('bsubmit','�����¼');
	}else{
		$db->query("DELETE $fromsql $wheresql");
		cls_message::show('��¼����ɹ�',"?entry=$entry&action=$action$filterstr");
	}
}

?>