<?php
/**
 * ΢��������
 *
 * @author    Wilson <Wilsonnet@163.com>
 * @copyright Copyright (C) 2008 - 2013 08CMS, Inc. All rights reserved.
 */

defined('_08CMS_APP_EXEC') || exit('No Permission');
class _08_M_Weixin_Config extends _08_M_Weixin_Base
{
    /**
     * ��ȡ΢��ͨ������
     * 
     * @param  string $param ����ͨ����ڵĲ������磺mid=1  �� aid=1
     * @return string        ����ͨ������
     * @since  nv50
     */
    public static function getWeixinURL( $param = '' )
    {        
        return _08_CMS_ABS . _08_Http_Request::uri2MVC('weixin=' . $param);
    }
}