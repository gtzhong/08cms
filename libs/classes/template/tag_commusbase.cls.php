<?PHP
/**
* [���������б�] ��ǩ������
* ��cls_Parse����ģ����Ʋ���ֱ�ӽӴ���ǩ������
*/

defined('M_COM') || exit('No Permission');
abstract class cls_Tag_CommusBase extends cls_TagParse{
	
	# �������ݽ��
	protected function TagReSult(){
		return $this->TagResultBySql();
	}
	
	# ȡ��Ĭ�ϵ������ִ�
	protected function TagDefaultOrderStr(){
		return "  ORDER BY c.cid DESC";
	}
	
	# ���ݱ�ǩ����ƴ��sqlstr���õ�SQL����Ҫ����(select��from��where)
	protected function CreateTagSqlBaseStr(){
		if(!($commu = cls_commu::Config(@$this->tag['cuid'])) || !$commu['tbl']) $this->TagThrowException("��Ҫָ����ȷ�Ľ�����Ŀ");
		
		$sqlselect = "SELECT c.*";
		$sqlfrom = " FROM ".self::$tblprefix."$commu[tbl] c".$this->ForceIndexSql('c');
		$sqlwhere = $this->TagHandWherestr();
		$sqlwhere = $sqlwhere ? " AND $sqlwhere" : '';
		if(!empty($this->tag['checked'])) $sqlwhere .= " AND c.checked <>'0'";
		$sqlwhere = $sqlwhere ? ' WHERE '.substr($sqlwhere,5) : '';
		$sqlstr = $sqlselect.$sqlfrom.$sqlwhere;
		return $sqlstr;
	}
	
}
