<?PHP
!defined('M_COM') && exit('No Permisson');
@include_once _08_EXTEND_LIBS_PATH.'functions'.DS.'compatibility_201308.php';//������ʱ�����ľɰ汾����(����)�ű�
@include_once _08_EXTEND_LIBS_PATH.'functions'.DS.'exgeneral.fun.php';

//������չϵͳ�е�ͨ�ú���

/**
 * �����ĵ��������飬��Ҫ����
 *
 * @param  string $tbl  ='archives'���������ĵ�����
 * @return array  $re   �����ĵ���������
 */
function m_tblarr($tbl){
	$re = array($tbl);
	$na = array_keys(cls_cache::Read('splitbls'));
	if($na && $tbl == 'archives'){
		foreach($na as $k) $re[] = $tbl.$k;
	}
	return $re;
}

/**
 * ��php������ʽ�Ĵ����ַ�����ת��Ϊphp����
 *
 * ��Ҫ����ת���ֶε���չ���õĴ����ִ����磺array(1='��',2='�ܺ�',3='�ǳ���',)��
 * Demo : $fieldnew['cfgs'] = varexp2arr($fieldnew['cfgs0']);
 *
 * @param  string $str   php�����ַ���
 * @return array  $re    ���ض�Ӧ������
 */
function varexp2arr($str = ''){
	if(!$str) return array();
	@eval("\$ret = ".stripslashes($str).";");
	return empty($ret) || !is_array($ret) ? array() : $ret;
}


/**
 * ��ȡ�ö��ŷֿ���ϵ��id�еĵ�һ��ID
 *
 * @param  string   $id    ����ö��ŷֿ���id�ַ���(��'23,68,89')
 * @return int      $rets  ID(������23)
 */
function cnoneid($id){
	return intval(ltrim($id,','));
}

/**
 * ˵����
 *
 * @param  int     $addno   
 * @param  string  $addnostr
 * @return string  ---
 */
function arc_addno($addno = 0,$addnostr = ''){
	return empty($addnostr) ? ($addno ? $addno : '') : $addnostr;
}

/**
 * ��ȡ��չ�ű�����ڵ�ַ
 *
 * @param  string  $str ��չ�ű���ʶ
 * @return string  $str ���û���ҵ����򷵻�false
 */
function exentry($str = ''){
	if(!$str || !($arr = cls_cache::cacRead('exscripts',_08_EXTEND_SYSCACHE_PATH)) || empty($arr[$str]) || !is_file(M_ROOT.$arr[$str])) return false;
	return M_ROOT.$arr[$str];
}


/**
 * �ж���ϵ$coid��$chid��Ӧ���ĵ��������Ƿ���Ч
 *
 * @param  int   $coid     ��ϵ��ĿID
 * @return int   $chid     �ĵ�ģ��ID
 */
function coid_in_chid($coid,$chid){
	if(!$coid || !$chid || !($channel = cls_channel::Config($chid)) || !($stid = $channel['stid'])) return false;
	$splitbls = cls_cache::Read('splitbls');
	if(!in_array($coid,@$splitbls[$stid]['coids'])) return false;
	return true;
}


/**
 * ��$var��JSON���ݱ��룬ע���÷������$var��������ַ���<script>֮��Ļ������ע����㡣
 *
 * @param  array  $var     ��$var��JSON���ݱ��룬������object,array,string
 * @param  bool   $is_a    �Ƿ�Ϊ����
 * @param  bool   $idx     �Ƿ�Ϊһ��id��ɵ��ַ���
 * @return array  $caccnt  ������JSON�ַ���
 */
function jsonEncode($var, $is_a = 0, $idx = 0){
	static $slashes = "\\\"\r\n";
	if(is_string($var)){
		return '"' . addcslashes($var, $slashes) . '"';
	}elseif(is_numeric($var)){
		return $var;
	}elseif(is_bool($var)){
		return $var ? 'true' : 'false';
	}elseif(is_null($var)){
		return 'null';
	}else{
		is_object($var) && $var = get_object_vars($var);
		if(is_array($var)){
			$keys = array_keys($var);
			$val = implode('', $keys);
			if(!$is_a && (!$val || is_numeric($val))){
				$let = '';
				if($idx){
					for($k = 0, $v = max($keys); $k < $v; $k++)$let .= ',' . (isset($var[$k]) ? jsonEncode($var[$k], $is_a, $idx) : '');
				}else{
					foreach($keys as $k)$let .= ',' . jsonEncode($var[$k], $is_a, 0);
				}
				return '[' . substr($let, 1) . ']';
			}else{
				$let = '';
				foreach($var as $k => $v)$let .= ',"' . addcslashes($k, $slashes) . '":' . jsonEncode($v, $is_a, $idx);
				return '{' . substr($let, 1) . '}';
			}
		}
	}
	return '"unknow"';
}

/**
 * ��������arr����sql�Ӿ�
 *
 * @param  array  $arr     ��ϵ��Ŀid
 * @param  bool   $chid    1: NOT IN��0: IN��Ĭ�ϣ�
 * @return array  $caccnt  ���ص����飬�磺IN ('1','2','3')
 */
function multi_str($arr = array(),$no = 0){
	if(count($arr) == 1) return ($no ? '!=' : '=')."'".array_shift($arr)."'";
	else return ($no ? 'NOT ' : '')."IN (".mimplode($arr).")";
}

/**
 * ��html����
 *
 * @param  string  $cacfile  �����ļ�
 * @return string  $caccnt   ��ȡ������,string��array��
 */
function read_htmlcac($cacfile){
	return (@include $cacfile) ? $caccnt : '';
}

/**
 * ����html����
 *
 * @param  int    $cnt      ��������
 * @param  string $cacfile  �����ļ�
 * @return NULL   ---       ---
 */
function save_htmlcac($cnt,$cacfile){
	str2file("<?php\ndefined('M_COM') || exit('No Permission');\n\$caccnt = '".addcslashes($cnt,'\'\\')."';",$cacfile);
}

/**
 * ����Ŀ¼��������Ȩ��
 *
 * @param  string   $dir     ·��
 * @param  bool     $create  �Ƿ�����inex.html,index.htm�ļ�
 * @param  bool     $isfile  ---
 * @return bool     ---      true:�����ɹ���false:����ʧ�ܣ�
 */
function mmkdir($dir,$create=1,$isfile=0){
	if(is_dir($dir)) return true;
	if($isfile){
		return mmkdir(dirname($dir),0);
	}else{
		if(!mmkdir(dirname($dir),0) || @!mkdir($dir,0777)) return false;
		if($create) foreach(array('htm','html') as $var) @touch($dir.'/index.'.$var);
		return true;
	}
}

/**
 * ����Ŀ¼
 *
 * @param  string   $dir     ·��
 * @param  bool     $self    �Ƿ����Ŀ¼����
 * @param  string    $expstr  ���Ե�Ŀ¼���Զ��ŷָ����Ŀ¼
 * @return NULL     ---     
 */
function clear_dir($dir,$self = false,$expstr = ''){
	if(empty($dir)) return false;
	if(is_dir($dir)){
		$exp_arr = array('.','..',);
		if($expstr) foreach(explode(',',$expstr) as $v) $exp_arr[] = $v;
		$p = @opendir($dir);
		while(false !== ($f = @readdir($p))){
			if(!in_array($f,$exp_arr)) clear_dir("$dir/$f",true,$expstr);
		}
		@closedir($p);
		if($self) @rmdir($dir);
	}elseif(is_file($dir)) 
    {
        $file = _08_FilesystemFile::getInstance();
        $file->delFile($dir);
    }
}

/**
 * �滻��ǩ�У�sql����еı���
 *
 * @param string   $str
 * @param array    &$temparr
 * @return string  $str
 */
function sqlstr_replace($str,&$temparr){
	return preg_replace("/\{\\$(.+?)\}/ies","sqlstrval('\\1',\$temparr)",$str);
}

/**
 * �滻��ǩ�У�sql����еı���
 *
 * @param string   $tname
 * @param array    &$temparr
 * @return string  $tname
 */
function sqlstrval($tname,&$temparr){
	global $timestamp,$G;
	$temparr['timestamp'] = $timestamp;
	if(isset($temparr[$tname])){
		return $temparr[$tname];
	}elseif(isset($G[$tname])){
		return $G[$tname];
	}else return '';
}

/**
 * ˵����
 *
 * @param string   $tname
 * @param array    &$sarr
 * @return string  $tname
 */
function btagval($tname,&$sarr){
	$btags = cls_cache::Read('btags');
	if(isset($sarr[$tname])){
		return str_tagcode($sarr[$tname]);
	}elseif(isset($btags[$tname])){
		return str_tagcode($btags[$tname]);
	}else return _08_DEBUGTAG ? "{ \$$tname}" : '';
}

/**
 * ˵����
 *
 * @param string   &$source
 * @param bool     $decode
 * @return string  $source
 */
function str_tagcode(&$source,$decode=0){
	return $decode ? str_replace(array(' $','? }'),array('$','?}'),$source) : str_replace(array('$','?}'),array(' $','? }'),$source);
}

/**
 * �����ʼ�
 *
 * @param string   $to      �ռ���ַ  
 * @param string   $subject ����
 * @param string   $msg     ����  
 * @param array    $sarr    ---
 * @param string   $from    �����˵�ַ    
 * @param bool     $ischeck �Ƿ񱣴淢�ͼ�¼
 * @return string  $ret 
 */
function mailto($to,$subject,$msg,$sarr=array(),$from = '',$ischeck=0){
	include_once M_ROOT.'include/mail.fun.php';
	$ret = sys_mail($to,splang($subject,$sarr),splang($msg,$sarr),$from);
	if(!$ischeck && $ret){
		global $timestamp;
		$curuser = cls_UserMain::CurUser();
		$record = mhtmlspecialchars($timestamp."\t".$curuser->info['mid']."\t".$curuser->info['mname']."\t".$ret);
		record2file('smtp',$record);
	}
	return $ret;
}
/**
 * ˵����
 *
 * @param string   $key  
 * @param array    &$sarr
 * @return string  $ret 
 */
function splang($key,&$sarr){
	$ret = $key;
	$splangs = cls_cache::Read('splangs');
	if(isset($splangs[$key])) $ret = preg_replace("/\{\\$(.+?)\}/ies","btagval('\\1',\$sarr)",$splangs[$key]);
	return $ret;
}

/**
 * ˵����
 *
 * @param array   $arr  
 * @return array  $arr  
 */
function marray_flip_keys($arr) {
	$arr2 = array();
	$arrkeys = array_keys($arr);
	list(, $first) = each(array_slice($arr, 0, 1));
	if($first) {
		foreach($first as $k=>$v) {
			foreach($arrkeys as $key) {
				$arr2[$k][$key] = $arr[$key][$k];
			}
		}
	}
	return $arr2;
}

/**
 * �Դ��ɱ����{$page}���ļ�����ɾ����ҳͬ���ļ�
 *
 * @param  string  $filepre  �ļ���
 * @param  string  $num      ��ҳ��Ŀ
 * @return NULL    ---       �ڵ���Ϣ
 */
function m_unlink($filepre='',$num=50){
	if(!$filepre) return;
    $file = _08_FilesystemFile::getInstance();
	for($i = 1;$i <= $num;$i++)
    {
		if(!$file->delFile(M_ROOT.cls_url::m_parseurl($filepre,array('page' => $i,))))
        {
            break;
        }
	}
}

/**
 * ���ݻ�Ա�ڵ��ִ����õ��ڵ�Ŀ¼
 * 
 * @param  string $cnstr    �ڵ��ִ�
 * @return string $dirname  �ڵ�Ŀ¼
 */
function mcn_dir($cnstr){
	if(!$cnstr) return '';
	$var = array_map('trim',explode('=',$cnstr));
	if($var[0] == 'caid'){
		$arr = cls_cache::Read('catalogs');
	}elseif(in_str('ccid',$var[0])) $arr = cls_cache::Read('coclasses',str_replace('ccid','',$var[0]));
	return empty($arr[$var[1]]['dirname']) ? $var[0].'_'.$var[1] : $arr[$var[1]]['dirname'];
}

/**
 * ��ȡ�ĵ�url����Ҫ���ֶ�
 * ���� INNER JOIN�У�ֻѡȡ��Ҫ���ֶΣ����������ֶθ���
 * @param  string $fix �ֶ�ǰ׺(�����)
 *
 * @return string fields_str �ֶ��б�����[,]��ǰ׺
 */
function aurl_fields($fix='a.'){
	$fstr = ",a.aid,a.chid,a.caid,a.createdate,a.initdate,a.customurl,a.nowurl,a.subject,a.mid";
	if($fix!='a.') $fstr = str_replace('a.',$fix,$fstr);
	return $fstr;
}

/**
 * ��ȡ��Ա�ռ�url����Ҫ���ֶ�
 * ���� INNER JOIN�У�ֻѡȡ��Ҫ���ֶΣ����������ֶθ���
 * @param  string $fix �ֶ�ǰ׺(�����)
 *
 * @return string fields_str �ֶ��б�����[,]��ǰ׺
 */
function murl_fields($fix='m.'){
	$fstr = ",m.mid,m.mchid,m.dirname,m.mspacepath,m.mname,m.msrefreshdate"; 
	if($fix!='m.') $fstr = str_replace('m.',$fix,$fstr);
	return $fstr;
}

/**
 * ���ַ���ת��Ϊ������ʽ
 *
 * @param  string    $str     ԭ�ַ���
 * @return string    $str     �������ַ���
 */
function u_regcode($str){
	return "/^".preg_quote($str,"/")."/i";
}

/**
 * ����ַ���$source�Ƿ�������ַ���$me
 *
 * @param  string    $me      ���ַ���
 * @param  string    $source  Ҫ�������ַ���
 * @return bool      ---      ����1,������0
 */
function in_str($me,$source){
	return !(strpos($source,$me) === FALSE);
}

/**
 * ���ַ�����document.write��js��ʽ���
 *
 * @param  string    $content   Ҫ���������
 * @return NULL      ---        �޷���
 */
function js_write($content){
	$content = cls_phpToJavascript::JsWriteCode($content);
    echo $content;
}

/**
 * �Ѳ�����¼д���ļ�
 *
 * @param  string    $rname     ��¼����(ͬʱȷ���ļ�����ʽ)
 * @param  string    $record    ������¼����
 * @return NULL      ---        �޷���
 */
function record2file($rname,$record){
	global $timestamp;
	$recorddir = M_ROOT.'dynamic/records/';
	$recordfile_pre = $recorddir.date('Ym',$timestamp).'_'.$rname;
	$recordfile = $recordfile_pre.'.php';
	if(@filesize($recordfile) > 1024*1024){
		$dir = opendir($recorddir);
		$length = strlen($rname);
		$maxid = $id = 0;
		while($file = readdir($dir)){
			if(in_str($recordfile_pre,$file)){
				$id = intval(substr($file,$length +8,-4));
				($id > $maxid) && ($maxid = $id);
			}
		}
		closedir($dir);
		$recordfilebk = $recordfile_pre.'_'.($maxid +1).'.php';
		@rename($recordfile,$recordfilebk);
	}
	if($fp = @fopen($recordfile, 'a')){
		@flock($fp, 2);
		$record = is_array($record) ? $record : array($record);
		foreach($record as $tmp) {
			fwrite($fp, "<?PHP exit;?>\t".str_replace(array('<?', '?>'), '', $tmp)."\n");
		}
		fclose($fp);
	}
}

/**
 * �����ļ��б�
 *
 * @param  string    $absdir     ���·��
 * @param  string    $str        �ؼ��֣�Ϊ�ձ�ʾȫ��
 * @param  bool      $inc        0����չ����ѯ��1�������ִ���ѯ
 * @return array     $tempfiles  �����ļ��б�
 */
function findfiles($absdir,$str='',$inc=0){//$inc 0����չ����ѯ��1�������ִ���ѯ
	$tempfiles = array();
	if(is_dir($absdir)){
		if($tempdir = opendir($absdir)){
			while(($tempfile = readdir($tempdir)) !== false){
				if(filetype($absdir."/".$tempfile) == 'file'){
					if(!$str){
						$tempfiles[] = $tempfile;
					}elseif(!$inc && mextension($tempfile) == $str){
						$tempfiles[] = $tempfile;
					}elseif($inc && in_str($str,$tempfile)){
						$tempfiles[] = $tempfile;
					}
				}
			}
			closedir($tempdir);
		}
	}
	return $tempfiles;
}

/**
 * ���ı���ʽ����������������ո�,����,�˸�
 *
 * @param  string    $absdir     ���·��
 * @param  string    $str        �ؼ��֣�Ϊ�ձ�ʾȫ��
 * @param  bool      $inc        0����չ����ѯ��1�������ִ���ѯ
 * @return array     $tempfiles  �����ļ��б�
 */
function mnl2br($string){
	return nl2br(str_replace(array("\t", '   ', '  '), array('&nbsp; &nbsp; &nbsp; &nbsp; ', '&nbsp; &nbsp;', '&nbsp;&nbsp;'),$string));
}

/**
 * �����ļ�����Ϊ�ַ���
 *
 * @param  string     $filename  �ļ���
 * @return string     $str       ʧ�ܷ���false
 */
function file2str($filename){
	if(!is_file($filename)) return false;
	return @file_get_contents($filename);
}

/**
 * ������д��ָ���ļ�
 *
 * @param  string     $result    ����
 * @param  string     $filename  �ļ���
 * @return bool       ---        true/false����д���ļ��Ƿ�ɹ�
 */
function str2file($string,$filename){
	if(!mmkdir($filename,0,1) || (false !== stripos($filename, '..'))) return false;
	// $re = file_put_contents($filename,$string); 
	// file_put_contents() ���� �����ε��� fopen()��fwrite() �Լ� fclose() ����һ������Ч�����ȶ��� ���Ⱥ��߲���ˡ�
	$handle = @fopen($filename,"wb");
	if($handle){
		$re = fwrite($handle,$string);
		fclose($handle);
		return $re;	
	}else{
		return false;	
	}
}

/**
 * ��� �ֻ�ȷ���� �Ƿ���ȷ
 *
 * @param  string     $mod      �ֻ�����ģ��ID
 * @param  string     $msgcode  �����ȷ����
 * @param  string     $tel      ����ĵ绰����(��Ϊ�ղ���֤,��Ϊ��ͬʱ��֤�绰�����Ƿ���ͬ)
 * @return bool       ---       true/false
 */
function smscode_pass($mod,$msgcode='',$msgtel=''){
	global $m_cookie;
	$timestamp = TIMESTAMP;
	$ckkey = 'smscode_'.$mod;
	@list($stamp, $svcode, $tel) = maddslashes(explode("\t", authcode($m_cookie[$ckkey],'DECODE')),1);
	@$pass = !empty($svcode) && (TIMESTAMP - intval($stamp))<3600 && $svcode===$msgcode; // && $fmdata['lxdh']===$tel; 
	if(!empty($msgtel)) $pass = $pass && ($msgtel===$tel);
	return $pass;
}

/**
 * ��� ��֤�� �Ƿ���ȷ
 *
 * @param  string     $rname    ��֤����Ŀ����
 * @param  string     $code     �������֤��
 * @return bool       ---       true/false
 */
function regcode_pass($rname,$code=''){
	global $m_cookie,$cms_regcode,$verify;
	$timestamp = TIMESTAMP;
	if(!$cms_regcode || !in_array($rname,explode(',',$cms_regcode))) return true;
	empty($verify) && $verify = '08cms_regcode';
	@list($inittime, $initcode) = maddslashes(explode("\t", @authcode($m_cookie[$verify],'DECODE')),1);
	mclearcookie($verify);#��֤�����Ҳ�������ע����������������ƽ�...
	mclearcookie('t_t');
	
	if(($timestamp - $inittime) > 1800 || strtolower($initcode) != strtolower($code)){
		return false;
	}
	return true;
}

/**
 * ����/�����ַ���
 *
 * @param  string     $string    ԭʼ�ַ���
 * @param  string     $operation ����ѡ��: DECODE�����ܣ�����Ϊ����
 * @param  string     $key       ������
 * @return string     $result    �������ַ���
 */
function authcode($string, $operation = '', $key = '') {
	global $authorization;
	$key = md5($key ? $key : $authorization);
	$key_length = strlen($key);

	$string = $operation == 'DECODE' ? base64_decode($string) : substr(md5($string.$key), 0, 8).$string;
	$string_length = strlen($string);

	$rndkey = $box = array();
	$result = '';

	for($i = 0; $i <= 255; $i++) {
		$rndkey[$i] = ord($key[$i % $key_length]);
		$box[$i] = $i;
	}

	for($j = $i = 0; $i < 256; $i++) {
		$j = ($j + $box[$i] + $rndkey[$i]) % 256;
		$tmp = $box[$i];
		$box[$i] = $box[$j];
		$box[$j] = $tmp;
	}

	for($a = $j = $i = 0; $i < $string_length; $i++) {
		$a = ($a + 1) % 256;
		$j = ($j + $box[$a]) % 256;
		$tmp = $box[$a];
		$box[$a] = $box[$j];
		$box[$j] = $tmp;
		$result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
	}

	if($operation == 'DECODE') {
		if(substr($result, 0, 8) == substr(md5(substr($result, 8).$key), 0, 8)) {
			return substr($result, 8);
		} else {
			return '';
		}
	} else {
		return str_replace('=', '', base64_encode($result));
	}

}

/**
 * ��ת����
 *
 * @param  string   $s                 ��תurl
 * @param  bool     $replace            ---
 * @return string   $http_response_code ---
 */
function mheader($s,$replace = true,$http_response_code = 0){
	$s = str_replace(array("\r","\n"),'',$s);
	@header($s,$replace,$http_response_code);
	if(preg_match('/^\s*location:/is',$s)) exit();
}

/**
 * ��ָ����Ԥ�����ַ�(',",\,NULL)ǰ��ӷ�б�ܣ�֧�����飬ע�⣺�粻������GPC��Ҫ��force=1
 *
 * @param  string   $s     ԭʼ�ַ���������������
 * @param  bool     $force ǿ��ѡ��
 * @return string   $s     �������ַ���
 */
function maddslashes($s, $force = 0) {
	return cls_env::maddslashes($s, $force);
}

/**
 * �ж�ָ�����ļ��Ƿ���ͨ�� HTTP POST �ϴ���
 *
 * @param  string   $file   �ļ�
 * @return bool     ---     ����:1,0
 */
function mis_uploaded_file($file){
	return function_exists('is_uploaded_file') && (is_uploaded_file($file) || is_uploaded_file(str_replace('\\\\', '\\', $file)));
}

/**
 * ��[',']�������飬��ǰ�����[,]
 *
 * @param  array    $arr    ԭʼ����
 * @return string   ---     ���Ӻ���ַ����磺'23','43','3434'
 */
function mimplode($arr){
	return empty($arr) ? '' : "'".implode("','", is_array($arr) ? $arr : array($arr))."'";
}
/**
 * �����Ϣ���˳�
 *
 * @param  string $message   Ҫ�������Ϣ
 * @return NULL   ---        ---
 */
function mexit($message = ''){
	echo $message;
	output();
	exit();
}
/**
 * �������������
 *
 * @param  bool $force   �Ƿ�ǿ�����
 * @return NULL ---    ---
 */
function m_clear_ob($force = 0){
	global $phpviewerror;
	if($force || $phpviewerror != 3){
		ob_end_clean();		
		cls_env::mob_start();
	}
}
/**
 * ѹ�����
 *
 * @param  string $var       ������
 * @param  bool   $allowget  �Ƿ���get�ύ(δ��)
 * @return bool   ---        �������ַ���
 */
function output(){
	$content = ob_get_clean();
	cls_env::mob_start();
	echo $content;
}

/**
 * �����ύ
 *
 * @param  string $var       ������
 * @param  bool   $allowget  �Ƿ���get�ύ(δ��)
 * @return bool   ---        �������ַ���
 */
function submitcheck($var, $allowget = 0)
{
    # �ύ��ʱ
    if ( !empty($GLOBALS[$var]) )
    {/*   // todo ��ʱ����֤�뷽ʽ���治����
        # ��֤CSRF��ֻ��֤POST
        if ( isset($_POST[$var]) )
        {
            $cookie = cls_env::_COOKIE();
            if ( !isset($cookie[cls_env::_08_HASH]) || !isset($_POST[cls_env::_08_HASH]) )
            {
                return false;
            }
            if ( $_POST[cls_env::_08_HASH] != $cookie[cls_env::_08_HASH] )
            {
                cls_message::show('����ʱ��Ƿ��ύ��');
            }
        }
        
        # ͨ����֤����������һ��HASHֵ��Ŀǰ�÷�ʽ���ǻ᲻̫���ʣ���Ϊ��֤�󲻴�����ύ�ɹ�
        cls_env::getHashValue(true);*/
        
        return true;
    }    
    
	return false;
}
/**
 * ���cookie
 *
 * @param  string $ckname   cookie��
 * @return NULL   ---       ---
 */
function mclearcookie($ckname='userauth'){
    $ckname = preg_replace('/[^\w]/', '', $ckname);
	msetcookie($ckname,'',-86400 * 365);
}
/**
 * ����cookie
 *
 * @param  string $ckname   cookie��
 * @param  string $ckvalue  ֵ
 * @param  string $cklife   ��������(s)
 * @return NULL   ---       ---
 */
function msetcookie($ckname, $ckvalue, $cklife = 0, $httponly = false) {
	global $ckpre, $ckdomain, $ckpath,$cms_top;
    $ckname = preg_replace('/[^\w]/', '', $ckname);
	$ckdomain = getCookieDomain();
	setcookie($ckpre.$ckname, $ckvalue, $cklife ? TIMESTAMP + $cklife : 0, $ckpath, $ckdomain, $_SERVER['SERVER_PORT'] == 443 ? 1 : 0, $httponly);
}

function getCookieDomain()
{
	global $ckdomain, $cms_top;
	$ckdomain = empty($ckdomain) && !empty($cms_top) ? '.'.$cms_top : $ckdomain;
    return $ckdomain;
}

/**
 * ����HTML�ַ����룬��htmlspecialchars��չ�����Դ�array����
 *
 * ��������HTML�ַ����룺&"'<> --> &amp; &quot; &#039; &lt; &gt;
 *
 * @param  string $string   ԭ�ַ�����array��string
 * @param  string $quotes   ѡ�2:������˫����; 3:����˫���ź͵�����(Ĭ��); 0:�������κ�����;
 * @return string $string   �������ַ���
 */
function mhtmlspecialchars($string, $quotes = ENT_QUOTES, $delete_rep = false) {
	if(is_array($string)) {
		foreach($string as $key => $val) $string[$key] = mhtmlspecialchars($val, $quotes, $delete_rep);
	} else { // 2:ENT_COMPAT:Ĭ��,������˫����; 3:ENT_QUOTES:����˫���ź͵�����; 0:ENT_NOQUOTES:�������κ�����;
        if ( $delete_rep )
        {
            $string = str_replace(array(' ', '%20', '%27', '*', '\'', '"', '/', ';', '#', '--'), '', $string);
        }
		$string = htmlspecialchars($string, $quotes);
	}
	return $string;
}
/**
* ����HTML�ַ����룬���Դ�array������������һЩ�����ַ���&amp;
 *
 * @param  string $string   ԭ�ַ�����array��string
 * @return string $string   �������ַ���
 */
function mhtmlspecialkeep($string) { // htmlspecialchars���벿���ַ�������ĳЩ�����ַ�
	if(is_array($string)) {
		foreach($string as $key => $val) $string[$key] = mhtmlspecialkeep($val);
	} else {
		$string = preg_replace(
		'/&amp;((#(\d{3,5}|x[a-fA-F0-9]{4})|[a-zA-Z][a-z0-9]{2,5});)/', 
		'&\\1',
		str_replace(array('&', '"', '<', '>'), array('&amp;', '&quot;', '&lt;', '&gt;'), $string)
		);
	}
	return $string;
}
/**
 * ��ȡ��չ�ļ���
 *
 * @param  string $filename   ԭ�ļ���
 * @return string ---         ��չ�ļ���
 */
function mextension($filename) {
	return trim(substr(strrchr($filename, '.'), 1, 10));
}

function misuploadedfile($file) {
	return function_exists('is_uploaded_file') && (is_uploaded_file($file) || is_uploaded_file(str_replace('\\\\', '\\', $file)));
}
/**
 * ��ǰip�Ƿ�����(���ʻ����)
 *
 * @param  string $ip         IP��ַ
 * @param  string $accesslist �����IP��ַ�б�
 * @return bool   ---         0:��ֹ, 1:����
 */
function ipaccess($ip, $accesslist) {
	//ע���������¶Իس����д���ͬ,����"\r\n", "\r", "\n"��Ҫת��Ϊ'|'
	return preg_match("/^(".str_replace(array("\r\n", "\r", "\n", ' '), array('|', '|', '|', ''), preg_quote($accesslist, '/')).")/", $ip);
}
/**
 * Ҳ��ִ�����,��ת��������
 * Demo: amessage('��Ϣ���ʧ��',axaction(6,"path/file.php?"));
 *
 * @param  string $mode       ����������ָ�����
 * @param  string $url        ��תurl
 * @return string $re         url��js����
 */
function axaction($mode, $url = ''){
	global $infloat, $handlekey;
    $url = cls_env::repGlobalURL($url);
	if(!$infloat)return $url;
	$ret = '';
    $handlekey === 0 && $handlekey = '';
	if((!$mode || $mode & 32) && $url){//0�����32����������ת
		$ret .= "floatwin('update_$handlekey','$url');";
	}
	if($mode & 1){//����1��ˢ�±�����
		$ret .= "floatwin('update_$handlekey');";
	}
	if($mode & 2){//����2���رձ�����
		$ret .= "floatwin('close_$handlekey',-1);";
	}
	$ret = 'javascript:' . ($ret ? ('setDelay(\'' . str_replace("'", "\\'", $ret) . '\',t);') : '');
	if($mode & 4){//����4��ˢ�¸�����
		$ret .= "floatwin('updateparent_$handlekey');";
	}
	if($mode & 16){//����16��ˢ�¸������ڣ�Ҫ�ڹرո�����ǰˢ��
		$ret .= "floatwin('updateup2_$handlekey',-1);";
	}
	if($mode & 8){//����8���رո�����
		$ret .= "floatwin('closeparent_$handlekey',-1);";
	}
    if ( $mode & 64 ) // �رձ����ڲ���ת������
    {        
		$ret .= "floatwin('closelocation_$handlekey', '$url');";
    }
	return $ret;
}
/**
 * ���ݻ�Ա��,�ж��Ƿ�������ϵ��ʽ��һ����
 *
 * @param bool $hid_connect 1:��,0��
 */
function is_hidden_connect(){
	$curuser = cls_UserMain::CurUser();
	$hid_connect = false;
	$exconfigs = cls_cache::cacRead('exconfigs',_08_EXTEND_SYSCACHE_PATH);
	$hid_configs = $exconfigs['hid_connect'];
	if(!empty($hid_configs['groups'])){
		$hid_groups = explode(',',$hid_configs['groups']);
		foreach($hid_groups as $g){
		  $g = $curuser->info["grouptype$g"];
		  if($g && strpos($hid_configs['hid'],$g)){
			 $hid_connect = true;
			 break;
		  }
		}
	}
	return $hid_connect;
}

/**
 * ���ָ����COOKIEֵ
 *
 * @param array $cookies Ҫ��յ�COOKIE��
 * @param bool  $strpos  �Ƿ�ʹ��strposƥ������
 */
function cleanCookies($cookies, $strpos = false)
{
    global $m_cookie;
    settype($cookies, 'array');
    foreach($cookies as $k)
    {
        if ( $strpos != false )
        {
            $keys = array_keys($m_cookie);
            foreach ($keys as $key) 
            {
                if ( false !== strpos($key, $k) )
                {
                    mclearcookie($key);
                }
            }
            
        }
        else
        {
        	if(isset($m_cookie[$k]))
            {
                mclearcookie($k);
            }
        }        
    }
}

/**
 * ȡ���û����ƻ�ϵͳ��չ����ڽű�
 * δ������ڻ���ڽű������ڣ������ؿ�ֵ
 * ���ڱ�����ǹٷ���չ�Ľű���ֻ��Ҫ�����Ƿ����û�������ڣ���ʱ����onlycustomΪ1
 * querys Ϊ��ڴ���Ĳ���Դ���飬��������Ĭ��Ϊ$_SERVER['QUERY_STRING']�õ�������
 */
function extend_script($main = '',$onlycustom = 0,$querys = array()){
	foreach(array('extendscripts','customscripts',) as $k) $$k = cls_cache::cacRead($k,_08_EXTEND_SYSCACHE_PATH);
	if(!$querys || !is_array($querys)) $querys = cls_env::_GET();
	foreach(array('custom','extend') as $var){
		if($onlycustom && $var == 'extend') break;
		if($cfg = &${$var.'scripts'}[$main]){
			foreach($cfg as $k => $v){//������ж�������
				if($v && is_array($v)){
					foreach($v as $key => $val){
						if(is_array($val) && $val){
							if(empty($querys[$key]) || !in_array($querys[$key],$val)) break 2;
						}elseif($val){
							if(empty($querys[$key]) || $querys[$key] != $val) break 2;
						}elseif(!empty($querys[$key])) break 2;
					}
				}
				return $k;
			}
		}
	}
	return '';
}

/**
 * ��ȡѡ���ı��Ļ����ļ�
 *
 * @param string $file �ļ����ƣ�������׺��
 * @param string $path ����·��
 * @return array �����ļ���������
 */
function read_select_file($file, $path = '')
{
    empty($path) && $path = _08_TEMP_TAG_CACHE;
    _08_FileSystemPath::checkPath($path, true);
    return cls_cache::cacRead($file, $path, true);
}


/**
 * ���������ؼ��֣�����󣺿�����%_�����ַ���
 * demo: AND (a.subject ".sqlkw($keyword).")";
 *
 * @param  string $keyword Ҫת�����ַ���
 * @param  string $multi =1ʱ��*���ո񵱳�ͨ�������
 * @return string $sqlstr ����sql���ַ��������� LIKE
 */
function sqlkw($keyword,$multi=1){
	$keyword = addcslashes($keyword,'%_');
	$multi && $keyword = str_replace(array(' ','*'),'[08cmsKwBlank]',$keyword);
	return " LIKE '%$keyword%' ";
}

/**
 *  ��$content�е�ռλ��{xxx}�����滻($arr['xxx'])
 *
 * @param  string  $content  ԭʼ����
 * @param  array   $arr      ֵ����
 * @param  int     $prefix   ռλ��ǰ׺
 * @param  int     $suffix   ռλ����׺
 * @return string  $content  �滻�������
 */
function key_replace($content = '',$arr = array(),$prefix = '{',$suffix = '}'){
	if(!$content || !$arr) return $arr;
	return preg_replace("/\{(.+?)\}/ies","_key_replace('\\1',\$arr,\$prefix,\$suffix)",$content);
}
// ֧��$GLOBALS��$timestamp,$cms_abs; ��ʽ�磺{pre}enddate < '{timestamp}'
// ������$arr�ļ�ֵ��û���ҵ�����$GLOBALS����
function _key_replace($key = '',$arr = array(),$prefix = '{',$suffix = '}'){
	if(!$key || (!isset($arr[$key]) && !isset($GLOBALS[$key]))) return $prefix.$key.$suffix;	
	return isset($arr[$key]) ? $arr[$key] : $GLOBALS[$key];
}

/**
 * ѡ�����ֶδ����ݿⴢ��ֵ�õ���ʾֵ
 *
 * @param  string  $val   ���ݿⴢ��ֵ
 * @param  array   $field �ֶ�������Ϣ
 * @param  int     $num   �Զ�ѡ,���Ƹ���,Ĭ��0����
 * @return string  $str   ��ʾֵ
 */
function view_field_title($val,$field,$num = 0){
	if(!$val || !$field || !in_array($field['datatype'],array('select','mselect','cacc',))) return $val;
	if(in_array($field['datatype'],array('mselect','select',))){
		$tmp = explode("\n",$field['innertext']);
		$arr = array();
		foreach($tmp as $v){
			$t = explode('=',str_replace(array("\r","\n"),'',$v));
			$t[1] = isset($t[1]) ? $t[1] : $t[0];
			$arr[$t[0]] = $t[1];
		}
		$multi = $field['datatype'] == 'mselect' ? 1 : 0;
	}elseif($field['datatype'] == 'cacc'){
		$arr = empty($field['coid']) ? cls_cache::Read('catalogs') : cls_cache::Read('coclasses',$field['coid']);
		foreach($arr as $k => $v) $arr[$k] = $v['title'];
		$multi = empty($field['max']) ? 0 : 1;
	}else return $val;
	if($multi){
		$vals = explode($field['datatype'] == 'cacc' ? ',' : "\t",$val);
		$ret = '';$i = 1;
		foreach($vals as $k){
			if(isset($arr[$k])){
				if(!empty($num) && ++$i > $num) break;
				$ret .= ($ret ? ' ' : '').$arr[$k];
			}
		}
		return $ret;
	}else return @$arr[$val];
}

/**
 * ����Ȩ�޷�����������Ȩ��, ���践����Ȩ�޵�ԭ����ʹ��mem_noPm
 *
 * @param  array  $info  ��Ա��������Ϣ
 * @param  int    $pmid  Ȩ�޷���ID
 * @return bool   $str   ֻ����true(��Ȩ��)/false(��Ȩ��)
 */
function mem_pmbypmid($info = array(),$pmid = 0){
	return _mem_noPm($info,$pmid) ? false : true;
}


// ��ȡstr����ĸ
function autoletter($str = ''){
	if(!$str) return '';
	return cls_string::FirstLetter($str);
}

// ˵����ϵͳ��ҳ�ľ�̬��ʽ��$Nodemode��1Ϊ�ֻ���
function idx_format($Nodemode = 0){
	return $Nodemode ? '' : cls_env::GetG('homedefault');
}
/**
 * ��ѡ��Ŀ�Ķ�ģʽ����
 * @param  int  $info  ����ģʽ��0��ȫ�����ã�1�����ģʽ��2���Ƴ�ģʽ
 * @param  int  $limit  ��ѡ����������
 * @param  string  $nids  ��ѡ���ѡ���ִ�
 * @param  string  $oids  ԭѡ���ִ�
 * @param  int  $both  �Ƿ��ڷ����ִ������˼���','
 * @return string ������ɺ��ѡ���ִ�
 */
function idstr_mode($mode,$limit,$nids,$oids,$both = 0){
	if($mode && $limit){
		$nids = array_filter(explode(',',$nids));
		$oids = array_filter(explode(',',$oids));
		$nids = $mode == 1 ? array_unique(array_merge($oids,$nids)) : array_diff($oids,$nids);
		$nids && $nids = array_slice($nids,-$limit,$limit);
		return $nids ? ($both ? (','.implode(',',$nids).',') : implode(',',$nids)) : '';
	}else return $nids;
}

//$infoid=�ĵ�/��Ա/����/��ϵ:id
//$modid=chid,mchid,cuid,coid �ֱ���� �ĵ�/��Ա/����/��ϵ ģ��ID
//$type=a,m,cu,co �ֱ���� �ĵ�/��Ա/����/��ϵ ����
//$field=clicks �ֶ�
function view_count($infoid,$modid,$type,$field){
	global $cms_abs;
	echo cls_phpToJavascript::str_js_src($cms_abs . _08_Http_Request::uri2MVC("ajax=view_count&infoid={$infoid}&modid={$modid}&type={$type}&field={$field}"));
}

#################### ���ݺ��� ##################################################

/**
 * ����ip������ip��Ӧ�������ַ
 *
 * @param  string $ip IP��ַ
 * @return string $re ���������ַ����
 */
function ipaddress($ip) {
	return cls_ipAddr::conv($ip,'local');
}

####################���º�����/include/common.fun.php��ֲ����##################
if ( !function_exists('js_callback') )
{
    function js_callback($var = 'succeed'){
    	global $callback;
    	if($callback){
    		ob_clean();
    		header("Content-Type: application/javascript;charset=".cls_env::getBaseIncConfigs('mcharset'));
    		mexit("js_callback(" . jsonEncode($var) . ",'$callback')");
    	}
    }
}

##############################################################################

