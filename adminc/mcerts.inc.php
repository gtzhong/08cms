<?php
!defined('M_COM') && exit('No Permission');
$sms = new cls_sms();
$mctypes = cls_cache::Read('mctypes');
$mchid = $curuser->info['mchid'];
$mfields = cls_cache::Read('mfields',$mchid);
isset($mctid ) && $mctid = (int)$mctid;
if(empty($deal)){
	$itemstr = '';
	$sn = 1;
	foreach($mctypes as $k => $v){
		if($v['available'] && in_array($mchid,explode(',',$v['mchids'])) && isset($mfields[$v['field']])){
			$statestr = '-';
			if($curuser->info["mctid$k"]){
				$statestr = '��֤����';
			}elseif($db->result_one("SELECT COUNT(*) FROM {$tblprefix}mcerts WHERE mctid='$k' AND mid='$memberid' AND checkdate=0")) $statestr = "������ &nbsp;<a href=\"?action=$action&deal=cancel&mctid=$k\">>>ȡ��</a>";
			$itemstr .= "<tr>\n".
				"<td class=\"item\" width=\"40\">$sn</td>\n".
				"<td class=\"item2\">$v[cname]</td>\n".
				"<td class=\"item\"><img src=\"$v[icon]\" border=\"0\" onload=\"if(this.height>20) {this.resized=true; this.height=20;}\" onmouseover=\"if(this.resized) this.style.cursor='pointer';\" onclick=\"if(!this.resized) {return false;} else {window.open(this.src);}\"></td>\n".
				"<td class=\"item2\">$v[remark]</td>\n".
				"<td class=\"item2\">$statestr</td>\n".
				"<td class=\"item\" width=\"40\"><a title='����/����/�鿴����' href=\"?action=$action&deal=detail&mctid=$k\" onclick=\"return floatwin('open_mcerts',this)\">����</a></td>\n".
				"</tr>\n";
			$sn ++;
		}
	}
	tabheader('�ҵ���֤����','','',10);
	if($itemstr){
		trcategory(array('���',array('����','left'),'ͼ��',array('��ע','left'),array('��֤״̬','left'),'����'));
		echo $itemstr;
	}else echo "<tr><td class=\"item\" colspan=\"10\"><br>û������Ҫ��֤����Ŀ��<br><br></td></tr>";
	tabfooter();
}elseif($deal == 'detail'){
	if(empty($mctid) || !($mctype = @$mctypes[$mctid])) cls_message::show('��ָ����֤���͡�');
	if(!$mctype['available'] || !in_array($mchid,explode(',',$mctype['mchids'])) || !isset($mfields[$mctype['field']])) cls_message::show('��Ч����֤���͡�');
	$curuser->detail_data();
	$mcfield = &$mfields[$mctype['field']];
	$flag = 0;$flagstr = '���ύ��֤���롣';
	if($curuser->info["mctid$mctid"]){
		$flag = 1;$flagstr = '��֤��ͨ����';
	}elseif($oldrow = $db->fetch_one("SELECT * FROM {$tblprefix}mcerts WHERE mctid='$mctid' AND mid='$memberid' AND checkdate=0")){
		$flag = 2;$flagstr = '��֤���������У���ȴ�����Ա��ˡ�';
	}
	if(!submitcheck('bsubmit')&&!submitcheck('buncheck')){
		tabheader("��Ա��֤ - $mctype[cname]", 'memcert_need', "?action=$action&deal=$deal&mctid=$mctid&t=$timestamp",2,1,1);
		trbasic('��֤״̬','',$flagstr,''); $jstag = 'script'; 
		echo "<$jstag type='text/javascript' src='{$cms_abs}include/sms/cer_code.js'></$jstag>";
		$a_field = new cls_field;
		$a_field->init($mcfield,$flag == 2 ? $oldrow['content'] : $curuser->info[$mctype['field']]);

		if(!$flag && $mctype['mode']==1){ //δ��֤
			if(!$sms->smsEnable($mctid)){ //�ر�-�ֻ����Žӿ�
				$msgcode = cls_string::Random(6, 1);
				msetcookie('08cms_msgcode', authcode("$timestamp\t$msgcode", 'ENCODE'));
				trhidden('msgcode',$msgcode);
			}else{
				$varname = "fmdata[$mctype[field]]"; 
				$inputstr = '<input type="text" size="10" id="msgcode" name="msgcode" rule="text" must="1" type="int" min="6" max="6" offset="1" rev="ȷ����"/>&nbsp;&nbsp;';
				$a_field->field['guide'] .=<<< EOT
<tr><td width="25%" class="item1"><b>ȷ����</b></td>
<td class="item2">$inputstr
<a id="tel_code" href="javascript:" onclick="sendCerCode('$varname','$mctid','tel_code');">��������ȷ���롿</a>
<a id="tel_code_rep" style="color:#CCC; display:none"><span id="tel_code_rep_in">60</span>������»�ȡ</a> 
<span id="alert_msgcode" style="color:red"></span>
<input name="is_check_code" type="hidden" value="1" />
</td></tr>
EOT;
			}
		}
		if($flag==1 && !$mctype['mode']){ //ֱ����ʾͼƬ
			$val = view_checkurl($curuser->info[$mctype['field']]);
			$val = '<a href="'.$val.'" target="_blank"><img src="'.$val.'" width="240" /></a>';
			echo "<tr><td width='150px' class='item1'><b>".$mfields[$mctype['field']]['cname']."</b></td><td>$val</td></tr>";
		}else{
			$a_field->trfield('fmdata');
		}
		if($flag==1&&empty($mctypes[$mctid]['autocheck'])&&!empty($mctypes[$mctid]['uncheck'])){ 
			tabfooter('buncheck','����');
		}elseif($flag==2){
			tabfooter('buncheck','ȡ������');
		}elseif(empty($flag)){
			tabfooter('bsubmit');
		}
		if(@$mctype['mode'] && @$mctype['isunique']){
			$paras = "&mctid=$mctid&mchid=".$curuser->info['mchid']."&oldval=".@$curuser->info[$mctype['field']]."&method=1&val=%1";
			echo "<$jstag type='text/javascript'>var ctel = \$id('fmdata[$mctype[field]]');</$jstag>";
			$ajaxURL = $cms_abs . _08_Http_Request::uri2MVC("ajax=checkUnique$paras");
			echo _08_HTML::AjaxCheckInput("fmdata[$mctype[field]]", $ajaxURL);
			#echo _08_HTML::AjaxCheckInput("fmdata[$mctype[field]]","{$cms_abs}tools/ajax.php?action=checkUnique$paras");
		}
		//tabfooter($flag ? 'buncheck' : 'bsubmit');
	}elseif(submitcheck('buncheck')){
		#���� 
		$curuser->updatefield("mctid$mctid",0); #$au->updatefield($mctype['field'],'',$a_field->field['tbl']);
		if($mctype['award'])$curuser->updatecrids(array($mctype['crid'] => -$mctype['award']),0,"$mctype[cname] �۷�");
		$curuser->updatedb();
		$db->query("DELETE FROM {$tblprefix}mcerts WHERE mctid='$mctid' AND mid='$memberid'");
		cls_message::show('������֤��ɡ�',axaction(6,"?action=$action"));
		//echo "uncheck!"; die('');
	}else{
		$c_upload = cls_upload::OneInstance();	
		$a_field = new cls_field;
		$a_field->init($mcfield,$flag == 2 ? $oldrow[$mctype['field']] : $curuser->info[$mctype['field']]);
		$content = $a_field->deal('fmdata','cls_message::show',M_REFERER);
		$msgcode = empty($msgcode) ? '' : trim($msgcode);
		$checkdate = 0;
		if(!empty($is_check_code)){
			@list($inittime, $initcode) = maddslashes(explode("\t", authcode($m_cookie['08cms_msgcode'],'DECODE')),1);
			if($timestamp - $inittime > 1800 || $initcode != $msgcode) cls_message::show('�ֻ�ȷ��������', M_REFERER);
			$mctype['autocheck'] = 1; //�ֻ�������֤,ǿ�����
		}
		$db->query("INSERT INTO {$tblprefix}mcerts SET mid='$memberid',mname='{$curuser->info['mname']}',mctid='$mctid',createdate='$timestamp',checkdate='$checkdate',content='$content',msgcode='$msgcode'");
		if($mcid = $db->insert_id()){
			$c_upload->closure(1,$mcid,"mcerts");
			$c_upload->saveuptotal(1);
			if($mctype['autocheck']){
				$curuser->updatefield($mctype['field'],$content,$a_field->field['tbl']);
				$curuser->updatefield("mctid$mctid",$mctid); //ֱ�����
				if($mctype['award']) $curuser->updatecrids(array($mctype['crid'] => $mctype['award']),0,"$mctype[cname] �ӷ�");
				$curuser->updatedb();
				$db->query("UPDATE {$tblprefix}mcerts SET checkdate='$timestamp',content='$content' WHERE mcid='$mcid'");
				cls_message::show('��֤�ɹ���',axaction(6,"?action=$action"));
			}else{
				cls_message::show('��֤����ɹ���',axaction(6,"?action=$action"));
			}
		}else{
			$c_upload->closure(1);
			cls_message::show('��֤���벻�ɹ���',axaction(6,"?action=$action"));
		}
	}
}elseif($deal == 'cancel'){
	if(empty($mctid)) cls_message::show('��Ҫɾ���������¼�����ڡ�');
	if($db->query("DELETE FROM {$tblprefix}mcerts WHERE mctid='$mctid' AND mid=$memberid AND checkdate=0")){
			cls_message::show('��֤����ɾ���ɹ�', M_REFERER);
	}else cls_message::show('��֤����ɾ��ʧ��', M_REFERER);
}
?>