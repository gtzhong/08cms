<?php
defined('M_UPSEN') || define('M_UPSEN', TRUE);
defined('NOROBOT') || define('NOROBOT', TRUE);
include_once dirname(__FILE__).'/include/general.inc.php';
if($ex = exentry('register')){include($ex);mexit();}
//����������һ����չʱ��������һ��ȥ�����滻����
//$mobiledir = cls_env::mconfig('mobiledir'); //��������ȥ�˾�Ҫ��һ��
_08_FilesystemFile::filterFileParam($mobiledir); 
$ismobiledir = defined('IN_MOBILE') ? "$mobiledir/" : ''; //echo $ismobiledir; //�����ֻ����ַ
foreach(array('mchannels','catalogs','cotypes','mtconfigs',) as $k) $$k = cls_cache::Read($k);

$forward = empty($forward) ? M_REFERER : $forward;

cls_env::CheckSiteClosed();//�ر�վ��ʱ���ܹر�ajaxУ��
if(empty($forward))$forward = $cms_abs;
$forwardstr = "forward=".urlencode($forward);
$curuser->info['mid'] && cls_message::show('�벻Ҫ�ظ�ע�� [<a href="login.php?action=logout">�˳�</a>]', '');
$registerclosed && cls_message::show(empty($regclosedreason) ? '��վ��ʱ�ر�ע���»�Ա��' : mnl2br($regclosedreason));
$mchid = empty($mchid) ? 1 : max(1,intval($mchid));
if(!($mchannel = cls_cache::Read('mchannel',$mchid))) cls_message::show('��ָ����ȷ�Ļ�Ա����');
$mfields = cls_cache::Read('mfields',$mchid);
$grouptypes = cls_cache::Read('grouptypes');
$sms = new cls_sms();

if(!submitcheck('register')){
	if(defined('IN_MOBILE')){ //�ֻ���ģ��
		$tplname = cls_tpl::SpecialTplname('register',defined('IN_MOBILE'));
	}else{ //��ҳ��ģ��
		$tplname = cls_tpl::CommonTplname('member',$mchid,'addtpl');
	} //$tplname = '';
	if(!$tplname){
		include_once M_ROOT.'include/adminm.fun.php';
		_header('ע��');
		$mchannel = cls_cache::Read('mchannel',$mchid);
	
		tabheader('ע���»�Ա','cmsregister',"?mchid=$mchid&forward=".rawurlencode($forward),2,1,1);
		$muststr = '<span style="color:red">*</span>';
		trbasic($muststr.'��Ա����','mname', '', 'text', array('validate' => ' rule="text" must="1" min="3" max="15"'));
		trbasic($muststr.'����','password','','password', array('validate' => ' rule="text" must="1"'));
		trbasic($muststr.'�ظ�����','password2','','password', array('validate' => ' rule="comp" must="1" vid="password"'));
		trbasic($muststr.'E-mail','email', '', 'text', array('validate' => ' rule="email" must="1" rev="E-mail"'));
		tr_regcode('register');
		echo '<script type="text/javascript">window._08cms_validator && _08cms_validator.attribute("mname","warn","�û���ӦΪ3-15���ֽڣ�").init("ajax","mname",{cache:1,url:"'.$cms_abs . _08_Http_Request::uri2MVC('ajax=Check_Member_Info_Base&filed=mname&val=%1').'"}).attribute("password","warn","����ջ�����Ƿ��ַ���������ȫ0�����룩��").attribute("password2","init","�����������룡").attribute("password2","comp","������ȷ�����룡").attribute("password2","warn","������������벻һ�£�").init("ajax","email",{cache:1,url:"'.$cms_abs . _08_Http_Request::uri2MVC('ajax=Check_Member_Info_Base&filed=email&val=%1').'"});</script>';
		foreach($grouptypes as $k => $v){
			if(!$v['mode'] && !in_array($mchid,explode(',',$v['mchids']))){
				trbasic($v['cname'],'grouptype'.$k,makeoption(ugidsarr($k,$mchid)),'select');
			}
		}
		$a_field = new cls_field;
		foreach($mfields as $k => $v){
			if(in_array($v['datatype'],array('image','images','flash','flashs','media','medias','file','files')))continue;
			if($v['available'] && !$v['issystem']){
				$a_field->init($v);
				$a_field->isadd = 1;
				$a_field->trfield();
			}
		}
		tabfooter('register','ע��');
		_footer();
	}else{//���Ƶ�ģ��

		$html = cls_SpecialPage::Create(
			array(
				'tplname' => $tplname,
				'_da' => array('mchid' => $mchid,'forward' => rawurlencode($forward),),
				'LoadAdv' => true,
				'NodeMode' => defined('IN_MOBILE'),
			)
		);
		exit($html);
	}
}else{
    if($sms->smsEnable('register')){ //??�ֻ����ų�
        $msgcode = cls_env::GetG('msgcode');
		$smstelfield = cls_env::GetG('smstelfield'); 
		$smstelval = cls_env::GetG($smstelfield); 
		if(!$pass=smscode_pass('register',$msgcode,$smstelval)) cls_message::show('�ֻ�ȷ��������', M_REFERER);
		//��Ա��֤-��Ա�ֻ���֤:ǿ����֤(v)
		//���-�Զ����(x)
    }else{
        //��֤��
        if(!regcode_pass('register',empty($regcode) ? '' : trim($regcode))) cls_message::show('��֤���������',M_REFERER);
		$smstelfield = '';
		$smstelval = '';
    }
	//Ԥ�����Ա�ʺš����롢Email
	foreach(array('mname','password','email') as $key){
		$re = cls_userinfo::CheckSysField(@$$key,$key,'add');
		if($re['error']){
			cls_message::show($re['error'], M_REFERER);
		}else $$key = $re['value'];
	}
	$password2 = trim($password2);
	if($password != $password2) cls_message::show('�����������벻һ��',M_REFERER);
	
	//UC��Աͬ��ע��
	$uc_uid = cls_ucenter::register($mname,$password,$email,TRUE);
      
      # ͬ��ע��ͨ��֤
	$pw_uid = cls_WindID_Send::getInstance()->synRegister( $mname, $password, $email, $onlineip );
      
	$a_field = new cls_field;
	foreach($mfields as $k => $v){
		if(in_array($v['datatype'],array('image','images','flash','flashs','media','medias','file','files')))continue;
		if(!$v['issystem'] && isset($$k)){
			$a_field->init($v);
			$$k = $a_field->deal('','message',M_REFERER);
		}
	}
	unset($a_field);
	
	$newuser = new cls_userinfo;
	if($mid = $newuser->useradd($mname,_08_Encryption::password($password),$email,$mchid)){
		//��Ա���������ֶ�ѡ��Ļ�Ա������
		foreach($grouptypes as $k => $v){
			if(!$v['mode'] && isset(${"grouptype$k"})){
				$newuser->updatefield("grouptype$k",${"grouptype$k"});
			}
		}
		foreach($mfields as $k => $v){
			if(in_array($v['datatype'],array('image','images','flash','flashs','media','medias','file','files')))continue;
			if(!$v['issystem'] && isset($$k)){
				$newuser->updatefield($k,$$k,$v['tbl']);
				if($arr = multi_val_arr($$k,$v)) foreach($arr as $x => $y) $newuser->updatefield($k.'_'.$x,$y,$v['tbl']);
			}
		}
		//reg_ex1(); //��չ;
		$autocheck = $mchannel['autocheck'];
          
          # ����ϵͳע���û��ɹ��󱣴�PW�û�ID����ϵͳ
          empty($pw_uid) || $newuser->updatefield(cls_Windid_Message::PW_UID, $pw_uid);
          # ����ϵͳע���û��ɹ��󱣴�UC�û�ID����ϵͳ
          empty($uc_uid) || $newuser->updatefield(cls_ucenter::UC_UID, $uc_uid);
		
		$newuser->check($autocheck);
		if($autocheck == 1){
			$newuser->OneLoginRecord();
		}elseif($autocheck == 2){
			cls_userinfo::SendActiveEmail($newuser->info);
		}
		$newuser->updatedb();
		if($autocheck == 1) $newuser->autopush(); //�Զ�����
		if($smstelfield && $smstelval){ //����ע���ֻ�������֤��(���Զ���֤:��Ա��֤-�ֻ���֤)
			$newuser->automcert($smstelfield,$smstelval);
		}
		if(!$forward || preg_match('/\bregister\.php(\?|#|$)/i', $forward)) $forward = $cms_abs.$ismobiledir;
		if($autocheck == 1 && !defined('IN_MOBILE')) $forward = $cms_abs.$ismobiledir.'adminm.php'; //��ҳ����л�Ա����		
		cls_message::show(!$autocheck ? '�û��ȴ����' : ($autocheck == 2 ? '��Ա�����ʼ��ѷ��͵��������䣬��������伤��' : '��Աע��ɹ���'),$forward);
	}else cls_message::show('��Աע��ʧ�ܣ�������ע�ᡣ',"?mchid=$mchid&forward=".rawurlencode($forward));
}
