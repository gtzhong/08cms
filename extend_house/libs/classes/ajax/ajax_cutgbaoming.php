<?php
/**
 * ajax�ύPOST����ͨ�ô������
 *
 * @author    Peace@08cms
 */

defined('_08CMS_APP_EXEC') || exit('No Permission');
class _08_M_Ajax_cutgbaoming extends _08_M_Ajax_cuAjaxPost_Base
{
    
	// ����չ 
    public function __toString()
    {
		$this->cuaj_post_init();
		
		#��չ
		if(!in_array($this->cuid,array(8,35,45))) cls_message::show('��������');
		
		$oA = new cls_cuedit($this->defCfgs());  
		$oA->add_init($this->defPid(),'',array('setCols'=>1)); 
		
		#��չ
		if($this->cuid==35){
			if(isset($oA->pinfo['tgend']) && ($oA->pinfo['tgend']<TIMESTAMP) && ($oA->pinfo['tgend'] != 0)) cls_message::show('�˿�����Ѿ����ڣ�');
		}
		if($this->cuid==45){
			if(empty($this->_get['fmdata']['yxlp'])) cls_message::show('��ѡ������¥�̣�');
			if(isset($oA->pinfo['enddate']) && ($oA->pinfo['enddate']<TIMESTAMP) && ($oA->pinfo['enddate'] != 0)) cls_message::show('�˿�����Ѿ����ڣ�');
		}
		
		$oA->sv_regcode("commu$this->cuid");
		$oA->sv_repeat($this->repCookie(), 'both'); // array('aid'=>$aid,'tocid'=>$tocid)
		$oA->sv_set_fmdata();//����$this->fmdata�е�ֵ 
		$oA->sv_items();//�������ݵ����飬��ʱδִ�����ݿ����
		$this->cid = $oA->sv_insert($this->extFields());//array('aid'=>$aid,'tocid'=>$tocid,'ip'=>$onlineip,)
		#$oA->sv_upload();//�ϴ�����
		
		//#��չ ���Ӳ���, ������, �Զ������..... 
		$spids = array(
			8=>5,
			35=>14,
			45=>110,
		); 
		$spid = $spids[$this->cuid]; 
		$this->_db->query("UPDATE {$this->_tblprefix}archives_$spid SET hdnum = hdnum + 1 WHERE aid = '$this->aid'"); 
		
		return $oA->sv_ajend('�ύ�ɹ���',array('aj_ainfo'=>$this->aj_ainfo,'aj_minfo'=>$this->aj_minfo));//����ʱ��Ҫ������

    }

}