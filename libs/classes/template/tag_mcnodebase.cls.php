<?PHP
/**
* [������Ա�ڵ�] ��ǩ������
* ��cls_Parse����ģ����Ʋ���ֱ�ӽӴ���ǩ������
*/

defined('M_COM') || exit('No Permission');
abstract class cls_Tag_McnodeBase extends cls_TagParse{
	
	# �������ݽ��
	protected function TagReSult(){
		if(empty($this->tag['cnsource']) || empty($this->tag['cnid'])) return array();
		$cnstr = $this->tag['cnsource'].'='.$this->tag['cnid'];
		$ReturnArray = cls_node::m_cnparse($cnstr);
		$ReturnArray += cls_node::mcnodearr($cnstr);
		return $ReturnArray;
	}
	
}
