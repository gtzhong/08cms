<?php
/**
 * ����΢����֤��
 *
 * �������չ΢������ֱ���ڱ������ӷ�����SDK
 *
 * @author    Wilson
 * @copyright Copyright (C) 2008 - 2012 08CMS, Inc. All rights reserved.
 */
defined('OTHER_SITE_BIND_PATH') || die('Access forbidden!');
class sinaAuth extends Auther {
    protected static $_sae_instance = null;
    /**
     * ������Ȩ��Ϣ
     *
     * @var    array
     * @static
     * @since  1.0
     */
    private static $_token = array();

    /**
     * ��ȡ�û�����
     *
     * @return string Ҫ��ȡ���û�����
     * @since  1.0
     */
    public function getUserName()
    {
        $info = & $this->getUserInfo();
        return isset($info['name']) ? $info['name'] : '';
    }

    /**
     * ��ȡ�û�ͷ��
     *
     * @return string �����û�ͷ��URL
     * @since  1.0
     */
    public function getUserAvatar() 
    {
        $info = & $this->getUserInfo();
        return isset($info['avatar_large']) ? $info['avatar_large'] : '';
    }

    /**
     * ��ȡ�û���Ϣ
     *
     * @return array Ҫ��ȡ���û���Ϣ
     * @since  1.0
     */
    public function getUserInfo() 
    {
        if(!isset($_SESSION['token']['uid'])) return array();
        return self::getClientInstance()->show_user_by_id($_SESSION['token']['uid']);
    }
    
    /**
     * ������Ȩ����
     */ 
    public function sendRevokeOAuth()
    {
        if ( empty($_SESSION['token']) )
        {
            return false;
        }
        
        return self::getClientInstance()->revokeoauth2($_SESSION['token']);
    }

    /**
     * ��װ΢����֤
     *
     * @since 1.0
     */
    public function Setup() 
    {
        global $code, $type, $sina_closed;
        empty($sina_closed) || cls_message::show('����΢����¼�����Ѿ��رգ�');
        $o = & parent::getSinaWeiBoAuthInstance();
        if (!empty($code)) 
        {
        	$keys = array();
        	$keys['code'] = $code;
        	$keys['redirect_uri'] = WB_CALLBACK_URL;
        	try {
        	    if(empty($_SESSION['token'])) 
                {
                    self::$_token = $o->getAccessToken( 'code', $keys ) ;
            	    $data = $o->parseSignedRequest(self::$_token['access_token']);
                    ($data == -2) && cls_message::show('ǩ������');
                    // ��Ȩ���
                    if (!empty(self::$_token)) {
                    	$_SESSION[otherSiteBind::$authfields[$type]] = self::$_token['uid'];
                    	$_SESSION['token'] = self::$_token;
                    }
                }
        	} catch (OAuthException $e) {
        	    die('error: ' . $e->getMessage());
        	}
        }
    }
    
    /**
     * ���ػص���ַ
     * 
     * @param string Ҫ���صĻص���ַ
     */  
    public function getCallBack()
    {
        parent::getSinaWeiBoAuthInstance();
        return parent::$_urls['sina'];
    }

    /**
     * ��ȡ����΢����Ϣ�Ķ���
     *
     * @return object self::$_sae_instance ���ز���΢����Ϣ�Ķ���
     * @since  1.0
     */
    public static function getClientInstance() 
    {
        global $cms_abs, $sina_appid, $sina_appkey, $mcharset;
        include_once( OTHER_SITE_BIND_PATH . 'weibocom' . DS . 'config.php' );
        require_once( OTHER_SITE_BIND_PATH . 'weibocom' . DS . 'saetv2.ex.class.php' );
        if(!(self::$_sae_instance instanceof SaeTClientV2)) 
        {
            self::$_sae_instance = new SaeTClientV2(WB_AKEY, WB_SKEY, $_SESSION['token']['access_token']);
        }
        return self::$_sae_instance;
    }
}