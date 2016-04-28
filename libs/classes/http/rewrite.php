<?php
/**
 * Rewrite������
 * Ŀǰֻ֧�����ַ����������IIS��APACHE��NGINX
 *
 * @author    Wilson <Wilsonnet@163.com>
 * @copyright Copyright (C) 2008 - 2013 08CMS, Inc. All rights reserved.
 */

class _08_Rewrite
{    
    protected $_virtualfile;
    
    /**
     * �Ƿ���α��̬
     **/
    protected $_virtualurl = false;
    
    private $mconfigs = array();
        
    /**
     * ��ȡҪʹ��α��̬������ļ�
     */
    public static function getVirtualFiles()
    {
        $virtualfiles = array();
        # ����Ҫʹ��α��̬����ڽű�����
        $files = array('/mspace/archive.php', '/mspace/index.php', '/wap/login.php', '/member/index.php',
                       '/index.php', '/archive.php', '/info.php');
        foreach($files as $file)
        {
            $virtualfiles[md5($file)] = $file;
        }
        
        return $virtualfiles;
    }
    
    /**
     * ����α��̬����
     */
    public function create( $server = '' )
    {
        $system = _08_SystemInfo::getInstance();
        $sysinfo = & $system->getInfo();
        $rule = $sysinfo['copyright'] . <<<EOT
        
##
# ʹ�ø��ļ�ʱ�����Ķ�������
# Ҫʹ�ø��ļ������ڷ����������￪��Rewriteģ�飬�������÷�����鿴��http://www.08cms.com/html/tech/1157-1.html
##
EOT;
        $file = '.htaccess';
        
        # �Զ���ȡ
        if ( empty($server) )
        {            
            # Ԥ��ʹ���˷������������ע���÷������������δ�����÷�����Ϣ���п��ܻ��ȡ���ɹ���
            $header = $this->getProxyHeader();
            if ( isset($header['Server']) )
            {
                $sysinfo['server'] = $header['Server'];
            }
        }
        else # �ֶ�ָ�� 
        {
            $sysinfo['server'] = $server;
        }
        
        if ( false !== stripos($sysinfo['server'], 'IIS') )
        {
            @list(, $version) = explode('_', $sysinfo['server']);
            $version < 3 && $file = 'httpd.ini';
            $rule .= $this->getIISRule($sysinfo['server']);
        }
        else if ( false !== stripos($sysinfo['server'], 'APACHE') )
        {
        	$rule .= $this->getApacheRule();
        }
        else
        {
        	$rule .= $this->getNginxRule();
        }
        
        if ($this->_virtualurl)
        {
        	_08_FilesystemFile::getInstance()->_fwrite(
                array('file' => $file, 'string' => $rule, 'close' => true)
            );
        }
        else
        {
        	_08_FilesystemFile::getInstance()->delFile($file);
        }
        
    }
    
    /**
     * ��ȡIIS����
     */
    public function getIISRule( $server_info )
    {
        @list(, $version) = explode('_', $server_info);
        if ( empty($version) )
        {
            $version = 3;
        }
        # ISAPI Rewrite 3 �����ϵĹ��������APACHE
        if ( $version > 2 )
        {
            $rule = $this->getApacheRule();
            $rule = str_replace(
                array('RewriteEngine'), 
                array("# ISAPI Rewrite3+\r\nRewriteEngine"),
                $rule
            );
        }
        else
        {
            $rule = <<<EOT

[ISAPI_Rewrite]
# Version 2.x-
# 3600 = 1 hour 
# CacheClockRate 3600
RepeatLimit 32

EOT;
            foreach( (array) $this->_virtualfile as $file)
            {
                $key = md5($file);
                $RewriteRule = str_replace('.php', '', $file);
                # ������վ����һ��Rewrite�ļ�ʱҪʹ��RewriteCond������Ӱ��������վ
                $rule .= "RewriteCond Host: ^{$_SERVER['HTTP_HOST']}\$\r\n";
                $rule .= 'RewriteRule ^' . $RewriteRule . $this->mconfigs['rewritephp'] . '(.+)$ ' . $file . "?\$1 \r\n";
            }
        
          #  $rule .= "RewriteRule ^/_/(.*)\$ ?/\$1 \r\n";
        }
        
        return $rule;
    }
    
    /**
     * ��ȡAPACHE����
     */
    public function getApacheRule()
    {
        $rule = <<<EOT

RewriteEngine On\r\n
RewriteBase {$this->mconfigs['cmsurl']}\r\n
EOT;
        foreach( (array) $this->_virtualfile as $file)
        {
            $file = substr($file, 1);
            $key = md5($file);
            $RewriteRule = str_replace('.php', '', $file);
            $rule .= 'RewriteRule ^' . $RewriteRule . $this->mconfigs['rewritephp'] . '(.+)$ ' . $file . "?\$1 [L]\r\n";
        }
        
        return $rule;
    }
    
    /**
     * ��ȡNGINX����
     */
    public function getNginxRule()
    {
        $rule = "\r\nrewrite /_/(.*)\$ /?/\$1 last;\r\n";
        $RewriteCond = array();
        foreach( (array) $this->_virtualfile as $file)
        {
            $key = md5($file);
            $RewriteCond[$key] = str_replace('.php', '', $file);
            $rule .= 'rewrite ' . $RewriteCond[$key] . $this->mconfigs['rewritephp'] . '(.+)$ ' . $file . "?\$1 last;\r\n";
        }
        
        return $rule;
    }
    
    /**
     * ��ȡ�����HTTPͷ
     */
    public function getProxyHeader()
    {
        _08_Loader::import('include:http.cls');
        $check_temp_file = 'check_server_temp.html';
        $file = _08_FilesystemFile::getInstance();
        $file->_fwrite( array('file' => $check_temp_file, 'close' => true) );
        list($server_protocol, $verison) = explode('/', @$_SERVER['SERVER_PROTOCOL']);
        if ( isset($_SERVER['HTTP_HOST']) )
        {
            $host = strtolower($server_protocol . '://' . $_SERVER['HTTP_HOST']) . '/';
        }
        else
        {
        	$host = @$this->mconfigs['cms_abs'];
        }
        # ��ע���������кܴ����ʱʱ��ʹ���ֶ�ָ������
        $header = http::getHeaders($host . $check_temp_file, 1);
        $file->delFile(M_ROOT . $check_temp_file, 'html');
        return $header;
    }
    
    public function __construct( $rewritephp = '', $virtualurl = false )
    {
        $this->mconfigs = (array) cls_cache::Read('mconfigs');
        empty($this->mconfigs) && cls_message::show('ϵͳ������Ϣ����');
        $this->_virtualfile = self::getVirtualFiles();
        $this->_virtualurl = (bool) $virtualurl;
        if ( empty($rewritephp) )
        {
            if ( isset($this->mconfigs['rewritephp']) )
            {
                $this->mconfigs['rewritephp'] = preg_quote($this->mconfigs['rewritephp']);
            }
        }
        else
        {
        	$this->mconfigs['rewritephp'] = preg_quote($rewritephp);
        }
    }
}