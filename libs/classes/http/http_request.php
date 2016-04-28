<?php
/**
 * HTTP���������
 *
 * @author    Wilson <Wilsonnet@163.com>
 * @copyright Copyright (C) 2008 - 2013 08CMS, Inc. All rights reserved.
 */

_08_Loader::import(_08_INCLUDE_PATH . 'http.cls');
class _08_Http_Request extends Http
{
    /**
     * �ض���
     * 
     * @param string $url        Ҫ��ת��Ŀ��URL
     * @param bool   $terminate  �Ƿ������ǰһ���Ƶı�ͷ�������ǿ�ƶ����ͬ���͵�ͷ������Ϊfalse
     * @param int    $statusCode HTTP״̬��
     */
    public function redirect( $url, $terminate = true, $statusCode = 302 )
    {
        if( 0 === strpos($url,'/') )
        {
            $url = _08_CMS_ABS . substr($url, 1);
        }
        
        header('Location: '.$url, $terminate, $statusCode);
    }
    
    /**
     * cURL������ȡ��Դ�������е������ڶ��̣߳���Ҫע��������߳��ǲ�ͬ��
     * 
     * @param  mixed $params  Ҫ��ȡ��Դ��������Ϣ����
     * @param  int   $timeOut ��ʱʱ��ֵ
     * @param  bool  $getInfo �Ƿ񷵻�������Դ�����Ϣ��TRUE ���أ�FALSE ������
     * @return array          ���ػ�ȡ������Դ����
     * 
     * @example $contents = _08_Http_Request::getResources('http://www.baidu.com/', 1);
     * 
                $contents = _08_Http_Request::getResources(array('http://www.baidu.com/', 'http://www.google.com.hk/'), 1);
                
                // �õ��÷����������Բ���Ӧ����urls�������
                // δ����'method'ʱĬ��Ϊ GET, postData ������GET��DELETE����ʱ��URL��Ҳ������POSTʱ������
                // timeOut��urls���룬���δ�������Զ�ʹ��getResources������������ֵ
                $contents = _08_Http_Request::getResources(
                    array( 'urls' => array('http://www.baidu.com/', 'http://www.google.com.hk/'), 
                           'timeOut' => array(5),
                           'method' => 'POST',
                           'postData' => array('test' => 'postdatas') )
                );
     *
     * @since 1.0
     */
    public static function getResources( $params, $timeOut = 5, $getInfo = false )
    {
        $responses = array();
        if ( $_params = self::getCurlParams($params) )
        {
            $queue = curl_multi_init();
            $map = array();
            
            foreach ($_params['urls'] as $key => $url)
            {
                if ( empty($url) ) { continue; }
                
                $method = (isset($_params['method'][$key]) ? strtoupper($_params['method'][$key]) : 'GET');
                $ch = curl_init();
                
                if( !empty($_params['postData'][$key]) )
                {
                    if ( $method == 'POST' )
                    {
                        curl_setopt($ch, CURLOPT_POST, 1);
                        curl_setopt($ch, CURLOPT_POSTFIELDS, $_params['postData'][$key]);                        
                    }
                    else if ( in_array($method, array('GET', 'DELETE')) )
                    {
                        $method == 'DELETE' && curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
                        $url .= (strpos($url, '?') ? '&' : '?') . 
                                (is_array($_params['postData'][$key]) ? http_build_query($_params['postData'][$key]) : $_params['postData'][$key]);
                    }
                }
     
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_TIMEOUT, isset($_params['timeOut'][$key]) ? (int)$_params['timeOut'][$key] : $timeOut);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_HEADER, 0);
                curl_setopt($ch, CURLOPT_NOSIGNAL, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
                curl_setopt($ch, CURLOPT_SSLVERSION, 1);
                curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 5.2; rv:27.0) Gecko/20100101 Firefox/27.0 FirePHP/0.7.4');
            
                # ��curl������Ự����ӵ�����curl���
                curl_multi_add_handle($queue, $ch);
                $map[(string) $ch] = md5($url);
            }
            
            do
            {
                while ( ($code = curl_multi_exec($queue, $active)) == CURLM_CALL_MULTI_PERFORM )
                {
                    continue;
                }
         
                if ($code != CURLM_OK) { break; }
         
                # �Ըո���ɵ����������ش�����Ϣ����
                while ($done = curl_multi_info_read($queue))
                {
                    if ( $getInfo )
                    {
                        # ��ȡһ��cURL������Դ�����Ϣ
                        $info = curl_getinfo($done['handle']);
                        $responses[$map[(string) $done['handle']]]['info'] = $info;
                    }
                    
                    # ����һ��������ǰ�Ự���һ�δ�����ַ���
                    $error = curl_error($done['handle']);
                    if ( !empty($error) )
                    {
                        $responses[$map[(string) $done['handle']]]['error'] = $error;
                    }
                    
                    $results = curl_multi_getcontent($done['handle']);
                    $responses[$map[(string) $done['handle']]]['results'] = $results;
         
                    # �Ƴ��ո���ɵľ����Դ
                    curl_multi_remove_handle($queue, $done['handle']);
                    curl_close($done['handle']);
                }
         
                // �ȴ�����cURL�������еĻ����
                if ($active > 0)
                {
                    curl_multi_select($queue, 0.5);
                }
            }
            while ($active);
         
            curl_multi_close($queue);
        }
        
        # ���ֻ��������Դʱ��ֻ������Դ����
        if ( count($responses) == 1 )
        {
            $responses = $responses[key($responses)]['results'];
        }
        
        return $responses;
    }
    
    /**
     * ��ȡCURL�������
     * 
     * @param mixed $params ����
     */
    private static function getCurlParams( $params )
    {
        if ( empty($params) )
        {
            return false;
        }
        
        $_params = array();
        if ( is_array($params) )
        {
            if ( isset($params['urls']) )
            {
                # һ�λ�ȡ�����ַ����Դ
                if ( is_array($params['urls']) )
                {
                    $_params = $params;
                }
                else
                {
                    # һ����ַ���ж������
                    foreach ( $params as $key => $param ) 
                    {
                        if ( is_array($param) )
                        {
                            $_params[$key][] = $param;
                        }
                        else
                        {
                        	$_params[$key] = (array) $param;
                        }
                    }
                }
            }
            else # ����һά����Ķ����ַ
            {
                $_params['urls'] = $params;
            }
        }
        else # �����ַ����͵ĵ�����ַ
        {
            $_params['urls'][] = (string) $params;
        }
        
        return $_params;
    }
    
    /**
     * ��URIת��MVC·�ɵ�URI
     * ע��ת�����URI�������û�� / ���Զ�����һ�� /
     * 
     * @param  mixed  $uri         Ҫת����ԭʼURI�������ַ��������鴫�ݣ�������һ�ļ���ֵ�����ǿ���������action��
     * @param  bool   $addFileName �Ƿ�Ҫ����·���ļ����ƣ�Ĭ��Ϊ���ӣ�����falseΪ������
     * @return string              ����ת�����MVC�ܹ�URI
     * 
     * @since  nv50
     */
    public static function uri2MVC ( $uri, $addFileName = true )
    {
        $split = '/';
        if ( is_array($uri) )
        {
            $uriString = '';
            foreach ( $uri as $key => $value ) 
            {
                if ( !empty($uriString) )
                {
                    $uriString .= $split;
                }
                
                $uriString .= ($key . $split . $value);
            }
            
            $uri = $uriString;
        }
        else
        {
            $uri = str_replace(array('&', '='), $split, (string) $uri);
        }        
        
        # ���Ҫ���·������ļ������
        if ( $addFileName && defined('_08_ROUTE_ENTRANCE') )
        {
            $uri = _08_ROUTE_ENTRANCE . (substr($uri, 0, 1) == $split ? substr($uri, 1) : $uri);
        }
        
        # ��URI������һ�� /
        if ( substr($uri, strlen($uri) - 1) == $split )
        {
            $uri = substr($uri, 0, -1);
        }
        
        return $uri;
    }
}