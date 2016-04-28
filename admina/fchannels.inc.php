<?php
(!defined('M_COM') || !defined('M_ADMIN')) && exit('No Permission');
aheader();
if($re = $curuser->NoBackFunc('freeinfo')) cls_message::show($re);
include_once M_ROOT."include/fields.fun.php";
$rprojects = cls_cache::Read('rprojects');
if($action == 'fchannelsedit') {
	backnav('fchannel','channel');
	$fchannels = cls_fchannel::InitialInfoArray();
	if(!submitcheck('bsubmit')) {
		$TitleStr = "����ģ�͹��� &nbsp; &nbsp;>><a href=\"?entry=$entry&action=fchanneladd\" onclick=\"return floatwin('open_fchanneldetail',this)\">���ģ��</a>";
		tabheader($TitleStr,'fchannelsedit',"?entry=$entry&action=$action",'4');
		trcategory(array('ID','ģ������|L','ɾ��','�ֶ�',));
		foreach($fchannels as $k => $v) {
			echo "<tr class=\"txt\">".
				"<td class=\"txtC w30\">$k</td>\n".
				"<td class=\"txtL\"><input type=\"text\" size=\"30\" maxlength=\"30\" name=\"fchannelnew[$k][cname]\" value=\"$v[cname]\"></td>\n".
				"<td class=\"txtC w40\"><a onclick=\"return deltip(this,$no_deepmode)\" href=\"?entry=$entry&action=fchanneldel&chid=$k\">ɾ��</a></td>\n".
				"<td class=\"txtC w40\"><a href=\"?entry=$entry&action=fchanneldetail&chid=$k\" onclick=\"return floatwin('open_fchannelsedit',this)\">�ֶ�</a></td>\n".
				"</tr>\n";
		}
		tabfooter('bsubmit');
		a_guide('fchannelsedit');
	}else{
		if(isset($fchannelnew)) {
			foreach($fchannelnew as $k => $v) {
				$v['cname'] = trim(strip_tags($v['cname']));
				$v['cname'] = $v['cname'] ? $v['cname'] : $fchannels[$k]['cname'];
				$fchannels[$k]['cname'] = $v['cname'];
			}
			adminlog('�༭����ģ�͹����б�');
			cls_fchannel::SaveInitialCache($fchannels);
		}
		cls_message::show('����ģ�ͱ༭���',"?entry=$entry&action=$action");
	}
}elseif($action =='fchanneladd'){
	echo _08_HTML::Title('��Ӹ���ģ��');
	if(!submitcheck('bsubmit')){
		tabheader('��Ӹ���ģ��','fmdata',"?entry=$entry&action=$action",2,0,1);
		trbasic('ģ������','fmdata[cname]','','text',array('validate' => makesubmitstr('fmdata[cname]',1,0,3,30)));
		tabfooter('bsubmit','���');
	}else{
		$fmdata['cname'] = trim(strip_tags($fmdata['cname']));
		if(empty($fmdata['cname'])) cls_message::show('ģ�����Ʋ���ȫ',M_REFERER);
		if($chid = cls_fchannel::AddOne($fmdata)){
			adminlog('��Ӹ���ģ��');
			cls_message::show('����ģ����ӳɹ�����Դ�ģ�ͽ�����ϸ���á�',axaction(36,"?entry=$entry&action=fchanneldetail&chid=$chid"));
		}else{
			cls_message::show('����ģ����Ӳ��ɹ���');
		}
	}

}elseif($action == 'fchanneldel' && $chid){
	
	backnav('fchannel','channel');
	deep_allow($no_deepmode);
	if(!submitcheck('confirm')){
		$message = "ɾ�����ָܻ���ȷ��ɾ����ѡ��Ŀ?<br><br>";
		$message .= 'ȷ��������'."[<a href=?entry=$entry&action=$action&chid=$chid&confirm=ok>ɾ��</a>]&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
		$message .= '����������'."[<a href=?entry=$entry&action=fchannelsedit>����</a>]";
		cls_message::show($message);
	}
	
	cls_fchannel::DeleteOne($chid);

	adminlog('ɾ������ģ��');
	cls_message::show('����ģ��ɾ�����',"?entry=$entry&action=fchannelsedit");

}elseif($action == 'fchanneldetail' && $chid) {
	!($fchannel = cls_fchannel::InitialOneInfo($chid)) && cls_message::show('ָ����ģ�Ͳ����ڡ�');
	$fields = cls_FieldConfig::InitialFieldArray('fchannel',$chid);
	echo _08_HTML::Title($fchannel['cname'].'- �ֶι���');
	if(!submitcheck('bsubmit')){
		tabheader("[".$fchannel['cname']."] - �ֶα༭&nbsp; &nbsp; &nbsp; >><a href=\"?entry=$entry&action=fieldone&chid=$chid\" onclick=\"return floatwin('open_fchanneldetail',this)\">����ֶ�</a>",'fchanneldetail',"?entry=$entry&action=$action&chid=$chid",7,0,1);
		trcategory(array('����','�ֶ�����|L','����','�ֶα�ʶ|L','���ݱ�|L','�ֶ�����|L','ɾ��','�༭'));
		foreach($fields as $k => $v){
			echo "<tr class=\"txt\">\n".
				"<td class=\"txtC w40\"><input class=\"checkbox\" type=\"checkbox\" name=\"fieldsnew[$k][available]\" value=\"1\"".($v['available'] ? ' checked' : '').($v['issystem'] ? ' disabled' : '')."></td>\n".
				"<td class=\"txtL\"><input type=\"text\" size=\"25\" name=\"fieldsnew[$k][cname]\" value=\"".mhtmlspecialchars($v['cname'])."\"></td>\n".
				"<td class=\"txtC w60\"><input type=\"text\" size=\"4\" name=\"fieldsnew[$k][vieworder]\" value=\"$v[vieworder]\"></td>\n".
				"<td class=\"txtL\">".mhtmlspecialchars($k)."</td>\n".
				"<td class=\"txtL\">$v[tbl]</td>\n".
				"<td class=\"txtL w100\">".cls_fieldconfig::datatype($v['datatype'])."</td>\n".
				"<td class=\"txtC w40\"><input class=\"checkbox\" type=\"checkbox\"".($v['issystem'] ? ' disabled' : " name=\"delete[$k]\" value=\"$k\" onclick=\"deltip()\"")."></td>\n".
				"<td class=\"txtC w50\"><a href=\"?entry=$entry&action=fieldone&chid=$chid&fieldname=$k\" onclick=\"return floatwin('open_fielddetail',this)\">����</a></td>\n".
				"</tr>";
		}
		tabfooter('bsubmit');
		a_guide('fchanneldetail');
	}else{
		if(!empty($delete)){
			$deleteds = cls_fieldconfig::DeleteField('fchannel',$chid,$delete);
			foreach($deleteds as $k){
				unset($fieldsnew[$k]);
			}
		}
		if(!empty($fieldsnew)){
			foreach($fieldsnew as $k => $v){
				$v['cname'] = trim(strip_tags($v['cname']));
				$v['cname'] = $v['cname'] ? $v['cname'] : $fields[$k]['cname'];
				$v['available'] = $fields[$k]['issystem'] || !empty($v['available']) ? 1 : 0;
				$v['vieworder'] = max(0,intval($v['vieworder']));
				cls_fieldconfig::ModifyOneConfig('fchannel',$chid,$v,$k);
			}
		}
		cls_fieldconfig::UpdateCache('fchannel',$chid);
		
		adminlog('��ϸ�޸ĸ���ģ���ֶ�');
		cls_message::show('ģ���޸����',"?entry=$entry&action=$action&chid=$chid");
	}
}elseif($action == 'fieldone'){
	cls_FieldConfig::EditOne('fchannel',@$chid,@$fieldname);
}
