<?
(!defined('M_COM') || !defined('M_ADMIN')) && exit('No Permission');
aheader();
if($re = $curuser->NoBackFunc('farchive')) cls_message::show($re); # ��������Ȩ��

# ������������
$fcaid = cls_fcatalog::InitID(@$fcaid);
if(!cls_fcatalog::Config($fcaid)) cls_message::show('��ָ����������');
if($re = $curuser->NoBackPmByTypeid($fcaid,'fcaid')) cls_message::show($re);# ��ǰ��������ĺ�̨����Ȩ��


$page = !empty($page) ? max(1, intval($page)) : 1;
submitcheck('bfilter') && $page = 1;

$fromsql = "FROM {$tblprefix}farchives a";
$wheresql = "WHERE a.fcaid='$fcaid'";
$filterstr = "&fcaid=$fcaid";

$checked = isset($checked) ? (int)$checked : '-1';
if($checked != -1){
	$wheresql .= " AND a.checked='$checked'";
	$filterstr .= "&checked=".$checked;
}

$valid = isset($valid) ? (int)$valid : '-1';
if($valid != -1){
	if($valid){
		$wheresql .= " AND a.startdate<'$timestamp' AND (a.enddate='0' OR a.enddate>'$timestamp')";
	}else{
		$wheresql .= " AND (a.startdate>'$timestamp' OR (a.enddate!='0' AND a.enddate<'$timestamp'))";
	}
	$filterstr .= "&valid=".$valid;
}


$keyword = empty($keyword) ? '' : $keyword;
if($keyword){
	$wheresql .= " AND (a.mname".sqlkw($keyword,1)." OR a.subject".sqlkw($keyword,1).")";
	$filterstr .= "&keyword=".rawurlencode(stripslashes($keyword));
}

$area_coid = cls_fcatalog::Config($fcaid,'farea'); //�Ƿ��������
if($area_coid){
	$farea = empty($farea) ? '0' : intval($farea);
	if($farea){ // farea LIKE '%,$farea,%'
		$wheresql .= " AND FIND_IN_SET('$farea',farea) "; 
		$filterstr .= "&farea=$farea";
	}
	$area_arr = cls_cache::Read('coclasses',$area_coid);
} 

$vflag = ''; //�Ƿ��� �������
if(!cls_fcatalog::Config($fcaid,'ftype')){
	$fields = cls_fcatalog::Field($fcaid);
	foreach($fields as $k => $v){ //multitext,
		if(in_array($v['datatype'],array('htmltext','image','flash'))){
			$vflag = '(������ͼƬ��Ч��)';
			break;
		}
	}
}

$path = dirname(__FILE__) . DIRECTORY_SEPARATOR;
switch(cls_fcatalog::Config($fcaid,'ftype'))
{
    case 1 : $file = $path . 'adv_managements.php'; break;
    default : $file =  $path . 'farchives_list.php'; break;
}

if(is_file($file)) {
    include $file;
    exit;
} else {
    exit('ϵͳ���󣬸ù���ҳ�����ڣ�');
}