<?php
/*
 * �㷨���룺
http://www.fuziba.com/2012/08/01/php%E8%AF%BB%E5%8F%96%E7%BA%AF%E7%9C%9Fip%E6%95%B0%E6%8D%AE%E5%BA%93/
 * ��ַ�⣺
http://pc6.com/softview/SoftView_41490.html (����IP���ݿ�)
���ذ�װ����qqwry.dat��ȡ�����ŵ�[/include/ipapi/ip_qqwry.dat]���ɸ���Ŀ¼����������IP��ַ�⣻����ж�ذ�װ�ĳ���...
*/
// ��ȡip��ַ
class iplocal{
	
	public $url = 'local'; 
	public $cset = 'gb2312'; //bin,gb2312
	
	// ��ȡ����
    function getAddr($ip){
		//IP�����ļ�·��
		$dat_path = _08_INCLUDE_PATH.'/ipapi/ip_qqwry.dat';
		//��IP�����ļ�
		if(!$fd = @fopen($dat_path, 'rb')){
			return 'Qqwry  Error';
		}
		
		//�ֽ�IP�������㣬�ó�������
		$ipNum = cls_ipAddr::ip2long($ip); 
		//��ȡIP����������ʼ�ͽ���λ��
		$DataBegin = fread($fd, 4);
		$DataEnd = fread($fd, 4);
		$ipbegin = implode('', unpack('L', $DataBegin));
		if($ipbegin < 0) $ipbegin += pow(2, 32);
		$ipend = implode('', unpack('L', $DataEnd));
		if($ipend < 0) $ipend += pow(2, 32);
		$ipAllNum = ($ipend - $ipbegin) / 7 + 1;
	 
		$BeginNum = 0;
		$EndNum = $ipAllNum;
		$ip1num = $ip2num = 0; //Peace����Notic����
		$ipAddr1 = $ipAddr2 = ''; //Peace����Notic����
	 
		//ʹ�ö��ֲ��ҷ���������¼������ƥ���IP��¼
		while($ip1num>$ipNum || $ip2num<$ipNum) {
			$Middle= intval(($EndNum + $BeginNum) / 2);
			//ƫ��ָ�뵽����λ�ö�ȡ4���ֽ�
			fseek($fd, $ipbegin + 7 * $Middle);
			$ipData1 = fread($fd, 4);
			if(strlen($ipData1) < 4) {
				fclose($fd);
				return 'System Error';
			}
			//��ȡ����������ת���ɳ����Σ���������Ǹ��������2��32����
			$ip1num = implode('', unpack('L', $ipData1));
			if($ip1num < 0) $ip1num += pow(2, 32);
			//��ȡ�ĳ���������������IP��ַ���޸Ľ���λ�ý�����һ��ѭ��
			if($ip1num > $ipNum) {
				$EndNum = $Middle;
				continue;
			}
			//ȡ����һ��������ȡ��һ������
			$DataSeek = fread($fd, 3);
			if(strlen($DataSeek) < 3) {
				fclose($fd);
				return 'System Error';
			}
			$DataSeek = implode('', unpack('L', $DataSeek.chr(0)));
			fseek($fd, $DataSeek);
			$ipData2 = fread($fd, 4);
			if(strlen($ipData2) < 4) {
				fclose($fd);
				return 'System Error';
			}
			$ip2num = implode('', unpack('L', $ipData2));
			if($ip2num < 0) $ip2num += pow(2, 32);
			//û�ҵ���ʾδ֪
			if($ip2num < $ipNum) {
				if($Middle == $BeginNum) {
					fclose($fd);
					return 'Unknown';
				}
				$BeginNum = $Middle;
			}
		}
	   $ipFlag = fread($fd, 1);
		if($ipFlag == chr(1)) {
			$ipSeek = fread($fd, 3);
			if(strlen($ipSeek) < 3) {
				fclose($fd);
				return 'System Error';
			}
			$ipSeek = implode('', unpack('L', $ipSeek.chr(0)));
			fseek($fd, $ipSeek);
			$ipFlag = fread($fd, 1);
		}
	 
		if($ipFlag == chr(2)) {
			$AddrSeek = fread($fd, 3);
			if(strlen($AddrSeek) < 3) {
				fclose($fd);
				return 'System Error';
			}
			$ipFlag = fread($fd, 1);
			if($ipFlag == chr(2)) {
				$AddrSeek2 = fread($fd, 3);
				if(strlen($AddrSeek2) < 3) {
					fclose($fd);
					return 'System Error';
				}
				$AddrSeek2 = implode('', unpack('L', $AddrSeek2.chr(0)));
				fseek($fd, $AddrSeek2);
			} else {
				fseek($fd, -1, SEEK_CUR);
			}
	 
			while(($char = fread($fd, 1)) != chr(0))
				$ipAddr2 .= $char;
	 
			$AddrSeek = implode('', unpack('L', $AddrSeek.chr(0)));
			fseek($fd, $AddrSeek);
	 
			while(($char = fread($fd, 1)) != chr(0))
				$ipAddr1 .= $char;
		} else {
			
			fseek($fd, -1, SEEK_CUR);
			while(($char = fread($fd, 1)) != chr(0))
				$ipAddr1 .= $char;
	 
			$ipFlag = fread($fd, 1);
			if($ipFlag == chr(2)) {
				$AddrSeek2 = fread($fd, 3);
				if(strlen($AddrSeek2) < 3) {
					fclose($fd);
					return 'System Error';
				}
				$AddrSeek2 = implode('', unpack('L', $AddrSeek2.chr(0)));
				fseek($fd, $AddrSeek2);
			} else {
				fseek($fd, -1, SEEK_CUR);
			}
			while(($char = fread($fd, 1)) != chr(0)){
				$ipAddr2 .= $char;
			}
		}
		fclose($fd);
	 
		//�������Ӧ���滻�����󷵻ؽ��
		if(preg_match('/http/i', $ipAddr2)) {
			$ipAddr2 = '';
		}
		$ipaddr = "$ipAddr1 $ipAddr2";
		
		$cset = cls_env::getBaseIncConfigs('mcharset');
		$ipaddr = cls_string::iconv("gb2312",$cset,$ipaddr);
		return $ipaddr; //mb_convert_encoding($ipaddr,"utf-8","gb2312");
	}
	
	// ���˴���
	function fill($addr){
		//���� CZ88.NET 
		$addr = preg_replace('/CZ88.Net/is', '', $addr);
		$addr = preg_replace('/^s*/is', '', $addr);
		$addr = preg_replace('/s*$/is', '', $addr);
		if(preg_match('/http/i', $addr) || $addr == '') {
			$addr = 'Unknown';
		}
		return $addr;
	}
}

