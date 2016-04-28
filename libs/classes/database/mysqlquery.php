<?php
/**
 * ���ݿ��ѯ��
 * д����Ϊ�˴󲿷ֲ�ѯ���󷽱�ʹ�ã���������޷����������ֱ��ʹ��$db->query֮���ԭ��������ѯ������˵���뿴{@see mysql.cls.php} �ļ���
 * 
 * @author    Wilson
 * @copyright Copyright (C) 2008 - 2012 08CMS, Inc. All rights reserved.
 * 
 * @example ���·���������ͨ��
    $row = $db->select('ms.*,m.*')->from('#__msession ms, #__members m')
              ->where('ms.mid = m.mid')
              ->_and(array('ms.msid'=> 'dzIB3C'))
              ->_and(array('m.password'=>'c3284d0f94606de1fd2af172aba15bf3'))
              ->_and(array('m.checked'=>1))
              ->exec()->fetch();
    var_dump($row);
    
    $row = $db->select('m.*,s.*')->from('#__members m')
              ->innerJoin('#__members_sub s')->_on('s.mid=m.mid')
              ->where(array('mname' => 'admin'))
              ->exec()->fetch();
    var_dump($row);
    
    $row = $db->select()->from('#__members m')
              ->where('m.mid')->_in('1')
              ->limit(1)
              ->exec()->fetch();
    var_dump($row);
    
    $db->select()->from('#__members AS m')
       ->where('m.mid')
       ->_in('1, 2, 6, 7')
       ->exec();
    while($row = $db->fetch())
    {
        var_dump($row);
    }
    
    $row = $db->select()->from('#__members m')
              ->where('m.mid = 1')
              ->_and("checked=1")
              ->having('COUNT(*) > 0')
              ->exec()->fetch();
    var_dump($row);
    
    $row = $db->select()->from('#__members AS m')
              ->leftJoin('#__members_1 AS m1')->_on('m.mid=m1.mid')
              ->where('m.mid = 1')
              ->exec()->fetch();
    var_dump($row);
    
    $row = $db->select('COUNT(*)')->from('#__archives16')
              ->where(array('chid'=>3))
              ->_and(array('createdate'=>'1370270040'))
              ->exec()->fetch();
    var_dump($row);
    
    $row = $db->getTableList();
    var_dump($row);
    
    $db->getTableList(true)->like('cms_', '_%')->exec();
    while($row = $db->fetch())
    {
        var_dump($row);
    }
    
    $value = 'admin@admin.com'; $opmode = 'edit'; $mid = 2;
    $db->select('mid')->from('#__members')->where(array('email' => $value));
    if( $mid && ($opmode == 'edit') )
    {
    	$db->_and("mid != {$mid}");
    }
    $uid = $db->exec()->fetch();
    var_dump($uid);
    
    $userInfo['username'] = 'admin';
    $row = $db->select('mid, password')->from('#__members')
              ->where(array('mname' => $userInfo['username']))
              ->_and('checked = 1')
              ->exec()->fetch();
    var_dump($row);
    
    $db->insert( '#__pms', 
        array(
            'fromuser' => 'test', 
            'fromid' => 1, 
            'toid' => 12, 
            'title' => 'te"st', 
            'content' => 'test', 
            'pmdate' => time()
        )
    )->exec();
    
    $db->insert( '#__pms', 'fromuser, fromid, toid, title, content, pmdate',
        array(
            array('test', 1, 12, 'te"dddddst', 'test', time()),
            array('test', 1, 12, 'te"dddddst_', 'test', time())
        )
    )->exec();
    
    $db->insert( '#__pms', 'fromuser, fromid, toid, title, content, pmdate',
        array('test', 1, 12, 'te"ddddds__t', 'test', time())
    )->exec();
    
    $db->delete('#__pms')->where('pmid = 9')->exec();
    
    $db->update('#__pms', array('title' => 'sdd', 'viewed' => 1))->where('pmid = 14')->exec();
    $db->update('#__pms', 'title, viewed', array('sss_st"t_dddd', 0))->where('pmid = 14')->exec();
    $db->update('#__pms', 'title')->where('pmid = 14')->exec(); # ����title�ֶ�Ϊ��ֵ
    
    
   ��ѭ������
    $db->select('fc.title, fc.fcaid')->from('#__fcatalogs AS fc')->exec();
    while($row = $db->fetch()) {
        echo "<div>{$row['fcaid']}---------{$row['title']}</div>";
    }
     
    ��ѭ��Ƕ�ײ�ѯ����
    $query = $db->select('mid')->from("#__members")->limit(3)->getQuery();
    while($row = $db->fetch($query))
    {
        $row2 = $db->select()->from('#__archives1')->where(array('mid' => $row['mid']))->limit(1)->exec()->fetch();
        var_dump($row, $row2);
    }
 */

class _08_MysqlQuery
{
    /**
     * ������SQL���
     *
     * @var   string
     * @since nv50
     */
    protected $_sql = '';
    
    /**
     * ���������ݱ�����
     *
     * @var   string
     * @since nv50
     */
    protected $_tableName = '';

    /**
     * query�����ķ���ֵ
     *
     * @var   resource
     * @since nv50
     */
    private $query = null;

    /**
     * ��������
     *
     * @var   string
     * @since nv50
     */
    protected $_type = '';

    /**
     * �Ƿ�ʹ����LIKE����
     *
     * @var   bool
     * @since nv50
     */
    private $like = false;

    /**
     * ���ݱ�ǰ׺
     *
     * @var   string
     * @since nv50
     */
    protected $_tblprefix = 'cms_';
    
    private $dervers = 'MySQL';
    
    private $debug = false;
    
    private $db = null;

    /**
     * ��ѯһ������
     *
     * @param string $fields Ҫ��ѯ�������ֶ�
     * @param bool   $use_this_table ʹ��$this->_tableName��������ʹ���򴫵�TRUE������FALSE
     * @since nv50
     */
    public function select( $fields = '*', $use_this_table = false )
    {		
        $fields = str_replace(array("\r", "\n"), '', $fields);
        $fields = $this->quoteName( $fields );
        $this->_sql .= "SELECT {$fields} ";
        
        if ( $use_this_table && $this->_tableName )
        {
            $this->from($this->_tableName);
        }
        
        return $this;
    }

    /**
     * ����һ�����ݵ����ݱ�
     *
     * @param string $table_name Ҫ��������ݱ�����
     * @param mixed  $fields     �����ֵΪ�ַ���ʱ��ֻ�����ֶ����ƣ���Ҫ���ݵ�����������Ϊֵ��
     *                           ���Ϊ����ʱ��Ҫ������ֶ����ݣ�KEYΪ�ֶ�����VALUEΪֵ�����������������ô��ݣ�
     * @param array  $values     Ҫ������ֶ�����ֵ�����ڶ�������$fieldsΪ�ַ���ʱ����Ҫ���ݸò��������ҳ���Ҫ��ڶ���������ͬ��
     *
     * @since nv50
     */
    public function insert( $table_name, $fields, $values = array(), $replace = false )
    {
        if ( is_array($fields) )
        {
            # ע��array_keys��array_values˳�����һ��
            $field_name = $this->filterField( array_keys($fields) );            
            $values = $this->filterInsertValue( array_values($fields) );
        }
        else if ( is_string($fields) )
        {
            $field_name = $this->filterField( $fields );
            $values = (array) $values;
            
            /**
             * �ø÷���֧�����ֵ��ã�����һ���Բ���������ݣ�ע��ֻ�еڶ�������Ϊ�ַ���ʱ��֧�֣���
             * $db->insert( '#__pms', 'fromuser, fromid, toid, title, content, pmdate',
                    array(
                        array('test', 1, 12, 'te"dddddst', 'test', time()),
                        array('test', 1, 12, 'te"dddddst_', 'test', time())
                    )
               )->exec();
             */
            if ( isset($values[0]) && is_array($values[0]) )
            {
                $values = $this->filterInsertValue($values, false);
            }
            else
            {
            	$values = $this->filterInsertValue($values);
            }
        }
        else
        {
            return false;
        }
		
        if( !empty($field_name) && is_string($values) )
        {
            $table_name = $this->quoteName($table_name); 
            if ($replace)
            {
                $action = 'REPLACE';
            }
            else
            {
            	$action = 'INSERT';
            }
            $this->_sql = "$action INTO {$table_name} ({$field_name}) VALUES {$values} ";
        }

        return $this;
    }
    
    /**
     * �滻����һ�����ݵ����ݱ�
     *
     * @param string $table_name Ҫ��������ݱ�����
     * @param mixed  $fields     �����ֵΪ�ַ���ʱ��ֻ�����ֶ����ƣ���Ҫ���ݵ�����������Ϊֵ��
     *                           ���Ϊ����ʱ��Ҫ������ֶ����ݣ�KEYΪ�ֶ�����VALUEΪֵ�����������������ô��ݣ�
     * @param array  $values     Ҫ������ֶ�����ֵ�����ڶ�������$fieldsΪ�ַ���ʱ����Ҫ���ݸò��������ҳ���Ҫ��ڶ���������ͬ��
     *
     * @since nv50
     */
    public function replace( $table_name, $fields, $values = array() )
    {
        return $this->insert($table_name, $fields, $values, true);
    }
    
    /**
     * ����һ�����ݵ����ݱ���
     * �÷�����ʹ���� parent::getModels() ��ȡ������ݱ�������MVC�ܹ��д���{@see self::insert()} ������ʹ��
     * 
     * @param  array $insert_values Ҫ�����ֵ��KEYΪ�ֶ����ƣ�VALUEΪֵ
     * @param  bool  $replace       �Ƿ���replace into��ʽ���룬trueΪ�ǣ�falseΪ��insert into
     * @return mixed                �������ɹ����ص�ǰ�����������򷵻�FALSE
     * 
     * @since  nv50
     */
    public function create( array $insert_values, $replace = false )
    {
        $field_name = $this->filterField( array_keys($insert_values) );            
        $values = $this->filterInsertValue( array_values($insert_values) );
        
        if( !empty($field_name) && is_string($values) )
        {
            if ( $replace )
            {
                $method = 'REPLACE';
            }
            else
            {
            	$method = 'INSERT';
            }
            $table_name = $this->quoteName($this->_tableName);
            $this->_sql = "$method INTO {$table_name} ({$field_name}) VALUES {$values} ";
            return $this->exec();
        }
        
        return false;
    }

    /**
     * ��ȡ���ݣ�ע���÷������������ѯ����һ�����Ժܷ���Ĳ�ѯ
     * ���·����õ��� parent Ϊ�ࣺ_08_Application_Base����ģ������ΪĿ¼��/include/application/models/ �µı���ģ�����׺
     *
     * @example $members = parent::getModels('Members');
     *          $members2 = parent::getModels('Members2');
     *          $members3 = parent::getModels('Members3');
     *          $row = $members->where(array('mid' => 2))->read('*', false);
     *          $row2 = $members2->read('*', false);
     *          $row3 = $members3->read();  // ����һ�¾��ܶ�ȡ��midΪ2�����������е����ݣ����ٶ��дͬһ��where���鷳
     *          
     * @param   string $fields   Ҫ��ȡ���ֶ����ƣ������Ӣ�Ķ��ŷָ�
     * @param   bool   $clearSQL �Ƿ��ѯ�������һ��SQL��Ϣ, TRUEΪ�����FALSEΪ���������һ������ʹ��
     * @return  array  $row      ��ȡ����ֶ�������Ϣ
     * @since   nv50
     **/
    public function read( $fields = '*', $clearSQL = true )
    {
        $fields = $this->quoteName( $fields );
        $table_name = $this->quoteName($this->_tableName);
        $clearSQL || $sql = $this->_sql;
        $this->_sql = "SELECT {$fields} FROM {$table_name} " . $this->_sql;
        $this->limit(1)->exec($clearSQL);
        $row = $this->fetch();
        $clearSQL || $this->_sql = $sql;

        return $row;
    }

    /**
     * ����һ������
     *
     * @param string $table_name Ҫ���µ����ݱ�����
     * @param mixed  $fields     �����ֵΪ�ַ���ʱ��ֻ�����ֶ����ƣ���Ҫ���ݵ�����������Ϊֵ��
     *                           ���Ϊ����ʱ��Ҫ������ֶ����ݣ�KEYΪ�ֶ�����VALUEΪֵ�����������������ô��ݣ�
     * @param array  $values     Ҫ������ֶ�����ֵ�����ڶ�������$fieldsΪ�ַ���ʱҪ���ݸò��������ҳ���Ҫ��ڶ���������ͬ��
     *                           ���Ҫ���ֶ����ֻ����һ��array('')���ɣ�
     *
     * @since nv50
     */
    public function update( $table_name, $fields = '', $values = array() )
    {
        /**
         * ����һ���·�ʽ���ã���ʱ��֧���Ϸ�ʽ
         * @example $table = parent::getModels('UserFiles_Table');  // ע��Ŀǰ��ǰ���÷���ֻ֧��MVC�ܹ�����ã�
         *                                                          // UserFiles_Table�� /include/application/models/
         *                                                          // Ŀ¼�µ��� table.php ��׺���ļ����������׺��
         *                                                          // ����������Ӧ���ǣ�userfiles_table.php �ļ����������׺
         *          $table->where(array('aid' => 1))->update(array('title' => 'test'));
         */
        if ( is_array($table_name) && $this->_tableName )
        {
            $fields = $table_name;
            $table_name = $this->_tableName;
        }
        
        if ( is_array($fields) )
        {
            # ע��array_keys��array_values˳�����һ��
            $field_name = $this->filterField( array_keys($fields) );
            $sql = $this->filterUpdateValue($field_name, array_values($fields));
        }
        else if ( is_string($fields) )
        {
            if ( empty($values) )
            {
                $sql = $field_name = $this->quoteName($fields);
            }
            else
            {
                $field_name = $this->filterField( $fields );
            	$sql = $this->filterUpdateValue( $field_name, (array) $values );
            }
        }
        else
        {
            return false;
        }
        
        if( !empty($field_name) && isset($sql) )
        {
            $table_name = $this->quoteName($table_name);            
        
            if ( $this->_tableName )
            {
                $this->_sql = "UPDATE {$table_name} SET {$sql} " . $this->_sql;
                return $this->exec();
            }
            else
            {
            	$this->_sql = "UPDATE {$table_name} SET {$sql} ";
            }            
        }
        
        return $this;
    }
    
    /**
     * ɾ��һ������
     * 
     * @param string $table_name Ҫɾ�����ݵı�����
     * @return object ���ص�ǰ����ָ��
     *
     * @since  1.0
     */
    public function delete($table_name = '')
    {
        if ( empty($table_name) || is_bool($table_name) )
        {
            $clearSQL = (false === $table_name ? false : true);
            $clearSQL || $sql = $this->_sql;
            $table_name = $this->quoteName($this->_tableName);
            $this->_sql = ("DELETE FROM {$table_name} " . $this->_sql);
            $flag = $this->exec($clearSQL);
            $clearSQL || $this->_sql = $sql;
            return $flag;
        }
        
        $table_name = $this->quoteName($table_name);
        $this->_sql = "DELETE FROM {$table_name} ";
        return $this;
    }

    /**
     * ƴ��FROM���
     *
     * @param  string $sql Ҫƴ�ӵ�SQL
     * @return object ���ص�ǰ����ָ��
     *
     * @since  1.0
     */
    public function from($table_name)
    {
        $table_name = $this->quoteName($table_name);
        $this->_sql .= "FROM {$table_name} ";
        return $this;
    }

    /**
     * ƴ��WHERE���
     *
     * @param  mixed  $values    Ҫƴ�ӵ��ֶ�����ֵ��ע���ô�ֻ�ᴦ���һ������Ԫ�أ�����ֵֻҪ��֤���治����SQLע��Ƿ��ַ�ʱ��
     *                           �����ַ��������鷽ʽ���õ�������зǷ��ַ�ʱ����������ã��磺where a.mid = b.mid ��  where mid = 1 
     *                           ��ʹ��   ->where(array('a.mid' => 'b.mid'))         ->where(array('mid' => 1)) 
     *                           ��       ->where('a.mid = b.mid')                   ->where('mid = 1')       ���ֵ��÷��� 
     *                           ������ǣ�where mid = '1' ʱ����ֻ����������ã���Ϊ1��������������ţ���������SQLע��ķǷ��ַ�
     * @param  bool   $operators ������Ĭ��Ϊ '=' ������ = ֵ��������� '!=' ʱ���� �� != ֵ
     * @return object            ���ص�ǰ����ָ��
     *
     * @since  1.0
     */
    public function where( $values, $operators = '=' )
    {
        if ( !empty($values) )
        {
            $this->_sql .= "WHERE " . $this->filterParams($values, $operators) . ' ';
            
            if (count($values) > 1)
            {
                $values = cls_Array::limit($values, 1, count($values), true);
                foreach ( $values as $key => $value )
                {
                    $this->_and(array($key => $value));
                }
            }
            
            $this->_sql .= ' ';
        }
        
        return $this;
    }

    /**
     * ƴ��OR����
     * �磺$db->select('*')->from('#__members')->where('mname')->like('a')->_and('mname')->like('d')->exec()->fetch();
     *
     * @param  mixed  $values    Ҫƴ�ӵ��ֶ�����ֵ��ע���ô�ֻ�ᴦ���һ������Ԫ�أ��������ֵΪ�ַ���ʱ��
     *                           ֻ�������ݱ���ֶ�������ɵ��ַ�������������ֵΪ�ֶ�ʱ����һ�������� ` ����
     * @param  bool   $operators ������Ĭ��Ϊ '=' ������ = ֵ��������� '!=' ʱ���� �� != ֵ
     * @since nv50
     */
    public function _or( $values, $operators = '=' )
    {
        if ( !empty($values) )
        {
            $this->_sql .= "OR " . $this->filterParams($values, $operators) . ' ';
        }
            
        return $this;
    }

    /**
     * ƴ��AND����
     * �磺$db->select()->from('#__members')->where(array('mid' => '1'))->_and(array('checked' => '1'))->exec()->fetch()
     *
     * @param  mixed  $values    Ҫƴ�ӵ��ֶ�����ֵ��ע���ô�ֻ�ᴦ���һ������Ԫ�أ��������ֵΪ�ַ���ʱ��
     *                           ֻ�������ݱ���ֶ�������ɵ��ַ�������������ֵΪ�ֶ�ʱ����һ�������� ` ����
     * @param  bool   $operators ������Ĭ��Ϊ '=' ������ = ֵ��������� '!=' ʱ���� �� != ֵ
     * @since  1.0
     */
    public function _and( $values, $operators = '=' )
    {
        if ( !empty($values) )
        {
            $this->_sql .= "AND " . $this->filterParams($values, $operators) . ' ';
        }
        
        return $this;
    }
    
    /**
     * ���˰�ȫ����
     * 
     * @param  mixed  $params    Ҫ���˵Ĳ���
     * @param  bool   $operators ������Ĭ��Ϊ '=' ������ = ֵ��������� '!=' ʱ���� �� != ֵ
     * @return string $sql       ���˺�Ĳ�����ɵ�SQL
     * 
     * @since  1.0
     */
    public function filterParams( $params, $operators = '=' )
    {
        if ( is_array($params) )
        {
            $field = key($params);
            $value = current($params);
            
            $field = $this->_addBacktick($field);
            $value = $this->filterValue($value);
            
            $sql = "{$field} {$operators} {$value}";
        }
        else
        {
        	$sql = $this->quoteName( (string) $params );
        }
        
        return $sql;
    }

    /**
     * ƴ��ORDER BY �������
     *
     * @param  string $sql Ҫƴ�ӵ�SQL
     * @return object      ���ص�ǰ����ָ��
     *
     * @since  1.0
     */
    public function order($sql)
    {
        $sql = $this->quoteName($sql);
        $this->_sql .= "ORDER BY {$sql} ";
        return $this;
    }

    /**
     * ƴ��HAVING ���
     *
     * @param  string $sql Ҫƴ�ӵ�SQL
     * @return object      ���ص�ǰ����ָ��
     *
     * @since  1.0
     */
    public function having($sql)
    {
        $sql = $this->quoteName($sql);
        $this->_sql .= "HAVING {$sql} ";
        return $this;
    }

    /**
     * ƴ��GROUP BY ���
     *
     * @param  string $field Ҫƴ�ӵ��ֶ�����
     * @return object        ���ص�ǰ����ָ��
     *
     * @since  1.0
     */
    public function group( $field )
    {
        $field = $this->quoteName($field);
        $this->_sql .= "GROUP BY {$field} ";
        return $this;
    }

    /**
     * ƴ��LIMIT ���
     *
     * @param  int    $limit  ��ʼƫ�Ƶ�λ��
     * @param  int    $offset ƫ����
     * @return object         ���ص�ǰ����ָ��
     *
     * @since  1.0
     */
    public function limit( $limit, $offset = 0 )
    {
        $limit = (int) $limit;
        $offset = (int) $offset;
        
        if ( $limit >= 0 )
        {
            $this->_sql .= "LIMIT {$limit} ";
        }
        
        if ( $offset > 0 )
        {
            $this->_sql .= ", {$offset} ";
            //$this->_sql .= "OFFSET {$offset} ";  # ������ƫ��λ�÷ź�����ÿ�ʹ�ø����
        }
        
        return $this;
    }
    
    /**
     * ���ֶ���ӷ�����
     * 
     * @param  string $field    �ֶ���
     * @return string           �Ѿ���ӷ����ŵ��ֶ���
     * 
     * @since  1.0
     */
    protected function _addBacktick( $field )
    {
        $field = (string) $field;
        
        if ( (false !== stripos($field, '.`')) || (@$field{0} === '`') )
        {
            $field = $this->quoteName($field);
        }
        else
        {
            # ��ֱ������ֶ���
            $field_split = explode('.', $field);
            
            if ( isset($field_split[1]) )
            {
                $field = $this->quoteName( $field_split[0] . '.`' . $field_split[1] . '`' );
            }
            else
            {
            	$field = ('`' . $this->quoteName($field_split[0]) . '`');
            }
        }
        
        return $field;
    }

    /**
     * ƴ��LIKE���
     *
     * @param  string $text  ��������
     * @param  string $split ��ѯ��ʽ�������һ���ַ�Ϊ��%����Ϊ keyword% ������ѯ��
     *                                 ����ڶ����ַ�Ϊ��%������ %keyword ������ѯĬ��Ϊ %keyword% ��ѯ��
     *                                 ���򵱴��ݿ��ַ�ʱʹ�� field = 'keyword' ������ѯ
     * @return object ���ص�ǰ����ָ��
     *
     * @since  1.0
     */
    public function like($text, $split = '%%', $multi = false)
    {
        $this->like = true;
        $text = $this->escape($text, true);
        if($split{0} == '%')
        {
            $text = '%' . $text;
        }
        if($split{1} == '%')
        {
            $text .= '%';
        }
		$multi && $text = str_replace(array(' ','*'),'%',$text);
        $this->_sql .= "LIKE '{$text}' ";

        return $this;
    }

    /**
     * ƴ�� LEFT JOIN ����
     *
     * @param string $table_name Ҫƴ�ӵ����ݱ�
     *
     * @since nv50
     */
    public function leftJoin($table_name)
    {
        $this->join('left', $table_name);
        return $this;
    }

    /**
     * ƴ�� RIGHT JOIN ����
     *
     * @param string $table_name Ҫƴ�ӵ����ݱ�
     *
     * @since nv50
     */
    public function rightJoin($table_name)
    {
        $this->join('right', $table_name);
        return $this;
    }

    /**
     * ƴ�� INNER JOIN ����
     *
     * @param string $table_name Ҫƴ�ӵ����ݱ�
     *
     * @since nv50
     */
    public function innerJoin($table_name)
    {
        $this->join('inner', $table_name);
        return $this;
    }

    /**
     * ƴ��JOIN���
     *
     * @param  string $type        ƴ�����ͣ��У�left,right,inner
     * @param  string $table_name  Ҫ��������ݱ���
     * @return object              ���ص�ǰ����ָ��
     * @since  1.0
     */
    public function join($type, $table_name)
    {
        $type = strtoupper($type);
        $table_name = $this->quoteName($table_name);
        $this->_sql .= $type . " JOIN {$table_name} ";
        return $this;
    }
    
    /**
     * ƴ��ON����
     * �÷���ֻΪ�����ѯƴ�Ӷ�д����ʹ�������ѯʱ�ɲ�ƴ�Ӵ˷���
     * �磺$db->select()->from('#__members AS m')->innerJoin('#__members_1 AS m1')->_on('m.mid=m1.mid')->where('m.mid=1')->exec()->fetch();
     *
     * @param string $sql Ҫƴ�ӵ�SQL��Ϣ
     * @since nv50
     */
    public function _on($sql)
    {
        if ( !empty($sql) )
        {
            $sql = $this->quoteName($sql);
            $this->_sql .= "ON {$sql} ";
        }
        
        return $this;
    }
    
    /**
     * ƴ��IN��������������Ӳ�ѯ��������һ��SQL����ѯ��ֵ�ٴ��ݵ��ò������ǿ�Ƽ����Ӳ�ѯ��ʹ�ã�
     * 
     * ��ȷ���÷���1��$db->select()->from('#__members AS m')->where('m.mid')->_in(array(1, 2, 3))->exec();
                      while($row = $db->fetch()){.....}
                        
     * ��ȷ���÷���2��$db->select()->from('#__members AS m')->where('m.mid')->_in('1, 2, 3')->exec();
                      while($row = $db->fetch()){.....}
                        
     * ������÷����磺$db->select()->from('#__members AS m')->where('m.mid')->_in("'1','2','3'")->exec();
     *
     * @param mixed $values Ҫƴ�ӵ�ֵ��ע����ֵ��Ϊ������ַ������������ֵ�п��ܻ�����SQLע��������ַ�ʱ�������鴫�ݣ������ѯ����ͨ����
     * @since nv50
     */
    public function _in( $values )
    {
        if ( empty($values) )
        {
            $values = $this->filterValue( $values );
            $this->_sql .= "= {$values} ";
        }
        else
        {
        	if ( is_array($values) )
            {
                $values = implode(', ', array_map(array($this, 'filterValue'), $values));
            }
            else
            {            	
                $values = $this->escape( $values );
            }
            $this->_sql .= "IN ({$values}) ";
        }
        
        return $this;            
    }
    
    /**
     * ƴ��NOT���
     * 
     * @param mixed  $values Ҫƴ�ӵ�ֵ��ע����ֵ��Ϊ������ַ������������ֵ�п��ܻ�����SQLע��������ַ�ʱ�������鴫�ݣ������ѯ����ͨ����
     * @param string $method ƴ�������������磺  like��_in
     */
    public function not( $values, $method )
    {
        $this->_sql .= 'NOT ';
        return $this->$method($values);
    }

    /**
     * ����һ��SQL��ѯ
     *
     * @param  string $clearSQL �Ƿ��ѯ�������һ��SQL��Ϣ, TRUEΪ�����FALSEΪ���������һ������ʹ��
     * @param  string $type     ��ѯ���ͣ�������鿴��{@see cls_mysql::query()}
     * @return object ��ѯ�ɹ����ص�ǰָ�룬���򷵻�FALSE
     * @since  1.0
     */
    public function exec( $clearSQL = true,  $type = '' )
    {
        if(empty($this->_sql)) return false;
        if ( $this->debug )
        {
        	var_dump($this->_sql);
            echo '<br />';
        }
        $this->query = $this->db->query(trim($this->_sql), $type, true);
        $clearSQL && $this->clear();
        
        return ($this->query ? $this : false);
    }
    
    /**
     * ��ȡ��ǰquery���
     * 
     * @param  string   $clearSQL �Ƿ��ѯ�������һ��SQL��Ϣ, TRUEΪ�����FALSEΪ���������һ������ʹ��
     * @param  string   $type     ��ѯ���ͣ�������鿴��{@see cls_mysql::query()}
     * 
     * @return resource           ����mysql_query��ѯ�����Դ��ʶ���������ѯʧ���򷵻�FALSE
     */
    public function getQuery( $clearSQL = true,  $type = '' )
    {
        if ( $this->exec($clearSQL, $type) )
        {
            return $this->query;
        }
        
        return FALSE;
    }

    /**
     * ��ѯ��Ϣ
     * ���÷���ʾ��һ��ѭ������
     * $db->select('fc.title, fc.fcaid')->from('#__fcatalogs AS fc')->exec();
     * while($row = $db->fetch()) {
     *     echo "<div>{$row['fcaid']}---------{$row['title']}</div>";
     * }
     *
     * ���÷���ʾ������ѭ��Ƕ�ײ�ѯ����
     * $query = $db->select('mid')->from("#__members")->limit(3)->getQuery();
       while($row = $db->fetch($query))
       {
           $row2 = $db->select()->from('#__archives1')->where(array('mid' => $row['mid']))->limit(1)->exec()->fetch();
           var_dump($row, $row2);
       }
     *
     * ���÷���ʾ��������ѯ��������
     * $row = $db->select('fc.title, fc.pid')->from('#__fcatalogs AS fc')->exec()->fetch();
     *
     * @limk http://docs.php.net/manual/zh/function.mysql-fetch-array.php
     *
     * @param  resource $query       ѭ����������ƣ������Ϊ��ʱֻ��ѯһ��������ѭ������
     * @param  int      $result_type ��ѯ���ͣ����Խ�������ֵ��
     *                               MYSQL_ASSOC��MYSQL_NUM �� MYSQL_BOTH��
     *                               Ĭ��ֵ�� MYSQL_BOTH
     * @return                       ���ظ��ݴӽ����ȡ�õ������ɵ����飬���û���򷵻� FALSE
     * @since  1.0
     */
    public function fetch( $query = '', $result_type = '' )
    {
        if( !is_resource($query) )
        {
            $query = $this->query;
        }
        
        if ( strtoupper($this->dervers) == 'MYSQLI' )
        {
            $result_type = MYSQLI_ASSOC;
        }
        else
        {
        	$result_type = MYSQL_ASSOC;
        }
        
        return $this->db->fetch_array( $query, $result_type );
    }

    /**
     * �޸ı�ṹ
     *
     * @example       $db->alterTable('#__members', array('Field' => 'DROP COLUMN test'));
                      $db->alterTable('#__members', array('Field' => 'ADD COLUMN test', 'Type' =>'varchar(10)', 'Null' => false));
     * @param  string $table_name Ҫ�޸ĵı�����
     * @param  string $condition  �޸�����
     *
     * @return bool   �޸ĳɹ�����TRUE�����򷵻�FALSE
     * @since  1.0
     */
    public function alterTable($table_name, $condition)
    {
        $table_name = $this->quoteName($table_name);
        $sql = $this->_getColumnSQL($condition);
        $this->_sql = "ALTER TABLE `{$table_name}` {$sql}";
        return (bool)$this->exec();
    }
    
    /**
     * ��ȡ����Ϣ
     * 
     * @param  array  $field �ֶ���Ϣ���飬key�У�Field��Type��Null��Defalut��Extra
     * @return string        ���ع���õ�SQL 
     */
    protected function _getColumnSQL(array $field)
	{
		$blobs = array('text', 'smalltext', 'mediumtext', 'largetext');

		$fieldName = (string) $field['Field'];
		$fieldType = isset($field['Type']) ? $field['Type'] : '';
		$fieldNull = isset($field['Null']) ? (bool)$field['Null'] : null;
		$fieldDefault = isset($field['Default']) ? (string) $field['Default'] : null;
		$fieldExtra = isset($field['Extra']) ? (string) $field['Extra'] : '';

		$sql = $this->quoteName($fieldName) . ' ' . $fieldType;

		if ( $fieldNull !== null )
        {
            if ($fieldNull)
    		{
    			if ($fieldDefault === null)
    			{
    				$sql .= ' DEFAULT NULL';
    			}
    			else
    			{
    				$sql .= ' DEFAULT ' . $this->filterValue($fieldDefault);
    			}
    		}
    		else
    		{
    			if (in_array($fieldType, $blobs) || $fieldDefault === null)
    			{
    				$sql .= ' NOT NULL';
    			}
    			else
    			{
    				$sql .= ' NOT NULL DEFAULT ' . $this->filterValue($fieldDefault);
    			}
		    }
        }

		if ($fieldExtra)
		{
			$sql .= ' ' . strtoupper($fieldExtra);
		}

		return $sql;
	}

    /**
     * ����ֵ���Բ��뷽ʽ����
     *
     * @param  array  $values      Ҫ���˵�ֵ
     * @param  string $parentheses �Ƿ���ֵ�����С���ţ�ע��һ���Բ����������ʱ���봫��false��
     * @return string              ���˺��ֵ
     * @since  1.1
     */
    public function filterInsertValue( array $values, $parentheses = true )
    {
        $arrays = array();
        foreach ($values as $value)
        {
            if ( is_array($value) )
            {
                $arrays[] = $this->filterInsertValue($value, true);
            }
            else
            {
            	$arrays[] = $this->filterValue($value);
            }
        }
        
        return (($parentheses ? '(' : '') . implode(', ', $arrays) . ($parentheses ? ')' : ''));
    }

    /**
     * �˹��ֶ�����
     *
     * @param  mixed  $field_name Ҫ���˵��ֶ�����
     * @return string $new_fields ���˺���ֶ�����
     *
     * @since  1.0
     */
    public function filterField( $field_name )
    {
        if(is_string($field_name)) {
            $field_array = explode(',', $field_name);
        } else {
            $field_array = (array) $field_name;
        }
        
        $new_fields = array();
        foreach($field_array as $name)
        {
            $name = trim($name);
            $new_fields[] = $this->_addBacktick($name);
        }
        
        return implode(', ', $new_fields);
    }

    /**
     * ����ֵ���Ը��·�ʽ����
     *
     * @param  mixed  $field     Ҫ���˵��ֶ�
     * @param  mixed  $value     Ҫ���˵�ֵ
     * @param  string $delimiter �ָ���
     * 
     * @return string ���˺��ֵ
     * @since  1.0
     */
    public function filterUpdateValue( $field, array $value, $delimiter = ', ' )
    {
        if( empty($field) ) return '';
        if( !is_array($field) )
        {
            $field_array = explode(',', (string) $field);
        }
        else
        {
        	$field_array = $field;
        }
        
        $new_value = array();
        for($i = 0; $i < count($field_array); ++$i)
        {
            $new_value[] = "{$field_array[$i]} = " . (empty($value[$i]) ? "'' " : $this->filterValue($value[$i]));
        }

        return implode($delimiter, $new_value);
    }

    /**
     * ����ֵ
     *
     * @param  string $value Ҫ���˵�ֵ�������ֵ��һ�����飬���Ҵ���type��valueʱ�������������ͣ�Ŀǰ֧��typeΪfield��Ϊһ���ֶ����ͣ�ֵ���������
     *                       �磺array( 'field_name' => array('type'=> 'field', 'value' => "field_name + 1") )
     * @return string        �����Ѿ����˵�ֵ
     *
     * @since  nv50
     **/
    public function filterValue($value)
    {
        if ( is_array($value) )
        {
            if ( isset($value['type']) && (strtolower($value['type']) == 'field') )
            {
                return $this->_db->escape($value['value']);
            }
        }
        
        return ("'" . $this->escape($value) . "'");
    }

    /**
     * ��ȡ���ݱ�����
     * ע���÷����Ժ�����ã��������MVC�е�����ʹ�� _08_MysqlQuery::count ����
     * @example $this->_db->where(array('aid' => $aid))->_and(array('checked' => 1))->getTableRowNum("#__$ntbl");
     *
     * @param  string $table_name Ҫ��ȡ�����ݱ�����
     * @return array  $row        ��������
     *
     * @since  1.0
     */
    public function getTableRowNum( $table_name )
    {
        $sql = "SELECT COUNT(*) AS num FROM " . $this->quoteName($table_name);
        if ( empty($this->_sql) )
        {
            $this->_sql = $sql;
        }
        else
        {
        	$this->_sql = ($sql . ' ' . $this->_sql);
        }
        
        $row = $this->exec()->fetch();
        return $row['num'];
    }

    /**
     * ��ȡ���ݱ�����
     * ע���÷������滻_08_MysqlQuery::getTableRowNum���������MVC�е�����ʹ�ø÷�������_08_MysqlQuery::getTableRowNum
     *
     * @param  string $table_name Ҫ��ȡ�����ݱ�����
     * @return array  $row        ��������
     *
     * @since  1.0
     */
    public function count( $clearSQL = true )
    {
        $row = $this->read('COUNT(*) AS num', $clearSQL);
        return $row['num'];
    }

    /**
     * ��ȡĳ�����ֶ���Ϣ
     *
     * @param  string $table_name Ҫ��ȡ�����ݱ�����
     * @param  bool   $type_only  �Ƿ�ֻ��ȡ���ͣ���ΪTRUE����ΪFLASE
     * @return array  $result     �����ֶ���Ϣ����
     *
     * @since  1.0
     */
    public function getTableColumns( $table_name, $type_only = true )
	{
		$result = array();
		$this->_sql = 'SHOW FULL COLUMNS FROM ' . $this->quoteName($table_name);
		$fields = $this->loadObjectList();
		if ($type_only) {
			foreach ($fields as $field)
			{
				$result[$field->Field] = $field->Type;
				#$result[$field->Field] = preg_replace("/[(0-9)]/", '', $field->Type);
			}
		} else {
			foreach ($fields as $field)
			{
				$result[$field->Field] = $field;
			}
		}

		return $result;
	}

    /**
     * ����һ�������б�
     *
     * @param  string $key   ָ�������±�
     * @param  string $class ʹ�÷��ص��ж��������
     * @return array  $array ���ض����б�����
     *
     * @since nv50
     */
    public function loadObjectList($key = '', $class = 'stdClass')
	{
		$array = array();
        $this->exec();
		while ($row = $this->db->fetch_object($this->query, $class))
		{
			if (!empty($key))
			{
				$array[$row->$key] = $row;
			}
			else
			{
				$array[] = $row;
			}
		}
		$this->db->free_result($this->query);

		return $array;
	}

    /**
     * ������һ�����ݱ�
     *
     * @param  string $old_table �ɵı���
     * @param  string $new_table �µı���
     * @return bool              �޸ĳɹ�����TRUE�����򷵻�FALSE
     *
     * @since nv50
     */
    public function renameTable($old_table, $new_table)
	{
	    $this->_sql = "RENAME TABLE {$old_table} TO {$new_table}";
		return (bool)$this->exec();
	}

    /**
     * ɾ��һ�����ݱ�
     *
     * @param  string $table_name Ҫɾ���ı�����
     * @param  bool   $ifExists   ѡ��ָ���ı�������
     * @return bool               ɾ���ɹ�����TRUE�����򷵻�FALSE
     *
     * @since nv50
     */
    public function dropTable($table_name, $if_exists = true)
	{
	    $this->_sql = 'DROP TABLE ' . ($if_exists ? 'IF EXISTS ' : '') . $this->quoteName($table_name);
		return (bool)$this->exec();
	}

    /**
     * ��ȡ���ݱ��б�
     *
     * @since nv50
     */
	public function getTableList( $return = false)
	{
        $this->_sql = 'SHOW TABLES ';
        
        if ( $return )
        {
            return $this;
        }
        
		return $this->exec()->fetch($query);
	}

    /**
     * ����һ����
     *
     * @param  string $table_name Ҫ���������ݱ���
     * @return bool               �����ɹ�����TRUE�����򷵻�FALSE
     *
     * @since  1.0
     */
	public function lockTable($table_name)
	{
	    $this->_sql = 'LOCK TABLES ' . $this->quoteName($table_name) . ' WRITE';
		return (bool)$this->exec();
	}

    /**
     * �������ݿ��еı�
     * 
     * @return bool �����ɹ�����TRUE�����򷵻�FALSE
     * @since  1.0
     */
	public function unlockTables()
	{
		$this->_sql = 'UNLOCK TABLES';
		return (bool)$this->exec();
	}

    /**
     * �滻SQLǰ׺
     *
     * @param  string $sql    Ҫ�滻��SQL���
     * @param  string $prefix Ҫ�滻��ǰ׺
     * @return string         �滻�������SQL���
     *
     * @since  1.0
     */
    public function replacePrefix($sql, $prefix = '#__')
    {
        return str_replace($prefix, $this->_tblprefix, $sql);
    }

    /**
     * ��ȫ���һ����
     *
     * ��ʼ����ṹ�磺����ID�ỹԭΪ1�ȵȣ��ú�����DELETE FROM�����Ǹ÷�����������ű���˵
     * ��DELETE FROM�죬��ûDELETE FROM��DELETE FROM����ı��ֽṹ״̬����TRUNCATE TABLE
     * ��ᣬ������ο�MYSQL��TRUNCATE TABLE��䡣
     * @param  string $table_name ���ݱ�����
     * @return bool               ��ճɹ�����TRUE�����򷵻�FALSE
     *
     * @see   http://dev.mysql.com/doc/refman/5.1/zh/sql-syntax.html#truncate
     * @since nv50
     */
    public function truncateTable($table_name)
    {
        $this->_sql = 'TRUNCATE TABLE ' . $this->escape($table_name);
		return (bool)$this->exec();
    }

    /**
     * �������ݱ�����
     *
     * @param  string $table_name Ҫ���˵����ݱ���
     * @return string             ���˺�����ݱ���
     *
     * @since  1.0
     */
    public function quoteName( $table_name )
    {
        return $this->replacePrefix($this->escape($table_name));
    }
    
    /**
     * ���õ�ǰ���Խӿ�
     */
    public function setter( $name, $value )
    {
        if ( isset($this->$name) )
        {
            $this->$name = $value;
        }        
    }
    
    /**
     * ��ȡ��ǰ���Խӿ�
     */
    public function getter( $name )
    {
        return $this->$name;
    }

    /**
     * ��ֹ�ⲿ��������
     *
     * @param  string $name  ��������
     * @param  string $value ����ֵ
     *
     * @since  1.0
     */
	public function __set( $name, $value )
	{
	    $this->$name = NULL;
	}

    /**
     * �������ֵ
     *
     * @since nv50
     */
    public function clear( $varname = '' )
    {
        if ( $varname )
        {
            if ( property_exists($this, $varname) )
            {
                $this->$varname = '';
            }
            else if ( property_exists($this->db, $varname) )
            {
            	$this->db->$varname = '';
            }
        }
        else
        {
        	$this->_sql = '';
            $this->_type = '';
            $this->db->like = false;
            $this->debug = false;
        }
        
        return $this;
    }

    /**
     * ���õ��Կ���
     **/
    public function setDebug()
    {
        $this->debug = true;
        return $this;
    }
    
    /**
     * �����ѯ����
     * 
     * @since      nv50
     * @deprecated ׼����������self::clear('query')����
     */
    public function clearQuery()
    {
        if ( is_resource($this->query) )
        {
            $this->query = NULL;
        }
    }

    public function __construct( array $config )
    {
        extract($config); 
        isset($flag) || $flag = true;
        isset($config['tblprefix']) && $this->_tblprefix = $config['tblprefix'];
        
        isset($drivers) && $this->dervers = $drivers;
        $driversClass = 'cls_' . strtolower($this->dervers);
        $this->db = new $driversClass();
        $this->db->connect( $dbhost, $dbuser, $dbpw, $dbname, $pconnect, $flag, $dbcharset );
    }
    
    public function __clone()
    {
        $this->__construct( _08_factory::getDBOConfig() );
    }
    
    public function __call($name, $argv)
    {
        if ( method_exists($this->db, $name) )
        {
            if ( empty($argv) )
            {
                return call_user_func(array($this->db, $name));
            }
            else
            {
            	return  call_user_func_array(array($this->db, $name), $argv);
            }
        }
    }
    
    public function __get($name)
    {
        if ( property_exists($this->db, $name) )
        {
            return $this->db->$name;
        }
        else
        {
        	return null;
        }
    }
}