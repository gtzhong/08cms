<?php
(!defined('M_COM') || !defined('M_ADMIN')) && exit('No Permission');
aheader();
if($re = $curuser->NoBackFunc('channel')) cls_message::show($re);
include_once M_ROOT."include/fields.fun.php";
foreach(array('rprojects','commus','cotypes','permissions','splitbls',) as $k) $$k = cls_cache::Read($k);
if($ex = exentry('channels')){
	include($ex);
	entryfooter();
}
$channels = cls_channel::InitialInfoArray();
if($action == 'channeledit'){
	backnav('channel','channel');
	echo _08_HTML::Title('�ĵ�ģ�͹���');
	if(!submitcheck('bchanneledit')){
		tabheader("�ĵ�ģ�͹���".modpro(" &nbsp; &nbsp;>><a href=\"?entry=$entry&action=channeladd\" onclick=\"return floatwin('open_channeledit',this)\">���</a>"),'channeledit',"?entry=$entry&action=$action",'10');
		trcategory(array('ID','����','ģ������|L','��ע|L','����',modpro('ɾ��'),'�ֶ�',modpro('��ϵ'),'����','��չ',));
		foreach($channels as $k => $channel){
			echo "<tr class=\"txt\">".
				"<td class=\"txtC w30\">$k</td>\n".
				"<td class=\"txtC w30\"><input class=\"checkbox\" type=\"checkbox\" name=\"channelnew[$k][available]\" value=\"1\"".($channel['available'] ? " checked" : "")."></td>\n".
				"<td class=\"txtL\"><input type=\"text\" size=\"15\" maxlength=\"30\" name=\"channelnew[$k][cname]\" value=\"$channel[cname]\"></td>\n".
				"<td class=\"txtL\"><input type=\"text\" size=\"30\" maxlength=\"30\" name=\"channelnew[$k][remark]\" value=\"$channel[remark]\"></td>\n".
				"<td class=\"txtC w40\"><input type=\"text\" size=\"4\" maxlength=\"4\" name=\"channelnew[$k][vieworder]\" value=\"$channel[vieworder]\"></td>\n".
				modpro("<td class=\"txtC w30\"><a onclick=\"return deltip(this,$no_deepmode)\" href=\"?entry=$entry&action=channeldel&chid=$k\">ɾ��</a></td>\n").
				"<td class=\"txtC w30\"><a href=\"?entry=$entry&action=channelfields&chid=$k\" onclick=\"return floatwin('open_channeledit',this)\">�ֶ�</a></td>\n".
				modpro("<td class=\"txtC w30\"><a href=\"?entry=$entry&action=channelcotypes&chid=$k\" onclick=\"return floatwin('open_channeledit',this)\">��ϵ</a></td>\n").
				"<td class=\"txtC w30\"><a href=\"?entry=$entry&action=channeldetail&chid=$k\" onclick=\"return floatwin('open_channeledit',this)\">����</a></td>\n".
				"<td class=\"txtC w30\"><a href=\"?entry=$entry&action=channeladv&chid=$k\" onclick=\"return floatwin('open_channeledit',this)\">�߼�</a></td>\n".
				"</tr>\n";
		}
		tabfooter('bchanneledit','�޸�');
		a_guide('channeledit');
	}else{
		if(isset($channelnew)){
			foreach($channelnew as $k => $v){
				$v['available'] = isset($v['available']) ? $v['available'] : 0;
				$v['cname'] = trim(strip_tags($v['cname']));
				$v['cname'] = $v['cname'] ? $v['cname'] : $channels[$k]['cname'];
				$v['remark'] = trim(strip_tags($v['remark']));
				$v['vieworder'] = max(0,intval($v['vieworder']));
				$db->query("UPDATE {$tblprefix}channels SET cname='$v[cname]',remark='$v[remark]',vieworder='$v[vieworder]',available='$v[available]' WHERE chid='$k'");
			}
			adminlog('�༭�ĵ�ģ���б�');
			cls_CacheFile::Update('channels');
		}
		cls_message::show('�ĵ�ģ�ͱ༭���',"?entry=$entry&action=$action");
	}
}elseif($action == 'channeladd'){
	echo _08_HTML::Title('����ĵ�ģ��');
	modpro() || cls_message::show('����ϵ��ʼ�˿��Ŷ��ο���ģʽ');
	deep_allow($no_deepmode);
	if(!submitcheck('bsubmit')){
		$submitstr = '';
		tabheader('����ĵ�ģ��',$action,"?entry=$entry&action=$action",2,0,1);
		trbasic('ģ������','fmdata[cname]','','text',array('validate'=>makesubmitstr('fmdata[cname]',1,0,3,30)));
		trbasic('ָ���ĵ�����','fmdata[stid]',makeoption(array(0 => '��������') + stidsarr()),'select',array('guide' => '���ú󲻿ɱ���������ѡ�������ݿ����ĵ������ʽ��"archives*"(*Ϊ����ID)��'));
		trbasic('������������','fmdata[stname]','','text',array('validate'=>makesubmitstr('fmdate[stname]',0,0,3,30),'guide' => '��������Ĭ��Ϊģ�����ơ�'));
		trbasic('��ע˵��','fmdata[remark]','','text',array('w'=>50));
		tabfooter('bsubmit','���');
		a_guide('channeladd');
	}else{
		!($fmdata['cname'] = trim(strip_tags($fmdata['cname']))) && cls_message::show('�������ĵ�ģ������');
		$fmdata['remark'] = trim(strip_tags($fmdata['remark']));
		$stid = max(0,intval(@$fmdata['stid']));
		$fmdata['stname'] = trim(strip_tags($fmdata['stname']));
		$fmdata['stname'] || $fmdata['stname'] = $fmdata['cname'];
		$newstid = false;
		if(!$stid){
			$db->query("INSERT INTO {$tblprefix}splitbls SET stid = ".auto_insert_id('splitbls').",cname='$fmdata[stname]'");
			if(!($stid = $db->insert_id())) cls_message::show('�����ĵ������ɹ���');
			$newstid = true;
			$db->query("CREATE TABLE {$tblprefix}archives$stid LIKE {$tblprefix}init_archives");
			$db->query("ALTER TABLE {$tblprefix}archives$stid COMMENT='$fmdata[stname](�ĵ�)����'");
		}
		$db->query("INSERT INTO {$tblprefix}channels SET
				   	chid = ".auto_insert_id('channels').",
					cname='$fmdata[cname]',
					stid='$stid',
					remark='$fmdata[remark]'
					");
		if($chid = $db->insert_id()){
			$db->query("CREATE TABLE {$tblprefix}archives_$chid (
						aid mediumint(8) unsigned NOT NULL default '0',
						PRIMARY KEY (aid))".(mysql_get_server_info() > '4.1' ? " ENGINE=MYISAM DEFAULT CHARSET=$dbcharset" : " TYPE=MYISAM"));
			$db->query("UPDATE {$tblprefix}splitbls SET chids=CONCAT(chids,',$chid,') WHERE stid='$stid'");
			$db->query("ALTER TABLE {$tblprefix}archives_$chid COMMENT='$fmdata[stname](�ĵ�)ģ�ͱ�'");
			$arcinitfields = cls_cache::cacRead('arcinitfields','',1);
			foreach($arcinitfields as $kk => $vv){
				$sqlstr = "ename='$kk',`type`='a',tpid='$chid',iscommon=1,available=1,tbl='archives$stid'";
				foreach($vv as $k => $v) $sqlstr .= ",`$k`='".addslashes($v)."'";
				$db->query("INSERT INTO {$tblprefix}afields SET $sqlstr");
			}
			cls_CacheFile::Update('splitbls');
			cls_CacheFile::Update('channels');
			cls_CacheFile::Update('fields',$chid);
			adminlog('����ĵ�ģ��-'.$fmdata['cname']);
			cls_message::show('�ĵ�ģ����ӳɹ�����Դ�ģ�ͽ�����ϸ���á�',"?entry=$entry&action=channeldetail&chid=$chid");
		}else{
			$newstid && $db->query("DELETE FROM {$tblprefix}splitbls WHERE stid='$stid'");
			$db->query("DROP TABLE {$tblprefix}archives$stid");
			cls_message::show('�ĵ�ģ����Ӳ��ɹ���');
		}
	}

}elseif($action == 'channeldel' && $chid && isset($channels[$chid])) {
	$channel = $channels[$chid];
	echo _08_HTML::Title("ɾ���ĵ�ģ�� - ģ��ID��$chid");
	modpro() || cls_message::show('����ϵ��ʼ�˿��Ŷ��ο���ģʽ');
	deep_allow($no_deepmode);
	if(!submitcheck('confirm')){
		$message = "ɾ�����ָܻ���ȷ��ɾ����ѡ��Ŀ?<br><br>";
		$message .= "ȷ������>><a href=?entry=$entry&action=$action&chid=$chid&confirm=ok>ɾ��</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
		$message .= "��������>><a href=?entry=$entry&action=channeledit>����</a>";
		cls_message::show($message);
	}
	if($db->result_one("SELECT COUNT(*) FROM {$tblprefix}".atbl($channel['stid'],1)." WHERE chid='$chid'")){
		cls_message::show('ɾ����ģ�ͣ�����ɾ���������ģ����ص��ĵ���',"?entry=$entry&action=channeledit");
	}
	$db->query("DROP TABLE IF EXISTS {$tblprefix}archives_$chid",'SILENT');
	$db->query("DELETE FROM {$tblprefix}channels WHERE chid='$chid'",'SILENT');
	cls_fieldconfig::DeleteOneSourceFields('channel',$chid);

	//�����������û�в��ٱ�����ģ��ʹ��ʱ�����Զ�ɾ��
	$db->query("UPDATE {$tblprefix}splitbls SET chids=REPLACE(chids,',$chid,',',') WHERE stid='$channel[stid]'",'SILENT');
	if($db->result_one("SELECT chids FROM {$tblprefix}splitbls WHERE stid='$channel[stid]'")==','){
		$db->query("DROP TABLE IF EXISTS {$tblprefix}archives".$channel['stid']."",'SILENT');
		$db->query("DELETE FROM {$tblprefix}splitbls WHERE stid='$channel[stid]'",'SILENT');
	}

	//�����ػ���
	cls_CacheFile::Update('channels');
	cls_CacheFile::Update('splitbls');
	adminlog('ɾ���ĵ�ģ��-'.$channel['cname']);
	cls_message::show('ָ�����ĵ�ģ���ѳɹ�ɾ����',"?entry=$entry&action=channeledit");
}elseif($action == 'channeldetail' && $chid){
	!($channel = cls_channel::InitialOneInfo($chid)) && cls_message::show('ָ�����ĵ�ģ�Ͳ����ڡ�');
	echo _08_HTML::Title("�ĵ�ģ�ͻ������� - $channel[cname]");
	if(!submitcheck('bchanneldetail')){
		tabheader($channel['cname'].'-��������','channeldetail',"?entry=$entry&action=channeldetail&chid=$chid");
		setPermBar('���ݷ���Ȩ������', 'channelnew[apmid]', @$channel['apmid'] ,'aadd', 'open', 'ѡ��Ȩ�޷������򷽰�������Ļ�Ա���ܷ�����ģ���ĵ���');
        setPermBar('�Զ����Ȩ������', 'channelnew[autocheck]', @$channel['autocheck'], 'chk', 'check', 'ѡ��Ȩ�޷������򷽰�������Ļ�Ա�������ĵ����Զ���ˣ��������ֶ���ˡ�');
        trbasic('����ҳ�Զ����ɾ�̬','channelnew[autostatic]',$channel['autostatic'],'radio',array('guide' => 'ѡ���ǣ����ĵ���ӻ�༭��ɺ�,�Զ����¸��ĵ������ݾ�̬ҳ��'));        
		trbasic('�ر�����ҳ��̬�Զ�����','channelnew[noautostatic]',$channel['noautostatic'],'radio',array('guide' => '��̬�Զ����±Ƚ�ռ�÷�������Դ�������ҳ�����ѡ��'));
		$dmin = intval(@$channel['click_defmin']);
		$dmax = intval(@$channel['click_defmax']);
		$defclick = "<input type='text' size='8' id='channelnew[click_defmin]' name='channelnew[click_defmin]' value='$dmin' rule='int' must='0' regx='' min='' max='' rev=''> ";
		$defclick .= "<input type='text' size='8' id='channelnew[click_defmax]' name='channelnew[click_defmax]' value='$dmax' rule='int' must='0' regx='' min='' max='' rev=''>";
		trbasic('Ĭ�ϵ����','',$defclick,'',array('guide' => '����ĵ�ʱ��Ĭ�ϵ�Ĭ�ϵ������ϵͳ���ڴ����������������һ����ֵ��'));
		tabfooter('bchanneldetail');
	}else{
		$db->query("UPDATE {$tblprefix}channels SET
			apmid='$channelnew[apmid]',
			click_defmin='$channelnew[click_defmin]',
			click_defmax='$channelnew[click_defmax]',
			autocheck='$channelnew[autocheck]',
			autostatic='$channelnew[autostatic]',
			noautostatic='$channelnew[noautostatic]'
			WHERE chid='$chid'");
		cls_CacheFile::Update('channels');
		adminlog('�༭�ĵ�ģ��-'.$channel['cname']);
		cls_message::show('ģ�ͱ༭���!',axaction(6,"?entry=$entry&action=channeledit"));
	}
}elseif($action == 'channeladv' && $chid){
	!($channel = cls_channel::InitialOneInfo($chid)) && cls_message::show('ָ�����ĵ�ģ�Ͳ����ڡ�');
	echo _08_HTML::Title("�ĵ�ģ�͸߼���չ - $channel[cname]");
	if(@!include("exconfig/channel_$chid.php")){
		if(!submitcheck('bchanneldetail')){
			tabheader($channel['cname'].'-�߼���չ����','channeldetail',"?entry=$entry&action=channeladv&chid=$chid");
			trbasic('���ò�������'.($channel['cfgs0'] && !$channel['cfgs'] ? '<br>�����ʽ���������!' : ''),'channelnew[cfgs0]',empty($channel['cfgs']) ? (empty($channel['cfgs0']) ? '' : $channel['cfgs0']) : var_export($channel['cfgs'],TRUE),'textarea',array('w' => 500,'h' => 300,'guide'=>'��array()���룬����������Ҫ��php�淶'));
			trbasic('����˵��','channelnew[content]',$channel['content'],'textarea',array('w' => 500,'h' => 300,));
			tabfooter('bchanneldetail');
		}else{
			$channelnew['cfgs0'] = empty($channelnew['cfgs0']) ? '' : trim($channelnew['cfgs0']);
			$channelnew['cfgs'] = varexp2arr($channelnew['cfgs0']);
			$channelnew['content'] = empty($channelnew['content']) ? '' : trim($channelnew['content']);
			$channelnew['cfgs'] = !empty($channelnew['cfgs']) ? addslashes(var_export($channelnew['cfgs'],TRUE)) : '';
			$db->query("UPDATE {$tblprefix}channels SET
						content='$channelnew[content]',
						cfgs0='$channelnew[cfgs0]',
						cfgs='$channelnew[cfgs]'
						WHERE chid='$chid'");
			cls_CacheFile::Update('channels');
			adminlog('�༭�ĵ�ģ��-'.$channel['cname']);
			cls_message::show('ģ�ͱ༭���!',axaction(6,"?entry=$entry&action=channeledit"));
		}
	}
}elseif($action == 'channelcotypes' && $chid){//ֻ�������ݱ����Ƿ��и��ֶ�
	!($channel = cls_channel::InitialOneInfo($chid)) && cls_message::show('ָ�����ĵ�ģ�Ͳ����ڡ�');
	echo _08_HTML::Title("��ϵ�ֶ� - $channel[cname]");
	if(!($stid = $channel['stid']) || empty($splitbls[$stid])) cls_message::show('ģ��û��ָ���ֱ�');
	$nowtbl = "archives$stid";
	if(!submitcheck('bsubmit')){
		tabheader($channel['cname']." - ��ϵ�ֶ� - ��������$nowtbl",'channeldetail',"?entry=$entry&action=$action&chid=$chid");
		trcategory(array('����','ID',array('��ϵ����','txtL'),'�Զ�',array('�����ֶ�','txtL'),array('��ע','txtL'),'��ѡ','����',));
		foreach($cotypes as $k => $v){
			$fieldstr = $v['self_reg'] ? '-' : "ccid$k".($v['emode'] ? ",ccid{$k}date" : '');
			echo "<tr class=\"txt\">\n".
				"<td class=\"txtC w40\"><input class=\"checkbox\" type=\"checkbox\" name=\"fmdata[$k][available]\" value=\"1\"".(in_array($k,@$splitbls[$stid]['coids']) ? ' checked' : '')."></td>\n".
				"<td class=\"txtC w35\">$k</td>\n".
				"<td class=\"txtL w120\">".mhtmlspecialchars($v['cname'])."</td>\n".
				"<td class=\"txtC w40\">".($v['self_reg'] ? 'Y' : '-')."</td>\n".
				"<td class=\"txtL w120\">$fieldstr</td>\n".
				"<td class=\"txtL\">".mhtmlspecialchars(@$v['remark'])."</td>\n".
				"<td class=\"txtC w40\">".($v['asmode'] ? $v['asmode'] : '-')."</td>\n".
				"<td class=\"txtC w40\">".($v['emode'] ? 'Y' : '-')."</td>\n".
				"</tr>";
		}
		tabfooter('bsubmit');
	}else{
		$coids = array();
		foreach($cotypes as $k => $v){
			$available = empty($fmdata[$k]['available']) ? 0 : 1;
			$available && $coids[] = $k;
			if(!$v['self_reg']){
				if(in_array($k,@$splitbls[$stid]['coids']) != $available){
					if($available){
						if($v['asmode']){
							$db->query("ALTER TABLE {$tblprefix}$nowtbl ADD ccid$k varchar(255) NOT NULL default ''",'SILENT');
						}else{
							$db->query("ALTER TABLE {$tblprefix}$nowtbl ADD ccid$k smallint(6) unsigned NOT NULL default 0",'SILENT');
							@$v['emode'] && $db->query("ALTER TABLE {$tblprefix}$nowtbl ADD ccid{$k}date int(10) unsigned NOT NULL default 0 AFTER ccid$k",'SILENT');
						}
					}else{
						$db-> query("ALTER TABLE {$tblprefix}$nowtbl DROP ccid$k",'SILENT');
						$db-> query("ALTER TABLE {$tblprefix}$nowtbl DROP ccid{$k}date",'SILENT');
					}
				}
			}
		}
		@sort($coids);
		$coids = empty($coids) ? '' : implode(',',$coids);
		$db->query("UPDATE {$tblprefix}splitbls SET coids='$coids' WHERE stid='$stid'");
		cls_CacheFile::Update('splitbls');
		adminlog('�༭'.$channel['cname'].'��ϵ�ֶ��б�');
		cls_message::show('ģ�ͱ༭���!',"?entry=$entry&action=$action&chid=$chid");
	}
}elseif($action == 'channelfields' && $chid){
	!($channel = cls_channel::InitialOneInfo($chid)) && cls_message::show('ָ�����ĵ�ģ�Ͳ����ڡ�');
	$fields = cls_fieldconfig::InitialFieldArray('channel',$chid);
	echo _08_HTML::Title("�ֶι��� - $channel[cname]");
	if(!submitcheck('bchanneldetail')){
		tabheader($channel['cname']."-�ֶι��� &nbsp; &nbsp;>><a href=\"?entry=$entry&action=fieldone&chid=$chid\" onclick=\"return floatwin('open_fielddetail',this)\">����ֶ�</a>",'channeldetail',"?entry=$entry&action=$action&chid=$chid");
		trcategory(array('����','�ֶ�����|L','����','�ֶα�ʶ|L','���ݱ�|L','�ֶ�����','ɾ��','�༭'));
		foreach($fields as $k => $v){
			echo "<tr class=\"txt\">\n".
				"<td class=\"txtC w40\"><input class=\"checkbox\" type=\"checkbox\" name=\"fieldsnew[$k][available]\" value=\"1\"".($v['available'] ? ' checked' : '').(!empty($v['issystem']) ? ' disabled' : '')."></td>\n".
				"<td class=\"txtL\"><input type=\"text\" size=\"25\" name=\"fieldsnew[$k][cname]\" value=\"".mhtmlspecialchars($v['cname'])."\"></td>\n".
				"<td class=\"txtC w60\"><input type=\"text\" size=\"4\" name=\"fieldsnew[$k][vieworder]\" value=\"$v[vieworder]\"></td>\n".
				"<td class=\"txtL\">".mhtmlspecialchars($k)."</td>\n".
				"<td class=\"txtL\">$v[tbl]</td>\n".
				"<td class=\"txtC w100\">".cls_fieldconfig::datatype($v['datatype'])."</td>\n".
				"<td class=\"txtC w40\"><input class=\"checkbox\" type=\"checkbox\"".(empty($v['iscustom']) ? ' disabled' : " name=\"delete[$k]\" value=\"$k\" onclick=\"deltip()\"")."></td>\n".
				"<td class=\"txtC w50\"><a href=\"?entry=$entry&action=fieldone&chid=$chid&fieldname=$k\" onclick=\"return floatwin('open_fielddetail',this)\">����</a></td>\n".
				"</tr>";
		}
		tabfooter();
		tabheader($channel['cname'].'-�ֶ���ع���');
		$abstractarr = $thumbarr = $keywordsarr = $newsarr = $sizearr = $letterarr = array('0' => '������');
		foreach($fields as $k => $v){
			if($v['available']){
				($k!='abstract') && in_array($v['datatype'],array('multitext','htmltext')) && $abstractarr[$k] = $v['cname'].' '.$k;
				($k!='thumb') && in_array($v['datatype'],array('image','images','multitext','htmltext')) && $thumbarr[$k] = $v['cname'].' '.$k;
				($k!='keywords') && in_array($v['datatype'],array('multitext','htmltext')) && $keywordsarr[$k] = $v['cname'].' '.$k;
				in_array($v['datatype'],array('multitext','htmltext')) && $newsarr[$k] = $v['cname'].' '.$k;
				in_array($v['datatype'],array('image','flash','media','file','images','flashs','medias','files',)) && $sizearr[$k] = $v['cname'].' '.$k;
				$v['datatype'] == 'text' && $letterarr[$k] = $v['cname'].' '.$k;
			}
		}
		trbasic('�Զ�����ĸ��Դ�ֶ�','channelnew[autoletter]',makeoption($letterarr,$channel['autoletter']),'select');
		trbasic('�Զ�ժҪ��Դ�ֶ�','channelnew[autoabstract]',makeoption($abstractarr,$channel['autoabstract']),'select');
		trbasic('�Զ�����ͼ��Դ�ֶ�','channelnew[autothumb]',makeoption($thumbarr,$channel['autothumb']),'select');
		trbasic('�Զ��ؼ�����Դ�ֶ�','channelnew[autokeyword]',makeoption($keywordsarr,$channel['autokeyword']),'select');
		trbasic('ȫ��������Դ�ֶ�','channelnew[fulltxt]',makeoption($newsarr,$channel['fulltxt']),'select');
		tabfooter('bchanneldetail');
	}else{
		if(!empty($delete)){
			$deleteds = cls_fieldconfig::DeleteField('channel',$chid,$delete);
			foreach($deleteds as $k){
				unset($fieldsnew[$k]);
			}
		}
		if(!empty($fieldsnew)){
			foreach($fieldsnew as $k => $v){
				$v['cname'] = trim(strip_tags($v['cname']));
				$v['cname'] = $v['cname'] ? $v['cname'] : $fields[$k]['cname'];
				$v['available'] = $fields[$k]['issystem'] || !empty($v['available']) ? 1 : 0;
				$v['vieworder'] = max(0,intval($v['vieworder']));
				cls_fieldconfig::ModifyOneConfig('channel',$chid,$v,$k);
			}
		}
		cls_fieldconfig::UpdateCache('channel',$chid);
		
		$db->query("UPDATE {$tblprefix}channels SET
			autoletter='$channelnew[autoletter]',
			autoabstract='$channelnew[autoabstract]',
			autokeyword='$channelnew[autokeyword]',
			autothumb='$channelnew[autothumb]',
			fulltxt='$channelnew[fulltxt]'
			WHERE chid='$chid'");
		cls_CacheFile::Update('channels');
		adminlog('�༭'.$channel['cname'].'�ֶ��б�');
		cls_message::show('ģ�ͱ༭���!',"?entry=$entry&action=$action&chid=$chid");
	}
}elseif($action == 'fieldone'){
	cls_FieldConfig::EditOne('channel',@$chid,@$fieldname);

}