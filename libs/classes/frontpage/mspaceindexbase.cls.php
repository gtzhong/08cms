<?php
/**
 * ���ɻ�Ա�ռ侲̬ҳ�Ĵ������
 *
 */
defined('M_COM') || exit('No Permission');
abstract class cls_MspaceIndexBase extends cls_FrontPage{
	
	protected static $_ExtendAplicationClass = 'cls_MspaceIndex'; 			# ��ǰ�������չӦ����(������)������
	
	# ҳ�����ɵ��ⲿִ�����
	# ����������ο� function _Init
	public static function Create($Params = array()){
		return self::_iCreate(self::$_ExtendAplicationClass,$Params);
	}

	protected function __construct(){
		parent::__construct();
 		$this->_PageCacheParams['typeid'] = 7; 								# ҳ�滺������
		$this->_Cfg['maxStaicPage'] = 1; 									# ע�⣺��Ա�ռ�ֻ���ɵ�һ��ҳ��ľ�̬
		$this->_Cfg['mspacedisabled'] = cls_env::mconfig('mspacedisabled');	# ��Ա�ռ�ر�
		$this->_Cfg['LoadAdv'] = true;										# �Ƿ���Ҫ���ɹ��js���ô���
	}
	
	# �ռ侲̬�����ɽ�����ظ�ʽ������ҳ�治ͬ
	protected function _StaticAllPage(){
		$_start_time = microtime(TRUE);
		
		$PageByteSize = 0;
		$maxStaticPage = 1;
		for($this->_SystemParams['page'] = 1;$this->_SystemParams['page'] <= $maxStaticPage;$this->_SystemParams['page'] ++){
			try{
				$re = $this->_CreateOnePage();
			}catch(cls_PageException $e){
				return array('error' => $e->getMessage());
			}
			if($error = $this->_SaveStaticFile($re['content'])){
				return array('error' => $error);
			}
			$PageByteSize += strlen($re['content']);
			$maxStaticPage = $this->_MaxStaticPageNo(@$re['pcount']);
		}
		cls_env::SetG('_no_dbhalt',false); # ��̬ʱ�ر�SQL�����ж� ????
		
		# ��ȷ������ɺ�ķ�����Ϣ
		$Result = array(
			'num' => $this->_Cfg['maxStaicPage'],
			'time' => round(microtime(TRUE) - $_start_time,2),
			'size' => $PageByteSize,
		);
		return $Result;
	}
	
	# Ӧ��ʵ���Ļ�����ʼ�������붨�壬ÿ��Ӧ�ö������ѵĴ�����
	protected function _Init($Params = array()){
		
		# �Ƿ�̬
		$this->_inStatic = empty($Params['inStatic']) ? false : true; # �Ƿ�̬

		# ����ҳ����		
		$this->_SystemParams['addno'] = $this->_inStatic || isset($Params['addno']) ? @$Params['addno'] : @$this->_QueryParams['addno']; # ����ҳ����
		
		# ҳ����������
		foreach(array('mid','mcaid','ucid',) as $k){
			$this->_SystemParams[$k] = $this->_inStatic || isset($Params[$k]) ? @$Params[$k] : @$this->_QueryParams[$k];
			$this->_SystemParams[$k] = max(0,intval($this->_SystemParams[$k]));
		}
	}
	
	# ��ͬ����ҳ�������ģ��
	protected function _ModelCumstom(){
		$this->_MspaceClosed(); # ��Ա�ռ��Ƿ�ر�
	}
	
	# ��Ա�ռ��Ƿ�ر�
	protected function _MspaceClosed(){
		if($this->_inStatic) return;
		if(!empty($this->_Cfg['mspacedisabled'])){
				throw new cls_PageException('��Ա�ռ���ͣ����');
		}	
	}
	
	# ����ҳ��Ŵ���
	protected function _Addno(){
		$this->_SystemParams['addno'] = max(0,intval(@$this->_SystemParams['addno'])); # ��ͨ�����γ�ʼ��
		if($this->_SystemParams['addno'] > 1){
			throw new cls_PageException($this->_PageName()." - ������ĸ���ҳ");
		}
	}
	
	# ҳ�泣��������������ڸ��ӱ���������������ҳUrl�Ƿ���Ҫ��̬���Ƿ���Ҫ����������¼
	protected function _NormalVars(){
		if($this->_inStatic) return;
		$this->_NormalVars = array('addno','page','mid','mcaid','ucid',);
	}
	
	# ��ȡҳ����������
	protected function _MainData(){
		
		# ��ʼ����������
		if(!$this->_SystemParams['mid']){
			throw new cls_PageException('��ָ����ԱID');
		}
		$this->_MainData = cls_Mspace::LoadMember($this->_SystemParams['mid'],0,0);
		if(!$this->_MainData){
			throw new cls_PageException('δ�ҵ�ָ���Ļ�Ա');
		}elseif(empty($this->_MainData['checked'])){
			throw new cls_PageException('��Աδ���');
		
		}
		
		# �Ƿ���Ҫ���ɾ�̬��ע�⣺��ʹ��̬ҳ�棬Ҳ��Ҫ����
		$this->_CheckAllowStatic();
		
		# δ����֤���ֶ���ղ���ʾ
		cls_UserMain::HiddenUncheckCertField($this->_MainData);
		
		# ׷��ģ��ԭʼ��ʶ����
		$this->_MainData += cls_Mspace::IndexAddParseInfo($this->_MainData,$this->_AddParams());
	}
	
	# �Ƿ���Ҫ���ɾ�̬
	# ��Ҫ��̬ʱ����ʹ�ڶ�̬ҳ�棬��ҳurlҲҪ����̬������(��ϱ�����̬)
	protected function _CheckAllowStatic(){
		
		if(empty($this->_MainData)){
			throw new cls_PageException('���ȳ�ʼ����Ա����');
		}
		
		$this->_Cfg['AllowStatic'] = cls_Mspace::AllowStatic($this->_MainData) ? false : true;
		
		# ��ҳUrl��̬������1)����̬��2)���ڻ��Ѿ����ɾ�̬
		if($this->_Cfg['AllowStatic']){	
			if($this->_inStatic || !empty($this->_MainData['msrefreshdate'])){
				$this->_Cfg['MpUrlStatic'] = true;
			}
		}
		$this->_CheckStatic();
	}
	
	# ���ҳ��ģ��
	protected function _ParseSource(){
		$this->_ParseSource = cls_Mspace::IndexTplname($this->_MainData['mtcid'],$this->_SystemParams);
		if(!$this->_ParseSource){
			throw new cls_PageException($this->_PageName().' - δ��ģ��');
		}
	}
	
	# ��ǰҳ�������Ϣ
	protected function _Mdebug(){
		$_mdebug = cls_env::GetG('_mdebug');
		$_mdebug->setvar('tpl',$this->_ParseSource);
		if($this->_inStatic){ # ��̬ʱ�����̬Url
			$ParamStr = '';
			foreach(array('mid','mcaid','ucid','addno',) as $k){
				if(!empty($this->_SystemParams[$k])){
					$ParamStr .= "&$k=".$this->_SystemParams[$k];
				}
			}
			$ParamStr = substr($ParamStr ,1);
			
			$mspacedir = cls_env::GetG('mspacedir');
			$_mdebug->setvar('uri',"{$mspacedir}/index.php?$ParamStr");
			cls_env::SetG('_no_dbhalt',true); # ��̬ʱ�ر�SQL�жϴ��� ????
		}
	}
	
	# ȡ�÷�ҳUrl���ø�ʽ
	protected function _UrlPre($isStatic = false){
		if($isStatic){ # ��̬Url���ø�ʽ
			if(!empty($this->_SystemParams['filterstr'])) return '';
			 $re = $this->_StaticFilePre();# ��Ҫ�����侲̬��ʽΪ''
			 return cls_url::view_url($re);
		}else{ # ��̬Url���ø�ʽ
			$re = MspaceIndexFormat($this->_MainData,$this->_AddParams(),true);
		}
		if(!$re) throw new cls_PageException($this->_PageName().' - '.($isStatic ? '��̬' : '��̬').'URL��ʽ����');
		$re = cls_url::view_url($re);
		return $re;
	}
	
	# ����ToolJs�Ĳ������飬ֻ��page=1ʱ����
	protected function _ToolParams(){
		if($this->_SystemParams['page'] == 1){
			$_ToolParams = array('mid' => $this->_SystemParams['mid'],);
			foreach(array('mcaid','ucid','addno',) as $k){
				if(!empty($this->_SystemParams[$k])) $_ToolParams[$k] = $this->_SystemParams[$k];
			}
			if(!$this->_inStatic) $_ToolParams['upsen'] = 1;
			# ֻ�пռ���ҳ����̬����
			if(!array_intersect(array_keys($_ToolParams),array('mcaid','ucid',))){
				$_ToolParams['msp_static'] = 1;
			}
		}
		return @$_ToolParams;
	}
	
	# ȡ�÷�ҳ_��̬�ļ������ʽ
	protected function _StaticFilePre(){
		if(!isset($this->_Cfg['_StaticFilePre'])){ # ��Ҫ�ظ�ʹ��
  			$Params = array();
			foreach($this->_SystemParams as $k => $v){
				if(in_array($k,array('addno','mid','mcaid','ucid',))){
					if(!empty($v)) $Params[$k] = $v;
				}
			}
			$this->_Cfg['_StaticFilePre'] = MspaceIndexFormat($this->_MainData,$Params,false);
		}
		return $this->_Cfg['_StaticFilePre'];
	}
	
	# ��ʽ��������������
	protected function _AddParams(){
		$re = $this->_SystemParams;
		if(!$this->_inStatic) $re += $this->_QueryParams;
		unset($re['page']); //ȥ��&page=999,��������&page={\$page}����;�����ҳ����
		return $re;
	}
	
	# ҳ������
	protected function _PageName(){
		return "��Ա�ռ�[{$this->_SystemParams['mid']}]";
	}
	
}
