<?
(!defined('M_COM') || !defined('M_ADMIN')) && exit('No Permission');
aheader();
if($re = $curuser->NoBackFunc('tpl')) cls_message::show($re);
foreach(array('cotypes','bnames','mtpls','o_mtpls','channels',) as $k) $$k = cls_cache::Read($k);
empty($action) && $action = 'mtplsedit';
$tpclasses = cls_mtpl::ClassArray();
$true_tpldir = cls_tpl::TemplateTypeDir('tpl');
mmkdir($true_tpldir);
if($action == 'mtpladd'){
	echo "<title>��ӳ���ģ��</title>";
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
		tabheader("��ӳ���ģ��&nbsp;&nbsp;&nbsp;&nbsp;<input class=\"button\" type=\"submit\" name=\"bmtplsearch\" value=\"�Զ�����\">",'mtpladd',"?entry=$entry&action=$action&tpclass=$tpclass");
		trbasic('ģ������','mtpladd[cname]');
		trbasic('ģ������','mtpladd[tpclass]',makeoption($tpclasses,$tpclass),'select');
		if($tpclass == 'archive') trbasic('���ĵ�����ʱ��ѡ','mtpladd[chid]',makeoption(array(0 => '��ѡ��') + cls_channel::chidsarr(1),0),'select',array('guide'=>'Ϊ�����ĵ�ָ������ҳģ��ʱ��ֻ��������ѡģ�͵��ĵ��ſ���ѡ���ģ��',));
		trbasic('ģ���ļ�','mtpladd[tplname]','','text',array('guide' => "�ļ�����ֻ���������ĸ�����֡��»���(-_)����(.)���ַ�,����htm��htmlΪ��չ��"));
		tabfooter('bmtpladd','���');
		if(!empty($in_search)){
			tabheader('����ģ��������','mtplsave',"?entry=$entry&action=$action&tpclass=$tpclass",'4');
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
		echo "<script>\$('select').mousedown(function(event){event.stopPropagation();});</script>";
		a_guide('mtpladd');
	}elseif(submitcheck('bmtpladd')){
		if(empty($mtpladd['cname'])) cls_message::show('������ģ������',M_REFERER);
		if($re = _08_FilesystemFile::CheckFileName($mtpladd['tplname'])) cls_message::show($re,M_REFERER);
		$enamearr = array_merge(array_keys($mtpls),array_keys($o_mtpls));
		if(in_array($mtpladd['tplname'], $enamearr)) cls_message::show('ҳ��ģ���ظ�����',M_REFERER);
		if(!is_file($true_tpldir.$mtpladd['tplname'])){
			if(@!touch($true_tpldir.$mtpladd['tplname'])) cls_message::show('ģ���ļ����ʧ��!',M_REFERER);
		}
		$mtpls[$mtpladd['tplname']] = array('cname' => stripslashes($mtpladd['cname']),'tpclass' => $mtpladd['tpclass']);
		if($mtpladd['tpclass'] == 'archive'){
			if($mtpladd['chid'] = max(0,intval($mtpladd['chid']))){
				$mtpls[$mtpladd['tplname']]['chid'] = $mtpladd['chid'];
			}else unset($mtpls[$mtpladd['tplname']]['chid']);
		}
		cls_CacheFile::Save($mtpls,'mtpls','mtpls');
		adminlog('��ӳ���ģ��');
		cls_message::show('ģ��������',axaction(6,"?entry=$entry&action=mtplsedit&tpclass=$mtpladd[tpclass]"));
	}elseif(submitcheck('bmtplsave')){
		if(!empty($selectid)){
			foreach($selectid as $tplname){
				if(_08_FilesystemFile::CheckFileName($tplname)) continue;
				if(!empty($mtplsnew[$tplname]['cname']) && !empty($mtplsnew[$tplname]['tpclass'])){
					$cname = $mtplsnew[$tplname]['cname'];
					$tpclass = $mtplsnew[$tplname]['tpclass'];
					$mtpls[$tplname] = array('cname' => stripslashes($mtplsnew[$tplname]['cname']),'tpclass' => $mtplsnew[$tplname]['tpclass']);
				}
			}
		}
		cls_CacheFile::Save($mtpls,'mtpls','mtpls');
		adminlog('��ӳ���ģ��');
		cls_message::show('ģ��������',axaction(6,"?entry=$entry&action=mtplsedit&tpclass=$tpclass"));
	}
}
elseif($action == 'mtplsedit'){
	echo "<title>����ҳ��ģ��</title>";
	empty($tpclass) && $tpclass = 'index';
	backnav('tpl','retpl');
	$tpclassarr = array();
	foreach($tpclasses as $k => $v){
		$tpclassarr[] = $tpclass == $k ? "<b>-$v-</b>" : "<a href=\"?entry=$entry&action=$action&tpclass=$k\">$v</a>";
	}
	echo tab_list($tpclassarr,10,0);
	if(!submitcheck('bmtplsedit')){
		if($tplbase = cls_env::GetG('templatebase')){ $tips = "<li>����ʾ����ǰ������չģ��,�̳��Ի���ģ��[$tplbase]������ģ�治��ɾ��,�ɴӻ���ģ��[��չ]����ǰģ�塣</li>"; echo "<div style='color:red'>$tips</div>"; }
		$_add = empty($templatebase) ? "&nbsp; &nbsp; <a href=\"?entry=$entry&action=mtpladd&tpclass=$tpclass\" onclick=\"return floatwin('open_mtplsedit',this)\">>>���</a>" : '';
		tabheader('����ҳ�� - '.$tpclasses[$tpclass].$_add,'mtplsedit',"?entry=$entry&action=mtplsedit&tpclass=$tpclass",'9');
		$_copy = empty($templatebase) ? '����' : '��չ';
		trcategory(array('���',array('ģ������','txtL'),$tpclass == 'archive' ? array('���ĵ�����ʱ��ѡ','txtL') : '',array('ģ���ļ�','txtL'),'<input class="checkbox" type="checkbox" name="chkall" onclick="deltip(this,0,checkall,this.form)">ɾ?',$_copy,'����'));
		$ii = 0;
		foreach($mtpls as $k => $v){
			if($tpclass == $v['tpclass']){
				echo "<tr class=\"txt\">".
					"<td class=\"txtC w40\">".++$ii."</td>\n".
					"<td class=\"txtL\"><input type=\"text\" size=\"25\" name=\"mtplsnew[$k][cname]\" value=\"".mhtmlspecialchars($v['cname'])."\"></td>\n";
				if($tpclass == 'archive'){
					echo "<td class=\"txtL\">".(empty($channels[@$v['chid']]) ? '-' : $v['chid'].'-'.$channels[$v['chid']]['cname'])."</td>\n";
				}
				if(empty($templatebase)){
					echo "<td class=\"txtL\">$k</td>\n".
						"<td class=\"txtC w40\"><input class=\"checkbox\" type=\"checkbox\" name=\"delete[$k]\" value=\"$k\" onclick=\"deltip()\"></td>\n".
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
						"<td class=\"txtC w40\"><input class=\"checkbox\" type=\"checkbox\" name=\"delete[$k]\" value=\"$k\" onclick=\"deltip()\"></td>\n".
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
					unset($mtplsnew[$k], $mtpls[$k]);
				}
			}
		}
		if(!empty($mtplsnew)){
			foreach($mtplsnew as $k => $v){
				$v['cname'] = empty($v['cname']) ? $mtpls[$k]['cname'] : $v['cname'];
				if($v['cname'] != $mtpls[$k]['cname']) $mtpls[$k]['cname'] = stripslashes($v['cname']);
			}
		}
		cls_CacheFile::Save($mtpls,'mtpls','mtpls');
		adminlog('�༭����ģ������б�');
		cls_message::show('ģ���޸����',"?entry=$entry&action=mtplsedit&tpclass=$tpclass");
	}
}
elseif($action == 'mtpldetail' && $tplname){
	echo "<title>ҳ��ģ��༭</title>";
	if($re = _08_FilesystemFile::CheckFileName($tplname)) cls_message::show($re);
	if(!($mtpl = $mtpls[$tplname])) cls_message::show('ָ����ģ�岻����');
	$forward = empty($forward) ? M_REFERER : $forward;
	$forwardstr = '&forward='.rawurlencode($forward);
	$isbasestr = empty($isbase) ? '' : '&isbase=1';
	if(!submitcheck('bmtpldetail')){
		$template = cls_tpl::load($tplname,0);
		tabheader("ģ������ - {$mtpl['cname']} - {$tplname}",'mtpldetail',"?entry=$entry&action=mtpldetail&tplname=$tplname$isbasestr$forwardstr");
		trbasic('ģ�����','mtplnew[tpclass]',makeoption($tpclasses,$mtpl['tpclass']),'select');
		if($mtpl['tpclass'] == 'archive') trbasic('���ĵ�����ʱ��ѡ','mtplnew[chid]',makeoption(array(0 => '��ѡ��') + cls_channel::chidsarr(1),empty($mtpl['chid']) ? 0 : $mtpl['chid']),'select',array('guide'=>'ָ�������ĵ�������ҳģ��ʱ��ֻ����ѡģ�͵��ĵ��ſ���ѡ���ģ�塣',));
		templatebox('ҳ��ģ��','templatenew',$template,28,110);
		tabfooter('bmtpldetail');
		a_guide('mtpldetail');
	}
	else{
		// �����Ƿ�����չģ��,���ﶼ��cls_tpl::rel_pathĬ�϶�λ����ǰģ��Ŀ¼; ���url��isbase=1��λ������ģ��
		@str2file(stripslashes($templatenew),cls_tpl::rel_path($tplname,'get'));
		$mtpls[$tplname]['tpclass'] = $mtplnew['tpclass'];
		if($mtplnew['tpclass'] == 'archive'){
			if($mtplnew['chid'] = max(0,intval($mtplnew['chid']))){
				$mtpls[$tplname]['chid'] = $mtplnew['chid'];
			}else unset($mtpls[$tplname]['chid']);
		}
		cls_CacheFile::Save($mtpls,'mtpls','mtpls');
		adminlog('��ϸ�޸ĳ���ģ��');
		cls_message::show('ģ���޸����',axaction(6,$forward));
	}
}
elseif($action == 'mtplcopy' && $tplname){
	echo "<title>����ҳ��ģ��</title>";
	if($re = _08_FilesystemFile::CheckFileName($tplname)) cls_message::show($re);
	if(!($mtpl = $mtpls[$tplname])) cls_message::show('ָ����ģ�岻����');
	if(!submitcheck('bmtplcopy')){
		!is_file($true_tpldir.$tplname) && cls_message::show('ָ����Դģ���ļ�������');
		tabheader('���Ƴ���ҳ��ģ��','mtplcopy',"?entry=$entry&action=mtplcopy&tplname=$tplname");
		trbasic('ģ������','mtpladd[cname]');
		trbasic('ģ�����','mtpladd[tpclass]',makeoption($tpclasses,$mtpl['tpclass']),'select');
		if($mtpl['tpclass'] == 'archive') trbasic('���ĵ�����ʱ��ѡ','mtpladd[chid]',makeoption(array(0 => '��ѡ��') + cls_channel::chidsarr(1),empty($mtpl['chid']) ? 0 : $mtpl['chid']),'select',array('guide'=>'Ϊ�����ĵ�ָ������ҳģ��ʱ��ֻ��������ѡģ�͵��ĵ��ſ���ѡ���ģ��',));
		trbasic('Դģ���ļ�','',$tplname,'');
		trbasic('ģ���ļ����Ϊ','mtpladd[tplname]','','text',array('guide' => "�ļ�����ֻ���������ĸ�����֡��»���(-_)����(.)���ַ�,����htm��htmlΪ��չ��"));
		tabfooter('bmtplcopy');
		a_guide('mtplcopy');
	}else{
		if(empty($mtpladd['cname'])) cls_message::show('������ģ������',M_REFERER);
		if($re = _08_FilesystemFile::CheckFileName($mtpladd['tplname'])) cls_message::show($re);
		$mtplsnew = findfiles($true_tpldir);
		in_array($mtpladd['tplname'],$mtplsnew) && cls_message::show('ָ����ģ���ļ������ظ�',M_REFERER);
		!copy($true_tpldir.$tplname,$true_tpldir.$mtpladd['tplname']) && cls_message::show('ģ�帴��ʧ��',M_REFERER);
		$mtpls[$mtpladd['tplname']] = array('cname' => stripslashes($mtpladd['cname']),'tpclass' => $mtpladd['tpclass']);
		if($mtpladd['tpclass'] == 'archive'){
			if($mtpladd['chid'] = max(0,intval($mtpladd['chid']))){
				$mtpls[$mtpladd['tplname']]['chid'] = $mtpladd['chid'];
			}else unset($mtpls[$mtpladd['tplname']]['chid']);
		}
		cls_CacheFile::Save($mtpls,'mtpls','mtpls');
		adminlog('���Ƴ���ģ��');
		cls_message::show('ģ�帴�����',axaction(6,"?entry=$entry&action=mtplsedit"));
	}
}elseif($action == 'mtagcode'){
    empty($fn) || $fn = preg_replace('/[^A-Z0-9_-]/i', '_', trim($fn));
    $types = trim($types);
    $textid = trim($textid);
    $floatwin_id = trim($floatwin_id);

    $url_params = array();
    foreach(array('fn', 'types', 'textid', 'floatwin_id', 'caretpos', 'ttype', 'bclass', 'sclass') as $key)
    {
        empty($$key) || $url_params[$key] = ("{$key}=" . $$key);
    }
    if(empty($url_params)) cls_message::show('��������!');

    $createranges = read_select_file($fn);
    $createrange = stripslashes($createranges['old_str']);
    
    // ��ǰִ�в����ʶʱ
    if(isset($types) && $types == 'insert') {
        $url_params = implode('&', $url_params);
        $url = "?entry=mtags&action=mtaginsert" . (empty($url_params) ? '' : "&{$url_params}");
        mheader("Location:$url");
    } else {
    	if(empty($createrange)) cls_message::show('��ָ����ʶ��Դ!');
    	if(preg_match("/\{(u|c|p|tpl)\\$(.+?)(\s|\})/is",$createrange,$matches))
        {
            if (empty($createranges['tclass']))
            {
                cls_message::show('�ñ�ʶ�����ڡ�');
            }
            # ѡ�б�ʶʱ�õ������ԭʼ��ʶ��Ӧ��
            $mtagses = _08_factory::getMtagsInstance($createranges['tclass']);
            $url_params['bclass'] = 'bclass=' . $createranges['tclass'];
            is_object($mtagses) && $url_params['sclass'] = 'sclass=' . $mtagses->getSclass((array) $createranges['setting']);
            $url_params = implode('&', $url_params);
    	    if(strtolower(trim($matches[1])) == 'tpl') {
    	        $ttype = 'rtag';
    	    } else {
    	        $ttype = $matches[1].'tag';
    	    }
    		$tname = $matches[2];
    		$url = "?entry=mtags&action=mtagsdetail&ttype=$ttype&tname=$tname" . (empty($url_params) ? '' : "&{$url_params}");
            #exit($url);
 
        	mheader("location:$url");
    	}
    }
	cls_message::show('��ָ����ʶ��Դ!');
}elseif($action == 'basic2extend' && $tplname){
    $msg = rtag_basic2extend($tplname) ? 'ģ����չ���' : '����ģ��ԭ�ļ�������';
    cls_message::show($msg,axaction(6,"?entry=$entry&action=mtplsedit"));
}
