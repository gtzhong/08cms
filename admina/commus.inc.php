<?PHP
(!defined('M_COM') || !defined('M_ADMIN')) && exit('No Permission');
aheader();
if($re = $curuser->NoBackFunc('cfcommu')) cls_message::show($re);
include_once M_ROOT."include/fields.fun.php";
if($action == 'commusedit'){
	backnav('exconfig','commu');
	$commus = cls_commu::InitialInfoArray();
	if(!submitcheck('bcommusedit')){
		tabheader('������Ŀ����'."&nbsp; &nbsp; >><a href=\"?entry=$entry&action=commuadd\" onclick=\"return floatwin('open_commusedit',this)\">".'���'."</a>",'commusedit',"?entry=$entry&action=$action",'7');
		trcategory(array('ID','����',array('��Ŀ����','txtL'),array('��ע','txtL'),array('���ݱ�','txtL'),'ɾ��','�ֶ�','�༭'));
		foreach($commus as $cuid => $commu){
			echo "<tr class=\"txt\">".
			"<td class=\"txtC w30\">$cuid</td>\n".
			"<td class=\"txtC w30\"><input class=\"checkbox\" type=\"checkbox\" name=\"commusnew[$cuid][available]\" value=\"1\"".(empty($commu['available']) ? '' : ' checked')."></td>\n".
			"<td class=\"txtL\"><input type=\"text\" size=\"20\" maxlength=\"20\" name=\"commusnew[$cuid][cname]\" value=\"$commu[cname]\"></td>\n".
			"<td class=\"txtL\"><input type=\"text\" size=\"50\" maxlength=\"100\" name=\"commusnew[$cuid][remark]\" value=\"$commu[remark]\"></td>\n".
			"<td class=\"txtL\">$commu[tbl]</td>\n".
			"<td class=\"txtC w30\"><a onclick=\"return deltip(this,$no_deepmode)\" href=\"?entry=$entry&action=commudel&cuid=$cuid\">ɾ��</a></td>\n".
			"<td class=\"txtC w30\">".(!$commu['tbl'] ? '-' : "<a href=\"?entry=$entry&action=commufields&cuid=$cuid\" onclick=\"return floatwin('open_commusedit',this)\">�ֶ�</a>")."</td>\n".
			"<td class=\"txtC w30\"><a href=\"?entry=$entry&action=commudetail&cuid=$cuid\" onclick=\"return floatwin('open_commusedit',this)\">����</a></td></tr>\n";
		}
		tabfooter('bcommusedit','�޸�');
	}else{
		if(!empty($commusnew)){
			foreach($commusnew as $k => $v){
				$v['cname'] = empty($v['cname']) ? $commus[$k]['cname'] : $v['cname'];
				$v['remark'] = empty($v['remark']) ? $commus[$k]['remark'] : $v['remark'];
				$v['available'] = empty($v['available']) ? 0 : 1;
				$db->query("UPDATE {$tblprefix}acommus SET cname='$v[cname]',remark='$v[remark]',available='$v[available]' WHERE cuid='$k'");
			}
		}
		cls_CacheFile::Update('commus');
		adminlog('�༭������Ŀ�б�');
		cls_message::show('������Ŀ�༭���', "?entry=$entry&action=$action");
	}
}elseif($action == 'commudel' && $cuid) {
	backnav('exconfig','commu');
	deep_allow($no_deepmode,"?entry=$entry&action=commusedit");
	if(!($commu = cls_commu::InitialOneInfo($cuid))) cls_message::show('��ָ����ȷ����Ŀ');
	if(!submitcheck('confirm')){
		$message = "ɾ�����ָܻ���ȷ��ɾ����ѡ��Ŀ?<br><br>";
		$message .= "ȷ������>><a href=?entry=$entry&action=$action&cuid=$cuid&confirm=ok>ɾ��</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
		$message .= "��������>><a href=?entry=$entry&action=commusedit>����</a>";
		cls_message::show($message);
	}
	$commu['tbl'] && $db->query("DROP TABLE IF EXISTS {$tblprefix}$commu[tbl]",'SILENT');
	cls_fieldconfig::DeleteOneSourceFields('commu',$cuid);
	$db->query("DELETE FROM {$tblprefix}acommus WHERE cuid='$cuid'",'SILENT');
	cls_CacheFile::Update('commus');
	
	adminlog('ɾ��������Ŀ'.$commu['cname']);
	cls_message::show('ɾ��������Ŀ���',"?entry=$entry&action=commusedit");
}elseif($action == 'commuadd'){
	deep_allow($no_deepmode);
	if(!submitcheck('bcommuadd')){
		tabheader('��ӽ�����Ŀ','commuadd',"?entry=$entry&action=commuadd");
		trbasic('��Ŀ����','communew[cname]');
		trbasic('��ע','communew[remark]','','text',array('w'=>50));
		trbasic('������¼���ݱ�','communew[tbl]');
		tabfooter('bcommuadd');
	}else{
		$communew['cname'] = empty($communew['cname']) ? '' : trim(strip_tags($communew['cname']));
		empty($communew['cname']) && cls_message::show('��ʶ���ϲ���ȫ',M_REFERER);
		$communew['remark'] = empty($communew['remark']) ? '' : trim(strip_tags($communew['remark']));
		if($communew['tbl'] = empty($communew['tbl']) ? '' : trim(strip_tags($communew['tbl']))){
			  $db->query("CREATE TABLE {$tblprefix}$communew[tbl] (
			  cid mediumint(8) unsigned NOT NULL auto_increment,
			  mid mediumint(8) unsigned NOT NULL default '0',
			  mname varchar(15) NOT NULL default '',
			  createdate int(10) unsigned NOT NULL default '0',
			  checked tinyint(1) unsigned NOT NULL default '0',
			  ucid mediumint(8) unsigned NOT NULL default '0',
			  PRIMARY KEY (cid))".(mysql_get_server_info() > '4.1' ? " ENGINE=MYISAM DEFAULT CHARSET=$dbcharset" : " TYPE=MYISAM"));
		}
		$db->query("INSERT INTO {$tblprefix}acommus SET cuid=".auto_insert_id('acommus').",cname='$communew[cname]',remark='$communew[remark]',tbl='$communew[tbl]'");
		$cuid = $db->insert_id();
		if($communew['tbl']){
			$db->query("ALTER TABLE {$tblprefix}$communew[tbl] ADD cuid smallint(6) unsigned NOT NULL default '$cuid' AFTER ucid");
			$db->query("ALTER TABLE {$tblprefix}$communew[tbl] COMMENT='$communew[cname](����)��'");
		}
		cls_CacheFile::Update('commus');
		adminlog('��ӽ�����Ŀ');
		cls_message::show('������Ŀ��ӳɹ�������ϸ���á�', axaction(36, "?entry=$entry&action=commudetail&cuid=$cuid"));
	}
}elseif($action == 'commudetail' && $cuid){
	if(!($commu = cls_commu::InitialOneInfo($cuid))) cls_message::show('��ָ����ȷ�Ľ�����Ŀ��');
	if(@!include("exconfig/commu_$cuid.php")){
		if(!submitcheck('bcommudetail')) {
			tabheader('������Ŀ����-'.$commu['cname'],'commudetail',"?entry=$entry&action=$action&cuid=$cuid");
			trbasic('��ע','communew[remark]',$commu['remark'],'text',array('w'=>50));
			trbasic('���ò�������'.($commu['cfgs0'] && !$commu['cfgs'] ? '�����ʽ����������!' : ''),'communew[cfgs0]',empty($commu['cfgs']) ? (empty($commu['cfgs0']) ? '' : $commu['cfgs0']) : var_export($commu['cfgs'],1),'textarea',array('w' => 500,'h' => 300,'guide'=>'��array()���룬����������Ҫ��php�淶'));
			trbasic('����˵��','communew[content]',$commu['content'],'textarea',array('w' => 500,'h' => 300,));
			tabfooter('bcommudetail','�޸�');
		}else{
			$communew['cfgs0'] = empty($communew['cfgs0']) ? '' : trim($communew['cfgs0']);
			$communew['cfgs'] = varexp2arr($communew['cfgs0']);
			$communew['remark'] = empty($communew['remark']) ? '' : trim(strip_tags($communew['remark']));
			$communew['content'] = empty($communew['content']) ? '' : trim($communew['content']);
			$communew['cfgs'] = !empty($communew['cfgs']) ? addslashes(var_export($communew['cfgs'],TRUE)) : '';
			$db->query("UPDATE {$tblprefix}acommus SET
						remark='$communew[remark]',
						content='$communew[content]',
						cfgs0='$communew[cfgs0]',
						cfgs='$communew[cfgs]'
						WHERE cuid='$cuid'");
			cls_CacheFile::Update('commus');
			adminlog('�༭������Ŀ'.$commu['cname']);
			cls_message::show('������Ŀ������ɡ�',axaction(36, "?entry=$entry&action=$action&cuid=$cuid"));
		}
	}

}elseif($action == 'commufields' && $cuid){
	if(!($commu = cls_commu::InitialOneInfo($cuid))) cls_message::show('��ָ����ȷ����Ŀ');
	if(!$commu['tbl']) cls_message::show('ָ���Ľ�����Ŀû��ָ����¼��');
	$fields = cls_fieldconfig::InitialFieldArray('commu',$cuid);
	if(!submitcheck('bcommudetail')){
		tabheader($commu['cname']."-�ֶι��� &nbsp; &nbsp;>><a href=\"?entry=$entry&action=fieldone&cuid=$cuid\" onclick=\"return floatwin('open_fielddetail',this)\">����ֶ�</a>",'commudetail',"?entry=$entry&action=$action&cuid=$cuid");
		trcategory(array('��Ч',array('�ֶ�����','txtL'),'����',array('�ֶα�ʶ','txtL'),array('���ݱ�','txtL'),'�ֶ�����','ɾ��','�༭'));
		foreach($fields as $k => $v){
		echo "<tr class=\"txt\">\n".
			"<td class=\"txtC w40\"><input class=\"checkbox\" type=\"checkbox\" name=\"fieldsnew[$k][available]\" value=\"1\"".($v['available'] ? ' checked' : '')."></td>\n".
			"<td class=\"txtL\"><input type=\"text\" size=\"25\" name=\"fieldsnew[$k][cname]\" value=\"".mhtmlspecialchars($v['cname'])."\"></td>\n".
			"<td class=\"txtC w60\"><input type=\"text\" size=\"4\" name=\"fieldsnew[$k][vieworder]\" value=\"$v[vieworder]\"></td>\n".
			"<td class=\"txtL\">".mhtmlspecialchars($k)."</td>\n".
			"<td class=\"txtL\">$v[tbl]</td>\n".
			"<td class=\"txtC w100\">".cls_fieldconfig::datatype($v['datatype'])."</td>\n".
			"<td class=\"txtC w40\"><input class=\"checkbox\" type=\"checkbox\" name=\"delete[$k]\" value=\"$k\" onclick=\"deltip(this,$no_deepmode)\"></td>\n".
			"<td class=\"txtC w50\"><a href=\"?entry=$entry&action=fieldone&cuid=$cuid&fieldname=$k\" onclick=\"return floatwin('open_fielddetail',this)\">����</a></td>\n".
			"</tr>";
		}
		tabfooter('bcommudetail');
	}else{
		if(!empty($delete) && deep_allow($no_deepmode)){
			$deleteds = cls_fieldconfig::DeleteField('commu',$cuid,$delete);
			foreach($deleteds as $k){
				unset($fieldsnew[$k]);
			}
		}
		if(!empty($fieldsnew)){
			foreach($fieldsnew as $k => $v){
				$v['cname'] = trim(strip_tags($v['cname']));
				$v['cname'] = !$v['cname'] ? $fields[$k]['cname'] : $v['cname'];
				$v['available'] = empty($v['available']) ? 0 : 1;
				$v['vieworder'] = max(0,intval($v['vieworder']));
				cls_fieldconfig::ModifyOneConfig('commu',$cuid,$v,$k);
			}
		}
		cls_fieldconfig::UpdateCache('commu',$cuid);
		
		adminlog('�༭������Ŀ'.$commu['cname'].'�ֶ��б�');
		cls_message::show('������Ŀ�ֶα༭��ɡ�',"?entry=$entry&action=$action&cuid=$cuid");
	}
}elseif($action == 'fieldone' && $cuid){
	cls_FieldConfig::EditOne('commu',@$cuid,@$fieldname);

}
