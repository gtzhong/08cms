<?php
/* 
** �ֶε����ò���(��ӡ��޸ġ�ɾ����)�����ݽ��ֶα��浽'ģ�建��/���ݿ�'�����ַ�ʽ
** ��ʱֻ�������ֶε����õ��޸ģ��б����֮�������Ͻ��������мܹ�
** ע�⣺��Ա��ϵͳ�ֶε������ǲ����޸ĵģ��ĵ��򸱼���ϵͳ�ֶο��޸ġ�
** ģ�����û��������֣�Ӧ�û���(��ffields*.cac.php)����Ӧ��ʼ��ȫ����Դ(�� _ffields*.cac.php)
** ע�����ݱ���ģ������Դ����ʱ�Ĳ��ǰ��cfgsΪ��ʽ������ִ�������cfgsΪ����
** ��Ϊ����ģ������Դ�����ݿⱣ�����ַ�ʽ����������Դ��ͬʱ������Ӧ�û��棬����Ҫע����Ҫ�������Ӧ�û���
*/

!defined('M_COM') && exit('No Permission');
class cls_FieldConfig{
	
	protected static $db = NULL;//���ݿ�����
	protected static $Table = 'afields';//�ֶ����ñ�
    protected static $params = array();
	protected static $fmdata = array();//������
	
	# �������
	protected static $SourceType = '';//��ǰ��������
	protected static $SourceID = '';//��ǰ����ID
	protected static $SourceConfig = '';//��ǰ��������
	
	# ��ǰ�ֶ�����
	protected static $datatype = '';//��ǰ�ֶ�����
	protected static $isNew = false;//�Ƿ�����ֶ�
	protected static $oldField = array();//ԭ�ֶ�����
	protected static $newField = array();//�޸ĺ󾭹�������ֶ�����
	private static $DataTypeInstance = NULL;//���������ֶ����ʵ��
	
	
	# ����Ӧ�û���
	public static function UpdateCache($SourceType = 'channel',$SourceID = 0){
		self::_SaveCache($SourceType,$SourceID);
	}
	
	# ר���ڸ���ģ���е���ȫ����Դ���൱�ڸ������ݱ�
	public static function SaveInitialCache($SourceType = 'channel',$SourceID = 0,$CacheArray = ''){
		self::_SaveCache($SourceType,$SourceID,$CacheArray,true);
	}
	
	# ��̬���ֶ��������飬ֱ�����Գ�ʼ����Դ(�����ݿ�/����)�����ں�̨����ʵʱ�ǻ������
	# $KeepDB �������ݿ�����ĸ�ʽ�������������������ڽ����������ݱ��и��Ƽ�¼
	public static function InitialFieldArray($SourceType = 'channel',$SourceID = 0,$OnlyAvailable = false,$KeepDB = false){
		if(self::_CheckSource($SourceType,$SourceID)) return array();
		
		if(self::isTemplateConfig($SourceType)){
			$re = cls_cache::Read(self::FieldCacheName($SourceType,true),$SourceID,'',1);
			foreach($re as $k => $v){
				if($OnlyAvailable && empty($v['available'])){
					unset($re[$k]);
					continue;
				}
				unset($re[$k]['fid']);
			}
		}else{
			$re = array();
			self::$db->select('*')->from(self::_Table())->where(array('type' => self::FieldType($SourceType)))->_and(array('tpid' => $SourceID));
			if($OnlyAvailable){
				self::$db->_and(array('available' => 1));
			}
			self::$db->order('vieworder,fid')->exec();
			while($r = self::$db->fetch()){
				if(!$KeepDB){
					cls_CacheFile::ArrayAction($r,'cfgs','varexport');
				}
				unset($r['fid']);
				$re[$r['ename']] = $r;
			}
		}	
		return $re;
		
	}
	
	# ����¶����ename�Ƿ�Ϸ�
	public static function CheckNewID($SourceType = 'channel',$SourceID = 0,$ename = ''){
		if(!($ename = self::InitID($ename))) return 'Ψһ��ʶ����Ϊ��';
		if($re = self::_InitSource($SourceType,$SourceID)) return $re;
		if(preg_match(self::_EnameRegular(self::$SourceType),$ename))  return '�ֶα�ʶ���Ϲ淶��';
		if(in_array($ename,self::_UsedEnameArray())) return '�ֶα�ʶ�ظ�';
		if(self::_isDBKeepWord($ename)) return '�ֶα�ʶ��ֹʹ��';
		return '';
	}
	
	# ��ename���г�ʼ��ʽ��
    public static function InitID($ename = ''){
		$ename = empty($ename) ? '' : trim(strtolower($ename));
		return cls_string::ParamFormat($ename);
	}
	
	
	# ��̬�ĵ����ֶ����ϣ�ֱ���Գ�ʼ����Դ(�����ݿ�/����)�����ں�̨����ʵʱ�ǻ������
	public static function InitialOneField($SourceType = 'channel',$SourceID = 0,$ename = ''){
		if(self::isTemplateConfig($SourceType)){
			$fields = self::InitialFieldArray($SourceType,$SourceID);
			$re = @$fields[$ename];
		}else{
			if(self::_CheckSource($SourceType,$SourceID)) return array();
			$re = self::$db->select('*')->from(self::_Table())->where(array('type' => self::FieldType($SourceType)))->_and(array('tpid' => $SourceID))->_and(array('ename' => $ename))->exec()->fetch();
			if($re){
				cls_CacheFile::ArrayAction($re,'cfgs','varexport');
				unset($re['fid']);
			}
		}
		$re = $re ? $re : array();
		return $re;
	}
	
	# �༭�����ֶε�����
	public static function EditOne($SourceType = 'channel',$SourceID = 0,$FieldName = ''){
		self::_InitEdit($SourceType,$SourceID);
		self::_LoadOneField($FieldName);
		self::$isNew = $FieldName ? false : true;
		if(!submitcheck('bsubmit')){
			self::_ViewOne();
		}else{
			self::_SaveOne();
		}
	}
	
	# ����ָ��������ֶΣ�ֻ�漰afields��¼���ֶλ��棬���漰��ṹ�ı����������ϸ���һ�����塣
	public static function CopyOneSourceFields($SourceType = 'channel',$fromID = 0,$toID = 0){
		
		if(!($fromID = self::SourceInitID($SourceType,$fromID))){ # ��Ŀ���Աͨ���ֶβ��ܸ���
			throw new Exception('��ָ����Դ����ID��');
		}
		if(!($toID = self::SourceInitID($SourceType,$toID))){
			throw new Exception('��ָ��Ŀ������ID��');
		}
		if($re = self::_InitSource($SourceType,$toID)){ # ��ȡĿ����������
			throw new Exception($re);
		}
		
		$CacheArray = self::InitialFieldArray($SourceType,$fromID,false,true);
		foreach($CacheArray as $k => &$v){
			$v['tpid'] = $toID;
			$v['tbl'] = self::_ContentTable($v);
		}
		if(self::isTemplateConfig($SourceType)){ # ɾ����ȫ����Դ
			self::SaveInitialCache($SourceType,$toID,$CacheArray);
		}else{
			self::_InitDB();
			$CacheArray = maddslashes($CacheArray,true);
			foreach($CacheArray as $k => $v){
				self::$db->insert(self::_Table(),$v)->exec();
			}
		}
		# �����ֶλ���
		self::UpdateCache($SourceType,$toID);
		return true;
	}
	
	# ɾ��ָ����Դ�������ֶ����ã�ͨ����Ϊ�������Դ��ɾ���������ﲻ�漰���ݱ�ṹ�Ĵ���ǰ�ڵ�Ԥ���
	# ͬʱɾ���ֶλ���
	public static function DeleteOneSourceFields($SourceType = 'channel',$SourceID = 0){
		if(!($SourceID = self::SourceInitID($SourceType,$SourceID))) return; # ��Ŀ���Աͨ���ֶβ�����Ϊһ����Դ��ɾ���������ֶ�
		if(self::isTemplateConfig($SourceType)){ # ɾ����ȫ����Դ
			cls_CacheFile::Del(self::FieldCacheName($SourceType,true),$SourceID); 
		}else{
			self::_InitDB();
			self::$db->delete(self::_Table())->where(array('type' => self::FieldType($SourceType)))->_and(array('tpid' => $SourceID))->exec();
		}
		cls_CacheFile::Del(self::FieldCacheName($SourceType),$SourceID);
	}
	
	# ͨ����������ķ�������ӻ����һ���ֶμ�¼�����漰���������ݱ�ṹ
	# ͨ��������ϲ����±��ֶ��������ֶμ�¼������ʱ�����ֶ�����(���ݱ������⴦��)
	# ������ֶ����ϲ����м�⼰�����ڴ���֮ǰ��Ҫ�ƿ��������ϵ�������Ϸ��ԡ�
	public static function ModifyOneConfig($SourceType = 'channel',$SourceID = 0,array $newField = array(),$FieldName = ''){
		if($re = self::_InitSource($SourceType,$SourceID)) cls_message::show($re);
		self::_LoadOneField($FieldName);
		self::$isNew = $FieldName ? false : true;
		if(self::$isNew){
			foreach(array('ename','cname','datatype') as $k){
				if(empty($newField[$k])) cls_message::show('ȱ�����ϣ����ֶ������޷�����');
			}
			self::$newField = self::_OneBlankField();
		}else{
			self::$newField = maddslashes(self::$oldField,true);
		}
		foreach(self::$newField as $k => &$v){
			if(isset($newField[$k])){
				$v = $newField[$k];
			}
		}
		
		# ���ӻ��޸ĵ�ǰ�ֶε����ü�¼
		self::_SaveOneConfig();
		
		# �����ֶλ���
		self::UpdateCache($SourceType,$SourceID);
	}
	
	# ɾ��ָ�����ֶΣ����ֶ����ƣ���ͬʱɾ������ֶ�
	# ͬʱ�������ݱ�ṹ��ͨ���ֶε��ֶ����ø�����
	public static function DeleteField($SourceType = 'channel',$SourceID = 0,$DelArray = array(),$UpdateCache = false){
		if(!$DelArray) return array();
		if(!is_array($DelArray)) $DelArray = array($DelArray);
		
		# ����ǰ�ֶμ�¼��������ͨ���ֶ��ڸ���ģ���еĸ���
		$Deleteds = array();
		foreach($DelArray as $ename){
			if($_field = self::InitialOneField($SourceType,$SourceID,$ename)){ 
			
				# �ų�����ɾ�����ֶ�
				if(($SourceType == 'mchannel') && $SourceID){ # �ڻ�Աģ���ֶι���ʱ������ɾ��ͨ���ֶ�
					if(!empty($_field['iscommon'])) continue;
				}else{
					if(empty($_field['iscustom'])) continue; # ���Զ��ֶβ���ɾ��
				}
				
				# ɾ��������ݱ���ֶ�
				cls_dbother::DropField($_field['tbl'],$_field['ename'],$_field['datatype']);
				
				# ɾ�����ֶε����ü�¼
				self::_DelOneConfig($SourceType,$SourceID,$ename);
  
				$Deleteds[] = $_field['ename'];
			}
		}
		
		if($Deleteds){
			# ���»���
			if($UpdateCache){
				self::UpdateCache($SourceType,$SourceID);
			}
			# �Ի�Աͨ���ֶ��ڸ���ģ���еĸ��������ر���
			if(($SourceType == 'mchannel') && !$SourceID){
				foreach($Deleteds as $ename){
					self::$db->delete(self::_Table())->where(array('type' => self::FieldType($SourceType)))->_and(array('ename' => $ename))->exec();
				}
				$mchannels = cls_mchannel::InitialInfoArray();
				foreach($mchannels as $k => $v){
					self::UpdateCache(self::$SourceType,$k);
				}
			}
		}
		return $Deleteds;
	}
	# ȡ���ֶ��������ϣ�ָ��$type�����ظ����ͱ��⣬���򷵻������ֶ���������
	public static function datatype($type = ''){
		$datatypes = array(//����������ֶ�����
			'text' => '�����ı�',
			'multitext' => '�����ı�',
			'htmltext' => 'Html�ı�',
			'image' => '��ͼ',
			'images' => 'ͼ��',
			'flash' => 'Flash',
			'flashs' => 'Flash��',
			'media' => '��Ƶ',
			'medias' => '��Ƶ��',
			'file' => '��������',
			'files' => '�������',
			'select' => '����ѡ��',
			'mselect' => '����ѡ��',
			'cacc' => '��Ŀѡ��',
			'date' => '����(ʱ���)',
			'int' => '����',
			'float' => 'С��',
			'map' => '��ͼ',
			'vote' => 'ͶƱ',
			'texts' => '�ı���',
		);
		return $type ? (isset($datatypes[$type]) ? $datatypes[$type] : '') : $datatypes;
	}
	# �Ƿ񱣴�Ϊģ�����û���
    public static function isTemplateConfig($SourceType = 'channel'){
		return in_array($SourceType,array('fchannel','pusharea',)) ? true : false;
	}
	
	# �ֶλ�������
    public static function FieldCacheName($SourceType,$isInit = false){
		return ($isInit ? '_' : '').($SourceType == 'channel' ? '' : self::FieldType($SourceType)).'fields';
    }
	
	public static function FieldType($SourceType = 'channel'){
		return self::_SourceVar($SourceType,'Type');
	}
	
	# ���ݴ�����ֶ����ò������޸Ļ������������ݱ�Ľṹ
	public static function AlterContentTableByConfig($newCfg = array(),$isNew = false,$oldCfg = array()){
		
		if(empty($newCfg['tbl']) || empty($newCfg['ename']) || empty($newCfg['datatype'])){
			throw new Exception('��ָ�����ݱ��ֶα�ʶ�����͡�');
		}
		if(in_array($newCfg['datatype'],array('mselect','select','text',)) && !isset($newCfg['length'])){
			throw new Exception('δָ�����ݱ��ֶγ��ȡ�');
		}
		if(($newCfg['datatype'] == 'cacc') && !isset($newCfg['cnmode'])){
			throw new Exception('��ָ�������ѡ��ʽ��');
		}
		
		if(in_array($newCfg['datatype'],array('files','flashs','htmltext','images','medias','multitext','texts','vote',))){
			$_sqlstr = "text NOT NULL";
		}elseif(in_array($newCfg['datatype'],array('file','flash','image','media',))){
			$_sqlstr = "varchar(255) NOT NULL default ''";
		}elseif(in_array($newCfg['datatype'],array('mselect','text',))){
			$_sqlstr = "varchar(".$newCfg['length'].") NOT NULL default ''";
		}elseif($newCfg['datatype'] == 'select'){
			$_sqlstr = $newCfg['length'] ? "varchar($newCfg[length]) NOT NULL default ''" : "int(10) NOT NULL default 0";
		}elseif($newCfg['datatype'] == 'int'){
			$_sqlstr = "int(11) NOT NULL default 0";
		}elseif($newCfg['datatype'] == 'map'){
			$_sqlstr = "varchar(40) NOT NULL default ''";
		}elseif($newCfg['datatype'] == 'date'){
			$_sqlstr = "int(10) NOT NULL default 0";
		}elseif($newCfg['datatype'] == 'float'){
			$_sqlstr = "float NOT NULL default 0";
		}elseif($newCfg['datatype'] == 'cacc'){
			$_sqlstr = "smallint(6) unsigned NOT NULL default 0";
		}
		
		if($isNew){
			cls_dbother::AddField($newCfg['tbl'],$newCfg['ename'],$newCfg['datatype'],$_sqlstr);
			if(($newCfg['datatype'] == 'cacc') && $newCfg['cnmode']){ # ����Ƕ�ѡ
				cls_dbother::AlterFieldSelectMode($newCfg['cnmode'],0,$newCfg['ename'],$newCfg['tbl']);
			}
		}else{
			if(in_array($newCfg['datatype'],array('mselect','select','text',)) && $newCfg['length'] != $oldCfg['length']){ # ����ֶγ���
				self::$db->query("ALTER TABLE ".cls_env::GetG('tblprefix').$newCfg['tbl']." CHANGE ".$newCfg['ename']." ".$newCfg['ename']." $_sqlstr");
			}elseif(($newCfg['datatype'] == 'cacc') && $newCfg['cnmode'] != $oldCfg['cnmode']){ # �����/��ѡ
				if(!cls_dbother::AlterFieldSelectMode($newCfg['cnmode'],$oldCfg['cnmode'],$newCfg['ename'],$newCfg['tbl'])){
					$newCfg['cnmode'] = $oldCfg['cnmode'];
				}
			}
		}
		return $newCfg;
	
	}	
	private static function _Table(){
		return '#__'.self::$Table;
	}
	
    private static function _InitEdit($SourceType = 'channel',$SourceID = 0){
		if($re = self::_InitSource($SourceType,$SourceID)) cls_message::show($re);
		self::$params = cls_env::_GET_POST();
		if(!empty(self::$params['fmdata'])) self::$fmdata = self::$params['fmdata'];
		if(self::$SourceType == 'commu' && empty(self::$SourceConfig['tbl'])) cls_message::show('δ���ý�����');
    }
	
	# ����ֶ�������������ͼ���ϸ����
	# ʹ������Ҫ�л����壬�������ĵ�ǰself::$SourceConfig��self::$SourceID�ĳ���
    private static function _CheckSource($SourceType = 'channel',$SourceID = 0){
		if(!self::_SourceVar($SourceType)) return '��ָ���ֶ������������͡�';
		if(!self::_LoadSourceConfig($SourceType,$SourceID)) return '�������ϲ���Ϊ�ա�';
		self::_InitDB();
	}
	
	# ��ʼ�����ݿ�
    protected static function _InitDB(){
		self::$db = _08_factory::getDBO();
	}
	
	# ȡ���ֶ�������������ͼ���ϸ����
    private static function _InitSource($SourceType = 'channel',$SourceID = 0){
		$SourceID = self::SourceInitID($SourceType,$SourceID);
		if((self::$SourceType == $SourceType) && (self::$SourceID == $SourceID) && self::$SourceConfig) return; # �����ظ�����
		self::$SourceType = $SourceType;
		if(!self::_SourceVar(self::$SourceType)) return '��ָ���ֶ������������͡�';
		self::$SourceID = self::SourceInitID($SourceType,$SourceID);
		self::$SourceConfig = self::_LoadSourceConfig(self::$SourceType,self::$SourceID);
		if(empty(self::$SourceConfig)) return '�������ϲ���Ϊ�ա�';
		self::_InitDB();
	}
	
	# ȡ��ָ�����������
    private static function _LoadSourceConfig($SourceType = 'channel',$SourceID = 0){
		$re = array();
		if(empty($SourceID)){
			switch($SourceType){
				case 'mchannel': # �൱��mchid=0��һ����Աģ��
					$re	= array(
						'mchid' => 0,
						'cname' => 'ͨ���ֶ�',
					);
				break;
				case 'catalog': # ģ����ϵ�����ã�ע�������������Ŀ��ȥ���afield�е�tpid=0������
					$re	= array(
						'coid' => 0,
						'title' => '��Ŀ',
					);
				break;
			}
		}else{
			$ClassName = self::_SourceVar($SourceType,'Class');
			$_tmpObj = new $ClassName(); //ĳЩ�����,method_exists�жϲ�����(��һ��),����$reΪ��
			if($ClassName && method_exists($_tmpObj,'InitialOneInfo')){
				$re = call_user_func_array(array($ClassName,'InitialOneInfo'),array($SourceID));
			}
			unset($_tmpObj);
		}
		return $re;
		
	}
	# ȡ��ָ���ֶε���ϸ����
    private static function _LoadOneField($FieldName = ''){
		if(!($FieldName = trim($FieldName))){//����ֶ�ʱ����Ҫָ���ֶ���
			self::$oldField = array();
			return;
		}
		self::$oldField = self::InitialOneField(self::$SourceType,self::$SourceID,$FieldName);
		if(empty(self::$oldField)) cls_message::show('ָ�����ֶβ����ڡ�');
	}
	# ����ָ���Ĳ���������url����ע����Ҫ�ڳ�ʼ���������Ϻ����
	public static function _RouteUrl($Action = 'fieldone'){
		if(empty(self::$SourceConfig)) cls_message::show('�������ϲ���Ϊ�ա�');
		$Params = array();
		$Params['entry'] = self::$SourceType.'s';
		if(self::$SourceID){
			$Params[self::_SourceVar(self::$SourceType,'ID')] = self::$SourceID;
		}
		switch($Action){
			case 'fieldone':
				$Params['action'] = $Action;
				if(!empty(self::$oldField['ename'])) $Params['fieldname'] = self::$oldField['ename'];
				break;
			case 'onefinish': # ��ʱ�������оɹ��򣬱༭(���)��󷵻ص�url
				switch(self::$SourceType){
					case 'mchannel':
						$Params['action'] = self::$SourceID ? 'mchannelfields' : 'initmfieldsedit';
						break;
					case 'fchannel':
						$Params['action'] = 'fchanneldetail';
						break;
					case 'catalog':
						$Params['action'] = 'cafieldsedit';
						break;
					case 'cotype':
						$Params['action'] = 'ccfieldsedit';
						break;
					case 'channel':
						$Params['action'] = 'channelfields';
						break;
					case 'commu':
						$Params['action'] = 'commufields';
						break;
					case 'pusharea':
						$Params['action'] = 'pushareafields';
						break;
					
						
				}
				break;
		}
		return self::_Url($Params);	
	}
	private static function _Url($Params = array()){
		$Url = '';
		foreach($Params as $k => $v){
			$Url .= '&'.$k.'='.rawurlencode($v);
		}
		$Url = $Url ? '?'.substr($Url,1) : '#';
		
		return $Url;
	}
	
    private static function _FieldFormHeader($HaveForm = true){
		$_Title = (self::$isNew ? '���' : '�༭')."�ֶ� - ".self::_SourceVar(self::$SourceType,'Name').' - '.self::$SourceConfig[self::_SourceVar(self::$SourceType,'Title')];
		echo "<title>$_Title</title>";
		if($HaveForm){
			tabheader($_Title,'field_detail',self::_RouteUrl(),2,0,1);
		}
	}
    private static function _FieldFormFooter(){
		a_guide('ffieldadd');
	
	}
	
	# �����ֶα༭ʱ���ֶ�����
    private static function _FieldDataType(){
		if(!empty(self::$oldField)){
			self::$datatype = self::$oldField['datatype'];
		}elseif(!empty(self::$fmdata['datatype'])){
			self::$datatype = self::$fmdata['datatype'];
		}
		if(empty(self::$datatype) || !self::datatype(self::$datatype)) cls_message::show('��ָ����ȷ���ֶ����͡�');
		
		# �������������ֶεĴ�������󣬸ö���̳е�ǰ�࣬���ö���ķ���������δ����ʱ����̳б����еķ��������ԡ�
		$FieldClassName = 'cls_field_'.self::$datatype;
		self::$DataTypeInstance = new $FieldClassName;
	}
	
	# �����ֶ����õı�չʾ
    private static function _ViewOneForm(){
		self::_FieldFormHeader(); # ��ͷ��
		self::_FieldDataType(); # �����ֶ�����
		
		self::_fm_cname(); # �ֶ���������
		self::_fm_ename(); # Ӣ��Ψһ��ʶ
		self::_fm_datatype(); # ��ʾ�ֶ�����
		self::_fm_iscommon(); # ����iscommon������
		self::_fm_separator('�ֶ�����');
		
		# ��ͬ�����ֶεĲ������
		self::$DataTypeInstance->_fm_custom_region();
		
		# ͨ���ֶ��޸�Ӧ�õ�����ģ��
		self::_fm_common_to_other();
		
		tabfooter('bsubmit',self::$isNew ? '���' : '�ύ');
		self::_FieldFormFooter();
	}
	
    private static function _SaveOne(){
		self::_FieldFormHeader(false); # ���ڱ���
		self::_FieldDataType(); # �����ֶ�����
		
		self::_sv_PreCommon(); # ͨ�ò��ֵ����ݴ���
		
		# ��ͬ�����ֶεĲ��������ھ�������в���Ҫ�����Բ�����˷���
		self::$DataTypeInstance->_sv_custom_region();
		
		# �����ֶε��������޸ģ����������ݱ�����Ӧ�ı��
		self::_sv_content_table();
		
		# �����ֶε��������޸ģ����ֶ���������Ӧ�ı��
		self::$DataTypeInstance->_sv_field_config();
		
		# �޸����
		self::_sv_finish();
		
    }
	
	# ȡ�����ݱ���ֶ����������½��ֶεı�ʶ�ѱ�ʹ��
    private static function _UsedEnameArray(){
		$tbls = array();
		switch(self::$SourceType){
			case 'channel':
				$tbls = array('archives'.self::$SourceConfig['stid'],'archives_'.self::$SourceID);
				break;
			case 'mchannel':
				$tbls = array('members','members_sub');
				if(self::$SourceID) $tbls[] = 'members_'.self::$SourceID;
				break;
			case 'fchannel':
				$tbls = array('farchives','farchives_'.self::$SourceID);
				break;
			case 'catalog':
				$tbls = array('catalogs');
				break;
			case 'cotype':
				$tbls = array('coclass'.self::$SourceID);
				break;
			case 'commu':
				$tbls = array(self::$SourceConfig['tbl']);
				break;
			case 'pusharea':
				$tbls = array(cls_PushArea::ContentTable(self::$SourceID));
				break;
		}
		return cls_DbOther::ColumnNames(implode(',',$tbls));
    }
	# ȡ�����ݱ�ı���
	# ������Ҫ��ʼ������
    protected static function _ContentTable($FieldConfig = array()){
		switch(self::$SourceType){
			case 'channel':
				$Table = empty($FieldConfig['iscommon']) ? 'archives_'.self::$SourceID : 'archives'.self::$SourceConfig['stid'];
				break;
			case 'mchannel':
				$Table = empty($FieldConfig['iscommon']) ? 'members_'.self::$SourceID : 'members_sub';
				break;
			case 'fchannel':
				$Table = empty($FieldConfig['iscommon']) ? 'farchives_'.self::$SourceID : 'farchives';
				break;
			case 'catalog':
				$Table = 'catalogs';
				break;
			case 'cotype':
				$Table = 'coclass'.self::$SourceID;
				break;
			case 'commu':
				$Table = self::$SourceConfig['tbl'];
				break;
			case 'pusharea':
				$Table = cls_PushArea::ContentTable(self::$SourceID);
				break;
		}
		return $Table;
    }
	
	# ��ֹ�ֶα�ʶΪ���ݿⱣ���ؼ���
    private static function _isDBKeepWord($ename){
		$sysparams = cls_cache::cacRead('sysparams');
		if(!empty($sysparams['keepwords']) && in_array($ename,$sysparams['keepwords'])) return true;
		return false;
    }
	# �ֶα�ʶ���������
    private static function _EnameRegular($SourceType){
		$Regular = '[^a-zA-Z_0-9]+|^[0-9_]+';
		if(self::_SourceVar($SourceType,'RegAdd')) $Regular .= self::_SourceVar($SourceType,'RegAdd');
		return "/$Regular/";
    }
	
    private static function _ViewOne(){
		if(!self::_PreDataType()){
			self::_ViewOneForm();
		}
    }
	# Ԥѡ�ֶ����ͣ�ֻ�������ֶ�ʱ��Ҫʹ��
    private static function _PreDataType(){
		if(!empty(self::$oldField)) return false;
		$datatype = empty(self::$fmdata['datatype']) ? '' : self::$fmdata['datatype'];
		
		if(!$datatype){ # ��δѡ���ֶ�����
			self::_FieldFormHeader(1);
			trbasic('�ֶ�����','fmdata[datatype]',makeoption(self::datatype()),'select');
			if(self::$SourceType == 'channel'){
				trbasic('ѡ�����ݱ�','',makeradio('fmdata[iscommon]',array(1 => "�ĵ�����(archives".self::$SourceConfig['stid'].")",0 => "ģ�ͱ�archives_".self::$SourceID)),'',array('guide' => '�ĵ��б���Ҫչʾ���ֶ�ͨ�������������ֶλ�ֻ��Ҫ������ҳ��չʾ���ֶηŵ�ģ�ͱ�'));
			}
			tabfooter('bsubmit_datatype','����');
			self::_FieldFormFooter();
			return true;
		}elseif(!submitcheck('bsubmit_cacc') && $datatype == 'cacc'){ # �������Ŀ�ֶΣ���Ҫѡ����Ŀ�ֶε���ϵ����
			self::_FieldFormHeader(1);
			trbasic('�ֶ�����','',self::datatype($datatype),'');
			trhidden('fmdata[datatype]',$datatype);
			if(self::$SourceType == 'channel'){//ѡ���Ƿ�����
				trbasic('ѡ�����ݱ�','',makeradio('fmdata[iscommon]',array(1 => "�ĵ�����(archives".self::$SourceConfig['stid'].")",0 => "ģ�ͱ�archives_".self::$SourceID),empty(self::$fmdata['iscommon']) ? 0 : 1),'',array('guide' => '�ĵ��б���Ҫչʾ���ֶ�ͨ�������������ֶλ�ֻ��Ҫ������ҳ��չʾ���ֶηŵ�ģ�ͱ�'));
			}
			$coidsarr = array('0' => '��Ŀ');
			$cotypes = cls_cache::Read('cotypes');
			foreach($cotypes as $k => $v) if(empty($v['self_reg'])) $coidsarr[$k] = $v['cname'];
			trbasic('ѡ����Դ��ϵ','fmdata[coid]',makeoption($coidsarr),'select');
			tabfooter('bsubmit_cacc','����');
			self::_FieldFormFooter();
			return true;
		}
		return false;
	}
	# ��֮�ֶ�����
    protected static function _fm_datatype(){
		trbasic('�ֶ�����','',self::datatype(self::$datatype),'');
		if(self::$isNew) trhidden('fmdata[datatype]',self::$datatype);
	}
	
	# ��֮��������
    protected static function _fm_cname(){
		$Value = self::$isNew ? '' : self::$oldField['cname'];
		trbasic('���ı���','fmdata[cname]',$Value,'text',array('validate' => ' onfocus="initPinyin(\'fmdata[ename]\')"' . makesubmitstr('fmdata[cname]',1,0,0,30)));
	}
	
	# ��֮iscommon����
    protected static function _fm_iscommon(){
		if(self::$isNew){
			if(self::$SourceType == 'channel'){//ѡ���Ƿ�����
				trhidden('fmdata[iscommon]',empty(self::$fmdata['iscommon']) ? 0 : 1);
			}elseif(self::$SourceType == 'mchannel'){//�Ƿ�ͨ���ֶ�
				trhidden('fmdata[iscommon]',empty(self::$SourceID) ? 1 : 0);
			}
		}
	}
		
	# ��֮Ӣ�ı�ʶ
    protected static function _fm_ename(){
		$cms_abs = cls_env::mconfig('cms_abs');
		if(self::$isNew){
			$na = array(
				'validate'=>' offset="1"' . makesubmitstr('fmdata[ename]',1,'tagtype',0,30),
				'guide' => '�涨��ʽ��ͷ�ַ�Ϊ��ĸ�������ַ�ֻ��Ϊ"��ĸ�����֡�_"��',
				'addstr' => ' <input type="button" value="�Զ�ƴ��" onclick="autoPinyin(\'fmdata[cname]\',\'fmdata[ename]\')" />',
				);
			trbasic('Ӣ��Ψһ��ʶ','fmdata[ename]','','text',$na);
			$ajaxURL = $cms_abs . _08_Http_Request::uri2MVC("ajax=check_fieldname&sourcetype=".self::$SourceType."&sourceid=".self::$SourceID."&fieldname=%1");
			echo _08_HTML::AjaxCheckInput('fmdata[ename]', $ajaxURL);
		}else{
			trbasic('Ӣ��Ψһ��ʶ','',self::$oldField['ename'],'');
		}
	}
	# ��֮������ֶγ���
    protected static function _fm_length(){
	}
	# ��֮���벻��Ϊ��
    protected static function _fm_notnull(){
		$Value = self::$isNew ? 0 : self::$oldField['notnull'];
		trbasic('���벻��Ϊ��','fmdata[notnull]',$Value,'radio');
	}
	# ��֮��������е�Html����
    protected static function _fm_nohtml(){
		$Value = self::$isNew ? 0 : self::$oldField['nohtml'];
		trbasic('��������е�Html����','fmdata[nohtml]',$Value,'radio');
	}
	# ��֮��ʾ˵��
    protected static function _fm_guide(){
		$Value = self::$isNew ? '' : self::$oldField['guide'];
		trbasic('����ʾ˵��','fmdata[guide]',$Value,'text',array('guide'=>'������ʹ��'.htmlspecialchars('<br>'),'w'=>50,'validate'=>makesubmitstr('fmdata[guide]',0,0,0,80)));
	}
	# ��֮������
    protected static function _fm_regular(){
		$Value = self::$isNew ? '' : self::$oldField['regular'];
		trbasic('�����ʽ�������ִ�','fmdata[regular]',$Value);
	}
	# ��֮Ĭ������ֵ
    protected static function _fm_vdefault(){
		$Value = self::$isNew ? '' : str_replace(",",'[##]',self::$oldField['vdefault']);
		trbasic('Ĭ������ֵ','fmdata[vdefault]',$Value,'text',array('guide'=>'���Ĭ��ֵ��[##] (�������м�##) ����','w'=>50));
	}	
	# ��֮��������
    protected static function _fm_search(){
		if(in_array(self::$SourceType,array('channel','mchannel',)) && (self::$isNew || !empty(self::$oldField['iscustom']))){
			$issearcharr = array('0' => '����������','1' => '��ȷ����','2' => '��Χ����');
			$Value = self::$isNew ? 0 : self::$oldField['issearch'];
			trbasic('������������','',makeradio('fmdata[issearch]',$issearcharr,$Value),'');
		}
	}
	# ��֮Զ�����ط���
    protected static function _fm_rpid(){
		$rprojects = cls_cache::Read('rprojects');
		$rpidsarr = array('0' => '������Զ�̸���');
		foreach($rprojects as $k => $v) $rpidsarr[$k] = $v['cname'];
		
		$Value = self::$isNew ? 0 : self::$oldField['rpid'];
		trbasic('Զ�����ط���','fmdata[rpid]',makeoption($rpidsarr,$Value),'select');
	}
	# ��֮ͼƬ��ˮӡ
    protected static function _fm_wmid(){
		$watermarks = cls_cache::Read('watermarks');
		$wmidsarr = array('0' => 'ͼƬ����ˮӡ');
		foreach($watermarks as $k => $v) $wmidsarr[$k] = $v['cname'];
		
		$Value = self::$isNew ? 0 : self::$oldField['wmid'];
		trbasic('ͼƬ��ˮӡ','fmdata[wmid]',makeoption($wmidsarr,$Value),'select');
	}
    
	/**
     * ��֮ͼƬ�ϴ��Զ�ѹ��
     * 
     * @since nv50
     **/ 
    protected static function _fm_autoCompression()
    {
		$maxWidht = (self::$isNew || !isset(self::$oldField['auto_compression_width'])) ? 0 : (int)self::$oldField['auto_compression_width'];
		trbasic('�ϴ��Զ�ѹ�����','fmdata[auto_compression_width]',$maxWidht,'text',array('guide'=>'��λ��px ��Ĭ��0Ϊ��ѹ��������ͼƬ���ϴ�ʱ���ͼƬ�����ÿ�Ȼ�߶�ʱ��d�Զ��ȱ�ѹ���ɸÿ�Ȼ�߶ȴ�С��'));
	}
    
	# ��֮�Զ���������
    protected static function _fm_cfgs(){
		$Value = empty(self::$oldField['cfgs']) ? '' : var_export(self::$oldField['cfgs'],1);
		trbasic('�Զ����ò���','fmdata[cfgs]',$Value,'textarea',array('w' => 500,'h' => 100,'guide'=>'��array(\'xxx\' => \'yyy\',)��ʽ���룬ʹ���ֶ����û���$field[\'xxx\']�ɵ��ø�����'));
	}
	
	# ͨ���ֶ��޸�Ӧ�õ�����ģ��
    protected static function _fm_common_to_other(){
		if(in_array(self::$SourceType,array('mchannel')) && !empty(self::$oldField['iscommon']) && empty(self::$SourceID)){
			$mchids = array();
			self::$db->select('tpid')->from(self::_Table())->where(array('type' => 'm'))->_and('tpid<>0')->_and(array('ename' => self::$oldField['ename']))->exec();
			while($r = self::$db->fetch()) $mchids[] = $r['tpid'];
			
			$mchidsarr = array();
			$mchannels = cls_mchannel::InitialInfoArray();
			foreach($mchannels as $k => $v){
				if(in_array($k,$mchids)) $mchidsarr[$k] = $v['cname'];
			}
			if($mchidsarr){
				trbasic('�޸�Ӧ�õ�����ģ��', '', '<label for="all_mchids"><input class="checkbox" type="checkbox" id="all_mchids" onclick="checkall(this.form, \'fmdata[mchids]\', \'all_mchids\')">ȫѡ</label>&nbsp;  &nbsp;' . makecheckbox('fmdata[mchids][]', $mchidsarr), '', array('guide' => 'ֻ�����޸Ĺ�����Ŀ��δ�޸ĵ���Ŀ����ԭģ������'));
			}
		}
	}
	# ���еķָ�����
    protected static function _fm_separator($title = ''){
		tabfooter();
		tabheader($title);
	}
	# �����շ���������������͵�����δ����ͬ������������ô˷���
    protected static function _sv_custom_region(){
	}
	
	# ���洢֮ͨ�ò��ֵ����ݴ���
    private static function _sv_PreCommon(){
		
		# ���ı���
		self::$newField['cname'] = trim(strip_tags(self::$fmdata['cname']));
		if(empty(self::$newField['cname'])) cls_message::show('���������ı��⡣',M_REFERER);
		
		# Ӣ�ı�ʶ
		if(self::$isNew){
			self::$newField['ename'] = self::InitID(self::$fmdata['ename']);
			if($re = self::CheckNewID(self::$SourceType,self::$SourceID,self::$newField['ename'])) cls_message::show($re,M_REFERER);
		}else{
			self::$newField['ename'] = self::$oldField['ename'];
		}
		
		# �ֶ�����
		self::$newField['datatype'] = self::$datatype;
		self::$newField['type'] = self::FieldType(self::$SourceType);
		self::$newField['tpid'] = self::$SourceID;
		
		# �ڶ����ܱ༭������
		self::$newField['issystem'] = empty(self::$oldField['issystem']) ? 0 : 1;  # �Ƿ�ϵͳ�̶��ֶ�
		self::$newField['iscustom'] = self::$isNew || !empty(self::$oldField['iscustom']) ? 1 : 0; # �Ƿ��Զ����ֶ�
		
		# �Ƿ�ͨ���ֶ�/�����ֶ�
		if(self::$isNew){
			self::$newField['iscommon'] = empty(self::$fmdata['iscommon']) ? 0 : 1; # �ĵ�ģ�Ϳ���ѡ�������ֶλ���ģ���ֶ�
		}else{
			self::$newField['iscommon'] = self::$oldField['iscommon'];
		}
		# ���Ҫ�ڴ���iscommon����֮��
		self::$newField['tbl'] = empty(self::$oldField['tbl']) ? self::_ContentTable(self::$newField) : self::$oldField['tbl'];
		# ��ʾ˵��
		self::$newField['guide'] = empty(self::$fmdata['guide']) ? '' : trim(self::$fmdata['guide']);
		
		# �Զ���������
		self::$newField['cfgs'] = empty(self::$fmdata['cfgs']) ? '' : trim(self::$fmdata['cfgs']);
		self::$newField['cfgs'] = varexp2arr(self::$newField['cfgs']);
		
		# ������ȫ����:'vieworder','available','issystem','iscustom',
		self::$newField['vieworder'] = empty(self::$oldField['vieworder']) ? 0 : self::$oldField['vieworder'];
		self::$newField['available'] = self::$isNew || !empty(self::$oldField['available']) ? 1 : 0;
    }
	
	
	# �����ֶλ���
    protected static function _sv_finish(){
		adminlog((self::$isNew ? '���' : '�༭').self::_SourceVar(self::$SourceType,'Name').'�ֶ�');
		cls_message::show('�ֶ�'.(self::$isNew ? '���' : '�༭').'���',axaction(6,self::_RouteUrl('onefinish')));
    }
	
	# �����ֶε��������޸ģ����������ݱ�����Ӧ�ı��
    protected static function _sv_content_table(){
		try{
			self::$newField = self::AlterContentTableByConfig(self::$newField,self::$isNew,self::$oldField);
			return true;
		}catch (Exception $e){
			cls_message::show($e->getMessage());
		}
	}
	
	# һ���½���¼�ĳ�ʼ������
	private static function _OneBlankField(){
		$AfieldsColumns = self::_AfieldsColumns();
		$BlankInfo = array();
		foreach($AfieldsColumns as $var => $cfg){
			if(isset($cfg['Default'])){
				$BlankInfo[$var] = is_null($cfg['Default']) ? (preg_match("/int/i",$cfg['Type']) ? 0 : '') : $cfg['Default'];
			}else{
				$BlankInfo[$var] = preg_match("/int/i",$cfg['Type']) ? 0 : '';
			}
		}
		return $BlankInfo;
	}
	
	# ���������һ���ֶ����õ���ʼ����Դ
	# ע�⣺self::$newField��Ҫ�� (1)�����ṹ���� (2)����ǰ�ڴ��� (3)addslashesת�����
	private static function _SaveOneConfig(){
		if(empty(self::$SourceConfig) || empty(self::$newField)) return; # ��Ҫ����ǰ�ڴ���
		if(self::isTemplateConfig(self::$SourceType)){ # ����ģ�建��
		
			# ����cfgs������ṹ
			if(isset(self::$newField['cfgs']) && empty(self::$newField['cfgs'])){
				self::$newField['cfgs'] = '';
			}
			
			# self::$newFieldĬ��Ϊ�����룬addslashesת����ģ����ļ�ʱ��Ҫ ȥת��
			cls_Array::array_stripslashes(self::$newField);
					
			$CacheArray = self::InitialFieldArray(self::$SourceType,self::$SourceID);
			$CacheArray[self::$newField['ename']] = self::$newField;
			self::SaveInitialCache(self::$SourceType,self::$SourceID,$CacheArray);
			
		}else{ # �������ݿ�
			
			# ���⴦��cfgs
			if(isset(self::$newField['cfgs']) && is_array(self::$newField['cfgs'])){
				if(is_array(self::$newField['cfgs'])){
					self::$newField['cfgs'] = empty(self::$newField['cfgs']) ? '' : addslashes(var_export(self::$newField['cfgs'],TRUE));
				}else{
					self::$newField['cfgs'] = empty(self::$newField['cfgs']) ? '' : addslashes(self::$newField['cfgs']);
				}
			}
		
			if(self::$isNew){
				self::$db->insert(self::_Table(),self::$newField)->exec();
			}else{
				# ���µ�ǰ�ֶμ�¼
				self::$db->update(self::_Table(),self::$newField)
						 ->where(array('type' => self::$newField['type']))->_and(array('tpid' => self::$newField['tpid']))->_and(array('ename' => self::$newField['ename']))->exec();
			}
		}
	}
	# �ӳ�ʼ����Դɾ��һ���ֶ����ü�¼
	# ��������������ٽ��м�⼰�����ڴ���֮ǰ��Ҫ�ƿ�������Ϸ��ԡ�
	private static function _DelOneConfig($SourceType = 'channel',$SourceID = 0,$ename = ''){
		if(self::isTemplateConfig($SourceType)){
			$CacheArray = self::InitialFieldArray($SourceType,$SourceID);
			unset($CacheArray[$ename]);
			self::SaveInitialCache($SourceType,$SourceID,$CacheArray);
		}else{
			self::$db->delete(self::_Table())->where(array('type' => self::FieldType($SourceType)))->_and(array('tpid' => $SourceID))->_and(array('ename' => $ename))->exec();
		}
	}
	
	# ��Ա��ύ�ĵ����ֶα༭���д���
	# ����������͵�����δ����ͬ������������ô˷���
    protected static function _sv_field_config(){
		
		# ��Ϊ�漰�����浽���棬�����ܲ�ȫ���ݽṹ�������ݱ�ͬ��
		$AfieldsColumns = self::_AfieldsColumns();
		foreach($AfieldsColumns as $var => $cfg){
			if(!isset(self::$newField[$var])){ # �ų�֮ǰ�Ѿ������������
				if(isset(self::$fmdata[$var])){//�޸ĵ�ֵ
					if(preg_match("/int/i",$cfg['Type'])){
						self::$newField[$var] = (int)self::$fmdata[$var];
					}else{
						self::$newField[$var] = trim(self::$fmdata[$var]);
					}
				}elseif(isset(self::$oldField[$var])){//δ�޸ĵĲ�������ԭֵ
					self::$newField[$var] = maddslashes(self::$oldField[$var],true);
				}elseif(isset($cfg['Default'])){//��Ĭ��ֵ��ȫ
					self::$newField[$var] = is_null($cfg['Default']) ? (preg_match("/int/i",$cfg['Type']) ? 0 : '') : $cfg['Default'];
				}else{
					self::$newField[$var] = preg_match("/int/i",$cfg['Type']) ? 0 : '';
				}
			}
		}
		# ���ӻ��޸ĵ�ǰ�ֶε����ü�¼
		self::_SaveOneConfig();
		
		# ��Աģ�͵�ͨ���ֶεĸ�������
		self::_ModifyCommonFieldCopy(@self::$fmdata['mchids']);
		
		# ���µ�ǰ������ֶλ���
		self::UpdateCache(self::$SourceType,self::$SourceID);
	}
	
	# ͨ���ֶεĸ�������Ŀǰֻ�漰��Աģ�͵�ͨ���ֶ�
	# $SourceIDs���޸�ʱѡ��ͬ������Щģ�ͣ�����ʱǿ��������Ч����������ģ��
    protected static function _ModifyCommonFieldCopy($SourceIDs = array()){
		if(in_array(self::$SourceType,array('mchannel')) && empty(self::$SourceID)){
			$_field = self::$newField; # ��Ҫcopy��ͨ���ֶ�����
			$mchannels = cls_mchannel::InitialInfoArray();
			
			if(self::$isNew){ # ��Աģ�͵�ͨ���ֶ���Ҫÿ��ģ�͸��Ƴ�һ��afields�ֶμ�¼
				foreach($mchannels as $k => $v){
					$_field['tpid'] = $k;
					self::$db->insert(self::_Table(),$_field)->exec();
					self::UpdateCache(self::$SourceType,$k);
				}
			}else{ # ��Աģ�͵�ͨ���ֶ��޸�ͬ��������ģ�͵��ֶμ�¼
				if(empty($SourceIDs) || !is_array($SourceIDs)) return; //����ѡ��ͬ������Щģ��
				foreach(array('ename','type','tpid','vieworder','available',) as $var) unset($_field[$var]);
				foreach($mchannels as $k => $v){
					if(in_array($k,self::$fmdata['mchids'])){
						self::$db->update(self::_Table(),$_field)
								 ->where(array('type' => self::$newField['type']))->_and(array('tpid' => $k))->_and(array('ename' => self::$newField['ename']))->exec();
						self::UpdateCache(self::$SourceType,$k);
					}
				}
			}
		}
	}
	
	# ���»���(Ӧ�û��������Դ)�����������ͣ�ģ�������û��洫����������$CacheArray
	protected static function _SaveCache($SourceType = 'channel',$SourceID = 0,$CacheArray = '',$isInit = false){
		if(self::isTemplateConfig($SourceType)){
			if(is_array($CacheArray)){ # ���Դ������������
				cls_Array::_array_multisort($CacheArray);# ��vieworder��������
			}else{ # ������ȫ����Դ
				$CacheArray = self::InitialFieldArray($SourceType,$SourceID);
			}
			$CacheName = self::FieldCacheName($SourceType,$isInit); # ������������ Ӧ�û���/��ȫ����Դ
		}else{
			if(!$isInit){
				$CacheArray = self::InitialFieldArray($SourceType,$SourceID);
				$CacheName = self::FieldCacheName($SourceType,false);
			}else{ # ���ݿⱣ������Ҫ��������Դ
				return;
			}
		}
		
		# ע��������ȫ����Դ��Ӧ�û��治ͬ�ĵط�
		if(empty($isInit)){
			foreach($CacheArray as $k => &$v){
				if(empty($v['available'])) unset($CacheArray[$k]);
				cls_CacheFile::ArrayAction($v,'cfgs','extract');
			}
		}
		
		cls_CacheFile::Save($CacheArray,$CacheName.$SourceID,$CacheName,$isInit);
	}
	
	# ��ȡ�������͵Ķ������ü���������
    protected static function _SourceVar($Type = 'channel',$Key = ''){
		$SourceTypes = array(//�������������
			'channel' => array(
				'ID' => 'chid', # �������ϵ�ID������
				'Title' => 'cname', # �������ϵı��������
				'Class' => 'cls_channel', # ���������������������
				'Type' => 'a', # ���ֶμ�¼���е�����type
				'Name' => '�ĵ�ģ��', # ��������
				'RegAdd' => '|^ccid(.*?)', # ����ֶα�ʶʱ���ӵ��������
			),
			'mchannel' => array(
				'ID' => 'mchid',
				'Title' => 'cname',
				'Class' => 'cls_mchannel',
				'Type' => 'm',
				'Name' => '��Աģ��',
				'RegAdd' => '|^grouptype(.*?)|^currency(.*?)',
			),
			'fchannel' => array(
				'ID' => 'chid',
				'Title' => 'cname',
				'Class' => 'cls_fchannel',
				'Type' => 'f',
				'Name' => '����ģ��',
			),
			'catalog' => array( # ע�⣬��������Ϊcoid=0��һ����ϵ�������
				'ID' => 'caid',
				'Title' => 'title',
				'Class' => 'cls_catalog',
				'Type' => 'cn',
				'Name' => '��Ŀ',
			),
			'cotype' => array(
				'ID' => 'coid',
				'Title' => 'cname',
				'Class' => 'cls_cotype',
				'Type' => 'cn',
				'Name' => '��ϵ',
			),
			'commu' => array(
				'ID' => 'cuid',
				'Title' => 'cname',
				'Class' => 'cls_commu',
				'Type' => 'cu',
				'Name' => '������Ŀ',
			),
			'pusharea' => array(
				'ID' => 'paid',
				'Title' => 'cname',
				'Class' => 'cls_pusharea',
				'Type' => 'pa',
				'Name' => '����λ',
			),
		);
		$re = isset($SourceTypes[$Type]) ? $SourceTypes[$Type] : array();
		if($Key) $re = isset($re[$Key]) ? $re[$Key] : '';
		return $re;
	
	}
	
	# ����������ID���г�ʼ��ʽ��
    protected static function SourceInitID($SourceType = 'channel',$SourceID = 0){
		$SourceID = trim($SourceID);
		if(in_array($SourceType,array('pusharea'))){
			$SourceID = cls_string::ParamFormat($SourceID);
		}else{
			$SourceID = (int)$SourceID;
		}
		return $SourceID;
	}
	
	# �õ�afields�����ݱ��ֶνṹ(�����ֶ���������)
    protected static function _AfieldsColumns(){
		$Columns = cls_DbOther::ColumnNames(self::$Table,true);
		unset($Columns['fid']); # �ų�������ID
		return $Columns;
	}
	
	
}
