<?
(!defined('M_COM') || !defined('M_ADMIN')) && exit('No Permission');
aheader();
foreach(array('crprojects','crprices',) as $k) $$k = cls_cache::Read($k);
$currencys = fetch_arr();
if($action == 'currencysedit'){
	backnav('currency','type');
	if($re = $curuser->NoBackFunc('currency')) cls_message::show($re);
	if(!submitcheck('bcurrencyadd') && !submitcheck('bcurrencysedit')){
		tabheader('���ֹ���','currencysedit',"?entry=$entry&action=$action",'6');
		trcategory(array('ID','��������','��λ','ע���ʼֵ','ɾ��','����'));
		foreach($currencys as $k => $v){
			echo "<tr  class=\"txt\"><td class=\"txtC\">$k</td>\n".
				"<td class=\"txtC\"><input type=\"text\" size=\"12\" maxlength=\"30\" name=\"currencysnew[$k][cname]\" value=\"$v[cname]\"></td>\n".
				"<td class=\"txtC\"><input type=\"text\" size=\"12\" maxlength=\"30\" name=\"currencysnew[$k][unit]\" value=\"$v[unit]\"></td>\n".
				"<td class=\"txtC\"><input type=\"text\" size=\"12\" maxlength=\"30\" name=\"currencysnew[$k][initial]\" value=\"$v[initial]\"></td>\n".
				"<td class=\"txtC\"><a onclick=\"return deltip(this,$no_deepmode)\" href=\"?entry=$entry&action=currencydel&crid=$k\">ɾ��</a></td>\n".
				"<td class=\"txtC\"><a href=\"?entry=$entry&action=currencydetail&crid=$k\" onclick=\"return floatwin('open_currencydetail',this)\">����</a></td>\n".
				"</tr>\n";
		}
		tabfooter('bcurrencysedit','�޸�');

		tabheader('��ӻ���','currencyadd',"?entry=$entry&action=$action");
		trbasic('��������','currencyadd[cname]');
		trbasic('���ֵ�λ','currencyadd[unit]');
		trbasic('ע���ʼֵ','currencyadd[initial]');
		tabfooter('bcurrencyadd','���');
		a_guide('currencysedit');
	}elseif(submitcheck('bcurrencyadd')){
		$currencyadd['cname'] = trim($currencyadd['cname']);
		$currencyadd['unit'] = trim($currencyadd['unit']);
		$currencyadd['initial'] = max(0,intval($currencyadd['initial']));
		if(empty($currencyadd['cname'])) cls_message::show('���ϲ���ȫ',"?entry=$entry&action=$action");
		$db->query("INSERT INTO {$tblprefix}currencys SET 
					crid=".auto_insert_id('currencys').",
					cname='$currencyadd[cname]', 
					unit='$currencyadd[unit]', 
					initial='$currencyadd[initial]'");
		if($crid = $db->insert_id()){
			$db->query("ALTER TABLE {$tblprefix}members ADD currency$crid float NOT NULL default 0", 'SILENT');
			$db->query("CREATE TABLE {$tblprefix}currency$crid (
						id mediumint(8) unsigned NOT NULL auto_increment,
						value float NOT NULL default 0,
						mid mediumint(8) unsigned NOT NULL default 0,
						mname varchar(15) NOT NULL default '',
						fromid mediumint(8) unsigned NOT NULL default 0,
						fromname varchar(15) NOT NULL default '',
						createdate int(10) unsigned NOT NULL default 0,
						mode tinyint(1) unsigned NOT NULL default 0,
						remark varchar(255) NOT NULL default '',
						PRIMARY KEY (id))".(mysql_get_server_info() > '4.1' ? " ENGINE=MYISAM DEFAULT CHARSET=$dbcharset" : " TYPE=MYISAM"));
		}
		cls_CacheFile::Update('currencys');
		adminlog('��ӻ�������','��ӻ�������');
		cls_message::show('����������',"?entry=$entry&action=$action");
	}elseif(submitcheck('bcurrencysedit')){
		if(!empty($currencysnew)){
			foreach($currencysnew as $k => $v){
				(empty($v['initial']) || (int)$v['initial']<=0) && cls_message::show('����ʧ�ܣ����ֳ�ʼֵ����Ϊ����0������',"?entry=$entry&action=$action");
				$v['cname'] = empty($v['cname']) ? $currencys[$k]['cname'] : $v['cname'];
				$v['unit'] = trim($v['unit']);
				$v['initial'] = max(0,intval($v['initial']));
				$db->query("UPDATE {$tblprefix}currencys SET 
							cname='$v[cname]', 
							initial='$v[initial]', 
							unit='$v[unit]' 
							WHERE crid='$k'");
			}
		}
		cls_CacheFile::Update('currencys');
		adminlog('�༭��������','�޸Ļ��ֹ����б�');
		cls_message::show('���ֲ������', "?entry=$entry&action=currencysedit");
	}
}elseif($action == 'currencydetail' && $crid){
	if($re = $curuser->NoBackFunc('currency')) cls_message::show($re);
	if(!($currency = $currencys[$crid])) cls_message::show('��ָ����ȷ�Ļ��֡�');
	if(!submitcheck('bcurrencydetail')){
		tabheader('�༭����','currencydetail',"?entry=$entry&action=currencydetail&crid=$crid");
		trbasic('��������','currencynew[cname]',$currency['cname']);
		trbasic('���ֵ�λ','currencynew[unit]',$currency['unit']);
		trbasic('ע���ʼֵ','currencynew[initial]',$currency['initial']);
		trbasic('�����ֶ���/��ֵ','currencynew[saving]',$currency['saving'],'radio');
		tabfooter();
		tabheader('������������');
		$arr = cls_cache::exRead('crbases');
		foreach($arr as $k => $v){
			trbasic($v,"currencynew[bases][$k]",empty($currency['bases'][$k]) ? 0 : $currency['bases'][$k]);
		}
		tabfooter('bcurrencydetail','�޸�');
		a_guide('currencydetail');
	}else{
		$currencynew['bases'] = empty($currencynew['bases']) ? '' : addslashes(serialize($currencynew['bases']));
		$db->query("UPDATE {$tblprefix}currencys SET
				cname='$currencynew[cname]',
				unit='$currencynew[unit]',
				saving='$currencynew[saving]',
				initial='$currencynew[initial]',
				bases='$currencynew[bases]'
				WHERE crid='$crid'");
		cls_CacheFile::Update('currencys');
		adminlog('�༭��������','��ϸ�޸Ļ�������');
		cls_message::show('���ֱ༭���',axaction(6,"?entry=$entry&action=currencydetail&crid=$crid"));
	}
}elseif($action == 'currencydel' && $crid){
	backnav('currency','type');
	if($re = $curuser->NoBackFunc('currency')) cls_message::show($re);
	deep_allow($no_deepmode);
	if(!($currency = $currencys[$crid])) cls_message::show('��ָ����ȷ�Ļ��֡�');
	if(!submitcheck('confirm')){
		$message = "ɾ�����ָܻ���ȷ��ɾ����ѡ��Ŀ?<br><br>";
		$message .= "ȷ������>><a href=?entry=$entry&action=$action&crid=$crid&confirm=ok>ɾ��</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
		$message .= "��������>><a href=?entry=$entry&action=currencysedit>����</a>";
		cls_message::show($message);
	}
	$db->query("DROP TABLE IF EXISTS {$tblprefix}currency$crid",'SILENT');
	$db->query("ALTER TABLE {$tblprefix}members DROP currency$crid", 'SILENT'); 
	$db->query("DELETE FROM {$tblprefix}crprojects WHERE scrid='$crid' OR ecrid='$crid'", 'SILENT');
	$db->query("DELETE FROM {$tblprefix}crprices WHERE crid='$crid'", 'SILENT');
	$db->query("DELETE FROM {$tblprefix}currencys WHERE crid='$crid'", 'SILENT');
	adminlog('ɾ����������-'.$currency['cname']);
	cls_CacheFile::Update('currencys');
	cls_CacheFile::Update('crprojects');
	cls_CacheFile::Update('crprices');
	cls_message::show('ָ���Ļ��������ѳɹ�ɾ����',"?entry=$entry&action=currencysedit");
}elseif($action == 'crprices'){
	backnav('currency','price');
	if($re = $curuser->NoBackFunc('currency')) cls_message::show($re);
	$cridsarr = cridsarr(1);
	empty($cridsarr) && cls_message::show('�붨���������');
	if(!submitcheck('bcrpricesedit') && !submitcheck('bcrpricesadd')){
		tabheader('�۸񷽰�����','crpricesedit',"?entry=$entry&action=crprices",'10');
		trcategory(array("<input class=\"checkbox\" type=\"checkbox\" name=\"chkall\" onclick=\"deltip(this,$no_deepmode,checkall,this.form,'delete','chkall')\">ɾ?|L",'�۸�����','����','����','�ĵ����','�ĵ�����','�ĵ�����','��������','��������'));
		foreach($crprices as $k => $crprice){
			echo "<tr class=\"txt\">".
				"<td class=\"txtL w40\"><input class=\"checkbox\" type=\"checkbox\" name=\"delete[$k]\" value=\"$k\" onclick=\"deltip(this,$no_deepmode)\"></td>\n".
				"<td class=\"txtC\"><input type=\"text\" size=\"20\" name=\"crpricesnew[$k][cname]\" value=\"$crprice[cname]\"></td>\n".
				"<td class=\"txtC w60\">".$cridsarr[$crprice['crid']]."</td>\n".
				"<td class=\"txtC w60\">".$crprice['crvalue']."</td>\n".
				"<td class=\"txtC w60\"><input class=\"checkbox\" type=\"checkbox\" name=\"crpricesnew[$k][tax]\" value=\"1\"".(empty($crprice['tax']) ? "" : " checked")."></td>\n".
				"<td class=\"txtC w60\"><input class=\"checkbox\" type=\"checkbox\" name=\"crpricesnew[$k][sale]\" value=\"1\"".(empty($crprice['sale']) ? "" : " checked")."></td>\n".
				"<td class=\"txtC w60\"><input class=\"checkbox\" type=\"checkbox\" name=\"crpricesnew[$k][award]\" value=\"1\"".(empty($crprice['award']) ? "" : " checked")."></td>\n".
				"<td class=\"txtC w60\"><input class=\"checkbox\" type=\"checkbox\" name=\"crpricesnew[$k][ftax]\" value=\"1\"".(empty($crprice['ftax']) ? "" : " checked")."></td>\n".
				"<td class=\"txtC w60\"><input class=\"checkbox\" type=\"checkbox\" name=\"crpricesnew[$k][fsale]\" value=\"1\"".(empty($crprice['fsale']) ? "" : " checked")."></td></tr>\n";
		}
		tabfooter('bcrpricesedit','�޸�');

		tabheader('��Ӽ۸񷽰�','crpricesadd',"?entry=$entry&action=crprices");
		trbasic('�۸�����','crpriceadd[cname]');
		trbasic('��������','crpriceadd[crid]',makeoption($cridsarr),'select');
		trbasic('��������','crpriceadd[crvalue]');
		tabfooter('bcrpricesadd','���');
		a_guide('crprices');
	}elseif(submitcheck('bcrpricesadd')){
		$crpriceadd['crvalue'] = empty($crpriceadd['crvalue']) ? 0 : round($crpriceadd['crvalue'],2);
		$crpriceadd['cname'] = trim($crpriceadd['cname']);
		if(empty($crpriceadd['crvalue']) || empty($crpriceadd['cname'])) cls_message::show('���ϲ���ȫ',"?entry=$entry&action=crprices");
		$crpriceadd['ename'] = $crpriceadd['crid'].'_'.$crpriceadd['crvalue'];
		if(in_array($crpriceadd['ename'],array_keys($crprices))) cls_message::show('�۸������ظ�',"?entry=$entry&action=crprices");
		$db->query("INSERT INTO {$tblprefix}crprices SET 
					cname='$crpriceadd[cname]', 
					ename='$crpriceadd[ename]', 
					crid='$crpriceadd[crid]', 
					crvalue='$crpriceadd[crvalue]'
					");
		cls_CacheFile::Update('crprices');
		adminlog('��ӻ��ּ۸񷽰�','��ӻ��ּ۸񷽰�');
		cls_message::show('��ӻ��ּ۸񷽰��ɹ�', "?entry=$entry&action=crprices");
	}elseif(submitcheck('bcrpricesedit')){
		if(!empty($delete) && deep_allow($no_deepmode)){
			$stids = stidsarr(1);
			foreach($delete as $k){//�����ʹ�øû��ֵĵط�ȫ�����
				$db->query("UPDATE {$tblprefix}catalogs  SET taxcp='' WHERE taxcp='$k'",'SILENT');
				$db->query("UPDATE {$tblprefix}catalogs  SET awardcp='' WHERE awardcp='$k'",'SILENT');
				$db->query("UPDATE {$tblprefix}catalogs  SET ftaxcp='' WHERE ftaxcp='$k'",'SILENT');
				$db->query("DELETE FROM {$tblprefix}crprices WHERE ename='$k'",'SILENT');
				unset($crpricesnew[$k]);
			}
		}
		if(!empty($crpricesnew)){
			foreach($crpricesnew as $k => $crpricenew){
				$crpricenew['cname'] = empty($crpricenew['cname']) ? $crprices[$k]['cname'] : $crpricenew['cname'];
				$sqlstr = "cname='$crpricenew[cname]'";
				$arr = explode('_',$k);
				foreach(array('tax','sale','award','ftax','fsale') as $varname){
					$crpricenew[$varname] = empty($crpricenew[$varname]) ? 0 : 1;
					if($arr[1] < 0 && $varname != 'award') $crpricenew[$varname] = 0;
					$sqlstr .= ",$varname=".$crpricenew[$varname];
				}
				$db->query("UPDATE {$tblprefix}crprices SET $sqlstr WHERE ename='$k'");
			}
		}
		cls_CacheFile::Update('crprices');
		adminlog('�༭���ּ۸񷽰�','�༭���ּ۸񷽰������б�');
		cls_message::show('���ּ۸�༭���',"?entry=$entry&action=crprices");
	}
}elseif($action == 'crprojects'){
	backnav('currency','project');
	if($re = $curuser->NoBackFunc('currency')) cls_message::show($re);
	$crids = cridsarr(1);
	if(count($crids) < 2) cls_message::show('�붨������������');
	if(!submitcheck('bcrprojectsedit') && !submitcheck('bcrprojectsadd')){
		tabheader('�һ���������','crprojectsedit',"?entry=$entry&action=crprojects",'5');
		trcategory(array("<input class=\"checkbox\" type=\"checkbox\" name=\"chkall\" onclick=\"deltip(this,$no_deepmode,checkall,this.form,'delete','chkall')\">ɾ?|L",'��Դ����','��Դ����ֵ','�һ�����','�һ�����ֵ'));
		foreach($crprojects as $crpid => $crproject){
			echo "<tr class=\"txt\">".
				"<td class=\"txtL w60\"><input class=\"checkbox\" type=\"checkbox\" name=\"delete[$crpid]\" value=\"$crpid\" onclick=\"deltip(this,$no_deepmode)\"></td>\n".
				"<td class=\"txtC w100\">".$crids[$crproject['scrid']]."</td>\n".
				"<td class=\"txtC\"><input type=\"text\" size=\"20\" name=\"crprojectsnew[$crpid][scurrency]\" value=\"$crproject[scurrency]\"></td>\n".
				"<td class=\"txtC w100\">".$crids[$crproject['ecrid']]."</td>\n".
				"<td class=\"txtC\"><input type=\"text\" size=\"20\" name=\"crprojectsnew[$crpid][ecurrency]\" value=\"$crproject[ecurrency]\"></td></tr>\n";
		}
		tabfooter('bcrprojectsedit','�޸�');

		tabheader('��Ӷһ�����','crprojectsadd',"?entry=$entry&action=crprojects");
		trbasic('��Դ����','crprojectadd[scrid]',makeoption($crids),'select');
		trbasic('��Դ����ֵ','crprojectadd[scurrency]');
		trbasic('�һ�����','crprojectadd[ecrid]',makeoption($crids),'select');
		trbasic('�һ�����ֵ','crprojectadd[ecurrency]');
		tabfooter('bcrprojectsadd','���');
		a_guide('crprojects');
	}elseif(submitcheck('bcrprojectsadd')){
		if($crprojectadd['scrid'] == $crprojectadd['ecrid']) cls_message::show('��ͬ����֮�䲻�ܶһ�',"?entry=$entry&action=crprojects");
		$crprojectadd['ename'] = $crprojectadd['scrid'].'_'.$crprojectadd['ecrid'];
		$enamearr = array();
		foreach($crprojects as $v) $enamearr[] = $v['ename'];
		if(in_array($crprojectadd['ename'], $enamearr)) cls_message::show('�һ������Ѵ���',"?entry=$entry&action=crprojects");
		$crprojectadd['scurrency'] = max(1,intval($crprojectadd['scurrency']));
		$crprojectadd['ecurrency'] = max(1,intval($crprojectadd['ecurrency']));
		$db->query("INSERT INTO {$tblprefix}crprojects SET 
					ename='$crprojectadd[ename]', 
					scrid='$crprojectadd[scrid]', 
					scurrency='$crprojectadd[scurrency]', 
					ecrid='$crprojectadd[ecrid]', 
					ecurrency='$crprojectadd[ecurrency]'
					");
		cls_CacheFile::Update('crprojects');
		adminlog('��ӻ��ֶһ�����','��ӻ��ֶһ�����');
		cls_message::show('�һ�����������', "?entry=$entry&action=crprojects");
	}elseif(submitcheck('bcrprojectsedit')){
		if(!empty($delete) && deep_allow($no_deepmode)){
			foreach($delete as $k){
				$db->query("DELETE FROM {$tblprefix}crprojects WHERE crpid=$k");
				unset($crprojectsnew[$k]);
			}
		}
		foreach($crprojectsnew as $crpid => $crprojectnew){
			$crprojectnew['scurrency'] = max(1,intval($crprojectnew['scurrency']));
			$crprojectnew['ecurrency'] = max(1,intval($crprojectnew['ecurrency']));
			$db->query("UPDATE {$tblprefix}crprojects SET 
						scurrency='$crprojectnew[scurrency]',
						ecurrency='$crprojectnew[ecurrency]'
						WHERE crpid='$crpid'");
		}
		cls_CacheFile::Update('crprojects');
		adminlog('�༭���ֶһ�����','�༭���ֶһ����������б�');
		cls_message::show('�һ������޸����',"?entry=$entry&action=crprojects");
	}
}elseif($action == 'currencysaving'){
	if($re = $curuser->NoBackFunc('save')) cls_message::show($re);
	if(!submitcheck('bsubmit')){
		tabheader('���ֳ�/��ֵ &nbsp;>><a href="?entry=currencys&action=currencysedit&isframe=1" target="_blank">�������</a>','currencysaving',"?entry=$entry&action=currencysaving",2,0,1);
		if(isset($mname)){
			trbasic('��Ա�ʺ�','',$mname,'');
			trhidden('crsaving[mname]',$mname);
		}else trbasic('��Ա�ʺ�','crsaving[mname]','','text',array('guide' => '����ʺ��Զ��ŷָ���','w' => 50,'validate' => " rule=\"text\" must=\"1\" min=\"3\""));
		$crids = array(0 => '�ֽ�');foreach($currencys as $k => $v) $v['saving'] && $crids[$k] = $v['cname'];
		if(isset($crid)){
			$crid = max(0,intval($crid));
			trbasic('���ֵ��������','',$currencys[$crid]['cname'],'');
			trhidden('crsaving[crid]',$crid);
		}else trbasic('���ֵ��������','',makeradio('crsaving[crid]',$crids),'');
		if(isset($mode)){
			$mode = empty($mode) ? 0 : 1;
			trbasic('���ģʽ','',$mode ? '��ֵ' : '��ֵ','');
			trhidden('crsaving[mode]',$mode);
		}else trbasic('���ģʽ','',makeradio('crsaving[mode]',array(0 => '��ֵ',1 => '��ֵ')),'');
		if(isset($currency)){
			$currency = max(0,round($currency,2));
			trbasic('���ֵ����','',$currency,'');
			trhidden('crsaving[currency]',$currency);
		}else trbasic('���ֵ����','crsaving[currency]','','text',array('guide' => '��������С����','validate' => " rule=\"float\" must=\"1\" min=\"0\""));
		trbasic('��ע˵��','crsaving[remark]','','textarea');
		tabfooter('bsubmit');
		a_guide('currencysaving');
	}else{
		$crsaving['mname'] = trim($crsaving['mname']);
		$crsaving['currency'] = max(0,round($crsaving['currency'],2));
		if(empty($crsaving['mname']) || empty($crsaving['currency'])) cls_message::show('���ϲ���ȫ',M_REFERER);
		$mnames = array_unique(array_filter(explode(',',$crsaving['mname'])));
		$actuser = new cls_userinfo;
		$num = 0;
		foreach($mnames as $v){
			if(!($v = trim($v))) continue;
			$actuser->activeuserbyname($v);
			$actuser->saving($crsaving['crid'],$crsaving['mode'],$crsaving['currency'],@$crsaving['remark']);
			$num ++;
		}
		unset($actuser);
		adminlog('��Ա���ֳ�/��ֵ');
		cls_message::show("$num ����Ա".($crsaving['mode'] ? '��ֵ' : '��ֵ')."�ɹ���",axaction(6,"?entry=$entry&action=currencysaving"));
	}
}elseif($action == 'cradminlogs'){
	backnav('cysave','record');
	if($re = $curuser->NoBackFunc('save')) cls_message::show($re);
	$crid = empty($crid) ? 0 : max(0,intval($crid));
	if($crid && empty($currencys[$crid])) cls_message::show('��ָ����ȷ�Ļ������͡�');
	
	$page = empty($page) ? 1 : max(1,intval($page));
	$mode = isset($mode) ? $mode : -1;
	$keyword = empty($keyword) ? '' : $keyword;
	$indays = empty($indays) ? 0 : max(0,intval($indays));
	$outdays = empty($outdays) ? 0 : max(0,intval($outdays));
	
	$wheresql = 'WHERE mode=1';
	$fromsql = "FROM {$tblprefix}currency$crid";
	
	if($mode != -1) $wheresql .= " AND value".($mode ? '<' : '>')."0";
	$keyword && $wheresql .= " AND (mname ".sqlkw($keyword)." OR fromname ".sqlkw($keyword).")";
	$indays && $wheresql .= " AND createdate>'".($timestamp - 86400 * $indays)."'";
	$outdays && $wheresql .= " AND createdate<'".($timestamp - 86400 * $outdays)."'";
	
	$filterstr = '';
	foreach(array('keyword','indays','outdays',) as $k) $$k && $filterstr .= "&$k=".rawurlencode(stripslashes($$k));
	foreach(array('mode',) as $k) $$k != -1 && $filterstr .= "&$k=".$$k;
	
	echo form_str($actionid.'arcsedit',"?entry=$entry&action=$action&crid=$crid&page=$page");
	tabheader_e();
	echo "<tr><td colspan=\"2\" class=\"txt txtleft\">";
	echo "<select style=\"vertical-align: middle;\" name=\"crid\" onchange=\"redirect('?entry=$entry&action=$action&crid=' + this.options[this.selectedIndex].value);\">".makeoption(cridsarr(1),$crid)."</select>&nbsp; &nbsp; &nbsp; ";
	echo "<input class=\"text\" name=\"keyword\" type=\"text\" value=\"$keyword\" size=\"8\" style=\"vertical-align: middle;\" title=\"������Ա������\">&nbsp; ";
	echo "<select style=\"vertical-align: middle;\" name=\"mode\">".makeoption(array('-1' => '���״̬','0' => '��ֵ','1' => '��ֵ',),$mode)."</select>&nbsp; ";
	echo "<input class=\"text\" name=\"outdays\" type=\"text\" value=\"$outdays\" size=\"4\" style=\"vertical-align: middle;\">��ǰ&nbsp; ";
	echo "<input class=\"text\" name=\"indays\" type=\"text\" value=\"$indays\" size=\"4\" style=\"vertical-align: middle;\">����&nbsp; ";
	echo strbutton('bfilter','ɸѡ');
	tabfooter();
	
	$pagetmp = $page;
	do{
		$query = $db->query("SELECT * $fromsql $wheresql ORDER BY id DESC LIMIT ".(($pagetmp - 1) * $atpp).",$atpp");
		$pagetmp--;
	} while(!$db->num_rows($query) && $pagetmp);
	
	$itemstr = '';
	$sn = $pagetmp * $atpp;
	while($r = $db->fetch_array($query)){
		$sn ++;
		$modestr = $r['value'] > 0 ? '+' : '-';
		$createdatestr = date("$dateformat $timeformat", $r['createdate']);
		$currenystr = $crid ? $currencys[$crid]['cname'] : '�ֽ�';
		$valuestr = abs($r['value']);
		if(empty($r['remark'])){
			$remarkstr = '';
		}elseif(mb_strlen($r['remark'],$mcharset) < 20){
			$remarkstr = htmlspecialchars($r['remark']);
		}else{
			$remarkstr = "<a id=\"{$actionid}_info_$r[id]\" href=\"?entry=$entry&action=crlogremark&crid=$crid&id=$r[id]\" onclick=\"return showInfo(this.id,this.href)\">[��ϸ]</a>";
		}
		$itemstr .= "<tr class=\"txt\"><td class=\"txtC w40\">$sn</td>\n".
			"<td class=\"txtL\">$r[mname]</td>\n".
			"<td class=\"txtL\">$r[fromname]</td>\n".
			"<td class=\"txtC\">$currenystr</td>\n".
			"<td class=\"txtR\">$modestr</td>\n".
			"<td class=\"txtL\">$valuestr</td>\n".
			"<td class=\"txtL\">$createdatestr</td>\n".
			"<td class=\"txtL\">$remarkstr</td>\n".
			"</tr>\n";
	}

	tabheader(($crid ? $currencys[$crid]['cname'] : '�ֽ�')."��ۼ�¼ &nbsp;>><a href=\"?entry=$entry&action=currencysaving\" onclick=\"return floatwin('open_currencys',this)\">�ֶ���ۻ���</a>",'','',8);
	trcategory(array('���',array('������Ա','txtL'),array('������','txtL'),'����',array('&nbsp;','txtR'),array('����','txtL'),array('��������','txtL'),array('��ע','txtL')));
	echo $itemstr;
	tabfooter();
	echo multi($db->result_one("SELECT count(*) $fromsql $wheresql"),$atpp,$page,"?entry=$entry&action=$action&crid=$crid$filterstr");

}elseif($action == 'crlogs'){
	backnav('cysave','currency');
	if($re = $curuser->NoBackFunc('record')) cls_message::show($re);
	$crid = empty($crid) ? 0 : max(0,intval($crid));
	if($crid && empty($currencys[$crid])) cls_message::show('��ָ����ȷ�Ļ������͡�');
	
	$page = empty($page) ? 1 : max(1,intval($page));
	$mode = isset($mode) ? $mode : -1;
	$keyword = empty($keyword) ? '' : $keyword;
	$indays = empty($indays) ? 0 : max(0,intval($indays));
	$outdays = empty($outdays) ? 0 : max(0,intval($outdays));
	
	$wheresql = 'WHERE mode<>1';
	$fromsql = "FROM {$tblprefix}currency$crid";
	
	if($mode != -1) $wheresql .= " AND value".($mode ? '<' : '>')."0";
	$keyword && $wheresql .= " AND (mname ".sqlkw($keyword)." OR fromname ".sqlkw($keyword).")";
	$indays && $wheresql .= " AND createdate>'".($timestamp - 86400 * $indays)."'";
	$outdays && $wheresql .= " AND createdate<'".($timestamp - 86400 * $outdays)."'";
	
	$filterstr = '';
	foreach(array('keyword','indays','outdays',) as $k) $$k && $filterstr .= "&$k=".rawurlencode(stripslashes($$k));
	foreach(array('mode',) as $k) $$k != -1 && $filterstr .= "&$k=".$$k;
	
	echo form_str($actionid.'arcsedit',"?entry=$entry&action=$action&crid=$crid&page=$page");
	tabheader_e();
	echo "<tr><td colspan=\"2\" class=\"txt txtleft\">";
	echo "<select style=\"vertical-align: middle;\" name=\"crid\" onchange=\"redirect('?entry=$entry&action=$action&crid=' + this.options[this.selectedIndex].value);\">".makeoption(cridsarr(1),$crid)."</select>&nbsp; &nbsp; &nbsp; ";
	echo "<input class=\"text\" name=\"keyword\" type=\"text\" value=\"$keyword\" size=\"8\" style=\"vertical-align: middle;\" title=\"������Ա������\">&nbsp; ";
	echo "<select style=\"vertical-align: middle;\" name=\"mode\">".makeoption(array('-1' => '�Ӽ���ʽ','0' => '��ֵ','1' => '��ֵ',),$mode)."</select>&nbsp; ";
	echo "<input class=\"text\" name=\"outdays\" type=\"text\" value=\"$outdays\" size=\"4\" style=\"vertical-align: middle;\">��ǰ&nbsp; ";
	echo "<input class=\"text\" name=\"indays\" type=\"text\" value=\"$indays\" size=\"4\" style=\"vertical-align: middle;\">����&nbsp; ";
	echo strbutton('bfilter','ɸѡ');
	tabfooter();
	
	$pagetmp = $page;
	do{
		$query = $db->query("SELECT * $fromsql $wheresql ORDER BY id DESC LIMIT ".(($pagetmp - 1) * $atpp).",$atpp");
		$pagetmp--;
	} while(!$db->num_rows($query) && $pagetmp);
	
	$itemstr = '';
	$sn = $pagetmp * $atpp;
	while($r = $db->fetch_array($query)){
		$sn ++;
		$modestr = $r['value'] > 0 ? '+' : '-';
		$createdatestr = date("$dateformat $timeformat", $r['createdate']);
		$currenystr = $crid ? $currencys[$crid]['cname'] : '�ֽ�';
		$valuestr = abs($r['value']);
		if(empty($r['remark'])){
			$remarkstr = '';
		}elseif(mb_strlen($r['remark'],$mcharset) < 20){
			$remarkstr = htmlspecialchars($r['remark']);
		}else{
			$remarkstr = "<a id=\"{$actionid}_info_$r[id]\" href=\"?entry=$entry&action=crlogremark&crid=$crid&id=$r[id]\" onclick=\"return showInfo(this.id,this.href)\">[��ϸ]</a>";
		}
		$itemstr .= "<tr class=\"txt\"><td class=\"txtC w40\">$sn</td>\n".
			"<td class=\"txtL\">$r[mname]</td>\n".
			"<td class=\"txtL\">$r[fromname]</td>\n".
			"<td class=\"txtC\">$currenystr</td>\n".
			"<td class=\"txtR\">$modestr</td>\n".
			"<td class=\"txtL\">$valuestr</td>\n".
			"<td class=\"txtL\">$createdatestr</td>\n".
			"<td class=\"txtL\">$remarkstr</td>\n".
			"</tr>\n";
	}

	tabheader(($crid ? $currencys[$crid]['cname'] : '�ֽ�').'�����־','','',8);
	trcategory(array('���',array('������Ա','txtL'),array('������','txtL'),'����',array('&nbsp;','txtR'),array('����','txtL'),array('��������','txtL'),array('��ע','txtL')));
	echo $itemstr;
	tabfooter();
	echo multi($db->result_one("SELECT count(*) $fromsql $wheresql"),$atpp,$page,"?entry=$entry&action=$action&crid=$crid$filterstr");

}elseif($action == 'crlogremark' && $id){
	$crid = empty($crid) ? 0 : max(0,intval($crid));
	if($crid && empty($currencys[$crid])) cls_message::show('��ָ����ȷ�Ļ������͡�');
	!($remark = $db->result_one("SELECT remark FROM {$tblprefix}currency$crid WHERE id='$id'")) && cls_message::show('��ָ�����ֵ��¼��');
	tabheader('���ֵ��ע');
	trbasic('��ע˵��','',$remark,'textarea');
	tabfooter();
}
function fetch_arr(){
	global $db,$tblprefix;
	$rets = array();
	$query = $db->query("SELECT * FROM {$tblprefix}currencys ORDER BY crid");
	while($r = $db->fetch_array($query)){
		if(empty($r['bases']) || !is_array($r['bases'] = @unserialize($r['bases']))) $r['bases'] = array();
		$rets[$r['crid']] = $r;
	}
	return $rets;
}

?>
