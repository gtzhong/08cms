<?php
/*
** �б�������Ŀ�����ݴ�����
** ���е�������Ԫ�����ݵ�Ԫ
*/
!defined('M_COM') && exit('No Permisson');
class cls_arcsetsbase{
	protected $mc = 0;//��Ա����
	public $A = array();//��ʼ��������š���chid��pre(����ǰ׺),tbl(����),stid(����id)
	public $cfgs = array();//����������
	
    function __construct($cfg = array()){
		$this->mc = defined('M_ADMIN') ? 0 : 1;
		$this->A = $cfg;
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
		if(!isset($this->cfgs[$key]) || empty($data['aid'])) return false;
		$cfg = $this->cfgs[$key];
		$value = $this->format_value($value,empty($cfg['type']) ? 'int+' : $cfg['type']);
		if(!isset($data[$key]) || stripslashes($value) == $data[$key]) return false;//���ֵδ�Ķ�����������
		
		if(!empty($cfgs['sql'])){
			$sql = key_replace($cfgs['sql'],array('value' => $value,'key' => $key) + $data);
		}else $sql = "UPDATE {$tblprefix}{$this->A['tbl']} SET $key='$value' WHERE aid='{$data['aid']}'";
		$db->query($sql);
	}
	
	protected function user_inorder($key,$value,$data = array()){
		//sql���Զ���sql������ʹ��{value},{key}����$data�е�������Ϊռλ��
		global $db,$tblprefix;
		if(!isset($this->cfgs[$key]) || empty($data['aid'])) return;
		if($this->A['isab'] != 1) return;
		
		$cfg = $this->cfgs[$key];
		$value = $this->format_value($value,'int+');
		if(!isset($data[$key]) || stripslashes($value) == $data[$key]) return;//���ֵδ�Ķ�����������
		
		if(!empty($cfgs['sql'])){
			$sql = key_replace($cfgs['sql'],array('value' => $value,'key' => $key) + $data);
		}else{
			if(empty($this->A['abtbl'])){
				$sql = "UPDATE {$tblprefix}{$this->tbl} SET inorder$arid='$value' WHERE aid='{$data['aid']}' LIMIT 1";
			}else{
				$sql = "UPDATE {$tblprefix}{$this->A['abtbl']} SET inorder='$value' WHERE inid='{$data['aid']}' AND arid='{$this->A['arid']}' AND pid='{$this->A['pid']}' LIMIT 1";
			}
		}
		$db->query($sql);
	}
	
	protected function user_incheck($key,$value,$data = array()){
		//sql���Զ���sql������ʹ��{value},{key}����$data�е�������Ϊռλ��
		global $db,$tblprefix;
		if(!isset($this->cfgs[$key]) || empty($data['aid'])) return;
		if($this->A['isab'] != 1) return;
		
		$cfg = $this->cfgs[$key];
		$value = $this->format_value($value,'bool');
		if(!isset($data[$key]) || stripslashes($value) == $data[$key]) return;//���ֵδ�Ķ�����������
		
		if(!empty($cfgs['sql'])){
			$sql = key_replace($cfgs['sql'],array('value' => $value,'key' => $key) + $data);
		}else{
			if(empty($this->A['abtbl'])){
				$sql = "UPDATE {$tblprefix}{$this->tbl} SET incheck$arid='$value' WHERE aid='{$data['aid']}' LIMIT 1";
			}else{
				$sql = "UPDATE {$tblprefix}{$this->A['abtbl']} SET incheck='$value' WHERE inid='{$data['aid']}' AND arid='{$this->A['arid']}' AND pid='{$this->A['pid']}' LIMIT 1";
			}
		}
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
