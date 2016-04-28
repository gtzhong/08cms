<?php
/**
 * ��������ҳ�Ĵ������
 * ��֧�ָ���ҳ
 */
defined('M_COM') || exit('No Permission');
abstract class cls_FarchivePageBase extends cls_FrontPage{
	
  	protected static $_ExtendAplicationClass = 'cls_FarchivePage'; 	# ��ǰ�������չӦ����(������)������
	protected $_Arc = NULL; 										# ��ǰ�����Ļ���ʵ��
	
	# ҳ�����ɵ��ⲿִ�����
	# ִ�о�̬��inStatic => true
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
			
		if(!empty($Params['arc'])){
			$this->_Arc = $Params['arc']; # ע�⣺ʵ��������
			$this->_SystemParams['aid'] = $this->_Arc->aid;
		}else{
			$this->_SystemParams['aid'] = isset($Params['aid']) ? $Params['aid'] : @$this->_QueryParams['aid'];
		}
	}
	
	# ��ͬ����ҳ�������ģ��
	protected function _ModelCumstom(){
		$this->_ReadArchive(); # ��ȡ��������
	}
	
	# ��ȡ��������
	protected function _ReadArchive(){
		$this->_SystemParams['aid'] = max(0,intval($this->_SystemParams['aid']));
		
		if(!$this->_SystemParams['aid']){
			throw new cls_PageException($this->_PageName()." - δָ������ID");
		}
		
		if(empty($this->_Arc)){
			$this->_Arc = new cls_farcedit();
			if(!$this->_Arc->set_aid($this->_SystemParams['aid'],0)){
				throw new cls_PageException($this->_PageName()." - ����������");
			}
		}
		
		# ����Ƿ����
		$this->_ArcChecked();
		
		# �����Ч��
		$this->_ArcValid();
	}
	
	# ����Ƿ����
	protected function _ArcChecked(){
		if(!$this->_Arc->archive['checked']){
			if($this->_inStatic || !self::$curuser->isadmin()){
				throw new cls_PageException($this->_PageName()." - ����δͨ�����");
			}
		}
	}
	
	# �����Ч��
	protected function _ArcValid(){
		if(($this->_Arc->archive['startdate'] > self::$timestamp) || ($this->_Arc->archive['enddate'] && $this->_Arc->archive['enddate'] < self::$timestamp)){
			throw new cls_PageException("ָ������������Ч��");
		}
	}
	
	# ����ҳ��Ŵ���
	protected function _Addno(){
		$this->_SystemParams['addno'] = 0;
	}
	
	# �Ƿ���Ҫ��̬
	# ��ҳurl�뵱ǰҳ�涯��̬��һ�µ�(����Ҫ������̬)
	protected function _CheckAllowStatic(){
		$this->_Cfg['AllowStatic'] = true;								# Ŀǰ���и������ݶ��������ɾ�̬�������ܿ��ص�����
		$this->_Cfg['MpUrlStatic']	= $this->_inStatic;					# ��ҳUrl��̬ԭ�򣺶��鶯�����龲
		$this->_CheckStatic();
	}
	
	# ҳ�泣��������������ڸ��ӱ���������������ҳUrl�Ƿ���Ҫ��̬���Ƿ���Ҫ����������¼
	protected function _NormalVars(){
		if($this->_inStatic) return;
		$this->_NormalVars = array('aid','page',);
	}
	
	# ��ȡҳ����������
	protected function _MainData(){
		$this->_CheckAllowStatic(); # �Ƿ���Ҫ���ɾ�̬��ע�⣺��ʹ��̬ҳ�棬Ҳ��Ҫ����
		cls_url::arr_tag2atm($this->_Arc->archive,'f');
		$this->_MainData = $this->_Arc->archive;
	}
	
	# ���ҳ��ģ��
	protected function _ParseSource(){
		$this->_ParseSource = cls_tpl::CommonTplname('farchive',$this->_Arc->archive['fcaid'],'arctpl');
		if(!$this->_ParseSource){
			throw new cls_PageException($this->_PageName().' - δ��ģ��');
		}
	}
	
	# ��ǰҳ�������Ϣ
	protected function _Mdebug(){
		$_mdebug = cls_env::GetG('_mdebug');
		$_mdebug->setvar('tpl',$this->_ParseSource);
		if($this->_inStatic){ # ��̬ʱ�����̬Url
			$ParamStr = "&aid={$this->_SystemParams['aid']}";
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
			$ParamStr = "&aid={$this->_SystemParams['aid']}";
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
	
	# ���ɾ�̬����������Ϣ(ʱ�䣬Url��)�������¼
	protected function _UpdateStaticRecord(){
		if(!$this->_inStatic) return;
		$StaticFilePre = $this->_StaticFilePre();
		$StaticFile = cls_url::m_parseurl($StaticFilePre,array('page' => 1));
		self::$db->query("UPDATE ".self::$tblprefix."farchives SET arcurl='$StaticFile' WHERE aid='".$this->_SystemParams['aid']."'");
	}
	
	# ȡ�÷�ҳ_��̬�ļ������ʽ
	protected function _StaticFilePre(){
		if(!isset($this->_Cfg['_StaticFilePre'])){ # ��Ҫ�ظ�ʹ��
			$this->_Cfg['_StaticFilePre'] = $this->_Arc->arcformat();
		}
		return $this->_Cfg['_StaticFilePre'];
	}
	
	# ҳ������
	protected function _PageName(){
		return "��������ҳ[{$this->_SystemParams['aid']}]";
	}
	
}
