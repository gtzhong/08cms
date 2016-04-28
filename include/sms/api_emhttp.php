<?php
/**
 * sms_emhttp��
 * http://sdk4report.eucp.b2m.cn:8080/
 * ����ӿ��ǲ���Ҫ�˺ź�IP��ַ�󶨵ģ��˺�����6SDK��ͷ��
 * http://sdkhttp.eucp.b2m.cn/
 * ����ӿ�����Ҫ�˺ź�IP��ַ�󶨵ģ��˺���3SDK��ͷ��
 */
class sms_emhttp{
	
	// ���к�
	var $userid;
	// ����/Key
	var $userpw;
	// SDK-Map
	var $urlmap = array(
		'3SDK' => 'http://sdkhttp.eucp.b2m.cn/sdkproxy/',
		'6SDK' => 'http://sdk4report.eucp.b2m.cn:8080/sdkproxy/',
		'_NUL' => 'http://sdk4report.eucp.b2m.cn:8080/sdkproxy/', //���ٷ��ĵ�
	);
	// base path
	var $baseurl = ''; 
	// post����
	var $post;

	// ��3,4,5����������
	function sms_emhttp($userid,$userpw,$extra_par3='',$extra_par4='',$extra_par5='')
	{
		$this->userid = $userid;
		$this->userpw = $userpw;
		$fix = substr($userid,0,4);
		if(isset($this->urlmap[$fix])){
			$this->baseurl = $this->urlmap[$fix];	
		}else{
			$this->baseurl = $this->urlmap['_NUL'];	
		}
		$this->getInit();
	}
	
	/**
	 * ��� ��ʼ��
	 */
	function getInit()
	{
		$this->post = new http();
		//$this->post->timeout = $this->timeout;
		$this->post->setCookies(60);
	}
	
	/**
	 * ���Ͷ��ţ�(utf-8����)
	 */
	function sendSMS($mobiles,$content)
	{
		$mcharset = cls_env::getBaseIncConfigs('mcharset');
		$this->post->timeout = $this->timeout; 
		if(is_array($mobiles)) $mobiles = implode(',',$mobiles);
		$content = cls_string::iconv($mcharset,"utf-8",$content);
		$content = str_replace(array(' ','��',"\r","\n"),'',$content); // �������ݲ�֧�ֿո�???
		$content = urlencode($content); 
		$path = "sendsms.action?cdkey={$this->userid}&password={$this->userpw}&phone=$mobiles&message=$content&addserial=";
		$html = cls_sms::getHttpData("$this->baseurl$path");
		$erno = $this->getEVal($html,'error');
		$emsg = $this->getEVal($html,'message');
		return $this->getReInfo(!$erno, $emsg);
	}
	
	/**
	 * ����ѯ 
	 */
	function getBalance()
	{
		$this->post->timeout = $this->timeout; 
		$path = "querybalance.action?cdkey={$this->userid}&password={$this->userpw}";
		$html = $this->post->fetchtext("$this->baseurl$path",'POST');
		$erno = $this->getEVal($html,'error');
		$emsg = $this->getEVal($html,'message');
		if(substr($emsg,0,1)=='-'){
			$emsg = $this->getReInfo(str_replace('.0','',$emsg));
			return array('0',"[$emsg[1]]"); 	
		}else{
			if(substr($emsg,0,1)=='.') $emsg = "0$html";
			return array('1',$emsg); 	
		}
	}
	
	/**
	 * ����ֵ-���� ��Ӧ��
	 */
	function getReInfo($no)
	{
		if($no=='0') $no = '1';
		$a = array(
		
			'1'=>'�����ɹ�',
			'-1'=>'ϵͳ�쳣',
			'-101'=>'�����֧��',
			'-102'=>'�û���Ϣɾ��ʧ��',
			'-103'=>'�û���Ϣ����ʧ��',
			'-104'=>'ָ�����������',
			'-111'=>'��ҵע��ʧ��',
			'-117'=>'���Ͷ���ʧ��',
			'-118'=>'��ȡMOʧ��',
			'-119'=>'��ȡReportʧ��',
			'-120'=>'��������ʧ��',
			'-122'=>'�û�ע��ʧ��',
			'-110'=>'�û�����ʧ��',
			'-123'=>'��ѯ����ʧ��',
			'-124'=>'��ѯ���ʧ��',
			'-125'=>'����MOת��ʧ��',
			'-127'=>'�Ʒ�ʧ�������',
			'-128'=>'�Ʒ�ʧ������',
			'-1100'=>'���кŴ���,���кŲ������ڴ���,���Թ������û�',
			'-1102'=>'���к���ȷ,Password����',
			'-1103'=>'���к���ȷ,Key����',
			'-1104'=>'���к�·�ɴ���',
			'-1105'=>'���к�״̬�쳣 δ��1',
			'-1106'=>'���к�״̬�쳣 ����2 ����ԭ��ϵͳΪ0',
			'-1107'=>'���к�״̬�쳣 ͣ��3',
			'-1108'=>'���к�״̬�쳣 ֹͣ5',
			'-113'=>'��ֵʧ��',
			'-1131'=>'��ֵ����Ч',
			'-1132'=>'��ֵ��������Ч',
			'-1133'=>'��ֵ�����쳣',
			'-1134'=>'��ֵ��״̬�쳣',
			'-1135'=>'��ֵ�������Ч',
			'-190'=>'���ݿ��쳣',
			'-1901'=>'���ݿ�����쳣',
			'-1902'=>'���ݿ�����쳣',
			'-1903'=>'���ݿ�ɾ���쳣',
		);	
		return array($no,isset($a[$no]) ? $a[$no] : '(δ֪����)');
		//return isset($a[$no]) ? $a[$no] : '(δ֪����)';
	}
	
	// �������
	// <response><error>0</error><message>3.0</message></response>
	function getEVal($data='',$tag=''){
		preg_match("/<$tag>(.*)<\/$tag>/i",$data,$vals); 
		if(isset($vals[1])){
			return $vals[1];	
		}else{
			return '';	
		}
	}

}

// ���ر������е�class
include_once M_ROOT."include/http.cls.php";


?>