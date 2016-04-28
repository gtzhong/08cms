<?php
/**
 * ΢�Ų����̨����
 *
 * @author    Wilson
 * @copyright Copyright (C) 2008 - 2014, 08CMS Inc. All rights reserved.
 * @version   1.0
 */

class _08_Admin_Weixin_Plugin extends _08_Plugins_AdminHeader
{
    public function __construct()
    {
        parent::__construct();
		if($re = $this->_curuser->NoBackFunc('tpl')) cls_message::show($re);
        aheader();
        (empty($this->_params['action']) || ($this->_params['action'] == 'init')) && $this->_params['action'] = 'config';
        backnav('weixin', $this->_params['action']);
    }
    
    /**
     * ����ƽ̨����
     */
    public function config()
    {
        if( submitcheck('weixin_submit') )
        {
            $configs = cls_envBase::_POST();
            $mconfigsnew = @$configs['mconfigsnew'];
            if ( !empty($mconfigsnew['weixin_enable']) && 
                 (empty($mconfigsnew['weixin_token']) || empty($mconfigsnew['weixin_appid']) || empty($mconfigsnew['weixin_appsecret'])) )
            {
                cls_message::show('�˺���Ϣ����Ϊ�ա�', M_REFERER);
            }
            if (!empty($mconfigsnew['weixin_qrcode']))
            {
                $mconfigsnew['weixin_qrcode'] = cls_url::save_atmurl($mconfigsnew['weixin_qrcode']);
                if (!empty($this->_mconfigs['ftp_enabled']))
                {
                    $mconfigsnew['weixin_qrcode'] = '<!ftpurl />' . $mconfigsnew['weixin_qrcode'];
                }
            }
    		saveconfig('weixin', $mconfigsnew);
    		adminlog('΢������','΢�Ź���ƽ̨����');
    		cls_message::show('΢���������', M_REFERER);
        }
        tabheader('΢�Ź���ƽ̨���� <span style="color:red;">����������Ϣ��΢�Ź���ƽ̨ ������ģʽ�� �µĽӿ�������Ϣ��Ӧ���ɣ�ʹ�øù��ܱ����ȿ���PHP��CURL��OPENSSL��չ��</span>', 'weixin_form', $this->_url);
        
		trbasic('������ά��ɨ���¼��ע�Ṧ��','mconfigsnew[weixin_login_register]', @$this->_mconfigs['weixin_login_register'],'radio', array('guide' => 'ע���ù��ܱ�����΢����֤�����ʹ�á�'));
        
        _08_Admin_Weixin_Plugin_Header::configUI( $this->_mconfigs ); 
        $title = "������վ΢�Ų˵�";       
        tabfooter('weixin_submit', '����', '&nbsp;&nbsp;&nbsp;&nbsp;<input class="btn use_menu" type="submit" name="weixin_create_menu" value="' . $title . '" onclick="_08cms_layer({type: 2, url:\'' . _08_M_Weixin_Config::getWeixinURL('create_menu') . '\', title: \'' . $title . '\' }); return false;">');
        a_guide('admin_weixin_config');
    }
    
    /**
     * �˵�����
     */
    public function menu()
    {
        $cacheName = 'weixin_menus';
        $configs = cls_envBase::_GET_POST();
        if ( empty($configs['cache_id']) )
        {
            $configs['cache_id'] = '';
        }
        else
        {
            $configs['cache_id'] = trim($configs['cache_id']);
        	$cacheName .= ('_' . $configs['cache_id']);
        }
        $weixin_menus = cls_cache::Read($cacheName, '', '', 0, true);
        if( submitcheck('weixin_submit') )
        {
            foreach ($configs['catalogsnew'] as $key => $value )
            {
                if (isset($weixin_menus[$key]))
                {
                    $weixin_menus[$key] = $value;
                }
            }
            
            cls_CacheFile::Save($weixin_menus, $cacheName, '', 0, true);
    		cls_message::show('�������', M_REFERER);
        }
        
        $weixin_list_menu = _08_Loader::import(_08_EXTEND_PLUGINS_PATH . basename(dirname(dirname(__FILE__))) . '::admin::config');
        if ( empty($weixin_list_menu[$configs['cache_id']]) )
        {
            $weixin_list_menu[$configs['cache_id']] = '��վ΢�Ų˵�';
        }
        tabheader('����' . $weixin_list_menu[$configs['cache_id']] . ' <span style="color:red;">����ע�⣬�����Զ���˵��������¹�ע����������΢�ſͻ��˻��棬��Ҫ24Сʱ΢�ſͻ��˲Ż�չ�ֳ�����</span>', 'weixin_form');
        
        if ( empty($weixin_menus) )
        {
            for($i = 1; $i <= 3; ++$i)
            {
                $ii = $i * 10;
                $weixin_menus[$ii] = array(
                    "caid"=> $ii,
                    "level" => 0,
                    "pid" => 0,
                    "title"=>"",
                    "vieworder"=> 0,
                    "url"=>''
                );
                for($j = ($ii + 1); $j <= ($ii + 5); ++$j)
                {
                    $weixin_menus[$j] = array(
                        "caid"=> $j,
                        "level" => 1,
                        "pid" => $ii,
                        "title"=>'',
                        "vieworder"=> 0,
                        "url"=>''
                    );
                }
            }
        }
        
        $menu_list = array();
        $menu_list['menus'] = array('' => '��վ΢�Ų˵�');        
        $menu_list['menus'] += $weixin_list_menu;        
        $menu_list['menus_ids'] = self::getIDs($configs['cache_id']);
        $menu_list['tip'] = '�����ɱ༭����ģ�͵Ĳ˵���';
        $this->_build->TableTree($weixin_menus, $menu_list);
        $title = '��������' . $weixin_list_menu[$configs['cache_id']];
        tabfooter('weixin_submit', '����', '&nbsp;&nbsp;&nbsp;&nbsp;<input class="btn" type="submit" name="weixin_create_menu" value="' . $title . '" onclick="_08cms_layer({type: 2, url:\'' . _08_M_Weixin_Config::getWeixinURL('create_menu&target=all&cache_id=' . $configs['cache_id']) . '\', title: \'' . $title . '\' }); return false;">');
    }
    
    /**
     * ��ȡ�Զ���Ĳ˵�ID��
     * 
     * @param  string $cache_id ����ID
     * @return array  $menu_ids �˵�ID��
     * 
     * @since  nv50
     */
    private static function getIDs($cache_id)
    {
        _08_FilesystemFile::filterFileParam($cache_id);
        $filename = 'config_' . $cache_id;        
        $menu_ids = _08_Loader::import(_08_EXTEND_PLUGINS_PATH . basename(dirname(dirname(__FILE__))) . '::admin::' . $filename);
        return _08_Documents_JSON::encode($menu_ids, true);
    }
    
    public function init()
    {
        $this->config();
    }
}