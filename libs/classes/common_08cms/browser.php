<?php
/**
 * ������࣬�ṩ�йص�ǰWeb�ͻ��˵���Ϣ��
 * �����ʶ����ͨ�����HTTP_USER_AGENT���У���Web�������ṩ�Ļ�������
 * 
 * @example    $browser = _08_Browser::getInstance();
               var_dump('isMobile: ' . $browser->isMobile());
                  echo '<br />';
               var_dump('Browser: ' . $browser->getBrowser());
                   echo '<br />';
               var_dump('Version: ' . $browser->getVersion());
                   echo '<br />';
               var_dump('Platform: ' . $browser->getPlatform());
                   echo '<br />';
               var_dump('isAndroid: ' .$browser->isAndroid());
                   echo '<br />';
               var_dump('isIPad: ' . $browser->isIPad());
                   echo '<br />';
               var_dump('isIPhone: ' . $browser->isIPhone());
                   echo '<br />';
               var_dump('PlatformVersion: ' . $browser->getPlatformVersion());
 * @package    08CMS.Platform
 * @subpackage common_08cms
 * 
 * @author     Wilson <Wilsonnet@163.com>
 * @copyright  Copyright (C) 2008 - 2014 08CMS, Inc. All rights reserved.
 */

defined('_08CMS_APP_EXEC') || exit('No Permission');
class _08_Browser
{
	/**
	 * @var   integer ���汾��
	 * @since nv50
	 */
	protected $_majorVersion = 0;

	/**
	 * @var   integer �ΰ汾��
	 * @since nv50
	 */
	protected $_minorVersion = 0;
    
	/**
	 * @var   string �������û������ַ���
	 * @since nv50
	 */
	protected $_agent = '';

	/**
	 * @var    string  HTTP_ACCEPT �ַ���.
	 * @since  nv50
	 */
	protected $_accept = '';

	/**
	 * @var   string Сд���û������ַ���
	 * @since nv50
	 */
	protected $_lowerAgent = '';

    /**
     * �ƶ��豸
     * 
	 * @since nv50
     */
    protected $_mobile = false;
    
    /**
     * IOSϵͳ
     */
    protected $_iphone = false;
    
    /**
     * iPad
     */
    protected $_ipad = false;
    
    /**
     * Androidϵͳ
     */
    protected $_android = false;

    /**
     * ��������
     * 
	 * @since nv50
     */
    protected $_robots = false;
    
    /**
     * �����
     * 
	 * @since nv50
     */
    protected $_browser = false;

	/**
	 * @var   string Ŀǰ����������е�ƽ̨
	 * @since nv50
	 */
	protected $_platform = '';
    
	/**
	 * @var   integer ƽ̨���汾��
	 * @since nv50
	 */
	protected $_platformMajorVersion = 0;

	/**
	 * @var   integer ƽ̨�ΰ汾��
	 * @since nv50
	 */
	protected $_platformMinorVersion = 0;

	/**
	 * MIMEͼƬ�����б�
	 * ���б�����ڣ� IE��Netscape��Mozilla.
	 *
	 * @var   array
	 * @since nv50
	 */
	protected $_images = array('jpeg', 'gif', 'png', 'pjpeg', 'x-png', 'bmp');
    
    /**
     * @var   array ���������������б�
	 * @since nv50
     */
    protected $_robots_tables = array(
		'Googlebot',     # Google
        'Baiduspider',   # �ٶ�
		'msnbot',        # MSN
		'bingbot',       # Bing
		'Yahoo',         # �Ż�
        'YodaoBot',      # �е�
        'Sosospider',    # ����
        '360Spider',     # 360
        'Sogou',         # �ѹ�
        'iaskspider',    # ����
        
        # ������֪����
        'AhrefsBot',        
		'Arachnoidea',
		'ArchitextSpider',
		'Ask Jeeves',
		'B-l-i-t-z-Bot',
		'BecomeBot',
		'cfetch',
		'ConveraCrawler',
		'ExtractorPro',
        'EasouSpider',   # ����
		'FAST-WebCrawler',
		'FDSE robot',
		'fido',
		'geckobot',
		'Gigabot',
		'Girafabot',
		'grub-client',
		'Gulliver',
		'HTTrack',
		'ia_archiver',   # alexa
		'InfoSeek',
		'kinjabot',
		'KIT-Fireball',
		'larbin',
		'LEIA',
		'lmspider',
		'Lycos_Spider',
		'Mediapartners-Google',
		'MuscatFerret',
		'NaverBot',
		'OmniExplorer_Bot',
		'polybot',
		'Pompos',
		'Scooter',
		'Teoma',
		'TheSuBot',
		'TurnitinBot',
		'Ultraseek',
		'ViolaBot',
		'webbandit',
		'www.almaden.ibm.com/cs/crawler',
        'YisouSpider',
		'ZyBorg'
    );
    
	/**
	 * @var   array �������������
	 * @since nv50
	 */
	protected static $_instances = array();
    
    /**
	 * ����һ�������ʵ�������캯����
	 *
	 * @param string $userAgent ������ַ�������
	 * @param string $accept    ��HTTP_ACCEPT����ʹ��
	 *
	 * @since nv50
	 */
	protected function __construct($userAgent = null, $accept = null)
	{
		$this->match($userAgent, $accept);
	}
    
    protected function __clone(){}
    
    /**
	 * ��ȡ���������������������򴴽�
	 *
	 * @param  string $userAgent ������ַ�������
	 * @param  string $accept    ��HTTP_ACCEPT����ʹ��
	 * @return object            ���ػ�ȡ�������������
	 *
	 * @since  nv50
	 */
	public static function getInstance($userAgent = null, $accept = null)
	{
		$signature = serialize(array($userAgent, $accept));

		if (empty(self::$_instances[$signature]))
		{
			self::$_instances[$signature] = new self($userAgent, $accept);
		}

		return self::$_instances[$signature];
	}
    
    /**
     * ƥ�价��
     * 
	 * @param string $userAgent ������ַ�������
	 * @param string $accept    ��HTTP_ACCEPT����ʹ��
     * 
     * @since nv50
     */
    public function match($userAgent = null, $accept = null)
    {
        if ( is_null($userAgent) && isset($_SERVER['HTTP_USER_AGENT']) )
		{
			$this->_agent = trim($_SERVER['HTTP_USER_AGENT']);
		}
		else
		{
			$this->_agent = $userAgent;
		}
        
        $this->_lowerAgent = strtolower($this->_agent);
        
        if ( is_null($accept) && isset($_SERVER['HTTP_ACCEPT']) )
		{
		    $this->_accept = trim($_SERVER['HTTP_ACCEPT']);
		}
		else
		{
			$this->_accept = $accept;
		}
        
        $this->_accept = strtolower($this->_accept);
        
        if ( !empty($this->_agent) )
		{
			$this->_setPlatform();

			if ( (strpos($this->_lowerAgent, 'mobileexplorer') !== false) || (strpos($this->_lowerAgent, 'openwave') !== false) ||
                 (strpos($this->_lowerAgent, 'opera mini') !== false) || (strpos($this->_lowerAgent, 'opera mobi') !== false) ||
                 (strpos($this->_lowerAgent, 'operamini') !== false) )
			{
				$this->_mobile = true;
			}
			else if (preg_match('|Opera[/ ]([0-9.]+)|', $this->_agent, $version))
			{
				$this->setBrowser('opera');
				list ($this->_majorVersion, $this->_minorVersion) = explode('.', $version[1]);

                /**
                 * ���ڸı���Opera��UA��Ϣ, ����������Ҫ���汾��XX.YY����ֻ�е��汾> 9.80ʱ��
                 * ������鿴��{@link http://dev.opera.com/articles/view/opera-ua-string-changes/}
                 */
				if ($this->_majorVersion == 9 && $this->_minorVersion >= 80)
				{
					$this->_identifyBrowserVersion();
				}
			}
			else if ( preg_match('|Chrome[/ ]([0-9.]+)|', $this->_agent, $version) )
			{
				$this->setBrowser('chrome');
				list ($this->_majorVersion, $this->_minorVersion) = explode('.', $version[1]);
			}
			else if ( preg_match('|CrMo[/ ]([0-9.]+)|', $this->_agent, $version) )
			{
				$this->setBrowser('chrome');
				list ($this->_majorVersion, $this->_minorVersion) = explode('.', $version[1]);
			}
			else if ( preg_match('|CriOS[/ ]([0-9.]+)|', $this->_agent, $version) )
			{
				$this->setBrowser('chrome');
				list ($this->_majorVersion, $this->_minorVersion) = explode('.', $version[1]);
				$this->_mobile = true;
			}
			else if ( (strpos($this->_lowerAgent, 'elaine/') !== false) || (strpos($this->_lowerAgent, 'palmsource') !== false) ||
                      (strpos($this->_lowerAgent, 'digital paths') !== false) )
			{
				$this->setBrowser('palm');
				$this->_mobile = true;
			}
			else if ( (preg_match('|MSIE ([0-9.]+)|', $this->_agent, $version)) || 
					  (preg_match('|Trident/[0-9.]+\; rv\:([0-9.]+)|', $this->_agent, $version)) || // ie11: Trident/7.0; rv:11.0
                      (preg_match('|Internet Explorer/([0-9.]+)|', $this->_agent, $version)) )
			{
				$this->setBrowser('msie');

				if (strpos($version[1], '.') !== false)
				{
					list ($this->_majorVersion, $this->_minorVersion) = explode('.', $version[1]);
				}
				else
				{
					$this->_majorVersion = $version[1];
					$this->_minorVersion = 0;
				}

				# ƥ�������ƶ��豸��Ϣ
				if (preg_match('/; (120x160|240x280|240x320|320x320)\)/', $this->_agent))
				{
					$this->_mobile = true;
				}
			}
			else if ( preg_match('|amaya/([0-9.]+)|', $this->_agent, $version) )
			{
				$this->setBrowser('amaya');
				$this->_majorVersion = $version[1];

				if (isset($version[2]))
				{
					$this->_minorVersion = $version[2];
				}
			}
			else if ( preg_match('|ANTFresco/([0-9]+)|', $this->_agent, $version) )
			{
				$this->setBrowser('fresco');
			}
			else if ( strpos($this->_lowerAgent, 'avantgo') !== false )
			{
				$this->setBrowser('avantgo');
				$this->_mobile = true;
			}
			else if ( preg_match('|Konqueror/([0-9]+)|', $this->_agent, $version) ||
                      preg_match('|Safari/([0-9]+)\.?([0-9]+)?|', $this->_agent, $version) )
			{
				$this->setBrowser('konqueror');
				$this->_majorVersion = $version[1];

				if (isset($version[2]))
				{
					$this->_minorVersion = $version[2];
				}

				// Safari.
				if (strpos($this->_agent, 'Safari') !== false )
				{
				    if (strpos($this->_agent, 'Mobile') !== false )
                    {
                        $this->_mobile = true;
                    }
                    
                    if ( $this->_majorVersion >= 60 )
                    {
    					$this->setBrowser('safari');
    					$this->_identifyBrowserVersion();
                    }
				}
			}
			else if ( preg_match('|Mozilla/([0-9.]+)|', $this->_agent, $version) )
			{
				$this->setBrowser('mozilla');

				list ($this->_majorVersion, $this->_minorVersion) = explode('.', $version[1]);
                
			    if (strpos($this->_agent, 'Mobile') !== false )
                {
                    $this->_mobile = true;
                }
			}
			else if ( preg_match('|Lynx/([0-9]+)|', $this->_agent, $version) )
			{
				$this->setBrowser('lynx');
			}
			else if ( preg_match('|Links \(([0-9]+)|', $this->_agent, $version) )
			{
				$this->setBrowser('links');
			}
			else if ( preg_match('|HotJava/([0-9]+)|', $this->_agent, $version) )
			{
				$this->setBrowser('hotjava');
			}
			else if ( (strpos($this->_agent, 'UP/') !== false) || (strpos($this->_agent, 'UP.B') !== false) ||
                      (strpos($this->_agent, 'UP.L') !== false) )
			{
				$this->setBrowser('up');
				$this->_mobile = true;
			}
			else if (strpos($this->_agent, 'Xiino/') !== false)
			{
				$this->setBrowser('xiino');
				$this->_mobile = true;
			}
			else if (strpos($this->_agent, 'Palmscape/') !== false)
			{
				$this->setBrowser('palmscape');
				$this->_mobile = true;
			}
			else if (strpos($this->_agent, 'Nokia') !== false)
			{
				$this->setBrowser('nokia');
				$this->_mobile = true;
			}
			else if (strpos($this->_agent, 'Ericsson') !== false)
			{
				$this->setBrowser('ericsson');
				$this->_mobile = true;
			}
			else if (strpos($this->_lowerAgent, 'wap') !== false)
			{
				$this->setBrowser('wap');
				$this->_mobile = true;
			}
			else if (strpos($this->_lowerAgent, 'docomo') !== false || strpos($this->_lowerAgent, 'portalmmm') !== false)
			{
				$this->setBrowser('imode');
				$this->_mobile = true;
			}
			else if ( strpos($this->_agent, 'BlackBerry') !== false )
			{
				$this->setBrowser('blackberry');
				$this->_mobile = true;
			}
			else if ( strpos($this->_agent, 'MOT-') !== false )
			{
				$this->setBrowser('motorola');
				$this->_mobile = true;
			}
			else if ( strpos($this->_lowerAgent, 'j-') !== false )
			{
				$this->setBrowser('mml');
				$this->_mobile = true;
			}
		}
    }
    
    /**
	 * ���õ�ǰ�������ϵͳƽ̨
	 *
	 * @since nv50
	 */
	protected function _setPlatform()
	{
		if (strpos($this->_lowerAgent, 'wind') !== false)
		{
			$this->_platform = 'win';
		}
		else if (strpos($this->_lowerAgent, 'mac') !== false)
		{
			$this->_platform = 'mac';
            if ( false !== strpos($this->_lowerAgent, 'ipad') )
            {
                $this->_ipad = true;
            }
            else if ( false !== strpos($this->_lowerAgent, 'iphone') )
            {
                $this->_iphone = true;
            }
            
            if ( preg_match('| os[/ ]([\d_]+)|', $this->_lowerAgent, $version) )
            {
                list ($this->_platformMajorVersion, $this->_platformMinorVersion) = explode('_', $version[1]);
            }
		}
		else
		{
			$this->_platform = 'unix';
            
            if ( preg_match('| android[/ ]([\d.]+)|', $this->_lowerAgent, $version) )
            {
                $this->_android = true;
                list ($this->_platformMajorVersion, $this->_platformMinorVersion) = explode('.', $version[1]);
            }
		}
	}

	/**
	 * Return the currently matched platform.
	 *
	 * @return  string  The user's platform.
	 *
	 * @since   nv50
	 */
	public function getPlatform()
	{
		return $this->_platform;
	}

	/**
	 * ����������İ汾��������������汾����ʹ��ʱû����������ʶ������汾
	 *
	 * @since nv50
	 */
	protected function _identifyBrowserVersion()
	{
		if (preg_match('|Version[/ ]([0-9.]+)|', $this->_agent, $version))
		{
			list ($this->_majorVersion, $this->_minorVersion) = explode('.', $version[1]);
		}
        else
        {
    		// �޷�ʶ��������汾ʱ����Ϊ0
    		$this->_majorVersion = 0;
    		$this->_minorVersion = 0;
        }
	}

	/**
	 * ���õ�ǰ�������Ϣ
	 *
	 * @param  string $browser Ҫ���õ��������Ϣ
	 *
	 * @since  nv50
	 */
	public function setBrowser($browser)
	{
		$this->_browser = $browser;
	}

	/**
	 * ��ȡ��ǰ�������Ϣ
	 *
	 * @return string ���ص�ǰ�������Ϣ
	 * @since  nv50
	 */
	public function getBrowser()
	{
		return $this->_browser;
	}

	/**
	 * ��ȡ���汾��
	 *
	 * @return  integer �������汾��
	 * @since   nv50
	 */
	public function getMajor()
	{
		return $this->_majorVersion;
	}

	/**
	 * ��ȡ�ΰ汾��
	 *
	 * @return  integer ���شΰ汾��
	 * @since   nv50
	 */
	public function getMinor()
	{
		return $this->_minorVersion;
	}

	/**
	 * ��ȡ��ǰ�ͻ���������汾
	 *
	 * @return  string ���ص�ǰ�ͻ��˰汾��Ϣ
	 * @since   nv50
	 */
	public function getVersion()
	{
		return $this->_majorVersion . '.' . $this->_minorVersion;
	}

	/**
	 * ��ȡ��ǰƽ̨�汾
	 *
	 * @return  string ���ص�ǰ�ͻ��˰汾��Ϣ
	 * @since   nv50
	 */
	public function getPlatformVersion()
	{
		return $this->_platformMajorVersion . '.' . $this->_platformMinorVersion;
	}

	/**
	 * ��ȡ����������������ַ���
	 *
	 * @return string ��������������������ַ���
	 * @since  nv50
	 */
	public function getAgentString()
	{
		return $this->_agent;
	}

	/**
	 * ��ȡ��ǰ��������ʹ�õ�HTTPЭ����Ϣ
	 *
	 * @return  string  ���ص�ǰ��������ʹ�õ�HTTPЭ����Ϣ
	 * @since   nv50
	 */
	public function getHTTPProtocol()
	{
		if ( isset($_SERVER['SERVER_PROTOCOL']) )
		{
			if ( $pos = strrpos($_SERVER['SERVER_PROTOCOL'], '/') )
			{
				return substr($_SERVER['SERVER_PROTOCOL'], $pos + 1);
			}
		}

		return null;
	}

	/**
	 * ȷ��һ��������ɷ���ʾ������MIME����
	 *
	 * ע�⣺ image / jpeg�ļ��� image/pjpeg ����ͬ�ģ�����Mozilla��������ܺ��ߡ��������ǽ������ǿ�������ͬ�ġ�
	 *
	 * @param   string  $mimetype Ҫ����MIME����
	 * @return  boolean           ���������ʾ��MIME���ͷ���TRUE�����򷵻�FALSE
	 *
	 * @since   nv50
	 */
	public function isViewable($mimetype)
	{
		$mimetype = strtolower($mimetype);
		list ($type, $subtype) = explode('/', $mimetype);

		if (!empty($this->_accept))
		{
			$wildcard_match = false;

			if (strpos($this->_accept, $mimetype) !== false)
			{
				return true;
			}

			if (strpos($this->_accept, '*/*') !== false)
			{
				$wildcard_match = true;

				if ($type != 'image')
				{
					return true;
				}
			}

			// ����Mozilla��PJPEG / jpeg�ļ�������
			if ($this->isBrowser('mozilla') && ($mimetype == 'image/pjpeg') && (strpos($this->_accept, 'image/jpeg') !== false))
			{
				return true;
			}

			if (!$wildcard_match)
			{
				return false;
			}
		}
        
		if ( $type != 'image')
		{
			return false;
		}

		return (in_array($subtype, $this->_images));
	}

	/**
	 * �жϿͻ��˵�ǰʹ�õ��ǲ������������$browser�Ƿ���ͬ
	 *
	 * @param  string  $browser Ҫ�����������Ϣ
	 * @return boolean          �����ͬ����TRUE�����򷵻�FALSE
	 *
	 * @since   nv50
	 */
	public function isBrowser($browser)
	{
		return ($this->_browser === $browser);
	}

	/**
	 * �жϿͻ����Ƿ�Ϊ��������֩������
	 *
	 * @return  boolean �������������֩�����淵��TRUE�����򷵻�FALSE
	 *
	 * @since   nv50
	 */
	public function isRobot()
	{
		foreach ($this->_robots_tables as $robot)
		{
			if ( false !== strpos($this->_agent, $robot) )
			{
				return true;
			}
		}

		return false;
	}

	/**
	 * �жϿͻ����Ƿ�Ϊ�ƶ��豸
	 *
	 * @return boolean ������ƶ��豸����TRUE�����򷵻�FALSE
	 *
	 * @since  nv50
	 */
	public function isMobile()
	{
	    if ( $this->isAndroid() || $this->isIPhone() )
        {
            $this->_mobile = true;
        }
        
	    return $this->_mobile;
	}

	/**
	 * �жϿͻ����Ƿ�ΪiPhone
	 *
	 * @return boolean ������ƶ��豸����TRUE�����򷵻�FALSE
	 *
	 * @since  nv50
	 */
	public function isIPhone()
	{
		return $this->_iphone;
	}

	/**
	 * �жϿͻ����Ƿ�ΪiPad
	 *
	 * @return boolean ������ƶ��豸����TRUE�����򷵻�FALSE
	 *
	 * @since  nv50
	 */
	public function isIPad()
	{
		return $this->_ipad;
	}

	/**
	 * �жϿͻ����Ƿ�ΪAndroid
	 *
	 * @return boolean ������ƶ��豸����TRUE�����򷵻�FALSE
	 *
	 * @since  nv50
	 */
	public function isAndroid()
	{
		return $this->_android;
	}

	/**
	 * �ж��Ƿ���SSL����
	 *
	 * @return  boolean ����Ƿ���TRUE�����򷵻�FALSE
	 *
	 * @since   nv50
	 */
	public function isSSLConnection()
	{
		return ( (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on')) || getenv('SSL_PROTOCOL_VERSION') );
	}
    
    /**
     * ��ȡHTTP��������
     * 
     * @return string ���ػ�ȡ��������
     * @since  nv50
     */
    public function getHttpConnection()
    {
        if ( $this->isSSLConnection() )
        {
            return 'https://';
        }
        
        return 'http://';
    }
}

