<?
(!defined('M_COM') || !defined('M_ADMIN')) && exit('No Permission');
aheader();
backallow('normal') || cls_message::show('��û�е�ǰ��Ŀ�Ĺ���Ȩ�ޡ�');

$mchannels = cls_cache::Read('mchannels');
$catalogs = cls_cache::Read('catalogs');
$grouptypes = cls_cache::Read('grouptypes');

$mchid = empty($mchid) ? 0 : max(0,intval($mchid));
$page = !empty($page) ? max(1, intval($page)) : 1;
submitcheck('bfilter') && $page = 1;
$viewdetail = empty($viewdetail) ? 0 : 1;
$checked = empty($checked)?'0':($checked == 1?'1':'-1');
$grouptype34 = isset($grouptype34) ? $grouptype34 : '106';
$keyword = empty($keyword) ? '' : $keyword;
$indays = empty($indays) ? 0 : max(0,intval($indays));
$outdays = empty($outdays) ? 0 : max(0,intval($outdays));
$wheresql = '';
$fromsql = "FROM {$tblprefix}members m INNER JOIN {$tblprefix}members_sub s ON s.mid=m.mid";

$caid = empty($caid) ? 0 : $caid;

//���ͷ�Χ
if(!empty($mchid)){
	if(!array_intersect(array(-1,$mchid),$a_mchids)) $no_list = 1;
	else $wheresql .= " AND m.mchid='$mchid'";
}elseif(empty($a_mchids)){
	$no_list = 1;
}elseif(!in_array(-1,$a_mchids) && $a_mchids) $wheresql .= ($wheresql ? ' AND ' : '')."m.mchid ".multi_str($a_mchids);
if($grouptype34 != -1) $wheresql .= " AND m.grouptype34='$grouptype34'";
//�����ؼ��ʴ���
$mode_keyword = empty($mode_keyword) ? 'ming' : $mode_keyword;
if($keyword){
	if(in_array($mode_keyword,array('ming','mname','mid'))) {
		$mode = $mode_keyword == 'ming' ? "s.$mode_keyword" : "m.$mode_keyword";
		$keyword && $wheresql .= " AND ($mode ".sqlkw($keyword).")";
	
	}
}
$indays && $wheresql .= " AND m.regdate>'".($timestamp - 86400 * $indays)."'";
$outdays && $wheresql .= " AND m.regdate<'".($timestamp - 86400 * $outdays)."'";

$filterstr = '';
foreach(array('mchid','keyword','indays','outdays',) as $k) $$k && $filterstr .= "&$k=".rawurlencode(stripslashes($$k));
foreach(array('checked',) as $k) $$k != -1 && $filterstr .= "&$k=".$$k;
foreach(array('grouptype34',) as $k) $$k != -1 && $grouptype34 .= "&$k=".$$k;

$grouptypes = cls_cache::Read('grouptypes');
foreach($grouptypes as $k => $v){
	${"ugid$k"} = empty(${"ugid$k"}) ? 0 : ${"ugid$k"}; 
	if(${"ugid$k"}){
		$filterstr .= "&ugid$k=".${"ugid$k"};
		$wheresql .= " AND m.grouptype$k='".${"ugid$k"}."'";
	}
}
$wheresql .= (empty($caid) || $caid==516) ? '' : " AND s.quaere like '%$caid%'";
//$wheresql .= (empty($mchid) || $mchid==516) ? '' : " AND m.mchid='$mchid'";
$wheresql = empty($no_list) ? ($wheresql ? 'WHERE '.substr($wheresql,5) : '') : 'WHERE 0';
//echo $wheresql;

if(!submitcheck('bsubmit')){
	
	echo form_str($actionid.'memberedit',"?entry=$entry$extend_str&page=$page&mchid=$mchid");
	tabheader_e();
	trhidden('mchid',$mchid);
	echo "<tr><td colspan=\"2\" class=\"txt txtleft\">";
	echo "<select style=\"vertical-align: middle;\" name=\"mode_keyword\">".makeoption(array('ming' => 'ר������','mname' => 'ר���˺�','mid' => 'ר��ID'),$mode_keyword)."</select>&nbsp; ";
	echo "<input class=\"text\" name=\"keyword\" type=\"text\" value=\"$keyword\" size=\"8\" style=\"vertical-align: middle;\" title=\"�����û���\">&nbsp; ";
	echo "<input class=\"text\" name=\"outdays\" type=\"text\" value=\"$outdays\" size=\"4\" style=\"vertical-align: middle;\" title=\"ע��\">��ǰ&nbsp; ";
	echo "<input class=\"text\" name=\"indays\" type=\"text\" value=\"$indays\" size=\"4\" style=\"vertical-align: middle;\" title=\"ע��\">����&nbsp; ";

	echo strbutton('bfilter','ɸѡ');
	echo "</td></tr>";
	tabfooter();
	//�б���	
	tabheader("��Ա�б�",'','',10);
	$cy_arr = array("<input class=\"checkbox\" type=\"checkbox\" name=\"chkall\" onclick=\"checkall(this.form, 'selectid', 'chkall')\">",array('��Ա����','txtL'),);
	$cy_arr[] = 'ר������';
	$cy_arr[] = '�ó�����';
	$cy_arr[] = '�Ƽ�ר��';
	$cy_arr[] = '��Ա����';
	$cy_arr[] = 'ע��IP';
	$cy_arr[] = 'ע������';
	$cy_arr[] = '����';
	$cy_arr[] = '��Ա��';
	$cy_arr[] = '����';
	$cy_arr[] = '����';
	trcategory($cy_arr);


	$pagetmp = $page; //echo "SELECT m.*,s.* $fromsql $wheresql";
	do{
		$query = $db->query("SELECT m.*,s.* $fromsql $wheresql ORDER BY m.mid DESC LIMIT ".(($pagetmp - 1) * $atpp).",$atpp");
		$pagetmp--;
	} while(!$db->num_rows($query) && $pagetmp);

	$itemstr = '';
	while($r = $db->fetch_array($query)){ // info.php?fid=107&mid=767
		$selectstr = "<input class=\"checkbox\" type=\"checkbox\" name=\"selectid[$r[mid]]\" value=\"$r[mid]\">";
		$mnamestr ="<a href='{$cms_abs}info.php?fid=107&mid=$r[mid]' target=\"_blank\">". $r['mname'].($r['isfounder'] ? '-��ʼ��': '').'</a>';
		$mingstr = $r['ming'] ? ($r['grouptype34'] ? "$r[ming]" : "<span class='tips1' title='δ���'>$r[ming]</span>") : '-';
		
		$mchannelstr = @$mchannels[$r['mchid']]['cname'];
		$checkstr = $r['checked'] == 1 ? 'Y' : '-';
		foreach($grouptypes as $k => $v){
			${'ugid'.$k.'str'} = '-';
			if($r['grouptype'.$k]){
				$usergroups = cls_cache::Read('usergroups',$k);
				${'ugid'.$k.'str'} = @$usergroups[$r['grouptype'.$k]]['cname'];
			}
		}
		if($r['quaere']){
				$sc_quaere = '';$gap = '';
				foreach(explode(',',$r['quaere']) as $x){
					if($x){
						$sc_quaere .= $gap.cls_catalog::cnstitle($x,0,$catalogs);
						$gap = ',';
					}
				}			
		}else{ $sc_quaere = '-'; }
		$regipstr = $r['regip'];
		$regdatestr = $r['regdate'] ? date('Y-m-d',$r['regdate']) : '-';
		$lastvisitstr = $r['lastvisit'] ? date('Y-m-d',$r['lastvisit']) : '-';
		$viewstr = "<a id=\"{$actionid}_info_$r[mid]\" href=\"?entry=extend&extend=memberinfo&mid=$r[mid]\" onclick=\"return showInfo(this.id,this.href)\">�鿴</a>";
		$editstr = "<a href=\"?entry=extend&extend=memberexpert&mid=$r[mid]\" onclick=\"return floatwin('open_memberedit',this)\">����</a>";
		$groupstr = "<a href=\"?entry=extend&extend=membergroup&mid=$r[mid]\" onclick=\"return floatwin('open_memberedit',this)\">��Ա��</a>";

		$itemstr .= "<tr class=\"txt\"><td class=\"txtC w40\" >$selectstr</td><td class=\"txtL\">$mnamestr</td>\n";
		$itemstr .= "<td class=\"txtC\">$mingstr</td>\n";
		$itemstr .= "<td class=\"txtC\">$sc_quaere</td>\n";

		$itemstr .= "<td class=\"txtC\">$ugid35str</td>\n";
		$itemstr .= "<td class=\"txtC\">$mchannelstr</td>\n";		
			

		$itemstr .= "<td class=\"txtC\">$regipstr</td>\n";
		
		$itemstr .= "<td class=\"txtC\">$regdatestr</td>\n";

		$itemstr .= "<td class=\"txtC\">$viewstr</td>\n";
		$itemstr .= "<td class=\"txtC\">$groupstr</td>\n";
		$itemstr .= "<td class=\"txtC\">$editstr</td>\n";
		$itemstr .= "<td class=\"txtC\"><a href=\"adminm.php?from_mid=$r[mid]\" target=\"_blank\">����</a></td>\n";
		$itemstr .= "</tr>\n";
	}
	$counts = $db->result_one("SELECT count(*) $fromsql $wheresql");
	$multi = multi($counts, $atpp, $page, "?entry=$entry$extend_str$filterstr");
	echo $itemstr;
	tabfooter();
	echo $multi;
	
	//������
	tabheader('������Ŀ');	
	foreach($grouptypes as $k => $v){
	if(in_array($k,array(34,35))){
		if(($v['mode'] < 2) && $k != 2){
			$ugidsarr = $k==34?array('0' => '�����Ա��'):array('0' => '�����Ա��') + ugidsarr($k,'',1);
			trbasic("<input class=\"checkbox\" type=\"checkbox\" name=\"arcdeal[gtid$k]\" value=\"1\">����$v[cname]",'arcugid'.$k,makeoption($ugidsarr),'select');
		}
	}}
	tabfooter('bsubmit');

}else{
	if(empty($arcdeal)) cls_message::show('��ѡ�������Ŀ',"?entry=$entry$extend_str&page=$page$filterstr");
	if(empty($selectid)) cls_message::show('��ѡ���Ա',"?entry=$entry$extend_str&page=$page$filterstr");

	$actuser = new cls_userinfo;	
	$ucdels = array();
	$clumn_arr = array('dantu','danwei','ming','quaere');
	
	foreach($selectid as $id){
		$actuser->activeuser($id);		
		foreach($grouptypes as $k => $v){
			if(($v['mode'] < 2) && !empty($arcdeal['gtid'.$k]) && $k != 2){
				$actuser->handgroup($k,${"arcugid$k"},-1);
				if($k == 34){
					foreach($clumn_arr as $h){
						$actuser->updatefield($h,'','members_sub');
					}
					$actuser->updatefield("grouptype34",0,'members');
				}
			}
		}
		$actuser->updatedb();
		$actuser->init();
	}
	unset($actuser);
	
	if($enable_uc && $ucdels){
		$uc_action = 'ucdels';include(M_ROOT.'./include/ucenter/uc.inc.php');
	}
	adminlog('��Ա����','��Ա�б�������');
	cls_message::show('��Ա�������',"?entry=$entry$extend_str&page=$page$filterstr");
}

?>
