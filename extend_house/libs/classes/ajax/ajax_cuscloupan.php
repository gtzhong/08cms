<?php
/**
 * ajax�ύPOST����ͨ�ô������
 *
 * @author    Peace@08cms
 */

defined('_08CMS_APP_EXEC') || exit('No Permission');
class _08_M_Ajax_cuscloupan extends _08_M_Ajax_cuAjaxPost_Base
{
    
	// ��չ 
    public function __toString()
    {
		$this->cuaj_post_init();
		if(!in_array($this->cuid,array(7))) cls_message::show('��������');
		
		global $timestamp, $onlineip;
		$db = _08_factory::getDBO();
		$tblprefix = cls_env::getBaseIncConfigs('tblprefix');
		$curuser = cls_UserMain::CurUser();
		$memberid = $curuser->info['mid'];
		
		$aid = empty($this->_get['aid']) ? 0 : max(1,intval($this->_get['aid']));

		$sqlstr = '';
        foreach(array('new','old','rent',) as $v){ 
            if(!empty($this->_get[$v])){
				$sqlstr = "$v=1";
				break;
			}
        } //echo "$memberid,$sqlstr,$aid";
		$curuser = cls_UserMain::CurUser();
		if(empty($curuser->info['mid'])){ 
			$re = 'noLogin';
		}elseif(empty($sqlstr)){
			$re = "��������";
		}elseif($db->result_one("SELECT COUNT(*) FROM {$tblprefix}commu_gz WHERE mid='$memberid' AND $sqlstr AND aid='$aid'")){
    	    $re = "Repeat";
        }elseif($db->result_one("SELECT COUNT(*) FROM {$tblprefix}commu_gz WHERE mid='$memberid' AND aid='$aid'")){
			$db->query("UPDATE {$tblprefix}commu_gz SET $sqlstr WHERE mid='$memberid' AND aid='$aid'");
			$re = "OK";
		}else{
    		$db->query("INSERT INTO {$tblprefix}commu_gz SET aid='$aid',mid='$memberid',mname='{$curuser->info['mname']}',createdate='$timestamp',checked=1,$sqlstr");
			$re = "OK";
        }
		
		return array('error'=>'', 'message'=>'�ύ��ɣ�', 'result'=>$re);
		
    }
	
}