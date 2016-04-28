<?PHP
/**
* [�����б�] ��ǩ������
* ��cls_Parse����ģ����Ʋ���ֱ�ӽӴ���ǩ������
*/

defined('M_COM') || exit('No Permission');
abstract class cls_Tag_PushsBase extends cls_TagParse{
	
	# �������ݽ��
	protected function TagReSult(){
		return $this->TagResultBySql();
	}
	
	# ���ؽ���еĵ�����¼�Ĵ���
	protected function TagOneRecord($OneRecord){
		$OneRecord = cls_pusher::ViewOneInfo($OneRecord);
		return $OneRecord;
	}
		
	# ȡ��Ĭ�ϵ������ִ�
	protected function TagDefaultOrderStr(){
		return " ORDER BY trueorder,pushid DESC";
	}
	
	# ���ݱ�ǩ����ƴ��sqlstr���õ�SQL����Ҫ����(select��from��where)
	protected function CreateTagSqlBaseStr(){
		
		if(empty($this->tag['paid'])) $this->TagThrowException("��Ҫָ������λ");
		
		# ����֮ǰpaid������ID�ı�ǩ����ʱ����
		if(is_numeric($this->tag['paid'])){
			$this->tag['paid'] = 'push_'.$this->tag['paid'];
		}
		
		if(!($pusharea = cls_PushArea::Config($this->tag['paid']))) $this->TagThrowException("ָ������λ������");

		$sqlselect = "SELECT *";
		$sqlfrom = " FROM ".self::$tblprefix.cls_PushArea::ContentTable($this->tag['paid'])." FORCE INDEX (trueorder)"; # ǿ��ʹ��trueorder����
		$sqlwhere = $this->TagHandWherestr();
		$sqlwhere = $sqlwhere ? " AND $sqlwhere" : '';
		$sqlwhere .= " AND checked=1 AND (startdate<'".self::$timestamp."' AND (enddate=0 OR enddate>'".self::$timestamp."'))";
		//������������
		for($i = 1;$i < 3;$i ++){
			if($classid = max(0,intval(@$this->tag["classid$i"]))){
				$sqlwhere .= " AND classid$i='$classid'";
			}
		}
		$sqlwhere = $sqlwhere ? ' WHERE '.substr($sqlwhere,5) : '';
		$sqlstr = $sqlselect.$sqlfrom.$sqlwhere;
		return $sqlstr;
		
	}	
}
