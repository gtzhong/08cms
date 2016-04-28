<?PHP
/**
* [��Ƭ����] ��ǩ������
* ��cls_Parse����ģ����Ʋ���ֱ�ӽӴ���ǩ������
*/

defined('M_COM') || exit('No Permission');
abstract class cls_Tag_FragmentBase extends cls_TagParse{
	
	
	# �������ݽ��
	protected function TagReSult(){
		
		if(empty($this->tag['url'])) return '';
		$ttl = empty($this->tag['ttl']) ? 0 : max(0,intval($this->tag['ttl']));
		$ExCacheKey = md5('fragment'.$this->tag['url']);
		$re = GetExtendCache($ExCacheKey,$ttl);
		if($re === false){
			$re = html_get_contents($this->tag['url'],empty($this->tag['timeout']) ? 2 : $this->tag['timeout']);
			SetExtendCache($ExCacheKey,$re,$ttl);
		}
		return $re;
		
	}
	
}
