<?
include_once dirname(dirname(__FILE__)).'/include/general.inc.php';
include_once M_ROOT."./include/adminm.fun.php";
$forward = empty($forward) ? M_REFERER : $forward;
$cuid = 11;
$inajax = empty($inajax) ? 0 : 1;
$mid = empty($mid) ? 0 : max(0,intval($mid));
if(!$mid) cls_message::show('��ָ����Ҫ�ղصĶ���',$forward);
$memberid || cls_message::show('���ȵ�¼��Ա��',$forward);
if($mid == $memberid) cls_message::show('���ѵĵ��̣�����Ҫ�ղء�',$forward);
if(!($commu = cls_cache::Read('commu',$cuid)) || !$commu['available']) cls_message::show('��ǰ���ܹرա�',$forward);
if(!$curuser->pmbypmid($commu['pmid'])) cls_message::show('��û���ղص��̵�Ȩ�ޡ�',$forward);

$au = new cls_userinfo;
$au->activeuser($mid);
if(!$au->info['mid'] || !$au->info['checked'] || !in_array($au->info['mchid'],$commu['chids'])) cls_message::show('��ָ���ղض���',$forward);

$db->result_one("SELECT COUNT(*) FROM {$tblprefix}$commu[tbl] WHERE mid='$memberid' AND tomid='$mid'") && cls_message::show('ָ���ĵ����Ѿ��������ղ����ˡ�',$forward);
$sqlstr = "tomid='$mid',tomname='{$au->info['mname']}',mid='$memberid',mname='{$curuser->info['mname']}',createdate='$timestamp',checked=1";
$db->query("INSERT INTO {$tblprefix}$commu[tbl] SET $sqlstr");
cls_message::show($inajax ? 'succeed' : '�����ղسɹ���',$forward);
?>

