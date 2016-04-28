<?php
/* 
** ��Ա�ռ�ģ�巽������mtconfig.cls.php�Ļ���
** ע�⣺Ϊ���ڻ�����ʹ����չ�ľ�̬�������ڻ�����ʹ�ã���չ��::method�����ʹ�ã�self::method������֧����չ��
*/
!defined('M_COM') && exit('No Permission');
class cls_mtconfigbase{
	
	# ��ȡ���ã�ͨ���Ի���ķ�ʽ����ȡ
	# �����ȡ��ȫ���������飬ָ��ID�����ã�ָ��ID��KEY������
    public static function Config($ID = 0,$Key = ''){
		$re = cls_cache::Read(cls_mtconfig::CacheName());
		if($ID){
			$ID = cls_mtconfig::InitID($ID);
			$re = isset($re[$ID]) ? $re[$ID] : array();
			if($Key){
				$re = isset($re[$Key]) ? $re[$Key] : '';
			}
		}
		return $re;
    }
	
	# ȡ��ָ����Ա(mid)��ʹ�õĿռ䷽��(����ָ��Keyȡֵ)
	# ���mtcid��֪����Ҫʹ�ô˷���
	public static function ConfigByMid($mid = 0,$Key = ''){
		global $db,$tblprefix;
		$re = array();
		if($mid = max(0,intval($mid))){
			if($mtcid = $db->result_one("SELECT mtcid FROM {$tblprefix}members WHERE mid='$mid'")){
				$re = cls_mtconfig::Config($mtcid);
			}
		}
		if($Key){
			$re = isset($re[$Key]) ? $re[$Key] : '';
		}
		return $re;
	}
	
	# ��ID���г�ʼ��ʽ��
    public static function InitID($ID = 0){
		return max(0,intval($ID));
	}
	
	# ��������
    public static function CacheName(){
		return 'mtconfigs';
    }
	
	# ���»��棬���ֶλ��������ṩ��cls_CacheFileʹ��
	public static function UpdateCache(){
		cls_mtconfig::SaveInitialCache();
	}
	
	# ����ģ���е���ȫ����Դ���൱�ڸ������ݱ�
	public static function SaveInitialCache($CacheArray = ''){
		if(!is_array($CacheArray)){ # ���Դ������������
			$CacheArray = cls_mtconfig::InitialInfoArray();
		}
		cls_Array::_array_multisort($CacheArray,'vieworder',true);# ��vieworder�������� //ksort($CacheArray);# ��������
		cls_CacheFile::Save($CacheArray,cls_mtconfig::CacheName());
	}
	
	# ��̬���������飬ֱ�����Գ�ʼ����Դ(�����ݿ�/����)�����ں�̨����ʵʱ�ǻ������
	public static function InitialInfoArray(){
		$CacheArray = cls_cache::Read(cls_mtconfig::CacheName(),'','',1);
		return $CacheArray;
	}
	
	# ��̬�ĵ������ϣ�ֱ���Գ�ʼ����Դ(�����ݿ�/����)�����ں�̨����ʵʱ�ǻ������
	public static function InitialOneInfo($ID){
		$ID = cls_mtconfig::InitID($ID);
		$CacheArray = cls_mtconfig::InitialInfoArray();
		return empty($CacheArray[$ID]) ? array() : $CacheArray[$ID];
	}
	
	# ���������һ�����õ���ʼ����Դ
	# ע�⣺$newConfig��Ԥ��Ϊ����addslahesת��������
	public static function ModifyOneConfig($newConfig = array(),$newID = 0){
		
		$newID = cls_mtconfig::InitID($newID);
		cls_Array::array_stripslashes($newConfig);
		
		if($newID){
			if(!($oldConfig = cls_mtconfig::InitialOneInfo($newID))){
				throw new Exception('��ָ����ȷ�Ŀռ�ģ�巽��ID��');
			}
			$nowID = $oldConfig['mtcid'];
		}else{
			$newConfig['cname'] = trim(strip_tags(@$newConfig['cname']));
			if(!$newConfig['cname']){
				throw new Exception('�ռ�ģ�巽�����ϲ���ȫ��');
			}
			if(!($nowID = auto_insert_id('mtconfigs'))){
				throw new Exception('�޷��õ������Ŀռ�ģ�巽��ID��');
			}
			$oldConfig = cls_mtconfig::_OneBlankInfo($nowID);
		}
		
		# ��ʽ������
		if(isset($newConfig['cname'])){
			$newConfig['cname'] = trim(strip_tags($newConfig['cname']));
			$newConfig['cname'] = $newConfig['cname'] ? $newConfig['cname'] : $oldConfig['cname'];
		}
		if(isset($newConfig['pmid'])){
			$newConfig['pmid'] = max(0,intval($newConfig['pmid']));
		}
		if(isset($newConfig['mchids'])){
			if(empty($newConfig['mchids'])){
				$newConfig['mchids'] = '';
			}elseif(is_array($newConfig['mchids'])){
				$newConfig['mchids'] = implode(',',array_filter($newConfig['mchids']));
			}
		}
		if(isset($newConfig['setting'])){
			$newConfig['setting'] = cls_mtconfig::_NewSetting($newConfig['setting']);
		}
		if(isset($newConfig['arctpls'])){
			$newConfig['arctpls'] = cls_mtconfig::_NewArctpls($newConfig['arctpls']);
		}
		
		# ��ֵ
		$InitConfig = cls_mtconfig::_OneBlankInfo($nowID); # ��ȫ�����ýṹ
		foreach($InitConfig as $k => $v){
			if(in_array($k,array('mtcid','issystem',))) continue;
			if(isset($newConfig[$k])){ # ����ֵ
				$oldConfig[$k] = $newConfig[$k];
			}elseif(!isset($oldConfig[$k])){ # �²����ֶ�
				$oldConfig[$k] = $v;
			}
		}	
		
		# ����
		$CacheArray = cls_mtconfig::InitialInfoArray();
		$CacheArray[$nowID] = $oldConfig;
		cls_mtconfig::SaveInitialCache($CacheArray);
		
		return $nowID;
		
	}
	# ɾ��һ������
	public static function DeleteOne($ID){
		$ID = cls_mtconfig::InitID($ID);
		if(!$ID || !($Info = cls_mtconfig::InitialOneInfo($ID))) return '��ָ����ȷ�Ŀռ�ģ�巽����';
		if(!empty($Info['issystem'])) return 'ϵͳ���÷�����ֹɾ����'; 
		
		$CacheArray = cls_mtconfig::InitialInfoArray();
		unset($CacheArray[$ID]);
		cls_mtconfig::SaveInitialCache($CacheArray);
	}
	
	# ��ʽ���ռ���Ŀ��ģ�������
	protected static function _NewSetting($Config = array()){
		$newConfig = array();
		if(!empty($Config[0]['index'])){
			_08_FilesystemFile::filterFileParam($Config[0]['index']);
			$newConfig[0]['index'] = $Config[0]['index'];
		}
		$mcatalogs = cls_mcatalog::InitialInfoArray();
		foreach($mcatalogs as $k => $v){
			if(isset($Config[$k])){
				$newConfig[$k] = array();
				foreach(array('index','list',) as $var){
					if(!empty($Config[$k][$var])){
						_08_FilesystemFile::filterFileParam($Config[$k][$var]);
						$newConfig[$k][$var] = $Config[$k][$var];
					}
				}
			}
		}
		return $newConfig;
	}
	# ��ʽ���ռ��ĵ�����ҳ��ģ�������
	protected static function _NewArctpls($Config = array()){
		$newConfig = array();
		$channels = cls_channel::Config();
		foreach(array('archive','ex1','ex2',) as $var){
			$newConfig[$var] = array();
			foreach($channels as $k => $v){
				if(!empty($Config[$var][$k])){
					  _08_FilesystemFile::filterFileParam($Config[$var][$k]);
					  $newConfig[$var][$k] = $Config[$var][$k];
				}
			}
			if(empty($newConfig[$var])) unset($newConfig[$var]);
		}
		return $newConfig;
	}
	# һ���½���¼�ĳ�ʼ������
	protected static function _OneBlankInfo($ID = 0){
		return array(
			'mtcid' => cls_mtconfig::InitID($ID),
			'cname' => '����',
			'issystem' => 0,
			'mchids' => '',
			'pmid' => 0,
			'vieworder' => '0',
			'setting' => array (),
			'arctpls' => array (),
		);
	}
	
}
