<?php
include_once dirname(dirname(__FILE__)).'/include/general.inc.php';
include_once M_ROOT.'include/adminm.fun.php';
$mobfid = empty($mobfid) ? 0 : $mobfid; //�ֻ���Ķ���ҳid
if($mobfid) define('IN_MOBILE', TRUE);
$forward = empty($forward) ? M_REFERER : $forward;
$forwardstr = 'forward='.rawurlencode($forward);
empty($action) && $action ='';
if($action == 'getpwd' && !empty($mid) && !empty($id)){
	$cmember = $db->fetch_one("SELECT m.mid,m.mname,m.email,s.confirmstr FROM {$tblprefix}members m,{$tblprefix}members_sub s WHERE m.mid='$mid' AND s.mid=m.mid");
	if(!$cmember || !$cmember['confirmstr']) cls_message::show('��Ч����');
	list($dateline,$deal,$confirmid) = explode("\t",$cmember['confirmstr']);
	if($dateline < $timestamp - 86400 * 3 || $deal != 1 || $confirmid != $id){
		cls_message::show('��Ч����');
	}
	if(!submitcheck('bgetpwd')){
		_header('��Ա�һ�����','curbox');
		tabheader('��Ա��������','getpwd',"?action=getpwd&mid=$mid&id=$id&mobfid=$mobfid",2,0,1);
		trbasic('��Ա����','',$cmember['mname'],'');
		trbasic('<font color="red">*</font>����������','npassword','','password', array('validate' => makesubmitstr('npassword',1,0,3,15)));
		trbasic('<font color="red">*</font>��������������','npassword2','','password', array('validate' => makesubmitstr('npassword2',1,0,3,15)));
		tr_regcode('register');
		tabfooter('bgetpwd');
	}else{ 
		if(!regcode_pass('register',empty($regcode) ? '' : trim($regcode))) cls_message::show('��֤�����');
		$npassword = trim($npassword);
		$npassword2 = trim($npassword2);
		if($npassword != $npassword2) cls_message::show('�����������벻һ��');
		if(!$npassword || strlen($npassword) > 15 || $npassword != addslashes($npassword)){
			cls_message::show('��Ա���벻�Ϲ淶');
		}
		if($re = cls_ucenter::edit($cmember['mname'],$npassword)) cls_message::show($re);
        
        # ͬ������WINDID�û�����
        cls_WindID_Send::getInstance()->editUserInfo( $mid,array('password' => $npassword) );# ͬ������WINDID����
        
		$npassword = _08_Encryption::password($npassword); 
		$db->query("UPDATE {$tblprefix}members SET password='$npassword' WHERE mid='$mid'");
		$db->query("UPDATE {$tblprefix}members_sub SET confirmstr='' WHERE mid='$mid'");
		cls_message::show('��Ա�һ�����ɹ���',"{$cms_abs}login.php");
	}
}else{
	$sms = new cls_sms();
	$phonefields = empty($phonefields) ? authcode('phone,tel,lxdh','','08cms') : $phonefields;

	if(!submitcheck('blostpwd')){ 
		_header('��Ա�һ�����','curbox');
		//$phonefields = authcode('phone,tel,lxdh', '', '08cms'); ��Ϊǰ̨�ṩ�ò���
        $way = empty($way) ? 'email' : $way;
		if ($sms->isClosed()){
            url_nav(array('email'=>array("���������һ�","?way=email&phonefields=$phonefields")),$way);
        }else{
            url_nav(array('phone'=>array("�ֻ������һ�","?way=phone&phonefields=$phonefields"),'email'=>array("���������һ�","?way=email&phonefields=$phonefields")),$way);
        }

		if($way == 'phone'){
			tabheader('��Ա�һ�����','lostpwd',"?way=$way&phonefields=$phonefields&$forwardstr",2,0,1);
			trbasic('<font color="red">*</font>��Ա����','mname', '', 'text', array('validate' => makesubmitstr('mname',1,0,0,15)));
$ajaxurl = $cms_abs._08_Http_Request::uri2MVC("ajax=mobcode&val=%1");
			echo <<<EOT
					<tr><td width="150px" class="item1"><b><font color="red">*</font>�ֻ�����</b></td>
					<td class="item2">
					<input type="text" size="25" id="lxdh" name="phone" value="" warn="����ȫ��������д������С��ͨ�������" rule="text" must="1" mode="0" regx="/^\s*(?:[48]00-?\d{3}-?\d{4}|(?:00?[1-9]\d{1,2}-?)?[2-8]\d{6,7}|1[358]\d{9})\s*$/" min="0" max="15"><div class="validator_message init" style="display: none;">OK</div>
					<a id="tel_code" href="javascript:" onclick="sendCerCode('lxdh','register','tel_code');">����ѻ�ȡ��֤�롿</a>
					<a id="tel_code_rep" style="margin-left:39px;color:#CCC; display:none"><span id="tel_code_rep_in">60</span>������»�ȡ</a>
					</td></tr>
					<tr><td width="150px" class="item1"><b><font color="red">*</font>ȷ �� ��</b></td>
					<td class="item2">
					<input type="text" size="6" id="msgcode" name="msgcode" value="" warn="������6λ��֤��" rule="text" regx="/^\s*\d{6}\s*$/" must="1" rev="ȷ����" pass="OK" mode="0" min="0" max="15"><div class="validator_message init" style="display: none;">OK</div>
					��1���Ӻ���δ�յ�����,���ط���
					</td></tr>
					<script type="text/javascript">
						window._08cms_validator && _08cms_validator.init("ajax","msgcode",{ url: '$ajaxurl' });
					</script>
					<script type='text/javascript' src='{$cms_abs}include/sms/cer_code.js'></script>

EOT;
		}else{
			tabheader('��Ա�һ�����','lostpwd',"?way=$way&phonefields=$phonefields&$forwardstr",2,0,1);
			trbasic('��Ա����','mname', '', 'text', array('validate' => makesubmitstr('mname66',1,0,0,15)));
			trbasic('��ԱEmail','email', '', 'text', array('validate' => makesubmitstr('email',1,'email',0,80)));
			tr_regcode('register');
		}
		tabfooter('blostpwd');
	}else{
		if ($way == 'phone' && !$sms->isClosed()) {
			@list($inittime, $initcode) = maddslashes(explode("\t", authcode($m_cookie['08cms_msgcode'],'DECODE')),1);
			if($timestamp - $inittime > 1800 || $initcode != $msgcode) cls_message::show('�ֻ�ȷ��������', M_REFERER);
			$mname = trim($mname);
			$phone = trim($phone);

			$phonefields = authcode($phonefields, 'DECODE','08cms');
            $phonefields = explode(',',$phonefields);
			$_len = cls_string::CharCount($mname);// ����ϵͳ�������ǰϵͳ��ͬʱ, ����ת��Ϊ��ǰϵͳ����
			if ($_len < 3 || $_len > 15) {
				cls_message::show('��Ա���Ƴ��Ȳ��淶');
			}
			$guestexp = '\xA1\xA1|^Guest|^\xD3\xCE\xBF\xCD|\xB9\x43\xAB\xC8';
			if (preg_match("/^\s*$|^c:\\con\\con$|[%,\*\"\s\t\<\>\&]|$guestexp/is", $mname)) {
				cls_message::show('��Ա���Ʋ��Ϲ淶');
			}

			$actuser = new cls_userinfo;
			if(!$actuser->activeuserbyname($mname)){
				cls_message::show('ָ����Ա�����ڻ��ֻ��������');
			}else{
				$actuser->detail_data();
				$info = $actuser->info;
                foreach($phonefields as $v){
					if (in_array($v,$info)){
						if(empty($info[$v])) cls_message::show('ָ����Ա�����ڻ��ֻ��������!');
					}
				}
			}
			if ($actuser->isadmin()) {
				cls_message::show('����Ա����ʹ�ô˹��ܣ�');
			}
			$mid = $info['mid'];
			unset($actuser);
			$confirmid = cls_string::Random(6);
			$confirmstr = "$timestamp\t1\t$confirmid";
			//var_dump($cmember);die;
			$db->query("UPDATE {$tblprefix}members_sub SET confirmstr='$confirmstr' WHERE mid='$mid'");
			$forward = "{$cms_abs}tools/lostpwd.php?action=getpwd&mid=$mid&id=$confirmid";
			//cls_message::show('��֤��ͨ��!',$forward);
			header("Location: $forward");
		} else {
			if (!regcode_pass('register', empty($regcode) ? '' : trim($regcode))) {
				cls_message::show('��֤�����');
			}
			$mname = trim($mname);
			$email = trim($email);
			$_len = cls_string::CharCount($mname);// ����ϵͳ�������ǰϵͳ��ͬʱ, ����ת��Ϊ��ǰϵͳ����
			if ($_len < 3 || $_len > 15) {
				cls_message::show('��Ա���Ƴ��Ȳ��淶');
			}
			$guestexp = '\xA1\xA1|^Guest|^\xD3\xCE\xBF\xCD|\xB9\x43\xAB\xC8';
			if (preg_match("/^\s*$|^c:\\con\\con$|[%,\*\"\s\t\<\>\&]|$guestexp/is", $mname)) {
				cls_message::show('��Ա���Ʋ��Ϲ淶');
			}
			if (!$email || !cls_string::isEmail($email)) {
				cls_message::show('��ԱEmail���淶');
			}
			$cmember = $db->fetch_one(
				"SELECT mid,mname,email FROM {$tblprefix}members WHERE mname='$mname' AND email='$email'"
			);
			if (!$cmember) {
				cls_message::show('ָ����Ա�����ڻ�Email����');
			}
			$actuser = new cls_userinfo;
			$actuser->activeuser($cmember['mid']);
			if ($actuser->isadmin()) {
				cls_message::show('����Ա����ʹ�ô˹��ܣ�');
			}
			unset($actuser);
			$confirmid = cls_string::Random(6);
			$confirmstr = "$timestamp\t1\t$confirmid";
			$db->query("UPDATE {$tblprefix}members_sub SET confirmstr='$confirmstr' WHERE mid='$cmember[mid]'");
			$url = "{$cms_abs}" . (empty($mobfid) ? "tools/lostpwd.php?" : "info.php?fid={$mobfid}&") . "action=getpwd&mid=$cmember[mid]&id=$confirmid";
			mailto(
				"$mname <$email>",
				'member_getpwd_subject',
				'member_getpwd_content',
				array('mid' => $cmember['mid'], 'mname' => $mname, 'url' => $url, 'onlineip' => $onlineip)
			);
			cls_message::show('ȡ������ķ����ɹ����͵����ĵ�������!', $forward);

		}
	}
}
