<?php
(!defined('M_COM') || !defined('M_ADMIN')) && exit('No Permission');
aheader();
if($re = $curuser->NoBackFunc('tpl')) cls_message::show($re);
foreach(array('mtpls','tpl_mconfigs','tpl_fields',) as $k) $$k = cls_cache::Read($k);
if($action == 'tplbase'){
	backnav('tpl','base');
	if(!submitcheck('bsubmit')){
		tabheader('ģ���������','tplbase',"?entry=$entry&action=$action");
		trbasic('ѡ��ģ��','mconfigsnew[templatedir]',makeoption(listTplpacks(),$mconfigs['templatedir']),'select',array('guide'=>'ÿ��ģ��һ��Ŀ¼,λ��templateĿ¼��'));
		trbasic('ģ��cssĿ¼','mconfigsnew[css_dir]',empty($mconfigs['css_dir']) ? 'css' : $mconfigs['css_dir'],'text',array('guide'=>'ֻ��Ҫ��дĿ¼��,λ��template/ģ��Ŀ¼/��'));
		trbasic('ģ��jsĿ¼','mconfigsnew[js_dir]',empty($mconfigs['js_dir']) ? 'js' : $mconfigs['js_dir'],'text',array('guide'=>'ֻ��Ҫ��дĿ¼��,λ��template/ģ��Ŀ¼/��'));
		trbasic('ģ���ʶ��SQL�������ڱ���','',makeradio('mconfigsnew[tagttlplus]',array(0 => '����ԭֵ',2 => '2��',3 => '3��',4 => '4��',-1 => '�رձ�ʶ��SQL����',),@$mconfigs['tagttlplus']),'',array('guide' => '�����ݿ��ѯѹ���Ƚϴ��ʱ��Ҫ��ϵ͵����,���ʵ����ߡ�����չ���濪��ʱ��Ч��'));
		tabfooter();
		
		tabheader('ģ�����ѡ��');
		trbasic('ϵͳ��Ϊ����״̬','debugtagnew',$debugtag,'radio',array('guide'=>'����ģʽ��ģ����޸ļ�ʱ���£���̬ҳ�漴ʱ���£�ͣ��ҳ�滺�棬��Ա���İ�������ʾδ������ĵ�����Ϣ��<br>�����û�Ӱ����ͬ����IP���û�����[<a href="?entry=tplconfig&action=cleardebug">����</a>]�ر����з���IP�ĵ���ģʽ��'));//�������ã������ʶ��ʾ����ʽ��������̬ҳ��ÿ��ˢ�¸���
		trbasic('ǰ̨ҳ����ʾ��ѯͳ��','mconfigsnew[viewdebug]',empty($mconfigs['viewdebug']) ? 0 : $mconfigs['viewdebug'],'radio',array('guide'=>'ǰ̨ҳ��ײ���ʾ��ѯͳ����Ϣ�����������ַ���: querys:15,in:0.0292s'));
		trbasic('ǰ̨������Ϣ��ʾ��ʽ','',makeradio('mconfigsnew[viewdebugmode]',array(0 => '��HTMLע����ʾ(�Ƽ�)','direct' => 'ֱ����ʾ(����ʹһЩcss��ʾ������)'),@$mconfigs['viewdebugmode']),'',array('guide' => '����Ϣͬʱ����[��ѯͳ��]��[����ģ��]����ʾ��ʽ��[��HTMLע����ʾ]��ǰ̨��������Ҫ���������[�鿴Դ����]�ſɿ�����'));
		tabfooter('bsubmit');
		a_guide('tplbase');
	}else{
		//����ģ��Ŀ¼
		$mconfigsnew['templatedir'] = trim(strip_tags($mconfigsnew['templatedir']));//ָ���µ�ģ���ļ��У����Կ����в�ͬ��ģ����ʽ
		if(empty($mconfigsnew['templatedir']) || preg_match("/[^a-zA-Z_0-9]+/",$mconfigsnew['templatedir'])){
			cls_message::show('ģ��Ŀ¼���Ϲ淶',M_REFERER);
		}
		if($mconfigs['templatedir']!=$mconfigsnew['templatedir']){ //������ģ��Ŀ¼��ִ��
			mmkdir(M_ROOT.'template/'.$mconfigsnew['templatedir']);
			clear_dir(cls_Parse::TplCacheDirFile('')); //ģ�建��
		}
		//��class cls_CacheFile���桰//��Ժ������������⴦��,��������Ϊȫ�ֱ����ſɸ���
		$_tpldir = $mconfigsnew['templatedir'];
		cls_env::SetG('templatedir',$_tpldir);
		
		$tplPacks = listTplpacks(''); //�����û���ģ��,Ϊ�̳���Ϊ��
		$mconfigsnew['templatebase'] = empty($tplPacks[$_tpldir]) ? '' : $tplPacks[$_tpldir];
		cls_env::SetG('templatebase',$mconfigsnew['templatebase']);
		
		$mconfigsnew['css_dir'] = trim(strip_tags($mconfigsnew['css_dir']));
		if(empty($mconfigsnew['css_dir']) || preg_match("/[^a-zA-Z_0-9]+/",$mconfigsnew['css_dir'])){
			cls_message::show('ģ��cssĿ¼���Ϲ淶',M_REFERER);
		}
		
		$mconfigsnew['js_dir'] = trim(strip_tags($mconfigsnew['js_dir']));
		if(empty($mconfigsnew['js_dir']) || preg_match("/[^a-zA-Z_0-9]+/",$mconfigsnew['js_dir'])){
			cls_message::show('ģ��jsĿ¼���Ϲ淶',M_REFERER);
		}
		
		//���õ���ģʽ
		if($onlineip && $debugtagnew != $debugtag){
			$ips = explode(',',$debugtag_ips);
			if($debugtagnew){
				if(count($ips) > 30) $ips = array_slice($ips, -30);
				in_array($onlineip, $ips) || $ips[] = $onlineip;
			}elseif(in_array($onlineip, $ips)){
				$key = array_search($onlineip, $ips); 
				unset($ips[$key]);
			}
			$mconfigsnew['debugtag_ips'] = empty($ips) ? '' : implode(',',$ips);
		}
		empty($debugtagnew) ? mclearcookie('debugtag') : msetcookie('debugtag',1);
		
		saveconfig('tpl');
		adminlog('ģ������','ģ���������');
		cls_message::show('ģ���������',M_REFERER);
	}

}elseif($action == 'tplfield'){
	backnav('tpl','tplfield');
	if(!submitcheck('bsubmit')){
		tabheader('ǰ̨ģ�����','tplbase',"?entry=$entry&action=$action");
		trspecial('վ��Logo',specialarr(array('type' => 'image','varname' => 'mconfigsnew[cmslogo]','value' => @$tpl_mconfigs['cmslogo'],'guide' => 'ǰ̨������ʽ��{$cmslogo}',)));
		trbasic('վ��SEO����','mconfigsnew[cmstitle]',@$tpl_mconfigs['cmstitle'],'text',array('w'=>50,'guide' => 'ǰ̨������ʽ��{$cmstitle}',));
		trbasic('վ��SEO�ؼ���','mconfigsnew[cmskeyword]',@$tpl_mconfigs['cmskeyword'],'text',array('w'=>50,'guide' => 'ǰ̨������ʽ��{$cmskeyword}',));
		trbasic('վ��SEO����','mconfigsnew[cmsdescription]',@$tpl_mconfigs['cmsdescription'],'textarea',array('guide' => 'ǰ̨������ʽ��{$cmsdescription}',));
		trbasic('��վICP����','mconfigsnew[cms_icpno]',@$tpl_mconfigs['cms_icpno'],'text',array('w'=>50,'guide' => 'ǰ̨������ʽ��{$cms_icpno}',));
		trbasic('����֤��bazs.cert�ļ�','mconfigsnew[bazscert]',@$tpl_mconfigs['bazscert'],'text',array('w'=>50,'guide' => 'ǰ̨������ʽ��{$bazscert}',));
		trbasic('��Ȩ��Ϣ','mconfigsnew[copyright]',@$tpl_mconfigs['copyright'],'textarea',array('guide' => 'ǰ̨������ʽ��{$copyright}',));
		trbasic('������ͳ�ƴ���','mconfigsnew[cms_statcode]',@$tpl_mconfigs['cms_statcode'],'textarea',array('guide' => 'ǰ̨������ʽ��{$cms_statcode}',));
		tabfooter();

		tabheader("�Զ�ģ����� &nbsp;>><a href=\"?entry=$entry&action=tpl_fieldsedit\" onclick=\"return floatwin('open_channeledit',this)\">�Զ���������</a>");
		foreach($tpl_fields as $k => $v){
		    /*# ��ʱ����΢�Ŷ�ά���ֶ�
            if ( in_array($k, array('weixin', 'wxewmpic')) )
            {
                continue;
            }*/
			$var = "user_$k";
			switch($v['type']){
				case 'image':
					trspecial($v['cname'],specialarr(array('type' => 'image','varname' => "mconfigsnew[$var]",'value' => @$tpl_mconfigs[$var],'guide' => 'ǰ̨������ʽ��{$'.$var.'}',)));
				break;
				case 'text':
					trbasic($v['cname'],"mconfigsnew[$var]",@$tpl_mconfigs[$var],'text',array('w'=>50,'guide' => 'ǰ̨������ʽ��{$'.$var.'}',));
				break;
				case 'multitext':
					trbasic($v['cname'],"mconfigsnew[$var]",@$tpl_mconfigs[$var],'textarea',array('guide' => 'ǰ̨������ʽ��{$'.$var.'}',));
				break;
			
			}
		}
		tabfooter('bsubmit');
		a_guide('tplfield');
	}else{
		$c_upload = cls_upload::OneInstance();
		
		$mconfigsnew['cmslogo'] = upload_s($mconfigsnew['cmslogo'],@$tpl_mconfigs['cmslogo'],'image');
		if($k = strpos($mconfigsnew['cmslogo'],'#')) $mconfigsnew['cmslogo'] = substr($mconfigsnew['cmslogo'],0,$k);
		//�Զ����ֶ�
		foreach($tpl_fields as $k => $v){
			$var = "user_$k";
			switch($v['type']){
				case 'image':
					$mconfigsnew[$var] = upload_s($mconfigsnew[$var],@$tpl_mconfigs[$var],'image');
					if($k = strpos($mconfigsnew[$var],'#')) $mconfigsnew[$var] = substr($mconfigsnew[$var],0,$k);
				break;
				case 'text':
				case 'multitext':
					$mconfigsnew[$var] = trim($mconfigsnew[$var]);
				break;
			
			}
		}
		$c_upload->closure(2, 0, 'mconfigs');
		saveconfig('tpl');
		adminlog('ģ������','ǰ̨ģ�����');
		cls_message::show('ģ������������',M_REFERER);
	}

}elseif($action == 'system'){
	backnav('bindtpl','system');
	$sptpls = cls_cache::Read('sptpls');
	if(!submitcheck('bsubmit')){
		
		tabheader("ϵͳ��ҳģ��",'tplbase',"?entry=$entry&action=$action");
		$index_items = array (
		  'index' => 
		  array (
			'cname' => 'ϵͳ��ҳģ��',
			'tpclass' => 'index',
			'tctitle' => '��ҳ',
		  ),
		  'm_index' => 
		  array (
			'cname' => '��ԱƵ����ҳģ��',
			'tpclass' => 'marchive',
			'tctitle' => '��Ա���',
		  ),
		  'rss_index' => 
		  array (
			'cname' => '��ҳRSSģ��',
			'tpclass' => 'xml',
			'tctitle' => 'RSS/SiteMap',
		  ),
		);
		foreach($index_items as $k => $v){
			trbasic($v['cname'],"fmdata[$k]",makeoption(array('' => '������') + cls_mtpl::mtplsarr($v['tpclass']),empty($sptpls[$k]) ? '' : $sptpls[$k]),'select',array('guide' => cls_mtpl::mtplGuide($v['tpclass'])));
		
		}
		tabfooter();
		
		tabheader('����ҳ��ģ��');
		$sp_items = array (
		  'msearch' => 
		  array (
			'cname' => 'ȫģ�ͻ�Ա����ҳ',
			'link' => '{$cms_abs}msearch.php',
		  ),
		  'login' => 
		  array (
			'cname' => '��Ա��¼ҳ��',
			'link' => '{$cms_abs}login.php',
		  ),
		  'message' => 
		  array (
			'cname' => 'ϵͳ��ʾ��Ϣģ��',
			'link' => '��ʾ��Ϣ(ϵͳ����)',
		  ),
		  'jslogin' => 
		  array (
			'cname' => '��Ա(δ)��¼js����ģ��',
			'link' => '{$cms_abs}login.php?mode=js',
		  ),
		  'jsloginok' => 
		  array (
			'cname' => '��Ա(��)��¼js����ģ��',
			'link' => '{$cms_abs}login.php?mode=js',
		  ),
		  'down' => 
		  array (
			'cname' => '�������ظ���ҳ',
			'link' => 'ͨ��ģ���ʶ����',
		  ),
		  'flash' => 
		  array (
			'cname' => 'FLASH���Ÿ���ҳ',
			'link' => 'ͨ��ģ���ʶ����',
		  ),
		  'media' => 
		  array (
			'cname' => '��Ƶ���Ÿ���ҳ',
			'link' => 'ͨ��ģ���ʶ����',
		  ),
		  'vote' => 
		  array (
			'cname' => 'ͶƱ�鿴ҳ��',
			'link' => '{$cms_abs}vote.php?action=view&vid={$vid}',
		  ),
		);
		foreach($sp_items as $k => $v){
			trbasic($v['cname'],"fmdata[$k]",makeoption(array('' => '������') + cls_mtpl::mtplsarr('special'),empty($sptpls[$k]) ? '' : $sptpls[$k]),'select',array('guide' => cls_mtpl::mtplGuide('special')."���������ӣ�$v[link]��"));
		}
		tabfooter('bsubmit');
		a_guide('bindindex');
	}else{
		cls_CacheFile::Save($fmdata,'sptpls','sptpls');
		adminlog('ģ���','ϵͳģ���');
		cls_message::show('ϵͳģ������',M_REFERER);
	}
}elseif($action == 'cleardebug'){
	$mconfigsnew['debugtag_ips'] = '';
	saveconfig('tpl');
	cls_message::show('�ر�������Դ�ĵ���ģʽ',M_REFERER);
}elseif($action == 'tpl_fieldadd'){
	$tpl_fields = cls_cache::Read('tpl_fields');
	echo "<title>���ģ���Զ�����</title>";
	if(!submitcheck('bsubmit')){
		$submitstr = '';
		$typesarr = array('text' => '�����ı�','multitext' => '�����ı�','image' => 'ͼƬ�ϴ�',);
		tabheader('���ģ���Զ�����','tpl_fieldadd',"?entry=$entry&action=$action",2,0,1);
		trbasic('��������','fmdata[cname]','','text',array('validate'=>makesubmitstr('fmdata[cname]',1,1,4,30)));
		trbasic('����Ӣ�ı�ʶ','fmdata[ename]','','text',array('validate'=>makesubmitstr('fmdata[ename]',1,'tagtype',0,30),'guide' => 'ģ���н�ʹ��{$user_Ӣ�ı�ʶ}�����ñ�������'));
		trbasic('��������','fmdata[type]',makeoption($typesarr),'select');
		tabfooter('bsubmit','���');
		a_guide('tpl_fieldadd');//?????????????
	} else {
		if(!($fmdata['cname'] = trim(strip_tags($fmdata['cname'])))) cls_message::show('��������⣡',M_REFERER);
		if(!($fmdata['ename'] = trim($fmdata['ename']))) cls_message::show('������ʶ��',M_REFERER);
		if(preg_match("/[^a-zA-Z_0-9]+|^[0-9_]+/",$fmdata['ename'])) cls_message::show('������ʶ���Ϲ淶',M_REFERER);
		$enamearr = array();foreach($tpl_fields as $k => $v) $enamearr[] = $k;
		if(in_array($fmdata['ename'],$enamearr)) cls_message::show('������ʶ��ռ��',M_REFERER);
		$tpl_fields[$fmdata['ename']] = array('cname' => $fmdata['cname'],'type' => $fmdata['type'],'vieworder' => 0);
		cls_CacheFile::Save($tpl_fields,'tpl_fields','tpl_fields');
		adminlog('���ģ���Զ�����');
		cls_message::show('ģ���Զ�����������',axaction(6,"?entry=$entry&action=tpl_fieldsedit"));
	}
}elseif($action == 'tpl_fieldsedit'){//ǿ�Ƽ�user_
	foreach(array('tpl_fields','tpl_mconfigs',) as $k) $$k = cls_cache::Read($k);
	echo "<title>ǰ̨ģ���Զ�����</title>";
	if(!submitcheck('bsubmit')){
		$typesarr = array('text' => '�����ı�','multitext' => '�����ı�','image' => 'ͼƬ�ϴ�',);
		tabheader("ǰ̨ģ���Զ�����&nbsp; &nbsp; >><a href=\"?entry=$entry&action=tpl_fieldadd\" onclick=\"return floatwin('open_cntplsedit',this)\">���</a>",'cntplsedit',"?entry=$entry&action=$action",'10');
		trcategory(array('ɾ��',array('��������','txtL'),'����','��������',array('ǰ̨������ʽ','txtL')));
		$ii = 0;
		foreach($tpl_fields as $k => $v){
			echo "<tr class=\"txt\">".
				"<td class=\"txtC w40\"><input class=\"checkbox\" type=\"checkbox\" name=\"delete[$k]\" value=\"$k\" onclick=\"deltip()\"></td>\n".
				"<td class=\"txtL\"><input type=\"text\" size=\"30\" maxlength=\"30\" name=\"fmdata[$k][cname]\" value=\"$v[cname]\"></td>\n".
				"<td class=\"txtC w40\"><input type=\"text\" size=\"4\" maxlength=\"4\" name=\"fmdata[$k][vieworder]\" value=\"$v[vieworder]\"></td>\n".
				"<td class=\"txtC w60\">".@$typesarr[$v['type']]."</td>\n".
				"<td class=\"txtL w120\">{\$user_$k}</td>\n".
				"</tr>\n";
		}
		tabfooter('bsubmit');
		a_guide('tpl_fieldsedit');
	}else{
		if(!empty($delete)){
			foreach($delete as $k){
				unset($tpl_fields[$k]);
				unset($tpl_mconfigs["user_$k"]);
				unset($fmdata[$k]);
			}
			cls_CacheFile::Save($tpl_mconfigs,'tpl_mconfigs','tpl_mconfigs');
			cls_CacheFile::Update('mconfigs');//��Ҫ�ڴ˹����и���btags
		}
		
		if(!empty($fmdata)){
			foreach($fmdata as $k => $v){
				$v['cname'] = trim(strip_tags($v['cname']));
				$v['cname'] = $v['cname'] ? $v['cname'] : $tpl_fields[$k]['cname'];
				$v['vieworder'] = max(0,intval($v['vieworder']));
				foreach(array('cname','vieworder',) as $var) $tpl_fields[$k][$var] = $v[$var];
			}
			adminlog('�༭ǰ̨ģ���Զ�����');
			cls_Array::_array_multisort($tpl_fields);
		}
		cls_CacheFile::Save($tpl_fields,'tpl_fields','tpl_fields');
		cls_message::show('�Զ������޸����',axaction(6,"?entry=$entry&action=$action"));
	}
}elseif($action == 'tplchannel'){
	foreach(array('channels','arc_tpl_cfgs','arc_tpls',) as $k) $$k = cls_cache::Read($k);
	if(empty($chid)){
		backnav('bindtpl','channel');
		tabheader("��ģ�Ͱ�
		 &nbsp; &nbsp;>><a href=\"?entry=$entry&action=tplcatalog\">����Ŀ��</a>
		 &nbsp; &nbsp;>><a href=\"?entry=$entry&action=arc_tplsedit\" onclick=\"return floatwin('open_channeledit',this)\">�ĵ�ģ�巽��</a>
		 &nbsp; &nbsp;".cls_mtpl::mtplGuide('archive',true));
		trcategory(array('ID',array('ģ������','txtL'),'����','����ҳ','����ҳ',array('�ĵ�ģ�巽��/����','txtL')));
		foreach($channels as $k => $v){
			$tid = empty($arc_tpl_cfgs[$k]) ? 0 : $arc_tpl_cfgs[$k];
			if(empty($arc_tpls[$tid])) $tid = 0;
			$namestr = $tid ? $tid.'-'.$arc_tpls[$tid]['cname']." &nbsp; &nbsp;>><a href=\"?entry=$entry&action=arc_tpldetail&tid=$tid\" onclick=\"return floatwin('open_cntplsedit',this)\">����</a>" : '-';
			$addnum = !$tid || empty($arc_tpls[$tid]['addnum']) ? 0 : $arc_tpls[$tid]['addnum'];
			$searchs = !$tid || empty($arc_tpls[$tid]['search']) ? 0 : count($arc_tpls[$tid]['search']);
			echo "<tr class=\"txt\">".
				"<td class=\"txtC w40\">$k</td>\n".
				"<td class=\"txtL\">$v[cname]</td>\n".
				"<td class=\"txtC w40\"><a href=\"?entry=$entry&action=$action&chid=$k\" onclick=\"return floatwin('open_channeledit',this)\">����</a></td>\n".
				"<td class=\"txtC w40\">$addnum</td>\n".
				"<td class=\"txtC w40\">$searchs</td>\n".
				"<td class=\"txtL\">$namestr</td>\n".
				"</tr>\n";
		}
		tabfooter();
	}else{
		echo "<title>��ģ�Ͱ��ĵ�ģ��</title>";
		if(!($channel = $channels[$chid])) cls_message::show('��ָ����ȷ���ĵ�ģ�ͣ�');
		$tid = empty($arc_tpl_cfgs[$chid]) ? 0 : $arc_tpl_cfgs[$chid];
		if(!submitcheck('bsubmit')){
			tabheader("[$channel[cname]]ģ������",'channel',"?entry=$entry&action=$action&chid=$chid");
			$na = array(0 => '������',);foreach($arc_tpls as $k => $v) $na[$k] = $v['cname']."($k)";
			trbasic('�ĵ�ģ�巽��','fmdata[tid]',makeoption($na,$tid),'select',array('guide' => '�ĵ�����ʹ��������Ŀ�󶨵�ģ�巽������Ŀδ��ģ��Ļ�����Ĭ��ʹ������ģ�Ͱ󶨵�ģ�巽��<br>ģ�巽��ָ��������ҳ�������б����õ�ģ��,��������ģ������->�ĵ�ģ��->�ĵ�ģ�巽��'));
			tabfooter('bsubmit');
			a_guide('tplchannel');
		}else{
			$arc_tpl_cfgs[$chid] = empty($fmdata['tid']) ? 0 : intval($fmdata['tid']);
			foreach($arc_tpl_cfgs as $k => $v) if(empty($channels[$k])) unset($arc_tpl_cfgs[$k]);
			cls_CacheFile::Save($arc_tpl_cfgs,'arc_tpl_cfgs','arc_tpl_cfgs');
			adminlog('��ϸ�޸��ĵ�ģ��');
			cls_message::show('ģ���޸����',axaction(6,"?entry=$entry&action=$action"));
		}
	}
}elseif($action == 'tplcatalog'){
	foreach(array('catalogs','ca_tpl_cfgs','arc_tpls',) as $k) $$k = cls_cache::Read($k);
	if(empty($caid)){
		backnav('bindtpl','channel');
		tabheader(">><a href=\"?entry=$entry&action=tplchannel\">��ģ�Ͱ�</a>
		 &nbsp; &nbsp;����Ŀ��
		 &nbsp; &nbsp;>> <a href=\"?entry=$entry&action=arc_tplsedit\" onclick=\"return floatwin('open_channeledit',this)\">�ĵ�ģ�巽��</a>
		 &nbsp; &nbsp;".cls_mtpl::mtplGuide('archive',true));
		trcategory(array('ID',array('��Ŀ����','txtL'),'ģ��/��̬��ʽ','����ҳ','����ҳ',array('�ĵ�ģ�巽��/����','txtL')));
		foreach($catalogs as $k => $v){
			$tid = empty($ca_tpl_cfgs[$k]) ? 0 : $ca_tpl_cfgs[$k];
			if(empty($arc_tpls[$tid])) $tid = 0;
			$namestr = $tid ? $tid.'-'.$arc_tpls[$tid]['cname']." &nbsp; &nbsp;>><a href=\"?entry=$entry&action=arc_tpldetail&tid=$tid\" onclick=\"return floatwin('open_cntplsedit',this)\">����</a>" : '-';
			$addnum = !$tid || empty($arc_tpls[$tid]['addnum']) ? 0 : $arc_tpls[$tid]['addnum'];
			$searchs = !$tid || empty($arc_tpls[$tid]['search']) ? 0 : count($arc_tpls[$tid]['search']);
			$titlestr = empty($v['level']) ? "<b>$v[title]</b>" : str_repeat('&nbsp; &nbsp; &nbsp; ',$v['level']).$v['title'];
			echo "<tr class=\"txt\">".
				"<td class=\"txtC w40\">$k</td>\n".
				"<td class=\"txtL\">$titlestr</td>\n".
				"<td class=\"txtC\"><a href=\"?entry=$entry&action=$action&caid=$k\" onclick=\"return floatwin('open_channeledit',this)\">����</a></td>\n".
				"<td class=\"txtC w40\">$addnum</td>\n".
				"<td class=\"txtC w40\">$searchs</td>\n".
				"<td class=\"txtL\">$namestr</td>\n".
				"</tr>\n";
		}
		tabfooter();
	}else{
		echo "<title>����Ŀ���ĵ�ģ��</title>";
		if(!($catalog = $catalogs[$caid])) cls_message::show('��ָ����ȷ����Ŀ��');
		$tid = empty($ca_tpl_cfgs[$caid]) ? 0 : $ca_tpl_cfgs[$caid];
		if(!submitcheck('bsubmit')){
			tabheader("[$catalog[title]]ģ������",'channel',"?entry=$entry&action=$action&caid=$caid");
			$na = array(0 => '������',);foreach($arc_tpls as $k => $v) $na[$k] = $v['cname']."($k)";
			trbasic('�ĵ�ģ�巽��','fmdata[tid]',makeoption($na,$tid),'select',array('guide' => '�ĵ�����ʹ��������Ŀ�󶨵�ģ�巽������Ŀδ��ģ��Ļ�����Ĭ��ʹ������ģ�Ͱ󶨵�ģ�巽��<br>ģ�巽��ָ��������ҳ�������б����õ�ģ��,��������ģ������->�ĵ�ģ��->�ĵ�ģ�巽��'));
			trbasic('�ĵ�ҳ��̬�����ʽ','fmdata[customurl]',$catalog['customurl'],'text',array('guide'=>'����ΪĬ�ϸ�ʽ��{$topdir}������ĿĿ¼��{$cadir}������ĿĿ¼��{$y}�� {$m}�� {$d}�� {$h}ʱ {$i}�� {$s}�� {$chid}ģ��id  {$aid}�ĵ�id {$page}��ҳҳ�� {$addno}����ҳid��id֮�佨���÷ָ���_��-���ӡ�','w'=>50));
			tabfooter('bsubmit');
			a_guide('tplcatalog');
		}else{
			$ca_tpl_cfgs[$caid] = empty($fmdata['tid']) ? 0 : intval($fmdata['tid']);
			foreach($ca_tpl_cfgs as $k => $v) if(empty($catalogs[$k])) unset($ca_tpl_cfgs[$k]);
			cls_CacheFile::Save($ca_tpl_cfgs,'ca_tpl_cfgs','ca_tpl_cfgs');
			
			$fmdata['customurl'] = preg_replace("/^\/+/",'',trim($fmdata['customurl']));
			$db->query("UPDATE {$tblprefix}catalogs SET
				customurl='$fmdata[customurl]'
				WHERE caid='$caid'");
			cls_CacheFile::Update('catalogs');
			
			adminlog('����Ŀ���ĵ�����ҳģ��');
			cls_message::show('�ĵ�����ҳģ������',axaction(6,"?entry=$entry&action=$action"));
		}
	}
}elseif($action == 'tplmchannel'){
	backnav('bindtpl','mchannel');
	$mchannels = cls_cache::Read('mchannels');
	$tplcfgs = cls_cache::Read('tplcfgs');
	if(!submitcheck('bmchannel')){
		tabheader("��Աģ���&nbsp; &nbsp; ".cls_mtpl::mtplGuide('marchive',true),'mchannel',"?entry=$entry&action=$action");
		trcategory(array('ID',array('��Աģ��','txtL'),array('ע��ģ��','txtL'),array('������Աģ��','txtL'),array('��������ҳ1ģ��','txtL'),array('����ģ��','txtL'),));
		foreach($mchannels as $k => $v){
			$tplcfg = empty($tplcfgs['member'][$k]) ? array() : $tplcfgs['member'][$k];
			$sel_style = " style='width:150px; height:23px;' ";
			echo "<tr class=\"txt\">".
				"<td class=\"txtC w40\">$k</td>\n".
				"<td class=\"txtL\">$v[cname]</td>\n".
				"<td class=\"txtL\">".makeselect("tplcfgnew[$k][addtpl]",makeoption(array('' => '������') + cls_mtpl::mtplsarr('marchive'),@$tplcfg['addtpl']),$sel_style)."</td>\n".
				"<td class=\"txtL\">".makeselect("tplcfgnew[$k][srhtpl]",makeoption(array('' => '������') + cls_mtpl::mtplsarr('marchive'),@$tplcfg['srhtpl']),$sel_style)."</td>\n".
				"<td class=\"txtL\">".makeselect("tplcfgnew[$k][srhtpl1]",makeoption(array('' => '������') + cls_mtpl::mtplsarr('marchive'),@$tplcfg['srhtpl1']),$sel_style)."</td>\n".
				"<td class=\"txtL\">".makeselect("tplcfgnew[$k][bktpl]",makeoption(array('' => '������') + cls_mtpl::mtplsarr('marchive'),@$tplcfg['bktpl']),$sel_style)."</td>\n".
				"</tr>\n";
		}
		tabfooter('bmchannel');
		a_guide('tplmchannel');
	}else{
		$vars = array('addtpl','srhtpl','srhtpl1','bktpl',);
		$tplcfgs['member'] = array();
		foreach($mchannels as $k => $v){
			foreach($vars as $var) @$tplcfgnew[$k][$var] && $tplcfgs['member'][$k][$var] = $tplcfgnew[$k][$var];
		}
		cls_CacheFile::Save($tplcfgs,'tplcfgs','tplcfgs');
		adminlog('��ϸ�޸Ļ�Աģ��');
		cls_message::show('ģ���޸����',M_REFERER);
	}
}elseif($action == 'tplfcatalog'){
	backnav('bindtpl','fcatalog');
	$fcatalogs = cls_cache::Read('fcatalogs');
	$tplcfgs = cls_cache::Read('tplcfgs');
	if(!submitcheck('bfcatalog')){
		tabheader("��������ģ���&nbsp; &nbsp; ".cls_mtpl::mtplGuide('freeinfo',true),'fcatalog',"?entry=$entry&action=tplfcatalog");
		foreach($fcatalogs as $k => $v){
			if(!empty($v['ftype'])) continue;
			$tplcfg = empty($tplcfgs['farchive'][$k]) ? array() : $tplcfgs['farchive'][$k];
			trbasic($k.'.'.$v['title'],"tplcfgnew[$k][arctpl]",makeoption(array('' => '������') + cls_mtpl::mtplsarr('freeinfo'),@$tplcfg['arctpl']),'select');
		}
		tabfooter('bfcatalog');
		a_guide('tplfcatalog');
	}else{
		$tplcfg = array();
		$vars = array('arctpl',);
		foreach($fcatalogs as $k => $v){
			if(!empty($v['ftype'])) continue;
			foreach($vars as $var){
				empty($tplcfgnew[$k][$var]) ||$tplcfg[$k][$var] = $tplcfgnew[$k][$var];
			}
		}
		$tplcfgs['farchive'] = $tplcfg;
		cls_CacheFile::Save($tplcfgs,'tplcfgs','tplcfgs');
		adminlog('�󶨸�������ҳģ��');
		cls_message::show('����ģ������',M_REFERER);
	}
}elseif($action == 'arc_tpladd'){
	$arc_tpls = cls_cache::Read('arc_tpls');
	echo "<title>����ĵ�ģ�巽��</title>";
	if(!submitcheck('bsubmit')){
		$submitstr = '';
		tabheader('����ĵ�ģ�巽��','arc_tpladd',"?entry=$entry&action=$action",2,0,1);
		trbasic('��������','fmdata[cname]','','text',array('validate'=>makesubmitstr('fmdata[cname]',1,1,4,30)));
		tabfooter('bsubmit','���');
		a_guide('arc_tpladd');//?????????????
	} else {
		if(!($fmdata['cname'] = trim(strip_tags($fmdata['cname'])))) cls_message::show('��������⣡',M_REFERER);
		$tid = auto_insert_id('arc_tpls');
		$arc_tpls[$tid] = array('cname' => $fmdata['cname'],'addnum' =>0,'chid' => 0,'vieworder' => 0,'cfg' => array(),);
		cls_CacheFile::Save($arc_tpls,'arc_tpls','arc_tpls');
		adminlog('����ĵ�ģ�巽��');
		cls_message::show('�ĵ�ģ�巽�������ɣ��������һ������',"?entry=$entry&action=arc_tpldetail&tid=$tid");
	}
}elseif($action == 'arc_tpldetail' && $tid){
	foreach(array('arc_tpls','channels',) as $k) $$k = cls_cache::Read($k);
	if(!($arc_tpl = @$arc_tpls[$tid])) cls_message::show('��ѡ���ĵ�ģ�巽��');
	echo "<title>�ĵ�ģ�巽�� - $arc_tpl[cname]</title>";
	if(!submitcheck('bsubmit')){
		tabheader("�ĵ�ģ������&nbsp;&nbsp;[$arc_tpl[cname]]",'cntpldetail',"?entry=$entry&action=$action&tid=$tid");
		$arr = array();for($i = 0;$i <= $max_addno;$i ++) $arr[$i] = $i;
		$addnum = empty($arc_tpl['addnum']) ? 0 : $arc_tpl['addnum'];
		trbasic('����ҳ����','',makeradio('fmdata[addnum]',$arr,$addnum),'');
		trbasic('�ĵ������б�ģ��','fmdata[search][0]',makeoption(array('' => '������') + cls_mtpl::mtplsarr('cindex'),@$arc_tpl['search'][0]),'select',array('guide' => cls_mtpl::mtplGuide('cindex')));
		trbasic('��������ҳ1ģ��','fmdata[search][1]',makeoption(array('' => '������') + cls_mtpl::mtplsarr('cindex'),@$arc_tpl['search'][1]),'select',array('guide' => cls_mtpl::mtplGuide('cindex')));
		tabfooter();
		for($i = 0;$i <= $max_addno;$i ++){
			tabheader(($i ? '����ҳ'.$i : '����ҳ').'����'.viewcheck(array('name' =>'viewdetail','title' => '��ϸ','value' => $i > $addnum ? 0 : 1,'body' =>$actionid.'tbodyfilter'.$i)));
			echo "<tbody id=\"{$actionid}tbodyfilter$i\" style=\"display:".($i > $addnum ? 'none' : '')."\">";
			trbasic('ҳ��ģ��',"fmdata[cfg][$i][tpl]",makeoption(array('' => '������') + cls_mtpl::mtplsarr('archive'),empty($arc_tpl['cfg'][$i]['tpl']) ? '' : $arc_tpl['cfg'][$i]['tpl']),'select',array('guide' => cls_mtpl::mtplGuide('archive')));
			trbasic('�Ƿ����ɾ�̬','',makeradio("fmdata[cfg][$i][static]",array(0 => '��ϵͳ������',1 => '���ֶ�̬'),empty($arc_tpl['cfg'][$i]['static']) ? 0 : $arc_tpl['cfg'][$i]['static']),'');
			trbasic('��̬��ʽ��addno���ֵ',"fmdata[cfg][$i][addno]",empty($arc_tpl['cfg'][$i]['addno']) ? '' : $arc_tpl['cfg'][$i]['addno'],'text',array('guide'=>'�ĵ�����ҳ��̬URL��{$addno}������ֵ������ʱ����ֵ���򣺻���ҳ=>�գ�����ҳ=>��ǰ����ҳ�������'.($i ? $i : '').'��',));
			trbasic('��̬��������(����)',"fmdata[cfg][$i][period]",empty($arc_tpl['cfg'][$i]['period']) ? '' : $arc_tpl['cfg'][$i]['period'],'text',array('guide'=>'������ϵͳ������','w'=>4));
			trbasic('���⾲̬URL','',makeradio("fmdata[cfg][$i][novu]",array(0 => '��ϵͳ������',1 => '�ر����⾲̬'),empty($arc_tpl['cfg'][$i]['novu']) ? 0 : $arc_tpl['cfg'][$i]['novu']),'');
			echo "</tbody>";
			if($i != $max_addno) tabfooter();
		}
		tabfooter('bsubmit');
		a_guide('arc_tpldetail');
	}else{
		$arc_tpl['addnum'] = max(0,intval($fmdata['addnum']));
		foreach(array(0,1) as $i){
			if(empty($fmdata['search'][$i])){
				unset($arc_tpl['search'][$i]);
			}else{
				$arc_tpl['search'][$i] = $fmdata['search'][$i];
			}
		}
		for($i = 0;$i <= $max_addno;$i ++){
			foreach(array('tpl','static','addno','period','novu',) as $var){
				if(in_array($var,array('tpl','addno',))){
					$fmdata['cfg'][$i][$var] = trim(strip_tags($fmdata['cfg'][$i][$var]));
				}elseif(in_array($var,array('static','novu',))){
					$fmdata['cfg'][$i][$var] = empty($fmdata['cfg'][$i][$var]) ? 0 : 1;
				}elseif(in_array($var,array('period',))){
					$fmdata['cfg'][$i][$var] = max(0,intval($fmdata['cfg'][$i][$var]));
				}
				
				if(empty($fmdata['cfg'][$i][$var])){
					unset($arc_tpl['cfg'][$i][$var]);
				}else{
					$arc_tpl['cfg'][$i][$var] = $fmdata['cfg'][$i][$var];
				}
			}
			if(empty($arc_tpl['cfg'][$i])) unset($arc_tpl['cfg'][$i]);	
		}
		$arc_tpls[$tid] = $arc_tpl;
		cls_CacheFile::Save($arc_tpls,'arc_tpls','arc_tpls');
		adminlog('�ĵ�ģ�巽���༭');
		cls_message::show('�ĵ�ģ�巽���޸����',axaction(6,"?entry=$entry&action=arc_tplsedit"));
	}

}elseif($action == 'arc_tplsedit'){
	foreach(array('arc_tpls','channels',) as $k) $$k = cls_cache::Read($k);
	echo "<title>�ĵ�ģ�巽������</title>";
	if(!submitcheck('bsubmit')){
		tabheader("�ĵ�ģ�巽������&nbsp; &nbsp; >><a href=\"?entry=$entry&action=arc_tpladd\" onclick=\"return floatwin('open_cntplsedit',this)\">���</a>",'cntplsedit',"?entry=$entry&action=$action",'10');
		trcategory(array('ID',array('��������','txtL'),'����','����ҳ','����ҳ','ɾ��','����'));
		foreach($arc_tpls as $k => $v){
			echo "<tr class=\"txt\">".
				"<td class=\"txtC w40\">$k</td>\n".
				"<td class=\"txtL\"><input type=\"text\" size=\"30\" maxlength=\"30\" name=\"fmdata[$k][cname]\" value=\"$v[cname]\"></td>\n".
				"<td class=\"txtC w40\"><input type=\"text\" size=\"4\" maxlength=\"4\" name=\"fmdata[$k][vieworder]\" value=\"$v[vieworder]\"></td>\n".
				"<td class=\"txtC w40\">".@$v['addnum']."</td>\n".
				"<td class=\"txtC w40\">".(empty($v['search']) ? 0 : count($v['search']))."</td>\n".
				"<td class=\"txtC w40\"><a onclick=\"return deltip(this,$no_deepmode)\" href=\"?entry=$entry&action=arc_tpldel&tid=$k\">ɾ��</a></td>\n".
				"<td class=\"txtC w40\"><a href=\"?entry=$entry&action=arc_tpldetail&tid=$k\" onclick=\"return floatwin('open_cntplsedit',this)\">����</a></td>\n".
				"</tr>\n";
		}
		tabfooter('bsubmit','�޸�');
		a_guide('arc_tplsedit');
	}else{
		if(isset($fmdata)){
			foreach($fmdata as $k => $v){
				$v['cname'] = trim(strip_tags($v['cname']));
				$v['cname'] = $v['cname'] ? $v['cname'] : $arc_tpls[$k]['cname'];
				$v['vieworder'] = max(0,intval($v['vieworder']));
				foreach(array('cname','vieworder',) as $var) $arc_tpls[$k][$var] = $v[$var];
			}
			adminlog('�༭�ĵ�ģ�巽��');
			cls_Array::_array_multisort($arc_tpls,'vieworder',1);
			cls_CacheFile::Save($arc_tpls,'arc_tpls','arc_tpls');
		}
		cls_message::show('�ĵ�ģ�巽���޸����',"?entry=$entry&action=$action");
	}
}elseif($action == 'arc_tpldel' && $tid){
	deep_allow($no_deepmode,"?entry=$entry&action=arc_tplsedit");
	$arc_tpls = cls_cache::Read('arc_tpls');
	if(!($arc_tpl = @$arc_tpls[$tid])) cls_message::show('��ѡ���ĵ�ģ�巽��');
	if(!submitcheck('confirm')){
		$message = "ɾ�����ָܻ���ȷ��ɾ����ѡ��Ŀ?<br><br>";
		$message .= "ȷ������>><a href=?entry=$entry&action=$action&tid=$tid&confirm=ok>ɾ��</a><br>";
		$message .= "��������>><a href=?entry=$entry&action=arc_tplsedit>����</a>";
		cls_message::show($message);
	}
	unset($arc_tpls[$tid]);
	cls_CacheFile::Save($arc_tpls,'arc_tpls','arc_tpls');
	cls_message::show('�ĵ�ģ�巽��ɾ���ɹ�', "?entry=$entry&action=arc_tplsedit");
}
