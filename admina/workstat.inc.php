<?php
	(!defined('M_COM') || !defined('M_ADMIN')) && exit('No Permission');
	aheader();
	$channels = cls_cache::Read('channels');
	if($re = $curuser->NoBackFunc('amember')) cls_message::show($re);
	if(!$mid = max(0,intval($mid))) cls_message::show('��ָ������Ա��');


	$startdate = empty($startdate) ? date('Y-m-d', ($timestamp - 2592000)):$startdate;
	$enddate = empty($enddate) ? date('Y-m-d', $timestamp):$enddate;


	echo form_str($entry,"?entry=$entry&mid=$mid&mname=$mname");
	tabheader_e();
	echo "<tr><td colspan=\"3\" class=\"txt txtleft\">";
	echo "<input type=\"text\" size=\"15\" id=\"startdate\" name=\"startdate\" value=\"$startdate\" class=\"Wdate\" onfocus=\"WdatePicker({readOnly:true})\" /> ������ ";
	echo "<input type=\"text\" size=\"15\" id=\"enddate\" name=\"enddate\" value=\"$enddate\" class=\"Wdate\" onfocus=\"WdatePicker({readOnly:true})\" />&nbsp;&nbsp;";
	echo strbutton('bfilter','ɸѡ');
	echo "</td></tr>";
	tabfooter();

	$startdate = strtotime($startdate);
	$enddate = strtotime("+1 day", strtotime($enddate));
	$datesql = " AND a.createdate BETWEEN $startdate AND $enddate";

	tabheader("����ͳ�� - $mname");
	$table = '<tr><td width="25%"></td><td class="txtC fB">�����/���</td><td class="txtL fB">�༭��/���</td></tr>';
	foreach($channels as $k=>$v){
	    $tab = atbl($v['chid']);
        if ( empty($tab) )
        {
            continue;
        }
		//���ͳ��
		$selectsql = "SELECT COUNT(a.aid) AS num,SUM(a.clicks) AS clk FROM {$tblprefix}".atbl($v['chid'])." a ";
		$wheresql = "WHERE a.mid='$mid'";
		$r = $db->fetch_one($selectsql.$wheresql.$datesql);
		$addstat = $r['num'].' / '.($r['clk'] ? $r['clk'] : 0);
		//�༭ͳ��
		$selectsql .= "INNER JOIN {$tblprefix}".atbl($v['chid'])." b on a.aid=b.aid ";
		$wheresql = "WHERE b.editorid='$mid'";
		$r = $db->fetch_one($selectsql.$wheresql.$datesql);
		$editstat = $r['num'].' / '.($r['clk'] ? $r['clk'] : 0);

		$table .= '<tr><td width="25%" class="txt txtright fB borderright">'.$v['cname'].'</td><td class="txtC">'.$addstat.'</td><td class="txtL" style="padding-left: 1em;">'.$editstat.'</td></tr>';
	}
	$table .= '</tbody></table>';
	echo $table;
	tabfooter();
?>
