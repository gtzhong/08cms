<?php
/*
** ����������ӻ�༭�Ĳ�����, �����Ҫ��ǰ̨etools�£�Ҫ���Ǽ���
** sv����������return_error��ʾ����ʱ����error����������message����
*/
!defined('M_COM') && exit('No Permisson');
class cls_cueditbase extends cls_cubasic{

	public $isadd = 0;//���ģʽ
	public $fmpre = 'fmdata';//form�е���������//������������
	public $predata = array();//Ԥ����������
	public $enddata = array();//��������������

	public $cfgs = array();//����������
	public $items_did = array();//�ݴ��Ѵ��������Ŀ
	public $fmdata = array();//���ύ�����������
	public $fulldata = array();//��������,����cid,checked��
	
	public $pinfo = array();//��������info
	public $fnoedit = array(); //������༭�ֶ�, ��'fnoedit' => array('jiaxiaokoubei','kaoshihegelv'),  //��У�ڱ�,���Ժϸ���
	
    function __construct($cfg = array()){
		parent::__construct($cfg);
		$this->cid = empty($cfg['cid']) ? 0 : max(0,intval($cfg['cid']));
		if($this->cid){
			$this->isadd = 0;
		}else{
			$this->isadd = 1;
		} 
		if(!empty($cfg['fmpre'])) $this->fmpre = $cfg['fmpre'];
		if(isset($cfg['fnoedit'])){	
			$this->fnoedit = $cfg['fnoedit'];
		}
    }
	
	// ��ӽ�������ʼ����飬��ǰ̨/etools��ʹ��,������top_head()
	// pchid: ��֤chids, 0-����֤,1��֤; pchid���þ���ָ��ĳ��pid���ĵ�/��Աģ��
	// setCols: $this->additems();
    function add_init($pid=0,$pnullmsg='',$cfg=array()){
		global $inajax, $infloat, $handlekey, $in_mobile; 
		//�����ֻ����ύ:in_mobile=1, ��ʾ��Ϣʹ���ֻ�����ʽ
		if(!empty($in_mobile)) define('IN_MOBILE', TRUE);
		$curuser = cls_UserMain::CurUser();
		empty($pnullmsg) && @$pnullmsg = "��ָ��[{$this->cucfgs['cname']}]����"; 
		empty($inajax) || $this->A['url'] .= "&inajax=$inajax";
		
		$burl = "?cuid=$this->cuid";
		$this->burl = $burl;
		$this->A['url'] = $this->burl.(empty($this->A['url']) ? '' : $this->A['url']);
		foreach(array('infloat','inajax','js') as $k){
			global $$k; $v = $$k;
			if($v && !strstr('$v=',$this->A['url'])) $this->A['url'] .= "&$k=$v";
		}
		
		$this->cucfgs || $this->message('�����ڵĽ�����Ŀ��');  
		if(isset($this->cucfgs['pmid']) && !$curuser->pmbypmid($this->cucfgs['pmid'])) $this->message('��û�д˽����Ĳ���Ȩ�ޡ�'); 
		if(empty($this->cucfgs['available'])) $this->message('�ù����ѹرա�');
		if(!empty($this->A['pchid']) && $pid){
			$pinfo = $this->pinfo = $this->getPInfo($this->A['ptype'],$pid); 
			$pinfo || $this->message($pnullmsg); 
			$pchid = @$pinfo[($this->A['ptype']=='m'?'m':'').'chid'];
			if(empty($this->cucfgs['chids']) || !in_array($pchid,$this->cucfgs['chids'])){
				$this->message($pnullmsg);	
			}
		}elseif(!empty($this->A['pchid']) && empty($cfg['pidskip'])){
			$this->message($pnullmsg);
		}
		// ����cfg����
		//if(!empty($cfg['chkData'])){
			//if(empty($this->predata)) $this->message('���������ݣ�'); 
		//}
		if(!empty($cfg['setCols'])){
			$this->additems();
		}
	}
	// ��ӽ���������ͨ��ͷ��html
    function add_header(){
		global $inajax, $infloat, $handlekey;
		$cms_abs = cls_env::mconfig('cms_abs');
		$mc_dir = cls_env::mconfig('mc_dir');
		include_once M_ROOT."./include/adminm.fun.php"; // Ҫ�� _header(),_footer()����
		if(empty($inajax)){
			_header(); 
		}
	}
	// ��ӽ���������ͨ��ҳ�����html
    function add_footer(){
		global $inajax, $infloat, $handlekey;
		if(empty($inajax)){
			_footer();
		}
	}
	// ��ӽ�������ʾ���������һ��������
    function add_pinfo($cfg=array()){
		if(empty($this->pinfo) && !empty($cfg['pid'])){ 
			$this->pinfo = $this->getPInfo($this->A['ptype'],$cfg['pid']); 
		}
		$title = empty($cfg['title']) ? ($this->ptype=='m' ? '������Ա' : '�����ĵ�') : $cfg['title'];
		$link = $this->getPLink($this->pinfo, $cfg);
		echo trbasic($title,'',$link,'');
	}
	
	
	function setvar($key,$var){
		$this->$key = $var;	
	}

	//���������Ŀ
	function del_item($key){
		unset($this->cfgs[$key]);
		return false;
	}
	
	//�Ƿ�һ���Ѵ��ڵ���Ŀ
	function is_item($key = ''){
		return isset($this->cfgs[$key]) ? true : false;
	}
	
	// ����һ���ֶΣ����ڻظ��� $oA->addcopy('content', 'reply','','0');
	function addcopy($from='content', $to='reply', $title='', $notnull='def'){
		$this->fields[$to] = $this->fields[$from];
		$this->fields[$to]['ename'] = $to; 
		$this->fields[$to]['cname'] = $title ? $title : '�ظ�'; 
		if(!($notnull==='def')) $this->fields[$to]['notnull'] = $notnull; 
		$this->additem($to,array('_type' => 'field'));
	}
	
	// (���/�༭)��Ӽܹ��ֶ�
	// $copy:���Ƶ�һ���ֶΣ����ڻظ��� $oA->fm_additems(array('content'=>'reply'));
	function additems($copy = array()){
		foreach($this->fields as $k => $v){//��̨�ܹ��ֶ�
			$this->additem($k,array('_type' => 'field'));
			if(isset($copy[$k])){
				$this->addcopy($k, $copy[$k]);
			}
		}
	}
	
	//���������Ŀ��������Ŀ��ʼ��
	function additem($key,$cfg = array()){
		$this->cfgs[$key] = $cfg;
		$re = $this->one_item($key,'init');
		if($re == 'undefined') $this->del_item($key);
	}
	
	function call_method($func,$args = array()){
		if(method_exists($this,$func)){
			return call_user_func_array(array(&$this,$func),$args);
		}else return 'undefined';
	}
	
	//�������ȴ���user_$key(����) -> type_$type(����) -> ͨ�÷���
	//���������Ҫ����init����ʼ�� fm����ʾ sv�����ݴ���
	function one_item($key,$mode = 'init'){
		if(!isset($this->cfgs[$key])) return false;
		$re = $this->call_method("user_$key",array($key,$mode));//���Ʒ���
		if($re == 'undefined'){
			switch($this->cfgs[$key]['_type']){
				case 'field':
					$re = $this->type_field($key,$mode);
				break;
				//case 'ugid':
					//$re = $this->type_ugid($key,$mode);
				//break;
			}
		}
		if(in_array($mode,array('fm','sv',))) $this->items_did[] = $key;//��¼�Ѵ������Ŀ
		return $re;
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
		
		$title || $title = $this->cucfgs['cname'].'&nbsp; -&nbsp; '.($this->isadd ? '��ӽ���' : '��������');
		
		$url = $this->burl.(empty($url) ? '' : $url);
		if(!in_str('cid=',$url)) $url .= "&cid={$this->cid}"; 
		if($url){
			//if($this->isadd){ //��̨��,��ӡ����ĵ�/��Ա������,��δ���
			//}else{
			//}
			tabheader($title,'cudetial',$url,2,1,1);
		}else{
			tabheader($title);
		}
	}
	
	// չʾָ���$incsΪ�ձ�ʾΪ����ʣ����
	// $noinc=array()����$incs�������ų�$noinc�е��ֶΣ�Ϊ�����ų���
	// $cfg[noaddinfo] : �Ƿ���ʾ�������Ϣ, 1:����ʾ, 0:��ʾ(Ĭ��)
	function fm_items($incs='', $noinc=array(), $cfg=array()){
		if(!empty($incs)) $incs = array_filter(explode(',',$incs));
		if(empty($incs)) $incs = array_keys($this->cfgs);//չʾʣ����
		foreach($incs as $key){
			if(!empty($noinc) && in_array($key,$noinc)) continue;
			if(!in_array($key,$this->items_did)){
				$this->one_item($key,'fm');
			}
		}
		if(empty($this->isadd) && empty($cfg['noaddinfo'])){ //�޸�״̬��, ��ʾ�����������Ϣ
			$data = $this->predata['mname'].' (ID:'.$this->predata['mid'].')';
			trbasic('������','',$data,'');	
			$date = $this->predata['createdate'];
			trbasic('��������','',($date ? date('Y-m-d H:i:s',$date) : '-'),'');
			if(isset($this->predata['ip'])){ // ??? 
				$ip = $this->predata['ip'];
				trbasic('������IP','',$ip,'');	
			}
		}
	}
	
	function fm_footer($button = '',$bvalue = ''){
		tabfooter($button,$button ? ($bvalue ? $bvalue : ($this->isadd ? '���' : '�ύ')) : '');
		global $setMoreFlag; //��������[�߼�����]�ĳ�ʼ��js
		if(!empty($setMoreFlag)){
			echo '<script type="text/javascript">setMoerInfo("'.$setMoreFlag.'",'.$this->mc.')</script>';
			$setMoreFlag = '';	
		}
	}
	
	/**
	*��ʾ��֤��
	* @param    string    $type  ��֤������  Ĭ��Ϊarchive
	*					  type��ֵ������/dynamic/syscache/cfregcodes.cac.php������
	*/
	//��ʾ��֤��
	function fm_regcode($type='commu',$params = array()){
		if($type && $this->isadd && $this->mc){
			tr_regcode($type,$params);
		}
	}
	
	//������֤��
	function sv_regcode($type='commu', $return_error = 0){
		global $regcode;
		if($type && $this->isadd && $this->mc){
			if(!regcode_pass($type,empty($regcode) ? '' : trim($regcode))) return $this->message('��֤�����',axaction(2,M_REFERER),$return_error);
		}
	}
	
	//(���༭ʹ��)�༭�ύ��,ͨ�õ��ύ��������ͨ�����Ʒ���������չ
	//�����������ʼ�����ý����ۺϴ���
	//cfg[message]:��ʾ��Ϣ
	function sv_all_common($cfg = array()){
		
		$this->sv_set_fmdata();//����$this->fmdata�е�ֵ
		$this->sv_items();//�������ݵ����飬��ʱδִ�����ݿ����
		$this->sv_update();//ִ���Զ��������������ϱ��
		$this->sv_upload();//�ϴ�����
		$this->sv_finish($cfg);//����ʱ��Ҫ�����񣬰���������¼���ɹ���ʾ��
		
	}
	
	//����ָ�������չʾ����ʣ����
	function sv_items($incs = ''){
		if(!empty($incs)) $incs = array_filter(explode(',',$incs));
		if(empty($incs)) $incs = array_keys($this->cfgs);//����ʣ����
		foreach($incs as $key){
			if(!in_array($key,$this->items_did)){
				$this->one_item($key,'sv');
			}
		}
	}
	
	// ���»ظ�ʱ��
	//$oA->sv_retime('replydate','reply'); �лظ����ݲŸ���
	function sv_retime($key,$rek=''){
		if(!empty($this->enddata[$rek])){ 
			$this->enddata[$key] = TIMESTAMP;	
		}
	}
	
	// ͨ�ñ���һ���ܹ�����ֶ�(һ����ָ����ĳ���ֶ�)
	// $oA->sv_excom('checked',!empty($reply)); 
	// iskey : ��fmdata��ȡkey��Ӧ��ֵ
	function sv_excom($key,$val=0,$iskey=0){
		$val = $iskey ? $this->fmdata[$val] : $val; //�������δ���,�õ��ԡ�
		$this->enddata[$key] = $val;
	}
	
	function sv_set_fmdata(){
		$this->fmdata = &$GLOBALS[$this->fmpre];//��Ϊ�ֶδ�����δ��һ���Ż���������Ҫ������
	}	
	
	//��ӽ���֮�󣬸�������ʱ�����쳣�Ļ�����Ҫɾ�������ӵĽ�����¼
	//mname,password,email֮����Ŀ��������󣬶���Ҫʹ�ô˷���
	function sv_rollback(){
		if($this->cid && $this->isadd){
			$c_upload = cls_upload::OneInstance();
			$this->delete($this->cid);
			$c_upload->closure(1);
		}
	}
	
	function sv_fail($return_error = 0){
		$c_upload = cls_upload::OneInstance();
		$c_upload->closure(1);
		return $this->message('�������ʧ��',M_REFERER);
	}
	
	// ִ�в������ݣ��ڶ�����, ģ����������, arr��������(��aid=>$aid,tocid=>$tocid)
	// ִ�мӷ�
	function sv_insert($arr=array()){
		global $timestamp;
		$curuser = cls_UserMain::CurUser();
		$data = &$this->enddata;
		if(empty($curuser->info['mid'])){
			$_dm['mid'] = 0;
			$_dm['mname'] = '�ο�';
		}else{
			$_dm['mid'] = $curuser->info['mid'];
			$_dm['mname'] = $curuser->info['mname'];
		}
		if(isset($arr['checked'])){ //��������
			$_dm['checked'] = $arr['checked'];
		}elseif(!isset($this->cucfgs['autocheck'])){ // ������,Ĭ��Ϊ1
			$_dm['checked'] = 1;	
		}else{
			if($ischeck = $curuser->pmautocheck(@$this->cucfgs['autocheck'])) $_dm['checked'] = 1;
			else $_dm['checked'] = 0;
		}
		$_dm['createdate'] = $timestamp;
		if(!empty($arr)){ //��⸽�ӵ��ֶ��Ƿ����
			$dbfields = empty($arr) ? array() : $this->getFields();
			foreach($arr as $k=>$v){
				if(!in_array($k,$dbfields)) unset($arr[$k]);
			}
		}
		$this->fulldata = $data = array_merge($_dm, $data, $arr); // ������������
		$flist = ''; $fdata = array();
		foreach($data as $k=>$v){
			$flist .= (empty($flist) ? '' : ' ,')."$k ";
			$fdata[] = $v;
		}
		$this->db->insert($this->table(), $flist,array($fdata))->exec();
		$this->cid = $this->db->insert_id();
		if(!$this->cid){
			$this->message('��Ӵ���');		
		}else{
			empty($_dm['mid']) || $this->setCrids('add', $_dm['mid']);	
			cls_commu::autopush($this->cuid, $this->cid); //�Զ�����
		}
        
        return $this->cid;
	}
	
	//ִ���Զ��������������ϱ��
	//update֮ǰ,�ɸ�����Ҫ��չ:�磺$this->enddata += array('myupdate'=>TIMESTAMP);
	function sv_update(){
		$this->db->update($this->table(), $this->enddata)->where('cid='.$this->cid)->exec();
	}
	
	//�ϴ�����
	function sv_upload(){
		$c_upload = cls_upload::OneInstance();
		$c_upload->closure(1,$this->cid,'comments'); //�����ĸ���,��ô�棿"commu$this->cuid"
		$c_upload->saveuptotal(1);
	}
	
	// �ظ��������������
	// act=check,save,both
	function sv_repeat($arr=array(), $act='check'){
		global $m_cookie;
		$curuser = cls_UserMain::CurUser(); $mid = $curuser->info['mid'];
		$key = "08cms_cuid_{$this->cuid}_{$mid}_";
		empty($arr) || $key .= implode('_',$arr);
		if(!empty($this->cucfgs['repeattime'])){
			if(in_array($act,array('check','both'))){ //���
				empty($m_cookie[$key]) || $this->message('�����벻Ҫ����Ƶ����',axaction(2,M_REFERER));
				if($act=='both') $this->sv_repeat($arr, 'save');	
			}
			if(in_array($act,array('save','both'))){ //����
				msetcookie($key,1,$this->cucfgs['repeattime'] * 60);
			}
		}
	}
		
	# ����һ������(������)ͶƱ(��1,��2)
	function sv_Vote($cid, $fix='opt', $no='1', $nos='1,2', $add=1){
		global $m_cookie;
		$tblprefix = cls_env::getBaseIncConfigs('tblprefix');
		$cid = intval($cid);
		$field = strstr(",$nos,",",$no,") ? "$fix$no" : '';
		$key = "08cms_1Vote_{$this->cuid}_{$cid}_$field";
		// 3״̬�� �ɹ�, ��ͶƱ, ����
		if($field && empty($m_cookie[$key])){ //��Ч
			msetcookie($key,1,365 * 86400);
			$re = $this->db->query("UPDATE {$tblprefix}".$this->cucfgs['tbl']." SET $field=$field+$add WHERE cid='$cid'");
			return ($re) ? 'OK' : 'Error'; //�ɹ�/ʧ��
		}elseif($field){ //��ͶƱ
			return 'Repeat';
		}else{ //����
			return 'Error';
		}	
	}
	
	# �����ĵ�����ͶƱ(����/ǹ��/����/��) [�翴�����ź�����������]
	// $pfield���ݿ�ȷ�ϴ��ڵ��ֶ�
	function sv_Mood($pfield='aid', $fix='opt', $no='1', $nos='1,2', $add=1){
		global $m_cookie, $timestamp;
		$tblprefix = cls_env::getBaseIncConfigs('tblprefix');
		$curuser = cls_UserMain::CurUser();
		$pfield = preg_replace('/[^\w]/', '', $pfield);
		if(empty($no) && empty($nos)){
			$field = $fix; 
		}else{
			$field = strstr(",$nos,",",$no,") ? "$fix$no" : '';
		} //echo "($field)"; 
		$pid = $this->pinfo['_pid'];
		$key = "08cms_1Mood_{$this->cuid}_{$pid}_0"; //$field(һ��pidֻͶһ��)
		// 3״̬�� �ɹ�, ��ͶƱ, ����
		if($field && empty($m_cookie[$key])){ //��Ч
			msetcookie($key,1,365 * 86400);
			if(!($row = $this->db->fetch_one("SELECT cid FROM {$tblprefix}".$this->cucfgs['tbl']." WHERE $pfield='$pid'"))){
				$acheck = empty($this->cucfgs['autocheck']) ? '' : ",checked=1";
				$sql = "INSERT INTO {$tblprefix}".$this->cucfgs['tbl']." SET $pfield='$pid',$field='1',createdate='$timestamp'$acheck";
			}else{
				$sql = "UPDATE {$tblprefix}".$this->cucfgs['tbl']." SET $field=$field+1 WHERE $pfield='$pid'";
			}
			$re = $this->db->query($sql);
			return ($re) ? 'OK' : 'Error'; //�ɹ�/ʧ��
		}elseif($field){ //��ͶƱ
			return 'Repeat';
		}else{ //����
			return 'Error';
		}	
	}
	
	# �����ĵ�/��Ա���ղ�
	// $pfield���ݿ�ȷ�ϴ��ڵ��ֶ�
	function sv_Favor($pfield='aid'){
		global $m_cookie, $timestamp, $onlineip;
		$tblprefix = cls_env::getBaseIncConfigs('tblprefix');
		$curuser = cls_UserMain::CurUser();
		$memberid = $curuser->info['mid'];
		$pfield = preg_replace('/[^\w]/', '', $pfield);
		$pid = @$this->pinfo['_pid'];
		$key = "08cms_Favor_{$this->cuid}_{$memberid}_{$pid}"; //��{$memberid}Ϊ�����л��û������ղ�;
		if(!$memberid) return 'noLogin'; //$oA->message('���ȵ�¼��Ա��'); //; 
		// 3״̬�� �ɹ�, ���ղ�, ����
		if(empty($m_cookie[$key])){ //��Ч
			msetcookie($key,1,365 * 86400);
			if(!($row = $this->db->fetch_one("SELECT cid FROM {$tblprefix}".$this->cucfgs['tbl']." WHERE mid='$memberid' AND $pfield='$pid'"))){
				$acheck = empty($this->cucfgs['autocheck']) ? '' : ",checked=1";
				$sql = "INSERT INTO {$tblprefix}".$this->cucfgs['tbl']." SET $pfield='$pid',mid='$memberid',mname='{$curuser->info['mname']}',createdate='$timestamp'$acheck "; 
				$re = $this->db->query($sql); //chid='{$arc->archive['chid']}',
				return ($re) ? 'OK' : 'Error'; //�ɹ�/ʧ��
			}else{
				return 'Repeat';
			}
			$re = $this->db->query($sql);
			return ($re) ? 'OK' : 'Error'; //�ɹ�/ʧ��
		}else{ //����
			return 'Repeat';
		}	
	}

	//����ʱ��Ҫ������ �磺������¼���ɹ���ʾ
	function sv_finish($cfg = array()){
		if(empty($cfg['message'])) $cfg['message'] = '����'.($this->isadd ? '���' : '�޸�').'���';
		if(empty($cfg['record'])) $cfg['record'] = ($this->isadd ? '���' : '�޸�').'����';
		$this->mc || adminlog($cfg['record']);
		//$cfg['jumptype']  ��Ϣ��ʾ֮��Ĵ���ʽ������رյ�ǰ���ڡ���ת�����ҳ��
		$this->message($cfg['message'],empty($cfg['jumptype'])?axaction(6,M_REFERER):$cfg['jumptype']);
	}

	//ajax�ύ:����ʱ��Ҫ������
	function sv_ajend($exmsg,$expars=array()){
		$fmdata = $this->fulldata;
		$fmdata['cid'] = $this->cid;
		$reinfo = array('error'=>'', 'message'=>'�ύ�ɹ���', 'result'=>'succeed', 'cu_data'=>$fmdata);
		$exmsg && $reinfo['exmsg'] = $exmsg; //$this->message('Error-T1(message������Ϣ)');
        if(!empty($expars['aj_minfo'])){ //ͬʱ���ػ�Ա����
			$user = new cls_userinfo;
			$user->activeuser($fmdata['mid']); //,$detail
			$reinfo['aj_minfo'] = $user->info;
        }
        if(!empty($expars['aj_ainfo']) && isset($fmdata['aid'])){ //ͬʱ�����ĵ�����
			$arc = new cls_arcedit;
			$arc->set_aid($fmdata['aid'],array('au'=>0)); //,'ch'=>$detail
			cls_ArcMain::Parse($arc->archive);
            $reinfo['aj_ainfo'] = $arc->archive;
        }
		return $reinfo; //$this->rejson($fmdata); //����josn
	}

	//���������ֶ�
	//cfg���봫������ã��Դ������������
	function type_field($key,$mode = 'init'){
		global $mctypes;
		$cfg = &$this->cfgs[$key];
		//if(empty($this->fields[$key]) || $this->IsSysField($key)) return $this->del_item($key);

		switch($mode){
			case 'init'://��ʼ��
			break;
			case 'fm'://����ʾ
				if($this->fhidden && in_array($key,$this->fhidden)){ //Ҫ�����ش�����ֶΣ����⴦��
					trbasic("<font color='blue'>{$this->fields[$key]['cname']}</font>",'',cls_string::SubReplace($this->predata[$key]),'');
				}elseif(in_array($key,$this->fnoedit)){ //������༭�ֶ�
					$a_field = new cls_field;
					$cfg = array_merge($this->fields[$key],$cfg);
					$a_field->init($cfg,isset($this->predata[$key]) ? $this->predata[$key] : '');
					$a_field->isadd = $this->isadd;
					$varr = $a_field->varr('noedit_'.$this->fmpre,'');
					$arr1 = array('<input ','<option ','<textarea ');
					$arr2 = array('<input disabled ','<option disabled ','<textarea disabled ');
					$varr = str_replace($arr1,$arr2,$varr);
					trspecial("<font color='blue'>{$varr['trname']}</font>",$varr);
					unset($a_field);
					//echo "<script>try{\$id('noedit_{$this->fmpre}[$key]').disabled=true;}catch(ex){}<-/script->";
				}else{
					$a_field = new cls_field;
					$cfg = array_merge($this->fields[$key],$cfg);
					$a_field->init($cfg,isset($this->predata[$key]) ? $this->predata[$key] : '');
					$a_field->isadd = $this->isadd;
					$a_field->trfield($this->fmpre);
					unset($a_field);
				}
			break;
			case 'sv'://���洦��
				global $sptype,$spsize;
				if(isset($this->fmdata[$key]) && $field = @$this->fields[$key]){
					$c_upload = cls_upload::OneInstance();
					$cfg && $field = array_merge($field,$cfg);
					if($field['datatype'] == 'htmltext' && $sptype == 'auto'){
						$spsize = empty($spsize) ? 5 : max(0,intval($spsize));
						$this->fmdata[$key] = SpBody($this->fmdata[$key],$spsize * 1024,'[##]');
					}
					
					$a_field = new cls_field; 
					$a_field->init($field,isset($this->predata[$key]) ? $this->predata[$key] : '');
					$this->fmdata[$key] = $a_field->deal($this->fmpre,'');
					if($a_field->error){//��׽������Ϣ
						$this->sv_rollback();
						return $this->message($a_field->error,M_REFERER);
					}
					unset($a_field); 
					
					$this->enddata[$key] = $this->fmdata[$key]; 
					if($arr = multi_val_arr($this->fmdata[$key],$this->fields[$key])){
							foreach($arr as $x => $y){
								$this->enddata[$key.'_'.$x] = $y;
							}
					}
					
				} 
			break;
		}
	}

}
