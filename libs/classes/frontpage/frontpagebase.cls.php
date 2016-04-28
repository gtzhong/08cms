<?php
/**
 * ����ǰ̨ҳ��(��̬/��̬/js��ǩ/����ǩ��)�Ĺ��ô����ܻ���
 * �������͵�ǰ̨ҳ��(��Ҫģ����ǩ����)�ľ��̳д˻���
 */
defined('M_COM') || exit('No Permission');
abstract class cls_FrontPageBase extends cls_BasePage implements ICreate{
	
	protected $_QueryParams = array(); 					# ����GP����
	protected $_SystemParams = array();					# ����ģ�����֮ǰ����Ԥ����ı������ϲ�ʱ����
	protected $_MainData = array();						# ҳ��������������
	protected $_Cfg = array();							# ��ǰ��������Ҫ��ʱ�����������������
	protected $_NormalVars = array();					# ҳ�泣��������������ڸ��ӱ���������������ҳUrl�Ƿ���Ҫ��̬���Ƿ���Ҫ����������¼
	
	protected $_SourceType = 'tplname';					# ��Դ���ͣ�(1)tplname(ҳ��ģ��) (2)js(js��ǩ) (3)adv(���) (4)fragment(��Ƭ) (4)adminm(��Ա���Ľű�)
	protected $_ParseSource = '';						# ҳ��ģ������/ģ���ǩ����(���ǩjs����)/��Ա���Ľű�����
	
	protected $_oPageCache = NULL;						# ҳ�滺�����ʵ��
 	protected $_PageCacheParams = array(); 				# ҳ�滺�渽�Ӳ���
	
	protected $_inStatic = false;						# ��̬ģʽ(false)/��̬ģʽ(true)
	protected $_inMobile = false;						# �Ƿ��ֻ���
	
	
	abstract protected function _Init($Params = array());					# Ӧ��ʵ���Ļ�����ʼ��
	
	# �ڲ���ʼ�����
	protected static function _iCreate($ExtendAplicationClass,$Params = array()){
		if(!$ExtendAplicationClass || !class_exists($ExtendAplicationClass)){
			exit("��չӦ����[$ExtendAplicationClass]δ����");
		}
		$_Instance = new $ExtendAplicationClass();
		$_Instance->_Init($Params);
		if($_Instance->_inStatic){ # ���ɾ�̬
			$re = $_Instance->_StaticAllPage(); # ��ҳ��һ�������ɣ�����������Ϣ/������ʾ
		}else{ # ��̬ҳ
			$re = $_Instance->_DynamicOnePage();
		}
		unset($_Instance);
		return $re;
	}
	
	protected function __construct(){
		
		self::$db = _08_factory::getDBO();
		self::$curuser = cls_env::GetG('curuser');
		self::$tblprefix = cls_env::GetG('tblprefix');
		self::$timestamp = cls_env::GetG('timestamp');
		self::$cms_abs = cls_env::mconfig('cms_abs');
		
		$this->_QueryParams = cls_env::_GET_POST(); # ����GP����
        //�ų��̶��Ķ������
        if(empty($this->_QueryParams['domain'])) unset($this->_QueryParams['domain']);
		
		$this->_Cfg['AllowStatic'] = false;				# ��ҳ�Ƿ��������ɾ�̬
		$this->_Cfg['MpUrlStatic']	= false; 			# ��ҳUrl�Ƿ���Ҫ��̬
		$this->_Cfg['maxStaicPage']	= 0;				# ��ҳ�뾲̬ʱ����ֻ����ǰ��ҳ��0Ϊ���ޣ�����ҳ�붼����
		$this->_Cfg['SonBlockOfPage']	= false;		# ����֧������ҳ��ģ����Ƕ��ģ�����飬ҳ������(false)/������(true)�������ҳ�����ͽ���Ƕ��
		$this->_Cfg['LoadAdv'] = false;					# �Ƿ���Ҫ���ɹ��js���ô���
	}
	
	
	# ���ɶ�̬ҳ��
	protected function _DynamicOnePage($Params = array()){
		$this->_CheckSiteClosed(); # ���վ��ر�
		$Content = '';
		try{
			try{
				$re = $this->_CreateOnePage();
				$Content = $re['content'];
				$this->_SavePageCache($Content);
			}catch(cls_PageCacheException $PageCache){ # ��׽ҳ�滺�棬����������ֹ
				$Content = $PageCache->getMessage();
			}
		}catch(cls_PageException $e){ # ������ֹ��Ϣ
			$Content = $e->getMessage();
		}
		return $this->_DynamicResultOut($Content);
	}
	
	# ����ȫ����ҳ�ľ�̬ҳ��
	# Ŀǰ�Ĵ�����һ��������ͬһ�ĵ������з�ҳ�����ڵ��ĵ��ڵļ����б�(�����������)����Ҫ�ر�ע��??????????????????
	protected function _StaticAllPage(){
		$_start_time = microtime(TRUE);
		
		$maxStaticPage = 1;
		$PageByteSize = 0;
		for($this->_SystemParams['page'] = 1;$this->_SystemParams['page'] <= $maxStaticPage;$this->_SystemParams['page'] ++){
			try{
				$re = $this->_CreateOnePage();
			}catch(cls_PageException $e){
				return $e->getMessage();
			}
			
			if($error = $this->_SaveStaticFile($re['content'])){
				return $error;
			}
			$PageByteSize += strlen($re['content']);
			$maxStaticPage = $this->_MaxStaticPageNo(@$re['pcount']);
		}
		cls_env::SetG('_no_dbhalt',false); # ��̬ʱ�ر�SQL�����ж� ????
		
		# ���ɾ�̬����������Ϣ(ʱ�䣬Url��)�������¼
		$this->_UpdateStaticRecord();
		
		# ��ȷ������ɺ�ķ�����Ϣ
		$_Msg = $maxStaticPage."����ҳ  ";
		$_Msg .= round(microtime(TRUE) - $_start_time,2)."s ";
		$_Msg .= $PageByteSize."byte >> ".cls_url::m_parseurl($this->_StaticFilePre(),array('page' => 1));
		return $_Msg;
	}
	
	
	# ���ɵĵ�ҳ�棨��ǰҳ�룩
	protected function _CreateOnePage(){
		$this->_InitMainPage();
		$this->_Model();
		$re = $this->_View();
		return $re;
	}
	
	# ��ʼ������ҳ�棬�����ҳ����ģ��(SonBlockOfPageΪtrue)����Ҫ����
	protected function _InitMainPage(){
		if(!$this->_Cfg['SonBlockOfPage']){
			$this->_LoadExtendFunc();
			self::$_mp = array();
			cls_env::SetG('G',array());# ҳ�湲�ñ�������$G����ʱά��global���Լ���Ŀǰ��ģ��
		}
	}
	
	# ����ģ����չ�������ɸ��ݲ�ͬ���͵�ҳ�棬�������м��ز�ͬ����չ����
	protected function _LoadExtendFunc(){
		foreach(array(cls_tpl::TemplateTypeDir('function').'utags.fun.php',) as $k){
			if(is_file($k)) include_once $k;
		}
	}
	# ����ģ�ʹ���
	protected function _Model(){
		$this->_PageNo(); # ��ǰҳ�봦��
		
		# ��̬ʱֻ��Ҫ��һҳ����ȫ������
		if(!$this->_inStatic || $this->_SystemParams['page'] == 1){
			$this->_ModelCumstom(); # ��ͬ����ҳ�������ģ��
			$this->_ModelCommon(); # ����ģ�͵�ͨ�ò���
			$this->_ModelEnd(); # ����ģ����β����
		}else{
			# ��̬�еĵڶ�ҳ�뿪ʼ��ֻ��Ҫ���·�ҳ���ϣ��������ϱ��֡�
			$this->_MpConfig(); # ��ҳ����
		}
	}
	
	# ����ģ�͵�ͨ�ò���
	# ���û����еı�������Ҫ�����ҳ��ģ��(tplname)���͵Ĵ���������������ͣ�������Ӧ�����ж���
	protected function _ModelCommon(){
		$this->_Addno(); # ����ҳ��Ŵ���
		if(!$this->_inStatic){
			$this->_NormalVars(); # ҳ�泣�����
			$this->_AllowRobot(); # ��ֹ��������ץȡ���Ӳ�����ҳ��
			$this->_ReadPageCache(); # ��ȡҳ�滺��(������Ҫ����ҳ�����ϣ���ע�����˳��)
		}
		$this->_MainData(); # ��ȡҳ����������
		$this->_ParseSource(); # ���ҳ��ģ��
		$this->_Mdebug(); # ��ǰҳ�������Ϣ
		$this->_MpConfig(); # ��ҳ����
	}
	
	# ����ģ����β����
	protected function _ModelEnd(){
		$this->_ModelExtend(); # Ԥ������ģ�͵���չ�ӿ�
		$this->_MainDataCombo(); # �������ݺϲ������ִ��!
	}
	
	# ģ�����
	protected function _View(){		
		$_ParseInitConfig = $this->_ParseInitConfig(); # ����ҳ������Ҫ��������������
		try{		  
            if ( isset($_ParseInitConfig['_da']['action']) && (strtolower($_ParseInitConfig['SourceType']) == 'js') )
            {
                $re = self::_MultiSourceCode($_ParseInitConfig);
            }
            else
            {
                $re = cls_Parse::OneSourceCode($_ParseInitConfig);
            }
		}catch(cls_ParseException $e){
			throw new cls_PageException($e->getMessage());
		}
		$this->_ViewToolParams($re);
		$this->_ViewAdv($re);
		return $re;
	}
    
    /**
     * ����AJAX����
     * ������AJAXһ��������JSģ��
     * @example http://auto.08cms.com/tools/ajax.php?action=get_tag&tname=532194d643&iteration=cid&data[cid]=411,410,408&data_format=js&_=1393829251424
     * 
     * @param  array $_ParseInitConfig ��������
     * @return array $re               ��Դ����
     * @since  auto5.0
     */
    protected function _MultiSourceCode( $_ParseInitConfig )
    {
        $iterationString = $iteration = '';
        $params = array();
        if ( isset($_ParseInitConfig['_da']['iteration']) )
        {
            $iteration = $_ParseInitConfig['_da']['iteration'];
        }
        if ( !empty($_ParseInitConfig['_da']['data'][$iteration]) )
        {
            $re = array();
            $params = array_filter(explode(',', $_ParseInitConfig['_da']['data'][$iteration]));
            foreach ( $params as $param ) 
            {
                $_ParseInitConfig['_da'][$iteration] = $_ParseInitConfig['_da']['data'][$iteration] = $param;
                $re = cls_Parse::OneSourceCode($_ParseInitConfig);
                if ( $iterationString )
                {
                    $iterationString .= '<!--_08_TAG_SPILT-->';
                }
                $iterationString .= $re['content'];
            }
            
            $re['content'] = $iterationString;
        }
        else  # ֻΪ����֮ǰ����
        {
        	$re = cls_Parse::OneSourceCode($_ParseInitConfig);
        }        
        
        return $re;
    }
	
	# ��ֹ��������ץȡ���Ӳ�����ҳ��
	protected function _AllowRobot(){
		if($this->_inStatic) return;
		cls_env::AllowRobot($this->_QueryParams,$this->_NormalVars);
	}
	
	# ƴ��Url���Ӳ����ִ�(ҳ�泣������ų�����)
	protected function _Filterstr(){
		$this->_SystemParams['filterstr'] = '';
		if($this->_inStatic) return;
		foreach($this->_QueryParams as $k => $v){
			if(!in_array($k,$this->_NormalVars)){
				$this->_SystemParams['filterstr'] .= "&$k=".rawurlencode(@stripslashes($v));
			}
		}
		if($this->_SystemParams['filterstr']){
			$this->_Cfg['AllowStatic'] = 0;
		}
	}
	
	# ��ҳ�����Լ���ҳ���ϳ�ʼ��
	protected function _MpConfig(){
		$this->_Filterstr(); # ƴ��Url���Ӳ����ִ�
		self::$_mp = array(
			'nowpage' => $this->_SystemParams['page'],
			'durlpre' => $this->_UrlPre(false),
			'surlpre' => $this->_UrlPre(true),
			'static' => $this->_Cfg['MpUrlStatic'],
			's_num' => $this->_Cfg['maxStaicPage'],
		);
		$this->_Mp_Init();
	}
	
	# ��ҳ�����Լ���ҳ���ϳ�ʼ��
	protected function _Mp_Init(){
		if(empty(self::$_mp)) return; # ֻ�������˷�ҳ������²ų�ʼ��
		self::$_mp['pcount'] = 1;
		self::$_mp['acount'] = 0;
		self::$_mp['limits'] = 10;
		self::$_mp['length'] = 10;
		self::$_mp['simple'] = 1;
		self::$_mp['static'] = empty(self::$_mp['static']) ? 0 : 1;
		self::$_mp['mptitle'] = '';
		self::$_mp['acount'] = 0;
		self::$_mp['mppage'] = self::$_mp['nowpage'];
		self::$_mp['mpcount'] = self::$_mp['pcount'];
		self::$_mp['mpacount'] = self::$_mp['acount'];
		foreach(array('mpstart','mpend','mppre','mpnext',) as $k) self::$_mp[$k] = '#';
		self::$_mp['mpnav'] = '';
	}
	
	# ���������ݺϲ����������ݣ�������ش���
	# Ϊ�˼��ݵ�ǰ��һ�����������($_da)
	protected function _MainDataCombo(){
		$this->_MainData = array_merge($this->_MainData,$this->_SystemParams);# _SystemParams����
		if(!$this->_inStatic){
			$this->_MainData += $this->_QueryParams;
			cls_env::repGlobalValue($this->_MainData); # XSS
		}
	}
	
	# ��ȡ��̬ҳ�滺�棬��Ҫ�����淵�غ󣬸��ݱ�������Ƿ��ػ������????????????
	protected function _ReadPageCache(){
		if($this->_inStatic || empty($this->_PageCacheParams['typeid'])) return;
		$this->_PageCacheParams['page'] = $this->_SystemParams['page'];
		$this->_oPageCache = new cls_pagecache();
		$Content = $this->_oPageCache->read($this->_QueryParams,$this->_PageCacheParams);
		if(!is_null($Content)){ # Ϊ�˼���"�������"��"ֱ�Ӵ�ӡ"�����ַ�ʽ����ҳ�滺���������׳�������ֹ��������
			throw new cls_PageCacheException($Content);
		}
	}
	
	# ���涯̬ҳ����
	protected function _SavePageCache($Content){
		if(!empty($this->_oPageCache)){
			$this->_oPageCache->save($Content);
		}
	}
	
	# ��ǰҳ�봦��
	protected function _PageNo(){
		$Page = empty($this->_SystemParams['page']) ? @$this->_QueryParams['page'] : $this->_SystemParams['page'];
		$this->_SystemParams['page'] = max(1,intval($Page));
	}
	
	# ��װ����ҳ������Ҫ��������������
	protected function _ParseInitConfig(){
		$re = array(
			'SourceType' => $this->_SourceType, 		# ������Դ����
			'ParseSource' => $this->_ParseSource, 		# ������Դ
			'_da' => $this->_MainData,					# ��ǰ��Դ����������
		);
		return $re;
	}
	
	# ���澲̬�ļ�
	protected function _SaveStaticFile($Content = ''){
		$StaticFilePre = $this->_StaticFilePre();
		if(!$StaticFilePre) return '��̬���ɸ�ʽδ����';
		$StaticFile = cls_url::m_parseurl($StaticFilePre,array('page' => $this->_SystemParams['page']));
		$re = str2file($Content,M_ROOT.$StaticFile);
		return $re ? '' : "$StaticFile �޷�д��";
	}
	
	# ���̬ҳ��
	# $Pcount��ҳ���ܷ�ҳ��
	protected function _MaxStaticPageNo($Pcount = 1){
		$Pcount = max(1,intval($Pcount));
		$re = empty($this->_Cfg['maxStaicPage']) ? $Pcount : min($Pcount,$this->_Cfg['maxStaicPage']);
		$re = max(1,$re);
		return $re;
	}
	
	# ����ToolJs�ִ�
	protected function _ViewToolParams(&$ContentArray = array()){
		if(isset($ContentArray['content'])){
			$ToolParams = $this->_ToolParams(); # ����ToolJs�Ĳ�������
			$ContentArray['content'] .= cls_phpToJavascript::PtoolJS($ToolParams);
		}
	}
	
	# ������js���ô���
	protected function _ViewAdv(&$ContentArray = array()){
		if(!empty($ContentArray['content'])){
			if(!empty($this->_Cfg['LoadAdv'])){
				$ContentArray['content'] .= cls_phpToJavascript::LoadAdv();
			}
		}
	}
	
	# ���վ��ر�
	protected function _CheckSiteClosed(){
		cls_env::CheckSiteClosed();
	}
	
	# ���/���ض�̬���(����ı�������������ͨҳ����������js,ajax�������������ж���)
	protected function _DynamicResultOut($Content){
		$Content .= $this->_ViewMdebug();	
		if(empty($this->_Cfg['DynamicReturn'])){ # ֱ�Ӵ�ӡ���
			//echo ' PRE_content '; //$a = 2/0;
			$preData = ob_get_contents(); //��ǰ������(�������Ϣ)һͬ��������
			ob_end_clean();
			cls_env::mob_start(true);			
			echo $preData.$Content;
			exit();
		}else{ # ���ؽ��
			return $Content;
		}
	}
	
	# ��ǰҳ�������Ϣ
	protected function _ViewMdebug(){
		$Return = '';
		if($_mdebug = cls_env::GetG('_mdebug')){
			$Return = $_mdebug->view();
		}
		return $Return;
	}
	
	# ��龲̬�����У��Ƿ��������ɾ�̬
	protected function _CheckStatic(){
		if($this->_inStatic && empty($this->_Cfg['AllowStatic'])){
			throw new cls_PageException($this->_PageName()." - ���������ɾ�̬");
		}
	}
	
	# ����Ȩ����Ƶ�ȿ���
	protected function _CheckSearchPermission(){
		if($error = self::$curuser->noPm($this->_Cfg['search_pmid'])){
			throw new cls_PageException($error);
		}
		if($this->_Cfg['search_repeat']){
			$diff = self::$timestamp - @self::$curuser->info['lastsearch'];
			//�޸Ĺ�ϵͳʱ��������,�ٻ�ԭϵͳʱ��,����ʹ$diffΪ����,�����������������
			if($diff>0 && $diff < $this->_Cfg['search_repeat']){
				throw new cls_PageException('������������Ƶ��');
			}
			self::$db->query("UPDATE ".self::$tblprefix."msession SET lastsearch='".self::$timestamp."' WHERE msid='".@self::$curuser->info['msid']."'",'SILENT');
		}
	}
	
	
	

# ******************����Ϊ��ֹ��ͬ�������в���Ҫ��δ���壬Ԥ���Ŀսӿ� *********************************************
	# Ԥ��һ���յ�����ģ����չ�ӿ�
	protected function _ModelExtend(){}
		
	# ���ɾ�̬����������Ϣ(ʱ�䣬Url��)�������¼��Ԥ���սӿ�
	protected function _UpdateStaticRecord(){}
	
	# ҳ�����ƣ�Ԥ���սӿ�
	protected function _PageName(){
		return 'ָ��ҳ��';
	}
	
	# ��ȡҳ���������ϣ�Ԥ���սӿ�
	protected function _MainData(){}
	
	# ҳ�泣��������������ڸ��ӱ���������������ҳUrl�Ƿ���Ҫ��̬���Ƿ���Ҫ����������¼��Ԥ���սӿ�
	protected function _NormalVars(){}
		
	# ����ҳ��Ŵ���Ԥ���սӿ�
	protected function _Addno(){}
	
	# ��ͬ����ҳ�������ģ�ͣ�Ԥ���սӿ�
	protected function _ModelCumstom(){}
	
	# ���ҳ��ģ�壬Ԥ���սӿ�
	protected function _ParseSource(){}

	# ��ǰҳ�������Ϣ��Ԥ���սӿ�
	protected function _Mdebug(){}
	
	# ȡ�÷�ҳUrl���ø�ʽ��Ԥ���սӿ�
	protected function _UrlPre($isStatic = false){
		return '';
	}
	
	# ȡ�÷�ҳ_��̬�ļ������ʽ��Ԥ���սӿ�
	protected function _StaticFilePre(){}
	
	# ����ToolJs�Ĳ������飬Ԥ���սӿ�
	protected function _ToolParams(){
	}
	
	
}

interface ICreate {
    public static function Create($Params = array());	# ҳ�����ɵ��ⲿִ�����
}