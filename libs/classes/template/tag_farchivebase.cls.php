<?PHP
/**
* [��������] ��ǩ������
* ��cls_Parse����ģ����Ʋ���ֱ�ӽӴ���ǩ������
*/

defined('M_COM') || exit('No Permission');
abstract class cls_Tag_FarchiveBase extends cls_TagParse{
	
	# �������ݽ��
	protected function TagReSult(){
		
		$ReturnArray = array();
		$Nowid = intval(empty($tag['id']) ? cls_Parse::Get('_a.aid') : $tag['id']);
		if(!$Nowid) return $ReturnArray;
		
		//��Ҫ����Ϊ��Ч��Ϣ
		$arc = new cls_farcedit;
		if(!$arc->set_aid($Nowid,0,intval(@$tag['ttl']))) return $ReturnArray;
		if(($arc->archive['startdate'] > self::$timestamp) || ($arc->archive['enddate'] && $arc->archive['enddate'] < self::$timestamp)) return $ReturnArray;
		$arc->archive['arcurl'] = cls_url::view_farcurl($arc->aid,$arc->archive['arcurl']);
		$ReturnArray = $arc->archive;
		unset($arc);
		
		return $ReturnArray;
	}
	
}
