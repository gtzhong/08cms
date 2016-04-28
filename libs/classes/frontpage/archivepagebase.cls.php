<?php
/**
 * �ĵ�����ҳ�Ĵ������
 *
 */
defined('M_COM') || exit('No Permission');
abstract class cls_ArchivePageBase extends cls_FrontPage{
	
  	protected static $_ExtendAplicationClass = 'cls_ArchivePage'; 	# ��ǰ�������չӦ����(������)������
	protected $_Arc = NULL; 										# ��ǰ�ĵ�������ʵ��
 	protected $_KeepStaticFormat = 0; 								# ����ԭ��̬��ʽ
	
	# ҳ�����ɵ��ⲿִ�����
	# ����������ο� function _Init
	public static function Create($Params = array()){
		return self::_iCreate(self::$_ExtendAplicationClass,$Params);
	}

	protected function __construct(){
		parent::__construct();
 		$this->_PageCacheParams['typeid'] = 2; 							# ҳ�滺������
		$this->_Cfg['AllowStatic'] = cls_env::mconfig('enablestatic');	# ��̬�ܿ���
		$this->_Cfg['LoadAdv'] = true;										# �Ƿ���Ҫ���ɹ��js���ô���
	}
	
	# Ӧ��ʵ���Ļ�����ʼ��
	protected function _Init($Params = array()){
		
		$this->_inStatic = empty($Params['inStatic']) ? false : true; # �Ƿ�̬	
			
		if(!empty($Params['arc'])){
			$this->_Arc = $Params['arc']; # ע�⣺ʵ��������
			$this->_SystemParams['aid'] = $this->_Arc->aid;
			$this->_inMobile = (bool)@$this->_Arc->archive['nodemode'];
		}else{
			$this->_SystemParams['aid'] = isset($Params['aid']) ? $Params['aid'] : @$this->_QueryParams['aid'];
			$this->_inMobile = defined('IN_MOBILE'); # �ֻ����ݲ�֧�־�̬
		}
		if($this->_inStatic && $this->_inMobile){
			throw new cls_PageException($this->_PageName()." - �ֻ����ݲ�֧�־�̬");
		}
		
		$this->_SystemParams['addno'] = isset($Params['addno']) ? $Params['addno'] : @$this->_QueryParams['addno']; # ����ҳ����
		$this->_KeepStaticFormat = empty($Params['kp']) ? 0 : 1;
	}
	
	# ��ͬ����ҳ�������ģ��
	protected function _ModelCumstom(){
		$this->_ReadArchive(); # ��ȡ�ĵ�����
		$this->_PageCacheConfig(); # ��̬ҳ��������ò���
	}
	
	# ��ȡ�ĵ�����
	protected function _ReadArchive(){
		$this->_SystemParams['aid'] = max(0,intval($this->_SystemParams['aid']));
		
		if(!$this->_SystemParams['aid']){
			throw new cls_PageException($this->_PageName()." - δָ���ĵ�ID");
		}
		
		if(empty($this->_Arc)){
			$this->_Arc = new cls_arcedit();
			if(!$this->_Arc->set_aid($this->_SystemParams['aid'],array('au' => 0,'ch' => 1,'nodemode' => $this->_inMobile))){
				throw new cls_PageException($this->_PageName()." - �ĵ�������");
			}
		}elseif(!$this->_Arc->detailed){
			$this->_Arc->detail_data(0);
		}
		
		# �������ĵ���ת
		if(!empty($this->_Arc->archive['jumpurl'])){
			throw new cls_PageException($this->_PageName()." - �ĵ���ת");
		}
		
		# ����Ƿ����
		$this->_ArcChecked();
	}
	
	# ����Ƿ����
	protected function _ArcChecked(){
		if(!$this->_Arc->archive['checked']){
			if($this->_inStatic || !self::$curuser->isadmin()){
				throw new cls_PageException($this->_PageName()." - �ĵ�δͨ�����");
			}
		}
	}
	
	# ��̬ҳ��������ò���
	protected function _PageCacheConfig(){
		if($this->_inStatic) return;
		$this->_PageCacheParams['chid'] = $this->_Arc->archive['chid'];
		$this->_PageCacheParams['initdate'] = $this->_Arc->archive['initdate'];
	}
	
	# ����ҳ��Ŵ���
	protected function _Addno(){
		$this->_SystemParams['addno'] = max(0,intval($this->_SystemParams['addno']));
		$Addnum = @$this->_Arc->arc_tpl['addnum'];
		if($this->_SystemParams['addno'] > $Addnum){
			throw new cls_PageException($this->_PageName()." - ������ĸ���ҳ");
		}
	}
	
	# �Ƿ���Ҫ��̬����$_inStatic(�Ƿ���̬ҳ���ɹ�����)�ǲ�ͬ�ĺ���
	# ��Ҫ��̬ʱ����ʹ�ڶ�̬ҳ�棬��ҳurlҲҪ����̬������(��ϱ�����̬)
	protected function _CheckAllowStatic(){
		if($this->_Cfg['AllowStatic']){
			if($this->_inMobile){
				$this->_Cfg['AllowStatic'] = 0;
			}elseif(!empty($this->_Arc->arc_tpl['cfg'][$this->_SystemParams['addno']]['static'])){ # ���������ùرվ�̬
				$this->_Cfg['AllowStatic'] = 0;
			}
		}
		$this->_Cfg['MpUrlStatic']	= $this->_Cfg['AllowStatic'];	# ��ҳUrl��̬ԭ�򣺶����о�����̬ҳ���и����Ƿ�����̬������������ҳUrl
		$this->_CheckStatic();
	}
	
	# ҳ�泣��������������ڸ��ӱ���������������ҳUrl�Ƿ���Ҫ��̬���Ƿ���Ҫ����������¼
	protected function _NormalVars(){
		if($this->_inStatic) return;
		$this->_NormalVars = array('aid','addno','page',);
	}
	
	# ��ȡҳ����������
	protected function _MainData(){
		$this->_CheckAllowStatic(); # �Ƿ���Ҫ���ɾ�̬��ע�⣺��ʹ��̬ҳ�棬Ҳ��Ҫ����
		$this->_MainData = $this->_Arc->archive;
		cls_ArcMain::Parse($this->_MainData);
	}
	
	# ���ҳ��ģ��
	protected function _ParseSource(){
		$this->_ParseSource = $this->_Arc->tplname($this->_SystemParams['addno']);	
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
			$ParamStr = $this->_SystemParams['addno'] ? "&addno={$this->_SystemParams['addno']}" : '';
			$ParamStr = substr($ParamStr ,1);
			$_mdebug->setvar('uri',"archive.php?$ParamStr");
			cls_env::SetG('_no_dbhalt',true); # ��̬ʱ�ر�SQL�жϴ��� ????
		}
	}
	
	# ȡ�÷�ҳUrl���ø�ʽ
	protected function _UrlPre($isStatic = false){
		$re = $this->_Arc->urlpre($this->_SystemParams['addno'],$this->_SystemParams['filterstr'],$isStatic,$this->_KeepStaticFormat);
		if(!$re) throw new cls_PageException($this->_PageName().' - '.($isStatic ? '��̬' : '��̬').'URL��ʽ����');
		return $re;
	}
	
	# ����ToolJs�Ĳ������飬ֻ��page=1ʱ����
	protected function _ToolParams(){
		if(!$this->_inMobile && $this->_SystemParams['page'] == 1){
			$_ToolParams = array();
			$_ToolParams = array('mode' => 'arc','static' => 1,'aid' => $this->_SystemParams['aid'],'chid' => $this->_Arc->archive['chid'],'mid' => $this->_Arc->archive['mid'],);
			if(!$this->_inStatic) $_ToolParams['upsen'] = 1;
			if($this->_SystemParams['addno']) $_ToolParams['addno'] = $this->_SystemParams['addno'];
		}
		return @$_ToolParams;
	}
	
	
	# ���ɾ�̬����������Ϣ(ʱ�䣬Url��)�������¼
	protected function _UpdateStaticRecord(){
		if(!$this->_inStatic) return;
		$ns = explode(',',$this->_Arc->archive['needstatics']);
		$nns = '';
		for($i = 0;$i <= $this->_Arc->arc_tpl['addnum'];$i++){
			$nns .= ($i == $this->_SystemParams['addno'] ? self::$timestamp : @$ns[$i]).',';
		}
		self::$db->query("UPDATE ".self::$tblprefix.$this->_Arc->tbl." SET needstatics='$nns' WHERE aid='{$this->_SystemParams['aid']}'");
	}
	
	# ȡ�÷�ҳ_��̬�ļ������ʽ
	protected function _StaticFilePre(){
		if(!isset($this->_Cfg['_StaticFilePre'])){ # ��Ҫ�ظ�ʹ��
			$this->_Cfg['_StaticFilePre'] = $this->_Arc->filepre($this->_SystemParams['addno'],$this->_KeepStaticFormat);
		}
		return $this->_Cfg['_StaticFilePre'];
	}
	
	# ҳ������
	protected function _PageName(){
		return "�ĵ�����ҳ[{$this->_SystemParams['aid']}]";
	}
	
}
