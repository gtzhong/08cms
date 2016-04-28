<?php
/**
 * JS����ģ���ǩ�Ĵ������
 *
 */
defined('M_COM') || exit('No Permission');
abstract class cls_JsTagBase extends cls_FrontPage{
	
   	protected static $_ExtendAplicationClass = 'cls_JsTag'; 	# ��ǰ�������չӦ����(������)������
	
	# ҳ�����ɵ��ⲿִ�����
	# ����������ο� function _Init
	public static function Create($Params = array()){
		return self::_iCreate(self::$_ExtendAplicationClass,$Params);
	}

	protected function __construct(){
		parent::__construct();
		$this->_SourceType = 'js'; # ��Դ����
 		$this->_PageCacheParams['typeid'] = 9; 					# ҳ�滺������
		$this->_PageCacheParams['is_p'] = empty($this->_QueryParams['is_p']) ? 0 : 1;
		unset($this->_QueryParams['t']);
	}
	
	# Ӧ��ʵ���Ļ�����ʼ�������붨�壬ÿ��Ӧ�ö������ѵĴ�����
	# �Ƿ񷵻ؽ��
	# ҳ����������(��aid,cnstr,tname��ҳ������)
	protected function _Init($Params = array()){
		
		# ��̬��������ʽ������(true)/���(false)
		$this->_Cfg['DynamicReturn'] = empty($Params['DynamicReturn']) ? false : true; 
		# ��̬������ݸ�ʽ��js/json/xml��
		$this->_Cfg['DataFormat'] = isset($Params['DataFormat']) ? strtolower($Params['DataFormat']) : ''; 
		
		# ҳ����������
		$this->_SystemParams['tname'] = '';
		if(isset($Params['tname'])){
			$this->_SystemParams['tname'] = $Params['tname'];
		}elseif(isset($this->_QueryParams['tname'])){
			$this->_SystemParams['tname'] = $this->_QueryParams['tname'];
		}
	}
	
	# ���վ��ر�
	protected function _CheckSiteClosed(){
		cls_env::CheckSiteClosed(1);
	}
	
	# ��ͬ����ҳ�������ģ��
	protected function _ModelCumstom(){
		$this->_ParseSource(); # ��ȡ��ǩ����
	}
	
	# ����ģ�͵�ͨ�ò���
	protected function _ModelCommon(){
		$this->_ReadPageCache(); # ��ȡҳ�滺��(������Ҫ����ҳ�����ϣ���ע�����˳��)
		$this->_MainData(); # ��ȡҳ����������
	}
	# ���ҳ��ģ��
	protected function _ParseSource(){
		if($this->_SystemParams['tname']){
			$this->_ParseSource = cls_cache::cacRead('js_tag_'.$this->_SystemParams['tname'],cls_Parse::TplCacheDirFile('',2));
			if(!empty($this->_ParseSource['mp'])) unset($this->_ParseSource['mp']);
		}
		if(!$this->_ParseSource){
			throw new cls_PageException('δ�ҵ�ָ����ģ���ǩ');
		}
	}
		
	# ��ȡҳ����������
	protected function _MainData(){
		if(!empty($this->_QueryParams['data'])){
			$this->_MainData = (array)$this->_QueryParams['data'];
		}
	}
	
	# ���/���ض�̬���
	protected function _DynamicResultOut($Content){
		if($this->_Cfg['DataFormat']){
			switch($this->_Cfg['DataFormat']){
				case 'js': # �����תΪ����JSʹ�õĸ�ʽ
					$Content = cls_phpToJavascript::JsFormat($Content);
				break;
				case 'get_tag_js': # תΪ����JS������JS����
					$Content = cls_phpToJavascript::JsFormat($Content);
					$Content = "var get_tag = '$Content';";
				break;
				case 'jswrite':	# document.write��JS����
					$Content = cls_phpToJavascript::JsWriteCode($Content);
				break;
			}
		}
		if(empty($this->_Cfg['DynamicReturn'])){ # ֱ�Ӵ�ӡ���
			if($this->_Cfg['DataFormat'] == 'write'){ # �������з�ʽ������ȷ���Ƿ���Ҫ???????????????????
				header("content-type: text/javascript; charset=".cls_env::getBaseIncConfigs('mcharset'));
			}
			exit($Content);
		}else{ # ���ؽ��
			return $Content;
		}
	}
	
}
