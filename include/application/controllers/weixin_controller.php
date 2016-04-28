<?php
/**
 * 微信接口控制器
 *
 * @author    Wilson <Wilsonnet@163.com>
 * @copyright Copyright (C) 2008 - 2013 08CMS, Inc. All rights reserved.
 */

defined('_08CMS_APP_EXEC') || exit('No Permission');
class _08_C_Weixin_Controller extends _08_Controller_Base
{
    private $params = array();
    
    public function __construct()
    {
		parent::__construct();
        $this->params = $this->_get;
        if (isset($this->params['mid']))
        {
            $this->params['weixin_fromid_type'] = 'mid';
            $this->params['weixin_fromid'] = (int) $this->params['mid'];
        }
        else
        {
            $this->params['weixin_fromid_type'] = 'aid';
            @$this->params['weixin_fromid'] = (int) $this->params['aid'];
        }
        # 验证签名
       # if( $this->__checkSignature() )
        if( isset($this->params['echostr']) && $this->__checkSignature() )
        {
            exit($this->params['echostr']);
        }
    }
    
    /**
     * 初始化接口，该接口用于在申请开发者身份时验证签名，与接收从微信推送过来的信息，并作响应
     * 
     * @since nv50
     */
    public function init()
    {
        $post = file_get_contents('php://input');
          # 回复文本测试数据：
//        $post = <<<EOT
//         <xml>
//             <ToUserName><![CDATA[toUser]]></ToUserName>
//             <FromUserName><![CDATA[fromUser]]></FromUserName> 
//             <CreateTime>1348831860</CreateTime>
//             <MsgType><![CDATA[text]]></MsgType>
//             <Content><![CDATA[21207]]></Content>
//             <MsgId>1234567890123456</MsgId>
//         </xml>
//EOT;
          # 点击菜单事件
//        $post = <<<EOT
//         <xml>
//            <ToUserName><![CDATA[toUser]]></ToUserName>
//            <FromUserName><![CDATA[oA1n9tqlWGlmVc4EYjisto6IQnjs]]></FromUserName>
//            <CreateTime>123456789</CreateTime>
//            <MsgType><![CDATA[event]]></MsgType>
//            <Event><![CDATA[CLICK]]></Event>
//            <EventKey><![CDATA[RENTING_PERIPHERY]]></EventKey>
//           </xml>
//EOT;
          # 关注事件
//        $post = <<<EOT
//        <xml>
//            <ToUserName><![CDATA[toUser]]></ToUserName>
//            <FromUserName><![CDATA[FromUser]]></FromUserName>
//            <CreateTime>123456789</CreateTime>
//            <MsgType><![CDATA[event]]></MsgType>
//            <Event><![CDATA[subscribe]]></Event>
//            <EventKey><![CDATA[qrscene_123123]]></EventKey>
//            <Ticket><![CDATA[TICKET]]></Ticket>
//        </xml>
//EOT;
        # 上报地理位置事件
//        $post = <<<EOT
//        <xml>
//            <ToUserName><![CDATA[toUser]]></ToUserName>
//            <FromUserName><![CDATA[fromUser]]></FromUserName>
//            <CreateTime>123456789</CreateTime>
//            <MsgType><![CDATA[event]]></MsgType>
//            <Event><![CDATA[LOCATION]]></Event>
//            <Latitude>23.137466</Latitude>
//            <Longitude>113.352425</Longitude>
//            <Precision>119.385040</Precision>
//        </xml>
//EOT;
        # 接收语音识别结果
//        $post = <<<EOT
//        <xml>
//            <ToUserName><![CDATA[toUser]]></ToUserName>
//            <FromUserName><![CDATA[fromUser]]></FromUserName>
//            <CreateTime>1357290913</CreateTime>
//            <MsgType><![CDATA[voice]]></MsgType>
//            <MediaId><![CDATA[media_id]]></MediaId>
//            <Format><![CDATA[Format]]></Format>
//            <Recognition><![CDATA[10207]]></Recognition>
//            <MsgId>1234567890123456</MsgId>
//        </xml>
//EOT;
        if ( !empty($post) )
        {
            $postObj = simplexml_load_string($post, 'SimpleXMLElement', LIBXML_NOCDATA);
            if( !empty( $postObj ) )
            {
                register_shutdown_function(array($this, 'checkStatus'), $postObj);
                $returnInfo = parent::getModels('Weixin_Extends_Base')->run($postObj); 
                exit($returnInfo);
            }
        }
    }
    
    /**
     * 检测回复状态
     * 当用户回复错误时有时可能存在数据库字段不存在情况，如果是该情况就显示：服务器返回有误，请稍候再试。
     */
    public function checkStatus( $post )
    {
        ob_start();
        $content = ob_get_contents();
        ob_end_clean();
        if ( $content && false === stripos($content, '<xml>') )
        {
            #写调试记录(开关???)
			#$post['ob_content'] = $content; $post = cls_outbug::fmtArr($post); 
			#cls_outbug::main("_08_M_Weixin_Event::checkStatus:".$post,'','wetest/log_'.date('Y_md').'.log',1);
            #回复通用出错信息
            $Weixin_Extends_Message = parent::getModels('Weixin_Extends_Message', $post);
            exit($Weixin_Extends_Message->_ReplyText('服务器返回有误，请稍候再试。'));
        }
        else
        {
        	exit($content);
        }
    }
    
    /**
     * 创建自定义菜单
     * 
     * @since nv50
     */
    public function create_menu()
    {
        if ( empty($this->_curuser->info['mid']) )
        {
            cls_message::ajax_info('请先登录。', 'CONTENT');
        }
        
        if ( !isset($this->_get['cache_id']) && !isset($this->_get['weixin_cache_id']) )
        {
            if($re = $this->_curuser->NoBackFunc('tpl')) cls_message::ajax_info($re, 'CONTENT');
        }
        
        if ( isset($this->_get['target']) && (strtolower($this->_get['target']) == 'end') )
        {
            cls_message::ajax_info('所有菜单创建完成。', 'CONTENT');
        }
        
        $params = $this->__getCreateMenuParams();
        $Weixin_Custom_Menu = _08_factory::getInstance('_08_M_Weixin_Custom_Menu', $params);
        $returnInfo = $Weixin_Custom_Menu->Create();
        $jumpParams = $Weixin_Custom_Menu->getNextJumpParams();
        $message = $Weixin_Custom_Menu->getter('_message');
        
        if ( is_null($returnInfo) || !isset($returnInfo->errcode) )
        {
            cls_message::ajax_info("可能由于网络原因，菜单{$message}创建不成功，请稍候再试。", 'CONTENT', $jumpParams);
        }
        
        if ( $returnInfo->errcode || strtoupper($returnInfo->errmsg) != 'OK' )
        {
            cls_message::ajax_info($message . _08_M_Weixin_Error_Message::get($returnInfo->errcode), 'CONTENT', $jumpParams);
        }
        else
        {            
        	cls_message::ajax_info("菜单{$message}创建完成。", 'CONTENT', $jumpParams);
        }
    }
    
    /**
     * 获取创建菜单的参数
     * 
     * @return array $params 返回获取后的创建菜单参数
     * @since  nv50
     */
    private function __getCreateMenuParams()
    {
        $params = array();
        
        if ( isset($this->_get['cache_id']) )
        {
            $params['weixin_cache_id'] = preg_replace('/[^\w]/', '', trim($this->_get['cache_id']));
        }
        else
        {
        	$params['weixin_cache_id'] = '';
        }
        
        foreach ( array('aid', 'mid') as $type ) 
        {
            if ( isset($this->_get[$type]) )
            {
                $params['weixin_fromid_type'] = $type;
                $params['weixin_fromid'] = $this->_get[$type];
                break;
            }
        }
        
        if ( count($params) == 1 && !empty($params['weixin_cache_id']) )
        {
            $params = parent::getModels('Weixin_DataBase')->getCacheConfig($params['weixin_cache_id']);
        }
        
        return $params;
    }
    
    /**
     * 获取微信二维码
     */
    public function show_qrcode()
    {
        if ( isset($this->_get['scene_id']) )
        {
            $Weixin_Qrcode = parent::getModels('Weixin_Qrcode');
            $qrcode = $Weixin_Qrcode->show_qrcode($this->_get);
        	@header("Pragma:no-cache");
        	@header("Cache-control:no-cache");
        	@header("Content-type: image/jpg");
            exit($qrcode);
        }
    }
    
    /**
     * 验证签名是否正确
     * 
     * @return bool 正确返回TRUE，否则返回FALSE
     * @since  nv50
     */
    private function __checkSignature()
    {
        $Weixin_Base = parent::getModels('Weixin_Config', $this->params);
        $config = $Weixin_Base->getAppIDAndAppSecret();
        if (isset($config['weixin_enable']))
        {
            $this->_mconfigs['weixin_enable'] = $config['weixin_enable'];
        }
        if ( isset($this->params["signature"]) && isset($this->params["timestamp"]) && 
             isset($this->params["nonce"]) && !empty($config['weixin_token']) )
        {
            $signature = trim($this->params["signature"]);
            $timestamp = trim($this->params["timestamp"]);
            $nonce = trim($this->params["nonce"]);
    		$token = trim($config['weixin_token']);
            
    		$tmpArr = array($token, $timestamp, $nonce);
    		sort($tmpArr, SORT_STRING);
    		$tmpStr = implode( $tmpArr );
    		$tmpStr = sha1( $tmpStr );
    	
    		if( $tmpStr == $signature )
            {
    			return true;
    		}
        }
        
        return false;
    }
}