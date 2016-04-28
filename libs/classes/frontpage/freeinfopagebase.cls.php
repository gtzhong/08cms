<?php
/**
 * ����ҳ�Ĵ������
 * ��֧�ָ���ҳ
 */
defined('M_COM') || exit('No Permission');
abstract class cls_FreeinfoPageBase extends cls_FrontPage{
	
  	protected static $_ExtendAplicationClass = 'cls_FreeinfoPage'; 	# ��ǰ�������չӦ����(������)������
 	protected $Freeinfo = array(); 								# ָ������ҳ����
	
	# ҳ�����ɵ��ⲿִ�����
	# ����������ο� function _Init
	public static function Create($Params = array()){
		return self::_iCreate(self::$_ExtendAplicationClass,$Params);
	}

	protected function __construct(){
		parent::__construct();
 		$this->_PageCacheParams['typeid'] = 3; 						# ҳ�滺������
		$this->_Cfg['LoadAdv'] = true;										# �Ƿ���Ҫ���ɹ��js���ô���
	}
	
	# Ӧ��ʵ���Ļ�����ʼ��
	protected function _Init($Params = array()){
		$this->_inStatic = empty($Params['inStatic']) ? false : true; 	# �Ƿ�̬	
		
		# ҳ����������
		$this->_SystemParams['fid'] = 0;
		if(isset($Params['fid'])){
			$this->_SystemParams['fid'] = $Params['fid'];
		}elseif(isset($this->_QueryParams['fid'])){
			$this->_SystemParams['fid'] = $this->_QueryParams['fid'];
		}
	}
	
	# ��ͬ����ҳ�������ģ��
	protected function _ModelCumstom(){
		$this->_ReadFreeinfo(); # ��ȡ����ҳ����
	}
	
	# ��ȡ����ҳ����
	protected function _ReadFreeinfo(){
		$this->_SystemParams['fid'] = cls_FreeInfo::InitID($this->_SystemParams['fid']);
		if(!($this->Freeinfo = cls_FreeInfo::Config($this->_SystemParams['fid']))){
			throw new cls_PageException('����ҳ'.$this->_SystemParams['fid'].'δ����');
		}
	}
	
	# ҳ�泣��������������ڸ��ӱ���������������ҳUrl�Ƿ���Ҫ��̬���Ƿ���Ҫ����������¼
	protected function _NormalVars(){
		if($this->_inStatic) return;
		$this->_NormalVars = array('fid','page',);
	}
	
	# ��ȡҳ����������
	protected function _MainData(){
		$this->_CheckAllowStatic(); 			# �Ƿ���Ҫ���ɾ�̬��ע�⣺��ʹ��̬ҳ�棬Ҳ��Ҫ����
	}
	
	# �Ƿ���Ҫ��̬
	# ��ҳurl�뵱ǰҳ�涯��̬��һ�µ�(����Ҫ������̬)
	protected function _CheckAllowStatic(){
		$this->_Cfg['AllowStatic'] = empty($this->Freeinfo['canstatic']) ? false : true;			# �����ܿ��ؿ��ƣ�ÿ������ҳ�ֱ����
		$this->_Cfg['MpUrlStatic']	= $this->_inStatic;												# ��ҳUrl��̬ԭ�򣺶��鶯�����龲
		$this->_CheckStatic();
	}
	
	# ���ҳ��ģ��
	protected function _ParseSource(){
		
		$this->_ParseSource = @$this->Freeinfo['tplname'];
		if(!$this->_ParseSource){
			throw new cls_PageException($this->_PageName().' - δ��ģ��');
		}
	}
	
	# ��ǰҳ�������Ϣ
	protected function _Mdebug(){
		$_mdebug = cls_env::GetG('_mdebug');
		$_mdebug->setvar('tpl',$this->_ParseSource);
		if($this->_inStatic){ # ��̬ʱ�����̬Url
			$ParamStr = "&fid={$this->_SystemParams['fid']}";
			$ParamStr = substr($ParamStr ,1);
			$_mdebug->setvar('uri',"info.php?$ParamStr");
			cls_env::SetG('_no_dbhalt',true); # ��̬ʱ�ر�SQL�жϴ��� ????
		}
	}
	
	# ȡ�÷�ҳUrl���ø�ʽ
	protected function _UrlPre($isStatic = false){
		if($isStatic){ # ��̬Url���ø�ʽ
			if(!$this->_inStatic) return '';
			$re = $this->_StaticFilePre();
		}else{ # ��̬Url���ø�ʽ
			if($this->_inStatic) return '';
			$ParamStr = "&fid={$this->_SystemParams['fid']}";
			$ParamStr .= $this->_SystemParams['filterstr'];
			$ParamStr .= "&page={\$page}";
			$ParamStr = substr($ParamStr ,1);
			
			$re = "info.php?".$ParamStr;
			$re = cls_url::en_virtual($re);
		}
		if(!$re) throw new cls_PageException($this->_PageName().' - '.($isStatic ? '��̬' : '��̬').'URL��ʽ����');
		$re = cls_url::view_url($re);
		return $re;
	}
	
	# ����ToolJs�Ĳ������飬ֻ��page=1ʱ����
	protected function _ToolParams(){
		if($this->_SystemParams['page'] == 1){
			$_ToolParams = array();
			if(!$this->_inStatic) $_ToolParams['upsen'] = 1;
		}
		return @$_ToolParams;
	}
	
	# ���ɾ�̬����������Ϣ(ʱ�䣬Url��)�������¼
	protected function _UpdateStaticRecord(){
		if(!$this->_inStatic) return;
		$StaticFilePre = $this->_StaticFilePre();
		$StaticFile = cls_url::m_parseurl($StaticFilePre,array('page' => 1));
		cls_FreeInfo::ModifyOneConfig(array('arcurl' => addslashes($StaticFile)),$this->_SystemParams['fid']);
	}
	
	# ȡ�÷�ҳ_��̬�ļ������ʽ
	protected function _StaticFilePre(){
		if(!isset($this->_Cfg['_StaticFilePre'])){ # ��Ҫ�ظ�ʹ��
			$this->_Cfg['_StaticFilePre'] = cls_FreeInfo::_StaticFormat($this->_SystemParams['fid']);
		}
		return $this->_Cfg['_StaticFilePre'];
	}
	
	# ҳ������
	protected function _PageName(){
		return "����ҳ[{$this->_SystemParams['fid']}]";
	}
	
}
