<?php

// ע����չ
function reg_exhouse(&$newuser,$mchid,$fxpid){
	$mchid==2 && $newuser->updatefield('fxpid',intval($fxpid),'members_sub'); 
}

// �ر�ģ������
function cmod_cfgs(){
	$cfgs = array( //��������
		'fenxiao'=>array(
			'cname'=>'¥�̷���',
			'mmenu'=>'339,340,341',
			'nodes'=>'114,119', //114,119
			'o_nodes'=>'', 
			'u_nodes'=>'',
			'sptabs'=>'66',
			'mchids'=>'',
		),
		'shangye'=>array(
			'cname'=>'��ҵ�ز�',
			'mmenu'=>'342,343,344,345,346,347',
			'nodes'=>'120,121,122,123,124,125', 
			'o_nodes'=>'', 
			'u_nodes'=>'',
			'sptabs'=>'68,69,70,71,72,73',
			'mchids'=>'',
		),
		/*'wenda'=>array(
			'cname'=>'�ʴ�',
			'mmenu'=>'333,332',
			'nodes'=>'101',
			'o_nodes'=>'', 
			'u_nodes'=>'',
			'sptabs'=>'22',
			'mchids'=>'',
		),*/
		
/*		
		'jiazhuang'=>array(
			'cname'=>'��װ',
			'mmenu'=>'302,303,304,305,307,308,312,313,314',
			'nodes'=>'35', // 35,110
			'o_nodes'=>'',  
			'u_nodes'=>'', 
			'sptabs'=>'17,18,19,20,29',
			'mchids'=>'11,12',
			'cnode_u'=>'504',
		),
		
*/		
	);
	return $cfgs;
}

// close_model(cmod) ��ѡģ��ر������ж����
// typ: seta(��������array), tpl(�ж��Ƿ�ر�), amenu(�������˵�), setdb(��������)
// $cfgarr: 1. ������ģ��
// ��¥�̷�������ҵ�ز����ʴ𣬼�װ
//   fenxiao, shangye, wenda, jiazhuang
// $cfgarr: 2. ģ����: 
//    mmenu(��Ա�˵�) - [��mmenus]��Ҫ����Ļ�Ա�˵�,����/?,��ʼ���ü�[ϵͳ����-��Ա����-��Ա���Ĳ˵�]
//    nodes(�ڵ���Ϸ���) - [��cnodes:[tid]=xxx]��Ҫ����Ľڵ���Ϸ���,�ر�/?,��ʼ���ü�[��վ�ܹ�-�ڵ����-�ڵ���Ϸ���-(����)��һ�е�����]
//    o_nodes(�ֻ��ڵ���Ϸ���) - [o_cnodes]
//    u_nodes(��Ա�ڵ��б�)
//    sptab(ע���Ƿֱ�ID,����ģ��ID)
//    mchid(��Աģ��,��Ա����)
//    (��δ����)amenu(����˵�),mnodes��ԱƵ���ڵ�,�ϼ�(����Ҫ����)
function cmod($model,$type='tpl'){
	$cfgarr = cmod_cfgs();
	switch($type){
		case 'seta': 
			return $cfgarr;
		break;
		case 'tpl': 
			$exconfigs = cls_cache::cacRead('exconfigs',_08_EXTEND_SYSCACHE_PATH);;
			if(isset($exconfigs['closemstr'])){ 
				$closemstr = $exconfigs['closemstr']; unset($exconfigs);
				if(strstr(",$closemstr,","$model,")) return true;
				else return false;
			}else{ 
				return false; 
			}
		break;
		//case 'amenu': 
		//break;
		case 'setdb': 
			$db = _08_factory::getDBO();
			$tblprefix = cls_env::getBaseIncConfigs('tblprefix');
			global $closestr;
			$open_mmenu = ''; $close_mmenu = '';
			$open_nodes = ''; $close_nodes = '';
			$open_o_nodes = ''; $close_o_nodes = '';
			$open_u_nodes = ''; $close_u_nodes = '';
			$open_sptabs = ''; $close_sptabs = '';
			$open_mchids = ''; $close_mchids = '';
		    foreach($cfgarr as $k=>$v){
			    if(strstr(",$closestr,","$k,")){
					$close_mmenu .= empty($v['mmenu']) ? '' : (empty($close_mmenu) ? '' : ',')."$v[mmenu]"; 
					$close_nodes .= empty($v['nodes']) ? '' : (empty($close_nodes) ? '' : ',')."$v[nodes]"; 
					$close_o_nodes .= empty($v['o_nodes']) ? '' : (empty($close_o_nodes) ? '' : ',')."$v[o_nodes]";
					$close_u_nodes .= empty($v['u_nodes']) ? '' : (empty($close_u_nodes) ? '' : ',')."'$v[u_nodes]'"; 
					$close_sptabs .= empty($v['sptabs']) ? '' : (empty($close_sptabs) ? '' : ',')."$v[sptabs]"; 
					$close_mchids .= empty($v['mchids']) ? '' : (empty($close_mchids) ? '' : ',')."$v[mchids]"; 
					if(!empty($v['cnode_u'])) cmod_ccnode($v['cnode_u'],$k,1);
				}else{
					$open_mmenu .= empty($v['mmenu']) ? '' : (empty($open_mmenu) ? '' : ',')."$v[mmenu]"; 
					$open_nodes .= empty($v['nodes']) ? '' : (empty($open_nodes) ? '' : ',')."$v[nodes]";
					$open_o_nodes .= empty($v['o_nodes']) ? '' : (empty($open_o_nodes) ? '' : ',')."$v[o_nodes]";
					$open_u_nodes .= empty($v['u_nodes']) ? '' : (empty($open_u_nodes) ? '' : ',')."'$v[u_nodes]'";  
					$open_sptabs .= empty($v['sptabs']) ? '' : (empty($open_sptabs) ? '' : ',')."$v[sptabs]";
					$open_mchids .= empty($v['mchids']) ? '' : (empty($open_mchids) ? '' : ',')."$v[mchids]";
					if(!empty($v['cnode_u'])) cmod_ccnode($v['cnode_u'],$k,0);
				}
		    }
			// mmenu����Ա�˵�
			$open_mmenu && $db->query("UPDATE {$tblprefix}mmenus SET available=1 WHERE mnid IN($open_mmenu)"); //����
			$close_mmenu && $db->query("UPDATE {$tblprefix}mmenus SET available=0 WHERE mnid IN($close_mmenu)"); //
			cls_CacheFile::Update('mmenus');
			// nodes, �ڵ����÷���
			$cnconfigs = cls_cache::Read('cnconfigs');
			cmod_tcnode($cnconfigs,$open_nodes,0,'');
			cmod_tcnode($cnconfigs,$close_nodes,1,'');
			// o_nodes, �ڵ����÷���
			$o_cnconfigs = cls_cache::Read('o_cnconfigs');
			cmod_tcnode($o_cnconfigs,$open_o_nodes,0,'o_');
			cmod_tcnode($o_cnconfigs,$close_o_nodes,1,'o_');
			// mcnodes����ԱƵ���ڵ�
			$open_u_nodes && $db->query("UPDATE {$tblprefix}mcnodes SET closed=0 WHERE ename IN($open_u_nodes)"); 
			$close_u_nodes && $db->query("UPDATE {$tblprefix}mcnodes SET closed=1 WHERE ename IN($close_u_nodes)"); 
			// sptab �ֱ�̬
			$open_sptabs && $db->query("UPDATE {$tblprefix}splitbls SET nostatic='0' WHERE stid IN($open_sptabs)"); 
			$close_sptabs && $db->query("UPDATE {$tblprefix}splitbls SET nostatic='1' WHERE stid IN($close_sptabs)"); 
			// mchid ��Ա���� 
			$open_mchids && $db->query("UPDATE {$tblprefix}members SET checked='1' WHERE mchid IN($open_mchids)"); 
			$close_mchids && $db->query("UPDATE {$tblprefix}members SET checked='0' WHERE mchid IN($close_mchids)");
			cls_CacheFile::Update('cnodes');
			cls_CacheFile::Update('o_cnodes');
			cls_CacheFile::Update('mcnodes');
			cls_CacheFile::Save($cnconfigs,'cnconfigs','cnconfigs');
			cls_CacheFile::Save($o_cnconfigs,'o_cnconfigs','o_cnconfigs');
			cls_CacheFile::Update('splitbls');
			return $model;
		break;
	}
}
//��Ŀ�ڵ㴦��-��tid
function cmod_tcnode(&$cncfgs,$str_nodes,$val,$o_=''){
	global $db,$tblprefix;
	if($str_nodes){
		$arr =  explode(",",$str_nodes);
		foreach($arr as $k){ 
			if(!isset($cncfgs[$k])) continue;
			$cncfgs[$k]['closed'] = 0; 
			$sql = "UPDATE {$tblprefix}{$o_}cnodes SET closed='$val' WHERE tid='".$cncfgs[$k]['tid']."'";
			$db->query($sql); //echo "<br>$sql";
		}
	}
}
//��Ŀ�ڵ㴦��-����Ŀ
function cmod_ccnode($cnode_u,$key,$val,$o_=''){
	global $db,$tblprefix;
	$cnode_a = is_array($cnode_u) ? $cnode_u : explode(',',$cnode_u);
	if($cnode_a){ foreach($cnode_a as $key){
		$cnode_u = cls_catalog::cnsonids($key,cls_cache::Read('catalogs'));
		foreach($cnode_u as $k) $db->query("UPDATE {$tblprefix}{$o_}cnodes SET closed=$val WHERE ename REGEXP 'caid=$k(&|$)'",'SILENT');
	} } 
}

// ���͹�˾�µľ�����(mids)
// �ƻ�����,ǰ̨�õ�
function get_subMids($mid){
	$db = _08_factory::getDBO();
	$tblprefix = cls_env::getBaseIncConfigs('tblprefix');
	$mid = intval($mid); $mids = '';
	$sql = "SELECT mid FROM {$tblprefix}members m WHERE m.mchid=2 AND pid4='$mid'"; // AND incheck4=1
	$query = $db->query($sql);
	while($row = $db->fetch_array($query)){    
		$mids .= ','.$row['mid'];
	}
	$mids = empty($mids) ? "-1" : substr($mids,1); 
	return $mids;
}

// ¥�̷���:����
function get_fxcfgs(){
	$exconfigs = cls_cache::cacRead('exconfigs',_08_EXTEND_SYSCACHE_PATH);
	$exfenxiao = $exconfigs['distribution']; // Array ( [num] => 3 [pnum] => 100 [vtime] => 15 [unvnum] => 10 [fxwords] => �ҷ������ҵ�Ӷ���ҷ��ƣ���׬���ˣ� ) print_r($exfenxiao);
	$exfenxiao['num'] = empty($exfenxiao['num']) ? 3 : max(1,intval($exfenxiao['num']));
	$exfenxiao['vtime'] = empty($exfenxiao['vtime']) ? 15 : max(1,intval($exfenxiao['vtime']));
	$exfenxiao['pnum'] = empty($exfenxiao['pnum']) ? 100 : max(1,intval($exfenxiao['pnum']));
	$exfenxiao['unvnum'] = empty($exfenxiao['unvnum']) ? 10 : max(1,intval($exfenxiao['unvnum']));
	return $exfenxiao;
}
// ¥�̷���:�¼���Աids
// $mids = get_xjmids(965); -=> ���� 847,971 �� ��
function get_xjmids($mid){
	$db = _08_factory::getDBO(); 
	$ids = ''; //SELECT c.mid FROM {$tblprefix}members_2 c INNER JOIN {$tblprefix}members_sub s ON s.mid=c.mid WHERE s.fxpid=965;
    $db->select('c.mid')->from('#__members_2 c')
		->innerJoin('#__members_sub s')->_on('s.mid=c.mid')
		->where(array('s.fxpid' => $mid))
		->exec(); //->fetch()	  
    while($row = $db->fetch()){
        $ids .= (empty($ids) ? '' : ',').$row['mid']; 
    }
	return $ids;
}
// ¥�̷���:�Ƽ�data; 
// ids : mid / cids
// type=self(���˿���ȡ),subs(�¼�����ȡ),
function get_fxlist($ids=0,$type='self'){
	$db = _08_factory::getDBO();
	if($type=='subs'){
		$mids = get_xjmids($ids);
		empty($mids) && $mids = '-1';	
	}
	$data = array(); 
    $db->select('*')->from('#__commu_customer');
	if($type=='self'){
		$db->where('mid')->_in($ids)
		->_and(array('status'=> 3))
		->_and(array('yjbase'=> 0));
	}elseif($type=='subs'){ //is_numeric($type)
		$db->where('mid')->_in($mids)
		->_and(array('status'=> 3))
		->_and(array('yjextra'=> 0));
	}else{ //if()
		$db->where('cid')->_in($ids);	
	}	
	$db->exec(); //->fetch()	  
    while($row = $db->fetch()){
        $okaid = $row['okaid'];
		$arc = new cls_arcedit;
		$arc->set_aid($okaid,array('chid'=>113));
		$pinfo = $arc->archive; //$pinfo && cls_ArcMain::Parse($pinfo);	
		$row['lpmc'] = $pinfo['lpmc'];
		$data[] = $row; 
    }
	return $data;
}

// ¥�̷���:Ӷ����ϸ 
function get_yjdetail($data=array(),$type='self'){
	$yjsum = 0; $stri = ''; $cids = '';
	foreach($data as $v){
		$orgyj = $v['okayj']; $iyj = $type=='self' ? $orgyj : intval($orgyj*0.1);
		$yjsum += $iyj; 	
		$stri .= "<tr>";
		$stri .= "<td class=\"item\">$v[xingming]</td>\n";	
		$stri .= "<td class=\"item\">$v[dianhua]</td>\n";
		$stri .= "<td class=\"item\">$v[lpmc]</td>\n";	
		$stri .= "<td class=\"item\">$iyj</td>\n";	
		/*
		if($type=='self'){
			$stri .= "<td class=\"item\" style='width:80px;'>$v[mname]</td>\n";		
		}else{
			
		}*/
		$stri .= "<td class=\"item\">$v[mname]</td>\n";	
		$stri .= "<td class=\"item\">".($type=='self' ? "$orgyj * 100%" : "$orgyj * 10%")."</td>\n";
		$stri .= "</tr>";
		$cids .= (empty($cids) ? '' : ',').$v['cid'];
	}
	
	$stri .= "<tr>";
	$stri .= "<td class=\"item\">(С��)</td>\n";	
	$stri .= "<td class=\"item\">&nbsp;</td>\n";
	$stri .= "<td class=\"item\">&nbsp;</td>\n";	
	if($yjsum==0){
		$stri .= "<td class=\"item\">0</td>\n";	
	}else{
		$stri .= "<td class=\"item\">$yjsum</td>\n";	
	}
	$stri .= "<td class=\"item\" style='width:120px;'>&nbsp;</td>\n";
	$stri .= "</tr>";
	return array($yjsum,$stri,$cids);
}

//������ˢ�º��ٴ�����ˢ�£����ݿ�members��ˢ�������ͽ��Ĳ���
function _kouchu($_num,$rules){
	$db = _08_factory::getDBO();
	$tblprefix = cls_env::getBaseIncConfigs('tblprefix');
	$curuser = cls_UserMain::CurUser();
	$memberid = $curuser->info['mid'];
	$_shengyu_ts = 0;
	$_shengyu_money = 0;
	
	if(!empty($curuser->info['freeyys'])){
		$_shengyu_ts = $curuser->info['freeyys'] - abs($_num);		
		//����ˢ�������������ǲ�������ԤԼˢ������Ҫ�����
		if($_shengyu_ts < 0)$_shengyu_money = $curuser->info['currency0'] - abs($_shengyu_ts) * $rules['price'];
	}else{
		//û��ˢ�����������ֻ���ֽ�����
		$_shengyu_money = $curuser->info['currency0'] - abs($_num) * $rules['price'];	
		$_shengyu_ts = -1;//�޸�_shengyu_ts��ֵ�������ܽ��и���freeyys �� currency0�Ĳ���
	}	
	if($_shengyu_money < 0)cls_message::show('�����ֽ��ʻ����㣬���ֵ��',M_REFERER);
	
	if($_shengyu_ts >= 0)$db->query("UPDATE {$tblprefix}members SET freeyys= '$_shengyu_ts' WHERE mid = '$memberid'");	
	
	if(($_shengyu_ts < 0) && ($_shengyu_money >= 0) )$db->query("UPDATE {$tblprefix}members SET freeyys= '0',currency0 = '$_shengyu_money' WHERE mid = '$memberid'");
	
}

//������ˢ�º��ٴ�����ˢ�£����ݿ��commu_yuyue�Ĳ����ɾ������
function _control_sql($info){
	$db = _08_factory::getDBO();
	$tblprefix = cls_env::getBaseIncConfigs('tblprefix');
	$_old_yytime = $info['_old_yytime'];
	$_new_yytime = $info['_new_yytime'];
	$aid = intval($info['aid']);
	$chid = $info['chid'];
	$_no_reffesh_arr = $info['_no_reffesh_arr'];
	$curuser = cls_UserMain::CurUser();
	$memberid = $curuser->info['mid'];
	
	$_dele_arr = array();
	$_insert_arr = array();	
	$_dele_arr = array_diff($_old_yytime,$_new_yytime);	
	$_insert_arr = array_diff($_new_yytime,$_old_yytime);	
	if(!empty($_dele_arr)){
		foreach($_dele_arr as $k => $v){
			$v = str_replace("'","",$v);
			is_numeric($v) && $db->query("DELETE FROM {$tblprefix}commu_yuyue WHERE aid = '$aid' AND refreshtime = '$v'");
		}
	}
	//���û��ʵʱˢ�£�Ҳ���Ǵ���_no_reffesh_arr
	//��ô��ûˢ�µ�ɾ����Ȼ�����
	if(!empty($_no_reffesh_arr)){
		$_i = 1;
		foreach($_no_reffesh_arr as $k => $v){
			//��Ϊ_no_reffesh_arr��ŵ������Ǹ���ˢ��ʱ��ĵ������������ģ����Ե�һ��ʱ�������µ�Ҫˢ�µ�ʱ��
			$v = str_replace("'","",$v);
			if($_i == 1) $db->query("UPDATE {$tblprefix}".atbl($chid)." set refreshdate = '$v' WHERE chid = '$chid'  AND aid = '$aid ' AND mid='$memberid'");
			$db->query("DELETE FROM {$tblprefix}commu_yuyue WHERE aid = '$aid' AND refreshtime = '$v'");
			$_i++;
		}
	}
	
	if(!empty($_insert_arr)){
		foreach($_insert_arr as $k => $v){
			$v = str_replace("'","",$v);
			is_numeric($v) && $db->query("INSERT INTO {$tblprefix}commu_yuyue set aid = '$aid',refreshtime = '$v',chid = '$chid',mid='$memberid'");
		}
	}
}

//��������ˢ��ʱ���Ѿ����ˣ����ǻ�û��ˢ�µ�����
function not_be_refresh($aid,$_today_time){
	$timestamp = TIMESTAMP; 
	$db = _08_factory::getDBO();
	$tblprefix = cls_env::getBaseIncConfigs('tblprefix');
	$_no_refresh_arr = array();
	$aid = intval($aid);
	$_today_time = preg_replace('/[^\d]/', '', $_today_time); //str_replace("'","",$_today_time);
	$_query = $db -> query("SELECT refreshtime FROM {$tblprefix}commu_yuyue WHERE aid = '$aid' AND refreshtime >= '$_today_time' AND refreshtime <= $timestamp ORDER BY refreshtime DESC");
	while($_rows = $db->fetch_array($_query)){
		$_no_refresh_arr[$_rows['refreshtime']] = $_rows['refreshtime'];
	}
	return $_no_refresh_arr;
}

function search_yytime($aid,$_today_time){
	$db = _08_factory::getDBO();
	$tblprefix = cls_env::getBaseIncConfigs('tblprefix');
	$_yytime_arr = array();
	$aid = intval($aid);
	$_today_time = preg_replace('/[^\d]/', '', $_today_time);
	$_query = $db -> query("SELECT refreshtime FROM {$tblprefix}commu_yuyue WHERE aid = '$aid' AND refreshtime >= '$_today_time' ORDER BY refreshtime DESC");
	while($_rows = $db->fetch_array($_query)){
		$_yytime_arr[] = $_rows['refreshtime'];
	}
	return $_yytime_arr;
}

// �ܱ�---�Զ�����[¥��/С��]
// $aid:�ĵ�id
// $chid:ģ��id
// $mapstr:��ͼ����
function ex_zhoubian($aid,$chid=8,$mapstr='',$chk=0){
	$db = _08_factory::getDBO();
	$tblprefix = cls_env::getBaseIncConfigs('tblprefix');
	$mconfigs = cls_cache::Read('mconfigs');
	$circum_km = empty($mconfigs['circum_km']) ? 3 : floatval($mconfigs['circum_km']);
	$aid = intval($aid);
	$map = explode(',',@$mapstr.","); 
	if(strlen($map[0])==0 || strlen($map[1])==0) return;
	if($chid==8){
		$tpl = "inid='$aid',pid='[tid]'";
		$ch = 4;
	}else{ //if($chid==4){
		$tpl = "inid='[tid]',pid='$aid'";
		$ch = 8;
	}
	$maps = cls_dbother::MapSql(floatval($map[0]), floatval($map[1]), $circum_km, 1, 'dt'); // dt_0>=22.456 AND dt_0<=52.456 AND dt_1>=50.33 AND dt_1<=150.33
	$sqla = "SELECT aid,dt_0,dt_1 FROM {$tblprefix}".atbl($ch)." WHERE $maps "; 
	//".(empty($sids) ? "" : "AND aid NOT IN(".substr($sids,1).")")."
	$query = $db->query($sqla); //echo $sqla;
	while($r = $db->fetch_array($query)){ 
		$_aid = $r['aid']; $_dt = $r['dt_0'].$r['dt_1'];
		$_tpl = str_replace("[tid]","$_aid",$tpl);
		if($chk && $db->result_one("SELECT abid FROM {$tblprefix}aalbums WHERE ".str_replace(","," AND ",$_tpl))) continue;
		$sqlb = "INSERT INTO {$tblprefix}aalbums SET arid='1',$_tpl,incheck='1'";
		//echo "<br>$_aid,$_dt,$sqlb";
		$db->query($sqlb);
	} //die();
}

// ��Ŀ/��ϵ�£�������ID 
// $ids, ��id����","�ֿ�������� ��1,593����
// $coid��0 - ��Ŀ��n - ��ϵ
// $self��1 - �����Լ���0�������Լ� 
// $diff���ų�id����","�ֿ�������� "2,3,4"��
function ex_get_msuns($ids='',$coid=0,$self=1,$diff=''){
	$t = explode(',',$ids);
	$r = array();
	foreach($t as $k){ //echo "$k,";
		$a = sonbycoid($k,$coid,$self);
		$r = array_merge($r,$a);
	} 
	if(empty($r)) $r[] = '-1';
	if(!empty($diff)){ 
		$t = explode(',',$diff);
		$d = array();
		foreach($t as $k){
			$a = sonbycoid($k,$coid,1);
			$d = array_merge($d,$a);
		}
		if(!empty($d)) $r = array_diff($r,$d);
	}
	return $r;
}

// �ĵ��µĺϼ�����, ԭ����Ϊgethjnum
function ex_gethjnum($aid=0,$chid,$arid = 0){
	$db = _08_factory::getDBO();
	$tblprefix = cls_env::getBaseIncConfigs('tblprefix');
	$aid = intval($aid);
	$chid = intval($chid);
	$arid = intval($arid);
	if($arid){
		$abrel = cls_cache::Read('abrel',$arid);
		if(empty($abrel['tbl'])){
			$sql = "SELECT COUNT(*) FROM {$tblprefix}".atbl($chid)." a WHERE a.chid='$chid' AND a.pid$arid='$aid'";
		}else{
			$sql = "SELECT count(*) FROM {$tblprefix}".atbl($chid)." a right join {$tblprefix}$abrel[tbl] b on b.inid=a.aid WHERE a.chid='$chid' AND b.pid='$aid'";
		}
	}
	return $db->result_one($sql);
}
// �ĵ��µĽ�������, ԭ����Ϊgetjhnum
function ex_getjhnum($aid=0,$cuid=0){
	$db = _08_factory::getDBO();
	$tblprefix = cls_env::getBaseIncConfigs('tblprefix');
	$aid = intval($aid);
	$cuid = intval($cuid);
	if($commu = cls_cache::Read('commu',$cuid)){
        $sqlstr = '';
        //��Ѷ���ۣ�¥�����ԣ��ʴ��ֻɸѡ���ۣ��ų��ظ�
        if(in_array($cuid,array(1,2,37))){
            $sqlstr .= " AND cu.tocid = '' ";
        }
		return $db->result_one("SELECT count(*) FROM {$tblprefix}$commu[tbl] cu WHERE cu.aid='$aid' $sqlstr ");
	}
}

//ͳ�ƽ����У����۵Ļظ�����
function huifu_count($cid,$cuid){
	$db = _08_factory::getDBO();
	$tblprefix = cls_env::getBaseIncConfigs('tblprefix');
	$cid = intval($cid);
	$cuid = intval($cuid);
	if($commu = cls_cache::Read('commu',$cuid)){
		return $db->result_one("SELECT count(*) FROM {$tblprefix}$commu[tbl] cu WHERE cu.tocid='$cid'");
	}
}


// ��Դ��ͼ, ԭ����Ϊfangyuan_imgnum
function exfy_imgnum($chid,$aid,$comtent){
	$db = _08_factory::getDBO();
	$tblprefix = cls_env::getBaseIncConfigs('tblprefix');
    $comtent = htmlspecialchars_decode($comtent);//��ʵ�廯��htmlת����ͨ�ַ����������ƥ��<��ƥ�䲻��
	$imgnum = substr_count($comtent,'<img');    
	$chid = intval($chid);
	$aid = intval($aid);
	$imgsql = "UPDATE {$tblprefix}".atbl($chid)." SET imgnum='$imgnum' WHERE aid ='$aid'";
	$db->query($imgsql);
}

// ȥ���Ѿ�����(��ʾ����ϵ)
// //$oA->setvar('coids',array(2,3,4));
function resetCoids(&$coids, $a = array()){
	if(empty($a)||empty($coids)) return;
	foreach($coids as $k=>$v){ 
		if(in_array($v,$a)) unset($coids[$k]);
	}
	//return $coids;
}

// �����ֶ� ��չʾ(��̨,��Ա����:�۸�༭ʹ��)
function p_editfield($info){
	$fn = $info['fn'];
	$a_field = $info['a_field'];
	$fix_fields = $info['fix_fields'];
	$chid = $info['chid'];
	$arc = $info['arc'];
	
	if(in_array($fn,$fix_fields)) die($fn);
	$fix_fields[] = $fn;
	if(($field = cls_cache::Read('field',$chid,$fn)) && $field['available']){
		$a_field->init($field,isset($arc->archive[$fn]) ? $arc->archive[$fn] : '');
		$a_field->trfield('fmdata');
	}
}

// �����ĵ�����/�޸���,��ϵ����
function relCcids($ccid1=0, $ccid2=0, $rid=0, $must=0, $fmdata='fmdata', $val1=0, $val2=0){
	global $chid, $fcdisabled2; //����û������:$_no_tr_flag
	$cotypes = cls_cache::Read('cotypes');
	if($ccid1 && empty($cotypes[$ccid1])) return;
	if($ccid2 && empty($cotypes[$ccid2])) return;
	//*
	$ucarr1 = cls_catalog::uccidsarr($ccid1,$chid,0,1,1);
	$ucarr2 = cls_catalog::uccidsarr($ccid2,$chid,0,1,1);
	$ccarr1 = array();
	$ccv1 = $val1; $ccv2 = $val2; 
	foreach($ucarr1 as $k=>$v){
		$ccarr1[$k] = $v['title'];
	}//*/
	$str = '';
	$str .= "<select style=\"vertical-align: middle;\" name=\"{$fmdata}[ccid$ccid1]\" id=\"{$fmdata}[ccid$ccid1]\" ".($must ? 'rule="must"' : '')." autocomplete=\"off\">".makeoption(array('0' => '��ѡ��')+$ccarr1,$ccv1)."</select>";
    
    // ����ϵͳ���ã������������ر���Ȧ��ϵ����
    if (empty($fcdisabled2))
    {
	   $str .= "&nbsp; <b>{$cotypes[$ccid2]['cname']}</b> <select style=\"vertical-align: middle;\" name=\"{$fmdata}[ccid$ccid2]\" id=\"{$fmdata}[ccid$ccid2]\" >".makeoption(array('0' => '��ѡ��'))."</select>";
    }
	/* relCcids_js */
	$str .= relCcids_js($ccid1, $ccid2, $rid, $val1, $val2, $fmdata);
	trbasic(($must ? '<font color="red"> * </font>' : '').$cotypes[$ccid1]['cname'],'',$str,'');
}

// �����ĵ�����/�޸���,����js
function relCcids_js($ccid1=0, $ccid2=0, $rid=0, $val1=0, $val2=0, $fmdata='fmdata'){
	global $chid;
	$cnrel = cls_cache::Read('cnrel',$rid);
	$ucarr1 = cls_catalog::uccidsarr($ccid1,$chid,0,1,1);
	$ucarr2 = cls_catalog::uccidsarr($ccid2,$chid,0,1,1);
	$ccarr1 = array();
	$ccv1 = $val1; $ccv2 = $val2; 
	$str = "";
	foreach($ucarr1 as $k=>$v){
		$ccarr1[$k] = $v['title'];
	}	
	$str .= "<script> var c{$ccid1}c{$ccid2}data = [";
	foreach($cnrel['cfgs'] as $k=>$v){
		foreach(explode(',',$v) as $sc=>$sv){
			$str .="[$sv,$k,'".addslashes(@$ucarr2[$sv]['title'])."'],";
		}
	}
	$str .= "];\n";
	//$str .= "var ccv2 = '$ccv2';";
	$str .= "var ccid$ccid1 = document.getElementById('{$fmdata}[ccid$ccid1]');";
	$str .="var ccid$ccid2 = document.getElementById('{$fmdata}[ccid$ccid2]');
			ccid$ccid1.onchange=function(){
				ccid$ccid2.options.length = 1;
				var v, i = 0;
				while(v = c{$ccid1}c{$ccid2}data[i++]){
					if(v[1]==this.value){
						ccid$ccid2.options.add(new Option(v[2],v[0]));
					}
				}
			};
			ccid$ccid1.onchange();
			if('$ccv2'>'0'){
				for(var j=0;j<ccid$ccid2.options.length;j++){
					if(ccid$ccid2.options[j].value=='$ccv2'){
						ccid$ccid2.options[j].selected=true;
					}
				}
			}
		";
	$str .= "</script>\n";
	return $str;
}

/**
 * �����ĵ��б���,��ϵ����
 * @param int $ccid1  ��ϵID
 * @param int $ccid2  ��ϵID
 * @param int $rid    ��ϵ����ID
 */
function RelCcjs($chid,$ccid1=0,$ccid2=0,$rid=0){	
	$cotypes = cls_cache::Read('cotypes');
	$cnrel = cls_cache::Read('cnrel',$rid);
	if($ccid1 && empty($cotypes[$ccid1])) return;
	if($ccid2 && empty($cotypes[$ccid2])) return;
	$ucarr1 = cls_catalog::uccidsarr($ccid1,$chid,0,1,1);//cls_cache::Read('coclasses',$ccid1);
	$ucarr2 = cls_catalog::uccidsarr($ccid2,$chid,0,1,1);
	$ccid2var = "var c{$ccid1}c{$ccid2}data=[";
	foreach($cnrel['cfgs'] as $k=>$v){
		foreach(explode(',',$v) as $sc=>$sv){
			$ccid2var .="[$sv,$k,'".addslashes(@$ucarr2[$sv]['title'])."'],";
		}
	}
	$ccid2var = substr($ccid2var,0,(strlen($ccid2var)-1));
	$ccid2var .= "];";
	echo "<script type='text/javascript'>
	$ccid2var
	var ccid$ccid1=document.getElementsByName('ccid$ccid1')[0];
	var ccid$ccid2=document.getElementsByName('ccid$ccid2')[0];
	ccid$ccid1.onchange=function(){
		var ccv = ccid$ccid1.options[ccid$ccid1.selectedIndex].value;
		ccid$ccid2.options.length=1;
		for(var i=0;i<c{$ccid1}c{$ccid2}data.length;i++){
			if(c{$ccid1}c{$ccid2}data[i][1]==ccv){
				ccid$ccid2.options.add(new Option(c{$ccid1}c{$ccid2}data[i][2],c{$ccid1}c{$ccid2}data[i][0]));
			}
		}
	}
	ccid$ccid1.onchange();
	</script>";
}

//��̨�д˺�����ǰ̨��û���֡�����̨��װ��ᵽ�˴�
function mtime_diff($t,$needsuffix = 0,$line = 0){
	$timestamp = TIMESTAMP; 
	$line || $line = $timestamp;
	$diff = $t - $line;
	$suffix = $diff > 0 ? '��' : 'ǰ';
	$diff = abs($diff);
	$na = array(31536000 => '��',2592000 => '��',86400 => '��',3600 => 'ʱ',60 => '��',);
	$str = '';
	foreach($na as $k => $v){
		if($x = floor($diff / $k)){
			$str = $x.$v;
            if($x) break;
		}		
	}
	$str || $str = $diff.'��';
	return $str.($needsuffix ? $suffix : '');
}

//��̨>>�������>>¥��/���ַ�/���� �ļ۸�����
function price_trend($chid,$avg_field,$cuid){
	$db = _08_factory::getDBO();
	$tblprefix = cls_env::getBaseIncConfigs('tblprefix');
    //��ʼ��������ǰ�µ�ǰ12���µļ۸�����
    //�Ȳ�ѯ���µ������Ƿ���ڣ���Ϊ���϶����Զ����ɵģ�����������ϴ��ڣ��Ǿͱ���ʮ�����µ����϶����ڣ�������ȥ�����ݡ�ֻ��ÿ�β�ѯ�����Ƿ���¼��ɡ�
    //�����Ҫ�鿴���µ���������һ���µģ���ȫ��ǰ�·ݵ��Ǹ��µ����ϡ�
    $cur_year  = date('Y');//��ǰ��
    $cur_month = date('n');//��ǰ��   
    $cur_date = mktime(0,0,0,$cur_month,1,$cur_year);//��ǰ��
    $monthes = 12;//ǰn����
	$price_result = $db->fetch_one("SELECT * FROM {$tblprefix}commu_pricetrend WHERE chid='$chid' AND area=0 AND month='$cur_date'");
    if(!$price_result){//��ǰ��û���� 
        $month_str = 0; //�鿴��������
        if($last_price = $db->fetch_one("SELECT * FROM {$tblprefix}commu_pricetrend WHERE chid='$chid' AND area=0 ORDER BY month DESC")){
            $last_time = $last_price['month'];//��������������µ�����
            $last_year = date('Y',$last_time);//�������ݵ����
            $last_month = date('n',$last_time);//�������ݵ��·ݣ�û��ǰ��0��
            $monthes_diff =  round((mktime(0,0,0,$cur_month,1,$cur_year) - $last_time)/2592000);       
            for($i = 1; $i < $monthes_diff + 1; $i ++){
                $month_str = mktime(0,0,0,$last_month + $i,1,$last_year);
				price_check1($chid,$month_str,$avg_field,0);          
            }
        }else{//��ȫû����
            for($i = $monthes-1; $i > -1; $i --){
                $month_str = mktime(0,0,0,$cur_month - $i,1,$cur_year);
				price_check1($chid,$month_str,$avg_field,0);
            }
        }   
    }else{ //��ǰ�±�������,ÿ�θ���
		price_check1($chid,$cur_date,$avg_field,1);
	}
}
// ����Ǹ��»��ǲ���,���������,
function price_check1($chid,$month,$avg_field,$update=0){
	$db = _08_factory::getDBO();
	$tblprefix = cls_env::getBaseIncConfigs('tblprefix');
	$coc1 = array('0'=>array('title'=>'��վ')) + cls_cache::Read('coclasses',1);
	foreach($coc1 as $ccid=>$v){ 
		//�۸�ͳ��(ȫ����¥�̣����ۣ�/���ַ����ܼۣ�/���⣨���ۣ����м۸�ͳ�ƣ���ƽ��ֵ)
		$price = $db->result_one("SELECT AVG($avg_field) AS price FROM {$tblprefix}".atbl($chid)." WHERE ".($ccid ? "ccid1='$ccid' AND" : '')." $avg_field > 0 ");
		$price = $chid == 3 ? round($price,2) : round($price);
		if(empty($price)) return;
		$res = $db->fetch_one("SELECT * FROM {$tblprefix}commu_pricetrend WHERE chid='$chid' AND month='$month' AND area='$ccid'"); //var_dump($res);
		if(!$res){ //û�оͼ���
			$db->query(" INSERT INTO {$tblprefix}commu_pricetrend set chid='$chid', month='$month', area='$ccid', price='$price', checked='1', createdate='".TIMESTAMP."' ");
		}elseif($update){ //��Ҫ���²Ÿ���
			$db->query(" UPDATE {$tblprefix}commu_pricetrend set price='$price' WHERE chid='$chid' AND month='$month' AND area='$ccid'");
		}
	}
}
// ��ȫ��վ
function price_sites($chid,$avg_field){
	$db = _08_factory::getDBO();
	$tblprefix = cls_env::getBaseIncConfigs('tblprefix');
	$query = $db->query("SELECT * FROM {$tblprefix}commu_pricetrend WHERE chid='$chid' AND area='0' AND createdate>'".(TIMESTAMP-366*86400)."'");
	if(!empty($query)){
		while($row=$db->fetch_array($query)){
			$chid = $row['chid'];
			$month = $row['month'];
			$price = $row['price'];
			price_check1($chid,$month,$avg_field,0);
		}
	}
}

// �����ĵ� ---- ??? ����һ��ҳ����������Ŀ? ������aboutarchive(�����Ϣ)������һ��?
// chid: �ĵ�ģ��id,����Ϊ�����2,3
// aids: ���Ի��ĵ�id
// max: ������
// fname: ��������
// smsg:��ʾ����(�ĵ�,¥��)
function getArchives($chid=4,$aids='',$max=10,$fname='loupan[]',$smsg='�ĵ�'){
	//die($chid);
	$cms_abs = cls_env::mconfig('cms_abs');
	$db = _08_factory::getDBO();
	$tblprefix = cls_env::getBaseIncConfigs('tblprefix');
	$channels = cls_cache::Read('channels');
	$alist = $sql = ''; $fugkey = str_replace(']','',str_replace('[','',$fname)); //�����Ŀ,����js����,html��ID
	$ChidFlag = array('3'=>'(����)','4'=>'(��¥)'); //������Ҫ��
	if($aids){
		$aids = substr($aids,1);
		if(!$aids) $aids = 0;
		$achid = explode(',',$chid); $nunion = '';
		foreach($achid as $ichid){
			$ichid = intval($ichid);
			$aids = preg_replace('/[^\d|\,]/', '', $aids); 
			$sql .= " $nunion SELECT '$ichid' AS achid,aid,subject FROM {$tblprefix}".atbl($ichid)." WHERE aid in ($aids) ";
			$nunion = " UNION ALL ";
		}
		$query=$db->query($sql);
		if(!empty($query)){
			while($row=$db->fetch_array($query)){
				$subject = $row['subject']; if(strstr($chid,',')) $subject .= $ChidFlag[$row['achid']];
				$alist .= "<label class='relLabel' onclick='relDelItem(this)'>
				  <input type='checkbox' checked='checked' name='$fname' id='arcRel_$row[aid]' value='$row[aid]'>$subject</label>";
			}
		}
	} //echo "$alist";
	if(strstr($chid,',')){
		$achid = explode(',',$chid);
		$mchidstr = "";
		foreach($achid as $ichid){
			$mchidstr .= "<option value='$ichid'>".$channels[$ichid]['cname']."</option>";
		}
		$mchidstr = "<select name='relSChid_$fugkey' id='relSChid_$fugkey'>$mchidstr</select>";
		$mchomsg = "����";
		$mchowidth = "12";
		$mchflag = 'yes';
		$mchfstr = "var relMchFlag = new Array('','','','(��)','(��)');";
	}else{
		$mchidstr = "<input name='relSChid_$fugkey' id='relSChid_$fugkey' type='hidden' value='$chid' />";
		$mchomsg = "����$smsg";
		$mchowidth = "18";
		$mchflag = 'no';
		$mchfstr = '';
	}
	$s = <<<HTML
<style type="text/css">
.relTemp_$fugkey {
  width:360px;
  height:80px;
  line-height:150%;
  overflow-y:scroll;
  text-align:left;
  position:absolute;
  padding:5px;
  background-color:#FFF;
  border:1px solid #CCC;
  margin:0px 0px 0px 0px;
}
.relColse {
  width:60px; float:right; cursor:pointer; color:red; text-align:right;
  display:inline-block;
  padding:1px 5px 5px 5px;
}
.relLabel {
  height:21px;
  display:inline-block; vertical-align:middle;
  overflow:hidden;
  border:0px solid #FFF;
  padding:0px 5px 0px 5px;
}
#relList_$fugkey div {
  cursor:pointer;
}
</style>
<div style="text-align:left" id="relItems_$fugkey">$alist</div>
<table border="0">
  <tr>
	<td>�ؼ���<span class="txt txtleft">:</span>
	  <input type="text" size="$mchowidth" name="relKeys_$fugkey" id="relKeys_$fugkey" />
	  $mchidstr
	  <input type="button" name="relSearch" id="relSearch" value="$mchomsg" onclick="relGetList_$fugkey()" /></td>
	<td valign="top"><div id="relTemp_$fugkey" class="relTemp_$fugkey" style="display:none">
	  <span class="relColse" onclick="javascript:this.parentNode.style.display='none';">�ر�</span> <b> &nbsp; ������$smsg (�������)</b>
		<div class="relList_$fugkey" id="relList_$fugkey" >
		  <div onclick="relAddItem_$fugkey(this,0)">[����] ��������-$smsg</div>
		</div>
	  </div></td>
  </tr>
</table>
<script type="text/javascript">
var relBaseUrl = "{$cms_abs}";
$mchfstr
function \$id(id){ return document.getElementById(id); }
function relGetList_$fugkey(){
	var chid = \$id('relSChid_$fugkey').value;
	var aj = Ajax("HTML","loading");
	var ekey = \$id('relKeys_$fugkey')
	var searchstr = 'ajax=relArchives&chid='+chid+'&fugkey=$fugkey';
	if(ekey.value.length<=0){  } // alert('����д�ؼ��֣�'); return false;
	else { searchstr += '&keywords=' + encodeURIComponent(ekey.value); }
	var ajaxurl = relBaseUrl +  uri2MVC(searchstr); 
	aj.get(ajaxurl,function(data){
		if(!data.length){ alert('û�������������Ϣ��'); return false; }
		\$id('relList_$fugkey').innerHTML = data; // reMove Exist?
		\$id('relTemp_$fugkey').style.display = "";
		\$id('relKeys_$fugkey').value = ''; // ������,����ؼ����Ա��´������������.
	});
}
function relAddItem_$fugkey(e,id){
	eitms = \$id('relItems_$fugkey').getElementsByTagName('INPUT');
	if(eitms.length>=$max){ alert('���{$max}��,�����ټ��룡'); return }
	if(\$id('arcRel_'+id)){ alert('�����ظ����룡'); return }
	var text = \$id('arcid_'+id).innerHTML;
	if('mchflag'=='yes') text += relMchFlag[\$id('relMChid').value]; // mchflag = 'yes'; //�Ƿ������¾ɾ�����relMChid
	var elab = document.createElement('LABLE');
	elab.onclick = function(){ relDelItem_$fugkey(this); }
	var ecbx = null;
	try{
		ecbx = document.createElement('<input name="$fname">'); //ie8֮ǰ�����
	}catch(ex){
		ecbx = document.createElement('INPUT');
	}
	ecbx.setAttribute('name','$fname'); //ie8֮ǰ�����Ч
	ecbx.setAttribute('type','checkbox');
	ecbx.setAttribute('id','arcRel_'+id);
	\$id('relItems_$fugkey').appendChild(elab); elab.appendChild(ecbx);
	ecbx.setAttribute('value',id);
	ecbx.setAttribute('checked','checked'); //ie6��,Ҫ�����Ž�ҳ���������true
	elab.setAttribute('class','relLabel');
	elab.innerHTML += text;
	e.parentNode.removeChild(e);
}
function relDelItem_$fugkey(e){
	e.parentNode.removeChild(e);
}
</script>
HTML;
	return $s;
} // end getArchives

?>