<?php
# ȡ��֧������ģ�壬��ֻ֧���ĵ�����������
include_once dirname(dirname(__FILE__)).'/include/general.inc.php';
cls_env::CheckSiteClosed();

# ��ʼ���ĵ�
$aid = empty($aid) ? 0 : max(0,intval($aid));
if(!$aid) cls_message::show('��ָ����ȷ���ĵ���');
$arc = new cls_arcedit();
$arc->set_aid($aid,array('au'=>0,'ch'=>1,));
if(empty($arc->aid))  cls_message::show('��ָ����ȷ���ĵ�');
if(!$arc->archive['checked'])  cls_message::show('ָ�����ĵ�δ��'); 

# ��ʼ��ҳ�洫��
$tname = empty($tname) ? '' : trim($tname); # �����ֶ�����
$tmode = empty($tmode) ? false : true; # �Ƿ��ļ��ֶΣ�false-���ؼ��ֶΣ�true-���ļ��ֶ�
$fid = empty($fid) ? 0 : max(0,intval($fid)); # ָ�����ؼ��ڵĸ�����ţ����ļ��ɲ�ָ��

if(empty($arc->archive[$tname]))  cls_message::show('ָ���ĸ���������'); 
if(!cls_ArcMain::AllowDown($arc->archive))  cls_message::show('��û�е�ǰ�ĵ�������Ȩ��');

# ȡ�ø���url
$url = '';
if(empty($tmode)){ #���ؼ��ֶ�
	if($temp = @unserialize($arc->archive[$tname])){
		$url = @$temp[$temparr['fid']]['remote'];
	}
}else{ # ���ļ��ֶ�
	$temp = @explode('#',$arc->archive[$tname]);
	$url = @$temp[0];
}
if(empty($url)) cls_message::show('δ�ҵ�ָ���ĸ���');
$url = cls_url::tag2atm($url);

# ���ؿۻ��ִ���
if($crids = $arc->arc_crids(1)){//��Ҫ�Ե�ǰ�û���ֵ//�Զ���ֵ
	$currencys = cls_cache::Read('currencys');
	$cridstr = '';
	foreach($crids as $k => $v){
		$cridstr .= ($cridstr ? ',' : '').abs($v).$currencys[$k]['unit'].$currencys[$k]['cname'];
	}
	if(!$curuser->crids_enough($crids)){
		cls_message::show('���ش˸�����Ҫ֧������ : &nbsp;:&nbsp;'.$cridstr.'<br><br>��û�����ش˸�������Ҫ���㹻����!');
	}
	$curuser->updatecrids($crids,0,'���ظ���');
	$curuser->payrecord($arc->aid,1,$cridstr,1);
}

# ������ͳ��
save_downs($aid,$arc->archive['chid']);//ͳ��������
down_url($url);

function down_url($url){
	if(cls_url::islocal($url)){
		$url = cls_url::local_file($url);
		cls_atm::Down($url);
	}else{
		header("location:$url");
	}
	exit();
}
function save_downs($aid,$chid){//ͳ���ĵ���������
	global $db,$tblprefix,$statweekmonth;
	if(!$aid || !$chid) return;
	$f = 'down';
	$db->query("UPDATE {$tblprefix}".atbl($chid)." SET $f=$f+1".($statweekmonth ? ",w$f=w$f+1,m$f=m$f+1" : '')." WHERE aid=$aid");
}