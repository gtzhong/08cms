<?php
/**
 * ��־ϵͳ������
 * 
 * @package     08CMS.Platform
 * @subpackage  Log
 * @author      Wilson <Wilsonnet@163.com>
 * @copyright   Copyright (C) 2008 - 2013 08CMS, Inc. All rights reserved.
 * @example     # �õ��÷Ǳ��룬'types' => 'database' Ϊ����־�洢�����ݿ⣨ע���ù���Ŀǰδʵ�֣���Ĭ�ϸ�ֵΪ 'file'
 *              _08_Log::addLogger( array('category' => '���') );  
 *              _08_Log::add('������־����');    # һ�����ֻ���ø���伴��
 */
defined('M_COM') || exit('No Permisson');
define('_08_LOG_PATH', _08_CORE_API_PATH . 'log' . DS);

class _08_Log
{
    /**
     * ��ǰ�������
     *
     * @var   object
     * @since 1.0
     */
    protected static $_instance = null;
    
    /**
     * ��������Ϣ
     * 
     * @var   int
     * @since 1.0
     */
    const EMERGENCY = 1;
    
    /**
     * ��������Ϣ
     * 
     * @var   int
     * @since 1.0
     */
    const ALERT = 2;
    
    /**
     * Σ������Ϣ
     * 
     * @var   int
     * @since 1.0
     */
    const CRITICAL = 4;
    
    /**
     * ��������Ϣ
     * 
     * @var   int
     * @since 1.0
     */     
    const ERROR = 8;
    
    /**
     * ��������Ϣ
     * 
     * @var   int
     * @since 1.0
     */    
    const WARNING = 16;
    
     /**
     * ע������Ϣ
     * 
     * @var   int
     * @since 1.0
     */
    const NOTICE = 32;
    
     /**
     * ��Ϣ����Ϣ
     * 
     * @var   int
     * @since 1.0
     */   
    const INFO = 64;
    
    /**
     * ��������Ϣ
     * 
     * @var   int
     * @since 1.0
     */
    const DEBUG = 128;
    
    /**
     * ������������Ϣ��������PHP�����E_ALL����
     * {@link http://php.net/manual/en/errorfunc.constants.php}
     */
    const ALL = 30719;
    
    protected $_lookup = array();
    
    protected $_loggers = array();
    
    protected $_configs = array();
    
    /**
     * �����־
     * 
     * @param mixed  $message  ��־��Ϣ������Ϣ����
     * @param int    $level    ��Ϣ����
     * @param string $category ��־��Ϣ����
     * @param int    $date     ���ڣ������ֵΪnullʱ������Ϊ��ǰ����
     * 
     * @since 1.0
     */
    public static function add( $message, $level = self::INFO, $category = 'syslog', $date = null )
    {        
        if ( !($message instanceof _08_Log_Message) )
		{
			$message = new _08_Log_Message( (string) $message, $level, $category, $date );
		}
        
        if ( !(self::$_instance instanceof self) )
		{
			self::setInstance(new self);
		}
        
        # ����ⲿδ����ִ�е���־�����Զ���ʼ��һ����־�࣬Ĭ�ϰ���־��ŵ��ļ���
        if ( empty(self::$_instance->_lookup) )
        {
            self::addLogger( array('types' => 'file'), $level, array($category) );
        }
        
        self::$_instance->addLogMessage( $message );
    }
    
    /**
     * �����־��Ϣͷ
     * 
     * @param array $options    ������Ϣ
     * @param int   $level      ��Ϣ����
     * @param array $categories ��־��������
     * 
     * @since 1.0
     */
    public static function addLogger( array $options, $level = self::ALL, $categories = array() )
    {
        if ( !(self::$_instance instanceof self) )
		{
			self::setInstance(new self);
		}
        
        # ���δ����Ҫʹ�õ�ͷ��׺��Ĭ��һ��
        if ( empty($options['types']) )
		{
			$options['types'] = 'file';
		}
        
        $signature = md5(serialize($options));
        
        if ( empty(self::$_instance->_configs[$signature]) )
		{
			self::$_instance->_configs[$signature] = $options;
		}
        
        self::$_instance->_lookup[$signature] = (object) array(
			'level' => $level,
			'categories' => array_map('strtolower', (array) $categories)
        );
    }
    
    /**
     * ��ʼ�����־��Ϣ
     * 
     * @param object $message ��־��Ϣ������{@see _08_LogMessage}
     */
    public function addLogMessage( _08_Log_Message $message )
    {
		$loggers = $this->findLoggers($message->__level, $message->__category);
        
        foreach ( (array) $loggers as $signature )
        {
            if ( empty($this->_loggers[$signature]) )
            {
                $class_name = '_08_Logger_To_' . ucfirst($this->_configs[$signature]['types']);                
                if ( class_exists($class_name) )
				{
				    # ������־����󲢴���������Ϣ
					$this->_loggers[$signature] = new $class_name($this->_configs[$signature]);
				}
				else
				{
					throw new _08_Log_Exception('�޷�������־��¼��Ϣͷ����');
				}
            }
            
            $this->_loggers[$signature]->addMessage($message);
        }
    }
    
    /**
     * Ѱ����־��¼��Ϣͷ
     * 
     * @param  int    $level    ��־����
     * @param  string $category ��־����
     * @return array  $loggers  ������־��¼��Ϣͷ����
     * 
     * @since  1.0
     */
    public function findLoggers( $level, $category )
    {
        $loggers = array();
        $level = (int) $level;
        $category = strtolower($category);
        
        foreach ((array) $this->_lookup as $signature => $rules)
        {
            if ($level & $rules->level)
            {
				if ( empty($category) || empty($rules->level) || in_array($category, $rules->categories) )
				{
					$loggers[] = $signature;
				}
            }
        }
        
        return $loggers;
    }
    
    /**
     * ���õ�ǰ�������
     *
     * @param object $instance �������
     * @since 1.0
     */
    public static function setInstance( $instance )
    {
        if( ($instance instanceof self) || (null == $instance) )
        {
            self::$_instance = & $instance;
        }
    }

    /**
     * ��ֹ�ⲿʵ����
     *
     * @since 1.0
     */
    protected function __construct() {}
    
    /**
     * ��ֹ���󱻿�¡
     *
     * @since 1.0
     */
    protected function __clone() {}
}