<link type="text/css" rel="stylesheet" href="<?php echo $cms_abs;?>images/admina/contentsAdmin.css" />
<?php
if(!defined('M_MCENTER')) exit('No Permission');
set_time_limit(0);
$chid = empty($chid) ? 0 : max(1,intval($chid));
$cuid = empty($cuid) ? 0 : max(1,intval($cuid));
$aid  = empty($aid) ? 0 : max(1,intval($aid));
$td_num = 5;//每行单元格的个数
$filename = empty($filename) ? 'Excel' : trim($filename);//excel的文件名
$where_str = empty($q)?'':stripslashes(trim($q));
$where_str = cls_env::deRepGlobalValue($where_str);
$p = empty($p)?'':stripslashes(trim($p));
//防篡改
//（点击导出excel，判断一次链接是否被篡改，提交表单，跳转后，再次判断链接是否被篡改）
($p != md5(urlencode($where_str).$authkey)) && exit('No Permission');

if($cuid){
	//array_intersect($a_cuids,array(-1,$cuid)) || cls_message::show('没有指定交互内容的管理权限');
	if(!($commu = cls_cache::Read('commu',$cuid))) cls_message::show('不存在的交互项目。');
}

if(!empty($chid) && !empty($cuid)){//用来判断链接传递过来的交互是不是属于某个模型的
	!in_array($chid,$commu['chids']) && !empty($commu['chids']) && cls_message::show("ID为".$chid."的文档模型与ID为".$cuid."的交互不对应。");
}
$excel = new cls_exportexcel;
//如果是经纪公司，则连接上不带mid
$excel->ShowFieldsTable($chid,$cuid,"请选择导出数据的项目",$where_str,$cms_abs."adminc/export_excel_content.inc.php?".(empty($ispid4)?"mid=".$curuser->info['mid']:''));

?>
