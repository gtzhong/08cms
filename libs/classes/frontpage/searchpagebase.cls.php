<?php
/**
 * �ĵ�����ҳ��Ĵ������
 *
 */
defined('M_COM') || exit('No Permission');
abstract class cls_SearchPageBase extends cls_FrontPage{
	
  	protected static $_ExtendAplicationClass = 'cls_SearchPage'; 	# ��ǰ�������չӦ����(������)������
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
 		$this->_PageCacheParams['typeid'] = 4; 			# ҳ�滺������
		$this->_Cfg['search_repeat'] = cls_env::mconfig('search_repeat');
		$this->_Cfg['search_pmid'] = cls_env::mconfig('search_pmid');
		$this->_Cfg['LoadAdv'] = true;										# �Ƿ���Ҫ���ɹ��js���ô���
	}
	
	# Ӧ��ʵ���Ļ�����ʼ��
	protected function _Init($Params = array()){
		$this->_inMobile = defined('IN_MOBILE'); #  ����ҳ��֧�־�̬
	}
	
	# ��ͬ����ҳ�������ģ��
	protected function _ModelCumstom(){
		$this->_CheckSearchPermission();
		$this->_ReadChannel();
		$this->_InitSQL();
		$this->_Caid();
		$this->_Ccid();
		$this->_InOutDays();
		$this->_SearchWord();
		$this->_FieldSearch();
		$this->_OrderStr();
		$this->_WhereStr();
	}

	# ��ʼ��ģ��
	protected function _ReadChannel(){
		$this->_SystemParams['chid'] = max(0, intval(@$this->_QueryParams['chid']));
		if(!$this->_SystemParams['chid'] || !($this->_Channel = cls_channel::Config($this->_SystemParams['chid']))){
			throw new cls_PageException('��ָ����Ҫ�������ĵ�ģ��');
		}
		$this->_SystemParams['channel'] = $this->_Channel['cname'];
		$this->_Fields = cls_cache::Read('fields',$this->_SystemParams['chid']);
	}
	
	# ��ʼ�����е�SQL���
	protected function _InitSQL(){
		$this->_SQLArray['selectstr'] = "SELECT a.*,c.*";
		$this->_SQLArray['fromstr'] = "FROM ".self::$tblprefix.atbl($this->_SystemParams['chid'])." AS a INNER JOIN ".self::$tblprefix."archives_{$this->_SystemParams['chid']} AS c ON (a.aid=c.aid)";
		$this->_SQLArray['wherestr'] = '';
		$this->_SQLArray['orderstr'] = '';
		$this->_WhereArray['checked'] = 'a.checked=1';
	}

	# ��Ŀ����
	protected function _Caid(){
		if($this->_SystemParams['caid'] = max(0,intval(@$this->_QueryParams['caid']))){
			if($catalog = cls_cache::Read('catalog',$this->_SystemParams['caid'])){
				$this->_SystemParams['catalog'] = $catalog['title'];
				if($cnsql = cnsql(0,sonbycoid($this->_SystemParams['caid']),'a.')){
					$this->_WhereArray['caid'] = $cnsql;
				}
			}else $this->_SystemParams['caid'] = 0;
		}
		unset($catalog);
	}

	# ��Ŀ����
	protected function _Ccid(){
		$cotypes = cls_cache::Read('cotypes');
		foreach($cotypes as $k => $v){
			if($this->_SystemParams["ccid$k"] = max(0,intval(@$this->_QueryParams["ccid$k"]))){
				if($coclass = cls_cache::Read('coclass',$k,$this->_SystemParams["ccid$k"])){
					$this->_SystemParams["ccid{$k}title"] = $coclass['title'];
					if($cnsql = cnsql($k,sonbycoid($this->_SystemParams["ccid$k"],$k),'a.')){ 
						$this->_WhereArray["ccid$k"] = $cnsql;
					}
				}else $this->_SystemParams["ccid$k"] = 0;
			}
		}
		unset($coclass);
	}
	
	# ʱ�䴦��
	protected function _InOutDays(){
		if($this->_SystemParams['indays'] = max(0,intval(@$this->_QueryParams['indays']))){
			$this->_WhereArray['indays'] = "a.createdate>'".(self::$timestamp - 86400 * $this->_SystemParams['indays'])."'";
		}
		if($this->_SystemParams['outdays'] = max(0,intval(@$this->_QueryParams['outdays']))){
			$this->_WhereArray['indays'] = "a.createdate<'".(self::$timestamp - 86400 * $this->_SystemParams['indays'])."'";
		}
	}
	# �ؼ�����������
	protected function _SearchWord(){
		$this->_SystemParams['searchmode'] = empty($this->_QueryParams['searchmode']) ? array('subject') : explode(',',trim($this->_QueryParams['searchmode']));
		$this->_FieldSearched = array(); # �ѽ��йؼ����������ֶΣ���������ظ�������Щ�ֶΡ�
		if($this->_SystemParams['searchword'] = cls_string::CutStr(trim(@$this->_QueryParams['searchword']),50,'')){
			
			# ����ȫ�����������ִ���ˣ��򲻽��������ֶεĹؼ�������
			if(in_array('fulltxt',$this->_SystemParams['searchmode'])){		
				if($this->Channel['fulltxt'] && isset($this->_Fields[$this->Channel['fulltxt']])) $fulltxt = $this->Channel['fulltxt'];
				if(!empty($fulltxt)){
					$this->_SystemParams['searchmode'] = array('fulltxt');	
					# �����fulltxt��øĳ�search��ʾΪ '�ؼ�������' ������sqlstr�������漰��ģ�壬����ͳһ�޸�
					$this->_WhereArray['fulltxt'] = ($this->_Fields[$fulltxt]['tbl'] == "archives_{$this->_SystemParams['chid']}" ? 'c.' : 'a.')."$fulltxt ".sqlkw($this->_SystemParams['searchword']);
					$this->_FieldSearched[] = $fulltxt;
				}else{ # ����ȫ��������ѡ��ȡ��
					$key = array_search('fulltxt',$this->_SystemParams['searchmode']);
					unset($this->_SystemParams['searchmode'][$key]);
				}
			}
			# �������ֶεĹؼ�������(����ȫ������)
			if(!in_array('fulltxt',$this->_SystemParams['searchmode'])){
				$_where_array = array();
				foreach($this->_SystemParams['searchmode'] as $k => $v){
					if(in_array($v,array('subject','keywords')) || !empty($this->_Fields[$v]['issearch'])){
						$_where_array[] = ($this->_Fields[$v]['tbl'] == "archives_{$this->_SystemParams['chid']}" ? 'c.' : 'a.')."$v ".sqlkw($this->_SystemParams['searchword']);
						$this->_FieldSearched[] = $v;
					}else unset($this->_SystemParams['searchmode'][$k]);
				}
				if($_where_array){
					# �����fulltxt��øĳ�search��ʾΪ '�ؼ�������' ������sqlstr�������漰��ģ�壬����ͳһ�޸�
					$this->_WhereArray['fulltxt'] = "(".implode(' OR ',$_where_array).")";
				}
				unset($_where_array);
			}
		}
	}
	
	# �ֶε�ɸѡ����
	protected function _FieldSearch(){
		$a_field = new cls_field;
		foreach($this->_Fields as $k => $v){
			if($v['issearch'] && !in_array($k,$this->_FieldSearched)){
				$a_field->init($v);
				$a_field->deal_search($a_field->field['tbl'] == "archives_{$this->_SystemParams['chid']}" ? "c." : "a.");
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
			if(!$this->_SystemParams[$_var_orderby] && !$k) $this->_SystemParams[$_var_orderby] = 'aid';
			if($this->_SystemParams[$_var_orderby]){
				$this->_SystemParams[$_var_mode] = empty($this->_QueryParams[$_var_mode]) ? 0 : 1;
				$this->_SQLArray['orderstr'] .= ($this->_SQLArray['orderstr'] ? ',' : '').'a.'.$this->_SystemParams[$_var_orderby].($this->_SystemParams[$_var_mode] ? ' ASC' : ' DESC');
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
		$this->_ParseSource = cls_tpl::SearchTplname(
			array(
				'chid' => $this->_SystemParams['chid'],
				'caid' => $this->_SystemParams['caid'],
				'addno' => $this->_SystemParams['addno'],
				'nodemode' => defined('IN_MOBILE'),
			)
		);
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
	
	
	# ȡ�÷�ҳUrl���ø�ʽ
	protected function _UrlPre($isStatic = false){
		if($isStatic){
			return '';
		}else{
			$ParamStr = $this->_SystemParams['filterstr'] ? "&{$this->_SystemParams['filterstr']}" : '';# Ϊ�˼�������ϵͳģ�壬filterstrͷ�ַ��ǲ��ܴ�&��
			$ParamStr .= "&page={\$page}";
			$ParamStr = substr($ParamStr ,1);
			
			$re = ($this->_inMobile ? cls_env::mconfig('mobiledir').'/' : '')."search.php?".$ParamStr;
			$re = cls_url::view_url($re);
		}
		if(!$re) throw new cls_PageException($this->_PageName().' - '.($isStatic ? '��̬' : '��̬').'URL��ʽ����');
		return $re;
	}
	
	# ���������ݺϲ����������ݣ�������ش���
	# Ϊ�˼��ݵ�ǰ��һ�����������($_da)
	protected function _MainDataCombo(){
		parent::_MainDataCombo();
		$this->_MainData += $this->_SQLArray; # SQL����Ҫ�ų�XSS����֮��
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
		return "[{$this->_SystemParams['channel']}]����ҳ";
	}
	
}
