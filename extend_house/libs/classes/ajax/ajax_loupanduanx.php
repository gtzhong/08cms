<?php
/**
 * ¥�̶���-������չ
 *
 * @author    Peace@08cms
 */

defined('_08CMS_APP_EXEC') || exit('No Permission');
class _08_M_Ajax_loupanduanx extends _08_M_Ajax_cuAjaxPost_Base
{
   
	 
    public function __toString(){
		
		$this->cuaj_post_init();

		#��չ
		if(!in_array($this->cuid,array(3))) cls_message::show('��������');		
		$oA = new cls_cuedit($this->defCfgs());  
		$oA->add_init($this->defPid(),'',array('setCols'=>1)); 
		$fmdata  = empty($this->_get['fmdata']) ? 0 : $this->_get['fmdata'];
		$quaid=$oA->pinfo;
		$arc = new cls_arcedit;
		$arc->set_aid($quaid['aid'],array('au'=>1,'ch'=>1,'chid'=>4));

		#��չ Լ����..
		//if($this->cuid==3){}
		
        $strfm=$this->_get;
		$fields=$this->extFields();
		$arr = array(); 
		foreach($strfm as $k=>$v){	
		  if(strpos($k,"[dyfl]")){
			$arr[] = $v;
		  }
		}
		$fields['dyfl'] = implode("\t",$arr); 

		$oA->sv_regcode("commu$this->cuid");
		$oA->sv_repeat($this->repCookie(), 'both'); // array('aid'=>$aid,'tocid'=>$tocid)
		$oA->sv_set_fmdata();//����$this->fmdata�е�ֵ 
		$oA->sv_items();//�������ݵ����飬��ʱδִ�����ݿ����
		$this->cid = $oA->sv_insert($fields);//array('aid'=>$aid,'tocid'=>$tocid,'ip'=>$onlineip,)
		#$oA->sv_upload();//�ϴ�����
		
		//#��չ ���Ӳ���, ������, �Զ������,����չ�ظ��ύ�������ύ
		$sms = new cls_sms();
		$_tel = $fmdata['sjhm'];
		$msg = array();
		if(!$sms->isClosed() && $_tel && $this->cucfgs['issms']== 1 && !empty($this->cucfgs['smscon']) && in_array(5,$arr)){ //$this->cuid==3 && 
			$msg = $sms->sendTpl($_tel,$this->cucfgs['smscon'],$arc->archive,'sadm');
		}	
		//����ʱ��Ҫ������	
		return $oA->sv_ajend('��ѯ�ɹ���'.(!empty($msg[0]) ? '�����ѷ���' : ''),array('aj_ainfo'=>$this->aj_ainfo,'aj_minfo'=>$this->aj_minfo));
    }
  
}