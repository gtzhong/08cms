<?php
/**
 * sms_dxqun��
 * 
 */
class sms_dxqun{
	
	// ���к�
	var $userid;
	// ����
	var $userpw;
	// base path
	var $baseurl = ''; // http://http.dxsms.com, http://http.chinasms.com.cn, http://http.chinasms.com.cn
	// post����
	var $post;

	// ��3,4,5����������
	function sms_dxqun($userid,$userpw,$extra_par3='',$extra_par4='',$extra_par5='')
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
	 * sms_dxqun��
	 * ����70���ַ����Զ��ֶ������͡��������ݲ�֧�ֿո�(webservice�ӿ�֧��)��
	 * ����ÿ���ύ��100�����ڣ�������������ѭ��
	 * ÿ��GET�ύ�벻Ҫ����100�����롣Post��ʽÿ���ύ�벻Ҫ����5000�����룬����ֻ������� , Ӣ�Ķ��Ÿ�
	 * HTTP�ӿڷ��ͺͽ��յĶ������ݱ�����GB2312���롣HTTP����,���ݲ�֧�ֿո�
	 */
	function sendSMS($mobiles,$content)
	{
		$mcharset = cls_env::getBaseIncConfigs('mcharset'); //global $mcharset; 
		$this->post->timeout = $this->timeout; 
		
		$baseurl = 'http://sms.106jiekou.com/gbk/sms.aspx?'; 
		
		if(is_array($mobiles)) $mobiles = implode(',',$mobiles);
		$content = cls_string::iconv($mcharset,"gbk",$content);
		$content = str_replace(array(' ','��',"\r","\n"),'',$content); // �������ݲ�֧�ֿո�???
		$content = rawurlencode($content); // urlencode(
		$path = "account={$this->userid}&password=".$this->userpw."&mobile=$mobiles&content=$content"; //md5()
		$html = cls_sms::getHttpData("$baseurl$path");
		$re = $this->getReInfo($html); 
		if($re[0]!='1') { $re[0] = "-".$re[0]; } //������Ϣ��ͳһ����-999��ʽ
		return $re;
	}
	
	/**
	 * ����ѯ 
	 * @return double ���
	 */
	function getBalance()
	{
		$this->post->timeout = $this->timeout; 
		$baseurl = 'http://www.dxton.com/webservice/sms.asmx/GetNum?';
		$path = "account={$this->userid}&password=".$this->userpw.""; 
		$html = $this->post->fetchtext("$baseurl$path"); 
		// re : <string xmlns="http://www.dxton.com/">0.900</string>
		if(strstr($html,'</string>')){
			$val = strip_tags($html); 
			$val = preg_replace("/[^0-9.]/",'',$val); //var_dump($val);
			return array('1',$val);
		}else{
			return array('-1',0);
		}
	}
	
	/**
	 * ����ֵ-���� ��Ӧ��
	 */
	function getReInfo($info)
	{
		if(strlen($info)>3) $no = substr($info,0,3);
		else $no = $info; //var_dump($info); echo "($no)";
		if($no=='100') $no = '1';
		$a = array(
			'1'   => '���ͳɹ�',
			'101' => '��֤ʧ�ܣ�',
			'102' => '�ֻ������ʽ����ȷ��',
			'103' => '��Ա���𲻹���',
			'104' => '����δ��ˣ�',
			'105' => '���ݹ��࣡',
			'106' => '�˻����㣡',
			'107' => 'Ip���ޣ�',
			'108' => '�ֻ����뷢��̫Ƶ��',
			'120' => 'ϵͳ������',
		);	
		$msg = isset($a[$no]) ? $a[$no] : '(δ֪����)';
		return array($no,"$msg($info)");
	}

}

// ���ر������е�class
include_once M_ROOT."include/http.cls.php";

/* ֮ǰ״̬
			'1'   => '���ͳɹ�',
			'101' => '��֤ʧ�ܣ�',
			'102' => '���Ų��㣡',
			'103' => '����ʧ�ܣ�',
			'104' => '�Ƿ��ַ���',
			'105' => '���ݹ��࣡',
			'106' => '������࣡',
			'107' => 'Ƶ�ʹ��죡',
			'108' => '�������ݿ�',
			'109' => '�˺Ŷ��ᣡ', 
			'110' => '��ֹƵ���������ͣ�',
			'111' => 'ϵͳ�ݶ����ͣ�',
			'112' => '�������',
			'113' => '��ʱʱ���ʽ���ԣ�',
			'114' => '�˺ű�����10���Ӻ��¼��',
			'115' => '����ʧ�ܣ�',
			'116' => '��ֹ�ӿڷ��ͣ�',
			'117' => '��IP����ȷ��',
			'120' => 'ϵͳ������',
*/

?>