<?php
//��ʱ�����Լ��ݾɰ汾

!defined('M_COM') && exit('No Permission');

//��������ϵͳ�ܹ����棬��ʱ�����Լ��ݾɰ汾
function rebuild_cache($except = ''){
	return cls_CacheFile::ReBuild($except);
}

//���ɻ����ָ����ϵͳ�ܹ����棬��ʱ�����Լ��ݾɰ汾
function updatecache($CacheName,$BigClass = ''){
	return cls_CacheFile::Update($CacheName,$BigClass);
}

//ͨ�����ݱ�õ����ɻ�������Ҫ��ԭʼ�������飬��ʱ�����Լ��ݾɰ汾
function cache_array($cachecfg = array()){
	return cls_DbOther::CacheArray($cachecfg);
}

//�õ�ָ������ֶ������飬��ʱ�����Լ��ݾɰ汾
function mfetch_fields($tbls = ''){
	return cls_DbOther::ColumnNames($tbls);
}

//��д��Ŀ���tureorder�����ֶΣ���ʱ�����Լ��ݾɰ汾
function cn_dborder($coid=0){
	return cls_catalog::DbTrueOrder($coid);
}

//ָ��id�������ϼ�id��ͨ�������ԭʼ�����ȡ������ʱ�����Լ��ݾɰ汾
function cn_pids($ccid,$cnArray = array()){
	return cls_catalog::PccidsByAarry($ccid,$cnArray);
}

//ȡ�ö�����������ʱ�����Լ��ݾɰ汾
function top_domain($url){
	return cls_env::TopDomain($url);
}

//ȡ����Ŀ������ϵ��Ŀ��ռ�õ�dirname(��̬·��)���飬��ʱ�����Լ��ݾɰ汾
function cn_dirname_arr(){
	return cls_catalog::DirnameArray();
}
