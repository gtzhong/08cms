<?php
!defined('M_COM') && exit('No Permission');
class cron_baidu_mob_push extends cron_exec{
	public function __construct(){
		parent::__construct();
		$this->main();
	}
	public function main(){ 
		//读取xml
		if(!function_exists('simplexml_load_string') || !function_exists('curl_init')){
			return $this->logger('simplexml_load_string或curl_init函数不可用,请设置php.ini'); 	
		}
		$xml = simplexml_load_string(@file_get_contents(M_ROOT.'baidu_mob_push.xml'));
		//$json = json_encode($xml); //_08_Documents_JSON::encode($xml,1)
		//$array = json_decode($json,TRUE); //echo count($array); print_r($array);
		if(empty($xml->url)){ // $array['url']
			return $this->logger('没有最新内容'); 
		} 
		
		$cms_abs = cls_env::mconfig('cms_abs'); 
		$urls = array( //若有多个sitemap的xml需要主动推送，继续往这个数组里加
			$cms_abs.'baidu_mob_push.xml',
		);
		$api = cls_env::mconfig('baidu_push_api');
		if(empty($api)){
			return $this->logger('api地址未填写'); 
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
			return $this->logger('成功推送');
		}
		
	}
	// 记录或显示
	public function logger($msg=''){ 
		if(in_array(cls_env::GetG('action'),array('runTest','sitemapsedit'))){
			echo '提示信息:'; print_r($msg); 
		}else{
			@adminlog('baidu主动推送',$msg);
		}
	}
}
