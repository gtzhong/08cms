<?php
/*
* �����̨ר�ú���
* �й����̨����ʽ���صĺ���
*/
!defined('M_COM') && exit('No Permission');
include_once M_ROOT.'include/admin.fun.php';
function aheader() {
	global $mcharset,$infloat,$ajaxtarget,$handlekey,$callback,$cms_abs,$aallowfloatwin,$aeisablepinyin, $cms_top, $cmsurl, $ck_plugins_enable, $ck_plugins_disable, $timestamp;
	if(!empty($callback))return;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?=$mcharset?>">
<script type="text/javascript">
	var _08_ROUTE_ENTRANCE = '<?php echo _08_ROUTE_ENTRANCE;?>';
    var CMS_ABS = "<?=$cms_abs?>" <?=empty($cmsurl) ? '' : ', CMS_URL = "'.$cmsurl.'"'?>
    <?=empty($aallowfloatwin) ? ', eisable_floatwin = 1' : ''?><?=empty($aeisablepinyin) ? '' : ', eisable_pinyin = 1'?>, 
    charset = '<?=$mcharset?>';var originDomain = originDomain || document.domain; document.domain = '<?php echo $cms_top;?>' || document.domain;
</script>
<?php 
    cls_phpToJavascript::loadJQuery();
    if ( _08_Browser::getInstance()->isMobile() )
    {
?>
<link type="text/css" rel="stylesheet" href="<?=$cmsurl?>images/common/jqueryui/css/custom-theme/smoothness/jquery-ui-1.10.2.min.css" />
<script type="text/javascript" src="<?=$cmsurl?>images/common/jqueryui/js/jquery-ui-1.11.0.js"></script>
<script type="text/javascript" src="<?=$cmsurl?>images/common/jqueryui/js/jquery.ui.touch-punch.min.js"></script>
<?php } ?>
<script type="text/javascript" src="<?=$cmsurl?>images/common/layer/layer.min.js"></script>
<script type="text/javascript" src="<?=$cmsurl?>include/js/common.js"></script>
<script type="text/javascript" src="<?=$cmsurl?>include/js/admina.js"></script>
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
<script type="text/javascript" src="<?=$cmsurl?>include/js/pinyin.js"></script>
<script type="text/javascript" src="<?=$cmsurl?>include/js/validator.js"></script>
<link type="text/css" rel="stylesheet" href="<?=$cmsurl?>images/admina/contentsAdmin.css" />
<link type="text/css" rel="stylesheet" href="<?=$cmsurl?>images/common/validator.css" />
<link type="text/css" rel="stylesheet" href="<?=$cmsurl?>images/common/window.css" />
</head>
<body style="overflow-x:hidden;">
<div id="append_parent"></div>
<div class="mainBox"><?
}
function afooter(){
	global $copyright,$cms_power,$infloat,$no_afooter,$callback;
	if(!empty($callback)){
		$s = ob_get_contents();
		ob_clean();
		mexit("js_callback('" . addcslashes($s, "\\\r\n'") . "','$callback')");
	}

    if ( _08_DEBUGTAG && class_exists('_08_Profiler') && empty($_POST) )
    {
        $global = cls_envBase::_GET_POST();
        $error = _08_Profiler::getInstance();
        $mark = $error->mark('"' . http_build_query($global) . '"');
    }
    else
    {
    	$mark = '';
    }
     
	if(empty($no_afooter)){
		if(!$infloat){
?>
</div>
<div class="blank9"></div>
<div class="copyFoot">
	<p>&#25628;&#34382;&#31934;&#21697;&#31038;&#21306;&#119;&#119;&#119;&#46;&#115;&#111;&#117;&#104;&#111;&#46;&#110;&#101;&#116;&#25552;&#20379;&#48;&#56;&#99;&#109;&#115;&#25151;&#20135;&#38376;&#25143;&#118;&#55;&#46;&#48;&#26071;&#33328;&#29256;</p><?php }?>
</div>
<div class="blank9"></div>
<?php echo $mark;?>
</body>
</html><?
	}
}

function adminlog($action='',$detail=''){
	global $timestamp,$onlineip;
	$curuser = cls_UserMain::CurUser();
	if(empty($action)) return;
	if($curuser->info['isfounder']){
		$agtname = '��ʼ��';
	}else{
		$usergroups = cls_cache::Read('usergroups',2);
		$agtname = $usergroups[$curuser->info['grouptype2']]['cname'];
	}
	$record = mhtmlspecialchars(
		$timestamp."\t".
		$curuser->info['mid']."\t".
		$curuser->info['mname']."\t".
		$agtname."\t".
		$onlineip."\t".
		$action."\t".
		$detail);
	record2file('adminlog',$record);
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
		$multipage = ($curpage - $offset > 1 && $pages > $page ? "<a href=\"{$mpurl}page=1\" class=\"p_redirect\"$onclick>1...</a>" : '').($curpage > 1 && !$simple ? "<a href=\"{$mpurl}page=".($curpage - 1)."\" class=\"p_redirect\"><<</a>" : '');
		for($i = $from; $i <= $to; $i++) $multipage .= $i == $curpage ? "<a class=\"p_curpage\">$i</a>" : "<a href=\"{$mpurl}page=$i\" class=\"p_num\"$onclick>$i</a>";
		$multipage .= ($curpage < $pages && !$simple ? "<a href=\"{$mpurl}page=".($curpage + 1)."\" class=\"p_redirect\"$onclick>>></a>" : '').($to < $pages ? "<a href=\"{$mpurl}page=$pages\" class=\"p_redirect\"$onclick>...$pages</a>" : '').
			(!$simple && $pages > $page ? "<a class=\"p_pages\" style=\"padding: 0px\"><input class=\"p_input\" type=\"text\" name=\"custompage\" onKeyDown=\"if(event.keyCode==13) {window.location='{$mpurl}page='+this.value; return false;}\"></a>" : '');
		$multipage = $multipage ? "<div class=\"p_bar\">".(!$simple ? "<a class=\"p_total\">&nbsp;$num&nbsp;</a>" : '')."$multipage ".(!$simple && $pages > $page ? "<input type=\"button\" name=\"jump\" value=\"��ת\" onclick=\"window.location='{$mpurl}page='+ document.getElementsByName('custompage')[0].value;\">" : "")."</div>" : '';
	}
	return $multipage;
}
function tabheader($tname='',$fname='',$furl='',$col=2,$fupload=0,$checksubmit=0,$newwin=0,$method='post'){
	if($fname) echo form_str($fname,$furl,$fupload,$checksubmit,$newwin,$method);
	echo "<div class=\"conlist1\">$tname</div>";
    tabheader_e();
}
function tabheader_e(){
	echo "<table width=\"100%\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\" tb tb2 bdbot\">\n";
}
function tabfooter($bname='',$bvalue='',$addstr='',$fmclose=1){//$fmclose�Ƿ�ر�form
	global $aListSetReset;
	$bvalue = empty($bvalue) ? '�ύ' : $bvalue;
	echo "</table>\n";
	if($aListSetReset){
		echo $aListSetReset;
		$aListSetReset = '';
	}
	echo $bname ? "<br /><input class=\"btn\" type=\"submit\" name=\"$bname\" value=\"$bvalue\">\n" : '';
	echo $addstr ? $addstr : '';
	echo $bname && $fmclose ? "</form>\n" : '';
	echo "<div class=\"blank9\"></div>";
}
function trcategory($arr = array(), $tabID='1'){
	global $ckpre,$entry,$extend,$action,$aListSetReset;
	$arr = array_filter($arr);
	$baseID = empty($entry)?'entID':$entry;
	foreach(array('extend','action','tabID') as $k) empty($$k) || $baseID .= '_'.$$k;
	$aListSet_tCfg = ''; //$i = 0;
	$trStr = "<tr id=\"TR_$baseID\" class=\"title txt w40\">\n";
	foreach ($arr as $v) {
	   $iCfg = '';
	   if(is_array($v)){
		  foreach ($v as $j => $vsub) $iCfg .= $v[$j].'|';
		  $iCfg .= '|';
	   }else{
		  $iCfg .= $v.'||'; //echo $value;
	   }
	   $iArr = explode('|',$iCfg);
	   $aListSet_tCfg .= strtoupper($iArr[2]).'|'; // S/H
	   $iVal = $iArr[0];
	   if(strlen($iArr[1])>1){ // txtR
		   $trStr .= "\n<td class=\"title $iArr[1]\">$iVal</td>\n";
	   }else{
		   if(strlen($iArr[1])=='') $iArr[1] = 'C';
		   else $iArr[1] = strtoupper($iArr[1]);
		   if($iArr[1]=='C'){
			   $trStr .= "\n<td class=\"title txtC\">$iVal</td>\n";
		   }else if($iArr[1]=='R'){
			   $trStr .= "\n<td class=\"title txtR\">$iVal</td>\n";
		   }else if($iArr[1]=='L'){
			   $trStr .= "\n<td class=\"title txtL\">$iVal</td>\n";
		   }else{
			   $trStr .= "\n<td class=\"title\">$iVal</td>\n";
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
function trcontent($arr = array()){
	echo "<tr>\n";
	foreach($arr as $v) echo "<td class=\"".(is_array($v) ? $v[1] : 'txtC')."\">".(is_array($v) ? $v[0] : $v)."</td>\n";
	echo "</tr>\n";
}
function viewcheck($param){
	$name = $value = $body = $title = '';$noblank = 0;
	extract($param, EXTR_OVERWRITE);
	return ($noblank ? '' : '&nbsp; &nbsp; ')."<input class=\"checkbox\" type=\"checkbox\" name=\"$name\" value=\"1\" onclick=\"alterview('$body')\"".(empty($value) ? '' : ' checked').">$title";
}
function strbutton($name,$value='�ύ',$class='btn',$onclick = ''){
	return "<input class=\"$class\" type='".($onclick ? 'button' : 'submit')."' name=\"$name\" value=\"$value\"".($onclick ?  " onclick=\"$onclick\"" : '').">";
}

function url_nav($title='',$arr = array(),$current='',$numpl=8){//�����ѡ������ӣ�������ǰҳ
	$multi = count($arr) < $numpl ? 0 : 1;
	echo "<div class=\"itemtitle\"><h3".(!$multi ? '' : ' class=h3other').">$title</h3><ul class=\"tab1".(!$multi ? '' : '  tab0 bdtop')."\">\n";
	foreach($arr as $k => $v){
		$nclassstr = (!$multi ? '' : 'td24').($k == $current ? ' current' : '');
		echo "<li".($nclassstr ? " class=\"$nclassstr\"" : '')."><a href=\"$v[1]\"><span>$v[0]</span></a></li>\n";
	}
	echo "</ul></div><div class=\"blank15h\"></div>";
}
function trrange($trname,$arr1,$arr2,$type='text',$guide='',$width = '25%'){
	$flag = $type == 'calendar' ? 1 : ($type == 'datetime' ? 2 : 0);
	echo "<tr><td width=\"$width\" class=\"txt txtright fB borderright\">$trname</td>\n"
		,"<td class=\"txt txtleft\">\n"
		,empty($arr1[2]) ? '' : $arr1[2]
			,'<input type="text" size="',empty($arr1[4]) ? ($flag ? ($flag == 1 ? 13 : 23) : 10) : $arr1[4]
				,"\" id=\"$arr1[0]\" name=\"$arr1[0]\" value=\"",mhtmlspecialchars($arr1[1]),'"'
				,$flag ? " class=\"Wdate\" onfocus=\"WdatePicker({readOnly:true" . ($flag == 1 ? '' : ",dateFmt:'yyyy-MM-dd HH:mm:ss'") . "})\"" : ''
				,empty($arr1['validate']) ? '' : $arr1['validate']
				,"><span id=\"alert_$arr1[0]\" name=\"alert_$arr1[0]\" class=\"red\"></span>",empty($arr1[3]) ? '' : $arr1[3]
		,empty($arr2[2]) ? '' : $arr2[2]
			,'<input type="text" size="',empty($arr2[4]) ? ($flag ? ($flag == 1 ? 13 : 23) : 10) : $arr2[4]
				,"\" id=\"$arr2[0]\" name=\"$arr2[0]\" value=\"",mhtmlspecialchars($arr2[1]),'"'
				,$flag ? " class=\"Wdate\" onfocus=\"WdatePicker({readOnly:true" . ($flag == 1 ? '' : ",dateFmt:'yyyy-MM-dd HH:mm:ss'") . "})\"" : ''
				,empty($arr2['validate']) ? '' : $arr2['validate']
				,"><span id=\"alert_$arr2[0]\" name=\"alert_$arr2[0]\" class=\"red\"></span>",empty($arr2[3]) ? '' : $arr2[3]
		,$guide ? "<div class=\"tips1\">$guide</div>" : ''
		,"</td></tr>";
}
function tr_regcode($rname, $params = array()){
	global $cms_regcode,$cms_abs,$timestamp;
    $fromName = empty($params['formName']) ? NULL : $params['formName'];
    $class = empty($params['class']) ? 'regcode' : $params['$class'];
    $inputName = empty($params['inputName']) ? '' : $params['inputName'];
    $inputString = empty($params['inputString']) ? '' : $params['inputString'];
    $code = _08_HTML::getCode($rname, $fromName, $class, $inputName, $inputString);
	if($cms_regcode && in_array($rname,explode(',',$cms_regcode))){
		echo "<tr><td class=\"txt txtright fB borderright\"><font color='red'> * </font>��֤��</td><td class=\"txt txtleft\">" . $code ."</td></tr>";
	}
}
function templatebox($trname,$varname,$template='',$rows,$cols){
    global $handlekey, $infloat;
	$lang = array('ctag' => '���ϱ�ʶ', 'rtag' => '�����ʶ');
	$insertstr = "<a class=\"btn\" href=\"#\" onclick=\"javascript:openCreateSelectText('{$varname}', 'update');\">�༭ѡ�б�ʶ</a>&nbsp;&nbsp;<a class=\"btn\" href=\"#\" onclick=\"javascript:openCreateSelectText('{$varname}', 'insert');\">�����±�ʶ</a>&nbsp;&nbsp;";
	$insertstr .= "<a class=\"btn\" href=\"#\" onclick=\"javascript:openCreateSelectText('{$varname}', 'insert_');\">����ԭʼ��ʶ</a>&nbsp;&nbsp;";
	foreach(array('ctag','rtag') as $ttype) $insertstr .= "<a class=\"btn\" href=\"?entry=mtags&action=mtagsedit&ttype=$ttype\" target=\"mtagcodewin\">�鿴{$lang[$ttype]}</a>&nbsp;&nbsp;";
	echo "<tr><td class=\"txt txtright fB\">* $trname</td><td class=\"txt txtleft\">$insertstr</td></tr>".
	"<tr><td class=\"txt\" colspan=\"2\"><textarea class=\"js-resize textarea\" rows=\"$rows\" name=\"$varname\" id=\"$varname\" cols=\"$cols\" onclick=\"setCaretpos('$varname', 'insert_');\" onkeyup=\"setCaretpos('$varname', 'insert_', event);\">".htmlspecialchars(str_replace("\t","    ",$template))."</textarea></td></tr>";
}

function tab_list($arr = array(),$num = 2){
	if(empty($arr)) return '';
	$ret = "<table width=\"100%\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\">";
	$i = 0;
	$width = floor(100 / $num).'%';
	foreach($arr as $v){
		if(!($i % $num)) $ret .= "<tr>";
		$ret .= "<td class=\"txt\" width=\"$width\">$v</td>\n";
		$i ++;
		if(!($i % $num)) $ret .= "</tr>\n";
	}
	if($i % $num){
		while($i % $num){
			$ret .= "<td class=\"txt\" width=\"$width\"></td>\n";
			$i ++;
		}
		$ret .= "</tr>\n";
	}
	$ret .= "</table><div class=\"blank9\"></div>\n";
	return $ret;
}
function trspecial($trname,$varr = array()){
	$lcls = 'txt txtright fB';$rcls = 'txt txtleft';
	$varr['width'] = empty($varr['width']) ? '20%' : $varr['width'];
	if(in_array($varr['type'],array('image','images','flash','flashs','media','medias')))
		$guidestr = $varr['guide'] ? "<div class=\"tips1\">$varr[guide]</div>" : '';
	else
		$guidestr = $varr['guide'] ? (!empty($varr['mode']) ? "<div class=\"tips1\">$varr[guide]</div>" : "<font class=\"gray\">$varr[guide]</font>") : '';
	if($varr['type'] == 'htmltext'){
		echo empty($varr['mode']) ? "<tr><td colspan=\"2\" class=\"txt txtleft fB\">".$trname.$guidestr."</td></tr><tr><td colspan=\"2\" class=\"$rcls\">\n" : "<tr><td width=\"$varr[width]\" class=\"$lcls\">".$trname."</td><td class=\"$rcls\">\n";
		echo $varr['frmcell'].$guidestr;
		echo "</td></tr>\n";
	}else{
		$varr['addcheck'] && $guidestr = ' '.$varr['addcheck'].$guidestr;
		echo "<tr><td width=\"$varr[width]\" class=\"$lcls\">".$trname."</td>\n";
		echo "<td class=\"$rcls\">".$varr['frmcell'].$guidestr."</td></tr>\n";
	}
}
function trbasic($trname, $varname, $value = '', $type = 'text', $arr = array()) {//w,hΪ�����ı�(size)������ı�ָ����ȼ��߶�(px)
	$guide=''; $width = '20%'; $rshow = 1; $rowid = ''; $validate = '';$w = 0;$h = 0;$addstr = '';$ops = NULL;
	extract($arr, EXTR_OVERWRITE);
	echo "<tr" . ($rowid ? " id=\"$rowid\"" : '') . ($rshow ? '' : ' style="display:none"') . "><td width=\"$width\" class=\"txt txtright fB\">$trname</td>\n";
	echo "<td class=\"txt txtleft\">\n";
	if($type == 'radio') {
		$check[$value ? 'true' : 'false'] = "checked";$check[$value ? 'false' : 'true'] = '';
		echo "<input type=\"radio\" class=\"radio\" id=\"{$varname}_1\" name=\"$varname\" value=\"1\" $check[true] $validate><label for=\"{$varname}_1\"> ��</label> &nbsp; &nbsp; \n"
			."<input type=\"radio\" class=\"radio\" id=\"{$varname}_0\" name=\"$varname\" value=\"0\" $check[false] $validate><label for=\"{$varname}_0\"> ��</label> \n";
	}elseif($type == 'select') {
		echo "<select style=\"vertical-align: middle;\" id=\"$varname\" name=\"$varname\" $validate>$value</select>";
	}elseif($type == 'text' || $type == 'password'){
		$w = $w ? $w : 25;
		echo "<input type=\"".($type == 'password' ? $type : 'text')."\" size=\"$w\" id=\"$varname\" name=\"$varname\" value=\"".mhtmlspecialchars($value)."\" $validate />\n";
		if(!empty($ops)){//���̶�ѡ�value�Զ����뵽�ı���
			echo "<select style=\"vertical-align: middle;\" onchange=\"document.getElementById('$varname').value += this.value;\">".makeoption($ops)."</select>";
		}
	}elseif($type == 'calendar') {
		$w = $w ? "size=\"$w\"" : "style=\"width:92px\""; 
		echo "<input type=\"text\" $w id=\"$varname\" name=\"$varname\" value=\"".mhtmlspecialchars($value)."\" class=\"Wdate\" onfocus=\"WdatePicker({readOnly:true})\" $validate />\n";
	}elseif($type == 'datetime') {
		$w = $w ? "size=\"$w\"" : "style=\"width:152px\"";
		echo "<input type=\"text\" $w id=\"$varname\" name=\"$varname\" value=\"".mhtmlspecialchars($value)."\" class=\"Wdate\" onfocus=\"WdatePicker({readOnly:true,dateFmt:'yyyy-MM-dd HH:mm:ss'})\" $validate />\n";
	}elseif($type == 'textarea'){
		$w = $w ? $w : 300;$h = $h ? $h : 100;
		echo "<textarea class=\"js-resize\" name=\"$varname\" id=\"$varname\" style=\"width:{$w}px;height:{$h}px\" $validate>".mhtmlspecialchars($value)."</textarea>\n";
	}else{
		echo $value;
		$validate && print("<input type=\"hidden\" $validate />");
	}
	echo $addstr;
	if($guide) echo "<div class=\"tips1\">$guide</div>";
	echo "</td></tr>\n";
}
function sourcemodule($trname,$svar,$sarr,$svalue,$sview,$idsvar,$idsarr,$idsvalue=array(),$width='25%',$rshow=1, $rowid='',$vmode = 0){
	echo "<tr" . ($rowid ? " id=\"$rowid\"" : '') . ($rshow ? '' : ' style="display:none"') . "><td width=\"$width\" class=\"txt txtright fB\">".$trname."</td>\n";
	echo "<td class=\"txt txtleft\">\n";
	echo "<select style=\"vertical-align: middle;\" name=\"$svar\" onchange=\"checkidsarr(this.value,'$sview','".$idsvar."')\">".makeoption($sarr,$svalue)."</select>";
	echo "<input id=\"$idsvar\" name=\"$idsvar\" onfocus=\"setidswithi(this,'$vmode')\" type=\"\"".($svalue == $sview ? '' : ' style="visibility:hidden"')." value=\"" . implode(',', $idsvalue) . "\" />";
	if(!$vmode){
		echo "<br /><select  id=\"mselect_$idsvar\" onchange=\"setidswiths(this)\" size=\"5\" multiple=\"multiple\" style=\"display:".($svalue == $sview ? '' : 'none').";width: 40%;\">\n";
		foreach($idsarr as $k => $v)  echo "<option value=\"$k\"".(in_array($k,$idsvalue) ? ' selected' : '').">{$v}</option>";
		echo "</select>";
	}else{
		echo '<div id="mselect_'.$idsvar.'_area"'.($svalue == $sview ? '' : ' style="display:none"').'>';
		$i = 0;
		foreach($idsarr as $k => $v){
			$checked = in_array($k,$idsvalue) ? ' checked' : '';
			echo "<input class=\"checkbox\" type=\"checkbox\" id=\"mselect_{$idsvar}_$k\" onchange=\"setidswiths(this,1)\" value=\"$k\"$checked><label for=\"mselect_{$idsvar}_$k\">$v</label>";
			$i++;
			echo !($i % 6) ?  '<br />' : '&nbsp;  &nbsp;';
		}
		echo '</div>';
	}
	if(strpos($trname,'is_self_reg')) echo '<div class="tips1">�ĵ��ڵı�ǩ����Ҫʹ���Զ���ϵ��"����"���ԣ��ĵ������ݸü������ԡ�</div>';
	echo "</td></tr>\n";
}

function a_guide($str,$txt = 0){
	if(!$txt){
	    _08_FilesystemFile::filterFileParam($str);
		@include M_ROOT.'dynamic/aguides/'.$str.'.php';
		echo "<!--$str-->";
	}else $aguide = &$str;
	// ����-ռλ������(Ҫ֧������ο�#(\[[a-z_A-Z0-9]{1,32}\])*#)
	// demo {$cms_abs@}
    // ����ԭ���ܶ��ļ�Ҫʹ������ {$cms_abs}��ԭʼ��ǩ������ı���
    // ����Ϊ������ռλ����ʽ���˸�@����Ϊ
	$aguide = empty($aguide) ? '' : $aguide;
	preg_match_all('/{[$][a-z_A-Z][a-z_A-Z0-9]{2,31}@}/',$aguide,$ma);
	if(!empty($ma[0])){
		foreach($ma[0] as $v){
			$k = str_replace(array('{','$','@}'),'',$v);
			$aguide = str_replace($v,@$GLOBALS[$k],$aguide);
		}
	}
	if(!empty($aguide)) echo "<div class=\"blank12\"></div><div class=\"tiShiTitle\">��ʾ˵��</div><table width=\"100%\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"txtleft lineheight20 tiShi\"><tr><td>$aguide</td></tr></table>\n";
}

function withOutUrl($key = 'isframe', $url = ''){
	$extra = $url ? $url : "?$_SERVER[QUERY_STRING]";
	if(preg_match("/([?&]){$key}[^&]*(&?)/", $extra, $match)){
		$extra = str_replace($match[0], $match[2] ? $match[1] : '', $extra);
	}else{
		$extra = $url;
	}
	return $extra;
}
function deep_allow($no_deepmode = 0,$url = ''){
	if($no_deepmode) cls_message::show('�ܹ�����ģʽ�½�ֹ�Ĳ���������ϵ��ʼ��',$url);
	return true;
}
function modpro($re = ''){
	global $cms_idkeep;
	return $cms_idkeep ? ($re ? $re : TRUE) : ($re ? '' : FALSE);
}

//��ʾ�����̨�������չ�˵�
//arr��Դ���飺array(id =>array('level' => int,'title'=>'xxx','active' => 0/1,))
//type���ͣ�0(��Ŀ)��1(����)��2(��Ա)��3(����)��c$coid(��ϵ)
function ViewBackMenu($arr = array(),$type = 0){
	$re = '';
	if(!$arr) return $re;	
	$i = 0;$space = '			';
	foreach($arr as $k => $v){
		$editstr = $v['active'] ? "<em><a href=\"javascript:\" onclick=\"get_operate('$k',$type)\" title=\"{$v['title']}:$k\">{$v['title']}</a></em>" : "<em>{$v['title']}</em>";
		if($i < $v['level']){
			$i++;
			$re .= "<ul>\n$space	<li>$editstr";
			$space .= '	';
		}else{
			if($i > $v['level']){
				while($i-- > $v['level']){
					$space = substr($space,0,$i+3);
					$re .= "</li></ul>\n$space";
				}
				$i++;
			}
			$re .= "</li>\n$space<li>$editstr";
		}
	}
	if($i > 0){
		while($i-- > 0){
			$space = substr($space,0,$i+3);
			$re .= "</li></ul>\n$space";
		}
	}
	$re = substr($re,5)."</li>\n".substr($space,0,-1);
	return $re;
}


// ��ʾ Ȩ������ ѡ��������ʾ
// $title:���⣬�磺�Զ����Ȩ������
// $fmid:��ID,�磺'channelnew[autocheck]'
// $fmdef:��ֵ,�磺'1'
// $source:ѡ����Դ����aread����������ڲ�$sotitle
// $soext:��չѡ� ��check����������ڲ�$soextcfg�����Զ�������
// $guide:˵����Ϣ��
function setPermBar($title, $fmid='', $fmdef='', $source='', $soext='open', $guide=''){
	global $_sp_rowid;
	$_sp_rowid = $fmid; //str_replace(array("[","]","",),"_",$fmid);
	$sotitle = array(
		'aread'=>'���',
		'aadd'=>'�ĵ�',
		'fadd'=>'����',
		'cuadd'=>'����',
		'chk'=>'���',
		'down'=>'����',
		'menu'=>'�˵�',
		'tpl'=>'ģ��',
		'other'=>'����',
	);
	$soextcfg = array(
		'open'=>array(0=>'��ȫ����',),
		'check'=>array(0=>'���Զ����', 1=>'ȫ���Զ����',),
	);
	
	$sign = '';
	if($soext=='check'){ //�Զ����,�ø���,�����ط�����Ҫ�ø���,�봦��
		$sign = '-';
	}elseif(is_array($soext) && !empty($soext['check'])){
		$sign = '-';
		unset($soext['check']);
	}
	if(is_string($soext) && isset($soextcfg[$soext])){
		$socfg = $soextcfg[$soext];
	}elseif(is_array($soext)){
		$socfg = $soext;
	}else{
		$socfg = array();
	} 
	$pmcarr = pmidsarr($source);
	if(abs($fmdef)>0 && isset($pmcarr[abs($fmdef)])){ // && $fmdef>0
		$pmcname = $pmcarr[abs($fmdef)];
		$pmlink = "<a href='?entry=permissions&action=permissionsdetail&pmid=".(abs($fmdef))."' onclick=\"return floatwin('open_permcase',this)\">$pmcname</a>";
	}else{
		$pmlink = '(�޶�ӦȨ�޷���)';	
	} 
	foreach($pmcarr as $k => $v) $k && $socfg[$sign.$k] = $v;
    
	$guide = $guide ? "<div>$guide</div>" : "";
	$gubase = "<div>Ȩ�޷���������ڣ�ϵͳ����=>��������";
	$gubase .= "=><a href='?entry=permissions&action=permissionsedit' onclick=\"return floatwin('open_permcase',this)\">Ȩ�޷���</a>";
	$gubase .= "=>{$sotitle[$source]}";
	$gubase .= "=><span id='spBar_g5$_sp_rowid'>$pmlink</span></div>";
	$options = makeoption($socfg,$fmdef); 
	trbasic($title,$fmid,$options,'select',array('guide'=>$guide.$gubase,'validate'=>"onchange=\"setPermBar('$_sp_rowid')\""));
	//'rowid'=>"spBar_r0$_sp_rowid",

}

// �����ȣ������(�ĵ�/��Ա)ģ�� ���н�����������
// vals, ��ʼֵ,
// from, chid-�ĵ�ģ��, mchid-��Աģ��
function setChidsBar($vals='',$from='chid'){
	if($from=='mchid'){
		$this_chs = cls_cache::Read('mchannels');	
		$farr = cls_mchannel::mchidsarr();
		$flag = "��������»�Ա";
	}else{
		$this_chs = cls_channel::Config();	
		$farr = cls_channel::chidsarr(0);
		$flag = "���������ģ��"; //�ĵ�
	}
	
    if(modpro()){ 
        trbasic($flag,'',makecheckbox('communew[cfgs][chids][]',$farr,$vals,5),'');
  	}else{
      	 if(!empty($vals)){
    		$str_temp = '';
    		foreach($this_chs as $k=>$v){
    			if(in_array($k,$vals)) $str_temp .=  '<input checked type="checkbox" class="checkbox"  name="communew[cfgs][chids][]" value="'.$k.'" id="ch_id'.$k.'" onclick="return false" >'.$v['cname'].'('.$k.')&nbsp;  &nbsp;';
    		}
    		trbasic($flag,'',$str_temp,'');
    	}else trbasic($flag,'','','',array('guide'=>'��������'));
    }
}

// ��ȡ��ģ��������б�
function listTplpacks($type='opt'){
	$re = array(); 
	$iterator = new DirectoryIterator($path = M_ROOT.'template');
	foreach($iterator as $ipath){
		if($ipath->isDir()){
			$tpl = $ipath->getFileName();
			if(is_dir($path.DS.$tpl.DS.'tpl')){ //��Чģ��Ŀ¼
				$cfg = @include($path.DS.$tpl.DS.'tpl_pack_config.php');
				$packname = "[$tpl] ".(isset($cfg['packname']) ? $cfg['packname'] : ' ��--δ����--��');
				$extends = isset($cfg['extends']) ? $cfg['extends'] : '';
				$packname .= $extends ? " (�̳���[$extends]ģ��)" : " (����ģ��)";
				$re[$tpl] = $type == 'opt' ? $packname : $extends;
			}
		}
	}
	return $re;
}

// �����ǩ : �ӻ���ģ�� -=> ��չ����ǰģ��
function rtag_basic2extend($tname){
	$org = cls_tpl::rel_path($tname,'base');
	$obj = cls_tpl::rel_path($tname,'dir');
	if(file_exists($org)){ // ��ֹ��������ʵ��Ŀ¼��û�д��ļ�
		return copy($org, $obj);
	}else{
		return false;
	}
}
// ģ������ : �жϵ�ǰģ��tpl���Ƿ����ļ�
// istpl : �Ƿ�Ϊģ�� �������ǩ, 1:ģ��, 0:����
function file_tplexists($tname,$istpl=1){ //
	if(empty($istpl)){ //�����ǩ
		$rtag = cls_cache::Read('rtag',$tname,'');
		$tname = $rtag['template'];
	}
	return file_exists(cls_tpl::rel_path($tname,'dir'));
}
