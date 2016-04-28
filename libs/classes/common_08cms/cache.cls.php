<?php
/**
 * �����ȡ����������
 *
 * @author    Wilson
 * @copyright Copyright (C) 2008 - 2013 08CMS, Inc. All rights reserved.
 */

class cls_cache
{ 

	public static $_08CACHE = array();//����ȡ���Ļ����ݴ棬����

    /**
     * ��ȡͨ�üܹ����棬��$CacheName��ͬ���ֱ𴢴���dynamic/cache/(�ܹ�)��template/xxx/config/(ģ��)
     * �����ȡ���������е�һ����֧
     * 
     * @param  string $CacheName 		����������channelsʱ��ȡ�������棬��channel���ȡ��֧
     * @param  string $BigClass			�������࣬���������
     * @param  string $SmallClass		����С���࣬���������
     * @param  int $noExCache			��������չ����
     * @param  bool   $config_dir       �Ƿ����ģ���ļ��еı�ǩ�໺�棬TRUEΪ�ǣ�FALSEΪ����
     * 
     * @return array					���ػ�ȡ����ϵͳ����
     * @since  1.0
     */ 
   
	public static function Read($CacheName,$BigClass = '',$SmallClass = '',$noExCache = 0, $config_dir = false){
		if($BigClass && $SmallClass){
			//�������棬������ȡ����(�ĵ�ģ��)�ֶΣ�Read('field','ģ��id','�ֶ���')
			$CacheName .= substr($CacheName,-1) == 's' ? 'es' : 's';
			$CacheArray = self::Read($CacheName,$BigClass,'',$noExCache);
			return empty($CacheArray[$SmallClass]) ? array() : $CacheArray[$SmallClass];
		}elseif($BigClass && self::_AllowSmallName($CacheName)){
			$CacheArray = self::Read($CacheName.'s','','',$noExCache);
			return empty($CacheArray[$BigClass]) ? array() : $CacheArray[$BigClass];
		}else{
			$m_excache = cls_excache::OneInstance();
			$noExCache = $m_excache ? $noExCache : 1;
			$Key = self::CacheKey($CacheName,$BigClass,$SmallClass);
			if(!isset(self::$_08CACHE[$Key])){
				if($noExCache || !(self::$_08CACHE[$Key] = $m_excache->get($Key))){
					@include self::CacheDir($CacheName, $config_dir)."$Key.cac.php";
					self::$_08CACHE[$Key] = empty($$Key) ? array() : $$Key;
					if(self::$_08CACHE[$Key] && !$noExCache) $m_excache->set($Key,self::$_08CACHE[$Key]);
				}
			}
			return self::$_08CACHE[$Key];
		}
	}
	
    /**
     * ��ȫ�ֱ�����ʽ����ͨ�üܹ�����
     * 
     * @param  string $Keys 	����������������Զ��ŷָ�
     * 
     * @since  1.0
     */ 
	public static function Load($Keys = ''){
		//ȫ��������չ����
		if(empty($Keys)) return;
		$Keys = array_filter(explode(',',$Keys));
		$m_excache = cls_excache::OneInstance();
		foreach($Keys as $Key){
			_08_FilesystemFile::filterFileParam($Key);
			if($Key = trim($Key)){
				global $$Key;
				if(!isset(self::$_08CACHE[$Key])){
					if(!$m_excache->enable || !($$Key = $m_excache->get($Key))){
						@include self::CacheDir($Key)."$Key.cac.php";
						if($m_excache->enable && !empty($$key)) $m_excache->set($Key,$$Key);
					}
					self::$_08CACHE[$Key] = empty($$Key) ? array() : $$Key;
				}
				$$Key = self::$_08CACHE[$Key];
			}
		}
		return;
	}
	
    /**
     * �ڵ�ǰ�����У���;���¿����û���$_08CACHE��֮�����Ϊ���º�Ļ��棬��ΪNULL����Ϊɾ������
     * 
     * @param  string $CacheKey 		������
     * @param  $CacheValue				����ֵ
     * 
     * @since  1.0
     */ 
	public static function SetNow($CacheKey,$CacheValue = NULL){
		if(isset(self::$_08CACHE[$CacheKey])){
			if(is_null($CacheValue)){
				unset(self::$_08CACHE[$CacheKey]);
			}else{
				self::$_08CACHE[$CacheKey] = $CacheValue;
			}
		}
	}
	
	
    /**
     * ��ȡ����ģ���ʶ���棬������setting�е����ý��кϲ�
     * 
     * @param  string $TagType 	��ʶ���ͣ���ctag(���ϱ�ʶ)��rtag(�����ʶ)
     * @param  string $TagName 	��ʶ����
     * 
     * @since  1.0
     */ 
	public static function ReadTag($TagType,$TagName){
		$TagAarray = self::Read($TagType,$TagName);
		$TagAarray && $TagAarray = array_merge($TagAarray,$TagAarray['setting']);
		unset($TagAarray['setting']);
		return $TagAarray;
	}
	
    /**
     * ����ͨ�üܹ�����ļ�ֵ(�����ļ���)
     * 
     * @param  string $CacheName 		����������channelsʱ��ȡ�������棬��channel���ȡ��֧
     * @param  string $BigClass			�������࣬���������
     * @param  string $SmallClass		����С���࣬���������
     * 
     * @return string					���ػ����ֵ(�����ļ���)
     * @since  1.0
     */ 
	public static function CacheKey($CacheName,$BigClass = '',$SmallClass = ''){
		$Key = $CacheName.$BigClass.$SmallClass;
		_08_FilesystemFile::filterFileParam($Key);
		return $Key;
	}
	
	
    /**
     * �õ�ͨ�üܹ��������������·��
     * 
     * @param  string $CacheName 		����������channelsʱ��ȡ�������棬��channel���ȡ��֧
     * @param  bool   $config_dir       �Ƿ����ģ���ļ��еı�ǩ�໺�棬TRUEΪ�ǣ�FALSEΪ����
     * 
     * @return string					������������·��
     * @since  1.0
     */ 
	public static function CacheDir( $CacheName, $config_dir = false ){
		$_template_config = array(//����ģ���ļ��е������໺��
		'cnodes','mcnodes','o_cnodes','mtpls','sptpls','jstpls','csstpls','tagclasses',
		'cntpls','mcntpls','cnconfigs','tplcfgs','arc_tpl_cfgs','arc_tpls','ca_tpl_cfgs','tpl_mconfigs','tpl_fields',
		'o_cntpls','o_cnconfigs','o_tplcfgs','o_mtpls','o_arc_tpl_cfgs','o_arc_tpls','o_ca_tpl_cfgs','o_sptpls',
		'fchannels','ffields','fcatalogs','pushtypes','pushareas','pafields','freeinfos','mtconfigs','mcatalogs',
		'_pushareas','_ffields','_pafields', # ��ȫ����Դ��Ӧ�û������
		);
		$_template_tag = array(//����ģ���ļ��еı�ǩ�໺��
		'advtag', 'advtags', 'ctag','ctags','rtag','rtags',
		);
		if(in_array($CacheName,$_template_config) || $config_dir){
			return cls_tpl::TemplateTypeDir('config');
		}elseif(in_array($CacheName,$_template_tag)){
			return cls_tpl::TemplateTypeDir('tag');
		}else{
			return _08_CACHE_PATH . 'cache'.DS;
		}
	}
	
    /**
     * �Ƿ�����ʹ�� read('xxx',key) ����ȡ����� $xxxs[key] ��֧
     * 
     */ 
	private static function _AllowSmallName($CacheName){
		$AllowArray = array('channel','catalog','fchannel','fcatalog','player','gmodel','gmission','aurl','commu','abrel','cnrel','mchannel','mctype','pusharea',);
		return in_array($CacheName,$AllowArray) ? true : false;
	}

    /**
     * ��ȡ����������
     * 
     * @param  string $class_name ��������
     * @param  string $ext        ��չ�����׺
     * @param  string $cache_dir  ����·��
     * 
     * @return array $vars ���ػ�ȡ����ϵͳ����
     * @since  1.0
     */ 
    public static function getCacheClassVar($class_name, $ext = '_son', $cache_dir = '')
    {
        $vars = array();
        # �������չ���������ȡ��չ������
        if(class_exists($class_name . $ext))
        {
            $vars = get_class_vars($class_name . $ext);            
        }
        # û����չ������ֱ�Ӷ�ȡ�˻�����
        else if(class_exists($class_name))
        {
            $vars = get_class_vars($class_name);   
        }
        # ������治�������װʱ����ԭʼ�ĵ��÷�ʽ��ȡ
        else
        {
            $vars = self::cacRead($class_name, $cache_dir);
        }
        return $vars;
    }
	
	/**
	 * ���ȶ�ȡ��չϵͳ�еĿ������û���
	 * ��չϵͳ�Ŀ������û���Ŀ¼��extend_sample/dynamic/syscache/��ͨ�ú��ĵĿ������û���Ŀ¼��dynamic/syscache/
	 *
	 * @param  string $cname  ��������
	 * @param  bool   $noex   ����ȡ��չ���棨��$m_excache����1Ϊ����ȡ��0Ϊ��ȡ��
	 * @return array  $re     ���ض�Ӧ�Ļ���
	 */
	public static function exRead($cname,$noex = 0){
		if(!$re = self::cacRead($cname,_08_EXTEND_SYSCACHE_PATH,$noex)) $re = self::cacRead($cname,'',$noex);
		return $re;
	}
    
    /**
     * ��ָ��·�����������ķ�ʽ��ȡ����
     * ͨ������ϵͳ���õĿ������û��棬�����ͨ�üܹ����棬��ʹ��Read����
     * 
     * @param  string $cname  ��������
     * @param  string $cacdir ����·��
     * @param  bool   $noex   ����ȡ��չ���棬1Ϊ����ȡ��0Ϊ��ȡ
     * @return array  $$cname ���ػ�����������
     * 
     * @static
     * @since  1.0
     */ 
    public static function cacRead($cname,$cacdir='',$noex = 0){
		$m_excache = cls_excache::OneInstance();
		$noex = $m_excache->enable ? $noex : 1;
    	$cacdir || $cacdir = _08_SYSCACHE_PATH;
		_08_FilesystemFile::filterFileParam($cname);
		if(!($cname = trim($cname))) return array();
        # ���Ļ����ļ�
		if(!in_array(substr($cacdir,-1),array('/',DS))) $cacdir .= DS;
    	if($noex){
    		@include $cacdir.$cname.'.cac.php';
    		empty($$cname) && $$cname = array();
    	}else{ # ��չ�����ļ�
    		$key = $cname.substr(md5($cacdir),6,10);
    		if(!($$cname = $m_excache->get($key))){
				$$cname = self::cacRead($cname,$cacdir,1);
    			$$cname && $m_excache->set($key,$$cname);
    		}
    	}
    	return $$cname;
    }
    /**
     * ǿ�ƴӻ����ļ���������ͨ�üܹ����棬��ʱ�����Լ��ݾɰ汾
     * 
     * @param  string $CacheName 		����������channelsʱ��ȡ�������棬��channel���ȡ��֧
     * @param  string $BigClass			�������࣬���������
     * @param  string $SmallClass		����С���࣬���������
     * 
     * @since  1.0
     */ 
	public static function ReLoad($CacheName,$BigClass = '',$SmallClass = ''){
		return self::Read($CacheName,$BigClass,$SmallClass,1);
	}
	/**
	 * ��ȡdynamic/htmlcac�еĻ�����·������������ھʹ���Ŀ¼
	 *
	 * @param  string   $mode   ����
	 * @param  string   $spath  ��·��
	 * @return string   $cacdir ·��
	 */
	public static function HtmlcacDir($mode='arc',$spath=''){
		_08_FilesystemFile::filterFileParam($mode);
		_08_FilesystemFile::filterFileParam($spath);
		$cacdir = _08_CACHE_PATH."htmlcac/$mode/";
		if($spath) $cacdir .= $spath.'/';
		is_dir($cacdir) || mmkdir($cacdir);
		return $cacdir;
	}
	
    /**
     * ��ȡexconfigs����
     * 
     * @param  string $key ������; eg: usedcar, qiuzu ��; Ϊ�շ�������
     * @param  string $sub �ӽ�; eg: m1:0,m3:g4,m0:else -=> mchid=1,ȡ0�±�, mchid=3ȡgrouptype4�±�, ������Աȡelse�±�(�����);  Ϊ�շ��������ӽ�
     * @return array  $re ���ػ�����������
     */ 
    public static function exConfig($key,$sub=0){ //,$ex=array()
		$exconfigs = cls_cache::cacRead('exconfigs',_08_EXTEND_SYSCACHE_PATH);
		if(empty($key)) return $exconfigs;
		if(!isset($exconfigs[$key])) return array();
		$tmp = $exconfigs[$key];
		if(strpos($sub,':')){
			$curuser = cls_UserMain::CurUser();	
			$re = array();
			$a0 = explode(',',$sub);
			foreach($a0 as $v){
				$b0 = explode(':',$v);
				$mchid = str_replace('m','',$b0[0]);
				if(empty($mchid) && isset($tmp[$b0[1]])){ 
					$re = $tmp[$b0[1]];
				}elseif($mchid==$curuser->info['mchid']){
					$k = strstr($b0[1],'g') ? $curuser->info[str_replace('g','grouptype',$b0[1])] : $k = $b0[1];
					$re = $tmp[$k];
					break;
				}
			}
		}elseif(isset($tmp[$sub])){  
			$re = $tmp[$sub];
		}else{ 
			$re = $tmp;	
		}
		//if($ex['xxxx']=='xxxx'){ } //��չ
		return $re;
    }
    
}