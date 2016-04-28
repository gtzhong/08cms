<?php
(!defined('M_COM') || !defined('M_ADMIN')) && exit('No Permission');
aheader();
if($re = $curuser->NoBackFunc('mchannel')) cls_message::show($re);
$mctypes = fetch_arr();
$mcmodearr = array(0 => '��ͨ',1 => '�ֻ�',);
if($action == 'mctypesedit'){
	backnav('mchannel','mctype');
	if(!submitcheck('bmctypesedit')){
		$str = " &nbsp; &nbsp;>><a href=\"?entry=$entry&action=mctypeadd\" onclick=\"return floatwin('open_mctypesedit',this)\">�����֤����</a>";
		$str .= " &nbsp; &nbsp;>><a href=\"?entry=sms_admin&action=setapi&isframe=1\" target=\"_blank\">�ֻ���������</a>";
		tabheader("��֤���͹���$str",'mctypesedit',"?entry=$entry&action=$action",'10');
		trcategory(array('ID','����','��������|L','ģʽ','��ע|L','ͼ��','����','ɾ��','����'));
		foreach($mctypes as $k => $v){
			$modestr = @$mcmodearr[$v['mode']];
			echo "<tr class=\"txt\">".
				"<td class=\"txtC w30\">$k</td>\n".
				"<td class=\"txtC w30\"><input class=\"checkbox\" type=\"checkbox\" name=\"mctypesnew[$k][available]\" value=\"1\"".($v['available'] ? " checked" : "")."></td>\n".
				"<td class=\"txtL\"><input type=\"text\" size=\"15\" maxlength=\"30\" name=\"mctypesnew[$k][cname]\" value=\"$v[cname]\"></td>\n".
				"<td class=\"txtC w30\">$modestr</td>\n".
				"<td class=\"txtL\"><input type=\"text\" size=\"30\" maxlength=\"30\" name=\"mctypesnew[$k][remark]\" value=\"$v[remark]\"></td>\n".
				"<td class=\"txtC\"><img src=\"$v[icon]\" border=\"0\" onload=\"if(this.height>20) {this.resized=true; this.height=20;}\" onmouseover=\"if(this.resized) this.style.cursor='pointer';\" onclick=\"if(!this.resized) {return false;} else {window.open(this.src);}\"></td>\n".
				"<td class=\"txtC w40\"><input type=\"text\" size=\"4\" maxlength=\"4\" name=\"mctypesnew[$k][vieworder]\" value=\"$v[vieworder]\"></td>\n".
				"<td class=\"txtC w30\"><a onclick=\"return deltip(this,$no_deepmode)\" href=\"?entry=$entry&action=mctypedel&mctid=$k\">ɾ��</a></td>\n".
				"<td class=\"txtC w30\"><a href=\"?entry=$entry&action=mctypedetail&mctid=$k\" onclick=\"return floatwin('open_mctypesedit',this)\">����</a></td>\n".
				"</tr>\n";
		}
		tabfooter('bmctypesedit','�޸�');
		a_guide('mctypesedit');
	}else{
		if(!empty($mctypesnew)){
			foreach($mctypesnew as $k => $v){
				$v['available'] = empty($v['available']) ? 0 : 1;
				$v['cname'] = trim(strip_tags($v['cname']));
				$v['cname'] = $v['cname'] ? $v['cname'] : $mctypes[$k]['cname'];
				$v['remark'] = trim(strip_tags($v['remark']));
				$v['vieworder'] = max(0,intval($v['vieworder']));
				$db->query("UPDATE {$tblprefix}mctypes SET cname='$v[cname]',remark='$v[remark]',vieworder='$v[vieworder]',available='$v[available]' WHERE mctid='$k'");
			}
			adminlog('�༭��֤�����б�');
			cls_CacheFile::Update('mctypes');
		}
		cls_message::show('��֤���ͱ༭���',"?entry=$entry&action=$action");
	}
}elseif($action == 'mctypeadd'){
	if(!submitcheck('bsubmit')){
		tabheader('�����֤����','mctypeadd',"?entry=$entry&action=$action",2,0,1);
		trbasic('��������','fmdata[cname]','','text',array('validate'=>makesubmitstr('fmdata[cname]',1,0,3,30)));
		trbasic('��ע','fmdata[remark]','','text',array('w'=>50));
		trbasic('��֤ģʽ','',makeradio('fmdata[mode]',$mcmodearr),'',array('guide'=>'ѡ��󲻿ɸ��ġ�'));
		tabfooter('bsubmit','���');
	}else{
		!($fmdata['cname'] = trim(strip_tags($fmdata['cname']))) && cls_message::show('��������֤��������');
		$fmdata['remark'] = trim(strip_tags($fmdata['remark']));
		$db->query("INSERT INTO {$tblprefix}mctypes SET 
					mctid=".auto_insert_id('mctypes').",
					cname='$fmdata[cname]', 
					remark='$fmdata[remark]',
					mode='$fmdata[mode]'
					");
		if($mctid = $db->insert_id()){
			$db->query("ALTER TABLE {$tblprefix}members ADD mctid$mctid smallint(6) unsigned NOT NULL default 0", 'SILENT');
			cls_CacheFile::Update('mctypes');
		}
		adminlog('�����֤����-'.$fmdata['cname']);
		cls_message::show('��֤������ӳɹ������Դ����ͽ�����ϸ���á�',"?entry=$entry&action=mctypedetail&mctid=$mctid");
	}

}elseif($action == 'mctypedetail' && $mctid){
	$mctype = fetch_one($mctid);
	if(!submitcheck('bsubmit')){
		tabheader($mctype['cname'].'-��������','mctypedetail',"?entry=$entry&action=$action&mctid=$mctid",2,1,1);
		trbasic('��������','fmdata[cname]',$mctype['cname'],'text',array('validate'=>makesubmitstr('fmdata[cname]',1,0,3,30)));
		trbasic('�Ƿ��Զ����','fmdata[autocheck]',$mctype['autocheck'],'radio');
		trbasic('�Ƿ����������','fmdata[uncheck]',$mctype['uncheck'],'radio');
		if($mctype['mode']){ //
			trbasic('�����Ƿ�Ψһ','fmdata[isunique]',@$mctype['isunique'],'radio');
		}
		trbasic('��ע','fmdata[remark]',$mctype['remark'],'text',array('w'=>50));
		$_guide = empty($mctype['mode']) ? '' :  '�Ƿ���<a href="?entry=sms_admin&action=enable&&isframe=1" target="_blank">�ֻ�������֤</a>��������[confirm'.$mctid.']��Ա�ֻ���֤��ز�����'; 
		trbasic('��֤ģʽ','',$mcmodearr[$mctype['mode']],'',array('guide'=>$_guide)); 
		/*
		if($mctype['mode']){
			$msg = !empty($mctype['msg']) ? $mctype['msg'] : '����ȷ����Ϊ%s������Ϣ�Զ����ͣ�����ظ���08CMS';
			trspecial('��������ģ��',specialarr(array('type' => 'multitext','varname' => 'fmdata[msg]','value' => $msg,)));
		}*/
		trspecial('��֤��ʾͼ��',specialarr(array('type' => 'image','varname' => 'fmdata[icon]','value' => $mctype['icon'],)));
		trbasic('��֤�����ֶ�','fmdata[field]',$mctype['field'],'text',array('guide'=>'ֻ����ӵ����ֶΣ���Ҫ�ǻ�Աģ���д��ڵ��ֶ�','validate'=>makesubmitstr('fmdata[field]',1,0,1,30)));
		trbasic('�����������ͻ�Ա��֤','',makecheckbox('fmdata[mchids][]',cls_mchannel::mchidsarr(),empty($mctype['mchids']) ? array() : explode(',',$mctype['mchids']),5),'');
		trbasic('������������','fmdata[crid]',makeoption(array(0 => '�ֽ�') + cridsarr(),$mctype['crid']),'select');
		trbasic('��������ֵ','fmdata[award]',$mctype['award'],'text',array('validate' => " rule=\"int\" min=\"0\"",'w' => 10,));
		tabfooter('bsubmit');
	}else{
		!($fmdata['cname'] = trim(strip_tags($fmdata['cname']))) && cls_message::show('��������֤��������');
		!($fmdata['field'] = trim(strip_tags($fmdata['field']))) && cls_message::show('��������֤�����ֶ�');
		$fmdata['remark'] = trim(strip_tags($fmdata['remark']));
		$fmdata['msg'] = trim(strip_tags(@$fmdata['msg']));
		$fmdata['award'] = max(0,intval($fmdata['award']));
		$fmdata['icon'] = upload_s($fmdata['icon'],$mctype['icon'],'image');
		if($k = strpos($fmdata['icon'],'#')) $fmdata['icon'] = substr($fmdata['icon'],0,$k);
		$fmdata['mchids'] = empty($fmdata['mchids']) ? '' : implode(',',$fmdata['mchids']);
		$db->update('#__mctypes',array('cname' => "$fmdata[cname]", 'remark'=>"$fmdata[remark]",'msg'=>"$fmdata[msg]",
							'icon'=>"$fmdata[icon]",'field'=>"$fmdata[field]",'mchids'=>"$fmdata[mchids]",
							'crid'=>"$fmdata[crid]",'award'=>"$fmdata[award]",'autocheck'=>"$fmdata[autocheck]",
							'uncheck'=>"$fmdata[uncheck]",'isunique'=>intval(@$fmdata['isunique'])))->where('mctid='.$mctid)->exec();
							
		cls_CacheFile::Update('mctypes');
		adminlog('�༭��֤����-'.$mctype['cname']);
		cls_message::show('���ͱ༭���!',axaction(6,"?entry=$entry&action=mctypesedit"));
	}
}elseif($action == 'mctypedel' && $mctid) {
	$mctype = $mctypes[$mctid];
	deep_allow($no_deepmode);
	if(!submitcheck('confirm')){
		$message = "ɾ�����ָܻ���ȷ��ɾ����ѡ��Ŀ?<br><br>";
		$message .= "ȷ������>><a href=?entry=$entry&action=$action&mctid=$mctid&confirm=ok>ɾ��</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
		$message .= "��������>><a href=?entry=$entry&action=mctypesedit>����</a>";
		cls_message::show($message);
	}
	$db->query("DELETE FROM {$tblprefix}mcerts WHERE mctid='$mctid'",'SILENT');
	$db->query("DELETE FROM {$tblprefix}mctypes WHERE mctid='$mctid'",'SILENT');
	$db->query("ALTER TABLE {$tblprefix}members DROP mctid$mctid", 'SILENT'); 
	adminlog('ɾ����֤����-'.$mctype['cname']);
	cls_CacheFile::Update('mctypes');
	cls_message::show('ָ������֤�����ѳɹ�ɾ����',"?entry=$entry&action=mctypesedit");
}
function fetch_arr(){
	global $db,$tblprefix;
	$rets = array();
	$query = $db->query("SELECT * FROM {$tblprefix}mctypes ORDER BY vieworder,mctid");
	while($r = $db->fetch_array($query)){
		$rets[$r['mctid']] = $r;
	}
	return $rets;
}

function fetch_one($mctid){
	global $db,$tblprefix;
	$r = $db->fetch_one("SELECT * FROM {$tblprefix}mctypes WHERE mctid='$mctid'");
	return $r;
}

?>
