<?php
!defined('M_COM') && exit('No Permission');
foreach(array('cotypes','channels','grouptypes','vcps','currencys','permissions','catalogs',) as $k) $$k = cls_cache::Read($k);
if(!$aid = max(0,intval($aid))) cls_message::show('��ָ���ĵ���');
$arc = new cls_arcedit;
$arc->set_aid($aid,array('ch'=>1,'au'=>0,));
tabheader('������Ϣ');
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