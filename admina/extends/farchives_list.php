<?php
(!defined('M_COM') || !defined('M_ADMIN')) && exit('No Permission');
# ����Ƿ�ͨ��farchives.php��ڳ�ʼ��
foreach(array('fromsql','wheresql','filterstr',) as $k){
	if(empty($$k)) cls_message::show('���ȳ�ʼ����');
}
if(!submitcheck('bsubmit')){
	echo form_str($actionid.'arcsedit',"?entry=$entry$extend_str&page=$page");
	//ĳЩ�̶�ҳ�����
	trhidden('fcaid',$fcaid);
	tabheader_e();
	echo "<tr><td colspan=\"2\" class=\"txt txtleft\">";
	echo "<input class=\"text\" name=\"keyword\" type=\"text\" value=\"$keyword\" size=\"8\" style=\"vertical-align: middle;\" title=\"�������������\">&nbsp; ";
	echo ''.cls_fcatalog::areaShow($fcaid,@$farea,'Search','farea').'&nbsp; '; //ѡ�����
	echo "<select style=\"vertical-align: middle;\" name=\"checked\">".makeoption(array('-1' => '���״̬','0' => 'δ��','1' => '����'),$checked)."</select>&nbsp; ";
	echo "<select style=\"vertical-align: middle;\" name=\"valid\">".makeoption(array('-1' => '��Ч״̬','0' => '��Ч','1' => '��Ч'),$valid)."</select>&nbsp; ";
	echo strbutton('bfilter','ɸѡ');
	tabfooter();

	//�б���
	tabheader("��Ϣ�б� - ".cls_fcatalog::Config($fcaid,'title'),'','',10);
	$cy_arr = array("<input class=\"checkbox\" type=\"checkbox\" name=\"chkall\" onclick=\"checkall(this.form, 'selectid', 'chkall')\">",'ID');
	$cy_arr[] = array("����$vflag",'txtL');
	$area_coid && $cy_arr[] = '����';
	$cy_arr[] = '���';
	$cy_arr[] = '����';
	if(!cls_fcatalog::Config($fcaid,'nodurat')){
		$cy_arr[] = '��ʼ����';
		$cy_arr[] = '����ʱ��';
	}
	$cy_arr[] = '����';
	$cy_arr[] = '�༭';
	trcategory($cy_arr);

	$pagetmp = $page; //echo "SELECT * $fromsql $wheresql";
	do{
		$query = $db->query("SELECT * $fromsql $wheresql ORDER BY a.vieworder DESC LIMIT ".(($pagetmp - 1) * $atpp).",$atpp");
		$pagetmp--;
	} while(!$db->num_rows($query) && $pagetmp);

	$itemstr = '';
	while($r = $db->fetch_array($query)){
		$_views = array();
		$_views['select'] = "<input class=\"checkbox\" type=\"checkbox\" name=\"selectid[$r[aid]]\" value=\"$r[aid]\">";
		$_views['aid'] = $r['aid'];

		$_views['subject'] = "<span ".($r['color']?'style="color:'.$r['color'].'"':'').">".mhtmlspecialchars($r['subject'])."</span>";
		if($vflag) $_views['subject'] = "<a href=\"?entry=extend&extend=farchiveinfo&aid=$r[aid]&detail=1\" target=\"_blank\">".$_views['subject']."</a>";
		if($r['arcurl']) $_views['subject'] .= '(��̬)';
		
		$_views['check'] = $r['checked'] ? 'Y' : '-';
		$_views['order'] = $r['vieworder'];
		$_views['startdate'] = $r['startdate'] ? date('Y-m-d',$r['startdate']) : '-';
		$_views['enddate'] = $r['enddate'] ? date('Y-m-d',$r['enddate']) : '-';
		$_views['view'] = "<a id=\"{$actionid}_info_$r[aid]\" href=\"?entry=extend&extend=farchiveinfo&aid=$r[aid]\" onclick=\"return showInfo(this.id,this.href)\">�鿴</a>";
		$_views['edit'] = "<a href=\"?entry=extend&extend=farchive&aid=$r[aid]\" onclick=\"return floatwin('open_farchive',this)\">����</a>";
		

		$itemstr .= "<tr class=\"txt\">\n";
		$itemstr .= "<td class=\"txtC w40\">{$_views['select']}</td>\n";
		$itemstr .= "<td class=\"txtC w40\">{$_views['aid']}</td>\n";
		$itemstr .= "<td class=\"txtL\">{$_views['subject']}</td>\n";
		if($area_coid){ 
			$vstr = cls_catalog::cnstitle($r['farea'],1,$area_arr,0);
			$vstr = cls_string::CutStr($vstr, 60);
			$itemstr .= "<td class=\"txtC\">$vstr</td>\n";
		}
		$itemstr .= "<td class=\"txtC w35\">{$_views['check']}</td>\n";
		$itemstr .= "<td class=\"txtC w35\"><input type='text' value='{$_views['order']}' class='w50' name='orders[{$_views['aid']}]'></td>\n";
		if(!cls_fcatalog::Config($fcaid,'nodurat')){
			$itemstr .= "<td class=\"txtC w100\">{$_views['startdate']}</td>\n";
			$itemstr .= "<td class=\"txtC w100\">{$_views['enddate']}</td>\n";
		}
		$itemstr .= "<td class=\"txtC w35\">{$_views['view']}</td>\n";
		$itemstr .= "<td class=\"txtC w35\">{$_views['edit']}</td>\n";;
		$itemstr .= "</tr>\n";
	}
	$counts = $db->result_one("SELECT count(*) $fromsql $wheresql");
	$multi = multi($counts,$atpp,$page,"?entry=$entry$extend_str$filterstr");

	echo $itemstr;
	tabfooter();
	echo $multi;

	tabheader('������Ŀ');
	$s_arr = array();
	if(allow_op('fdel')) $s_arr['delete'] = 'ɾ��';
    if (allow_op('fcheck'))
    {
    	$s_arr['check'] = '����';
    	$s_arr['uncheck'] = '������';
    }
	$s_arr['static'] = '���ɾ�̬';
	$s_arr['unstatic'] = '�����̬';
	if($s_arr){
		$soperatestr = '';
		foreach($s_arr as $k => $v) $soperatestr .= "<input class=\"checkbox\" type=\"checkbox\" id=\"arcdeal[$k]\" name=\"arcdeal[$k]\" value=\"1\"" . ($k == 'delete' ? ' onclick="deltip()"' : '') . "><label for=\"arcdeal[$k]\">$v</label> &nbsp;";
		trbasic('ѡ�������Ŀ','',$soperatestr,'');
	}
	//trbasic('<input type="checkbox" value="1" name="arcdeal[vieworder]" class="checkbox">&nbsp;��������','','<input name="arcorder">','');
	echo ''.cls_fcatalog::areaShow($fcaid,@$arcfarea,'Sets','arcfarea').'&nbsp; '; //ѡ�����
	tabfooter('bsubmit');

}else{
	//if(empty($arcdeal)) cls_message::show('��ѡ�������Ŀ',"?entry=$entry$extend_str&page=$page$filterstr");
	if(empty($selectid)) cls_message::show('��ѡ����Ϣ',"?entry=$entry$extend_str&page=$page$filterstr");
	$arc = new cls_farcedit;
	foreach($selectid as $aid){
		$arc->set_aid($aid);
		if(!empty($arcdeal['delete'])){
			$arc->arc_delete();
			continue;
		}
		if(!empty($arcdeal['check'])){
			$arc->arc_check(1);
		}elseif(!empty($arcdeal['uncheck'])){
			$arc->arc_check(0);
		}
		if(!empty($arcdeal['static'])){
			$arc->tostatic();
		}elseif(!empty($arcdeal['unstatic'])){
			$arc->unstatic();
		}elseif(!empty($arcdeal['farea'])){
			$arc->set_column($mode_arcfarea,$arcfarea,$area_coid,'farea',"farchives",19,1);
		} 
		$iOrder = @$orders[$aid];
		$iOrder = empty($iOrder) ? 0 : max(0,intval($iOrder));
		$arc->updatefield('vieworder',$iOrder); 
		$arc->updatedb();
	}
	unset($arc);

	adminlog('������Ϣ����','������Ϣ�б����');
	cls_message::show('������Ϣ�������',"?entry=$entry$extend_str&page=$page$filterstr");

}
