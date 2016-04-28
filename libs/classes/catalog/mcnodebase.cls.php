<?php
/**
* ��ԱƵ���ڵ�Ĵ�����
* ����Ϊcls_mcnode�Ļ���
** ע�⣺Ϊ���ڻ�����ʹ����չ�ľ�̬�������ڻ�����ʹ�ã���չ��::method�����ʹ�ã�self::method������֧����չ��
*/
defined('M_COM') || exit('No Permission');
class cls_mcnodebase{
	
	# ָ���ڵ��ĳ������ҳ��url
	public static function url($cnstr,$addno = 0){
		$Node = cls_node::mcnodearr($cnstr);
		$addno = (int)$addno;
		$urlvar = 'mcnurl'.($addno ? $addno : '');
		return isset($Node[$urlvar]) ? $Node[$urlvar] : '#';
	}
	
	# ���»���
	public static function UpdateCache(){
		$CacheArray = cls_mcnode::CacheArray();
		cls_CacheFile::Save($CacheArray,cls_mcnode::CacheName());
	}
	
	# �����ԱƵ����ҳ��̬
	public static function UnStaticIndex(){
		m_unlink(cls_node::mcn_format('',0));
		return '��̬����ɹ�';
	}
	
	public static function BlankStaticUrl($cnstr,$force=0){//force:ǿ�и��ǵ�һ���ļ���Ϊ0ʱΪ�޸�����
		$memberdir = cls_env::mconfig('memberdir');
		if(!($enablestatic = cls_env::mconfig('enablestatic'))) return;
		if(!$cnstr || !($cnode = cls_node::read_mcnode($cnstr))) return;
		$statics = empty($cnode['statics']) ? array() : explode(',',$cnode['statics']);
		for($i = 0;$i <= @$cnode['addnum'];$i++){
			if(empty($statics[$i]) ? $enablestatic : 0){
				$cnfile = M_ROOT.cls_url::m_parseurl(cls_node::mcn_format($cnstr,$i),array('page' => 1));
				if($force || !is_file($cnfile)) @str2file(_08_HTML::DirectUrl(cls_env::mconfig('memberdir')."/index.php?$cnstr".($i ? "&&addno=$i" : '')),$cnfile);
			}
		}
	}
	
	
	# �����ݿ������ɻ�������Ҫ������
	public static function CacheArray(){
		$CacheArray = array();
		$db = _08_factory::getDBO();
		$db->select('ename,alias,appurl,tid')->from(cls_mcnode::Table())->where(array('closed' => 0))->exec();
		while($r = $db->fetch()){
			$cnstr = $r['ename'];
			unset($r['ename']);
			foreach(array('alias','appurl','tid',) as $k) if(!$r[$k]) unset($r[$k]);
			$CacheArray[$cnstr] = $r;
		}
		return $CacheArray;
	}
	
	# ȡ���������ݱ���
	public static function Table($NoPre = false){
		return ($NoPre ? '' : '#__').'mcnodes';
	}
	# ��������
    public static function CacheName(){
		return 'mcnodes';
    }
		
}
