<?php
defined('M_COM') || exit('No Permission');
$advtagadv_fcatalog145 = array (
  'ename' => 'adv_fcatalog145',
  'tclass' => 'advertising',
  'template' => '<div class="imgbox">
                <div class="bigpic" id="bigpic">
                    <ul>
                    {c$farc [tclass=farchives/] [casource=fcatalog145/] [limits=5/] [validperiod=1/]}
                     <li><a href="{link}" target="_blank" >{c$ad_image368_245 [tclass=image/] [tname=image/] [val=u/] [maxwidth=354/] [maxheight=246/]}<img src="{url}" width="354" height="246" alt="{$v[\'subject\']}" data-url="{$v[\'link\']}" />{/c$ad_image368_245}</a></li>{/c$farc}
                    </ul>
                </div>
                <ul class="num" id="num">
                    {c$farc [tclass=farchives/] [casource=fcatalog145/] [limits=5/] [validperiod=1/]}
                    <li>{sn_row}</li>
                  {/c$farc}
                </ul>
            
            </div>

        <script type="text/javascript">
       $(\'#bigpic\').find(\'li\').imgChange({thumbObj:\'#num li\',showTxt:1})//flash
        </script>   ',
  'setting' => 
  array (
    'limits' => 1,
    'casource' => 'fcatalog145',
    'validperiod' => '1',
    'orderstr' => ' a.vieworder DESC ',
  ),
) ;