<?php
/**
 * Sitemapҳ��Ĵ������
 * 
 */
defined('M_COM') || exit('No Permission');
abstract class cls_SitemapPageBase extends cls_FrontPage{
	
  	protected static $_ExtendAplicationClass = 'cls_SitemapPage'; 	# ��ǰ�������չӦ����(������)������
 	protected $_Sitemap = array(); 									# ָ��Sitemap����
	
	# ҳ�����ɵ��ⲿִ�����
	# ����������ο� function _Init
	public static function Create($Params = array()){
		return self::_iCreate(self::$_ExtendAplicationClass,$Params);
	}

	protected function __construct(){
		parent::__construct();
		$this->_Cfg['maxStaicPage'] = 1; 									# ע�⣺Sitemapҳ��û�з�ҳ
	}
	
	# Ӧ��ʵ���Ļ�����ʼ��
	protected function _Init($Params = array()){
		$this->_inStatic = empty($Params['inStatic']) ? false : true; 	# �Ƿ�̬	
		
		# ҳ����������
		$this->_SystemParams['map'] = '';
		if(isset($Params['map'])){
			$this->_SystemParams['map'] = trim($Params['map']);
		}elseif(isset($this->_QueryParams['map'])){
			$this->_SystemParams['map'] = trim($this->_QueryParams['map']);
		}
		if(!$this->_SystemParams['map']) $this->_SystemParams['map'] = 'google';
	}
	
	# ��ͬ����ҳ�������ģ��
	protected function _ModelCumstom(){
		$this->_ReadSitemap(); # ��ȡSitemap����
	}
	
	# ��ȡSitemap����
	protected function _ReadSitemap(){
		$sitemaps = cls_cache::Read('sitemaps');
		if(!($this->_Sitemap = $sitemaps[$this->_SystemParams['map']])){
			throw new cls_PageException($this->_PageName().'δ����');
		}
		if(empty($this->_Sitemap['available'])){
			throw new cls_PageException('ָ����Sitemap�ѹر�');
		}

		_08_FilesystemFile::filterFileParam(@$this->_Sitemap['xml_url']);
		if(empty($this->_Sitemap['xml_url'])){
			throw new cls_PageException('������Sitemap��XML�����ļ���');
		}
		$this->_Sitemap['ttl'] = min(24,max(0,intval(@$this->_Sitemap['ttl'])));
	}
	
	# ����ģ�͵�ͨ�ò��֣���ʹ��ͨ�û����еĴ�����
	protected function _ModelCommon(){
		if(!$this->_inStatic){
			$this->_ReadPageCache(); # ��ȡҳ�滺��(������Ҫ����ҳ�����ϣ���ע�����˳��)
		}
		$this->_MainData(); # ��ȡҳ����������
		$this->_ParseSource(); # ���ҳ��ģ��
		$this->_Mdebug(); # ��ǰҳ�������Ϣ
	}
	
	# ��ȡҳ�滺��
	protected function _ReadPageCache(){
		if(!_08_DEBUGTAG && !empty($this->_Sitemap['ttl'])){
			$CacheFile = $this->_PageCacheFile();
			if(is_file($CacheFile) && (@filemtime($CacheFile) > (self::$timestamp - $this->_Sitemap['ttl'] * 3600))){
				$Content = file2str($CacheFile);
				throw new cls_PageCacheException($Content);
			}
		}
	}
	
	# ��ȡҳ����������
	protected function _MainData(){
		$this->_MainData = array(
			'ttl' => $this->_Sitemap['ttl'],
			'adminemail' => cls_env::getBaseIncConfigs('adminemail'),
		);
	}
	
	# ���ҳ��ģ��
	protected function _ParseSource(){
		$this->_ParseSource = @$this->_Sitemap['tpl'];
		
		if(!$this->_ParseSource){
			throw new cls_PageException($this->_PageName().' - δ��ģ��');
		}
	}
	
	# ��ǰҳ�������Ϣ
	protected function _Mdebug(){
		$_mdebug = cls_env::GetG('_mdebug');
		$_mdebug->setvar('tpl',$this->_ParseSource);
		if($this->_inStatic){ # ��̬ʱ�����̬Url
			$_mdebug->setvar('uri',"sitemap.php?map={$this->_SystemParams['map']}");
			cls_env::SetG('_no_dbhalt',true); # ��̬ʱ�ر�SQL�жϴ��� ????
		}
	}
	
	# ���涯̬ҳ����
	protected function _SavePageCache($Content){
		if(!empty($this->_Sitemap['ttl'])){
			$CacheFile = $this->_PageCacheFile();
			if(!@str2file($Content,$CacheFile)){
				throw new cls_PageException($this->_Sitemap['xml_url']."�޷�д��");
			}
		}
	}

	# ��̬ҳ�滺���ļ���
	protected function _PageCacheFile(){
		if(empty($this->_Cfg['CacheFile'])){
			$this->_Cfg['CacheFile'] = M_ROOT.$this->_Sitemap['xml_url'];
		}
		return $this->_Cfg['CacheFile'];
	}
	
	# ���/���ض�̬���
	protected function _DynamicResultOut($Content){
		# ���XML
		header("Content-type: application/xml");
		echo $Content;
		exit();
	}
	
	# ȡ�÷�ҳ_��̬�ļ������ʽ
	protected function _StaticFilePre(){
		return $this->_Sitemap['xml_url'];	
	}
	
	# ҳ������
	protected function _PageName(){
		return "Sitemap[{$this->_SystemParams['map']}]";
	}
	
}
