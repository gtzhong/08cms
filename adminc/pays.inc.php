<?PHP
!defined('M_COM') && exit('No Permission');
if($curuser->getTrusteeshipInfo()) cls_message::show('���Ǵ����û�����ǰ������ԭ�û�������Ȩ�ޣ�');
$currencys = cls_cache::Read('currencys');
$pay_fields = array('alipay' => 'alipay_v33', 'tenpay' => 'tenpay');
$pmodearr = array('0' => '����֧��','1' => '����֧��','2' => '����ת��','3' => '�ʾֻ��');
$poids = _08_factory::getInstance(_08_Loader::MODEL_PREFIX . 'PayGate_Pays')->getPays();
if(empty($pid)){
	backnav('payonline','record');
	$page = !empty($page) ? max(1, intval($page)) : 1;
	submitcheck('bfilter') && $page = 1;
	$pmode = isset($pmode) ? $pmode : '-1';
	$receive = isset($receive) ? $receive : '-1';
	$trans = isset($trans) ? $trans : '-1';
	$poid = empty($poid) ? '' : $poid;

	$wheresql = "WHERE mid=$memberid";
	if($pmode != '-1') $wheresql .= ($wheresql ? " AND " : "")."pmode='$pmode'";
	if($receive != '-1') $wheresql .= ($wheresql ? " AND " : "")."receivedate".($receive ? '>' : '=')."0";
	if($trans != '-1') $wheresql .= ($wheresql ? " AND " : "")."transdate".($trans ? '>' : '=')."0";
	if(!empty($poid)) $wheresql .= ($wheresql ? " AND " : "")."poid='$poid'";

	$filterstr = '';
	foreach(array('pmode','trans','receive','poid') as $k) $filterstr .= "&$k=".rawurlencode(stripslashes($$k));
	if(!submitcheck('barcsedit')){
		$pmodearr = array('-1' => '֧����ʽ') + $pmodearr;
		$receivearr = array('-1' => '����״̬','0' => 'δ����','1' => '�ѵ���');
		$transarr = array('-1' => '��ֵ״̬','0' => 'δ��ֵ','1' => '�ѳ�ֵ');
		$poidsarr = array('' => '֧���ӿ�') + $poids;
		echo form_str($action.'arcsedit',"?action=$action&page=&page");
		tabheader_e();
		echo "<tr><td class=\"item2\">";
		echo "<select style=\"vertical-align: middle;\" name=\"receive\">".makeoption($receivearr,$receive)."</select>&nbsp; ";
		echo "<select style=\"vertical-align: middle;\" name=\"trans\">".makeoption($transarr,$trans)."</select>&nbsp; ";
		echo "<select style=\"vertical-align: middle;\" name=\"pmode\">".makeoption($pmodearr,$pmode)."</select>&nbsp; ";
		echo "<select style=\"vertical-align: middle;\" name=\"poid\">".makeoption($poidsarr,$poid)."</select>&nbsp; ";
		echo strbutton('bfilter','ɸѡ').'</td></tr>';
		tabfooter();
		$pagetmp = $page;
		do{
			$query = $db->query("SELECT * FROM {$tblprefix}pays $wheresql ORDER BY pid DESC LIMIT ".(($pagetmp - 1) * $mrowpp).",$mrowpp");
			$pagetmp--;
		} while(!$db->num_rows($query) && $pagetmp);
		$stritem = '';
		while($item = $db->fetch_array($query)){
			$pid = $item['pid'];
            if ( array_key_exists($item['poid'], $poids) )
            {
                $item['pmode'] = 1;
            }
			$pmodestr = $pmodearr[$item['pmode']];
			$poidstr = empty($poids[$item['poid']]) ? '-' : $poids[$item['poid']];
			$sendstr = date("$dateformat",$item['senddate']);
			$receivestr = empty($item['receivedate']) ? '-' : date("$dateformat",$item['receivedate']);
			$transstr = empty($item['transdate']) ? '-' : date("$dateformat",$item['transdate']);
			$stritem .= "<tr><td class=\"item\" width=\"30\"><input class=\"checkbox\" type=\"checkbox\" name=\"selectid[$pid]\" value=\"$pid\"></td>\n".
				"<td class=\"item2\">$pmodestr</td>\n".
				"<td class=\"item\" width=\"80\">$item[amount]</td>\n".
				"<td class=\"item\" width=\"60\">$poidstr</td>\n".
				"<td class=\"item\" width=\"70\">$sendstr</td>\n".
				"<td class=\"item\" width=\"70\">$receivestr</td>\n".
				"<td class=\"item\" width=\"70\">$transstr</td>\n".
				"<td class=\"item\" width=\"30\"><a href=\"?action=pays&pid=$pid\" onclick=\"return floatwin('open_pays',this)\">�鿴</a></td></tr>\n";
		}
		$counts = $db->result_one("SELECT count(*) FROM {$tblprefix}pays $wheresql");
		$multi = multi($counts, $mrowpp, $page, "?action=pays$filterstr");

		tabheader('֧����¼�б�','','',9);
		trcategory(array("<input class=\"checkbox\" type=\"checkbox\" name=\"chkall\" onclick=\"checkall(this.form, 'selectid', 'chkall')\">",array('֧����ʽ','item2'),'֧������','֧���ӿ�','��¼����','��������','��ֵ����','����'));
		echo $stritem;
		tabfooter();
		echo $multi;
		tabfooter('barcsedit','ɾ��');
		m_guide("pay_notes",'fix');
	}else{
		empty($selectid) && cls_message::show('��ѡ��֧����¼',"?action=pays&page=$page$filterstr");
		$db->query("DELETE FROM {$tblprefix}pays WHERE mid=$memberid AND pid ".multi_str($selectid)." AND receivedate=0",'SILENT');
		cls_message::show('�ֽ��ֵ��Ϣɾ���ɹ�',"?action=pays&page=$page$filterstr");
	}
}else{
	$forward = empty($forward) ? M_REFERER : $forward;
	$pid = (int)$pid;
	empty($pid) && cls_message::show('��ָ����ȷ��֧��',$forward);
	if(!$item = $db->fetch_one("SELECT * FROM {$tblprefix}pays WHERE pid='$pid'")) cls_message::show('��ָ����ȷ��֧����¼');
	if(!submitcheck('bpaydetail')){
		if(!$item['transdate']){
			tabheader('�޸�֧����Ϣ','paydetail','?action=pays&pid='.$pid.'&forward='.rawurlencode($forward),2,1);
		}else{
			tabheader('�鿴֧����Ϣ');
		}
		trbasic('��Ա����','',$item['mname'],'');
		trbasic('֧����ʽ','',$pmodearr[$item['pmode']],'');
		trbasic('֧������(�����)','',$item['amount'],'');
		trbasic('������(�����)','',$item['handfee'],'');
		trbasic('֧���ӿ�','',$item['poid'] ? $poids[$item['poid']] : '-','');
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
		}else{
			tabfooter('bpaydetail','�޸�');
		}
		m_guide("pay_notes",'fix');
	}else{
		if($item['transdate']) cls_message::show('�ѳ�ֵ֧����Ϣ�����޸�');
		$itemnew['truename'] = trim(strip_tags($itemnew['truename']));
		$itemnew['telephone'] = trim(strip_tags($itemnew['telephone']));
		$itemnew['email'] = trim(strip_tags($itemnew['email']));
		$itemnew['remark'] = trim($itemnew['remark']);
		$c_upload = cls_upload::OneInstance();
		$itemnew['warrant'] = upload_s($itemnew['warrant'],$item['warrant'],'image');
		$c_upload->saveuptotal(1);
		$db->query("UPDATE {$tblprefix}pays SET
					 truename='$itemnew[truename]',
					 telephone='$itemnew[telephone]',
					 email='$itemnew[email]',
					 remark='$itemnew[remark]',
					 warrant='$itemnew[warrant]'
					 WHERE pid='$pid'
					 ");
		$c_upload->closure(1, $pid, 'pays');
		cls_message::show('֧����Ϣ�޸����',axaction(6,$forward));
	}
}
?>
