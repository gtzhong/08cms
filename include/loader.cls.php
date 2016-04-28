<?php
/**
 * �Զ�������
 *
 * ���Ӵ�������ⲿ�ļ�newһ����ʱ�Զ����ظ������ڵ��ļ�
 * ע��Ҫnew�������ڵ��ļ�������self::registerPrefix()����ע���Ŀ¼��Χ�ڣ����Ҹ�����������ע���Ŀ¼ǰ׺ǰ����������ͬ��
 *     ��������׺�������ļ�����ͬ������Ĭ��֧�ֵ�ǰĿ¼�������֧������Ŀ¼ֻҪ�ڱ����setup���������ӵ���
 *     self::registerPrefix ����Ҫ֧�ֵ�Ŀ¼��ǰ׺��Ϊ�������ݼ��ɡ�
 * �磺����self::registerPrefixע�����self::registerPrefix('_08cms_', dirname(__FILE__));  ������ǰ׺ǰ��λ����_08 ,
 *     ��ǰ·���µ������ļ�����������ǰ׺Ϊ��_08�Ĳ��Һ�׺���ļ�����ͬ�Ķ��ᱻ�Զ����أ����ļ�����Ŀ¼����ΪСд��
 * �磺1��$load = new _08cms_Loader(); ����Զ����ر�Ŀ¼�� loader.php�ļ�������Ϊ��_08cms_Loader
 *     2��$test = new _08_test();      ����Զ����ر�Ŀ¼�� test.php�ļ�������Ϊ��_08_test
 *     3��$test = new _08_test_file(); ����ж���»����������һ���ַ���Ϊ�ļ������ڻ��Զ����ر�Ŀ¼�� file.php �ļ���
 *                                     ����Ϊ��_08_test_file
 *
 * @package   08CMS.Platform
 * @author    Wilson
 * @copyright Copyright (C) 2008 - 2012 08CMS, Inc. All rights reserved.
 */
defined('M_COM') || die('Access forbidden!');
defined('DS') || define('DS', DIRECTORY_SEPARATOR);
class _08_Loader
{
    /**
     * ģ��ǰ׺
     * 
     * @var   string
     * @since nv50
     */
    const MODEL_PREFIX = '_08_M_';
    
    /**
     * ������ǰ׺
     * 
     * @var   string
     * @since nv50
     */
    const CONTROLLER_PREFIX = '_08_C_';
    
    // �����ļ��б�
    protected static $_files = array();

    // �Զ����ظ�����Ԫ��Ŀ¼�������ļ�
    protected static $_prefixes = array();
	
    // ��������������
    protected static $_Loader = null;
	
    // �Զ����ػ������ļ�map
    protected static $_maplist = array();
	
    // �����ӵ�map���
    protected static $_addflag = 0;

    /**
     * ��ʼ�����棬����ʹ��m_excache, �ٴ�ʹ���ļ����棻
	 * ����Ŀ¼��û�еĻ�,�������ɣ�
	 * ������map�����������и��£�
     */
    function __construct(){
		
		$m_excache = cls_excache::OneInstance();
		$mapfixs = _08_SYSCACHE_PATH.'sysparams.cac.php'; //�Զ����ص�ǰ׺��·�����
		$mappath = _08_USERCACHE_PATH.'autoload_pathmap.php';
		$mapfile = _08_USERCACHE_PATH.'autoload_filemap.php';
		//��ʼ������������·��map
		$modflag = @filemtime($mappath)>filemtime($mapfixs); //�޸ı��
		if($modflag && $m_excache->enable && $re = $m_excache->get(md5('autoload_pathmap'))){
			 self::$_prefixes = $re;
		}elseif($modflag && @include($mappath)){ 
			 self::$_prefixes = $autoload_pathmap;
		}else{ 
			// ���δ����ϵͳ��������ϵͳ������ʽ����ʱ�Ͱ�ϵͳĬ�ϵ�ǰ׺����
			require_once $mapfixs;
			if(empty($sysparams['autoload']) || !is_array($sysparams['autoload']))
			{
				foreach( array(
						dirname(__FILE__),
						M_ROOT . _08_ADMIN,
						M_ROOT . _08_ADMIN . DS . 'extends'
				) as $path ) {
					self::registerPrefix( '_08cms_', $path );
				}
				self::registerPrefix( 'cls_', dirname(__FILE__) );
			}
			else
			{
                self::autoLoadPathConfigs( $sysparams['autoload'] );
			}
			if($m_excache->enable) $m_excache->set(md5('autoload_pathmap'),self::$_prefixes); //m_excache����
			self::saveCacheMap('path',self::$_prefixes); //�ļ�����
		}
		//��ʼ�������������ļ�map
		if($m_excache->enable && $re = $m_excache->get(md5('class_filemap'))){
			 self::$_maplist = $re;
		}elseif(@include($mapfile)){ //is_file($mapfile) && empty($no)
			 self::$_maplist = $autoload_filemap;
		}
        self::autoLoadRegister();
	
	
	} //$this->setup();

    /**
     * ע���Զ������ļ��б�
     * @param string $class ����
     * @param string $path  �����ڵ��ļ�·��
     */
    public static function register($class, $path = '')
    {
        if(false !== stripos($path, M_ROOT . 'include'))
        {
            $class = '_08cms_' . ucfirst($class);
        }
        if(!empty($class) && is_file($path))
        {
            if(empty(self::$_files[$class]))
            {
                self::$_files[$class] = $path;
            }
        }
    }

    /**
     * �����ļ�
     * @param string $class ����
     * @return bool         ������سɹ�����TRUE�����򷵻�FALSE
     */
    private static function load($class)
    {
        if(class_exists($class)) return true;
       	if (isset(self::$_files[$class]))
        {
			include self::$_files[$class];
			return true;
		}

		return false;
    }

    /**
     * �Զ���������Ҫ���ļ�
     * @param  string $class Ҫ���ص�����
     * @return bool          ������سɹ�������ļ��Ѿ����ڷ���TRUE�����򷵻�FALSE
     */
    private static function _autoload ($class)
    {   
        foreach(self::$_prefixes as $prefix => $v)
        {
            if (0 === strpos($class, $prefix))
            {
				return self::_load($class, $v, $prefix);
			}
        }
    }

    /**
     * �����ļ�
     *
     * @param  string $class  Ҫ���ص�����
     * @param  array  $paths  �ļ�·��
     * @return bool           ���سɹ�����TRUE�����򷵻�FALSE
     */
    private static function _load($class, $paths, $prefix)
    {
		if (false !== strpos($class, '\\'))
        {
            $fileName = strtolower(str_replace('\\', DS, substr($class, strlen($prefix))) . '.php');
        }
        else
        {
        	$prefix2 = substr($prefix, 0, -1);
            preg_match("/^({$prefix2})([^_]*)_(\w+)/i", $class, $parts);
            isset($parts[2]) && ($prefix == '_08') && $parts[2] = substr($parts[2], 1);
            if(empty($parts[3]) || empty($parts[1])) return false;
            if(0 === strpos($prefix, '_08'))
            {
                $fileName = strtolower($parts[3]) . ($parts[2] ? '.' . strtolower($parts[2]) : '') . '.php';
            }
            else
            {
            	$fileName = strtolower($parts[3]) . ".{$parts[1]}.php";
            }
        }
		
		$class = strtolower($class); //���������ִ�Сд,�����±�����
		if(isset(self::$_maplist[$class])){ 
			include self::$_maplist[$class]; //��ͬ��,��ͬ·��,���ﻹΪ���
			if(class_exists($class) || interface_exists($class)) return true; 
			//���������,����������������
		} 
		
        foreach ($paths as $k => $v)
        {
            $path = ($v . DIRECTORY_SEPARATOR . $fileName);
            if (file_exists($path)) //�ļ���������,file_exists��is_file��ܶ�
            {
				include $path; 
				if(class_exists($class) || interface_exists($class)){
					self::$_addflag = 1; 
					self::$_maplist[$class] = $path; 
					return true; 
				}else{
					die("�Ҳ�����[$class] $path ��⣡");	
				}
			} 
        }
    }

    /**
     * ע��Ҫ�Զ����ص�·��
     * ע�⣺���������Ŀ¼���ļ�̫���п��ܻ�Ӱ�����ܣ������뾡����Ҫ�ڲ���Ҫ��Ŀ¼�����
     *
     * @param string $prefix    ����ǰ׺
     * @param string $path      Ҫ�Զ����ص�·��
     * @param bool   $traversal �Ƿ����ע���·���ڵ������ļ��У�falseΪ������
     */
    public static function registerPrefix($prefix, $path, $traversal = false)
    {
		if (!file_exists($path))
        {
			die('�Ҳ�����' . $path . ' ��·����');
		}

        if($traversal)
        {
				$iterator = new DirectoryIterator($path);
				foreach($iterator as $it_path)
				{
					if(@$it_path->isDir() && !$it_path->isDot())
					{
						//��Ա��ؿ�����ȥ�����Ŀ¼,����Ŀ¼,��Ҫ����ֱ���������?!
						if(in_array($it_path->getFileName(),array('.svn','_svn','.git','_git'))){ 
							continue;
						}
						self::registerPrefix($prefix, $it_path->getPathname(), $traversal);
					}
				}
			}
        self::$_prefixes[$prefix][] = $path;
    }

    /**
     * �����������ض���
	 * ע�⣺Ҫ��֤new�����󣻲���������һ������__construct()���Ż�ִ����������
     */
    public static function setup()
    {
		if(empty(self::$_Loader)){
			self::$_Loader = new self();
		}
    }
    
    /**
     * ���� �Զ�����·�����ļ�map (���¡�ϵͳ���û��桱ʱ����)
     * @param array $type �������
	 * @param array $cacarr ��������
     */
    public static function saveCacheMap($type,$cacarr)
    {
		$mapfile = "autoload_{$type}map";
		//�ļ����� $mapfile = _08_USERCACHE_PATH.'autoload_filemap.php';
		$cacstr = "<?php\n\$$mapfile = ".var_export($cacarr,TRUE)." ;";
		// 7 => 'E:\\webs\\08svn\\auto_v60\\extend_auto\\libs\\classes\\ajax',
		$mroot = str_replace(array("\\\\","\\"), array("/","/"), M_ROOT);
		$cacstr = str_replace(array("\\\\","\\"), array("/","/"), $cacstr);
		$cacstr = str_replace("'".$mroot, "M_ROOT.'", $cacstr);
		$cacstr = str_replace(array("//"), array("/"), $cacstr);
		$re = file_put_contents(_08_USERCACHE_PATH.$mapfile.'.php',$cacstr);
		if(false === $re){
			die("�����޷�д��"._08_USERCACHE_PATH.$mapfile);
        }
    }

    /**
     * �Զ�·������
     * 
     * @param array $configs Ҫ�����Զ�����·��������ǰ׺��·��
     * @since nv50
     */
    public static function autoLoadPathConfigs( array $configs )
    {
        foreach( $configs as $prefix => $paths )
		{
		    if ( is_array($paths) )
            {
                $path_arr = $paths;
            }
            else
            {
            	$path_arr = explode(',', $paths);
            }
			
			foreach( $path_arr as $path )
			{
				$split_path = explode('|', $path);
                $split_path[0] = trim($split_path[0]);
                # ���˸�Ŀ¼��ע�����Զ����ر����Ƿű������Ŀ¼�£�
				if( 0 === strpos($split_path[0], M_ROOT) )
				{
					$path = ( M_ROOT . str_replace(array(M_ROOT, '.'), array('', DS), $split_path[0]) );
				}
                else
                {
                    $path = (M_ROOT . str_replace('.', DS, $split_path[0]));
                }
				self::registerPrefix( $prefix, $path, (isset($split_path[1]) && $split_path[1] ? true : false) );
			}
		}
    }
    
    /**
     * ��ʼ�Զ�ע��
     * 
     * @since nv50
     */
    public static function autoLoadRegister()
    {
		spl_autoload_register(array('_08_Loader', 'load'));
		spl_autoload_register(array('_08_Loader', '_autoload')); 
    }
    

    
    /**
     * ����һ���ļ������ļ��Ӹ�Ŀ¼����
     * 
     * @param  string $file   Ҫ������ļ�
     * @param  array  $params Ҫɢ�еĲ���
     * @param  string $ext    �ļ���׺
     * 
     * @return mixed        ����ɹ����������ļ������ص���Ϣ��ʧ�ܷ���FALSE
     */
    public static function import( $file, $params = array(), $ext = '.php' )
    {
        $params = (array) $params;
        $file = (M_ROOT . str_replace(array(M_ROOT, ':'), array('', DS), $file) . $ext);
        if ( is_file($file) )
        {
            empty($params) || extract($params);
            return (include_once $file); 
        }
        
        return false;
    }
	
    /**
     * ��������, ��������������ӵ���map������»���
     * 
     * @return null 
     */
	 
	function __destruct(){
		if(!empty(self::$_addflag)){
			$m_excache = cls_excache::OneInstance();
			if($m_excache->enable) $m_excache->set(md5('autoload_filemap'),self::$_maplist); //m_excache����
			self::saveCacheMap('file',self::$_maplist); //�ļ�����
		}
	}
	
}