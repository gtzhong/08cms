<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?=$mcharset?>" />
<title>�ֻ��ͻ���-<?=$hostname?></title>
<!-- 360�ü���ģʽ -->
<meta name="renderer" content="webkit">
<link rel="shortcut icon" type="image/ico" href="<? if($u=cls_Parse::Tag(array('ename'=>"image",'tclass'=>"image",'tname'=>"$user_favicon",))){cls_Parse::Active($u);?><?=$u['url']?><? cls_Parse::ActiveBack();} unset($u);?>"/>
<meta content="IE=7" http-equiv="X-UA-Compatible"/>
<script type="text/javascript">
	var CMS_ABS = '<?=$cms_abs?>'
	, hostname  = '<?=$hostname?>'
	, tplurl    = '<?=$tplurl?>'
    , vcodes    = '<?=$cms_regcode?>';
	var originDomain = originDomain || document.domain;
	document.domain = '<?=$cms_top?>' || document.domain;
</script>
<link rel="stylesheet" type="text/css" href="<?=$tplurl?>css/global.css?<?=$user_version?>"/>
<!-- ���� index | newhouse | money | business | oldhouse | rent | news | special | mobile | tools -->
<?php $pagetype = null; ?>
<?php $pagetype = 'mobile'; ?>
</head>
<body>
<!--�ֻ�Appͷ��-->
<div class="bgFB">
<!-- {tpl$head-}  -->
</div>
<link rel="stylesheet" type="text/css" href="<?=$tplurl?>css/phonebm.css?<?=$user_version?>" />
<div class="tophead">
  <div class="wrap">
    <div class="phnav r pR15 mT10"><a href="<?=cls_Parse::Tag(array('ename'=>"kfturl",'tclass'=>"freeurl",'fid'=>109,))?><??>" <? if($fid==109) { ?>class="act"<? } ?>>�ֻ��ͻ���</a> <a href="<?=cls_Parse::Tag(array('ename'=>"wapurl",'tclass'=>"freeurl",'fid'=>110,))?><??>" <? if($fid==110) { ?>class="act"<? } ?>>�ֻ���</a> </div>
    <div class="phlogo"> <a href="#" rel="nofollow"><img src="<? if($u=cls_Parse::Tag(array('ename'=>"image",'tclass'=>"image",'tname'=>"$user_phone",'maxwidth'=>310,'maxheight'=>60,))){cls_Parse::Active($u);?><?=$u['url']?><? cls_Parse::ActiveBack();} unset($u);?>" width="310" height="60"/></a> </div>
  </div>
</div>
<div class="overbg">
<div class="blank10"></div>
    <div class="wrap1 phdetail">
        <div class="ewmdown r">
            <? if($_kfianddw=cls_Parse::Tag(array('ename'=>"kfianddw",'tclass'=>"farchives",'casource'=>"fcatalog173",'limits'=>1,'validperiod'=>1,'orderstr'=>"a.vieworder DESC",))){foreach($_kfianddw as $v){ cls_Parse::Active($v);?><? if($x=cls_Parse::Tag(array('ename'=>"androidxz",'tclass'=>"file",'tname'=>"$v[andrdown]",))){cls_Parse::Active($x);?><a class="first" title="Android��ά������"><img src="<?=cls_tpl::QRcodeImage($x['url'])?>" width="110" height="110" /></a><? cls_Parse::ActiveBack();} unset($x);?><? cls_Parse::ActiveBack();} unset($_kfianddw,$v);?><? }else{  } ?>
            <a href="<?=$mobileurl?>" class="sed" target="_blank"></a>
            <? if($_kfiphdw=cls_Parse::Tag(array('ename'=>"kfiphdw",'tclass'=>"farchives",'casource'=>"fcatalog173",'limits'=>1,'validperiod'=>1,'orderstr'=>"a.vieworder DESC",))){foreach($_kfiphdw as $v){ cls_Parse::Active($v);?><? if($x=cls_Parse::Tag(array('ename'=>"iphonexz",'tclass'=>"file",'tname'=>"$v[iphdown]",))){cls_Parse::Active($x);?><a title="iphone��ά������"><img src="<?=cls_tpl::QRcodeImage(cls_url::view_url('info.php?fid=113'))?>" width="110" height="110" /></a><? cls_Parse::ActiveBack();} unset($x);?><? cls_Parse::ActiveBack();} unset($_kfiphdw,$v);?><? }else{  } ?>
        </div>
        <div class="blank10"></div>
        <div class="bntdown r">
        <a href="<? if($_kfianddw=cls_Parse::Tag(array('ename'=>"kfianddw",'tclass'=>"farchives",'casource'=>"fcatalog173",'limits'=>1,'validperiod'=>1,'orderstr'=>"a.vieworder DESC",))){foreach($_kfianddw as $v){ cls_Parse::Active($v);?><? if($x=cls_Parse::Tag(array('ename'=>"androidxz",'tclass'=>"file",'tname'=>"$v[andrdown]",))){cls_Parse::Active($x);?><?=$x['url']?><? cls_Parse::ActiveBack();} unset($x);?><? cls_Parse::ActiveBack();} unset($_kfianddw,$v);?><? }else{  } ?>" title="Android������" class="first"></a>
        <a href="<? if($_kfiphdw=cls_Parse::Tag(array('ename'=>"kfiphdw",'tclass'=>"farchives",'casource'=>"fcatalog173",'limits'=>1,'validperiod'=>1,'orderstr'=>"a.vieworder DESC",))){foreach($_kfiphdw as $v){ cls_Parse::Active($v);?><? if($x=cls_Parse::Tag(array('ename'=>"iphonexz",'tclass'=>"file",'tname'=>"$v[iphdown]",))){cls_Parse::Active($x);?><?=$x['url']?><? cls_Parse::ActiveBack();} unset($x);?><? cls_Parse::ActiveBack();} unset($_kfiphdw,$v);?><? }else{  } ?>" title="iphone������"></a>
        </div>
    </div>
    <div class="linebg2"></div>
</div>
<div class="bgD7 clearfix">
<div class="wrap1">
<div class="phmintro">
   <ul>
    <? if($_sjkhdtj=cls_Parse::Tag(array('ename'=>"sjkhdtj",'tclass'=>"farchives",'casource'=>"fcatalog173",'limits'=>1,'validperiod'=>1,'orderstr'=>"a.vieworder DESC",))){foreach($_sjkhdtj as $x){ cls_Parse::Active($x);?>
      <? if($_pictj=cls_Parse::Tag(array('ename'=>"pictj",'tclass'=>"images",'tname'=>"$x[images]",'limits'=>8,'thumb'=>1,))){foreach($_pictj as $v){ cls_Parse::Active($v);?>      
      <li>
      <img src="<?=$v['url']?>" alt="<?=$v['title']?>" title="<?=$v['title']?>" width="215" height="310"/>
      </li>
      <? cls_Parse::ActiveBack();} unset($_pictj,$v);?><? }else{  } ?>
    <? cls_Parse::ActiveBack();} unset($_sjkhdtj,$x);?><? }else{  } ?>
   </ul>
</div>
<div class="blank15"></div>
</div>
</div>
<div class="blank1"></div>
<div id="login-wrap" class="w420 modal" data-head="��Ա��¼">
    <form class="jqValidate" data-tipmode="#tipinfo" onsubmit="return _08Login(this)">
    	<div style="height:270px;" class="p0-10">
            <div class="wrap-pc" data-title="�˺ŵ�¼">
                <ul class="form form-lg form-control bdrs form-fz16">
                    <li>
                        <div class="blank20 mt-15">
                            <span id="tipinfo"></span>
                        </div>
                        <input type="text" placeholder="�û���" class="txt user" data-type="*3-15" size="25" id="username" name="username" value=""/>
                    </li>
                    <li><input type="password" placeholder="����" class="txt psd" size="25" id="password" name="password" value="" data-type="*"/></li>
                    <li class="reg-wrap" data-regcode="login">
                        <input type="text" placeholder="��֤��" name="regcode" class="txt regcode" placeholder="" value=""/>
                    </li>
                    <li>
                        <input type="submit" class="btnok btn" name="cmslogin"  value="������¼" />
                    </li>
                </ul>
            </div><!-- /��ͨ�˺ŵ�¼ -->
            <div class="wrap-wx dn tc por" data-title="΢�ŵ�¼" style="padding-top:20px">
                <h3 class="mb10">�ֻ�΢��ɨ�裬��ȫ��¼</h3>
                <img class="wx-img" width="150" height="150"> <br/>
                <span id="wx-tag-tip" class="fcg mr10">ʹ�ð���</span>
                <span id="wx-refresh"><i class="ico08 mr3 fco">&#xf025;</i>���¼���</span>
                <img id="wx-login-tip" class="wx-login-tip" src="<?=$tplurl?>images/weixin_logintip.png">
            </div><!-- /΢�ŵ�¼ -->
        </div>
        <div class="p0-10">
            ������¼: <a id="qqlogin" onclick="OtherWebSiteLogin('qq', 600, 470);" href="javascript:;" title="QQ�ʺŵ�¼"  class="ico08 fz22 fcb mr5" target="_self">&#xf1d6;</a><a id="sinalogin" onclick="OtherWebSiteLogin('sina', 600, 400);" href="javascript:;" title="����΢���ʺŵ�¼" class="ico08 fz22 fcr" target="_self">&#xf18a;</a><a class="mt5" href="<?=$cms_abs?>tools/lostpwd.php?" target="_blank"><i class="ico08 fco ml10">&#xf059;</i>��������</a>
        </div>
    </form>
    <i id="ico-login" class="ico-login" title="΢�ŵ�¼"></i>
</div><!-- /��¼�� -->

<div class="fixed-right jqFixed jqScrollspy" data-fixed="600" data-css='{"right": 20, "bottom": 50, "display":"none"}' data-offset="140">
    <? if(@$pagetype=='index') { ?>
    <a href="#thlp" target="_top" ><span>�ػ�¥��</span><i class="ico08">&#xe73c;</i></a><a href="#xptj" target="_top" ><span>�����Ƽ�</span><i class="ico08">&#xe617;</i></a><a href="#sydc" target="_top" ><span>��ҵ�ز�</span><i class="ico08">&#xf0fb;</i></a><a href="#escz" target="_top" ><span>���ֳ���</span><i class="ico08">&#xe637;</i></a><? } ?><a href="body" target="_top" class="go-top" ><span>���ض���</span><i class="ico08">&#xe68e;</i></a>
</div>

<script type="text/javascript">window.jQuery || document.write('<script src="<?=$tplurl?>js/jquery.js"><\/script>');</script>
<script type="text/javascript">$.fn.jqModal || document.write('<script src="<?=$tplurl?>js/jqmodal.js?<?=$user_version?>"><\/script>');</script>
<script type="text/javascript">$.fn.jqValidate || document.write('<script src="<?=$tplurl?>js/jqvalidate.js?<?=$user_version?>"><\/script>');</script>
<script type="text/javascript">$.fn.jqScrollspy || document.write('<script src="<?=$tplurl?>js/jqscrollspy.js?<?=$user_version?>"><\/script>');</script>
<script type="text/javascript">$.fn.jqFixed || document.write('<script src="<?=$tplurl?>js/jqfixed.js?<?=$user_version?>"><\/script>');</script>
<script type="text/javascript" src="<?=$tplurl?>js/common.js?<?=$user_version?>"></script>

<script type="text/javascript">
<?php
// ��ݵ�¼ģ�飬���������վ��¼���رյ���������ظ�ģ��
if( @in_array(0, array_map("intval", array($qq_closed, $sina_closed))) ) {
?>
		<?php if(empty($qq_closed)) {?>
		$('#qqlogin').css('display','inline-block');
		<?php
	}
		if(empty($sina_closed)) {?>
		$('#sinalogin').css('display','inline-block');
		<?php }
?>
		//��������¼
		var urls = {
			"qq": CMS_ABS+"tools/other_site_sdk/qqcom/oauth/qq_login.php",
			"qq_reauth": CMS_ABS+"tools/other_site_sdk/qqcom/oauth/qq_login.php",
			"sina": "close"
		};
		var childWindow;

		function OtherWebSiteLogin(type, width, height)
		{
			if (urls[type] == 'close')
			{
				alert('�õ�¼�����Ѿ��رգ�');
				return false;
			} else
			{
				childWindow = window.open(urls[type], type, "width=" + width + ",height=" + height + ",left=" + ((window.screen.availWidth - width) / 2) + ",top=" + ((window.screen.availHeight - height) / 2));
			}
		}
<?php
  } // ��ݵ�¼ģ�����
 ?>

</script>

    <div class="blank1"></div>
    <div class="footer">
        <div class="wrap">
            <div class="footer-nav clearfix">
                <dl>
                    <dt><?php echo cls_PushArea::Config('push_24','cname'); ?></dt>
                    <dd>
                        <? if($_pushs=cls_Parse::Tag(array('ename'=>"pushs",'tclass'=>"pushs",'paid'=>"push_24",'limits'=>10,'ttl'=>600,))){foreach($_pushs as $v){ cls_Parse::Active($v);?>
                        <a href="<?=$v['url']?>" title="<?=$v['subject']?>"><?=cls_Parse::Tag(array('ename'=>"subject",'tclass'=>"text",'tname'=>"$v[subject]",'trim'=>100,'color'=>"$v[color]",))?><??></a><? cls_Parse::ActiveBack();} unset($_pushs,$v);?><? }else{  } ?>
                    </dd>
                </dl>
                <dl>
                    <dt><?php echo cls_PushArea::Config('push_25','cname'); ?></dt>
                    <dd>
                        <? if($_pushs=cls_Parse::Tag(array('ename'=>"pushs",'tclass'=>"pushs",'paid'=>"push_25",'limits'=>10,'ttl'=>600,))){foreach($_pushs as $v){ cls_Parse::Active($v);?>
                        <a href="<?=$v['url']?>" title="<?=$v['subject']?>"><?=cls_Parse::Tag(array('ename'=>"subject",'tclass'=>"text",'tname'=>"$v[subject]",'trim'=>100,'color'=>"$v[color]",))?><??></a><? cls_Parse::ActiveBack();} unset($_pushs,$v);?><? }else{  } ?>
                    </dd>
                </dl>
                <dl>
                    <dt><?php echo cls_PushArea::Config('push_26','cname'); ?></dt>
                    <dd>
                        <? if($_pushs=cls_Parse::Tag(array('ename'=>"pushs",'tclass'=>"pushs",'paid'=>"push_26",'limits'=>10,'ttl'=>600,))){foreach($_pushs as $v){ cls_Parse::Active($v);?>
                        <a href="<?=$v['url']?>" title="<?=$v['subject']?>"><?=cls_Parse::Tag(array('ename'=>"subject",'tclass'=>"text",'tname'=>"$v[subject]",'trim'=>100,'color'=>"$v[color]",))?><??></a><? cls_Parse::ActiveBack();} unset($_pushs,$v);?><? }else{  } ?>
                    </dd>
                </dl>
                <dl>
                    <dt><?php echo cls_PushArea::Config('push_27','cname'); ?></dt>
                    <dd>
                        <? if($_pushs=cls_Parse::Tag(array('ename'=>"pushs",'tclass'=>"pushs",'paid'=>"push_27",'limits'=>10,'ttl'=>600,))){foreach($_pushs as $v){ cls_Parse::Active($v);?>
                        <a href="<?=$v['url']?>" title="<?=$v['subject']?>"><?=cls_Parse::Tag(array('ename'=>"subject",'tclass'=>"text",'tname'=>"$v[subject]",'trim'=>100,'color'=>"$v[color]",))?><??></a><? cls_Parse::ActiveBack();} unset($_pushs,$v);?><? }else{  } ?>
                    </dd>
                </dl>
                <dl>
                    <dt><?php echo cls_PushArea::Config('push_11','cname'); ?></dt>
                    <dd>
                        <? if($_pushs=cls_Parse::Tag(array('ename'=>"pushs",'tclass'=>"pushs",'paid'=>"push_11",'limits'=>10,'ttl'=>600,))){foreach($_pushs as $v){ cls_Parse::Active($v);?>
                        <a href="<?=$v['url']?>" title="<?=$v['subject']?>"><?=cls_Parse::Tag(array('ename'=>"subject",'tclass'=>"text",'tname'=>"$v[subject]",'trim'=>100,'color'=>"$v[color]",))?><??></a><? cls_Parse::ActiveBack();} unset($_pushs,$v);?><? }else{  } ?>
                    </dd>
                </dl>
                <dl class="ewm">
                    <dt>ɨ��ά��</dt>
                    <dd class="tc">
            			<? if($_kfianddw=cls_Parse::Tag(array('ename'=>"kfianddw",'tclass'=>"farchives",'casource'=>"fcatalog173",'limits'=>1,'validperiod'=>1,'orderstr'=>"a.vieworder DESC",))){foreach($_kfianddw as $v){ cls_Parse::Active($v);?><? if($x=cls_Parse::Tag(array('ename'=>"androidxz",'tclass'=>"file",'tname'=>"$v[andrdown]",))){cls_Parse::Active($x);?>
                        <div class="l mr30">
                        	<img src="<?=cls_tpl::QRcodeImage($x['url'])?>" width="100" height="100" /><br/>Android����
                        </div>
                        <? cls_Parse::ActiveBack();} unset($x);?><? cls_Parse::ActiveBack();} unset($_kfianddw,$v);?><? }else{  } ?>
                        <? if($_kfiphdw=cls_Parse::Tag(array('ename'=>"kfiphdw",'tclass'=>"farchives",'casource'=>"fcatalog173",'limits'=>1,'validperiod'=>1,'orderstr'=>"a.vieworder DESC",))){foreach($_kfiphdw as $v){ cls_Parse::Active($v);?><? if($x=cls_Parse::Tag(array('ename'=>"iphonexz",'tclass'=>"file",'tname'=>"$v[iphdown]",))){cls_Parse::Active($x);?>
						<div class="l mr30">
                        	<img src="<?=cls_tpl::QRcodeImage(cls_url::view_url('info.php?fid=113'))?>" width="100" height="100" /><br/>iphone����
                        </div>
                        <? cls_Parse::ActiveBack();} unset($x);?><? cls_Parse::ActiveBack();} unset($_kfiphdw,$v);?><? }else{  } ?>
                        <div class="l">
							<img alt="ɨһɨ,��ע΢��" <? if($u=cls_Parse::Tag(array('ename'=>"image",'tclass'=>"image",'tname'=>"$weixin_qrcode",'maxwidth'=>100,'maxheight'=>100,))){cls_Parse::Active($u);?> src="<?=$u['url']?>"  height="100" width="100" <? cls_Parse::ActiveBack();} unset($u);?>/><br/>
							ɨһɨ,��ע΢��
                        </div>
                    </dd>
                </dl>
            </div>
            <div class="webinfo">
                <a href="<?=cls_Parse::Tag(array('ename'=>"wzdt",'tclass'=>"freeurl",'fid'=>9,))?><??>" target="_blank">��վ��ͼ</a><? if($_wzdbcj=cls_Parse::Tag(array('ename'=>"wzdbcj",'tclass'=>"farchives",'limits'=>1000,'casource'=>26,'orderstr'=>"a.vieworder ASC",))){foreach($_wzdbcj as $v){ cls_Parse::Active($v);?>|<a href="<?=$v['arcurl']?>" target="_blank" rel="nofollow"><?=$v['subject']?></a><? cls_Parse::ActiveBack();} unset($_wzdbcj,$v);?><? }else{  } ?>|<a href="<?=cls_Parse::Tag(array('ename'=>"freeurl",'tclass'=>"freeurl",'fid'=>102,))?><??>" target="_blank" rel="nofollow">��վ����</a>|<a href="<? if($v=cls_Parse::Tag(array('ename'=>"bzzxurl",'tclass'=>"cnode",'listby'=>"ca",'casource'=>561,))){cls_Parse::Active($v);?><?=$v['indexurl']?><? cls_Parse::ActiveBack();} unset($v);?>" target="_blank">��������</a>|<a href="<?=cls_Parse::Tag(array('ename'=>"kfturl",'tclass'=>"freeurl",'fid'=>109,))?><??>" target="_blank">�ֻ���</a>
            </div>
            <div class="copyright">
		<center><div style=line-height:21px>��Դ�ṩ��<a href=http://www.souho.net target=_blank><font color=yellow>�ѻ���Ʒ����</font></a>
<br>&nbsp;
<a href=http://www.souho.net target=_blank>�ѻ���Ʒ����</a> | <a href=http://vip.souho.net target=_blank>��Ʒ��ҵԴ��</a> | <a href=http://idc.souho.net target=_blank>�ѻ���Ʒ�����ռ䡢����</a> | <a href=http://vip.souho.net/templates/Korea/ target=_blank>90G����������ҵģ��</a> | <a href=http://tool.souho.net/ target=_blank>վ��������</a>
<br>
���ྫƷ��ҵ��Դ������<a href=http://www.souho.net target=_blank>�ѻ���Ʒ����</a></font>
</div></center>

			</div>
        </div>
    </div><!-- /footer -->
</body>
</html>
