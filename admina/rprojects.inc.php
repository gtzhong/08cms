<?
(!defined('M_COM') || !defined('M_ADMIN')) && exit('No Permission');
aheader();
if($re = $curuser->NoBackFunc('project')) cls_message::show($re);
foreach(array('rprojects','channels',) as $k) $$k = cls_cache::Read($k);
backnav('project','rproject');
if($action == 'rprojectedit'){
	if(!submitcheck('brprojectadd') && !submitcheck('brprojectedit')){
		tabheader('Զ�̷�������','rprojectedit','?entry=rprojects&action=rprojectedit','5');
		trcategory(array('ID',array('��������', 'txtL'),'��������','�ļ�������չ��','ɾ��','�༭'));
		foreach($rprojects as $k => $rproject){
			$extnames = implode('&nbsp;&nbsp;',array_keys($rproject['rmfiles']));
			$rproject['issystemstr'] = empty($rproject['issystem']) ? '�Զ�': 'ϵͳ';
			echo "<tr class=\"txt\">".
				"<td class=\"txtC w40\">$k</td>\n".
				"<td class=\"txtL\"><input type=\"text\" size=\"30\" name=\"rprojectsnew[$k][cname]\" value=\"$rproject[cname]\"".(!empty($rproject['issystem']) ? " unselectable=\"on\"" : "")."></td>\n".
				"<td class=\"txtC w80\">$rproject[issystemstr]</td>\n".
				"<td class=\"txtC\">$extnames</td>\n".
				"<td class=\"txtC w40\"><input class=\"checkbox\" type=\"checkbox\"".(!empty($rproject['issystem']) ? ' disabled' : " name=\"delete[$k]\" value=\"$k\" onclick=\"deltip(this,$no_deepmode)\"").">\n".
				"<td class=\"txtC w40\"><a href=\"?entry=rprojects&action=rprojectdetail&rpid=$k\">����</a></td></tr>\n";
		}

		tabfooter('brprojectedit','�޸�');
		tabheader('���Զ�̷���','rprojectadd','?entry=rprojects&action=rprojectedit');
		trbasic('��������','rprojectadd[cname]');
		tabfooter('brprojectadd','���');
		a_guide('rprojectedit');
	}
	elseif(submitcheck('brprojectedit')) {
		if(!empty($delete) && deep_allow($no_deepmode)){
			foreach($delete as $k){
				$db->query("DELETE FROM {$tblprefix}rprojects WHERE rpid=$k");
				unset($rprojectsnew[$k]);
			}
		}
		foreach($rprojectsnew as $k => $rprojectnew){
			if(empty($rprojects[$k]['issystem'])){
				$rprojectnew['cname'] = empty($rprojectnew['cname']) ? $rprojects[$k]['cname'] : $rprojectnew['cname'];
				$db->query("UPDATE {$tblprefix}rprojects SET cname='$rprojectnew[cname]' WHERE rpid=$k");
			}
		}
		cls_CacheFile::Update('rprojects');
		adminlog('�༭Զ���ϴ�����','�༭�����б�');
		cls_message::show('�����޸����', '?entry=rprojects&action=rprojectedit');
	}
	elseif(submitcheck('brprojectadd')) {
		if(!$rprojectadd['cname']) {
			cls_message::show('��������missiong', '?entry=rprojects&action=rprojectedit');
		}
		$db->query("INSERT INTO {$tblprefix}rprojects SET rpid=".auto_insert_id('rprojects').",cname='$rprojectadd[cname]'");
		cls_CacheFile::Update('rprojects');
		adminlog('���Զ���ϴ�����','���Զ���ϴ�����');
		cls_message::show('����������', '?entry=rprojects&action=rprojectedit');
	}
}
if($action =='rprojectdetail' && $rpid){
	$rmfiles = $rprojects[$rpid]['rmfiles'];
	$excludes = implode("\n",$rprojects[$rpid]['excludes']);
	$timeout = empty($rprojects[$rpid]['timeout']) ? 0 : intval($rprojects[$rpid]['timeout']);
	if(!submitcheck('bfilesedit') && !submitcheck('bfilesadd')){
		tabheader('Զ�������ļ�����'.'&nbsp; - &nbsp;'.$rprojects[$rpid]['cname'],'filesedit',"?entry=rprojects&action=rprojectdetail&rpid=$rpid",6);
		trcategory(array('<input class="checkbox" type="checkbox" name="chkall" onclick="deltip(this,0,checkall,this.form)">ɾ?','�ļ���չ��','�������'.'(K)','��С����'.'(K)','MIME'.'����','�������'));
		$ftypearr = array(
						'image' => 'ͼƬ',
						'flash' => 'Flash',
						'media' => '��Ƶ',
						'file' => '����',
		);
		foreach($rmfiles as $k => $rmfile) {
			echo "<tr class=\"txt\">".
				"<td class=\"txtC w40\"><input class=\"checkbox\" type=\"checkbox\" name=\"delete[$k]\" value=\"$k\" onclick=\"deltip()\">\n".
				"<td class=\"txtC\">$rmfile[extname]</td>\n".
				"<td class=\"txtC\"><input type=\"text\" size=\"6\" name=\"rmfilesnew[$k][maxsize]\" value=\"$rmfile[maxsize]\"></td>\n".
				"<td class=\"txtC\"><input type=\"text\" size=\"6\" name=\"rmfilesnew[$k][minisize]\" value=\"$rmfile[minisize]\"></td>\n".
				"<td class=\"txtC\"><input type=\"text\" size=\"25\" name=\"rmfilesnew[$k][mime]\" value=\"$rmfile[mime]\"></td>\n".
				"<td class=\"txtC w50\"><select name=\"rmfilesnew[$k][ftype]\">".makeoption($ftypearr,$rmfile['ftype'])."</select></td></tr>\n";
		}
		tabfooter();
		tabheader('��������'.'&nbsp; - &nbsp;'.$rprojects[$rpid]['cname']);
		trbasic('���س�ʱ����(��)','timeoutnew',$timeout,'text',array('guide'=>'0��ձ�ʾ������'));
		trbasic('���Ժ������ִ���Զ���ļ�','excludesnew',$excludes,'textarea',array('guide'=>'�������趨������ĳЩ��ַ��Զ���ļ���ÿ������һ���ִ���ȫ���������𳬹�255�ֽڡ�'));
		tabfooter('bfilesedit');

		tabheader('����ļ�����','filesadd',"?entry=rprojects&action=rprojectdetail&rpid=$rpid",2,0,1);
		trbasic('�ļ���չ��','rmfileadd[extname]','','text',array('validate'=>makesubmitstr('rmfileadd[extname]',1,'numberletter',0,10)));
		trbasic('�ļ��������','rmfileadd[ftype]',makeoption($ftypearr),'select');
		tabfooter('bfilesadd','���');
		a_guide('rprojectdetail');

	}elseif(submitcheck('bfilesadd')){
		$rmfileadd['extname'] = trim(strtolower($rmfileadd['extname']));
		$rmfileadd['mime'] = '';
		if(!$rmfileadd['extname']){
			cls_message::show('���ϲ���ȫ', '?entry=rprojects&action=rprojectdetail&rpid='.$rpid);
		}
		if(preg_match("/[^a-zA-Z0-9]+/",$rmfileadd['extname'])) {
			cls_message::show('�ļ���չ�����Ϲ淶','?entry=rprojects&action=rprojectdetail&rpid='.$rpid);
		}
		if(in_array($rmfileadd['extname'],array_keys($rmfiles))) {
			cls_message::show('�ļ���չ���ظ�','?entry=rprojects&action=rprojectdetail&rpid='.$rpid);
		}
		$rmfileadd['maxsize'] = 0;
		$rmfileadd['minisize'] = 0;
		$rmfiles[$rmfileadd['extname']] = $rmfileadd;
		$rmfiles = addslashes(serialize($rmfiles));
		$db->query("UPDATE {$tblprefix}rprojects SET rmfiles='$rmfiles' WHERE rpid='$rpid'");
		cls_CacheFile::Update('rprojects');
		adminlog('�༭Զ���ϴ�����','���Զ���ϴ������ļ�����');
		cls_message::show('�ļ�����������','?entry=rprojects&action=rprojectdetail&rpid='.$rpid);
	}elseif(submitcheck('bfilesedit')){
		if(isset($delete)){
			foreach($delete as $id) {
				unset($rmfilesnew[$id]);
			}
		}
		if(!empty($rmfilesnew)){
			foreach($rmfilesnew as $id => $rmfilenew) {
				$rmfilenew['extname'] = $rmfiles[$id]['extname'];
				$rmfilenew['mime'] = trim(strtolower($rmfilenew['mime']));
				$rmfilenew['maxsize'] = max(0,intval($rmfilenew['maxsize']));
				$rmfilenew['minisize'] = max(0,intval($rmfilenew['minisize']));
				$rmfilesnew[$id] = $rmfilenew;
			}
			$rmfilesnew = addslashes(serialize($rmfilesnew));
		}else{
			$rmfilesnew = '';
		}	
		if(!empty($excludesnew)){
			$excludesnew = str_replace(array("\r","\n"),',',$excludesnew);
			$excludesarr = array_filter(explode(',',$excludesnew));
			$excludesnew = implode(',',$excludesarr);
		}else $excludesnew = '';
		$timeoutnew = max(0,intval($timeoutnew));
		$db->query("UPDATE {$tblprefix}rprojects SET 
			rmfiles='$rmfilesnew',
			timeout='$timeoutnew',
			excludes='$excludesnew' 
			WHERE rpid='$rpid'");
		cls_CacheFile::Update('rprojects');
		adminlog('�༭Զ���ϴ�����','��ϸ�޸�Զ���ϴ�����');
		cls_message::show('Զ�̷����༭���','?entry=rprojects&action=rprojectdetail&rpid='.$rpid);
	}
}
?>
