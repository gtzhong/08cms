<?PHP
/**
* [�ؼ����б�] ��ǩ������
* ��cls_Parse����ģ����Ʋ���ֱ�ӽӴ���ǩ������
*/

defined('M_COM') || exit('No Permission');
abstract class cls_Tag_KeywordBase extends cls_TagParse{
	
	# �������ݽ��
	protected function TagReSult(){
		$ReturnArray = array();
		$uwordlinks = cls_cache::Read('uwordlinks');
		if(empty($uwordlinks)) $this->TagThrowException("���������Źؼ���");
		$TempArray = @array_slice($uwordlinks['swords'],$this->TagInitStart(),$this->TagInitLimits(),TRUE);
		foreach($TempArray as $k =>$v){
			$ReturnArray[] = array('word' => $v,'wordlink' => $uwordlinks['rwords'][$k]);
		}
		return $ReturnArray;
	
	}
	
	
}
