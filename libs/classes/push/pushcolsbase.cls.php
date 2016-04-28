<?php
/*
** �б�������Ŀ�����ݴ�����
** ���е�������Ԫ�����ݵ�Ԫ
*/
!defined('M_COM') && exit('No Permisson');
class cls_pushcolsbase{
	protected $mc = 0;//��Ա����
	public $A = array();//��ʼ��������š���chid��pre(����ǰ׺),tbl(����),stid(����id)
	public $cfgs = array();//����Ŀ����
	public $titles = array();//�����е�title����
	public $groups = array();//����������Ϣ
	
    function __construct($cfg = array()){
		$this->mc = defined('M_ADMIN') ? 0 : 1;
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
	//coid����ϵid
	//len���������ݽ�ȡ���ȣ���subject
	//icon����ϵ�Ƿ�ͼ����ʾ
	//num����ѡ�����ʾ����
	//empty��Ϊ��ʱ����ʾ����
	//fmt��ʱ�䷽���ĸ�ʽ������
	
	//��ȫ���Ʒ�����user_$key����Ϊ����ʹ�õķ���
	//ϵͳ���÷�����type_$key������������µ��ã�������type��δ���Ƶ���ϵ������δָ����������ʾ��
	public function additem($key = '',$cfg = array()){
		//�Ὣ�����е������ȴ����
		if(!$key) return;
		$this->cfgs[$key] = $cfg;
		$re = $this->call_method("user_$key",array(1));//���Ʒ���
		if($re == 'undefined'){
			$re = $this->type_method($key,1);//�����͵ķ���
		}
		return $re;
	}
	
	public function fetch_one_row($data = array()){//���ص����ĵ�������
		$mains = array();//��ʼ���������һ�е�����
		foreach($this->cfgs as $key => $cfg){
			$mains[$key] = $this->one_item($key,$data);
		}
		$mains = $this->deal_group($mains,'main');
		return $mains;
	}
	
	public function fetch_top_row(){//���������е�����
		return $this->deal_group($this->titles,'top');
	}
	
	public function addgroup($mainstyle,$topstyle = ''){//���ӷ���
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
		if(!empty($this->cfgs[$key]['type'])){//����ר�����͵ķ���
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
	protected function top_title($key,$cfg){
		$re = empty($cfg['title']) ? $key : $cfg['title'];
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
	
	//ʱ�䷽��
	//fmt:ʱ�䷽���ĸ�ʽ������
	protected function type_date($key = '',$mode = 0,$data = array()){
		$cfg = &$this->cfgs[$key];	
		if($mode){//�����б���������
			$arr = array('createdate' => '�������','refreshdate' => 'ˢ������','updatedate' => '��������','enddate' => 'ʧЧ����',);
			if(empty($cfg['title']) && isset($arr[$key])) $cfg['title'] = $arr[$key];
			$this->titles[$key] = $this->top_title($key,$cfg);
		}else{
			return empty($data[$key]) ? (isset($cfg['empty']) ? $cfg['empty'] : '-') : date(isset($cfg['fmt']) ? $cfg['fmt'] : 'Y-m-d',$data[$key]);
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
	
	//�����text�����б�������Ŀ���ã�wָ�������Ŀ��
	protected function type_input($key = '',$mode = 0,$data = array()){
		$cfg = &$this->cfgs[$key];	
		if($mode){//�����б���������
			$this->titles[$key] = $this->top_title($key,$cfg);
		}else{//�б����ڵı���
			$w = empty($cfg['w']) ? 4 : max(1,intval($cfg['w']));
			return $this->input_text("{$this->A['mfm']}[{$data['pushid']}][$key]",isset($data[$key]) ? $data[$key] : '',$w);
		}
	}
	
	//checkbox�����б�������Ŀ���ã�wָ�������Ŀ��
	//atitle��ȫѡ�ı���
	protected function type_checkbox($key = '',$mode = 0,$data = array()){
		$cfg = &$this->cfgs[$key];	
		if(empty($cfg['width'])) $cfg['width'] = 40;
		if($mode){//�����б���������
			if(empty($cfg["title"])){
				$cfg['title'] = "<input class=\"checkbox\" type=\"checkbox\" name=\"chkall$key\" onclick=\"checkall(this.form,'{$this->A['mfm']}','chkall$key')\">";
				if(!empty($cfg['atitle'])) $cfg['title'] .= $cfg['atitle'];
			}
			$this->titles[$key] = $this->top_title($key,$cfg);
		}else{//�����б�������
			return $this->input_checkbox("{$this->A['mfm']}[{$data['pushid']}][$key]",empty($data[$key]) ? 0 : $data[$key]);
		}
	}
	
	//URL����
	//winsize-���ڴ�С����:��500,300,��url��ʹ��
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
		$field = empty($this->A['fields']) ? cls_PushArea::Field($this->A['paid'],$key) : @$this->A['fields'][$key];
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
	
	//��������
	//empty��Ϊ��ʱ��ʾ����
	protected function user_loadtype($mode = 0,$data = array()){
		$key = substr(__FUNCTION__,5);
		$cfg = &$this->cfgs[$key];	
		if($mode){//�����б���������
			$this->titles[$key] = empty($cfg['title']) ? '��ԴID' : $cfg['title'];
		}else{
			$reval = $data['fromid']; $loadtype = @$data[$key];
			$tarr = array('11'=>array('�ֶ����','999999'), '21'=>array('�Զ�����','0033FF')); //'0'=>'�б����',
			$title = isset($tarr[$loadtype]) ? $tarr[$loadtype][0] : '�ֶ�����';
			$css = isset($tarr[$loadtype]) ? $tarr[$loadtype][1] : '333333';
			if(empty($reval) && $loadtype=='11') $reval = '�ֶ����'; //�в����������:�ֶ���ӵ�,fromid��Ϊ��?
			$re = "<span title='$title' style='color:#$css'>$reval</span>";
			return $re;
		}
	}
	
	//��ͨ�ֶ�,��$key����ȡ��������
	//empty��Ϊ��ʱ��ʾ����
	protected function type_other($key = '',$mode = 0,$data = array()){
		$cfg = &$this->cfgs[$key];	
		if($mode){//�����б���������
			$this->titles[$key] =  $this->top_title($key,$cfg);
		}else{
			return isset($data[$key])&&!empty($data[$key]) ? $data[$key] : (isset($cfg['empty']) ? $cfg['empty'] : '-');
		}
	}
	
	//ѡ��id
	protected function user_selectid($mode = 0,$data = array()){
		$key = substr(__FUNCTION__,5);
		$cfg = &$this->cfgs[$key];
		if(empty($cfg['width'])) $cfg['width'] = 30;
		if(!isset($cfg['view'])) $cfg['view'] = 'S';
		if($mode){//�����б���������
			if(empty($cfg["title"])) $cfg['title'] = "<input class=\"checkbox\" type=\"checkbox\" name=\"chkall\" onclick=\"checkall(this.form,'selectid','chkall')\">";
			$this->titles[$key] = $this->top_title($key,$cfg);
		}else{//�����б�������
			return "<input class=\"checkbox\" type=\"checkbox\" name=\"selectid[{$data['pushid']}]\" value=\"{$data['pushid']}\">";
		}
	}
	
	//����
	protected function user_subject($mode = 0,$data = array()){
		$key = substr(__FUNCTION__,5);
		$cfg = &$this->cfgs[$key];
		if($mode){//�����б���������
			empty($cfg['side']) && $cfg['side'] = 'L';
			!isset($cfg['view']) && $cfg['view'] = 'S';
			if(empty($cfg['title'])) $cfg['title'] = '����';
			$this->titles[$key] = $this->top_title($key,$cfg);
		}else{//�����б�������
			$re = (!empty($data['thumb']) ? '<font style="color:red">ͼ&nbsp;</font>' : '');
			$len = empty($cfg['len']) ? 40 : $cfg['len'];
			if(!empty($data['thumb'])) $len -= 4;
			$re .= htmlspecialchars(cls_string::CutStr($data['subject'],$len));
			if(!empty($data['color'])) $re = "<font style=\"color:{$data['color']}\">$re</font>";
			if(!empty($data['url'])) $re = "<a href=\"{$data['url']}\" target=\"_blank\" title=\"".htmlspecialchars($data['subject'])."\">$re</a>";
			return $re;
		}
	}
	
	//����
	protected function user_detail($mode = 0,$data = array()){
		$key = substr(__FUNCTION__,5);
		$cfg = &$this->cfgs[$key];	
		if($mode){//�����б���������
			if(empty($cfg["title"])) $cfg['title'] = '�༭';
			if(empty($cfg["width"])) $cfg['width'] = 40;
			$this->titles[$key] = $this->top_title($key,$cfg);
		}else{
			return "<a href=\"?entry=extend&extend=push&paid={$this->A['paid']}&pushid={$data['pushid']}\" onclick=\"return floatwin('open_push$key',this)\">����</a>";
		}
	}
	//����
	protected function user_share($mode = 0,$data = array()){
		$key = substr(__FUNCTION__,5);
		if(!($area = cls_PushArea::Config($this->A['paid'])) || empty($area['copyspace'])) return $this->del_item($key);
		$cfg = &$this->cfgs[$key];	
		if($mode){//�����б���������
			if(empty($cfg["title"])) $cfg['title'] = '����';
			if(empty($cfg["width"])) $cfg['width'] = 40;
			$this->titles[$key] = $this->top_title($key,$cfg);
		}else{
			$mtitle = '(0)';
			if($num = cls_pusher::copynum($data,$this->A['paid'])) $mtitle = "(<b>$num</b>)";
			return "<a href=\"?entry=extend&extend=push_share&paid={$this->A['paid']}&pushid={$data['pushid']}\" onclick=\"return floatwin('open_push$key',this)\">$mtitle</a>";
		}
	}
	
	//�ֶ��������б�������Ŀ���ã�wָ�������Ŀ��
	protected function user_vieworder($mode = 0,$data = array()){
		$key = substr(__FUNCTION__,5);
		$cfg = &$this->cfgs[$key];	
		if($mode){//�����б���������
			if(empty($cfg["title"])) $cfg['title'] = '����';
			$this->titles[$key] = $this->top_title($key,$cfg);
		}else{//�б����ڵı���
			$w = empty($cfg['w']) ? 3 : max(1,intval($cfg['w']));
			$value = in_array($data[$key],array(0,500)) ? '' : $data[$key];
			return $this->input_text("{$this->A['mfm']}[{$data['pushid']}][$key]",$value,$w);
		}
	}
	//��λ�������б�������Ŀ���ã�wָ�������Ŀ��
	protected function user_fixedorder($mode = 0,$data = array()){
		$key = substr(__FUNCTION__,5);
		$cfg = &$this->cfgs[$key];	
		if($mode){//�����б���������
			if(empty($cfg["title"])) $cfg['title'] = '��λ';
			$this->titles[$key] = $this->top_title($key,$cfg);
		}else{//�б����ڵı���
			$w = empty($cfg['w']) ? 3 : max(1,intval($cfg['w']));
			$value = in_array($data[$key],array(0,500)) ? '' : $data[$key];
			return $this->input_text("{$this->A['mfm']}[{$data['pushid']}][$key]",$value,$w);
		}
	}
		
	//�Ƿ���Ч
	protected function user_valid($mode = 0,$data = array()){
		$key = substr(__FUNCTION__,5);
		$cfg = &$this->cfgs[$key];	
		if($mode){//�����б���������
			if(empty($cfg["title"])) $cfg['title'] = '��Ч';
			$this->titles[$key] = $this->top_title($key,$cfg);
		}else{//�����б�������
			global $timestamp;
			return ($data['checked'] && ($data['startdate'] < $timestamp) && (empty($data['enddate']) || $data['enddate'] > $timestamp)) ? 'Y' : (isset($cfg['empty']) ? $cfg['empty'] : '-');
		}
	}
	
}
