<?PHP
/**
* [�Զ������б�] ��ǩ������
* ��cls_Parse����ģ����Ʋ���ֱ�ӽӴ���ǩ������
*/

defined('M_COM') || exit('No Permission');
abstract class cls_Tag_FunctionsBase extends cls_TagParse{
	
	# �������ݽ��
	protected function TagReSult(){
		$ReturnArray = array();
		if(@$this->tag['func']){
			$ReturnArray = EvalFuncInTag($this->tag['func']);
			if(is_array($ReturnArray)){
				foreach($ReturnArray as $k => $v){
					$ReturnArray[$k]['sn_row'] = $i = empty($i) ? 1 : ++ $i;
				}
			}
		}
		return $ReturnArray ? $ReturnArray : array();
	}
	
	# ��ҳ����self::$_mp['acount']�Ȳ�ͬ���ͱ�ǩ�Ĳ��컯����
	protected function TagCustomMpInfo(){
		$Return = EvalFuncInTag(@$this->tag['mpfunc']);
		self::$_mp['acount'] = (int)$Return;
	}
	
	
}
