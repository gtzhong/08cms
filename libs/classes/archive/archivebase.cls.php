<?php
/**
 *  archivebase.cls.php �����ĵ���ӻ�༭�Ĳ������� 
 *		sv����������return_error��ʾ����ʱ����error����������message����
 *
 * @copyright			(C) 2008-2013 08cms
 * @license				http://www.08cms.com/license/
 * @lastmodify			2013-2-23
 */

!defined('M_COM') && exit('No Permisson');
class cls_archivebase{
	protected $mc = 0;//��Ա����
	public $A = array();//��ʼ��������š���chid��caid(��Ŀ)
	public $isadd = 0;//���ģʽ
	public $aid = 0;//�ĵ�id
	public $chid = 0;//�ĵ�ģ��id
	public $caid = 0;//�ĵ���Ŀid
	public $fmdata = 'fmdata';//form�е���������//������������
	public $predata = array();//Ԥ����������
	public $channel = array();//�ĵ�ģ��
	public $fields = array();//ģ���ֶ�
	public $stid = 0;//����ֱ�id
	public $coids = array();//ģ�����ڷֱ��������ϵ�ֶ�
	public $coids_showed = array();//�Ѿ���ʾ�˵���ϵ�ֶ�
	public $fields_did = array();//�ݴ��Ѵ�������ֶ�
	public $arc = NULL;//�ĵ���
	
	
	/**
	 * ��ȡ�����ĵ���ģ��
	 */
	public function get_chid(){
		return $this->chid;
	}
	
	/**
	 * ���캯����ʼ������
	 * $cfg['chid'] : ָ���ĵ�ģ��, δָ����GET��global����
	 * $cfg['caid'] : ָ���ĵ���Ŀ, δָ����GET/POST��global����
	 */
    function __construct($cfg = array()){
		$this->mc = defined('M_ADMIN') ? 0 : 1;
		//$isadd��Ҫָ����ͨ��url���ݣ�ͨ���Ƿ�ָ��aid��ʶ�𣬲����Ļ�Ϊ���ģʽ��0Ϊ����༭��1Ϊ�ĵ����
		$this->isadd = empty($GLOBALS['aid']) ? 1 : 0;
		$this->A = $cfg;
		if($this->isadd){ //���ʱ��Ҫ
			if(isset($cfg['chid'])) $this->chid = $cfg['chid'];
			if(isset($cfg['caid'])) $this->caid = $cfg['caid'];
		}
    }
	
	/**
	* ������ĵ���������ʱѡ���Ը�λ�����ĳЩ������������һ������
	*
	* @param    array     $vars  $vars��Ҫ��ʼ���ı�����������Ĭ��Ϊarray('aid','predata','fields_did',)
	* 
	*/
	function next_init($vars = array()){
		$vars || $vars = array('aid','predata','fields_did',);
		foreach($vars as $var){
			if($var == 'fmdata'){
				$this->$var = 'fmdata';
			}if($var == 'arc'){
				$this->$var = NULL;
			}elseif(in_array($var,array('aid','chid','stid',))){
				$this->$var = 0;
			}else $this->$var = array();
		}
	}
	
	/**
	* �����Ի���
	*
	* @param    string     $str  ��ʾ�ַ��� Ĭ��Ϊ��
	* @param    string     $url  ��תurl ��Ĭ��Ϊ��
	* @param    int        $return_error  ��תurl ��Ĭ��Ϊ0 $return_errorΪ1ʱ�������������ش�����Ϣ
	*							
	*/
	function message($str = '',$url = '',$return_error = 0){
		if($return_error){
			return $str;
		}else{
			call_user_func('cls_message::show',$str,$url);
		}
	}
	function setvar($key,$var){
		$this->$key = $var;	
	}
	
	function top_head(){
		$curuser = cls_UserMain::CurUser();
		if($this->mc){
			if(!defined('M_COM')) exit('No Permission');
		}else{
			if(!defined('M_COM') || !defined('M_ADMIN')) exit('No Permission');
			aheader();
			if($re = $curuser->NoBackFunc('normal')) $this->message($re);
		}
		echo "<title>����".($this->isadd ? '���' : '����')."</title>";
	}
	
	/**
	 * ��ȡ���п������ϣ���ģ�͡��ֶΡ����ĵ�
	 */
	function read_data(){
		$this->find_chid();
		$this->read_archive();
		$this->read_cfg();
		return;
	}
	
	/**
	 * ��ȡ�ĵ�ģ��
	 */	
	function find_chid(){
		if(empty($this->chid)){
			$this->chid = empty($GLOBALS['chid']) ? 0 : max(0,intval($GLOBALS['chid']));
		}
		if(!$this->chid && !empty($GLOBALS['caid'])){
			if(($catalog = cls_cache::Read('catalog',$GLOBALS['caid'])) && $chids = array_filter(explode(',',$catalog['chids']))) $this->chid = current($chids);
			// ǰһ��, array_filter���ֵ����, ���ܵ�һ��Ϊ�����1��ʼ, ������currentȡ��һ���� һ��ָ����ģ�ͻ���url�д�������,��������һ���ò���
		}
		if($this->chid && !($this->channel = cls_channel::Config($this->chid))){
			$this->chid = 0;
			$this->message('��ָ���ĵ����͡�');
		}
		return;
	}
	
	/**
	 * ��ȡ�ĵ������ݸ�ֵ��$this->predata
	 */	
	function read_archive(){
		if($this->isadd) return;
		if(!($aid = max(0,intval($GLOBALS['aid'])))) $this->message('��ָ���ĵ���');
		if(empty($this->arc)){
			$this->arc = new cls_arcedit;
			if(!($this->aid = $this->arc->set_aid($aid,array('chid'=>$this->chid,'ch'=>1,'au'=>1,)))) $this->message('��ָ���ĵ���');
			$this->chid || $this->chid = $this->arc->archive['chid'];
			$this->predata = &$this->arc->archive;
			if($re = $this->NoBackPm($this->predata['caid'])) $this->message($re);
	}
		return;
	}
	
	/**
	* �����ɫ����Ŀ����Ȩ�ޣ����ڹ����̨��ʹ��
	*
	* @param    int     $caid  ��ĿID Ĭ��Ϊ0
	*							
	*/
	function NoBackPm($caid = 0){
		$curuser = cls_UserMain::CurUser();
		if($this->mc) return '';
		if(!$caid) return '��ָ����Ŀ';
		return $curuser->NoBackPmByTypeid($caid,'caid');
	}	
	
	/**
	* ��Ա����ֻ�ܱ༭���˷������ĵ�
	*							
	*/
	function allow_self(){
		$curuser = cls_UserMain::CurUser();
		if($this->isadd) return;
		if($this->predata['mid'] != $curuser->info['mid']) $this->message('�Բ�����ѡ�������ѵ��ĵ���');
	}
	
	/**
	 * ��ȡ�ĵ�ģ�͵��ֶ�����
	 */
	function read_cfg(){
		$splitbls = cls_cache::Read('splitbls');
		if(!($this->channel = cls_channel::Config($this->chid))) $this->message('��ָ���ĵ����͡�');
		$this->fields = cls_cache::Read('fields',$this->chid);
		$this->stid = $this->channel['stid'];
		$this->coids = empty($splitbls[$this->stid]['coids']) ? array() : $splitbls[$this->stid]['coids'];
		return;
	}
	
	
	/**
	* ��Ա����-�����ʾ��Ϣ
	*
	* @param    array     $cfg  ���ò��� Ĭ��Ϊ��
	*						cfg[limit]=array(800,32),�ܹ��ɷ�����800,�ѷ�������32
	*						cfg[daymax]=array(200,5),����ɷ�����200,�ѷ�������5
	* 						cfg[voild]=array(200,12,90),�ܹ����ϼ���200,���ϼ�����12,�ϼ�����90��
	* @param    string    $msg  ��ʾ��Ϣ�ַ��� Ĭ��Ϊ��
	*							
	*/
	function getmtips($cfg=array(),$msg=''){
		if($msg) $msg = "<br/>$msg"; // && !strstr($msg,'<li>')
		if(!empty($cfg['check'])){ 
			$curuser = cls_UserMain::CurUser();
			$cancheck = $this->channel['autocheck'];
			if(intval($cancheck)<0){
				$cancheck = $curuser->noPm(-$this->channel['autocheck']); 
				if($cancheck) $msg .= "<br/>����������Ϣû�� <span class='tipm_bred'>���</span> Ȩ�ޣ�(ԭ��:{$cancheck})��";
				else $msg .= "<br/>�����ڵĻ�Ա�� ��������Ϣӵ�� <span class='tipm_bred'>ֱ�����</span> Ȩ�ޡ�";
			}else{
				if($cancheck) $msg .= "<br/>�����ڵĻ�Ա�� ��������Ϣӵ�� <span class='tipm_bred'>ֱ�����</span> Ȩ�ޡ�";
				else $msg .= "<br/>����������Ϣû�� <span class='tipm_bred'>���</span> Ȩ�ޣ�(ԭ��:ϵͳ��ֹ���)��";
			}
		}
		$a = array('limit'=>'����','daymax'=>'����','valid'=>'�ϼ�',);
		foreach($a as $key=>$title){
			if(isset($cfg[$key])){  
				$total = $cfg[$key][0];
				$pub = $cfg[$key][1];
				$msg .= "<br/>�����ڵĻ�Ա�� ".($key=='daymax' ? '24Сʱ��' : '')."�ܹ���{$title} <span class='tipm_bred'>$total</span> ������{$title} <span class='tipm_bred'>$pub</span> ��������{$title} <span class='tipm_bred'>".($total - $pub)."</span> ����";
				if($key=='valid'){ 
					if(($total-$pub)>0){ 
						$msg .= empty($cfg[$key][2]) ? "" : " �ϼ�����Ϊ��<span class='tipm_bred'>".$cfg[$key][2]."</span>�졣";
					}else{
						$msg .= " �ϼܳ�����Ϣ����ڲֿ�,<span class='tipm_bred'>������ʾ</span>��ǰ̨��";
					}
				}			}
		}
		$msg && $msg = substr($msg,5);
		return $msg;
	}
	
	/**
	* ��Ŀ������Ԥ����
	*							
	*/
	function fm_pre_cns(){
		if(!$this->isadd) return;//�����ʱ��Ҫ
		$cotypes = cls_cache::Read('cotypes');
		if(empty($this->caid)){
			$this->predata['caid'] = empty($GLOBALS['caid']) ? 0 : max(0,intval($GLOBALS['caid']));
		}else{
			$this->predata['caid'] = $this->caid;	
		}
		foreach($cotypes as $k => $v){
			if(!$v['self_reg']  && in_array($k,$this->coids)){
				$this->predata['ccid'.$k] = empty($GLOBALS['ccid'.$k]) ? '' : trim($GLOBALS['ccid'.$k]);
			}
		}
		$this->predata = array_filter($this->predata);
		return;
	}
	
	/**
	* ������ǰ��Ա�ķ���Ȩ�޼�����Ȩ�ޣ���fm_pre_cns֮��ִ��
	*							
	*/
	function fm_allow(){
		$curuser = cls_UserMain::CurUser();
		if(!$this->mc && !empty($this->predata['caid']) && $re = $this->NoBackPm($this->predata['caid'])) $this->message($re);
		if($this->isadd && ($re = $curuser->arcadd_nopm($this->chid,$this->predata))) $this->message($re);
	}
	
	
	/**
	* ָ���������ϼ��Ĵ���
	*
	* @param    string    $pidkey  �ϼ��ֶ��� Ĭ��Ϊpid
	* @param    int       $mc      ��ʶ������ָ��ǰ̨���ǻ�Ա�ռ�	
	*						 1  ָ���Ա�ռ�
	*	   					 0  ָ��ǰ̨��Ĭ�ϣ�
	* exurl ������չ,һ����etools��ʵ�֣��磺etools/ajax.php?action=ajax_arc_mylist
	*/
	function fm_album($pidkey = 'pid',$mc=0,$exurl=''){
		if(!$pidkey) return;
		$p_album = $this->fm_find_album($pidkey);
		$this->fm_view_album($pidkey,$p_album,$mc,$exurl);
	}
	
	/**
	* �����Ƿ�ָ���������ϼ�
	*
	* @param    string    $pidkey  �ϼ��ֶ��� Ĭ��Ϊpid
	* ��� pid=-1����ѡ�����ϼ�
	*/
	function fm_find_album($pidkey = 'pid'){
		global $db,$tblprefix; //$pid=-1,ѡ�����ϼ�
		$pid = empty($GLOBALS[$pidkey]) ? @$this->predata[$pidkey] : max(-1,intval($GLOBALS[$pidkey]));
		if($pid==-1) return $pid;
		if(!$pid || !($ntbl = atbl($pid,2)) || !$p_album = $db->fetch_one("SELECT * FROM {$tblprefix}$ntbl WHERE aid='$pid'")) $p_album = array();
		return $p_album;
	}
	
	/**
	* ��ʾ�ϼ���Ϣ
	*
	* @param    string    $pidkey  �ϼ��ֶ��� Ĭ��Ϊpid
	* ��� pid=-1����ѡ�����ϼ�
	* exurl ������չ,һ����etools��ʵ�֣��磺etools/ajax.php?action=ajax_arc_mylist
	*/
	function fm_view_album($pidvar = 'pid',$p_album = array(),$mc=0,$exurl=''){
		if($p_album==-1){ //ѡ�����ϼ�
			$pchid = max(0,intval(@$GLOBALS['pchid']));
			if(empty($pchid)) return;
			$p_channel = cls_channel::Config($pchid);
			trhidden("{$this->fmdata}[$pidvar]",'');
			trbasic('<font color="red"> * </font>���� - '.$p_channel['cname'],$this->fmdata.'[pid_label]','','text',array('w'=>60,'validate'=>'rule="text" must="1" mode="" rev="����'.$p_channel['cname'].'"','guide'=>'������������������'));	
			?> 
			<script type="text/javascript">
			var fmdata = '<?php echo $this->fmdata; ?>';
			function createobj(element,type,value,id){
				var e = document.createElement(element);
				e.type = type;
				e.value = value;
				e.id = id;
				return e;
			}
			function set_select(obj,value,dochange){
				if(obj==null) return;
				for(var j=0;j<obj.options.length;j++){
					if(obj.options[j].value == value){
						obj.options[j].selected = true;	
						if(dochange && obj.onchange)obj.onchange();
					}	
				}
			}
			function closediv(){
				//alert('['+pid.value+']');
				divin.nextSibling.style.display = 'none';
				divin.style.display="none";
				if(pid.value.length==0 || pid.value=='0'){ 
					plable.value = '';
					//plable.onfocus();
					plable.onblur();
				} // ?? �᲻�ᣬ����Ϊ��Ҳ���ύ��
			}
			var plable = $id(fmdata+'[pid_label]');
			plable.setAttribute('autocomplete','off');
			var divout = document.createElement('DIV');
			var pid = document.getElementsByName('fmdata[<?php echo $pidvar; ?>]')[0];
			with(divout.style){position = 	'relative';left = 0+'px';top = 0+'px';zIndex = 100;}
			var showdiv = "	<div style=\"border: 1px solid rgb(102, 102, 102); position: absolute; z-index: 1000; overflow-y: scroll; height: 300px; width: 500px; background-color: rgb(255, 255, 255);display:none;\" id=\"SuggestionDiv\"></div><iframe frameborder=\"0\" style=\"border: 0px solid rgb(102, 102, 102); position: absolute; z-index: 100; overflow-y: scroll; height: 300px; width: 500px; background-color: rgb(255, 255, 255);display:none;\"></iframe>";
			divout.innerHTML = showdiv;
			plable.parentNode.insertBefore(divout,plable.nextSibling);
			var divin = $id('SuggestionDiv');
			var aj=Ajax("HTML","loading");
			plable.onkeyup = function(){
				var keywords = plable.value;
				//$exurl������չ
				var urlbase = '<?php echo empty($exurl) ? 'ajax=arc_list' : "$exurl"; ?>';
				var urlpara = '&chid=<?php echo $pchid; ?>&keywords='+encodeURIComponent(keywords);
				var urlfull = CMS_ABS + uri2MVC(urlbase+urlpara);
				//console.log(urlfull); // &datatype=js|json 
				aj.post(urlfull,'',function(re){
					eval("var s = "+re+";"); 
					divin.style.display = '';
					divin.nextSibling.style.display = '';
					var str="<table width=\"480px\" cellspacing=\"0\" cellpadding=\"4\" border=\"0\" bgcolor=\"#ffffff\" class=\"search_select\" id=\"Suggestion\" style=\"top: -1px;\";><tbody><tr>";
					str += "<td height=\"16\" align=\"center\" style=\"color: rgb(153, 153, 153); padding-left: 3px; background-repeat: repeat-x; background-position: center center;\" >����ѡ��û�������� ���[�ر�]����������ؼ��ʣ�</td>";
					str += "<td align='right'><a style=\"cursor:pointer;text-decoration:none; color:red;\" onclick=\"closediv()\">�ر�</a></td></tr>"
					for(i=0;i<s.length;i++){
						str+="<tr onclick=\"sendaid("+i+")\" style=\"cursor:pointer; \"><td style=\"color:#09C;padding: 8px;\" >"+s[i].subject+"</td><td style=\"width:100px;padding: 8px;\" align='right'>ʱ�䣺"+s[i].create+"</td></tr>";	
					}
					if(s.length == 0){
						str += "<tr style=\"cursor:pointer\"><td index=\"1\" style=\"padding: 5px; color: rgb(51, 51, 51);\" ><span style=\"color: rgb(0, 101, 181);width:320px; display:block; float:left;\">�������Ϣ������������ؼ��ʣ�</span></td></tr>";	
					}
					str+="</tbody></table>";
					divin.innerHTML = str;
					
				function sendaid(i){
					pid.value = s[i].aid;
					plable.value = s[i].subject;
					divin.style.display="none";
					divin.nextSibling.style.display = 'none';
					plable.onfocus(); //û�����,���Ϊ��״̬��ѡһ����Ŀ,ѡȡ����֤��ʾ������ʧ
				}
				window.sendaid=sendaid;	
				});
			}
			</script>
			<?
			
		}elseif($p_album){
			$p_channel = cls_channel::Config($p_album['chid']);
			trhidden("{$this->fmdata}[$pidvar]",$p_album['aid']);
			$url = $mc ? cls_Mspace::IndexUrl($p_album) : cls_ArcMain::Url($p_album);
			trbasic('���� - '.$p_channel['cname'],'',"<a href=\"".$url."\" target=\"_blank\">".mhtmlspecialchars($p_album['subject'])."</a>",'');
		}
	}
	
	// cfg['hidden'] = 1 : ����[�߼�����]
	// $cfg['hidstr'] : �߼����õ���ʾ��Ϣ
	function fm_header($title = '',$url = '',$cfg = array()){
		if(!empty($cfg['hidden'])){ 
			global $setMoreFlag;
			$cfg['hidstr'] = empty($cfg['hidstr']) ? "�߼�����" : $cfg['hidstr'];
			$setMoreFlag = str_replace('.','',microtime(1));
			$title = "<span id='setMore_$setMoreFlag' style='display:inline-block;float:right;cursor:pointer' onclick='setMoerInfo(\"$setMoreFlag\",".$this->mc.")'> $cfg[hidstr] </span>$title";
		}
		$title || $title = $this->channel['cname'].'&nbsp; -&nbsp; ����';
		if($url){
			if($this->isadd){
				if(!in_str('chid=',$url)) $url .= "&chid={$this->chid}"; 
			}else{ //str_replace()������caid=��ͻ
				if(!in_str('aid=',str_replace('caid=','',$url))) $url .= "&aid={$this->aid}"; 
			}
			tabheader($title,'archivedetial',$url,2,1,1);
			echo "<input type='hidden' name='fmsend_reload_flag' value='".TIMESTAMP.'_'.cls_string::Random(6, 8)."' />";
		}else{
			tabheader($title);
		}
	}
	function fm_footer($button = '',$bvalue = '',$addstr=''){
		tabfooter($button,$button ? ($bvalue ? $bvalue : ($this->isadd ? '���' : '�ύ')) : '',$addstr);
		global $setMoreFlag; //��������[�߼�����]�ĳ�ʼ��js
		if(!empty($setMoreFlag)){
			echo '<script type="text/javascript">setMoerInfo("'.$setMoreFlag.'",'.$this->mc.')</script>';
			$setMoreFlag = '';	
		}
	}
	
	
	/**
	* ������Ŀ��ͨ���������飬��ָ���ر��չʾ������array('ids' => 5,'hidden' => 1)��
	*
	* @ex  $oA->fm_caid();
	*
	* @param    array    $cfg  ��ѡ���ò��� ��Ϊ���ֵ Ĭ��Ϊ��  idsΪָ����Ŀ��ID�� hiddenΪ�̶���Ŀ
	*					
	*/
	function fm_caid($cfg = array()){
		isset($this->predata['caid']) || $this->predata['caid']=0;
		//if(!empty($cfg['topid'])){
			//$cfg['ids'] = cls_catalog::son_ccids($cfg['topid'],0); 
		//}
		//���˵�hidden��ָ����ids��ָ��
		if(!array_key_exists('hidden',$cfg) && !array_key_exists('ids',$cfg)){ 
			 $pid = $this->find_topcaid(); //�ɵ�һ����˵���
			 $cfg['ids'] = cls_catalog::son_ccids($pid,0); 
		}
		$cfg = array_merge(
		array('value' => $this->predata['caid'],
		'chid' => $this->chid,
		'hidden' => empty($this->predata['caid']) || ($this->mc)? $this->isadd : !$this->isadd ? 0 : 1,
		'notblank' => 1,
		),
		$cfg);
		if($cfg['hidden']===1) echo "\n<input type=\"hidden\" name=\"{$this->fmdata}[caid]\" value=\"$cfg[value]\">\n";
		else tr_cns('������Ŀ',"{$this->fmdata}[caid]",$cfg);
	}
	
	
	/**
	* ���ҵ�ǰ��Ŀ�Ķ�����Ŀ
	*
	*/
	function find_topcaid(){
		$catalogs = cls_cache::Read('catalogs');
		$caid = $this->predata['caid'];
		$pid = 0;
		while($arr = array_intersect_key($catalogs,array($caid=>'0',))){
			$pid = @$arr[$caid]['pid'];
			if(!$pid) break;
			$caid = $pid;
		}
		return $caid;
	}
	
	/**
	*������࣬$coids��ϵid���飬��array(3,4,5)��Ϊ��������̨�������У���Ա���Ĳ������κ���ϵ
	* ��̨���Ϊ��,ǰ����ʾ������ϵ��������ʾ
	* @ex  $oA->fm_ccids(array());
	*
	* @param    array    $coids  ��ѡ��ϵID���� ��Ϊ���ֵ Ĭ��Ϊ��
	*					
	*/
	function fm_ccids($coids = array()){
		if($coids){
			foreach($coids as $coid){ 
				if(!in_array($coid,$this->coids_showed)){
					$this->fm_ccid($coid);
					$this->coids_showed[] = $coid;
				}
			}
		}elseif(!$this->mc){
			$cotypes = cls_cache::Read('cotypes');
			foreach($cotypes as $coid => $v){
				if(empty($v['self_reg']) && in_array($coid,$this->coids) && !in_array($coid,$this->coids_showed)){ 
					$this->fm_ccid($coid);
					$this->coids_showed[] = $coid;
				}
			}
		}
	}
	
	//����������
	//cfg���봫������ã��Դ������������
	function fm_ccid($coid = 0,$cfg = array()){
		$cotypes = cls_cache::Read('cotypes');
		if($coid && in_array($coid,$this->coids) && ($v = @$cotypes[$coid]) && empty($v['self_reg'])){
			$cfg = array_merge(
			array(
			'value' => empty($this->predata['ccid'.$coid]) ? 0 : $this->predata['ccid'.$coid],
			'coid' => $coid,
			'chid' => $this->chid,
			'max' => $v['asmode'],
			'notblank' => $v['notblank'],
			'hidden' => empty($this->predata['ccid'.$coid]) || !$this->isadd ? 0 : 1,
			'emode' => $v['emode'],
			'evarname' => "{$this->fmdata}[ccid{$coid}date]",
			'evalue' => @$this->predata["ccid{$coid}date"] ? date('Y-m-d',$this->predata["ccid{$coid}date"]) : '',
			'guide'=> $v['emode'] ? '��ֹ����Ϊ�����ʾ������Ч' : '',
			),
			$cfg);
			tr_cns($v['cname'],"{$this->fmdata}[ccid$coid]",$cfg);
		}
	}
	
	//չʾ�ĵ��ֶ�
	//$arrΪ�գ�չʾ������Ч�ֶΡ�$noinc=1��ָ�ų�$arr�е��ֶΣ�����Ϊָ��������
	function fm_fields($arr = array(),$noinc = 0){
		if(!$arr || $noinc){
			foreach($this->fields as $k => $v){
				if(!$arr || !in_array($k,$arr)) $this->fm_field($k);
			}
		}else{
			foreach($arr as $k) $this->fm_field($k);
		}
	}	
	//չʾ����ʣ���ֶ�,���ں��������ֶε��Զ�չʾ
	function fm_fields_other($nos = array()){
		foreach($this->fields as $k => $v){
			if(!in_array($k,$this->fields_did) && (!$nos || !in_array($k,$nos))) $this->fm_field($k);
		}
	}
	
	//�����ĵ��ֶ�չʾ
	//cfg���봫������ã��Դ������������
	function fm_field($ename,$cfg = array()){
		$this->fm_subject_unique();
		if(!empty($this->fields[$ename]) && $this->fields[$ename]['available'] && !in_array($ename,$this->fields_did)){
			$a_field = new cls_field;
			$cfg = array_merge($this->fields[$ename],$cfg);
			$a_field->init($cfg,isset($this->predata[$ename]) ? $this->predata[$ename] : '');
			$a_field->isadd = $this->isadd;
			$a_field->trfield($this->fmdata);
			$this->fields_did[] = $ename;
			unset($a_field);
		}
	}
	
	//���������жϵ��ĵ�����
	function fm_subject_unique(){
		global $subject_table;
		$subject_table || $subject_table = atbl($this->chid);
	}	
	
	//չʾ�������������̨Ĭ��Ϊarray('createdate','clicks','jumpurl','customurl','relate_ids')����Ա����Ĭ��Ϊarray('jumpurl','ucid')
	//��ѡ��Ŀarray('jumpurl','ucid','createdate','clicks','arctpls','customurl','dpmid','relate_ids',)
	function fm_params($incs = array(), $cfg=array()){
		if(empty($incs)) $incs = $this->mc ? array('ucid') : array('createdate','clicks','jumpurl','customurl','relate_ids',);
		foreach($incs as $k) $this->fm_param($k, $cfg);
	}
	
	//չʾָ�����������ѡ��Ŀarray('jumpurl','ucid','createdate','clicks','arctpls','customurl','dpmid','relate_ids',enddate)
	//cfg[addnums]����ʾ���ĵ�ģ��addnum��Ϊ����Ϊ����
	function fm_param($ename, $cfg=array()){
		global $timestamp;
		switch($ename){
			case 'enddate':
				trbasic('����ʱ��',"{$this->fmdata}[enddate]",date('Y-m-d',isset($this->predata['enddate']) ? $this->predata['enddate'] : $timestamp),'calendar');
			break;
			case 'jumpurl':
				trbasic('��תURL',"{$this->fmdata}[jumpurl]",isset($this->predata['jumpurl']) ? cls_url::view_url($this->predata['jumpurl'],false) : '','text',array('guide'=>'��������http://��ͷ������url��ָ����ת�����и��ĵ���url��Ϊ�õ�ַ��','w'=>50));
			break;
			case 'createdate':
				trbasic('���ʱ��',"{$this->fmdata}[createdate]",date('Y-m-d',isset($this->predata['createdate']) ? $this->predata['createdate'] : $timestamp),'calendar');//�޸����ʱ��
			break;
			case 'clicks':
				trbasic('�����',"{$this->fmdata}[clicks]",isset($this->predata['clicks']) ? $this->predata['clicks'] : 0,'text',array('guide'=>'�ĵ��ĵ�������������Ϊ0��ģ�����������','validate'=>' rule="int"'));
			break;
			case 'arctpls':
				$arctpls = explode(',',isset($this->predata['arctpls']) ? $this->predata['arctpls'] : '');				
				$guide = '����ģ��⣺[ģ����]��[ģ������]��['.cls_mtpl::mtplGuide('archive',true).']<br>ģ��󶨣�[ģ����]��[ģ���]��[<a href="?entry=tplconfig&action=tplchannel&isframe=1" target="_blank">�ĵ�����ҳ</a>]';
				trbasic('�ĵ�����ģ��',"{$this->fmdata}[arctpls][0]",makeoption(array('' => '������') + cls_mtpl::mtplsarr('archive',$this->chid),@$arctpls[0]),'select',array('guide'=>$guide));
				$arc_tpl = cls_tpl::arc_tpl(@$this->chid,@$predata['caid']);
				for($i = 1;$i <= @$arc_tpl['addnum'];$i ++){ 
					if(empty($cfg['addnums']) || in_array($i,$cfg['addnums'])){
						trbasic('����ҳ'.$i.'ģ��',"{$this->fmdata}[arctpls][$i]",makeoption(array('' => '������') + cls_mtpl::mtplsarr('archive',$this->chid),@$arctpls[$i]),'select');
					}else{
						trhidden("{$this->fmdata}[arctpls][$i]",@$arctpls[$i]);	
					}
				}
				unset($arctpls);
			break;
			case 'customurl':
				trbasic('�ĵ�ҳ��̬�����ʽ',"{$this->fmdata}[customurl]",isset($this->predata['customurl']) ? $this->predata['customurl'] : '','text',array('guide'=>'����ΪĬ�ϸ�ʽ��{$topdir}������ĿĿ¼��{$cadir}������ĿĿ¼��{$y}�� {$m}�� {$d}�� {$h}ʱ {$i}�� {$s}�� {$chid}ģ��id  {$aid}�ĵ�id {$page}��ҳҳ�� {$addno}����ҳid��id֮�佨���÷ָ���_��-���ӡ�','w'=>50));
			break;
			case 'relate_ids':
				aboutarchive(isset($this->predata['relatedaid']) ? $this->predata['relatedaid'] : '');
			break;
			case 'dpmid':
				trbasic('��������Ȩ������',"{$this->fmdata}[dpmid]",makeoption(array('-1' => '�̳���ĿȨ��') + pmidsarr('down'),isset($this->predata['dpmid']) ? $this->predata['dpmid'] : -1),'select');
			break;
			case 'ucid':
				if($this->mc){
					$curuser = cls_UserMain::CurUser();
					$nowUclasses = cls_Mspace::LoadUclasses($curuser->info['mid'],0);
					$ucidsarr = array();
					foreach($nowUclasses as $k => $v) if(!$v['cuid']) $ucidsarr[$k] = $v['title'];
					if($ucidsarr){
						trbasic('�ҵķ���',"{$this->fmdata}[ucid]",makeoption(array(0 => '�����÷���') + $ucidsarr,isset($this->predata['ucid']) ? $this->predata['ucid'] : 0),'select');
					}
				}
			break;
			case 'subjectstr':
				trhidden("{$this->fmdata}[subjectstr]",empty($this->predata['subject'])?'':$this->predata['subject']);
			break;
		}
			
	}
	
	
	/**
	*��ʾ��֤��
	*
	* @param    string    $type  ��֤������  Ĭ��Ϊarchive
	*						type��ֵ������/dynamic/syscache/cfregcodes.cac.php������
	*/
	//��ʾ��֤��
	function fm_regcode($type = 'archive'){
		if($type && $this->isadd && $this->mc){
			tr_regcode($type);
		}
	}
	
	//�����̨��$typeĬ��Ϊ0ʱ$strΪ���������ǣ�1��ʾ$strΪ�ı�����
	//��Ա���ģ�str���������Ա���İ�����ʶ��ֱ�ӵ��ı����ݣ�$typeĬ��Ϊ0ֱ����ʾ���ݣ�tip-�����ص���ʾ��fix-�̶�����ʾ��
	function fm_guide_bm($str = '',$type = 0){
		if($this->mc){
			m_guide($str,$type ? $type : '');
		}else{
			if(!$str){
				$str = $this->isadd ? 'archiveadd' : 'archivedetail';
				if(is_file(M_ROOT."dynamic/aguides/{$str}_{$this->chid}.php")) $str .= "_{$this->chid}";
				$type = 0;
			}
			a_guide($str,$type);
		}
	}
	
	//������֤��
	function sv_regcode($type = 'archive',$return_error = 0){
		global $regcode;
		if($type && $this->isadd && $this->mc){
			if(!regcode_pass($type,empty($regcode) ? '' : trim($regcode))) return $this->message('��֤�����',axaction(2,M_REFERER),$return_error);
		}
	}
	
	//������ǰ��Ա�ķ���Ȩ�޼�����Ȩ�ޣ���fm_pre_cns֮��ִ��
	function sv_allow($return_error = 0){
		$curuser = cls_UserMain::CurUser();
		if(!$this->mc && $re = $this->NoBackPm($this->predata['caid'])) $this->message($re,axaction(2,M_REFERER),$return_error);
		if($this->isadd && ($re = $curuser->arcadd_nopm($this->chid,$this->predata))) $this->message($re,axaction(2,M_REFERER),$return_error);
	}
		
	//���ʱ����ĿԤ�����쳣����
	//$incs:ָ��ֻ����ĳЩ��ϵ
	function sv_pre_cns($incs = array(),$return_error = 0){
		if(!$this->isadd) return;//�����ʱ��Ҫ
		$cotypes = cls_cache::Read('cotypes');
		foreach(array_merge(array(0),array_keys($cotypes)) as $k){
			if(!$incs || in_array($k,$incs)){
				if($re = $this->sv_pre_cn($k,array(),$return_error)) return $re;
			}
		}
	}
	//��������Ŀ
	//cfg���봫������ã��Դ������������
	function sv_pre_cn($coid = 0,$cfg = array(),$return_error = 0){
		if(!$this->isadd) return;//�����ʱ��Ҫ
		$fmdata = &$GLOBALS[$this->fmdata];
		if(!$coid){
			if(empty($fmdata['caid']) || !cls_cache::Read('catalog',$fmdata['caid'])) return $this->message('��ָ����ȷ����Ŀ',axaction(2,M_REFERER),$return_error);
			$this->predata['caid'] = $fmdata['caid'];
			if(!$this->mc && $re = $this->NoBackPm($this->predata['caid'])) $this->message($re,axaction(2,M_REFERER),$return_error);
		}elseif(in_array($coid,$this->coids) && isset($fmdata["ccid$coid"])){
			$cotypes = cls_cache::Read('cotypes');
			if(($v = $cotypes[$coid]) && !$v['self_reg']){
				$cfg && $v = array_merge($v,$cfg);
				$fmdata["ccid$coid"] = empty($fmdata["ccid$coid"]) ? '' : $fmdata["ccid$coid"];
				if($v['notblank'] && !$fmdata["ccid$coid"]) return $this->message("������ $v[cname] ����",axaction(2,M_REFERER),$return_error);
				if($fmdata["ccid$coid"]) $this->predata['ccid'.$coid] = $fmdata["ccid$coid"];
				if($v['emode']){
					$fmdata["ccid{$coid}date"] = !cls_string::isDate($fmdata["ccid{$coid}date"]) ? 0 : trim($fmdata["ccid{$coid}date"]);
					!$fmdata["ccid$coid"] && $fmdata["ccid{$coid}date"] = 0;
					if($fmdata["ccid$coid"] && !$fmdata["ccid{$coid}date"] && $v['emode'] == 2) return $this->message("������ $v[cname] ��������",axaction(2,M_REFERER),$return_error);
				}
			}
		}
	}
	
	//���һ���ĵ���¼	
	function sv_addarc(){
		global $m_cookie;
		if(!$this->isadd) return 0;//�����ʱ��Ҫ
		$fmpost = @$GLOBALS['fmsend_reload_flag']; //���ύ������,ÿ�β�ͬ������ͨ��ˢ��ҳ���ύ��,������һ�β�ͬ����ֵ����ʾ,�������ݿ⣻����$fmdata = &$GLOBALS[$this->fmdata];ȡֵ��
		$fmcook = empty($m_cookie['fmsend_reload_flag']) ? '-' : $m_cookie['fmsend_reload_flag']; 
		if($fmpost!=$fmcook){ 
			msetcookie('fmsend_reload_flag', $fmpost, 86400); //���棬�����ж��Ƿ�ͨ��ˢ��ҳ���ύ�ġ�
			empty($this->arc) && $this->arc = new cls_arcedit;
			$this->aid = $this->arc->arcadd($this->chid,$this->predata['caid']);
			return $this->aid;
		}else{ 
			return 0;	
		}
	}
	
	//����ĵ���¼֮�󣬸�������ʱ�����쳣�Ļ�����Ҫɾ����Ӻõ��ĵ���¼
	function sv_rollback(){
		if($this->aid && $this->isadd){
			global $db,$tblprefix;
			$c_upload = cls_upload::OneInstance();
			$db->query("DELETE FROM {$tblprefix}archives_sub WHERE aid='{$this->aid}'");
			$db->query("DELETE FROM {$tblprefix}".atbl($this->chid)." WHERE aid='{$this->aid}'");
			$db->query("DELETE FROM {$tblprefix}archives_{$this->chid} WHERE aid='{$this->aid}'");
			$c_upload->closure(1);
		}
	}
	
	//������ϵ�����ɴ�����Ҫ����ϵ
	function sv_cns($incs = array(),$return_error = 0){
		$cotypes = cls_cache::Read('cotypes');
		foreach(array_merge(array(0),array_keys($cotypes)) as $k){
			if(!$incs || in_array($k,$incs)){
				if($re = $this->sv_cn($k,array(),$return_error)) return $re;
			}
		}
	}
	
	//������ϵ����
	//cfg���봫������ã��Դ������������
	function sv_cn($coid = 0,$cfg = array(),$return_error = 0){
		$fmdata = &$GLOBALS[$this->fmdata];
		if(!$coid){
			if(isset($fmdata['caid'])) $this->arc->arc_caid($fmdata['caid']);
		}else{
			$cotypes = cls_cache::Read('cotypes');
			if(isset($fmdata["ccid$coid"]) && in_array($coid,$this->coids)){
				if(($v = @$cotypes[$coid]) && !$v['self_reg']){
					$cfg && $v = array_merge($v,$cfg);
					if($v['notblank'] && !$fmdata["ccid$coid"]) return $this->message("������ $v[cname] ����",M_REFERER,$return_error);
					$this->arc->arc_ccid($fmdata["ccid$coid"],$coid,$v['emode'] ? @$fmdata["ccid{$coid}date"] : 0);
				}
			}
		}
	}
	
	function sv_fields($nos = array(),$return_error = 0){//$nos�����ų��ֶ�
		foreach($this->fields as $k => $v){
			if(!$nos || !in_array($k,$nos)){
				if($re = $this->sv_field($k,array(),$return_error)) return $re;
			}
		}
	}
	
	//�����ֶδ�������ָ���ֶ�ĳ�����ò���
	function sv_field($ename,$cfg = array(),$return_error = 0){
		global $sptype,$spsize;
		$fmdata = &$GLOBALS[$this->fmdata];
		if(isset($fmdata[$ename]) && $v = @$this->fields[$ename]){
			$c_upload = cls_upload::OneInstance();
			$cfg && $v = array_merge($v,$cfg);
			if($v['datatype'] == 'htmltext' && $sptype == 'auto'){
				$spsize = empty($spsize) ? 5*1024 : $spsize*1024;
				$fmdata[$ename] = SpBody($fmdata[$ename],$spsize,'[##]');
			}
			
			$a_field = new cls_field;
			$a_field->init($v,isset($this->predata[$ename]) ? $this->predata[$ename] : '');
			$fmdata[$ename] = $a_field->deal($this->fmdata,''); //����Ҫֱ�ӷ��ش�����Ϣ,������ִ��sv_rollback()
			if($a_field->error){//��׽������Ϣ
				$this->sv_rollback();
				return $this->message($a_field->error,axaction(2,M_REFERER),$return_error);
			}
			unset($a_field);
			
			if($ename == 'keywords') $fmdata[$ename] = cls_string::keywords($fmdata[$ename],@$this->predata[$ename]);
			$this->arc->updatefield($ename,$fmdata[$ename],$v['tbl']);
			if($arr = multi_val_arr($fmdata[$ename],$v)) foreach($arr as $x => $y) $this->arc->updatefield($ename.'_'.$x,$y,$v['tbl']);
		}
	}
	
	//����������������̨Ĭ��Ϊarray('createdate','clicks','jumpurl','customurl','relate_ids')����Ա����Ĭ��Ϊarray('jumpurl','ucid')
	function sv_params($incs = array()){
		if(empty($incs)) $incs = $this->mc ? array('ucid') : array('createdate','clicks','jumpurl','customurl','relate_ids',);
		foreach($incs as $k) $this->sv_param($k);
	}
	
	//����ָ�����������ѡ��Ŀarray('jumpurl','ucid','createdate','clicks','arctpls','customurl','dpmid','relate_ids',)
	function sv_param($ename){
		global $timestamp;
		$fmdata = &$GLOBALS[$this->fmdata];
		if($ename == 'relate_ids' && !empty($GLOBALS['relatedaid'])) $this->arc->autorelated();
		if($ename && isset($fmdata[$ename])){
			if($ename == 'createdate'){//���ʱ��
				$fix = $this->isadd ? $timestamp : @$this->arc->archive['createdate'];
				$fix = $fix - strtotime(date('Y-m-d',$fix));
				$this->arc->updatefield($ename,empty($fmdata[$ename]) ? $timestamp : strtotime($fmdata[$ename])+$fix);
			}elseif($ename == 'enddate'){//����ʱ��
				$this->arc->updatefield($ename,empty($fmdata[$ename]) ? 0 : strtotime($fmdata[$ename]));
			}elseif($ename == 'arctpls'){//�Զ�ģ��
				$this->arc->updatefield($ename,implode(',',$fmdata[$ename]));
			}elseif($ename == 'customurl'){//�Զ���̬url
				$this->predata['nokeep'] = $this->arc->updatefield($ename,trim($fmdata[$ename]));
			}elseif($ename == 'jumpurl'){//��תurl
				$this->arc->updatefield($ename,cls_url::save_url(trim($fmdata[$ename])));
			}elseif($ename == 'subjectstr'){//���������ĸ����Լ�ȫƴ
				if(strcmp($fmdata['subject'],$fmdata['subjectstr']) != 0){
					$fmdata['subject'] = str_replace('\\','',$fmdata['subject']);
					$this->arc->updatefield($ename,cls_string::Pinyin($fmdata['subject'],1));
				}				
			}else{//���˷���ucid�������clicks������Ȩ��dmpid
				$this->arc->updatefield($ename,max(0,intval($fmdata[$ename])));
			}
		}
	}
	
	//�ĵ���¼δ��ӳɹ��Ĵ���
	function sv_fail($return_error = 0){
		$c_upload = cls_upload::OneInstance();
		$c_upload->closure(1);
		return $this->message('�ĵ����ʧ��',axaction(2,M_REFERER),$return_error);
	}
	
	//ִ���Զ��������������ϱ��
	function sv_update(){
		$this->isadd && $this->arc->autocheck();
		$this->isadd && $this->arc->autoclick(); //Ĭ�ϵ����
		$this->arc->auto();
		$this->arc->updatedb();
		if($this->isadd){ 
			$this->arc->autopush(); //�Զ�����
		}
	}
	
	//�ĵ���ӻ��޸ĳɹ�����ϴ�����
    //furl:������ַ; ��һ����ͼ�ֶ�ת��Ϊ��ͼ�ֶ�,�ϴ�ʱ,���뱣��ɶ���ĵ�,��Ҫ��furl����
	function sv_upload($furl=''){
		$c_upload = cls_upload::OneInstance();
        $paras = $furl ? array('aid'=>$this->aid,'url'=>$furl) : $this->aid;
		$c_upload->closure(1,$paras);
		$c_upload->saveuptotal(1);
	}
	
	//Ҫָ���ϼ�id������$pidkey��ϼ�pid(����)���ϼ���Ŀ$arid
	function sv_album($pidkey = 'pid',$arid = 0){
		if($pidkey && $arid = (int)$arid){
			if(is_numeric($pidkey)){
				$pidval = $pidkey;
			}else{
				$fmdata = &$GLOBALS[$this->fmdata];
				$pidval = intval(@$fmdata[$pidkey]);	
			}
			if(!empty($pidval)) $this->arc->set_album($pidval,$arid);
		}
	}
	
	//���ִ���Զ���̬
	function sv_static(){
		//$this->arc->autostatic(empty($this->predata['nokeep']) ? 1 : 0);
		//sv_album()�Ȳ���,��û�и���$this->arc������; ��������newһ��cls_arcedit�����¾�̬
		$arc = new cls_arcedit;
		$arc->set_aid($this->aid,array('chid'=>$this->chid));
		$arc->autostatic(empty($this->predata['nokeep']) ? 1 : 0);
	}
		
	/*����ʱ��Ҫ������ �磺������¼���ɹ���ʾ
	 *@param $arr_direct ��ת�������� �����cls_message::show�����Ĳ�������
	 *@param $msg        ��ʾ����Ϣ�ַ�����Ĭ����ӳɹ�
	 *
	 */
	function sv_finish($arr_direct=array(),$msg=NULL){
		$modestr = $this->isadd ? '���' : '�޸�';
		$this->mc || adminlog($modestr.'�ĵ�');
		if($this->isadd && $arr_direct) {
			$msg = empty($msg) ? '��ӳɹ�' : $msg;
			cls_message::show('<br/>'.$msg,$arr_direct);
		}
		$this->message('�ĵ�'.$modestr.'���',axaction(6,M_REFERER));
	}
	
	//�Ѷ�ͼת��Ϊ�ĵ�
	//cfgs:pid/pfield,chid,caid,arid
	//cfg['props'] = array(1=>'subject',2=>'lx'); ��ͼƬ���Դ�Ϊĳ���ֶ�����
	static function sv_images2arcs($fmdata=array(),$field='thumb',$cfgs=array(),$key=''){
		cls_env::SetG('fmdata',$fmdata);
		$key || $key = $field;
		$oA = new cls_archive($cfgs); 
		$oA->isadd = 1;
		$oA->read_data();
		$oA->setvar('coids',array(0));
		$fields = &$oA->fields;
		$oA->sv_pre_cns(array());
		$msg = array(); $firstimg = '';
		$_a = explode("\n",str_replace(array("\r","\r\r"),array("\n","\n"),$fmdata[$key])); 
		foreach($_a as $val){
		if(!empty($val)){
			$fmdata[$field] = $val; 
			if(strpos($fmdata[$field],'|')>0){
				$_pica = explode('|',$fmdata[$field]);
				$fmdata[$field] = $_pica[0]; 
			}
			$fmdata[$field] = str_replace(array('##'," ","\t","\r","\n"),'',$fmdata[$field]);
			cls_env::SetG("fmdata.$field",$fmdata[$field]);
			$oA->arc = new cls_arcedit;
			$oA->aid = $oA->arc->arcadd($oA->chid,$oA->predata['caid']);
			if(!$oA->aid){
				$msg[] = $oA->sv_fail(1); 
			}else{
				$msg[] = $oA->sv_cns(array(),1); 
				$msg[] = $oA->sv_fields(array(),1); 
				//��img����ת��Ϊ�ĵ��ֶ�����
				if(!empty($cfgs['props'])){ 
					foreach($cfgs['props'] as $pk=>$pf){ //echo '<br>2,';
						if(!empty($_pica[$pk])){ 
							$oA->arc->updatefield($pf,$_pica[$pk]); 
						}
					}
				}
				if(empty($firstimg)) $firstimg = $oA->arc->archive[$field]; //��¼��һ��ͼ������ͼ
				$oA->sv_update(); 
				$oA->sv_upload($fmdata[$field]); 
				$pidkey = isset($cfgs['pid']) ? $cfgs['pid'] : $cfgs['pfield'];
				if($pidkey && $cfgs['arid']) $oA->sv_album($pidkey,$cfgs['arid']); 
				$oA->sv_static();
			} 
			unset($oA->arc); // ??? 
		}  } //print_r($msg); die('xxx');
		return array($msg,$firstimg);
	}
}
?>
