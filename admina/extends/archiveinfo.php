<?php
(!defined('M_COM') || !defined('M_ADMIN')) && exit('No Permission');
aheader();
foreach(array('cotypes','channels','grouptypes','vcps','currencys','permissions','catalogs','mtpls',) as $k) $$k = cls_cache::Read($k);
if(!$aid = max(0,intval(@$aid))) cls_message::show('��ָ���ĵ���');
$arc = new cls_arcedit;
$arc->set_aid($aid,array('ch'=>1,'au'=>0,));
tabheader('������Ϣ - '.$arc->archive['subject']);

//�������������¼�����������ﲻ���ʣ�����չ�ɣ���
$curltype = empty($curltype) ? '0' : $curltype; // 0:һ���ĵ�����(Ĭ��), skip:��Ҫ����, m:��Ա��������
$exmobile = empty($exmobile) ? '0' : $exmobile; // 1:Ҫ�ֻ�������, 0:����ʾ�ֻ�������(Ĭ��) 

if($curltype!='skip'){
	cls_ArcMain::Url($arc->archive,-1);
	$str = '';
	for($i = 0;$i <= @$arc->arc_tpl['addnum'];$i ++) $str .= "&gt;<a href='".$arc->archive['arcurl'.($i ? $i : '')]."' target='_blank'>".($i ? "��$i" : "��ҳ")."</a> &nbsp;";
	trbasic('ǰ̨ҳ��Ԥ��','',$curltype=='m' ? "&gt;<a href='".$arc->archive['marcurl']."' target='_blank'>����</a>" : $str,'');
}

if(!empty($exmobile)){
	$arc->ChangeNodeMode(1);
	cls_ArcMain::Url($arc->archive,-1);
	$str = '';
	for($i = 0;$i <= @$arc->arc_tpl['addnum'];$i ++) $str .= "&gt;<a href='".$arc->archive['arcurl'.($i ? $i : '')]."' target='_blank'>".($i ? "��$i" : "��ҳ")."</a> &nbsp;";
	trbasic('�ֻ���Ԥ��','',$str,'');
}

trbasic('�ĵ�ģ��','',$arc->channel['cname'],'');
trbasic('����/ID','',$arc->archive['mname']." &nbsp;/ &nbsp;{$arc->archive['mid']}",'');
trbasic('���ʱ��','',date("Y-m-d H:i:s",$arc->archive['createdate']),'');
trbasic('����ʱ��','',date("Y-m-d H:i:s",$arc->archive['updatedate']),'');
trbasic('ˢ��ʱ��','',date("Y-m-d H:i:s",$arc->archive['refreshdate']),'');
trbasic('����ʱ��','',$arc->archive['enddate'] ? date("Y-m-d H:i:s",$arc->archive['enddate']) : '-','');
trbasic('���/�༭','',($arc->archive['checked'] ? '���': 'δ��').' &nbsp;/ &nbsp;'.($arc->archive['editor'] ? $arc->archive['editor'] : '-'),'');
trbasic('�����','',$arc->archive['clicks'],'');
tabfooter();

?>
