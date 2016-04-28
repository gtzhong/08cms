<?php
/**
 * @package    08CMS.Platform
 * @subpackage �ļ�ϵͳ(FileSystem)��Ŀ¼������
 *
 * @author     Wilson
 * @copyright  Copyright (C) 2008 - 2012 08CMS, Inc. All rights reserved.
 */
defined('DS') || define('DS', DIRECTORY_SEPARATOR);
class _08_FileSystemPath
{

    private $iterator = null;

    /**
     * Ҫ�����·��
     *
     * @var   string
     * @since 1.0
     */
    private $path;

    /**
     * ��Ŀ¼������ʱ�Ƿ񴴽�
     *
     * @var    bool
     * @static
     * @since  1.0
     */
    private static $create = false;

    /**
     * ���ò���Ȩ�޵�Ŀ¼
     *
     * @var    array
     * @static
     * @since  1.0
     */
    private static $error_path = array();

    /**
     * Ҫ�����·��ģʽ��Ĭ��Ϊ0777
     *
     * @var    string
     * @static
     * @since  1.0
     */
    private static $mode = 0777;

    /**
     * ���췽��
     *
     * @param string $path   Ҫ������·��
     * @param bool   $create ��Ŀ¼������ʱ�Ƿ񴴽���TRUEΪ������Ĭ��ΪFALSEΪ������
     * @param int    $mode   Ĭ�ϵ� mode �� 0777����ζ�������ܵķ���Ȩ��
     */
    public function __construct($path, $create = false, $mode = 0777)
    {
        if(self::checkPath($path, $create, $mode)) {
            $this->path = $path;
            self::$mode = $mode;
            self::$create = $create;
            $this->iterator = new RecursiveDirectoryIterator($path);
        } else {
            die('��������Ϊ�Ϸ�Ŀ¼�����ұ�����ڣ�');
        }
    }

    /**
     * ���Ŀ¼�Ƿ����
     *
     * @param  string $path   Ҫ����Ŀ¼
     * @param  bool   $create ��Ŀ¼������ʱ�Ƿ񴴽���TRUEΪ������Ĭ��ΪFALSEΪ������
     *
     * @return bool           Ŀ¼���ڻ򴴽��ɹ�ʱ����TRUE�����򷵻�FALSE
     * @since  1.0
     */
    public static function checkPath($path, $create = false, $mode = 0777)
    {
        $path = M_ROOT . str_replace(array(M_ROOT, '.'), array('', DIRECTORY_SEPARATOR), $path);
        if(is_dir($path)) {
            return true;
        } else {
            if($create) {
                return self::Create($path, $mode);
            } else {
                return false;
            }
        }
    }
    
    /**
     * ���Ŀ¼
     * 
     * @param string $dir Ҫ��յ�Ŀ¼
     * @since nv50
     */
    public static function clear( $dir )
    {        
    	$directory = dir($dir);
        $file = _08_FilesystemFile::getInstance();
    	while($entry = $directory->read()){
    		$filename = $dir.'/'.$entry;
    		if(is_file($filename)) 
            {
                $file->delFile($filename);
            }
    	}
    	$directory->close();
    	@touch($dir.'/index.htm');
    	@touch($dir.'/index.html');
    }
    
    /** 
     * ���ص��������õ�����Ŀ¼��
     * 
     * @param  callable $function   �ص����������ƣ�����ɿ���{@link http://docs.php.net/manual/zh/language.types.callable.php}
     * @param  string   $path       ҪӦ�ûص�������Ŀ¼
     * @param  bool     $traversal  �Ƿ����Ŀ¼��TRUEΪ������FALSEΪ������
     * @static array    $returnInfo
     * @return array    $returnInfo ����callback�����ķ���ֵ����
     * 
     * @since  nv50
     **/
    public static function map($function, $path, $traversal = true)
    {
        static $returnInfo = array();
        if ( is_dir($path) )
        {
            $iterator = new DirectoryIterator($path);
            foreach ( $iterator as $fileInfo )
            {
                if ( !$fileInfo->isDot() && (strrchr($fileInfo->getPathname(), '.') != '.svn') )
                {
                    if ( $fileInfo->isDir() && $traversal )
                    {
                        self::map($function, $fileInfo->getPathname(), $traversal);
                    }
                    else
                    {
                        $returnInfo[] = call_user_func($function, $fileInfo);
                    }
                }
            }
        }
        
        return $returnInfo;
    }

    /**
     * ����Ŀ¼
     *
     * ֱ�Ӵ���$path�������Ա��ⲿ�ɲ�ͨ�����챾���ֱ�ӵ��÷�������Ŀ¼
     *
     * @param  string $path   Ҫ������Ŀ¼
     * @param  int    $mode   Ĭ�ϵ� mode �� 0777����ζ�������ܵķ���Ȩ��
     * @return bool           Ŀ¼���ڻ򴴽��ɹ�ʱ����TRUE�����򷵻�FALSE
     * @link   http://docs.php.net/manual/zh/function.mkdir.php
     * @since  1.0
     */
    public static function Create($path, $mode = 0777)
    {
        if(is_dir($path)) {
            return true;
        } else {
            $path_str = '';
            $parts = preg_split('@[\\\|/].*?@', $path);
            if(is_array($parts)) {
                foreach ($parts as $path) {
                    $path_str .= $path . DS;
                    if(!is_dir($path_str)) {
                        @mkdir($path_str, $mode);
                    }
                }
            }
            if(is_dir($path_str)) {
                file_put_contents(
                    $path_str . 'index.html',
                    "<html>\n\t<body bgcolor=\"#FFFFFF\">\n\t</body>\n</html>"
                );
                file_put_contents(
                    $path_str . 'index.htm',
                    "<html>\n\t<body bgcolor=\"#FFFFFF\">\n\t</body>\n</html>"
                );
                return true;
            } else {
                return false;
            }
        }
    }

    /**
     * ���·��Ȩ���Ƿ���޸�
     *
     * @param  string $path Ҫ����·��
     * @return bool         ���·��Ȩ���Ƿ���޸ģ���������򷵻�TRUE�����򷵻�FALSE
     * @static
     * @since  1.0
     */
    public static function checkChmod($path)
    {
        $perms = fileperms($path);

        if($perms !== false)
        {
            // ���Ա��ԭ��Ȩ�ޣ�������޸�Ȩ���򷵻�TRUE�����û�ԭ��Ȩ��
            if(@chmod($path, $perms ^ 0001))
            {
                @chmod($path, $perms);
                return true;
            }
        }
        return false;
    }

    /**
     * ���ش��̻�Ŀ¼�Ŀ��ÿռ�
     *
     * @param  string $directory ����һ��������һ��Ŀ¼���ַ���
     * @return float             ���ؿ��ÿռ��С����λ���ֽ�����
     *
     * @since  1.0
     */
    public static function getDiskFreeSpace( $directory )
    {
        return disk_free_space($directory);
    }

    /**
     * ��ʽ���ֽڵ�λ
     *
     * @param  int    $bytes Ҫ��ʽ�����ֽ���
     * @return string        ���ظ�ʽ����ĵ�λ��Ϣ
     * @since  1.0
     */
    public static function byteConvert($bytes)
    {
        $s = array('B', 'KB', 'MB', 'GB', 'TB', 'PB');
        $e = floor(log($bytes)/log(1024));

        return sprintf('%.2f '.$s[$e], ($bytes/pow(1024, floor($e))));
    }

    /**
     * ѭ���ݹ�����Ŀ¼Ȩ��
     *
     * @return mixed ������óɹ����ؿ����飬����δ���챾��ʱ����FALSE�����򷵻����ò��ɹ���·������
     * @since  1.0
     */
    public function setPermissions()
    {
        if((null == $this->iterator) || (!self::checkChmod($this->path))) return false;
        foreach($this->iterator as $path)
        {
            if($path->isDir())
            {
                if(!self::checkChmod($path) || (false == @chmod($path, self::$mode)))
                {
                    self::$error_path[] = $path;
                }
                self::__construct($path, self::$create, self::$mode);
                $this->setPermissions();
            }
        }

        return self::$error_path;
    }
    
    /**
     * ����·�����ò�����·�������ڱ�ϵͳ��Ŀ¼����
     * 
     * @param  mixed $path Ҫ���˵�·��
     * 
     * @since  1.0
     */
    public static function filterPath( &$path )
    {
        if ( is_array($path) )
        {            
            foreach($path as &$_path)
            {
                self::filterPath($_path);
            }
        }
		else
		{
			$path = M_ROOT . str_replace(array(M_ROOT), array(''), $path);
		}
    }
    
    /**
     * ����Ŀ¼����
     * 
     * @param string $param Ҫ���˵�Ŀ¼����
     */
    public static function filterPathParam( $param )
    {
        $param = preg_replace('/[^\w\-]+/', '', $param);
    }
	
	/**
	 * ����·�����Ƽ�飬�����ļ�ϵͳ��ȫ
	 *
	 * @param  string  	$PathName 	�ļ�����
	 * @param  array  	$RegPattern	�����������ΪĬ�ϸ�ʽ
	 * @return string  	$str   		��������ԭ���ܹ���֤�򷵻ؿ�
	 */
	public static function CheckPathName($PathName,$RegPattern = ''){
		if(!$PathName) return '��ָ��·������';
		if(empty($RegPattern)) $RegPattern = "/[^\w\-]+/";
		if(preg_match($RegPattern,$PathName)) return '·������ֻ���������ĸ�����֡��»���(-_)����(.)���ַ�';
		return false;
	}
	
	
	
}