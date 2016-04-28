<?php
/**
 * ΢���Զ���˵��ӿ�ģ��
 * 
 * �ýӿڿ���΢�ŷ���˷��Ͳ˵���������������鿴��{@link http://mp.weixin.qq.com/wiki/index.php?title=%E8%87%AA%E5%AE%9A%E4%B9%89%E8%8F%9C%E5%8D%95%E6%8E%A5%E5%8F%A3}
 *
 * @author    Wilson <Wilsonnet@163.com>
 * @copyright Copyright (C) 2008 - 2013 08CMS, Inc. All rights reserved.
 */

defined('_08CMS_APP_EXEC') || exit('No Permission');
class _08_M_Weixin_Custom_Menu_Base extends _08_M_Weixin_Base
{    
    protected $_urlFormat = 'https://api.weixin.qq.com/cgi-bin/menu/%s?access_token=%s';
    
    public function __construct( array $params = array() )
    {
        parent::__construct($params);
        $weixin_config = $this->getAppIDAndAppSecret( parent::PLUGIN_ENABLE_VALUE );
        
        if ( empty($weixin_config) || empty($weixin_config['weixin_enable']) )
        {
            cls_message::ajax_info("��{$this->_message}΢�Ź���ƽ̨δ���û�����δ���档", 'CONTENT', $this->getNextJumpParams());
        }
                
        $this->_requestGetAccessToken($weixin_config['weixin_appid'], $weixin_config['weixin_appsecret']);
    }
    
    /**
     * �����˵���ͨ��POSTһ���ض��ṹ�壬ʵ����΢�ſͻ��˴����Զ���˵�
     * 
     * @param array $menuInfo ҪPOST�����ݣ������뿴{@link http://mp.weixin.qq.com/wiki/index.php?title=%E8%87%AA%E5%AE%9A%E4%B9%89%E8%8F%9C%E5%8D%95%E6%8E%A5%E5%8F%A3#.E8.8F.9C.E5.8D.95.E5.88.9B.E5.BB.BA}
     * @example  # ���÷�ʽһ
     *          $Weixin_Custom_Menu = parent::getModels('Weixin_Custom_Menu');
                $Weixin_Custom_Menu->Create(
                    array( 'button' => 
                       array(
                           array('type' => 'click', 'name' => '���ո���', 'key' => 'V1001_TODAY_MUSIC'),
                           array('type' => 'view', 'name' => '���ּ��', 'url' => 'http://www.08cms.com/'),
                           array(
                               'name' => '�˵�', 
                               'sub_button' => array(
                                    array('type' => 'click', 'name' => 'hello word', 'key' => 'V1001_HELLO_WORLD'),
                                    array('type' => 'click', 'name' => '��һ������', 'key' => 'V1001_GOOD')
                               )
                           ),
                       )
                   )
                );
                
                # ���÷�ʽ����
                $Weixin_Custom_Menu->Create(
                       <<<JSON
                        {
                        	"button":[
                        	{
                        		"type":"click",
                        		"name":'���ո���',
                        		"key":"V1001_TODAY_MUSIC"
                        	},
                        	{
                        		"type":"view",
                        		"name":'���ּ��',
                        		"url":"http://www.08cms.com/"
                        	},
                        	{
                        		"name":'�˵�',
                        		"sub_button":[
                        		{
                        			"type":"click",
                        			"name":"hello word",
                        			"key":"V1001_HELLO_WORLD"
                        		},
                        		{
                        			"type":"click",
                        			"name":'��һ������',
                        			"key":"V1001_GOOD"
                        		}]
                        	}]
                        }
JSON
                );
     * 
     * @return object ����json_decodeת������״̬��־����
     * @since  1.0
     */
    public function Create( $menuInfo = array() )
    {
        if ( empty($menuInfo) )
        {
            $cacheName = 'weixin_menus';
            if ( !empty($this->_params['weixin_cache_id']) )
            {
                $cacheName .= ('_' . trim($this->_params['weixin_cache_id']));
            }
            $weixin_menus = cls_cache::Read($cacheName, '', '', 0, true);
            if ( empty($weixin_menus) || !is_array($weixin_menus) )
            {
                cls_message::ajax_info('�����ں�̨ģ���� �� ΢������ �� �˵������������Զ���˵���', 'CONTENT');
            }
            
            $menuInfo = $this->__08ToWeixinConfig($weixin_menus);
        }
        #var_dump($menuInfo);exit;
        $menuInfo = $this->formatPostDatasToJSON($menuInfo);
        $url = sprintf($this->_urlFormat, 'create', $this->_access_token);
        $returnInfo = _08_Http_Request::getResources(array('urls' => $url, 'method' => 'POST', 'postData' => $menuInfo), 5);
        
        return json_decode($returnInfo);
    }
    
    /**
     * �ӱ�ϵͳ�����ʽת��΢����ʹ�õĲ˵���ʽ
     * 
     * @param  array $weixin_menus ��ϵͳ�����ʽ��������
     * @return array $menuInfo     ΢�Ų˵���ʽ����
     * 
     * @since  nv50
     */
    private function __08ToWeixinConfig( array $weixin_menus )
    {
        $menuInfo = array();
        foreach ( $weixin_menus as $menu_id => $menu ) 
        {
            $menu['title'] = strip_tags(trim($menu['title']));
            $menu['url'] = strip_tags(trim($menu['url']));
            $this->_parseTag($menu['url']);
            if ( empty($menu['title']) )
            {
                continue;
            }
            
            if ( $menu['url'] && (strtolower(substr($menu['url'], 0, 4)) == 'http') )
            {
                $menu['url'] .= '&is_weixin=1';
                $array = array('type' => 'view', 'name' => $menu['title'], 'url' => $menu['url']);
            }
            else
            {
                $array = array('type' => 'click', 'name' => $menu['title'], 'key' => $menu['url']);
            } 
            
            # ���ĳһ���˵����ж����˵�ʱ��һ���˵�ֻ��������
            if ( isset($menuInfo['button']) && array_key_exists($menu['pid'], $menuInfo['button']) )
            {
                $menuInfo['button'][$menu['pid']]['sub_button'][] = $array;
                unset($menuInfo['button'][$menu['pid']]['type']);
                if ( isset($menuInfo['button'][$menu['pid']]['url']) )
                {
                    unset($menuInfo['button'][$menu['pid']]['url']);
                }
                else
                {
                	unset($menuInfo['button'][$menu['pid']]['key']);
                }
            }
            else
            {
            	$menuInfo['button'][$menu_id] = $array;
            }
        }
        
        return $menuInfo;
    }
    
    /**
     * ������ǩ
     * 
     * @param mixed $value Ҫ�����ֵ
     * @since nv50
     */
    protected function _parseTag( &$value )
    {
        $value = @str_replace(
                    array('{cms_abs}', '{aid}', '{mid}'),
                    array(_08_CMS_ABS, (int)$this->_get['aid'], (int)$this->_get['mid']),
                    $value
                 );
    }
    
    /**
     * �˵���ѯ����ѯ��ǰʹ�õ��Զ���˵��ṹ
     * 
     * @return object ����json_decodeת������״̬��־����
     * @since  1.0
     */
    public function Inquiry()
    {
        $url = sprintf($this->_urlFormat, 'get', $this->_access_token);
        $returnInfo = _08_Http_Request::getResources($url, 5);
        
        return json_decode($returnInfo);
    }
    
    /**
     * �˵�ɾ����ȡ����ǰʹ�õ��Զ���˵�
     * 
     * @return object ����json_decodeת������״̬��־����
     * @since  1.0
     */
    public function Delete()
    {
        $url = sprintf($this->_urlFormat, 'delete', $this->_access_token);
        $returnInfo = _08_Http_Request::getResources($url, 5);
        
        return json_decode($returnInfo);
    }
}