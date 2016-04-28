<?php echo '<?';?>xml version="1.0" encoding="<?=$mcharset?>"<?php echo '?>';?> 
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"> 
    <url> 
        <loc><?=htmlspecialchars($cms_abs)?></loc> 
        <lastmod><?=date('Y-m-d')?></lastmod> 
        <changefreq>daily</changefreq> 
        <priority>1.0</priority> 
    </url> 
    <? if($_col09=cls_Parse::Tag(array('ename'=>"col09",'tclass'=>"catalogs",'limits'=>1000,'listby'=>"ca",))){foreach($_col09 as $v){ cls_Parse::Active($v);?><url> 
        <loc><?=htmlspecialchars($v['indexurl'])?></loc> 
        <lastmod><?=date('Y-m-d')?></lastmod> 
        <changefreq>daily</changefreq> 
        <priority>0.9</priority> 
    </url> <? cls_Parse::ActiveBack();} unset($_col09,$v);?><? }else{  } ?>
    <? if($_qichezx=cls_Parse::Tag(array('ename'=>"qichezx",'tclass'=>"catalogs",'limits'=>10000,'listby'=>"ca",'casource'=>4,))){foreach($_qichezx as $v){ cls_Parse::Active($v);?><url> 
        <loc><?=htmlspecialchars($v['indexurl1'])?></loc> 
        <lastmod><?=date('Y-m-d')?></lastmod> 
        <changefreq>daily</changefreq> 
        <priority>0.8</priority> 
 </url> <? cls_Parse::ActiveBack();} unset($_qichezx,$v);?><? }else{  } ?>
    <? if($_qichezx3=cls_Parse::Tag(array('ename'=>"qichezx3",'tclass'=>"catalogs",'limits'=>10000,'listby'=>"ca",'casource'=>5,))){foreach($_qichezx3 as $v){ cls_Parse::Active($v);?><url> 
        <loc><?=htmlspecialchars($v['indexurl1'])?></loc> 
        <lastmod><?=date('Y-m-d')?></lastmod> 
        <changefreq>daily</changefreq> 
        <priority>0.7</priority> 
 </url> <? cls_Parse::ActiveBack();} unset($_qichezx3,$v);?><? }else{  } ?>
</urlset>