<?php
$_mconfigs = array (

//	//��ͬϵͳ���ܻ��в��������,ע��ά��!!!!!!!!!!==================== 
//	'templatedir' => 'blue', // ģ��Ŀ¼ : Ĭ��ֵ��default
//	'cn_max_addno' => '5', //��Ŀ�ڵ㸽��ҳ�������
//	'mcn_max_addno' => '1', //��ԱƵ���ڵ㸽��ҳ�������
//	'max_addno' => '11', //�ĵ���������ҳ������� 
//	'cms_regcode' => 'register,login,admin,payonline,archive,archive8,archive106,archive108,archive101,commu1,commu2,commu3,commu4,commu5,commu8,commu32,commu33,commu35,commu40,commu45,commu46', //������֤��
//	'hostname' => '08cms�����Ż�ϵͳ',
//	'cmsname' => '08CMS�����Ż���վ', //����,����֮ǰ�����cmsname
//	'enable_mobile' => '1', //�����ֻ���-��
//	'unique_email' => '1',//һ������ֻ��ע��һ���˺�-��
//	
//	// house�������
//	'nouser_exts' => 'gif,jpg', //�ο������ϴ���������-
//	'nouser_capacity' => '300', //�ο��ϴ���С����-300K 
//	'close_gpub' => '0', //�ر��οͷ�����Դ-��
//	'count_gpub' => '3', //�οͷ�����Դ����-3
	
	//����ϵͳ��ͬ���ֵ����ã������ϲ���Ҫ�䶯==================== 
	
	// 1. api,plus : �ӿ�,���
	'sms_cfg_api' => '(close)', //�ֻ�����-�ӿ��ṩ��
	'enable_uc' => '0', //����UCenter-��
	'enable_pptout' => '0', //����ͨ��֤-�����   
	'enable_pptin' => '0', //����ͨ��֤-�ͻ���   
	'onlineautosaving' => '1', //����֧�������Զ���ֵ-�� 
	'ftp_enabled' => '0', //���ø���FTP�ϴ�-��
	'webcall_enable' => '0', //��վ�ṩ400�ܻ�-��
	'user_session' => '0', //���ÿ�վSESSION-��
	'qq_closed' => '0', //QQ��½-�ر� 
	'sina_closed' => '0', //����΢����½-�ر� 
	
	// 2. model,function : ����ģ��,��������
	'cmsclosed' => '0', // վ��ر�-��
	'registerclosed' => '0', //վ��ر�ע��-��
	'gzipenable' => '0', //�Ƿ�����ҳ��Gzipѹ��
	'enablestatic' => '0', //�Ƿ����þ�̬-��
	'virtualurl' => '0', //ǰ̨��̬ҳ��url���⾲̬-��

	// 3. ·�����
	'disable_htmldir' => '1',//�������ĵ�����Ŀ�ڵ㾲̬��Ŀ¼(html)
	'dir_userfile' => 'userfiles', //����·��(���ϵͳ��·��)
	'memberdir' => 'member', //��ԱƵ��·��
	'mspacedir' => 'mspace', //��Ա�ռ�·��
	'mobiledir' => 'mobile', //�ֻ���·��
	'infohtmldir' => 'info', //����ҳ��̬·��:Ĭ��ֵ��info
	'mc_dir' => 'adminc', //��Ա����Ŀ¼  Ĭ��ֵ��adminc
	
	// 4. ����,����
	'no_deepmode' => '1', //���üܹ�����ģʽ-��
	'cms_idkeep' => '0', //���üܹ�����ģʽ�������id-��
	'viewdebug' => '0', //ǰ̨ҳ����ʾ��ѯͳ��-��
	
	'debugenabled' => '0', //�Ƿ��ռ�ҳ��SQL��¼(SQL�������)
	'mallowfloatwin' => '1', //���ø�������(��Ա����)-��
	'debugtag' => '0', //ģ�������Ϊ����״̬-��
#	'arccustomurl' => '{$topdir}/{$y}{$m}{$d}/{$aid}_{$addno}_{$page}.html',  //  �ĵ�ҳ��̬�����ʽ
	
	'timezone' => '-8', //վ��ʱ��,+-�෴��
#	'cmslogo' => 'images/common/indlogo.gif', //վ��Logo, logo.png
	'regcode_width' => '60', //��֤��ͼƬ���(����)
	'regcode_height' => '25', //��֤��ͼƬ�߶�(����)
	'aeisablepinyin' => '0', //�����Զ�ƴ��-�� ---------- ע�����ֵ���෴��
	'aallowfloatwin' => '1', //���ø�������-��
	
	// 5. ����,�鿴,ͳ��
	'search_repeat' => '0',                //����ʱ��������(��),Ĭ��Ϊ0  
	'enabelstat' => '1',                  //������վͳ��:Ĭ��Ϊ1
	'clickscachetime' => '10',            //���ͳ�ƵĻ������� :Ĭ��Ϊ10
	'statweekmonth' => '1',               //�����ĵ��������ͳ��  :Ĭ��Ϊ1
	'amaxerrtimes' => '3',              //��¼����Դ������:  Ĭ��ֵ��3
	'aminerrtime' => '60',              //�����Զ��˳�ʱ��(����):  Ĭ��ֵ��60

	'vs_holdtime' => '0', //��¼������ʼ�¼
	'adminipaccess' => '', //��¼������ʼ�¼
	'censoruser' => '', //��¼������ʼ�¼
	'jsrefsource' => '', //js��̬����ֻ����������·
	'debugtag' => '1', //ģ�������Ϊ����״̬
	'search_pmid' => '0',                  //�����ĵ���Ȩ������,��ֵ��ΪO����
	'msearch_pmid' => '0',                  //�����ĵ���Ȩ������,��ֵ��ΪO����
	
	// del-���õ�����,����ɾ��
	'css_dir' => 'css', //ģ��cssĿ¼  Ĭ��ֵ��css
	'js_dir' => 'js', //ģ��jsĿ¼  Ĭ��ֵ��js
	//'msgcode_mode' => '0', //(�ֻ�����)��֤ģʽ-�ر���֤ ??? 	
	//'o_index_tpl' => '', //�ֻ�����ҳģ�� ---- �ر��ˣ�����
	
	'noinj_cfg_GET' => '1', //�ݱ�������;$_GET ��ע��-��
	'noinj_cfg_POST' => '0', //�ݱ�������;$_POST ��ע��-��
	//'hometpl' => 'v4_index.html', //��ҳģ��
	
) ;