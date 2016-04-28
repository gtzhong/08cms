<?php
/**
 * �����б����Ĳ�������	 
 */
!defined('M_COM') && exit('No Permisson');
class cls_culistbase extends cls_cubasic{

	//�����й�
	public $oS	= NULL;//�������Ķ���
	public $sqlall = '';//����sql�ִ�
	public $acount = 0;//��Ϣ������ͳ��
	public $filterstr = '';//ɸѡ������url�еĴ����ִ�
	
	public $oC	= NULL;//�����ݴ���Ķ���
	public $oO	= NULL;//������Ŀ����Ķ���
	public $rs	= array();//�����ݴ�����ݴ���ѡ�б���������
	
	public $tomid = 'tomid'; //��������,�����߻�Աid���ֶ�,�������,�봫�ݹ���
			
    /**
	 * ���캯����ʼ������
	 */
	
	// list�£�pchid: �����б�����
	function __construct($cfg = array()){
		parent::__construct($cfg);
		isset($cfg['tomid']) && $this->tomid = $cfg['tomid'];
		global $db,$tblprefix;
		$curuser = cls_UserMain::CurUser();
		//if(empty($this->A['mode'])) $this->A['mode'] = '';//����ģʽ���ã�''Ϊ��ͨ�����б�'pushload'Ϊ����λ���ع���
		
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
		
		//sql��ѯ��ʼ����where��from��select�����գ��ɸ���һ���֣�whereҪ�ԡ�[ AND ]��ʼ��
		$_select = "SELECT cu.*,cu.createdate AS cucreate,cu.mid as cu_mid,cu.mname as cu_mname";	
		// ע��:���ĵ�����ʱ,�ĵ���createdate�Ḳ�ǽ������ֶ�, ����:cu.createdate AS cucreate
		//      ���Ա����ʱ,��Ա��mid,mname�Ḳ�ǽ������ֶ�, ����:cu.mid as cu_mid,cu.mname as cu_mname 
		$_from = "FROM {$tblprefix}{$this->cucfgs['tbl']} cu";
		if($this->ptype=='m'){ //����Ա ���� ����Ա�Ľ���
			$selectsql = "$_select ".murl_fields('m.');
			$wheresql = " WHERE m.mchid={$this->pchid} ";
			$fromsql = " $_from INNER JOIN {$tblprefix}members m ON m.mid=cu.{$this->tomid}";
		}elseif($this->ptype=='a'){ // ����Ա ���� ���ĵ��Ľ���
			$selectsql = "$_select ".aurl_fields('a.');
			$wheresql = ' WHERE 1=1 '; // $this->cucfgs['tbl']
			if(!empty($this->A['caid']) && $cnsql = cnsql(0,sonbycoid($this->A['caid']),'a.')) $wheresql .= " AND $cnsql";
			$fromsql = " $_from INNER JOIN {$tblprefix}".atbl($this->pchid)." a ON a.aid=cu.aid";
		}elseif($this->ptype=='e'){ //��վ����,��Ŀ����
			$selectsql = $_select;
			$wheresql = " WHERE 1=1 ";
			$fromsql = $_from;
		}elseif($this->ptype=='u'){ //�Զ���where��from��select
			$selectsql = $cfg['select'];
			$wheresql = " WHERE 1=1 ".$cfg['where'];
			$fromsql = $cfg['from'];
		}
		$_arr = array('select'=>$selectsql,'where'=>$wheresql,'from'=>$fromsql);
		if($this->ptype == 'u'){
			foreach($_arr as $k=>$v){
				$this->A[$k] = $v;	
			}		
		}else{
			foreach($_arr as $k=>$v){
				$this->A[$k] = $v.' '.$this->A[$k];	
			}
		}
		
		//��������
		if(empty($this->A['ofm'])) $this->A['ofm'] = 'arcdeal';//����ѡ����ı��������������磺ѡ����������Ŀ��arcdeal['caid']=1
		if(empty($this->A['opre'])) $this->A['opre'] = 'arc';//����ֵ�Ĳ���ǰ׺���磺������ĿidΪ23ʱ��arccaid=23
		
		$cfg = array();
		foreach($this->A as $k => $v){
			if(!in_array($k,array('url','cbsMore','MoreSet','where','from','select',))) $cfg[$k] = $v;
		}
		
		//�������Ķ���
		$this->oS = new cls_cuschs($cfg);
		//�����ݴ���Ķ���
		$this->oC = new cls_cucols($cfg);
		//��������Ķ���
		$this->oO = new cls_cuops($cfg);
    }
	
	/**
	* ɸѡ�����Ŀ                                    
	*/
	
	function s_additem($key = '',$cfg = array()){//��׷��$key��$cfg֮��Ĵ���
		$this->oS->additem($key,$cfg);
	}
	
	/**
	* ɸѡ��ͷ��
	*/
	function s_header(){
		echo form_str('cus_'.md5($this->A['url']),$this->A['url']);
		tabheader_e();
		echo "<tr><td class=\"".($this->mc ? 'item2' : 'txt txtleft')."\">\n";
		trhidden('page',$this->A['page']);
	}
	
	/**
	* ɸѡ�ַ���sql������װ�Ͳ�ѯ����
	*/
	function s_deal_str(){//��oS�д�����ת����ǰ������
		$this->s_sqlstr();
		$this->s_filterstr();
	}
	
	/**
	* sql����
	*/
	function s_sqlstr(){
		global $db,$tblprefix;
		if(!empty($this->oS->wheres)){ 
			foreach($this->oS->wheres as $k => $v) $this->A['where'] .= " AND $v";//�������Ӳ�����where����
		}
		if(!$this->acount = $db->result_one('SELECT COUNT(*) '.$this->A['from'].$this->A['where'])){
			$this->acount = 0;
		}
		$this->sqlall = $this->A['select'].$this->A['from'].$this->A['where'].' ORDER BY '. $this->oS->orderby;
	}

	/**
	* ɸѡ�ַ�����url��װ
	*/
	function s_filterstr(){
		foreach($this->oS->filters as $k => $v){
			$this->filterstr .= "&$k=".(is_numeric($v) ? $v : rawurlencode($v));
		}
	}
	
	
	/**
	* �б�����Ŀ����ʾ
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
	* @param    array    $incs  ��Ŀ�ؼ������� Ĭ��Ϊ��                                 
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
	*/
	function s_adv_point(){
		echo strbutton('bfilter','ɸѡ');
		echo "\n &nbsp;<input class='checkbox' type='checkbox' name='cbsMore' id='cbsMore' value='1' onclick=\"display('boxMore')\"".($this->A['cbsMore'] ? "checked = 'checked'" : " ")."/>�߼�ѡ��</label>";	
		echo "\n<div id='boxMore'".(!$this->A['cbsMore'] ? " style='display:none'>" : " style='display:'>");
		$this->A['MoreSet'] = 1;
	}
	
	/**
	* ɸѡ��β��                                
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
	* @param    string    $title    �б���� Ĭ��Ϊ���õ�title
	* @param    int       $pid      ���ӽ�����������,pidΪ��������ID�������۵����ű���ID��
	* @param    string    $exstr    ������Ϣ  
	$ Demo : $oL->m_header('', $aid, $aid ? " &nbsp; &nbsp; <a href='?entry=extend&extend=$extend='>ȫ������&gt;&gt;</a>" : '');                          
	*/
	function m_header($title = '', $pid=0, $exstr = ''){
		$title = $title ? $title : $this->cucfgs['cname'].' �б�';
		if(!empty($pid)){
			$title = "[".$this->getPLink($pid, array())."] $title";	
		}
		if(!empty($exstr)){
			$title .= $exstr; 
		}
		tabheader($title,'','',20);
	}
	
	
	/**
	* �б������Ŀ
	* @ex  s_additem('subject',array('title'=>'����','hidden'=>1,));
	* @param    string    $key  ��Ŀ�ؼ��� Ĭ��Ϊ��
	* @param    array     $cfg  ��Ŀ���ò��� ����ѡ��Ĭ��Ϊ��
	*					��Ϊ title��Ŀ����, hidden��ĳ������ֵ�̶�����, url��ת���ӵȵ�                                  
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
			while($r = $db->fetch_array($query)){ 
				if($this->fhidden){ //Ҫ�����ش�����ֶΣ����⴦��
					foreach($this->fhidden as $_k){
						$r[$_k]	= cls_string::SubReplace($r[$_k]);
					}
				}
				$re[] = $r;
			}
			$this->rs = $re;
		}
		return $this->rs;
	}
	
	/**
	* �����б������ݼ���ʾhtml                                     
	*/		
	function m_view_main(){
		$rs = $this->m_db_array();
		foreach($rs as $k => $v){
			echo $this->m_one_row($v);
		}
	}
	
	/**
	* ����������������                                   
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
	*/
	function m_footer(){
		global $db;
		tabfooter();
		echo multi($this->acount,$this->A['rowpp'],$this->A['page'],$this->A['url'].$this->filterstr);
		foreach(array('oC','sqlall','acount','filterstr',) as $k) unset($this->$k);
	}
	
	/**
	* ���������������Ŀ
	* @ex  $oL->o_additem('delete',array('skip'=>1));
	* @param    string    $key  ��Ŀ�ؼ��� Ĭ��Ϊ��
	* @param    array     $cfg  ��Ŀ���ò��� ����ѡ��Ĭ��Ϊ��
	*                        ��Ϊ skip=>1  ���Դ���Ŀ ���ڽ�������,��̬�����ǰ�����Ƿ��д˲���Ȩ��         
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
		if(!$this->A['cuid']) return;
		$na = cls_pusher::paidsarr('commus',$this->A['cuid']);
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
	*���ص�����ѡ���������ʾ����                 
	*/
	function o_view_one_bool($key){
		$re = $this->oO->view_one_bool($key);
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
	*��ѡ�����������չʾ
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
		if(empty($GLOBALS['selectid'])) $this->message('��ѡ�񽻻�',$this->A['url']."&page={$this->A['page']}".$this->filterstr);
	}
	
	// msg �����Զ�������У���õĶ�����Ϣ����[�޶�����]��Ϣ
	function sv_footer($msg=''){
		$this->mc || adminlog('������������','�����б�������');
		$url = $this->A['url']."&page={$this->A['page']}".$this->filterstr;
		$exmsg = $msg;
		if(!empty($this->oO->cnt_msgs)){
			foreach($this->oO->cnt_msgs as $imsg){
				$exmsg .= '<br>'.$imsg;
			}
		}
		$this->message('�����������'.$exmsg,$url);
	}
	
	//�������ͼ�������
	function sv_o_pushload(){
		$selectid = @$GLOBALS['selectid'];
		if(empty($selectid)) $this->message('��ѡ�񽻻���¼',$this->A['url']."&page={$this->A['page']}".$this->filterstr);
		if($this->A['mode'] != 'pushload') $this->message('��������λ���ز���',$this->A['url']."&page={$this->A['page']}".$this->filterstr);
		
		$auser = &$this->oO->auser;
		if(empty($auser)) $auser = new cls_userinfo;
		
		$rs = $this->m_db_array();//�ٴ����Ʒ�Χ���Է�����Ȩ�޽��в���
		foreach($rs as $r){
			if(!in_array($r['cid'],$selectid)) continue;
			$auser->activeuser($r['cid']);
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
	*/
	function sv_o_all($cfg=array()){
		$ofm = @$GLOBALS[$this->A['ofm']];
		$selectid = @$GLOBALS['selectid'];
		$rs = $this->m_db_array();//�ٴ����Ʒ�Χ���Է�����Ȩ�޽��в���
		if($ofm && $selectid && $rs){
			$actcu = &$this->oO->actcu;
			foreach($rs as $r){ 
				if(!in_array($r['cid'],$selectid)) continue;
				$actcu = $this->getRow($r['cid']);
				if(!empty($ofm['delete'])){//ɾ���򲻼�����������
					$this->sv_o_one('delete');
					continue;
				}
				foreach($ofm as $key => $v){ 
					$this->sv_o_one($key);
				}
				//$auser->updatedb();
			}
		}
	}
}
