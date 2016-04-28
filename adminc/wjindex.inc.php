<?
!defined('M_COM') && exit('No Permission');

$grouptypes = cls_cache::Read('grouptypes');
$mctypes = cls_cache::Read('mctypes');

function d_time_format($time, $fix = '����'){
	global $timestamp;
	$time = $timestamp - $time;
	if($time < 60){
		return '�Ÿո�'.$fix;
	}elseif($time < 1800){
		return floor($time / 60) . '����ǰ'.$fix;
	}elseif($time < 3600){
		return '��Сʱǰ'.$fix;
	}elseif($time < 86400){
		return floor($time / 3660) . 'Сʱǰ'.$fix;
	}elseif($time < 86400 * 30){
		return floor($time / 86400) . '��ǰ'.$fix;
	}else{
		return floor($time / 86400 / 30) . '����ǰ'.$fix;
	}
}
$curuser->sub_data();
$usergroupstr = '';
foreach($grouptypes as $k => $v){
	if($curuser->info['grouptype'.$k]){
		$usergroups = cls_cache::Read('usergroups',$k);
		$usergroupstr .=  '<span>'.$usergroups[$curuser->info['grouptype'.$k]]['cname'].'</span>';
	}
}

$sendchuzunum = cls_DbOther::ArcLimitCount(2, '');
$sendchushounum = cls_DbOther::ArcLimitCount(3, ''); 
$chuzunum = cls_DbOther::ArcLimitCount(2, 'enddate', 'valid'); 
$chushounum = cls_DbOther::ArcLimitCount(3, 'enddate', 'valid'); 

$tuijiannums_cz = cls_DbOther::ArcLimitCount(2, 'ccid19', '>0');
$tuijiannums_cs = cls_DbOther::ArcLimitCount(3, 'ccid19', '>0');


$showhynews = '';
$query=$db->query("SELECT * From {$tblprefix}".atbl(1)." where chid=1 and checked=1 order by aid desc limit 0,5");
while($row=$db->fetch_array($query)){
	$row['arcurl'] = cls_ArcMain::Url($row);
	$subject=cls_string::CutStr($row['subject'],28);
	$showhynews.="<li><a href=\"{$row['arcurl']}\" title=\"{$row['subject']}\" target=\"_blank\">$subject</a></li>";
}

$showxzl = '';
$query=$db->query("SELECT * From {$tblprefix}".atbl(4)." a INNER JOIN {$tblprefix}archives_4 c ON c.aid=a.aid where a.chid=4 and a.checked=1 AND (c.leixing='0' OR c.leixing='1') order by a.updatedate desc limit 0,4");
while($row=$db->fetch_array($query)){
	$row['arcurl'] = cls_ArcMain::Url($row);
	$subjectdes=$row['createdate'] == $row['updatedate'] ? "������¥�������Ϣ!(".d_time_format($row['createdate'],"����").")" : "������¥�������Ϣ!(".d_time_format($row['updatedate'],"����").")" ;
	$showxzl.="<li><a href=\"{$row['arcurl']}\" title=\"{$row['subject']}\" target=\"_blank\">{$row['subject']}</a> $subjectdes</li>";
}


$cuid = 5;
$lycommu = cls_cache::Read('commu',$cuid);
$liuyancount = $db->result_one("SELECT count(*) FROM {$tblprefix}$lycommu[tbl] WHERE tomid='$memberid'");

$mcertimg = '';
$mcertname = '';
foreach($mctypes as $k => $v){
	if($v['available']){
		if($curuser->info["mctid$k"]){
			!empty($v['icon']) && $mcertimg .= "<img src=\"$v[icon]\" alt=\"$v[cname]\" title=\"$v[cname]\" />$v[cname]&nbsp;&nbsp;";
			$mcertname .= "$v[cname] ";
		}
	}
}

/*v2 ϵͳ����*/
$valid_1str = "(enddate=0 OR enddate>'$timestamp')";

if($curuser->info['grouptype31'] || $curuser->info['grouptype32'])
	$exconfigs = cls_cache::cacRead('exconfigs',_08_EXTEND_SYSCACHE_PATH);    
?>

    <div class="r_col_l blue_a">
        <!--��Ϣ-->
        <div class="welcome gray_t">
            <div class="img"><img src="<?=empty($curuser->info['image']) ? $cms_abs."images/common/mlogo.gif" : (empty($ftp_enabled)?$cms_abs:$ftp_url).$curuser->info['image']?>" /><br/><a href="adminm.php?action=memberinfo">�޸�����</a><br/>(�ϴ�ͷ��ɻ�û���)</div>
            <div class="hint">
                <strong>���ã�<?=$curuser->info['mname']?></strong><br/><?=$mcertimg?>
                <p>
                ����ǰ���֣�<b><font color="#990000"><?=$curuser->info['currency1']?></font></b> �� &nbsp;&nbsp;<? if(!empty($curuser->info['grouptype14'])) echo '����ֵ��<b><font color="#990000">'.$curuser->info['currency2'] .'</font></b> ��'?><br/>
                ��ǰ��<b><font color="#990000"><?=$curuser->info['currency0']?></font></b> Ԫ 
                <?php if(in_array($curuser->info['mchid'],array(1,2))){ echo "&nbsp;&nbsp;�ö�������<b><font color=\"#990000\">".$curuser->info['freezds'] ."</font></b> ��";}
                ?>
                </p>
        	</div>
        <div style=" width:420px; float:right;">
            <?
			if(!empty($curuser->info['grouptype14'])){
				#$memcert = $db->result_one("SELECT count(*) FROM {$tblprefix}mcerts WHERE mid='$memberid' AND checkdate<>0");
				$query=$db->query("SELECT * FROM {$tblprefix}mcerts WHERE mid='$memberid' AND checkdate=0");
				$sqstr = '';
				while($row = $db->fetch_array($query)){
					$sqstr .= $mctypes[$row['mctid']]['cname'].' ';
				}
				if($sqstr && $mcertname){
					echo "����ǰ����֤����ǣ�<strong> $mcertname</strong>,������������ǣ�<strong> $sqstr</strong>��������У�";
				}elseif($sqstr){
					echo "����<strong> $sqstr</strong>��������У�";
				}elseif($mcertname){
					echo "����ǰ����֤����ǣ� <strong> $mcertname</strong>��Ա��";
				}else{
					$mchid = $curuser->info['mchid'];
					$mfields = cls_cache::Read('mfields',$mchid);
					foreach($mctypes as $k => $v){
						//�����жϺ�̨�Ƿ�����Ա��֤��
						if($v['available'] && in_array($mchid,explode(',',$v['mchids'])) && isset($mfields[$v['field']])){
							echo "������Ϣ<strong>��δͨ����֤</strong>�����ܷ�����Դ��Ϣ��<a href=\"?action=mcerts\"><strong>�����������ϡ��ύ��֤</strong></a>�ɣ�<br/>";
							break;
						}
					}

				}
				if($curuser->info['grouptype14'] == 8 && !empty($curuser->info['grouptype14date'])){
					echo "<br/>����<strong>�߼�������</strong>Ȩ��ʣ��<font class=\"red\"><b>".ceil(($curuser->info['grouptype14date']-$timestamp)/86400)."</b></font>�죬��Ҫ�ӳ�Ȩ����Ч�ڣ������>>><a style=\"color:red;font-weight:bold;\" href=\"?action=gaoji\">����</a>";
				}
			}elseif(!empty($curuser->info['grouptype13'])){
				echo "����<strong>���˻�Ա</strong>��������������Ϣ�뾭������Ա����ˣ�ע�ᾭ�����������Ա��ˡ�";
			}elseif(!empty($curuser->info['grouptype15'])){
				echo "����<strong>���͹�˾��Ա</strong>�����ɹ���<a href='?action=chushouarchives&ispid4=1'>�����˶��ַ�Դ</a> �� <a href='?action=chuzuarchives&ispid4=1'>�����˳��ⷿԴ</a>��";
			}elseif(!empty($curuser->info['grouptype31'])){
				if($curuser->info['grouptype31'] == 102){
					echo "����<strong>VIP��˾</strong>��Ա��������������ӵ�и���ķ���������";
					if(!empty($curuser->info['grouptype31date'])) 
						echo "<br/>����<strong>VIP��˾</strong>Ȩ��ʣ��<font class=\"red\"><b>".ceil(($curuser->info['grouptype31date']-$timestamp)/86400)."</b></font>�죬��Ҫ�ӳ�Ȩ����Ч�ڣ������>>><a style=\"color:red;font-weight:bold;\" href=\"?action=vip&type=vipgs\">����</a>";
				}else{
					echo "����<strong>��ͨ��˾</strong>��Ա�����������������������ơ�<br/>����<strong>VIP��˾</strong>��Ա����������������󷢲�������<a style=\"color:red;font-weight:bold;\" href=\"?action=vip&type=vipgs\">��������</a>";
				}
			}elseif(!empty($curuser->info['grouptype32'])){				
				if($curuser->info['grouptype32'] == 104){
					echo "����<strong>VIP�̼�</strong>��Ա��������������ӵ�и���ķ���������";
					if(!empty($curuser->info['grouptype32date']))
						echo "<br/>����<strong>VIP�̼�</strong>Ȩ��ʣ��<font class=\"red\"><b>".ceil(($curuser->info['grouptype32date']-$timestamp)/86400)."</b></font>�죬��Ҫ�ӳ�Ȩ����Ч�ڣ������>>><a style=\"color:red;font-weight:bold;\" href=\"?action=vip&type=vipsj\">����</a>";
				}else{
					echo "����<strong>��ͨ�̼�</strong>��Ա�����������������������ơ�<br/>����<strong>VIP�̼�</strong>��Ա����������������󷢲�������<a style=\"color:red;font-weight:bold;\" href=\"?action=vip&type=vipsj\">��������</a>";
				}
			}
			if(!empty($curuser->info['grouptype34'])){	
				echo "<br/>����<strong>�ʴ�ר��</strong>���ɵ�� <a href='?action=zhuanjia_manage'>�޸�ר�����ϣ�</a>";
			}else{
				$memid = $curuser->info['mid'];
				$commu = cls_cache::Read('commu',42); 
				$fval = $db->result_one("SELECT mid FROM {$tblprefix}$commu[tbl] WHERE mid='$memid'"); 
				if($fval){
					echo "<br/>���Ѿ�<strong>�������ʴ�ר��</strong>����δ��ˣ��ɵ�� <a href='?action=zhuanjia_manage'>�޸����ϣ�</a>";
				}else{
					echo "<br/>��������<strong>�ʴ�ר��</strong>���ɵ�� <a href='?action=zhuanjia_manage'>����ר�ң�</a>";
				}
			}
			?>

            </div>
            <? if(!empty($curuser->info['grouptype14'])){ ?>
            <div class="btn_a">
            	<? if($curuser->pmbypmid('16')){ ?>
                <a href="?action=chushouadd"><span>�������ַ�</span></a>
                <a href="?action=chushouarchives"><span>������ַ�</span></a>
                <a href="?action=chuzuadd"><span>�������ⷿ</span></a>
                <a href="?action=chuzuarchives"><span>������ⷿ</span></a>
                <? }if(empty($curuser->info['grouptype14'])){ ?><a href="?action=tuijianarchives"><span>���õ����Ƽ�λ</span></a><? } ?>
            </div>
            <? } ?>
        </div>
        <!--�ճ�����-->		
        <ul class="cor_box">
            <li class="cor tl"></li>
            <li class="cor tr"></li>
            <li class="con">
                <ul>
                    <li class="box_head"><i class="ico_manage4">&nbsp;</i>�ճ�����</li>
                    <li class="box_body">
					<? if($curuser->info['grouptype14'] || $curuser->info['grouptype13']){ ?>
                    	<? if($curuser->pmbypmid('16')){ ?>
                            <ul>
                                <li class="cap"><strong>��Դ����</strong></li>
                                <li class="infos"><span>���ⷿԴ��</span><span>�ѷ���<em><?=$sendchuzunum?></em>��</span> <span>�ϼ�<em><?=$chuzunum?></em>��</span> <span>�¼�<em><?=$sendchuzunum-$chuzunum?></em>��</span> </li>
                                <li>
                                	<a href="?action=chuzuadd">�������ⷿ&gt;&gt;</a>
                                	<a href="?action=chuzuarchives">������ⷿ&gt;&gt;</a>
                                </li>
                                <li class="infos"><span>���ַ�Դ��</span><span>�ѷ���<em><?=$sendchushounum?></em>��</span> <span>�ϼ�<em><?=$chushounum?></em>��</span> <span>�¼�<em><?=$sendchushounum-$chushounum?></em>��</span> </li>
                                <li>
                                	<a href="?action=chushouadd">�������ַ�&gt;&gt;</a>
                                	<a href="?action=chushouarchives">������ַ�&gt;&gt;</a> </li>
                            </ul>
                        <?
                        } 
						if($curuser->pmbypmid('14')){ 
                            $sendqiuzunum = cls_DbOther::ArcLimitCount(9, ''); 
                            $sendqiugounum = cls_DbOther::ArcLimitCount(10, ''); 
                            
                            $qiuzunum = cls_DbOther::ArcLimitCount(9, 'enddate', 'valid'); 
                            $qiugounum = cls_DbOther::ArcLimitCount(10, 'enddate', 'valid'); 

    						?>
                            <ul>
                                <li class="cap"><strong>�������</strong></li>
                                <li class="infos"><span>������Ϣ��</span><span>�ѷ���<em><?=$sendqiuzunum?></em>��</span> <span>������<em><?=$qiuzunum?></em>��</span> <span>�¼�<em><?=$sendqiuzunum-$qiuzunum?></em>��</span> </li>
                                <li>
                                	<a href="?action=xuqiuarchive&chid=9">��������&gt;&gt;</a>
                                	<a href="?action=xuqiuarchives&chid=9">��������&gt;&gt;</a>
                                </li>
                                <li class="infos"><span>����Ϣ��</span><span>�ѷ���<em><?=$sendqiugounum?></em>��</span> <span>����<em><?=$qiugounum?></em>��</span> <span>�¼�<em><?=$sendqiugounum-$qiugounum?></em>��</span> </li>
                                <li>
                                	<a href="?action=xuqiuarchive&chid=10">������&gt;&gt;</a>
                                	<a href="?action=xuqiuarchives&chid=10">������&gt;&gt;</a> </li>
                            </ul>
    						<?
						}
					}   
                    if($curuser->info['grouptype14']) { ?>
                		<? if($curuser->pmbypmid('17')){ ?>
    						<ul>
                                <li class="cap"><strong>���̹���</strong></li>
                                <li class="infos"><span>���ַ��Ƽ���</span> 
                                    <span>���Ƽ�<em><?=$tuijiannums_cs?></em>��</span> 
                                </li>
                                <li class="infos"><span>���ⷿ�Ƽ���</span> 
                                    <span>���Ƽ�<em><?=$tuijiannums_cz?></em>��</span> 
                                </li>
                                <li><a href="?action=tuijianarchives">�����Ƽ���Ϣ&gt;&gt;</a>
                                </li>
                                <li class="infos"><span>�������ԣ�</span> <span>����<em><?=$liuyancount?></em>��������</span></li>
                                <li><a href="?action=liuyans">�鿴����&gt;&gt;</a>
                                </li>
                            </ul>
					    <? 
						}
					}  
                    if($curuser->info['grouptype31']) {
                        $newsChid = 104; $designChid = 101; $designCaseChid = 102;
                        $newsValidNum = cls_DbOther::ArcLimitCount($newsChid, 'enddate', 'valid'); 
                        $newsTotalNum = cls_DbOther::ArcLimitCount($newsChid, ''); 
                        $designValidNum = cls_DbOther::ArcLimitCount($designChid, 'enddate', 'valid'); 
                        $designTotalNum = cls_DbOther::ArcLimitCount($designChid, ''); 
                        $designCaseValidNum = cls_DbOther::ArcLimitCount($designCaseChid, 'enddate', 'valid'); 
                        $designCaseTotalNum = cls_DbOther::ArcLimitCount($designCaseChid, ''); 
                        
                        $cuid = 31;
                        $commu_yezhupl = cls_cache::Read('commu', $cuid);
                        $yezhuplNum = $db->result_one("SELECT count(*) FROM {$tblprefix}$commu_yezhupl[tbl] WHERE tomid='$memberid'");
                        
                        empty($yezhuplNum) && $yezhuplNum = 0;
						?>
                        <? if($curuser->pmbypmid('114')){?>
						<ul>
                            <li class="cap"><strong>��̬����</strong></li>
                            <li class="infos"><span>����Ч <em><?=$newsValidNum?></em>/<strong><?=$newsTotalNum?></strong></span></li>
                            <li>
                            	<a href="?action=designNews_a&chid=104&caid=512">������̬&gt;&gt;</a>
                            	<a href="?action=designNews_s">����̬&gt;&gt;</a>
                            </li>
                        </ul>
                        <? }
							if($curuser->pmbypmid('101')){?>
						<ul>
                            <li class="cap"><strong>���ʦ����</strong></li>
                            <li class="infos"><span>����Ч <em><?=$designValidNum?></em>/<strong><?=$designTotalNum?></strong></span></li>
                            <li>
                            	<a href="?action=design_a">�������ʦ&gt;&gt;</a>
                            	<a href="?action=design_s">�������ʦ&gt;&gt;</a>
                            </li>
                        </ul>
						<ul>
                            <li class="cap"><strong>��������</strong></li>
                            <li class="infos"><span>����Ч <em><?=$designCaseValidNum?></em>/<strong><?=$designCaseTotalNum?></strong></span></li>
                            <li>
                            	<a href="?action=designCase_a">��������&gt;&gt;</a>
                            	<a href="?action=designCase_s">������&gt;&gt;</a>
                            </li>
                        </ul>
						<ul>
                            <li class="cap"><strong>���̹���</strong></li>
                            <li class="infos"><span>���̵�����</span> <span>����<em><?=$yezhuplNum?></em>������</span></li>
                            <li><a href="?action=commu_yezhupl">�鿴����&gt;&gt;</a>
                            </li>
                        </ul>
						<? 
							}
					} 
                    if($curuser->info['grouptype32']) {
                        $newsChid = 104; $goodsChid = 103;
                        $newsValidNum = cls_DbOther::ArcLimitCount($newsChid, 'enddate', 'valid'); 
                        $newsTotalNum = cls_DbOther::ArcLimitCount($newsChid, ''); 
                        $goodsValidNum = cls_DbOther::ArcLimitCount($goodsChid, 'enddate', 'valid'); 
                        $goodsTotalNum = cls_DbOther::ArcLimitCount($goodsChid, ''); 
                        
                        $cuid = 34;
                        $commu_brandsjly = cls_cache::Read('commu', $cuid);
                        $brandsjlyNum = $db->result_one("SELECT count(*) FROM {$tblprefix}$commu_brandsjly[tbl] WHERE tomid='$memberid'");
                        
                        empty($brandsjlyNum) && $brandsjlyNum = 0;
						?>
                        <? if($curuser->pmbypmid('114')){?>
						<ul>
                            <li class="cap"><strong>��̬����</strong></li>
                            <li class="infos"><span>����Ч <em><?=$newsValidNum?></em>/<strong><?=$newsTotalNum?></strong></span></li>
                            <li>
                            	<a href="?action=designNews_a">������̬&gt;&gt;</a>
                            	<a href="?action=designNews_s">����̬&gt;&gt;</a>
                            </li>
                        </ul>
                        <? }
							if($curuser->pmbypmid('103')){?>
						<ul>
                            <li class="cap"><strong>��Ʒ����</strong></li>
                            <li class="infos"><span>����Ч <em><?=$goodsValidNum?></em>/<strong><?=$goodsTotalNum?></strong></span></li>
                            <li>
                            	<a href="?action=designGoods_a">������Ʒ&gt;&gt;</a>
                            	<a href="?action=designGoods_s">������Ʒ&gt;&gt;</a>
                            </li>
                        </ul>
						<ul>
                            <li class="cap"><strong>���̹���</strong></li>
                            <li class="infos"><span>�������ԣ�</span> <span>����<em><?=$brandsjlyNum?></em>������</span></li>
                            <li><a href="?action=commu_brandsjly">�鿴����&gt;&gt;</a>
                            </li>
                        </ul>
						<?
						}
					} 
					if($curuser->info['grouptype15']) { // ���͹�˾
                        $idstr = '';
                        $namesql = "select m.mid,m.mname FROM {$tblprefix}members m WHERE m.mchid=2 AND pid4='$memberid' AND incheck4=1";
                        $query = $db->query($namesql);
                        while($row = $db->fetch_array($query)){
                        	$idstr .= ','.$row['mid'];
                        }
                        $idstr = empty($idstr) ? "0" : substr($idstr,1); 
                        $cnt_ch2 = 0;
                        $cnt_ch3 = 0;
                        if(!empty($idstr)){
                        	$cnt_ch2 = $db->result_one("SELECT COUNT(*) FROM {$tblprefix}".atbl(2)." WHERE mid IN($idstr) ");
                        	$cnt_ch3 = $db->result_one("SELECT COUNT(*) FROM {$tblprefix}".atbl(3)." WHERE mid IN($idstr) ");	
                        	empty($cnt_ch2) && $cnt_ch2 = 0;
                        	empty($cnt_ch3) && $cnt_ch3 = 0;
                        }
                        $cnt_ch104 = cls_DbOther::ArcLimitCount(104, ''); 
						
						?>
						<ul>
                            <li class="cap"><strong>��Դ����</strong></li>
                            <li class="infos">
                            <span><a href='?action=chushouarchives&ispid4=1'>�����˶��ַ�Դ</a>(<em><?php echo $cnt_ch3; ?></em>)</span> 
                            <span><a href='?action=chuzuarchives&ispid4=1'>�����˳��ⷿԴ</a>(<em><?php echo $cnt_ch2; ?></em>)</span>
                            </li>
                            <li>
                            	<a href="?action=commu_yixiang">��Դ�������&gt;&gt;</a>
                            </li>
                        </ul>
						<ul>
                            <li class="cap"><strong>���̹���</strong></li>
                            <li class="infos">
                            <span><a href="?action=designNews_s">��̬����</a>(<em><?php echo $cnt_ch104; ?></em>)</span> 
                            <span><a href="?action=designNews_a&chid=104&caid=554">����&gt;&gt;</a></span>
                            </li>
                            <li>
                            	<a href='?action=agents&incheck=1'>�����˹���</a>
                            </li>
                        </ul>
						<?
						  
					}
						
					if($curuser->info['grouptype33']) { // ��¥��˾
                        $sql_ids = "SELECT loupan FROM {$tblprefix}members_13 WHERE mid='$memberid'"; 
                        $loupanids = $db->result_one($sql_ids); if($loupanids) $loupanids = substr($loupanids,1); 
                        if(empty($loupanids)) $loupanids = 0;
                        
                        $cu_yx = $db->result_one("SELECT count(*) FROM {$tblprefix}commu_yx WHERE aid IN(SELECT aid FROM {$tblprefix}".atbl(4)." WHERE aid IN($loupanids))");
                        $cu_dp = $db->result_one("SELECT count(*) FROM {$tblprefix}commu_zixun WHERE aid IN(SELECT aid FROM {$tblprefix}".atbl(4)." WHERE aid IN($loupanids))");
                        empty($cu_yx) && $cu_yx = 0;
                        empty($cu_dp) && $cu_dp = 0;
						
						?>
						<ul>
                            <li class="cap"><strong>¥�̹���</strong></li>
                            <li class="infos">
                            <span><a href='?action=louyx'>¥������</a>(<em><?php echo $cu_yx; ?></em>)</span> 
                            <span><a href='?action=loupan_pinlun'>¥�̵���</a>(<em><?php echo $cu_dp; ?></em>)</span>
                            </li>
                            <li>
                            	<a href="?action=loupans">����¥��&gt;&gt;</a>
                            </li>
                        </ul>
						<?
						  
					}
						
                    $qa_ch = cls_DbOther::ArcLimitCount(106, ''); 
                    
                    $qa_get = $db->result_one("SELECT count(*) FROM {$tblprefix}".atbl(106)." WHERE tomid='$memberid'");
                    $qa_rep = $db->result_one("SELECT count(*) FROM {$tblprefix}commu_answers WHERE mid='$memberid'");
                    
                    empty($qa_get) && $qa_get = 0;
                    empty($qa_rep) && $qa_rep = 0;
						
						?>
                        
                        <ul>
                            <li class="cap"><strong>�����ʴ�</strong></li>
                            <li class="infos">  
                            <span><a href="?action=wenda_manage&actext=qget">���ҵ�����</a>��<em><?php echo $qa_get; ?></em>��</span> 
                            <span><a href="?action=wenda_manage&actext=qout">�ҵ�����</a>��<em><?php echo $qa_ch; ?></em>��</span> 
                            <span><a href="?action=wenda_manage&actext=answer">�ҵĻش�</a>��<em><?php echo $qa_rep; ?></em>��</span>
                            </li>
                            <li>
                            	<a href="?action=zhuanjia_manage">�ʴ�ר������/�޸�����&gt;&gt;</a>
                            </li>
                        </ul>
                        
                    </li>
                </ul>
            </li>
            <li class="cor bl"></li>
            <li class="cor br"></li>
        </ul>		
        <!--�ҵ��ƹ�-->
    </div>
    <!--ҳ���Ҳ�-->
    <style type="text/css">.box{margin-bottom: 10px;}.box .box_head a{background: none;}</style>
    <div class="r_col_r">
        <div class="box">
            <div class="box_head">
                <a href="{c$cnode [tclass=cnode/] [listby=ca/] [casource=574/]}{indexurl}{/c$cnode}" target="_blank">����&gt;&gt;</a><i class="ico_infos">&nbsp;</i>��վ����</div>
            <ul>
                {c$archives [tclass=archives/] [chids=109/] [chsource=2/] [casource=1/] [caids=574/]}
				<li><a href="{arcurl}" target="_blank">{c$text [tclass=text/] [tname=subject/] [trim=24/] [ellip=.../] [color=color/]}{/c$text}</a></li>
                {/c$archives}
            </ul>
        </div>
        <div class="box dongtai">
            <div class="box_head">
                <a href="{c$cnode [tclass=cnode/] [listby=ca/] [casource=575/]}{indexurl}{/c$cnode}" target="_blank">����&gt;&gt;</a><i class="ico_share">&nbsp;</i>���ֵ���</div>
            <ul class="graywhite_t blue_a">
                {c$archives [tclass=archives/] [chids=109/] [chsource=2/] [casource=1/] [caids=575/]}
				<li><a href="{arcurl}" target="_blank">{c$text [tclass=text/] [tname=subject/] [trim=24/] [ellip=.../] [color=color/]}{/c$text}</a></li>
                {/c$archives}
            </ul>
            <div align="center">
                <span id="lblDisplayListMsg"></span></div>
        </div>
        <?php 
        if (!empty($curuser->info['grouptype33'])) {
         ?>
            <div class="box dongtai">
                <div class="box_head">
                    <a href="{c$cnode [tclass=cnode/] [listby=ca/] [casource=2/]}{indexurl}{/c$cnode}" target="_blank">����&gt;&gt;</a><i class="ico_share">&nbsp;</i>¥�̶�̬</div>
                <ul class="graywhite_t blue_a">
                    <?=$showxzl?>
                </ul>
                <div align="center">
                    <span id="lblDisplayListMsg"></span></div>
            </div>
        <?php 
	    } 
		if (!empty($curuser->info['grouptype14'])||!empty($curuser->info['grouptype15'])) {
        ?>
            <div class="box dongtai">
                <div class="box_head">
                    <a href="{c$cnode [tclass=cnode/] [listby=ca/] [casource=565/]}{indexurl}{/c$cnode}" target="_blank">����&gt;&gt;</a><i class="ico_share">&nbsp;</i>�����˰���</div>
                <ul class="graywhite_t blue_a">{c$archives [tclass=archives/] [chids=109/] [chsource=2/] [caidson=1/] [casource=1/] [caids=565/] [validperiod=1/]}                <li><a href="{arcurl}" target="_blank">{c$text [tclass=text/] [tname=subject/] [trim=24/] [ellip=.../] [color=color/]}{/c$text}</a></li>
                    {/c$archives}
                </ul>
                <div align="center">
                    <span id="lblDisplayListMsg"></span></div>
            </div>
        <?php 
    	} 
		if (!empty($curuser->info['grouptype31'])) {
        ?>
            <div class="box dongtai">
                <div class="box_head">
                    <a href="{c$cnode [tclass=cnode/] [listby=ca/] [casource=579/]}{indexurl}{/c$cnode}" target="_blank">����&gt;&gt;</a><i class="ico_share">&nbsp;</i>װ�޹�˾����</div>
                <ul class="graywhite_t blue_a">
                    {c$archives [tclass=archives/] [chids=109/] [chsource=2/] [casource=1/] [caids=579/]}
    				<li><a href="{arcurl}" target="_blank">{c$text [tclass=text/] [tname=subject/] [trim=24/] [ellip=.../] [color=color/]}{/c$text}</a></li>
                    {/c$archives}
                </ul>
                <div align="center">
                    <span id="lblDisplayListMsg"></span></div>
            </div>
        <?php 
    	} 
		if (!empty($curuser->info['grouptype32'])) {
        ?>
            <div class="box dongtai">
                <div class="box_head">
                    <a href="{c$cnode [tclass=cnode/] [listby=ca/] [casource=580/]}{indexurl}{/c$cnode}" target="_blank">����&gt;&gt;</a><i class="ico_share">&nbsp;</i>Ʒ���̼Ұ���</div>
                <ul class="graywhite_t blue_a">
                    {c$archives [tclass=archives/] [chids=109/] [chsource=2/] [casource=1/] [caids=580/]}
    				<li><a href="{arcurl}" target="_blank">{c$text [tclass=text/] [tname=subject/] [trim=24/] [ellip=.../] [color=color/]}{/c$text}</a></li>
                    {/c$archives}
                </ul>
                <div align="center">
                    <span id="lblDisplayListMsg"></span></div>
            </div>
        <?php 
    	} 
        ?>
    </div>