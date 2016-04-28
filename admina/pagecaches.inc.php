<?php
(!defined('M_COM') || !defined('M_ADMIN')) && exit('No Permission');
aheader();
if($re = $curuser->NoBackFunc('webparam')) cls_message::show($re);
$channels = cls_cache::Read('channels');
if(empty($action)) $action = 'pagecachesedit';
$pctypes = array(
	1 => '��Ŀ�ڵ�|index.php',
	2 => '�ĵ�ҳ|archive.php',
	3 => '����ҳ|info.php',
	4 => '����ҳ|search.php',
	5 => '��Ա�ڵ�|member/index.php',
	6 => '��Ա����|member/search.php',
	7 => '�ռ���Ŀ|mspace/index.php',
	8 => '�ռ��ĵ�|mspace/archive.php',
	9 => 'js����|tools/js.php',
	);
if($action == 'pagecachesedit'){
	backnav('project','pagecache');
	if(!submitcheck('bsubmit')){
		tabheader("ҳ�滺�淽������&nbsp; &nbsp; >><a href=\"?entry=$entry&action=pagecacheadd\" onclick=\"return floatwin('open_pagecaches',this)\">��ӷ���</a>&nbsp; &nbsp; >><a href=\"?entry=rebuilds&action=pagecache\">������</a>",$actionid.'arcsedit',"?entry=$entry&action=$action");
		trcategory(array('ID',"<input class=\"checkbox\" type=\"checkbox\" name=\"chkallc\" onclick=\"checkall(this.form,'fmdata','chkallc')\">����",'��������|L','ҳ������|L','����ҳ','����(s)','����',"<input class=\"checkbox\" type=\"checkbox\" name=\"chkall\" onclick=\"deltip(this,$no_deepmode,checkall,this.form, 'delete', 'chkall')\">ɾ?",'�༭'));
		$query = $db->query("SELECT * FROM {$tblprefix}pagecaches ORDER BY vieworder,pcid");
		while($item = $db->fetch_array($query)){
			$id = $item['pcid'];
			echo "<tr class=\"txt\">".
			"<td class=\"txtC w40\">$id</td>\n".
			"<td class=\"txtC w50\"><input type=\"checkbox\" class=\"checkbox\" ".(empty($item['available']) ? "" : " checked")." value=\"1\" name=\"fmdata[$id][available]\"></td>\n".
			"<td class=\"txtL\"><input type=\"text\" size=\"40\" name=\"fmdata[$id][cname]\" value=\"$item[cname]\"></td>\n".
			"<td class=\"txtL\">".$item['typeid'].'_'.@$pctypes[$item['typeid']]."</td>\n".
			"<td class=\"txtC\">".($item['demourl'] ? "<a href=\"".cls_url::view_url($item['demourl'])."\" target=\"_blank\">�鿴</a>" : '-')."</td>\n".
			"<td class=\"txtC\"><input type=\"text\" size=\"5\" name=\"fmdata[$id][period]\" value=\"$item[period]\"></td>\n".
			"<td class=\"txtC w40\"><input type=\"text\" size=\"4\" name=\"fmdata[$id][vieworder]\" value=\"$item[vieworder]\"></td>\n".
			"<td class=\"txtC w40\"><input class=\"checkbox\" type=\"checkbox\" name=\"delete[$id]\" value=\"$id\" onclick=\"deltip(this,$no_deepmode)\"></td>\n".
			"<td class=\"txtC w40\"><a onclick=\"return floatwin('open_pagecaches',this)\" href=\"?entry=$entry&action=pagecachedetail&pcid=$id\">����</a></td></tr>\n";
			"</tr>\n";
		}
		tabfooter('bsubmit');
		a_guide('pagecachesedit');
	}else{
		if(!empty($delete) && deep_allow($no_deepmode)){
			foreach($delete as $k){
				$db->query("DELETE FROM {$tblprefix}pagecaches WHERE pcid='$k'");
				unset($fmdata[$k]);
			}
		}
		if(!empty($fmdata)){
			foreach($fmdata as $k => $v){
				$v['cname'] = trim(strip_tags($v['cname']));
				$v['available'] = empty($v['available']) ? 0 : 1;
				$v['period'] = max(0,intval($v['period']));
				$v['vieworder'] = max(0,intval($v['vieworder']));
				if(!$v['cname']) continue;
				$db->query("UPDATE {$tblprefix}pagecaches SET cname='$v[cname]',available='$v[available]',period='$v[period]',vieworder='$v[vieworder]' WHERE pcid='$k'");
			}
		}
		adminlog('�༭ҳ�滺�淽���б�');
		cls_CacheFile::Update('pagecaches'); 
		cls_message::show('���淽���༭��ɣ�', "?entry=$entry&action=$action");
	}

}elseif($action == 'pagecacheadd'){
	if(!submitcheck('bsubmit')){
		tabheader('���ҳ�滺�淽��','pagecacheadd',"?entry=$entry&action=$action",2,0,1);
		trbasic('��������','fmdata[cname]','','text',array('validate'=>makesubmitstr('fmdata[cname]',1,0,3,50),'w' => 50,));
		trbasic('ҳ������','fmdata[typeid]',makeoption($pctypes),'select',array('guide' => '|����Ϊҳ�����url��ڽű������л�ԱƵ�����ռ�ҳ���Ŀ¼�����ϵͳ���ñ仯��'));
		tabfooter('bsubmit');
		a_guide('pagecachesedit');
	}else{
		$fmdata['cname'] = trim(strip_tags($fmdata['cname']));
		if(!$fmdata['cname'] || !$fmdata['typeid']) cls_message::show('���ϲ���ȫ',M_REFERER);
		$db->query("INSERT INTO {$tblprefix}pagecaches SET 
				   	pcid = ".auto_insert_id('pagecaches').",
					cname='$fmdata[cname]', 
					typeid='$fmdata[typeid]'
					");
		if($pcid = $db->insert_id()){
			adminlog('���ҳ�滺�淽��');
			cls_CacheFile::Update('pagecaches');
			cls_message::show('���淽����ӳɹ�����Է���������ϸ���á�',"?entry=$entry&action=pagecachedetail&pcid=$pcid");
		}else cls_message::show('���淽����Ӳ��ɹ���', axaction(6,"?entry=$entry&action=pagecachesedit"));
	}
}elseif($action == 'pagecachedetail' && $pcid){
	!($pagecache = fetch_one($pcid)) && cls_message::show('ָ���ķ��������ڡ�');
	if(!submitcheck('bsubmit')){
		tabheader('ҳ�滺�淽������','pagecacheadd',"?entry=$entry&action=$action&pcid=$pcid",2,0,1);
		trbasic('��������','fmdata[cname]',$pagecache['cname'],'text',array('validate'=>makesubmitstr('fmdata[cname]',1,0,3,50),'w' => 50,));
		trbasic('ҳ������','',$pagecache['typeid'].'_'.@$pctypes[$pagecache['typeid']],'',array('guide' => '|����Ϊҳ�����url��ڽű������л�ԱƵ�����ռ�ҳ���Ŀ¼�����ϵͳ���ñ仯��'));
		trbasic('ʾ��ҳ��url','fmdata[demourl]',$pagecache['demourl'],'text',array('validate'=>makesubmitstr('fmdata[cname]',0,0,3,255),'w' => 50,));
		trbasic('��������',"fmdata[period]",$pagecache['period'],'text',array('guide'=>'��λ���룬����Ϊ�����档','validate'=>makesubmitstr("fmdata[period]",1,'int',1,5)));
		trbasic('������ʼҳ��',"fmdata[pagefrom]",$pagecache['pagefrom'],'text',array('guide'=>'������1-99֮���������','validate'=>makesubmitstr("fmdata[pagefrom]",0,'int',0,2)));
		trbasic('�������ҳ��',"fmdata[pageto]",$pagecache['pageto'],'text',array('guide'=>'������1-99֮���������','validate'=>makesubmitstr("fmdata[pageto]",0,'int',0,2)));
		$cfgs = &$pagecache['cfgs'];
		if(in_array($pagecache['typeid'],array(2,8))){
			trbasic('��������ģ�͵��ĵ�<br /><input class="checkbox" type="checkbox" name="chchkall" onclick="checkall(this.form,\'cfgsnew[chids]\',\'chchkall\')">ȫѡ','',makecheckbox('cfgsnew[chids][]',cls_channel::chidsarr(1),empty($cfgs['chids']) ? array() : explode(',',$cfgs['chids']),5),'');
			trbasic('����������ڷ�����',"cfgsnew[indays]",empty($cfgs['indays']) ? '' : $cfgs['indays'],'text',array('guide'=>'������1-999֮�������������Ϊ���ޡ�','validate'=>makesubmitstr("cfgsnew[indays]",0,'int',0,3)));
		}	
		trbasic("url�к������ִ��򻺴�<br><input class=\"checkbox\" type=\"checkbox\" name=\"cfgsnew[instrall]\" value=\"1\"".(empty($cfgs['instrall']) ? "" : " checked").">����ִ�ͬʱ����",'cfgsnew[instr]',@$cfgs['instr'],'text',array('validate'=>makesubmitstr('cfgsnew[instr]',0,0,3,100),'w' => 50,'guide' => '�ִ�ͨ��Ϊ abc=123 ��ʽ�е�һ���֣�����ִ���Ӣ�Ķ��ŷָ������������Ƶ�ǰ��'));
		trbasic("url�к������ִ�������<br><input class=\"checkbox\" type=\"checkbox\" name=\"cfgsnew[nostrall]\" value=\"1\"".(empty($cfgs['nostrall']) ? "" : " checked").">����ִ�ͬʱ����",'cfgsnew[nostr]',@$cfgs['nostr'],'text',array('validate'=>makesubmitstr('cfgsnew[nostr]',0,0,1,100),'w' => 50,'guide' => '����ִ���Ӣ�Ķ��ŷָ�������*��ʾ�������и��Ӳ�����ҳ�棬�������ų��κθ�ʽ'));
		if(in_array($pagecache['typeid'],array(1,2,4))){
			trbasic('�ر�ͬҳ���ֻ��滺��',"cfgsnew[nomobile]",@$cfgs['nomobile'],'radio');
		}	
		tabfooter('bsubmit');
	}else{
		$fmdata['cname'] = trim(strip_tags($fmdata['cname']));
		if(!$fmdata['cname']) cls_message::show('���ϲ���ȫ',M_REFERER);
		$fmdata['demourl'] = preg_replace(u_regcode($cms_abs),'',trim($fmdata['demourl']));
		$fmdata['period'] = max(0,intval($fmdata['period']));
		$fmdata['pagefrom'] = min(99,max(1,intval($fmdata['pagefrom'])));
		$fmdata['pageto'] = min(99,max(1,intval($fmdata['pageto'])));
		if(!empty($cfgsnew['chids'])) $cfgsnew['chids'] = implode(',',$cfgsnew['chids']);
		$fmdata['cfgs'] = empty($cfgsnew) ? '' : addslashes(var_export($cfgsnew,TRUE));
		$db->query("UPDATE {$tblprefix}pagecaches SET 
					cname='$fmdata[cname]', 
					demourl='$fmdata[demourl]', 
					period='$fmdata[period]', 
					pagefrom='$fmdata[pagefrom]', 
					pageto='$fmdata[pageto]', 
					cfgs='$fmdata[cfgs]'
					WHERE pcid='$pcid'");
			adminlog('�༭ҳ�滺�淽��');
			cls_CacheFile::Update('pagecaches');
			cls_message::show('���淽�����óɹ���', axaction(6,"?entry=$entry&action=pagecachesedit"));
	}

}
function fetch_one($pcid){
	global $db,$tblprefix;
	$r = $db->fetch_one("SELECT * FROM {$tblprefix}pagecaches WHERE pcid='$pcid'");
	if(empty($r['cfgs']) || !is_array($r['cfgs'] = @varexp2arr($r['cfgs']))) $r['cfgs'] = array();
	return $r;
}

?>