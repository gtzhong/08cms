<?php
/*
ϵͳ����Ҫ���ֶΡ��ɼ����ڵ���ɷ�����Ҫ����չ��������������
�ֶΣ���ѡ�ֶ�ѡ��������Ļ�ȡ����Ŀ�ֶ�id�Ļ�ȡ
�ɼ����ɼ����������
�ڵ���ɷ�����������ϵ�id�Ļ�ȡ
*/


function u_auto_cntpl_id($cnstr=''){//��Ŀ�ڵ���Զ��庯��
	$cntpls = cls_cache::Read('cntpls');
	parse_str($cnstr,$na);
	$re = 0;
	if(!empty($na['caid'])){
		if($na['caid'] == 3){//����
			$re = count($na) == 1 ? 15 : 1;
		}elseif($na['caid'] == 4){//����
			$re = count($na) == 1 ? 16 : 2;
		}elseif($na['caid'] == 2){//¥��
			$re = count($na) == 1 ? 9 : 3;
		}elseif($na['caid'] == 11){//����
			$re = 4;
		}else{
			$a = cls_cache::Read('catalogs');
			$topcaid = cls_catalog::cn_upid($na['caid'],$a);
			if($topcaid == 1){//��Ѷ
				$re = $na['caid'] == $topcaid ? 11 : 5;
			}elseif($topcaid == 8){//����
				$re = 6;
			}elseif($topcaid == 30){//��Ƶ
				$re = $na['caid'] == $topcaid ? 12 : 7;
			}elseif($topcaid == 37){//ר��
				$re = 8;
			}
		}
	}
	$re = isset($cntpls[$re]) ? $re : 0;
	unset($cntpls,$a,$na);
	return $re;
}
function u_auto_mcntpl_id($mcnvar = '',$mcnid = 0){//��Ա�ڵ���Զ��庯��
	$mcntpls = cls_cache::Read('mcntpls');
	$re = 0;
	if($mcnvar == 'ccid1'){
		$re = 14;
	}
	$re = isset($mcntpls[$re]) ? $re : 0;
	unset($mcntpls);
	return $re;
}

function u_inhuxingids($aid){
	global $db,$tblprefix;
	if(!$aid || !($abrel = cls_cache::Read('abrel',2))) return array();
	$rets = array();
	$query = $db->query("SELECT a.* FROM {$tblprefix}archives a INNER JOIN {$tblprefix}$abrel[tbl] b ON b.inid=a.aid  WHERE b.pid='$aid' AND b.arid=2");
	while($r = $db->fetch_array($query)){
		$rets[$r['aid']] = " <a href=\"".cls_url::view_arcurl($r)."\" target=\"_blank\" title=\"$r[subject]\">".($r['thumb'] ? "<img src=\"".cls_url::view_atmurl($r['thumb'])."\" width=\"80\" height=\"80\">" : cls_string::CutStr($r['subject'],20))."</a>";
	}
	return $rets;
}

function g_dateformat($timestamp,$dateformat='Y-n-j H:i:s'){
	$result = @date("$dateformat",$timestamp);
	return $result;
}
function lp_zt(){
	  global $aid,$db,$tblprefix;//���Ӵ��ݹ�����aid
	  $aid = empty($aid)?0:max(1,intval($aid));
	  $lp_arr = array();	
	  $_lp_zt = $db->query("SELECT a.aid,a.subject FROM {$tblprefix}".atbl(4)." a INNER JOIN {$tblprefix}aalbum_kfhdlp b ON a.aid = b.inid WHERE b.pid = '$aid' AND b.arid = '32'");
	  $lp_arr[0] = "ѡ��¥��";
	  while($r = $db->fetch_array($_lp_zt)){
	  	$lp_arr[$r['aid']]= $r['subject'];
	  }
	 return $lp_arr;	
}

function __replace($arr,$arr1,$result)
{
	return  str_replace($arr, $arr1, $result);
}
?>