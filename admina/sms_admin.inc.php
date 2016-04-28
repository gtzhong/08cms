<?php
(!defined('M_COM') || !defined('M_ADMIN')) && exit('No Permission');
aheader();
if($re = $curuser->NoBackFunc('smsapi')) cls_message::show($re);
$mchannels = cls_cache::Read('mchannels');
$grouptypes = cls_cache::Read('grouptypes');
$sms = new cls_sms(); //echo $sms->check_ipmax();
$sms_cfg_aset = $sms->cfga;

$defact = 'sendlog';
$action = empty($action) ? $defact : $action;
$page = !empty($page) ? max(1, intval($page)) : 1; 
$checked = empty($checked) ? 0 : $checked; 
$fclose = false;
$ermsg = ''; //������Ϣ
if($sms->isClosed()){
	$balance = array(-1,0);
	if($action!='setapi'){
		$fclose = true;
	}
	$sms_cfg_api = '(close)';
}else{
	$balance = $sms->getBalance();
	if(($balance[1]<=0) || ($sms->balanceWarn(5))){
		$defact = 'apiwarn';	
	}
	if(!empty($balance['msg'])) $ermsg = '('.$balance['msg'].')';
}
 
backnav('sms_admin',$action);
if($fclose) cls_message::show('���ȿ���[�ֻ����Žӿ�]����������->�ֻ�����->�ӿ�����');

if($action=='sendlog'){

  $keyword = empty($keyword) ? '' : $keyword; 
  $keytype = empty($keytype) ? 'mname' : $keytype;
  $filterstr = $checked?"&checked=$checked":'';
  foreach(array('keyword','keytype') as $k) $$k && $filterstr .= "&$k=".rawurlencode(stripslashes($$k));

  $selectsql = "SELECT * ";
  $wheresql = " WHERE 1=1 "; //cu.mid='$memberid' commu_offer. archives1.
  $fromsql = "FROM {$tblprefix}sms_sendlogs ";
    
  if($keyword){
	  if($keytype){
	  	$wheresql .= " AND ($keytype ".sqlkw($keyword).") ";
	  }else{
	  	$wheresql .= " AND (tel ".sqlkw($keyword)." OR msg ".sqlkw($keyword).") ";
	  }
  }
  
  if(!submitcheck('bsubmit')){
	  
	  echo form_str('sendlogs',"?entry=$entry$filterstr&page=$page");
	  tabheader_e();
	  echo "<tr><td class=\"txt txtleft\">";
	  echo "<input class=\"text\" name=\"keyword\" type=\"text\" value=\"$keyword\" size=\"8\" style=\"vertical-align: middle;\" title=\"�ؼ���\">&nbsp; ";
	  echo "<select style=\"vertical-align: middle;\" name=\"keytype\">".makeoption(array('0'=>'--ɸѡ��Χ--','mname'=>'��Ա��','tel'=>'�绰','msg'=>'����','ip'=>'IP'),$keytype)."</select>&nbsp; ";
	  echo strbutton('bfilter','ɸѡ');
	  tabfooter();
	  tabheader("���ŷ��ͼ�¼",'','',10);
	  $cy_arr = array("<input class=\"checkbox\" type=\"checkbox\" name=\"chkall\" onclick=\"checkall(this.form, 'selectid', 'chkall')\">");
	  //$cy_arr[] = '����ʱ��';
	  $cy_arr[] = '���͵绰|L';
	  $cy_arr[] = '��Ϣ����|L';
	  $cy_arr[] = '��Ա';
	  $cy_arr[] = '����ip/ʱ��';
	  $cy_arr[] = '���';
	  $cy_arr[] = '�ӿ���Ϣ';
	  
	  trcategory($cy_arr);
  
	  $pagetmp = $page; //echo "$selectsql $fromsql $wheresql";
	  do{
		  $query = $db->query("$selectsql $fromsql $wheresql ORDER BY cid DESC LIMIT ".(($pagetmp - 1) * $mrowpp).",$mrowpp");
		  $pagetmp--;
	  } while(!$db->num_rows($query) && $pagetmp);
  
	  $itemstr = ''; $stype = array('sadm'=>'����Ա��','scom'=>'��Ա����','ctel'=>'������֤',);
	  while($r = $db->fetch_array($query)){
		  $selectstr = "<input class=\"checkbox\" type=\"checkbox\" name=\"selectid[$r[cid]]\" value=\"$r[cid]\">";
		  $time = date('Y-m-d H:i',$r['stamp']);
		  
		  $tel = str_replace(',',', ',$r['tel']);
		  if(strlen($tel)>72) $tel = substr($tel,0,64).'...'.substr($tel,strlen($tel)-15);
		  $msg = mhtmlspecialchars($r['msg']);
		  $res = (substr($r['res'],0,1)=='1' ? 'OK' : 'ʧ��')."<br>$r[cnt]��";
		  $a = explode('/',$r['api']); $key = substr($a[0],4);
		  $api_u = empty($stype[$a[1]]) ? 'ϵͳ����' : $stype[$a[1]];
		  $api = @$sms_cfg_aset[$a[0]]['name'].'<br>'.$api_u;
		  $itemstr .= "<tr class=\"txt\"><td class=\"txtC w40\">$selectstr</td>";
		  $itemstr .= "<td class=\"txtL w190\">$tel</td>\n";
		  $itemstr .= "<td class=\"txtL w240\">$msg</td>\n";
		  $itemstr .= "<td class=\"txtC\">$r[mname]</td>\n";
		  $itemstr .= "<td class=\"txtC w110\">$r[ip]<br>$time</td>\n";
		  $itemstr .= "<td class=\"txtC w50\">$res</td>\n";
		  $itemstr .= "<td class=\"txtC w60\">$api</td>\n";
		  $itemstr .= "</tr>\n"; 
		  
	  }
	  echo $itemstr;
	  tabfooter();
	  echo multi($db->result_one("SELECT count(*) $fromsql $wheresql"),$mrowpp,$page, "?entry=$entry&page=$page$filterstr");
  
	  tabheader('��������');
	  $str = "<input class=\"radio\" type=\"radio\" name=\"arcdeal_del\" value=\"now\">ɾ����¼ &nbsp;";
	  $str .= "<input class=\"radio\" type=\"radio\" name=\"arcdeal_del\" value=\"m3\">ɾ��3��ǰ &nbsp;";
	  $str .= "<input class=\"radio\" type=\"radio\" name=\"arcdeal_del\" value=\"m1\">ɾ��1��ǰ &nbsp;";
	  trbasic("<input class=\"checkbox\" type=\"checkbox\" name=\"arcdeal[delete]\" value=\"1\" onclick='deltip()'> ɾ����¼",'',$str,'');
	  //trbasic("<input class=\"checkbox\" type=\"checkbox\" name=\"arcdeal[checkf]\" value=\"1\"> �������",'checkv',makeoption(array('1'=>'�������','0'=>'��������')),'select');
	  tabfooter('bsubmit');
  }else{
	if(empty($arcdeal)) cls_message::show('��ѡ�������Ŀ��',"?entry=$entry&page=$page$filterstr");
	if(empty($arcdeal_del)) cls_message::show('��ѡ�������Ŀ��',"?entry=$entry&page=$page$filterstr");
	//echo "$arcdeal[delete],$arcdeal_del";
	if($arcdeal_del=='now'){
		if(empty($selectid)) cls_message::show('��ѡ���¼��',"?entry=$entry&page=$page$filterstr");
		foreach($selectid as $k){
			$db->query("DELETE FROM {$tblprefix}sms_sendlogs WHERE cid='$k'",'UNBUFFERED');
			continue;
		}
	}elseif($arcdeal_del=='m3'){
		$sql = "DELETE FROM {$tblprefix}sms_sendlogs WHERE stamp<='".($timestamp-90*24*3600)."'";
		//echo "$sql";
		$db->query($sql,'UNBUFFERED');
	}elseif($arcdeal_del='m1'){
		$sql = "DELETE FROM {$tblprefix}sms_sendlogs WHERE stamp<='".($timestamp-30*24*3600)."'";
		//echo "$sql";
		$db->query($sql,'UNBUFFERED');
	}
	cls_message::show('��¼���������ɹ�'.'��',"?entry=$entry&page=$page$filterstr");
 }

}elseif($action=='chargelog'){

  $keyword = empty($keyword) ? '' : $keyword; 
  $keytype = empty($keytype) ? 'mname' : $keytype;
  $filterstr = $checked?"&checked=$checked":'';
  /*if(!empty($mid)){
	  $keyword = $db->result_one("SELECT mname FROM {$tblprefix}members WHERE mid='$mid'");
	  $keytype = 'mid'; //,'mid'
  }*/
  foreach(array('keyword','keytype','action') as $k) $$k && $filterstr .= "&$k=".rawurlencode(stripslashes($$k));

  $selectsql = "SELECT * ";
  $wheresql = " WHERE 1=1 "; //cu.mid='$memberid' commu_offer. archives1.
  $fromsql = "FROM {$tblprefix}sms_recharge ";
    
  /*if(!empty($mid)){ 
	  $kmname = $db->result_one("SELECT mname FROM {$tblprefix}members WHERE mid='$mid'");
	  $wheresql .= " AND (mname='$kmname') ";
	  $keyword = $kmname;
  }else*/
  if($keyword){
	  if($keytype=='mid'){
	 	 $kmname = $db->result_one("SELECT mname FROM {$tblprefix}members WHERE mid='$keyword'");
	 	 $wheresql .= " AND (mname='$kmname') ";
	  }elseif($keytype){
		 $wheresql .= " AND ($keytype ".sqlkw($keyword).") ";
	  }else{
	  	 $wheresql .= " AND (tel ".sqlkw($keyword)." OR msg ".sqlkw($keyword).") ";
	  }
  }
  
  if(!submitcheck('bsubmit')){
	  
	  echo form_str('chargelog',"?entry=$entry$filterstr&page=$page");
	  tabheader_e();
	  echo "<tr><td class=\"txt txtleft\">";
	  echo "<input class=\"text\" name=\"keyword\" type=\"text\" value=\"$keyword\" size=\"8\" style=\"vertical-align: middle;\" title=\"�ؼ���\">&nbsp; ";
	  echo "<select style=\"vertical-align: middle;\" name=\"keytype\">".makeoption(array('0'=>'--ɸѡ��Χ--','mname'=>'��Ա��','mid'=>'��ԱID','ip'=>'IP','msg'=>'������ʾ','opname'=>'������','note'=>'��ע'),$keytype)."</select>&nbsp; ";
	  echo strbutton('bfilter','ɸѡ');
	  tabfooter();
	  tabheader("���ų�ֵ��¼",'','',10);
	  $cy_arr = array("<input class=\"checkbox\" type=\"checkbox\" name=\"chkall\" onclick=\"checkall(this.form, 'selectid', 'chkall')\">");
	  //$cy_arr[] = '����ʱ��';
	  $cy_arr[] = '��ԱID';
	  $cy_arr[] = '��Ա����';
	  $cy_arr[] = '��ֵ����';
	  $cy_arr[] = 'ʱ��';
	  $cy_arr[] = 'ip';
	  $cy_arr[] = '������ʾ';
	  $cy_arr[] = '������';
	  $cy_arr[] = '��ע';
	  
	  trcategory($cy_arr);
  
	  $pagetmp = $page; //echo "$selectsql $fromsql $wheresql";
	  do{
		  $query = $db->query("$selectsql $fromsql $wheresql ORDER BY cid DESC LIMIT ".(($pagetmp - 1) * $mrowpp).",$mrowpp");
		  $pagetmp--;
	  } while(!$db->num_rows($query) && $pagetmp);
  
	  $itemstr = ''; $stype = array('sadm'=>'����Ա��','scom'=>'��Ա����','ctel'=>'������֤',);
	  while($r = $db->fetch_array($query)){
		  $selectstr = "<input class=\"checkbox\" type=\"checkbox\" name=\"selectid[$r[cid]]\" value=\"$r[cid]\">";
		  $time = date('Y-m-d H:i',$r['stamp']); //print_r($r); //die();
		  
		  $msg = mhtmlspecialchars($r['msg']);
		  $itemstr .= "<tr class=\"txt\"><td class=\"txtC w40\">$selectstr</td>";
		  $itemstr .= "<td class=\"txtC\">$r[mid]</td>\n";
		  $itemstr .= "<td class=\"txtC\">$r[mname]</td>\n";
		  $itemstr .= "<td class=\"txtC\">$r[cnt]</td>\n";
		  $itemstr .= "<td class=\"txtC\">$time</td>\n";
		  $itemstr .= "<td class=\"txtC\">$r[ip]</td>\n";
		  $itemstr .= "<td class=\"txtC\">$r[msg]</td>\n";
		  $itemstr .= "<td class=\"txtC\">$r[opname]</td>\n";
		  $itemstr .= "<td class=\"txtC\">$r[note]</td>\n";
		  $itemstr .= "</tr>\n"; 
		  
	  }
	  echo $itemstr;
	  tabfooter();
	  echo multi($db->result_one("SELECT count(*) $fromsql $wheresql"),$mrowpp,$page, "?entry=$entry&page=$page$filterstr");
  
	  tabheader('��������');
	  $str = "<input class=\"radio\" type=\"radio\" name=\"arcdeal_del\" value=\"now\">ɾ����¼ &nbsp;";
	  $str .= "<input class=\"radio\" type=\"radio\" name=\"arcdeal_del\" value=\"m3\">ɾ��3��ǰ &nbsp;";
	  $str .= "<input class=\"radio\" type=\"radio\" name=\"arcdeal_del\" value=\"m1\">ɾ��1��ǰ &nbsp;";
	  trbasic("<input class=\"checkbox\" type=\"checkbox\" name=\"arcdeal[delete]\" value=\"1\" onclick='deltip()'> ɾ����¼",'',$str,'');
	  //trbasic("<input class=\"checkbox\" type=\"checkbox\" name=\"arcdeal[checkf]\" value=\"1\"> �������",'checkv',makeoption(array('1'=>'�������','0'=>'��������')),'select');
	  tabfooter('bsubmit');
  }else{
	if(empty($arcdeal)) cls_message::show('��ѡ�������Ŀ��',"?entry=$entry&page=$page$filterstr");
	if(empty($arcdeal_del)) cls_message::show('��ѡ�������Ŀ��',"?entry=$entry&page=$page$filterstr");
	//echo "$arcdeal[delete],$arcdeal_del";
	if($arcdeal_del=='now'){
		if(empty($selectid)) cls_message::show('��ѡ���¼��',"?entry=$entry&page=$page$filterstr");
		foreach($selectid as $k){
			$db->query("DELETE FROM {$tblprefix}sms_recharge WHERE cid='$k'",'UNBUFFERED');
			continue;
		}
	}elseif($arcdeal_del=='m3'){
		$sql = "DELETE FROM {$tblprefix}sms_recharge WHERE stamp<='".($timestamp-90*24*3600)."'";
		//echo "$sql";
		$db->query($sql,'UNBUFFERED');
	}elseif($arcdeal_del='m1'){
		$sql = "DELETE FROM {$tblprefix}sms_recharge WHERE stamp<='".($timestamp-30*24*3600)."'";
		//echo "$sql";
		$db->query($sql,'UNBUFFERED');
	}
	cls_message::show('��¼���������ɹ�'.'��',"?entry=$entry&page=$page$filterstr");
 }
 
}elseif($action=='balance'){

  $keyword = empty($keyword) ? '' : $keyword; 
  $mchid = empty($mchid) ? '0' : $mchid;
  $chg1 = empty($chg1) ? '' : $chg1;
  $chg2 = empty($chg2) ? '' : $chg2;
  $filterstr = $checked?"&checked=$checked":'';
  foreach(array('keyword','mchid','chg1','chg2') as $k) $$k && $filterstr .= "&$k=".rawurlencode(stripslashes($$k));

  //$entryv = "&action=$action";
  $wheresql = ' WHERE 1=1 ';
  $fromsql = "FROM {$tblprefix}members m";
  
  $keyword && $wheresql .= " AND (m.mid='$keyword' OR m.mname ".sqlkw($keyword).")";
  $mchid && $wheresql .= " AND m.mchid='$mchid'";
  $chg1 && $wheresql .= " AND m.sms_charge>='$chg1'";
  if($chg2 && $chg2>$chg1) $wheresql .= " AND m.sms_charge<='$chg2'";

  if(!submitcheck('bsubmit')){
	  
	  echo form_str($actionid.'members',"?entry=$entry$filterstr&page=$page");
	  tabheader_e();
	  //trhidden('mchid',$mchid);
	  $sarr['0'] = '--��Ա����--';
	  foreach($mchannels as $k=>$v) $sarr[$k] = $v['cname'];
	  echo "<tr><td colspan=\"2\" class=\"txt txtleft\">";
	  echo "<input class=\"text\" name=\"keyword\" type=\"text\" value=\"$keyword\" size=\"8\" style=\"vertical-align: middle;\" title=\"������Ա����ID\">&nbsp; ";
	  echo "<select style=\"vertical-align: middle;\" name=\"mchid\">".makeoption($sarr,$mchid)."</select>&nbsp; ";
	  /*
	  foreach($grouptypes as $gtid => $grouptype){
		  echo "<select style=\"vertical-align: middle;\" name=\"ugid$gtid\">".makeoption(array('0' => $grouptype['cname']) + ugidsarr($gtid),${"ugid$gtid"})."</select>&nbsp; ";
	  }
	  //*/
	  echo "�������<input class=\"text\" name=\"chg1\" type=\"text\" value=\"$chg1\" size=\"4\" style=\"vertical-align: middle;\" title=\"����:�����\">";
	  echo "~<input class=\"text\" name=\"chg2\" type=\"text\" value=\"$chg2\" size=\"4\" style=\"vertical-align: middle;\" title=\"���:�����\">�� ";
  
	  trhidden('action',$action);
	  echo strbutton('bfilter','ɸѡ');
	  echo "</td></tr>";
	  tabfooter();
	  //�б���	
	  tabheader("��Ա�б�",'','',10);
	  $cy_arr = array("<input class=\"checkbox\" type=\"checkbox\" name=\"chkall\" onclick=\"checkall(this.form, 'selectid', 'chkall')\">",);
	  $cy_arr[] = '��ԱID|L';
	  $cy_arr[] = '��Ա����|L';
	  $cy_arr[] = '��Ա����';
	  $cy_arr[] = '���';
	  #foreach($grouptypes as $k => $v) $cy_arr["ugid$k"] = $v['cname'];
	  $cy_arr[] = '����(��)';
	  $cy_arr[] = '�ֽ�(Ԫ)';
	  $cy_arr[] = '��ֵ��¼';
	  $cy_arr[] = 'ע������';
	  $cy_arr[] = '�������';
	  //$cy_arr[] = '����';
	  //$cy_arr[] = '��Ա��';
	  //$cy_arr[] = '����';
	  trcategory($cy_arr);
  
  
	  $pagetmp = $page; //echo "$selectsql $fromsql $wheresql";
	  do{
		  $query = $db->query("SELECT * $fromsql $wheresql ORDER BY mid DESC LIMIT ".(($pagetmp - 1) * $atpp).",$atpp");
		  $pagetmp--;
	  } while(!$db->num_rows($query) && $pagetmp);
  
	  $itemstr = '';
	  while($r = $db->fetch_array($query)){
		  $selectstr = "<input class=\"checkbox\" type=\"checkbox\" name=\"selectid[$r[mid]]\" value=\"$r[mid]\">";
		  $mnamestr ="<a href=".cls_Mspace::IndexUrl($r)." target=\"_blank\">". $r['mname'].($r['isfounder'] ? '-��ʼ��': '').'</a>';
		  $mchannelstr = @$mchannels[$r['mchid']]['cname'];
		  $checkstr = $r['checked'] == 1 ? 'Y' : '-';
		  foreach($grouptypes as $k => $v){
			  ${'ugid'.$k.'str'} = '-';
			  if($r['grouptype'.$k]){
				  $usergroups = cls_cache::Read('usergroups',$k);
				  ${'ugid'.$k.'str'} = @$usergroups[$r['grouptype'.$k]]['cname'];
			  }
		  }
		  $sms_charge = $r['sms_charge'];
		  $regdatestr = $r['regdate'] ? date('Y-m-d',$r['regdate']) : '-';
		  $lastvisitstr = $r['lastvisit'] ? date('Y-m-d',$r['lastvisit']) : '-';
		  $viewstr = "<a id=\"{$actionid}_info_$r[mid]\" href=\"?entry=extend&extend=memberinfo&mid=$r[mid]\" onclick=\"return showInfo(this.id,this.href)\">��Ϣ</a>";
		  $editstr = "<a href=\"?entry=extend&extend=member&mid=$r[mid]\" onclick=\"return floatwin('open_memberedit',this)\">����</a>";
		  $groupstr = "<a href=\"?entry=extend&extend=membergroup&mid=$r[mid]\" onclick=\"return floatwin('open_memberedit',this)\">��Ա��</a>";
  
		  $itemstr .= "<tr class=\"txt\"><td class=\"txtC w40\" >$selectstr</td>";
		  $itemstr .= "<td class=\"txtL\">$r[mid]</td>\n";
		  $itemstr .= "<td class=\"txtL\">$mnamestr</td>\n";
		  $itemstr .= "<td class=\"txtC\">$mchannelstr</td>\n";
		  $itemstr .= "<td class=\"txtC w35\">$checkstr</td>\n";
		  #foreach($grouptypes as $k => $v) $itemstr .= "<td class=\"txtC\">".${'ugid'.$k.'str'}."</td>\n";
		  $itemstr .= "<td class=\"txtC\">$sms_charge</td>\n";
		  $itemstr .= "<td class=\"txtC\">$r[currency0]</td>\n";
		  $itemstr .= "<td class=\"txtC\"><a href=\"?entry=sms_admin&action=chargelog&keytype=mid&keyword=$r[mid]\">�鿴����</a></td>\n";
		  $itemstr .= "<td class=\"txtC\">$regdatestr</td>\n";
		  $itemstr .= "<td class=\"txtC\">$lastvisitstr</td>\n";
		  //$itemstr .= "<td class=\"txtC\">$viewstr</td>\n";
		  //$itemstr .= "<td class=\"txtC\">$groupstr</td>\n";
		  //$itemstr .= "<td class=\"txtC\">$editstr</td>\n";
		  $itemstr .= "</tr>\n";
	  }
	  $counts = $db->result_one("SELECT count(*) $fromsql $wheresql");
	  $multi = multi($counts, $atpp, $page, "?entry=$entry$filterstr&action=$action");
	  echo $itemstr;
	  tabfooter();
	  echo $multi;
	  
	  //������
	  tabheader('������Ŀ');
	  $sql = "SELECT SUM(sms_charge) as sms_charge FROM {$tblprefix}members ";
	  $sum = $db->result_one($sql);
	  trbasic('���ų�ֵ','','<input name="arc_charge" >(��) ��ֵ��ֵ���ɱ�ע<input name="arc_note" >(�ɲ���)','');
	  $msg = '������Ϊ��ֵ,��Ϊ��,��Ϊ�۳�����Ա��ǰ��������ܺ�('.$sum.')����
	  �ӿڵ�ǰ�������('.$balance[1].')'.$sms_cfg_aset[$sms_cfg_api]['unit'].'��'.($sms_cfg_aset[$sms_cfg_api]['unit']=='Ԫ' ? '�ӿڼ۸�('.$sms_cfg_papi.')Ԫ/����' : '').'';
	  trbasic('˵��','',$msg,'');
	  tabfooter('bsubmit');
  
  }else{
	  //if(empty($arcdeal)) cls_message::show('��ѡ�������Ŀ',"?entry=$entry$extend_str&page=$page$filterstr&action=$action");
	  if(empty($selectid)) cls_message::show('��ѡ���Ա',"?entry=$entry&page=$page$filterstr&action=$action");
	  if(!is_numeric($arc_charge)) cls_message::show('��ֵ���� ����Ϊ����! ',"?entry=$entry&page=$page$filterstr&action=$action");
	  /*
	  if($sms_cfg_aset[$sms_cfg_api]['unit']=='Ԫ') $b2 = $balance[1]/$sms_cfg_papi;
	  else $b2 = $balance[1];
	  if($b2<count($selectid)*$arc_charge) cls_message::show('�ӿ�����! ');
	  */
	  foreach($selectid as $id){
		$sql = "UPDATE {$tblprefix}members SET sms_charge=sms_charge+('$arc_charge') WHERE mid='$id'";
		$db->query($sql,'UNBUFFERED');
		//echo "$sql";
		$mname = $db->result_one("SELECT mname FROM {$tblprefix}members WHERE mid='$id'");
		$sql = "INSERT INTO {$tblprefix}sms_recharge SET 
		  mid='$id',mname='$mname',stamp='$timestamp',ip='$onlineip',
		  cnt='$arc_charge',msg='����Ա',opname='{$curuser->info['mname']}',note='$arc_note'";
		$db->query($sql);
	  }

	  adminlog('��Ա���ų�ֵ','���ų�ֵ����');
	  cls_message::show('���ų�ֵ�������',"?entry=$entry&page=$page$filterstr&action=$action");
  }

}elseif($action=='sendsms'){

  $apiarr = $sms_cfg_aset[$sms_cfg_api];
  $apimsg = "���:($balance[1]".$apiarr['unit']."){$ermsg}���ӿ�����:(".$apiarr['name'].")";
  $apimsg .= $apiarr['home'] ? "��<a href=\"".$apiarr['home']."\" target=\"_blank\">�ӿڹ���</a>" : '';
  
  if(!submitcheck('bsubmit')){
	  
		tabheader("���ŷ���",'sendsms',"?entry=$entry&page=$page",2,1,1);
		trbasic('�ӿ���Ϣ','',"$apimsg",'');
		trbasic("�ֻ�����",'fmdata[tel]','','textarea',array('w'=>360,'h'=>80,'validate'=>' rule="text" must="1" min="11" max="24000" rev="�ֻ�����" ','guide'=>'һ��һ������,�ŷֿ����Զ��������·��š�(-)��<br>һ�����2000���͸��ֻ�����'));
		$hostname = cls_env::mconfig('hostname'); //�ܶ�ӿ�Ҫ��ǩ��,�����Ĭ��ǩ��
		$cmsg = "<br />����[".$sms->cfg_mchar."]���֣���[".($sms->cfg_mchar-5)."]����ÿ���۷ѡ�[��ǰ������<span id='mcnt'>0</span>����]";
		$cmsg .= "<br />��Щ�ӿ�Ҫ���������Ҫ<a href='#' onClick='setMsgSign()'>�������ơ���ĳ��˾�����򡱡�������������ǩ��,�����ṩ��[����ģ��]</a>��������ܷ�����ȥ��������������ṩ�����磡";
		trbasic("��������",'fmdata[msg]','','textarea',array('w'=>360,'h'=>80,'validate'=>' rule="text" must="1" min="3" max="255" rev="��������" ','guide'=>'һ�η���,���255���ַ����ڣ�<a href="#" onclick="setTestMsg()">[���Զ���]</a>'.$cmsg));
		$apiarr['note'] && trbasic('�ӿ�˵��','',$apiarr['note'],'');
		trhidden('action',$action);

		if($balance[1]<=0){ 
			//�������ݲ���Ϊ��</div>
			trbasic('�ӿ���ʾ','',"<div style='' class='validator_message warn'>���㣡����ϵ��<a href=\"".$apiarr['home']."\" target=\"_blank\">���Žӿ��ṩ��</a>�ݳ�ֵ��</div>",'');
			tabfooter('');
		}
		tabfooter('bsubmit'); 
		echo "\r\n<script type='text/javascript'>
		var m=\$id('fmdata[msg]');m.onblur=function(){\$id('mcnt').innerHTML=m.value.length;}
		function setTestMsg(){m.value='{$apiarr['name']}���:$balance[1]{$apiarr['unit']}(".date('H:i:s').")';\$id('mcnt').innerHTML=m.value.length;}
		function setMsgSign(){m.value='��{$hostname}��'+m.value;m.onblur();}
		</script>";
  }else{
		
		$msg = $sms->sendSMS($fmdata['tel'],$fmdata['msg'],'sadm');
		if($msg[0]==1){
			$msg0 = "���ͳɹ�!";
		}else{
			$msg0 = "����ʧ��!";
		}
		$msg = $msg0.$msg[1];
		cls_message::show($msg,axaction(6,M_REFERER));
 }
		
}elseif($action=='setapi'){

	$f1 = function_exists('fsockopen'); $f2 = function_exists('curl_init');
	if(!$f1 || !$f2){
		$msg = "<p style='font-size:14px;color:#F00;padding:10px 20px;'><span style='color:#FF00FF'>��ʾ��</span>[1.]������php.ini,allow_url_fopen=On,����php_curl.dll��չ��[2.]��֤fsockopen,curl_init�Ⱥ������ã�[3.]���úú���ʾ�Զ��رա�</p>";
		a_guide($msg,1);	
	}
	
	if(!submitcheck('bmconfigs')){
		$sms_cfg_api = $sms->isClosed() ? '(close)' : $sms_cfg_api;
		if(!modpro()) unset($sms_cfg_aset['0test']);
		tabheader('�ֻ����Žӿ�','cfmobile','?entry=sms_admin&action=setapi',2,0,1);
		echo "<tr class=\"txt\"><td class=\"txt txtright fB borderright\">�ӿ��ṩ��</td>\n".
		"<td class=\"txtL\">\n";
		$jstab = ''; $jsflg = '(close)';
		foreach($sms_cfg_aset as $k=>$v){
			$jsitm = '';
			$name = empty($v['gray']) ? "$v[name]" : "<span style='color:#BBB' title='ֻ�ṩ������ά��,���û�����ʹ�������ӿ�'>$v[name]</span>";
			echo "<label><input class=\"radio\" type=\"radio\" id=\"sms_cfg_api$k\" name=\"mconfigsnew[sms_cfg_api]\" value=\"$k\" onclick=\"setApi('$k')\"".($sms_cfg_api == $k ? ' checked="checked"' : '').">$name</label>&nbsp; ";
			$jsitm .= $v['home'] ? "<a href=\"$v[home]\" target=\"_blank\">�ӿڹ�����ҳ</a>" : '';
			$jsitm .= $v['admin'] ? ($jsitm ? ' | ' : '')."<a href=\"$v[admin]\" target=\"_blank\">�ӿڹ����½</a>" : '';
			$jsitm .= $v['note'] ? ($jsitm ? ' <br /> ' : '').str_replace(array("\r","\n",'"',"'"),array("","","\\\"","\\'"),$v['note']) : '';
			$jstab .= "\nvar sms_js_$k = '".($jsitm ? $jsitm : '')."';";
			$jstab .= "\nvar sms_js_{$k}_uid = '".(@$mconfigs['sms_'.$k.'_uid'] ? $mconfigs['sms_'.$k.'_uid'] : '')."';";
			$jstab .= "\nvar sms_js_{$k}_upw = '".(@$mconfigs['sms_'.$k.'_upw'] ? $mconfigs['sms_'.$k.'_upw'] : '')."';";
			echo "<input type=\"hidden\" name=\"mconfigsnew[sms_{$k}_uid]\" value=\"".@$mconfigs['sms_'.$k.'_uid']."\">";
			echo "<input type=\"hidden\" name=\"mconfigsnew[sms_{$k}_upw]\" value=\"".@$mconfigs['sms_'.$k.'_upw']."\">";
			if($k==$sms_cfg_api) $jsflg = $k;
			//echo "\n\n<hr>$jsitm;\n";
		}
		echo "<label><input class=\"radio\" type=\"radio\" id=\"sms_cfg_api0\" name=\"mconfigsnew[sms_cfg_api]\" value=\"(close)\" onclick=\"setApi('(close)')\"".($sms_cfg_api == '(close)' ? ' checked="checked"' : '').">[�رսӿ�]</label>&nbsp; ";
		echo "</td></tr>\n";
		trbasic('�ӿ�˵��','',"",'',array('rowid'=>'sms_id_note')); 
		trbasic('�ʺ�/���к�','mconfigsnew[sms_cfg_uid]',@$mconfigs['sms_cfg_uid'],'text',array('rowid'=>'sms_uid'));  
		trbasic('����/��Կ','mconfigsnew[sms_cfg_upw]',@$mconfigs['sms_cfg_upw'],'password',array('validate' => ' autocomplete="off"','rowid'=>'sms_upw')); 
		trbasic('�ӿڼ۸�(Ԫ/��)','mconfigsnew[sms_cfg_papi]',isset($mconfigs['sms_cfg_papi']) ? $mconfigs['sms_cfg_papi'] : '0.1','text',array('guide'=>'(Ԫ/��)�������ǰ�ӿڲ��ܲ�Ѱ�۸�,����д����ӿڿ��Բ�ѯ�۸��,��ʹ�ô��','validate'=>' rule="float" must="1" regx="" min="0.001" max="9999" '));
		trbasic('��Ա�۸�(Ԫ/��)','mconfigsnew[sms_cfg_price]',isset($mconfigs['sms_cfg_price']) ? $mconfigs['sms_cfg_price'] : '0.15','text',array('guide'=>'(Ԫ/��)�����ڸ���Ա��ֵ��ʾ��','validate'=>' rule="float" must="1" regx="" min="0.001" max="9999" '));
		trbasic('����Ա�ֻ�','mconfigsnew[hosttel]',@$mconfigs['hosttel'],'text',array('guide'=>'���ڹ���Ա�����ֻ����ţ���û��ͨ���Žӿڣ��ɲ���'));
		trbasic('�����뷢���޶�','mconfigsnew[sms_cfg_smax]',isset($mconfigs['sms_cfg_smax']) ? $mconfigs['sms_cfg_smax'] : '10','text',array('guide'=>'��������,һ����(24Сʱ)����ܷ��Ͷ��ŵĴ���; Ⱥ��ȡǰ24�������ַ���Ĭ��Ϊ10������ݶ�����Ӫ�����á�','validate'=>' rule="float" must="1" regx="" min="0.001" max="9999" '));
		trbasic('��IP����ʱ����','mconfigsnew[sms_cfg_ipmax]',isset($mconfigs['sms_cfg_ipmax']) ? $mconfigs['sms_cfg_ipmax'] : '120','text',array('guide'=>'��λ(��)������IP���η�����Ϣ�����ʱ������0Ϊ�����ƣ�����ݶ�����Ӫ�����á�','validate'=>' rule="int" must="1" regx="" min="0" max="9999" '));
		echo "\r\n<script type='text/javascript'>$jstab\nfunction setApi(api){var tr = \$id('sms_id_note');var td = tr.getElementsByTagName('td')[1];var td_uid=\$id('mconfigsnew[sms_cfg_uid]');var td_upw = \$id('mconfigsnew[sms_cfg_upw]');var tr_uid = \$id('sms_uid');var tr_upw = \$id('sms_upw');if(api=='(close)'){tr.style.display = 'none';tr_uid.style.display='none';tr_upw.style.display='none';}else{tr.style.display = '';tr_uid.style.display='';tr_upw.style.display='';eval('var note = sms_js_'+api+';var uid=sms_js_'+api+'_uid;var upw=sms_js_'+api+'_upw;');td.innerHTML = note;td_uid.value= uid;td_upw.value= upw;}}setApi('$jsflg');</script>";
		tabfooter('bmconfigs');
		a_guide('sms_apiset');
	}else{
		!empty($mconfigsnew) or cls_message::show('����������',axaction(6,M_REFERER));
		isset($mconfigsnew['sms_'.$mconfigsnew['sms_cfg_api'].'_uid']) or $mconfigsnew['sms_'.$mconfigsnew['sms_cfg_api'].'_uid']='';
		isset($mconfigsnew['sms_'.$mconfigsnew['sms_cfg_api'].'_upw']) or $mconfigsnew['sms_'.$mconfigsnew['sms_cfg_api'].'_upw']='';
		//���µ����ø���ԭ��������
		$mconfigsnew['sms_'.$mconfigsnew['sms_cfg_api'].'_uid'] = $mconfigsnew['sms_cfg_uid'];
		$mconfigsnew['sms_'.$mconfigsnew['sms_cfg_api'].'_upw'] = $mconfigsnew['sms_cfg_upw'];
		
		saveconfig('sms');
		adminlog('���Žӿ�����');
		cls_message::show('���Žӿ��������',axaction(6,M_REFERER));
	}
	
}elseif($action=='apiwarn'){
	
	$file = M_ROOT."dynamic/sms/balance_apiwarn.wlog"; 
	if(!empty($unlink)){
	    $fp = _08_FilesystemFile::getInstance();
		$fp->delFile(M_ROOT.$file);
	}
	$iapi = $sms_cfg_aset[$sms_cfg_api];
	$info = '';
	$info .= $iapi['home'] ? " ------ <a href=\"$iapi[home]\" target=\"_blank\">�ӿڹ�����ҳ</a>" : '';
	$info .= $iapi['admin'] ? ($info ? ' | ' : '')."<a href=\"$iapi[admin]\" target=\"_blank\">�ӿڹ����½</a>" : '';
	$sum1 = $db->result_one("SELECT SUM(sms_charge) as sms_charge FROM {$tblprefix}members ");
	$sum2 = $db->result_one("SELECT SUM(cnt)        as sms_cnt FROM {$tblprefix}sms_sendlogs WHERE stamp>='".($timestamp-30*24*3600)."'");
	$agv1 = round($sum2/30);
	tabheader('ͳ���뱨��','info_warn','');
	trbasic('�ӿ���Ϣ','',"�ӿ�����:(".$iapi['name'].") $info",'');
	trbasic('�ӿ������','',"($balance[1])".$iapi['unit']."",''); 
	trbasic('��Ա�����','',"($sum1)��",'');
	trbasic('���ŷ�������','',"($sum2)�� [����30�����]",'');
	trbasic('ƽ��ÿ�췢��','',"($agv1)�� [����30�����]",'');
	if($iapi['unit']=='Ԫ') $b2 = $balance[1]/$sms_cfg_papi;
	else $b2 = $balance[1];
	$wno = 0;
	if($balance[1]<=0){
		$wno++;
		trbasic("<span class='cDRed'>����{$wno}��</span>",'',"��ǰ���Ϊ[0]������ϵ�ӿڹ�Ӧ�̳�ֵ",'');
	}
	$sum5 = $agv1*5;
	if($b2<$sum5){
		$wno++;
		trbasic("<span class='cDRed'>����{$wno}��</span>",'',"��[���30��]��[ƽ��ÿ�췢����]���㣬��ǰ����Ѿ�����ʹ��5�죬����ϵ�ӿڹ�Ӧ�̳�ֵ",'');
	}
	if($wflag = $sms->balanceWarn(5)){
		$wno++;
		$unlink = "<a href='?entry=sms_admin&action=apiwarn&unlink=1'>[����]</a>";
		trbasic("<span class='cDRed'>����{$wno}��</span>",'',"��⵽[���5����]����ӿ���������Ͷ���ʧ�ܼ�¼���£�ɾ���ļ����$unlink",'');
		$data = mhtmlspecialchars(cls_string::CutStr(file_get_contents($file),2048)); //2K
		echo '<tr><td colspan="2" class="txt txtleft"><textarea name="textarea" id="textarea" style="width:100%" rows="12">'.$data.'</textarea></td></tr>';
	}
	if($iapi['unit']=='��'&&$wno===0){
		if($b2<$sum1){
			trbasic("<span class='cBlue'>��Ҫ��ʾ��</span>",'',"��Ա����� &gt; �ӿ���������Ա�������һ�������ʹ���꣬������ȷ���Ƿ񾡿��ֵ��",'');
		}
	}
	if($wno===0){
		trbasic("<span class='cGreen'>������ʾ��</span>",'',"û�м�⵽�κξ�����Ϣ�������ʹ�ýӿڣ�",'');
	}

	tabfooter('');

}elseif($action=='enable'){
    if(!submitcheck('bsubmit')){
        $smscfgsets = cls_cache::exRead('smsregcodes');
        $smscfgsave = cls_cache::cacRead('smsconfigs',_08_USERCACHE_PATH);
		$groups = array(
			'sys' => '����ģ��',
			'ex' => '��չģ��',
			'cu' => '����ģ��',
		);

		// �Ƿ������ת����������ҳ,
		if(empty($smscfgsets['cumodels']['cuids'])){
			$endgk = 'ex';
			unset($groups['cu']);
		}else{
			$endgk = 'cu';	
		}

		//�Ƿ��׼���ڻ���������

		foreach($groups as $gk=>$gname){
			tabheader("{$gname}����",'exconfigs', $gk=='sys' ? "?entry=sms_admin&action=$action" : '',2,0,1);	
				echo "<tr class=\"txt\">\n";
				if($gk=='cu'){
					echo "<td width='20%' class='txt txtright fB'>����ģ��</td>\n";
					echo "<td width='20%' class='txt txtcenter fB'>��������</td>\n";
					echo "<td class='txt txtleft fB'>˵��</td>\n";
				}else{
					echo "<td width='20%' class='txt txtright fB'>ģ��/״̬</td>\n";
					echo "<td class='txt txtleft'>����ģ��/����</td>\n";		
				}
				echo "</td></tr>\n";
				foreach($smscfgsets as $key=>$v){
					if($gk=='cu' && $v['group']=='cu'){
						$commus = cls_cache::Read('commus');
						foreach($v['cuids'] as $cuid){
							if(!isset($commus[$cuid])) continue;
							$cucfg = $commus[$cuid]; 
							echo "<tr class=\"txt\">\n<td width='20%' class='txt txtright fB'>����($cuid) - {$cucfg['cname']}</td>\n";
							echo "<td width='20%' class='txt txtcenter'><a href='?entry=commus&action=commudetail&cuid=$cuid' onclick=\"return floatwin('open_commussms',this)\">��������</a></td>\n";
							echo "<td class='txt txtleft'>{$cucfg['remark']}</td>\n</td></tr>\n";
						}
					}elseif($v['group']==$gk){
						$cfg1 = $v; //һ������ֵ
						$val1 = @$smscfgsave[$key]; //һ��滺�� 
						$tpl = empty($val1['tpl']) ? @$v['tpl'] : @$val1['tpl'];
						$ischeck = !empty($val1['open']) ? 'checked="checked"' : '';
						$cbox = in_array($key,array('commtpl','membexp')) ? '' : '<input name="smsarrnew['.$key.'][open]" type="checkbox" value="1"'. $ischeck .'/>'; //.$val1['open']
						$cbox = empty($cfg1['nocbox']) ? $cbox : '';
						$sarea = (@$v['tpl']=='notpl') ? '' : '<textarea class="js-resize" name="smsarrnew['.$key.'][tpl]" id="smsarrnew['.$key.'][tpl]" style="width:400px;height:60px">'.$tpl.'</textarea>';
						if(!empty($cfg1['ugcfgs'])){
							$ugtitle = $key=='membexp' ? '���������»�Ա��: ' : @$cfg1['ugtitle'];
							$sarea .= $ugtitle.@makecheckbox('smsarrnew['.$key.'][cfgs][ugids][]',$cfg1['ugcfgs'],$val1['cfgs']['ugids']); //array('4_2')
						}
						if($key=='membexp'){
							$daysarr = array(15=>'15��',7=>'7��',3=>'3��',1=>'1��');
							$sarea .= '<br>��ǰ��������: '.@makeradio('smsarrnew['.$key.'][cfgs][days]',$daysarr,$val1['cfgs']['days']); //'7'
						}
						trbasic("$key - ".$v['title']." $cbox",'',$sarea,'',array('guide' =>$v['guide'] ));
					}
				}
			tabfooter($endgk==$gk ? 'bsubmit' : '');
		}
        //tabfooter('bsubmit');
        a_guide('sms_open');
    }else{
        //$smsarr = cls_cache::exRead('smsregcodes');
        //���µ����ø���ԭ��������
        foreach($smsarrnew as $smskey=>$smsvalue){
           $smsarrnew[$smskey]['open'] = empty($smsvalue['open']) ? '0' : 1;
        }
        /*foreach($smsarr as $key=>$value){
            $smsarr[$key]['open'] = $smsarrnew[$key]['open'];
            $smsarr[$key]['tpl'] = $smsarrnew[$key]['tpl'];
            //array_merge($value,$smsvalue);
        }*/
        cls_CacheFile::cacSave($smsarrnew,'smsconfigs',_08_USERCACHE_PATH);
        cls_message::show('�ֻ����Ź������óɹ���',M_REFERER);
    }

	
}

/*
<script type='text/javascript'>
function warnLink(){
  var ul = document.getElementsByTagName('ul'); 
  ul[0].innerHTML += '<li><a href="?entry=sms_admin&action=current"><span class="cDRed">����</span></a></li>'; 
}
if($defact=='apiwarn') echo 'warnLink()';
</script>
*/

?>
