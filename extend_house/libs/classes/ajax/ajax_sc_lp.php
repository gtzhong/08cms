<?php
/**
 * С������ҳ�ĳ�����Ϣ֪ͨ�����ַ���Ϣ֪ͨ��¥������ҳ�Ĺ�ע¥��
 *
 * @example   ������URL��index.php?/ajax/sc_lp/aid/...
 * @author    lyq <692378514@163.com>
 * @copyright Copyright (C) 2008 - 2014 08CMS, Inc. All rights reserved.
 */

defined('_08CMS_APP_EXEC') || exit('No Permission');
class _08_M_Ajax_SC_LP extends _08_Models_Base
{
    public function __toString()
    {
		$db = $this->_db;
		$tblprefix = $this->_tblprefix;
		$timestamp = TIMESTAMP;
		$curuser   = $this->_curuser;
		$memberid = empty($curuser->info['mid']) ? 0 : $curuser->info['mid'];
		$aid  = empty($this->_get['aid']) ? 0 : max(1,intval($this->_get['aid']));
        $cuid = 7;

		//��ָ���ղض���
		if(empty($aid)) return "var r='��ָ���ղض���';";
		//���ȵ�¼��Ա
		if(empty($memberid)) return "var r='���ȵ�¼��Ա';";
		//��ǰ���ܹر�
		if(!($commu = cls_cache::Read('commu',$cuid)) || !$commu['available']) {
			return "var r='��ǰ���ܹر�';";
		}
		//��û�й�עȨ��
		if(!$curuser->pmbypmid($commu['pmid'])) {
			return "var r='��û�й�עȨ��';";
		}
        //��ָ���ղض���
		$arc = new cls_arcedit;
		$arc->set_aid($aid,array('au'=>0));
		if(!$arc->aid || !$arc->archive['checked'] || !in_array($arc->archive['chid'],$commu['chids'])){
			return "var r='��ָ���ղض���';";
		};
        $sqlstr = '';
        foreach(array('new','old','rent',) as $v){ 
            !empty($this->_get[$v]) && $sqlstr .= ",$v=1";
        }
        if($db->result_one("SELECT COUNT(*) FROM {$tblprefix}$commu[tbl] WHERE mid='$memberid' AND ".substr($sqlstr,1)." AND aid='$aid'")){
    	       return "var r='�ף��Ѿ��ղ��ˣ�'";
        }else{
    	   $db->query("INSERT INTO {$tblprefix}$commu[tbl] SET aid='$aid',mid='$memberid',mname='{$curuser->info['mname']}',createdate='$timestamp',checked=1 $sqlstr");
        }
		//�ղسɹ�
		return "var r=5;";



	}
}