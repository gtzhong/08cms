<?php
// ��ȡip��ַ-taobao
class iptaobao{
	
	public $url = 'http://ip.taobao.com/service/getIpInfo.php?ip='; 
	public $cset = 'utf-8';
	
	// Http��ȡ����
    function getAddr($ip){
		if(empty($ip)) return '';
		include_once M_ROOT."include/http.cls.php";
		$get = new http();
		$get->timeout = 3; //$get->setCookies(60); 
		$addr = $get->fetchtext($this->url.$ip,'GET'); //��ȡԭʼ����
		if(empty($addr)) return ''; 
		$arr = json_decode($addr,1); //utf-8,ת��֮ǰִ��
		$addr = var_export($arr,1);
		$cs08 = cls_env::getBaseIncConfigs('mcharset');
		$addr = cls_string::iconv($this->cset,$cs08,$addr);
		return $addr;
    }
	
	// ���˴���
	function fill($addr){
		//�й�,CN,����,300000,����ʡ,320000,�γ���,320900,,-1,��ͨ,100026,122.96.199.133 
		//{"code":0,"data":{"country":"\u4e2d\u56fd","country_id":"CN","area":"\u534e\u4e1c","area_id":"300000","region":"\u6c5f\u82cf\u7701","region_id":"320000","city":"\u76d0\u57ce\u5e02","city_id":"320900","county":"","county_id":"-1","isp":"\u8054\u901a","isp_id":"100026","ip":"122.96.199.133"}}
		@eval("\$arr = $addr;");
		$addr = implode(',',empty($arr['data']) ? '-' : $arr['data']);
		$addr = preg_replace("/\d{1,3}([.][0-9]{1,3}){3,15}/",'',$addr);
		return $addr;
	}
}

