<?
!defined('M_COM') && exit('No Permission'); 
$sms = new cls_sms();
$sms_cfg_aset = $sms->cfga;
//$sms_cfg_api = $sms->api;
if($sms->isClosed()) cls_message::show('[�ֻ����Žӿ�]δ����!');
$balance = $sms->getBalance();
$balanceu = $curuser->info['sms_charge'];

$page = !empty($page) ? max(1, intval($page)) : 1; 
$checked = empty($checked) ? 0 : $checked; 
$section = empty($section) ? 'sendlog' : $section;
backnav('sms_member',$section);  

if($section=='sendlog'){ 

  $keyword = empty($keyword) ? '' : $keyword;
  $keytype = empty($keytype) ? 'tel' : $keytype;
  $filterstr = $checked?"&checked=$checked":'';
  foreach(array('keyword','keytype') as $k) $$k && $filterstr .= "&$k=".rawurlencode(stripslashes($$k));

  $selectsql = "SELECT * ";
  $wheresql = " WHERE mid='$memberid' "; //
  $fromsql = "FROM {$tblprefix}sms_sendlogs ";
    
  if($keyword){
	  if($keytype){
	  	$wheresql .= " AND ($keytype ".sqlkw($keyword).") ";
	  }else{
	  	$wheresql .= " AND (tel ".sqlkw($keyword)." OR msg ".sqlkw($keyword).") ";
	  }
  }
  
  if(!submitcheck('bsubmit')){ 
	  
	  echo form_str($action.'archivesedit',"?action=$action&page=$page&section=sendlog");
	  tabheader_e();
	  echo "<tr height=\"38\"><td class=\"txt txtleft\">";
	  echo "<select style=\"vertical-align: middle;\" name=\"keytype\">".makeoption(array('tel'=>'�绰','msg'=>'����','ip'=>'IP'),$keytype)."</select> ";
	  echo "&nbsp;<input class=\"text\" name=\"keyword\" type=\"text\" value=\"$keyword\" size=\"8\" style=\"vertical-align: middle;\" title=\"����\">&nbsp; ";
	  echo strbutton('bfilter','ɸѡ');
	  tabfooter();
	  tabheader('���ŷ��ͼ�¼','','',10);
	  //$cy_arr = array("<input class=\"checkbox\" type=\"checkbox\" name=\"chkall\" onclick=\"checkall(this.form, 'selectid', 'chkall')\">");
	  //$cy_arr[] = '����ʱ��';
	  $cy_arr[] = '&nbsp;���͵绰|L';
	  $cy_arr[] = '&nbsp;��Ϣ����|L';
	  //$cy_arr[] = '��Ա';
	  $cy_arr[] = '����ip/ʱ��';
	  $cy_arr[] = '���ͽ��';
	  //$cy_arr[] = '�ӿ���Ϣ';
	  
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
		  
		  $a = explode('/',$r['api']); $key = substr($a[0],4);
		  $api_u = empty($stype[$a[1]]) ? 'ϵͳ����' : $stype[$a[1]];
		  $res = (substr($r['res'],0,1)=='1' ? 'OK' : (substr($r['res'],0,2)=='-2' ? '<span style="color:#F0F">����</span>' : 'ʧ��')).'<br>'.$api_u; 
		  $itemstr .= "<td class=\"item2\" width='180'>$tel</td>\n";
		  $itemstr .= "<td class=\"item2\" width='360'>$msg</td>\n";
		  //$itemstr .= "<td class=\"txtC\">$r[mname]</td>\n";
		  $itemstr .= "<td class=\"item\">$r[ip]<br>$time</td>\n";
		  $itemstr .= "<td class=\"item\">$res</td>\n";
		  $itemstr .= "</tr>\n"; 
		  
	  }
	  echo $itemstr;
	  tabfooter();
	  echo multi($db->result_one("SELECT count(*) $fromsql $wheresql"),$mrowpp,$page, "?action=$action&page=$page&section=sendlog$filterstr");
	  m_guide("sms_mobile",'fix');
	  /*
	  tabheader('��������');
	  $str = "<input class=\"radio\" type=\"radio\" name=\"arcdeal_del\" value=\"now\">ɾ����¼ &nbsp;";
	  $str .= "<input class=\"radio\" type=\"radio\" name=\"arcdeal_del\" value=\"m3\">ɾ��3��ǰ &nbsp;";
	  $str .= "<input class=\"radio\" type=\"radio\" name=\"arcdeal_del\" value=\"m1\">ɾ��1��ǰ &nbsp;";
	  trbasic("<input class=\"checkbox\" type=\"checkbox\" name=\"arcdeal[delete]\" value=\"1\" onclick='deltip()'> ɾ������",'',$str,'');
	  //trbasic("<input class=\"checkbox\" type=\"checkbox\" name=\"arcdeal[checkf]\" value=\"1\"> �������",'checkv',makeoption(array('1'=>'�������','0'=>'��������')),'select');
	  tabfooter('bsubmit');
	  */
  }else{
	if(empty($arcdeal)) cls_message::show('��ѡ�������Ŀ��',"?entry=$entry&page=$page$filterstr");
	if(empty($arcdeal_del)) cls_message::show('��ѡ�������Ŀ��',"?entry=$entry&page=$page$filterstr");
	//echo "$arcdeal[delete],$arcdeal_del";
	if($arcdeal_del=='now'){
		if(empty($selectid)) cls_message::show('��ѡ���¼��',"?entry=$entry&page=$page$filterstr");
		foreach($selectid as $k){
			//$db->query("DELETE FROM {$tblprefix}sms_sendlogs WHERE cid='$k'",'UNBUFFERED');
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
 
}elseif($section=='chargelog'){
 
  //echo "xx";
  $keyword = empty($keyword) ? '' : $keyword; 
  $keytype = empty($keytype) ? 'cnt' : $keytype;
  $indays  = empty($indays)  ? 0 : max(0,intval($indays));
  $outdays = empty($outdays) ? 0 : max(0,intval($outdays));
  $filterstr = $checked?"&checked=$checked":'';
  foreach(array('keyword','keytype','section','indays','outdays') as $k) $$k && $filterstr .= "&$k=".rawurlencode(stripslashes($$k));

  $selectsql = "SELECT * ";
  $wheresql = " WHERE mid='$memberid' "; //
  $fromsql = "FROM {$tblprefix}sms_recharge ";
    
  if($keyword){
	  if($keytype){
	  	$wheresql .= " AND ($keytype ".sqlkw($keyword).") ";
	  }else{
	  	$wheresql .= " AND (tel ".sqlkw($keyword)." OR msg ".sqlkw($keyword).") ";
	  }
  }
  
  $indays && $wheresql .= " AND stamp>'".($timestamp - 86400*$indays)."'"; 
  $outdays && $wheresql .= " AND stamp<'".($timestamp - 86400*$outdays)."'"; 
  
  if(!submitcheck('bsubmit')){ 
	  
	  echo form_str('sendlogs',"?action=$action&page=$page&section=chargelog");
	  tabheader_e();
	  //tabheader("���ŷ���",'sendsms',"?action=$action&page=$page&section=chargelog",2,1,1);
	  echo "<tr height=\"38\"><td class=\"txt txtleft\">";
	  echo "<select style=\"vertical-align: middle;\" name=\"keytype\">".makeoption(array('cnt'=>'����','msg'=>'������',),$keytype)."</select> ";
	  echo "&nbsp;<input class=\"text\" name=\"keyword\" type=\"text\" value=\"$keyword\" size=\"8\" style=\"vertical-align: middle;\" title=\"�ؼ���\">&nbsp; ";
	  echo "<input class=\"text\" name=\"outdays\" type=\"text\" value=\"$outdays\" size=\"3\" style=\"vertical-align: middle;\">��ǰ&nbsp; ";
		echo "<input class=\"text\" name=\"indays\" type=\"text\" value=\"$indays\" size=\"3\" style=\"vertical-align: middle;\">����&nbsp; ";
	  echo strbutton('bfilter','ɸѡ');
	  tabfooter();
	  tabheader("���ų�ֵ��¼",'','',10);
	  //$cy_arr = array("<input class=\"checkbox\" type=\"checkbox\" name=\"chkall\" onclick=\"checkall(this.form, 'selectid', 'chkall')\">");
	  //$cy_arr[] = '����ʱ��';
	  $cy_arr[] = '��ԱID';
	  $cy_arr[] = '��Ա����';
	  $cy_arr[] = '��ֵ����';
	  $cy_arr[] = 'ʱ��';
	  $cy_arr[] = 'ip';
	  $cy_arr[] = '������';
	  $cy_arr[] = '��ע';
	  
	  trcategory($cy_arr);
  
	  $pagetmp = $page; //echo "$selectsql $fromsql $wheresql";
	  do{
		  $query = $db->query("$selectsql $fromsql $wheresql ORDER BY cid DESC LIMIT ".(($pagetmp - 1) * $mrowpp).",$mrowpp");
		  $pagetmp--;
	  } while(!$db->num_rows($query) && $pagetmp);
  
	  $itemstr = ''; 
	  while($r = $db->fetch_array($query)){
		  $selectstr = "<input class=\"checkbox\" type=\"checkbox\" name=\"selectid[$r[cid]]\" value=\"$r[cid]\">";
		  $time = date('Y-m-d H:i',$r['stamp']);
		  
		  $msg = mhtmlspecialchars($r['msg']);
		  $note = empty($r['note']) ? '-' : $r['note'];
		  $itemstr .= "<td class=\"item\">$r[mid]</td>\n";
		  $itemstr .= "<td class=\"item\">$r[mname]</td>\n";
		  $itemstr .= "<td class=\"item\">$r[cnt]</td>\n";
		  $itemstr .= "<td class=\"item\">$time</td>\n";
		  $itemstr .= "<td class=\"item\">$r[ip]</td>\n";
		  $itemstr .= "<td class=\"item2\">$r[msg]</td>\n";
		  $itemstr .= "<td class=\"item2\">$note</td>\n";
		  $itemstr .= "</tr>\n"; 
		  
	  }
	  echo $itemstr;
	  tabfooter();
	  echo multi($db->result_one("SELECT count(*) $fromsql $wheresql"),$mrowpp,$page, "?action=$action&page=$page&section=chargelog$filterstr");
	  m_guide("sms_mobile",'fix');
	  /*
	  tabheader('��������');
	  $str = "<input class=\"radio\" type=\"radio\" name=\"arcdeal_del\" value=\"now\">ɾ����¼ &nbsp;";
	  $str .= "<input class=\"radio\" type=\"radio\" name=\"arcdeal_del\" value=\"m3\">ɾ��3��ǰ &nbsp;";
	  $str .= "<input class=\"radio\" type=\"radio\" name=\"arcdeal_del\" value=\"m1\">ɾ��1��ǰ &nbsp;";
	  trbasic("<input class=\"checkbox\" type=\"checkbox\" name=\"arcdeal[delete]\" value=\"1\" onclick='deltip()'> ɾ������",'',$str,'');
	  //trbasic("<input class=\"checkbox\" type=\"checkbox\" name=\"arcdeal[checkf]\" value=\"1\"> �������",'checkv',makeoption(array('1'=>'�������','0'=>'��������')),'select');
	  tabfooter('bsubmit');
	  */
  }else{
	if(empty($arcdeal)) cls_message::show('��ѡ�������Ŀ��',"?entry=$entry&page=$page$filterstr");
	if(empty($arcdeal_del)) cls_message::show('��ѡ�������Ŀ��',"?entry=$entry&page=$page$filterstr");
	//echo "$arcdeal[delete],$arcdeal_del";
	if($arcdeal_del=='now'){
		if(empty($selectid)) cls_message::show('��ѡ���¼��',"?entry=$entry&page=$page$filterstr");
		foreach($selectid as $k){
			//$db->query("DELETE FROM {$tblprefix}sms_recharge WHERE cid='$k'",'UNBUFFERED');
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
  

}elseif($section=='balance'){
	
		//$curuser->info['currency0'] = 1; //����
		if(!submitcheck('bsubmit')){ 
			tabheader('�ֻ����ų�ֵ','gtexchagne',"?action=$action&section=$section",2,1,1);
			trbasic('���ж������','',$balanceu.' ��','');
			trbasic('�ֽ��ʻ����','',$curuser->info['currency0']."Ԫ &nbsp; &nbsp;<a href=\"?action=payonline\" target=\"_blank\">>>����֧��</a>",'');
			trbasic('���ŵ���','',$sms_cfg_price.' Ԫ/��','');
			
			$max1 = ceil($curuser->info['currency0']/$sms_cfg_price); 
			$max2 = min($max1,80000);
			if($max2<10){
				trbasic('�����ʾ','',"<div style='' class='validator_message warn'>�ֽ������ֵ10�����ţ�����ϵ[����Ա]��>>����֧����</div>",'');
				tabfooter('');
			}else{
				trbasic('<FONT color=red>*</FONT> ��ֵ����','',"<input class=\"input\" type=\"text\" id=\"sms_count\" name=\"sms_count\" value=\"0\" rule='int' must='1' regx='' min='10' max='$max2' rev='��ֵ����'>",'');
				tabfooter("bsubmit\" onclick=\"return sendReCheck('sms_count','��ֵ����','ȷ��ִ�г�ֵ?');\"");
			}
			m_guide("sms_mobile",'fix');

		}else{
			$sms_count = max(0,intval($sms_count));
			$sms_money = $sms_count*$sms_cfg_price;
			$cur_money = $curuser->info['currency0']-$sms_money;
			$cur_count = $curuser->info['sms_charge']+$sms_count;
			if($curuser->info['currency0'] < $sms_money) cls_message::show('�����ֽ��ʻ����㣬���ֵ��',M_REFERER);
			$curuser->updatefield("currency0",$cur_money);
			$curuser->updatefield("sms_charge",$cur_count);
			$curuser->updatedb();
			//echo "$cur_money,$cur_count";
			$sql = "INSERT INTO {$tblprefix}sms_recharge SET 
			  mid='$memberid',mname='{$curuser->info['mname']}',stamp='$timestamp',ip='$onlineip',
			  cnt='$sms_count',msg='��Ա������ֵ',note='��ǰ���:($cur_money)Ԫ'";
			$db->query($sql);
			cls_message::show('���ų�ֵ�ɹ���',M_REFERER);
		}
		


}elseif($section=='sendsms'){

  $apiarr = $sms->cfgs;
  $apimsg = "���:(".$balanceu."��)���ӿ�����:(".$apiarr['name'].")";
  $apimsg .= $apiarr['home'] ? "��<a href=\"".$apiarr['home']."\" target=\"_blank\">�ӿڹ���</a>" : '';
  
  m_guide("�ӿ���Ϣ : $apimsg",'tip');
  if(!submitcheck('bsubmit')){
	  
		tabheader("���ŷ���",'sendsms',"?action=$action&section=sendsms",2,1,1);
		//trbasic('�ӿ���Ϣ','',"$apimsg",'');
		trbasic("�ֻ�����",'fmdata[tel]','','textarea',array('w'=>360,'h'=>80,'validate'=>' rule="text" must="1" min="11" max="24000" rev="�ֻ�����" ','guide'=>'<br>һ��һ������,�ŷֿ����Զ��������·��š�(-)��<br>һ�����2000���͸��ֻ�����'));
		$curuser = cls_UserMain::CurUser(); 
		$hostname = isset($curuser->info['company']) ? $curuser->info['company'] : $curuser->info['mname'];//cls_env::mconfig('hostname'); //�ܶ�ӿ�Ҫ��ǩ��,�����Ĭ��ǩ��
		$cmsg = "<br />����[".$sms->cfg_mchar."]���֣���[".($sms->cfg_mchar-3)."]����ÿ���۷ѡ�[��ǰ������<span id='mcnt'>0</span>����]";
		$cmsg .= "<br />��Щ�ӿ�Ҫ���������Ҫ<a href='#' onClick='setMsgSign()'>�������ơ�[ĳ��˾]���򡱡�[����]����ǩ��</a>�ſɳɹ����ͣ������������Ա���磡";
		trbasic("��������",'fmdata[msg]','','textarea',array('w'=>360,'h'=>80,'validate'=>' rule="text" must="1" min="3" max="255" rev="��������" ','guide'=>'<br>һ�η���,���255���ַ����ڣ�<a href="#" onclick="setTestMsg()">[���Զ���]</a>'.$cmsg));
		$msg = ($apiarr['nmem'] ? $apiarr['nmem'].'<br>' : '')."";
		$msg && trbasic('�ӿ�˵��','',$msg,'');
		trhidden('section',$section);

		if($balanceu<=0){ 
			//�������ݲ���Ϊ��</div>
			trbasic('�����ʾ','',"<div style='' class='validator_message warn'>���㣡���ֵ��</div>",'');
			tabfooter('');
		}else{
			tabfooter('bsubmit'); 
		}
		echo "\r\n<script type='text/javascript'>
		var m=\$id('fmdata[msg]');m.onblur=function(){\$id('mcnt').innerHTML=m.value.length;}
		function setTestMsg(){m.value='sms��Ա����(".$curuser->info['mname'].");ʱ��(".date('H:i:s').");���:$balanceu($apiarr[unit]);\\n�ӿ�����[$apiarr[name]]';\$id('mcnt').innerHTML=m.value.length;}
		function setMsgSign(){m.value+=m.innerHTML+'[$hostname]';m.onblur();}
		</script>\n";
		m_guide("sms_mobile",'fix');
  }else{
		
		$msg = $sms->sendSMS($fmdata['tel'],$fmdata['msg']);
		if($msg[0]==1){
			$msg0 = "���ͳɹ�!";
		}else{
			$msg0 = "����ʧ��!";
		}
		$msg = $msg0.$msg[1];
		cls_message::show($msg,"?action=$action&section=sendsms");
 }
		
}

?>
