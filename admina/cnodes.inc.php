<?php
(!defined('M_COM') || !defined('M_ADMIN')) && exit('No Permission');
aheader();
if($re = $curuser->NoBackFunc('tpl')) cls_message::show($re);
foreach(array('catalogs','mtpls','cotypes','cntpls','cnconfigs',) as $k) $$k = cls_cache::Read($k);
if($action == 'cnconfigs'){
	backnav('cnode','cnconfigs');
	if(!submitcheck('bcnconfigs')){
		$ncoid = isset($ncoid) ? intval($ncoid) : -1;//����ĳ��ϵ�йصķ�������Ԥѡ
		tabheader("�ڵ���ɷ���&nbsp; &nbsp; >><a href=\"?entry=$entry&action=cnconfigadd\" onclick=\"return floatwin('open_cnodes',this)\">���</a>",'cnodesupdate',"?entry=$entry&action=$action",3);
		trcategory(array("<input class=\"checkbox\" type=\"checkbox\" name=\"chkall\" onclick=\"checkall(this.form, 'selectid', 'chkall')\">",'ID',array('��������','txtL'),array('��ע˵��','txtL'),array('�ڵ���ɽṹ','txtL'),array('�ڵ�����','txtL'),'�ر�','����','����','�༭'));
		foreach($cnconfigs as $k => $v){
			$configstr = '';$checked = 0;
			if(empty($v['isfunc'])){
				$idsarr = cfgs2ids($v['configs']);
				foreach($v['configs'] as $k1 => $v1){
					$configstr .= ($configstr ? ' x ' : '').($k1 ? @$cotypes[$k1]['cname'] : '��Ŀ').'('.count($idsarr[$k1]).')';
					$k1 == $ncoid && $checked = 1;
				}
			}else{
				foreach($v['configs'] as $k1 => $v1){
					$configstr .= ($configstr ? ' x ' : '').($k1 ? @$cotypes[$k1]['cname'] : '��Ŀ');
					$k1 == $ncoid && $checked = 1;
				}
				$configstr .= '...����';
			}
			$cntplstr = empty($cntpls[$v['tid']]['cname']) ? '-' : $v['tid'].'-'.$cntpls[$v['tid']]['cname'];
			echo "<tr class=\"txt\">".
				"<td class=\"txtC w30\"><input class=\"checkbox\" type=\"checkbox\" name=\"selectid[]\" value=\"$k\"".($checked ? ' checked' : '')."></td>\n".
				"<td class=\"txtC\">$k</td>\n".
				"<td class=\"txtL\"><input type=\"text\" size=\"20\" maxlength=\"30\" name=\"cnconfigsnew[$k][cname]\" value=\"$v[cname]\"></td>\n".
				"<td class=\"txtL\"><input type=\"text\" size=\"35\" maxlength=\"50\" name=\"cnconfigsnew[$k][remark]\" value=\"$v[remark]\"></td>\n".
				"<td class=\"txtL\">$configstr</td>\n".
				"<td class=\"txtL\">$cntplstr</td>\n".
				"<td class=\"txtC w30\"><input class=\"checkbox\" type=\"checkbox\" name=\"cnconfigsnew[$k][closed]\" value=\"1\"".($v['closed'] ? " checked" : "")."></td>\n".
				"<td class=\"txtC w40\"><input type=\"text\" size=\"4\" maxlength=\"4\" name=\"cnconfigsnew[$k][vieworder]\" value=\"$v[vieworder]\"></td>\n".
				"<td class=\"txtC w30\"><a href=\"?entry=$entry&action=cnconfigdetail&cncid=$k&iscopy=1\" onclick=\"return floatwin('open_cnodes',this)\">����</a></td>\n".
				"<td class=\"txtC w30\"><a href=\"?entry=$entry&action=cnconfigdetail&cncid=$k\" onclick=\"return floatwin('open_cnodes',this)\">����</a></td>\n".
				"</tr>\n";
		}
		tabfooter();
		tabheader('������Ŀ'.viewcheck(array('name' => 'viewdetail','value' =>0,'body' =>$actionid.'tbodyfilter',)).' &nbsp;��ʾ��ϸ');
		$str = "<input class=\"radio\" type=\"radio\" name=\"arcdeal\" value=\"edit\" checked>�޸ķ����б� &nbsp;";
		$str .= "<input class=\"radio\" type=\"radio\" name=\"arcdeal\" value=\"newupdate\"".(empty($arcdeal) || $arcdeal != 'newupdate' ? '' : ' checked')."><b>��ȫ�����нڵ�</b> &nbsp;";
		$str .= "<input class=\"radio\" type=\"radio\" name=\"arcdeal\" value=\"oldupdate\"".(empty($arcdeal) || $arcdeal != 'oldupdate' ? '' : ' checked')."><b>��ȫ�����½ڵ�����</b> &nbsp;";
		$str .= "<input class=\"radio\" type=\"radio\" name=\"arcdeal\" value=\"delete\" onclick=\"return deltip(this,$no_deepmode)\">ɾ������ &nbsp;";
		trbasic('ѡ�������Ŀ','',$str,'');
		echo "<tbody id=\"{$actionid}tbodyfilter\" style=\"display:none\">";
		$cnmodearr = array(0 => '����ģʽ',1 => '���ģʽ',2 => '�Ƴ�ģʽ',);
		trbasic("<input class=\"radio\" type=\"radio\" name=\"arcdeal\" value=\"ccid0\">&nbsp;�޸ķ������:��Ŀ",'',multiselect('cnccids0[]',cls_catalog::ccidsarr(0),array(),'30%').
		"&nbsp; &nbsp; <select id=\"cnmode0\" name=\"cnmode0\" style=\"vertical-align: top;\">".makeoption($cnmodearr)."</select>",'',array('guide' => '����ǰ��Ŀ����ϵ������ѡ�з�������Ϊ�ֶ�ѡ��ʱ��Ч���޸ķ������Զ����½ڵ㡣',));
		foreach($cotypes as $k => $v){
			if($v['sortable']){
				trbasic("<input class=\"radio\" type=\"radio\" name=\"arcdeal\" value=\"ccid$k\">&nbsp;�޸ķ������:".$v['cname'],'',multiselect('cnccids'.$k.'[]',cls_catalog::ccidsarr($k),array(),'30%').
				"&nbsp; &nbsp; <select id=\"cnmode$k\" name=\"cnmode$k\" style=\"vertical-align: top;\">".makeoption($cnmodearr)."</select>",'');
			}
		}
		echo "</tbody>";
		tabfooter('bcnconfigs');
		a_guide('cnconfigs');
	}else{
		if(!empty($arcdeal)){
			if($arcdeal == 'edit'){
				if(!empty($cnconfigsnew)){
					foreach($cnconfigsnew as $k => $v){
						$v['cname'] = trim(strip_tags($v['cname']));
						!$v['cname'] && $v['cname'] = $cnconfigs[$k]['cname'];
						$v['remark'] = trim(strip_tags($v['remark']));
						$v['closed'] = empty($v['closed']) ? 0 : 1;
						$v['vieworder'] = max(0,intval($v['vieworder']));
						foreach(array('cname','remark','closed','vieworder',) as $var) $cnconfigs[$k][$var] = $v[$var];
					}
					cls_Array::_array_multisort($cnconfigs,'vieworder',1);
					cls_CacheFile::Save($cnconfigs,'cnconfigs','cnconfigs');
				}
			}elseif($arcdeal == 'delete'){
				if(!empty($selectid) && deep_allow($no_deepmode)){
					foreach($selectid as $k){
						unset($cnconfigs[$k]);
						unset($cnconfigsnew[$k]);
					}
					cls_CacheFile::Save($cnconfigs,'cnconfigs','cnconfigs');
				}
			}elseif(in_array($arcdeal,array('newupdate','oldupdate'))){
				if(empty($selectid) && empty($selectstr)) cls_message::show('��ѡ��ڵ���ɷ���',"?entry=$entry&action=$action");
				if(empty($selectid)) $selectid = array_filter(explode(',',$selectstr));
				$pages = max(empty($pages) ? 0 : max(0,intval($pages)),count($selectid));
				$cncid = $selectid[0];
				if($cnconfig = $cnconfigs[$cncid]){
					if(cnodesfromcnc($cnconfig,$arcdeal == 'newupdate' ? 0 : 1)) cls_CacheFile::Update('cnodes');
				}
				unset($selectid[0]);
				if($selectid){
					$selectstr = implode(',',$selectid);
					$npage = $pages - count($selectid) + 1;
					cls_message::show("�ڵ�������ڽ�����...<br>�� $pages ҳ�����ڴ���� $npage ҳ<br><br><a href=\"?entry=$entry&action=$action\">>>��ֹ��ǰ����</a>","?entry=$entry&action=$action&selectstr=$selectstr&arcdeal=$arcdeal&pages=$pages&bcnconfigs=1");
				}
			}elseif(in_str('ccid',$arcdeal)){//���½ṹ�������½ڵ�
				if(!empty($selectid)){
					$coid = intval(str_replace('ccid','',$arcdeal));
					${"cnccids$coid"} = empty(${"cnccids$coid"}) ? array() : ${"cnccids$coid"};
					${"cnmode$coid"} = empty(${"cnmode$coid"}) ? 0 : ${"cnmode$coid"};
					foreach($selectid as $k) modify_cnconfig(@$cnconfigs[$k],$coid,${"cnccids$coid"},${"cnmode$coid"});
					cls_CacheFile::Save($cnconfigs,'cnconfigs','cnconfigs');
				}
			}
		}
		adminlog('���������ڵ���ɷ���');
		cls_message::show('��Ϸ����������', "?entry=$entry&action=$action");
	}
}elseif($action == 'patchupdate'){
	echo "<title>��Ŀ�ڵ�������ȫ</title>";
	$coid = empty($coid) ? 0 : intval($coid);
	if($coid && empty($cotypes[$coid])) cls_message::show('ָ������ϵ������');
	if(empty($selectstr)){
		$selectid = array();
		foreach($cnconfigs as $k => $v) empty($v['configs'][$coid]) || $selectid[] = $k;
	}else $selectid = array_filter(explode(',',$selectstr));
	if($selectid){
		$pages = max(empty($pages) ? 0 : max(0,intval($pages)),count($selectid));
		$cncid = $selectid[0];
		if($cnconfig = $cnconfigs[$cncid]){
			if(cnodesfromcnc($cnconfig)) cls_CacheFile::Update('cnodes');
		}
		unset($selectid[0]);
	}
	if($selectid){
		$selectstr = implode(',',$selectid);
		$npage = $pages - count($selectid) + 1;
		cls_message::show("�ڵ�������ڽ�����...<br>�� $pages ҳ�����ڴ���� $npage ҳ","?entry=$entry&action=$action&coid=$coid&selectstr=$selectstr&pages=$pages");
	}else cls_message::show('�ڵ�������ȫ���',axaction(2));
}elseif($action == 'clearnodes'){
	echo "<title>������нڵ㲢�ؽ�</title>";
	if(!submitcheck('bsubmit')) cls_message::show('�Ƿ��Ĳ�����'); # ������ʾվ�Ĳ���Ȩ��
	$db->query("TRUNCATE TABLE {$tblprefix}cnodes");
	if(!empty($cnconfigs)){
		$selectid = array();
		foreach($cnconfigs as $k => $v){
			$selectid[] = $k;
		}
		$selectstr = implode(',',$selectid);
		$pages = count($selectid);
		$npage = 1;
		cls_message::show("�ڵ�����գ����������ؽ��ڵ�...<br>�� $pages ҳ�����ڴ���� $npage ҳ","?entry=$entry&action=patchupdate&selectstr=$selectstr&pages=$pages");
		
	}else cls_message::show('���нڵ�����գ�δ�����κνڵ㡣',axaction(2));
}elseif($action == 'cnconfigdetail' && $cncid){
	$iscopy = empty($iscopy) ? 0 : 1;
	echo "<title>".($iscopy ? '���ƽڵ���ɷ���' : '�༭�ڵ���ɷ���')."</title>";
	if(!($cnconfig = @$cnconfigs[$cncid])) cls_message::show('��ָ����ȷ�Ľڵ���ɷ���');
	$configs = &$cnconfig['configs'];
	if(!submitcheck('bsubmit')){
		tabheader($iscopy ? '���ƽڵ���ɷ���' : '�༭�ڵ���ɷ���','cnconfigdetail',"?entry=$entry&action=$action".($iscopy ? '&iscopy=1' : '')."&cncid=$cncid",2,0,1);
		trbasic('��������','cnconfignew[cname]',$cnconfig['cname'].($iscopy ? '_����' : ''),'text',array('w' => 50,'validate' => makesubmitstr('cnconfignew[cname]',1,0,4,50)));
		$arr = array(0 => '������');
		foreach($cntpls as $k => $v) $arr[$k] = $k.'-'.$v['cname'];
		trbasic('�ڵ�����','cnconfignew[tid]',makeoption($arr,$cnconfig['tid']),'select');
		$modearr = array(0 => 'ȫ������',1 => 'ȫ��������Ŀ',2 => 'ȫ��������Ŀ',3 => 'ȫ��������Ŀ',4 => 'ȫ���ļ���Ŀ',5 => '������չ����',-1 => '�ֶ�ָ��');
		$nomodearr = array(0 => '������',1 => '�ֶ�ָ��');
		$i = 1;
		foreach($configs as $k => $v){
			$arr = $k ? cls_cache::Read('coclasses',$k) : $catalogs;
			foreach($arr as $x => $y) $arr[$x] = $y['title'].'('.$y['level'].')';
			$cname = $k ? $cotypes[$k]['cname'] : '��Ŀ';
			sourcemodule("$i.����:".$cname.
				"<br><input class=\"checkbox\" type=\"checkbox\" name=\"configsnew[$k][son]\" value=\"1\"".(empty($v['son']) ? "" : " checked").">���ӷ���",
				"configsnew[$k][mode]",
				$modearr,
				empty($v['mode']) ? 0 : $v['mode'],
				-1,
				"configsnew[$k][ids][]",
				$arr,
				empty($v['ids']) ? array() : explode(',',$v['ids']),
				'25%',1,'',1
			);
			sourcemodule("$i.�ų�:".$cname.
				"<br><input class=\"checkbox\" type=\"checkbox\" name=\"configsnew[$k][noson]\" value=\"1\"".(empty($v['noson']) ? "" : " checked").">���ӷ���",
				"configsnew[$k][nomode]",
				$nomodearr,
				empty($v['noids']) ? 0 : 1,
				1,
				"configsnew[$k][noids][]",
				$arr,
				empty($v['noids']) ? array() : explode(',',$v['noids']),
				'25%',1,'',1
			);
			$i ++;
		}
		if(!empty($cnconfig['isfunc'])){
			trbasic('��չ����','cnconfignew[funcode]',$cnconfig['funcode'],'textarea',array('guide'=>'��ʹ��return ��չ������(����...);���룬���󷵻�FALSE���ɹ�����TRUE��<br>��ǰ��Ϸ�������ʹ��$cnconfig���롣<br>��չ����Ϊ��ǰ�������ɽڵ���̣��붨�嵽'._08_EXTEND_DIR.DS._08_LIBS_DIR.DS.'functions'.DS.'custom.fun.php'));
			trbasic('����ʹ�ñ�ע','cnconfignew[fremark]',$cnconfig['fremark'],'textarea');
		}
		tabfooter('bsubmit');
	}else{
		$cnconfignew['cname'] = trim(strip_tags($cnconfignew['cname']));
		$cnconfignew['cname'] || $cnconfignew['cname'] = $cnconfig['cname'];
		if(!empty($configsnew)){
			foreach($configsnew as $k => $v){
				foreach(array('ids','noids') as $var) $configsnew[$k][$var] = $configsnew[$k][$var][0];
				foreach(array('son','noson') as $var) $configsnew[$k][$var] = empty($configsnew[$k][$var]) ? 0 : 1;
				if(empty($configsnew[$k]['nomode'])) $configsnew[$k]['noids'] = '';
				unset($configsnew[$k]['nomode']);
			}
		}
		if(!$configsnew) cls_message::show('������д������',M_REFERER);
		
		if($iscopy){
			$cncid = auto_insert_id('cnconfigs');
			$cnconfig['cncid'] = $cncid;
		}
		
		$cnconfig['cname'] = $cnconfignew['cname'];
		$cnconfig['tid'] = $cnconfignew['tid'];
		$cnconfig['configs'] = $configsnew;
		if(!empty($cnconfig['isfunc'])){
			$cnconfignew['funcode'] = trim($cnconfignew['funcode']);
			if(!$cnconfignew['funcode']) cls_message::show('������д������',M_REFERER);
			$cnconfig['isfunc'] = 1;
			$cnconfig['funcode'] = $cnconfignew['funcode'];
			$cnconfig['fremark'] = $cnconfignew['fremark'];
		}
		$cnconfigs[$cncid] = $cnconfig;
		cls_CacheFile::Save($cnconfigs,'cnconfigs','cnconfigs');
		
		adminlog($iscopy ? '���ƽڵ���ɷ���' : '�༭�ڵ���ɷ���');
		cls_message::show($iscopy ? '���ƽڵ���ɷ������' : '�༭�ڵ���ɷ������',axaction(6,"?entry=$entry&action=cnconfigs"));
	}
}elseif($action == 'cnconfigadd'){
	echo "<title>��ӽڵ���ɷ���</title>";
	if(!submitcheck('bsubmit')){
		tabheader('��ӽڵ���ɷ���','cnconfigsadd',"?entry=$entry&action=$action",2,0,1);
		trbasic('�ڵ���ɷ�������','cncfgcname',@$cncfgcname,'text',array('w' => 50,'validate' => makesubmitstr('cncfgcname',1,0,4,50)));
		$arr = array(0 => '������');
		foreach($cntpls as $k => $v) $arr[$k] = $k.'-'.$v['cname'];
		trbasic('ѡ��ڵ�����','cntplnew',makeoption($arr,@$cntplnew),'select');
		if(empty($cncoids)){
			ksort($cotypes);
			$coidsarr = array('caid' => '��Ŀ');
			foreach($cotypes as $k => $v) $v['sortable'] && $coidsarr['ccid'.$k] = $v['cname'];
			trbasic('*�������ɽڵ����ϵ','',makecheckbox('cncoids[]',$coidsarr,array(),5),'');
			trbasic('�������ģʽ','',makeradio('isfunc',array('��ͨģʽ','��չ����'),empty($isfunc) ? 0 : 1),'',array('guide' => '��ע�⣬�����ύ�󲻿ɱ��'));
			tabfooter('baddpre','����');
		}else{
				$modearr = array(0 => 'ȫ������',1 => 'ȫ��������Ŀ',2 => 'ȫ��������Ŀ',3 => 'ȫ��������Ŀ',4 => 'ȫ���ļ���Ŀ',5 => '������չ����',-1 => '�ֶ�ָ��');
				$nomodearr = array(0 => '������',1 => '�ֶ�ָ��');
				$i = 1;
				$cncoids = array_filter($cncoids); //ȥ����һ��������
				foreach($cncoids as $k){
					$k = $k == 'caid' ? 0 :  intval(str_replace('ccid','',$k));
					$arr = $k ? cls_cache::Read('coclasses',$k) : $catalogs;
					foreach($arr as $x => $y) $arr[$x] = $y['title'].'('.$y['level'].')';
					$cname = $k ? $cotypes[$k]['cname'] : '��Ŀ';
					sourcemodule("$i.".'�������£�'.$cname.
						"<br><input class=\"checkbox\" type=\"checkbox\" name=\"configsnew[$k][son]\" value=\"1\">���ӷ���",
						"configsnew[$k][mode]",
						$modearr,
						0,
						-1,
						"configsnew[$k][ids][]",
						$arr,
						array(),
						'25%',1,'',1
					);
					sourcemodule("$i.".'�ų����£�'.$cname.
						"<br><input class=\"checkbox\" type=\"checkbox\" name=\"configsnew[$k][noson]\" value=\"1\">���ӷ���",
						"configsnew[$k][nomode]",
						$nomodearr,
						0,
						1,
						"configsnew[$k][noids][]",
						$arr,
						array(),
						'25%',1,'',1
					);
					$i ++;
				}
			if(!empty($isfunc)){
				trbasic('��չ����','funcodenew','','textarea',array('guide'=>'��ʹ��return ��չ������(����...);���룬���󷵻�FALSE���ɹ�����TRUE��<br>��ǰ��Ϸ�������ʹ��$cnconfig���롣<br>��չ����Ϊ��ǰ�������ɽڵ���̣��붨�嵽'._08_EXTEND_DIR.DS._08_LIBS_DIR.DS.'functions'.DS.'custom.fun.php'));
				trbasic('����ʹ�ñ�ע','fremarknew','','textarea');
				trhidden('isfunc',1);
			}
			tabfooter('bsubmit','���');
		}
		a_guide('cnconfigs');
	}else{
		if(!$cncfgcname = trim($cncfgcname)) cls_message::show('�����뷽������',M_REFERER);
		$tid = empty($cntplnew) ? 0 : $cntplnew;
		foreach($configsnew as $k => $v){
			foreach(array('ids','noids') as $var) $configsnew[$k][$var] = $configsnew[$k][$var][0];
			foreach(array('son','noson') as $var) $configsnew[$k][$var] = empty($configsnew[$k][$var]) ? 0 : 1;
			if(empty($configsnew[$k]['nomode'])) $configsnew[$k]['noids'] = '';
			unset($configsnew[$k]['nomode']);
		}
		if(empty($configsnew)) cls_message::show('��������Ŀ���',M_REFERER);
		$cncid = auto_insert_id('cnconfigs');
		$cnconfig = array('cncid' => $cncid,'cname' => $cncfgcname,'configs' => $configsnew,'tid' => $tid,'vieworder' => 0,'remark' => '','closed' => 0,);
		if(!empty($isfunc)){
			$funcodenew = trim($funcodenew);
			if(empty($funcodenew)) cls_message::show('��������չ��������',M_REFERER);
			$cnconfig['isfunc'] = 1;
			$cnconfig['funcode'] = $funcodenew;
			$cnconfig['fremark'] = $fremarknew;
		}
		$cnconfigs[$cncid] = $cnconfig;
		
		cls_CacheFile::Save($cnconfigs,'cnconfigs','cnconfigs');
		adminlog('��ӽڵ���ɷ���');
		cls_message::show('�ڵ���ɷ�����ӳɹ�',axaction(6,"?entry=$entry&action=cnconfigs"));
	}
}elseif($action == 'cntpladd'){
	echo "<title>��ӽڵ�����</title>";
	if(!submitcheck('bcntpladd')){
		tabheader('��ӽڵ�����','cntpladd',"?entry=$entry&action=$action",2,0,1);
		trbasic('�ڵ���������','cntplnew[cname]','','text',array('validate'=>makesubmitstr('cntplnew[cname]',1,1,4,30)));
		tabfooter('bcntpladd','���');
		a_guide('cntpladd');
	} else {
		if(!($cntplnew['cname'] = trim(strip_tags($cntplnew['cname'])))) cls_message::show('��������⣡',M_REFERER);
		$tid = auto_insert_id('cntpls');
		$cntpls[$tid] = array('tid' => $tid,'cname' => $cntplnew['cname'],'addnum' =>0,'vieworder' => 0,'cfgs' => array(),);
		cls_CacheFile::Save($cntpls,'cntpls','cntpls');
		adminlog('��ӽڵ�����');
		cls_message::show('�ڵ�����������',"?entry=$entry&action=cntpldetail&tid=$tid");
	}
}elseif($action == 'cntpldetail' && $tid){
	if(!($cntpl = @$cntpls[$tid])) cls_message::show('��ѡ��ڵ�����');
	echo "<title>�ڵ����� - $cntpl[cname]</title>";
	if(!submitcheck('bcntpldetail')){
		tabheader("�ڵ�����&nbsp;&nbsp;[$cntpl[cname]]",'cntpldetail',"?entry=$entry&action=$action&tid=$tid");
		$arr = array();for($i = 0;$i <= $cn_max_addno;$i ++) $arr[$i] = $i;
		$addnum = empty($cntpl['addnum']) ? 0 : $cntpl['addnum'];
		trbasic('����ҳ����','',makeradio('fmdata[addnum]',$arr,$addnum),'');
		trbasic('�ڵ�RSSģ��','fmdata[rsstpl]',makeoption(array('' => '������') + cls_mtpl::mtplsarr('xml'),@$cntpl['rsstpl']),'select',array('guide' => cls_mtpl::mtplGuide('xml')));
		tabfooter();
		
		$cfgs = @$cntpl['cfgs'];
		for($i = 0;$i <= $cn_max_addno;$i ++){
			tabheader(($i ? '����ҳ'.$i : '�ڵ���ҳ').'����'.viewcheck(array('name' =>'viewdetail','title' => '��ϸ','value' => $i > $addnum ? 0 : 1,'body' =>$actionid.'tbodyfilter'.$i)));
			echo "<tbody id=\"{$actionid}tbodyfilter$i\" style=\"display:".($i > $addnum ? 'none' : '')."\">";
			trbasic('ҳ��ģ��',"cfgsnew[$i][tpl]",makeoption(array('' => '������') + cls_mtpl::mtplsarr('cindex'),empty($cfgs[$i]['tpl']) ? '' : $cfgs[$i]['tpl']),'select',array('guide' => cls_mtpl::mtplGuide('cindex')));
			trbasic('��̬�����ʽ',"cfgsnew[$i][url]",empty($cfgs[$i]['url']) ? '' : $cfgs[$i]['url'],'text',array('guide'=>'������ϵͳ�����ã�{$cndir}ϵͳĬ�ϱ���·����{$page}��ҳҳ�룬����֮�佨����Ϸָ���_��-���ӡ�','w'=>50));
			trbasic('�Ƿ����ɾ�̬','',makeradio("cfgsnew[$i][static]",array(0 => '��ϵͳ������',1 => '���ֶ�̬'),empty($cfgs[$i]['static']) ? 0 : $cfgs[$i]['static']),'');
			trbasic('��̬��������(����)',"cfgsnew[$i][period]",empty($cfgs[$i]['period']) ? '' : $cfgs[$i]['period'],'text',array('guide'=>'������ϵͳ������','w'=>4));
			trbasic('���⾲̬URL','',makeradio("cfgsnew[$i][novu]",array(0 => '��ϵͳ������',1 => '�ر����⾲̬'),empty($cfgs[$i]['novu']) ? 0 : $cfgs[$i]['novu']),'');
			echo "</tbody>";
			if($i != $cn_max_addno) tabfooter();
		}
		tabfooter('bcntpldetail');
		a_guide('cntpldetail');
	}else{
		$cntpl['addnum'] = max(0,intval($fmdata['addnum']));
		if(!empty($fmdata['rsstpl'])){
			$cntpl['rsstpl'] = $fmdata['rsstpl'];
		}else unset($cntpl['rsstpl']);
		
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
		$cntpls[$tid] = $cntpl;
		cls_CacheFile::Save($cntpls,'cntpls','cntpls');
		adminlog('��Ŀ�ڵ�����');
		cls_message::show('�ڵ������޸����',axaction(6,"?entry=$entry&action=cntplsedit"));
	}
}elseif($action == 'cntplsedit'){
	backnav('cnode','cntpls');
	if(!submitcheck('bcntplsedit')){
		tabheader("�ڵ����ù���&nbsp; &nbsp; >><a href=\"?entry=$entry&action=cntpladd\" onclick=\"return floatwin('open_cntplsedit',this)\">���</a>",'cntplsedit',"?entry=$entry&action=$action",'10');
		trcategory(array('ID',array('����','txtL'),'����ҳ','����','ɾ��','����'));
		foreach($cntpls as $k => $v){
			echo "<tr class=\"txt\">".
				"<td class=\"txtC w30\">$k</td>\n".
				"<td class=\"txtL\"><input type=\"text\" size=\"30\" maxlength=\"30\" name=\"cntplsnew[$k][cname]\" value=\"$v[cname]\"></td>\n".
				"<td class=\"txtC w40\">$v[addnum]</td>\n".
				"<td class=\"txtC w40\"><input type=\"text\" size=\"4\" maxlength=\"4\" name=\"cntplsnew[$k][vieworder]\" value=\"$v[vieworder]\"></td>\n".
				"<td class=\"txtC w30\"><a onclick=\"return deltip(this,$no_deepmode)\" href=\"?entry=$entry&action=cntpldel&tid=$k\">ɾ��</a></td>\n".
				"<td class=\"txtC w30\"><a href=\"?entry=$entry&action=cntpldetail&tid=$k\" onclick=\"return floatwin('open_cntplsedit',this)\">����</a></td>\n".
				"</tr>\n";
		}
		tabfooter('bcntplsedit','�޸�');
		a_guide('cntplsedit');
	}else{
		if(isset($cntplsnew)){
			foreach($cntplsnew as $k => $v){
				$v['cname'] = trim(strip_tags($v['cname']));
				$v['cname'] = $v['cname'] ? $v['cname'] : $cntpls[$k]['cname'];
				$v['vieworder'] = max(0,intval($v['vieworder']));
				foreach(array('cname','vieworder',) as $var) $cntpls[$k][$var] = $v[$var];
			}
			cls_Array::_array_multisort($cntpls,'vieworder',1);
			cls_CacheFile::Save($cntpls,'cntpls','cntpls');
			adminlog('�༭�ڵ�����');
		}
		cls_message::show('�ڵ������޸����',"?entry=$entry&action=$action");
	}
}elseif($action == 'cntpldel' && $tid){
	backnav('cnode','cntpls');
	if(empty($cntpls[$tid])) cls_message::show('��ѡ��ڵ�����');
	deep_allow($no_deepmode,"?entry=$entry&action=cntplsedit");
	if(!submitcheck('confirm')){
		$message = "ɾ�����ָܻ���ȷ��ɾ����ѡ��Ŀ?<br><br>";
		$message .= "ȷ������>><a href=?entry=$entry&action=cntpldel&tid=$tid&confirm=ok>ɾ��</a><br>";
		$message .= "��������>><a href=?entry=$entry&action=cntplsedit>����</a>";
		cls_message::show($message);
	}
	$db->query("UPDATE {$tblprefix}cnodes SET tid='0' WHERE tid='$tid'");
	unset($cntpls[$tid]);
	
	cls_CacheFile::Save($cntpls,'cntpls','cntpls');
	cls_CacheFile::Update('cnodes');
	adminlog('ɾ���ڵ�����');
	cls_message::show('�ڵ�����ɾ���ɹ�', "?entry=$entry&action=cntplsedit");
}elseif($action == 'cnodescommon'){
	backnav('cnode','cnodescommon');
	$page = !empty($page) ? max(1, intval($page)) : 1;
	submitcheck('bfilter') && $page = 1;
	$keyword = empty($keyword) ? '' : $keyword;
	$caid = !isset($caid)? '0' : max(-4,intval($caid));
	$tid = !isset($tid)? '-1' : max(-1, intval($tid));
	$keeptid = !isset($keeptid)? '-1' : max(-1, intval($keeptid));
	$cnlevel = !isset($cnlevel) ? '0' : $cnlevel;

	$fromsql = "FROM {$tblprefix}cnodes cn force index(ename)";
	$wheresql = "cn.closed=0";
	$cnlevel && $wheresql .= " AND cn.cnlevel='$cnlevel'";
	if(!empty($caid)){
		if($caid < -1){
			$fromsql .= " INNER JOIN {$tblprefix}catalogs ca ON (ca.caid=cn.caid)";
			$wheresql .= " AND ca.level='".(abs($caid) - 2)."'";
		}elseif($caid == -1){
			$wheresql .= " AND cn.caid<>0";
		}else $wheresql .= " AND cn.caid ".multi_str(sonbycoid($caid));
	}
	$tid != '-1' && $wheresql .= " AND cn.tid='$tid'";
	$keeptid != '-1' && $wheresql .= " AND cn.keeptid='$keeptid'";
	$keyword && $wheresql .= " AND cn.ename ".sqlkw($keyword);

	$filterstr = '';
	foreach(array('keyword','caid','cnlevel',) as $k) $$k && $filterstr .= "&$k=".rawurlencode(stripslashes($$k));
	foreach(array('tid','keeptid',) as $k) $$k != -1 && $filterstr .= "&$k=".$$k;
	foreach($cotypes as $k => $v){
		if($v['sortable']){
			${"ccid$k"} = isset(${"ccid$k"}) ? max(-4,intval(${"ccid$k"})) : 0;
			if(!empty(${"ccid$k"})){
				if(${"ccid$k"} < -1){
					$fromsql .= " INNER JOIN {$tblprefix}coclass$k cc$k ON (cc$k.ccid=cn.ccid$k)";
					$wheresql .= " AND cc$k.level='".(abs(${"ccid$k"}) - 2)."'";
				}elseif(${"ccid$k"} == -1){
					$wheresql .= " AND cn.ccid$k<>0";
				}else{
					$wheresql .= " AND cn.ccid$k ".multi_str(sonbycoid(${"ccid$k"},$k));
				}
				${"ccid$k"} && $filterstr .= "&ccid$k=".${"ccid$k"};
			}
		}
	}
	$wheresql = $wheresql ? "WHERE $wheresql" : '';
	if(!submitcheck('bcnodescommon')){
		echo form_str('cnodescommon',"?entry=$entry&action=$action&page=$page");
		tabheader_e();
		echo "<tr><td colspan=\"2\" class=\"txt txtleft\" width=\"740\">";
		echo "<input class=\"text\" name=\"keyword\" type=\"text\" value=\"$keyword\" size=\"8\" style=\"vertical-align: middle;\">&nbsp; ";
		echo "<select name=\"cnlevel\">".makeoption(array('0'=>'����','1'=>'����','2'=>'����','3'=>'����','4'=>'����'),$cnlevel)."</select>&nbsp; ";
		$arr = array('-1' => '�ڵ�����','0' => '��δ����',);foreach($cntpls as $k => $v) $arr[$k] = $k.'-'.$v['cname'];
		echo "<select name=\"tid\">".makeoption($arr,$tid)."</select>&nbsp; ";
		$arr = array('-1' => '���ƽڵ�?','1' => '��','0' => '��',);
		echo "<select name=\"keeptid\">".makeoption($arr,$keeptid)."</select>&nbsp; ";
		echo "<select name=\"caid\">".makeoption(array('0' => '������Ŀ','-1' => 'ȫ��','-2' => '����','-3' => '����','-4' => '����',) + cls_catalog::ccidsarr(0),$caid)."</select>&nbsp; ";
		foreach($cotypes as $k => $v){
			if($v['sortable']) echo "<select name=\"ccid$k\">".makeoption(array('0' => $v['cname'],'-1' => 'ȫ��','-2' => '����','-3' => '����','-4' => '����',) + cls_catalog::ccidsarr($k),${"ccid$k"})."</select>&nbsp; ";
		}
		echo strbutton('bfilter','ɸѡ');
		echo "</td></tr>";
		tabfooter();

		$TitleStr = "��Ŀ�ڵ��б�";
		$TitleStr .= "&nbsp; &nbsp; <input class=\"checkbox\" type=\"checkbox\" name=\"select_all\" value=\"1\">&nbsp;ȫѡ����ҳ����";
		$TitleStr .= "&nbsp; &nbsp; <a href=\"?entry=$entry&action=clearnodes&bsubmit=1\" onclick=\"return floatwin('open_fnodes',this)\">>>������нڵ㲢�ؽ�</a>";
		tabheader($TitleStr,'','',12);
		$cy_arr = array("<input class=\"checkbox\" type=\"checkbox\" name=\"chkall\" onclick=\"checkall(this.form, 'selectid', 'chkall')\">",array('�ڵ�����','txtL'),array('�ڵ�ʶ���ִ�','txtL'),array('�ڵ�����','txtL'),'����ҳ',array('�鿴','txtL'),);
		$cy_arr[] = '����';
		trcategory($cy_arr);

		$pagetmp = $page;
		do{
			$query = $db->query("SELECT cn.* $fromsql $wheresql ORDER BY cnid ASC LIMIT ".(($pagetmp - 1) * $atpp).",$atpp");
			$pagetmp--;
		} while(!$db->num_rows($query) && $pagetmp);
		while($cnode = $db->fetch_array($query)){
			$cnode = LoadCnodeConfig($cnode);
			$catalogstr = $cnode['ename'];
			cls_url::view_cnurl($cnode['ename'],$cnode);
			$cnamestr = cls_node::cnode_cname($cnode['ename']);
			$addnum = empty($cnode['addnum']) ? 0 : $cnode['addnum'];
			$cnstplstr = (empty($cntpls[$cnode['tid']]['cname']) ? '-' : "$cnode[tid]-".$cntpls[$cnode['tid']]['cname']).($cnode['keeptid'] ? ' (����)' : '');
			$lookstr = '';
			for($i = 0;$i <= $addnum;$i ++) $lookstr .= "<a href=\"".$cnode['indexurl'.($i ? $i : '')]."\" target=\"_blank\">".($i ? $i : '��ҳ')."</a>&nbsp; ";
			echo "<tr class=\"txt\"><td class=\"txtC\"><input class=\"checkbox\" type=\"checkbox\" name=\"selectid[$cnode[cnid]]\" value=\"$cnode[cnid]\">\n";
			echo "<td class=\"txtL\">$cnamestr</td>\n";
			echo "<td class=\"txtL\">$catalogstr</td>\n";
			echo "<td class=\"txtL\">$cnstplstr</td>\n";
			echo "<td class=\"txtC\">$addnum</td>\n";
			echo "<td class=\"txtL\">$lookstr</td>\n";
			echo "<td class=\"txtC\"><a href=\"?entry=$entry&action=cnodedetail&cnid=$cnode[cnid]\" onclick=\"return floatwin('open_cnodedetail',this)\">�༭</a></td></tr>\n";
		}
		tabfooter();
		echo multi($db->result_one("SELECT count(*) $fromsql $wheresql"), $atpp, $page, "?entry=$entry&action=$action$filterstr");

		tabheader('��������');
		$s_arr = array();
		$s_arr['delete'] = 'ɾ���ڵ�';
		$s_arr['keeptid'] = '��Ϊ���ƽڵ�';
		$s_arr['un_keeptid'] = 'ȡ�����ƽڵ�';
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
		$arr = array();foreach($cntpls as $k => $v) $arr[$k] = $k.'-'.$v['cname'];
		trbasic("<input class=\"checkbox\" type=\"checkbox\" name=\"cndeal[cntpl]\" value=\"1\">&nbsp;���ýڵ�����",'cncntpl',makeoption($arr,0),'select',array('guide'=>'�ڵ����ð����ڵ�������ģ�壬��̬�����ʽ�����á�'));
		$ptypearr = array();
		for($i = 0;$i <= $cn_max_addno;$i ++) $ptypearr[$i] = $i ? '��'.$i : '��ҳ';
		trbasic("<input class=\"checkbox\" type=\"checkbox\" name=\"cndeal[static]\" value=\"1\">&nbsp;�������ɾ�̬",'',"<input class=\"checkbox\" type=\"checkbox\" name=\"mchkall\" onclick=\"checkall(this.form,'ptypes','mchkall')\">ȫѡ &nbsp;".makecheckbox('ptypes[]',$ptypearr),'',array('guide'=>'����̬����ʱ��Ч'));
		tabfooter('bcnodescommon');
		a_guide('cnodesedit');
	}
	else{
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

			$selectid = $cnstrarr = array();
			$npage = empty($npage) ? 1 : $npage;
			if(empty($pages)) $pages = @ceil($db->result_one("SELECT count(*) $fromsql $wheresql") / $atpp);
			if($npage <= $pages){
				$fromstr = empty($fromid) ? "" : "cn.cnid>$fromid";
				$nwheresql = !$wheresql ? ($fromstr ? "WHERE $fromstr" : "") : ($wheresql.($fromstr ? " AND " : "").$fromstr);
				$query = $db->query("SELECT cn.cnid,cn.ename $fromsql $nwheresql ORDER BY cn.cnid ASC LIMIT 0,$atpp");
				while($item = $db->fetch_array($query)) $selectid[] = $item['cnid'];
			}
			if(empty($selectid)) cls_message::show('��ѡ��ڵ�',"?entry=$entry&action=$action&page=$page$filterstr");
		}
		if(!empty($cndeal['delete'])){
			$query = $db->query("SELECT * FROM {$tblprefix}cnodes WHERE cnid ".multi_str($selectid));
			while($r = $db->fetch_array($query)){
				$r = LoadCnodeConfig($r);
				for($i = 0;$i <= @$r['addnum'];$i ++) m_unlink(cls_url::m_parseurl(cls_node::cn_format($r['ename'],$i,$r),array('addno' => $i)));
			}
			$db->query("DELETE FROM {$tblprefix}cnodes WHERE cnid ".multi_str($selectid), 'UNBUFFERED');
		}else{
			if(!empty($cndeal['keeptid'])){
				$db->query("UPDATE {$tblprefix}cnodes SET keeptid='1' WHERE cnid ".multi_str($selectid));
			}elseif(!empty($cndeal['un_keeptid'])){
				$db->query("UPDATE {$tblprefix}cnodes SET keeptid='0' WHERE cnid ".multi_str($selectid));
			}
			if(!empty($cndeal['cntpl'])){
				$cncntpl = empty($cncntpl) ? 0 : (empty($cntpls[$cncntpl]) ? 0 : $cncntpl);
				$db->query("UPDATE {$tblprefix}cnodes SET tid='$cncntpl' WHERE cnid ".multi_str($selectid));
			}
			if(!empty($cndeal['static']) && $ptypes){
				$query = $db->query("SELECT * FROM {$tblprefix}cnodes WHERE cnid ".multi_str($selectid));
				while($r = $db->fetch_array($query)){
					foreach($ptypes as $k){
						cls_CnodePage::Create(array('cnstr' => $r['ename'],'addno' => $k,'inStatic' => true));
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
				$transtr .= "&bcnodescommon=1";
				$transtr .= "&fromid=$fromid";
				cls_message::show("�ļ��������ڽ�����...<br>�� $pages ҳ�����ڴ���� $npage ҳ<br><br><a href=\"?entry=$entry&action=$action&page=$page$filterstr\">>>��ֹ��ǰ����</a>","?entry=$entry&action=$action&page=$page$filterstr$transtr$parastr&dealstr=$dealstr");
			}
		}
		cls_CacheFile::Update('cnodes');
		adminlog('�ڵ�������','�ڵ��б�������');
		cls_message::show('�ڵ�������',"?entry=$entry&action=$action&page=$page$filterstr");
	}
}elseif($action == 'cnodedetail' && $cnid){
	echo "<title>�ڵ�����</title>";
	$forward = empty($forward) ? M_REFERER : $forward;
	$cnode = $db->fetch_one("SELECT * FROM {$tblprefix}cnodes WHERE cnid=$cnid");
	$cnode = LoadCnodeConfig($cnode);
	if(!submitcheck('bcnodedetail')){
		tabheader('�ڵ���ϸ����','cnodedetail',"?entry=$entry&action=$action&cnid=$cnid&forward=".urlencode($forward));
		trbasic('�ڵ�����','',cls_node::cnode_cname($cnode['ename']),'');
		trbasic('�ڵ����','cnodenew[alias]',$cnode['alias']);
		trbasic('ָ���ڵ�����','cnodenew[appurl]',$cnode['appurl'],'text',array('guide'=>'վ��Url��վ��Url����','w'=>50));
		$arr = array('0' => '������');foreach($cntpls as $k => $v) $arr[$k] = $k.'-'.$v['cname'];
		trbasic('���ýڵ�����','cnodenew[tid]',makeoption($arr,$cnode['tid']),'select',array('guide'=>'�ڵ����ð����ڵ�������ģ�壬��̬�����ʽ�����á�'));
		trbasic('��Ϊ���ƽڵ�?','cnodenew[keeptid]',$cnode['keeptid'],'radio',array('guide'=>'���ƽڵ��ڽڵ���ɷ����� \'��ȫ�����½ڵ�����\' ���������У��ڵ����ý������������ã��������¡�'));
		tabfooter();

		tabheader('�ڵ�������� &nbsp;- &nbsp;'.(empty($cntpls[$cnode['tid']]['cname']) ? '��δ����' : "<a href=\"?entry=$entry&action=cntpldetail&tid=$cnode[tid]\" onclick=\"return floatwin('open_cntplsedit',this)\">>>".$cntpls[$cnode['tid']]['cname']."</a>"));
		trbasic('����ҳ����','',empty($cnode['addnum']) ? '0' : $cnode['addnum'],'');
		$arr = cls_mtpl::mtplsarr('xml');
		trbasic('�ڵ�RSSģ��','',empty($cnode['rsstpl']) ? 'δ����' : $cnode['rsstpl'].' &nbsp;- &nbsp;'.@$arr[$cnode['rsstpl']],'');
		$mtplsarr = cls_mtpl::mtplsarr('cindex');
		for($i = 0;$i <= @$cnode['addnum'];$i ++){
			$pvar = $i ? '����ҳ'.$i : '��ҳ';
			trbasic($pvar.'ģ��','',empty($cnode['cfgs'][$i]['tpl']) ? 'δ����' : $cnode['cfgs'][$i]['tpl'].' &nbsp;- &nbsp;'.@$mtplsarr[$cnode['cfgs'][$i]['tpl']],'');
			trbasic($pvar.'��̬�����ʽ','',empty($cnode['cfgs'][$i]['url']) ? '��ϵͳ������' : $cnode['cfgs'][$i]['url'],'',array('guide'=>!$i ? '{$cndir}ϵͳĬ�ϱ���·����{$page}��ҳҳ�롣': ''));
			trbasic($pvar.'�Ƿ����ɾ�̬','',empty($cnode['cfgs'][$i]['static']) ? '��ϵͳ������' : '���ֶ�̬','');
			trbasic($pvar.'��̬��������','',empty($cnode['cfgs'][$i]['period']) ? '��ϵͳ������' : $cnode['cfgs'][$i]['period'].'(����)','');
			trbasic($pvar.'���⾲̬URL','',empty($cnode['cfgs'][$i]['novu']) ? '��ϵͳ������' : '�ر����⾲̬','');
		}
		tabfooter('bcnodedetail');
		a_guide('cnodedetail');
	}else{
		$cnodenew['alias'] = trim(strip_tags($cnodenew['alias']));
		$cnodenew['appurl'] = trim($cnodenew['appurl']);
		$cnodenew['keeptid'] = empty($cnodenew['keeptid']) ? 0 : 1;
		$db->query("UPDATE {$tblprefix}cnodes SET alias='$cnodenew[alias]',appurl='$cnodenew[appurl]',tid='$cnodenew[tid]',keeptid='$cnodenew[keeptid]' WHERE cnid=$cnid");
		adminlog('��ϸ��Ŀ�ڵ�');
		cls_CacheFile::Update('cnodes');
		cls_message::show('�ڵ��������',axaction(6,$forward));
	}
}

function modify_cnconfig(&$cncfg,$coid = 0,$ccids = array(),$mode = 0){
	global $db,$tblprefix,$cnconfigs;
	if(empty($cncfg)) return false;
	$configs = $cncfg['configs'];
	if(($cfg = @$configs[$coid]) && (@$cfg['mode'] == -1)){
		$ids = empty($cfg['ids']) ? array() : explode(',',$cfg['ids']);
		$ids = !$mode ? $ccids : ($mode == 1 ? array_filter(array_merge($ids,$ccids)) : array_diff($ids,$ccids));
		$configs[$coid]['ids'] = !$ids ? '' : implode(',',$ids);
		$cnconfigs[$cncfg['cncid']]['configs'] = $configs;
		return true;
	}
	return false;
}

?>
