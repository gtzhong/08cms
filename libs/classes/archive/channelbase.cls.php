<?php
/* 
** ����ģ�͵ķ�������
** ע�⣺Ϊ���ڻ�����ʹ����չ�ľ�̬�������ڻ�����ʹ�ã���չ��::method�����ʹ�ã�self::method������֧����չ��
*/
!defined('M_COM') && exit('No Permission');
class cls_channelbase{
	
	public static function Table($NoPre = false){
		return ($NoPre ? '' : '#__').'channels';
	}
	
	# ��ȡ���ã�ͨ���Ի���ķ�ʽ����ȡ
	# �����ȡ��ȫ���������飬ָ��ID�����ã�ָ��ID��KEY������
    public static function Config($ID = 0,$Key = ''){
		$re = cls_cache::Read(cls_channel::CacheName());
		if($ID){
			$ID = cls_channel::InitID($ID);
			$re = isset($re[$ID]) ? $re[$ID] : array();
			if($Key){
				$re = isset($re[$Key]) ? $re[$Key] : '';
			}
		}
		return $re;
    }
	# ��������
    public static function CacheName(){
		return 'channels';
    }
	
	# ��ID���г�ʼ��ʽ��
    public static function InitID($ID = 0){
		return max(0,intval($ID));
	}
	# ���� ID=>���� ���б�����
	public static function chidsarr($all=0,$noViewID = 0){
		$channels = cls_channel::Config();
		$narr = array();
		foreach($channels as $k => $v){
			if($all || $v['available']){
				if(!$noViewID) $v['cname'] .= "($k)";
				$narr[$k] = $v['cname'];
			}
		}
		return $narr;
	}
	
	# ���»���
	public static function UpdateCache(){
		$CacheArray = self::InitialInfoArray();
		foreach($CacheArray as &$r){
			unset($r['vieworder'],$r['content'],$r['cfgs0'],$r['remark']);
			cls_CacheFile::ArrayAction($r,'cfgs','extract');
		}
		cls_CacheFile::Save($CacheArray,cls_channel::CacheName());
	}

	# ��̬���������飬ֱ�����Գ�ʼ����Դ(�����ݿ�/����)�����ں�̨����ʵʱ�ǻ������
	public static function InitialInfoArray(){
		$re = array();
		$db = _08_factory::getDBO();
		$db->select('*')->from(cls_channel::Table())->order('vieworder,chid')->exec();
		while($r = $db->fetch()){
			cls_CacheFile::ArrayAction($r,'cfgs','varexport');
			$re[$r['chid']] = $r;
		}
		return $re;
	}
	
	# ��̬�ĵ������ϣ�ֱ���Գ�ʼ����Դ(�����ݿ�/����)�����ں�̨����ʵʱ�ǻ������
	public static function InitialOneInfo($id){
		if(!($id = cls_channel::InitID($id))) return array();
		$db = _08_factory::getDBO();
		$re = $db->select('*')->from(cls_channel::Table())->where(array('chid' => $id))->exec()->fetch();
		cls_CacheFile::ArrayAction($re,'cfgs','varexport');
		return $re ? $re : array();
	}
	
	
}
