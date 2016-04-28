<?php
/* 
** ����ģ�͵ķ�������
** ʹ��ģ�����û�����б�������Դ��Ŀǰ ��ȫ����Դ=Ӧ�û��档
** ע�⣺Ϊ���ڻ�����ʹ����չ�ľ�̬�������ڻ�����ʹ�ã���չ��::method�����ʹ�ã�self::method������֧����չ����
*/
!defined('M_COM') && exit('No Permission');
class cls_fchannelbase{


	# ��ȡ���ã�ͨ���Ի���ķ�ʽ����ȡ
	# �����ȡ��ȫ���������飬ָ��ID�����ã�ָ��ID��KEY������
    public static function Config($chid = 0,$Key = ''){
		$re = cls_cache::Read(cls_fchannel::CacheName());
		if($chid){
			$chid = cls_fchannel::InitID($chid);
			$re = isset($re[$chid]) ? $re[$chid] : array();
			if($Key){
				$re = isset($re[$Key]) ? $re[$Key] : '';
			}
		}
		return $re;
    }
	
	# ��ȡ�ֶ�����
    public static function Field($chid = '',$FieldName = ''){
		$re = array();
		if(cls_fchannel::Config($chid)){
			$re = cls_cache::Read('ffields',$chid);
			if($FieldName){
				$re = isset($re[$FieldName]) ? $re[$FieldName] : array();
			}
		}
		return $re;
    }
	
	# ��ID���г�ʼ��ʽ��
    public static function InitID($chid){
		return max(0,intval($chid));
	}
	
	# ��������
    public static function CacheName(){
		return 'fchannels';
    }
	
	# �õ����������ݱ�
	# chid = 0ʱ��ʾΪ��������Ϊģ�ͱ�
	public static function ContentTable($chid = 0){
		$chid = (int)$chid;
		return 'farchives'.($chid ? "_$chid" : '');
	}
	
	# ���� ID=>���� ���б�����
	public static function fchidsarr(){
		$fchannels = cls_cache::Read(cls_fchannel::CacheName());
		$narr = array();
		foreach($fchannels as $k => $v) $narr[$k] = $v['cname']."($k)";
		return $narr;
	}
	
	# ����Ӧ�û���
	public static function UpdateCache(){
		cls_fchannel::_SaveCache();
	}
	
	# ��������Դ���൱�ڸ������ݱ���Ҫ��������
	public static function SaveInitialCache($CacheArray = array()){
		cls_fchannel::_SaveCache($CacheArray);
	}
	
	# ��̬�ĸ���ģ���������飬ֱ�����Գ�ʼ����Դ(�����ݿ�/����)�����ں�̨����ʵʱ�ǻ������
	public static function InitialInfoArray(){
		return cls_cache::Read(cls_fchannel::CacheName(),'','',1);
	}
	
	# ��̬�ĵ�������ģ�����ϣ�ֱ���Գ�ʼ����Դ(�����ݿ�/����)�����ں�̨����ʵʱ�ǻ������
	public static function InitialOneInfo($chid){
		$chid = (int)$chid;
		$CacheArray = cls_fchannel::InitialInfoArray();
		return empty($CacheArray[$chid]) ? array() : $CacheArray[$chid];
	}
	
	# ɾ��һ����¼
	public static function DeleteOne($chid = 0){
		if(!($fchannel = cls_fchannel::InitialOneInfo($chid))) cls_message::show('ָ����ģ�Ͳ����ڡ�');
		
		# ����Ƿ�����صĸ�������
		$fcatalogs = cls_fcatalog::InitialInfoArray();
		foreach($fcatalogs as $k => $v){
			if($v['chid'] == $chid) cls_message::show('����ɾ��������ĸ������ࡣ');
		}
		
		# ɾ��ģ�ͱ�
		$db = _08_factory::getDBO();
		$db->dropTable('#__'.cls_fchannel::ContentTable($chid),true);
		
		# ɾ���ֶ����ü�¼������
		cls_fieldconfig::DeleteOneSourceFields('fchannel',$chid);
		
		# ���µ�ǰ����Դ������
		$CacheArray = cls_fchannel::InitialInfoArray();
		unset($CacheArray[$chid]);
		cls_fchannel::SaveInitialCache($CacheArray);
	}
	
	# �½�һ����¼
	# ������Ҫ���������rollback???
	public static function AddOne($UserConfig = array()){
		global $db,$tblprefix,$dbcharset;
		$CacheArray = cls_fchannel::InitialInfoArray();
		if($newID = auto_insert_id('fchannels')){
			
			# ���ɸ���ģ�ͼ�¼
			cls_Array::array_stripslashes($UserConfig);
			$CacheArray[$newID] = array_merge(cls_fchannel::_OneBlankInfo($newID),$UserConfig);
			cls_fchannel::SaveInitialCache($CacheArray);//�ȸ��»���
			
			# �������ݱ�
			$db->query("CREATE TABLE {$tblprefix}".cls_fchannel::ContentTable($newID)." (
						aid mediumint(8) unsigned NOT NULL default '0',
						PRIMARY KEY (aid))".(mysql_get_server_info() > '4.1' ? " ENGINE=MYISAM DEFAULT CHARSET=$dbcharset" : " TYPE=MYISAM"));
			
			# �����ֶ����ü�¼
			$newField = array(
				'ename' => 'subject', 
				'cname' => '����', 
				'datatype' => 'text', 
				'type' => 'f',
				'tbl' => cls_fchannel::ContentTable(), 
				'tpid' => $newID, 
				'issystem' => '1', 
				'iscommon' => '1', 
				'available' => '1', 
				'length' => '255', 
				'notnull' => '1', 
			);
			cls_fieldconfig::ModifyOneConfig('fchannel',$newID,$newField);
		}
		return $newID ? $newID : 0;
	}
	
	# һ���½���¼�ĳ�ʼ������
	protected static function _OneBlankInfo($ID = 0){
		return array(
				'chid' => $ID,
				'cname' => '',
		);
	}
	
	# ����Ӧ�û���/��ȫ����Դ��
	protected static function _SaveCache($CacheArray = ''){
		if(!is_array($CacheArray)){ # ���ļ�ˢ�»���
			$CacheArray = cls_fchannel::InitialInfoArray();
		}else{ # ���Դ������������
			ksort($CacheArray);# ��������
		}
		cls_CacheFile::Save($CacheArray,cls_fchannel::CacheName());
	}
	
	
	
}
