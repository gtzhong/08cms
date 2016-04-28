<?php
/**
 * ������Ŀ�ڵ�ҳ��Ĵ������
 *
 */
defined('M_COM') || exit('No Permission');
abstract class cls_CnodePageBase extends cls_FrontPage{
	
 	protected $_Node = array(); 									# ��ǰ�ڵ����������
 	protected static $_ExtendAplicationClass = 'cls_CnodePage'; 	# ��ǰ�������չӦ����(������)������

	# ҳ�����ɵ��ⲿִ�����
	# ����������ο� function _Init
	public static function Create($Params = array()){
		return self::_iCreate(self::$_ExtendAplicationClass,$Params);
	}

	protected function __construct(){
		parent::__construct();
 		$this->_PageCacheParams['typeid'] = 1; 										# ҳ�滺������
		$this->_Cfg['AllowStatic'] = cls_env::mconfig('enablestatic');				# ��̬�ܿ���
		$this->_Cfg['maxStaicPage'] = max(0,intval(cls_env::mconfig('liststaticnum'))); # ע�⣺��������ҳȫ�����ɾ�̬����ʹ�ô�����
		$this->_Cfg['LoadAdv'] = true;										# �Ƿ���Ҫ���ɹ��js���ô���
	}
	
	# Ӧ��ʵ���Ļ�����ʼ�������붨�壬ÿ��Ӧ�ö������ѵĴ�����
	# �Ƿ�̬
	# �Ƿ��ֻ���
	# �Ƿ񷵻ؽ��
	# ����ҳ����		
	# ҳ����������(��aid,cnstr,tname��ҳ������)
	protected function _Init($Params = array()){
		
		$this->_inStatic = empty($Params['inStatic']) ? false : true; # �Ƿ�̬
		$this->_inMobile = $this->_inStatic ? false : defined('IN_MOBILE'); # �Ƿ��ֻ���
		$this->_SystemParams['addno'] = $this->_inStatic || isset($Params['addno']) ? @$Params['addno'] : @$this->_QueryParams['addno']; # ����ҳ����
		
		# ҳ����������
		$this->_SystemParams['cnstr'] = $this->_inStatic || isset($Params['cnstr']) ? @$Params['cnstr'] : cls_cnode::cnstr($this->_QueryParams); # �õ��ڵ��ִ�
		if(!$this->_SystemParams['cnstr']) $this->_SystemParams['cnstr'] = '';
	}
	
	# ��ͬ����ҳ�������ģ��
	protected function _ModelCumstom(){
		$this->_Cnstr(); # �����ڵ��ִ�
		$this->_Node(); # ��ȡ�ڵ�
	}
	
	# �����ڵ��ִ�(Ԥ����չ�ӿ�)
	protected function _Cnstr(){}
	
	# ��ȡ�ڵ�
	protected function _Node(){
		if($this->_SystemParams['cnstr']){
			if(!($this->_Node = cls_node::cnodearr($this->_SystemParams['cnstr'],$this->_inMobile))){
				throw new cls_PageException($this->_PageName()." - �����ڻ�δ����");
			}
		}
	}
	
	# ����ҳ��Ŵ���
	protected function _Addno(){
		$this->_SystemParams['addno'] = max(0,intval(@$this->_SystemParams['addno'])); # ��ͨ�����γ�ʼ��
		$Addnum = $this->_SystemParams['cnstr'] ? @$this->_Node['addnum'] : 0;
		if($this->_SystemParams['addno'] > $Addnum){
			throw new cls_PageException($this->_PageName()." - ������ĸ���ҳ");
		}
	}
	
	# �Ƿ���Ҫ���ɾ�̬
	# ��Ҫ��̬ʱ����ʹ�ڶ�̬ҳ�棬��ҳurlҲҪ����̬������(��ϱ�����̬)
	protected function _CheckAllowStatic(){
		if($this->_Cfg['AllowStatic']){
			if($this->_inMobile){
				$this->_Cfg['AllowStatic'] = 0;
			}elseif(! empty($this->_Node['cfgs'][$this->_SystemParams['addno']]['static'])){
				$this->_Cfg['AllowStatic'] = 0;
			}
		}
		$this->_Cfg['MpUrlStatic']	= $this->_Cfg['AllowStatic'];	# ��ҳUrl��̬ԭ�򣺶����о�����̬ҳ���и����Ƿ�����̬������������ҳUrl
		$this->_CheckStatic();
	}
	
	# ҳ�泣��������������ڸ��ӱ���������������ҳUrl�Ƿ���Ҫ��̬���Ƿ���Ҫ����������¼
	protected function _NormalVars(){
		if($this->_inStatic) return;
		$this->_NormalVars = array('addno','page',);
		parse_str($this->_SystemParams['cnstr'],$idsarr);
		$this->_NormalVars = array_merge($this->_NormalVars,array_keys($idsarr));
	}
	
	# ��ȡҳ����������
	protected function _MainData(){
		$this->_CheckAllowStatic(); 			# �Ƿ���Ҫ���ɾ�̬��ע�⣺��ʹ��̬ҳ�棬Ҳ��Ҫ����
		if($this->_SystemParams['cnstr']){
			$this->_MainData = cls_node::cn_parse($this->_SystemParams['cnstr']);
			cls_node::re_cnode($this->_MainData,$this->_SystemParams['cnstr'],$this->_Node);
		}else{
			$this->_MainData['rss'] = cls_url::view_url('rss.php',false);
		}
	}
	
	# ���ҳ��ģ��
	protected function _ParseSource(){
		if($this->_SystemParams['cnstr']){
			$this->_ParseSource = cls_tpl::cn_tplname($this->_SystemParams['cnstr'],$this->_Node,$this->_SystemParams['addno']);
		}else{
			$this->_ParseSource = cls_tpl::SpecialTplname('index',$this->_inMobile);
		}
		if(!$this->_ParseSource){
			throw new cls_PageException($this->_PageName().' - δ��ģ��');
		}
	}
	
	# ��ǰҳ�������Ϣ
	protected function _Mdebug(){
		$_mdebug = cls_env::GetG('_mdebug');
		$_mdebug->setvar('tpl',$this->_ParseSource);
		if($this->_inStatic){ # ��̬ʱ�����̬Url
			$ParamStr = $this->_SystemParams['cnstr'] ? "&{$this->_SystemParams['cnstr']}" : '';
			$ParamStr .= $this->_SystemParams['addno'] ? "&addno={$this->_SystemParams['addno']}" : '';
			$ParamStr = substr($ParamStr ,1);
			$_mdebug->setvar('uri',"index.php?$ParamStr");
			cls_env::SetG('_no_dbhalt',true); # ��̬ʱ�ر�SQL�жϴ��� ????
		}
	}
	
	# ȡ�÷�ҳUrl���ø�ʽ
	protected function _UrlPre($isStatic = false){
		if($isStatic && empty($this->_SystemParams['filterstr'])){ # ��̬Url���ø�ʽ
			if($this->_inMobile || !empty($this->_SystemParams['filterstr'])) return '';
			$re = $this->_StaticFilePre();
		}else{ # ��̬Url���ø�ʽ
			$ParamStr = $this->_SystemParams['cnstr'] ? "&{$this->_SystemParams['cnstr']}" : '';
			$ParamStr .= $this->_SystemParams['addno'] ? "&addno={$this->_SystemParams['addno']}" : '';
			$ParamStr .= $this->_SystemParams['filterstr'];
			$ParamStr .= "&page={\$page}";
			$ParamStr = substr($ParamStr ,1);
			
			$re = ($this->_inMobile ? cls_env::mconfig('mobiledir').'/' : '')."index.php?".$ParamStr;
			$re = cls_url::en_virtual($re,$this->_inMobile);
		}
		if(!$re) throw new cls_PageException($this->_PageName().' - '.($isStatic ? '��̬' : '��̬').'URL��ʽ����');
		$re = cls_url::view_url($re);
		return $re;
	}
	
	# ����ToolJs�Ĳ������飬ֻ��page=1ʱ����
	protected function _ToolParams(){
		if(!$this->_inMobile && $this->_SystemParams['page'] == 1){
			$_ToolParams = array('mode' => 'cnindex','static' => 1,);
			if(!$this->_inStatic) $_ToolParams['upsen'] = 1;
			if($this->_SystemParams['cnstr']) $_ToolParams[0] = $this->_SystemParams['cnstr'];
			if($this->_SystemParams['addno']) $_ToolParams['addno'] = $this->_SystemParams['addno'];
		}
		return @$_ToolParams;
	}
	
	# ���ɾ�̬����������Ϣ(ʱ�䣬Url��)�������¼
	protected function _UpdateStaticRecord(){
		if(!$this->_inStatic) return;
		if($this->_SystemParams['cnstr']){
			$ns = self::$db->result_one("SELECT needstatics FROM ".self::$tblprefix."cnodes WHERE ename='".$this->_SystemParams['cnstr']."'");
			$ns = explode(',',$ns);
			$nns = '';
			for($i = 0;$i <= @$this->_Node['addnum'];$i++){
				$nns .= ($i == $this->_SystemParams['addno'] ? self::$timestamp : @$ns[$i]).',';
			}
			self::$db->query("UPDATE ".self::$tblprefix."cnodes SET needstatics='$nns' WHERE ename='".$this->_SystemParams['cnstr']."'");
		}else{
			self::$db->query("UPDATE ".self::$tblprefix."mconfigs SET value='".self::$timestamp."' WHERE varname='ineedstatic'");
		}
	}
	
	# ȡ�÷�ҳ_��̬�ļ������ʽ
	protected function _StaticFilePre(){
		if(!isset($this->_Cfg['_StaticFilePre'])){ # ��Ҫ�ظ�ʹ��
			$this->_Cfg['_StaticFilePre'] = $this->_CnFormat();
		}
		return $this->_Cfg['_StaticFilePre'];
	}
	
	# ��Ŀҳ�ľ�̬Url���ļ���ʽ
	protected function _CnFormat(){
		if($this->_SystemParams['cnstr']){
			$re = cls_node::cn_format($this->_SystemParams['cnstr'],$this->_SystemParams['addno'],$this->_Node);
		}else{
			$re = idx_format($this->_inMobile);
		}
		return $re;
	}
	
	# ҳ������
	protected function _PageName(){
		return $this->_SystemParams['cnstr'] ? "�ڵ�[{$this->_SystemParams['cnstr']}]" : "ϵͳ��ҳ";
	}
	
}
