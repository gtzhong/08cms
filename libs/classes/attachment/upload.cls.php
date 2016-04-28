<?php
defined('M_COM') || exit('No Permission');
class cls_upload
{    
    /**
     * δ֪����
     * 
     * @var int
     */
    const ERROR_UNKNOW = -1;
    
    /**
     * �����ϴ�����Ϊ�ա�
     * 
     * @var int
     */
    const ERROR_PROGRAM_NOT_FOUND = 1;
    
    /**
     * �ϴ��ļ����Ͳ��ԡ�
     * 
     * @var int
     */
    const ERROR_FILE_TYPE = 2;
    
    /**
     * �������ļ����ʹ�С���ơ�
     * 
     * @var int
     */
    const ERROR_FILE_SIZE_LIMIT = 3;
    
    /**
     * Ӳ�̿ռ䲻����
     * 
     * @var int
     */
    const ERROR_DISK_SPACE_LIMIT = 4;
    
    /**
     * �ļ���Ч��
     * 
     * @var int
     */
    const ERROR_FILE_INVALID = 5;
    
    /**
     * ������Ҫ�ϴ����ļ���
     * 
     * @var int
     */
    const ERROR_FILE_NOT_FOUND = 6;
    
    /**
     * ����ϵͳ����Ŀռ��С
     * 
     * @var int
     */
    const ERROR_USER_SPACE_LIMIT = 7;
    
    /**
     * �ϴ����ļ������� php.ini �� upload_max_filesize ѡ�����Ƶ�ֵ��
     * 
     * @var int
     */
    const ERROR_INI_SIZE_LIMIT = 8;
    
    /**
     * �ļ�ֻ�в��ֱ��ϴ���
     * 
     * @var int
     */
    const ERROR_PARTIAL = 9;
    
    /**
     * �ϴ��ļ��Ĵ�С������ HTML ���� MAX_FILE_SIZE ѡ��ָ����ֵ��
     * 
     * @var int
     */
    const ERROR_FORM_SIZE_LIMIT = 10;
    
    /**
     * �Ҳ�����ʱ�ļ��С�
     * 
     * @var int
     */
    const ERROR_NO_TMP_DIR = 11;
    
    /**
     * �ļ�д��ʧ�ܡ�
     * 
     * @var int
     */
    const ERROR_CANT_WRITE = 12;
    
    /**
     * Զ�̷���Ϊ�ա�
     */
    const ERROR_REMOTE_PROGRAM_NOT_FOUND = 13;
    
    /**
     * �벻Ҫ�ϴ��к����ļ���
     */
    const ERROR_BAD_FILE = 14;
    
	var $current_dir = '';//��ָ�����ϴ�����·��,����ͨ���ļ����������ϴ��ļ�,��ʽΪ/xxx/
	var $ufids = array();//��¼�ϴ����ļ�id
	var $upload_size = 0;//��¼�ϴ�����ͼ���ļ���С(K)
	var $capacity;//��Ա�ϴ��ռ�����(K),-1Ϊ����
    protected static $_file = null;
    protected static $_Instance = NULL;			# ����ģʽ
    
    /**
     * �ϴ���Դ����
     * 
     * @var   array
     * @since nv50
     */
    private $result = array();
    
    /**
     * �ϴ����ͣ�ֵĿǰ֧�� upload��base64
     * 
     * @var   string
     * @since nv50
     */
    private $type;
    
    /**
     * Ҫ�ϴ����ļ�����
     * 
     * @var   string
     * @since nv50
     */
    private $file_type = 'image';
    
    private $lfile_types = array();
    
    /**
     * �����ϴ���������
     * 
     * @var   array
     * @since nv50
     */
    private $localfile = array();
    
    /**
     * ������Ϣ
     * 
     * @var   array
     * @since nv50
     */
    private $configs = array();
    
    /**
     * ��ǰ�û�������
     * 
     * @var   object
     * @since nv50
     */
    private $curuser = null;
	
	final public static function OneInstance(){
        if(!(self::$_Instance instanceof self)){
			self::$_Instance = new self();
		}
		return self::$_Instance;
	}	
	
	public function __construct()
    {
		$this->init();
	}
    
	public function init()
    {
		$this->curuser = cls_UserMain::CurUser();
		$this->current_dir = '';
		$this->ufids = array();
		$this->upload_size = 0;
		$this->capacity = $this->curuser->upload_capacity();
        self::$_file = _08_FilesystemFile::getInstance();
        $this->result = array();
        $this->type = 'upload';
        $this->configs = $this->lfile_types = array();
	}
    
    /**
     * �����ļ��ϴ�
     * 
     * @param  string $localname ���ļ�������
     * @param  string $file_type �ļ�����
     * @param  int    $configs   �������飬Ŀǰ���У�ˮӡ����ID���Զ�ѹ�����
     * @return array             �����ϴ����״̬����
     * 
     * @since  nv50
     */
	public function local_upload($localname,$file_type='image', $configs = array())
    {
		$uploadfile = array();
		$file_saved = false;
        $this->result['error'] = 0;
        $this->file_type = $file_type;
		$wmid = (is_array($configs) && isset($configs['wmid'])) ? (int) $configs['wmid'] : (int) $configs;
        
        if ( !$this->checkProgram() )
        {
            return $this->result;
        }
        
        $method = $this->type;
        $uploadfile = $this->$method($localname);
        
        if ( empty($uploadfile) )
        {
            if ( empty($this->result['error']) )
            {
                $this->result['error'] = self::ERROR_FILE_NOT_FOUND; //'������Ҫ�ϴ����ļ���!'
            }
            
            return $this->result;
        }
        
		$uploadfile['mid'] = $this->curuser->info['mid'];
		$uploadfile['mname'] = $this->curuser->info['mname'];
        
		if( in_array($this->result['extension'], array('jpg','jpeg','gif','png','swf','bmp')) )
        {
            # ��ȡ�ļ��Ŀ����߶�
            $infos = @getimagesize($uploadfile['target']);
			if( isset($infos[0]) && isset($infos[1]) )
            {
				$this->result['width'] = $infos[0];
				$this->result['height'] = $infos[1];
			}
            else 
            {
				self::$_file->delFile($uploadfile['target']);
				$this->result['error'] = self::ERROR_FILE_INVALID;//'��Ч��ͼƬ�ϴ�!'
				return $this->result;
            }
            
            # �Զ�ѹ��ͼƬ��С
			$auto_compression_width = (isset($configs['auto_compression_width']) && intval($configs['auto_compression_width'])) ? (int) $configs['auto_compression_width'] : 0;
            $this->auto_compression($auto_compression_width,$uploadfile['target']);
            
            # ��ͼƬ��ˮӡ
			if( in_array($this->result['extension'], array('jpg', 'jpeg', 'gif', 'png', 'bmp')) && 
                $this->image_watermark($uploadfile['target'], $wmid) )
            {
				$this->result['size'] = filesize($uploadfile['target']);
			}
		}
        
        # �ƶ��ļ���FTP
		if(cls_url::is_remote_atm($uploadfile['url']))
        {
			include_once M_ROOT."include/ftp.fun.php";
			ftp_upload($uploadfile['target'],$uploadfile['url']);
		}
        
        # ���»�Ա����Ŀռ�ֵ
		$this->upload_size += ceil($this->result['size'] / 1024);
		if($this->capacity != -1)
        {
			$this->capacity -= ceil($this->result['size'] / 1024);
			$this->capacity = max(0,$this->capacity);
		}
		$this->result['remote'] = $uploadfile['url'];
        $insertData = array('filename' => $uploadfile['filename'], 'url' => $uploadfile['url'], 'type' => $this->file_type, 
                            'createdate' => TIMESTAMP, 'mid' => $uploadfile['mid'], 'mname' => $uploadfile['mname'], 
                            'size' => $this->result['size']);
        $db = _08_factory::getDBO();
        $db->insert( '#__userfiles', $insertData )->exec();
		if($ufid = $db->insert_id()) $this->ufids[] = $ufid;
		$this->result['ufid'] = $ufid;
		unset($uploadfile);
		return $this->result;
	}
	 /**
     * �Զ�ѹ��ͼƬ�ߴ�
     * @param  int $auto_compression_width ѹ���ߴ�(����ͬʱ����)
     * @param  string $target  ͼƬ��ַ
     * @since  nv50
     */
	protected function auto_compression($auto_compression_width,$target){
		if ($auto_compression_width && in_array($this->file_type, array('image', 'images'), true) && 
               ($auto_compression_width < $this->result['width'] || $auto_compression_width < $this->result['height']))
            {
				if($this->result['width']>$this->result['height']){
					$auto_compression_height = ceil($auto_compression_width * $this->result['height'] / $this->result['width']);                    
					$this->image_resize($target, $auto_compression_width, $auto_compression_height, $target);
					$this->result['width'] = $auto_compression_width;
					$this->result['height'] = $auto_compression_height;  
					$this->result['size'] = filesize($target);   
				}else{
					$auto_compression_height = ceil($auto_compression_width * $this->result['width'] / $this->result['height']);
					$this->image_resize($target, $auto_compression_height, $auto_compression_width, $target);
					$this->result['width'] = $auto_compression_height;
					$this->result['height'] = $auto_compression_width;  
					$this->result['size'] = filesize($target);
				}  
            }
	}
    
    /**
     * ʹ�� $_FILES �ķ�ʽ�ϴ��ļ�
     */
    public function upload($localname)
    {
		if(empty($_FILES[$localname]) || !mis_uploaded_file($_FILES[$localname]['tmp_name']) || 
           !$_FILES[$localname]['tmp_name'] || !$_FILES[$localname]['name'] || $_FILES[$localname]['tmp_name'] == 'none')
        {
			$this->result['error'] = self::ERROR_FILE_NOT_FOUND;//'Ҫ�ϴ����ļ�������!'
			return false;
		}
        
        $uploadfile = $_FILES[$localname];
        if ( !empty($uploadfile['error']) )
        {
            $this->setErrorCode($uploadfile['error']);
			return false;
        }
        
		$this->result['extension'] = strtolower(mextension($uploadfile['name']));
        $this->result['original'] = $uploadfile['name'];
        $this->result['size'] = $uploadfile['size'];
        
        // �ж��Ƿ񳬹��û�����Ŀռ��С�����ϴ���������Ĵ�С����չ��
        if ( $this->checkUserSpaceSizeLimit($uploadfile['size']) || !$this->checkExt() || !$this->checkSize() )
        {
            @unlink($uploadfile['tmp_name']);
            return false;
        }
        
        $uploadfile['filename'] = $this->getFileName( addslashes($uploadfile['name']) );
		$uploadpath = $this->upload_path($this->file_type);
		$uploadfile['url'] = $uploadpath.$uploadfile['filename'];
		$uploadfile['target'] = M_ROOT.$uploadpath.$uploadfile['filename'];
		@chmod($uploadfile['target'], 0644);
        
        if ( false === @move_uploaded_file($uploadfile['tmp_name'], $uploadfile['target']) )
        {
            @copy($uploadfile['tmp_name'], $uploadfile['target']);
            @unlink($uploadfile['tmp_name']);
        }
        
        return $uploadfile;
    }
    
    /**
     * ʹ�� BASE64 �ķ�ʽ�ϴ��ļ�
     */
    public function base64($localname)
    {
        $uploadfile = array();
        $post = cls_env::_POST($localname);
        if ( !empty($post[$localname]) )
        {
            $localfile = $post[$localname];
            $needle = 'base64,';
            if ( false !== strpos($localfile, $needle) )
            {
                $localfile = substr($localfile, strpos($localfile, $needle) + strlen($needle));
            }
            $localfile = base64_decode($localfile);
            if (preg_match('/(\$_POST|\$_GET|<\?php|<%.*?%>)/i', $localfile))
            {
    			$this->result['error'] = self::ERROR_BAD_FILE;// �к��ļ�
    			return false;
            }
            
            if ( isset($this->configs['oriName']) )
            {
                _08_FilesystemFile::filterFileParam($this->configs['oriName']);
                $this->result['original'] = trim($this->configs['oriName']);
                $this->result['extension'] = strtolower(mextension($this->result['original']));
            }
            else
            {
            	$this->result['original'] = $this->result['extension'] = '';
            }
            $this->result['size'] = strlen($localfile);            
        
            // �ж��Ƿ񳬹��û�����Ŀռ��С�����ϴ���������Ĵ�С����չ��
            if ( $this->checkUserSpaceSizeLimit($this->result['size']) || !$this->checkExt() || !$this->checkSize() )
            {
                return false;
            }
            
            $uploadfile['filename'] = $this->getFileName( addslashes($this->result['original']) );        
    		$uploadpath = $this->upload_path($this->file_type);
    		$uploadfile['url'] = $uploadpath.$uploadfile['filename'];
    		$uploadfile['target'] = M_ROOT.$uploadpath.$uploadfile['filename'];
            @chmod($uploadfile['target'], 0644);
            if ( self::$_file->_fopen($uploadfile['target'], 'wb') )
            {
                self::$_file->_fwrite($localfile);
                self::$_file->_fclose();
            }       
        }
        
        return $uploadfile;
    }
    
    /**
     * �����ϴ��������
     * 
     * @param int $code ״̬����
     * 
     * @since nv50
     */
    public function setErrorCode( $code )
    {
        $code = (int) $code;
        switch($code)
        {
            # �ϴ����ļ������� php.ini �� upload_max_filesize ѡ�����Ƶ�ֵ��
            case UPLOAD_ERR_INI_SIZE: $this->result['error'] = self::ERROR_INI_SIZE_LIMIT; break;
            
            # �ϴ��ļ��Ĵ�С������ HTML ���� MAX_FILE_SIZE ѡ��ָ����ֵ��
            case UPLOAD_ERR_FORM_SIZE: $this->result['error'] = self::ERROR_FORM_SIZE_LIMIT; break;
            
            # �ļ�ֻ�в��ֱ��ϴ���
            case UPLOAD_ERR_PARTIAL : $this->result['error'] = self::ERROR_PARTIAL; break;
            
            # û���ļ����ϴ���
            case UPLOAD_ERR_NO_FILE: $this->result['error'] = self::ERROR_FILE_NOT_FOUND; break;
            
            # �Ҳ�����ʱ�ļ��С�
            case UPLOAD_ERR_NO_TMP_DIR: $this->result['error'] = self::ERROR_NO_TMP_DIR; break;
            
            # �ļ�д��ʧ�ܡ�
            case UPLOAD_ERR_CANT_WRITE: $this->result['error'] = self::ERROR_CANT_WRITE; break;
            
            default: $this->result['error'] = self::ERROR_UNKNOW; break;
        }
    }
    
    /**
     * ����ļ���С
     * 
     * @return bool $status ������ͨ������TRUE�����򷵻�FALSE
     * 
     * @since  nv50
     */
    public function checkSize()
    {
        $status = true;
        
        if ( $this->result['size'] > disk_free_space(M_ROOT) )
        {
			$this->result['error'] = self::ERROR_DISK_SPACE_LIMIT;//'Ӳ�̿ռ䲻��!'
            $status = false;            
        }
        
        if( !empty($this->localfile[$this->result['extension']]['minisize']) && 
           ($this->result['size'] < 1024 * $this->localfile[$this->result['extension']]['minisize']) )
        {
			$this->result['error'] = self::ERROR_FILE_SIZE_LIMIT;//'�������ļ����ʹ�С����!'
            $status = false;
		}
        
		if( !empty($this->localfile[$this->result['extension']]['maxsize']) && 
            ($this->result['size'] > 1024 * $this->localfile[$this->result['extension']]['maxsize']) )
        {
			$this->result['error'] = self::ERROR_FILE_SIZE_LIMIT;//'�������ļ����ʹ�С����!'
            $status = false;
		}
        
        return $status;
    }

    /**
     * ����ϴ�����
     * 
     * @return bool ��������ʱ����TRUE�����򷵻�FALSE
     * 
     * @since  nv50
     */
    public function checkProgram()
    {
        $status = true;
        $this->localfile = cls_atm::getLocalFilesExts($this->file_type);
        if ( !empty($this->lfile_types) )
        {
            foreach ( (array) $this->lfile_types as $type ) 
            {
                if ( !isset($this->lfile_types[$this->file_type]) )
                {
                    $this->localfile = array_merge($this->localfile, cls_atm::getLocalFilesExts($type));
                }
            }
        }
        
        if ( empty($this->localfile) )
        {
            $status = false;
            # �ϴ�����������
            $this->result['error'] = self::ERROR_PROGRAM_NOT_FOUND;
        }
        
        return $status;
    }
    
    /**
     * ����ļ���չ
     */
    public function checkExt()
    {
        # ��ȡϵͳ���ã���վ�����������������ο������ϴ���������        
        $nouser_exts = cls_env::mconfig('nouser_exts');
        if(!in_array($this->result['extension'], array_keys($this->localfile))){//�ļ����Ͳ��ڱ����ϴ�������
			$this->result['error'] = self::ERROR_FILE_TYPE;//'��ֹ�ϴ��ļ�����!'
		}
        
        # ����ο������ϴ���������
		if (!empty($nouser_exts) && 
            empty($this->curuser->info['mid']) && 
            !in_array($this->result['extension'], explode(',',@$nouser_exts), true)
        ) {
			$this->result['error'] = self::ERROR_FILE_TYPE;//'��ֹ�ϴ��ļ�����!'
		}
        
        return empty($this->result['error']) ? true : false;
    }
    
    /**
     * ����û��ռ��С����
     * 
     * @param  int  $size �ļ���С
     * @return bool       ��������˴�С����TRUE�����򷵻�FALSE
     * 
     * @since  nv50
     */
    public function checkUserSpaceSizeLimit( $size )
    {
        if ( ($this->capacity != -1) && ($size > 1024 * $this->capacity) )
        {
            $this->result['error'] = self::ERROR_USER_SPACE_LIMIT;
            return true;
        }
			
        return false;
    }
    
    /**
     * ��ȡ��ǰ�ϴ�������Ϣ
     * 
     * @return string $error_message ����д���ʱ���ش�����Ϣ�����򷵻ؿ��ַ���
     * 
     * @since  nv50
     */
    public function getErrorMessage()
    {
        $error_message = '';
        $this->result['error'] = (empty($this->result['error']) ? 0 : (int) $this->result['error']);
        
        if ( in_array($this->result['error'], array(self::ERROR_FILE_SIZE_LIMIT, self::ERROR_USER_SPACE_LIMIT)) )
        {
            if ( !empty($this->localfile[$this->result['extension']]['maxsize']) && 
                 !empty($this->localfile[$this->result['extension']]['minisize']) )
            {
                $maxsize = $this->localfile[$this->result['extension']]['maxsize'];
                $minsize = $this->localfile[$this->result['extension']]['minisize'];
                $size_message = "����ķ�Χ�ǣ�{$minsize} - {$maxsize}KB";
            }
            else
            {
            	$size_message = '';
            }
        }
        
        if ( $this->result['error'] )
        {
            switch($this->result['error'])
            {
                case self::ERROR_PROGRAM_NOT_FOUND: $error_message = '�����ϴ�����Ϊ�գ������ں�̨ϵͳ���ã����������ϴ����������á�'; break;
                case self::ERROR_FILE_TYPE: $error_message = '�ϴ��ļ����Ͳ��ԡ�'; break;
                case self::ERROR_FILE_SIZE_LIMIT: $error_message = '�������ļ����ʹ�С���ơ�' . $size_message; break;
                case self::ERROR_DISK_SPACE_LIMIT: $error_message = 'Ӳ�̿ռ䲻����'; break;
                case self::ERROR_FILE_INVALID: $error_message = '�ļ���Ч��'; break;
                case self::ERROR_FILE_NOT_FOUND: $error_message = '������Ҫ�ϴ����ļ���'; break;
                case self::ERROR_USER_SPACE_LIMIT: $error_message = '����ϵͳ����Ŀռ��С��' . $size_message; break;
                case self::ERROR_INI_SIZE_LIMIT: $error_message = '�ϴ����ļ������� php.ini �� upload_max_filesize ѡ�����Ƶ�ֵ��'; break;
                case self::ERROR_PARTIAL: $error_message = '�ļ�ֻ�в��ֱ��ϴ���'; break;
                case self::ERROR_FORM_SIZE_LIMIT: $error_message = '�ϴ��ļ��Ĵ�С������ HTML ���� MAX_FILE_SIZE ѡ��ָ����ֵ��'; break;
                case self::ERROR_NO_TMP_DIR: $error_message = '�Ҳ�����ʱ�ļ��С�'; break;
                case self::ERROR_CANT_WRITE: $error_message = '�ļ�д��ʧ�ܡ�'; break;
                case self::ERROR_REMOTE_PROGRAM_NOT_FOUND: $error_message = 'Զ�̷���Ϊ�գ������ں�̨ϵͳ���ã���������Զ�����������á�'; break;
                case self::ERROR_BAD_FILE: $error_message = '�벻Ҫ�ϴ��к����ļ���'; break;
                default: $error_message = 'δ֪����'; break;
            }
        }
        return $error_message;
    }
    
    /**
     * ��ȡ�ϴ��ļ�����
     * 
     * @param  string $localfilename �����ļ���
     * @return string $filename      ����һ���µ��ļ�����
     * 
     * @since  nv50
     */
    public function getFileName( $localfilename = '' )
    {
        $localfilename = ($localfilename ? $localfilename : cls_env::GetLicense());
        $filename = (date('dHis').substr(md5($localfilename.microtime()),5,10).cls_string::Random(4,1) . '.' . $this->result['extension']);
    	$filename = preg_replace("/(php|phtml|php3|php4|jsp|exe|dll|asp|cer|asa|shtml|shtm|aspx|asax|cgi|fcgi|pl)(\.|$)/i", "_\\1\\2", $filename);
        
        return $filename;
    }
    
    /**
     * ��ȡԶ�̸�����Ŀǰֻ�����ڵ���Զ�̸���
     * 
     * @param  string $remotefile Զ�̸�����ַ
     * @param  int    $rpid       Զ�̷���ID
     * @param  int    $configs   �������飬Ŀǰ���У�ˮӡ����ID���Զ�ѹ�����
     * @return array              ���ػ�ȡ����Զ�̸����±��ػ���ַ��Ϣ���飬�������᷵�ش���ԭ������ַ����Ϣ����
     * 
     * @since  nv50
     */
	public function remote_upload($remotefile,$rpid = 0, $configs = array())
    {
		$curuser = cls_UserMain::CurUser();
		$this->result = array('remote' => $remotefile, 'error' => 0);
		if(!$this->capacity) return $this->result;
		$rprojects = cls_cache::Read('rprojects');
        $rpid = (int) $rpid;             
        $wmid = (is_array($configs) && isset($configs['wmid'])) ? (int) $configs['wmid'] : (int) $configs;
		 
		if(empty($rpid) || empty($rprojects[$rpid]['rmfiles']))
        {
            $this->result['error'] = self::ERROR_REMOTE_PROGRAM_NOT_FOUND;
            return $this->result;
        }
		if(cls_url::islocal($remotefile,1)) return $this->result;
		if(!empty($rprojects[$rpid]['excludes'])){
			foreach($rprojects[$rpid]['excludes'] as $k){
				if(in_str($k,$remotefile))
                {
                    $this->result['error'] = self::ERROR_REMOTE_PROGRAM_NOT_FOUND;
                    return $this->result;
                }
			}
		}
		$this->localfile = $rprojects[$rpid]['rmfiles'];
		$extension = strtolower(mextension($remotefile));
		if(in_array($extension,array_keys($this->localfile))){
			$rmfile = $this->localfile[$extension];
		}
        else
        {
            $this->result['error'] = self::ERROR_FILE_TYPE;
            return $this->result;
        }
		
		$uploadfile = array();
		$uploadfile['mid'] = $curuser->info['mid'];
		$uploadfile['mname'] = $curuser->info['mname'];
		$file_saved = false;
        $this->result['extension'] = $rmfile['extname'];
        $this->result['original'] = $remotefile;
		$uploadfile['filename'] = $this->getFileName($remotefile);
		$uploadpath = $this->upload_path($rmfile['ftype']);
		$uploadfile['url'] = $uploadpath.$uploadfile['filename'];
		$uploadfile['target'] = $target = M_ROOT.$uploadpath.$uploadfile['filename'];
		@chmod($target, 0644);

        include_once M_ROOT."include/http.cls.php";
		$m_http = new http;
		if($rprojects[$rpid]['timeout']) $m_http->timeout = $rprojects[$rpid]['timeout'];
		$file_saved = $m_http->savetofile($remotefile,$target,$rmfile['maxsize']);
		unset($m_http);
		
		if(!$file_saved){
			self::$_file->delFile($target);
            $this->result['error'] = self::ERROR_FILE_NOT_FOUND;
			return $this->result;
		}
		if(filesize($target) < $rmfile['minisize'] * 1024){
			self::$_file->delFile($target);
            $this->result['error'] = self::ERROR_FILE_SIZE_LIMIT;
			return $this->result;
		}
		$this->result['size'] = filesize($target);
		if(in_array($rmfile['extname'], array('jpg', 'jpeg', 'gif', 'png', 'swf', 'bmp'))){//ͼƬ����flash
			if(!$infos = @getimagesize($target)){
				self::$_file->delFile($target);
                $this->result['error'] = self::ERROR_FILE_INVALID;
				return $this->result;
			}
			
			$this->result['width'] = $uploadfile['width'] = @$infos[0];
			$this->result['height'] = $uploadfile['height'] = @$infos[1];
            
            # �Զ�ѹ��ͼƬ��С
			$auto_compression_width = (is_array($configs) && isset($configs['auto_compression_width']) && intval($configs['auto_compression_width'])) ? (int) $configs['auto_compression_width'] : 0;
			$this->auto_compression($auto_compression_width,$uploadfile['target']);
            
			if(in_array($rmfile['extname'], array('jpg', 'jpeg', 'gif', 'png', 'bmp'))){
				if($this->image_watermark($target, $wmid)) $this->result['size'] = filesize($target);
			}
		}
		if(cls_url::is_remote_atm($uploadfile['url'])){
			include_once M_ROOT."include/ftp.fun.php";
			ftp_upload($target,$uploadfile['url']);
		}
		$this->upload_size += ceil($this->result['size'] / 1024);
		if($this->capacity != -1){
			$this->capacity -= ceil($this->result['size'] / 1024);
			$this->capacity = max(0,$this->capacity);
		}
		$this->result['remote'] = $uploadfile['url'];
        
        $insertData = array('filename' => $uploadfile['filename'], 'url' => $uploadfile['url'], 'type' => $rmfile['ftype'], 
                            'createdate' => TIMESTAMP, 'mid' => $uploadfile['mid'], 'mname' => $uploadfile['mname'], 
                            'size' => $this->result['size']);
        $db = _08_factory::getDBO();
        $db->insert( '#__userfiles', $insertData )->exec();
		if($ufid = $db->insert_id()) $this->ufids[] = $ufid;
		unset($uploadfile);
		return $this->result;
	}
    
    /**
     * �ϴ�ѹ�����ļ�
     * 
     * @deprecated nv50
     */
	function zip_upload($localname,$type='image',$wmid = 0){
		global $memberid,$_FILES,$dir_userfile,$db,$tblprefix,$timestamp;
		include_once M_ROOT.'include/zip.cls.php';
		$curuser = cls_UserMain::CurUser();
		$uploadfile = $result = array();
		$file_saved = false;
		
		$localfiles = cls_cache::Read('localfiles');
		$localfile = $localfiles[$type];
        $result['error'] = 0;
		foreach($localfile as $k => $v){
			if(empty($v['islocal'])){
				unset($localfile[$k]);
			}
		}
		if(!$_FILES[$localname] || !mis_uploaded_file($_FILES[$localname]['tmp_name']) || !$_FILES[$localname]['tmp_name'] || !$_FILES[$localname]['name'] || $_FILES[$localname]['tmp_name'] == 'none'){
			$result['error'] = 1;//'�����ڵ��ϴ��ļ�!'
			return $result;
		}
		$uploadfile = $_FILES[$localname];
		$localfilename = addslashes($uploadfile['name']);
		$uploadfile['mid'] = $curuser->info['mid'];
		$uploadfile['mname'] = $curuser->info['mname'];
		$uploadpath = $this->upload_path($type);
		$fuploadpath = M_ROOT.$uploadpath;

		if(empty($localfile)){//�����ϴ�����Ϊ��
			@unlink($uploadfile['tmp_name']);
			$result['error'] = 1;
			return $result;
		}
		if($this->capacity != -1 && $uploadfile['size'] > 1024 * $this->capacity){//�����ռ�
			@unlink($uploadfile['tmp_name']);
			$result['error'] = 1;
			return $result;
		}
		$zip=new PHPZip($uploadfile['tmp_name']);
		$lst=$zip->filelist();
		$result['count'] = count($lst);
		$ret=array();
		$capacity=1024 * $this->capacity;
		$size=0;
		foreach($lst as $z){
			if($z['folder']){
				$result['count']--;
				continue;
			}
			$extension = strtolower(mextension($z['name']));
			if(!in_array($extension,array_keys($localfile))){//�ļ����Ͳ��ڱ����ϴ�������
				continue;
			}
			if(!empty($localfile[$extension]['minisize']) && ($z['size'] < 1024 * $localfile[$extension]['minisize'])){//'�������ļ����ʹ�С����!'
				continue;
			}
			if(!empty($localfile[$extension]['maxsize']) && ($z['size'] > 1024 * $localfile[$extension]['maxsize'])){//'�������ļ����ʹ�С����!'
				continue;
			}
			$size+=$z['size'];
			if($this->capacity != -1 && $size > $capacity)break;
			$ret[]=$z['index'];
		}
		if(empty($ret)){
			$result['error'] = -2;
			return $result;
		}
		$tzip="$fuploadpath{$memberid}_".cls_string::Random(6).'/';
		$lst=$zip->Extract($tzip,$ret);
		@unlink($uploadfile['tmp_name']);
		$ret=array();
		foreach($lst as $k => $v){
			if(substr($k,-1)=='/')continue;
			$uploadfile['filename'] = preg_replace("/(php|phtml|php3|php4|jsp|exe|dll|asp|cer|asa|shtml|shtm|aspx|asax|cgi|fcgi|pl)(\.|$)/i", "_\\1\\2", date('dHis').substr(md5($k.microtime()),5,10).cls_string::Random(4,1).'.'.$extension);
			$uploadfile['url'] = $uploadpath.$uploadfile['filename'];
			$target = $fuploadpath.$uploadfile['filename'];
			if(!rename($tzip.$k,$target))continue;
			$uploadfile['thumbed'] = 0;
			if(in_array($extension, array('jpg','jpeg','gif','png','swf','bmp'))){
				if(!$infos = @getimagesize($target)){
					self::$_file->delFile($target);
					continue;
				}
				if(isset($infos[0]) && isset($infos[1])){
					$result['width'] = $infos[0];
					$result['height'] = $infos[1];
				}
				if($this->image_watermark($target,$wmid)){
					$uploadfile['size'] = filesize($target);
				}
			}
			if(cls_url::is_remote_atm($uploadfile)){
				include_once M_ROOT."include/ftp.fun.php";
				ftp_upload($target,$uploadfile);
			}
			$this->upload_size += ceil($uploadfile['size'] / 1024);
			if($this->capacity != -1){
				$this->capacity -= ceil($uploadfile['size'] / 1024);
				$this->capacity = max(0,$this->capacity);
			}
			$db->query("INSERT INTO {$tblprefix}userfiles SET
					filename='$uploadfile[filename]',
					url='$uploadfile[url]',
					type='$type',
					createdate='$timestamp',
					mid='$uploadfile[mid]',
					mname='$uploadfile[mname]',
					size='$uploadfile[size]',
					thumbed='$uploadfile[thumbed]'");
			if($ufid = $db->insert_id()) $this->ufids[] = $ufid;
			$ret[] = $uploadfile['url'];
		}
		unset($uploadfile);
		clear_dir($tzip,1);
		$result['remote']=$ret;
		return $result;
	}
	function thumb_pick($string,$datatype='htmltext',$rpid=0){//ֻ�����Ѿ�stripslashes���ı���
		if(!$string) return '';
		$thumb = '';
		if(in_array($datatype,array('text','multitext','htmltext'))){
		/*	if(preg_match("/<img\b[^>]+\bsrc\s*=\s*(?:\"(.+?)\"|'(.+?)'|(.+?)(?:\s|\/?>))/is",$string,$matches)){*/
            # ��ȡ�༭�����һ��ͼƬ������ͼ
			if(preg_match("/<img.*src\s*=\s*[\"|']*([^'\"]+)[\"|'].*>/isU",$string,$matches)){
				$thumb = @"$matches[1]$matches[2]$matches[3]";
				$thumb = cls_url::tag2atm($thumb);
				if(!cls_url::islocal($thumb,1) && $rpid){
					$filearr = $this->remote_upload($thumb,$rpid);
					$thumb = $filearr['remote'];
				}
				if(isset($filearr['width'])){
					$thumb .= '#'.$filearr['width'].'#'.$filearr['height'];
				}elseif($infos = @getimagesize(cls_url::local_file($thumb))){
					$thumb .= '#'.$infos[0].'#'.$infos[1];
				}
			}
		}elseif($datatype == 'images'){
			$images = @unserialize($string);
			if(is_array($images)){
				if(empty($images)) return '';
				$image = $images[min(array_keys($images))];
				$image['remote'] = cls_url::tag2atm($image['remote']);
				if(!cls_url::islocal($image['remote'],1) && $rpid){
					$image = $this->remote_upload($image['remote'],$rpid);
				}
				$thumb = $image['remote'];
				isset($image['width']) && $thumb .= '#'.$image['width'].'#'.$image['height'];
			}
		}elseif($datatype == 'image'){
			$image = array_filter(explode('#',$string));
			$image[0] = cls_url::tag2atm($image[0]);
			if(!cls_url::islocal($image[0],1) && $rpid){
				$filearr = $this->remote_upload($image[0],$rpid);
				$image[0] = $filearr['remote'];
				if(isset($filearr['width'])){
					$image[1] = $filearr['width'];
					$image[2] = $filearr['height'];
				}
			}
			$thumb = $image[0];
			isset($image[1]) && $thumb .= '#'.$image[1].'#'.$image[2];
		}
		return cls_url::save_atmurl($thumb);
	}
	function remotefromstr($string,$rpid,$wmid = 0){
		//��Ƕ���ı��е�Զ�̸������ػ�
		if(!$this->capacity) return $string;
		$rprojects = cls_cache::Read('rprojects');
		if(empty($rpid) || empty($rprojects[$rpid]['rmfiles'])) return $string;
		if(!preg_match_all("/(href|src)\s*=\s*(\"(.+?)\"|'(.+?)'|(.+?)(\s|\/?>))/is",$string,$matches)){
			return $string;
		}
		$remoteurls = array_filter(array_merge($matches[3],$matches[4],$matches[5]));
		foreach($remoteurls as $k => $v){
			if(cls_url::islocal($v,1)){//�ų����ػ�ftp�ϵ�����
				unset($remoteurls[$k]);
			}elseif(!empty($rprojects[$rpid]['excludes'])){
				foreach($rprojects[$rpid]['excludes'] as $i){
					if(in_str($i,$v)){
						unset($remoteurls[$k]);
						break;
					}
				}
			}
		}
		$remoteurls = array_unique($remoteurls);
		$oldurls = $newurls = array();
		foreach($remoteurls as $oldurl){
			$filearr = $this->remote_upload($oldurl,$rpid,$wmid);
			$newurl = $filearr['remote'];
			if(strpos($newurl,':/') === false) $newurl = '<!cmsurl />'.$newurl;//����·����ͼƬҲҪ����<!cmsurl />�����ֱ�Ӵ����ݿ�ĸ����ǲ�һ���ġ�
			if($newurl != $oldurl){
				$oldurls[] = $oldurl;
				$newurls[] = $newurl;
			}
		}
		return str_replace($oldurls,$newurls,$string);
	}
	function upload_path($type){//��ʽ��userfiles/image/xxxx/
		global $dir_userfile,$path_userfile;
		$uploadpath = $dir_userfile.'/'.$type;
		if($this->current_dir){
			$uploadpath .= $this->current_dir;
		}else{
			if(empty($path_userfile)){
				$uploadpath .= '/';
			}elseif($path_userfile == 'month'){
				$uploadpath .= '/'.date('Ym').'/';
			}elseif($path_userfile == 'day'){
				$uploadpath .= '/'.date('Ymd').'/';
			}
		}
		mmkdir(M_ROOT.$uploadpath);
		return $uploadpath;
	}
	function saveuptotal($updatedb=0){//�������̽�������һ���Եĸ����û��ϴ���
		$curuser = cls_UserMain::CurUser();
		if($this->upload_size) $curuser->updateuptotal($this->upload_size);
		$updatedb && $curuser->updatedb();
	}
	
    /**
	 * ����ָ����С������ͼ
	 *
	 * @param  string  $target    ͼƬ��Ե�ַ�����ݿ�洢��ַ��
	 * @param  int  $to_w    ָ������ͼ���
	 * @param  int  $to_h    ָ������ͼ�߶�
     * @param  int $tofile   ����ͼ����ʾurl
	 * @param  int  $cutall    ����ͼ���ɷ�ʽ��0��Ѽ��ã�1����ȫͼ
	 * @param	int  $padding  ���׷�ʽ 1 ����  0 ������
	 */
	function image_resize($target = '',$to_w,$to_h,$tofile = '',$cutall = 1,$padding=0){
		$tofile = !$tofile ? cls_url::thumb_local($target,$to_w,$to_h) : $tofile;
		mmkdir($tofile,0,1);
		$info = @getimagesize($target);
		$info_mime = $info['mime'];
		$thumbed = false;
		if(in_array($info_mime, array('image/jpeg','image/gif','image/png'))){
			$from_w = $info[0];
			$from_h = $info[1];
			$fto_w = $to_w;
			$fto_h = $to_h;
			$isanimated = 0;
			if($info['mime'] == 'image/gif'){
				$fp = fopen($target, 'rb');
				$im = fread($fp, filesize($target));
				fclose($fp);
				$isanimated = strpos($im,'NETSCAPE2.0') === FALSE ? 0 : 1;
			}
			// �ж��Ƿ����ڴ�ľ�
			$mmem = ini_get('memory_limit');
			if(strpos($mmem,'M')){
				$mmem = str_replace('M','',$mmem) * 1024 * 1024;	
			}elseif(strpos($mmem,'K')){
				$mmem = str_replace('K','',$mmem) * 1024;	
			}elseif(strpos($mmem,'G')){ //�в���?
				$mmem = str_replace('G','',$mmem) * 1024 * 1024 * 1024;			
			}else{ //�в���?
				$mmem = intval($mmem); //$mmem = str_replace('?','',ini_get('memory_limit'));			
			}
			// ($from_w * $from_h * 5) > //�ǹٷ��ҵ����㷨���Լ����Ժͱ��˵ľ���������ܽӽ���
			// ����0.75���غ��㷨�д��پ�׼...
			if($from_w * $from_h * 5 > $mmem * 0.75){
				@copy(M_ROOT.'images/common/error_thumb.jpg',$tofile);
				return; //adminlog('����Ա����','��Ա�б�������'); echo '<br>save-log<br>';
			}
			if(!$isanimated){
				switch($info['mime']) {
					case 'image/jpeg':
						$im = imagecreatefromjpeg($target);
						break;
					case 'image/gif':
						$im = imagecreatefromgif($target);
						break;
					case 'image/png':
						$im = imagecreatefrompng($target);
						break;
				}			
		if($cutall==2){
		    if($padding){
				if($to_w>$from_w && $to_h>$from_h){
						$m_x = round(($to_w - $from_w)/2);
						$m_y = round(($to_h - $from_h)/2);
						$cut_x = 0;
						$cut_y = 0;
						$fto_w = $from_w;
						$fto_h = $from_h;
						$cut_w = $from_w;
						$cut_h = $from_h;	
					}else{
						$to_radio = $to_w/$to_h;
						$from_radio = $from_w/$from_h;
						if($to_radio>$from_radio){
							$temp_h = $to_h;
							$temp_w = $to_h * $from_radio; 
						}else{
							$temp_w = $to_w;
							$temp_h = $to_w / $from_radio;
						}
						$m_x = round(($to_w - $temp_w)/2);
						$m_y = round(($to_h - $temp_h)/2);
						$cut_x = 0;
						$cut_y = 0;
						$fto_w = $temp_w;
						$fto_h = $temp_h;
						$cut_w = $from_w;
						$cut_h = $from_h;
					}
			}else{
				$m_x = 0;
				$m_y = 0;
				$cut_x = 0;
				$cut_y = 0;
				$fto_w = $to_w;
				$fto_h = $to_h;
				$cut_w = $from_w;
				$cut_h = $from_h;
			}		
						
		}elseif($cutall==1){//��ѻ��ü�
       
			if($padding){//����             
             	if($from_w <= $to_w && $from_h <= $to_h){ //ԭͼ������ͼ��С
					$m_x = round(($to_w - $from_w)/2);
					$m_y = round(($to_h - $from_h)/2);
					$cut_x = 0;
					$cut_y = 0;
					$fto_w = $from_w;
					$fto_h = $from_h;
					$cut_w = $from_w;
					$cut_h = $from_h; 	
				}else{
					if($from_w>=$to_w && $from_h>=$to_h){ //ԭͼ������ͼ����Ҫ��   
                    $to_radio = $to_w/$to_h;
					$from_radio = $from_w/$from_h;                                        
					if($to_radio<$from_radio){
						$temp_h = $from_h;
						$temp_w = round($temp_h * $to_radio); 
					}else{
						$temp_w = $from_w;
						$temp_h = round($temp_w / $to_radio);
					}   
                        $m_x = 0;
                        $m_y = 0;
                        if($from_radio>1){          //��ȴ��ڸ߶ȴ����Ͻǲ�
                            $cut_x = ($from_w-$temp_w)/2;
                            $cut_y = ($from_h-$temp_h)/2; 
                        }else{                      //���С�ڸ߶ȴ��м�
                            $cut_x = $cut_y = 0;
                        }                      								
						$fto_w = $to_w;
						$fto_h = $to_h;
						$cut_w = $temp_w;
						$cut_h = $temp_h;
                          
					}elseif($from_w <= $to_w ){//����ͼ��ȴ���ԭͼ
					    $m_x = ($to_w-$from_w)/2;
                        $m_y = 0;
                        $cut_x = 0;
                        $cut_y = ($from_h-$to_h)/2;                        								
						$fto_w = $from_w;
						$fto_h = $to_h;
						$cut_w = $from_w;
						$cut_h = $to_h;
					}elseif($from_h <= $to_h){//����ͼ�߶ȴ���ԭͼ
                        $m_x = 0;
                        $m_y = ($to_h-$from_h)/2;
                        $cut_x = ($from_w-$to_w)/2;
                        $cut_y = 0;                        								
						$fto_w = $to_w;
						$fto_h = $from_h;
						$cut_w = $to_w;
						$cut_h = $from_h;
					}
				}             
            
			}else{//������
				$m_x = 0;
				$m_y = 0;
				$cut_x = 0;
				$cut_y = 0;
				$fto_w = $to_w;
				$fto_h = $to_h;
				$cut_w = $from_w;
				$cut_h = $from_h;
			}
		}
			
		if(function_exists("imagecreatetruecolor")){
			if($im_n = imagecreatetruecolor($to_w,$to_h)){
				$white = imagecolorallocate($im_n, 255, 255, 255);
				imagefill($im_n,0,0,$white); 
				imagecopyresampled($im_n,$im,$m_x,$m_y,$cut_x,$cut_y,$fto_w,$fto_h,$cut_w,$cut_h); 
			}elseif($im_n = imagecreate($to_w,$to_h)){
				$white = imagecolorallocate($im_n, 255, 255, 255);
				imagefill($im_n,0,0,$white);
				imagecopyresized($im_n,$im,$m_x,$m_y,$cut_x,$cut_y,$fto_w,$fto_h,$cut_w,$cut_h); 
			} 
		}else{ 
				$white = imagecolorallocate($im_n, 255, 255, 255);
				imagefill($im_n,0,0,$white);
				$im_n = imagecreate($to_w,$to_h); 
				imagecopyresized($im_n,$im,$m_x,$m_y,$cut_x,$cut_y,$fto_w,$fto_h,$cut_w,$cut_h); 
		}
				imagejpeg($im_n,$tofile);
				imagedestroy($im); 
				imagedestroy($im_n); 
				$thumbed = true;
		}
			if(!$thumbed) @copy($target,$tofile);
			return;
		}
	}
	
	function image_watermark($target,$wmid = 0){
		$watermarks = cls_cache::Read('watermarks');
		if(@empty($watermarks[$wmid])) return false;
		if(!$watermarks[$wmid]['Available']) return false; 
		$wmark = $watermarks[$wmid];
		extract($wmark);
		if($watermarktype != 2){
			$watermark_file = $watermarktype ? M_ROOT.'images/common/watermark.png' : M_ROOT.'images/common/watermark.gif';
			if(!is_file($watermark_file)) return false;
		}else{
			if(empty($waterfontfile)) return false;
			$watermark_font = M_ROOT.'images/common/'.$waterfontfile;
			if(!is_file($watermark_font)) return false;
		}
		$imageinfo = getimagesize($target);
		$watermarked = false;
		if(in_array($imageinfo['mime'], array('image/jpeg', 'image/gif', 'image/png')) && $watermarkminwidth < $imageinfo[0] && $watermarkminheight < $imageinfo[1]) {
			if($watermarkstatus) {
				$wmstatus_arr = array_filter(explode(',',$watermarkstatus));
				if($watermarktype!=2){
					$watermarkinfo	= getimagesize($watermark_file);
					$watermark_logo = $watermarktype ? imagecreatefrompng($watermark_file) : imagecreatefromgif($watermark_file);
					$logo_w		= $watermarkinfo[0];
					$logo_h		= $watermarkinfo[1];
					$img_w		= $imageinfo[0];
					$img_h		= $imageinfo[1];
					$wmwidth	= $img_w - $logo_w;
					$wmheight	= $img_h - $logo_h;
		
					$isanimated = 0;
					if($imageinfo['mime'] == 'image/gif') {
						$fp = fopen($target, 'rb');
						$imagebody = fread($fp, filesize($target));
						fclose($fp);
						$isanimated = strpos($imagebody, 'NETSCAPE2.0') === FALSE ? 0 : 1;
					}
					
					if(is_readable($watermark_file) && $wmwidth > 10 && $wmheight > 10 && !$isanimated) {
						switch($imageinfo['mime']) {
							case 'image/jpeg':
								$dst_photo = imagecreatefromjpeg($target);
								break;
							case 'image/gif':
								$dst_photo = imagecreatefromgif($target);
								break;
							case 'image/png':
								$dst_photo = imagecreatefrompng($target);
								break;
						}
						foreach($wmstatus_arr as $wmstatus){
							$xy = $this->wmlocation($wmstatus,$img_w,$img_h,$logo_w,$logo_h,$watermarkoffsetx,$watermarkoffsety);
							if($watermarktype) {
								imagecopy($dst_photo, $watermark_logo, $xy[0], $xy[1], 0, 0, $logo_w, $logo_h);
							} else {
								imagealphablending($watermark_logo, true);
								imagecopymerge($dst_photo, $watermark_logo, $xy[0], $xy[1], 0, 0, $logo_w, $logo_h, $watermarktrans);
							}
						}
		
						switch($imageinfo['mime']) {
							case 'image/jpeg':
								imagejpeg($dst_photo, $target, $watermarkquality);
								break;
							case 'image/gif':
								imagegif($dst_photo, $target);
								break;
							case 'image/png':
								imagepng($dst_photo, $target);
								break;
						}
						$watermarked = true;
					}
				}else{					
					switch($imageinfo['mime']) {
						case 'image/jpeg':
							$im = imagecreatefromjpeg($target);
							break;
						case 'image/gif':
							$im = imagecreatefromgif($target);
							break;
						case 'image/png':
							$im = imagecreatefrompng($target);
							break;
					}
					$watermarktext = cls_string::iconv(cls_env::getBaseIncConfigs('mcharset'),"UTF-8",$watermarktext);
					$ar = imagettfbbox($watermarkfontsize, $watermarkangle, $watermark_font, $watermarktext);
					$img_w		= $imageinfo[0];
					$img_h		= $imageinfo[1];
					$logo_h = max($ar[1], $ar[3]) - min($ar[5], $ar[7]);
					$logo_w = max($ar[2], $ar[4]) - min($ar[0], $ar[6]);
					$white = imagecolorallocate($im, 255,255,255);
					$r1   =   hexdec(substr($watermarkcolor,1,2));
					$g1   =   hexdec(substr($watermarkcolor,3,2));
					$b1   =   hexdec(substr($watermarkcolor,5,2)); 
					$color = imagecolorclosestalpha($im,$r1,$g1,$b1,20);
					foreach($wmstatus_arr as $wmstatus){
						$xy = $this->wmlocation($wmstatus,$img_w,$img_h,$logo_w,$logo_h,$watermarkoffsetx,$watermarkoffsety);
						$xy[1] += $logo_h;
						imagettftext($im, $watermarkfontsize,$watermarkangle,$xy[0], $xy[1], $color, $watermark_font, $watermarktext);
					}
					switch($imageinfo['mime']) {
						case 'image/jpeg':
							imagejpeg($im, $target);
							break;
						case 'image/gif':
							imagegif($im, $target);
							break;
						case 'image/png':
							imagepng($im, $target);
							break;
					}
					imagedestroy($im);
				}
			}
		}
		return $watermarked;
	}
	
	function wmlocation($status,$img_w,$img_h,$logo_w,$logo_h,$offsetx = 5,$offsety = 5){
		switch($status) {
			case 1:
				$x = $offsetx;
				$y = $offsety;
				break;
			case 2:
				$x = ($img_w - $logo_w) / 2;
				$y = $offsety;
				break;
			case 3:
				$x = $img_w - $logo_w - $offsetx;
				$y = $offsety;
				break;
			case 4:
				$x = $offsetx;
				$y = ($img_h - $logo_h) / 2;
				break;
			case 5:
				$x = ($img_w - $logo_w) / 2;
				$y = ($img_h - $logo_h) / 2;
				break;
			case 6:
				$x = $img_w - $logo_w - $offsetx;
				$y = ($img_h - $logo_h) / 2;
				break;
			case 7:
				$x = + $offsetx;
				$y = $img_h - $logo_h - $offsety;
				break;
			case 8:
				$x = ($img_w - $logo_w) / 2;
				$y = $img_h - $logo_h - $offsety;
				break;
			case 9:
				$x = $img_w - $logo_w - $offsetx;
				$y = $img_h - $logo_h - $offsety;
				break;
		}
		return array(@$x,@$y);
	}
	
    // paras:�ĵ�id; ��һ����ͼ�ֶ�ת��Ϊ��ͼ�ֶ�,�ϴ�ʱ,���뱣��ɶ���ĵ�,��Ҫ����url����,��ʽΪarray('aid'=>123,'url'=>'/path/fname.ext')
	function closure($clear = 0, $paras = 0, $table = 'archives'){
		global $db, $tblprefix, $m_cookie;
		$curuser = cls_UserMain::CurUser();
		$ckey = @$curuser->info['msid'] . '_upload';
		$ids = implode(',', $this->ufids);
		empty($m_cookie[$ckey]) || $ids = $m_cookie[$ckey] . ($ids ? ",$ids" : '');
		if($clear){
			//��ID��Ӧ����
			$tids = array(
					'archives' => 1,
					'farchives' => 2,
					'members' => 3,
					'marchives' => 4,
					'comments' => 16,
					'replys' => 17,
					'offers' => 18,
					'mcomments' => 32,
					'mreplys' => 33,
					'pushs' => 64,
			);
			$tid = $table && isset($tids[$table]) ? $tids[$table] : 0;
			//��ֹ�����޸�cookieע��MySQL
			if(preg_match('/^\d+(?:,\d+)*$/', $ids)){
				if($paras){//�������д���󣬽��ϴ��ĸ���������id���й���
					if(is_array($paras)){
					   $aid = $paras['aid'];
                       $url = $paras['url'];
                       if($pos=strpos($url,'#')) $url = substr($url,0,$pos);
					   if(strstr($url,"http://")) $url = cls_url::save_atmurl($url); 
                       $urlsql = " AND url = '$url' "; //echo $urlsql;
					}else{
					   $aid = $paras;
                       $urlsql = " ";
					}
                    $aid = intval($aid);
                    $tid && $db->query("UPDATE {$tblprefix}userfiles SET aid=$aid,tid=$tid WHERE aid=0 $urlsql AND ufid IN ($ids)", 'UNBUFFERED');
				}elseif($clear == 1){//�������ʧ�ܣ�ɾ�����β����йصĸ�������¼
					$query = $db->query("SELECT url,type FROM {$tblprefix}userfiles WHERE ufid IN ($ids) AND tid=0 AND aid=1");
					while($item = $db->fetch_array($query)){
						atm_delete($item['url'],$item['type']);
					}
					$db->query("DELETE FROM {$tblprefix}userfiles WHERE ufid IN ($ids) AND tid=0 AND aid=1", 'UNBUFFERED');
				}
			}
			msetcookie($ckey, '', -31536000);
		}else{//�����ϴ��ɹ��󣬽�����idд��cookie
			msetcookie($ckey, $ids, 31536000);
		}
	}
    
    public function setter( $name, $argc )
    {
        if ( property_exists($this, $name) )
        {
            $this->$name = $argc;
        }
    }
    
    /**
     * �����ϴ�����
     * 
     * @param string $type  ��ǰ�ϴ������ͣ�Ŀǰ�У�upload��base64
     * 
     * @since nv50
     */
    public function setType( $type )
    {
        $this->type = strtolower( (string) $type );
    }
    
    /**
     * ����������Ϣ
     * 
     * @param array $config Ҫ���õ�������Ϣ
     * 
     * @since nv50
     */
    public function setConfig( array $configs )
    {
        $this->configs = $configs;
    }
}