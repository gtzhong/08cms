<?php
// ��ȡip��ַ-s1616
class ips1616{
	
	public $url = 'http://chaxun.1616.net/s.php?type=ip&output=json&callback=data&v='; 
	public $cset = 'utf-8';
	
	// ��ȡ����
    //function getAddr($ip, $text=1){}
	
	// ���˴���
	function fill($addr){
		//����ʡ�γ��� ��ͨ 
		//data({"Ip":"122.96.199.133","Isp":"����ʡ�γ��� ��ͨ","Browser":"","OS":"Windows 7","QueryResult":1}) 
		$arrText = array('Isp":"','","Browser');
		$addr = cls_ipAddr::getVal($addr, $arrText);
		return $addr;
	}
}

