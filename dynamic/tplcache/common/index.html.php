<!DOCTYPE html PUBLIC "-//W3C//liD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/liD/xhtml1-transitional.lid">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=<?=$mcharset?>" />
    <title><?=$cmstitle?></title>
    <meta name="keywords" content="<?=$cmskeyword?>" />
    <meta name="description" content="<?=$cmsdescription?>" />
    <meta name="mobile-agent" content="format=html5; url=<?=$mobileurl?>">
    <meta name="mobile-agent" content="format=xhtml; url=<?=$mobileurl?>">
    <meta name="baidu-tc-verification" content="75e113393e7b65ff7d43595dcba9feaf" />
    <!-- 360用极速模式 -->
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
<!-- 类型 index | newhouse | money | business | oldhouse | rent | news | special | mobile | tools -->
<?php $pagetype = null; ?>
    <base target="_blank"/>
    <? if(@$enable_mobile&&empty($frommob)) { ?>
   <script type="text/javascript">
 //判断设备是否为手机
(function(){var g=["iPhone","Android","Windows Phone"],b="<?=$mobileurl?>",e=window.location.pathname,f=false;for(var d=0,a;a=g[d];d++){if(navigator.userAgent.indexOf(a)!=-1){f=true;break}}function h(){location.href=b}if(f){h()}})()
</script>
    <? } ?>
    <link rel="stylesheet" type="text/css" href="<?=$tplurl?>css/index.css?<?=$user_version?>"/>
    <?php $pagetype = 'index'; ?>
</head>
<body>
    <? if($_farchives=cls_Parse::Tag(array('ename'=>"farchives",'tclass'=>"farchives",'limits'=>10,'validperiod'=>1,'casource'=>"fcatalog119",'orderstr'=>"a.vieworder ASC",))){?>
    <div class="wrap">
        <? foreach($_farchives as $v){ cls_Parse::Active($v);?>
        <div class="ad mb3">
            <? if($v['html']) { ?><?=$v['html']?><? } elseif($v['image']) { ?><a href="<?=$v['link']?>" target="_blank"><img <? if($u=cls_Parse::Tag(array('ename'=>"image",'tclass'=>"image",'tname'=>"$v[image]",'maxwidth'=>1200,'maxheight'=>60,))){cls_Parse::Active($u);?> src="<?=$u['url']?>"  height="60" width="1200"
        <? cls_Parse::ActiveBack();} unset($u);?>/></a><? } elseif($v['flash']) { ?><? if($f=cls_Parse::Tag(array('ename'=>"flash",'tclass'=>"flash",'tname'=>"$v[flash]",'width'=>1200,'height'=>60,))){cls_Parse::Active($f);?><?=$f['playbox']?><? if($v['link']) { ?><a href="<?=$v['link']?>" style="position: relative;margin-top:-<?=$f['height']?>px;width:<?=$f['width']?>px;height:<?=$f['height']?>px;display:block" target="_blank"><img width="<?=$f['width']?>" height="<?=$f['height']?>" src="<?=$cms_abs?>userfiles/notdel/blank.gif"/></a><? } ?><? cls_Parse::ActiveBack();} unset($f);?><? } ?>
        </div>
        <? cls_Parse::ActiveBack();} unset($_farchives,$v);?>
    </div>
    <? }else{  } ?>
    <div class="<? if(empty($navNoFixed)) { ?>fixed<? } ?> clearfix" data-fixed="<? if(@$pagetype == 'index') { ?>700<? } else { ?>95<? } ?>">
    <div class="wrap logo-sea-man">
        <div class="blank15"></div>
        <div class="w270 l">
            <h2 class="logo l"><a title="<?=$hostname?>" href="<?=$cms_abs?>"><img width="175" height="63" src="<? if($u=cls_Parse::Tag(array('ename'=>"image",'tclass'=>"image",'tname'=>"$cmslogo",'padding'=>1,))){cls_Parse::Active($u);?><?=$u['url']?><? cls_Parse::ActiveBack();} unset($u);?>"/></a></h2>
            <div class="substate" onmouseover="this.className='substate substate-hover'" onmouseout="this.className='substate'">
    <span class="tit-tag ptb8">
        <span class="tit">主站<i class="ico08 ml5">&#xe68d;</i></span>
        [切换分站]
    </span>
    <? if($_catalogs=cls_Parse::Tag(array('ename'=>"catalogs",'tclass'=>"catalogs",'limits'=>50,'listby'=>"co1",'orderstr'=>"vieworder ASC",'ttl'=>900,))){?>
   	<? $k=count($_catalogs)?>
    <div class="d-list <? if($k>10) { ?>d-list-row<? } ?>">
        <i class="ico08 arrow">&#xf0dc;</i>
        <? foreach($_catalogs as $v){ cls_Parse::Active($v);?><a href="<?=$v['indexurl']?>" target="_blank"><?=$v['title']?></a><? cls_Parse::ActiveBack();} unset($_catalogs,$v);?>
    </div>
    <? }else{  } ?>
</div>
        </div><!-- /logo-substate -->
        <div class="col-search l ptb8">
            <div class="s-detail l">
                <form action="index.php" target="_blank" class="clearfix">
                    <div id="s-cate" class="s-cate">
                        <i class="ico08 arrow">&#xe68d;</i>
                        <span class="s-tit">新房</span>
                        <div class="s-cate-list">
                            <ul>
                                <li class="<? if(empty($pagetype) || @$pagetype == 'index' || @$pagetype == 'newhouse') { ?>act<? } ?>" data-param='{"caid":"2","searchword":"请输入楼盘名称/地址/拼音","addno":"1"}'> 新房 </li>
                                <li class="<? if(@$pagetype == 'oldhouse') { ?>act<? } ?>" data-param='{"caid":"3","searchword":"请输入二手/小区","addno":""}'> 二手 </li>
                                <li class="<? if(@$pagetype == 'rent') { ?>act<? } ?>" data-param='{"caid":"4","searchword":"请输入出租/小区","addno":""}'> 出租 </li>
                                <li class="<? if(@$pagetype == 'news') { ?>act<? } ?>" data-param='{"caid":"1","searchword":"请输入资讯关键字","addno":""}'> 资讯 </li>
                            </ul>
                        </div>
                    </div>
                    <div class="s-txt"><input type="text" class="jqAutocomplete" name="searchword" x-webkit-speech speech value="<? if(@$searchword) { ?><?=$searchword?><? } ?>"/></div>
                    <input type="submit" class="s-btn ico08" value="&#xe607;"/>
                    <input type="hidden" name="caid" value=""/>
                    <input type="hidden" name="addno" value=""/>
                </form>
            </div>
            <div class="s-menu r">
                <a href="<?=cls_Parse::Tag(array('ename'=>"freeurl",'tclass'=>"freeurl",'fid'=>109,))?><??>" ><i class="ico08 mr5">&#xe74d;</i>手机找房</a><br/>
                <a href="<?=cls_Parse::Tag(array('ename'=>"freeurl",'tclass'=>"freeurl",'fid'=>1,))?><??>" ><i class="ico08 mr5">&#xe613;</i>地图找房</a>
            </div>
        </div><!-- /col-search -->
        <div class="w270 r">
            <div class="col-login ptb8">
                <span id="userLogin">
                    <a id="login-btn" data-head="会员登录" class="btn-jqModal log-btn" data-target="#login-wrap" href="<?=$cms_abs?>login.php" target="_self"><i class="ico08 mr5">&#xf007;</i>登陆</a>
                    <a class="log-btn" href="<?=$cms_abs?>register.php" ><i class="ico08 mr5">&#xf14b;</i>注册</a>
                </span>
                <a class="log-btn" href="<?=$cms_abs?>adminm.php?action=guanzus" ><i class="ico08 mr5">&#xf005;</i>收藏</a>
            </div>
        </div>
        <div class="blank15"></div>
        <i class="close dn ico08">&#xe774;</i>
    </div>
    <!-- nav -->
    <div id="nav" class="nav clearfix">
        <ul class="wrap clearfix">
            <li class="w270 nav-dt <? if(@$pagetype == 'index') { ?>keep<? } ?>">
                <? if(@$pagetype == 'oldhouse') { ?>
                <h3><i class="ico08 r">&#xf039;</i>二手房检索</h3>
                <dl class="condition">
                    <dd>
                        <div class="con-cate">
                            <h4><i class="ico08 r fcg">&#xe68f;</i>区域</h4>
                            <div class="con-cate-list">
                                <? if($_catalogs=cls_Parse::Tag(array('ename'=>"catalogs",'tclass'=>"catalogs",'limits'=>15,'listby'=>"co1",'cainherit'=>3,))){foreach($_catalogs as $v){ cls_Parse::Active($v);?><a href="<?=$v['indexurl']?>" ><?=$v['title']?></a><? cls_Parse::ActiveBack();} unset($_catalogs,$v);?><? }else{  } ?>
                            </div>
                        </div>
                        <div class="con-cate-more">
                            <h4>所有区域</h4>
                            <div class="con-cate-list">
                                <? if($_catalogs=cls_Parse::Tag(array('ename'=>"catalogs",'tclass'=>"catalogs",'limits'=>15,'listby'=>"co1",'cainherit'=>3,))){foreach($_catalogs as $v){ cls_Parse::Active($v);?><a href="<?=$v['indexurl']?>" ><?=$v['title']?></a><? cls_Parse::ActiveBack();} unset($_catalogs,$v);?><? }else{  } ?>
                            </div>
                        </div>
                    </dd>
                    <dd>
                        <div class="con-cate">
                            <h4><i class="ico08 r fcg">&#xe68f;</i>价格</h4>
                            <div class="con-cate-list">
                                <? if($_catalogs=cls_Parse::Tag(array('ename'=>"catalogs",'tclass'=>"catalogs",'listby'=>"co4",'cainherit'=>3,))){foreach($_catalogs as $v){ cls_Parse::Active($v);?><a href="<?=$v['indexurl']?>" ><?=$v['title']?></a><? cls_Parse::ActiveBack();} unset($_catalogs,$v);?><? }else{  } ?>
                            </div>
                        </div>
                        <div class="con-cate-more">
                            <h4>所有价格</h4>
                            <div class="con-cate-list">
                                <? if($_catalogs=cls_Parse::Tag(array('ename'=>"catalogs",'tclass'=>"catalogs",'listby'=>"co4",'cainherit'=>3,))){foreach($_catalogs as $v){ cls_Parse::Active($v);?><a href="<?=$v['indexurl']?>" ><?=$v['title']?></a><? cls_Parse::ActiveBack();} unset($_catalogs,$v);?><? }else{  } ?>
                            </div>
                        </div>
                    </dd>
                    <dd>
                        <div class="con-cate">
                            <h4><i class="ico08 r fcg">&#xe68f;</i>面积</h4>
                            <div class="con-cate-list">
                                <? if($_catalogs=cls_Parse::Tag(array('ename'=>"catalogs",'tclass'=>"catalogs",'limits'=>15,'listby'=>"co6",'cainherit'=>3,))){foreach($_catalogs as $v){ cls_Parse::Active($v);?><a href="<?=$v['indexurl']?>" ><?=$v['title']?></a><? cls_Parse::ActiveBack();} unset($_catalogs,$v);?><? }else{  } ?>
                            </div>
                        </div>
                        <div class="con-cate-more">
                            <h4>所有面积</h4>
                            <div class="con-cate-list">
                                <? if($_catalogs=cls_Parse::Tag(array('ename'=>"catalogs",'tclass'=>"catalogs",'limits'=>15,'listby'=>"co6",'cainherit'=>3,))){foreach($_catalogs as $v){ cls_Parse::Active($v);?><a href="<?=$v['indexurl']?>" ><?=$v['title']?></a><? cls_Parse::ActiveBack();} unset($_catalogs,$v);?><? }else{  } ?>
                            </div>
                        </div>
                    </dd>
                    <dd class="last">
                        <div class="con-cate">
                            <h4><i class="ico08 r fcg">&#xe68f;</i>房龄</h4>
                            <div class="con-cate-list">
                                <? if($_catalogs=cls_Parse::Tag(array('ename'=>"catalogs",'tclass'=>"catalogs",'limits'=>15,'listby'=>"co34",))){foreach($_catalogs as $v){ cls_Parse::Active($v);?><a href="<?=$cms_abs?>index.php?caid=3&addno=1&ccid34=<?=$v['ccid']?>" ><?=$v['title']?></a><? cls_Parse::ActiveBack();} unset($_catalogs,$v);?><? }else{  } ?>
                            </div>
                        </div>
                        <div class="con-cate-more">
                            <h4>所有房龄</h4>
                            <div class="con-cate-list">
                                <? if($_catalogs=cls_Parse::Tag(array('ename'=>"catalogs",'tclass'=>"catalogs",'limits'=>15,'listby'=>"co34",))){foreach($_catalogs as $v){ cls_Parse::Active($v);?><a href="<?=$cms_abs?>index.php?caid=3&ccid34=<?=$v['ccid']?>" ><?=$v['title']?></a><? cls_Parse::ActiveBack();} unset($_catalogs,$v);?><? }else{  } ?>
                            </div>
                        </div>
                    </dd>
                </dl>
                <? } elseif(@$pagetype == 'rent') { ?>
                <h3><i class="ico08 r">&#xf039;</i>出租检索</h3>
                <dl class="condition">
                    <dd>
                        <div class="con-cate">
                            <h4><i class="ico08 r fcg">&#xe68f;</i>区域</h4>
                            <div class="con-cate-list">
                                <? if($_catalogs=cls_Parse::Tag(array('ename'=>"catalogs",'tclass'=>"catalogs",'limits'=>15,'listby'=>"co1",'cainherit'=>4,))){foreach($_catalogs as $v){ cls_Parse::Active($v);?><a href="<?=$v['indexurl']?>" ><?=$v['title']?></a><? cls_Parse::ActiveBack();} unset($_catalogs,$v);?><? }else{  } ?>
                            </div>
                        </div>
                        <div class="con-cate-more">
                            <h4>所有区域</h4>
                            <div class="con-cate-list">
                                <? if($_catalogs=cls_Parse::Tag(array('ename'=>"catalogs",'tclass'=>"catalogs",'limits'=>15,'listby'=>"co1",'cainherit'=>4,))){foreach($_catalogs as $v){ cls_Parse::Active($v);?><a href="<?=$v['indexurl']?>" ><?=$v['title']?></a><? cls_Parse::ActiveBack();} unset($_catalogs,$v);?><? }else{  } ?>
                            </div>
                        </div>
                    </dd>
                    <dd>
                        <div class="con-cate">
                            <h4><i class="ico08 r fcg">&#xe68f;</i>租金</h4>
                            <div class="con-cate-list">
                                <? if($_catalogs=cls_Parse::Tag(array('ename'=>"catalogs",'tclass'=>"catalogs",'listby'=>"co5",'cainherit'=>4,))){foreach($_catalogs as $v){ cls_Parse::Active($v);?><a href="<?=$v['indexurl']?>" ><?=$v['title']?></a><? cls_Parse::ActiveBack();} unset($_catalogs,$v);?><? }else{  } ?>
                            </div>
                        </div>
                        <div class="con-cate-more">
                            <h4>所有租金</h4>
                            <div class="con-cate-list">
                                <? if($_catalogs=cls_Parse::Tag(array('ename'=>"catalogs",'tclass'=>"catalogs",'listby'=>"co5",'cainherit'=>4,))){foreach($_catalogs as $v){ cls_Parse::Active($v);?><a href="<?=$v['indexurl']?>" ><?=$v['title']?></a><? cls_Parse::ActiveBack();} unset($_catalogs,$v);?><? }else{  } ?>
                            </div>
                        </div>
                    </dd>
                    <dd>
                        <div class="con-cate">
                            <h4><i class="ico08 r fcg">&#xe68f;</i>户型</h4>
                            <div class="con-cate-list">
                               <?php
                                    foreach (u_field_by(2, 'shi') as $key => $value) {
                                        echo '<a href="'.$cms_abs.'index.php?caid=4&shi='.$key.'" >'.$value.'</a>';
                                    }
                                ?>
                            </div>
                        </div>
                        <div class="con-cate-more">
                            <h4>所有户型</h4>
                            <div class="con-cate-list">
                               <?php
                                    foreach (u_field_by(2, 'shi') as $key => $value) {
                                        echo '<a href="'.$cms_abs.'index.php?caid=4&shi='.$key.'" >'.$value.'</a>';
                                    }
                                ?>
                            </div>
                        </div>
                    </dd>
                    <dd class="last">
                        <div class="con-cate">
                            <h4><i class="ico08 r fcg">&#xe68f;</i>方式 / 装修</h4>
                            <div class="con-cate-list">
                               <?php
                                    foreach (u_field_by(2, 'zlfs') as $key => $value) {
                                        echo '<a href="'.$cms_abs.'index.php?caid=4&zlfs='.$key.'" >'.$value.'</a>';
                                    }
                                ?>
                               <?php
                                    foreach (u_field_by(2, 'zxcd') as $key => $value) {
                                        echo '<a href="'.$cms_abs.'index.php?caid=4&zxcd='.$key.'" >'.$value.'</a>';
                                    }
                                ?>
                            </div>
                        </div>
                        <div class="con-cate-more">
                            <h4>所有方式</h4>
                            <div class="con-cate-list mb20">
                               <?php
                                    foreach (u_field_by(2, 'zlfs') as $key => $value) {
                                        echo '<a href="'.$cms_abs.'index.php?caid=4&zlfs='.$key.'" >'.$value.'</a>';
                                    }
                                ?>
                            </div>
                            <h4>所有装修</h4>
                            <div class="con-cate-list">
                               <?php
                                    foreach (u_field_by(2, 'zxcd') as $key => $value) {
                                        echo '<a href="'.$cms_abs.'index.php?caid=4&zxcd='.$key.'" >'.$value.'</a>';
                                    }
                                ?>
                            </div>
                        </div>
                    </dd>
                </dl>
                <? } else { ?>
                <h3><i class="ico08 r">&#xf039;</i>楼盘检索</h3>
                <dl class="condition">
                    <dd>
                        <div class="con-cate">
                            <h4><i class="ico08 r fcg">&#xe68f;</i>区域</h4>
                            <div class="con-cate-list">
                                <? if($_catalogs=cls_Parse::Tag(array('ename'=>"catalogs",'tclass'=>"catalogs",'limits'=>30,'listby'=>"co1",'cainherit'=>2,))){foreach($_catalogs as $v){ cls_Parse::Active($v);?><a href="<?=$v['indexurl1']?>" ><?=$v['title']?></a><? cls_Parse::ActiveBack();} unset($_catalogs,$v);?><? }else{  } ?>
                            </div>
                        </div>
                        <div class="con-cate-more">
                            <h4>所有区域</h4>
                            <div class="con-cate-list">
                                <? if($_catalogs=cls_Parse::Tag(array('ename'=>"catalogs",'tclass'=>"catalogs",'limits'=>30,'listby'=>"co1",'cainherit'=>2,))){foreach($_catalogs as $v){ cls_Parse::Active($v);?><a href="<?=$v['indexurl1']?>" ><?=$v['title']?></a><? cls_Parse::ActiveBack();} unset($_catalogs,$v);?><? }else{  } ?>
                            </div>
                        </div>
                    </dd>
                    <dd>
                        <div class="con-cate">
                            <h4><i class="ico08 r fcg">&#xe68f;</i>价格</h4>
                            <div class="con-cate-list">
                                <? if($_catalogs=cls_Parse::Tag(array('ename'=>"catalogs",'tclass'=>"catalogs",'listby'=>"co17",'cainherit'=>2,))){foreach($_catalogs as $v){ cls_Parse::Active($v);?><a href="<?=$v['indexurl1']?>" ><?=$v['title']?></a><? cls_Parse::ActiveBack();} unset($_catalogs,$v);?><? }else{  } ?>
                            </div>
                        </div>
                        <div class="con-cate-more">
                            <h4>所有价格</h4>
                            <div class="con-cate-list">
                                <? if($_catalogs=cls_Parse::Tag(array('ename'=>"catalogs",'tclass'=>"catalogs",'listby'=>"co17",'cainherit'=>2,))){foreach($_catalogs as $v){ cls_Parse::Active($v);?><a href="<?=$v['indexurl1']?>" ><?=$v['title']?></a><? cls_Parse::ActiveBack();} unset($_catalogs,$v);?><? }else{  } ?>
                            </div>
                        </div>
                    </dd>
                    <dd>
                        <div class="con-cate">
                            <h4><i class="ico08 r fcg">&#xe68f;</i>类别</h4>
                            <div class="con-cate-list">
                                <? if($_catalogs=cls_Parse::Tag(array('ename'=>"catalogs",'tclass'=>"catalogs",'listby'=>"co12",'cainherit'=>2,))){foreach($_catalogs as $v){ cls_Parse::Active($v);?><a href="<?=$v['indexurl1']?>" ><?=$v['title']?></a><? cls_Parse::ActiveBack();} unset($_catalogs,$v);?><? }else{  } ?>
                            </div>
                        </div>
                        <div class="con-cate-more">
                            <h4>所有类别</h4>
                            <div class="con-cate-list">
                                <? if($_catalogs=cls_Parse::Tag(array('ename'=>"catalogs",'tclass'=>"catalogs",'listby'=>"co12",'cainherit'=>2,))){foreach($_catalogs as $v){ cls_Parse::Active($v);?><a href="<?=$v['indexurl1']?>" ><?=$v['title']?></a><? cls_Parse::ActiveBack();} unset($_catalogs,$v);?><? }else{  } ?>
                            </div>
                        </div>
                    </dd>
                    <dd>
                        <div class="con-cate">
                            <h4><i class="ico08 r fcg">&#xe68f;</i>特色</h4>
                            <div class="con-cate-list">
								<?php
									foreach (u_field_by(4, 'tslp') as $key => $value) {
										echo '<a href="'.$cms_abs.'index.php?caid=2&addno=1&tslp='.$key.'" >'.$value.'</a>';
									}
								?>
                            </div>
                        </div>
                        <div class="con-cate-more">
                            <h4>所有特色</h4>
                            <div class="con-cate-list">
								<?php
									foreach (u_field_by(4, 'tslp') as $key => $value) {
										echo '<a href="'.$cms_abs.'index.php?caid=2&addno=1&tslp='.$key.'" >'.$value.'</a>';
									}
								?>
                            </div>
                        </div>
                    </dd>
                    <dd class="last">
                        <div class="con-cate">
                            <h4><i class="ico08 r fcg">&#xe68f;</i>状态 / 楼层</h4>
                            <div class="con-cate-list">
                                <? if($_catalogs=cls_Parse::Tag(array('ename'=>"catalogs",'tclass'=>"catalogs",'limits'=>4,'listby'=>"co18",'cainherit'=>2,))){foreach($_catalogs as $v){ cls_Parse::Active($v);?><a href="<?=$v['indexurl1']?>" ><?=$v['title']?></a><? cls_Parse::ActiveBack();} unset($_catalogs,$v);?><? }else{  } ?>
								<?php
									foreach (u_field_by(4, 'lcs') as $key => $value) {
										echo '<a href="'.$cms_abs.'index.php?caid=2&addno=1&lcs='.$key.'" >'.$value.'</a>';
									}
								?>
                            </div>
                        </div>
                        <div class="con-cate-more">
                            <h4>所有状态</h4>
                            <div class="con-cate-list mb20">
                                <? if($_catalogs=cls_Parse::Tag(array('ename'=>"catalogs",'tclass'=>"catalogs",'limits'=>4,'listby'=>"co18",'cainherit'=>2,))){foreach($_catalogs as $v){ cls_Parse::Active($v);?><a href="<?=$v['indexurl1']?>" ><?=$v['title']?></a><? cls_Parse::ActiveBack();} unset($_catalogs,$v);?><? }else{  } ?>
                            </div>
                            <h4>所有楼层</h4>
                            <div class="con-cate-list">
								<?php
									foreach (u_field_by(4, 'lcs') as $key => $value) {
										echo '<a href="'.$cms_abs.'index.php?caid=2&addno=1&lcs='.$key.'" >'.$value.'</a>';
									}
								?>
                            </div>
                        </div>
                    </dd>
                </dl>
                <? } ?>
            </li><!-- /楼盘检索 -->
            <li class="<? if(@$pagetype == 'index') { ?>act<? } ?>">
                <a class="tit" href="<?=$cms_abs?>">首页</a>
                <i class="ico08 arrow">&#xf0dc;</i>
            </li>
            <li class="<? if(@$pagetype == 'newhouse') { ?>act<? } ?>">
                <a class="tit" href="<? if($a=cls_Parse::Tag(array('ename'=>"cnode",'tclass'=>"cnode",'listby'=>"ca",'casource'=>2,))){cls_Parse::Active($a);?><?=$a['indexurl']?><? cls_Parse::ActiveBack();} unset($a);?>">新房</a>
                <i class="ico08 arrow">&#xf0dc;</i>
                <span class="d-list">
                    <? if($_catalogs=cls_Parse::Tag(array('ename'=>"catalogs",'tclass'=>"catalogs",'listby'=>"ca",'casource'=>1,'caids'=>2,))){foreach($_catalogs as $v){ cls_Parse::Active($v);?><a href="<?=$v['indexurl1']?>">所有楼盘</a><? cls_Parse::ActiveBack();} unset($_catalogs,$v);?><? }else{  } ?><a href="<?=cls_Parse::Tag(array('ename'=>"dtzf",'tclass'=>"freeurl",'fid'=>1,))?><??>">楼盘地图</a><a href="<? if($a=cls_Parse::Tag(array('ename'=>"cnode",'tclass'=>"cnode",'listby'=>"ca",'casource'=>2,))){cls_Parse::Active($a);?><?=$a['indexurl3']?><? cls_Parse::ActiveBack();} unset($a);?>">房价</a><a href="<? if($v=cls_Parse::Tag(array('ename'=>"kfturl",'tclass'=>"cnode",'listby'=>"ca",'casource'=>560,))){cls_Parse::Active($v);?><?=$v['indexurl']?><? cls_Parse::ActiveBack();} unset($v);?>">看房团</a><? if($_catalogs=cls_Parse::Tag(array('ename'=>"catalogs",'tclass'=>"catalogs",'listby'=>"ca",'casource'=>1,'caids'=>"5,559",))){foreach($_catalogs as $v){ cls_Parse::Active($v);?><a href="<?=$v['indexurl']?>"><?=$v['title']?></a><? cls_Parse::ActiveBack();} unset($_catalogs,$v);?><? }else{  } ?><? if($_catalogs=cls_Parse::Tag(array('ename'=>"catalogs",'tclass'=>"catalogs",'listby'=>"ca",'casource'=>1,'caids'=>"36,11",))){foreach($_catalogs as $v){ cls_Parse::Active($v);?><a href="<?=$v['indexurl']?>"><?=$v['title']?></a><? cls_Parse::ActiveBack();} unset($_catalogs,$v);?><? }else{  } ?></span> 
                </li>
            <? if(!cmod('fenxiao')) { ?>
            <li class="<? if(@$pagetype == 'money') { ?>act<? } ?>">
                <a class="tit" href="<? if($a=cls_Parse::Tag(array('ename'=>"cnode",'tclass'=>"cnode",'listby'=>"ca",'casource'=>605,))){cls_Parse::Active($a);?><?=$a['indexurl']?><? cls_Parse::ActiveBack();} unset($a);?>">赚佣金</a>
                <i class="ico08 arrow">&#xf0dc;</i>
                <span class="d-list">
                    <a href="<?=cls_Parse::Tag(array('ename'=>"freeurl",'tclass'=>"freeurl",'fid'=>114,))?><??>">赚佣金</a><a href="<?=$cms_abs?>adminm.php?action=fxmy_fxtuijian">我的分销</a>
                </span>
            </li>
            <? } ?>
            <li class="<? if(@$pagetype == 'oldhouse') { ?>act<? } ?>">
                <a class="tit" href="<? if($a=cls_Parse::Tag(array('ename'=>"cnode",'tclass'=>"cnode",'listby'=>"ca",'casource'=>3,))){cls_Parse::Active($a);?><?=$a['indexurl']?><? cls_Parse::ActiveBack();} unset($a);?>">二手</a>
                <i class="ico08 arrow">&#xf0dc;</i>
                <span class="d-list">
                    <a href="<?=$cms_abs?>index.php?caid=3&mchid=1">个人房源</a><a href="<? if($v=cls_Parse::Tag(array('ename'=>"jjrjd",'tclass'=>"mcnode",'cnsource'=>"mcnid",'cnid'=>14,))){cls_Parse::Active($v);?><?=$v['mcnurl']?><? cls_Parse::ActiveBack();} unset($v);?>">经纪人</a><a href="<? if($v=cls_Parse::Tag(array('ename'=>"jjgsurl",'tclass'=>"mcnode",'cnsource'=>"mcnid",'cnid'=>51,))){cls_Parse::Active($v);?><?=$v['mcnurl']?><? cls_Parse::ActiveBack();} unset($v);?>">经纪公司</a><a href="<?=cls_Parse::Tag(array('ename'=>"dtzf",'tclass'=>"freeurl",'fid'=>117,))?><??>">二手地图</a><a href="<? if($v=cls_Parse::Tag(array('ename'=>"xqzdlj",'tclass'=>"cnode",'listby'=>"ca",'casource'=>2,))){cls_Parse::Active($v);?><?=$v['indexurl2']?><? cls_Parse::ActiveBack();} unset($v);?>">小区</a><? if($_catalogs=cls_Parse::Tag(array('ename'=>"catalogs",'tclass'=>"catalogs",'listby'=>"ca",'casource'=>1,'caids'=>10,))){foreach($_catalogs as $v){ cls_Parse::Active($v);?><a href="<?=$v['indexurl']?>"><?=$v['title']?></a><? cls_Parse::ActiveBack();} unset($_catalogs,$v);?><? }else{  } ?>
                </span>
            </li>
            <li class="<? if(@$pagetype == 'rent') { ?>act<? } ?>">
                <a class="tit" href="<? if($a=cls_Parse::Tag(array('ename'=>"cnode",'tclass'=>"cnode",'listby'=>"ca",'casource'=>4,))){cls_Parse::Active($a);?><?=$a['indexurl']?><? cls_Parse::ActiveBack();} unset($a);?>">租房</a>
                <i class="ico08 arrow">&#xf0dc;</i>
                <span class="d-list">
                    <a href="<?=$cms_abs?>index.php?caid=4&mchid=1">个人出租</a><a href="<?=cls_Parse::Tag(array('ename'=>"dtzf",'tclass'=>"freeurl",'fid'=>116,))?><??>">租房地图</a><? if($_catalogs=cls_Parse::Tag(array('ename'=>"catalogs",'tclass'=>"catalogs",'listby'=>"ca",'casource'=>1,'caids'=>9,))){foreach($_catalogs as $v){ cls_Parse::Active($v);?><a href="<?=$v['indexurl']?>"><?=$v['title']?></a><? cls_Parse::ActiveBack();} unset($_catalogs,$v);?><? }else{  } ?>
                </span>
            </li>
            <? if(!cmod('shangye')) { ?>
            <li class="<? if(@$pagetype == 'business') { ?>act<? } ?>"><a class="tit" href="<? if($v=cls_Parse::Tag(array('ename'=>"cnode",'tclass'=>"cnode",'listby'=>"ca",'casource'=>612,))){cls_Parse::Active($v);?><?=$v['indexurl']?><? cls_Parse::ActiveBack();} unset($v);?>">商业</a>
                <i class="ico08 arrow">&#xf0dc;</i>
                <? if($_catalogs=cls_Parse::Tag(array('ename'=>"catalogs",'tclass'=>"catalogs",'listby'=>"ca",'casource'=>1,'caids'=>"612,613,614,616,617,618",'ttl'=>3600,))){?>
                <span class="d-list">
                    <? foreach($_catalogs as $v){ cls_Parse::Active($v);?><a href="<?=$v['indexurl']?>"><?=$v['title']?></a><? cls_Parse::ActiveBack();} unset($_catalogs,$v);?>
                </span>
                <? }else{  } ?>
            </li>
            <? } ?>
            <li class="<? if(@$pagetype == 'special') { ?>act<? } ?>">
                <a class="tit" href="<? if($_catalogs=cls_Parse::Tag(array('ename'=>"catalogs",'tclass'=>"catalogs",'listby'=>"ca",'casource'=>1,'caids'=>37,))){foreach($_catalogs as $v){ cls_Parse::Active($v);?><?=$v['indexurl']?><? cls_Parse::ActiveBack();} unset($_catalogs,$v);?><? }else{  } ?>">专题</a>
                <i class="ico08 arrow">&#xf0dc;</i>
                <span class="d-list">
                    <? if($_catalogs=cls_Parse::Tag(array('ename'=>"catalogs",'tclass'=>"catalogs",'listby'=>"ca",'casource'=>3,'cainherit'=>2,'wherestr'=>"pid='37'",'ttl'=>300,))){foreach($_catalogs as $v){ cls_Parse::Active($v);?><a href="<?=$v['indexurl']?>"><?=$v['title']?></a><? cls_Parse::ActiveBack();} unset($_catalogs,$v);?><? }else{  } ?>
                </span>
            </li>
            <li class="<? if(@$pagetype == 'news') { ?>act<? } ?>">
                <a class="tit" href="<? if($a=cls_Parse::Tag(array('ename'=>"cnode",'tclass'=>"cnode",'listby'=>"ca",'casource'=>1,))){cls_Parse::Active($a);?><?=$a['indexurl']?><? cls_Parse::ActiveBack();} unset($a);?>">资讯</a>
                <i class="ico08 arrow">&#xf0dc;</i>
                <? if($_catalogs=cls_Parse::Tag(array('ename'=>"catalogs",'tclass'=>"catalogs",'listby'=>"ca",'casource'=>1,'caids'=>"502,21,20",))){?>
                <span class="d-list">
                    <? foreach($_catalogs as $v){ cls_Parse::Active($v);?><a href="<?=$v['indexurl']?>"><?=$v['title']?></a><? cls_Parse::ActiveBack();} unset($_catalogs,$v);?>
                </span>
                <? }else{  } ?>
            </li>
            <li class="<? if(@$pagetype == 'mobile') { ?>act<? } ?>"><a class="tit" href="<?=cls_Parse::Tag(array('ename'=>"freeurl",'tclass'=>"freeurl",'fid'=>109,))?><??>">客户端</a>
                <i class="ico08 arrow">&#xf0dc;</i></li>
            <? if(@$user_bbsurl) { ?><li><a class="tit" href="http://%77%77%77%2E%73%6F%75%68%6F%2E%63%63/">源码</a>
                <i class="ico08 arrow">&#xf0dc;</i></li><? } ?>
            <li class="<? if(@$pagetype == 'tools') { ?>act<? } ?>"><a class="tit" href="<?=cls_Parse::Tag(array('ename'=>"freeurl",'tclass'=>"freeurl",'fid'=>5,))?><??>">购房工具</a>
                <i class="ico08 arrow">&#xf0dc;</i></li>
            <li class="more">
                <a class="tit" href="###">更多</a>
                <i class="ico08 arrow">&#xf0dc;</i>
                <span class="d-list">
                    <? if($_catalogs=cls_Parse::Tag(array('ename'=>"catalogs",'tclass'=>"catalogs",'listby'=>"ca",'casource'=>1,'caids'=>"30,516,600,561",))){foreach($_catalogs as $v){ cls_Parse::Active($v);?><a href="<?=$v['indexurl']?>"><?=$v['title']?></a><? cls_Parse::ActiveBack();} unset($_catalogs,$v);?><? }else{  } ?>
                </span>
            </li>
        </ul>
    </div><!-- /nav -->
</div>
<div class="blank10"></div>
    <div class="wrap clearfix">
        <div class="w640 l ml280">
            <div class="flash jqDuang jqDuang-mod" data-obj="li" data-cell=".inner" data-prevbtn=".prev" data-nextbtn=".next">
                <div class="big">
                    <ul>
                        <? if($_pushs=cls_Parse::Tag(array('ename'=>"pushs",'tclass'=>"pushs",'paid'=>"push_syhdp",'limits'=>10,'ttl'=>600,))){foreach($_pushs as $v){ cls_Parse::Active($v);?>
                        <li><a href="<?=$v['url']?>" target="_blank"><img alt="<?=$v['subject']?>" width="640" height="340" src="<? if($u=cls_Parse::Tag(array('ename'=>"image",'tclass'=>"image",'tname'=>"$v[thumb]",'padding'=>1,))){cls_Parse::Active($u);?><?=$u['url']?><? cls_Parse::ActiveBack();} unset($u);?>"/></a></li>
                        <? cls_Parse::ActiveBack();} unset($_pushs,$v);?><? }else{  } ?>
                    </ul>
                </div>
                <div class="dot"><span class="inner"></span></div>
                <i class="ico08 prev">&#xe68c;</i>
                <i class="ico08 next">&#xe68f;</i>
            </div><!-- /flash -->
            <div class="blank13"></div>
            <div class="scroll jqDuang jqDuang-mod" data-effect="left" data-steps="3" data-visible="3" data-prevbtn=".prev" data-nextbtn=".next">
                <div class="big">
                    <ul>
                        <? if($_pushs=cls_Parse::Tag(array('ename'=>"pushs",'tclass'=>"pushs",'paid'=>"push_gdlp",'limits'=>9,'ttl'=>600,))){foreach($_pushs as $v){ cls_Parse::Active($v);?>
                        <li><a href="<?=$v['url']?>" target="_blank">
                            <img alt="<?=$v['subject']?>" width="100" height="70" src="<? if($u=cls_Parse::Tag(array('ename'=>"image",'tclass'=>"image",'tname'=>"$v[thumb]",'maxwidth'=>100,'maxheight'=>70,'thumb'=>1,'padding'=>1,))){cls_Parse::Active($u);?><?=$u['url_s']?><? cls_Parse::ActiveBack();} unset($u);?>"/>
                            <h4><?=$v['subject']?></h4>
                            <p><? if($a=cls_Parse::Tag(array('ename'=>"cnode",'tclass'=>"cnode",'listby'=>"co1",'cosource1'=>"$v[ccid1]",))){cls_Parse::Active($a);?><?=$a['title']?><? cls_Parse::ActiveBack();} unset($a);?><br/>
                                <span class="fcr"><? if($v['dj']) { ?><?=$v['dj']?>元/m&sup2;<? } else { ?>待定<? } ?></span>
                            </p>
                        </a></li>
                        <? cls_Parse::ActiveBack();} unset($_pushs,$v);?><? }else{  } ?>
                    </ul>
                </div>
                <i class="ico08 prev">&#xe68c;</i>
                <i class="ico08 next">&#xe68f;</i>
            </div>
        </div>
        <div class="w270 r">
            <div class="col-tools clearfix">
                <a href="<? if($a=cls_Parse::Tag(array('ename'=>"cnode",'tclass'=>"cnode",'listby'=>"ca",'casource'=>5,))){cls_Parse::Active($a);?><?=$a['indexurl']?><? cls_Parse::ActiveBack();} unset($a);?>"><i class="ico08">&#xe757;</i>团购</a><a href="<? if($a=cls_Parse::Tag(array('ename'=>"cnode",'tclass'=>"cnode",'listby'=>"ca",'casource'=>560,))){cls_Parse::Active($a);?><?=$a['indexurl']?><? cls_Parse::ActiveBack();} unset($a);?>"><i class="ico08">&#xe603;</i>看房团</a><a href="<? if($a=cls_Parse::Tag(array('ename'=>"cnode",'tclass'=>"cnode",'listby'=>"ca",'casource'=>555,))){cls_Parse::Active($a);?><?=$a['indexurl']?><? cls_Parse::ActiveBack();} unset($a);?>"><i class="ico08">&#xf06b;</i>优惠活动</a><a href="<? if($a=cls_Parse::Tag(array('ename'=>"cnode",'tclass'=>"cnode",'listby'=>"ca",'casource'=>2,))){cls_Parse::Active($a);?><?=$a['indexurl3']?><? cls_Parse::ActiveBack();} unset($a);?>"><i class="ico08">&#xf203;</i>查房价</a><a href="<?=cls_Parse::Tag(array('ename'=>"freeurl",'tclass'=>"freeurl",'fid'=>5,))?><??>"><i class="ico08">&#xf1ec;</i>购房工具</a><a href="<? if($a=cls_Parse::Tag(array('ename'=>"cnode",'tclass'=>"cnode",'listby'=>"ca",'casource'=>559,))){cls_Parse::Active($a);?><?=$a['indexurl']?><? cls_Parse::ActiveBack();} unset($a);?>"><i class="ico08">&#xe73c;</i>特价房</a>
            </div>
            <!-- ad270_145 -->
            <? if($_farchives=cls_Parse::Tag(array('ename'=>"farchives",'tclass'=>"farchives",'limits'=>2,'validperiod'=>1,'casource'=>"fcatalog175",'orderstr'=>"a.vieworder ASC",))){foreach($_farchives as $v){ cls_Parse::Active($v);?>
            <div class="ad mt10"><? if($v['html']) { ?><?=$v['html']?><? } elseif($v['image']) { ?><a href="<?=$v['link']?>" target="_blank"><img <? if($u=cls_Parse::Tag(array('ename'=>"image",'tclass'=>"image",'tname'=>"$v[image]",'maxwidth'=>270,'maxheight'=>140,))){cls_Parse::Active($u);?> src="<?=$u['url']?>"  height="140" width="270"
            <? cls_Parse::ActiveBack();} unset($u);?>/></a><? } elseif($v['flash']) { ?><? if($f=cls_Parse::Tag(array('ename'=>"flash",'tclass'=>"flash",'tname'=>"$v[flash]",'width'=>270,'height'=>140,))){cls_Parse::Active($f);?><?=$f['playbox']?><? if($v['link']) { ?><a href="<?=$v['link']?>" style="position: relative;margin-top:-<?=$f['height']?>px;width:<?=$f['width']?>px;height:<?=$f['height']?>px;display:block" target="_blank"><img width="<?=$f['width']?>" height="<?=$f['height']?>" src="<?=$cms_abs?>userfiles/notdel/blank.gif"/></a><? } ?><? cls_Parse::ActiveBack();} unset($f);?><? } ?></div>
            <? cls_Parse::ActiveBack();} unset($_farchives,$v);?><? }else{  } ?>
        </div>
        <div class="blank20"></div>
        <!-- ad1200_60 -->
        <? if($_farchives=cls_Parse::Tag(array('ename'=>"farchives",'tclass'=>"farchives",'limits'=>10,'validperiod'=>1,'casource'=>"fcatalog120",'orderstr'=>"a.vieworder ASC",))){foreach($_farchives as $v){ cls_Parse::Active($v);?>
        <div class="ad mb3">
            <? if($v['html']) { ?><?=$v['html']?><? } elseif($v['image']) { ?><a href="<?=$v['link']?>" target="_blank"><img <? if($u=cls_Parse::Tag(array('ename'=>"image",'tclass'=>"image",'tname'=>"$v[image]",'maxwidth'=>1200,'maxheight'=>60,))){cls_Parse::Active($u);?> src="<?=$u['url']?>"  height="60" width="1200"
        <? cls_Parse::ActiveBack();} unset($u);?>/></a><? } elseif($v['flash']) { ?><? if($f=cls_Parse::Tag(array('ename'=>"flash",'tclass'=>"flash",'tname'=>"$v[flash]",'width'=>1200,'height'=>60,))){cls_Parse::Active($f);?><?=$f['playbox']?><? if($v['link']) { ?><a href="<?=$v['link']?>" style="position: relative;margin-top:-<?=$f['height']?>px;width:<?=$f['width']?>px;height:<?=$f['height']?>px;display:block" target="_blank"><img width="<?=$f['width']?>" height="<?=$f['height']?>" src="<?=$cms_abs?>userfiles/notdel/blank.gif"/></a><? } ?><? cls_Parse::ActiveBack();} unset($f);?><? } ?>
        </div>
        <? cls_Parse::ActiveBack();} unset($_farchives,$v);?><? }else{  } ?>
        <div class="blank20"></div>
        <div class="tab jqDuang" data-cell=".tab-hd ul" data-obj=".plist" data-autoplay="0" data-speed="0">
            <div class="tab-hd">
                <ul class="clearfix">
                    <li class="act"><?php echo cls_PushArea::Config('push_4','cname'); ?></li>
                    <li><?php echo cls_PushArea::Config('push_thzq','cname'); ?></li>
                    <li><?php echo cls_PushArea::Config('push_mxlp','cname'); ?></li>
                    <li><?php echo cls_PushArea::Config('push_rxlp','cname'); ?></li>
                </ul>
            </div>
            <div class="tab-bd">
                <ul class="plist plist-mod clearfix">
                    <? if($_pushs=cls_Parse::Tag(array('ename'=>"pushs",'tclass'=>"pushs",'paid'=>"push_4",'limits'=>5,'ttl'=>600,))){foreach($_pushs as $v){ cls_Parse::Active($v);?>
                    <li>
                       <a class="img" href="<?=$v['url']?>" target="_blank"><img alt="<?=$v['subject']?>" width="220" height="190" src="<? if($u=cls_Parse::Tag(array('ename'=>"image",'tclass'=>"image",'tname'=>"$v[thumb]",'maxwidth'=>220,'maxheight'=>190,'thumb'=>1,'padding'=>1,))){cls_Parse::Active($u);?><?=$u['url_s']?><? cls_Parse::ActiveBack();} unset($u);?>"/></a>
                       <h4><a href="<?=$v['url']?>" target="_blank"><?=$v['subject']?></a></h4>
                       <p><i class="ico08 fco mr5">&#xe611;</i><?=$v['abstract']?></p>
                    </li>
                    <? cls_Parse::ActiveBack();} unset($_pushs,$v);?><? }else{  } ?>
                </ul><!-- /plist -->   
                <ul class="plist plist-mod clearfix dn">
                    <? if($_pushs=cls_Parse::Tag(array('ename'=>"pushs",'tclass'=>"pushs",'paid'=>"push_thzq",'limits'=>5,'ttl'=>600,))){foreach($_pushs as $v){ cls_Parse::Active($v);?>
                    <li>
                       <a class="img" href="<?=$v['url']?>" target="_blank"><img alt="<?=$v['subject']?>" width="220" height="190" src="<? if($u=cls_Parse::Tag(array('ename'=>"image",'tclass'=>"image",'tname'=>"$v[thumb]",'maxwidth'=>220,'maxheight'=>190,'thumb'=>1,'padding'=>1,))){cls_Parse::Active($u);?><?=$u['url_s']?><? cls_Parse::ActiveBack();} unset($u);?>"/></a>
                       <h4><a href="<?=$v['url']?>" target="_blank"><?=$v['subject']?></a></h4>
                       <p class="fcr"><i class="ico08 mr5">&#xe62f;</i><?=$v['bdsm']?></p>
                    </li>
                    <? cls_Parse::ActiveBack();} unset($_pushs,$v);?><? }else{  } ?>
                </ul><!-- /plist -->  
                <ul class="plist plist-mod clearfix dn">
                    <? if($_pushs=cls_Parse::Tag(array('ename'=>"pushs",'tclass'=>"pushs",'paid'=>"push_mxlp",'limits'=>5,'ttl'=>600,))){foreach($_pushs as $v){ cls_Parse::Active($v);?>
                    <li>
                       <a class="img" href="<?=$v['url']?>" target="_blank"><img alt="<?=$v['subject']?>" width="220" height="190" src="<? if($u=cls_Parse::Tag(array('ename'=>"image",'tclass'=>"image",'tname'=>"$v[thumb]",'maxwidth'=>220,'maxheight'=>190,'thumb'=>1,'padding'=>1,))){cls_Parse::Active($u);?><?=$u['url_s']?><? cls_Parse::ActiveBack();} unset($u);?>"/></a>
                       <h4><a href="<?=$v['url']?>" target="_blank"><?=$v['subject']?></a></h4>
                       <p class="fcr"><i class="ico08 mr5">&#xe62f;</i><?=$v['bdsm']?></p>
                    </li>
                    <? cls_Parse::ActiveBack();} unset($_pushs,$v);?><? }else{  } ?>
                </ul><!-- /plist -->  
                <ul class="plist plist-mod clearfix dn">
                    <? if($_pushs=cls_Parse::Tag(array('ename'=>"pushs",'tclass'=>"pushs",'paid'=>"push_rxlp",'limits'=>5,'ttl'=>600,))){foreach($_pushs as $v){ cls_Parse::Active($v);?>
                    <li>
                       <a class="img" href="<?=$v['url']?>" target="_blank"><img alt="<?=$v['subject']?>" width="220" height="190" src="<? if($u=cls_Parse::Tag(array('ename'=>"image",'tclass'=>"image",'tname'=>"$v[thumb]",'maxwidth'=>220,'maxheight'=>190,'thumb'=>1,'padding'=>1,))){cls_Parse::Active($u);?><?=$u['url_s']?><? cls_Parse::ActiveBack();} unset($u);?>"/></a>
                       <h4><a href="<?=$v['url']?>" target="_blank"><?=$v['subject']?></a></h4>
                       <p class="fcr"><i class="ico08 mr5">&#xe62f;</i><?=$v['bdsm']?></p>
                    </li>
                    <? cls_Parse::ActiveBack();} unset($_pushs,$v);?><? }else{  } ?>
                </ul><!-- /plist -->   
            </div>
        </div>
        <div class="blank10"></div>
        <? if($_farchives=cls_Parse::Tag(array('ename'=>"farchives",'tclass'=>"farchives",'limits'=>10,'validperiod'=>1,'casource'=>"fcatalog128",'orderstr'=>"a.vieworder ASC",))){foreach($_farchives as $v){ cls_Parse::Active($v);?>
        <div class="ad mb3">
            <? if($v['html']) { ?><?=$v['html']?><? } elseif($v['image']) { ?><a href="<?=$v['link']?>" target="_blank"><img <? if($u=cls_Parse::Tag(array('ename'=>"image",'tclass'=>"image",'tname'=>"$v[image]",'maxwidth'=>1200,'maxheight'=>60,))){cls_Parse::Active($u);?> src="<?=$u['url']?>"  height="60" width="1200"
        <? cls_Parse::ActiveBack();} unset($u);?>/></a><? } elseif($v['flash']) { ?><? if($f=cls_Parse::Tag(array('ename'=>"flash",'tclass'=>"flash",'tname'=>"$v[flash]",'width'=>1200,'height'=>60,))){cls_Parse::Active($f);?><?=$f['playbox']?><? if($v['link']) { ?><a href="<?=$v['link']?>" style="position: relative;margin-top:-<?=$f['height']?>px;width:<?=$f['width']?>px;height:<?=$f['height']?>px;display:block" target="_blank"><img width="<?=$f['width']?>" height="<?=$f['height']?>" src="<?=$cms_abs?>userfiles/notdel/blank.gif"/></a><? } ?><? cls_Parse::ActiveBack();} unset($f);?><? } ?>
        </div>
        <? cls_Parse::ActiveBack();} unset($_farchives,$v);?><? }else{  } ?><!-- /ad1200_60 -->
        <div class="blank20"></div>
        <div id="thlp" class="jqDuang clearfix" data-cell=".col-nav" data-obj=".plist1|#thlp .col-info" data-autoplay="0" data-speed="0">
            <div class="col-title clearfix">
                <h2 class="l">特惠楼盘</h2>
                <div class="col-nav col-nav-lg l">
                    <a class="act" href="<? if($a=cls_Parse::Tag(array('ename'=>"cnode",'tclass'=>"cnode",'listby'=>"ca",'casource'=>5,))){cls_Parse::Active($a);?><?=$a['indexurl']?><? cls_Parse::ActiveBack();} unset($a);?>"><span><?php echo cls_PushArea::Config('push_5','cname'); ?></span><i class="ico08">&#xf0dc;</i></a>
                    <a href="<? if($a=cls_Parse::Tag(array('ename'=>"cnode",'tclass'=>"cnode",'listby'=>"ca",'casource'=>560,))){cls_Parse::Active($a);?><?=$a['indexurl']?><? cls_Parse::ActiveBack();} unset($a);?>
"><span><?php echo cls_PushArea::Config('push_kfttj','cname'); ?></span><i class="ico08">&#xf0dc;</i></a>
                    <a href="<? if($a=cls_Parse::Tag(array('ename'=>"cnode",'tclass'=>"cnode",'listby'=>"ca",'casource'=>559,))){cls_Parse::Active($a);?><?=$a['indexurl']?><? cls_Parse::ActiveBack();} unset($a);?>"><span><?php echo cls_PushArea::Config('push_tjftj','cname'); ?></span><i class="ico08">&#xf0dc;</i></a>
                    <? if(!cmod('fenxiao')) { ?>
                    <a href="<? if($a=cls_Parse::Tag(array('ename'=>"cnode",'tclass'=>"cnode",'listby'=>"ca",'casource'=>605,))){cls_Parse::Active($a);?><?=$a['indexurl']?><? cls_Parse::ActiveBack();} unset($a);?>"><span><?php echo cls_PushArea::Config('push_zyj','cname'); ?></span><i class="ico08">&#xf0dc;</i></a>
                    <? } ?>
                </div>
                <div class="col-info r">
                    共有<span><? if($v=cls_Parse::Tag(array('ename'=>"counts",'tclass'=>"acount",'chids'=>5,'chsource'=>2,'casource'=>1,'caids'=>5,))){cls_Parse::Active($v);?><?=$v['counts']?><? cls_Parse::ActiveBack();} unset($v);?></span>个<?php echo cls_PushArea::Config('push_5','cname'); ?>
                </div>
                <div class="col-info r dn">
                    共有<span><? if($v=cls_Parse::Tag(array('ename'=>"counts",'tclass'=>"acount",'chids'=>110,'casource'=>1,'caids'=>560,'chsource'=>2,))){cls_Parse::Active($v);?><?=$v['counts']?><? cls_Parse::ActiveBack();} unset($v);?></span>个<?php echo cls_PushArea::Config('push_kfttj','cname'); ?>
                </div>
                <div class="col-info r dn">
                    共有<span><? if($v=cls_Parse::Tag(array('ename'=>"counts",'tclass'=>"acount",'chids'=>107,'chsource'=>2,'casource'=>1,'caids'=>559,))){cls_Parse::Active($v);?><?=$v['counts']?><? cls_Parse::ActiveBack();} unset($v);?></span>个<?php echo cls_PushArea::Config('push_tjftj','cname'); ?>
                </div>
                <? if(!cmod('fenxiao')) { ?>
                <div class="col-info r dn">
                    共有<span><? if($v=cls_Parse::Tag(array('ename'=>"counts",'tclass'=>"acount",'chids'=>113,'chsource'=>2,'casource'=>1,'caids'=>605,))){cls_Parse::Active($v);?><?=$v['counts']?><? cls_Parse::ActiveBack();} unset($v);?></span>个<?php echo cls_PushArea::Config('push_zyj','cname'); ?>
                </div>
                <? } ?>
            </div>
            <div class="w270 l mr20 pt20">
                <? if($_pushs=cls_Parse::Tag(array('ename'=>"pushs",'tclass'=>"pushs",'paid'=>"push_120",'limits'=>1,'ttl'=>600,))){foreach($_pushs as $v){ cls_Parse::Active($v);?>
                <? if($a=cls_Parse::Tag(array('ename'=>"archive",'tclass'=>"archive",'chid'=>110,'id'=>"$v[fromid]",'detail'=>1,))){cls_Parse::Active($a);?>
                <div class="form form-control form-tg mb5">
                    <form class="jqValidate" data-tipmode="#bmtip" action="###" onsubmit="return fyCummus(this,'报名')">
                        <h4><?php echo cls_PushArea::Config('push_120','cname'); ?></h4>
                        <p><span class="r"><i id="bmtip"></i></span><?=$a['subject']?></p>
                        <input type="hidden" name="cuid" value="45" />
                        <input type="hidden" name="aid" value="<?=$a['aid']?>" />
                        <ul>
                            <li>
                                <input type="text" class="txt" placeholder="您的姓名" data-type="*3-15" name="fmdata[xingming]" id="xingming" value=""/>
                            </li>
                            <li>
                                <input type="text" class="txt" placeholder="您的手机号" data-type="m" name="fmdata[tel]" value=""/>
                            </li>
                            <li>
                                <select data-type="*" name="fmdata[yxlp]" id="yxlp">
                                <option value="">请选楼盘</option>
                                <? if($_lpopt=cls_Parse::Tag(array('ename'=>"lpopt",'tclass'=>"archives",'chids'=>4,'chsource'=>2,'limits'=>100,'mode'=>"in",'arid'=>32,'detail'=>1,'wherestr'=>"(leixing='0' OR leixing='1')",))){foreach($_lpopt as $b){ cls_Parse::Active($b);?>                                
                                <option value="<?=$b['aid']?>"><?=$b['subject']?></option>
                                <? cls_Parse::ActiveBack();} unset($_lpopt,$b);?><? }else{  } ?>
                                </select>
                            </li>
                            <li>
                                <input type="submit" class="btn" value="申请入团"/>
                            </li>
                        </ul>
                    </form>
                </div>
                <div class="form-list">
                    <div class="form-list-hd mb5">
                        已累计<span><div id="_08_count_aa_<?=$a['aid']?>" class="count08" url-params="{type: 'a', modid: 110, field: 'hdnum'}">&nbsp;</div></span>人报名参加
                    </div>
                    <div class="jqDuang form-list-bd" data-obj="li" data-effect="weibo" data-visible="6" data-steps="1">
                        <ul>
                            <? if($_kftbmls=cls_Parse::Tag(array('ename'=>"kftbmls",'tclass'=>"commus",'cuid'=>45,'checked'=>1,'wherestr'=>"aid ='$a[aid]'",))){foreach($_kftbmls as $b){ cls_Parse::Active($b);?>
                            <li><?php echo cls_string::CutStr($b['xingming'],2,'').'**'; ?> <?php echo " ".substr($b['tel'],0,3).'*****'." "; ?>报名了<? if($c=cls_Parse::Tag(array('ename'=>"lpyxsub",'tclass'=>"archive",'chid'=>4,'id'=>"$b[yxlp]",))){cls_Parse::Active($c);?><a class="fcr" href="<?=$c['arcurl']?>" title="<?=$c['subject']?>" ><?=$c['subject']?></a><? cls_Parse::ActiveBack();} unset($c);?></li>
                            <? cls_Parse::ActiveBack();} unset($_kftbmls,$b);?><? }else{  } ?>
                        </ul>
                    </div>
                </div>
                <? cls_Parse::ActiveBack();} unset($a);?>
                <? cls_Parse::ActiveBack();} unset($_pushs,$v);?><? }else{  } ?>
            </div>
            <div class="w640 l">
                 <!-- plist -->
                <ul class="plist1 plist-mod clearfix">
                    <? if($_pushs=cls_Parse::Tag(array('ename'=>"pushs",'tclass'=>"pushs",'paid'=>"push_5",'limits'=>6,'ttl'=>600,))){foreach($_pushs as $v){ cls_Parse::Active($v);?>
                    <? if($a=cls_Parse::Tag(array('ename'=>"archive",'tclass'=>"archive",'chid'=>5,'id'=>"$v[fromid]",'detail'=>1,))){cls_Parse::Active($a);?>
                     <li>
                         <a class="img" href="<?=$a['arcurl']?>"><img alt="<?=$a['subject']?>" width="193" height="140" src="<? if($u=cls_Parse::Tag(array('ename'=>"image",'tclass'=>"image",'tname'=>"$a[thumb]",'maxwidth'=>193,'maxheight'=>140,'thumb'=>1,'padding'=>1,))){cls_Parse::Active($u);?><?=$u['url_s']?><? cls_Parse::ActiveBack();} unset($u);?>"/></a>
                         <h4><a href="<?=$a['arcurl']?>"><?=$a['subject']?></a></h4>
                         <h5>
                            <span class="r"><?=$a['ccid1title']?></span><span class="fcr fz14"><? if($a['tgj']) { ?><?=$a['tgj']?><? } else { ?>待定<? } ?></span>
                        </h5>
                        <h5 class="tip fcr"><i class="ico08 mr5">&#xe73b;</i><?=$a['yhsm']?></h5>
                     </li>
                     <? cls_Parse::ActiveBack();} unset($a);?>
                    <? cls_Parse::ActiveBack();} unset($_pushs,$v);?><? }else{  } ?>                    
                </ul>
                <ul class="plist1 plist-mod clearfix dn">
                    <? if($_pushs=cls_Parse::Tag(array('ename'=>"pushs",'tclass'=>"pushs",'paid'=>"push_kfttj",'limits'=>6,'ttl'=>600,))){foreach($_pushs as $v){ cls_Parse::Active($v);?>
                    <? if($a=cls_Parse::Tag(array('ename'=>"archive",'tclass'=>"archive",'chid'=>110,'id'=>"$v[fromid]",'detail'=>1,))){cls_Parse::Active($a);?>
                     <li>
                         <a class="img" href="<?=$a['arcurl']?>"><img alt="<?=$a['subject']?>" width="193" height="140" src="<? if($u=cls_Parse::Tag(array('ename'=>"image",'tclass'=>"image",'tname'=>"$a[thumb]",'maxwidth'=>193,'maxheight'=>140,'thumb'=>1,'padding'=>1,))){cls_Parse::Active($u);?><?=$u['url_s']?><? cls_Parse::ActiveBack();} unset($u);?>"/></a>
                         <h4><a href="<?=$a['arcurl']?>"><?=$a['subject']?></a></h4>
                         <h5><span class="fz14"><i class="ico08 mr5 fcr">&#xe612;</i><?=$a['jhdd']?></span></h5>
                         <h5 class="tip fcr"><i class="ico08 mr5">&#xe014;</i><?=$a['kfsj']?></h5>
                     </li>
                    <? cls_Parse::ActiveBack();} unset($a);?>
                    <? cls_Parse::ActiveBack();} unset($_pushs,$v);?><? }else{  } ?>
                </ul>
                <ul class="plist1 plist-mod clearfix dn">
                    <? if($_pushs=cls_Parse::Tag(array('ename'=>"pushs",'tclass'=>"pushs",'paid'=>"push_tjftj",'limits'=>6,'ttl'=>600,))){foreach($_pushs as $v){ cls_Parse::Active($v);?>
                    <? if($a=cls_Parse::Tag(array('ename'=>"archive",'tclass'=>"archive",'chid'=>107,'id'=>"$v[fromid]",'detail'=>1,))){cls_Parse::Active($a);?>
                     <li>
                         <a class="img" href="<?=$a['arcurl']?>"><img alt="<?=$a['subject']?>" width="193" height="140" src="<? if($u=cls_Parse::Tag(array('ename'=>"image",'tclass'=>"image",'tname'=>"$a[thumb]",'maxwidth'=>193,'maxheight'=>140,'thumb'=>1,'padding'=>1,))){cls_Parse::Active($u);?><?=$u['url_s']?><? cls_Parse::ActiveBack();} unset($u);?>"/></a>
                         <h4><a href="<?=$a['arcurl']?>"><?=$a['subject']?></a></h4>
                         <h5><span class="r"><?=$a['ccid18title']?></span><? if($a['zj']) { ?><span class="fz24 fcr"><?=$a['zj']?></span>万<? } else { ?><span class="fz24 fcr">待定</span><? } ?></h5>
                         <h5 class="tip"><span class="r"><?=$a['mj']?>m&sup2;</span><i class="ico08 mr5 fcr">&#xe004;</i><?=cls_Parse::Tag(array('ename'=>"shi",'tclass'=>"field",'tname'=>"$a[shi]",'type'=>"archive",'fname'=>"shi",))?><??><?=cls_Parse::Tag(array('ename'=>"ting",'tclass'=>"field",'tname'=>"$a[ting]",'type'=>"archive",'fname'=>"ting",))?><??><?=cls_Parse::Tag(array('ename'=>"wei",'tclass'=>"field",'tname'=>"$a[wei]",'type'=>"archive",'fname'=>"wei",))?><??></h5>
                     </li>
                    <? cls_Parse::ActiveBack();} unset($a);?>
                    <? cls_Parse::ActiveBack();} unset($_pushs,$v);?><? }else{  } ?>
                </ul>
                <? if(!cmod('fenxiao')) { ?>
                <ul class="plist1 plist-mod clearfix dn">
                    <? if($_pushs=cls_Parse::Tag(array('ename'=>"pushs",'tclass'=>"pushs",'paid'=>"push_zyj",'limits'=>6,'ttl'=>600,))){foreach($_pushs as $v){ cls_Parse::Active($v);?>
                    <? if($a=cls_Parse::Tag(array('ename'=>"archive",'tclass'=>"archive",'chid'=>113,'id'=>"$v[fromid]",'detail'=>1,))){cls_Parse::Active($a);?>
                     <li>
                         <a class="img" href="<?=$a['arcurl']?>"><img alt="<?=$a['subject']?>" width="193" height="140" src="<? if($u=cls_Parse::Tag(array('ename'=>"image",'tclass'=>"image",'tname'=>"$a[thumb]",'maxwidth'=>193,'maxheight'=>140,'thumb'=>1,'padding'=>1,))){cls_Parse::Active($u);?><?=$u['url_s']?><? cls_Parse::ActiveBack();} unset($u);?>"/></a>
                         <h4><a href="<?=$a['arcurl']?>"><?=$a['subject']?></a></h4>
                         <h5><span class="r"><?=$a['ccid1title']?></span>佣金<span class="fz24 fcr"><?=$a['yj']?></span>元</h5>
                         <h5 class="tip"><i class="ico08 fcr mr5">&#xe73b;</i><?=$a['yhsm']?></h5>
                     </li>
                    <? cls_Parse::ActiveBack();} unset($a);?>
                    <? cls_Parse::ActiveBack();} unset($_pushs,$v);?><? }else{  } ?>
                </ul>
                <? } ?>
            </div>
            <div class="w270 r">
                <? if($_farchives=cls_Parse::Tag(array('ename'=>"farchives",'tclass'=>"farchives",'limits'=>3,'validperiod'=>1,'casource'=>"fcatalog176",'orderstr'=>"a.vieworder ASC",))){foreach($_farchives as $v){ cls_Parse::Active($v);?>
                <div class="ad mt20">
                    <? if($v['html']) { ?><?=$v['html']?><? } elseif($v['image']) { ?><a href="<?=$v['link']?>" target="_blank"><img <? if($u=cls_Parse::Tag(array('ename'=>"image",'tclass'=>"image",'tname'=>"$v[image]",'maxwidth'=>270,'maxheight'=>148,))){cls_Parse::Active($u);?> src="<?=$u['url']?>"  height="148" width="270"
                <? cls_Parse::ActiveBack();} unset($u);?>/></a><? } elseif($v['flash']) { ?><? if($f=cls_Parse::Tag(array('ename'=>"flash",'tclass'=>"flash",'tname'=>"$v[flash]",'width'=>270,'height'=>148,))){cls_Parse::Active($f);?><?=$f['playbox']?><? if($v['link']) { ?><a href="<?=$v['link']?>" style="position: relative;margin-top:-<?=$f['height']?>px;width:<?=$f['width']?>px;height:<?=$f['height']?>px;display:block" target="_blank"><img width="<?=$f['width']?>" height="<?=$f['height']?>" src="<?=$cms_abs?>userfiles/notdel/blank.gif"/></a><? } ?><? cls_Parse::ActiveBack();} unset($f);?><? } ?>
                </div>
                <? cls_Parse::ActiveBack();} unset($_farchives,$v);?><? }else{  } ?><!-- /ad270_148 -->
            </div>
        </div>
        <div class="blank20"></div>
        <? if($_farchives=cls_Parse::Tag(array('ename'=>"farchives",'tclass'=>"farchives",'limits'=>10,'validperiod'=>1,'casource'=>"fcatalog133",'orderstr'=>"a.vieworder ASC",))){foreach($_farchives as $v){ cls_Parse::Active($v);?>
        <div class="ad mb3">
            <? if($v['html']) { ?><?=$v['html']?><? } elseif($v['image']) { ?><a href="<?=$v['link']?>" target="_blank"><img <? if($u=cls_Parse::Tag(array('ename'=>"image",'tclass'=>"image",'tname'=>"$v[image]",'maxwidth'=>1200,'maxheight'=>60,))){cls_Parse::Active($u);?> src="<?=$u['url']?>"  height="60" width="1200"
        <? cls_Parse::ActiveBack();} unset($u);?>/></a><? } elseif($v['flash']) { ?><? if($f=cls_Parse::Tag(array('ename'=>"flash",'tclass'=>"flash",'tname'=>"$v[flash]",'width'=>1200,'height'=>60,))){cls_Parse::Active($f);?><?=$f['playbox']?><? if($v['link']) { ?><a href="<?=$v['link']?>" style="position: relative;margin-top:-<?=$f['height']?>px;width:<?=$f['width']?>px;height:<?=$f['height']?>px;display:block" target="_blank"><img width="<?=$f['width']?>" height="<?=$f['height']?>" src="<?=$cms_abs?>userfiles/notdel/blank.gif"/></a><? } ?><? cls_Parse::ActiveBack();} unset($f);?><? } ?>
        </div>
        <? cls_Parse::ActiveBack();} unset($_farchives,$v);?><? }else{  } ?><!-- /ad1200_60 -->
        <div class="blank20"></div>
        <div id="xptj" class="jqDuang clearfix" data-cell=".col-nav" data-obj=".plist1" data-autoplay="0" data-speed="0">
            <div class="col-title clearfix">
                <h2 class="l">新盘推荐</h2>
                <div class="col-nav l">
                    <?php
                        $ii = 0;
                        foreach (u_field_by(4, 'tslp') as $key => $value) {
                            echo '<a class="'.($ii==0?'act':'').'" href="'.$cms_abs.'index.php?caid=2&addno=1&tslp='.$key.'" ><span>'.$value.'</span><i class="ico08">&#xf0dc;</i></a>';
                            $ii++;
                            if ($ii == 7) break;
                        }
                    ?>
                </div>
                <div class="col-info r">
                    这个共有<span>4350</span>个楼盘
                </div>
            </div>
            <div class="w270 l mr20 pt20">
                <div class="jqDuang"  data-cell=".tab-hd1" data-obj=".tlist" data-autoplay="0" data-speed="0">
                    <div class="tab-hd1 clearfix mb5">
                        <a class="act" href="javascript:;"><?php echo cls_PushArea::Config('push_zxkp','cname'); ?></a>
                        <a href="javascript:;"><?php echo cls_PushArea::Config('push_rqlp','cname'); ?></a>
                    </div>
                    <div class="tab-bd1">
                        <ul class="tlist">
                            <? if($_pushs=cls_Parse::Tag(array('ename'=>"pushs",'tclass'=>"pushs",'paid'=>"push_zxkp",'limits'=>11,'ttl'=>600,))){foreach($_pushs as $v){ cls_Parse::Active($v);?>
                            <? if($a=cls_Parse::Tag(array('ename'=>"archive",'tclass'=>"archive",'chid'=>4,'id'=>"$v[fromid]",'detail'=>1,))){cls_Parse::Active($a);?>
                            <li><span class="r fcr"><? if($a['kprq']) { ?><?=$a['kprq']?><? } else { ?><?=cls_Parse::Tag(array('ename'=>"date",'tclass'=>"date",'tname'=>"$a[kpsj]",'date'=>"Y-m-d",))?><??><? } ?></span><a href="<?=$a['arcurl']?>"><?=$a['subject']?><span class="fcg fz12">[<?=$a['ccid1title']?>]</span></a></li>
                            <? cls_Parse::ActiveBack();} unset($a);?>
                            <? cls_Parse::ActiveBack();} unset($_pushs,$v);?><? }else{  } ?>
                        </ul><!-- /tlist -->
                        <ul class="tlist dn">
                            <? if($_pushs=cls_Parse::Tag(array('ename'=>"pushs",'tclass'=>"pushs",'paid'=>"push_rqlp",'limits'=>11,'ttl'=>600,))){foreach($_pushs as $v){ cls_Parse::Active($v);?>
                            <? if($a=cls_Parse::Tag(array('ename'=>"archive",'tclass'=>"archive",'chid'=>4,'id'=>"$v[fromid]",'detail'=>1,))){cls_Parse::Active($a);?>
                            <li><span class="r fcr"><? if($a['dj']) { ?><?=$a['dj']?>元/m&sup2;<? } else { ?>待定<? } ?></span><a href="<?=$a['arcurl']?>"><?=$a['subject']?><span class="fcg fz12">[<?=$a['ccid1title']?>]</span></a></li>
                            <? cls_Parse::ActiveBack();} unset($a);?>
                            <? cls_Parse::ActiveBack();} unset($_pushs,$v);?><? }else{  } ?>
                        </ul><!-- /tlist -->
                    </div>
                </div>                
            </div>
            <div class="w640 l">
            <!-- 特色楼盘列表 -->
                <?php
                    $ii = 0;
                    foreach (u_field_by(4, 'tslp') as $key => $value) {
                ?>
                <ul class="plist1 plist-mod clearfix <? if($ii > 0) { ?>dn<? } ?>">
                    <? if($_archives=cls_Parse::Tag(array('ename'=>"archives",'tclass'=>"archives",'limits'=>6,'chsource'=>2,'chids'=>4,'casource'=>1,'caids'=>2,'detail'=>1,'wherestr'=>"(leixing='0' OR leixing='1') and CONCAT('\t',tslp,'\t') LIKE '%\t$key\t%'",'ttl'=>600,))){foreach($_archives as $v){ cls_Parse::Active($v);?>
                     <li>
                         <a class="img" href="<?=$v['arcurl']?>"><img alt="<?=$v['subject']?>" width="193" height="140" src="<? if($u=cls_Parse::Tag(array('ename'=>"image",'tclass'=>"image",'tname'=>"$v[thumb]",'maxwidth'=>193,'maxheight'=>140,'thumb'=>1,'padding'=>1,))){cls_Parse::Active($u);?><?=$u['url_s']?><? cls_Parse::ActiveBack();} unset($u);?>"/></a>
                         <h4><a href="<?=$v['arcurl']?>"><?=$v['subject']?></a></h4>
                         <h5><span class="r"><?=$v['ccid1title']?></span><? if($v['dj']) { ?><span class="fz24 fcr"><?=$v['dj']?></span>元/m&sup2;<? } else { ?><span class="fz24 fcr">待定</span><? } ?></h5>
                         <h5 class="tip fcr"><i class="ico08 mr5">&#xe73b;</i><?=$v['bdsm']?></h5>
                         
                     </li>
                    <? cls_Parse::ActiveBack();} unset($_archives,$v);?><? }else{  } ?>
                </ul><!-- /plist -->
                <?php
                        $ii++;
                        if ($ii == 7) break;
                    }
                ?>
            </div>
            <div class="w270 r pt20">
                <ul class="tlist1">
                    <? if($_pushs=cls_Parse::Tag(array('ename'=>"pushs",'tclass'=>"pushs",'paid'=>"push_7",'limits'=>12,'ttl'=>600,))){foreach($_pushs as $v){ cls_Parse::Active($v);?>
                    <? if($v['sn_row']==7) { ?>
                </ul><!-- /tlist -->
                <div class="blank20"></div>
                <ul class="tlist1">
                    <? } ?>
                    <? if($v['sn_row']%6 == 1) { ?>
                    <li class="dt"><a href="<?=$v['url']?>"><?=$v['subject']?></a></li>
                    <? } else { ?>
                    <li><? if($a=cls_Parse::Tag(array('ename'=>"cnode",'tclass'=>"cnode",'listby'=>"ca",'casource'=>"$v[classid1]",))){cls_Parse::Active($a);?><a href="<?=$a['indexurl']?>"><?=$a['title']?></a> | <? cls_Parse::ActiveBack();} unset($a);?><a href="<?=$v['url']?>"><?=$v['subject']?></a></li>
                    <? } ?>
                    <? cls_Parse::ActiveBack();} unset($_pushs,$v);?><? }else{  } ?>
                </ul><!-- /tlist -->
            </div>
        </div>
        <div class="blank20"></div>
        <? if(!cmod('shangye')) { ?>
        <? if($_farchives=cls_Parse::Tag(array('ename'=>"farchives",'tclass'=>"farchives",'limits'=>10,'validperiod'=>1,'casource'=>"fcatalog134",'orderstr'=>"a.vieworder ASC",))){foreach($_farchives as $v){ cls_Parse::Active($v);?>
        <div class="ad mb3">
            <? if($v['html']) { ?><?=$v['html']?><? } elseif($v['image']) { ?><a href="<?=$v['link']?>" target="_blank"><img <? if($u=cls_Parse::Tag(array('ename'=>"image",'tclass'=>"image",'tname'=>"$v[image]",'maxwidth'=>1200,'maxheight'=>60,))){cls_Parse::Active($u);?> src="<?=$u['url']?>"  height="60" width="1200"
        <? cls_Parse::ActiveBack();} unset($u);?>/></a><? } elseif($v['flash']) { ?><? if($f=cls_Parse::Tag(array('ename'=>"flash",'tclass'=>"flash",'tname'=>"$v[flash]",'width'=>1200,'height'=>60,))){cls_Parse::Active($f);?><?=$f['playbox']?><? if($v['link']) { ?><a href="<?=$v['link']?>" style="position: relative;margin-top:-<?=$f['height']?>px;width:<?=$f['width']?>px;height:<?=$f['height']?>px;display:block" target="_blank"><img width="<?=$f['width']?>" height="<?=$f['height']?>" src="<?=$cms_abs?>userfiles/notdel/blank.gif"/></a><? } ?><? cls_Parse::ActiveBack();} unset($f);?><? } ?>
        </div>
        <? cls_Parse::ActiveBack();} unset($_farchives,$v);?><? }else{  } ?><!-- /ad1200_60 -->
        <div class="blank20"></div>
        <div id="sydc" class="jqDuang clearfix" data-cell=".col-nav" data-obj=".plist1|#sydc .condition" data-autoplay="0" data-speed="0">
            <div class="col-title clearfix">
                <h2 class="l">商业地产</h2>
                <div class="col-nav l">
                <? if($_catalogs=cls_Parse::Tag(array('ename'=>"catalogs",'tclass'=>"catalogs",'listby'=>"ca",'casource'=>1,'caids'=>"612,613,614,616,617,618",'orderstr'=>"vieworder asc",'ttl'=>3600,))){foreach($_catalogs as $v){ cls_Parse::Active($v);?><a class="<? if($v['sn_row']==1) { ?>act<? } ?>" href="<?=$v['indexurl']?>"><span><?=$v['title']?></span><i class="ico08">&#xf0dc;</i></a><? cls_Parse::ActiveBack();} unset($_catalogs,$v);?><? }else{  } ?>
                </div>
            </div>
            <div class="w270 l mr20 pt20">
                <dl class="condition">
                    <dd>
                        <div class="con-cate">
                            <h4><i class="ico08 r fcg">&#xe68f;</i>区域</h4>
                            <div class="con-cate-list">
                                <? if($_catalogs=cls_Parse::Tag(array('ename'=>"catalogs",'tclass'=>"catalogs",'limits'=>20,'listby'=>"co1",'cainherit'=>616,))){foreach($_catalogs as $v){ cls_Parse::Active($v);?><a href="<?=$v['indexurl']?>"><?=$v['title']?></a><? cls_Parse::ActiveBack();} unset($_catalogs,$v);?><? }else{  } ?>
                            </div>
                        </div>
                        <div class="con-cate-more">
                            <h4>所有区域</h4>
                            <div class="con-cate-list">
                                <? if($_catalogs=cls_Parse::Tag(array('ename'=>"catalogs",'tclass'=>"catalogs",'limits'=>20,'listby'=>"co1",'cainherit'=>616,))){foreach($_catalogs as $v){ cls_Parse::Active($v);?><a href="<?=$v['indexurl']?>"><?=$v['title']?></a><? cls_Parse::ActiveBack();} unset($_catalogs,$v);?><? }else{  } ?>
                            </div>
                        </div>
                    </dd>
                    <dd>
                        <div class="con-cate">
                            <h4><i class="ico08 r fcg">&#xe68f;</i>类型</h4>
                            <div class="con-cate-list">
                                <? if($_catalogs=cls_Parse::Tag(array('ename'=>"catalogs",'tclass'=>"catalogs",'limits'=>20,'listby'=>"co48",'cainherit'=>616,))){foreach($_catalogs as $v){ cls_Parse::Active($v);?><a href="<?=$v['indexurl']?>"><?=$v['title']?></a><? cls_Parse::ActiveBack();} unset($_catalogs,$v);?><? }else{  } ?>
                            </div>
                        </div>
                        <div class="con-cate-more">
                            <h4>所有类型</h4>
                            <div class="con-cate-list">
                                <? if($_catalogs=cls_Parse::Tag(array('ename'=>"catalogs",'tclass'=>"catalogs",'limits'=>20,'listby'=>"co48",'cainherit'=>616,))){foreach($_catalogs as $v){ cls_Parse::Active($v);?><a href="<?=$v['indexurl']?>"><?=$v['title']?></a><? cls_Parse::ActiveBack();} unset($_catalogs,$v);?><? }else{  } ?>
                            </div>
                        </div>
                    </dd>
                    <dd class="last">
                        <div class="con-cate">
                            <h4><i class="ico08 r fcg">&#xe68f;</i>价格</h4>
                            <div class="con-cate-list">
                                <? if($_catalogs=cls_Parse::Tag(array('ename'=>"catalogs",'tclass'=>"catalogs",'limits'=>20,'listby'=>"co17",'cainherit'=>616,))){foreach($_catalogs as $v){ cls_Parse::Active($v);?><a href="<?=$v['indexurl']?>"><?=$v['title']?></a><? cls_Parse::ActiveBack();} unset($_catalogs,$v);?><? }else{  } ?>
                            </div>
                        </div>
                        <div class="con-cate-more">
                            <h4>所有价格</h4>
                            <div class="con-cate-list">
                                <? if($_catalogs=cls_Parse::Tag(array('ename'=>"catalogs",'tclass'=>"catalogs",'limits'=>20,'listby'=>"co17",'cainherit'=>616,))){foreach($_catalogs as $v){ cls_Parse::Active($v);?><a href="<?=$v['indexurl']?>"><?=$v['title']?></a><? cls_Parse::ActiveBack();} unset($_catalogs,$v);?><? }else{  } ?>
                            </div>
                        </div>
                    </dd>
                </dl><!-- /商铺楼盘 -->
                <dl class="condition dn">
                    <dd>
                        <div class="con-cate">
                            <h4><i class="ico08 r fcg">&#xe68f;</i>区域</h4>
                            <div class="con-cate-list">
                                <? if($_catalogs=cls_Parse::Tag(array('ename'=>"catalogs",'tclass'=>"catalogs",'limits'=>20,'listby'=>"co1",'cainherit'=>612,))){foreach($_catalogs as $v){ cls_Parse::Active($v);?><a href="<?=$v['indexurl']?>"><?=$v['title']?></a><? cls_Parse::ActiveBack();} unset($_catalogs,$v);?><? }else{  } ?>
                            </div>
                        </div>
                        <div class="con-cate-more">
                            <h4>所有区域</h4>
                            <div class="con-cate-list">
                                <? if($_catalogs=cls_Parse::Tag(array('ename'=>"catalogs",'tclass'=>"catalogs",'limits'=>20,'listby'=>"co1",'cainherit'=>612,))){foreach($_catalogs as $v){ cls_Parse::Active($v);?><a href="<?=$v['indexurl']?>"><?=$v['title']?></a><? cls_Parse::ActiveBack();} unset($_catalogs,$v);?><? }else{  } ?>
                            </div>
                        </div>
                    </dd>
                    <dd>
                        <div class="con-cate">
                            <h4><i class="ico08 r fcg">&#xe68f;</i>类型</h4>
                            <div class="con-cate-list">
                                <? if($_catalogs=cls_Parse::Tag(array('ename'=>"catalogs",'tclass'=>"catalogs",'limits'=>20,'listby'=>"co46",'cainherit'=>612,))){foreach($_catalogs as $v){ cls_Parse::Active($v);?><a href="<?=$v['indexurl']?>"><?=$v['title']?></a><? cls_Parse::ActiveBack();} unset($_catalogs,$v);?><? }else{  } ?>
                            </div>
                        </div>
                        <div class="con-cate-more">
                            <h4>所有类型</h4>
                            <div class="con-cate-list">
                                <? if($_catalogs=cls_Parse::Tag(array('ename'=>"catalogs",'tclass'=>"catalogs",'limits'=>20,'listby'=>"co46",'cainherit'=>612,))){foreach($_catalogs as $v){ cls_Parse::Active($v);?><a href="<?=$v['indexurl']?>"><?=$v['title']?></a><? cls_Parse::ActiveBack();} unset($_catalogs,$v);?><? }else{  } ?>
                            </div>
                        </div>
                    </dd>
                    <dd class="last">
                        <div class="con-cate">
                            <h4><i class="ico08 r fcg">&#xe68f;</i>价格</h4>
                            <div class="con-cate-list">
                                <? if($_catalogs=cls_Parse::Tag(array('ename'=>"catalogs",'tclass'=>"catalogs",'limits'=>20,'listby'=>"co17",'cainherit'=>612,))){foreach($_catalogs as $v){ cls_Parse::Active($v);?><a href="<?=$v['indexurl']?>"><?=$v['title']?></a><? cls_Parse::ActiveBack();} unset($_catalogs,$v);?><? }else{  } ?>
                            </div>
                        </div>
                        <div class="con-cate-more">
                            <h4>所有价格</h4>
                            <div class="con-cate-list">
                                <? if($_catalogs=cls_Parse::Tag(array('ename'=>"catalogs",'tclass'=>"catalogs",'limits'=>20,'listby'=>"co17",'cainherit'=>612,))){foreach($_catalogs as $v){ cls_Parse::Active($v);?><a href="<?=$v['indexurl']?>"><?=$v['title']?></a><? cls_Parse::ActiveBack();} unset($_catalogs,$v);?><? }else{  } ?>
                            </div>
                        </div>
                    </dd>
                </dl><!-- /写字楼楼盘 -->
                
                <div class="blank19"></div>
                <!-- ad270_190 -->
                <? if($_farchives=cls_Parse::Tag(array('ename'=>"farchives",'tclass'=>"farchives",'limits'=>1,'validperiod'=>1,'casource'=>"fcatalog127",'orderstr'=>"a.vieworder ASC",))){foreach($_farchives as $v){ cls_Parse::Active($v);?>
                <div class="ad"><? if($v['html']) { ?><?=$v['html']?><? } elseif($v['image']) { ?><a href="<?=$v['link']?>" target="_blank"><img <? if($u=cls_Parse::Tag(array('ename'=>"image",'tclass'=>"image",'tname'=>"$v[image]",'maxwidth'=>270,'maxheight'=>190,))){cls_Parse::Active($u);?> src="<?=$u['url']?>"  height="190" width="270"
                    <? cls_Parse::ActiveBack();} unset($u);?>/></a><? } elseif($v['flash']) { ?><? if($f=cls_Parse::Tag(array('ename'=>"flash",'tclass'=>"flash",'tname'=>"$v[flash]",'width'=>270,'height'=>190,))){cls_Parse::Active($f);?><?=$f['playbox']?><? if($v['link']) { ?><a href="<?=$v['link']?>" style="position: relative;margin-top:-<?=$f['height']?>px;width:<?=$f['width']?>px;height:<?=$f['height']?>px;display:block" target="_blank"><img width="<?=$f['width']?>" height="<?=$f['height']?>" src="<?=$cms_abs?>userfiles/notdel/blank.gif"/></a><? } ?><? cls_Parse::ActiveBack();} unset($f);?><? } ?></div>
                <? cls_Parse::ActiveBack();} unset($_farchives,$v);?><? }else{  } ?>
            </div>
            <div class="w640 l">
                <ul class="plist1 plist-mod clearfix">
                    <? if($_pushs=cls_Parse::Tag(array('ename'=>"pushs",'tclass'=>"pushs",'paid'=>"push_spcs",'limits'=>6,))){foreach($_pushs as $v){ cls_Parse::Active($v);?>
                    <? if($a=cls_Parse::Tag(array('ename'=>"archive",'tclass'=>"archive",'chid'=>116,'id'=>"$v[fromid]",'detail'=>1,))){cls_Parse::Active($a);?>
                    <li>
                        <a class="img" href="<?=$a['arcurl']?>"><img width="193" height="140" src="<? if($u=cls_Parse::Tag(array('ename'=>"image",'tclass'=>"image",'tname'=>"$a[thumb]",'maxwidth'=>193,'maxheight'=>140,'thumb'=>1,'padding'=>0,))){cls_Parse::Active($u);?><?=$u['url_s']?><? cls_Parse::ActiveBack();} unset($u);?>"/></a>
                        <h4><a href="<?=$a['arcurl']?>"><?=$a['subject']?></a></h4>
                        <h5><span class="r"><?=$a['ccid1title']?></span><? if($a['dj']) { ?><span class="fz24 fcr"><?=$a['dj']?></span>元/m&sup2;<? } else { ?><span class="fz24 fcr">待定</span><? } ?></h5>
                        <h5 class="tip"><i class="ico08 mr5 fcr">&#xe73b;</i><?=$a['bdsm']?></h5>
                     </li>
                    <? cls_Parse::ActiveBack();} unset($a);?>
                    <? cls_Parse::ActiveBack();} unset($_pushs,$v);?><? }else{  } ?>
                </ul><!-- /plist -->
                <ul class="plist1 plist-mod clearfix dn">
                    <? if($_pushs=cls_Parse::Tag(array('ename'=>"pushs",'tclass'=>"pushs",'paid'=>"push_xzlcs",'limits'=>6,))){foreach($_pushs as $v){ cls_Parse::Active($v);?>
                    <? if($a=cls_Parse::Tag(array('ename'=>"archive",'tclass'=>"archive",'chid'=>115,'id'=>"$v[fromid]",'detail'=>1,))){cls_Parse::Active($a);?>
                    <li>
                        <a class="img" href="<?=$a['arcurl']?>"><img width="193" height="140" src="<? if($u=cls_Parse::Tag(array('ename'=>"image",'tclass'=>"image",'tname'=>"$a[thumb]",'maxwidth'=>193,'maxheight'=>140,'thumb'=>1,'padding'=>0,))){cls_Parse::Active($u);?><?=$u['url_s']?><? cls_Parse::ActiveBack();} unset($u);?>"/></a>
                        <h4><a href="<?=$a['arcurl']?>"><?=$a['subject']?></a></h4>
                        <h5><span class="r"><?=$a['ccid1title']?></span><? if($a['dj']) { ?><span class="fz24 fcr"><?=$a['dj']?></span>元/m&sup2;<? } else { ?><span class="fz24 fcr">待定</span><? } ?></h5>
                        <h5 class="tip"><i class="ico08 mr5 fcr">&#xe73b;</i><?=$a['bdsm']?></h5>
                     </li>
                    <? cls_Parse::ActiveBack();} unset($a);?>
                    <? cls_Parse::ActiveBack();} unset($_pushs,$v);?><? }else{  } ?>
                </ul><!-- /plist -->
            </div>
            <div class="w270 r pt20">
                <ul class="tlist1">
                    <? if($_pushs=cls_Parse::Tag(array('ename'=>"pushs",'tclass'=>"pushs",'paid'=>"push_sydt",'limits'=>9,'ttl'=>600,))){foreach($_pushs as $v){ cls_Parse::Active($v);?>
                    <li class="<? if($v['sn_row']==1) { ?>dt<? } ?>"><a href="<?=$v['url']?>" title="<?=$v['subject']?>"><?=cls_Parse::Tag(array('ename'=>"subject",'tclass'=>"text",'tname'=>"$v[subject]",'trim'=>100,'color'=>"$v[color]",))?><??></a></li>
                    <? cls_Parse::ActiveBack();} unset($_pushs,$v);?><? }else{  } ?>
                </ul><!-- /tlist -->
                <div class="blank16"></div>
                <!-- ad270_125 -->
                <? if($_farchives=cls_Parse::Tag(array('ename'=>"farchives",'tclass'=>"farchives",'limits'=>1,'validperiod'=>1,'casource'=>"fcatalog135",'orderstr'=>"a.vieworder ASC",))){foreach($_farchives as $v){ cls_Parse::Active($v);?>
                <div class="ad"><? if($v['html']) { ?><?=$v['html']?><? } elseif($v['image']) { ?><a href="<?=$v['link']?>" target="_blank"><img <? if($u=cls_Parse::Tag(array('ename'=>"image",'tclass'=>"image",'tname'=>"$v[image]",'maxwidth'=>270,'maxheight'=>125,))){cls_Parse::Active($u);?> src="<?=$u['url']?>"  height="125" width="270"
                    <? cls_Parse::ActiveBack();} unset($u);?>/></a><? } elseif($v['flash']) { ?><? if($f=cls_Parse::Tag(array('ename'=>"flash",'tclass'=>"flash",'tname'=>"$v[flash]",'width'=>270,'height'=>125,))){cls_Parse::Active($f);?><?=$f['playbox']?><? if($v['link']) { ?><a href="<?=$v['link']?>" style="position: relative;margin-top:-<?=$f['height']?>px;width:<?=$f['width']?>px;height:<?=$f['height']?>px;display:block" target="_blank"><img width="<?=$f['width']?>" height="<?=$f['height']?>" src="<?=$cms_abs?>userfiles/notdel/blank.gif"/></a><? } ?><? cls_Parse::ActiveBack();} unset($f);?><? } ?></div>
                <? cls_Parse::ActiveBack();} unset($_farchives,$v);?><? }else{  } ?>
            </div>
        </div>
        <div class="blank20"></div>
        <? } ?>
        
        <? if($_farchives=cls_Parse::Tag(array('ename'=>"farchives",'tclass'=>"farchives",'limits'=>10,'validperiod'=>1,'casource'=>"fcatalog165",'orderstr'=>"a.vieworder ASC",))){foreach($_farchives as $v){ cls_Parse::Active($v);?>
        <div class="ad mb3">
            <? if($v['html']) { ?><?=$v['html']?><? } elseif($v['image']) { ?><a href="<?=$v['link']?>" target="_blank"><img <? if($u=cls_Parse::Tag(array('ename'=>"image",'tclass'=>"image",'tname'=>"$v[image]",'maxwidth'=>1200,'maxheight'=>60,))){cls_Parse::Active($u);?> src="<?=$u['url']?>"  height="60" width="1200"
        <? cls_Parse::ActiveBack();} unset($u);?>/></a><? } elseif($v['flash']) { ?><? if($f=cls_Parse::Tag(array('ename'=>"flash",'tclass'=>"flash",'tname'=>"$v[flash]",'width'=>1200,'height'=>60,))){cls_Parse::Active($f);?><?=$f['playbox']?><? if($v['link']) { ?><a href="<?=$v['link']?>" style="position: relative;margin-top:-<?=$f['height']?>px;width:<?=$f['width']?>px;height:<?=$f['height']?>px;display:block" target="_blank"><img width="<?=$f['width']?>" height="<?=$f['height']?>" src="<?=$cms_abs?>userfiles/notdel/blank.gif"/></a><? } ?><? cls_Parse::ActiveBack();} unset($f);?><? } ?>
        </div>
        <? cls_Parse::ActiveBack();} unset($_farchives,$v);?><? }else{  } ?><!-- /ad1200_60 -->
        <div class="blank20"></div>
        
        <div id="escz" class="jqDuang clearfix" data-cell=".col-nav" data-obj=".plist1|#escz .condition" data-autoplay="0" data-speed="0">
            <div class="col-title clearfix">
                <h2 class="l">二手房 <i class="ico08 fz12" style="vertical-align:top;_vertical-align:middle">&#xf052;</i> 出租</h2>
                <div class="col-nav col-nav-lg l">
                    <a class="act" href="<? if($a=cls_Parse::Tag(array('ename'=>"cnode",'tclass'=>"cnode",'listby'=>"ca",'casource'=>3,))){cls_Parse::Active($a);?><?=$a['indexurl']?><? cls_Parse::ActiveBack();} unset($a);?>"><span>二手房</span><i class="ico08">&#xf0dc;</i></a>
                    <a href="<? if($a=cls_Parse::Tag(array('ename'=>"cnode",'tclass'=>"cnode",'listby'=>"ca",'casource'=>4,))){cls_Parse::Active($a);?><?=$a['indexurl']?><? cls_Parse::ActiveBack();} unset($a);?>"><span>出租房</span><i class="ico08">&#xf0dc;</i></a>
                    <a href="<? if($a=cls_Parse::Tag(array('ename'=>"cnode",'tclass'=>"cnode",'listby'=>"ca",'casource'=>2,))){cls_Parse::Active($a);?><?=$a['indexurl2']?><? cls_Parse::ActiveBack();} unset($a);?>"><span>热门小区</span><i class="ico08">&#xf0dc;</i></a>
                    <a href="<? if($v=cls_Parse::Tag(array('ename'=>"jjrjd",'tclass'=>"mcnode",'cnsource'=>"mcnid",'cnid'=>14,))){cls_Parse::Active($v);?><?=$v['mcnurl']?><? cls_Parse::ActiveBack();} unset($v);?>"><span>经纪人</span><i class="ico08">&#xf0dc;</i></a>
                    <a href="<? if($v=cls_Parse::Tag(array('ename'=>"jjgsurl",'tclass'=>"mcnode",'cnsource'=>"mcnid",'cnid'=>51,))){cls_Parse::Active($v);?><?=$v['mcnurl']?><? cls_Parse::ActiveBack();} unset($v);?>"><span>经纪公司</span><i class="ico08">&#xf0dc;</i></a>
                    <a href="<? if($a=cls_Parse::Tag(array('ename'=>"cnode",'tclass'=>"cnode",'listby'=>"ca",'casource'=>9,))){cls_Parse::Active($a);?><?=$a['indexurl']?><? cls_Parse::ActiveBack();} unset($a);?>"><span>求租</span><i class="ico08">&#xf0dc;</i></a>
                    <a href="<? if($a=cls_Parse::Tag(array('ename'=>"cnode",'tclass'=>"cnode",'listby'=>"ca",'casource'=>10,))){cls_Parse::Active($a);?><?=$a['indexurl']?><? cls_Parse::ActiveBack();} unset($a);?>"><span>求购</span><i class="ico08">&#xf0dc;</i></a>
                </div>
            </div>
            <div class="w270 l mr20 pt20">
                <dl class="condition">
                    <dd>
                        <div class="con-cate">
                            <h4><i class="ico08 r fcg">&#xe68f;</i>区域</h4>
                            <div class="con-cate-list">
                                <? if($_catalogs=cls_Parse::Tag(array('ename'=>"catalogs",'tclass'=>"catalogs",'limits'=>15,'listby'=>"co1",'cainherit'=>3,))){foreach($_catalogs as $v){ cls_Parse::Active($v);?><a href="<?=$v['indexurl']?>" ><?=$v['title']?></a><? cls_Parse::ActiveBack();} unset($_catalogs,$v);?><? }else{  } ?>
                            </div>
                        </div>
                        <div class="con-cate-more">
                            <h4>所有区域</h4>
                            <div class="con-cate-list">
                                <? if($_catalogs=cls_Parse::Tag(array('ename'=>"catalogs",'tclass'=>"catalogs",'limits'=>15,'listby'=>"co1",'cainherit'=>3,))){foreach($_catalogs as $v){ cls_Parse::Active($v);?><a href="<?=$v['indexurl']?>" ><?=$v['title']?></a><? cls_Parse::ActiveBack();} unset($_catalogs,$v);?><? }else{  } ?>
                            </div>
                        </div>
                    </dd>
                    <dd>
                        <div class="con-cate">
                            <h4><i class="ico08 r fcg">&#xe68f;</i>价格</h4>
                            <div class="con-cate-list">
                                <? if($_catalogs=cls_Parse::Tag(array('ename'=>"catalogs",'tclass'=>"catalogs",'listby'=>"co4",'cainherit'=>3,))){foreach($_catalogs as $v){ cls_Parse::Active($v);?><a href="<?=$v['indexurl']?>" ><?=$v['title']?></a><? cls_Parse::ActiveBack();} unset($_catalogs,$v);?><? }else{  } ?>
                            </div>
                        </div>
                        <div class="con-cate-more">
                            <h4>所有价格</h4>
                            <div class="con-cate-list">
                                <? if($_catalogs=cls_Parse::Tag(array('ename'=>"catalogs",'tclass'=>"catalogs",'listby'=>"co4",'cainherit'=>3,))){foreach($_catalogs as $v){ cls_Parse::Active($v);?><a href="<?=$v['indexurl']?>" ><?=$v['title']?></a><? cls_Parse::ActiveBack();} unset($_catalogs,$v);?><? }else{  } ?>
                            </div>
                        </div>
                    </dd>
                    <dd>
                        <div class="con-cate">
                            <h4><i class="ico08 r fcg">&#xe68f;</i>面积</h4>
                            <div class="con-cate-list">
                                <? if($_catalogs=cls_Parse::Tag(array('ename'=>"catalogs",'tclass'=>"catalogs",'limits'=>15,'listby'=>"co6",'cainherit'=>3,))){foreach($_catalogs as $v){ cls_Parse::Active($v);?><a href="<?=$v['indexurl']?>" ><?=$v['title']?></a><? cls_Parse::ActiveBack();} unset($_catalogs,$v);?><? }else{  } ?>
                            </div>
                        </div>
                        <div class="con-cate-more">
                            <h4>所有面积</h4>
                            <div class="con-cate-list">
                                <? if($_catalogs=cls_Parse::Tag(array('ename'=>"catalogs",'tclass'=>"catalogs",'limits'=>15,'listby'=>"co6",'cainherit'=>3,))){foreach($_catalogs as $v){ cls_Parse::Active($v);?><a href="<?=$v['indexurl']?>" ><?=$v['title']?></a><? cls_Parse::ActiveBack();} unset($_catalogs,$v);?><? }else{  } ?>
                            </div>
                        </div>
                    </dd>
                    <dd class="last">
                        <div class="con-cate">
                            <h4><i class="ico08 r fcg">&#xe68f;</i>房龄</h4>
                            <div class="con-cate-list">
                                <? if($_catalogs=cls_Parse::Tag(array('ename'=>"catalogs",'tclass'=>"catalogs",'limits'=>15,'listby'=>"co34",))){foreach($_catalogs as $v){ cls_Parse::Active($v);?><a href="<?=$cms_abs?>index.php?caid=3&ccid34=<?=$v['ccid']?>" ><?=$v['title']?></a><? cls_Parse::ActiveBack();} unset($_catalogs,$v);?><? }else{  } ?>
                            </div>
                        </div>
                        <div class="con-cate-more">
                            <h4>所有房龄</h4>
                            <div class="con-cate-list">
                                <? if($_catalogs=cls_Parse::Tag(array('ename'=>"catalogs",'tclass'=>"catalogs",'limits'=>15,'listby'=>"co34",))){foreach($_catalogs as $v){ cls_Parse::Active($v);?><a href="<?=$cms_abs?>index.php?caid=3&ccid34=<?=$v['ccid']?>" ><?=$v['title']?></a><? cls_Parse::ActiveBack();} unset($_catalogs,$v);?><? }else{  } ?>
                            </div>
                        </div>
                    </dd>
                </dl>
                <dl class="condition dn">
                    <dd>
                        <div class="con-cate">
                            <h4><i class="ico08 r fcg">&#xe68f;</i>区域</h4>
                            <div class="con-cate-list">
                                <? if($_catalogs=cls_Parse::Tag(array('ename'=>"catalogs",'tclass'=>"catalogs",'limits'=>15,'listby'=>"co1",'cainherit'=>4,))){foreach($_catalogs as $v){ cls_Parse::Active($v);?><a href="<?=$v['indexurl']?>" ><?=$v['title']?></a><? cls_Parse::ActiveBack();} unset($_catalogs,$v);?><? }else{  } ?>
                            </div>
                        </div>
                        <div class="con-cate-more">
                            <h4>所有区域</h4>
                            <div class="con-cate-list">
                                <? if($_catalogs=cls_Parse::Tag(array('ename'=>"catalogs",'tclass'=>"catalogs",'limits'=>15,'listby'=>"co1",'cainherit'=>4,))){foreach($_catalogs as $v){ cls_Parse::Active($v);?><a href="<?=$v['indexurl']?>" ><?=$v['title']?></a><? cls_Parse::ActiveBack();} unset($_catalogs,$v);?><? }else{  } ?>
                            </div>
                        </div>
                    </dd>
                    <dd>
                        <div class="con-cate">
                            <h4><i class="ico08 r fcg">&#xe68f;</i>租金</h4>
                            <div class="con-cate-list">
                                <? if($_catalogs=cls_Parse::Tag(array('ename'=>"catalogs",'tclass'=>"catalogs",'listby'=>"co5",'cainherit'=>4,))){foreach($_catalogs as $v){ cls_Parse::Active($v);?><a href="<?=$v['indexurl']?>" ><?=$v['title']?></a><? cls_Parse::ActiveBack();} unset($_catalogs,$v);?><? }else{  } ?>
                            </div>
                        </div>
                        <div class="con-cate-more">
                            <h4>所有租金</h4>
                            <div class="con-cate-list">
                                <? if($_catalogs=cls_Parse::Tag(array('ename'=>"catalogs",'tclass'=>"catalogs",'listby'=>"co5",'cainherit'=>4,))){foreach($_catalogs as $v){ cls_Parse::Active($v);?><a href="<?=$v['indexurl']?>" ><?=$v['title']?></a><? cls_Parse::ActiveBack();} unset($_catalogs,$v);?><? }else{  } ?>
                            </div>
                        </div>
                    </dd>
                    <dd>
                        <div class="con-cate">
                            <h4><i class="ico08 r fcg">&#xe68f;</i>户型</h4>
                            <div class="con-cate-list">
                               <?php
                                    foreach (u_field_by(2, 'shi') as $key => $value) {
                                        echo '<a href="'.$cms_abs.'index.php?caid=4&shi='.$key.'" >'.$value.'</a>';
                                    }
                                ?>
                            </div>
                        </div>
                        <div class="con-cate-more">
                            <h4>所有户型</h4>
                            <div class="con-cate-list">
                               <?php
                                    foreach (u_field_by(2, 'shi') as $key => $value) {
                                        echo '<a href="'.$cms_abs.'index.php?caid=4&shi='.$key.'" >'.$value.'</a>';
                                    }
                                ?>
                            </div>
                        </div>
                    </dd>
                    <dd class="last">
                        <div class="con-cate">
                            <h4><i class="ico08 r fcg">&#xe68f;</i>方式 / 装修</h4>
                            <div class="con-cate-list">
                               <?php
                                    foreach (u_field_by(2, 'zlfs') as $key => $value) {
                                        echo '<a href="'.$cms_abs.'index.php?caid=4&zlfs='.$key.'" >'.$value.'</a>';
                                    }
                                ?>
                               <?php
                                    foreach (u_field_by(2, 'zxcd') as $key => $value) {
                                        echo '<a href="'.$cms_abs.'index.php?caid=4&zxcd='.$key.'" >'.$value.'</a>';
                                    }
                                ?>
                            </div>
                        </div>
                        <div class="con-cate-more">
                            <h4>所有方式</h4>
                            <div class="con-cate-list mb20">
                               <?php
                                    foreach (u_field_by(2, 'zlfs') as $key => $value) {
                                        echo '<a href="'.$cms_abs.'index.php?caid=4&zlfs='.$key.'" >'.$value.'</a>';
                                    }
                                ?>
                            </div>
                            <h4>所有装修</h4>
                            <div class="con-cate-list">
                               <?php
                                    foreach (u_field_by(2, 'zxcd') as $key => $value) {
                                        echo '<a href="'.$cms_abs.'index.php?caid=4&zxcd='.$key.'" >'.$value.'</a>';
                                    }
                                ?>
                            </div>
                        </div>
                    </dd>
                </dl>
                <div class="col-tools1 clearfix">
                    <a href="<?=$cms_abs?>info.php?fid=111&action=chushou" rel="nofollow"><i class="ico08 mr5">&#xe635;</i>发布二手</a>
                    <a href="<?=$cms_abs?>info.php?fid=111&action=chuzu" rel="nofollow"><i class="ico08 mr5">&#xe632;</i>发布出租</a>
                    <a href="<?=$cms_abs?>info.php?fid=112&action=qiugou" rel="nofollow"><i class="ico08 mr5">&#xe62f;</i>发布求购</a>
                    <a href="<?=$cms_abs?>info.php?fid=112&action=qiuzu" rel="nofollow"><i class="ico08 mr5">&#xe626;</i>发布求租</a>
                    <a href="<?=$cms_abs?>info.php?fid=101&chid=3" rel="nofollow"><i class="ico08 mr5">&#xf15b;</i>委托出售</a>
                    <a href="<?=$cms_abs?>info.php?fid=101&chid=2" rel="nofollow"><i class="ico08 mr5">&#xf0f6;</i>委托出租</a>
                    <div class="blank0"></div>
                </div>
            </div>
            <div class="w640 l">
                <ul class="plist1 plist-mod clearfix">
                    <? if($_pushs=cls_Parse::Tag(array('ename'=>"pushs",'tclass'=>"pushs",'paid'=>"push_18",'limits'=>6,'ttl'=>600,))){foreach($_pushs as $v){ cls_Parse::Active($v);?>
                     <li>
                         <a class="img" href="<?=$v['url']?>"><img alt="<?=$v['subject']?>" width="193" height="140" src="<? if($u=cls_Parse::Tag(array('ename'=>"image",'tclass'=>"image",'tname'=>"$v[thumb]",'maxwidth'=>193,'maxheight'=>140,'thumb'=>1,'padding'=>1,))){cls_Parse::Active($u);?><?=$u['url_s']?><? cls_Parse::ActiveBack();} unset($u);?>"/></a>
                         <h4><a href="<?=$v['url']?>"><?=$v['subject']?></a></h4>
                         <h5><span class="r"><? if($a=cls_Parse::Tag(array('ename'=>"cnode",'tclass'=>"cnode",'listby'=>"co1",'cosource1'=>"$v[classid1]",))){cls_Parse::Active($a);?><?=$a['title']?><? cls_Parse::ActiveBack();} unset($a);?></span><? if($v['zj']) { ?><span class="fz24 fcr"><?=$v['zj']?></span>万<? } else { ?><span class="fz24 fcr">面议</span><? } ?></h5>
                         <h5 class="tip"><?=$v['lpmc']?></h5>
                     </li>
                    <? cls_Parse::ActiveBack();} unset($_pushs,$v);?><? }else{  } ?>
                </ul><!-- /plist -->
                <ul class="plist1 plist-mod clearfix dn">
                    <? if($_pushs=cls_Parse::Tag(array('ename'=>"pushs",'tclass'=>"pushs",'paid'=>"push_20",'limits'=>6,'ttl'=>600,))){foreach($_pushs as $v){ cls_Parse::Active($v);?>
                    <li>
                        <a class="img" href="<?=$v['url']?>"><img alt="<?=$v['subject']?>" width="193" height="140" src="<? if($u=cls_Parse::Tag(array('ename'=>"image",'tclass'=>"image",'tname'=>"$v[thumb]",'maxwidth'=>193,'maxheight'=>140,'thumb'=>1,'padding'=>1,))){cls_Parse::Active($u);?><?=$u['url_s']?><? cls_Parse::ActiveBack();} unset($u);?>"/></a>
                        <h4><a href="<?=$v['url']?>"><?=$v['subject']?></a></h4>
                        <h5><span class="r"><? if($a=cls_Parse::Tag(array('ename'=>"cnode",'tclass'=>"cnode",'listby'=>"co1",'cosource1'=>"$v[classid1]",))){cls_Parse::Active($a);?><?=$a['title']?><? cls_Parse::ActiveBack();} unset($a);?></span><? if($v['zj']) { ?><span class="fz24 fcr"><?=$v['zj']?></span>元/月<? } else { ?><span class="fz24 fcr">面议</span><? } ?>
                        </h5>
                        <h5 class="tip"><?=$v['lpmc']?></h5>
                    </li>
                    <? cls_Parse::ActiveBack();} unset($_pushs,$v);?><? }else{  } ?>
                </ul>
                <ul class="plist1 plist-mod clearfix dn">
                    <? if($_pushs=cls_Parse::Tag(array('ename'=>"pushs",'tclass'=>"pushs",'paid'=>"push_129",'limits'=>6,'ttl'=>600,))){foreach($_pushs as $v){ cls_Parse::Active($v);?>
                    <? if($a=cls_Parse::Tag(array('ename'=>"archive",'tclass'=>"archive",'chid'=>4,'id'=>"$v[fromid]",'detail'=>1,))){cls_Parse::Active($a);?>
                    <li>
                        <a class="img" href="<?=$a['arcurl']?>"><img alt="<?=$a['subject']?>" width="193" height="140" src="<? if($u=cls_Parse::Tag(array('ename'=>"image",'tclass'=>"image",'tname'=>"$a[thumb]",'maxwidth'=>193,'maxheight'=>140,'thumb'=>1,'padding'=>1,))){cls_Parse::Active($u);?><?=$u['url_s']?><? cls_Parse::ActiveBack();} unset($u);?>"/></a>
                        <h4><a href="<?=$a['arcurl']?>"><?=$a['subject']?></a></h4>
                        <h5><span class="r"><?=$a['ccid1title']?></span><span class="fz24 fcr"><?=$a['dj']?></span>元/m&sup2;<? if($a['price_trend']==0) { ?><i class="ico08 ico-<?=$a['price_trend']?>">&#xe62d;</i><? } elseif($a['price_trend']==1) { ?><i class="ico08 ico-<?=$a['price_trend']?>">&#xe62c;</i><? } elseif($a['price_trend']==2) { ?><i class="ico08 ico-<?=$a['price_trend']?>">&#xe62b;</i><? } ?></h5>
                        <h5 class="tip"><i class="l">售:<span class="fcr fz16"><?=$a['lpesfsl']?></span>套</i><i class="r">租:<span class="fcr fz16"><?=$a['lpczsl']?></span>套</i></h5>
                    </li>
                    <? cls_Parse::ActiveBack();} unset($a);?>
                    <? cls_Parse::ActiveBack();} unset($_pushs,$v);?><? }else{  } ?>
                </ul>
            </div>
            <div class="w270 r pt20">
                <div class="plist4">
                    <ul class="clearfix">
                        <? if($_pushs=cls_Parse::Tag(array('ename'=>"pushs",'tclass'=>"pushs",'paid'=>"push_tjjjr",'limits'=>5,'ttl'=>600,))){foreach($_pushs as $v){ cls_Parse::Active($v);?>
                        <li>
                             <a href="<?=$v['url']?>"><img width="56" height="56" alt="<?=$v['subject']?>" src="<? if($u=cls_Parse::Tag(array('ename'=>"image",'tclass'=>"image",'tname'=>"$v[thumb]",'padding'=>1,))){cls_Parse::Active($u);?><?=$u['url']?><? cls_Parse::ActiveBack();} unset($u);?>"/></a>
                             <p>
                                <a href="<?=$v['url']?>" class="fcr"><?=$v['subject']?></a><br />
                                <? if($a=cls_Parse::Tag(array('ename'=>"member",'tclass'=>"member",'chids'=>2,'chsource'=>2,'id'=>"$v[fromid]",'detail'=>1,))){cls_Parse::Active($a);?>
                                手机:<?=$a['lxdh']?> <br/>
                                售<span class="fcr"><? if($b=cls_Parse::Tag(array('ename'=>"acount",'tclass'=>"acount",'chsource'=>2,'chids'=>3,'space'=>1,'validperiod'=>1,))){cls_Parse::Active($b);?><? if($b['counts']) { ?><?=$b['counts']?><? } else { ?>0<? } ?><? cls_Parse::ActiveBack();} unset($b);?></span>套,租<span class="fcr"><? if($b=cls_Parse::Tag(array('ename'=>"acount",'tclass'=>"acount",'chsource'=>2,'chids'=>2,'space'=>1,'validperiod'=>1,))){cls_Parse::Active($b);?><? if($b['counts']) { ?><?=$b['counts']?><? } else { ?>0<? } ?><? cls_Parse::ActiveBack();} unset($b);?></span>套
                                <? cls_Parse::ActiveBack();} unset($a);?>
                             </p>
                         </li>
                        <? cls_Parse::ActiveBack();} unset($_pushs,$v);?><? }else{  } ?>
                    </ul>
                    <a class="more" href="<? if($v=cls_Parse::Tag(array('ename'=>"jjrjd",'tclass'=>"mcnode",'cnsource'=>"mcnid",'cnid'=>14,))){cls_Parse::Active($v);?><?=$v['mcnurl']?><? cls_Parse::ActiveBack();} unset($v);?>">更多经纪人</a>
                </div><!-- /plist -->
            </div>
        </div>
        <div class="blank20"></div>
        <ul class="plist2 plist-mod clearfix">
            <? if($_pushs=cls_Parse::Tag(array('ename'=>"pushs",'tclass'=>"pushs",'paid'=>"push_17",'limits'=>7,'ttl'=>600,))){foreach($_pushs as $v){ cls_Parse::Active($v);?>
             <li>
                 <a class="img" href="<?=$v['url']?>" target="_blank"><img alt="<?=$v['subject']?>" width="150" height="70" src="<? if($u=cls_Parse::Tag(array('ename'=>"image",'tclass'=>"image",'tname'=>"$v[thumb]",'thumb'=>1,'padding'=>1,))){cls_Parse::Active($u);?><?=$u['url']?><? cls_Parse::ActiveBack();} unset($u);?>"/></a>
             </li>
            <? cls_Parse::ActiveBack();} unset($_pushs,$v);?><? }else{  } ?>
        </ul><!-- /plist -->
        <div class="blank20"></div>
        <div class="scroll2 jqDuang jqDuang-mod" data-effect="leftLoop" data-steps="4" data-visible="4" data-prevbtn=".prev" data-nextbtn=".next">
            <div class="plist3 plist-mod">
                <ul class="clearfix">
                    <? if($_pushs=cls_Parse::Tag(array('ename'=>"pushs",'tclass'=>"pushs",'paid'=>"push_137",'limits'=>20,'ttl'=>600,))){foreach($_pushs as $v){ cls_Parse::Active($v);?>
                     <li>
                         <a class="img" href="<?=$v['url']?>"><img alt="<?=$v['subject']?>" width="285" height="175" src="<? if($u=cls_Parse::Tag(array('ename'=>"image",'tclass'=>"image",'tname'=>"$v[thumb]",'maxwidth'=>285,'maxheight'=>175,'thumb'=>1,'padding'=>1,))){cls_Parse::Active($u);?><?=$u['url_s']?><? cls_Parse::ActiveBack();} unset($u);?>"/></a>
                         <h4><a href="<?=$v['url']?>"><?=$v['subject']?></a></h4>
                     </li>
                    <? cls_Parse::ActiveBack();} unset($_pushs,$v);?><? }else{  } ?>
                </ul>
            </div>
            <i class="ico08 prev">&#xe68c;</i>
            <i class="ico08 next">&#xe68f;</i>
        </div><!-- /plist -->
        <div class="blank20"></div>
    </div>
    <div class="link">
        <div class="link-hd wrap clearfix">
            <h2>友情链接</h2>
        </div>
        <div class="link-bd">
            <div class="wrap clearfix ptb20">
                <? if($_farchives=cls_Parse::Tag(array('ename'=>"farchives",'tclass'=>"farchives",'limits'=>50,'validperiod'=>1,'casource'=>17,'orderstr'=>"a.vieworder ASC",))){foreach($_farchives as $v){ cls_Parse::Active($v);?><a href="<?=$v['link']?>" ><?=$v['subject']?></a><? cls_Parse::ActiveBack();} unset($_farchives,$v);?><? }else{  } ?>
            </div>
        </div>
    </div><!-- /link -->
    <div class="blank1"></div>
<div id="login-wrap" class="w420 modal" data-head="会员登录">
    <form class="jqValidate" data-tipmode="#tipinfo" onsubmit="return _08Login(this)">
    	<div style="height:270px;" class="p0-10">
            <div class="wrap-pc" data-title="账号登录">
                <ul class="form form-lg form-control bdrs form-fz16">
                    <li>
                        <div class="blank20 mt-15">
                            <span id="tipinfo"></span>
                        </div>
                        <input type="text" placeholder="用户名" class="txt user" data-type="*3-15" size="25" id="username" name="username" value=""/>
                    </li>
                    <li><input type="password" placeholder="密码" class="txt psd" size="25" id="password" name="password" value="" data-type="*"/></li>
                    <li class="reg-wrap" data-regcode="login">
                        <input type="text" placeholder="验证码" name="regcode" class="txt regcode" placeholder="" value=""/>
                    </li>
                    <li>
                        <input type="submit" class="btnok btn" name="cmslogin"  value="立即登录" />
                    </li>
                </ul>
            </div><!-- /普通账号登录 -->
            <div class="wrap-wx dn tc por" data-title="微信登录" style="padding-top:20px">
                <h3 class="mb10">手机微信扫描，安全登录</h3>
                <img class="wx-img" width="150" height="150"> <br/>
                <span id="wx-tag-tip" class="fcg mr10">使用帮助</span>
                <span id="wx-refresh"><i class="ico08 mr3 fco">&#xf025;</i>重新加载</span>
                <img id="wx-login-tip" class="wx-login-tip" src="<?=$tplurl?>images/weixin_logintip.png">
            </div><!-- /微信登录 -->
        </div>
        <div class="p0-10">
            其它登录: <a id="qqlogin" onclick="OtherWebSiteLogin('qq', 600, 470);" href="javascript:;" title="QQ帐号登录"  class="ico08 fz22 fcb mr5" target="_self">&#xf1d6;</a><a id="sinalogin" onclick="OtherWebSiteLogin('sina', 600, 400);" href="javascript:;" title="新浪微博帐号登录" class="ico08 fz22 fcr" target="_self">&#xf18a;</a><a class="mt5" href="<?=$cms_abs?>tools/lostpwd.php?" target="_blank"><i class="ico08 fco ml10">&#xf059;</i>忘记密码</a>
        </div>
    </form>
    <i id="ico-login" class="ico-login" title="微信登录"></i>
</div><!-- /登录框 -->

<div class="fixed-right jqFixed jqScrollspy" data-fixed="600" data-css='{"right": 20, "bottom": 50, "display":"none"}' data-offset="140">
    <? if(@$pagetype=='index') { ?>
    <a href="#thlp" target="_top" ><span>特惠楼盘</span><i class="ico08">&#xe73c;</i></a><a href="#xptj" target="_top" ><span>新盘推荐</span><i class="ico08">&#xe617;</i></a><a href="#sydc" target="_top" ><span>商业地产</span><i class="ico08">&#xf0fb;</i></a><a href="#escz" target="_top" ><span>二手出租</span><i class="ico08">&#xe637;</i></a><? } ?><a href="body" target="_top" class="go-top" ><span>返回顶部</span><i class="ico08">&#xe68e;</i></a>
</div>

<script type="text/javascript">window.jQuery || document.write('<script src="<?=$tplurl?>js/jquery.js"><\/script>');</script>
<script type="text/javascript">$.fn.jqModal || document.write('<script src="<?=$tplurl?>js/jqmodal.js?<?=$user_version?>"><\/script>');</script>
<script type="text/javascript">$.fn.jqValidate || document.write('<script src="<?=$tplurl?>js/jqvalidate.js?<?=$user_version?>"><\/script>');</script>
<script type="text/javascript">$.fn.jqScrollspy || document.write('<script src="<?=$tplurl?>js/jqscrollspy.js?<?=$user_version?>"><\/script>');</script>
<script type="text/javascript">$.fn.jqFixed || document.write('<script src="<?=$tplurl?>js/jqfixed.js?<?=$user_version?>"><\/script>');</script>
<script type="text/javascript" src="<?=$tplurl?>js/common.js?<?=$user_version?>"></script>

<script type="text/javascript">
<?php
// 快捷登录模块，如果所有外站登录都关闭的情况下隐藏该模块
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
		//第三方登录
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
				alert('该登录功能已经关闭！');
				return false;
			} else
			{
				childWindow = window.open(urls[type], type, "width=" + width + ",height=" + height + ",left=" + ((window.screen.availWidth - width) / 2) + ",top=" + ((window.screen.availHeight - height) / 2));
			}
		}
<?php
  } // 快捷登录模块结束
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
                    <dt>扫二维码</dt>
                    <dd class="tc">
            			<? if($_kfianddw=cls_Parse::Tag(array('ename'=>"kfianddw",'tclass'=>"farchives",'casource'=>"fcatalog173",'limits'=>1,'validperiod'=>1,'orderstr'=>"a.vieworder DESC",))){foreach($_kfianddw as $v){ cls_Parse::Active($v);?><? if($x=cls_Parse::Tag(array('ename'=>"androidxz",'tclass'=>"file",'tname'=>"$v[andrdown]",))){cls_Parse::Active($x);?>
                        <div class="l mr30">
                        	<img src="<?=cls_tpl::QRcodeImage($x['url'])?>" width="100" height="100" /><br/>Android下载
                        </div>
                        <? cls_Parse::ActiveBack();} unset($x);?><? cls_Parse::ActiveBack();} unset($_kfianddw,$v);?><? }else{  } ?>
                        <? if($_kfiphdw=cls_Parse::Tag(array('ename'=>"kfiphdw",'tclass'=>"farchives",'casource'=>"fcatalog173",'limits'=>1,'validperiod'=>1,'orderstr'=>"a.vieworder DESC",))){foreach($_kfiphdw as $v){ cls_Parse::Active($v);?><? if($x=cls_Parse::Tag(array('ename'=>"iphonexz",'tclass'=>"file",'tname'=>"$v[iphdown]",))){cls_Parse::Active($x);?>
						<div class="l mr30">
                        	<img src="<?=cls_tpl::QRcodeImage(cls_url::view_url('info.php?fid=113'))?>" width="100" height="100" /><br/>iphone下载
                        </div>
                        <? cls_Parse::ActiveBack();} unset($x);?><? cls_Parse::ActiveBack();} unset($_kfiphdw,$v);?><? }else{  } ?>
                        <div class="l">
							<img alt="扫一扫,关注微信" <? if($u=cls_Parse::Tag(array('ename'=>"image",'tclass'=>"image",'tname'=>"$weixin_qrcode",'maxwidth'=>100,'maxheight'=>100,))){cls_Parse::Active($u);?> src="<?=$u['url']?>"  height="100" width="100" <? cls_Parse::ActiveBack();} unset($u);?>/><br/>
							扫一扫,关注微信
                        </div>
                    </dd>
                </dl>
            </div>
            <div class="webinfo">
                <a href="<?=cls_Parse::Tag(array('ename'=>"wzdt",'tclass'=>"freeurl",'fid'=>9,))?><??>" target="_blank">网站地图</a><? if($_wzdbcj=cls_Parse::Tag(array('ename'=>"wzdbcj",'tclass'=>"farchives",'limits'=>1000,'casource'=>26,'orderstr'=>"a.vieworder ASC",))){foreach($_wzdbcj as $v){ cls_Parse::Active($v);?>|<a href="<?=$v['arcurl']?>" target="_blank" rel="nofollow"><?=$v['subject']?></a><? cls_Parse::ActiveBack();} unset($_wzdbcj,$v);?><? }else{  } ?>|<a href="<?=cls_Parse::Tag(array('ename'=>"freeurl",'tclass'=>"freeurl",'fid'=>102,))?><??>" target="_blank" rel="nofollow">网站提问</a>|<a href="<? if($v=cls_Parse::Tag(array('ename'=>"bzzxurl",'tclass'=>"cnode",'listby'=>"ca",'casource'=>561,))){cls_Parse::Active($v);?><?=$v['indexurl']?><? cls_Parse::ActiveBack();} unset($v);?>" target="_blank">帮助中心</a>|<a href="<?=cls_Parse::Tag(array('ename'=>"kfturl",'tclass'=>"freeurl",'fid'=>109,))?><??>" target="_blank">手机版</a>
            </div>
            <div class="copyright">
		<center><div style=line-height:21px>资源提供：<a href=http://www.0762vip.com target=_blank><font color=yellow>优源精品社区</font></a>
<br>&nbsp;
<a href=http://www.0762vip.com target=_blank>优源精品社区</a> | <a href=http://vip.0762vip.com target=_blank>极品商业源码</a> | <a href=http://idc.0762vip.com target=_blank>优源精品社区空间、域名</a> | <a href=http://vip.0762vip.com/templates/Korea/ target=_blank>90G韩国豪华商业模版</a> | <a href=http://tool.0762vip.com/ target=_blank>站长工具箱</a>
<br>
更多精品商业资源，就在<a href=http://www.0762vip.com target=_blank>优源精品社区</a></font>
</div></center>

			</div>
        </div>
    </div><!-- /footer -->
    <script type="text/javascript" src="<?=$tplurl?>js/jqduang.js?2015-8-26"></script>
    <?php @include_once(M_ROOT."./template/$templatedir/_demo.php"); ?>
</body>
</html>