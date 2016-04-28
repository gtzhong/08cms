<?php
/**
 * 
*** ��������, ������Ź�Ӧ�����磺
-1- (��ƽ��)�����Ǹ��ӿڣ���ʱ��������(ǰ������������վ����ʱ���Ǻܿ�ġ�)
--- ������Ź�˾�Ŀͻ����� �����ڲ�������,������һ�鷢���ŵ����ֶࡣ��ʱ�϶��Ļ��еġ�
#2# �����ͺ���һЩ�ؼ���ʱ��
### ��ʱ��ʾ���ͳɹ�����ʵû�з��ͳɹ���
### ��cr6868.com��վ��̨�ɷ����С����ء���ʾ��
### ������ϵͳ�Ѿ��ǰ���ʱ״̬(�ɹ�)������������Ǹ����⡣
 * 
 * sms_cr6868��
 */
class sms_cr6868{
	
	// ���к�
	var $userid;
	// ����
	var $userpw;
	// base path
	var $baseurl = 'http://web.cr6868.com/asmx/smsservice.aspx';
	// post����
	var $post;

	// ��3,4,5����������
	function __construct($userid,$userpw,$extra_par3='',$extra_par4='',$extra_par5=''){ 
	//{
		$this->userid = $userid;
		$this->userpw = $userpw;
		$this->baseurl = $this->baseurl."?name=$this->userid&pwd=$this->userpw";
		$this->getInit();
	}
	
	/**
	 * http ��ʼ��
	 */
	function getInit()
	{
		$this->post = new http(); 
		$this->post->setCookies(60);
	}
	
	/**
	 * sms_cr6868��
	 * �������ݣ�1-500 �����֣�UTF-8����
	 * http://web.cr6868.com/asmx/smsservice.aspx?name=13537432147&pwd=xxx&content=test����msg[��ƽ��]&mobile=13537432147&type=pt
	 */
	function sendSMS($mobiles,$content)
	{
		$this->post->timeout = $this->timeout; 
		if(is_array($mobiles)) $mobiles = implode(',',$mobiles);
		
		$content = str_replace(array(' ','��',"\r","\n","&","#"),'',$content); // �������ݲ�֧�ֿո�???
        $content = str_replace(array('[',']'),array('��','��'),$content); // ������ѯ���Ź�Ӧ��
        
        $mcharset = cls_env::getBaseIncConfigs('mcharset');
        $content = cls_string::iconv($mcharset,"utf8",$content);
		//$content = urlencode($content); //�������������
        
		$path = "&type=pt&content=$content&mobile=$mobiles";
		$html = $this->post->fetchtext("$this->baseurl$path",'POST'); //print_r("r:($html)"); die();
        //$html = '0, 20130821110353234137876543,0,500,0,�ύ�ɹ�';
		$re = $this->fmtInfo($html); //echo 'xxx'; print_r($html); die('yy'); //."$this->baseurl$path"
		return $this->getReInfo($re[0]);
	}
	
	/**
	 * ����ѯ 
	 * @return double ���
	 */
	function getBalance()
	{
		$this->post->timeout = $this->timeout; 
		$path = "&type=balance";
		$html = $this->post->fetchtext("$this->baseurl$path",'GET');
		$re = $this->fmtInfo($html); 
		if($re[0]==='0' && is_numeric($re[1])){
            return array(1,$re[1]);
		}else{
            $msg = $this->getReInfo($re[0]);
			return array('-1',0,'msg'=>$msg[1]);
		}
	}
	
	function fmtInfo(&$info)
	{
		$mcharset = cls_env::getBaseIncConfigs('mcharset');
		$info = cls_string::iconv("utf8",$mcharset,$info);
		$a = explode(',',$info);
		if(count($a)>=2){
			return array($a[0],$a[1]);
		}
		return array('-2','��������');
	}

	/**
	 * ����ֵ-���� ��Ӧ��
	 */
	function getReInfo($no)
	{
		$nobak = $no; //
        $conv = array(
			'0'  => '1', 
			'1'  => '9',
		);
		if(isset($conv[$no])) $no = $conv[$no];
		// �ӿ�0Ϊ�ɹ�,1Ϊ�������дʻ�; ��ϵͳapi�淶Ϊ1Ϊ�ɹ�
		$a = array(
			'1'  => '�����ɹ�', //0
			'9'  => '�������дʻ㣡', //1
			'2'  => '����',
			'3'  => 'û�к���',
			'4'  => '����sql���',
			'10' => '�˺Ų�����',
			'11' => '�˺�ע��',
			'12' => '�˺�ͣ��',
			'13' => 'IP��Ȩʧ��',
			'14' => '��ʽ����',
			'-1' => 'ϵͳ�쳣',
			'-2' => '��������',
		);	//echo $no;
		$msg = isset($a[$no]) ? $a[$no] : '(δ֪����)';
		return array($no,"{$msg}".($no==1 ? '' : "[error:$nobak]"));
	}

}

// ���ر������е�class
include_once M_ROOT."include/http.cls.php";


?>