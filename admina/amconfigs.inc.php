<?php
(!defined('M_COM') || !defined('M_ADMIN')) && exit('No Permission');
aheader();
foreach(array('channels','fchannels','mchannels','catalogs','fcatalogs','cotypes','mtpls','aurls','linknodes',) as $k) $$k = cls_cache::Read($k);
$amconfigs = cls_DbOther::CacheArray(array('tbl' => 'amconfigs','key' => 'amcid','orderby' => 'vieworder',));
$linkitemtype = array(
	'a' => '��������',
	'm' => '��Ա����',
);
if($action == 'amconfigadd'){
	if($re = $curuser->NoBackFunc('bkconfig')) cls_message::show($re);
	if(!submitcheck('bsubmit')){
		tabheader('��̨�����ɫ���','amconfigadd',"?entry=$entry&action=$action");
		trbasic('��̨�����ɫ����','fmdata[cname]');
		trbasic('��ע','fmdata[remark]','','text',array('w'=>50));
		tabfooter('bsubmit','���');
		a_guide('amconfigsedit');
	}else{
		$fmdata['cname'] = trim(strip_tags($fmdata['cname']));
		$fmdata['remark'] = trim(strip_tags($fmdata['remark']));
		if(empty($fmdata['cname'])) cls_message::show('�������̨��ɫ�������ơ�',M_REFERER);
		$db->query("INSERT INTO {$tblprefix}amconfigs SET amcid=".auto_insert_id('amconfigs').",cname='$fmdata[cname]',remark='$fmdata[remark]'");
		$amcid = $db->insert_id();
		adminlog('��Ӻ�̨�����ɫ');
		cls_CacheFile::Update('amconfigs');
		cls_message::show('��̨��ɫ�����ɣ����������ϸ���á�',"?entry=$entry&action=amconfigdetail&amcid=$amcid");
	}
}elseif($action == 'amconfigsedit'){
	backnav('backarea','config');
	if($re = $curuser->NoBackFunc('bkconfig')) cls_message::show($re);
	if(!submitcheck('bsubmit')){
		tabheader("��̨�����ɫ���� &nbsp;<a href=\"?entry=$entry&action=amconfigadd\" onclick=\"return floatwin('open_amconfigsedit',this)\">>>���</a>",'amconfigsedit',"?entry=$entry&action=$action",4);
		trcategory(array('���',array('��ɫ����','txtL'),array('��ע','txtL'),'����','ɾ��','�༭'));
		$ii = 0;
		foreach($amconfigs as $k => $v){
			echo "<tr class=\"txt\">\n".
				"<td class=\"txtC w40\">".++$ii."</td>\n".
				"<td class=\"txtL\"><input type=\"text\" name=\"fmdata[$k][cname]\" value=\"".mhtmlspecialchars($v['cname'])."\" size=\"20\" maxlength=\"60\"></td>\n".
				"<td class=\"txtL\"><input type=\"text\" name=\"fmdata[$k][remark]\" value=\"$v[remark]\" size=\"40\" maxlength=\"50\"></td>\n".
				"<td class=\"txtC w40\"><input type=\"text\" size=\"4\" maxlength=\"4\" name=\"fmdata[$k][vieworder]\" value=\"$v[vieworder]\"></td>\n".
				"<td class=\"txtC w40\"><input class=\"checkbox\" type=\"checkbox\" name=\"delete[$k]\" value=\"$k\" onclick=\"deltip()\"></td>\n".
				"<td class=\"txtC w50\"><a href=\"?entry=$entry&action=amconfigdetail&amcid=$k\" onclick=\"return floatwin('open_amconfigsedit',this)\">����</a></td>\n".
				"</tr>";
		}
		tabfooter('bsubmit');
		a_guide('amconfigsedit');
	}else{
		if(!empty($delete)){
			foreach($delete as $k){
				$db->query("DELETE FROM {$tblprefix}amconfigs WHERE amcid='$k'");
				unset($fmdata[$k]);
			}
		}
		if(!empty($fmdata)){
			foreach($fmdata as $k => $v){
				$v['cname'] = trim(strip_tags($v['cname']));
				$v['cname'] = empty($v['cname']) ? $amconfigs[$k]['cname'] : $v['cname'];
				$v['vieworder'] = max(0,intval($v['vieworder']));
				$v['remark'] = trim(strip_tags($v['remark']));
				$db->query("UPDATE {$tblprefix}amconfigs SET cname='$v[cname]',remark='$v[remark]',vieworder='$v[vieworder]' WHERE amcid='$k'");
			}
		}
		adminlog('�༭��̨�����ɫ�����б�');
		cls_CacheFile::Update('amconfigs');
		cls_message::show('��̨��ɫ�޸����', "?entry=amconfigs&action=amconfigsedit");
	}
}elseif($action == 'amconfigdetail' && !empty($amcid)){
	if($re = $curuser->NoBackFunc('bkconfig')) cls_message::show($re);
	empty($amconfigs[$amcid]) && cls_message::show('��ָ����ȷ�Ĺ����̨��ɫ');
	$amconfig = $amconfigs[$amcid];
	echo "<title>�����ɫ���� - $amconfig[cname]</title>";
	$menus = cls_cache::Read('mnmenus');
	if(!submitcheck('bamconfigdetail')){
		foreach(array('menus','funcs','caids','fcaids','mchids','cuids','checks','extends') as $var) $amconfig[$var] = array_filter(explode(',',$amconfig[$var]));
		tabheader('�����̨��ʾ���²˵�&nbsp; &nbsp; &nbsp; <input class="checkbox" type="checkbox" name="mchkall" onclick="checkall(this.form,\'menusnew\',\'mchkall\')">ȫѡ','amconfigdetail',"?entry=$entry&action=$action&amcid=$amcid",6);
		if($cocsmenus = cls_cache::exRead('cocsmenus')){
			$na = array();foreach($cocsmenus as $k => $v) $na[$k] = $v['label'];
			trbasic('��չ�ڵ���','',makecheckbox("extendsnew[]",$na,empty($amconfig['extends']) ? array() : $amconfig['extends'],5),'');
		}
		foreach($menus as $k1 => $v1){
			$menusarr = array();foreach($v1['childs'] as $k2 => $v2) $menusarr[$k2] = $v2['title'];
			trbasic($v1['title'],'',makecheckbox("menusnew[]",$menusarr,empty($amconfig['menus']) ? array() : $amconfig['menus'],5),'');
		}
		tabfooter();

		tabheader('ӵ�����¹��ܵĹ���Ȩ��&nbsp; &nbsp; &nbsp; <input class="checkbox" type="checkbox" name="fchkall" onclick="checkall(this.form,\'funcsnew\',\'fchkall\')">ȫѡ');
		$arr = cls_cache::exRead('amfuncs');
		foreach($arr as $k => $v) trbasic($k,'',makecheckbox("funcsnew[]",$v,empty($amconfig['funcs']) ? array() : $amconfig['funcs'],6),'');
		tabfooter();

		$caidsarr = $cuidsarr = $fcaidsarr = $mchidsarr = array('-1' => '<b>ȫ��</b>');
		tabheader('�������������Ŀ�е��ĵ�&nbsp; &nbsp; &nbsp; <input class="checkbox" type="checkbox" name="cachkall" onclick="checkall(this.form,\'caid0snew\',\'cachkall\')">ȫѡ');
		$catalogs = cls_cache::Read('catalogs');
		foreach($catalogs as $k => $v) $caidsarr[$k] = $v['title'].'('.$v['level'].')';
		echo "<tr><td class=\"txt txtleft\">".makecheckbox("caid0snew[]",$caidsarr,empty($amconfig['caids']) ? array() : $amconfig['caids'])."</td><tr>";
		tabfooter();

		tabheader('���������ݹ���Ȩ��');
		foreach(array('commus','matypes','mcommus',) as $k) $$k = cls_cache::Read($k);
		$checkarr = array(-1 => '<b>ȫ��</b>','adel' => 'ɾ����������','acheck' => '��˳�������','mdel' => 'ɾ����Ա','mcheck' => '��˻�Ա','fdel' => 'ɾ������','fcheck' => '��˸���',);
		trbasic('���ݹ���Ȩ��','',makecheckbox("checksnew[]",$checkarr,empty($amconfig['checks']) ? array() : $amconfig['checks'],7),'',array('guide'=>'�������ݰ����ĵ��������������������ȣ���Ա���������̼�'));
		foreach($commus as $k => $v) $cuidsarr[$k] = $v['cname'];
		$fcaidsarr += cls_fcatalog::fcaidsarr();
		$mchidsarr += cls_mchannel::mchidsarr();
		trbasic('����������·���ĸ���<br /><input class="checkbox" type="checkbox" name="fachkall" onclick="checkall(this.form,\'fcaidsnew\',\'fachkall\')">ȫѡ','',makecheckbox('fcaidsnew[]',$fcaidsarr,empty($amconfig['fcaids']) ? array() : $amconfig['fcaids'],5,1),'');
		trbasic('��������������͵Ļ�Ա<br /><input class="checkbox" type="checkbox" name="mcchkall" onclick="checkall(this.form,\'mchidsnew\',\'mcchkall\')">ȫѡ','',makecheckbox('mchidsnew[]',$mchidsarr,empty($amconfig['mchids']) ? array() : $amconfig['mchids'],8,1),'');
		trbasic('����������½�������<br /><input class="checkbox" type="checkbox" name="ychkall" onclick="checkall(this.form,\'cuidsnew\',\'ychkall\')">ȫѡ','',makecheckbox('cuidsnew[]',$cuidsarr,empty($amconfig['cuids']) ? array() : $amconfig['cuids'],8,1),'');
		tabfooter('bamconfigdetail');
		a_guide('amconfigsedit');
	}else{
		$extendsnew = empty($extendsnew) ? '' : implode(',',$extendsnew);
		$menusnew = empty($menusnew) ? '' : implode(',',$menusnew);
		$funcsnew = empty($funcsnew) ? '' : implode(',',$funcsnew);
		$checksnew = empty($checksnew) ? '' : (in_array('-1',$checksnew) ? '-1' : implode(',',$checksnew));
		foreach(array('caid0snew','fcaidsnew','mchidsnew','cuidsnew',) as $var) $$var = empty($$var) ? '' : (in_array('-1',$$var) ? '-1' : implode(',',$$var));
		$db->query("UPDATE {$tblprefix}amconfigs SET
		menus='$menusnew',
		funcs='$funcsnew',
		checks='$checksnew',
		extends='$extendsnew',
		caids='$caid0snew',
		fcaids='$fcaidsnew',
		cuids='$cuidsnew',
		mchids='$mchidsnew'
		WHERE amcid='$amcid'");
		adminlog('��ϸ�޸ĺ�̨�����ɫ');
		cls_CacheFile::Update('amconfigs');
		cls_message::show('�����̨��ɫ�������',axaction(6,"?entry=amconfigs&action=amconfigsedit"));
	}
}elseif($action == 'amconfigcaedit'){
	backnav('backarea','caedit');
	if($re = $curuser->NoBackFunc('bkconfig')) cls_message::show($re);
	tabheader('��̨����ڵ�������');
	trbasic('����������','',"<a href=\"?entry=$entry&action=amconfigablock\" onclick=\"return floatwin('open_fnodes',this)\">>>�ڵ�����</a> &nbsp; &nbsp;<a href=\"?entry=$entry&action=amconfigmdflink&linktype=a\" onclick=\"return floatwin('open_fnodes',this)\">>>��������</a>",'');
	trbasic('��Ա������','',"<a href=\"?entry=$entry&action=amconfigmblock\" onclick=\"return floatwin('open_mnodes',this)\">>>�ڵ�����</a> &nbsp; &nbsp;<a href=\"?entry=$entry&action=amconfigmdflink&linktype=m\" onclick=\"return floatwin('open_fnodes',this)\">>>��������</a>",'');
	tabfooter();
	a_guide('amconfigcaedit');
}elseif($action == 'amconfigablock'){
	if($re = $curuser->NoBackFunc('bkconfig')) cls_message::show($re);
	$catalogs = cls_cache::Read('catalogs');
	$anodes = @unserialize($db->result_one("SELECT content FROM {$tblprefix}variables WHERE variable='anodes' LIMIT 1"));
	empty($anodes) && $anodes = array();
	if(!submitcheck('bsubmit')){
		echo form_str('amconfigablock', "?entry=amconfigs&action=amconfigablock");
		echo "<div class=\"conlist1\">����������ڵ�����</div>";
		$catalogs = array('-1' => array('title' => 'ȫ����Ŀ','level' => 0)) + $catalogs;
		echo '<script type="text/javascript">var cata = [';
		foreach($catalogs as $caid => $catalog){
			$aurlstr = '';
			$tcaid = $caid == -1 ? 0 : $caid;
			if(empty($anodes[$tcaid])){
				$aurlstr = '��Ч�ڵ�';
			}else{
				$aurlsarr = explode(',',$anodes[$tcaid]);
				foreach($aurlsarr as $k) isset($aurls[$k]['name']) && $aurlstr .= ($aurlstr ? ',' : '').$k.'-'.@$aurls[$k]['name'];
			}
			
			echo "[$catalog[level],$caid,'" . str_replace("'", "\\'", mhtmlspecialchars($catalog['title'])) . "','$aurlstr'],";
		}
		empty($treesteps) && $treesteps = '';
		echo <<<DOT
];
document.write(tableTree({data:cata,step:'$treesteps'.split(',')[0],html:{
		head: '<td class="txtC" width="40"><input type="checkbox" name="chkall" class="checkbox" onclick="checkall(this.form,\'selectid\',\'chkall\')"></td>'
			+ '<td class="txtL" width="240"%code%><b>���ݹ���ڵ�</b> %input%</td>'
			+ '<td class="txtL"><b>�ڵ��������</b></td>',
		cell:[1,1],
		rows: '<td class="txtC" width="40"><input class="checkbox" name="selectid['
				+ '%1%]" value="%1%" type="checkbox" onclick="tableTree.setChildBox()" /></td>'
			+ '<td width="240" class="txtL">%ico%%2%</td>'
			+ '<td class="txtL">%3%</td>'
		},
	callback : true
}));
DOT;
		echo '</script>';

		tabheader("������Ŀ &nbsp; &nbsp;<a href=\"?entry=$entry&action=amconfigmdflink&linktype=a\" onclick=\"return floatwin('open_alinks',this)\">>>������������</a>");
		$aurlsarr = array();
		foreach($aurls as $k => $v) $v['type'] == 'a' && $aurlsarr[$v['auid']] = "$v[auid]-<b title=\"$v[mark]\">$v[name]</b>" . ($v['mark'] ? "-$v[mark]" : '');
		//Ҫ��[����]����Ϊ׼, �ѹ������Ƶķ���һ��
		//ksort($aurlsarr); // �������,������IDҪ����Щ��
		if(empty($aurlsarr)){
			$str = "<a href=\"?entry=$entry&action=amconfigaddlink&linktype=a\" onclick=\"return floatwin('open_alinks',this)\">>>��ӹ�������</a>";
		}else{
			$str = "<b>����ģʽ</b>��<select id=\"select_mode\" name=\"select_mode\" style=\"vertical-align: top;\">".makeoption(array(0 => '��������',1 => '������ѡ',2 => '�Ƴ���ѡ',))."</select><br>";
			$str .= makecheckbox('arcauids[]',$aurlsarr,array(),1);
		}
		trbasic('���ýڵ�Ĺ�������','', $str,'');
		tabfooter('bsubmit'); 
		a_guide('amconfigblock');
	}else{
		
		if(!empty($selectid)){
			$arcauids = empty($arcauids) ? array() : $arcauids;
			foreach($selectid as $id){
				$tid = $id == -1 ? 0 : $id;
				$old_ids = empty($anodes[$tid]) ? array() : explode(',',$anodes[$tid]);
				$new_ids =	empty($select_mode) ? $arcauids : ($select_mode == 1 ? array_filter(array_merge($old_ids,$arcauids)) : array_diff($old_ids,$arcauids));			
				$new_ids = array_filter($new_ids);
				if($new_ids){
					$new_ids = array_unique($new_ids);
					$anodes[$tid] = implode(',',$new_ids);
				}else unset($anodes[$tid]);
			}
		}
		foreach($anodes as $k => $v) if($k && empty($catalogs[$k])) unset($anodes[$k]);
		$anodes = empty($anodes) ? '' : addslashes(serialize($anodes));
		$db->query("UPDATE {$tblprefix}variables SET
		content='$anodes'
		WHERE variable='anodes'");
		adminlog('��ϸ�޸ĺ�̨����ڵ���');
		cls_CacheFile::Update('linknodes');
		cls_message::show('��̨����ڵ����������',M_REFERER);
	}
}elseif($action == 'amconfigmblock'){
	if($re = $curuser->NoBackFunc('bkconfig')) cls_message::show($re);
	$mnodes = unserialize($db->result_one("SELECT content FROM {$tblprefix}variables WHERE variable='mnodes' LIMIT 1"));
	if(!submitcheck('bsubmit')){
		$mchannels = cls_cache::Read('mchannels');
		$mchidsarr = array(0 => 'ȫ��ģ��') + cls_mchannel::mchidsarr();
		tabheader('��Ա�������ڵ�����','amconfigmblock','?entry=amconfigs&action=amconfigmblock',6);
		trcategory(array("<input class=\"checkbox\" type=\"checkbox\" name=\"chkall\" onclick=\"checkall(this.form, 'selectid', 'chkall')\">",array('��Աģ�ͽڵ�','txtL'),array('�ڵ��������','txtL')));
		foreach($mchidsarr as $mchid => $title){
			$aurlstr = '';
			if(empty($mnodes[$mchid])){
				$aurlstr = '��Ч�ڵ�';
			}else{
				$aurlsarr = explode(',',$mnodes[$mchid]);
				foreach($aurlsarr as $k) isset($aurls[$k]['name']) && $aurlstr .= ($aurlstr ? ',' : '').$k.'-'.@$aurls[$k]['name'];
			}
			echo "<tr class=\"txt\">\n".
				"<td class=\"txtC w30\"><input class=\"checkbox\" type=\"checkbox\" name=\"selectid[$mchid]\" value=\"$mchid\"></td>\n".
				"<td class=\"txtL\">$title</td>\n".
				"<td class=\"txtL\">$aurlstr</td>\n".
				"</tr>\n";
		}
		tabfooter();
		tabheader("������Ŀ &nbsp; &nbsp;<a href=\"?entry=amconfigs&action=amconfigmdflink&linktype=m\" onclick=\"return floatwin('open_alinks',this)\">>>�ڵ��������</a>");
		$aurlsarr = array();
		foreach($aurls as $k => $v)$v['type'] == 'm' && $aurlsarr[$v['auid']] = "$v[auid]-<b title=\"$v[mark]\">$v[name]</b>" . ($v['mark'] ? "-$v[mark]" : '');
		if(empty($aurlsarr)){
			$str = "<a href=\"?entry=amconfigs&action=amconfigaddlink&linktype=f\" onclick=\"return floatwin('open_alinks',this)\">>>��ӹ�������</a>";
		}else{
			$str = "<b>����ģʽ</b>��<select id=\"select_mode\" name=\"select_mode\" style=\"vertical-align: top;\">".makeoption(array(0 => '��������',1 => '������ѡ',2 => '�Ƴ���ѡ',))."</select><br>";
			$str .= makecheckbox('arcauids[]',$aurlsarr,array(),1);
		}
		trbasic('���ýڵ�Ĺ�������','', $str,'');
		tabfooter('bsubmit');
		a_guide('amconfigblock');
	}else{
		if(!empty($selectid)){
			$arcauids = empty($arcauids) ? array() : $arcauids;
			foreach($selectid as $id){
				$old_ids = empty($mnodes[$id]) ? array() : explode(',',$mnodes[$id]);
				$new_ids =	empty($select_mode) ? $arcauids : ($select_mode == 1 ? array_filter(array_merge($old_ids,$arcauids)) : array_diff($old_ids,$arcauids));			
				$new_ids = array_filter($new_ids);
				if($new_ids){
					$new_ids = array_unique($new_ids);
					$mnodes[$id] = implode(',',$new_ids);
				}else unset($mnodes[$id]);
			}
		}
		
		foreach($mnodes as $k => $v) if($k && empty($mchannels[$k])) unset($mnodes[$k]);
		$mnodes = empty($mnodes) ? '' : addslashes(serialize($mnodes));
		$db->query("UPDATE {$tblprefix}variables SET
		content='$mnodes'
		WHERE variable='mnodes'");
		adminlog('��ϸ�޸ĺ�̨����ڵ���');
		cls_CacheFile::Update('linknodes');
		cls_message::show('��̨����ڵ����������',M_REFERER);
	}
}elseif($action == 'amconfigaddlink'){
	if($re = $curuser->NoBackFunc('bkconfig')) cls_message::show($re);
	deep_allow($no_deepmode);
	if(!submitcheck('bsubmit')){
		(empty($linktype) || !in_array($linktype, array('a','m'))) && cls_message::show('��Ч��linktype����');
		if(!empty($auid)){
			$linkitem = $db->fetch_one("SELECT * FROM {$tblprefix}aurls WHERE auid='$auid' AND type='$linktype' LIMIT 1");
			$linkitem || cls_message::show('��Ч�Ĳ���');
		}
		tabheader((empty($auid) ? '���' : '�޸�') . '��������', 'amconfigeditlink',"?entry=amconfigs&action=$action&linktype=$linktype" . (empty($linkitem['auid']) ? '' : "&auid=$linkitem[auid]"),6);
		trbasic('��������','linkitemnew[name]', empty($linkitem['name']) ? '' : $linkitem['name'], 'text', array('validate' => " rule=\"must\" rev=\"��������\""));
		trbasic('���ӵ�ַ','linkitemnew[link]',empty($linkitem['link']) ? '?entry=�ű�&action=����' : $linkitem['link'],'text',array('guide'=>'�벻Ҫ�����仯��IDֵ����������Ŀģ�͵�','w'=>50,'validate' => " rule=\"must\" rev=\"���ӵ�ַ\""));
		trbasic('���ӱ�ע','linkitemnew[mark]',empty($linkitem['mark']) ? '' : $linkitem['mark'],'textarea');
		tabfooter('bsubmit');
	}else{
		$sql = "name='$linkitemnew[name]',link='$linkitemnew[link]',mark='$linkitemnew[mark]'";
		if(empty($auid)){
			$sql = "INSERT INTO {$tblprefix}aurls SET auid=".auto_insert_id('aurls').",type='$linktype',$sql";
		}else{
			$sql = "UPDATE {$tblprefix}aurls SET $sql WHERE auid='$auid' LIMIT 1";
		}
		$db->query($sql);
		cls_CacheFile::Update('aurls');
		cls_message::show('��������' . (empty($auid) ? '���' : '�޸�') . '�ɹ���',axaction(6,"?entry=amconfigs&action=amconfigmdflink&linktype=$linktype"));
	}
}elseif($action == 'amconfigmdflink'){
	if($re = $curuser->NoBackFunc('bkconfig')) cls_message::show($re);
	if(!submitcheck('bsubmit')){
		$where = empty($linktype) ? '' : "type='$linktype'";
		$where && $where = " WHERE $where";
		echo form_str($actionid.'linkitemfilter',"?entry=$entry&action=$action&linktype=$linktype");

		tabheader($linkitemtype[$linktype].'����'. "&nbsp;&nbsp;<a href=\"?entry=amconfigs&action=amconfigaddlink&linktype=$linktype\" onclick=\"return floatwin('open_alink',this)\">��ӹ�������</a>");
		trcategory(array("<input class=\"checkbox\" type=\"checkbox\" name=\"chkall\" onclick=\"checkall(this.form, 'selectid', 'chkall')\">", '����ID', '����|L', '����|L', '����', '����'));
		$query = $db->query("SELECT * FROM {$tblprefix}aurls $where ORDER BY vieworder ASC");
		while($row = $db->fetch_array($query)){
			echo "<tr class=\"txt\">\n".
				"<td class=\"txtC w30\"><input class=\"checkbox\" type=\"checkbox\" name=\"selectid[$row[auid]]\" value=\"$row[auid]\"></td>\n".
				"<td class=\"txtC\">$row[auid]</td>\n".
				"<td class=\"txtL\">$row[name]</td>\n".
				"<td class=\"txtL\">$row[link]</td>\n".
				"<td class=\"txtC w40\"><input class=\"w40\" type=\"text\" name=\"vieworder[$row[auid]]\" value=\"$row[vieworder]\"></td>\n".
				"<td class=\"txtC\"><a href=\"?entry=amconfigs&action=amconfigaddlink&linktype=$linktype&auid=$row[auid]\" onclick=\"return floatwin('open_alink',this)\">�޸�</a></td>\n".
				"</tr>\n";
		}
		tabfooter();

		//������
		tabheader('������Ŀ');
		trbasic('ѡ�������Ŀ','', "<input class=\"checkbox\" type=\"checkbox\" name=\"linkdeal[delete]\" id=\"linkdeal[delete]\" value=\"1\" onclick=\"deltip(this,$no_deepmode)\"><label for=\"linkdeal[delete]\">ɾ��</label> &nbsp;",'');
		tabfooter('bsubmit');
		a_guide('amconfigablock');
	}else{
		empty($vieworder) && $vieworder = array();
		if(!empty($linkdeal) && !empty($selectid)){
			foreach($selectid as $auid){
				if(!empty($linkdeal['delete']) && deep_allow($no_deepmode)){
					unset($vieworder[$auid]);
					$db->query("DELETE FROM {$tblprefix}aurls WHERE auid='$auid' "); //LIMIT 1,ִ��escape_old_sql()���limit����ͨ��
				}
			}
		}
		asort($vieworder);
		$index = 0;
		foreach($vieworder as $k => $v){
			$db->query("UPDATE {$tblprefix}aurls SET vieworder=" . ++$index . " WHERE auid='$k' LIMIT 1");
		}
		cls_CacheFile::Update('aurls');
		cls_message::show('�����������',M_REFERER);		
	}

}
?>