<?
(!defined('M_COM') || !defined('M_ADMIN')) && exit('No Permission');
aheader();
if($re = $curuser->NoBackFunc('fragment')) cls_message::show($re);
$frcatalogs = cls_cache::Read('frcatalogs');
$tclassarr = cls_Tag::TagClass(true);
empty($action) && $action = 'fragmentsedit';
empty($sclass) && $sclass = 0;
if($action == 'fragmentsedit'){
	backnav('fragment','fragment');
	$page = !empty($page) ? max(1, intval($page)) : 1;
	submitcheck('bfilter') && $page = 1;
	$frcaid = isset($frcaid) ? max(-1,intval($frcaid)) : -1;
	$checked = isset($checked) ? $checked : '-1';
	$valid = isset($valid) ? $valid : '-1';
	$keyword = empty($keyword) ? '' : $keyword;

	$wheresql = '';
	$fromsql = "FROM {$tblprefix}fragments";

	if($frcaid != -1) $wheresql .= " AND frcaid='$frcaid'";
	if($checked != -1) $wheresql .= " AND checked='$checked'";
	if($valid != -1) $wheresql .= $valid ? " AND startdate<'$timestamp' AND (enddate='0' OR enddate>'$timestamp')" : " AND (startdate>'$timestamp' OR (enddate!='0' AND enddate<'$timestamp'))";
	$keyword && $wheresql .= " AND (ename ".sqlkw($keyword)." OR title ".sqlkw($keyword).")";
	$wheresql = substr($wheresql,5);
	$wheresql = $wheresql ? "WHERE $wheresql" : '';

	$filterstr = '';
	foreach(array('keyword',) as $k) $$k && $filterstr .= "&$k=".rawurlencode(stripslashes($$k));
	foreach(array('frcaid','checked','valid',) as $k) $$k != -1 && $filterstr .= "&$k=".$$k;

	if(!submitcheck('bsubmit')){
		echo form_str($actionid.'arcsedit',"?entry=$entry&action=$action&page=$page");
		tabheader_e();
		echo "<tr><td colspan=\"2\" class=\"txt txtleft\">";
		echo "<input class=\"text\" name=\"keyword\" type=\"text\" value=\"$keyword\" size=\"8\" style=\"vertical-align: middle;\" title=\"���������Ψһ��ʶ\">&nbsp; ";
		echo "<select style=\"vertical-align: middle;\" name=\"frcaid\">".makeoption(array('-1' => '���޷���','0' => 'δ����',) + $frcatalogs,$frcaid)."</select>&nbsp; ";
		echo "<select style=\"vertical-align: middle;\" name=\"checked\">".makeoption(array('-1' => '���״̬','0' => 'δ��','1' => '����'),$checked)."</select>&nbsp; ";
		echo "<select style=\"vertical-align: middle;\" name=\"valid\">".makeoption(array('-1' => '��Ч״̬','0' => '��Ч','1' => '��Ч'),$valid)."</select>&nbsp; ";
		echo strbutton('bfilter','ɸѡ');
		tabfooter();

		//�б���
		tabheader("��Ƭ�б� &nbsp;>><a href=\"?entry=$entry&action=add\" onclick=\"return floatwin('open_fragment',this)\">�����Ƭ</a>",'','',10);
		$cy_arr = array("<input class=\"checkbox\" type=\"checkbox\" name=\"chkall\" onclick=\"checkall(this.form, 'selectid', 'chkall')\">",array('��Ƭ����','txtL'),array('Ψһ��ʶ','txtL'),);
		$cy_arr[] = array('ģ������','txtL');
		$cy_arr[] = '����';
		$cy_arr[] = '����';
		$cy_arr[] = '���';
		$cy_arr[] = '��Ч';
		$cy_arr[] = '����';
		$cy_arr[] = 'ģ��';
		$cy_arr[] = 'Ԥ��';
		$cy_arr[] = '����';
		trcategory($cy_arr);

		$pagetmp = $page;
		do{
			$query = $db->query("SELECT * $fromsql $wheresql ORDER BY vieworder LIMIT ".(($pagetmp - 1) * $atpp).",$atpp");
			$pagetmp--;
		} while(!$db->num_rows($query) && $pagetmp);

		$itemstr = '';
		while($r = $db->fetch_array($query)){
			$selectstr = "<input class=\"checkbox\" type=\"checkbox\" name=\"selectid[$r[ename]]\" value=\"$r[ename]\">";
			$aidstr = $r['ename'];
            $uri_str = '';
            empty($r['tclass'])|| $uri_str .= "&tclass={$r['tclass']}";
			$subjectstr = mhtmlspecialchars($r['title']);
			$tclassstr = @$tclassarr[$r['tclass']];
			$checkstr = $r['checked'] ? 'Y' : '-';
			$periodstr = $r['period'] ? $r['period'] : '-';
			$validstr = ($r['startdate'] < $timestamp) && (!$r['enddate'] || $r['enddate'] > $timestamp) ? 'Y' : '-';
			$setstr = "<a href=\"?entry=$entry&action=detail&ename=$r[ename]{$uri_str}\" onclick=\"return floatwin('open_fragment',this)\">����</a>";
			$editstr = "<a href=\"?entry=$entry&action=tpl&ename=$r[ename]{$uri_str}\" onclick=\"return floatwin('open_fragment',this)\">ģ��</a>";
			$pickstr = "<a href=\"?entry=$entry&action=pick&ename=$r[ename]{$uri_str}\" onclick=\"return floatwin('open_fragment',this)\">����</a>";
			$viewstr = "<a href=\"?entry=$entry&action=view&ename=$r[ename]{$uri_str}\" onclick=\"return floatwin('open_fragment',this)\">Ԥ��</a>";

			$itemstr .= "<tr class=\"txt\"><td class=\"txtC w40\">$selectstr</td><td class=\"txtL\">$subjectstr</td><td class=\"txtL\">$aidstr</td>\n";
			$itemstr .= "<td class=\"txtL\">$tclassstr</td>\n";
			$itemstr .= "<td class=\"txtC w40\"><input type=\"text\" size=\"4\" maxlength=\"4\" name=\"fmdata[$r[ename]][vieworder]\" value=\"$r[vieworder]\"></td>\n";
			$itemstr .= "<td class=\"txtC w40\">$periodstr</td>\n";
			$itemstr .= "<td class=\"txtC w35\">$checkstr</td>\n";
			$itemstr .= "<td class=\"txtC w35\">$validstr</td>\n";
			$itemstr .= "<td class=\"txtC w35\">$setstr</td>\n";
			$itemstr .= "<td class=\"txtC w35\">$editstr</td>\n";
			$itemstr .= "<td class=\"txtC w35\">$viewstr</td>\n";;
			$itemstr .= "<td class=\"txtC w35\">$pickstr</td>\n";;
			$itemstr .= "</tr>\n";
		}
		$counts = $db->result_one("SELECT count(*) $fromsql $wheresql");
		$multi = multi($counts,$atpp,$page,"?entry=$entry&action=$action$filterstr");

		echo $itemstr;
		tabfooter();
		echo $multi;

		tabheader('������Ŀ');
		$s_arr = array();
		$s_arr['delete'] = 'ɾ��';
		$s_arr['check'] = '���';
		$s_arr['uncheck'] = '����';
		$s_arr['update'] = '���»���';
		if($s_arr){
			$soperatestr = '';
			foreach($s_arr as $k => $v) $soperatestr .= "<input class=\"checkbox\" type=\"checkbox\" id=\"arcdeal[$k]\" name=\"arcdeal[$k]\" value=\"1\"" . ($k == 'delete' ? ' onclick="deltip()"' : '') . "><label for=\"arcdeal[$k]\">$v</label> &nbsp;";
			trbasic('ѡ�������Ŀ','',$soperatestr,'');
		}
		tabfooter('bsubmit');

	}else{
		if(empty($fmdata)) cls_message::show('��ѡ����Ϣ',"?entry=$entry&action=$action&page=$page$filterstr");
		foreach($fmdata as $k => $v) $db->query("UPDATE {$tblprefix}fragments SET vieworder=".max(0,intval($v['vieworder']))." WHERE ename='$k'");
		if(!empty($selectid)){
			if(!empty($arcdeal['delete'])){
				foreach($selectid as $k => $v){
					cls_CacheFile::Del($v['tclass'] ? 'ctag' : 'rtag','fr_'.$k,'');
					if(!_08_FileSystemPath::CheckPathName($v)) clear_dir(M_ROOT."dynamic/fragment/$v",true);
				}
				$db->query("DELETE FROM {$tblprefix}fragments WHERE ename ".multi_str($selectid));
			}else{
				if(!empty($arcdeal['check'])){
					$db->query("UPDATE {$tblprefix}fragments SET checked=1 WHERE ename ".multi_str($selectid));
				}elseif(!empty($arcdeal['uncheck'])){
					$db->query("UPDATE {$tblprefix}fragments SET checked=0 WHERE ename ".multi_str($selectid));
				}
				if(!empty($arcdeal['update'])){
					foreach($selectid as $k => $v){
						if(!_08_FileSystemPath::CheckPathName($v)) clear_dir(M_ROOT."dynamic/fragment/$v",true);
					}
				}
			}
		}
		cls_CacheFile::Update('fragments');
		adminlog('��Ƭ����','��Ƭ�б����');
		cls_message::show('��Ƭ�������',"?entry=$entry&action=$action&page=$page$filterstr");

	}
}elseif($action == 'add'){
	if(!submitcheck('bsubmit')){
		tabheader('�����Ƭ','fragmentadd',"?entry=$entry&action=$action",2,1,1);
		trbasic('��Ƭ����','fmdata[title]','','text',array('validate' => ' onfocus="initPinyin(\'fmdata[ename]\')"' . makesubmitstr('fmdata[title]',1,0,3,30)));
		trbasic('��ƬӢ�ı�ʶ','','<input type="text" value="" name="fmdata[ename]" id="fmdata[ename]" size="25" ' . makesubmitstr('fmdata[ename]',1,'tagtype',0,30) . ' offset="2">&nbsp;&nbsp;<input type="button" value="�������" onclick="check_repeat(\'fmdata[ename]\',\'frnamesame\');">&nbsp;&nbsp;<input type="button" value="�Զ�ƴ��" onclick="autoPinyin(\'fmdata[title]\',\'fmdata[ename]\')" />','');
		trbasic('��Ƭ����','fmdata[frcaid]',makeoption(array(0 => '������') + $frcatalogs),'select');
		trbasic('ģ������','fmdata[tclass]',makeoption($tclassarr),'select');
		trbasic('��ʼ����',"fmdata[startdate]",'','calendar',array('guide'=>'��������������','validate'=>makesubmitstr("fmdata[startdate]",0,0,0,0,'date')));
		trbasic('��������',"fmdata[enddate]",'','calendar',array('guide'=>'��������������','validate'=>makesubmitstr("fmdata[enddate]",0,0,0,0,'date')));
		trbasic('������ı�����','fmdata[params]','','text',array('guide' => '���ŷָ������������ָ�����������charset������ȡֵgbk/big5/utf-8��'));
		trbasic('��������',"fmdata[period]",'','text',array('guide'=>'��λ�����ӣ�����Ϊ�����档','validate'=>makesubmitstr("fmdata[period]",0,'int',0,4)));
		tabfooter('bsubmit');
		a_guide('fragmentadd');
	}else{
		$fmdata['title'] = trim(strip_tags($fmdata['title']));
		if(!$fmdata['title'] || !$fmdata['ename']) cls_message::show('���ϲ���ȫ',M_REFERER);
		if(preg_match("/[^a-zA-Z_0-9]+/",$fmdata['ename'])) cls_message::show('��ʶ���Ϲ淶',M_REFERER);
		$fmdata['ename'] = strtolower($fmdata['ename']);
		if(in_array($fmdata['ename'],ename_arr())) cls_message::show('��ʶ�ظ�',M_REFERER);
		foreach(array('startdate','enddate') as $var){
			if(isset($fmdata[$var])){
				$fmdata[$var] = trim($fmdata[$var]);
				$fmdata[$var] = !cls_string::isDate($fmdata[$var]) ? 0 : strtotime($fmdata[$var]);
			}
		}
		$fmdata['params'] = trim(strip_tags($fmdata['params']));
		$params = explode(',',$fmdata['params']);
		foreach($params as $k => $v){
			if(preg_match("/[^a-zA-Z_0-9]+/",$v)) unset($params[$k]);
		}
		$fmdata['params'] = implode(',',$params);
		$fmdata['period'] = max(0,intval($fmdata['period']));
		$db->query("INSERT INTO {$tblprefix}fragments SET
		ename ='$fmdata[ename]',
		title='$fmdata[title]',
		frcaid='$fmdata[frcaid]',
		tclass='$fmdata[tclass]',
		startdate='$fmdata[startdate]',
		enddate='$fmdata[enddate]',
		params='$fmdata[params]',
		period='$fmdata[period]'
		");
		adminlog('�����Ƭ');
		cls_CacheFile::Update('fragments');
		cls_message::show('��Ƭ�����ɣ�����������ģ�塣',"?entry=$entry&action=tpl&ename=$fmdata[ename]&tclass={$fmdata['tclass']}");
	}

}elseif($action == 'detail' && $ename){
	if(!($fragment = fetch_one($ename))) cls_message::show('��ָ����ȷ����Ƭ��');
	if(!submitcheck('bsubmit')){
		tabheader('��Ƭ����','fragmentdetail',"?entry=$entry&action=$action&ename=$ename",2,1,1);
		trbasic('��Ƭ����','fmdata[title]',$fragment['title'],'text',array('validate' => ' onfocus="initPinyin(\'fmdata[ename]\')"' . makesubmitstr('fmdata[title]',1,0,3,30)));
		trbasic('��ƬӢ�ı�ʶ','',$fragment['ename'],'');
		trbasic('��Ƭ����','fmdata[frcaid]',makeoption(array(0 => '������') + $frcatalogs,$fragment['frcaid']),'select');
		trbasic('ģ������','',@$tclassarr[$fragment['tclass']],'');
		trbasic('��ʼ����',"fmdata[startdate]",$fragment['startdate'] ? date('Y-m-d',$fragment['startdate']) : '','calendar',array('guide'=>'��������������','validate'=>makesubmitstr("fmdata[startdate]",0,0,0,0,'date')));
		trbasic('��������',"fmdata[enddate]",$fragment['enddate'] ? date('Y-m-d',$fragment['enddate']) : '','calendar',array('guide'=>'��������������','validate'=>makesubmitstr("fmdata[enddate]",0,0,0,0,'date')));
		trbasic('������ı�����','fmdata[params]',$fragment['params'],'text',array('guide' => '���ŷָ������������ָ�����������charset������ȡֵgbk/big5/utf-8��'));
		trbasic('��������',"fmdata[period]",$fragment['period'],'text',array('guide'=>'��λ�����ӣ�����Ϊ�����档','validate'=>makesubmitstr("fmdata[period]",0,'int',0,4)));
		tabfooter('bsubmit');
		a_guide('fragmentdetail');
	}else{
		$fmdata['title'] = trim(strip_tags($fmdata['title']));
		if(!$fmdata['title']) cls_message::show('���ϲ���ȫ',M_REFERER);
		foreach(array('startdate','enddate') as $var){
			if(isset($fmdata[$var])){
				$fmdata[$var] = trim($fmdata[$var]);
				$fmdata[$var] = !cls_string::isDate($fmdata[$var]) ? 0 : strtotime($fmdata[$var]);
			}
		}
		$fmdata['params'] = trim(strip_tags($fmdata['params']));
		$params = explode(',',$fmdata['params']);
		foreach($params as $k => $v){
			if(preg_match("/[^a-zA-Z_0-9]+/",$v)) unset($params[$k]);
		}
		$fmdata['params'] = implode(',',$params);
		$fmdata['period'] = max(0,intval($fmdata['period']));

		$db->query("UPDATE {$tblprefix}fragments SET
		title='$fmdata[title]',
		frcaid='$fmdata[frcaid]',
		startdate='$fmdata[startdate]',
		enddate='$fmdata[enddate]',
		params='$fmdata[params]',
		period='$fmdata[period]'
		WHERE ename ='$ename'
		");
		adminlog('������Ƭ');
		cls_CacheFile::Update('fragments');
		cls_message::show('��Ƭ�������', axaction(6,"?entry=$entry&action=fragmentsedit"));
	}

}elseif($action == 'view' && $ename){
	if(!($fragment = fetch_one($ename))) cls_message::show('��ָ����ȷ����Ƭ��');
	$na = array_filter(explode(',',$fragment['params']));
	if($na){
		tabheader('������Ƭ��Ԥ������','fragmentdetail',"{$cms_abs}api/pick.php",2,0,1,0,'get');
		trhidden('frname',$ename);
		trhidden('frview',1);
		foreach($na as $k) trbasic("$k ������ֵ",$k,'','text',$k == 'charset' ? array('guide' => 'ָ�����ݵı��룬ȡֵgbk/big5/utf-8') : array());
		tabfooter('bsubmit','Ԥ��');
	}else mheader("location:{$cms_abs}api/pick.php?frname=$ename&frview=1");
}elseif($action == 'pick' && $ename){
	if(!($fragment = fetch_one($ename))) cls_message::show('��ָ����ȷ����Ƭ��');
	$pstr = '';
	if($na = array_filter(explode(',',$fragment['params']))){
		foreach($na as $k) $pstr .= "&$k=����ֵ";
	}
	tabheader('վ�ڵ�����Ƭ���� - '.$fragment['title']);
	trbasic("��Ƭjs���ô���",'js',"<script language=\"javascript\" src=\"{\$cms_abs}api/pick.php?frname=$ename$pstr\"></script>",'textarea',array('w' => 560,'h' => 30,'guide' => '������ҳ��ģ������Ҫ���ø���Ƭ���ݵ�λ�ã����б��������������ñ���ֵ��'));
	trbasic("ģ���ʶ���ô���",'tag',"{c\$pre_$ename [tclass=fragment/] [url={\$ cms_abs}api/pick.php?frname=$ename&frdata=1$pstr/] [ttl=1800/] [timeout=2/]}{/c\$pre_$ename}",'textarea',array('w' => 560,'h' => 50,'guide' => '������ҳ��ģ������Ҫ���ø���Ƭ���ݵ�λ�ã����б��������������ñ���ֵ��<br>pre_'.$ename.'(��ʶ��:ע����βͬ��),ttl(��������),timeout(��ʱ����)���������޸ġ�'));
	trbasic("��Ƭ������ȡurl",'xml',"{\$ cms_abs}api/pick.php?frname=$ename&frdata=1$pstr",'textarea',array('w' => 560,'h' => 30,'guide' => '�����Զ��� ��Ƭ���� ģ���ʶʱ����urlֵ�����б��������������ñ���ֵ��'));
	tabfooter();
	tabheader('��վ������Ƭ���� - '.$fragment['title']);
	trbasic("��Ƭjs���ô���",'js',"<script language=\"javascript\" src=\"{$cms_abs}api/pick.php?frname=$ename$pstr\"></script>",'textarea',array('w' => 560,'h' => 30,'guide' => '�������κκ��ĵ�ϵͳ���п�վ���ݵ�js���á�'));
	trbasic("ģ���ʶ���ô���",'tag',"{c\$pre_$ename [tclass=fragment/] [url={$cms_abs}api/pick.php?frname=$ename&frdata=1$pstr/] [ttl=1800/] [timeout=2/]}{/c\$pre_$ename}",'textarea',array('w' => 560,'h' => 50,'guide' => 'ֻ������08cms���ĵ�ϵͳ���п�վ���ݵ���,���ڵ��÷�ϵͳ�����ҳ��ģ���ڡ�'));
	trbasic("��Ƭ������ȡurl",'xml',"{$cms_abs}api/pick.php?frname=$ename&frdata=1$pstr",'textarea',array('w' => 560,'h' => 30,'guide' => '������08cms���ĵ�ϵͳ�Զ��� ��Ƭ���� ģ���ʶʱ����urlֵ��Ҳ�����������ƽӿڵ�����ϵͳ��'));
	tabfooter();
}elseif($action == 'tpl' && $ename){
	if(!($fragment = fetch_one($ename))) cls_message::show('��ָ����ȷ����Ƭ��');
	$ttype = $fragment['tclass'] ? 'ctag' : 'rtag';
	include_once dirname(__FILE__) . '/mtags/_taginit.php';
	$mtags = load_mtags($ttype);
	$isadd = 1;$_infragment = 1;
	if($mtag = cls_cache::Read($ttype,'fr_'.$ename,'')) $isadd = 0;
    $mtag = array_merge((array) $mtag, (array) @$mtagnew);
    empty($_POST) || cls_Array::array_stripslashes($mtag);
	$tclass = $mtagnew['tclass'] = empty($fragment['tclass']) ? '' : $fragment['tclass'];
    $sclass = @$mtag['setting']['chids']; // = '4'; //Ĭ��û���޸Ĺ���ģ�ͣ�include���滹Ҫ������
    _08_FilesystemFile::filterFileParam($tclass);
	$mtagnew['ename'] = 'fr_'.$ename;
	$mtagnew['cname'] = '��Ƭ_'.$fragment['title'];
	if(!submitcheck($isadd ? 'bmtagadd' : 'bmtagsdetail')){
		$upform = in_array($tclass,array('image','images',)) ? 1 : 0;
		$helpstr = !$tclass ? '' : "&nbsp; &nbsp;>><a href=\"tools/taghelp.html#".(str_replace('tag','',$ttype).'_'.$tclass)."\" target=\"08cmstaghelp\">����</a>";
		tabheader('��Ƭ���ݵ���ģ��'.$helpstr,'mtagsadd',"?entry=$entry&action=$action&ename=$ename",2,$upform);
        $mtagses = _08_factory::getMtagsInstance($tclass);
        if ( is_object($mtagses) )
        {
            $mtagses->showCotypesSelect($mtag);
            # ����Ǳ༭ѡ��ʱ�ö���sclass
            if( empty($_POST) )
            {
                trhidden('_sclass', $mtagses->getSclass(@(array)$mtag['setting']));
            }
        }
		trbasic('��ʶ����','',$mtagnew['cname'], '');
		trbasic('��ʶӢ������','',$mtagnew['ename'], '');
		
		list($modeAdd,$modeSave) = array($isadd,0);
		include(dirname(__FILE__) . "/mtags/".($tclass ? $tclass : 'rtag').".php");
		/*$b_flag = submitcheck('re_preid') || ($modeAdd && !submitcheck('set_preid'));
		if(!$b_flag || empty($tclass)) */
		tabfooter($isadd ? 'bmtagadd' : 'bmtagsdetail','�ύ');
		
		a_guide($ttype.(empty($mtagnew['tclass']) ? 'edit' : $mtagnew['tclass']));
	}else{
		list($modeAdd,$modeSave) = array($isadd,1);
		include_once dirname(__FILE__) . "/mtags/".($tclass ? $tclass : 'rtag').".php";
		$mtagnew['setting'] = empty($mtagnew['setting']) ? array() : $mtagnew['setting'];
		if(!empty($mtagnew['setting'])){
			foreach($mtagnew['setting'] as $key => $val){
				if(in_array($key,$unsetvars) && empty($val)) unset($mtagnew['setting'][$key]);
				if(!empty($unsetvars1[$key]) && in_array($val,$unsetvars1[$key])) unset($mtagnew['setting'][$key]);
			}
		}
		$mtagnew['template'] = empty($mtagnew['template']) ? '' : stripslashes($mtagnew['template']);
		$mtagnew['disabled'] = @$iscopy || empty($mtag['disabled']) ? 0 : 1;
		$mtag = array(
		'cname' => stripslashes($mtagnew['cname']),
		'ename' => $mtagnew['ename'],
		'tclass' => $tclass,
		'template' => $mtagnew['template'],
		'setting' => $mtagnew['setting'],
		);
        $mtag['setting']['chids'] = empty($sclass) ? @$_sclass : $sclass; //�����ĵ�ģ��ID�ı���
		cls_CacheFile::Save($mtag,cls_cache::CacheKey($ttype,$mtagnew['ename']),$ttype);
		adminlog('������Ƭ���ݵ���ģ��');
		cls_message::show('��Ƭ���ݵ���ģ���������',axaction(6,"?entry=$entry&action=fragmentsedit"));
	}
}

function ename_arr(){
	global $db,$tblprefix;
	$re = array();
	$query = $db->query("SELECT ename FROM {$tblprefix}fragments");
	while($r = $db->fetch_array($query)) $re[] = $r['ename'];
	return $re;
}
function fetch_one($ename){
	global $db,$tblprefix;
	$r = $db->fetch_one("SELECT * FROM {$tblprefix}fragments WHERE ename='$ename'");
	return $r;
}


?>
