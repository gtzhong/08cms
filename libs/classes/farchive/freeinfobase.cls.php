<?php
/* 
** ����ҳ��ķ�������
** ���ô�����ģ��Ŀ¼��Ӧ�û���������Դ��ͬһ��,����Դ��ȡ�����ļ���������չ����(memcached)�ж�ȡ��
** ע�⣺Ϊ���ڻ�����ʹ����չ�ľ�̬�������ڻ�����ʹ�ã���չ��::method�����ʹ�ã�self::method������֧����չ����
*/
!defined('M_COM') && exit('No Permission');
class cls_FreeInfobase{
	
	# ��ȡ���ã�ͨ���Ի���ķ�ʽ����ȡ
	# �����ȡ��ȫ���������飬ָ��ID�����ã�ָ��ID��KEY������
    public static function Config($ID = 0,$Key = ''){
		$re = cls_cache::Read(cls_FreeInfo::CacheName());
		if($ID){
			$ID = cls_FreeInfo::InitID($ID);
			$re = isset($re[$ID]) ? $re[$ID] : array();
			if($Key){
				$re = isset($re[$Key]) ? $re[$Key] : '';
			}
		}
		return $re;
    }
	# ��������
    public static function CacheName(){
		return 'freeinfos';
    }
	
	# ��ID���г�ʼ��ʽ��
    public static function InitID($ID = 0){
		return max(0,intval($ID));
	}
	# ���»��棬���ֶλ��������ṩ��cls_CacheFileʹ��
	public static function UpdateCache(){
		cls_FreeInfo::SaveInitialCache();
	}
	
	# ����ҳ���Url
	function Url($fid = 0){
		$fid = cls_FreeInfo::InitID($fid);
		if(empty($fid) || !($FreeInfo = cls_FreeInfo::Config($fid))) return '#';
        $mconfigs = cls_cache::Read('mconfigs');
        if (!empty($mconfigs['virtualurl']) && !empty($mconfigs['rewritephp']))
        {
            $pageUrl = 'info' . $mconfigs['rewritephp'] . "fid-$fid.html";
        }
        else
        {
        	$pageUrl = "info.php?fid=$fid";
        }
		return cls_url::view_url(empty($FreeInfo['arcurl']) ? $pageUrl : $FreeInfo['arcurl']);
	}
	
	# ����ģ���е���ȫ����Դ���൱�ڸ������ݱ�
	public static function SaveInitialCache($CacheArray = ''){
		if(!is_array($CacheArray)){ # ���Դ������������
			$CacheArray = cls_FreeInfo::InitialInfoArray();
		}
		ksort($CacheArray);# ��������
		cls_CacheFile::Save($CacheArray,cls_FreeInfo::CacheName());
	}
	
	# ��̬���������飬ֱ�����Գ�ʼ����Դ(�����ݿ�/����)�����ں�̨����ʵʱ�ǻ������
	public static function InitialInfoArray(){
		$CacheArray = cls_cache::Read(cls_FreeInfo::CacheName(),'','',1);
		return $CacheArray;
	}
	
	# ��̬�ĵ������ϣ�ֱ���Գ�ʼ����Դ(�����ݿ�/����)�����ں�̨����ʵʱ�ǻ������
	public static function InitialOneInfo($ID){
		$ID = cls_FreeInfo::InitID($ID);
		$CacheArray = cls_FreeInfo::InitialInfoArray();
		return empty($CacheArray[$ID]) ? array() : $CacheArray[$ID];
	}
	# ���������һ�����õ���ʼ����Դ
	# ע�⣺$newConfig��Ԥ��Ϊ����addslahesת��������
	public static function ModifyOneConfig($newConfig = array(),$newID = 0){
		
		$newID = cls_FreeInfo::InitID($newID);
		cls_Array::array_stripslashes($newConfig);
		
		if($newID){
			if(!($oldConfig = cls_FreeInfo::InitialOneInfo($newID))){
				throw new Exception('��ָ����ȷ�Ķ���ҳID��');
			}
			$nowID = $oldConfig['fid'];
		}else{
			$newConfig['cname'] = trim(strip_tags(@$newConfig['cname']));
			if(!$newConfig['cname']){
				throw new Exception('����ҳ���ϲ���ȫ��');
			}
			if(!($nowID = auto_insert_id('freeinfos'))){
				throw new Exception('�޷��õ������Ķ���ҳID��');
			}
			$oldConfig = cls_FreeInfo::_OneBlankInfo($nowID);
		}
		
		# ��ʽ������
		if(isset($newConfig['cname'])){
			$newConfig['cname'] = trim(strip_tags($newConfig['cname']));
			$newConfig['cname'] = $newConfig['cname'] ? $newConfig['cname'] : $oldConfig['cname'];
		}
		if(isset($newConfig['tplname'])){
			_08_FilesystemFile::filterFileParam($newConfig['tplname']);
		}
		if(isset($newConfig['customurl'])){
			$newConfig['customurl'] = preg_replace("/^\/+/",'',trim($newConfig['customurl']));
		}
		if(isset($newConfig['canstatic'])){
			$newConfig['canstatic'] = empty($newConfig['canstatic']) ? 0 : 1;
			if(!$newConfig['canstatic']) $newConfig['arcurl'] = '';
		}
		
		# ��ֵ
		$InitConfig = cls_FreeInfo::_OneBlankInfo($nowID); # ��ȫ�����ýṹ
		foreach($InitConfig as $k => $v){
			if(in_array($k,array('fid'))) continue;
			if(isset($newConfig[$k])){ # ����ֵ
				$oldConfig[$k] = $newConfig[$k];
			}elseif(!isset($oldConfig[$k])){ # �²����ֶ�
				$oldConfig[$k] = $v;
			}
		}	
		
		# ����
		$CacheArray = cls_FreeInfo::InitialInfoArray();
		$CacheArray[$nowID] = $oldConfig;
		
		cls_FreeInfo::SaveInitialCache($CacheArray);
		
		return $nowID;
		
	}
	
	# ɾ��һ������
	public static function DeleteOne($ID){
		$ID = cls_FreeInfo::InitID($ID);
		if(!$ID || !($Info = cls_FreeInfo::InitialOneInfo($ID))) return '��ָ����ȷ�Ķ���ҳ��';
		if($re = cls_FreeInfo::UnStatic($ID,true)) return $re; # ͬʱ��Ҫɾ����Ӧ�ľ�̬�ļ�
		$CacheArray = cls_FreeInfo::InitialInfoArray();
		unset($CacheArray[$ID]);
		cls_FreeInfo::SaveInitialCache($CacheArray);
	}
		
	# ��һ������ҳ���ɾ�̬
	public static function ToStatic($fid=0){
		$re = cls_FreeinfoPage::Create(array('fid' => $fid,'inStatic' => true));
		return $re;
	}
	
	# �����ɾ��һ������ҳ�ľ�̬
	public static function UnStatic($fid=0,$isDelete = false){
		$fid = cls_FreeInfo::InitID($fid);
		if(!($FreeInfo = cls_FreeInfo::Config($fid))){
			return '��ָ����ȷ�Ķ���ҳ�档';
		}
		if($StaticFormat = cls_FreeInfo::_StaticFormat($fid)){
			m_unlink($StaticFormat);
		}
		# �����ɾ������ҳ������Ҫ���¼�¼
		if(!$isDelete){
			try {
				cls_FreeInfo::ModifyOneConfig(array('arcurl' => ''),$fid);
			} catch (Exception $e){
				return $e->getMessage();
			}
		}
	}
	
	# ϵͳĬ�ϵľ�̬��ʽ
	public static function DefaultFormat(){
		return '{$infodir}/f-{$fid}-{$page}.html';
	}
	
	# �õ���̬ҳ��ʽ������{$page}��δ����������Ϊռλ��
	public static function _StaticFormat($fid=0){
		$fid = cls_FreeInfo::InitID($fid);
		if(!($FreeInfo = cls_FreeInfo::Config($fid))) return '';
		$u = empty($FreeInfo['customurl']) ? cls_FreeInfo::DefaultFormat() : $FreeInfo['customurl'];
		return cls_url::m_parseurl($u,array('fid' => $fid,'infodir' => cls_env::GetG('infohtmldir'),));
	}
	
	# һ���½���¼�ĳ�ʼ������
	protected static function _OneBlankInfo($ID = 0){
		return array(
			'fid' => cls_FreeInfo::InitID($ID),
			'cname' => '',
			'tplname' => '',
			'customurl' => '',
			'arcurl' => '',
			'canstatic' => '1',
		);
	}
	
}
