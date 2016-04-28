<?php
/**
 *  arcopsbase.cls.php  �ĵ��б�����е�������������	 
 *
 *
 * @copyright			(C) 2008-2013 08cms
 * @license				http://www.08cms.com/license/
 * @lastmodify			2013-2-23
 */
!defined('M_COM') && exit('No Permisson');
class cls_arcopsbase{
	protected $mc = 0;//��Ա����
	public $A = array();//��ʼ��������š���chid��pre(����ǰ׺),tbl(����),stid(����id)
	public $cfgs = array();//��Ŀ����
	public $arc	= NULL;//���������е�ָ���ĵ���ֻ�����ݱ���ʱʹ��
	public $channel = array();//��ǰģ��
	public $recnt = array();//ͳ�Ƹ�����,������ʾ,readd,valid
	
    function __construct($cfg = array()){
		$this->mc = defined('M_ADMIN') ? 0 : 1;
		$this->A = $cfg;	
		if(empty($this->A['chid']) || !($this->channel = cls_channel::Config($this->A['chid']))) exit('��ָ���ĵ�����');
	}
	
	
	/**
	* �������������Ŀ
	*
	* @ex $oL->o_additem('validperiod',array('value' => 30));;
	*
	* @param    string     $key  ��Ŀ�ؼ��� �����Լ�������Ŀ��Ҳ����������ֵ �ȵ�
						delete��ɾ������
						delbad��ɾ�����ۻ��֣�����
						check����˲���
						uncheck������˲���
						readd���ط�������
						autoletter���Զ�����ĸ
						autoabstract���Զ�ժҪ
						autothumb���Զ�����ͼ
						autokeyword���Զ��ؼ���
						static�����ָ�ʽ��̬
						nstatic�����¸�ʽ��̬
						caid��������Ŀ����
						ccid$k ������ϵ
						vieworder �������
						validperiod ��Ч�ڲ��� 
	* @param    array      $cfg  ��Ŀ���ò��� ��ѡ��Ĭ��Ϊ�� 
						type����������֮�󣬻�����function type_{type}()������,date,bool,url,select,checkbox,input����
						
	
	//��ȫ���Ʒ�����user_$key����Ϊ����ʹ�õķ���
	//ϵͳ���÷�����type_$key������������µ��ã�������type��δ���Ƶ���ϵ������δָ����������ʾ��
	*/
	
	public function additem($key,$cfg = array()){
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
	public function view_one_bool($key){
		if(!isset($this->cfgs[$key]) || @$this->cfgs[$key]['bool'] != 1) return '';
		$re = $this->view_one($key);
		$this->del_item($key);
		return $re;		
	}	
	
	//��ʾ���еĲ�����
	public function view_one_row($key){
		if(!isset($this->cfgs[$key]) || !empty($this->cfgs[$key]['bool'])) return false;
		$re = $this->view_one($key);//ֱ����ʾ
		$this->del_item($key);
		return $re;		
	}
	
	//�����
	public function save_one($key){
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
			if('ccid' == substr($key,0,4)){
				$re = $this->type_ccid($key,$mode);
			}elseif('push_' == substr($key,0,5)){
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
	
	//�Ƿ�һ���Ѷ���Ĳ�����Ŀ
	protected function is_item($key = ''){
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
		if(!$ischeck) $re .= "{$title} ";
		$re .= "<input class=\"checkbox\" type=\"checkbox\" id=\"{$this->A['ofm']}[$key]\" name=\"{$this->A['ofm']}[$key]\" value=\"1\" $addstr>";
		if($ischeck) $re .= "<label for=\"{$this->A['ofm']}[$key]\">{$title}</label> ";
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
			$this->arc->push($key);
		}
	}
	
	protected function type_ccid($key,$mode = 0){
		$cotypes = cls_cache::Read('cotypes');
		if(!($coid = max(0,intval(str_replace('ccid','',$key)))) || empty($cotypes[$coid]) || !in_array($coid,$this->A['coids']) || $cotypes[$coid]['self_reg']) return $this->del_item($key);
		$cfg = &$this->cfgs[$key];
		if(!$mode){//��ʼ��
		}elseif($mode == 1){//��ʾ
			$v = $cotypes[$coid];
			if(empty($cfg['title'])) $cfg['title'] = "����{$v['cname']}";
			isset($v['asmode']) ||  $v['asmode']='';
			isset($v['emode']) || $v['emode']='';
			$na = array('coid' => $coid,'chid' => $this->A['chid'],'max' => $v['asmode'],'emode' => $v['emode'],'evarname' => "{$this->A['opre']}{$key}date",);
			foreach($cfg as $k => $v){//���±�������ͨ��$cfg����
				if(in_array($k,array('chid','addstr','max','emode','ids','guide',))) $na[$k] = $v;
			}
			$na['addstr'] = '-ȡ��-';
			tr_cns($this->input_checkbox($key,$cfg['title']),"{$this->A['opre']}$key",$na,1);
		}elseif($mode == 2){//����
			if(!isset($cfg['limit'])||empty($GLOBALS[$this->A['opre'].$key])){
				$this->arc->set_ccid(@$GLOBALS["mode_".$this->A['opre'].$key],$GLOBALS[$this->A['opre'].$key],$coid,@$GLOBALS[$this->A['opre'].$key.'date']);
			}else{ //��ϵ�޶����
				$do = 1; 
				if(!isset($this->recnt['reccids'][$coid])){
					$this->recnt['reccids'][$coid]['title'] = $cfg['title'];
					$this->recnt['reccids'][$coid]['do'] = 0; //������
					$this->recnt['reccids'][$coid]['skip'] = 0; //������
				}
				if($this->recnt['reccids'][$coid]['do']>=$cfg['limit']){ //��������
					$this->recnt['reccids'][$coid]['skip']++; 
					return false;
				}
				if($do){
					$this->recnt['reccids'][$coid]['do']++;
					$this->arc->set_ccid(@$GLOBALS["mode_".$this->A['opre'].$key],$GLOBALS[$this->A['opre'].$key],$coid,@$GLOBALS[$this->A['opre'].$key.'date']);
				}
			}
		}
	}
	
	protected function user_caid($mode = 0){
		$key = substr(__FUNCTION__,5);
		$cfg = &$this->cfgs[$key];
		if(!$mode){//��ʼ��
		}elseif($mode == 1){//��ʾ
			if(empty($cfg['title'])) $cfg['title'] = '������Ŀ';
			$na = array('coid' => 0,'chid' => $this->A['chid'],);
			foreach($cfg as $k => $v){//���±�������ͨ��$cfg����
				if(in_array($k,array('chid','addstr','ids','guide',))) $na[$k] = $v;
			}
			tr_cns($this->input_checkbox($key,$cfg['title']),"{$this->A['opre']}$key",$na);
		}elseif($mode == 2){//����
			$this->arc->arc_caid($GLOBALS[$this->A['opre'].$key]);
		}
	}
	
	protected function user_validperiod($mode = 0){
		//value����ʼ��������
		//ͨ��ֻ�ڹ����̨ʹ�������������Ӧ��Ա���ĵ��ϼ����¼�
		$key = substr(__FUNCTION__,5);
		$cfg = &$this->cfgs[$key];
		if(!$mode){//��ʼ��
		}elseif($mode == 1){//��ʾ
			if(empty($cfg['title'])) $cfg['title'] = '������Ч��(��)';
			if(empty($cfg['guide'])) $cfg['guide'] = '��������Ч����(��30)��0����ʱʧЧ��-1��������Ч';
			$na = array('w' => 5);
			foreach($cfg as $k => $v){//���±�������ͨ��$cfg����
				if(in_array($k,array('guide','w',))) $na[$k] = $v;
			}
			trbasic($this->input_checkbox($key,$cfg['title']),"{$this->A['opre']}$key",empty($cfg['value']) ? '' : $cfg['value'],'text',$na);
		}elseif($mode == 2){//����
			$days = max(-1,intval(@$GLOBALS[$this->A['opre'].$key]));
			$this->arc->setend($days);
		}
	}
	protected function user_vieworder($mode = 0){
		$key = substr(__FUNCTION__,5);
		$cfg = &$this->cfgs[$key];
		if(!$mode){//��ʼ��
		}elseif($mode == 1){//��ʾ
			if(empty($cfg['title'])) $cfg['title'] = '�������ȼ�';
			$na = array('w' => 5);
			foreach($cfg as $k => $v){//���±�������ͨ��$cfg����
				if(in_array($k,array('guide','w',))) $na[$k] = $v;
			}
			trbasic($this->input_checkbox($key,$cfg['title']),"{$this->A['opre']}$key",'','text',$na);
		}elseif($mode == 2){//����
			$this->arc->updatefield($key,$GLOBALS[$this->A['opre'].$key]);
		}
	}
	protected function user_dpmid($mode = 0){
		$key = substr(__FUNCTION__,5);
		$cfg = &$this->cfgs[$key];
		if(!$mode){//��ʼ��
		}elseif($mode == 1){//��ʾ
			if(empty($cfg['title'])) $cfg['title'] = '��������Ȩ��';
			$na = array();
			foreach($cfg as $k => $v){//���±�������ͨ��$cfg����
				if(in_array($k,array('guide',))) $na[$k] = $v;
			}
			trbasic($this->input_checkbox($key,$cfg['title']),"{$this->A['opre']}$key",makeoption(array('-1' => '�̳���Ŀ') + pmidsarr('down'),-1),'select',$na);
		}elseif($mode == 2){//����
			$this->arc->updatefield($key,$GLOBALS[$this->A['opre'].$key]);
		}
	}
	
	protected function user_delete($mode = 0){
		$key = substr(__FUNCTION__,5);
		if(!$this->mc && !allow_op('adel')) return $this->del_item($key);
		
		$cfg = &$this->cfgs[$key];
		if(!$mode){//��ʼ��
			$cfg['bool'] = 1;
		}elseif($mode == 1){//��ʾ
			if(empty($cfg['title'])) $cfg['title'] = 'ɾ��';
			return $this->input_checkbox($key,$cfg['title'],1,'onclick="deltip()"');
		}elseif($mode == 2){//����
			$this->arc->arc_delete();
		}
	}	
	protected function user_delbad($mode = 0){
		$key = substr(__FUNCTION__,5);
		if(!$this->mc && !allow_op('adel')) return $this->del_item($key);
		
		$cfg = &$this->cfgs[$key];
		if(!$mode){//��ʼ��
			$cfg['bool'] = 1;
		}elseif($mode == 1){//��ʾ
			if(empty($cfg['title'])) $cfg['title'] = 'ɾ��(�ۻ���)'; 
			//this,'ȷ��ɾ�����ô˷�ʽɾ��Ҫ�۳������˵���ػ��֡�'
			//input_checkbox()���Ǵ���������ȥ����title�����deltip()�Ӹ��Զ�����ʾ��
			return $this->input_checkbox($key,$cfg['title'],1,"onclick=\"deltip()\"");
		}elseif($mode == 2){//����
			$this->arc->arc_delete(1);
		}
	}
	
	protected function user_check($mode = 0){
		$key = substr(__FUNCTION__,5);
		if(!$this->mc && !allow_op('acheck')) return $this->del_item($key);
		
		$cfg = &$this->cfgs[$key];
		if(!$mode){//��ʼ��
			$cfg['bool'] = 1;
		}elseif($mode == 1){//��ʾ
			if(empty($cfg['title'])) $cfg['title'] = '���';
			return $this->input_checkbox($key,$cfg['title'],1);
		}elseif($mode == 2){//����
			$this->arc->arc_check(1);
		}
	}	
	protected function user_uncheck($mode = 0){
		$key = substr(__FUNCTION__,5);
		if(!$this->mc && !allow_op('acheck')) return $this->del_item($key);
		
		$cfg = &$this->cfgs[$key];
		if(!$mode){//��ʼ��
			$cfg['bool'] = 1;
		}elseif($mode == 1){//��ʾ
			if(empty($cfg['title'])) $cfg['title'] = '����';
			return $this->input_checkbox($key,$cfg['title'],1);
		}elseif($mode == 2){//����
			if($this->is_item('check')) return false;//����checkͬʱִ��
			$this->arc->arc_check(0);
		}
	}
	// limit:�޶�(��),timeʱ����Ϊ(����),
	protected function user_readd($mode = 0){
		global $timestamp; 
		$key = substr(__FUNCTION__,5);
		$cfg = &$this->cfgs[$key];
		if(!$mode){//��ʼ��
			$cfg['bool'] = 1;
		}elseif($mode == 1){//��ʾ
			if(empty($cfg['title'])) $cfg['title'] = 'ˢ��';
			return $this->input_checkbox($key,$cfg['title'],1);
		}elseif($mode == 2){//����
			if(!isset($cfg['limit'])){
				$this->arc->readd();
			}else{
				$do = 1; 
				if(!isset($cfg['time'])) $cfg['time'] = 0;
				if(!isset($this->recnt['readd'])){
					$this->recnt['readd']['do'] = 0; //������
					$this->recnt['readd']['skip'] = 0; //������
				}
				if($this->recnt['readd']['do']>=$cfg['limit']){ //��������
					$this->recnt['readd']['skip']++; 
					return false;
				}elseif($cfg['time']&&($timestamp-$this->arc->archive['refreshdate']<$cfg['time']*60)){
					$this->recnt['readd']['skip']++;
					return false; //$do = 0; //������
				}
				if($do){
					$this->recnt['readd']['do']++;
					$this->arc->readd();
					//���»�Ա������ˢ�´������ֶ�	refreshes
					isset($cfg['fieldname']) &&	$this->arc->update_refreshes($cfg['fieldname']);
				}
			}
		}
	}
	// limit:�޶�(��),days�ϼ�����(��),	
	protected function user_valid($mode = 0){
		global $timestamp; 
		$key = substr(__FUNCTION__,5);
		$cfg = &$this->cfgs[$key];
		if(!$mode){//��ʼ��
			$cfg['bool'] = 1;
		}elseif($mode == 1){//��ʾ
			if(empty($cfg['title'])) $cfg['title'] = '�ϼ�';
			return $this->input_checkbox($key,$cfg['title'],1);
		}elseif($mode == 2){//����
			$days = empty($cfg['days']) ? -1 : max(1,intval($cfg['days']));
			if(!isset($cfg['limit'])){
				$this->arc->setend($days);
			}else{
				$do = 1; 
				if(!isset($this->recnt['valid'])){
					$this->recnt['valid']['do'] = 0; //������
					$this->recnt['valid']['skip'] = 0; //������
				}
				if($this->recnt['valid']['do']>=$cfg['limit']){ //��������
					$this->recnt['valid']['skip']++; 
					return false; 
				}elseif(empty($this->arc->archive['enddate']) || ($timestamp<$this->arc->archive['enddate'])){
					$this->recnt['valid']['skip']++;
					return false; //$do = 0; //������
				}
				if($do){
					$this->recnt['valid']['do']++;
					$this->arc->setend($days);
				}
			}
		}
	}	
	protected function user_unvalid($mode = 0){
		$key = substr(__FUNCTION__,5);
		
		$cfg = &$this->cfgs[$key];
		if(!$mode){//��ʼ��
			$cfg['bool'] = 1;
		}elseif($mode == 1){//��ʾ
			if(empty($cfg['title'])) $cfg['title'] = '�¼�';
			return $this->input_checkbox($key,$cfg['title'],1);
		}elseif($mode == 2){//����
			if($this->is_item('valid')) return;//����validͬʱִ��
			$this->arc->setend(0);
		}
	}
	
	protected function user_incheck($mode = 0){
		$key = substr(__FUNCTION__,5);
		if($this->A['isab'] != 1) return $this->del_item($key);
		
		$cfg = &$this->cfgs[$key];
		if(!$mode){//��ʼ��
			$cfg['bool'] = 1;
		}elseif($mode == 1){//��ʾ
			if(empty($cfg['title'])) $cfg['title'] = '������Ч';
			return $this->input_checkbox($key,$cfg['title'],1);
		}elseif($mode == 2){//����
			$this->arc->incheck(1,$this->A['arid'],$this->A['pid']);
		}
	}	
	protected function user_unincheck($mode = 0){
		$key = substr(__FUNCTION__,5);
		if($this->A['isab'] != 1) return $this->del_item($key);
		
		$cfg = &$this->cfgs[$key];
		if(!$mode){//��ʼ��
			$cfg['bool'] = 1;
		}elseif($mode == 1){//��ʾ
			if(empty($cfg['title'])) $cfg['title'] = '������Ч';
			return $this->input_checkbox($key,$cfg['title'],1);
		}elseif($mode == 2){//����
			if($this->is_item('incheck')) return;//����checkͬʱִ��
			$this->arc->incheck(0,$this->A['arid'],$this->A['pid']);
		}
	}
		
	protected function user_inclear($mode = 0){
		$key = substr(__FUNCTION__,5);
		if($this->A['isab'] != 1) return $this->del_item($key);
		
		$cfg = &$this->cfgs[$key];
		if(!$mode){//��ʼ��
			$cfg['bool'] = 1;
		}elseif($mode == 1){//��ʾ
			if(empty($cfg['title'])) $cfg['title'] = '���';
			return $this->input_checkbox($key,$cfg['title'],1,"onclick=\"deltip()\" title=\"����ѡ��Ŀ��ȡ��������ͬ��˲ٲ�����ʾ��������\"");	
		}elseif($mode == 2){//����
			$this->arc->exit_album($this->A['arid'],$this->A['pid']);
		}
	}
	protected function user_autoletter($mode = 0){
		$key = substr(__FUNCTION__,5);
		if(empty($this->channel['autoletter'])) return $this->del_item($key);
		
		$cfg = &$this->cfgs[$key];
		if(!$mode){//��ʼ��
			$cfg['bool'] = 1;
		}elseif($mode == 1){//��ʾ
			if(empty($cfg['title'])) $cfg['title'] = '�Զ�����ĸ';
			return $this->input_checkbox($key,$cfg['title'],1);
		}elseif($mode == 2){//����
			$this->arc->autoletter();
		}
	}	
	protected function user_static($mode = 0){
		$key = substr(__FUNCTION__,5);
		$cfg = &$this->cfgs[$key];
		if(!$mode){//��ʼ��
			$enablestatic = cls_env::mconfig('enablestatic');
			$splitbls = cls_cache::Read('splitbls');
			$spstatic = $splitbls[$this->channel['stid']]['nostatic']; //$this->channel['stid']; //����ֱ�ID
			$canstatic = $enablestatic && empty($spstatic);
			$cfg['bool'] = $canstatic; //������:������̬ �� �ֱ����>>�رվ�̬>>δ��ѡ
		}elseif($mode == 1){//��ʾ
			if(empty($cfg['title'])) $cfg['title'] = '���ָ�ʽ��̬';
			return $this->input_checkbox($key,$cfg['title'],1);
		}elseif($mode == 2){//����
			$this->arc->arc_static(1);
		}
	}	
	protected function user_nstatic($mode = 0){
		$key = substr(__FUNCTION__,5);
		$cfg = &$this->cfgs[$key];
		if(!$mode){//��ʼ��
			$enablestatic = cls_env::mconfig('enablestatic');
			$splitbls = cls_cache::Read('splitbls');
			$spstatic = $splitbls[$this->channel['stid']]['nostatic']; //$this->channel['stid']; //����ֱ�ID
			$canstatic = $enablestatic && empty($spstatic);
			$cfg['bool'] = $canstatic; //������:������̬ �� �ֱ����>>�رվ�̬>>δ��ѡ
		}elseif($mode == 1){//��ʾ
			if(empty($cfg['title'])) $cfg['title'] = '���¸�ʽ��̬';
			return $this->input_checkbox($key,$cfg['title'],1);
		}elseif($mode == 2){//����
			if($this->is_item('static')) return;//����staticͬʱִ��
			$this->arc->arc_static(0);
		}
	}	
	protected function user_autoabstract($mode = 0){
		$key = substr(__FUNCTION__,5);
		if(empty($this->channel['autoabstract'])) return $this->del_item($key);
		
		$cfg = &$this->cfgs[$key];
		if(!$mode){//��ʼ��
			$cfg['bool'] = 1;
		}elseif($mode == 1){//��ʾ
			if(empty($cfg['title'])) $cfg['title'] = '�Զ�ժҪ';
			return $this->input_checkbox($key,$cfg['title'],1);
		}elseif($mode == 2){//����
			$this->arc->autoabstract();
		}
	}	
	protected function user_autothumb($mode = 0){
		$key = substr(__FUNCTION__,5);
		if(empty($this->channel['autothumb'])) return $this->del_item($key);
		
		$cfg = &$this->cfgs[$key];
		if(!$mode){//��ʼ��
			$cfg['bool'] = 1;
		}elseif($mode == 1){//��ʾ
			if(empty($cfg['title'])) $cfg['title'] = '�Զ�����ͼ';
			return $this->input_checkbox($key,$cfg['title'],1);
		}elseif($mode == 2){//����
			$c_upload = cls_upload::OneInstance();
			$this->arc->autothumb();
			if(!empty($c_upload->ufids)){
				$c_upload->closure(1, $this->arc->aid);
				$c_upload->ufids = array();
			}
		}
	}	
	protected function user_autokeyword($mode = 0){
		$key = substr(__FUNCTION__,5);
		if(empty($this->channel['autokeyword'])) return $this->del_item($key);
		
		$cfg = &$this->cfgs[$key];
		if(!$mode){//��ʼ��
			$cfg['bool'] = 1;
		}elseif($mode == 1){//��ʾ
			if(empty($cfg['title'])) $cfg['title'] = '�Զ��ؼ���';
			return $this->input_checkbox($key,$cfg['title'],1);
		}elseif($mode == 2){//����
			$this->arc->autokeyword();
		}
	}
}
