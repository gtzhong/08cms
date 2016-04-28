<?php
/**
 * �û�����װ������װ����ģʽ��
 * ��ֹ�û�����Խ��Խ�Ӵ󣬲����ٶ��ؼ̳�֮��ĸ����߼�
 * 
 * @author    Wilson <Wilsonnet@163.com>
 * @copyright Copyright (C) 2008 - 2013 08CMS, Inc. All rights reserved.
 */
defined('M_COM') || exit('No Permisson');
class cls_UserbaseDecorator
{
	private $user;
	
	public function __construct( cls_userbase $user )
    {
        $this->user = $user;
    }
    
    /**
     * ��ͨ����UC��ͨ��֤֮��ĵ�¼����ͬ�����±�������
     * 
     * @param string $password ��ǰ��¼���û�����
     * @param string $email    ��ǰ��¼���û�����
     * 
     * @since 1.0
     */
    public function synUpdateLocalData( $password, $email )
    {
        $md5_password = _08_Encryption::password($password);
		$needupdate = false;
        # �����ϵͳ������UC��ͨ��֤�Ĳ�ͬ�������ͬ��
		if($this->user->info['email'] != $email)
        {
			$this->user->updatefield('email', $email);
			$needupdate = true;
		}
        
		if($this->user->info['password'] != $md5_password)
        {
			$this->user->updatefield('password', $md5_password);
			$needupdate = true;
		}
		$needupdate && $this->user->updatedb();
    }
    
    /**
     * ��ͨ����UC��ͨ��֤֮��ĵ�¼����ͬ����ӱ����û�
     * 
     * @param  string $username      ��ǰ��¼���û���
     * @param  string $password      ��ǰ��¼���û�����
     * @param  string $email         ��ǰ��¼���û�����
     * @param  array  $update_fields Ҫ���µ������ֶ���Ϣ��KEYΪ�ֶ�����VALUEΪֵ���������Ҫ��־�ɲ����ݸò���
     * @return bool                  ���ע��ɹ�����true�����򷵻�false
     * 
     * @since  1.0
     */
    public function synAddLocalUser( $username, $password, $email, $update_fields = array() )
    {
		$newuser = new cls_userinfo;
        $userLen = strlen($username);
        $add_status = false;
        $md5_password = _08_Encryption::password($password);
        
        # �ж��û����Ƿ���ڣ����$uidС�ڻ����0���û���������
        $uid = $newuser->getIdForName($username);
		if(
            $uid <= 0 &&
            ($mid = $newuser->useradd($username, $md5_password, $email, $mchid = 1)) && 
            ($userLen >= 3 && $userLen <= 15) )
        {
            # ͬʱ���������ֶ�
            if ( !empty($update_fields) && is_array($update_fields) )
            {
                foreach($update_fields as $field => $value)
                {
                    $newuser->updatefield($field, $value);
                }
            }
			$newuser->check(1, true);
			//��������Ա������ת��$curuser��ͳһ�����¼����
			$this->user->info = array_merge($this->user->info, $newuser->info);
            $add_status = true;
		}
        unset($newuser);
		return ($add_status ? true : false);
    }
}