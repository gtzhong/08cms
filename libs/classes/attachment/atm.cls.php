<?php
/**
* �йظ�����ͼƬ�ȵĴ�����
* 
*/
class cls_atm{
	
	
	
	/**
	 * �������ݿ��ʽͼƬ������ָ����С������ͼ��������ʾurl
	 *
	 * @param  string  $dbstr    ͼƬ��Ե�ַ�����ݿ�洢��ַ��
	 * @param  int  $width    ָ������ͼ���
	 * @param  int  $height    ָ������ͼ�߶�
	 * @param  int  $mode    ����ͼ���ɷ�ʽ��0��Ѽ��ã�1����ȫͼ
	 * @param	int  $padding  ���׷�ʽ 1 ����  0 ������
	 * @return string  ����ͼ����ʾurl
	 */
	public static function thumb($dbstr,$width = 0,$height = 0,$mode = 0,$padding=0){
		//�������ݿ��ʽ��������ʾurl��$modeΪ0��Ѽ��ã�1����ȫͼ
		global $ftp_url;
		if(!($dbstr = str_replace(array('<!cmsurl />','<!ftpurl />'),'',$dbstr))) return '';
		if(!$width || !$height || !cls_url::islocal($dbstr,1)) return cls_url::tag2atm($dbstr);//Զ��ͼƬ����������ͼ
		
		$isftp = cls_url::is_remote_atm($dbstr);
		$sourcefile = cls_url::local_atm($dbstr,1);//��������·��
		$thumbfile = cls_url::thumb_local($sourcefile,$width,$height);//��������·��
		$thumblogfile = str_replace('.jpg','.log',$thumbfile);//���ڼ�¼fpt�Ƿ�����������ͼ�ı��ر���ļ�
		$thumbview = cls_url::tag2atm(str_replace(M_ROOT,'',$thumbfile));
		
		//�������ͼ�Ѿ����ڣ������ظ�����
		if(!file_exists($isftp ? $thumblogfile : $thumbfile)){
			if($isftp){
				include_once M_ROOT."include/http.cls.php";
				mmkdir($sourcefile,0,1);
				$m_http = new http;
				$m_http->savetofile($ftp_url.$dbstr,$sourcefile);
				unset($m_http);
			}
			$m_upload = cls_upload::OneInstance();
			$m_upload -> image_resize($sourcefile,$width,$height,$thumbfile,$mode,$padding);
			unset($m_upload);
			if($isftp){
				include_once M_ROOT."include/ftp.fun.php";
				$_ftp_re = ftp_upload($thumbfile,str_replace(M_ROOT,'',$thumbfile));
				$file = _08_FilesystemFile::getInstance();
				$file->delFile($sourcefile);
				$file->delFile($thumbfile);
				if($_ftp_re) @touch($thumblogfile);
			}
		}		
		return $thumbview;
	}
	
	/**
	 * ��ԭͼ�ı�������ͼƬ�Ĵ�С
	 *
	 * @param  int  $width    ԭͼ���
	 * @param  int  $height    ԭͼ�߶�
	 * @param  int  $maxwidth    ������������
	 * @param  int  $maxheight   ����������߶�
	 * @return array('width' => ���,'height' => �߶�)
	 */
	public static function ImageSizeKeepScale($width=0,$height=0,$maxwidth=0,$maxheight=0){
		if(!$width) $width = !$maxwidth ? '100' : $maxwidth;
		if(!$height) $height = !$maxheight ? '100' : $maxheight;
		$maxwidth = !$maxwidth ? $width : $maxwidth;
		$maxheight = !$maxheight ? $height : $maxheight;
		$size['width'] = $width;
		$size['height'] = $height;
		if($size['width'] > $maxwidth || $size['height'] > $maxheight) {
			$x_ratio = $maxwidth / $size['width'];
			$y_ratio = $maxheight / $size['height'];
			if(($x_ratio * $size['height']) < $maxheight) {
				$size['height'] = @ceil($x_ratio * $size['height']);
				$size['width'] = $maxwidth;
			} else {
				$size['width'] = @ceil($y_ratio * $size['width']);
				$size['height'] = $maxheight;
			}
		}
		return $size;
	}
	
	/**
	 * ˵����
	 *
	 * @param  array  &$item   
	 * @param  bool   $fmode  
	 * @return NULL   ---  
	 */
	public static function arr_image2mobile(&$item,$fmode = ''){return;//ȡ���÷���
		/*
		$fmodearr = array(
		'' => array('fields','chid'),
		'f' => array('ffields','chid'),
		'm' => array('mfields','mchid'),
		'pa' => array('pafields','paid'),
		'ca' => array('cnfields',0),
		'cc' => array('cnfields','coid'),
		);
		if(!empty($fmodearr[$fmode])){
			$fields = @cls_cache::Read($fmodearr[$fmode][0],$fmodearr[$fmode][1] ? $item[$fmodearr[$fmode][1]] : 0);
			foreach($fields as $k => $v){
				if(isset($item[$k]) && $v['datatype'] == 'htmltext'){
					$item[$k] = self::image2mobile($item[$k]);
				}
			}
		}
		*/
	}
	
	/**
	 * ���ֻ����н�html�е�ͼƬתΪ�����ֻ��õ�����ͼ
	 *
	 * @param  string  $html    �ֶ��е�html�����ִ�
	 * @param  int  $maxwidth    ������������
	 * @param  int  $maxheight   ����������߶�
     * @patam  array   $imageLocalSize   ����ԭͼ������
	 */
	public static function image2mobile($html,$w){
		if(empty($html)) return '';
		if(preg_match_all("/(=\s*['\"]?)((<\!cmsurl \/>|<\!ftpurl \/>)(.+?))['\" >]/i",$html,$arr) && !empty($arr[2])){
			foreach($arr[2] as $v){
				if(($url = trim($v)) && preg_match("/\.(jpg|jpeg|gif|bmp|png)$/i",$url)){                  
                    # �趨ͼƬ���640���߶�960��
                    # �Ƚ�ԭͼ��ߣ��Դ��Ϊ����Ȼ������趨�Ŀ�߰���������
        		    $imageLocalPath = cls_url::local_atm(str_replace(array('<!cmsurl />','<!ftpurl />'),'',$url), true);					
                    $maxwidth = empty($w)?640:$w;//������������
                    $maxheight = 960;//����������߶�
                    if ( is_file($imageLocalPath) )
                    {
                        $imageLocalSize = @getimagesize($imageLocalPath);  
                        $size = self::ImageSizeKeepScale($imageLocalSize[0],$imageLocalSize[1],$maxwidth,$maxheight);
                        $maxwidth = $size['width'];
                        $maxheight = $size['height'];                        
                    }else{//Զ�̸�������������ͼ
						$maxwidth = 0;
						$maxheight = 0;
					}              
					$url = self::thumb($url, $maxwidth, $maxheight, 1);
					$html = str_replace($v,$url,$html);
				}
			}
		}
		return $html;
	}
    
    public static function atm_delete($dbstr,$type = 'image')
    {
    	//����ͼƬ����ͼ��ɾ������ftp�ϸ�����ɾ��
    	$dbstr = str_replace(array('<!cmsurl />','<!ftpurl />'),'',$dbstr);
    	if(!$dbstr || strpos($dbstr,':') !== false)	return;
    	$dir = dirname($dbstr);
    	if(strpos(realpath(M_ROOT.$dir),realpath(M_ROOT)) === false) return;//��ֹ������ϵͳɾ���ļ�
    	$arr = array($dbstr);
    	if($type == 'image'){//����ͼƬ������ͼ��ftp��������ͼ��.log
    		$str = substr(basename($dbstr),0,strrpos(basename($dbstr),'.'));//�ļ����ø�ʽ
    		if(strlen($str) < 5) return;//��ֹ���ݷǷ���ַ����ɾ�������ļ�
    		$na = findfiles($dir,$str,1);
    		foreach($na as $k) in_array("$dir/$k",$arr) || $arr[] = "$dir/$k";
    		unset($na);
    	}
    	if($isftp = cls_url::is_remote_atm($dbstr)) include_once(M_ROOT."include/ftp.fun.php");
        $file = _08_FilesystemFile::getInstance();
    	foreach($arr as $k){
    		$ex = strtolower(mextension($k));
    		if(in_array($ex,array('php','js','css','xml','txt','htm','html'))) continue;
            $exts = array_keys(self::getLocalFilesExts($type));
    		if($isftp){
    			if($ex == 'log'){
    				ftp_del(str_replace('.log','.jpg',$k));
    				$file->delFile($k, $exts);
    			}else ftp_del($k);
    		}else 
            {
                $file->delFile($k, $exts);
            }
    	}
    }
    
    /**
     * ��ȡ�������ϴ�������
     */
    public static function getLocalFilesExts( $type )
    {
        if ( substr($type,-1) == 's' )
        {
            $type = substr($type,0,-1);
        }
        $_localfiles = array();
        $localfiles = cls_cache::Read('localfiles');
        if ( isset($localfiles[$type]) )
        {
            $localfiles = $localfiles[$type];
    		foreach($localfiles as $k => $v){
    			if(empty($v['islocal'])){
    				unset($localfiles[$k]);
    			}
    		}
            
            $_localfiles = $localfiles;
        }
        
        return $_localfiles;
    }


	/**
	 * ����ָ���ı��ظ���
	 *	ע��ʹ�ô˷���ǰ���������ļ���ȫ���
	 * @param  string    $file      ָ����Ҫ���ص��ļ�(��ȫ·��)
	 * @param  string    $filename  ������ʾ��Ϣ�е��ļ���(�磺**�����ļ���.��չ��)
	 * @return NULL      ---        �޷���
	 */
    public static function Down($file, $filename = ''){
		if(!is_file($file)) return;
		$filename = $filename ? $filename : basename($file);
		$filetype = mextension($filename);
		$filesize = filesize($file);
		$timestamp = cls_env::GetG('timestamp');
		ob_end_clean();
		@set_time_limit(900);
		header('Cache-control: max-age=31536000');
		header('Expires: '.gmdate('D, d M Y H:i:s', $timestamp + 31536000).' GMT');
		header('Content-Encoding: none');
		header('Content-Length: '.$filesize);
		header('Content-Disposition: attachment; filename='.$filename);
		header('Content-Type: '.$filetype);
		readfile($file);
		exit;
    }
	
	
}
