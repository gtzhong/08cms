<?php
/**
 * 根据后台短信模块启用情况; 获取配置信息,获取验证码,发送短信
 *
 * @example   请求范例URL：/index.php?/ajax/sms_msend/mod/arc2pub/act/
                           /index.php?/ajax/sms_msend/mod/arc2pub/act/code/tel/132233322433
						   /index.php?/ajax/sms_msend/mod/arc2pub/act/send/tel/13223332244/msg/%C4%FA%B5%C4{groupname}%BB%E1%D4%B1%B5%BD%C6%DA%C8%D5%CE%AA{expdate},%CE%AA%B2%BB%D3%B0%CF%EC%C4%FA%D5%FD%B3%A3%CA%B9%D3%C3,%C7%EB%BC%B0%CA%B1%C1%AA%CF%B5%B9%DC%C0%ED%D4%B1%A1%BE{$hostname}%A1%BF
 * @author    Peace <@08cms.com>
 * @copyright Copyright (C) 2008 - 2015 08CMS, Inc. All rights reserved.
 */

defined('_08CMS_APP_EXEC') || exit('No Permission');
class _08_M_Ajax_sms_msend_Base extends _08_Models_Base
{
	public $mod = ''; //公用参数：mod (短信“启用模块”中启用的模块id)
	public $act = 'init'; //init(初始化),code(发送认证码),send(发送信息)
	public $tel = ''; //号码
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
		//安全综合检测
		$re = $this->check_all(); 
		if($re['error']) return $re;
		//执行操作
		$func = "sms_".$this->act;
		return $this->$func();
    }
	
	/* ==================== 短信操作相关方法 ==================== */
	
	// init(初始化)
    public function sms_init()
    {   
		$re = array('error'=>'', 'message'=>'初始化成功');
		$re['tpl'] = $this->tpl;
		$this->check_init('init'); 
		return $re;
	}
	
	// code(发送认证码)
	// tel : 手机(电话)号码
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
			$re['message'] = '发送成功';
			$re['stamp'] = TIMESTAMP;
		}else{
			$re['error'] = 'ErrorSend';	
			$re['message'] = $sre[1];	
		}
		return $re;
	}

	// check(检查认证码是否输入正确)
	// send : 发送时再认证
	// must : 发短信时认证,且必须经过发认证码（根据需要扩展）
	// url : code : 认证码
	// url : stamp : 时间戳
    public function sms_check($send=0,$must=0)
    {   
		$re = array('error'=>'', 'message'=>'');
		@$pass = smscode_pass($this->mod,$this->_get['code'],$this->_get['tel']); //带了tel参数就一起认证
		$isjs = empty($this->_get['isjs']) ? '' : $this->_get['isjs'];
		if($isjs){ //使用js类认证,按它要的规范返回
			if($pass){
				$restr = '';
			}else{ 
				$restr = '认证码错误或超时';
			}
        	return $restr;	
		}elseif($send){ //发送信息认证
			if(empty($cksave) && empty($must)){ //没有经过发认证码过程：sms_code()
				$re['message'] = 'OK';
			}elseif($pass){
				$re['message'] = 'OK';
				$re['tel'] = @$this->_get['tel'];
			}else{ // 如果smscode_mod不为空，发送时再次认证? 
				$re['error'] = 'checkError';
				$re['message'] = '认证码错误或超时';
			}
		}elseif($pass){ 
			$re['message'] = 'OK';
		}else{ 
			$re['error'] = 'checkError';
			$re['message'] = '认证码错误或超时';
		}
		return $re;
	}
	
	// send(发送信息)
	// code : 认证码 --- 执行了发“认证码”操作，需要此参数
	// stamp : 时间戳 --- 执行了发“认证码”操作，需要此参数
	// tel : 手机(电话)号码
	// msg : 短信内容(注意url提交短信内容不能太长,<200汉字为宜)
    public function sms_send()
    {   
		$re = array('error'=>'', 'message'=>'');
		$tel = $this->tel;
		// 发送时再次认证? 
		//$rc = $this->sms_check(1);
		//regcode_pass($rname,$code='')
		$regcode = $this->_get['regcode'];
		//if(!regcode_pass('register',empty($regcode) ? '' : trim($regcode))) cls_message::show('验证码输入错误！',M_REFERER);
		if(!regcode_pass('freesms',empty($regcode) ? '' : trim($regcode))){
			$re['error'] = 'ErrorCode';	
			$re['message'] = '验证码输入错误！';
			return $re;
		}
		$sre = $this->sms->sendTpl($tel,$this->msg,$this->_get,'sadm');
    	if($sre[0]==1){ 
			$re['message'] = '发送成功';
		}else{
			$re['error'] = 'ErrorSend';	
			$re['message'] = $sre[1];	
		}
		//默认清除cookie, 除非!empty(设置：cksmscode_noclear=1)
		$ckkey = 'smscode_'.$this->mod; 
		#$cksmscode_noclear = empty($this->_get['cksmscode_noclear']) ? '0' : $this->_get['cksmscode_noclear'];
		#$cksmscode_noclear || msetcookie($ckkey, '', -3600);
		mclearcookie($ckkey);
		return $re;
	}
	
	/* ==================== 安全检测相关方法 ==================== */
	
	//安全综合检测
    public function check_all()
    {
		//url检测
		$re = $this->check_curl(); 
		if($re['error']) return $re;
		//act检测：//为空或未开启或未定义的操作
		if(empty($this->mod) || !$this->sms->smsEnable($this->mod) || !in_array($this->act,array('init','code','check','send'))){ 
			return array('error'=>'close', 'message'=>'模块关闭或参数不合法');
		}
		//是否sms_init初始化检测
		if(in_array($this->act,array('code','send'))){ 
			$re = $this->check_init('check');
			if($re['res']!=='OK') return array('error'=>'Timout', 'message'=>'超时或未初始化');	
			$this->tel = empty($this->_get['tel']) ? '' : $this->_get['tel'];
			if(!preg_match("/^\d{3,4}[-]?\d{7,8}$/", $this->tel)){
				$re['error'] = 'ErrorNumber';	
				$re['message'] = '号码错误';
				return $re;	
			}
		}
		return array('error'=>'', 'message'=>'');	
	}
	
	//url外部网页提交检测
    public function check_curl()
    {
		$re = array('error'=>'', 'message'=>'');
		$curuser = cls_UserMain::CurUser();
		if($curuser->isadmin()) return $re; //管理员测试不用这个检测
		if($ore = cls_Safefillter::refCheck('',0)){ // die("不是来自{$cms_abs}的请求！");
			return array('error'=>"Outsend", 'message'=>$ore);
		}
		return $re;
	}
	
	//加密cookie:初始化,检测,设置
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