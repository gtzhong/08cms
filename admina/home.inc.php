<?php
(!defined('M_COM') || !defined('M_ADMIN')) && exit('No Permission');
foreach(array('cmsinfos') as $k) $$k = cls_cache::Read($k); 
$updatetime = @filemtime(M_ROOT.'./dynamic/cache/cmsinfos.cac.php');
$now_svr = strtolower($_SERVER["SERVER_NAME"]);
$tm_base = mktime(0,0,0);


$opsarr = array(
'ck' => "checked='1'",
'nock' => "checked='0'",
'm' => "createdate>'".($timestamp-30*24*3600)."'",
'w' => "createdate>'".($timestamp-7*24*3600)."'",
'd3' => "createdate>'".($timestamp-3*24*3600)."'",
'd1' => "createdate>'".($tm_base)."'",
);

$mem_gt = array(
'mem_gt14' => '8', 
/*'mem_gt31' => '102', 
'mem_gt32' => '104', */
);

$tblarr = array(
'archive3' => atbl(3),
'archive2' => atbl(2),
'archive4' => atbl(4),
'archive107' => atbl(107),
'archive9' => atbl(9),
'archive10' => atbl(10),
'archive1' => atbl(1),
'archive106' => atbl(106),
//'archive108' => atbl(108),

'member1' => 'members',
'member2' => 'members',
'member3' => 'members',
//'member11' => 'members',
//'member12' => 'members',
'member13' => 'members',

'mem_gt14' => 'members',
/*'mem_gt31' => 'members', 
'mem_gt32' => 'members', */

'commu_zixun' => 'commu_zixun',
'commu_dp' => 'commu_zixun',
'commu_yx' => 'commu_yx',
'commu_jubao' => 'commu_jubao',
'commu_kanfang' => 'commu_kanfang',

'commu_df' => 'commu_df',
'commu_fyyx' => 'commu_fyyx',
'commu_weituo' => 'commu_weituo',
'commu_answers' => 'commu_answers',
'commu_jbask' => 'commu_jbask',

);

$tbllang = array(
'archive3' => '���ַ�',
'archive2' => '����',
'archive4' => '¥��',
'archive107' => '�ؼ۷�',
'archive5' => '�·��Ź�',
'archive9' => '����',
'archive10' => '��',
'archive1' => '��Ѷ',
'archive106' => '�ʴ�',
'archive108' => '��Ƹ',

'member1' => '��ͨ��Ա',
'member2' => '������',
'member3' => '���͹�˾',
'member11' => 'װ�޹�˾',
'member12' => 'Ʒ���̼�',
'member13' => '��¥��˾',

'mem_gt14' => '�߼�������', 
/*'mem_gt31' => 'VIP��˾', 
'mem_gt32' => 'VIP�̼�', */

'commu_zixun' => '��Ѷ����',
'commu_dp' => '¥�̵���',
'commu_yx' => '¥������',
'commu_jubao' => '��Դ�ٱ�',
'commu_kanfang' => '���������',

'commu_df' => '���϶���',
'commu_fyyx' => '��Դ����',
'commu_weituo' => 'ί�з�Դ',
'commu_answers' => '�ʴ��',
'commu_jbask' => '�ʴ�ٱ�',

);

$tblurl = array(
'archive3' => '?entry=extend&extend=usedhousearchives&caid=3',
'archive2' => '?entry=extend&extend=chuzuarchives&caid=4',
'archive4' => '?entry=extend&extend=loupanarchives&caid=2',
'archive107' => '?entry=extend&extend=tejiaarchives&caid=559',
'archive5' => '?entry=extend&extend=dinggouarchives&caid=5',
'archive9' => '?entry=extend&extend=qiuzuarchives&caid=9',
'archive10' => '?entry=extend&extend=qiugouarchives&caid=10',
'archive1' => '?entry=extend&extend=zixunarchives&caid=1',
'archive106' => '?entry=extend&extend=qa_s&caid=516',
'archive108' => '?entry=extend&extend=zhaopinarchives&caid=558',

'member1' => '?entry=extend&extend=memberspt&mchid=1',
'member2' => '?entry=extend&extend=membersjr&mchid=2',
'member3' => '?entry=extend&extend=membersjs&mchid=3',
'member11' => '?entry=extend&extend=memberszx&mchid=11',
'member12' => '?entry=extend&extend=memberszx&mchid=12',
'member13' => '?entry=extend&extend=membersales&mchid=13',

'mem_gt14' => '?entry=extend&extend=membersjr&mchid=2',
'mem_gt31' => '?entry=extend&extend=membersjr&mchid=11',
'mem_gt32' => '?entry=extend&extend=membersjr&mchid=12',

'commu_zixun' => '?entry=extend&extend=comments&cuid=1&chid=1',
'commu_dp' => '?entry=extend&extend=comments&cuid=1&chid=4',
'commu_yx' => '?entry=extend&extend=yixiangs&caid=2',
'commu_jubao' => '?entry=extend&extend=jubaos',
'commu_kanfang' => '?entry=extend&extend=commu_kanfangbm',

'commu_df' => '?entry=extend&extend=dfangs&caid=5',
'commu_fyyx' => '?entry=extend&extend=commu_yixiang&caid=3',
'commu_weituo' => '?entry=extend&extend=weituo&caid=3',
'commu_answers' => '?entry=extend&extend=commu_answers&caid=516',
'commu_jbask' => '?entry=extend&extend=jubaos&caid=3',

);
if($timestamp - $updatetime > 3600 * 4){
	$cmsinfos['dbversion'] = $db->result_one("SELECT VERSION()");
	$cmsinfos['dbsize'] = 0;
	$query = $db->query("SHOW TABLE STATUS LIKE '$tblprefix%'", 'SILENT');
	while($table = $db->fetch_array($query)) {
		$cmsinfos['dbsize'] += $table['Data_length'] + $table['Index_length'];
	}
	$cmsinfos['dbsize'] = $cmsinfos['dbsize'] ? sizecount($cmsinfos['dbsize']) : 'δ֪';
	$cmsinfos['attachsize'] = $db->result_one("SELECT SUM(size) FROM {$tblprefix}userfiles");
	$cmsinfos['attachsize'] = is_numeric($cmsinfos['attachsize']) ? sizecount($cmsinfos['attachsize']) : 'δ֪';
	$cmsinfos['sys_mail'] = @ini_get('sendmail_path') ? 'Unix Sendmail ( Path: '.@ini_get('sendmail_path').')' : (@ini_get('SMTP') ? 'SMTP ( Server: '.ini_get('SMTP').')' : 'Disabled');
	$cmsinfos['serverip'] = @$_SERVER["SERVER_ADDR"];
	$cmsinfos['servername'] = @$_SERVER["SERVER_NAME"];
	foreach($tblarr as $k => $v){
	    if ( empty($v) )
        {
             continue;
        }
		foreach($opsarr as $x => $y){
			if(substr($k,0,7) == 'archive'){
				$chid = str_replace('archive','',$k);
				$x == 'ck' && $y = "chid='$chid' AND checked='1'";
				$x == 'nock' && $y = "chid='$chid' AND checked='0'";
				$x == 'm' && $y = "chid='$chid' AND createdate>'".($timestamp-30*24*3600)."'";
				$x == 'w' && $y = "chid='$chid' AND createdate>'".($timestamp-7*24*3600)."'";
				$x == 'd3' && $y = "chid='$chid' AND createdate>'".($timestamp-3*24*3600)."'";
				$x == 'd1' && $y = "chid='$chid' AND createdate>'".($tm_base)."'";
			}elseif(substr($k,0,6) == 'member'){
				$mchid = str_replace('member','',$k);
				$x == 'ck' && $y = "mchid='$mchid' AND checked='1'";
				$x == 'nock' && $y = "mchid='$mchid' AND checked='0'";				
				$x == 'm' && $y = "mchid='$mchid' AND regdate>'".($timestamp-30*24*3600)."'";
				$x == 'w' && $y = "mchid='$mchid' AND regdate>'".($timestamp-7*24*3600)."'";
				$x == 'd3' && $y = "mchid='$mchid' AND regdate>'".($timestamp-3*24*3600)."'";
				$x == 'd1' && $y = "mchid='$mchid' AND regdate>'".($tm_base)."'";
			}elseif(substr($k,0,6) == 'mem_gt'){ 
				$gtid = str_replace('mem_gt','',$k); 
				$x == 'ck' && $y = "grouptype$gtid='$mem_gt[$k]' AND checked='1'";
				$x == 'nock' && $y = "grouptype$gtid='$mem_gt[$k]' AND checked='0'";
				$x == 'm' && $y = "grouptype$gtid='$mem_gt[$k]' AND grouptype{$gtid}date<'".($timestamp+30*24*3600)."' AND grouptype{$gtid}date>'".($timestamp+7*24*3600)."'";
				$x == 'w' && $y = "grouptype$gtid='$mem_gt[$k]' AND grouptype{$gtid}date<'".($timestamp+7*24*3600)."' AND grouptype{$gtid}date>'".($timestamp+3*24*3600)."'";
				$x == 'd3' && $y = "grouptype$gtid='$mem_gt[$k]' AND grouptype{$gtid}date<'".($timestamp+3*24*3600)."' AND grouptype{$gtid}date>'".($timestamp+24*3600)."'";
				$x == 'd1' && $y = "grouptype$gtid='$mem_gt[$k]' AND grouptype{$gtid}date<'".($tm_base+24*3600)."' AND grouptype{$gtid}date>'".($tm_base)."'";
			}
			//��Ϊ����ʱ��where����ֻ��ʱ����������������ǰ��������װ�����sql����˲��ؽ���substr($k,0,6) == 'commu'ȥ��װsql�Ĳ���           
           
			if($k === 'commu_dp' || $k === 'commu_zixun'){//¥�̵�������Ѷ����
			    $commudz=$tblprefix.'commu_zixun';
                $x == 'ck' && $y = " {$commudz}.checked = '1' AND {$commudz}.tocid=0  ";
				$x == 'nock' && $y = " {$commudz}.checked = '0' AND {$commudz}.tocid=0  ";
			    $x == 'm' && $y = "{$commudz}.createdate>'".($timestamp-30*24*3600)."'";
				$x == 'w' && $y = "{$commudz}.createdate>'".($timestamp-7*24*3600)."'";
				$x == 'd3' && $y = "{$commudz}.createdate>'".($timestamp-3*24*3600)."'";
				$x == 'd1' && $y = "{$commudz}.createdate>'".($tm_base)."'";
				$k == 'commu_dp' ? $v = "commu_zixun INNER JOIN {$tblprefix}archives15 a ON a.aid={$tblprefix}commu_zixun.aid ":$v = "commu_zixun INNER JOIN {$tblprefix}archives21 a ON a.aid={$tblprefix}commu_zixun.aid ";
            }
			$cmsinfos[$k][$x] = $db->result_one("SELECT COUNT(*) FROM {$tblprefix}$v WHERE $y");
		}
	}
#\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\	
	$cmsinfos['lic_str'] = cls_env::GetLicense();
	cls_CacheFile::Save($cmsinfos,'cmsinfos');
}
$archivestr = $memberstr = $commustr = $mem_gtstr = '';
foreach($tblarr as $k => $v){
	if(substr($k,0,7) == 'archive'){
		$var = 'archivestr';
	}elseif(substr($k,0,5) == 'commu'){
		$var = 'commustr';
	}else{
		$var = 'memberstr';
	}
	substr($k,0,6)!='mem_gt' && $$var .= '<tr><td class="bgc_E7F5FE fB">'.$tbllang[$k].'</td><td class="bgc_FFFFFF">'.@$cmsinfos[$k]['ck'].'</td><td class="bgc_FFFFFF">'.@$cmsinfos[$k]['nock'].'</td><td class="bgc_FFFFFF">'.@$cmsinfos[$k]['m'].'</td><td class="bgc_FFFFFF">'.@$cmsinfos[$k]['w'].'</td><td class="bgc_FFFFFF">'.@$cmsinfos[$k]['d3'].'</td><td class="bgc_FFFFFF">'.@$cmsinfos[$k]['d1'].'</td><td class="bgc_FFFFFF"><a href="'.$tblurl[$k].'">>></a></td></tr>';
}

$mem_gt = array(
'mem_gt14' => '8', 
/*'mem_gt31' => '102', 
// 'mem_gt32' => '104', */
);
foreach($mem_gt as $k=>$v){
	$mem_gtstr .= '<tr><td class="bgc_E7F5FE fB">'.$tbllang[$k].'</td><td class="bgc_FFFFFF">'.@$cmsinfos[$k]['m'].'</td><td class="bgc_FFFFFF">'.@$cmsinfos[$k]['w'].'</td><td class="bgc_FFFFFF">'.@$cmsinfos[$k]['d3'].'</td><td class="bgc_FFFFFF">'.@$cmsinfos[$k]['d1'].'</td><td class="bgc_FFFFFF"><a href="'.$tblurl[$k].'">>></a></td></tr>';
}


$LicenseMessage = empty($cmsinfos['lic_str']) ? 'δ��Ȩ�汾 &nbsp;<a href="http://www.08cms.com" target="_blank" class="cRed">>>�������</a>' : '��Ȩ�ţ�'. $cmsinfos['lic_str'].' &nbsp;<a href="http://www.08cms.com" target="_blank" class="cRed">>>��ʵ��Ȩ</a>';
$cmsinfos['server'] = PHP_OS.'/PHP '.PHP_VERSION;
$cmsinfos['safe_mode'] = @ini_get('safe_mode') ? 'ON' : 'OFF';
$cmsinfos['max_upload'] = @ini_get('upload_max_filesize') ? @ini_get('upload_max_filesize') : 'Disabled';
$cmsinfos['allow_url_fopen'] = (@ini_get('allow_url_fopen') && function_exists('fsockopen') && function_exists('gzinflate')) ? "YES" : "NO";
$cmsinfos['gdpic'] = (function_exists("imagealphablending") && function_exists("imagecreatefromjpeg") && function_exists("ImageJpeg")) ? 'YES' : 'NO';
$cmsinfos['servertime'] = date("Y-m-d  H:i");

if($curuser->info['isfounder'] == 1){
	$group = '��������Ա';
}else{
    $gid = $curuser->info['grouptype2'];
    $group = cls_cache::Read('usergroups', 2);
    $group = $gid && isset($group[$gid]) ? $group[$gid]['cname'] : 'δ֪';
}

function show_tip($key){
	if(@include(M_ROOT.'./dynamic/aguides/'.$key.'.php'))echo $aguide;
}
$registeropenstr = $registerclosed ? '�ѹر�': '����';
$mspaceopenstr = $mspacedisabled ? '�ѹر�': '����';

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?=$mcharset?>">
<style type="text/css">
/* resett.css
>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>*/
body {text-align:center;margin:0;padding:0;background:#FFF;font-size:12px;color:#000;}
div,dl,dt,dd,ul,ol,li,h1,h2,h3,h4,h5,h6,pre,code,form,fieldset,legend,input,button,textarea,p,blockquote,th,td{margin:0;padding:0;border:0;}
ul,li{list-style-type:none;}
img{vertical-align:top; border:0;}
strong{font-weight:normal;}
em{font-style:normal;}
h1,h2,h3,h4,h5,h6{margin:0;padding:0;font-size:12px;font-weight:normal;}
input, textarea, select{margin:2px 0px;border:1px solid #CCCCCC;font:12px Arial, Helvetica, sans-serif;line-height: 1.2em;color: #006699;background:#FFFFFF;}
textarea {overflow:auto;}
cite {float:right; font-style:normal;}

.area{margin:0 auto; width:98%; padding:4px; background:#fafafa;  clear:both;}

/* Link */
a:link {color: #333; text-decoration:none;}
a:hover {color: #134d9d; text-decoration:underline;}
a:active {color: #103d7c;}
a:visited {color: #333;text-decoration:none;}
/* Color */
.cRed,a.cRed:link,a.cRed:visited{ color:#f00; }
.cBlue,a.cBlue:link,a.cBlue:visited,a.cBlue:active{color:#1f3a87;}
.cDRed,a.cDRed:link,a.cDRed:visited{ color:#bc2931;}
.cGray,a.cGray:link,a.cGray:visited{ color: #4F544D;}
.csGray,a.csGray:link,a.csGray:visited{ color: #999;}
.cDGray,a.cDGray:link,a.cDGray:visited{ color: #666;}
.cWhite,a.cWhite:link,a.cWhite:visited{ color:#fff;}
.cBlack,a.cBlack:link,a.cBlack:visited{color:#000;}a.cBlack:hover{color:#bc2931;}
.cYellow,a.cYellow:link,a.cYellow:visited{color:#ff0;}
.cGreen,a.cGreen:link,a.cGreen:visited{color:#008000;}
/* Font  */
.fn{font-weight:normal;}
.fB{font-weight:bold;}
.f12px{font-size:12px;}
.f14px{font-size:14px;}
.f16px{font-size:16px;}
.f18px{font-size:18px;}
.f24px{font-size:24px;}
/* Other */
.left{ float: left;}
.right{ float: right;}
.clear{ clear: both; font-size:1px; width:1px; height:0; visibility: hidden; }
.clearfix:after{content:"."; display:block; height: 0; clear: both; visibility: hidden;} /* only FF */
.hidden {display: none;}
.unLine ,.unLine a{text-decoration: none;}
.noBorder{border:none;}
.txtleft{text-align:left;}
.txtright{text-align:right;}
.nobg { background:none;}
.txtindent12 {text-indent:12px;}
.txtindent24 {text-indent:24px;}
.lineheight24{line-height:24px;}
.lineheight20{line-height:20px;}
.lineheight16{line-height:16px;}
.lineheight200{line-height:200%;}
.blank1{ height:1px; clear:both;display:block; font-size:1px;overflow:hidden;}
.blank3{ height:3px; clear:both;display:block; font-size:1px;overflow:hidden;}
.blank9{ height:9px; font-size:1px;display:block; clear:both;overflow:hidden;}
.blank6{height:6px; font-size:1px; display:block;clear:both;overflow:hidden;}
.blankW6{ height:6px; display:block;background:#fff; clear:both;overflow:hidden;}
.blankW9{ height:9px; display:block;background:#fff; clear:both;overflow:hidden;}
.blank12{ height:12px; font-size:1px;clear:both;overflow:hidden;}
.blank18{ height:18px; font-size:1px;clear:both;overflow:hidden;}
.blank36{ height:36px; font-size:1px;clear:both;overflow:hidden;}
.bgc_E7F5FE { background:#E7F5FE;}
.bgc_FFFFFF { background:#FFFFFF;}
.bgc_71ACD2 { background:#71ACD2;}
.bgc_c6e9ff { background:#c6e9ff;}

/*border*/
.borderall {border:1px #134d9d solid;}
.borderall2 {border:1px #CCCCCC solid; border-right:1px #666 solid; border-bottom:1px #666 solid; }
.borderleft {border-left:1px #CCC solid;}
.borderright {border-right:1px #CCC solid;}
.bordertop {border-top:1px #CCC solid;}
.borderbottom {border-bottom:1px #005584 solid;}
.borderno {border:none;}
.borderbottom_no {border-bottom:none;}

.nav1 { height:50px; line-height:50px;}
.nav2 { padding:9px; background:#FFF;}
.table_frame { clear:both; padding:0 9px;}
.w48 { width:48%;}
.m18{margin-top:18px;}
</style>
<script type="text/javascript"> var originDomain=originDomain || document.domain; document.domain = '<?php echo $cms_top;?>'||document.domain;</script>
</head>
<body>

<div class="area">
	<div class="blank9"></div>
    <div class="nav1">
        <font class=" left f24px">��ӭʹ��08CMS��������ϵͳ</font><font class="right"><a href="http://%77%77%77%2e%30%37%36%32%76%69%70%2e%63%6f%6d" class="cBlue" target="_blank">��������һ����������</a></font>	
    </div>
    <div class="nav2 borderall" style="background:#FFF;">
        <div class="blank12"></div>
        <h1 class=" lineheight200 txtindent12 txtleft fB f14px">��Դ��Ʒ����www.0762vip.com&#25552;&#20379;&#48;&#56;&#99;&#109;&#115;&#25151;&#20135;&#38376;&#25143;&#28304;&#30721;&#65292;&#25345;&#32493;&#26356;&#26032;&#21319;&#32423;&#20013;&#65281;</h1>
        <div class="blank6"></div>
        <ul class="txtindent12 lineheight200">
            <li><font class="left f14px w48"><a href="http://%77%77%77%2e%30%37%36%32%76%69%70%2e%63%6f%6d" class="cBlue" target="_blank">>>������������</a></font> <font class="right f14px w48"><a href="http://%77%77%77%2e%30%37%36%32%76%69%70%2e%63%6f%6d" class="cBlue" target="_blank">>><b>��</b><b>Դ��</b><b>Ʒ��</b><b>��</b></a></font> </li>
        </ul>
        <div class="blank6"></div>
    </div>
	<div class="blank18"></div>
    <div class="nav2 borderall">
        <div class="table_frame">
        <div class="left w48 lineheight200">
            <table width="100%" border="0" cellspacing="1" cellpadding="0" class="bgc_71ACD2">
				 <tr>
					<td colspan="8" class="bgc_c6e9ff fB txtleft txtindent12">�ĵ���Ϣͳ��</td>
				</tr>
				<tr>
					<td class="bgc_E7F5FE fB">ͳ��</td>
					<td class="bgc_E7F5FE fB">����</td>
					<td class="bgc_E7F5FE fB">δ��</td>
					<td class="bgc_E7F5FE fB">һ��</td>
					<td class="bgc_E7F5FE fB">һ��</td>
					<td class="bgc_E7F5FE fB">����</td>
					<td class="bgc_E7F5FE fB">����</td>
                    <td class="bgc_E7F5FE fB">����</td>
				</tr>
				<?=$archivestr?>
    		</table>
            <div class="blank18"></div>
            <table width="100%" border="0" cellspacing="1" cellpadding="0" class="bgc_71ACD2">
              <tr>
                <td colspan="8" class="bgc_c6e9ff fB txtleft txtindent12">��Ա��Ϣͳ��</td>
              </tr>
              <tr>
                <td class="bgc_E7F5FE fB">ͳ��</td>
                <td class="bgc_E7F5FE fB">����</td>
                <td class="bgc_E7F5FE fB">δ��</td>
                <td class="bgc_E7F5FE fB">һ��</td>
                <td class="bgc_E7F5FE fB">һ��</td>
                <td class="bgc_E7F5FE fB">����</td>
                <td class="bgc_E7F5FE fB">����</td>
                <td class="bgc_E7F5FE fB">����</td>
              </tr>
              <?=$memberstr?>
            </table>
        </div>
        <div class=" right w48 lineheight200">
            <table width="100%" border="0" cellspacing="1" cellpadding="0" class="bgc_71ACD2">
              <tr>
                <td colspan="8" class="bgc_c6e9ff fB txtleft txtindent12">������Ϣͳ��</td>
              </tr>
              <tr>
                <td class="bgc_E7F5FE fB">ͳ��</td>
                <td class="bgc_E7F5FE fB">����</td>
                <td class="bgc_E7F5FE fB">δ��</td>
                <td class="bgc_E7F5FE fB">һ��</td>
                <td class="bgc_E7F5FE fB">һ��</td>
                <td class="bgc_E7F5FE fB">����</td>
                <td class="bgc_E7F5FE fB">����</td>
                <td class="bgc_E7F5FE fB">����</td>
              </tr>
              <?=$commustr?>
            </table>
            <div class="blank18"></div>
            <table width="100%" border="0" cellspacing="1" cellpadding="0" class="bgc_71ACD2">
              <tr>
                <td colspan="8" class="bgc_c6e9ff fB txtleft txtindent12">��Ա����ͳ��</td>
              </tr>
              <tr>
                <td class="bgc_E7F5FE fB">����ͳ��</td>
                <td class="bgc_E7F5FE fB">һ��</td>
                <td class="bgc_E7F5FE fB">һ��</td>
                <td class="bgc_E7F5FE fB">����</td>
                <td class="bgc_E7F5FE fB">����</td>
                <td class="bgc_E7F5FE fB">����</td>
              </tr>
              <?=$mem_gtstr?>
            </table>
        </div>
        <div class="blank9"></div>
        </div>
    </div>
	<div class="blank18"></div>
    <div class="nav2 borderall">
        <div class="blank6"></div>
        <div class="table_frame txtleft">
            <ul class="left w48 lineheight200">
                <li>��Ĺ�����<?=$group?></li>
                <li>��Ա����ע�᣺<?=$registeropenstr?></li>
                <li>��Ա�ռ俪�ţ�<?=$mspaceopenstr?></li>
                <li>��ǰ������<?=$cmsinfos['servername']?></li>
                <li>��ǰ����IP��<?=$onlineip?></li>
                <li>����汾��Ϣ��08CMS V<?=$cms_version?></li>			
                <li>������IP��<?=$cmsinfos['serverip']?></li>
                <li>��������ǰʱ�䣺<?=$cmsinfos['servertime']?></li>			
                 <li>ϵͳ������£�<?=$last_patch?></li>			
           </ul>
            <ul class="right w48 lineheight200">
                <li>��������Ϣ��<?=$cmsinfos['server']?></li>
                <li>PHP��ȫģʽ��<?=$cmsinfos['safe_mode']?></li>
                <li>MySQL�汾��<?=$cmsinfos['dbversion']?></li>
                <li>�����Զ���ļ���<?=$cmsinfos['allow_url_fopen']?> (�迪��allow_url_fopen��չ;����fsockopen,gzinflate����)</li>
                <li>ͼ��GD��֧�֣�<?=$cmsinfos['gdpic']?></li>
                <li>����ϴ����ƣ�<?=$cmsinfos['max_upload']?></li>
                <li>��ǰ���ݿ��С��<?=$cmsinfos['dbsize']?></li>
                <li>��ǰ����������<?=$cmsinfos['attachsize']?></li>
                <li>�ʼ�֧��ģʽ��<?=$cmsinfos['sys_mail']?></li>
            </ul>
        <div class="blank3"></div>
        </div>
    </div>
	<div class="blank18"></div>
    <div class="nav2 borderall">
        <?=show_tip('08cms_group')?>
    </div>
	<br><br>
    <div class="footer"><hr size="0" noshade color="#86B9D6" width="100%">