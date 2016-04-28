<?php
/**
* ucenter��Ա������
* 
*/
class cls_ucenter{
    /**
     * UCenter������ڱ�ϵͳ�����ݱ�����û��ֶα�ʶ��
     * 
     * @var string
     */
    const UC_UID = 'uc_uid';
    
	function __construct(){//���ã��п���֮����Ҫnew
		self::init();
	}
	
	//��ʼ��
	public static function init(){
		if(!cls_env::mconfig('enable_uc')) return false;
		if(!defined('UC_CONNECT')){
			self::_define_cont();
			require_once _08_INCLUDE_PATH . 'uc_client/client.php';
		}
		return true;
	}
	
	//��Աͬ���˳�
	public static function logout(){
		if(!self::init()) return;
		self::_hidden(uc_user_synlogout());
	}
	
	//��Աͬ����¼
	public static function login($uid){
		if(!self::init() || $uid <= 0) return false;
		self::_hidden(uc_user_synlogin($uid));
		return true;
	}
	
	//ɾ����Ա
	public static function delete($mnames = array()){
		if(!self::init()) return false;
		if(!$mnames) return false;
		$uids = array();
		foreach($mnames as $k){
			$re = uc_get_user($k);
			is_array($re) && $uids[] = $re[0];
		}
		$uids && uc_user_delete($uids);
		return true;
	}
	
	//�������û���ע�ᣬ$synloginע��ɹ���ͬ����¼
	//���ش�����Ϣ��uid
	public static function register($username,$password,$email = '',$synlogin = FALSE)
    {
		$re = array('error' => '');
		if(!self::init()) return $re['error'];
		$uid = uc_user_register($username,$password,$email);
		if($uid <= 0) {
			if($uid == -1) {
				$re['error'] = '[Ucenter] �û������Ϸ�';
			}elseif($uid == -2) {
				$re['error'] = '[Ucenter] ����������ע��Ĵ���';
			}elseif($uid == -3) {
				$re['error'] = '[Ucenter] �û����Ѿ�����';
			}elseif($uid == -4) {
				$re['error'] =  '[Ucenter] Email��ʽ����';
			}elseif($uid == -5) {
				$re['error'] = '[Ucenter] Email������ע��';
			}elseif($uid == -6) {
				$re['error'] = '[Ucenter] ��Email�Ѿ���ע��';
			}else {
				$re['error'] = '[Ucenter] �������';
			}
            cls_message::show( $re['error'], M_REFERER );
		}elseif($synlogin){//��¼�ɹ���Ҫͬ����¼
			self::login($uid);
		}
		return $uid;
	}
	
	//�޸����뼰email�����޸�����$newpw��$email
	//���ش�����Ϣ
	public static function edit($username,$newpw = '',$email = ''){
		$re = '';	
		if(!self::init()) return $re;
		if(!$newpw && !$email) return $re;
		$ucre = uc_user_edit($username, '', $newpw, $email, 1);
		switch($ucre){
	#		case 0:
			case -1:
				$re = '[Ucenter] �޸�ʧ��';
				#$re = '[Ucenter] �����Emailû�����κ��޸�';
			break;
			case -4:
				$re = '[Ucenter] Email��ʽ����';
			break;
			case -5:
				$re = '[Ucenter] Email������ע��';
			break;
			case -6:
				$re = '[Ucenter] Email�Ѿ���ע��';
			break;
			case -7:
				$re = '[Ucenter] ���û��ܱ�����Ȩ�޸���';
			break;
		}
		return $re;		
	}
	
	//�û��ĵ�¼��֤����UC�����ڸû�Ա(-1)��������ע���»�Ա��UC������uid>0��udi=-1ʱ����Ҫ���ش���
	//��Ҫ����UC�����ϣ����ڱ�վ�ӻ�Ա����±�վ��Ա����
	public static function checklogin($username,$password){
		$re = array('error' => '');
		if(!self::init()) return $re;
		list($re['uid'], $re['username'], $re['password'], $re['email']) = uc_user_login($username,$password);
		if($re['uid'] > 0) {
			# '��¼�ɹ�';
		} elseif($re['uid'] == -1) {
			# '�û�������,���߱�ɾ��';
		} elseif($re['uid'] == -2) {
			$re['error'] = '[Ucenter] �������';
		} else {
			$re['error'] = '[Ucenter] δ����';
		}
        
		return $re;
	}
	
	//���ڼ���û�������û����ĺϷ��ԣ����ش�����Ϣ
	public static function checkname($mname){
		$re = '';	
		if(!self::init()) return $re;
		$uid = uc_user_checkname($mname);
		switch($uid){
			case -1:
				$re = '[Ucenter] �û������Ϸ�';
			break;
			case -2:
				$re = '[Ucenter] ����������ע��Ĵ���';
			break;
			case -3:
				$re = '[Ucenter] �û����Ѿ�����';
			break;
		}
		return $re;		
	}
	
	//���ڼ���û������ Email �ĺϷ��ԣ����ش�����Ϣ
	public static function checkemail($email){
		$re = '';	
		if(!self::init()) return $re;
		$ucresult  = uc_user_checkemail($email);
		switch($ucresult ){
			case -4:
				$re = '[Ucenter] Email ��ʽ����';
			break;
			case -5:
				$re = '[Ucenter] Email ������ע��';
			break;
			case -6:
				$re = '[Ucenter] �� Email �Ѿ���ע��';
			break;
		}
		return $re;		
	}
	
	private static function _hidden($html){
		echo "<div style=\"display:none\">$html</div>";
	}
	
	
	private static function _define_cont(){
		define('UC_CONNECT', cls_env::mconfig('uc_connect')); //mysql/post
		define("UC_DBHOST", cls_env::mconfig('uc_dbhost')) ;
		define("UC_DBUSER", cls_env::mconfig('uc_dbuser')) ;
		define("UC_DBPW", cls_env::mconfig('uc_dbpwd')) ;
		define("UC_DBNAME", cls_env::mconfig('uc_dbname')) ;
		define('UC_DBCHARSET', cls_env::GetG('dbcharset'));
		define("UC_DBTABLEPRE", '`'.cls_env::mconfig('uc_dbname').'`.'.cls_env::mconfig('uc_dbpre')) ;
		define('UC_DBCONNECT', '0');
		define("UC_KEY", cls_env::mconfig('uc_key')) ;
		define("UC_API", cls_env::mconfig('uc_api')) ;
		define('UC_CHARSET', cls_env::getBaseIncConfigs('mcharset'));
		define("UC_IP", cls_env::mconfig('uc_ip')) ;
		define('UC_APPID', cls_env::mconfig('uc_appid')) ;
		define('UC_PPP', '20');
	}
}
