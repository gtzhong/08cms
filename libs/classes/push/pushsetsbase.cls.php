<?php
/*
** �б�������Ŀ�����ݴ�����
** ���е�������Ԫ�����ݵ�Ԫ
*/
!defined('M_COM') && exit('No Permisson');
class cls_pushsetsbase{
	protected $mc = 0;//��Ա����
	public $A = array();//��ʼ��������š���paid��tbl(����)��
	public $cfgs = array();//����������
	
    function __construct($cfg = array()){
		$this->mc = defined('M_ADMIN') ? 0 : 1;
		$this->A = $cfg;
		if(empty($this->A['paid'])) exit('��ָ������λ');
	}
	
	public function additem($key = '',$cfg = array()){
		if(!$key) return;
		$this->cfgs[$key] = $cfg;
	}
	
	//ָ���ĵ���ָ������������ݴ���
	public function set_one($key,$value,$data = array()){
		$args = func_get_args();
		$re = $this->call_method("user_$key",$args);//���Ʒ���
		if($re == 'undefined'){
			$re = $this->com_method($key,$value,$data);//ͨ�÷���
		}
		return $re;
	}
	protected function call_method($func,$args = array()){
		if(method_exists($this,$func)){
			return call_user_func_array(array(&$this,$func),$args);
		}else return 'undefined';
	}
	
	protected function com_method($key,$value,$data = array()){
		//ͨ�÷���������������keyΪ���ݱ��ֶ������ĵ�����
		//type�����ݸ�ʽ
		//sql���Զ���sql������ʹ��{value},{key}����$data�е�������Ϊռλ��
		
		global $db,$tblprefix;
		if(!isset($this->cfgs[$key]) || empty($data['pushid'])) return false;
		$cfg = $this->cfgs[$key];
		$value = $this->format_value($value,empty($cfg['type']) ? 'int+' : $cfg['type']);
		if(!isset($data[$key]) || stripslashes($value) == $data[$key]) return false;//���ֵδ�Ķ�����������
		
		if(!empty($cfgs['sql'])){
			$sql = key_replace($cfgs['sql'],array('value' => $value,'key' => $key) + $data);
		}else $sql = "UPDATE {$tblprefix}{$this->A['tbl']} SET $key='$value' WHERE pushid='{$data['pushid']}'";
		$db->query($sql);
	}
	
	protected function user_vieworder($key,$value,$data = array()){
		global $db,$tblprefix;
		if(!isset($this->cfgs[$key]) || empty($data['pushid'])) return;
		$cfg = $this->cfgs[$key];
		$value = cls_pusher::orderformat($value,$this->A['paid'],'vieworder');
		if(!isset($data[$key]) || stripslashes($value) == $data[$key]) return false;//���ֵδ�Ķ�����������
		$sql = "UPDATE {$tblprefix}{$this->A['tbl']} SET vieworder='$value' WHERE pushid='{$data['pushid']}' LIMIT 1";
		$db->query($sql);
	}
	
	protected function user_fixedorder($key,$value,$data = array()){
		global $db,$tblprefix;
		if(!isset($this->cfgs[$key])) return;
		$cfg = $this->cfgs[$key];
		$value = cls_pusher::orderformat($value,$this->A['paid'],'fixedorder');
		if(!isset($data[$key]) || stripslashes($value) == $data[$key]) return false;//���ֵδ�Ķ�����������
		$sql = "UPDATE {$tblprefix}{$this->A['tbl']} SET fixedorder='$value' WHERE pushid='{$data['pushid']}' LIMIT 1";
		$db->query($sql);
	}
	
	protected function format_value($value,$type = ''){
		$type || $type = 'int+';
		switch($type){
			case 'int+':
				$value = max(0,intval($value));
				break;
			case 'int':
				$value = intval($value);
				break;
			case 'bool':
				$value = empty($value) ? 0 : 1;
				break;
			case 'str':
				$value = trim(strip_tags($value));
				break;
			case 'date':
				$value = $value ? strtotime($value) : 0;
				break;
		}
		return $value;
	}
	
}
