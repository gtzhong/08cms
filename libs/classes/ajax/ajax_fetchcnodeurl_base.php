<?php
/**
 * ȡ�ýڵ��url��������url����$urltype,��$caid��$ccid2��ʽ����Ŀ����
 *
 * @example   ������URL��http://nv50.08cms.com/index.php?/ajax/fetchcnodeurl/datatype/xml/caid/4/keywords/d/&callback=$_iNp$JgYF8
 * @author    Wilson <Wilsonnet@163.com>
 * @copyright Copyright (C) 2008 - 2014 08CMS, Inc. All rights reserved.
 */

defined('_08CMS_APP_EXEC') || exit('No Permission');
class _08_M_Ajax_Fetchcnodeurl_Base extends _08_Models_Base
{
    public function __toString()
    {        
    	$temparr = cls_env::_GET();
    	$cnstr = cls_cnode::cnstr($temparr);
    	if( !($cnode = cls_node::cnodearr($cnstr)) ) 
        {
            $result = '#';
        }
        else
        {
        	$result = ($cnode[empty($this->_get['urltype']) ? 'indexurl' : $this->_get['urltype']]);
        }
        
        return $result;
    }
}