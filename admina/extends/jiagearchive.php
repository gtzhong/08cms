<?
//��λ���ڸ������в���
(!defined('M_COM') || !defined('M_ADMIN')) && exit('No Permission');
aheader();
backallow('normal') || cls_message::show('��û�е�ǰ��Ŀ�Ĺ���Ȩ�ޡ�');
($aid = max(0,intval($aid))) || cls_message::show('��ָ���ĵ���');
$cid = empty($cid) ? '' : $cid;
$isnew = empty($isnew) ? '0' : $isnew; //echo $isnew;
$arc = new cls_arcedit;
$arc->set_aid($aid,array('ch'=>1));
$chid = $arc->archive['chid'];
$zschids = array(
	'4'=>array('zu'=>2,'shou'=>3),
	'115'=>array('zu'=>119,'shou'=>117),
	'116'=>array('zu'=>120,'shou'=>118),
);
$channel = &$arc->channel;
$fields = cls_cache::Read('fields',$chid);
$action = empty($action) ? 'def' : $action;
$baseurl = "?entry=$entry$extend_str&aid=$aid"; //&action=$action
$react = empty($react) ? '' : $react; 
$reflag = ''; if($react=='This') $reflag='checked="checked"'; 
$acttab = array();

if($isnew){
	$acttab['def'] = '¥�̵�ǰ�۸�༭';
    $navstr = 'estate';
}else{
	$acttab['def'] = 'С����ǰ�۸�༭';
    $navstr = 'housing_estate';
}

$acttab['list'] = '��ʷ�۸��б�';

$acttab['edit'] = '��ʷ�۸�༭';
$actitm = " : ";
$actmow = " ";

foreach($acttab as $k => $v){
	if($k==$action) $actmow = $v; $actitm = " - ";//$action = empty($action) ? 'def' : $action;
}
$page = !empty($page) ? max(1, intval($page)) : 1;
$keyword = empty($keyword) ? '' : $keyword;
$djfrom = empty($djfrom) ? '' : $djfrom;
$djto = empty($djto) ? '' : $djto;
$chname = ($isnew==0) ? 'С��' : '¥��';

if((strstr("def,edit",$action))&&(!submitcheck('bsubmit'))){//�۸��޸�
    $action=='def' ? backnav($navstr,'price') : backnav($navstr.'_historical','list');
    tabheader("<font color=red>".$arc->archive['subject']."</font> - $actmow",'archivedetail',"$baseurl&action=$action&page=$page",2,1,1);
	trhidden('fmdata[caid]',$arc->archive['caid']);
	trhidden('action',$action);
	trhidden('aid',$aid);
	trhidden('cid',$cid);
	trhidden('isnew',$isnew);
	trhidden('page',$page);
	trhidden('keyword',$keyword);
	$subject_table = atbl($chid); 
	$a_field = new cls_field;
	$fix_fields = array();
	$fields = cls_cache::Read('field',$chid);

	if(strstr("edit",$action)){ // ��ʷ�۸��޸�
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
			if($action == 'def'){
			trbasic('��ʾ','','�ò����Ǳ༭¥����Ϣ����ļ۸񣬱༭����ʷ�۸񡱿�����ʷ�۸��б��������������Ҫ���֡�','',array('x_guide'=>'��ע��Ϣ'));
			}
			foreach(array('dj','jgjj','jdjj','bdsm') as $k){
				p_editfield(array('fn'=>$k,'a_field'=>$a_field,'fix_fields'=>$fix_fields,'chid'=>$chid,'arc'=>$arc));
			}		
		}else{ //С��
			
			//�ο�ֵ:С����Ӧ���ַ��۸�
			$ref = $db->fetch_one("SELECT AVG(dj) as dj,MAX(dj) as jgjj,MAX(dj) as jdjj FROM {$tblprefix}".atbl($zschids[$chid]['shou'])." WHERE pid3=$aid");
			if(!$ref['dj']) $ref = array('dj'=>'(��)','jgjj'=>'(��)','jdjj'=>'(��)');
			//ʵ��ֵ:С������������
			$rec = $db->fetch_one("SELECT  dj,  csjgz as jgjj,  csjdj as jdjj FROM {$tblprefix}".atbl($chid)." WHERE aid=$aid");
			
			trbasic('��ʾ','','С����ؼ۸�Ϊ��Ӧ��Դ��ƽ���۸�','',array('x_guide'=>'��ע��Ϣ'));
			trbasic('����','fmdata[dj]',$rec['dj'],'text',array('guide'=>"�ο�ֵ:$ref[dj]; ��λ�ǣ�Ԫ/M<sup>2</sup>������дΪ����"));
			trbasic('��߼�','fmdata[jgjj]',$rec['jgjj'],'text',array('guide'=>"�ο�ֵ:$ref[jgjj]"));
			trbasic('��ͼ�','fmdata[jdjj]',$rec['jdjj'],'text',array('guide'=>"�ο�ֵ:$ref[jdjj]"));
		}
        trbasic('���ʱ��','fmdata[createdate]',date('Y-m-d'),'calendar');
	}
	tabfooter('bsubmit');
	a_guide('archivedetail');

}else if(($action=='list')){//��ʷ�۸��б�
	
	$wheresql = "";
	$fromsql = "FROM {$tblprefix}housesrecords r";
	//��Ҫ���ǽ�ɫ����Ŀ����Ȩ��
	$wheresql .= " AND r.aid='$aid' AND isnew=$isnew ";	
	$wheresql = $wheresql ? 'WHERE '.substr($wheresql,5) : '';
	if($keyword){
		$timef = strtotime($keyword);
		if($timef) $wheresql .= " AND (r.createdate>='".$timef."' AND r.createdate<='".($timef+86400)."')";
		else $wheresql .= " AND (r.message LIKE '%".addslashes($keyword)."%'  )";
	}
    if($djfrom && $djto){
        $wheresql .= " AND (r.average>='".$djfrom."' AND r.average<='".($djto)."')";
    } elseif($djfrom=='' && $djto) {
        $wheresql .= " AND r.average<='".($djto)."'";
    }elseif($djfrom && $djto=='') {
        $wheresql .= " AND r.average>='".$djfrom."'";
    }
    $filterstr = '';
    foreach(array('keyword','djfrom','djto') as $k)$filterstr .= "&$k=".urlencode($$k);

    if(!submitcheck('bsubmit')){
        backnav($navstr,'list');

        echo form_str($actionid.'arcsedit',"$baseurl&action=$action&page=$page&isnew=$isnew");
		//ĳЩ�̶�ҳ�����
		trhidden('aid',$aid);	
		trhidden('isnew',$isnew);
		trhidden('action',$action);		
		tabheader_e();
		echo "<tr><td colspan=\"2\" class=\"txt txtleft\">";
		echo "(��¼ʱ���䶯˵��)�ؼ���&nbsp; <input class=\"text\" name=\"keyword\" type=\"text\" value=\"$keyword\" size=\"8\" style=\"vertical-align: middle;\">&nbsp; ";
        echo '����&nbsp;<input type="text" class="txt1" title="��������ͼ۸�" name="djfrom" value="'.$djfrom.'" size="6"> - <input class="txt1" type="text" title="��������߼۸�" name="djto" value="'.$djto.'" size="6">Ԫ&nbsp;';

        echo strbutton('bfilter','ɸѡ');

		tabfooter();
		//�б���	
		tabheader("�����б�",'','',9);
		$cy_arr = array("<input class=\"checkbox\" type=\"checkbox\" name=\"chkall\" onclick=\"checkall(this.form, 'selectid', 'chkall')\">");
		$cy_arr[] = '��¼ʱ��';
		$cy_arr[] = '����';
		$cy_arr[] = '��߼�';
		$cy_arr[] = '��ͼ�';
		$cy_arr[] = '�۸�˵��';
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
			$editstr = "<a href=\"$baseurl&action=edit&page=$page&keyword=$keyword&cid=$r[cid]&isnew=$isnew\">�༭</a>";
			$itemstr .= "<tr class=\"txt\"><td class=\"txtC w40\" >$selectstr</td>\n";
			$itemstr .= "<td class=\"txtC\">".date('Y-m-d',$r['createdate'])."</td>\n";
			$itemstr .= "<td class=\"txtC\">$r[average]</td>\n";
			$itemstr .= "<td class=\"txtC\">$r[highest]</td>\n";
			$itemstr .= "<td class=\"txtC\">$r[lowest]</td>\n";
			$itemstr .= "<td class=\"txtC\">$msg</td>\n";
			$itemstr .= "<td class=\"txtC w35\">$editstr</td>\n";			
			$itemstr .= "</tr>\n";
		}
	
		$counts = $db->result_one("SELECT count(*) $fromsql $wheresql");
		$multi = multi($counts, $atpp, $page, "$baseurl&action=$action&isnew=$isnew".$filterstr);
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
		tabfooter('bsubmit');
		a_guide('archivesedit');		
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
		adminlog('��¼���¹���','��¼�б�������');
		cls_message::show('��¼�������',"$baseurl&action=$action&page=$page&keyword=$keyword&isnew=$isnew");
	}
	
}else if(submitcheck('bsubmit')){ 
	$setstr = "highest='$fmdata[jgjj]',average='$fmdata[dj]',lowest='$fmdata[jdjj]'";
	$setstr .= empty($fmdata['bdsm'])?'': ",message='$fmdata[bdsm]' ";//¥�̲��б䶯˵����С��û�б䶯˵��
	$setadd = "aid=$aid,isnew=$isnew";	
	if(strstr("edit",$action)){ 		
		$db->query("UPDATE {$tblprefix}housesrecords SET $setstr,createdate='".strtotime($fmdata['createdate'])."' WHERE cid=$cid");
		adminlog('�۸�-�޸ļ�¼');
		cls_message::show('��¼�༭���',axaction(6,M_REFERER));
	}else{
		if($action=='def'){ // �۸�༭ 
			$_the_recent_price  = $db->result_one("SELECT average FROM {$tblprefix}housesrecords WHERE aid='$aid' ORDER BY cid DESC");
			$_the_price_diff = $fmdata['dj'] - $_the_recent_price;//�����ύ����������������µ���ʷ�۸���ȣ��ж����ۻ��ǽ���
			$_price_trend = 0;
			if($_the_price_diff >0)$_price_trend = 1;//����
			if($_the_price_diff <0)$_price_trend = 2;//����
			if($isnew==1){ //¥��
				$db->query("UPDATE {$tblprefix}".atbl($chid)." SET dj='$fmdata[dj]' WHERE aid=$aid");
				$db->query("UPDATE {$tblprefix}archives_$chid SET jgjj='$fmdata[jgjj]',jdjj='$fmdata[jdjj]',bdsm='$fmdata[bdsm]' WHERE aid=$aid");				
			}else{				
				$db->query("UPDATE {$tblprefix}".atbl($chid)." SET csjgz='$fmdata[jgjj]',dj='$fmdata[dj]',csjdj='$fmdata[jdjj]' WHERE aid=$aid");
			}
			//�޸�¥�̱��۸������ֶ�
			$db->query("UPDATE {$tblprefix}".atbl($chid)." SET price_trend = '$_price_trend' WHERE aid=$aid");
			adminlog('�۸�-�޸ļ�¼');
			//���Ӽ�¼����ʷ�۸�           
			$db->query("INSERT INTO {$tblprefix}housesrecords SET $setadd,$setstr,createdate='".strtotime($fmdata['createdate'])."'");
		
			$arc->auto();
			$arc->updatedb();
			$arc->autostatic();
		}
	}
    cls_message::show('��¼�༭���',axaction(2,M_REFERER)); 
}
?>
