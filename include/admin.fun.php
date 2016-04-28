<?php
/*
* ���ű����ù����̨����Ա���ĻṲ�ã���ǰ̨ҳ�治��Ҫ�õĺ���
* �ֱ�ֻʹ���ڻ�Ա���Ļ�����̨�ĺ�����������admina.fun.php��adminm.fun.php��
*/
!defined('M_COM') && exit('No Permission');
function time_diff($t,$needsuffix = 0,$level= 2,$line = 0){
	global $timestamp;
	$line || $line = $timestamp;
	$diff = $t - $line;
	$suffix = $diff > 0 ? '��' : 'ǰ';
	$diff = abs($diff);
	$na = array(31536000 => '��',2592000 => 'M',86400 => '��',3600 => 'ʱ',60 => '��',);
	$str = '';$lv = 0;
	foreach($na as $k => $v){
		if($x = floor($diff / $k)){
			$str .= $x.$v;
			$diff = $diff % $k;
			$lv ++;
		}
		if($level && $lv >= $level) break;
	}
	$str || $str = '����';
	return $str.($needsuffix ? $suffix : '');
}
function cnodesfromcnc(&$cnconfig,$oldupdate = 0,$NodeMode = 0){
//$NodeMode���Ƿ��ֻ��ڵ�
//oldupdate:��ȫ�ڵ��ͬʱ���½ڵ����ã��������¶��ƽڵ�
	if($cnconfig['closed']) return false;
	$tid = $cnconfig['tid'];
	if(empty($cnconfig['isfunc'])){
		if(!($idsarr = cfgs2ids($cnconfig['configs']))) return false;
		$narr = array();$i = 0;$j = count($idsarr) - 1;
		foreach($idsarr as $k =>$ids){
			$kv = !$k ? 'caid' : 'ccid'.$k;
			if(!$i){
				foreach($ids as $id){
					if($i == $j) cls_node::AddOneCnode("$kv=$id",$tid,$oldupdate,$NodeMode);
					else $narr[] = "$kv=$id";
				}
			}else{
				$arr = array();
				foreach($narr as $v){
					foreach($ids as $id){
						if($i == $j) cls_node::AddOneCnode($v."&$kv=$id",$tid,$oldupdate,$NodeMode);
						else $arr[] = $v."&$kv=$id";
					}
				}
				$narr = $arr;
			}
			$i ++;
		}
		unset($narr,$arr,$idsarr,$ids);
		return true;
	}else{
		@include_once _08_EXTEND_LIBS_PATH.'functions'.DS.'custom.fun.php';
		if(empty($cnconfig['funcode'])) return false;
		return ($re = @eval($cnconfig['funcode'])) ? true : false;
	}
}
function mcnodesfromcnc($idcfg,$tid = 0){//�ֶ����ýڵ�
	if($idcfg['mcnvar'] == 'mcnid'){
		maddonecnode(array('mcnvar' => 'mcnid','alias' => $idcfg['alias'],),$tid);
	}else{
		foreach($idcfg['ids'] as $k) maddonecnode(array('mcnvar' => $idcfg['mcnvar'],'mcnid' => $k,),$tid);
	}
	return;
}
function maddonecnode($arr = array(),$tid = 0){
	global $db,$tblprefix;
	if(!$arr || empty($arr['mcnvar'])) return false;
	$sqlstr = "mcnvar='".$arr['mcnvar']."',tid='$tid'";
	if($arr['mcnvar'] == 'mcnid'){
		$arr['alias'] = empty($arr['alias']) ? '�Զ���ڵ�' : trim(strip_tags($arr['alias']));
		$db->query("INSERT INTO {$tblprefix}mcnodes SET alias='$arr[alias]',$sqlstr");
		if($cnid = $db->insert_id()) $db->query("UPDATE {$tblprefix}mcnodes SET mcnid='$cnid',ename='".$arr['mcnvar']."=$cnid' WHERE cnid=$cnid");
	}elseif(!$db->result_one("SELECT 1 FROM {$tblprefix}mcnodes WHERE ename='".$arr['mcnvar']."=$arr[mcnid]'")){
		$db->query("INSERT INTO {$tblprefix}mcnodes SET alias='".cls_node::mcnode_cname($arr['mcnvar'].'='.$arr['mcnid'])."',mcnid='$arr[mcnid]',ename='".$arr['mcnvar']."=$arr[mcnid]',$sqlstr");
	}
	return true;
}

//�˺����ں��������н��� NoBackFunc ȡ�������������Լ��ݾɰ档
function backallow($name){
	$curuser = cls_UserMain::CurUser();
	return $curuser->NoBackFunc($name) ? false : true;
}
function backnav($type,$menu,$cfg = array()){//�����б���������ʹ����չ����
	if(defined('M_MCENTER')){
		$cfg || $cfg = cls_cache::cacRead('mcnavurls',MC_ROOTDIR.'./func/',1);
		if(@count($cfg[$type]) > 1) url_nav($cfg[$type],$menu);
	}else{
		$cfg || $cfg = cls_cache::exRead('bknavurls',1);
		$cfg &&	url_nav($cfg[$type]['title'],$cfg[$type]['menus'],$menu,12);
	}
}
function saveconfig($cftype, $mconfigsnew2 = array()){
	global $mconfigsnew,$db,$tblprefix;
    if ($mconfigsnew2)
    {
        $mconfigsnew = $mconfigsnew2;
    }
	$tpl_mconfigs = cls_cache::Read('tpl_mconfigs');
	$tpl_fields = cls_cache::Read('tpl_fields');
	$tplvars = array('cmslogo','cmstitle','cmskeyword','cmsdescription','cms_icpno','bazscert','copyright','cms_statcode',);
	foreach($tpl_fields as $k => $v) $tplvars[] = "user_$k";
	foreach($mconfigsnew as $k => $v){
		if(in_array($k,$tplvars)){
			$tpl_mconfigs[$k] = stripslashes($v);
			$cachetpl = TRUE;
		}else{
			$db->query("REPLACE INTO {$tblprefix}mconfigs (varname,value,cftype) VALUES ('$k','$v','$cftype')");
			if(in_array($k,array('hosturl','cmsurl','enablestatic','virtualurl',))){//��Ժ������������⴦��
				global $$k;
				$$k = $v;
			}
		}
	}
	empty($cachetpl) || cls_CacheFile::Save($tpl_mconfigs,'tpl_mconfigs','tpl_mconfigs');
	cls_CacheFile::Update('mconfigs');//��Ҫ�ڴ˹����и���btags
}
function atm_delete($dbstr,$type = 'image'){
	cls_atm::atm_delete($dbstr, $type);
}
function view_checkurl($dbstr){
	// $dbstrΪ���ݿⴢ��ֵ��������url,���ں�̨��ʾͼƬ�б�,�����ļ�������ʱ��ʾnopic.gif
	global $ftp_url;
	$dbstr = str_replace(array('<!ftpurl />','<!cmsurl />'),'',$dbstr);//����֮ǰ��ftp��ʽ�Ĵ洢
	if(strstr($dbstr,":/")) return $dbstr;
	return cls_url::is_remote_atm($dbstr) ? ($ftp_url.$dbstr) : (is_file(M_ROOT.preg_replace('/(#\d*)*/','',$dbstr)) ? cls_url::view_url($dbstr) : 'images/common/nopic.gif');
}
function auto_insert_id($tbl = 'coclass'){//0���ش���״̬
	global $cms_idkeep,$db,$tblprefix,$coid;
	$cms_idkeep = empty($cms_idkeep) ? 0 : intval($cms_idkeep);
	$idvars = array(
		'channels' => 'chid','splitbls' => 'stid',
		'cotypes' => 'coid','catalogs' => 'caid','coclass' => 'ccid','abrels' => 'arid','acommus' => 'cuid','cnrels' => 'rid',
		'mchannels' => 'mchid','grouptypes' => 'gtid','usergroups' => 'ugid','currencys' => 'crid','permissions' => 'pmid',
		'frcatalogs' => 'frcaid',
		'amconfigs' => 'amcid','localfiles' => 'lfid','players' => 'plid','rprojects' => 'rpid','watermarks' => 'wmid','pagecaches' => 'pcid',
		'aurls' => 'auid','mctypes' => 'mctid','mtypes' => 'mtid','menus' => 'mnid','mmtypes' => 'mtid','mmenus' => 'mnid','usualurls' => 'uid',
		'cntpls' => '','mcntpls' => '','cnconfigs' => '','arc_tpls' => '','o_cntpls' => '','o_cnconfigs' => '','o_arc_tpls' => '',//ģ��config�е�����
		'fcatalogs' => '','fchannels' => '','pushareas' => '','pushtypes' => '',//ģ��config�е�����
		'freeinfos' => '','mtconfigs' => '','mcatalogs' => '',//ģ��mconfig�е�����
	);
	if(!isset($idvars[$tbl])) exit('insert_id_error');
	$idvar = $idvars[$tbl];
	$cfg = cls_cache::cacRead('idkeeps',_08_EXTEND_SYSCACHE_PATH,1);
	if($idvar){//���ݱ��id
		$dbtbl = ($tbl=='coclass') ? "coclass$coid" : $tbl; 
		if(!($min = empty($cfg[$tbl][0]) ? 0 : $cfg[$tbl][0]) || !($max = empty($cfg[$tbl][1]) ? 0 : $cfg[$tbl][1])){
			$maxid = $db->result_one("SELECT MAX($idvar) FROM {$tblprefix}$dbtbl");
		}else{
			$maxid = $cms_idkeep == 1 ? max($min,$db->result_one("SELECT MAX($idvar) FROM {$tblprefix}$dbtbl WHERE $idvar>$min AND $idvar<$max")) : max($max,$db->result_one("SELECT MAX($idvar) FROM {$tblprefix}$dbtbl"));
		}
	}else{//ģ�建���е�����id
		$$tbl = cls_cache::Read($tbl);
		$maxid = ($keys = array_keys($$tbl)) ? max($keys) : 0;
		if(($min = empty($cfg[$tbl][0]) ? 0 : $cfg[$tbl][0]) && ($max = empty($cfg[$tbl][1]) ? 0 : $cfg[$tbl][1])){
			if($cms_idkeep == 1){
				$maxid = 0;
				foreach($$tbl as $k => $v){
					if($k >= $min && $k <= $max && $k >= $maxid){
						$maxid = $k;
					}
				}
				$maxid || $maxid = $min - 1;
			}else $maxid = max($max,$maxid);
		}
	}
	return 1 + $maxid;
}
function autokeyword($str){
	global $a_split;
	if(empty($a_aplit)){
		include_once M_ROOT."include/splitword.cls.php";
		$a_split = new SplitWord();
	}
	if(!$str) return '';
	$str = preg_replace("/&#?\\w+;/", '', strip_tags($str));
	return str_replace(' ',',',$a_split->GetIndexText($a_split->SplitRMM($str),100));
}
function sizecount($filesize){
	if($filesize >= 1073741824){
		$filesize = round($filesize / 1073741824 * 100) / 100 . ' GB';
	}elseif($filesize >= 1048576){
		$filesize = round($filesize / 1048576 * 100) / 100 . ' MB';
	}elseif($filesize >= 1024){
		$filesize = round($filesize / 1024 * 100) / 100 . ' KB';
	}elseif($filesize) $filesize = $filesize . ' Bytes';
	return $filesize;
}
function umakeoption($sarr=array(),$selected=''){
	$optionstr = '';
	foreach($sarr as $k => $v) $optionstr .= isset($v['unsel']) ? "<optgroup label=\"$v[title]\" style=\"background-color :#E0ECF2;\"></optgroup>\n" : "<option value=\"$k\"".($k == $selected ? ' selected' : '').">$v[title]</option>\n";
	return $optionstr;
}
function umakeradio($varname,$arr=array(),$selectid='',$ppr=0){
	$str = '';
	$i = 0;
	foreach($arr as $k => $v){
		if(empty($v['unsel'])){
			$checked = $selectid == $k || (!$i && $selectid == '') ? 'checked' : '';
			$str .= "<input class=\"radio\" type=\"radio\" name=\"$varname\" id=\"_$varname$k\" value=\"$k\" $checked><label for=\"_$varname$k\">$v[title]</label>";
			$i ++;
			$str .= !$ppr || ($i % $ppr) ? '&nbsp;  &nbsp;' : '<br />';
		}
	}
	return $str;
}
function makeselect($varname,$options,$addstr = ''){
	//html���������style,��׼����,��һ���������á�
	if(strpos($addstr,'vertical-align')<=0) $addstr .= ' style="vertical-align: middle;"';
	return "<select id=\"$varname\" name=\"$varname\" $addstr>$options</select>";
}

function makeoption($arr, $key='', $default='') {
	$str = $default ? "<option value=\"\">$default</option>\n" : '';
	if(is_array($arr))
    {
        foreach($arr as $k => $v)
        {
            $str .= "<option value=\"$k\"";
            if( (is_array($key) && in_array($k, $key)) || ($k == $key && empty($k) == empty($key)) )
            {
                $str .= ' selected="selected"';
            }
            $str .= ">$v</option>\n";
        }
    }
	return $str;
}
function makeradio($varname,$arr=array(),$selectid='',$ppr=0,$onclick='',$cls = ''){
	$str = '';
	$i = 0;
	foreach($arr as $k => $v){
		$checked = $selectid == $k && empty($k) == empty($selectid) || (!$i && $selectid == '') ? ' checked' : '';
		$checked .= $onclick ? " onclick=\"$onclick\"" : '';
		$str .= "<label for=\"_$varname$k\"".($cls ? " class=\"$cls\"" : '')."><input class=\"radio\" type=\"radio\" name=\"$varname\" id=\"_$varname$k\" value=\"$k\"$checked>$v</label>";
		$i ++;
		$str .= !$ppr || ($i % $ppr) ? '&nbsp;  &nbsp;' : '<br />';
	}
	return $str;
}

function OneCheckBox($varname,$title = '',$value = 0,$chkedvalue = 1){
	$re = "<input type=\"hidden\" name=\"$varname\" value=\"\">\n"; //��ѡ�����Ϊ��,������!inset()
	$re .= "<input type=\"checkbox\" class=\"checkbox\" name=\"$varname\" value=\"$chkedvalue\"".($value == $chkedvalue ? ' checked' : '').">\n";
	if($title) $re .= "<label for=\"$varname\">$title</label>\n";
	return $re;
}

function OneInputText($varname,$value = '',$width = 20,$addstr = ''){
	if(!$varname) return $value;
	return "<input type=\"text\" size=\"$width\" id=\"$varname\" name=\"$varname\" value=\"".mhtmlspecialchars($value)."\"  $addstr/>\n";
}
function OneCalendar($varname,$value = '',$addstr = ''){
	return OneInputText($varname,$value,20,"class=\"Wdate\" onfocus=\"WdatePicker({readOnly:true})\"  $addstr");
}
function makecheckbox($varname,$sarr,$value=array(),$ppr=0,$pad=0,$cls = ''){//$pprÿ�е�Ԫ��
	$str = "<input type=\"hidden\" name=\"$varname\" value=\"\">\n"; //��ѡ�����Ϊ��,������!inset()
	$i = 0;
	foreach($sarr as $k => $v){
		$checked = in_array($k,$value) ? 'checked' : '';
		$str .= "<label for=\"_$varname$k\"".($cls ? " class=\"$cls\"" : '')."><input class=\"checkbox\" type=\"checkbox\" name=\"$varname\" id=\"_$varname$k\" value=\"$k\" $checked>$v</label>";
		$i++;
		$str .= ($ppr && !($i % $ppr)) || ($pad && $i == $pad) ?  '<br />' : '&nbsp;  &nbsp;';
	}
	return $str;
}
function multiselect($varname,$sarray,$value=array(),$width='50%'){
	$value = is_array($value)?$value:array();
	$selectstr = "<select name=\"$varname\" id=\"$varname\" size=\"5\" multiple=\"multiple\" style=\"width:".$width."\">\n";
	foreach($sarray as $k => $v) $selectstr .= "<option value=\"$k\"".(in_array($k,$value) ? ' selected' : '').">$v";
	$selectstr .= "</select>";
	return $selectstr;
}
function autoabstract($str){
	global $autoabstractlength;
	empty($autoabstractlength) && $autoabstractlength = 100;
	if(!$str) return '';
	$str = str_replace(chr(0xa1).chr(0xa1),' ',cls_string::HtmlClear($str));
	$str = preg_replace("/&([^;&]*)(;|&)/s",' ',$str);
	$str = preg_replace("/\s+/s",' ',$str);
	$str = preg_replace('/\[#.*?#\]/','',$str); //ȥ����ҳ���[#����1#]
	return cls_string::CutStr(trim($str),$autoabstractlength);
}
function cridsarr($cash=0){
	$currencys = cls_cache::Read('currencys');
	$narr = $cash ? array(0 => '�ֽ�') : array();
	foreach($currencys as $k => $v) $narr[$k] = $v['cname'];
	return $narr;
}
function pmidsarr($mode = 'aread',$addstr=''){
	$permissions = cls_cache::Read('permissions');
	$narr = array('0' => !$addstr ? '��ȫ����': $addstr);
	foreach($permissions as $k => $v) if(!empty($v[$mode]))  $narr[$k] = $v['cname'];
	return $narr;
}
function vcaidsarr(){
	$vcatalogs = cls_cache::Read('vcatalogs');
	$narr = array();
	foreach($vcatalogs as $k => $v) $narr[$v['caid']] = $v['title'];
	return $narr;
}
function stidsarr($noid=0){//$noid�Ƿ񸽴�id
	$splitbls = cls_cache::Read('splitbls');
	$narr = array();
	foreach($splitbls as $k => $v) $narr[$k] = $v['cname'].($noid ? '' : "($k)");
	return $narr;
}
function first_id($arr){//ȡ�û�������ĵ�һ����Ԫid
	foreach($arr as $k => $v) return $k;
	return 0;
}
function ugidsarr($gtid,$mchid=0,$noid = 0){
	$grouptypes = cls_cache::Read('grouptypes');
	$mchannels = cls_cache::Read('mchannels');
	$narr = array();
	if(empty($grouptypes[$gtid])) return $narr;
	$usergroups = cls_cache::Read('usergroups',$gtid);
	foreach($usergroups as $k => $v) if(!$mchid || in_array($mchid,explode(',',$v['mchids']))) $narr[$k] = $v['cname'].($noid ? '' : "($k)");
	return $narr;
}
function allow_op($mode = 'acheck'){//�����̨����������ѡ���ϵĽ�ɫ����ز���Ȩ��
	//a-��������(�ĵ��뽻��) f-���� m-��Ա
	//check-�������� del-ɾ��
	global $a_checks;
	if(!$a_checks) return false;
	return array_intersect($a_checks,array(-1,$mode)) ? true : false;
}

function form_str($fname='',$furl='',$fupload=0,$checksubmit=1,$newwin=0,$method='post'){
	global $infloat,$ajaxtarget,$handlekey,$_vFormInit;
	$ques = strpos($furl, '?') === false ? '?' : '&';
    
    # CSRF HASH
    $hash = cls_env::getHashValue();
    $hash_name = cls_env::_08_HASH;
	$_vFormInit = 1; //����ʼ�����(��֤�봦�ж�)
	return ($checksubmit ? "<script type=\"text/javascript\">var _08cms_validator = _08cms.validator('$fname');</script>" : '')
	."<form name=\"$fname\" id=\"$fname\" method=\"$method\"".(!$fupload ? "" : " enctype=\"multipart/form-data\"")." action=\"$furl".($infloat?"{$ques}infloat=$infloat&handlekey=$handlekey":'')."\"".($newwin ? " onsubmit=\"return ajaxform(this)\"" : '').">\n<input type=\"hidden\" name=\"$hash_name\" value=\"$hash\" />\n";
        
}
function trhidden($varname,$value){
	echo "<input type=\"hidden\" id=\"$varname\" name=\"$varname\" value=\"$value\">\n";
}
function makesubmitstr($varname,$notnull = 0,$mlimit = 0,$min = 0,$max = 0,$type = 'text',$regular = ''){
	if(!$notnull && !$mlimit && !$regular && !$min && !$max && $type != 'date') return '';
	$regular = str_replace('"', '&quot;', $regular);
	if(in_array($type,array('image','flash','media','file'))){
		$submitstr = " rule=\"adj\" must=\"$notnull\" exts=\"$exts\"";
	}elseif(in_array($type,array('images','flashs','medias','files'))){
		$submitstr = " rule=\"adjs\" must=\"$notnull\" exts=\"$exts\" min=\"$min\" max=\"$max\"";
	}elseif($type == 'htmltext'){
		$submitstr = " rule=\"html\" must=\"$notnull\" vid=\"$varname\" min=\"$min\" max=\"$max\"";
	}elseif($type == 'multitext'){
		$submitstr = " rule=\"text\" must=\"$notnull\" min=\"$min\" max=\"$max\"";
	}elseif($type == 'text'){
		$submitstr = " rule=\"text\" must=\"$notnull\" mode=\"$mlimit\" regx=\"$regular\" min=\"$min\" max=\"$max\"";
	}elseif($type == 'date'){
		$submitstr = " rule=\"date\" must=\"$notnull\" min=\"$min\" max=\"$max\"";
	}elseif($type == 'int'){
		$submitstr = " rule=\"int\" must=\"$notnull\" regx=\"$regular\" min=\"$min\" max=\"$max\"";
	}elseif($type == 'float'){
		$submitstr = " rule=\"float\" must=\"$notnull\" regx=\"$regular\" min=\"$min\" max=\"$max\"";
	}else{
		if(!$notnull)return '';
		$submitstr = " rule=\"must\" regx=\"$regular\" min=\"$min\" max=\"$max\"";
	}
	return $submitstr;
}
function tr_cns($trname, $varname,$arr = array(),$multiset = 0){
	if(!empty($arr['coid']) && !empty($arr['chid']) && !coid_in_chid($arr['coid'],$arr['chid'])) return;
	empty($arr['notblank']) || $trname = '<font color="red"> * </font>'.$trname;
	$str = !$multiset || empty($arr['max']) || empty($arr['coid']) ? '' : "<select id=\"mode_$varname\" name=\"mode_$varname\" style=\"vertical-align: middle;\">".makeoption(array(0 => '����',1 => '׷��',2 => '�Ƴ�',),1)."</select> &nbsp;";
	trbasic($trname,$varname,$str.cn_select($varname,$arr),'');
}
function cn_select($varname,$arr = array()){
	//array('value'=>0,'coid'=>0,'chid'=>0,'notblank'=>0,'addstr'=>'','framein'=>0,'ids'=>array(),'max'=>0,'notip'=>0,'hidden'=>0,'emode'=>0,'evarname'=>'','evalue'=>0,'guide'=>0)
	//addstr��Ϊ��ʱ���ַ���Ҳ����ʾ���ַ�
	//framein�����ṹ����ĿҲ��Ϊ��Ч��Ŀ
	//hidden��ʹ��������
	//max����ѡʱ���ѡ����
	//ids��ָ�������г���ָ��id������ӷ������Ŀ������Ŀ��
	//emode���Ƿ���Ҫ�г���������
	//notip������Ҫ�������漰����
	//guide����ʾ����Ϣ
	//viewp��0-����catahidden�����Ч��Ŀ��1-��Ҫpid���ϣ��������Ч��Ŀ����Ϊunsel,-1��ȫ�����Ч��Ŀ
	global $ca_vmode;
	$cotypes = cls_cache::Read('cotypes');
	$value = 0;$coid = 0;$chid = 0;$addstr='��ѡ��';$hidden = 0;$notblank = 0;$framein=0;$max=0;$notip=0;$emode=0;$evalue='';$evarname='';$ids=array();$guide='';$viewp=0;
	$vmode = empty($arr['coid']) ? $ca_vmode : @$cotypes[$arr['coid']]['vmode'];
	extract($arr, EXTR_OVERWRITE);
	if(!empty($ids)) $ids = is_numeric($ids) ? array($ids) : $ids; //ָ������Ŀ����ϵID
	if($max && !$vmode) $vmode = 2;
	if($hidden){
		$str = cls_catalog::cnstitle($value,$max,$coid ? cls_cache::Read('coclasses',$coid) : cls_cache::Read('catalogs'))."<input type=\"hidden\" name=\"$varname\" value=\"$value\">\n";
	}elseif(!$vmode){

		$dt_arr = array();
		$arr_mode0 = array();
		if(!empty($ids)){
			foreach($ids as $k) $arr_mode0[] = cls_catalog::uccidsarr($coid,$chid,$framein,0,$viewp,$k);
			foreach($arr_mode0 as $p){
				if($p && is_array($p))
		 			$dt_arr += $p;
			}
		}else{
			$dt_arr = cls_catalog::uccidsarr($coid,$chid,$framein,0,$viewp);
		}

		$str = "<select style=\"vertical-align: middle;\" name=\"$varname\"" . ($notblank ? ' rule="must"' : '') . ">".umakeoption(($addstr ? array('0' => array('title' => $addstr)) : array()) + $dt_arr,$value)."</select>";
		unset($dt_arr);
		unset($arr_mode0);
	}elseif($vmode == 1){
		$arr = cls_catalog::uccidsarr($coid,$chid,$framein,1,0);
		if(!empty($ids)) foreach($arr as $k=>$v) if(!in_array($k,$ids)) unset($arr[$k]);
		if(!$max){ //��ϵ:��ѡ��ť(radio):��Ҫ��һ��[��ѡ��]
			$str = umakeradio($varname,$arr,$value) . ($notblank ? "<input type=\"hidden\" rule=\"must\" vid=\"$varname\" />" : '');
			if(empty($value) && $notblank) $str = str_replace('checked>','>',$str); //��ѡ��,��һ����ҪĬ��Ϊѡ��״̬
		}else{
			$str = "<span onclick=\"boxTo()\"><input type=\"hidden\" name=\"$varname\" value=\"$value\" max=\"$max\"/>";
			$val = explode(',', $value);
			foreach($arr as $k => $v) empty($v['unsel']) && $str .= "<input type=\"checkbox\" id=\"$varname$k\" value=\"$k\"" . (in_array($k, $val) ? ' checked' : ''). "><label for=\"$varname$k\">$v[title]</label> &nbsp;";
			$str .= ($notblank ? "<input type=\"hidden\" rule=\"must\" vid=\"$varname\" />" : '').'</span>';
		}
	}elseif($vmode == 2){
		$arr_mode2 = array();
		$str = "<script>var data = [";
		if(!empty($ids)){
			foreach($ids as $k) $arr_mode2[] = cls_catalog::uccidsarr($coid,$chid,$framein,1,1,$k);
			$_tmp = array();
			foreach($arr_mode2 as $p){
				foreach($p as $k2=>$p2){
					$_tmp[$k2] = $p2;
				}
			}
			cls_catalog::uccidstop($_tmp);
			$cnt = 0;
			foreach($_tmp as $k=>$v){
				$str .= ($cnt ? ',' : '' )."[$k,$v[pid],'".addslashes($v['title'])."',".(empty($v['unsel']) ? 0 : 1) . ']';
				$cnt++;
			}
		}else {
			$arr_mode2 =  cls_catalog::uccidsarr($coid,$chid,$framein,1,1);
			cls_catalog::uccidstop($arr_mode2);
			$cnt = 0;
			foreach($arr_mode2 as $k=>$v){ 
				$str .= ($cnt ? ',' : '' )."[$k,$v[pid],'".addslashes($v['title'])."',".(empty($v['unsel']) ? 0 : 1) . ']';
				$cnt++;
			}
		}
		$str .= "];\n_08cms.fields.linkage('$varname', data, '$value',$max,$notip,'','','$addstr');</script>" . ($notblank ? "<input type=\"hidden\" rule=\"must\" vid=\"$varname\" max=\"$max\" />" : '');
		unset($arr_mode2,$_tmp);
	}else{
		$data = $coid ? "coid&coid=$coid" : 'caid';
		if(!empty($ids)) $data .= "&ids=".implode(',',$ids);
		$data .= "&chid=$chid&framein=$framein&charset=" . cls_env::getBaseIncConfigs('mcharset');
		$data = _08_Http_Request::uri2MVC($data, false);
		$str = "<span><script>_08cms.fields.linkage('$varname', 'action/$data', '$value',$max,$notip,'','','$addstr');</script></span>" . ($notblank ? "<input type=\"hidden\" rule=\"must\" vid=\"$varname\" max=\"$max\" />" : '');
	}
	$emode && $str .= ' &nbsp;��ֹ����'.($emode > 1 ? '<font color="red"> * </font>' : '')."<input type=\"text\" size=\"10\" id=\"$evarname\" name=\"$evarname\" value=\"$evalue\" class=\"Wdate\" onfocus=\"WdatePicker({readOnly:true})\" rule=\"date\"" . ($emode > 1 ? ' must="1"' : '') . ">\n";
	if($guide) $str .= "<div class=\"tips1\">$guide</div>";
	return $str;
}
/**
 *  �ĵ��Զ���ҳ
 *
 * @access    public
 * @param     string  $mybody  ����
 * @param     string  $spsize  ��ҳ��С
 * @param     string  $sptag  ��ҳ���
 * @return    string
 */
function SpBody($mybody, $spsize, $sptag){
	$mybody = preg_replace('/\[#.*?#\]/','',$mybody);//�Զ�ģʽ��ȥ�����еķ�ҳ
    if(strlen($mybody) < $spsize) return $mybody;
    $mybody = stripslashes($mybody);
    $bds = explode('<', $mybody);
    $npageBody = '';
    $istable = 0;
    $mybody = '';
    foreach($bds as $i=>$k){
        if($i==0){
			$npageBody .= $bds[$i];
			continue;
		}
        $bds[$i] = "<".$bds[$i];
        if(strlen($bds[$i])>6){
            $tname = substr($bds[$i],1,5);
            if(strtolower($tname)=='table'){
                $istable++;
            }else if(strtolower($tname)=='/tabl'){
                $istable--;
            }
            if($istable>0){
                $npageBody .= $bds[$i]; continue;
            }else{
                $npageBody .= $bds[$i];
            }
        }else{
            $npageBody .= $bds[$i];
        }
        if(strlen($npageBody)>$spsize){
            $mybody .= $npageBody.$sptag;
            $npageBody = '';
        }
    }
    if($npageBody!='') $mybody .= $npageBody;
	$mybody = $sptag.$mybody;
    return addslashes($mybody);
}
function aboutarchive($aids='',$usemode='archives'){#empty($aids)��ʾ���ģʽ��!empty($aids)��ʾ�༭ģʽ��$usemode=='archives'��ʾӦ�����ĵ���ӣ�$usemode == 'tagarchives'��ʾӦ�ñ�ǩ�ĵ���ӣ�$usemode='tagfarchives'��ʾӦ���ڸ�����ǩ��ӡ�
	global $db,$tblprefix,$chid;
	$chidarr = array(0=>'ѡ�����ģ��') + ($usemode == 'tagfarchives' ? cls_fchannel::fchidsarr() : cls_channel::chidsarr());
	if($aids){
		$relatedarr = array();$relatedstr = '';
		if($usemode == 'tagfarchives'){
			$query = $db->query("SELECT a.aid,a.subject FROM {$tblprefix}farchives a WHERE aid in($aids)");
			while($r = $db->fetch_array($query)){$relatedarr[$r['aid']] = $r['subject'];$relatedstr .= ','.$r['aid'];}
		}else{
			$q = $db->query("SELECT distinct chid FROM {$tblprefix}archives_sub WHERE aid in ($aids)");
			while($row = $db->fetch_array($q)){
				if($ntbl = atbl($row['chid'])){
					$query = $db->query("SELECT aid,subject FROM {$tblprefix}$ntbl where aid in ($aids)");
					while($r = $db->fetch_array($query)){
						$relatedarr[$r['aid']] = $r['subject'];
						$relatedstr .= ','.$r['aid'];
					}
				}
			}
		}
		$relatedstr = substr($relatedstr,1);
	}
	$usemode == 'archives' && trbasic('�����Ϣ','',makeradio('autorelated',array(1=>'�Զ�',0=>'�ֶ�'),(!empty($relatedstr) ? 0 : 1 )),'');
	echo '<tr id="related" '.(!empty($relatedstr) || $usemode != 'archives' ? '' : 'style="display:none;"').'><td colspan="2">
	�ؼ��֣�<input type="text" size="30" name="RelativeKey" id="RelativeKey">&nbsp;&nbsp;<label><input name="RelativeTypeSubject" id="RelativeTypeSubject" type="checkbox" value="1" checked="checked">����</label>&nbsp;&nbsp;<label><input name="RelativeTypeKey" type="checkbox" value="2" id="RelativeTypeKey" checked="checked">�ؼ���Tag</label>&nbsp;&nbsp;<select name="relatedchid" id="relatedchid">'.makeoption($chidarr,$chid).'</select>&nbsp;&nbsp;<input type="button" name="relativeButton" id="relativeButton" value="���������Ϣ">
	<div class="blank12"></div>
	
	<table border="0" width="100%">
	<tbody><tr><td>��ѡ��Ϣ<br><select style="width: 240px; height: 250px;" multiple="" name="TempInfoList" id="TempInfoList"></select></td>
	<td><input type="button" class="button" id="RAddButton" value=" ���ѡ�� &gt;  "><br><br><input type="button" class="button" id="RAddMoreButton" value=" ȫ����� &gt;&gt; "><br><br><input type="button" class="button" id="RDelButton" value=" &lt; ɾ��ѡ��  "><br><br><input type="button" class="button" id="RDelMoreButton" value=" &lt;&lt; ȫ��ɾ�� "></td>          <td>ѡ����Ϣ<br><select style="width: 240px; height: 250px;" multiple="" name="SelectInfoList" id="SelectInfoList">'.(!empty($relatedarr) ? makeoption($relatedarr) : '').'</select></td></tr></tbody></table>
	</td></tr>';
	echo '<script type="text/javascript">var relatedaid = document.getElementById("'.($usemode == 'archives' ? 'relatedaid' : 'mtagnew[setting][ids]').'"),aids = "'.$aids.'";</script>';
	echo '<script type="text/javascript" src="include/js/aboutarchive.js"></script>';
}
function editColor($color=''){
	if($color){
		echo "<script type=\"text/javascript\">try{ColorSel('".$color."');}catch(eColor){}</script>\n";
	}
}

