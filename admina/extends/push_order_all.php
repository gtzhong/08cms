<?PHP
/*
** ��������Ϣˢ������Ĵ��ڻ�����
** ��ִ�е�����������λ������
*/

(!defined('M_COM') || !defined('M_ADMIN')) && exit('No Permission');
aheader();
if($re = $curuser->NoBackFunc('normal')) cls_message::show($re);
foreach(array('pushtypes','pushareas',) as $k) $$k = cls_cache::Read($k);

$paidsarr = array();
foreach($pushtypes as $k => $v){
	$paidsarr[$k] = array('title' => $v['title']);
	foreach($pushareas as $x => $y){
		if($y['ptid'] == $k){
			$paidsarr[$k]['arr'][$x] = $y['cname'];
		}
	}
	if(empty($paidsarr[$k]['arr'])) unset($paidsarr[$k]);
}
if(empty($paidsarr)) cls_message::show('ϵͳ��δ��������λ��');	

if(!submitcheck('bsubmit')){
	tabheader('����������λˢ������&nbsp; <input class="checkbox" type="checkbox" name="mchkall" onclick="checkall(this.form,\'paidsnew\',\'mchkall\')">ȫѡ','amconfigdetail',"?entry=$entry&extend=$extend",2);
	foreach($paidsarr as $k => $v){
		trbasic($v['title']."<br/><input type='checkbox' id='chooseall".$k."' class='checkbox' onclick='chooseall(this)'>ȫѡ<br/>",'',makecheckbox("paidsnew[]",$v['arr'],array_keys($v['arr']),5),'');
	}
	tabfooter('bsubmit');
}else{
	if(empty($paidsnew)){
		cls_message::show('��ѡ�������λȫ��������ϡ�',"?entry=$entry&extend=$extend");
	}elseif(!is_array($paidsnew)){
		$paidsnew = explode(',',$paidsnew);
	}
	$paidsnew = array_filter($paidsnew);
	
	if($paid = array_shift($paidsnew)){
		cls_pusher::ORefreshPaid($paid);
	}
	
	if(empty($paidsnew)){
		cls_message::show('��ѡ�������λȫ��������ϡ�',"?entry=$entry&extend=$extend");
	}else{
		$num = count($paidsnew);
		$paidsnew = implode(',',$paidsnew);
		cls_message::show("���� <b>{$num}</b> ������λ��Ҫ���������ĵȴ���","?entry=$entry&extend=$extend&paidsnew=$paidsnew&bsubmit=1");
	}
}
