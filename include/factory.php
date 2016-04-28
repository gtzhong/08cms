<?php
/**
 * ���Ĺ�����
 *
 * @author    Wilson
 * @copyright Copyright (C) 2008 - 2013 08CMS, Inc. All rights reserved.
 */

abstract class _08_factory
{
    /**
     * ��ȡ���ݿ����
     * 
     * @static
     */
    private static $db = array();
    
    /**
     * Ӧ�þ��
     * 
     * @var    array
     * @since  1.0
     * @static
     */ 
	private static $application = array();
    
    private static $instances = array();
    
    /**
     * ��ȡ�����븸��֮���л��Ķ��󣬵����಻����ʱ�Զ�ʵ��������
     * ע������������֮��Ĺ����ǣ���������������ƺ���� '_Base' �� 'Base' ��׺
     * 
     * @param  string $sub_class Ҫʵ����������
     * @param  mixed  $param     Ҫ���ݹ��캯���Ĳ���
     * @return object            ����ʵ��ʵ�����Ķ���
     * 
     * @since  nv50
     */
    public static function getInstance( $sub_class, $param = null )
    {
        # ����_Base��׺�ĸ�������
        $parentClass = $sub_class . '_Base';
        # ����������Ʋ���_Base��׺����������ΪBase��׺
        if ( !class_exists($parentClass) )
        {
            $_parentClass = $parentClass;
            $parentClass = $sub_class . 'Base';
            
            if ( !class_exists($parentClass) )
            {
                die("Fatal error: Class '{$_parentClass}' or '{$parentClass}' not found");
            } 
        }        
                     
        # ��Ĭ��Ҫʵ��������Ϊ����
        $newClass = $parentClass;  
        
        if ( class_exists($sub_class) && is_subclass_of($sub_class, $parentClass) )
        {
            # ����������ʱʵ�������࣬����ʵ��������
            $newClass = $sub_class;
        }
        
        if ( is_null($param) )
        {
            if ( empty(self::$instances[$newClass]) )
            {
                self::$instances[$newClass] = new $newClass();
            }
            
            return self::$instances[$newClass];
        }
        else
        {
        	return new $newClass( $param );
        }
    }
    
    /**
     * ��ȡ��ʶ�������ʵ��
     * 
     * @param  string $tclass ��ʶ����
     * @return object         ����ʵ��
     * 
     * @since  nv50
     */ 
    public static function getMtagsInstance($tclass)
    {
        $class_name = "cls_mtags_$tclass";
        if(class_exists($class_name))
        {
            return new $class_name();
        }  
    }
    
    /**
     * ��ȡӦ�ö���
     * 
     * @param  mixed  $id     �ͻ��˱�ʶ��������
     * @param  array  $config ��������
     * @param  string $prefix Ӧ��ǰ׺
     * 
     * @return object         ����Ӧ�ö�����
     * @since  nv50
     */
    public static function getApplication($id = null, array $config = array(), $prefix = '_08')
    {
        $key = md5( $prefix . $id . serialize( $config ) );
        if ( empty(self::$application[$key]) )
        {
            self::$application[$key] = _08_Application::getInstance($id, $config, $prefix);
        }
        
        return self::$application[$key];
    }
    
    /**
     * ��ȡ���ݿ����д�÷�������չ$db�������������ݿ�����ں�����ʹ��ʱ����global����������global��ʹ�á�
     * 
     * @param  array  $config ���ݿ�����������Ϣ
     *         ��ʽ�� array('dbhost' => $dbhost, 'dbuser' => $dbuser, 'dbpw' => $dbpw, 'dbname' => $dbname, 
     *                      'tblprefix' => $tblprefix, 'pconnect' => $pconnect, 'dbcharset' => $dbcharset)
     * @return object
     * 
     * @since nv50
     */ 
    public static function getDBO( array $config = array() )
    {
        global $db; # ��ʱ������global��ʹ�ã��Ժ������ʹ��general.inc.php��$dbʱ��ȥ����

        # ȥ��global���ģ���ɾ��
        $dbDriversClass = '_08_MysqlQuery';
        if( ($db instanceof $dbDriversClass) && empty($config))
        {
            return $db;
        }
        
        ksort($config);
        $key = md5(serialize($config));
        if( empty(self::$db[$key]) )
        {
            # ����������Ϣ
            if ( empty($config) )
            {
                $config = self::getDBOConfig();
            }
            
            # �������ݿ�
            self::$db[$key] = new $dbDriversClass( $config );
        }
        
        return self::$db[$key];
    }
    
    /**
     * ��ȡDBO����
     * 
     * @return array $config ���ػ�ȡ����DBO����
     * @since  nv50
     */
    public static function getDBOConfig()
    {
        $config = cls_env::getBaseIncConfigs('dbcharset, mcharset, dbhost, drivers, dbport, dbuser, dbpw, dbname, tblprefix, pconnect');
        if ( empty($config['dbcharset']) && in_array(strtolower($config['mcharset']), array('gbk','big5','utf-8')) )
        {
            $config['dbcharset'] = str_replace('-', '', $config['mcharset']);
        }
        empty($config['drivers']) && $config['drivers'] = 'Mysql';
        empty($config['dbport']) && $config['dbport'] = '3306';
        
        return $config;
    }
    
    /**
     * ��ȡ֧�����ض���
     * 
     * @param  string $payType �������ƣ�Ŀǰ֧�֣� alipaydirect -- ֧������ʱ����
     * @return object          ����֧�����ض���
     * 
     * @since  nv50
     */
    public static function getPays($payType)
    {
        $class = _08_Loader::MODEL_PREFIX . ucfirst($payType);
        return self::getInstance($class);
    }
}