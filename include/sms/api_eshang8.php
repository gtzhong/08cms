
<?php
/**
 * sms_eshang8��
 */
class sms_eshang8{
	
	// �û���
	var $userid;
	// ��Կ
	var $userpw;
	// base path
	var $baseurl = 'http://sms.eshang8.com/jdk/';
	// post����
	var $xml;

	// ��3,4,5����������
	function sms_eshang8($userid,$userpw,$extra_par3='',$extra_par4='',$extra_par5='')
	{
		$this->userid = $userid;
		$this->userpw = $userpw;
		$this->xml = new DOMDocument();
	}
	
	/**
	 * sms_eshang8��
	 * ��Ϣ����(GBK����)���������ĵĶ��ų���С�ڵ���70���ַ�����Ӣ�ĵĶ��ų���С�ڵ���150���ַ�
	 */
	function sendSMS($mobiles,$content)
	{
		$mcharset = cls_env::getBaseIncConfigs('mcharset');
		if(is_array($mobiles)) $mobiles = implode(',',$mobiles);
		if($mcharset=='utf-8') $content = iconv($mcharset,'gbk',$content); 
		//$content = str_replace(array(' ','��',"\r","\n"),'',$content); // �������ݲ�֧�ֿո�???
		// >70�ַָ� ?????????????? 
		$content = urlencode($content); 
		$path = "?esname={$this->userid}&key={$this->userpw}&phone=$mobiles&msg=$content&smskind=1";
		$this->xml->load("$this->baseurl$path");
		return $this->getReInfo();
	}
	
	/**
	 * ����ѯ 
	 * @return double ���
	 */
	function getBalance()
	{
		$path = "?esname={$this->userid}&key={$this->userpw}&smskind=1";
		$this->xml->load("$this->baseurl$path");
		return $this->getReInfo('PayCount');
	}
	
	/**
	 * ����ֵ-���� ��Ӧ��
	 */
	function getReInfo($flag='')
	{
		global $mcharset; 
		$root1 = $this->xml->getElementsByTagName("root")->item(0); 
		$cnt = $root1->getElementsByTagName( "PayCount" )->item(0)->nodeValue;
		$res = $root1->getElementsByTagName( "result" )->item(0)->nodeValue;
		$err = $root1->getElementsByTagName( "ErrorDesc" )->item(0)->nodeValue;
		if($mcharset!='utf-8') $err = iconv('utf-8','gbk',$err); 
		$no = $res; 
		if($no=='1') $no = '-1';
		if($no=='0') $no = '1'; //�ɹ�ͳһ����1
		$a = array( //0�ɹ� 1����ʧ��2��������3������4��֤ʧ��
			'-1' => '����ʧ��',
			'1'   => '�����ɹ�',
			'2' => '��������',
			'3' => '�����֣�',
			'4' => '��֤ʧ�ܣ�',
		);
		$msg = isset($a[$no]) ? $a[$no] : '(δ֪����)';
		if($flag=='PayCount'){
			if(substr($cnt,0,1)=='.') $html = "0$cnt";
			if($res=='1') return array('1',$cnt); 
			else return array('-1',0); 
		}
		return array($no,$msg);
	}

}

?>