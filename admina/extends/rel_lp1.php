<?
//??????????????????��ν���������ҳ��������Ĺ�ϵ,�����ҳ��������ֻ��һ�ֲ������ǿ���֧�ֵġ�
(!defined('M_COM') || !defined('M_ADMIN')) && exit('No Permission');
aheader();
backallow('normal') || cls_message::show('��û�е�ǰ��Ŀ�Ĺ���Ȩ�ޡ�');

$cotypes = cls_cache::Read('cotypes');
$catalogs = cls_cache::Read('catalogs');


if(!in_array($chid,array(12,13))) $chid = 12; //$chid = 12;
$ch_title = array(12=>'��Ƶ',13=>'������');
$ch_pid = array(12=>'5',13=>'6');
$ch_edit = array(12=>'shipinadd',13=>'kfsadd');

$caid = empty($caid) ? 0 : max(0,intval($caid));
$page = !empty($page) ? max(1, intval($page)) : 1;
submitcheck('bfilter') && $page = 1;
$chid = empty($chid) ? 0 : max(0,intval($chid));
$pid = empty($pid) ? 0 : max(0,intval($pid));
$keyword = empty($keyword) ? '' : $keyword;
if(empty($action)){
	$wheresql = "";
	$fromsql = "FROM {$tblprefix}".atbl($chid)." a";
	//��Ҫ���ǽ�ɫ����Ŀ����Ȩ��
	$caids = array(-1);
	if($caid) $caids = sonbycoid($caid);
	if(!in_array(-1,$a_caids)) $caids = in_array(-1,$caids) ? $a_caids : array_intersect($caids,$a_caids);
	if(!$caids) $no_list = true;
	elseif(!in_array(-1,$caids) && $cnsql = cnsql(0,$caids,'a.')) $wheresql .= " AND $cnsql";//////////////
	if($chid){
		$wheresql .= " AND a.chid='$chid'";
	}
	if($pid){
		$wheresql .= " AND b.pid='$pid'";	
	}
	$keyword && $wheresql .= " AND (a.mname LIKE '%".str_replace(array(' ','*'),'%',addcslashes($keyword,'%_'))."%' OR a.subject LIKE '%".str_replace(array(' ','*'),'%',addcslashes($keyword,'%_'))."%')";
	$filterstr = '';
	foreach(array('caid','chid','keyword','pid') as $k) $$k && $filterstr .= "&$k=".rawurlencode(stripslashes($$k));
	$wheresql = empty($no_list) ? ($wheresql ? 'WHERE '.substr($wheresql,5) : '') : 'WHERE 0';
	if(!submitcheck('bsubmit')){		
		echo form_str($actionid.'arcsedit',"?entry=$entry$extend_str&page=$page&chid=$chid");
		//ĳЩ�̶�ҳ�����
		trhidden('caid',$caid);	
		trhidden('pid',$pid);		
		tabheader_e();
		echo "<tr><td colspan=\"2\" class=\"txt txtleft\">";
		echo "�ؼ���&nbsp; <input class=\"text\" name=\"keyword\" type=\"text\" value=\"$keyword\" size=\"8\" style=\"vertical-align: middle;\">&nbsp; ";
		//��Ŀ����
	
		echo strbutton('bfilter','ɸѡ');
		tabfooter();
		//�б���	
		tabheader("¥��{$ch_title[$chid]}�б�",'','',9);
		$cy_arr = array("<input class=\"checkbox\" type=\"checkbox\" name=\"chkall\" onclick=\"checkall(this.form, 'selectid', 'chkall')\">",array('����','txtL'),);
		#$cy_arr[] = 'ģ��';
		$cy_arr[] = '��Ŀ';
		//ģ����ϼ������ۺ���һ��
		#$cy_arr[] = '��������';
		$cy_arr[] = '���ʱ��';
		$cy_arr[] = '�鿴';
		$cy_arr[] = '����';
		trcategory($cy_arr);
		$pagetmp = $page;
		do{
			$query = $db->query("SELECT a.* $fromsql $wheresql ORDER BY a.aid DESC LIMIT ".(($pagetmp - 1) * $atpp).",$atpp");
			$pagetmp--;
		} while(!$db->num_rows($query) && $pagetmp);
	
		$itemstr = '';
		while($r = $db->fetch_array($query)){
			$channel = cls_cache::Read('channel',$r['chid']);
			$selectstr = "<input class=\"checkbox\" type=\"checkbox\" name=\"selectid[$r[aid]]\" value=\"$r[aid]\" title=\"".mhtmlspecialchars($r['subject'])."\">";
			$subjectstr = ($r['thumb'] ? '[ͼ]'.' &nbsp;' : '')."<a href=".cls_ArcMain::Url($r)." target=\"_blank\">".cls_string::CutStr(mhtmlspecialchars($r['subject']),36)."</a>";
			$catalogstr = @$catalogs[$r['caid']]['title'];
			$channelstr = @$channel['cname'];
			$viewstr = "<a id=\"{$actionid}_info_$r[aid]\" href=\"?entry=extend&extend=archiveinfo&aid=$r[aid]\" onclick=\"return showInfo(this.id,this.href)\">�鿴</a>";
			$editstr = "<a href=\"?entry=extend&extend={$ch_edit[$chid]}&aid=$r[aid]\" onclick=\"return floatwin('open_arcexit',this)\">�༭</a>";
			foreach($cotypes as $k => $v){
				${'ccid'.$k.'str'} = '';
				if(!$v['self_reg'] && @$r['ccid'.$k]){
					${'ccid'.$k.'str'} = cls_catalog::cnstitle($r['ccid'.$k],$v['asmode'],cls_cache::Read('coclasses',$k));
				}
			}
			$vieworderstr = $r['vieworder'];
			$adddatestr = $r['createdate'] ? date('Y-m-d',$r['createdate']) : '-';
			$itemstr .= "<tr class=\"txt\"><td class=\"txtC w40\" >$selectstr</td><td class=\"txtL\">$subjectstr</td>\n";
			#$itemstr .= "<td class=\"txtC\">$channelstr</td>\n";
			$itemstr .= "<td class=\"txtC\">$catalogstr</td>\n";
			$itemstr .= "<td class=\"txtC w100\">$adddatestr</td>\n";
			$itemstr .= "<td class=\"txtC w35\">$viewstr</td>\n";
			$itemstr .= "<td class=\"txtC w35\">$editstr</td>\n";			
			$itemstr .= "</tr>\n";
		}
	
		$counts = $db->result_one("SELECT count(*) $fromsql $wheresql");
		$multi = multi($counts, $atpp, $page, "?entry=$entry$extend_str$filterstr");
		echo $itemstr;
		tabfooter();
		echo $multi;
		echo '<br/><input class="btn" type="button" name="selbtn" id="selbtn" value="ѡ��" onclick="selheji()"></form>';
		a_guide('archivesedit');
	}
}
?>
<script>
var ppid = '<?php echo $ch_pid[$chid]; ?>';
function selheji(){
	var sel = document.getElementsByTagName('INPUT');
	var num = 0;
	var selobj = 0;
	for(var i=0;i<sel.length;i++){
		if(sel[i].name.match('selectid')){
			if(sel[i].checked){
				num++;
				selobj = sel[i];
			}
		}	
	}
	if(num>1){
		alert('ֻ������ѡ��һ����');
		return;
	}
	if(selobj){
		var popener =  CWindow.getWindow(document.CWindow_wid).parent_window[0];	
		var pobj = popener.document.getElementById('pid'+ppid);
		var pobjtext = popener.document.getElementById('pid'+ppid+'text');
        var pobjname = popener.document.getElementById('fmdata[kfsname]');
		pobjtext.innerHTML = selobj.title;
		pobj.value = selobj.value;
        pobjname.value = selobj.title;
	}
	popener.focus();
	floatwin('close_'+document.CWindow_wid);
}
</script>
