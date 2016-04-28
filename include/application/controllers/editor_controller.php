<?php
/**
 * �༭��������
 *
 * @since     nv50
 * @author    Wilson <Wilsonnet@163.com>
 * @copyright Copyright (C) 2008 - 2014 08CMS, Inc. All rights reserved.
 */
defined('_08CMS_APP_EXEC') || exit('No Permission');
_08_Loader::import(_08_INCLUDE_PATH . 'ck_public_class');
class _08_C_Editor_Controller extends _08_Controller_Base
{
    /**
     * ��չ���·��
     * 
     * @var string
     */
    private $extPluginsPath;
    
    /**
     * ���·��
     * 
     * @var string
     */
    private $pluginsPath;
    
    private $editorPublicClassObject = null;
    
    /**
     * ��ҳ������
     */
    public function paging_management()
    {
        _08_Loader::import($this->pluginsPath . __FUNCTION__);
    }
    
    /**
     * �������飬ֻ�Ƿŵ��ô��룬�����������չϵͳ�ļ�ʱ���������ļ�
     */
    public function hangqing()
    {
        _08_Loader::import($this->extPluginsPath . __FUNCTION__, $this->_get);        
    }
    
    /**
     * ����ͼƬ��ֻ�Ƿŵ��ô��룬�����������չϵͳ�ļ�ʱ���������ļ�
     */
    public function chetu()
    {
        _08_Loader::import($this->extPluginsPath . __FUNCTION__, $this->_get);        
    }
    
    /**
     * ¥����Ϣ��ֻ�Ƿŵ��ô��룬�����������չϵͳ�ļ�ʱ���������ļ�
     */
    public function house_info()
    {
        _08_Loader::import($this->extPluginsPath . __FUNCTION__);        
    }
    
    /**
     * ѡС��ͼ��ֻ�Ƿŵ��ô��룬�����������չϵͳ�ļ�ʱ���������ļ�
     */
    public function plot_pigure()
    {
        _08_Loader::import($this->extPluginsPath . __FUNCTION__);        
    }
    
    /**
     * ѡ����ͼ��ֻ�Ƿŵ��ô��룬�����������չϵͳ�ļ�ʱ���������ļ�
     */
    public function size_chart()
    {
        _08_Loader::import($this->extPluginsPath . __FUNCTION__);        
    }
    
    public function __construct()
    {
        parent::__construct();
        $path = 'classes:ueditor:plugins:';
        $this->pluginsPath = _08_LIBS_PATH . $path;
        $this->extPluginsPath = _08_EXTEND_LIBS_PATH . $path;
    }
}