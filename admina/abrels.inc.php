<?
(!defined('M_COM') || !defined('M_ADMIN')) && exit('No Permission');
aheader();
if($re = $curuser->NoBackFunc('cfcommu')) cls_message::show($re);
$typearr = array('�ĵ�','��Ա');
if($action == 'abrelsedit'){
	backnav('exconfig','abrel');
	$abrels = fetch_arr();
	if(!submitcheck('babrelsedit')){
		tabheader("�ϼ���Ŀ����&nbsp; &nbsp; >><a href=\"?entry=$entry&action=abreladd\" onclick=\"return floatwin('open_abrelsedit',this)\">".'���'."</a>",'abrelsedit',"?entry=$entry&action=$action",'7');
		trcategory(array('ID','����',array('��Ŀ����','txtL'),array('��ע','txtL'),'��Ŀ����',array('���ݱ�','txtL'),'ɾ��','�༭'));
		foreach($abrels as $k => $v){
			echo "<tr class=\"txt\">".
			"<td class=\"txtC w30\">$k</td>\n".
			"<td class=\"txtC w30\"><input class=\"checkbox\" type=\"checkbox\" name=\"abrelsnew[$k][available]\" value=\"1\"".(empty($v['available']) ? '' : ' checked')."></td>\n".
			"<td class=\"txtL\"><input type=\"text\" size=\"20\" maxlength=\"20\" name=\"abrelsnew[$k][cname]\" value=\"$v[cname]\"></td>\n".
			"<td class=\"txtL\"><input type=\"text\" size=\"40\" maxlength=\"100\" name=\"abrelsnew[$k][remark]\" value=\"$v[remark]\"></td>\n".
			"<td class=\"txtC w100\">".$typearr[$v['source']].'=>'.$typearr[$v['target']]."</td>\n".
			"<td class=\"txtL\">".($v['tbl'] ? $v['tbl'] : ($v['source'] ? 'members' : "archives*".modpro(" >><a href=\"?entry=$entry&action=archivetbl&arid=$k\" onclick=\"return floatwin('open_abrelsedit',this)\">����</a>")))."</td>\n".
			"<td class=\"txtC w30\"><a onclick=\"return deltip(this,$no_deepmode)\" href=\"?entry=$entry&action=abreldel&arid=$k\">ɾ��</a></td>\n".
			"<td class=\"txtC w30\"><a href=\"?entry=$entry&action=abreldetail&arid=$k\" onclick=\"return floatwin('open_abrelsedit',this)\">����</a></td></tr>\n";
		}
		tabfooter('babrelsedit','�޸�');
		a_guide('abrelsedit');
	}else{
		if(!empty($abrelsnew)){
			foreach($abrelsnew as $k => $v){
				$v['cname'] = empty($v['cname']) ? $abrels[$k]['cname'] : $v['cname'];
				$v['remark'] = empty($v['remark']) ? $abrels[$k]['remark'] : $v['remark'];
				$v['available'] = empty($v['available']) ? 0 : 1;
				$db->query("UPDATE {$tblprefix}abrels SET cname='$v[cname]',remark='$v[remark]',available='$v[available]' WHERE arid='$k'");
			}
		}
		cls_CacheFile::Update('abrels');	
		adminlog('�༭�ϼ���Ŀ�б�');
		cls_message::show('�ϼ���Ŀ�༭���', "?entry=$entry&action=$action");
	}
}elseif($action == 'abreldel' && $arid) {
	backnav('exconfig','abrel');
	deep_allow($no_deepmode);
	if(!($abrel = fetch_one($arid))) cls_message::show('��ָ����ȷ����Ŀ');
	if(!submitcheck('confirm')){
		$message = "ɾ�����ָܻ���ȷ��ɾ����ѡ��Ŀ?<br><br>";
		$message .= "ȷ������>><a href=\"?entry=$entry&action=$action&arid=$arid&confirm=ok\">ɾ��</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
		$message .= "��������>><a href=\"?entry=$entry&action=abrelsedit\">����</a>";
		cls_message::show($message);
	}
	if($abrel['tbl']){
		$db->query("DELETE FROM {$tblprefix}$abrel[tbl] WHERE arid='$arid'",'SILENT');
	}else{
		$tbl = empty($abrel['source']) ? 'archives' : 'members';
		$db->query("ALTER TABLE {$tblprefix}$tbl DROP pid$arid",'SILENT');
		$db->query("ALTER TABLE {$tblprefix}$tbl DROP inorder$arid",'SILENT');
		$db->query("ALTER TABLE {$tblprefix}$tbl DROP incheck$arid",'SILENT');
	}
	$db->query("DELETE FROM {$tblprefix}abrels WHERE arid='$arid'",'SILENT');
	cls_CacheFile::Del('abfields',$arid);
	adminlog('ɾ���ϼ���Ŀ'.$abrel['cname']);
	cls_CacheFile::Update('abrels');
	cls_message::show('�ϼ�ɾ�����',"?entry=$entry&action=abrelsedit");
}elseif($action == 'abreladd'){
	deep_allow($no_deepmode);
	if(!submitcheck('babreladd')){
		tabheader('��Ӻϼ���Ŀ','abreladd',"?entry=$entry&action=abreladd");
		trbasic('��Ŀ����','abrelnew[cname]');
		trbasic('��ע','abrelnew[remark]','','text',array('w'=>50));
		trbasic('�鼭��Դ����','',makeradio('abrelnew[source]',$typearr),'',array('guide'=>'����󲻿ɸ���'));
		trbasic('�鼭Ŀ������','',makeradio('abrelnew[target]',$typearr),'',array('guide'=>'����󲻿ɸ���'));
		trbasic('�ϼ���¼���ݱ�','abrelnew[tbl]','','text',array('guide'=>'����󲻿ɸ��ģ����������ϵͳ�Զ��������ڼ�¼�ϼ���ϵ�����ݱ���ʽ��aalbums_***��<br>�������ĵ�����archives���Ա��members��Ҫ��pid*��incheck*��inorder*����¼�ϼ���ϵ��'));
		tabfooter('babreladd');
		a_guide('abreladd');
	}else{
		$abrelnew['cname'] = empty($abrelnew['cname']) ? '' : trim(strip_tags($abrelnew['cname']));
		empty($abrelnew['cname']) && cls_message::show('��������Ŀ����',M_REFERER);
		$abrelnew['remark'] = empty($abrelnew['remark']) ? '' : trim(strip_tags($abrelnew['remark']));
		if($abrelnew['tbl'] = empty($abrelnew['tbl']) ? '' : trim(strip_tags($abrelnew['tbl']))){
			$tables = array();	
			$query = $db->query("SHOW TABLES FROM $dbname");
			while($r = $db->fetch_row($query)) $tables[] = $r[0];
			if(in_array("{$tblprefix}$abrelnew[tbl]",$tables)) cls_message::show('ָ���½����ݱ��Ѿ�ռ��',M_REFERER);
		}
		$db->query("INSERT INTO {$tblprefix}abrels SET arid=".auto_insert_id('abrels').",cname='$abrelnew[cname]',remark='$abrelnew[remark]',source='$abrelnew[source]',target='$abrelnew[target]',tbl='$abrelnew[tbl]'");
		$arid = $db->insert_id();
		if($abrelnew['tbl']){
			$db->query("CREATE TABLE {$tblprefix}$abrelnew[tbl] LIKE {$tblprefix}init_aalbum");
			$db->query("ALTER TABLE {$tblprefix}$abrelnew[tbl] COMMENT='$abrelnew[cname](�ϼ�)������'");
		}else{
			if(!empty($abrelnew['source'])){
				$db->query("ALTER TABLE {$tblprefix}members ADD pid$arid mediumint(8) unsigned NOT NULL default '0'");
				$db->query("ALTER TABLE {$tblprefix}members ADD inorder$arid smallint(6) unsigned NOT NULL default '0'");
				$db->query("ALTER TABLE {$tblprefix}members ADD incheck$arid tinyint(1) unsigned NOT NULL default '0'");
			}
		}
		cls_CacheFile::Update('abrels');
		adminlog('��Ӻϼ���Ŀ');
		cls_message::show('�ϼ���Ŀ��ӳɹ�������ϸ���á�',"?entry=$entry&action=abreldetail&arid=$arid");
	}
}elseif($action == 'archivetbl' && $arid){//ֻ�������ݱ����Ƿ��и��ֶ�
	modpro() || cls_message::show('����ϵ��ʼ�˿��Ŷ��ο���ģʽ');
	if(!($abrel = fetch_one($arid))) cls_message::show('��ָ����ȷ�ĺϼ���Ŀ��');
	if($abrel['tbl'] || $abrel['source']) cls_message::show('�ϼ����ݱ���ĵ�����');
	echo "<title>�ϼ��ֶ�Ӧ�õ��ĵ���</title>";
	foreach(array('channels','splitbls',) as $k) $$k = cls_cache::Read($k);
	if(!submitcheck('bsubmit')){
		tabheader($abrel['cname']."($arid) - �ϼ��ֶ�Ӧ�õ�����",'abreldetail',"?entry=$entry&action=$action&arid=$arid");
		trcategory(array('����','ID',array('�ĵ�����','txtL'),array('���ݱ�','txtL'),array('�ĵ�ģ��','txtL')));
		foreach($splitbls as $k => $v){
			$channelstr = '';foreach($v['chids'] as $x) @$channels[$x]['cname'] && $channelstr .= $channels[$x]['cname']."($x),";
			$query = $db->query("DESCRIBE {$tblprefix}archives$k pid$arid");
			$available = $db->fetch_array($query) ? TRUE : FALSE;
			echo "<tr class=\"txt\">\n".
				"<td class=\"txtC w40\"><input class=\"checkbox\" type=\"checkbox\" name=\"fmdata[$k][enabled]\" value=\"1\"".($available ? ' checked' : '')."></td>\n".
				"<td class=\"txtC w35\">$k</td>\n".
				"<td class=\"txtL\">$v[cname]</td>\n".
				"<td class=\"txtL\">archives$k</td>\n".
				"<td class=\"txtL\">".($channelstr ? $channelstr : '��')."</td>\n".
				"</tr>";
		}
		tabfooter('bsubmit');
	}else{
		foreach($splitbls as $k => $v){
			$query = $db->query("DESCRIBE {$tblprefix}archives$k pid$arid");
			$available = $db->fetch_array($query) ? TRUE : FALSE;
			$enabled = empty($fmdata[$k]['enabled']) ? FALSE : TRUE;
			if($enabled != $available){
				if($enabled){
					$db->query("ALTER TABLE {$tblprefix}archives$k ADD pid$arid mediumint(8) unsigned NOT NULL default '0'",'SILENT');
					$db->query("ALTER TABLE {$tblprefix}archives$k ADD inorder$arid smallint(6) unsigned NOT NULL default '0'",'SILENT');
					$db->query("ALTER TABLE {$tblprefix}archives$k ADD incheck$arid tinyint(1) unsigned NOT NULL default '0'",'SILENT');
				}else{
					$db->query("ALTER TABLE {$tblprefix}archives$k DROP pid$arid",'SILENT');
					$db->query("ALTER TABLE {$tblprefix}archives$k DROP inorder$arid",'SILENT');
					$db->query("ALTER TABLE {$tblprefix}archives$k DROP incheck$arid",'SILENT');
				}
			}
		}
		adminlog($abrel['cname']."�ϼ�Ӧ�õ�����");
		cls_message::show('�ϼ���Ŀ������ɡ�',"?entry=$entry&action=$action&arid=$arid");
	}
	
}elseif($action == 'abreldetail' && $arid){
	if(!($abrel = fetch_one($arid))) cls_message::show('��ָ����ȷ�ĺϼ���Ŀ��');
    _08_FilesystemFile::filterFileParam($arid);
	if(@!include("exconfig/abrel_$arid.php")){
		if(!submitcheck('babreldetail')) {
			tabheader('�ϼ���Ŀ����-'.$abrel['cname'],'abreldetail',"?entry=$entry&action=$action&arid=$arid");
			trbasic('��ע','abrelnew[remark]',$abrel['remark'],'text',array('w'=>50));
			trbasic('���ò�������'.($abrel['cfgs0'] && !$abrel['cfgs'] ? '�����ʽ����������!' : ''),'abrelnew[cfgs0]',empty($abrel['cfgs']) ? (empty($abrel['cfgs0']) ? '' : $abrel['cfgs0']) : var_export($abrel['cfgs'],1),'textarea',array('w' => 500,'h' => 300,'guide'=>'��array()���룬����������Ҫ��php�淶'));
			trbasic('����˵��','abrelnew[content]',$abrel['content'],'textarea',array('w' => 500,'h' => 300,));
			tabfooter('babreldetail','�޸�');
			a_guide('abreldetail');
		}else{
			$abrelnew['cfgs0'] = empty($abrelnew['cfgs0']) ? '' : trim($abrelnew['cfgs0']);
			$abrelnew['cfgs'] = varexp2arr($abrelnew['cfgs0']);
			$abrelnew['remark'] = empty($abrelnew['remark']) ? '' : trim(strip_tags($abrelnew['remark']));
			$abrelnew['content'] = empty($abrelnew['content']) ? '' : trim($abrelnew['content']);
			$abrelnew['cfgs'] = !empty($abrelnew['cfgs']) ? addslashes(var_export($abrelnew['cfgs'],TRUE)) : '';
			$db->query("UPDATE {$tblprefix}abrels SET 
						remark='$abrelnew[remark]',
						content='$abrelnew[content]',
						cfgs0='$abrelnew[cfgs0]',
						cfgs='$abrelnew[cfgs]'
						WHERE arid='$arid'");
			cls_CacheFile::Update('abrels');
			adminlog('�༭�ϼ���Ŀ'.$abrel['cname']);
			cls_message::show('�ϼ���Ŀ������ɡ�',"?entry=$entry&action=$action&arid=$arid");
		}
	}

}
function fetch_arr(){
	global $db,$tblprefix;
	$rets = array();
	$query = $db->query("SELECT * FROM {$tblprefix}abrels ORDER BY vieworder ASC,arid ASC");
	while($r = $db->fetch_array($query)){
		if(empty($r['cfgs']) || !is_array($r['cfgs'] = @varexp2arr($r['cfgs']))) $r['cfgs'] = array();
		$rets[$r['arid']] = $r;
	}
	return $rets;
}
function fetch_one($arid){
	global $db,$tblprefix;
	$r = $db->fetch_one("SELECT * FROM {$tblprefix}abrels WHERE arid='$arid'");
	if(empty($r['cfgs']) || !is_array($r['cfgs'] = @varexp2arr($r['cfgs']))) $r['cfgs'] = array();
	return $r;
}
