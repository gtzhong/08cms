<?php
/*
* ��Ա����ר�ú��������ĺ���
* ��չϵͳר�û������ʽ�ĺ�����������뵽 MC_ROOTDIR."func/main.php"
*/
!defined('M_COM') && exit('No Permission');
define('MC_DIR',empty($mc_dir) ? 'adminm' : $mc_dir);
define('MC_ROOTDIR',M_ROOT.MC_DIR.'/');
define('MC_ROOTURL',$cms_abs.MC_DIR.'/');
include_once M_ROOT."include/admin.fun.php";
include_once MC_ROOTDIR."func/main.php";
//��Ա���ĵĺ�����ڣ������Ժ���������ģ�干�õĺ��������漰���ĺ���������

#վ����Ϣͳ��
function pmstat(){
	static $stat = array();
	global $db, $tblprefix, $memberid;
	if(!$stat){
		$row = $db->fetch_one("SELECT COUNT(pmid) AS pms,SUM(viewed) AS views FROM {$tblprefix}pms WHERE toid='$memberid'");
		$stat[0] = $row['pms'] - $row['views'];
		$stat[1] = $row['pms'];
	}
	return $stat;
}
// ��Ա����-��ʾ��ʾ
// key��ʾID,����ʾ�ı�����"
// type: ��ʾģʽ;
//     Ĭ�Ͽ�-ֱ����ʾ����;
//     tip-�����ص���ʾ��;
//     fix-�̶�����ʾ��
// ����ռλ������,��:{$cms_version},��ȷ��ȫ�����ж����
function m_guide($key,$type=''){ //$mguide
	if(empty($key)) return;
	if(preg_match("/^[a-zA-Z][a-z_A-Z0-9]{2,31}$/",$key)){ // ��̨�ɹ����ע��
		$file = M_ROOT.'dynamic/mguides/'.$key.'.php';
		if(is_file($file)){
			include $file;
			if(empty($mguide)) return;
			$msg = "<!--$key-->$mguide";
		}else{
			if(!_08_DEBUGTAG) return;
			$msg = "<span style='color:#F0F;'>������ʾ! </span>����ϵ����Ա, ���� [�����̨&gt;&gt;ϵͳ����&gt;&gt;��Ա����&gt;&gt;��Ա����ע��] ��, ���IDΪ [{$key}] ��ע��";
		}
	}else{ // ��ͨ�ı�,����һ��Ψһkeyֵ,����tip״̬����ʾ
		$msg = $key;
		$key = md5($key);
	}
	if($type=='fix'){ // �̶�����ʾ��
    	$msg = "<div id='tipm_ptop_bot' class='tipm_botmsg tipm_tclose_out'>$msg</div>";
	}elseif($type=='tip'){ // �����ص���ʾ��
		$msg = "<div id='tipm_ptop_lamp_$key' class='tipm_topen_out'>
		<div class='tipm_topen' onclick='ftip_open(\"$key\")'>&nbsp;</div></div>
		<div id='tipm_ptop_msg_$key' class='tipm_tclose_out'>
		<div class='tipm_tclose' onclick='ftip_close(\"$key\")'>&nbsp;</div>$msg</div>
		<script type='text/javascript'>ftip_inti('$key');</script>";
	}
	// ����-ռλ������(Ҫ֧������ο�#(\[[a-z_A-Z0-9]{1,32}\])*#)
	$msg = str_replace('{$','{',$msg);
	$msg = key_replace($msg,array(array()));
	echo $msg;
}
