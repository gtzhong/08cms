<?
!defined('M_COM') && exit('No Permission');
($aid = max(0,intval($aid))) || cls_message::show('��ָ���ĵ���');

$mchid = $curuser->info['mchid'];
$infloat = empty($infloat) ? '0' : $infloat;

$sql_ids = "SELECT CONCAT(loupan,',',xiezilou,',',shaopu) as lpids FROM {$tblprefix}members_$mchid WHERE mid='$memberid'"; 
$lpids = $db->result_one($sql_ids); //echo $sql_ids.":$lpids<BR>$oA->aid";
if(empty($lpids)) $lpids = 0;
if(!strstr(",$lpids,",','.$aid.',')) $oA->message('�Բ�����û��Ȩ�޹����¥�̡�');

$cid = empty($cid) ? '' : $cid;
$isnew = empty($isnew) ? '0' : $isnew;
$arc = new cls_arcedit;
$arc->set_aid($aid,array('ch'=>1));
$chid = $arc->archive['chid'];
$channel = &$arc->channel;
$fields = cls_cache::Read('fields',$chid);
$action2 = empty($action2) ? 'def' : $action2;
$page = !empty($page) ? max(1, intval($page)) : 1;
$keyword = empty($keyword) ? '' : $keyword;
$baseurl = "?action=$action&aid=$aid&infloat=$infloat"; 
$react = empty($react) ? '' : $react; 
$reflag = ''; if($react=='This') $reflag='checked="checked"'; 
$atpp = 10;

$acttab = array();
if($isnew){
	$acttab['def'] = '¥�̼۸�༭';
}else{
	$acttab['def'] = 'С���۸�༭';
}
$acttab['list'] = '��ʷ�۸��б�';

$acttab['edit'] = '��ʷ�۸��޸�';

$actdiv = ''; $actitm = " : "; $actmow = " ";
foreach($acttab as $k => $v){
  	if($k!='edit') $actdiv .= "$actitm<a href='$baseurl&action2=$k&isnew=$isnew' style=\"".($action2 == $k?"color:red;":'')."\">$v</a>";
	if($k==$action2) $actmow = $v; $actitm = " - ";
}
$actdiv = "<div style='width:350px;float:right;'>ѡ�����$actdiv</div>";
$chname = ($isnew==0) ? 'С��' : '¥��';

if((strstr("def,edit",$action2))&&(!submitcheck('bsubmit'))){
	tabheader("$actdiv$chname - <font color='red'>".$arc->archive['subject']."</font> - $actmow",'archivedetail',"$baseurl&action2=$action2&page=$page&keyword=$keyword",2,1,1);
	trhidden('fmdata[caid]',$arc->archive['caid']);
	trhidden('action',$action2);
	trhidden('aid',$aid);
	trhidden('cid',$cid);
	trhidden('isnew',$isnew);
	trhidden('page',$page);
	trhidden('keyword',$keyword);
	$subject_table = atbl($chid); 
	$a_field = new cls_field;
	$fix_fields = array();
	$fields = cls_cache::Read('field',$chid);
	if(strstr("edit",$action2)){ // ��ʷ�۸��޸�
		$farr = array('dj','jgjj','jdjj','bdsm');
		$rec = $db->fetch_one("SELECT average as dj,highest as jgjj,lowest as jdjj,message as bdsm,createdate FROM {$tblprefix}housesrecords WHERE cid=$cid");
		for($i=0;$i<count($farr);$i++){
			$fn = $farr[$i];
			$fix_fields[] = $fn;
			if(($field = cls_cache::Read('field',$chid,$fn)) && $field['available']){
				$a_field->init($field,$rec[$fn]);
				$a_field->trfield('fmdata');
			}
		}
		trbasic('���ʱ��','fmdata[createdate]',date('Y-m-d',$rec['createdate']),'calendar');
	}else{ // ��ʷ�۸�༭/����
		if($isnew==1){ //¥��
			if($action2 == 'def'){
			trbasic('��ʾ','','�ò����Ǳ༭¥����Ϣ����ļ۸񣬱༭����ʷ�۸񡱿�����ʷ�۸��б��������������Ҫ���֡�','',array('x_guide'=>'��ע��Ϣ'));
			}
			
			foreach(array('dj','jgjj','jdjj','bdsm') as $k){
				p_editfield(array('fn'=>$k,'a_field'=>$a_field,'fix_fields'=>$fix_fields,'chid'=>$chid,'arc'=>$arc));
			}
			trbasic('��ʷ�۸�','','<input name="history" type="radio" value="Skip" />��������ʷ�۸� &nbsp; <input name="history" type="radio" value="Add" checked="checked"/>���ӵ���ʷ�۸�','');
			trbasic('���ش���','','<input name="react" type="radio" value="List" checked="checked"/>������ʷ�۸��б� &nbsp; <input name="react" type="radio" value="Close" $reflag/>����Ĭ��ҳ','');
		}else{ //С��
			trbasic('��ʾ','','С����ؼ۸�Ϊ��Ӧ��Դ��ƽ���۸�','',array('x_guide'=>'��ע��Ϣ'));
			//�ο�ֵ:С����Ӧ���ַ��۸�
			$ref = $db->fetch_one("SELECT AVG(dj) as dj,MAX(dj) as jgjj,MAX(dj) as jdjj FROM {$tblprefix}".atbl(3)." WHERE pid3=$aid");
			if(!$ref['dj']) $ref = array('dj'=>'(��)','jgjj'=>'(��)','jdjj'=>'(��)');
			//ʵ��ֵ:С������������
			$rec = $db->fetch_one("SELECT   cspjj as dj,  csjgz as jgjj,  csjdj as jdjj FROM {$tblprefix}".atbl(4)." WHERE aid=$aid");

			trbasic('����','fmdata[dj]',$rec['dj'],'text',array('guide'=>"�ο�ֵ:$ref[dj]; ��λ�ǣ�Ԫ/M<sup>2</sup>������дΪ����"));
			trbasic('��߾���','fmdata[jgjj]',$rec['jgjj'],'text',array('guide'=>"�ο�ֵ:$ref[jgjj]"));
			trbasic('��;���','fmdata[jdjj]',$rec['jdjj'],'text',array('guide'=>"�ο�ֵ:$ref[jdjj]"));
		
			trbasic('��ʷ�۸�','','<input name="history" type="radio" value="Skip" />��������ʷ�۸� &nbsp; <input name="history" type="radio" value="Add" checked="checked"/>���ӵ���ʷ�۸�','');
			
			trbasic('���ش���','','<input name="react" type="radio" value="List" checked="checked"/>������ʷ�۸��б� &nbsp; <input name="react" type="radio" value="Close" $reflag/>����Ĭ��ҳ','');
		}
	}
	tabfooter('bsubmit');

}else if($action2=='list'){
	
	$wheresql = "";
	$fromsql = "FROM {$tblprefix}housesrecords r";
	//��Ҫ���ǽ�ɫ����Ŀ����Ȩ��
	$wheresql .= " AND r.aid='$aid' AND isnew=$isnew ";	
	$wheresql = $wheresql ? 'WHERE '.substr($wheresql,5) : '';
	if($keyword){ 
		$wheresql .= " AND (r.message LIKE '%".addslashes($keyword)."%'  )";
	}
	$filterstr = '';
	
	if(!submitcheck('ybsubmit')){		
		echo form_str('action2id'.'arcsedit',"$baseurl&action2=list&page=$page&isnew=$isnew");
		//ĳЩ�̶�ҳ�����
		trhidden('aid',$aid);	
		trhidden('isnew',$isnew);
		trhidden('action',$action2);		
		tabheader_e();
		echo "<tr><td colspan=\"2\" class=\"txt txtleft\">";
		echo "(��¼ʱ���䶯˵��)�ؼ���&nbsp; <input class=\"text\" name=\"keyword\" type=\"text\" value=\"$keyword\" size=\"8\" style=\"vertical-align: middle;\">&nbsp; ";
	
		echo strbutton('bfilter','ɸѡ');

		tabfooter();
		//�б���	
		tabheader($actdiv."�����б�",'','',9);
		$cy_arr = array("<input class=\"checkbox\" type=\"checkbox\" name=\"chkall\" onclick=\"checkall(this.form, 'selectid', 'chkall')\">");
		$cy_arr[] = '��¼ʱ��';
		$cy_arr[] = '����';
		$cy_arr[] = '��߾���';
		$cy_arr[] = '��;���';
		$cy_arr[] = '�䶯˵��';
		$cy_arr[] = '�޸�';
		trcategory($cy_arr);
	
		$pagetmp = $page; 
		do{
			$query = $db->query("SELECT r.* $fromsql $wheresql ORDER BY r.createdate DESC LIMIT ".(($pagetmp - 1) * $atpp).",$atpp");
			$pagetmp--;
		} while(!$db->num_rows($query) && $pagetmp);
	
		$itemstr = '';
		while($r = $db->fetch_array($query)){
			$selectstr = "<input class=\"checkbox\" type=\"checkbox\" name=\"selectid[$r[cid]]\" value=\"$r[cid]\">";
			$msg = cls_string::CutStr($r['message'],36);
			$editstr = "<a href=\"$baseurl&action2=edit&page=$page&keyword=$keyword&cid=$r[cid]&isnew=$isnew\" onclick=\"return floatwin('open_arcexit',this)\">����</a>";
			$itemstr .= "<tr class=\"txt\"  style=\"text-align:center;\"><td class=\"txtC w40\" >$selectstr</td>\n";
			$itemstr .= "<td class=\"txtC\">".date('Y-m-d',$r['createdate'])."</td>\n";
			$itemstr .= "<td class=\"txtC\">$r[average]</td>\n";
			$itemstr .= "<td class=\"txtC\">$r[highest]</td>\n";
			$itemstr .= "<td class=\"txtC\">$r[lowest]</td>\n";
			$itemstr .= "<td class=\"txtC\">$msg</td>\n";
			$itemstr .= "<td class=\"txtC w35\">$editstr</td>\n";			
			$itemstr .= "</tr>\n";
		}
	
		$counts = $db->result_one("SELECT count(*) $fromsql $wheresql");
		$multi = multi($counts, $atpp, $page, "$baseurl&action2=$action2&keyword=$keyword&isnew=$isnew");
		echo $itemstr;
		tabfooter();
		echo $multi;
	
		//������
		tabheader('������Ŀ');
		$s_arr = array();
		$s_arr['delete'] = 'ɾ��';
		if($s_arr){
			$soperatestr = '';
			$i = 1;
			foreach($s_arr as $k => $v){
				$soperatestr .= "<input class=\"checkbox\" type=\"checkbox\" name=\"arcdeal[$k]\" value=\"1\"".($k=='delete'?' onclick="deltip()"':'').">$v &nbsp;";
				if(!($i % 5)) $soperatestr .= '<br>';
				$i ++;
			}
			trbasic('ѡ�������Ŀ','',$soperatestr,'');
		}
		tabfooter('ybsubmit');
	}else{
		if(empty($arcdeal) && empty($albumsnew)) cls_message::show('��ѡ�������Ŀ',axaction(0,M_REFERER));
		if(empty($selectid) && empty($albumsnew)) cls_message::show('��ѡ���¼',axaction(0,M_REFERER));

		if(!empty($selectid)){
			foreach($selectid as $cid){
				$db->query("DELETE FROM {$tblprefix}housesrecords WHERE cid=$cid");
			}
		}

		$arc->auto();
		$arc->updatedb();
		$arc->autostatic();
		cls_message::show('��¼�������',"$baseurl&action2=$action2&page=$page&keyword=$keyword&isnew=$isnew");
	}
	
}else if(submitcheck('bsubmit')){ 

	$setstr = "highest='$fmdata[jgjj]',average='$fmdata[dj]',lowest='$fmdata[jdjj]',message='$fmdata[bdsm]'";
	$setadd = "aid=$aid,isnew=$isnew";
	if($action2 == 'edit'){ 	
		$db->query("UPDATE {$tblprefix}housesrecords SET $setstr,createdate='$timestamp' WHERE cid=$cid");
		cls_message::show('��¼�༭���',axaction(6,M_REFERER));
	}else if($action2=='def'){ // �۸�༭ 
		$_the_recent_price  = $db->result_one("SELECT average FROM {$tblprefix}housesrecords WHERE aid='$aid' ORDER BY cid DESC");
		$_the_price_diff = $fmdata['dj'] - $_the_recent_price;//�����ύ����������������µ���ʷ�۸���ȣ��ж����ۻ��ǽ���
		$_price_trend = 0;
		if($_the_price_diff >0)$_price_trend = 1;//����
		if($_the_price_diff <0)$_price_trend = -1;//����
		
		if($isnew==1){ //¥��
			$db->query("UPDATE {$tblprefix}".atbl(4)." SET dj='$fmdata[dj]' WHERE aid=$aid");
			$db->query("UPDATE {$tblprefix}archives_$chid SET jgjj='$fmdata[jgjj]',jdjj='$fmdata[jdjj]',bdsm='$fmdata[bdsm]' WHERE aid=$aid");
		}else{
			$db->query("UPDATE {$tblprefix}".atbl(4)." SET csjgz='$fmdata[jgjj]',cspjj='$fmdata[dj]',csjdj='$fmdata[jdjj]' WHERE aid=$aid");
		}	
		
		//�޸�¥�̱��۸������ֶ�
		$db->query("UPDATE {$tblprefix}".atbl(4)." SET price_trend = '$_price_trend' WHERE aid=$aid");

		if($history=='Add'){ 				
			$db->query("INSERT INTO {$tblprefix}housesrecords SET $setadd,$setstr,createdate='$timestamp'");
		}
		$arc->auto();
		$arc->updatedb();
		$arc->autostatic();
	}

	if($react=='Close'){		
		cls_message::show('��¼�༭���',axaction(6,M_REFERER)); 
	}else{
		cls_message::show('��¼�������',"$baseurl&action2=list&isnew=$isnew");
	}

}
?>
