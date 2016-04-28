<?php
/**
 * AJAX������
 *
 * @author    Wilson <Wilsonnet@163.com>
 * @copyright Copyright (C) 2008 - 2014 08CMS, Inc. All rights reserved.
 */
defined('_08CMS_APP_EXEC') || exit('No Permission');
defined('_08CMS_AJAX_EXEC') || define('_08CMS_AJAX_EXEC', true);
class _08_C_Ajax_Controller extends _08_Controller_Base
{
    private $__className;
    
    protected $_datatype;
    
    protected $_charset = '';
    
    const _AJAX_PREFIXE_ = '_08_M_Ajax_';
    
    public function __call($name, $argc)
    {
        $contents = NULL;
        $this->__className = self::_AJAX_PREFIXE_ . ucfirst($name);
        
        # �����չ�಻�������жϺ�����
        if ( !class_exists($this->__className) )
        {
            $this->__className .= '_Base';
        }
        
        if ( class_exists($this->__className) )
        {
            # ������AJAX
            _08_Http_Request::clearCache();
            $instance = new $this->__className;
            $callback = (isset($this->_get['callback']) ? preg_replace('/[^\w\$]/', '', $this->_get['callback']) : '');
            # ����Ǻϲ����󣨼�һ��������AJAX���ݣ�ʱ
            if ( isset($this->_get['iteration']) && isset($this->_get['params']) && isset($this->_get['ajax_id']) )
            {
                $params = $this->_get['params'];
                cls_Array::array_stripslashes($params);
                $split = '<!--_08_' . strtoupper($this->_get['ajax_id']) . '_SPILT-->';
                $params = (array) _08_Documents_JSON::decode($params);
                foreach ( $params as $key => $param ) 
                {
                    if ( !is_null($contents) )
                    {
                        $contents .= $split;
                    }
                    
                    $instance->setter($this->_get['iteration'], $key);
                    $instance->setter('params', $param);
                    # ����ǿ�Ʒ����ַ�������                    
                    $contents .= (string) $instance->__toString();
                }
                
                $contents = $this->format( $contents, $callback );
            }
            else
            {
            	$contents .= $this->format( $instance->__toString(), $callback );
            }
        }
        
        @mexit($contents);
    }
    
    /**
     * ��ʽ������Ҫ�����ݸ�ʽ
     * 
     * @param  mexid  $string Ҫ��ʽ��������
     * @return string         �Ѿ���ʽ���������ַ���
     */
    public function format( $string, $callback = '' )
    {
        switch($this->_datatype)
        {
            case 'JS' : 
		        @header("Content-type:text/javascript;charset=" . cls_env::getBaseIncConfigs('mcharset'));
                if (isset($this->_get['varname']))
                {
                    $this->__className = preg_replace('/[^\w]/', '', $this->_get['varname']);
                }
                else
                {
                	if ( false !== strrpos($this->__className, '_Base') )
                    {
                        $this->__className = substr($this->__className, 0, strrpos($this->__className, '_Base'));
                    }
                    $this->__className = strtolower($this->__className);
                }
                
                $string = ($string === '' ? '""' : _08_Documents_JSON::encode($string, true));
                if (strtolower($string) === '"true"')
                {
                    $string = 'true';
                }
                if (strtolower($string) === '"false"')
                {
                    $string = 'false';
                }
                $string = "var {$this->__className} = $string;";
                if ( !empty($callback) )
                {
                    $string .= "$callback({$this->__className});";             
                }
                break;
            
            case 'CONTENT' :
            case 'XML' : 
                # Ϊ����ajax_info�����ļ��ݣ���ʱ����XML��ʽ�ķ��ַ�������
                if ( ($this->_datatype == 'CONTENT') && !is_string($string) )
                {
                    $string = _08_Documents_JSON::encode($string);
                }                
                cls_message::ajax_info($string, $this->_datatype);
                break;
            
            case 'JSON' :
                switch(gettype($string)){
                    case 'NULL':
                        $string = 'null';
                    break;
                    case 'boolean':
                        $string = ($string ? 'true' : 'false');
                    break;
                    case 'integer':
                    case 'double':
                    case 'float':
                        $string = $string;
                    break;
                    case 'string':
                    case 'array':
                        $this->_charset = cls_env::getBaseIncConfigs('mcharset');
                        @header("Content-type:application/json;charset={$this->_charset}");                        
                        $string = _08_Documents_JSON::encode($string,true);                        
                    break;
                    case 'object':
                        @header("Content-type:application/json;charset=UTF-8");
                        $string = get_object_vars($string);
                        $string = _08_Documents_JSON::encode($string);
                    break;
                    default:
                        $string = '';
                }
                
                if ( !empty($callback) )
                {
                    $string = "$callback($string);";             
                }
                           
                break;
            
            default :
                if(is_null($string)){ //ajax��ֱ��echo�ı���js���,û��return���
					//header('Content-Type: application/javascript'); 
					$string = ''; //�൱��: return '';
				}else{
					if ( !is_string($string) )
					{
						@header("Content-type:application/json;charset={$this->_charset}");
						$string = _08_Documents_JSON::encode($string);
                        if ( !empty($callback) )
                        {
							$string = "$callback($string);";
                        }						
					}
				}
                break;
        }
        
        return $string;
    }
    
    public function __construct()
    {
        parent::__construct();
        
        if ( empty($this->_get['datatype']) )
        {
            $this->_datatype = '';
        }
        else
        {
        	$this->_datatype = strtoupper(trim($this->_get['datatype']));
        }
        $this->__className = '';
        if ( strtolower($this->_action) === 'format' )
        {
            cls_message::show('��������');
        }
        
        if (isset($this->_get['charset']))
        {
            $this->_charset = strtoupper(trim($this->_get['charset']));
        } 
        
        if ((empty($this->_charset) && ($this->_datatype !== "JS")) || ($this->_charset === 'UTF8'))
        {
            $this->_charset = 'UTF-8'; 
        }
        else
        {
        	$this->_charset = cls_env::getBaseIncConfigs('mcharset');
        }
    }
    
    public static function getInstance()
    {
        return new self();
    }
}