<?php
/**
* ���ļ�������PW����˷���֪ͨ����
* ���API��鿴{@link http://wiki.open.phpwind.com/index.php?title=WindID_API}
* 
* @package    PHPWIND
* @subpackage WindID
* @author     Wilson <Wilsonnet@163.com>
* @copyright  Copyright (C) 2008 - 2013 08CMS, Inc. All rights reserved.
*/
define('PW_EXEC', TRUE);

class cls_WindID_Send
{
    /**
     * �Ƿ���ʾ������Ϣ
     **/        
    private $is_show_error_message = true;
    
    private $error_message = '';     
    
    private static $instance = null;  
    
    private $enable = true;     
                
    /**
     * ��ʼ����֤�������жϺ�̨�Ƿ�����PW����
     * 
     * @static
     */
	protected function __construct()
	{
    	$this->enable = (bool) cls_env::mconfig('enable_pptin');
        if (!$this->isEnable())
        {
            $this->error_message = 'WINDID δ������';
        }
        
        try
        {
            _08_Loader::import('include:phpwind:windid_message.pw');
            _08_Loader::import('include:phpwind:windid_client:src:windid:WindidApi');
        }
        catch(WindDbException $e)
        {
            #echo $e->getMessage();   ǿ����ֹ��̨�û�����ͨ��֤�����ô����������ر�ͨ��֤��
            if ( defined('M_ADMIN') && $this->isEnable() && $this->is_show_error_message )
            {
                cls_message::show('WINDIDͨ��֤ͨ��ʧ�ܣ�������ϵͳ���� - ͨ��֤�����ú�WINDID��������Ϣ');
            }
            else  # ��Ϊ������ԭ��Ӱ�쵽ǰ̨�û�����ʹ�ã���ǰ̨��ǿ��
            {
            	return false;
            }
        }
		
        return true;
	}
	
	/**
	 * ��WINDID����˷���ͬ����¼����
     * {@link http://wiki.open.phpwind.com/index.php?title=User/login}
	 * 
	 * @param  string $username �����¼���û���
	 * @param  string $password �����¼������
     * @return int              �����¼�ɹ������û�UID������ֱ�Ӵ�ӡ������Ϣ
	 * 
     * @static
	 * @since  1.0
	 */
	public function synLogin( $username, $password )
	{
        # �����̨δ����WINDID��������
		if ( !$this->isEnable() )
        {
            return false;
        }
		$curuser = cls_UserMain::CurUser();
		$api = self::__getWindidAPI();
		# ������WINDID����˷��͵�¼����
		list($status, $userinfo) = $api->login( $username, $password );
        /**
         * �����¼�ɹ�ʱ������Ӧ�ÿͻ��˷���ͬ����¼���󣬾��巵��״̬���ʾ�뿴
         * @see cls_Windid_Message::get()
         */
		if($status == 1)
		{
            $user = new cls_UserbaseDecorator($curuser);
            # ͬ�����±�������
            if ( isset($curuser->info['mid']) && ($curuser->info['mid'] > 0) )
            {
                $user->synUpdateLocalData($password, $userinfo['email']);
            }
            else // ���������и��û���������û�����Զ�ע��
            {
                $user->synAddLocalUser($username, $password, $userinfo['email']);
            }
            unset($user);
			
			# ��PW��¼�ɹ����û�дһ��COOKIE��ʶPW�û���ID��Ԥ�������û�ID��ͬ��������˳����⡣
			msetcookie(cls_Windid_Message::PW_UID_COOKIE, $userinfo['uid'], cls_Windid_Message::PW_UID_COOKIE_TIME);
			echo $api->synLogin($userinfo['uid']);
            return $userinfo['uid'];
		}
		else # ���ͬ����¼���ɹ�ʱ��ʾ��Ӧ����
		{
		    if ($this->is_show_error_message)
            {
                cls_message::show( cls_Windid_Message::get($status), M_REFERER );                                
            }
            else
            {
            	$this->error_message = cls_Windid_Message::get($status);
                return false;                
            }
		}
	}
    
    /**
     * ��WINDID����˷���ͬ��ע������
     * {@link http://wiki.open.phpwind.com/index.php?title=User/register}
     * 
	 * @param  string $username ������û���
	 * @param  string $password ���������
	 * @param  string $email    ���������
     * @param  string $regip    ע��IP
     * @param  bool   $synlogin �Ƿ����ע���ͬ����¼��TRUEΪͬ����FALSEΪ��ͬ������̨��Ӳ���Ҫͬ����
     * @param  string $question ������ʾ����
     * @param  string $answer   ������ʾ��
     * @return int              ����WINDIDע�����û�ID������ֱ�Ӵ�ӡ����
     * 
     * @static
     * @since  1.0
     */
    public function synRegister( $username, $password, $email, $regip = '', $synlogin = true, $question = '', $answer = '' )
    {
        # �����̨δ����WINDID��������
		if ( !$this->isEnable() )
        {
            return false;
        }
        $api = self::__getWindidAPI();
        $uid = $api->register( $username, $email, $password, $question, $answer, $regip );
        if ($uid > 0)
        {
            # ע��ɹ���ͬ����¼
            if ( $synlogin )
            {
		        list($status,) = $api->login( $username, $password );
                if ( $status == 1 )
                {
                    // BUG: WINDID��SDKò����BUG��ע����¼Ҫ������ε�¼�ŵ�¼�ɹ�����������¼��û��������
                    for($i = 0; $i < 3; ++$i) echo $api->synLogin($uid);
                    
        			# ��PW��¼�ɹ����û�дһ��COOKIE��ʶPW�û���ID��Ԥ�������û�ID��ͬ��������˳����⡣
        			msetcookie(cls_Windid_Message::PW_UID_COOKIE, $uid, cls_Windid_Message::PW_UID_COOKIE_TIME);
                }
            }
            return $uid;
        }
        else
        {
		    if ($this->is_show_error_message)
            {
                cls_message::show( cls_Windid_Message::get($uid), M_REFERER );                
            }
            else
            {
            	$this->error_message = cls_Windid_Message::get($uid);
                return false;
            }
        }
    }
    
    /**
     * ͬ���༭�û�������Ϣ
     * {@link http://wiki.open.phpwind.com/index.php?title=User/edit}
     * 
     * @param  int    $uid      �û�ID
     * @param  string $password �û�ԭ����
     * @param  array  $editinfo �޸���Ϣarray('username', 'password', 'email', 'question', 'answer')
     * 
     * @static
     * @since  1.0
     */
    public function editUser( $uid, $password, array $editinfo )
    {
        # �����̨δ����WINDID��������
		if ( !$this->isEnable() )
        {
            return false;
        }
        
        $api = self::__getWindidAPI();
        return $api->editUser( self::_getPwUidByMid($uid), $password, $editinfo );
    }
    
    /**
     * ͨ����ϵͳ���û�ID��ȡWINDID����˵��û�ID
     * 
     * @param  int $mid ��ϵͳ���û�ID
     * @return int      ����WINDID����˵��û�ID
     */
    protected static function _getPwUidByMid( $mid )
    {
        $info = cls_userbase::getUserInfo( cls_Windid_Message::PW_UID, 'mid = ' . (int)$mid  );
        return $info[cls_Windid_Message::PW_UID];
    }
    
    /**
     * ͬ���༭�û���ϸ��Ϣ
     * {@link http://wiki.open.phpwind.com/index.php?title=User/editInfo}
     * 
     * @param  int    $uid      �û�ID
     * @param  array  $editinfo �޸���Ϣ
     * array('realname', 'gender', 'byear', 'bmonth','bday', 'hometown', 'location', 
     *       'homepage', 'qq', 'aliww', 'mobile', 'alipay', 'msn','profile')
     * 
     * @static
     * @since  1.0
     */
    public function editUserInfo( $uid, array $editinfo )
    {
        # �����̨δ����WINDID��������
		if ( !$this->isEnable() )
        {
            return false;
        }
        
        $api = self::__getWindidAPI();
        return $api->editUserInfo( self::_getPwUidByMid($uid), $editinfo );
    }
    
    /**
     * ͬ��ɾ�������û�
     * {@link http://wiki.open.phpwind.com/index.php?title=User/delete}
     * 
     * @param  int $uid �û�ID
     * @return int      ɾ���ɹ�����1�����򷵻�0
     * 
     * @static
     * @since  1.0
     */
    public function deleteUser( $uid )
    {
        return self::batchDeleteUser( (array)$uid );
    }
    
    /**
     * ͬ��ɾ������û�
     * {@link http://wiki.open.phpwind.com/index.php?title=User/batchDelete}
     * 
     * @param  array $uids �û�ID����
     * @return int         ɾ���ɹ�����1�����򷵻�0
     * 
     * @static
     * @since  1.0
     */
    public function batchDeleteUser( array $mids )
    {
        # �����̨δ����WINDID��������
		if ( !$this->isEnable() )
        {
            return false;
        }
        
        $api = self::__getWindidAPI();
        return $api->batchDeleteUser( self::_getPwUidsByMids($mids) );
    }
    
    /**
     * ����վ�ڶ���
     * {@link http://wiki.open.phpwind.com/index.php?title=Message/send}
     * 
     * @param  array  $mids    �����û�ID��֧�ֶ��
     * @param  string $content ��������
     * @param  int    $fromUid ������ID
     * @return int             �ɹ�����1��ʧ�ܷ��ش������
     */
    public function send( $mids, $content, $fromUid )
    {
        # �����̨δ����WINDID��������
		if ( !$this->isEnable() )
        {
            return false;
        }
        $api = self::__getWindidAPI('message');
        return $api->send( self::_getPwUidsByMids($mids), $content, $fromUid );
    }
    
    /**
     * ���ݴ���ı�ϵͳUID����WINDID����˵��û�ID
     * 
     * @param  array $mids ��ϵͳ�û�ID
     * @return array $uids WINDID������û�ID
     * @since  1.0
     */
    protected static function _getPwUidsByMids( array $mids )
    {        
        $uids = array();
        if ( !empty($mids) )
        {
            $mids = array_map('intval', $mids);
            #��ȡ��ӦWINDID�û���ID
            $db = _08_factory::getDBO();
            $db->select( cls_Windid_Message::PW_UID )
               ->from('#__members')
               ->where('mid ')->_in($mids)
               ->exec();
            while( $row = $db->fetch() )
            {
                $uids[] = $row[cls_Windid_Message::PW_UID];
            }
        }
        return $uids;
    }
    
    /**
     * ɾ����������
     * {@link http://wiki.open.phpwind.com/index.php?title=Message/deleteMessages}
     * 
     * @param  int   $uid        �û�ID
     * @param  array $messageIds ˽��ID
     * @return int               �ɹ�����1 ʧ�ܷ���0
     */
    public function deleteMessages( $uid, array $messageIds )
    {
        # �����̨δ����WINDID��������
		if ( !$this->isEnable() )
        {
            return false;
        }
        
        $api = self::__getWindidAPI('message');
        return $api->deleteMessages( self::_getPwUidByMid($uid), self::_getMessageIds($messageIds) );
    }
    
    /**
     * ͨ����ϵͳ��˽��ID��ȡWINDID�����˽��ID
     * 
     * @param  array $messageIds  ��ϵͳ��˽��ID
     * @return array $message_ids WINDID�����˽��ID
     * @since  1.0
     */
    protected static function _getMessageIds( array $messageIds )
    {
        # ��ȡ��ϵͳ˽��ID
        $db = _08_factory::getDBO();
        $db->select(cls_Windid_Message::PW_MESSAGE_ID)
           ->from('#__pms')
           ->where('pmid')->_in($messageIds)
           ->exec();
        $message_ids = array();
        while($row = $db->fetch())
        {
            $message_ids[] = $row[cls_Windid_Message::PW_MESSAGE_ID];
        }
        return $message_ids;
    }
    
 	/**
	 * �����û�����
     * {@link http://wiki.open.phpwind.com/index.php?title=User/editCredit}
	 *
	 * @param  int  $uid   �û�ID
	 * @param  int  $cType �����������ID���� ǰ׺_windid_user_data���ݱ���ֶ� credit1 (1-8)
	 * @param  int  $value Ҫ���»���ֵ
     * @param  bool $isset �ò������޸Ļ��ֻ������ӻ��֣�TRUEΪ�޸ģ�FASLEΪ����
     * 
     * @static
     * @since  1.0
	 */   
    public function editCredit( $uid, $cType, $value, $isset = false )
    {
        # �����̨δ����WINDID��������
		if ( !$this->isEnable() )
        {
            return false;
        }
        $api = self::__getWindidAPI();
        return $api->editCredit( self::_getPwUidByMid($uid), $cType, $value, $isset );
    }
    
    /**
     * ��֤�û���Ϣ
     * {@link http://wiki.open.phpwind.com/index.php?title=User/checkInput}
     * 
     * @param  string $input    ����֤���ַ�
     * @param  int    $type     ��֤���� 1-�û���, 2-����, 3-����
     * @param  string $username �û���,���ڶ�ĳusername���������ͽ�����֤
     * @param  int    $uid      �û�UID,���ڶ�ĳuid���������ͽ�����֤
     * @return int              int�ͣ���֤�ɹ�������1, ��¼ʧ�ܣ�����С��1�Ĵ������
     * 
     * @static
     * @since  1.0
     */
    public function checkUserInput($input, $type, $username = '', $uid = 0)
    {
        # �����̨δ����WINDID��������
		if ( !$this->isEnable() )
        {
            return false;
        }
        $api = self::__getWindidAPI();
        return $api->checkUserInput($input, $type, $username, $uid );
    }
    
    /**
     * ��ȡPW�ӿ�API����
     * 
     * @return object API������
     */
    private static function __getWindidAPI( $type = 'user' )
    {
        return WindidApi::api( $type );
    }
    
    /**
     * ��ȡPW�û�δ��˽��
     * {@link http://wiki.open.phpwind.com/index.php?title=Message/getUnreadDialogsByUid}
     * 
     * @param  int   $uid �û�ID
     * @return array      �����û�δ��˽������
     */
    public function getPwUserMessage( $uid )
    {
        # �����̨δ����WINDID��������
		if ( !$this->isEnable() || ($uid < 0) )
        {
            return false;
        }
        $api = self::__getWindidAPI('message');
        return $api->getUnreadDialogsByUid( self::_getPwUidByMid($uid) );
    }
	
	/**
	 * ��WINDID����˷���ͬ���˳�����
     * 
     * @return bool �˳��ɹ�����TRUE�����򷵻�FALSE
	 * @since  1.0
	 */
	public function synLogout()
	{
	    # �����̨δ����WINDID��������
		$cookies = cls_env::_COOKIE();
		if ( $this->isEnable() && !empty($cookies[cls_Windid_Message::PW_UID_COOKIE]) )
        {
    		mclearcookie( cls_Windid_Message::PW_UID_COOKIE );
            $api = WindidApi::api('user');
    		echo $api->synLogout($cookies[cls_Windid_Message::PW_UID_COOKIE]);
            return true;
        }
        
        return false;
	}
    
    /**
     * �Ƿ��Ѿ�����WINNDID
     * 
     * @return bool ����Ѿ���������TRUE�����򷵻�FALSE
     * @since  nv50
     **/
    public function isEnable()
    {
        return $this->enable;
    }
    
    public static function getInstance()
    {
        if (!(self::$instance instanceof self))
        {
            self::$instance = new self();
        }
        
        return self::$instance;
    }
    
    /**
     * ��������
     * ע�����������ø÷���ʱ�������Ҫ�жϵ����Ա��費��Ϊ private
     * 
     * @param string $name  Ҫ���õ���������
     * @param mixed  $value ����ֵ
     * 
     * @since nv50
     */
    public function setter($name, $value)
    {        
        if ( property_exists($this, $name) )
        {
            $this->$name = $value;
        }
    }    
    
    /**
     * ��ȡ����
     * ע�����������ø÷���ʱ�������Ҫ�жϵ����Ա��費��Ϊ private
     * 
     * @param  string $name  Ҫ��ȡ����������
     * @return mixed         ���ػ�ȡ��������ֵ����������ڸ����Ի��Ǹ�����Ϊprivateʱ����null
     * 
     * @since  nv50
     */
    public function getter($name)
    {    
        if ( property_exists($this, $name) )
        {
            return $this->$name;
        }
        
        return null;
    }
}