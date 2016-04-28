<?php
/**
 * ���͵�MVCǰ�˿����������࣬ĿǰĿ¼�ݶ�Ϊ��
 * M��/include/application/models
 * ��̨V: /admina/views
 * ǰ̨V: /template
 * C: /include/application/controllers
 * ��������ӻ��޸�Ŀ¼ʱ��ֻҪ�Ѹ�Ŀ¼ע�ᵽ�Զ�����ջ���ɣ�ע�⣺V��Ĳ��������̳���_08_IController�ӿ�
 *
 * @author    Wilson
 * @copyright Copyright (C) 2013, 08CMS Inc. All rights reserved.
 * @version   1.0
 */

class cls_frontController
{
    /**
     * ����������
     *
     * @since 1.0
     */
    protected $_controller = '';

    /**
     * ��������Ϊ
     *
     * @since 1.0
     */
    protected $_action = '';

    /**
     * ��������������
     *
     * @since 1.0
     */
    protected $_params = array();

    /**
     * ��ǰ������
     *
     * @static
     * @since  1.0
     */
    protected static $_instance = null;

    /**
     * ������·��
     * ע����·�ɱ���������������Ż����У�
     * 1���������ڣ�����������Զ����ع���ͼ̳���_08_IController�ӿ�
     * 2������ķ���������URI��action����һ�£�����ò���Ϊ�գ����Զ�����init()����
     *
     * @since  1.0
     */
    public function route()
    {
        $class_name = $this->getControllerClass();
        if( class_exists( $class_name ) )
        {
            $reflection = new ReflectionClass( $class_name );
            // ����Ƿ��Ѿ�ʵ���˸ýӿ�
            if( $reflection->implementsInterface('_08_IController') )
            {
                $action = $this->getAction();
                $hasAction = $reflection->hasMethod( $action );
                _08_Application::$__isNewStructure = true;
                // ��鷽���Ƿ��Ѷ��壬�����������ִ�Сд
                if( $hasAction || $reflection->hasMethod( '__call' ) )
                {
                    try
                    {
                        $controller = $reflection->newInstance();
                        if ( $hasAction )
                        {
                            $method = $reflection->getMethod( $action );
                            $method->invoke( $controller );
                        }
                        else
                        {
                        	$method = $reflection->getMethod( '__call' );
                            $method->invoke( $controller, $action, null );
                        }
    
                        # �Զ���һ�� __end ħ���������ÿ��������ƺ�������������������
                        if ( $reflection->hasMethod( '__end' ) )
                        {
                            $endMethod = $reflection->getMethod( '__end' );
                            $endMethod->invoke( $controller );
                        }
                    }
                    catch (ReflectionException $error)
                    {
                        throw new _08_ApplicationException($error->getMessage());
                    }
                }
            }
            else
            {
                _08_Application::$__isNewStructure = false;
            	throw new _08_ApplicationException('The controller must inherit Interface : _08_IController');
            }
        }
        else if ( self::checkActionMVC() )
        {
            _08_Application::$__isNewStructure = false;
            throw new _08_ApplicationException('Controller Not Found!');
        }
        
        $this->__doPluginsAction();
    }
    
    /**
     * ��ȡ������������
     * 
     * @return string $class_name ���ؿ����������������
     * @since  1.0
     */
    private function getControllerClass()
    {        
        // ֻ����֮ǰ�Զ������������������������
        $class_name = $this->getController();
        if ( defined('M_ADMIN') )
        {
            if( class_exists( 'cls_' . $class_name ))
            {
                $class_name = 'cls_' . $class_name;
            }
            else if( class_exists('_08_' . $class_name) )
            {
                $class_name = '_08_' . $class_name;
            }
            else
            {
            	$class_name = '_08_C_Admin_' . ucfirst($class_name) . '_Controller';
            }
        }
        else
        {
        	$class_name = '_08_C_' . ucfirst($class_name) . '_Controller';
        }
        
        return $class_name;
    }

    /**
     * ��ȡ������
     *
     * @return string ���ؿ���������
     * @since  1.0
     */
    public function getController()
    {
        return $this->_controller;
    }

    /**
     * ��ȡ��������Ϊ
     *
     * @return string ��ȡ��������Ϊ
     * @since  1.0
     */
    public function getAction()
    {
        return $this->_action;
    }

    /**
     * ��ȡ����������
     *
     * @return array ��ȡ����������
     * @since  1.0
     */
    public function getParams()
    {
        return $this->_params;
    }

    public static function getInstance(array $params = array())
    {
        if( !(self::$_instance instanceof self) )
        {
            self::$_instance = new self($params);
        }
        
        return self::$_instance;
    }

    private function __clone() {}

    /**
     * ����·�ɲ���
     *
     * @since 1.0
     */
    private function __construct( array $params = array() )
    {
        if ( empty($params) )
        {
            $params = cls_env::_GET_POST();
        }
        
        if ( defined('M_ADMIN') )
        {
            $this->adminRoute($params);
        }
        else
        {
        	$this->siteRoute($params);
        }
    }
    
    /**
     * ��̨·��
     */
    private function adminRoute(array $params)
    {
        if( empty( $params ) || empty( $params['entry'] ) ) return false;
        if( $params['entry'] == 'extend' && isset( $params['extend'] ) )
        {
            $this->_controller = $params['extend'];
        }
        else
        {
            $this->_controller = trim( $params['entry'] );
        }

        if( empty( $params['action'] )  )
        {
            $params['action'] = 'init';
        }
        else
        {
            $params['action'] = trim( $params['action'] );
        }

        $this->_action = $params['action'];
        $this->_params = $params;
    }
    
    /**
     * ����Ƿ�ִ��MVC�ܹ�
     */
    public static function checkActionMVC()
    {
        if ( isset($_SERVER['QUERY_STRING']) )
        {
            if ( 0 === strpos($_SERVER['QUERY_STRING'], '/') )
            {
                return true;
            }
        }        
        
        return false;
    }
    
    /**
     * ǰ̨·������
     */
    private function siteRoute(array $_params)
    {
        // ��ʱ��URI��ͷΪ /?/ �Ĺ���ʹ���¼ܹ����Ȳ�����CLI�������
        if ( self::checkActionMVC() )
        {
            if ( false !== strpos($_SERVER['QUERY_STRING'], '?') )
            {
                $_SERVER['QUERY_STRING'] = str_replace('?', '', $_SERVER['QUERY_STRING']);
            }
            $_SERVER['QUERY_STRING'] = str_replace(array('&', '='), '/', $_SERVER['QUERY_STRING']);

            //$queryString = rawurldecode($_SERVER['QUERY_STRING']);
			$queryString = urldecode($_SERVER['QUERY_STRING']);
            $queryString .= (substr($queryString, strlen($queryString) - 1) == '/' ? '' : '/');
            $request_uris = explode('/', $queryString);
            unset($request_uris[0]);
            if ( isset($request_uris[1]) )
            {
                _08_FileSystemPath::filterPathParam($request_uris[1]);
                $this->_controller = $request_uris[1];
                unset($request_uris[1]);
            }
            else
            {
            	$this->_controller = 'index';
            }
            
            if ( !empty($request_uris[2]) && !in_array(substr($request_uris[2], 0, 1), array('?', '&')) )
            {
                _08_FileSystemPath::filterPathParam($request_uris[2]);
                $this->_action = $request_uris[2];
                unset($request_uris[2]);
            }
            else
            {
            	$this->_action = 'init';
            }
            
            $params = array(); $prevValue = '';
            foreach ( $request_uris as $key => $value ) 
            {
                if ( $key % 2 == 0 && $prevValue )
                {
                    if (preg_match('/^(\w+)\[(\w+)\]$/i', $prevValue, $prevKeys))
                    {
                        $params[$prevKeys[1]][$prevKeys[2]] = addslashes($value);
                    }
                    else
                    {
                    	$params[$prevValue] = addslashes($value);
                    }
                }
                $prevValue = $value;
            }
			#���notify_time��������֧������ʱ���� ��'+'ת��''
			if(isset($params['notify_time']) && !empty($params['notify_time']))
			{
				$params['notify_time'] = urldecode($params['notify_time']);
			}	
            
            $this->_params = $params + $_params;
        }
        else
        {
        	$this->_params = $_params;
        }
    }
    
    /**
     * ִ�в������
     * 
     * @since nv50
     */
    private function __doPluginsAction()
    {
        if ( defined('M_ADMIN') && isset($this->_params['entry']) )
        {
            $uri = $this->_params['entry'];
            if ( isset($this->_params['action']) )
            {
                $uri .= '.' . $this->_params['action'];
            }
            _08_Plugins_Base::getInstance()->trigger('admin.' . $uri);
        }
    }
}