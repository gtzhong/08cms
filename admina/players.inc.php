<?
(!defined('M_COM') || !defined('M_ADMIN')) && exit('No Permission');
aheader();
if($re = $curuser->NoBackFunc('project')) cls_message::show($re);
$players = cls_cache::Read('players');
$ptypearr = array('media' => '��Ƶ������','flash' => 'Flash������');
backnav('project','player');
if($action == 'playersedit'){
	if(!submitcheck('bplayersedit') && !submitcheck('bplayeradd')) {
		tabheader('����������','playersedit','?entry=players&action=playersedit','7');
		trcategory(array('��Ч','����������|L','����������','Ĭ�ϲ����ļ���ʽ','����','ɾ��','����'));
		foreach($players as $plid => $player){
			echo "<tr class=\"txt\">".
				"<td class=\"txtC w40\"><input class=\"checkbox\" type=\"checkbox\" name=\"playersnew[$plid][available]\" value=\"1\"".(!empty($player['available']) ? ' checked' : '')."></td>\n".
				"<td class=\"txtL\"><input type=\"text\" size=\"25\" maxlength=\"30\" name=\"playersnew[$plid][cname]\" value=\"$player[cname]\"></td>\n".
				"<td class=\"txtC w100\">".$ptypearr[$player['ptype']]."</td>\n".
				"<td class=\"txtC\"><input type=\"text\" size=\"25\" maxlength=\"50\" name=\"playersnew[$plid][exts]\" value=\"$player[exts]\"></td>\n".
				"<td class=\"txtC w50\"><input type=\"text\" size=\"4\" maxlength=\"4\" name=\"playersnew[$plid][vieworder]\" value=\"$player[vieworder]\"></td>\n".
				"<td class=\"txtC w40\"><input class=\"checkbox\" type=\"checkbox\"".(!empty($player['issystem']) ? ' disabled' : " name=\"delete[$plid]\" value=\"$plid\" onclick=\"deltip(this,$no_deepmode)\"")."></td>\n".
				"<td class=\"txtC w50\"><a href=\"?entry=players&action=playerdetail&plid=$plid\">����</a></td>\n".
				"</tr>\n";
		}
		tabfooter('bplayersedit','�޸�');
	
		tabheader('��Ӳ�����','playeradd','?entry=players&action=playersedit');
		trbasic('����������','playeradd[cname]');
		trbasic('����������','playeradd[ptype]',makeoption($ptypearr),'select');
		trbasic('Ĭ�ϲ����ļ���ʽ','playeradd[exts]');
		tabfooter('bplayeradd','���');
		a_guide('playersedit');
	}
	elseif(submitcheck('bplayeradd')){
		if(!$playeradd['cname']) {
			cls_message::show('�����벥��������', '?entry=players&action=playersedit');
		}
		if(preg_match("/[^a-z,A-Z0-9]+/",$playeradd['exts'])){
			cls_message::show('�ļ���չ�����Ϲ淶', '?entry=players&action=playersedit');
		}
		$playeradd['exts'] = strtolower($playeradd['exts']);
	
		$db->query("INSERT INTO {$tblprefix}players SET 
					plid=".auto_insert_id('players').",
					cname='$playeradd[cname]',
					ptype='$playeradd[ptype]',
					exts='$playeradd[exts]',
					available='1'
					");
		cls_CacheFile::Update('players');
		adminlog('�����Ƶ������','�����Ƶ������');
		cls_message::show('������������','?entry=players&action=playersedit');
	
	}elseif(submitcheck('bplayersedit')){
		if(!empty($delete) && deep_allow($no_deepmode)){
			foreach($delete as $plid){
				$db->query("DELETE FROM {$tblprefix}players WHERE plid=$plid");
				unset($playersnew[$plid]);
			}
		}
		foreach($playersnew as $plid => $playernew){
			$playernew['cname'] = empty($playernew['cname']) ? $players[$plid]['cname'] : $playernew['cname'];
			$playernew['exts'] = preg_match("/[^a-z,A-Z0-9]+/",$playernew['exts']) ? $players[$plid]['exts'] : strtolower($playernew['exts']);
			$playernew['available'] = empty($playernew['available']) ? 0 : $playernew['available'];
			$db->query("UPDATE {$tblprefix}players SET 
						cname='$playernew[cname]',
						exts='$playernew[exts]',
						available='$playernew[available]',
						vieworder='$playernew[vieworder]' 
						WHERE plid='$plid'");
		}
		cls_CacheFile::Update('players');
		adminlog('�༭��Ƶ�������б�','�༭��Ƶ�������б�');
		cls_message::show('�������༭���','?entry=players&action=playersedit');
	}
}elseif($action == 'playerdetail' && !empty($plid)){
	empty($players[$plid]) && cls_message::show('��ָ����ȷ�Ĳ�����','?entry=players&action=playersedit');
	$player = cls_cache::Read('player',$plid);
	if(!submitcheck('bplayerdetail')){
		tabheader('����������','playerdetail','?entry=players&action=playerdetail&plid='.$plid);
		trbasic('����������','playernew[cname]',$player['cname'],'text');
		trbasic('����������','',$ptypearr[$player['ptype']],'');
		trbasic('Ĭ�ϲ����ļ���ʽ','playernew[exts]',$player['exts'],'text');
		echo "<tr class=\"txt\"><td class=\"txtL\">".'������ģ��'."</td><td class=\"txtL\"><textarea rows=\"25\" name=\"playernew[template]\" id=\"playernew[template]\" cols=\"100\">".mhtmlspecialchars(str_replace("\t","    ",$player['template']))."</textarea></td></tr>";
		tabfooter('bplayerdetail');
		a_guide('playerdetail');
	}else{
		if(!$playernew['template']) {
			cls_message::show('�����벥����ģ��','?entry=players&action=playerdetail&plid='.$plid);
		}
		$playernew['cname'] = empty($playernew['cname']) ? $players[$plid]['cname'] : $playernew['cname'];
		$playernew['exts'] = preg_match("/[^a-z,A-Z0-9]+/",$playernew['exts']) ? $players[$plid]['exts'] : strtolower($playernew['exts']);
		$db->query("UPDATE {$tblprefix}players SET 
					cname='$playernew[cname]',
					exts='$playernew[exts]',
					template='$playernew[template]' 
					WHERE plid='$plid'");
		cls_CacheFile::Update('players');
		adminlog('��ϸ�޸���Ƶ������','��ϸ�޸���Ƶ������');
		cls_message::show('�������޸����','?entry=players&action=playersedit');

	}
}

?>
