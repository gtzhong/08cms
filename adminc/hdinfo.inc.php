<?php
!defined('M_COM') && exit('No Permission');
cls_cache::Load('cotypes,channels,grouptypes,vcps,currencys,permissions,catalogs');
if(!$aid = max(0,intval($aid))) cls_message::show('��ָ���ĵ���');
$arc = new cls_arcedit;
$arc->set_aid($aid,array('ch'=>1,'au'=>0,));
tabheader('������Ϣ');
cls_ArcMain::Url($arc->archive,-1);
$str = '';
for($i = 0;$i <= @$arc->channel['addnum'];$i ++) $str .= "><a href='".$arc->archive['arcurl'.($i ? $i : '')]."' target='_blank'>".($i ? "��$i" : "��ҳ")."</a> &nbsp;";
trbasic('ǰ̨ҳ��չʾ','',$str,'');
trbasic('�ĵ�����','',$arc->archive['subject'],'');
trbasic('��Ա����','',$arc->archive['mname'],'');
trbasic('���ʱ��','',date("Y-m-d H:i:s",$arc->archive['createdate']),'');
trbasic('����ʱ��','',date("Y-m-d H:i:s",$arc->archive['updatedate']),'');
trbasic('�ط���ʱ��','',date("Y-m-d H:i:s",$arc->archive['refreshdate']),'');
trbasic('����ʱ��','',$arc->archive['enddate'] ? date("Y-m-d H:i:s",$arc->archive['enddate']) : '-','');
trbasic('���״̬','',($arc->archive['checked'] ? '���': '����').'&nbsp;&nbsp;/&nbsp;&nbsp;'.($arc->archive['editor'] ? $arc->archive['editor'] : '-'),'');
trbasic('�����','',$arc->archive['clicks'],'');
tabfooter();
tabheader('������Ϣ');
trbasic('ģ��','',$arc->channel['cname'],'');
tabfooter();

?>