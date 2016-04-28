<?php
/**
* ��ģ�����������н�������ģ������������ϵ�У��Ƕ���Ľӿ�(cls_Parse::xxx��)��֧��Ƕ�׽���ģ������
* Ϊ�˼���Ŀǰ��ģ���ģ�庯����Ƶ��ʹ��global���Ĳ�����״����Ŀǰ����Ҫ��$_da�����ڲ�������Ϊglobal������ʹ�ã��д�ģ��������滯����
* �����������þ�̬����������(cls_TagParse_xxx��cls_TagParse)�̳�ʹ��
* �������������þ�̬������ͨ������ʵ������������(cls_TagParse_xxx��cls_TagParse)������չ
* �̳�cls_FronPage��Ϊ�˹����䲿�־�̬��������$_mp�ȡ�
*/

defined('M_COM') || exit('No Permission');
abstract class cls_ParseBase extends cls_BasePage{
	
	protected static $_Instances = array();				# һ������ҳ��Ķ��ģ������Ľ���ʵ���Ķ�ջ���ر��Ǵ�����Ƕģ������Ľ�������Ҫ�ݴ��ϲ�ģ�����ʵ��
	protected static $_ActiveVarArray = array();		# ��Ҫ��������б���������
	
	protected $SourceType = 'tplname';					# ��Դ���ͣ�(1)tplname(ҳ��ģ������) (2)js(js��ǩ) (3)adv(���) (4)fragment(��Ƭ) (4)adminm(��Ա���Ľű�)
	protected $ParseSource = '';						# ��Դҳ��ģ������/ģ���ǩ(���ǩjs����)����/��Ա���Ľű�����
	protected $_da = array();							# ҳ��������������(Ŀǰʹ��global,��ʱ������)
	protected $_a = array();							# ��ǰ�����������
	protected $_ActiveParamStack = array();				# �������������ݴ��ջ
	
	
	# �������ã��õ�һ����Դ(��ָ��ҳ��)������������
	public static function OneSourceCode($ParseInitConfig = array()){
		try{
			$re = self::_ParseInstance($ParseInitConfig)->_iOneSourceCode();
			self::_DestroyNowInstance();
		}catch(cls_ParseException $e){
			self::_DestroyNowInstance();
			throw new cls_ParseException($e->getMessage());
		}
		return $re;
	}
	
	# ��ȡ�̶�����($G��$_da��$_mp)������ӿ�
	public static function Get($Key = 'G'){
		return self::_ParseInstance()->_Get($Key);
	}
	
	# ���¹̶�����($G��$_da��$_mp)������ӿ�
	public static function Set($Key = 'G',$Value){
		return self::_ParseInstance()->_Set($Key,$Value);
	}
	
	# ģ����ʹ�õ�������Ϣ��ʾ������ģ���в���ֱ��������die/exit/message�ȷ����˳�(�ᵼ��������̬ʱ�ж�)�����ô˷���
	public static function Message($message = ''){
		throw new cls_ParseException($message ? $message : 'δ֪ԭ��ҳ����ֹ');
	}

	# ����ǰ�����������$_a����ѹ�뼤�������ջ���ݴ档Ϊ�˼��ݣ���ʱ��������ӿ�
	public static function Active($SourceArray = array(),$isInit = 0){
		return self::_ParseInstance()->_Active($SourceArray,$isInit);
	}
	
	# �ڼ�������Ķ�ջ�л���һ�㣬�����µ�ǰ�ļ����������$_a��Ϊ�˼��ݣ���ʱ��������ӿ�
	public static function ActiveBack(){
		return self::_ParseInstance()->_ActiveBack();
	}
	
	# ���ݸ��ϱ�ǩ������ȡ���ϱ�ǩ�����ݣ�Ψһ��ڡ�Ϊ�˼��ݣ���ʱ��������ӿ�
	public static function Tag($tag = array()){			
		return self::_ParseInstance()->_Tag($tag);
	}
	
	# ģ�������õ�Ȩ���ж�
	public static function Pm($pmid=0){
		return self::$curuser->pmbypmid($pmid);
	}
	
	# ��ʽ��ģ�建�����ȫ�ļ�·��
	# ���ļ���Ϊ��ʱ�����ػ���Ŀ¼���ƣ������������ʱȡ��Ŀ¼
	# $SmallPathType��0Ϊ����ģ�建��Ŀ¼(common)��1Ϊ��Ա���Ļ���(adminm)��2Ϊjs��ǩ����(jstag)
	public static function TplCacheDirFile($file = '',$SmallPathType = 0){
		_08_FilesystemFile::filterFileParam($file);
		$SmallPathTypeArray = array(0 => 'common',1 => 'adminm',2 => 'jstag',);
		return _08_TPL_CACHE.(empty($SmallPathTypeArray[$SmallPathType]) ? 'common' : $SmallPathTypeArray[$SmallPathType]).DIRECTORY_SEPARATOR.($file ? $file : '');
	}

	# ������Դȡ��PHP�ļ�������ļ���ȫ·��
	public static function PHPCacheFileName($ParseSource,$SourceType = 'tplname'){
		if(!$ParseSource) return false;
		
		# Ԥ���
		$CacheID = $ParseSource;
		if(in_array($SourceType,array('fragment','adv',))){ # ģ���ǩ���� ��Ƭ�Զ���ģ��
			$CacheID = @$ParseSource['tclass'] ? (string)@$ParseSource['ename'] : (string)@$ParseSource['template'];
		}elseif($SourceType == 'js'){
			$CacheID = 'js_'.$ParseSource['ename'].'_'.substr(md5(var_export($ParseSource,TRUE)),0,10);
		}
		_08_FilesystemFile::filterFileParam($CacheID);
		if(!$CacheID) return false;
		
		# �����ļ�����·��
		$CacheFileName = $CacheID.($SourceType == 'adminm' ? '' : '.php');
		$CacheFileName = cls_Parse::TplCacheDirFile($CacheFileName,$SourceType == 'adminm');
		return $CacheFileName;
	}
	
	# ��ʼ��һ����Դ�Ľ���
	protected function __construct($ParseInitConfig = array()){
		
		# ҳ��ԭʼ����$_da����ʱά��global���Լ���Ŀǰ��ģ��
		$this->_Set('_da',isset($ParseInitConfig['_da']) ? $ParseInitConfig['_da'] : array());
		$this->_Active($this->_Get('_da'),true); # ��ʼ���������
		
		# ��ǰҳ�����Դ����
		$this->SourceType = isset($ParseInitConfig['SourceType']) ? $ParseInitConfig['SourceType'] : 'tplname';
		
		# ��ǰҳ�����Դģ�����ƻ�ģ���ǩ
		$this->ParseSource = isset($ParseInitConfig['ParseSource']) ? $ParseInitConfig['ParseSource'] : '';
		if(!$this->ParseSource){
			throw new cls_ParseException('ҳ��ģ��δ����');
		}
		
	}
	
	# ��ģ�����Ϊģ��PHP���棬��PHP����ִ�н������ݣ����ؽ��
	protected function _iOneSourceCode(){
		$PHPCacheFileName = $this->_PHPCacheFileName();
				
		# ---------------------------------------------------------------------------		
		# Ϊ����Ŀǰģ�壬��ʱά��globalɢ����ģʽ
		# ע�Ᵽ�� "$_da->$mconfig->$btags" ��˳��
		# ע��ȷ��$_da֮ǰ������⴫�������ǵ�����($_da������'�⴫����+�ڲ���ȡ����'����������)
		$mconfigs = cls_env::mconfig();
		$btags = cls_cache::Read('btags');
		$_da = $this->_Get('_da');
		
		foreach(array('_da','mconfigs','btags',) as $var){
			extract($$var,EXTR_OVERWRITE);
			//foreach($$var as $k => $v) cls_env::SetG($k,$v);//Ӧ�ò���Ҫ�����ҿ����а�ȫ����???
		}
		
		# ����base.inc.php�������ɢ����
		$BaseIncConfigs = cls_env::getBaseIncConfigs();
		extract($BaseIncConfigs,EXTR_OVERWRITE);
		
#		unset($mconfigs,$btags,$BaseIncConfigs);#  unset($_da);
		
		# ����general.inc.php�������ɢ���������ܻ�����©��ע�⼰ʱ����
		foreach(array('m_excache','m_cookie','onlineip','timestamp','authorization','debugtag','dbcharset','db','curuser','memberid',) as $k){
			$$k = cls_env::GetG($k);
		}
		# ---------------------------------------------------------------------------		
		
		# ��ȡ�������
		ob_start();
		try{ # ���粶׽tpl_exit()��ҳ���ж���Ϣ
			if(_08_DEBUGTAG){
				include $PHPCacheFileName;
			}else{
				@include $PHPCacheFileName;
			}
		}catch(cls_ParseException $e){ # ������ֹ��Ϣ��ֱ���״�
			throw new cls_ParseException($e->getMessage());
		}
		$_content = ob_get_contents();
		ob_end_clean();
		
		$re = array(
			'content' => $_content,
			'pcount' => isset(self::$_mp['pcount']) ? self::$_mp['pcount'] : 1,
		);
		
		return $re;
	}
	
	# ���¹̶�����($G��$_da��$_mp��$_a)��ʹ��ͳһ�����������Ժ�ȡ��globalģʽ
	# ֧��'G.x.y'��ʽ�Ķ�ά����
	protected function _Get($Key = 'G'){
		if(!($Key = preg_replace('/[^\w\.]/', '', (string)$Key))) return;
		$_DotPos = strpos($Key,'.');
		$_Var = false === $_DotPos ? $Key : substr($Key,0,$_DotPos);
		
		if($_Var == 'G'){ # ��ʱά��global���Լ���Ŀǰ��ģ��
			return cls_env::GetG($Key);
		}elseif($_Var == '_mp'){
			return false === $_DotPos ? self::$_mp : cls_Array::Get(self::$_mp,substr($Key,$_DotPos + 1));
		}elseif(in_array($_Var,array('_da','_a',))){
			return false === $_DotPos ? $this->$_Var : cls_Array::Get($this->$_Var,substr($Key,$_DotPos + 1));
		}
	}
	
	# ���¹̶�����($G��$_da��$_mp��$_a)��ʹ��ͳһ�����������Ժ�ȡ��globalģʽ
	# ֧��'G.x.y'��ʽ�Ķ�ά����
	protected function _Set($Key = 'G',$Value){
		if(!($Key = preg_replace('/[^\w\.]/', '', (string)$Key))) return;
		$_DotPos = strpos($Key,'.');
		$_Var = false === $_DotPos ? $Key : substr($Key,0,$_DotPos);
		
		if($_Var == 'G'){ # ��ʱά��global���Լ���Ŀǰ��ģ��
			cls_env::SetG($Key,$Value);
		}elseif($_Var == '_mp'){
			if(false === $_DotPos){
				self::$_mp = $Value;
			}else{
				cls_Array::Set(self::$_mp,substr($Key,$_DotPos + 1),$Value);
			}
		}elseif(in_array($_Var,array('_da','_a',))){
			if(false === $_DotPos){
				$this->$_Var = $Value;
			}else{
				cls_Array::Set($this->$_Var,substr($Key,$_DotPos + 1),$Value);
			}
		}
	}
	
	# ����ǰ�����������$_a����ѹ�뼤�������ջ���ݴ�
	protected function _Active($SourceArray = array(),$isInit = false){
		$_ActiveArray = $this->_Get('_a'); # ԭ�еļ����������
		

		if($isInit){ # ҳ�濪ʼʱ����ʼ�������ջ����ǰ��������
			$this->_ActiveParamStack = array();			# ��ռ����ջ
			$_ActiveArray = array();
		}
		
		# ȡ�õ�ǰ���������еļ���������ھɼ�������Ļ������и���
		$_ActiveVarArray = $this->_ActiveVarArray();
		foreach($_ActiveVarArray as $k => $v){
			if(isset($SourceArray[$k])){
				$_ActiveArray[$k] = $v == 'cn' ? cnoneid($SourceArray[$k]) : $SourceArray[$k];
			}
		}
		
		array_unshift($this->_ActiveParamStack,$_ActiveArray);		# ����ǰ��������ѹ���ջ�ݴ�
		$this->_Set('_a',$_ActiveArray);							# ��ǰ����������鸳ֵ
	}
	
	
	# �ڼ�������Ķ�ջ�л���һ�㣬�����µ�ǰ�ļ����������$_a
	protected function _ActiveBack(){
		array_shift($this->_ActiveParamStack);
		$this->_Set('_a',@$this->_ActiveParamStack[0]);	# Ϊ��ǰ����������鸳ֵ
	}
	
	# ���ݸ��ϱ�ǩ������ȡ���ϱ�ǩ�����ݣ�Ψһ��ڡ�Ϊ�˼��ݣ���ʱ��������ӿ�
	protected function _Tag($tag = array()){
		return cls_TagParse::OneTag($tag);
	}
	
	# ȡ��PHP�ļ�������ļ���ȫ·��
	protected function _PHPCacheFileName(){
		if(!($PHPCacheFileName = cls_Parse::PHPCacheFileName($this->ParseSource,$this->SourceType))){
			throw new cls_ParseException('PHPģ�建��δ֪');
		}
		if(_08_DEBUGTAG || !is_file($PHPCacheFileName)){
			if(!($PHPCacheFileName = cls_Refresh::OneSource($this->ParseSource,$this->SourceType))){
				throw new cls_ParseException('PHPģ�建��δ֪');
			}
		}
		return $PHPCacheFileName;
	}
	
	# ��Ҫ����ı�����������
	# �����Ҫ��չ���������ж��屾����
	protected function _ActiveVarArray(){
		if(empty(self::$_ActiveVarArray)){
			self::$_ActiveVarArray = array();
			
			# ����ID�������
			foreach(array('aid','mid','ucid','chid','coid','mchid','mcaid','fcaid','vid','addid','fid','cuid','arid','cid','paid') as $k){
				self::$_ActiveVarArray[$k] = '';
			}
			$grouptypes = cls_cache::Read('grouptypes');
			foreach($grouptypes as $x => $y) self::$_ActiveVarArray['grouptype'.$x] = '';
			
			# ��ĿID�������
			self::$_ActiveVarArray['caid'] = 'cn';
			$cotypes = cls_cache::Read('cotypes');
			foreach($cotypes as $x => $y) self::$_ActiveVarArray['ccid'.$x] = 'cn';
		}
		return self::$_ActiveVarArray;
	}	
		
	# ȡ�õ�ǰ��Դ�Ĵ���ʵ��(����)
	private static function _ParseInstance($ParseInitConfig = NULL){		
		if(!is_null($ParseInitConfig)){
			$ParseClassName = self::_ParseClassName($ParseInitConfig);
			$_NewInstance = new $ParseClassName($ParseInitConfig); # ע�⣺����չ���ʵ��
			array_unshift(self::$_Instances,$_NewInstance);
		}
		if(empty(self::$_Instances[0])){
			throw new cls_ParseException('ģ��������ʼ������');
		}		
		return self::$_Instances[0];
	}
	
	# ������cls_Parse����չ�������ļ������򣬲�ʹ���Զ����أ���չ��̳�cls_Parse���������������չ
	private static function _ParseClassName($ParseInitConfig){
		$ClassName = 'cls_Parse';
		$_ExClassName = '';
		switch(@$ParseInitConfig['SourceType']){
			case 'tplname':	# ��ͨģ��
				$_ExClassName = (string)$ParseInitConfig['ParseSource'];
			break;
			case 'js':	# ��̬JS��ǩ
				$_ExClassName = (string)@$ParseInitConfig['ParseSource']['ename'].'_'.@$ParseInitConfig['ParseSource']['tclass'].'_js';
			break;
		}
		
		_08_FilesystemFile::filterFileParam($_ExClassName);
		if($_ExClassName){
			$_ExClassName = str_replace('.','_',$_ExClassName);
			$_ExClassFile = cls_tpl::TemplateTypeDir('tpl_model').$_ExClassName.'.php';
			if(is_file($_ExClassFile)){ #��ʹ�����Զ�����
				include_once $_ExClassFile;
				$_ExClassName = 'tpl_'.$_ExClassName;
				if(class_exists($_ExClassName)){
					$ClassName = $_ExClassName;
				}
			}
		}
		return $ClassName;
	}
	
	# ����ǰʵ������
	private static function _DestroyNowInstance(){
		if(isset(self::$_Instances[0])){
			self::$_Instances[0] = NULL;
			array_shift(self::$_Instances);
		}
	}
	
}
