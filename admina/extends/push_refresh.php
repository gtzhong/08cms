<?PHP
/*
** ����ͬ����Դ
** ��ִ�е�����������λ������
*/

(!defined('M_COM') || !defined('M_ADMIN')) && exit('No Permission');
aheader();
if($re = $curuser->NoBackFunc('normal')) cls_message::show($re);
if(!($paid = cls_PushArea::InitID(@$paid)))  cls_message::show('��ָ����ȷ������λ');

$num = cls_pusher::RefreshPaid($paid);

cls_message::show("����Դ����ͬ����{$num}����Ч��Ϣ","?entry=extend&extend=pushs&paid=$paid");
