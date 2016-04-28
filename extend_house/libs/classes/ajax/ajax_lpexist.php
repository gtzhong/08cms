<?php
/**
 * ���¥���Ƿ��ظ�
 *
 * @example   ������URL��index.php?/ajax/lpexist/lpname/...
 * @author    lyq <692378514@163.com>
 * @copyright Copyright (C) 2008 - 2014 08CMS, Inc. All rights reserved.
 */

defined('_08CMS_APP_EXEC') || exit('No Permission');
class _08_M_Ajax_LpExist extends _08_Models_Base
{
    public function __toString()
    {
		$mcharset = $this->_mcharset;	
		header("Content-Type:text/html;CharSet=$mcharset");		
		$db = $this->_db;
		$tblprefix = $this->_tblprefix;
		$lpname  = empty($this->_get['lpname']) ? '' : trim($this->_get['lpname']);		
		$lpname = cls_string::iconv('utf-8',$mcharset,$lpname);
		$leixing = empty($this->_get['leixing']) ? '' : trim($this->_get['leixing']);
		// 0: ¥�̱� (Ԥ��:1:��¥��;2:��С��...)
		// 5: ¥�̱�+��ʱС����
		$rec0 = $db->result_one("SELECT aid FROM {$tblprefix}".atbl(4)." WHERE subject='$lpname'");
		$rec5 = $db->result_one("SELECT aid FROM {$tblprefix}arctemp15 WHERE subject='$lpname'"); //��ʱС����
		if((empty($leixing) && $rec0) || ($leixing=='5' && ($rec0 || $rec5))){
			return "[$lpname] �Ѿ����ڣ�";
		}else{
			return "succeed";	
		}
	}
}