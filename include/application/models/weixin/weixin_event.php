<?php
/**
 * ΢���¼���Ӧ������չģ��(�����¼�����)
 * {@link http://mp.weixin.qq.com/wiki/index.php?title=%E6%8E%A5%E6%94%B6%E4%BA%8B%E4%BB%B6%E6%8E%A8%E9%80%81}
 *
 * @author    Wilson <Wilsonnet@163.com>
 * @copyright Copyright (C) 2008 - 2013 08CMS, Inc. All rights reserved.
 */

defined('_08CMS_APP_EXEC') || exit('No Permission');
class _08_M_Weixin_Event extends _08_M_Weixin_Message
{
    /**
     * ȡ����ע�¼�
     * �û�ȡ����ע���ں��£�΢�Ż������¼����͵���������д��URL�����㿪���߸��û��·���ӭ��Ϣ�������ʺŵĽ�� 
     * 
     * @return string $message ����Ҫ�ظ���XML��ʽ����
     * @since  nv50
     */
    public function responseUnsubscribe()
    {     
    }
    
    /**
     * ��Ӧ��ע/ɨ���������ά���¼�
     * �û��ڹ�ע���ں��£�΢�Ż������¼����͵���������д��URL�����㿪���߸��û��·���ӭ��Ϣ�������ʺŵİ󶨡� 
     * 
     * @return string $message ����Ҫ�ظ���XML��ʽ����
     * @since  nv50
     */
    public function responseSubscribe()
    {
        # ɨ���������ά���¼�
//        if ( isset($this->_post->Ticket) && strtoupper($this->_post->Ticket) == 'TICKET' )
//        {
//            return $this->_ReplyText( "ɨ���������ά���¼�" );
//        }

        return $this->_ReplyText( "���ã���ӭ����ע {$this->_mconfigs['hostname']}��" );
    }
    
    /**
     * �û��ѹ�עʱ���¼�����
     * Ŀǰ֧�ֵ�¼��ע���¼���$eventKeyΪ1ʱ���¼��Ϊ2ʱ��ע�ᡣ
     * 
     * @return string $message ����Ҫ�ظ���XML��ʽ����
     * @since  nv50
     */
    public function responseScan()
    {
		global $onlineip;
        $cms_abs = _08_CMS_ABS;
        $time = TIMESTAMP;
        $weixin_token = _08_Encryption::password($this->_mconfigs['weixin_token']);
        $hash = cls_env::getHashValue();
		$eventKey = intval($this->_post->EventKey); //��ά�볡��ID
		$fromName = $this->_post->FromUserName; //΢��open_id(�û�ID)
		$db = _08_factory::getDBO();
		if (empty($this->_mconfigs['weixin_login_register'])){                           
            return $this->_ReplyText( '��Ǹ���ù�����ʱδ������' );
        }
		$token = _08_Encryption::getInstance("$fromName,{$weixin_token},{$time},{$hash}")->enCryption();
		$urlLoin = "{$cms_abs}login.php?action=weixin_login&is_weixin=1&token={$token}&scene_id=$eventKey";
		$message = "<a href=\"$urlLoin\">���������¼{$this->_mconfigs['hostname']}</a>";
		$row = $db->select('weixin_from_user_name')->from('#__members')->where(array('weixin_from_user_name'=>$fromName))->exec()->fetch();
		if($eventKey==parent::SCENE_ID_REGISTER && empty($row)){ //δע��,ɨ��ע��
			$username = $this->setUserName();
			$password = cls_string::Random(6);
			$email = ($username . '@' . @$this->_mconfigs['cms_top']);  
			$newuser = new cls_userinfo;
			$mchid = $autocheck = 1;
			if($mid = $newuser->useradd($username,_08_Encryption::password($password),$email, $mchid)){
				//UC��Աͬ��ע��
				$_ucid = cls_ucenter::register($username,$password,$email,TRUE);
				# ͬ��ע��ͨ��֤
				$pw_uid = cls_WindID_Send::getInstance()->synRegister($username, $password, $email, $onlineip);				
				$newuser->updatefield('weixin_from_user_name', $fromName);
				$newuser->check($autocheck);
				$newuser->updatedb();
				$_message = "{$this->_mconfigs['hostname']}\n��ӭ���ɹ�ע���˻���\n�û���: {$username} \n����: {$password} \n";
				$message = "$_message ���˻��Ѿ���΢���˻��ɹ��󶨣���֧��΢��ɨ���¼��\n $message ";
			}else{
				$message = "[{$username}] ע��ʧ�ܣ����Ժ����ԡ�";
			}
		}elseif($eventKey==parent::SCENE_ID_REGISTER){ //�Ѿ�ע��,ɨ��ע��
			$message = "���Ѿ�ɨ��ע�������ֱ��ɨ���¼��";
		}elseif($eventKey && $row){ //�Ѿ�ע��,ɨ���¼
			//;
		}elseif($eventKey){ //δע��,ɨ���¼(ִ�а�)
			$mobiledir = cls_env::mconfig('mobiledir');
			$urlLoin = "{$cms_abs}$mobiledir/login.php?action=login&weixinid=$fromName&scene_id=$eventKey"; //&token={$token}&
			$message = "����δ��΢��ɨ��ע�᣻<a href=\"$urlLoin\">���ת���ֻ����¼{$this->_mconfigs['hostname']}</a>"; 
			// ??? ������������¼ͬʱ��΢�ź�
		}
        return $this->_ReplyText( $message );
    }
    
    /**
     * ��Ӧ�ϱ�����λ���¼�
     * �û�ͬ���ϱ�����λ�ú�ÿ�ν��빫�ںŻỰʱ�������ڽ���ʱ�ϱ�����λ�ã����ڽ���Ự��ÿ5���ϱ�һ�ε���λ�ã�
     * ���ںſ����ڹ���ƽ̨��վ���޸��������á��ϱ�����λ��ʱ��΢�ŻὫ�ϱ�����λ���¼����͵���������д��URL�� 
     * 
     * @return string $message ����Ҫ�ظ���XML��ʽ����
     * @since  nv50
     */
    public function responseLocation()
    {
        if ( isset($this->_post->FromUserName) && (isset($this->_post->Event) && (strtoupper($this->_post->Event) == 'LOCATION')) )
        {            
            $datas = _08_M_Weixin_Base::getConfigs( $this->_post );
            $datas['FromUserName'] = (string) $this->_post->FromUserName;
            # ����λ��γ��
            isset($this->_post->Latitude) && $datas['Latitude'] = floatval($this->_post->Latitude);
            # ����λ�þ���
            isset($this->_post->Longitude) && $datas['Longitude'] = floatval($this->_post->Longitude);
            # ����λ�þ���
            isset($this->_post->Precision) && $datas['Precision'] = floatval($this->_post->Precision);          
            _08_M_Weixin_Base::setConfigs( $datas, $this->_post );  
        }
    }
    
    /**
     * ��Ӧ����¼����������û�����İ�ť��Ӧ��Ӧ�Ļظ���
     * �û�����Զ���˵�������˵���ť����Ϊclick���ͣ���΢�Ż�Ѵ˴ε���¼����͸������ߣ�ע��view���ͣ���ת��URL���Ĳ˵���������ϱ��� 
     * 
     * @return string $message ����Ҫ�ظ���XML��ʽ����
     * @since  nv50
     */
    public function responseClick()
    {        
        if ( isset($this->_post->EventKey) )
        {
            $method = strtolower($this->_post->EventKey);
            $Weixin_Extends_Event_Click = parent::getModels('Weixin_Extends_Event_Click', $this->_post);
            if ( method_exists($Weixin_Extends_Event_Click, $method) )
            {
                return call_user_func(array($Weixin_Extends_Event_Click, $method));
            }
        }
    }
	
    /**
     * �Զ����õ�¼�û���
     * 
     * @return string $username ����һ�����õ��û���
     * @since  nv50
     */
    function setUserName(){  
		$mcharset = cls_env::getBaseIncConfigs('mcharset');
		$fromName = $this->_post->FromUserName; //΢��open_id(�û�ID)
		$_08_M_Weixin_Users = _08_factory::getInstance('_08_M_Weixin_Users', $this->_post);
		$weixin_userinfo = $_08_M_Weixin_Users->getUserInfo($fromName);
		$weixin_userinfo = cls_string::iconv('utf-8',$mcharset,$weixin_userinfo);
		$weixin_userinfo = _08_Documents_JSON::decode($weixin_userinfo);
		if(!empty($weixin_userinfo['nickname'])){
			$username = $weixin_userinfo['nickname'];
		}else{
			$username = cls_string::Random(8);
		}
		$username || $username = cls_string::Random(8);
		$username = cls_string::ParamFormat(cls_string::Pinyin($username));
		if(strlen($username)>15) $username = substr($username,0,15);
		while(cls_userinfo::checkUserName($username)){
			$username = substr($username,0,9).'_'.cls_string::Random(3);
		}
		return $username;
	}
	
}

//$ustr = "\n$username,$password,$email,$mchid,".$fromName.",$autocheck\n";
//$umsg = cls_outbug::fmtArr($this->_post);
//cls_outbug::main("_08_M_Weixin_Event::Scan-27a".$ustr.$umsg,'','wetest/log_'.date('Y_md').'.log',1);
