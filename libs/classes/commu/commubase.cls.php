<?php
/* 
** ������Ŀ�ķ�������
** ע�⣺Ϊ���ڻ�����ʹ����չ�ľ�̬�������ڻ�����ʹ�ã���չ��::method�����ʹ�ã�self::method������֧����չ��
*/
!defined('M_COM') && exit('No Permission');
class cls_commubase{
	
	# ���õ����ݱ���
	public static function Table($NoPre = false){
		return ($NoPre ? '' : '#__').'acommus';
	}
	
	# ��������
    public static function CacheName(){
		return 'commus';
    }
	
	# �������ݱ�ı���
    public static function ContentTable($cuid = 0){
		return cls_commu::Config($cuid,'tbl');
    }
	
	# ����ָ��������Ϣ��ָ������λ
	# loadtype : 0.�ֶ�����, 11.�ֶ����, 21.�Զ�����
	public static function push($cuid,$cid,$paid,$loadtype=0){
		if($PushArea = cls_PushArea::Config($paid)){
			if($cuid != $PushArea['sourceid']) return false;
			if($Info = cls_commu::OneInfo($cid,$cuid)){
				return cls_pusher::push($Info,$paid,$loadtype);
			}
		}
		return false;
	}
	# �Զ�����
	public static function autopush($cuid,$cid){ 
		$pa = cls_pusher::paidsarr('commus',$cuid); 
		foreach($pa as $paid=>$paname){ 
			$pusharea = cls_PushArea::Config($paid);
			if(!empty($pusharea['autopush'])){ //���÷���ֵ
				cls_commu::push($cuid,$cid,$paid,21); 
			}
		}
	}
	# ����aid,mid,����ɾ������λ; ����userbase.cls.php/arcedit.cls.php�е�ɾ������
	// cuid,key,kid��,�������������
	public static function delpushs($cuid,$key='mid',$kid='0'){ 
		global $db,$tblprefix;
		$table = cls_commu::ContentTable($cuid);
		$query = $db->query("SELECT cid FROM {$tblprefix}$table WHERE $key='$kid'");
		while($r = $db->fetch_array($query)){
			cls_pusher::DelelteByFromid($r['cid'],'commus',$cuid);
		}
	}
	
	# ��ȡһ��������Ϣ��ֻ����й������ݱ�Ľ���
	public static function OneInfo($cid,$cuid){
		$re = array();
		if(!($cid = (int)$cid)) return $re;
		if($ContentTable = cls_commu::ContentTable($cuid)){
			global $db,$tblprefix;
			$re = $db->fetch_one("SELECT * FROM {$tblprefix}$ContentTable WHERE cid='$cid'");
		}
		return $re ? $re : array();
	}
	
	
	# ��ȡ���ã�ͨ���Ի���ķ�ʽ����ȡ
	# �����ȡ��ȫ���������飬ָ��ID�����ã�ָ��ID��KEY������
    public static function Config($cuid = 0,$Key = ''){
		$re = cls_cache::Read(cls_commu::CacheName());
		if($cuid){
			$cuid = (int)$cuid;
			$re = isset($re[$cuid]) ? $re[$cuid] : array();
			if($Key){
				$re = isset($re[$Key]) ? $re[$Key] : '';
			}
		}
		return $re;
    }
	
	# ���»���
	public static function UpdateCache(){
		$CacheArray = cls_commu::InitialInfoArray();
		foreach($CacheArray as &$r){
			unset($r['vieworder'],$r['content'],$r['cfgs0']);
			cls_CacheFile::ArrayAction($r,'cfgs','extract');
		}
		cls_CacheFile::Save($CacheArray,cls_commu::CacheName());
	}
	# ��̬���������飬ֱ�����Գ�ʼ����Դ(�����ݿ�/����)�����ں�̨����ʵʱ�ǻ������
	public static function InitialInfoArray(){
		$re = array();
		$db = _08_factory::getDBO();
		$db->select('*')->from(cls_commu::Table())->order('vieworder,cuid')->exec();
		while($r = $db->fetch()){
			cls_CacheFile::ArrayAction($r,'cfgs','varexport');
			$re[$r['cuid']] = $r;
		}
		return $re;
	}
	
	# ��̬�ĵ������ϣ�ֱ���Գ�ʼ����Դ(�����ݿ�/����)�����ں�̨����ʵʱ�ǻ������
	public static function InitialOneInfo($id){
		if(!($id = (int)$id)) return array();
		$db = _08_factory::getDBO();
		$re = $db->select('*')->from(cls_commu::Table())->where(array('cuid' => $id))->exec()->fetch();
		cls_CacheFile::ArrayAction($re,'cfgs','varexport');
		return $re ? $re : array();
	}
	
	
}
