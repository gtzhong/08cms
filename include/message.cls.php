<?php
/**
 * ��Ϣ��ʾģ����
 * floatwin Demo��������Ϊ�ύ����ͬʱ��ӡ������ť�����Ҫ��ת�ĵ�ַ��: 
 *  cls_message::show('������Ϣ�༭���', array(
        '������һ��' => 'history.go(-1)',
        '�����б�ҳ' => axaction(64, '?entry=extend&extend=members'),
        '�رմ���' => axaction(2)
    ));
 *
 * @author    Wilson <Wilsonnet@163.com>
 * @copyright Copyright (C) 2008 - 2013 08CMS, Inc. All rights reserved.
 */
class cls_message
{
    protected static $_instance = null;
    
    protected $_params = array();
    
    /**
     * ��ʾ��Ϣ
     * ���Ҫ��ʾ�����ť��ֱ���������ã�
     * cls_message::show(
     *      '��ʾ��Ϣ��ʾ��Ϣ��ʾ��Ϣ', 
     *      array('������һ��' => '?return=go-back', '�����б�ҳ' => '?return=go-list', '�رմ���' => '?return=go-close')
     * );
     * 
     * @param string $str   Ҫ��ӡ����Ϣ
     * @param mixed  $url   ��ӡ��Ϣ����ת��URL���ò�����Ϊ���飬���Ϊ����ʱ�������Ϣ�������ӡ��ť��
     * @param int    $mtime ��ʾ��Ϣʱͣ����ʱ��
     */
    public static function show( $str='', $urls = '', $mtime = 1250 )
    {
        global $inajax, $infloat, $handlekey;
        if (defined('_08CMS_AJAX_EXEC'))
        {
            /**
             * ��cls_message����MVC��AJAX�ű���עcls_message�᷵��һ��JSON��ʽ��״̬���ݣ�
             * {'error'=> '������Ϣ', 'message'=>'')���URL���һ��callback=funName ʱ��JSON��ʽ�����ݻᱻ���callback���ã�
             * �磺  funName({'error'=>'������Ϣ', 'message'=>''});
             **/
            $gets = cls_env::_GET('callback');
            $status = array('error' => $str, 'message' => '');
            $ajax = _08_C_Ajax_Controller::getInstance();
            if (!empty($gets['callback']))
            {
                $status = $ajax->format($status, $gets['callback']);
            }
            else
            {
            	$status = $ajax->format($status);
            }
            
            exit($status);
        }
        
		if (!empty($inajax))
        {
			self::ajax_info($str);
		}
       
//        empty($urls) && empty($infloat) && $urls = M_REFERER;
        # �ڴ˷������ $this ת�� self::$_instance ��ʽ����
        self::setInstance();
        self::_setParamsToTpl(
            array( 'str' => (string)$str, 'urls' => $urls, 'mtime' => (int) $mtime, 
                   'infloat' => (int)$infloat, 'handlekey' => (int)$handlekey )
        );
        
        if ( defined('M_ADMIN') )
        {
            self::$_instance->_amessage();
        }
        else if ( defined('M_MCENTER') )
        {
            self::$_instance->_mcmessage();
        }
        else
        {
            self::$_instance->_message();
        }
    }
   
    # ��̨
    protected function _amessage()
    {
    	global $amsgforwordtime;	
    	empty($amsgforwordtime) || $this->_params['mtime'] = $amsgforwordtime;  
        if ( !function_exists('aheader') || !function_exists('afooter') )
        {
            include _08_INCLUDE_PATH . 'admina.fun.php';
            function_exists('aheader') && aheader();
        }
        $this->setButton();
        
        $this->_display();
    }
    
    #��Ա����
    protected function _mcmessage()
    {
    	defined('MMSGFORWORDTIME') && ($this->_params['mtime'] = defined('MMSGFORWORDTIME'));  
    	$this->_params['no_mcfooter'] = defined('NO_MCFOOTER');    
             
        $this->setButton();
        $this->_params['str'] .= '&nbsp; <a href="javascript:window.close();"'.($this->_params['infloat']?" onclick=\"return floatwin('close_" . $this->_params['handlekey'] . "')\"":'').'>[�رմ���]</a>';
        $this->_display();
    }
    
    # ǰ̨
    protected function _message()
    {
		$cms_abs = cls_env::mconfig('cms_abs');
		$mcharset = cls_env::getBaseIncConfigs('mcharset');
		$msgforwordtime = cls_env::mconfig('msgforwordtime');
		empty($msgforwordtime) || $this->_params['mtime'] = $msgforwordtime;  
        
        self::_setParamsToTpl(
            array('cms_abs' => $cms_abs, 'mcharset' => $mcharset )
        );
        if ( !empty($this->_params['infloat']) )
        {
            $this->_params['str'] .= cls_phpToJavascript::str_js_src("{$cms_abs}include/js/floatwin.js");
        }
        
        $this->setButton();
        
        if ( !is_array($this->_params['urls']) )
        {
            $this->_params['str'] .= "<a href=\"javascript:\" onclick=\"return top.floatwin?top.floatwin('close_" . $this->_params['handlekey'] . "'):window.close()\">[�رմ���]</a>";
        }
        
        $this->_params['str'] = "<br><br>{$this->_params['str']}<br><br>";
        
        $this->_display();
    }
    
    /**
     * ���ð�ť��ʾ��Ϣ
     * �÷����������е��ң���Ϊ������ǰ���ã�Ŀǰֻ����ʱ��������
     */
    private function setButton()
    {
        if ( empty($this->_params['urls']) ) return false;
        if ( is_array($this->_params['urls']) ) { return $this->setButtons(); }
        $url = $this->_params['urls'];
        
        $this->_params['str'] .= '<br />';
        if(preg_match('/^javascript:/',$url)) {  # ����ǰֻ�ܵ���axaction���������Ҳ��ֱ�Ӵ���JS�����ò��ø�������ͨ��
			$this->_params['str'] .= "<script type=\"text/javascript\" reload=\"1\">var t = " . $this->_params['mtime'] . ";".substr($url,11)."</script>";
		} else if ( false !== strpos($url,'history') ) { # ���ݲ���Ϊ��history.go(-1)������һ�������
        	$this->_params['str'] .= "<br /><br /><a href=\"javascript:$url\">��������û����ת�������</a><script>setTimeout('$url', " . $this->_params['mtime'] . ");</script>";
        } else if(strpos($this->_params['str'],'����') === false && !defined('M_ADMIN') ){ # ǰ̨���ذ�ť
            $url = cls_env::repGlobalURL($url);
       		$this->_params['str'] .= "<br /><br /><a href=\"$url\">[������ת]</a><script>setTimeout(\"window.location.replace('$url');\", ". $this->_params['mtime'] .");</script>&nbsp; ";
        } else {
            $url = cls_env::repGlobalURL($url);
            if ( empty($this->_params['infloat']) )
            {
                $this->_params['str'] .= "<br /><br /><a href=\"$url\">��������û����ת�������</a><script>setTimeout(\"redirect('$url');\", " . $this->_params['mtime'] . ");</script>";
            }
			else
            {
               	$this->_params['str'] .= "<a href=\"$url\" onclick=\"return floatwin('update_" . $this->_params['handlekey'] . "', this);\">��������û����ת�������</a><script type=\"text/javascript\" reload=\"1\">setDelay(\"floatwin('update_" . $this->_params['handlekey'] . "', '$url');\", " . $this->_params['mtime'] . ");</script>";
            }
		}
    }
    
    /**
     * ��ӡ��ʾ��Ϣ
     */
    protected function _display()
    {
        global $message_class;
    	$this->_params['class'] = empty($message_class) ? 'tabmain' : $message_class;
        _08_Loader::import(_08_INCLUDE_PATH . 'message_tpl.cls', $this->_params);
    }
    
    /**
     * ���ɶ����ť
     */
    private function setButtons()
    {
        if ( empty($this->_params['urls']) ) return false;
        
		$this->_params['str'] .= '<br /><br /><br />';
		$i = 1;
        foreach ( (array) $this->_params['urls'] as $message => $url )
        {
            if( (false !== strpos($url, 'history.')) || (false !== strpos($url, 'window.')) )
            {
                $url = 'javascript:' . $url;
            } 
			$this->_params['str'] .= "��<a href=\"$url\">[$message]</a>"; //ע��ǰ���Ǹ�[ȫ�ǿո�],����ĳЩ����»����&nbsp;�е�&�ַ�
			$i == 1 && $this->_params['str'] .= "<script>setTimeout(\"window.location.replace('$url');\", " . $this->_params['mtime'] . ");</script>";
			$i ++;
        }
    }
    
    /**
     * ���ò���Ӧ�õ���Ϣģ��
     * 
     * @param array $params Ҫ���õĲ�����key Ϊ �������� value Ϊ ֵ
     */
    protected static function _setParamsToTpl( array $params )
    {
        if ( !(self::$_instance instanceof self) )
        {
            return false;
        }
        
        foreach ($params as $key => $value) 
        {
            self::$_instance->_params[$key] = $value;
        }
    }
    
    # ajax
    public static function ajax_info($str, $format = 'XML', $param = array())
    {
    	global $mcharset,$callback;
        switch ( strtoupper($format) )
        {
           case 'CONTENT':
               if ( !empty($param['url']) )
               {
                   empty($param['timeout']) && $param['timeout'] = 2;
                   $jsString = <<<HTML
                   <br /><br /><span style="font-size:12px;">{$param['timeout']} ����Զ���ת��<a href="{$param['url']}">��������û����ת�������</a></span>
                   <script type="text/javascript">
                        setTimeout(function(){location.href="{$param['url']}";}, {$param['timeout']}000);
                   </script>
HTML;
               }
               else
               {
               	   $jsString = '';
               }
               
               
               exit('<span style="width:100%; text-align:center; display:block; margin-top: 60px">' . $str . $jsString . '</span>');
           case 'JSON':
               // TODO: JSON��ʽ�д����
               break;
        }
    	$callback && js_callback($str);
    	@header("Expires: -1");
    	@header("Cache-Control: no-store, private, post-check=0, pre-check=0, max-age=0", FALSE);
    	@header("Pragma: no-cache");
    	header("Content-type: application/xml");
    	echo "<?xml version=\"1.0\" encoding=\"$mcharset\"?>\n<root><![CDATA[";
    	echo $str;
    	echo ']]></root>';
    	die();
    }
    
    protected function __construct(){}
    
    public static function setInstance()
    {
        if ( empty(self::$_instance) )
        {
            self::$_instance = new self();
        }
    }
}
