<?php
(!defined('M_COM') || !defined('M_ADMIN')) && exit('No Permission');
aheader();
if($re = $curuser->NoBackFunc('mchannel')) cls_message::show($re);
include_once M_ROOT."include/fields.fun.php";
foreach(array('rprojects','cotypes',) as $k) $$k = cls_cache::Read($k);
if($action == 'mchannelsedit'){
	backnav('mchannel','channel');
	$mchannels = cls_mchannel::InitialInfoArray();
	if(!submitcheck('bmchannelsedit')){
		tabheader("��Աģ�͹���&nbsp; &nbsp; >><a href=\"?entry=mchannels&action=mchanneladd\" onclick=\"return floatwin('open_mchanneledit',this)\">���</a> &nbsp; &nbsp;<a href=\"?entry=amconfigs&action=amconfigmblock\" onclick=\"return floatwin('open_fnodes',this)\">>>��̨�ڵ�</a>",'mchanneledit','?entry=mchannels&action=mchannelsedit','10');
		trcategory(array('ID','��Ч','ģ������|L','ɾ��','�ֶ�','�༭','����'));
		foreach($mchannels as $k => $mchannel){
			echo "<tr class=\"txt\">".
				"<td class=\"txtC w30\">$k</td>\n".
				"<td class=\"txtC w30\"><input class=\"checkbox\" type=\"checkbox\" name=\"mchannelnew[$k][available]\" value=\"1\"".($mchannel['available'] ? " checked" : "").($mchannel['issystem'] ? ' disabled' : '')."></td>\n".
				"<td class=\"txtL\"><input type=\"text\" size=\"30\" maxlength=\"30\" name=\"mchannelnew[$k][cname]\" value=\"$mchannel[cname]\"></td>\n".
				"<td class=\"txtC w30\">".($mchannel['issystem'] ? '-' : "<a onclick=\"return deltip(this,$no_deepmode)\" href=\"?entry=mchannels&action=mchanneldel&mchid=$k\">ɾ��</a>")."</td>\n".
				"<td class=\"txtC w30\"><a href=\"?entry=mchannels&action=mchannelfields&mchid=$k\" onclick=\"return floatwin('open_mchanneledit',this)\">�ֶ�</a></td>\n".
				"<td class=\"txtC w30\"><a href=\"?entry=mchannels&action=mchanneldetail&mchid=$k\" onclick=\"return floatwin('open_mchanneledit',this)\">����</a></td>\n".
				"<td class=\"txtC w30\"><a href=\"?entry=mchannels&action=mchanneladv&mchid=$k\" onclick=\"return floatwin('open_mchanneledit',this)\">�߼�</a></td>\n".
				"</tr>\n";
		}
		tabfooter('bmchannelsedit','�޸�');
		a_guide('mchannelsedit');
	}else{
		if(isset($mchannelnew)){
			foreach($mchannelnew as $k => $v) {
				$v['available'] = isset($v['available']) ? $v['available'] : 0;
				$v['cname'] = trim(strip_tags($v['cname']));
				$v['cname'] = $v['cname'] ? $v['cname'] : $mchannels[$k]['cname'];
				if(($v['cname'] != $mchannels[$k]['cname']) || ($v['available'] != $mchannels[$k]['available'])) {
					$db->query("UPDATE {$tblprefix}mchannels SET cname='$v[cname]', available='$v[available]' WHERE mchid='$k'");
				}
			}
			adminlog('�༭��Աģ���б�');
			cls_CacheFile::Update('mchannels');
			cls_message::show('��Աģ�ͱ༭���',"?entry=mchannels&action=mchannelsedit");
		}
	}
}elseif($action == 'mchanneladd'){
	if(!submitcheck('bmchanneladd')){
		tabheader('��ӻ�Աģ��','mchanneladd','?entry=mchannels&action=mchanneladd',2,0,1);
		trbasic('��Աģ������','mchanneladd[cname]','','text',array('validate'=>makesubmitstr('mchanneladd[cname]',1,0,3,30)));
		tabfooter('bmchanneladd','���');
	}else{
		$mchanneladd['cname'] = trim(strip_tags($mchanneladd['cname']));
		empty($mchanneladd['cname']) && cls_message::show('���ϲ���ȫ', '?entry=mchannels&action=mchanneledit');
		$db->query("INSERT INTO {$tblprefix}mchannels SET mchid=".auto_insert_id('mchannels').",cname='$mchanneladd[cname]'");
		if($mchid = $db->insert_id()){
			$db->query("CREATE TABLE {$tblprefix}members_$mchid (
						mid mediumint(8) unsigned NOT NULL default '0',
						PRIMARY KEY (mid))".(mysql_get_server_info() > '4.1' ? " ENGINE=MYISAM DEFAULT CHARSET=$dbcharset" : " TYPE=MYISAM"));

			$query = $db->query("SELECT * FROM {$tblprefix}afields WHERE type='m' AND tpid='0' ORDER BY vieworder,fid");
			while($r = $db->fetch_array($query)){
				$sqlstr = "tpid='$mchid'";
				foreach($r as $k => $v) if(!in_array($k,array('fid','tpid'))) $sqlstr .= ",`$k`='".addslashes($v)."'";
				$db->query("INSERT INTO {$tblprefix}afields SET $sqlstr");
			}
			cls_CacheFile::Update('mchannels');
			cls_CacheFile::Update('mfields',$mchid);
		}
		adminlog('��ӻ�Աģ��');
		cls_message::show('��Աģ��������',"?entry=mchannels&action=mchanneldetail&mchid=$mchid");
	}

}elseif($action == 'mchanneldetail' && $mchid) {
	!($mchannel = cls_mchannel::InitialOneInfo($mchid)) && cls_message::show('ָ���Ļ�Աģ�Ͳ����ڡ�');
	if(!submitcheck('bmchanneldetail')){
		$autocheckarr = array(0 => '�ֶ����',1 => '�Զ����',2 => 'Email����');
		tabheader("[$mchannel[cname]]".'��Աģ������','mchanneldetail','?entry=mchannels&action=mchanneldetail&mchid='.$mchid,'4');
		trbasic('ע���Ա��˷�ʽ','',makeradio('mchannelnew[autocheck]',$autocheckarr,$mchannel['autocheck']),'');
		tabfooter('bmchanneldetail');
		a_guide('mchanneldetail');
	}else{
		$db->query("UPDATE {$tblprefix}mchannels SET
			autocheck='$mchannelnew[autocheck]'
			WHERE mchid='$mchid'");
		adminlog('��ϸ�޸Ļ�Աģ��');
		cls_CacheFile::Update('mchannels');
		cls_message::show('ģ���޸����', '?entry=mchannels&action=mchanneldetail&mchid='.$mchid);
	}
}elseif($action == 'mchanneladv' && $mchid){
	!($mchannel = cls_mchannel::InitialOneInfo($mchid)) && cls_message::show('ָ�����ĵ�ģ�Ͳ����ڡ�');
	if(@!include("mchannels/mchannel_$mchid.php")){
		if(!submitcheck('bmchanneldetail')){
			tabheader($mchannel['cname'].'-�߼���չ����','mchanneldetail',"?entry=$entry&action=mchanneladv&mchid=$mchid");
			trbasic('���ò�������'.($mchannel['cfgs0'] && !$mchannel['cfgs'] ? '<br>�����ʽ���������!' : ''),'mchannelnew[cfgs0]',empty($mchannel['cfgs']) ? (empty($mchannel['cfgs0']) ? '' : $mchannel['cfgs0']) : var_export($mchannel['cfgs'],1),'textarea',array('w' => 500,'h' => 300,'guide'=>'��array()���룬����������Ҫ��php�淶'));
			trbasic('����˵��','mchannelnew[content]',$mchannel['content'],'textarea',array('w' => 500,'h' => 300,));
			tabfooter('bmchanneldetail');
		}else{
			$mchannelnew['cfgs0'] = empty($mchannelnew['cfgs0']) ? '' : trim($mchannelnew['cfgs0']);
			$mchannelnew['cfgs'] = varexp2arr($mchannelnew['cfgs0']);
			$mchannelnew['content'] = empty($mchannelnew['content']) ? '' : trim($mchannelnew['content']);
			$mchannelnew['cfgs'] = !empty($mchannelnew['cfgs']) ? addslashes(var_export($mchannelnew['cfgs'],TRUE)) : '';
			$db->query("UPDATE {$tblprefix}mchannels SET
						content='$mchannelnew[content]',
						cfgs0='$mchannelnew[cfgs0]',
						cfgs='$mchannelnew[cfgs]'
						WHERE mchid='$mchid'");
			cls_CacheFile::Update('mchannels');
			adminlog('�༭�ĵ�ģ��-'.$mchannel['cname']);
			cls_message::show('ģ�ͱ༭���!',"?entry=$entry&action=mchanneladv&mchid=$mchid");
		}
	}
}elseif($action == 'mchanneldel' && $mchid) {
	deep_allow($no_deepmode);
	$mchannel = $mchannels[$mchid];
	if($mchannel['issystem']) cls_message::show('ϵͳģ�Ͳ���ɾ��', '?entry=mchannels&action=mchannelsedit');
	if(!submitcheck('confirm')){
		$message = "ɾ�����ָܻ���ȷ��ɾ����ѡ��Ŀ?<br><br>";
		$message .= 'ȷ��������'."[<a href=?entry=mchannels&action=mchanneldel&mchid=$mchid&confirm=ok>ɾ��</a>]&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
		$message .= '����������'."[<a href=?entry=mchannels&action=mchannelsedit>����</a>]";
		cls_message::show($message);
	}
	if($db->result_one("SELECT COUNT(*) FROM {$tblprefix}members WHERE mchid='$mchid'")){
		cls_message::show('ģ��û��������Ļ�Ա����ɾ��', '?entry=mchannels&action=mchannelsedit');
	}
	$customtable = 'members_'.$mchid;
	$db->query("DROP TABLE IF EXISTS {$tblprefix}$customtable",'SILENT');
	$db->query("DELETE FROM {$tblprefix}mchannels WHERE mchid='$mchid'",'SILENT');
	cls_fieldconfig::DeleteOneSourceFields('mchannel',$mchid);
	
	//�����ػ���
	adminlog('ɾ����Աģ��');
	cls_CacheFile::Update('mchannels');
	cls_message::show('��Աģ��ɾ�����',"?entry=mchannels&action=mchannelsedit");
}elseif($action == 'fieldone'){
	cls_FieldConfig::EditOne('mchannel',@$mchid,@$fieldname);

}elseif($action == 'mchannelfields' && $mchid) {
	!($mchannel = cls_mchannel::InitialOneInfo($mchid)) && cls_message::show('ָ���Ļ�Աģ�Ͳ����ڡ�');
	$fields = cls_fieldconfig::InitialFieldArray('mchannel',$mchid);
	if(!submitcheck('bmchanneldetail')){
		tabheader($mchannel['cname'].'-'.'�ֶι���'."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;>><a href=\"?entry=mchannels&action=fieldone&mchid=$mchid\" onclick=\"return floatwin('open_fielddetail',this)\">����ֶ�</a>",'mchanneldetail','?entry=mchannels&action=mchannelfields&mchid='.$mchid,'8');
		trcategory(array('����','�ֶ�����','����','�ֶα�ʶ','�ֶ�����','���ݱ�|L','ɾ��','�༭'));
		foreach($fields as $k => $v){
			echo "<tr class=\"txt\">\n".
				"<td class=\"txtC w40\"><input class=\"checkbox\" type=\"checkbox\" name=\"fieldsnew[$k][available]\" value=\"1\"".($v['available'] ? ' checked' : '').(!empty($v['issystem']) ? ' disabled' : '')."></td>\n".
				"<td class=\"txtL\"><input type=\"text\" size=\"25\" name=\"fieldsnew[$k][cname]\" value=\"".mhtmlspecialchars($v['cname'])."\"></td>\n".
				"<td class=\"txtC w60\"><input type=\"text\" size=\"4\" name=\"fieldsnew[$k][vieworder]\" value=\"$v[vieworder]\"></td>\n".
				"<td class=\"txtC\">".mhtmlspecialchars($k)."</td>\n".
				"<td class=\"txtC w100\">".cls_fieldconfig::datatype($v['datatype'])."</td>\n".
				"<td class=\"txtL\">".$v['tbl']."</td>\n".
				"<td class=\"txtC w40\"><input class=\"checkbox\" type=\"checkbox\"".(!empty($v['iscommon']) ? ' disabled' : " name=\"delete[$k]\" value=\"$k\" onclick=\"deltip(this,$no_deepmode)\"")."></td>\n".
				"<td class=\"txtC w50\">".($v['issystem'] ? '-' : "<a href=\"?entry=$entry&action=fieldone&mchid=$mchid&fieldname=$k\" onclick=\"return floatwin('open_fielddetail',this)\">����</a>")."</td>\n".
				"</tr>";
		}
		tabfooter();
		tabheader($mchannel['cname'].'-�ֶ���ع���');
		$letterarr = array('0' => '������');
		foreach($fields as $k => $v){
			if($v['available']){
				$v['datatype'] == 'text' && $letterarr[$k] = $v['cname'];
			}
		}
		trbasic('�Զ�����ĸ��Դ�ֶ�','mchannelnew[autoletter]',makeoption($letterarr,$mchannel['autoletter']),'select');
		tabfooter('bmchanneldetail');
	}else{
		if(!empty($delete) && deep_allow($no_deepmode)){
			$deleteds = cls_fieldconfig::DeleteField('mchannel',$mchid,$delete);
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
				cls_fieldconfig::ModifyOneConfig('mchannel',$mchid,$v,$k);
			}
		}
		cls_fieldconfig::UpdateCache('mchannel',$mchid);
		
		$db->query("UPDATE {$tblprefix}mchannels SET
			autoletter='$mchannelnew[autoletter]'
			WHERE mchid='$mchid'");
		adminlog('��ϸ�޸Ļ�Աģ��');
		cls_CacheFile::Update('mchannels');
		cls_message::show('ģ���޸����', '?entry=mchannels&action=mchannelfields&mchid='.$mchid);
	}
}elseif($action == 'initmfieldsedit'){
	backnav('mchannel','field');
	$fields = cls_fieldconfig::InitialFieldArray('mchannel',0);
	if(!submitcheck('binitmfieldsedit')){
		tabheader('��Աͨ���ֶι���'."&nbsp; &nbsp; >><a href=\"?entry=mchannels&action=fieldone\" onclick=\"return floatwin('open_fielddetail',this)\">���</a>",'initmfieldsedit','?entry=mchannels&action=initmfieldsedit','5');
		trcategory(array('���','�ֶ�����|L','�ֶα�ʶ','�ֶ�����','���ݱ�|L','ɾ��','�༭'));
		$ii = 0;
		foreach($fields as $k => $v) {
			echo "<tr class=\"txt\">\n".
				"<td class=\"txtC w40\">".++$ii."</td>\n".
				"<td class=\"txtL\"><input type=\"text\" size=\"25\" name=\"fieldsnew[$k][cname]\" value=\"".mhtmlspecialchars($v['cname'])."\"></td>\n".
				"<td class=\"txtC\">".mhtmlspecialchars($k)."</td>\n".
				"<td class=\"txtC w100\">".cls_fieldconfig::datatype($v['datatype'])."</td>\n".
				"<td class=\"txtL\">".$v['tbl']."</td>\n".
				"<td class=\"txtC w40\"><input class=\"checkbox\" type=\"checkbox\"".(empty($v['iscustom']) ? ' disabled' : " name=\"delete[$k]\" value=\"$k\" onclick=\"deltip(this,$no_deepmode)\"")."></td>\n".
				"<td class=\"txtC w60\">".($v['issystem'] ? '-' : "<a href=\"?entry=$entry&action=fieldone&fieldname=$k\" onclick=\"return floatwin('open_fielddetail',this)\">����</a>")."</td>\n".
				"</tr>";
		}
		tabfooter('binitmfieldsedit');
		a_guide('initmfieldsedit');
	}else{
		if(!empty($delete) && deep_allow($no_deepmode)){
			$deleteds = cls_fieldconfig::DeleteField('mchannel',0,$delete);
			foreach($deleteds as $k){
				unset($fieldsnew[$k]);
			}
		}
		foreach($fieldsnew as $k => $v){
			$v['cname'] = trim($v['cname']) ? trim($v['cname']) : $fields[$k]['cname'];
			cls_fieldconfig::ModifyOneConfig('mchannel',0,$v,$k);
		}
		cls_fieldconfig::UpdateCache('mchannel',0);
		
		adminlog('�༭��Աͨ����Ϣ�ֶι����б�');
		cls_message::show('�ֶα༭���','?entry=mchannels&action=initmfieldsedit');
	}
}
