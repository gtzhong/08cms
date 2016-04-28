<?php
/*
** ������Ա��ӻ�༭�Ĳ����࣬���ǵ����������Ĳ����ԱȽϴ�������������̽ű����з���
** sv����������return_error��ʾ����ʱ����error����������message����
** mname,password,email�����ֶ����ų����������ر���
*/
!defined('M_COM') && exit('No Permisson');
class cls_memberbase{
	protected static $mc = 0;//��Ա����
	public $isadd = 0;//���ģʽ
	public $mid = 0;//��Աid
	public $mchid = 0;//��Աģ��id
	public $noTrustee = 0;//Ϊ1ʱ���������Ա���Ĵ���
	public $fmpre = 'fmdata';//form�е���������//������������
	public $predata = array();//Ԥ����������
	public $mchannel = array();//��Աģ��
	public $fields = array();//ģ���ֶ�
	public $cfgs = array();//����������
	public $items_did = array();//�ݴ��Ѵ��������Ŀ
	public $auser = NULL;//��Ա����
	public $fmdata = array();//���ύ�����������
	
    function __construct($cfg = array()){
		self::$mc = defined('M_ADMIN') ? 0 : 1;
		$this->mid = empty($cfg['mid']) ? 0 : max(0,intval($cfg['mid']));
		if(self::$mc){//��Ա����ֻ�����޸����ѵ�����
			$this->isadd = 0;
		}else{
			$this->isadd = $this->mid ? 0 : 1;//$isaddͨ���Ƿ�ָ��mid��ʶ��0Ϊ�༭��1Ϊ���
		}
		$this->mchid = empty($cfg['mchid']) ? 0 : max(0,intval($cfg['mchid']));
		if(!empty($cfg['fmpre'])) $this->fmpre = $cfg['fmpre'];
		if(!empty($cfg['noTrustee'])) $this->noTrustee = 1;
    }
	
	function setvar($key,$var){
		$this->$key = $var;	
	}
	
	public function message($str = '',$url = '')
    {
		cls_message::show($str, $url);
	}
	
	//���������Ŀ
	protected function del_item($key){
		unset($this->cfgs[$key]);
		return false;
	}
	
	//�Ƿ�һ���Ѵ��ڵ���Ŀ
	protected function is_item($key = ''){
		return isset($this->cfgs[$key]) ? true : false;
	}
	
	protected function IsSysField($ename){
		return in_array($ename,array('mname','password','email')) ? true : false;
	}
	
	//���������Ŀ��������Ŀ��ʼ��
	function additem($key,$cfg = array()){
		$this->cfgs[$key] = $cfg;
		$re = $this->one_item($key,'init');
		if($re == 'undefined') $this->del_item($key);
	}
	
	protected function call_method($func,$args = array()){
		if(method_exists($this,$func)){
			return call_user_func_array(array(&$this,$func),$args);
		}else return 'undefined';
	}
	
	//�������ȴ���user_$key(����) -> type_$type(����) -> ͨ�÷���
	//���������Ҫ����init����ʼ�� fm����ʾ sv�����ݴ���
	protected function one_item($key,$mode = 'init'){
		if(!isset($this->cfgs[$key])) return false;
		$re = $this->call_method("user_$key",array($key,$mode));//���Ʒ���
		if(!isset($this->cfgs[$key]['_type'])) $this->cfgs[$key]['_type'] = 'common';
		if($re == 'undefined'){
			switch($this->cfgs[$key]['_type']){
				case 'field':
					$re = $this->type_field($key,$mode);
				break;
				case 'ugid':
					$re = $this->type_ugid($key,$mode);
				break;
			}
		}
		if(in_array($mode,array('fm','sv',))) $this->items_did[] = $key;//��¼�Ѵ������Ŀ
		return $re;
	}

	// $cfg['title'] = 'ר������' // �Զ��帡�����ȵ�title
	// $cfg['isself'] = 1 ��̨�޸��Լ���pw
	function TopHead($cfg=array()){
		$curuser = cls_UserMain::CurUser();
		if(self::$mc){
			if(!defined('M_COM')) exit('No Permission');
		}else{
			if(!defined('M_COM') || !defined('M_ADMIN')) exit('No Permission');
			aheader();
			if(empty($cfg['isself'])){
				if($re = $curuser->NoBackFunc('member')) $this->message($re);
			}
		}
		$title = isset($cfg['title']) ? $cfg['title'] : "��Ա".($this->isadd ? '���' : '����')."";
		echo "<title>$title</title>";
		
		//��ȡ����ʼ�����ϣ���ģ�͡��ֶΡ�����Ա����
		$this->ReadInfo();//��ȡ��Ա����
		$this->ReadConfig();//��ȡ����
	}
	
	//��Ա����ʱ����ȡָ����Աmid��ԭ������
	function ReadInfo(){
		if(!$this->isadd){//������ʱ��Ч
			if(self::$mc){
				$this->auser = cls_UserMain::CurUser();
				$this->mid = $this->auser->info['mid'];
			}else{
				if(!$this->mid) $this->message('��ָ����Ա��');
				$curuser = cls_UserMain::CurUser();
				if($this->mid == $curuser->info['mid']){
					$this->auser = cls_UserMain::CurUser();
					$this->auser->detailed || $this->auser->detail_data(); //��ǰ��Ա����û��detail����
				}else{
					$this->auser = new cls_userinfo;
					if(!($this->auser->activeuser($this->mid,2))) $this->message('��ָ����Ա��');
				}
			}
			$this->mchid = $this->auser->info['mchid'];
			$this->predata = $this->auser->info;
	#		if(!$this->admin_pm($this->predata['caid'])) $this->message('��û��ָ����Ŀ�Ĺ���Ȩ�� !');???????????????
		}
	}
	
	//��ȡ��Աģ�ͼ��ֶ����� 
	function ReadConfig(){
		if(!($this->mchannel = cls_cache::Read('mchannel',$this->mchid))) $this->message('��ָ����Ա���͡�');
		$this->fields = cls_cache::Read('mfields',$this->mchid);
		foreach($this->fields as $k => $v){//�ų�ϵͳ�����ֶ�
			if($this->IsSysField($k)) unset($this->fields[$k]);
		}
		if(self::$mc){//�ڻ�Ա�����ų���Ա��֤�ֶ�
			$mctypes = cls_cache::Read('mctypes');
			foreach($mctypes as $k => $v){
				if(!empty($v['available']) && strstr(",$v[mchids],",",".$this->mchid.",")){ //����Ļ�Աģ��
					unset($this->fields[$v['field']]);
				}
			}
		}
	}
	
	//��Ҫ�����Ա����/�����̨�����/�༭�Ĳ��
	function TopAllow(){
		$curuser = cls_UserMain::CurUser();
		if(self::$mc){//��Ա����ֻ�ܱ༭���ѵ����ϣ����ܽ�����Ӳ���
			if($this->isadd) $this->message('����ֹ�Ĺ��ܡ�');//��Ա���Ĳ������ӻ�Ա 
			if($this->noTrustee && $curuser->getTrusteeshipInfo()) $this->message('���Ǵ����û�����ǰ������ԭ�û�������Ȩ�ޣ�');
		}else{
			if($re = $this->NoBackPm($this->mchid)) $this->message($re);
			if($this->isadd){
			
			}else{
				if($this->predata['isfounder'] && $curuser->info['mid'] != $this->predata['mid']) $this->message('��ʼ������ֻ���ɱ��˹���');
			}
		}
	}
	
	//�����ɫ�Ļ�Աģ�͹���Ȩ�ޣ����ڹ����̨��ʹ��
	function NoBackPm($mchid = 0){
		$curuser = cls_UserMain::CurUser();
		if(self::$mc) return '';
		if(!$mchid) return '��ָ����Ա����';
		return $curuser->NoBackPmByTypeid($mchid,'mchid');
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
		
		$title || $title = (empty($this->predata['mname']) ? $this->mchannel['cname'] : $this->predata['mname']).'&nbsp; -&nbsp; '.($this->isadd ? '��ӻ�Ա' : '��Ա����');
		if($url){
			if($this->isadd){
				if(!in_str('mchid=',$url)) $url .= "&mchid={$this->mchid}"; 
			}else{
				if(!in_str('mid=',$url)) $url .= "&mid={$this->mid}"; 
			}
			tabheader($title,'memberdetial',$url,2,1,1);
		}else{
			tabheader($title);
		}
	}
	
	// չʾָ���$incsΪ�ձ�ʾΪ����ʣ����
	// $noinc=array()����$incs�������ų�$noinc�е��ֶΣ�Ϊ�����ų���
	function fm_items($incs = '',$noinc = array()){
		if(!empty($incs)) $incs = array_filter(explode(',',$incs));
		if(empty($incs)) $incs = array_keys($this->cfgs);//չʾʣ����
		foreach($incs as $key){
			if(!empty($noinc) && in_array($key,$noinc)) continue;
			if(!in_array($key,$this->items_did)){
				$this->one_item($key,'fm');
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
	
	//�����̨��$typeĬ��Ϊ0ʱ$strΪ���������ǣ�1��ʾ$strΪ�ı�����
	//��Ա���ģ�str���������Ա���İ�����ʶ��ֱ�ӵ��ı����ݣ�$typeĬ��Ϊ0ֱ����ʾ���ݣ�tip-�����ص���ʾ��fix-�̶�����ʾ��
	function fm_guide_bm($str = '',$type = 0){
		if(self::$mc){
			m_guide($str,$type ? $type : '');
		}else{
			if(!$str){
				$str = $this->isadd ? 'memberadd' : 'memberdetail';
				if(is_file(M_ROOT."dynamic/aguides/{$str}_{$this->mchid}.php")) $str .= "_{$this->mchid}";
				$type = 0;
			}
			a_guide($str,$type);
		}
	}
	
	//ͨ�õ��ύ��������ͨ�����Ʒ���������չ
	//�����������ʼ�����ý����ۺϴ���
	//cfg[message]:��ʾ��Ϣ
	function sv_all_common($cfg = array()){
		
		//����$this->fmdata�е�ֵ
		$this->sv_set_fmdata();
		
		//��������Ҫ�ر�����������ucenter�й�
		$this->sv_items('mname,password,email');
		
		//�����Ҫ��mname,password,email֮��ִ��
		$this->sv_add_init();
		
		//����UC/WINDIDͬ������
		$this->sv_ucenter();
		
		//�������µ�������Ŀ������ʱδִ�����ݿ����
		$this->sv_items();
		
		//ִ���Զ��������������ϱ��
		$this->sv_update();
		
		//�Զ�����
		if($this->isadd){ 
			$this->auser->autopush();
		}
		
		//�ϴ�����
		$this->sv_upload();
		
		//����ʱ��Ҫ�����񣬰���������¼���ɹ���ʾ��
		$this->sv_finish($cfg);
		
	}
	
	//�޸�����������ύ����
	function sv_all_password_self(){
		
		//����$this->fmdata�е�ֵ
		$this->sv_set_fmdata();
		
		//�������µ�������Ŀ������ʱδִ�����ݿ����
		$this->sv_items();
		
		//ִ���Զ��������������ϱ��
		$this->sv_update();
		
		//����ʱ��Ҫ�����񣬰���������¼���ɹ���ʾ��
		$this->sv_finish(array('message' => '�����޸ĳɹ�','record' => '�޸���������'));
		
	}
	
	//�ռ侲̬���ύ����
	function sv_all_static(){
		
		//����$this->fmdata�е�ֵ
		$this->sv_set_fmdata();
		
		//�������µ�������Ŀ������ʱδִ�����ݿ����
		$this->sv_items();
		
		//ִ���Զ��������������ϱ��
		$this->sv_update();
		
		//���ɾ�̬Ŀ¼���棬ֻ�������ݿ����֮��ִ��
		cls_CacheFile::Update('mspacepaths');
		
		//�ռ侲̬���£���Ҫ��sv_itemsִ�У���Ϊ��Ҫ��������Ŀִ�в��������ݿ�֮��������ɿռ侲̬
		$message = $this->sv_static_update();
		
		//����ʱ��Ҫ�����񣬰���������¼���ɹ���ʾ��
		$this->sv_finish(array('message' => $message ? $message : '��̬�ռ����óɹ�','record' => '���þ�̬�ռ�'));
	}
	
	//�ռ侲̬����
	function sv_static_update(){
		global $timestamp;
		$message = '';
		if(!empty($this->fmdata['update']) && !empty($this->auser->info['mspacepath'])){
			// ���ɾ�̬ʱcls_Mspacebase::IndexUrl()������ж���Ҫ�ȸ���msrefreshdate��
			$this->auser->updatefield('msrefreshdate',$timestamp);
			$this->auser->updatedb();
			$message = cls_Mspace::ToStatic($this->auser->info['mid']);
		}
		return $message;
	}
	
	//����ָ�������չʾ����ʣ����
	function sv_items($incs = ''){
		if(!empty($incs)) $incs = array_filter(explode(',',$incs));
		if(empty($incs)) $incs = array_keys($this->cfgs);//չʾʣ����
		foreach($incs as $key){
			if(!in_array($key,$this->items_did)){
				$this->one_item($key,'sv');
			}
		}
	}
	
	function sv_set_fmdata(){
		$this->fmdata = &$GLOBALS[$this->fmpre];//��Ϊ�ֶδ�����δ��һ���Ż���������Ҫ������
	}	
	
	//��ӻ�Ա֮�󣬸�������ʱ�����쳣�Ļ�����Ҫɾ�������ӵĻ�Ա��¼
	//mname,password,email֮����Ŀ��������󣬶���Ҫʹ�ô˷���
	function sv_rollback(){
		if($this->mid && $this->isadd){
			$c_upload = cls_upload::OneInstance();
			$this->auser->delete();
			$c_upload->closure(1);
		}
	}
	
	function sv_fail($return_error = 0){
		$c_upload = cls_upload::OneInstance();
		$c_upload->closure(1);
		return $this->message('��Ա���ʧ��',M_REFERER);
	}
	
	//���ӻ�Ա�ĳ�ʼ������
	function sv_add_init(){
		if(!$this->isadd) return;
		$na = array('mname' => '��Ա�ʺ�','password' => '��Ա����','email' => 'E-mail',);
		foreach($na as $key => $val){
			$$key = '';
			if($this->is_item($key) && !empty($this->fmdata[$key])) $$key = $this->fmdata[$key];
			if(!$$key) $this->message("���ӻ�Ա��Ҫ���� $val",M_REFERER);
		}
		if(empty($this->auser))  $this->auser = new cls_userinfo;
		if($this->mid = $this->auser->useradd($mname,_08_Encryption::password($password),$email,$this->mchid)){
			$this->auser->check(1);
		}else{
			$this->sv_fail();
		}
	}
		
	//����uc/WINDIDͬ�����������
	function sv_ucenter()
    {
	    global $onlineip;
		$na = array('mname' => '��Ա�ʺ�','password' => '��Ա����','email' => 'E-mail',);
		if($this->isadd){
			foreach($na as $key => $val){
				$$key = '';
				if($this->is_item($key) && !empty($this->fmdata[$key])) $$key = $this->fmdata[$key];
				if(!$$key) $this->message("ͬ��ע����Ҫ���� $val",M_REFERER);
			}
            # UCenter
            if(cls_ucenter::init())
            {
    			$uc_uid = cls_ucenter::register($mname,$password,$email,FALSE); //��̨��Ӳ���Ҫͬ����¼
    			empty($uc_uid) || $this->auser->updatefield(cls_ucenter::UC_UID, $uc_uid);           
            }
            
        	# ͬ��ע���û���WINDID��Ŀǰ�÷���ֻ���ں�̨����û�
        	$pw_uid = cls_WindID_Send::getInstance()->synRegister($mname, $password, $email, $onlineip, false);
        	empty($pw_uid) || $this->auser->updatefield(cls_Windid_Message::PW_UID, $pw_uid);
		}else{
			unset($na['mname']);//�༭ʱ����Ҫmname
			foreach($na as $key => $val){
				$$key = '';
				if($this->is_item($key) && @$this->fmdata[$key] != @$this->predata[$key]) $$key = $this->fmdata[$key];
			}
			if(!$password && !$email) return;//δ�޸������email
            
            # UCenter
            if(cls_ucenter::init())
            {
                if($re = cls_ucenter::edit($this->auser->info['mname'],$password,$email)) $this->message($re,M_REFERER);
            }
            # ͬ���޸�����
            $updata_arr = array('email' => $email);
            if($password) $updata_arr['password'] = $password;
            cls_WindID_Send::getInstance()->editUser($this->auser->info['mid'], '', $updata_arr);
		}
	}
	
	//ִ���Զ��������������ϱ��
	function sv_update(){
		$this->auser->updatedb();
	}
	
	//�ϴ�����
	function sv_upload(){
		$c_upload = cls_upload::OneInstance();
		$c_upload->closure(1,$this->mid,'members');
		$c_upload->saveuptotal(1);
	}
		
	//����ʱ��Ҫ������ �磺������¼���ɹ���ʾ
	function sv_finish($cfg = array()){
		if(empty($cfg['message'])) $cfg['message'] = '��Ա'.($this->isadd ? '���' : '�޸�').'���';
		if(empty($cfg['record'])) $cfg['record'] = ($this->isadd ? '���' : '�޸�').'��Ա';
		self::$mc || adminlog($cfg['record']);
        //$cfg['jumptype']  ��Ϣ��ʾ֮��Ĵ���ʽ������رյ�ǰ���ڡ���ת�����ҳ��
		$this->message($cfg['message'],empty($cfg['jumptype'])?axaction(6,M_REFERER):$cfg['jumptype']);
	}
	
	//ֻ��������»�Ա
	protected function user_mname($key,$mode = 'init'){
		global $cms_abs;
		if(!$this->isadd) return $this->del_item($key);//�޸�ʱ������Ч
		$cfg = &$this->cfgs[$key];
		switch($mode){
			case 'init'://��ʼ��
			break;
			case 'fm'://����ʾ�����У������?????????
				$guide = '������3-15λ�ַ�';
				trbasic(self::NotNullFlag().'��Ա�ʺ�',"{$this->fmpre}[mname]",isset($this->predata['mname']) ? $this->predata['mname'] : '','text',array('validate'=>makesubmitstr("{$this->fmpre}[mname]",1,0,3,15),'guide' => $guide,));
				$ajaxURL = $cms_abs . _08_Http_Request::uri2MVC('ajax=check_member_info&filed=mname&val=%1');
				echo _08_HTML::AjaxCheckInput($this->fmpre.'[mname]', $ajaxURL);
			break;
			case 'sv'://���洦��
				$re = cls_userinfo::CheckSysField(@$this->fmdata[$key],$key,$this->isadd ? 'add' : 'edit');
				if($re['error']){
					$this->message($re['error'], M_REFERER);
				}else $this->fmdata[$key] = $re['value'];
			break;
		}
	}
	
	protected function user_password($key,$mode = 'init'){
		$cfg = &$this->cfgs[$key];
		switch($mode){
			case 'init'://��ʼ��
			break;
			case 'fm'://����ʾ
				$guide = $this->isadd ? '������1-15λ����' : '�޸�����������1-15λ���룬���������գ���ʾ���޸�����';
				$title = $this->isadd ? (self::NotNullFlag().'��Ա����') : '�޸�����';
				trbasic($title,"{$this->fmpre}[$key]",'',$key,array('validate'=>' autocomplete="off"'.makesubmitstr("{$this->fmpre}[$key]",$this->isadd?1:0,0,1,15),'guide' => $guide,));
			break;
			case 'sv'://���洦��UC��ͬ�����л��ܴ�����������������
				$re = cls_userinfo::CheckSysField(@$this->fmdata[$key],$key,$this->isadd ? 'add' : 'edit');
				if($re['error']){
					$this->message($re['error'], M_REFERER);
				}else $this->fmdata[$key] = $re['value'];
				
				if(!$this->isadd){//���ӻ�Ա���⴦��
					if(empty($this->fmdata[$key])) return;//���޸�����
					if(!$this->auser->updatefield($key,_08_Encryption::password($this->fmdata[$key]))) $this->del_item($key);
				}//ͨ��ͳһ�����ӻ�Ա���������������
			break;
		}
	}
	
	
	//����������֤��������������
	//ע��ֻ�����޸����ѵ�����
	protected function user_password_self($key,$mode = 'init'){
		$curuser = cls_UserMain::CurUser();
		if($this->isadd) return $this->del_item($key);//ֻ�����޸�����
		if($curuser->info['mid'] != $this->auser->info['mid']) return $this->del_item($key);//ֻ�����޸����ѵ�����
		$cfg = &$this->cfgs[$key];
		switch($mode){
			case 'init'://��ʼ��
			break;
			case 'fm'://����ʾ
				$guide = '������1-15λ����';
				trbasic(self::NotNullFlag().'ԭʼ����',"{$this->fmpre}[opassword]",'','password', array('validate' => ' autocomplete="off" '.makesubmitstr("{$this->fmpre}[opassword]",1,0,0,15),'guide' => $guide,));
				trbasic(self::NotNullFlag().'������',"{$this->fmpre}[npassword]",'','password', array('validate' => ' autocomplete="off" '.makesubmitstr("{$this->fmpre}[npassword]",1,0,0,15),'guide' => $guide,));
				trbasic(self::NotNullFlag().'�ظ�����',"{$this->fmpre}[npassword2]",'','password', array('validate' => ' autocomplete="off" '.makesubmitstr("{$this->fmpre}[npassword2]",1,0,0,15),'guide' => $guide,));
			break;
			case 'sv':
				foreach(array('opassword','npassword','npassword2',) as $var){
					$re = cls_userinfo::CheckSysField(@$this->fmdata[$var],'password','edit');
					if($re['error']){
						$this->message($re['error'], M_REFERER);
					}else $this->fmdata[$var] = $re['value'];
				}
				if(_08_Encryption::password($this->fmdata['opassword']) != $this->auser->info['password']) $this->message('ԭʼ�������',M_REFERER);
				if($this->fmdata['npassword'] != $this->fmdata['npassword2']) $this->message('�����������벻һ��',M_REFERER);
				if($this->fmdata['opassword'] == $this->fmdata['npassword']) $this->message('���������������ͬ',M_REFERER);
				//UC������Ա����Ϊ��¼״̬
				if($this->auser->updatefield('password',_08_Encryption::password($this->fmdata['npassword'])))
                {
					if($re = cls_ucenter::edit($this->auser->info['mname'],$this->fmdata['npassword'])) $this->message($re,M_REFERER);
                    # ͬ���޸�WINDID�û�����
                    cls_WindID_Send::getInstance()->editUser(
                        $curuser->info['mid'], 
                        $this->fmdata['opassword'], 
                        array('password' => $this->fmdata['npassword'])
                    );
					cls_userinfo::LoginFlag($this->auser->info['mid'],_08_Encryption::password($this->fmdata['npassword']));
				}
			break;
		}
	}
	
	protected function user_email($key,$mode = 'init'){
        global $mid,$cms_abs;
		$cfg = &$this->cfgs[$key];
		switch($mode){
			case 'init'://��ʼ��
			break;
			case 'fm'://����ʾ
				$guide = '��������ȷ��email��ʽ';
				trbasic(self::NotNullFlag().'E-mail',"{$this->fmpre}[$key]",isset($this->predata[$key]) ? $this->predata[$key] : '','text',array('validate'=>makesubmitstr("{$this->fmpre}[$key]",1,'email',0,50),'guide' => $guide,));
				if($this->isadd){
					$ajaxURL = $cms_abs . _08_Http_Request::uri2MVC('ajax=check_member_info&filed=email&val=%1');
					echo _08_HTML::AjaxCheckInput($this->fmpre.'[email]', $ajaxURL);
				}
			break;
			case 'sv'://���洦��
				$re = cls_userinfo::CheckSysField(@$this->fmdata[$key],$key,$this->isadd ? 'add' : 'edit', $mid);
				if($re['error']){
					$this->message($re['error'], M_REFERER);
				}else $this->fmdata[$key] = $re['value'];
				
				if(!$this->isadd){//���ӻ�Ա���⴦��
					if(!$this->auser->updatefield($key,$this->fmdata[$key])) $this->del_item($key);
				}//ͨ��ͳһ�����ӻ�Ա���������������
			break;
		}
	}
	
	//��Ա�����ã��ֶ�������ã�����ֻ�ܲ鿴
	//onlyset��ֻ��ʾ�����õ���
	//'ismust'=>1, //�Ƿ��ѡ
	//'notime'=>1, //����ʾʱ��
	//'afirst'=>array(''=>'-��ѡ��-'), //��һ��ѡ������, Ĭ��Ϊarray('0' => '�����Ա')
	protected function type_ugid($key,$mode = 'init'){
		$grouptypes = cls_cache::Read('grouptypes');
		$gtid = max(0,intval(str_replace('ugid','',$key)));
		if(!$gtid || ($gtid== 2) || empty($grouptypes[$gtid])) return $this->del_item($key);//�ų���������ϵ
		if(!($ugidsarr = ugidsarr($gtid,$this->mchid,1))) return $this->del_item($key);
		$cfg = &$this->cfgs[$key];
		
		$_can_set = false;//�Ƿ�������ã����ǽ���ʾ
		if(self::$mc){
			if(!$grouptypes[$gtid]['mode']) $_can_set = true;
		}else{
			if($grouptypes[$gtid]['mode'] < 2) $_can_set = true;
		}
		if(!empty($cfg['onlyset']) && !$_can_set) return $this->del_item($key);//ֻ������ʾ��������
		
		switch($mode){
			case 'init'://��ʼ��
				$grouptypes[$gtid]['ismust'] = empty($cfg['ismust']) ? '' : "<span style='color:#F00'> * </span>";
			break;
			case 'fm'://����ʾ
				if($_can_set){
					$afirst = empty($cfg['afirst']) ? array('0' => '�����Ա') : $cfg['afirst'];
					$ismust = empty($cfg['ismust']) ? '' : " rule='must' ";
					$str = makeselect("{$this->fmpre}[grouptype$gtid]",makeoption($afirst + $ugidsarr,!empty($this->predata["grouptype$gtid"]) ? $this->predata["grouptype$gtid"] : 0),$ismust);
					empty($cfg['notime']) && $str .= " �������ڣ�".OneCalendar("{$this->fmpre}[grouptype{$gtid}date]",!empty($this->predata["grouptype{$gtid}date"]) ? date('Y-m-d',$this->predata["grouptype{$gtid}date"]) : '');
				}else{
					$str = !empty($this->predata["grouptype$gtid"]) ? $ugidsarr[$this->predata["grouptype$gtid"]] : '�����Ա';
					$str = "<b>$str</b>";
					$str .= " �������ڣ�".(!empty($this->predata["grouptype{$gtid}date"]) ? date('Y-m-d',$this->predata["grouptype{$gtid}date"]) : '������');
				}
				trbasic(@$grouptypes[$gtid]['ismust'].$grouptypes[$gtid]['cname'],'',$str,'');
			break;
			case 'sv'://���洦��
				if($_can_set){
					$this->fmdata["grouptype$gtid"] = empty($this->fmdata["grouptype$gtid"]) ? 0 : trim($this->fmdata["grouptype$gtid"]);
					$this->fmdata["grouptype{$gtid}date"] = empty($this->fmdata["grouptype{$gtid}date"]) || !cls_string::isDate($this->fmdata["grouptype{$gtid}date"]) ? 0 : strtotime($this->fmdata["grouptype{$gtid}date"]);
					$this->auser->handgroup($gtid,$this->fmdata["grouptype$gtid"],$this->fmdata["grouptype{$gtid}date"]);
				}else return $this->del_item($key);//�����ֶ�������,�����޸�
			break;
		}
	}
	
	//ֻ���ڻ�Ա�༭
	protected function user_mtcid($key,$mode = 'init'){
		if($this->isadd) return $this->del_item($key);
		if(!($mtcidsarr = $this->auser->mtcidsarr())) return $this->del_item($key);
		$cfg = &$this->cfgs[$key];
		switch($mode){
			case 'init'://��ʼ��
			break;
			case 'fm'://����ʾ
				trbasic('��Ա�ռ�ģ�巽��',"{$this->fmpre}[mtcid]",makeoption($mtcidsarr,$this->auser->info['mtcid']),'select');
			break;
			case 'sv'://���洦��
				$this->fmdata[$key] = empty($this->fmdata[$key]) ? 0 : trim($this->fmdata[$key]);
				$mtckeys = array_keys($mtcidsarr);
				if(in_array($this->fmdata[$key],$mtckeys)){ 
					$this->auser->updatefield('mtcid',$this->fmdata[$key]);
				}else return $this->del_item($key);//�������ѡ��Χ�������޸�
			break;
		}
	}
	
	//400�绰����
	//ע����Ϊ��ȫ���Բ�ʹ����վ�ٷ��ṩ���ܻ������Բ���webcall_enable��������ʹ��400�绰
	protected function user_webcall($key,$mode = 'init'){
		global $webcallpmid;
		if($this->isadd || !self::$mc) return $this->del_item($key);
		if(empty($webcallpmid) || $this->auser->noPm($webcallpmid)) return $this->del_item($key);
		
		$cfg = &$this->cfgs[$key];
		$keyurl = $key.'url';
		switch($mode){
			case 'init'://��ʼ��
			break;
			case 'fm'://����ʾ
				trbasic('400�绰����',"{$this->fmpre}[$key]",$this->auser->info[$key],'text',array('w' => 30,'validate' => makesubmitstr("{$this->fmpre}[$key]",0,0,6,20),));
				trbasic('400��Ѳ�������',"{$this->fmpre}[$keyurl]",$this->auser->info[$keyurl],'textarea',array('validate' => makesubmitstr("{$this->fmpre}[$keyurl]",0,0,10,255),));
			break;
			case 'sv'://���洦��
				$this->fmdata[$key] = empty($this->fmdata[$key]) ? '' : trim($this->fmdata[$key]);
				$this->fmdata[$keyurl] = empty($this->fmdata[$keyurl]) ? '' : trim($this->fmdata[$keyurl]);
				$this->auser->updatefield($key,$this->fmdata[$key]);
				$this->auser->updatefield($keyurl,$this->fmdata[$keyurl]);
			break;
		}
	}
	
	//������Ա�ֶ�
	//cfg���봫������ã��Դ������������
	protected function type_field($key,$mode = 'init'){
		$cfg = &$this->cfgs[$key];
		if(empty($this->fields[$key]) || $this->IsSysField($key)) return $this->del_item($key);
		
		if(self::$mc){//��Ա���Ĳ��ܶ���֤�ֶν�������
			$mctypes = cls_cache::Read('mctypes');
			foreach($mctypes as $k => $v){
				if(!empty($v['available']) && $v['field']==$key && strstr(",$v[mchids],",",".$this->mchid.",")) return $this->del_item($key);
			}
		}
		
		switch($mode){
			case 'init'://��ʼ��
			break;
			case 'fm'://����ʾ
				$a_field = new cls_field;
				$cfg = array_merge($this->fields[$key],$cfg);
				$a_field->init($cfg,isset($this->predata[$key]) ? $this->predata[$key] : '');
				$a_field->isadd = $this->isadd;
				$a_field->trfield($this->fmpre);
				unset($a_field);
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
					
					$this->auser->updatefield($key,$this->fmdata[$key],$field['tbl']);
					if($arr = multi_val_arr($this->fmdata[$key],$field)) foreach($arr as $x => $y) $this->auser->updatefield($key.'_'.$x,$y,$field['tbl']);
				}
			break;
		}
	}
	
	//��Ա�ռ�Ŀ¼
	protected function user_mspacepath($key,$mode = 'init'){
		global $mspacepmid;
		$cfg = &$this->cfgs[$key];
		if(!$mspacepmid || $this->auser->noPm($mspacepmid)) return $this->del_item($key);
		
		switch($mode){
			case 'init'://��ʼ��
			break;
			case 'fm'://����ʾ
				$na = array(
				'w' => '15',
				'guide' => '��Сд��ĸ���ּ�_��ɣ�����ĸ��ͷ�������ֵ��ʾʹ�ö�̬�ռ䡣',
				'validate' => makesubmitstr("{$this->fmpre}[mspacepath]",0,0,2,15),
				'addstr' => "<input type=\"button\" value=\"�������\" onclick=\"check_repeat('{$this->fmpre}[mspacepath]','mdirname');\">",
				);
				trbasic('���þ�̬Ŀ¼',"{$this->fmpre}[mspacepath]",empty($this->auser->info[$key]) ? '' : $this->auser->info[$key],'text',$na);
				trbasic('ͬʱ���¾�̬','',OneCheckBox("{$this->fmpre}[update]",'���ڸ���',1),'');
			break;
			case 'sv'://���洦��
				//���¾�̬������Ҫ������ִ�У���Ϊ��Ҫ��������Ŀִ�в��������ݿ�֮��������ɿռ侲̬
				global $db,$tblprefix,$mspacedir,$timestamp;
				if(isset($this->fmdata[$key])){
					$this->fmdata[$key] = strtolower(trim(strip_tags($this->fmdata[$key])));
					if(!$this->fmdata[$key] || preg_match("/[^a-z_0-9]+/",$this->fmdata[$key])) $this->fmdata[$key] = '';
					$o_mspacepath = $this->auser->info[$key];
					if($this->auser->info[$key] != $this->fmdata[$key]){
						if($this->fmdata[$key]){
							if($db->result_one("SELECT mspacepath FROM {$tblprefix}members WHERE mid<>'{$this->auser->info['mid']}' AND mspacepath='{$this->fmdata[$key]}'")){
								$this->message('��̬�ռ�Ŀ¼�ѱ�ռ��',M_REFERER);
							}
							if($this->auser->info[$key] && is_dir(M_ROOT.$mspacedir.'/'.$this->auser->info[$key])){
								if(!rename(M_ROOT.$mspacedir.'/'.$this->auser->info[$key],M_ROOT.$mspacedir.'/'.$this->fmdata[$key])) $this->fmdata[$key] = '';
							}elseif(!mmkdir(M_ROOT.$mspacedir.'/'.$this->fmdata[$key],0)) $this->message('��̬�ռ�Ŀ¼�޷�����',M_REFERER);
						}
						$this->auser->updatefield($key,$this->fmdata[$key]);
					}
					if($this->auser->info[$key]){
						$ifile = M_ROOT.$mspacedir.'/'.$this->auser->info[$key].'/index.php';
						if(!is_file($ifile)) str2file('<?php $mid = '.$this->auser->info['mid'].'; include dirname(dirname(__FILE__)).\'/index.php\'; ?>',$ifile);
					}elseif($o_mspacepath){
						if(!_08_FileSystemPath::CheckPathName($o_mspacepath)) clear_dir(M_ROOT.$mspacedir.'/'.$o_mspacepath,true);
						$this->auser->updatefield('msrefreshdate',0);
					}
				}
			break;
		}
	}
	
	//��Ա�ռ侲̬״̬
	protected function user_static_state($key,$mode = 'init'){
		$cfg = &$this->cfgs[$key];
		switch($mode){
			case 'init'://��ʼ��
			break;
			case 'fm'://����ʾ
				trbasic('�ռ���ҳ','',"<a href=\"{$this->auser->info['mspacehome']}\" target=\"_blank\">>>Ԥ��</a>",'');
				trbasic('�ռ侲̬����','',empty($this->auser->info['msrefreshdate']) ? '��δ����' : date('Y-m-d H:i',$this->auser->info['msrefreshdate']).' ����','');
			break;
			case 'sv'://���洦��
			break;
		}
	}
	
	//�����й�������
	protected function user_trusteeship($key,$mode = 'init'){
		global $cms_abs,$g_apid;
		$cfg = &$this->cfgs[$key];
		switch($mode){
			case 'init'://��ʼ��
			break;
			case 'fm'://����ʾ
				$mnames = array();
				if($this->auser->info['trusteeship'] && $mids = array_filter(explode(',',$this->auser->info['trusteeship']))){
					foreach($mids as $mid){
						if($mname = cls_userinfo::getNameForId($mid)) $mnames[] = $mname;
					}
				}
                $url = $cms_abs . 'adminm.php?from_mid=' . $this->auser->info['mid'];
                $call_function = _08_HTML::createCopyCode('call_function', $url, '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;');
    			trbasic("�����й�����","{$this->fmpre}[$key]",$mnames ? implode(',',$mnames) : '','text', array('guide'=>'<br />( ��ʽ����Ա1,��Ա2 )������������Զ��ŷֿ������뱾վ��Ա�Ļ�Ա�ʺţ�����Ϊ��������йܻ�Ա��','w' => 70,));
				trbasic("���ܵ�ַ {$call_function}",'url', $url,'text',array('guide'=>'<br />�й��˿��Ծ����ϴ��ܵ�ַ������Ļ�̨���Ľ�����Ϣ����','w' => 70,'validate' => 'readonly'));
			break;
			case 'sv'://���洦��
				$this->fmdata[$key] = empty($this->fmdata[$key]) ? '' : trim($this->fmdata[$key]);
				$this->auser->setTrusteeshipUser($this->fmdata[$key]);
			break;
		}
	}
	
	//��Ա�ռ侲̬״̬
	protected static function NotNullFlag(){
		return '<font color="red"> * </font>';
	}
    
	//��QQ������΢��
	protected function user_openid_sinauid($key,$mode = 'init'){
		$curuser = cls_UserMain::CurUser();
		switch($mode){
			case 'init'://��ʼ��
			break;
			case 'fm'://����ʾ
                cls_phpToJavascript::showOtherBind();
    			trbasic("��QQ", '', (empty($curuser->info['openid']) ? '<a target="_self" onclick="OtherWebSiteLogin(\'qq\', 600, 470);" href="javascript:void(0)" title="QQ�ʺŵ�¼" class="qqbnt l" style="color:green;">��ʼ��¼��</a>' : '<a target="_self" onclick="OtherWebSiteLogin(\'qq_reauth\', 600, 470);" href="javascript:void(0)" title="QQ�ʺŵ�¼" class="qqbnt l" style="color:green;">���°�</a>'), 'string');
    			trbasic("������΢��", '', (empty($curuser->info['sina_uid']) ? '<a onclick="OtherWebSiteLogin(\'sina\', 600, 400);" href="javascript:void(0)" title="����΢���ʺŵ�¼" class="wbbnt" target="_self" style="color:green;">��ʼ��¼��</a>' : '<a onclick="OtherWebSiteLogin(\'sina_reauth\', 600, 400);" href="javascript:void(0)" title="����΢���ʺŵ�¼" class="wbbnt" target="_self" style="color:green;">���°�</a>'), 'string');
			break;
			case 'sv'://���洦��
			break;
		}
	}
}
