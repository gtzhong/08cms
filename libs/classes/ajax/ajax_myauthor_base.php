<?php
/**
 * ѡ������
 *
 * @example   ������URL��http://nv50.08cms.com/index.php?/ajax/myauthor/
 * @author    Wilson <Wilsonnet@163.com>
 * @copyright Copyright (C) 2008 - 2014 08CMS, Inc. All rights reserved.
 * @since     nv50
 */

defined('_08CMS_APP_EXEC') || exit('No Permission');
class _08_M_Ajax_Myauthor_Base extends _08_Models_Base
{
    public function __toString()
    {
		$cms_abs = cls_env::mconfig('cms_abs');    	
        $str = '<div class="coolbg4" onmousedown="aListSetMoving.Move(\'myauthord\',event)">[<a href="'.$cms_abs.'tools/edit_author.php?" onclick="HideObj(\'myauthor\');removeObj(\'myauthor\');return floatwin(\'editmyauthor\',this,400,400);">����</a>]&nbsp;[<a href="javascript:void(0)" onclick="javascript:HideObj(\'myauthor\');">�ر�</a>]</div><div class="wsselect">';
    	$myauthor = cls_cache::cacRead('myauthor');
    	foreach($myauthor as $s){
    		$str .= "<a href=\"javascript:void(0)\" onclick=\"javascript:PutAuthor('$s')\">$s</a> | ";
    	}
    	$str .= "</div><div class='coolbg5'>&nbsp;</div>";
    	
        return $str;
    }
}