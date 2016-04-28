<?php
/**
 * ����ǩ�Ĵ������
 *
 */
defined('M_COM') || exit('No Permission');
abstract class cls_AdvTagBase extends cls_FrontPage{
	
   	protected static $_ExtendAplicationClass = 'cls_AdvTag'; 	# ��ǰ�������չӦ����(������)������
 	protected $_Adv = array(); 									# ָ�����λ����
 	protected $_InitParams = array(); 							# �ݴ��ⲿ����
	
	# ҳ�����ɵ��ⲿִ�����
	# ����������ο� function _Init
	public static function Create($Params = array()){
		return self::_iCreate(self::$_ExtendAplicationClass,$Params);
	}

	protected function __construct(){
		parent::__construct();
		$this->_SourceType = 'adv'; # ��Դ����
		$this->_Cfg['adv_period'] = max(0,intval(cls_env::mconfig('adv_period'))); 			# ������ݻ�������
		$this->_Cfg['adv_viewscache'] = max(0,intval(cls_env::mconfig('adv_viewscache'))); 	# �������ͳ������
		$this->_Cfg['adv_view'] = empty($this->_QueryParams['adv_view']) ? false : true; 	# Ԥ�����Ч��
	}
	
	# Ӧ��ʵ���Ļ�����ʼ�������붨�壬ÿ��Ӧ�ö������ѵĴ�����
	# �Ƿ񷵻ؽ��
	# ҳ����������(��aid,cnstr,tname��ҳ������)
	protected function _Init($Params = array()){
		
		$this->_Cfg['DynamicReturn'] = empty($Params['DynamicReturn']) ? false : true; # ��̬���������(true)/���(false)
		
		# ҳ����������
		$this->_SystemParams['fcaid'] = '';
		if(isset($Params['fcaid'])){
			$this->_SystemParams['fcaid'] = $Params['fcaid'];
		}elseif(isset($this->_QueryParams['fcaid'])){
			$this->_SystemParams['fcaid'] = $this->_QueryParams['fcaid'];
		}
		
		# �������ݴ�
		$this->_InitParams = $Params;
	}
	
	# ���վ��ر�
	protected function _CheckSiteClosed(){
		cls_env::CheckSiteClosed(1);
	}
	
	# ��ͬ����ҳ�������ģ��
	protected function _ModelCumstom(){
		$this->_ReadAdv(); 			# ��ȡ���λ����
		$this->_ParseSource();		# ��ȡ����ǩ
		
	}
	
	# ����ģ�͵�ͨ�ò���
	protected function _ModelCommon(){
		$this->_MainData(); # ��ȡҳ����������
		$this->_ReadPageCache(); # ��ȡҳ�滺��(������Ҫ����ҳ�����ϣ���ע�����˳��)
	}
	
	# ��ȡ���λ����
	protected function _ReadAdv(){
		$this->_SystemParams['fcaid'] = cls_fcatalog::InitID($this->_SystemParams['fcaid']);
		
		if($this->_SystemParams['fcaid']){
			$this->_Adv = cls_cache::Read('fcatalog',$this->_SystemParams['fcaid']);
		}
		if(!$this->_Adv || empty($this->_Adv['checked'])){
			throw new cls_PageException('���λδ�����δ����');
		}
	}
	
	# ��ȡ����ǩ
	protected function _ParseSource(){
    	$this->_ParseSource = cls_cache::ReadTag('advtag','adv_'.$this->_SystemParams['fcaid']);
		if(!$this->_ParseSource){
			throw new cls_PageException('δ�ҵ�ָ����ģ���ǩ');
		}
	}
		
	# ��ȡҳ����������
	protected function _MainData(){
		$_params = empty($this->_Adv['params']) ? array() : array_filter(@explode(',', $this->_Adv['params']));
		$this->_Cfg['CacheKey'] = "fcaid={$this->_SystemParams['fcaid']}"; # ���ݻ���������ִ�
		foreach($_params as $k => $v){
			if (!empty($v)){
				list($key, ) = explode(':', $v); // ��ʽ:"������":"����ֵ",�磺farea:{ccid20}
				if(isset($this->_InitParams[$key])){
					$this->_MainData[$key] = intval($this->_InitParams[$key]);
				}elseif(isset($this->_QueryParams[$key])){
					$this->_MainData[$key] = intval($this->_QueryParams[$key]);
				}
				if(!empty($this->_MainData[$key])){
					$this->_Cfg['CacheKey'] .= "$key={$this->_MainData[$key]}";
				}
			}    
		}
	}
	
	# ��ȡҳ�滺��
	protected function _ReadPageCache(){
		if(!_08_DEBUGTAG && $this->_Cfg['adv_period']){
			$CacheFile = $this->_PageCacheFile();
			
			# Ϊ�˼���"�������"��"ֱ�Ӵ�ӡ"�����ַ�ʽ����ҳ�滺���������׳�������ֹ��������
			if(is_file($CacheFile) && (@filemtime($CacheFile) > (self::$timestamp - $this->_Cfg['adv_period'] * 60))){
				$Content = read_htmlcac($CacheFile);
				throw new cls_PageCacheException($Content);
			}
		}	
	}
	
	# ���涯̬ҳ����
	protected function _SavePageCache($Content){
		if($this->_Cfg['adv_period']){
			$CacheFile = $this->_PageCacheFile();
			save_htmlcac($Content,$CacheFile);
		}
	}

	# ��̬ҳ�滺���ļ���
	protected function _PageCacheFile(){
		if(empty($this->_Cfg['CacheFile'])){
			_08_FileSystemPath::checkPath(_08_CACHE_PATH.'adv_cache/adv_' . $this->_SystemParams['fcaid'], true);
			$this->_Cfg['CacheFile'] = _08_CACHE_PATH.'adv_cache/adv_'. $this->_SystemParams['fcaid'] . '/' . md5($this->_Cfg['CacheKey']).".php";
		}
		return $this->_Cfg['CacheFile'];
	}
	
	# ���/���ض�̬���
	protected function _DynamicResultOut($Content){
		$this->_AdvViews($Content);
		if(!empty($this->_Cfg['adv_view'])){ # Ч��Ԥ����ֱ��������
			echo '<style type="text/css"> li{ list-style:none; } img { border:0px; } </style>';
			echo "\n<p style='font-size:12px'> &nbsp; ����Ϊ����Ԥ��Ч��,ǰ̨��css,html��Ӱ���չʾ��ͬЧ��; �հױ�ʾ������.</p><hr>\n";
			echo $Content;
			exit();
		}elseif(!empty($this->_Cfg['DynamicReturn'])){ # ����ԭʼ���(��ajax)
			return $Content;
		}else{ # ֱ��js���
			js_write($Content);
			exit();
		}
	}
	
	# ��������ͳ��
	protected function _AdvViews($Content){
		if ( empty($this->_Cfg['CacheKey']) )
        {
            return NULL;          
        }
		_08_FileSystemPath::checkPath(_08_CACHE_PATH.'stats/adv_'. $this->_SystemParams['fcaid'], true);
		$viewscachefile = _08_CACHE_PATH.'stats/adv_'. $this->_SystemParams['fcaid'] . '/' . md5($this->_Cfg['CacheKey']).".views";
		
		$file = _08_FilesystemFile::getInstance();
		$views = _08_Advertising::getViews($Content);
		if(is_file($viewscachefile) && !empty($this->_Cfg['adv_viewscache'])) {
			$file->_fopen($viewscachefile, 'rb');
			if( $file->_flock(LOCK_SH) )
			{
				$filestr = (string) $file->_fread(128);
				$file->_flock(LOCK_UN);        
			}
            else
            {
            	$filestr = '';
            }
			$time = (int)substr($filestr, 0, strpos($filestr, ','));
		} else {
			$time = self::$timestamp;
		}
		
		if(is_file($viewscachefile) && ($time > (self::$timestamp - $this->_Cfg['adv_viewscache'] * 60))) {
			$file->_fopen($viewscachefile, 'ab+');
			if( $file->_flock() )
			{
				$file->_fwrite(',' . implode(',', $views));
				$file->_flock(LOCK_UN);        
			}
		} else {
			is_file($viewscachefile) && _08_Advertising::setViews($viewscachefile);
			$file->_fopen($viewscachefile, 'wb');
			
			if( $file->_flock() )  # ��������棬����Ҫд���ļ���ֱ�ӽ�views�������ݱ�????????????????????????????
			{
				$file->_fwrite(time() . ',' . implode(',', $views));
				$file->_flock(LOCK_UN);        
			}
		}
		
		$file->_fclose();
	}
	
}
