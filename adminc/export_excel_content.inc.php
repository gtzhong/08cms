<?php
include_once dirname(dirname(__FILE__)).'/include/general.inc.php';
include_once M_ROOT."./include/adminm.fun.php";

set_time_limit(0);
$chid = empty($chid) ? 0 : max(1,intval($chid));
$cuid = empty($cuid) ? 0 : max(1,intval($cuid));
$aid  = empty($aid) ? 0 : max(1,intval($aid));
$td_num = 5;//ÿ�е�Ԫ��ĸ���
$filename = empty($filename) ? 'Excel' : trim($filename);//excel���ļ���
$where_str = empty($q)?'':stripslashes(trim($q));
cls_env::deRepGlobalValue($where_str);
$p = empty($p)?'':stripslashes(trim($p));

//���۸�
//���������excel���ж�һ�������Ƿ񱻴۸ģ��ύ������ת���ٴ��ж������Ƿ񱻴۸ģ�
($p != md5(urlencode($where_str).$authkey)) && exit('No Permission');


if($cuid){
	//array_intersect($a_cuids,array(-1,$cuid)) || cls_message::show('û��ָ���������ݵĹ���Ȩ��');
	if(!($commu = cls_cache::Read('commu',$cuid))) cls_message::show('�����ڵĽ�����Ŀ��');
}

if(!empty($chid) && !empty($cuid)){//�����ж����Ӵ��ݹ����Ľ����ǲ�������ĳ��ģ�͵�
	!in_array($chid,$commu['chids']) && !empty($commu['chids']) && cls_message::show("IDΪ".$chid."���ĵ�ģ����IDΪ".$cuid."�Ľ�������Ӧ��");
}

$excel = new cls_exportexcel;
if(!empty($fmdata) && count($fmdata)<2){	
	cls_message::show('��ѡ�񵼳���Ŀ��');
}	
$excel->ExportExcel($filename,$fmdata,$where_str);




?>
