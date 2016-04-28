<?php
/**
 * �����̨�Ĳ鿴ԭʼ��ʶ,���ɼ�����ԭʼ��ʶ�б�
 *
 * @author    Wilson
 * @copyright Copyright (C) 2008 - 2013 08CMS, Inc. All rights reserved.
 */
class _08_btags extends cls_AdminHeader
{
    private $db = null;
    /**
     * ����ģ�ͽ�����
     *
     * @var object
     */
    private $build = null;

    /**
     * ԭʼ��ʶ��������
     *
     * @var array
     * @static
     */
    public static $bclasses = array(
    	'common' => 'ͨ����Ϣ',
    	'archives' => '�ĵ����',
    	'catalogs' => '��Ŀ���',
    	'farchives' => '�������',
        'pushs' => '�������',
    	'commus' => '�������',
    	'members' => '��Ա���',
    	'others' => '����',
    );

    /**
     * ԭʼ��ʶ������������
     *
     * @var array
     * @static
     */
    public static $datatypearr = array(
    	'text' => '�����ı�',
    	'multitext' => '�����ı�',
    	'htmltext' => 'Html�ı�',
    	'image' => '��ͼ',
    	'images' => 'ͼ��',
    	'flash' => 'Flash',
    	'flashs' => 'Flash��',
    	'media' => '��Ƶ',
    	'medias' => '��Ƶ��',
    	'file' => '��������',
    	'files' => '�������',
    	'select' => '����ѡ��',
    	'mselect' => '����ѡ��',
    	'cacc' => '��Ŀѡ��',
    	'date' => '����(ʱ���)',
    	'int' => '����',
    	'float' => 'С��',
    	'map' => '��ͼ',
    	'vote' => 'ͶƱ',
    	'texts' => '�ı���',
    );

    /**
     * �Զ�ά���ı�ʶ����
     * 
     * @var array
     */ 
    private $btagnames = array();
    
    /**
     * �ֶ�ά���ı�ʶ����
     * 
     * @var array
     */
    private $notautobtagnames = array();
    
    /**
     * �ֶ�ɾ���ı�ʶ����
     * 
     * @var array
     */
    private $del_btagnames = array();

    /**
     * ��ǰ�������ӷ���ID����
     * 
     * @var array
     */ 
    private $sclasses = array();
    
    private $url = '';

    private $sClass = 0;//��¼С����
	
    public function __construct()
    {
        global $db;
        parent::__construct('tpl');
        $this->setBclass();
        
        # ��ȡ�ֶ�����ı�ʶ����
        $this->notautobtagnames = cls_cache::getCacheClassVar('cac_btagnames');    
        
        # ��ȡ�ֶ�ɾ���ı�ʶ����
        $this->del_btagnames = cls_cache::getCacheClassVar('cac_btagnames_del');
        $this->db = $db;
        if(!empty($this->_params['textid']))
        {
            if(empty($this->_params['floatwin_id']) || $this->_params['floatwin_id'] == 'null') {
                $this->_params['floatwin_id'] = 'main';
            } else if(is_numeric($this->_params['floatwin_id'])) {
                $this->_params['floatwin_id'] = "_08winid_" . (int)$this->_params['floatwin_id'];
            } else {
                $this->_params['floatwin_id'] = $this->_params['floatwin_id'];                
            }
            $this->url = "&caretpos={$this->_params['caretpos']}&types={$this->_params['types']}&textid={$this->_params['textid']}&floatwin_id={$this->_params['floatwin_id']}";
            cls_phpToJavascript::insertParentWindowString($this->_params['floatwin_id'], $this->_params['textid'], '', 0, false);
        }
    }
    
    /**
     * ���õ�ǰ��������
     */ 
    public function setBclass()
    {        
        # ����δ���������Զ���������������
        if( empty($this->_params['bclass']) ) 
        {
            $this->_params['bclass'] = 'common';
        }
        else if( !array_key_exists($this->_params['bclass'], self::$bclasses) )
        {
            # �����ڸ��ϱ�ʶ����ʱ�����
            if( in_array($this->_params['bclass'], array('searchs', 'archive', 'acount')) ) // �ĵ�
            {
                $this->_params['bclass'] = 'archives';
                $this->_params['sclass'] < 0 && $this->_params['sclass'] = 0;
            }
            else if( in_array($this->_params['bclass'], array('member', 'msearchs', 'mcount')) ) // ��Ա
            {
                $this->_params['bclass'] = 'members';
            }
            else if( in_array($this->_params['bclass'], array('farchive',)) ) // ����
            {
                $this->_params['bclass'] = 'farchives';
            }
            else if( in_array($this->_params['bclass'], array('commu')) ) // ����
            {
                $this->_params['bclass'] = 'commus';
            }
            else if( in_array($this->_params['bclass'], array('mccatalogs', 'cnode', 'nownav', 'mcnode')) ) // ��Ŀ����ϵ
            {
                $this->_params['bclass'] = 'catalogs';
            }
            else 
            {
				if( in_array($this->_params['bclass'], array('vote', 'votes'))){//ͶƱ
                	$this->_params['sclass'] = 'vote';
				}
				else if( in_array($this->_params['bclass'], array('images', 'image', 'files', 'file', 'flashs', 'flash', 'medias', 'media')) ) // ����
				{
					$this->_params['sclass'] = 'attachment';
				}
				else if( in_array($this->_params['bclass'], array('keyword',)) ) // �ؼ���
				{
					$this->_params['sclass'] = 'keyword';
				}
 				else if( in_array($this->_params['bclass'], array('mcatalogs','mnownav')) ) //�ռ���Ŀ
				{
					$this->_params['sclass'] = 'mcatalogs';
				}
  				else if( in_array($this->_params['bclass'], array('texts',)) ) //�ı���
				{
					$this->_params['sclass'] = 'texts';
				}
              $this->_params['bclass'] = 'others';
				
            }
        }
        /**
         * ��Ӹ��ϱ�ʶ�����������Ĵ��ڷ���ID���ID��һ�£�
         * ���Զ�����������sclass ID
         */ 
        if(
            ($this->_params['bclass'] == 'farchives') && !empty($this->_params['textid']) && 
            !empty($this->_params['handlekey'])
        )
        {            
            $this->_params['sclass'] = cls_fcatalog::Config($this->_params['sclass'],'chid');
        } else if ($this->_params['bclass'] == 'catalogs' && !empty($this->_params['textid']))
        {            
            $this->_params['sclass'] = str_replace('co', '', @$this->_params['sclass']);
        }
    }

    public function init()
    {
    	$arr = array();
        # ��ʾ��ǰ������Ϣ
    	foreach(self::$bclasses as $k => $v)
        {
            $arr[] = ($this->_params['bclass'] == $k ? "<b>-$v-</b>" : "<a href=\"?entry=btags&bclass=$k{$this->url}\">$v</a>");
        }
    	echo tab_list($arr,count(self::$bclasses),0);

        # ���õ�ǰҳ����ӷ���ID
        $this->setSclasses();
        
        # ��ȡҪ�ڽ�����ʾ���ֶα�ʶ����
        $showdatas = $this->getShowDatas(); 
        # ����ʱ���˲�ƥ�����
        if( submitcheck('bbtagsearch') )
        {
            $this->getSearchValue($showdatas);
        }
        # ��ʼ��������
        $this->_build->table( $showdatas );
    }

    /**
     * ����Ĭ�Ϸ����ʶ���ÿ���ʵӦ�÷�ģ����
     * �����Զ���ʶ$btagnames
     */
    public function setSclasses()//������������븽�Ӵ���
    {
		$this->sClass = empty($this->_params['sclass']) ? 0 : $this->_params['sclass'];
        switch($this->_params['bclass'])
        {
            # �ĵ����
           case 'archives' :
                $this->setDatas('channels', 'archives');
          break;

            #��Ա���
            case 'members' :
                $this->setDatas('mchannels', 'members', 'mfields');
                $this->getCommonDatas('grouptypes');
            break;

            # �������
            case 'farchives' :
                $this->setDatas('fchannels', 'farchives', 'ffields');
            break;
            
            # �������
            case 'pushs' :
                # ���������������
                $this->setSclass('pushareas');
				if(empty($this->sClass)){
					$pushareas = cls_PushArea::Config();
					$keys = array_keys($pushareas);
					$this->sClass = $keys[0];
				}
				if($pusharea = cls_PushArea::Config($this->sClass)){
                    $fields = $this->db->getTableColumns('#__'.cls_PushArea::ContentTable($this->sClass), false);
                    cls_Array::setObjectDOM($fields, 'maintable', 1);
                    $this->setFields($fields, $this->sClass, '');
				}
            break;

            #�������
            case 'commus' :
                $this->setSclass('commus', 'cuid');
                $commus = cls_commu::Config();
				if(empty($this->sClass)){
					$keys = array_keys($commus);
					$this->sClass = $keys[0];
				}
				if($commu = cls_commu::Config($this->sClass)){
                    $fields = $this->db->getTableColumns("#__{$commu['tbl']}", false);
                    cls_Array::setObjectDOM($fields, 'maintable', 1);
                    $this->setFields($fields,$this->sClass, '');       
                }
            break;

            #��Ŀ���
            case 'catalogs' :
				$this->sClass = intval($this->sClass);
                $this->sclasses = array(
        			'catalogs' => '��Ŀ',
        		);
                $this->setSclass('cotypes');
				if(empty($this->sClass)){
					$fields = $this->db->getTableColumns("#__catalogs", false);
					cls_Array::setObjectDOM($fields, 'maintable', 1);
					$this->setFields($fields, 'catalogs', 'cnfields_0');
				}else{
                	$cotypes = cls_cache::Read('cotypes');
					if($cotype = @$cotypes[$this->sClass]){
						$fields = $this->db->getTableColumns("#__coclass{$this->sClass}", false);
						cls_Array::setObjectDOM($fields, 'maintable', 1);
						$this->setFields($fields, $this->sClass, 'cnfields_1');          
					
					}
				}
            break;

            #����
            case 'others' :
                $this->sclasses = array(
        			'mp' => '��ҳ',
        			'attachment' => '����',
                    'vote' => 'ͶƱ',
                    'keyword' => '�ؼ���',
                    'mcatalogs' => '�ռ���Ŀ',
                    'texts' => '�ı���',
       		);
	           $this->sClass || $this->sClass = 'mp';
            break;
            default : 
                $this->getCommonDatas();
            break;
        }
		$this->AutoTagSupplement();
    }
	
    /**
     * �Եõ����Զ���ʶ$btagnames���䴦��
     * ׷���������ƣ��û���ֵ
     */ 
    public function AutoTagSupplement(){
		$cnames = array();
        switch($this->_params['bclass']){
			case 'archives' ://��ϵ���ƣ��ϼ���Ŀ���Ƶ�
				$cnames['catalog'] = '������Ŀ����';
				$source = cls_cache::Read('cotypes');
				foreach($source as $k => $v){
					$cnames["ccid$k"] = "[{$v['cname']}]����ID";
					$cnames["ccid{$k}title"] = "[{$v['cname']}]�������";
					$cnames["ccid{$k}date"] = "[{$v['cname']}]���ൽ��";
				}
				$source = cls_cache::Read('abrels');
				foreach($source as $k => $v){
					if(!$v['tbl']){
						$cnames["pid$k"] = "[{$v['cname']}]�ϼ�ID";
						$cnames["inorder{$k}"] = "[{$v['cname']}]��������";
						$cnames["incheck{$k}"] = "[{$v['cname']}]�������";
					}
					
				}
			break;
			case 'members' ://��ϵ���ƣ��ϼ���Ŀ���Ƶ�
				$source = cls_cache::Read('grouptypes');
				foreach($source as $k => $v){
					$cnames["grouptype$k"] = "[{$v['cname']}]��ID";
					$cnames["grouptype{$k}date"] = "[{$v['cname']}]�鵽��";
				}
				$source = cls_cache::Read('mctypes');
				foreach($source as $k => $v){
					$cnames["mctid$k"] = $v['cname'];
				}
				$source = cls_cache::Read('abrels');
				foreach($source as $k => $v){
					if(!$v['tbl']){
						$cnames["pid$k"] = "[{$v['cname']}]�ϼ�ID";
						$cnames["inorder{$k}"] = "[{$v['cname']}]��������";
						$cnames["incheck{$k}"] = "[{$v['cname']}]�������";
					}
					
				}
				$source = cls_cache::Read('currencys');
				foreach($source as $k => $v){
					$cnames["currency$k"] = $v['cname'];
				}
			break;
		}
		foreach($this->btagnames as $k => $v){
			if(!$v['cname'] && isset($cnames[$v['ename']])) $v['cname'] = $cnames[$v['ename']];
			if(!$v['cname']) $v['cname'] = $v['ename'];
			$this->btagnames[$v['ename']] = $v;
			unset($this->btagnames[$k]);
		}
	}        
	
	
    
    /**
     * ��������
     * 
     * @param string $cache_name ��������
     * @param string $table_name ���ݱ�����
     */ 
    public function setDatas($cache_name, $table_name, $type_cache = 'fields')
    {        
        $this->setSclass($cache_name);
        $caches = cls_cache::Read($cache_name);
		
		if(empty($this->sClass)){
			$keys = array_keys($caches);
			$this->sClass = $keys[0];
		}
		
		if($cache = @$caches[$this->sClass]){
			# ��ȡ��������ṹ
			$maintable = ($table_name == 'archives' ? ($table_name . $cache['stid']) : $table_name);
			$fields1 = $this->db->getTableColumns('#__' . $maintable, false);
			# ���ṹ��������ֵ
			cls_Array::setObjectDOM($fields1, 'maintable', 1);
			$fields2 = $this->db->getTableColumns("#__{$table_name}_{$this->sClass}", false);
			cls_Array::setObjectDOM($fields2, 'maintable', 0);
			# �ϲ������븱��Ľṹ����
			$fields = array_merge($fields2, $fields1);
			# ���ݽṹ��������Ҫ��ʾ���ֶα�ʶ
			$this->setFields($fields, $this->sClass, $type_cache); 
		}
	}
		
    
    /**
     * ��ȡϵͳ���ã�ͨ����Ϣ�����ǻ�Ա�����Ʊ�ʶ��Ϣ
     * ����ͬ�ܹ�����ĳЩ�����Ա�ʶ(�Զ�)
     */ 
    public function getCommonDatas( $cache_name = 'tpl_fields' )
    {
        $btags = cls_cache::Read($cache_name);
        $enames = $cnames = $bclasses = '';
        $datatype = 'text';
        foreach($btags as $ename => $btag)
        {            
            switch($cache_name)
            {
                # ��Ա��
                case 'grouptypes' :
                    $enames = 'grouptype'.$ename.'name';
                    $cnames = $btag['cname'].'��Ա��';
                    $bclasses = 'member';
                break;
                # ͨ����Ϣ
                default :
                    $enames = 'user_'.$ename;
                    $cnames = $btag['cname'];
                    $bclasses = 'common';
                    isset($btag['type']) && $datatype = $btag['type'];
                    isset($btag['datatype']) && $datatype = $btag['datatype'];
                break;
            }
            // �����ֶ�ά�����ڵı�ʶ
                $this->btagnames[] = array(
                    'ename' => $enames,
                    'cname' => $cnames,
                    'bclass' => $bclasses,
                    'sclass' => 0,
                    'datatype' => $datatype,
                    'iscommon' => 0,
                    'maintable' => 1
                );
        }
    }

    /**
     * ���ñ�ʶ��Ϣ
     *
     * @param string $fields     ���ݱ��ֶ���Ϣ
     * @param int    $chid       ���ݱ�ģ��ID
     * @param bool   $maintable  �ñ�ʶ�Ƿ���������1Ϊ����0Ϊ����
     *
     * @since 1.0
     */
    public function setFields( $fields, $chid, $type_cache, $maintable = 1 )
    {
        # ��ȡ��ϵͳ����ӵı�ʶ�Զ�������
        if(false !== strpos($type_cache, 'cnfields'))
        {
            $parts = explode('_', $type_cache);
            $type_caches = cls_cache::Read($parts[0], isset($parts[1]) ? (int)$parts[1] : 0);
        }
        else
        {
            $type_caches = cls_cache::Read($type_cache, $chid);
        }
		
		
        foreach($fields as $ename => $field)
        {
            if(!is_object($field)) continue;
            $types = explode(' ', $field->Type);
            $type = preg_replace('/\(.*\)/', '', $types[0]);
            # ��ȡ�Զ�������
            if(array_key_exists($ename, $type_caches))
            {
                $type = $type_caches[$ename]['datatype'];
				$field->Comment || $field->Comment = $type_caches[$ename]['cname'];
				
            }
            else
            {
                self::getCustom($type);
#				$field->Comment || $field->Comment = $ename;
            }
            // �����ֶ�ά�����ڵı�ʶ
			  $this->btagnames[] = array(
				  'ename' => $ename,
				  'cname' => $field->Comment,
				  'bclass' => $this->_params['bclass'],
				  'sclass' => $chid,
				  'datatype' => $type,
				  'iscommon' => 0,
				  'maintable' => $field->maintable
			  );
        }
    }
    
    /**
     * ��ȡ�Զ������ͣ�Ŀǰֻ�������õñȽ϶��
     * 
     * @param  string $type ����ԭʼ���ݿ�����
     * @return string $type ���ر�ϵͳ�Զ������������
     * 
     * @since  1.0
     */ 
    public static function getCustom(&$type)
    {
        false !== stripos($type, 'double') && $type = 'float';
        false !== stripos($type, 'text') && $type = 'multitext';
        false !== stripos($type, 'int') && $type = 'int';
        false !== stripos($type, 'char') && $type = 'text';
        false !== stripos($type, 'time') && $type = 'date';
    }

    /**
     * ���õ����ӱ�ʶ����
     *
     * @param string $cache_name Ҫ��ȡ�Ļ�������
     * @param string $array_key  ���ֵΪ����ʱ���ø�ֵָ����ȡ���±�
     */
    public function setSclass($cache_name, $array_key = '')
    {
        $caches = cls_cache::Read($cache_name);
		foreach($caches as $k => $v)
        {
			$this->sclasses[empty($array_key) ? $k : $v[$array_key]] = $v['cname'];
		}
    }

    /**
     * ��ȡ�������ֵ
     *
     * @param array $datas ���б�ʶ��Ϣ
     */
    public function getSearchValue( array &$datas )
    {
        foreach($datas['showdatas'] as $k => $data)
        {
            $data = array_map('strip_tags', $data);
            if (
                # ����ʹ����ʽ
                !empty($this->_params['ename']) &&
                !in_str($this->_params['ename'], $data[0]) &&
                !in_str($this->_params['ename'], $data[1]) &&
                !in_str($this->_params['ename'], $data[2]) ||
                # ������ʶ����
                (!empty($this->_params['cname']) && !in_str($this->_params['cname'], $k))
            )
            {
                # �������������ʱ����˸���
                unset($datas['showdatas'][$k]);
            }
        }
    }

    /**
     * ��ȡҪչʾ��ԭʼ��ʶ����
     *
     * @return array $config ���ػ�ȡ��������
     * @since  1.0
     */
    public function getShowDatas()
    {
        empty($this->_params['ename']) && $this->_params['ename'] = '';
        empty($this->_params['cname']) && $this->_params['cname'] = '';
        # ��ȡselect��Ϣ
        if(empty($this->sclasses))
        {
            $select_str = '';
        }
        else
        {
            if(empty($this->_params['sclass']))
            {
                $keys = array_keys($this->sclasses);
                $this->_params['sclass'] = $keys[0];
            }
            $select_str = $this->_build->select(
                array(
                    'selectname' => 'sclass',
                    'selectdatas' => $this->sclasses,
                    'selectedkey' => (isset($this->_params['sclass']) ? $this->_params['sclass'] : 0),
                    'selectstr' => 'onchange="location.href=\'?entry=btags&bclass='.$this->_params['bclass'].$this->url.'&sclass=\' + this.value"'
                )
            );
        }

        # ��ȡ����
        $config = array(
            'title' => self::$bclasses[$this->_params['bclass']] .
<<<EOT
              >>&nbsp;&nbsp;&nbsp;&nbsp;{$select_str}
              ��ʶʹ����ʽ<input type="text" name="ename" value="{$this->_params['ename']}" class="txt" />
              ��ʶ����<input type="text" name="cname" value="{$this->_params['cname']}" class="txt" />
              <input class="btn" type="submit" name="bbtagsearch" value="����" />
EOT
            , 'tabletitle' => array('��ʶ����', '��ʽ1', '��ʽ2', '��ʽ3', '��������', '����')
            , 'showdatas' => array()
        );
        !empty($this->_params['textid']) && $config['title'] .= ' (�����ʽ����)';
        $this->getBtagnames($this->btagnames, $config );
       return $config;
    }
	
    /**
     * ��ȡҪ��ʾ��ԭʼ��ʶ��������
     *
     * @param array $btagnames ��ʶ��������
     * @param array $config    ��ŵ���ͼ�����������
     */
    public function getBtagnames( array $btagnames, &$config )
    {
		foreach($this->notautobtagnames as $k => $v){//ֻ�ϲ���ǰ����ı�ʶ
            if($v['bclass'] != $this->_params['bclass'] ) continue;
			if(empty($v['iscommon']) && $this->sClass != @$v['sclass']) continue;
			$btagnames[$k] = $v;
		}		
		
        foreach($btagnames as $key => $btagname)
        {
			$comArr = array('ename'=> $btagname['ename'], 'bclass'=> $btagname['bclass'], 'sclass'=> $btagname['sclass']);
			if(cls_Array::_in_array($comArr, $this->del_btagnames, true)) continue;
			
			if(!empty($this->_params['textid']))
			{   
				# ֻ�дӡ���ʶ��ģ�塱��������Ĵ��ڲų��ֲ���ԭʼ��ʶ����
				$ename1 = '<a href="javascript:obj.insertTagStr(\''.$this->_params['textid'].'\', \'{'.$btagname['ename'].'}\', '.(int)$this->_params['caretpos'].');">{<b>' . $btagname['ename'] . '</b>}</a>';
				$ename2 = '<a href="javascript:obj.insertTagStr(\''.$this->_params['textid'].'\', \'{$'.$btagname['ename'].'}\', '.(int)$this->_params['caretpos'].');">{$<b>' . $btagname['ename'] . '</b>}</a>';
				$ename3 = '<a href="javascript:obj.insertTagStr(\''.$this->_params['textid'].'\', \'{$v['.$btagname['ename'].']}\', '.(int)$this->_params['caretpos'].');">{$<b>v[' . $btagname['ename'] . ']</b>}</a>';
			}
			else 
			{
				# ����ֻ��ʾԭʼʹ����ʽ
				$ename1 = '{<b>' . $btagname['ename'] . '</b>}';
				$ename2 = '{$<b>' . $btagname['ename'] . '</b>}';
				$ename3 = '{$<b>v[' . $btagname['ename'] . ']</b>}';
			}
			$array = array(
				$ename1,
				$ename2,
				$ename3,
				@self::$datatypearr[$btagname['datatype']],
				empty($btagname['maintable']) ? '��' : '��'
			);
			empty($btagname['cname']) ? array_push($config['showdatas'], $array) : $config['showdatas'][$btagname['cname']] = $array;                
        }
    }
}