<?PHP
defined('M_COM') || exit('No Permission');
class tpl_xiaoquloupang_html extends cls_Parse{
	
	# ��ʼ����չ����������������������չ����ɾ��
	protected function __construct($ParseInitConfig = array()){
		parent::__construct($ParseInitConfig);
		// �������Ŀ����ĸӰ��
		$letter = cls_env::GetG('letter');
		$this->_Set("_da.letter",$letter); 
	}
}
