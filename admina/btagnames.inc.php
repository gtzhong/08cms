<?
(!defined('M_COM') || !defined('M_ADMIN')) && exit('No Permission');
foreach(array('channels','fchannels','mchannels','matypes',) as $k) $$k = cls_cache::Read($k);
$bclass = empty($bclass) ? 'common' : $bclass;
$isApp = true;//�Ƿ�Ӧ�ý׶�

aheader();
$bclasses = array(
	'common' => 'ͨ����Ϣ',
	'archive' => '�ĵ����',
	'cnode' => '��Ŀ���',
	'freeinfo' => '�������',
	'commu' => '�������',
	'member' => '��Ա���',
	'other' => '����',
);
$datatypearr = array(
	'text' => '�����ı�',
	'multitext' => '�����ı�',
	'htmltext' => 'Html�ı�',
	'image' => '��ͼ',
	'images' => 'ͼ��',
	'flash' => 'Flash',
	'flashs' => 'Flash��',
	'media' => '��Ƶ',
	'medias' => '��Ƶ��',
	'file' => '��������',
	'files' => '�������',
	'select' => '����ѡ��',
	'mselect' => '����ѡ��',
	'cacc' => '��Ŀѡ��',
	'date' => '����(ʱ���)',
	'int' => '����',
	'float' => 'С��',
	'map' => '��ͼ',
	'vote' => 'ͶƱ',
	'texts' => '�ı���',
);
if(empty($action)){	
	echo '<div class="itemtitle"><h3>ԭʼ��ʶ����</h3></div>';

	$arr = array();
	foreach($bclasses as $k => $v) $arr[] = $bclass == $k ? "<b>-$v-</b>" : "<a href=\"?entry=btagnames&bclass=$k\">$v</a>";
	echo tab_list($arr,count($bclasses),0);

	$sclasses = array();
	if($bclass == 'archive'){
		foreach($channels as $chid => $channel){
			$sclasses[$chid] = $channel['cname'];
		}
	}elseif($bclass == 'cnode'){
		$sclasses = array(
			'catalog' => '��Ŀ',
			'coclass' => '����',
		);
	}elseif($bclass == 'freeinfo'){
		foreach($fchannels as $chid => $channel){
			$sclasses[$chid] = $channel['cname'];
		}
	}elseif($bclass == 'member'){
		foreach($mchannels as $chid => $channel){
			$sclasses[$chid] = $channel['cname'];
		}
	}elseif($bclass == 'commu'){
		$commus = cls_cache::Read('commus');
		$sclasses = $isApp ? array() : array('' => 'ͨ�ñ�ʶ');
		foreach($commus as $v){
			$sclasses[$v['cuid']] = $v['cname'];
		}
	}elseif($bclass == 'other'){
		$sclasses = array(
			'mp' => '��ҳ',
			'attachment' => '����',
			'vote' => 'ͶƱ',
		);
	}
	
	if(!submitcheck('bbtagnamesedit') && !submitcheck('bbtagnamesadd')){
		tabheader("���$bclasses[$bclass]��ʶ����",'btagnamesadd',"?entry=btagnames&bclass=$bclass");
		trbasic('��ʶ����','btagnameadd[cname]','', 'text', array('validate' => ' onfocus="initPinyin(\'btagnameadd[ename]\')"'));
		trbasic('Ӣ������','btagnameadd[ename]','', 'text', array('addstr' => ' <input type="button" value="�Զ�ƴ��" onclick="autoPinyin(\'btagnameadd[cname]\',\'btagnameadd[ename]\')" />'));
		trbasic('�ֶ�����','btagnameadd[datatype]',makeoption($datatypearr),'select');
		in_array($bclass,array('commu','other')) && trbasic('��ʶ���','btagnameadd[sclass]',makeoption($sclasses),'select');
		tabfooter('bbtagnamesadd','���');
	
		tabheader("$bclasses[$bclass]��ʶ",'btagnamesedit',"?entry=btagnames&bclass=$bclass",'6');
		trcategory(array('<input class="checkbox" type="checkbox" name="chkall" onclick="deltip(this,0,checkall,this.form)">ɾ?','��ʶ����','Ӣ������','����','�ֶ�����','����'));
		$query = $db->query("SELECT * FROM {$tblprefix}btagnames WHERE bclass='$bclass' ORDER BY sclass,vieworder,bnid");
		while($btagname = $db->fetch_array($query)){
			$sclassstr = '';
			if(in_array($bclass,array('commu','other'))){
				if($isApp){
					$sclassstr .= empty($btagname['sclass']) ? 'ͨ�ñ�ʶ':"<select style=\"vertical-align: middle;\" name=\"btagnamesnew[$btagname[bnid]][sclass]\">".makeoption($sclasses,$btagname['sclass'])."</select>";
				} else {
					$sclassstr .= "<select style=\"vertical-align: middle;\" name=\"btagnamesnew[$btagname[bnid]][sclass]\">".makeoption($sclasses,$btagname['sclass'])."</select>";
				}				
			} else {
				$sclassstr .= "-";
			}			
			$datatypestr = "<select style=\"vertical-align: middle;\" name=\"btagnamesnew[$btagname[bnid]][datatype]\">".makeoption($datatypearr,$btagname['datatype'])."</select>";
			echo "<tr align=\"center\">".
				"<td class=\"item1\" width=\"50\"><input class=\"checkbox\" type=\"checkbox\" name=\"delete[$btagname[bnid]]\" value=\"$btagname[bnid]\" onclick=\"deltip()\"></td>\n".
				"<td class=\"item2\"><input type=\"text\" size=\"30\" maxlength=\"30\" name=\"btagnamesnew[$btagname[bnid]][cname]\" value=\"$btagname[cname]\"></td>\n".
				"<td class=\"item1\"><input type=\"text\" size=\"30\" maxlength=\"30\" name=\"btagnamesnew[$btagname[bnid]][ename]\" value=\"$btagname[ename]\"></td>\n".
				"<td class=\"item2\" width=\"100\">$sclassstr</td>\n".
				"<td class=\"item1\" width=\"100\">$datatypestr</td>\n".
				"<td class=\"item2\" width=\"100\"><input type=\"text\" size=\"5\" maxlength=\"5\" name=\"btagnamesnew[$btagname[bnid]][vieworder]\" value=\"$btagname[vieworder]\"></td>\n".
				"</tr>\n";
		}
		tabfooter('bbtagnamesedit','�޸�');
	}
	elseif(submitcheck('bbtagnamesadd')){
		if(!$btagnameadd['cname'] || !$btagnameadd['ename']) {
			cls_message::show('���ݶ�ʧ',"?entry=btagnames&bclass=$bclass");
		}
		if(preg_match("/[^a-z_A-Z0-9]+/",$btagnameadd['ename'])){
			cls_message::show('������Ϸ��ı�ʶid!',"?entry=btagnames&bclass=$bclass");
		}
		$btagnameadd['sclass'] = empty($btagnameadd['sclass']) ? '' : $btagnameadd['sclass'];
		$db->query("INSERT INTO {$tblprefix}btagnames SET 
					cname='$btagnameadd[cname]',
					ename='$btagnameadd[ename]',
					datatype='$btagnameadd[datatype]',
					bclass='$bclass',
					sclass='$btagnameadd[sclass]'
					");
		updatethiscache();
		cls_message::show('��ʶ��ӳɹ�!',"?entry=btagnames&bclass=$bclass");
	
	}
	elseif(submitcheck('bbtagnamesedit')){
		if(isset($delete)){
			foreach($delete as $bnid){
				$db->query("DELETE FROM {$tblprefix}btagnames WHERE bnid=$bnid");
				unset($btagnamesnew[$bnid]);
			}
		}
		foreach($btagnamesnew as $bnid => $btagnamenew){
			$btagnamenew['sclass'] = empty($btagnamenew['sclass']) ? '' : $btagnamenew['sclass'];
			$db->query("UPDATE {$tblprefix}btagnames SET 
						cname='$btagnamenew[cname]',
						ename='$btagnamenew[ename]',
						datatype='$btagnamenew[datatype]',
						vieworder='$btagnamenew[vieworder]',
						sclass='$btagnamenew[sclass]'
						WHERE bnid='$bnid'");
		}
		updatethiscache();
		cls_message::show('��ʶ�޸ĳɹ�!',"?entry=btagnames&bclass=$bclass");
	}

}

function updatethiscache(){
	global $db,$tblprefix;
	$items = array();
	$query = $db->query("SELECT * FROM {$tblprefix}btagnames ORDER BY bclass,sclass,vieworder,bnid");
	while($item = $db->fetch_array($query)){
		$items[$item['bnid']] = array('ename' => $item['ename'],'cname' => $item['cname'],'bclass' => $item['bclass'],'sclass' => $item['sclass'],'datatype' => $item['datatype'],);
	}
	$cacstr = var_export($items,TRUE);
	if($fp = fopen(_08_SYSCACHE_PATH.'btagnames.cac.php','wb')){
		fwrite($fp,"<?php\n\$btagnames = $cacstr ;\n?>");
		fclose($fp);
	}
}
?>
