<?
!defined('M_COM') && exit('No Permission');
$arid = 4;$schid = 2;$tchid = 3;
if(!($abrel = cls_cache::Read('abrel',$arid)) || empty($abrel['available'])) cls_message::show('�����ڻ�رյĺϼ���Ŀ��');
if($curuser->info['mchid'] != $tchid) cls_message::show('����ע��Ϊ���͹�˾��');
if(empty($deal)){
	$page = !empty($page) ? max(1, intval($page)) : 1;
	submitcheck('bfilter') && $page = 1;
	$incheck = empty($incheck) ? 0 : 1;
	$keyword = empty($keyword) ? '' : $keyword;
	$wheresql = "WHERE m.mchid='$schid'  AND pid$arid='$memberid' AND incheck$arid='$incheck'";
	$fromsql = "FROM {$tblprefix}members m";
	$keyword && $wheresql .= " AND m.mname LIKE '%".str_replace(array(' ','*'),'%',addcslashes($keyword,'%_'))."%'";
	$filterstr = '';
	foreach(array('incheck','keyword',) as $k) $$k && $filterstr .= "&$k=".rawurlencode(stripslashes($$k));
	
	if(!submitcheck('bsubmit')){
		echo form_str($action.'newform',"?action=$action&page=$page");
		backnav('agents',"incheck$incheck");
		tabheader_e();
		trhidden('incheck',$incheck);
		echo "<tr><td class=\"item2\">";
		echo "<input class=\"text\" name=\"keyword\" type=\"text\" value=\"$keyword\" size=\"20\" placeholder=\"���������\" style=\"vertical-align: middle;\" title=\"�����û���\">&nbsp; ";
		echo strbutton('bfilter','ɸѡ');
		echo "</td></tr>";
		tabfooter();
		//�б���
		$num = $db->result_one("SELECT count(*) $fromsql WHERE m.mchid='$schid'  AND pid$arid='$memberid' AND incheck$arid='".($incheck ? 0 : 1)."'");
		$num = $num ? $num : 0;
		tabheader(($incheck ? "��˾��ʽ������" : "��˾���󾭼���")." &nbsp;<a href=\"?action=$action".($incheck ? '' : "&incheck=1")."\">>>".($incheck ? '���󾭼�' : '��ʽ����')."</a>($num)",'','',10);
		$cy_arr = array("<input class=\"checkbox\" type=\"checkbox\" name=\"chkall\" onclick=\"checkall(this.form, 'selectid', 'chkall')\">",array('������','left'),);
		$cy_arr[] = '�������';
		$cy_arr[] = '�ֽ�';
		$cy_arr[] = '���ַ�Դ�ϼ�';
		$cy_arr[] = '�ֿ�';
		$cy_arr[] = '���⻧Դ�ϼ�';
		$cy_arr[] = '�ֿ�';
		$cy_arr[] = '�ʽ����';
		trcategory($cy_arr);
	
	
		$pagetmp = $page;
		do{
			$query = $db->query("SELECT * $fromsql $wheresql ORDER BY mid DESC LIMIT ".(($pagetmp - 1) * $mrowpp).",$mrowpp");
			$pagetmp--;
		} while(!$db->num_rows($query) && $pagetmp);
	
		$itemstr = '';
		while($r = $db->fetch_array($query)){
			$selectstr = "<input class=\"checkbox\" type=\"checkbox\" name=\"selectid[$r[mid]]\" value=\"$r[mid]\">";
			$r['mspacehome'] = cls_Mspace::IndexUrl($r);
			$mnamestr = $r['mid'] ? "<a href=\"$r[mspacehome]\" target=\"_blank\">$r[mname]</a>" : $r['mname'];
			$lastvisitstr = $r['lastvisit'] ? date('Y-m-d',$r['lastvisit']) : '-';
			$zjstr = $incheck ? "<a href=\"?action=zijing&deal=fp&mid=$r[mid]\" onclick=\"return floatwin('open_memberedit',this)\">����</a> &nbsp;<a href=\"?action=zijing&deal=cq&mid=$r[mid]\" onclick=\"return floatwin('open_memberedit',this)\">��ȡ</a>" : '-';
	
			$itemstr .= "<tr><td class=\"item\" width=\"40\">$selectstr</td><td class=\"item2\">$mnamestr</td>\n";
			$itemstr .= "<td class=\"item\">$lastvisitstr</td>\n";
			$itemstr .= "<td class=\"item\">$r[currency0]</td>\n";
			$onnum = cls_DbOther::ArcLimitCount(3, 'enddate', 'valid', $r['mid']); 
			$itemstr .= "<td class=\"item\">".(empty($onnum) ? '-' : $onnum)."</td>\n";
			$totalnum = cls_DbOther::ArcLimitCount(3, '', '', $r['mid']); 
			$storenum = $totalnum - $onnum;
			$itemstr .= "<td class=\"item\">".(empty($storenum) ? '-' : $storenum)."</td>\n";
			$onnum = cls_DbOther::ArcLimitCount(2, 'enddate', 'valid', $r['mid']); 
			$itemstr .= "<td class=\"item\">".(empty($onnum) ? '-' : $onnum)."</td>\n";
			$totalnum = cls_DbOther::ArcLimitCount(2, '', '', $r['mid']); 
			$storenum = $totalnum - $onnum;
			$itemstr .= "<td class=\"item\">".(empty($storenum) ? '-' : $storenum)."</td>\n";
			$itemstr .= "<td class=\"item\">$zjstr</td>\n";
			$itemstr .= "</tr>\n";
		}
		echo $itemstr;
		tabfooter();
		echo multi($db->result_one("SELECT count(*) $fromsql $wheresql"),$mrowpp,$page, "?action=$action$filterstr");
		
		//������
		tabheader('��������');
		$s_arr = array();
		$s_arr['exit'] = 'ɾ��';
		!$incheck && $s_arr['check'] = '���';
		$incheck && $s_arr['uncheck'] = '����';
		if($s_arr){
			$soperatestr = '';
			foreach($s_arr as $k => $v) $soperatestr .= "<label><input class=\"checkbox\" type=\"checkbox\" name=\"arcdeal[$k]\" value=\"1\"".($k=='delete'?' onclick="deltip()"':'').">$v</label> &nbsp;";
			trbasic('ѡ�������Ŀ','',$soperatestr,'');
		}
		tabfooter('bsubmit');
	
	}else{
		if(empty($arcdeal)) cls_message::show('��ѡ�������Ŀ',"?action=$action&page=$page$filterstr");
		if(empty($selectid)) cls_message::show('��ѡ���Ա',"?action=$action&page=$page$filterstr");
	
		$actuser = new cls_userinfo;
		foreach($selectid as $id){
			$id = empty($id)?0:max(0,intval($id));
			$actuser->activeuser($id);
			if(!empty($arcdeal['exit'])){
				$actuser->exit_comp();
				continue;
			}
			if(!empty($arcdeal['check'])){
				$actuser->updatefield("incheck$arid",1);
			}elseif(!empty($arcdeal['uncheck'])){
				$actuser->updatefield("incheck$arid",0);
			}
			$actuser->updatedb();
		}
		unset($actuser);
		cls_message::show('��Ա�������',"?action=$action&page=$page$filterstr");
	}
}elseif($deal == 'zj'){
	echo $deal;
}

?>
