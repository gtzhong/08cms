<?
(!defined('M_COM') || !defined('M_ADMIN')) && exit('No Permission');
aheader();
foreach(array('mchannels','catalogs','cotypes','mtconfigs','channels','grouptypes','currencys','rprojects','amconfigs',) as $k) $$k = cls_cache::Read($k);
if($action == 'edit'){
	backnav('backarea','amember');
	if($re = $curuser->NoBackFunc('amember')) cls_message::show($re);
	$page = !empty($page) ? max(1, intval($page)) : 1;
	submitcheck('bfilter') && $page = 1;
	$keyword = empty($keyword) ? '' : $keyword;
	$ugid2 = empty($ugid2) ?  0 : max(0,intval($ugid2));
	$wheresql = 'WHERE m.grouptype2'.($ugid2 ? "='$ugid2'" : '<>0');
	$fromsql = "FROM {$tblprefix}members m";

	$keyword && $wheresql .= " AND m.mname ".sqlkw($keyword);
	$ugid2 && $wheresql .= " AND m.grouptype2='$ugid2'";

	$filterstr = '';
	foreach(array('keyword','ugid2') as $k) $$k && $filterstr .= "&$k=".rawurlencode(stripslashes($$k));
	$wheresql && $wheresql = 'WHERE '.substr($wheresql,5);

	if(!submitcheck('bsubmit')){
		echo form_str($actionid.'memberedit',"?entry=$entry&action=$action&page=$page");
		tabheader_e();
		echo "<tr><td colspan=\"2\" class=\"txt txtleft\">";
		echo "<input class=\"text\" name=\"keyword\" type=\"text\" value=\"$keyword\" size=\"8\" style=\"vertical-align: middle;\" title=\"�����û���\">&nbsp; ";
		echo "<select style=\"vertical-align: middle;\" name=\"ugid2\">".makeoption(array('0' => '���޹�����') + ugidsarr(2),$ugid2)."</select>&nbsp; ";
		echo strbutton('bfilter','ɸѡ');
		echo "</td></tr>";
		tabfooter();
		//�б���
		tabheader("����Ա�б� &nbsp; &nbsp;>><a href=\"?entry=$entry&action=add\" onclick=\"return floatwin('open_amembers',this)\">��ӹ���Ա</a>",'','',10);
		$cy_arr = array("<input class=\"checkbox\" type=\"checkbox\" name=\"chkall\" onclick=\"checkall(this.form, 'selectid', 'chkall')\">",array('����Ա�ʺ�','txtL'),);
		$cy_arr[] = '������';
		$cy_arr[] = '��������';
		$cy_arr[] = array('���ӹ����ɫ','txtL');
		$cy_arr[] = '����ͳ��';
		$cy_arr[] = '���';
		$cy_arr[] = '�������';
		trcategory($cy_arr);


		$pagetmp = $page;
		do{
			$query = $db->query("SELECT * $fromsql $wheresql ORDER BY mid DESC LIMIT ".(($pagetmp - 1) * $atpp).",$atpp");
			$pagetmp--;
		} while(!$db->num_rows($query) && $pagetmp);

		$itemstr = '';
		while($r = $db->fetch_array($query)){
			$selectstr = "<input class=\"checkbox\" type=\"checkbox\" name=\"selectid[$r[mid]]\" value=\"$r[mid]\">";
			$mnamestr = $r['mname'];
			$checkstr = $r['checked'] ? 'Y' : '-';
			$arr = cls_cache::Read('usergroups',2);
			$ugid2str = @$arr[$r['grouptype2']]['cname'];
			$enddatestr = !$r['grouptype2date'] ? '-': date('Y-m-d',$r['grouptype2date']);
			$amcids = $r['amcids'] ? explode(',',$r['amcids']) : array();
			$amcidstr = '';foreach($amcids as $k) !empty($amconfigs[$k]) && $amcidstr .= $amconfigs[$k]['cname'].' ';
			$lastvisitstr = $r['lastvisit'] ? date('Y-m-d',$r['lastvisit']) : '-';

			$itemstr .= "<tr class=\"txt\"><td class=\"txtC w40\">$selectstr</td><td class=\"txtL 80\">$mnamestr</td>\n";
			$itemstr .= "<td class=\"txtC\">$ugid2str</td>\n";
			$itemstr .= "<td class=\"txtC\">$enddatestr</td>\n";
			$itemstr .= "<td class=\"txtL\">$amcidstr</td>\n";
			$itemstr .= "<td class=\"txtC\"><a id=\"{$actionid}_info_$r[mid]\" href=\"?entry=workstat&mid=$r[mid]&mname=$r[mname]\" onclick=\"return floatwin('open_$action',this)\">�鿴</a></td>\n";
			$itemstr .= "<td class=\"txtC w35\">$checkstr</td>\n";
			$itemstr .= "<td class=\"txtC w80\">$lastvisitstr</td>\n";
			$itemstr .= "</tr>\n";
		}
		$counts = $db->result_one("SELECT count(*) $fromsql $wheresql");
		$multi = multi($counts, $atpp, $page, "?entry=$entry&action=$action$filterstr");
		echo $itemstr;
		tabfooter();
		echo $multi;
		//������
		tabheader('������Ŀ');
		$s_arr = array();
		$s_arr['check'] = '���';
		$s_arr['uncheck'] = '����';
		if($s_arr){
			$soperatestr = '';
			foreach($s_arr as $k => $v) $soperatestr .= "<input class=\"checkbox\" type=\"checkbox\" name=\"arcdeal[$k]\" value=\"1\">$v &nbsp;";
			trbasic('ѡ�������Ŀ','',$soperatestr,'');
		}
		$str = "<select style=\"vertical-align: middle;\" name=\"arcugid2\">".makeoption(array('0' => '���������') + ugidsarr(2))."</select>&nbsp; <input type=\"text\" size=\"15\" id=\"arcugid2date\" name=\"arcugid2date\" value=\"\" class=\"Wdate\" onfocus=\"WdatePicker({readOnly:true})\" />";
		trbasic("<input class=\"checkbox\" type=\"checkbox\" name=\"arcdeal[ugid2]\" value=\"1\">���ù�����",'',$str,'');
		$arr = array();foreach($amconfigs as $k => $v) $arr[$k] = $v['cname'];
		trbasic("<input class=\"checkbox\" type=\"checkbox\" name=\"arcdeal[amcids]\" value=\"1\">���ӹ����ɫ",'',makecheckbox("arcamcids[]",$arr,array(),5),'',array('guide' => '�����ڹ��������õĹ����ɫͬʱ��Ч��'));
		tabfooter('bsubmit');
		a_guide('amembers');
	}else{
		if(empty($arcdeal)) cls_message::show('��ѡ�������Ŀ',"?entry=$entry&action=$action&page=$page$filterstr");
		if(empty($selectid)) cls_message::show('��ѡ���Ա',"?entry=$entry&action=$action&page=$page$filterstr");
		$actuser = new cls_userinfo;
		foreach($selectid as $id){
			$actuser->activeuser($id);
			if(!empty($arcdeal['check'])){
				$actuser->check(1);
			}elseif(!empty($arcdeal['uncheck'])){
				$actuser->check(0);
			}
			if(!empty($arcdeal['ugid2'])){
				$actuser->handgroup(2,$arcugid2,!$arcugid2 || !cls_string::isDate($arcugid2date) ? 0 : strtotime($arcugid2date));
			}
			if(!empty($arcdeal['amcids'])){
				$actuser->updatefield('amcids',empty($arcamcids) ? '' : implode(',',$arcamcids));
			}
			$actuser->updatedb();
			$actuser->init();
		}
		unset($actuser);
		adminlog('����Ա����','��Ա�б�������');
		cls_message::show('����Ա������ɡ�',"?entry=$entry&action=$action&page=$page$filterstr");
	}
}elseif($action == 'add'){
	if($re = $curuser->NoBackFunc('amember')) cls_message::show($re);
	if(!submitcheck('bsubmit')){
		tabheader('��ӹ���Ա', 'addadmin', "?entry=$entry&action=$action",2,1,1);
		trbasic('�û�����*', 'mname','','text',array('validate'=>' rule="text" must="1" min="3" max="15"','guide' => '��Ҫ���ѳɹ�ע��Ļ�Ա��'));
		$str = "<select style=\"vertical-align: middle;\" name=\"ugid2\" rule=\"must\">".makeoption(ugidsarr(2))."</select>&nbsp; <input type=\"text\" size=\"15\" id=\"ugid2date\" name=\"ugid2date\" value=\"\" class=\"Wdate\" onfocus=\"WdatePicker({readOnly:true})\" />";
		trbasic('���ù�����*', '',$str,'',array('guide' => '����Ϊ������Ч��������һ��>�������ڵ�ʱ�䣻ʱ��ܵ�������ʱ���൱�ڽ��������ϵ��������ӹ���Աʧ�ܡ�'));
		tabfooter('bsubmit');
	}else{
		$mname = trim(strip_tags($mname));
		if(empty($mname) || empty($ugid2)) cls_message::show('�������Ա�ʺż�������',M_REFERER);
		$actuser = new cls_userinfo;
		$actuser->activeuserbyname($mname);
		if(!$actuser->info['mid'] || $actuser->info['isfounder']) cls_message::show('��ָ����ȷ�Ļ�Ա��',M_REFERER);
		$actuser->handgroup(2,$ugid2,!$ugid2 || !cls_string::isDate($ugid2date) ? 0 : strtotime($ugid2date));
		$actuser->updatedb();
		adminlog('��Ӻ�̨����Ա');
		cls_message::show('����Ա��ӳɹ�',axaction(6,M_REFERER));
	}
}

?>
