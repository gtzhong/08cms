<?php
/**
 * ��Ƭ��ǩ�Ĵ������
 *
 */
defined('M_COM') || exit('No Permission');
abstract class cls_FrTagBase extends cls_FrontPage{
	
   	protected static $_ExtendAplicationClass = 'cls_FrTag'; 	# ��ǰ�������չӦ����(������)������
 	protected $_Fragment = array(); 							# ָ����Ƭ����
	
	# ҳ�����ɵ��ⲿִ�����
	# ����������ο� function _Init
	public static function Create($Params = array()){
		return self::_iCreate(self::$_ExtendAplicationClass,$Params);
	}

	protected function __construct(){
		parent::__construct();
		$this->_SourceType = 'fragment'; # ��Դ����
		$this->_Cfg['frview'] = empty($this->_QueryParams['frview']) ? false : true; 					# Ԥ����ƬЧ��
		$this->_Cfg['frdata'] = empty($this->_QueryParams['frdata']) ? false : true; 					# ֱ�Ӵ�ӡԭʼ����
		$this->_Cfg['charset'] = empty($this->_QueryParams['charset']) ? '' : $this->_Cfg['charset']; 	# �Ƿ�ָ���������
	}
	
	# Ӧ��ʵ���Ļ�����ʼ�������붨�壬ÿ��Ӧ�ö������ѵĴ�����
	# �Ƿ񷵻ؽ��
	# ҳ����������(��aid,cnstr,tname��ҳ������)
	protected function _Init($Params = array()){
		
		$this->_Cfg['DynamicReturn'] = empty($Params['DynamicReturn']) ? false : true; # ��̬���������(true)/���(false)
		
		# ҳ����������
		$this->_SystemParams['frname'] = '';
		if(isset($Params['frname'])){
			$this->_SystemParams['frname'] = $Params['frname'];
		}elseif(isset($this->_QueryParams['frname'])){
			$this->_SystemParams['frname'] = $this->_QueryParams['frname'];
		}
		
	}
	
	# ���վ��ر�
	protected function _CheckSiteClosed(){
		cls_env::CheckSiteClosed(1);
	}
	
	# ��ͬ����ҳ�������ģ��
	protected function _ModelCumstom(){
		$this->_ReadFragment(); 			# ��ȡ��Ƭ����
		$this->_ParseSource();				# ��ȡ��Ƭ��ǩ
	}
	
	# ����ģ�͵�ͨ�ò���
	protected function _ModelCommon(){
		$this->_MainData(); # ��ȡҳ����������
		$this->_ReadPageCache(); # ��ȡҳ�滺��(������Ҫ����ҳ�����ϣ���ע�����˳��)
	}
	
	# ��ȡ��Ƭ����
	protected function _ReadFragment(){
		$this->_SystemParams['frname'] = cls_string::ParamFormat($this->_SystemParams['frname']);
		if($this->_SystemParams['frname']){
			$fragments = cls_cache::Read('fragments');
			$this->_Fragment = @$fragments[$this->_SystemParams['frname']];
		}
		if(!$this->_Fragment || empty($this->_Fragment['checked'])){
			throw new cls_PageException('��Ƭ'.$this->_SystemParams['frname'].'δ�����δ����');
		}
		if(($this->_Fragment['startdate'] > self::$timestamp) || ($this->_Fragment['enddate'] && $this->_Fragment['enddate'] < self::$timestamp)){
			throw new cls_PageException('��Ƭ'.$this->_SystemParams['frname'].'������Ч��');
		}
	}
	
	# ��ȡ��Ƭ��ǩ
	protected function _ParseSource(){
		$tname = 'fr_'.$this->_Fragment['ename'];
		$ttype = empty($this->_Fragment['tclass']) ? 'rtag' : 'ctag';
    	$this->_ParseSource = cls_cache::ReadTag($ttype,$tname);
		if(!$this->_ParseSource){
			throw new cls_PageException('δ�ҵ�ָ����ģ���ǩ��'.$tname);
		}
	}
		
	# ��ȡҳ����������
	protected function _MainData(){
		$_params = empty($this->_Fragment['params']) ? array() : array_filter(@explode(',', $this->_Adv['params']));
		$this->_Cfg['CacheKey'] = "frname={$this->_SystemParams['frname']}"; # ���ݻ���������ִ�
		foreach($_params as $key){
			if(isset($this->_InitParams[$key])){ # ͨ�����ε���Ĳ���
				$this->_MainData[$key] = intval($this->_InitParams[$key]);
			}elseif(isset($this->_QueryParams[$key])){ # GP����
				$this->_MainData[$key] = intval($this->_QueryParams[$key]);
			}
			if(!empty($this->_MainData[$key])){
				$this->_Cfg['CacheKey'] .= "$key={$this->_MainData[$key]}";
			}
		}
	}
	
	# ��ȡҳ�滺��
	protected function _ReadPageCache(){
		if(!_08_DEBUGTAG && $this->_Fragment['period']){
			$CacheFile = $this->_PageCacheFile();
			
			# Ϊ�˼���"�������"��"ֱ�Ӵ�ӡ"�����ַ�ʽ����ҳ�滺���������׳�������ֹ��������
			if(is_file($CacheFile) && (@filemtime($CacheFile) > (self::$timestamp - $this->_Fragment['period'] * 60))){
				$Content = read_htmlcac($CacheFile);
				throw new cls_PageCacheException($Content);
			}
		}	
	}
	
	# ���涯̬ҳ����
	protected function _SavePageCache($Content){
		if($this->_Fragment['period']){
			$CacheFile = $this->_PageCacheFile();
			save_htmlcac($Content,$CacheFile);
		}
	}

	# ��̬ҳ�滺���ļ���
	protected function _PageCacheFile(){
		if(empty($this->_Cfg['CacheFile'])){
			_08_FileSystemPath::checkPath(_08_CACHE_PATH.'fragment/'.$this->_SystemParams['frname'], true);
			$this->_Cfg['CacheFile'] = _08_CACHE_PATH.'fragment/'.$this->_SystemParams['frname'] . '/' . md5($this->_Cfg['CacheKey']).".php";
		}
		return $this->_Cfg['CacheFile'];
	}
	
	# �����̬���
	protected function _DynamicResultOut($Content){
		# ����ָ������
		$mcharset = cls_env::getBaseIncConfigs('mcharset');
		if($this->_Cfg['charset'] && $Content && $this->_Cfg['charset'] != $mcharset){
			$Content = cls_string::iconv($mcharset,$this->_Cfg['charset'],$Content);
		}
		
		if(!empty($this->_Cfg['frview']) || !empty($this->_Cfg['frdata'])){ # Ч��Ԥ����ֱ��������
			echo $Content;
			exit();
		}elseif(!empty($this->_Cfg['DynamicReturn'])){ # ����ԭʼ���(��ajax)
			return $Content;
		}else{ # ֱ��js���
			js_write($Content);
			exit();
		}
	}
	
}
