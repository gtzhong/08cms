<?php
defined('M_UPSEN') || define('M_UPSEN', TRUE);
defined('NOROBOT') || define('NOROBOT', TRUE);
include_once dirname(__FILE__).'/include/general.inc.php';
//$mobiledir = cls_env::mconfig('mobiledir'); //��������ȥ�˾�Ҫ��һ��
_08_FilesystemFile::filterFileParam($mobiledir); 
$ismobiledir = defined('IN_MOBILE') ? "$mobiledir/" : ''; //echo $ismobiledir; //�����ֻ����ַ
if($ex = exentry('login')){include($ex);mexit();} 
empty($action) && $action = 'login';
empty($mode) && $mode = '';
empty($forward) && $forward = M_REFERER;
$gets = cls_env::_GET_POST('token');
if(!$forward || preg_match('/\b(?:login|register)\.php(\?|#|$)/i', $forward))$forward = $cms_abs.(defined('IN_MOBILE') ? $ismobiledir : 'adminm.php');#����pw����pw���ص���ת����
switch($action){
case 'login':
	if($enable_pptin && $pptin_url && $pptin_login && $mode != 'js'){//������ʾjs��¼ģ�壬ֱ����ת��ͨ��֤�����
		$forward = substr($cms_abs, 0, -1);//??
		$url = $pptin_url.$pptin_login;
		$url .= (in_str('?',$url) ? '&' : '?') . 'forward=' . rawurlencode($forward);
		mheader('location:'.$url);
	}
	if(!submitcheck('cmslogin')){
		$temparr = array('forward' => rawurlencode($forward));
		if($mode == 'js'){//ǰ̨js���õ�¼��Ϣ�Ĵ���
			# �����ڹ�ȥ��"ʧЧ"
			header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
			# ��Զ�ǸĶ�����
			header("Last-Modified: " . gmdate("D, d M Y H:i:s") . "GMT");
			# HTTP/1.1
			header("Cache-Control: no-store, no-cache , must-revalidate");
			header("Cache-Control: post-check=0, pre-check=0", false);
			# HTTP/1.0
			header("Pragma: no-cache");
			header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
			js_write(cls_tpl::SpecialHtml(empty($memberid) ? 'jslogin' : 'jsloginok',array('forward' => rawurlencode($forward))),defined('IN_MOBILE'));
			mexit();
		}else{
			if(!($tplname = cls_tpl::SpecialTplname('login',defined('IN_MOBILE')))){
				include_once M_ROOT."include/adminm.fun.php";
				_header('��Ա��½');
				echo form_str('cmslogin',"?forward=".rawurlencode($forward));
				tabheader_e();
				echo '<tr class="header"><td colspan="2"><b>��Ա��½&nbsp; &nbsp; >><a href="'.$cms_abs.'tools/lostpwd.php"'.(empty($infloat)?'':" onclick=\"return floatwin('open_$handlekey',this)\"").'>�һ�����</a> <a href="'.$cms_abs.$ismobiledir.'register.php"'.(empty($infloat)?'':" onclick=\"return floatwin('open_$handlekey',this)\"").'>ע��</a></b></td></tr>';
				trbasic('��Ա����','username', '', 'text', array('validate' => ' rule="text" must="1" min="3" max="15" warn="�û���ӦΪ3-15���ֽڣ�"'));
				trbasic('��½����','password','','password', array('validate' => ' rule="text" must="1" min="1" max="15" warn="����ջ�����Ƿ��ַ���������ȫ0�����룩��"'));
				$cookiearr = array('0' => '���ü�ס','-1' => '����ס','2592000' => '1����', '7776000' => '3����',);
				trbasic('��ס��¼״̬','expires',makeoption($cookiearr,0),'select');
				tr_regcode('login');
				echo '<script type=\"text/javascript\">window._08cms_validator && _08cms_validator.submit(function(){if(this.client_t)this.client_t.value=(new Date).getTime()})</script>';
				trhidden('client_t','');
                if (isset($gets['token']))
                {
                    trhidden('token', $gets['token']);
                }
				tabfooter('cmslogin','��½');
				mexit('</div></body></html>');
			}else{
				$html = cls_SpecialPage::Create(
					array(
						'tplname' => $tplname,
						'_da' => array('forward' => rawurlencode($forward),),
						'LoadAdv' => true,
						'NodeMode' => defined('IN_MOBILE'),
					)
				);
                if (isset($gets['token']))
                {
                    $trhidden = <<<HTML
                    <input type="hidden" name="token" value="{$gets['token']}" />
HTML;
                    $html = preg_replace('/(<form.*>)/', "$1\n{$trhidden}", $html);
                }
                
				exit($html);
			}
		}
	}else{
		//��֤��
		regcode_pass('login',empty($regcode) ? '' : trim($regcode)) || cls_message::show('��֤�����',axaction(1,M_REFERER));
		
		//Ԥ�����Ա�ʺš�����
		foreach(array('username' => 'mname','password' => 'password') as $key => $type){
			$re = cls_userinfo::CheckSysField(@$$key,$type,'login');
			if($re['error']){
				cls_message::show($re['error'],axaction(1,M_REFERER));
			}else $$key = $re['value'];
		}
		$md5_password = _08_Encryption::password($password);
		
		
		//��¼ǰ��Ԥ���
        $curuser->loginPreTesting(axaction(1,M_REFERER));//��ϵͳ��¼��Ԥ���
		
		//����¼�ʺŵĻ�Ա���϶���$curuser�����Ա�����ڣ��򱣳�Ϊ�οͣ���������������UC�еĻ�Ա
		$curuser->merge_user($username);
		
		//��ϵ�ǰ��¼�ʺż����룬��UC��Ա�뱾վ��Ա�������ϣ�������ͬ����¼
		if($re = $curuser->UCLogin($username,$password)) cls_message::show($re,axaction(1,M_REFERER));
       
       	# ͬ����¼ͨ��֤
		cls_WindID_Send::getInstance()->synLogin( $username, $password );
        
        # �������ñ�ͷ������UC��PW������ͷӰ���˱�վ���������
        header("Content-type:text/html;charset=$mcharset");
		
		//��ʽ��֤��¼������¼�����
		if($curuser->info['password'] == $md5_password){
			if($curuser->info['checked'] == 1){
                # �����΢�ŵ�¼ʱ��΢�ź�
        		if (isset($gets['token']))
                {
                    $token = _08_Encryption::getInstance($gets['token'])->deCryption();
                    @list($FromUserName, $token, $time, $hash) = explode(',', $token);
                    if (empty($time) || (TIMESTAMP - $time > 1800))
                    {
                        cls_message::show('΢�ŵ�¼�����ѳ�ʱ��������ɨ���ά����е�¼��');
                    }
                    if (empty($token) || ($token != _08_Encryption::password($weixin_token)))
                    {
                        cls_message::show('�������������ɨ���ά����е�¼��');
                    }
                    if (empty($FromUserName))
                    {
                        cls_message::show('��������Ҫ�󶨵�΢���û���');
                    }
                    
                    $curuser->updatefield('weixin_from_user_name', $db->escape($FromUserName)); 
                    $curuser->updatedb();
                    
                    cls_message::show('΢�Ű󶨳ɹ�', _08_CMS_ABS);
                }
				$curuser->OneLoginRecord(@$expires);
				//cls_message::show('��Ա��¼�ɹ�',axaction(2,$forward));		
				//��½�ɹ����û�Աѡ�������ҳ���ǽ����Ա����
				$_url_arr = array();			
				if(!empty($forward)){
					$_url_arr['����'] = $forward; 
					$refurl = str_replace(array('index.html','index.htm','index.php'),"",$forward);
					if($refurl!=$cms_abs.$ismobiledir) $_url_arr['��ҳ'] = $cms_abs.$ismobiledir;
				}
				defined('IN_MOBILE') || $_url_arr['��Ա����'] = $cms_abs.$ismobiledir.'adminm.php'; //��ҳ����л�Ա����			
				cls_message::show('��Ա��¼�ɹ�',$_url_arr);
			}elseif($curuser->info['checked'] == 2){#��Ҫ�ʼ�������ε�¼���ɹ������·��ͼ����ʼ�
				cls_message::show('��Ա��Ҫͨ��Email��������ط������ʼ�����������',cls_userinfo::SendActiveEmailUrl($username,$curuser->info['email'],$forward));
			}else cls_message::show('δ���Ա!',axaction(1,M_REFERER));
		}
        else
        {
            $curuser->loginFailureHandling($username, $password, axaction(1, M_REFERER));//��¼ʧ��
        }
	}
	break;

case 'weixin_login':
    if (isset($gets['token']))
    {
        $token = _08_Encryption::getInstance($gets['token'])->deCryption();
        @list($FromUserName, $token, $time, $hash) = explode(',', $token);
        if (empty($time) || (TIMESTAMP - $time > 1800))
        {
            cls_message::show('�����ѳ�ʱ��������ɨ���ά����е�¼��');
        }
        if (empty($hash) || empty($token) || ($token != _08_Encryption::password($weixin_token)))
        {
            cls_message::show('�������������ɨ���ά����е�¼��');
        }
        
        $userinfo = $curuser->getUserInfo('mid, mname', array('weixin_from_user_name' => $FromUserName));
        if (empty($userinfo))
        {
            header('Location: ' . _08_CMS_ABS . $mobiledir . '/login.php?is_weixin=1&token=' . $gets['token']);
            exit;
        }
        
		$newuser = new cls_userinfo(); //ע�ⲻ��$curuser
		$newuser->activeuser($userinfo['mid']);
		$newuser->OneLoginRecord();
		//cls_outbug::main("_08_M_Weixin_Event::Scan-28b",'','wetest/log_'.date('Y_md').'.log',1);
		$db->update('#__msession', array('scene_id'=>$scene_id))->where(array('msid'=>$m_cookie['msid']))->exec();
        $_SESSION[$hash] = $time;
		//$_SESSION["mid_$hash"] = $userinfo['mid'];
        cls_message::show('��¼�ɹ���', _08_CMS_ABS);
    }
    
    cls_message::show('��¼ʧ�ܡ�');
    break;
    
case 'quit':#����PHPWindͨ��֤
case 'logout':
    ob_start();
	cls_ucenter::logout();    
    # ��WINDID����˷���ͬ���˳�����
	cls_WindID_Send::getInstance()->synLogout();
	cls_userinfo::LogoutFlag();
    $contents = ob_get_contents();
    ob_end_clean();
    $gets = cls_env::_GET('datatype, callback');
    if (isset($gets['datatype']) && (strtolower($gets['datatype']) === 'js'))
    {
        cls_phpToJavascript::toAjaxSynchronousRequest($contents);
        $ajax = _08_C_Ajax_Controller::getInstance();
        exit($ajax->format(array('error' => '', 'message' => '��Ա�˳��ɹ�'), $gets['callback']));
    }
    else
    {
        echo $contents;
    	if(!$forward || preg_match('/\badminm.php(\?|#|$)/i', $forward) || preg_match('/\blogin.php(\?|#|$)/i', $forward))$forward = $cms_abs.$ismobiledir; //.'index.php'
    	cls_message::show('��Ա�˳��ɹ�',$forward);
    }

}
?>
