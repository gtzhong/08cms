<?
(!defined('M_COM') || !defined('M_ADMIN')) && exit('No Permission');
aheader();
if($re = $curuser->NoBackFunc('mcconfig')) cls_message::show($re);

$page = isset($page) ? $page : 1;
$oflag = empty($oflag) ? 'cname' : $oflag;
$page = !empty($page) ? max(1, intval($page)) : 1;
submitcheck('bfilter') && $page = 1;
empty($keyword) && $keyword = '';
//$atpp = 2;

$fromsql = "FROM {$tblprefix}mguides ";
$wheresql = 'WHERE 1=1 ';
$keyword && $wheresql .= " AND (ename ".sqlkw($keyword)." OR cname ".sqlkw($keyword).")";

$filterstr = '';
foreach(array('keyword',) as $k) $$k && $filterstr .= "&$k=".rawurlencode(stripslashes($$k));
$action = empty($action) ? 'mglist' : $action;

if($action=='mglist'){
	backnav('mcenter','mguides');
	if(!submitcheck('bmlistdo')){
		
	  echo form_str($actionid.'arcsedit',"?entry=$entry&page=$page");
	  //trhidden('caid',$caid);
	  tabheader_e();
	  echo "<tr><td colspan=\"2\" class=\"txt txtleft\">";
	  echo "<input class=\"text\" name=\"keyword\" type=\"text\" value=\"$keyword\" size=\"24\" style=\"vertical-align: middle;\" title=\"�������������\">&nbsp; ";
	  echo strbutton('bfilter','ɸѡ');
	  tabfooter();
  
	  //�б���
	  tabheader("ע���б� ������<a href='?entry=mguides&action=mgadd' onclick=\"return floatwin('open_add',this)\">���ע��&gt;&gt;</a>",'','',9);
  
	  $cy_arr = array("<input class=\"checkbox\" type=\"checkbox\" name=\"chkall\" onclick=\"checkall(this.form, 'selectid', 'chkall')\">",);
	  $cy_arr[] = 'ע��ID';
	  $cy_arr[] = 'ע������';
	  $cy_arr[] = 'λ����ʾ';
	 
	  $cy_arr[] = '�������';
	  $cy_arr[] = '��������';
	  $cy_arr[] = '������'; 
	  $cy_arr[] = '�༭';
	  trcategory($cy_arr);
  
	  $pagetmp = $page;
	  do{
		  $query = $db->query("SELECT * $fromsql $wheresql ORDER BY mgid DESC LIMIT ".(($pagetmp - 1) * $atpp).",$atpp");
		  $pagetmp--;
	  } while(!$db->num_rows($query) && $pagetmp);
  
	  $itemstr = '';
	  while($r = $db->fetch_array($query)){
		  $selectstr = "<input class=\"checkbox\" type=\"checkbox\" name=\"selectid[$r[mgid]]\" value=\"$r[mgid]\">";
		  $cname = mhtmlspecialchars($r['cname']);
		  $posmsg = mhtmlspecialchars($r['posmsg']);
		  $addstr = date('Y-m-d',$r['createdate']);
		  $updstr = empty($r['updatedate']) ? '-' : date('Y-m-d',$r['updatedate']);
		  $itemstr .= "<tr class=\"txt\"><td class=\"txtC w40\" >$selectstr</td>";
		  $itemstr .= "<td class=\"txtC\">$r[ename]</td>\n";
		  $itemstr .= "<td class=\"txtL\"><input type=\"text\" size=\"30\" name=\"fmg[$r[mgid]][cname]\" value=\"$cname\"></td>\n";
		  $itemstr .= "<td class=\"txtC\"><input type=\"text\" size=\"50\" name=\"fmg[$r[mgid]][posmsg]\" value=\"$posmsg\"></td>\n";
		  
		  $itemstr .= "<td class=\"txtC\">$addstr</td>\n";
		  $itemstr .= "<td class=\"txtC\">$updstr</td>\n";
		  $itemstr .= "<td class=\"txtC\">$r[mname]</td>\n";
		  $itemstr .= "<td class=\"txtC\"><a href='?entry=mguides&action=mgedit&mgid=$r[mgid]' onclick=\"return floatwin('open_edit',this)\">".'�޸�'."</a></td>\n";
		  trhidden("fmg[$r[mgid]][ename]",$r['ename']);
		  $itemstr .= "</tr>\n";
	  }
  
	  $counts = $db->result_one("SELECT count(*) $fromsql $wheresql");
	  $multi = multi($counts, $atpp, $page, "?entry=$entry$filterstr");
	  echo $itemstr;
	  tabfooter();
	  echo $multi;
  
	  //������
	  tabheader('������Ŀ');
	  $s_arr = array();
	  $s_arr['delete'] = 'ɾ����ѡ';
	  $s_arr['update'] = '�����б�';
	  //$s_arr['ucache'] = '���»���';
	  if($s_arr){
		  $soperatestr = '';
		  foreach($s_arr as $k => $v){
			  $soperatestr .= "<input class=\"checkbox\" type=\"checkbox\" name=\"arcdeal[$k]\" value=\"1\"".($k=='delete'?' onclick="deltip()"':'').">$v &nbsp;";
		  }
		  //trhidden('arcdeal[update]','');
		  trbasic('ѡ�������Ŀ','',$soperatestr,'');
	  }
  
	  tabfooter('bmlistdo');
	  a_guide('archivesedit');

	}else{
		
		if(empty($arcdeal)) cls_message::show('��ѡ�������Ŀ',axaction(1,M_REFERER)); 
		if(empty($selectid)) cls_message::show('��ѡ��ע����Ŀ',axaction(1,M_REFERER)); 
		$file = _08_FilesystemFile::getInstance();
		foreach($selectid as $mgid){
			if(!empty($arcdeal['delete'])){
				$db->query("DELETE FROM {$tblprefix}mguides WHERE mgid='$mgid'");
				$file->delFile(M_ROOT."dynamic/mguides/".$fmg[$mgid]['ename'].".php"); //unset($mguidesnew[$mgid]);
				continue;
			}
			if(!empty($arcdeal['update'])){
				$db->query("UPDATE {$tblprefix}mguides SET 
							cname='".$fmg[$mgid]['cname']."',
							posmsg='".$fmg[$mgid]['posmsg']."',
							updatedate='$timestamp' 
							WHERE mgid='$mgid'");
			}
		}

		updatethiscache();
		cls_message::show('ע�Ͳ����ɹ�!',"?entry=$entry$filterstr&page=$page");
		
	}

}elseif($action == 'mgadd'){
		
	echo "<title>���ע��</title>";
	if(!submitcheck('bsubmit')){
		$submitstr = '';
		tabheader('���ע��',$action,"?entry=$entry&action=$action",2,0,1);
		trbasic('ע��ID','fmdata[ename]','','text',array('validate'=>makesubmitstr('fmdata[cname]',1,0,3,30)));
		trbasic('ע������','fmdata[cname]','','text',array('validate'=>makesubmitstr('fmdata[cname]',1,0,3,30)));
		trbasic('λ����ʾ','fmdata[posmsg]','','text',array('validate'=>makesubmitstr('fmdata[posmsg]',1,0,3,100)));
		trbasic('ע������','',"<textarea rows=\"8\" name=\"fmdata[content]\" style='width:100%;'></textarea>",'');
		tabfooter('bsubmit','���');
		a_guide('mguide_ae');
	}else{
		!($fmdata['ename'] = trim(strip_tags($fmdata['ename']))) && cls_message::show('������ע��ID');
		!($fmdata['cname'] = trim(strip_tags($fmdata['cname']))) && cls_message::show('������ע������');
		if(!preg_match("/[a-zA-Z][a-z_A-Z0-9]{2,31}/",$fmdata['ename'])){
			cls_message::show('������Ϸ���Ӣ�ı�ʶ!(3~32����ĸ���ֺ��»������,��ĸ��ͷ)', "?entry=$entry&action=$action");
		}
		if($db->result_one("SELECT COUNT(*) FROM {$tblprefix}mguides WHERE ename='$fmdata[ename]'")){
			cls_message::show('�����Ӣ�ı�ʶ�Ѵ���!', "?entry=$entry&action=$action");
		}
		$fmdata['content'] = trim($fmdata['content']);
		$db->query("INSERT INTO {$tblprefix}mguides SET 
					cname='$fmdata[cname]',
					posmsg='$fmdata[posmsg]',
					ename='$fmdata[ename]',
					content='$fmdata[content]',
					createdate='$timestamp',
					mid='$memberid',
					mname='".$curuser->info['mname']."'
					");
		updatethiscache();
		cls_message::show('ע����ӳɹ�',axaction(6,M_REFERER));
	}

}elseif($action == 'mgedit' && $mgid){

	if(!($mguide = $db->fetch_one("SELECT * FROM {$tblprefix}mguides WHERE mgid='$mgid'"))){
		cls_message::show('��ָ����ȷ��ע��!',"?entry=$entry&action=$action");
	}
	
	echo "<title>�޸�ע��</title>";
	if(!submitcheck('bsubmit')){
		$submitstr = '';
		tabheader('�޸�ע��',$action,"?entry=$entry&action=$action",2,0,1);
		trbasic('ע��ID','',$mguide['ename'],'');
		trbasic('ע������','fmdata[cname]',$mguide['cname'],'text',array('validate'=>makesubmitstr('fmdata[cname]',1,0,3,30)));
		trbasic('λ����ʾ','fmdata[posmsg]',$mguide['posmsg'],'text',array('validate'=>makesubmitstr('fmdata[posmsg]',1,0,3,60)));
		trbasic('ע������','',"<textarea rows=\"8\" name=\"fmdata[content]\" style='width:100%;'>$mguide[content]</textarea>",'');
		trhidden('mgid',$mgid);
		tabfooter('bsubmit','�޸�');
		a_guide('mguide_ae');
	}else{

		!($fmdata['cname'] = trim(strip_tags($fmdata['cname']))) && cls_message::show('������ע������');

		$fmdata['cname']   = trim($fmdata['cname']) ? trim($fmdata['cname']) : $mguide['cname'];
		$fmdata['content'] = trim($fmdata['content']); //? trim($fmdata['content']) : $mguide['content'];
		$db->query("UPDATE {$tblprefix}mguides SET 
					cname='$fmdata[cname]',
					posmsg='$fmdata[posmsg]',
					content='$fmdata[content]',
					updatedate='$timestamp' 
					WHERE mgid='$mgid'");
		updatethiscache();
		cls_message::show('ע���޸ĳɹ�',axaction(6,M_REFERER));

	}

}
function updatethiscache(){
	global $db,$tblprefix;
	clear_files(M_ROOT.'dynamic/mguides/');
	$query = $db->query("SELECT * FROM {$tblprefix}mguides ORDER BY mgid");
	while($mguide = $db->fetch_array($query)){
		if(@$fp = fopen(M_ROOT.'dynamic/mguides/'.$mguide['ename'].'.php','wb')){
			fwrite($fp,"<?php\n\$mguide = '".addcslashes($mguide['content'],'\'\\')."';\n?>");
			fclose($fp);
		}
	}
}
function clear_files($dir,$keeps = array('index.html','index.htm')){
	if(!is_dir($dir)) return;
	$handle = dir($dir);
    $file = _08_FilesystemFile::getInstance();
	while($entry = $handle->read()){
		if($entry != '.' && $entry != '..' && is_file($dir.'/'.$entry)){
			if(!$keeps || !in_array($entry,$keeps)) 
            {
                $file->delFile($dir.'/'.$entry);
            }
		}
	}
	$handle->close();
}

?>