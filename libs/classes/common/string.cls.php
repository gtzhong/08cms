<?php
/**
* ����ı��Ĵ�����
* 
*/
class cls_string{
	
	// �����ַ����ֽ�����Ӣ����һ���ֽ�,����[GBK/utf-8]���������������ֽ�
	// �����ֶγ��ȼ��
	public static function CharCount($str){
		global $mcharset;
		$ch = $mcharset=='utf-8' ? 3 : 2; //���Ŀ��
		$length = strlen(preg_replace('/[x00-x7F]/', '', $str)); 
		if($length){
			return strlen($str) - $length + intval($length / $ch) * 2;
		}else{
			return strlen($str);
		}
		//return strlen(preg_replace('/([x4e00-x9fa5])/u','**',$str));
	}
	
	// �����ȼ����ı�($length�ֽ�)��
	// ����ǰ̨��ʾ�ȿ��ַ���(���İ������ֽ�,utf-8Ҳ������)
	public static function CutStr($string, $length, $dot = ' ...') {
		global $mcharset;
		$strlen = strlen($string);
		if($strlen <= $length) {
			return $string;
		}
		$strcut = '';
		$n = $tn = $noc = 0;
		$length -= strlen($dot);
		if(strtolower($mcharset) == 'utf-8') {
			while($n < $strlen) {
				$t = ord($string[$n]);
				if($t == 38){
					# the "&" char
					if(preg_match('/^&#?(\w+);/', substr($string, $n, 16), $match)){
						$noc += is_numeric($match[1]) && intval($match[1]) > 255 ? 2 : 1;
						$tn = strlen($match[0]);
						$n += $tn;
					}else{
						$tn = 1; $n++; $noc++;
					}
				}elseif($t == 9 || $t == 10 || (32 <= $t && $t <= 126)) {
					$tn = 1; $n++; $noc++;
				} elseif(194 <= $t && $t <= 223) {
					$tn = 2; $n += 2; $noc += 2;
				} elseif(224 <= $t && $t < 239) {
					$tn = 3; $n += 3; $noc += 2;
				} elseif(240 <= $t && $t <= 247) {
					$tn = 4; $n += 4; $noc += 2;
				} elseif(248 <= $t && $t <= 251) {
					$tn = 5; $n += 5; $noc += 2;
				} elseif($t == 252 || $t == 253) {
					$tn = 6; $n += 6; $noc += 2;
				} else {
					$n++;
				}
				if($noc >= $length) {
					break;
				}
			}
		} else {
			while($n < $strlen) {
				$t = ord($string[$n]);
				if($t == 38){
					# the "&" char
					if(preg_match('/^&#?(\w+);/', substr($string, $n, 16), $match)){
						$noc += is_numeric($match[1]) && intval($match[1]) > 255 ? 2 : 1;
						$tn = strlen($match[0]);
						$n += $tn;
					}else{
						$tn = 1; $n++; $noc++;
					}
				}else{
					$tn = $t > 127 ? 2 : 1;
					$n += $tn;
					$noc += $tn;
				}
				if($noc >= $length) {
					break;
				}
			}
		}
		if($noc > $length) {
			$n -= $tn;
		}
		$strcut = substr($string, 0, $n);
		return $strcut.$dot;
	}
	
	/**
	 * int WordCount(string $string[, bool $flag]) ��������
	 *
	 * @param	string	$string	�������ַ���
	 * @param	bool	$flag	Ϊ����ֻ������ֽ��ַ���
	 * @return	int				�ַ����ַ���
	 *
	 * @remark	�� strlen �������Ƕ��ֽ��ַ�ֻ��һ����
	 *
	 **/
	public static function WordCount($string, $flag = false){
		global $mcharset;
		$n = $word = 0;
		$strlen = strlen($string);
		if(strncasecmp('utf', $mcharset, 3)){
			while($n < $strlen){
				$t = ord($string[$n]);
				if($t > 127){
					$n += 2;
					$word++;
				}else{
					$n++;
					$flag || $word++;
				}
			}
		}else{
			while($n < $strlen){
				$t = ord($string[$n]);
				if(194 <= $t && $t <= 223){
					$n += 2;
					$word++;
				}elseif(224 <= $t && $t < 239){
					$n += 3;
					$word++;
				}elseif(240 <= $t && $t <= 247){
					$n += 4;
					$word++;
				}elseif(248 <= $t && $t <= 251){
					$n += 5;
					$word++;
				}elseif($t == 252 || $t == 253){
					$n += 6;
					$word++;
				}else{
					$n++;
					$flag || $word++;
				}
			}
		}
		return $word;
	}
	
	
	
	/**
	 * string keywords(string $nstr, string $ostr) �и��ؼ���
	 *
	 * @param	string	$nstr	���� addslashes ����Ķ��Ż�ո�ָ���ַ���
	 * @param	string	$ostr	���ŷָ���ַ���
	 * @return	string			���� addslashes ����Ķ��ŷָ���ַ���
	 *
	 * @remark	�ָ�����ݰ�Ǻ�ȫ�ǣ����ַ����ж���ʱʹ�ö��ŷָ����ʹ�ÿո�ָ�
	 *			ÿ���ؼ���Ҫ���� 2 - 8 �����ֻ� 2 - 24 ����ĸ֮��
	 **/
	public static function keywords($nstr, $ostr=''){
		global $hotkeywords, $mcharset, $db, $tblprefix;
		if(empty($nstr))return '';
		$nstr = stripslashes($nstr);
		if(!strncasecmp('gb', $mcharset, 2)){
			#gbk, gb2312
			$comma = pack('C*', 163, 172);#����
			$blank = pack('C*', 161, 161);#�ո�
		}elseif(!strncasecmp('big', $mcharset, 3)){
			#big5, big5-HKSCS
			$comma = pack('C*', 161, 65);
			$blank = pack('C*', 161, 64);
		}else{
			#utf-8
			$comma = pack('C*', 239, 188, 140);
			$blank = pack('C*', 227, 128, 128);
		}
		$tstr = str_replace($comma, ',', $nstr);
		$isbk = strpos($tstr, ',') === false;
		$narr = array_unique(explode($isbk ? ' ' : ',', $isbk ? str_replace($blank, ' ', $nstr) : $tstr));
		$oarr = $ostr ? explode(',', $ostr) : array();
		$i = 0;
		$ret = $sqlstr = '';
		foreach($narr as $str){
			$str = trim(strip_tags($str));
			$len = strlen($str);
			if($len >= 2 && $len <= 24){
				$word = self::WordCount($str, 1);
				if($word == 0 || ($word >= 2 && $word <= 8)){
					#�к��־ͱ����� 2-8 ����
					$ret .= ($ret ? ',' : '') . $str;
					$hotkeywords && !in_array($str, $oarr) && $sqlstr .= ($sqlstr ? ',' : '') . "('" . addslashes($str) . "')";
	
					if(++$i >= 5){
						unset($narr,$oarr);
						break;
					}
				}
			}
		}
		$sqlstr && $db->query("INSERT INTO {$tblprefix}keywords (keyword) VALUES $sqlstr");
		return addslashes($ret);
	}
	
	//����html�ı��е���ʽ��js��
	public static function HtmlClear($str){
		$str = preg_replace("/<sty.*?\\/style>|<scr.*?\\/script>|<!--.*?-->/is", '', $str);
		$str = preg_replace("/<\\/?(?:p|div|dt|dd|li)\b.*?>/is", '<br>', $str);
		$str = preg_replace("/\s+/", '', $str);
		$str = preg_replace("/<br\s*\\/?>/is", "\r\n", $str);
		$str = strip_tags($str);
	
		return str_replace(
			array('&lt;', '&gt;', '&nbsp;', '&quot;', '&ldquo;', '&rdquo;', '&amp;'),
			array('<','>', ' ', '"', '"', '"', '&'),
			$str
		);
	}
    
	/*
	//��ȫ�ִ�
	public static function SafeStr($string)
    {
		$searcharr = array("/(javascript|jscript|js|vbscript|vbs|about):/i","/on(mouse|exit|error|click|dblclick|key|load|unload|change|move|submit|reset|cut|copy|select|start|stop)/i","/<script([^>]*)>/i","/<iframe([^>]*)>/i","/<frame([^>]*)>/i","/<link([^>]*)>/i","/@import/i");
		$replacearr = array("\\1\n:","on\n\\1","&lt;script\\1&gt;","&lt;iframe\\1&gt;","&lt;frame\\1&gt;","&lt;link\\1&gt;","@\nimport");
		$string = preg_replace($searcharr,$replacearr,$string);
		$string = str_replace("&#","&\n#",$string);
		return $string;
	}*/
    
    /**
     * ��ȫ�ִ���Ҳ������XSS��
     * 
     * @param  string $value Ҫ���˵�ֵ
     * @return string        �����Ѿ����˹���ֵ
     * 
     * @since  1.0
     */
    public static function SafeStr($value)
    {
		
       // remove all non-printable characters. CR(0a) and LF(0b) and TAB(9) are allowed
       // this prevents some character re-spacing such as <java\0script>
       // note that you have to handle splits with \n, \r, and \t later since they *are* allowed in some inputs
    #   $value = preg_replace('/([\x00-\x08,\x0b-\x0c,\x0e-\x19])/', '', $value);
    
       // straight replacements, the user should never need these since they're normal characters
       // this prevents like <IMG SRC=@avascript:alert('XSS')>
       $search = 'abcdefghijklmnopqrstuvwxyz';
       $search .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
       $search .= '1234567890!@#$%^&*()';
       $search .= '~`";:?+/={}[]-_|\'\\';
       for ($i = 0; $i < strlen($search); $i++)
       {
          // ;? matches the ;, which is optional
          // 0{0,7} matches any padded zeros, which are optional and go up to 8 chars
    
          // @ @ search for the hex values
          $value = preg_replace('/(&#[xX]0{0,8}'.dechex(ord($search[$i])).';?)/i', $search[$i], $value); // with a ;
          // @ @ 0{0,7} matches '0' zero to seven times
          $value = preg_replace('/(&#0{0,8}'.ord($search[$i]).';?)/', $search[$i], $value); // with a ;
       }
    
       // now the only remaining whitespace attacks are \t, \n, and \r
       #$ra1 = array('javascript', 'vbscript', 'expression', 'applet', 'meta', 'xml', 'blink', 'link', 'style', 
       #             'script', 'embed', 'object', 'iframe', 'frame', 'frameset', 'ilayer', 'layer', 'bgsound', 'base');
       $ra1 = array('<javascript', '<vbscript', '<expression', '<applet', '<script', '<object', '<iframe',
	                '<frame', '<frameset', '<ilayer', '<bgsound', '<base');
                    
       $ra2 = array('onabort', 'onactivate', 'onafterprint', 'onafterupdate', 'onbeforeactivate', 'onbeforecopy', 'onbeforecut', 
           'onbeforedeactivate', 'onbeforeeditfocus', 'onbeforepaste', 'onbeforeprint', 'onbeforeunload', 'onbeforeupdate', 
           'onblur', 'onbounce', 'oncellchange', 'onchange', 'onclick', 'oncontextmenu', 'oncontrolselect', 'oncopy', 'oncut', 
           'ondataavailable', 'ondatasetchanged', 'ondatasetcomplete', 'ondblclick', 'ondeactivate', 'ondrag', 'ondragend', 
           'ondragenter', 'ondragleave', 'ondragover', 'ondragstart', 'ondrop', 'onerror', 'onerrorupdate', 'onfilterchange', 
           'onfinish', 'onfocus', 'onfocusin', 'onfocusout', 'onhelp', 'onkeydown', 'onkeypress', 'onkeyup', 'onlayoutcomplete', 
           'onload', 'onlosecapture', 'onmousedown', 'onmouseenter', 'onmouseleave', 'onmousemove', 'onmouseout', 'onmouseover', 
           'onmouseup', 'onmousewheel', 'onmove', 'onmoveend', 'onmovestart', 'onpaste', 'onpropertychange', 'onreadystatechange', 
           'onreset', 'onresize', 'onresizeend', 'onresizestart', 'onrowenter', 'onrowexit', 'onrowsdelete', 'onrowsinserted', 
           'onscroll', 'onselect', 'onselectionchange', 'onselectstart', 'onstart', 'onstop', 'onsubmit', 'onunload');
           
       $ra = array_merge($ra1, $ra2);
    
       $found = true; // keep replacing as long as the previous round replaced something
       while ($found == true) 
       {
          $val_before = $value;
          for ($i = 0; $i < sizeof($ra); $i++) 
          {
             $pattern = '/';
             for ($j = 0; $j < strlen($ra[$i]); $j++) 
             {
                if ($j > 0) 
                {
                   $pattern .= '(';
                   $pattern .= '(&#[xX]0{0,8}([9ab]);)';
                   $pattern .= '|';
                   $pattern .= '|(&#0{0,8}([9|10|13]);)';
                   $pattern .= ')*';
                }
                $pattern .= $ra[$i][$j];
             }
             $pattern .= '/i';
             
             $replacement = substr($ra[$i], 0, 2).'<!--08CMS-->'.substr($ra[$i], 2); // add in <> to nerf the tag
             $value = preg_replace($pattern, $replacement, $value); // filter out the hex tags
             
             if ($val_before == $value)
             {
                // no replacements were made, so exit the loop
                $found = false;
             }
          }
       }
       
       return $value;
    }
    
    /**
     * �Ծ�����self::SafeStr �������ú���ַ�����ԭ
     * 
     * @param  string $string Ҫ��ԭ���ַ���
     * @return string         ��ԭ����ַ���
     * 
     * @since  1.0
     */
    public static function RestoreSafeStr( $string )
    {
        # ֻ��ԭ���滻��ͨ�ַ��ķ����������ϵ��滻û��ԭ����Ϊ��ԭ�����ϵ��ַ����岻��
        $string = str_replace('<!--08CMS-->', '', $string);
        return $string;
    }
    
    /**
     * �жϸ��ַ��Ƿ�Ϊ��ȫ�ִ�
     * 
     * @param  string $string Ҫ�жϵ��ַ���
     * @return bool           ����ǰ�ȫ�ִ�����TRUE�����򷵻�FALSE
     * 
     * @since  1.0
     */
    public static function isSafeStr( $string )
    {
        $string2 = self::SafeStr($string);
        if ( $string === $string2 )
        {
            return true;
        }
        
        return false;
    }
	
	//����ת��
	public static function iconv($from,$to,$source){
		if(!is_array($source) && ($source === '')) return '';
		$from = strtolower($from);
		$to = strtolower($to);
		if($from == $to) return $source;
		if(is_array($source)){
			$re = array();
			foreach($source as $k => $v) $re[$k] = self::iconv($from,$to,$v);
			return $re;
		}elseif(is_int($source)){
			return $source;
		}else{
			if(($from == "big5" && $to == "gbk")||($from == "gbk" && $to == "big5")) $flag = false;
			else $flag = true;
			if(function_exists('mb_convert_encoding') && $to != 'pinyin' && $flag){
				return mb_convert_encoding($source,$to,$from);
			}elseif(function_exists('iconv') && $to != 'pinyin' && $flag){
				strcasecmp('utf8', $from) || $from = 'utf-8';
				strcasecmp('gb2312', $from) || $from = 'gbk';
				strcasecmp('utf8', $to) || $to = 'utf-8';
				strcasecmp('gb2312', $to) || $to = 'gbk';
				return iconv($from, $to."//IGNORE", $source);
			}else{
				if($to=='pinyin'){ // ƴ��ת��,ר��
					return self::Pinyin($source);
				}else{ // chinese��ƴ��ת��,��ʹ����
					include_once _08_INCLUDE_PATH."chinese.cls.php";
					$chs = new chinese();
					$from = str_replace("utf-8","utf8",$from);
					$from = str_replace("gbk","gb2312",$from);
					$to = str_replace("utf-8","utf8",$to);
					$to = str_replace("gbk","gb2312",$to);
					$charset = array("utf8","gb2312","big5","unicode","pinyin");
					if(!in_array($from,$charset) || !in_array($to,$charset)){
						return '';
					}else{
						$from = strtoupper($from);
						$to = strtoupper($to);
						return $chs->Convert($from,$to,$source);
					}
				}
			}
		}
	}
    
    /**
     * �ж�һ���ַ����ǲ���UTF8����
     * ������鿴��{@link http://www.w3.org/International/questions/qa-forms-utf-8.zh-hans.php?changelang=zh-hans}
     * 
     * @param  string $string Ҫ�жϵ��ַ���
     * @return bool           �����UTF8����TRUE�����򷵻�FALSE
     */
    public static function isUTF8($string)
    {
        return (bool) preg_match('%^(?:
              [\x09\x0A\x0D\x20-\x7E]            # ASCII
            | [\xC2-\xDF][\x80-\xBF]             # non-overlong 2-byte
            |  \xE0[\xA0-\xBF][\x80-\xBF]        # excluding overlongs
            | [\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}  # straight 3-byte
            |  \xED[\x80-\x9F][\x80-\xBF]        # excluding surrogates
            |  \xF0[\x90-\xBF][\x80-\xBF]{2}     # planes 1-3
            | [\xF1-\xF3][\x80-\xBF]{3}          # planes 4-15
            |  \xF4[\x80-\x8F][\x80-\xBF]{2}     # plane 16
        )*$%xs', $string);
    }
	
	/**���ִ�תΪƴ��
	  *@param int $need_first_letter �Ƿ�Ҫ���� �ַ���ת�������д��ĸ�Լ�ȫƴ��ϳɵ��ַ���,�����ö��ŷָ��
	  *								 eg:������԰  ����   dhhy,donghuhuayuan
	  */
	public static function Pinyin($_String,$need_first_letter = 0){	
		global $mcharset;
		include_once _08_INCLUDE_PATH."encoding/pinyin.table.php";
		$pytab = pycfgTab(); 
		$mcharset != 'gbk' && $_String = self::iconv($mcharset,'GB2312',$_String);
		$cset = 2; //GB2312����ռ�����ֽ�
		$py=""; 
		$first_letter = '';//ȡ����תΪƴ����ÿ�����ʵ�����ĸ��ϳɵ��ַ���
		$p = 0;
		$len = strlen($_String);
		for($i = 0;$i < $len; $i++){   
			$ch = substr($_String,$p,1);
			if(ord($ch)<160){ //160(10)=11xxxxxx(2)��λ,��ʾ�����ֽڵĺ���
			  $py .= $ch;
			  $first_letter .= $ch;
			  $p++;
			}else{
			  $ch = substr($_String,$p,$cset);  
			  $py .= self::py__One($ch, $pytab); 
			  $first_letter .= substr(self::py__One($ch, $pytab),0,1);
			  $p += $cset; 
			}
			if($p>=$len) break;
		}   
		return empty($need_first_letter)?$py:addslashes(str_replace(array('(',')',' ','\\'),'',$first_letter.",".$py)); 
	}
	
	public static function py__One($chr, $tab=''){
	  if(empty($tab)){
		include_once _08_INCLUDE_PATH."encoding/pinyin.table.php";
		$tab = pycfgTab();
	  }
	  $p = strpos($tab,$chr); 
	  $t = substr($tab,0,$p);
	  $t = strrchr($t,"(");
	  $p = strpos($t,")");
	  $t = substr($t,1,$p-1);
	  return $t;   
	}
	
	//ȡ���ִ�������ĸ
	public static function FirstLetter($string, $number=0, $first=1){
		global $mcharset;
		$cset = 2; //GB2312����ռ�����ֽ�
		if($first) $mcharset != 'gbk' && $string = self::iconv($mcharset, 'GB2312', $string);
		$p = 0;
		for($i=0, $l = strlen($string); $i < $l; $i++){
			$_P = ord($_Z = $string{$i});
			if($_P>160){
				//$pytab = pycfgTab(); 
				$ch = self::py__One(substr($string,$p,$cset)); 
				if($ch){
					return strtoupper(substr($ch,0,1));
				}else{
					$p += $cset; 
					if($p>=$l) return '';
					self::FirstLetter(substr($string,$cset), $number, 0);
				}
			}elseif($_P >= 65 && $_P < 91){
				return $_Z;
			}elseif($_P >= 97 && $_P < 123){
				return chr($_P - 32);
			}elseif($number && $_P >= 48 && $_P < 58){
				return $_Z;
			}
		}
		return '';
	}
	
	/**
	*���ص绰,�ֻ�,�ʼ�,qq,ip���м�һ����
	*$charΪ�滻���ַ�,Ĭ��Ϊ*
	*/
	public static function SubReplace($str,$char=''){
		$char = empty($char) ? '*' : $char;
		if(strpos($str,'@')>0){
			$a = explode('@',$str);
			$suf = '@'.$a[1];
			$str = $a[0];
		}else{
			$suf = '';
		}
		$len = strlen($str);
		if($len<3) return $str.$suf;
		if($len<6) $n = 2;
		else $n = 4;
		$start = ($len-6)<1 ? 1 : $len-6;
		$re = ''; for($i=0;$i < $n;$i++) $re .= $char;
		$str = substr_replace($str,$re,$start,$n);
		return $str.$suf;
	}

	public static function isEmail($email){
		return strlen($email) > 6 && preg_match("/^[\w\-\.]+@[\w\-\.]+(\.\w+)+$/", $email);
	}
	public static function isDate($date, $mode = 0) {
		if(!empty($date) && strlen($date) < 20 &&
			preg_match('/^([12][0-9]{3})-([01]?\d)-([0123]?\d)(?: ([012]?[0-9]):([0-9]{1,2}):([0-9]{1,2}))?$/', $date, $match) &&
			checkdate(intval($match[2]), intval($match[3]), intval($match[1]))){
				return $mode ? ($match[4] >= 0 && $match[4] < 24 && $match[5] >= 0 && $match[5] < 60 && $match[6] >= 0 && $match[6] < 60) : count($match) < 6;
		}
		return false;
	}

	//��������ִ�
	public static function Random($length, $types = 0) {
		if($types == 1) {
			$result = sprintf('%0'.$length.'d', mt_rand(0, pow(10, $length) - 1));
		} else {
			$result = '';
			$chars = str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz');
            if ( $types != 2 )
            {
                $chars .= '0123456789';
            }
			$max = strlen($chars) - 1;
			for($i = 0; $i < $length; $i++) {
				$result .= $chars[mt_rand(0, $max)];
			}
		}
		return $result;
	}
	
    /**
     * ��ʽ��һ���ִ���ʹ�����ΪPHP�����������ݿ��ֶ�����������'��ĸ����_'֮����ַ�)
     * 
     * @param  string $string Ҫ���˵��ļ�����
     */
    public static function ParamFormat( $string  = ''){
        return preg_replace('/[^\w]/', '', $string);
    }
    
    /**
     * ����һ���ַ���
     * 
     * @param string $string   Ҫ������ַ���
     * @param string $fcuntion ��������ķ�����������÷����ɲ鿴��������򷽷�
     * @param mixed  $callable ��Ӧ���򷽷��Ĳ���
     * 
     * @since nv50
     */
    public static function sort( &$string, $function = '', $callable = array() )
    {
        if ( empty($function) )
        {
            $function = 'sort';
        }
        $stringArray = str_split($string);
        if ( empty($callable) )
        {
            $function($stringArray);
        }
        else
        {
        	$function($stringArray, $callable);
        }
        
        $string = implode('', $stringArray);
    }
    
    /**
     * ��URI��������
     * 
     * @param string $uri Ҫ�����URI�ַ���
     * @since nv50
     */
    public static function sortURI( &$uri )
    {        
        parse_str($uri, $uriArray);
        ksort($uriArray);
        $uri = http_build_query($uriArray);
    }
}
