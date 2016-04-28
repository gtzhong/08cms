<?php
/**
 * չʾͳ����
 *
 * @author    Wilson <Wilsonnet@163.com>
 * @copyright Copyright (C) 2008 - 2014 08CMS, Inc. All rights reserved.
 */

defined('_08CMS_APP_EXEC') || exit('No Permission');
class _08_M_Ajax_View_Count_Base extends _08_Models_Base
{
    /**
     * ��ѯ����ID
     * 
     * @var int
     */
    protected $infoid = 0;
    
    /**
     * ģ��ID
     * 
     * @var int
     */
    protected $modid = 0;
    
    /**
     * ��ǰ����������
     * 
     * @var string
     */
    protected $_type;
    
    /**
     * �ֶΰ�������ֻ���ڰ��������ֶβ��ܻ�ȡͳ����
     * 
     * @var array
     */
    protected $whiteList = array(
        'a' => array('clicks', 'wclicks', 'mclicks'),
        'm' => array('msclicks'),
        'cu' => array('aid', 'ccid', 'tomid', 'mid')
    );
    
    /**
     * ͳ�����ֶ�����
     * 
     * @var string
     */
    protected $field = '';
    
    protected $parmas = array();
    
    /**
     * ע���÷������ܱ���д
     */
    final public function __toString()
    {
        defined( 'M_NOUSER' ) || define( 'M_NOUSER', 1 );
        
        $this->infoid = (empty($this->infoid) ? 0 : max(0,intval($this->infoid)));	
        $this->modid = (empty($this->_get['modid']) ? 0 : max(0,intval($this->_get['modid'])));	
        $this->field = (empty($this->_get['field']) ? '' : preg_replace('/[^\w]/', '', $this->_get['field']));

        if(empty($this->_get['type'])) {
            $this->_type = 'a'; 
        } else {
            $this->_type = strtolower(trim($this->_get['type']));
        }
        $method = "_{$this->_type}Statistics";
        
        if( (empty($this->infoid) && ($this->_type !== 'cu')) || empty($this->modid) || empty($this->field) || !method_exists($this, $method) )
        {
            return '0';
        }
        
        $fieldWhiteList = "{$this->_type}FieldWhiteList";
        
        if ( method_exists($this, $fieldWhiteList) )
        {
            foreach ( (array) $this->$fieldWhiteList() as $value ) 
            {
                if ( !in_array($value, $this->whiteList[$this->_type]) )
                {
                    array_push($this->whiteList[$this->_type], $value);
                }                
            }
        }
        
        return $this->ex_Statistics($method);
    }
    
	//��չ����,������ֻһ�����,����������չϵͳ��չ
    protected function ex_Statistics($method)
    {
		return $this->$method();
	}
	
    /**
     * �ĵ�ͳ����
     */
    protected function _aStatistics()
    {
       $channels = cls_cache::Read('channels');
	   if ( empty($channels[$this->modid])){
	   		return '0';
	   }
        if ( !in_array($this->field, $this->whiteList[$this->_type]) )
        {
            return '0';
        }
        
        $row = $this->_db->select($this->field)
                         ->from('#__' . atbl($this->modid) . ' a')
                         ->innerJoin("#__archives_{$this->modid} c")->_on('c.aid=a.aid')
                         ->where(array('a.aid' => $this->infoid))
                         ->_and('checked=1')
                         ->limit(1)#->setDebug()
                         ->exec()->fetch();
        return $row[$this->field];
    }
    
    /**
     * ��Աͳ����
     */
    protected function _mStatistics()
    {
       $mchannels = cls_cache::Read('mchannels');
	   if ( empty($mchannels[$this->modid])){
	   		return '0';
	   }
	    if ( !in_array($this->field, $this->whiteList[$this->_type]) )
        {
            return '0';
        }
        
        $row = $this->_db->select($this->field)
                         ->from("#__members_{$this->modid} c")
                         ->innerJoin("#__members m")->_on('c.mid=m.mid')
                         ->innerJoin("#__members_sub s")->_on('s.mid=m.mid')
                         ->where(array('c.mid' => $this->infoid))
                         ->limit(1)#->setDebug()
                         ->exec()->fetch();
        return $row[$this->field];
    }
    
    /**
     * ����ͳ����
     */
    protected function _cuStatistics()
    {
        $commu = cls_cache::Read('commu', $this->modid);
        if ( !isset($commu['tbl']) || !in_array($this->field, $this->whiteList[$this->_type]) )
        {
            return '0';
        }
        
        $this->_db->select("COUNT(*) AS num")->from("#__{$commu['tbl']}");
        if ($this->infoid)
        {
            $this->_db->where(array($this->field => $this->infoid)); 
        }
        else
        {
        	$this->_db->where('1 = 1'); 
        }
                  
        if ( isset($this->_get['_and']) )
        {
            $this->_splitParmas($this->_get['_and']);
        } 
                  
        if ( isset($this->_get['_or']) )
        {
            $this->_splitParmas($this->_get['_or'], '_or');
        }
        
        $row = $this->_db->_and('checked=1')
                         ->limit(1)#->setDebug()
                         ->exec()->fetch();
        return $row['num'];
    }
    
    /**
     * ��ϵͳ����
     */
    protected function _coStatistics()
    {
        $row = $this->_db->select($this->field)
                         ->from("#__coclass{$this->modid}")
                         ->where(array('ccid' => $this->infoid))
                         ->limit(1)#->setDebug()
                         ->exec()->fetch();
        return $row[$this->field];
    }
    
    /**
     * �ָ�����
     * 
     * @param string $params Ҫ�ָ��Ĳ����ַ���
     * @param string $type   Ҫ��װSQL�Ĳ������ͣ�һ��Ϊ _and �� _or����ֵ��{@see _08_MysqlQuery::$type}�෽��һ��
     */
    protected function _splitParmas( $params, $type = '_and' )
    {
        $params = array_map('trim', array_filter(explode(',', $params)));
        
        foreach ( $params as $param ) 
        {
            $_params = explode('_', $param);
            if ( count($_params) > 2 )
            {
                $_params[1] = str_ireplace(
                    array('not', 'large', 'small', 'largeand', 'smalland'),
                    array('!=', '>', '<', '>=', '<='),
                    $_params[1]
                );
                
                $this->_db->$type(array($_params[0] => implode('_', array_slice($_params, 2))), $_params[1]);
            }
            else
            {
            	$this->_db->$type(array($_params[0] => $_params[1]));
            }            
        }
    }
}