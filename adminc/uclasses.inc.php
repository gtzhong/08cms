<?php
!defined('M_COM') && exit('No Permission');
foreach(array('mcatalogs','commus','mcommus','mtconfigs') as $k) $$k = cls_cache::Read($k);

empty($cuid) && $cuid = 0;
$cuidsarr = array(0 => '�ĵ�');foreach($commus as $k => $v) $v['tbl'] && $cuidsarr[$k] = $v['cname'];
array_key_exists($cuid, $cuidsarr) || $cuid = 0;
if(empty($deal)){
	if(!submitcheck('bsubmit')){
		tabheader($cuidsarr[$cuid]."����&nbsp; <a href=\"?action=$action&deal=uclassadd&cuid=$cuid\" onclick=\"return floatwin('open_uclasses',this)\">>>��ӷ���</a>",'uclassesedit',"?action=$action&cuid=$cuid",'6');
		trcategory(array('ɾ?',array('��������','left'),'����','�����ռ���Ŀ','�༭'));
		$query = $db->query("SELECT * FROM {$tblprefix}uclasses WHERE cuid='$cuid' AND mid='$memberid' ORDER BY vieworder,mcaid,ucid");
		while($r = $db->fetch_array($query)) {
			$mcatalogstr = empty($mcatalogs[$r['mcaid']]) ? '-' : $mcatalogs[$r['mcaid']]['title'].'&nbsp; <font class="gray">'.$mcatalogs[$r['mcaid']]['remark'].'</font>';
			echo "<tr>\n".
				"<td class=\"item\" width=\"40\"><input class=\"checkbox\" type=\"checkbox\" name=\"delete[$r[ucid]]\" value=\"$r[ucid]\"></td>\n".
				"<td class=\"item2\"><input type=\"text\" size=\"30\" maxlength=\"30\" name=\"uclassesnew[$r[ucid]][title]\" value=\"$r[title]\"></td>\n".
				"<td class=\"item\"><input type=\"text\" size=\"4\" maxlength=\"3\" name=\"uclassesnew[$r[ucid]][vieworder]\" value=\"$r[vieworder]\"></td>\n".
				"<td class=\"item\">$mcatalogstr</td>\n".
				"<td class=\"item\" width=\"40\"><a href=\"?action=$action&deal=uclassdetail&ucid=$r[ucid]\" onclick=\"return floatwin('open_uclasses',this)\">����</a></td>\n".
				"</tr>\n";
		}
		tabfooter('bsubmit');
	}else{
		if(!empty($delete)){
			foreach($delete as $k){
				$k = (int)$k;
				$db->query("DELETE FROM {$tblprefix}uclasses WHERE ucid='$k' AND mid='$memberid'");
				$na = stidsarr(1);
				foreach($na as $x => $y){
					$db->query("UPDATE {$tblprefix}".atbl($x,1)." SET ucid='0' WHERE ucid='$k'",'SILENT');
				}
				unset($uclassesnew[$k]);
			}
		}
		foreach($uclassesnew as $k => $v){
			$k = (int)$k;
			$v['vieworder'] = intval($v['vieworder']);
			$v['title'] = trim(strip_tags($v['title']));
			if($v['title']){
				$v['title'] = cls_string::CutStr($v['title'],$uclasslength,'');
				$db->query("UPDATE {$tblprefix}uclasses SET 
							title='$v[title]', 
							vieworder='$v[vieworder]' 
							WHERE ucid='$k'");
			}
		}
		cls_message::show('�û�����༭���',"?action=$action&cuid=$cuid");
	}
}elseif($deal == 'uclassadd'){
	if(!submitcheck('bsubmit')){
		tabheader('����û�����-'.$cuidsarr[$cuid],'uclassesadd',"?action=$action&deal=uclassadd&cuid=$cuid",2,0,1);
		trbasic('�û��������','fmdata[title]', '', 'text', array('validate' => makesubmitstr('fmdata[title]',1,0,0,$uclasslength)));
		trhidden('fmdata[cuid]',$cuid);
		trbasic('�����ռ���Ŀ','fmdata[mcaid]',makeoption(array('0' => '������') + cls_mcatalog::mcaidsarr($curuser->info['mtcid'],1)),'select');
		tabfooter('bsubmit','���');
	}else{
		if(!($fmdata['title'] = trim(strip_tags($fmdata['title'])))) cls_message::show('�������û�������⡣',M_REFERER);
		$fmdata['title'] = cls_string::CutStr($fmdata['title'],$uclasslength,'');
		$nowUclasses = cls_Mspace::LoadUclasses($curuser->info['mid'],0);
		if($maxuclassnum && count($nowUclasses) > $maxuclassnum) cls_message::show("���ķ�����������$maxuclassnum,�������ַ��ࡣ",M_REFERER);
		if($fmdata['mcaid']){
			if(!($allownum = @$mcatalogs[$fmdata['mcaid']]['maxucid'])) cls_message::show('ָ������Ŀ��������û�����',M_REFERER);
			$num = 0;foreach($nowUclasses as $k => $v) if(@$v['mcaid'] == $fmdata['mcaid']) $num ++;
			if($num >= $allownum) cls_message::show("ָ����Ŀ�ڵķ��������ܳ���$allownum,�������ַ��ࡣ",M_REFERER);
		}
		$db->query("INSERT INTO {$tblprefix}uclasses SET 
					title='$fmdata[title]', 
					mcaid='$fmdata[mcaid]', 
					cuid='$fmdata[cuid]', 
					mid='$memberid'");
		cls_message::show('��ӷ������',axaction(6,"?action=$action&cuid=$cuid"));
	}
}elseif($deal == 'uclassdetail' && !empty($ucid)){
	$ucid = (int)$ucid;
	if(!($uclass = $db->fetch_one("SELECT * FROM {$tblprefix}uclasses WHERE ucid='$ucid' AND mid='$memberid'"))) cls_message::show('��ָ����ȷ���û����ࡣ',M_REFERER);
	if(!submitcheck('bsubmit')){
		tabheader('�༭���˷���','uclassdetail',"?action=$action&deal=uclassdetail&ucid=$ucid",2,0,1);
		trbasic('���˷�������','fmdata[title]',$uclass['title'],'text', array('validate' => makesubmitstr('fmdata[title]',1,0,0,$uclasslength)));
		trbasic('�����ռ���Ŀ','fmdata[mcaid]',makeoption(array('0' => '������') + cls_mcatalog::mcaidsarr($curuser->info['mtcid'],1),$uclass['mcaid']),'select');
		tabfooter('bsubmit');
	
	}else{
		!($fmdata['title'] = trim(strip_tags($fmdata['title']))) && cls_message::show('��������˷�������',M_REFERER);
		$fmdata['title'] = cls_string::CutStr($fmdata['title'],$uclasslength,'');
		$db->query("UPDATE {$tblprefix}uclasses SET 
					title='$fmdata[title]', 
					mcaid='$fmdata[mcaid]' 
					WHERE ucid='$ucid'");
		cls_message::show('�û�����༭��ɡ�',axaction(6,"?action=$action&cuid=$cuid"));
	}

}
?>
