<?PHP
defined('M_COM') || exit('No Permission');
class tpl_lpfangyanges_html extends cls_Parse{
	
	# ��ʼ����չ����������������������չ����ɾ��
	protected function __construct($ParseInitConfig = array()){
		parent::__construct($ParseInitConfig);
		// ��������ĵ�aid�д����Ĳ���
		$arr = array(4,5,6,12,'shi');
		foreach($arr as $k){
			$key = is_numeric($k) ? "ccid$k" : $k;
			$this->_Set("_da.$key",cls_env::GetG($key)); 
		}
	}
}
