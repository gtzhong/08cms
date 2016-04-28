<?php
/**
* ���������������á���ȫ�ֱ�����ϵͳȫ�����õ���صĴ�����
* ���ù��ˣ���ȫԤ�����
*/
defined('M_COM') || exit('No Permission');
abstract class cls_envBase{
	
    const _08_HASH = '_08_hash';
    private static $hash = '';
    public static $__baseIncConfigs = array();
    private static $globals = NULL;
	protected static $_08_GET = NULL;
	protected static $_08_POST = NULL;
	protected static $_08_COOKIE = NULL;

    /**
     * ���ؼ��ܺ���
     * 
     */
    public static function LoadZcore(){
		include _08_EXTEND_LIBS_PATH .'classes'.DS.'zcore'.DS.'cecore'.(PHP_VERSION < '5.3.0' ? '' : '_1').'.cls.php';
    }

    /**
     * XSSת��
     * 
     * @param mixed $values     Ҫת��ı���ֵ
     * @param bool  $delete_rep �Ƿ�ɾ�������ַ�
     * @param bool  $derep      �Ƿ񷴱���֮ǰ�����������
     * @param int   $quotes     htmlspecialchars�����ĵڶ�������{@link http://docs.php.net/manual/zh/function.htmlspecialchars.php}
     */
    public static function repGlobalValue( &$values, $delete_rep = false, $derep = false, $quotes = ENT_QUOTES ){
		if(is_null(self::$globals)){
			self::$globals = array_merge( (array) self::_GET_POST(), (array) self::_COOKIE());
			self::$globals = cls_array::_array_multi_to_one(self::$globals, true);
		}
        
        if ( is_array($values) )
        {
            cls_array::_array_uasort($values);
            cls_array::_array_uasort(self::$globals);

            $newValues = array();
            foreach($values as $key => $value)
            {
                if ( is_array($value) || cls_Array::_in_array(self::$globals, array($value), 2) || 
                     cls_Array::_in_array(array_keys(self::$globals), array($value), 2) )
                {
                    self::repGlobalValue($value, $delete_rep, $derep, $quotes);
                }elseif(isset(self::$globals[$key])){
                    $_values = array($key => $value);
                    $value = self::repAction($_values, $delete_rep, $derep, $quotes);
				}
                $key = mhtmlspecialchars($key);
                $newValues[$key] = $value;
            }
            
            $values = $newValues;
        }
        else
        {
            # ��ʼת��
            $values = self::repAction($values, $delete_rep, $derep, $quotes);
        }
    }
    
    /**
     * ��ʼ����XSS��ע����֧��$delete_rep����ַ�
     */
    private static function repAction( $value, $delete_rep = false, $derep = false, $quotes = ENT_QUOTES )
    {
        if ( is_array($value) )
        {
            $key = key($value);
            $value = current($value);
        }
        if ( $delete_rep )
        {
            $value = preg_replace('@<script.*>.*</script>@isU', '', $value);
        }
        
        # ��������Ѿ������������
        if ( $derep )
        {
            return mhtmlspecialchars($value, $quotes, $delete_rep);
        }
            
        foreach ( self::$globals as $_key => $global)
        {
            if ( !is_string($global) || (false === @strpos($global, $value)) && (false === @strpos($value, $global)) &&
                 (false === @strpos($value, $_key)) )
            {
                continue;
            }
            
            if ( isset($key) && ($key == $_key) || (strlen($global) > strlen($value)) )
            {
                $value = self::filteringCodingTable($value, $delete_rep, $quotes);
            }
            else
            {
                if ( false !== @strpos($value, $_key) )
                {
                    $value = str_replace($_key, self::filteringCodingTable($_key, $delete_rep, $quotes), $value);
                }
                else
                {
                	$value = str_replace($global, self::filteringCodingTable($global, $delete_rep, $quotes), $value);
                }
            }
        }
        
        return $value;
    }
    
    /**
     * �����뾭�� htmlspecialchars ������������ַ���
     * 
     * @param  mixed  $values   �Ѿ���������ַ���������
     * @param  array  $varnames Ҫ������ı���������ֵ�����δָ���򷴱�������
     * @since  nv50
     */
    public static function deRepGlobalValue($values, array $varnames = array(), $quotes = ENT_QUOTES )
    {
        if ( is_array($values) )
        {
            foreach($values as $key => &$value )
            {
                $value = self::deRepGlobalValue($value, $varnames, $quotes);               
            }
        }
        else
        {
        	$values = (string) $values;
            if ( !empty($varnames) )
            {
                foreach ( $varnames as $value ) 
                {
                    if(empty($value)) continue;
					if ( false !== strpos($values, $value) )
                    {
                        $values = str_replace($value, htmlspecialchars_decode($value, $quotes), $values);
                    }
                }
            }
            else
            {
                $values = htmlspecialchars_decode($values, $quotes);            	
            }
        }
		return $values;
    }
    
    /**
     * �����Ѿ���������ַ�
     * 
     * @param  string $string Ҫ������ַ�
     * @return string         �������ַ�
     * 
     * @since  1.0
     */
    private static function filteringCodingTable( $string, $delete_rep = false, $quotes = ENT_QUOTES )
    {
        $translation_table = get_html_translation_table();
        $array = array();
        for ($i = 0; $i < count($translation_table); ++$i) 
        {
            $array[] = "[__08cms__$i]";
        }
        # ���Ѿ���������������Զ����ַ��滻
        $string = str_replace($translation_table, $array, $string);
        # ��ʼ��������
        if(0 === stripos($string, 'http'))
        {
			$string = self::repGlobalURL($string);
		}
        else
        {
			$string = mhtmlspecialchars($string, $quotes, $delete_rep);
		}
        # ���Զ����ַ���ԭ���Ѿ������������
        $string = str_replace($array, $translation_table, $string);
        
        return $string;
    }
    
    /**
     * ����URL
     * 
     * @param  string $url    Ҫ�����URL
     * @return string $my_url ������URL
     * 
     * @since  1.0
     */
    public static function repGlobalURL( $url )
    {
        $my_url = '';
        $url = str_replace('&', '[--08cms--]', $url);
        $url_info = parse_url($url);
        if ( isset($url_info['scheme']) )
        {
            $my_url .= $url_info['scheme'] . '://';
        }
        
        if ( isset($url_info['user']) )
        {
            $my_url .= $url_info['user'] . ':';
        }
        
        if ( isset($url_info['pass']) )
        {
            $my_url .= $url_info['pass'] . '@';
        }
        
        if ( isset($url_info['host']) )
        {
            $my_url .= $url_info['host'];
        }

        if ( isset($url_info['port']) )
        {
            $my_url .= ':'.$url_info['port'];
        }
        
        if ( isset($url_info['path']) )
        {
            $my_url .= mhtmlspecialchars($url_info['path']);
        }
        
        if ( isset($url_info['query']) )
        {
            $my_url .= '?' . mhtmlspecialchars(urldecode($url_info['query']));
        }
        
        if ( isset($url_info['fragment']) )
        {
            $my_url .= '#' . mhtmlspecialchars($url_info['fragment']);
        }
        
        $my_url = str_replace('[--08cms--]', '&', $my_url);
        return $my_url;
    }
	
	public static function OnlineIP(){
		if(isset($_SERVER['HTTP_X_REAL_FORWARDED_FOR']) && !empty($_SERVER['HTTP_X_REAL_FORWARDED_FOR'])){
			$onlineip = $_SERVER['HTTP_X_REAL_FORWARDED_FOR'];
		}elseif(isset($_SERVER['HTTP_X_FORWARDED_FOR']) && !empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
			$onlineip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		}elseif(isset($_SERVER['HTTP_CLIENT_IP']) && !empty($_SERVER['HTTP_CLIENT_IP'])){
			$onlineip = $_SERVER['HTTP_CLIENT_IP'];
		}else{
            if (isset($_SERVER['REMOTE_ADDR']))
            {
			     $onlineip = $_SERVER['REMOTE_ADDR'] == '::1' ? '127.0.0.1' : $_SERVER['REMOTE_ADDR'];
            }
            else
            {
            	$onlineip = '127.0.0.1';
            }
		}
		preg_match("/[\d\.]{7,15}/",$onlineip,$onlineipmatches);
		$onlineip = isset($onlineipmatches[0]) ? $onlineipmatches[0] : '';
		return $onlineip;
	}
	
	public static function IpBanned($onlineip){
		if($bannedipstr = implode('|',cls_cache::Read('bannedips'))){
			if(preg_match("/^($bannedipstr)$/",$onlineip)) return true;
		}
		return false;
	}
	
	//�Ƿ�������������
	public static function IsRobot($user_agent = ''){
		$kw_spiders = 'Bot|Crawl|Spider|slurp|sohu-search|lycos|robozilla';
		$kw_browsers = 'MSIE|Netscape|Opera|Konqueror|Mozilla';
		$user_agent || $user_agent = empty($_SERVER['HTTP_USER_AGENT']) ? '' : $_SERVER['HTTP_USER_AGENT'];
		if(!in_str('http://',$user_agent) && preg_match("/($kw_browsers)/i",$user_agent)){
			return false;
		}elseif(preg_match("/($kw_spiders)/i",$user_agent) || (class_exists('_08_Browser') && _08_Browser::getInstance()->isRobot())) return true;
		return false;
	}
	
	public static function RobotFilter(){
		if(defined('NOROBOT') && ISROBOT) exit(header("HTTP/1.1 403 Forbidden"));
	}
	
	//ֻ���������Ӳ�����ҳ��������������
	//$Paramsҳ���ⲿ������$AllowKeys�����Ĳ�����(��Ϊ�������κβ���)
	public static function AllowRobot($Params = array(),$AllowKeys = array()){
		if((defined('ISROBOT') && !ISROBOT) || !$Params) return;
		foreach($Params as $k => $v){
			if(!in_array($k,$AllowKeys)){
				header("HTTP/1.1 403 Forbidden");
				exit("[cls_envBase::AllowRobot()]NetworkError: 403 Forbidden"); //�����ֶ��޸�UA����ֹͣ��ʾ��������ʾ��Ϣ���ڵ���
			}
		}
	}    
	
	public static function GLOBALS()
    {
	    if ( isset($_SERVER['REQUEST_URI']) )
        {
            $_SERVER['REQUEST_URI'] = self::maddslashes(rawurldecode($_SERVER['REQUEST_URI']));
        }
		if(isset($_REQUEST['GLOBALS']) || isset($_FILES['GLOBALS'])) exit('08cms Error');
	}
	
    /**
     * ��ȡ$_GET����
     * 
     * @param  mixed $varnames ��Ҫ��ȡ�Ĳ�������
     * @return array           ��ȡ����$_GET��������
     * 
     * @since  nv50
     */
	public static function &_GET( $varnames = '' ){
	    # ��MVC��ʹ�ò���MVC�ܹ��ϵĺ���ʱҲ��ͨ���÷�����ȡMVC��GET����
	    if ( class_exists('cls_frontController') && cls_frontController::checkActionMVC() )
        {
            $frontController = cls_frontController::getInstance(array_merge($_POST, $_GET));
            $_GET = $frontController->getParams();
        }
        
		if(is_null(self::$_08_GET)){
			//��α��̬URL�еı���ת��$_GET
			if($QueryStringParams = self::QueryStringToArray(defined('UN_VIRTURE_URL') ? 1 : 0)){
				$_GET = $QueryStringParams;
			}
			self::$_08_GET = self::_PreDealGP($_GET);
		}
        
        // ��$_GETȫ��������д����ֱֹ����$_GET['xxx']����ȡ����ֵ��������ȫ����
        $_GET = self::$_08_GET;
        self::initVarnames($varnames, self::$_08_GET);
		return self::$_08_GET;
	}
    
    /**
     * ��ʼ���������Ԫ�أ��÷����ɼ���δ��ʼ������ʹ�õı���
     * ����ñ�����������$params���������Զ���ʼ��Ϊnull
     * 
     * @param mixed $varnames ��Ҫ��ʼ���Ĳ�������
     * @param array $params   �Ӹö������ʼ��Ԫ��
     * 
     * @since nv50
     */
    public static function initVarnames($varnames, array &$params)
    {
        if ( !is_array($varnames) )
        {
            $varnames = array_map('trim', array_filter(explode(',', (string) $varnames)));
        }
        
        foreach ( $varnames as $varname ) 
        {
            if ( !isset($params[$varname]) )
            {
                $params[$varname] = null;
            }
        }
    }	
	
    /**
     * ��ȡ$_POST����
     * 
     * @param  mixed $varnames ��Ҫ��ȡ�Ĳ�������
     * @return array           ��ȡ����$_POST��������
     * 
     * @since  nv50
     */
	public static function &_POST( $varnames = '' ){
		if(is_null(self::$_08_POST)){
			self::$_08_POST = self::_PreDealGP($_POST);
		}
        
        self::initVarnames($varnames, self::$_08_POST);
        // ��$_POSTȫ��������д����ֱֹ����$_POST['xxx']����ȡ����ֵ��������ȫ����
        $_POST = self::$_08_POST;
		return self::$_08_POST;
	}
	
    /**
     * ��ȡ$_GET/$_POST����
     * 
     * @param  mixed $varnames ��Ҫ��ȡ�Ĳ�������
     * @return array           ��ȡ����$_GET/$_POST��������
     * 
     * @since  nv50
     */
	public static function _GET_POST( $varnames = '' ){
		return self::_GET( $varnames ) + self::_POST( $varnames );
	}
	
	public static function _FILES(){
		if($_FILES) $_FILES = self::maddslashes($_FILES);
	}
	
	public static function &_COOKIE(){
		if(is_null(self::$_08_COOKIE)){
			global $ckpre;
			$cklen = strlen($ckpre);
			if(!empty($_COOKIE)){ 
				foreach((array)$_COOKIE as $k => $v){
					$_COOKIE[$k] = self::maddslashes($v); //���еĶ�ת��һ��,����ֱ��ʹ��$_COOKIE
					if(substr($k,0,$cklen) == $ckpre){ 
						self::$_08_COOKIE[(substr($k,$cklen))] = $_COOKIE[$k];		
					}
				}
			}
		}
		return self::$_08_COOKIE;
	}
	
    /**
     * ȡ�ö�������
     * 
     * @param string $url		ָ����url
     * 
     * @return string			�õ�ָ��url�Ķ�������
     * @static
     * @since 1.0
     */ 
	public static function TopDomain($host){
		if(strpos($host,':/')){
			$parts = parse_url($host);
			$host = $parts['host']; 
		}
		$arr = explode('.',$host);
		$pos = strpos($host,'.');
		if(empty($pos)){ //localhost/pcname
			return '';
		}elseif(is_numeric($arr[count($arr)-1])){ //IP(ipv6δ����)
			return '';	
		}else{ //����
			$suf = strlen($arr[count($arr)-1])==2 ? substr($host,-3) : ''; //.xxΪ���Ҽ���������
			$host = $suf ? substr($host,0,strlen($host)-3) : $host;
			$arr = explode('.',$host); $re = '';
			$tops = '.com.net.org.edu.gov.int.mil.top.cat.biz.pro.tel.xxx.aero.arpa.asia.coop.info.mobi.name.jobs.museum.travel.';
			for($i=count($arr)-1;$i>=0;$i--){
				if(strstr($tops,$arr[$i])){ $suf = '.'.$arr[$i]."$suf";	} 
				// ����:www.net.cn,topdomain��net.cn����www.net.cn������������
				if(!strstr($tops,$arr[$i])){
					$re = '.'.$arr[$i];	
					break;
				}
			}
			return substr($re.$suf,1);
		} 
	}
	
	/**
	 * ���վ���Ƿ�رգ����ֻ����Ƿ�ر�
	 *
	 * js.php,ptool.php��js�е��ã���$noout = 1����ʾԭ��
	 *
	 * @param  bool    $noout ��Ҫ��ʾ�ر�ԭ��Ĭ��0:��ʾԭ��,1:����ʾ
	 * @return NULL   (ֱ�������ֹͣ,�޷���)
	 */
	public static function CheckSiteClosed($noout = 0){
		global $cmsclosed,$cmsclosedreason,$enable_mobile;
		if($cmsclosed){
			if(!$noout){
				cls_message::show(empty($cmsclosedreason) ? '��վ����ά�������Ժ������ӡ�': mnl2br($cmsclosedreason));
			}else exit();
		}elseif(defined('IN_MOBILE') && empty($enable_mobile)){
			if(!$noout){
				cls_message::show('�ֻ�����δ����');
			}else exit();
		}
	}
	
	/**
     * ��ȡCSRF HASHֵ
     * 
     * @param  bool   $reset �Ƿ��������ã�trueΪ�������ã�falseΪ��ȡCOOKIE����������ڲ���������
     * 
     * @return string $hash ����������ɵ�hashֵ
     * @since  1.0
     */
	public static function getHashValue( $reset = false )
    {
        $cookies = self::_COOKIE();
        if ( $reset || empty($cookies[self::_08_HASH]) )
        {
            if ( $reset || empty(self::$hash) )
            {
                $hash = _08_Encryption::password(cls_string::Random(8)); # ����Hashֵ
                if ( !headers_sent() )
                {
                    msetcookie(self::_08_HASH, $hash, 3600, true);
                    self::$hash = $hash;
                }
            }
            else
            {
            	$hash = self::$hash;
            }
        }
        else
        {
        	$hash = $cookies[self::_08_HASH];
        }
        
        return $hash;
    }
	
	/**
	 * ��ȡϵͳ��Ȩ�е������(����Ȩ��������Ȩϵͳ���ͣ���Ȩ��)
	 *
	 * @return string
	 */
	public static function GetLicense($key = 'lic_str'){
		if(!in_array($key,array('lic_domain','lic_type','lic_str',))) return '';
		$certvars = cls_cache::cacRead('certvars');
		return empty($certvars[$key]) ? '' : $certvars[$key];
	}
	
	/**
	 * ��α��̬���$_SERVER['QUERY_STRING']�еõ���������
	 *
     * @param  int $un_virtual α��̬������ʽ��0-������1-����
	 *
	 * @return array ��α��̬�����ִ��α��̬�򷵻ؿ�����
	 */
	public static function QueryStringToArray($un_virtual = 0){
		$ReturnArray = array();
         if (!isset($_SERVER['QUERY_STRING'])) return $ReturnArray;
         elseif($un_virtual && $QueryString = rawurldecode($_SERVER['QUERY_STRING'])){
			 
			 $QueryString = preg_replace("/(\/domain\/[^\/]+)/i",'',$QueryString);#���˵���������'-'��������������������ƥ�䵽
			 
			if(preg_match("/(\w+)-(.+?)(?:$|\/|\.html)/i",$QueryString)){# ��������α��̬�ִ�֮���ٸ��� &xxx=3 �����ķ�ʽ
				$QueryString = preg_replace("/(\w+)-(.+?)(?:$|\/|\.html)/is","\\1=\\2&",$QueryString);
				parse_str($QueryString,$ReturnArray);
			}
		}
		return $ReturnArray;
	}
	
	/**
     * ��ȡ$mconfigs��ֵ
     * @param  string  $key ������֧��'xx.kk.dd'�õ�$mconfigs['xx']['kk']['dd']
     */
	public static function mconfig($Key = ''){
		$mconfigs = cls_cache::Read('mconfigs');
		if(!($KeyArray = cls_Array::ParseKey($Key))) return $mconfigs;
		return cls_Array::Get($mconfigs,$Key);
    }
	/**
     * ��ȡ$GLOBAL��ֵ
     * @param  string  $key ������֧��'xx.kk.dd'�õ�$GLOBALS['xx']['kk']['dd']
     */
	public static function GetG($Key = ''){
		return cls_Array::Get($GLOBALS,$Key);
    }
	
	/**
     * ����$GLOBAL��ֵ
     * @param  string  $key ������֧��'xx.kk.dd'����$GLOBALS['xx']['kk']['dd']
     * @param  string  $Value ֵ
    */
	public static function SetG($Key = '',$Value = 0){
		cls_Array::Set($GLOBALS,$Key,$Value);
    }
   
    /**
     * ��ȡbase.inc.php�ļ���ı���ֵ���������ط����þ�������global
     * 
     * @param  string $var Ҫ��ȡ�ı������ƣ���������˸���ֻ���ظ����Ʊ�����ֵ,����Զ��ŷֿ�
     * @return array       ���������ָ�����������������飬�����������ر���ֵ��
     * 
     * @since  nv50
     */
    public static function getBaseIncConfigs( $var = '' )
    {
        if ( empty(self::$__baseIncConfigs) )
        {
			$_KeyArray = array(
			'_08_extend_dir','dbuser','dbpw','dbhost','dbname','pconnect','tblprefix','dbcharset','drivers', 'dbport',
			'mcharset','cms_version','lan_version','ckpre','ckdomain','ckpath','adminemail','phpviewerror','is_menghu','is_bz',
			'excache_prefix','ex_memcache_server','ex_memcache_port','ex_memcache_pconnect','ex_memcache_timeout','ex_eaccelerator','ex_xcache','ex_secache','ex_secache_size',
			);
            include M_ROOT . 'base.inc.php';
			foreach($_KeyArray as $k){
				if(isset($$k)){
					self::$__baseIncConfigs[$k] = $$k;
				}
                else
                {
                	self::$__baseIncConfigs[$k] = null;
                }
			}
        }
        
        # ���ָ����Ҫ��ȡ�ı���������ֻ���ز������ݵı���ֵ
        # ���varΪ���������򷵻ظñ���ֵ�����varΪ�����(','�ָ�)����������
       if ( !empty($var) ){
            $vars = array_filter(explode(',', (string) $var));
			if(count($vars) > 1){
				$varValues = array();
				foreach ( $vars as $varName ) 
				{
					$varName = trim($varName);
					$varValues[$varName] = isset(self::$__baseIncConfigs[$varName]) ? self::$__baseIncConfigs[$varName] : NULL;
				}
				return $varValues;
			}else{
				$var = trim($var);
				return isset(self::$__baseIncConfigs[$var]) ? self::$__baseIncConfigs[$var] : NULL;
			}
        }else{
			return self::$__baseIncConfigs;
		}
    }
	
	# ��GP�������Ԥ����
	private static function _PreDealGP($SourceArray = array()){
		$ReturnArray = array();
		foreach($SourceArray as $k => $v){
			if($k == 'GLOBALS') exit('08cms Error');
			if($k{0} ==  '_') continue; 
			if(in_array($k,array('infloat','handlekey','aid','caid','mid','chid','mchid','addno','win_id','mincount','maxcount','wmid','field_id'))){
				$v = (int)$v;
			}else{
				$v = self::maddslashes($v);
			}
			$ReturnArray[$k] = $v;
		}
		return $ReturnArray;
	}
	
    /**
     * ��ָ����Ԥ�����ַ�(',",\,NULL)ǰ��ӷ�б�ܣ�֧�����飬ע�⣺�粻������GPC��Ҫ��force=1
     *
     * @param  string   $s     ԭʼ�ַ���������������
     * @param  bool     $force ǿ��ѡ��
     * @return string   $s     �������ַ���
     */
    public static function maddslashes($s, $force = 0)
    {
    	defined('QUOTES_GPC') || define('QUOTES_GPC', @get_magic_quotes_gpc());
    	if(!QUOTES_GPC || $force){
    		if(is_array($s)){
    			foreach($s as $k => $v) $s[$k] = self::maddslashes($v, $force);
    		}else $s = addslashes($s);
    	}
    	return $s;
    }
   
    /**
     * ͨ��gzipenable�����ж�Output������ʽ,
     * @param  bool     $checkGzip Ϊtrue����php������gzipenable����
     */
    public static function mob_start($checkGzip=false)
    {   
		if($checkGzip){		  
			if( self::mconfig('gzipenable') && self::gzipenable() )
				{
					ob_start('ob_gzhandler');
				}
				else
				{
					ob_start();
				}   
		}else{
		  ob_start();
		}
    }
    
    /**
     * �жϵ�ǰ�����ܷ���GZIP����
     * 
     * @return bool ��������ܿ���gzip����TRUE������FALSE
     * 
     * @since  nv50
     */
	public static function gzipenable()
    {
        return (bool) (extension_loaded('zlib') && !ini_get('zlib.output_compression'));
    }
   
    /**
     * ��黷��
     * 
     * @since nv50
     */
    public static function __checkEnvironment()
    {
       /*if( @ini_get('register_globals') )
        {
            die('Ϊ�����İ�ȫ���뽫PHP.INI�е�register_globals����ΪOff����������޷�������');
        }*/
        
        $basefile = M_ROOT . 'base.inc.php';
        if ( !is_file($basefile) )
        {
            die('base.inc.php�����ڣ������ϴ�!');
        }
        
        # Ϊ����֮ǰ�������У���ʱ�ȱ����������ж�
        if( !@ini_get('short_open_tag') )
        {
            die('�뽫PHP.INI�е�short_open_tag����ΪOn����������޷�������');
        }
		
		# Ϊ��ȷ��Ĭ�Ͽ����Ļ���������gzip��ʽ����,�ȱ����������ж�
        if( @ini_get('output_handler') != '' )
        {
            die('�뽫PHP.INI�е�output_handler����Ϊ�գ���������޷�������');
        }
		
/*		
		
		if ( !is_file(_08_CACHE_PATH . 'install.lock') )
		{
			header('Location: ' . substr($_SERVER['REQUEST_URI'], 0, strpos($_SERVER['REQUEST_URI'], 'index.php')) . 'install/index.php');
			exit;
		}
		
*/		
    }
           
    /**
     * ����ClickJacking������ע�⣺�ñ�ͷ������ֵ���ֱ�Ϊ��
     * DENY               �ܾ���ǰҳ������κ�frameҳ��
     * SAMEORIGIN         ��frameҳ��ĵ�ַֻ��ΪͬԴ�����µ�ҳ��
     * ALLOW-FROM origin  ��������frame���ص�ҳ���ַ
     */ 
    public static function filterClickJacking()
    {
        $gets = self::_GET('domain');
		$mconfigs = cls_cache::Read('mconfigs');		
        if (empty($gets['domain']) || !self::setDoMain($gets['domain']))
        {
           if (!preg_match("/{$mconfigs['cms_top']}$/i", @$_SERVER['HTTP_HOST'])){
			  cls_HttpStatus::trace(array('X-Frame-Options' => 'SAMEORIGIN'));
		   }
        }
    }
    
    /**
     * ����Ajax����ֻ������IE8���ϣ���ֻҪ�ڵ���JSʱ�ഫ��һ�������� domain=news.08cms.com   �������Զ���
     * 
     * @example $.get($cms_abs + "tools/ajax.php?action=get_regcode&domain=" + document.domain, function(data) { .... });
     */
    public static function setDoMain( $domain )
    {
        $mconfigs = cls_cache::Read('mconfigs');
        $domain = (string) $domain;
        if ( isset($_SERVER['SERVER_PORT']) && !empty($domain) && !filter_var($domain, FILTER_VALIDATE_IP) )
        {
            # ֻ����ͬ����������������������
            if ( preg_match("/{$mconfigs['cms_top']}$/i", $domain) )
            {
                $domainValue = ($_SERVER['SERVER_PORT'] == 443 ? 'https://' : 'http://') . $domain;              
                
                cls_HttpStatus::trace(array('Access-Control-Allow-Origin' => $domainValue));
				#cls_HttpStatus::trace(array('X-Frame-Options' => 'ALLOW-FROM ' . $domainValue));
				cls_HttpStatus::trace(array('X-Frame-Options' => 'ALLOWALL'));	//ALLOWALL				
                cls_HttpStatus::trace(array('Access-Control-Allow-Headers' => 'X-Requested-With,X_Requested_With'));
                return true;
            }
        }
        
        return false;
    }
}
