<?php

/**
 * ������ʹ�ã����ڲ���ϵͳ�������̣�
 * ����������ᷢ���ţ���дһ���ļ���¼��ʾ������
 */
class sms_0test{
	
	// ���к�
	var $userid;
	// ����
	var $userpw;
	// ����ļ�
	var $bfile;
	// �������ӿڱ���һ��,����һ������
	var $baseurl;

	// ��3,4,5����������
	function sms_0test($userid,$userpw,$extra_par3='',$extra_par4='',$extra_par5='')
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
		$path = M_ROOT."dynamic/sms"; 
		$file = "$path/0test_balance.txt";
		if(!is_file($file)){
			mmkdir($path,0);
			$fp = fopen($file, 'wb');
			$fee = rand(50,100);
			flock($fp, 2); fwrite($fp, $fee); fclose($fp);
		}
		$this->bfile = $file;
	}
	
	/**
	 * ����ѯ  (ע:�˷�������Ϊ�ѵ�¼״̬�·��ɲ���)
	 * @return double ���
	 */
	function getBalance()
	{
		$rnd = rand(1,1000);
		if($rnd<998){ // ģ��,99.8%����³ɹ�
			$cnt = file_get_contents($this->bfile);
			return array(1,$cnt);
		}else{
			return array(-1,'ʧ��!');
		}	
	}
	
	// ��ֵ
	function chargeUp($count)
	{
		$rnd = rand(1,1000);
		if($rnd<900){ // ģ��,90%����³ɹ�
			$cnt = file_get_contents($this->bfile);
			$cnt += $count;
			$fp = fopen($this->bfile, 'wb');
			flock($fp, 2); fwrite($fp, $cnt); fclose($fp);
			return array(1,$cnt);
		}else{
			return array(-1,'ʧ��!');
		}	
	}
	
	// �۷�
	function deductingCharge($count)
	{
			$cnt = file_get_contents($this->bfile);
			$cnt -= $count; 
			if((float)$cnt<0) $cnt = 0; 
			$fp = fopen($this->bfile, 'wb');
			flock($fp, 2); fwrite($fp, $cnt); fclose($fp);
			return array(1,$cnt);
	}

	/**
	 * ����������ᷢ���ţ���дһ���ļ���¼��ʾ������
	 */
	function sendSMS($mobiles,$content)
	{
		$rnd = rand(1,1000);
		if($rnd<900){ // ģ��,90%����³ɹ�
			// ��ʹ��db��¼,���ﲻҪ.txt�ı���¼��
			// ��Ǯ test_balance.txt
			return array(1,"OK!");
		}else{
			return array(-1,'ʧ��!');
		}
	}

}

// ���ر������е�class
//include_once M_ROOT.'include/general.inc.php';

// ����˵��
// none

?>
