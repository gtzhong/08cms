<?
(!defined('M_COM') || !defined('M_ADMIN')) && exit('No Permission');
aheader();
if($re = $curuser->NoBackFunc('webparam')) cls_message::show($re);
foreach(array('currencys','commus','channels','cotypes','mconfigs',) as $k) $$k = cls_cache::Read($k);
$mconfigs = cls_cache::Read('mconfigs');
if($action == 'cfsite'){
	backnav('mconfig','cfsite');
	if(!submitcheck('bmconfigs')){
		tabheader('��������','cfsite',"?entry=mconfigs&action=cfsite",2,1);
		trbasic('վ������','mconfigsnew[hostname]',$mconfigs['hostname'],'text',array('guide'=>'ǰ̨������ʽ:{$hostname}'));
		trbasic('վ������','mconfigsnew[hosturl]',$mconfigs['hosturl'],'text',array('guide'=>'<li>1��Ӧ��http����β�� /</li><li>2��ǰ̨������ʽ{$cms_abs}</li>'));
		trbasic('վ���������µ����·��','mconfigsnew[cmsurl]',$mconfigs['cmsurl'],'text',array('guide'=>'��β�躬 /'));
		trbasic('��ԱƵ��·��','mconfigsnew[memberdir]',$mconfigs['memberdir'],'text',array('guide'=>'��ԱƵ��·������Ҫ��/��{$memberdir}����·����{$memberurl}����url��'));
		trbasic('��Ա�ռ�·��','mconfigsnew[mspacedir]',$mconfigs['mspacedir'],'text',array('guide'=>'��Ա�ռ�·������Ҫ��/��{$mspacedir}����·����{$mspaceurl}����url��'));
		$tzarr=array(
			'+12'=>'(GMT-12) International Date Line (West)',
			'+11'=>'(GMT-11) Midway Island,Samoa',
			'+10'=>'(GMT-10) Hawaii,Honolulu',
			'+9'=>'(GMT-9) Alaska',
			'+8'=>'(GMT-8) Pacific Standard Time,US,Canada',
			'+7'=>'(GMT-7) British Columbia N.E.,Santa Fe,Mountain Time',
			'+6'=>'(GMT-6) Central America,Chicago,Guatamala,Mexico City',
			'+5'=>'(GMT-5) US,Canada,Bogota,Boston,New York',
			'+4'=>'(GMT-4) Canada,Santiago,Atlantic Standard Time',
			'+3'=>'(GMT-3) Brazilia,Buenos Aires,Georgetown,Greenland',
			'+2'=>'(GMT-2) Mid-Atlantic',
			'+1'=>'(GMT-1) Azores,Cape Verde Is.,Western Africa Time',
			'0'=>'(GMT) London,Iceland,Ireland,Morocco,Portugal',
			'-1'=>'(GMT+1) Amsterdam,Berlin,Bern,Madrid,Paris,Rome',
			'-2'=>'(GMT+2) Athens,Cairo,Cape Town,Finland,Greece,Israel',
			'-3'=>'(GMT+3) Ankara,Aden,Baghdad,Beruit,Kuwait,Moscow',
			'-4'=>'(GMT+4) Abu Dhabi,Baku,Kabul,Tehran,Tbilisi,Volgograd',
			'-5'=>'(GMT+5) Calcutta,Colombo,Islamabad,Madras,New Dehli',
			'-6'=>'(GMT+6) Almaty,Dhakar,Kathmandu,Colombo,Sri Lanka',
			'-7'=>'(GMT+7) Bangkok,Hanoi,Jakarta,Phnom Penh,Australia',
			'-8'=>'(GMT+8) Beijing,Hong Kong,Singapore,Taipei',
			'-9'=>'(GMT+9) Seoul,Tokyo,Central Australia',
			'-10'=>'(GMT+10) Brisbane,Canberra,Guam,Melbourne,Sydney',
			'-11'=>'(GMT+11) Magadan,New Caledonia,Solomon Is.',
			'-12'=>'(GMT+12) Auckland,Fiji,Kamchatka,Marshall,Wellington'
		);
		trbasic('����վ��ʱ��','mconfigsnew[timezone]',makeoption($tzarr,isset($timezone)?$timezone:-8),'select');	
		trbasic('���ֱ����¼��Ч��',"mconfigsnew[point_interval]",(empty($mconfigs['point_interval']) ? 0 : $mconfigs['point_interval']),'text',array('guide'=>'��λ���£����������֮ǰ�ĳ��ֽ���������л������͵ļ�¼������Ϊ�������','validate'=>makesubmitstr("mconfigsnew[point_interval]",0,'int',0,4)));
		tabfooter();
		
		tabheader('��ͼ����');
		trbasic('��ͼ��ʼ��λ����','',"<input class='btnmap' type='button' onmouseover='this.onfocus()' onfocus='_08cms.map.setButton(this,\"marker\",\"mconfigsnew[init_map]\",\"\",\"13\");' /> <label for='mconfigsnew[init_map]'>γ��,���ȣ�</label><input type='text' id='mconfigsnew[init_map]' name='mconfigsnew[init_map]' value='".@$mconfigs['init_map']."' style='width:150px'>",'',array('guide'=>'��ͼ�ֶ��е�Ĭ�ϳ�ʼλ��'));
        trbasic('��ͼ��ʼ���ż���','',"<label for='mconfigsnew[init_map_zoom]'>���ż���</label><input type='text' id='mconfigsnew[init_map_zoom]' name='mconfigsnew[init_map_zoom]' value='".@$mconfigs['init_map_zoom']."' style='width:50px'>",'',array('guide'=>'��ͼ��ʼ���ż���������1-19������'));
		trbasic('�ٶȵ�ͼKEY','',"<label for='mconfigsnew[bmapkey]'></label><input type='text' id='mconfigsnew[bmapkey]' name='mconfigsnew[bmapkey]' value='".@$mconfigs['bmapkey']."' style='width:320px'>",'',array('guide'=>'�ɵ��<a href=\'http://lbsyun.baidu.com/apiconsole/key?application=key\' style=\'color:blue;\' target=\'_blank\'>>>����ٶȵ�ͼ��Կ</a>�������롣����ɹ��󣬰���Կ����������ı����У��ύ���ɡ�֧�ֵ�ͼAPI2.0�汾������Ĭ��Ϊ�ϰ汾��ͼ���°汾֧�ֲ�ѯ������·�߷��ֻ��ȹ���,����IE6������ϱ��ֲ��Ѻ�'));		
        trbasic('�־���ͼ��ʾ����','',makeradio('mconfigsnew[streetviewtype]', array('Tencent'=>'��Ѷ�־�','noview'=>'�رս־�'), empty($mconfigs['streetviewtype'])?'noview':$mconfigs['streetviewtype']),'',array('guide'=>'Ŀǰ�־����ǵĳ���ֻ��һС���ִ���С��������ĳ��в��ڸ��Ƿ�Χ�ڣ���ѡ��\'�رս־�\'��<br/>�鿴<a href=\'http://map.qq.com/jiejing/city.html\' style=\'color:blue;\' target=\'_blank\'>>>��Ѷ�־�����</a>'));
        trbasic('�־���ͼKEY','',"<label for='mconfigsnew[streetviewkey]'></label><input type='text' id='mconfigsnew[streetviewkey]' name='mconfigsnew[streetviewkey]' value='".@$mconfigs['streetviewkey']."' style='width:320px'>",'',array('guide'=>'�ɵ��<a href=\'http://open.map.qq.com/key.html\' style=\'color:blue;\' target=\'_blank\'>>>������Ѷ��Կ</a>�������롣����ɹ��󣬰���Կ����������ı����У��ύ���ɡ�'));
        tabfooter();
		
		tabheader('�ٶȲ���');
        trbasic('�༭��appkey','',"<label for='mconfigsnew[ueditor_appkey]'></label><input type='text' id='mconfigsnew[ueditor_appkey]' name='mconfigsnew[ueditor_appkey]' value='".@$mconfigs['ueditor_appkey']."' style='width:320px'>",'',array('guide'=>'�����ڰٶȱ༭�������ٶ�Ӧ�ã���<a href=\'http://app.baidu.com/static/cms/getapikey.html\' style=\'color:blue;\' target=\'_blank\'>>>�������</a>�������롣����ɹ��󣬰���Կ����������ı����У��ύ���ɡ�'));
        tabfooter();

		
		tabheader('��վͳ��');
		trbasic('������վͳ��','mconfigsnew[enabelstat]',$mconfigs['enabelstat'],'radio');
		trbasic('���ͳ�ƵĻ�������','mconfigsnew[clickscachetime]',$mconfigs['clickscachetime'],'text',array('guide' => '��λ���룬Ӱ���ĵ����ռ�ĵ��ͳ�ơ���ͳ�Ƽ�ʱ��Ҫ�󲻸ߣ���ɴ���600Ϊ�ˡ�'));
		trbasic('�����ĵ��������ͳ��','mconfigsnew[statweekmonth]',@$mconfigs['statweekmonth'],'radio',array('guide' => '��Ҫ��ϼƻ������е��������ͳ�ƹ�������ɡ�'));
		tabfooter();
		
		tabheader('��滺������','cfupload','?entry=mconfigs&action=cfupload');
        trbasic('���ݻ�������',"mconfigsnew[adv_period]",(empty($mconfigs['adv_period']) ? 0 : $mconfigs['adv_period']),'text',array('guide'=>'��λ�����ӣ�����Ϊ�����档','validate'=>makesubmitstr("mconfigsnew[adv_period]",0,'int',0,4)));
        trbasic('�����ͳ������',"mconfigsnew[adv_viewscache]",(empty($mconfigs['adv_viewscache']) ? 0 : $mconfigs['adv_viewscache']),'text',array('guide'=>'��λ�����ӣ�����Ϊ��ʱͳ�ơ�','validate'=>makesubmitstr("mconfigsnew[adv_viewscache]",0,'int',0,4)));
		tabfooter('bmconfigs');
		a_guide('cfsite');
	}else{
		if(empty($mconfigsnew['hosturl']) || !in_str('http://',$mconfigsnew['hosturl'])){
			cls_message::show('����URL���Ϲ淶',M_REFERER);
		}
		$mconfigsnew['hosturl'] = strtolower($mconfigsnew['hosturl']);
		$mconfigsnew['cmsurl'] = empty($mconfigsnew['cmsurl']) ? '/' : trim(strtolower($mconfigsnew['cmsurl']));
		$mconfigsnew['cmsurl'] .= (substr($mconfigsnew['cmsurl'], strlen($mconfigsnew['cmsurl']) - 1) == '/' ? '' : '/');
		$mconfigsnew['cmsname'] = $mconfigsnew['hostname'] = trim(strip_tags($mconfigsnew['hostname']));//����֮ǰ�����cmsname

		foreach(array('mspacedir','memberdir',) as $var){
			$mconfigsnew[$var] = strtolower($mconfigsnew[$var]);
			if($mconfigsnew[$var] == $mconfigs[$var]) continue;
			if(!$mconfigsnew[$var] || preg_match("/[^a-z_0-9]+/",$mconfigsnew[$var])){
				$mconfigsnew[$var] = $mconfigs[$var];
				continue;
			}
			if($mconfigs[$var] && is_dir(M_ROOT.$mconfigs[$var])){
				if(!rename(M_ROOT.$mconfigs[$var],M_ROOT.$mconfigsnew[$var])) $mconfigsnew[$var] = $mconfigs[$var];
			}else mmkdir(M_ROOT.$mconfigsnew[$var],0);
		}
		
		$mconfigsnew['adv_period'] = max(0,intval($mconfigsnew['adv_period']));		       
        $mconfigsnew['init_map_zoom'] = max(0,intval($mconfigsnew['init_map_zoom']));
		$mconfigsnew['adv_viewscache'] = max(0,intval($mconfigsnew['adv_viewscache']));
		
		saveconfig('site');
		adminlog('��վ����','վ����Ϣ');
		cls_message::show('��վ�������',M_REFERER);
	}
}elseif($action == 'cfvisit'){
	backnav('mconfig','cfvisit');
	if(!submitcheck('bmconfigs')){
		tabheader('��������','cfvisit','?entry=mconfigs&action=cfvisit');
		trbasic('վ��ر�','mconfigsnew[cmsclosed]',$mconfigs['cmsclosed'],'radio');
		trbasic('վ��ر�ԭ��','mconfigsnew[cmsclosedreason]',$mconfigs['cmsclosedreason'],'text',array('w'=>50));
		trbasic('��Ա�ռ�ر�','mconfigsnew[mspacedisabled]',$mconfigs['mspacedisabled'],'radio');
		tabfooter();

		tabheader('��Աע������');
		trbasic('վ��ر�ע��','mconfigsnew[registerclosed]',$mconfigs['registerclosed'],'radio');
		trbasic('ע��ر�ԭ��','mconfigsnew[regclosedreason]',$mconfigs['regclosedreason'],'text',array('w'=>50));
		trbasic('����ͬһ Email ��ַע�����û�','mconfigsnew[unique_email]', @$mconfigs['unique_email'],'radio');
		trbasic('�û����Ʊ�����','mconfigsnew[censoruser]',$mconfigs['censoruser'],'textarea',array('guide'=>'�û�������ʹ���б��еĹؼ��ʣ�ÿ����дһ���ؼ��ʣ�����ʹ��ͨ��� *'));
		tabfooter();

		tabheader('��Ա��������');
		trbasic('��¼����Դ������','mconfigsnew[maxerrtimes]',$mconfigs['maxerrtimes'],'text',array('guide'=>'���ձ�ʾ���޴��������������Ϊ3��'));
		trbasic('��¼ʧ������ʱ��','mconfigsnew[minerrtime]',$mconfigs['minerrtime'],'text',array('guide'=>'��λ:���ӣ�����Ϊ60���ӡ�'));
		trbasic('��Ա�״����������','mconfigsnew[onlinetimecircle]',$mconfigs['onlinetimecircle'],'text',array('guide'=>'��λ:���ӣ���Ϊ10-20����Ϊ�ˡ�'));
		trbasic('��Ա���¼����ʱ��','mconfigsnew[onlinehold]',$mconfigs['onlinehold'],'text',array('guide'=>'��λ:Сʱ��ʱ�䳤������ڵ�¼ʧ������ʱ�估��Ա�ʱ��������ڣ���Ϊ6СʱΪ�ˡ�'));
		tabfooter();

		tabheader('��֤������');
		$arr = cls_cache::exRead('cfregcodes');
		foreach($arr as $k => $v) $arr[$k] = $v.'-'.$k;
		trbasic('��Ҫ���õ���֤��','',makecheckbox('mconfigsnew[cms_regcode][]',$arr,empty($mconfigs['cms_regcode']) ? array() : explode(',',$mconfigs['cms_regcode']),5),'');
		trbasic('������֤���Զ���������ʽ','mconfigsnew[regcode_style]', @$mconfigs['regcode_style'],'radio', array('guide'=>'������ø�ѡ���ֱ���� /images/fonts Ŀ¼���޸������ļ����ɣ�ע���ļ�����ֻ�������֡���ĸ���»�����ϣ�_08 ��ͷ�ĺ� simsun.ttc Ϊϵͳ���岻��ɾ����' ));
		trbasic('��֤����Ϸ�ʽ','mconfigsnew[regcode_mode]', makeoption(array('1' => '����', '2' => '��ĸ', '3' => '��������ĸ'), @$mconfigs['regcode_mode']), 'select');
		trbasic('��֤��ͼƬ���(����)','mconfigsnew[regcode_width]',$mconfigs['regcode_width'], 'text', array('guide'=>'������ֵ��200�������Ҫ�������뽨��߶�ֵ������������' ));
		trbasic('��֤��ͼƬ�߶�(����)','mconfigsnew[regcode_height]',$mconfigs['regcode_height'], 'text', array('guide'=>'����߶�ֵ��70�������Ҫ�������뽨����ֵ������������' ));
		tabfooter();

		tabheader('��������');
		setPermBar('�����ĵ���Ȩ������', 'mconfigsnew[search_pmid]', @$mconfigs['search_pmid'], 'aread', 'open', '');
		setPermBar('������Ա��Ȩ������', 'mconfigsnew[msearch_pmid]', @$mconfigs['msearch_pmid'], 'aread', 'open', '');
        trbasic('����ʱ��������(��)','mconfigsnew[search_repeat]',$mconfigs['search_repeat']);
		tabfooter();

		tabheader('RSS����');
		trbasic('����RSS','mconfigsnew[rss_enabled]',$mconfigs['rss_enabled'],'radio');
		trbasic('RSSˢ������(����)','mconfigsnew[rss_ttl]',$mconfigs['rss_ttl']);
		tabfooter('bmconfigs');
		a_guide('cfvisit');
	}else{
		$mconfigsnew['maxerrtimes'] = max(0,intval($mconfigsnew['maxerrtimes']));
		$mconfigsnew['minerrtime'] = max(1,intval($mconfigsnew['minerrtime']));
		$mconfigsnew['onlinetimecircle'] = max(1,intval($mconfigsnew['onlinetimecircle']));
		$mconfigsnew['onlinehold'] = max(1,intval($mconfigsnew['onlinehold']));

		$mconfigsnew['search_repeat'] = max(0,intval($mconfigsnew['search_repeat']));
		$mconfigsnew['regcode_width'] = max(60,intval($mconfigsnew['regcode_width']));
		$mconfigsnew['regcode_height'] = max(20,intval($mconfigsnew['regcode_height']));
		$mconfigsnew['cms_regcode'] = empty($mconfigsnew['cms_regcode']) ? '' : implode(',',$mconfigsnew['cms_regcode']);
		$mconfigsnew['rss_ttl'] = empty($mconfigsnew['rss_ttl']) ? 30 : max(0,intval($mconfigsnew['rss_ttl']));
		saveconfig('visit');
		adminlog('��վ����','������ע������');
		cls_message::show('��վ�������','?entry=mconfigs&action=cfvisit');
	}
}elseif($action == 'cfview'){
	backnav('mconfig','cfview');
	if(!submitcheck('bmconfigs')){
		tabheader('ҳ��ͨ������','cfview',"?entry=mconfigs&action=cfview");
		trbasic('ҳ��Gzipѹ��','mconfigsnew[gzipenable]',$mconfigs['gzipenable'],'radio');
		trbasic('Ĭ�����ڸ�ʽ','mconfigsnew[dateformat]',makeoption(array('Y-m-d' => '����'.'2008-01-01','Y-n-j' => '����'.'2008-1-1',),$mconfigs['dateformat']),'select');
		trbasic('Ĭ��ʱ���ʽ','mconfigsnew[timeformat]',makeoption(array('H:i' => '����'.'20:30','H:i:s' => '����'.'20:30:30',),$mconfigs['timeformat']),'select');
		trbasic('ǰ̨��ʾ��Ϣͣ��(����)','mconfigsnew[msgforwordtime]',$mconfigs['msgforwordtime']);
		tabfooter();

		tabheader('α��̬����');
		trbasic('����ǰ̨urlα��̬','mconfigsnew[virtualurl]',$mconfigs['virtualurl'],'radio');
		trbasic('.php?��Rewrite��Ӧ�ִ�','mconfigsnew[rewritephp]',$mconfigs['rewritephp'],'text',array('guide'=>'������Ϊ-htm-����α��̬url ��archive.php?aid=5������װΪarchive-htm-aid-5.html��<br>������urlα��̬����ʱ��Ч���뱣����վ��rewrite�������Ӧ��'));
        trbasic('�����������Rewrite�汾','mconfigsnew[serversoft]',makeoption(array('0' => '�Զ���ȡ','apache' => 'APACHE', 'nginx' => 'NGINX', 'iis_2' => 'IIS ISAPI Rewrite2������', 'iis_3' => 'IIS ISAPI Rewrite3������'), empty($mconfigs['serversoft']) ? 0 : $mconfigs['serversoft']),'select',array('guide'=>'<span style="color:red;">1��ע�������վ��Ŀ¼���Ѿ�����Rewrite�����ļ�(IIS����httpd.ini��NGINX��APACHE���� .htaccess)�������޸Ĺ�ʱ���������б��ݡ�</span><br />2��Rewrite�����ļ�ֻ�������ˣ�ǰ̨��̬ҳ��urlα��̬���޸Ĺ���.php?��Rewrite��Ӧ�ִ� ʱ�Ż����������ļ�����վ��Ŀ¼��<br />3���뾡���ֶ�ѡ���ȡ����Ϊ�Զ�ѡ���ȡĬ�ϵ��������°汾����,���Կ��ܻ��л�ȡ��׼ȷ������������ʱ�Ӷ�Ӱ������������⡣'));
		tabfooter();

		tabheader('ҳ�渽��ҳ����');
		trbasic('��Ŀ�ڵ㸽��ҳ�������','mconfigsnew[cn_max_addno]',empty($mconfigs['cn_max_addno']) ? 0 : $mconfigs['cn_max_addno']);
		trbasic('�ĵ���������ҳ�������','mconfigsnew[max_addno]',empty($mconfigs['max_addno']) ? 0 : $mconfigs['max_addno']);
		trbasic('��ԱƵ���ڵ㸽��ҳ�������','mconfigsnew[mcn_max_addno]',empty($mconfigs['mcn_max_addno']) ? 0 : $mconfigs['mcn_max_addno']);
		tabfooter('bmconfigs');
		a_guide('cfview');

	}else{
		$mconfigsnew['msgforwordtime'] = max(0,intval($mconfigsnew['msgforwordtime']));
		$mconfigsnew['cn_max_addno'] = min(empty($_sys_cnaddmax) ? 2 : $_sys_cnaddmax,max(0,intval($mconfigsnew['cn_max_addno'])));
		$mconfigsnew['mcn_max_addno'] = min(empty($_sys_mcnaddmax) ? 0 : $_sys_mcnaddmax,max(0,intval($mconfigsnew['mcn_max_addno'])));
		$mconfigsnew['max_addno'] = min(empty($_sys_addmax) ? 3 : $_sys_addmax,max(0,intval($mconfigsnew['max_addno'])));
        if ( ($mconfigsnew['rewritephp'] != $mconfigs['rewritephp']) || ($mconfigsnew['serversoft'] != $mconfigs['serversoft']) )
        {
            $rewrite = new _08_Rewrite($mconfigsnew['rewritephp'], $mconfigsnew['virtualurl']);
            $rewrite->create($mconfigsnew['serversoft'], $mconfigsnew['virtualurl']);
        }
		saveconfig('view');
		adminlog('��վ����','ҳ���������');
		cls_message::show('��վ�������',"?entry=mconfigs&action=cfview");
	}
}elseif($action == 'cfppt'){
	backnav('mconfig','cfppt');
	if(!submitcheck('bmconfigs')){
		tabheader('UCenter �ͻ�������','cfppt','?entry=mconfigs&action=cfppt');
		trbasic('����UCenter','mconfigsnew[enable_uc]',$mconfigs['enable_uc'],'radio');
		trbasic('UCenter ���ӷ�ʽ','',makeradio('mconfigsnew[uc_connect]', array('mysql'=>'mysql(�Ƚ��ȶ�,�Ƽ�)','post'=>'post(����ҪUCenter���ݿ�����)'), $mconfigs['uc_connect']=='post' ? 'post' : 'mysql'),''); //
		trbasic('UCenter API ��ַ','mconfigsnew[uc_api]',$mconfigs['uc_api'],'text',array('guide' => 'ĩβ����б�ˡ�','w' => 50,));
		trbasic('UCenter ����IP','mconfigsnew[uc_ip]',$mconfigs['uc_ip'],'text',array('guide' => 'ͨ�����գ�������������ͨ��ʧ��ʱ�����ø�ֵ��',));
		trbasic('UCenter ���ݿ�������','mconfigsnew[uc_dbhost]',$mconfigs['uc_dbhost']);
		trbasic('UCenter ���ݿ���','mconfigsnew[uc_dbname]',$mconfigs['uc_dbname']);
		trbasic('UCenter ���ݿ��û���','mconfigsnew[uc_dbuser]',$mconfigs['uc_dbuser']);
		trbasic('UCenter ���ݿ�����','mconfigsnew[uc_dbpwd]',$mconfigs['uc_dbpwd'],'password',array('validate' => ' autocomplete="off"'));#��ֹ������Զ���ɱ�
		trbasic('UCenter ���ݿ��ǰ׺','mconfigsnew[uc_dbpre]',$mconfigs['uc_dbpre']);
		trbasic('UCenter �����Ӧ��ID','mconfigsnew[uc_appid]',$mconfigs['uc_appid']);
		trbasic('UCenter ͨ����Կ','mconfigsnew[uc_key]',$mconfigs['uc_key']);
		trbasic('UCenter ��¼������Ϣ','mconfigsnew[uc_debug]',$mconfigs['uc_debug'],'radio',array('guide' => '�����������ѡ���ǣ���¼������Ϣ(<a href="'.$cms_abs.'api/uc.php?code=uclog_view" target="_blank">���յ�����Ϣ</a>)��'));
		tabfooter();
		$pfilearr = array('08cms' => '08CMS','phpwind' => 'PHPwind',);
		$pcharsetarr = array('gbk' => 'GBK/GB2312','utf-8' => 'UTF-8','big5' => 'BIG5',);
		$pptenable = array(1 => '����', 0 => '����');
	#	$pptmode = array(1 => '�����', 0 => '�ͻ���');
		tabheader('WindID ͨ��֤�ͻ�������');
		trbasic('����ͨ��֤','',makeradio('is_enable_ppt', $pptenable, $mconfigs['enable_pptout'] || $mconfigs['enable_pptin']),'');
		trbasic('ͨ��֤���ӷ�ʽ','',makeradio('mconfigsnew[pptout_connect]', array('db'=>'mysql(�Ƚ��ȶ�,�Ƽ�)','http'=>'http(����Ҫͨ��֤���ݿ�����)'), @$mconfigs['pptout_connect']=='http' ? 'http' : 'db'),'');
		trbasic('ͨ��֤����˵�ַ','mconfigsnew[pptin_url]',$mconfigs['pptin_url'], 'text',array('guide' => 'ĩβ����б�ˡ�','w' => 50,));
	#	trbasic('����ϵͳ��Ϊ','',makeradio('ppt_mode', $pptmode, $mconfigs['enable_pptout'] ? 1 : ($mconfigs['enable_pptin'] ? 0 : -1)),'');
		trbasic('�ӿڳ����ַ���','mconfigsnew[pptout_charset]',makeoption($pcharsetarr,$mconfigs['pptout_charset']),'select',array('guide' => '�뱣�ֽӿڳ����ַ��������ݿ��ַ�����ͬ��'));
		trbasic('ͨ��֤��Կ','ppt_key',$mconfigs['pptin_key'] ? $mconfigs['pptin_key'] : $mconfigs['pptout_key']);
	#	echo '<tr><td class="txt txtleft fB borderright" colspan="2"><div style="margin:0 100px; padding:0 10px;color:#134D9D; background:#F1F7FD">�����</div></td></tr>';
#		trbasic('�ӿڳ���URL��ַ','mconfigsnew[pptout_url]',$mconfigs['pptout_url']);
#		echo '<tr><td class="txt txtleft fB borderright" colspan="2"><div style="margin:0 100px; padding:0 10px;color:#134D9D; background:#F1F7FD">�ͻ���</div></td></tr>';
		trbasic('��֤�ִ���Ч��(��)','mconfigsnew[pptin_expire]',$mconfigs['pptin_expire']);
		trbasic('ͨ��֤Ӧ��ID','mconfigsnew[pptin_appid]', @$mconfigs['pptin_appid']);
		trbasic('ͨ��֤���ݿ�������','mconfigsnew[pptin_dbhost]', empty($mconfigs['pptin_dbhost']) ? 'localhost' : $mconfigs['pptin_dbhost']);
		trbasic('ͨ��֤���ݿ�˿�','mconfigsnew[pptin_port]', empty($mconfigs['pptin_port']) ? '3306' : $mconfigs['pptin_port']);
		trbasic('ͨ��֤���ݿ���','mconfigsnew[pptin_dbname]',@$mconfigs['pptin_dbname']);
		trbasic('ͨ��֤���ݿ��û���','mconfigsnew[pptin_dbuser]',@$mconfigs['pptin_dbuser']);
		trbasic('ͨ��֤���ݿ�����','mconfigsnew[pptin_dbpwd]',@$mconfigs['pptin_dbpwd'],'password',array('validate' => ' autocomplete="off"'));#��ֹ������Զ���ɱ�
		trbasic('ͨ��֤���ݿ��ǰ׺','mconfigsnew[pptin_dbpre]',@$mconfigs['pptin_dbpre'],'text',array('guide' => 'windid�ı�ǰ׺��Ĭ��ϵͳ��ǰ׺����windid_����pw_��windid�ı�ǰ׺Ϊpw_windid_��'));
	#	trbasic('�ӿڳ���ע���ַ','mconfigsnew[pptin_register]',$mconfigs['pptin_register']);
	#	trbasic('�ӿڳ����¼��ַ','mconfigsnew[pptin_login]',$mconfigs['pptin_login']);
	#	trbasic('�ӿڳ����˳���ַ','mconfigsnew[pptin_logout]',$mconfigs['pptin_logout']);
  
		tabfooter('bmconfigs');
		a_guide('cfppt');
	}else{
		if(($mconfigsnew['enable_uc'] && empty($mconfigs['enable_uc']) || !$is_enable_ppt)){
			//ʹ��UC
			$mconfigsnew['enable_pptout'] = 0;
			$mconfigsnew['enable_pptin']  = 0;
		}else{
		    $ppt_mode = 0; #ָ��Ϊ�ͻ���
			$mconfigsnew['enable_uc'] = 0;
			if(empty($ppt_mode)){
				//ʹ�ÿͻ���
				$mconfigsnew['enable_pptout'] = 0;
				$mconfigsnew['enable_pptin']  = 1;
				$mconfigsnew['pptin_key']	  = $ppt_key;
				$mconfigsnew['pptout_key']	  = '';
			}else{
				//ʹ�÷����
				$mconfigsnew['enable_pptout'] = 1;
				$mconfigsnew['enable_pptin']  = 0;
				$mconfigsnew['pptin_key']	  = '';
				$mconfigsnew['pptout_key']	  = $ppt_key;
			}
		}

		saveconfig('ppt');
		adminlog('��վ����','��վpptput����ͨ��֤����');
		cls_message::show('��վ�������','?entry=mconfigs&action=cfppt');
	}
}elseif($action == 'cfpay'){
	backnav('mconfig','cfpay');
	if(!submitcheck('bmconfigs')){
		tabheader('������ػ�������','cfpay','?entry=mconfigs&action=cfpay');
		trbasic('����֧�������Զ���ֵ','mconfigsnew[onlineautosaving]',$mconfigs['onlineautosaving'],'radio');
		$pmodearr = array('0' => '��������','1' => 'վ���ʻ�֧��','2' => '֧������ʱ����','3' => '�Ƹ�֧ͨ��','4' => '֧��������֧��','5' => '֧�����ֻ�֧��');
		$payarr = array();
		for($i = 0; $i < 32; $i++)if(@$mconfigs['cfg_paymode'] & (1 << $i))$payarr[] = $i;
		trbasic('֧��ģʽ','',makecheckbox('paymodenew[]',$pmodearr,$payarr),'');
		tabfooter();

		tabheader('֧����-����֧������<span style="color:red;">��ע������֧�����ܱ����ȿ���PHP��CURL��OPENSSL��չ��</span>');
		trbasic('֧�����ʻ�','mconfigsnew[cfg_alipay]',@$mconfigs['cfg_alipay']);
		trbasic('���������(PID)','mconfigsnew[cfg_alipay_partnerid]',@$mconfigs['cfg_alipay_partnerid']);
		trbasic('��ȫУ����(Key)','mconfigsnew[cfg_alipay_keyt]', @$mconfigs['cfg_alipay_keyt'], 'password');
		tabfooter();
		tabheader('�Ƹ�ͨ-����֧������');
		trbasic('�̻����','mconfigsnew[cfg_tenpay]',@$mconfigs['cfg_tenpay']);
		trbasic('֧����Կ','mconfigsnew[cfg_tenpay_keyt]',@$mconfigs['cfg_tenpay_keyt'], 'password');
		tabfooter('bmconfigs');
		a_guide('cfpay');
	}else{
		$mconfigsnew['cfg_paymode'] = 0;
		empty($paymodenew) && $paymodenew = array();
		foreach($paymodenew as $v){
			if($v==='') continue; //��һ����ֵȡ����,0����ȥ��
			$mconfigsnew['cfg_paymode'] = $mconfigsnew['cfg_paymode'] | (1 << $v);
		}   
        if (!$curuser->info['mid'] == 1)
        {
            $salt = $curuser->info['salt'];
        }
        else
        {
            $row = $db->select('salt')->from('#__members')->where(array('mid' => 1))->limit(1)->exec()->fetch();
            $salt = $row['salt'];
        }
        
        if (@$mconfigsnew['cfg_alipay_keyt'] != @$mconfigs['cfg_alipay_keyt'])
        {
            $mconfigsnew['cfg_alipay_keyt'] = authcode($mconfigsnew['cfg_alipay_keyt'], 'ENCODE', $salt);
        }
        if (@$mconfigsnew['cfg_tenpay_keyt'] != @$mconfigs['cfg_tenpay_keyt'])
        {
            $mconfigsnew['cfg_tenpay_keyt'] = authcode($mconfigsnew['cfg_tenpay_keyt'], 'ENCODE', $salt);
        }
		saveconfig('pay');
		adminlog('��վ����֧������','��վ����֧������');
		cls_message::show('����֧���������','?entry=mconfigs&action=cfpay');
	}
}elseif($action == 'cfupload'){
	backnav('mconfig','cfupload');
	$vftp_password = $tftp_password = '';
	if(!empty($mconfigs['ftp_password'])){
		$tftp_password = authcode($mconfigs['ftp_password'],'DECODE',md5($authkey));
		@$vftp_password = $tftp_password{0}.'********'.$tftp_password{strlen($tftp_password) - 1};
	}
	if(!submitcheck('bmconfigs')){
		$upatharr = array('0' => 'Ĭ��'.'('.'��������'.')','month' => '��������'.'+'.'��','day' => '��������'.'+'.'����');

		tabheader('�ϴ��������� &nbsp;>><a href="?entry=localfiles&action=localfilesedit">������������</a>','cfupload','?entry=mconfigs&action=cfupload');
		trbasic('����·��(���ϵͳ��·��)','mconfigsnew[dir_userfile]',$mconfigs['dir_userfile']);
		trbasic('�������ౣ��','mconfigsnew[path_userfile]',makeoption($upatharr,$mconfigs['path_userfile']),'select');
		if(!empty($watermarks) && is_array($watermarks)) foreach($watermarks as $k => $v) $wmidsarr[$k] = $v['cname'];
		trbasic('Ĭ��ý�岥�ſ��','mconfigsnew[player_width]',$mconfigs['player_width']);
		trbasic('Ĭ��ý�岥�Ÿ߶�','mconfigsnew[player_height]',$mconfigs['player_height']);
		setPermBar('�ϴ�����Ȩ������', 'mconfigsnew[pm_upload]', @$mconfigs['pm_upload'] , 'down', 'open', '');
		setPermBar('�������Ȩ������', 'mconfigsnew[atmbrowser]', @$mconfigs['atmbrowser'], 'down', 'open', '');
        trbasic('�ο��ϴ���С����','mconfigsnew[nouser_capacity]',$mconfigs['nouser_capacity'],'text',array('guide' => '���ջ�����0Ϊ��ֹ�ο��ϴ�����λ:K'));
		trbasic('�ο������ϴ���������','mconfigsnew[nouser_exts]',$mconfigs['nouser_exts'],'text',array('guide' => '�˴������������Ҫͬʱ�������ϴ������в���Ч����ʽ��:gif,jpg�������������ϴ���������������'));
		tabfooter();

		tabheader('Զ�̸���FTP����');
		trbasic('���ø���FTP�ϴ�','mconfigsnew[ftp_enabled]',$mconfigs['ftp_enabled'],'radio',array('guide'=>'���ú󣬽�����"����·���ĸ���ʹ��FTP"�ĸ����ű��浽FTP')); // ��ָ��������"ftpԶ�̸�������"��Ч��
		trbasic('����·���ĸ���ʹ��FTP','mconfigsnew[other_ftp_dir]',$mconfigs['other_ftp_dir'],'text',array('w'=>'60','guide'=>'ֻ�б�ָ�����ļ��в�����Զ��ftp���渽����"userfiles"��ָ�����·������|�ָ���:userfiles/image|userfiles/video,���������κθ������浽ftp')); //Ĭ��ftp_dir,ȥ����
		trbasic('FTP ��������ַ','mconfigsnew[ftp_host]',$mconfigs['ftp_host']);
		trbasic('FTP �������˿�','mconfigsnew[ftp_port]',$mconfigs['ftp_port']);
		trbasic('FTP �ʺ�','mconfigsnew[ftp_user]',$mconfigs['ftp_user']);
		trbasic('FTP ����','mconfigsnew[ftp_password]',$vftp_password);
		trbasic('FTP ���䳬ʱʱ��','mconfigsnew[ftp_timeout]',$mconfigs['ftp_timeout']);
		trbasic('�Ƿ�ʹ�ñ���ģʽ(pasv)�ϴ�','mconfigsnew[ftp_pasv]',$mconfigs['ftp_pasv'],'radio');
		trbasic('�Ƿ�����SSL��ȫ����','mconfigsnew[ftp_ssl]',$mconfigs['ftp_ssl'],'radio');
		trbasic('FTP�ĸ���������Ŀ¼','mconfigsnew[ftp_dir]',      $mconfigs['ftp_dir'],      'text',array('w'=>'60','guide'=>'����һ����Ŀ(վ��)һ��Ŀ¼��"08cms"����ʼ��β����Ҫ��б��"/"��"."��ʾ FTP ��Ŀ¼���������ֻ��һ����Ŀ���ø�Ŀ¼��'));
		trbasic('FTP�����ķ�����URL', 'mconfigsnew[ftp_url]',      $mconfigs['ftp_url'],      'text',array('w'=>'60','guide'=>'�������urlָ��"����������Ŀ¼"��Ӧ��http����β���/���磺http://img.domain.com/08cms/'));
		tabfooter('bmconfigs','�ύ','&nbsp; &nbsp;<input class="button" type="submit" name="ftpcheck" value="���FTP" onclick="var f=this.form,u=f.action;f.action=\'?entry=checks&action=ftpcheck\';f.target=\'ftpcheckiframe\';f.submit();f.target=\'_self\';f.action=u"><iframe name="ftpcheckiframe" style="display: none"></iframe>');
		a_guide('cfupload');
	}else{
		$mconfigsnew['dir_userfile'] = trim(strip_tags($mconfigsnew['dir_userfile']));
        if(isset($mconfigsnew['atm_smallsite']))
        {
    		$mconfigsnew['atm_smallsite'] = strtolower(trim($mconfigsnew['atm_smallsite']));
    		$mconfigsnew['atm_smallsite'] .= !preg_match("#/$#",$mconfigsnew['atm_smallsite']) ? '/' : '';
    		$mconfigsnew['atm_smallsite'] = (!preg_match("#http://#i",$mconfigsnew['atm_smallsite']) || preg_match('#'.$hosturl.'#i',$mconfigsnew['atm_smallsite'])) ? '' : $mconfigsnew['atm_smallsite'];
        }
		$mconfigsnew['player_width'] = max(0,intval($mconfigsnew['player_width']));
		$mconfigsnew['player_height'] = max(0,intval($mconfigsnew['player_height']));
		$mconfigsnew['nouser_capacity'] = max(0,intval($mconfigsnew['nouser_capacity']));
		$mconfigsnew['nouser_exts'] = strtolower(trim($mconfigsnew['nouser_exts']));
		$mconfigsnew['ftp_host'] = trim(strip_tags($mconfigsnew['ftp_host']));
		$mconfigsnew['ftp_port'] = max(1,intval($mconfigsnew['ftp_port']));
		$mconfigsnew['ftp_user'] = trim(strip_tags($mconfigsnew['ftp_user']));
		if($mconfigsnew['ftp_password'] != $vftp_password){
			$mconfigsnew['ftp_password'] =  $mconfigsnew['ftp_password'] ? authcode($mconfigsnew['ftp_password'],'ENCODE',md5($authkey)) : '';
		}else $mconfigsnew['ftp_password'] = $mconfigs['ftp_password'];
		$mconfigsnew['ftp_timeout'] = max(0,intval($mconfigsnew['ftp_timeout']));
		$mconfigsnew['ftp_dir'] = trim(strip_tags($mconfigsnew['ftp_dir']));
		$mconfigsnew['other_ftp_dir'] = trim(strip_tags($mconfigsnew['other_ftp_dir']));
		$mconfigsnew['ftp_url'] = trim(strip_tags($mconfigsnew['ftp_url']));
		saveconfig('upload');
		adminlog('��վ����','�ϴ�����������');
		cls_message::show('��վ�������','?entry=mconfigs&action=cfupload');
	}
}elseif($action == 'cfmobmail'){
	backnav('mconfig','cfmobmail');
	if(!submitcheck('bmconfigs')){
		$modearr = array(1 => 'PHP��mail��������',2 => 'SOCKET ����SMTP������(֧�������֤)',3 => 'PHP��SMTP����(������Windows����,��֧�������֤)',);
		$delimiterarr = array(1 => 'CRLF (Windows ����)',2 => 'LF (Unix/Linux ����)',3 => 'CR (Mac ����)',);
		tabheader('Email����','cfmail','?entry=mconfigs&action=cfmobmail&deal=mail');
		echo "<tr class=\"txt\"><td class=\"txt txtright fB borderright\">Email���ͷ�ʽ</td>\n".
		"<td class=\"txtL\">\n".
		"<input class=\"radio\" type=\"radio\" name=\"mconfigsnew[mail_mode]\" value=\"1\" onclick=\"\$id('mail_mod1').style.display = 'none';\$id('mail_mod2').style.display = 'none';\"".($mconfigs['mail_mode'] <= 1 ? ' checked' : '').">PHP��mail��������<br>\n".
		"<input class=\"radio\" type=\"radio\" name=\"mconfigsnew[mail_mode]\" value=\"2\" onclick=\"\$id('mail_mod1').style.display = '';\$id('mail_mod2').style.display = '';\"".($mconfigs['mail_mode'] == 2 ? ' checked' : '').">SOCKET ����SMTP������(֧�������֤)<br>\n".
		"<input class=\"radio\" type=\"radio\" name=\"mconfigsnew[mail_mode]\" value=\"3\" onclick=\"\$id('mail_mod1').style.display = '';\$id('mail_mod2').style.display = 'none';\"".($mconfigs['mail_mode'] == 3 ? ' checked' : '').">PHP��SMTP����(������Windows����,��֧�������֤)<br>\n".
		"</td></tr>\n";
		echo "<tbody id=\"mail_mod1\" style=\"display:".($mconfigs['mail_mode'] > 1 ? '' : 'none')."\">";
		trbasic('SMTP ������','mconfigsnew[mail_smtp]',$mconfigs['mail_smtp']);
		trbasic('SMTP �˿�','mconfigsnew[mail_port]',$mconfigs['mail_port']);
		echo "</tbody>";
		echo "<tbody id=\"mail_mod2\" style=\"display:".($mconfigs['mail_mode'] == 2 ? '' : 'none')."\">";
		trbasic('SMTP Ҫ�������֤','mconfigsnew[mail_auth]',$mconfigs['mail_auth'],'radio');
		trbasic('�������ʼ���ַ','mconfigsnew[mail_from]',$mconfigs['mail_from']);
		trbasic('SMTP �����֤�ʻ�','mconfigsnew[mail_user]',$mconfigs['mail_user']);
		trbasic('SMTP �����֤����','mconfigsnew[mail_pwd]',$mconfigs['mail_pwd'],'password');
		echo "</tbody>";
		trbasic('�ʼ�ͷ�ķָ���','mconfigsnew[mail_delimiter]',makeoption($delimiterarr,$mconfigs['mail_delimiter']),'select');
		trbasic('�����ʼ����͵ĳ�����Ϣ','mconfigsnew[mail_silent]',$mconfigs['mail_silent'],'radio');
		trbasic('�����ʼ����ŵ�ַ','mconfigsnew[mail_to]');
		trbasic('�����ʼ�ǩ��','mconfigsnew[mail_sign]');

		tabfooter();
		echo '<input class="button" type="submit" name="bmconfigs" value="�ύ">&nbsp; &nbsp;
		<input class="button" type="button" name="mailcheck" value="�ʼ�����" onclick="var f=this.form,u=f.action;f.action=\'?entry=checks&action=mailcheck\';f.target=\'mailcheckiframe\';f.submit();f.target=\'_self\';f.action=u"><iframe name="mailcheckiframe" style="display: none"></iframe>
		</form>';

		$provides = array('�������','����ͨ��');
		tabheader("400�绰����", 'webcall', "?entry=$entry&action=$action&deal=webcall");
		trbasic('��վ�ṩ400�ܻ�', 'mconfigsnew[webcall_enable]', $mconfigs['webcall_enable'], 'radio');
		trbasic('Ĭ������','webcall_default', makeoption($provides),'select',array('addstr'=>' &nbsp; &nbsp;<a id="webcall_setdefault" href="javascript:void(0)">�ָ�Ĭ��ֵ</a> &nbsp; &nbsp;<a id="webcall_apply_url" href="http://www.port400.com/" target="_blank">������վ�ܻ�</a>'));
		trbasic('400�绰�ṩ��','mconfigsnew[webcall_provide]', $mconfigs['webcall_provide']);
		trbasic('400�ܻ�����','mconfigsnew[webcall_big]', $mconfigs['webcall_big']);
		trbasic('400�ֻ���������','mconfigsnew[webcall_small_admin]', @$mconfigs['webcall_small_admin'],'text',array('w'=>60));
		setPermBar('���»�Ա��������400', 'mconfigsnew[webcallpmid]', @$mconfigs['webcallpmid'], 'other', array(0=>'ȫ��������'), '');
        tabfooter('bmconfigs');
		echo <<<EOT
<!--?>-->
<script type="text/javascript">
	var url = Array('http://www.port400.com/','http://www.web4008.com/');
	var admin = Array('http://customer.port400.com/Menu/Login.aspx','');

	var webcall_default = document.getElementById("webcall_default");
	webcall_default.onchange = function(){
		document.getElementById("webcall_apply_url").href = url[webcall_default.value];
	}

	var webcall_setdefault = document.getElementById("webcall_setdefault");
	webcall_setdefault.onclick = function(){
		document.getElementById("mconfigsnew[webcall_small_admin]").value = admin[webcall_default.value];
		document.getElementById("mconfigsnew[webcall_provide]").value = webcall_default.options[webcall_default.selectedIndex].text;
	}
</script>
EOT;
#<?
		a_guide('cfmail');
	}else{
		if($deal == 'mail'){
			$mconfigsnew['mail_smtp'] = trim($mconfigsnew['mail_smtp']);
			$mconfigsnew['mail_port'] = trim($mconfigsnew['mail_port']);
			$mconfigsnew['mail_from'] = trim($mconfigsnew['mail_from']);
			$mconfigsnew['mail_user'] = trim($mconfigsnew['mail_user']);
			$mconfigsnew['mail_pwd'] = trim($mconfigsnew['mail_pwd']);
			unset($mconfigsnew['mail_sign'],$mconfigsnew['mail_to']);
			$str = '�ʼ�����';
		}elseif($deal == 'mobmail'){
			//$str = '�ֻ�����';
		}elseif($deal == 'webcall'){
			$str = '400�绰����';
		}
		saveconfig('mail');
		adminlog($str);
		cls_message::show($str.'���','?entry=mconfigs&action=cfmobmail');
	}
}elseif($action == 'other_site_connect'){
	backnav('mconfig','other_site_connect');
	if(!submitcheck('bmconfigs')){
	    /**
         * SESSION����Ѿ����ڷŵ�memcache��ʱ����Ҫ���������������
         * ���������ѡ��ʱ����ѡ�����ݿ���SESSIONʱ��������һ�����ݱ�:
         *
         * CREATE TABLE `cms_cross_site_session` (
         *     `session_id` varchar(255) binary NOT NULL default '',
         *     `session_expires` int(10) unsigned NOT NULL default '0',
         *     `session_data` text,
         *     PRIMARY KEY  (`session_id`)
         * ) ENGINE=MyISAM;
         */
	    if ( strtolower(@ini_get('session.save_handler')) != 'memcache' )
        {
            tabheader('������������','publicsetting','?entry=mconfigs&action=other_site_connect');
    		trbasic('���ÿ�վSESSION','mconfigsnew[user_session]',@$mconfigs['user_session'],'radio',array('guide'=>'ע�⣺�����վ�����ڶ�������������á�'));
    		tabfooter();
            $memcache_flag = false;
        }
        else
        {
        	$memcache_flag = true;            
        }

	    // QQ��¼����
		tabheader('QQ��½��������<span style="color:red">��ע�������õ�¼��ʽ�����ȿ���PHP��CURL��OPENSSL��չ��</span>','qqconnect','?entry=mconfigs&action=other_site_connect');
		trbasic('QQ��½','',makeradio('mconfigsnew[qq_closed]',array(0=>'����',1=>'�ر�'),empty($mconfigs['qq_closed']) ? 0 : 1),'');
	#	trbasic('�����󶨹���','mconfigsnew[qq_bind_enabled]',@$mconfigs['qq_bind_enabled'],'radio',array('guide'=>'����APP IDʱ���ȹرգ�ͨ�����ٿ���'));
		trbasic('APPID','mconfigsnew[qq_appid]',@$mconfigs['qq_appid'],'text',array('guide'=>'û��APPIDô��<a style="color:red" href="http://connect.qq.com/manage/" target="_blank" >�������</a>'));
		trbasic('APPKEY','mconfigsnew[qq_appkey]',@$mconfigs['qq_appkey'],'text',array('guide'=>'û��APPKEYô��<a style="color:red" href="http://connect.qq.com/manage/" target="_blank" >�������</a>','w'=>50));
		trbasic('��¼���Ա������ʾ','',makeradio('mconfigsnew[qq_nickname]',array(0=>'��ʾQQ�ǳ�',1=>'��ʾ��ϵͳ��Ա����'),empty($mconfigs['qq_nickname']) ? 0 : 1),'', array('guide'=>'ע����ѡ��ֻΪ����APP KEYʱʹ�ã��������ͨ������ѡ�� ��ʾ��ϵͳ��Ա����'));
		tabfooter();

        // ����΢����¼����
		tabheader('����΢����½��������','','?entry=mconfigs&action=sinaconnect');
		trbasic('����΢����½','',makeradio('mconfigsnew[sina_closed]',array(0=>'����',1=>'�ر�'),empty($mconfigs['sina_closed']) ? 0 : 1),'');
	#	trbasic('�����󶨹���','mconfigsnew[sina_bind_enabled]',@$mconfigs['sina_bind_enabled'],'radio',array('guide'=>'����App Keyʱ���ȹرգ�ͨ�����ٿ���'));
		trbasic('App Key','mconfigsnew[sina_appid]',@$mconfigs['sina_appid'],'text',array('guide'=>'û��App Keyô��<a style="color:red" href="http://open.weibo.com/connect/" target="_blank" >�������</a>'));
		trbasic('App Secret','mconfigsnew[sina_appkey]',@$mconfigs['sina_appkey'],'text',array('guide'=>'û��App Secretô��<a style="color:red" href="http://open.weibo.com/connect/" target="_blank" >�������</a>','w'=>50));
        $memcache_flag && trhidden('mconfigsnew[user_session]', 1);
		tabfooter('bmconfigs');

		a_guide('qqconnect');
	}else{
	    foreach ( array('qq_appid', 'qq_appkey', 'sina_appid', 'sina_appkey') as $key ) 
        {
            isset($mconfigsnew[$key]) && ($mconfigsnew[$key] = trim($mconfigsnew[$key]));
        }
		saveconfig('other_site_connect');
		adminlog('��վ����','��ݵ�½����');
		cls_message::show('�޸Ŀ�ݵ�½���óɹ�!','?entry=mconfigs&action=other_site_connect');
	}
}
