<?php
!defined('M_COM') && exit('No Permission');
class cron_sitemap extends cron_exec{    
	public function __construct(){
		parent::__construct();
		$this->main();  
    }
	public function main(){
		# ��Ҫ�Ż�����ƻ����񣬽��������ƻ��������ڣ�ÿ��ִֻ��һ����?????????????
		$sitemaps = cls_cache::Read('sitemaps');
		foreach($sitemaps as $k => $v){
		cls_SitemapPage::Create(array('map' => $k,'inStatic' => true));
		}
	}
}

