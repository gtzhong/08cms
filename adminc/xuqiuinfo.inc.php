<?php
!defined('M_COM') && exit('No Permission');

$cotypes = cls_cache::Read('cotypes');
$channels = cls_cache::Read('channels');

$cuid = 9;
if(!($commu = cls_cache::Read('commu',$cuid)) || !$commu['available']) cls_message::show('�뿪����ע���ܡ�');
$aid = empty($aid) ? 0 : max(0,intval($aid));

if(empty($aid)){
	//ʹ��ȫ����Ŀ
	$page = !empty($page) ? max(1, intval($page)) : 1;
	submitcheck('bfilter') && $page = 1;
	$chid = empty($chid) ? 0 : max(0,intval($chid));
	in_array($chid,array(9,10)) || $chid = 0;
	$keyword = empty($keyword) ? '' : $keyword;
	$indays = empty($indays) ? 0 : max(0,intval($indays));
	$outdays = empty($outdays) ? 0 : max(0,intval($outdays));
	
	$wheresql = "WHERE a.checked='1' AND cu.cid IS NULL AND a.mid<>'$memberid' AND (a.enddate='0' OR a.enddate>'$timestamp')";
	$fromsql = "FROM {$tblprefix}archives a INNER JOIN {$tblprefix}$commu[tbl] cu ON (cu.aid=a.aid AND cu.mid='$memberid')";
	
	$wheresql .= $chid ? " AND a.chid='$chid'" : " AND a.chid IN (9,10)";
	$indays && $wheresql .= " AND a.createdate>'".($timestamp - 86400 * $indays)."'";
	$outdays && $wheresql .= " AND a.createdate<'".($timestamp - 86400 * $outdays)."'";
	$keyword && $wheresql .= " AND a.subject LIKE '%".str_replace(array(' ','*'),'%',addcslashes($keyword,'%_'))."%'";
	$filterstr = '';
	foreach(array('keyword','indays','outdays',) as $k) $$k && $filterstr .= "&$k=".rawurlencode(stripslashes($$k));
//��ϵɸѡ
	foreach($cotypes as $k => $v){
		${"ccid$k"} = isset(${"ccid$k"}) ? max(0,intval(${"ccid$k"})) : 0;
		if(!empty(${"ccid$k"})){
			if($cnsql = cnsql($k,sonbycoid(${"ccid$k"},$k),'a.')) $wheresql .= " AND $cnsql";
			$filterstr .= "&ccid$k=".${"ccid$k"};
		}
	}

	$filterstr = '';
	foreach(array('chid','keyword','indays','outdays',) as $k) $$k && $filterstr .= "&$k=".rawurlencode(stripslashes($$k));
	if(!submitcheck('bsubmit')){
		echo form_str($action.'archivesedit',"?action=$action&page=$page");
		tabheader_e();
		echo "<tr><td class=\"item2\">";
		echo "<input class=\"text\" name=\"keyword\" type=\"text\" value=\"$keyword\" size=\"8\" style=\"vertical-align: middle;\" title=\"��������Դ\">&nbsp; ";
		echo "<select style=\"vertical-align: middle;\" name=\"chid\">".makeoption(array('0' => '��������','9'=>'����','10'=>'��'),$chid)."</select>&nbsp; ";
		echo '<span>'.cn_select("ccid1",array('value' => $ccid1,'coid' => 1,'notip' => 1,'addstr' => '���޵���','vmode' => 0,'framein' => 1,)).'</span>&nbsp; ';
		echo "<input class=\"text\" name=\"outdays\" type=\"text\" value=\"$outdays\" size=\"4\" style=\"vertical-align: middle;\">��ǰ&nbsp; ";
		echo "<input class=\"text\" name=\"indays\" type=\"text\" value=\"$indays\" size=\"4\" style=\"vertical-align: middle;\">����&nbsp; ";
		echo strbutton('bfilter','ɸѡ');
		tabfooter();
	
		$pagetmp = $page;
		do{
			$query = $db->query("SELECT a.* $fromsql $wheresql ORDER BY a.aid DESC LIMIT ".(($pagetmp - 1) * $mrowpp).",$mrowpp");
			$pagetmp--;
		}while(!$db->num_rows($query) && $pagetmp);
	
		tabheader('�鿴������Ϣ','','',30);
		$cy_arr = array("<input class=\"checkbox\" type=\"checkbox\" name=\"chkall\" onclick=\"checkall(this.form, 'selectid', 'chkall')\">",array('������Ϣ', 'left'),);
		$cy_arr[] = '����';
		$cy_arr[] = '���';
		$cy_arr[] = '�۸�';
		foreach($cotypes as $k => $v) if(!$v['self_reg'] && $k==1) $cy_arr["ccid$k"] = $v['cname'];
		$cy_arr[] = '���';
		$cy_arr[] = '���ʱ��';
		$cy_arr[] = '����';
		$cy_arr[] = '����';
	
		trcategory($cy_arr);
		$itemstr = '';
		while($row = $db->fetch_array($query)){
			$channel = cls_cache::Read('channel',$row['chid']);
			$selectstr = "<input class=\"checkbox\" type=\"checkbox\" name=\"selectid[$row[aid]]\" value=\"$row[aid]\">";
			$row['arcurl'] = cls_ArcMain::Url($row);
			$subjectstr = mhtmlspecialchars($row['subject']);
			$channelstr = @$channel['cname'];
			foreach($cotypes as $k => $v){
				${'ccid'.$k.'str'} = '';
				if(!$v['self_reg'] && $row['ccid'.$k]){
					${'ccid'.$k.'str'} = cls_catalog::cnstitle($row['ccid'.$k],$v['asmode'],$coclasses = cls_cache::Read('coclasses',$k));
				}
			}
			$mjstr = $row['mj'] ? $row['mj'].'�O' : '-';
			$zjstr = $row['zj'] ? $row['zj'].($row['chid']==9 ? 'Ԫ/��' : '��Ԫ') : '-';
			$clicksstr = $row['clicks'];
			$adddatestr = $row['createdate'] ? date('Y-m-d',$row['createdate']) : '-';
			$viewstr = "<a id=\"{$action}_info_$row[aid]\" href=\"?action=archiveinfo&aid=$row[aid]\" onclick=\"return showInfo(this.id,this.href)\">�鿴</a>";
			$detailstr = "<a href=\"?action=$action&aid=$row[aid]\" onclick=\"return floatwin('open_inarchive',this)\">����</a>";
			
			$itemstr .= "<tr><td class=\"item\">$selectstr</td><td class=\"item2\">$subjectstr</td>\n";
			$itemstr .= "<td class=\"item\">$channelstr</td>\n";
			$itemstr .= "<td class=\"item\">$mjstr</td>\n";
			$itemstr .= "<td class=\"item\">$zjstr</td>\n";
			foreach($cotypes as $k => $v) if(!$v['self_reg'] && $k==1) $itemstr .= "<td class=\"item\">".${'ccid'.$k.'str'}."</td>\n";
			$itemstr .= "<td class=\"item\">$clicksstr</td>\n";
			$itemstr .= "<td class=\"item\">$adddatestr</td>\n";
			$itemstr .= "<td class=\"item\">$viewstr</td>\n";
			$itemstr .= "<td class=\"item\">$detailstr</td>\n";
			$itemstr .= "</tr>\n";
	
	
		}
		$counts = $db->result_one("SELECT count(*) $fromsql $wheresql");
		$multi = multi($counts,$mrowpp,$page,"?action=$action$filterstr");
		echo $itemstr;
		tabfooter();
		echo $multi;
		
		tabheader('������Ŀ');
		$s_arr = array();
		$s_arr['readd'] = '�����ע';
		if($s_arr){
			$soperatestr = '';
			foreach($s_arr as $k => $v) $soperatestr .= "<input class=\"checkbox\" type=\"checkbox\" name=\"arcdeal[$k]\" value=\"1\">$v &nbsp;";
			trbasic('��ѡ�������Ŀ','',$soperatestr,'');
		}
		tabfooter('bsubmit');
	}else{
		if(!$curuser->pmbypmid($commu['pmid'])) cls_message::show('��û�й�עȨ�ޡ�',M_REFERER);
		if(empty($arcdeal)) cls_message::show('��ѡ�������Ŀ',M_REFERER);
		if(empty($selectid)) cls_message::show('��ѡ��������Ϣ',M_REFERER);
		$arc = new cls_arcedit;
		foreach($selectid as $k){
			$arc->set_aid($k);
			$db->query("INSERT INTO {$tblprefix}$commu[tbl] SET aid='$k',mid='$memberid',mname='{$curuser->info['mname']}',createdate='$timestamp',checked=1",'SILENT');
			//���ִ�����ͳ�Ƶ�
			#$arc->updatedb();
		}
		unset($arc);
		cls_message::show('������ע�������',axaction(2,"?action=$action$filterstr&page=$page"));
	}
}else{
	
	
	include_once M_ROOT."./include/extends/arcedit.cls.php";
	$arc = new cls_arcedit;
	$arc->set_aid($aid,array('ch'=>1));
	!$arc->aid && cls_message::show('ѡ���ĵ�');
	$chid = $arc->archive['chid'];
	$channel = &$arc->channel;
	$fields = cls_cache::Read('fields',$chid);
	tabheader($channel['cname'].'&nbsp; -&nbsp; ��ϸ����','','');
	$a_field = new cls_field;
	foreach($cotypes as $k => $v){
		if(!$v['self_reg'] && $k==1){
			tr_cns($v['cname'],"fmdata[ccid$k]",array('value' => $arc->archive["ccid$k"],'coid' => $k,'chid' => $chid,'max' => $v['asmode'],'notblank' => $v['notblank'],'emode' => $v['emode'],'evarname' => "fmdata[ccid{$k}date]",'evalue' => @$arc->archive["ccid{$k}date"] ? date('Y-m-d',$arc->archive["ccid{$k}date"]) : '',));
		}
	}
	foreach($fields as $k => $field){
	  $a_field->init($field,isset($arc->archive[$k]) ? $arc->archive[$k] : '');
	  $a_field->trfield('fmdata');
	}
	tabfooter();
	
}

?>
