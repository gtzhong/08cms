<?php
// ��ȡip��ַ-ip138
class ipip138{
	
	public $url = 'http://www.ip138.com/ips138.asp?action=2&ip='; 
	public $cset = 'gb2312';
	
	// ��ȡ����
    //function getAddr($ip, $text=1){}
	
	// ���˴���
	function fill($addr){
		//����ʡ�γ���  ��ͨ
		//<li>��վ�����ݣ�����ʡ�γ���  ��ͨ</li><li>�ο�����һ������ʡ�γ��� ��ͨ</li>
		$arrText = array('<ul class="ul1"><li>','</li><li>');  
		$addr = cls_ipAddr::getVal($addr, $arrText);
		//$addr = substr($addr,12); //����Ҫ���Ǳ���
		return $addr;
	}
}

