<?php
/**
 * ǰ̨��ע�ᷢ���������������ʱ������绰��Ψһ��
 *
 * @example   ������URL��index.php?/ajax/checkUniqueTel/val/...
 * @author    lyq <692378514@163.com>
 * @copyright Copyright (C) 2008 - 2014 08CMS, Inc. All rights reserved.
 */

defined('_08CMS_APP_EXEC') || exit('No Permission');
class _08_M_Ajax_CheckUniqueTel extends _08_Models_Base
{
    public function __toString()
    {
		$mcharset = $this->_mcharset;	
		$timestamp = TIMESTAMP; 
		header("Content-Type:text/html;CharSet=$mcharset");
		$db = $this->_db;
		$tblprefix = $this->_tblprefix;
		$val  = empty($this->_get['val']) ? '-1' : $this->_get['val'];
		$chid = isset($this->_get['chid']) ? intval($this->_get['chid']) : 0;
        
        if(!in_array($chid,array(2,3,9,10))) return '��������';

		$sql = "SELECT mid FROM {$tblprefix}members_sub WHERE lxdh='$val'";
		$mid = $db->result_one($sql);
		// �Ƿ���ͨ��Ա�򾭼���
		$sql = "SELECT mid FROM {$tblprefix}members WHERE mid='$mid' AND mchid IN(1,2)";
		$sid = $db->result_one($sql);
		$msg = $sid ? '�����Ѿ�������ϵͳ��Ա�У�����ʹ�ã�' : '';
		if($msg) return $msg;
		
		//������������
		$count_gpub = cls_env::mconfig('count_gpub'); //�οͷ�������
		$count_gpub = empty($count_gpub) ? 3 : $count_gpub;
		$sql = "SELECT count(*) FROM {$tblprefix}".atbl($chid)." a INNER JOIN {$tblprefix}archives_$chid c ON c.aid=a.aid WHERE a.mid='0' AND c.lxdh='$val' AND a.createdate>'".($timestamp-85400)."' ";
		$all_gpub = $db->result_one($sql); $all_gpub = empty($all_gpub) ? 0 : $all_gpub;
		
		if($all_gpub>=$count_gpub){
			$msg = "��������췢���޶�����,�����ٷ�����Դ��";
		}
		return $msg;
	}
}