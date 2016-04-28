<?php
/**
 * ���ɴ������Ķ�ά��ӿ�
 * {@link http://mp.weixin.qq.com/wiki/index.php?title=%E7%94%9F%E6%88%90%E5%B8%A6%E5%8F%82%E6%95%B0%E7%9A%84%E4%BA%8C%E7%BB%B4%E7%A0%81}
 *
 * @author    Wilson <Wilsonnet@163.com>
 * @copyright Copyright (C) 2008 - 2013 08CMS, Inc. All rights reserved.
 */

defined('_08CMS_APP_EXEC') || exit('No Permission');
class _08_M_Weixin_Qrcode extends _08_M_Weixin_Base
{
    protected $_createUrlFormat = 'https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=%s';
    
    protected $_showUrlFormat = 'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=%s';
    
    public function __construct()
    {
        parent::__construct();
        $config = $this->getAppIDAndAppSecret();
        $this->_requestGetAccessToken($config['weixin_appid'], $config['weixin_appsecret']);
    }
    
    /**
     * ������ά��ticket
     * 
     * @param array $configs ������ά��ticket����Ҫ�Ĳ���
     */
    protected function _create_ticket( array $configs )
    {
        $configs = $this->formatPostDatasToJSON($configs);
        $url = sprintf($this->_createUrlFormat, $this->_access_token);
        $returnInfo = _08_Http_Request::getResources(array('urls' => $url, 'method' => 'POST', 'postData' => $configs), 5);
        return json_decode($returnInfo);
    }
    
    /**
     * ͨ��ticket��ȡ��ά��
     * 
     * @param array $configs ������ά��ticket����Ҫ�Ĳ���
     */
    public function show_qrcode( array $configs )
    {
        $postDatas = array();
        # �ö�ά����Чʱ�䣬����Ϊ��λ�� ��󲻳���1800
        if ( isset($configs['expire_seconds']) )
        {
            $configs['expire_seconds'] = (int) $configs['expire_seconds'];
            if ( $configs['expire_seconds'] > 0 )
            {
                $postDatas['expire_seconds'] = $configs['expire_seconds'];
                $configs['action_name'] = 'QR_SCENE';
            }
        }
        
        # ��ά�����ͣ�QR_SCENEΪ��ʱ,QR_LIMIT_SCENEΪ����
        if ( empty($configs['action_name']) )
        {
            $postDatas['action_name'] = 'QR_LIMIT_SCENE';
            if ( isset($postDatas['expire_seconds']) )
            {
                unset($postDatas['expire_seconds']);
            }
        }
        else
        {
        	$postDatas['action_name'] = strtoupper(trim($configs['action_name']));
        }
        
        # ��ά����ϸ��Ϣ
//        if ( isset($configs['action_info']) )
//        {
//            $postDatas['action_info'] = trim($configs['action_info']);
//        }
        
        # ����ֵID����ʱ��ά��ʱΪ32λ��0���ͣ����ö�ά��ʱ���ֵΪ10000��Ŀǰ����ֻ֧��1--10000��        
        if ( isset($configs['scene_id']) )
        {
            # ֻ��֤���ޣ����������ޣ���΢�Źٷ�����Ԥ���ٷ��Ĺ���(ע��:�����32λ,����������int)
            $postDatas['action_info']['scene']['scene_id'] = max(1, (int) $configs['scene_id']); 
        }
        ksort($postDatas);
        
        $qrcode_path = M_ROOT . $this->_mconfigs['dir_userfile'] . DS . 'qrcode' . DS;
        _08_FileSystemPath::checkPath($qrcode_path, true);
        $qrcode_file = $qrcode_path . md5(serialize($postDatas)) . '.jpg';
        if ( is_file($qrcode_file) )
        {
            if ( !isset($configs['expire_seconds']) || (TIMESTAMP - filemtime($qrcode_file) < $configs['expire_seconds']) )
            {
                $qrcode = file_get_contents($qrcode_file);
                return $qrcode;
            }
        }
        
        $ticket_info = $this->_create_ticket( $postDatas );
        if ( isset($ticket_info->ticket) )
        {
            $url = sprintf($this->_showUrlFormat, urlencode($ticket_info->ticket));
            $qrcode = _08_Http_Request::getResources($url);
            file_put_contents($qrcode_file, $qrcode);
            return $qrcode;
        }
        else
        {
        	cls_message::ajax_info(_08_M_Weixin_Error_Message::get(@$ticket_info->errcode), 'CONTENT');
        }
    }
	
	/**
     * ��ȡ��ʱ��ά�� ����IDֵ��32λ��0���ͣ�
     * 
	 * @param  int   $timegap ʱ����(����), $timegap������δʹ�ù��������ֵID
     * 
     * @return string $hash ����δʹ�ù��������ֵID
     * @08cms  1.0
     */
	public static function getSceneID($timegap=10)
    {
		global $m_cookie; //$cookies = self::_COOKIE();
		$db = _08_factory::getDBO();
		$reval = mt_rand(100001,2147483123); //2,147,483,648
		$time = TIMESTAMP - $timegap*60;
		while($db->select('scene_id')->from('#__msession')->where("mslastactive>$time")->_and(array('scene_id'=>$reval))->exec()->fetch())
		{
			$reval = mt_rand(100001,2147483123); 
		}
		$db->update('#__msession', array('scene_id'=>$reval))->where(array('msid'=>$m_cookie['msid']))->exec();
        return $reval;
    }
	
}