<?php
/**
 * �����ļ�д�룬���£�ɾ������ز���
 *
 */

class cls_CacheFile{ 

    /**
     * ���������鱣�浽ͨ�û����ļ�����չ���棬ͬʱ�������û���
     * 
     * @param array  $carr		 Ҫ���������
     * @param string $cname		 Ҫ����Ļ��������뻺���ļ�����
     * @param string $ctype		 �������ͣ����������Զ�ȷ������·����
     * @param bool   $noex   	 �Ƿ������չ���棬1Ϊ�����룬0Ϊ����
     * @param bool   $config_dir �Ƿ����ģ���ļ��еı�ǩ�໺�棬TRUEΪ�ǣ�FALSEΪ����
     * 
     * @static
     * @since 1.0
     */ 
    public static function Save($carr,$cname,$ctype='',$noex = 0, $config_dir = false){
		if(!is_array($carr) || empty($cname)){
			cls_message::show('�������ƻ����ݴ���,����ʧ��');
		}
		_08_FilesystemFile::filterFileParam($cname);
		$cacfile = cls_cache::CacheDir($ctype ? $ctype : $cname, $config_dir)."$cname.cac.php";
		$cacstr = "<?php\ndefined('M_COM') || exit('No Permission');\n\$$cname = ".var_export($carr,TRUE)." ;";
		
		if(false === str2file($cacstr,$cacfile)){
			cls_message::show("�����޷�д��".str_replace(M_ROOT,'M_ROOT',$cacfile));
		}
		
		cls_cache::SetNow($cname,$carr); # ��ǰ���̵ĸ���
		$m_excache = cls_excache::OneInstance();
		
		if($m_excache->enable && !$noex){
			$m_excache->set($cname,$carr);
		}
		return;
	}

    /**
     * ɾ��ĳ��ͨ�û����Ӧ�Ļ����ļ�
     * 
     * @param  string $CacheName 		����������channelsʱ��ȡ�������棬��channel���ȡ��֧
     * @param  string $BigClass			�������࣬���������
     * 
     * @static
     * @since 1.0
     */ 
	
	public static function Del($CacheName='',$BigClass=''){
		$Key = cls_cache::CacheKey($CacheName,$BigClass);
		$Dir = cls_cache::CacheDir($CacheName);
		if(is_file($file = $Dir."$Key.cac.php")){
		    $_file = _08_FilesystemFile::getInstance();
			$_file->delFile($file);
		}
		
		cls_cache::SetNow($Key); # ��ǰ���̵����
		$m_excache = cls_excache::OneInstance();
		if($m_excache->enable) $m_excache->rm($Key);
	}
	

    /**
     * �����ݱ��浽���棨ϵͳ���棩�ļ��У���ͨ�û���Ĵ���
     * 
     * @param array  $carr   Ҫ���������
     * @param string $cname  Ҫ����Ļ��������뻺���ļ�����
     * @param string $cacdir ����Ŀ¼
     * 
     * @static
     * @since 1.0
     */ 
    public static function cacSave($carr,$cname,$cacdir=''){
		if(!is_array($carr) || empty($cname)) cls_message::show('�������ƻ����ݴ���,����ʧ��');
    	$cacdir || $cacdir = _08_SYSCACHE_PATH;
		if(!in_array(substr($cacdir,-1),array('/',DS))) $cacdir .= DS;
    	$cacstr = var_export($carr,TRUE);
		_08_FilesystemFile::filterFileParam($cname);
		if(false === str2file("<?php\ndefined('M_COM') || exit('No Permission');\n\$$cname = $cacstr ;","$cacdir$cname.cac.php")){
			cls_message::show("�����޷�д�� $cacdir$cname.cac.php");
		}
		$m_excache = cls_excache::OneInstance();
		if($m_excache->enable) $m_excache->set($cname.substr(md5($cacdir),6,10),$carr);
    }
	
    /**
     * �������£��ؽ���ϵͳͨ�üܹ��Ի���
     * 
     * @param string $except  ��Ҫ�ų��Ļ��������ų�����Զ��ŷָ�
     * 
     * @static
     * @since 1.0
     */ 
	public static function ReBuild($except = ''){
		$excepts = $except ? explode(',',$except) : array();
		$cacarr = array(
		'mconfigs','channels','fchannels','mchannels','players','fcatalogs','currencys','grouptypes','cotypes','rprojects','permissions',
		'crprojects','mtconfigs','amconfigs','commus','sitemaps','localfiles','crprices','vcatalogs','badwords','wordlinks','dbsources','splangs',
		'menus','mmenus','uprojects','freeinfos','usualurls','dbfields','mcatalogs','faces','domains','mcnodes','aurls','cnrels','abrels',
		'linknodes','watermarks','mctypes','catalogs','cnodes','gmodels','gmissions','splitbls','mspacepaths','frcatalogs','fragments',
		'bannedips','pagecaches','pushtypes','pushareas','o_cnodes'
		);
		foreach($cacarr as $k){
			if(!in_array($k,$excepts)){
				if($k == 'catalogs') cls_catalog::DbTrueOrder(0);
				self::Update($k);
			}
		}
		if(!in_array('fields',$excepts)){
			$vars = array_keys(cls_channel::Config());
			foreach($vars as $k) self::Update('fields',$k);
		}
		if(!in_array('mfields',$excepts)){
			$vars = array_keys(cls_cache::Read('mchannels'));
			$vars[] = 0;
			foreach($vars as $k) self::Update('mfields',$k);
		}
		if(!in_array('ffields',$excepts)){
			$vars = array_keys(cls_cache::Read('fchannels'));
			foreach($vars as $k) self::Update('ffields',$k);
		}
		if(!in_array('cufields',$excepts)){
			$vars = array_keys(cls_commu::Config());
			foreach($vars as $k) self::Update('cufields',$k);
		}
		if(!in_array('cnfields',$excepts)){
			$vars = array_keys(cls_cache::Read('cotypes'));//��ϵ�ֶ�
			$vars[] = 0;//��Ŀ�ֶ�
			foreach($vars as $k) self::Update('cnfields',$k);
		}
		if(!in_array('pafields',$excepts)){
			$vars = array_keys(cls_PushArea::Config());
			foreach($vars as $k) self::Update('pafields',$k);
		}
		if(!in_array('usergroups',$excepts)){
			$vars = array_keys(cls_cache::Read('grouptypes'));
			foreach($vars as $k) self::Update('usergroups',$k);
		}
		if(!in_array('coclasses',$excepts)){
			$vars = array_keys(cls_cache::Read('cotypes'));
			foreach($vars as $k){
				cls_catalog::DbTrueOrder($k);
				self::Update('coclasses',$k);
			}
		}
		//�����Զ�����·������
		@unlink(_08_USERCACHE_PATH.'autoload_pathmap.php'); 
		@unlink(_08_USERCACHE_PATH.'autoload_filemap.php'); 
	}	
    /**
     * ���ɻ����ָ����ϵͳ�ܹ�����
     * 
     * @param string $CacheName  		������
     * @param  string $BigClass			�������࣬���������
     * 
     * @static
     * @since 1.0
     */ 
	
	public static function Update($CacheName,$BigClass = ''){
        $db = _08_factory::getDBO();
        $tblprefix = cls_envBase::getBaseIncConfigs('tblprefix');
		$do = cls_cache::exRead('cachedos',1);
		switch($CacheName){
			case 'fields'://�ĵ��ֶ�
			case 'mfields'://��Ա�ֶ�
			case 'ffields'://�����ֶ�
			case 'cnfields'://��Ŀ�ֶ�
			case 'cufields'://�����ֶ�
			case 'pafields'://�����ֶ�
				$_Names = array('fields' => 'channel','mfields' => 'mchannel','cufields' => 'commu','ffields' => 'fchannel','pafields' => 'pusharea',);
				if($CacheName == 'cnfields'){
					$SourceType = $BigClass ? 'cotype' : 'catalog';
				}else $SourceType = $_Names[$CacheName];
				cls_FieldConfig::UpdateCache($SourceType,$BigClass);
				break;
			case 'channels':
			case 'fchannels':
			case 'cotypes':
			case 'mchannels':
			case 'commus':
			case 'pushareas':
			case 'pushtypes':
			case 'fcatalogs':
			case 'freeinfos':
			case 'mcatalogs':
			case 'mtconfigs':
			case 'mcnodes':
				$ClassName = 'cls_'.substr($CacheName,0,-1);
				call_user_func_array(array($ClassName,'UpdateCache'),array());
				break;
			case 'catalogs':
			case 'coclasses':
				cls_catalog::UpdateCache($BigClass);
				break;
			case 'cnodes':
			case 'o_cnodes':
				cls_cnode::UpdateCache($CacheName == 'cnodes' ? false : true);
				break;
			case 'abrels':
			case 'aurls':
			case 'cnrels':
			case 'mctypes':
			case 'fragments':
			case 'currencys':
			case 'dbsources':
			case 'players':
			case 'gmodels':
			case 'gmissions':
			case 'grouptypes':
			case 'rprojects':
			case 'watermarks':
			case 'uprojects':
			case 'permissions':
			case 'crprojects':
			case 'amconfigs':
			case 'sitemaps':
			case 'vcatalogs':
			case 'usualurls':
			case 'splitbls':
				$$CacheName = cls_DbOther::CacheArray($do[$CacheName]);
				self::Save($$CacheName,$CacheName);
				break;
			case 'frcatalogs':
				$$CacheName = cls_DbOther::CacheArray($do[$CacheName]);
				foreach($frcatalogs as $k => $v) $frcatalogs[$k] = $v['title'];
				self::Save($$CacheName,$CacheName);
				break;
			case 'mspacepaths':
				$mspacepaths = array();
				$na = cls_DbOther::CacheArray($do[$CacheName]);
				foreach($na as $v) $mspacepaths[$v['mid']] = $v['mspacepath'];
				self::Save($$CacheName,$CacheName);
				break;
			case 'bannedips':
				global $timestamp;
				$bannedips = array();
				$do[$CacheName]['where'] = "enddate=0 || enddate>'$timestamp'";
				$na = cls_DbOther::CacheArray($do[$CacheName]);
				foreach($na as $v){
					$str = '';
					for($i = 1;$i < 5;$i ++){
						$str .= ($str ? '\.' : '').($v["ip$i"] == -1 ? '\d+' : $v["ip$i"]);
					}
					$bannedips[] = $str;
				}
				self::Save($$CacheName,$CacheName);
				break;
			case 'usergroups':
				$do[$CacheName]['where'] = "gtid='$BigClass'";
				$$CacheName = cls_DbOther::CacheArray($do[$CacheName]);
				self::Save($$CacheName,$CacheName.$BigClass,$CacheName);
				break;
			case 'splangs':
				$$CacheName = cls_DbOther::CacheArray($do[$CacheName]);
				foreach($splangs as $k => $v) $splangs[$k] = $v['content'];
				self::Save($$CacheName,$CacheName);
				break;
			case 'mconfigs':
				
				$mconfigs = cls_DbOther::CacheArray($do[$CacheName]);
				$btags = array();
				foreach($mconfigs as $k => $v){
					$mconfigs[$k] = $v['value'];
					if(in_array($k,array('hosturl','cmsurl','enablestatic','virtualurl','templatedir','templatebase',))){//��Ժ������������⴦��
						if(cls_env::GetG($k)) $mconfigs[$k] = cls_env::GetG($k);
					}elseif(in_array($k,array('cn_urls','cn_periods',))){
						$mconfigs[$k] = explode(',',$mconfigs[$k]);
					}
					if(in_array($k,array('hostname','hosturl','cmsname','cmsurl',))){
						$btags[$k] = $mconfigs[$k];
					}
				}
				
				cls_env::SetG('templatedir',$mconfigs['templatedir']);
				$tpl_mconfigs = cls_cache::Read('tpl_mconfigs');
				$tpl_fields = cls_cache::Read('tpl_fields');
				$tplvars = array('cmslogo','cmstitle','cmskeyword','cmsdescription','cms_icpno','bazscert','copyright','cms_statcode',);
				foreach($tpl_fields as $k => $v) $tplvars[] = "user_$k";
				foreach($tpl_mconfigs as $k => $v){
					if(in_array($k,$tplvars)) $btags[$k] = $v;
				}
				
				$mconfigs['cms_abs'] = $btags['cms_abs'] = strpos($mconfigs['cmsurl'],$mconfigs['hosturl']) === FALSE ? ($mconfigs['hosturl'].$mconfigs['cmsurl']) : $mconfigs['cmsurl'];
				$mconfigs['cms_rel'] = $btags['cms_rel'] = strpos($mconfigs['cmsurl'],$mconfigs['hosturl']) === FALSE ? $mconfigs['cmsurl'] : str_replace($mconfigs['hosturl'],'',$mconfigs['cmsurl']);
				foreach(array('cms_abs','cmsurl',) as $k) cls_env::SetG($k,$mconfigs[$k]);//����������ֵ��ʱ��Ч���Ա����������˳������
				
				$mconfigs['cms_top'] = $btags['cms_top'] = cls_env::TopDomain($mconfigs['hosturl']);
				foreach(array('member','mspace','mobile',) as $k) $mconfigs[$k.'url'] = $btags[$k.'url'] = cls_url::view_url($mconfigs[$k.'dir'].'/');
	
				//����ֵ����base.inc.php�е�����
				foreach(array('mcharset','cms_version',) as $k) $btags[$k] = cls_env::GetG($k);
				
				$btags['tplurl'] = $mconfigs['cms_abs'].'template/'.$mconfigs['templatedir'].'/';
				foreach(array('btags','mconfigs') as $k) self::Save($$k,$k);
				break;
			case 'localfiles':
                $localfiles = array();
				$inits = cls_DbOther::CacheArray($do[$CacheName]);
				foreach($inits as $v) $localfiles[$v['ftype']][$v['extname']] = $v;
				self::Save($$CacheName,$CacheName);
				break;
			case 'crprices':
				$$CacheName = cls_DbOther::CacheArray($do[$CacheName]);
				$vcps = array('tax' => array(),'sale' => array(),'award' => array(),'ftax' => array(),'fsale' => array(),);
				foreach($crprices as $k => $v){
					foreach(array('tax','sale','award','ftax','fsale') as $var){
						$v[$var] && $vcps[$var][$v['ename']] = $v['CacheName'];
					}
				}
				self::Save($vcps,'vcps');
				self::Save($$CacheName,$CacheName);
				break;
			case 'badwords':
				$badwords = array();
				$query = $db->query("SELECT * FROM {$tblprefix}badwords ORDER BY bwid");
				while($badword = $db->fetch_array($query)){
					$badwords['wreplace'][] = $badword['wreplace'];
					$badword['wsearch'] = preg_replace("/\\\{(\d+)\\\}/", ".{0,\\1}", preg_quote($badword['wsearch'],'/'));
					$badwords['wsearch'][] = '/'.$badword['wsearch'].'/i';
				}
				self::Save($$CacheName,$CacheName);
				break;
			case 'wordlinks':
				$wordlinks = $uwordlinks = array();
				$query = $db->query("SELECT * FROM {$tblprefix}wordlinks WHERE available=1 ORDER BY pcs DESC");
				while($row = $db->fetch_array($query)){
					$wordlinks['swords'][] = '/'.preg_quote($row['sword'],'/').'/i';
					$wordlinks['rwords'][] = '<a href="'.cls_url::view_url($row['url']).'" class="p_wordlink" target="_blank">'.$row['sword'].'</a>';
					$uwordlinks['swords'][] = $row['sword'];
					$uwordlinks['rwords'][] = cls_url::view_url($row['url']);
				}
				self::Save($$CacheName,$CacheName);
				self::Save($uwordlinks,'uwordlinks');
				break;
			case 'domains':
				$domains = array('from' => array(),'to' => array(),);
				$query = $db->query("SELECT domain,folder,isreg FROM {$tblprefix}domains ORDER BY vieworder,id");
				while($row = $db->fetch_array($query)){
					$domains['from'][] = $row['isreg'] ? $row['folder'] : u_regcode($row['folder']);
					$domains['to'][] = $row['domain'];
				}
				self::Save($$CacheName,$CacheName);
				break;
			case 'faces':
				$faceicons = array('from' => array(),'to' => array(),);
				$jsstr = 'var FACEICONS = [';
				$query = $db->query("SELECT * FROM {$tblprefix}facetypes WHERE available=1 ORDER BY vieworder,ftid");
				while($row = $db->fetch_array($query)){
					$jsstr .= '[\''.$row['cname'].'\',[';
					$query1 = $db->query("SELECT * FROM {$tblprefix}faces WHERE ftid='$row[ftid]' AND available=1 ORDER BY vieworder,id");
					while($row1 = $db->fetch_array($query1)){
						$faceicons['from'][] = $row1['ename'];
						$faceicons['to'][] = 'images/face/'.$row['facedir'].'/'.$row1['url'];
						$jsstr .= '[\''.$row1['ename'].'\',\'images/face/'.$row['facedir'].'/'.$row1['url'].'\'],';
					}
					$jsstr .= ']],';
				}
				$jsstr .= '];';
				str2file($jsstr,M_ROOT.'dynamic/cache/faceicons.js');
				self::Save($faceicons,'faceicons',$CacheName);
				break;
			case 'dbfields':
				$dbfields = array();
				$query = $db->query("SELECT * FROM {$tblprefix}dbfields ORDER BY dfid");
				while($row = $db->fetch_array($query)){
					$dbfields[$row['ddtable'].'_'.$row['ddfield']] = $row['ddcomment'];
				}
				self::Save($$CacheName,$CacheName);
				break;
			case 'menus':
				$mnmenus = array();
				$query1 = $db->query("SELECT * FROM {$tblprefix}mtypes ORDER BY vieworder");
				while($row1 = $db->fetch_array($query1)){
					$mnmenus[$row1['mtid']] = array(
						'title' => $row1['title'],
						'childs'=> array()
					);
					$query2 = $db->query("SELECT * FROM {$tblprefix}menus WHERE mtid='$row1[mtid]' AND available=1 AND isbk=0 ORDER BY vieworder");
					while($row2 = $db->fetch_array($query2)){
						$mnmenus[$row1['mtid']]['childs'][$row2['mnid']] = array(
							'title' => $row2['title'],
							'url'=> $row2['url']
						);
					}
					if(empty($mnmenus[$row1['mtid']]['childs']))unset($mnmenus[$row1['mtid']]);
				}
				self::Save($mnmenus,'mnmenus');
				break;
			case 'mmenus':
				$mmnmenus = array();
				$query = $db->query("SELECT * FROM {$tblprefix}mmtypes ORDER BY vieworder,mtid");
				while($row0 = $db->fetch_array($query)){
					$mmnmenus[$row0['mtid']]['title'] = $row0['title'];
					$mmnmenus[$row0['mtid']]['menuimage'] = $row0['menuimage'];	
					$mmnmenus[$row0['mtid']]['submenu'] = array();
					$query1 = $db->query("SELECT * FROM {$tblprefix}mmenus WHERE mtid='$row0[mtid]' AND available=1 AND isbk='0' ORDER BY vieworder,mnid");
					while($row1 = $db->fetch_array($query1)){
						$mmnmenus[$row0['mtid']]['submenu'][$row1['mnid']] = array(
						'title' => $row1['title'],
						'url' => $row1['url'],
						'pmid' => $row1['pmid'],
						'newwin' => $row1['newwin'],
						'onclick' => $row1['onclick'],
						);
					}
				}
				self::Save($mmnmenus,'mmnmenus');
				break;
			case 'linknodes':
				$query = $db->query("SELECT * FROM {$tblprefix}variables WHERE type='$CacheName' ORDER BY variable ASC");
				${$CacheName} = array();
				while($row = $db->fetch_array($query)){
					${$CacheName}[$row['variable']] = is_array($row['content'] = @unserialize($row['content'])) ? $row['content'] : array();
				}
				self::Save($$CacheName,$CacheName);
				break;
			case 'pagecaches':
				$$CacheName = cls_DbOther::CacheArray($do[$CacheName]);
				$na = array();
				foreach($pagecaches as $k => $v){
					$typeid = $v['typeid'];$pcid = $v['pcid'];
					foreach(array('pcid','CacheName','typeid','cfgs','vieworder','available',) as $key) unset($v[$key]);
					$na[$typeid][$pcid] = $v;
				}
				for($i=1;$i<10;$i++) self::Save(empty($na[$i]) ? array() : $na[$i],"pagecaches$i");
				break;
		}
	}
	
    /**
     * ָ��������ĳ��������ֵ����ĳ���ض���������Ҫ�Ǽܹ����ݿ��������ʱ��һЩ���ò���
     *
     * @param  array	$array		Ҫ���������
     * @param  string	$key		ָ�������еļ�ֵ
     * @param  string	$action		��������
     * @return array				���ز����������
     *
     * @since  1.0
     */
    public static function ArrayAction( array &$array, $key, $action){
		if(empty($array) || !$key || !$action || !isset($array[$key])) return;
		switch($action){
			case 'unserialize':
				if(empty($array[$key]) || !is_array($array[$key] = @unserialize($array[$key]))){
					$array[$key] = array();
				}
				break;
			case 'explode':
				if($array[$key] !== ''){
					$array[$key] = array_filter(explode(',',$array[$key]));
				}else $array[$key] = array();
				break;
			case 'varexport':
				$array[$key] = varexp2arr($array[$key]);
				break;
			case 'extract':
				if(is_array($array[$key])){
					$array += $array[$key];
					unset($array[$key]);
				}
				break;
		}
    }
    
    
}