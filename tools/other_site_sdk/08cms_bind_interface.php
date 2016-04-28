<?php
/**
 * 08CMS�󶨽ӿ�
 *
 * @author    Wilson
 * @copyright Copyright (C) 2008 - 2012 08CMS, Inc. All rights reserved.
 */
defined('OTHER_SITE_BIND_PATH') || die('Access forbidden!');
class bind08CMSInterface
{
    /**
     * ����վ�Ļ�Աδ�󶨱�վ�Ļ�Ա���д򿪰�ģ��
     *
     * @param array $auth  ��ǰ��Ȩ����
     * @param array $minfo �û���Ϣ
     * @since 1.0
     */
    public static function BindTemplate($auth, $minfo)
    {
        global $cms_abs, $mcharset, $infloat, $timestamp, $handlekey, $hostname, $type, $cms_top;
		$curuser = cls_UserMain::CurUser();
        $username = substr($_SESSION[otherSiteBind::$authfields[$type]], 0, 10);
        #$username = $auth->getUserName();
        $useravatar = $auth->getUserAvatar();
        $post_url = $cms_abs . substr($_SERVER['REQUEST_URI'], 1);
        
        # QQ����һ����֤��ʽ�����Ըĳ�ֱ�Ӱ�
        #include_once OTHER_SITE_BIND_PATH . '08cms_bind_template.php';
        
        $minfo = (array) $minfo;
        
        if(empty($curuser->info['mid']))
        {
            # �ж�QQ��¼����û����Ƿ���ڱ�վ������ʱ�����û�������������ֱ��ʹ��
            while(cls_userinfo::checkUserName($username) && (strlen($username) < 20))
            {
                $username .= cls_string::Random(1);
            }
            $minfo['mname'] = $username;
            $minfo['password'] = cls_string::Random(6);
            empty($useravatar) || $minfo['thumb'] = $useravatar;
            $minfo['email'] = substr($_SESSION[otherSiteBind::$authfields[$type]], 0, 10) . '@' . $cms_top;  
            self::actionBind('bindregister', $minfo);         
        }       
        else
        {
            $minfo['mname'] = $curuser->info['mname'];
            $minfo['password'] = $curuser->info['password'];
            $minfo['password2'] = $curuser->info['password'];
            self::actionBind('bindlogin', $minfo); 
        }
    }

    /**
     * ��ʼ���а󶨲���
     *
     * @param string $act ֵΪbindregister��ע��󶨣�������Ϊ��¼��
     * @since 1.0
     */
    public static function actionBind($act, $minfo = array())
    {
        global $mname, $password, $email, $regcode, $thumb, $type, $mcharset, $tblprefix, $db, $censoruser, $onlineip, $timestamp, $password2;
		$curuser = cls_UserMain::CurUser();
		$mconfigs = cls_env::mconfig();
        empty($_SESSION[otherSiteBind::$authfields[$type]]) && otherAuthFactory::UcActive('�������󣬵�ǰ��½��Ϣ�Ѿ����ڣ�');
        $act = strtolower(trim($act));
        # ֱ��Ĭ��Ϊע��״̬
        foreach(array('mname', 'password', 'thumb', 'email') as $key)
        {
            empty($minfo[$key]) || $$key = $minfo[$key];
            $key == 'password' && $password2 = $minfo[$key];
        }
		
		//Ԥ�����Ա�ʺš����롢Email
		foreach(array('mname','password','email') as $key){
			if($act != 'bindregister' && $key == 'email') continue;//��¼����Ҫemail
			$re = cls_userinfo::CheckSysField(@$$key,$key,$act == 'bindregister' ? 'add' : 'login');
			if($re['error']){
				otherAuthFactory::UcActive($re['error']);
			}else $$key = $re['value'];
		}
		
        $username = trim($mname);
        switch($act) {
            // ע���
            case 'bindregister' :
		        $password2 = trim($password2);
                if($password != $password2) cls_message::show('������ȷ�����벻��ͬ��',axaction(1,M_REFERER));
				//UC��Աͬ��ע��
				$_ucid = cls_ucenter::register($username,$password,$email,TRUE);
				
                $mchid = 1;#���������Ա��
                $mchannel = cls_cache::Read('mchannel',$mchid);
                $newuser = new cls_userinfo;
                if($mid = $newuser->useradd($username,_08_Encryption::password($password),$email,$mchid))
                {
                    if(strtolower($type) == 'qq')
                    {
                        $auth = otherAuthFactory::Create($type);
                        $qqNickName = $auth->getUserName();
            			$newuser->updatefield('qq_nickname', $db->escape($qqNickName)); 
                    }
                    
        			$newuser->updatefield(otherSiteBind::$authfields[$type], $_SESSION[otherSiteBind::$authfields[$type]]);
                    empty($_ucid) || $newuser->updatefield(cls_ucenter::UC_UID, $_ucid);
        			#$autocheck = $mchannel['autocheck'];
        			$autocheck = 1;
					$newuser->check($autocheck);
        			if($autocheck == 1){
						cls_userinfo::LoginFlag($mid,_08_Encryption::password($password));
        			}elseif($autocheck == 2){
						cls_userinfo::SendActiveEmail($newuser->info);
        			}
        			$newuser->updatedb();
        			otherAuthFactory::UcActive(!$autocheck ? '�û��ȴ����' : ($autocheck == 2 ? '��Ա�����ʼ��ѷ��͵��������䣬��������伤����ɰ�' : '��Ա��¼�ɹ���'));
                } else {
                    otherAuthFactory::UcActive('��Աע��ʧ�ܣ�������ע�ᡣ');
                }
			break;
            // ��¼�󶨣����ַ�ʽ�Ѿ���UC�ǿ�ʧЧ����Ϊ�Ѿ���ȡ����������������֤
            default :
				//��¼ǰ��Ԥ���
                $curuser->loginPreTesting("javascript_alert: window.close(); ");//��ϵͳ��¼��Ԥ���
				
				//����¼�ʺŵĻ�Ա���϶���$curuser�����Ա�����ڣ��򱣳�Ϊ�οͣ���������������UC�еĻ�Ա
				$curuser->merge_user($username);
				
				//��ϵ�ǰ��¼�ʺż����룬��UC��Ա�뱾վ��Ա�������ϣ�������ͬ����¼
				if($re = $curuser->UCLogin($username,$password)) otherAuthFactory::UcActive($re);
                $windid = cls_WindID_Send::getInstance();
                $windid->setter('is_show_error_message', false);
    		    $windid->synLogin( $username, $password );
				
				//��ʽ��֤��¼������¼�����
				$flag = false;
	//			if ( $curuser->info['password'] == _08_Encryption::password($password) ){
					$curuser->updatefield(otherSiteBind::$authfields[$type],$_SESSION[otherSiteBind::$authfields[$type]]);//�������°󶨺���
					$curuser->updatedb();
					
					if ($curuser->info['checked'] == 1) {
						$curuser->OneLoginRecord(@$expires);
						otherAuthFactory::UcActive('�󶨳ɹ���');
					} elseif ($curuser->info['checked'] == 2) {#��Ҫ�ʼ�������ε�¼���ɹ������·��ͼ����ʼ�
						exit('<script type="text/javascript"> alert("��Ա��Ҫͨ��Email��������ط������ʼ����������䡣"); location.href = "'. self::getActivationJumpUrl($curuser->info, 'uc_action') . '"; </script>');
					} else {
					    otherAuthFactory::UcActive('��Ա��Ҫ����Ա��˺����������¼',axaction(1,$forward));
					}
	//			} else {
//					$curuser->logincheck(-1,$username,$password);
//					otherAuthFactory::UcActive('��Ա�ʺŰ�ʧ��');
//				}
			break;
        }
    }

    /**
     * ����Ѿ�������е�¼
     *
     * @since 1.0
     */
    public static function Login08CMS($minfo)
    {
        global $enable_uc, $timestamp, $onlineip, $type, $ckpath, $db;
        $curuser = cls_UserMain::CurUser();
		$curuser->currentuser();
        // ����Աģ����Ҫ�ֶ�������˻��ʼ�����������(2Ϊ�����ʼ�����Ա����)
		if(empty($minfo['checked'])){
        	otherAuthFactory::UcActive('��Ա�ʺ���Ҫ����Ա��ˣ�');
		}elseif(intval(@$minfo['checked']) == 2) {//��Ҫ�ʼ�����ط�����email
			mheader('Location:'. self::getActivationJumpUrl($minfo, 'uc_action'));
        }elseif(intval(@$minfo['checked']) == 1){
            //�������°󶨺���
            if(!empty($curuser->info['mid']))
            {
                $curuser->OneLoginRecord();
				$k = otherSiteBind::$authfields[$type];
                $v = $_SESSION[otherSiteBind::$authfields[$type]];
                $db->update('#__members', array($k => ''))->where(array('mid' => $curuser->info['mid']))->exec(); #����
                $curuser->info[otherSiteBind::$authfields[$type]] = '';
                                
                if(strtolower($type) == 'qq')
                {
                    $auth = otherAuthFactory::Create($type);
                    $qqNickName = $auth->getUserName();
                    $curuser->updatefield('qq_nickname', $db->escape($qqNickName));
                }
                
                $curuser->updatefield(otherSiteBind::$authfields[$type],$_SESSION[otherSiteBind::$authfields[$type]]);//���°󶨺���
                $curuser->updatedb();
                otherAuthFactory::UcActive('���°󶨳ɹ���');
            }
                    
            cls_userinfo::LoginFlag($minfo['mid'],$minfo['password']);            
        	otherAuthFactory::UcActive('��¼�ɹ���');
		}
    }

    /**
     * ��ȡ������תURL
     *
     * @param  string $action ��ת����
     * @return string         ����Ҫ��ת��URL
     * @since  1.0
     */
    public static function getActivationJumpUrl($minfo, $action)
    {
        global $cms_abs;
		return cls_userinfo::SendActiveEmailUrl(@$minfo['mname'],@$minfo->info['email'],$cms_abs . substr($_SERVER['REQUEST_URI'], 1) . "&act={$action}")	;	
    }
}