<?php
/**
 * �����б�����е�������������	 
 */
!defined('M_COM') && exit('No Permisson');
class cls_cuopsbase extends cls_cubasic{

	public $A = array();//��ʼ��������š���chid��pre(����ǰ׺),tbl(����),stid(����id)
	public $cfgs = array();//��Ŀ����
	public $actcu	= NULL;//���������е�ָ��������ֻ�����ݱ���ʱʹ��
	public $mchannel = array();//��ǰģ��

	public $recnt = array();//ͳ�Ƹ�����,������ʾ,readd,valid
	public $cnt_msgs = array();//����ֱ����ʾ�ַ�������ʾ,readd,valid,refresh
	
    function __construct($cfg = array()){
		parent::__construct($cfg);
		$this->mc = defined('M_ADMIN') ? 0 : 1;
		$this->A = $cfg;
	}
	
	/**
	* �������������Ŀ
	*
	* @ex $oL->o_additem('validperiod',array('value' => 30));;
	*
	* @param    string     $key  ��Ŀ�ؼ��� �����Լ�������Ŀ��Ҳ����������ֵ �ȵ�
						delete��ɾ����������
						delbad��ɾ��(�۷�)
						check����˽�������
						uncheck������˲���
	* @param    array      $cfg  ��Ŀ���ò��� ��ѡ��Ĭ��Ϊ�� 
						type����������֮�󣬻�����function type_{type}()������
	//��ȫ���Ʒ�����user_$key����Ϊ����ʹ�õķ���
	//ϵͳ���÷�����type_$key������������µ��ã�������type��δ���Ƶ���ϵ������δָ����������ʾ��
	*/
	function additem($key,$cfg = array()){
		//title����Ŀ����
		//bool���Ƿ�ѡ�ȡֵ��0��1
		//guide����ʾ˵���������ڶ�ռһ�е���Ŀ
		//w�������ı���Ŀ��
		$this->cfgs[$key] = $cfg;
		return $this->one_item($key,0);
	}

	//�������������ʾhtml
	public function view_one_push($key){
		if(!isset($this->cfgs[$key]) || @$this->cfgs[$key]['bool'] != 2) return '';
		$re = $this->view_one($key);
		$this->del_item($key);
		return $re;		
	}	

	//���ص�ѡ�����ʾhtml
	function view_one_bool($key){
		if(!isset($this->cfgs[$key]) || @$this->cfgs[$key]['bool'] != 1) return '';
		$re = $this->view_one($key);
		$this->del_item($key);
		return $re;		
	}	
	
	//��ʾ���еĲ�����
	function view_one_row($key){
		if(!isset($this->cfgs[$key]) || !empty($this->cfgs[$key]['bool'])) return false;
		$re = $this->view_one($key);//ֱ����ʾ
		$this->del_item($key);
		return $re;		
	}
	
	//�����
	function save_one($key){
		if(!isset($this->cfgs[$key])) return false;
		return $this->one_item($key,2);
	}
	
	protected function call_method($func,$args = array()){
		if(method_exists($this,$func)){
			return call_user_func_array(array(&$this,$func),$args);
		}else return 'undefined';
	}
	
	//���������Ҫ��0����ʼ�� 1����ʾ 2�����ݴ���
	protected function one_item($key,$mode = 0){
		$re = $this->call_method("user_$key",array($mode));//���Ʒ���
		if($re == 'undefined'){
			if('ugid' == substr($key,0,4)){
				$re = $this->type_ugid($key,$mode);
			}elseif('push' == substr($key,0,4)){
				$re = $this->type_push($key,$mode);
			}
		}
		return $re;
	}
	
	//������ʾ
	protected function view_one($key){
		$re = $this->one_item($key,1);
		if($re == 'undefined') $re = '';
		return $re ? $re : '';
	}
	
	//�Ƿ�һ����ѡ��Ĳ�����Ŀ��ֻ�����ύ֮����ж�
	protected function isSelectedItem($key = ''){
		$fmdata = &$GLOBALS[$this->A['ofm']];
		return empty($fmdata[$key]) ? false : true;
	}
	
	//���һ��������
	protected function del_item($key){
		unset($this->cfgs[$key]);
		return false;
	}
	
	protected function input_checkbox($key = '',$title = '',$ischeck = 0,$addstr = ''){
		//ischeck��1-��ѡ��Ŀ��checkbox��0-������Ŀ��checkbox
		$re = '';
		if(!$key || !$title) return $re;
		if(!$ischeck) $re .= "{$title} &nbsp;";
		$re .= "<input class=\"checkbox\" type=\"checkbox\" id=\"{$this->A['ofm']}[$key]\" name=\"{$this->A['ofm']}[$key]\" value=\"1\" $addstr>";
		if($ischeck) $re .= "<label for=\"{$this->A['ofm']}[$key]\">{$title}</label> &nbsp;";
		return $re;
	}
	
	//����
	protected function type_push($key,$mode = 0){
		if(!cls_PushArea::Config($key)) return $this->del_item($key);
		$cfg = &$this->cfgs[$key];
		if(!$mode){//��ʼ��
			$cfg['bool'] = 2;
		}elseif($mode == 1){//��ʾ
			if(empty($cfg['title'])) $cfg['title'] = cls_pusher::AllTitle($key,1,1);
			return $this->input_checkbox($key,$cfg['title'],1);
		}elseif($mode == 2){//����
			cls_commu::push($this->cuid,$this->actcu['cid'],$key);
		}
	}
	
	//exkey: ����ͬʱɾ���ظ�,ͬʱɾ��exkey=$cid
	protected function user_delete($mode = 0){
		$key = substr(__FUNCTION__,5);
		if(!$this->mc && !allow_op('mdel')) return $this->del_item($key);
		$cfg = &$this->cfgs[$key];
		if(!$mode){//��ʼ��
			$cfg['bool'] = 1;
		}elseif($mode == 1){//��ʾ
			if(empty($cfg['title'])) $cfg['title'] = 'ɾ��';
			return $this->input_checkbox($key,$cfg['title'],1,'onclick="deltip()"');
		}elseif($mode == 2){//����
			$this->delete($this->actcu['cid'],@$cfg['exkey']);
		}
	}	
	
	//exkey: ����ͬʱɾ���ظ�,ͬʱɾ��exkey=$cid
	protected function user_delbad($mode = 0){
		$key = substr(__FUNCTION__,5);
		if(!$this->mc && !allow_op('adel')) return $this->del_item($key);
		$cfg = &$this->cfgs[$key];
		if(!$mode){//��ʼ��
			$cfg['bool'] = 1;
		}elseif($mode == 1){//��ʾ
			if(empty($cfg['title'])) $cfg['title'] = 'ɾ��(�ۻ���)'; 
			return $this->input_checkbox($key,$cfg['title'],1,"onclick=\"deltip()\"");
		}elseif($mode == 2){//����
			$this->setCrids('dec', $this->actcu['mid']); //�ۻ���
			$this->delete($this->actcu['cid'],@$cfg['exkey']);
		}
	}
	
	protected function user_check($mode = 0){
		$key = substr(__FUNCTION__,5);
		//if(!$this->mc && !allow_op('mcheck')) return $this->del_item($key);
		$cfg = &$this->cfgs[$key];
		if(!$mode){//��ʼ��
			$cfg['bool'] = 1;
		}elseif($mode == 1){//��ʾ
			if(empty($cfg['title'])) $cfg['title'] = '���';
			return $this->input_checkbox($key,$cfg['title'],1);
		}elseif($mode == 2){//����
			$this->db->update($this->table(), array('checked' => '1'))->where('cid='.$this->actcu['cid'])->exec();
		}
	}	
	protected function user_uncheck($mode = 0){
		$key = substr(__FUNCTION__,5);
		//if(!$this->mc && !allow_op('mcheck')) return $this->del_item($key);
		$cfg = &$this->cfgs[$key];
		if(!$mode){//��ʼ��
			$cfg['bool'] = 1;
		}elseif($mode == 1){//��ʾ
			if(empty($cfg['title'])) $cfg['title'] = '����';
			return $this->input_checkbox($key,$cfg['title'],1);
		}elseif($mode == 2){//����
			if($this->isSelectedItem('check')) return false;//����checkͬʱִ��
			$this->db->update($this->table(), array('checked' => '0'))->where('cid='.$this->actcu['cid'])->exec();
		}
	}
		
}

