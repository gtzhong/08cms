<?php
/* 
** ��Ի��ִ���ķ�������
** ע�⣺Ϊ���ڻ�����ʹ����չ�ľ�̬�������ڻ�����ʹ�ã���չ��::method�����ʹ�ã�self::method������֧����չ��
*/
!defined('M_COM') && exit('No Permission');
class cls_CurrencyBase{
	
	//��������ƻ�
	static function clearCurrency(){
		global $db,$tblprefix;
		$interval = cls_env::mconfig('point_interval');
		if(empty($interval))  return;
		$fliename =_08_CACHE_PATH.'stats/currency.cf';
		if(!file_exists($fliename) || filemtime($fliename)<= mktime(0,0,0)){
			$cridsarr = cridsarr(1);
			if(isset($cridsarr[0])) unset($cridsarr[0]);
			$t = strtotime("-$interval months");
			foreach($cridsarr as $k=>$v){
				$db->query("DELETE FROM {$tblprefix}currency$k WHERE createdate < $t");
			}
			mmkdir($fliename,1,1);
			touch($fliename);
			chmod($fliename,'0666');
		}
	}
}