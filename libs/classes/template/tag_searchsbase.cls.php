<?PHP
/**
* [�ĵ������б�] ��ǩ������
* ��cls_Parse����ģ����Ʋ���ֱ�ӽӴ���ǩ������
*/

defined('M_COM') || exit('No Permission');
abstract class cls_Tag_SearchsBase extends cls_TagParse{
	
	# �������ݽ��
	protected function TagReSult(){
		return $this->TagResultBySql();
	}
	
	# ���ؽ���еĵ�����¼�Ĵ���
	protected function TagOneRecord($OneRecord){
		$OneRecord['nodemode'] = defined('IN_MOBILE');//�����ֻ����־?????????????????
		cls_ArcMain::Parse($OneRecord,TRUE);
		return $OneRecord;
	}
		
	# ȡ��Ĭ�ϵ������ִ�
	protected function TagDefaultOrderStr(){
		return " ORDER BY a.aid DESC";
	}
	
	# ���ݱ�ǩ����ƴ��sqlstr���õ�SQL����Ҫ����(select��from��where)
	# ������ǩ��������ǩ�Ĳ�ѯ��䴦��ͬ
	protected function CreateTagSqlBaseStr(){
		$sqlstr = $this->TagHandWherestr(); # �ֶ�����sqlstr
		if(!$sqlstr){
			$sqlstr = cls_Parse::Get('_da.selectstr').' '.cls_Parse::Get('_da.fromstr').' '.cls_Parse::Get('_da.wherestr');
			if(!empty($tag['validperiod'])) $sqlstr .= " AND (a.enddate=0 OR a.enddate>'$timestamp')";
			if(!empty($tag['letter']) && cls_Parse::Get('_da.letter')) $sqlstr .= " AND a.letter='".cls_Parse::Get('_da.letter')."'";
		}
		return $sqlstr;
	}
	
	# ������ǩ�������ִ��Ĳ����
	protected function TagCustomOrderStr($OrderStr){
		if($Return = cls_Parse::Get('_da.orderstr')){
			$OrderStr .= ' '.$Return;
		}
		return $OrderStr;
	}
	
	
}
