<?php
!defined('M_COM') && exit('No Permission');
foreach(array('grouptypes','currencys','mchannels','commus','mcommus') as $k) $$k = cls_cache::Read($k);
$curuser->sub_data();

$usergroupstr = '';
foreach($grouptypes as $k => $v){
	if($curuser->info['grouptype'.$k]){
		$usergroups = cls_cache::Read('usergroups',$k);
		$usergroupstr .=  '<font class="cBlue">'.$usergroups[$curuser->info['grouptype'.$k]]['cname'].'</font> &nbsp;';
	}
}
$repugradestr = '�������õȼ���';
$currencystr='�ֽ��ʻ�'.' : <font class="cRed">'.$curuser->info['currency0'].'</font><font class="cBlue"> '.'Ԫ'.'</font>&nbsp; ';
foreach($currencys as $v){
	$tmp = $curuser->info['currency'.$v['crid']];
	$currencystr .= " $v[cname] : <font class=\"cRed\">$tmp</font><font class=\"cBlue\"> $v[unit]</font>&nbsp; ";
}
$friendnum = $db->result_one("SELECT COUNT(*) FROM {$tblprefix}mfriends WHERE mid='$memberid' AND checked=1");
$friendstr = '';
$query = $db->query("SELECT * FROM {$tblprefix}mfriends WHERE mid='$memberid' AND checked=1 ORDER BY cid DESC LIMIT 0,10");
while($row = $db->fetch_array($query)){
	$friendstr .= "<li><a href=\"{$mspaceurl}index.php?mid=$row[fromid]\" target=\"_blank\">$row[fromname]</a></li>";
}
$msgstr = '';
$pmstat = pmstat();
$msgstr .= '<tr><td>'.'�յ���'.'����'." : <font class=\"cRed\">$pmstat[1]</font></td>";
$msgstr .= '<td>'.'δ��'." : <font class=\"cRed\">$pmstat[0]</font></td></tr>";
$query = $db->query("SELECT cuid,COUNT(cid) AS cids,SUM(uread) AS ureads FROM {$tblprefix}replys cu INNER JOIN {$tblprefix}archives a ON cu.aid=a.aid WHERE a.mid='$memberid' GROUP BY cu.cuid");
while($row = $db->fetch_array($query)){
	$msgstr .= '<tr><td>'.'�յ���'.@$commus[$row['cuid']]['cname']." : <font class=\"cRed\">$row[cids]</font></td>";
	$msgstr .= '<td>'.'δ��'." : <font class=\"cRed\">".($row['cids'] - $row['ureads'])."</font></td></tr>";
}
$query = $db->query("SELECT cuid,COUNT(cid) AS cids,SUM(uread) AS ureads FROM {$tblprefix}mreplys WHERE mid='$memberid' GROUP BY cuid");
while($row = $db->fetch_array($query)){
	$msgstr .= '<tr><td>'.'�յ���'.@$mcommus[$row['cuid']]['cname']." : <font class=\"cRed\">$row[cids]</font></td>";
	$msgstr .= '<td>'.'δ��'." : <font class=\"cRed\">".($row['cids'] - $row['ureads'])."</font></td></tr>";
}
$statearr = array('0' => '�ȴ��̼�ȷ��','1' => '�ȴ�����','2' => '�ȴ�����','3' => '�ѷ���','-1' => '���','-2' => 'ȡ��');
$query = $db->query("SELECT state,COUNT(oid) AS orders FROM {$tblprefix}orders WHERE tomid='$memberid' GROUP BY state");
while($row = $db->fetch_array($query)){
	$msgstr .= '<tr><td>'.$statearr[$row['state']].'�Ķ���'."</td><td><font class=\"cRed\">$row[orders]</font></td></tr>";
}
?>
		<div class="index_con">
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td valign="top">
			<div class="w100border left">
				<dl class="userinfo">
					<dt>���ã�<font class="red"><?=$curuser->info['mname']?></font> ��ӭ��½��<div class="right lineheight200 cGray"> <?='�ϴε�½IP'?>:<?=$curuser->info['lastip']?> &nbsp; <?='�ϴε�½ʱ��'?>:<?=date('Y-m-d H:i',$curuser->info['lastvisit'])?></div></dt>							
				</dl>
				<div class="blank6"></div>
				<ul class="userinfo">
					<dl class="info2 left txtleft lineheight300">
						<dd><?='��Ŀǰ������ǣ�'.$usergroupstr?></dd>
						<dd><?='���Ļ�Ա�����ǣ�<font class="cBlue">'.$mchannels[$curuser->info['mchid']]['cname'].'</font>'?></dd>
						<dd><?=$repugradestr?></dd>
						<dd><?=$currencystr?><?php if(($commu = cls_cache::Read('commu',9)) && !empty($commu['available'])){?><br /><div align="right"><b class="spreadlink" id="get_spread" onclick="return showInfo(this.id,'?action=spread',450,108)"><?='�������,��û���'?></b></div><?php }?></dd>
					</dl>
					<div class="blank18"></div>
				</ul>
				<ul class="userinfo">
					<h3 class="infotitle"><img src="<?=MC_ROOTURL.'images/message1.gif'?>" width="22" height="18" align="absmiddle" /> <?='��Ϣ����'?><font style="font-weight:100;">>><a href="?action=pmsend"><?='���Ͷ���'?></a>&nbsp;</font></h3>
					<table width="100%" border="0" cellspacing="0" cellpadding="0"><?=$msgstr?></table>
				</ul>
				<div class="blank6"></div>
			</div>
					</td>
					<td valign="top" width="265">
			<div class="info_Statistics w100border txtleft">
				<ul class="userinfo">
					<h1 class="infotitle"><?='��Ϣͳ��'?></h1>
					<div class="blank6"></div>
					<li><?='��Ա����ĵ����� '.$curuser->info['archives']?></li>
					<li><?='��Ա�����ĵ����� '.$curuser->info['checks']?></li> 
					<li><?='��Ա������ '.$curuser->info['comments']?></li>
					<li><?='��Ա�ĵ��������� '.$curuser->info['subscribes']?></li>
					<li><?='��Ա������������ ' . $curuser->info['fsubscribes']?></li>
					<li><?="��Ա���ϴ����� {$curuser->info['uptotal']} (K)"?></li>
					<li><?="��Ա�����ظ��� {$curuser->info['downtotal']} (K)"?></li>
				</ul>
				<ul class="userinfo">
					<h1 class="infotitle"><?='�����б�'?>(<?=$friendnum?>)</h1>
					<div class="blank6"></div>
					<?=$friendstr?>
				</ul>
				<div class="blank9"></div>
			</div>
					</td>
				</tr>
			</table>
		</div>

	