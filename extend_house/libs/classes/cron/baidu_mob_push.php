<?php
!defined('M_COM') && exit('No Permission');
class cron_baidu_mob_push extends cron_exec{
	public function __construct(){
		parent::__construct();
		$this->main();
	}
	public function main(){ 
		//��ȡxml
		if(!function_exists('simplexml_load_string') || !function_exists('curl_init')){
			return $this->logger('simplexml_load_string��curl_init����������,������php.ini'); 	
		}
		$xml = simplexml_load_string(@file_get_contents(M_ROOT.'baidu_mob_push.xml'));
		//$json = json_encode($xml); //_08_Documents_JSON::encode($xml,1)
		//$array = json_decode($json,TRUE); //echo count($array); print_r($array);
		if(empty($xml->url)){ // $array['url']
			return $this->logger('û����������'); 
		} 
		
		$cms_abs = cls_env::mconfig('cms_abs'); 
		$urls = array( //���ж��sitemap��xml��Ҫ�������ͣ�����������������
			$cms_abs.'baidu_mob_push.xml',
		);
		$api = cls_env::mconfig('baidu_push_api');
		if(empty($api)){
			return $this->logger('api��ַδ��д'); 
		}
		
		$ch = curl_init();
		$options =  array(
				CURLOPT_URL => $api,
				CURLOPT_POST => true,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_POSTFIELDS => implode("\n", $urls),
				CURLOPT_HTTPHEADER => array('Content-Type: text/plain'),
		);
		curl_setopt_array($ch, $options);
		$result = curl_exec($ch);
		
		$message = json_decode($result); 
		if(!empty($message->message)){ 
			return $this->logger($message->message); 
		}elseif(isset($message->success)){	
			$push_time = TIMESTAMP;
			$this->db->query('update '.$this->tblprefix.'mconfigs set value ='.$push_time.' where varname= "push_time"');
			return $this->logger('�ɹ�����');
		}
		
	}
	// ��¼����ʾ
	public function logger($msg=''){ 
		if(in_array(cls_env::GetG('action'),array('runTest','sitemapsedit'))){
			echo '��ʾ��Ϣ:'; print_r($msg); 
		}else{
			@adminlog('baidu��������',$msg);
		}
	}
}
