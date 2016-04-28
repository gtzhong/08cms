<?php
/**
 * ajax�ύPOST����ͨ�ô������
 *
 * @author    Peace@08cms
 */

defined('_08CMS_APP_EXEC') || exit('No Permission');
class _08_M_Ajax_cuAjaxPost_Base extends _08_Models_Base
{
    protected $cuid = 0;
	protected $cutype = 0;
	
	protected $aid = 0;
	protected $tomid = 0;
	protected $tocid = 0;
	
	protected $cucfgs = array();
	protected $exfields = array();
	protected $pinfo = array();
	
	protected $verify = '';
	protected $regcode = '';
	protected $fmpre = '';
	protected $cucbs = ''; //��ѡ�ֶ�:����ʱ��,�ֿ�;����ʱ��tab���ֿ�
	protected $cucbvals = array(); //��ѡ����ֵ
	
	//protected $cid = 0; //���ʱ����? 
    
	// ���ʺ���Ҫ������չ
	// ��չע��̳б��ࣺextends _08_M_Ajax_cuAjaxPost_Base
    //  'aj_minfo',     //ͬʱ���ػ�Ա����
    //  'aj_ainfo',     //ͬʱ�����ĵ�����
    //  'aj_func',      //����cuedit��sv_Favor(),sv_Mood(),sv_Vote()
    public function __toString()
    {
		$this->cuaj_post_init();
		$oA = new cls_cuedit($this->defCfgs());  
		$oA->add_init($this->defPid(),'',array('setCols'=>1)); 
		$this->pinfo = $oA->pinfo; 
		
        if($this->aj_func && in_array($this->aj_func,array('Favor','Mood','Vote'))){
            $re = $this->cu_funcs($oA, $this->aj_func); 
            return array('error'=>'', 'message'=>'�ύ��ɣ�', 'result'=>$re); //, 'cu_data'=>$fmdata
        }
        
		$oA->sv_regcode("commu$this->cuid");
		$oA->sv_repeat($this->repCookie(), 'both'); // array('aid'=>$aid,'tocid'=>$tocid)
		$oA->sv_set_fmdata();//����$this->fmdata�е�ֵ 
		$oA->sv_items();//�������ݵ����飬��ʱδִ�����ݿ����
		$this->cid = $oA->sv_insert($this->extFields());//array('aid'=>$aid,'tocid'=>$tocid,'ip'=>$onlineip,)
		#$oA->sv_upload();//�ϴ�����
		//���Ӳ���, ������, �Զ������..... 
		return $oA->sv_ajend('�ύ�ɹ���',array('aj_ainfo'=>$this->aj_ainfo,'aj_minfo'=>$this->aj_minfo));//����ʱ��Ҫ������
        //'';//$contents;
    }
    
    //����cuedit��sv_Favor(),sv_Mood(),sv_Vote()
	public function cu_funcs($oA=null, $func='')
    {
		// sv_Favor($pfield='aid')
        // sv_Mood($pfield='aid', $fix='opt', $no='1', $nos='1,2', $add=1)
        // sv_Vote($cid,          $fix='opt', $no='1', $nos='1,2', $add=1)
		$dbfields = $oA->getFields(); //print_r($dbfields);
        foreach(array('pfield','fix','no','tocid') as $k){
			$$k = empty($this->_get[$k]) ? '' : $this->_get[$k];	
		}
        $_fkeys = array('pfield'); //print_r($pfield);
        if($func=='Favor' && $pfield && in_array($pfield,$dbfields)){
            if(empty($oA->pinfo)) return 'Error';
            return $oA->sv_Favor($pfield);
        }
        if($func=='Mood' && $pfield && in_array($pfield,$dbfields)){ 
            if(in_array($fix,$dbfields)){
				$no = $nos = '';
			}else{
				$nos = $no;		
			} //echo "($fix$no)"; 
			return in_array("$fix$no",$dbfields) ? $oA->sv_Mood($pfield,$fix,$no,$nos) : 'Error';
        }
        if($func=='Vote' && $tocid){ 
            return in_array("$fix$no",$dbfields) ? $oA->sv_Vote($tocid,$fix,$no,"$no") : 'Error';
        }
        return 'Error';
    }
    
	// init��ʼ��(���ò���)
	protected function cuaj_post_init(){
		$a1 = array('cuid','aid','tomid','tocid'); 
		foreach($a1 as $k){
			$this->$k = empty($this->_get[$k]) ? 0 : floatval($this->_get[$k]);	
		}
		$a2 = array('cutype','verify','regcode','fmpre','cucbs','aj_minfo','aj_ainfo','aj_func'); //'cureval',
		foreach($a2 as $k){
			$this->$k = empty($this->_get[$k]) ? '' : $this->_get[$k];
			cls_env::SetG($k, $this->$k);
		}
		if(empty($this->cutype) && $this->aid) $this->cutype = 'a'; //��aidĬ��Ϊ����ĵ�
		if(empty($this->cutype) && $this->tomid) $this->cutype = 'm'; //��tomidĬ��Ϊ��Ի�Ա
		$this->cucfgs = cls_cache::Read('commu',$this->cuid);
		if(empty($this->cuid)) cls_message::show('����');
		empty($this->fmpre) && $this->fmpre = 'fmdata';
		$this->ip = cls_env::OnlineIP();
		cls_env::SetG('inajax', 1); //����:ajax�ύ $this->_get['inajax'] ? 1 : 0
		//����ajax������$this->_get��ȡֵ, ����cubasic����Ҫ��cls_env::SetG���ܻ�ȡ
		$fmdata = @$this->_get[$this->fmpre];
		$mcharset = cls_env::getBaseIncConfigs('mcharset');
		$cucba = empty($this->cucbs) ? array(-1) : explode(',',str_replace(array($this->fmpre,'[',']'),'',$this->cucbs));
		if(!empty($fmdata)){
			foreach($fmdata as $k=>$v){ //print_r($cucba);  //echo " --- $k \n";
				$fmdata[$k] = @cls_string::iconv("utf-8",$mcharset,$v);
				// ��ѡ�ֶδ������ַ���������,ת��Ϊ�����fields.clsʹ��
				// ��ajax���������ת��,�����ƿ�,ʹ�ñ���$this->cucbvals���� 
				if(in_array($k,$cucba)){ 
					$this->cucbvals[$k] = str_replace(",","\t",$fmdata[$k]);
					unset($fmdata[$k]); //$fmdata[$k] = explode(",",$fmdata[$k]); 
				}
				if(!isset($this->cucfgs[$k])){
					@$this->exfields[$k] = $fmdata[$k];
				}
			} 
		}else{
			;//	
		}
		cls_env::SetG($this->fmpre, $fmdata);
	}
	// Ĭ��init��cfgs
	protected function defCfgs(){
		$_init = array(
			'cuid' => $this->cuid,
			'ptype' => $this->cutype,
			'pchid' => (empty($this->cutype) ? 0 : 1), 
			'url' => '', 
		);
		if($this->fmpre != 'fmdata') $_init['fmpre'] = $this->fmpre;
		return $_init;
	}
	// Ĭ�ϵ�Pids
	protected function defPid(){
		$pid = empty($this->cutype) ? 0 : ($this->cutype=='m' ? $this->tomid : $this->aid);
		return $pid;
	}
	// Cookie�����Ŀ(repeat)
	protected function repCookie(){
		$a = array(); //array('aid'=>$aid,'tocid'=>$tocid)
		if(!empty($this->aid)){
			$a['aid'] = $this->aid;
			if(!empty($this->tocid)) $a['tocid'] = $this->tocid;
		}elseif(!empty($this->tomid)){
			$a['tomid'] = $this->tomid;
		}else{
				
		}
		return $a;
	}
	// ��չ�ֶ���()
	protected function extFields(){
		$a = array('ip'=>$this->ip); //array('aid'=>$aid,'tocid'=>$tocid,'ip'=>$onlineip,)
		if(!empty($this->aid)){
			$a['aid'] = $this->aid;
			if(!empty($this->tocid)) $a['tocid'] = $this->tocid;
		}elseif(!empty($this->tomid)){
			$a['tomid'] = $this->tomid;
			$a['tomname'] = @$this->pinfo['mname'];
		}else{
				
		}
		if(!empty($this->exfields)){
			$a = array_merge($a,$this->exfields);
		}
		if(!empty($this->cucbvals)){
			$a = array_merge($a,$this->cucbvals);
		}
		
		return $a;
		// tocid,ip������Щ����û��...Ҫ��insert����ȥ�жϡ�
	}

}