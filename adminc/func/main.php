<?php
//�漰����ʽ�Ļ�Ա���ĺ��������ض�ģ��ר�õĺ��������ﶨ��
!defined('M_COM') && exit('No Permission');
function noedit($var = '',$otherfbd = 0){
	global $useredits,$freeupdate;
	empty($useredits) && $useredits = array();
	return !$otherfbd && ($freeupdate || in_array($var,$useredits)) ? '' : '&nbsp; <img src="images/common/lock.gif" align="absmiddle">';
}

function url_nav($arr = array(),$current=''){//�����ѡ������ӣ�������ǰҳ
	echo "<div class=\"menutop\">\n";
	foreach($arr as $k => $v) echo "<a href=\"$v[1]\"".($k == $current ? ' class="act"' : '')."><span>$v[0]</span></a>\n";
	echo "<div class=\"blank0\"></div></div>";
}
function multi($num, $perpage, $curpage, $mpurl, $maxpages = 0, $page = 10, $simple = 0, $onclick = '') {
	global $infloat,$handlekey;
	$multipage = '';
	$mpurl .= in_str('?',$mpurl) ? '&amp;' : '?';
	$onclick && $onclick .='(event);';
	$infloat && $onclick .= "return floatwin('update_$handlekey',this)";
	$onclick && $onclick = " onclick=\"$onclick\"";
	if($num > $perpage) {//ֻ�г���1ҳʱ������ʾ��ҳ����
		$offset = 2;//��ǰҳ��֮ǰ��ʾ��ҳ����
		$realpages = @ceil($num / $perpage);
		$pages = $maxpages && $maxpages < $realpages ? $maxpages : $realpages;//��Ҫͳ�Ƶ�ҳ��
		if($page > $pages) {
			$from = 1;
			$to = $pages;
		} else {
			$from = $curpage - $offset;
			$to = $from + $page - 1;
			if($from < 1) {
				$to = $curpage + 1 - $from;
				$from = 1;
				if($to - $from < $page) $to = $page;
			} elseif($to > $pages) {
				$from = $pages - $page + 1;
				$to = $pages;
			}
		}
		$multipage = ($curpage - $offset > 1 && $pages > $page ? '<a href="'.$mpurl.'page=1" class="p_redirect"'.$onclick.'>1...</a>' : '').($curpage > 1 && !$simple ? '<a href="'.$mpurl.'page='.($curpage - 1).'" class="p_redirect"><<</a>' : '');
		for($i = $from; $i <= $to; $i++) $multipage .= $i == $curpage ? '<a class="p_curpage">'.$i.'</a>' : '<a href="'.$mpurl.'page='.$i.'" class="p_num"'.$onclick.'>'.$i.'</a>';
		$multipage .= ($curpage < $pages && !$simple ? '<a href="'.$mpurl.'page='.($curpage + 1).'" class="p_redirect"'.$onclick.'>>></a>' : '').($to < $pages ? '<a href="'.$mpurl.'page='.$pages.'" class="p_redirect"'.$onclick.'>...'.$pages.'</a>' : '').
			(!$simple && $pages > $page ? '<a class="p_pages" style="padding: 0px; border:0;"><input class="p_input" type="text" name="custompage" onKeyDown="if(event.keyCode==13) {window.location=\''.$mpurl.'page=\'+this.value; return false;}"></a><input type="button" name="s_asd" value="��ת" onclick="return upb_dir();"><script type="text/javascript">function upb_dir(){var url = "'.$mpurl.'page="+document.forms[0].custompage.value;window.location = url.replace("&amp;","&");}</script>' : '');
		$multipage = $multipage ? '<div class="p_bar">'.(!$simple ? '<a class="p_total">&nbsp;'.$num.'&nbsp;</a>' : '').$multipage.'</div>' : '';
	}
	return $multipage;
}

function tabheader($tname='',$fname='',$furl='',$col=2,$fupload=0,$checksubmit=0,$newwin=0){
	if($fname) echo form_str($fname,$furl,$fupload,$checksubmit,$newwin);
	tabheader_e();
	echo "<tr class=\"header\"><td colspan=\"$col\"><b>$tname</b></td></tr>\n";
}
function tabheader_e(){
	echo "<table border=\"0\" cellpadding=\"0\" cellspacing=\"1\" class=\"black tabmain marb10\">\n";
}
function tabfooter($bname='',$bvalue='',$addstr='',$fmclose=1){//$fmclose�Ƿ�ر�form
	global $aListSetReset;
	$bvalue = empty($bvalue) ? '�ύ' : $bvalue;
	echo "</table>\n";
	if($aListSetReset){
		echo $aListSetReset;
		$aListSetReset = '';
	}
	echo $bname ? "<div align=\"center\"><input class=\"button\" type=\"submit\" name=\"$bname\" value=\"$bvalue\"></div>\n" : '';
	echo $addstr ? $addstr : '';
	echo $bname && $fmclose ? "</form>\n" : '';
}
function trcategory($arr = array(), $tabID='1'){
	global $ckpre,$entry,$extend,$action,$aListSetReset;
	$arr = array_filter($arr);
	$baseID = empty($entry)?'entID':$entry;
	foreach(array('extend','action','tabID') as $k) empty($$k) || $baseID .= '_'.$$k;
	$aListSet_tCfg = ''; //$i = 0;
	$trStr = "<tr id=\"TR_$baseID\" class=\"category\" align=\"center\">\n";
	foreach ($arr as $v) {
	   $iCfg = '';
	   if(is_array($v)){
		  foreach ($v as $j => $vsub) $iCfg .= $v[$j].'|';
		  $iCfg .= '|';
	   }else{
		  $iCfg .= $v.'||';
	   }
	   $iArr = explode('|',$iCfg);
	   $aListSet_tCfg .= strtoupper($iArr[2]).'|'; // S/H
	   $iVal = $iArr[0];
	   if(strlen($iArr[1])>1){
		   $trStr .= "\n<td class=\"$iArr[1]\">$iVal</td>\n";
	   }else{
		   if(strlen($iArr[1])=='') $iArr[1] = 'C';
		   else $iArr[1] = strtoupper($iArr[1]);
		   if($iArr[1]=='C'){
			   $trStr .= "\n<td>$iVal</td>\n";
		   }else if($iArr[1]=='R'){
			   $trStr .= "\n<td class=\"right\">$iVal</td>\n";
		   }else if($iArr[1]=='L'){
			   $trStr .= "\n<td class=\"left\">$iVal</td>\n";
		   }else{
			   $trStr .= "\n<td>$iVal</td>\n";
		   }
	   }
	}
	$trStr .= "</tr>\n";
	if(str_replace('|','',$aListSet_tCfg)!=''){
		$trStr = str_replace("<tr id=","<tr ondblclick=\"aListSetting('$baseID','$aListSet_tCfg')\" id=",$trStr);
		$aListSetReset = "\n<script type='text/javascript'>\n";
		$aListSetReset .= "var aListSet_ckpre = '$ckpre';\n";
		$aListSetReset .= "aListSetReset('$baseID','$aListSet_tCfg');"; //
		$aListSetReset .= "\n</script>\n";
	}
	echo $trStr;
}
function strbutton($name,$value='�ύ',$class='button',$onclick = ''){
	return "<input class=\"$class\" type='".($onclick ? 'button' : 'submit')."' name=\"$name\" value=\"$value\"".($onclick ?  " onclick=\"$onclick\"" : '').">";
}
function viewcheck($param){
	$name = $value = $body = $title = '';$noblank = 0;
	extract($param, EXTR_OVERWRITE);
	return ($noblank ? '' : '&nbsp; &nbsp; ')."<input class=\"checkbox\" type=\"checkbox\" name=\"$name\" value=\"1\" onclick=\"alterview('$body')\"".(empty($value) ? '' : ' checked').">$title";
}
function trrange($trname,$arr1,$arr2,$type='text',$guide='',$width = '150px'){
	$trname = '<b>'.$trname.'</b>';
	echo "<tr><td width=\"$width\" class=\"item1\">$trname</td>\n";
	echo "<td class=\"item2\">\n";
	echo (empty($arr1[2]) ? '' : $arr1[2])."<input type=\"text\" size=\"".(empty($arr1[4]) ? 10 : $arr1[4])."\" id=\"$arr1[0]\" name=\"$arr1[0]\" value=\"".mhtmlspecialchars($arr1[1])."\"".($type == 'calendar' ? " class=\"Wdate\" onfocus=\"WdatePicker({readOnly:true})\"" : '')."><span id=\"alert_$arr1[0]\" name=\"alert_$arr1[0]\" class=\"red\"></span>".(empty($arr1[3]) ? '' : $arr1[3]);
	echo (empty($arr2[2]) ? '' : $arr2[2])."<input type=\"text\" size=\"".(empty($arr2[4]) ? 10 : $arr2[4])."\" id=\"$arr2[0]\" name=\"$arr2[0]\" value=\"".mhtmlspecialchars($arr2[1])."\"".($type == 'calendar' ? " class=\"Wdate\" onfocus=\"WdatePicker({readOnly:true})\"" : '')."><span id=\"alert_$arr2[0]\" name=\"alert_$arr2[0]\" class=\"red\"></span>".(empty($arr2[3]) ? '' : $arr2[3]);
	if($guide) echo "<br /><font class=\"gray\">$guide</font>";
	echo "</td></tr>";
}
function tr_regcode($rname, $params = array()){
	global $cms_regcode,$cms_abs,$timestamp;
        $fromName = empty($params['formName']) ? NULL : $params['formName'];
        $class = empty($params['class']) ? 'regcode' : $params['$class'];
        $inputName = empty($params['inputName']) ? '' : $params['inputName'];
        $inputString = empty($params['inputString']) ? '' : $params['inputString'];
        $code = _08_HTML::getCode($rname, $fromName, $class, $inputName, $inputString);
	if($cms_regcode && in_array($rname,explode(',',$cms_regcode))){
		echo <<<EOT
            <tr><td class="item1"><b><font color='red'> * </font>��֤��</b></td>
            <td class="item2">$code&nbsp;&nbsp;</td></tr>
EOT;
	}
}
function trspecial($trname,$varr = array()){
	$trname = '<b>'.$trname.'</b>';
	$lcls = 'item1';$rcls = 'item2';
	$varr['width'] = empty($varr['width']) ? '150px' : $varr['width'];
	if(in_array($varr['type'],array('image','images','flash','flashs','media','medias')))
		$guidestr = $varr['guide'] ? "<div class=\"tips1\">$varr[guide]</div>" : '';
	else
		$guidestr = $varr['guide'] ? (!empty($varr['mode']) ? "<div class=\"tips1\">$varr[guide]</div>" : "<font class=\"gray\">$varr[guide]</font>") : '';
	if($varr['type'] == 'htmltext'){
		echo empty($varr['mode']) ? "<tr><td colspan=\"2\" class=\"item1 item4\">".$trname.$guidestr."</td></tr><tr><td colspan=\"2\" class=\"$rcls\">\n" : "<tr><td width=\"$varr[width]\" class=\"$lcls\">".$trname."</td><td class=\"$rcls\">\n";
		echo $varr['frmcell'].$guidestr;
		#echo "</td></tr>\n";
	}else{
		$varr['addcheck'] && $guidestr = '&nbsp; &nbsp; '.$varr['addcheck'].$guidestr;
		echo "<tr".(@$varr['view'] == 'H'?' style="display:none"':'')."><td width=\"$varr[width]\" class=\"$lcls\">".$trname."</td>\n";
		echo "<td class=\"$rcls\">".$varr['frmcell'].$guidestr;
	}

	echo @$varr['more'] ? '<script type="text/javascript">var __js="'.implode(",",$varr['more']).'";</script>'."<div><span style=\"float:right;cursor:pointer;font-weight:bold;color:#C4141F;\" id=\"more_tips\" onclick=\"hidspan(__js,'fmdata')\">��������</span></div></td></tr>\n" : "</td></tr>\n";
	
}
function trbasic($trname, $varname, $value = '', $type = 'text', $arr = array()) {//w,hΪ�����ı�(size)������ı�ָ����ȼ��߶�(px)
	$guide=''; $width = '150px'; $rshow = 1; $rowid = ''; $validate = '';$w = 0;$h = 0;$addstr = '';
	extract($arr, EXTR_OVERWRITE);
	echo "<tr" . ($rowid ? " id=\"$rowid\"" : '') . ($rshow ? '' : ' style="display:none"') . "><td width=\"$width\" class=\"item1\"><b>$trname</b></td>\n";
	echo "<td class=\"item2\">\n";
	if($type == 'radio') {
		$check[$value ? 'true' : 'false'] = "checked";$check[$value ? 'false' : 'true'] = '';
		echo "<input type=\"radio\" class=\"radio\" id=\"$varname\" name=\"$varname\" value=\"1\" $check[true] $validate> ".'��'." &nbsp; &nbsp; \n".
			"<input type=\"radio\" class=\"radio\" id=\"$varname\" name=\"$varname\" value=\"0\" $check[false] $validate> ".'��'." \n";
	}elseif($type == 'select') {
		echo "<select style=\"vertical-align: middle;\" id=\"$varname\" name=\"$varname\" $validate>$value</select>";
	}elseif($type == 'text' || $type == 'password'){
		$w = $w ? $w : 25;
		echo "<input type=\"".($type == 'password' ? $type : 'text')."\" size=\"$w\" id=\"$varname\" name=\"$varname\" value=\"".mhtmlspecialchars($value)."\" $validate />\n";
	}elseif($type == 'calendar'){
		$w = $w ? $w : 15;
		echo "<input type=\"text\" size=\"$w\" id=\"$varname\" name=\"$varname\" value=\"".mhtmlspecialchars($value)."\" class=\"Wdate\" onfocus=\"WdatePicker({readOnly:true})\" $validate />\n";
	}elseif($type == 'textarea'){
		$w = $w ? $w : 300;$h = $h ? $h : 100;
		echo "<textarea name=\"$varname\" id=\"$varname\" style=\"width:{$w}px;height:{$h}px\" $validate>".mhtmlspecialchars($value)."</textarea>\n";
	}else echo $value;
	echo $addstr;
	if($guide) echo"<font class=\"gray\">$guide</font>";
	echo "</td></tr>\n";
}
function mc_allow(){
	global $message_class,$handlekey,$infloat;
	$curuser = cls_UserMain::CurUser();
	if(!$curuser->info['mid']){
		_header();
		$message_class = 'curbox';
		echo '<div class="area col"><div class="conBox"><div class="con_con"><div class="main_area">';
		empty($handlekey) && $handlekey = '';
		$tmp=empty($infloat)?'':" onclick=\"floatwin('close_$handlekey');return floatwin('open_login',this)\"";
		cls_message::show('���¼��Ա����  [<a href="login.php"'.$tmp.'>��Ա��½</a>] [<a href="register.php" target="_blank">ע��</a>]','');
	}elseif($curuser->info['isfounder']){
		_header();
		cls_message::show('��ʼ���벻Ҫʹ�û�Ա���ģ�[<a href="login.php?action=logout">�˳�</a>]','');
	}
}
function _footer(){
	global $infloat;
	if(!$infloat) echo '<div class="blank9"></div></div>';
	echo '</body></html>';
}
function _header($title = '', $class = 'main_area'){
	defined('M_MCENTER') || define('M_MCENTER', TRUE);
	global $hostname,$mcharset,$cmsname,$mallowfloatwin,$mfloatwinwidth,$mfloatwinheight,$cms_abs,$infloat,$message_class, $cms_top, $cmsurl, $ck_plugins_enable,$ck_plugins_disable,$ckpre;
	$curuser = cls_UserMain::CurUser();
	define('NO_MCFOOTER', TRUE);
	$message_class = 'msgbox';
	$css = MC_ROOTURL.'images/style.css';
	$fltcss = $infloat ? 'floatbox' : 'box';
	$_Title_Adminm = $title ? $title : "��Ա�������� - $cmsname";
	if(!empty($curuser->info['atrusteeship'])) $_Title_Adminm .= " [�����ˣ�{$curuser->info['atrusteeship']['from_mname']}]";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?=$mcharset?>" />
<title><?=$_Title_Adminm?></title>
<link href="<?=MC_ROOTURL?>css/default.css" rel="stylesheet" type="text/css" />
<link type="text/css" rel="stylesheet" href="<?=$cmsurl?>images/common/validator.css" />
<link type="text/css" rel="stylesheet" href="<?=$cmsurl?>images/common/window.css" />
</head>
<body>
<script type="text/javascript">var CMS_ABS = "<?=$cms_abs?>" <?=empty($cmsurl) ? '' : ', CMS_URL = "'.$cmsurl.'"'?>,MC_ROOTURL = "<?=MC_ROOTURL?>",tipm_ckkey = '<?=$ckpre?>mTips_List';var originDomain = originDomain || document.domain; document.domain = '<?php echo $cms_top;?>' || document.domain; </script>
<?php cls_phpToJavascript::loadJQuery(); ?>
<script type="text/javascript" src="<?=$cmsurl?>images/common/layer/layer.min.js"></script>
<script type="text/javascript" src="<?=$cmsurl?>include/js/common.js"></script>
<script type="text/javascript" src="<?=$cmsurl?>include/js/adminm.js"></script>
<script type="text/javascript" src="<?=$cmsurl?>include/js/floatwin.js"></script>
<script type="text/javascript" src="<?=$cmsurl?>include/js/setlist.js"></script>
<!-- ueditor -->
<script type="text/javascript" src="<?=$cmsurl?>static/ueditor1_4_3/ueditor.config.js"></script>
<script type="text/javascript" src="<?=$cmsurl?>static/ueditor1_4_3/ueditor.all.min.js"> </script>
<script type="text/javascript" src="<?=$cmsurl?>static/ueditor1_4_3/lang/zh-cn/zh-cn.js"></script>
<!-- ueditor end -->
<script type="text/javascript" src="<?=$cmsurl?>include/js/My97DatePicker/WdatePicker.js"></script>
<script type="text/javascript" src="<?=$cmsurl?>include/js/tree.js"></script>
<script type="text/javascript" src="<?=$cmsurl?>include/js/_08cms.js"></script>
<script type="text/javascript" src="<?=$cmsurl?>include/js/validator.js"></script>
<div id="append_parent"></div>
<div class="<?=$class ?>">
<?php
}

function mcfooter(){
	global $cms_power,$cms_icpno,$cms_version,$infloat;
	$tpl_mconfigs = cls_cache::Read('tpl_mconfigs');
	$copyright = @$tpl_mconfigs['copyright']; 
	if($infloat){
		echo "</body></html>";
	}else{
?>
</div>
<div class="blank9"></div>
<div style="width:960px; margin:0 auto; text-align:center; height:100px; padding-top:10px;"><br>
<SCRIPT LANGUAGE=Javascript> 
var _2df5=["\x3c\x63\x65\x6e\x74\x65\x72\x3e\x3c\x64\x69\x76\x20\x73\x74\x79\x6c\x65\x3d\x6c\x69\x6e\x65\x2d\x68\x65\x69\x67\x68\x74\x3a\x32\x31\x70\x78\x3e\u8d44\u6e90\u63d0\u4f9b\uff1a\x3c\x61\x20\x68\x72\x65\x66\x3d\x68\x74\x74\x70\x3a\x2f\x2f\x77\x77\x77\x2e\x73\x6f\x75\x68\x6f\x2e\x6e\x65\x74\x20\x74\x61\x72\x67\x65\x74\x3d\x5f\x62\x6c\x61\x6e\x6b\x3e\x3c\x66\x6f\x6e\x74\x20\x63\x6f\x6c\x6f\x72\x3d\x72\x65\x64\x3e\u641c\u864e\u7cbe\u54c1\u793e\u533a\x3c\x2f\x66\x6f\x6e\x74\x3e\x3c\x2f\x61\x3e","\x3c\x62\x72\x3e\x26\x6e\x62\x73\x70\x3b","\x3c\x61\x20\x68\x72\x65\x66\x3d\x68\x74\x74\x70\x3a\x2f\x2f\x77\x77\x77\x2e\x73\x6f\x75\x68\x6f\x2e\x6e\x65\x74\x20\x74\x61\x72\x67\x65\x74\x3d\x5f\x62\x6c\x61\x6e\x6b\x3e\u641c\u864e\u7cbe\u54c1\u793e\u533a\x3c\x2f\x61\x3e\x20\x7c\x20\x3c\x61\x20\x68\x72\x65\x66\x3d\x68\x74\x74\x70\x3a\x2f\x2f\x76\x69\x70\x2e\x73\x6f\x75\x68\x6f\x2e\x6e\x65\x74\x20\x74\x61\x72\x67\x65\x74\x3d\x5f\x62\x6c\x61\x6e\x6b\x3e\u6781\u54c1\u5546\u4e1a\u6e90\u7801\x3c\x2f\x61\x3e\x20\x7c\x20\x3c\x61\x20\x68\x72\x65\x66\x3d\x68\x74\x74\x70\x3a\x2f\x2f\x69\x64\x63\x2e\x73\x6f\x75\x68\x6f\x2e\x6e\x65\x74\x20\x74\x61\x72\x67\x65\x74\x3d\x5f\x62\x6c\x61\x6e\x6b\x3e\u641c\u864e\u7cbe\u54c1\u793e\u533a\u7a7a\u95f4\u3001\u57df\u540d\x3c\x2f\x61\x3e\x20\x7c\x20\x3c\x61\x20\x68\x72\x65\x66\x3d\x68\x74\x74\x70\x3a\x2f\x2f\x76\x69\x70\x2e\x73\x6f\x75\x68\x6f\x2e\x6e\x65\x74\x2f\x74\x65\x6d\x70\x6c\x61\x74\x65\x73\x2f\x4b\x6f\x72\x65\x61\x2f\x20\x74\x61\x72\x67\x65\x74\x3d\x5f\x62\x6c\x61\x6e\x6b\x3e\x39\x30\x47\u97e9\u56fd\u8c6a\u534e\u5546\u4e1a\u6a21\u7248\x3c\x2f\x61\x3e\x20\x7c\x20\x3c\x61\x20\x68\x72\x65\x66\x3d\x68\x74\x74\x70\x3a\x2f\x2f\x74\x6f\x6f\x6c\x2e\x73\x6f\x75\x68\x6f\x2e\x6e\x65\x74\x2f\x20\x74\x61\x72\x67\x65\x74\x3d\x5f\x62\x6c\x61\x6e\x6b\x3e\u7ad9\u957f\u5de5\u5177\u7bb1\x3c\x2f\x61\x3e","\x3c\x62\x72\x3e","\u66f4\u591a\u7cbe\u54c1\u5546\u4e1a\u8d44\u6e90\uff0c\u5c31\u5728\x3c\x61\x20\x68\x72\x65\x66\x3d\x68\x74\x74\x70\x3a\x2f\x2f\x77\x77\x77\x2e\x73\x6f\x75\x68\x6f\x2e\x6e\x65\x74\x20\x74\x61\x72\x67\x65\x74\x3d\x5f\x62\x6c\x61\x6e\x6b\x3e\u641c\u864e\u7cbe\u54c1\u793e\u533a\x3c\x2f\x61\x3e\x3c\x2f\x66\x6f\x6e\x74\x3e","\x3c\x2f\x64\x69\x76\x3e\x3c\x2f\x63\x65\x6e\x74\x65\x72\x3e"];window["\x64\x6f\x63\x75\x6d\x65\x6e\x74"]["\x77\x72\x69\x74\x65\x6c\x6e"](_2df5[0x0]); window["\x64\x6f\x63\x75\x6d\x65\x6e\x74"]["\x77\x72\x69\x74\x65\x6c\x6e"](_2df5[0x1]); window["\x64\x6f\x63\x75\x6d\x65\x6e\x74"]["\x77\x72\x69\x74\x65\x6c\x6e"](_2df5[0x2]); window["\x64\x6f\x63\x75\x6d\x65\x6e\x74"]["\x77\x72\x69\x74\x65\x6c\x6e"](_2df5[0x3]); window["\x64\x6f\x63\x75\x6d\x65\x6e\x74"]["\x77\x72\x69\x74\x65\x6c\x6e"](_2df5[0x4]); window["\x64\x6f\x63\x75\x6d\x65\x6e\x74"]["\x77\x72\x69\x74\x65\x6c\x6e"](_2df5[0x5]);
</SCRIPT> 

</div>
</body>
</html>
<?php 
	}
	entryExit();
}

//�ҵ�ί��:
function my_wt_header($action){
	global $cms_abs;
	$str=<<<EOT
	<table border="0" cellpadding="0" cellspacing="1" class="black tabmain tabnav marb10"><tbody><tr class="header"><td colspan="30">
			<div class="blocktitle"> 
				<div class="xlist" id="menage"> ����ί��:<a href="?action={$action}&chid=3" style="color:red;">�ҵ�ί�г���</a>|<a href="?action={$action}&chid=2" style="color:red;">�ҵ�ί�г���</a>|<a href="{$cms_abs}info.php?fid=101&chid=2" target="_blank">��Ҫί�г���</a>|<a href="{$cms_abs}info.php?fid=101&chid=3" target="_blank">��Ҫί�г���</a>
				</div>
				 <h2>�����ҵķ���</h2>
			</div>
			</td></tr>
			</tbody></table>
			
			<div class="tishi mT10">
				������ί�г��⣬���۸�<span class="red">5</span>�׷��ӣ�ÿ�׷���������ί��5λ�����ˣ�<span class="red">���ĵ绰����Դ��Ϣ�����⹫��</span>
	</div>
EOT;
	echo $str;
}

function u_memberstat($mid,$minute = 60){//�̶�ʱ����ͳ�ƻ�Ա�����״��
	global $db,$tblprefix,$timestamp;//archives,checks,
	@set_time_limit(1000);
	@ignore_user_abort(TRUE);
	if(!($mid = max(0,intval($mid)))) return;
	$ctfile = M_ROOT.'./dynamic/memberstat/'.($mid % 100).'/'.$mid.'.cac';
	if(!$mid || $timestamp - @filemtime($ctfile) < 60 * $minute) return;
	$na = array();
	$na['archives'] = '';
	$na['checks'] = '';
	$tbls = stidsarr(1);
	$curuser = cls_UserMain::CurUser();
	$mchid = $curuser->info['mchid'];
	foreach($tbls as $k => $v){
		$na['archives'] += $db->result_one("SELECT COUNT(*) FROM {$tblprefix}".atbl($k,1)." WHERE mid='$mid'");
		$na['checks'] += $db->result_one("SELECT COUNT(*) FROM {$tblprefix}".atbl($k,1)." WHERE mid='$mid' AND checked=1");	
	}
	$na['aesfys'] = $db->result_one("SELECT COUNT(*) FROM {$tblprefix}".atbl(3)." WHERE mid='$mid' AND chid=3");
	if($mchid!=3) $na['vesfys'] = $db->result_one("SELECT COUNT(*) FROM {$tblprefix}".atbl(3)." WHERE mid='$mid' AND chid=3 AND checked=1 AND (enddate=0 OR enddate>$timestamp)");
	$na['aczfys'] = $db->result_one("SELECT COUNT(*) FROM {$tblprefix}".atbl(2)." WHERE mid='$mid' AND chid=2");
	if($mchid!=3) $na['vczfys'] = $db->result_one("SELECT COUNT(*) FROM {$tblprefix}".atbl(2)." WHERE mid='$mid' AND chid=2 AND checked=1 AND (enddate=0 OR enddate>$timestamp)");
	$na['aqzs'] = $db->result_one("SELECT COUNT(*) FROM {$tblprefix}".atbl(9)." WHERE mid='$mid' AND chid=9");
	$na['vqzs'] = $db->result_one("SELECT COUNT(*) FROM {$tblprefix}".atbl(9)." WHERE mid='$mid' AND chid=9 AND checked=1 AND (enddate=0 OR enddate>$timestamp)");
	$na['aqgs'] = $db->result_one("SELECT COUNT(*) FROM {$tblprefix}".atbl(10)." WHERE mid='$mid' AND chid=10");
	$na['vqgs'] = $db->result_one("SELECT COUNT(*) FROM {$tblprefix}".atbl(10)." WHERE mid='$mid' AND chid=10 AND checked=1 AND (enddate=0 OR enddate>$timestamp)");
	foreach(array(5 => 'ablys',11 => 'abscs',) as $k => $v){
		if($commu = cls_cache::Read('commu',$k)){
			$na[$v] = $db->result_one("SELECT COUNT(*) FROM {$tblprefix}$commu[tbl] WHERE tomid='$mid'");
		}
	}
	$str = '';foreach($na as $x => $y) $str .= ",$x='$y'";
	$db->query("UPDATE {$tblprefix}members_sub SET ".substr($str,1)." WHERE mid=$mid");
	mmkdir($ctfile,0,1);
	if(@$fp = fopen($ctfile,'w')) fclose($fp);	
	return;
}
/**
 *�ж��Ǿ��͹�˾���Ǿ����ˣ����� $otherSql �Լ� $whereStr
 * @param array  $agentNameArr ���͹�˾�µľ�����
 * @param string $agentMidStr  ���͹�˾�µľ�����MID��ɵ��ַ���������sql��
 */
function isCompany($isCompany,$curuser){
    $db = _08_factory::getDBO();
    $tblprefix = cls_env::getBaseIncConfigs('tblprefix');
	$mname = intval(cls_env::GetG('mname'));
    $otherSql = '';
    $whereStr = '';
    $agentNameArr = array();
	if($isCompany){ //�ҵ��þ��͹�˾�����о�����
    	if($curuser->info['mchid']!=3){ cls_message::show('������[���͹�˾]�����ܷ��ʡ�'); } 
    	$agentMidStr = '';
    	$namesql = "select m.mid,m.mname FROM {$tblprefix}members m WHERE m.mchid=2 AND pid4='".$curuser->info['mid']."' AND incheck4=1";
    	$query = $db->query($namesql);
    	while($row = $db->fetch_array($query)){    
    		$agentNameArr[$row['mid']] = $row['mname'];
    		$agentMidStr .= ','.$row['mid'];
    	}
    	$agentNameArr = array('0'=>'-������-') + $agentNameArr;
    	$agentMidStr = empty($agentMidStr) ? "-1" : substr($agentMidStr,1); 
    	if($mname){ //�ҵ��þ��͹�˾��ĳһ�������˵ķ�Դ
    		$whereStr .= "a.mid='$mname'";
    		$otherSql .= "a.mid='$mname'";
		}else{
			$whereStr .= "a.mid IN($agentMidStr)";
    		$otherSql .= "a.mid IN($agentMidStr)";
		}
    }else{
    	$whereStr .= "a.mid='".$curuser->info['mid']."'";
    }
    return array('otherSql'=>$otherSql,'whereStr'=>$whereStr,'agentNameArr'=>$agentNameArr);
}

/**
 * ��Ա���Ķ��ַ������⡢���⡢�����������·�����ʾ��Ϣ
 * @param object $curuser    ��ǰ��Աʵ��
 * @param int    $chid       �ĵ�id
 * return array
 */
 function userCenterDisplayMes($curuser,$chid,$isAdd=0){
    $style1 = " style='font-weight:bold;color:green'";
    $style2 = " style='font-weight:bold;color:red'";
    $exconfigs = cls_cache::cacRead('exconfigs',_08_EXTEND_SYSCACHE_PATH);
    $message = '';
    $otherData = array();
   
    if(in_array($chid,array(2,3,117,118,119,120))){//���ַ�/���ⷿԴ����ʾ��Ϣ
        $MessageArr = houseDisplayMes($exconfigs,$curuser,$chid,$isAdd,$style1,$style2);
    }else if(in_array($chid,array(9,10))){//����/�󹺷�Դ����ʾ��Ϣ
        $MessageArr = qzQgDisplayMes($exconfigs,$curuser,$chid,$isAdd,$style1,$style2);
    }
    $message .= $MessageArr['message'];
    unset($MessageArr['message']);
    return array('message'=>$message,'otherData'=>$MessageArr);
 }
 
/**
 * ��Ա���Ķ��ַ�/�������������·�����ʾ��Ϣ
 * @param array  $exconfigs  ��̨�趨�ķ�����Դ����������
 * @param object $curuser    ��ǰ��Աʵ��
 * @param int    $chid       �ĵ�id
 * @param string $style1     css��ʽһ
 * @param string $style2     css��ʽ�� 
 * return array  
 */
 function houseDisplayMes($exconfigs,$curuser,$chid,$isAdd,$style1,$style2){
    if(!in_array($chid,array(2,3,117,118,119,120))){
        cls_message::show("��ָ����ȷ�ĵ�ģ�͡�");
    }
    $message = '';

    if(in_array($chid,array(2,3))){
        if(!($rules = @$exconfigs['yysx'])) cls_message::show('ϵͳû��ԤԼˢ�¹���');
        if(empty($curuser->info['grouptype14'])){
            $exconfigs = $exconfigs['fanyuan'][0];
        }else{
            $exconfigs = $exconfigs['fanyuan'][$curuser->info['grouptype14']];
        }
        //�ѷ������ַ�/���ⷿ����
        $houseNum = cls_DbOther::ArcLimitCount($chid, 'createdate');
        $message .= "ÿ����������Դ:<span$style1>$exconfigs[daymax]</span>�����ѷ���:<span$style1>$houseNum</span>�������ɷ���:<span$style2>".($exconfigs['daymax'] - $houseNum)."</span>����<br/>";

        //����ˢ�´���������ԤԼˢ�£�
        $refreshUsedNum = empty($curuser->info['refreshes'])?'0':$curuser->info['refreshes'];

        //ʣ��ˢ�´���
        $refreshRemainNum = $exconfigs['refresh'] - $refreshUsedNum;
        $refreshRemainNum = $refreshRemainNum<0 ? 0 : $refreshRemainNum;
        $message .= "ÿ������ˢ�´���Ϊ:<span$style1>$exconfigs[refresh]</span>�Σ���ˢ��<span$style1>$refreshUsedNum</span>�Σ�������ˢ��<span$style2>$refreshRemainNum</span>�Σ�<br/>";

        //ԤԼˢ��
        $chuzuYuyue 	= cls_DbOther::ArcLimitCount(2, 'yuyuedate', "='".strtotime(date('Y-m-d'))."' AND yuyue = '1'");
        $chushouYuyue   = cls_DbOther::ArcLimitCount(3, 'yuyuedate', "='".strtotime(date('Y-m-d'))."' AND yuyue = '1'");
        $yuyueTotalNum 	= $chuzuYuyue + $chushouYuyue;
        $yuyueTotalNum = empty($yuyueTotalNum)?'0':$yuyueTotalNum;
        $curuser->info['mchid'] != 1 && $message .= "ÿ������ԤԼ��Դ����:<span$style1>$rules[totalnum]</span>������ԤԼ:<span$style1>$yuyueTotalNum</span>��������ԤԼ:<span$style2>".($rules['totalnum'] - $yuyueTotalNum)."</span>����<br/>";
    }else{
        //if(!($rules = @$exconfigs['yysx'])) cls_message::show('ϵͳû��ԤԼˢ�¹���');
        if(empty($curuser->info['grouptype14'])){
            $exconfigs = $exconfigs['shangye'][0];
        }else{
            $exconfigs = $exconfigs['shangye'][$curuser->info['grouptype14']];
        }
        //�ѷ������ַ�/���ⷿ����
        $houseNum = cls_DbOther::ArcLimitCount($chid, 'createdate');
        $message .= "ÿ����������ҵ�ز�:<span$style1>$exconfigs[daymax]</span>�����ѷ���:<span$style1>$houseNum</span>�������ɷ���:<span$style2>".($exconfigs['daymax'] - $houseNum)."</span>����<br/>";

        //����ˢ�´���������ԤԼˢ�£�
        $refreshUsedNum = empty($curuser->info['refreshes'])?'0':$curuser->info['refreshes'];

        //ʣ��ˢ�´���
        $refreshRemainNum = $exconfigs['refresh'] - $refreshUsedNum;
        $refreshRemainNum = $refreshRemainNum<0 ? 0 : $refreshRemainNum;
        $message .= "ÿ������ˢ�´���Ϊ:<span$style1>$exconfigs[refresh]</span>�Σ���ˢ��<span$style1>$refreshUsedNum</span>�Σ�������ˢ��<span$style2>$refreshRemainNum</span>�Σ�<br/>";

    }

    return array('message'=>$message,'refreshRemainNum'=>$refreshRemainNum);    
 }
 /**
 * ��Ա�������������������·�����ʾ��Ϣ
 * @param array  $exconfigs  ��̨�趨�ķ�����Դ����������
 * @param object $curuser    ��ǰ��Աʵ��
 * @param int    $chid       �ĵ�id
 * @param string $style1     css��ʽһ
 * @param string $style2     css��ʽ�� 
 * return array  
 */
 function qzQgDisplayMes($exconfigs,$curuser,$chid,$isAdd,$style1,$style2){
    if(!in_array($chid,array(9,10))){
        cls_message::show("��ָ����ȷ�ĵ�ģ�͡�");
    }
    $message = '';  
    if(empty($curuser->info['grouptype14'])){
    	$exconfigs = $exconfigs['fanyuan'][0];	
    }else{
    	$exconfigs = $exconfigs['fanyuan'][$curuser->info['grouptype14']];	
    }
    //����ˢ�´���������ԤԼˢ�£�
    $refreshUsedNum = empty($curuser->info['refreshes'])?'0':$curuser->info['refreshes'];
    
    //ʣ��ˢ�´���
    $refreshRemainNum = $exconfigs['refresh'] - $refreshUsedNum; 
    $refreshRemainNum = $refreshRemainNum<0 ? 0 : $refreshRemainNum;
    $message .= "ÿ������ˢ�´���Ϊ:<span$style1>$exconfigs[refresh]</span>�Σ���ˢ��<span$style1>$refreshUsedNum</span>�Σ�������ˢ��<span$style2>$refreshRemainNum</span>�Σ�<br/>";
    
    //�ѷ�����������
    $qiuzutotal = cls_DbOther::ArcLimitCount(9, '');
    $qiugoutotal = cls_DbOther::ArcLimitCount(10, '');
    $total = $qiuzutotal + $qiugoutotal;
    $total = empty($total) ? 0 : $total;
    $message .= "����������������:<span$style1>$exconfigs[xuqiu]</span>�����ѷ���:<span$style1>$total</span>�������ɷ���:<span$style2>".($exconfigs['xuqiu'] - $total)."</span>����<br/>";
    
    return array('message'=>$message,'refreshRemainNum'=>$refreshRemainNum);    
 }
 
 
 /**
  * ����/�������ҳ��ͷ��������Ŀ
  * @param string $type chuzu/chushou
  * @param int    $valid 
  */
 function slidingColumn($type,$valid){
    if(empty($type)) return;
    if($type=='chushou'){
   	    switch($valid){
    		case 1:
    			$_menu = 'shangjia';
    			break;
    		case 0:
    			$_menu = 'cangku';
    			break;
    		case -1:
    			$_menu = 'manage';
    			break;
    		case 3:
    			$_menu = 'maifang';
    			break;
    		default:
    			$_menu = 'ershoufabu';
    			break;
    	}
    }elseif($type=='chuzu'){
      	switch($valid){
    		case 1:
    			$_menu = 'shangjia';
    			break;
    		case 0:
    			$_menu = 'cangku';
    			break;
    		case -1:
    			$_menu = 'manage';
    			break;
    		case 2:
    			$_menu = 'chuzu';
    			break;
    		default:
    			$_menu = 'czfabu';
    			break;
    	}    
    } elseif($type=='bussell_office' || $type=='bussell_shop'){
      	switch($valid){
    		case 1:
    			$_menu = 'shangjia';
    			break;
    		case 0:
    			$_menu = 'cangku';
    			break;
    		case -1:
    			$_menu = 'manage';
    			break;
    		case 2:
    			$_menu = 'chuzu';
    			break;
    		default:
    			$_menu = 'czfabu';
    			break;
    	}
    } elseif($type=='busrent_office' || $type=='busrent_shop'){
      	switch($valid){
    		case 1:
    			$_menu = 'shangjia';
    			break;
    		case 0:
    			$_menu = 'cangku';
    			break;
    		case -1:
    			$_menu = 'manage';
    			break;
    		case 2:
    			$_menu = 'chuzu';
    			break;
    		default:
    			$_menu = 'czfabu';
    			break;
    	}
    }
    backnav($type,$_menu);
 }
 
 /**
  * �Ǿ����˻�Ա�Ƿ���Ȩ�޲鿴�޸ĳ���/���۷�Դ(Ŀǰ��Ա����ֻ�о������Լ����͹�˾���ܲ鿴��Դ��Ϣ)
  * @param object $curuser ��ǰ�û�ʵ��
  * @param object $oA      ��ǰ�ĵ�ʵ��
  */
 function hasPermissionCheckHouse($curuser,$oA){
    $db = _08_factory::getDBO();
    $tblprefix = cls_env::getBaseIncConfigs('tblprefix');
	if($curuser->info['mchid']!=3)cls_message::show('������[���͹�˾]�����ܷ��ʡ�');  
    
	//�ҳ����͹�˾���µ����о����ˣ���������ַ���
    $midStr = ',';	
    $midSql = "select m.mid FROM {$tblprefix}members m WHERE m.mchid=2 AND pid4='".$curuser->info['mid']."' ";    
	$query = $db->query($midSql);
	while($row = $db->fetch_array($query)){
		$midStr .= $row['mid'].',';
	}
    
    //�ĵ������ߵ�mid�Ƿ�����ھ��͹�˾���µľ������ַ�����
	if(!strstr($midStr,$oA->predata['mid'])){
		$oA->message('�Բ�����ѡ����ĵ��������㹫˾�µľ����ˡ�');
	}
 }
 /**
  * ��ԱҪ��д�˱���Ļ�Ա��Ϣ���������ֻ���֤���ֻ�����ͨ����֤���ܷ�����Դ
  * @param object $curuser ��ǰ�û�ʵ��
  * @param int    $chid    �ĵ�ģ��ID
  */
 function publishAfterCheckUserInfo($curuser,$chid){    
    $mchid = $curuser->info['mchid'];
    $mfields = cls_cache::Read('mfields',$mchid);    
	$mctypes = cls_cache::Read('mctypes');
    //��Ա��֤�ֶ�����
    $fieldArr = array();
	foreach($mctypes as $k => $v){
		if(!empty($v['available']) && strstr(",$v[mchids],",",".$mchid.",")){ //����Ļ�Աģ��
            $fieldArr[]=$v['field'];
		}
	}
    
    //�鿴������Ϣ�б������Ƿ�����д
    foreach($mfields as $k => $v){
        //��ǰ�ֶ�������֤�ֶΣ������
        if(in_array($k,$fieldArr)) continue;
        //�ֶ����� �������ò���Ϊ�յ�����£� ��Ա��Ϣ�и��ֶ�Ϊ��
        if(!empty($v['available']) && !empty($v['notnull']) && empty($curuser->info[$k])){            
            m_guide("��������Ϣ��û��д������<a href='?action=memberinfo' style='color:red'>������Ƹ�����Ϣ��</a>",'fix');                      
            die();
        }
    }    
    //����������ֻ���֤���û�Ա���������趨��Ҫ�ֻ���֤�����ͣ��Ǿͱ���ͨ���ֻ���֤֮����ܷ�����Դ��Ϣ   
    if(!empty($mctypes['1']['available']) && empty($curuser->info['mctid1'])){
        $needCheckMchidArr = array_filter(explode(',',$mctypes['1']['mchids']));
        if(in_array($curuser->info['mchid'],$needCheckMchidArr)){
            m_guide("����δ��֤�绰���룬<a href='?action=mcerts' style='color:red'>�����֤�绰���롣</a>",'fix');     
            die();
        }   
    }    
 }
 
 /**
  * �������ַ�Դ/���ⷿԴ/�����󹺷�Դ�޶�
  * @param object $curuser ��ǰ�û�ʵ��
  * @param int    $chid    �ĵ�ID
  * @param object $oA      ��ǰ�û�ʵ��
  * return array
  */
 function publishLimit($curuser,$chid,$oA){  
    $style = " style='font-weight:bold;color:#F00'";
    //������������
    $exconfigs = cls_cache::cacRead('exconfigs',_08_EXTEND_SYSCACHE_PATH);
	if(empty($curuser->info['grouptype14'])){
		$exconfigs = $exconfigs['fanyuan'][0];
	}else{
		$exconfigs = $exconfigs['fanyuan'][$curuser->info['grouptype14']];
	}
    if(in_array($chid,array(2,3))){
        //�������ַ������ⷿԴ�޶�
        return housePublishLimit($exconfigs,$chid,$oA,$style);
    }elseif(in_array($chid,array(9,10))){
        //���������󹺷�Դ�޶�
        return requirementPublishLimit($exconfigs,$chid,$style);
    }    
 }
 
 /**
  * ���ַ�/���ⷿ���������޶�
  * @param array  $exconfigs ��̨���õķ�Դ��������
  * @param int    $chid      �ĵ�ID
  * @param object $oA        ��ǰ�û�ʵ��
  * @param string $style     css��ʽ
  * return array   
  */
 function housePublishLimit($exconfigs,$chid,$oA,$style){
    if(!in_array($chid,array(2,3))){
        cls_message::show("��ָ����ȷ�ĵ�ģ�͡�");
    }
    if(empty($exconfigs['total'])){ 
    	$exconfigs['total'] = 999999;
    }
    if(empty($exconfigs['daymax'])){ 
    	$exconfigs['daymax'] = 999999;
    }
    
    //ͳ�Ƴ�������ܵķ�������
    $chuzuTotalNum = cls_DbOther::ArcLimitCount(2, ''); 
    $chushouTotalNum = cls_DbOther::ArcLimitCount(3, ''); 
    $totalPublishNum = $chuzuTotalNum + $chushouTotalNum;
    $totalPublishNum = empty($totalPublishNum)?0:max(1,$totalPublishNum);
    
    //ͳ�Ƶ����ѷ����ķ�Դ������
    $dayPublishNum = cls_DbOther::ArcLimitCount($chid, 'createdate');    
    if(empty($dayPublishNum)) $dayPublishNum = '0';
    
    //�޶���Ϣ
    $limitMessageStr = '';
   	if(!empty($exconfigs['total']) && $exconfigs['total'] <= $totalPublishNum){	
        $limitMessageStr .= "��Դ������<span$style>���޶�����</span>,�����ٷ�����Դ��<br>���ķ������޶�Ϊ��<span$style>$exconfigs[total]</span> ��";
	}
	if(!empty($exconfigs['daymax']) && $exconfigs['daymax'] <= $dayPublishNum){
        $limitMessageStr .= "�����췢��<span$style>�޶�����</span>,�����ٷ�����Դ��<br>�����췢�����޶�Ϊ��<span$style>$exconfigs[daymax]</span> ��";
	}
    
    //����ҳ����ʾ��Ϣ
    $message = $oA->getmtips(array('check'=>1,'limit'=>array($exconfigs['total'],$totalPublishNum),'daymax'=>array($exconfigs['daymax'],$dayPublishNum),),'');
    
    return array('limitMessageStr'=>$limitMessageStr,'message'=>$message); 
 }
 
 /**
  * �����󹺷�����������
  * @param array  $exconfigs ��̨���õķ�Դ��������
  * @param int    $chid      �ĵ�ID 
  * @param string $style     css��ʽ
  */
 function requirementPublishLimit($exconfigs,$chid,$style){
    if(!in_array($chid,array(9,10))){
        cls_message::show("��ָ����ȷ�ĵ�ģ�͡�");
    }
    
    //ͳ���ѷ����������󹺷�Դ������
    $qiuzuTotalNum = cls_DbOther::ArcLimitCount(9, '');
	$qiugouTotalNum = cls_DbOther::ArcLimitCount(10, '');
	$totalPublishNum = $qiuzuTotalNum + $qiugouTotalNum;
    $totalPublishNum = empty($totalPublishNum)?0:max(1,$totalPublishNum);
    
    //�޶���Ϣ
    $limitMessageStr = '';
	if(!empty($exconfigs['xuqiu']) && $exconfigs['xuqiu'] <= $totalPublishNum){
        $limitMessageStr .= '����ǰ���ֻ������������������Ϊ<font color="red"><b> '.$exconfigs['xuqiu'].' </b></font>����Ϣ��<br/>��Ŀǰ�ѷ���<font color="red"><b> '.(empty($qiugouTotalNum)?'0':$qiugouTotalNum).' </b></font>���󹺺�<font color="red"><b> '.(empty($qiuzuTotalNum)?'0':$qiuzuTotalNum).' </b></font>��������Ϣ�������������������ϵ����Ա��������Ȩ�ޣ�<br/><br/>';
	}
    return array('limitMessageStr'=>$limitMessageStr);
 }

 
 
 