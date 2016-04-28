<?php
/**
 * ΢�����ݿ�ģ����
 *
 * @author    Wilson <Wilsonnet@163.com>
 * @copyright Copyright (C) 2008 - 2013 08CMS, Inc. All rights reserved.
 */

defined('_08CMS_APP_EXEC') || exit('No Permission');
class _08_M_Weixin_DataBase extends _08_Models_Base
{
    /**
     * ���ݿ����
     * 
     * @var   object
     * @since nv50
     */
    protected $_Weixin_Config_Table = null; 
    
    /**
     * ��ȡ΢������
     * 
     * @param  string $id_type ID����
     * @param  int    $id      ID
     * @return array           ������������
     * 
     * @since  nv50
     */
    public function getConfig( $id_type, $id )
    {
        $id = (int) $id;
        $id_type = preg_replace('/[^\w]/', '', $id_type);
        $row = $this->_Weixin_Config_Table->where(array('weixin_fromid_type' => $id_type))
                                          ->_and(array('weixin_fromid' => $id))
                                          ->read();
        return $row;
    }
    
    /**
     * ����΢������
     * 
     * @param  array $configs Ҫ�������������
     * @return bool           ����ɹ�����TRUE�����򷵻�FALSE
     * 
     * @since  nv50
     */
    public function saveConfig( array $configs )
    {
        if ( empty($configs['weixin_fromid_type']) || empty($configs['weixin_fromid']) )
        {
            cls_message::show('�����Ƿ���', M_REFERER);
        }
        empty($configs['weixin_qrcode']) || ($configs['weixin_qrcode'] = cls_url::save_url($configs['weixin_qrcode']));
        $row = $this->getConfig($configs['weixin_fromid_type'], $configs['weixin_fromid']);
        $Weixin_Config_Table = $this->_Weixin_Config_Table;
        
        if ( empty($row) )
        {
            $updateStatus = $Weixin_Config_Table->create($configs);
        }
        else
        {
            $id_type = $configs['weixin_fromid_type'];
            $id = $configs['weixin_fromid'];
            unset($configs['weixin_fromid_type'], $configs['weixin_fromid']);
            $updateStatus = $Weixin_Config_Table->where(array('weixin_fromid_type' => $id_type))
                                                ->_and(array('weixin_fromid' => $id))
                                                ->update($configs);
        }
        
        return ((bool)$updateStatus ? true : false);
    }
    
    public function getNextID($id_type, $id)
    {
       $row = $this->_Weixin_Config_Table->where(array('weixin_fromid_type' => $id_type))
                                          ->_and(array('weixin_fromid' => $id), '>')
                                          ->order('weixin_fromid ASC')
                                          ->read('weixin_fromid');
        
        return $row['weixin_fromid'];
    }
    
    /**
     * ��ȡ��������
     * 
     * @param  string $cache_id Ҫ��ȡ���õĻ���ID
     * @param  string $limit    ����ƫ����
     * @return array            ����������Ϣ
     * 
     * @since  nv50
     */
    public function getCacheConfig( $cache_id, $limit = '' )
    {
        $this->_Weixin_Config_Table->where(array('weixin_cache_id' => $cache_id))->order('weixin_fromid ASC');
        if ( empty($limit) || $limit == 1 )
        {
            return $this->_Weixin_Config_Table->read();
        }
        else
        {
            @list($limit, $offset) = array_map('trim', explode(',', $limit));
        	$this->_Weixin_Config_Table->limit($limit, $offset)->exec();
        }
        
        $rows = array();
        while($row = $this->_Weixin_Config_Table->fetch())
        {
            $rows[] = $row;
        }
        return $rows;
    }
    
    public function __construct()
    {
        $this->_Weixin_Config_Table = parent::getModels('Weixin_Config_Table');
    }
}