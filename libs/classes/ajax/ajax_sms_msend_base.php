<?php
/**
 * ���ݺ�̨����ģ���������; ��ȡ������Ϣ,��ȡ��֤��,���Ͷ���
 *
 * @example   ������URL��/index.php?/ajax/sms_msend/mod/arc2pub/act/
                           /index.php?/ajax/sms_msend/mod/arc2pub/act/code/tel/132233322433
						   /index.php?/ajax/sms_msend/mod/arc2pub/act/send/tel/13223332244/msg/%C4%FA%B5%C4{groupname}%BB%E1%D4%B1%B5%BD%C6%DA%C8%D5%CE%AA{expdate},%CE%AA%B2%BB%D3%B0%CF%EC%C4%FA%D5%FD%B3%A3%CA%B9%D3%C3,%C7%EB%BC%B0%CA%B1%C1%AA%CF%B5%B9%DC%C0%ED%D4%B1%A1%BE{$hostname}%A1%BF
 * @author    Peace <@08cms.com>
 * @copyright Copyright (C) 2008 - 2015 08CMS, Inc. All rights reserved.
 */

defined('_08CMS_APP_EXEC') || exit('No Permission');
class _08_M_Ajax_sms_msend_Base extends _08_Models_Base
{
	public $mod = ''; //���ò�����mod (���š�����ģ�顱�����õ�ģ��id)
	public $act = 'init'; //init(��ʼ��),code(������֤��),send(������Ϣ)
	public $tel = ''; //����
	public $tpl = ''; 
	//private $re = array('error'=>'', 'message'=>'');
	public $sms = null; //new cls_sms();
	
    public function __toString()
    {       
		$this->mod = empty($this->_get['mod']) ? '' : $this->_get['mod'];
		$this->act = empty($this->_get['act']) ? 'init' : $this->_get['act'];
		$this->sms = new cls_sms();
		$this->tpl = $this->sms->smsTpl($this->mod);
		$this->msg = empty($this->_get['msg']) ? $this->tpl : cls_string::iconv('utf-8',cls_env::getBaseIncConfigs('mcharset'),$this->_get['msg']); 
		//��ȫ�ۺϼ��
		$re = $this->check_all(); 
		if($re['error']) return $re;
		//ִ�в���
		$func = "sms_".$this->act;
		return $this->$func();
    }
	
	/* ==================== ���Ų�����ط��� ==================== */
	
	// init(��ʼ��)
    public function sms_init()
    {   
		$re = array('error'=>'', 'message'=>'��ʼ���ɹ�');
		$re['tpl'] = $this->tpl;
		$this->check_init('init'); 
		return $re;
	}
	
	// code(������֤��)
	// tel : �ֻ�(�绰)����
    public function sms_code()
    {   
		$re = array('error'=>'', 'message'=>'');
		$code = cls_string::Random(6, 1); 
		$tel = $this->tel;
		$msg = str_replace(array('%s','{$smscode}'), $code, $this->msg);
		$sre = $this->sms->sendSMS($tel,$msg,'ctel'); //print_r($sre);
    	if($sre[0]==1){ 
			global $m_cookie; 
			$ckkey = 'smscode_'.$this->mod; 
			$cksave = authcode(TIMESTAMP."\t$code\t$tel", 'ENCODE'); 
			msetcookie($ckkey, $cksave, 3600); 
			$re['message'] = '���ͳɹ�';
			$re['stamp'] = TIMESTAMP;
		}else{
			$re['error'] = 'ErrorSend';	
			$re['message'] = $sre[1];	
		}
		return $re;
	}

	// check(�����֤���Ƿ�������ȷ)
	// send : ����ʱ����֤
	// must : ������ʱ��֤,�ұ��뾭������֤�루������Ҫ��չ��
	// url : code : ��֤��
	// url : stamp : ʱ���
    public function sms_check($send=0,$must=0)
    {   
		$re = array('error'=>'', 'message'=>'');
		@$pass = smscode_pass($this->mod,$this->_get['code'],$this->_get['tel']); //����tel������һ����֤
		$isjs = empty($this->_get['isjs']) ? '' : $this->_get['isjs'];
		if($isjs){ //ʹ��js����֤,����Ҫ�Ĺ淶����
			if($pass){
				$restr = '';
			}else{ 
				$restr = '��֤������ʱ';
			}
        	return $restr;	
		}elseif($send){ //������Ϣ��֤
			if(empty($cksave) && empty($must)){ //û�о�������֤����̣�sms_code()
				$re['message'] = 'OK';
			}elseif($pass){
				$re['message'] = 'OK';
				$re['tel'] = @$this->_get['tel'];
			}else{ // ���smscode_mod��Ϊ�գ�����ʱ�ٴ���֤? 
				$re['error'] = 'checkError';
				$re['message'] = '��֤������ʱ';
			}
		}elseif($pass){ 
			$re['message'] = 'OK';
		}else{ 
			$re['error'] = 'checkError';
			$re['message'] = '��֤������ʱ';
		}
		return $re;
	}
	
	// send(������Ϣ)
	// code : ��֤�� --- ִ���˷�����֤�롱��������Ҫ�˲���
	// stamp : ʱ��� --- ִ���˷�����֤�롱��������Ҫ�˲���
	// tel : �ֻ�(�绰)����
	// msg : ��������(ע��url�ύ�������ݲ���̫��,<200����Ϊ��)
    public function sms_send()
    {   
		$re = array('error'=>'', 'message'=>'');
		$tel = $this->tel;
		// ����ʱ�ٴ���֤? 
		//$rc = $this->sms_check(1);
		//regcode_pass($rname,$code='')
		$regcode = $this->_get['regcode'];
		//if(!regcode_pass('register',empty($regcode) ? '' : trim($regcode))) cls_message::show('��֤���������',M_REFERER);
		if(!regcode_pass('freesms',empty($regcode) ? '' : trim($regcode))){
			$re['error'] = 'ErrorCode';	
			$re['message'] = '��֤���������';
			return $re;
		}
		$sre = $this->sms->sendTpl($tel,$this->msg,$this->_get,'sadm');
    	if($sre[0]==1){ 
			$re['message'] = '���ͳɹ�';
		}else{
			$re['error'] = 'ErrorSend';	
			$re['message'] = $sre[1];	
		}
		//Ĭ�����cookie, ����!empty(���ã�cksmscode_noclear=1)
		$ckkey = 'smscode_'.$this->mod; 
		#$cksmscode_noclear = empty($this->_get['cksmscode_noclear']) ? '0' : $this->_get['cksmscode_noclear'];
		#$cksmscode_noclear || msetcookie($ckkey, '', -3600);
		mclearcookie($ckkey);
		return $re;
	}
	
	/* ==================== ��ȫ�����ط��� ==================== */
	
	//��ȫ�ۺϼ��
    public function check_all()
    {
		//url���
		$re = $this->check_curl(); 
		if($re['error']) return $re;
		//act��⣺//Ϊ�ջ�δ������δ����Ĳ���
		if(empty($this->mod) || !$this->sms->smsEnable($this->mod) || !in_array($this->act,array('init','code','check','send'))){ 
			return array('error'=>'close', 'message'=>'ģ��رջ�������Ϸ�');
		}
		//�Ƿ�sms_init��ʼ�����
		if(in_array($this->act,array('code','send'))){ 
			$re = $this->check_init('check');
			if($re['res']!=='OK') return array('error'=>'Timout', 'message'=>'��ʱ��δ��ʼ��');	
			$this->tel = empty($this->_get['tel']) ? '' : $this->_get['tel'];
			if(!preg_match("/^\d{3,4}[-]?\d{7,8}$/", $this->tel)){
				$re['error'] = 'ErrorNumber';	
				$re['message'] = '�������';
				return $re;	
			}
		}
		return array('error'=>'', 'message'=>'');	
	}
	
	//url�ⲿ��ҳ�ύ���
    public function check_curl()
    {
		$re = array('error'=>'', 'message'=>'');
		$curuser = cls_UserMain::CurUser();
		if($curuser->isadmin()) return $re; //����Ա���Բ���������
		if($ore = cls_Safefillter::refCheck('',0)){ // die("��������{$cms_abs}������");
			return array('error'=>"Outsend", 'message'=>$ore);
		}
		return $re;
	}
	
	//����cookie:��ʼ��,���,����
    public function check_init($act='init',$data='')
    {
		global $authorization, $m_cookie; //TIMESTAMP;
		$ckkey = 'smsinit_'.$this->mod; 
		$re = 'OK'; $ckval = '';
		if($act=='init'){
			$ckval = TIMESTAMP.':'.md5(TIMESTAMP."$ckkey$authorization");
			msetcookie($ckkey, $ckval, 3600); //echo "$ckkey, $ckval";
		}elseif($act=='check'){ 
			$ckval = @$m_cookie[$ckkey].":";
			$ckarr = explode(':',$ckval);
			$ckchk = md5($ckarr[0]."$ckkey$authorization");
			$re = strstr($ckval,$ckchk) ? 'OK' : 'Error'; 
		} //echo $ckval;
		return array('res'=>$re,'val'=>$ckval);
	}
	
	// var _login_stamp = '8e5c3f48a07722bdf4208b7d42a378d8~mKxxqRTodQMnr+ah5IbSVyPE'; 
	
}