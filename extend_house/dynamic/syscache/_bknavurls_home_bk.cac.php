<?php
	foreach(array('mtcid','gsid','chid','fcaid','aid','cid') as $v) $$v = $GLOBALS[$v];
	$bknavurls = array(
		'house' => array(
			'title' => '��������',		   
			'menus' => array(
				'gaoji' => array('����������',"?entry=extend&extend=exconfigs&action=gaoji"),
				'vipgs' => array('װ��������',"?entry=extend&extend=exconfigs&action=vipgs"),
				'vipsj' => array('Ʒ��������',"?entry=extend&extend=exconfigs&action=vipsj"),
				'upmemberhelp' => array('����˵��',"?entry=extend&extend=exconfigs&action=upmemberhelp"),
				'gssendrules' => array('װ���̷���',"?entry=extend&extend=exconfigs&action=gssendrules"),
				'sjsendrules' => array('Ʒ���̷���',"?entry=extend&extend=exconfigs&action=sjsendrules"),				
				'fanyuan' => array('��Դ����',"?entry=extend&extend=exconfigs&action=fanyuan"),
				'shangye' => array('��ҵ�ز�����',"?entry=extend&extend=exconfigs&action=shangye"),
				'zding' => array('��Դ�ö�',"?entry=extend&extend=exconfigs&action=zding"),
				'weituo' => array('ί�з�Դ',"?entry=extend&extend=exconfigs&action=weituo"),
				'yysx' => array('ԤԼˢ��',"?entry=extend&extend=exconfigs&action=yysx"),
				'distribution' => array('¥�̷���',"?entry=extend&extend=exconfigs&action=distribution"),
				'closemod' => array('��ѡģ��',"?entry=extend&extend=exconfigs&action=closemod"),
				'fccotype' => array('��������',"?entry=extend&extend=exconfigs&action=fccotype"),
			),
		),
		'mobile' => array(
			'title' => '�ֻ���',
			'menus' => array(
				'system'   => array('ϵͳ����',"?entry=o_tplconfig&action=system"),
				'archive'   => array('�ĵ�ģ��',  "?entry=o_tplconfig&action=tplchannel"),
				'cnodes' => array('�ڵ����',"?entry=o_cnodes&action=cnodescommon"),
				'cnconfigs' => array('�ڵ㷽��',"?entry=o_cnodes&action=cnconfigs"),
				'cntpls' => array('�ڵ�����',"?entry=o_cnodes&action=cntplsedit"),
				'farchive' => array('����ģ��',"?entry=o_tplconfig&action=tplfcatalog"),
				'mtpls'   => array('�ֻ�ģ���',"?entry=o_mtpls&action=mtplsedit"),
			),
		),
		'sms_admin' => array(
		'title' => '�ֻ�����',
		'menus' => array(
			'sendlog'   => array('���ͼ�¼',  "?entry=sms_admin&action=sendlog"),
			'balance'   => array('������ֵ',"?entry=sms_admin&action=balance"),
			'chargelog' => array('��ֵ��¼',  "?entry=sms_admin&action=chargelog"),
			'sendsms'   => array('���ŷ���',  "?entry=sms_admin&action=sendsms"),
			'setapi'    => array('�ӿ�����',  "?entry=sms_admin&action=setapi"), //?entry=mconfigs&action=cfmobmail
            'enable'   => array('ģ������',"?entry=sms_admin&action=enable"),
			'apiwarn'   => array('ͳ���뱨��',"?entry=sms_admin&action=apiwarn"),
		),
		),		
		'tpl' => array(
			'title' => 'ģ������',
			'menus' => array(
				'base' => array('��������',"?entry=tplconfig&action=tplbase"),
				'tplfield' => array('ģ�����',"?entry=tplconfig&action=tplfield"),
				'retpl' => array('����ģ���',"?entry=mtpls&action=mtplsedit"),
				'cssjs' => array('CSS/JS�ļ�����',"?entry=csstpls"),
			),
		),
		'bindtpl' => array(
			'title' => 'ģ���',
			'menus' => array(
				'system' => array('ϵͳģ��',"?entry=tplconfig&action=system"),
				//'exhouse' => array('������չ',"?entry=tplcfgex&action=exhouse"),
				'channel' => array('�ĵ�����ҳ',"?entry=tplconfig&action=tplchannel"),
				'mchannel' => array('��Աģ��',"?entry=tplconfig&action=tplmchannel",1),
				'fcatalog' => array('��������ҳ',"?entry=tplconfig&action=tplfcatalog",1),
				'freeinfos' => array('����ҳ��',"?entry=freeinfos&action=freeinfosedit"),
				'cnodes' => array('>��Ŀ�ڵ�',"?entry=cnodes&action=cnodescommon"),
				'mcnodes' => array('>��ԱƵ���ڵ�',"?entry=mcnodes&action=mcnodesedit",1),
			),
		),
		'othertpl' => array(
			'title' => 'ģ�����',		   
			'menus' => array(
				'tcah' => array('�ؽ�ģ�建��',"?entry=tplcache"),
				'cssjs' => array('CSS��JS����',"?entry=csstpls"),
				'db' => array('�ⲿ����Դ',"?entry=dbsources&action=dbsourcesedit"),
			),
		),
		'catalog' => array(
			'title' => '��Ŀ����',
			'menus' => array(
				'admin' => array('��Ŀ����',"?entry=catalogs&action=catalogedit"),
				'adds' => array('�������',"?entry=catalogs&action=catalogadds"),
				'fields' => array('��Ŀ�ֶ�',"?entry=catalogs&action=cafieldsedit"),
				'mconfigs' => array('��Ŀ����',"?entry=catalogs&action=mconfigs"),
			),
		),		
		'cata' => array(
			'title' => '��Ŀ����',		   
			'menus' => array(
				'cotype' => array('��ϵ����',"?entry=cotypes&action=cotypesedit"),
				'cnrel' => array('��Ŀ��������',"?entry=cnrels&action=cnrelsedit"),
			),
		),
		'faces' => array(
			'title' => '�������',		   
			'menus' => array(
				'face' => array('�������',"?entry=faces"),
				'update' => array('�����±���',"?entry=faces&action=update"),
			),
		),
		'project' => array(
			'title' => '��վ����',		   
			'menus' => array(
				'pm' => array('Ȩ�޷���',"?entry=permissions&action=permissionsedit"),
				'localfile' => array('�ϴ�����',"?entry=localfiles&action=localfilesedit"),
				'rproject' => array('Զ������',"?entry=rprojects&action=rprojectedit"),
				'player' => array('������',"?entry=players&action=playersedit"),
				'watermark' => array('ˮӡ����',"?entry=watermark&action=watermarkedit"),
				'pagecache' => array('ҳ�滺��',"?entry=pagecaches"),
			),
		),
		'mtdetail' => array(
			'title' => '�ռ�ģ��',		   
			'menus' => array(
				'base' => array('��������',"?entry=mtconfigs&action=mtconfigdetail&mtcid=$mtcid"),
				'tpl' => array('����ģ��',"?entry=mtconfigs&action=mtconfigtpl&mtcid=$mtcid"),
			),
		),
		'backarea' => array(
			'title' => '�����̨����',
			'menus' => array(
				'bkparam' => array('��̨����',"?entry=backparams&action=bkparams"),
				'amember' => array('��̨����Ա',"?entry=amembers&action=edit"),
				'm' => array('��̨�˵�',"?entry=menus&action=menusedit"),
				'config' => array('�����ɫ',"?entry=amconfigs&action=amconfigsedit"),
				'caedit' => array('��̨�ڵ���',"?entry=amconfigs&action=amconfigcaedit"),
				'ausual' => array('��������',"?entry=usualurls&action=usualurlsedit"),
			),
		),		
		'mcenter' => array(
			'title' => '��Ա��������',		   
			'menus' => array(
				'mcparam' => array('��Ա���Ĳ���',"?entry=backparams&action=mcparams"),
				'c' => array('�˵�����',"?entry=mmenus&action=mmenusedit"),
				'musual' => array('��������',"?entry=usualurls&action=usualurlsedit&ismc=1"),
				'mguides' => array('��Ա����ע��',"?entry=mguides"),
			),
		),
		'bannedip' => array(
			'title' => '��ֹIP',		   
			'menus' => array(
				'ip' => array('��ֹIP',"?entry=bannedips"),
				'cfg' => array('���ʼ�¼',"?entry=bannedips&action=visitors"),
			),
		),
		'btags' => array(
			'title' => 'ԭʼ��ʶ',		   
			'menus' => array(
				'btag' => array('ԭʼ��ʶ�б�',"?entry=btags"),
				'search' => array('����ԭʼ��ʶ',"?entry=btagsearch"),
			),
		),
		'channel' => array(
			'title' => '�ĵ�ģ��',		   
			'menus' => array(
				'channel' => array('�ĵ�ģ�͹���',"?entry=channels&action=channeledit"),
				'dbsplit' => array('�ĵ��������',"?entry=splitbls"),
			),
		),
		'cnode' => array(
			'title' => '��Ŀ�ڵ�',		   
			'menus' => array(
				'cnodescommon' => array('��Ŀ�ڵ����',"?entry=cnodes&action=cnodescommon"),
				'cnconfigs' => array('�ڵ���ɷ���',"?entry=cnodes&action=cnconfigs"),
				'cntpls' => array('�ڵ����ù���',"?entry=cnodes&action=cntplsedit"),
				'tpl' => array('>����ģ���',"?entry=tplconfig&action=system"),
			),
		),
		'mcnode' => array(
			'title' => '��Ա�ڵ�',		   
			'menus' => array(
				'mcnodesedit' => array('��ԱƵ���ڵ�',"?entry=mcnodes&action=mcnodesedit"),
				'mcnodeadd' => array('��ӻ�Ա�ڵ�',"?entry=mcnodes&action=mcnodeadd"),
				'cntpls' => array('�ڵ����ù���',"?entry=mcnodes&action=cntplsedit"),
				'tpl' => array('>����ģ���',"?entry=tplconfig&action=system"),
			),
		),
		'currency' => array(
			'title' => '��վ����',		   
			'menus' => array(
				'type' => array('��������',"?entry=currencys&action=currencysedit"),
				'project' => array('���ֻ��ҷ���',"?entry=currencys&action=crprojects"),
				'price' => array('���ּ۸񷽰�',"?entry=currencys&action=crprices"),
			),
		),
		'cysave' => array(
			'title' => '����&�ֽ�',
			'menus' => array(
				'pays' => array('��Ա֧������',"?entry=pays&action=paysedit"),
				'record' => array('����Ա��ۻ���',"?entry=currencys&action=cradminlogs"),
				'currency' => array('���ֱ����¼',"?entry=currencys&action=crlogs"),
			),
		),		
		'data' => array(
			'title' => '���ݿ����',		   
			'menus' => array(
				'dbbackup' => array('���ݿⱸ��',"?entry=database&action=dbexport"),
				'dbimport' => array('�������ݿⱸ��',"?entry=database&action=dbimport"),
				'dboptimize' => array('�Ż����޸�',"?entry=database&action=dboptimize"),
				'dbsql' => array('ִ��SQL',"?entry=database&action=dbsql"),
				'dbsource' => array('�ⲿ����Դ',"?entry=dbsources&action=dbsourcesedit"),
				'dbdict' => array('���ݿ�ʵ�',"?entry=dbdict"),
				'dbdebug' => array('SQL��Ϸ���',"?entry=dbdebug"),
				'dbstkeys' => array('�����Ա�',"?entry=dbstkeys&action=compare"),
			),
		),
		'fchannel' => array(
			'title' => '�����ܹ�',		   
			'menus' => array(
				'coclass' => array('��������',"?entry=fcatalogs&action=fcatalogsedit"),
				'channel' => array('����ģ��',"?entry=fchannels&action=fchannelsedit"),
			),
		),
		'adv' => array(
			'title' => '���λ����',
			'menus' => array(
				'adv_tpl' => array('ģ��',"?entry=extend&extend=adv_management&action=adv_tpl&src_type=other&fcaid=$fcaid"),
				'view' => array('Ԥ��',"?entry=extend&extend=adv_management&action=view&fcaid=$fcaid"),
			),
		),
		'fragment' => array(
			'title' => '��Ƭ����',		   
			'menus' => array(
				'fragment' => array('��Ƭ����',"?entry=fragments&action=fragmentsedit"),
				'catalog' => array('��Ƭ����',"?entry=frcatalogs&action=frcatalogsedit"),
			),
		),
		'gmiss' => array(
			'title' => '�ɼ�����',		   
			'menus' => array(
				'admin' => array('�ɼ��������',"?entry=gmissions&action=gmissionsedit"),
				'model' => array('�ɼ�ģ�͹���',"?entry=gmodels&action=gmodeledit"),
			),
		),
		'grule' => array(
			'title' => '�ɼ�����',		   
			'menus' => array(
				'netsite' => array('��ַ�ɼ�',"?entry=gmissions&action=gmissionurls&gsid=$gsid"),
				'content' => array('���ݲɼ�',"?entry=gmissions&action=gmissionfields&gsid=$gsid"),
				'output' => array('�������',"?entry=gmissions&action=gmissionoutput&gsid=$gsid"),
				'test' => array('���Թ���',"?entry=gmissions&action=urlstest&gsid=$gsid"),
			),
		),
		'channelex' => array(
			'title' => '�߼�����',		   
			'menus' => array(
				'search' => array('����ѡ��',"?entry=channels&action=channeladv&chid=$chid&deal=search"),
				'other' => array('������չ',"?entry=channels&action=channeladv&chid=$chid&deal=other"),
				//'group' => array('�ֶη���',"?entry=channels&action=channeladv&chid=$chid&deal=group"),
				//'region' => array('�б�����',"?entry=channels&action=channeladv&chid=$chid&deal=region"),
			),
		),
		'exconfig' => array(
			'title' => '��չ�ܹ�',		   
			'menus' => array(
				'commu' => array('������Ŀ����',"?entry=commus&action=commusedit"),
				'abrel' => array('�ϼ���Ŀ����',"?entry=abrels&action=abrelsedit"),
			),
		),
		'otherset' => array(
			'title' => '��������',		   
			'menus' => array(
				'misc' => array('�ƻ�����',"?entry=misc&action=cronedit"),
				'domain' => array('��������',"?entry=domains"),
				'email' => array('�ʼ�ģ��',"?entry=splangs&action=splangsedit"),
			),
		),
		'mchannel' => array(
			'title' => '��Աģ��',		   
			'menus' => array(
				'grouptype' => array('��Ա��ϵ����',"?entry=grouptypes&action=grouptypesedit"),
				'channel' => array('��Աģ�͹���',"?entry=mchannels&action=mchannelsedit"),
				'field' => array('��Աͨ���ֶ�',"?entry=mchannels&action=initmfieldsedit"),
				'mctype' => array('��Ա��֤����',"?entry=mctypes&action=mctypesedit"),
			),
		),
		'mconfig' => array(
			'title' => '��վ����',		   
			'menus' => array(
				'cfsite' => array('վ������',"?entry=mconfigs&action=cfsite"),
				'cfvisit' => array('����ע��',"?entry=mconfigs&action=cfvisit",1),
				'cfview' => array('ҳ������',"?entry=mconfigs&action=cfview"),
				'cfppt' => array('ͨ��֤',"?entry=mconfigs&action=cfppt",1),
				'cfpay' => array('��������',"?entry=mconfigs&action=cfpay",1),
				'cfupload' => array('��������',"?entry=mconfigs&action=cfupload",1),
				'cfmobmail' => array('�����400�绰',"?entry=mconfigs&action=cfmobmail",1),
				'other_site_connect' => array('��ݵ�½����',"?entry=mconfigs&action=other_site_connect",1),				
			),
		),
		'pms' => array(
			'title' => 'վ�ڶ���',		   
			'menus' => array(
				'manage' => array('���Ź���',"?entry=pms&action=pmsmanage"),
				'batch' => array('���͹���',"?entry=pms&action=batchpms"),
				'clear' => array('�������',"?entry=pms&action=clearpms"),			
			),
		),
		'record' => array(
			'title' => 'վ����־',		   
			'menus' => array(
				'bad' => array('��¼������־',"?entry=records&action=badlogin"),
				'admin' => array('���������־',"?entry=records&action=adminlog"),
			),
		),
		'static' => array(
			'title' => 'ҳ�澲̬',
			'menus' => array(
				'index' => array('��ҳ��̬',"?entry=static&action=index"),
				'cnodes' => array('��Ŀҳ��̬',"?entry=static&action=cnodes"),
				'archives' => array('����ҳ��̬',"?entry=static&action=archives"),
				'mcnodes' => array('��ԱƵ����̬',"?entry=static&action=mcnodes",1),
				'freeinfos' => array('����ҳ��̬',"?entry=freeinfos&action=static"),
				'cfstatic' => array('��̬��������',"?entry=static&action=cfstatic"),
				'statichelp' => array('<span style="color:#00F;font-weight:normal">[��̬����]</span>',"tools/taghelp.html#p_jtscsm\" target='_blank'"),
			),
		),
			'usualtags' => array(
			'title' => '���ñ�ʶ',		   
			'menus' => array(
				'usualtags' => array('���ñ�ʶ',"?entry=usualtags"),
				'tagclasses' => array('���ñ�ʶ����',"?entry=usualtags&action=tagclasses"),
			),
		),
		'vote' => array(
			'title' => 'ͶƱ����',		   
			'menus' => array(
				'vcata' => array('ͶƱ����',"?entry=vcatalogs&action=vcatalogsedit"),
				'admin' => array('ͶƱ����',"?entry=votes&action=votesedit"),
				'add' => array('���ͶƱ',"?entry=votes&action=voteadd"),
			),
		),
		'wap' => array(
			'title' => 'WAP���',		   
			'menus' => array(
				'set' => array('WAP����',"?entry=wap"),
				'lang' => array('WAP���԰�',"?entry=wap&action=lang"),
			),
		),
		'memcert' => array(
			'title' => '��Ա��֤���',		   
			'menus' => array(
				'' => array('��֤�������',"?entry=memcerts"),
				'memcerts' => array('��֤���͹���',"?entry=memcerts&action=memcerts"),
				'add' => array('��֤�������',"?entry=memcerts&action=add"),
				'cfmobmail' => array('�ֻ�������','?entry=mconfigs&action=cfmobmail'),
				'email' => array('�ʼ�ģ��',"?entry=splangs&action=splangsedit"),
			),
		),
		'rebuilds' => array(
			'title' => '�������',		   
			'menus' => array(
				'system' => array('����ϵͳ����',"?entry=rebuilds"),
				'pagecache' => array('����ҳ�滺��',"?entry=rebuilds&action=pagecache"),
				'backup' => array('���汸��',"?entry=rebuilds&action=backup"),
			),
		),
		'mtconfigs' => array(
			'title' => '�ռ�ģ�巽��',
			'menus' => array(
				'mtconfigs' => array('�ռ�ģ��',"?entry=mtconfigs&action=mtconfigsedit"),
				'mcatalogs' => array('�ռ���Ŀ',"?entry=mcatalogs&action=mcatalogsedit"),
			),
		),
		'pushareas' => array(
			'title' => '����λ',
			'menus' => array(
				'pusharea' => array('����λ����',"?entry=pushareas"),
				'pushtype' => array('����λ����',"?entry=pushtypes"),
			),
		),	

    	'weixin' => array(
    		'title' => '΢������',
    		'menus' => array(
    			'config' => array('����ƽ̨����',"?entry=weixin&action=config"),
    			'menu' => array('�˵�����',"?entry=weixin&action=menu"),
    	#		'architecture' => array('���ܼܹ�',"?entry=weixin&action=architecture"),
    		),
    	),

        'estate' => array(//¥��
            'title' => '¥�̼۸�༭',
            'menus' => array(
                'price' => array('��ǰ�۸�',"?entry=extend&extend=jiagearchive&aid=$aid&isnew=1"),
                'list' => array('��ʷ�۸��б�',"?entry=extend&extend=jiagearchive&aid=$aid&action=list&isnew=1"),
                #'mcnodes' => array('>��ԱƵ���ڵ�',"?entry=mcnodes&action=mcnodesedit",1),
                #'historical' => array('��ʷ�۸�༭',"?entry=weixin&action=architecture"),
            ),
        ),
        'estate_historical' => array(//¥����ʷ�۸�
            'title' => '¥�̼۸�༭',
            'menus' => array(
                'price' => array('��ǰ�۸�',"?entry=extend&extend=jiagearchive&aid=$aid&isnew=1"),
                'list' => array('��ʷ�۸��б�',"?entry=extend&extend=jiagearchive&action=list&isnew=1&aid=$aid"),
                #'historical' => array('��ʷ�۸�༭',"?entry=weixin&action=architecture"),
            ),
        ),

        'housing_estate' => array(//С��
            'title' => 'С���۸�༭',
            'menus' => array(
                'price' => array('��ǰ�۸�',"?entry=extend&extend=jiagearchive&isnew=0&aid=$aid"),
                'list' => array('��ʷ�۸��б�',"?entry=extend&extend=jiagearchive&action=list&isnew=0&aid=$aid"),
            ),
        ),
        'housing_estate_historical' => array(//С����ʷ�۸�
            'title' => 'С���۸�༭',
            'menus' => array(
                'price' => array('��ǰ�۸�',"?entry=extend&extend=jiagearchive&isnew=0&aid=$aid"),
                'list' => array('��ʷ�۸��б�',"?entry=extend&extend=jiagearchive&action=list&isnew=0&aid=$aid"),
                #'historical' => array('��ʷ�۸�༭',"?entry=weixin&action=architecture"),
            ),
        ),



    );
?>
