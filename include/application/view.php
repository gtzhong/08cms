<?php
/**
 * ��ͼ
 * {@link http://docs.php.net/manual/zh/class.arrayobject.php}
 *
 * @author    Wilson
 * @copyright Copyright (C) 2013, 08CMS Inc. All rights reserved.
 * @version   1.0
 */

defined('_08CMS_APP_EXEC') || exit('No Permission');
class _08_View extends ArrayObject implements _08_IView
{
    private $array;

    public function __construct()
    {
        if ( class_exists('cls_frontController') )
        {
            $front = cls_frontController::getInstance();
            $this->array = $front->getParams();
        }
        else
        {
        	$this->array = array();
        }
    }

    /**
     * �ѱ���������ͼ��������ģ�����
     *
     * @param string $key   ��������
     * @param mixed  $value ����ֵ
     *
     * @since 1.0
     */
    public function assign( $keys, $value = '' )
    {
        if ( is_array($keys) )
        {
        	foreach ( $keys as $key => $value )
            {
                $this->array[$key] = $value;
            }
        }
        else if ( is_string($keys) )
        {
        	$this->array[$keys] = $value;
        }
    }

    /**
     * ����ģ��
     *
     * @param string $file_name Ҫ�����ģ������
     * @since 1.0
     */
    public function display( $file_name, $ext = '.html', $path = _08_V_PATH )
    {        
        parent::__construct( $this->array, ArrayObject::ARRAY_AS_PROPS );
        
        $file = ($path . strtolower(str_replace(':', DIRECTORY_SEPARATOR, $file_name) . $ext));
        if ( is_file($file) )
        {
            return include_once $file;
        }
    }
}