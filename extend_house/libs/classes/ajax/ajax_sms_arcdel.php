<?php
/**
 * ����: �����ֻ�ȷ���� ɾ���ĵ���Ϣ
 *
 * @example   /index.php?/ajax/sms_arcdel/mod/arcxdel/act/send/code/803634/tel/13223332244/ids/234,123,89
 * @author    peace@08cms
 * @copyright 2008 - 2014 08CMS, Inc. All rights reserved.
 *
 */
defined('_08CMS_APP_EXEC') || exit('No Permission');
class _08_M_Ajax_sms_arcdel extends _08_M_Ajax_sms_msend_Base
{
	public $mod = 'arcxdel'; //�̶�
	public $act = 'send'; //�̶�����send�淶��չ�������ﲻ�����ţ�ֻ����aidsɾ���ĵ� 
	
    public function __toString()
    {       
		$this->sms = new cls_sms(); 
		$this->tpl = $this->sms->smsTpl($this->mod);
		$this->aids = empty($this->_get['aids']) ? '' : $this->_get['aids']; 
		//��ȫ�ۺϼ��
		$re = $this->check_all(); 
		if($re['error']) return $re;
		//ִ�в���
		return $this->sms_delete();
    }
	
	// delete(ɾ����Ϣ)
	// chid : �ĵ�ģ��
	// ids : �ĵ�ids
	// ����������:sms_send()
    public function sms_delete()
    {   
		$db = _08_factory::getDBO();
		$tblprefix = cls_env::getBaseIncConfigs('tblprefix');
		$re = array('error'=>'', 'message'=>'');
		// �ٴ���֤?, 'must':���뾭��ǰһ������֤��
		$rc = $this->sms_check(1,'must');
		if($rc['error'] || empty($rc['tel']) || $rc['tel']!==$this->tel){
			return $rc;	
		} 
		if(@$rc['tel']!==$this->tel){
			return array('error'=>'checkNumber', 'message'=>"{$this->tel}�������");
		} 
		$ids = empty($this->_get['ids']) ? '' : $this->_get['ids']; //echo $ids;
		if(empty($ids)){ // || !in_array($chid,array(2,3,9,10))
			$re['error'] = 'ErrorParas';	
			$re['message'] = '��������';
			return $re;
		}
		$ids = explode(',',$ids); 
		$arc = new cls_arcedit; $cnt = 0;
		$arr = array('2'=>'11','3'=>'16','9'=>'24','10'=>'25');
		foreach($arr as $chid=>$tbid){
		foreach($ids as $aid){
			$aid = intval($aid); if(empty($aid)) continue;
			$sql = "SELECT lxdh FROM {$tblprefix}archives_$chid WHERE aid='$aid' AND lxdh='{$this->tel}'";
			$r = $db->fetch_one($sql); //echo "\n$sql\n"; //atbl($chid)
			if(!empty($r)){
				$arc->set_aid($aid,array('chid'=>$chid));
				$arc->arc_delete(0);
				$cnt++;
			}
		} }
		unset($arc);
    	if($cnt>0){ 
			$re['message'] = "{$cnt}����¼ɾ���ɹ�";
		}else{
			$re['error'] = 'ErrorDelete';	
			$re['message'] = "û�з��������ļ�¼";	
		}
		//���cookie, ǰ̨�ж�
		return $re;
	}
}