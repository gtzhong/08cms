<?php
/**
 * ΢�Žӿڿ�����
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
        # ��֤ǩ��
       # if( $this->__checkSignature() )
        if( isset($this->params['echostr']) && $this->__checkSignature() )
        {
            exit($this->params['echostr']);
        }
    }
    
    /**
     * ��ʼ���ӿڣ��ýӿ����������뿪�������ʱ��֤ǩ��������մ�΢�����͹�������Ϣ��������Ӧ
     * 
     * @since nv50
     */
    public function init()
    {
        $post = file_get_contents('php://input');
          # �ظ��ı��������ݣ�
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
          # ����˵��¼�
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
          # ��ע�¼�
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
        # �ϱ�����λ���¼�
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
        # ��������ʶ����
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
     * ���ظ�״̬
     * ���û��ظ�����ʱ��ʱ���ܴ������ݿ��ֶβ��������������Ǹ��������ʾ�������������������Ժ����ԡ�
     */
    public function checkStatus( $post )
    {
        ob_start();
        $content = ob_get_contents();
        ob_end_clean();
        if ( $content && false === stripos($content, '<xml>') )
        {
            #д���Լ�¼(����???)
			#$post['ob_content'] = $content; $post = cls_outbug::fmtArr($post); 
			#cls_outbug::main("_08_M_Weixin_Event::checkStatus:".$post,'','wetest/log_'.date('Y_md').'.log',1);
            #�ظ�ͨ�ó�����Ϣ
            $Weixin_Extends_Message = parent::getModels('Weixin_Extends_Message', $post);
            exit($Weixin_Extends_Message->_ReplyText('�����������������Ժ����ԡ�'));
        }
        else
        {
        	exit($content);
        }
    }
    
    /**
     * �����Զ���˵�
     * 
     * @since nv50
     */
    public function create_menu()
    {
        if ( empty($this->_curuser->info['mid']) )
        {
            cls_message::ajax_info('���ȵ�¼��', 'CONTENT');
        }
        
        if ( !isset($this->_get['cache_id']) && !isset($this->_get['weixin_cache_id']) )
        {
            if($re = $this->_curuser->NoBackFunc('tpl')) cls_message::ajax_info($re, 'CONTENT');
        }
        
        if ( isset($this->_get['target']) && (strtolower($this->_get['target']) == 'end') )
        {
            cls_message::ajax_info('���в˵�������ɡ�', 'CONTENT');
        }
        
        $params = $this->__getCreateMenuParams();
        $Weixin_Custom_Menu = _08_factory::getInstance('_08_M_Weixin_Custom_Menu', $params);
        $returnInfo = $Weixin_Custom_Menu->Create();
        $jumpParams = $Weixin_Custom_Menu->getNextJumpParams();
        $message = $Weixin_Custom_Menu->getter('_message');
        
        if ( is_null($returnInfo) || !isset($returnInfo->errcode) )
        {
            cls_message::ajax_info("������������ԭ�򣬲˵�{$message}�������ɹ������Ժ����ԡ�", 'CONTENT', $jumpParams);
        }
        
        if ( $returnInfo->errcode || strtoupper($returnInfo->errmsg) != 'OK' )
        {
            cls_message::ajax_info($message . _08_M_Weixin_Error_Message::get($returnInfo->errcode), 'CONTENT', $jumpParams);
        }
        else
        {            
        	cls_message::ajax_info("�˵�{$message}������ɡ�", 'CONTENT', $jumpParams);
        }
    }
    
    /**
     * ��ȡ�����˵��Ĳ���
     * 
     * @return array $params ���ػ�ȡ��Ĵ����˵�����
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
     * ��ȡ΢�Ŷ�ά��
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
     * ��֤ǩ���Ƿ���ȷ
     * 
     * @return bool ��ȷ����TRUE�����򷵻�FALSE
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