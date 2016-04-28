<?php echo '<?';?>xml version="1.0" encoding="<?=$mcharset?>"<?php echo '?>';?> 
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"> 
    <url> 
        <loc><?=htmlspecialchars($cms_abs)?></loc> 
        <lastmod><?=date('Y-m-d')?></lastmod> 
        <changefreq>daily</changefreq> 
        <priority>1.0</priority> 
    </url> 
    <? if($_gg_zixun=cls_Parse::Tag(array('ename'=>"gg_zixun",'tclass'=>"archives",'limits'=>100,'chsource'=>2,'chids'=>1,))){foreach($_gg_zixun as $v){ cls_Parse::Active($v);?><url> 
        <loc><?=htmlspecialchars($v['arcurl'])?></loc> 
        <lastmod><?=date('Y-m-d',$v['createdate'])?></lastmod> 
        <changefreq>daily</changefreq> 
        <priority>0.8</priority> 
    </url> <? cls_Parse::ActiveBack();} unset($_gg_zixun,$v);?><? }else{  } ?>
    <? if($_gg_house=cls_Parse::Tag(array('ename'=>"gg_house",'tclass'=>"archives",'limits'=>50,'chsource'=>2,'chids'=>4,))){foreach($_gg_house as $v){ cls_Parse::Active($v);?><url> 
        <loc><?=htmlspecialchars($v['arcurl'])?></loc> 
        <lastmod><?=date('Y-m-d',$v['createdate'])?></lastmod> 
        <changefreq>daily</changefreq> 
        <priority>0.8</priority> 
    </url> <? cls_Parse::ActiveBack();} unset($_gg_house,$v);?><? }else{  } ?>

    <? if($_baidu_house=cls_Parse::Tag(array('ename'=>"baidu_house",'tclass'=>"archives",'limits'=>10,'chsource'=>2,'chids'=>110,))){foreach($_baidu_house as $v){ cls_Parse::Active($v);?>
    <url>
        <loc><?=htmlspecialchars($v['arcurl'])?></loc>
        <lastmod><?=date('Y-m-d',$v['updatedate'])?></lastmod>
        <changefreq>daily</changefreq>
        <priority>0.8</priority>
    </url>
    <? cls_Parse::ActiveBack();} unset($_baidu_house,$v);?><? }else{  } ?>

    <? if($_baidu_house=cls_Parse::Tag(array('ename'=>"baidu_house",'tclass'=>"archives",'limits'=>10,'chsource'=>2,'chids'=>117,))){foreach($_baidu_house as $v){ cls_Parse::Active($v);?>
    <url>
        <loc><?=htmlspecialchars($v['arcurl'])?></loc>
        <lastmod><?=date('Y-m-d',$v['updatedate'])?></lastmod>
        <changefreq>daily</changefreq>
        <priority>0.8</priority>
    </url>
    <? cls_Parse::ActiveBack();} unset($_baidu_house,$v);?><? }else{  } ?>
    <? if($_gg_ersou=cls_Parse::Tag(array('ename'=>"gg_ersou",'tclass'=>"archives",'limits'=>50,'chsource'=>2,'chids'=>3,))){foreach($_gg_ersou as $v){ cls_Parse::Active($v);?><url> 
        <loc><?=htmlspecialchars($v['arcurl'])?></loc> 
        <lastmod><?=date('Y-m-d',$v['createdate'])?></lastmod> 
        <changefreq>daily</changefreq> 
        <priority>0.8</priority> 
    </url> <? cls_Parse::ActiveBack();} unset($_gg_ersou,$v);?><? }else{  } ?>
    <? if($_gg_chuzu=cls_Parse::Tag(array('ename'=>"gg_chuzu",'tclass'=>"archives",'limits'=>50,'chsource'=>2,'chids'=>2,))){foreach($_gg_chuzu as $v){ cls_Parse::Active($v);?><url> 
        <loc><?=htmlspecialchars($v['arcurl'])?></loc> 
        <lastmod><?=date('Y-m-d',$v['createdate'])?></lastmod> 
        <changefreq>daily</changefreq> 
        <priority>0.8</priority> 
    </url> <? cls_Parse::ActiveBack();} unset($_gg_chuzu,$v);?><? }else{  } ?>
    <? if($_gg_huxin=cls_Parse::Tag(array('ename'=>"gg_huxin",'tclass'=>"archives",'limits'=>50,'chsource'=>2,'chids'=>11,))){foreach($_gg_huxin as $v){ cls_Parse::Active($v);?><url> 
        <loc><?=htmlspecialchars($v['arcurl'])?></loc> 
        <lastmod><?=date('Y-m-d',$v['createdate'])?></lastmod> 
        <changefreq>daily</changefreq> 
        <priority>0.8</priority> 
    </url> <? cls_Parse::ActiveBack();} unset($_gg_huxin,$v);?><? }else{  } ?>
    <? if($_baidu_huixing=cls_Parse::Tag(array('ename'=>"baidu_huixing",'tclass'=>"archives",'limits'=>30,'chsource'=>2,'chids'=>7,))){foreach($_baidu_huixing as $v){ cls_Parse::Active($v);?>
    <url>
        <loc><?=htmlspecialchars($v['arcurl'])?></loc>
        <lastmod><?=date('Y-m-d',$v['updatedate'])?></lastmod>
        <changefreq>daily</changefreq>
        <priority>0.8</priority>
    </url>
    <? cls_Parse::ActiveBack();} unset($_baidu_huixing,$v);?><? }else{  } ?>
    <? if($_gg_video=cls_Parse::Tag(array('ename'=>"gg_video",'tclass'=>"archives",'limits'=>50,'chsource'=>2,'chids'=>12,))){foreach($_gg_video as $v){ cls_Parse::Active($v);?><url> 
        <loc><?=htmlspecialchars($v['arcurl'])?></loc> 
        <lastmod><?=date('Y-m-d',$v['createdate'])?></lastmod> 
        <changefreq>daily</changefreq> 
        <priority>0.8</priority> 
    </url> <? cls_Parse::ActiveBack();} unset($_gg_video,$v);?><? }else{  } ?>
    <? if($_gg_zuanti=cls_Parse::Tag(array('ename'=>"gg_zuanti",'tclass'=>"archives",'limits'=>50,'chsource'=>2,'chids'=>14,))){foreach($_gg_zuanti as $v){ cls_Parse::Active($v);?><url> 
        <loc><?=htmlspecialchars($v['arcurl'])?></loc> 
        <lastmod><?=date('Y-m-d',$v['createdate'])?></lastmod> 
        <changefreq>daily</changefreq> 
        <priority>0.8</priority> 
    </url> <? cls_Parse::ActiveBack();} unset($_gg_zuanti,$v);?><? }else{  } ?>
    <? if($_baidu_zuanti=cls_Parse::Tag(array('ename'=>"baidu_zuanti",'tclass'=>"archives",'limits'=>10,'chsource'=>2,'chids'=>108,))){foreach($_baidu_zuanti as $v){ cls_Parse::Active($v);?>
    <url>
        <loc><?=htmlspecialchars($v['arcurl'])?></loc>
        <lastmod><?=date('Y-m-d',$v['updatedate'])?></lastmod>
        <changefreq>daily</changefreq>
        <priority>0.8</priority>
    </url>
    <? cls_Parse::ActiveBack();} unset($_baidu_zuanti,$v);?><? }else{  } ?>
    <? if($_gg_sjs=cls_Parse::Tag(array('ename'=>"gg_sjs",'tclass'=>"archives",'limits'=>50,'chsource'=>2,'chids'=>101,))){foreach($_gg_sjs as $v){ cls_Parse::Active($v);?><url> 
        <loc><?=htmlspecialchars($v['marcurl'])?></loc> 
        <lastmod><?=date('Y-m-d',$v['createdate'])?></lastmod> 
        <changefreq>daily</changefreq> 
        <priority>0.8</priority> 
    </url> <? cls_Parse::ActiveBack();} unset($_gg_sjs,$v);?><? }else{  } ?>
    <? if($_gg_zxal=cls_Parse::Tag(array('ename'=>"gg_zxal",'tclass'=>"archives",'limits'=>50,'chsource'=>2,'chids'=>102,))){foreach($_gg_zxal as $v){ cls_Parse::Active($v);?><url> 
        <loc><?=htmlspecialchars($v['marcurl'])?></loc> 
        <lastmod><?=date('Y-m-d',$v['createdate'])?></lastmod> 
        <changefreq>daily</changefreq> 
        <priority>0.8</priority> 
    </url> <? cls_Parse::ActiveBack();} unset($_gg_zxal,$v);?><? }else{  } ?>
    <? if($_gg_sp=cls_Parse::Tag(array('ename'=>"gg_sp",'tclass'=>"archives",'limits'=>50,'chsource'=>2,'chids'=>103,))){foreach($_gg_sp as $v){ cls_Parse::Active($v);?><url> 
        <loc><?=htmlspecialchars($v['marcurl'])?></loc> 
        <lastmod><?=date('Y-m-d',$v['createdate'])?></lastmod> 
        <changefreq>daily</changefreq> 
        <priority>0.8</priority> 
    </url> <? cls_Parse::ActiveBack();} unset($_gg_sp,$v);?><? }else{  } ?>
    <? if($_gg_gsdt=cls_Parse::Tag(array('ename'=>"gg_gsdt",'tclass'=>"archives",'limits'=>50,'chsource'=>2,'chids'=>104,))){foreach($_gg_gsdt as $v){ cls_Parse::Active($v);?><url> 
        <loc><?=htmlspecialchars($v['marcurl'])?></loc> 
        <lastmod><?=date('Y-m-d',$v['createdate'])?></lastmod> 
        <changefreq>daily</changefreq> 
        <priority>0.8</priority> 
    </url> <? cls_Parse::ActiveBack();} unset($_gg_gsdt,$v);?><? }else{  } ?>
    <? if($_gg_tg=cls_Parse::Tag(array('ename'=>"gg_tg",'tclass'=>"archives",'limits'=>50,'chsource'=>2,'chids'=>105,))){foreach($_gg_tg as $v){ cls_Parse::Active($v);?><url> 
        <loc><?=htmlspecialchars($v['arcurl'])?></loc> 
        <lastmod><?=date('Y-m-d',$v['createdate'])?></lastmod> 
        <changefreq>daily</changefreq> 
        <priority>0.8</priority> 
    </url> <? cls_Parse::ActiveBack();} unset($_gg_tg,$v);?><? }else{  } ?>
</urlset>