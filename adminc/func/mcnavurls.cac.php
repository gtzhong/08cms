<?php
foreach(array('chid','lpchid') as $v) $$v = $GLOBALS[$v];//��������Ҫ���͵ı�������Ҫ�ӽ���
$mcnavurls = array(
	'payonline' => array(
		'record' => array('֧����¼',"?action=pays"),
		'online' => array('����֧��',"?action=payonline"),
		'other' => array('����֧��',"?action=payother"),
	),
	'currency' => array(
		'record' => array('���ּ�¼',"?action=crrecords"),
		'exchange' => array('���ֶһ�',"?action=crexchange"),
	),
	'pm' => array(
		'box' => array('�����б�',"?action=pmbox"),
		'send' => array('���Ͷ���',"?action=pmsend"),
	),
	'loupanbar' => array(
		'loupan' => array('����¥��',"?action=loupans"),
		'zixun' => array('¥����Ѷ',"?action=zixuns"),	
		'xiangce' => array('¥�����',"?action=xiangces"),
		'huxing' => array('¥�̻���',"?action=huxings"),	
	),	
	'loupanbus' => array(
		'loupan' => array('����¥��',"?action=loupans&lpchid=$lpchid"),
		'zixun' => array('¥����Ѷ',"?action=zixuns&lpchid=$lpchid"),	
		'xiangce' => array('¥�����',"?action=xiangces&lpchid=$lpchid"),
		//'huxing' => array('¥�̻���',"?action=huxings"),	
	),	
	'tuijian' => array(
		'tjchid2' => array('����',"?action=tuijianarchives&chid=2"),
		'tjchid3' => array('����',"?action=tuijianarchives&chid=3"),		
	),
	'cuxuqiu' => array(
		'list9' => array('������Ϣ',"?action=xuqiugzs&chid=9"),
		'list10' => array('����Ϣ',"?action=xuqiugzs&chid=10"),
	),
	'chushou' => array(
		'manage' => array('ȫ�����ַ�Դ',"?action=chushouarchives&chid=3&valid=-1"),
		'shangjia' => array('���ϼܷ�Դ',"?action=chushouarchives&chid=3&valid=1"),
		'cangku' => array('���¼ܷ�Դ',"?action=chushouarchives&chid=3&valid=0"),
		'ershoufabu' => array('�������ַ�',"?action=chushouadd&chid=3&caid=3"),
		'maifang' => array('������',"?action=commu_yixiang&chid=3&valid=3"),		
	),
	'chuzu' => array(
		'manage' => array('ȫ�����ⷿԴ',"?action=chuzuarchives&chid=2&valid=-1"),
		'shangjia' => array('���ϼܷ�Դ',"?action=chuzuarchives&chid=2&valid=1"),		
		'cangku' => array('���¼ܷ�Դ',"?action=chuzuarchives&chid=2&valid=0"),
		'czfabu' => array('��������',"?action=chuzuadd&chid=2"),		
		'zufang' => array('�ⷿ����',"?action=commu_yixiang&chid=2&valid=2"),	
	),
    'bussell_office' => array(
        'manage' => array('ȫ��д��¥',"?action=bus_chushouarchives&chid=117&valid=-1"),
        'shangjia' => array('���ϼ�д��¥',"?action=bus_chushouarchives&chid=117&valid=1"),
        'cangku' => array('���¼�д��¥',"?action=bus_chushouarchives&chid=117&valid=0"),
        'ershoufabu' => array('����д��¥����',"?action=bus_chushouadd&chid=117&caid=613"),
        'maifang' => array('��д��¥����',"?action=commu_yixiang&chid=117&valid=3"),
    ),
    'busrent_office' => array(
        'manage' => array('ȫ������д��¥',"?action=bus_chuzuarchives&chid=119&valid=-1"),
        'shangjia' => array('���ϼܳ���д��¥',"?action=bus_chuzuarchives&chid=119&valid=1"),
        'cangku' => array('���¼ܳ���д��¥',"?action=bus_chuzuarchives&chid=119&valid=0"),
        'czfabu' => array('��������д��¥',"?action=bus_chuzuadd&chid=119&caid=614"),
        'zufang' => array('��д��¥����',"?action=commu_yixiang&chid=119&valid=2"),
    ),
    'bussell_shop' => array(
        'manage' => array('ȫ����������',"?action=bus_chushouarchives&chid=118&valid=-1"),
        'shangjia' => array('���ϼܳ�������',"?action=bus_chushouarchives&chid=118&valid=1"),
        'cangku' => array('���¼ܳ�������',"?action=bus_chushouarchives&chid=118&valid=0"),
        'ershoufabu' => array('������������',"?action=bus_chushouadd&chid=118&caid=617"),
        'maifang' => array('����������',"?action=commu_yixiang&chid=118&valid=3"),
    ),
    'busrent_shop' => array(
        'manage' => array('ȫ����������',"?action=bus_chuzuarchives&chid=120&valid=-1"),
        'shangjia' => array('���ϼܳ�������',"?action=bus_chuzuarchives&chid=120&valid=1"),
        'cangku' => array('���¼ܳ�������',"?action=bus_chuzuarchives&chid=120&valid=0"),
        'czfabu' => array('������������',"?action=bus_chuzuadd&chid=120&caid=618"),
        'zufang' => array('����������',"?action=commu_yixiang&chid=120&valid=2"),
    ),
    'company' => array(
		'manage' => array('�ҵľ��͹�˾',"?action=tocomp"),
		'cash' => array('��˾�ʽ�',"?action=zijing"),
	),
	'xuqiu' => array(
		'list9' => array('������Ϣ',"?action=xuqiuarchives&chid=9"),
		'list10' => array('����Ϣ',"?action=xuqiuarchives&chid=10"),		
		'qzadd' => array('��������',"?action=xuqiuarchive&chid=9"),
		'qgadd' => array('������',"?action=xuqiuarchive&chid=10"),	
	),
	'zhaopin' => array(
		'manage' => array('ȫ����Ƹ��Ϣ',"?action=zhaopinarchives"),
		'fubuzp' => array('������Ƹ',"?action=zhaopinadd")
	),
	'kuaiwen' => array(
		'qget' => array('���ҵ�����',"?action=wenda_manage&actext=qget"),
		'qout' => array('�ҵ�����',"?action=wenda_manage&actext=qout"),		
		'answer' => array('�ҵĻش�',"?action=wenda_manage&actext=answer")	
	),
	'designNews' => array(
		'manage' => array('�����б�',"?action=designNews_s"),
		'add' => array('��ӹ�˾��̬',"?action=designNews_a")
	),
	'agents' => array(
		'incheck1' => array('��ʽ������',"?action=agents&incheck=1"),
		'incheck0' => array('���󾭼���',"?action=agents")
	),
	'design' => array(
		'manage' => array('���ʦ�б�',"?action=design_s"),
		'add' => array('���ʦ���',"?action=design_a&chid=101&caid=510")
	),
	'designCase' => array(
		'manage' => array('�����б�',"?action=designCase_s"),
		'nocheck' => array('�������',"?action=designCase_a&chid=$chid&caid=511&pid31=-1")
	),
	'designGoods' => array(
		'manage' => array('�����б�',"?action=designGoods_s"),
		'add' => array('�����Ʒ',"?action=designGoods_a&chid=103&caid=513")
	),
	'sms_member' => array(
		'sendlog'   => array('���ͼ�¼',  "?action=sms_member&section=sendlog"),
		'balance'   => array('������ֵ',"?action=sms_member&section=balance"),
		'chargelog' => array('��ֵ��¼',  "?action=sms_member&section=chargelog"),
		'sendsms'   => array('���ŷ���',  "?action=sms_member&section=sendsms"),
	),
	'account' => array(
		'pwd' => array('�޸�����',"?action=memberpwd"),
		'bind' => array('�ʺŰ�',"?action=memberbind"),
	),	
	'scangs' => array(		
		'ch3' => array('���۷�Դ',"?action=scangs&chid=3"),
		'ch2' => array('���ⷿԴ',"?action=scangs&chid=2"),
	),
	'scxuqiu' => array(		
		'ch9' => array('��ע����',"?action=scangs&chid=9"),
		'ch10' => array('��ע��',"?action=scangs&chid=10"),
	),
	'scshye' => array( //��ҵ�ز�		
		'ch115' => array('д��¥¥��',"?action=scangs&chid=115"),
		'ch116' => array('����¥��',"?action=scangs&chid=116"),
		'ch117' => array('д��¥����',"?action=scangs&chid=117"),
		'ch118' => array('���̳���',"?action=scangs&chid=118"),
		'ch119' => array('д��¥����',"?action=scangs&chid=119"),
		'ch120' => array('���̳���',"?action=scangs&chid=120"),
	),
	'tejia' => array(		
		'manage' => array('�ؼ۷�����',"?action=tejiaarchives"),
		'add' => array('����ؼ۷�',"?action=tejiaarchive"),
	),
	'weituo' => array(		
		'chushou' => array('���۷�Դί�й���',"?action=delegations&chid=3"),
		'chuzu' => array('���ⷿԴί�й���',"?action=delegations&chid=2"),
	),
	'yongjin' => array(		
		'yjgets' => array('��ȡӶ��',"?action=fxmy_brokerage&part=yjgets"),
		'yjlist' => array('��ȡ��¼',"?action=fxmy_brokerage&part=yjlist"),
	),
);
?>
