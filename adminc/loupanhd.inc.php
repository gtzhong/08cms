<?
//??????????????????��ν���������ҳ��������Ĺ�ϵ,�����ҳ��������ֻ��һ�ֲ������ǿ���֧�ֵġ�
!defined('M_COM') && exit('No Permission');

$cotypes = cls_cache::Read('cotypes');
$catalogs = cls_cache::Read('catalogs');
$chid = 5;

$caid = empty($caid) ? 0 : max(0,intval($caid));
$page = !empty($page) ? max(1, intval($page)) : 1;
submitcheck('bfilter') && $page = 1;
$chid = empty($chid) ? 0 : max(0,intval($chid));
$pid = empty($pid) ? 0 : max(0,intval($pid));
$keyword = empty($keyword) ? '' : $keyword;

$wheresql = "";
$fromsql = "FROM {$tblprefix}".atbl(5)." a ";
//��Ҫ���ǽ�ɫ����Ŀ����Ȩ��
$caids = array(-1);
if($caid) $caids = sonbycoid($caid);
if(!$caids) $no_list = true;
elseif(!in_array(-1,$caids) && $cnsql = cnsql(0,$caids,'a.')) $wheresql .= " AND $cnsql";
if($chid){
	$wheresql .= " AND a.chid='$chid'";
}
if($pid){
	$wheresql .= " AND a.pid3='$pid'";	
}
$keyword && $wheresql .= " AND (a.mname LIKE '%".str_replace(array(' ','*'),'%',addcslashes($keyword,'%_'))."%' OR a.subject LIKE '%".str_replace(array(' ','*'),'%',addcslashes($keyword,'%_'))."%')";
$filterstr = '';
foreach(array('caid','chid','keyword','pid') as $k) $$k && $filterstr .= "&$k=".rawurlencode(stripslashes($$k));
//��ϵɸѡ
foreach($cotypes as $k => $v){
	${"ccid$k"} = isset(${"ccid$k"}) ? max(0,intval(${"ccid$k"})) : 0;
	if(!empty(${"ccid$k"})){
		if($cnsql = cnsql($k,sonbycoid(${"ccid$k"},$k),'a.')) $wheresql .= " AND $cnsql";
		$filterstr .= "&ccid$k=".${"ccid$k"};
	}
}
$wheresql = empty($no_list) ? ($wheresql ? 'WHERE '.substr($wheresql,5) : '') : 'WHERE 0';

if(!submitcheck('bsubmit')){		
	echo form_str('arcsedit',"?action=$action&page=$page");
	//ĳЩ�̶�ҳ�����
	trhidden('caid',$caid);	
	trhidden('pid',$pid);
	tabheader_e();
	echo "<tr><td colspan=\"2\" class=\"txt txtleft\">";
	echo "�ؼ���&nbsp; <input class=\"text\" name=\"keyword\" type=\"text\" value=\"$keyword\" size=\"8\" style=\"vertical-align: middle;\">&nbsp; ";
	//��Ŀ����
	echo '<span>'.cn_select("ccid1",array('value' => $ccid1,'coid' => 1,'notip' => 1,'addstr' => '���޵���','vmode' => 0,'framein' => 1,)).'</span>&nbsp; ';
	echo strbutton('bfilter','ɸѡ');
	tabfooter();
	//�б���	
	tabheader("�����б�",'','',9);
	$cy_arr = array("<input class=\"checkbox\" type=\"checkbox\" name=\"chkall\" onclick=\"checkall(this.form, 'selectid', 'chkall')\">",array('����','txtL'),);
	foreach($cotypes as $k => $v) if(!$v['self_reg'] && $k==1) $cy_arr["ccid$k"] = $v['cname'];
	$cy_arr[] = '��������';
	$cy_arr[] = '���ʱ��';
	$cy_arr[] = '����ʱ��';
	$cy_arr[] = '����';
	$cy_arr[] = '�鿴';
	trcategory($cy_arr);

	$pagetmp = $page;
	do{
		$query = $db->query("SELECT a.* $fromsql $wheresql ORDER BY a.inorder3 ASC,a.aid DESC LIMIT ".(($pagetmp - 1) * $atpp).",$atpp");
		$pagetmp--;
	} while(!$db->num_rows($query) && $pagetmp);

	$itemstr = '';
	while($r = $db->fetch_array($query)){
		$channel = cls_cache::Read('channel',$r['chid']);
		$selectstr = "<input class=\"checkbox\" type=\"checkbox\" name=\"selectid[$r[aid]]\" value=\"$r[aid]\">";
		$subjectstr = ($r['thumb'] ? '[ͼ]'.' &nbsp;' : '')."<a href=".cls_ArcMain::Url($r)." target=\"_blank\">".cls_string::CutStr(mhtmlspecialchars($r['subject']),36)."</a>";
		$catalogstr = @$catalogs[$r['caid']]['title'];
		$channelstr = @$channel['cname'];
		$orderstr = "<a href=\"?action=dfangs&aid=$r[aid]\" onclick=\"return floatwin('open_arcexit',this)\">(".$r['awgs'].")</a>";
		$orderstr .= "<a href=\"etools/dfang.php?aid=$r[aid]\" onclick=\"return floatwin('open_arcexit',this)\">���</a>";
		$viewstr = "<a id=\"_info_$r[aid]\" href=\"?action=hdinfo&aid=$r[aid]\" onclick=\"return showInfo(this.id,this.href)\">�鿴</a>";
		$editstr = "<a href=\"?entry=extend&extend=dinggouarchive&aid=$r[aid]\" onclick=\"return floatwin('open_arcexit',this)\">�༭</a>";
		foreach($cotypes as $k => $v){
			${'ccid'.$k.'str'} = '';
			$r['ccid'.$k] = empty($r['ccid'.$k])?'':$r['ccid'.$k];
			if(!$v['self_reg'] && $r['ccid'.$k]){
				${'ccid'.$k.'str'} = cls_catalog::cnstitle($r['ccid'.$k],$v['asmode'],cls_cache::Read('coclasses',$k));
			}
		}
		$vieworderstr = $r['vieworder'];
		$adddatestr = $r['createdate'] ? date('Y-m-d',$r['createdate']) : '-';
		$enddatestr = $r['enddate'] ? date('Y-m-d',$r['enddate']) : '-';
		$inorderstr = "<input type=\"text\" size=\"5\" maxlength=\"5\" name=\"albumsnew[".$r['aid']."][inorder3]\" value=\"".$r['inorder3']."\">";
		$itemstr .= "<tr class=\"txt\" align=\"center\"><td class=\"txtC w40\" >$selectstr</td><td class=\"txtL\">$subjectstr</td>\n";
		foreach($cotypes as $k => $v) if(!$v['self_reg'] && $k==1) $itemstr .= "<td class=\"txtC\">".${'ccid'.$k.'str'}."</td>\n";
		$itemstr .= "<td class=\"txtC w100\">$inorderstr</td>\n";
		$itemstr .= "<td class=\"txtC w100\">$adddatestr</td>\n";
		$itemstr .= "<td class=\"txtC w100\">$enddatestr</td>\n";
		$itemstr .= "<td class=\"txtC w35\">$orderstr</td>\n";	
		$itemstr .= "<td class=\"txtC w35\">$viewstr</td>\n";		
		$itemstr .= "</tr>\n";
	}

	$counts = $db->result_one("SELECT count(*) $fromsql $wheresql");
	$multi = multi($counts, $atpp, $page, "?action=$action$filterstr");
	echo $itemstr;
	tabfooter();
	echo $multi;

	//������
	tabheader('������Ŀ');
	$s_arr = array();
	$s_arr['delete'] = 'ɾ��';
	if($s_arr){
		$soperatestr = '';
		$i = 1;
		foreach($s_arr as $k => $v){
			$soperatestr .= "<input class=\"checkbox\" type=\"checkbox\" name=\"arcdeal[$k]\" value=\"1\"".($k=='delete'?' onclick="deltip()"':'').">$v &nbsp;";
			if(!($i % 5)) $soperatestr .= '<br>';
			$i ++;
		}
		trbasic('ѡ�������Ŀ','',$soperatestr,'');
	}
	tabfooter('bsubmit');
		
}else{
	if(empty($arcdeal) && empty($albumsnew)) cls_message::show('��ѡ�������Ŀ',axaction(0,M_REFERER));
	if(empty($selectid) && empty($albumsnew)) cls_message::show('��ѡ���ĵ�',axaction(0,M_REFERER));
	$arc = new cls_arcedit;
	if(!empty($albumsnew)){
		foreach($albumsnew as $k => $v) $db->query("UPDATE {$tblprefix}".atbl($chid)." SET inorder3='".max(0,intval($v['inorder3']))."' WHERE aid='$k'");
	}
	if(!empty($selectid)){
		foreach($selectid as $aid){
			$arc->set_aid($aid);
			if(!empty($arcdeal['delete'])){
				$arc->arc_delete();
				continue;
			}
			$arc->updatedb();
			$arc->init();
		}
	}
	unset($arc);

	cls_message::show('�ĵ��������',axaction(0,M_REFERER));
}
?>
