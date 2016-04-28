<?PHP
/*
** ���ű������¼����ط�����
** 1��ģ�帴�ϱ�ʶ���ɲ�ѯ�ִ��������ִ���action=��ʶ����
** 2���Զ�������ϵ�������������ִ���action:selfclass
** 3������λ����׷�ӵ�SQL��䣬pushmode=1
*/
(!defined('M_COM') || !defined('M_ADMIN')) && exit('No Permission');
aheader();
$dbfields = cls_cache::Read('dbfields');
empty($action) && $action = 'archives';
$typeid = __TypeInitID(@$typeid,$action);//ָ������id����chid��mchid��
$actiontitle = '';//�������
$pushmode = empty($pushmode) ? 0 : 1;//�Ƿ�����ɸѡsql(������archives,members,catalogs,commus);
$tablearr = array();
switch($action){
	case 'archives'://�ĵ��б�
		$actiontitle = '�ĵ��б�SQL����';//�������
		$NotypeidMsg = '��ѡ���ĵ�ģ��';
		$channels = cls_cache::Read('channels');

		if($typeid && !empty($channels[$typeid])){
			$stid = $channels[$typeid]['stid'];
			if($pushmode){
				$tablearr["archives$stid"] = array('{pre}','�ĵ�����','archives');//������������ƣ�ע�ͱ���(����Ϊ����)
			}else{
				$tablearr["archives$stid"] = array('a.','�ĵ�����','archives');//������������ƣ�ע�ͱ���(����Ϊ����)
				$tablearr["archives_$typeid"] = array('c.','ģ��ר�ñ�','');
			}
		}
		$filters = array(0 => '��ָ���ĵ�ģ��') + cls_channel::chidsarr();
	break;
	case 'members'://��Ա�б�
		$actiontitle = '��Ա�б�SQL����';//�������
		$mchannels = cls_cache::Read('mchannels');
		if($pushmode){
			$tablearr["members"] = array('{pre}','��Ա����','');//������������ƣ�ע�ͱ���(����Ϊ����)
		}else{
			$tablearr["members"] = array('m.','��Ա����','');//������������ƣ�ע�ͱ���(����Ϊ����)
			$tablearr["members_sub"] = array('s.','��Ա����','');//������������ƣ�ע�ͱ���(����Ϊ����)
			if($typeid && !empty($mchannels[$typeid])){
				$tablearr["members_$typeid"] = array('c.','ģ��ר�ñ�','');
			}
			$filters = array(0 => '��ָ����Աģ��') + cls_mchannel::mchidsarr();
		}
	break;
	case 'catalogs'://��Ŀ�б�
		$actiontitle = '��Ŀ�б�SQL����';//�������
		$pre = $pushmode ? '{pre}' : '';
		$cotypes = cls_cache::Read('cotypes');
		if($typeid && !empty($cotypes[$typeid])){
			$tablearr = array(
				"coclass$typeid" => array($pre,"[{$cotypes[$typeid]['cname']}]�����",'coclass'),//������������ƣ�ע�ͱ���(����Ϊ����)
			);
		}else{
			$tablearr = array(
				'catalogs' => array($pre,'��Ŀ��',''),//������������ƣ�ע�ͱ���(����Ϊ����)
			);
		}
		$filters = array(0 => '��Ŀ�б�(0)');
		foreach($cotypes as $k => $v){
			$filters[$k] = $v['cname']."($k)";
		}
	break;
	case 'farchives'://�����б�
	case 'adv_farchives'://����б�
		$actiontitle = ($action == 'farchives' ? '����' : '���').'�б�SQL����';//�������
		$fcatalogs = cls_cache::Read('fcatalogs');
		$tablearr = array(
			'farchives' => array('a.','��������',''),//������������ƣ�ע�ͱ���(����Ϊ����)
		);
		if($typeid && ($chid = @$fcatalogs[$typeid]['chid'])){
			$tablearr["farchives_$chid"] = array('c.','ģ��ר�ñ�','');//������������ƣ�ע�ͱ���(����Ϊ����)
		}
		$filters = array(0 => '��ָ����������') + cls_fcatalog::fcaidsarr();
	break;
	case 'commus'://�����б�
		$actiontitle = '�����б�SQL����';//�������
		$NotypeidMsg = '��ѡ�񽻻���Ŀ';
		$pre = $pushmode ? '{pre}' : '';
		$commus = cls_cache::Read('commus');
		$tablearr = array();
		if($typeid && !empty($commus[$typeid]) && ($tbl = @$commus[$typeid]['tbl'])){
			$tablearr[$tbl] = array($pre,"[{$commus[$typeid]['cname']}]������",'');//������������ƣ�ע�ͱ���(����Ϊ����)
		}
		$filters = array(0 => '��ָ��������Ŀ');
		foreach($commus as $k => $v){
			if($v['tbl']) $filters[$k] = $v['cname']."($k)";
		}
	break;
	case 'pushs'://�����б�
		$actiontitle = '�����б�SQL����';//�������
		$NotypeidMsg = '��ѡ������λ';
		$pre = '';
		$tablearr = array();
		if($typeid && $pusharea = cls_PushArea::Config($typeid)){
			$tablearr[cls_PushArea::ContentTable($typeid)] = array($pre,"[{$pusharea['cname']}]���ͱ�",'');//������������ƣ�ע�ͱ���(����Ϊ����)
		}
		$filters = array(0 => '��ָ������λ');
		$pushareas = cls_PushArea::Config();
		foreach($pushareas as $k => $v){
			$filters[$k] = $v['cname']."($k)";
		}
	break;
	case 'selfclass'://������������
		$actiontitle = '��Ŀ�Զ�������SQL����';//�������
		$NotypeidMsg = '��ָ��һ���ĵ�ģ��<br>����ȷʹ���ĸ�������Ϊ��������';
		$channels = cls_cache::Read('channels');
		if($typeid && !empty($channels[$typeid])){
			$stid = $channels[$typeid]['stid'];
			$tablearr = array(
				"archives$stid" => array('{$pre}','�ĵ�����','archives'),//������������ƣ�ע�ͱ���(����Ϊ����)
			);
		}
		$filters = array(0 => '��ָ���ĵ�ģ��') + cls_channel::chidsarr();
	break;
}
$orderbyarr = array('' => '','ASC' => '����','DESC' => '����',);
$dbtypearr = array(1 => array('text','mediumtext','longtext','char','varchar','tinytext',), 2 => array('tinyint','smallint','int','mediumint','bigint','float','double','decimal','bit','bool','binary',));
$modearr = array(
	'=' => 0,
	'>' => 1,
	'>=' => 1,
	'<' => 1,
	'<=' => 1,
	'!=' => 0,
	'LIKE' => 0,
	'NOT LIKE' => 0,
	'LIKE %...%' => 2,
	'LIKE %...' => 2,
	'LIKE ...%' => 2,
	'REGEXP' => 2,
	'NOT REGEXP' => 2,
	'IS NULL' => 0,
	'IS NOT NULL' => 0,
);

echo "<title>$actiontitle</title>";
//ͬaction�µ�typeid�л��б�
$filterbox = $actiontitle;
if(!empty($filters)){
	$filterbox .= " &nbsp;<select style=\"vertical-align: middle;\" name=\"tclass\" onchange=\"redirect('?entry=$entry&action=$action&pushmode=$pushmode&typeid=' + this.options[this.selectedIndex].value);\">";
	foreach($filters as $k => $v) $filterbox .= "<option value=\"$k\"".($typeid == $k ? ' selected' : '').">$v</option>";
	$filterbox .= "</select>";
}
tabheader($filterbox);
tabfooter();

if(submitcheck('bliststr') && !empty($fmdata)){
	$wherestr = $orderstr = '';
	$orderarr = array();
	foreach($fmdata as $k => $v){
		if(!empty($v['mode'])){
			if(in_array($v['mode'],array('IS NULL','IS NOT NULL',))){
				$wherestr .= ($wherestr ? ' AND ' : '').$k.' '.$v['mode'];
			}elseif(in_array($v['mode'],array('LIKE','NOT LIKE','REGEXP','NOT REGEXP',)) && $v['value'] != ''){
				$wherestr .= ($wherestr ? ' AND ' : '').$k." ".$v['mode']." '".$v['value']."'";
			}elseif(in_array($v['mode'],array('LIKE %...%','LIKE ...%','LIKE %...',)) && $v['value'] != ''){
				$wherestr .= ($wherestr ? ' AND ' : '').$k." ".str_replace(array('%...%','...%','%...'),array("'%".$v['value']."%'","'".$v['value']."%'","'%".$v['value']."'"),$v['mode']);
			}else{
				$wherestr .= ($wherestr ? ' AND ' : '').$k.' '.$v['mode']." '".$v['value']."'";
			}
		}
		if(!empty($v['order'])){
			$orderarr[$k.' '.$v['order']] = intval($v['prior']);
		}
	}
	if(!empty($orderarr)){
		asort($orderarr);
		foreach($orderarr as $k => $v) $orderstr .= ($orderstr ? ',' : '').$k;
	}
	tabheader('��ѯ�ִ����ɽ��');
	trbasic('ɸѡ�ִ�','view_wherestr',$wherestr,'textarea');
	if(!$pushmode && !in_array($action,array('selfclass',))){
		trbasic('�����ִ�','view_orderstr',$orderstr,'textarea');
	}
	tabfooter();
}
if(empty($tablearr)){
	cls_message::show(empty($NotypeidMsg) ? '��ѡ���������' : $NotypeidMsg);
}else{
	$TblNo = 0;
	foreach($tablearr as $dbtable => $cfg){
		$ititle = "$cfg[1] &nbsp;- &nbsp;$dbtable &nbsp;(���� ".($cfg[0] ? $cfg[0] : '��').")";
		if(!$TblNo){
			tabheader($ititle,'liststr',"?entry=$entry&action=$action&pushmode=$pushmode&typeid=$typeid",7);
		}else tabheader($ititle);
		trcategory(array('���',array('�ֶ�����','txtL'),array('�ֶ�����','txtL'),array('�ֶ�˵��','txtL'),'ɸѡģʽ','ɸѡֵ','����ģʽ','��������'));
		$query = $db->query("SHOW FULL COLUMNS FROM {$tblprefix}$dbtable",'SILENT');
		$tblfields = array();
		while($row = $db->fetch_array($query)){
			$types = explode(' ',$row['Type']);
			$tblfields[$row['Field']] = strtolower($types[0]);
		}
		$i = 1;
		foreach($tblfields as $k => $v){
			$var = $cfg[0].$k;
			echo "<tr class=\"txt\">".
				"<td class=\"txtC w30\">$i</td>\n".
				"<td class=\"txtL\"><b>$k</b></td>\n".
				"<td class=\"txtL\">$v</td>\n".
				"<td class=\"txtL\">".DbfieldComment(empty($cfg[2]) ? $dbtable : $cfg[2],$k)."</td>\n".
				"<td class=\"txtC\"><select style=\"vertical-align: middle;\" name=\"fmdata[$var][mode]\">".makeoption(thismodearr($v),empty($fmdata[$var]['mode']) ? '' : $fmdata[$var]['mode'])."</select></td>\n".
				"<td class=\"txtC\"><input type=\"text\" size=\"20\" name=\"fmdata[$var][value]\" value=\"".(empty($fmdata[$var]['value']) ? '' : mhtmlspecialchars(stripslashes($fmdata[$var]['value'])))."\"></td>\n".
				"<td class=\"txtC w50\"><select style=\"vertical-align: middle;\" name=\"fmdata[$var][order]\">".makeoption($orderbyarr,empty($fmdata[$var]['order']) ? '' : $fmdata[$var]['order'])."</select></td>\n".
				"<td class=\"txtC w80\"><input type=\"text\" size=\"4\" name=\"fmdata[$var][prior]\" value=\"".(empty($fmdata[$var]['prior']) ? 0 : mhtmlspecialchars(stripslashes($fmdata[$var]['prior'])))."\"></td>\n".
				"</tr>";
			$i ++;
		}
		$TblNo ++;
		if($TblNo < count($tablearr)){
			tabfooter();
		}else tabfooter('bliststr','����');
	}

}
function DbfieldComment($tbl,$field){
	$dbfields = cls_cache::Read('dbfields');
	return empty($dbfields[$tbl.'_'.$field]) ? '-' : $dbfields[$tbl.'_'.$field];
}
function thismodearr($type){
	global $modearr,$dbtypearr;
	$type = str_replace(strstr($type,'('),'',$type);
	$retarr = array('' => '');
	foreach($modearr as $k => $v){
		if(!$v || !in_array($type,$dbtypearr[$v])) $retarr[$k] = $k;
	}
	return $retarr;
}
function __TypeInitID($typeid,$action = 'archives'){
	$typeid = empty($typeid) ? '' : trim($typeid);
	if(in_array($action,array('farchives','adv_farchives','pushs',))){
		$typeid = cls_string::ParamFormat($typeid);
	}else{
		$typeid = empty($typeid) ? 0 : max(0,intval($typeid));
	}
	return $typeid;
}


