<?php
/**
 * ΢����Ϣ������չģ�ͣ�����ͨ΢���û������˺ŷ�������Ϣ���лظ���
 * ע�����ļ�����������ʱ�벻Ҫ�滻
 *
 * @author    Wilson <Wilsonnet@163.com>
 * @copyright Copyright (C) 2008 - 2013 08CMS, Inc. All rights reserved.
 */

defined('_08CMS_APP_EXEC') || exit('No Permission');
class _08_M_Weixin_Extends_Message extends _08_M_Weixin_Message
{
    /**
     * ����ģ��ID
     */
    const RENTING_CHID = 2;
    
    /**
     * ���ַ�ģ��ID
     */
    const SECOND_HAND_HOUSING_CHID = 3;
    
    /**
     * ¥��ģ��ID
     */
    const PROPERTY_CHID = 4;
    
    /**
     * ��Ӧ�ı���Ϣ ���ú���������������չ���ܻظ��ض�����Ϣʹ�ã�
     * 
     * @return string $message ����Ҫ�ظ���XML��ʽ����
     * @since  4.2+
     */
    public function responseText()
    {
        # var_dump($this->_post);  �ɸ����û����͵���Ϣ���лظ�
        if ( isset($this->_post->Content) )
        {
            $this->_post->Content = strtoupper(trim($this->_post->Content));        
            #@list($prefixes, $ccid) = preg_split('/(?<=[a-zA-Z])(?=[\d])/x', $this->_post->Content);
            $prefixes = substr($this->_post->Content, 0, 2);
            $ccid = substr($this->_post->Content, 2);
            
            if ( empty($prefixes) || empty($ccid) )
            {
                return $this->_ReplyText('�ظ����������»ظ���');
            }
            
            $Weixin_Extends_Message_Text = parent::getModels('Weixin_Extends_Message_Text', $this->_post);
            $method = ('responseText' . strtoupper($prefixes));   
        	$datas = call_user_func(array($Weixin_Extends_Message_Text, $method), $ccid);
                
            if ( empty($datas) )
            {
                return $this->_ReplyText('����������ݡ�');
            }
            
            return $datas;
        }        
        
        return $this->_ReplyText('�ظ����������»ظ���');
    }
        
    /**
     * ��ӦͼƬ��Ϣ ���ú���������������չ���ܻظ��ض�����Ϣʹ�ã�
     * 
     * @return string $message ����Ҫ�ظ���XML��ʽ����
     * @since  4.2+
     */
    public function responseImage()
    {        
    }
        
    /**
     * ��Ӧ������Ϣ ���ú���������������չ���ܻظ��ض�����Ϣʹ�ã�
     * 
     * @return string $message ����Ҫ�ظ���XML��ʽ����
     * @since  4.2+
     */
    public function responseVoice()
    {
        $this->_post->Content = '';
        if ( isset($this->_post->Recognition) )
        {
            $this->_post->Content = $this->_post->Recognition;
        }
        
        return $this->responseText();
    }
        
    /**
     * ��Ӧ��Ƶ��Ϣ ���ú���������������չ���ܻظ��ض�����Ϣʹ�ã�
     * 
     * @return string $message ����Ҫ�ظ���XML��ʽ����
     * @since  4.2+
     */
    public function responseVideo()
    {        
    }
        
    /**
     * ��Ӧ����λ����Ϣ ���ú���������������չ���ܻظ��ض�����Ϣʹ�ã�
     * 
     * @return string $message ����Ҫ�ظ���XML��ʽ����
     * @since  4.2+
     */
    public function responseLocation()
    {        
    }
        
    /**
     * ��Ӧ������Ϣ ���ú���������������չ���ܻظ��ض�����Ϣʹ�ã�
     * 
     * @return string $message ����Ҫ�ظ���XML��ʽ����
     * @since  4.2+
     */
    public function responseLink()
    {        
    }
}