<?
(!defined('M_COM') || !defined('M_ADMIN')) && exit('No Permission');
aheader();
if($re = $curuser->NoBackFunc('tpl')) cls_message::show($re);
cls_cache::Load('bnames');
cls_cache::Load('sptpls');
$dbtpls = fetch_arr();
$true_tpldir = M_ROOT."./template/$templatedir";
mmkdir($true_tpldir);
if($action == 'sptplsedit'){
	backnav('tpls','futpl');
	if(!submitcheck('bsptplsedit')) {
		tabheader('�ض�����ҳ�����','sptplsedit',"?entry=sptpls&action=sptplsedit",'5');
		trcategory(array('���','ҳ������','��������','ģ���ļ�','����'));
		$no = 0;
		foreach($dbtpls as $k => $v){
			$no ++;
			echo "<tr class=\"txt\">".
				"<td class=\"txtC w30\">$no</td>\n".
				"<td class=\"txtL\">$v[cname]</td>\n".
				"<td class=\"txtL\">$v[link]</td>\n".
				"<td class=\"txtC\"><input type=\"text\" size=\"20\" name=\"sptplsnew[$k][tplname]\" value=\"".(empty($sptpls[$k]) ? '' : $sptpls[$k])."\"></td>\n".
				"<td class=\"txtC w30\"><a href=\"?entry=sptpls&action=sptpldetail&spid=$k\" onclick=\"return floatwin('open_sptplsedit',this)\">�༭</a></td></tr>\n";
		}
		tabfooter('bsptplsedit','�޸�');
		a_guide('sptplsedit');
	}else{
		$sptpls = array();
		foreach($dbtpls as $k => $v){
			$sptplsnew[$k]['tplname'] = trim($sptplsnew[$k]['tplname']);
			if(preg_match("/[^a-z_A-Z0-9\.]+/",$sptplsnew[$k]['tplname'])) $sptplsnew[$k]['tplname'] = '';
			$sptpls[$k] = $sptplsnew[$k]['tplname'];
		}
		cls_CacheFile::Save($sptpls,'sptpls','sptpls');
		adminlog('�༭�ض�����ģ������б�');
		cls_message::show('ҳ���޸����', "?entry=sptpls&action=sptplsedit");
	}
}
elseif($action == 'sptpldetail' && $spid){
	$dbtpl = $dbtpls[$spid];
	$tplname = empty($sptpls[$spid]) ? '' : $sptpls[$spid];
	if(!submitcheck('bsptpldetail')){
		if(empty($tplname) || !is_file($true_tpldir.'/'.$tplname)){
			if(@!touch($true_tpldir.'/'.$tplname)) cls_message::show('û�ж���ģ���ģ�岻����!',axaction(2,M_REFERER));
		}
		$template = cls_tpl::load($tplname,0);
		tabheader('�ض�����ģ������'.'-'.$dbtpl['cname'],'sptpldetail',"?entry=sptpls&action=sptpldetail&spid=$spid");
		trbasic('ģ������','',$tplname,'');
		templatebox('ģ������','templatenew',$template,30,110);
		tabfooter('bsptpldetail','�޸�');
		a_guide('sptpldetail');
	}else{
		empty($templatenew) && cls_message::show('ģ�����ݲ���Ϊ��',"?entry=sptpls&action=sptplsedit");
		!str2file(stripslashes($templatenew),$true_tpldir.'/'.$tplname) && cls_message::show('ģ�屣��ʱ��������',"?entry=sptpls&action=sptplsedit");
		adminlog('��ϸ�޸��ض�����ģ��');
		cls_message::show('ģ���޸����',axaction(6,"?entry=sptpls&action=sptplsedit"));
	}
}
function fetch_arr(){
	global $db,$tblprefix;
	$items = array();
	$query = $db->query("SELECT * FROM {$tblprefix}sptpls ORDER BY vieworder");
	while($item = $db->fetch_array($query)){
		$items[$item['ename']] = $item;
	}
	return $items;
}

?>
