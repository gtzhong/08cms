<?
(!defined('M_COM') || !defined('M_ADMIN')) && exit('No Permission');
aheader();
if($re = $curuser->NoBackFunc('tpl')) cls_message::show($re);
$dbsources = cls_cache::Read('dbsources');
$charsetarr = array('gbk' => 'GBK','big5' => 'BIG5','utf8' => 'UTF-8','latin1' => 'Latin1',);
if($action == 'dbsourcesedit'){
	backnav('data','dbsource');
	if(!submitcheck('bdbsourcesedit') && !submitcheck('bdbsourceadd')){
		tabheader('�ⲿ����Դ����','dbsourcesedit','?entry=dbsources&action=dbsourcesedit','10');
		trcategory(array('ID','�ⲿ����Դ����','���ݿ������','���ݿ��û�','���ݿ�����','���ݿ��ַ���','<input class="checkbox" type="checkbox" name="chkall" onclick="deltip(this,0,checkall,this.form)">ɾ?','���ݿ�ṹ','����'));
		foreach($dbsources as $k => $dbsource){
			echo "<tr class=\"txt\">\n".
			"<td class=\"txtC w40\">$k</td>\n".
			"<td class=\"txtL\"><input type=\"text\" size=\"25\" name=\"dbsourcesnew[$k][cname]\" value=\"$dbsource[cname]\"></td>\n".
			"<td class=\"txtC\">$dbsource[dbhost]</td>\n".
			"<td class=\"txtC\">$dbsource[dbuser]</td>\n".
			"<td class=\"txtC\">$dbsource[dbname]</td>\n".
			"<td class=\"txtC\">".$charsetarr[$dbsource['dbcharset']]."</td>\n".
			"<td class=\"txtC w40\"><input class=\"checkbox\" type=\"checkbox\" name=\"delete[$k]\" value=\"$k\" onclick=\"deltip()\"></td>\n".
			"<td class=\"txtC\"><a href=\"?entry=dbsources&action=viewconfigs&dsid=$k\" target=\"_blank\">".'�鿴'."</a></td>\n".
			"<td class=\"txtC w40\"><a href=\"?entry=dbsources&action=dbsourcedetail&dsid=$k\">����</a></td>\n".
			"</tr>";
		}
		tabfooter('bdbsourcesedit');

		tabheader('����ⲿ����Դ','dbsourceadd',"?entry=dbsources&action=dbsourcesedit");
		trbasic('�ⲿ����Դ����','dbsourcenew[cname]');
		trbasic('���ݿ������','dbsourcenew[dbhost]');
		trbasic('���ݿ��û�','dbsourcenew[dbuser]');
		trbasic('���ݿ�����','dbsourcenew[dbpw]');
		trbasic('���ݿ�����','dbsourcenew[dbname]');
		trbasic('���ݿ��ַ���','dbsourcenew[dbcharset]',makeoption($charsetarr),'select');
		tabfooter();
		echo '
			<input class="button" type="submit" name="bdbsourceadd" value="���">&nbsp; &nbsp;
			<input class="button" type="button" name="dbcheck" value="��������" onclick="var f=this.form,u=f.action;f.action=\'?entry=checks&action=dbcheck&deal=add\';f.target=\'dbcheckiframe\';f.submit();f.target=\'_self\';f.action=u"><iframe name="dbcheckiframe" style="display: none"></iframe>
		</form>';
		a_guide('dbsourcesedit');
	
	}elseif(submitcheck('bdbsourceadd')){
		$dbsourcenew['cname'] = trim(strip_tags($dbsourcenew['cname']));
		$dbsourcenew['dbhost'] = trim(strip_tags($dbsourcenew['dbhost']));
		$dbsourcenew['dbuser'] = trim(strip_tags($dbsourcenew['dbuser']));
		$dbsourcenew['dbname'] = trim(strip_tags($dbsourcenew['dbname']));
		if(empty($dbsourcenew['cname']) || empty($dbsourcenew['dbhost']) || empty($dbsourcenew['dbuser']) || empty($dbsourcenew['dbname'])){
			cls_message::show('���ϲ���ȫ', '?entry=dbsources&action=dbsourcesedit');
		}
		$dbsourcenew['dbpw'] = trim($dbsourcenew['dbpw']);
		$dbsourcenew['dbpw'] = empty($dbsourcenew['dbpw']) ? '' : authcode(trim($dbsourcenew['dbpw']),'ENCODE',md5($authkey));
		$db->query("INSERT INTO {$tblprefix}dbsources SET 
					cname='$dbsourcenew[cname]', 
					dbhost='$dbsourcenew[dbhost]', 
					dbuser='$dbsourcenew[dbuser]', 
					dbpw='$dbsourcenew[dbpw]', 
					dbname='$dbsourcenew[dbname]', 
					dbcharset='$dbsourcenew[dbcharset]'
					");
		adminlog('����ⲿ����Դ');
		cls_CacheFile::Update('dbsources');
		cls_message::show('�ⲿ����Դ������','?entry=dbsources&action=dbsourcesedit');
	}elseif(submitcheck('bdbsourcesedit')){
		if(!empty($delete)){
			foreach($delete as $k) {
				$db->query("DELETE FROM {$tblprefix}dbsources WHERE dsid='$k'");
				unset($dbsourcesnew[$k]);
			}
		}

		if(!empty($dbsourcesnew)){
			foreach($dbsourcesnew as $k => $v){
				$v['cname'] = empty($v['cname']) ? $dbsources[$k]['cname'] : $v['cname'];
				if($v['cname'] != $dbsources[$k]['cname']){
					$db->query("UPDATE {$tblprefix}dbsources SET
								cname='$v[cname]'
								WHERE dsid='$k'");
				}
			}
		}
		adminlog('�༭�ⲿ����Դ');
		cls_CacheFile::Update('dbsources');
		cls_message::show('�ⲿ����Դ�޸����','?entry=dbsources&action=dbsourcesedit');
	}
}elseif($action == 'dbsourcedetail' && $dsid){
	backnav('data','dbsource');
	empty($dbsources[$dsid]) && cls_message::show('��ָ����ȷ���ⲿ����Դ','?entry=dbsources&action=dbsourcesedit');
	$dbsource = $dbsources[$dsid];
	$dbsource['vdbpw'] = $dbsource['tdbpw'] = '';
	if(!empty($dbsource['dbpw'])){
		$dbsource['tdbpw'] = authcode($dbsource['dbpw'],'DECODE',md5($authkey));
		$dbsource['vdbpw'] = $dbsource['tdbpw']{0}.'********'.$dbsource['tdbpw']{strlen($dbsource['tdbpw']) - 1};
	}
	if(!submitcheck('bdbsourcedetail')){
		tabheader('�༭�ⲿ����Դ','dbsourcedetail',"?entry=dbsources&action=dbsourcedetail");
		trbasic('�ⲿ����Դ����','dbsourcenew[cname]',$dbsource['cname']);
		trbasic('���ݿ������','dbsourcenew[dbhost]',$dbsource['dbhost']);
		trbasic('���ݿ��û�','dbsourcenew[dbuser]',$dbsource['dbuser']);
		trbasic('���ݿ�����','dbsourcenew[dbpw]',$dbsource['vdbpw']);
		echo "<input type=\"hidden\" name=\"dbsourcenew[dbpw0]\" value=\"$dbsource[dbpw]\">\n";
		echo "<input type=\"hidden\" name=\"dsid\" value=\"$dsid\">\n";
		trbasic('���ݿ�����','dbsourcenew[dbname]',$dbsource['dbname']);
		trbasic('���ݿ��ַ���','dbsourcenew[dbcharset]',makeoption($charsetarr,$dbsource['dbcharset']),'select');
		tabfooter();
		echo '
			<input class="button" type="submit" name="bdbsourcedetail" value="���">&nbsp; &nbsp;
			<input class="button" type="button" name="dbcheck" value="��������" onclick="var f=this.form,u=f.action;f.action=\'?entry=checks&action=dbcheck&deal=edit\';f.target=\'dbcheckiframe\';f.submit();f.target=\'_self\';f.action=u"><iframe name="dbcheckiframe" style="display: none"></iframe>
		</form>';
		a_guide('dbsourcedetail');
	}else{
		$dbsourcenew['cname'] = trim(strip_tags($dbsourcenew['cname']));
		$dbsourcenew['dbhost'] = trim(strip_tags($dbsourcenew['dbhost']));
		$dbsourcenew['dbuser'] = trim(strip_tags($dbsourcenew['dbuser']));
		$dbsourcenew['dbname'] = trim(strip_tags($dbsourcenew['dbname']));
		if(empty($dbsourcenew['cname']) || empty($dbsourcenew['dbhost']) || empty($dbsourcenew['dbuser']) || empty($dbsourcenew['dbname'])){
			cls_message::show('���ϲ���ȫ', '?entry=dbsources&action=dbsourcesedit');
		}
		if($dbsourcenew['dbpw'] == $dbsource['vdbpw']){
			$dbsourcenew['dbpw'] = $dbsource['dbpw'];
		}else{
			$dbsourcenew['dbpw'] = trim($dbsourcenew['dbpw']);
			$dbsourcenew['dbpw'] = empty($dbsourcenew['dbpw']) ? '' : authcode(trim($dbsourcenew['dbpw']),'ENCODE',md5($authkey));
		}
		$db->query("UPDATE {$tblprefix}dbsources SET 
					cname='$dbsourcenew[cname]', 
					dbhost='$dbsourcenew[dbhost]', 
					dbuser='$dbsourcenew[dbuser]', 
					dbpw='$dbsourcenew[dbpw]', 
					dbname='$dbsourcenew[dbname]', 
					dbcharset='$dbsourcenew[dbcharset]'
					WHERE dsid='$dsid'
					");
		adminlog('�����޸��ⲿ����Դ');
		cls_CacheFile::Update('dbsources');
		cls_message::show('�ⲿ����Դ�޸����','?entry=dbsources&action=dbsourcesedit');
	}
}elseif($action == 'viewconfigs'){
	$dsid = empty($dsid) ? 0 : max(0,intval($dsid));
	$dbtable = empty($dbtable) ? '' : trim($dbtable);
	if($dsid && empty($dbsources[$dsid])) cls_message::show('��ָ����ȷ���ⲿ����Դ',$forward);
	if(!$dsid){
		$ndb = &$db;
		$dbsource['cname'] = '��ǰϵͳ';
		$dbsource['dbname'] = $dbname;
	}else{
		$dbsource = $dbsources[$dsid];
		$dbsource['dbpw'] && $dbsource['dbpw'] = authcode($dbsource['dbpw'],'DECODE',md5($authkey));
		if(empty($dbsource['cname']) || empty($dbsource['dbhost']) || empty($dbsource['dbuser']) || empty($dbsource['dbname'])){
			cls_message::show('�ⲿ����Դ���ϲ���ȫ');
		}
		$ndb = & _08_factory::getDBO( 
            array('dbhost' => $dbsource['dbhost'], 'dbuser' => $dbsource['dbuser'], 'dbpw' => $dbsource['dbpw'], 
                  'dbname' => $dbsource['dbname'], 'pconnect' => 0, 'dbcharset' => $dbsource['dbcharset'])
        );
		if(!is_resource($ndb->link)) cls_message::show('�ⲿ����Դ���Ӵ���');
	}

	$dbtables = array('' => '��ѡ�����ݱ�');
	$query = $ndb->query("SHOW TABLES FROM `$dbsource[dbname]`"); //�����ݿ���Ϊbbs.domain.com�������ݿ���Ҫ��[`]������
	while($v = $ndb->fetch_row($query)){
		$dbtables[$v[0]] = $v[0];
	}
	$dsidsarr = array(0 => '��ǰϵͳ');
	foreach($dbsources as $k => $v) $dsidsarr[$k] = $v['cname'];
	$filterbox = 'ѡ���ⲿ����Դ'.'&nbsp; :&nbsp; ';
	$filterbox .= "<select style=\"vertical-align: middle;\" name=\"dsid\" onchange=\"redirect('?entry=dbsources&action=viewconfigs&dsid=' + this.options[this.selectedIndex].value);\">";
	foreach($dsidsarr as $k => $v){
		$filterbox .= "<option value=\"$k\"".($dsid == $k ? ' selected' : '').">$v</option>";
	}
	$filterbox .= "</select>";			
	$filterbox .= '&nbsp; &nbsp; &nbsp; '.'ѡ�����ݱ�'.'&nbsp; &nbsp;';
	$filterbox .= "<select style=\"vertical-align: middle;\" name=\"dbtable\" onchange=\"redirect('?entry=dbsources&action=viewconfigs&dsid=$dsid&dbtable=' + this.options[this.selectedIndex].value);\">";
	foreach($dbtables as $k => $v){
		$filterbox .= "<option value=\"$k\"".($dbtable == $k ? ' selected' : '').">$v</option>";
	}
	$filterbox .= "</select>";			
	tabheader($filterbox);
	tabfooter();
	$tblfields = array();
	if($dbtable){
		$query = $ndb->query("SHOW FULL COLUMNS FROM $dbtable",'SILENT');
		while($row = $ndb->fetch_array($query)){
			$types = explode(' ',$row['Type']);
			$tblfields[$row['Field']] = strtolower($types[0]);
		}
	}
	tabheader('���ɲ�ѯ�ִ�','dbsqlstr',"?entry=dbsources&action=viewconfigs&dsid=$dsid&dbtable=$dbtable",8);
	trcategory(array('���','�ֶ�����','�ֶ�����','<input class="checkbox" type="checkbox" name="chkall" onclick="checkall(this.form)">'.'ѡ��','��ѯģʽ','ֵ','����','��������'));
	$i = 1;
	$orderarr = array('' => '','ASC' => '����','DESC' => '����',);
	$dbtypearr = array(1 => array('text','mediumtext','longtext','char','varchar','tinytext',),
				2 => array('tinyint','smallint','int','mediumint','bigint','float','double','decimal','bit','bool','binary',));
	$modearr = array(
		'=' => 0,
		'>' => 1,
		'>=' => 1,
		'<' => 1,
		'<=' => 1,
		'!=' => 0,
		'LIKE' => 0,
		'NOT LIKE' => 0,
		'LIKE %...%' => 2,
		'LIKE %...' => 2,
		'LIKE ...%' => 2,
		'REGEXP' => 2,
		'NOT REGEXP' => 2,
		'IS NULL' => 0,
		'IS NOT NULL' => 0,
	);
	foreach($tblfields as $k => $v){
		echo "<tr class=\"txt\">".
			"<td class=\"txtC w30\">$i</td>\n".
			"<td class=\"txtL\"><b>$k</b></td>\n".
			"<td class=\"txtL\">$v</td>\n".
			"<td class=\"txtC w45\"><input class=\"checkbox\" type=\"checkbox\" name=\"dbnews[$k][adopt]\" value=\"1\"".(empty($dbnews[$k]['adopt']) ? '' : ' checked').">\n".
			"<td class=\"txtC\"><select style=\"vertical-align: middle;\" name=\"dbnews[$k][mode]\">".makeoption(thismodearr($v),empty($dbnews[$k]['mode']) ? '' : $dbnews[$k]['mode'])."</select></td>\n".
			"<td class=\"txtC\"><input type=\"text\" size=\"20\" name=\"dbnews[$k][value]\" value=\"".(empty($dbnews[$k]['value']) ? '' : mhtmlspecialchars(stripslashes($dbnews[$k]['value'])))."\"></td>\n".
			"<td class=\"txtC w50\"><select style=\"vertical-align: middle;\" name=\"dbnews[$k][order]\">".makeoption($orderarr,empty($dbnews[$k]['order']) ? '' : $dbnews[$k]['order'])."</select></td>\n".
			"<td class=\"txtC w60\"><input type=\"text\" size=\"4\" name=\"dbnews[$k][prior]\" value=\"".(empty($dbnews[$k]['prior']) ? 0 : mhtmlspecialchars(stripslashes($dbnews[$k]['prior'])))."\"></td>\n".
			"</tr>";
		$i ++;
	}
	tabfooter('bdbsqlstr','����');
	if(!empty($dbnews) && $dbtable){
		$selectstr = '';
		$selectnum = $nprior = 0;
		$wherestr = $orderstr = $sqlstr = '';
		$orderarr = array();
		foreach($dbnews as $k => $v){
			if(!empty($v['adopt'])){
				$selectstr .= ($selectstr ? ',' : '').$k;
				$selectnum ++;
			}
			if(!empty($v['mode'])){
				if(in_array($v['mode'],array('IS NULL','IS NOT NULL',))){
					$wherestr .= ($wherestr ? ' AND ' : '').$k.' '.$v['mode'];
				}elseif(in_array($v['mode'],array('LIKE','NOT LIKE','REGEXP','NOT REGEXP',)) && $v['value'] != ''){
					$wherestr .= ($wherestr ? ' AND ' : '').$k." ".$v['mode']." '".$v['value']."'";
				}elseif(in_array($v['mode'],array('LIKE %...%','LIKE ...%','LIKE %...',)) && $v['value'] != ''){
					$wherestr .= ($wherestr ? ' AND ' : '').$k." ".str_replace(array('%...%','...%','%...'),array("'%".$v['value']."%'","'".$v['value']."%'","'%".$v['value']."'"),$v['mode']);
				}else{
					$wherestr .= ($wherestr ? ' AND ' : '').$k.' '.$v['mode']." '".$v['value']."'";
				}
			}
			if(!empty($v['order'])){
				$orderarr[$k.' '.$v['order']] = intval($v['prior']);
			}
		}
		if(!empty($orderarr)){
			asort($orderarr);
			foreach($orderarr as $k => $v) $orderstr .= ($orderstr ? ',' : '').$k;
		}
		$selectstr = $selectnum == count($dbnews) ? '*' : $selectstr;
		if($selectstr){
			$sqlstr = 'SELECT '.$selectstr.' FROM `'.$dbtable.'`';
			if($wherestr) $sqlstr .= ' WHERE '.$wherestr;
			if($orderstr) $sqlstr .= ' ORDER BY '.$orderstr;
		}
		tabheader('��ѯ�ִ����ɽ��');
		trbasic('��ѯ�ִ�','view_sqlstr',$sqlstr,'textarea',array('w' => 500,'h' => 300,));
		tabfooter();
	}
}
function thismodearr($type){
	global $modearr,$dbtypearr;
	$type = str_replace(strstr($type,'('),'',$type);
	$retarr = array('' => '');
	foreach($modearr as $k => $v){
		if(!$v || !in_array($type,$dbtypearr[$v])) $retarr[$k] = $k;
	}
	return $retarr;
}
?>