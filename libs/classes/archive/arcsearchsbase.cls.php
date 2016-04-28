<?php
/**
 *  arcsearchsbase.cls.php  �б���������Ŀ�Ĵ����������	 
 *   ��������������ɸѡֵ��չʾ����SQL ����Ϊextend_example/libs/xxxx/asearch.cls.php�Ļ���
 *
 * @copyright			(C) 2008-2013 08cms
 * @license				http://www.08cms.com/license/
 * @lastmodify			2013-2-23
 */

!defined('M_COM') && exit('No Permisson');
class cls_arcsearchsbase{
	protected $mc = 0;//��Ա����
	public $A = array();//��ʼ���������
	public $cfgs = array();//����������
	public $nvalues = array();//��ǰɸѡֵ
	public $wheres = array();//����Ŀ����
	public $filters = array();//url���ִ�
	public $htmls	= array();//���������html����
	public $orderby = '';//�����ִ�
	public $no_list = false;//��ΪȨ�޵�ԭ�򣬲�ѯ������ҪΪ��
	
    function __construct($cfg = array()){
		$this->mc = defined('M_ADMIN') ? 0 : 1;
		$this->A = $cfg;	
		if(empty($this->A['orderby'])){
			if($this->A['isab'] == 1){
				if($this->A['abtbl']){
					$this->A['orderby'] = "{$this->A['bpre']}inorder DESC";	
				}else{
					$this->A['orderby'] = "{$this->A['pre']}inorder{$this->A['arid']} DESC";	
				}
			}else $this->A['orderby'] = "{$this->A['pre']}aid DESC";
		}
		$this->orderby = $this->A['orderby'];
	}
	
	
	/**
	* ���ɸѡ������Ŀ
	*
	* @ex $oL->s_additem('keyword',array('fields' => array(),));
	*
	* @param    string     $key  ��Ŀ�ؼ��� �����Լ�������Ŀ����Ҫ������Ӧ������Ҳ����������ֵ �ȵ�
						keyword�������ؼ���
						caid��������Ŀ
						checked���������
						valid����Ч������
						ccid$k����ϵ����
						orderby����������
						indays������������
						outdays������ǰ����
						
	* @param    array      $cfg  ��Ŀ���ò��� ��ѡ��Ĭ��Ϊ�� 
						type����������֮�󣬻�����function type_{type}()������,other,ccid,field����
						fields�������ĵ�ģ���ض������ݿ��ֶΣ������Ϲؼ���������һ��Ϊ���⣬��Ա���ĵ�ID
						
	*/
	public function additem($key = '',$cfg = array()){//��׷��$key��$cfg֮��Ĵ���
		if(!$key) return false;
		if(!isset($cfg['pre'])) $cfg['pre'] = $this->A['pre'];
		$this->cfgs[$key] = $cfg;
		$args = array_slice(func_get_args(),2);//key,cfg֮��Ĳ����������������
		if(!$this->call_method("user_$key",$args)){//���Ʒ���
			if('ccid' == substr($key,0,4)){//ͨ�õ���ϵ����
				$type = 'ccid';
			}elseif(!empty($this->cfgs[$key]['type'])){//������type�ķ���
				$type = $this->cfgs[$key]['type'];
			}else $type = 'other';//ֻ���������Ĵ���
			$this->call_method("type_$type",array($key) + $args);
		}
	}
	
	protected function call_method($func,$args = array()){//���Զ������Ӵ���
		if(method_exists($this,$func)){
			call_user_func_array(array(&$this,$func),$args);
			return true;
		}else return false;
	}
	
	protected function del_item($key){
		unset($this->cfgs[$key]);
		return false;
	}	
	
	protected function type_field($key){
		$field = empty($this->A['fields']) ? cls_cache::Read('field',$this->A['chid'],$key) : @$this->A['fields'][$key];
		if(!$field || !in_array($field['datatype'],array('select','mselect','cacc',))) return $this->del_item($key);
		$cfg = &$this->cfgs[$key];
		$a_field = new cls_field;
		$field['issearch'] = 1;//ǿ��Ϊ�������ֶ�
		$a_field->init($field,@$GLOBALS[$key]);
		$a_field->deal_search($cfg['pre']);
		if(!empty($a_field->ft)) $this->filters += $a_field->ft;
		if(!empty($a_field->searchstr)) $this->wheres[$key] = $a_field->searchstr;
		unset($a_field);
		
		if(empty($cfg['hidden'])){
			$sarr = cls_field::options_simple($field,array('blank' => '&nbsp; &nbsp; '));
			$title = empty($cfg['title']) ? "-{$field['cname']}-" : $cfg['title'];
			$this->htmls[$key] = $this->input_select($key,array('' => $title) + $sarr,@$GLOBALS[$key]);
		}else $this->htmls[$key] = $this->input_hidden($key,@$GLOBALS[$key]);
	}
	
	// ids��ָ�������г���id�����ڹ����� $oL->s_additem("ccid3",array('ids'=>$ccid3s));
	// skip�������html����; eg: $oL->s_additem("ccid3",array('skip'=>1)); 
	// self����Ϊ1ʱ�����Զ���ϵ������Ĭ��Ϊ�ų��Զ���ϵ��
	protected function type_ccid($key){
		$cotypes = cls_cache::Read('cotypes');
		$coid = max(0,intval(str_replace('ccid','',$key)));
		if(!$coid || empty($cotypes[$coid]) || !in_array($coid,$this->A['coids'])) return $this->del_item($key);
		
		$cfg = &$this->cfgs[$key];
		if(empty($cfg['self']) && $cotypes[$coid]['self_reg']) return $this->del_item($key);
		
		$this->init_item($key,'int+',1);

		if(!empty($this->nvalue[$key]) && $ccids = sonbycoid($this->nvalue[$key],$coid)){//����Դ���wherestr
			if($cnsql = cnsql($coid,$ccids,$cfg['pre'])) $this->wheres[$key] = $cnsql;
		}
		if(!empty($cfg['skip'])) return; // �����html(skip)����
		if(empty($cfg['hidden'])){
			isset($cotypes[$coid]['cname']) || $cotypes[$coid]['cname']='';
			$title = empty($cfg['title']) ? "-{$cotypes[$coid]['cname']}-" : $cfg['title'];
			$ids = empty($cfg['ids']) ? array() : $cfg['ids']; //ids��ָ������ϵID
			$this->htmls[$key] = '<span>'.cn_select($key,array(
			'value' => $this->nvalue[$key],
			'coid' => $coid,
			'notip' => 1,
			'addstr' => $title,
			'vmode' => 0,
			'framein' => 1,
			'ids' =>$ids,)).'</span> ';	
		}else $this->htmls[$key] = $this->input_hidden($key,$this->nvalue[$key]);
	}
	
	protected function type_other($key){
		//����������������
		$cfg = &$this->cfgs[$key];
		$this->init_item($key,'int');
		$this->htmls[$key] = $this->input_hidden($key,$this->nvalue[$key]);
	}
	/**
	* ��Ŀɸѡ
	*	ncaid ����ǰɸѡ����Ŀkey,��ϵͳ���õ�
	*	caid  �����ⲿ����������Ŀkey
	*/
	 
	protected function user_caid(){
		$key = substr(__FUNCTION__,5);
		$cfg = &$this->cfgs[$key];
		$this->init_item($key,'int+',1);
		$this->nvalue['ncaid'] = empty($GLOBALS['ncaid']) ? 0 : max(0,intval($GLOBALS['ncaid']));
		if(empty($this->nvalue['ncaid']) && !empty($GLOBALS['caid'])){ 
			$this->nvalue['ncaid'] = $GLOBALS['caid']; //Ĭ�϶�λ�ڣ���ǰ��Ŀ��
		}
		$this->filters['ncaid'] = $this->nvalue['ncaid']; 
			
		$caids = empty($this->nvalue['ncaid']) ? sonbycoid($this->nvalue[$key]) : sonbycoid($this->nvalue['ncaid']);
		$caids = empty($caids) ? array(-1) : $caids;
		
		$ids = empty($cfg['ids']) ? array() : $cfg['ids']; //ָ������ĿID
		if(!$this->mc && !$this->A['pid']){
			//�����˹����ɫ����Ŀ����Ȩ��
			global $a_caids;
			if(!in_array(-1,$a_caids)) $caids = in_array(-1,$caids) ? $a_caids : array_intersect($caids,$a_caids);
			if(!$caids) $this->no_list = true;
		}
		if(!in_array(-1,$caids) && $cnsql = cnsql(0,$caids,$cfg['pre'])){
			$this->wheres[$key] = $cnsql;
		}
		if(empty($cfg['hidden'])){
			$title = empty($cfg['title']) ? "--��Ŀ--" : $cfg['title'];
			$this->htmls[$key] = cn_select('ncaid',array(
			'value' => $this->nvalue['ncaid'],
			'chid' => $this->A['chid'],
			'notip' => 1,
			'addstr' => $title,
			'vmode' => 0,
			'framein' => 1,
			'viewp' => -1,//��ȫ������Ч��ϵ
			'ids' => $ids,
			));	
			$this->htmls[$key] = '<span>'.$this->htmls[$key].'</span> ';
			$this->htmls[$key] .= $this->input_hidden($key,$this->nvalue[$key]);
		}else $this->htmls[$key] = $this->input_hidden($key,$this->nvalue[$key]);
	}
	protected function user_chid(){
		$key = substr(__FUNCTION__,5);
		$cfg = &$this->cfgs[$key];
		$this->init_item($key,'int+',1);
		
		if(empty($cfg['hidden'])){
		}else $this->htmls[$key] = $this->input_hidden($key,$this->nvalue[$key]);
	}
	
	//ids :array���� ��Ҫɸѡ��chid  $oL->s_additem("chid",array('ids'=>array()));
	protected function user_nchid(){
		$channels = cls_channel::Config();
		
		$key = substr(__FUNCTION__,5);
		$cfg = &$this->cfgs[$key];
		$this->init_item($key,'int+',1);
		
		$ids = empty($cfg['ids']) ? array() : $cfg['ids']; //ids��ָ�������г���id
		$chidarr = array();
		foreach($channels as $k=>$v) {
			if(!empty($ids))  in_array($k,$ids) && $chidarr[$k] = $v['cname'];
			else $chidarr[$k] = $v['cname'];
		}
		$this->htmls[$key] = "<select style=\"vertical-align: middle;\" name=\"$key\">".makeoption($chidarr,$cfg['chid'])."</select>";
	}
	
	protected function user_checked(){
		$key = substr(__FUNCTION__,5);
		$cfg = &$this->cfgs[$key];
		$this->init_item($key,'int-1');
		if(empty($cfg['hidden'])){
			$title = empty($cfg['title']) ? "-���-" : $cfg['title'];
			$this->htmls[$key] = $this->input_select($key,array('-1' => $title,'0' => 'δ��','1' => '����',),$this->nvalue[$key]);
		}else $this->htmls[$key] = $this->input_hidden($key,$this->nvalue[$key]);
	}
	protected function user_inchecked(){
		$key = substr(__FUNCTION__,5);
		if($this->A['isab'] != 1) return $this->del_item($key);
		
		$cfg = &$this->cfgs[$key];
		$this->init_item($key,'int-1',1);
		if($this->nvalue[$key] != -1){//����Դ���wherestr
			$field = empty($cfg['field']) ? "b.incheck" : $cfg['field'];
			$this->wheres[$key] = "$field='{$this->nvalue[$key]}'";
		}
		if(empty($cfg['hidden'])){
			$title = empty($cfg['title']) ? "-������Ч-" : $cfg['title'];
			$this->htmls[$key] = $this->input_select($key,array('-1' => $title,'0' => '��Ч','1' => '��Ч',),$this->nvalue[$key]);
		}else $this->htmls[$key] = $this->input_hidden($key,$this->nvalue[$key]);
	}
	protected function user_valid(){
		$key = substr(__FUNCTION__,5);
		$cfg = &$this->cfgs[$key];
		$this->init_item($key,'int-1',1);
		if($this->nvalue[$key] != -1){//����Դ���wherestr
			global $timestamp;
			if($this->nvalue[$key]){
				$this->wheres[$key] = "({$cfg['pre']}enddate='0' OR {$cfg['pre']}enddate>'$timestamp')";
			}else{
				$this->wheres[$key] = "{$cfg['pre']}enddate>'0' AND {$cfg['pre']}enddate<'$timestamp'";
			}
		}
		if(empty($cfg['hidden'])){
			$title = empty($cfg['title']) ? "-��Ч-" : $cfg['title'];
			$this->htmls[$key] = $this->input_select($key,array('-1' => $title,'0' => '��Ч','1' => '��Ч',),$this->nvalue[$key]);
		}else $this->htmls[$key] = $this->input_hidden($key,$this->nvalue[$key]);
	}
	protected function user_orderby(){//���Դ���$cfg['options']
		$key = substr(__FUNCTION__,5);
		$cfg = &$this->cfgs[$key];
		$this->init_item($key,'int',1);
		if(empty($cfg['options'])){
			$title = empty($cfg['title']) ? "-����ʽ-" : $cfg['title'];
			$cfg['options'] = array(
				0 => array($title,$this->A['orderby']),
				1 => array('�������',$cfg['pre'].'clicks DESC'),
				2 => array('��ˢ��ʱ��',$cfg['pre'].'refreshdate DESC'),
				3 => array('�����ʱ��',$cfg['pre'].'createdate DESC'),
				4 => array('���ĵ�����',$cfg['pre'].'vieworder DESC'),
			);
		}
		$sarr = array();
		foreach($cfg['options'] as $k => $v){
			$sarr[$k] = $v[0];
			if($this->nvalue[$key] == $k) $this->orderby = $v[1];
		}
		if(empty($cfg['hidden'])){
			$this->htmls[$key] = $this->input_select($key,$sarr,$this->nvalue[$key]);
		}else $this->htmls[$key] = $this->input_hidden($key,$this->nvalue[$key]);
	}
	
	protected function user_indays(){
		$key = substr(__FUNCTION__,5);
		$cfg = &$this->cfgs[$key];
		$this->init_item($key,'int+',1);
		if($this->nvalue[$key]){//����Դ���wherestr
			global $timestamp;
			$this->wheres[$key] = $cfg['pre']."createdate>'".($timestamp - 86400 * $this->nvalue[$key])."'";
		}
		if(empty($cfg['hidden'])){
			$title = empty($cfg['title']) ? "����" : $cfg['title'];
			$title = "<label class='sch_text'>$title</label>";
			$this->htmls[$key] = $this->input_text($key,$this->nvalue[$key],'',2).$title;
		}else $this->htmls[$key] = $this->input_hidden($key,$this->nvalue[$key]);
	}
	
	protected function user_outdays(){
		$key = substr(__FUNCTION__,5);
		$cfg = &$this->cfgs[$key];
		$this->init_item($key,'int+',1);
		if($this->nvalue[$key]){//����Դ���wherestr
			global $timestamp;
			$this->wheres[$key] = $cfg['pre']."createdate<'".($timestamp - 86400 * $this->nvalue[$key])."'";
		}
		if(empty($cfg['hidden'])){
			$title = empty($cfg['title']) ? "��ǰ" : $cfg['title'];
			$title = "<label class='sch_text'>$title</label>";
			$this->htmls[$key] = $this->input_text($key,$this->nvalue[$key],'',2).$title;
		}else $this->htmls[$key] = $this->input_hidden($key,$this->nvalue[$key]);
	}
	
	//fields�����������ֶ�(�������ǰ׺)������ֶ�ʹ�����鴫��
	protected function user_keyword(){
		$key = substr(__FUNCTION__,5);
		$cfg = &$this->cfgs[$key];
		$this->init_item($key,'keyword',1);
		
		if(empty($cfg['fields'])){
			if($this->mc) { 
				$fields = array($cfg['pre'].'subject' => '����',$cfg['pre'].'keywords' => '�ؼ���',$cfg['pre'].'aid' => '�ĵ�ID');
			}else { 
				$fields = array($cfg['pre'].'subject' => '����',$cfg['pre'].'keywords' => '�ؼ���',$cfg['pre'].'aid' => '�ĵ�ID',$cfg['pre'].'mname' => '��Ա');
			}
		}else $fields = $cfg['fields'];
		
		$mode_key = "mode_{$key}";
		if(!empty($this->nvalue[$key])){//����Դ���wherestr
			$i = 0;
			foreach($fields as $k => $v){
				if($i++ == $this->nvalue[$mode_key]){ 
					$this->wheres[$key] = $k=="a.aid" ? $k."=".intval($this->nvalue[$key])."" : $k.sqlkw($this->nvalue[$key]); 
				}
			}
		}
		$narr = array();$i = 0;
		foreach($fields as $k => $v) $narr[$i++] = $v;
		$this->htmls[$key] = $this->input_select($mode_key,$narr,empty($this->nvalue[$mode_key]) ? 0 : $this->nvalue[$mode_key]);
		$this->htmls[$key] .= $this->input_text($key,$this->nvalue[$key],'����',10);
	}
	
	// $nowhere=1ʱ����Ҫ����wheres
	// defval, Ĭ��ֵ�����ǻ����$GLOBALS[$key]��
	protected function init_item($key,$type = '',$nowhere = 0){
		$cfg = &$this->cfgs[$key];
		if(isset($cfg['defval'])) $GLOBALS[$key] = $cfg['defval'];
		switch($type){
			case 'int+'://������
				$this->nvalue[$key] = $GLOBALS[$key] = empty($GLOBALS[$key]) ? 0 : intval($GLOBALS[$key]);
				if($this->nvalue[$key]){
					$this->filters[$key] = $this->nvalue[$key];
					$nowhere || $this->wheres[$key] = "{$cfg['pre']}$key='{$this->nvalue[$key]}'";
				}
				break;
			case 'int-1'://������Ϊ-1������Ϊ����
				$this->nvalue[$key] = $GLOBALS[$key] = isset($GLOBALS[$key]) ? intval($GLOBALS[$key]) : -1;
				if($this->nvalue[$key] != -1){
					$this->filters[$key] = $this->nvalue[$key];
					$nowhere || $this->wheres[$key] = "{$cfg['pre']}$key='{$this->nvalue[$key]}'";
				}
				break;
			case 'keyword'://��ģ�������Ĺؼ���
				$this->nvalue[$key] = $GLOBALS[$key] = empty($GLOBALS[$key]) ? '' : trim($GLOBALS[$key]);
				if($this->nvalue[$key]) $this->filters[$key] = stripslashes($this->nvalue[$key]);
				$mode_key = "mode_$key";
				$this->nvalue[$mode_key] = $GLOBALS[$mode_key] = empty($GLOBALS[$mode_key]) ? 0 : intval($GLOBALS[$mode_key]);
				if($this->nvalue[$mode_key]) $this->filters[$mode_key] = $this->nvalue[$mode_key];
				break;
			case 'str'://�ִ���ȷƥ��
				$this->nvalue[$key] = $GLOBALS[$key] = empty($GLOBALS[$key]) ? '' : trim($GLOBALS[$key]);
				if($this->nvalue[$key]){
					$this->filters[$key] = stripslashes($this->nvalue[$key]);
					$nowhere || $this->wheres[$key] = "{$cfg['pre']}$key='{$this->nvalue[$key]}'";
				}
				break;
			case 'int'://����
				$this->nvalue[$key] = $GLOBALS[$key] = empty($GLOBALS[$key]) ? 0 : intval($GLOBALS[$key]);
				if($this->nvalue[$key]){
					$this->filters[$key] = $this->nvalue[$key];
					$nowhere || $this->wheres[$key] = "{$cfg['pre']}$key='{$this->nvalue[$key]}'";
				}
				break;
		}
	}
	
	protected function input_checkbox($name = '',$sarr = array(),$value = '',$ppr = 0){
		$re = '';$i = 0;
		foreach($sarr as $k => $v){
			$checked = in_array($k,$value) ? 'checked' : '';
			$re .= "<input class=\"checkbox\" type=\"checkbox\" name=\"{$name}[]\" value=\"$k\" $checked>$v";
			$re .= $ppr && !(++$i % $ppr) ?  '<br />' : '';
		}
		return $re;
	}	
	protected function input_text($name = '',$value = '',$title = '',$size = 4){
		return "<input class=\"text\" name=\"$name\" type=\"text\" value=\"$value\" size=\"$size\" style=\"vertical-align: middle;\"".($title ? " title=\"$title\"" : '').">\n";
	}	
	protected function input_select($name = '',$sarr = array(),$value = ''){
		return "<select style=\"vertical-align: middle;\" name=\"$name\">".makeoption($sarr,$value)."</select>\n";
	}
	protected function input_hidden($name = '',$value = ''){
		return "<input type=\"hidden\" name=\"$name\" value=\"$value\">\n";
	}	
	
	
}
