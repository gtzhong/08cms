<?
(!defined('M_COM') || !defined('M_ADMIN')) && exit('No Permission');
aheader();
foreach(array('currencys','mchannels',) as $k) $$k = cls_cache::Read($k);
if($action == 'grouptypesedit'){
	backnav('mchannel','grouptype');
	if($re = $curuser->NoBackFunc('mchannel')) cls_message::show($re);
	$grouptypes = fetch_arr();
	if(!submitcheck('bgrouptypesadd') && !submitcheck('bgrouptypesedit')){
		$modearr = array('0' => '�û��ֶ�','1' => '�����ֶ�','2' => '���ֻ���','3' => '���ֶһ�',);
		$cridsarr = array(0 => '������') + cridsarr();
		$itemstr = '';
		foreach($grouptypes as $k => $v){
			$modestr = $modearr[$v['mode']];
			$cridstr = empty($v['crid']) || empty($cridsarr[$v['crid']]) ? '-' : $cridsarr[$v['crid']];
			if(empty($v['crid']) && $v['mode'] == 3) $cridstr = '�ֽ�';
			$itemstr .= "<tr class=\"txt\"><td class=\"txtC w40\">$k</td>\n".
					"<td class=\"txtL\"><input type=\"text\" size=\"25\" maxlength=\"30\" name=\"grouptypesnew[$k][cname]\" value=\"$v[cname]\"></td>\n".
					"<td class=\"txtC\">$modestr</td>\n".
					"<td class=\"txtC\">$cridstr</td>\n".
					"<td class=\"txtC\">".($v['issystem'] || $v['mode'] != 1 ? '-' : "<a href=\"?entry=grouptypes&action=uprojects&gtid=$k\" onclick=\"return floatwin('open_grouptypesedit',this)\">����</a>")."</td>\n".
					"<td class=\"txtC w40\"><input class=\"checkbox\" type=\"checkbox\"".($v['issystem'] ? ' disabled' : " name=\"delete[$k]\" value=\"$k\" onclick=\"deltip(this,$no_deepmode)\"")."></td>\n".
					"<td class=\"txtC w40\"><a href=\"?entry=grouptypes&action=grouptypedetail&gtid=$k\" onclick=\"return floatwin('open_grouptypesedit',this)\">����</a></td>\n".
					"<td class=\"txtC w40\"><a href=\"?entry=usergroups&action=usergroupsedit&gtid=$k\" onclick=\"return floatwin('open_grouptypesedit',this)\">����</a></td></tr>\n";
		}
		tabheader('�༭��Ա����ϵ','grouptypesedit','?entry=grouptypes&action=grouptypesedit','7');
		trcategory(array('ID','��ϵ����|L','����ģʽ','��ػ���','��������','ɾ��','����','��Ա��'));
		echo $itemstr;
		tabfooter('bgrouptypesedit','�޸�');

		tabheader('��ӻ�Ա����ϵ','grouptypesadd','?entry=grouptypes&action=grouptypesedit');
		trbasic('��ϵ����','grouptypeadd[cname]');
		trbasic('����ģʽ','grouptypeadd[mode]',makeoption($modearr),'select');
		trbasic('��ػ�������','grouptypeadd[crid]',makeoption($cridsarr),'select');
		tabfooter('bgrouptypesadd','���');
		a_guide('grouptypesedit');
	}elseif(submitcheck('bgrouptypesadd')){
		if(empty($grouptypeadd['cname']) || (($grouptypeadd['mode'] == 2) && empty($grouptypeadd['crid']))){
			cls_message::show('��Ա����ϵ���ϲ���ȫ','?entry=grouptypes&action=grouptypesedit');
		}
		$grouptypeadd['crid'] = $grouptypeadd['mode'] < 2 ? 0 : $grouptypeadd['crid'];
		$db->query("INSERT INTO {$tblprefix}grouptypes SET
					gtid=".auto_insert_id('grouptypes').",
					cname='$grouptypeadd[cname]',
					mode='$grouptypeadd[mode]',
					crid='$grouptypeadd[crid]'");
		if(!$gtid = $db->insert_id()){
			cls_message::show('��Ա����ϵ����ʱ��������','?entry=grouptypes&action=grouptypesedit');
		}else{
			$addfieldid = 'grouptype'.$gtid;
			$addfielddate = 'grouptype'.$gtid.'date';
			$db->query("ALTER TABLE {$tblprefix}members ADD $addfieldid smallint(6) unsigned NOT NULL default 0", 'SILENT');
			$db->query("ALTER TABLE {$tblprefix}members ADD $addfielddate int(10) unsigned NOT NULL default 0", 'SILENT');
		}
		adminlog('��ӻ�Ա����ϵ');
		cls_CacheFile::Update('grouptypes');
		cls_message::show('��Ա����ϵ������',"?entry=grouptypes&action=grouptypesedit");
	}elseif(submitcheck('bgrouptypesedit')){
		if(!empty($delete) && deep_allow($no_deepmode)){		    
            $file = _08_FilesystemFile::getInstance();
			foreach($delete as $gtid) {
				if(empty($grouptypes[$gtid]['issystem'])){
					if($db->result_one("SELECT COUNT(*) FROM {$tblprefix}usergroups WHERE gtid='$gtid'")) continue;//������ػ�Ա��ʱ����ɾ��
					$db->query("DELETE FROM {$tblprefix}grouptypes WHERE gtid='$gtid'","SILENT");
					$deletefield = 'grouptype'.$gtid;
					$deletefielddate = 'grouptype'.$gtid.'date';
					$db->query("ALTER TABLE {$tblprefix}members DROP $deletefield,DROP $deletefielddate","SILENT");
					$file->delFile(M_ROOT."dynamic/cache/usergroups$gtid.cac.php");
					unset($grouptypesnew[$gtid]);
				}
			}
		}

		if(!empty($grouptypesnew)){
			foreach($grouptypesnew as $gtid => $grouptype){
				if(empty($grouptypes[$gtid]['issystem'])){
					$grouptype['cname'] = empty($grouptype['cname']) ? $grouptypes[$gtid]['cname'] : $grouptype['cname'];
					if($grouptype['cname'] != $grouptypes[$gtid]['cname']){
						$db->query("UPDATE {$tblprefix}grouptypes SET
									cname='$grouptype[cname]'
									WHERE gtid='$gtid'");
					}
				}
			}
		}
		adminlog('�༭��Ա����ϵ�����б�');
		cls_CacheFile::Update('grouptypes');
		cls_message::show('��Ա����ϵ�༭���',"?entry=grouptypes&action=grouptypesedit");
	}
}elseif($action == 'grouptypedetail' && $gtid){
	if($re = $curuser->NoBackFunc('mchannel')) cls_message::show($re);
	if(!($grouptype = fetch_one($gtid))) cls_message::show('��ָ����ȷ�Ļ�Ա��ϵ');
	if(!submitcheck('bgrouptypedetail')){
		tabheader('�༭��Ա����ϵ','grouptypedetail',"?entry=grouptypes&action=grouptypedetail&gtid=$gtid");
		$modearr = array('0' => '�û��ֶ�','1' => '�����ֶ�','2' => '���ֻ���','3' => '���ֶһ�',);
		$cridsarr = array(0 => $grouptype['mode'] == 3 ? '�ֽ�': '������') + cridsarr();
		trbasic('��ϵ����','grouptypenew[cname]',$grouptype['cname']);
		if($grouptype['issystem']){
			trbasic('����ģʽ','',$modearr[$grouptype['mode']],'');
			trbasic('��ػ�������','',$cridsarr[$grouptype['crid']],'');
		}else{
			trbasic('����ģʽ','grouptypenew[mode]',makeoption($modearr,$grouptype['mode']),'select');
			trbasic('��ػ�������','grouptypenew[crid]',makeoption($cridsarr,$grouptype['crid']),'select');
		}
		trbasic('������ģ���н�ֹʹ��','',makecheckbox('grouptypenew[mchids][]',cls_mchannel::mchidsarr(),!empty($grouptype['mchids']) ? explode(',',$grouptype['mchids']) : array(),5),'');
		tabfooter('bgrouptypedetail','�޸�');
		a_guide('grouptypedetail');
	}else{
		$grouptypenew['mode'] = empty($grouptypenew['mode']) ? 0 : $grouptypenew['mode'];
		$grouptypenew['crid'] = empty($grouptypenew['crid']) ? 0 : $grouptypenew['crid'];
		if(empty($grouptypenew['cname']) || (($grouptypenew['mode'] == 2) && empty($grouptypenew['crid']))){
			cls_message::show('��Ա����ϵ���ϲ���ȫ',M_REFERER);
		}
		$grouptypenew['crid'] = $grouptypenew['mode'] < 2 ? 0 : $grouptypenew['crid'];
		$grouptypenew['mchids'] = !empty($grouptypenew['mchids']) ? implode(',',$grouptypenew['mchids']) : '';
		$sqlstr = $grouptype['issystem'] ? '' : "mode='$grouptypenew[mode]',crid='$grouptypenew[crid]',";
		$db->query("UPDATE {$tblprefix}grouptypes SET
					cname='$grouptypenew[cname]',
					$sqlstr
					mchids='$grouptypenew[mchids]'
					WHERE gtid='$gtid'");
		adminlog('�����޸Ļ�Ա����ϵ');
		cls_CacheFile::Update('grouptypes',$gtid);
		cls_message::show('��Ա����ϵ�༭���',"?entry=grouptypes&action=grouptypedetail&gtid=$gtid");
	}
}elseif($action == 'uprojects' && $gtid){
	if($re = $curuser->NoBackFunc('mchannel')) cls_message::show($re);
	if(!($grouptype = fetch_one($gtid))) cls_message::show('��ָ����ȷ�Ļ�Ա��ϵ');
	if($grouptype['issystem'] || $grouptype['mode'] != 1)  cls_message::show('ֻ�й����ֶ�����ϵ�ſ��Զ�����������');
	$uprojects = fetch_parr();
	if(!submitcheck('bsubmit')){
		$ugidsarr = array(0 => '�����Ա') + ugidsarr($gtid);

		$nuprojects = array();foreach($uprojects as $k => $v) $v['gtid'] == $gtid && $nuprojects[$k] = $v;
		tabheader("��Ա��������&nbsp; -&nbsp; $grouptype[cname]"."&nbsp; &nbsp; >><a href=\"?entry=$entry&action=uprojectadd&gtid=$gtid\" onclick=\"return floatwin('open_uprojects',this)\">��ӷ���</a>",'uprojects',"?entry=$entry&action=uprojects&gtid=$gtid",'10');
		trcategory(array(array('��������','txtL'),array('��Դ��Ա��','txtL'),array('Ŀ���Ա��','txtL'),'�Զ����','ɾ��','�༭'));
		foreach($nuprojects as $k => $v){
			echo "<tr class=\"txt\">".
				"<td class=\"txtL\"><input type=\"text\" size=\"20\" maxlength=\"30\" name=\"uprojectsnew[$k][cname]\" value=\"$v[cname]\"></td>\n".
				"<td class=\"txtL\">".$ugidsarr[$v['sugid']]."</td>\n".
				"<td class=\"txtL\">".$ugidsarr[$v['tugid']]."</td>\n".
				"<td class=\"txtC w60\"><input class=\"checkbox\" type=\"checkbox\" name=\"uprojectsnew[$k][autocheck]\" value=\"1\"".($v['autocheck'] ? " checked" : "")."></td>\n".
				"<td class=\"txtC w30\"><input class=\"checkbox\" type=\"checkbox\" name=\"delete[$k]\" value=\"$k\" onclick=\"deltip()\"></td>\n".
				"<td class=\"txtC w30\"><a href=\"?entry=$entry&action=uprojectdetail&gtid=$gtid&upid=$k\" onclick=\"return floatwin('open_uprojects',this)\">����</a></td>\n".
				"</tr>\n";
		}
		tabfooter('bsubmit');
		a_guide('grouptypedetail');
	}else{
		if(!empty($delete)){
			foreach($delete as $k){
				$db->query("DELETE FROM {$tblprefix}uprojects WHERE upid='$k'");
				unset($uprojectsnew[$k]);
			}
		}
		if(!empty($uprojectsnew)){
			foreach($uprojectsnew as $k => $v){
				$v['cname'] = trim(strip_tags($v['cname']));
				$v['cname'] = $v['cname'] ? $v['cname'] : $uprojects[$k]['cname'];
				$v['autocheck'] = empty($v['autocheck']) ? 0 : 1;
				$db->query("UPDATE {$tblprefix}uprojects SET cname='$v[cname]',autocheck='$v[autocheck]' WHERE upid='$k'");
			}
		}
		cls_CacheFile::Update('uprojects');
		adminlog('�޸Ļ�Ա��������');
		cls_message::show('��Ա���������޸����',"?entry=$entry&action=uprojects&gtid=$gtid");
	}
}elseif($action == 'uprojectadd' && $gtid){
	if($re = $curuser->NoBackFunc('mchannel')) cls_message::show($re);
	!($ugidsarr = ugidsarr($gtid)) && cls_message::show('���������Ч�Ļ�Ա��');
	$uprojects = fetch_parr();
	if(!submitcheck('buprojectadd')){
		$ugidsarr = array(0 => '�����Ա') + $ugidsarr;
		tabheader('��ӻ�Ա��������',"uprojectadd","?entry=$entry&action=uprojectadd&gtid=$gtid",2,0,1);
		trbasic('��������','uprojectnew[cname]','','text',array('validate'=>makesubmitstr('uprojectnew[cname]',1,0,3,30)));
		trbasic('��Դ��Ա��','uprojectnew[sugid]',makeoption($ugidsarr),'select');
		trbasic('Ŀ���Ա��','uprojectnew[tugid]',makeoption($ugidsarr),'select');
		trbasic('��Ա�����Զ����','uprojectnew[autocheck]',0,'radio');
		tabfooter('buprojectadd','���');
		a_guide('uprojectadd');
	}else{
		$uprojectnew['cname'] = trim(strip_tags($uprojectnew['cname']));
		if(!$uprojectnew['cname']) cls_message::show('�����뷽������!',M_REFERER);
		if($uprojectnew['sugid'] == $uprojectnew['tugid']) cls_message::show('��Դ��Ա����Ŀ���Ա����ͬ!',M_REFERER);
		$uprojectnew['ename'] = $uprojectnew['sugid'].'_'.$uprojectnew['tugid'];
		$usedcnames = array();foreach($uprojects as $v) $usedcnames[] = $v['ename'];
		if(in_array($uprojectnew['ename'],$usedcnames)) cls_message::show('�����ظ�����!',M_REFERER);
		$db->query("INSERT INTO {$tblprefix}uprojects SET
					cname='$uprojectnew[cname]',
					ename='$uprojectnew[ename]',
					gtid='$gtid',
					sugid='$uprojectnew[sugid]',
					tugid='$uprojectnew[tugid]',
					autocheck='$uprojectnew[autocheck]'
					");
		cls_CacheFile::Update('uprojects');
		adminlog('��ӻ�Ա��������');
		cls_message::show('��Ա��������������',axaction(6,"?entry=$entry&action=uprojects&gtid=$gtid"));
	}
}elseif($action == 'uprojectdetail' && $gtid && $upid){
	if($re = $curuser->NoBackFunc('mchannel')) cls_message::show($re);
	!($ugidsarr = ugidsarr($gtid)) && cls_message::show('���������Ч�Ļ�Ա��');
	!($uproject = fetch_pone($upid)) && cls_message::show('��ָ����ȷ�Ļ�Աģ�ͱ������');
	$uprojects = fetch_parr();
	if(!submitcheck('buprojectdetail')){
		$ugidsarr = array(0 => '�����Ա') + $ugidsarr;
		tabheader('�༭��Ա��������',"uprojectdetail","?entry=$entry&action=uprojectdetail&gtid=$gtid&upid=$upid",2,0,1);
		trbasic('��������','uprojectnew[cname]',$uproject['cname'],'text',array('validate'=>makesubmitstr('uprojectnew[cname]',1,0,3,30)));
		trbasic('��Դ��Ա��','uprojectnew[sugid]',makeoption($ugidsarr,$uproject['sugid']),'select');
		trbasic('Ŀ���Ա��','uprojectnew[tugid]',makeoption($ugidsarr,$uproject['tugid']),'select');
		trbasic('��Ա�����Զ����','uprojectnew[autocheck]',$uproject['autocheck'],'radio');
		tabfooter('buprojectdetail');
		a_guide('uprojectdetail');
	}else{
		$uprojectnew['cname'] = trim(strip_tags($uprojectnew['cname']));
		if(!$uprojectnew['cname']) cls_message::show('�����뷽������!',M_REFERER);
		if($uprojectnew['sugid'] == $uprojectnew['tugid']) cls_message::show('��Դģ����Ŀ��ģ����ͬ!',M_REFERER);
		$uprojectnew['ename'] = $uprojectnew['sugid'].'_'.$uprojectnew['tugid'];
		$usedcnames = array();foreach($uprojects as $v) $usedcnames[] = $v['ename'];
		if(($uprojectnew['ename'] != $uproject['ename']) && in_array($uprojectnew['ename'],$usedcnames)) cls_message::show('�����ظ�����!',M_REFERER);
		$db->query("UPDATE {$tblprefix}uprojects SET
					cname='$uprojectnew[cname]',
					ename='$uprojectnew[ename]',
					sugid='$uprojectnew[sugid]',
					tugid='$uprojectnew[tugid]',
					autocheck='$uprojectnew[autocheck]'
					WHERE upid='$upid'
					");
		cls_CacheFile::Update('uprojects');
		adminlog('�޸Ļ�Ա��������');
		cls_message::show('��Ա���������޸����',axaction(6,"?entry=$entry&action=uprojects&gtid=$gtid"));
	}
}
function fetch_arr(){
	global $db,$tblprefix;
	$rets = array();
	$query = $db->query("SELECT * FROM {$tblprefix}grouptypes ORDER BY gtid");
	while($r = $db->fetch_array($query)){
		$rets[$r['gtid']] = $r;
	}
	return $rets;
}

function fetch_one($gtid){
	global $db,$tblprefix;
	$r = $db->fetch_one("SELECT * FROM {$tblprefix}grouptypes WHERE gtid='$gtid'");
	return $r;
}
function fetch_parr(){
	global $db,$tblprefix;
	$rets = array();
	$query = $db->query("SELECT * FROM {$tblprefix}uprojects ORDER BY upid");
	while($r = $db->fetch_array($query)){
		$rets[$r['upid']] = $r;
	}
	return $rets;
}

function fetch_pone($upid){
	global $db,$tblprefix;
	$r = $db->fetch_one("SELECT * FROM {$tblprefix}uprojects WHERE upid='$upid'");
	return $r;
}

?>
