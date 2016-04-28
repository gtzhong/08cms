<?php
/**
 * ����HTMLԪ���ࣨ������ģʽ�����ࣩ
 * �������ڽ�һ�����Ӷ���Ĺ��������ı�ʾ����,ʹ��ͬ���Ĺ������̿��Դ�����ͬ�ı�ʾ
 *
 * @author    Wilson
 * @copyright Copyright (C) 2008 - 2013 08CMS, Inc. All rights reserved.
 */

class _08_BuilderHtmls
{
    /**
     * ��ǰ�����߶�����
     *
     * @var object
     */
    private $instance = null;

    /**
     * Ҫ�������������
     *
     * @var array
     */
    private $config = array();

    /**
     * ������ͼ���
     *
     * @param array $config ��������,�����ֱ��У�
     *                      title      string ��������ʾ�ı�������
     *                      formname   string �����ƣ���������Ĭ��Ϊ��ǰ��������
     *                      formurl    string �����ӣ���������Ĭ��Ϊ��ǰURL
     *                      tabletitle string ����������
     *                      showdatas  array  ��ʾ���ݣ�keyΪ��һ�����ݣ�valueΪ�������
     *                      submits    array  ����ť��keyΪ���ƣ�valueΪֵ
     *
     * @since 1.0
     */
    public function buildTable( array $config )
    {
        if( empty($config['formname']) )
        {
            $config['formname'] = strtolower(__FUNCTION__);
        }

        // �����ݴ��ݸ���ͼ
        foreach($config as $key => $value)
        {
            $this->instance->assign($key, $value);
        }

        // ָ����ͼģ��
        return $this->instance->display('tables', '.tpl');
    }

    /**
     * ������selectѡ��
     *
     * @param array $select_config ��������,�����ֱ��У�
     *                             selectname   string selectѡ������
     *                             selectdatas  array  selectѡ����ʾ����������
     *                             selectedkey  mixed  selectѡ��ѡ�е�ֵ
     *                             defulatvalue mixed  selectѡ��Ĭ��ֵ
     *                             selectstr    string select����������Ϣ
     * @param int   $type          select���ͣ�Ĭ��0ΪHTMLĬ�ϵ�select��ǩ��ʽ
     *
     *
     * @since 1.0
     */
    public function buildSelect( array $select_config, $type = 0 )
    {
        $select_config['type'] = $type;
        // �����ݴ��ݸ���ͼ
        foreach($select_config as $key => $value)
        {
            $this->instance->assign($key, $value);
        }
        // ָ����ͼģ��
        return $this->instance->display('select', '.tpl');
    }

    /**
     * ������selectѡ��
     *
     * @param array $config    TableTree��������
     * @param mixed $menu_list TableTree��Ŀ
     *
     * @since 1.0
     */
    public function buildTableTree( array $config, $menu_list = array() )
    {
        // �����ݴ��ݸ���ͼ
        $this->instance->assign('tableTree', $config);
        $this->instance->assign('cms_abs', _08_CMS_ABS);
        $this->instance->assign('menu_list', $menu_list);
        // ָ����ͼģ��
        return $this->instance->display('table_tree', '.tpl');
    }

    public function __call( $name, $arguments )
    {
        $name = 'build' . ucfirst($name);
        if( method_exists($this, $name) )
        {
            if ( $arguments )
            {
                return call_user_func_array(array($this, $name), $arguments);
            }
            else
            {
            	return call_user_func(array($this, $name));
            }
        }
    }

    public function __construct( _08_View $instance )
    {
        $this->instance = $instance;
    }
}