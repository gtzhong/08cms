<?php
(!defined('M_COM') || !defined('M_ADMIN')) && exit('No Permission');
aheader();
if($re = $curuser->NoBackFunc('tpl')) cls_message::show($re);
if($action == 'mtconfigsedit'){
	backnav('mtconfigs','mtconfigs');
	$mtconfigs = cls_mtconfig::InitialInfoArray();
	if(!submitcheck('bsubmit')){
		
		$TitleStr = "�ռ�ģ�巽��";
		$TitleStr .= " &nbsp; &nbsp;>><a href=\"?entry=$entry&action=mtconfigadd\" onclick=\"return floatwin('open_mtconfigsedit',this)\">��ӷ���</a>";
		tabheader($TitleStr,'mtconfigsedit',"?entry=$entry&action=$action",'4');
		$CategoryArray = array();
		$CategoryArray[] = 'ID';
		$CategoryArray[] = '��������|L';
		$CategoryArray[] = '�������ͻ�Ա��ѡ|L';
		$CategoryArray[] = '���»�Ա���ѡ|L';
		$CategoryArray[] = '����';
		$CategoryArray[] = 'ɾ��';
		$CategoryArray[] = '��Ŀ';
		$CategoryArray[] = '�ĵ�';
		trcategory($CategoryArray);
		
		foreach($mtconfigs as $k => $v){
			$_views = array();
			$_views['id'] = $k;
			$_views['cname'] = OneInputText("fmdata[$k][cname]",$v['cname'],25);
			$_views['mchids'] = makecheckbox("fmdata[$k][mchids][]",cls_mchannel::mchidsarr(),empty($v['mchids']) ? array() : explode(',',$v['mchids']),5);
			$_views['pmid'] = makeselect("fmdata[$k][pmid]",makeoption(pmidsarr('tpl'),@$v['pmid']));
			$_views['vieworder'] = intval(@$v['vieworder']);
			$_views['delete'] = "<input class=\"checkbox\" type=\"checkbox\" name=\"delete[$k]\" value=\"$k\" onclick=\"deltip(this,$no_deepmode)\">";
			$_views['index'] = "<a href=\"?entry=$entry&action=mtconfigdetail&mtcid=$k\" onclick=\"return floatwin('open_mtconfigsedit',this)\">ģ��</a>";
			$_views['archive'] = "<a href=\"?entry=$entry&action=mtconfigtpl&mtcid=$k\" onclick=\"return floatwin('open_mtconfigsedit',this)\">ģ��</a>";
			
			echo "<tr class=\"txt\">\n".
				"<td class=\"txtC w30\">{$_views['id']}</td>\n".
				"<td class=\"txtL w100\">{$_views['cname']}</td>\n".
				"<td class=\"txtL\">{$_views['mchids']}</td>\n".
				"<td class=\"txtL\">{$_views['pmid']}</td>\n".
				"<td class=\"txtC w35\">".OneInputText("fmdata[$k][vieworder]",$_views['vieworder'],4)."</td>\n".
				"<td class=\"txtC w35\">{$_views['delete']}</td>\n".
				"<td class=\"txtC w35\">{$_views['index']}</td>\n".
				"<td class=\"txtC w35\">{$_views['archive']}</td>\n".
				"</tr>";
		}
		tabfooter('bsubmit');
		a_guide('mtconfigsedit');
	}else{
		if(!empty($delete) && deep_allow($no_deepmode)){
			foreach($delete as $k){
				if(!cls_mtconfig::DeleteOne($k)){
					unset($fmdata[$k]);
				}
			}
		}
		if(!empty($fmdata)){
			foreach($fmdata as $k => $v){
				try {
					cls_mtconfig::ModifyOneConfig($v,$k);
				} catch (Exception $e){
					continue;
				}
			}
		}
		adminlog('�༭�ռ�ģ�巽�������б�');
		cls_message::show('�ռ�ģ�巽���޸����', "?entry=$entry&action=$action");
	}
}elseif($action == 'mtconfigadd'){
	echo _08_HTML::Title('��ӿռ�ģ�巽��');
	if(!submitcheck('bsubmit')){
		tabheader('��ӿռ�ģ�巽��','mtconfigadd',"?entry=$entry&action=$action");
		trbasic('ģ�巽������','fmdata[cname]');
		trbasic('�������ͻ�Ա��ѡ','',makecheckbox('fmdata[mchids][]',cls_mchannel::mchidsarr(),array(),5),'');
		setPermBar('���»�Ա���ѡ', 'fmdata[pmid]', '', 'tpl', $soext='open', $guide='');
        tabfooter('bsubmit');
		a_guide('mtconfigdetail');
	}else{
		try {
			cls_mtconfig::ModifyOneConfig($fmdata,0);
		} catch (Exception $e){
			cls_message::show($e->getMessage());
		}
		
		adminlog('��ӿռ�ģ�巽��');
		cls_message::show('ģ�巽��������', axaction(6,"?entry=$entry&action=mtconfigsedit"));
	}

}elseif($action == 'mtconfigdetail' && !empty($mtcid)){
	echo _08_HTML::Title('�ռ���Ŀģ��');
	if(!($mtconfig = cls_mtconfig::InitialOneInfo($mtcid))) cls_message::show('��ָ����ȷ�Ŀռ�ģ�巽��');
	$setting = $mtconfig['setting'];
	$mcatalogs = cls_mcatalog::InitialInfoArray();
	if(!submitcheck('bsubmit')){
		tabheader($mtconfig['cname'].' - �ռ���Ŀģ��','mtconfigdetail',"?entry=$entry&action=$action&mtcid=$mtcid",5);
		
		$CategoryArray = array();
		$CategoryArray[] = "<input class=\"checkbox\" type=\"checkbox\" name=\"chkall\" onclick=\"checkall(this.form, 'selectid', 'chkall')\">";
		$CategoryArray[] = '����';
		$CategoryArray[] = '�ռ���Ŀ|L';
		$CategoryArray[] = '��Ŀ����ҳģ��|L';
		$CategoryArray[] = '��Ŀ����ҳģ��|L';
		trcategory($CategoryArray);
		
		$_views = array();
		$_views[0] = array( # �ռ���ҳ
			'title' => '<b>��ҳ</b>',
			'enable' => 'Y',
			'index' => empty($setting[0]['index']) ? '-' : $setting[0]['index'],
			'list' => '-',
		);
		foreach($mcatalogs as $k => $v){
			$_views[$k] = array( # �ռ���Ŀҳ
				'title' => isset($setting[$k]) ? "<b>{$v['title']}</b>" : $v['title'],
				'enable' => isset($setting[$k]) ? 'Y' : '-',
				'index' => empty($setting[$k]['index']) ? '-' : $setting[$k]['index'],
				'list' => empty($setting[$k]['list']) ? '-' : $setting[$k]['list'],
			);
		}
		
		foreach($_views as $k => $v){
			echo "<tr class=\"txt\">\n".
				"<td class=\"txtC w40\"><input class=\"checkbox\" type=\"checkbox\" name=\"selectid[$k]\" value=\"$k\"></td>\n".
				"<td class=\"txtC w40\">{$v['enable']}</td>\n".
				"<td class=\"txtL\">{$v['title']}</td>\n".
				"<td class=\"txtL\">{$v['index']}</td>\n".
				"<td class=\"txtL\">{$v['list']}</td>\n".
				"</tr>";
		}
		tabfooter();

		tabheader('������Ŀ');
		trbasic("���ÿռ���Ŀ <input class=\"checkbox\" type=\"checkbox\" name=\"cndeal[enable]\" value=\"1\">",'',makeradio('spaceenable',array('0' => '������','1' => '����')),'');
		foreach(array('index' => '��Ŀ����ҳģ��','list' => '��Ŀ����ҳģ��',) as $k => $v){
			trbasic("$v <input class=\"checkbox\" type=\"checkbox\" name=\"cndeal[$k]\" value=\"1\">","space$k",makeoption(array('' => '������') + cls_mtpl::mtplsarr('space')),'select',array('guide' => cls_mtpl::mtplGuide('space')));
		}
		tabfooter('bsubmit');
		a_guide('mtconfigdetail');
	}else{
		if(empty($selectid)) cls_message::show('��ѡ�������Ŀ',M_REFERER);
		if(empty($cndeal)) cls_message::show('��ѡ�������Ŀ',M_REFERER);
		
		foreach($selectid as $k){
			if(!empty($cndeal['enable'])){
				if($k){
					if(empty($spaceenable)){
						unset($setting[$k]);
					}else{
						$setting[$k] = isset($setting[$k]) ? $setting[$k] : array();
					}
				}else{
					$setting[$k] = isset($setting[$k]) ? $setting[$k] : array();
				}
			}
			if(!empty($cndeal['index'])){
				if(!$k || isset($setting[$k])){
					$setting[$k]['index'] = $spaceindex;
				}
			}
			if(!empty($cndeal['list'])){ # ��ҳû�и���ҳ
				if($k && isset($setting[$k])){
					$setting[$k]['list'] = $spacelist;
				}
			}
		}
		
		try {
			cls_mtconfig::ModifyOneConfig(array('setting' => $setting),$mtcid);
		} catch (Exception $e){
			cls_message::show($e->getMessage());
		}
		
		adminlog('��ϸ�޸Ŀռ�ģ�巽��');
		cls_message::show('ģ�巽���������',"?entry=$entry&action=$action&mtcid=$mtcid");
	}
}elseif($action == 'mtconfigtpl' && !empty($mtcid)){
	echo _08_HTML::Title('�ռ��ĵ�ģ��');
	if(!($mtconfig = cls_mtconfig::InitialOneInfo($mtcid))) cls_message::show('��ָ����ȷ�Ŀռ�ģ�巽��');
	$arctpls = $mtconfig['arctpls'];
	if(!submitcheck('bsubmit')){
		tabheader($mtconfig['cname'].' - �ռ��ĵ�ģ��','mtconfigdetail',"?entry=$entry&action=$action&mtcid=$mtcid",5);

		trcategory(array("<input class=\"checkbox\" type=\"checkbox\" name=\"chkall\" onclick=\"checkall(this.form, 'selectid', 'chkall')\">",'ID',array('�ĵ�ģ��','txtL'),'����ҳ','��չҳ1','��չҳ2'));
		$channels = cls_channel::Config();
		foreach($channels as $k => $v){
			$archivetpl = empty($arctpls['archive'][$k]) ? '-' : $arctpls['archive'][$k];
			$ex1tpl = empty($arctpls['ex1'][$k]) ? '-' : $arctpls['ex1'][$k];
			$ex2tpl = empty($arctpls['ex2'][$k]) ? '-' : $arctpls['ex2'][$k];
			echo "<tr class=\"txt\">\n".
				"<td class=\"txtC w40\"><input class=\"checkbox\" type=\"checkbox\" name=\"selectid[$k]\" value=\"$k\"></td>\n".
				"<td class=\"txtC w40\">$k</td>\n".
				"<td class=\"txtL\">$v[cname]</td>\n".
				"<td class=\"txtC\">$archivetpl</td>\n".
				"<td class=\"txtC\">$ex1tpl</td>\n".
				"<td class=\"txtC\">$ex2tpl</td>\n".
				"</tr>";
		}
		tabfooter();

		tabheader('������Ŀ');
		foreach(array('archive' => '�ĵ�����ҳ','ex1' => '��չҳ1','ex2' => '��չҳ2',) as $k => $v){
			trbasic("$v <input class=\"checkbox\" type=\"checkbox\" name=\"cndeal[$k]\" value=\"1\">","tpl$k",makeoption(array('' => '������') + cls_mtpl::mtplsarr('space')),'select',array('guide' => cls_mtpl::mtplGuide('space')));
		}
		tabfooter('bsubmit');
		a_guide('mtconfigdetail');
	}else{
		if(empty($selectid)) cls_message::show('��ѡ�������Ŀ',M_REFERER);
		if(empty($cndeal)) cls_message::show('��ѡ�������Ŀ',M_REFERER);
		foreach($selectid as $k){
			foreach(array('archive','ex1','ex2',) as $var){
				if(!empty($cndeal[$var])){
					$arctpls[$var][$k] = ${"tpl$var"};
				}
			}
		}
		
		try {
			cls_mtconfig::ModifyOneConfig(array('arctpls' => $arctpls),$mtcid);
		} catch (Exception $e){
			cls_message::show($e->getMessage());
		}
		
		adminlog('��ϸ�޸Ŀռ�ģ�巽��');
		cls_message::show('ģ�巽���������',"?entry=$entry&action=$action&mtcid=$mtcid");
	}
}