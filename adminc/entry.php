<?php
defined('M_MCENTER') || exit('No Permission');

isset($action) || $action = '';
# ����CK���
if(in_array($action, array('chushouadd', 'chuzuadd')))
{
    if ( empty($ck_plugins_enable) )
    {
        $ck_ = new _08House_Archive();
        // ����CKҪ�����Ĳ����ע����ֵ��CK���������ͬ������ö��ŷָ�����������ýű�ʱ��̳���ȥ
        $ck_plugins_enable = "{$ck_->__ck_plot_pigure},{$ck_->__ck_size_chart}";
        unset($ck_);
    }
}

u_memberstat($memberid,30);

if(!empty($infloat)){ # �ڸ��������У���Ҫ����Ч����
    if(in_array(@$action, array('chushouadd', 'chuzuadd')))
    {
        cls_env::SetG('ck_plugins_enable',$ck_plugins_enable);
    }
	_header();
}else{ # ��ȫչʾ����������
	$_Title_Adminm = "��Ա�������� - $cmsname";
	if(!empty($curuser->info['atrusteeship'])) $_Title_Adminm .= " [�����ˣ�{$curuser->info['atrusteeship']['from_mname']}]";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html  xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?=$mcharset?>" />
<title><?=$_Title_Adminm?></title>
<meta content="IE=EmulateIE7" http-equiv="X-UA-Compatible"/>
<link type="text/css" rel="stylesheet" href="<?=$cms_abs?>images/common/validator.css" />
<link href="<?=MC_ROOTURL?>css/default.css" rel="stylesheet" type="text/css" />
<link href="<?=MC_ROOTURL?>css/pub_house.css" rel="stylesheet" type="text/css" />
<link type="text/css" rel="stylesheet" href="<?=$cmsurl?>images/common/window.css" />
</head>
<body>
<script language="javascript" type="text/javascript">
var CMS_ABS = "<?=$cms_abs?>" <?=empty($cmsurl) ? '' : ', CMS_URL = "'.$cmsurl.'"'?>, MC_ROOTURL = "<?=MC_ROOTURL?>"<?=empty($aallowfloatwin) ? ', eisable_floatwin = 1' : ''?><?=empty($aeisablepinyin) ? '' : ', eisable_pinyin = 1'?>, charset = '<?=$mcharset?>', tipm_ckkey = '<?=$ckpre?>mTips_List';
var originDomain = originDomain || document.domain; document.domain = '<?php echo $cms_top;?>' || document.domain;
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
<script type="text/javascript" src="<?=$cmsurl?>include/js/_08cms.js"></script>
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
<script type="text/javascript" src="<?=$cmsurl?>include/js/validator.js"></script>
<script type="text/javascript" src="<?=MC_ROOTURL?>js/Default.js"></script>
<?php
$usergroupstr = '';
$grouptypes = cls_cache::Read('grouptypes');
foreach($grouptypes as $k => $v){
	if($curuser->info['grouptype'.$k]){
		$usergroups = cls_cache::Read('usergroups',$k);
		$usergroupstr .=  '<span>(<em>'.$usergroups[$curuser->info['grouptype'.$k]]['cname'].'</em>)</span>';
	}
}
$mlogostyle = empty($mcenterlogo) ? '' : 'style="background:url('.(empty($ftp_enabled)?$cms_abs:$ftp_url).$mcenterlogo.') no-repeat;"';
?>
    <div class="header">
        <div class="header_con">
            <div class="logo" <?=$mlogostyle?>>
                <div><?=$hostname.$usergroupstr?></div>
            </div>
            <ul class="links black_a">
                <li><em>��ӭ����<span id="spanAgentName"><?=$curuser->info['mname']?></span></em>| </li>
                <li><a href="<?=$cms_abs?>login.php?action=logout">�˳�</a>|</li>
                <?php if(!empty($curuser->info['atrusteeship'])) { ?>
                <li><a href="<?=$cms_abs?>login.php?action=logout&target=atrusteeship">�˳�����</a>|</li>
                <?php } ?>                
                <? if(!in_array($curuser->info['mchid'],array(1,13))){?><li><a href="{c$diluurl [tclass=member/] [id=-1/]}{mspacehome}{/c$diluurl}" target="_blank">���̿ռ�</a>|</li>
				<? }?>
                <li><a href="<?=$cms_abs?>"><i class="ico_home">&nbsp;</i>������ҳ</a>|</li>
                <li><a href="{c$help}" target="_blank"><i class="ico_help">&nbsp;</i>����</a></li>
                <!--li><a href="[c$fwcturl [tclass=cnode/] [listby=ca/] [casource=76/]}[indexurl}{/c$fwcturl]" target="_blank"><i class="ico_help">&nbsp;</i>����</a></li-->
            </ul>
			<ul class="usualurls">
<?php
$usualurls = cls_cache::Read('usualurls');
foreach($usualurls as $v){
	if($v['ismc'] && $v['available'] && $curuser->pmbypmid($v['pmid'])){
		if(($tmp=strpos($v['logo'],'#'))!==false) $v['logo']=substr($v['logo'],0,$tmp);
		echo "\n				<li><a href=\"$v[url]\"".($v['onclick'] ? " onclick=\"$v[onclick]\"" : '').($v['newwin'] ? ' target="_blank"' : '').'>'.($v['logo']?"<img src=\"$v[logo]\" width=\"28\" height=\"24\" align=\"absmiddle\" />":'')."<b>$v[title]</b></a></li>";
	}
}
?>
			</ul>

        </div>
    </div>
	<!--ҳ�涥�� end-->
	<!--������ begin-->
	<div class="main">
    	<div class="l_col black_a btn_a">
        	<ul class="cor_box" >
                <li class="cor tl"></li>
                <li class="cor tr"></li>
                <li class="con">
    				<ul class="cor_box close_box" id="menubox0">
                        <li class="box_head" onclick="javascript:redirect('<?=$cms_abs.'adminm.php'?>');"><i class="ico_home">&nbsp;</i><a href="<?=$cms_abs."adminm.php"?>"  onclick="SetClass();">�ҵ���ҳ</a></li>
                    </ul>
<?php
	$currarea = '';
	empty($m_cookie['ucmenu']) && $m_cookie['ucmenu'] = '';
	$i=$j=0;
	$mmnmenus = cls_cache::Read('mmnmenus');
	foreach($mmnmenus as $k => $v){
		$j++;
		$tmp=array();
		$ucmenu = 0;
		foreach($v['submenu'] as $key => $arr){
			if($curuser->pmbypmid(empty($arr['pmid']) ? 0 : $arr['pmid'])){
				$i++;
				$tmp[]="<li id=\"menu$i\" class=\"\" onclick=\"SetCookie($i)\"><a class=\"submenu".$key."\" href=\"$arr[url]\" target=\"".(empty($arr['newwin']) ? '_self' : '_blank') ."\">".$arr['title']."</a></li>";
			}
		}
		if(count($tmp)){?>
					<ul class="cor_box" id="menubox<?=$j?>">
                        <li class="box_t"></li>
                        <li class="box_head" onclick="changeBoxState(this)"><i class="ico_manages<?=$k?>"></i><a href="javascript:void(0)"
                            ><?=$v['title']?></a><b></b></li>
                        <li class="box_body">
                            <ul>
								<?=join($tmp,"\n				")?>
                            </ul>
                        </li>
                        <li class="box_b"></li>
                    </ul>

<?php		}
	}
?>

                </li>
                <li class="cor bl"></li>
                <li class="cor br"></li>
          </ul>
        </div>
        <!--���������-->
        <div class="r_col<?=empty($action) ? '' : ' borGray'?>">
        <input id="hideMenuID" type="hidden" value="1" />
		<!--ҳ���Ҳ� begin-->

			<!--��ǰλ�� end-->
            

<?php
}

# �������
_08_Plugins_Base::getInstance()->trigger('member.' . $action);
echo cls_AdminmPage::Create(array('DynamicReturn' => true));

?>
		<div class="clear"></div>
		</div>
	<!--������ end-->
<?php
mcfooter();
?>