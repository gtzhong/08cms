<?php
!defined('M_COM') && exit('No Permission');

$fxpid = $curuser->info['fxpid'];
if($fxpid){
	$puser = new cls_userinfo;
	$puser->activeuser($fxpid,1);
	cls_message::show("�����ϼ���Ա�ǣ�{$puser->info['mname']}<br>��û���ƹ����ӹ��ܣ�");	
}else{
		
}
$exconfigs = cls_cache::cacRead('exconfigs',_08_EXTEND_SYSCACHE_PATH);
$defwords = $exconfigs['distribution']['fxwords'];
$copybtn = "<div style='text-align:center;'><input type='button' value='��������' onClick='setCopy()'><script>function setCopy(){ \$id('fxlinks').select(); alert('����[Crtl+C]���ƣ�'); }</script></div>";
$sharebar = '<div id="bdshare" class="bdshare_t bds_tools get-codes-bdshare" style="line-height:12px;padding-right:220px; float:right"><a style="padding-top:2px;margin-top:5px;padding-bottom:0"> ������</a> <a class="bds_qzone"></a> <a class="bds_tsina"></a> <a class="bds_tqq"></a> <a class="bds_renren"></a> <a class="bds_t163"></a> <span class="bds_more" >����</span> </div>
	<script type="text/javascript" id="bdshare_js" data="type=tools&amp;uid=18486" ></script>
	<script type="text/javascript" id="bdshell_js"></script>
	<script type="text/javascript">
	var bds_config = {"url":"'."{$cms_abs }register.php".'", "text":"'.$defwords.'"};
	document.getElementById("bdshell_js").src = "http://bdimg.share.baidu.com/static/js/shell_v2.js?cdnversion=" + Math.ceil(new Date()/3600000)
	</script>
';
$fullbar = "<div>$copybtn</div>"; //$sharebar

if(!submitcheck('bsubmit')){
	tabheader("�ҵ�ר������",'mspacestatic','',2,0,1); //?action=static
	echo '<tr><td width="150px" class="item1"><b>�ƹ�˵��</b></td><td class="item2">';m_guide('house_fxlink_note'); echo '</td></tr>';

	trbasic('�ƹ�ں�','fxwords',$defwords,'textarea', array('w' => 400,'h' => 50,'validate' => makesubmitstr('fxwords',1,0,0,100)));
	trbasic('�ƹ�����','fxlinks',"{$cms_abs }register.php?fxpid={$curuser->info['mid']}",'text', array('guide' => '','validate' => makesubmitstr('fxlinks',1,0,0,100).' readonly','w'=>75));
	trbasic('','',$fullbar,'', array('guide' => '','validate' => makesubmitstr('fxlinks',1,0,0,100).' readonly','w'=>75));

	tabfooter(''); //bsubmit
	m_guide('house_fxlink_rule','fix');

}else{ //���������ʽ...
	/*
	updatecache('mspacepaths');
	mcmessage('��Ա��̬�ռ����óɹ�',M_REFERER);
	*/
}

?>
