<?php
(defined('M_COM') && defined('M_ADMIN')) || exit('No Permission');
aheader();
if($re = $curuser->NoBackFunc('member')) cls_message::show($re);
foreach(array('mctypes','mchannels',) as $k) $$k = cls_cache::Read($k);
$mcmodearr = array(0 => '��ͨ',1 => '�ֻ�',);
$mctidsarr = array();foreach($mctypes as $k => $v) $mctidsarr[$k] = $v['cname'];
if(empty($action)){
	$mchid = empty($mchid) ? 0 : max(0,intval($mchid));
	$mctid = !isset($mctid) ? -1 : max(-1,intval($mctid));
	$page = empty($page) ? 1 : max(1, intval($page));
	submitcheck('bfilter') && $page = 1;
	$checked = isset($checked) ? $checked : '-1';
	$keyword = empty($keyword) ? '' : $keyword;
	$indays = empty($indays) ? 0 : max(0,intval($indays));
	$outdays = empty($outdays) ? 0 : max(0,intval($outdays));
	
	$selectsql = "SELECT mc.*,m.mchid";
	$wheresql = "";
	$fromsql = "FROM {$tblprefix}mcerts mc INNER JOIN {$tblprefix}members m ON m.mid=mc.mid";
	
	$mchid && $wheresql .= " AND m.mchid='$mchid'";
	if($mctid != -1) $wheresql .= " AND mc.mctid='$mctid'";
	$keyword && $wheresql .= " AND mc.mname ".sqlkw($keyword);
	if($checked != -1) $wheresql .= $checked ? " AND mc.checkdate<>0" : " AND mc.checkdate=0";
	$indays && $wheresql .= " AND mc.createdate>'".($timestamp - 86400 * $indays)."'";
	$outdays && $wheresql .= " AND mc.createdate<'".($timestamp - 86400 * $outdays)."'";
	
	$filterstr = '';
	foreach(array('mchid','keyword','indays','outdays',) as $k) $$k && $filterstr .= "&$k=".rawurlencode(stripslashes($$k));
	foreach(array('mctid','checked',) as $k) $$k != -1 && $filterstr .= "&$k=".$$k;
	
	$wheresql = $wheresql ? 'WHERE '.substr($wheresql,5) : '';
	if(!submitcheck('bsubmit')){
		echo form_str($actionid.'arcsedit',"?entry=$entry&page=$page");
		trhidden('mchid',$mchid);
		tabheader_e();
		echo "<tr><td colspan=\"2\" class=\"txt txtleft\">";
		echo "<input class=\"text\" name=\"keyword\" type=\"text\" value=\"$keyword\" size=\"8\" style=\"vertical-align: middle;\" title=\"������Ա\">&nbsp; ";
		echo "<select style=\"vertical-align: middle;\" name=\"mctid\">".makeoption(array(-1 => '��������') + $mctidsarr,$mctid)."</select>&nbsp; ";
		echo "<select style=\"vertical-align: middle;\" name=\"checked\">".makeoption(array('-1' => '���״̬','0' => 'δ��','1' => '����'),$checked)."</select>&nbsp; ";
		echo "<input class=\"text\" name=\"outdays\" type=\"text\" value=\"$outdays\" size=\"4\" style=\"vertical-align: middle;\">��ǰ&nbsp; ";
		echo "<input class=\"text\" name=\"indays\" type=\"text\" value=\"$indays\" size=\"4\" style=\"vertical-align: middle;\">����&nbsp; ";
		echo strbutton('bfilter','ɸѡ');
		tabfooter();
		tabheader('��֤�������','','',9);
		$cy_arr = array("<input class=\"checkbox\" type=\"checkbox\" name=\"chkall\" onclick=\"checkall(this.form, 'selectid', 'chkall')\">",array('��֤��Ա','txtL'),);
		$cy_arr[] = array('��֤����','txtL');
		$cy_arr[] = '��Ա����';
		$cy_arr[] = '����ʱ��';
		$cy_arr[] = '���ʱ��';
		$cy_arr[] = '�༭';
		trcategory($cy_arr);
		
		$pagetmp = $page;
		do{
			$query = $db->query("$selectsql $fromsql $wheresql ORDER BY mc.mcid DESC LIMIT ".(($pagetmp - 1) * $atpp).",$atpp");
			$pagetmp--;
		} while(!$db->num_rows($query) && $pagetmp);
	
		$itemstr = '';
		while($r = $db->fetch_array($query)){
			$selectstr = "<input class=\"checkbox\" type=\"checkbox\" name=\"selectid[$r[mcid]]\" value=\"$r[mcid]\">";
			$mnamestr = $r['mid'] ? "<a href=\"{$mspaceurl}index.php?mid=$r[mid]\" target=\"_blank\">$r[mname]</a>" : $r['mname'];
			$mctypestr = @$mctypes[$r['mctid']]['cname'];
			$mchannelstr = @$mchannels[$r['mchid']]['cname'];
			$adddatestr = date('Y-m-d',$r['createdate']);
			$checkdatestr = $r['checkdate'] ? date('Y-m-d',$r['checkdate']) : '-';
			$editstr = "<a href=\"?entry=$entry&action=detail&mcid=$r[mcid]\" onclick=\"return floatwin('open_commentsedit',this)\">����</a>";
	
			$itemstr .= "<tr class=\"txt\"><td class=\"txtC w40\" >$selectstr</td><td class=\"txtL\">$mnamestr</td>\n";
			$itemstr .= "<td class=\"txtL\">$mctypestr</td>\n";
			$itemstr .= "<td class=\"txtC\">$mchannelstr</td>\n";
			$itemstr .= "<td class=\"txtC w100\">$adddatestr</td>\n";
			$itemstr .= "<td class=\"txtC w100\">$checkdatestr</td>\n";
			$itemstr .= "<td class=\"txtC w35\">$editstr</td>\n";
			$itemstr .= "</tr>\n";
		}
		echo $itemstr;
		tabfooter();
		echo multi($db->result_one("SELECT count(*) $fromsql $wheresql"),$atpp,$page,"?entry=$entry$filterstr");
		
		tabheader('��������');
		$s_arr = array();
		$s_arr['delete'] = 'ɾ��';
		//$s_arr['check'] = '���';
		//$s_arr['uncheck'] = '����ɾ��';
		if($s_arr){
			$str = '';
			$i = 1;
			foreach($s_arr as $k => $v){
				$str .= "<input class=\"checkbox\" type=\"checkbox\" name=\"arcdeal[$k]\" value=\"1\"" . ($k == 'delete' ? ' onclick="deltip()"' : '') . ">$v &nbsp;";
				if(!($i % 5)) $str .= '<br>';
				$i ++;
			}
			trbasic('ѡ�������Ŀ','',$str,'');
		}
		tabfooter('bsubmit');
	}else{
		if(empty($arcdeal)) cls_message::show('��ѡ�������Ŀ��',axaction(1,M_REFERER));
		if(empty($selectid)) cls_message::show('��ѡ�����ۼ�¼��',axaction(1,M_REFERER));
		foreach($selectid as $k){
			if(!empty($arcdeal['delete'])){
				$db->query("DELETE FROM {$tblprefix}mcerts WHERE mcid='$k'",'UNBUFFERED');
				continue;
			}
		}
		adminlog('��Ա��֤�б����');
		cls_message::show('��Ա��֤���������ɹ���',"?entry=$entry&page=$page$filterstr");
		
		
	}
}elseif($action == 'detail'){
	if(empty($mcid) || !($row = $db->fetch_one("SELECT * FROM {$tblprefix}mcerts WHERE mcid='$mcid'"))) cls_message::show('��Ч����֤����');
	$au = new cls_userinfo;
	$au->activeuser($row['mid']);
	$mchid = $au->info['mchid'];
	$mfields = cls_cache::Read('mfields',$mchid);
	if(!($mctid = $row['mctid']) || !($mctype = @$mctypes[$mctid])) cls_message::show('��Ч����֤���͡�');
	if(!$mctype['available'] || !in_array($mchid,explode(',',$mctype['mchids'])) || !isset($mfields[$mctype['field']])) cls_message::show('��Ч����֤���͡�');
	$mcfield = &$mfields[$mctype['field']];
	$a_field = new cls_field;
	$a_field->init($mcfield,$row['content']);
	if(!submitcheck('bsubmit')){
		tabheader("��֤���� - $mctype[cname]", 'memcert_need', "?entry=$entry&action=$action&mcid=$mcid",2,1,1);
		$a_field->trfield('fmdata');
		if($mctype['mode'] == 1 && $row['msgcode']) trbasic('�ֻ�ȷ����','',$row['msgcode'],'');
		if($mcfield['datatype'] == 'image' ){
		?>
			<script type="text/javascript">
				function setImgSize(obj,w,h){
					img = new Image(); img.src = obj.src;
					zw = img.width; zh = img.height;
					zr = zw / zh;
					if(w){ fixw = w; }
					else { fixw = obj.getAttribute('width'); }
					if(h){ fixh = h; }
					else { fixh = obj.getAttribute('height'); }
					if(zw > fixw) {
						zw = fixw; zh = zw/zr;
					}
					if(zh > fixh) {
						zh = fixh; zw = zh*zr;
					}
					obj.width = zw; obj.height = zh;
				}
			</script>
		<?php		
			$image_url = view_checkurl($row['content']);
			echo "<div style=\"width:600px; margin-left:85px; margin-top:50px;\">";			
			echo "<a href=\"".$image_url."\" target=\"_blank\"><img alt=\"".$image_url."\" onload=setImgSize(this,500,500) src=\"".$image_url."\"></a><br/><br/>";
			echo "<span style=\"color:#999999;\">������ͼƬ���������ɵ��ͼƬ���йۿ�����</span><br/><br/>";
			echo "</div>";
		}
		tabfooter('bsubmit',$row['checkdate'] ? '�����֤' : '�����֤');
	}elseif($row['checkdate']){
		#����
		#$au->updatefield($mctype['field'],'',$a_field->field['tbl']);
		$au->updatefield("mctid$mctid",0);
		if($mctype['award'])$au->updatecrids(array($mctype['crid'] => -$mctype['award']),0,"$mctype[cname] �۷�");
		$au->updatedb();
		
		$db->query("DELETE FROM {$tblprefix}mcerts WHERE mcid='$mcid'");
		cls_message::show('������֤���', axaction(6,"?entry=$entry"));
	}else{
		$content = $a_field->deal('fmdata','cls_message::show',M_REFERER);
		
		$au->updatefield($mctype['field'],$content,$a_field->field['tbl']);
		$au->updatefield("mctid$mctid",$mctid);
		if($mctype['award']) $au->updatecrids(array($mctype['crid'] => $mctype['award']),0,"$mctype[cname] �ӷ�");
		$au->updatedb();
		
		$db->query("UPDATE {$tblprefix}mcerts SET checkdate='$timestamp',content='$content' WHERE mcid='$mcid'");
		cls_message::show('��֤������', axaction(6,"?entry=$entry"));
	}
}
?>