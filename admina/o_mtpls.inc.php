<?
(!defined('M_COM') || !defined('M_ADMIN')) && exit('No Permission');
aheader();
if($re = $curuser->NoBackFunc('tpl')) cls_message::show($re);
foreach(array('cotypes','bnames','mtpls','o_mtpls','channels',) as $k) $$k = cls_cache::Read($k);

empty($action) && $action = 'mtplsedit';
$tpclasses = cls_mtpl::ClassArray(1);
$true_tpldir = cls_tpl::TemplateTypeDir('tpl');
mmkdir($true_tpldir);
if($action == 'mtpladd'){
	echo "<title>����ֻ���ģ��</title>";
	if(!submitcheck('bmtpladd') && !submitcheck('bmtplsave')){
		$tpclass = empty($tpclass) ? 'index' : $tpclass;
		if(submitcheck('bmtplsearch')){
			$mtplstmp = findfiles($true_tpldir);
			$enamearr = array_merge(array_keys($mtpls),array_keys($o_mtpls));
			foreach($mtplstmp as $k => $tplname){
				if(in_array($tplname,$enamearr)) unset($mtplstmp[$k]);
			}
			empty($mtplstmp) && cls_message::show('û����������Ҫ����ģ���ļ�', "?entry=$entry&action=mtpladd");
			$in_search = 1;
		}
		tabheader("����ֻ���ģ��&nbsp;&nbsp;&nbsp;&nbsp;<input class=\"button\" type=\"submit\" name=\"bmtplsearch\" value=\"�Զ�����\">",'mtpladd',"?entry=$entry&action=$action&tpclass=$tpclass");
		trbasic('ģ������','mtpladd[cname]');
		trbasic('ģ������','mtpladd[tpclass]',makeoption($tpclasses,$tpclass),'select');
		trbasic('ģ���ļ�','mtpladd[tplname]','','text',array('guide' => "�ļ�����ֻ���������ĸ�����֡��»���(-_)����(.)���ַ�,����htm��htmlΪ��չ��"));
		tabfooter('bmtpladd','���');
		if(!empty($in_search)){
			tabheader('�ֻ���ģ��������','mtplsave',"?entry=$entry&action=$action&tpclass=$tpclass",'4');
			trcategory(array('<input class="checkbox" type="checkbox" name="chkall" onclick="checkall(this.form)">ȫѡ',array('ģ���ļ�','txtL'),'����ģ������','����ģ������'));
			foreach($mtplstmp as $tplname){
				if(_08_FilesystemFile::CheckFileName($tplname)) continue;
				echo "<tr class=\"txt\">".
					"<td class=\"txtC w45\"><input class=\"checkbox\" type=\"checkbox\" name=\"selectid[$tplname]\" value=\"$tplname\">\n".
					"<td class=\"txtL\">$tplname</td>\n".
					"<td class=\"txtC\"><input type=\"text\" size=\"30\" name=\"mtplsnew[$tplname][cname]\" value=\"\"></td>\n".
					"<td class=\"txtC w150\"><select style=\"vertical-align: middle;\" name=\"mtplsnew[$tplname][tpclass]\">".makeoption($tpclasses,$tpclass)."</select></td></tr>";
			}
			tabfooter('bmtplsave','���');
		}
		a_guide('mtpladd');
	}elseif(submitcheck('bmtpladd')){
		if(empty($mtpladd['cname'])) cls_message::show('������ģ������',M_REFERER);
		if($re = _08_FilesystemFile::CheckFileName($mtpladd['tplname'])) cls_message::show($re,M_REFERER);
		$enamearr = array_merge(array_keys($mtpls),array_keys($o_mtpls));
		if(in_array($mtpladd['tplname'], $enamearr)) cls_message::show('ҳ��ģ���ظ�����',M_REFERER);
		if(!is_file($true_tpldir.'/'.$mtpladd['tplname'])){
			if(@!touch($true_tpldir.'/'.$mtpladd['tplname'])) cls_message::show('ģ���ļ����ʧ��!',M_REFERER);
		}
		$o_mtpls[$mtpladd['tplname']] = array('cname' => stripslashes($mtpladd['cname']),'tpclass' => $mtpladd['tpclass']);
		cls_CacheFile::Save($o_mtpls,'o_mtpls','o_mtpls');
		adminlog('����ֻ���ģ��');
		cls_message::show('ģ��������',axaction(6,"?entry=$entry&action=mtplsedit&tpclass=$mtpladd[tpclass]"));
	}elseif(submitcheck('bmtplsave')){
		if(!empty($selectid)){
			foreach($selectid as $tplname){
				if(_08_FilesystemFile::CheckFileName($tplname)) continue;
				if(!empty($mtplsnew[$tplname]['cname']) && !empty($mtplsnew[$tplname]['tpclass'])){
					$cname = $mtplsnew[$tplname]['cname'];
					$tpclass = $mtplsnew[$tplname]['tpclass'];
					$o_mtpls[$tplname] = array('cname' => stripslashes($mtplsnew[$tplname]['cname']),'tpclass' => $mtplsnew[$tplname]['tpclass']);
				}
			}
		}
		cls_CacheFile::Save($o_mtpls,'o_mtpls','o_mtpls');
		adminlog('����ֻ���ģ��');
		cls_message::show('ģ��������',axaction(6,"?entry=$entry&action=mtplsedit&tpclass=$tpclass"));
	}
}elseif($action == 'mtplsedit'){
	echo "<title>�ֻ���ģ��</title>";
	empty($tpclass) && $tpclass = 'index';
	backnav('mobile','mtpls');
	$tpclassarr = array();
	foreach($tpclasses as $k => $v){
		$tpclassarr[] = $tpclass == $k ? "<b>-$v-</b>" : "<a href=\"?entry=$entry&action=$action&tpclass=$k\">$v</a>";
	}
	echo tab_list($tpclassarr,10,0);
	if(!submitcheck('bmtplsedit')){
		if($tplbase = cls_env::GetG('templatebase')){ $tips = "<li>����ʾ����ǰ������չģ��,�̳��Ի���ģ��[$tplbase]������ģ�治��ɾ��,�ɴӻ���ģ��[��չ]����ǰģ�塣</li>"; echo "<div style='color:red'>$tips</div>"; }
		$_add = empty($templatebase) ? "&nbsp; &nbsp; <a href=\"?entry=$entry&action=mtpladd&tpclass=$tpclass\" onclick=\"return floatwin('open_mtplsedit',this)\">>>���</a>" : '';
		tabheader('�ֻ���ģ�� - '.$tpclasses[$tpclass].$_add,'mtplsedit',"?entry=$entry&action=mtplsedit&tpclass=$tpclass",'9');
		$_copy = empty($templatebase) ? '����' : '��չ';
		trcategory(array('���',array('ģ������','txtL'),array('ģ���ļ�','txtL'),'<input class="checkbox" type="checkbox" name="chkall" onclick="deltip(this,0,checkall,this.form)">ɾ?',$_copy,'����'));
		$ii = 0;
		foreach($o_mtpls as $k => $v){
			if($tpclass == $v['tpclass']){
				echo "<tr class=\"txt\">".
					"<td class=\"txtC w40\">".++$ii."</td>\n".
					"<td class=\"txtL\"><input type=\"text\" size=\"25\" name=\"mtplsnew[$k][cname]\" value=\"".mhtmlspecialchars($v['cname'])."\"></td>\n";
				if(empty($templatebase)){
					echo "<td class=\"txtL\">$k</td>\n".
						"<td class=\"txtC w40\"><input class=\"checkbox\" type=\"checkbox\" name=\"delete[$k]\" value=\"$k\" onclick=\"deltip()\">\n".
						"<td class=\"txtC w30\"><a href=\"?entry=$entry&action=mtplcopy&tplname=$k\" onclick=\"return floatwin('open_mtplsedit',this)\">����</a></td>\n".
						"<td class=\"txtC w30\"><a href=\"?entry=$entry&action=mtpldetail&tplname=$k\" onclick=\"return floatwin('open_mtplsedit',this)\">�༭</a></td></tr>\n";
				}elseif(!empty($templatebase)&&!file_tplexists($k)){
					//����չģ��
					echo "<td class=\"txtL\">$k</td>\n".
						"<td class=\"txtC w40 tips1\">--</td>\n".
						"<td class=\"txtC w30\"><a href=\"?entry=$entry&action=basic2extend&tplname=$k\" onclick=\"return floatwin('open_mtplsedit',this)\">��չ</a></td>\n".
						"<td class=\"txtC w30\"><a href=\"?entry=$entry&action=mtpldetail&tplname=$k&isbase=1\" onclick=\"return floatwin('open_mtplsedit',this)\">�༭</a></td></tr>\n";
				}elseif(!empty($templatebase)&&file_tplexists($k)){
					//����չģ��
					echo "<td class=\"txtL\">$k</td>\n".
						"<td class=\"txtC w40\"><input class=\"checkbox\" type=\"checkbox\" name=\"delete[$k]\" value=\"$k\" onclick=\"deltip()\">\n".
						"<td class=\"txtC w30 tips1\">��չ</td>\n".
						"<td class=\"txtC w30\"><a href=\"?entry=$entry&action=mtpldetail&tplname=$k\" onclick=\"return floatwin('open_mtplsedit',this)\">�༭</a></td></tr>\n";
				}
			}
		}
		tabfooter('bmtplsedit','�޸�');
		a_guide("mtplsedit$tpclass");
	}else{
		if(!empty($delete)){
			foreach($delete as $k){
				if(!empty($templatebase)){
					$tname = cls_tpl::rel_path($k,'dir');
					file_exists($tname) && unlink($tname);
				}else {
					unset($mtplsnew[$k], $o_mtpls[$k]);
				}
			}
		}
		if(!empty($mtplsnew)){
			foreach($mtplsnew as $k => $v){
				$v['cname'] = empty($v['cname']) ? $mtpls[$k]['cname'] : $v['cname'];
				if($v['cname'] != $o_mtpls[$k]['cname']) $o_mtpls[$k]['cname'] = stripslashes($v['cname']);
			}
		}
		cls_CacheFile::Save($o_mtpls,'o_mtpls','o_mtpls');
		adminlog('�༭�ֻ���ģ������б�');
		cls_message::show('ģ���޸����',"?entry=$entry&action=mtplsedit&tpclass=$tpclass");
	}
}
elseif($action == 'mtpldetail' && $tplname){
	echo "<title>ҳ��ģ��༭</title>";
	if($re = _08_FilesystemFile::CheckFileName($tplname)) cls_message::show($re);
	if(!($mtpl = $o_mtpls[$tplname])) cls_message::show('ָ����ģ�岻����');
	$forward = empty($forward) ? M_REFERER : $forward;
	$forwardstr = '&forward='.rawurlencode($forward);
	$isbasestr = empty($isbase) ? '' : '&isbase=1';
	if(!submitcheck('bmtpldetail')){
		$template = cls_tpl::load($tplname,0);
		tabheader("ģ������ - {$mtpl['cname']} - {$tplname}",'mtpldetail',"?entry=$entry&action=mtpldetail&tplname=$tplname$isbasestr$forwardstr");
		trbasic('ģ�����','mtplnew[tpclass]',makeoption($tpclasses,$mtpl['tpclass']),'select');
		templatebox('ҳ��ģ��','templatenew',$template,30,110);
		tabfooter('bmtpldetail');
		a_guide('mtpldetail');
	}
	else{
		@str2file(stripslashes($templatenew),cls_tpl::rel_path($tplname,'get'));
		$o_mtpls[$tplname]['tpclass'] = $mtplnew['tpclass'];
		cls_CacheFile::Save($o_mtpls,'o_mtpls','o_mtpls');
		adminlog('��ϸ�޸��ֻ���ģ��');
		cls_message::show('ģ���޸����',axaction(6,$forward));
	}
}
elseif($action == 'mtplcopy' && $tplname){
	echo "<title>����ҳ��ģ��</title>";
	if($re = _08_FilesystemFile::CheckFileName($tplname)) cls_message::show($re);
	if(!($mtpl = $o_mtpls[$tplname])) cls_message::show('ָ����ģ�岻����');
	if(!submitcheck('bmtplcopy')){
		!is_file($true_tpldir.'/'.$tplname) && cls_message::show('ָ����Դģ���ļ�������');
		tabheader('�����ֻ���ģ��','mtplcopy',"?entry=$entry&action=mtplcopy&tplname=$tplname");
		trbasic('ģ������','mtpladd[cname]');
		trbasic('ģ�����','mtpladd[tpclass]',makeoption($tpclasses,$mtpl['tpclass']),'select');
		trbasic('Դģ���ļ�','',$tplname,'');
		trbasic('ģ���ļ����Ϊ','mtpladd[tplname]','','text',array('guide' => "�ļ�����ֻ���������ĸ�����֡��»���(-_)����(.)���ַ�,����htm��htmlΪ��չ��"));
		tabfooter('bmtplcopy');
		a_guide('mtplcopy');
	}else{
		if(empty($mtpladd['cname'])) cls_message::show('������ģ������',M_REFERER);
		if($re = _08_FilesystemFile::CheckFileName($mtpladd['tplname'])) cls_message::show($re);
		$mtplsnew = findfiles($true_tpldir);
		in_array($mtpladd['tplname'],$mtplsnew) && cls_message::show('ָ����ģ���ļ������ظ�',M_REFERER);
		!copy($true_tpldir.'/'.$tplname,$true_tpldir.'/'.$mtpladd['tplname']) && cls_message::show('ģ�帴��ʧ��',M_REFERER);
		$o_mtpls[$mtpladd['tplname']] = array('cname' => stripslashes($mtpladd['cname']),'tpclass' => $mtpladd['tpclass']);
		cls_CacheFile::Save($o_mtpls,'o_mtpls','o_mtpls');
		adminlog('�����ֻ���ģ��');
		cls_message::show('ģ�帴�����',axaction(6,"?entry=$entry&action=mtplsedit"));
	}
}elseif($action == 'basic2extend' && $tplname){
    $msg = rtag_basic2extend($tplname) ? 'ģ����չ���' : '����ģ��ԭ�ļ�������';
    cls_message::show($msg,axaction(6,"?entry=$entry&action=mtplsedit"));
}