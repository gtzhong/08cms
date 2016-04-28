<?php
defined('M_COM') || exit('No Permission');
$advtagadv_fcatalog112 = array (
  'ename' => 'adv_fcatalog112',
  'tclass' => 'advertising',
  'template' => '<div class="area">{if $v[\'html\']}{html}{elseif $v[\'image\']}{c$ad_image1200_314 [cname=ad_image1200_314/] [tclass=image/] [tname=image/] [val=u/] [maxwidth=1200/] [maxheight=314/]}<img src="{url}" width="1200" height="314" />{/c$ad_image1200_314}{elseif $v[\'flash\']}{c$flash1200_314 [cname=flash1200_314/] [tclass=flash/] [tname=flash/] [val=u/] [width=1200/] [height=314/]}{playbox}{if $v[\'link\']}<a class="ad-link" href="{$v[\'link\']}" style="margin-top:-{height}px;width:{width}px;height:{height}px;"></a>{/if}{/c$flash1200_314}{/if}</div>
<div class="blank10"></div>',
  'setting' => 
  array (
    'limits' => 1,
    'casource' => 'fcatalog112',
    'validperiod' => '1',
    'orderstr' => ' a.vieworder DESC ',
  ),
) ;