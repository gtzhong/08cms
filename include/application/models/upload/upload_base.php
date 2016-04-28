<?php
/**
 * �ϴ������ͼģ�ͻ���
 *
 * @author    Wilson <Wilsonnet@163.com>
 * @copyright Copyright (C) 2008 - 2013 08CMS, Inc. All rights reserved.
 */

defined('_08CMS_APP_EXEC') || exit('No Permission');
class _08_M_Upload_Base extends _08_Models_Base
{    
    protected $_userfiles = null;
    
    /**
     * ɾ������
     * 
     * @param string $ufid Ҫɾ����ͼƬID��Ϊ��Ҫ����֮ǰ��ͼƬ���Ե�ID��Ϊ����ʱ��ɾ��
     * @since nv50
     */
    public function delete( $ufid )
    {
//        if ( !is_numeric($ufid) ) return false;
//        
//        $row = $this->_userfiles->where(array('ufid' => $post['ufid']))->read('url', false);
//        if ( $row )
//        {
//            // ����ɹ�ɾ�����ݿ���Ϣ��ɾ���ϴ��ļ�
//            if ( $this->_userfiles->delete() )
//            {
//                # ��ʱ��ɾ���ļ�����ǰͼƬ��������
//               # cls_atm::atm_delete($row['url'], $this->type);
//            }
//        }
        
        return true;
    }
    
    /**
     * ͨ���ĵ����aid��ͼƬ��ַ��ȡ��ͼƬ��ufid
     * 
     * @param  int    $aid    ��ǰ�ĵ�ID
     * @param  string $imgurl ��ǰͼƬ��ַ
     * @return int    $ufid   ������ID
     * 
     * @since  nv50
     */
    public function getUFidForAid( $aid, $imgurl )
    {
        $ufid = $imgurl;
        $aid = (int) $aid;
        $this->_userfiles->select('ufid, url', true)->where(array('aid' => $aid))->exec();
        while($row = $this->_userfiles->fetch())
        {
            if ( $imgurl == $row['url'] )
            {
                $ufid = $row['ufid'];
                break;
            }
        }
        
        return $ufid;
    }
    
    /**
     * ͨ��ufid��ȡuserfiles������
     * 
     * @reutn array $info ���ر�����
     * @since nv50
     */
    public function getInfoForUFid( $ufid, $fields = '*' )
    {
        $ufid = (int) $ufid;
        $info = $this->_userfiles->where(array('ufid' => $ufid))->read($fields);
        return $info;
    }
    
    public function __construct()
    {
        $this->_userfiles = parent::getModels('UserFiles_Table');
    }
}