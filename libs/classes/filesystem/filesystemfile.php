<?php
/**
 * @package    08CMS.Platform
 * @subpackage �ļ�ϵͳ(FileSystem)���ļ�������
 *
 * @author     Wilson
 * @copyright  Copyright (C) 2008 - 2012 08CMS, Inc. All rights reserved.
 */

class _08_FilesystemFile
{
    /**
     * Ҫɾ�����ļ���չ�������ָ����ɾ������
     *
     * @var   private
     * @since 1.0
     */
    private $exts = array();

    /**
     * ��ǰ�������ļ�·��
     *
     * @var   private
     * @since 1.0
     */
    private $path_file = '';

    /**
     * ��ǰ�������ļ�ָ��
     *
     * @var   private
     * @since 1.0
     */
    private $fp = null;

    /**
     * �����Ѿ��򿪵��ļ�ָ��
     *
     * @var    private
     * @static 
     * @since  1.0
     */
    private static $fps = array();
    
    private static $instance = null;

    /**
     * ֻ�����ڵ�ǰϵͳ�н����ļ���������Ϊfalse�������ϵͳ����(��Ҫ��һ������??)
     *
     * @var   private
     * @since 1.0
     */
    private $OnlyInNowSystem = true;
	
    /**
     * ɾ��һ���ļ�
     *
     * @param  string $path_file ���ɾ���ɹ�����TRUE�����򷵻�FALSE
     * @param  string $exts      ����ɾ������չ��������������������չ
     *
     * @return bool              ɾ���ɹ�����TRUE�����򷵻�FALSE
     * @since  1.0
     */
    public function delFile($path_file, $exts = '')
    {
        if(empty($path_file)) return false;
        _08_FileSystemPath::filterPath($path_file);
        
        // ��������ɾ�����ļ���չ���������������
        if(!empty($exts)) 
        {
            if(is_array($exts)) {
                $this->exts = $exts;
            } else if(is_string($exts)) {
                $this->exts = explode(',', $exts);
            } else {
                die('�ڶ�����������');
            }
        }
        $this->path_file = $path_file;
        /* ������鿴��{@link http://docs.php.net/manual/zh/class.splfileinfo.php} */
        $fileinfo = self::getFileInfoObject($path_file);
        if(!$fileinfo->isFile()) return false;
        $ext = explode('.', $this->path_file);
        $ext = $ext[count($ext) - 1];
        
        if(empty($exts) || in_array($ext, $this->exts))
        {
            if(false !== strpos($this->path_file, '..')) 
            {
                return false;
            }
            
            return @unlink($this->path_file);
        }
    }
    
    /**
     * ��ȡ�ļ���Ϣ����
     * 
     * @param  string $file Ҫ��ȡ���ļ�
     * @return object       ���ػ�ȡ�����ļ���Ϣ����
     * 
     * @since  nv50
     */
    public static function getFileInfoObject( $file )
    {
        return new SplFileInfo($file);
    }

    /**
     * ���Ŀ¼�ļ�
     *
     * @param  string $path      Ҫ��յ�Ŀ¼
     * @param  string $exts      ����ɾ������չ��������������������չ
     * @param  bool   $traversal �Ƿ�Ը�Ŀ¼������TRUEΪ������FALSE������
     *
     * @return bool              ��ճɹ�����TRUE�����򷵻�FALSE
     * @since  1.0
     */
    public function cleanPathFile($path, $exts = '', $traversal = false)
    {
        if(empty($path)) return false;
        $path = M_ROOT . str_replace(array(M_ROOT, '.'), array('', DIRECTORY_SEPARATOR), $path);
        try {
            /* ������鿴��{@link http://docs.php.net/manual/zh/class.directoryiterator.php} */
            $iterator = new DirectoryIterator($path);
            foreach($iterator as $it)
            {
                if($traversal && $it->isDir() && !$it->isDot()) {
                    $this->cleanPathFile($it->getPathname(), $exts, true);
                }
                if($it->isFile()) {
                    $this->delFile($it->getPathname(), $exts);
                }
            }
            return true;
        } catch (RuntimeException $e) {
            die('ϵͳ������������·���Ƿ���ڣ�');
        }
    }

    /**
     * ��fopen��ʽ����һ���ļ�
     * {@link  http://docs.php.net/manual/zh/function.fopen.php}
     *
     * @param string   $filename         Ҫ�������ļ���
     * @param string   $mode             ָ����Ҫ�󵽸����ķ�������,Ϊ��ֲ�Կ��ǣ�
     *                                   ǿ�ҽ������� fopen() ���ļ�ʱ����ʹ�� 'b' ���
     * @param bool     $rewind           �Ƿ������ļ�ָ��
     * @param bool     $use_include_path �����Ҫ�� include_path ����Ѱ�ļ��Ļ���
     *                                   ���Խ��ò��� use_include_path ��Ϊ '1' �� TRUE
     * @param resource $context          �� PHP 5.0.0 �������� ��������(Context)��֧��
     * @since 1.1
     */
    public function _fopen($filename, $mode, $rewind = true, $use_include_path = false, $context = null)
    {
        $filename = M_ROOT . str_replace(array(M_ROOT, ':'), array('', DS), $filename);
        #�ļ������Ƿ�ʱ�˳�
        if($this->OnlyInNowSystem && !self::checkFile($filename) )
        {
            return false;
        }
        
        $file_name = md5($filename.$mode.$use_include_path);
        /** 
         * ����ļ�ָ���Ѿ����ڣ���ֱ�ӵ��ã������´��ļ���
         * ��ֹ��Щ�˴������ѭ�����ͬһ���ļ����磺
         * while($i < 10) { $file->_fopen('file.txt')....}
         */
        if ( empty(self::$fps[$file_name]) || !is_resource(self::$fps[$file_name])) {
            if ( is_resource($context) ) {
                $this->fp = @fopen($filename, $mode, $use_include_path, $context);
            } else {
                $this->fp = @fopen($filename, $mode, $use_include_path);
            }
            self::$fps[$file_name] = $this->fp;
        } else {
            $this->fp = self::$fps[$file_name];
        }
        
        $rewind && $this->_rewind();
        $this->path_file = $filename;
        return $this->fp;
    }

    /**
     * �Զ����Ʒ�ʽд�����ݵ��ļ�
     * {@link   http://docs.php.net/manual/zh/function.fwrite.php}
     *
     * @param  mixed $string Ҫд��������ַ�������������ʽ��������Զ�����$this->_fopen���ļ�
     * @param  int   $length �����ַ������ȣ���ָ����ָ��0�򳤶�Ϊ$string����
     * @param  bool  $fclose д���Ƿ�ر��ļ�ָ�룬TRUEΪ�رգ�FALSEΪ���ر�
     * 
     * @return bool           ����д����ַ��������ִ���ʱ�򷵻� FALSE
     * @since  1.0
     */
    public function _fwrite($string, $length = 0, $fclose = false)
    {
        if( !is_resource($this->fp) && empty($string['file']) )
        {
            return false;
        }
       
        # ���$string��������ʽ��������Զ�����$this->_fopen���ļ�
        if ( is_array($string) && !empty($string['file']) )
        {
            $string['file'] = M_ROOT . str_replace(array(M_ROOT, ':'), array('', DS), $string['file']);
            empty($string['mode']) && $string['mode'] = 'wb';
            
            $this->fp = $this->_fopen($string['file'], $string['mode']);
            
            if( isset($string['close']) && $string['close'] )
            {
                $fclose = (bool) $string['close'];
            }
            isset($string['length']) && ($length = $string['length']);
            if ( isset($string['string']) )
            {
                $string = $string['string'];
            }
            else # ����һ�����ļ�
            {
            	$string = '';
            }
        }
        
        if(0 == $length) $length = strlen($string);
        $fwrite = fwrite($this->fp, (string)$string, (int)$length);
        $fclose && $this->_fclose();
        return $fwrite;
    }

    /**
     * �Զ����Ʒ�ʽ��ȡĳ���ļ�����
     * {@link   http://docs.php.net/manual/zh/function.fread.php}
     *
     * @param  int  $length Ҫ��ȡȡ�����ݳ��ȣ���ָ�����ȡ�ļ���С
     * @param  bool $fclose �����Ƿ�ر��ļ�ָ�룬TRUEΪ�رգ�FALSEΪ���ر�
     * 
     * @return              ��������ȡ���ַ����� ������ʧ��ʱ���� FALSE��
     * @since  1.0
     */
    public function _fread($length = 0, $fclose = false)
    {
        if(!is_resource($this->fp) || !is_file($this->path_file)) 
        {
            return false;
        }
        if(0 == $length) $length = filesize($this->path_file);
        $fread = @fread($this->fp, (int)$length);
        $fclose && $this->_fclose();
        return $fread;
    }

    /**
     * ���ļ�ָ���ж�ȡһ��
     * {@link   http://docs.php.net/manual/zh/function.fgets.php}
     *
     * @param  int  $length Ҫ��ȡȡ�����ݳ��ȣ���ָ����Ϊ1024�ֽ�
     * @param  bool $fclose �����Ƿ�ر��ļ�ָ�룬TRUEΪ�رգ�FALSEΪ���ر�
     * 
     * @return              ��������ȡ���ַ����� ������ʧ��ʱ���� FALSE��
     * @since  nv50
     */
    public function _fgets($length = 1024, $fclose = false)
    {
        if(!is_resource($this->fp) || !is_file($this->path_file)) 
        {
            return false;
        }
        
        $fread = @fgets($this->fp, (int)$length);
        $fclose && $this->_fclose();
        return $fread;
    }

    /**
     * ����һ���ļ���ע�����ʹ���˸ú������ǵ������Ҫ����������������������
     *
     * @param  int  $operation  ����ģʽ��Ĭ��ȡ�ö�ռ������д��ĳ���)
     * @param  int  $wouldblock �������������Ļ���EWOULDBLOCK ����������£���
     *                          �ò����ᱻ����Ϊ TRUE����Windows �ϲ�֧�֣�
     * @return bool             �ɹ�ʱ���� TRUE�� ������ʧ��ʱ���� FALSE
     * @since  1.0
     */
    public function _flock($operation = LOCK_EX, $wouldblock = 0)
    {
        if(!is_resource($this->fp)) 
        {
            return false;
        }

        if(0 == $wouldblock) {
            return @flock($this->fp, $operation);
        } else {
            return @flock($this->fp, $operation, $wouldblock);
        }
    }
    
    /**
     * �����ļ�ָ���/д��λ��
     * 
     * @return int  �ļ�ָ���/д��λ��
     * 
     * @since  nv50
     */
    public function _ftell()
    {
        if ( is_resource($this->fp) )
        {
            return ftell($this->fp);
        }
        
        return false;        
    }    
    
    /**
     * ƫ���ļ�ָ��
     * {@link http://docs.php.net/manual/zh/function.fseek.php}
     * 
     * @param int $offset ƫ����
     * @param int $whence SEEK_SET - �趨λ�õ��� offset �ֽڡ�
     *                    SEEK_CUR - �趨λ��Ϊ��ǰλ�ü��� offset��
     *                    SEEK_END - �趨λ��Ϊ�ļ�β���� offset��
     * 
     * @todo �ù����д����
     **/
    public function _fseek($offset, $whence = SEEK_SET) 
    {
        if ( is_resource($this->fp) )
        {
            return fseek($this->fp, $offset, $whence);
        }
        return false;
    }
    
    /**
     * �����ļ�ָ���λ��
     * 
     * @return bool ������سɹ�����TRUE�����򷵻�FALSE
     * @since  1.0
     */
    public function _rewind()
    {
        if ( is_resource($this->fp) )
        {
            return rewind($this->fp);
        }
        return false;
    }
    
    /**
     * ��֤��ǰ�������ļ���ȷ��
     * 
     * @param  string $file Ҫ��֤���ļ�
     * @return bool         ��֤ͨ������TRUE�����򷵻�FALSE
     * 
     * @since  1.0
     */
    public static function checkFile( $file )
    {
        # ��ֹ������ϵͳ��Ŀ¼������Ŀ¼�����ļ�
        if ( (0 !== stripos($file, M_ROOT)) || (false !== strpos($file, '..')) )
        {
            return false;
        }
        
        return true;
    }
    /**
     * ���ļ������趨Ϊ�ɿ�ϵͳ���в�����ֻ����������;
     * 
     * @since  1.0
     */
    public function AllowOutOfSystem(){
		$this->OnlyInNowSystem = false;
    }
    
    /**
     * �رյ�ǰ�ļ�ָ��
     * 
     * @return ����ļ�δ�򿪻�ر�ʧ�ܷ���FALSE�����򷵻�TRUE  
     * @since  1.0
     */
    public function _fclose()
    {
        if(is_resource($this->fp)) 
        {
            return (bool) fclose($this->fp);
        }
        
        return false;
    }
    
    /**
     * _08_FilesystemFile::__construct()
     * 
     * @return
     */
    private function __construct() {}
    
    /**
     * _08_FilesystemFile::__clone()
     * 
     * @return
     */
    private function __clone(){}

	/**
	 * _08_FilesystemFile::getInstance()
	 * 
	 * @return
	 */
	public static function getInstance()
    {
		if(! (self::$instance instanceof self))
        {
			self::$instance = new self();
		}
        
		return self::$instance;
	}
    
    /**
     * ����ļ�ָ���Ѿ������Զ��ر��ļ�ָ��
     *
     * @since 1.0
     */
    public function __destruct()
    {
        if(is_resource($this->fp)) 
        {
            fclose($this->fp);
        }
    }
    
    public static function debug()
    {
        $params = func_get_args();
        file_put_contents(M_ROOT . 'debug.txt', var_export($params, true), FILE_APPEND);
    }
	
	/**
	 * �ļ������ƣ������ļ�ϵͳ��ȫ
	 *
	 * @param  string  	$FileName 	�ļ�����
	 * @param  array  	$AllowExtArray	����ʹ�õ���չ��
	 * @return string  	$str   		��������ԭ���ܹ���֤�򷵻ؿ�
	 */
	public static function CheckFileName($FileName,$AllowExtArray = array('htm','html')){
		if(!$FileName) return '��ָ���ļ���';
		if(preg_match("/[^a-z_A-Z0-9\.\-]+/",$FileName)) return '�ļ�����ֻ���������ĸ�����֡��»���(-_)����(.)���ַ�';
		$FileName = strtolower($FileName);
		if(!$AllowExtArray) $AllowExtArray = array('htm','html');
		if(!is_array($AllowExtArray)) $AllowExtArray = array($AllowExtArray);
		if(!($ext = mextension($FileName))) return '��ָ���ļ���չ��';
		if(!in_array($ext,$AllowExtArray)) return '��չ��ֻ����Ϊ��'.implode(',',$AllowExtArray);
		return false;
	}
	
    /**
     * ���˲��Ϸ����ļ��������������ļ���׺���ļ���������
     * 
     * @param  string $fileparam Ҫ���˵��ļ�����
     * @since  1.0
     */
    public static function filterFileParam( &$fileparam )
    {
        $fileparam = preg_replace('/[^\w\-\.]/', '', $fileparam);
    }
}