<?php

// ����:��ʼ����, ��δ���Զ������ദ��
include_once dirname(__FILE__).DS.'utagbase.cls.php'; //
include_once dirname(__FILE__).DS.'utag.cls.php'; //

/**
*  ǰ̨ģ���ǩ: ��ʼ������, ��ǩ����, ����
*  ģ���ǩ�������û��Զ��庯����  
*/

#----------------------
/**
 * �����ĵ�ģ����ĵ�ѡ�ֶ�
 * @param  [type] $chid  ģ��id
 * @param  [type] $field �ֶα�ʶ
 * @return [type]        ���ֶε�����
 */

function u_field_by($chid, $field){
	return cls_field::options(cls_cache::Read('field', $chid, $field));
}
/**
 * ���ý�����ĵ�ѡ�ֶ�
 * @param  [type] $chid      ����id
 * @param  [type] $field 	 �ֶα�ʶ
 * @return [type]            ���ֶε�����
 */

function u_cufield_by($chid,$field){
	$array = array();
	if($fields = cls_cache::Read('cufields',$chid)){
		$items   = explode("\n",$fields[$field]['innertext']);
		foreach($items as $k=>$v){
			$v = explode("=",$v);
			$array[$v[0]]= $v[1];	
		}
	}
	return $array;
 }

 
 // ������������(isuser:1-��Ա,0-�ĵ�)
 function baidu_push($isuser){
 
 	$db = _08_factory::getDBO();
 	$tblprefix = cls_env::getBaseIncConfigs('tblprefix');
 	//��ȡ�ϴ�����xml��ʱ��
 	$push_time = $db->result_one('select value from '.$tblprefix.'mconfigs where varname="push_time"');
 
 	$field = $isuser ? 'regdate' : 'initdate'; //��Ա/�ĵ��ֶ�
 	$stamp = strtotime(date('Y/m/d')); //����0��ʱ��;
 	$now_time = TIMESTAMP; //��ǰʱ��
 
 	if(empty($push_time)){ //��һ������
 
 		$sql = "$field >= ".($stamp-86400)." AND $field < $stamp";
 		$db->query('insert into '.$tblprefix.'mconfigs values("push_time",'.$now_time.',"site")');
 	}else{
 
 		if($now_time > $push_time + 7*86400){ //����һ��û�гɹ������ύ����һ��ֻ�ύǰһ�쵽��ǰ��
 			$sql = "$field >= ".($stamp-86400)." AND $field < $stamp";
 				
 		}else{
 			$sql = "$field > ".$push_time;
 				
 		}
 	}
 
 	return $sql;
 }
 
 
function u_member_houses($mid, $mchid, $stat = '', $lock = 1){#$mid=1;#w113124
    $tpl_mconfigs = cls_cache::Read('tpl_mconfigs');
    $mcachetime = $tpl_mconfigs["user_cachetime"];
	$timestamp = TIMESTAMP;
	$db = _08_factory::getDBO();
	$tblprefix = cls_env::getBaseIncConfigs('tblprefix');
	$mid = empty($mid)?0:max(1,intval($mid));
	$mchid = empty($mchid)?0:max(1,intval($mchid));
	$update = $timestamp - $mcachetime;# ������/���͹�˾�ռ䷿Դͳ�Ƹ���ʱ��/��
	$cachefile = M_ROOT . './dynamic/mspace/' . ceil($mid / 500) . "/$mid.php";

    $fp = _08_FilesystemFile::getInstance();
    $fp->_fopen($cachefile, 'r');
    $writeStat = $fp->_fread(12);

    if ( is_file($cachefile) && $writeStat != '<?php exit?>' )
    {
        $stat = include($cachefile);
        $stat['update'] = filemtime($cachefile);
    }
    else
    {
    	$stat = array();
        $stat['update'] = $timestamp;
    }

	if( _08_DEBUGTAG || empty($stat) || ($stat['update'] < $update) )
    {
        $mids = u_getHouseMids($mid, $mchid);
        $rows = u_getHouseList($mids, $mchid);
        foreach ( array(2, 3) as $chid )
        {
            # ��С��------������С��ͳ������Ԥ����Ա��񷢲�¥����Ϣ������������
         #   $stat[$chid]['area'] = array_count_values($rows[$chid]['pid3']);
            # ������
            if ( isset($rows[$chid]['shi']) )
            {
                $stat[$chid]['room'] = array_count_values((array)$rows[$chid]['shi']);
            }
            else
            {
            	$stat[$chid]['room'] = array();
            }

            # ���۸�
            if ( isset($rows[$chid]['zj']) )
            {
                $stat[$chid]['price'] = u_array_count_values($mids, $chid, (array)$rows[$chid]['zj']);
            }
            else
            {
            	$stat[$chid]['price'] = array();
            }
        }

		# ���»�������
		if(mmkdir($cachefile, 1, 1)){
            $cache = "<?php\r\nreturn " . var_export($stat, true) . ';';
			file_put_contents($cachefile, $cache);
		}
	}
	return $stat;
}

/**
 * ��ȡ���۸����ֵ�ͳ����
 * @author Wilson
 *
 * @param  mixed $mids Ҫ��ȡ���û�ID��
 * @param  int   $chid ��ǰ�ĵ�ģ��ID
 * @param  array $rows ��ǰ�û����ѯ�ķ�Դ��Ϣ
 *
 * @return array $stat ���ذ��۸����ֵ�ͳ����
 * @since  1.0
 */
function u_array_count_values($mids, $chid, array $rows)
{
    $db = _08_factory::getDBO();
	$timestamp = TIMESTAMP;
    $stat = array();
    $ccids = array();
    $coid = $chid == 3 ? 4 : 5;
	$coclasses = cls_cache::Read('coclasses', $coid);
    foreach ( $coclasses as $coclasse )
    {
        $coclasse['conditions']['sqlstr'] = str_replace(array('{$pre}', '\\\'', '\"'), array('a.', '', ''), $coclasse['conditions']['sqlstr']);
        $db->select('COUNT(*) AS num')
           ->from('#__' . atbl($chid) . ' a')
           ->where('mid')->_in($mids)->_and($coclasse['conditions']['sqlstr'])
		   ->_and("(a.enddate=0 OR a.enddate>$timestamp)")
           ->exec();
        $row = $db->fetch();
        if ( isset($row['num']) )
        {
            $stat[$coclasse['ccid']] = $row['num'];
        }
    }

    return $stat;
}

/**
 * ��ȡ��ǰ������ID�������ǰ��Ա�Ǿ��͹�˾����ȡ�������о�����ID
 * @author Wilson
 *
 * @param int $mid   ��ǰ��ԱID
 * @param int $mchid ��ǰ��Աģ��ID
 *
 * @return string $mids ��ǰ��ԱID�飬������Զ��ŷָ�
 * @since  1.0
 */
function u_getHouseMids($mid, $mchid)
{
    $db = _08_factory::getDBO();
    # ����Ǿ��͹�˾���ȡ�������о�����ID
    if($mchid == '3') {
        $db->select('m.mid')
           ->from('#__members m')
           ->where(array('pid4'=>$mid))->_and(array('m.incheck4'=>1))
           ->exec();

        $mids = array();
		while($row = $db->fetch()){
			$mids[] = $row['mid'];
		}
        if ( !empty($mids) )
        {
            $mids = ($mid . ',' . implode(',', $mids));
        }
        else
        {
        	$mids = $mid;
        }
	} else {
	    $mids = $mid;
	}

    return $mids;
}

/**
 * ��ȡ��Դ��Ϣ�б�
 * @author Wilson
 *
 * @param int    $mids  ��ǰ��ԱID
 * @param int    $mchic ��ǰ��Աģ��ID
 * @since 1.0
 */
function u_getHouseList($mids, $mchid)
{
    $timestamp = TIMESTAMP;
    $db = _08_factory::getDBO();

    $rows = array();
    foreach(array(2, 3) as $chid)
    {
        $db->select('shi, zj')
           ->from('#__' . atbl($chid) . ' a')
           ->innerJoin("#__archives_$chid b")->_on('a.aid = b.aid')
           ->where(array('a.checked'=>1))->_and('a.mid')->_in($mids)->_and("(a.enddate = 0 OR a.enddate > " . (int)$timestamp . ')')
           ->exec();
        while($row = $db->fetch())
        {
          #  $rows[$chid]['pid3'][] = $row['pid3'];
            $rows[$chid]['shi'][] = $row['shi'];
            $rows[$chid]['zj'][] = $row['zj'];
        }
    }

    return $rows;
}

function u_func_jjrls($mcaid, $ccid19 = 0, $size = 20){
	$_da 	= cls_Parse::Get('_da');
	$timestamp = TIMESTAMP;
	$mid 	= empty($_da['mid'])?0:max(1,intval($_da['mid']));
	$mchid 	= empty($_da['mchid'])?0:max(1,intval($_da['mchid']));
    $chid 	= ((int)$mcaid == 1 ? 3 : 2);
	$mids 	= u_getHouseMids($mid, $mchid);
	$page 	= empty($_da['page']) ? 0 : max(1,intval($_da['page']));
	$extra 	= empty($_da['extra'])?'':(string)$_da['extra'];
    $extraArray = array_filter(explode(':', $extra));


    if ( in_array($page, array(0, 1)) )
    {
        $start = 0;
    }
    else
    {
        $start = ($page - 1) * $size;
    }

    $db = _08_factory::getDBO();
    if ( $ccid19 == -1 )
    {
        $field = 'COUNT(*) AS num';
    }
    else
    {
    	$field = '*';
    }
    $db->select($field)
       ->from('#__' . atbl($chid) . ' a')
       ->innerJoin("#__archives_$chid b")->_on('a.aid = b.aid')
       ->where(array('a.checked'=>1))->_and("(a.enddate=0 OR a.enddate>$timestamp)")->_and('a.mid')->_in($mids);

       if ( empty($extraArray) && $ccid19 && ($ccid19 != -1) )
       {
           $db->_and(array('a.ccid19' => $ccid19));
       }
       else
       {
           $extraArray[0] = (isset($extraArray[0]) ? (string) $extraArray[0] : '');
           $extraArray[1] = (isset($extraArray[1]) ? (int)$extraArray[1] : '');

           if ( $extraArray[0] == 'area' )
           {
               $extraArray[0] = 'a.pid3';
               $db->_and(array($extraArray[0] => $extraArray[1]));
           }
           else if ( $extraArray[0] == 'price' )
           {
               $coid = ($chid == 3 ? 4 : 5);
               $coclasses = cls_cache::Read('coclasses', $coid);
               $coclassesSQL = str_replace(array('{$pre}', '\\\'', '\"'), array('a.', '', ''), $coclasses[$extraArray[1]]['conditions']['sqlstr']);
               $db->_and($coclassesSQL);
           }
           else if ( $extraArray[0] == 'room' )
           {
               $extraArray[0] = 'a.shi';
               $db->_and(array($extraArray[0] => $extraArray[1]));
           }
       }
    if ( $ccid19 == -1 )
    {
        $row = $db->exec()->fetch();
        $rows = $row['num'];
    }
    else
    {
    	$db->order(' a.ccid19 DESC, a.aid DESC')
           ->limit($start, $size)
           ->exec();
        $rows = array();
        $coclass = cls_cache::Read('coclasses', 1);
		$db2 = clone $db;
        while ( $row = $db->fetch() )
        {
            $row['arcurl'] = cls_url::view_arcurl($row);

			$row2 = $db2->select()->from('#__' . atbl(4) . ' a')->where(array('aid'=> $row['pid3']))->exec()->fetch();
			$row['_arcurl'] = cls_url::view_arcurl($row2)."&addno=7";

            $row['ccid1title'] = @$coclass[$row['ccid1']]['title'];
            $rows[$row['aid']] = $row;
        }
    }
    return $rows;
}

function u_time_format($time, $fix = ''){
	$timestamp = TIMESTAMP;
	$time = $timestamp - $time;
	if($time < 60){
		return '�Ÿո�'.$fix;
	}elseif($time < 1800){
		return floor($time / 60) . '����ǰ'.$fix;
	}elseif($time < 3600){
		return '��Сʱǰ'.$fix;
	}elseif($time < 86400){
		return floor($time / 3660) . 'Сʱǰ'.$fix;
	}elseif($time < 86400 * 30){
		return floor($time / 86400) . '��ǰ'.$fix;
	}else{
		return floor($time / 86400 / 30) . '����ǰ'.$fix;
	}
}

function u_array_merge($arr1, $arr2){
	foreach($arr2 as $k => $v){
		if(is_array($v)){
			$arr1[$k] = isset($arr1[$k]) ? u_array_merge($arr1[$k], $v) : $v;
		}else{
			return array_unique(array_merge($arr1, $arr2));
		}
	}
	return $arr1;
}



function getsearchfields($chid = 0){
	$channels = cls_cache::Read('channels');
	if(is_array($channels[$chid]['searchfields'])){
		return $channels[$chid]['searchfields'];
	}else{ //���ַ���ת�������,����֮ǰ
		if(strstr($channels[$chid]['searchfields'],'array')){
			eval("\$searchfields = ".$channels[$chid]['searchfields'].'; ');
		}else{
			$searchfields = '';
		}
		return !empty($searchfields) ? $searchfields : array();
	}
}
function print_keywords($keywords = '',$klink = ''){
	$str = '';
	$keywords = trim($keywords);
	$keywords = str_replace(array('��', ' '), ",", $keywords);
	$arr = array();
    $arr = explode(",", $keywords);
	!is_array($arr) && $arr = explode("��", $keywords);
	!is_array($arr) && $arr = explode(" ", $keywords);
	for($i = 0; $i<count($arr); $i++)
	   !empty($arr[$i]) && $str .= '<a href="'.$klink.$arr[$i].'" target="_blank">' . $arr[$i] . '</a>&nbsp;';
	return $str;
}

function u_dpcount($pid3=0){
	$_da = cls_Parse::Get('_da');
	$db = _08_factory::getDBO();
	$tblprefix = cls_env::getBaseIncConfigs('tblprefix');
	$aid = ($pid3)?intval($pid3):$_da['pid3']; // ����,�����϶�Ӧ��¥��id
	$aid = intval($aid);
	$commu = cls_cache::Read('commu',2); //$cuid = 2;
	$sql = "SELECT COUNT(*) AS cnt FROM {$tblprefix}$commu[tbl] WHERE aid='$aid'";
	$row = $db->fetch_one($sql); //$row[] =$sql; print_r($row);
	if($pid3) { return $row['cnt']; }
	else { $result = array(0=>$row); return $result; }
}

// {c$hxsslb} �������� �б�ʹ��
function u_sql_hxsslb($para=0){
	$tblprefix = cls_env::getBaseIncConfigs('tblprefix');
	$_da = cls_Parse::Get('_da');
	$_da['wherestr'] .= " AND a.pid3 IN (SELECT aid FROM {$tblprefix}archives_4 WHERE (leixing=0 OR leixing=1) ) ";
	$sql = "$_da[selectstr] $_da[fromstr] $_da[wherestr] "; //print_r($sql);
	return $sql;
	// select��from��where
}

//������;��������۸����ͨ�ú���
/*function u_show(array(
		"id"=>1,
		"title"=>'����',
		"name" => '',
		"id4" => array(),
		"isccid" => 1,
		"level" => 0,
		"classname1" => 'select',
		"classname2" => 'droplist',
		"num" => 10))
˵����
	id��        ����ϵ������Ŀid������Ѷ����idΪ1
	isccid��    �ж�����ϵ������Ŀ�������й涨����ϵ��1   ��Ŀ��2
	level��     ��level
	name:		�Զ���input��nameֵ��Ϊ�ջ������Ŀ������ϵ�Զ���ֵ
    id4��       Ҫ�ų�����ϵ����Ŀ��id�������Ҫĳ������Ŀ����Ϣ����Ѹ�����Ŀid��Ϊid4��ֵ���ñ���Ϊ���顣���ǣ��ô������id��
		        Ҫô���Ǻ��������ĸ�id��Ҫô����û��������id��
	title��     Ϊ���⣬����������;��������۸�
	classname1��Ϊspan�������ʽ����
	classname2��Ϊspan�������ʽ����
	num��       ��ѡ����������numʱ�������ʽl-more
*/
function u_show($cfgs = array()){
	$name = '';$id4=array();$isccid=1;$level=0;$classname1='select';$classname2='droplist';$num=10;
	extract($cfgs);
	$arr = array();
	if($isccid == 1){
		$coclasses = cls_cache::Read('coclasses',$id);
		if(!empty($level)){
			foreach($coclasses as $x=>$y){
				if($y['level'] == $level) $arr[$x]['title'] = $y['title'];
			}
		}else $arr = $coclasses;
	}elseif($isccid == 2){
		if(!is_array($id4))return;
		$catalogs = cls_cache::Read('catalogs');
		foreach($catalogs as $j=>$k){
			if(in_array($k['pid'],$id4)){
				$fulei = 1;//����������id���滹��������ʱ��$fulei=1,����$fulei=0
				break;
			}else $fulei = 0;
		}

        foreach($catalogs as $m=>$n){
            $chid_arr = array_filter(explode(',',$n['chids']));
		    if(in_array($id,$chid_arr) && $n['level'] == $level){
				if(!in_array($fulei == 0?$n['caid']:$n['pid'],$id4))$arr[$m]['title'] = $n['title'];
			}
		}
	}
	$total_num = count($arr);
	$classname2 = $classname2.($total_num>$num?' l-more':'');
	echo "<span class=\"".$classname1."\">";

	$input_name = empty($name)?($isccid==1?'ccid'.$id:'caid'):$name;
	echo "<input type=\"hidden\" name=\"".$input_name."\" value=\"".($isccid==1?'':$id)."\"/>";

	echo "<span  class=\"txt1\">".$title."</span>";
	echo "<span class=\"".$classname2." \">";
	echo "<em class=\"act\" rel=\"\">".$title."</em>";
    foreach($arr as $k=>$v) echo "<em rel=\"".$k."\">".$v['title']."</em>";
	echo "</span><b class='ico08'>&#xe68d;</b></span>";
}

//���б�������۵����
//tplurl�����ӣ���ģ��ҳ��ֱ�ӵ��õ�$tplurl
//content:����
function show_face($tplurl,$content){
	$content=str_replace(array('{:',':}'),array("<img src=\"".$tplurl."images/face/",".gif\"/>"),$content);
	echo $content;
}

//�����ܱ߸����Ķ��ַ������ⷿ
function u_zhoubian($aid){
	$db = _08_factory::getDBO();
	$tblprefix = cls_env::getBaseIncConfigs('tblprefix');
	$_wherestr = '';
	$aid = intval($aid);
	$_wherestr .= " a.pid3 IN ( SELECT e.aid FROM {$tblprefix}".atbl(4)." e INNER JOIN {$tblprefix}aalbums f ON e.aid = f.pid INNER JOIN {$tblprefix}archives_4 g ON g.aid = e.aid  WHERE f.inid = '$aid' AND g.leixing IN ('0','2'))";
	return $_wherestr;
}

// ¥��,����,����,������ʾ��ѡ��Χ����
function u_sch_now_area($keys=array(),$unit='Ԫ'){
	$_da = cls_Parse::Get('_da');
	$i = 0; $v = array();
	foreach($keys as $k){
		$v[$i] = empty($_da[$k]) ? 0 : $_da[$k];
		//unset($pick_urls[$k]);
		$i++;
	}
	$r = "$v[0]~$v[1]";
	if(!$v[0] && !$v[1]) return '';
	if(!$v[0]) $r = "0~$v[1]";
	if(!$v[1]) $r = "&gt;$v[0]";
	$r = '<a class="search_selected" href="'.cls_uso::extra_url(implode('|',$keys)).'" title="ȡ������">'.$r.$unit.'</a>';
	return $r;
}

//�ʴ���Ŀ������ר��ɸѡ
function question_expert(){
	$caid = cls_Parse::Get('_da.caid');
	$caid = intval($caid);
	return $caid=='516'?'':"  s.quaere like '%$caid%'";
}
//ר�����б�ɸѡ
function u_sql_spe(){
	$caid = cls_Parse::Get('_da.caid');
	$sql = "";
	$caid = intval($caid);
	if($caid != '516'){
		$sql .= " AND s.quaere LIKE '%,$caid,%'";
	}
	return substr($sql,5);
}
//��������б�
function u_sql_hmeasure(){
	$tblprefix = cls_env::getBaseIncConfigs('tblprefix');
	$sql = "SELECT * FROM {$tblprefix}commu_housemeasure c
		GROUP BY createdate,mid";
	return $sql;
}
//��������б�-��˾����
function u_sql_hmcorps($time,$mid,$tomid=0){
	$db = _08_factory::getDBO();
	$tblprefix = cls_env::getBaseIncConfigs('tblprefix');
	$tomids = "";
	$time = preg_replace('/[^\d]/', '', $time);
	$mid = intval($mid);
	$sql = "SELECT tomid FROM {$tblprefix}commu_housemeasure WHERE createdate='$time' AND mid='$mid'";
	$query = $db->query($sql);
	while($r = $db->fetch_array($query)){
		$tomids .= (empty($tomids) ? "" : ",")."$r[tomid]";
	}
	$re = ''; $cnt = 0;
	if($tomids=='') $tomids = '0';
	$sql = "SELECT companynm FROM {$tblprefix}members_11 WHERE mid IN($tomids)";
	$query = $db->query($sql);
	while($r = $db->fetch_array($query)){
		$re .= "\n<li>$r[companynm]</li>";
		$cnt++;
	}
	if($cnt>1){
		$re = "\n<ul class='hm_hidc'>$re</ul>"; //hm_hidc����css
		$re = "\n<div class='hm_cntc'>��{$cnt}�ҹ�˾...</div>".$re;
	}else{
		$re = "\n<ul class='hm_onec'>$re</ul>"; //һ����˾
	}
	echo $re;
}

//����ַ���
//string: ������ֵ��ַ���
//symbol:  �ָ����������Զ���Ϊ�ָ�������ַ���
function str_explode($string,$symbol){
	$arr = array();
	$arr = explode($symbol,$string);
	foreach($arr as $k => $v){
		echo "<strong class=\"item_$k\">".$v."</strong>";
	}
	unset($arr);
}
// ��Դί��-�ƹ����ĺ���
function wtmsg($str){
	echo "<div class='jinggao'>$str</div>";
}

/**��Դί�е����������weituos
  *@param	string	$_hmtl_code   ģ��ҳ��html����
*/
function wt_step3($_hmtl_code,$checked_mids,$cid){
	$curuser = cls_UserMain::CurUser();
	$memberid = $curuser->info['mid'];
	$timestamp = TIMESTAMP;
	$db = _08_factory::getDBO();
	$tblprefix = cls_env::getBaseIncConfigs('tblprefix');
	$cid = empty($cid) ? 0 : max(1,intval($cid));
	$memberid = empty($memberid) ? 0 : max(1,intval($memberid));
	if($memberid == 1){//������Ա���й���ʽ�����Ա���ģ�����ί�з�Դɾ��ʱ����ȡ���йܵĻ�Ա��ID
		define('M_MCENTER', TRUE); // ���ڴ������²��������Ի�Ա����
		$member_info = $curuser->isTrusteeship();
		$memberid = $member_info['mid'];
	}

	if(!($_cid_exist = $db->fetch_one("SELECT * FROM {$tblprefix }commu_weituo where cid = '$cid'"))){
		wtmsg("�ܱ�Ǹ��������cidΪ".$cid."�ķ�Դί����Ϣ��");
	}else{
		$wtnum = $db->result_one("SELECT count(*) FROM {$tblprefix }weituos WHERE cid='$cid' AND fmid='$memberid'");
		$wtnum = empty($wtnum) ? 0 : $wtnum;
		$weituo_mid_arr = array_filter(explode(',',$checked_mids));
		if(!empty($weituo_mid_arr)){
			$mid_arr = array();
			$_sql = $db->query("SELECT tmid FROM {$tblprefix }weituos WHERE cid='$cid' AND fmid='$memberid'");
			while($row = $db->fetch_array($_sql)){
				$mid_arr[] = $row['tmid'];
			}
			$_mids = array_diff($weituo_mid_arr,$mid_arr);//��ֹͬһ�������˱�����������ݣ������Ա����>>��ί�еķ�Դ>>����ί�У�
			if(!empty($_mids)){
				foreach($_mids as $mid){
					if($wtnum>5) break;
					$mid = intval($mid);
					$db->query("INSERT INTO {$tblprefix }weituos SET cid='$cid',fmid='$memberid',tmid='$mid',weituodate='$timestamp'");
					$wtnum++;
				}
			}
		}
		echo $_hmtl_code;
	}
}


//����cid�Լ�tel����⣬�Ƿ���ڸ����ݣ���ֹ�����޸Ĳ���
function wt_step2_cid_exist($cid,$tel,$cuid,$fanye=0){
	if(!($commu = cls_cache::Read('commu',$cuid)) || !$commu['available']) cls_Parse::Message('ί�й����ѹرա�');
	$db = _08_factory::getDBO();
	$tblprefix = cls_env::getBaseIncConfigs('tblprefix');

	$curuser = cls_UserMain::CurUser();
	$memberid = empty($curuser->info['mid']) ? 0 : $curuser->info['mid'];
	if($memberid == 1){//������Ա���й���ʽ�����Ա���ģ�����ί�з�Դɾ��ʱ����ȡ���йܵĻ�Ա��ID
		M_MCENTER || define('M_MCENTER', TRUE); // ���ڴ������²��������Ի�Ա����
		$member_info = $curuser->isTrusteeship();
		$memberid = $member_info['mid'];
	}
	$_cid_exist = 1;
	//�����ҳ��ʱ�򣬸���cid�Լ�tel����⣬�Ƿ���ڸ����ݣ���ֹ�����޸Ĳ���
	if($fanye){
		if($memberid){
			$_result_exist = $db->fetch_one("SELECT * FROM {$tblprefix}$commu[tbl] WHERE mid='$memberid' AND cid='$cid'");
		}else{
			$_result_exist = $db->fetch_one("SELECT * FROM {$tblprefix}$commu[tbl] WHERE tel='$tel' AND cid='$cid'");
		}
		if(empty($_result_exist)){
			$_cid_exist = 0;
		}
	}
	return $_cid_exist;
}


/**��ȡsendwtnum��ĳ����Ա������ĳ���绰�����Ѿ�������ί�з�Դ����
  *@rerurn
*/
function wt_step2_sendwtnum($cid,$tel,$chid,$cuid){
	if(!($commu = cls_cache::Read('commu',$cuid)) || !$commu['available']) cls_Parse::Message('ί�й����ѹرա�');
	$db = _08_factory::getDBO();
	$tblprefix = cls_env::getBaseIncConfigs('tblprefix');

	$curuser = cls_UserMain::CurUser();
	$memberid = empty($curuser->info['mid']) ? 0 : $curuser->info['mid'];

	$_sendwtnum = 0;
	if($memberid){
		if($memberid == 1){//������Ա���й���ʽ�����Ա���ģ�����ί�з�Դɾ��ʱ����ȡ���йܵĻ�Ա��ID
			M_MCENTER || define('M_MCENTER', TRUE); // ���ڴ������²��������Ի�Ա����
			$member_info = $curuser->isTrusteeship();
			$memberid = $member_info['mid'];
		}
		$_sendwtnum = $db->result_one("SELECT COUNT(*) FROM {$tblprefix}$commu[tbl] WHERE mid='$memberid' AND chid='$chid'");
	}else{
		$_sendwtnum = $db->result_one("SELECT COUNT(*) FROM {$tblprefix}$commu[tbl] WHERE tel='$tel' AND chid='$chid'");
	}
	return $_sendwtnum;
}
/**��Դ�ڶ�������cid�Լ�����Աί���˼�����Ա�Լ���Щ�������ѱ�ѡȡ
  *@param	string	$mid_str  ����Ѿ�ί�еľ�����
  *@param	int		$wtnum	  ����ί�з�Դ�Ѿ�ί�еľ����˸���
  *@param	array	$fmdata	  ί�еķ�Դ����
  *@return	array	$_sqlstr_and_wtnum	where��������Լ�����ί�з�Դ�Ѿ�ί�еľ����˸���
*/
function  wt_step2(&$cid,$cuid,$chid,$fmdata){
	$onlineip = cls_env::OnlineIP();
	if(!($commu = cls_cache::Read('commu',$cuid)) || !$commu['available']) cls_Parse::Message('ί�й����ѹرա�');
	$timestamp = TIMESTAMP;
	$db = _08_factory::getDBO();
	$tblprefix = cls_env::getBaseIncConfigs('tblprefix');
	$curuser = cls_UserMain::CurUser();
	$fields = cls_cache::Read('cufields',$cuid);

	$_sqlstr_and_wtnum = array();
	$memberid = empty($curuser->info['mid']) ? 0 : $curuser->info['mid'];
	$mname = $curuser->info['mname'] ;
	if(!empty($memberid)){
		if($memberid == 1){//������Ա���й���ʽ�����Ա���ģ�����ί�з�Դ����ʱ����ȡ���йܵĻ�Ա��ID
			define('M_MCENTER', TRUE); // ���ڴ������²��������Ի�Ա����
			$member_info = $curuser->isTrusteeship();
			$memberid = $member_info['mid'];
			$mname = $member_info['mname'];
		 }
	 }

	if(empty($cid)){

	  $sqlstr = "pid='$fmdata[pid]',ip='$onlineip',mid='$memberid',mname='$mname',createdate='$timestamp'";
	  $sqlstr .= ",address='$fmdata[address]',dt='$fmdata[dt]',ccid1='$fmdata[ccid1]',ccid2='$fmdata[ccid2]',ccid3='$fmdata[ccid3]',ccid14='$fmdata[ccid14]',chid='$chid'";
	  if($curuser->pmautocheck(@$commu['autocheck'],'cuadd')) $sqlstr .= ",checked=1";
	  $chid == 2 && $sqlstr .= ",zlfs='$fmdata[zlfs]'";

	  $a_field = new cls_field;
	  foreach($fields as $k => $v){
		  if(isset($fmdata[$k])){
			  $a_field->init($v);
			  $fmdata[$k] = $a_field->deal('fmdata','mcmessage',axaction(2,M_REFERER));
			  $sqlstr .= ",$k='$fmdata[$k]'";
		  }
	  }
	  $db->query("INSERT INTO {$tblprefix }$commu[tbl] SET $sqlstr");
	  $cid = $db->result($db->query("SELECT last_insert_id()"), 0);
	}elseif($memberid){
	  $cid = empty($cid) ? 0 : max(1,intval($cid));
	  $fmdata = $cid ? $db->fetch_one("SELECT * FROM {$tblprefix }$commu[tbl] WHERE cid='$cid' AND mid='$memberid' AND chid='$chid'") : array();
	  $mid_str = "";//mid��ɵ�sql�������
	  $mid_string = "";//����Ѿ�ί�е�mid����Ϊ�ڶ�������input��value
	  $qy = $db->query("SELECT tmid FROM {$tblprefix }weituos WHERE cid='$cid' AND fmid='$memberid'");
	  while($m = $db->fetch_array($qy)){
		  $mid_str .= "'".$m['tmid']."',";
		  $mid_string .= ",".$m['tmid'];
	  }
	  if(!empty($mid_str)) $mid_str = " m.mid NOT IN (".substr($mid_str,0,-1).")";
	  $wtnum = $db->result_one("SELECT count(*) FROM {$tblprefix }weituos WHERE cid='$cid' AND fmid='$memberid'");
	}
	$_sqlstr_and_wtnum['wtnum'] = empty($wtnum) ? 0 : $wtnum;
	$_sqlstr_and_wtnum['midstr'] = empty($mid_string)?'':$mid_string;

	$exconfigs = cls_cache::cacRead('exconfigs',_08_EXTEND_SYSCACHE_PATH);
	$relus = $exconfigs['weituo'];
	$_where_str = '';
	!empty($mid_str) && $_where_str .= $mid_str;
	empty($relus['allowptjjr']) && $_where_str .= " AND m.grouptype14='8'";
	empty($relus['allowccid1']) && !empty($ccid1) && $_where_str .= " AND s.ccid1='$ccid1'";
	$_sqlstr_and_wtnum['where_str'] = empty($_where_str) ? '' : substr($_where_str,5);

	return $_sqlstr_and_wtnum;
}


//����������ۡ������󹺡�ί�г������
//�����ɹ���ʾ
function _tmp_sendok($_message,$action,$cms_abs,$tplurl){
		$fid = 112;
		if(in_array($action,array('chushou','chuzu'))){
			$fid = 111;
		}else if(in_array($action,array('qiuzu','qiugou'))){
			$fid = 112;
		}
        echo "<div style=\"text-align: center;\"><img src=\"".$tplurl."images/fbcg.gif\"><br>";
        echo empty($_message)?'':"<span>ע��:".$_message."</span><br>";
        echo "���������ԣ�&nbsp;<a href=\"".$cms_abs."\" target=\"_parent\"  style=\"color:#093;\">&lt;&lt;������ҳ</a>&nbsp; �� &nbsp;<a href=\"?fid=$fid&action=".$action."\" target=\"_parent\" style=\"color:#093;\">��������&gt;&gt;</a>&nbsp;</div>";
}

//��������λ��title;
function u_push_info($paid){
	$db = _08_factory::getDBO();
	$tblprefix = cls_env::getBaseIncConfigs('tblprefix');
	$_data = array();
	$sql = $db->query("SELECT pushid,subject FROM {$tblprefix}push_$paid WHERE  checked = 1 ");
	while($row = $db->fetch_array($sql)){
		$_data[$row['pushid']] = $row['subject'];
	}
	return $_data;
}

 /**
  *�������ַ�/���������С���ĵ�ͼ����
  *param int pid3 �����ϼ���С��aid
  *return array   ��ͼ�ľ��ȡ�γ�ȡ��Ƿ�ϼ���С��
  */
function search_xq_dt($pid3){
   	$db = _08_factory::getDBO();
	$tblprefix = cls_env::getBaseIncConfigs('tblprefix');
    if(empty($pid3)){
        return array('dt_1'=>0,'dt_0'=>0,'pid3'=>0);
    }else{
        $aid = max(1,intval($pid3));
        $row = $db->fetch_one("SELECT dt_1,dt_0 FROM {$tblprefix}".atbl(4)." WHERE aid = '$aid'");
        if($row){
            return array('dt_1'=>$row['dt_1'],'dt_0'=>$row['dt_0'],'pid3'=>$pid3);
        }else{
            return array('dt_1'=>0,'dt_0'=>0,'pid3'=>0);
        }
    }
}


/**
 *�ж�ĳ�������Ƿ�����
 *param string block ����Ӣ�ı�ʶ
 *return bool  true | false  ������Ϊtrue���ر���false
 */
function block_open($block){
	$rtags = cls_cache::Read('rtags');
	$open = 0;
	if(empty($rtags[$block]['disabled'])){
		$open = 1;
	}
	return $open ? true : false;
}

/**
* ����ȫ���ĶԱ�����
* @param  array $compareArr ���еĶԱ�����
*$compareArr['title']����¥��/���ַ�/����ı���
*$compareArr['content']����¥��/���ַ�/�����������Ϣ
* @param  int   $column     �Ա�ҳ����ʾ���ݵ���������������ߵı�����Ŀ��һ�У�
*/
function compareInfo($columnNum,$chid){
    $db = _08_factory::getDBO();
    $tblprefix = cls_env::getBaseIncConfigs('tblprefix');
    //������еĶԱ�����
    $compareArr = array();

    //��ȡ�ĵ�ģ���ֶ�
    $archiveFields = cls_cache::Read('fields',$chid);
    //��Ҫ�ų����ֶ�
    $putAwayArr = array('subject','author','stpic','lphf','loupanlogo','dt','keywords','abstract','xqt','content','fdname','fdtel','fdnote','qqqun','xqjs','xqhs');

    //����������ֶ����ֿ���ţ�Ϊ��ʵ��ǰ̨�Ĺ�����꣬����һ��ʼ������������Ϸ���Ч��
    $compareArr['title'][0] = $archiveFields['subject']['cname'];

    //�ų���������ֶ�����
    $allowFieldArr = array();
	foreach($archiveFields as $k => $v){
		if(!in_array($k, $putAwayArr)){
            $compareArr['content'][0][]= $v['cname'];
		}
	}

    foreach($compareArr['content'] as $k => $v){
        $htmlStr = '';
        //html��ul��ǩ��id����
        for ($i=0; $i <= $columnNum; $i++) { 
        	$htmlStr .= "<ul class='col$i'>";
        	foreach($v as $key =>$val){
		        $htmlStr .= "<li>";
            	$htmlStr .= "<div>".($i>0?'&nbsp;':$val)."</div>";
	    	    $htmlStr .= "</li>";
        	}
        	$htmlStr .= '</ul>';
        }
        
        echo $htmlStr;
    }
 }
/*
* ��ͼ�ҷ�������������
* @param  array $allCondition ���п�������
* @param $chid ģ��id
* @return ���˺������
*/
function conditionFilter($allCondition,$chid){
	$searchfield = getsearchfields($chid);
	$allCondition_flip = array_flip($allCondition);
    $ConditionConfig = array();
    foreach($searchfield as $k=>$v){
        if(in_array($k,$allCondition)){            
            $ConditionConfig[$allCondition_flip[$k]] = $k;
        }
    }
    return $ConditionConfig;
}
/*
* ��ͼ�ҷ����ص���������
* @param  array $configs ���˺������
* @param $chidMode ����ģ��
* @return array
*/
function getConditions($configs=array(),$chidMode=''){
	   $conditions = array();
       $mconfigs = cls_cache::Read('mconfigs');
	   $fcdisabled2 = $mconfigs['fcdisabled2'];
       $fcdisabled3 = $mconfigs['fcdisabled3'];
       if(empty($fcdisabled2)){
       		$cnrel1 = cls_cache::Read('cnrel',1);
            $coclass2 = cls_cache::Read('coclasses', 2);            
       }
       if(empty($fcdisabled2)){
       		$cnrel2 = cls_cache::Read('cnrel',2);
            $coclass14 = cls_cache::Read('coclasses', 14);            
       }
	   foreach($configs as $k => $v){
			   if(is_numeric($v)){
					if($coclass = cls_cache::Read('coclasses', $v)){
						foreach($coclass as $coclass_k => $coclass_v){
								$conditions[$k]['text'][] = $coclass_v['title'];
								$conditions[$k]['value'][] = $coclass_v['ccid'];
                                if($v ==1 && !empty($cnrel1) && isset($cnrel1['cfgs'][$coclass_k])){
                                    $value =  explode(',',$cnrel1['cfgs'][$coclass_k]);
                                    $text = array();
                                    foreach($value as $valuek=>$valuev){
                                        	$text[] = @$coclass2[$valuev]['title'];                                                                          	
                                    }                                 
                                	$conditions[$k]['coid2'][$coclass_k] = array('value'=>$value,'text'=>$text);                                                   				                     
                    			}
                                if($v ==3 && !empty($cnrel2)){ 
                                    $value =  explode(',',$cnrel2['cfgs'][$coclass_k]);
                                    $text = array();
                                    foreach($value as $valuek=>$valuev){
                                        	$text[] = @$coclass14[$valuev]['title'];                                                                          	
                                    }                                 
                                	$conditions[$k]['coid14'][$coclass_k] = array('value'=>$value,'text'=>$text);                                                   				                     
                    			}
							}                   
					} 
				}else if(is_string($v)){
					$mode = substr($chidMode,0,strpos($chidMode,'_'));
					$modeNum = substr($chidMode,strpos($chidMode,'_')+1);										
					if(!empty($v)){		
						switch($mode){							
							case 'mchid':
								$mchid = intval($modeNum);
                                $field = cls_cache::Read('mfield', $mchid, $v);
                                if(in_array($field['datatype'],array('select','mselect'))){
									$items = explode("\n",$field['innertext']);
									foreach($items as $items_v){
										$items_v = explode('=', $items_v);
										$conditions[$k]['text'][] = $items_v[1];
										$conditions[$k]['value'][] = $items_v[0];                                                                                
                                        }									
								}elseif(in_array($field['datatype'],array('cacc',))){
									$coclass = cls_cache::Read('coclasses',$field['coid']);
                                    if(31==$field['coid']){//��Ʒ�������⴦��
                                        foreach($coclass as $coclass_k => $coclass_v){
                                            if(0 != $coclass_v['level']){
                                                $conditions[$k]['text'][] = $coclass_v['title'];
                                                $conditions[$k]['value'][] = $coclass_v['ccid'];
                                            }                                         
                                        }
                                    }else{
                                        foreach($coclass as $coclass_k => $coclass_v){
                                            $conditions[$k]['text'][] = $coclass_v['title'];
                                            $conditions[$k]['value'][] = $coclass_v['ccid'];
                                        }
                                    }
								}
							break;					
							case 'chid':				
								$chid = intval($modeNum);
								$field = cls_cache::Read('field', $chid, $v);
								if(in_array($field['datatype'],array('select','mselect'))){
									$items = explode("\n",$field['innertext']);
									foreach($items as $items_v){
										$items_v = explode('=', $items_v);
										$conditions[$k]['text'][] = $items_v[1];
										$conditions[$k]['value'][] = $items_v[0];
									}
								}elseif(in_array($field['datatype'],array('cacc',))){
									$coclass = cls_cache::Read('coclasses',$field['coid']);
									foreach($coclass as $coclass_k => $coclass_v){
										$conditions[$k]['text'][] = $coclass_v['title'];
										$conditions[$k]['value'][] = $coclass_v['ccid'];
									}
								}								
							break;						
						}
					}
				}
	   }
	   return $conditions;
	}
// ==============================================================================

////////////////////////////////////////////////////////////////////
// ���� - ���ݺ��� - ��ʱ����; ����ɾ��       ... //////////////////
////////////////////////////////////////////////////////////////////

/*
 * *******   v5���ݺ����ο���   *******
 * ���ʹ�÷ǹٷ�ģ�棬���Լ����ο������֣�ʹ���˾�ģ�溯����
 * 1. ���鰴�µķ�ʽ�����޸���ش��룻
 * 2. �� ��������ע�͵Ĵ��룻 
 * 3. �� ���ļ���utags.fun_v5x.php �и��Ƴ���ش��롣
*/

/* 

function u_filter_init($argv, $cfgs = array()){
	$G = cls_Parse::Get('G');
	$mconfigs = cls_cache::Read('mconfigs');
	$cms_abs = $mconfigs['cms_abs'];
	empty($G) && $G = array();
	empty($G['cache']) && $G['cache'] = array();
	empty($G['stack']) && $G['stack'] = array();
	$G['node'] = !empty($cfgs['nodes']);
	$G['nodes'] = array();
	$G['exts'] = array();
	$G['cfgs'] = &$cfgs;
	$G['addno'] = &$cfgs['addno'];
	$G['supply'] = &$cfgs['supply'];
	$G['argv'] = array();
	#��ʼ������
	$init = array(
		'fid' => 0,
		'chid' => 0,
		'addno' => 0,
		'script' => '',
		'rids' => array(),
		'nodes' => array(),
		'values' => array(),
		'include' => array()
	);
	foreach($init as $k => $v)isset($cfgs[$k]) || $cfgs[$k] = $v;
#	��һ�к�����Ҫ��ʱ����ʵ��
	empty($cfgs['addno']) && $cfgs['addno'] = '';
	isset($cfgs['eupno']) || $cfgs['eupno'] = $cfgs['addno'];	#����ȫ��ʱ�� addno
	(empty($cfgs['nodes'][0]) || !is_array($cfgs['nodes'][0])) && $cfgs['nodes'] = array($cfgs['nodes']);
	if(empty($cfgs['chid'])){
		$_temp_caid = cls_cache::Read('catalog', $argv['caid']);
		$cfgs['chid'] = @$_temp_caid['chids'];
		if(!$G['chids'] = strpos($cfgs['chid'], ',') ? 's' : ''){
			$G['fields'] = array();
			$fields = cls_cache::Read('fields', $cfgs['chid']);
			foreach($fields as $k => $v)($v['datatype'] == 'select' || $v['datatype'] == 'mselect') && $G['fields'][$k] = $v['cname'];
#			$cfgs['include'] = array_merge(array_keys($G['fields']), $cfgs['include']);
		}
	}else{
		if($cfgs['chid'] < 0)$cfgs['chid'] = 0;
		$G['chids'] = strpos($cfgs['chid'], ',') ? 's' : '';
	}
	foreach($cfgs['nodes'] as $v)$cfgs['include'] = array_merge($cfgs['include'], $v);
	$phpbug = in_array(0, $cfgs['include'], true);#PHP5.2.9-2 array_unique���ȫ��0ȥ��
	$cfgs['include'] = array_unique($cfgs['include']);
	$phpbug && !in_array(0, $cfgs['include'], true) && $cfgs['include'][] = 0;
	foreach($cfgs['include'] as $k){										#��Ҫ��ʾ���������
		$key = is_numeric($k) ? ($k ? "ccid$k" : 'caid') : $k;
		if(!$val = !empty($cfgs['values'][$key]) ? $cfgs['values'][$key] : (!empty($argv[$key]) ? $argv[$key] : 0))continue;
		if(is_numeric($k)){										#��ϵ�ڵ�
			$G['nodes'][] = $k;
			$G['name'][$key] = $argv[$k ? "{$key}title" : 'catalog'];
		}else{
			$G['exts'][$k] = true;
			$G['node'] = false;
			$name = cls_cache::Read('field', $cfgs['chid'], $k);
			if(!empty($name)){									#�ֶ�����
				$args = explode("\n", $name['innertext']);
				foreach($args as $v){
					$v = explode('=', $v);
					if($val == $v[0]){
						$G['name'][$key] = empty($v[1]) ? $v[0] : $v[1];
						break;
					}
				}
			}
		}
		$G['argv'][$key] = rawurlencode(stripslashes($val));
	}
	$rids = $cfgs['rids'];
	$cfgs['rids'] = array();
	$cfgs['clear'] = array();
	foreach($rids as $rid){
		if($rid){
			$cnrel = cls_cache::Read('cnrel', $rid);
			$cfgs['clear'][$cnrel['coid']] = $cnrel['coid1'];
			$k = @$G['argv'][$cnrel['coid'] ? "ccid$cnrel[coid]" : 'caid'];
			while($k){
				if($keys = @$cnrel['cfgs'][$k]){
					$cfgs['rids'][$cnrel['coid1']] = explode(',', $keys); //echo "$keys,";
					break;
				}else{
					#���û�й�����ȥ���ϼ���(ȫ��)
					//$k = cls_cache::Read($cnrel['coid'] ? 'coclass' : 'catalog', $cnrel['coid'] ? $cnrel['coid'] : $k, $k);
					//$k && $k = $k['pid'];
					#���û�й���(��Ȧ)��Ϊ��
					$cfgs['rids'][$cnrel['coid1']] = array(-1);
					break;
				}
			}
		}
	}
	$G['uargv'] = array();
	foreach($G['argv'] as $k => $v)$G['uargv'][$k] = "$k=$v";
	$script = $cfgs['script'] ? $cfgs['script'] : ($cfgs['fid'] ? "info.php?fid=$cfgs[fid]&" : 'search.php?');
	$G['surl'] = $cfgs['script'] ? "$cms_abs$script" : ("$cms_abs{$script}" . ($cfgs['chid'] ? "chid$G[chids]=$cfgs[chid]&" : ''));
	cls_Parse::Set('G',$G);
	$G['fill'] = empty($argv['filterstr']) ? u_search_url() : "$cms_abs$script$argv[filterstr]";#����
	cls_Parse::Set('G.fill',$G['fill']);
}

function u_node_check($coid, $remove = 0){
	$G = cls_Parse::Get('G');
	$flag = false;
	if($remove && !is_numeric($coid)){
		$exts = $G['exts'];
		if(is_array($coid))
			foreach($coid as $k)unset($exts[$k]);
		else
			unset($exts[$coid]);
		$flag = empty($exts);
	}
	if($G['node'] || $flag){
		$nodes = $G['nodes'];
		if($remove){
			$nodes = array_diff($nodes, is_array($coid) ? $coid : array($coid));
		}else{
			$nodes[] = $coid;
		}
		foreach($G['cfgs']['nodes'] as $k => $v){
			if(count($v) == count(array_unique(array_merge($v, $nodes))))return true;
		}
	}
	return false;
}

#�ӵ�һ����ʼ���������ң��Ϳ���ͨ�� pid �ҵ����/////�������
function u_caco_urls($coid, $level = 0, $keys = 0){
	$G = cls_Parse::Get('G');
	$_da = cls_Parse::Get('_da');
	if(isset($G['cache'][$cachekey = "u_caco_urls:$coid|$level"]))return $G['cache'][$cachekey];
	$cache = array();
	$argv = $G['argv'];
	$uargv = $G['uargv'];
	$G['cache'][$cachekey] = &$cache;

	if($coid){
		$key = "ccid$coid";
		$caco = cls_cache::Read('coclasses', $coid);
	}else{
		$key = 'caid';
		$catalogs = cls_cache::Read('catalogs');
		$caco = &$catalogs;
	}
	if(!isset($G['stack'][$coid])){
		#���ɽڵ���ջ
		$G['stack'][$coid] = array(0);
#		$pid = empty($_da[$key]) ? 0 : $_da[$key];
		$pid = empty($argv[$key]) ? 0 : $argv[$key];
		if($pid && is_numeric($pid)){
			while($pid){
				$G['stack'][$coid][$caco[$pid]['level'] + 1] = $pid;
				$pid = $caco[$pid]['pid'];
			}
		}
	}

	if(!isset($G['stack'][$coid][$level]))return $cache;
	$pid = $G['stack'][$coid][$level];
	$G['selected'] = @$G['stack'][$coid][$level + 1];
	#�������ֵ
	$clear = $G['cfgs']['clear'];
	if(isset($clear[$coid])){
		unset($argv[$k = ($v = $clear[$coid]) ? "ccid$v" : 'caid']);
		unset($uargv[$k]);
	}
	$keys || $keys = @$G['cfgs']['rids'][$coid];
	#��ϵΪ��
	$keys || $keys = array_keys($caco);
	if(u_node_check($coid)){
		#Ϊ�ڵ��ʱ��
		foreach($keys as $k){
			if(empty($caco[$k])) continue;
			if($pid != $caco[$k]['pid'])continue;#ֻҪ������ϵ
			$argv[$key] = $k;

			$node = cls_node::cnodearr(cnstr($argv));
			if($coid == 12){
				$caco[$k]['chids'] = array_filter(explode(",",$caco[$k]['chids']));

				if(in_array($G['cfgs']['chid'],$caco[$k]['chids'])){
					$cache[$k] = array(
						'title' => $caco[$k]['title'],
						'url' => $node["indexurl$G[addno]"],
						'level' => $caco[$k]['level']
					);
				}
			}else{
				$cache[$k] = array(
					'title' => $caco[$k]['title'],
					'url' => @$node["indexurl$G[addno]"],
					'level' => $caco[$k]['level']
				);
			}
		}
	}else{
		foreach($keys as $k){
			if(empty($caco[$k])) continue;
			if($pid != $caco[$k]['pid'])continue;#ֻҪ������ϵ
			$uargv[$key] = "$key=$k";

			$cache[$k] = array(
				'title' => $caco[$k]['title'],
				'url' => $G['surl'] .  (empty($G['surl']) ? '' : substr($G['surl'],-1,1)=='&'?'':'&') . implode('&', $uargv),
				'level' => $caco[$k]['level']
			);

		}
	}
	cls_Parse::Set('G',$G);
	return $cache;
}

function u_field_urls($key){
	$G = cls_Parse::Get('G');
	$mconfigs = cls_cache::Read('mconfigs');
	$cms_abs = $mconfigs['cms_abs'];
	if(isset($G['cache'][$cachekey = "u_field_urls:$key"]))return $G['cache'][$cachekey];
	$cache = array();
	$G['cache'][$cachekey] = &$cache;

	if($G['chids'])return $cache;
	$field = cls_cache::Read('field', $G['cfgs']['chid'], $key);
	$args = array();
	$argv = explode("\n", $field['innertext']);
	foreach($argv as $v){
		$v = explode('=', $v);
		$args[$v[0]] = empty($v[1]) ? $v[0] : $v[1];
	}

	$uargv = $G['uargv'];
	foreach($args as $k => $v){
		$uargv[$key] = "$key=" . rawurlencode($k);
		$cache[$k] = array(
			'title' => $v,
			'url' => $G['surl'] . (empty($G['surl']) ? '' : substr($G['surl'],-1,1)=='&'?'':'&') . implode('&', $uargv)
		);
	}
	cls_Parse::Set('G',$G);
	return $cache;
}

function u_pick_urls($all = 0){
	$G = cls_Parse::Get('G');
	if(isset($G['cache'][$cachekey = "u_pick_urls:$all"]))return $G['cache'][$cachekey];
	$cache = array();
	$G['cache'][$cachekey] = &$cache;

	$clear = $G['cfgs']['clear'];
	foreach($G['cfgs']['include'] as $k){
		$key = is_numeric($k) ? ($k ? "ccid$k" : 'caid') : $k;
		if(!$all && empty($G['argv'][$key]))continue;
		$argv = $G['argv'];
		$uargv = $G['uargv'];
		if(empty($G['supply'][$key])){
			unset($argv[$key]);
			unset($uargv[$key]);
		}else{
			$argv[$key] = $G['supply'][$key];
			$uargv[$key] = "$key={$G['supply'][$key]}";
		}
		#�������ֵ
		if(isset($clear[$k])){
			unset($argv[$z = ($v = $clear[$k]) ? "ccid$v" : 'caid']);
			unset($uargv[$z]);
		}
		if(u_node_check($k, 1)){
			$node = cls_node::cnodearr(cnstr($argv));
#			$cnstr = $node["indexurl$G[addno]"];
#			��һ�к�����Ҫ��ʱ����ʵ��
			$cnstr = @$node['indexurl' . (count($argv) != 1 ? $G['addno'] : $G['cfgs']['eupno'])];
		}else{
			$cnstr = $G['surl'] . implode('&', $uargv);
		}
		$cache[$key] = array(
			'title' => @$G['name'][$key],
			'url' => $cnstr
		);
	}
	cls_Parse::Set('G',$G);
	return $cache;
}

function u_extra_url($key, $node = 0){
	$G = cls_Parse::Get('G');
	if(isset($G['cache'][$cachekey = "u_extra_url:$key|$node"]))return $G['cache'][$cachekey];
	$kz = explode('|', $key);
	if($node && u_node_check($kz, 1)){
		$argv = $G['argv'];
		foreach($kz as $k)unset($argv[$k]);
		$node = cls_node::cnodearr(cnstr($argv));
#		$cnstr = $node["indexurl$G[addno]"];
#		��һ�к�����Ҫ��ʱ����ʵ��
		$cnstr = $node['indexurl' . (count($argv) != 1 ? $G['addno'] : $G['cfgs']['eupno'])];
	}else{
		$cnstr = preg_replace("/&(?:$key)=[^&]*|\b(?:$key)=[^&]*&?/", '', $G['fill']);
	}
	return $G['cache'][$cachekey] = $cnstr;
}

function u_search_url(){
	$G = cls_Parse::Get('G');
	if(isset($G['cache'][$cachekey = 'u_search_url']))return $G['cache'][$cachekey];
	return $G['cache'][$cachekey] = $G['surl'] . implode('&', $G['uargv']);
}

function u_gettop_caco($id, $coid = 0, $level = 0){
	if($coid){
		$key = "ccid$coid";
		$caco = cls_cache::Read('coclasses', $coid);
	}else{
		$key = 'caid';
		$caco = cls_cache::Read('catalogs');
	}
	while(!empty($caco[$id]['level']) && $caco[$id]['level'] != $level)$id = $caco[$id]['pid'];
	return @$caco[$id];
}

function u_fliter_html($title, $field, $value, $rid = 0){
	if(is_numeric($field)){
		$rows = u_caco_urls($field, $rid);
		$field = $field ? "ccid$field" : 'caid';
	}else{
		$rows = u_field_urls($field);
	}
	$current = $value ? '' : ' class="current"';
	$pick = u_pick_urls(1);
	$pickurl = $pick[$field]['url'];
	echo "
<dl>
			<dt>{$title}��</dt>
			<dd>
				<ul>
					<li$current><a href=\"$pickurl\">����</a></li>";
	foreach($rows as $k => $v){
		$current = $k == $value ? ' class="current"' : '';
		echo "
					<li$current><a href=\"$v[url]\">$v[title]</a></li>";
	}
	return '
				</ul>
			</dd>
		</dl>';
}

function u_order_set($title, $by, $orderby, $ordermode, $class){
	$url = u_extra_url('orderby|ordermode');
	return '<i class="' . $class[$by === $orderby ? ($ordermode ? 1 : 0) : 2] . "\"><a rel=\"nofollow\" href=\"$url&orderby=$by" . ($by != $orderby || empty($ordermode) ? '&ordermode=1' : '') . "\">$title</a></i>";
}


//*/

 