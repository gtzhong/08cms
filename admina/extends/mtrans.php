<?
(!defined('M_COM') || !defined('M_ADMIN')) && exit('No Permission');
aheader();
backallow('member') || cls_message::show('��û�е�ǰ��Ŀ�Ĺ���Ȩ�ޡ�');

$mchannels = cls_cache::Read('mchannels');
$channels = cls_cache::Read('channels');
$currencys = cls_cache::Read('currencys');
$mctypes = cls_cache::Read('mctypes');

empty($action) && $action = 'mtransedit'; //print_r($mchannels);
if($action == 'mtransedit'){
	$page = !empty($page) ? max(1, intval($page)) : 1;
	submitcheck('bfilter') && $page = 1;
	$checked = isset($checked) ? $checked : '-1';
	$keyword = empty($keyword) ? '' : $keyword;

	$wheresql = '';
	$checked != '-1' && $wheresql .= ($wheresql ? " AND " : "")."checked='$checked'";
	$keyword && $wheresql .= ($wheresql ? " AND " : "")."mname LIKE '%".str_replace(array(' ','*'),'%',addcslashes($keyword,'%_'))."%'";

	$filterstr = '';
	foreach(array('keyword',) as $k) $$k && $filterstr .= "&$k=".rawurlencode(stripslashes($$k));
	foreach(array('checked',) as $k) $$k != -1 && $filterstr .= "&$k=".$$k;
	$wheresql = $wheresql ? "WHERE ".$wheresql : "";
	if(!submitcheck('bsubmit')){
		echo form_str($actionid.'utransedit',"?entry=$entry$extend_str&action=$action&page=$page");
		tabheader_e();
		echo "<tr><td colspan=\"2\" class=\"txt txtleft\">";
		echo "<input class=\"text\" name=\"keyword\" type=\"text\" value=\"$keyword\" size=\"8\" style=\"vertical-align: middle;\" title=\"�����û���\">&nbsp; ";
		echo "<select style=\"vertical-align: middle;\" name=\"checked\">".makeoption(array('-1' => '���״̬','0' => 'δ��','1' => '����'),$checked)."</select>&nbsp; ";
		echo strbutton('bfilter','ɸѡ');
		echo "</td></tr>";
		tabfooter();

		tabheader('������Ա ���� �̼�','','',8);
		$cy_arr = array("<input class=\"checkbox\" type=\"checkbox\" name=\"chkall\" onclick=\"checkall(this.form,'selectid','chkall')\">",'��Ա�ʺ�|L',);
		$cy_arr[] = "����/��˾��";
		$cy_arr[] = "��������";
		$cy_arr[] = "���";
		$cy_arr[] = '��������ʱ��';
		$cy_arr[] = '����';
		trcategory($cy_arr);
		
		$pagetmp = $page;	
		do{
			$query = $db->query("SELECT * FROM {$tblprefix}mtrans $wheresql ORDER BY trid DESC LIMIT ".(($pagetmp - 1) * $atpp).",$atpp");
			$pagetmp--;
		} while(!$db->num_rows($query) && $pagetmp);
		$itemstr = '';
		while($row = $db->fetch_array($query)){
			$selectstr = "<input class=\"checkbox\" type=\"checkbox\" name=\"selectid[$row[trid]]\" value=\"$row[trid]\">";
			$createdatestr = date("Y-m-d H:i", $row['createdate']);
			$arr = unserialize($row['contentarr']);
			$arrname = isset($arr['xingming'])?$arr['xingming']:$arr['companynm']; //xingming,companynm			
			$arrtype = "($row[toid])".$mchannels[$row['toid']]['cname'];
			$checkstr = $row['checked'] ? 'Y' : '-';
			$detailstr = $row['checked'] ? '-' : "<a href=\"?entry=$entry$extend_str&action=mtrandetail&trid=$row[trid]\" onclick=\"return floatwin('open_transdetail',this)\">����</a>";
			$itemstr .= "<tr class=\"txt\">\n".
			"<td class=\"txtC w40\">$selectstr</td>\n".
			"<td class=\"txtL\">$row[mname]</td>\n".
			"<td class=\"txt\">$arrname</td>\n".			
			"<td class=\"txt\">$arrtype</td>\n".
			"<td class=\"txtC w40\">$checkstr</td>\n".
			"<td class=\"txtC w150\">$createdatestr</td>\n".
			"<td class=\"txtC w40\">$detailstr</td>\n".
			"</tr>\n";
		}
		$counts = $db->result_one("SELECT count(*) FROM {$tblprefix}mtrans $wheresql");
		$multi = multi($counts,$atpp,$page,"?entry=$entry$extend_str&action=$action$filterstr");

		echo $itemstr;
		tabfooter();
		echo $multi;
		
		tabheader('������Ŀ');
		$s_arr = array();
		$s_arr['delete'] = 'ɾ��';
		$s_arr['check'] = '���';
		if($s_arr){
			$soperatestr = '';
			$i = 1;
			foreach($s_arr as $k => $v){
				$soperatestr .= "<input class=\"checkbox\" type=\"checkbox\" id=\"arcdeal[$k]\" name=\"arcdeal[$k]\" value=\"1\"" . ($k == 'delete' ? ' onclick="deltip()"' : '') . "><label for=\"arcdeal[$k]\">$v</label> &nbsp;";
				if(!($i % 5)) $soperatestr .= '<br>';
				$i ++;
			}
			trbasic('ѡ�������Ŀ','',$soperatestr,'');
		}
		tabfooter('bsubmit');
	}else{
		if(empty($arcdeal)) cls_message::show('��ѡ�������Ŀ',"?entry=$entry$extend_str&action=$action&page=$page$filterstr");
		if(empty($selectid)) cls_message::show('��ѡ���Ա',"?entry=$entry$extend_str&action=$action&page=$page$filterstr");
		
		if(!empty($arcdeal['delete'])){
			$db->query("DELETE FROM {$tblprefix}mtrans WHERE trid ".multi_str($selectid));
		}elseif(!empty($arcdeal['check'])){
			$actuser = new cls_userinfo;
			$a_field = new cls_field;
			$c_upload = new cls_upload;
			foreach($selectid as $trid){
				if($row = $db->fetch_one("SELECT * FROM {$tblprefix}mtrans WHERE trid='$trid' AND checked='0'")){
					$contentarr = empty($row['contentarr']) ? array() : unserialize($row['contentarr']);
					unset($row['contentarr']);
					
					//ѭ���������֤��ִ�յ���֤��˿�ʼ
					$au = new cls_userinfo;
					$au->activeuser($row['mid']);
					$mchid = $au->info['mchid'];
					$mfields = cls_cache::Read('mfields',$mchid);
					$mcfield = &$mfields[$mctype['field']];
				
					foreach($contentarr as $tplx=>$tplj){
						if($tplx == 'sfz' || $tplx == 'jjrzz'){
							$mctid = $tplx == 'sfz'?'2':'3';
							$a_field->init($mcfield,$tplj);
							$content = $a_field->deal('fmdata','amessage',M_REFERER);
							$au->updatefield($tplx,$tplj,$a_field->field['tbl']);
							$au->updatefield("mctid$mctid",$mctid);
							$au->updatedb();
							$db->query("INSERT INTO {$tblprefix}mcerts SET mid='$row[mid]',mname='$row[mname]',mctid='$mctid',createdate='$timestamp',checkdate='$timestamp',content='$tplj'");
						}
					}//ѭ���������֤��ִ�յ���֤��˽���
					
					$actuser->activeuser($row['mid'],1);
					$ochid = $row['fromid'];
					$mchid = $row['toid'];
					$mchannel = $mchannels[$mchid];
					$mfields = cls_cache::Read('mfields',$mchid);
					
					$db->query("DELETE FROM {$tblprefix}members_$ochid WHERE mid='$row[mid]'");
					$db->query("INSERT INTO {$tblprefix}members_$mchid SET mid='$row[mid]'",'SILENT');
					$actuser->updatefield('mchid',$mchid);
					
					if($mchid == 2){//�������ͨ��Ա����Ϊ�����ˣ�����ַ������ⷿԴ�ж�Ӧ��mid��mchid�ֶ�ҲҪ�޸ĳ�2
						$db->query("UPDATE {$tblprefix}".atbl(2)." SET mchid = '2' WHERE mid = '$row[mid]'");
						$db->query("UPDATE {$tblprefix}".atbl(3)." SET mchid = '2' WHERE mid = '$row[mid]'");
					}
					
					foreach($mfields as $k => $v){
						if(!$v['issystem'] && !empty($contentarr[$k])){
							$a_field->init($v);
							$contentarr[$k] = $a_field->deal('contentarr','');
							if(!$a_field->error){
								$actuser->updatefield($k,$contentarr[$k],$v['tbl']);
								if($arr = multi_val_arr($contentarr[$k],$v)) foreach($arr as $x => $y) $actuser->updatefield($k.'_'.$x,$y,$v['tbl']);
							}
						}
					}
					$crids = array();foreach($currencys as $k => $v) $v['available'] && $v['initial'] && $crids[$k] = $v['initial'];
					$crids && $actuser->updatecrids($crids,0,'��Աע���ʼ���֡�');
					$actuser->updatefield('checked',1);
					$actuser->nogroupbymchid();//ģ�ͱ���Ժ�������Ҫ���鶨��
					$actuser->groupinit();
					$actuser->updatefield('mtcid',($mtcid = array_shift(array_keys($actuser->mtcidsarr()))) ? $mtcid : 0);
					$actuser->autoletter();
					$actuser->updatedb();
					$db->query("UPDATE {$tblprefix}mtrans SET contentarr='',remark='',reply='',checked='1' WHERE trid='$trid'");
				}
			}
			
			$c_upload->closure(1,$memberid,'members');
			$c_upload->saveuptotal(1);
			unset($c_upload);
			unset($a_field);
			unset($actuser);
		}
		adminlog('��Ա�����̼ҹ���','�б����');
		cls_message::show('�����������',"?entry=$entry$extend_str&action=$action&page=$page$filterstr");
	
	}
}elseif($action == 'mtrandetail' && $trid){
	if(!$row = $db->fetch_one("SELECT * FROM {$tblprefix}mtrans WHERE trid='$trid' AND checked='0'")) cls_message::show('��ѡ�������¼');
	$contentarr = empty($row['contentarr']) ? array() : unserialize($row['contentarr']);
	unset($row['contentarr']);
	
	$ochid = $row['fromid'];
	$mchid = $row['toid'];  //$mchannels[$row['toid']]['cname']
	$mchannel = $mchannels[$mchid]['cname'];  //echo $mchannel;
	$mfields = cls_cache::Read('mfields',$mchid);
	
	// �ʴ�ר��-�����ֶ�
	$mfexp = array('dantu','ming','danwei','quaere');
	foreach($mfexp as $k){//��̨�ܹ��ֶ�
		unset($mfields[$k]);
	}
	// �ų���Ա��֤�ֶ�
	foreach($mctypes as $k => $v){
		if(strstr(",$v[mchids],",",$mchid,")){ //����Ļ�Աģ��
			unset($mfields[$v['field']]);
		}
	}
	
	if(!submitcheck('bsubmit')){
		tabheader("$row[mname] ����Ϊ $mchannel",$actionid,"?entry=$entry$extend_str&action=mtrandetail&trid=$trid",2,1,1);
		trbasic('����ʱ��','',date("Y-m-d H:m",$row['createdate'] ? $row['createdate'] : $timestamp),'');
		trbasic('����˵��','',empty($row['remark']) ? '' : $row['remark'],'textarea',array('guide' => '���ɸ���'));
		trbasic('����Ա�ظ�','fmdata[reply]',empty($row['reply']) ? '' : $row['reply'],'textarea');
		tabfooter();
		
		tabheader('��ϸ����');
		$a_field = new cls_field;
		function mainpro(){
		}
		foreach($mfields as $k => $field){
			if(!$field['issystem']){
				empty($contentarr[$k]) || $contentarr[$k] = stripslashes($contentarr[$k]);
				$a_field->init($field,empty($contentarr[$k]) ? '' : $contentarr[$k]);
				$a_field->trfield('fddata');
			}
		}
		mainpro();
		tabfooter('bsubmit');
	}else{
		$c_upload = new cls_upload;	
		$a_field = new cls_field;
		foreach($mfields as $k => $v){
			if(!$v['issystem'] && isset($fddata[$k])){
				empty($contentarr[$k]) || $contentarr[$k] = stripslashes($contentarr[$k]);
				$a_field->init($v,empty($contentarr[$k]) ? '' : $contentarr[$k]);
				$fddata[$k] = $a_field->deal('fddata','amessage',M_REFERER);
			}
		}
		unset($a_field);
		
		$fmdata['reply'] = trim($fmdata['reply']);
		$fddata = empty($fddata) ? '' : addslashes(serialize($fddata));
		$db->query("UPDATE {$tblprefix}mtrans SET contentarr='$fddata',reply='$fmdata[reply]' WHERE trid='$trid'");
		$c_upload->closure(1,$memberid,'members');
		$c_upload->saveuptotal(1);
		adminlog('�༭��Ա��������');
		cls_message::show('�����¼�༭���',axaction(6,M_REFERER));
	}
}
?>
