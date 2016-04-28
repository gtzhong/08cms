<?PHP
/**
* [�����б�] ��ǩ������
* ��cls_Parse����ģ����Ʋ���ֱ�ӽӴ���ǩ������
*/

defined('M_COM') || exit('No Permission');
abstract class cls_Tag_FarchivesBase extends cls_TagParse{
		
	# �������ݽ��
	protected function TagReSult(){
		return $this->TagResultBySql();
	}
	
	# ���ؽ���еĵ�����¼�Ĵ���
	protected function TagOneRecord($OneRecord){
		cls_url::arr_tag2atm($OneRecord,'f');
		$OneRecord['arcurl'] = cls_url::view_farcurl($OneRecord['aid'],$OneRecord['arcurl']);
		return $OneRecord;
	}
		
	# ȡ��Ĭ�ϵ������ִ�
	protected function TagDefaultOrderStr(){
		return " ORDER BY a.aid DESC";
	}
	
	# ���ݱ�ǩ����ƴ��sqlstr���õ�SQL����Ҫ����(select��from��where)
	protected function CreateTagSqlBaseStr(){
		if(empty($this->tag['casource'])) $this->TagThrowException("��Ҫָ����������");
		# ��ʱ�Է��������ID��һ�����ݴ���(���ݺ���������ģ���ǩδ����ӦID�滻�����)
		if(is_numeric($this->tag['casource'])) $this->tag['casource'] = 'fcatalog'.$this->tag['casource'];
		$this->tag['casource'] = cls_fcatalog::InitID($this->tag['casource']);
		if(!($chid = cls_fcatalog::Config($this->tag['casource'],'chid'))) $this->TagThrowException("ָ���˴���ĸ���ģ��");
		
		$sqlselect = "SELECT a.*,c.*";
		$sqlfrom = " FROM ".self::$tblprefix."farchives a".$this->ForceIndexSql('a')." INNER JOIN ".self::$tblprefix."farchives_$chid c".$this->ForceIndexSql('c')." ON c.aid=a.aid";
		$sqlwhere = $this->TagHandWherestr();
		$sqlwhere = $sqlwhere ? " AND $sqlwhere" : '';
		$sqlwhere .= " AND a.fcaid='{$this->tag['casource']}'";
		$sqlwhere .= " AND a.checked=1";
		if(!empty($this->tag['ids'])) $sqlwhere .= cls_DbOther::str_fromids($this->tag['ids'],'a.aid');
		if(!empty($this->tag['validperiod'])) $sqlwhere .= " AND (a.startdate<'".self::$timestamp."' AND (a.enddate=0 OR a.enddate>'".self::$timestamp."'))";
		$sqlwhere = $sqlwhere ? ' WHERE '.substr($sqlwhere,5) : '';
		$sqlstr = $sqlselect.$sqlfrom.$sqlwhere;
		return $sqlstr;
	}	
}
