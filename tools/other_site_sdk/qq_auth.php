<?php
/**
 * QQ��¼��֤��
 *
 * �������չ����ֱ���ڱ������ӷ�����SDK
 *
 * @author    Wilson
 * @copyright Copyright (C) 2008 - 2012 08CMS, Inc. All rights reserved.
 */
defined('OTHER_SITE_BIND_PATH') || die('Access forbidden!');
class qqAuth extends Auther {
    /**
     * ��װ��֤�ӿ�
     *
     * @since 1.0
     */
    public function Setup()
    {
        global $cms_abs, $type, $m_cookie;
        $url = $cms_abs . 'tools/other_site_sdk/qqcom/oauth/qq_callback.php?' . $_SERVER['QUERY_STRING'];
        if(!isset($_SESSION[otherSiteBind::$authfields[$type]])) {
            if( !isset($m_cookie['read_openid']) || $m_cookie['read_openid'] < 3 ) {
                if ( isset($m_cookie['read_openid']) )
                {
                    msetcookie('read_openid', ++$m_cookie['read_openid']);
                }
                else 
                {
                    msetcookie('read_openid', 1);
                }
            } else {
                msetcookie('read_openid', 0);
                $message = <<<HTML
                ��¼ʧ�ܣ����Ժ����ԡ�<br /><br />
                <!-- 
                    ��ȡ��Ȩ��Ϣʧ�ܣ���������PHP����:
                    1. SESSION�Ƿ�ɶ�д
                    2. php_openssl.dll��php_curl.dll��չ���Ƿ��Ѿ�������
                -->
HTML;
                cls_message::show($message);
            }
            // �����ڴ˹رմ��ڣ������������ⷽ����qq_callback.php��<script>window.close();</script>ʧЧ
            echo '<script type="text/javascript" src="' . $url . '"></script>';
            exit ('<script type="text/javascript"> window.location.reload(); </script>');
        } else {
            $_SESSION[otherSiteBind::$authfields[$type]] = $_SESSION["openid"];
        }
    }

    /**
     * ��ȡ�û�����
     *
     * @return string Ҫ��ȡ���û�����
     * @since  1.0
     */
    public function getUserName()
    {
        global $mcharset;
        $info = & $this->getUserInfo();
        if(false === stripos($mcharset, 'UTF'))
        {
            return cls_string::iconv('UTF-8', $mcharset, $info['nickname']);
        }
        else
        {
            return $info['nickname'];
        }
    }

    /**
     * ��ȡ�û�ͷ��
     *
     * figureurl_2Ϊ100*100��figureurl_1Ϊ50*50��figureurl_0Ϊ��20*20
     *
     * @return string Ҫ��ȡ���û�����
     * @since  1.0
     */
    public function getUserAvatar( $figureurl = 'figureurl_2' )
    {
        $info = & $this->getUserInfo();
        return $info[$figureurl];
    }

    /**
     * ��ȡ�û���Ϣ
     *
     * @return array Ҫ��ȡ���û���Ϣ
     * @since  1.0
     */
    public function getUserInfo() {
        include_once OTHER_SITE_BIND_PATH . 'qqcom' . DS . 'comm' . DS . 'utils.php';
        $get_user_info = "https://graph.qq.com/user/get_user_info?"
            . "access_token=" . $_SESSION['access_token']
            . "&oauth_consumer_key=" . $_SESSION["appid"]
            . "&openid=" . $_SESSION["openid"]
            . "&format=json";

        $info = get_url_contents($get_user_info);
        $arr = json_decode($info, true);

        return $arr;
    }
    
    /**
     * ������Ȩ����
     * 
     * @todo ��QQ����Ҫ���ͣ�ֱ�������ͺ�
     */ 
    public function sendRevokeOAuth() {}
    
    /**
     * ���ػص���ַ
     * 
     * @param string Ҫ���صĻص���ַ
     */    
    public function getCallBack()
    {
        parent::getQQAuthInstance();
        return parent::$_urls['qq'];
    }
}