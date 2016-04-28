<?php
/**
 * ������վ�󶨽ӿ��࣬ʹ�øýӿ�ʱ�뾡����SESSION���浽memcached�Ч�ʻ�Ƚ�ֱ�ӱ���SESSION���ļ��á�
 *
 * �����ӽӿ�ֻ���ڱ��������һ���������ɣ�������õķ������ڵ�39�д�����б��У�ע������ڽ����������ų������磺ħ��������
 * �����뿴��http://php.net/manual/zh/language.oop5.magic.php
 * ע�������ķ��������ڽ���ǰ�����������ȨURL��������$_urls���±���������JS����OtherWebSiteLogin����typeһ��
 *
 * @author    Wilson
 * @copyright Copyright (C) 2008 - 2012 08CMS, Inc. All rights reserved.
 */
defined( 'DS' ) || define( 'DS', DIRECTORY_SEPARATOR );
defined( 'OTHER_SITE_BIND_PATH' ) || define( 'OTHER_SITE_BIND_PATH', dirname(__FILE__) . DS );

class otherSiteBind {
    /**
     * ������Ȩ�����SESSION��(ע����ֵ���������ݱ�members��־Ϊ�õ�¼��Ϣ���ֶ�����һ��)��
     * ���� array($type => $key); $type��GET���ݹ���������
     */
    public static $authfields = array('sina' => 'sina_uid', 'qq' => 'openid');
    
    /**
     * �洢�ⲿ��վ��ȨURL
     *
     * @var    array
     * @static
     * @since  1.0
     */
    protected static $_urls = array();

    public function __construct() 
    {
        # ���ֶ���������Ϊ��ִ֤��˳������
        self::checkAction();
        $reflection = new ReflectionClass('otherSiteBind');
        // �÷���API�Զ�������չ����������__construct��__toString������
        foreach($reflection->getMethods() as $k => $v)
        {
            // �ų����Զ�������õķ�����ע������ڽ����������ų������磺ħ������
            if(!in_array($v->name, array(__FUNCTION__, '__toString', 'checkAction')) && $reflection->hasMethod($v->name)) {
                $function = $reflection->getMethod($v->name);
                $function->invoke(null);
            }
        }
    }
    
    /**
     * ��֤����
     */ 
    public static function checkAction()
    {
        global $type;
        // ����$type��ֵ������otherSiteBind::$authfields�ļ���
        if(!empty($type) && !key_exists($type, self::$authfields) && isset($_SERVER['PHP_SELF']) )
        {
            $basename = basename(dirname(__FILE__));
            // ֻ���˵�ǰĿ¼�µ��ļ�
            if(false !== stripos($_SERVER['PHP_SELF'], $basename))
            {
                die('�Ƿ�����!');
            }
        }
    }

    /**
     * ��ȡQQ��¼��ȨURL
     *
     * @static
     * @since  1.0
     */
    public static function getQQAuthInstance()
    {
        global $cms_abs, $qq_appid, $qq_appkey, $qq_closed, $mcharset, $cms_top, $dbhost, $dbname, $dbpw, $dbuser;
        include_once( OTHER_SITE_BIND_PATH . 'qqcom' . DS . 'Config.php' );
        // ���õ�¼���ܹر�ʱ����URLΪclose����
        if( $qq_closed || empty($qq_appid) || empty($qq_appkey) ) return self::$_urls['qq'] = 'close';
        include_once( OTHER_SITE_BIND_PATH . 'qqcom' . DS . 'comm' . DS . 'config.php' );
        empty(self::$_urls['qq']) && self::$_urls['qq'] = $cms_abs . "tools/other_site_sdk/qqcom/oauth/qq_login.php";
        self::$_urls['qq_reauth'] = "{$cms_abs}tools/other_site_sdk/other_site_public_callback.php?type=qq&act=qq_reauth&target=" . urlencode(self::$_urls['qq']);
    }

    /**
     * ��ȡ����SDK��Ȩ��ڶ���������ȨURL
     *
     * @static
     * @since  1.0
     */
    public static function getSinaWeiBoAuthInstance() 
    {
        global $cms_abs, $sina_appid, $sina_appkey, $mcharset, $sina_closed;
        // ���õ�¼���ܹر�ʱ����URLΪclose����
        if( $sina_closed || empty($sina_appid) || empty($sina_appkey) ) return self::$_urls['sina'] = 'close';
        include_once( OTHER_SITE_BIND_PATH . 'weibocom' . DS . 'config.php' );
        require_once( OTHER_SITE_BIND_PATH . 'weibocom' . DS . 'extends_saetv2.ex.class.php' );
        $o = new extendsSaeTOAuthV2( WB_AKEY , WB_SKEY );
        empty(self::$_urls['sina']) && self::$_urls['sina'] = $o->getAuthorizeURL( WB_CALLBACK_URL );
        self::$_urls['sina_reauth'] = "{$cms_abs}tools/other_site_sdk/other_site_public_callback.php?type=sina&act=sina_reauth&target=" . urlencode(WB_CALLBACK_URL);
        return $o;
    }

    /**
     * ��ȡQQ΢����¼��ȨURL
     *
     * @static
     * @since  1.0
    public static function getQQWeiboAuthInstance() {
        global $cms_abs, $qq_appid, $qq_appkey, $qq_closed, $mcharset;
        // ���õ�¼���ܹر�ʱ����URLΪclose����
        if( $qq_closed || empty($qq_appid) || empty($qq_appkey) ) return self::$_urls['qq'] = 'close';
        include_once( OTHER_SITE_BIND_PATH . 'qqcom' . DS . 'Config.php' );
        require_once( OTHER_SITE_BIND_PATH . 'qqcom' . DS . 'Tencent.php' );
        OAuth::init($client_id, $client_secret);
        Tencent::$debug = $debug;
        empty(self::$_urls['qq_weibo']) && self::$_urls['qq_weibo'] = OAuth::getAuthorizeURL( CALLBACK );
    }
     */

    public function __toString() 
    {
        return json_encode(self::$_urls);
    }
}