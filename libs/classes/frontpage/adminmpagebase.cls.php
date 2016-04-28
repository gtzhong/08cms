<?php
/**
 * ��Ա���Ľű�����ģ���ǩ�Ĵ������
 * ������̬����ҳ������ҳ
 */
 defined('M_COM') || exit('No Permission');
abstract class cls_AdminmPageBase extends cls_FrontPage{
	
 	protected static $_ExtendAplicationClass = 'cls_AdminmPage'; 	# ��ǰ�������չӦ����(������)������
 	protected static $_UserChecked = false; 						# �Ƿ���ִ���˻�Ա���(ֻ��Ҫ��entry.php�м���Ա����)

	# ҳ�����ɵ��ⲿִ�����
	# ����������ο� function _Init
	public static function Create($Params = array()){
		return self::_iCreate(self::$_ExtendAplicationClass,$Params);
	}

	protected function __construct(){
		parent::__construct();
		$this->_SourceType = 'adminm'; # ��Դ����
	}
	
	# Ӧ��ʵ���Ļ�����ʼ�������붨�壬ÿ��Ӧ�ö������ѵĴ�����
	# �Ƿ�Ϊ���ģ��
	# �Ƿ��ڲ�����($action.inc.php)
	# �Ƿ񷵻ؽ��
	# ҳ����������(��aid,cnstr,tname��ҳ������)
	protected function _Init($Params = array()){
		
		$this->_Cfg['isEntry'] = empty($Params['isEntry']) ? false : true; # �Ƿ�Ϊ���ģ��
		$this->_Cfg['DynamicReturn'] = empty($Params['DynamicReturn']) ? false : true; # ��̬���������(true)/���(false)
		$this->_Cfg['SonBlockOfPage'] = empty($this->_Cfg['isEntry']);
		# ҳ����������
		$this->_SystemParams['action'] = isset($this->_QueryParams['action']) ? $this->_QueryParams['action'] : ''; # �õ�action
	}
	
	# ��ͬ����ҳ�������ģ��
	protected function _ModelCumstom(){
		$this->_Action(); # ����action
		$this->_UserCheck(); # ��ʼ����Ա����
	}
	
	# ����ģ�͵�ͨ�ò���
	protected function _ModelCommon(){
		$this->_ParseSource(); # ���ҳ��ģ��
		$this->_Mdebug(); # ��ǰҳ�������Ϣ
	}
	
	# ����action
	protected function _Action(){
		_08_FilesystemFile::filterFileParam($this->_SystemParams['action']);
		$this->_SystemParams['action'] = empty($this->_SystemParams['action']) ? 'wjindex' : $this->_SystemParams['action'];
	}
	
	# ��ʼ����Ա����
	protected function _UserCheck(){
		
		if(empty($this->_Cfg['isEntry'])){
			if(empty(self::$_UserChecked)){
				throw new cls_PageException('δ����Ա����');
			}
		}else{
			self::$curuser->detail_data();	# ͳһ��ȡ������Ա����
			self::$curuser->mcTrustee();	# ��Ա���Ĵ���
			mc_allow();						# ��֤�����Ա���ĵ�Ȩ��
			self::$_UserChecked = true;
			
		}
		
	}
	# ���ҳ��ģ��
	protected function _ParseSource(){
		if(empty($this->_Cfg['isEntry'])){
			$this->_ParseSource = $this->_SystemParams['action'].'.inc.php';
		}else{
			$this->_ParseSource = 'entry.php';
		}
	}
	
	# ��ǰҳ�������Ϣ
	protected function _Mdebug(){
		$_mdebug = cls_env::GetG('_mdebug');
		$_mdebug->setvar('tpl',$this->_ParseSource);
	}
	
	
	# ҳ������
	protected function _PageName(){
		return $this->_SystemParams['cnstr'] ? "�ڵ�[{$this->_SystemParams['cnstr']}]" : "ϵͳ��ҳ";
	}
	
}
