<?php
/**
   Fafil(Safe fillter), ������ʹ�ã������ⲿ��ַ�����ύ��
*/
// TIMESTAMP, M_ROOT
// cls_env::mconfig('cmsurl'), authcode(), cls_env::TopDomain(), mmkdir()
// 

class cls_Safefillter{ 
	
	static $rnd_seed    = 'bbmU-dcxy-Xwrm-ECZ2'; // ��ȫ�����; !!!Ϊ�˰�ȫ,�ɾ����ĸ�; �����ַ�,18~21λ,
	static $rnd_varname = 'rnd__08';             // ����ǰ׺; !!!Ϊ�˰�ȫ,�ɾ����ĸ�; 08ϵͳ��,�������ĸ��ͷ; ����ϵͳ�¿���_��ͷ;
	static $rnd_timeout = 60;                    // ��ʱʱ��(min)
	static $rnd_split   = '~';                   // �ָ����,��Ҫ��url�����ַ�; �����磺~_-/;|@��; ����Ҫ��ϵͳ������������;�ķָ�ų�ͻ
	static $rnd_debug   = 1;                     // �Ƿ����...
	
	/* ��ʼ��url
	  Demo : <a href="{$cms_abs}etools/wdadd.php<?php echo '&'.cls_Safefillter::urlInit();?>">��Ҫ����</a> 
	*/
	static function urlInit(){
		return self::$rnd_varname."=".self::_getStamp();
	}
	
	/* ǩ��url(����ajax����)
	  Demo : /tools/safefillter.php?act=surl 
	*/
	static function urlSign(){
		self::refCheck();
		$sign = cls_Safefillter::urlInit();
		echo "var _url_sign = '$sign';";
	}
	
	/* ��֤url
	  Demo : cls_Safefillter::urlCheck();
	*/
	static function urlCheck($die=1){
		self::refCheck(); //exit('IP_Fobidden'); 
		$re = self::_chkStamp();
		if($re && $die){ 
			self::_stop(__CLASS__, __FUNCTION__, $re);
		}
		return $re; 
	}
	
	/* ��ʼ��form (??? ����/index.php?/ajax/���,�ɲ�����֮ǰ��ϵͳ)
	  fmid : ��id
	  Demo : <script type='text/javascript' src='{cms_abs}tools/safefillter.php?act=init&fmid=addcu12'></script>;
	*/
	static function formInit(){
		$fmid = self::_req('fmid');
		$elmid = $fmid.self::$rnd_varname;
		$elmfm = self::$rnd_varname.'_fmid';
		$elms = "<input name='$elmfm' id='$elmfm' type='hidden' value='$fmid'><input name='$elmid' id='$elmid' type='hidden' value=''>";
		echo "document.write(\"$elms\");\n";
		echo "function _{$fmid}_setAjaxVals(v){document.getElementById('$elmid').value = v;}\n"; 
	}
	
	/* ajax����form (??? ����/index.php?/ajax/���,�ɲ�����֮ǰ��ϵͳ)
	  fmid : ��id
	  Demo : $.getScript({cms_abs}tools/safefillter.php?act=ajax&fmid=addcu12), function(){
				try{_addcu12_setAjaxVals(_addcu12_stamp);}catch(e){}
			 });
	  ԭ�� : js���¼���������ж�; 
	  		 ��ȡ����{cms_abs}tools/safefillter.php?act=ajax&fmid=addcu12�õ�js���벢����; 
			 ִ��js�������_addcu12_setAjaxVals(_addcu12_stamp);
	*/
	static function formAjax(){
		self::refCheck();
		$fmid = self::_req('fmid');
		$stamp = self::_getStamp(0);
		echo "var _{$fmid}_stamp = '$stamp';";
	}
	
	/* ��֤form
	  * ref : ��Դ��ַ
	  * fmid : ��id,��ʡ��
	  Demo : cls_Safefillter::urlCheck('register.php','cmsregister');
	*/
	static function formCheck($ref='',$fmid=''){
		self::refCheck($ref);
		$fmid || $fmid = self::_req(self::$rnd_varname.'_fmid');
		if($re = self::_chkStamp($fmid)) self::_stop(__CLASS__, __FUNCTION__, $re);
	}

	//����Ƿ��ⲿ�ύ������Url
	//expath : ·��ƥ�䲿��,��Ϊ��
	//die : Ĭ��ֱ��die, ��Ϊ���򷵻������ж�
	//return : Ĭ��ֱ��die; false:�����ⲿ�ύ���ĵ�ַ; true(string):�����Ϣ,��ʾ���ⲿ�ύ��ֱ��������ַ����
	//demo: if(cls_Safefillter::refCheck('',0)) die("��������{$cms_abs}������");
	//demo: if(cls_Safefillter::refCheck('/dgpeace/_php_test.php'));
	static function refCheck($expath='',$die=1){
		$re = '';
		$from = empty($_SERVER["HTTP_REFERER"]) ? '' : $_SERVER["HTTP_REFERER"];
		$froma = parse_url($from);
		//Ϊ��:(�����ַ��)
		if(empty($from)) $re = 'Null'; 
		// ƥ��:����/����+�˿�
		$from = self::_urlParse($from);
		$hnow = self::_urlParse($_SERVER['HTTP_HOST']); //HTTP_HOST = SERVER_NAME:SERVER_PORT
		if(@$from['host']!==@$hnow['host']){ 
			$re = $from['host']; 
		}
		// ƥ��:·��
		$npath = cls_env::mconfig('cmsurl'); // ��:/house/
		if($expath) $npath = str_replace(array('///','//'),'/',"$npath/$expath"); 
		if(strlen($npath)>0 && !preg_match('/^'.preg_quote($npath,"/").'/i',$froma['path'])){ 
			$re = $npath;	
		} 
		if($re && $die){ 
			self::_stop(__CLASS__, __FUNCTION__, $re);
		}
		return $re; 
	}

	static function _getStamp($isurl=1){
		$stamp = TIMESTAMP;
		$encMD5 = md5(self::$rnd_seed.$stamp);
		$encAuth = authcode($stamp,'');
		$isurl && $encAuth = urlencode($encAuth);
		$encBoth = $encMD5.self::$rnd_split.$encAuth;
		return $encBoth;
	}
	static function _chkStamp($fmid=''){ 
		$fmid || $fmid = self::_req('fmid');
		$a = explode(self::$rnd_split,self::_req($fmid.self::$rnd_varname)); 
		if(empty($a[0]) || empty($a[1])) return 'Error';
		$stamp = intval(authcode($a[1],'DECODE'));
		$encMD5 = md5(self::$rnd_seed.$stamp); 
		if(TIMESTAMP-$stamp>self::$rnd_timeout || $encMD5!==$a[0]){
			return 'Timeout';
		}
	}

	/*	��ȡ: host(������+�˿�) �� path(·��)
	--- Demo --- 
	$url1 = "http://m.08cms.com:808/shopinfo.d?m=fbyzm&mobile=13537432146&city=bj#aa=33";
	$url2 = "http://www.08cms.com:808/example/index.php/dir/test.php?aaa=bbb";
	$url3 = "http://192.168.1.11:888/house/dgpeace/_php_test.php?aaaa=bbb";
	$url4 = "http://[2001:410:0:1:250:fcee:e450:33ab]:8443/file.php?aa=bb"; 
	echo 'aab:<pre>'.var_dump(cls_Safefillter::_urlParse($url4)).'</pre><br>';
	*/
	static function _urlParse($url){	
		$aurl = parse_url($url); //var_dump($aurl);
		$top = cls_env::TopDomain(@$aurl['host']);
		if(!empty($top)){ //IP(��ipv6)
			$aurl['host'] = $top;
		}
		$host = @$aurl['host'].(isset($aurl['port']) ? ':'.$aurl['port'] : '');
		$path = empty($aurl['path']) ? '' : $aurl['path'];
		return array('h'=>$host,'p'=>$path);
	}
		
	static function _stop($class,$func,$msg){
		$msg = "$class::$func Fobidden : [$msg]";
		$ip = self::_userIP();
		if(self::$rnd_debug){ //����
			$dmsg = date('Y-m-d H:i:s')." --- $msg";
			if(!is_dir(M_ROOT.'dynamic/debug/')) @mmkdir(M_ROOT.'dynamic/debug/',0);
			$logfile = M_ROOT.'dynamic/debug/safil_'.date('Y_md').'.sflog';
			!is_file($logfile) && @touch($logfile);
			$dold = "\n\r\n".@file_get_contents($logfile);
			$data = $dmsg." --- ref:".@$_SERVER["HTTP_REFERER"]." --- ua:".$_SERVER['HTTP_USER_AGENT']." --- ip:$ip";
			@file_put_contents($logfile,$data.$dold);
		}
		die($msg." --- ($ip@".date('Y-m-d H:i:s').')');
	}
	
	// ��ȡ�ͻ���IP��ַ
	static function _userIP($flag=1){
		$a = array('x'=>'HTTP_X_FORWARDED_FOR','r'=>'REMOTE_ADDR','c'=>'HTTP_CLIENT_IP'); //'r'=>'HTTP_X_REAL_FORWARDED_FOR',
		$ip = '';
		foreach($a as $k=>$v){
			$v = str_replace(' ','',$v);
			if(isset($_SERVER[$v]) && !strstr($ip,$_SERVER[$v])){
				$ip .= ';'.($flag ? "$k," : '').$_SERVER[$v];
			}
		}
		$ip = substr($ip,1);
		return $ip;
	}
	
	// Reuest����
	static function _req($key){ //,$def='',$type='Title',$len=255
		if(isset($_POST[$key])){
			$val = $_POST[$key];
		}elseif(isset($_GET[$key])){
			$val = $_GET[$key];	
		}else{
			$val = '';
		} 
		$val = is_array($val) ? implode(',',$val) : $val; 
		//$val = preg_replace("/[^a-zA-Z0-9_|\~|\-|\.|\@]/",'',$val);
		$val = str_replace(array(">","<","'",'"',"\n","\r","\\",),'',$val);
		return $val; 
	}
		
	// --- End ----------------------------------------
	
}

