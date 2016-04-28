<?PHP
/**
* [ʱ������] ��ǩ������
* ��cls_Parse����ģ����Ʋ���ֱ�ӽӴ���ǩ������
*/

defined('M_COM') || exit('No Permission');
abstract class cls_Tag_DateBase extends cls_TagParse{
	
	# �������ݽ��
	protected function TagReSult(){
		if(!($datetime = @$this->tag['tname']) || !($datetime = intval($datetime))) return '';
		$formatstr = '';
		!empty($this->tag['date']) && $formatstr .= $this->tag['date'];
		!empty($this->tag['time']) && $formatstr .= ($formatstr ? ' ' : '').$this->tag['time'];
		if($formatstr) $datetime = @date($formatstr,$datetime);
		return $datetime;
	}
}
