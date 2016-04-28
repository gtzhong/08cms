<?php
(!defined('M_COM') || !defined('M_ADMIN')) && exit('No Permission');
aheader();
if($re = $curuser->NoBackFunc('tpl')) cls_message::show($re);
if($action == 'mcatalogsedit'){
	backnav('mtconfigs','mcatalogs');
	$mcatalogs = cls_mcatalog::InitialInfoArray();
	if(!submitcheck('bsubmit')){
		$TitleStr = "�ռ���Ŀ����";
		$TitleStr .= " &nbsp; &nbsp;>><a href=\"?entry=$entry&action=mcatalogadd\" onclick=\"return floatwin('open_mtconfigsedit',this)\">��ӿռ���Ŀ</a>";
		tabheader($TitleStr,'mcatalogsedit',"?entry=$entry&action=$action",6);
		trcategory(array('ID','�ռ���Ŀ����|L','��̬Ŀ¼(����Ϊ��̬)|L','�����޶�','����','ɾ��','��ע|L'));
		
		foreach($mcatalogs as $k => $v) {
			$_views = array();
			$_views['title'] = $v['title'];
			$_views['dirname'] = $v['dirname'];
			$_views['maxucid'] = (int)$v['maxucid'];
			$_views['vieworder'] = (int)$v['vieworder'];
			$_views['remark'] = $v['remark'];
			echo "<tr class=\"txt\">\n".
			"<td class=\"txtC w30\">$k</td>\n".
			"<td class=\"txtL w120\">".OneInputText("fmdata[$k][title]",$_views['title'],25)."</td>\n".
			"<td class=\"txtL w150\">".OneInputText("fmdata[$k][dirname]",$_views['dirname'],15)."</td>\n".
			"<td class=\"txtC w80\">".OneInputText("fmdata[$k][maxucid]",$_views['maxucid'],4)."</td>\n".
			"<td class=\"txtC w80\">".OneInputText("fmdata[$k][vieworder]",$_views['vieworder'],4)."</td>\n".
			"<td class=\"txtC w30\"><input class=\"checkbox\" type=\"checkbox\" name=\"delete[$k]\" value=\"$k\" onclick=\"deltip(this,$no_deepmode)\"></td>\n".
			"<td class=\"txtL\">".OneInputText("fmdata[$k][remark]",$_views['remark'],50)."</td>\n".
			"</tr>";
		}
		tabfooter('bsubmit');
		a_guide('mcatalogsedit');
	}else{
		if(!empty($delete) && deep_allow($no_deepmode)){
			foreach($delete as $k){
				if(!cls_mcatalog::DeleteOne($k)){
					unset($fmdata[$k]);
				}
			}
		}
		if(!empty($fmdata)){
			foreach($fmdata as $k => $v){
				try {
					cls_mcatalog::ModifyOneConfig($v,$k);
				} catch (Exception $e){
					continue;
				}
			}
		}
		adminlog('�༭�ռ���Ŀ�����б�');
		cls_message::show('�ռ���Ŀ�޸����', "?entry=$entry&action=$action");
	}

}elseif($action == 'mcatalogadd'){
	echo _08_HTML::Title('��ӿռ���Ŀ');
	if(!submitcheck('bsubmit')){
		tabheader('��ӿռ���Ŀ','mcatalogadd',"?entry=$entry&action=$action",2,1,1);
		trbasic('*�ռ���Ŀ����','fmdata[title]','','text',array('validate' => makesubmitstr('fmdata[title]',1,0,4,30)));
		trbasic('��Ŀ��̬Ŀ¼','fmdata[dirname]','','text',array('validate' => makesubmitstr('fmdata[dirname]',0,0,2,30),'guide' => '��������˿ռ����ɾ�̬ʱ����Ŀ���ֶ�̬'));
		trbasic('���˷����������','fmdata[maxucid]',0,'text',array('w' => 2,'guide' => '��Ա�ڱ���Ŀ��������Ӹ��˷�������������0Ϊ������'));
		trbasic('��Ŀ��ע','fmdata[remark]','','text',array('w'=>50));
		tabfooter('bsubmit');
		a_guide('mcatalogdetail');
	}else{
		try {
			cls_mcatalog::ModifyOneConfig($fmdata,0);
		} catch (Exception $e){
			cls_message::show('�ռ���Ŀ���ʧ�ܣ�'.$e->getMessage());
		}
		adminlog('��ӿռ���Ŀ');
		cls_message::show('�ռ���Ŀ������', axaction(6,"?entry=$entry&action=mcatalogsedit"));
	}

}