<?php
/**
 * ΢���û�����ӿڻ���
 *
 * @author    Wilson <Wilsonnet@163.com>
 * @copyright Copyright (C) 2008 - 2013 08CMS, Inc. All rights reserved.
 */

defined('_08CMS_APP_EXEC') || exit('No Permission');
class _08_M_Weixin_Users_Base extends _08_M_Weixin_Base
{
    /**
     * ��ȡ΢���û���Ϣ
     * 
     * @param string $openid       ��ͨ�û��ı�ʶ���Ե�ǰ���ں�Ψһ 
     **/
    public function getUserInfo($openid)
    {
        $url = sprintf($this->_get_user_info_url, $this->_access_token, $openid);
        $returnInfo = _08_Http_Request::getResources($url);
        
        return $returnInfo;
    }
    
    public function __construct()
    {
        parent::__construct();
        $weixin_config = $this->getAppIDAndAppSecret( parent::PLUGIN_ENABLE_VALUE );
        //if ( empty($weixin_config) || empty($weixin_config['weixin_enable']) )
        //{
            //cls_message::ajax_info("��{$this->_message}΢�Ź���ƽ̨δ���û�����δ���档", 'CONTENT', $this->getNextJumpParams());
        //}    
        $this->_requestGetAccessToken($weixin_config['weixin_appid'], $weixin_config['weixin_appsecret']);   
    }
}