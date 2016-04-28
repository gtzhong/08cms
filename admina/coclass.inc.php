<?php
(!defined('M_COM') || !defined('M_ADMIN')) && exit('No Permission');
aheader();
if($re = $curuser->NoBackFunc('catalog')) cls_message::show($re);
$cotypes = cls_cache::Read('cotypes');
if(!$coid || empty($cotypes[$coid])) cls_message::show('��ָ����ȷ�������ϵ');
include_once M_ROOT."include/fields.fun.php";
foreach(array('channels','grouptypes','permissions','vcps','rprojects','catalogs',) as $k) $$k = cls_cache::Read($k);
$cotype = $cotypes[$coid];
$coclasses = cls_catalog::InitialInfoArray($coid);
$cotypename = $cotype['cname'];
$ccfields = cls_cache::Read('cnfields',$coid);
empty($action) && $action = 'coclassedit';
if(is_file($ex = dirname(__FILE__)."/exconfig/coclass_{$coid}_$action.php")){
	include($ex);
	entryfooter();
}
$c_upload = cls_upload::OneInstance();	
if($action == 'coclassadd'){
	echo "<title>��ӷ��� - $cotypename</title>";
	if(!submitcheck('bcoclassadd')) {
		$pid = empty($pid) ? 0 : max(0,intval($pid));
		if($pid) $pmsg = @$coclasses[$pid];
		tabheader("��� [$cotypename] ����",'coclassadd',"?entry=$entry&action=$action&coid=$coid",2,1,1);
		trbasic('��������','coclassnew[title]','','text',array('validate' => ' onfocus="initPinyin(\'coclassnew[dirname]\')"' . makesubmitstr('coclassnew[title]',1,0,0,30)));
		trbasic('�����ʶ','', '<input type="text" value="" name="coclassnew[dirname]" id="coclassnew[dirname]" size="25"' . makesubmitstr('coclassnew[dirname]',1,'numberletter',0,30) . ' offset="2">&nbsp;&nbsp;<input type="button" value="�������" onclick="check_repeat(\'coclassnew[dirname]\',\'dirname\');">&nbsp;&nbsp;<input type="button" value="�Զ�ƴ��" onclick="autoPinyin(\'coclassnew[title]\',\'coclassnew[dirname]\')" />',
		'',array('guide' => '���ɾ�̬ʱ���ñ�ʶ����Ϊ��̬Ŀ¼����ֻ������ĸ���ֺ��»���'));
		trbasic('������','coclassnew[pid]',makeoption(array('0' => '��������') + pidsarr($coid), $pid),'select');
		trbasic('�ṹ����(�����ӷ���)','coclassnew[isframe]','','radio');
		if(empty($cotype['self_reg']) && empty($cotype['chidsforce'])){
			$dchids = empty($pmsg['chids']) ? (empty($cotype['chids']) ? '' : $cotype['chids']) : $pmsg['chids'];
			trbasic('������ģ����Ч<br /><input class="checkbox" type="checkbox" name="chchkall" onclick="checkall(this.form,\'coclassnew[chids]\',\'chchkall\')">ȫѡ','',makecheckbox('coclassnew[chids][]',cls_channel::chidsarr(1),empty($dchids) ? array() : explode(',',$dchids),5),'');
		}
		if($cotype['groups']){ 
			$garr = select_arr($cotype['groups']); $vdef = explode(',',$cotype['groups']);
			trbasic('��������','',makecheckbox('coclassnew[groups][]',$garr,$vdef,5),'');
		}
		tabfooter();
		if(!empty($cotype['self_reg'])){
			tabheader("���&nbsp;[$cotypename]&nbsp;����-�ĵ��Զ�������������");
			trrange('�������',array('coclassnew[conditions][indays]','','','&nbsp; ��ǰ&nbsp; &nbsp; -&nbsp; &nbsp; '),array('coclassnew[conditions][outdays]','','','&nbsp; '.'����'));
			trrange('�������',array('coclassnew[conditions][createdatefrom]','','','&nbsp; ��ʼ&nbsp; &nbsp; -&nbsp; &nbsp; '),array('coclassnew[conditions][createdateto]','','','&nbsp; '.'����'),'calendar');
			trrange('�����',array('coclassnew[conditions][clicksfrom]','','','&nbsp; ��С&nbsp; &nbsp; -&nbsp; &nbsp; '),array('coclassnew[conditions][clicksto]','','','&nbsp; '.'���'));
			$createurl = "<br>>><a href=\"?entry=liststr&action=selfclass\" target=\"_blank\">�����ִ�</a>";
			trbasic('�Զ���������ѯ�ִ�'.$createurl,'coclassnew[conditions][sqlstr]','','textarea');
			tabfooter();
		}
		tabheader("������Ϣ");
		$a_field = new cls_field;
		foreach($ccfields as $k => $v){
			$a_field->init($v);
			$a_field->isadd = 1;
			$a_field->trfield('coclassnew');
		}
		trbasic('�����ĺ�������','',makeradio('needtip',array('��ʾ����һ����ʲô','�������','�رմ���'),empty($m_cookie["np_add_$coid"]) ? 0 : $m_cookie["np_add_$coid"]),'');
		tabfooter('bcoclassadd','���');
		a_guide('coclassadd');
	}else{
		if(!$coclassnew['title'] || !$coclassnew['dirname']) cls_message::show('�������ϲ���ȫ',M_REFERER);
		if(preg_match("/[^a-zA-Z_0-9]+/",$coclassnew['dirname'])) cls_message::show('�����ʶ���Ϲ淶',M_REFERER);
		$coclassnew['dirname'] = strtolower($coclassnew['dirname']);
		if(in_array($coclassnew['dirname'],cls_cache::Read('cn_dirnames'))) cls_message::show('�����ʶ�ظ�',M_REFERER);
		$coclassnew['level'] = $coclassnew['pid'] ? ($coclasses[$coclassnew['pid']]['level'] + 1) : 0;
		$sqlstr0 = "title='$coclassnew[title]',
					dirname='$coclassnew[dirname]',
					isframe='$coclassnew[isframe]',
					level='$coclassnew[level]',
					pid='$coclassnew[pid]'";
		if(!empty($coclassnew['groups'])){
			$coclassnew['groups'] = empty($coclassnew['groups']) ? '' : implode(',',$coclassnew['groups']);
			$sqlstr0 .= ",groups='$coclassnew[groups]'";
		}
		if(empty($cotype['self_reg'])){
			$coclassnew['chids'] = empty($cotype['chidsforce']) ? (empty($coclassnew['chids']) ? '' : implode(',',$coclassnew['chids'])) : $cotype['chids'];
			$sqlstr0 .= ",chids='$coclassnew[chids]'";
		}else{
			foreach(array('clicksfrom','indays','clicksto','outdays',) as $v){
				if($coclassnew['conditions'][$v] == ''){
					unset($coclassnew['conditions'][$v]);
				}else $coclassnew['conditions'][$v] = max(0,intval($coclassnew['conditions'][$v]));
			}
			foreach(array('createdatefrom','createdateto',) as $v){
				if($coclassnew['conditions'][$v] == '' || !cls_string::isDate($coclassnew['conditions'][$v])){
					unset($coclassnew['conditions'][$v]);
				}else $coclassnew['conditions'][$v] = strtotime($coclassnew['conditions'][$v]);
			}
			$coclassnew['conditions']['sqlstr'] = trim($coclassnew['conditions']['sqlstr']);
			if($coclassnew['conditions']['sqlstr'] == '') unset($coclassnew['conditions']['sqlstr']);
			if(empty($coclassnew['conditions'])) cls_message::show('�������Զ���������',M_REFERER);
			$coclassnew['conditions'] = addslashes(serialize($coclassnew['conditions']));
			$sqlstr0 .= ",conditions='$coclassnew[conditions]'";
		}
		$a_field = new cls_field;
		$sqlstr = "";
		foreach($ccfields as $k => $v){
			$a_field->init($v);
			$a_field->deal('coclassnew','cls_message::show',M_REFERER);
			$sqlstr .= ','.$k."='".$a_field->newvalue."'";
			if($arr = multi_val_arr($a_field->newvalue,$v)) foreach($arr as $x => $y) $sqlstr .= ','.$k.'_'.$x."='$y'";
		}
		$c_upload->saveuptotal(1);
		!empty($cotype['autoletter']) && $sqlstr .= ",letter='".autoletter(@$coclassnew[$cotype['autoletter']])."'";
		$db->query("INSERT INTO {$tblprefix}coclass$coid SET ccid=".auto_insert_id('coclass').",$sqlstr0,coid='$coid' $sqlstr");
		if($ccid = $db->insert_id()){
			$c_upload->closure(1, $ccid, 'coclass');
		}
		unset($a_field);
		
		adminlog('�����ϵ����');
		cls_catalog::DbTrueOrder($coid);
		cls_CacheFile::Update('coclasses',$coid);
		
		$needtip = min(2,max(0,intval($needtip)));
		$needtip ? msetcookie("np_add_$coid",$needtip,31536000) : mclearcookie("np_add_$coid");
		$na = array(array('�鿴��������',36,"follow"),array('���������һ��',36,$action),array('�����رմ���',6,'coclassedit'),);
		cls_message::show('������ӳɹ���'.$na[$needtip][0], axaction($na[$needtip][1],"?entry=$entry&coid=$coid&action=".$na[$needtip][2]));
	}
}elseif($action == 'follow'){
	echo "<title>��������</title>";
	$cnrels = cls_cache::Read('cnrels');
	$viewsarr = array();
	foreach($cnrels as $k => $v){
		if(in_array($coid,array($v['coid'],$v['coid1']))){
			$viewsarr[] = array("$k.��Ŀ����","<a href=\"?entry=cnrels&action=cnreldetail&rid=$k&isframe=1\" target=\"_blank\">>>����</a>������ [$v[cname]] �еĹ���");
		}
	}
	if($cotype['sortable']){
		$str = "<a href=\"?entry=cnodes&action=cnconfigs&ncoid=$coid&arcdeal=newupdate&isframe=1\" target=\"_blank\">>>��ʽ1</a>��ѡ����صĽڵ���ɷ�������ȫ�����еĽڵ㡣�˷�ʽ���ֶ�ѡ��";
		$str .= "<br><a href=\"?entry=cnodes&action=patchupdate&coid=$coid\" onclick=\"return floatwin('open_fnodes',this)\">>>��ʽ2</a>���Զ���ȫ������ [$cotypename] ��صĽڵ���ɷ������˷�ʽһ����ɡ�";
		$viewsarr[] = array('�����Ŀ�ڵ�',$str);
		$viewsarr[] = array('��ӻ�Ա�ڵ�',"<a href=\"?entry=mcnodes&action=mcnodeadd&isframe=1\" target=\"_blank\">>>����</a>�������Ҫ�Ļ�Ա�ڵ�");
		$str = "<a href=\"?entry=o_cnodes&action=cnconfigs&ncoid=$coid&arcdeal=newupdate&isframe=1\" target=\"_blank\">>>��ʽ1</a>��ѡ����صĽڵ���ɷ�������ȫ�����еĽڵ㡣�˷�ʽ���ֶ�ѡ��";
		$str .= "<br><a href=\"?entry=o_cnodes&action=patchupdate&coid=$coid\" onclick=\"return floatwin('open_fnodes',this)\">>>��ʽ2</a>���Զ���ȫ������ [$cotypename] ��صĽڵ���ɷ������˷�ʽһ����ɡ�";
		$viewsarr[] = array('����ֻ���ڵ�',$str);
	}
	tabheader('��ӷ���֮��ĺ�������');
	if(empty($viewsarr)){
		trbasic('��ʾ˵��','','1���ǽڵ���ϵ����Ҫ��ؽڵ����<br>2��û����ص���Ŀ������Ŀ��Ҫ����','');
	}else{
		foreach($viewsarr as $k => $v) trbasic($v[0],'',$v[1],'');
	}
	tabfooter();
}elseif($action == 'coclassadds' && empty($cotype['self_reg'])){
	echo "<title>������� - $cotypename</title>";
	$pid = 0;
	$chids = cls_channel::chidsarr(1);
	$groups = select_arr($cotype['groups']); //$vdef = explode(',',$coclass['groups']);
	$_settings = array(
		'pid' => array(
			'type' => 'select',
			'title' => '������',
			'value' => makeoption(array('0' => '��������') + pidsarr($coid))
		),
		'isframe' => array(
			'type' => 'radio',
			'title' => '�ṹ����(�����ӷ���)',
			'value' => ''
		),
	);
	if(empty($cotype['chidsforce'])){
		$_settings['chids'] = array(
			'type' => '',
			'title' => '������ģ����Ч',
			'value' => makecheckbox('coclasssitems[chids][]',$chids,!empty($cotype['chids']) ? explode(',',$cotype['chids']) : array(),5)
		);
	}
	//��chids����
	if($cotype['groups']){ 
		$garr = select_arr($cotype['groups']); $vdef = explode(',',$cotype['groups']);
		$_settings['groups'] = array(
			'type' => '',
			'title' => '��������',
			'value' => makecheckbox('coclasssitems[groups][]',$garr,!empty($cotype['groups']) ? explode(',',$cotype['groups']) : array(),5)
		);	
	}	
	foreach($ccfields as $k => $v){
		$_settings[$k] = array(
			'type' => 'field',
			'title' => $v['cname']
		);
	}
	if(!submitcheck('bcoclassset') && !submitcheck('bcoclassadd')) {
		tabheader("������ӷ��� - $cotypename",'coclasseadd',"?entry=$entry&action=$action&coid=$coid",2,0,1);
		trbasic('��ӷ�������','batch_count','','text',array('validate'=>' rule="int" must="1" min="1" max="200"'));
		trbasic('��Ҫ�ֱ����õ���','','','');

		trbasic('<input class="checkbox" type="checkbox" checked="checked" disabled="disabled" />', '', '�����ʶ'.
			' <input class="checkbox" type="checkbox" name="auto_pinyin" id="auto_pinyin" value="1" /><label for="auto_pinyin">�Զ�ƴ��</label>',
			'',array('guide' => '���ɾ�̬ʱ���ñ�ʶ����Ϊ��̬Ŀ¼����ֻ������ĸ���ֺ��»���'));
		foreach($_settings as $k => $v)trbasic('<input class="checkbox" type="checkbox" name="diffitems[]" value="'.$k.'" />', '', $v['title'], '');
		tabfooter('bcoclassset');
	}elseif(!submitcheck('bcoclassadd')){
		$batch_count = max(0, intval($batch_count));
		empty($batch_count) && cls_message::show('����д������ӵ���Ŀ����', M_REFERER);
		$_diffitems = array(
			'title' => array(
				'type' => 'text',
				'title' => '��������',
				'value' => ''
			),
		);
		empty($auto_pinyin) && $_diffitems['dirname'] = array(
			'type' => 'text',
			'title' => '�����ʶ',
			'value' => ''
		);

		$a_field = new cls_field;
		empty($diffitems) && $diffitems = array();
		tabheader('������ӷ��� - ��ͬ����','coclassadd',"?entry=$entry&action=$action&coid=$coid",2,1,1);
		foreach($_settings as $k => $v){
			if(in_array($k, $diffitems)){
				$_diffitems[$k] = $v;
			}elseif($v['type'] == 'field'){
				$a_field->init($ccfields[$k]);
				$a_field->isadd = 1;
				$a_field->trfield('coclasssome');
			}else{
				trbasic($v['title'], "coclasssome[$k]", $v['value'], $v['type'],array('guide'=>array_key_exists('tip', $v) ? $v['tip'] : ''));
			}
		}
		trbasic('�����ĺ�������','',makeradio('needtip',array('��ʾ����һ����ʲô','�������','�رմ���'),empty($m_cookie["np_adds_$coid"]) ? 0 : $m_cookie["np_adds_$coid"]),'');
		tabfooter();
		
		echo '<br /><br />'.(empty($auto_pinyin) ? '' : '<input type="hidden" name="auto_pinyin" value="1" />');
		for($i = 0; $i < $batch_count; $i++){
			tabheader('������� - ����'.($i+1));
			foreach($_diffitems as $k => $v){
				if($v['type'] == 'field'){
					$a_field->init($ccfields[$k]);
					$a_field->isadd = 1;
					$a_field->trfield("coclassitems[$i]");
				}else{
					if($k=='chids'){
						$str = makecheckbox("coclassitems[$i][chids][]",$chids,!empty($pmsg['chids']) ? explode(',',$pmsg['chids']) : array(),5);
					}elseif($k=='groups'){
						$str = makecheckbox("coclassitems[$i][groups][]",$groups, array() ,5); //echo "@@@@@@@@@@@";
					}else{
						$str = $v['value'];
					}
					trbasic($v['title'], "coclassitems[$i][$k]", "$str", $v['type'],array('guide'=>array_key_exists('tip', $v) ? $v['tip'] : ''));
				}
			}
			tabfooter();
		}
		echo '<br /><input class="btn" type="submit" name="bcoclassadd" value="�ύ">';
		a_guide('coclassadd');
	}else{
		$enamearr = cls_cache::Read('cn_dirnames');

		$ok = 0;
		$a_field = new cls_field;
		foreach($coclassitems as $item){
			$coclassnew = $coclasssome;
			foreach($item as $k => $v){
				if(is_array($v)){
					foreach($v as $a => $b)$coclassnew[$k][$a] = $b;
				}else{
					$coclassnew[$k] = $v;
				}
			}

			empty($auto_pinyin) || $coclassnew['dirname'] = cls_string::Pinyin($coclassnew['title']);
			if(!$coclassnew['title'] || !$coclassnew['dirname'])continue;
			if(preg_match("/[^a-zA-Z_0-9]+/",$coclassnew['dirname']))continue;
			$coclassnew['dirname'] = strtolower($coclassnew['dirname']);
			if(empty($auto_pinyin)){
				if(in_array($coclassnew['dirname'], $enamearr))continue;
			}else{
				$i = 1;
				$dirname = $coclassnew['dirname'];
				while(in_array($coclassnew['dirname'], $enamearr))$coclassnew['dirname'] = $dirname.($i++);
			}

			$coclassnew['level'] = $coclassnew['pid'] ? ($coclasses[$coclassnew['pid']]['level'] + 1) : 0;
			$sqlstr0 = "title='$coclassnew[title]',
						dirname='$coclassnew[dirname]',
						isframe='$coclassnew[isframe]',
						level='$coclassnew[level]',
						pid='$coclassnew[pid]'";
					
			if(!empty($coclassnew['groups'])){
				$coclassnew['groups'] = empty($coclassnew['groups']) ? '' : implode(',',$coclassnew['groups']);
				$sqlstr0 .= ",groups='$coclassnew[groups]'";
			}
						
			$coclassnew['chids'] = empty($cotype['chidsforce']) ? (empty($coclassnew['chids']) ? '' : implode(',',$coclassnew['chids'])) : $cotype['chids'];
			$sqlstr0 .= ",chids='$coclassnew[chids]'";
						
			$a_field = new cls_field;
			$sqlstr = "";
			foreach($ccfields as $k => $v){
				$a_field->init($v);
				$a_field->deal('coclassnew','cls_message::show',axaction(2,M_REFERER));
				$sqlstr .= ','.$k."='".$a_field->newvalue."'";
				if($arr = multi_val_arr($a_field->newvalue,$v)) foreach($arr as $x => $y) $sqlstr .= ','.$k.'_'.$x."='$y'";
			}
			$c_upload->saveuptotal(1);
			!empty($cotype['autoletter']) && $sqlstr .= ",letter='".autoletter(@$coclassnew[$cotype['autoletter']])."'";
			$db->query("INSERT INTO {$tblprefix}coclass$coid SET 
				ccid=".auto_insert_id('coclass').",
				$sqlstr0,
				coid='$coid' 
				$sqlstr
				");
			if($ccid = $db->insert_id()){
				$c_upload->closure(1,$ccid,'coclass');
				$enamearr[] = $coclassnew['dirname'];
				$ok++;
			}
		}
		unset($a_field);
		adminlog('����ĵ�����');
		cls_catalog::DbTrueOrder($coid);
		cls_CacheFile::Update('coclasses',$coid);
		
		$needtip = min(2,max(0,intval($needtip)));
		$needtip ? msetcookie("np_adds_$coid",$needtip,31536000) : mclearcookie("np_adds_$coid");
		$na = array(array('�鿴��������',36,"follow"),array('���������һ��',36,$action),array('�����رմ���',6,'coclassedit'),);
		cls_message::show(($ok ? "�ɹ���� $ok ����Ŀ," : '�������ʧ��').$na[$needtip][0], axaction($na[$needtip][1],"?entry=$entry&coid=$coid&action=".$na[$needtip][2]));
	}
}elseif($action == 'coclassedit') {
	echo "<title>������� - $cotypename</title>";
	if(!submitcheck('bcoclassedit')) {
		echo form_str('coclassedit',"?entry=$entry&action=$action&coid=$coid");
		$addfieldstr = "&nbsp; &nbsp; >><a href=\"?entry=$entry&action=coclassadd&coid=$coid\" title=\"������ӷ���\" onclick=\"return floatwin('open_coclassedit',this)\">���</a>";
		empty($cotype['self_reg']) && $addfieldstr .= " [<a href=\"?entry=$entry&action=coclassadds&coid=$coid\" title=\"������ӷ���\" onclick=\"return floatwin('open_coclassedit',this)\">����</a>]";
		$addfieldstr .= " [<a href=\"?entry=$entry&action=follow&coid=$coid\" title=\"��ӷ�����֮��ĺ�������\" onclick=\"return floatwin('open_coclassedit',this)\">����</a>]";
		echo "<div class=\"conlist1\">[$cotypename]&nbsp;�������$addfieldstr</div>";
		echo '<script type="text/javascript">var cocs = [';
		$pidsarr = pidsarr($coid);
		foreach($coclasses as $k => $v){
			$s = isset($pidsarr[$k]) ? "<a href=\"?entry=$entry&action=coclassadd&coid=$coid&pid=$k\" onclick=\"return floatwin('open_coclassedit',this)\">���</a>" : '-';
			echo "[$v[level],$k,'" . str_replace("'","\\'",mhtmlspecialchars($v['title'])) . "',$v[vieworder],'".str_replace("'","\\'",$s)."'],";
		}
		empty($cotype['treestep']) && $cotype['treestep'] = '';
		echo <<<DOT
];
document.write(tableTree({data:cocs,ckey:'ckey_{$coid}_',step:'$cotype[treestep]',html:{
		head: '<td class="txtC" width="30"><input type="checkbox" name="chkall" class="checkbox" onclick="checkall(this.form,\'selectid\',\'chkall\')">ȫ</td>'
			+ '<td class="txtC" width="40">ID</td>'
			+ '<td class="txtL" width="350"%code%>�������� %input%</td>'
			+ '<td class="txtC" width="40">����</td>'
			+ '<td class="txtC" width="40">���</td>'
			+ '<td class="txtC" width="40">����</td>'
			+ '<td class="txtC" width="40">ɾ��</td>',
		cell:[2,4],
		rows: '<td class="txtC" width="30"><input class="checkbox" name="selectid[%1%]" value="'
					+ '%1%" type="checkbox" onclick="tableTree.setChildBox()" /></td>'
			+ '<td class="txtC" width="40">%1%</td>'
			+ '<td class="txtL" width="350">%ico%<input name="coclassesnew['
					+ '%1%][title]" value="%2%" size="25" maxlength="30" type="text" /></td>'
			+  '<td class="txtC" width="40"><input name="coclassesnew['
					+ '%1%][vieworder]" value="%3%" type="text" style="width:36px" /></td>'
			+ '<td class="txtC" width="40">%4%</td>'
			+ '<td class="txtC" width="40"><a href="?entry=$entry&action=coclassdetail&coid=$coid&ccid='
					+ '%1%" onclick="return floatwin(\'open_coclassedit\',this)">����</a></td>'
			+ '<td class="txtC" width="40"><a onclick="return deltip()" href="?entry=$entry&action=coclassdelete&coid=$coid&ccid=%1%">ɾ��</a></td>'
		},
	callback : true
}));
DOT;
		echo '</script>';

		tabheader('������Ŀ'.viewcheck(array('name' => 'viewdetail', 'title' => '��ʾ��ϸ', 'value' => 0, 'body' => $actionid.'tbodyfilter')));
		echo "<tbody id=\"{$actionid}tbodyfilter\" style=\"display:none\">";
		$s_arr = array();
		$cotype['autoletter'] && $s_arr['letter'] = '��������ĸ';
		$s_arr['noletter'] = '�������ĸ';
		$s_arr['delete'] = '����ɾ��';
		if($s_arr){
			$soperatestr = '';
			$i = 1;
			foreach($s_arr as $k => $v){
				$soperatestr .= "<input class=\"checkbox\" type=\"checkbox\" name=\"arcdeal[$k]\" ".($k=='delete' ? "onclick=\"deltip()\"" : '')."  value=\"1\">$v &nbsp;";
				if(!($i % 5)) $soperatestr .= '<br>';
				$i ++;
			}
			$soperatestr && trbasic('ѡ�������Ŀ','',$soperatestr,'');
		}
		if($paidsarr = cls_pusher::paidsarr('catalogs',$coid)){ # ����λ
			$soperatestr = '';
			$i = 1;
			foreach($paidsarr as $k => $v){
				$soperatestr .= OneCheckBox("arcdeal[$k]",cls_pusher::AllTitle($k,1,1),0,1)." &nbsp;";
				if(!($i % 5)) $soperatestr .= '<br>';
				$i ++;
			}
			$soperatestr && trbasic('ѡ������λ','',$soperatestr,'');
		
		}
		trbasic("<input class=\"checkbox\" type=\"checkbox\" name=\"arcdeal[pid]\" value=\"1\">&nbsp;���踸����",'arcpid',makeoption(array('0' => '��������') + pidsarr($coid)),'select');


		if(!$cotype['self_reg'] && empty($cotype['chidsforce'])){
			$cnmodearr = array(0 => '�޸�����������',1 => '��ԭ���������',2 => '��ԭ�������Ƴ�',);
			trbasic("<input class=\"checkbox\" type=\"checkbox\" name=\"arcdeal[chids]\" value=\"1\">&nbsp;������ģ����Ч<br><input class=\"checkbox\" type=\"checkbox\" name=\"chkallc\" onclick=\"checkall(this.form,'arcchids','chkallc')\">ȫѡ",'',"<select id=\"cnmode\" name=\"cnmode\" style=\"vertical-align: middle;\">".makeoption($cnmodearr)."</select><br>".makecheckbox('arcchids[]',cls_channel::chidsarr(0),array(),5),'');
		
		}
		
		if($cotype['groups']){ //echo "$cotype[groups]";
			$gtitle = "<input class=\"checkbox\" type=\"checkbox\" name=\"arcdeal[groups]\" value=\"1\">";
			$gtitle .= "&nbsp;��������";
			//$gtitle .= "<br><input class=\"checkbox\" type=\"checkbox\" name=\"chkallc\" onclick=\"checkall(this.form,'arcgroups','chkallc')\">ȫѡ";
			$gmodearr = array(0 => '�޸�����������',1 => '��ԭ���������',2 => '��ԭ�������Ƴ�',);
			$gmodestr = "<select id=\"gmode\" name=\"gmode\" style=\"vertical-align: middle;\">".makeoption($gmodearr)."</select><br>";
			$garr = select_arr($cotype['groups']); $vdef = explode(',',$cotype['groups']);
			trbasic($gtitle,'',$gmodestr.makecheckbox('arcgroups[]',$garr,array(),5),'');
		}
		
		echo "</tbody>";
		tabfooter('bcoclassedit');
		a_guide('coclassedit');
	}else{
		if(isset($coclassesnew)){
			foreach($coclassesnew as $ccid => $coclassnew){
				$coclassnew['title'] = trim(strip_tags($coclassnew['title']));
				$coclassnew['title'] = $coclassnew['title'] ? $coclassnew['title'] : $coclasses[$ccid]['title'];
				$sqlstr = $coclassnew['vieworder'] != $coclasses[$ccid]['vieworder'] ? ",vieworder='" . max(0,intval($coclassnew['vieworder'])) . "'" : '';
				if(($coclassnew['title'] != $coclasses[$ccid]['title']) || $sqlstr){
					$db->query("UPDATE {$tblprefix}coclass$coid SET 
								title='$coclassnew[title]'
								$sqlstr
								WHERE ccid='$ccid'
								");
				}
			}
		}
		if(!empty($selectid) && !empty($arcdeal)){
			if(!empty($arcdeal['groups']) && $cotype['groups']){
				foreach($selectid as $ccid){
					$groupsnew = empty($arcgroups) ? array() : $arcgroups;
					if(!empty($gmode)){
						$coclass = cls_cache::Read('coclass',$coid,$ccid);
						$groups = empty($coclass['groups']) ? array() : explode(',',$coclass['groups']);
						$groupsnew = $gmode == 1 ? array_unique(array_merge($groups,$groupsnew)) : array_diff($groups,$groupsnew);
					}
					$groupsnew = !empty($groupsnew) ? implode(',',$groupsnew) : '';
					$db->query("UPDATE {$tblprefix}coclass$coid SET groups='$groupsnew' WHERE ccid='$ccid'");
				}
			}
			
			# ����λ
			if($paidsarr = cls_pusher::paidsarr('catalogs',$coid)){
				foreach($paidsarr as $k => $v){
					if(!empty($arcdeal[$k])){
						foreach($selectid as $ccid){
							cls_catalog::push($coid,$ccid,$k);
						}
					}
				}
			}
			
			if(!empty($arcdeal['chids'])){
				foreach($selectid as $ccid){
					$chidsnew = empty($arcchids) ? array() : $arcchids;
					if(!empty($cnmode)){
						$coclass = cls_cache::Read('coclass',$coid,$ccid);
						$chids = empty($coclass['chids']) ? array() : explode(',',$coclass['chids']);
						$chidsnew = $cnmode == 1 ? array_unique(array_merge($chids,$chidsnew)) : array_diff($chids,$chidsnew);
					}
					$chidsnew = !empty($chidsnew) ? implode(',',$chidsnew) : '';
					$db->query("UPDATE {$tblprefix}coclass$coid SET chids='$chidsnew' WHERE ccid='$ccid'");
				}
			}
			
			if(!empty($arcdeal['letter'])){				
					foreach($selectid as $ccid){						
							$letter = !empty($cotype['autoletter'])?autoletter(@$coclasses[$ccid][$cotype['autoletter']]):'';							
							$db->query("UPDATE {$tblprefix}coclass$coid SET letter='$letter' WHERE ccid='$ccid'");						
					}
			}
			//�������ĸ
			if(!empty($arcdeal['noletter'])){				
					foreach($selectid as $ccid){
							$db->query("UPDATE {$tblprefix}coclass$coid SET letter='' WHERE ccid='$ccid'");						
					}
			}
			if(!empty($arcdeal['pid'])){
				foreach($selectid as $ccid){
					$sonids = cls_catalog::cnsonids($ccid,$coclasses);
					if(in_array($arcpid,$sonids)) continue;//���ܸ�������Ϊ��ǰid�����¼�����
					$newlevel = !$arcpid ? 0 : $coclasses[$arcpid]['level'] + 1;
					$db->query("UPDATE {$tblprefix}coclass$coid SET pid='$arcpid',level='$newlevel' WHERE ccid='$ccid'");
					$leveldiff = $newlevel - $coclasses[$ccid]['level'];
					foreach($sonids as $sonid) if($sonid != $ccid) $db->query("UPDATE {$tblprefix}coclass$coid SET level=level+".$leveldiff." WHERE ccid='$sonid'");
				}
			}
			if(!empty($arcdeal['delete'])){
				//�ܹ�����
				deep_allow($no_deepmode && in_array($coid,@explode(',',$deep_coids)),"?entry=$entry&action=coclassedit&coid=$coid");
				foreach($selectid as $ccid){
					if(!($coclass = cls_catalog::InitialOneInfo($coid,$ccid))) cls_message::show('��ָ����ȷ�ķ��ࡣ');
					if($db->result_one("SELECT COUNT(*) FROM {$tblprefix}coclass$coid WHERE pid='$ccid'")) {
						cls_message::show('����û����������ӷ������ɾ��', "?entry=$entry&action=coclassedit&coid=$coid");
					}
					$na = stidsarr(1);
					foreach($na as $k => $v){
						//���÷������Ϣ�����ĵ���ɾ��
						$db->query("UPDATE {$tblprefix}".atbl($k,1)." SET ccid$coid=0 WHERE ccid$coid='$ccid'",'SILENT');
					}
					
					//ɾ����صĽڵ�
					$db->query("DELETE FROM {$tblprefix}cnodes WHERE ename REGEXP 'ccid$coid=$ccid(&|$)'");
					cls_CacheFile::Update('cnodes');
					$db->query("DELETE FROM {$tblprefix}o_cnodes WHERE ename REGEXP 'ccid$coid=$ccid(&|$)'");
					cls_CacheFile::Update('o_cnodes');
					$db->query("DELETE FROM {$tblprefix}mcnodes WHERE mcnvar='ccid$coid' AND mcnid='$ccid'");
					cls_CacheFile::Update('mcnodes');
					
					$db->query("DELETE FROM {$tblprefix}coclass$coid WHERE ccid='$ccid'");
					adminlog('ɾ���ĵ�����');
					cls_CacheFile::Update('coclasses',$coid);
					//������Ŀ����
					$cnrels = cls_cache::Read('cnrels');
					foreach($cnrels as $k => $v){
						$alter = false;
						if(($v['coid'] == $coid) && isset($v['cfgs'][$ccid])){
							unset($v['cfgs'][$ccid]);
							$alter = true;
						}
						if($v['coid1'] == $coid){
							foreach($v['cfgs'] as $x => $y){
								$a = empty($y) ? array() : array_filter(explode(',',$y));
								if(in_array($ccid,$a)){
									$a = array_filter($a,"clearsameid");
									$v['cfgs'][$x] = implode(',',$a);
									$alter = true;
								}
							}
						}
						$alter && $db->query("UPDATE {$tblprefix}cnrels SET cfgs='".(empty($v['cfgs']) ? '' : addslashes(var_export($v['cfgs'],TRUE)))."' WHERE rid='$k'",'SILENT');
					}
					cls_CacheFile::Update('cnrels');					
				}				
			}
		}
		adminlog('�༭�ĵ���������б�');
		cls_catalog::DbTrueOrder($coid);
		cls_CacheFile::Update('coclasses',$coid);
		cls_message::show('����༭���', "?entry=$entry&action=coclassedit&coid=$coid");
	}
}elseif($action =='coclassdetail' && $ccid) {
	echo "<title>�������� - $cotypename</title>";
	if(!($coclass = cls_catalog::InitialOneInfo($coid,$ccid))) cls_message::show('��ָ����ȷ�ķ��ࡣ');
	if(!submitcheck('bcoclassdetail')) {
		tabheader("[$coclass[title]] ��������",'coclassdetail',"?entry=$entry&action=coclassdetail&coid=$coid&ccid=$ccid",2,1,1);
		trbasic('�����ʶ','', '<input type="text" value="'.$coclass['dirname'].'" name="coclassnew[dirname]" id="coclassnew[dirname]" size="25"' . makesubmitstr('coclassnew[dirname]',1,'numberletter',0,30) . ' offset="2">&nbsp;&nbsp;<input type="button" value="�������" onclick="check_repeat(\'coclassnew[dirname]\',\'dirname\');">',
		'',array('guide' => '���ɾ�̬ʱ���ñ�ʶ����Ϊ��̬Ŀ¼����ֻ������ĸ���ֺ��»���'));
		trbasic('������','coclassnew[pid]',makeoption(array('0' => '��������') + pidsarr($coid),$coclass['pid']),'select');
		trbasic('�ṹ����(�����ӷ���)','coclassnew[isframe]',$coclass['isframe'],'radio');
		$coclass['conditions'] = @unserialize($coclass['conditions']);
		if(empty($cotype['self_reg']) && empty($cotype['chidsforce'])){
			trbasic('������ģ����Ч<br /><input class="checkbox" type="checkbox" name="chchkall" onclick="checkall(this.form,\'coclassnew[chids]\',\'chchkall\')">ȫѡ','',makecheckbox('coclassnew[chids][]',cls_channel::chidsarr(1),!empty($coclass['chids']) ? explode(',',$coclass['chids']) : array(),5),'');
		}
		if($cotype['groups']){ 
			$garr = select_arr($cotype['groups']); $vdef = explode(',',$coclass['groups']);
			trbasic('��������','',makecheckbox('coclassnew[groups][]',$garr,$vdef,5),'');
		}
		tabfooter();
		if(!empty($cotype['self_reg'])){
			tabheader("����&nbsp;[$coclass[title]]&nbsp;�ĵ��Զ�������������");
			trrange('�������',array('coclassnew[conditions][indays]',isset($coclass['conditions']['indays']) ? $coclass['conditions']['indays'] : '','','&nbsp; '.'��ǰ'.'&nbsp; &nbsp; -&nbsp; &nbsp; '),array('coclassnew[conditions][outdays]',isset($coclass['conditions']['outdays']) ? $coclass['conditions']['outdays'] : '','','&nbsp; '.'����'));
			trrange('�������',array('coclassnew[conditions][createdatefrom]',isset($coclass['conditions']['createdatefrom']) ? date('Y-m-d',$coclass['conditions']['createdatefrom']) : '','','&nbsp; '.'��ʼ'.'&nbsp; &nbsp; -&nbsp; &nbsp; '),array('coclassnew[conditions][createdateto]',isset($coclass['conditions']['createdateto']) ? date('Y-m-d',$coclass['conditions']['createdateto']) : '','','&nbsp; '.'����'),'calendar');
			trrange('�����',array('coclassnew[conditions][clicksfrom]',isset($coclass['conditions']['clicksfrom']) ? $coclass['conditions']['clicksfrom'] : '','','&nbsp; '.'��С'.'&nbsp; &nbsp; -&nbsp; &nbsp; '),array('coclassnew[conditions][clicksto]',isset($coclass['conditions']['clicksto']) ? $coclass['conditions']['clicksto'] : '','','&nbsp; '.'���'));
			$createurl = "<br>>><a href=\"?entry=liststr&action=selfclass\" target=\"_blank\">�����ִ�</a>";
			trbasic('�Զ���������ѯ�ִ�'.$createurl,'coclassnew[conditions][sqlstr]',isset($coclass['conditions']['sqlstr']) ? stripslashes($coclass['conditions']['sqlstr']) : '','textarea');
			tabfooter();
		}
		$a_field = new cls_field;
		tabheader("[$coclass[title]] ��������");
		foreach($ccfields as $field){
			$a_field->init($field,!isset($coclass[$field['ename']]) ? '' : $coclass[$field['ename']]);
			$a_field->trfield('coclassnew');
		}
		tabfooter('bcoclassdetail');
		a_guide('coclassdetail');
	}else{
		$coclassnew['dirname'] = strtolower($coclassnew['dirname']);
		if($coclassnew['dirname'] != $coclass['dirname']){
			preg_match("/[^a-zA-Z_0-9]+/",$coclassnew['dirname']) && cls_message::show('�����ʶ���Ϲ淶',M_REFERER);
			in_array($coclassnew['dirname'], cls_cache::Read('cn_dirnames')) && cls_message::show('�����ʶ�ظ�',M_REFERER);
		}
		$sonids = cls_catalog::cnsonids($ccid,$coclasses);
		(in_array($coclassnew['pid'],$sonids)) && cls_message::show('��Ŀ����ת��ԭ��Ŀ��������Ŀ��',M_REFERER);
		$coclassnew['level'] = !$coclassnew['pid'] ? 0 : $coclasses[$coclassnew['pid']]['level'] + 1;
		$coclassnew['groups'] = empty($coclassnew['groups']) ? '' : implode(',',$coclassnew['groups']);
		$sqlstr0 = "isframe='$coclassnew[isframe]',
					dirname='$coclassnew[dirname]',
					level='$coclassnew[level]',
					groups='$coclassnew[groups]',
					pid='$coclassnew[pid]'";
		if(empty($cotype['self_reg'])){
			$coclassnew['chids'] = empty($cotype['chidsforce']) ? (empty($coclassnew['chids']) ? '' : implode(',',$coclassnew['chids'])) : $cotype['chids'];
			$sqlstr0 .= ",chids='$coclassnew[chids]'";
		}else{
			foreach(array('clicksfrom','indays','clicksto','outdays',) as $v){
				if($coclassnew['conditions'][$v] == ''){
					unset($coclassnew['conditions'][$v]);
				}else $coclassnew['conditions'][$v] = max(0,intval($coclassnew['conditions'][$v]));
			}
			foreach(array('createdatefrom','createdateto',) as $v){
				if($coclassnew['conditions'][$v] == '' || !cls_string::isDate($coclassnew['conditions'][$v])){
					unset($coclassnew['conditions'][$v]);
				}else $coclassnew['conditions'][$v] = strtotime($coclassnew['conditions'][$v]);
			}
			$coclassnew['conditions']['sqlstr'] = trim($coclassnew['conditions']['sqlstr']);
			if($coclassnew['conditions']['sqlstr'] == '') unset($coclassnew['conditions']['sqlstr']);
			if(empty($coclassnew['conditions'])) cls_message::show('�������Զ���������',M_REFERER);
			$coclassnew['conditions'] = addslashes(serialize($coclassnew['conditions']));
			$sqlstr0 .= ",conditions='$coclassnew[conditions]'";
		}
		
		$a_field = new cls_field;
		$sqlstr = "";
		foreach($ccfields as $k => $v){
			$a_field->init($v,!isset($coclass[$k]) ? '' : $coclass[$k]);
			$a_field->deal('coclassnew','cls_message::show',"?entry=$entry&action=coclassdetail&coid=$coid&ccid=$ccid");
			$sqlstr .= ','.$k."='".$a_field->newvalue."'";
			if($arr = multi_val_arr($a_field->newvalue,$v)) foreach($arr as $x => $y) $sqlstr .= ','.$k.'_'.$x."='$y'";
		}
		$c_upload->closure(1, $ccid, 'coclass');
		$c_upload->saveuptotal(1);
		unset($a_field);

		$leveldiff = $coclassnew['level'] - $coclass['level'];
		foreach($sonids as $sonid){
			 if($sonid != $ccid) $db->query("UPDATE {$tblprefix}coclass$coid SET level=level+".$leveldiff." WHERE ccid='$sonid'");
		}
		!empty($cotype['autoletter']) && $sqlstr .= ",letter='".autoletter(empty($coclassnew[$cotype['autoletter']]) ? @$coclass[$cotype['autoletter']] : $coclassnew[$cotype['autoletter']])."'";
		$db->query("UPDATE {$tblprefix}coclass$coid SET $sqlstr0 $sqlstr WHERE ccid='$ccid'");
		adminlog('��ϸ�޸��ĵ�����');
		cls_catalog::DbTrueOrder($coid);
		cls_CacheFile::Update('coclasses',$coid);
		cls_message::show('�����������',axaction(6,M_REFERER));
	}
}elseif($action == 'coclassdelete' && $ccid) {
	deep_allow($no_deepmode && in_array($coid,@explode(',',$deep_coids)),"?entry=$entry&action=coclassedit&coid=$coid");
	if(!($coclass = cls_catalog::InitialOneInfo($coid,$ccid))) cls_message::show('��ָ����ȷ�ķ��ࡣ');
	if($db->result_one("SELECT COUNT(*) FROM {$tblprefix}coclass$coid WHERE pid='$ccid'")) {
		cls_message::show('����û����������ӷ������ɾ��', "?entry=$entry&action=coclassedit&coid=$coid");
	}
	if(!submitcheck('confirm')){
		$message = "ɾ�����ָܻ���ȷ��ɾ����ѡ��Ŀ?<br><br>";
		$message .= "ȷ������>><a href=\"?entry=$entry&action=coclassdelete&coid=$coid&ccid=$ccid&confirm=ok\">ɾ��</a><br>";
		$message .= "��������>><a href=\"?entry=$entry&action=coclassedit&coid=$coid\">����</a>";
		cls_message::show($message);
	}
	$na = stidsarr(1);
	foreach($na as $k => $v){
		$db->query("UPDATE {$tblprefix}".atbl($k,1)." SET ccid$coid=0 WHERE ccid$coid='$ccid'",'SILENT');//���÷������Ϣ�����ĵ���ɾ��
	}
	
	//ɾ����صĽڵ�
	$db->query("DELETE FROM {$tblprefix}cnodes WHERE ename REGEXP 'ccid$coid=$ccid(&|$)'");
	cls_CacheFile::Update('cnodes');
	$db->query("DELETE FROM {$tblprefix}o_cnodes WHERE ename REGEXP 'ccid$coid=$ccid(&|$)'");
	cls_CacheFile::Update('o_cnodes');
	$db->query("DELETE FROM {$tblprefix}mcnodes WHERE mcnvar='ccid$coid' AND mcnid='$ccid'");
	cls_CacheFile::Update('mcnodes');
	
	$db->query("DELETE FROM {$tblprefix}coclass$coid WHERE ccid='$ccid'");
	adminlog('ɾ���ĵ�����');
	cls_CacheFile::Update('coclasses',$coid);
	//������Ŀ����
	$cnrels = cls_cache::Read('cnrels');
	foreach($cnrels as $k => $v){
		$alter = false;
		if(($v['coid'] == $coid) && isset($v['cfgs'][$ccid])){
			unset($v['cfgs'][$ccid]);
			$alter = true;
		}
		if($v['coid1'] == $coid){
			foreach($v['cfgs'] as $x => $y){
				$a = empty($y) ? array() : array_filter(explode(',',$y));
				if(in_array($ccid,$a)){
					$a = array_filter($a,"clearsameid");
					$v['cfgs'][$x] = implode(',',$a);
					$alter = true;
				}
			}
		}
		$alter && $db->query("UPDATE {$tblprefix}cnrels SET cfgs='".(empty($v['cfgs']) ? '' : addslashes(var_export($v['cfgs'],TRUE)))."' WHERE rid='$k'",'SILENT');
	}
	cls_CacheFile::Update('cnrels');
	cls_message::show('����ɾ�����', "?entry=$entry&action=coclassedit&coid=$coid");
}

# Ϊ�˼���Ӧ��ϵͳ����չ���֣���ʱ����
function fetch_arr(){
	if(!($coid = cls_env::GetG('coid'))) return array();
	return cls_catalog::InitialInfoArray($coid);
}
# Ϊ�˼���Ӧ��ϵͳ����չ���֣���ʱ����
function fetch_one($ccid){
	$ccid = intval($ccid);
	if(!($coid = cls_env::GetG('coid'))) return array();
	return cls_catalog::InitialOneInfo($coid,$ccid);
}
function clearsameid($var){
	global $ccid;
	return $var == $ccid ? false : true;
}
function pidsarr($coid,$maxlv = 0,$nospace = 0){//maxlvΪ0ʱ����ϵ���ã������ֶ�����
	global $cotypes;
	$narr = array();
	if(empty($cotypes[$coid])) return $narr;
	$maxlv || $maxlv = $cotypes[$coid]['maxlv'];
	$sarr = cls_cache::Read('coclasses',$coid);
	foreach($sarr as $k => $v){
		if(!$maxlv || $v['level'] < $maxlv - 1){
			$narr[$k] = ($nospace ? '' : str_repeat('&nbsp; &nbsp; ',$v['level'])).$v['title'];
		}
	}
	return $narr;
}