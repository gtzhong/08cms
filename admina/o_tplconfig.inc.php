<?php
(!defined('M_COM') || !defined('M_ADMIN')) && exit('No Permission');
aheader();
if($re = $curuser->NoBackFunc('tpl')) cls_message::show($re);
foreach(array('mtpls','tpl_mconfigs',) as $k) $$k = cls_cache::Read($k);
if(empty($action)) $action = 'system';
if($action == 'system'){
	//�ֻ�����ҳ������ͨ��������
	backnav('mobile','system');
	$o_sptpls = cls_cache::Read('o_sptpls');
	if(!submitcheck('bsubmit')){
		tabheader('ϵͳ����','tplbase',"?entry=$entry&action=$action");
		trbasic('�����ֻ���','mconfigsnew[enable_mobile]',empty($mconfigs['enable_mobile']) ? 0 : 1,'radio');
		trbasic('�ֻ���·��','mconfigsnew[mobiledir]',empty($mconfigs['mobiledir']) ? '' : $mconfigs['mobiledir'],'text',array('guide'=>'�ֻ���·������Ҫ��/��{$mobiledir}����·����{$mobileurl}����url��'));
		tabfooter();

		tabheader('ϵͳģ��');
		$index_items = array (
		  'index' => 
		  array (
			'cname' => '��ҳģ��',
			'tpclass' => 'index',
			'tctitle' => '��ҳ',
		  ),
		);
		foreach($index_items as $k => $v){
			trbasic($v['cname'],"fmdata[$k]",makeoption(array('' => '������') + cls_mtpl::o_mtplsarr($v['tpclass']),empty($o_sptpls[$k]) ? '' : $o_sptpls[$k]),'select',array('guide' => cls_mtpl::mtplGuide('index',0,1)));
		}
		//$sp_regs = array(); //��ģ�ͷֿ���ģ��?
		$sp_items = array (
		  'register' => 
		  array (
			'cname' => 'ע��ģ��',
			'link' => '{$cms_abs}/{$mobiledir}/register.php',
		  ),
		  'login' => 
		  array (
			'cname' => '��¼ģ��',
			'link' => '{$cms_abs}/{$mobiledir}/login.php',
		  ),
		  'message' => 
		  array (
			'cname' => '��ʾ��Ϣģ��',
			'link' => '��ʾ��Ϣ(ϵͳ����)',
		  ),
		);
		foreach($sp_items as $k => $v){
			trbasic($v['cname'],"fmdata[$k]",makeoption(array('' => '������') + cls_mtpl::o_mtplsarr('special'),empty($o_sptpls[$k]) ? '' : $o_sptpls[$k]),'select',array('guide' => cls_mtpl::mtplGuide('special',0,1)."���������ӣ�$v[link]��"));
		}
		tabfooter('bsubmit');
		a_guide('mobile_base');
	}else{
		foreach(array('mobiledir',) as $var){
			$mconfigsnew[$var] = strtolower($mconfigsnew[$var]);
			if($mconfigsnew[$var] == $mconfigs[$var]) continue;
			if(!$mconfigsnew[$var] || preg_match("/[^a-z_0-9]+/",$mconfigsnew[$var])){
				$mconfigsnew[$var] = $mconfigs[$var];
				continue;
			}
			if($mconfigs[$var] && is_dir(M_ROOT.$mconfigs[$var])){
				if(!rename(M_ROOT.$mconfigs[$var],M_ROOT.$mconfigsnew[$var])) $mconfigsnew[$var] = $mconfigs[$var];
			}else mmkdir(M_ROOT.$mconfigsnew[$var],0);
		}
		saveconfig('visit');
		
		cls_CacheFile::Save($fmdata,'o_sptpls','o_sptpls');
		adminlog('�ֻ���ģ���','ϵͳģ���');
		cls_message::show('ϵͳģ������',M_REFERER);
	}
}elseif($action == 'tplchannel'){
	foreach(array('channels','o_arc_tpl_cfgs','o_arc_tpls',) as $k) $$k = cls_cache::Read($k);
	if(empty($chid)){
		backnav('mobile','archive');
		tabheader("���ĵ�ģ��
		 &nbsp; &nbsp;>><a href=\"?entry=$entry&action=tplcatalog\">���ĵ���Ŀ</a>
		 &nbsp; &nbsp;>><a href=\"?entry=$entry&action=arc_tplsedit\" onclick=\"return floatwin('open_channeledit',this)\">�ĵ�ģ�巽��</a>
		 &nbsp; &nbsp;".cls_mtpl::mtplGuide('archive',true));
		trcategory(array('ID',array('ģ������','txtL'),'����','����ҳ','����ҳ',array('�ĵ�ģ�巽��/����','txtL')));
		foreach($channels as $k => $v){
			$tid = empty($o_arc_tpl_cfgs[$k]) ? 0 : $o_arc_tpl_cfgs[$k];
			if(empty($o_arc_tpls[$tid])) $tid = 0;
			$namestr = $tid ? $tid.'-'.$o_arc_tpls[$tid]['cname']." &nbsp; &nbsp;>><a href=\"?entry=$entry&action=arc_tpldetail&tid=$tid\" onclick=\"return floatwin('open_cntplsedit',this)\">����</a>" : '-';
			$addnum = !$tid || empty($o_arc_tpls[$tid]['addnum']) ? 0 : $o_arc_tpls[$tid]['addnum'];
			$searchs = !$tid || empty($o_arc_tpls[$tid]['search']) ? 0 : count($o_arc_tpls[$tid]['search']);
			echo "<tr class=\"txt\">".
				"<td class=\"txtC w40\">$k</td>\n".
				"<td class=\"txtL\">$v[cname]</td>\n".
				"<td class=\"txtC w40\"><a href=\"?entry=$entry&action=$action&chid=$k\" onclick=\"return floatwin('open_channeledit',this)\">����</a></td>\n".
				"<td class=\"txtC w40\">$addnum</td>\n".
				"<td class=\"txtC w40\">$searchs</td>\n".
				"<td class=\"txtL\">$namestr</td>\n".
				"</tr>\n";
		}
		tabfooter();
	}else{
		echo "<title>��ģ�Ͱ��ĵ�ģ��</title>";
		if(!($channel = $channels[$chid])) cls_message::show('��ָ����ȷ���ĵ�ģ�ͣ�');
		$tid = empty($o_arc_tpl_cfgs[$chid]) ? 0 : $o_arc_tpl_cfgs[$chid];
		if(!submitcheck('bsubmit')){
			tabheader("[$channel[cname]]ģ������",'channel',"?entry=$entry&action=$action&chid=$chid");
			$na = array(0 => '������',);foreach($o_arc_tpls as $k => $v) $na[$k] = $v['cname']."($k)";
			trbasic('�ĵ�ģ�巽��','fmdata[tid]',makeoption($na,$tid),'select',array('guide' => '�ĵ�����ʹ��������Ŀ�󶨵�ģ�巽������Ŀδ��ģ��Ļ�����Ĭ��ʹ������ģ�Ͱ󶨵�ģ�巽��<br>ģ�巽��ָ��������ҳ�������б����õ�ģ��,��������ģ������->�ĵ�ģ��->�ĵ�ģ�巽��'));
			tabfooter('bsubmit');
			a_guide('tplchannel');
		}else{
			$o_arc_tpl_cfgs[$chid] = empty($fmdata['tid']) ? 0 : intval($fmdata['tid']);
			foreach($o_arc_tpl_cfgs as $k => $v) if(empty($channels[$k])) unset($o_arc_tpl_cfgs[$k]);
			cls_CacheFile::Save($o_arc_tpl_cfgs,'o_arc_tpl_cfgs','o_arc_tpl_cfgs');
			adminlog('��ϸ�޸��ĵ�ģ��');
			cls_message::show('ģ���޸����',axaction(6,"?entry=$entry&action=$action"));
		}
	}
}elseif($action == 'tplcatalog'){
	foreach(array('catalogs','ca_tpl_cfgs','o_arc_tpls',) as $k) $$k = cls_cache::Read($k);
	if(empty($caid)){
		backnav('mobile','archive');
		tabheader(">><a href=\"?entry=$entry&action=tplchannel\">���ĵ�ģ��</a>
		 &nbsp; &nbsp;���ĵ���Ŀ
		 &nbsp; &nbsp;>> <a href=\"?entry=$entry&action=arc_tplsedit\" onclick=\"return floatwin('open_channeledit',this)\">�ĵ�ģ�巽��</a>
		 &nbsp; &nbsp;".cls_mtpl::mtplGuide('archive',true));
		trcategory(array('ID',array('��Ŀ����','txtL'),'ģ��/��̬��ʽ','����ҳ','����ҳ',array('�ĵ�ģ�巽��/����','txtL')));
		foreach($catalogs as $k => $v){
			$tid = empty($ca_tpl_cfgs[$k]) ? 0 : $ca_tpl_cfgs[$k];
			if(empty($o_arc_tpls[$tid])) $tid = 0;
			$namestr = $tid ? $tid.'-'.$o_arc_tpls[$tid]['cname']." &nbsp; &nbsp;>><a href=\"?entry=$entry&action=arc_tpldetail&tid=$tid\" onclick=\"return floatwin('open_cntplsedit',this)\">����</a>" : '-';
			$addnum = !$tid || empty($o_arc_tpls[$tid]['addnum']) ? 0 : $o_arc_tpls[$tid]['addnum'];
			$searchs = !$tid || empty($o_arc_tpls[$tid]['search']) ? 0 : count($o_arc_tpls[$tid]['search']);
			$titlestr = empty($v['level']) ? "<b>$v[title]</b>" : str_repeat('&nbsp; &nbsp; &nbsp; ',$v['level']).$v['title'];
			echo "<tr class=\"txt\">".
				"<td class=\"txtC w40\">$k</td>\n".
				"<td class=\"txtL\">$titlestr</td>\n".
				"<td class=\"txtC\"><a href=\"?entry=$entry&action=$action&caid=$k\" onclick=\"return floatwin('open_channeledit',this)\">����</a></td>\n".
				"<td class=\"txtC w40\">$addnum</td>\n".
				"<td class=\"txtC w40\">$searchs</td>\n".
				"<td class=\"txtL\">$namestr</td>\n".
				"</tr>\n";
		}
		tabfooter();
	}else{
		echo "<title>����Ŀ���ĵ�ģ��</title>";
		if(!($catalog = $catalogs[$caid])) cls_message::show('��ָ����ȷ����Ŀ��');
		$tid = empty($ca_tpl_cfgs[$caid]) ? 0 : $ca_tpl_cfgs[$caid];
		if(!submitcheck('bsubmit')){
			tabheader("[$catalog[title]]ģ������",'channel',"?entry=$entry&action=$action&caid=$caid");
			$na = array(0 => '������',);foreach($o_arc_tpls as $k => $v) $na[$k] = $v['cname']."($k)";
			trbasic('�ĵ�ģ�巽��','fmdata[tid]',makeoption($na,$tid),'select',array('guide' => '�ĵ�����ʹ��������Ŀ�󶨵�ģ�巽������Ŀδ��ģ��Ļ�����Ĭ��ʹ������ģ�Ͱ󶨵�ģ�巽��<br>ģ�巽��ָ��������ҳ�������б����õ�ģ��,��������ģ������->�ĵ�ģ��->�ĵ�ģ�巽��'));
			trbasic('�ĵ�ҳ��̬�����ʽ','fmdata[customurl]',$catalog['customurl'],'text',array('guide'=>'����ΪĬ�ϸ�ʽ��{$topdir}������ĿĿ¼��{$cadir}������ĿĿ¼��{$y}�� {$m}�� {$d}�� {$h}ʱ {$i}�� {$s}�� {$chid}ģ��id  {$aid}�ĵ�id {$page}��ҳҳ�� {$addno}����ҳid��id֮�佨���÷ָ���_��-���ӡ�','w'=>50));
			tabfooter('bsubmit');
			a_guide('tplcatalog');
		}else{
			$ca_tpl_cfgs[$caid] = empty($fmdata['tid']) ? 0 : intval($fmdata['tid']);
			foreach($ca_tpl_cfgs as $k => $v) if(empty($catalogs[$k])) unset($ca_tpl_cfgs[$k]);
			cls_CacheFile::Save($ca_tpl_cfgs,'ca_tpl_cfgs','ca_tpl_cfgs');
			
			$fmdata['customurl'] = preg_replace("/^\/+/",'',trim($fmdata['customurl']));
			$db->query("UPDATE {$tblprefix}catalogs SET
				customurl='$fmdata[customurl]'
				WHERE caid='$caid'");
			cls_CacheFile::Update('catalogs');
			
			adminlog('����Ŀ���ĵ�����ҳģ��');
			cls_message::show('�ĵ�����ҳģ������',axaction(6,"?entry=$entry&action=$action"));
		}
	}
}elseif($action == 'tplfcatalog'){
	backnav('mobile','farchive');
	foreach(array('fcatalogs','o_tplcfgs',) as $k) $$k = cls_cache::Read($k);
	if(!submitcheck('bfcatalog')){
		tabheader("��������ģ���&nbsp; &nbsp; ".cls_mtpl::mtplGuide('freeinfo',true),'fcatalog',"?entry=$entry&action=tplfcatalog");
		foreach($fcatalogs as $k => $v){
			if(!empty($v['ftype'])) continue;
			$tplcfg = empty($o_tplcfgs['farchive'][$k]) ? array() : $o_tplcfgs['farchive'][$k];
			trbasic($k.'.'.$v['title'],"tplcfgnew[$k][arctpl]",makeoption(array('' => '������') + cls_mtpl::o_mtplsarr('freeinfo'),@$tplcfg['arctpl']),'select');
		}
		tabfooter('bfcatalog');
		a_guide('tplfcatalog');
	}else{
		$tplcfg = array();
		$vars = array('arctpl',);
		foreach($fcatalogs as $k => $v){
			if(!empty($v['ftype'])) continue;
			foreach($vars as $var){
				empty($tplcfgnew[$k][$var]) ||$tplcfg[$k][$var] = $tplcfgnew[$k][$var];
			}
		}
		$o_tplcfgs['farchive'] = $tplcfg;
		cls_CacheFile::Save($o_tplcfgs,'o_tplcfgs','o_tplcfgs');
		adminlog('�󶨸�������ҳģ��');
		cls_message::show('����ģ������',M_REFERER);
	}
}elseif($action == 'arc_tpladd'){
	$o_arc_tpls = cls_cache::Read('o_arc_tpls');
	echo "<title>����ĵ�ģ�巽��</title>";
	if(!submitcheck('bsubmit')){
		$submitstr = '';
		tabheader('����ĵ�ģ�巽��','arc_tpladd',"?entry=$entry&action=$action",2,0,1);
		trbasic('��������','fmdata[cname]','','text',array('validate'=>makesubmitstr('fmdata[cname]',1,1,4,30)));
		tabfooter('bsubmit','���');
		a_guide('o_arc_tpladd');//?????????????
	} else {
		if(!($fmdata['cname'] = trim(strip_tags($fmdata['cname'])))) cls_message::show('��������⣡',M_REFERER);
		$tid = auto_insert_id('o_arc_tpls');
		$o_arc_tpls[$tid] = array('cname' => $fmdata['cname'],'addnum' =>0,'chid' => 0,'vieworder' => 0,'cfg' => array(),);
		cls_CacheFile::Save($o_arc_tpls,'o_arc_tpls','o_arc_tpls');
		adminlog('����ĵ�ģ�巽��');
		cls_message::show('�ĵ�ģ�巽�������ɣ��������һ������',"?entry=$entry&action=arc_tpldetail&tid=$tid");
	}
}elseif($action == 'arc_tpldetail' && $tid){
	foreach(array('o_arc_tpls','channels',) as $k) $$k = cls_cache::Read($k);
	if(!($arc_tpl = @$o_arc_tpls[$tid])) cls_message::show('��ѡ���ĵ�ģ�巽��');
	echo "<title>�ĵ�ģ�巽�� - $arc_tpl[cname]</title>";
	if(!submitcheck('bsubmit')){
		tabheader("�ĵ�ģ������&nbsp;&nbsp;[$arc_tpl[cname]]",'cntpldetail',"?entry=$entry&action=$action&tid=$tid");
		$arr = array();for($i = 0;$i <= $max_addno;$i ++) $arr[$i] = $i;
		$addnum = empty($arc_tpl['addnum']) ? 0 : $arc_tpl['addnum'];
		trbasic('����ҳ����','',makeradio('fmdata[addnum]',$arr,$addnum),'');
		trbasic('�ĵ������б�ģ��','fmdata[search][0]',makeoption(array('' => '������') + cls_mtpl::o_mtplsarr('cindex'),@$arc_tpl['search'][0]),'select',array('guide' => cls_mtpl::mtplGuide('cindex')));
		trbasic('��������ҳ1ģ��','fmdata[search][1]',makeoption(array('' => '������') + cls_mtpl::o_mtplsarr('cindex'),@$arc_tpl['search'][1]),'select',array('guide' => cls_mtpl::mtplGuide('cindex')));
		tabfooter();
		for($i = 0;$i <= $max_addno;$i ++){
			tabheader(($i ? '����ҳ'.$i : '����ҳ').'����'.viewcheck(array('name' =>'viewdetail','title' => '��ϸ','value' => $i > $addnum ? 0 : 1,'body' =>$actionid.'tbodyfilter'.$i)));
			echo "<tbody id=\"{$actionid}tbodyfilter$i\" style=\"display:".($i > $addnum ? 'none' : '')."\">";
			trbasic('ҳ��ģ��',"fmdata[cfg][$i][tpl]",makeoption(array('' => '������') + cls_mtpl::o_mtplsarr('archive'),empty($arc_tpl['cfg'][$i]['tpl']) ? '' : $arc_tpl['cfg'][$i]['tpl']),'select',array('guide' => cls_mtpl::mtplGuide('archive')));
			trbasic('���⾲̬URL','',makeradio("fmdata[cfg][$i][novu]",array(0 => '��ϵͳ������',1 => '�ر����⾲̬'),empty($arc_tpl['cfg'][$i]['novu']) ? 0 : $arc_tpl['cfg'][$i]['novu']),'');
			echo "</tbody>";
			if($i != $max_addno) tabfooter();
		}
		tabfooter('bsubmit');
		a_guide('arc_tpldetail');
	}else{
		$arc_tpl['addnum'] = max(0,intval($fmdata['addnum']));
		foreach(array(0,1) as $i){
			if(empty($fmdata['search'][$i])){
				unset($arc_tpl['search'][$i]);
			}else{
				$arc_tpl['search'][$i] = $fmdata['search'][$i];
			}
		}
		for($i = 0;$i <= $max_addno;$i ++){
			foreach(array('tpl','novu',) as $var){
				if(in_array($var,array('tpl',))){
					$fmdata['cfg'][$i][$var] = trim(strip_tags($fmdata['cfg'][$i][$var]));
				}elseif(in_array($var,array('novu',))){
					$fmdata['cfg'][$i][$var] = empty($fmdata['cfg'][$i][$var]) ? 0 : 1;
				}
				
				if(empty($fmdata['cfg'][$i][$var])){
					unset($arc_tpl['cfg'][$i][$var]);
				}else{
					$arc_tpl['cfg'][$i][$var] = $fmdata['cfg'][$i][$var];
				}
			}
			if(empty($arc_tpl['cfg'][$i])) unset($arc_tpl['cfg'][$i]);	
		}
		$o_arc_tpls[$tid] = $arc_tpl;
		cls_CacheFile::Save($o_arc_tpls,'o_arc_tpls','o_arc_tpls');
		adminlog('�ĵ�ģ�巽���༭');
		cls_message::show('�ĵ�ģ�巽���޸����',axaction(6,"?entry=$entry&action=arc_tplsedit"));
	}

}elseif($action == 'arc_tplsedit'){
	foreach(array('o_arc_tpls','channels',) as $k) $$k = cls_cache::Read($k);
	echo "<title>�ĵ�ģ�巽������</title>";
	if(!submitcheck('bsubmit')){
		tabheader("�ĵ�ģ�巽������&nbsp; &nbsp; >><a href=\"?entry=$entry&action=arc_tpladd\" onclick=\"return floatwin('open_cntplsedit',this)\">���</a>",'cntplsedit',"?entry=$entry&action=$action",'10');
		trcategory(array('ID',array('��������','txtL'),'����','����ҳ','����ҳ','ɾ��','����'));
		foreach($o_arc_tpls as $k => $v){
			echo "<tr class=\"txt\">".
				"<td class=\"txtC w40\">$k</td>\n".
				"<td class=\"txtL\"><input type=\"text\" size=\"30\" maxlength=\"30\" name=\"fmdata[$k][cname]\" value=\"$v[cname]\"></td>\n".
				"<td class=\"txtC w40\"><input type=\"text\" size=\"4\" maxlength=\"4\" name=\"fmdata[$k][vieworder]\" value=\"$v[vieworder]\"></td>\n".
				"<td class=\"txtC w40\">".@$v['addnum']."</td>\n".
				"<td class=\"txtC w40\">".(empty($v['search']) ? 0 : count($v['search']))."</td>\n".
				"<td class=\"txtC w40\"><a onclick=\"return deltip(this,$no_deepmode)\" href=\"?entry=$entry&action=arc_tpldel&tid=$k\">ɾ��</a></td>\n".
				"<td class=\"txtC w40\"><a href=\"?entry=$entry&action=arc_tpldetail&tid=$k\" onclick=\"return floatwin('open_cntplsedit',this)\">����</a></td>\n".
				"</tr>\n";
		}
		tabfooter('bsubmit','�޸�');
		a_guide('arc_tplsedit');
	}else{
		if(isset($fmdata)){
			foreach($fmdata as $k => $v){
				$v['cname'] = trim(strip_tags($v['cname']));
				$v['cname'] = $v['cname'] ? $v['cname'] : $o_arc_tpls[$k]['cname'];
				$v['vieworder'] = max(0,intval($v['vieworder']));
				foreach(array('cname','vieworder',) as $var) $o_arc_tpls[$k][$var] = $v[$var];
			}
			adminlog('�༭�ĵ�ģ�巽��');
			cls_Array::_array_multisort($o_arc_tpls,'vieworder',1);
			cls_CacheFile::Save($o_arc_tpls,'o_arc_tpls','o_arc_tpls');
		}
		cls_message::show('�ĵ�ģ�巽���޸����',"?entry=$entry&action=$action");
	}
}elseif($action == 'arc_tpldel' && $tid){
	deep_allow($no_deepmode,"?entry=$entry&action=arc_tplsedit");
	$o_arc_tpls = cls_cache::Read('o_arc_tpls');
	if(!($arc_tpl = @$o_arc_tpls[$tid])) cls_message::show('��ѡ���ĵ�ģ�巽��');
	if(!submitcheck('confirm')){
		$message = "ɾ�����ָܻ���ȷ��ɾ����ѡ��Ŀ?<br><br>";
		$message .= "ȷ������>><a href=?entry=$entry&action=$action&tid=$tid&confirm=ok>ɾ��</a><br>";
		$message .= "��������>><a href=?entry=$entry&action=arc_tplsedit>����</a>";
		cls_message::show($message);
	}
	unset($o_arc_tpls[$tid]);
	cls_CacheFile::Save($o_arc_tpls,'o_arc_tpls','o_arc_tpls');
	cls_message::show('�ĵ�ģ�巽��ɾ���ɹ�', "?entry=$entry&action=arc_tplsedit");
}