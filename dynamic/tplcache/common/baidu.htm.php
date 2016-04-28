<?php echo '<?';?>xml version="1.0" encoding="<?=$mcharset?>"<?php echo '?>';?>
<urlset>
    <url> 
        <loc><?=htmlspecialchars($cms_abs)?></loc> 
        <lastmod><?=date('Y-m-d')?></lastmod> 
        <changefreq>daily</changefreq> 
        <priority>1.0</priority> 
    </url> 
    <? if($_baidu_zixun=cls_Parse::Tag(array('ename'=>"baidu_zixun",'tclass'=>"archives",'limits'=>100,'chsource'=>2,'chids'=>1,))){foreach($_baidu_zixun as $v){ cls_Parse::Active($v);?>
    <url>
        <loc><?=htmlspecialchars($v['arcurl'])?></loc>
        <lastmod><?=date('Y-m-d',$v['updatedate'])?></lastmod>
        <changefreq>daily</changefreq>
        <priority>0.8</priority>
    </url>
    <? cls_Parse::ActiveBack();} unset($_baidu_zixun,$v);?><? }else{  } ?>
    <? if($_baidu_house=cls_Parse::Tag(array('ename'=>"baidu_house",'tclass'=>"archives",'chids'=>4,'limits'=>50,'detail'=>1,'chsource'=>2,'wherestr'=>"(leixing='0' OR leixing='1')",))){foreach($_baidu_house as $v){ cls_Parse::Active($v);?>
    <url>
        <loc><?=htmlspecialchars($v['arcurl'])?></loc>
        <lastmod><?=date('Y-m-d',$v['updatedate'])?></lastmod>
        <changefreq>daily</changefreq>
        <priority>0.8</priority>
    </url>
    <? cls_Parse::ActiveBack();} unset($_baidu_house,$v);?><? }else{  } ?>
    <? if($_baidu_house=cls_Parse::Tag(array('ename'=>"baidu_house",'tclass'=>"archives",'chids'=>4,'limits'=>50,'detail'=>1,'chsource'=>2,'wherestr'=>"(leixing='0' OR leixing='2')",))){foreach($_baidu_house as $v){ cls_Parse::Active($v);?>
    <url>
        <loc><?=htmlspecialchars($v['arcurl7'])?></loc>
        <lastmod><?=date('Y-m-d',$v['updatedate'])?></lastmod>
        <changefreq>daily</changefreq>
        <priority>0.8</priority>
    </url>
    <? cls_Parse::ActiveBack();} unset($_baidu_house,$v);?><? }else{  } ?>
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
    <? if($_baidu_ersou=cls_Parse::Tag(array('ename'=>"baidu_ersou",'tclass'=>"archives",'limits'=>100,'chsource'=>2,'chids'=>3,))){foreach($_baidu_ersou as $v){ cls_Parse::Active($v);?>
    <url>
        <loc><?=htmlspecialchars($v['arcurl'])?></loc>
        <lastmod><?=date('Y-m-d',$v['updatedate'])?></lastmod>
        <changefreq>daily</changefreq>
        <priority>0.8</priority>
    </url>
    <? cls_Parse::ActiveBack();} unset($_baidu_ersou,$v);?><? }else{  } ?>
    <? if($_baidu_chuzu=cls_Parse::Tag(array('ename'=>"baidu_chuzu",'tclass'=>"archives",'limits'=>100,'chsource'=>2,'chids'=>2,))){foreach($_baidu_chuzu as $v){ cls_Parse::Active($v);?>
    <url>
        <loc><?=htmlspecialchars($v['arcurl'])?></loc>
        <lastmod><?=date('Y-m-d',$v['updatedate'])?></lastmod>
        <changefreq>daily</changefreq>
        <priority>0.8</priority>
    </url>
    <? cls_Parse::ActiveBack();} unset($_baidu_chuzu,$v);?><? }else{  } ?>
    <? if($_baidu_huixing=cls_Parse::Tag(array('ename'=>"baidu_huixing",'tclass'=>"archives",'limits'=>30,'chsource'=>2,'chids'=>11,))){foreach($_baidu_huixing as $v){ cls_Parse::Active($v);?>
    <url>
        <loc><?=htmlspecialchars($v['arcurl'])?></loc>
        <lastmod><?=date('Y-m-d',$v['updatedate'])?></lastmod>
        <changefreq>daily</changefreq>
        <priority>0.8</priority>
    </url>
    <? cls_Parse::ActiveBack();} unset($_baidu_huixing,$v);?><? }else{  } ?>
    <? if($_baidu_huixing=cls_Parse::Tag(array('ename'=>"baidu_huixing",'tclass'=>"archives",'limits'=>30,'chsource'=>2,'chids'=>7,))){foreach($_baidu_huixing as $v){ cls_Parse::Active($v);?>
    <url>
        <loc><?=htmlspecialchars($v['arcurl'])?></loc>
        <lastmod><?=date('Y-m-d',$v['updatedate'])?></lastmod>
        <changefreq>daily</changefreq>
        <priority>0.8</priority>
    </url>
    <? cls_Parse::ActiveBack();} unset($_baidu_huixing,$v);?><? }else{  } ?>
    <? if($_baidu_huixing=cls_Parse::Tag(array('ename'=>"baidu_huixing",'tclass'=>"archives",'limits'=>10,'chsource'=>2,'chids'=>8,))){foreach($_baidu_huixing as $v){ cls_Parse::Active($v);?>
    <url>
        <loc><?=htmlspecialchars($v['arcurl'])?></loc>
        <lastmod><?=date('Y-m-d',$v['updatedate'])?></lastmod>
        <changefreq>daily</changefreq>
        <priority>0.8</priority>
    </url>
    <? cls_Parse::ActiveBack();} unset($_baidu_huixing,$v);?><? }else{  } ?>
    <? if($_baidu_vedio=cls_Parse::Tag(array('ename'=>"baidu_vedio",'tclass'=>"archives",'limits'=>30,'chsource'=>2,'chids'=>12,))){foreach($_baidu_vedio as $v){ cls_Parse::Active($v);?>
    <url>
        <loc><?=htmlspecialchars($v['arcurl'])?></loc>
        <lastmod><?=date('Y-m-d',$v['updatedate'])?></lastmod>
        <changefreq>daily</changefreq>
        <priority>0.8</priority>
    </url>
    <? cls_Parse::ActiveBack();} unset($_baidu_vedio,$v);?><? }else{  } ?>
    <? if($_baidu_zuanti=cls_Parse::Tag(array('ename'=>"baidu_zuanti",'tclass'=>"archives",'limits'=>30,'chsource'=>2,'chids'=>14,))){foreach($_baidu_zuanti as $v){ cls_Parse::Active($v);?>
    <url>
        <loc><?=htmlspecialchars($v['arcurl'])?></loc>
        <lastmod><?=date('Y-m-d',$v['updatedate'])?></lastmod>
        <changefreq>daily</changefreq>
        <priority>0.8</priority>
    </url>
    <? cls_Parse::ActiveBack();} unset($_baidu_zuanti,$v);?><? }else{  } ?>
    <? if($_baidu_zuanti=cls_Parse::Tag(array('ename'=>"baidu_zuanti",'tclass'=>"archives",'limits'=>10,'chsource'=>2,'chids'=>108,))){foreach($_baidu_zuanti as $v){ cls_Parse::Active($v);?>
    <url>
        <loc><?=htmlspecialchars($v['arcurl'])?></loc>
        <lastmod><?=date('Y-m-d',$v['updatedate'])?></lastmod>
        <changefreq>daily</changefreq>
        <priority>0.8</priority>
    </url>
    <? cls_Parse::ActiveBack();} unset($_baidu_zuanti,$v);?><? }else{  } ?>
    <? if($_baidu_sjs=cls_Parse::Tag(array('ename'=>"baidu_sjs",'tclass'=>"archives",'limits'=>30,'chsource'=>2,'chids'=>101,))){foreach($_baidu_sjs as $v){ cls_Parse::Active($v);?>
    <url>
        <loc><?=htmlspecialchars($v['marcurl'])?></loc>
        <lastmod><?=date('Y-m-d',$v['updatedate'])?></lastmod>
        <changefreq>daily</changefreq>
        <priority>0.8</priority>
    </url>
    <? cls_Parse::ActiveBack();} unset($_baidu_sjs,$v);?><? }else{  } ?>
    <? if($_baidu_zxal=cls_Parse::Tag(array('ename'=>"baidu_zxal",'tclass'=>"archives",'limits'=>30,'chsource'=>2,'chids'=>102,))){foreach($_baidu_zxal as $v){ cls_Parse::Active($v);?>
    <url>
        <loc><?=htmlspecialchars($v['marcurl'])?></loc>
        <lastmod><?=date('Y-m-d',$v['updatedate'])?></lastmod>
        <changefreq>daily</changefreq>
        <priority>0.8</priority>
    </url>
    <? cls_Parse::ActiveBack();} unset($_baidu_zxal,$v);?><? }else{  } ?>
    <? if($_baidu_sp=cls_Parse::Tag(array('ename'=>"baidu_sp",'tclass'=>"archives",'limits'=>30,'chsource'=>2,'chids'=>103,'validperiod'=>1,))){foreach($_baidu_sp as $v){ cls_Parse::Active($v);?>
    <url>
        <loc><?=htmlspecialchars($v['marcurl'])?></loc>
        <lastmod><?=date('Y-m-d',$v['updatedate'])?></lastmod>
        <changefreq>daily</changefreq>
        <priority>0.8</priority>
    </url>
    <? cls_Parse::ActiveBack();} unset($_baidu_sp,$v);?><? }else{  } ?>
    <? if($_baidu_gsdt=cls_Parse::Tag(array('ename'=>"baidu_gsdt",'tclass'=>"archives",'limits'=>30,'chsource'=>2,'chids'=>104,))){foreach($_baidu_gsdt as $v){ cls_Parse::Active($v);?>
    <url>
        <loc><?=htmlspecialchars($v['marcurl'])?></loc>
        <lastmod><?=date('Y-m-d',$v['updatedate'])?></lastmod>
        <changefreq>daily</changefreq>
        <priority>0.8</priority>
    </url>
    <? cls_Parse::ActiveBack();} unset($_baidu_gsdt,$v);?><? }else{  } ?>
    <? if($_baidu_tg=cls_Parse::Tag(array('ename'=>"baidu_tg",'tclass'=>"archives",'limits'=>30,'chsource'=>2,'chids'=>105,))){foreach($_baidu_tg as $v){ cls_Parse::Active($v);?>
    <url>
        <loc><?=htmlspecialchars($v['arcurl'])?></loc>
        <lastmod><?=date('Y-m-d',$v['updatedate'])?></lastmod>
        <changefreq>daily</changefreq>
        <priority>0.8</priority>
    </url>
    <? cls_Parse::ActiveBack();} unset($_baidu_tg,$v);?><? }else{  } ?>
</urlset>