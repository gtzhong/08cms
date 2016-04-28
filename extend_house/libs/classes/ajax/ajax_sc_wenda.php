<?php
/**
 * �ղ��ʴ�
 *
 * @example   ������URL��/index.php?/ajax/sc_wenda/aid...
 * @author    lyq <692378514@163.com>
 * @copyright Copyright (C) 2008 - 2014 08CMS, Inc. All rights reserved.
 */

defined('_08CMS_APP_EXEC') || exit('No Permission');
class _08_M_Ajax_SC_WenDa extends _08_Models_Base
{
    public function __toString()
    {
		$db = $this->_db;
		$tblprefix = $this->_tblprefix;
		$timestamp = TIMESTAMP; 
		$curuser   = $this->_curuser;
		$memberid = empty($curuser->info['mid']) ? 0 : $curuser->info['mid'];
		$aid  = empty($this->_get['aid']) ? 0 : max(1,intval($this->_get['aid']));
		
		$cuid = 39;
		//��ָ���ղ��ʴ�
		if(empty($aid)) return "var data=1";
		//���ȵ�¼��Ա
		if(empty($memberid)) return "var data=2";
		//��ǰ���ܹر�
		if(!($commu = cls_cache::Read('commu',$cuid)) || !$commu['available']) {
			return "var data=3";
		}
		//��û�й�עȨ��		
		if(!$curuser->pmbypmid($commu['pmid'])) {
			return "var data=4";
		}
		//��ָ���ղض���
		$arc = new cls_arcedit;
		$arc->set_aid($aid,array('au'=>0));
		if(!$arc->aid || !$arc->archive['checked'] || !in_array($arc->archive['chid'],$commu['chids'])){
			return "var data=1";
		};
		//�ף����Ѿ��ղ���
		if($result = $db->result_one("SELECT COUNT(*) FROM {$tblprefix}$commu[tbl] WHERE mid='$memberid' AND aid='$aid'")){
			return "var data=5";
		}	
		
		$sqlstr = "aid='$aid',mid='$memberid',mname='{$curuser->info['mname']}',createdate='$timestamp',checked=1";
		$db->query("INSERT INTO {$tblprefix}$commu[tbl] SET $sqlstr");
		//�ղسɹ�
		return "var data=6";
    }
}
