<?php echo '<?';?>xml version="1.0" encoding="<?=$mcharset?>"<?php echo '?>';?>
<?php 
defined('IN_MOBILE') || define('IN_MOBILE', TRUE); 
$is_bz = cls_env::getBaseIncConfigs('is_bz');
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:mobile="http://www.baidu.com/schemas/sitemap-mobile/1/">
    <? if($_baidu_zixun=cls_Parse::Tag(array('ename'=>"baidu_zixun",'tclass'=>"archives",'chids'=>2,'chsource'=>2,'limits'=>10,'isfunc'=>1,'wherestr'=>"baidu_push(0)",))){foreach($_baidu_zixun as $v){ cls_Parse::Active($v);?>
    <url>
        <loc><?=htmlspecialchars($v['arcurl'])?></loc>
        <mobile:mobile type="mobile"/>
        <lastmod><?=date('Y-m-d',$v['updatedate'])?></lastmod>
        <changefreq>daily</changefreq> 
        <priority>0.8</priority>
    </url>
    <? cls_Parse::ActiveBack();} unset($_baidu_zixun,$v);?><? }else{  } ?>
    <? if(empty($is_bz) && !cmod('moder')) { ?>
    <? if($_baidu_old=cls_Parse::Tag(array('ename'=>"baidu_old",'tclass'=>"members",'chsource'=>2,'chids'=>3,'limits'=>10,'isfunc'=>1,'wherestr'=>"baidu_push(1)",))){foreach($_baidu_old as $v){ cls_Parse::Active($v);?>
    <url>
        <loc><?=htmlspecialchars($mobileurl."index.php?caid=551&addno=5&mid=$v[mid]")?></loc>
        <mobile:mobile type="mobile"/>
        <lastmod><?=date('Y-m-d',$v['regdate'])?></lastmod>
        <changefreq>daily</changefreq>
        <priority>0.7</priority>
    </url>
    <? cls_Parse::ActiveBack();} unset($_baidu_old,$v);?><? }else{  } ?>
    <? } ?>
    <? if($_baidu_new=cls_Parse::Tag(array('ename'=>"baidu_new",'tclass'=>"members",'chsource'=>2,'chids'=>2,'limits'=>10,'isfunc'=>1,'wherestr'=>"baidu_push(1)",))){foreach($_baidu_new as $v){ cls_Parse::Active($v);?>
    <url>
        <loc><?=htmlspecialchars($mobileurl."index.php?caid=1&addno=7&mid=$v[mid]")?></loc>
        <mobile:mobile type="mobile"/>
        <lastmod><?=date('Y-m-d',$v['regdate'])?></lastmod>
        <changefreq>daily</changefreq>
        <priority>0.8</priority>
    </url>
    <? cls_Parse::ActiveBack();} unset($_baidu_new,$v);?><? }else{  } ?>
</urlset>
