<?PHP
/**
* [��֤����] ��ǩ������
* ��cls_Parse����ģ����Ʋ���ֱ�ӽӴ���ǩ������
*/

defined('M_COM') || exit('No Permission');
abstract class cls_Tag_RegcodeBase extends cls_TagParse{
	
	
	# �������ݽ��
	protected function TagReSult(){
		if(empty($this->tag['type'])) return 0;
		return @in_array($this->tag['type'],explode(',',cls_env::mconfig('cms_regcode'))) ? 1 : 0;
	}
}
