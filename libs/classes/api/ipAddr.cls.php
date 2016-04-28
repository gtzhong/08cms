<?php
/**
 * ����ip����ȡip��ַ�ĵ���λ����Ϣ
 *
 * @Demo      $ipa = new cls_ipAddr($api); echo $ipa->addr($ip,$text);
 *            $addr = cls_ipAddr::conv('59.37.255.230','sina');
 * ��չapi    ��ǰ: local,pcoln,s1616,sina,taobao, ��չapi��ο�Ŀ¼include/ipapi��Ӧ����һ��api�ļ�
 *            ����޽ӿ��ļ�,��Ĭ��Ϊlocal�ӿڡ�
 * @author    Peace <xpigeon@163.com>
 * @copyright Copyright (C) 2008 - 2014 08CMS, Inc. All rights reserved.
 *
 */
class cls_ipAddr{
	
	// Ĭ�Ͻӿ�
	private $api = 'local'; // local,sina,...
	private $class = ''; // ipSina
	
	// api  : local,pcoln,s1616,sina,taobao, ��չapi��ο�Ŀ¼include/ipapi��Ӧ����һ��api�ļ�
    function __construct($api='local') { 
		_08_FilesystemFile::filterFileParam($api);
		$this->api = $api ? $api : 'local'; 
		$this->class = 'ip'.strtolower($this->api); //ucfirst();
		$file = _08_INCLUDE_PATH.'/ipapi/'.$this->class.'.cls.php';
		if(is_file($file)){
			require_once($file);	
		}else{ //Ĭ�ϱ���api
			$this->api = 'local';
			$this->class = 'iplocal';
			require_once(_08_INCLUDE_PATH.'/ipapi/'.$this->class.'.cls.php');		
		}
    }
	
	// ��ȡ����
	// text : 1:�����ı�, 0:����ԭʼ����
    function addr($ip, $text=1){
		//if(empty($ip)) return '';
		//if(is_numeric($ip)) $ip = self::long2ip($ip);
		//���IP��ַ
		if(!preg_match("/\b(((?!\d\d\d)\d+|1\d\d|2[0-4]\d|25[0-5])(\b|\.)){4}/", $ip)) {
			return 'IP Error';
		}
		//�������ip��ַ  10.0.0.0~10.255.255.255,  172.16.0.0~172.31.255.255,  192.168.0.0~192.168.255.255
		$na = explode('.',$ip);
		if($na[0] == 10 || $na[0] == 127 || ($na[0] == 192 && $na[1] == 168) || ($na[0] == 172 && ($na[1] >= 16 && $na[1] <= 31))){
			return 'LAN'; //Local Area Network
		}
		$ipa = new $this->class();
		if(method_exists($ipa,'getAddr')){ //��鷽��...
			$addr = $ipa->getAddr($ip);
		}else{
			$addr = $this->http($ipa->url,$ipa->cset,$ip);
		}
		if($text){
			$addr = $ipa->fill($addr); //���ӿڷֱ���
		}
		return $addr;
    }
	
	// ת��:255.255.255.255=>4294967295
    static function ip2long($ip){ 
		return sprintf("%u", ip2long($ip)); 
    }
	// ת��:4294967295=>255.255.255.255
    static function long2ip($int){ 
		return long2ip($int); 
    }
	// *** ��ȡһ��xml��һ��ֵ �� html ��һ��innerHTML
	static function getVal($xStr,$flag){  
		if(!is_array($flag)){
			$a = array("<$flag>","</$flag>");
		}else{
			$a = $flag;	
		}
		$p1 = strpos($xStr, $a[0]);
		$p2 = strpos($xStr, $a[1]);
		$len = strlen($a[0]);
		if($p1 && $p2>$p1){
			$re = substr($xStr, $p1+$len, $p2-$p1-$len);	
		}else{
			$re = '';	
		}
		return $re;
	}
	
	// Http��ȡ����
    function http($url, $cset, $ip){
		if(empty($ip)) return '';
		include_once M_ROOT."include/http.cls.php";
		$get = new http();
		$get->timeout = 3; //$get->setCookies(60); 
		$addr = $get->fetchtext($url.$ip,'GET'); //��ȡԭʼ����
		if(empty($addr)) return ''; 
		$cs08 = cls_env::getBaseIncConfigs('mcharset');
		//echo "$cset,$cs08,$addr";
		$addr = cls_string::iconv($cset,$cs08,$addr);
		return $addr;
    }
	
	// ���⾲̬����
	// api  : �ӿ�
	// text : 1:�����ı�, 0:����ԭʼ����
    static function conv($ip,$api='local',$text=1){	
		$ipa = new cls_ipAddr($api);
		return $ipa->addr($ip,$text);
    }	
}
