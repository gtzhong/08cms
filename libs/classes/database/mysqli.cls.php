<?php
/**
 * MySQL��ǿ��չ�ӿڲ�����
 *
 * ���ʹ��MySQL4.1.3����°汾��ǿ�ҽ���ʹ�øýӿڣ�����ԭ��
 * ��������鿴��http://docs.php.net/manual/zh/mysqli.overview.php
 *
 * @author    Wilson <Wilsonnet@163.com>
 * @copyright Copyright (C) 2008 - 2014 08CMS, Inc. All rights reserved.
 */
!defined('M_COM') && exit('No Permisson');
class cls_mysqli
{
	public $link;
	public $name = '';
    protected $_mdebug = true;
    
    /**
     * �������ݿ�
     */
	public function connect( $dbhost, $dbuser, $dbpw, $dbname = '', $pconnect = 0, $halt = TRUE, $ncharset = '')
    {
        $dbport = @trim(cls_envBase::getBaseIncConfigs('dbport'));
		if(!$this->link = @mysqli_connect($dbhost, $dbuser, $dbpw, null, empty($dbport) ? 3306 : (int) $dbport)){
			if($halt){
				$this->halt('Can not connect to MySQL server', '', false);
			}else return false;
		}else{
			if($this->version() > '4.1')
            {
				global $dbcharset;
                $mcharset = cls_envBase::getBaseIncConfigs('mcharset');
				$ncharset = empty($ncharset) ? (empty($dbcharset) ? str_replace('-','',strtolower($mcharset)) : $dbcharset) : $ncharset;
                mysqli_query($this->link, "SET @@SESSION.sql_mode = '';");                
                if ( !function_exists('mysqli_set_charset') || (false === @mysqli_set_charset($this->link, $ncharset)) )
                {
                    // ���mysqli_set_charsetʧ��ʱ�����ⷽ������
                    $serverset = $ncharset ? 'character_set_connection='.$ncharset.', character_set_results='.$ncharset.', character_set_client=binary' : '';
    				$serverset && mysqli_query($this->link, "SET $serverset");
                }
			}
                        
			if($dbname && !@$this->select_db($dbname)){
				if($halt){
					$this->halt("Can not select database $dbname");
				}else return false;
			}
			$this->name = $dbhost.'_'.$dbname;
		}
		return true;
	}
    
    /**
     * ѡ�����ݿ���
     */
	public function select_db($dbname)
    {
		return mysqli_select_db($this->link, $dbname);
	}
    
    /**
     * ����һ�����ݿ��ѯ
     */
	public function query($sql, $type = '', $new_class = false)
    {
		// ����3��,�ڲ��ô���,����Ҫɾ��; ��ʹ��ɾ��,extend_sample�ǲ����ڵ�,Ҳ��Ӱ�칦��,ֻӰ����һ���Ч��
		if(is_file(M_ROOT.'extend_sample/mysql.inc.php')){
			include M_ROOT."extend_sample/mysql.inc.php";
		}
		
		global $_mdebug;
        empty($new_class) && $this->escape_old_sql($sql);
        
		$dbstart = microtime(TRUE);
		if( !($query = mysqli_query($this->link, $sql)) )
        {
			if(in_array($this->errno(), array(2006, 2013)) && substr($type, 0, 5) != 'RETRY'){
				$this->close();
				require M_ROOT.'base.inc.php';
				$this->connect($dbhost,$dbuser,$dbpw,$dbname,$pconnect,true,$dbcharset);
				return $this->query($sql,'RETRY'.$type);
			} elseif($type != 'SILENT' && substr($type, 5) != 'SILENT'){
				$this->halt('MySQL Query Error', $sql);
			}
		}
        
        if ($this->_mdebug)
        {
            empty($_mdebug) || $_mdebug->add($sql,microtime(TRUE)-$dbstart,$this->name);
        }
		return $query;
	}
    
    /**
     * �رնԵ�����Ϣ�ļ�¼
     **/
    public function closeDebug()
    {
        $this->_mdebug = false;
    }
    
    # ��ʱ�԰ѱ�ϵͳ֮ǰд��SQL������mysql_real_escape_string����ת����
    public function escape_old_sql( &$sql, $new_sql_param = array(), $action = false, $extra = false )
    {
        #$sql = "DELETE FROM cms_asession WHERE (mid='1' AND ip='127.0.0.1') OR dateline<1414507990";
        try
        {            
            $_08_SQL_Parser = _08_SQL_Parser::getInstance($sql);
            $sql = $_08_SQL_Parser#->setDebug()
                                   ->mergeSQL();
        }
        catch(UnableToCalculatePositionException $e)
        {
            $this->halt('MySQL Parse Error', $sql);
        }
    }

    /**
     * ����������
     *
     * @param  string $text   Ҫ���˵��ı���Ϣ
     * @param  bool   $extra  ����÷�����������������Ϊtrue
     *
     * @return string $result ���˺���ı�
     * @since  1.0
     */
    public function escape( $string, $extra = false )
	{
		$result = mysqli_real_escape_string($this->link, stripslashes($string));

		if ($extra)
		{
			$result = addcslashes($result, '%_');
			$result = str_replace('[08cmsKwBlank]','%',$result);
		}

		return $result;
	}
	
	public function fetch_array($query, $result_type = MYSQLI_ASSOC)
    {
        return mysqli_fetch_array($query, $result_type);
	}
    
	function ex_fetch_array($sql,$ttl = 0,$type = ''){//��չ������Ч
		$ExCacheKey = md5($this->name.$sql);
		$re = GetExtendCache($ExCacheKey,$ttl);
		if($re === false){
			if($query = $this->query($sql,$type)){
				$re = array();
				while($r = $this->fetch_array($query)) $re[] = $r;
				$this->free_result($query);
				SetExtendCache($ExCacheKey,$re,$ttl);
			}
		}
		return $re ? $re : array();
	}
	function fetch_one($sql,$ttl = 0,$type = ''){//ֻȡ�����������ĵ�һ����¼����չ������Ч
		$ExCacheKey = md5($this->name.$sql);
		$re = GetExtendCache($ExCacheKey,$ttl);
		if($re === false){
			if($query = $this->query($sql,$type)){
				$re = $this->fetch_array($query);
				$this->free_result($query);
				SetExtendCache($ExCacheKey,$re,$ttl);
			}
		}
		return $re ? $re : array();
	}
	function result_one($sql,$ttl = 0,$type = '') {//���ص�һ����¼�ĵ�һ���ֶ�,��չ������Ч
		$ExCacheKey = md5($this->name.$sql);
		$re = GetExtendCache($ExCacheKey,$ttl);
		if($re === false){
			if($query = $this->query($sql,$type)){
				$re = $this->result($query,0);
				$this->free_result($query);
				SetExtendCache($ExCacheKey,$re,$ttl);
			}
		}
		return $re ? $re : '';
	}
    
	public function affected_rows()
    {
		return mysqli_affected_rows($this->link);
	}
    
	public function error()
    {
		return $this->link ? mysqli_error($this->link) : mysqli_connect_error();
	}
    
	public function errno()
    {
		return $this->link ? mysqli_errno($this->link) : mysqli_connect_errno();
	}
    
    /**
     * @deprecated nv50 �����е���SQL��ѯ�ĵط��������������ʱ�����ú���
     */
	public function result($query, $row)
    {
        #return @mysql_result($query, $row);
        
        # �� $this->fetch_array() ���������棿�����д���֤
        $rowResult = $this->fetch_array($query, MYSQLI_NUM);
        return @$rowResult[$row];
	}
    
	public function num_rows($query)
    {
        return mysqli_num_rows($query);
	}
    
	public function num_fields($query)
    {
        die('���� _08_MysqlQuery::getTableColumns() ���档');
	}
    
	public function free_result($query)
    {
		return mysqli_free_result($query);
	}
    
	public function insert_id()
    {
		return ($id = mysqli_insert_id($this->link)) >= 0 ? $id : $this->result($this->query("SELECT last_insert_id()"), 0);
	}
    
	public function fetch_row($query)
    {
		return mysqli_fetch_row($query);
	}
    
	public function fetch_fields($query)
    {
		return mysqli_fetch_field($query);
	}
    
	public function version()
    {
		return mysqli_get_server_info($this->link);
	}
    
	public function close()
    {
		return mysqli_close($this->link);
	}
    
	public function halt($message = '', $sql = '', $checkUser = true)
    {
		global $timestamp,$tblprefix,$_no_dbhalt;
        # �����ݿ�����ʧ��ʱ���ж��û�Ҳ��ʾ��Ϣ����Ȼ�ж��û�ʱ�������ѭ��
        if ( $checkUser && class_exists('cls_UserMain') )
        {
            $curuser = cls_UserMain::CurUser();
        }
        else
        {
        	$curuser = null;
        }
		if(empty($_no_dbhalt)) include M_ROOT.'include/mysql.err.php';
	}
    
    public function fetch_object($query, $class_name = 'stdClass')
    {
        return mysqli_fetch_object($query, $class_name);
    }
    
    public function __construct()
    {        
        if( !function_exists('mysqli_connect') )
        {
            $this->halt('���ݿ����Ӵ���MYSQLI��չ�����ã�', '', false);
        }
    }
}