<?php
!defined('M_COM') && exit('No Permission');
$chid = empty($chid) ? 3 : max(2,min(3,intval($chid)));
$cuid = 36;
$cid = empty($cid) ? '' : max(0,intval($cid));
if(!($commu = cls_cache::Read('commu',$cuid)) || !$commu['available']) cls_message::show('ί�й����ѹرա�');
$result = $cresult = array();
$query = $db->query("SELECT * FROM {$tblprefix}$commu[tbl] WHERE chid='$chid' AND mid='$memberid'");

while($r = $db->fetch_array($query)){
	if(!empty($r['pid'])){
		$ar = $db->fetch_one("SELECT a.thumb".aurl_fields()." FROM {$tblprefix}".atbl(4)." a WHERE a.aid='$r[pid]' ");
		if(!empty($ar)) {
			$r['arcurl'] = @cls_ArcMain::Url($ar,-1); 
			$r['arcurl'] = empty($r['arcurl']) ? '' : $r['arcurl'];
			$r['thumb'] = empty($r['arcurl']) ? '' : $ar['thumb'];
		}
	}
	$result[] = $r; 
	$cquery = $db->query("SELECT m.*,s.xingming,s.lxdh,s.szqy,s.image,w.jjrstatus,w.wid FROM {$tblprefix}weituos w INNER JOIN {$tblprefix}members m ON w.tmid=m.mid INNER JOIN {$tblprefix}members_sub s ON m.mid=s.mid WHERE cid='$r[cid]'");
	while($rw = $db->fetch_array($cquery)){
		$rw['mspacehome'] = cls_Mspace::IndexUrl($rw);
		$cresult[$r['cid']][] = $rw;
	}
}

my_wt_header($action);
?>

<ul class="wttab mT10">
	<?php
	foreach($result as $k=>$v) echo "<li".($k == 0 ? ' class="act"' : '')." title=\"$v[lpmc]\" onclick=\"weituotab('$v[cid]')\" id=\"weituoli$v[cid]\">$v[lpmc]</li>";	
	?>
</ul>
<?php

foreach($result as $key=>$row){
	$row['thumb']  = empty($row['thumb'])?'':$row['thumb'];
?>
<div id="weituo<?php echo $row['cid']?>"<?php echo $key == 0 ? '' : ' style="display:none"'?>>
	<div class="p10 wtintro">
		<span class="w610 lh22px">
		 <!--<a class="wtimg">{c$lpimg125_75 [cname=lpimg125_75/] [tclass=image/] [tname=$row[thumb]/] [maxwidth=125/] [maxheight=75/] [thumb=2/]}<img src="{url_s}" width="125" height="75"/>{/c$lpimg125_75}</a>-->
		 <div>
		 	<?php 
				if(!empty($row['aid'])){ 
					echo "<a style=\"color:red; font-weight:800; font-size:14px;\" href=\"".$row['arcurl7']."\"  target=\"_blank\" title=\"". $row['lpmc']."\">".$row['lpmc']."</a>";
				}else{ 			
            		echo "<font style=\"color:red; font-weight:800; font-size:14px;\">".$row['lpmc']."</font>";		
				} 
			?>
         </div>
		 <div>��ַ��<?php echo $row['address']?></div>
		 <div>���ͣ�<?php echo $row['shi']?>��<?php echo $row['ting']?>��<?php echo $row['wei']?>��</div>
		 <div>�����<?php echo $row['mj']?>ƽ����</div>
		</span>
		<span class="w150"><span class="wtprice" id="price<?php echo $row['cid']?>"><?php echo $row['zj']?></span><?php echo $row['chid'] == 3 ? '��Ԫ' : 'Ԫ/��'?></span>
		<span>
		<a class="button" href="javascript:void(0)" onclick="modifyPrice('<?php echo $row['cid']?>')">�޸ļ۸�</a>
		<a class="button" href="javascript:void(0)" onclick="if(confirm('��ȷ��Ҫɾ����Դ��')){delWeituo('<?php echo $row['cid']?>')}">ɾ����Դ</a>
		</span>
	</div>
	<div class=" blank10"></div>
	<div class="jxwt">
    	����ί����<font color="red" id="hweituonum<?php echo $row['cid'];?>"><?php echo count($cresult[$row['cid']]);?></font>λ�����ˣ�������ί��<font color="red" id="nweituonum<?php echo $row['cid'];?>"><?php echo (5-count($cresult[$row['cid']]));?></font>λ�����ˡ�
        <div id="wtcontinue" <?php echo count($cresult[$row['cid']])==5 ? "style=\"display:none\"":'';?>><a href="<?php echo $cms_abs?>info.php?fid=101&action=step2&is_mc=1&chid=<?php echo $chid?>&cid=<?php echo $row['cid']?>&tel=<?php echo $row['tel']?>&ccid1=<?php echo $row['ccid1']?>&lpmc=<?php echo $row['lpmc']?>" target="_blank" style="color:red;">>>>����ί��</a></div>
    </div>
	<table cellspacing="1" cellpadding="0" border="0" width="780px" class="glwdfz3-table">
	  <tbody>
		<tr>
		  <td height="30" align="center" width="355px" colspan="2"><strong class="lv">��ί�еľ�����</strong></td>
		  <td align="center" width="235px"><strong class="lv">�������</strong></td>
		  <td align="center" width="200px"><strong class="lv">����</strong></td>
		</tr>
		<?
		if(!empty($cresult[$row['cid']])){
			foreach($cresult[$row['cid']] as $k=>$c){
				if($c['jjrstatus'] == 1){
					$statusstr = '<font color="red">ί���ѱ��ܾ�</font>';
				}elseif($c['jjrstatus'] == 2){
					$statusstr = '<font color="green">ί���ѱ�����</font>';	
				}else{
					$statusstr = '�ȴ�����';
				}
				$_xingming = empty($c['xingming'])?'':$c['xingming'];
				?>
                    <tr id="tr<?php echo $c['wid'];?>">
                      <td height="110" align="center"><a href="<?php echo $c['mspacehome'];?>" target="_blankank"><img alt="<?php echo $_xingming?>" width="66" height="85" src="{c$image66_85_url [cname=image66_85_url/] [tclass=image/] [tname=$c[image]/] [val=u/] [maxwidth=66/] [maxheight=85/] [emptyurl=images/common/mlogo.gif/]}{url_s}{/c$image66_85_url}"/></a><br></td>
                      <td class="line22 p-l10"><strong><?php echo $c['xingming'] ? $c['xingming'] : 'δ֪����';?></strong><br>
                        ��������{if $c['szqy']}{c$weituojhjjrqy [cname=��������������/] [tclass=mcnode/] [cnsource=ccid1/] [cnid=$c[szqy]/]}<a href='{mcnurl1}' target="_blank">{title}</a>{/c$weituojhjjrqy}{else}δ֪{/if}<br>
                        �ֻ���<?php echo $c['lxdh'];?></td>
                      <td align="center"><?php echo $statusstr;?></td>
                      <td align="center"><a class="button" onclick="if(confirm('��ȷ��Ҫȡ��ί�У�'))cancelWeituo('<?php echo $c['wid'];?>','<?php echo $row['cid'];?>')">ȡ��ί��</a></td>
                    </tr>
				<?php
			} 
		}
	?>
	  </tbody>
	</table>
</div>
<?
}
?>
<script>var $cms_abs = '<?php echo $cms_abs?>',ajax = new Ajax('HTML');</script>
<script type="text/javascript" src="<?=$cmsurl?>adminc/js/myweituo.js"></script>
