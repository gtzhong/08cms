<?php
/**
 *  memsearchsbase.cls.php ��Ա�б���������Ŀ�Ĵ������	 
 *��������������ɸѡֵ��չʾ����SQL ����Ϊextend_example/libs/xxxx/memsearch.cls.php�Ļ���
 *  
 *
 * @copyright			(C) 2008-2013 08cms
 * @license				http://www.08cms.com/license/
 * @lastmodify			2013-2-23
 */
!defined('M_COM') && exit('No Permisson');
class cls_memsearchsbase{
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
		if(empty($this->A['orderby'])) $this->A['orderby'] = "{$this->A['pre']}mid DESC";
		$this->orderby = $this->A['orderby'];
		
		//�������̨Ȩ��(�����ɫ��ָ����������ģ��)
		if(!$this->mc){
			global $a_mchids;
			if(!empty($this->A['mchid'])){//ָ��ģ��ʱ
				if(!array_intersect(array(-1,$this->A['mchid']),$a_mchids)) $this->no_list = 1;
				else $this->wheres['mchid'] = "{$this->A['pre']}mchid='{$this->A['mchid']}'";
			}elseif(empty($a_mchids)){//�����ɫ��δ���ÿɹ����ģ��
				$this->no_list = 1;
			}elseif(!in_array(-1,$a_mchids) && $a_mchids){//ֻȡ��������ģ����ʾ����
				$this->wheres['mchid'] = "{$this->A['pre']}mchid ".multi_str($a_mchids);
			}
		}
	}
	
	
	/**
	* ���ɸѡ������Ŀ
	*
	* @ex $oL->s_additem('keyword',array('fields' => array(),));
	*
	* @param    string     $key  ��Ŀ�ؼ��� �����Լ�������Ŀ����Ҫ������Ӧ������Ҳ����������ֵ �ȵ�
						keyword�������ؼ���
						checked���������
						indays������������
						outdays������ǰ����
						nchid: ������Աģ��
						mctid: ��֤����
	* @param    array      $cfg  ��Ŀ���ò��� ��ѡ��Ĭ��Ϊ�� 
						type����������֮�󣬻�����function type_{type}()������,other,ugid,field����
						fields�������ĵ�ģ���ض������ݿ��ֶΣ������Ϲؼ���������һ��Ϊ���⣬��Ա���ĵ�ID
						
	*/
	public function additem($key = '',$cfg = array()){//��׷��$key��$cfg֮��Ĵ���
		if(!$key) return false;
		if(!isset($cfg['pre'])) $cfg['pre'] = $this->A['pre'];
		$this->cfgs[$key] = $cfg;
		$args = array_slice(func_get_args(),2);//key,cfg֮��Ĳ����������������
		if(!$this->call_method("user_$key",$args)){//���Ʒ���
			if('ugid' == substr($key,0,4)){//ͨ�õ���ϵ����
				$type = 'ugid';
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
		$field = cls_cache::Read('mfield',$this->A['mchid'],$key);
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
	
	// ����Ա���������ų�������ϵ
	// skip�������html����; eg: $oL->s_additem("ugid3",array('skip'=>1)); 
	protected function type_ugid($key){
		$grouptypes = cls_cache::Read('grouptypes');
		$gtid = max(0,intval(str_replace('ugid','',$key)));
		if(!$gtid || ($gtid== 2) || empty($grouptypes[$gtid])) return $this->del_item($key);
		if(!($ugidsarr = ugidsarr($gtid,$this->A['mchid'],1))) return $this->del_item($key);
		
		$cfg = &$this->cfgs[$key];
		$this->init_item($key,'int+',1);

		if(!empty($this->nvalue[$key])){//����Դ���wherestr
			$this->wheres[$key] = "{$cfg['pre']}grouptype{$gtid}='{$this->nvalue[$key]}'";
		}
		if(!empty($cfg['skip'])) return; // �����html(skip)����
		if(empty($cfg['hidden'])){
			$grouptypes = cls_cache::Read('grouptypes');
			$title = empty($cfg['title']) ? "-{$grouptypes[$gtid]['cname']}-" : $cfg['title'];
			$this->htmls[$key] = $this->input_select($key,array('0' => $title) + $ugidsarr,$this->nvalue[$key]);
		}else $this->htmls[$key] = $this->input_hidden($key,$this->nvalue[$key]);
	}
	
	
	// ids��ָ�������г���id�����ڹ����� $oL->s_additem("ccid3",array('ids'=>$ccid3s));
	// skip�������html����; eg: $oL->s_additem("ccid3",array('skip'=>1)); 
	// self����Ϊ1ʱ�����Զ���ϵ������Ĭ��Ϊ�ų��Զ���ϵ��
	//custom �Զ����ֶ� �����Զ����ֶι�����ϵ�����
	private function type_ccid_final($key,$custom){
		$cotypes = cls_cache::Read('cotypes');
		$coid = max(0,intval(str_replace('ccid','',$key))); //������ϵ������
		
		empty($custom) && $custom = $key;
		
		if(!$coid || empty($cotypes[$coid])) return $this->del_item($custom);
		
		$cfg = &$this->cfgs[$custom]; //�Զ����ֶε�����
		if(empty($cfg['self']) && $cotypes[$coid]['self_reg']) return $this->del_item($custom);
		
		$this->init_item($custom,'int+',1);

		if(!empty($this->nvalue[$custom]) && $ccids = sonbycoid($this->nvalue[$custom],$coid)){//����Դ���wherestr
			if($cnsql = cnsql($coid,$ccids,$cfg['pre'])) $this->wheres[$custom] = $cnsql;
		}

		if(!empty($cfg['skip'])) return; // �����html(skip)����
		if(empty($cfg['hidden'])){
			isset($cotypes[$coid]['cname']) || $cotypes[$coid]['cname']='';
			$title = empty($cfg['title']) ? "-{$cotypes[$coid]['cname']}-" : $cfg['title'];
			$ids = empty($cfg['ids']) ? array() : $cfg['ids']; //ids��ָ������ϵID
			$this->htmls[$custom] = '<span>'.cn_select($custom,array(
			'value' => $this->nvalue[$custom],
			'coid' => $coid,
			'notip' => 1,
			'addstr' => $title,
			'vmode' => 0,
			'framein' => 1,
			'ids' =>$ids,)).'</span> ';	
		}else $this->htmls[$custom] = $this->input_hidden($custom,$this->nvalue[$custom]);
	}
	
	protected function type_other($key){
		$field = empty($this->A['fields']) ? cls_cache::Read('mfield',$this->A['mchid'],$key) : @$this->A['fields'][$key];
		//�Զ����ֶι�����ϵ����
		if( $field['coid'] && $field['datatype'] == 'cacc'){
			 $coid = max(0,intval($field['coid']));
			 return $this->type_ccid_final("ccid$coid",$key);
		}
		//����������������
		$cfg = &$this->cfgs[$key];
		$this->init_item($key,'int');
		$this->htmls[$key] = $this->input_hidden($key,$this->nvalue[$key]);
	}
	
	//��Աģ��ɸѡ��ָ��ģ��ʱ��Ч
	//ids :array���� ��Ҫɸѡ��mchid  $oL->s_additem("nchid",array('ids'=>array()));
	protected function user_nchid(){
		$key = substr(__FUNCTION__,5);
		if($this->A['mchid'] || !$mchidsarr = cls_mchannel::mchidsarr()) return $this->del_item($key);
		
		$cfg = &$this->cfgs[$key];
		$this->init_item($key,'int+',1);
		
		if(!empty($this->nvalue[$key])){//����Դ���wherestr
			$this->wheres[$key] = "{$cfg['pre']}mchid='{$this->nvalue[$key]}'";
		}
		if(empty($cfg['hidden'])){
			$title = empty($cfg['title']) ? "-��Ա����-" : $cfg['title'];
			$this->htmls[$key] = $this->input_select($key,array('0' => $title) + $mchidsarr,$this->nvalue[$key]);
		}else $this->htmls[$key] = $this->input_hidden($key,$this->nvalue[$key]);
	}
	
	protected function user_mctid(){//��Ա��֤
		$key = substr(__FUNCTION__,5);
		if(!$mctidsarr = $this->mctidsarr()) return $this->del_item($key);
		
		$cfg = &$this->cfgs[$key];
		$this->init_item($key,'int+',1);
		
		if(!empty($this->nvalue[$key]) && isset($mctidsarr[$this->nvalue[$key]])){//����Դ���wherestr
			$this->wheres[$key] = "{$cfg['pre']}mctid{$this->nvalue[$key]}<>0";
		}
		if(empty($cfg['hidden'])){
			$title = empty($cfg['title']) ? "-��Ա��֤-" : $cfg['title'];
			$this->htmls[$key] = $this->input_select($key,array('0' => $title) + $mctidsarr,$this->nvalue[$key]);
		}else $this->htmls[$key] = $this->input_hidden($key,$this->nvalue[$key]);
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
	
	protected function user_orderby(){//���Դ���$cfg['options']
		$key = substr(__FUNCTION__,5);
		$cfg = &$this->cfgs[$key];
		$this->init_item($key,'int',1);
		if(empty($cfg['options'])){
			$title = empty($cfg['title']) ? "-Ĭ������-" : $cfg['title'];
			$cfg['options'] = array(
				0 => array($title,$this->A['orderby']),
				1 => array('�ϴε�¼ʱ��',$cfg['pre'].'lastvisit DESC'),
				2 => array('�ϴλʱ��',$cfg['pre'].'lastactive DESC'),
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
			$this->wheres[$key] = $cfg['pre']."regdate>'".($timestamp - 86400 * $this->nvalue[$key])."'";
		}
		if(empty($cfg['hidden'])){
			$title = empty($cfg['title']) ? "����" : $cfg['title'];
			$this->htmls[$key] = $this->input_text($key,$this->nvalue[$key],'ע��ʱ��',2).$title;
		}else $this->htmls[$key] = $this->input_hidden($key,$this->nvalue[$key]);
	}
	
	protected function user_outdays(){
		$key = substr(__FUNCTION__,5);
		$cfg = &$this->cfgs[$key];
		$this->init_item($key,'int+',1);
		if($this->nvalue[$key]){//����Դ���wherestr
			global $timestamp;
			$this->wheres[$key] = $cfg['pre']."regdate<'".($timestamp - 86400 * $this->nvalue[$key])."'";
		}
		if(empty($cfg['hidden'])){
			$title = empty($cfg['title']) ? "��ǰ" : $cfg['title'];
			$this->htmls[$key] = $this->input_text($key,$this->nvalue[$key],'ע��ʱ��',2).$title;
		}else $this->htmls[$key] = $this->input_hidden($key,$this->nvalue[$key]);
	}
	
	//fields�����������ֶ�(�������ǰ׺)������ֶ�ʹ�����鴫��
	protected function user_keyword(){
		$key = substr(__FUNCTION__,5);
		$cfg = &$this->cfgs[$key];
		$this->init_item($key,'keyword',1);
		
		if(empty($cfg['fields'])){
			$fields = array($cfg['pre'].'mname' => '��Ա�ʺ�',$cfg['pre'].'mid' => '��ԱID');
		}else $fields = $cfg['fields'];
		
		$mode_key = "mode_{$key}";
		if(!empty($this->nvalue[$key])){//����Դ���wherestr
			$i = 0;
			foreach($fields as $k => $v){
				if($i++ == $this->nvalue[$mode_key]){ 
					$this->wheres[$key] = $k=="m.mid" ? $k."=".intval($this->nvalue[$key])."" : $k.sqlkw($this->nvalue[$key]); 
				}
			}
		}
		$narr = array();$i = 0;
		foreach($fields as $k => $v) $narr[$i++] = $v;
		$this->htmls[$key] = $this->input_select($mode_key,$narr,empty($this->nvalue[$mode_key]) ? 0 : $this->nvalue[$mode_key]);
		$this->htmls[$key] .= $this->input_text($key,$this->nvalue[$key],'����',10);
	}
	
	//$nowhere=1ʱ����Ҫ����wheres
	protected function init_item($key,$type = '',$nowhere = 0){
		$cfg = &$this->cfgs[$key];
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
	
	protected function mctidsarr(){
		$mctypes = cls_cache::Read('mctypes');
		$re = array();
		foreach($mctypes as $k => $v){
			if(empty($this->A['mchid']) || in_array($this->A['mchid'],explode(',',$v['mchids']))){
				$re[$k] = $v['cname'];
			}
		}
		return $re;
	}
	
}
