<?php
/**
 * ȡ��ί��
 *
 * @example   ������URL��index.php?/ajax/cancelweituo/wid/...
 * @author    lyq <692378514@163.com>
 * @copyright Copyright (C) 2008 - 2014 08CMS, Inc. All rights reserved.
 */

defined('_08CMS_APP_EXEC') || exit('No Permission');
class _08_M_Ajax_ModifyPrice extends _08_Models_Base
{
    public function __toString()
    {
		$mcharset = $this->_mcharset;	
		header("Content-Type:text/html;CharSet=$mcharset");		
		$db = $this->_db;
		$tblprefix = $this->_tblprefix;
		$curuser   = $this->_curuser;
		$memberid = empty($curuser->info['mid']) ? 0 : $curuser->info['mid'];
		$cid  = empty($this->_get['cid']) ? 0 : max(1,intval($this->_get['cid']));	
		$zj  = empty($this->_get['zj']) ? 0 : max(1,intval($this->_get['zj']));

		if(!$cid || !$zj) mexit('��������');
		$cuid = 36;
		if(!($commu = cls_cache::Read('commu',$cuid)) || !$commu['available']) mexit('ί�й����ѹرա�');
		if(!empty($memberid)){
			if($memberid == 1){//������Ա���й���ʽ�����Ա���ģ�����ί�з�Դɾ��ʱ����ȡ���йܵĻ�Ա��ID
				define('M_MCENTER', TRUE); // ���ڴ������²��������Ի�Ա����
				$member_info = $curuser->isTrusteeship();
				$memberid = $member_info['mid'];
			}			
			
			$db->query("UPDATE {$tblprefix}$commu[tbl] SET zj='$zj' WHERE cid='$cid' AND mid='$memberid'");
			mexit($db->affected_rows() ? 'SUCCEED' : 'û�����κ��޸ġ�');
		}else{
			mexit('���ȵ�½��Ա��');
		}
	}
}