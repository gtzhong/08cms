<?PHP
/**
* [��Ա�����б�] ��ǩ������
* ��cls_Parse����ģ����Ʋ���ֱ�ӽӴ���ǩ������
*/

defined('M_COM') || exit('No Permission');
abstract class cls_Tag_MsearchsBase extends cls_TagParse{
	
	# �������ݽ��
	protected function TagReSult(){
		return $this->TagResultBySql();
	}
	
	# ���ؽ���еĵ�����¼�Ĵ���
	protected function TagOneRecord($OneRecord){
		cls_UserMain::Parse($OneRecord,true);
		return $OneRecord;
	}
		
	# ȡ��Ĭ�ϵ������ִ�
	protected function TagDefaultOrderStr(){
		return " ORDER BY m.mid DESC";
	}
	
	# ���ݱ�ǩ����ƴ��sqlstr���õ�SQL����Ҫ����(select��from��where)
	# ������ǩ��������ǩ�Ĳ�ѯ��䴦��ͬ
	protected function CreateTagSqlBaseStr(){
		$sqlstr = $this->TagHandWherestr(); # �ֶ�����sqlstr
		if(!$sqlstr){
			$sqlstr = cls_Parse::Get('_da.selectstr').' '.cls_Parse::Get('_da.fromstr').' '.cls_Parse::Get('_da.wherestr');
			if(!empty($tag['letter']) && cls_Parse::Get('_da.letter')) $sqlstr .= " AND m.letter='".cls_Parse::Get('_da.letter')."'";
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
