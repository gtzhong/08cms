<?PHP
(!defined('M_COM') || !defined('M_ADMIN')) && exit('No Permission');
aheader();
backallow('tpl') || cls_message::show('��û�е�ǰ��Ŀ�Ĺ���Ȩ�ޡ�');
foreach(array('mtpls','csstpls','jstpls',) as $k) $$k = cls_cache::Read($k);
$action = empty($action) ? 'csstplsedit' : $action;
$jsmode = empty($jsmode) ? 0 : 1;
$FileTypeTitle = $jsmode ? 'JS' : 'CSS';
$FileTypeExt = $jsmode ? 'js' : 'css';
$true_tpldir = cls_tpl::TemplateTypeDir(empty($jsmode) ? 'css' : 'js');
mmkdir($true_tpldir);
if($action == 'csstplsedit'){
	backnav('tpl','cssjs');
	if(!submitcheck('bcsstplsedit')){
		$cssdocs = glob(cls_tpl::TemplateTypeDir('css').'*.css');
		tabheader('CSS�ļ�����'."&nbsp;&nbsp;&nbsp;&nbsp;[<a href=\"?entry=$entry&action=fileadd\" onclick=\"return floatwin('open_csstplsedit',this)\">���</a>]",'csstplsedit',"?entry=$entry&action=$action",'9');
		trcategory(array(array('css�ļ�','txtL'),array('����','txtL'),'ɾ��','����','����'));
		foreach($cssdocs as $k => $v){
			$v = basename($v);
			echo "<tr class=\"txt\">".
				"<td class=\"txtL w150\">$v</td>\n".
				"<td class=\"txtL\"><input type=\"text\" size=\"25\" name=\"csstplsnew[$v][cname]\" value=\"".mhtmlspecialchars(@$csstpls[$v]['cname'])."\"></td>\n".
				"<td class=\"txtC w40\"><a onclick=\"return deltip()\" href=\"?entry=$entry&action=filedel&filename=$v\">ɾ��</a></td>\n".
				"<td class=\"txtC w30\"><a href=\"?entry=$entry&action=filecopy&filename=$v\" onclick=\"return floatwin('open_csstplsedit',this)\">����</a></td>\n".
				"<td class=\"txtC w30\"><a href=\"?entry=$entry&action=filedetail&filename=$v\" onclick=\"return floatwin('open_csstplsedit',this)\">�༭</a></td>\n".
				"</tr>\n";
		}
		tabfooter('bcsstplsedit','�޸�');

		$jsdocs = glob(cls_tpl::TemplateTypeDir('js').'*.js');
		tabheader('js�ļ�����'."&nbsp;&nbsp;&nbsp;&nbsp;[<a href=\"?entry=$entry&action=fileadd&jsmode=1\" onclick=\"return floatwin('open_csstplsedit',this)\">���</a>]",'jstplsedit',"?entry=$entry&action=csstplsedit&jsmode=1",'9');
		trcategory(array('JS�ļ�|L','����|L','ɾ��','����','����'));
		foreach($jsdocs as $k => $v){
			$v = basename($v);
			echo "<tr class=\"txt\">".
				"<td class=\"txtL w150\">$v</td>\n".
				"<td class=\"txtL\"><input type=\"text\" size=\"25\" name=\"jstplsnew[$v][cname]\" value=\"".mhtmlspecialchars(@$jstpls[$v]['cname'])."\"></td>\n".
				"<td class=\"txtC w40\"><a onclick=\"return deltip()\" href=\"?entry=$entry&action=filedel&filename=$v&jsmode=1\">ɾ��</a></td>\n".
				"<td class=\"txtC w30\"><a href=\"?entry=$entry&action=filecopy&filename=$v&jsmode=1\" onclick=\"return floatwin('open_csstplsedit',this)\">����</a></td>\n".
				"<td class=\"txtC w30\"><a href=\"?entry=$entry&action=filedetail&filename=$v&jsmode=1\" onclick=\"return floatwin('open_csstplsedit',this)\">�༭</a></td>\n".
				"</tr>\n";
		}
		tabfooter('bcsstplsedit','�޸�');
	}elseif(!$jsmode){
		if(!empty($csstplsnew)){
			foreach($csstplsnew as $k => $v){
				$csstpls[$k]['cname'] = stripslashes($v['cname']);
			}
		}
		cls_CacheFile::Save($csstpls,'csstpls','csstpls');
		adminlog('�༭CSS�ļ������б�');
		cls_message::show('CSS�ļ��޸����',M_REFERER);
	}else{
		if(!empty($jstplsnew)){
			foreach($jstplsnew as $k => $v){
				$jstpls[$k]['cname'] = stripslashes($v['cname']);
			}
		}
		cls_CacheFile::Save($jstpls,'jstpls','jstpls');
		adminlog('�༭JS�ļ������б�');
		cls_message::show('JS�ļ��޸����',M_REFERER);
	}
}elseif($action == 'fileadd'){
	$forward = empty($forward) ? M_REFERER : $forward;
	$forwardstr = '&forward='.rawurlencode($forward);
	if(!submitcheck('bfileadd')){
		tabheader("���{$FileTypeTitle}�ļ�",'filecopy',"?entry=$entry&action=$action&jsmode=$jsmode$forwardstr");
		trbasic($FileTypeTitle.'�ļ����Ϊ','filenamenew','','text',array('guide' => "�ļ�����ֻ���������ĸ�����֡��»���(_)����(.)���ַ�,����{$FileTypeExt}Ϊ��չ��"));
		echo "<tr class=\"txt\"><td class=\"txtL\">�ļ�����</td>".
		"<td class=\"txtL\"><textarea class=\"textarea\" style=\"width:650px;height:400px\" name=\"contentnew\" id=\"contentnew\"></textarea></td></tr>";
		tabfooter('bfileadd');
		a_guide('csstpladd');
	}else{
		if($re = _08_FilesystemFile::CheckFileName($filenamenew,$FileTypeExt)) cls_message::show($re,M_REFERER);
		$filesnew = findfiles($true_tpldir);
		in_array($filenamenew,$filesnew) && cls_message::show('ָ�����ļ������ظ�',M_REFERER);
		if(!str2file(stripslashes($contentnew),$true_tpldir.$filenamenew)) cls_message::show('�ļ����ʧ��',M_REFERER);
		adminlog("���{$FileTypeTitle}�ļ�");
		cls_message::show("{$FileTypeTitle}�ļ�������",axaction(6,$forward));
	}

}elseif($action == 'filecopy'){
	$forward = empty($forward) ? M_REFERER : $forward;
	$forwardstr = '&forward='.rawurlencode($forward);
	if($re = _08_FilesystemFile::CheckFileName($filename,$FileTypeExt)) cls_message::show($re);
	if(!is_file($true_tpldir.$filename)) cls_message::show('ָ����Դ�ļ�������');
	if(!submitcheck('bfilecopy')){
		tabheader("����{$FileTypeTitle}�ļ�",'filecopy',"?entry=$entry&action=$action&filename=$filename&jsmode=$jsmode$forwardstr");
		trbasic('Դ�ļ�','',$filename,'');
		trbasic($FileTypeTitle.'�ļ����Ϊ','filenamenew','','text',array('guide' => "�ļ�����ֻ���������ĸ�����֡��»���(_)����(.)���ַ�,����{$FileTypeExt}Ϊ��չ��"));
		tabfooter('bfilecopy');
		a_guide('csstplcopy');
	}else{
		if($re = _08_FilesystemFile::CheckFileName($filenamenew,$FileTypeExt)) cls_message::show($re,M_REFERER);
		$filesnew = findfiles($true_tpldir);
		in_array($filenamenew,$filesnew) && cls_message::show('ָ�����ļ������ظ�',M_REFERER);

		if(!copy($true_tpldir.$filename,$true_tpldir.$filenamenew)) cls_message::show('�ļ�����ʧ��',M_REFERER);
		adminlog("����{$FileTypeTitle}�ļ�");
		cls_message::show('�ļ��������',axaction(6,$forward));
	}
}elseif($action == 'filedetail'){
	$forward = empty($forward) ? M_REFERER : $forward;
	$forwardstr = '&forward='.rawurlencode($forward);
	if($re = _08_FilesystemFile::CheckFileName($filename,$FileTypeExt)) cls_message::show($re);
	if(!submitcheck('bfiledetail')){
		$content = @file2str($true_tpldir.$filename);
		tabheader($FileTypeTitle.'�ļ��༭'.'&nbsp;-&nbsp;'.$filename,'filedetail',"?entry=$entry&action=$action&filename=$filename&jsmode=$jsmode$forwardstr");
		echo "<tr class=\"txt\"><td colspan=\"2\"><textarea class=\"textarea\" style=\"width:700px;height:400px\" name=\"contentnew\" id=\"contentnew\">".htmlspecialchars(str_replace("\t","    ",$content))."</textarea></td><tr>";
		tabfooter('bfiledetail');
	}else{
		@str2file(stripslashes($contentnew),$true_tpldir.$filename);
		adminlog('��ϸ�޸�'.$FileTypeTitle.'�ļ�');
		cls_message::show($FileTypeTitle.'�ļ��޸����',axaction(6,$forward));
	}
}elseif($action == 'filedel'){
	$forward = empty($forward) ? M_REFERER : $forward;
	$forwardstr = '&forward='.rawurlencode($forward);
	if($re = _08_FilesystemFile::CheckFileName($filename,$FileTypeExt)) cls_message::show($re,M_REFERER);
	if(!submitcheck('confirm')){
		$message = "ɾ�����ָܻ���ȷ��ɾ����ѡ��Ŀ?<br><br>";
		$message .= "ȷ��������[<a href='?entry=$entry&action=$action&filename=$filename&jsmode=$jsmode&confirm=ok$forwardstr'>ɾ��</a>]&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
		$message .= "����������[<a href='?entry=$entry'>����</a>]";
		cls_message::show($message);
	}
    $file = _08_FilesystemFile::getInstance();
	$file->delFile($true_tpldir.$filename);
	adminlog('ɾ��'.$FileTypeTitle.'�ļ�');
	cls_message::show($FileTypeTitle.'�ļ�ɾ�����',$forward);
}