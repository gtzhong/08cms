<?
(!defined('M_COM') || !defined('M_ADMIN')) && exit('No Permission');
aheader();
if($re = $curuser->NoBackFunc('gather')) cls_message::show($re);
$channels = cls_cache::Read('channels');
$gmodels = cls_cache::Read('gmodels');
if($action == 'gmodeledit'){
	if(!submitcheck('bsubmit')){
		backnav('gmiss','model');
		tabheader("�ɼ�ģ�͹���&nbsp; &nbsp; >><a href=\"?entry=$entry&action=gmodeladd\" onclick=\"return floatwin('open_gmodel',this)\">���</a>",'gmodeledit',"?entry=$entry&action=$action",'5');
		trcategory(array('ID',array('�ɼ�ģ��','txtL'),'�ĵ�ģ��','<input class="checkbox" type="checkbox" name="chkall" onclick="deltip(this,0,checkall,this.form)">ɾ?','�༭'));
		foreach($gmodels as $k => $v){
			$channelstr = @$channels[$v['chid']]['cname'];
			$editstr = "<a href=\"?entry=$entry&action=gmodeldetail&gmid=$k\" onclick=\"return floatwin('open_gmodel',this)\">����</a>";
			echo "<tr class=\"txt\">".
				"<td class=\"txtC w30\">$k</td>\n".
				"<td class=\"txtL\"><input type=\"text\" size=\"30\" name=\"gmodelsnew[$k][cname]\" value=\"$v[cname]\"></td>\n".
				"<td class=\"txtC\">$channelstr</td>\n".
				"<td class=\"txtC w40\"><input class=\"checkbox\" type=\"checkbox\" name=\"delete[$k]\" value=\"$k\" onclick=\"deltip()\">\n".
				"<td class=\"txtC w30\">$editstr</td></tr>\n";
		}
		tabfooter('bsubmit','�޸�');
		a_guide('gmodeledit');
	}else{
		if(!empty($delete)){
			foreach($delete as $k){
				if($db->result_one("SELECT COUNT(*) FROM {$tblprefix}gmissions WHERE gmid='$k'")) continue;
				$db->query("DELETE FROM {$tblprefix}gmodels WHERE gmid=$k");
				unset($gmodelsnew[$k]);
			}
		}
		if(!empty($gmodelsnew)){
			foreach($gmodelsnew as $k => $v){
				$v['cname'] = empty($v['cname']) ? addslashes($gmodels[$k]['cname']) : $v['cname'];
				$db->query("UPDATE {$tblprefix}gmodels SET cname='$v[cname]' WHERE gmid=$k");
			}
		}
		cls_CacheFile::Update('gmodels');
		adminlog('�༭�ɼ�ģ�͹����б�');
		cls_message::show('�ɼ�ģ���޸����',axaction(6,"?entry=$entry&action=gmodeledit"));
	}
}elseif($action == 'gmodeladd'){
	if(!submitcheck('bsubmit')){
		tabheader('��Ӳɼ�ģ��','gmodeladd',"?entry=$entry&action=$action");
		trbasic('�ɼ�ģ������','gmodeladd[cname]');
		trbasic('��ָ���ɼ����ĵ�ģ��','gmodeladd[chid]',makeoption(cls_channel::chidsarr(0)),'select');
		tabfooter('bsubmit','���');
	}else{
		$gmodeladd['cname'] = trim(strip_tags($gmodeladd['cname']));
		if(!$gmodeladd['cname']) cls_message::show('������ɼ�ģ������!',M_REFERER);
		if(!$gmodeladd['chid']) cls_message::show('��ѡ���ĵ�ģ�ͻ�ϼ�����!',M_REFERER);
		$db->query("INSERT INTO {$tblprefix}gmodels SET cname='$gmodeladd[cname]',chid='$gmodeladd[chid]'");
		$gmid = $db->insert_id();
		cls_CacheFile::Update('gmodels');
		adminlog('��Ӳɼ�ģ��');
		cls_message::show('�ɼ�ģ��������',"?entry=$entry&action=gmodeldetail&gmid=$gmid");
	}

}elseif($action =='gmodeldetail' && $gmid){
	$gmodel = cls_cache::Read('gmodel',$gmid,'');
	empty($gmodel) && cls_message::show('��ָ����ȷ�Ĳɼ�ģ��');
	empty($channels[$gmodel['chid']]) && cls_message::show('�ɼ�ģ�͹������ĵ�ģ�Ͳ�����');
	$gfields = empty($gmodel['gfields']) ? array() : $gmodel['gfields'];
	$fields = cls_cache::Read('fields',$gmodel['chid']);
    $cotypes = cls_cache::Read('cotypes');
    $cfields = array('caid'=>array('datatype'=>'select','cname'=>'��Ŀ'));
    foreach($cotypes as $k=>$v){
        $cfields['ccid'.$k]['datatype'] = $v['asmode'] ? 'mselect' : 'select';
        $cfields['ccid'.$k]['cname'] = $v['cname'];
    }
    $fields = $cfields + $fields + array('jumpurl'=>array('datatype'=>'text','cname'=>'��תURL'),'createdate'=>array('datatype'=>'text','cname'=>'���ʱ��'),'enddate'=>array('datatype'=>'text','cname'=>'����ʱ��'),'mname'=>array('datatype'=>'text','cname'=>'��Ա����'));
	if(!submitcheck('bsubmit')){
		include_once M_ROOT."include/fields.fun.php";
		tabheader($gmodel['cname'].'-�ɼ��ֶ�����','gmodeldetail',"?entry=$entry&action=gmodeldetail&gmid=$gmid",'5');
		trcategory(array('�ɼ�','������',array('�ֶ�����','txtL'),'�ֶα�ʶ','�ֶ�����'));
		foreach($fields as $k => $v){
			$islinkstr = ($v['datatype'] != 'text' || $k == 'createdate' || $k == 'mname' || $k == 'enddate') ? '-' : "<input class=\"checkbox\" type=\"checkbox\" name=\"fieldsnew[$k][islink]\" value=\"1\"".(!empty($gfields[$k]) ? ' checked' : '').">";
			echo "<tr class=\"txt\">\n".
				"<td class=\"txtC w40\"><input class=\"checkbox\" type=\"checkbox\" name=\"fieldsnew[$k][available]\" value=\"1\"".(isset($gfields[$k]) ? ' checked' : '')."></td>\n".
				"<td class=\"txtC w50\">$islinkstr</td>\n".
				"<td class=\"txtL\">$v[cname]</td>\n".
				"<td class=\"txtC\">$k</td>\n".
				"<td class=\"txtC w80\">".cls_fieldconfig::datatype($v['datatype'])."</td>\n".
				"</tr>";
		}
		tabfooter('bsubmit');
		a_guide('gmodeldetail');
	}else{
		foreach($fields as $k => $v){
			if(!empty($fieldsnew[$k]['available'])){
				$islink = empty($fieldsnew[$k]['islink']) ? 0 : 1;
				in_array($v['datatype'],array('image','flash','file','media','jumpurl')) && $islink = 1;
				$newgfields[$k] = $islink;
			}
		}
		$gfieldsnew = empty($newgfields) ? '' : addslashes(serialize($newgfields));
		$db->query("UPDATE {$tblprefix}gmodels SET gfields='$gfieldsnew' WHERE gmid='$gmid'");
		cls_CacheFile::Update('gmodels');
		cls_CacheFile::Update('gmissions');//��֤�ڲ��Ի�ȡ����ʱ�������ֶ�һ��
		adminlog('��ϸ�޸Ĳɼ�ģ��');
		cls_message::show('�ɼ�ģ�ͱ༭���',axaction(6,"?entry=$entry&action=gmodeledit"));
	}
}
?>
