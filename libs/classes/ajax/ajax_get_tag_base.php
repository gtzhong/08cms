<?php
/**
 * ��AJAX��ȡ��ǩ
 *
 * @example   ������URL��http://nv50.08cms.com/index.php?/ajax/get_tag/data_format/js/
 * @author    Wilson <Wilsonnet@163.com>
 * @copyright Copyright (C) 2008 - 2014 08CMS, Inc. All rights reserved.
 */

defined('_08CMS_APP_EXEC') || exit('No Permission');
class _08_M_Ajax_Get_Tag_Base extends _08_Models_Base
{    
    public function __toString()
    {
    	# ���οͳ�ʼ����ǰ��Ա���趨δ��Ч????????????
        
    	$_DataFormat = '';
    	if(!empty($this->_get['data_format'])){
    		switch(strtolower($this->_get['data_format'])){
    			case 'js':
    				$_DataFormat = 'get_tag_js';
    			break;
    		}
    	}
        
        return cls_JsTag::Create(array('DataFormat' => $_DataFormat, 'DynamicReturn' => true));
    }
}