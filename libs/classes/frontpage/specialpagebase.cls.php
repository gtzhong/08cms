<?php
/**
 * ����ҳ��(��ָ��ģ������)��ҳ�洦�����
 * ��֧�ָ���ҳ
 */
defined('M_COM') || exit('No Permission');
abstract class cls_SpecialPageBase extends cls_FrontPage{
	
  	protected static $_ExtendAplicationClass = 'cls_SpecialPage'; 	# ��ǰ�������չӦ����(������)������
	
	# ҳ�����ɵ��ⲿִ�����
	# ����������ο� function _Init
	public static function Create($Params = array()){
		return self::_iCreate(self::$_ExtendAplicationClass,$Params);
	}

	protected function __construct(){
		parent::__construct();
		$this->_Cfg['LoadAdv'] = true;										# �Ƿ���Ҫ���ɹ��js���ô���
	}
	
	# Ӧ��ʵ���Ļ�����ʼ��
	protected function _Init($Params = array()){
		
		# �Ƿ��ֻ���
		$this->_inMobile = empty($Params['NodeMode']) ? false : true;
		
		# �Ƿ������js���ô���
		$this->_Cfg['LoadAdv'] = empty($Params['LoadAdv']) ? false : true;
		
		# ҳ����������(����ҳ�����ƻ�ģ������)
		if(isset($Params['spname'])){ # ���빦��ҳ������
			$this->_SystemParams['spname'] = $Params['spname'];
			$this->_ParseSource = cls_tpl::SpecialTplname($this->_SystemParams['spname'],$this->_inMobile);
		}elseif(isset($Params['tplname'])){ # ֱ�Ӵ���ģ������
			$this->_ParseSource = $Params['tplname'];
		}
		
		# ��������
		if(isset($Params['_da'])){
			$this->_MainData = $Params['_da'];
		}
	}
	
	# ���ҳ��ģ��
	protected function _ParseSource(){
		if(!$this->_ParseSource){
			throw new cls_PageException($this->_PageName().' - δ��ģ��');
		}
	}
	
	# ���/���ض�̬���
	protected function _DynamicResultOut($Content){
		return $Content;
	}
	
}
