<?PHP
/**
* [��Ա�ڵ��б�] ��ǩ������
* ��cls_Parse����ģ����Ʋ���ֱ�ӽӴ���ǩ������
*/

defined('M_COM') || exit('No Permission');
abstract class cls_Tag_MccatalogsBase extends cls_TagParse{
	
	protected $_ListCoid = 0;		# ����ǩ���б���ϵID���������Ŀ����Ϊ0
	protected $_ListOldKey = '';	# ԭ���ж�����ID�ı���������Ŀ(caid)����ϵ(ccid)
	protected $_ListNewKey = '';	# ����ֵ�е�ID�ı���������Ŀ(caid)����ϵ(ccid*)
	
	# �������ݽ��
	protected function TagReSult(){
		return $this->TagResultBySql();
	}
	
	# ��ʼ����ǰ��ǩ
	protected function _TagInit(){
		$this->_ListCoid = $this->tag['listby'] == 'ca' ? 0 :  intval(str_replace('co','',$this->tag['listby']));
		$this->_ListOldKey = $this->_ListCoid ? 'ccid' : 'caid';
		$this->_ListNewKey = $this->_ListCoid ? 'ccid'.$this->_ListCoid : 'caid';
#		$this->_cotypes= cls_cache::Read('cotypes');
	}
	
	# ���ؽ���еĵ�����¼�Ĵ���
	protected function TagOneRecord($OneRecord){
		if($this->_ListCoid) $OneRecord[$this->_ListNewKey] = $OneRecord[$this->_ListOldKey];
		$OneRecord = array_merge($OneRecord,cls_node::mcnodearr($this->_ListNewKey.'='.$OneRecord[$this->_ListOldKey]));
		return $OneRecord;
	}
	
	# ȡ��Ĭ�ϵ������ִ�
	protected function TagDefaultOrderStr(){
		return ' ORDER BY trueorder ASC';
	}
	
	# ���ݱ�ǩ����ƴ��sqlstr���õ�SQL����Ҫ����(select��from��where)
	protected function CreateTagSqlBaseStr(){
		
		$sqlselect = "SELECT *";
		$sqlfrom = " FROM ".self::$tblprefix.($this->_ListCoid ? "coclass{$this->_ListCoid}" : 'catalogs');
		$sqlwhere = $this->TagHandWherestr();
		$sqlwhere = $sqlwhere ? " AND $sqlwhere" : '';

		$TagOption = @$this->tag[$this->_ListCoid ? "cosource{$this->_ListCoid}" : 'casource'];
		if(empty($TagOption)){
			$sqlwhere .= " AND level=0";
		}elseif($TagOption == 1){
			if($ids = array_filter(explode(',',@$this->tag[$this->_ListCoid ? 'ccids'.$this->_ListCoid : 'caids']))){
				$sqlwhere .= ' AND '.$this->_ListOldKey.multi_str($ids);
			}else $this->TagThrowException("���ֶ��趨ID");
		}elseif($TagOption == 2){//������Ŀ������Ŀ
			if($ActiveID = (int)cls_Parse::Get('_a.'.$this->_ListOldKey)){
				$sqlwhere .= " AND pid=$ActiveID";
			}else $this->TagThrowException("�޷��õ�����ID");
		}elseif($TagOption == 4){
			$sqlwhere .= " AND level=1";
		}elseif($TagOption == 5){
			$sqlwhere .= " AND level=2";
		}
		$sqlwhere = ' WHERE '.substr($sqlwhere.' AND closed=0',5);
		$sqlstr = $sqlselect.$sqlfrom.$sqlwhere;
		return $sqlstr;
	}
}
