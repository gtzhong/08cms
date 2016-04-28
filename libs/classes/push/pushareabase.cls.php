<?php
/* 
** ����λ�ķ�������
** ģ�����û��������֣�Ӧ�û���(pushareas.cac.php)����Ӧ��ʼ��ȫ����Դ( _pushareas.cac.php)
** ע�⣺Ϊ���ڻ�����ʹ����չ�ľ�̬�������ڻ�����ʹ�ã���չ��::method�����ʹ�ã�self::method������֧����չ��
*/
!defined('M_COM') && exit('No Permission');
class cls_PushAreaBase{
	
	# ��ȡ���ã�ͨ���Ի���ķ�ʽ����ȡ
	# �����ȡ��ȫ���������飬ָ��ID�����ã�ָ��ID��KEY������
    public static function Config($paid = '',$Key = ''){
		$re = cls_cache::Read(cls_PushArea::CacheName());
		if($paid){
			$paid = cls_PushArea::InitID($paid);
			$re = isset($re[$paid]) ? $re[$paid] : array();
			if($Key){
				$re = isset($re[$Key]) ? $re[$Key] : '';
			}
		}
		return $re;
    }
	
	# ��ȡ�ֶ����ã���Ӧ�û����ж�ȡ
    public static function Field($paid = '',$FieldName = ''){
		$re = array();
		if(cls_PushArea::Config($paid)){
			$re = cls_cache::Read('pafields',$paid);
			if($FieldName){
				$re = isset($re[$FieldName]) ? $re[$FieldName] : array();
			}
		}
		return $re;
    }
	
	# ��������
    public static function CacheName($isInit = false){
		return ($isInit ? '_' : '').'pushareas';
    }
	
	# �������ݱ�ı���
    public static function ContentTable($paid = 0){
		$paid = cls_PushArea::InitID($paid);
		return $paid ? $paid : '';
    }
	
	# ����Ӧ�û���
	public static function UpdateCache(){
		cls_PushArea::_SaveCache();
	}
	
	# ����ģ���е���ȫ����Դ���൱�ڸ������ݱ�
	public static function SaveInitialCache($CacheArray = ''){
		cls_PushArea::_SaveCache($CacheArray,true);
	}
	
	# ��ID���г�ʼ��ʽ��
    public static function InitID($paid = ''){
		$paid = empty($paid) ? '' : trim(strtolower($paid));
		return cls_string::ParamFormat($paid);
	}
	
	# ����¶����paid�Ƿ�Ϸ�
	public static function CheckNewID($paid = ''){
		if(!($paid = cls_PushArea::InitID($paid))) return 'Ψһ��ʶ����Ϊ��';
		if(!preg_match("/push_/i",$paid)) return '����push_Ϊǰ׺';
		if(cls_PushArea::InitialOneInfo($paid)) return 'ָ����Ψһ��ʶ��ռ��';
		return '';
	}
	
	# ������ݱ��Ƿ�����
	public static function CheckTable($paid = ''){
		if(!cls_PushArea::InitialOneInfo($paid)) return 'δָ������λ';
		$ContentTable = cls_PushArea::ContentTable($paid);
		if(!($ColumnArray = @cls_DbOther::ColumnNames($ContentTable))) return "���ݱ�{$ContentTable}������";
		if(!($Fields = cls_fieldconfig::InitialFieldArray('pusharea',$paid))) return "����λδ�����ֶ�";
		$iColumnArray = cls_DbOther::ColumnNames('init_push');
		$iColumnArray = array_unique(array_merge($iColumnArray,array_keys($Fields)));
		if($diff = array_diff($iColumnArray,$ColumnArray)){
			return "ȱ�������ֶΣ�".implode(',',$diff);
		}
		return '';
	}
	
	# �޸����ݱ�(�ر�����ֶ��ڻ������������λ���ֶ�����)
	public static function RepairTable($paid = ''){
		if(!($PushArea = cls_PushArea::InitialOneInfo($paid))) return false;
		$_RepairOK = true;
		$ContentTable = cls_PushArea::ContentTable($paid);
		if(!($ColumnArray = @cls_DbOther::ColumnNames($ContentTable))){
			# �½����ݳ�ʼ��
			cls_PushArea::_AddContentTable($paid,$PushArea['cname'].'����λ��');
		}else{ # ��ȫ��ʼ����ֶ�
			$iColumnArray = cls_DbOther::ColumnNames('init_push',true);
			$db = _08_factory::getDBO();
			foreach($iColumnArray as $k => $v){
				if(!in_array($k,$ColumnArray)){
					$v['Field'] = 'Add '.$v['Field'];
					$db->alterTable('#__'.$ContentTable,$v);
					$ColumnArray[] = $k;
				}
			}
		}
		# ��ȫ�ֶ������е��ֶ�
		$Fields = cls_fieldconfig::InitialFieldArray('pusharea',$paid);
		foreach($Fields as $k => $v){
		  	if(!in_array($k,$ColumnArray)){
				try{
					cls_fieldconfig::AlterContentTableByConfig($v,true);
				}catch (Exception $e){
					$_RepairOK = false;
					continue;
				}
		  	}
		}
		return $_RepairOK;
	}
	
	
	
	# ��̬���������飬ֱ�����Գ�ʼ����Դ(�����ݿ�/����)�����ں�̨����ʵʱ�ǻ������
	# �����Է�������ָ������Ĵ���$ptid=-1��ԭʼ��Դ�����$ptid=0ʱ�������������������
	public static function InitialInfoArray($ptid = -1){
		$ptid = (int)$ptid;
		$CacheArray = cls_cache::Read(cls_PushArea::CacheName(true),'','',1);
		$re = array();
		if($ptid == -1){ # ��ԭʼ��Դ
			$re = $CacheArray;
		}elseif(!$ptid){ # ptid = 0 �����������������
			$pushtypes = cls_pushtype::InitialInfoArray();
			foreach($pushtypes as $k => $v){
				foreach($CacheArray as $x => $y){
					if($y['ptid'] == $k) $re[$x] = $y;
				}
			}
		}else{ # ָ������
			foreach($CacheArray as $x => $y){
				if($y['ptid'] == $ptid) $re[$x] = $y;
			}
		}
		return $re;
	}
	
	# ��̬�ĵ������ϣ�ֱ���Գ�ʼ����Դ(�����ݿ�/����)�����ں�̨����ʵʱ�ǻ������
	public static function InitialOneInfo($id){
		$id = cls_PushArea::InitID($id);
		$CacheArray = cls_PushArea::InitialInfoArray();
		return empty($CacheArray[$id]) ? array() : $CacheArray[$id];
	}
	
	
	# �������޸�һ�����ã�ͬʱ���������ֶμ����������ݱ�
	# ���ﲻ����sourcefields�������޸ģ�ֻ�Ǳ���ԭֵ
	# ע�⣺$newConfig��addslashes֮�������
	public static function ModifyOneConfig($nowID,$newConfig = array(),$isNew = false){
		
		$nowID = cls_PushArea::InitID($nowID);
		if(empty($newConfig)) return;
		
		# ���������ü�¼
		cls_Array::array_stripslashes($newConfig);
		$nowID = cls_PushArea::_SaveOneConfig($nowID,$newConfig,$isNew);
		
		# �½���¼���ֶμ��������ݱ�Ĵ���
		if($isNew){
			# ��������λʱ�������ͷ����ֶν����ر���
			$ContentTableParams = array();
			if(isset($newConfig['cname'])){
				$ContentTableParams['cname'] = trim(strip_tags($newConfig['cname']));
			}
			for($k = 1;$k <= 2;$k ++){
				if(isset($newConfig["classoption$k"])){
					$ContentTableParams["classoption$k"] = $newConfig["classoption$k"];
					unset($newConfig["classoption$k"]);
				}
			}
			cls_PushArea::_AddContentTable_Fields($nowID,$ContentTableParams);
		}
		
		# ��������Ӧ�û���
		cls_PushArea::UpdateCache();
		
		return $nowID;
	}
	
	# ����һ������λ
	# ע�⣺$newConfig��addslashes֮�������
	public static function CopyOneConfig($fromID,$toID,$newConfig = array()){
		
		if(!($fromCfg = cls_pusharea::InitialOneInfo($fromID))){
			throw new Exception('��ָ����ȷ���Ƽ�λ��');
		}
		cls_Array::array_stripslashes($newConfig);
		$newConfig = array_merge($fromCfg,$newConfig);
		
		# �������ü�¼
		$nowID = cls_PushArea::_SaveOneConfig($toID,$newConfig,true);
		
		# �����ֶμ�¼
		try {
			cls_fieldconfig::CopyOneSourceFields('pusharea',$fromID,$nowID);
		} catch (Exception $e){
			throw new Exception($e->getMessage());
		}
		
		# �޸����ݱ�
		cls_PushArea::RepairTable($nowID);
		
		# ��������Ӧ�û���
		cls_PushArea::UpdateCache();
		
		return $nowID;
	}
	
	public static function DeleteOne($paid,$force = false){
		global $tblprefix;
		$paid = cls_PushArea::InitID($paid);
		if(!($pusharea = cls_PushArea::InitialOneInfo($paid))) return '��ָ����ȷ������λ';
		
		$db = _08_factory::getDBO();
		if(!$force && $db->result_one("SELECT COUNT(*) FROM {$tblprefix}.".cls_PushArea::ContentTable($paid),0,'SILENT')){
			return '����λ��û�й�����������Ϣ����ɾ��';
		}
		
		cls_fieldconfig::DeleteOneSourceFields('pusharea',$paid);
		$db->query("DROP TABLE IF EXISTS {$tblprefix}".cls_PushArea::ContentTable($paid),'SILENT');
		
		# ����
		$CacheArray = cls_PushArea::InitialInfoArray();
		unset($CacheArray[$paid]);
		cls_PushArea::SaveInitialCache($CacheArray);
		
		# ��������Ӧ�û���
		cls_PushArea::UpdateCache();
	}
	
	# ����һ���µ����ݹ�����
	protected static function _AddContentTable($newID = 0,$Comment = '����λ��'){
		global $db,$tblprefix;
		$newID = cls_PushArea::InitID($newID);
		if(!$newID) return false;
		$db->query("CREATE TABLE {$tblprefix}".cls_PushArea::ContentTable($newID)." LIKE {$tblprefix}init_push");
		$db->query("ALTER TABLE {$tblprefix}".cls_PushArea::ContentTable($newID)." COMMENT='".$Comment."'");
	
	
	}
	# Ϊ�������������ݹ������ֶ�����
	protected static function _AddContentTable_Fields($newID = 0,$AddParams = array()){
		$newID = cls_PushArea::InitID($newID);
		if(!$newID) return false;
		
		# �������ݱ�
		cls_PushArea::_AddContentTable($newID,@$AddParams['cname'].'����λ��');
		
		# �����ֶ����ü�¼
		$initfields = array (
		  'subject' => 
		  array (
			'datatype' => 'text',
			'cname' => '����',
			'issystem' => '1',
			'length' => '100',
			'nohtml' => '1',
			'notnull' => '1',
			'mode' => '1',
		  ),
		  'url' => 
		  array (
			'datatype' => 'text',
			'cname' => 'URL',
			'length' => '255',
			'nohtml' => '1',
			'notnull' => '1',
			'mode' => '1',
		  ),
		  'thumb' => 
		  array (
			'datatype' => 'image',
			'cname' => '����ͼ',
			'nohtml' => '1',
		  ),
		);
		for($k = 1;$k <= 2;$k ++){
			$_field = array(
				'cname' => "���ͷ���$k",
			);
			if(empty($AddParams["classoption$k"])){
				$_field['datatype'] = 'select';
			}else{
				$_field['datatype'] = 'cacc';
				$_field['coid'] = $AddParams["classoption$k"] < 0 ? 0 : intval($AddParams["classoption$k"]);
			}
			$initfields["classid$k"] = $_field;
		}
		$i = 0;
		foreach($initfields as $k => $v){
			$v['ename'] = $k;
			$v['type'] = 'pa';
			$v['tpid'] = $newID;
			$v['iscommon'] = 1;
			$v['vieworder'] = ++$i;
			$v['tbl'] = cls_PushArea::ContentTable($newID);
			cls_fieldconfig::ModifyOneConfig('pusharea',$newID,$v);
		}
	}	
	
	
	# �����������һ�����ü�¼����ʼ����Դ����Ӱ���ֶμ�¼���������ݱ�
	protected static function _SaveOneConfig($nowID,$newConfig = array(),$isNew = false){
		
		# Ԥ�������
		$nowID = cls_PushArea::InitID($nowID);
		if(!$isNew){
			if(!($oldConfig = cls_PushArea::InitialOneInfo($nowID))) cls_message::show('��ָ����ȷ������λ��');
			$nowID = $oldConfig['paid'];
		}else{
			if($re = cls_PushArea::CheckNewID($nowID)) cls_message::show($re);
			$newConfig['cname'] = trim(strip_tags(@$newConfig['cname']));
			if(!$newConfig['cname']) cls_message::show('����������λ����');
			if(empty($newConfig['sourcetype']) || !cls_PushArea::SourceType($newConfig['sourcetype'])) cls_message::show('������������Դ��');
			if(!($newConfig['ptid'] = max(0,intval(@$newConfig['ptid'])))) cls_message::show('��ѡ�����');
			if(!cls_pushtype::InitialOneInfo($newConfig['ptid'])) cls_message::show('ָ�������ͷ��಻����');
			$oldConfig = cls_PushArea::_OneBlankInfo($nowID);
		}
		
		# ��ʽ������
		if(isset($newConfig['cname'])){
			$newConfig['cname'] = trim(strip_tags($newConfig['cname']));
			$newConfig['cname'] = $newConfig['cname'] ? $newConfig['cname'] : $oldConfig['title'];
		}
		if(isset($newConfig['ptid'])){
			$newConfig['ptid'] = max(0,intval($newConfig['ptid']));
		}
		if(isset($newConfig['maxorderno'])){
			$newConfig['maxorderno'] = min(99,max(2,intval($newConfig['maxorderno'])));
		}
		if(isset($newConfig['orderspace'])){
			$newConfig['orderspace'] = min(3,max(0,intval($newConfig['orderspace'])));
		}
		if(isset($newConfig['copyspace'])){
			$newConfig['copyspace'] = min(3,max(0,intval($newConfig['copyspace'])));
		}
		if(isset($newConfig['smallson'])){
			$newConfig['smallson'] = empty($newConfig['smallson']) ? 0 : 1;
		}
		if(isset($newConfig['sourceadv'])){
			$newConfig['sourceadv'] = empty($newConfig['sourceadv']) ? 0 : 1;
		}
		if(isset($newConfig['sourcefields'])){
			if(empty($newConfig['sourcefields']) || !is_array($newConfig['sourcefields'])){
				$newConfig['sourcefields'] = array();
			}
		}
		foreach(array('smallids','sourcesql','script_admin','script_detail','script_load',) as $var){
			if(isset($newConfig[$var])){
				$newConfig[$var] = empty($newConfig[$var]) ? '' : trim($newConfig[$var]);
			}
		}
		
		# ��ֵ
		$InitConfig = cls_PushArea::_OneBlankInfo($nowID); # ��ȫ�����ýṹ
		foreach($InitConfig as $k => $v){
			if(in_array($k,array('paid'))) continue;
			if(isset($newConfig[$k])){ # ����ֵ
				$oldConfig[$k] = $newConfig[$k];
			}elseif(!isset($oldConfig[$k])){ # �²����ֶ�
				$oldConfig[$k] = $v;
			}
		}		
		
		# ����
		$CacheArray = cls_PushArea::InitialInfoArray();
		$CacheArray[$nowID] = $oldConfig;
		cls_PushArea::SaveInitialCache($CacheArray);
		
		return $nowID;
		
	}

	# �õ��ֶ���Դ�������б���ע��	
	public static function SourceFieldArray($type,$typeid,$onlysql = 0){
		$re = array();
		switch($type){
			case 'archives'://typeidΪģ��chid
				$tbls = array(atbl($typeid));
				if(!$onlysql){
					$tbls[] = "archives_$typeid";
					$arc_tpl = cls_tpl::arc_tpl($typeid);
					for($i = 0;$i <= @$arc_tpl['addnum'];$i ++){
						$key = 'arcurl'.($i ? $i : '');
						$re['{'.$key.'}'] = $key.' - ����ҳ'.($i ? "��$i" : '').'url';
					}
					$re['{marcurl}'] = 'marcurl - ��Ա�ռ�����ҳurl';
				}
			break;
			case 'members':
				$tbls = array("members");
				if(!$onlysql){
					$tbls[] = "members_$typeid";
					$tbls[] = "members_sub";
				}
				$re['{mspacehome}'] = 'mspacehome - ��Ա�ռ�url';
			break;
			case 'commus':
				$tbls = array(cls_commu::Config($typeid,'tbl'));
			break;
			case 'catalogs':
				$tbls = $typeid ? array("coclass$typeid") : array('catalogs');
				$cnstr = $typeid ? "ccid$typeid={ccid}" : 'caid={caid}';
				$re["[cnode::$cnstr::0::0]"] = "��Ŀ�ڵ� - [cnode::�����ִ�::����ҳ::�ֻ���]";
				$re["[mcnode::$cnstr::0]"] = '��ԱƵ���ڵ� - [mcnode::�����ִ�::����ҳ]';
			break;
		}
		
		$dbfields = cls_cache::Read('dbfields');
		
		foreach($tbls as $key => $tbl){
			$na = cls_DbOther::ColumnNames($tbl);
			$tbltype = '';
			if($key == 1){
				$tbltype = ' - ģ�ͱ�';
			}elseif($key == 2){
				$tbltype = ' - ��Ա����';
			}
			foreach($na as $k){
				if(!isset($re[$k])){
					$dtbl = $tbl;
					if($type == 'archives' && !in_str('_',$tbl)){
						$dtbl = "archives";
					}elseif($type == 'catalogs'){
						$dtbl = $typeid ? 'coclass' : 'catalogs';
					}
					$re['{'.$k.'}'] = $k.(empty($dbfields[$dtbl.'_'.$k]) ? '' : ' - '.$dbfields[$dtbl.'_'.$k]).$tbltype;
				}
			}
		}
		return $re;
	}
	
	# �����ֶ�-���ڣ�����������Դ�ֶ� ������
	public static function DateFieldArray($type,$chid,$onlysql = 0){
		$re = array(''=>'-��ѡ���ֶ�-'); //$sfields = array('' => '����������Դ�ֶ�') + 
		if(!in_array($type,array('archives','members'))) return $re; 
		if($type=='members'){
			$fields = cls_cache::Read('mfields',$chid);
			$ugidarr = array();
			$grouptypes = cls_cache::Read('grouptypes');
			foreach($grouptypes as $gk=>$gv){
				if($gk<=2) continue; echo "";
				if(!empty($gv['mchids']) && strstr(",{$gv['mchids']},",",$chid,")) continue; // �ų���������ģ���н�ֹʹ�á������ã�'mchids' => ',1,3,11,12,13,14,15', 
				$re["grouptype{$gk}date"] = '��Ա����ϵ['.$gv['cname']."]����ʱ��(grouptype{$gk}date)"; 
			}
		}else{
			$fields = cls_cache::Read('fields',$chid);
			$re['enddate'] = '����[����ʱ��](enddate)';
		}
		foreach($fields as $k=>$v){
			if($v['datatype']=='date'){
				$re[$k] = '�ܹ�['.$v['cname']."]($k)";
			}		
		}
		return $re;
	}
	
	public static function SourceType($Type = '',$Key = ''){
		$SourceTypeArray = array(
			'archives' => array(
				'title' => '�ĵ�',
				'cache' => 'channels',
			),
			'members' => array(
				'title' => '��Ա',
				'cache' => 'mchannels',
			),
			'catalogs' => array(
				'title' => '����',
				'cache' => 'cotypes',
			),
			'commus' => array(
				'title' => '����',
				'cache' => 'commus',
			),
		);
		$re = $SourceTypeArray;
		if($Type){
			$re = isset($re[$Type]) ? $re[$Type] : array();
			if($Key){
				$re = isset($re[$Key]) ? $re[$Key] : '';
			}
		}
		return $re;
	}
	
	public static function SourceIDArray(){
		$re = array();
		$SourceTypeArray = cls_PushArea::SourceType();
		foreach($SourceTypeArray as $k => $v){
			$na = cls_cache::Read($v['cache']);
			if($k == 'catalogs') $na = array(0 => array('cname' => '��Ŀ','self_reg' => 0)) + $na;
			foreach($na as $x => $y){
				if($k == 'catalogs' && $y['self_reg']) continue;
				$re[$k.'_'.$x] = "{$v['title']}_{$x}_{$y['cname']}";
			}
		}
		return $re;
	}
	
	public static function SourceIDTitle($type,$typeid){
		static $sarr;
		$sarr || $sarr = cls_PushArea::SourceIDArray();
		return empty($sarr[$type.'_'.$typeid]) ? '-' : $sarr[$type.'_'.$typeid];
	}
	
	# �����̨�����չ���˵�����ʾ
	public static function BackMenuCode(){
		$pushtypes = cls_cache::Read('pushtypes');
		$pushareas = cls_PushArea::Config();
		$curuser = cls_UserMain::CurUser();
		$na = array();
		if(!$curuser->NoBackFunc('pusharea')){
			$na['_pusharea'] = array('title' => '����λ�ܹ�','level' => 0,'active' => 1,);
		}
		$na['_all'] = array('title' => 'ȫ������λ','level' => 0,'active' => 1,);
		foreach($pushtypes as $k => $v){
			$na["_$k"] = array('title' => $v['title'],'level' => 0,'active' => 0,);
			$i = 0;$n = false;
			foreach($pushareas as $x => $y){
				if($y['ptid'] == $k){
					$na[$x] = array('title' => $y['cname'],'level' => 1,'active' => 1,);
					if(cls_pusher::HaveNewToday($x)){
						$na[$x]['title'] = "<font color='#FF0000'>{$y['cname']}</font>";
						$n = true;
					}
					$i ++;
				}
			}
			if(!$i){
				unset($na["_$k"]);
			}elseif($n){
				$na["_$k"]['title'] = "<font color='#FF0000'>{$v['title']}</font>";
			}
		}
		return ViewBackMenu($na,3);
	}
	
	# �����̨����൥������Ĺ���ڵ�չʾ(ajax����)
	public static function BackMenuBlock($paid = 0){
		$UrlsArray = cls_PushArea::BackMenuBlockUrls($paid);
		return _08_M_Ajax_Block_Base::getInstance()->OneBackMenuBlock($UrlsArray);
	}
	
	# �����̨����൥������Ĺ���ڵ�url���飬���Ը�����Ҫ��Ӧ��ϵͳ������չ
	protected static function BackMenuBlockUrls($paid){
		$UrlsArray = array();
		$paid = cls_PushArea::InitID($paid);
		if($paid == '_pusharea'){
			$UrlsArray['����λ����'] = "?entry=pushareas";
			$UrlsArray['����λ����'] = "?entry=pushtypes";
			$UrlsArray['����λ�޸�'] = "?entry=pushareas&action=repair";
		}elseif($paid == '_all'){
			$UrlsArray['��������'] = "?entry=extend&extend=push_order_all";
			$UrlsArray['ͬ����Դ'] = "?entry=extend&extend=push_refresh_all";
		}elseif($pusharea = cls_PushArea::Config($paid)){
			$UrlsArray['���͹���'] = "?entry=extend&extend=pushs&paid=$paid";
			if(in_array($pusharea['sourcetype'],array('archives','members',))){
				$UrlsArray['������Ϣ'] = "?entry=extend&extend=push_load&paid=$paid";
			}
			$UrlsArray['��������'] = "?entry=extend&extend=push_order&paid=$paid";
			$UrlsArray['ͬ����Դ'] = "?entry=extend&extend=push_refresh&paid=$paid";
		}
		return $UrlsArray;
	}
	
	# ָ��Ӧ�û���/��ȫ����Դ�ķ�ʽ���»���
	protected static function _SaveCache($CacheArray = '',$isInit = false){
		if(!is_array($CacheArray)){ # ���Դ������������
			$CacheArray = cls_PushArea::InitialInfoArray();
		}
		if(!$isInit){
			foreach($CacheArray as $k => $v){
				if(empty($v['available'])) unset($CacheArray[$k]);
			}
		}
		cls_Array::_array_multisort($CacheArray,'vieworder',true);# ��vieworder��������
		cls_CacheFile::Save($CacheArray,cls_PushArea::CacheName($isInit),'',$isInit);
		
	}
	# һ���½���¼�ĳ�ʼ������
	protected static function _OneBlankInfo($ID = 0){
		return array(
			'paid' => cls_PushArea::InitID($ID),
			'cname' => '',
			'ptid' => 0,
			'sourcetype' => '',
			'sourceid' => 0,
			'smallids' => '',
			'smallson' => 0,
			'sourcesql' => '',
			'sourcefields' => array(),
			'sourceadv' => 0,
			'vieworder' => 0,
			'autopush' => 0, //�Զ�����
			'enddate_from' => '', //����������Դ�ֶ�,�ĵ���Ա
			'forbid_useradd' => 0, //��ֹ�ֶ����
			'available' => 1,
			'apmid' => 0,
			'autocheck' => 1,
			'maxorderno' => 10,
			'mspace' => 0,
			'orderspace' => 0,
			'copyspace' => 0,
			'script_admin' => '',
			'script_detail' => '',
			'script_load' => '',
		);
	}
	
}
