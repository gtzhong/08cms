<?php
/**
* url��صĴ�����
* 
*/
class cls_url{
	
	/**
	 * ��url��������
	 *
	 * @param  string  $url    ����ǰ��$url
	 * @return string  $url    ������$url
	 */
	public static function domain_bind($url){
		$na = cls_cache::Read('domains');
		if(!$url || empty($na['from'])) return $url;
		foreach($na['from'] as $k => $v){
			$nurl = @preg_replace($v,$na['to'][$k],$url);
			$url = $nurl ? $nurl : $url;
		}
		return $url;
	}
	
	/**
	 * ����ϵͳ����[����]��url
	 *
	 * @param  string  $url    ����ǰ��$url
	 * @return string  $url    ������$url
	 */
	public static function remove_index($url){
		global $hiddensinurl;
		if(!$url || !($arr = explode(',',$hiddensinurl))) return $url;
		return str_replace($arr,'',$url);
	}
	
	/**
	 * ����html�ֶ�ʱ�����������url
	 *
	 * @param  string  &$str    ����ǰ��$str
	 * @return string  &$str    ������$str
	 */
	public static function html_atm2tag(&$str){
		global $ftp_url,$ftp_enabled,$cms_abs;
		$re = preg_quote($cms_abs,"/");
		if($ftp_enabled && $ftp_url) $re .= '|'.preg_quote($ftp_url,"/");
		$str = addslashes(preg_replace("/(=\s*['\"]?)($re)(.+?['\" >])/ies",'"$1"."<!cmsurl />"."$3"',stripslashes($str)));
	}
	
	/**
	 * �������ݿⱣ��·�����ж��Ƿ�Ϊ ftp�������Ǳ��ظ���,ֻ�ܷ�����������
	 *
	 * @param  string  $str     ԭʼ�����ݿⱣ��·��
	 * @return bool    ---      �Ƿ�Ϊftp����
	 */
	public static function is_remote_atm($str){
		global $ftp_enabled,$other_ftp_dir,$ftp_url;
		if(!$ftp_enabled || !$ftp_url || empty($other_ftp_dir)) return false;
		$otherftpdir = str_replace(array('./','/','-'),array('',"\\/","\\-"),$other_ftp_dir);
		return preg_match('/(<\!cmsurl \/>|<\!ftpurl \/>)?('.$otherftpdir.')/i',$str) ? true : false;
	}
	
	/**
	 * ����url���ж��Ƿ�Ϊ�����ļ�(����)
	 *
	 * @param  string  $url     ԭʼ��url�������Ǳ����ִ�Ҳ��������ʾurl
	 * @param  int     $isatm   ������ǣ�0-�Ǹ�����1-ftp�����㱾�أ�2-ftp�������㱾��
	 * @return bool    ---      �Ƿ�Ϊ�����ļ�
	 */
	public static function islocal($url,$isatm=0){
		global $cms_abs,$ftp_url,$ftp_enabled;
		if(strpos($url,':/') === false) return true;
		if(preg_match(u_regcode($cms_abs),$url)) return true;
		if($ftp_enabled && $ftp_url && ($isatm == 1) && preg_match(u_regcode($ftp_url),$url)) return true;
		return false;
	}
	
	/**
	 * �� ԭʼ�����url�ַ� ת��Ϊ ���������url
	 *
	 * @param  string  $url     ԭʼ�����url�ַ�
	 * @param  bool    $ishtml  Ĭ��0��1:��ʾ�������html�ı�,Ҫ���������Ƕ�ĸ���
	 * @return string  $url     �������������url
	 */
	public static function tag2atm($str,$ishtml=0){
		//ishtml:�����1�Ļ��������html�ı���Ҫ���������Ƕ�ĸ���
		global $ftp_url;
		if(empty($str)) return '';
		if($ishtml){ //Html
			if(preg_match_all("/(=\s*['\"]?)((<\!cmsurl \/>|<\!ftpurl \/>)(.+?))['\" >]/i",$str,$arr) && !empty($arr[2])){
				foreach($arr[2] as $v) $str = str_replace($v,self::tag2atm($v),$str);
			}
			return $str;
		}else{
			//����֮ǰ�ķ�ʽ�����ܻ���������
			$str = str_replace(array('<!cmsurl />','<!ftpurl />'),'',$str);
			if(self::is_remote_atm($str)) return $ftp_url.$str;
			else return self::view_url($str);
		}
	}
	

	/**
	 * ˵����
	 *
	 * @param  array  &$item   
	 * @param  bool   $fmode  
	 * @return NULL   ---  
	 */
	public static function arr_tag2atm(&$item,$fmode=''){
		$fmodearr = array(
		'' => array('fields','chid'),
		'f' => array('ffields','chid'),
		'm' => array('mfields','mchid'),
		'pa' => array('pafields','paid'),
		'ca' => array('cnfields',0),
		'cc' => array('cnfields','coid'),
		);
		if(!empty($fmodearr[$fmode])){
			$fields = @cls_cache::Read($fmodearr[$fmode][0],$fmodearr[$fmode][1] ? $item[$fmodearr[$fmode][1]] : 0);
			foreach($fields as $k => $v){
				if(isset($item[$k]) && $v['datatype'] == 'htmltext'){
					$item[$k] = self::tag2atm($item[$k],1);
				}
			}
		}
	}
	
	/**
	 * 
	 *
	 * @param  string  $url     ·��
	 * @return string  $url     ������·��
	 */
	public static function local_file($url){
		global $cms_abs;
		return self::islocal($url) ? M_ROOT.preg_replace(u_regcode($cms_abs),'',$url) : $url;
	}
    
    /**
     * �ѱ����ļ�ת��URL
     * 
     * @param  string $localFile Ҫת���ı����ļ���ַ
     * @return string            ת������ļ�URL��ַ
     * 
     * @since  nv50
     */
    public static function localToUrl( $localFile )
    {
        return _08_CMS_ABS . str_replace(array(M_ROOT, '\\'), array('', '/'), $localFile);
    }
	
	/**
	 * ����url�õ�����·��//incftpͬʱ����ftp��url//����ǵ����������򷵻�ԭurl
	 *
	 * @param  string  $url     ·��
	 * @param  bool    $incftp  ����ftp��url
	 * @return string  $url     ������·��
	 */
	public static function local_atm($url,$incftp=0){
		//����url�õ�����·��//incftpͬʱ����ftp��url//����ǵ����������򷵻�ԭurl
		global $cms_abs,$ftp_url,$ftp_enabled;
		$url = preg_replace(u_regcode($cms_abs),'',$url);
		if($incftp && $ftp_enabled && $ftp_url) $url = preg_replace(u_regcode($ftp_url),'',$url);
		return (strpos($url,':/') === false ? M_ROOT : '').$url;
	}
	
	/**
	 * ���ݱ���·�����õ�����ͼ�ı���·����
	 *
	 * @param  string  $local   ·��
	 * @param  int     $width   ��
	 * @param  int     $height  ��
	 * @return string  $local   ������·��
	 */
	public static function thumb_local($local,$width,$height){//���ݱ���·�����õ�����ͼ�ı���·����
		return preg_replace("/(_\d+_\d+)*\.\w+$/i","_{$width}_{$height}.jpg",$local);
	}
	
	/**
	 * ��url��ʽ����ʾ������ ������,ϵͳ����[����]��url
	 *
	 * @param  string  	$url    	����ǰ��$url
	 * @param  bool  	$NeedBind   �Ƿ���Ҫ�����������󶨣�ȷ������Ҫ����������url����ʡ���������󶨲���
	 * @return string 	$url    	������$url
	 */
	public static function view_url($url,$NeedBind = TRUE){
		global $cms_abs;
		if(empty($url)) return $url;
		if(strpos($url,$cms_abs) === 0) $url = str_replace($cms_abs,'',$url);
		if(strpos($url,'://') === false){
			if($NeedBind) $url = self::domain_bind($url);
			$url = self::remove_index($url);
			if(strpos($url,'://') === false) $url = $cms_abs.$url;
		}
		return $url;
	}
	
	/**
	 * �Ծ�̬�ļ���ʽ�ִ��������ϴ��룬��������ʽ��������
	 * ��ҳ��page=1ʱ�����page���ִ�������
	 * @param  string   $u		����ľ�̬��ʽ�ִ�  
	 * @param array     $s  	�������������
	 * @return array    $u  	���ش�����ľ�̬��ʽ�ִ�
	 */
	public static function m_parseurl($u,$s = array()){
		if(!$s || !$u) return $u;
		$u = str_replace(' ','',$u);
		foreach($s as $k => $v){
			if(($k == 'page') && ($v == 1)){
				//�������Ʋ���page=1���м�����������
				$u = preg_replace("/([&\/]page[-=]\{\\\$page\}*|page[-=]\{\\\$page\}[&\/]*)/",'',$u);
				preg_match("/(^|\/)[\d_-]*(?:[a-z][\d_-]*)+\{\\\$page\}\./i",$u) && $v = '';
			}
			$u = str_replace('{$'.$k.'}',$v,$u);
		}
		$u = preg_replace(array('/(?:_[_-]*)+/','/(?:-[_-]*)+/','/(?:[_-]*\/[\/_-]*)+/','/[\/_-]*\.+[\/_-]*/'),array('_','-','/','.'),$u);
		return str_replace(':/','://',$u);
	}

	/**
	 * ���ݽڵ��ִ������½ڵ��������
	 *
	 * @param  string  $cnstr  �ڵ��ִ�
	 * @return array   &$cnode �ڵ�������Ϣ
	 * @return NULL    ---     ����$cnode�������
	 */
	public static function view_cnurl($cnstr,&$cnode){
		global $enablestatic,$cn_max_addno,$mobiledir;
		if(empty($cnode)){
			for($i = 0;$i <= $cn_max_addno;$i ++) $cnode['indexurl'.($i ? $i : '')] = '#';
		}elseif(!empty($cnode['appurl'])){
			for($i = 0;$i <= @$cnode['addnum'];$i ++) $cnode['indexurl'.($i ? $i : '')] = $cnode['appurl'];
		}else{
            $get = cls_env::_GET('is_weixin');
			for($i = 0;$i <= @$cnode['addnum'];$i ++){
				if(!empty($cnode['nodemode'])){//�ֻ��ڵ�
                    $key = 'indexurl'.($i ? $i : '');
					$cnode[$key] = self::view_url("$mobiledir/index.php?$cnstr".($i ? "&addno=$i" : ''));
                    if (!empty($get['is_weixin']))
                    {
                        $cnode[$key] .= "&is_weixin=1";
                    }                    
				}elseif(empty($cnode['cfgs'][$i]['static']) ? $enablestatic : 0){
					$cnode['indexurl'.($i ? $i : '')] = self::view_url(self::m_parseurl(cls_node::cn_format($cnstr,$i,$cnode),array('page' => 1)));
				}else $cnode['indexurl'.($i ? $i : '')] = self::view_url(self::en_virtual("index.php?$cnstr".($i ? "&addno=$i" : '')));
			}
		}
	}

	/**
	 * ��Ա�ռ���Ŀҳurl
	 *
	 * @param  array	$info		ָ����Ա��������Ϣ����
	 * @param  array	$params		ָ���ĸ������ԣ���mcaid(�ռ���Ŀ)��addno(����ҳ)��ucid(�ռ���Ŀ�ڵĸ��˷���)
	 * @param  bool		$dforce		ǿ�Ʒ��ض�̬��ʽ
	 * @return string      			���ػ�Ա�ռ���Ŀҳurl
	 */
	public static function view_mspcnurl($info,$params = array(),$dforce = false){
		return cls_Mspace::IndexUrl($info,$params,$dforce);
	}
	
	/**
	 * ��ԱƵ���ڵ�url
	 * @param  string	$cnstr		��Ա�ڵ�������ִ�
	 * @param  array	$cnode		��Ա�ڵ����������
	 * @return      				�����أ���$cnode�������и���ҳ��url
	 */
	public static function view_mcnurl($cnstr,&$cnode){
		global $enablestatic,$mcn_max_addno,$memberurl;
		if(empty($cnode)) return;
		if(!empty($cnode['appurl'])){
			for($i = 0;$i <= $mcn_max_addno;$i ++) $cnode['mcnurl'.($i ? $i : '')] = $cnode['appurl'];
		}else{
			for($i = 0;$i <= $mcn_max_addno;$i ++){
				if(empty($cnode['cfgs'][$i]['static']) ? $enablestatic : 0){
					$cnode['mcnurl'.($i ? $i : '')] = $i <= @$cnode['addnum'] ? self::view_url($memberurl.self::m_parseurl(empty($cnode['cfgs'][$i]['url']) ? '{$cndir}/index{$addno}_{$page}.html' : $cnode['cfgs'][$i]['url'],array('cndir' => mcn_dir($cnstr),'addno' => $i ? $i : '','page' => 1,))) : '#';
				}else $cnode['mcnurl'.($i ? $i : '')] = $i <= @$cnode['addnum'] ? $memberurl.self::en_virtual("index.php?$cnstr".($i ? "&addno=$i" : '')) : '#';
			}
		}
	}	
	
	/**
	 * ��Ե�������url�õ����浽���ݿ��еĸ�ʽ
	 *
	 * @param  string  $url     ��������$url
	 * @return string  $url     ������$url
	 */
	public static function save_atmurl($url){
		global $cms_abs,$ftp_url,$ftp_enabled;
		$url = preg_replace(u_regcode($cms_abs),'',$url);
		if($ftp_enabled && $ftp_url) $url = preg_replace(u_regcode($ftp_url),'',$url);
		return $url;
	}
	
	/**
	 * ��Ծ���url�õ����浽���ݿ��еĸ�ʽ�������url����
	 *
	 * @param  string  $url     ��������$url
	 * @return string  $url     ������$url
	 */
	public static function save_url($url){
		global $cms_abs;
		$url = preg_replace(u_regcode($cms_abs),'',$url);
		return $url;
	}
	
	/**
	 * �ο�cls_url::tag2atm(�� tag2atm ���� ??? )
	 *
	 * @param  string  $url     url
	 * @return string  $url     ������url
	 */
	public static function view_atmurl($url=''){
		if(!$url) return '';
		return self::tag2atm($url);
	}
	
	/**
	 * ��ȡ��������ҳurl
	 *
	 * @param  int     $id      ����id
	 * @param  $url    $archive url
	 * @return string  $url     ������url
	 */
	public static function view_farcurl($id,$url=''){
		if(!$url) $url = self::en_virtual("info.php?aid=$id");
		return self::view_url($url);
	}
	
	/**
	 * �������⾲̬��ַ
	 *
	 * @param  string   $str      ԭʼurl  
	 * @param  bool     $novu     ��ֹ���⾲̬
	 * @return string   $str      ���⾲̬��ַ
	 */
	public static function en_virtual($str = '',$novu=0){
		$virtualurl = cls_env::mconfig('virtualurl');
		$rewritephp = cls_env::mconfig('rewritephp');
		if(empty($str) || empty($virtualurl) || $novu) return $str;
		$str = str_replace('=','-',str_replace('&','/',$str));
		$str .= '.html';
		if(!empty($rewritephp)) $str = str_replace('.php?',$rewritephp,$str);
		return $str;
	}
	
    /**
     * ����MVCӦ��URL
     *
     * @param  string $route  ·�ɹ���
     * @param  array  $params URL���ò���
     * @return string         ���ظ��ݲ��������õ�URL
     *
     * @since  1.0
     */
    public static function create( $route, $params = array() )
    {
        if ( is_string($route) )
        {
            $route = explode('/', $route);
            $route = array($route[0] => $route[1]);
        }
        else
        {
            $route = (array) $route;
        }
        
        $params = array_merge($route, $params);
        
        $route = _08_CMS_ABS . _08_Http_Request::uri2MVC($params);
        return $route;
    }

    /**
     * ��URL����urlencode���루��֧�����飩
     * 
     * @param  mixed $urls              Ҫ�����URL
     * @param  bool  $onlyEncodeChinese ֻ��������
     * @return mixed                    �����Ѿ������URL
     * 
     * @since  nv50
     */
	public static function encode( $urls, $onlyEncodeChinese = true )
    {
        if (!$onlyEncodeChinese)
        {
            return cls_Array::map('rawurlencode', $urls);
        }
        
        if (is_array($urls))
        {
            foreach ($urls as &$url)
            {
                if (is_array($url))
                {
                    $url = self::encode($url);
                }
                else
                {
                    $url = self::getEncodeChinese($url);
                }
            }
        }
        else
        {
            $urls = self::getEncodeChinese((string) $urls);
        }
        
        return $urls;
    } 
    
    /**
     * ��ȡֻ�������ĵ��ַ�����Ŀǰֻ֧��UTF-8������ַ�����
     * 
     * @param  string $string Ҫ������ַ���
     * @return string         ����ֻ�����ľ����˱������ַ���
     **/
    public static function getEncodeChinese($string)
    {
        if (preg_match_all('/[\x7f-\xff]+/', (string) $string, $chinse))
        {
            $array = array();
            foreach ($chinse[0] as &$_chinse)
            {
                $array[] = urlencode($_chinse);
            }
            
            $string = str_replace($chinse[0], $array, $string);
        }
        
        return $string;
    }

    /**
     * ��URL����{@see self::encode}�������URL����
     * 
     * @param  mixed $urls Ҫ�����URL
     * @return mixed       �����Ѿ������URL
     * @since  nv50
     */
	public static function decode( $urls )
    {
        return cls_Array::map('rawurldecode', $urls);
    } 
# ----------------------------------------------------------------------------------	
	# ��ȡ�ĵ�����ҳ��url����ʱ�����Լ��ݾɰ汾
	public static function view_arcurl(&$archive,$addno = 0){
		return cls_ArcMain::Url($archive,$addno);
	}

	
}
