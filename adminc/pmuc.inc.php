<?php
(defined('M_COM') && $enable_uc) || exit('No Permission');
/*
ֻ�ܷ��͵�����Ϣ�����Ͷ��ʧ�ܣ��������û������ܷ��������ֻ����id
*/
$page = isset($page) ? $page : 1;
$page = max(1, intval($page));

cls_ucenter::init();
list($uid,$username) = uc_get_user($curuser->info['mname']);
$boxs=array('newpm', 'privatepm', 'systempm', 'announcepm');
$boxl=array('δ������', '��Ա����', 'ϵͳ����', 'UC����');//��ӻ������
$new = uc_pm_checknew($uid, 4);
$new['privatepm'] = $new['newprivatepm'];
$new['systempm'] = $new['newpm'] - $new['privatepm'];
$action=='pmbox' && $box = !empty($box) && in_array($box, $boxs) ? $box : ($new['newpm'] ? 'newpm' : 'privatepm');
$l = count($boxs);
$urlsarr = array('pmsend' => array('���Ͷ���', '?action=pmsend'));
for($i = 0; $i < $l; $i++)$urlsarr[$boxs[$i]] = array($boxl[$i].($new[$boxs[$i]]?('('.$new[$boxs[$i]].')'):''), "?action=pmbox&box=$boxs[$i]&page=$page");
url_nav($urlsarr,'pmbox'==$action ? $box : 'pmsend',6);

if($action=='pmsend'){
	if(!submitcheck('bpmsend')){//���Ϳ�
		tabheader("���Ͷ���",'pmsend',"?action=pmsend&box=$box&page=$page",2,0,1);
		trbasic('����','pmnew[title]','','text', array('validate' => makesubmitstr('pmnew[title]',1,0,0,80),'w'=>50));
		trbasic('������','pmnew[tonames]',empty($tonames) ? '' : $tonames,'text', array('guide' => '�ö��ŷָ������Ա����','validate' => makesubmitstr('pmnew[tonames]',1,0,0,100),'w'=>50));
		trbasic('����','pmnew[content]','','textarea', array('w' => 500,'h' => 300,'validate' => makesubmitstr('pmnew[content]',1,0,0,1000)));
		tr_regcode('pm');
		tabfooter('bpmsend');
	}else{//���Ͷ���
		if(!regcode_pass('pm',empty($regcode) ? '' : trim($regcode))) cls_message::show('��֤�����',M_REFERER);
		$pmnew['title'] = trim($pmnew['title']);
		$pmnew['tonames'] = trim($pmnew['tonames']);
		$pmnew['content'] = trim($pmnew['content']);
		if(empty($pmnew['content']) || empty($pmnew['tonames'])){
			cls_message::show('�������ݲ�����',M_REFERER);
		}
		$tos=array_filter(explode(',',$pmnew['tonames']));$count=0;
		$pmnew['title'] = $pmnew['title'] ? $pmnew['title'] : ($pmnew['content'] ? $pmnew['content'] : '');
		foreach($tos as $to)if(uc_pm_send($uid,$to,$pmnew['title'],$pmnew['content'],1,0,1))$count++;
		$count ? cls_message::show($count.'���ŷ��ͳɹ�',"?action=pmbox&box=$box&page=$page") : cls_message::show('���ŷ��ʹ���',M_REFERER);
	}
}elseif(empty($fid)&&empty($pmid)){
	if(!submitcheck('bpmbox')){//���ռ���
			$ucpm = uc_pm_list($uid, $page, $mrowpp, 'inbox', $box, 30);
			tabheader("�����б�",'pmsedit',"?action=pmbox&box=$box&page=$page",6);
			trcategory(array($box=='announcepm'?'':("<input class=\"checkbox\" type=\"checkbox\" name=\"chkall\" class=\"category\" onclick=\"checkall(this.form, '', 'chkall')\">".'ɾ?'),array('����','left'),'������','״̬','��������','����'));
			if($ucpm['data']){
				foreach($ucpm['data'] as $pm){
					echo "<tr title=\"".mhtmlspecialchars($pm['message'])."\">\n<td align=\"left\" width=\"40\">".($box=='announcepm'?'':"<input class=\"checkbox\" type=\"checkbox\" name=\"".($pm['msgformid']?"fids[$pm[msgformid]]\" value=\"$pm[msgform]":"pmids[$pm[pmid]]\" value=\"$pm[pmid]").'">')."</td>\n".
						"<td class=\"item2\">".mhtmlspecialchars($pm['subject'])."</td>\n".
						"<td align=\"center\" width=\"120\">".($pm['msgfromid'] ? $pm['msgfrom'] : 'ϵͳ����')."</td>\n".
						"<td align=\"center\" width=\"40\">".($box=='announcepm'?'-':($pm['new'] ? 'δ��' : '�Ѷ�'))."</td>\n".
						"<td align=\"center\" width=\"80\">".date($dateformat, $pm['dateline'])."</td>\n".
						"<td align=\"center\" width=\"40\"><a href=\"?action=pmbox&box=$box&page=$page&".($pm['msgfromid']?"fid=$pm[msgfromid]":"pmid=$pm[pmid]")."\">".'�鿴'."</a></td></tr>\n";
				}
			}else{
				echo '<tr class="item2" height="50"><td align="center" colspan="6">'.'û�ж���'.'</td></tr>';
			}
			echo multi($ucpm['count'],$mrowpp,$page,"?action=pmbox");
			$box=='announcepm'?tabfooter():tabfooter('bpmbox','ɾ��');
	}else{//ɾ��
		empty($fids) && empty($pmids) && cls_message::show('��ѡ��ɾ����Ŀ',"?action=pmbox&box=$box&page=$page");
		is_array($fids) || $fids=array($fids);
		is_array($pmids) || $pmids=array($pmids);
		if($fids) {
			uc_pm_deleteuser($uid, $fids);
		}
		if($pmids) {
			uc_pm_delete($uid, 'inbox', $pmids);
		}
		cls_message::show('����Ϣɾ���������',"?action=pmbox&box=$box&page=$page");
	}
}else{//�Ķ�����
	$days = array(1=>'����',3=>'�������',4=>'����',5=>'����');
	$day = isset($day) && array_key_exists($day,$days) ? $day : 3;

	$ucpm = empty($fid) ? uc_pm_view($uid, $pmid, 0, $day) : uc_pm_view($uid, '', $fid, $day);//$ucpm=uc_pm_view($uid, $pmid, 0, 3);
//	exit(var_export($ucpm));
	empty($ucpm) && cls_message::show('û���¶���');
	$fuser = '';
	foreach($ucpm as $pm)if($pm['msgfrom']!=$curuser->info['mname']){$fuser=$pm['msgfrom'];break;}

	if($fuser){
		$str='';
		foreach($days as $k => $v)$str.='&nbsp;'.($day==$k?$v:"<a href=\"?action=pmbox&box=$box&page=$page&fid=$fid&day=$k\">$v</a>");
		tabheader("�� $fuser �Ķ���Ϣ��¼��$str".($fuser ? "&nbsp;&nbsp;>><a href=\"?action=pmsend&box=$box&page=$page&tonames=".rawurlencode($pm['msgfrom'])."\">".'�ظ�'."</a>" : ''));
		tabfooter();
	}

	tabheader('����');
	$pm=end($ucpm);
	if($fuser==$pm['msgfrom']){
		array_pop($ucpm);
		$fuser ? trbasic('������','',($pm['new']?'[<b style="color:red">new</b>]':'').$fuser,'') : trbasic('����','',($pm['msgtoid'] && $pm['new']?'[<b style="color:red">new</b>]':'').($pm['subject'] ? $pm['subject'] : 'ϵͳ����'),'');
		trbasic('����ʱ��','',date("$dateformat $timeformat",$pm['dateline']),'');
		$fuser && trbasic('����','',mhtmlspecialchars($pm['subject']),'');
		trbasic('����','',mnl2br(mhtmlspecialchars($pm['message'])),'');
	}
	if(!empty($ucpm)){
		echo '<tr><td class="item2" colspan="2"><b>'.'��ʷ����'.'</b></td></tr>';
		foreach($ucpm as $pm){
			echo '<tr><td class="item2" colspan="2">'.($fuser==$pm['msgfrom']?(($pm['new']?'[<b style="color:red">new</b>]':'').("$pm[msgfrom] �� " . date("$dateformat $timeformat",$pm['dateline']) . ' ˵��')):('���� ' . date("$dateformat $timeformat",$pm['dateline']) . ' ˵��')).'</td></tr>'.
				 '<tr><td class="item2" colspan="2">'.($pm['subject'] ? '<b>'.mhtmlspecialchars($pm['subject']).'</b><br />' : '').mnl2br(mhtmlspecialchars($pm['message'])).'</td></tr>';
		}
	}
	tabfooter();
	echo "<input class=\"button\" type=\"submit\" name=\"\" value=\"����\" onclick=\"redirect('?action=pmbox&box=$box&page=$page')\">\n";
}
?>
