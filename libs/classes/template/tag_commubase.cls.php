<?PHP
/**
* [��������] ��ǩ������
* ��cls_Parse����ģ����Ʋ���ֱ�ӽӴ���ǩ������
*/

defined('M_COM') || exit('No Permission');
abstract class cls_Tag_CommuBase extends cls_TagParse{
	
	# �������ݽ��
	protected function TagReSult(){
		$NowID = $this->tag['id'] ? $this->tag['id'] : cls_Parse::Get('_a.aid');
		$NowID = (int)$NowID;
		if(!$NowID || !($commu = cls_commu::Config(@$this->tag['cuid'])) || !$commu['tbl']) $this->TagThrowException("��Ҫָ����ȷ�Ľ�����Ŀ");
		$ReturnArray = self::$db->fetch_one("SELECT * FROM ".self::$tblprefix."$commu[tbl] WHERE cid='$NowID'",intval(@$this->tag['ttl']));
		return $ReturnArray;
	}
}
