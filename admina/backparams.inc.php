<?
(!defined('M_COM') || !defined('M_ADMIN')) && exit('No Permission');
aheader();
foreach(array('mconfigs','currencys','commus','channels',) as $k) $$k = cls_cache::Read($k);
if($action == 'bkparams'){
	backnav('backarea','bkparam');
	if($re = $curuser->NoBackFunc('bkconfig')) cls_message::show($re);
	if(!submitcheck('bmconfigs')){
		tabheader('�����̨����','cfview',"?entry=backparams&action=bkparams");
		trbasic('�ĵ��Զ�ժҪ����','mconfigsnew[autoabstractlength]',$mconfigs['autoabstractlength']);
		trbasic('�����̨�б�ÿҳ��ʾ��','mconfigsnew[atpp]',$mconfigs['atpp']);
		trbasic('�����̨��ʾ��Ϣͣ��','mconfigsnew[amsgforwordtime]',$mconfigs['amsgforwordtime'],'text',array('guide' => '��λ������'));
		trbasic('�ڵ����ý���ÿ����ʾ��','mconfigsnew[cnprow]',$mconfigs['cnprow'],'text',array('guide' => '�˲�������λ�ã�ģ����-->��Ŀ�ڵ����-->�ڵ���Ϸ���-->ѡ��ĳ������������-->��������Ŀ/��ϵ��(�ֶ�ָ��)  �е��б��'));
		trbasic('�����Զ�ƴ��','mconfigsnew[aeisablepinyin]',empty($mconfigs['aeisablepinyin']) ? 1 : 0,'radio');
		trbasic('���ø�������','mconfigsnew[aallowfloatwin]',empty($mconfigs['aallowfloatwin']) ? 0 : 1,'radio');
		tabfooter();
		if($curuser->info['isfounder']){
			tabheader('�����̨����Ȩ��');
			trbasic('������Ҫ�ܹ�����','',makeradio('mconfigsnew[no_deepmode]',array(0 => '�ر�',1 => '����'),empty($mconfigs['no_deepmode']) ? 0 : 1),'',array('guide'=>'�����ý���ʼ����Ȩ�ޣ��ڼܹ�����ģʽ�½�ֹ��ӻ�ɾ��ĳЩ��Ҫ�ܹ������������Ӱ��ϵͳ���ܣ�ƽʱ�뱣�ֿ���״̬��'));
			if(1 != @$mconfigs['cms_idkeep']){//ԭ�ȴ��ڹٷ�����ģʽ�Ļ��������ڴ�����
				trbasic('ϵͳ����ģʽ','',makeradio('mconfigsnew[cms_idkeep]',array(0 => '�ǿ���ģʽ',2 => '���ο���ģʽ'),empty($mconfigs['cms_idkeep']) ? 0 : 2),'',
				array('guide' => '�ǿ���ģʽ�����β�����Ҫ�ܹ���������ɾ�������ζ����ݱ��ֱ�Ӳ�������δ�漰������ο�����ǿ�ҽ���ʹ�ô�ģʽ��<br>
				���ο���ģʽ��������Ҫ�ܹ���������ɾ�����ɶԲ������ݱ����ֱ�Ӳ���������������������������ʼ����Ȩ�ޡ�
				'));
			}
			tabfooter();
		}
		tabheader('�����̨��¼����');
		trbasic('��¼����Դ������','mconfigsnew[amaxerrtimes]',$mconfigs['amaxerrtimes'],'text',array('guide'=>'���ձ�ʾ���޴��������������Ϊ3�������Թ�����������ʺš�'));
		trbasic('�����Զ��˳�ʱ��(����)','mconfigsnew[aminerrtime]',$mconfigs['aminerrtime'],'text',array('guide'=>'����Ϊ60���ӣ�ͬʱҲ�ǵ�¼ʧ������ʱ�䡣'));
		trbasic('�����̨����IP�б�','mconfigsnew[adminipaccess]',$mconfigs['adminipaccess'],'textarea',array('guide'=>'ÿ������һ�� IP����Ϊ������ַ��Ҳ���� IP ��ͷĳ�����ַ����ձ�ʾ�����Ƶ�¼��IP'));
		tabfooter('bmconfigs');
	}else{
		if($curuser->info['isfounder']){
			$mconfigsnew['no_deepmode'] = empty($mconfigsnew['no_deepmode']) ? 0 : 1;
			if(1 != @$mconfigs['cms_idkeep']){//ԭ�ȴ��ڹٷ�����ģʽ�Ļ��������ڴ�����	
				$mconfigsnew['cms_idkeep'] = empty($mconfigsnew['cms_idkeep']) ? 0 : 2;
			}	
		}
		$mconfigsnew['amaxerrtimes'] = max(1,intval($mconfigsnew['amaxerrtimes']));
		$mconfigsnew['aminerrtime'] = max(3,intval($mconfigsnew['aminerrtime']));
		$mconfigsnew['autoabstractlength'] = min(1000,max(10,intval($mconfigsnew['autoabstractlength'])));
		$mconfigsnew['atpp'] = max(5,intval($mconfigsnew['atpp']));
		$mconfigsnew['amsgforwordtime'] = max(0,intval($mconfigsnew['amsgforwordtime']));
		$mconfigsnew['cnprow'] = max(1,intval($mconfigsnew['cnprow']));
		$mconfigsnew['aeisablepinyin'] = empty($mconfigsnew['aeisablepinyin']) ? 1 : 0;
		saveconfig('view');
		adminlog('��վ����','ҳ����ģ������');
		cls_message::show('��վ�������',"?entry=backparams&action=bkparams");
	}
}elseif($action == 'mcparams'){
	backnav('mcenter','mcparam');
	if($re = $curuser->NoBackFunc('mcconfig')) cls_message::show($re);
	if(!submitcheck('bmconfigs')){
		tabheader('��Ա���Ĳ���','cfview',"?entry=backparams&action=mcparams");
		trbasic('��Ա����Ŀ¼','mconfigsnew[mc_dir]',$mconfigs['mc_dir'],'text',array('guide'=>'ϵͳ���õĻ�Ա����Ŀ¼Ϊadminm��Ϊ�˲�Ӱ���������������ο����Ļ�Ա������ʹ��������Ŀ¼��'));
		trbasic('��Ա������ʾ��Ϣͣ��(����)','mconfigsnew[mmsgforwordtime]',$mconfigs['mmsgforwordtime']);
		trbasic('��Ա�����б�ÿҳ��ʾ��','mconfigsnew[mrowpp]',$mconfigs['mrowpp']);
		trbasic('���˷��������������','mconfigsnew[maxuclassnum]',empty($mconfigs['maxuclassnum']) ? 0 : $mconfigs['maxuclassnum']);
		trbasic('���˷����ֽڳ�������','mconfigsnew[uclasslength]',$mconfigs['uclasslength']);
		trspecial('��Ա����LOGO',specialarr(array('type' => 'image','varname' => 'mconfigsnew[mcenterlogo]','value' => $mconfigs['mcenterlogo'],'guide' => '��ѳߴ� 260 X 50')));
		trbasic('���ø�������','mconfigsnew[mallowfloatwin]',empty($mconfigs['mallowfloatwin']) ? 0 : $mconfigs['mallowfloatwin'],'radio');
		setPermBar('��Ա�����й�Ȩ��','mconfigsnew[g_apid]',empty($mconfigs['g_apid']) ? 0 : $mconfigs['g_apid'], 'other', 'open', '����������Ļ�Ա�ſ��Խ���Ա����ί�и������˹���');
        tabfooter('bmconfigs');
	}else{
		$mconfigsnew['mc_dir'] = strtolower(trim(strip_tags($mconfigsnew['mc_dir'])));
		$mconfigsnew['mc_dir'] = empty($mconfigsnew['mc_dir']) ? 'adminm' : $mconfigsnew['mc_dir'];
		$mconfigsnew['mmsgforwordtime'] = max(0,intval($mconfigsnew['mmsgforwordtime']));
		$mconfigsnew['mrowpp'] = max(5,intval($mconfigsnew['mrowpp']));
		$mconfigsnew['g_apid'] = (int)$mconfigsnew['g_apid'];
		$mconfigsnew['uclasslength'] = min(30,max(4,intval($mconfigsnew['uclasslength'])));
		$mconfigsnew['maxuclassnum'] = max(0,intval($mconfigsnew['maxuclassnum']));
		$c_upload = cls_upload::OneInstance();
		$mconfigsnew['mcenterlogo'] = upload_s($mconfigsnew['mcenterlogo'],$mconfigs['mcenterlogo'],'image');
		if($k = strpos($mconfigsnew['mcenterlogo'],'#')) $mconfigsnew['mcenterlogo'] = substr($mconfigsnew['mcenterlogo'],0,$k);
		$c_upload->saveuptotal(1);
		saveconfig('view');
		adminlog('��վ����','ҳ����ģ������');
		cls_message::show('��վ�������',"?entry=backparams&action=mcparams");
	}
}

?>
