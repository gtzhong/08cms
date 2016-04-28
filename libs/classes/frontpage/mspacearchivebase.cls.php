<?php
/**
 * ���ɻ�Ա�ռ��ĵ�����ҳ�Ĵ������
 *
 */
defined('M_COM') || exit('No Permission');
abstract class cls_MspaceArchiveBase extends cls_FrontPage{
	
	protected static $_ExtendAplicationClass = 'cls_MspaceArchive'; 		# ��ǰ�������չӦ����(������)������
	protected $_Arc = NULL; 												# ��ǰ�ĵ��Ļ���ʵ��
	
	# ҳ�����ɵ��ⲿִ�����
	# ����������ο� function _Init
	public static function Create($Params = array()){
		return self::_iCreate(self::$_ExtendAplicationClass,$Params);
	}

	protected function __construct(){
		parent::__construct();
 		$this->_PageCacheParams['typeid'] = 8; 								# ҳ�滺������
		$this->_Cfg['mspacedisabled'] = cls_env::mconfig('mspacedisabled');	# ��Ա�ռ�ر�
		$this->_Cfg['LoadAdv'] = true;										# �Ƿ���Ҫ���ɹ��js���ô���
	}
	
	# Ӧ��ʵ���Ļ�����ʼ�������붨�壬ÿ��Ӧ�ö������ѵĴ�����
	protected function _Init($Params = array()){
		
		# ����ҳ����		
		$this->_SystemParams['addno'] = isset($Params['addno']) ? $Params['addno'] : @$this->_QueryParams['addno']; # ����ҳ����
		
		# ҳ����������
		foreach(array('mid','aid',) as $k){
			$this->_SystemParams[$k] = isset($Params[$k]) ? $Params[$k] : @$this->_QueryParams[$k];
			$this->_SystemParams[$k] = max(0,intval($this->_SystemParams[$k]));
		}
	}
	
	# ��ͬ����ҳ�������ģ��
	protected function _ModelCumstom(){
		$this->_ReadArchive();# ��ȡ��ǰ�ĵ�������
		$this->_MspaceClosed(); # ��Ա�ռ��Ƿ�ر�
	}
	
	# ��ȡ��ǰ�ĵ�������
	protected function _ReadArchive(){
		
		# ��ʼ����������
		if(!$this->_SystemParams['aid']){
			throw new cls_PageException('��ָ���ĵ�ID');
		}
		$this->_Arc = new cls_arcedit();
		if(!$this->_Arc->set_aid($this->_SystemParams['aid'],array('au'=>0,'ch'=>1))){
			throw new cls_PageException('��ָ����ȷ���ĵ�');
		}
		if(!$this->_Arc->archive['checked'] && !self::$curuser->isadmin()){
			throw new cls_PageException('ָ�����ĵ���δ���');
		}
	}
	
	# ��Ա�ռ��Ƿ�ر�
	protected function _MspaceClosed(){
		if(!empty($this->_Cfg['mspacedisabled'])){
			throw new cls_PageException('��Ա�ռ���ͣ����');
		}	
	}
	
	# ����ҳ��Ŵ���
	protected function _Addno(){
		$this->_SystemParams['addno'] = max(0,intval(@$this->_SystemParams['addno'])); # ��ͨ�����γ�ʼ��
		if($this->_SystemParams['addno'] > 2){
			throw new cls_PageException($this->_PageName()." - ������ĸ���ҳ");
		}
	}
	
	# ҳ�泣��������������ڸ��ӱ���������������ҳUrl�Ƿ���Ҫ��̬���Ƿ���Ҫ����������¼
	protected function _NormalVars(){
		$this->_NormalVars = array('addno','page','mid','aid',);
	}
	
	# ��̬ҳ��������ò���
	protected function _PageCacheConfig(){
		$this->_PageCacheParams['chid'] = $this->_Arc->archive['chid'];
		$this->_PageCacheParams['initdate'] = $this->_Arc->archive['initdate'];
	}
	
	# ��ȡҳ����������
	protected function _MainData(){
		
		# �Ƿ���Ҫ���ɾ�̬��ע�⣺��ʹ��̬ҳ�棬Ҳ��Ҫ����
		$this->_CheckAllowStatic();
		
		# ��ʼ����������
		if(!$this->_SystemParams['mid']){
			throw new cls_PageException('��ָ����ԱID');
		}
		$this->_MainData = cls_Mspace::LoadMember($this->_SystemParams['mid']);
		if(!$this->_MainData){
			throw new cls_PageException('δ�ҵ�ָ���Ļ�Ա');
		}
		
		# δ����֤���ֶ���ղ���ʾ
		cls_UserMain::HiddenUncheckCertField($this->_MainData);
		
		# ����֮ǰ���÷�����ʱ���������Ժ��ģ���У���ȡ�ĵ����ϣ�ʹ���ĵ���ǩ
		$this->_MainData['_arc'] = $this->_Arc->archive;
		cls_ArcMain::Parse($this->_Arc->archive);
		$this->_MainData  += $this->_Arc->archive;
		
	}
	
	# �Ƿ���Ҫ���ɾ�̬
	# ��Ҫ��̬ʱ����ʹ�ڶ�̬ҳ�棬��ҳurlҲҪ����̬������(��ϱ�����̬)
	protected function _CheckAllowStatic(){
		$this->_Cfg['AllowStatic'] = false;
	}
	
	# ���ҳ��ģ��
	protected function _ParseSource(){
		$this->_ParseSource = cls_Mspace::ArchiveTplname($this->_MainData['mtcid'],$this->_Arc->archive['chid'],$this->_SystemParams['addno']);
		if(!$this->_ParseSource){
			throw new cls_PageException($this->_PageName().' - δ��ģ��');
		}
	}
	
	# ��ǰҳ�������Ϣ
	protected function _Mdebug(){
		$_mdebug = cls_env::GetG('_mdebug');
		$_mdebug->setvar('tpl',$this->_ParseSource);
	}
	
	# ȡ�÷�ҳUrl���ø�ʽ
	protected function _UrlPre($isStatic = false){
		if($isStatic){ # ��̬Url���ø�ʽ
			return '';
		}else{ # ��̬Url���ø�ʽ
			$ParamStr = "&mid={$this->_SystemParams['mid']}";
			$ParamStr .= "&aid={$this->_SystemParams['aid']}";
			$ParamStr .= $this->_SystemParams['filterstr'];
			$ParamStr .= "&page={\$page}";
			$ParamStr = substr($ParamStr ,1);
			
			$re = cls_env::mconfig('mspaceurl')."archive.php?".$ParamStr;
			$re = cls_url::en_virtual($re);
		}
		if(!$re) throw new cls_PageException($this->_PageName().' - '.($isStatic ? '��̬' : '��̬').'URL��ʽ����');
		$re = cls_url::view_url($re);
		return $re;
	}
	
	# ����ToolJs�Ĳ������飬ֻ��page=1ʱ����
	protected function _ToolParams(){
		if($this->_SystemParams['page'] == 1){
			$_ToolParams = array('mid' => $this->_SystemParams['mid'],'aid' => $this->_SystemParams['aid'],);
		}
		return @$_ToolParams;
	}
	
	# ҳ������
	protected function _PageName(){
		return "��Ա�ռ�[{$this->_SystemParams['mid']}]";
	}
	
}
