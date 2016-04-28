<?php
/*
** �����б�����Ĺ��������
** 
*/
!defined('M_COM') && exit('No Permisson');
class cls_pushsbase{
	
	protected $mc = 0;//��Ա����
	public $A = array();//��ʼ���������
	public $area = array();//��ǰ����λ����
	
	//�����й�
	public $oS	= NULL;//�������Ķ���
	public $sqlall = '';//����sql�ִ�
	public $sqlnum = '';//����ͳ��sql
	public $filterstr = '';//ɸѡ������url�еĴ����ִ�
	
	//�����б�
	public $oC	= NULL;//�����ݴ���Ķ���
	
	//��������
	public $oO	= NULL;//������Ŀ����Ķ���
	public $rs	= array();//�����ݴ�����ݴ���ѡ�б���������
	
	//���������
	public $oE	= NULL;//�б����������Ķ���
	
    function __construct($cfg = array()){
		global $db,$tblprefix;
		
		$this->mc = defined('M_ADMIN') ? 0 : 1;
		$this->A = $cfg;
		
		//��ʼ������
		if(empty($this->A['paid']) || !($this->area = cls_PushArea::Config($this->A['paid']))) $this->message('��ָ������λ');
		if(empty($this->A['url'])) $this->message('����д���ύURL');
		$this->A['tbl'] = cls_PushArea::ContentTable($this->A['paid']);
		if(empty($this->A['backallow']) && !$this->mc) $this->A['backallow'] = 'normal';
		if(!in_str('paid=',$this->A['url'])) $this->A['url'] .= "&paid={$this->A['paid']}"; 
		
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
		
		
		//sql��ѯ��ʼ����where��from��selectҪô��ȫ��д��Ҫô����
		if(empty($this->A['pre'])) $this->A['pre'] = 'p.';
		if(empty($this->A['where'])) $this->A['where'] = '';
		if(empty($this->A['from'])) $this->A['from'] = "{$tblprefix}{$this->A['tbl']} ".substr($this->A['pre'],0,-1);
		if(empty($this->A['select'])) $this->A['select'] = "{$this->A['pre']}*";
		
		//��������
		if(empty($this->A['ofm'])) $this->A['ofm'] = 'ofm';//����ѡ����ı��������������磺ѡ����������Ŀ��ofm['caid']=1
		if(empty($this->A['opre'])) $this->A['opre'] = 'opre_';//����ֵ�Ĳ���ǰ׺���磺������ĿidΪ23ʱ��opre_caid=23
		
		$cfg = array();
		foreach($this->A as $k => $v){
			if(!in_array($k,array('url','cols','cbsMore','MoreSet',))) $cfg[$k] = $v;
		}
		
		//�������Ķ���
		$this->oS = new cls_pushsearchs($cfg);
		//�����ݴ���Ķ���
		$this->oC = new cls_pushcols($cfg);
		//�б����������Ķ���
		$this->oE = new cls_pushsets($cfg);
		//��������
		$this->oO = new cls_pushops($cfg);
    }
	
	function setvar($key,$var){
		$this->$key = $var;	
	}
	
	function message($str = '',$url = ''){
		call_user_func('cls_message::show',$str,$url);
	}

	function top_head(){
		if($this->mc){
			!defined('M_COM') && exit('No Permission');
		}else{
			if(!defined('M_COM') || !defined('M_ADMIN')) exit('No Permission');
			aheader();
			$curuser = cls_UserMain::CurUser();
			if($re = $curuser->NoBackFunc($this->A['backallow'])) $this->message($re);
		}
		echo "<title>���ݹ��� - {$this->area['cname']}</title>";
	}
	
	//�����̨��������ʽ($str,$type)��$typeĬ��Ϊ0ʱ$strΪ���������ǣ�1��ʾ$strΪ�ı�����
	//��Ա���ģ�������ʽ($str,$type)��$str���������Ա���İ�����ʶ��ֱ�ӵ��ı����ݣ�$typeĬ��Ϊ0ֱ����ʾ���ݣ�tip-�����ص���ʾ��fix-�̶�����ʾ��
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
	
	//hidden��������ĳ������ֵ�̶�����,����url�̶�����
	//title����
	function s_additem($key = '',$cfg = array()){//��׷��$key��$cfg֮��Ĵ���
		$this->oS->additem($key,$cfg);
	}
	
	function s_header(){
		echo form_str('arcs_'.md5($this->A['url']),$this->A['url']);
		tabheader_e();
		echo "<tr><td class=\"".($this->mc ? 'item2' : 'txt txtleft')."\">\n";
		trhidden('page',$this->A['page']);
	}
	
	function s_deal_str(){//��oS�д�����ת����ǰ������
		$this->s_sqlstr();
		$this->s_filterstr();
	}
	
	//����sql
	function s_sqlstr(){
		$wherestr = empty($this->A['where']) ? '' : " AND {$this->A['where']}";
		foreach($this->oS->wheres as $k => $v) $wherestr .= " AND $v";
		if($wherestr) $wherestr = " WHERE ".substr($wherestr,5);
		$this->sqlall = "SELECT ".$this->A['select'].' FROM '.$this->A['from'].$wherestr.(empty($this->area['copyspace']) ? '' : ' GROUP BY copyid').' ORDER BY '. $this->oS->orderby;
		$this->sqlnum = 'SELECT '.(empty($this->area['copyspace']) ? 'COUNT(*)' : 'COUNT(DISTINCT copyid)').' FROM '.$this->A['from'].$wherestr;
	}
	
	//ɸѡ������url�еĴ���
	function s_filterstr(){
		foreach($this->oS->filters as $k => $v){
			$this->filterstr .= "&$k=".(is_numeric($v) ? $v : rawurlencode($v));
		}
	}
	
	function s_view_one($key = ''){
		if(empty($key) || empty($this->oS->htmls[$key])) return;
		echo $this->oS->htmls[$key].' &nbsp;';
		unset($this->oS->htmls[$key]);
	}
	
	function s_view_array($incs = array()){
		if($incs){
			foreach($incs as $k) $this->s_view_one($k);
		}else{
			foreach($this->oS->htmls as $k => $v) $this->s_view_one($k);
		}
	}
	function s_adv_point(){
		echo strbutton('bfilter','ɸѡ');
		echo "\n &nbsp;<input class='checkbox' type='checkbox' name='cbsMore' id='cbsMore' value='1' onclick=\"display('boxMore')\"".($this->A['cbsMore'] ? "checked = 'checked'" : " ")."/>�߼�ѡ��</label>";	
		echo "\n<div id='boxMore'".(!$this->A['cbsMore'] ? " style='display:none'>" : " style='display:'>");
		$this->A['MoreSet'] = 1;
	}
	
	function s_footer(){
		if(empty($this->A['MoreSet'])){
			echo strbutton('bfilter','ɸѡ');
		}else echo "</div>";//�߼�����β
		tabfooter();
		unset($this->oS);
	}

	function m_header($title = '',$addmode = 0){//$addmode=1ʱ��Ĭ�ϵı�������$title���ݣ�����$title�滻Ĭ��title
		if(!$title || $addmode){
			$pcfg = cls_PushAreaBase::Config($this->A['paid']);
			if($pcfg['sourcetype'] == 'catalogs'){ //��Ŀ����, Ĭ����ת����Ŀ����ҳ
				if(empty($pcfg['sourceid'])){
					$_aurl = "?entry=catalogs&action=catalogedit";
					$_titile = "��Ŀ����";
				}else{
					$_aurl = "?entry=coclass&action=coclassedit&coid={$pcfg['sourceid']}";
					$_cotypes = cls_cache::Read('cotypes'); 
					$_titile = "[{$_cotypes[$pcfg['sourceid']]['cname']}]����";
				}
			}else{
				$_aurl = "?entry=extend&extend=push&paid={$this->A['paid']}";
				$_titile = empty($pcfg['forbid_useradd']) ? "�ֶ����" : ''; //����Ϊ:[��ֹ�ֶ����]��,�����������
			}
			$tt = "{$this->area['cname']}-���͹���";
			$_titile && $tt .= " &nbsp;>><a href=\"$_aurl\" onclick=\"return floatwin('open_push_{$this->A['paid']}',this)\">$_titile</a>";
		}
		$title = $addmode ? ($tt.$title) : ($title ? $title : $tt);
		tabheader($title,'','',20);
	}
	function m_additem($key = '',$cfg = array()){//�����б��е���
		if(!$key) $this->message('������Ŀ��key����Ϊ��');
		if($this->oC->additem($key,$cfg) == 'undefined') $this->message("����Ŀ{$key}δ�ҵ�������");
	}
	
	function m_addgroup($mainstyle,$topstyle = ''){//���ӷ���
		if(!$mainstyle) return;
		$this->oC->addgroup($mainstyle,$topstyle);
	}
	
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
	
	function m_db_array(){
		if(empty($this->rs)){
			global $db;
			$pagetmp = $this->A['page'];
			do{
				$query = $db->query("{$this->sqlall} LIMIT ".(($pagetmp - 1) * $this->A['rowpp']).",{$this->A['rowpp']}");
				$pagetmp--;
			} while(!$db->num_rows($query) && $pagetmp);
			$re = array();
			while($r = $db->fetch_array($query)){
				$re[] = cls_pusher::ViewOneInfo($r);
			}
			$this->rs = $re;
		}
		return $this->rs;
	}
			
	//�����б������ݼ���ʾhtml
	function m_view_main(){
		$rs = $this->m_db_array();
		if($this->A['cols']){//�������ͨ��һ�ֶ����ʽ��װ��������Ϊһ����Ŀ���
			$html = '';$i = 0;
			foreach($rs as $k => $v){
				if(!($i % $this->A['cols'])) $html .= "<tr".($this->mc ? '' : " class=\"txt\"").">\n";
				$html .= "<td class=\"".($this->mc ? 'item2' : 'txtL')."\">".$this->m_one_row($v)."</td>\n";
				$i ++;
				if(!($i % $this->A['cols'])) $html .= "</tr>\n";
			}
			if($i-- % $this->A['cols']) $html .= "</tr>\n";
			echo $html;
		}else{
			foreach($rs as $k => $v){
				echo $this->m_one_row($v);
			}
		}
	}
	
	function m_one_row($data = array()){//�������ĵ�������
		$narr = $this->oC->fetch_one_row($data);
		if($this->A['cols']){//�������ͨ��һ�ֶ����ʽ��װ��������Ϊһ����Ŀ���
			if(empty($this->A['mcols_style'])) $this->A['mcols_style'] = '{selectid} &nbsp;{subject}';
			return key_replace($this->A['mcols_style'],$narr);
		}else{
			$cfgs = $this->oC->cfgs;
			
			//����һ�е�hmtl
			$re = "<tr".($this->mc ? '' : " class=\"txt\"").">\n";
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

	//ÿ�����ݵ���ʽӦ��
	function m_view_td($content = '',$cfg = array()){
		$width = empty($cfg['width']) ? '' : " w{$cfg['width']}";
		$class = $this->mc ? 'item' : 'txt';
		if(!empty($cfg['side']) && in_array($cfg['side'],array('L','R',))){
			$class = $this->mc ? ($cfg['side'] == 'L' ? 'item2' : 'item1') : ($cfg['side'] == 'L' ? 'txtL' : 'txtR');
		}
		$class .= empty($cfg['width']) ? '' : " w{$cfg['width']}";
		return "<td class=\"$class\">$content</td>\n";
	} 
	
	function m_footer(){//�б����ײ�
		global $db;
		tabfooter();
		$counts = $db->result_one($this->sqlnum);
		$multi = multi($counts,$this->A['rowpp'],$this->A['page'],$this->A['url'].$this->filterstr);
		echo $multi;
		foreach(array('oC','sqlall','sqlnum','filterstr',) as $k) unset($this->$k);
	}
	
	function o_additem($key,$cfg = array()){
		$re = $this->oO->additem($key,$cfg);
		if($re == 'undefined') $this->message("����������{$key}δ�ҵ�������");
	}
	
	function o_header($title = '',$addmode = 0){//$addmode=1ʱ��Ĭ�ϵı�������$title���ݣ�����$title�滻Ĭ��title
		if(!$title || $addmode){
			$tt = "{$this->area['cname']}-��������";
		}
		$title = $addmode ? ($tt.$title) : ($title ? $title : $tt);
		tabheader($title,'','',10);
	}
	
	
	//���ص�����ѡ���������ʾ����
	function o_view_one_bool($key){
		$re = $this->oO->view_one_bool($key);
		if($re == 'undefined') $this->message("����������{$key}δ�ҵ�������");
		return $re;
	}
	//��ѡ�����������չʾ
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
		$addstr = ' &nbsp;'.OneCheckBox('needorefresh','ͬʱ��������',1);
		tabfooter($button,$button ? ($bvalue ? $bvalue : '�ύ') : '',$addstr);
	}
	
	function o_end_form($button = '',$bvalue = ''){
		//echo $this->mc ? '<br>' : '<br><br>';
		echo "\n<div align=\"center\" style='display:block;clear:both;'>".strbutton($button,$button ? ($bvalue ? $bvalue : '�ύ') : '')."</div>\n</form>\n";
	}
	
	function sv_header(){
		if(empty($GLOBALS[$this->A['mfm']])){
			if(empty($GLOBALS[$this->A['ofm']])) $this->message('��ѡ�������Ŀ',$this->A['url']."&page={$this->A['page']}".$this->filterstr);
			if(empty($GLOBALS['selectid'])) $this->message('��ѡ���ĵ�',$this->A['url']."&page={$this->A['page']}".$this->filterstr);
		}
	}
	
	// msg �����Զ�������У���õĶ�����Ϣ����[�޶�����]��Ϣ
	function sv_footer($msg=''){
		$c_upload = cls_upload::OneInstance();
		$c_upload->saveuptotal(1);
		$this->mc || adminlog('������������','�����б�������');
		$url = $this->A['url']."&page={$this->A['page']}".$this->filterstr;
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
			if(!empty($mfm[$r['pushid']])){
				foreach($mfm[$r['pushid']] as $key => $val){
					$this->oE->set_one($key,$val,$r);
				}
			}
		}
	}
	
	//����ϼ���������
	function sv_o_load(){
		if(empty($GLOBALS['selectid'])) $this->message('��ѡ���ĵ�',$this->A['url']."&page={$this->A['page']}".$this->filterstr);
		if($this->A['isab'] != 2) $this->message('���Ǻϼ����ز���',$this->A['url']."&page={$this->A['page']}".$this->filterstr);
		
		$arc = &$this->oO->arc;
		if(empty($arc)) $arc = new cls_arcedit;
		foreach($GLOBALS['selectid'] as $aid){
			$arc->set_aid($aid,$this->A['multi_chid'] ? array() : array('chid' => $this->A['chid']));
			$arc->set_album($this->A['pid'],$this->A['arid']);
		}
		
		$this->mc || adminlog('�ϼ����ز���','�ĵ��б�������');
		$this->message('�����������',axaction(5,$this->A['url']."&page={$this->A['page']}".$this->filterstr));
	}
	//����ϼ���������
	function sv_o_pushload(){
		if(empty($GLOBALS['selectid'])) $this->message('��ѡ���ĵ�',$this->A['url']."&page={$this->A['page']}".$this->filterstr);
		if($this->A['isab'] != 3) $this->message('��������λ���ز���',$this->A['url']."&page={$this->A['page']}".$this->filterstr);
		
		$arc = &$this->oO->arc;
		if(empty($arc)) $arc = new cls_arcedit;
		foreach($GLOBALS['selectid'] as $aid){
			$arc->set_aid($aid,$this->A['multi_chid'] ? array() : array('chid' => $this->A['chid']));
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
	
	function sv_o_all(){
		$ofm = @$GLOBALS[$this->A['ofm']];
		$selectid = @$GLOBALS['selectid'];
		$rs = $this->m_db_array();//�ٴ����Ʒ�Χ���Է�����Ȩ�޽��в���
		if($ofm && $selectid && $rs){
			foreach($rs as $r){
				if(!in_array($r['pushid'],$selectid)) continue;
				$this->oO->push = &$r;
				
				if(!empty($ofm['delete'])){//ɾ���򲻼�����������
					$this->sv_o_one('delete');
					continue;
				}
				
				foreach($ofm as $key => $v){
					$this->sv_o_one($key);
				}
				cls_pusher::updatedb($r['pushid']);//������¼����
			}
		}
		
		if(@$GLOBALS['needorefresh']){//ѡ����ͬʱ��������
			cls_pusher::ORefreshPaid($this->A['paid']);
		}
	}
    
    /**
     * ��ȡ����λ���ͣ�select���ݸ�ʽʹ�ã�
     * 
     * @return array $selectDatas ����select���ݸ�ʽʹ������λ����
     * @since  nv50
     */
    public static function getPushTypesInSelect()
    {
        $selectDatas = array(0 => '����ѡ��');
        $push_types = cls_cache::Read('pushtypes');
        foreach ( $push_types as $push_type ) 
        {
            $selectDatas[$push_type['ptid']] = $push_type['title'];
        }
        
        return $selectDatas;
    }
	
    
    /**
     * ������λ����ID��ȡ����λ�б�select���ݸ�ʽʹ�ã�
     * 
     * @return array $selectDatas ����select���ݸ�ʽʹ������λ����
     * @since  nv50
     */
    public static function getPushAreasInSelect( $ptid )
    {
        $ptid = (int) $ptid;
        $selectDatas = array(0 => '����ѡ��');
        $pushareas = cls_PushArea::Config();
        
        foreach ( $pushareas as $k => $v ) 
        {
            if ( $v['ptid'] == $ptid )
            {
                $selectDatas[$k] = $v['cname'];
            }            
        }
        return $selectDatas;
    }
}
