<?PHP
/*
** ����������Ϣ����������Ĵ��ڲ���
** 
*/
(!defined('M_COM') || !defined('M_ADMIN')) && exit('No Permission');
aheader();
if($re = $curuser->NoBackFunc('normal')) cls_message::show($re);

$paid = cls_PushArea::InitID(@$paid);//�����ⲿ��paid
if(!($pusharea = cls_PushArea::Config($paid))) cls_message::show('��ָ����ȷ������λ');
if(empty($pusharea['copyspace'])) cls_message::show('δָ���������');
$pushid = empty($pushid) ? 0 : max(0,intval($pushid));//�����ⲿ��pushid
if(!($push = cls_pusher::oneinfo($pushid,$paid))) cls_message::show('��ָ����ȷ��������Ϣ');

$copyspace = "classid{$pusharea['copyspace']}";
$field = cls_PushArea::Field($paid,$copyspace);
$classes = cls_field::options_simple($field,array('onlysel' => 1));
$copyinfos = cls_pusher::copyinfos($push,$paid);

if(!submitcheck('bsubmit')){
	tabheader("[{$push['subject']}]��������",'pushshare',"?entry=extend&extend=$extend&paid=$paid&pushid=$pushid",2,0,1);
	trbasic('Ŀǰ����״̬','',$copyinfos ? "�ѹ�����".count($copyinfos)."������" : 'δ������������','');
//	trbasic('����ģʽ','',makeradio('fmdata[isclear]',array('���ӹ���','�Ƴ�����'),0),'');
	if($classes){
		$classidshared = array();
		if($copyinfos){
			foreach($copyinfos as $k => $v) in_array($v[$copyspace],$classidshared) || $classidshared[] = $v[$copyspace];
		}
		$modearr = array(0 => '���������з���',2 => 'ȡ�����з���Ĺ���',1 => '�ֶ�ָ���������',);
		sourcemodule("��ѡ��[{$field['cname']}]����",
			"fmdata[mode]",
			$modearr,
			$copyinfos ? 1 : 0,
			1,
			"fmdata[ids]",
			$classes,
			$classidshared,
			'25%',1,'',1
		);
	}
	tabfooter('bsubmit');
}else{
	if(isset($fmdata['mode'])){
		switch($fmdata['mode']){
			case 0:
				foreach($classes as $k => $v){
					cls_pusher::AddCopy($pushid,$k,$paid);
				}
			break;
			case 1:
				$selectid = empty($fmdata['ids']) ? array() : array_filter(explode(',',$fmdata['ids']));
				foreach($classes as $k => $v){
					if(in_array($k,$selectid)){
						cls_pusher::AddCopy($pushid,$k,$paid);
					}else{
						cls_pusher::DelCopy($pushid,$k,$paid);
					}
				}
			break;
			case 2:
				foreach($classes as $k => $v){
					cls_pusher::DelCopy($pushid,$k,$paid);
				}
			break;
		}
	}
	cls_message::show('���������ɡ�',axaction(6,"?entry=extend&extend=pushs&paid=$paid"));
}
?>