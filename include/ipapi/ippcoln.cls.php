<?php
// ��ȡip��ַ-pcoln
class ippcoln{
	
	public $url = 'http://whois.pconline.com.cn/jsFunction.jsp?callback=jsShow&ip='; 
	public $cset = 'gb2312';
	
	// ��ȡ����
    //function getAddr($ip, $text=1){}
	
	// ���˴���
	function fill($addr){
		//����ʡ�γ��� ��ͨ',' 
		//if (window.jsShow){jsShow('����ʡ�γ��� ��ͨ','');} 
		$arrText = array("jsShow('","');}");
		$addr = cls_ipAddr::getVal($addr, $arrText);
		$addr = str_replace("','",',',$addr);
		return $addr;
	}
}

