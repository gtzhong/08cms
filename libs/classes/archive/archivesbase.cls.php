<?php
/**
 *  archivesbase.cls.php �ĵ��б����Ĳ�������	 
 *
 *
 * @copyright			(C) 2008-2013 08cms
 * @license				http://www.08cms.com/license/
 * @lastmodify			2013-2-23
 */

!defined('M_COM') && exit('No Permisson');
class cls_archivesbase{
	
	protected $mc = 0;//��Ա����
	public $A = array();//��ʼ��������š���chid��pre(����ǰ׺),tbl(����),stid(����id)
	public $channel = array();//��ǰģ��
	public $album = array();//ָ���ϼ�pid������
	
	
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
	
	//���������
	public $oE	= NULL;//�б����������Ķ���
	
	
	/**
	 * ���캯����ʼ������
	 */
    function __construct($cfg = array()){
		global $db,$tblprefix;
		
		$this->mc = defined('M_ADMIN') ? 0 : 1;
		$this->A = $cfg;
		
		//ģ����ֱ�
		$splitbls = cls_cache::Read('splitbls');
		if(empty($this->A['chid']) || !($this->channel = cls_channel::Config($this->A['chid']))) $this->message('��ָ���ĵ�����');
		if(empty($this->A['url'])) $this->message('����д���ύURL');
		$this->A['stid'] = $this->channel['stid'];
		$this->A['tbl'] = 'archives'.$this->channel['stid'];
		if(empty($this->A['coids'])) $this->A['coids'] = empty($splitbls[$this->A['stid']]) ? array() : $splitbls[$this->A['stid']]['coids'];
		$this->A['multi_chid'] = empty($this->A['multi_chid']) ? 0 : 1;//����ͬ����Ķ�ģ�͹�����ʱchid����������
		if(empty($this->A['backallow']) && !$this->mc) $this->A['backallow'] = 'normal';//��̨����Ȩ��
		if(empty($this->A['pid'])) $this->A['pid'] = 0;//�ϼ�id
		if(empty($this->A['isab'])) $this->A['isab'] = 0;//����ģʽ���ã�0Ϊ��ͨ�����б�1Ϊ���ڹ����б�2Ϊ���������б�3Ϊ����λ���ع���
		if(!in_str('chid=',$this->A['url']) && $this->A['isab'] < 3) $this->A['url'] .= "&chid={$this->A['chid']}"; 
		
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
		$this->A['cols'] = empty($this->A['cols']) ? 0 : max(0,intval($this->A['cols']));
		$this->A['cols'] = $this->A['cols'] < 2 ? 0 : min(10, $this->A['cols']);
		if($this->A['cols']) $this->A['rowpp'] = ceil($this->A['rowpp'] / $this->A['cols']) * $this->A['cols'];
		if(empty($this->A['mfm'])) $this->A['mfm'] = 'fmdata';	//�б�����������ı�����������
		
		//��ͬ����ģʽ�µ�Ԥ���洦��
		if(in_array($this->A['isab'],array(1,2))){//���ڹ�������
			if(empty($this->A['pid'])) $this->message('��ָ���ϼ�id');
			if(empty($this->A['arid'])) $this->message('��ָ���ϼ���Ŀid');
			if($abrel = cls_cache::Read('abrel',$this->A['arid'])){
				$this->A['abtbl'] = $abrel['tbl'];//�ϼ���ϵ�ļ�¼��
			}else $this->message('��ָ����ȷ�ĺϼ���Ŀid');
			$this->pid_allow($this->A['pid'],@$this->A['pids_allow']);
			if(!in_str('pid=',$this->A['url'])) $this->A['url'] .= "&pid={$this->A['pid']}";
			if($this->A['abtbl'] && empty($this->A['bpre'])) $this->A['bpre'] = 'b.';//�����ϼ���ϵ���ǰ׺,
			if(($ntbl = atbl($this->A['pid'],2)) && $this->album = $db->fetch_one("SELECT * FROM {$tblprefix}$ntbl WHERE aid='{$this->A['pid']}'")){//��ȡ�ϼ����ϣ�ֻ����aid,subject,mid��url����
				cls_ArcMain::Parse($this->album);
				foreach($this->album as $k => $v) if(!in_array($k,array('aid','caid','subject','mid','mname')) && !in_str('arcurl',$k)) unset($this->album[$k]);
			}else $this->message('��ָ����ȷ�ĺϼ�');
		}elseif($this->A['isab'] == 3){
			if(empty($this->A['paid'])) $this->message('��ָ������λid');
			if(!in_str('paid=',$this->A['url'])) $this->A['url'] .= "&paid={$this->A['paid']}";
		}
		
		//sql��ѯ��ʼ����where��from��selectҪô��ȫ��д��Ҫô����
		if(empty($this->A['pre'])) $this->A['pre'] = 'a.';
		if(empty($this->A['isab'])){//��ͨ�ĵ�����
			if(empty($this->A['where'])) $this->A['where'] = '';
			if(empty($this->A['from'])) $this->A['from'] = "{$tblprefix}{$this->A['tbl']} ".substr($this->A['pre'],0,-1);
			if(empty($this->A['select'])) $this->A['select'] = "{$this->A['pre']}*";
		}elseif($this->A['isab'] == 1){//���ڹ����б�
			if($this->A['abtbl']){//�����ϼ���ϵ��
				if(empty($this->A['select'])) $this->A['select'] = "{$this->A['pre']}*,{$this->A['bpre']}*";
				if(empty($this->A['from'])) $this->A['from'] = "{$tblprefix}{$this->A['abtbl']} ".substr($this->A['bpre'],0,-1)." INNER JOIN {$tblprefix}{$this->A['tbl']} ".substr($this->A['pre'],0,-1)." ON {$this->A['pre']}aid={$this->A['bpre']}inid";
				if(empty($this->A['where'])) $this->A['where'] = "{$this->A['bpre']}pid='{$this->A['pid']}'";
			}else{//���úϼ���ϵ��
				if(empty($this->A['where'])) $this->A['where'] = "{$this->A['pre']}pid{$this->A['arid']}='{$this->A['pid']}'";
				if(empty($this->A['from'])) $this->A['from'] = "{$tblprefix}{$this->A['tbl']} ".substr($this->A['pre'],0,-1);
				if(empty($this->A['select'])) $this->A['select'] = "{$this->A['pre']}*";
			}
		}elseif($this->A['isab'] == 2){//�ϼ������б�
			//ע���Ȳ�ѯ��NOT IN()��IDs, ��ֱ����SELECT�Ӿ䣬ƽ��Ҫ����10������	
			if($this->A['abtbl']){//�����ϼ���ϵ��
				if(empty($this->A['where'])){ 
					$subids = cls_DbOther::SubSql_InIds('inid', "{$this->A['abtbl']}", "pid='{$this->A['pid']}'");
					$this->A['where'] = "{$this->A['pre']}aid NOT IN($subids)";
				}
			}else{
				if(empty($this->A['where'])){
					$subids = cls_DbOther::SubSql_InIds('aid', "{$this->A['tbl']}", "pid{$this->A['arid']}='{$this->A['pid']}'");
					$this->A['where'] = "{$this->A['pre']}aid NOT IN($subids)";	
				}
			}
			if(empty($this->A['from'])) $this->A['from'] = "{$tblprefix}{$this->A['tbl']} ".substr($this->A['pre'],0,-1);
			if(empty($this->A['select'])) $this->A['select'] = "{$this->A['pre']}*";
		}elseif($this->A['isab'] == 3){//���ͼ����б�
			if(empty($this->A['where'])){
				$this->A['where'] = cls_pusher::InitWhere($this->A['paid'],$this->A['pre']);
				//���������ã����ӹ���SQL���溬��{$tblprefix}���д���
				$this->A['where'] = str_replace('{$tblprefix}',$tblprefix,$this->A['where']);
			}
			if(empty($this->A['from'])) $this->A['from'] = cls_pusher::InitFrom($this->A['paid'],$this->A['pre']);
			if(empty($this->A['select'])) $this->A['select'] = "{$this->A['pre']}*";
		}
		
		//��������
		if(empty($this->A['ofm'])) $this->A['ofm'] = 'arcdeal';//����ѡ����ı��������������磺ѡ����������Ŀ��arcdeal['caid']=1
		if(empty($this->A['opre'])) $this->A['opre'] = 'arc';//����ֵ�Ĳ���ǰ׺���磺������ĿidΪ23ʱ��arccaid=23
		
		$cfg = array();
		foreach($this->A as $k => $v){
			if(!in_array($k,array('url','cols','cbsMore','MoreSet','where','from','select',))) $cfg[$k] = $v;
		}
		
		//�������Ķ���
		$this->oS = new cls_arcsearchs($cfg);
		//�����ݴ���Ķ���
		$this->oC = new cls_arccols($cfg);
		//�б����������Ķ���
		$this->oE = new cls_arcsets($cfg);
		//��������
		$this->oO = new cls_arcops($cfg);
    }
	
	/**
	 * ���ҵ�ǰ��Ŀ�Ķ�����Ŀ
	 */
	function find_topcaid(){
		$catalogs = cls_cache::Read('catalogs');
		$caid = $GLOBALS['caid'];
		$pid = 0;
		while($arr = array_intersect_key($catalogs,array($caid=>'0',))){
			$pid = @$arr[$caid]['pid'];
			if(!$pid) break;
			$caid = $pid;
		}
		return $caid;
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
	
	
	/**
	* ���ڻ�Ա���ĺϼ�Ȩ���ж�
	*
	* @param    int        $pid         ���ĵ��ĺϼ�ID Ĭ��Ϊ��
	* @param    string     $allow_pids  �������ĺϼ�id ��Ĭ��Ϊ��
	* 
	*/
	function pid_allow($pid = 0,$allow_pids = ''){
		if(!$this->mc || !$pid) return;
		if(empty($allow_pids)) $this->message('�������������ĺϼ�id��Χ');
		if($allow_pids=='-1'){
			;//�����ڻ�Ա���ģ��ɼ��ر��˵���Ϣ���ϼ�����
		}elseif($allow_pids == 'self'){//ָ��Ϊ���ѷ����ĺϼ�
			global $db,$tblprefix;
			$curuser = cls_UserMain::CurUser();
			if($curuser->info['mid'] != $db->result_one("SELECT mid FROM {$tblprefix}".atbl($pid,2)."  WHERE aid='$pid'")) $this->message('��ֻ�ܹ������ѷ���������');
		}elseif($allow_pids = explode(',',$allow_pids)){
			if(!in_array($pid,$allow_pids)) $this->message('��û�е�ǰ�ϼ��Ĺ���Ȩ��');
		}else $this->message('�������������ĺϼ�id��Χ');
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
		echo "<title>���ݹ��� - {$this->channel['cname']}</title>";
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
				$str = 'archivesedit';
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
		echo form_str('arcs_'.md5($this->A['url']),$this->A['url']);
		tabheader_e();
		echo "<tr><td class=\"".($this->mc ? 'item2' : 'txt txtleft')."\">\n<div class='search_area'>\n";
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
			// �ų��Ѽ��ص��ĵ�
			if(in_array($this->A['isab'],array(3))){ // isab=2,��ʼ�����Ѿ�����array(2,3)->array(3)
				if($this->acount && $loadeds = $this->s_loaded_ids()){
					//��������
					$this->acount -= count($loadeds);
					$this->acount = max(0,intval($this->acount));
					//����wherestr
					$wherestr .= " AND {$this->A['pre']}aid ".multi_str($loadeds,1);
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
		if($this->A['isab'] == 2){//�ų��Ѽ��ص��ĵ�
			if($this->A['abtbl']){//�����ϼ���ϵ��
				return cls_DbOther::SubSql_InIds('inid', $this->A['abtbl'], "pid='{$this->A['pid']}'", '');
			}else{
				return cls_DbOther::SubSql_InIds('aid', $this->A['tbl'], "pid{$this->A['arid']}='{$this->A['pid']}'", '');
			}
		}elseif($this->A['isab'] == 3){//�ų��Ѽ��ص��ĵ�
			return cls_DbOther::SubSql_InIds('fromid', cls_PushArea::ContentTable($this->A['paid']), "", '');
		}else return array();
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
		echo $this->oS->htmls[$key].' ';
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
	
	
	function s_adv_point(){
		echo strbutton('bfilter','ɸѡ');
		echo "\n <label><input class='checkbox' type='checkbox' name='cbsMore' id='cbsMore' value='1' onclick=\"display('boxMore')\"".($this->A['cbsMore'] ? "checked = 'checked'" : " ")."/>�߼�ѡ��</label>";	
		echo "\n<div id='boxMore'".(!$this->A['cbsMore'] ? " style='display:none'>" : " style='display:'>");
		$this->A['MoreSet'] = 1;
	}
	
	
	function s_footer(){
		if(empty($this->A['MoreSet'])){
			echo strbutton('bfilter','ɸѡ');
		}else echo "</div></div>";//�߼�����β
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
			$tts = (!empty($GLOBALS['caid']) && $catalog = cls_cache::Read('catalog',$GLOBALS['caid'])) ? $catalog['title'] : $this->channel['cname'];
			if(empty($this->A['isab'])){
				$tt = "$tts ���ݹ���";
			}elseif($this->A['isab'] == 1){
				$tt = "[{$this->album['subject']}] �ڵ� $tts";
			}elseif($this->A['isab'] == 2){
				$tt = "[{$this->album['subject']}] ���� $tts";
			}elseif($this->A['isab'] == 3){
				$tt = "[����λ] ���� $tts";
			}
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
		if($this->A['cols']) return;//���ж��е�ģʽ����Ҫʹ���������
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
	* $cfg[trclass]���е�css  
	$ $cfg[divclass]��div[��Ԫ��]��css  
	*                                     
	*/
	function m_view_main($cfg=array()){
		$rs = $this->m_db_array();
		if($this->A['cols']){//�������ͨ��һ�ֶ����ʽ��װ��������Ϊһ����Ŀ���
			$html = '';$i = 0;
            $width = floor(100/($this->A['cols'])).'%';
			$cnt = count($rs);
			$_cnt = ceil($cnt/$this->A['cols']) * $this->A['cols'];
			$addnum = $_cnt - $cnt;
			while($addnum){$rs[] = null;$addnum--;}
			foreach($rs as $k => $v){
				$trclass = empty($cfg['trclass']) ? ($this->mc ? '' : " class=\"txt\"") : " class=\"$cfg[trclass]\"";
				$divclass = empty($cfg['divclass']) ? '' : " class=\"$cfg[divclass]\"";
				if(!($i % $this->A['cols'])) $html .= "<tr $trclass>\n";
				$html .= "<td width=\"$width\" class=\"".($this->mc ? 'item2' : 'txtL')."\"><div $divclass style=\"width:100%;margin:0 auto;\">".(empty($v)?'':$this->m_one_row($v))."</div></td>\n";
				$i ++;
				if(!($i % $this->A['cols'])) $html .= "</tr>\n";
			}
			if($i-- % $this->A['cols']) $html .= "</tr>\n";
			echo $html;
		}else{
			foreach($rs as $k => $v){
				echo $this->m_one_row($v, $cfg);
			}
		}
	}
	
	/**
	* �������ĵ�������
	* $cfg[trclass]���е�css                          
	*/	
	function m_one_row($data=array(), $cfg=array()){//�������ĵ�������
		$narr = $this->oC->fetch_one_row($data);
		if($this->A['cols']){//�������ͨ��һ�ֶ����ʽ��װ��������Ϊһ����Ŀ���
			if(empty($this->A['mcols_style'])) $this->A['mcols_style'] = '{selectid} &nbsp;{subject}';
			return key_replace($this->A['mcols_style'],$narr);
		}else{
			$cfgs = $this->oC->cfgs;
			//����һ�е�hmtl
			$trclass = empty($cfg['trclass']) ? ($this->mc ? '' : " class=\"txt\"") : " class=\"$cfg[trclass]\"";
			$re = "<tr $trclass>\n";
			foreach($narr as $key => $val){
				$re .= $this->m_view_td($val,$cfgs[$key]);
			}	
			$re .= "</tr>\n";
			return $re;
		}
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
	function m_footer(){//�б����ײ�
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
		if($re == 'undefined') $this->message("����������{$key}δ�ҵ�������");
	}
	
	
	/**
	* ���������������λ��Ŀ
	*
	* @param    array     $noincs  ����λid����
	*                          
	*/
	function o_addpushs($noincs = array()){
		$caid = empty($GLOBALS['caid']) ? 0 : max(0,intval($GLOBALS['caid']));
		$na = cls_pusher::paidsarr('archives',$this->A['chid'],$caid);
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
	
	//����nolist���� ���������������ϵ��������ֹ�û������ڱ������ϵ����� ���÷�ʽ sv_header(array(10,11,12)
	function sv_header($nolist=array()){
		if(empty($GLOBALS[$this->A['mfm']])){
			if(empty($GLOBALS[$this->A['ofm']])) $this->message('��ѡ�������Ŀ',$this->A['url']."&page={$this->A['page']}".$this->filterstr);
			if(empty($GLOBALS['selectid'])) $this->message('��ѡ���ĵ�',$this->A['url']."&page={$this->A['page']}".$this->filterstr);
		}
		if($nolist) foreach(@$this->oO->A['coids'] as $k=>$v) if(in_array($v,$nolist)) unset($this->oO->A['coids'][$k]);
	}
	
	// msg �����Զ�������У���õĶ�����Ϣ����[�޶�����]��Ϣ
	function sv_footer($msg=''){
		$c_upload = cls_upload::OneInstance();
		$c_upload->saveuptotal(1);
		$this->mc || adminlog('�ĵ���������','�ĵ��б�������');
		$url = $this->A['url']."&page={$this->A['page']}".$this->filterstr;
		if($this->A['isab'] == 2) $url = axaction(6,$url);
		if(!empty($this->oO->recnt['readd'])){ 
			if(!$this->oO->recnt['readd']['do']){ //δ�����κ�����
				$msg = ($msg ? "$msg<br>" : '')."[ˢ��]����δ�ɹ���ˢ��:".$this->oO->recnt['readd']['do']."��; ";
			}elseif(!$this->oO->recnt['readd']['skip']){ //ȫ���ɹ�
				$msg = ($msg ? "$msg<br>" : '')."[ˢ��]�����ɹ���ˢ��:".$this->oO->recnt['readd']['do']."��; ";
			}else{ //���ֳɹ�
				$msg = ($msg ? "$msg<br>" : '')."[ˢ��]��������δ�ɹ����ɹ�:".$this->oO->recnt['readd']['do']."��; ����:".$this->oO->recnt['readd']['skip']."��;";
			}
		}
		if(!empty($this->oO->recnt['valid'])){ 
			if(!$this->oO->recnt['valid']['do']){ //δ�����κ�����
				$msg = ($msg ? "$msg<br>" : '')."[�ϼ�]����δ�ɹ����ϼ�:".$this->oO->recnt['valid']['do']."��; ";
			}elseif(!$this->oO->recnt['valid']['skip']){ //ȫ���ɹ�
				$msg = ($msg ? "$msg<br>" : '')."[�ϼ�]�����ɹ����ϼ�:".$this->oO->recnt['valid']['do']."��; ";
			}else{ //���ֳɹ�
				$msg = ($msg ? "$msg<br>" : '')."[�ϼ�]��������δ�ɹ����ɹ�:".$this->oO->recnt['valid']['do']."��; ����:".$this->oO->recnt['valid']['skip']."��;";
			}
		}
		if(!empty($this->oO->recnt['reccids'])){ //��ϵ�޶����
		foreach($this->oO->recnt['reccids'] as $ccid=>$v){
			if(!$v['do']){ //δ�����κ�����
				$msg = ($msg ? "$msg<br>" : '')."[$v[title]]����δ�ɹ����޶�����; ";
			}elseif(!$v['skip']){ //ȫ���ɹ�
				$msg = ($msg ? "$msg<br>" : '')."[$v[title]]�����ɹ�����:".$v['do']."�����óɹ�; ";
			}else{ //���ֳɹ�
				$msg = ($msg ? "$msg<br>" : '')."[$v[title]]��������δ�ɹ����ɹ�:".$v['do']."��; ����:".$v['skip']."��;";
			}
		}}
		$this->message('�����������'.(empty($msg) ? '' : "<br>$msg"),$url);
	}
	
	function sv_e_additem($key,$cfg = array()){
		$this->oE->additem($key,$cfg);
	}
	
	//�б�������������ݴ���
	function sv_e_all(){
		$mfm = @$GLOBALS[$this->A['mfm']];
		$rs = $this->m_db_array();
		foreach($rs as $r){
			if(!empty($mfm[$r['aid']])){
				foreach($mfm[$r['aid']] as $key => $val){
					$this->oE->set_one($key,$val,$r);
				}
			}
		}
	}
	
	//����ϼ���������
	function sv_o_load(){
		$selectid = @$GLOBALS['selectid'];
		if(empty($selectid)) $this->message('��ѡ���ĵ�',$this->A['url']."&page={$this->A['page']}".$this->filterstr);
		if($this->A['isab'] != 2) $this->message('���Ǻϼ����ز���',$this->A['url']."&page={$this->A['page']}".$this->filterstr);
		
		$arc = &$this->oO->arc;
		if(empty($arc)) $arc = new cls_arcedit;
		
		$rs = $this->m_db_array();//�ٴ����Ʒ�Χ���Է�����Ȩ�޽��в���
		foreach($rs as $r){
			if(!in_array($r['aid'],$selectid)) continue;
			$arc->set_aid($r['aid'],$this->A['multi_chid'] ? array() : array('chid' => $this->A['chid']));
			$arc->set_album($this->A['pid'],$this->A['arid']);
		}
		
		$this->mc || adminlog('�ϼ����ز���','�ĵ��б�������');
		$this->message('�����������',axaction(5,$this->A['url']."&page={$this->A['page']}".$this->filterstr));
	}
	//����ϼ���������
	function sv_o_pushload(){
		$selectid = @$GLOBALS['selectid'];
		if(empty($selectid)) $this->message('��ѡ���ĵ�',$this->A['url']."&page={$this->A['page']}".$this->filterstr);
		if($this->A['isab'] != 3) $this->message('��������λ���ز���',$this->A['url']."&page={$this->A['page']}".$this->filterstr);
		
		$arc = &$this->oO->arc;
		if(empty($arc)) $arc = new cls_arcedit;
		
		$rs = $this->m_db_array();//�ٴ����Ʒ�Χ���Է�����Ȩ�޽��в���
		foreach($rs as $r){
			if(!in_array($r['aid'],$selectid)) continue;
			$arc->set_aid($r['aid'],$this->A['multi_chid'] ? array() : array('chid' => $this->A['chid']));
			$arc->push($this->A['paid']);
		}
		
		$this->mc || adminlog('����λ���ز���','�ĵ��б�������');
		$this->message('�����������',axaction(5,$this->A['url']."&page={$this->A['page']}".$this->filterstr));
	}

	function sv_o_one($key){
		$re = $this->oO->save_one($key);
		if($re == 'undefined') $this->message("����������{$key}δ�ҵ�������");
		return $re;
	}
	
	
	/**
	*�б���������ݴ���
	*                  
	*/
	function sv_o_all($cfg=array()){
		$ofm = @$GLOBALS[$this->A['ofm']];
		$selectid = @$GLOBALS['selectid'];
		$rs = $this->m_db_array();//�ٴ����Ʒ�Χ���Է�����Ȩ�޽��в���
		if($ofm && $selectid && $rs){
			$arc = &$this->oO->arc;
			if(empty($arc)) $arc = new cls_arcedit;
			foreach($rs as $r){
				if(!in_array($r['aid'],$selectid)) continue;
				$arc->set_aid($r['aid'],$this->A['multi_chid'] ? array() : array('chid' => $this->A['chid']));
				if(!empty($ofm['delete'])){//ɾ���򲻼�����������
					$this->sv_o_one('delete');
					continue;
				}elseif(!empty($ofm['delbad'])){//ɾ��(�ۻ���)�򲻼�����������
					$this->sv_o_one('delbad');
					continue;
				}
				foreach($ofm as $key => $v){
					$this->sv_o_one($key);
				}
				$arc->updatedb();
			}
		}
	}
/*** ���²��ֽű���ֱ�Ӹ��Ƶ�����ű���ȡ��sv_all()�������붨�ƣ�

		$ofm = ${$oL->A['ofm']};
		$rs = $oL->m_db_array();//�ٴ����Ʒ�Χ���Է�����Ȩ�޽��в���
		if($ofm && $selectid && $rs){
			$arc = &$oL->oO->arc;
			if(empty($arc)) $arc = new cls_arcedit;
			
			foreach($rs as $r){
				if(!in_array($r['aid'],$selectid)) continue;
				$arc->set_aid($r['aid'],$oL->A['multi_chid'] ? array() : array('chid' => $oL->A['chid']));
				if(!empty($ofm['delete'])){//ɾ���򲻼�����������
					$oL->sv_one('delete');
					continue;
				}
				foreach($ofm as $key =>$v){
					$oL->sv_one($key);
				}
				$arc->updatedb();
			}
		}
*/	
	
	
}
