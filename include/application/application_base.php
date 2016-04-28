<?php
/**
 * Ӧ�ü��ϻ���
 *
 * @author    Wilson <Wilsonnet@163.com>
 * @copyright Copyright (C) 2008 - 2013 08CMS, Inc. All rights reserved.
 */
 
defined('_08CMS_APP_EXEC') || exit('No Permission');
abstract class _08_Application_Base
{    
    /**
     * ǰ�˿��������
     * 
     * @var   object
     * @since 1.0
     */
    protected $_front = null;
    
    /**
     * HTTP������
     * 
     * @var   object
     * @since 1.0
     */
    protected $_request = null;
    
    /**
     * ϵͳ���ò�����Ϣ����
     * 
     * @var   array
     * @since 1.0
     */
    protected $_mconfigs = array();
    
    /**
     * ��ǰ������
     * 
     * @var   string
     * @since 1.0
     */
    protected $_controller = '';
    
    /**
     * MVCģ�Ͷ�������
     *
     * @var   array
     * @since 1.0
     */
    protected static $_models = array();
    
    /**
     * ��ǰURL
     * 
     * @var   string
     * @since 1.0
     */
    protected $_currentUrl = '';
    
    /**
     * ��ǰִ�ж���
     * 
     * @var   string
     * @since 1.0
     */
    protected $_action = '';
    
    /**
     * ��ǰGET��POST��������
     * 
     * @var   array
     * @since 1.0
     */
    protected $_get = array();
    
    const MODEL_PREFIX = '_08_M_';
    
    protected $_curuser = null;
    
	protected $_mcharset = ''; //ajax�ȴ�����,����ʹ�����
    
    public function __construct()
    {
        $this->_curuser = cls_UserMain::CurUser();
        $this->_front = cls_frontController::getInstance();
        $this->_controller = $this->_front->getController();
        $this->_action = $this->_front->getAction();
        $this->_get = $this->_front->getParams();
        $this->_currentUrl = cls_url::create(array($this->_controller => $this->_action));
        $this->_request = new _08_Http_Request();
        $this->_mconfigs = cls_cache::Read('mconfigs');
		$this->_mcharset = cls_env::getBaseIncConfigs('mcharset');
    }

    /**
     * ��ȡģ�Ͷ���
     *
     * @param string $_name  ģ������
     * @param mixed  $params ģ�͹��캯������
     * @since nv50
     */
    public static function getModels( $_name, $params = null )
    {
        if ( is_object($params) )
        {
            $key = md5( $_name . (string) $params );
        }
        else
        {
        	$key = md5( $_name . serialize( (array) $params ) );
        }
        
        if ( empty(self::$_models[$key]) )
        {
            $modelClass = self::MODEL_PREFIX . $_name;
            if ( is_null($params) )
            {
                self::$_models[$key] = new $modelClass();
            }
            else
            {
            	self::$_models[$key] = new $modelClass($params);
            }        	
        }

        return self::$_models[$key];
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