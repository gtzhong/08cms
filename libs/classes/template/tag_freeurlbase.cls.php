<?PHP
/**
* [����ҳURL] ��ǩ������
* ��cls_Parse����ģ����Ʋ���ֱ�ӽӴ���ǩ������
*/

defined('M_COM') || exit('No Permission');
abstract class cls_Tag_FreeurlBase extends cls_TagParse{

	# �������ݽ��
	protected function TagReSult(){
		return cls_FreeInfo::Url(@$this->tag['fid']);
	}
}
