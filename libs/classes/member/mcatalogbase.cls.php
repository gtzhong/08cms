<?php
/* 
** ��Ա�ռ����Ŀ������mcatalog.cls.php�Ļ���
** ע�⣺Ϊ���ڻ�����ʹ����չ�ľ�̬�������ڻ�����ʹ�ã���չ��::method�����ʹ�ã�self::method������֧����չ��
*/
!defined('M_COM') && exit('No Permission');
class cls_mcatalogbase{
	
	# ��ȡ���ã�ͨ���Ի���ķ�ʽ����ȡ
	# �����ȡ��ȫ���������飬ָ��ID�����ã�ָ��ID��KEY������
    public static function Config($ID = 0,$Key = ''){
		$re = cls_cache::Read(cls_mcatalog::CacheName());
		if($ID){
			$ID = cls_mcatalog::InitID($ID);
			$re = isset($re[$ID]) ? $re[$ID] : array();
			if($Key){
				$re = isset($re[$Key]) ? $re[$Key] : '';
			}
		}
		return $re;
    }
	
	# ȡ�� ID => ���� �������б�����
	# $mctidָ���Ŀռ䷽��ID��-1Ϊȫ����Ŀ��0Ϊδ���ÿռ���Ŀ
	# $AllowUclass=1ʱ��ֻ�г�������Ӹ��˷���Ŀռ���Ŀ
	public static function mcaidsarr($mctid = -1,$AllowUclass = 0){
		
		$narr = array();
		$mcatalogs = cls_mcatalog::Config();
		foreach($mcatalogs as $k => $v){
			if($AllowUclass && empty($v['maxucid'])) continue;
			$narr[$k] = "($k)".$v['title'];
		}
		if($mctid != -1){
			if(!$mctid || !($_msTpls = cls_mtconfig::Config($mctid,'setting'))) return array(); # ָ���Ŀռ䷽��������
			$narr = array_intersect_key($narr,$_msTpls);
		}
		return $narr;
	}
	
	
	# ��ID���г�ʼ��ʽ��
    public static function InitID($ID = 0){
		return max(0,intval($ID));
	}
	
	# ��������
    public static function CacheName(){
		return 'mcatalogs';
    }
	
	# ���»��棬���ֶλ��������ṩ��cls_CacheFileʹ��
	public static function UpdateCache(){
		cls_mcatalog::SaveInitialCache();
	}
	
	# ����ģ���е���ȫ����Դ���൱�ڸ������ݱ�
	public static function SaveInitialCache($CacheArray = ''){
		if(!is_array($CacheArray)){ # ���Դ������������
			$CacheArray = cls_mcatalog::InitialInfoArray();
		}
		cls_Array::_array_multisort($CacheArray,'vieworder',true);# ��vieworder��������
		cls_CacheFile::Save($CacheArray,cls_mcatalog::CacheName());
	}
	
	# ��̬���������飬ֱ�����Գ�ʼ����Դ(�����ݿ�/����)�����ں�̨����ʵʱ�ǻ������
	public static function InitialInfoArray(){
		$CacheArray = cls_cache::Read(cls_mcatalog::CacheName(),'','',1);
		return $CacheArray;
	}
	
	# ��̬�ĵ������ϣ�ֱ���Գ�ʼ����Դ(�����ݿ�/����)�����ں�̨����ʵʱ�ǻ������
	public static function InitialOneInfo($ID){
		$ID = cls_mcatalog::InitID($ID);
		$CacheArray = cls_mcatalog::InitialInfoArray();
		return empty($CacheArray[$ID]) ? array() : $CacheArray[$ID];
	}
	
	# ���������һ�����õ���ʼ����Դ
	# ע�⣺$newConfig��Ԥ��Ϊ����addslahesת��������
	public static function ModifyOneConfig($newConfig = array(),$newID = 0){
		
		$newID = cls_mcatalog::InitID($newID);
		cls_Array::array_stripslashes($newConfig);
		
		if($newID){
			if(!($oldConfig = cls_mcatalog::InitialOneInfo($newID))){
				throw new Exception('��ָ����ȷ�Ŀռ���ĿID��');
			}
			$nowID = $oldConfig['mcaid'];
		}else{
			$newConfig['title'] = trim(strip_tags(@$newConfig['title']));
			if(!$newConfig['title']){
				throw new Exception('�ռ���Ŀ���ϲ���ȫ��');
			}
			if(!($nowID = auto_insert_id('mcatalogs'))){
				throw new Exception('�޷��õ������Ŀռ���ĿID��');
			}
			$oldConfig = cls_mcatalog::_OneBlankInfo($nowID);
		}
		
		# ��ʽ������
		if(isset($newConfig['title'])){
			$newConfig['title'] = trim(strip_tags($newConfig['title']));
			$newConfig['title'] = $newConfig['title'] ? $newConfig['title'] : $oldConfig['title'];
		}
		if(isset($newConfig['maxucid'])){
			$newConfig['maxucid'] = max(0,intval($newConfig['maxucid']));
		}
		if(isset($newConfig['remark'])){
			$newConfig['remark'] = trim(strip_tags($newConfig['remark']));
		}
		if(isset($newConfig['dirname'])){
			$newConfig['dirname'] = cls_mcatalog::_NewDirname($newConfig['dirname'],$newID);
		}
		
		# ��ֵ
		$InitConfig = cls_mcatalog::_OneBlankInfo($nowID); # ��ȫ�����ýṹ
		foreach($InitConfig as $k => $v){
			if(in_array($k,array('mcaid'))) continue;
			if(isset($newConfig[$k])){ # ����ֵ
				$oldConfig[$k] = $newConfig[$k];
			}elseif(!isset($oldConfig[$k])){ # �²����ֶ�
				$oldConfig[$k] = $v;
			}
		}	
		
		# ����
		$CacheArray = cls_mcatalog::InitialInfoArray();
		$CacheArray[$nowID] = $oldConfig;
		cls_mcatalog::SaveInitialCache($CacheArray);
		
		return $nowID;
		
	}
	
	# ɾ��һ������
	public static function DeleteOne($ID){
		$ID = cls_mcatalog::InitID($ID);
		if(!$ID || !($Info = cls_mcatalog::InitialOneInfo($ID))) return '��ָ����ȷ�Ŀռ���Ŀ��';
		
		# ���¸��˷���
		$db = _08_factory::getDBO();
		$db->update('#__uclasses',array('mcaid' => 0))->where(array('mcaid' => $ID))->exec();
		
		$CacheArray = cls_mcatalog::InitialInfoArray();
		unset($CacheArray[$ID]);
		cls_mcatalog::SaveInitialCache($CacheArray);
	}
	# ȡ�úϷ��Ŀռ���Ŀ��̬Ŀ¼
	# newID=0��Ϊ�½��Ŀռ���Ŀȡ�þ�̬Ŀ¼
	protected static function _NewDirname($Dirname = '',$newID = 0){
		_08_FilesystemFile::filterFileParam($Dirname);
		$Dirname = strtolower($Dirname);
		if(!$Dirname) return '';
		
		$DirnameArray = cls_mcatalog::_DirnameArray();
		$CacheArray = cls_mcatalog::InitialInfoArray();
		foreach($CacheArray as $k => $v){
			if(empty($v['dirname'])) continue;
			if($Dirname == $v['dirname']){
				if($newID == $k){ # ����Ŀ����δ��
					continue;
				}else{ # ��������Ŀռ��
					while(in_array($Dirname,$DirnameArray)) $Dirname .= 'a';
				}
			}
		}
		return $Dirname;
	}
	# ȡ�����еĿռ���Ŀ��̬Ŀ¼
	protected static function _DirnameArray(){
		$CacheArray = cls_mcatalog::InitialInfoArray();
		$re = array();
		foreach($CacheArray as $k => $v){
			if(!empty($v['dirname'])){
				$re[] = strtolower($v['dirname']);
			}
		}
		return $re;
	}
	# һ���½���¼�ĳ�ʼ������
	protected static function _OneBlankInfo($ID = 0){
		return array(
			'mcaid' => cls_mcatalog::InitID($ID),
			'title' => '����',
			'maxucid' => '0',
			'vieworder' => '0',
			'remark' => '',
			'dirname' => '',
		);
	}
	
}
