<?PHP
/*
** ��������Ϣˢ������Ĵ��ڻ�����
** ��ִ�е�����������λ������
*/

(!defined('M_COM') || !defined('M_ADMIN')) && exit('No Permission');
aheader();
if($re = $curuser->NoBackFunc('normal')) cls_message::show($re);

if(!($paid = cls_PushArea::InitID(@$paid)))  cls_message::show('��ָ����ȷ������λ');

cls_pusher::ORefreshPaid($paid);

cls_message::show('�Ƽ�λ����������',"?entry=extend&extend=pushs&paid=$paid");
