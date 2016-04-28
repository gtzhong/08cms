<?php
/**
 * sms_winic��
 */
class sms_winic{
	
	// ���к�
	var $userid;
	// ����
	var $userpw;
	// base path
	var $baseurl = 'http://service.winic.org';
	// post����
	var $post;

	// ��3,4,5����������
	function sms_winic($userid,$userpw,$extra_par3='',$extra_par4='',$extra_par5='')
	{
		$this->userid = $userid;
		$this->userpw = $userpw;
		$this->getInit();
	}
	
	/**
	 * ��� ��ʼ��
	 */
	function getInit()
	{
		$this->post = new http(); 
		$this->post->setCookies(60);
	}
	
	/**
	 * sms_winic��
	 * ����70���ַ����Զ��ֶ������͡��������ݲ�֧�ֿո�(webservice�ӿ�֧��)��
	 * ����ÿ���ύ��100�����ڣ�������������ѭ��
	 * ÿ��GET�ύ�벻Ҫ����100�����롣Post��ʽÿ���ύ�벻Ҫ����5000�����룬����ֻ������� , Ӣ�Ķ��Ÿ�
	 * HTTP�ӿڷ��ͺͽ��յĶ������ݱ�����GB2312���롣HTTP����,���ݲ�֧�ֿո�
	 */
	function sendSMS($mobiles,$content)
	{
		$mcharset = cls_env::getBaseIncConfigs('mcharset');
		$this->post->timeout = $this->timeout; 
		if(is_array($mobiles)) $mobiles = implode(',',$mobiles);
		$content = cls_string::iconv($mcharset,"gbk",$content);
		$content = str_replace(array(' ','��',"\r","\n"),'',$content); // �������ݲ�֧�ֿո�???
		$content = urlencode($content); 
		$path = "/sys_port/gateway/?id={$this->userid}&pwd={$this->userpw}&to=$mobiles&content=$content";
		$html = $this->post->fetchtext("$this->baseurl$path",'POST');
		// -02/Send:2/Consumption:0/Tmoney:0/sid:
		return $this->getReInfo($html);
	}
	
	/**
	 * ����ѯ 
	 * @return double ���
	 */
	function getBalance()
	{
		$this->post->timeout = $this->timeout; 
		$path = ":8009/webservice/public/remoney.asp?uid={$this->userid}&pwd={$this->userpw}";
		$html = $this->post->fetchtext("$this->baseurl$path",'POST');
		if(substr($html,0,1)=='-'){
			return array('-1',0);
		}else{
			if(substr($html,0,1)=='.') $html = "0$html";
			return array(1,$html);
		}
	}
	
	/**
	 * ����ֵ-���� ��Ӧ��
	 */
	function getReInfo($info)
	{
		if(strlen($info)>3) $no = substr($info,0,3);
		else $no = $info; //var_export($info); echo "($no)";
		if($no=='000') $no = '1';
		$a = array(
			'nul' => '�޽�������',
			'1'   => '�����ɹ�',
			'-01' => '��ǰ�˺����㣡',
			'-02' => '��ǰ�û�ID����',
			'-03' => '��ǰ�������',
			'-04' => '����������������ݵ����ʹ���',
			'-05' => '�ֻ������ʽ���ԣ�',
			'-06' => '�������ݱ��벻�ԣ�',
			'-07' => '�������ݺ��������ַ���',
			'-09' => 'ϵͳά����.. ',
			'-10' => '�ֻ���������������', //�������ݳ�������70���ַ���Ŀǰ��ȡ��
			'-11' => '�������ݳ�����',
			'-12' => '��������',
		);	
		$msg = isset($a[$no]) ? $a[$no] : '(δ֪����)';
		return array($no,"$msg($info)");
	}

}

// ���ر������е�class
include_once M_ROOT."include/http.cls.php";


?>