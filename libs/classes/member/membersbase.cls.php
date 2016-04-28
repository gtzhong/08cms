<?php
/**
 *  membersbase.cls.php ��Ա�б����Ĳ�������	 
 *
 *  ���ĵ�������������������ǣ���������ж��м��б���������
 *
 * @copyright			(C) 2008-2013 08cms
 * @license				http://www.08cms.com/license/
 * @lastmodify			2013-2-23
 */
!defined('M_COM') && exit('No Permisson');
class cls_membersbase{
	protected $mc = 0;//��Ա����
	public $A = array();//��ʼ��������š���chid��pre(����ǰ׺),tbl(����),stid(����id)
	public $mchannel = array();//��ǰģ��
	
	//�����й�
	public $oS	= NULL;//�������Ķ���
	public $sqlall = '';//����sql�ִ�
	public $acount = 0;//��Ϣ������ͳ��
	public $filterstr = '';//ɸѡ������url�еĴ����ִ�
	
	//�����б�
	public $oC	= NULL;//�����ݴ���Ķ���
	
	//��������
	public $oO	= NULL;//������Ŀ����Ķ���
	public $rs	= array();//�����ݴ�����ݴ���ѡ�б���������
		
		
    /**
	 * ���캯����ʼ������
	 */
	function __construct($cfg = array()){
		global $db,$tblprefix;
		
		$this->mc = defined('M_ADMIN') ? 0 : 1;
		$this->A = $cfg;
		$this->A['tbl'] = 'members';
		if(empty($this->A['url'])) $this->message('����д���ύURL');
		if(empty($this->A['mode'])) $this->A['mode'] = '';//����ģʽ���ã�''Ϊ��ͨ�����б�'pushload'Ϊ����λ���ع���
		
		if(!$this->A['mode']){//��ͨ�б�ģʽ
			if(!empty($this->A['mchid'])){
				if(!($this->mchannel = cls_cache::Read('mchannel',$this->A['mchid']))) $this->message('��ָ����Ա����');
				if(!in_str('mchid=',$this->A['url'])) $this->A['url'] .= "&mchid={$this->A['mchid']}"; 
			}else $this->A['mchid'] = 0;
			if(empty($this->A['backallow']) && !$this->mc) $this->A['backallow'] = 'member';//��̨����Ȩ��
		}elseif($this->A['mode'] == 'pushload'){//���ͼ���ģʽ
			if(empty($this->A['paid']) || !($pusharea = cls_PushArea::Config($this->A['paid'])) || $pusharea['sourcetype'] != 'members') exit('��ָ����ȷ������λ');
			$this->A['mchid'] = $pusharea['sourceid'];//��Աģ��chid
			if(!($this->mchannel = cls_cache::Read('mchannel',$this->A['mchid']))) $this->message('��ָ����Ա����');
			if(!in_str('paid=',$this->A['url'])) $this->A['url'] .= "&paid={$this->A['paid']}"; 
			if(empty($this->A['backallow']) && !$this->mc) $this->A['backallow'] = 'normal';//��̨����Ȩ��
		}
		
		
		//�߼�����ѡ����infloat
		$this->A['cbsMore'] = $GLOBALS['cbsMore'] = empty($GLOBALS['cbsMore']) ? 0 : intval($GLOBALS['cbsMore']);//�Ƿ���ʾ�߼�������
		if($this->A['cbsMore']) $this->filterstr = "&cbsMore={$this->A['cbsMore']}";
		$this->A['MoreSet'] = 0;//�Ƿ���ָ߼�ѡ��
		$this->filterstr .= empty($GLOBALS['infloat']) ? '' : "&infloat=1"; //�Ƿ񴫵�infloat
		
		//��ǰҳ��
		$this->A['page'] = &$GLOBALS['page'];
		$this->A['page'] = empty($this->A['page']) ? 1 : max(1,intval($this->A['page']));
		if(submitcheck('bfilter')) $this->A['page'] = 1;
		//�б�����
		global $mrowpp,$atpp;
		$this->A['rowpp'] = empty($this->A['rowpp']) ? ($this->mc ? $mrowpp : $atpp) : max(1,intval($this->A['rowpp']));//ÿҳչʾ������
		if(empty($this->A['mfm'])) $this->A['mfm'] = 'fmdata';	//�б�����������ı�����������
		
		//sql��ѯ��ʼ����where��from��selectҪô��ȫ��д��Ҫô����
		
		if(empty($this->A['pre'])) $this->A['pre'] = 'm.';
		if(!$this->A['mode']){//��ͨ�б�ģʽ
			if(empty($this->A['where'])) $this->A['where'] = '';
			if(empty($this->A['from'])) $this->A['from'] = "{$tblprefix}{$this->A['tbl']} ".substr($this->A['pre'],0,-1);
			if(empty($this->A['select'])) $this->A['select'] = "{$this->A['pre']}*";
		}elseif($this->A['mode'] == 'pushload'){//���ͼ���ģʽ
			if(empty($this->A['where'])) $this->A['where'] = cls_pusher::InitWhere($this->A['paid'],$this->A['pre']);
			if(empty($this->A['from'])) $this->A['from'] = cls_pusher::InitFrom($this->A['paid'],$this->A['pre']);
			if(empty($this->A['select'])) $this->A['select'] = "{$this->A['pre']}*";
		}
		
		//��������
		if(empty($this->A['ofm'])) $this->A['ofm'] = 'arcdeal';//����ѡ����ı��������������磺ѡ����������Ŀ��arcdeal['caid']=1
		if(empty($this->A['opre'])) $this->A['opre'] = 'arc';//����ֵ�Ĳ���ǰ׺���磺������ĿidΪ23ʱ��arccaid=23
		
		$cfg = array();
		foreach($this->A as $k => $v){
			if(!in_array($k,array('url','cbsMore','MoreSet','where','from','select',))) $cfg[$k] = $v;
		}
		
		//�������Ķ���
		$this->oS = new cls_memsearchs($cfg);
		//�����ݴ���Ķ���
		$this->oC = new cls_memcols($cfg);
		//��������Ķ���
		$this->oO = new cls_memops($cfg);
    }
	
	
	/**
	* ����Ŀ
	*
	* @param    string     $key  ��Ŀ�ؼ���
	* @param    string     $var  ��Ŀ������
	* 
	*/
	function setvar($key,$var){
		$this->$key = $var;	
	}
	
	
	/**
	* �����Ի���
	*
	* @param    string     $str  ��ʾ�ַ��� Ĭ��Ϊ��
	* @param    string     $url  ��תurl ��Ĭ��Ϊ��
	* 
	*/
	function message($str = '',$url = ''){
		call_user_func('cls_message::show',$str,$url);
	}

	function top_head(){
		$curuser = cls_UserMain::CurUser();
		if($this->mc){
			!defined('M_COM') && exit('No Permission');
		}else{
			if(!defined('M_COM') || !defined('M_ADMIN')) exit('No Permission');
			aheader();
			if($re = $curuser->NoBackFunc($this->A['backallow'])) $this->message($re);
		}
		include_once _08_INCLUDE_PATH.'mem_static.fun.php';
		echo "<title>��Ա����".($this->A['mchid'] ? '-'.$this->mchannel['cname'] : '')."</title>";
	}
	
	/**
	* ��ʾ��
	*
	* @param    string     $str  Ϊ������ʶ��ֱ�ӵ��ı����� Ĭ��Ϊ�գ�
	*							
	* @param    string     $type ��ʾ���� ��Ĭ��Ϊ0 
	*							 =0     ����Ա���� ֱ����ʾ$str������  �������̨ ��ʾ$strΪ���������ǵ�����
	*							 >0     �������̨ ֱ����ʾ$str������
	*							 =tip   �����ص���ʾ��ֻ�л�Ա������
	*							 =fix	�̶�����ʾ��ֻ�л�Ա������
	* 
	*/
	function guide_bm($str = '',$type = 0){
		if($this->mc){
			m_guide($str,$type ? $type : '');
		}else{
			if(!$str){
				$str = 'membersedit';
				$type = 0;
			}
			a_guide($str,$type);
		}
	}
	
	
	/**
	* ɸѡ�����Ŀ
	*
	* @ex  $oL->s_additem('keyword',array('fields' => array(),));
	*
	* @param    string    $key  ��Ŀ�ؼ��� Ĭ��Ϊ��
	* @param    array     $cfg  ��Ŀ���ò��� ����ѡ��Ĭ��Ϊ��
	*                                     
	*/
	
	function s_additem($key = '',$cfg = array()){//��׷��$key��$cfg֮��Ĵ���
		$this->oS->additem($key,$cfg);
	}
	
	
	/**
	* ɸѡ��ͷ��
	*
	*/
	function s_header(){
		echo form_str('mems_'.md5($this->A['url']),$this->A['url']);
		tabheader_e();
		echo "<tr><td class=\"".($this->mc ? 'item2' : 'txt txtleft')."\">\n";
		trhidden('page',$this->A['page']);
	}
	
	
	/**
	* ɸѡ�ַ���sql������װ�Ͳ�ѯ����
	*
	*/
	function s_deal_str(){//��oS�д�����ת����ǰ������
		$this->s_sqlstr();
		$this->s_filterstr();
	}
	
	/**
	* sql����
	*
	*/
	function s_sqlstr(){
		global $db,$tblprefix;
		$wherestr = empty($this->A['where']) ? '' : " AND {$this->A['where']}";
		if(empty($this->oS->no_list)){
			foreach($this->oS->wheres as $k => $v) $wherestr .= " AND $v";//�������Ӳ�����where����
			if(!$this->acount = $db->result_one('SELECT COUNT(*) FROM '.$this->A['from'].($wherestr ? " WHERE ".substr($wherestr,5) : ''))){
				$this->acount = 0;
			}
			if(in_array($this->A['mode'],array('pushload',))){//�ų��Ѽ��ص��ĵ�
				if($this->acount && $loadeds = $this->s_loaded_ids()){
					//��������
					$this->acount -= count($loadeds);
					$this->acount = max(0,intval($this->acount));
					//����wherestr
					$wherestr .= " AND {$this->A['pre']}mid ".multi_str($loadeds,1);
				}
			}
			if($wherestr) $wherestr = " WHERE ".substr($wherestr,5);
		}else{
			$wherestr = ' WHERE 0';
			$this->acount = 0;
		}
		$this->sqlall = "SELECT ".$this->A['select'].' FROM '.$this->A['from'].$wherestr.' ORDER BY '. $this->oS->orderby;
	}
	
	
	/**
	* ���ںϼ����߼��������˵��ϼ������б����Ѿ����ع���id
	*
	*/
	function s_loaded_ids(){//��ȡ�Ѽ��ع���id
		global $db,$tblprefix;
		if($this->A['mode'] == 'pushload'){//�ų��Ѽ��ص��ĵ�
			$sqlstr = "SELECT DISTINCT fromid AS ID FROM {$tblprefix}".cls_PushArea::ContentTable($this->A['paid']);
		}else return array();
		
		$re = array();
		$query = $db->query($sqlstr);
		while($r = $db->fetch_array($query)) $re[] = $r['ID'];
		return $re;
	}	


	/**
	* ɸѡ�ַ�����url��װ
	*
	*/
	function s_filterstr(){
		foreach($this->oS->filters as $k => $v){
			$this->filterstr .= "&$k=".(is_numeric($v) ? $v : rawurlencode($v));
		}
	}
	
	
	/**
	* �б�����Ŀ����ʾ
	*
	* @param    string    $key  ��Ŀ�ؼ��� Ĭ��Ϊ��
	* @return   html   ���html�ַ���                                   
	*/
	function s_view_one($key = ''){
		if(empty($key) || empty($this->oS->htmls[$key])) return;
		echo $this->oS->htmls[$key].' &nbsp;';
		unset($this->oS->htmls[$key]);
	}
	
	
	/**
	* �б�����Ŀ����ʾ
	*
	* @param    array    $incs  ��Ŀ�ؼ������� Ĭ��Ϊ��
	*                                   
	*/
	function s_view_array($incs = array()){
		if($incs){
			foreach($incs as $k) $this->s_view_one($k);
		}else{
			foreach($this->oS->htmls as $k => $v) $this->s_view_one($k);
		}
	}
	
	
	/**
	* ��ʾɸѡ�͸߼�ɸѡ��ť
	*                                  
	*/
	function s_adv_point(){
		echo strbutton('bfilter','ɸѡ');
		echo "\n &nbsp;<input class='checkbox' type='checkbox' name='cbsMore' id='cbsMore' value='1' onclick=\"display('boxMore')\"".($this->A['cbsMore'] ? "checked = 'checked'" : " ")."/>�߼�ѡ��</label>";	
		echo "\n<div id='boxMore'".(!$this->A['cbsMore'] ? " style='display:none'>" : " style='display:'>");
		$this->A['MoreSet'] = 1;
	}
	
	/**
	* ɸѡ��β��
	*                                  
	*/
	function s_footer(){
		if(empty($this->A['MoreSet'])){
			echo strbutton('bfilter','ɸѡ');
		}else echo "</div>";//�߼�����β
		tabfooter();
		unset($this->oS);
	}
	
	
	/**
	* �б�ͷ��
	*
	* @param    string    $title    �б���� Ĭ��Ϊ���õ�title
	* @param    int       $addmode  ����ģʽ Ĭ��Ϊ0
	*							  =0   �����$title�滻��Ĭ�ϵ�
	*							  =1   ��Ĭ�ϵı�������$title����
	*                                   
	*/
	function m_header($title = '',$addmode = 0){//$addmode=1ʱ��Ĭ�ϵı�������$title���ݣ�����$title�滻Ĭ��title
		if(!$title || $addmode){
			$tt = empty($this->A['mchid']) ? '��Ա�б�' : ($this->mchannel['cname'].' �б�');
		}
		$title = $addmode ? ($tt.$title) : ($title ? $title : $tt);
		tabheader($title,'','',20);
	}
	
	
	/**
	* �б������Ŀ
	*
	* @ex  s_additem('subject',array('title'=>'����','hidden'=>1,));
	*
	* @param    string    $key  ��Ŀ�ؼ��� Ĭ��Ϊ��
	* @param    array     $cfg  ��Ŀ���ò��� ����ѡ��Ĭ��Ϊ��
	*						��Ϊ title��Ŀ����, hidden��ĳ������ֵ�̶�����, url��ת���ӵȵ�
	*                                     
	*/
	
	function m_additem($key = '',$cfg = array()){//�����б��е���
		if(!$key) $this->message('������Ŀ��key����Ϊ��');
		if($this->oC->additem($key,$cfg) == 'undefined') $this->message("����Ŀ{$key}δ�ҵ�������");
	}
	
	
	
	function m_addgroup($mainstyle,$topstyle = ''){//���ӷ���
		if(!$mainstyle) return;
		$this->oC->addgroup($mainstyle,$topstyle);
	}
	
	
	/**
	* �б���Ŀ����ʾ������
	*
	*                                     
	*/
	function m_view_top(){
		$narr = $this->oC->fetch_top_row();
		$cfgs = $this->oC->cfgs;
		foreach($narr as $k => $v){//���������е���ʾ��ʽ
			$narr[$k] = $this->m_top_mode($v,$cfgs[$k]);
		}
		trcategory($narr);
	}
	
	function m_top_mode($str = '',$cfg = array()){
		$re = '|';
		if(!empty($cfg['side']) && in_array($cfg['side'],array('L','R'))){
			$re .= $cfg['side'];
		}
		$re .= '|';
		if(!empty($cfg['view']) && in_array($cfg['view'],array('S','H'))){
			$re .= $cfg['view'];
		}
		if($re == '||') $re = '';
		return $str.$re;
	}
	
	
	/**
	* �б��ҳsql����
	*                                    
	*/
	function m_db_array(){
		if(empty($this->rs)){
			global $db;
			$pagetmp = $this->A['page'];
			do{
				$query = $db->query("{$this->sqlall} LIMIT ".(($pagetmp - 1) * $this->A['rowpp']).",{$this->A['rowpp']}");
				$pagetmp--;
			} while(!$db->num_rows($query) && $pagetmp);
			$re = array();
			while($r = $db->fetch_array($query)) $re[] = $r;
			$this->rs = $re;
		}
		return $this->rs;
	}
	
	
	/**
	* �����б������ݼ���ʾhtml
	*
	*                                     
	*/		
	function m_view_main(){
		$rs = $this->m_db_array();
		foreach($rs as $k => $v){
			echo $this->m_one_row($v);
		}
	}
	
	/**
	* ��������Ա������
	*                                    
	*/		
	function m_one_row($data = array()){
		$narr = $this->oC->fetch_one_row($data);
		$cfgs = $this->oC->cfgs;
		
		//����һ�е�hmtl
		$re = "<tr".($this->mc ? '' : " class=\"txt\"").">\n";
		foreach($narr as $key => $val){
			$re .= $this->m_view_td($val,$cfgs[$key]);
		}	
		$re .= "</tr>\n";
		return $re;
	}
	function m_mcols_style($style = ''){
		$this->A['mcols_style'] = $style;
	}

	/**
	* ��Ŀ���ݵ���ʽӦ��
	*                                    
	*/
	function m_view_td($content = '',$cfg = array()){
		$width = empty($cfg['width']) ? '' : " w{$cfg['width']}";
		$class = $this->mc ? 'item' : 'txt';
		if(!empty($cfg['side']) && in_array($cfg['side'],array('L','R',))){
			$class = $this->mc ? ($cfg['side'] == 'L' ? 'item2' : 'item1') : ($cfg['side'] == 'L' ? 'txtL' : 'txtR');
		}
		$class .= empty($cfg['width']) ? '' : " w{$cfg['width']}";
		return "<td class=\"$class\">$content</td>\n";
	} 
	
	
	/**
	* �б����ײ�
	*                                    
	*/
	function m_footer(){
		global $db;
		tabfooter();
		echo multi($this->acount,$this->A['rowpp'],$this->A['page'],$this->A['url'].$this->filterstr);
		foreach(array('oC','sqlall','acount','filterstr',) as $k) unset($this->$k);
	}
	
	/**
	* ���������������Ŀ
	*
	* @ex  $oL->o_additem('delete',array('skip'=>1));
	*
	* @param    string    $key  ��Ŀ�ؼ��� Ĭ��Ϊ��
	* @param    array     $cfg  ��Ŀ���ò��� ����ѡ��Ĭ��Ϊ��
	*                        ��Ϊ skip=>1  ���Դ���Ŀ ���ڻ�Ա����,��̬�����ǰ��Ա�Ƿ��д˲���Ȩ��         
	*/
	function o_additem($key,$cfg = array()){
		if(!empty($cfg['skip'])) return;
		$re = $this->oO->additem($key,$cfg);
		if($re === 'undefined') $this->message("����������{$key}δ�ҵ�������");
	}
	
	
	/**
	* ���������������λ��Ŀ
	*
	* @param    array     $noincs  ����λid����
	*                          
	*/
	function o_addpushs($noincs = array()){
		if(!$this->A['mchid']) return;
		$na = cls_pusher::paidsarr('members',$this->A['mchid']);
		foreach($na as $k => $v) in_array($k,$noincs) || $this->o_additem($k);
	}
	
	function o_header($title = ''){
		tabheader($title ? $title : '��������');
	}
	
	
	/**
	*���ص������������ʾ����
	*
	* @param    string   $key  ����λ��Ŀ�ؼ���
	* @return   html    ����html�ַ���                      
	*/
	function o_view_one_push($key){
		$re = $this->oO->view_one_push($key);
		if($re == 'undefined') $this->message("����������{$key}δ�ҵ�������");
		return $re;
	}
	
	
	/**
	*�����������չʾ
	*
	* @param    string   $key  ����λ��Ŀ�ؼ���
	* @return   html    ����html�ַ���                      
	*/
	function o_view_pushs($title = '',$incs = array(),$numpr = 5){
		//$numprÿ����ʾ����
		$html = '';$i = 0;
		$incs || $incs = array_keys($this->oO->cfgs);
		foreach($incs as $k){
			if($re = $this->o_view_one_push($k)){
				if($numpr && $i && !($i % $numpr)) $html .= '<br>';
				$i ++;
				$html .= $re;
			}
		}
		if($html){
			$title || $title = 'ѡ������λ';
			trbasic($title,'',$html,'');
		}
	}
	
	/**
	*���ص�����ѡ���������ʾ����
	*                  
	*/
	function o_view_one_bool($key){
		$re = $this->oO->view_one_bool($key);
		if($re == 'undefined') $this->message("����������{$key}δ�ҵ�������");
		return $re;
	}
	
	
	/**
	*��ѡ�����������չʾ
	*
	* @param    string   $title  ��ѡ�����
	* @return   html    ����html�ַ���                      
	*/
	function o_view_bools($title = '',$incs = array(),$numpr = 5){
		//$numprÿ����ʾ����
		$html = '';$i = 0;
		$incs || $incs = array_keys($this->oO->cfgs);
		foreach($incs as $k){
			if($re = $this->o_view_one_bool($k)){
				if($numpr && $i && !($i % $numpr)) $html .= '<br>';
				$i ++;
				$html .= $re;
			}
		}
		if($html){
			$title || $title = 'ѡ�������Ŀ';
			trbasic($title,'',$html,'');
		}
	}
	
	//�������в��������ʾ��ע�⣺���Ƿ������ݣ�����ֱ����ʾ
	function o_view_one_row($key){
		$re = $this->oO->view_one_row($key);
		if($re == 'undefined') $this->message("����������{$key}δ�ҵ�������");
	}
	
	//���������������������ʾ
	function o_view_rows($incs = array()){
		$incs || $incs = array_keys($this->oO->cfgs);
		foreach($incs as $k) $this->o_view_one_row($k);
	}
	
	function o_footer($button = '',$bvalue = ''){
		tabfooter($button,$button ? ($bvalue ? $bvalue : '�ύ') : '');
	}
	
	function o_end_form($button = '',$bvalue = ''){
		//echo $this->mc ? '<br>' : '<br><br>';
		echo "\n<div align=\"center\" style='display:block;clear:both;'>".strbutton($button,$button ? ($bvalue ? $bvalue : '�ύ') : '')."</div>\n</form>\n";
	}
	
	//���ύ���Ԥ���
	function sv_header(){
		if(empty($GLOBALS[$this->A['ofm']])) $this->message('��ѡ�������Ŀ',$this->A['url']."&page={$this->A['page']}".$this->filterstr);
		if(empty($GLOBALS['selectid'])) $this->message('��ѡ���Ա',$this->A['url']."&page={$this->A['page']}".$this->filterstr);
	}
	
	// msg �����Զ�������У���õĶ�����Ϣ����[�޶�����]��Ϣ
	function sv_footer($msg=''){
		$this->mc || adminlog('��Ա��������','��Ա�б�������');
		$url = $this->A['url']."&page={$this->A['page']}".$this->filterstr;
		#if($this->A['isab'] == 2) $url = axaction(6,$url);
		$this->message('�����������'.(empty($msg) ? '' : "<br>$msg"),$url);
	}
	
	//�������ͼ�������
	function sv_o_pushload(){
		$selectid = @$GLOBALS['selectid'];
		if(empty($selectid)) $this->message('��ѡ���Ա',$this->A['url']."&page={$this->A['page']}".$this->filterstr);
		if($this->A['mode'] != 'pushload') $this->message('��������λ���ز���',$this->A['url']."&page={$this->A['page']}".$this->filterstr);
		
		$auser = &$this->oO->auser;
		if(empty($auser)) $auser = new cls_userinfo;
		
		$rs = $this->m_db_array();//�ٴ����Ʒ�Χ���Է�����Ȩ�޽��в���
		foreach($rs as $r){
			if(!in_array($r['mid'],$selectid)) continue;
			$auser->activeuser($r['mid']);
			$auser->push($this->A['paid']);
		}
		$this->mc || adminlog('����λ���ز���','�ĵ��б�������');
		$this->message('�����������',axaction(5,$this->A['url']."&page={$this->A['page']}".$this->filterstr));
	}

	function sv_o_one($key){
		$re = $this->oO->save_one($key);
		if($re === 'undefined') $this->message("����������{$key}δ�ҵ�������");
		return $re;
	}
	
	
	/**
	*�б���������ݴ���
	*                  
	*/
	function sv_o_all($cfg=array()){//ɾ��ʱҪͬ������uc��Ա
		global $enable_uc;
		$ofm = @$GLOBALS[$this->A['ofm']];
		$selectid = @$GLOBALS['selectid'];
		$rs = $this->m_db_array();//�ٴ����Ʒ�Χ���Է�����Ȩ�޽��в���
		if($ofm && $selectid && $rs){
			$auser = &$this->oO->auser;
			if(empty($auser)) $auser = new cls_userinfo;
			$ucdels = array();
			foreach($rs as $r){
				if(!in_array($r['mid'],$selectid)) continue;
				$auser->activeuser($r['mid'],1);
				if(!empty($ofm['delete'])){//ɾ���򲻼�����������
                    # ͬ��ɾ��WINDID��Ա��ע���ô�������ɾ�������������ɾ����ϵͳ
                    cls_WindID_Send::getInstance()->deleteUser($r['mid']);
                    cls_ucenter::delete(array($auser->info['mname']));//ͬʱɾ��uc�Ļ�Ա
					$this->sv_o_one('delete');
					continue;
				}
				foreach($ofm as $key => $v){
					$this->sv_o_one($key);
				}
				$auser->updatedb();
			}
		}
	}
}
