<?php
/**
 *  arccolsbase.cls.php �б�������Ŀ�����ݴ������ʾ����	 
 *
 *
 * @copyright			(C) 2008-2013 08cms
 * @license				http://www.08cms.com/license/
 * @lastmodify			2013-2-23
 */
!defined('M_COM') && exit('No Permisson');
class cls_arccolsbase{
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
	
	
	
	/**
	* ����б���Ŀ
	*
	* @ex  $oL->m_additem('clicks',array('type' => 'input','title'=>'�����','width'=>50,'view'=>'H','w' => 3,));
	*
	* @param    string     $key  ��Ŀ�ؼ��� Ĭ��Ϊ��key��ֵһ��Ϊ���ݿ�ĳ���ֶε����ƣ�ϵͳ�Ѿ�����ĳЩ�ֶε���ʾ�ʹ�������keyҲ�������Լ���չ�����ƣ���Ҫͬʱ��չ�����Ŀ����ʾ�ʹ�����
	* @param    array      $cfg  ��Ŀ���ò��� ��ѡ��Ĭ��Ϊ�� 
						type����������֮�󣬻�����function type_{type}()������,date,bool,url,select,checkbox,input����
						title�������б���
						width���п��
						side������λ��(L/R/C)��Ĭ��ΪC
						view�����Ƿ����أ���(��ʾ��������)/S(��������)/H(Ĭ������)
						url������url�������κε�ǰ������ʹ��ռλ��{xxxx}����
						winsize-���ڴ�С����:��500,300,��url��ʹ��
						mtitle�����ı��⣬��url,other�ȷ�������ʾ����;��:{dj}Ԫ/M2,{zj}��Ԫ
						umode��url�򿪷�ʽ,0(Ĭ�ϸ�����)/1(�´���)/2(������)
						coid����ϵid
						len���������ݽ�ȡ���ȣ���subject
						icon����ϵ�Ƿ�ͼ����ʾ
						num����ѡ�����ʾ����
						empty��Ϊ��ʱ����ʾ����
						fmt��ʱ�䷽���ĸ�ʽ������
						aclass��<a>��ʽ,url,subject,ccid�е�<a>ʹ��
						onclick��type_image��ʹ��,���ͼƬ,��checkbox����ѡ�в���,Ĭ��Ϊ1
						showEnd: =0/1,���type_date(ʱ��)���� ������ʱ�䷽ʽ������ʾ(����ɫ����ʾ<����>), Ĭ��enddate���˷�ʽ��ʾ
	
	//��ȫ���Ʒ�����user_$key����Ϊ����ʹ�õķ���
	//ϵͳ���÷�����type_$key������������µ��ã�������type��δ���Ƶ���ϵ������δָ����������ʾ��
	*/
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
		if('ccid' == substr($key,0,4)){//ͨ�õ���ϵ����
			if(strlen($key)<9) $re = $this->type_ccid($key,$mode,$data);
			//��ϵ����ʱ�䴦��
			else $re = $this->type_cciddate($key,$mode,$data); 
		}elseif(!empty($this->cfgs[$key]['type'])){//����ר�����͵ķ���
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
	//showEnd:������ʱ�䷽ʽ������ʾ(����ɫ����ʾ<����>), Ĭ��enddate���˷�ʽ��ʾ
	protected function type_date($key = '',$mode = 0,$data = array()){
		$cfg = &$this->cfgs[$key];	
		if($mode){//�����б���������
			$arr = array('createdate' => '�������','refreshdate' => 'ˢ������','updatedate' => '��������','enddate' => 'ʧЧ����',);
			if(empty($cfg['title']) && isset($arr[$key])) $cfg['title'] = $arr[$key];
			$this->titles[$key] = $this->top_title($key,$cfg);
		}else{
			// enddateĬ�ϰ�isenddate��ʽ��ʾ����ʾ<����>, �����ɫ��ʾ�������
			$showEnd = isset($cfg['showEnd']) ? $cfg['showEnd'] : ($key=='enddate' ? 1 : 0);
			global $timestamp;
			$null = isset($cfg['empty']) ? $cfg['empty'] : ($showEnd ? '&lt;����&gt;' : '-');
			$fmt = isset($cfg['fmt']) ? $cfg['fmt'] : 'Y-m-d';
			$sval = date($fmt,$data[$key]);
			if($showEnd){
				$cval = date($fmt,$timestamp);
				if($cval>$sval){ $sval = "<span style='color:#FF0000'>$sval</span>"; } //�Ѿ�����:��ɫ
				elseif($cval==$sval){ $sval = "<span style='color:#0000FF'>$sval</span>"; } //�������:��ɫ
			}
			return empty($data[$key]) ? $null : $sval;
		}
	}
	
	//��ϵ����ʱ��  ���� $oL->m_additem("ccid{$k}date",array('view'=>'H','title'=>'�ö�����','empty'=>'����'));
	//fmt:ʱ�䷽���ĸ�ʽ������
	protected function type_cciddate($key = '',$mode = 0,$data = array()){
		$cotypes = cls_cache::Read('cotypes');
		if(!($coid = max(0,intval(str_replace(array('ccid','date'),'',$key)))) || empty($cotypes[$coid]) || !in_array($coid,$this->A['coids']) || $cotypes[$coid]['self_reg']) return $this->del_item($key);
		#if($cotypes[$coid]['emode']<=0 ) return '-';
		
		$cfg = &$this->cfgs[$key];
		$cfg['coid'] = $coid;	
		if($mode){//�����б���������
			$cfg['title'] = empty($cfg['title']) ? '��ϵ����' : $cfg['title'];
			$this->titles[$key] = $this->top_title($key,$cfg);
		}else{
			return empty($data[$key]) ? ((isset($cfg['empty']) && !empty($data['ccid'.$coid])) ? $cfg['empty'] : '-') : date(isset($cfg['fmt']) ? $cfg['fmt'] : 'Y-m-d',$data[$key]);
		}
	}
	
	//����ֵ�ķ�������ʾY/-
	//cfg['mtitle'] = '���' �� 'OK'
	protected function type_bool($key = '',$mode = 0,$data = array()){
		$cfg = &$this->cfgs[$key];	
		if($mode){//�����б���������
			$this->titles[$key] = $this->top_title($key,$cfg);
		}else{
			$mtitle = isset($cfg['mtitle']) ? key_replace($cfg['mtitle'],$data) : 'Y';
			return empty($data[$key]) ? (isset($cfg['empty']) ? $cfg['empty'] : '-') : $mtitle;
		}
	}
	
	//�����text�����б�������Ŀ���ã�wָ�������Ŀ��
	protected function type_input($key = '',$mode = 0,$data = array()){
		$cfg = &$this->cfgs[$key];	
		if($mode){//�����б���������
			$this->titles[$key] = $this->top_title($key,$cfg);
		}else{//�б����ڵı���
			$w = empty($cfg['w']) ? 4 : max(1,intval($cfg['w']));
			return $this->input_text("{$this->A['mfm']}[{$data['aid']}][$key]",isset($data[$key]) ? $data[$key] : '',$w);
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
			return $this->input_checkbox("{$this->A['mfm']}[{$data['aid']}][$key]",empty($data[$key]) ? 0 : $data[$key]);
		}
	}
	
	//URL����
	//winsize-���ڴ�С����:��500,300,��url��ʹ��
	//aclass-<a>��ʽ
	//empty - $data[$key]Ϊ��ʱ���滻ֵ
	protected function type_url($key = '',$mode = 0,$data = array()){
		$cfg = &$this->cfgs[$key];	
		if($mode){//�����б���������
			$this->titles[$key] = $this->top_title($key,$cfg);
		}else{
			$addstr = empty($cfg['umode']) ? "onclick=\"return floatwin('open_arc$key',this".(empty($cfg['winsize']) ? '' : ','.$cfg['winsize']).")\"" : ($cfg['umode'] == 1 ? "target=\"_blank\"" : '');
			if(isset($cfg['empty']) && empty($data[$key])) $data[$key] = $cfg['empty'];
			return "<a ".(isset($cfg['aclass']) ? "class='$cfg[aclass]'" : "class='scol_url_pub'")." href=\"".key_replace($cfg['url'],$data)."\" {$addstr}>".(empty($cfg['mtitle']) ? (isset($data[$key]) ? $data[$key] : $key) : key_replace($cfg['mtitle'],$data))."</a>";
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
	
	//��ϵ��ͨ�÷���
	//title-���⣬icon�Ƿ�ͼ����ʾ��num��ѡ��ʱ�����ʾ����
	//url����,��������,���ڹ�����ϵ���������
	//winsize-���ڴ�С����:��500,300,��url��ʹ��
	//aclass-<a>��ʽ
	protected function type_ccid($key = '',$mode = 0,$data = array()){
		$cotypes = cls_cache::Read('cotypes');
		if(!($coid = max(0,intval(str_replace('ccid','',$key)))) || empty($cotypes[$coid]) || !in_array($coid,$this->A['coids']) || $cotypes[$coid]['self_reg']) return $this->del_item($key);
		
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
	
	//ͼƬ��ʾ
	//cfgs[onclick]��Ϊ��ʱ������js
	public function type_image($key = '',$mode = 0,$data = array()){
		global $cms_abs;
		$cfg = &$this->cfgs[$key];
		if($mode){//�����б���������
			$this->titles[$key] = $this->top_title($key,$cfg);
		}else{
			$thumb = view_checkurl($data[$key]);
			$cfg['onclick'] = isset($cfg['onclick']) ? $cfg['onclick'] : 1;
			$_onclick = empty($cfg['onclick'])?'':"onclick=\"_img_affect_checkbox($data[aid]);\"";
			return "<img src=\"".$thumb."\" style=\"float:left;display:block; \" width=\"".$cfg['width']."\"  height=\"".$cfg['height']."\" ".$_onclick.">";
		}	
	}

	/**
	 * �����ֶΣ�����ѧ����������Ϊ��ͨ������ʾ��decimals(С��λ��)��dec_point(С���ָ��)��thousands_sep(ǧ���ָ���)
	 * $oL->m_additem("zj",array('type'=>'number','mtitle'=>'{zj}��Ԫ'));//170000000��Ԫ
	 * $oL->m_additem("zj",array('type'=>'number','thousands_sep'=>',','mtitle'=>'{zj}��Ԫ'));//170,000,000
	 */	
	public function type_number($key='',$mode=0,$data=array()){
		$cfg = &$this->cfgs[$key];		
		if($mode){//�����б���������
			$this->titles[$key] = $this->top_title($key,$cfg);
		}else{
			$decimals = isset($cfg['decimals']) ? intval($cfg['decimals']) : 0;
			$dec_point = isset($cfg['dec_point']) ? $cfg['dec_point'] : '.';
			$thousands_sep = isset($cfg['thousands_sep']) ? $cfg['thousands_sep'] : '';			
			$data[$key] = number_format($data[$key],$decimals,$dec_point,$thousands_sep);			
			return isset($cfg['mtitle']) ? key_replace($this->cfgs[$key]['mtitle'],$data) : $data[$key];
		}
	}
	
	//��ͨ�ֶ�,��$key����ȡ��������
	//empty��Ϊ��ʱ��ʾ����
	//mtitle: ��ʾģ��,��:{dj}Ԫ/M2,{zj}��Ԫ
	protected function type_other($key = '',$mode = 0,$data = array()){
		$cfg = &$this->cfgs[$key];	
		if($mode){//�����б���������
			$this->titles[$key] =  $this->top_title($key,$cfg);
		}else{
			$empty = (isset($cfg['empty']) ? $cfg['empty'] : '-');
			$data[$key] = empty($data[$key]) ? $empty : (isset($cfg['mtitle']) ? key_replace($cfg['mtitle'],$data): $data[$key]);
			$len = empty($cfg['len']) ? '' : $cfg['len'];
			return $len ? htmlspecialchars(cls_string::CutStr($data[$key],$len)) : $data[$key];
		}
	}
	
	/*�ĵ�ID��̬��ַ
	* @example  $oL->m_additem('aid'); //Ĭ��,�ĵ���̬����
				$oL->m_additem('aid',array('url'=>"{$cms_abs}archive.php?aid={aid}",'title'=>'ID','mtitle'=>'[{aid}]'));
				$oL->m_additem('mid',array('url'=>"#",'title'=>'ID')); //��Ҫ����
				$oL->m_additem('mid',array('mc'=>"1",'title'=>'ID')); //��Ա�ռ䶯̬����
    *
	*/
	protected function user_aid($mode = 0,$data = array()){
		$key = substr(__FUNCTION__,5);
		$cfg = &$this->cfgs[$key];
		if($mode){//�����б���������
			$this->titles[$key] =  isset($cfg["title"])&&!empty($cfg["title"]) ? $cfg["title"] : 'ID' ;
		}else{
			$cms_abs = cls_env::mconfig('cms_abs');
			$re = $data[$key];
			if(!empty($cfg['mc'])){ //��Ա�ռ䶯̬����    
				$re = "<a target='_blank' title='��Ա�ռ䶯̬��ַ'".(isset($cfg['aclass']) ? "class='$cfg[aclass]'" : "class='scol_url_pub'")." href=\"".(isset($cfg['url']) ? key_replace($cfg['url'],$data) : $cms_abs.'mspace/archive.php?mid='.$data['mid'].'&aid='.$re)."\" >$re</a>";
			}elseif(@$cfg['url']!='#'){  // ����Ҫurl����
				$re = "<a target='_blank' title='����鿴��̬��ַ'".(isset($cfg['aclass']) ? "class='$cfg[aclass]'" : "class='scol_url_pub'")." href=\"".(isset($cfg['url']) ? key_replace($cfg['url'],$data) : $cms_abs.'archive.php?aid='.$re)."\" >$re</a>";
			}
			return $re;			
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
			return "<input class=\"checkbox\" type=\"checkbox\" name=\"selectid[$data[aid]]\" value=\"$data[aid]\">";
		}
	}
	
	//�ĵ�����
	//addno:ָ��ʹ���ĸ�����ҳ��url
	//aclass-<a>��ʽ
	//nothumb:Ĭ��Ϊ��,��ʾ���; ����Ϊ1ʱ,����ʾ��ɫ����ͼ���(�������ͼƬ�б��û������thumb�ֶ�ʱʹ��)
	protected function user_subject($mode = 0,$data = array()){
		$key = substr(__FUNCTION__,5);
		$cfg = &$this->cfgs[$key];
		if($mode){//�����б���������
			empty($cfg['side']) && $cfg['side'] = 'L';
			!isset($cfg['view']) && $cfg['view'] = 'S';
			if(empty($cfg['title'])) $cfg['title'] = '����';
			$this->titles[$key] = $this->top_title($key,$cfg);
		}else{//�����б�������
			$re = (empty($cfg['nothumb']) && !empty($data['thumb']) ? '<font style="color:red">ͼ&nbsp;</font>' : '');
			$addno = empty($cfg['addno']) ? 0 : max(0,intval($cfg['addno']));
			$url = '';
			if(empty($cfg['url'])){
				if(!empty($cfg['mc'])){  //��Ա�ռ�    
					cls_ArcMain::Url($data,-1);
					$url = $data['marcurl'];
				}
				else $url = cls_ArcMain::Url($data,$addno);
			}elseif($cfg['url'] == '#'){  // ����Ҫurl����
				if(!empty($data['color'])) $re .= "<span style=\"color:{$data['color']}\">";
				$len = empty($cfg['len']) ? 40 : $cfg['len'];
				if(!empty($data['thumb'])) $len -= 4;
				$re .= htmlspecialchars(cls_string::CutStr($data['subject'],$len))."</span>";
				return $re;
			}else $url = key_replace($cfg['url'],$data); //�����Զ���url��ʽ
			$re .= "<a ".(isset($cfg['aclass']) ? "class='$cfg[aclass]'" : "class='scol_subject'")." href=\"$url\" target=\"_blank\"";
			
			if(!empty($data['color'])) $re .= " style=\"color:{$data['color']}\"";
			
			$len = empty($cfg['len']) ? 40 : $cfg['len'];
			if(!empty($data['thumb'])) $len -= 4;
			$re .= " title=\"".htmlspecialchars($data['subject'])."\">".htmlspecialchars(cls_string::CutStr($data['subject'],$len))."</a>";
			return $re;
		}
	}
	
	/**
	 *��Ŀ����
	 */
	protected function user_caid($mode = 0,$data = array()){
		$key = substr(__FUNCTION__,5);
		$cfg = &$this->cfgs[$key];
		if($mode){
			if(empty($cfg['title'])) $cfg['title'] = '��Ŀ';
			$this->titles[$key] =  $this->top_title($key,$cfg);
		}else{
			$re = ($catalog = cls_cache::Read('catalog',$data['caid'])) ? $catalog['title'] : '';
			$re || $re = isset($cfg['empty']) ? $cfg['empty'] : '-';
			return $re;
		}
	}
	
	//�Ƿ���Ч
	//cfg['mtitle'] = '�ϼ�' �� '��Ч'
	protected function user_valid($mode = 0,$data = array()){
		$key = substr(__FUNCTION__,5);
		$cfg = &$this->cfgs[$key];	
		if($mode){//�����б���������
			if(empty($cfg["title"])) $cfg['title'] = '��Ч';
			$this->titles[$key] = $this->top_title($key,$cfg);
		}else{//�����б�������
			global $timestamp;
			$mtitle = isset($cfg['mtitle']) ? key_replace($cfg['mtitle'],$data) : 'Y';
			return empty($data['enddate']) || $data['enddate'] > $timestamp ? $mtitle : (isset($cfg['empty']) ? $cfg['empty'] : '-');
		}
	}
	
	//ģ���б� $oL->m_additem('chid',array('title'=>'ģ��',));
	protected function user_chid($mode = 0,$data = array()){
		$key = substr(__FUNCTION__,5);
		$cfg = &$this->cfgs[$key];	
		if($mode){//�����б���������
			if(empty($cfg["title"])) $cfg['title'] = 'ģ��';
			$this->titles[$key] = $this->top_title($key,$cfg);
		}else{//�����б�������
			isset($channels) || $channels = array();
			$channels = cls_channel::Config();
			return empty($data['chid']) ? '-' : @$channels[$data['chid']]['cname'];
		}
	}
}
