<?php
/* 
** ���ͷ�����йط�������
** ĿǰӦ�û�������ȫ����Դ��ͬһ��
** ע�⣺Ϊ���ڻ�����ʹ����չ�ľ�̬�������ڻ�����ʹ�ã���չ��::method�����ʹ�ã�self::method������֧����չ��
*/
!defined('M_COM') && exit('No Permission');
class cls_PushTypeBase{
	
	# ��������
    public static function CacheName(){
		return 'pushtypes';
    }
	
	# ���� ID=>���� ���б�����
	public static function ptidsarr(){
		$pushtypes = cls_cache::Read(cls_PushType::CacheName());
		$narr = array();
		foreach($pushtypes as $k => $v) $narr[$k] = $v['title'];
		return $narr;
	}
	
	# ���»��棬���ֶλ��������ṩ��cls_CacheFileʹ��
	public static function UpdateCache(){
		cls_PushType::SaveInitialCache();
	}
	
	# ����ģ���е���ȫ����Դ���൱�ڸ������ݱ�
	public static function SaveInitialCache($CacheArray = ''){
		if(!is_array($CacheArray)){ # ���Դ������������
			$CacheArray = cls_PushType::InitialInfoArray();
		}
		cls_Array::_array_multisort($CacheArray,'vieworder',true); # ��vieworder��������
		cls_CacheFile::Save($CacheArray,cls_PushType::CacheName());
	}
	
	# ��̬���������飬ֱ�����Գ�ʼ����Դ(�����ݿ�/����)�����ں�̨����ʵʱ�ǻ������
	public static function InitialInfoArray(){
		$CacheArray = cls_cache::Read(cls_PushType::CacheName(),'','',1);
		return $CacheArray;
	}
	
	# ��̬�ĵ������ϣ�ֱ���Գ�ʼ����Դ(�����ݿ�/����)�����ں�̨����ʵʱ�ǻ������
	public static function InitialOneInfo($id){
		
		$id = (int)$id;
		$CacheArray = cls_PushType::InitialInfoArray();
		return empty($CacheArray[$id]) ? array() : $CacheArray[$id];
		
	}
	# ���������һ�����õ���ʼ����Դ
	public static function ModifyOneConfig($newConfig = array(),$newID = 0){
		
		$newID = (int)$newID;
		cls_Array::array_stripslashes($newConfig);
		if($newID){
			if(!($oldConfig = cls_PushType::InitialOneInfo($newID))) cls_message::show('��ָ����ȷ�����ͷ��ࡣ');
			$nowID = $oldConfig['ptid'];
		}else{
			$newConfig['title'] = trim(strip_tags(@$newConfig['title']));
			if(!$newConfig['title']) cls_message::show('�������ϲ���ȫ');
			$nowID = auto_insert_id('pushtypes');
			if(!$nowID) cls_message::show('�޷��õ����������ͷ���ID��');
			$oldConfig = cls_PushType::_OneBlankInfo($nowID);
		}
		
		# ��ʽ������
		if(isset($newConfig['title'])){
			$newConfig['title'] = trim(strip_tags($newConfig['title']));
			$newConfig['title'] = $newConfig['title'] ? $newConfig['title'] : $oldConfig['title'];
		}
		if(isset($newConfig['remark'])){
			$newConfig['remark'] = trim(strip_tags($newConfig['remark']));
		}
		if(isset($newConfig['vieworder'])){
			$newConfig['vieworder'] = max(0,intval($newConfig['vieworder']));
		}
		
		# ��ֵ
		$InitConfig = cls_PushType::_OneBlankInfo($nowID); # ��ȫ�����ýṹ
		foreach($InitConfig as $k => $v){
			if(in_array($k,array('ptid'))) continue;
			if(isset($newConfig[$k])){ # ����ֵ
				$oldConfig[$k] = $newConfig[$k];
			}elseif(!isset($oldConfig[$k])){ # �²����ֶ�
				$oldConfig[$k] = $v;
			}
		}	
		
		# ����
		$CacheArray = cls_PushType::InitialInfoArray();
		$CacheArray[$nowID] = $oldConfig;
		cls_PushType::SaveInitialCache($CacheArray);
		
		return $nowID;
		
	}
	public static function DeleteOne($ID){
		
		$ID = (int)$ID;
		if(!$ID || !($Info = cls_PushType::InitialOneInfo($ID))) return '��ָ����ȷ�����ͷ��ࡣ';
		
		if($PushAreas = cls_pusharea::InitialInfoArray($ID)){
			return '����ɾ�������ڵ�����λ��';
		}
		
		$CacheArray = cls_PushType::InitialInfoArray();
		unset($CacheArray[$ID]);
		cls_PushType::SaveInitialCache($CacheArray);
	}
	
	# һ���½���¼�ĳ�ʼ������
	protected static function _OneBlankInfo($ID = 0){
		return array(
			'ptid' => (int)$ID,
			'title' => '',
			'vieworder' => '0',
			'remark' => '',
		);
	}
	

	
}
