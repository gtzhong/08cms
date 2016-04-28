<?php
/**
 * ���ݻ�Ա��֤����id,mchid,��֤�ֶ� �Ƿ��ظ����������ǿɼ����ĵ�
 *
 * @example   ������URL��http://nv50.08cms.com/index.php?/ajax/checkunique/var/test/mctid/1/mchid/1/oldval/test/datatype/xml/&callback=$_iNp$JgYF8
 * @author    Wilson <Wilsonnet@163.com>
 * @copyright Copyright (C) 2008 - 2014 08CMS, Inc. All rights reserved.
 */

defined('_08CMS_APP_EXEC') || exit('No Permission');
class _08_M_Ajax_CheckUnique_Base extends _08_Models_Base
{
    public function __toString()
    {
    	$val = empty($this->_get['val']) ? '' : $this->_get['val'];
    	$oldval = empty($this->_get['oldval']) ? '' : $this->_get['oldval'];
    	$mctid = empty($this->_get['mctid']) ? 0 : max(0,intval($this->_get['mctid']));
    	$mchid = empty($this->_get['mchid']) ? 0 : max(0,intval($this->_get['mchid']));
    	$mctypes = cls_cache::Read('mctypes');
    	$mfields = cls_cache::Read('mfields',$mchid); 
    	$field = @$mctypes[$mctid]['field']; 
    	if(!isset($mctypes[$mctid]) || !isset($mfields[$field])){
    		$msg = '��������';
    	}else{
            $row = $this->_db->select('mid')->from("#__{$mfields[$field]['tbl']}")->where(array($field => $val))->limit(1)->exec()->fetch();
     		$mid = $row['mid'];
    		$msg = $mid ? 'Exists' : 'OK';
    	}
    	//echo $msg;
    	if(empty($this->_get['method'])){ //��js��֤
    		ajax_info(array('msg'=>$msg));
    	}else{ //ʹ��validator.js��֤
    		if($oldval && $msg=='Exists' && $oldval==$val) $msg = "";	
    		elseif($msg=='Exists') $msg = "�����Ѿ����ڣ�";	
    		elseif($msg=='OK') $msg = "";
    		return $msg;	
    	}		
    }
}