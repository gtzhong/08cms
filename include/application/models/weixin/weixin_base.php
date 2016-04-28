<?php
/**
 * ΢�Žӿڻ���
 *
 * @author    Wilson <Wilsonnet@163.com>
 * @copyright Copyright (C) 2008 - 2013 08CMS, Inc. All rights reserved.
 */

defined('_08CMS_APP_EXEC') || exit('No Permission');
abstract class _08_M_Weixin_Base extends _08_Models_Base
{
    /**
     * ��ȡ��Ȩ��URL
     * 
     * @var string
     */
    protected $_get_access_token_url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=%s&secret=%s';
    
    /**
     * ��ȡ�û�������Ϣ��URL
     * 
     * @var string
     */
    protected $_get_user_info_url = 'https://api.weixin.qq.com/cgi-bin/user/info?access_token=%s&openid=%s&lang=zh_CN';
    
    protected $_access_token = '';
    
    protected $_params = array();
    
    /**
     * ���ݿ��￪������ı�ʾֵ
     * 
     * @var   int
     * @since nv50
     */
    const PLUGIN_ENABLE_VALUE = 1;
    
    /**
     * ���ݿ���رղ���ı�ʾֵ
     * 
     * @var   int
     * @since nv50
     */
    const PLUGIN_CLOSE_VALUE = 0;
    
    protected $_message = '';
    
    public function __construct( $params = array() )
    {
        parent::__construct();
        $this->_params = $params;
        
        if ( !isset($this->_get['cache_id']) && !isset($this->_get['weixin_cache_id']) )
        {
            if ( empty($this->_mconfigs['weixin_token']) || empty($this->_mconfigs['weixin_appid']) || empty($this->_mconfigs['weixin_appsecret']) )
            {
                cls_message::ajax_info('��������վ��̨ ϵͳ���� -> ��վ���� -> ͨ��֤ -> ΢�Ź���ƽ̨���� ������������Ϣ��', 'CONTENT');
            }
        }
        
        if ( empty($this->_params['weixin_fromid']) )
        {
            $this->_message = '';
        }
        else
        {
        	$this->_message = " ( ID: {$this->_params['weixin_fromid']} ) ";
        }
    }
    
    /**
     * �����ȡaccess_token
     * 
     * @param string $weixin_appid     ΢��APPID
     * @param string $weixin_appsecret ΢��AppSecret
     */
    protected function _requestGetAccessToken($weixin_appid, $weixin_appsecret)
    {
		$accessInfo = WeixinAccessInfo($this->_get_access_token_url, $weixin_appid, $weixin_appsecret);
		if(empty($accessInfo)){
			cls_message::ajax_info('����[https://api.weixin.qq.com/cgi-bin/token]ʧ�ܣ�<br>��ȡ����΢�ŷ��������ݣ����ܳ�ʱ��<br>���������������û������������', 'CONTENT');
		}elseif ( isset($accessInfo->access_token) ){
            $this->_access_token = $accessInfo->access_token;
        }else{
            $message = $this->_message . _08_M_Weixin_Error_Message::get(@$accessInfo->errcode);            
        	cls_message::ajax_info($message, 'CONTENT', $this->getNextJumpParams());
        }
    }
    
    /**
     * ��ȡ��һ����ת�Ĳ���
     * 
     * @return string $url ������һ����ת�Ĳ���
     * @since  nv50
     */
    public function getNextJumpParams()
    {
        $jumpParams = array();
        if ( !empty($this->_params['weixin_cache_id']) && isset($this->_get['target']) && strtolower($this->_get['target']) == 'all' )
        {
            isset($this->_params['weixin_fromid_type']) || ($this->_params['weixin_fromid_type'] = '');
            isset($this->_params['weixin_fromid']) || ($this->_params['weixin_fromid'] = 0);
            $next_id = parent::getModels('Weixin_DataBase')->getNextID($this->_params['weixin_fromid_type'], $this->_params['weixin_fromid']);
            
            $uri = "create_menu&cache_id={$this->_params['weixin_cache_id']}&{$this->_params['weixin_fromid_type']}={$next_id}";
            if ( $next_id )
            {
                $jumpParams = array('url' => _08_M_Weixin_Config::getWeixinURL($uri . '&target=all'), 'timeout' => 2);
            }
            else
            {
            	$jumpParams = array('url' => _08_M_Weixin_Config::getWeixinURL($uri . '&target=end'), 'timeout' => 2);
            }
        }
        
        return $jumpParams;
    }
    
    /**
     * ��ȡ��ǰ����΢�ŵ�appid��appsecret
     * 
     * @param  int   $pluginStatusValue �Ƿ��ȡ�����벻������΢��������Ϣ��Ĭ��Ϊ��ȡ����
     * 
     * @return array $config            ���ػ�ȡ����appid��appsecret
     */
    public function getAppIDAndAppSecret( $pluginStatusValue = null )
    {
        $config = array();
        if ( isset($this->_params['weixin_fromid_type']) && !empty($this->_params['weixin_fromid']) )
        {
            $Weixin_Config_Table = parent::getModels('Weixin_Config_Table');
            $Weixin_Config_Table->where(array('weixin_fromid_type' => $this->_params['weixin_fromid_type']))
                                ->_and(array('weixin_fromid' => $this->_params['weixin_fromid']));
//            if (!empty($this->_params['weixin_cache_id']))
//            {
//                $Weixin_Config_Table->_and(array('weixin_cache_id' => $this->_params['weixin_cache_id']));
//            }
            
            if ( !empty($pluginStatusValue) )
            {
                $Weixin_Config_Table->_and(array('weixin_enable' => (int)$pluginStatusValue));
            }
            
            $config = $Weixin_Config_Table->read('weixin_token, weixin_appid, weixin_appsecret, weixin_enable');
        }
        else if ( empty($this->_params['weixin_cache_id']) && empty($this->_get['cache_id']) )
        {
        	$config = array('weixin_token' => $this->_mconfigs['weixin_token'], 
                            'weixin_appid' => $this->_mconfigs['weixin_appid'], 
                            'weixin_appsecret' => $this->_mconfigs['weixin_appsecret'], 
                            'weixin_enable' => $this->_mconfigs['weixin_enable']);
        }
        
        return $config;
    }
    
    /**
     * ��ʽ��POST����
     * 
     * @param  mixed  $postDatas ҪPOST�����ݣ���ʽ�����JSONʱֱ�ӷ��أ�����ʱ�������ת��JSON����
     * @return string            ����JSON��ʽ����
     * 
     * @since  nv50
     */
    public function formatPostDatasToJSON( $postDatas )
    {
        $postDatas = cls_string::iconv(cls_env::getBaseIncConfigs('mcharset'), 'UTF-8', $postDatas);        
        if ( is_array($postDatas) )
        {
            if (version_compare(PHP_VERSION, '5.4.0') >= 0)
            {
                $postDatas = json_encode($postDatas, JSON_UNESCAPED_UNICODE);
            }
            else
            {
            	$postDatas = jsonEncode($postDatas);
            }
        }
        
        return $postDatas;
    }
    
    /**
     * ��ʼ������չϵͳ��Ӧ����
     */
    public function run( $postObj )
    {
        $class = 'Weixin_';
        $MsgType = isset($postObj->MsgType) ? strtolower($postObj->MsgType) : '';
        // Ŀǰֻ��֧����Ϣ���¼���Ӧ���ִ�����
        if ( 'event' === $MsgType )
        {
            $class .= 'Event';
        }
        else
        {
        	$class .= 'Extends_Message';
        }
        
        $instance = parent::getModels($class, $postObj);
        if ( !empty($instance) )
        {
            $method = 'response';
            
            if ( isset($postObj->Event) )
            {
                $method .= ucfirst(strtolower($postObj->Event));
            }
            else
            {
                $method .= ucfirst($MsgType);            	
            }
            
            if ( method_exists($instance, $method) )
            {
                return call_user_func(array($instance, $method));
            }
        }
    }
    
    /**
     * ��ȡ��ǰ�Ự΢������
     * 
     * @return array
     * @since  nv50
     */
    public static function getConfigs( SimpleXMLElement $post )
    {
        $datas = array();
        if ( isset($post->FromUserName) )
        {
            $key = '_' . md5($post->FromUserName);
            $savePath = self::_getCachePath($post->FromUserName);
            $datas = cls_cache::cacRead($key, $savePath);
        }
        
        return (array) $datas;
    }
    
    /**
     * ���õ�ǰ�Ự΢������
     * 
     * @return array
     * @since  nv50
     */
    public static function setConfigs( array $configs, SimpleXMLElement $post )
    {
        if ( isset($post->FromUserName) )
        {
            $key = '_' . md5($post->FromUserName);
            $savePath = self::_getCachePath($post->FromUserName);
            cls_CacheFile::cacSave($configs, $key, $savePath . DS);
            return true;
        }
        
        return false;
    }
    
    /**
     * ��ȡ΢�Ż���·��
     * 
     * @param  string $FromUserName ��ǰ�Ự��Դ�û���
     * @return string $savePath     ����΢�Ż���·��
     * @since  nv50
     */
    protected static function _getCachePath( $FromUserName )
    {
        $savePath = _08_CACHE_PATH . 'excache';
        
        if ( empty($FromUserName) )
        {
            return $savePath;
        }
        
        $key = md5($FromUserName);
        $savePath .= DS . substr($key, 0, 1);
        _08_FileSystemPath::checkPath($savePath, true);
        return $savePath;
    }
}