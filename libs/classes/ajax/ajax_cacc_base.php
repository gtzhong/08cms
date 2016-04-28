<?php
/**
 * _08cms.fields.linkage �����˵�ʹ��AJAX
 *
 * @example   ������URL��http://nv50.08cms.com/index.php?/ajax/cacc/type/ctag/varname/archives11/ename/test/
 * @author    Wilson <Wilsonnet@163.com>
 * @copyright Copyright (C) 2008 - 2014 08CMS, Inc. All rights reserved.
 */

defined('_08CMS_APP_EXEC') || exit('No Permission');
class _08_M_Ajax_Cacc_Base extends _08_Models_Base
{
    public function __toString()
    {
    	if ( empty($this->_get['varname']) || empty($this->_get['type']) || empty($this->_get['ename']) )
        {
            exit();
        }
        
        foreach ( array('varname', 'type', 'ename') as $key ) 
        {
            $this->_get[$key] = preg_replace('/[^\w]/', '', $this->_get[$key]);
        }
        
        _08_Loader::import('include::field.fun');
		header("Content-Type: text/javascript");
		$output = "var {$this->_get['varname']}=[";
		$arr = cacc_arr(trim($this->_get['type']),empty($this->_get['tpid']) ? 0 : intval($this->_get['tpid']),trim($this->_get['ename']));
		// for ����ĸ���� add Letter
		foreach($arr as $k=>$v){
			if($v['level']==0 && $v['letter']){
				$arr[$k]['title'] = $v['letter'].' '.$v['title']; //,"$v[title]"
			}
		}
		cls_catalog::uccidstop($arr);
		$cnt = 0;
		foreach($arr as $k => $v){ 
			$output .= ($cnt ? ',' : '' ).("[$k,$v[pid],'".addslashes($v['title'])."',".(empty($v['unsel']) ? 0 : 1) . ']');
			$cnt++;
		}
		$output .= ']';
        
        return $output;
    }
}