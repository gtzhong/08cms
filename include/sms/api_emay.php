<?php

/**
 * ��֤���̸�Ҫ:
 * ��һ��ʹ��ʱ����ʹ��[���к�]��[����]����login(��¼����),���ڵ�¼ͬʱ����һ��session key
 * ��¼�ɹ��󣬳�Ϊ[�ѵ�¼״̬],��Ҫ����˲�����session key,�����Ժ����ز���(�緢�Ͷ��ŵȲ���)
 * logout(ע������)��, session key��ʧЧ�����Ҳ����ٷ�������, �����ٽ���login(��¼����)
 */
class sms_emay{
	
	/**
	 * ���ص�ַ 
	 */     
	var $url = 'http://sdk999ws.eucp.b2m.cn:8080/sdk/SDKService?wsdl'; 
	
	/**
	 * ���к�,��ͨ������������Ա��ȡ
	 */
	var $serialNumber;
	
	/**
	 * ����,��ͨ������������Ա��ȡ
	 */
	var $password;
	
	/**
	 * ��¼�������е�SESSION KEY������ͨ��login����ʱ����
	 */
	var $sessionKey = '111110'; //������6λ��- 345678/111110
	
	/**
	 * webservice�ͻ���
	 */
	var $soap;
	
	/**
	 * Ĭ�������ռ�
	 */
	var $namespace = 'http://sdkhttp.eucp.b2m.cn/';
	
	/**
	 * ���ⷢ�͵����ݵı���,Ĭ��Ϊ GBK
	 */
	var $outgoingEncoding = "gbk";
	
	/**
	 * @param string $url 			���ص�ַ
	 * @param string $serialNumber 	���к�,��ͨ������������Ա��ȡ
	 * @param string $password		����,��ͨ������������Ա��ȡ
	 * @param string $sessionKey	��¼�������е�SESSION KEY������ͨ��login����ʱ����
	 * @param string $extra_par3-5  ��3,4,5����������
	 */
	function sms_emay($serialNumber,$password,$extra_par3='',$extra_par4='',$extra_par5='')
	{
		$mcharset = cls_env::getBaseIncConfigs('mcharset');
		$this->timeout = empty($sms_cfg_timeout) ? 3 : $timeout;
		$this->serialNumber = $serialNumber;
		$this->password = $password;
		//$this->sessionKey = 'EMAYID'; //��������Key
		/**
		 * ��ʼ�� webservice �ͻ���
		 * @param string $proxyhost		��ѡ�������������ַ��Ĭ��Ϊ false ,��ʹ�ô��������
		 * @param string $proxyport		��ѡ������������˿ڣ�Ĭ��Ϊ false
		 * @param string $proxyusername	��ѡ������������û�����Ĭ��Ϊ false
		 * @param string $proxypassword	��ѡ��������������룬Ĭ��Ϊ false
		 * @param string $timeout		���ӳ�ʱʱ�䣬Ĭ��0��Ϊ����ʱ
		 * @param string $response_timeout		��Ϣ���س�ʱʱ�䣬Ĭ��30
		 */	
		$proxyhost = false; $proxyusername = false; 
		$proxyport = false; $proxypassword = false;
		$this->soap = new nusoap_client($this->url,false,$proxyhost,$proxyport,$proxyusername,$proxypassword,$this->timeout,10); 
		$this->soap->soap_defencoding = $mcharset;
		$this->soap->decode_utf8 = $mcharset=='gbk' ? false : true;				
	}

	function setNameSpace($ns)
	{
		$this->namespace = $ns;
	}
	
	/**
	 * ָ��һ�� session key �� ���е�¼����
	 * @param string $sessionKey ָ��һ��session key 
	 * @return int �������״̬��
	 * ������:
	 * $sessionKey = $smsdo->generateKey(); //�������6λ�� session key
	 * if ($smsdo->login($sessionKey)==0)
	 * {
	 * 	 //��¼�ɹ������������� $sessionKey �Ĳ����������Ժ���ز�����ʹ��
	 * }else{
	 * 	 //��¼ʧ�ܴ���
	 * } 
	 */
	function login()
	{
		$params = array('arg0'=>$this->serialNumber,'arg1'=>$this->sessionKey, 'arg2'=>$this->password);
		$result = $this->soap->call("registEx",$params,	$this->namespace);
		return $this->getReInfo($result);
	}
	
	/**
	 * ע������  (ע:�˷�������Ϊ�ѵ�¼״̬�·��ɲ���)
	 * 
	 * @return int �������״̬��
	 * 
	 * ֮ǰ�����sessionKey��������
	 * ����Ҫ��������login
	 */
	function logout()
	{
		$params = array('arg0'=>$this->serialNumber,'arg1'=>$this->sessionKey);
		$result = $this->soap->call("logout", $params ,
			$this->namespace
		);
		return $this->getReInfo($result);
	}

	/**
	 * ���ŷ���  (ע:�˷�������Ϊ�ѵ�¼״̬�·��ɲ���)
	 * @param array $mobiles		�ֻ���, ���Ϊ200���ֻ����룬�� array('159xxxxxxxx'),�����Ҫ����ֻ���Ⱥ��,�� array('159xxxxxxxx','159xxxxxxx2') 
	 * @param string $content		�������ݣ����500�����ֻ�1000����Ӣ��
	 * @param string $sendTime		��ʱ����ʱ�䣬��ʽΪ yyyymmddHHiiss, ��Ϊ ����������������ʱʱ�ַ�����,����:20090504111010 ����2009��5��4�� 11ʱ10��10��
	 * 								�������Ҫ��ʱ���ͣ���Ϊ'' (Ĭ��)
	 * @param string $addSerial 	��չ��, Ĭ��Ϊ ''
	 * @param string $charset 		�����ַ���, Ĭ��GBK
	 * @param int $priority 		���ȼ�, Ĭ��5
	 * @return int �������״̬��
	 */
	function sendSMS($mobiles,$content,$sendTime='',$addSerial='',$priority=5)
	{
		$mcharset = cls_env::getBaseIncConfigs('mcharset'); //global $mcharset; 
		$this->post->timeout = $this->timeout; 
		if(is_string($mobiles)) $mobiles = explode(',',$mobiles);
		$content = cls_string::iconv($mcharset,"gbk",$content);
		$params = array('arg0'=>$this->serialNumber,'arg1'=>$this->sessionKey,'arg2'=>$sendTime,
			'arg4'=>$content,'arg5'=>$addSerial, 'arg6'=>$mcharset,'arg7'=>$priority
			); //print_r($mobiles); print_r($content);
		/**
		 * ������뷢�͵�xml���ݸ�ʽ�� 
		 * <arg3>159xxxxxxxx</arg3>
		 * <arg3>159xxxxxxx2</arg3>
		 * ....
		 * ������Ҫ����ĵ�������
		 */
		foreach($mobiles as $mobile)
		{
			array_push($params,new soapval("arg3",false,$mobile));	
		}
		$result = $this->soap->call("sendSMS",$params,$this->namespace);
		return $this->getReInfo($result);
	}
	
	/**
	 * ����ѯ(ע:�˷�������Ϊ�ѵ�¼״̬�·��ɲ���)
	 * @return double ���
	 */
	function getBalance()
	{
		$this->post->timeout = $this->timeout; 
		$params = array('arg0'=>$this->serialNumber,'arg1'=>$this->sessionKey);
		$res1 = $this->soap->call("getBalance",$params,$this->namespace); 
		if(strstr($res1,"-")){
			$res1 = str_replace('.0','',$res1);
			$_re = $this->getReInfo($res1); 
			return array('0',"[$_re[1]]"); 	
		}else{
			if(substr($res1,0,1)=='.') $res1 = "0$res1"; // .5
			return array('1',$res1); 	
		}
		//if(is_numeric($res1)) return array('1',$res1); 
		//else return array('-1',0); 
		// getEachFee:��ѯ��������  (ע:�˷�������Ϊ�ѵ�¼״̬�·��ɲ���)
		// @return double ��������
		// $params = array('arg0'=>$this->serialNumber,'arg1'=>$this->sessionKey);
		// $res2 = $this->soap->call("getEachFee",$params,$this->namespace);
		//return array(1,$res2);
		
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
			/*
			'1' => '�����ɹ�',
			'10' => '�ͻ���ע��ʧ��',
			'11' => '��ҵ��Ϣע��ʧ��',
			'13' => '��ֵʧ��',
			'17' => '������Ϣʧ��',
			'18' => '���Ͷ�ʱ��Ϣʧ��',
			'22' => 'ע��ʧ��',
			'303' => '�ͻ����������',
			'305' => '�������˷��ش��󣬴���ķ���ֵ������ֵ���������ַ�����',
			'307' => 'Ŀ��绰���벻���Ϲ��򣬵绰�����������0��1��ͷ',
			'308' => '�����벻�����֣�����������',
			'997' => 'ƽ̨�����Ҳ�����ʱ�Ķ��ţ�����Ϣ�Ƿ�ɹ��޷�ȷ��',
			'998' => '���ڿͻ����������⵼����Ϣ���ͳ�ʱ������Ϣ�Ƿ�ɹ��·��޷�ȷ��',
			'999' => '����Ƶ��',
			*/
		);	
		return array($no,isset($a[$no]) ? $a[$no] : '(δ֪����)');
		//return isset($a[$no]) ? $a[$no] : '(δ֪����)';
	}

}

// ���ر������е�class
require_once M_ROOT.'/include/nusoaplib/nusoap.php';

// ����˵��
/** getMO() : 
 * ���ڷ���˷��صı�����UTF-8,������Ҫ���б���ת��
 echo "��������:".iconv("UTF-8","GBK",$mo->getSmsContent());
 �ֻ�����(�ַ�������,���Ϊ200���ֻ�����)
 ��������(���500�����ֻ�1000����Ӣ�ģ�emay�����������ܹ��Զ��ָ�����ж��ͨ��Ϊ�ͻ��ṩ�������Էָ�ԭ��������������ͨ��Ϊ�ָ���ų��ȵĹ�����ͻ�Ӧ�ó���Ҫ�Լ��ָ����������ɻ���)
 */

?>
