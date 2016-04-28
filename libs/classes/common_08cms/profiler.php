<?php
/**
 * ���ܷ���������
 *
 * @author    Wilson
 * @copyright Copyright (C) 2012, 08CMS Inc. All rights reserved.
 */
defined('M_COM') || exit('No Permission');
class _08_Profiler
{
    /**
     * ��ǰ��������
     *
     * @var    array
     * @static
     * @since  1.0
     */
    private static $instance = array();

    /**
     * ��ȡ����ʼִ��ʱ��
     *
     * @var   int
     * @since 1.0
     */
    protected $_start_time = 0;

    /**
     * �������Ҫʹ�õ�ǰ׺
     *
     * @var   string
     * @since 1.0
     */
    protected $_prefix = '';

    /**
     * ��ȡ��Ϣ��������
     *
     * @var   array
     * @since 1.0
     */
    protected $_buffer = array();

    /**
     * �洢��һ���ڴ�
     *
     * @var   float
     * @since 1.0
     */
    protected $_previous_mem = 0.0;

    /**
     * �洢��һ��ʱ��
     *
     * @var   float
     * @since 1.0
     */
    protected $_previous_time = 0.0;

    /**
     * �жϷ�����ϵͳ�Ƿ�ΪWIN
     *
     * @var   bool
     * @since 1.0
     */
    protected $_iswin = true;

    public function __construct($prefix = '') 
    {
		$this->_start_time = $this->getMicrotime();
		$this->_prefix = $prefix;
		$this->_buffer = array();
		$this->_iswin = (strtoupper(substr(PHP_OS, 0, 3)) == 'WIN');
	}

    /**
     * ��ʽ��������ܷ�����Ϣ
     *
     * @param  string $label ��Ϣ��ǩ
     * @return string $mark  ���ط�����Ϣ
     *
     * @since  1.0
     */
	public function mark($label = '') 
    {
		$current = self::getMicrotime() - $this->_start_time;
        $current_mem = $this->getMemory();
        if(is_numeric($current_mem)) {
			// byte ת MB
			$current_mem = $current_mem / 1048576;
    		$mark = sprintf(
    			'<code>%s %.3f seconds (+%.3f); %0.2f MB (%s%0.3f)</code>',
    			$this->_prefix,
    			$current,
    			$current - $this->_previous_time,
    			$current_mem,
    			($current_mem > $this->_previous_mem) ? '+' : '',
                $current_mem - $this->_previous_mem
    		);
        } else if(is_string($current_mem) && $current_mem != '') {
    		$mark = sprintf(
    			'<code>%s %.3f seconds (+%.3f); %s</code>',
    			$this->_prefix,
    			$current,
    			$current - $this->_previous_time,
    			$current_mem
    		);
        } else {
            $mark = sprintf(
                '<code>%s %.3f seconds (+%.3f)</code>',
                $this->_prefix, $current,
                $current - $this->_previous_time
            );
        }

		$this->_previous_time = $current;
		$this->_previous_mem = $current_mem;
		$this->_buffer[] = $mark;

		return $mark . (empty($label) ? '' : " - $label");
	}

    /**
     * ��ȡ��ǰ������ʹ�õ��ڴ���Ϣ
     *
     * @return int ����ʹ�õ��ڴ���Ϣ
     *
     * @since  1.0
     */
    public function getMemory() 
    {
		if (function_exists('memory_get_usage')) 
        {
            // �ú������ص�ֻ��PHP�ڴ�״�������ص�λ��bytes  170672
			return memory_get_usage();
		} 
        else 
        {
			$output = array();
            // ��ȡ��ǰ����PID
			$pid = getmypid();

            /**
             * ����������������exec�����п��ܱ����Σ����Կ��ܷ��ز��ɹ�
			 * ���������WINϵͳ�����ִ�е����ⲽ�򷵻�string�����ҷ��ص��Ƿ������������APACHE��+ PHP�ڴ�״��
             * ����ʹ�õ���CGI���൥���������е�PHP
             */
			if ($this->_iswin) 
            {
				@exec('tasklist /FI "PID eq ' . $pid . '" /FO LIST', $output);
				if (!isset($output[5])) 
                {
					$output[5] = null;
				} 
                else 
                {
				    $split = explode(':', $output[5]);
                    // ��ʽ��20,916 K
                    $output[5] = $split[1];
				}
				return @$output[5];
			} 
            else 
            { // ����ϵͳ
				@exec("ps -o rss -p $pid", $output);
				return @$output[1] * 1024;
			}
		}
	}

    /**
     * ��ȡ����ǰִ��ʱ��
     *
     * @static
     * @return float ���ص�ǰ������ʱ��ֵ
     *
     * @since  1.0
     */
    public static function getMicrotime() 
    {
		list ($usec, $sec) = explode(' ', microtime());

		return ((float) $usec + (float) $sec);
	}

    /**
     * ��ȡ��������ʱ��
     *
     * @return float ���س�������ʱ��
     * @since  1.0
     */
    public function getEndTime() 
    {
        return self::getMicrotime() - $this->_start_time;
    }

    /**
     * ��ȡ���浽�����������ܷ�����Ϣ
     *
     * @return array ��������Ϣ����
     *
     * @since  1.0
     */
	public function getBuffer() 
    {
		return $this->_buffer;
	}

    /**
     * ����һ�����Ի�����Ϣ
     * (PHP 4 >= 4.3.0, PHP 5)
     * {@link http://docs.php.net/manual/zh/function.debug-backtrace.php}
     *
     * @param  string $in_args      �ò�����Ϊ��ʱ���ظ�ֵ����$in_args����Ϣ����
     * @param  bool   $debug_enable �Ƿ������ɵ�����Ϣ
     * @return array  $backtrace    ����һ���洢������Ϣ������
     * @since  1.0
     */
    public function getDebugBacktrace($in_args = '', $debug_enable = true)
    {
        if ( !$debug_enable ) return false;
        
        if ( function_exists('debug_backtrace') )
        {
            $backtrace = debug_backtrace();
		    $index = count($backtrace) - 1;
			if (isset($backtrace[$index]['file']))
			{
				self::replaceBacktracePath($backtrace[$index]['file']);
			}
            
			if ( !isset($backtrace[$index]['line']) )
			{
				$backtrace[$index]['line'] = '';
			}
            
			if ( isset($backtrace[$index]['args']) && is_array($backtrace[$index]['args']) )
			{
			    $args_index = count($backtrace[$index]['args']) - 1;
				self::replaceBacktracePath($backtrace[$index]['args'][$args_index]);
                $backtrace[$index]['args'] = $backtrace[$index]['args'][$args_index];
			}
            
			if ( !isset($backtrace[$index]['class']) )
			{
				$backtrace[$index]['class'] = '';
			}
            
			if ( !isset($backtrace[$index]['function']) )
			{
				$backtrace[$index]['function'] = '';
			}
            
			if ( !isset($backtrace[$index]['type']) )
			{
				$backtrace[$index]['type'] = '';
			}
            
			if ( !isset($backtrace[$index]['object']) )
			{
				$backtrace[$index]['object'] = null;
			}
            
            return $backtrace[$index];
        }
        
        return self::debugPrintBacktrace();  
    }
    
    /**
     * ��ȡ�������һ�������Ĵ������Ϣ
     * (PHP 5 >= 5.2.0)
     * {@link http://docs.php.net/manual/zh/function.error-get-last.php}
     * 
     * @return array $error_get_last ����д������򷵻�������ɴ���Ĵ�����Ϣ�����򷵻�null
     */
    public static function getLastError()
    {
        $error_get_last = null;
        if ( function_exists('error_get_last') )
        {
            $error_get_last = error_get_last();
        }
        
        return $error_get_last;
    }
    
    /**
     * ��ӡһ������
     * 
     * @return string $debug_print_backtrace ����һ����ӡ�Ļ�����Ϣ
     */
    public static function debugPrintBacktrace()
    {
        $debug_print_backtrace = '';
        if ( function_exists('debug_print_backtrace') )
        {
            #ob_end_clean();
            ob_start();
            debug_print_backtrace();
            $debug_print_backtrace = ob_get_contents();
            ob_end_clean();
            self::replaceBacktracePath($debug_print_backtrace);
        }
        
        return $debug_print_backtrace;
    }
    
    /**
     * ��ȡһ������ص���Ϣ
     * 
     * @param  string $string        ����һ��������Ϣ��ԭ���
     * @return string $error_filestr ���ػص���Ϣ
     * 
     * @since  1.0
     */
    public static function getDebugBacktraceMessage( $string )
    {
        $error_filestr = '';
		$error_info = self::getDebugBacktrace($string);
		if( !empty($error_info) )
		{
			if ( is_string($error_info) || empty($error_info['file']) || empty($error_info['line']) )
			{
				$error_filestr = '<br />' . $error_info;
			}
			else
			{
                if ( empty($error_info['function']) )
                {                    
                	$error_filestr = ($error_info['file'] . ' : ' . $error_info['line']);
                }
                else
                {
				    $error_filestr = ($error_info['function'] . '(' . $error_info['args'] . ') called at [' . $error_info['file'] . ' : ' . $error_info['line'] . ']');
                }
			}
		}
        
        return $error_filestr;
    }
    
    /**
     * �滻·������������¶·�����ⲿ
     * �ô��� / ����ʲô��������ã��Ȳ���Ŀ¼��¶���ⲿ��������վ��һ����֪���ĸ��ļ����еġ�
     */
    public static function replaceBacktracePath( &$path )
    {
        $path = (str_replace(array(M_ROOT, '\\'), array('/', '/'), $path));
    }

    /**
     * ��ȡ��ǰ��������
     *
     * @return array self::$instance ���ص�ǰ����
     *
     * @static
     * @since  1.0
     */
    public static function getInstance($prefix = '') 
    {
        if(empty(self::$instance[$prefix])) 
        {
            self::$instance[$prefix] = new self($prefix);
        }
        return self::$instance[$prefix];
    }
}