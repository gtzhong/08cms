<?PHP
(!defined('M_COM') || !defined('M_ADMIN')) && exit('No Permission');
aheader();
if($re = $curuser->NoBackFunc('catalog')) cls_message::show($re);
foreach(array('channels','mtpls','rprojects','splitbls',) as $k) $$k = cls_cache::Read($k);
include_once M_ROOT."include/fields.fun.php";
if($action=='cotypesedit'){
	$cotypes = cls_cotype::InitialInfoArray();
	backnav('cata','cotype');
	echo "<title>��ϵ����</title>";
	if(!submitcheck('bcotypesedit')){
		tabheader("��ϵ���� &nbsp;".modpro(">><a href=\"?entry=$entry&action=cotypeadd\" onclick=\"return floatwin('open_cotypesedit',this)\">�����ϵ</a>&nbsp;"),'cotypesedit',"?entry=$entry&action=$action",'10');
		$ii = 0;
		foreach($cotypes as $k => $cotype){
			if(!($ii % 15)) trcategory(array('ID',array('��ϵ����','txtL'),array('���','txtL'),array('��ע','txtL'),'����',array('���ݱ�','txtL'),'�Զ�','�ڵ�','��ѡ','����',modpro('ɾ��'),modpro('ģ��'),'����','�ֶ�','����'));
			$ii ++;
			echo "<tr class=\"txt\">\n".
				"<td class=\"txtC w35\">$k</td>\n".
				"<td class=\"txtL\"><input type=\"text\" size=\"15\" maxlength=\"15\" name=\"cotypesnew[$k][cname]\" value=\"$cotype[cname]\"></td>\n".
				"<td class=\"txtL\"><input type=\"text\" size=\"5\" maxlength=\"10\" name=\"cotypesnew[$k][sname]\" value=\"$cotype[sname]\"></td>\n".
				"<td class=\"txtL\"><input type=\"text\" size=\"40\" maxlength=\"40\" name=\"cotypesnew[$k][remark]\" value=\"$cotype[remark]\"></td>\n".
				"<td class=\"txtC w40\"><input type=\"text\" size=\"4\" maxlength=\"4\" name=\"cotypesnew[$k][vieworder]\" value=\"$cotype[vieworder]\"></td>\n".
				"<td class=\"txtL\">coclass$k</td>\n".
				"<td class=\"txtC w35\">".($cotype['self_reg'] ? 'Y' : '-')."</td>\n".
				"<td class=\"txtC w35\">".($cotype['sortable'] ? 'Y' : '-')."</td>\n".
				"<td class=\"txtC w35\">".($cotype['asmode'] ? $cotype['asmode'] : '-')."</td>\n".
				"<td class=\"txtC w35\">".($cotype['emode'] ? 'Y' : '-')."</td>\n".
				modpro("<td class=\"txtC w35\"><a onclick=\"return deltip(this,$no_deepmode)\" href=\"?entry=$entry&action=cotypesdelete&coid=$k\">ɾ��</a></td>\n").
				modpro("<td class=\"txtC w35\"><a href=\"?entry=$entry&action=archivetbl&coid=$k\" onclick=\"return floatwin('open_cotypesedit',this)\">ģ��</a></td>\n").
				"<td class=\"txtC w35\"><a href=\"?entry=$entry&action=cotypedetail&coid=$k\" onclick=\"return floatwin('open_cotypesedit',this)\">����</a></td>\n".
				"<td class=\"txtC w35\"><a href=\"?entry=$entry&action=ccfieldsedit&coid=$k\" onclick=\"return floatwin('open_cotypesedit',this)\">�ֶ�</a></td>\n".
				"<td class=\"txtC w35\"><a href=\"?entry=coclass&action=coclassedit&coid=$k\" onclick=\"return floatwin('open_cotypesedit',this)\">����</a></td>\n".
				"</tr>";
		}
		tabfooter('bcotypesedit','�޸�');
		a_guide('cotypesedit');
	}else{
		if(!empty($cotypesnew)){
			foreach($cotypesnew as $k => $cotype) {
				$cotype['vieworder'] = max(0,intval($cotype['vieworder']));
				$cotype['cname'] = trim(strip_tags($cotype['cname']));
				$cotype['cname'] = $cotype['cname'] ? $cotype['cname'] : $cotypes[$k]['cname'];
				$cotype['sname'] = trim(strip_tags($cotype['sname']));
				$cotype['remark'] = trim(strip_tags($cotype['remark']));
				$db->query("UPDATE {$tblprefix}cotypes SET 
							cname='$cotype[cname]', 
							sname='$cotype[sname]', 
							remark='$cotype[remark]', 
							vieworder='$cotype[vieworder]'
							WHERE coid='$k'
							");
			}
			adminlog('�༭��ϵ�����б�');
			cls_CacheFile::Update('cotypes');
			cls_message::show('��ϵ�༭���',"?entry=$entry&action=$action");
		}
	}
}elseif($action == 'cotypeadd'){
	modpro() || cls_message::show('����ϵ��ʼ�˿��Ŷ��ο���ģʽ');
	echo "<title>�����ϵ</title>";
	deep_allow($no_deepmode);
	if(!submitcheck('bcotypesadd')){
		tabheader('�����ϵ','cotypesadd',"?entry=$entry&action=$action",2,0,1);
		trbasic('��ϵ����','fmdata[cname]','','text',array('validate'=>makesubmitstr('fmdata[cname]',1,0,0,30)));
		trbasic('�Ƿ��Զ�����','fmdata[self_reg]',0,'radio',array('guide' => '�ύ�󲻿ɱ�����Զ�������ϵ�еķ�����ֹ��趨�����Ǹ���ĳЩ����ϵͳ�Զ��趨�ĵ������ķ��ࡣ'));
		$stidsarr = array();foreach($splitbls as $k => $v) $stidsarr[$k] = "($k)$v[cname]";
		trbasic('Ӧ�õ���������<br /><input class="checkbox" type="checkbox" name="chchkall" onclick="checkall(this.form,\'fmdata[stids]\',\'chchkall\')">ȫѡ','',makecheckbox('fmdata[stids][]',$stidsarr,array(),5),'');
		tabfooter('bcotypesadd','���');
	}else{
		($fmdata['cname'] = trim(strip_tags($fmdata['cname']))) || cls_message::show('��ϵ���Ʋ���ȫ',M_REFERER);
		$fmdata['stids'] = empty($fmdata['stids']) ? array() : array_filter($fmdata['stids']);
		$db->query("INSERT INTO {$tblprefix}cotypes SET coid = ".auto_insert_id('cotypes').",cname='$fmdata[cname]',self_reg='$fmdata[self_reg]'");
		if($coid = $db->insert_id()){
			if($fmdata['stids'] && !$fmdata['self_reg']){
				foreach($fmdata['stids'] as $stid){
					empty($splitbls[$stid]) || $db->query("ALTER TABLE {$tblprefix}archives$stid ADD ccid$coid smallint(6) unsigned NOT NULL default 0",'SILENT');
				}
			}
			$db->query("CREATE TABLE {$tblprefix}coclass$coid LIKE {$tblprefix}init_coclass");
			$db->query("ALTER TABLE {$tblprefix}coclass$coid COMMENT='$fmdata[cname](��ϵ)��'");
			adminlog('�����ϵ');
			cls_CacheFile::Update('cotypes');
			cls_message::show('��ϵ������',axaction(36,"?entry=$entry&action=cotypedetail&coid=$coid"));
		}else cls_message::show('��ϵ���ʧ��',axaction(2,"?entry=$entry&action=cotypesedit"));
	}
}elseif($action == 'archivetbl' && $coid){//ֻ�������ݱ����Ƿ��и��ֶ�
	modpro() || cls_message::show('����ϵ��ʼ�˿��Ŷ��ο���ģʽ');
	!($cotype = cls_cotype::InitialOneInfo($coid)) && cls_message::show('��ָ����ȷ����ϵ');
//	if($cotype['self_reg']) cls_message::show('ָ����ϵΪ�Զ���ϵ��');
	echo "<title>��ϵӦ�õ��ĵ���</title>";
	if(!submitcheck('bsubmit')){
		tabheader($cotype['cname']."($coid) - ��ϵӦ�õ�����",'cotypedetail',"?entry=$entry&action=$action&coid=$coid");
		trcategory(array('����','ID',array('�ĵ�����','txtL'),array('���ݱ�','txtL'),array('�ĵ�ģ��','txtL')));
		foreach($splitbls as $k => $v){
			$channelstr = '';foreach($v['chids'] as $x) @$channels[$x]['cname'] && $channelstr .= $channels[$x]['cname']."($x),";
			$available = in_array($coid,$v['coids']) ? TRUE : FALSE;
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
			$available = in_array($coid,$v['coids']) ? TRUE : FALSE;
			$enabled = empty($fmdata[$k]['enabled']) ? FALSE : TRUE;
			if($enabled != $available){
				if($enabled){
					if(!$cotype['self_reg']){
						if($cotype['asmode']){
							// ����������[2,3,4]�ᱣ����һ��ֵ, ����������[,2,3,4,]���Ϊ0, ������ִ������UPDATE
							// UPDATE xtest_msg_ys SET t3=substring(t3,2) WHERE t3 LIKE ',%' (select *,substring(t3,2) as t3a from xtest_msg_ys WHERE t3 LIKE ',%')
							$db->query("UPDATE {$tblprefix}archives$k SET ccid$coid=SUBSTRING(ccid$coid,2) WHERE ccid$coid LIKE ',%'",'SILENT');
							$db->query("ALTER TABLE {$tblprefix}archives$k ADD ccid$coid varchar(255) NOT NULL default ''",'SILENT');
						}else{
							$db->query("ALTER TABLE {$tblprefix}archives$k ADD ccid$coid smallint(6) unsigned NOT NULL default 0",'SILENT');
							$cotype['emode'] && $db->query("ALTER TABLE {$tblprefix}archives$k ADD ccid{$coid}date int(10) unsigned NOT NULL default 0",'SILENT');
						}
					}
					$v['coids'][] = $coid;
				}else{
					if(!$cotype['self_reg']){
						$db-> query("ALTER TABLE {$tblprefix}archives$k DROP ccid$coid",'SILENT'); 
						$db-> query("ALTER TABLE {$tblprefix}archives$k DROP ccid{$coid}date",'SILENT'); 
					}
					$key = array_search($coid,$v['coids']);
					if($key !== FALSE) unset($v['coids'][$key]);
				}
			}
			@sort($v['coids']);
			$db->query("UPDATE {$tblprefix}splitbls SET coids='".(empty($v['coids']) ? '' : implode(',',$v['coids']))."' WHERE stid='$k'");
		}
		cls_CacheFile::Update('splitbls');
		adminlog($cotype['cname']."��ϵӦ�õ�����");
		cls_message::show('��ϵ������ɡ�',"?entry=$entry&action=$action&coid=$coid");
	}
}elseif($action == 'cotypedetail' && $coid){
	!($cotype = cls_cotype::InitialOneInfo($coid)) && cls_message::show('��ָ����ȷ����ϵ');
	echo "<title>��ϵ���� - $cotype[cname]</title>";
	if(!submitcheck('bcotypedetail')){
		tabheader("������ϵ - $cotype[cname]",'cotypedetail',"?entry=$entry&action=$action&coid=$coid");
		$fields = cls_cache::Read('cnfields',$coid);
		$arr = array('' => '������','title' => '��������','dirname' => '�����ʶ',);
		foreach($fields as $k => $v) $v['datatype'] == 'text' && $arr[$k] = $v['cname'];
		trbasic('�Զ�����ĸ��Դ�ֶ�','cotypenew[autoletter]',makeoption($arr,@$cotype['autoletter']),'select');
		$vmodearr = array('0' => '��ͨѡ���б�','1' => '��ѡ��ť','2' => '�༶����','3' => '�༶����(ajax)',);
		trbasic('����ѡ���б�ģʽ','',makeradio('cotypenew[vmode]',$vmodearr,empty($cotype['vmode']) ? 0 : $cotype['vmode']),'');
		if(modpro()){
			tabfooter();
			tabheader('�߼�ѡ��');
			trbasic('�ڵ��Ա��ϵ','cotypenew[sortable]',$cotype['sortable'],'radio',array('guide'=>'�����������ȡ���ڵ���ϵ��ɾ�������뱾��ϵ�йص���Ŀ�ڵ㡣'));
			trbasic('����������η�ҳ��ʾ', 'cotypenew[treestep]', empty($cotype['treestep']) ? '' : $cotype['treestep'], 'text', array('guide'=>'������ÿҳ����������Ϊ����ҳ������Ŀ��������ʱ��������Ϊ10-30���������'));
			trbasic('�����Ӽ������','cotypenew[maxlv]',empty($cotype['maxlv']) ? '' : $cotype['maxlv'], 'text', array('guide'=>'���ջ�0��ʾ���޲�����������������'));
			if(empty($cotype['self_reg'])){
				trbasic('�Ƿ��ѡ��Ŀ','cotypenew[notblank]',$cotype['notblank'],'radio');
				$relatearr = array(0 => '��ѡ',);
				for($i = 2;$i < 20;$i ++) $relatearr[$i] = "<={$i}��";	
				trbasic('�����ѡ��ģʽ','',makeradio('cotypenew[asmode]',$relatearr,empty($cotype['asmode']) ? 0 : $cotype['asmode']),'',array('guide'=>'���������������ѡ��Ӱ��ĳЩ��ѯЧ�ʣ���ѡ���ѡ���л����������ݿ�Ĵ������ݡ�<br>��ѡתΪ��ѡʱ����ֻ������һ��ԭ��ѡ���Ҳ��ɻָ���'));
				$emodearr = array(0 => '����������',1 => '�趨����(ѡ��)',2 => '�趨����(����)');
				trbasic('�������������ģʽ','',makeradio('cotypenew[emode]',$emodearr,empty($cotype['emode']) ? 0 : $cotype['emode']),'',array('guide'=>'�������������֧�����޵���֧�ֻᶪʧԭ�з�����������ݡ�'));
				trbasic('�Ƿ�ǿ�Ʒ���ģ��','cotypenew[chidsforce]',$cotype['chidsforce'],'radio',array('guide' => 'ǿ��ģʽ�£��������Чģ���ڱ��������ã������ڷ������ý����ֶ�����'));
				trbasic('�������Чģ��<br /><input class="checkbox" type="checkbox" name="chchkall" onclick="checkall(this.form,\'cotypenew[chids]\',\'chchkall\')">ȫѡ','',makecheckbox('cotypenew[chids][]',cls_channel::chidsarr(1),empty($cotype['chids']) ? array() : explode(',',$cotype['chids']),5),'',
				array('guide' => 'ǿ��ģʽ�£��������������Զ���Ϊ��ǰ���ã������µ����з��࣬��������ʱ�����ָ��<br>��ǿ��ģʽ�£�������ֻ����Ϊ�����ֶ����ý����Ĭ����'));
			}
		}	
		$fmcstr1 = '';//'<br><input class="checkbox" type="checkbox" name="fieldnew[fromcode]" value="1"'.(empty($field['fromcode']) ? '' : ' checked').'>���Դ��뷵������';
		$fmcstr2 = '';//'<br> ��ѡ ���Դ��뷵�����飬����дPHP���룬ʹ��return array(��������);�õ�ѡ�����ݡ�<br>��ʹ����չ�������붨�嵽'._08_EXTEND_DIR.DS._08_LIBS_DIR.DS.'functions'.DS.'custom.fun.php';
		trbasic('������������'.$fmcstr1,'cotypenew[groups]',empty($cotype['groups']) ? '' : $cotype['groups'],'textarea',array('guide'=>"������ϵ����ʱʹ�ã��������򲻷��飻ÿ����дһ��ѡ���ʽ�������ʶ=������ʾ���⡣$fmcstr2"));
		tabfooter('bcotypedetail');
		a_guide('cotypedetail');
	}else{
		$cotypenew['autoletter'] = trim($cotypenew['autoletter']);
		$cotypenew['notblank'] = empty($cotypenew['notblank']) ? 0 : 1;
		$sqlstr = "notblank='$cotypenew[notblank]',autoletter='$cotypenew[autoletter]',vmode='$cotypenew[vmode]',groups='$cotypenew[groups]'";
		
		if(modpro()){
			if($cotypenew['sortable'] && !$cotype['sortable']){
				$db->query("ALTER TABLE {$tblprefix}cnodes ADD ccid$coid smallint(6) unsigned NOT NULL default '0'",'SILENT');
				$db->query("ALTER TABLE {$tblprefix}o_cnodes ADD ccid$coid smallint(6) unsigned NOT NULL default '0'",'SILENT');
			}elseif(!$cotypenew['sortable'] && $cotype['sortable']){
				$db->query("DELETE FROM {$tblprefix}cnodes WHERE ccid$coid<>0",'SILENT');
				$db->query("ALTER TABLE {$tblprefix}cnodes DROP ccid$coid",'SILENT');
				cls_CacheFile::Update('cnodes');
				$db->query("DELETE FROM {$tblprefix}o_cnodes WHERE ccid$coid<>0",'SILENT');
				$db->query("ALTER TABLE {$tblprefix}o_cnodes DROP ccid$coid",'SILENT');
				cls_CacheFile::Update('o_cnodes');
			}
			$sqlstr .= ",sortable='$cotypenew[sortable]'";	
				
			$cotypenew['treestep'] = empty($cotypenew['treestep']) ? 0 : max(10,intval($cotypenew['treestep']));
			$cotypenew['maxlv'] = empty($cotypenew['maxlv']) ? 0 : max(0,intval($cotypenew['maxlv']));
			$sqlstr .= ",treestep='$cotypenew[treestep]',maxlv='$cotypenew[maxlv]'";		

			
			$cotypenew['chidsforce'] = empty($cotypenew['chidsforce']) ? 0 : 1;
			$cotypenew['chids'] = empty($cotypenew['chids']) ? '' : implode(',',$cotypenew['chids']);
			if($cotypenew['chidsforce']){
				$db->query("UPDATE {$tblprefix}coclass$coid SET chids='$cotypenew[chids]'");
				cls_CacheFile::Update('coclasses',$coid);
			}
			$sqlstr .= ",chidsforce='$cotypenew[chidsforce]',chids='$cotypenew[chids]'";		
			
			$cotypenew['asmode'] = empty($cotypenew['asmode']) ? 0 : max(2,intval($cotypenew['asmode']));
			$cotypenew['emode'] = empty($cotypenew['emode']) ? 0 : max(0,intval($cotypenew['emode']));
			if(empty($cotype['self_reg'])){
				if(!cls_DbOther::AlterFieldSelectMode($cotypenew['asmode'],@$cotype['asmode'],'ccid'.$coid,'archives')) $cotypenew['asmode'] = @$cotype['asmode'];
				if($cotypenew['emode'] != @$cotype['emode']){
					if($cotypenew['emode']){
						cls_dbother::BatchAlterTable("ALTER TABLE {TABLE} ADD ccid{$coid}date int(10) unsigned NOT NULL default 0 AFTER ccid{$coid}");
					}else{
						cls_dbother::BatchAlterTable("ALTER TABLE {TABLE} DROP ccid{$coid}date");
					}
				}
			}
			$sqlstr .= ",asmode='$cotypenew[asmode]',emode='$cotypenew[emode]'";
		}
		$db->query("UPDATE {$tblprefix}cotypes SET $sqlstr WHERE coid='$coid'");
		adminlog('��ϸ�޸���ϵ');
		cls_CacheFile::Update('cotypes');
		cls_message::show('��ϵ�������',axaction(6,"?entry=$entry&action=cotypesedit"));
	}
}elseif($action == 'cotypesdelete' && $coid) {//ɾ����ϵ����ڵ�Ĺ�ϵ
	backnav('cata','cotype');
	modpro() || cls_message::show('����ϵ��ʼ�˿��Ŷ��ο���ģʽ');
	!($cotype = cls_cotype::InitialOneInfo($coid)) && cls_message::show('��ָ����ȷ����ϵ');
	if(!submitcheck('confirm')){
		$message = "ɾ�����ָܻ���ȷ��ɾ����ѡ��Ŀ?<br><br>";
		$message .= "ȷ������>><a href=?entry=$entry&action=cotypesdelete&coid=$coid&confirm=ok>ɾ��</a><br>";
		$message .= "��������>><a href=?entry=$entry&action=cotypesedit>����</a>";
		cls_message::show($message);
	}
	//ɾ���뵱ǰ��ϵ�йص���Ŀ�ֶμ�����
	$cids = array();$rels = array('a' => 'fields','m' => 'mfields','f' => 'ffields','cu' => 'cufields','cn' => 'cnfields',);
	$query = $db->query("SELECT * FROM {$tblprefix}afields WHERE datatype='cacc' AND coid='$coid'");
	while($r = $db->fetch_array($query)){
		if($var = @$rels[$r['type']]){
			if(empty($cids[$var]) || !in_array($r['tpid'],$cids[$var])) $cids[$var][] = $r['tpid'];
		}
		cls_dbother::DropField($r['tbl'],$r['ename'],$r['datatype']);
	}
	$db->query("DELETE FROM {$tblprefix}afields WHERE datatype='cacc' AND coid='$coid'"); 
	if($cids){//���²�ͬ���ϵ��ֶλ���
		foreach($cids as $k => $v){
			foreach($v as $id) cls_CacheFile::Update($k,$id);
		}
	}
	unset($cids,$rels);
	
	//ɾ������ϵ�ķ����ֶμ�¼
	cls_fieldconfig::DeleteOneSourceFields('cotype',$coid);
	
	//ɾ����صĽڵ�
	$db->query("DELETE FROM {$tblprefix}cnodes WHERE ccid$coid<>0",'SILENT');
	$db->affected_rows && cls_CacheFile::Update('cnodes');
	$db->query("DELETE FROM {$tblprefix}o_cnodes WHERE ccid$coid<>0",'SILENT');
	$db->affected_rows && cls_CacheFile::Update('o_cnodes');
	$db->query("DELETE FROM {$tblprefix}mcnodes WHERE mcnvar='ccid$coid'",'SILENT');
	$db->affected_rows && cls_CacheFile::Update('mcnodes');
	
	//ɾ�������ĵ����ϵ���ϵ�ֶ�
	$na = stidsarr(1);
	foreach($na as $k => $v){
		$db-> query("ALTER TABLE {$tblprefix}".atbl($k,1)." DROP ccid$coid",'SILENT'); 
		$db-> query("ALTER TABLE {$tblprefix}".atbl($k,1)." DROP ccid{$coid}date",'SILENT'); 
	}
	
	//������Ŀ����
	$cnrels = cls_cache::Read('cnrels');
	foreach($cnrels as $k => $v){
		if($v['coid'] == $coid || $v['coid1'] == $coid){
			$db->query("DELETE FROM {$tblprefix}cnrels WHERE rid='$k'");
		}
	}
	cls_CacheFile::Update('cnrels');
	
	//����������Ĺ���
	
	$db->query("DELETE FROM {$tblprefix}cotypes WHERE coid='$coid'",'SILENT');
	$db->query("DROP TABLE IF EXISTS {$tblprefix}coclass$coid",'SILENT');
	cls_CacheFile::Update('cotypes');
	cls_CacheFile::Del('coclasses',$coid);
	adminlog('ɾ����ϵ');
	cls_message::show('��ϵɾ�����',"?entry=$entry&action=cotypesedit");
}elseif($action == 'ccfieldsedit' && $coid){
	echo "<title>�����ֶι���</title>";
	!($cotype = cls_cotype::InitialOneInfo($coid)) && cls_message::show('��ָ����ȷ����ϵ');
	$fields = cls_fieldconfig::InitialFieldArray('cotype',$coid);
	if(!submitcheck('bccfieldsedit')){
		tabheader("�����ֶι��� - $cotype[cname]&nbsp;&nbsp;&nbsp;>><a href=\"?entry=$entry&action=fieldone&coid=$coid\" onclick=\"return floatwin('open_fielddetail',this)\">����ֶ�</a>",'ccfieldsedit',"?entry=$entry&action=$action&coid=$coid",'5');
		trcategory(array('����',array('�ֶ�����','txtL'),'����',array('�ֶα�ʶ','txtL'),array('���ݱ�','txtL'),'�ֶ�����','ɾ��','�༭'));
		foreach($fields as $k => $v) {
			echo "<tr class=\"txt\">\n".
				"<td class=\"txtC w40\"><input class=\"checkbox\" type=\"checkbox\" name=\"fieldsnew[$k][available]\" value=\"1\"".($v['available'] ? ' checked' : '')."></td>\n".
				"<td class=\"txtL\"><input type=\"text\" size=\"25\" name=\"fieldsnew[$k][cname]\" value=\"".mhtmlspecialchars($v['cname'])."\"></td>\n".
				"<td class=\"txtC w60\"><input type=\"text\" size=\"4\" name=\"fieldsnew[$k][vieworder]\" value=\"$v[vieworder]\"></td>\n".
				"<td class=\"txtL\">".mhtmlspecialchars($k)."</td>\n".
				"<td class=\"txtL\">$v[tbl]</td>\n".
				"<td class=\"txtC w100\">".cls_fieldconfig::datatype($v['datatype'])."</td>\n".
				"<td class=\"txtC w40\"><input class=\"checkbox\" type=\"checkbox\" name=\"delete[$k]\" value=\"$k\" onclick=\"deltip(this,$no_deepmode)\"></td>\n".
				"<td class=\"txtC w50\"><a href=\"?entry=$entry&action=fieldone&coid=$coid&fieldname=$k\" onclick=\"return floatwin('open_fielddetail',this)\">����</a></td>\n".
				"</tr>";
		}
		tabfooter('bccfieldsedit');
		a_guide('ccfieldsedit');
	}else{
		if(!empty($delete) && deep_allow($no_deepmode)){
			$deleteds = cls_fieldconfig::DeleteField('cotype',$coid,$delete);
			foreach($deleteds as $k){
				unset($fieldsnew[$k]);
			}
		}
		if(!empty($fieldsnew)){
			foreach($fieldsnew as $k => $v){
				$v['cname'] = trim(strip_tags($v['cname']));
				$v['cname'] = $v['cname'] ? $v['cname'] : $fields[$k]['cname'];
				$v['available'] = !empty($v['available']) ? 1 : 0;
				$v['vieworder'] = max(0,intval($v['vieworder']));
				cls_fieldconfig::ModifyOneConfig('cotype',$coid,$v,$k);
			}
		}
		cls_fieldconfig::UpdateCache('cotype',$coid);
		
		adminlog('�༭��ϵ��Ϣ�ֶ�');
		cls_message::show('�ֶ��޸����',"?entry=$entry&action=ccfieldsedit&coid=$coid");
	}
}elseif($action == 'fieldone' && $coid){
	cls_FieldConfig::EditOne('cotype',@$coid,@$fieldname);

}