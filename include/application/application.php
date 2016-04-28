<?php
/**
 * Ӧ�ü��ϲ�����
 *
 * @author    Wilson <Wilsonnet@163.com>
 * @copyright Copyright (C) 2008 - 2013 08CMS, Inc. All rights reserved.
 */

defined('_08CMS_APP_EXEC') || define('_08CMS_APP_EXEC', true);
class _08_Application
{
    /**
     * �Ƿ�ʹ���¼ܹ�
     * 
     * @var   bool
     * @since 1.0
     */
    public static $__isNewStructure = false;
    
    /**
     * Ӧ�þ����
     * 
     * @var   array 
     * @since 1.0
     */
    protected static $_instances = array();
    
    /**
     * ��ȡӦ�ö���
     * 
     * @param  mixed  $client �ͻ��˱�ʶ��������
     * @param  array  $config ��������
     * @param  string $prefix Ӧ��ǰ׺
     * 
     * @return object         ����Ӧ�ö������������ȡ���ɹ�����null
     * @since  1.0
     */
    public static function getInstance($client, $config = array(), $prefix = '_08')
    {
        $key = md5($prefix . $client . serialize( array($config) ));
        try
        {
            /**
             * ʹ��ǰ�˿�����(MVC�ܹ�ģʽ)����Ӧ�ã�һ�����ڴ��������CLI��ʽ�򿪵�Ӧ��
             */
            if ( is_null($client) )
            {
                self::$_instances[$key] = new self();
                $front = cls_frontController::getInstance();
                $front->route();
            }
            else
            { 
                /**
                 * �Զ��巽ʽ����Ӧ�ã�һ������������CLI֮��Ŀ���̨ʹ�ã�ע���÷�ʽʹ����Դ��Ƚ��٣�
                 * ������ʱ����Ҫ��ô����Դ����ʱ��Ӧ�ÿ��ø÷�ʽ���룩
                 */
            	if (empty(self::$_instances[$key]))
                {
                    $class_name = $prefix . $client;
                    if ( class_exists($class_name) )
                    {
                        self::$_instances[$key] = new $class_name($config);
                    }
                    else 
                    {
                        cls_HttpStatus::trace(500);
                        self::$_instances[$key] = null;
                    }
                }
            }
        }
        catch (_08_ApplicationException $error)
        {
            die($error->getMessage());
        }    
        
        return self::$_instances[$key];
    }
    
    /**
     * �����ж��Ƿ�ʹ���¼ܹ�
     */
    public function run()
    {
        if ( self::$__isNewStructure )
        {
            return true;
        }
        
        return false;
    }
}