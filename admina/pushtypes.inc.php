<?php
/*
* �Ƽ�λ�������
* ע�⣺��������ǿ��ɾ��
*/
(!defined('M_COM') || !defined('M_ADMIN')) && exit('No Permission');
aheader();
if($re = $curuser->NoBackFunc('pusharea')) cls_message::show($re);
if(empty($action)){
	backnav('pushareas','pushtype');
	$pushtypes = cls_PushType::InitialInfoArray();
	if(!submitcheck('bsubmit')){
		$TitleStr = "���ͷ������ &nbsp; &nbsp;>><a href=\"?entry=$entry&action=pushtypeadd\" onclick=\"return floatwin('open_pushtypesedit',this)\">��ӷ���</a>";
		tabheader($TitleStr,'pushtypesedit',"?entry=$entry",'7');
		trcategory(array('ID','��������|L','��ע˵��|L','����','ɾ��',));
		foreach($pushtypes as $k => $v){
			echo "<tr class=\"txt\">\n".
				"<td class=\"txtC w30\">$k</td>\n".
				"<td class=\"txtL\"><input type=\"text\" name=\"fmdata[$k][title]\" value=\"".mhtmlspecialchars($v['title'])."\" size=\"25\" maxlength=\"30\"></td>\n".
				"<td class=\"txtL\"><input type=\"text\" name=\"fmdata[$k][remark]\" value=\"".mhtmlspecialchars($v['remark'])."\" size=\"50\" maxlength=\"100\"></td>\n".
				"<td class=\"txtC w50\"><input type=\"text\" name=\"fmdata[$k][vieworder]\" value=\"$v[vieworder]\" size=\"2\"></td>\n".
				"<td class=\"txtC w30\"><a onclick=\"return deltip(this,$no_deepmode)\" href=\"?entry=$entry&action=pushtypedel&ptid=$k\">ɾ��</a></td>\n".
				"</tr>";
		}
		tabfooter('bsubmit');
		a_guide('pushtypesedit');
	}else{
		if(!empty($fmdata)){
			foreach($fmdata as $k => $v){
				cls_pushtype::ModifyOneConfig($v,$k);
			}
		}
		adminlog('�༭����λ��������б�');
		cls_message::show('����༭���',"?entry=$entry");
	}
}elseif($action =='pushtypeadd'){
	if(!submitcheck('bsubmit')){
		tabheader('�������λ����','pushtypeadd',"?entry=$entry&action=$action",2,0,1);
		trbasic('��������','fmdata[title]','','text',array('validate'=>makesubmitstr('fmdata[cname]',1,0,3,30)));
		trbasic('��ע˵��','fmdata[remark]','','text');
		tabfooter('bsubmit');
	}else{
		$ptid = cls_pushtype::ModifyOneConfig($fmdata,0);
		if($ptid){
			adminlog('������ͷ���');
			cls_message::show('���ͷ�����ӳɹ���',axaction(6,"?entry=$entry"));
		}else{
			cls_message::show('���ͷ�����Ӳ��ɹ���');
		}
	}
}elseif($action == 'pushtypedel' && $ptid){
	backnav('pushareas','pushtype');
	deep_allow($no_deepmode);
	if(!submitcheck('confirm')){
		$message = "ɾ�����ָܻ���ȷ��ɾ����ѡ��Ŀ?<br><br>";
		$message .= "ȷ������>><a href=?entry=$entry&action=$action&ptid=$ptid&confirm=ok>ɾ��</a><br>";
		$message .= "��������>><a href=?entry=$entry>����</a>";
		cls_message::show($message);
	}
	if($re = cls_pushtype::DeleteOne($ptid)) cls_message::show($re);

	adminlog('ɾ������λ����');
	cls_message::show('����ɾ�����',"?entry=$entry");
	
}else cls_message::show('������ļ�����');
