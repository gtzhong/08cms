<?php
/**
 * ¥��΢�Ų����̨����
 *
 * @author    Wilson
 * @copyright Copyright (C) 2008 - 2014, 08CMS Inc. All rights reserved.
 * @version   1.0
 */

class _08_Admin_Weixin_Property extends _08_Plugins_AdminHeader
{ 
    public function __construct()
    {
        parent::__construct();
		if($re = $this->_curuser->NoBackFunc('weixin')) cls_message::show($re);
        aheader();
        $this->_get['aid'] = (isset($this->_get['aid']) ? (int) $this->_get['aid'] : 0);
        if ( empty($this->_get['aid']) || empty($this->_get['cache_id']) )
        {
            cls_message::show('�����Ƿ���', M_REFERER);
        }
    }
    
    public function init()
    {
        $configs = cls_envBase::_GET_POST();
        $Weixin_DataBase = parent::getModels('Weixin_DataBase');
        if( submitcheck('weixin_submit') )
        {
            $mconfigsnew = $configs['mconfigsnew'];
            if ( !empty($mconfigsnew['weixin_enable']) &&
                 (empty($mconfigsnew['weixin_token']) || empty($mconfigsnew['weixin_appid']) || empty($mconfigsnew['weixin_appsecret'])) )
            {
                cls_message::show('�˺���Ϣ����Ϊ�ա�', M_REFERER);
            }
            
            $mconfigsnew['weixin_fromid_type'] = 'aid';
            $mconfigsnew['weixin_fromid'] = $this->_get['aid'];
            $mconfigsnew['weixin_cache_id'] = $this->_get['cache_id'];
            if ( isset($mconfigsnew['weixin_url']) )
            {
                unset($mconfigsnew['weixin_url']);
            }
            
            if ( $Weixin_DataBase->saveConfig($mconfigsnew) )
            {
                adminlog('¥��΢������','¥��΢�Ź���ƽ̨����');
    		    cls_message::show('΢�������޸ĳɹ���', M_REFERER); 
            }
    		else
            {
              	cls_message::show('΢�������޸�ʧ�ܣ����Ժ����ԡ�', M_REFERER); 
            }
        }
        $aidString = "&aid={$this->_get['aid']}";
        tabheader('΢�Ź���ƽ̨���� <span style="color:red;">( ID: ' . $this->_get['aid'] . ' ) ��������Ϣ��΢�Ź���ƽ̨ ������ģʽ�� �µĽӿ�������Ϣ��Ӧ����</span>', 'weixin_form', $this->_url . "&cache_id={$this->_get['cache_id']}" . $aidString);
        
        $config = (array) $Weixin_DataBase->getConfig('aid', $this->_get['aid']);
		_08_Admin_Weixin_Plugin_Header::configUI( $config, "init{$aidString}" );
        $title = "����¥�̣�ID��{$this->_get['aid']}��΢�Ų˵�";
        tabfooter('weixin_submit', '����', '&nbsp;&nbsp;&nbsp;&nbsp;<input class="btn" type="submit" name="weixin_create_menu" value="' . $title . '" onclick="_08cms_layer({type: 2, url:\'' . _08_M_Weixin_Config::getWeixinURL("create_menu&cache_id={$configs['cache_id']}{$aidString}") . '\', title: \'' . $title . '\' }); return false;">');
        a_guide('admin_weixin_config');
    }
}