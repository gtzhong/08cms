<?PHP
/**
* [�ֶα���ֵ] ��ǩ������
* ��cls_Parse����ģ����Ʋ���ֱ�ӽӴ���ǩ������
*/

defined('M_COM') || exit('No Permission');
abstract class cls_Tag_FieldBase extends cls_TagParse{
	
	# �������ݽ��
	protected function TagReSult(){
		$val = @$this->tag['tname'];
		$typearr = array(
			'archive' => array('','chid'),
			'member' => array('m','mchid'),
			'farchive' => array('f','chid'),
			'catalog' => array('cn',0),
			'coclass' => array('cn','coid'),
			'commu' => array('cu','cuid'),
			'push' => array('pa','paid'), 
		);
		if(!($type = @$typearr[$this->tag['type']]) || (!$fields = cls_cache::Read($type[0].'fields',$type[1] ? cls_Parse::Get('_a.'.$type[1]) : '')) || !($field = @$fields[$this->tag['fname']])){
			return $val;
		}
		return view_field_title($val,$field,@$this->tag['limits']);
		
	}
	
}
