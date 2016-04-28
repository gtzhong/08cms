<?php
/**
 * ��֤Ŀ¼�Ƿ���ڣ�-1 Ϊδ����Ŀ¼��0 Ϊ�����ڣ�1 Ϊ����
 *
 * @example   ������URL��http://nv50.08cms.com/index.php?/ajax/dirname/datatype/xml/&callback=$_iNp$JgYF8
 * @author    Wilson <Wilsonnet@163.com>
 * @copyright Copyright (C) 2008 - 2014 08CMS, Inc. All rights reserved.
 */

defined('_08CMS_APP_EXEC') || exit('No Permission');
class _08_M_Ajax_Dirname_Base extends _08_Models_Base
{
    public function __toString()
    {
        $returnValue = 0;
    	if( empty($this->_get['value']) )
        {
            $returnValue = -1;
    	}
        else
        {
    	    $value = strtolower(trim($this->_get['value']));
    	    in_array($value,cls_cache::Read('cn_dirnames')) && ($returnValue = 1);        	
        }
        
        return $returnValue;
    }
}