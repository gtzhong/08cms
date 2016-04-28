<?php
/**
 * ΢����Ϣ�ӿڹ���ģ��
 *
 * @author    Wilson <Wilsonnet@163.com>
 * @copyright Copyright (C) 2008 - 2013 08CMS, Inc. All rights reserved.
 */

defined('_08CMS_APP_EXEC') || exit('No Permission');
abstract class _08_M_Weixin_Message extends _08_M_Weixin_Base
{
	
    /**
     * ��¼�¼�eventKey
     * 
     * @var   int
     */
    const SCENE_ID_LOGIN = 1; 
	
    /**
     * ע���¼�eventKey
     * 
     * @var   int
     */
    const SCENE_ID_REGISTER = 2; 
	
    /**
     * �ı���Ϣ / �ظ��ı���Ϣ �ṹ
     * ����ṹ�뿴��
     * �ı���Ϣ��{@link http://mp.weixin.qq.com/wiki/index.php?title=%E6%B6%88%E6%81%AF%E6%8E%A5%E5%8F%A3%E6%8C%87%E5%8D%97#.E6.96.87.E6.9C.AC.E6.B6.88.E6.81.AF}
     * �ظ��ı���Ϣ��{@link http://mp.weixin.qq.com/wiki/index.php?title=%E6%B6%88%E6%81%AF%E6%8E%A5%E5%8F%A3%E6%8C%87%E5%8D%97#.E5.9B.9E.E5.A4.8D.E6.96.87.E6.9C.AC.E6.B6.88.E6.81.AF}
     */
    protected $_ToUserName = '<ToUserName><![CDATA[%s]]></ToUserName>';  # ������΢�ź� 
    
    protected $_FromUserName = '<FromUserName><![CDATA[%s]]></FromUserName>'; # ���ͷ��ʺţ�һ��OpenID�� 
    
    protected $_CreateTime = '<CreateTime>%s</CreateTime>'; # ��Ϣ����ʱ�� �����ͣ� 
    
    protected $_MsgType = '<MsgType><![CDATA[%s]]></MsgType>'; # ��Ϣ����
    
    protected $_Content = '<Content><![CDATA[%s]]></Content>'; # �ı���Ϣ����
    
    protected $_MsgId = '<MsgId>%s</MsgId>'; # ��Ϣid��64λ���� 
    
    /**
     * ͼƬ��Ϣ�ṹ�����ı���Ϣ�ṹ���������½ṹ��
     * 
     * ����ṹ�뿴��{@link http://mp.weixin.qq.com/wiki/index.php?title=%E6%B6%88%E6%81%AF%E6%8E%A5%E5%8F%A3%E6%8C%87%E5%8D%97#.E5.9B.BE.E7.89.87.E6.B6.88.E6.81.AF}
     */
    protected $_PicUrl = '<PicUrl><![CDATA[%s]]></PicUrl>'; #  	ͼƬ���� 
    
    /**
     * ����λ����Ϣ�ṹ�����ı���Ϣ�ṹ���������½ṹ��
     * 
     * ����ṹ�뿴��{@link http://mp.weixin.qq.com/wiki/index.php?title=%E6%B6%88%E6%81%AF%E6%8E%A5%E5%8F%A3%E6%8C%87%E5%8D%97#.E5.9C.B0.E7.90.86.E4.BD.8D.E7.BD.AE.E6.B6.88.E6.81.AF}
     */
    protected $_Location_X = '<Location_X>%s</Location_X>'; # ����λ��γ�� 
    
    protected $_Location_Y = '<Location_Y>%s</Location_Y>'; # ����λ�þ��� 
    
    protected $_Scale = '<Scale>%s</Scale>'; # ��ͼ���Ŵ�С
    
    protected $_Label = '<Label><![CDATA[%s]]></Label>'; # ����λ����Ϣ 
    
    /**
     * ������Ϣ�ṹ�����ı���Ϣ�ṹ���������½ṹ��
     * 
     * ����ṹ�뿴��{@link http://mp.weixin.qq.com/wiki/index.php?title=%E6%B6%88%E6%81%AF%E6%8E%A5%E5%8F%A3%E6%8C%87%E5%8D%97#.E9.93.BE.E6.8E.A5.E6.B6.88.E6.81.AF}
     */
    protected $_Title = '<Title><![CDATA[%s]]></Title>'; # ��Ϣ���� 
    
    protected $_Description = '<Description><![CDATA[%s]]></Description>'; # ��Ϣ���� 
    
    protected $_Url = '<Url><![CDATA[%s]]></Url>'; # ��Ϣ����
    
    /**
     * �¼����ͽṹ�����ı���Ϣ�ṹ���������½ṹ��
     * 
     * ����ṹ�뿴��{@link http://mp.weixin.qq.com/wiki/index.php?title=%E6%B6%88%E6%81%AF%E6%8E%A5%E5%8F%A3%E6%8C%87%E5%8D%97#.E4.BA.8B.E4.BB.B6.E6.8E.A8.E9.80.81}
     */
    protected $_Event = '<Event><![CDATA[%s]]></Event>'; # �¼����ͣ�subscribe(����)��unsubscribe(ȡ������)��CLICK(�Զ���˵�����¼�) 
    
    protected $_EventKey = '<EventKey><![CDATA[%s]]></EventKey>'; # �¼�KEYֵ�����Զ���˵��ӿ���KEYֵ��Ӧ
    
    /**
     * �ظ�������Ϣ�ṹ�����ı���Ϣ�ṹ���������½ṹ��
     * 
     * ����ṹ�뿴��{@link http://mp.weixin.qq.com/wiki/index.php?title=%E6%B6%88%E6%81%AF%E6%8E%A5%E5%8F%A3%E6%8C%87%E5%8D%97#.E5.9B.9E.E5.A4.8D.E9.9F.B3.E4.B9.90.E6.B6.88.E6.81.AF}
     */
    protected $_MusicUrl = '<MusicUrl><![CDATA[%s]]></MusicUrl>'; # �������� 
     
    protected $_HQMusicUrl = '<HQMusicUrl><![CDATA[%s]]></HQMusicUrl>'; # �������������ӣ�WIFI��������ʹ�ø����Ӳ������� 
    
    /**
     * �ظ�ͼ����Ϣ�ṹ����ͼƬ��Ϣ��������Ϣ�ṹ���������½ṹ��
     * 
     * ����ṹ�뿴��{@link http://mp.weixin.qq.com/wiki/index.php?title=%E6%B6%88%E6%81%AF%E6%8E%A5%E5%8F%A3%E6%8C%87%E5%8D%97#.E5.9B.9E.E5.A4.8D.E5.9B.BE.E6.96.87.E6.B6.88.E6.81.AF}
     */
    protected $_ArticleCount = '<ArticleCount>%s</ArticleCount>'; # ͼ����Ϣ����������Ϊ10������
    
    protected $_post = null;
    
    private $replyItem = "<xml>\n";
    
    public function __construct( SimpleXMLElement $postObj )
    {
        parent::__construct();
        $this->_post = $postObj;
    }
    
    /**** ���¶���ÿһ��POST���󣬿���������Ӧ���з����ض�xml�ṹ���Ը���Ϣ������Ӧ����֧�ֻظ��ı���ͼ�ġ���������Ƶ�����֣��� ****/
    
    /**
     * �ظ��ı���Ϣ
     * 
     * @example $this->_ReplyText('���Իظ��ı���Ϣ��'); 
     * 
     * @param   string $content Ҫ�ظ����ı�����
     * @return  string          ����һ��XML�ṹ
     * @since   nv50
     */
    public function _ReplyText( $content )
    {        
        if (empty($this->_mconfigs['weixin_enable']))
        {
            $content = '΢�Ź���ƽ̨δ������';
        }
        elseif ( empty($content) )
        {
            $content = '����������ݣ�';
        }
        
        $items = array(
            'ToUserName' => $this->_post->FromUserName,
            'FromUserName' => $this->_post->ToUserName,
            'CreateTime' => time(),
            'MsgType' => 'text',
            'Content' => trim( (string) $content )
        );        
        $this->_setXmlItem($items);
        
        $this->replyItem .= '</xml>';
        
        return $this->replyItem;
    }
    
    /**
     * �ظ�������Ϣ
     * 
     * @example
        # �ظ�������Ϣ
        return $this->_ReplyMusic(
            array(
                'Title' => '��Ϣ����',
                'Description' => '��Ϣ����',
                'MusicUrl' => '��������',
                'HQMusicUrl' => '�������������ӣ�WIFI��������ʹ�ø����Ӳ�������'
            )
        ); 
     * 
     * @param  array $musicInfo Ҫ�ظ���������Ϣ
     * @return string           ����һ��XML�ṹ
     * @since  nv50
     */
    protected function _ReplyMusic( array $musicInfo )
    {     
        if (empty($this->_mconfigs['weixin_enable']))
        {
            return $this->_ReplyText('΢�Ź���ƽ̨δ������');
        }
        elseif ( empty($musicInfo) )
        {
            return $this->_ReplyText('');
        }
        
        $items = array(
            'ToUserName' => $this->_post->FromUserName,
            'FromUserName' => $this->_post->ToUserName,
            'CreateTime' => time(),
            'MsgType' => 'music'
        );
        $this->_setXmlItem($items);
        $this->replyItem .= "<Music>\n";
        $this->_setXmlItem($musicInfo);
        $this->replyItem .= "</Music>\n";
        
        $this->replyItem .= '</xml>';
        
        return $this->replyItem;
    }
    
    
    /**
     * �ظ�ͼ����Ϣ
     * 
     * @example
        
        # �ظ�ͼ����Ϣ��������
        return $this->_ReplyNews(
            array(
                array('Title' => '��Ϣ����һ', 'Description' => '��Ϣ����һ', 'PicUrl' => '��ϢͼƬ��ַһ', 'Url' => '��Ϣ����һ'),
                array('Title' => '��Ϣ�����', 'Description' => '��Ϣ������', 'PicUrl' => '��ϢͼƬ��ַ��', 'Url' => '��Ϣ���Ӷ�'),
                array('Title' => '��Ϣ������', 'Description' => '��Ϣ������', 'PicUrl' => '��ϢͼƬ��ַ��', 'Url' => '��Ϣ������')
            )
        );
        
        # �ظ�ͼ����Ϣ��������
        return $this->_ReplyNews(
            array('Title' => '��Ϣ����', 'Description' => '��Ϣ����', 'PicUrl' => '��ϢͼƬ��ַ', 'Url' => '��Ϣ����')
        );
     * 
     * @param  string $articles Ҫ�ظ���ͼ���������飬
     *                          ע��������һ�η��͵�Ԫ�ظ������ܴ���10����һ�β��ܻظ�����10����ͼ����Ϣ��,����ͼ����Ϣ��Ϣ��Ĭ�ϵ�һ��itemΪ��ͼ 
     * @return string           ����һ��XML�ṹ
     * @since  nv50
     */
    protected function _ReplyNews( array $articles )
    {     
        if (empty($this->_mconfigs['weixin_enable']))
        {
            return $this->_ReplyText('΢�Ź���ƽ̨δ������');
        }
        elseif ( empty($articles) )
        {
            return $this->_ReplyText('');
        }
        
        $items = array(
            'ToUserName' => $this->_post->FromUserName,
            'FromUserName' => $this->_post->ToUserName,
            'CreateTime' => time(),
            'MsgType' => 'news',
            'ArticleCount' => cls_Array::array_dimension($articles) == 1 ? 1 : count($articles)
        );
        $this->_setXmlItem($items);
        $this->replyItem .= "<Articles>\n";
        
        foreach ($articles as $key => $items) 
        {
            if ( !empty($items) )
            {
                $this->replyItem .= "<item>\n";
                if ( is_array($items) )
                {
                    $this->_setXmlItem($items);
                }
                else
                {
                	$this->_setXmlItem($articles);
                    $break = true;
                }
                $this->replyItem .= "</item>\n";
            }
            
            if ( isset($break) && $break )
            {
                break;
            }
        }
        
        $this->replyItem .= "</Articles>\n</xml>";
        
        return $this->replyItem;
    }
    
    /**
     * ����XML�ڵ�
     * 
     * @param string $name  �ڵ�����
     * @param mixed  $value �ڵ�ֵ
     * @since nv50
     */
    protected function _setXmlItem( array $items )
    {
        $mcharset = cls_env::getBaseIncConfigs('mcharset');
        foreach ($items as $key => $itemValue) 
        {
            $format = $this->{'_' . $key};
            if ( !empty($format) )
            {
                $this->replyItem .= sprintf($format . "\n", cls_string::iconv($mcharset, 'UTF-8', $itemValue));
            }  
        }
    }
    
    public function __call( $name, $argc )
    {
         return $this->_ReplyText('�����͵���Ϣ������������ԡ�');
    }
}