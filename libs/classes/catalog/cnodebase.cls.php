<?php
/**
* ��Ŀ�ڵ�Ĵ�����
* ����Ϊcls_cnode�Ļ���
* NodeMode���Ƿ��ֻ��ڵ�
** ע�⣺Ϊ���ڻ�����ʹ����չ�ľ�̬�������ڻ�����ʹ�ã���չ��::method�����ʹ�ã�self::method������֧����չ��
*/
defined('M_COM') || exit('No Permission');
class cls_cnodebase{
	
	# ���ݴ���Ĳ�����ȡ�ýڵ��ִ�
	public static function cnstr($Params = array()){
		#cnstr()��Ҫ�ų�������չ
		return cnstr($Params); 
	}
	
	
	# ָ���ڵ��ĳ������ҳ��url
	# NodeMode���Ƿ��ֻ��ڵ�
	public static function url($cnstr,$addno = 0,$NodeMode = 0){
		$Node = cls_node::cnodearr($cnstr,empty($NodeMode) ? 0 : 1);
		$addno = (int)$addno;
		$urlvar = 'indexurl'.($addno ? $addno : '');
		return isset($Node[$urlvar]) ? $Node[$urlvar] : '#';
	}
	
	# ���ϵͳ��ҳ��̬
	public static function UnStaticIndex(){
		$cnformat = idx_format();
		m_unlink($cnformat);
		return '��̬����ɹ�';
	}
	
	# �޸��ڵ㾲̬����
	public static function BlankStaticUrl($cnstr,$force=0){//force:ǿ�и��ǵ�һ���ļ���Ϊ0ʱΪ�޸�����
		$enablestatic = cls_env::mconfig('enablestatic');
		if($enablestatic && $cnstr){
			if(!$cnode = cls_node::read_cnode($cnstr)) return;
			$statics = empty($cnode['statics']) ? array() : explode(',',$cnode['statics']);
			for($i = 0;$i <= $cnode['addnum'];$i++){
				if(empty($statics[$i]) ? $enablestatic : 0){
					$cnfile = M_ROOT.cls_url::m_parseurl(cls_node::cn_format($cnstr,$i,$cnode),array('page' => 1));
					if($force || !is_file($cnfile)) @str2file(_08_HTML::DirectUrl("index.php?$cnstr".($i ? "&addno=$i" : '')),$cnfile);
				}
			}
		}
	}
		
	# ���»���
	public static function UpdateCache($NodeMode = 0){
		$CacheArray = cls_cnode::CacheArray($NodeMode);
		cls_CacheFile::Save($CacheArray,cls_cnode::CacheName($NodeMode));
	}
	
	# ȡ���������ݱ���
	public static function Table($NodeMode = 0,$NoPre = false){
		return ($NoPre ? '' : '#__').($NodeMode ? 'o_' : '').'cnodes';
	}
	
	# �����ݿ������ɻ�������Ҫ������
	public static function CacheArray($NodeMode = 0){
		$CacheArray = array();
		$db = _08_factory::getDBO();
		$db->select('ename,alias,appurl,tid')->from(cls_cnode::Table($NodeMode))->where(array('closed' => 0))->exec();
		while($r = $db->fetch()){
			$cnstr = $r['ename'];
			unset($r['ename']);
			foreach(array('alias','appurl','tid',) as $k) if(!$r[$k]) unset($r[$k]);
			$CacheArray[$cnstr] = $r;
		}
		return $CacheArray;
	}
	
	# ��������
    public static function CacheName($NodeMode = 0){
		return ($NodeMode ? 'o_' : '').'cnodes';
    }
	
}
