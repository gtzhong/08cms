<?
(!defined('M_COM') || !defined('M_ADMIN')) && exit('No Permission');
aheader();
$currencys = cls_cache::Read('currencys');
$pmodearr = array('0' => '����֧��','1' => '����֧��','2' => '����ת��','3' => '�ʾֻ��');
$poids = _08_factory::getInstance(_08_Loader::MODEL_PREFIX . 'PayGate_Pays')->getPays();
if($action == 'paysedit'){
	backnav('cysave','pays');
	if($re = $curuser->NoBackFunc('pay')) cls_message::show($re);
	$page = !empty($page) ? max(1, intval($page)) : 1;
	submitcheck('bfilter') && $page = 1;
	$viewdetail = empty($viewdetail) ? '' : $viewdetail;
	$pmode = isset($pmode) ? $pmode : '-1';
	$receive = isset($receive) ? $receive : '-1';
	$trans = isset($trans) ? $trans : '-1';
	$poid = empty($poid) ? '' : $poid;
	$mname = empty($mname) ? '' : $mname;
	$indays = empty($indays) ? 0 : max(0,intval($indays));
	$outdays = empty($outdays) ? 0 : max(0,intval($outdays));

	$filterstr = '';
	foreach(array('pmode','trans','receive','poid','mname','indays','outdays') as $k){
		$filterstr .= "&$k=".rawurlencode(stripslashes($$k));
	}

	$wheresql = '';
	if($pmode != '-1') $wheresql .= ($wheresql ? " AND " : "")."pmode='$pmode'";
	if($receive != '-1') $wheresql .= ($wheresql ? " AND " : "")."receivedate".($receive ? '>' : '=')."0";
	if($trans != '-1') $wheresql .= ($wheresql ? " AND " : "")."transdate".($trans ? '>' : '=')."0";
	if(!empty($poid)) $wheresql .= ($wheresql ? " AND " : "")."poid='$poid'";
	if(!empty($mname)) $wheresql .= ($wheresql ? " AND " : "")."mname ".sqlkw($mname);
	if(!empty($indays)) $wheresql .= ($wheresql ? " AND " : "")."senddate>'".($timestamp - 86400 * $indays)."'";
	if(!empty($outdays)) $wheresql .= ($wheresql ? " AND " : "")."senddate<'".($timestamp - 86400 * $outdays)."'";
	$wheresql = $wheresql ? "WHERE $wheresql" : '';

	if(!submitcheck('barcsedit')){
		$pmodearr = array('-1' => '֧����ʽ') + $pmodearr;
		$receivearr = array('-1' => '����״̬','0' => 'δ����','1' => '�ѵ���');
		$transarr = array('-1' => '��ֵ״̬','0' => 'δ��ֵ','1' => '�ѳ�ֵ');
		$poidsarr = array('' => '֧���ӿ�') + $poids;
		echo form_str($actionid.'arcsedit',"?entry=pays&action=paysedit&page=$page");
		tabheader_e();
		echo "<tr><td colspan=\"2\" class=\"txt txtleft\">";
		echo "<input class=\"text\" name=\"mname\" type=\"text\" value=\"$mname\" size=\"8\" style=\"vertical-align: middle;\" title=\"����֧����Ա\">&nbsp; ";
		echo "<select style=\"vertical-align: middle;\" name=\"receive\">".makeoption($receivearr,$receive)."</select>&nbsp; ";
		echo "<select style=\"vertical-align: middle;\" name=\"trans\">".makeoption($transarr,$trans)."</select>&nbsp; ";
		echo "<select style=\"vertical-align: middle;\" name=\"pmode\">".makeoption($pmodearr,$pmode)."</select>&nbsp; ";
		echo "<select style=\"vertical-align: middle;\" name=\"poid\">".makeoption($poidsarr,$poid)."</select>&nbsp; ";
		echo "<input class=\"text\" name=\"outdays\" type=\"text\" value=\"$outdays\" size=\"4\" style=\"vertical-align: middle;\">��ǰ&nbsp; ";
		echo "<input class=\"text\" name=\"indays\" type=\"text\" value=\"$indays\" size=\"4\" style=\"vertical-align: middle;\">����&nbsp; ";
		echo strbutton('bfilter','ɸѡ');
		tabfooter();
		
		$pagetmp = $page;
		do{
			$query = $db->query("SELECT * FROM {$tblprefix}pays $wheresql ORDER BY pid DESC LIMIT ".(($pagetmp - 1) * $atpp).",$atpp");
			$pagetmp--;
		} while(!$db->num_rows($query) && $pagetmp);
		$stritem = '';
		while($item = $db->fetch_array($query)){
			$pid = $item['pid'];
			$pmodestr = $pmodearr[$item['pmode']];
			$poidstr = empty($item['poid']) ? '-' : @$poids[$item['poid']];
			$sendstr = date("$dateformat",$item['senddate']);
			$receivestr = empty($item['receivedate']) ? '-' : date("$dateformat",$item['receivedate']);
			$transstr = empty($item['transdate']) ? '-' : date("$dateformat",$item['transdate']);
			$stritem .= "<tr class=\"txt\"><td class=\"txtC w40\"><input class=\"checkbox\" type=\"checkbox\" name=\"selectid[$pid]\" value=\"$pid\"></td>\n".
				"<td class=\"txtL\">$item[mname]</td>\n".
				"<td class=\"txtC w80\">$item[amount]</td>\n".
				"<td class=\"txtC w80\">$pmodestr</td>\n".
				"<td class=\"txtC w80\">$poidstr</td>\n".
				"<td class=\"txtC w80\">$sendstr</td>\n".
				"<td class=\"txtC w80\">$receivestr</td>\n".
				"<td class=\"txtC w80\">$transstr</td>\n".
				"<td class=\"txtC w40\"><a href=\"?entry=pays&action=paydetail&pid=$pid\" onclick=\"return floatwin('open_pays',this)\">�鿴</a></td></tr>\n";
		}
		$counts = $db->result_one("SELECT count(*) FROM {$tblprefix}pays $wheresql");
		$multi = multi($counts, $atpp, $page, "?entry=pays&action=paysedit$filterstr");
		
		tabheader('��Ա֧������ &nbsp;>><a href="?entry=mconfigs&action=cfpay&isframe=1" target="_blank">֧������</a>','','',9);
		trcategory(array("<input class=\"checkbox\" type=\"checkbox\" name=\"chkall\" onclick=\"checkall(this.form, 'selectid', 'chkall')\">",array('֧����Ա','txtL'),'֧������','֧��ģʽ','֧���ӿ�','��¼����','��������','��ֵ����','����'));
		echo $stritem;
		tabfooter();
		echo $multi;
		
		$receivearr = array('0' => 'δ����','1' => '�ѵ���');
		tabheader('������Ŀ');
		trbasic("<input class=\"checkbox\" type=\"checkbox\" name=\"arcdeal[delete]\" value=\"1\">&nbsp;ɾ��֧����¼",'','��δ���˻��ѳ�ֵ��֧����¼����ɾ��','');
		trbasic("<input class=\"checkbox\" type=\"checkbox\" name=\"arcdeal[receive]\" value=\"1\">&nbsp;���õ���״̬",'arcreceive',makeradio('arcreceive',$receivearr,1),'');
		trbasic("<input class=\"checkbox\" type=\"checkbox\" name=\"arcdeal[trans]\" value=\"1\">&nbsp;Ϊ��Ա�ֽ��ʻ���ֵ",'','֧�����ʲ��ܳ�ֵ','');
		tabfooter('barcsedit');
	}else{
		if(empty($arcdeal)) cls_message::show('��ѡ�������Ŀ',"?entry=pays&action=paysedit&page=$page$filterstr");
		if(empty($selectid)) cls_message::show('��ѡ��֧����¼',"?entry=pays&action=paysedit&page=$page$filterstr");
		if(!empty($arcdeal['delete'])){
			$db->query("DELETE FROM {$tblprefix}pays WHERE pid ".multi_str($selectid)." AND (receivedate=0 OR transdate>0)",'SILENT');
		}else{
			if(!empty($arcdeal['receive'])){
				$db->query("UPDATE {$tblprefix}pays SET receivedate='".(empty($arcreceive) ? 0 : $timestamp)."' WHERE pid ".multi_str($selectid)." AND transdate=0",'SILENT');
			}
			if(!empty($arcdeal['trans'])){
				$auser = new cls_userinfo;
				$query = $db->query("SELECT * FROM {$tblprefix}pays WHERE pid ".multi_str($selectid));
				while($item = $db->fetch_array($query)){
					if(!$item['amount'] || !$item['receivedate'] || $item['transdate']) continue;
					$auser->activeuser($item['mid']);
					$auser->updatecrids(array(0 => $item['amount']),1,'�ֽ��ֵ');
					$db->query("UPDATE {$tblprefix}pays SET transdate='$timestamp' WHERE pid='$item[pid]'",'SILENT');
					$auser->init();
				}
				unset($actuser);
			}
		}
		adminlog('�ֽ��ֵ����','֧����ֵ�б�������');
		cls_message::show('�ֽ��ֵ����������',"?entry=pays&action=paysedit&page=$page$filterstr");
	}
}
elseif($action == 'paydetail' && $pid){
	if($re = $curuser->NoBackFunc('pay')) cls_message::show($re);
	$forward = empty($forward) ? M_REFERER : $forward;
	empty($pid) && cls_message::show('��ָ����ȷ��֧��',$forward);
	if(!$item = $db->fetch_one("SELECT * FROM {$tblprefix}pays WHERE pid=$pid")) cls_message::show('��ָ����ȷ��֧����¼',$forward);
	if(!submitcheck('bpaydetail')){
		if(!$item['transdate']){
			tabheader('֧����Ϣ�޸�','paydetail','?entry=pays&action=paydetail&pid='.$pid.'&forward='.rawurlencode($forward),2,1);
		}else{
			tabheader('֧����Ϣ�鿴');
		}
		trbasic('��Ա����','',$item['mname'],'');
		trbasic('֧��ģʽ','',$pmodearr[$item['pmode']],'');
		trbasic('֧������(�����)','itemnew[amount]',$item['amount']);
		trbasic('������(�����)','',$item['handfee'],'');
		trbasic('֧���ӿ�','',$item['poid'] ? @$poids[$item['poid']] : '-','');
		trbasic('֧��������','',$item['ordersn'] ? $item['ordersn'] : '-','');
		trbasic('��Ϣ����ʱ��','',date("$dateformat $timeformat",$item['senddate']),'');
		trbasic('�ֽ���ʱ��','',$item['receivedate'] ? date("$dateformat $timeformat",$item['receivedate']) : '-','');
		trbasic('���ֳ�ֵʱ��','',$item['transdate'] ? date("$dateformat $timeformat",$item['transdate']) : '-','');
		trbasic('��ϵ������','itemnew[truename]',$item['truename']);
		trbasic('��ϵ�绰','itemnew[telephone]',$item['telephone']);
		trbasic('��ϵEmail','itemnew[email]',$item['email']);
		trbasic('��ע','itemnew[remark]',$item['remark'],'textarea');
		trspecial('֧��ƾ֤'."&nbsp; &nbsp; ["."<a href=\"".$item['warrant']."\" target=\"_blank\">".'��ͼ'."</a>"."]",specialarr(array('type' => 'image','varname' => 'itemnew[warrant]','value' => $item['warrant'],)));
		if($item['transdate']){
			tabfooter();
			echo "<input class=\"button\" type=\"submit\" name=\"\" value=\"����\" onclick=\"history.go(-1);\">";
		}else{
			tabfooter('bpaydetail','�޸�');
		}
		a_guide('paydetail');
	}else{
		$itemnew['amount'] = max(0,round(floatval($itemnew['amount']),2));
		empty($itemnew['amount']) && cls_message::show('������֧������',M_REFERER);
		$itemnew['truename'] = trim(strip_tags($itemnew['truename']));
		$itemnew['telephone'] = trim(strip_tags($itemnew['telephone']));
		$itemnew['email'] = trim(strip_tags($itemnew['email']));
		$itemnew['remark'] = trim($itemnew['remark']);
		$c_upload = cls_upload::OneInstance();	
		$itemnew['warrant'] = upload_s($itemnew['warrant'],$item['warrant'],'image');
		$c_upload->closure(1, $pid, 'pays');
		$c_upload->saveuptotal(1);
		$db->query("UPDATE {$tblprefix}pays SET
					 amount='$itemnew[amount]',
					 truename='$itemnew[truename]',
					 telephone='$itemnew[telephone]',
					 email='$itemnew[email]',
					 remark='$itemnew[remark]',
					 warrant='$itemnew[warrant]' 
					 WHERE pid='$pid'
					 ");
		cls_message::show('֧����Ϣ�޸����',axaction(6,$forward));
	}
}