<?PHP
/**
* �����̨�Ļ�Ա����ű�
* ����ϵͳ����Ҫ�������롢����
*/


/* ������ʼ������ */
$mid = empty($mid) ? 0 : max(0,intval($mid));
$_init = array(
	'mid' => $mid,//����һ����Ҫ����mid
);

#-----------------
$oA = new cls_member($_init);

$oA->TopHead();//�ļ�ͷ��

$oA->TopAllow();//��������Ȩ��

$actuser = &$oA->auser;

tabheader("[{$actuser->info['mname']}] ������Ϣ");
$cridsarr = cridsarr(1);
foreach($cridsarr as $k => $v){
	trbasic("$v ����",'',$actuser->info["currency$k"],'');
}
trbasic("��̬Ŀ¼",'',empty($actuser->info["mspacepath"]) ? 'δ���þ�̬Ŀ¼' : $actuser->info["mspacepath"],'');
$actuser->info['mchid'] != 1 && trbasic("�ռ�����",'',empty($actuser->info["msclicks"]) ? '0' : $actuser->info['msclicks'],'');//�ռ�����
$t_user = array();
if(!empty($actuser->info['trusteeship'])) {
    $db->select('mname')->from('#__members')->where('mid')->_in($actuser->info['trusteeship'])->exec();
    while($row = $db->fetch()) {
        $t_user[] = $row['mname'];
    }
} else {
    $t_user[] = 'δ���ô�����������';
}
trbasic("��Ա���ĵĴ��ܻ�Ա��",'', implode(', ', $t_user),'');
tabfooter();
