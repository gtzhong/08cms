<?PHP
/**
* [���ɵ����б�] ��ǩ������
* ��cls_Parse����ģ����Ʋ���ֱ�ӽӴ���ǩ������
*/

defined('M_COM') || exit('No Permission');
abstract class cls_Tag_OutinfosBase extends cls_TagParse{
	
	protected $TagSourceDB = NULL;				# �ⲿ����Դ����������
	
	# �������ݽ��
	protected function TagReSult(){
		
		$ReturnArray = array();
		if(empty($this->tag['wherestr'])) $this->TagThrowException("��Ҫ�ֶ������ѯ���wherestr");
		
		$sqlstr = $this->tag['wherestr'];
		$sqlstr .= $this->iTagLimitStr();
		
		$ReturnArray = $this->TagSourceDB->ex_fetch_array($sqlstr,intval(@$this->tag['ttl']));
		foreach($ReturnArray as $k => $v){
			$v['sn_row'] = $k + 1;
			$ReturnArray[$k] = $v;
		}
		return $ReturnArray;
		
	}
	
	# ��ʼ����ǰ��ǩ
	# ��ȷ�����ݿ�����
	protected function _TagInit(){
		if(empty($this->tag['dsid'])){
			$this->TagSourceDB = self::$db;
		}else{
			$dbsources = cls_cache::Read('dbsources');
			if(empty($dbsources[$this->tag['dsid']])) $this->TagThrowException("ָ�����ⲿ����Դdsid������");
			
			$dbsource = $dbsources[$this->tag['dsid']];
			if($dbsource['dbpw']) $dbsource['dbpw'] = authcode($dbsource['dbpw'],'DECODE',md5(cls_env::mconfig('authkey')));
			if(empty($dbsource['dbhost']) || empty($dbsource['dbuser']) || empty($dbsource['dbname'])) $this->TagThrowException("�ⲿ����Դdsid�����ϲ���ȫ");
			
			$this->TagSourceDB = & _08_factory::getDBO( 
				array('dbhost' => $dbsource['dbhost'], 'dbuser' => $dbsource['dbuser'], 'dbpw' => $dbsource['dbpw'], 
					  'dbname' => $dbsource['dbname'], 'pconnect' => 0, 'dbcharset' => $dbsource['dbcharset'])
			);
			
			if(!$this->TagSourceDB->link) $this->TagThrowException("�ⲿ����Դ�޷�����");
		}
	}
		
		
	function outinfos_nums(){
		
	}
		
	# ��ҳ����self::$_mp['acount']�Ȳ�ͬ���ͱ�ǩ�Ĳ��컯����
	protected function TagCustomMpInfo(){
		
		if(empty($this->tag['wherestr'])) $this->TagThrowException("��Ҫ�ֶ������ѯ���wherestr");
		$Return = $this->TagSourceDB->result_one($this->SqlStrTransToCount($this->tag['wherestr']),intval(@$this->tag['ttl']));
		self::$_mp['acount'] = (int)$Return;
	}
}
