<?php
/*
** �б�������Ŀ�����ݴ�����
** ���е�������Ԫ�����ݵ�Ԫ
*/
!defined('M_COM') && exit('No Permisson');
class cls_cucolsbase extends cls_cubasic{

	public $A = array();//��ʼ��������š���chid��pre(����ǰ׺),tbl(����),stid(����id)
	public $cfgs = array();//����Ŀ����
	public $titles = array();//�����е�title����
	public $groups = array();//����������Ϣ
	
    function __construct($cfg = array()){
		parent::__construct($cfg);
		$this->A = $cfg;	
	}
	protected function call_method($func,$args = array()){////���Զ������Ӵ���
		if(method_exists($this,$func)){
			return call_user_func_array(array(&$this,$func),$args);
		}else return 'undefined';
	}
	
	//type����������֮�󣬻�����function type_{type}()������,date,bool,url,select�ȷ���
	//title�������б���
	//width���п��
	//side������λ��(L/R/C)��Ĭ��ΪC
	//view�����Ƿ����أ���(��ʾ��������)/S(��������)/H(Ĭ������)
	//url������url�������κε�ǰ������ʹ��ռλ��{xxxx}����
	//winsize-���ڴ�С����:��500,300,��url��ʹ��
	//mtitle�����ı��⣬��url��ʾ����
	//umode��url�򿪷�ʽ,0(Ĭ�ϸ�����)/1(�´���)/2(������)
	//len���������ݽ�ȡ���ȣ���subject
	//num����ѡ�����ʾ����
	//empty��Ϊ��ʱ����ʾ����
	//fmt��ʱ�䷽���ĸ�ʽ������
	
	//��ȫ���Ʒ�����user_$key����Ϊ����ʹ�õķ���
	//ϵͳ���÷�����type_$key������������µ��ã�������type��δ���Ƶ���ϵ������δָ����������ʾ��
	function additem($key = '',$cfg = array()){ 
		//�Ὣ�����е������ȴ����
		if(!$key) return;
		$this->cfgs[$key] = $cfg; 
		$re = $this->call_method("user_$key",array(1));//���Ʒ���
		if($re == 'undefined'){ 
			$re = $this->type_method($key,1);//�����͵ķ���
		}
		return $re;
	}
	
	function fetch_one_row($data = array()){//���ص����ĵ�������
		$mains = array();//��ʼ���������һ�е�����
		foreach($this->cfgs as $key => $cfg){
			$mains[$key] = $this->one_item($key,$data);
		}
		$mains = $this->deal_group($mains,'main');
		return $mains;
	}
	
	function fetch_top_row(){//���������е�����
		return $this->deal_group($this->titles,'top');
	}
	
	function addgroup($mainstyle,$topstyle = ''){//���ӷ���
		if(preg_match_all("/\{(\w+?)\}/is",$mainstyle,$matches)){
			if($keys = array_unique($matches[1])){
				foreach($this->groups as $k => $v){
					if(array_intersect($keys,$v['keys'])) return;
				}
				$this->groups[$keys[0]] = array(
				'keys' => $keys,
				'mainstyle' => $mainstyle,
				'topstyle' => $topstyle,
				);
			}
		}
	}
	
	protected function deal_group($source = array(),$type = 'top'){
		if(!$source) return $source;
		$var_style = $type.'style';
		if($this->groups){
			foreach($source as $k => $v){
				if(!empty($this->groups[$k])){
					$na = array();
					foreach($this->groups[$k]['keys'] as $gk){
						if(isset($source[$gk])) $na[$gk] = $source[$gk];
						if($gk != $k) unset($source[$gk]);
					}
					$source[$k] = key_replace($this->groups[$k][$var_style],$na);
				}
			}
		}
		return $source;
	}
	
	protected function type_method($key = '',$mode = 0,$data = array()){
		if(!empty($this->cfgs[$key]['type'])){ //����ר�����͵ķ���,��bool,url,field��
			$re = $this->call_method("type_{$this->cfgs[$key]['type']}",array($key,$mode,$data));
			if($re == 'undefined') $re = $mode ? '' : false;
		}else $re = $this->type_other($key,$mode,$data);//��ͨ�ֶ�,��$key����ȡ��������
		if($mode && $re === false) $re = '';
		return $re;
	}
	
	//���ص�������
	protected function one_item($key = '',$data = array()){//����������
		if(!isset($this->cfgs[$key])) return '';
		$cfg = $this->cfgs[$key];
		
		$re = $this->call_method("user_$key",array(0,$data));//���Ʒ���
		if($re == 'undefined'){
			$re = $this->type_method($key,0,$data);//�����͵ķ���
		}
		if(!empty($cfg['prefix'])) $re = key_replace($cfg['prefix'],$data).$re;
		if(!empty($cfg['suxfix'])) $re .= key_replace($cfg['suxfix'],$data);
		return $re;
	}
	protected function del_item($key){
		unset($this->cfgs[$key]);
		return false;
	}
	
	// �ܹ��ֶοɲ���title����,�ɴ�cname�Զ����; �����ֶ�checked,mnameҲ��Ĭ��
	protected function top_title($key,$cfg){
		if(!empty($cfg['title'])){
			$re = $cfg['title'];
		}elseif(isset($this->fields[$key])){
			$re = $this->fields[$key]['cname'];
		}elseif($key=='checked'){
			$re = '���';
		}elseif($key=='mname'){
			$re = '��Ա';
		}else{
			$re = $key;	
		}
		return $re;
	}
	
	protected function input_text($varname,$value = '',$width = 4){
		if(!$varname) return $value;
		return "<input type=\"text\" size=\"$width\" id=\"$varname\" name=\"$varname\" value=\"".mhtmlspecialchars($value)."\" />\n";
	}
	protected function input_checkbox($varname,$value = 0,$chkedvalue = 1){
		if(!$varname) return $value;
		return "<input type=\"hidden\" name=\"$varname\" value=\"\"><input type=\"checkbox\" class=\"checkbox\" name=\"$varname\" value=\"$chkedvalue\"".($value == $chkedvalue ? ' checked' : '').">\n";
	}
	
	//�����������ƣ����[�ĵ�,��Ա]ʹ��
	//addno:ָ��ʹ���ĸ�����ҳ��url
	//aclass-<a>��ʽ
	// empty($pid) && $oL->m_additem('subject',array('title'=>'������˾','len'=>40,'field'=>'company'));
	protected function user_subject($mode = 0,$data = array()){
		$key = substr(__FUNCTION__,5); 
		$cfg = &$this->cfgs[$key];	
		if($mode){//�����б���������
			empty($cfg['side']) && $cfg['side'] = 'L';
			isset($cfg['view']) || $cfg['view'] = 'S';
			if(empty($cfg['title'])) $cfg['title'] = $this->ptype=='m' ? '������Ա' : '�����ĵ�'; 
			$this->titles[$key] = $this->top_title($key,$cfg); 
		}else{ 
			return $this->getPLink($data, $cfg); 
		}
	}
	
	//ʱ�䷽��
	//fmt:ʱ�䷽���ĸ�ʽ������
	//showEnd:������ʱ�䷽ʽ������ʾ(����ɫ����ʾ<����>), Ĭ��enddate���˷�ʽ��ʾ
	protected function type_date($key = '',$mode = 0,$data = array()){
		$cfg = &$this->cfgs[$key];	
		if($mode){//�����б���������
			$arr = array('cucreate' => '���ʱ��',);
			if(empty($cfg['title']) && isset($arr[$key])) $cfg['title'] = $arr[$key];
			$this->titles[$key] = $this->top_title($key,$cfg);
		}else{
			// enddateĬ�ϰ�showEnd��ʽ��ʾ����ʾ<����>, �����ɫ��ʾ�������
			$showEnd = isset($cfg['showEnd']) ? $cfg['showEnd'] : ($key=='enddate' ? 1 : 0);
			$timestamp = TIMESTAMP;
			$null = isset($cfg['empty']) ? $cfg['empty'] : ($showEnd ? '&lt;����&gt;' : '-');
			$fmt = isset($cfg['fmt']) ? $cfg['fmt'] : 'Y-m-d';
			$sval = date($fmt,intval($data[$key]));
			if($showEnd){
				$cval = date($fmt,$timestamp);
				if($cval>$sval){ $sval = "<span style='color:#FF0000'>$sval</span>"; } //�Ѿ�����:��ɫ
				elseif($cval==$sval){ $sval = "<span style='color:#0000FF'>$sval</span>"; } //�������:��ɫ
			}
			return empty($data[$key]) ? $null : $sval;
		}
	}
	
	//����ֵ�ķ�������ʾY/-
	protected function type_bool($key = '',$mode = 0,$data = array()){
		$cfg = &$this->cfgs[$key];	
		if($mode){//�����б���������
			$this->titles[$key] = $this->top_title($key,$cfg);
		}else{
			return empty($data[$key]) ? (isset($cfg['empty']) ? $cfg['empty'] : '-') : 'Y';
		}
	}
	
	//URL����
	//winsize-���ڴ�С����:��500,300,��url��ʹ��
	//umode:�򿪷�ʽ��0Ϊ�������򿪣�1Ϊ�´��ڴ�
	protected function type_url($key = '',$mode = 0,$data = array()){
		$cfg = &$this->cfgs[$key];	
		if($mode){//�����б���������
			$this->titles[$key] = $this->top_title($key,$cfg);
		}else{
			$addstr = empty($cfg['umode']) ? "onclick=\"return floatwin('open_arc$key',this".(empty($cfg['winsize']) ? '' : ','.$cfg['winsize']).")\"" : ($cfg['umode'] == 1 ? "target=\"_blank\"" : '');
			return "<a href=\"".key_replace($cfg['url'],$data)."\" {$addstr}>".(empty($cfg['mtitle']) ? (isset($data[$key]) ? $data[$key] : $key) : key_replace($cfg['mtitle'],$data))."</a>";
		}
	}
	
	//��ѡ�ֶη���
	protected function type_field($key = '',$mode = 0,$data = array()){
		@$field = $this->fields[$key]; 
		if(!$field || !in_array($field['datatype'],array('select','mselect','cacc',))) return $this->del_item($key);
		
		$cfg = &$this->cfgs[$key];
		if($mode){//�����б���������
			if(empty($cfg['title'])) $cfg['title'] = $field['cname'];
			$this->titles[$key] =  $this->top_title($key,$cfg);
		}else{
			$num = empty($cfg['num']) ? 0 : $cfg['num'];
			return empty($data[$key]) ? (isset($cfg['empty']) ? $cfg['empty'] : '-') : view_field_title($data[$key],$field,$num);
		}
	}
	
	//��ͨ�ֶ�,��$key����ȡ��������
	//empty��Ϊ��ʱ��ʾ����
	//mtitle: ��ʾģ��
	protected function type_other($key = '',$mode = 0,$data = array()){
		$cfg = &$this->cfgs[$key];	
		if($mode){//�����б���������
			$this->titles[$key] =  $this->top_title($key,$cfg);
		}else{ 
			$len = empty($cfg['len']) ? 40 : $cfg['len'];
			$dre = htmlspecialchars(cls_string::CutStr(@$data[$key],$len));
			if(isset($cfg['mtitle'])){
				$re = key_replace($cfg['mtitle'],$data);
			}else{
				$re = empty($data[$key]) ?  (isset($cfg['empty']) ? $cfg['empty'] : '-') : $dre;
			}
			return $re;
		}
	}
	
	//�ظ�����ͳ��
	//winsize-���ڴ�С����:��500,300,��url��ʹ��
	//aclass-<a>��ʽ
	//empty - $data[$key]Ϊ��ʱ���滻ֵ
	//tpl: ��ʾģ��(��: ��[{num}]��,numΪռλ��)
	protected function user_recounts($mode = 0,$data = array()){ 
		$key = substr(__FUNCTION__,5);
		$cfg = &$this->cfgs[$key];
		if(empty($cfg['width'])) $cfg['width'] = 30;
		//isset($cfg['view']) || $cfg['view'] = 'S';
		if($mode){//�����б���������
			if(empty($cfg['title'])) $cfg['title'] = '�ظ�'; 
			$this->titles[$key] = $this->top_title($key,$cfg);
		}else{//�����б�������
			global $tblprefix;
			$field = empty($cfg['field']) ? 'tocid' : $cfg['field']; 
			$nums = $this->db->select('COUNT(*)')->from(self::table())->where(array($field=>$data['cid']))->exec()->fetch();
			$nums = $nums['COUNT(*)'];
			$nums = empty($nums)? (isset($cfg['empty']) ? $cfg['empty'] : '0') : $nums;
			$tpl = isset($cfg['tpl']) ? $cfg['tpl'] : '[{num}]';
			$re = str_replace('{num}',$nums,$tpl);
			if(empty($cfg['url'])) return $re;
			$addstr = empty($cfg['umode']) ? "onclick=\"return floatwin('open_arc$key',this".(empty($cfg['winsize']) ? '' : ','.$cfg['winsize']).")\"" : ($cfg['umode'] == 1 ? "target=\"_blank\"" : '');
			return "<a ".(isset($cfg['aclass']) ? "class='$cfg[aclass]'" : "class='scol_url_pub'")." href=\"".key_replace($cfg['url'],$data)."\" {$addstr}>$re</a>";
			
		}
	}
	
	//ѡ��id
	protected function user_selectid($mode = 0,$data = array()){
		$key = substr(__FUNCTION__,5);
		$cfg = &$this->cfgs[$key];
		if(empty($cfg['width'])) $cfg['width'] = 30;
		isset($cfg['view']) || $cfg['view'] = 'S';
		if($mode){//�����б���������
			if(empty($cfg["title"])) $cfg['title'] = "<input class=\"checkbox\" type=\"checkbox\" name=\"chkall\" onclick=\"checkall(this.form,'selectid','chkall')\">";
			$this->titles[$key] = $this->top_title($key,$cfg);
		}else{//�����б�������
			return "<input class=\"checkbox\" type=\"checkbox\" name=\"selectid[$data[cid]]\" value=\"$data[cid]\">";
		}
	}

}
