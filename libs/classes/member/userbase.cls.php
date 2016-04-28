<?php
!defined('M_COM') && exit('No Permisson');
class cls_userbase{
	var $info = array();
	var $updatearr = array();
	var $subed = 0;
	var $detailed = 0;
	var $apms = array();//�����ɫȨ��
    
    protected $_db = null;
    protected $_mconfigs = array();
    protected $_timestamp = 0;
    protected $_onlineip = '';
    
	function init(){
		$this->info = array();
		$this->detailed = 0;
		$this->subed = 0;
		$this->updatearr = array();
	}
	function currentuser(){
		global $m_cookie,$db,$tblprefix,$onlineip,$timestamp,$msessionexist;
		if(defined('M_NOUSER') || (defined('ISROBOT') && ISROBOT)){
			$this->info = self::nouser_info();
			$this->info += array('onlineip' => $onlineip,'mslastactive' => $timestamp,'lastolupdate' => $timestamp,'errtimes' => 0,'errdate' => 0,);
			return;
		}
		$memberid = 0;$memberpwd = '';
        
        /**
         * ĳЩ�����ʹ�ö���̵ķ�ʽ��������ʹswfupload�ϴ��ؼ�COOKIE���ܴ���ȥ��
         * �������ⷽ����COOKIE�����������
         **/
        $post = cls_env::_POST();
        if (isset($post['userauth']))
        {
            $m_cookie['userauth'] = $post['userauth'];
        }
        if (isset($post['msid']))
        {
            $m_cookie['msid'] = $post['msid'];
        }
        
		if(!empty($m_cookie['userauth'])) @list($memberpwd,$memberid) = maddslashes(explode("\t", authcode($m_cookie['userauth'], 'DECODE')), 1);
		if(!($memberid = max(0,intval($memberid)))) list($memberpwd,$memberid) = array('',0);
		$msessionexist = 0;
		if($msid = isset($m_cookie['msid']) ? $m_cookie['msid'] : ''){
			if($memberid){
				$sqlstr = "SELECT ms.*,m.* FROM {$tblprefix}msession ms,{$tblprefix}members m WHERE ms.mid=m.mid AND ms.msid='$msid' AND m.mid='$memberid' AND m.password='$memberpwd' AND m.checked=1";
			}else $sqlstr = "SELECT * FROM {$tblprefix}msession WHERE msid='$msid'";
			if($msession = $db->fetch_one($sqlstr)){
				$msessionexist = 1;
				$memberid || $msession = array_merge($msession,self::nouser_info());
			}
		}
		if(!$msessionexist){
			if($memberid){
				if(!($msession = $db->fetch_one("SELECT * FROM {$tblprefix}members WHERE mid='$memberid' AND password='$memberpwd' AND checked=1"))){
					list($memberpwd,$memberid) = array('',0);
				}
			}
			$memberid || $msession = self::nouser_info();
			$msession += array('onlineip' => $onlineip,'mslastactive' => $timestamp,'lastolupdate' => $timestamp,'errtimes' => 0,'errdate' => 0,);
			if(!$msid){
				$msession['msid'] = cls_string::Random(6);
				msetcookie('msid',$msession['msid'],365*86400);
			}else $msession['msid'] = $msid;
		}
        $this->info = $msession;
		
		$this->info['mspacehome'] = cls_Mspace::IndexUrl($this->info);
		$this->groupclear(1);
		$this->updatesession();
	}
    /**
     * ��Ա���ĵĴ��ܲ���
     * ʹ�ñ������ߵ���ݵ�¼��Ա����
     */
    public function mcTrustee(){
		global $onlineip,$timestamp,$memberid;
        // �ж��Ƿ�Ϊ�йܲ����������Ƿ���Ȩ��
        if($info = $this->isTrusteeship()) {
            $trusteeship = array('from_mid' => $this->info['mid'], 'from_mname' => $this->info['mname']);
			foreach(array('msid','onlineip','mslastactive','lastolupdate','errtimes','errdate',) as $var){
				if(isset($this->info[$var])) $info[$var] = $this->info[$var];//�̳в����ߵ�msid��session����
			}
            $this->info = $info;
            $memberid = $info['mid'];
            $this->info['atrusteeship'] = $trusteeship;//���ܱ��
        } else if(defined('M_MCENTER')){
            mclearcookie('trusteeship');
        }
	}


    /**
     * �ж��Ƿ�Ϊ�йܲ����������Ƿ���Ȩ��
     *
     * @return ������򷵻��йܻ�Ա��Ϣ�����򷵻�FALSE
     * @since  1.0
     */
    public function isTrusteeship()
    {
        global $from_mid, $g_apid;
        // Ŀǰ�й�ֻӦ���ڻ�Ա����
        if(!defined('M_MCENTER')) return false;
        if(!empty($from_mid)) {
            $from_mid = (int)$from_mid;
        } else {
            $trusteeship = self::TrusteeCookieInfo();//����cookie
            if($trusteeship)
            {
                $from_mid = intval($trusteeship['from_mid']);
            }
        }

        if(!empty($from_mid) && ($from_mid != $this->info['mid']))//�ڴ���$from_id�����cookie�������
        {
			$msg = '';
			$from_user = new cls_userinfo;
			$from_user->activeuser($from_mid,1);
			if(!$from_user->info['mid']){
				$msg = 'ָ������Ч��Ա';
			}elseif(empty($this->info['isfounder']) && $re = $from_user->noPm(@$g_apid)){//�û�Ա�������й�
				$msg = $re;
			}elseif(!$this->inTrusteeshipList(@$from_user->info['trusteeship']) && $this->NoBackFunc('trusteeship')) {//����Ȩ��
				$msg = '���ڴ���������������ɫû�д���Ȩ��';
			}
			if($msg){
				mclearcookie('trusteeship');
				_header();
				cls_message::show('��Ϊ����ԭ�����޷����ܸû�Ա���ģ�<br>'.$msg);
			}else{
				$trusteeship = array('from_mid' => $from_user->info['mid'], 'from_mname' => $from_user->info['mname']);
				msetcookie('trusteeship', serialize($trusteeship));//�������Ч��
				return $from_user->info;
			}
       }
        return false;
    }

    /**
     * �жϵ�ǰ��¼�û��Ƿ���������Ա���й��б���
     *
     * @param  string  $trusteeship ������Ա���й��б�
     * @return bool           ���б��з���TRUE�����򷵻�FALSE
     * @since  1.0
     */
    public function inTrusteeshipList($trusteeship = '')
    {
        if(!$trusteeship) return false;
        $mids = explode(',',$trusteeship);
        return in_array($this->info['mid'], $mids);
    }

    /**
     * ��ȡcookie�еĴ����û���Ϣ
     *
     * @return ��ȡ�ɹ������û�ID�����ƣ����򷵻� false
     * @since  1.0
     */
    public static function TrusteeCookieInfo()
    {
        global $m_cookie;
        if(!empty($m_cookie['trusteeship'])) {
            return unserialize(stripslashes($m_cookie['trusteeship']));
        }
        return false;
    }

    /**
     * ��ȡ�����û���Ϣ
     *
     * @return ��ȡ�ɹ������û�ID�����ƣ����򷵻� false
     * @since  1.0
     */
    public function getTrusteeshipInfo()
    {
		return empty($this->info['atrusteeship']) ? false : $this->info['atrusteeship'];
    }

    /**
     * ���������й�ѡ�л�Ա��Ϣ�Ļ�Ա
     *
     * @param  string $usernames �����йܵĻ�Ա����
     * @return bool             ���óɹ�����TRUE�����򷵻�FALSE
     *
     * @since  1.0
     */
    public function setTrusteeshipUser($usernames,$updatedb = 0)
    {
        global $g_apid;
        if($this->noPm($g_apid)) return false;
        $user_ids = array();
        // ����ǽ������
        if($users = array_filter(explode(',', (string) $usernames)))
        {
            foreach($users as $user)
            {
                if($id = self::getIdForName(trim($user))) $user_ids[] = $id;
            }
        }
		$this->updatefield('trusteeship',$user_ids ? implode(',', $user_ids) : '');
		$updatedb && $this->updatedb();
		return true;
    }

    /**
     * ͨ���û�����ȡ�û�ID
     *
     * @param  string ��Ա����
     * @return        �����û�ID
     *
     * @since  1.0
     */
    public static function getIdForName($user)
    {
        $user = addslashes($user);
        $user_info = self::getUserInfo('mid', array('mname'=>$user));
        return (int)$user_info['mid'];
    }
	
    /**
     * ͨ���û�ID��ȡ�û���
     *
     * @param  string ��Աid
     * @return        �����û���
     *
     * @since  1.0
     */
    public static function getNameForId($mid)
    {
		$mname = '';
        if($mid = max(0,intval($mid))){
			$info = self::getUserInfo('mname', "mid = $mid");
			if(!empty($info['mname'])) $mname = $info['mname'];
		}
        return $mname;
    }

    /**
     * ��ȡ�û���Ϣ
     *
     * @param  string $field Ҫ��ȡ���ֶ�
     * @param  string $where Ҫ��ȡ������
     * @param  bool   batch  �Ƿ�Ҫ��ȡ������Ϣ��TRUEΪ�ǣ�FALSEΪֻ��ȡ������Ϣ
     * @return object        ��ȡ�ɹ����ص�ǰ���ݿ�ָ�룬���򷵻�FALSE
     *
     * @since  1.1
     */
    public static function getUserInfo($field = '*', $where='', $batch = false)
    {
        $db = _08_factory::getDBO();
        $db->select($field)->from('#__members')->where($where)->exec();
        # ��ȡ�����û���Ϣ
        if($batch)
        {
            $datas = array();
            while ( $row = $db->fetch() )
            {
                $datas[] = $row;
            }
            return $datas;
        }
        
        return $db->fetch();
    }

    /**
     * �Ի�Ա���Ʒ�ʽ�ж��Ƿ�������Ա
     *
     * @param  string $user       ��Ա����
     * @return int                ���ش��ڵ��������������򷵻� 0
     *
     * @since  1.0
     */
    public static function checkUserName($user)
    {
        $checked = self::getUserInfo('COUNT(*) AS num', array('mname'=>$user));
        return (int)$checked['num'];
    }


	function vsrecord(){
		global $vs_holdtime,$db,$tblprefix,$timestamp,$m_cookie;
		$vs_holdtime = empty($vs_holdtime) ? 0 : max(0,min(300,intval($vs_holdtime)));
		if(empty($vs_holdtime) || empty($this->info)) return;
        if ( empty($this->info['msid']) )
        {
            $this->info['msid'] = @$m_cookie['msid'];
        }
		$db->insert( '#__visitors', 
			array(
				'url' => 'http://'.M_SERVER.M_URI, 
				'robot' => ISROBOT ? 1 : 0, 
				'msid' => $this->info['msid'], 
				'onlineip' => $this->info['onlineip'], 
				'useragent' => @$_SERVER['HTTP_USER_AGENT'], 
				'mid' => $this->info['mid'],
				'mname' => $this->info['mname'],
				'createdate' => TIMESTAMP,
			)
    	)->exec();
		if(!($timestamp % 10)) $db->query("DELETE FROM {$tblprefix}visitors WHERE createdate<'".($timestamp - 60 * $vs_holdtime)."'");
		return;
	}
	function updatesession(){
		global $m_cookie,$onlinetimecircle,$db,$tblprefix,$timestamp,$onlineip,$maxerrtimes,$minerrtime;
		static $sessionupdated;
		if($sessionupdated || !defined('M_UPSEN')) return;
		$onlinetimecircle || $onlinetimecircle = 10;
		if($onlinetimecircle && $this->info['mid'] && $timestamp - $this->info['lastolupdate'] > $onlinetimecircle * 60){//��һ��ʱ�佫�������ʱ��д���Ա��
			$lastolupdate = $timestamp;
			$db->query("UPDATE {$tblprefix}members SET lastactive='{$this->info['mslastactive']}' WHERE mid='{$this->info['mid']}'");
		}else $lastolupdate = $this->info['lastolupdate'];
		if(!empty($m_cookie['msid'])){
			if($db->result_one("SELECT 1 FROM {$tblprefix}msession WHERE msid='{$this->info['msid']}'")){
				$db->query("UPDATE {$tblprefix}msession SET
					  mid='{$this->info['mid']}',
					  mname='{$this->info['mname']}',
					  onlineip='$onlineip',
					  mslastactive='$timestamp',
					  lastolupdate='$lastolupdate'
					  WHERE msid='{$this->info['msid']}'");
			}else{
				$db->query("INSERT INTO {$tblprefix}msession (msid,onlineip,mid,mname,mslastactive) VALUES
					  ('{$this->info['msid']}','$onlineip','{$this->info['mid']}','{$this->info['mname']}','$timestamp')", 'SILENT');
				if($this->info['mid'] && $timestamp - $this->info['lastactive'] > 21600){
					$db->query("UPDATE {$tblprefix}members SET lastip='$onlineip',lastactive='$timestamp' WHERE mid='{$this->info['mid']}'");
				}
			}
			if($maxerrtimes && $this->info['errtimes'] && ($timestamp - $this->info['errdate'] > $minerrtime * 60)){
				$db->query("UPDATE {$tblprefix}msession SET errtimes=0,errdate=0 WHERE msid='{$this->info['msid']}'");
				$this->info['errtimes'] = 0;
			}
		}
		$sessionupdated = 1;
	}
    
    /**
     * ��¼ǰԤ��⣬����û�����������ֹ
     * 
     * @param string $callbackurl ������ַ����������˸ò�����᷵�ظ���ַ
     */
    public function loginPreTesting( $callbackurl = '' )
    {
        if($this->_mconfigs['maxerrtimes'] && $this->info['errtimes'] >= $this->_mconfigs['maxerrtimes'])
        {        
            $this->showLoginMessage('��¼���Թ�����ʱ����������'.
                                    ($this->_mconfigs['minerrtime'] - intval(($this->_timestamp - $this->info['errdate']) / 60)) . 
                                    '���Ӻ��ٵ�¼��', $callbackurl);
        }
    }
    
    /**
     * ��¼�ɹ������ô���SESSION��¼
     */
    public function resetErrorMsession()
    {
        if(empty($this->info['msid'])) return;
        
        if ( $this->_mconfigs['maxerrtimes'] )
        {
            $this->_db->update('#__msession', array('errtimes' => 0, 'errdate' => 0))
                      ->where(array('msid' => $this->info['msid']))->exec();
        }
    }
    
    /**
     * ��¼ʧ�ܴ���
     * 
     * @param string $username    ��ǰ��¼���û���
     * @param string $password    ��ǰ��¼���û�����
     * @param string $callbackurl ������ַ����������˸ò�����᷵�ظ���ַ
     * 
     * @since 1.0
     */
    public function loginFailureHandling( $username = '',$password = '', $callbackurl = '' )
    {
        $maxerrtimes = (int) $this->_mconfigs['maxerrtimes'];
        $timestamp = $this->_timestamp;
        
		$password = preg_replace("/^(.{".round(strlen($password) / 4)."})(.+?)(.{".round(strlen($password) / 6)."})$/s", "\\1***\\3", $password);
		record2file('badlogin',mhtmlspecialchars($timestamp."\t".stripslashes($username)."\t".$password."\t".$this->_onlineip));
		if( $maxerrtimes && !empty($this->info['msid']) )
        {
		    $x=intval($this->info['errtimes']);
            $this->_db->update('#__msession', array('errtimes' => $x+1, 'errdate' => $timestamp))
                      ->where(array('msid' => $this->info['msid']))->exec();
			$num = $maxerrtimes - $this->info['errtimes'] - 1;
            
            if ( $num > 0 )
            {
                $msg =  "���ĵ�¼��Ϣ���󣬻����Գ��� $num �Σ�";
            }
            else
            {
            	$msg =  '�����¼��'.$maxerrtimes.'�Σ�'.($this->_mconfigs['minerrtime'] - intval(($timestamp - $this->info['errdate']) / 60)).'�������벻Ҫ�ٳ��ԡ�';
            }
		}
        else
        {
            $msg = '��Ա��¼ʧ�ܡ�';
        }
        
        $this->showLoginMessage($msg, $callbackurl);
    }
    
    /**
     * ��ӡ��¼ʱ�����Ϣ
     * 
     * @param string $message     Ҫ��ӡ�������Ϣ
     * @param string $callbackurl ��ӡ�󷵻ص�URL
     * 
     * @since 1.0
     */
    private function showLoginMessage( $message, $callbackurl = '' )
    {
        $callbackurl = trim($callbackurl);
        if ( false !== stripos($callbackurl, 'javascript_alert') )
        {
            $javascripts = substr($callbackurl, 17);
            cls_message::show($message, 'javascript:(function(){ alert(\'' . $message . '\'); ' . $javascripts . '})();');
            
        }
        else
        {
        	cls_message::show($message, $callbackurl);
        }        
    }
    
    // �ú�����ʱ�����������ݣ��Ժ������ֱ�ӵ������Ϻ���
	function logincheck($mode = 0,$username = '',$password = ''){
		switch($mode){
			case 0://��¼ǰ
				$this->loginPreTesting();
			break;
			case 1://��¼�ɹ�
				$this->resetErrorMsession();
			break;
			case -1://��¼ʧ��
				$this->loginFailureHandling($username, $password);
			break;
		}
	}
	
	//���ڽ���һ����Ա�����Ϻϲ�����ǰ��Ա
	function merge_user($mname = ''){
		if(!$mname) return $this->info['mid'];
		$auser = new cls_userinfo;
		$auser->activeuserbyname($mname);
		$this->info = array_merge($this->info,$auser->info);
		unset($auser);
		return $this->info['mid'];
	
	}	
	
	function activeuserbyname($mname,$detail = 0,$ttl = 0){
		global $db,$tblprefix;
		$this->init();
		if($mname && $this->info = $db->fetch_one("SELECT m.*,s.* FROM {$tblprefix}members m INNER JOIN {$tblprefix}members_sub s ON s.mid=m.mid WHERE mname='$mname'",$ttl)){
			$this->subed = 1;
			$detail && $this->detail_data($ttl);
		}else{
			$this->info = self::nouser_info();
		}
		$this->info['mspacehome'] = cls_Mspace::IndexUrl($this->info);
		$this->groupclear(1);
		return $this->info['mid'];
	}
	function activeuser($mid,$detail=0,$ttl = 0){
		global $db,$tblprefix;
		$this->init();
		if($mid && $this->info = $db->fetch_one("SELECT m.*,s.* FROM {$tblprefix}members m INNER JOIN {$tblprefix}members_sub s ON s.mid=m.mid WHERE m.mid='$mid'",$ttl)){
			$this->subed = 1;
			$detail && $this->detail_data($ttl);
		}else{
			$this->info = self::nouser_info();
		}
		$this->info['mspacehome'] = cls_Mspace::IndexUrl($this->info);
		$this->groupclear(1);
		return $this->info['mid'];
	}
	public static function nouser_info(){
		$sysparams = cls_cache::cacRead('sysparams');
		return $sysparams['nouser'];
	}	
	
	function useradd($mname = '',$password = '',$email = '',$mchid = 0){
		global $db,$tblprefix,$timestamp,$onlineip;
		if(!$mname || !$mchid) return 0;
        $salt = cls_string::Random(6);
		$db->query("INSERT INTO {$tblprefix}members SET mname='$mname',password='$password',email='$email',mchid='$mchid',regdate='$timestamp',regip='$onlineip',lastvisit='$timestamp', salt = '$salt'");
		if(!($mid = $db->insert_id())){
			return 0;
		}else{
			$db->query("INSERT INTO {$tblprefix}members_sub SET mid='$mid'");
			$db->query("INSERT INTO {$tblprefix}members_$mchid SET mid='$mid'");
			$this->info = $db->fetch_one("SELECT m.*,s.* FROM {$tblprefix}members m INNER JOIN {$tblprefix}members_sub s ON s.mid=m.mid WHERE m.mid='$mid'");
			$this->info['mspacehome'] = cls_Mspace::IndexUrl($this->info);
			$this->subed = 1;
			$this->detail_data();
			
			//���¸��²���Ҫ��ʱ���µ����ݿ⣬��Ϊִ���������֮�󣬺���������ͳһ��updatedb����
			$this->InitCurrency();
			$this->groupinit();	
			$this->updatefield('mtcid',($mtcid = array_shift(array_keys($this->mtcidsarr()))) ? $mtcid : 0);
			return $mid;
		}
	}
	function sub_data($ttl = 0){
		global $db,$tblprefix;
		if(empty($this->info['mid'])) return;
		if($this->subed) return;
		if($member = $db->fetch_one("SELECT * FROM {$tblprefix}members_sub WHERE mid=".$this->info['mid'],$ttl)) $this->info = array_merge($this->info,$member);
		unset($member);
		$this->subed = 1;
	}
	function detail_data($ttl = 0){
		global $db,$tblprefix;
		if(empty($this->info['mid']) || $this->detailed) return;
		!$this->subed && $this->sub_data();
		if($r = $db->fetch_one("SELECT * FROM {$tblprefix}members_{$this->info['mchid']} WHERE mid='".$this->info['mid']."'",$ttl)){
			$this->info = array_merge($r,$this->info);
			unset($r);
		}
		$this->detailed = 1;
	}
	function check($check=1,$updatedb=0){//$checkִ����˻����Ĳ���
		if(!$this->info['mid'] || $this->info['checked'] == $check) return;
		if(!$check && $this->info['isfounder']) return;
		$this->updatefield('checked',$check);
		$updatedb && $this->updatedb();
	}
	function autopush(){ //�Զ�����
		$pa = cls_pusher::paidsarr('members',$this->info['mchid']);
		foreach($pa as $paid=>$paname){ 
			$pusharea = cls_PushArea::Config($paid);
			if(!empty($pusharea['autopush'])){ //���÷���ֵ
				cls_pusher::push($this->info,$paid,21); 
			}
		}
	}
	
	// ����ע���ֻ�������֤��(���Զ���֤:��Ա��֤-�ֻ���֤)
	function automcert($smstelfield,$smstelval){ 
		$db = _08_factory::getDBO();
		$tblprefix = cls_env::getBaseIncConfigs('tblprefix');
		$timestamp = TIMESTAMP;
		$mctypes = cls_cache::Read('mctypes');
		$msgcode = cls_env::GetG('msgcode');
		$memberid = $this->info['mid'];
		$mctid = 0;
		foreach($mctypes as $k => $v){
			if($v['field']==$smstelfield && $v['mode']==1){
				$mctype = $v;
				$mctid = $k;
				break;
			}
		} 
		if(!$mctid) return;
		$db->query("INSERT INTO {$tblprefix}mcerts SET mid='$memberid',mname='{$this->info['mname']}',mctid='$mctid',createdate='$timestamp',checkdate='$timestamp',content='$smstelval',msgcode='$msgcode'");
		if($mcid = $db->insert_id()){
			//if($mctype['autocheck']){
				$this->updatefield("mctid$mctid",$mctid); //ֱ�����
				if($mctype['award']) $this->updatecrids(array($mctype['crid'] => $mctype['award']),0,"$mctype[cname] �ӷ�");
				$this->updatedb();
			//}
		}
	}	
	/**
	 * ɾ���û���
	 *
	 */
	function delete(){
		global $db,$tblprefix;
		if(!$this->info['mid'] || $this->info['isfounder']) return false;
		/********** extend_example/libs/xxxx/userinfo.cls.php��ͬ����������չ����(��Ҫ����,����ĵ�������) ***************/
		$this->_delete();
		return true;
	}
	function _delete(){
		global $db,$tblprefix,$mspacedir;
		$mid = $this->info['mid']; 
		
		// ɾ��-���б�
		$db->query("DELETE FROM {$tblprefix}webcall      WHERE mid='$mid'",'UNBUFFERED'); // 400�绰
		$db->query("DELETE FROM {$tblprefix}pms          WHERE fromid='$mid' OR toid='$mid'",'UNBUFFERED'); // վ�ڶ���(�շ�)
		$db->query("DELETE FROM {$tblprefix}sms_sendlogs WHERE mid='$mid' ",'UNBUFFERED'); // ���ŷ��ͼ�¼
		
    	//����-��̬�ļ�
		$dir  = $db->result_one("SELECT mspacepath FROM {$tblprefix}members WHERE mid='$mid'");
    	if(!_08_FileSystemPath::CheckPathName($dir)) clear_dir(M_ROOT.$mspacedir.'/'.$dir,true);
		
		//ɾ����Ա�����������Ϣ
		cls_pusher::DelelteByFromid($this->info['mid'],'members');
		
		/* // ɾ��-���� (�ĵ��ĸ����Ѿ�ɾ������Ҫ�ǻ�Ա���ϵȸ���) ??? 
		if($iskeep){
			$query = $db->query("SELECT * FROM {$tblprefix}userfiles WHERE mid='$mid'");
			while($r = $db->fetch_array($query)){
				atm_delete($r['url'],$r['type']);
			} 
			$db->query("DELETE FROM {$tblprefix}userfiles WHERE mid='$mid'",'UNBUFFERED'); 
		}*/
		
		// ɾ��-��Ա�� 
		$db->query("DELETE FROM {$tblprefix}members_{$this->info['mchid']} WHERE mid='$mid'",'UNBUFFERED');
		$db->query("DELETE FROM {$tblprefix}members_sub WHERE mid='$mid'",'UNBUFFERED');
		$db->query("DELETE FROM {$tblprefix}members WHERE mid='$mid'",'UNBUFFERED');
		// 
		$this->init();
	}
	function push($paid){
		if(cls_pusher::SourceNeedAdv($paid)){
			$this->detail_data();
		}
		return cls_pusher::push($this->info,$paid);
	}
	
	function handgroup($gtid,$ugid=0,$endstamp=-1,$updatedb = 0){//-1����Ա����Ч��0������>0ʵ������ʱ��
		global $timestamp;
		$grouptypes = cls_cache::Read('grouptypes');
		if(!$this->info['mid'] || empty($grouptypes[$gtid]) || $grouptypes[$gtid]['mode'] > 1) return;
		$mchid = $this->info['mchid'];
		if($ugid && !in_array($mchid,explode(',',$grouptypes[$gtid]['mchids']))){
			$usergroups = cls_cache::Read('usergroups',$gtid);
			if(in_array($mchid,explode(',',$usergroups[$ugid]['mchids'])) && ($endstamp <= 0 || $endstamp > $timestamp)){
				$this->updatefield('grouptype'.$gtid,$ugid);
				$this->updatefield('grouptype'.$gtid.'date',$endstamp == -1 ? ($usergroups[$ugid]['limitday'] ? ($timestamp + $usergroups[$ugid]['limitday'] * 86400) : 0) : $endstamp);
			}else $ugid = 0;
		}else $ugid = 0;
		if(!$ugid){
			$this->updatefield('grouptype'.$gtid,0);
			$this->updatefield('grouptype'.$gtid.'date',0);
		}
		$updatedb && $this->updatedb();
	}
	function mtcidsarr(){
		$na = cls_mtconfig::Config();
		$re = array();
		foreach($na as $k => $v){
			if((!$v['mchids'] || in_array($this->info['mchid'],explode(',',$v['mchids']))) && $this->pmbypmid($v['pmid'])) $re[$k] = $v['cname'];
		}
		return $re;
	}
	function isadmin(){
		if(!$this->info['mid'] || !$this->info['checked']) return false;
		return $this->info['grouptype2'] || $this->info['isfounder'];
	}
    
    /**
     * �жϵ�ǰ�û��Ƿ��¼
     * 
     * @return bool ����Ѿ���¼����TRUE�����򷵻�FALSE
     * @since  nv50
     */
    public function isLogin()
    {
        if (empty($this->info['mid']))
        {
            return false;
        }
        
        return (bool) $this->info['mid'];
    }
	
    /**
     * ���������ɫȨ�ޣ�ָ��$Type�򷵻�ָ�����͵�Ȩ�����飬���򷵻�������Ȩ�����顣
     */
	function aPermissions($Type = ''){
		$TypeArray = array('menus','funcs','caids','mchids','fcaids','cuids','checks','extends',);
		if(!$Type){
			if(empty($this->apms)){
				foreach($TypeArray as $var) $this->apms[$var] = !empty($this->info['isfounder']) ? array('-1') : array();
				if(empty($this->info['isfounder'])){
					$amconfigs = cls_cache::Read('amconfigs');
					$ausergroup = cls_cache::Read('usergroup',2,@$this->info['grouptype2']);
					$a_amconfig = array();
					
					//������ɫ�ۼ�Ȩ��
					if(($ids = @$ausergroup['amcids'].','.@$this->info['amcids']) && $ids = array_unique(array_filter(explode(',',$ids)))){
						foreach($ids as $v){
							if(!empty($amconfigs[$v])){
								foreach($amconfigs[$v] as $k => $z){
									if(empty($a_amconfig[$k])){
										$a_amconfig[$k] = $z;
									}elseif($z) $a_amconfig[$k] .= ",$z";
								}
							}
						}
					}
					if($a_amconfig){
						foreach($TypeArray as $var){
							if($a_amconfig[$var]){
								$this->apms[$var] = array_unique(explode(',',$a_amconfig[$var]));
							}else unset($this->apms[$var]);
						}
					}else $this->apms = array();
				}
			}
			return $this->apms;
		}else{
			if(!in_array($Type,$TypeArray)) return array();
			$this->aPermissions();
			return empty($this->apms[$Type]) ? array() : $this->apms[$Type];
		}
	}
	
	//�����̨���ݹ���Ȩ�ޣ�ʶ��ָ������id�����ݹ���Ȩ�ޣ�������Ȩ��ԭ��
	function NoBackPmByTypeid($typeid,$type = 'caid'){
		if($this->info['isfounder']) return '';
		$na = array('caid' => '��Ŀ','mchid' => '��Ա����','fcaid' => '��������','cuid' => '��������',);
		if(!isset($na[$type])) return 'ָ���˴��������Ȩ������';
		if(array_intersect(array(-1,$typeid),$this->aPermissions("{$type}s"))){
			return '';
		}else return self::NoBackMessage("ָ��{$na[$type]}");
	}
	
	//�����̨����Ȩ�ޣ�������Ȩ��ԭ��
	function NoBackFunc($name){
		if(!empty($this->info['isfounder'])) return '';
		if(array_intersect(array(-1,$name),$this->aPermissions('funcs'))){
			return '';
		}else{
			$amfuncs = cls_cache::exRead('amfuncs');
			$na = array();
			foreach($amfuncs as $k => $v){
				foreach($v as $k0 => $v0){
					$na[$k0] = $v0;
				}
			}
			return self::NoBackMessage(empty($na[$name]) ? '��ǰ��Ŀ' : "{$na[$name]}($name)");
		}
	}
	private static function NoBackMessage($content){
		$re = '��û�� "'.$content.'" �Ĺ����̨Ȩ�ޡ�<br>';
		$re .= '�����Ա(��ʼ�˻��̨Ȩ�޹���Ա)ͨ�����·�ʽ(֮һ)����Ȩ�����ã�<br>';
		$re .= "1) ���������ڵĹ����飬��Ϊ������������صĹ����ɫ��<a href=\"?entry=amembers&action=edit&isframe=1\" target=\"_blank\">>>����</a><br>";
		$re .= "2) ���������ڹ���������������ɫ��<a href=\"?entry=usergroups&action=usergroupsedit&gtid=2\" onclick=\"return floatwin('open_grouptypesedit',this)\">>>����</a><br>";
		$re .= "3) ���������ɫ����ϸ���ã�<a href=\"?entry=amconfigs&action=amconfigsedit&isframe=1\" target=\"_blank\">>>����</a><br>";
		return $re;
	}	
	function basedeal($dname,$mode=1,$count=1,$reason='',$updatedb=0){//��Ա����֮��Ļ��ֻ������ԵĴ���,$modeΪ1���0Ϊɾ��
		$currencys = cls_cache::Read('currencys');
		if(!$this->info['mid']) return;
		$crids = array();
		foreach($currencys as $k => $v){
			if($v['available'] && !empty($v['bases'][$dname])) $crids[$k] = $mode ? $count * $v['bases'][$dname] : -$count * $v['bases'][$dname];
		}
		$crids && $this->updatecrids($crids,$updatedb,$reason ? $reason : '������������');
	}
	function paydeny($aid,$isatm=0){//$isatmΪ1����ʾΪ����
		$grouptypes = cls_cache::Read('grouptypes');
		if(empty($this->info['mid'])) return false;
		foreach($grouptypes as $gtid => $grouptype){//��Ѷ���
			if(!$grouptype['forbidden'] && !empty($this->info['grouptype'.$gtid])){
				$usergroup = cls_cache::Read('usergroup',$gtid,$this->info['grouptype'.$gtid]);
				if(!empty($usergroup['deny'.($isatm ? 'atm' : 'arc')])) return true;
			}
		}
		if($db->result_one("SELECT COUNT(*) FROM {$tblprefix}subscribes WHERE aid='$aid' AND mid='".$this->info['mid']."' AND isatm='$isatm'")) return true;
		return false;
	}
	function payrecord($aid,$isatm=0,$cridstr='',$updatedb=0){
		global $db,$tblprefix,$timestamp;
		if(empty($this->info['mid'])) return;
		$db->query("INSERT INTO {$tblprefix}subscribes SET
				mid='".$this->info['mid']."',
				mname='".$this->info['mname']."',
				aid='$aid',
				cridstr='$cridstr',
				isatm='$isatm',
				createdate='$timestamp'");
	}
	function checkforbid($var){//��ֹ����flase
		if(!$var || empty($this->info['grouptype1']) || !($usergroup = cls_cache::Read('usergroup',1,$this->info['grouptype1']))) return true;
		return in_array($var,explode(',',$usergroup['forbids'])) ? false : true;
	}
	function check_allow($var){
		$grouptypes = cls_cache::Read('grouptypes');
		if(!$var || !$this->info['mid']) return 0;
		if($this->info['isfounder']) return 1;
		foreach($grouptypes as $k => $v){
			if(!$v['forbidden'] && $this->info["grouptype$k"] && $usergroup = cls_cache::Read('usergroup',$gtid,$this->info['grouptype'.$gtid])){
				if(in_array($var,explode(',',$usergroup['allows']))) return 1;
			}
		}
		return 0;
	}

	// �����ж��ĵ�/�����ȵ��Զ����
	// 0:���Զ����,1:�Զ����,����:Ȩ�޷���
	function pmautocheck($pmid=0){
		return $pmid < 0 ? $this->pmbypmid(-$pmid) : $pmid;
	}

	//��ʹ��pmbypmid�滻��Ϊ�˼��ݾɰ汾����ʱ����������
	function pmbypmids($pname,$pmid=0){
		return mem_pmbypmid($this->info,$pmid);
	}

	//����Ȩ�޷�������Ȩ�ޣ�ֻ����true(��Ȩ��)/false(��Ȩ��)����ʹ��noPm�ɷ�����Ȩ�޵�ԭ��
	function pmbypmid($pmid=0){
		return mem_pmbypmid($this->info,$pmid);
	}
	//����Ȩ�޷�������Ȩ�ޣ�����Ȩ��ʱ������ԭ����Ȩ���򷵻�false
	function noPm($pmid = 0){
		return cls_Permission::noPmReason($this->info,$pmid);
	}

	//ȷ�ϵ�ǰ��Ա�Ƿ���Ȩ����ָ����Ŀ($sarr)���ָ��ģ��($chid)�ĵ�
	//����Ȩ��ʱ������ԭ�򣬷��򷵻�false
	function arcadd_nopm($chid,$sarr=array()){
		if(!$this->checkforbid('issue')) return '�Բ�������������';
		if(!$chid || !($channel = cls_channel::Config($chid))) return '��ָ��Ҫ�������ĵ�����';
		if($re = $this->noPm($channel['apmid'])) return $re;
		foreach($sarr as $k =>$v){
			if($k == 'caid'){
				if(!$a = cls_cache::Read('catalog',$v)) return '��ָ��Ҫ�������ĸ���Ŀ';
				if($a['isframe']) return '��Ŀ['.$a['title'].']�в��ܷ����ĵ�';
				if(!in_array($chid,explode(',',$a['chids']))) return $channel['cname'].'���ܷ�������Ŀ['.$a['title'].']';
			}elseif($coid = intval(str_replace('ccid','',$k))){
				$cotypes = cls_cache::Read('cotypes');
				if(empty($cotypes[$coid]) || $cotypes[$coid]['self_reg']) continue;
				if(!empty($sarr["ccid$coid"]) && $ccids = array_filter(explode(',',$sarr["ccid$coid"]))){
					foreach($ccids as $x){
						if(!($a = cls_cache::Read('coclass',$coid,$x))) return 'ָ����['.$cotypes[$coid]['cname'].']���಻����';
						if($a['isframe']) return '����['.$a['title'].']�в��ܷ����ĵ�';
						if(!in_array($chid,explode(',',@$a['chids']))) return $channel['cname'].'���ܷ���������['.$a['title'].']';
					}
				}
			}
		}
	}
	
	//����˵Ļ�Ա�ڱ�ϵͳ�����õ�¼��Ǽ���¼��
	//$expires����¼��Ч����(��)
	public function OneLoginRecord($expires = 0,$updatedb = true){
		global $timestamp,$onlineip,$memberid,$client_t;//$client_tΪ�ͻ���ʱ��
		if(!$this->info['mid'] || $this->info['checked'] != 1) return false;
		$this->updatefield('lastvisit', $timestamp);
		$this->updatefield('lastip', $onlineip);
        # ��֤ÿ���û�����Ҫ���Լ���salt
        if ( empty($this->info['salt']) )
        {
            $this->updatefield('salt', cls_string::Random(6));
        }
		$updatedb && $this->updatedb();
		
		if(!($expires = empty($expires) ? 0 : intval($expires))) $expires = 365 * 86400;
		$expires > 0 && !empty($client_t) && $expires = intval(floatval($client_t) / 1000) - $timestamp + $expires;
		$expires < 0 && $expires = 0; 
		cls_userinfo::LoginFlag($this->info['mid'],$this->info['password'],$expires);
		$memberid = $this->info['mid'];
		$this->resetErrorMsession();
	}				
	
	//���õ�¼���
	public static function LoginFlag($mid = 0,$md5_password = '',$expires = 31536000){
		if(!$mid || !$md5_password) return;
		msetcookie('userauth', authcode("$md5_password\t$mid",'ENCODE'),$expires);
	}
	
	//�����˳����
	public static function LogoutFlag(){
	    global $target;
        
        mclearcookie('trusteeship');
        # ֻ�˳������û�
        if ( $target === 'atrusteeship' )
        {
            return true;
        }
        
        # �����QQ��¼ʱͬʱ�˳�QQ��¼
        if ( isset($_SESSION['openid']) )
        {
            unset($_SESSION['openid']);
        }
        
        $hash = cls_env::getHashValue();
        # �����΢�ŵ�¼ʱͬʱ�˳�΢�ŵ�¼
        if ( isset($_SESSION[$hash]) )
        {
            unset($_SESSION[$hash]);
        }
        
		mclearcookie();
	}
	
	//��¼ʱ�ϲ���ͬ��UC����ϵͳ��Ա��ͬʱͬ����¼��UC
	//֮ǰ��Ҫ����mname,password�ļ��
	//���س�����Ϣ
	public function UCLogin($mname,$password,$_ucre = array()){
		$re = '';
		if(!$mname || !$password) return '�������ʺż�����';
		$_ucre = cls_ucenter::checklogin($mname,$password);//UC��¼��Ԥ���//??��UC�뱾ϵͳ��ͬ����������UCͨ�������Ļ����������ٵ�¼
		if(!empty($_ucre['error'])) return $_ucre['error'];//��UC����֤ͨ�����Ļ�����ֹ���е�¼����
		if(isset($_ucre['uid'])){
			$md5_password = _08_Encryption::password($password);
			if($_ucre['uid'] == -1){//��ϵͳ�иû�Ա����UC�в����ڣ�����UC��ע��һ���»�Ա
				if($this->info['mid'] && $md5_password == $this->info['password']){
					cls_ucenter::register($mname,$password,$this->info['email'],TRUE);//ע�Ტͬ����¼
				}
			}elseif($_ucre['uid'] > 0){//��UC��ͨ���˻�Ա��֤
                $user = new cls_UserbaseDecorator($this);
				if($this->info['mid']){//ʹ��UC�е��ʺ���������±�ϵͳ
					$user->synUpdateLocalData($password, $_ucre['email']);
				}else{//�ڱ�ϵͳ���һ���»�Ա
					$user->synAddLocalUser($mname, $password, $_ucre['email']);
				}
                unset($user);
				cls_ucenter::login($_ucre['uid']);//ִ��ͬ����¼
			}
		}
		return $re;
	}
	
	//Ԥ����mname,password,email��
	//opmode����ģʽ:add(������Ա)/edit(�޸Ļ�Ա)/login(��Ա��¼)
	public static function CheckSysField($value,$type = 'mname',$opmode = 'add', $mid = 0){
		global $db,$tblprefix,$censoruser,$mcharset, $unique_email;
		$re = array('value' => $value,'error' => '');
		switch($type){
			case 'mname':
				if($opmode == 'edit') return self::_returnError('�ʺŲ������޸�',$re);
				$re['value'] = $value = empty($value) ? '' : trim(strip_tags($value));
				if(empty($value)) return self::_returnError('�ʺŲ���Ϊ��',$re);
				$_len = cls_string::CharCount($value);// ����ϵͳ�������ǰϵͳ��ͬʱ, ����ת��Ϊ��ǰϵͳ����
				if($_len < 3 || $_len > 15) return self::_returnError('�ʺų���ӦΪ3-15�ֽ�',$re);
				if($opmode != 'login'){//ע������ӻ�Աʱ��Ҫ�����
					$guestexp = '\xA1\xA1|^Guest|^\xD3\xCE\xBF\xCD|\xB9\x43\xAB\xC8';
					if(preg_match("/^\s*$|^c:\\con\\con$|[%,\*\"\s\t\<\>\&]|$guestexp/is",$value)) return self::_returnError('�ʺŲ��Ϲ淶',$re);
					if(!defined('M_ADMIN') && $censoruser){//�����̨������ӱ���ֹ���ʺ�
						$censorexp = '/^('.str_replace(array('\\*', "\r\n", ' '), array('.*', '|', ''), preg_quote(($censoruser = trim($censoruser)), '/')).')$/i';
						if($censoruser && @preg_match($censorexp,$value)) return self::_returnError('�ʺű���ֹʹ��',$re);
					}
					if($msg = cls_ucenter::checkname($value)) return self::_returnError($msg,$re);
                    # ��֤WINDID������û���
                    if((int)($code = cls_WindID_Send::getInstance()->checkUserInput($value, 1)) < 0)
                    {
                        return self::_returnError( cls_Windid_Message::get($code), $re );
                    }
					if($db->result_one("SELECT mid FROM {$tblprefix}members WHERE mname='$value'")) return self::_returnError('�ʺ��ѱ�ע����',$re);
				}
			break;
			case 'password':
				$re['value'] = $value = empty($value) ? '' : trim($value);
				if(!$value) return $opmode == 'edit' ? $re : self::_returnError('����������',$re);//�޸�ģʽ�£�����Ϊ�ձ�ʾ�����޸ģ��������ؽ��
				if($opmode != 'login'){
					$_len = cls_string::CharCount($value);//???����ϵͳ�������ǰϵͳ��ͬʱ
					if($_len > 15) return self::_returnError('���볤��ӦС��15�ֽ�',$re);
					if($value != addslashes($value)) return self::_returnError('���벻�Ϲ淶',$re);
                    # ��֤WINDID���������
                    if((int)($code = cls_WindID_Send::getInstance()->checkUserInput($value, 2)) < 0)
                    {
                        return self::_returnError( cls_Windid_Message::get($code), $re );
                    }
				}
			break;
			case 'email':
				$re['value'] = $value = empty($value) ? '' : trim($value);
				if(empty($value)) return self::_returnError('������Email',$re);
                
                # һ������ֻ��ע��һ���û�
                if ( empty($unique_email) )
                {
                    $mid = @intval($mid);					
                    $db->select('mid')->from('#__members')->where(array('email' => $value));
                    if( $mid && ($opmode == 'edit') )
                    {
						$db->_and("mid != {$mid}");
                    }
					$uid = $db->exec()->fetch();
                    if ( $uid )
                    {
                         return self::_returnError('�������Ѿ��������û�ʹ�ã�',$re);
                    }
                }
                
                # ��֤WINDID�����Email
                if((int)($code = cls_WindID_Send::getInstance()->checkUserInput($value, 3)) < 0)
                {
                    return self::_returnError( cls_Windid_Message::get($code), $re );
                }
				if(!cls_string::isEmail($value)) return self::_returnError('Email���Ϲ淶',$re);
			break;
		}
		return $re;
	}
	protected static function _returnError($error,$re = array()){
		$re['error'] = $error;
		return $re;
	}
	
	//�������·��ͼ����ʼ���url
	public static function SendActiveEmailUrl($mname,$email,$forward = ''){
		global $cms_abs;
		if(empty($mname) || empty($email)) return '';
		$re = $cms_abs.'tools/memactive.php?action=sendemail';
		$re .= '&mname='.rawurlencode($mname);
		$re .= '&email='.rawurlencode($email);
		$forward && $re .= '&forward='.rawurlencode($forward);
		return $re;
	}
	
	//��ָ����Ա���ͼ����ʼ����ʼ�������ָ������������
	//info����Ҫ��mid,mname,email
	public static function SendActiveEmail($info = array()){
		global $timestamp,$cms_abs,$db,$tblprefix;
		if(empty($info['mid']) || empty($info['mname']) || empty($info['email'])) return;
		$confirmid = cls_string::Random(6);
		$db->query("UPDATE {$tblprefix}members_sub SET confirmstr='$timestamp\t2\t$confirmid' WHERE mid='{$info['mid']}'");
		$db->query("UPDATE {$tblprefix}members SET checked='2' WHERE mid='{$info['mid']}'");
		mailto($info['email'],'member_active_subject','member_active_content',array(
		'mid' => $info['mid'],'mname' => $info['mname'],'url' => "{$cms_abs}tools/memactive.php?action=emailactive&mid={$info['mid']}&confirmid=$confirmid")
		);
	}
	
	//ȷ�ϵ�ǰ��Ա�Ƿ���Ȩ����ָ����Ŀ($sarr)���ָ��ģ��($chid)�ĵ�
	//ֻ����true(��Ȩ��)/false(��Ȩ��)������Ҫ������Ȩ�޵�ԭ����ʹ�� arcadd_nopm
	function allow_arcadd($chid,$sarr=array()){
		return $this->arcadd_nopm($chid,$sarr) ? false : true;
	}
	function upload_capacity(){
		global $pm_upload,$nouser_capacity;
		if(!$this->info['mid']) return empty($nouser_capacity) ? 0 : $nouser_capacity;
		if($this->info['isfounder']) return -1;//��������
		if(!$this->checkforbid('upload') || !$this->pmbypmid(@$pm_upload)) return 0;
		$maxsize1 = 1;$maxsize2 = 0;
		$grouptypes = cls_cache::Read('grouptypes');
		foreach($grouptypes as $k => $v){
			if(!$v['forbidden'] && !empty($this->info["grouptype$k"])){
				$arr = cls_cache::Read('usergroup',$k,$this->info["grouptype$k"]);
				empty($arr['maxuptotal']) && $maxsize1 = 0;
				$maxsize2 = max($maxsize2,$arr['maxuptotal']);
			}
		}
		return empty($maxsize1) ? -1 : max(0,$maxsize2 * 1024 - $this->info['uptotal']);//�ռ�����(K)
	}
	function updateuptotal($upsize,$reduce=0,$updatedb=0){//$upsize��kΪ��λ
		if(!$this->info['mid']) return;
		$this->updatefield('uptotal',!$reduce ? ($this->info['uptotal'] + $upsize) : max(0,$this->info['uptotal'] - $upsize));
		$updatedb && $this->updatedb();
	}
	function saving($crid,$mode=0,$value = 0,$remark = ''){
		if(empty($value) || empty($this->info['mid'])) return;
		$this->updatecrids(array($crid => $mode ? -$value : $value),1,$remark,1);
	}
	function updatecrids($crids=array(),$updatedb=0,$remark='',$mode=0){//modeΪ1��ʾΪ�ֶ����
		global $db,$tblprefix,$timestamp;
		if(empty($this->info['mid'])) return;
		$currencys = cls_cache::Read('currencys');
		if(empty($crids) || !is_array($crids)) return;
		$curuser = cls_UserMain::CurUser();
		foreach($crids as $k => $v){
			if(!$v || ($k && empty($currencys[$k]))) continue;
			$nn = $this->info["currency$k"] + $v;
			$this->updatefield("currency$k",$nn > 0 ? $nn : 0);
			$db->query("INSERT INTO {$tblprefix}currency$k SET
					value='$v',
					mid='".$this->info['mid']."',
					mname='".$this->info['mname']."',
					fromid='".$curuser->info['mid']."',
					fromname='".$curuser->info['mname']."',
					createdate='$timestamp',
					mode='$mode',
					remark='".($remark ? $remark : '����ԭ��')."'");
		}
		$this->autogroup();
		$updatedb && $this->updatedb();
	}
	function crids_enough($crids=array()){
		if(empty($this->info['mid'])) return false;
		if(empty($crids)) return true;
		foreach($crids as $k => $v){
			if($v < 0 && $this->info['currency'.$k] < abs($v)) return false;
		}
		return true;
	}
	// ����[CONVMEMBER]����,���ڻ�Աģ��ת��(����)��
	function updatefield($fieldname,$newvalue,$tbl='members'){
		if(empty($this->info['mid'])) return false;
		if($tbl == 'members_sub' && !$this->subed){
			$this->sub_data();
		}elseif($tbl == "members_{$this->info['mchid']}" && !$this->detailed){
			$this->detail_data();
		}
		if(defined('CONVMEMBER') || $this->info[$fieldname] != stripslashes($newvalue)){
			$this->info[$fieldname] = stripslashes($newvalue);
			$this->updatearr[$tbl][$fieldname] = $newvalue;
			return true;
		}else return false;
	}
	function autogroup(){
		global $timestamp;
		if(!$this->info['mid']) return;
		$grouptypes = cls_cache::Read('grouptypes');
		foreach($grouptypes as $k => $v){
			if($v['mode'] == 2){
				$nid = 0;
				if(!in_array($this->info['mchid'],explode(',',$v['mchids']))){
					$arr = cls_cache::Read('usergroups',$k);
					foreach($arr as $x => $y){
						if($this->info['currency'.$v['crid']] >= $y['currency'] && in_array($this->info['mchid'],explode(',',$y['mchids']))){
							$nid = $x;
							break 1;
						}
					}
				}
				$nid == $this->info["grouptype$k"] || $this->updatefield("grouptype$k",$nid);
			}
			if($this->info["grouptype{$k}date"] && $this->info["grouptype{$k}date"] < $timestamp){
				$this->updatefield("grouptype$k",0);
				$this->updatefield("grouptype{$k}date",0);
			}
		}
		$this->groupclear();
	}
	function groupclear($updatedb = 0){
		global $timestamp;
		if(!$this->info['mid']) return;
		$grouptypes = cls_cache::Read('grouptypes');
		foreach($grouptypes as $k => $v){
			if($this->info["grouptype{$k}date"] && $this->info["grouptype{$k}date"] < $timestamp){
				$ovid = $ovday = 0;
				if($this->info["grouptype$k"] && ($oug = cls_cache::Read('usergroup',$k,$this->info["grouptype$k"])) && !empty($oug['overugid'])){
					if($nug = cls_cache::Read('usergroup',$k,$oug['overugid'])){
						$ovid = $oug['overugid'];
						$ovday = $oug['limitday'];
					}
				}
				$this->updatefield("grouptype$k",$ovid);
				$this->updatefield("grouptype{$k}date",$ovday ? ($timestamp + $ovday * 86400) : 0);
			}
		}
		$updatedb && $this->updatedb();
	}
	function InitCurrency(){
		$currencys = cls_cache::Read('currencys');
		$crids = array();foreach($currencys as $k => $v) $v['available'] && $v['initial'] && $crids[$k] = $v['initial'];
		$crids && $this->updatecrids($crids,0,'��Աע���ʼ���֡�');
	}	
	function InitMtcid(){
		$this->updatefield('mtcid',($mtcid = array_shift(array_keys($this->mtcidsarr()))) ? $mtcid : 0);
	}	
	function groupinit($updatedb = 0){
		global $timestamp;
		if(!$this->info['mid']) return;
		$grouptypes = cls_cache::Read('grouptypes');
		foreach($grouptypes as $k => $v){
			if(!$v['issystem'] && !$this->info['grouptype'.$k] && $v['mode'] != 2){
				if(!in_array($this->info['mchid'],explode(',',$v['mchids']))){
					$arr = cls_cache::Read('usergroups',$k);
					foreach($arr as $x => $y){
						if($y['autoinit'] && in_array($this->info['mchid'],explode(',',$y['mchids']))){
							$this->updatefield('grouptype'.$k,$x);
							$y['limitday'] && $this->updatefield('grouptype'.$k.'date',$timestamp + $y['limitday'] * 86400);
							break;
						}
					}
				}
			}else if(!$v['issystem'] && !$this->info['grouptype'.$k] && $v['mode'] == 2){
				$this->autogroup();
			}
		}
		$updatedb && $this->updatedb();
	}
	function nogroupbymchid(){//����Աģ�ͱ仯�󣬼��ԭ�ȵĻ�Ա���Ƿ���Ч
		if(!$this->info['mid']) return;
		$mchid = $this->info['mchid'];
		$grouptypes = cls_cache::Read('grouptypes');
		foreach($grouptypes as $k => $v){
			if($this->info["grouptype$k"]){
				if(!in_array($mchid,explode(',',$v['mchids']))){
					$ug = cls_cache::Read('usergroup',$k,$this->info["grouptype$k"]);
					if(in_array($mchid,explode(',',$ug['mchids']))) continue;//ֻ���������ά��ԭ��Ա��
				}
				$this->updatefield("grouptype$k",0);
				$this->updatefield("grouptype{$k}date",0);
			}
		}
	}
	
	function autoletter($updatedb=0){
		$mchannel = cls_cache::Read('mchannel',$this->info['mchid']);
		if(isset($mchannel['autoletter']) && $mchannel['autoletter']){
			$this->detail_data();
			if(isset($this->info[$mchannel['autoletter']]) && isset($this->info['letter'])){
				$this->updatefield('letter',autoletter($this->info[$mchannel['autoletter']]));
			}
		}
		$updatedb && $this->updatedb();
	}
	
	function updatedb(){
		global $db,$tblprefix;
		if(empty($this->info['mid'])) return;
		$this->autoletter();
		foreach(array('members','members_sub',"members_{$this->info['mchid']}") as $tbl){
			if(!empty($this->updatearr[$tbl])){
				$sqlstr = '';foreach($this->updatearr[$tbl] as $k => $v) $sqlstr .= ($sqlstr ? "," : "").$k."='".$v."'";
				$sqlstr && $db->query("UPDATE {$tblprefix}$tbl SET $sqlstr WHERE mid=".$this->info['mid']);
			}
		}
		$this->updatearr = array();
	}
    
    public function getter($name)
    {
        if (property_exists($this, $name))
        {
            return $this->$name;
        }
        
        return null;
    }
    
    /**
     * ��ȡ��Ա֧���ʺ���Ϣ
     * 
     * @param  int   $mid Ҫ��ȡ�Ļ�ԱID
     * @return array      ���ػ�ȡ����֧���ʺ���Ϣ
     */
    public function getPaysInfo( $mid, $type = 'alipay' )
    {
        $mid = max(1, (int) $mid);        
        $row = $this->_db->select('salt')->from('#__members')->where(array('mid' => $mid))->limit(1)->exec()->fetch(); 
        switch (strtolower($type))
        {
        	case 'alipay': // ֧����
                if ($mid === 1)
                {
                    if (!empty($this->_mconfigs['cfg_alipay_keyt']))
                    {
                        $this->_mconfigs['cfg_alipay_keyt'] = authcode($this->_mconfigs['cfg_alipay_keyt'], 'DECODE', $row['salt']);
                    }
                    
                    return array('alipay_partnerid' => @$this->_mconfigs['cfg_alipay_partnerid'], 
                                 'alipay_partnerkey' => @$this->_mconfigs['cfg_alipay_keyt'],
                                 'alipay_seller_account' => @$this->_mconfigs['cfg_alipay']);
                }
                else
                {                	
                    $rowPays = $this->_db->select('alipay_seller_account, alipay_partnerid, alipay_partnerkey')
                                         ->from('#__pays_account')
                                         ->where(array('id' => $mid))
                                         ->limit(1)
                                         ->exec()->fetch();       
                    $rowPays['alipay_partnerkey'] = authcode($rowPays['alipay_partnerkey'], 'DECODE', $row['salt']);
                }
                break;
            default : // �Ƹ�ͨ
                if ($mid === 1)
                {
                    if (!empty($this->_mconfigs['cfg_tenpay_keyt']))
                    {
                        $this->_mconfigs['cfg_tenpay_keyt'] = authcode($this->_mconfigs['cfg_tenpay_keyt'], 'DECODE', $row['salt']);
                    } 
                    return array('tenpay_partnerkey' => @$this->_mconfigs['cfg_tenpay_keyt'],
                                 'tenpay_seller_account' => @$this->_mconfigs['cfg_tenpay']);
                }
                else
                {
                    $rowPays = $this->_db->select('tenpay_seller_account, tenpay_partnerkey')
                                         ->from('#__pays_account')
                                         ->where(array('id' => $mid))
                                         ->limit(1)
                                         ->exec()->fetch();       
                    $rowPays['tenpay_partnerkey'] = authcode($rowPays['tenpay_partnerkey'], 'DECODE', $row['salt']);
                }
            	break;
        }
        
        return $rowPays;
    }
    
    public function __construct( $mchid = 0 )
    {
        global $timestamp, $onlineip;
        $this->_db = _08_factory::getDBO();
        $this->_mconfigs = cls_cache::Read('mconfigs');
        $this->_timestamp = $timestamp;
        $this->_onlineip = $onlineip;
    }
}
