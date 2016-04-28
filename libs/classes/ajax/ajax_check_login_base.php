<?php
/**
 * �ж��û���¼����
 *
 * @example   ������URL��http://nv50.08cms.com/index.php?/ajax/check_login/username/admin/password/admin/verify/ttt/regcode/9830/datatype/js
 *                         http://nv50.08cms.com/index.php?/ajax/check_login/username/admin/password/admin/verify/ttt/regcode/9830/callback/callbackFun
 * @ ����: subdata=1       ����sub��ȡͨ���ֶ�����
 * @author    Wilson <Wilsonnet@163.com>
 * @copyright Copyright (C) 2008 - 2014 08CMS, Inc. All rights reserved.
 */

defined('_08CMS_APP_EXEC') || exit('No Permission');
class _08_M_Ajax_Check_Login_Base extends _08_Models_Base
{
    protected $status;
    
    protected $_userInfo;
    
    public function __construct()
    {
        parent::__construct();
        $this->_userInfo = array();
        $this->status = array('error' => '', 'message' => '', 'user_info' => array('mid' => 0, 'mname' => '�ο�'));
    }
    
    public function __toString()
    {
        $verify = empty($this->_get['verify'])?'':$this->_get['verify'];
		cls_env::SetG('verify',$verify); //regcode_pass($rname,$code='')Ҫglobal $verify; �õ���ֵ
		if (!regcode_pass('login', empty($this->_get['regcode']) ? '' : trim($this->_get['regcode'])))
        {
            $this->status['error'] = '��֤�����';
        }
        elseif ( empty($this->_get['username']) || empty($this->_get['password']) )
        {
            $this->status['error'] = '�û��������벻��Ϊ�ա�';
        }
        else
        {
            ob_start();
            $this->_get['username'] = cls_String::iconv('UTF-8', cls_env::getBaseIncConfigs('mcharset'), $this->_get['username']);
    		//��ϵ�ǰ��¼�ʺż����룬��UC��Ա�뱾վ��Ա�������ϣ�������ͬ����¼
            $this->_curuser->UCLogin($this->_get['username'],$this->_get['password']);
           	# ͬ����¼ͨ��֤
            $windid = cls_WindID_Send::getInstance();
            $windid->setter('is_show_error_message', false);
    		$windid->synLogin( $this->_get['username'], $this->_get['password'] );
            $contents = ob_get_contents();
            ob_end_clean();
            cls_phpToJavascript::toAjaxSynchronousRequest($contents);
        	$md5_password = _08_Encryption::password($this->_get['password']);
            $user = new cls_userinfo;
            $user->activeuserbyname($this->_get['username']);
			$user->sub_data(); 
            $this->_userInfo = $user->getter('info');
            if ($md5_password == $this->_userInfo['password'])
            {
				if(empty($this->_userInfo['checked'])){
					$this->status['error'] = '��Աδ��ˡ�';
				}else{
					$user->LoginFlag($this->_userInfo['mid'], $md5_password);
					$this->status['user_info'] = $this->filterUserInfo();
					$this->status['message'] = '��¼�ɹ���';	
				}
            }
            else
            {
            	$this->status['error'] = '�û������������';
            }
        }       
        
        return $this->status;
    }
    
    public function filterUserInfo()
    {
        $new_user_info = array();
        if ($this->_userInfo)
        {
			$arr = array('mid', 'mname', 'mchid', 'checked', 'qq_nickname', 'regdate', 'currency0');
            
			$grouptypes = cls_cache::Read('grouptypes'); 
			foreach($grouptypes as $k => $v)
            {
            	if($this->_userInfo['grouptype'.$k])
                {
            		$usergroups = cls_cache::Read('usergroups',$k);
            		$usergroupName = $usergroups[$this->_userInfo['grouptype'.$k]]['cname'];
				    $new_user_info["grouptype{$k}name"] = $usergroups[$this->_userInfo['grouptype'.$k]]['cname'];
            	}
			}
            
			$currencys = cls_cache::Read('currencys');
			foreach($currencys as $k=>$v){
				$arr[] = "currency$k";
			}            
			foreach ($arr as $key)
            {
                if (isset($this->_userInfo[$key]))
                {
                    $new_user_info[$key] = $this->_userInfo[$key];
                }
            }
        }
        if(!empty($this->_get['subdata'])){ //��sub��ȡͨ���ֶ�����
			#$this->_curuser->sub_data(); 
			$mfields0 = cls_cache::Read('mfields',0); 
			foreach($mfields0 as $key=>$cfg){ 
				if(!empty($this->_userInfo[$key])){ 
					$val = $this->_userInfo[$key]; 
					if($cfg['datatype']=='image'){ //����ͼͷ���
						$val = cls_url::tag2atm($val);
					}
					$new_user_info[$key] = $val;
				}
			}
		}
        return $new_user_info;
    }
}