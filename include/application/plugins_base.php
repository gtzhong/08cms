<?php
/**
 * ����ܹ�����
 *
 * @author    Wilson <Wilsonnet@163.com>
 * @copyright Copyright (C) 2008 - 2014 08CMS, Inc. All rights reserved.
 */

defined('_08CMS_APP_EXEC') || exit('No Permission'); 
class _08_Plugins_Base
{
    /**
     * ����������Ӷ�����
     * 
     * @var array
     */
    protected static $_listeners = array();
    
    /**
     * ����б�
     * 
     * @var array
     */
    protected static $_plugins = array();
    
    private static $instance = null;
    
    /**
     * ��ȡ�������
     * 
     * @return array             ��Ų������
     * 
     * @since  nv50
     */
    public function getPluginsData()
    {
        self::$_plugins = cls_cache::Read('plugins');
        $updatetime = @filemtime(_08_PLUGINS_PATH);
        if ( empty(self::$_plugins) || ($updatetime != @self::$_plugins['updatetime']) )
        {
            self::$_plugins = array();
            _08_FileSystemPath::map(array($this, '_getPluginsData'), _08_PLUGINS_PATH, false);
            _08_FileSystemPath::map(array($this, '_getPluginsData'), _08_EXTEND_PLUGINS_PATH, false);
            self::$_plugins['updatetime'] = $updatetime;
            cls_CacheFile::Save(self::$_plugins, 'plugins');
        }
        
        return self::$_plugins;
    }
    
    /**
     * ��ȡ�����Ϣ
     * ��ʱ�ñ���Ŀ¼����ȡ�����ڵ�Ҫ�ں�̨�༭ʱ�����Ƿ������ݿ⡣��
     * 
     * @param object $iterator Ҫ��ȡ�Ĳ��Ŀ¼����������
     * @since nv50
     */
    public function _getPluginsData( DirectoryIterator $iterator )
    {
        $_plugins = array();
        if ( $iterator->isDir() && !$iterator->isDot() )
        {
            $file = _08_FilesystemFile::getInstance();
            if ( is_file($iterator->getPathname() . '_plugin.php') )
            {
                $pluginFile = ($iterator->getPathname() . '_plugin.php');
            }
            else
            {
            	$pluginFile = $iterator->getPathname() . DS . $iterator->getBasename() . '_plugin.php';
            }
            
            if ( $file->_fopen($pluginFile, 'r') )
            {
                $contents = $file->_fread(0, true);
                if ( preg_match("/Plugin\s+Name\s*:*(.*)$/im", $contents, $plugin_name) )
                {
                    $plugin_name[1] = trim($plugin_name[1]);
                    
                    $_plugins['Name'] = $plugin_name[1];
                    $_plugins['Id'] = $iterator->getBasename();
                    foreach ( array('Version', 'Author', 'Description') as $_name ) 
                    {
                        $_plugins[$_name] = '';
                    }
                    
                    if ( preg_match("/Description\s*:*(.*)$/im", $contents, $description) )
                    {
                        $_plugins['Description'] = trim($description[1]);
                    }
                    
                    if ( preg_match("/Author\s*:*(.*)$/im", $contents, $author) )
                    {
                        $_plugins['Author'] = trim($author[1]);
                    }
                    
                    if ( preg_match("/Version\s*:*(.*)$/im", $contents, $author) )
                    {
                        $_plugins['Version'] = trim($author[1]);
                    }
                    
                    $_plugins['Enable'] = false;
                    if ( preg_match("/Enable\s*:*(.*)$/im", $contents, $author) )
                    {
                        $_plugins['Enable'] = (strtolower(trim($author[1])) == 'yes' ? 'true' : 'false');
                    }
                    
                    if ( $_plugins['Enable'] )
                    {
                        self::$_plugins['plugins']['Yes'][$_plugins['Id']] = $_plugins;
                    }
                    else
                    {
                    	self::$_plugins['plugins']['No'][$_plugins['Id']] = $_plugins;
                    }
                }
            }
        }
    }
    
    /**
     * ��ȡ����Ĳ��
     * 
     * @return array ���ز���ӿڶ�������
     * @since  nv50
     */
    protected function _getActivePlugins()
    {
        if ( empty(self::$_listeners['classes']) )
        {
            self::$_listeners['classes'] = array();
            if ( !isset(self::$_plugins['plugins']['Yes']) )
            {
                self::$_plugins = $this->getPluginsData();
            }
            
            # �������õĲ�����ļ�
            if ( isset(self::$_plugins['plugins']['Yes']) )
            {
                foreach ( (array) self::$_plugins['plugins']['Yes'] as $plugin ) 
                {
                    if ( isset($plugin['Id']) )
                    {
                        if ( !_08_Loader::import(_08_EXTEND_PLUGINS_PATH . $plugin['Id'] . DS . $plugin['Id'] . '_plugin') )
                        {
                            _08_Loader::import(_08_EXTEND_PLUGINS_PATH . $plugin['Id'] . '_plugin');
                        }
                        
                        if ( !_08_Loader::import(_08_PLUGINS_PATH . $plugin['Id'] . DS . $plugin['Id'] . '_plugin') )
                        {
                            _08_Loader::import(_08_PLUGINS_PATH . $plugin['Id'] . '_plugin');
                        }
                    }
                }
            }
            
            foreach ( get_declared_classes() as $class ) 
            {
                $reflectionClass = new ReflectionClass($class);
                if ( $reflectionClass->implementsInterface('_08_IPlugins') )
                {
                    $parentClass = $reflectionClass->getParentClass();
                    if ( is_object($parentClass) && ($parentClass->getName() != '_08_Controller_Base') )
                    {
                        self::$_listeners['classes'][$class] = $reflectionClass;
                    }
                }
            }
        }
        
        return self::$_listeners;
    }
    
    /**
     * ע����
     * 
     * @param string $hook     �Ѳ��ע�ᵽ�ù�����
     * @param mixed  $callback �ڸù�����Ҫִ�еķ���
     * 
     * @since nv50
     */
    public static function register( $hook, $callback )
    {
        /**
         * ����ò�����̳���չ�����������
         * ע���������������Ϊ parentClass_Sub ����_Sub��׺
         **/
        if (is_array($callback) && isset($callback[0]))
        {
            $subClass = $callback[0] . '_Sub';
            if (class_exists($subClass))
            {
                $callback[0] = $subClass;
            }
        }
        
        self::$_listeners['methods'][$hook][] = $callback;
    }
    
    /**
     * ����Hook�����в��
     * 
     * @param string $hook  Ҫ�����ã����ӣ������в��
     * @since nv50
     */
    public function trigger( $hook )
    {
        $contents = array();
        $hooks = $this->_getActivePlugins();
        if ( isset($hooks['methods'][$hook]) && self::checkInterface($hooks['classes'], $hook) )
        {
            foreach ( (array)$hooks['methods'][$hook] as $method )
            {
                if ( is_array($method) )
                {
                    if ( is_object($method[0]) )
                    {
                        $method[0] = get_class($method[0]);
                    }
                    if ( isset($hooks['classes'][$method[0]]) )
                    {
                        $hookReflection = $hooks['classes'][$method[0]];
                        $contents = array_merge($contents, (array) $this->__trigger($hookReflection, $method[1]));   
                        unset($hooks['classes'][$method[0]]);
                    }            
                }
                else
                {
                	foreach ( (array) $hooks['classes'] as $hook ) 
                    {
                        $contents = array_merge($contents, $this->__trigger($hook, $method));
                    }
                }
            }
        }
        
        return $contents;
    }
    
    /**
     * �����������
     * 
     * @param  object $hook   Ҫ�����Ĺ��ӽӿ�
     * @param  string $method ע�ᵽ������ķ���
     * @return mixed  $items  ���ط���ִ�к�ķ�������
     * 
     * @since  nv50
     */
    private function __trigger( ReflectionClass $hookReflection, $method )
    {
        $items = array();
        if ( $hookReflection->hasMethod($method) )
        {
            $reflectionMethod = $hookReflection->getMethod($method);
            if ( $reflectionMethod->isStatic() )
            {
                $items = $reflectionMethod->invoke(null);
            }
            else
            {
            	$hookInstance = $hookReflection->newInstance();
                $items = $reflectionMethod->invoke($hookInstance);
            }
        }
        
        return $items;
    }
    
    /**
     * ���ӿ�
     * 
     * ��֤�����Ĳ���������̳������ض��Ľӿڣ�
     * ǰ̨�������̳У�     _08_Plugins_SiteHeader   ��
     * ��̨�������̳У�     _08_Plugins_AdminHeader  ��
     * ��Ա���Ĳ������̳У� _08_Plugins_MemberHeader ��
     * ��Ա�ռ�������̳У� _08_Plugins_MspaceHeader ��
     * �ֻ���������̳У�   _08_Plugins_MobileHeader ��
     * 
     * @param  array  Ҫ���Ĳ��������
     * @param  string Ҫ�����Ĺ���
     * @return bool   �����ͨ���ķ������򷵻�TRUE�����򷵻�FALSE
     * 
     * @since  nv50
     */
    private static function checkInterface( array &$pluginReflectionClass, $hook )
    {
        @list($namespace, $action) = explode('.', (string) $hook);
        $namespace = strtolower($namespace);
        $parentClass = '_08_Plugins_' . ucfirst($namespace) . 'Header';
        #if ( in_array($namespace, array('admin', 'site', 'member', 'mspace', 'mobile')) )
        if ( $namespace )
        {
            foreach ( $pluginReflectionClass as &$class ) 
            {
                if ( !$class->isSubclassOf($parentClass) )
                {
                    unset($class);
                }
            }
            
            if ( !empty($pluginReflectionClass) )
            {
                return true;
            }
        }
        
        return false;     
    }
    
    public static function getInstance()
    {
        if ( !(self::$instance instanceof self) )
        {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    protected function __construct()
    {
        self::$_plugins = array();
    }
    
    private function __clone(){}
}