<?php
/**
 * �ղص���
 *
 * @example   ������URL��index.php?/ajax/sc_dianpu/mid/...
 * @author    lyq <692378514@163.com>
 * @copyright Copyright (C) 2008 - 2014 08CMS, Inc. All rights reserved.
 */

defined('_08CMS_APP_EXEC') || exit('No Permission');
class _08_M_Ajax_SC_DianPu extends _08_Models_Base
{
    public function __toString()
    {		
		$db = $this->_db;
		$tblprefix = $this->_tblprefix;
		$timestamp = TIMESTAMP; 
		$curuser   = $this->_curuser;
		$memberid = empty($curuser->info['mid']) ? 0 : $curuser->info['mid'];
		$scmid  = empty($this->_get['scmid']) ? 0 : max(1,intval($this->_get['scmid']));

		//��ָ���ղض���
		if(empty($scmid)) return "var data=1";
		//���ȵ�¼��Ա
		if(empty($memberid)) return "var data=2";
		//��ǰ���ܹر�
		if(!($commu = cls_cache::Read('commu',11)) || !$commu['available']) {
			return "var data=3";
		}
		//��û�й�עȨ��		
		if(!$curuser->pmbypmid($commu['pmid'])) {
			return "var data=4";
		}
		//��ָ���ղض���
		if(!($scname = $db->result_one("SELECT mname FROM {$tblprefix}members WHERE mid = '$scmid'"))){
			return "var data=1";
		};
		//�ף����Ѿ��ղ���
		if($result = $db->result_one("SELECT COUNT(*) FROM {$tblprefix}$commu[tbl] WHERE mid='$memberid' AND tomid='$scmid'")){
			return "var data=5";
		}	
		
		$sqlstr = "tomid='$scmid',tomname='$scname',mid='$memberid',mname='{$curuser->info['mname']}',createdate='$timestamp',checked=1";
		$db->query("INSERT INTO {$tblprefix}$commu[tbl] SET $sqlstr");
		//�ղسɹ�
		return "var data=6";
	}
}