<?php
(!defined('M_COM') || !defined('M_ADMIN')) && exit('No Permission');
aheader();
if($re = $curuser->NoBackFunc('tpl')) cls_message::show($re);
foreach(array('catalogs','cotypes','grouptypes','mcnodes','mcntpls','mtpls',) as $k) $$k = cls_cache::Read($k);
$mcnvars = array('caid' => '��Ŀ');
$mcnvars['mcnid'] = '�Զ���ڵ�';
foreach($cotypes as $k => $v) !$v['self_reg'] && $mcnvars['ccid'.$k] = "[����] {$v['cname']}";
foreach($grouptypes as $k => $v) !$v['issystem'] && $mcnvars['ugid'.$k] = "[��Ա��] {$v['cname']}";
empty($action) && $action = 'mcnodesedit';
if($action == 'cntpladd'){
	echo "<title>��ӽڵ�����</title>";
	if(!submitcheck('bsubmit')){
		tabheader('��ӽڵ�����','cntpladd',"?entry=$entry&action=$action",2,0,1);
		trbasic('�ڵ���������','cntplnew[cname]','','text',array('validate'=>makesubmitstr('cntplnew[cname]',1,1,4,30)));
		tabfooter('bsubmit','���');
		a_guide('mcntpladd');
	} else {
		if(!($cntplnew['cname'] = trim(strip_tags($cntplnew['cname'])))) cls_message::show('��������⣡',M_REFERER);
		$tid = auto_insert_id('mcntpls');
		$mcntpls[$tid] = array('tid' => $tid,'cname' => $cntplnew['cname'],'addnum' =>0,'vieworder' => 0);
		cls_CacheFile::Save($mcntpls,'mcntpls','mcntpls');
		adminlog('��ӽڵ�����');
		cls_message::show('�ڵ�����������',axaction(36,"?entry=$entry&action=cntpldetail&tid=$tid"));
	}
}elseif($action == 'cntpldetail' && $tid){
	if(!($cntpl = @$mcntpls[$tid])) cls_message::show('��ѡ��ڵ�����');
	echo "<title>�ڵ����� - $cntpl[cname]</title>";
	if(!submitcheck('bsubmit')){
		tabheader("�ڵ�����&nbsp;&nbsp;[$cntpl[cname]]",'cntpldetail',"?entry=$entry&action=$action&tid=$tid");
		$arr = array();for($i = 0;$i <= $mcn_max_addno;$i ++) $arr[$i] = $i;
		$addnum = empty($cntpl['addnum']) ? 0 : $cntpl['addnum'];
		trbasic('����ҳ����','',makeradio('fmdata[addnum]',$arr,$addnum),'');
		tabfooter();
		
		$cfgs = @$cntpl['cfgs'];
		for($i = 0;$i <= $mcn_max_addno;$i ++){
			tabheader(($i ? '����ҳ'.$i : '�ڵ���ҳ').'����'.viewcheck(array('name' =>'viewdetail','title' => '��ϸ','value' => $i > $addnum ? 0 : 1,'body' =>$actionid.'tbodyfilter'.$i)));
			echo "<tbody id=\"{$actionid}tbodyfilter$i\" style=\"display:".($i > $addnum ? 'none' : '')."\">";
			trbasic('ҳ��ģ��',"cfgsnew[$i][tpl]",makeoption(array('' => '������') + cls_mtpl::mtplsarr('marchive'),empty($cfgs[$i]['tpl']) ? '' : $cfgs[$i]['tpl']),'select',array('guide' => !$i ? cls_mtpl::mtplGuide('marchive') : ''));
			trbasic('��̬�����ʽ',"cfgsnew[$i][url]",empty($cfgs[$i]['url']) ? '' : $cfgs[$i]['url'],'text',array('guide'=>!$i ? '������ϵͳ�����ã�{$cndir}ϵͳĬ�ϱ���·����{$page}��ҳҳ�룬����֮�佨����Ϸָ���_��-���ӡ�': '','w'=>50));
			trbasic('�Ƿ����ɾ�̬','',makeradio("cfgsnew[$i][static]",array(0 => '��ϵͳ������',1 => '���ֶ�̬'),empty($cfgs[$i]['static']) ? 0 : $cfgs[$i]['static']),'');
			trbasic('��̬��������(����)',"cfgsnew[$i][period]",empty($cfgs[$i]['period']) ? '' : $cfgs[$i]['period'],'text',array('guide'=>'������ϵͳ������','w'=>4));
			trbasic('���⾲̬URL','',makeradio("cfgsnew[$i][novu]",array(0 => '��ϵͳ������',1 => '�ر����⾲̬'),empty($cfgs[$i]['novu']) ? 0 : $cfgs[$i]['novu']),'');
			echo "</tbody>";
			if($i != $mcn_max_addno) tabfooter();
		}
		tabfooter('bsubmit');
		a_guide('mcntpldetail');
	}else{
		$cntpl['addnum'] = max(0,intval($fmdata['addnum']));
		
		foreach($cfgsnew as $k => $v){
			if($k > $cntpl['addnum']){
				unset($cfgsnew[$k]);
				continue;
			}else{
				foreach(array('tpl','url','static','period','novu') as $var){
					if(empty($v[$var])){
						unset($cfgsnew[$k][$var]);
					}
				}
			}
		}
		$cntpl['cfgs'] = empty($cfgsnew) ? array() : $cfgsnew;
		$mcntpls[$tid] = $cntpl;
		cls_CacheFile::Save($mcntpls,'mcntpls','mcntpls');
		adminlog('��ԱƵ���ڵ�����');
		cls_message::show('��ԱƵ���ڵ������޸����',axaction(6,"?entry=$entry&action=cntplsedit"));
	}

}elseif($action == 'cntplsedit'){
	backnav('mcnode','cntpls');
	if(!submitcheck('bsubmit')){
		tabheader("�ڵ����ù���&nbsp; &nbsp; >><a href=\"?entry=$entry&action=cntpladd\" onclick=\"return floatwin('open_cntplsedit',this)\">���</a>",'cntplsedit',"?entry=$entry&action=$action",'10');
		trcategory(array('ID',array('����','txtL'),'����ҳ','����','ɾ��','����'));
		foreach($mcntpls as $k => $v){
			echo "<tr class=\"txt\">".
				"<td class=\"txtC w30\">$k</td>\n".
				"<td class=\"txtL\"><input type=\"text\" size=\"30\" maxlength=\"30\" name=\"cntplsnew[$k][cname]\" value=\"$v[cname]\"></td>\n".
				"<td class=\"txtC w40\">$v[addnum]</td>\n".
				"<td class=\"txtC w40\"><input type=\"text\" size=\"4\" maxlength=\"4\" name=\"cntplsnew[$k][vieworder]\" value=\"$v[vieworder]\"></td>\n".
				"<td class=\"txtC w30\"><a onclick=\"return deltip(this,$no_deepmode)\" href=\"?entry=$entry&action=cntpldel&tid=$k\">ɾ��</a></td>\n".
				"<td class=\"txtC w30\"><a href=\"?entry=$entry&action=cntpldetail&tid=$k\" onclick=\"return floatwin('open_cntplsedit',this)\">����</a></td>\n".
				"</tr>\n";
		}
		tabfooter('bsubmit','�޸�');
		a_guide('mcntplsedit');
	}else{
		if(isset($cntplsnew)){
			foreach($cntplsnew as $k => $v){
				$v['cname'] = trim(strip_tags($v['cname']));
				$v['cname'] = $v['cname'] ? $v['cname'] : $cntpls[$k]['cname'];
				$v['vieworder'] = max(0,intval($v['vieworder']));
				foreach(array('cname','vieworder',) as $var) $mcntpls[$k][$var] = $v[$var];
			}
			cls_Array::_array_multisort($mcntpls,'vieworder',1);
			cls_CacheFile::Save($mcntpls,'mcntpls','mcntpls');
			adminlog('�༭�ڵ�����');
		}
		cls_message::show('�ڵ������޸����',"?entry=$entry&action=$action");
	}
}elseif($action == 'cntpldel' && $tid){
	backnav('cnode','cntpls');
	if(!($cntpl = @$mcntpls[$tid])) cls_message::show('��ѡ��ڵ�����');
	deep_allow($no_deepmode);
	if(!submitcheck('confirm')){
		$message = "ɾ�����ָܻ���ȷ��ɾ����ѡ��Ŀ?<br><br>";
		$message .= "ȷ������>><a href=?entry=$entry&action=cntpldel&tid=$tid&confirm=ok>ɾ��</a><br>";
		$message .= "��������>><a href=?entry=$entry&action=cntplsedit>����</a>";
		cls_message::show($message);
	}
	
	$db->query("UPDATE {$tblprefix}mcnodes SET tid='0' WHERE tid='$tid'");
	unset($mcntpls[$tid]);
	
	cls_CacheFile::Save($mcntpls,'mcntpls','mcntpls');
	cls_CacheFile::Update('mcnodes');
	
	adminlog('ɾ���ڵ�����');
	cls_message::show('�ڵ�����ɾ���ɹ�', "?entry=$entry&action=cntplsedit");
}elseif($action == 'mcnodesedit'){
	backnav('mcnode','mcnodesedit');
	$page = !empty($page) ? max(1, intval($page)) : 1;
	submitcheck('bfilter') && $page = 1;
	$keyword = empty($keyword) ? '' : $keyword;
	$mcnvar = empty($mcnvar)? '' : $mcnvar;
	$tid = !isset($tid)? '-1' : max(-1, intval($tid));
	
	$wheresql = '';
	$fromsql = "FROM {$tblprefix}mcnodes";
	
	$mcnvar && $wheresql .= " AND mcnvar='$mcnvar'";
	$keyword && $wheresql .= " AND (ename ".sqlkw($keyword)." OR alias ".sqlkw($keyword).")";
	$tid != '-1' && $wheresql .= " AND tid='$tid'";
	$wheresql && $wheresql = 'WHERE '.substr($wheresql,5);
	
	$filterstr = '';
	foreach(array('mcnvar','keyword',) as $k) $filterstr .= "&$k=".urlencode($$k);
	foreach(array('tid',) as $k) $$k != -1 && $filterstr .= "&$k=".$$k;
	if(!submitcheck('bmcnodesedit')){
		echo form_str($actionid.'mcnodesedit',"?entry=$entry&action=$action&page=$page");
		tabheader_e();
		echo "<tr><td class=\"txt txtleft\">";
		echo "<input class=\"text\" name=\"keyword\" type=\"text\" value=\"$keyword\" size=\"8\" style=\"vertical-align: middle;\">&nbsp; ";
		echo "<select style=\"vertical-align: middle;\" name=\"mcnvar\">".makeoption(array('' => 'ȫ������') + $mcnvars,$mcnvar)."</select>&nbsp; ";
		$arr = array('-1' => '�ڵ�����','0' => '��δ����');foreach($mcntpls as $k => $v) $arr[$k] = $v['cname'];
		echo "<select name=\"tid\">".makeoption($arr,$tid)."</select>&nbsp; ";
		echo strbutton('bfilter','ɸѡ');
		echo "</td></tr>";
		tabfooter();
		
		tabheader("��Ա�ڵ��б�&nbsp; &nbsp; <input class=\"checkbox\" type=\"checkbox\" name=\"select_all\" value=\"1\">ȫѡ����ҳ����&nbsp;",'','',12);
		$cy_arr = array("<input class=\"checkbox\" type=\"checkbox\" name=\"chkall\" onclick=\"checkall(this.form, 'selectid', 'chkall')\">",array('�ڵ�����','txtL'),array('�ڵ��ʶ','txtL'),array('�ڵ�����(����ID)','txtL'),array('�ڵ�����','txtL'),'����ҳ',array('�鿴','txtL'),);
		$cy_arr[] = '����';
		trcategory($cy_arr);
		$pagetmp = $page;
		do{
			$query = $db->query("SELECT * $fromsql $wheresql ORDER BY cnid ASC LIMIT ".(($pagetmp - 1) * $atpp).",$atpp");
			$pagetmp--;
		} while(!$db->num_rows($query) && $pagetmp);
		while($cnode = $db->fetch_array($query)){
			$cnode = LoadMcnodeConfig($cnode);
			cls_url::view_mcnurl($cnode['ename'],$cnode);
			$aliasstr = $cnode['alias'];
			$ename0str = $cnode['ename'];
			$enamestr = $mcnvars[$cnode['mcnvar']]." ({$cnode['mcnid']})";
			$cnstplstr = empty($mcntpls[$cnode['tid']]['cname']) ? '-' : $mcntpls[$cnode['tid']]['cname'];
			$addnum = empty($cnode['addnum']) ? 0 : $cnode['addnum'];
			$lookstr = '';for($i = 0;$i <= @$cnode['addnum'];$i ++) $lookstr .= "<a href=\"".$cnode['mcnurl'.($i ? $i : '')]."\" target=\"_blank\">".($i ? '��'.$i : '��ҳ')."</a>&nbsp; ";
			echo "<tr class=\"txt\"><td class=\"txtC\"><input class=\"checkbox\" type=\"checkbox\" name=\"selectid[$cnode[cnid]]\" value=\"$cnode[cnid]\"></td>\n";
			echo "<td class=\"txtL\">$aliasstr</td>\n";
			echo "<td class=\"txtL\">$ename0str</td>\n";
			echo "<td class=\"txtL\">$enamestr</td>\n";
			echo "<td class=\"txtL\">$cnstplstr</td>\n";
			echo "<td class=\"txtC\">$addnum</td>\n";
			echo "<td class=\"txtL\">$lookstr</td>\n";
			echo "<td class=\"txtC\"><a href=\"?entry=$entry&action=mcnodedetail&cnid=$cnode[cnid]\" onclick=\"return floatwin('open_cnodedetail',this)\">�༭</a></td></tr>\n";
		}
		tabfooter();
		$counts = $db->result_one("SELECT count(*) $fromsql $wheresql");
		echo multi($counts,$atpp,$page,"?entry=$entry&action=$action$filterstr");

		tabheader('��������');
		$s_arr = array();
		$s_arr['delete'] = 'ɾ���ڵ�';
		if($s_arr){
			$soperatestr = '';
			$i = 1;
			foreach($s_arr as $k => $v){
				$soperatestr .= "<input class=\"checkbox\" type=\"checkbox\" id=\"cndeal[$k]\" name=\"cndeal[$k]\" value=\"1\"" . ($k == 'delete' ? ' onclick="deltip()"' : '') . "><label for=\"cndeal[$k]\">$v</label> &nbsp;";
				if(!($i % 5)) $soperatestr .= '<br>';
				$i ++;
			}
			trbasic('ѡ�������Ŀ','',$soperatestr,'');
		}
		$arr = array('0' => '������');
		foreach($mcntpls as $k => $v) $arr[$k] = $v['cname'];
		trbasic("<input class=\"checkbox\" type=\"checkbox\" name=\"cndeal[cntpl]\" value=\"1\">&nbsp;���ýڵ�����",'cncntpl',makeoption($arr,0),'select',array('guide'=>'�ڵ����ð����ڵ�������ģ�壬��̬�����ʽ�����á�'));
		$ptypearr = array();
		for($i = 0;$i <= $mcn_max_addno;$i ++) $ptypearr[$i] = $i ? '��'.$i : '��ҳ';
		trbasic("<input class=\"checkbox\" type=\"checkbox\" name=\"cndeal[static]\" value=\"1\">&nbsp;�������ɾ�̬",'',"<input class=\"checkbox\" type=\"checkbox\" name=\"mchkall\" onclick=\"checkall(this.form,'ptypes','mchkall')\">ȫѡ &nbsp;".makecheckbox('ptypes[]',$ptypearr),'',array('guide'=>'����̬����ʱ��Ч'));
		tabfooter('bmcnodesedit');
		a_guide('mcnodesedit');
	}else{
		if(empty($cndeal) && empty($dealstr)) cls_message::show('��ѡ�������Ŀ',"?entry=$entry&action=$action&page=$page$filterstr");
		if(empty($selectid) && empty($select_all)) cls_message::show('��ѡ��ڵ�',"?entry=$entry&action=$action&page=$page$filterstr");
		if(!empty($select_all)){
			if(empty($dealstr)){
				$dealstr = implode(',',array_keys(array_filter($cndeal)));
			}else{
				$cndeal = array();
				foreach(array_filter(explode(',',$dealstr)) as $k) $cndeal[$k] = 1;
			}
			if(!isset($ptypestr)){
				$ptypes = empty($ptypes) ? array() : $ptypes;
				$ptypestr = implode(',',$ptypes);
			}else $ptypes = explode(',',$ptypestr);
			
			$parastr = "";
			foreach(array('cncntpl','ptypestr',) as $k) $parastr .= "&$k=".$$k;
			
			$selectid = array();
			$npage = empty($npage) ? 1 : $npage;
			if(empty($pages)) $pages = @ceil($db->result_one("SELECT count(*) $fromsql $wheresql") / $atpp);
			if($npage <= $pages){
				$fromstr = empty($fromid) ? "" : "cnid>$fromid";
				$nwheresql = !$wheresql ? ($fromstr ? "WHERE $fromstr" : "") : ($wheresql.($fromstr ? " AND " : "").$fromstr);
				$query = $db->query("SELECT cnid,ename $fromsql $nwheresql ORDER BY cnid ASC LIMIT 0,$atpp");
				while($item = $db->fetch_array($query)) $selectid[] = $item['cnid'];
			}
			if(empty($selectid)) cls_message::show('��ѡ��ڵ�',"?entry=$entry&action=$action&page=$page$filterstr");
		}

		if(!empty($cndeal['delete'])){
			$query = $db->query("SELECT * $fromsql WHERE cnid ".multi_str($selectid));
			while($r = $db->fetch_array($query)){
				$r = LoadMcnodeConfig($r);
				for($i = 0;$i <= @$r['addnum'];$i ++) m_unlink(cls_url::m_parseurl(cls_node::mcn_format($r['ename'],$i),array('addno' => $i)));
			}
			$db->query("DELETE $fromsql WHERE cnid ".multi_str($selectid), 'UNBUFFERED');
		}else{
			if(!empty($cndeal['cntpl'])){
				$cncntpl = empty($cncntpl) ? 0 : (empty($mcntpls[$cncntpl]) ? 0 : $cncntpl);
				$db->query("UPDATE {$tblprefix}mcnodes SET tid='$cncntpl' WHERE cnid ".multi_str($selectid));
			}
			if(!empty($cndeal['static']) && $ptypes){
				$query = $db->query("SELECT * FROM {$tblprefix}mcnodes WHERE cnid ".multi_str($selectid));
				while($r = $db->fetch_array($query)){
					foreach($ptypes as $k){
						cls_McnodePage::Create(array('cnstr' => $r['ename'],'addno' => $k,'inStatic' => true));
					}
				}
			}
		}
		if(!empty($select_all)){
			$npage ++;
			if($npage <= $pages){
				$fromid = max($selectid);
				$transtr = '';
				$transtr .= "&select_all=1";
				$transtr .= "&pages=$pages";
				$transtr .= "&npage=$npage";
				$transtr .= "&bmcnodesedit=1";
				$transtr .= "&fromid=$fromid";
				cls_message::show('�ļ��������ڽ�����...<br>�� '.$pages.' ҳ�����ڴ���� '.$npage.' ҳ<br><br><a href=\"?entry=$entry&action=$action&page=$page$filterstr\">>>��ֹ��ǰ����</a>',"?entry=$entry&action=$action&page=$page$filterstr$transtr$parastr&dealstr=$dealstr");
			}
		}
		cls_CacheFile::Update('mcnodes');
		adminlog('�ڵ�������','�ڵ��б�������');
		cls_message::show('�ڵ�������',"?entry=$entry&action=$action&page=$page$filterstr");
	}
}elseif($action == 'mcnodeadd'){
	backnav('mcnode','mcnodeadd');
	$mcnvar = empty($mcnodenew['mcnvar']) ? '' : $mcnodenew['mcnvar'];
	if(!submitcheck('bmcnodeadd')){
		tabheader('��ӻ�Ա�ڵ�','mcnodeadd',"?entry=$entry&action=$action&mcnvar=$mcnvar",2,0,1);
		if(empty($mcnvar)){
			trbasic('�ڵ�����','mcnodenew[mcnvar]',makeoption($mcnvars),'select',array('guide' => '��ԱƵ���ڵ�Ϊ��ԱƵ��ҳ���б�ҳ�����壬�ɰ�ģ�弰�趨����ҳ�����<br>�ڵ�����ʾ����ѡ������Ϊ\'����\'����ʱ�����������Բ�ͬ����Ϊ�����Ļ�ԱƵ���ڵ㡣'));
			tabfooter('baddpre','����');
		}else{
			trbasic('�ڵ�����','',$mcnvars[$mcnvar],'');
			trhidden('mcnodenew[mcnvar]',$mcnvar);
			$arr = array(0 => '�ݲ�����');
			foreach($mcntpls as $k => $v) $arr[$k] = $v['cname'];
			trbasic('ѡ��ڵ�����','cntplnew',makeoption($arr,0),'select',array('guide'=>'�ڵ����þ����˽ڵ��ģ�弰����ҳ�����'));
			if($mcnvar == 'mcnid'){
				trbasic('�ڵ�����','mcnodenew[alias]','','text',array('validate'=>makesubmitstr('cntplnew[cname]',1,1,4,30)));
			}else{
				if($mcnvar == 'caid'){
					$arr = $catalogs;
					$tvar = 'title';
				}elseif(in_str('ccid',$mcnvar)){
					$arr = cls_cache::Read('coclasses',str_replace('ccid','',$mcnvar));
					$tvar = 'title';
				}elseif(in_str('ugid',$mcnvar)){
					$arr = cls_cache::Read('usergroups',str_replace('ugid','',$mcnvar));
					$tvar = 'cname';
				}
				$narr = array();
				foreach($arr as $k => $v) if(empty($mcnodes[$mcnvar.'='.$k])) $narr[$k] = $v[$tvar].(isset($v['level']) ? '('.$v['level'].')' : '');
				trbasic("ѡ���Ϊ�ڵ�<br><input class=\"checkbox\" type=\"checkbox\" name=\"chkallmcnids\" onclick=\"checkall(this.form,'mcnidsnew','chkallmcnids')\">ȫѡ",'',$narr ? makecheckbox('mcnidsnew[]',$narr,array(),5) : '�����ͽڵ���ȫ�����ɣ������˷��������ִ�б�����','');
			}
			tabfooter('bmcnodeadd','���');
		}
	}else{
		empty($mcnodenew) && $mcnodenew = array();
		$mcnodenew['ids'] = empty($mcnidsnew) ? array() : $mcnidsnew;
		$tid = $cntplnew;
		mcnodesfromcnc($mcnodenew,$tid);
		cls_CacheFile::Update('mcnodes');
		cls_message::show('��Ա�ڵ���ӳɹ���',axaction(6,"?entry=$entry&action=mcnodesedit"));
	}
}elseif($action == 'mcnodedetail' && $cnid){
	if(!$cnode = $db->fetch_one("SELECT * FROM {$tblprefix}mcnodes WHERE cnid='$cnid'")) cls_message::show('��ָ����ȷ�Ľڵ㣡');
	$cnode = LoadMcnodeConfig($cnode);
	foreach(array('tpls','urls','statics','periods',) as $var) ${$var.'arr'} = ${$var.'arr'} = empty($cnode[$var]) ? array() : explode(',',$cnode[$var]);
	if(!submitcheck('bmcnodedetail')){
		tabheader('�ڵ����','mcnodedetail',"?entry=$entry&action=$action&cnid=$cnid",2);
		trbasic('�ڵ�����','',$mcnvars[$cnode['mcnvar']],'');
		trbasic('�ڵ�����','mcnodenew[alias]',$cnode['alias']);
		trbasic('ָ���ڵ�����','mcnodenew[appurl]',$cnode['appurl'],'text',array('guide'=>'վ��Url��վ��Url����','w'=>50));
		$arr = array('0' => '������');foreach($mcntpls as $k => $v) $arr[$k] = $v['cname'];
		trbasic('���ýڵ�����','mcnodenew[tid]',makeoption($arr,$cnode['tid']),'select',array('guide'=>'�ڵ����ð����ڵ�������ģ�壬��̬�����ʽ�����á�'));
		tabfooter();
		
		tabheader('�ڵ�������� &nbsp;- &nbsp;'.(empty($mcntpls[$cnode['tid']]['cname']) ? '��δ����' : "<a href=\"?entry=$entry&action=cntpldetail&tid=$cnode[tid]\" onclick=\"return floatwin('open_cntplsedit',this)\">>>".$mcntpls[$cnode['tid']]['cname']."</a>"));
		trbasic('����ҳ����','',empty($cnode['addnum']) ? '0' : $cnode['addnum'],'');
		for($i = 0;$i <= @$cnode['addnum'];$i ++){
			$pvar = $i ? '����ҳ'.$i : '��ҳ';
			$arr = cls_mtpl::mtplsarr('marchive');
			trbasic($pvar.'ģ��','',empty($cnode['cfgs'][$i]['tpl']) ? 'δ����' : $cnode['cfgs'][$i]['tpl'].' &nbsp;- &nbsp;'.@$arr[$cnode['cfgs'][$i]['tpl']],'');
			trbasic($pvar.'��̬�����ʽ','',empty($cnode['cfgs'][$i]['url']) ? '��ϵͳ����' : $cnode['cfgs'][$i]['url'],'',array('guide'=>!$i ? '{$cndir}ϵͳĬ�ϱ���·����{$page}��ҳҳ�롣': ''));
			trbasic($pvar.'�Ƿ����ɾ�̬','',empty($cnode['cfgs'][$i]['static']) ? '��ϵͳ����' : '���ֶ�̬','');
			trbasic($pvar.'��̬��������','',empty($cnode['cfgs'][$i]['period']) ? '��ϵͳ����' : $cnode['cfgs'][$i]['period'].'(����)','');
			trbasic($pvar.'���⾲̬URL','',empty($cnode['cfgs'][$i]['novu']) ? '��ϵͳ����' : '�ر����⾲̬','');
		}
		tabfooter('bmcnodedetail');
	}else{
		if(!($mcnodenew['alias'] = trim(strip_tags($mcnodenew['alias'])))) $mcnodenew['alias'] = $mcnode['alias'];
		$mcnodenew['appurl'] = trim($mcnodenew['appurl']);
		$db->query("UPDATE {$tblprefix}mcnodes SET alias='$mcnodenew[alias]',appurl='$mcnodenew[appurl]',tid='$mcnodenew[tid]' WHERE cnid=$cnid");
		cls_CacheFile::Update('mcnodes');
		cls_message::show('��Ա�ڵ�༭��ɣ�',axaction(6,"?entry=$entry&action=mcnodesedit"));
	}
}
