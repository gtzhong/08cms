<?PHP
(!defined('M_COM') || !defined('M_ADMIN')) && exit('No Permission');
aheader();
backallow('normal') || cls_message::show('��û�е�ǰ��Ŀ�Ĺ���Ȩ�ޡ�');

$nowid = empty($nowid) ? 0 : $nowid;
$count = empty($count) ? 100 : $count;
$circum_km = empty($circum_km) ? 100 : $circum_km;

if(!submitcheck('bsubmit')){
	
	tabheader("��ʼ�� �ܱ����� �� ¥��/С�� ����",'newform',"?entry=extend&extend=peitaos_init&action=init",2,1,1);
	trbasic("ÿ��ִ������",'count',$count,'text',array('w'=>12,'guide'=>''));
	trbasic("�Զ���������",'circum',"{$circum_km} Km����ˣ�<a href='?entry=extend&extend=exconfigs&action=fccotype' target='_blank'>���ò���</a>",'');
	trbasic('��ʼ��˵��','',"1. ���������� �ܱ����׵�ͼ���� �� ¥��/С����ͼ���� λ�ã����������÷�Χ�ڣ��Զ�������
	<br>2. <span style='color:#F00'>����</span>���Գ�ʼ�����ݣ�ִ��һ�α�����������<span style='color:#F00'>�����ظ�ִ��</span>��
	<br>3. <span style='color:#F00'>����</span>�����ֶ�ά���� ¥��/С�� �ڵ� �ܱ����ϵģ��˲���<span style='color:#F00'>���ܻḲ��һЩ�ֶ�����</span>��
	<br> &nbsp; &nbsp; ���磬�ֶ�ȡ����ĳ���ܱ���¥�̹�����ִ�д˲������ֻỹԭ���������
	",'');
	tabfooter('bsubmit');
	
}else{//���ݴ���

	$timer = microtime(1);
	$sqla = "SELECT aid,dt FROM {$tblprefix}".atbl(4)." WHERE aid>'$nowid' ORDER BY aid LIMIT $count"; 
	$query = $db->query($sqla); $n = 0; 
	while($r = $db->fetch_array($query)){ 
		$_aid = $r['aid']; 
		$_dt = $r['dt'];
		$nowid = $_aid;
		ex_zhoubian($_aid, 4, $_dt, 1);
		$n++;
	} 
	$timer = microtime(1) - $timer;
	$timer = number_format($timer,3); 
	$msg = $n ? "���δ���{$n}��," : "�������,";
	$msg .= "\n��ʱ:{$timer}s��";
	$msg .= "<br>����ִ����ʼID: $nowid\n";
	cls_message::show($msg,"?entry=extend&extend=peitaos_init".($n ? "&action=init&nowid=$nowid&count=$count&bsubmit=1" : ""));

}
?>