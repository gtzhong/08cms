<?php
/**
 * �ĵ�����ҳ��Ĵ������
 *
 */
defined('M_COM') || exit('No Permission');
abstract class cls_MsearchPageBase extends cls_FrontPage{
	
  	protected static $_ExtendAplicationClass = 'cls_MsearchPage'; 	# ��ǰ�������չӦ����(������)������
	protected $_WhereArray = array();								# WHERE�������
	protected $_SQLArray = array();									# ���в�ѯ�ִ�
	protected $_Channel = array();									# ��ǰ�ĵ�ģ��
	protected $_Fields = array();									# ��ǰ�ֶλ���
	protected $_FieldSearched = array();							# �ݴ��Ѵ�����������ֶ����������ظ�
	
	# ҳ�����ɵ��ⲿִ�����
	# ����������ο� function _Init
	public static function Create($Params = array()){
		return self::_iCreate(self::$_ExtendAplicationClass,$Params);
	}

	protected function __construct(){
		parent::__construct();
 		$this->_PageCacheParams['typeid'] = 6; 			# ҳ�滺������
		$this->_Cfg['search_repeat'] = cls_env::mconfig('search_repeat');
		$this->_Cfg['search_pmid'] = cls_env::mconfig('search_pmid');
		$this->_Cfg['LoadAdv'] = true;										# �Ƿ���Ҫ���ɹ��js���ô���
	}
	
	# Ӧ��ʵ���Ļ�����ʼ��
	protected function _Init($Params = array()){
	}
	
	# ��ͬ����ҳ�������ģ��
	protected function _ModelCumstom(){
		$this->_CheckSearchPermission();
		$this->_ReadChannel();
		$this->_InitSQL();
		$this->_GroupType();
		$this->_Mname();
		$this->_InOutDays();
		$this->_FieldSearch();
		$this->_OrderStr();
		$this->_WhereStr();
	}
		
	# ��ʼ��ģ��
	protected function _ReadChannel(){
		$this->_SystemParams['mchid'] = max(0, intval(@$this->_QueryParams['mchid']));
		if($this->_SystemParams['mchid'] && !($this->_Channel = cls_mchannel::Config($this->_SystemParams['mchid']))){
			$this->_SystemParams['mchid'] = 0;
		}
		
		if($this->_SystemParams['mchid']){
			$this->_SystemParams['mchannel'] = $this->_Channel['cname'];
			$this->_Fields = cls_cache::Read('mfields',$this->_SystemParams['mchid']);
		}else{
			$this->_SystemParams['mchannel'] = '';
			$this->_Fields = cls_cache::Read('mfields',0);
		}
	}
	
	# ��ʼ�����е�SQL���
	protected function _InitSQL(){
		$this->_SQLArray['selectstr'] = "SELECT m.*,s.*";
		$this->_SQLArray['fromstr'] = "FROM ".self::$tblprefix."members AS m INNER JOIN ".self::$tblprefix."members_sub AS s ON (s.mid=m.mid)";
		$this->_SQLArray['wherestr'] = '';
		$this->_SQLArray['orderstr'] = '';
		$this->_WhereArray['checked'] = 'm.checked=1';
		
		if($this->_SystemParams['mchid']){ # ģ�ͱ���
			$this->_SQLArray['selectstr'] .= ",c.*";
			$this->_SQLArray['fromstr'] .= " INNER JOIN ".self::$tblprefix."members_{$this->_SystemParams['mchid']} AS c ON (c.mid=m.mid)";
			$this->_WhereArray['mchid'] = "m.mchid='{$this->_SystemParams['mchid']}'";
		}else{
			if($this->_SystemParams['nochids'] = empty($this->_QueryParams['nochids']) ? '' : trim($this->_QueryParams['nochids'])){ # �ų���ģ��
				$this->_WhereArray['nochids'] = "m.mchid ".multi_str(explode(',',$this->_SystemParams['nochids']),1);
			}
		}
	}
	
	# ��ϵ����
	protected function _GroupType(){
		$grouptypes = cls_cache::Read('grouptypes');
		foreach($grouptypes as $k => $v){
			if(!$v['issystem']){
				if($this->_SystemParams["grouptype$k"] = max(0,intval(@$this->_QueryParams["grouptype$k"]))){
					$_WhereArray["grouptype$k"] = "m.grouptype$k = '".$this->_SystemParams["grouptype$k"]."'";
				}
			}
		}
	}
	
	# �ʺŴ���
	protected function _Mname(){
		if($this->_SystemParams['mname'] = empty($this->_QueryParams['mname']) ? '' : trim($this->_QueryParams['mname'])){
			$_WhereArray['mname'] = "m.mname ".sqlkw($this->_SystemParams['mname']);
			$this->_FieldSearched[] = 'mname';
		}
	}
	
	# ʱ�䴦��
	protected function _InOutDays(){
		if($this->_SystemParams['indays'] = max(0,intval(@$this->_QueryParams['indays']))){
			$this->_WhereArray['indays'] = "m.regdate>'".(self::$timestamp - 86400 * $this->_SystemParams['indays'])."'";
		}
		if($this->_SystemParams['outdays'] = max(0,intval(@$this->_QueryParams['outdays']))){
			$this->_WhereArray['indays'] = "m.regdate<'".(self::$timestamp - 86400 * $this->_SystemParams['indays'])."'";
		}
	}
	
	# �ֶε�ɸѡ����
	protected function _FieldSearch(){
		$a_field = new cls_field;
		foreach($this->_Fields as $k => $v){
			if(!$v['issystem'] && $v['issearch'] && !in_array($k,$this->_FieldSearched)){
				$a_field->init($v);
				$a_field->deal_search($a_field->field['tbl'] == 'members_sub' ? 's.' : 'c.');
				if($a_field->searchstr){
					$this->_WhereArray[$k] = $a_field->searchstr;
				}
			}
		}
		unset($a_field);
	}
	
	# ������
	protected function _OrderStr(){
		foreach(array('','1','2') as $k){
			$_var_orderby = "orderby$k";
			$_var_mode = "ordermode$k";
			
			$this->_SystemParams[$_var_orderby] = empty($this->_QueryParams[$_var_orderby]) ? '' : cls_string::ParamFormat($this->_QueryParams[$_var_orderby]);
			if(!$this->_SystemParams[$_var_orderby] && !$k) $this->_SystemParams[$_var_orderby] = 'mid';
			if($this->_SystemParams[$_var_orderby]){
				$this->_SystemParams[$_var_mode] = empty($this->_QueryParams[$_var_mode]) ? 0 : 1;
				$this->_SQLArray['orderstr'] .= ($this->_SQLArray['orderstr'] ? ',' : '').'m.'.$this->_SystemParams[$_var_orderby].($this->_SystemParams[$_var_mode] ? ' ASC' : ' DESC');
			}
		}
	}

	# ��ѯ��wherestr����
	protected function _WhereStr(){
		
		$this->_SQLArray['wherestr'] = '';
		foreach($this->_WhereArray as $k => $v){
			$this->_SQLArray['wherestr'] .= ' AND '.$v;
		}
		if($this->_SQLArray['wherestr']){
			$this->_SQLArray['wherestr'] = 'WHERE '.substr($this->_SQLArray['wherestr'],5);
		}
		$this->_SQLArray['wherearr'] = $this->_WhereArray;# Ϊ�˼���֮ǰ��ģ�壬��ʱ����֮ǰ������wherearr
	}


	# ��ǰҳ�������Ϣ
	protected function _Mdebug(){
		$_mdebug = cls_env::GetG('_mdebug');
		$_mdebug->setvar('tpl',$this->_ParseSource);
	}
	
	# ���ҳ��ģ��
	protected function _ParseSource(){
		if($this->_SystemParams['mchid']){
			$this->_ParseSource = cls_tpl::CommonTplname('member',$this->_SystemParams['mchid'],'srhtpl'.($this->_SystemParams['addno'] ? $this->_SystemParams['addno'] : ''));
		}else{
			$this->_ParseSource = cls_tpl::SpecialTplname('msearch');
		}
		if(!$this->_ParseSource){
			throw new cls_PageException($this->_PageName().' - δ��ģ��');
		}
	}
	
	# ����ҳ��Ŵ���
	protected function _Addno(){
		$this->_SystemParams['addno'] = max(0,intval(@$this->_QueryParams['addno']));
		if($this->_SystemParams['addno'] > 1){
			throw new cls_PageException($this->_PageName()." - ������ĸ���ҳ");
		}
	}
	
	# ҳ�泣��������������ڸ��ӱ���������������ҳUrl�Ƿ���Ҫ��̬���Ƿ���Ҫ����������¼
	protected function _NormalVars(){
		if($this->_inStatic) return;
		$this->_NormalVars = array('page',);
	}
	
	# ƴ��Url���Ӳ����ִ�(ҳ�泣������ų�����)
	# Ϊ�˼�������ϵͳģ�壬searchҳ��filterstrͷ�ַ��ǲ��ܴ�&��
	protected function _Filterstr(){
		parent::_Filterstr();
		$this->_SystemParams['filterstr'] = substr($this->_SystemParams['filterstr'],1);
	}
	
	# ���������ݺϲ����������ݣ�������ش���
	# Ϊ�˼��ݵ�ǰ��һ�����������($_da)
	protected function _MainDataCombo(){
		parent::_MainDataCombo();
		$this->_MainData += $this->_SQLArray; # SQL����Ҫ�ų�XSS����֮��
	}
	
	# ȡ�÷�ҳUrl���ø�ʽ
	protected function _UrlPre($isStatic = false){
		if($isStatic){
			return '';
		}else{
			$ParamStr = $this->_SystemParams['filterstr'] ? "&{$this->_SystemParams['filterstr']}" : '';# Ϊ�˼�������ϵͳģ�壬filterstrͷ�ַ��ǲ��ܴ�&��
			$ParamStr .= "&page={\$page}";
			$ParamStr = substr($ParamStr ,1);
			
			$re = "search.php?".$ParamStr;
			$re = cls_env::mconfig('memberurl').$re;
			$re = cls_url::view_url($re);
			
		}
		if(!$re) throw new cls_PageException($this->_PageName().' - '.($isStatic ? '��̬' : '��̬').'URL��ʽ����');
		return $re;
	}
	
	# ����ToolJs�Ĳ������飬ֻ��page=1ʱ����
	protected function _ToolParams(){
		if($this->_SystemParams['page'] == 1){
			$_ToolParams = array();
		}
		return @$_ToolParams;
	}
	
	# ҳ������
	protected function _PageName(){
		return "��ԱƵ��[{$this->_SystemParams['mchannel']}]����ҳ";
	}
	
}
