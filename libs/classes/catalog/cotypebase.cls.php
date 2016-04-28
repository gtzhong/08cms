<?php
/* 
** ��ϵ�ķ�������
** ע�⣺Ϊ���ڻ�����ʹ����չ�ľ�̬�������ڻ�����ʹ�ã���չ��::method�����ʹ�ã�self::method������֧����չ��
*/
!defined('M_COM') && exit('No Permission');
class cls_cotypebase{
	
	public static function Table($NoPre = false){
		return ($NoPre ? '' : '#__').'cotypes';
	}
	
	# ���� ID=>���� ���б�����
	public static function coidsarr($noSelf = 0,$ViewID = 0){
		$cotypes = cls_cache::Read('cotypes');
		$narr = array();
		foreach($cotypes as $k => $v){
			if($noSelf && !empty($v['self_reg'])) continue;
			if($ViewID) $v['cname'] .= "($k)";
			$narr[$k] = $v['cname'];
		}
		return $narr;
	}
	
	# ���»���
	public static function UpdateCache(){
		$CacheArray = cls_cotype::InitialInfoArray();
		foreach($CacheArray as &$r){
			unset($r['vieworder'],$r['remark']);
		}
		cls_CacheFile::Save($CacheArray,'cotypes');
	}
	
	# ��̬����ϵ�������飬ֱ�����Գ�ʼ����Դ(�����ݿ�/����)�����ں�̨����ʵʱ�ǻ������
	public static function InitialInfoArray(){
		$re = array();
		$db = _08_factory::getDBO();
		$db->select('*')->from(cls_cotype::Table())->order('vieworder,coid')->exec();
		while($r = $db->fetch()){
			$re[$r['coid']] = $r;
		}
		return $re;
	}
	
	# ��̬�ĵ�����ϵ���ϣ�ֱ���Գ�ʼ����Դ(�����ݿ�/����)�����ں�̨����ʵʱ�ǻ������
	public static function InitialOneInfo($coid){
		if(!($coid = (int)$coid)) return array();
		$db = _08_factory::getDBO();
		$re = $db->select('*')->from(cls_cotype::Table())->where(array('coid' => $coid))->exec()->fetch();
		return $re ? $re : array();
	}
	
	
	# �����̨�����չ���˵�����ʾ
	public static function BackMenuCode($coid = 0){
		if(!$coid){
			$curuser = cls_UserMain::CurUser();
			$a_caids = $curuser->aPermissions('caids');
			$linknodes = cls_cache::Read('linknodes');
			$catalogs = cls_cache::Read('catalogs');
				
			$na = array();
			$a_ucaids = empty($linknodes['anodes']) ? array() : array_keys($linknodes['anodes']);
			if(!in_array('-1',$a_caids)) $a_ucaids = array_intersect($a_ucaids,$a_caids);
			$a_vcaids = array();
			foreach($a_ucaids as $v){
				$a_vcaids = array_merge($a_vcaids,!$v ? array($v) : cls_catalog::Pccids($v,0,1));//������ʾ��Ŀ���ϼ���Ŀ��Ҫ��ʾ����
			}
			if($a_vcaids = array_unique($a_vcaids)){
				$catalogs = array(0 => array('title' => 'ȫ����Ŀ','level' => 0)) + $catalogs;
				
				foreach($catalogs as $k => $v){
					if(!in_array($k,$a_vcaids)) continue;
					$na[$k] = array('title' => $v['title'],'level' => $v['level'],'active' => in_array($k,$a_ucaids) ? 1 : 0,);
				}
			}
			return ViewBackMenu($na,0);
		}
	}
	
	# �����̨����൥������Ĺ���ڵ�չʾ(ajax����)
	public static function BackMenuBlock($coid = 0,$ccid = 0){
		$UrlsArray = cls_cotype::BackMenuBlockUrls($coid,$ccid);
		return _08_M_Ajax_Block_Base::getInstance()->OneBackMenuBlock($UrlsArray);
	}
	
	# �����̨����൥������Ĺ���ڵ�url���飬���Ը�����Ҫ��Ӧ��ϵͳ������չ
	protected static function BackMenuBlockUrls($coid = 0,$ccid = 0){
		$coid = max(0,intval($coid));
		$ccid = max(0,intval($ccid));
		$UrlsArray = array();
		if(!$coid){
			$aurls = cls_cache::Read('aurls');
			$linknodes = cls_cache::Read('linknodes');
			$auidstr = @$linknodes['anodes'][$ccid];
		}else{
			$cocsmenus = cls_cache::exRead('cocsmenus');
			$aurls = $cocsmenus[$coid]['aurls'];
			$auidstr = @$cocsmenus[$coid]['items'][$ccid];
		}
		if(!empty($auidstr)){
			$suffix = $ccid ? "&".($coid ? "ccid$coid" : 'caid')."=$ccid" : '';
			$auidsarr = explode(',',$auidstr);
			foreach($auidsarr as $k){
				if(!empty($aurls[$k])){
					$UrlsArray[$aurls[$k]['name']] = $aurls[$k]['link'].$suffix;
				}
			}
		}
		return $UrlsArray;
	}

	
}
