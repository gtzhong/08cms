<?php
/*
** �б�������Ŀ�����ݴ�����
** ���е�������Ԫ�����ݵ�Ԫ
*/
!defined('M_COM') && exit('No Permisson');
class cls_memcolsbase{
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
		if('ugid' == substr($key,0,4)){//��Ա���ͨ�÷���
			$re = $this->type_ugid($key,$mode,$data);
		}elseif('mctid' == substr($key,0,5)){//��Ա��֤��ͨ�÷���
			$re = $this->type_mctid($key,$mode,$data);
		}elseif(!empty($this->cfgs[$key]['type'])){//����ר�����͵ķ���,��bool,url,field��
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
			$arr = array('regdate' => 'ע������','lastvisit' => '�ϴε�¼',);
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
		$field = empty($this->A['fields']) ? cls_cache::Read('field',$this->A['chid'],$key) : @$this->A['fields'][$key];
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
	
	//��ʾ��Ա�飬�ų�������ϵ
	//title-�����б���
	protected function type_ugid($key = '',$mode = 0,$data = array()){
		$grouptypes = cls_cache::Read('grouptypes');
		$gtid = max(0,intval(str_replace('ugid','',$key)));
		if(!$gtid || ($gtid== 2) || empty($grouptypes[$gtid])) return $this->del_item($key);
		if(!($ugidsarr = ugidsarr($gtid,$this->A['mchid'],1))) return $this->del_item($key);
		
		$cfg = &$this->cfgs[$key];
		if($mode){
			if(empty($cfg['title'])) $cfg['title'] = $grouptypes[$gtid]['cname'];
			if(!isset($cfg['view'])) $cfg['view'] = $this->A['mchid'] ? '' : 'H';
			$this->titles[$key] =  $this->top_title($key,$cfg);
		}else{
			$re = empty($ugidsarr[$data["grouptype$gtid"]]) ? '' : $ugidsarr[$data["grouptype$gtid"]];
			$re || $re = isset($cfg['empty']) ? $cfg['empty'] : '-';
			return $re;
		}
	}
	
	//��ʾ��Ա��֤
	//title-�����б���
	protected function type_mctid($key = '',$mode = 0,$data = array()){
		$mctypes = cls_cache::Read('mctypes');
		if(!($mctid = max(0,intval(str_replace('mctid','',$key)))) || empty($mctypes[$mctid]['available'])) return $this->del_item($key);
		if($this->A['mchid'] && !in_array($this->A['mchid'],explode(',',$mctypes[$mctid]['mchids']))) return $this->del_item($key);//��ǰģ�Ͳ���Ҫ����֤
		
		$cfg = &$this->cfgs[$key];
		if($mode){
			if(empty($cfg['title'])) $cfg['title'] = $mctypes[$mctid]['cname'];
			isset($cfg['view']) || $cfg['view'] = 'H';
			$this->titles[$key] =  $this->top_title($key,$cfg);
		}else{
			return empty($data["mctid$mctid"]) ? '-' : 'Y';
		}
	}
	
	
	//��ϵ��ͨ�÷���
	//title-���⣬icon�Ƿ�ͼ����ʾ��num��ѡ��ʱ�����ʾ����
	//url����,��������,���ڹ�����ϵ���������
	//winsize-���ڴ�С����:��500,300,��url��ʹ��
	//aclass-<a>��ʽ
	//custom �Զ����ֶ� �����Զ����ֶι�����ϵ�����
	private function type_ccid_final($key = '',$mode = 0,$data = array(),$custom=''){
		$cotypes = cls_cache::Read('cotypes');
		if(!($coid = max(0,intval(str_replace('ccid','',$key)))) || empty($cotypes[$coid]) || $cotypes[$coid]['self_reg']) return $this->del_item($key);
		
		!empty($custom) && $key = $custom;
		$cfg = &$this->cfgs[$key];
		$cfg['coid'] = $coid;
		if($mode){
			if(empty($cfg['title'])) $cfg['title'] = $cotypes[$coid]['cname'];
			$this->titles[$key] =  $this->top_title($key,$cfg);
		}else{
			$color = empty($cfg['color']) ? '' : $cfg['color'];
			$icon = empty($cfg['icon']) ? 0 : 1;
			$num = empty($cfg['num']) ? 0 : $cfg['num'];
			isset($cotypes[$coid]['asmode']) || $cotypes[$coid]['asmode']='';
			$re = cls_catalog::cnstitle(@$data[$key],$cotypes[$coid]['asmode'],cls_cache::Read('coclasses',$coid),$num,$icon);
			$re || $re = isset($cfg['empty']) ? $cfg['empty'] : '-';
			$addstr = empty($cfg['umode']) ? "onclick=\"return floatwin('open_arc$key',this".(empty($cfg['winsize']) ? '' : ','.$cfg['winsize']).")\"".($color ? " style=\"color:$color\"" : "") : ($cfg['umode'] == 1 ? "target=\"_blank\"" : '').($color ? " style=\"color:$color\"" : "");
			isset($cfg['url']) && $re = "<a ".(isset($cfg['aclass']) ? "class='$cfg[aclass]'" : "class='scol_url_pub'")." href=\"".key_replace($cfg['url'],$data)."\" {$addstr}>$re</a>";
			return $re;
		}
	}
	
	
	//��ͨ�ֶ�,��$key����ȡ��������
	//empty��Ϊ��ʱ��ʾ����
	protected function type_other($key = '',$mode = 0,$data = array()){
		$field = empty($this->A['fields']) ? cls_cache::Read('mfield',$this->A['mchid'],$key) : @$this->A['fields'][$key];
		//�Զ����ֶι�����ϵ����
		if( @$field['coid'] && $field['datatype'] == 'cacc'){
			 $coid = max(0,intval($field['coid']));
			 return $this->type_ccid_final("ccid$coid",$mode,$data,$key);
		}
		$cfg = &$this->cfgs[$key];	
		if($mode){//�����б���������
			$this->titles[$key] =  $this->top_title($key,$cfg);
		}else{
			return isset($data[$key]) ? $data[$key] : (isset($cfg['empty']) ? $cfg['empty'] : '-');
		}
	}
	
	/*��ԱID��̬��ַ
	* @example  $oL->m_additem('mid');
				$oL->m_additem('mid',array('url'=>"{$cms_abs}mspace/index.php?mid={mid}",'title'=>'ID')); //ָ������
				$oL->m_additem('mid',array('url'=>"#",'title'=>'ID')); //��Ҫ����
    *
	*/
	protected function user_mid($mode = 0,$data = array()){
		$key = substr(__FUNCTION__,5);
		$cfg = &$this->cfgs[$key];
			
		if($mode){//�����б���������
			$this->titles[$key] =  isset($cfg["title"])&&!empty($cfg["title"]) ? $cfg["title"] : ' ID' ;
		}else{
			$re = $data[$key];
			if(@$cfg['url']!='#'){  // ����Ҫurl����
				$cms_abs = cls_env::mconfig('cms_abs');
				$re = 	"<a target='_blank' title='����鿴��̬��ַ'".(isset($cfg['aclass']) ? "class='$cfg[aclass]'" : "class='scol_url_pub'")." href=\"".(isset($cfg['url']) ? key_replace($cfg['url'],$data) : $cms_abs.'mspace/index.php?mid='.$re)."\" >$re</a>";	
			}
			return $re;	
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
			return "<input class=\"checkbox\" type=\"checkbox\" name=\"selectid[$data[mid]]\" value=\"$data[mid]\">";
		}
	}
	
	//��Ա���ƣ�Ĭ�ϴ��Ͽռ�url
	//nourl:����Ҫ�ռ�url��1:����Ҫ�ռ�url��array(1,2,3):mchidΪ1,2,3�Ĳ�Ҫ�ռ�url
	//field:��ʾ���ֶ����ƣ�Ĭ��Ϊmname
	//pic:���������ͼƬ�ֶ��ж��Ƿ�������ݶ���ʾ��ͼ������
	protected function user_subject($mode = 0,$data = array()){
		$key = substr(__FUNCTION__,5);
		$cfg = &$this->cfgs[$key];
		if($mode){//�����б���������
			empty($cfg['side']) && $cfg['side'] = 'L';
			isset($cfg['view']) || $cfg['view'] = 'S';
			if(empty($cfg['title'])) $cfg['title'] = '����';
			$this->titles[$key] = $this->top_title($key,$cfg);
		}else{//�����б�������
			
			$len = empty($cfg['len']) ? 40 : $cfg['len'];
			$re = $data['isfounder'] && $cfg['field'] == 'mname' ? '[��ʼ��]' : '';
			$re .= $data[$cfg['field']];
			$re = htmlspecialchars(cls_string::CutStr($re,$len));
			if(empty($cfg['nourl']) || (is_array($cfg['nourl']) && !in_array($data['mchid'],$cfg['nourl']))){			
				$re = "<a href=\"".cls_Mspace::IndexUrl($data)."\" target=\"_blank\" title=\"{$data[$cfg['field']]}\">".(empty($cfg['pic'])?'':(empty($data[$cfg['pic']])?'':"<font style=\"color:red;\">ͼ</font>"))."$re</a>";
			}
			return $re;
		}
	}
	
	//��Ա����
	//����ű�Ϊָ��ģ�ͣ����Զ�����
	protected function user_mchid($mode = 0,$data = array()){
		$key = substr(__FUNCTION__,5);
		if($this->A['mchid']) return $this->del_item($key);
		$cfg = &$this->cfgs[$key];
		if($mode){
			if(empty($cfg['title'])) $cfg['title'] = '��Ա����';
			$this->titles[$key] =  $this->top_title($key,$cfg);
		}else{
			$re = ($mchannel = cls_cache::Read('mchannel',$data['mchid'])) ? $mchannel['cname'] : '';
			$re || $re = isset($cfg['empty']) ? $cfg['empty'] : '-';
			return $re;
		}
	}
	
	//ע��IP
	//����ű�Ϊָ��ģ�ͣ����Զ�����
	protected function user_regip($mode = 0,$data = array()){
		$key = substr(__FUNCTION__,5);
		#if($this->A['mchid']) return $this->del_item($key);
		$cfg = &$this->cfgs[$key];
		if($mode){
			if(empty($cfg['title'])) $cfg['title'] = 'ע��IP';
			$this->titles[$key] =  $this->top_title($key,$cfg);
		}else{
			return empty($data['regip']) ? '' : $data['regip'];
		}
	}
	
	//��Ա���Ĵ���
	protected function user_trustee($mode = 0,$data = array()){
		global $cms_abs,$g_apid;
		$curuser = cls_UserMain::CurUser();
		$key = substr(__FUNCTION__,5);
		$cfg = &$this->cfgs[$key];
		
		if($curuser->NoBackFunc('trusteeship')) return $this->del_item($key);
		
		if($mode){//�����б���������
			empty($cfg['side']) && $cfg['side'] = 'C';
			isset($cfg['view']) || $cfg['view'] = 'S';
			empty($cfg['width']) && $cfg['width'] = '60';
			if(empty($cfg['title'])) $cfg['title'] = '��Ա����';
			$this->titles[$key] = $this->top_title($key,$cfg);
		}else{//�����б�������
			return cls_Permission::noPmReason($data,@$g_apid) && !$curuser->info['isfounder'] ? '-' : "<a href=\"{$cms_abs}adminm.php?from_mid=$data[mid]\" target=\"_blank\">����</a>";
		}
	}
	
	//��̬�ռ�
	protected function user_static($mode = 0,$data = array()){
		global $mspacepmid;
		$key = substr(__FUNCTION__,5);
		$cfg = &$this->cfgs[$key];
		
		if($mode){//�����б���������
			empty($cfg['side']) && $cfg['side'] = 'C';
			isset($cfg['view']) || $cfg['view'] = 'S';
			empty($cfg['width']) && $cfg['width'] = '60';
			if(empty($cfg['title'])) $cfg['title'] = '��̬';
			$this->titles[$key] = $this->top_title($key,$cfg);
		}else{//�����б�������
			$static_state = empty($data['mspacepath']) ? "<a href=\"?entry=extend&extend=memberstatic&mid={$data['mid']}\" onclick=\"return floatwin('open_mem$key',this)\"><b>����</b></a>" : "<a href=\"?entry=extend&extend=memberstatic&mid={$data['mid']}\" onclick=\"return floatwin('open_mem$key',this)\">����</a>- <a href=\"?entry=extend&extend=memberstatic&mid={$data['mid']}&bsubmit=1&fmdata[mspacepath]=''\">ɾ��</a>";
			return $mspacepmid && !cls_Permission::noPmReason($data,$mspacepmid) ? $static_state : '��Ȩ��';
		}
	}
}
