<?
(!defined('M_COM') || !defined('M_ADMIN')) && exit('No Permission');
aheader();
if($re = $curuser->NoBackFunc('gather')) cls_message::show($re);
foreach(array('gmodels','gmissions','catalogs','rprojects','cotypes','channels','abrels','vcps','permissions','currencys',) as $k) $$k = cls_cache::Read($k);
include_once M_ROOT.'include/progress.cls.php';
$gmidsarr = array();foreach($gmodels as $k =>$v) $gmidsarr[$k] = $v['cname'];
if($action == 'gmissionsedit'){
	backnav('gmiss','admin');
	empty($gmidsarr) && cls_message::show('����Ӳɼ�ģ��!');
	if(!submitcheck('bsubmit')){
		tabheader("�ɼ��������&nbsp; &nbsp; >><a href=\"?entry=gmissions&action=gmissionadd\" onclick=\"return floatwin('open_gmission',this)\">���</a>",'gmissionsedit',"?entry=gmissions&action=gmissionsedit",'8');
		trcategory(array(array('��������','txtL'),'��������','�ɼ�ģ��','����','�ɼ�','<input class="checkbox" type="checkbox" name="chkall" onclick="deltip(this,0,checkall,this.form)">ɾ?','����','������'));
		foreach($gmissions as $k => $v){
			if(empty($v['pid'])){
				gmission_list($k);
				if(!empty($v['sonid'])) gmission_list($v['sonid']);
			}
		}
		tabfooter('bsubmit','�޸�');
		a_guide('gmissionadd');
	}else{
		if(!empty($delete)){
			foreach($delete as $k){
				$gmission = cls_cache::Read('gmission',$k,'');
				if($gmission['pid']) $db->query("UPDATE {$tblprefix}gmissions SET sonid='0' WHERE gsid='".$gmission['pid']."'");//����и����񣬽�������ϵ���
				if($gmission['sonid']){//���м������񣬽���������һ��ɾ��
					$db->query("DELETE FROM {$tblprefix}gmissions WHERE gsid='".$gmission['sonid']."'");
					$db->query("DELETE FROM {$tblprefix}gurls WHERE gsid='".$gmission['sonid']."'");
					unset($gmissionsnew[$gmission['sonid']]);
				}
				$db->query("DELETE FROM {$tblprefix}gmissions WHERE gsid=$k");
				$db->query("DELETE FROM {$tblprefix}gurls WHERE gsid=$k");//����ؼ�¼���
				unset($gmissionsnew[$k]);
			}
		}
		if(!empty($gmissionsnew)){
			foreach($gmissionsnew as $k => $v){
				$v['cname'] = empty($v['cname']) ? addslashes($gmissions[$k]['cname']) : $v['cname'];
				$db->query("UPDATE {$tblprefix}gmissions SET cname='$v[cname]' WHERE gsid=$k");
			}
		}
		cls_CacheFile::Update('gmissions');
		adminlog('�༭�ɼ���������б�');
		cls_message::show('�ɼ������޸����',"?entry=gmissions&action=gmissionsedit");

	}
}elseif($action == 'gmissionadd'){
	$pid = empty($pid) ? 0 : max(0,intval($pid));
	if(empty($gmissions[$pid])) $pid = 0;
	if(!submitcheck('bgmissionadd')){
		tabheader('�ɼ��������','gmissionadd',"?entry=gmissions&action=gmissionadd");
		trbasic('�ɼ���������','gmissionadd[cname]');
		trbasic('�ɼ�ģ��','gmissionadd[gmid]',makeoption($gmidsarr),'select');
		if($pid){
			trbasic('�����ɼ�����','',$gmissions[$pid]['cname'],'');
			trhidden('pid',$pid);
		}
		tabfooter('bgmissionadd','���');
		a_guide('gmissionadd');
	}else{
		$gmissionadd['cname'] = trim(strip_tags($gmissionadd['cname']));
		(!$gmissionadd['cname'] || !$gmissionadd['gmid']) && cls_message::show('�ɼ��������ϲ���ȫ',M_REFERER);
		$db->query("INSERT INTO {$tblprefix}gmissions SET cname='$gmissionadd[cname]',gmid='$gmissionadd[gmid]',pid='$pid',timeout=5");
		if($pid && $sonid = $db->insert_id()){
			$db->query("UPDATE {$tblprefix}gmissions SET sonid='$sonid' WHERE gsid='$pid'");
		}
		cls_CacheFile::Update('gmissions');
		adminlog('��Ӳɼ�����');
		cls_message::show('�ɼ�����������',axaction(6,"?entry=gmissions&action=gmissionsedit"));
	}
}elseif($action == 'gmissioncopy'){
	$gsid = empty($gsid) ? 0 : max(0,intval($gsid));
	empty($gmissions[$gsid]) && cls_message::show('�ɼ��������ϲ���ȫ');
	$gmissionss = array(cls_cache::Read('gmission', $gsid, ''));
	if(!submitcheck('bgmissioncopy')){
		tabheader('�ɼ�������','gmissioncopy',"?entry=gmissions&action=gmissioncopy");
		trbasic('�ɼ���������','gmissionnew[cname][]',$gmissions[$gsid]['cname'].'����');
		trbasic('�ɼ�ģ��','',$gmidsarr[$gmissions[$gsid]['gmid']],'');
		if($gmissionss[0]['sonid']){
			trbasic('����������','gmissionnew[cname][]',$gmissions[$gmissionss[0]['sonid']]['cname'].'����');
			trbasic('������ģ��','',$gmidsarr[$gmissions[$gmissionss[0]['sonid']]['gmid']],'');
		}
		trhidden('gsid',$gsid);
		tabfooter('bgmissioncopy','����');
		a_guide('gmissioncopy');
	}else{
		foreach($gmissionnew['cname'] as $k => $cname)
			$gmissionnew['cname'][$k] = trim(strip_tags($cname));
		$gmissionnew['cname'][0] || cls_message::show('�ɼ��������ϲ���ȫ',M_REFERER);
		$gmissionss[0]['sonid'] && !empty($gmissionnew['cname'][1]) && $gmissionss[] = cls_cache::Read('gmission', $gmissionss[0]['sonid'], '');
		$gmissionss[0]['gsid'] = $pid = 0;
		cls_CacheFile::Update('gmissions');
		foreach($gmissionss as $k => $gmission){
			$cname = $gmissionnew['cname'][$k];
			$gmission['fsettings']	= serialize($gmission['fsettings']);
			$gmission['dvalues']	= serialize($gmission['dvalues']);
			while(list($key, $val) = each($gmission))$gmission[$key] = addslashes($val);
			$db->query("INSERT INTO {$tblprefix}gmissions SET
				cname='$cname',
				gmid='$gmission[gmid]',
				umode1='$gmission[umode1]',
				umode2='$gmission[umode2]',
				ubase='$gmission[ubase]',
				ubase0='$gmission[ubase0]',
				ubase1='$gmission[ubase1]',
				ubase2='$gmission[ubase2]',
				mcharset='$gmission[mcharset]',
				timeout='$gmission[timeout]',
				mcookies='$gmission[mcookies]',
				umode='$gmission[umode]',
				uurls='$gmission[uurls]',
				uregular='$gmission[uregular]',
				ufromnum='$gmission[ufromnum]',
				utonum='$gmission[utonum]',
				ufrompage='$gmission[ufrompage]',
				udesc='$gmission[udesc]',
				uinclude='$gmission[uinclude]',
				uforbid='$gmission[uforbid]',
				uregion='$gmission[uregion]',
				uspilit='$gmission[uspilit]',
				uurltag='$gmission[uurltag]',
				utitletag='$gmission[utitletag]',
				uurltag1='$gmission[uurltag1]',
				uinclude1='$gmission[uinclude1]',
				uforbid1='$gmission[uforbid1]',
				uurltag2='$gmission[uurltag2]',
				uinclude2='$gmission[uinclude2]',
				uforbid2='$gmission[uforbid2]',
				mpfield='$gmission[mpfield]',
				mpmode='$gmission[mpmode]',
				mptag='$gmission[mptag]',
				mpinclude='$gmission[mpinclude]',
				mpforbid='$gmission[mpforbid]',
				fsettings='$gmission[fsettings]',
				dvalues='$gmission[dvalues]',
				pid='$pid',sonid='0'"
			);
			$gmissionss[$k]['gsid'] = $pid = $db->insert_id();
		}
		if(count($gmissionss) > 1)
			$db->query("UPDATE {$tblprefix}gmissions SET sonid='$pid' WHERE gsid='{$gmissionss[0]['gsid']}'");
		cls_CacheFile::Update('gmissions');
		adminlog('���Ʋɼ�����');
		cls_message::show('�ɼ�����������',axaction(6,"?entry=gmissions&action=gmissionsedit"));
	}
}elseif($action == 'gmissionurls' && $gsid){
	backnav('grule','netsite');
	$gmission = cls_cache::Read('gmission',$gsid,''); //print_r($gmission);
	$gmodel = $gmodels[$gmission['gmid']];
	if(!submitcheck('bgmissionurls')){
		$mchararr = array('gbk' => 'GBK/GB2312','utf8' => 'UTF-8','big5' => 'BIG5',);
		tabheader('�ɼ���������','gmissionurls',"?entry=gmissions&action=gmissionurls&gsid=$gsid");
		trbasic('�ɼ���������','gmissionnew[cname]',$gmission['cname']);
		trbasic('ҳ�����','gmissionnew[mcharset]',makeoption($mchararr,$gmission['mcharset']),'select');
		trbasic('���ӳ�ʱ(��)','gmissionnew[timeout]',empty($gmission['timeout']) ? 0 : $gmission['timeout'],'text',array('guide'=>'0��ձ�ʾ������'));
		trbasic('��¼��վ'.'Cookies','gmissionnew[mcookies]',empty($gmission['mcookies']) ? '' : $gmission['mcookies'],'text',array('w'=>50));
		tabfooter();

		tabheader('��ַ��Դ����');
		if(empty($gmission['pid'])){
			trbasic('�ֶ���Դ��ַ<br> (ÿ��һ����ַ�����������)','gmissionnew[uurls]',$gmission['uurls'],'textarea');
			trbasic("������Դ��ַ<br><span onclick=\"replace_html(this,'gmissionnew[uregular]')\" style=\"color:#03F;cursor: pointer;\">(*)</span>&nbsp;<span onclick=\"replace_html(this,'gmissionnew[uregular]')\" style=\"color:#03F;cursor: pointer;\">(?)</span>",'gmissionnew[uregular]',empty($gmission['uregular']) ? '' : $gmission['uregular'],'text',array('w'=>50));
			trbasic('���п�ʼҳ��','gmissionnew[ufromnum]',$gmission['ufromnum']);
			trbasic('���н���ҳ��','gmissionnew[utonum]',$gmission['utonum']);
			trbasic('��ԴҳBASE��ַ','gmissionnew[ubase]',empty($gmission['ubase']) ? '' : $gmission['ubase'],'text',array('w' => 70, 'guide'=>'�Ǳ���������Դҳ�������� &lt;base href="http://xxx.xxx" /> �ı�ǩ��������������href�������'));
		}else{
			$frompagearr = array(0 => '��������ҳ',1 => '����׷��ҳ1',2 => '����׷��ҳ2');
			trbasic('��ַ���Ժϼ����ĸ�ҳ��','gmissionnew[ufrompage]',makeoption($frompagearr,$gmission['ufrompage']),'select');
		}
		trbasic('����ɼ�','gmissionnew[udesc]',$gmission['udesc'],'radio');
		trbasic('����ҳBASE��ַ','gmissionnew[ubase0]',empty($gmission['ubase0']) ? '' : $gmission['ubase0'],'text',array('w' => 70, 'guide'=>'�Ǳ�����������ҳ�������� &lt;base href="http://xxx.xxx" /> �ı�ǩ��������������href�������'));
		tabfooter();

		tabheader('��ַ�ɼ�����');
		trbasic("ҳ��ɼ���Χ<br /> �ɼ�ģӡ<br><span onclick=\"replace_html(this,'gmissionnew[uregion]')\" style=\"color:#03F;cursor: pointer;\">(*)</span>&nbsp;<span onclick=\"replace_html(this,'gmissionnew[uregion]')\" style=\"color:#03F;cursor: pointer;\">(?)</span>",'gmissionnew[uregion]',$gmission['uregion'],'textarea');
		trbasic('��ַ�б�ָ���','gmissionnew[uspilit]',$gmission['uspilit']);
		trbasic("��ַ�ɼ�ģӡ<br><span onclick=\"replace_html(this,'gmissionnew[uurltag]')\" style=\"color:#03F;cursor: pointer;\">(*)</span>&nbsp;<span onclick=\"replace_html(this,'gmissionnew[uurltag]')\" style=\"color:#03F;cursor: pointer;\">(?)</span>",'gmissionnew[uurltag]',$gmission['uurltag'],'textarea');
		trbasic("����ɼ�ģӡ<br><span onclick=\"replace_html(this,'gmissionnew[utitletag]')\" style=\"color:#03F;cursor: pointer;\">(*)</span>&nbsp;<span onclick=\"replace_html(this,'gmissionnew[utitletag]')\" style=\"color:#03F;cursor: pointer;\">(?)</span>",'gmissionnew[utitletag]',$gmission['utitletag'],'textarea');
		trbasic('�����ַ�غ�','gmissionnew[uinclude]',$gmission['uinclude']);
		trbasic('�����ַ����','gmissionnew[uforbid]',$gmission['uforbid']);
		tabfooter();

		tabheader('׷����ַ����');
		trbasic("׷����ַ1�ɼ�ģӡ<br><span onclick=\"replace_html(this,'gmissionnew[uurltag1]')\" style=\"color:#03F;cursor: pointer;\">(*)</span>&nbsp;<span onclick=\"replace_html(this,'gmissionnew[uurltag1]')\" style=\"color:#03F;cursor: pointer;\">(?)</span>",'gmissionnew[uurltag1]',$gmission['uurltag1'],'textarea');
		trbasic('��ȫƥ��ģӡ','gmissionnew[umode1]',$gmission['umode1'],'radio',array('guide'=>'ѡ����ȫƥ��ģӡ,��ƥ�䷵�ص���������ȫ�ַ���',));
		trbasic('׷����ַ1�غ�','gmissionnew[uinclude1]',$gmission['uinclude1']);
		trbasic('׷����ַ1����','gmissionnew[uforbid1]',$gmission['uforbid1']);
		trbasic('׷��ҳ1BASE��ַ','gmissionnew[ubase1]',empty($gmission['ubase1']) ? '' : $gmission['ubase1'],'text',array('w' => 70, 'guide'=>'�Ǳ�������׷��ҳ1�������� &lt;base href="http://xxx.xxx" /> �ı�ǩ��������������href�������'));
		trbasic("׷����ַ2�ɼ�ģӡ<br><span onclick=\"replace_html(this,'gmissionnew[uurltag2]')\" style=\"color:#03F;cursor: pointer;\">(*)</span>&nbsp;<span onclick=\"replace_html(this,'gmissionnew[uurltag2]')\" style=\"color:#03F;cursor: pointer;\">(?)</span>",'gmissionnew[uurltag2]',$gmission['uurltag2'],'textarea');
		trbasic('��ȫƥ��ģӡ','gmissionnew[umode2]',$gmission['umode2'],'radio',array('guide'=>'ѡ����ȫƥ��ģӡ,��ƥ�䷵�ص���������ȫ�ַ���',));
		trbasic('׷����ַ2�غ�','gmissionnew[uinclude2]',$gmission['uinclude2']);
		trbasic('׷����ַ2����','gmissionnew[uforbid2]',$gmission['uforbid2']);
		trbasic('׷��ҳ2BASE��ַ','gmissionnew[ubase2]',empty($gmission['ubase2']) ? '' : $gmission['ubase2'],'text',array('w' => 70, 'guide'=>'�Ǳ�������׷��ҳ2�������� &lt;base href="http://xxx.xxx" /> �ı�ǩ��������������href�������'));
		tabfooter('bgmissionurls');
		a_guide('gmissionurls');
	}else{
		$gmissionnew['cname'] = empty($gmissionnew['cname']) ? $gmission['cname'] : $gmissionnew['cname'];
		if(empty($gmission['pid'])){
			$gmissionnew['uurls'] = trim($gmissionnew['uurls']);
			$gmissionnew['uregular'] = trim($gmissionnew['uregular']);
			$gmissionnew['ufromnum'] = max(0,intval($gmissionnew['ufromnum']));
			$gmissionnew['utonum'] = max(0,intval($gmissionnew['utonum']));
			$gmissionnew['ufrompage'] = 0;
		}else{
			$gmissionnew['uurls'] = '';
			$gmissionnew['uregular'] = '';
			$gmissionnew['ufromnum'] = 0;
			$gmissionnew['utonum'] = 0;
			$gmissionnew['ufrompage'] = max(0,intval($gmissionnew['ufrompage']));
		}
		$db->query("UPDATE {$tblprefix}gmissions SET
					umode1='$gmissionnew[umode1]',
					umode2='$gmissionnew[umode2]',
					ubase='$gmissionnew[ubase]',
					ubase0='$gmissionnew[ubase0]',
					ubase1='$gmissionnew[ubase1]',
					ubase2='$gmissionnew[ubase2]',
					cname='$gmissionnew[cname]',
					timeout='$gmissionnew[timeout]',
					mcharset='$gmissionnew[mcharset]',
					mcookies='$gmissionnew[mcookies]',
					uurls='$gmissionnew[uurls]',
					uregular='$gmissionnew[uregular]',
					ufromnum='$gmissionnew[ufromnum]',
					utonum='$gmissionnew[utonum]',
					ufrompage='$gmissionnew[ufrompage]',
					udesc='$gmissionnew[udesc]',
					uregion='$gmissionnew[uregion]',
					uspilit='$gmissionnew[uspilit]',
					uurltag='$gmissionnew[uurltag]',
					utitletag='$gmissionnew[utitletag]',
					uinclude='$gmissionnew[uinclude]',
					uforbid='$gmissionnew[uforbid]',
					uurltag1='$gmissionnew[uurltag1]',
					uinclude1='$gmissionnew[uinclude1]',
					uforbid1='$gmissionnew[uforbid1]',
					uurltag2='$gmissionnew[uurltag2]',
					uinclude2='$gmissionnew[uinclude2]',
					uforbid2='$gmissionnew[uforbid2]'
					WHERE gsid=$gsid");
		cls_CacheFile::Update('gmissions');
		adminlog('��ϸ�޸Ĳɼ�����');
		cls_message::show('�ɼ������޸����',M_REFERER);

	}
}elseif($action == 'gmissionfields' && $gsid){
	backnav('grule','content');
	$gmission = cls_cache::Read('gmission',$gsid,'');
	$gmodel = cls_cache::Read('gmodel',$gmission['gmid'],'');
	$fields = cls_cache::Read('fields',$gmodel['chid']);
	$cotypes = cls_cache::Read('cotypes');
	$cfields = array('caid'=>array('datatype'=>'select','cname'=>'��Ŀ'));
	foreach($cotypes as $k=>$v){
		$cfields['ccid'.$k]['datatype'] = $v['asmode'] ? 'mselect' : 'select';
		$cfields['ccid'.$k]['cname'] = $v['cname'];
	}
	$fields = $cfields + $fields + array('jumpurl'=>array('datatype'=>'text','cname'=>'��תURL'),'createdate'=>array('datatype'=>'text','cname'=>'���ʱ��'),'enddate'=>array('datatype'=>'text','cname'=>'����ʱ��'),'mname'=>array('datatype'=>'text','cname'=>'��Ա����'));
	if(!submitcheck('bgmissionfields')){
		$mpfieldarr = array('' => '�޷�ҳ�ֶ�');
		foreach($fields as $k => $v){
			if(isset($gmodel['gfields'][$k])) $mpfieldarr[$k] = $v['cname'];
		}
		tabheader('��ҳ�ɼ�����','gmissionfields',"?entry=gmissions&action=gmissionfields&gsid=$gsid",4);
		trbasic('��ҳ�ֶ�','gmissionnew[mpfield]',makeoption($mpfieldarr,isset($gmission['mpfield']) ? $gmission['mpfield'] : ''),'select');
		trbasic('��ҳ�����Ƿ�����','',makeradio('gmissionnew[mpmode]', array('0' => '��', '1' => '��'), $gmission['mpmode']),'');
		trbasic('��ҳ��������ɼ�ģӡ','gmissionnew[mptag]',isset($gmission['mptag']) ? $gmission['mptag'] : '','textarea');
		trbasic('��ҳ���ӱغ�','gmissionnew[mpinclude]',isset($gmission['mpinclude']) ? $gmission['mpinclude'] : '');
		trbasic('��ҳ���ӽ���','gmissionnew[mpforbid]',isset($gmission['mpforbid']) ? $gmission['mpforbid'] : '');
		tabfooter();
		tabheader('�ɼ��ֶι���','',"",4);
		foreach($fields as $k => $v){
			if(isset($gmodel['gfields'][$k])) missionfield($v['cname'],$k,empty($gmission['fsettings'][$k]) ? array() : $gmission['fsettings'][$k],$v['datatype']);
		}
		tabfooter('bgmissionfields');
		a_guide('gmissionfields');
	}else{
		if(!empty($fsettingsnew)){
			foreach($fsettingsnew as $k => $fsettingnew){
				if(!in_array($fields[$k]['datatype'],array('images','files','flashs','medias'))){
					$fsettingnew['clearhtml'] = isset(${'clearhtml'.$k}) ? implode(',',${'clearhtml'.$k}) : '';
				}
				foreach($fsettingnew as $t => $v){
					$fsettingnew[$t] = stripslashes($v);
				}
				$fsettingsnew[$k] = $fsettingnew;
			}
		}
		$fsettingsnew = empty($fsettingsnew) ? '' : addslashes(serialize($fsettingsnew));
		$db->query("UPDATE {$tblprefix}gmissions SET
					mpfield='$gmissionnew[mpfield]',
					mpmode='$gmissionnew[mpmode]',
					mptag='$gmissionnew[mptag]',
					mpinclude='$gmissionnew[mpinclude]',
					mpforbid='$gmissionnew[mpforbid]',
					fsettings='$fsettingsnew'
					WHERE gsid=$gsid");
		cls_CacheFile::Update('gmissions');
		adminlog('��ϸ�޸Ĳɼ�����');
		cls_message::show('�ɼ�����༭���',M_REFERER);

	}
}elseif($action == 'gmissionoutput' && $gsid){
	backnav('grule','output');
	$gmission = cls_cache::Read('gmission',$gsid,'');
	$gmodel = cls_cache::Read('gmodel',$gmission['gmid'],'');
	$dvalues = empty($gmission['dvalues']) ? array() : $gmission['dvalues'];
	$chid = $gmodel['chid'];
	$channel = cls_channel::Config($chid);
	$fields = cls_cache::Read('fields',$chid);
	if(!submitcheck('bgmissionoutput')){
		$a_field = new cls_field;
		$mustsarr = array();
		foreach($fields as $k => $v) isset($gmodel['gfields'][$k]) && $mustsarr[$k] = $v['cname'];
		tabheader("[$gmission[cname]]����������",'gmissionoutput',"?entry=gmissions&action=gmissionoutput&gsid=$gsid",2,1,1);
		trbasic('���²ɼ��ֶ�Ϊ��ʱ�������','',multiselect('dvaluesnew[musts][]',$mustsarr,empty($dvalues['musts']) ? array() : explode(',',$dvalues['musts'])),'');
		if(isset($fields['abstract']) && !in_array('abstract',array_keys($gmodel['gfields']))){
			trbasic('�Զ�ժҪ','dvaluesnew[autoabstract]',empty($dvalues['autoabstract']) ? 0 : $dvalues['autoabstract'],'radio');
		}
		if(isset($fields['thumb']) && !in_array('thumb',array_keys($gmodel['gfields']))){
			trbasic('�Զ�����ͼ','dvaluesnew[autothumb]',empty($dvalues['autothumb']) ? 0 : $dvalues['autothumb'],'radio');
		}
		if($gmission['pid']){
			$abidsarr = array();foreach($abrels as $k => $v) $abidsarr[$k] = $v['cname'];
			trbasic('���鼭ʱ��ѭ�ĺϼ���Ŀ','dvaluesnew[arid]',makeoption($abidsarr,empty($dvalues['arid']) ? 0 : $dvalues['arid']),'select');
		}
		tabfooter();
		tabheader("[$gmission[cname]]���Ĭ��ֵ");
		tr_cns('������Ŀ','dvaluesnew[caid]',array('value' => empty($dvalues['caid']) ? 0 : $dvalues['caid'],'chid' => $chid,'notblank' => 1,));
		foreach($cotypes as $k => $v){
			if(!$v['self_reg']){
				tr_cns($v['cname'],"dvaluesnew[ccid$k]",array('value' => empty($dvalues["ccid$k"]) ? '' : $dvalues["ccid$k"],'coid' => $k,'chid' => $chid,'max' => $v['asmode'],));
			}
		}
		foreach($fields as $k => $v){ 
			if(!in_array($k,array('abstract','thumb','content','subject'))){
				$a_field->init($v,!isset($dvalues[$k]) ? '' : $dvalues[$k]); 
				$a_field->trfield('dvaluesnew');
			}
		}
		trbasic('��Ա����','dvaluesnew[mname]',empty($dvalues['mname']) ? '' : $dvalues['mname'],'text',array('guide'=>'ָ�������Ա����ʱ���ԡ�,���ָ���ע����д�����Աʱ�������ȡ��Ա���й�������ʽ��mmmm,bbbb,ccccc'));
		tabfooter('bgmissionoutput');
		a_guide('gmissionoutput');
	}else{//�����ڵ�addsalshes
		if(empty($dvaluesnew['caid'])) cls_message::show('��ָ����ȷ����Ŀ',"?entry=gmissions&action=gmissionoutput&gsid=$gsid");
		if($gmission['pid'] && empty($dvaluesnew['arid'])) cls_message::show('��ָ�����鼭ʱ��ѭ�ĺϼ���Ŀ',"?entry=gmissions&action=gmissionoutput&gsid=$gsid");
		$dvaluesnew['musts'] = empty($dvaluesnew['musts']) ? '' : implode(',',$dvaluesnew['musts']);
		foreach($cotypes as $k => $v){
			$dvaluesnew["ccid$k"] = empty($dvaluesnew["ccid$k"]) ? '' : $dvaluesnew["ccid$k"];
		}
		$dvaluesnew['autoabstract'] = empty($dvaluesnew['autoabstract']) ? 0 : $dvaluesnew['autoabstract'];
		$dvaluesnew['autothumb'] = empty($dvaluesnew['autothumb']) ? 0 : $dvaluesnew['autothumb'];
		$c_upload = cls_upload::OneInstance();
		$a_field = new cls_field;
		foreach($fields as $k => $v){
			if(!isset($gmodel['gfields'][$k]) && !in_array($k,array('abstract','thumb'))){
				$a_field->init($v,!isset($dvalues[$k]) ? '' : $dvalues[$k]);
				$a_field->deal('dvaluesnew','cls_message::show',M_REFERER);
				$dvaluesnew[$k] = $a_field->newvalue; 
			}
			if($v['datatype']=='date'){
				$dvaluesnew[$k] = strtotime($dvaluesnew[$k]);
			}
		}
		//print_r($dvaluesnew); die();
		unset($a_field);
		//�����Ա�ָ���ֹ����
		$dvaluesnew['mname'] = str_replace('��',',',$dvaluesnew['mname']);
		if(!empty($dvaluesnew)){
			foreach($dvaluesnew as $x => $y){
                if(is_array($y)){ 
                    $y = implode("\t",$y);
                } 
                $dvaluesnew[$x] = stripslashes($y);
                
			} 
            
		}
		$dvaluesnew = empty($dvaluesnew) ? '' : addslashes(serialize($dvaluesnew));
		$db->query("UPDATE {$tblprefix}gmissions SET
					dvalues='$dvaluesnew'
					WHERE gsid=$gsid");
		$c_upload->closure(1, $gsid, 'gmissions');
		$c_upload->saveuptotal(1);
		cls_CacheFile::Update('gmissions');
		adminlog('��ϸ�޸Ĳɼ�����');
		cls_message::show('�������޸����',M_REFERER);
	}
}elseif($action == 'urlstest' && $gsid){
	backnav('grule','test');
	if(!submitcheck('confirm') && empty($gather_test_url)){
		$message = "��ѡ���˲��Թ���<br>�ڲ���֮ǰ��ȷ���Ѿ�������ع���<br>�����Դ���ַ����һ��һ�����ԣ���ע��ѡ�����ҳ���������ӽ�����һ�����ԡ�<br><br>";
		$message .= "ȷ������>><a href=?entry=gmissions&action=urlstest&gsid=$gsid&confirm=ok>��ʼ</a>";
		cls_message::show($message);
	}else{
		tabheader('�ɼ���ַ�������', 'gather_testu', "?$_SERVER[QUERY_STRING]");
		$gather = new cls_gather;
		$gather->set_mission($gsid);
#		check_rule_urls($gather->gmission);
		if(empty($gather_test_url)){
			$message = '';
			if($surls = $gather->fetch_surls()){
				foreach($surls as $surl) $message .= $surl.'<br>';
			}else $message = '����Դ��ַ'.'<br>';
				trbasic('ȫ����Դ��ַ','',$message,'');
			$surl = empty($surls) ? '' : $surls[array_rand($surls)];
		}else{
			$surl = $gather_test_url;
		}
		$sonid = $gather->gmission['sonid'];
		$lang_test = '����';
		$lang_content = '����';
		$lang_son = '������';
		trbasic('��ǰ������Դ��ַ','',"<input name=\"gather_test_url\" style=\"width:98%\" value=\"$surl\" />",'');
		tabfooter('bsubmit');
		$tab_titles = array('���',array('��ַ����', 'txtL'),array('������ַ', 'txtL'),$lang_test,'׷����ַ1','׷����ַ2');
		if($sonid){
			array_splice($tab_titles, 3, 0, $lang_son);
			$ufrompage = cls_cache::Read('gmission',$sonid,'');
			$ufrompage = $ufrompage['ufrompage'];
			$ufrompage = 'gurl' . ($ufrompage ? $ufrompage : '');
		}
		if($rets = $gather->fetch_gurls($surl,1)){//�õ�������ַ�б�
			tabheader('������ַ�б� (������ַ�����������10)','','',$sonid ? 7 : 6);
			trcategory($tab_titles);
			$i = 0;
			foreach($rets as $k => $v){
				$i ++;
				$titlestr = empty($v['son']) ? "<b>$v[utitle]</b>" : "&nbsp; &nbsp; &nbsp; &nbsp; $v[utitle]";
				$gurlstr  = empty($k) ? '-' : "<a href=\"$k\" target=\"_blank\">".mhtmlspecialchars(strlen($k) > 25 ? '...' . substr($k, -25) : $k)."</a>";
				$gurl1str = empty($v['gurl1']) ? '-' : "<a href=\"$v[gurl1]\" target=\"_blank\">Y</a>";
				$gurl2str = empty($v['gurl2']) ? '-' : "<a href=\"$v[gurl2]\" target=\"_blank\">Y</a>";
				if(empty($k)){
					$teststr  = '&nbsp;';
				}else{
					$gurl	  = rawurlencode($k);
					$gurl1	  = empty($v['gurl1']) ? '' : rawurlencode($v['gurl1']);
					$gurl2	  = empty($v['gurl2']) ? '' : rawurlencode($v['gurl2']);
					$teststr  = "<a href=\"?entry=gmissions&action=contentstest&gsid=$gsid&confirm=ok&gather_test_url=$gurl&gather_test_url1=$gurl1&gather_test_url2=$gurl2\" onclick=\"return floatwin('open_newgmission_cnt',this)\" >$lang_content</a>";
					if($sonid){
	#					$sonurl   = $gurl2 ? $gurl2 : ($gurl1 ? $gurl1 : $gurl);
						$sonurl   = $$ufrompage;
						$teststr2 = "<a href=\"?entry=gmissions&action=urlstest&gsid=$sonid&confirm=ok&gather_test_url=$sonurl\" onclick=\"return floatwin('open_newgmission_son',this)\" >$lang_son</a>";
					}
				}
				echo "<tr class=\"txt\">".
					"<td class=\"txtC w40\">$i</td>\n".
					"<td class=\"txtL\">$titlestr</td>\n".
					"<td class=\"txtL\">$gurlstr</td>\n".
					"<td class=\"txtC\">$teststr</td>\n".
					($sonid ? "<td class=\"txtC\">$teststr2</td>\n" : '') .
					"<td class=\"txtC\">$gurl1str</td>\n".
					"<td class=\"txtC\">$gurl2str</td></tr>\n";
			}
			tabfooter();
		}else{
			$surl && cls_message::show(is_array($rets) ? 'û�вɼ�������' : '�ɼ���ʱ�����');
		}
		a_guide('urlstest');
	}
}elseif($action == 'contentstest' && $gsid){//ֻ�����ݿ��м�����Ч����������
	backnav('grule','test');
	if(!submitcheck('confirm')){
		$message = "��ѡ�������ݹ�����ԡ�<br>��ִ����ǰ��ȷ�����������ݹ���<br>�Լ�������Ҫ��δ�ɼ����ݵ���ַ��<br><br>";
		$message .= "ȷ������>><a href=?entry=gmissions&action=contentstest&gsid=$gsid&confirm=ok>��ʼ</a>";
		cls_message::show($message);
	}else{
		tabheader('�ɼ����ݹ������', 'gather_testc', "?$_SERVER[QUERY_STRING]");
#		$counts = $db->result_one("SELECT COUNT(*) FROM {$tblprefix}gurls WHERE gsid='$gsid' AND gatherdate=0");

		if(empty($gather_test_url)){
			$item = $db->fetch_one("SELECT guid,gurl,gurl1,gurl2,utitle FROM {$tblprefix}gurls WHERE gsid='$gsid' AND gatherdate=0 AND guid >= (SELECT floor(RAND() * (SELECT MAX(guid) FROM {$tblprefix}gurls))) ORDER BY guid LIMIT 1");
		}else{
			$item = array(
				'utitle' => '[���Բɼ�]',
				'gurl' => $gather_test_url,
				'gurl1' => $gather_test_url1,
				'gurl2' => $gather_test_url2,
			);
		}
		if($item){
			trbasic('��ǰ������ַ����','',mhtmlspecialchars($item['utitle']),'');
#			trbasic('��ǰ������ַ','',"<input name=\"gather_test_url\" style=\"width:98%\" value=\"$item[gurl]\" />",'');
#			trbasic('', '', '<input class="bigButton" type="submit" name="bsubmit" value="' . '�ύ'. '">','');
			trbasic('��ǰ������ַ','',"<a href=\"$item[gurl]\" target=\"_blank\">$item[gurl]</a>",'');
			empty($item['gurl1']) || trbasic('׷����ַ1','',"<a href=\"$item[gurl1]\" target=\"_blank\">$item[gurl1]</a>",'');
			empty($item['gurl2']) || trbasic('׷����ַ2','',"<a href=\"$item[gurl2]\" target=\"_blank\">$item[gurl2]</a>",'');
			$gather = new cls_gather;
			$gather->set_mission($gsid);
			$contents = $gather->gather_guid(0,1, $item);
			if($contents){
				$timeout = '�ɼ���ʱ�����';
				$chid = $gmodels[$gmissions[$gsid]['gmid']]['chid'];
				$fields = cls_cache::Read('fields',$chid);
				$cotypes = cls_cache::Read('cotypes');
				$cfields = array('caid'=>array('datatype'=>'select','cname'=>'��Ŀ'));
				foreach($cotypes as $k=>$v){
					$cfields['ccid'.$k]['datatype'] = $v['asmode'] ? 'mselect' : 'select';
					$cfields['ccid'.$k]['cname'] = $v['cname'];
				}
				$fields = $cfields + $fields + array('jumpurl'=>array('datatype'=>'text','cname'=>'��תURL'),'createdate'=>array('datatype'=>'text','cname'=>'���ʱ��'),'mname'=>array('datatype'=>'text','cname'=>'��Ա����'));;
				foreach($contents as $k => $v){
					trbasic('['.$fields[$k]['cname'].']'.'�ɼ����', '', $v === false ? $timeout : mhtmlspecialchars($v),'');
				}
			}else{
				trbasic('�ɼ����','','','');
			}
		}else{
			trbasic('', '', '���Ȳɼ�������ַ','');
		}
		tabfooter();
		a_guide('contentstest');
	}
}elseif($action == 'contentsoption' && $gsid){
	empty($gmissions[$gsid]) && cls_message::show('��ָ����ȷ�Ĳɼ�����');
	$page = !empty($page) ? max(1, intval($page)) : 1;
	submitcheck('bfilter') && $page = 1;
	$viewdetail = empty($viewdetail) ? 0 : $viewdetail;
	$gathered = isset($gathered) ? $gathered : '-1';
	$outputed = isset($outputed) ? $outputed : '-1';
	$abover = isset($abover) ? $abover : '-1';
	$keyword = empty($keyword) ? '' : $keyword;

	$filterstr = '';
	foreach(array('viewdetail','gathered','outputed','abover','keyword') as $k) $filterstr .= "&$k=".rawurlencode(stripslashes($$k));

	$wheresql = "WHERE gsid='$gsid'";
	$gathered != '-1' && $wheresql .= " AND gatherdate".($gathered ? '!=' : '=')."'0'";
	$outputed != '-1' && $wheresql .= " AND outputdate".($outputed ? '!=' : '=')."'0'";
	$abover != '-1' && $wheresql .= " AND abover='$abover'";
	$keyword && $wheresql .= " AND utitle ".sqlkw($keyword);
	if(!submitcheck('barcsedit')){
		echo form_str($actionid.'arcsedit',"?entry=gmissions&action=contentsoption&gsid=$gsid&page=$page");
		tabheader_e();
		echo "<tr><td colspan=\"2\" class=\"txt txtleft\">";
		echo "&nbsp; <input class=\"text\" name=\"keyword\" type=\"text\" value=\"$keyword\" size=\"8\"  title=\"�����ɼ�����\" style=\"vertical-align: middle;\">&nbsp; ";
		$gatheredarr = array('-1' => '�ɼ�״̬','0' => 'δ�ɼ�','1' => '�Ѳɼ�');
		echo "<select style=\"vertical-align: middle;\" name=\"gathered\">".makeoption($gatheredarr,$gathered)."</select>&nbsp; ";
		$outputedarr = array('-1' => '���״̬','0' => 'δ���','1' => '�����');
		echo "<select style=\"vertical-align: middle;\" name=\"outputed\">".makeoption($outputedarr,$outputed)."</select>&nbsp; ";
		$aboverarr = array('-1' => '�Ƿ����ϼ�','0' => 'δ���','1' => '���');
		echo "<select style=\"vertical-align: middle;\" name=\"abover\">".makeoption($aboverarr,$abover)."</select>&nbsp; ";
		echo strbutton('bfilter','ɸѡ');
		echo "</td></tr>";
		tabfooter();

		$pagetmp = $page;
		do{
			$query = $db->query("SELECT * FROM {$tblprefix}gurls $wheresql ORDER BY guid DESC LIMIT ".(($pagetmp - 1) * $atpp).",$atpp");
			$pagetmp--;
		} while(!$db->num_rows($query) && $pagetmp);
		$itemstr = '';
		while($row = $db->fetch_array($query)){
			$gatherstr = $row['gatherdate'] ? date("Y-m-d",$row['gatherdate']) : '-';
			$outputstr = $row['outputdate'] ? date("Y-m-d",$row['outputdate']) : '-';
			$gurl1str = $row['gurl1'] ? "<a href=$row[gurl1] target=\"_blank\">�鿴</a>" : '-';
			$gurl2str = $row['gurl2'] ? "<a href=$row[gurl2] target=\"_blank\">�鿴</a>" : '-';
			$aboverstr = $row['abover'] ? 'Y' : '-';
			$itemstr .= "<tr class=\"txt\"><td class=\"txtC\"><input class=\"checkbox\" type=\"checkbox\" name=\"selectid[$row[guid]]\" value=\"$row[guid]\">\n".
				"<td class=\"txtL\"><a href=$row[gurl] target=\"_blank\">$row[utitle]</a></td>\n".
				"<td class=\"txtC\">$gurl1str</td>\n".
				"<td class=\"txtC\">$gurl2str</td>\n".
				"<td class=\"txtC\">$gatherstr</td>\n".
				"<td class=\"txtC\">$outputstr</td>\n".
				"<td class=\"txtC\">$aboverstr</td>\n".
				"<td class=\"txtC\"><a href=\"?entry=gmissions&action=contentdetail&guid=$row[guid]\" onclick=\"return floatwin('open_newgmission',this)\">�鿴</a></td></tr>\n";
		}
		$counts = $db->result_one("SELECT count(*) FROM {$tblprefix}gurls $wheresql");
		$multi = multi($counts,$atpp,$page, "?entry=gmissions&action=contentsoption&gsid=$gsid$filterstr");

		tabheader('���ݲɼ�����'.'-'.$gmissions[$gsid]['cname']."&nbsp; &nbsp; <input class=\"checkbox\" type=\"checkbox\" name=\"select_all\" value=\"1\">&nbsp;".'ȫѡ����ҳ����','','',8);
		trcategory(array("<input class=\"checkbox\" type=\"checkbox\" name=\"chkall\" onclick=\"checkall(this.form, 'selectid', 'chkall')\">",'������ַ','׷����ַ1','׷����ַ2','�ɼ�','���','���','���'));
		echo $itemstr;
		tabfooter();
		echo $multi;

		tabheader('������Ŀ');
		$soperatestr = '';
		$s_arr = array('delete' => 'ɾ��','gather' => '�ɼ�','output' => '���','regather' => '����״̬');
		foreach($s_arr as $k => $v) $soperatestr .= "<input class=\"radio\" type=\"radio\" id=\"arcdeal_$k\" name=\"arcdeal\" value=\"$k\"" . ($k == 'delete' ? ' onclick="deltip()"' : ''). " /><label for=\"arcdeal_$k\">$v</label> &nbsp;";
		trbasic('ѡ�������Ŀ','',$soperatestr,'');
		$aboverarr = array(0 => 'δ���',1 => '�����');
		trbasic("<input class=\"radio\" type=\"radio\" name=\"arcdeal\" value=\"abover\">&nbsp;".'�ϼ����','',makeradio('arcabover',$aboverarr),'');
		tabfooter('barcsedit');
	}else{
		if(empty($selectid) && empty($select_all)) cls_message::show('��ѡ����ַ',"?entry=gmissions&action=contentsoption&gsid=$gsid$filterstr");
		if(!empty($select_all)){
			$parastr = "";
			foreach(array('arcabover') as $k) $parastr .= "&$k=".$$k;
			$selectid = array();
			$npage = empty($npage) ? 1 : $npage;
			if(empty($pages)){
				$counts = $db->result_one("SELECT count(*) FROM {$tblprefix}gurls $wheresql");
				$pages = @ceil($counts / $atpp);
			}
			if($npage <= $pages){
				$fromstr = empty($fromid) ? "" : "guid<$fromid";
				$nwheresql = !$wheresql ? ($fromstr ? "WHERE $fromstr" : "") : ($wheresql.($fromstr ? " AND " : "").$fromstr);
				$query = $db->query("SELECT guid FROM {$tblprefix}gurls $nwheresql ORDER BY guid DESC LIMIT 0,$atpp");
				while($item = $db->fetch_array($query)) $selectid[] = $item['guid'];
			}
		}
		if(!empty($arcdeal)){
			if($arcdeal == 'delete'){
				$idstr = multi_str($selectid);
				$db->query("DELETE FROM {$tblprefix}gurls WHERE guid $idstr OR pid $idstr", 'UNBUFFERED');
			}elseif($arcdeal == 'gather'){
				$progress = new Progress();
				$gather = new cls_gather;
				$gather->set_mission($gsid);
				foreach($selectid as $guid) $gather->gather_guid($guid,0);
				unset($gather);
			}elseif($arcdeal == 'output'){
				$progress = new Progress();
				$gather = new cls_gather;
				$gather->set_mission($gsid);
				foreach($selectid as $guid) $gather->output_guid($guid);
				unset($gather);
			}elseif($arcdeal == 'abover'){
				$gmissions[$gsid]['sonid'] && $db->query("UPDATE {$tblprefix}gurls SET abover='$arcabover' WHERE guid ".multi_str($selectid),'UNBUFFERED');
			}elseif($arcdeal == 'regather'){
				$db->query("UPDATE {$tblprefix}gurls SET gatherdate=0,outputdate=0 WHERE guid ".multi_str($selectid),'UNBUFFERED');
			}
		}
		empty($progress) || $progress->hide();
		if(!empty($select_all)){
			$npage ++;
			if($npage <= $pages){
				$fromid = min($selectid);
				$transtr = '';
				$transtr .= "&select_all=1";
				$transtr .= "&pages=$pages";
				$transtr .= "&npage=$npage";
				$transtr .= "&barcsedit=1";
				$transtr .= "&fromid=$fromid";
				cls_message::show("�ļ��������ڽ�����...<br>ȫ�� $pages ҳ,���ڴ��� $npage ҳ<br><br>
				<a href=\"?entry=gmissions&action=contentsoption&gsid=$gsid$filterstr\">>>��ֹ��ǰ����</a>",
				"?entry=gmissions&action=contentsoption&gsid=$gsid&page=$page$filterstr$transtr$parastr&arcdeal=$arcdeal",200);
			}
		}
		adminlog('�����ռ�����');
		cls_message::show('�ɼ�����������ɣ�',"?entry=gmissions&action=contentsoption&gsid=$gsid$filterstr");
	}
}elseif($action == 'contentdetail' && $guid){
	if(!$item = $db->fetch_one("SELECT * FROM {$tblprefix}gurls WHERE guid=".$guid)) cls_message::show('��ָ����ȷ�Ĳɼ���¼��');
	tabheader('�ɼ����');
	trbasic('��ַ����','',mhtmlspecialchars($item['utitle']),'');
	trbasic('������ַ','',$item['gurl'] ? "<a href=\"$item[gurl]\" target=\"_blank\">$item[gurl]</a>" : '-','');
	trbasic('׷����ַ'.'1','',$item['gurl1'] ? "<a href=\"$item[gurl1]\" target=\"_blank\">$item[gurl1]</a>" : '-','');
	trbasic('׷����ַ'.'2','',$item['gurl2'] ? "<a href=\"$item[gurl2]\" target=\"_blank\">$item[gurl2]</a>" : '-','');
	if($item['contents']){
		$item['contents'] = unserialize($item['contents']);
		$chid = $gmodels[$gmissions[$item['gsid']]['gmid']]['chid'];
		$fields = cls_cache::Read('fields',$chid);
		$cotypes = cls_cache::Read('cotypes');
		$cfields = array('caid'=>array('datatype'=>'select','cname'=>'��Ŀ'));
		foreach($cotypes as $k=>$v){
			$cfields['ccid'.$k]['datatype'] = $v['asmode'] ? 'mselect' : 'select';
			$cfields['ccid'.$k]['cname'] = $v['cname'];
		}
		$fields = $cfields + $fields + array('jumpurl'=>array('datatype'=>'text','cname'=>'��תURL'),'createdate'=>array('datatype'=>'text','cname'=>'���ʱ��'),'mname'=>array('datatype'=>'text','cname'=>'��Ա����'));;
		foreach($item['contents'] as $k => $v){
			trbasic('['.$fields[$k]['cname'].']'.'�ɼ����','',mhtmlspecialchars($v),'');
		}
	}elseif($item['outputdate']){
		trbasic('�ɼ����','','�����','');
	}
	tabfooter();
}elseif($action == 'urlsauto' && $gsid){
	empty($gmissions[$gsid]) && cls_message::show('��ָ����ȷ�Ĳɼ�����');
	if(!submitcheck('confirm')){
		$message = '��ѡ���������ɼ���ǰ����(����������)��������ַ��<br>��ʾ��һ��ȫ����ɰ����ⲽ����'."<br><br>";
		$message .= "ȷ������>><a href=?entry=gmissions&action=urlsauto&gsid=$gsid&confirm=ok>��ʼ</a>";
		cls_message::show($message);
	}
	$gather = new cls_gather;
	$gather->set_mission($gsid);
	$surls = $gather->fetch_surls();
	$progress = new Progress();
	foreach($surls as $surl) $gather->fetch_gurls($surl);
	unset($gather);
	$progress->hide();
	adminlog('�����Զ��ɼ�');
	cls_message::show('������ַ�ɼ����');

}elseif($action == 'gatherauto' && $gsid){
	empty($gmissions[$gsid]) && cls_message::show('��ָ����ȷ�Ĳɼ�����');
	if(!submitcheck('confirm')){
		$message = '��ѡ���˲ɼ���ǰ����(����������)���ĵ����ݣ�<br>��ʾ��һ��ȫ����ɰ����˱����Ĳ�����<br><br>';
		$message .= "ȷ������>><a href=?entry=gmissions&action=gatherauto&gsid=$gsid&confirm=ok>��ʼ</a>";
		cls_message::show($message);
	}
	$gmission = cls_cache::Read('gmission',$gsid,'');
	//�Ѳɼ���δ���ĺϼ��е�������Ҳ��Ҫ�ɼ�
	$wheresql = "WHERE gsid='$gsid' AND ".($gmission['sonid'] ? 'abover=0' : 'gatherdate=0');
	if(empty($pages)){
		if(!$nums = $db->result_one("SELECT COUNT(*) FROM {$tblprefix}gurls $wheresql")) cls_message::show('�޲ɼ���Ŀ');
		$pages = @ceil($nums / $atpp);
		$npage = $fromid = 0;
	}
	$npage = empty($npage) ? 0 : $npage;
	$gather = new cls_gather;
	$gather->set_mission($gsid);
	$gather->gather_fields();//���з����ɼ�����
	empty($gather->fields) && cls_message::show('�����òɼ�����!');
	$progress = new Progress();
	$query = $db->query("SELECT guid FROM {$tblprefix}gurls $wheresql AND guid>'$fromid' ORDER BY guid ASC LIMIT 0,$atpp");
	while($row = $db->fetch_array($query)){
		$gather->gather_guid($row['guid'],0);
		$fromid = $row['guid'];
	}
	unset($gather);
	$npage ++;
	if($npage <= $pages){
		cls_message::show("�ļ��������ڽ�����...<br>�� $pages ҳ�����ڴ���� $npage ҳ<br><br><a href=\"?entry=gmissions&action=gmissionsedit\">>>��ֹ��ǰ����</a>","?entry=gmissions&action=gatherauto&gsid=$gsid&pages=$pages&npage=$npage&fromid=$fromid&confirm=ok");
	}
	$progress->hide();
	adminlog('�����Զ��ɼ�');
	cls_message::show('�����Զ��ɼ����');

}elseif($action == 'outputauto' && $gsid){
	empty($gmissions[$gsid]) && cls_message::show('��ָ����ȷ�Ĳɼ�����');
	if(!submitcheck('confirm')){
		$message = "��ѡ���˽���ǰ����(����������)�е�����������⣡<br>��ʾ��һ��ȫ����ɰ����˱����Ĳ�����<br><br>";
		$message .= "ȷ������>><a href=?entry=gmissions&action=outputauto&gsid=$gsid&confirm=ok>��ʼ</a>";
		cls_message::show($message);
	}
	$gmission = cls_cache::Read('gmission',$gsid,'');
	//����⵫δ���ĺϼ��е�������Ҳ��Ҫ���
	$wheresql = "WHERE gsid='$gsid' AND gatherdate<>'0' AND ".($gmission['sonid'] ? 'abover=0' : 'outputdate=0');
	if(empty($pages)){
		if(!$nums = $db->result_one("SELECT COUNT(*) FROM {$tblprefix}gurls $wheresql")) cls_message::show('�������Ŀ');
		$pages = @ceil($nums / $atpp);
		$npage = $fromid = 0;
	}
	$gather = new cls_gather;
	$gather->set_mission($gsid);
	$gather->output_configs();//���з���������
	empty($gather->oconfigs) && cls_message::show('������������!');
	$progress = new Progress();
	$query = $db->query("SELECT guid FROM {$tblprefix}gurls $wheresql AND guid>'$fromid' ORDER BY guid ASC LIMIT 0,$atpp");
	while($row = $db->fetch_array($query)){
		$gather->output_guid($row['guid']);
		$fromid = $row['guid'];
	}
	$progress->hide();
	unset($gather);
	$npage ++;
	if($npage <= $pages){
		cls_message::show("�ļ��������ڽ�����...<br>�� $pages ҳ�����ڴ���� $npage ҳ<br><br><a href=\"?entry=gmissions&action=gmissionsedit\">>>��ֹ��ǰ����</a>","?entry=gmissions&action=outputauto&gsid=$gsid&pages=$pages&npage=$npage&fromid=$fromid&confirm=ok");
	}
	adminlog('�����Զ����');
	cls_message::show('�����Զ�������');
}elseif($action == 'allauto' && $gsid){
	empty($gmissions[$gsid]) && cls_message::show('��ָ����ȷ�Ĳɼ�����');
	if(!submitcheck('confirm')){
		$message = '��ѡ����һ��������²�����<br>��ַ�ɼ������ݲɼ���������⣡<br>ִ��֮ǰȷ�����й����Ѿ�������ɡ�<br><br>';
		$message .= "ȷ������>><a href=?entry=gmissions&action=allauto&gsid=$gsid&confirm=ok>��ʼ</a>";
		cls_message::show($message);
	}
	$gmission = cls_cache::Read('gmission',$gsid,'');
	$gather = new cls_gather;
	$gather->set_mission($gsid);
	$progress = new Progress();
	if(empty($deal)){
		$surls = $gather->fetch_surls();
		foreach($surls as $surl){
			$gather->fetch_gurls($surl);
		}
		$progress->hide();
		cls_message::show('������ַ�ɼ���ϣ�<br> ϵͳ���Զ�ת�����ݲɼ���',"?entry=gmissions&action=allauto&gsid=$gsid&deal=gather&confirm=ok");
	}elseif($deal == 'gather'){
		//�Ѳɼ���δ���ĺϼ��е�������Ҳ��Ҫ�ɼ�
		$wheresql = "WHERE gsid='$gsid' AND ".($gmission['sonid'] ? 'abover=0' : 'gatherdate=0');
		if(empty($pages)){
			if(!$nums = $db->result_one("SELECT COUNT(*) FROM {$tblprefix}gurls $wheresql")) cls_message::show('û����Ҫ�ɼ���������ַ��');
			$pages = @ceil($nums / $atpp);
			$npage = $fromid = 0;
		}
		$npage = empty($npage) ? 0 : $npage;
		$gather = new cls_gather;
		$gather->set_mission($gsid);
		$gather->gather_fields();//���з����ɼ�����
		empty($gather->fields) && cls_message::show('����ɼ����������ԣ�');
		$query = $db->query("SELECT guid FROM {$tblprefix}gurls $wheresql AND guid>'$fromid' ORDER BY guid ASC LIMIT 0,$atpp");
		while($row = $db->fetch_array($query)){
			$gather->gather_guid($row['guid'],0);
			$fromid = $row['guid'];
		}
		unset($gather);
		$npage ++;
		if($npage <= $pages){
			cls_message::show("�ļ��������ڽ�����...<br>�� $pages ҳ�����ڴ���� ".($npage+1)." ҳ<br><br><a href=\"?entry=gmissions&action=gmissionsedit\">>>��ֹ��ǰ����</a>","?entry=gmissions&action=allauto&gsid=$gsid&deal=gather&pages=$pages&npage=$npage&fromid=$fromid&confirm=ok");
		}
		$progress->hide();
		cls_message::show('���ݲɼ���ɣ�<br> ϵͳ�����Զ���������⣡',"?entry=gmissions&action=allauto&gsid=$gsid&deal=output&confirm=ok");
	}elseif($deal == 'output'){
		$progress->hide();
		//����⵫δ���ĺϼ��е�������Ҳ��Ҫ���
		$wheresql = "WHERE gsid='$gsid' AND gatherdate<>'0' AND ".($gmission['sonid'] ? 'abover=0' : 'outputdate=0');
		if(empty($pages)){
			if(!$nums = $db->result_one("SELECT COUNT(*) FROM {$tblprefix}gurls $wheresql")) cls_message::show('�������Ŀ',"?entry=gmissions&action=gmissionsedit");
			$pages = @ceil($nums / $atpp);
			$npage = $fromid = 0;
		}
		$gather = new cls_gather;
		$gather->set_mission($gsid);
		$gather->output_configs();//���з���������
		empty($gather->oconfigs) && cls_message::show('����ɼ����������ԣ�');
		$query = $db->query("SELECT guid FROM {$tblprefix}gurls $wheresql AND guid>'$fromid' ORDER BY guid ASC LIMIT 0,$atpp");
		while($row = $db->fetch_array($query)){
			$gather->output_guid($row['guid']);
			$fromid = $row['guid'];
		}
		unset($gather);
		$npage ++;
		if($npage <= $pages){
			cls_message::show("�ļ��������ڽ�����...<br>�� $pages ҳ�����ڴ���� ".($npage+1)." ҳ<br><br><a href=\"?entry=gmissions&action=gmissionsedit\">>>��ֹ��ǰ����</a>","?entry=gmissions&action=allauto&gsid=$gsid&deal=output&pages=$pages&npage=$npage&fromid=$fromid&confirm=ok");
		}
		cls_message::show('һ���ɼ�ȫ��������ɣ�');
	}
}elseif($action == 'break'){
	cls_message::show('��ֹ�������', axaction(2, "?entry=gmissions&action=gmissionsedit"));
}
function gmission_list($gsid = 0){
	global $gmission;
	$gmission = cls_cache::Read('gmission',$gsid,'');
	$gmodel = cls_cache::Read('gmodel',$gmission['gmid'],'');
	$levelstr = !empty($gmission['pid']) ? '&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; ' : '';
	$addstr = !empty($gmission['pid']) ? 'Y' : (!empty($gmission['sonid']) ? '-' : "<a href=\"?entry=gmissions&action=gmissionadd&pid=$gsid\" onclick=\"return floatwin('open_gmission',this)\">���</a>");
	$regularstr = "<a href=\"?entry=gmissions&action=gmissionurls&gsid=$gsid\" onclick=\"return floatwin('open_gmission',this)\">����</a>";
	$gatherstr = !empty($gmission['pid']) ? '&nbsp;' : "<a href=\"?entry=gmissions&action=allauto&gsid=$gsid\" onclick=\"return floatwin('open_gmission_gather',this)\"><b>һ��</b></a>&nbsp;" .
				 "<a href=\"?entry=gmissions&action=urlsauto&gsid=$gsid\" onclick=\"return floatwin('open_gmission_gather',this)\">��ַ</a>&nbsp;" .
				 "<a href=\"?entry=gmissions&action=gatherauto&gsid=$gsid\" onclick=\"return floatwin('open_gmission_gather',this)\">����</a>&nbsp;" .
				 "<a href=\"?entry=gmissions&action=outputauto&gsid=$gsid\" onclick=\"return floatwin('open_gmission_gather',this)\">���</a>";
	echo "<tr class=\"txt\">".
		"<td class=\"txtL\">$levelstr<input type=\"text\" size=\"20\" name=\"gmissionsnew[$gsid][cname]\" value=\"$gmission[cname]\"></td>\n".
		"<td class=\"txtC\">$addstr</td>\n".
		"<td class=\"txtC\">$gmodel[cname]</td>\n".
		"<td class=\"txtC w70\">$regularstr</td>\n".
		"<td class=\"txtC w120\">$gatherstr</td>\n".
		"<td class=\"txtC w40\"><input class=\"checkbox\" type=\"checkbox\" name=\"delete[$gsid]\" value=\"$gsid\" onclick=\"deltip()\">\n".
		"<td class=\"txtC w40\"><a href=\"?entry=gmissions&action=contentsoption&gsid=$gsid\" onclick=\"return floatwin('open_gmission',this)\">����</a></td>".
		"<td class=\"txtC w60\"><a href=\"?entry=gmissions&action=gmissioncopy&gsid=$gsid\" onclick=\"return floatwin('open_gmission',this)\">����</a></td>".
		"</tr>\n";
}
function missionfield($cname,$ename,$setting=array(),$datatype='text'){
	global $rprojects,$gmodel;
	$mcell = in_array($datatype,array('images','files','flashs','medias')) ? 1 : 0;//�Ƿ��Ƕ༯ģʽ�ֶ�
	$noremote = in_array($datatype,array('int','float','select','mselect','text')) ? 1 : 0;//�Ƿ񲻴��ڸ����������ص��ֶ�
	${'clearhtml'.$ename} = (isset($setting['clearhtml']) && !$mcell) ? explode(',',$setting['clearhtml']) : array();
	$rpidsarr = array('0' => '������Զ���ļ�');foreach($rprojects as $k => $v) $rpidsarr[$k] = $v['cname'];
	$frompagearr = array('0' => '��������ҳ','1' => '��ַ�б�ҳ','2' => '����׷��ҳ1','3' => '����׷��ҳ2');
	//����id�Լ�����title���ַ���
	$title_str = '';
	$num_str = '';
	if(strstr($ename,'ccid') && ($ccid_arr = cls_cache::Read('coclasses',str_replace('ccid','',$ename)))){
		foreach($ccid_arr as $k => $v){
			$title_str .= "(|)$v[title]";
			$num_str .= "(|)$k";
		}
	}else if(in_array($datatype,array('select','mselect')) && $ename != 'caid'){
		$_field_arr = cls_cache::Read('fields',$gmodel['chid']);
		$_field_innertext = explode("\n",$_field_arr[$ename]['innertext']);	
		foreach($_field_innertext as $v){
			$_temparr = explode('=',str_replace(array("\r","\n"),'',$v));
			$_temparr[1] = isset($_temparr[1]) ? $_temparr[1] : $_temparr[0];		
			$title_str .= "(|)$_temparr[1]";
			$num_str .= "(|)$_temparr[0]";
		}
		unset($_field_arr,$_field_innertext,$_temparr);	
	}
	$title_str = empty($title_str)?'':substr($title_str,3);
	$num_str = empty($num_str)?'':substr($num_str,3);
	echo "<tr class=\"category\"><td class=\"txtL\"><b>[".mhtmlspecialchars($cname)."]</b></td><td colspan=\"3\"></td></tr>";
	echo "<tr>\n".
		"<td width=\"15%\" class=\"txtR\">������Դҳ��</td>\n".
		"<td width=\"35%\" class=\"txtL\"><select style=\"vertical-align: middle;\" name=\"fsettingsnew[$ename][frompage]\">".makeoption($frompagearr,empty($setting['frompage']) ? 0 : $setting['frompage'])."</select></td>\n".
		"<td width=\"15%\" class=\"txtR\">���������</td>\n".
		"<td class=\"txtL\"><input type=\"text\" size=\"25\" name=\"fsettingsnew[$ename][func]\" value=\"".(empty($setting['func']) ? '' : mhtmlspecialchars($setting['func']))."\"></td>\n".
		"</tr>\n";
	if(!$mcell){
		echo "<tr>\n".
			"<td width=\"15%\" class=\"txtR\">�ֶ�����<br>�ɼ�ģӡ<br><span onclick=\"replace_html(this,'".$ename."ftag')\" style=\"color:#03F;cursor: pointer;\">(*)</span>&nbsp;<span onclick=\"replace_html(this,'".$ename."ftag')\" style=\"color:#03F;cursor: pointer;\">(?)</span></td>\n".
			"<td class=\"txtL\"><textarea rows=\"5\" id=\"".$ename."ftag\" name=\"fsettingsnew[$ename][ftag]\" cols=\"30\">".(isset($setting['ftag']) ? mhtmlspecialchars($setting['ftag']) : '')."</textarea></td>\n".
			"<td width=\"15%\" class=\"txtR\">���Html<br><input class=\"checkbox\" type=\"checkbox\" name=\"chk$ename\" onclick=\"checkall(this.form,'clearhtml$ename','chk$ename')\">ȫѡ</td>\n".
			"<td class=\"txtL\">";
			$html_arr = array('1'=>'a','2'=>'br','3'=>'table','4'=>'tr','5'=>'td','6'=>'p','7'=>'font','8'=>'div','9'=>'tbody','10'=>'tbody','11'=>'b','12'=>'&amp;nbsp;','13'=>'script');
		foreach($html_arr as $k => $v){
			echo "<input type=\"checkbox\" class=\"checkbox\" name=\"clearhtml{$ename}[]\" value=\"$k\"".(in_array($k,${'clearhtml'.$ename}) ? " checked" : "").">".(in_array($k,array('12'))?$v:"&lt;".($v)."&gt;").($k%4==0?"<br>":'')."\n";
		}
		echo "</td>\n</tr>\n";
		echo "<tr>\n".
			"<td width=\"15%\" class=\"txtR\">�滻��Ϣ<br> ��Դ����<br>".(!empty($title_str) && !empty($num_str)?"<span style=\"color:#03F;cursor: pointer;\" onclick=\"export_ccid('".$ename."','".$title_str."','".$num_str."')\">(����'".$cname."')</span><span onclick=\"add_html('".$ename."')\" style=\"color:#03F;cursor: pointer;\">(|)</span><span style=\"color:#F03;cursor: pointer;\" onclick=\"clear_ccid('".$ename."')\"><br>(���'".$cname."')</span>":'')."</td>\n".
			"<td class=\"txtL\"><textarea rows=\"5\" ".(!empty($title_str) && !empty($num_str)?"id=\"".$ename."_from\"":'')." name=\"fsettingsnew[$ename][fromreplace]\" cols=\"30\">".(isset($setting['fromreplace']) ? mhtmlspecialchars($setting['fromreplace']) : '')."</textarea></td>\n".
			"<td width=\"15%\" class=\"txtR\">�滻��Ϣ<br>=>�������</td>\n".
			"<td class=\"txtL\"><textarea rows=\"5\" ".(!empty($title_str) && !empty($num_str)?"id=\"".$ename."_to\"":'')." name=\"fsettingsnew[$ename][toreplace]\" cols=\"30\">".(isset($setting['toreplace']) ? mhtmlspecialchars($setting['toreplace']) : '')."</textarea></td>\n".
			"</tr>\n";
	}else{
		echo "<tr>\n".
			"<td width=\"15%\" class=\"txtR\">�б�����<br>�ɼ�ģӡ</td>\n".
			"<td class=\"txtL\"><textarea rows=\"4\" name=\"fsettingsnew[$ename][ftag]\" cols=\"30\">".(isset($setting['ftag']) ? mhtmlspecialchars($setting['ftag']) : '')."</textarea></td>\n".
			"<td width=\"15%\" class=\"txtR\">�б�Ԫ�ָ���ʶ</td>\n".
			"<td class=\"txtL\"><textarea rows=\"4\" name=\"fsettingsnew[$ename][splittag]\" cols=\"30\">".(isset($setting['splittag']) ? mhtmlspecialchars($setting['splittag']) : '')."</textarea></td>\n".
			"</tr>\n";
		echo "<tr>\n".
			"<td width=\"15%\" class=\"txtR\">��Ԫ����<br>�ɼ�ģӡ</td>\n".
			"<td class=\"txtL\"><textarea rows=\"4\" name=\"fsettingsnew[$ename][remotetag]\" cols=\"30\">".(isset($setting['remotetag']) ? mhtmlspecialchars($setting['remotetag']) : '')."</textarea></td>\n".
			"<td width=\"15%\" class=\"txtR\">��Ԫ����<br>�ɼ�ģӡ</td>\n".
			"<td class=\"txtL\"><textarea rows=\"4\" name=\"fsettingsnew[$ename][titletag]\" cols=\"30\">".(isset($setting['titletag']) ? mhtmlspecialchars($setting['titletag']) : '')."</textarea></td>\n".
			"</tr>\n";

	}
	if(!$noremote){
		echo "<tr>\n".
			"<td width=\"15%\" class=\"txtR\">Զ�����ط���</td>\n".
			"<td width=\"35%\" class=\"txtL\"><select style=\"vertical-align: middle;\" name=\"fsettingsnew[$ename][rpid]\">".makeoption($rpidsarr,empty($setting['rpid']) ? 0 : $setting['rpid'])."</select></td>\n".
			"<td width=\"15%\" class=\"txtR\">������ת�ļ���ʽ</td>\n".
			"<td class=\"txtL\"><input type=\"text\" size=\"25\" name=\"fsettingsnew[$ename][jumpfile]\" value=\"".(empty($setting['jumpfile']) ? '' : mhtmlspecialchars($setting['jumpfile']))."\"></td>\n".
			"</tr>\n";
	}
}

function check_rule_urls(&$g){
	!$g['uurls'] && (!$g['uregular'] || !$g['ufromnum'] || !$g['utonum']) && cls_message::show('�ֶ���Դ��ַ��������Դ��ַ������Ҫ��дһ��');
	$g['uspilit'] && $g['uurltag'] || cls_message::show('��ַ�б�ָ�������ַ�ɼ�ģӡ����Ϊ��');
}

function check_rule_cnts(&$g){
}
?>

