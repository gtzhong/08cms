<?php
/* 
** ����ģ�͵ķ�������
** ע�⣺Ϊ���ڻ�����ʹ����չ�ľ�̬�������ڻ�����ʹ�ã���չ��::method�����ʹ�ã�self::method������֧����չ��
*/
!defined('M_COM') && exit('No Permission');
class cls_mchannelbase{
	
	public static function Table($NoPre = false){
		return ($NoPre ? '' : '#__').'mchannels';
	}
	
	# ��ȡ���ã�ͨ���Ի���ķ�ʽ����ȡ
	# �����ȡ��ȫ���������飬ָ��ID�����ã�ָ��ID��KEY������
    public static function Config($ID = 0,$Key = ''){
		$re = cls_cache::Read(cls_mchannel::CacheName());
		if($ID){
			$ID = cls_mchannel::InitID($ID);
			$re = isset($re[$ID]) ? $re[$ID] : array();
			if($Key){
				$re = isset($re[$Key]) ? $re[$Key] : '';
			}
		}
		return $re;
    }
	
	# ��������
    public static function CacheName(){
		return 'mchannels';
    }
	
	# ��ID���г�ʼ��ʽ��
    public static function InitID($ID = 0){
		return max(0,intval($ID));
	}

	# ���� ID=>���� ���б�����
	public static function mchidsarr($noViewID = 0){
		$mchannels = cls_cache::Read('mchannels');
		$narr = array();
		foreach($mchannels as $k => $v){
			if(!$noViewID) $v['cname'] .= "($k)";
			$narr[$k] = $v['cname'];
		}
		return $narr;
	}
	
	# ���»���
	public static function UpdateCache(){
		$CacheArray = cls_mchannel::InitialInfoArray();
		foreach($CacheArray as &$r){
			unset($r['cfgs0'],$r['content']);
			cls_CacheFile::ArrayAction($r,'cfgs','extract');
		}
		cls_CacheFile::Save($CacheArray,cls_mchannel::CacheName());
	}
	
	# ��̬���������飬ֱ�����Գ�ʼ����Դ(�����ݿ�/����)�����ں�̨����ʵʱ�ǻ������
	public static function InitialInfoArray(){
		$re = array();
		$db = _08_factory::getDBO();
		$db->select('*')->from(cls_mchannel::Table())->order('mchid')->exec();
		while($r = $db->fetch()){
			cls_CacheFile::ArrayAction($r,'cfgs','varexport');
			$re[$r['mchid']] = $r;
		}
		return $re;
	}
	
	# ��̬�ĵ������ϣ�ֱ���Գ�ʼ����Դ(�����ݿ�/����)�����ں�̨����ʵʱ�ǻ������
	public static function InitialOneInfo($id){
		if(!($id = (int)$id)) return array();
		$db = _08_factory::getDBO();
		$re = $db->select('*')->from(cls_mchannel::Table())->where(array('mchid' => $id))->exec()->fetch();
		return $re ? $re : array();
	}
	
	# �����̨�����չ���˵�����ʾ
	public static function BackMenuCode(){
		$linknodes = cls_cache::Read('linknodes');
		$na = array();
		if($a_vmchids = empty($linknodes['mnodes']) ? array() : array_keys($linknodes['mnodes'])){
			$mchidsarr = array(0 => 'ȫ����Ա') + cls_mchannel::mchidsarr(1);
			foreach($mchidsarr as $k => $v){
				if(in_array($k,$a_vmchids)) $na[$k] = array('title' => $v,'level' => 0,'active' => 1,);
			}
		}
		return ViewBackMenu($na,2);
	}
	
	# �����̨����൥������Ĺ���ڵ�չʾ(ajax����)
	public static function BackMenuBlock($mchid = 0){
		$UrlsArray = cls_mchannel::BackMenuBlockUrls($mchid);
		return _08_M_Ajax_Block_Base::getInstance()->OneBackMenuBlock($UrlsArray);
	}
	
	# �����̨����൥������Ĺ���ڵ�url���飬���Ը�����Ҫ��Ӧ��ϵͳ������չ
	protected static function BackMenuBlockUrls($mchid = 0){
		$UrlsArray = array();
		$mchid = max(0,intval($mchid));
		$aurls = cls_cache::Read('aurls');
		$linknodes = cls_cache::Read('linknodes');
		if(!empty($linknodes['mnodes'][$mchid])){
			$suffix = $mchid ? "&mchid=$mchid" : '';
			$auidsarr = explode(',',$linknodes['mnodes'][$mchid]);
			foreach($auidsarr as $k){
				if(!empty($aurls[$k])){
					$UrlsArray[$aurls[$k]['name']] = $aurls[$k]['link'].$suffix;
				}
			}
		}
		return $UrlsArray;
	}
	
	
	
}
