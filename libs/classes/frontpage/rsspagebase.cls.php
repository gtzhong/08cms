<?php
/**
 * ������Ŀ�ڵ�Rssҳ��Ĵ������
 * �����ɾ�̬����ҳ������ҳ
 */
defined('M_COM') || exit('No Permission');
abstract class cls_RssPageBase extends cls_FrontPage{
	
 	protected $_Node = array(); 									# ��ǰ�ڵ����������
 	protected static $_ExtendAplicationClass = 'cls_RssPage'; 		# ��ǰ�������չӦ����(������)������
 	protected $_ErrorFlag = 0; 		                                # ������

	# ҳ�����ɵ��ⲿִ�����
	# ����������ο� function _Init
	public static function Create($Params = array()){
		return self::_iCreate(self::$_ExtendAplicationClass,$Params);
	}

	protected function __construct(){
		parent::__construct();
		$this->_Cfg['rss_enabled'] = cls_env::mconfig('rss_enabled');				# Rss�ر�
		$this->_Cfg['rss_ttl'] = max(0,intval(cls_env::mconfig('rss_ttl')));		# Rss��������
		//echo $this->_Cfg['rss_enabled'];
	}
	
	# Ӧ��ʵ���Ļ�����ʼ�������붨�壬ÿ��Ӧ�ö������ѵĴ�����
	protected function _Init($Params = array()){
		# ҳ����������
		$this->_SystemParams['cnstr'] = isset($Params['cnstr']) ? $Params['cnstr'] : cls_cnode::cnstr($this->_QueryParams); # �õ��ڵ��ִ�
		if(!$this->_SystemParams['cnstr']) $this->_SystemParams['cnstr'] = '';
	}
	
	# ��ͬ����ҳ�������ģ��
	protected function _ModelCumstom(){
		$this->_RssClosed(); # Rss�Ƿ�ر�
		$this->_Cnstr(); # �����ڵ��ִ�
		$this->_Node(); # ��ȡ�ڵ�
	}
	
	# �����ڵ��ִ�(Ԥ����չ�ӿ�)
	protected function _Cnstr(){}
	
	# RSS�Ƿ�����
	protected function _RssClosed(){
		if(empty($this->_Cfg['rss_enabled'])){ 
			$this->_ErrorFlag = 1; 	
			throw new cls_PageException('Rss��ͣ����');
		}	
	}
	# ��ȡ�ڵ�
	protected function _Node(){
		if($this->_SystemParams['cnstr']){
			if(!($this->_Node = cls_node::cnodearr($this->_SystemParams['cnstr']))){
				$this->_ErrorFlag = 1; 	
				throw new cls_PageException($this->_PageName()." - �����ڻ�δ����");
			}
		}
	}
	
	# ����ģ�͵�ͨ�ò��֣���ʹ��ͨ�û����еĴ�����
	protected function _ModelCommon(){
		$this->_ReadPageCache(); # ��ȡҳ�滺��(������Ҫ����ҳ�����ϣ���ע�����˳��)
		$this->_MainData(); # ��ȡҳ����������
		$this->_ParseSource(); # ���ҳ��ģ��
		$this->_Mdebug(); # ��ǰҳ�������Ϣ
	}
	
	# ��ȡҳ����������
	protected function _MainData(){
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
			$this->_ParseSource = cls_tpl::cn_tplname($this->_SystemParams['cnstr'],$this->_Node,0,'rsstpl');
		}else{
			$this->_ParseSource = cls_tpl::SpecialTplname('rss_index');
		}
		if(!$this->_ParseSource){
			$this->_ErrorFlag = 1; 	
			throw new cls_PageException($this->_PageName().' - δ��ģ��');
		}
	}
	
	# ��ǰҳ�������Ϣ
	protected function _Mdebug(){
		$_mdebug = cls_env::GetG('_mdebug');
		$_mdebug->setvar('tpl',$this->_ParseSource);
	}
	
	
	# ��ȡҳ�滺��
	protected function _ReadPageCache(){
		if(!_08_DEBUGTAG && $this->_Cfg['rss_ttl']){
			$CacheFile = $this->_PageCacheFile();
			
			# Ϊ�˼���"�������"��"ֱ�Ӵ�ӡ"�����ַ�ʽ����ҳ�滺���������׳�������ֹ��������
			if(is_file($CacheFile) && (@filemtime($CacheFile) > (self::$timestamp - $this->_Cfg['rss_ttl'] * 60))){
				$Content = read_htmlcac($CacheFile);
				//$this->_ErrorFlag = 1; 	
				throw new cls_PageCacheException($Content);
			}
		}	
	}
	
	# ���涯̬ҳ����
	protected function _SavePageCache($Content){
		if($this->_Cfg['rss_ttl']){
			$CacheFile = $this->_PageCacheFile();
			save_htmlcac($Content,$CacheFile);
		}
	}

	# ��̬ҳ�滺���ļ���
	protected function _PageCacheFile(){
		if(empty($this->_Cfg['CacheFile'])){
			$this->_Cfg['CacheFile'] = cls_cache::HtmlcacDir('rss').md5('rss'.$this->_SystemParams['cnstr']).'.php';
		}
		return $this->_Cfg['CacheFile'];
	}
	
	# �����̬���
	protected function _DynamicResultOut($Content){
		
		# ���XML(�����˾Ͳ���xml��ʽ�����)
		if(empty($this->_ErrorFlag)) header("Content-type: application/xml");
		echo $Content;
		exit();
	}
	
	# ҳ������
	protected function _PageName(){
		return $this->_SystemParams['cnstr'] ? "�ڵ�[{$this->_SystemParams['cnstr']}]Rss" : "ϵͳ��ҳRss";
	}
	
}
