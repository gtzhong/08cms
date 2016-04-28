<?php
/**
 * �ýű��Ѿ�����������������ϢAJAX����������ʱ����� /libs/classes/ajax/ajax_$action.php ����ļ�˵���鿴���޸�
 * ԭ���� eools�µ� ajax.php�ű��Ѿ��Ƶ�����/extend_sample/libs/classes/Ŀ¼��
 */
include_once dirname(dirname(__FILE__)).'/include/general.inc.php';
include_once M_ROOT.'include/field.fun.php';
m_clear_ob();
empty($action) && die();
/**
 * ����Ajax����ֻҪ�ڵ���JSʱ�ഫ��һ�������� domain=news.08cms.com   �������Զ��壬
 * ���ǵ���ȫ���⣬���������ں�̨�� ϵͳ���� -> �������� -> �������� ��ͬʱ���ڸ������ַ����
 * 
 * @example $.get($cms_abs + "tools/ajax.php?action=get_regcode&domain=" + document.domain, function(data) { .... });
 */
isset($domain) && _08_Controller_Base::setDomain($domain);


# ��ʱ�øýű�ִ����·��
$_SERVER['QUERY_STRING'] = str_replace(array('&', '='), '/', '/' . $_SERVER['QUERY_STRING']);
$_SERVER['QUERY_STRING'] = preg_replace('@^/action/@i', '/ajax/', $_SERVER['QUERY_STRING']);
if ( $action == 'ajax_arc_list' )
{
    $_SERVER['QUERY_STRING'] = str_replace('/ajax_arc_list/', '/arc_list/', $_SERVER['QUERY_STRING']);
}

if( in_array($action, array('checkUser', 'checkEmail')) ) 
{
	$_SERVER['QUERY_STRING'] = str_replace("/$action/", '/Check_Member_Info/', $_SERVER['QUERY_STRING']);
}

if ( in_array($action, array('ajax_arc_list', 'fetchcnodeurl', 'block', 'ablock', 'fblock', 'pblock', 'mblock', 'subject','stat','mark', 'memcert', 'dirname', 'mdirname', 'frnamesame', 'check_sitemaps_repeat')) )
{
    $_SERVER['QUERY_STRING'] .= '/datatype/xml/';
}
_08_factory::getApplication()->run();
call_user_func(array($controller, $action));

exit;

switch($action){
### �ù��ܽӿ��Ѿ��Ƶ���_08_M_Ajax_Arc_list::__toString();
case 'ajax_arc_list': //�ĵ����ʱ-ѡ�������ϼ�
    
//	$chid = max(0,intval($chid));
//	!empty($keywords) && $keywords = @cls_string::iconv("UTF-8",$mcharset,$keywords);
//	//$wherestr = empty($wherestr) ? '' : " AND ".base64_decode($wherestr); //����������,�ݲ���,���ܱ��������ĵ��ֶ�...
//	$result = array(); 
//	if($ntbl = atbl($chid)){ 
//		$db->select('a.*,c.*')->from("#__{$ntbl} a")->innerJoin("#__archives_{$chid} c");
//		$db->_on("a.aid=c.aid")->where("checked=1");
//		//$wherestr && $db->_and($wherestr);
//		$db->_and('a.subject')->like($keywords)->limit(100)->exec();
//		//if(!empty($query)){
//			while($r=$db->fetch()){
//				$thumb = $r['thumb'];
//				$thumb = empty($thumb) ? '' : '[ͼ]';
//				$result[] = array('aid' => $r['aid'], 'subject'=>$thumb.$r['subject'],'create'=>date('Y-m-d',$r['createdate']));
//			}
//		//}
//	}
//	echo cls_message::ajax_info($result);
break;

### �ù��ܽӿ��Ѿ��Ƶ���_08_M_Ajax_Fetchcnodeurl_Base::__toString();
case 'fetchcnodeurl':
//	//ȡ�ýڵ��url��������url����$urltype,��$caid��$ccid2��ʽ����Ŀ����
//	$temparr = cls_env::_GET();
//	$cnstr = cls_cnode::cnstr($temparr);
//	if(!($cnode = cls_node::cnodearr($cnstr))) cls_message::ajax_info('#');
//	cls_message::ajax_info($cnode[empty($urltype) ? 'indexurl' : $urltype]);
	break;

### �ù��ܽӿ��Ѿ��Ƶ���_08_M_Ajax_Block_Base::__toString();
case 'block':
//	$output = cls_cotype::BackMenuBlock((int)@$coid,(int)@$ccid);
//	cls_message::ajax_info($output);
	break;

### �ù��ܽӿ��Ѿ��Ƶ���_08_M_Ajax_Ablock_Base::__toString();
case 'ablock':
//	$output = cls_cotype::BackMenuBlock(0,(int)@$caid);
//	cls_message::ajax_info($output);
	break;

### �ù��ܽӿ��Ѿ��Ƶ���_08_M_Ajax_Fblock_Base::__toString();
case 'fblock':
//	$output = cls_fcatalog::BackMenuBlock(@$fcaid);
//	cls_message::ajax_info($output);
	break;

### �ù��ܽӿ��Ѿ��Ƶ���_08_M_Ajax_Pblock_Base::__toString();
case 'pblock':
//	$output = cls_PushArea::BackMenuBlock(@$paid);
//	cls_message::ajax_info($output);
	break;

### �ù��ܽӿ��Ѿ��Ƶ���_08_M_Ajax_Mblock_Base::__toString();
case 'mblock':
//	$output = cls_mchannel::BackMenuBlock((int)@$mchid);
//	cls_message::ajax_info($output);
	break;

### �ù��ܽӿ��Ѿ��Ƶ���_08_M_Ajax_Subject_Base::__toString();
case 'subject':
//	if(empty($table) || empty($subject) || preg_match('/\W/', $table)){
//		$output = '-1';
//	}else{
//		$output = $db->result_one("SELECT COUNT(*) FROM {$tblprefix}$table WHERE subject='$subject' LIMIT 0,1");
//	}
//	cls_message::ajax_info($output);
	break;
case 'stat':

### �ù��ܽӿ��Ѿ��Ƶ���_08_M_Ajax_Stat_Base::__toString();
//	preg_match("/^\d+(,\d+)?(?:,\d+)*$/", $aids, $match) || exit();
//	$sql  =	'SELECT a.clicks,a.comments,a.scores,a.orders,a.favorites,a.praises,a.debases,a.answers,a.adopts,a.price,a.crid,a.currency,a.closed,a.downs,a.plays,a.mclicks,a.mplays,a.mdowns,a.wclicks,a.wdowns,a.wplays,' .
//			" FROM {$tblprefix}archives a WHERE a.checked=1 AND a.aid ";
//	$sql .=	empty($match[1]) ? "=$aids" : "IN ($aids)";
//	$query = $db->query($sql);
//	$output = '';
//	while($row = $db->fetch_array($query)){
//		$output .= ",$row[aid]:{";
//		unset($row['aid']);
//		$row = array_filter($row);
//		$tmp = '';
//		foreach($row as $k => $v)$tmp .= ",$k:$v";
//		$output .= substr($tmp, 1) . '}';
//	}
//	cls_message::ajax_info('{' . substr($output, 1) . '}');
	break;
    
### �ù��ܽӿ��Ѿ��Ƶ���_08_M_Ajax_Mark_Base::__toString();
case 'mark'://�����¼
//	$aid = empty($aid) ? 0 : max(0,intval($aid));
//	if(!$aid || !($ntbl = atbl($aid,2))) exit();
//	if(!($db->result_one("SELECT COUNT(*) FROM {$tblprefix}$ntbl WHERE aid='$aid' AND checked=1"))) exit();
//	$cookie_key = "BR_R_$memberid";
//	$limit = 30;
//	$tmp = empty($m_cookie[$cookie_key]) ? array() : explode(';', $m_cookie[$cookie_key]);
//	in_array($aid, $tmp) || $tmp[] = "$aid,$timestamp";
//	$cookie_val = implode(';', count($tmp) > $limit ? array_splice($tmp, -$limit) : $tmp);
//	msetcookie($cookie_key, $cookie_val);
//	exit;
	break;
    
### �ù��ܽӿ��Ѿ��Ƶ���_08_M_Ajax_Caid_Base::__toString();
case 'caid':
//	empty($varname) && exit();
//	$framein = empty($framein) ? 0 : 1;
//	$chid = empty($chid) ? 0 : max(0,intval($chid));
//	$arr_mode = array();
//	header("Content-Type: text/javascript");
//	echo "var $varname=[";
//	if(!empty($ids)){
//		$ids = explode(',',$ids);
//		foreach($ids as $k) $arr_mode[] = cls_catalog::uccidsarr(empty($coid)? 0 : $coid,$chid,$framein,1,1,$k);
//		$_tmp = array();
//		foreach($arr_mode as $p){
//			foreach($p as $k2=>$p2){
//				$_tmp[$k2] = $p2;
//			}
//		}
//		cls_catalog::uccidstop($_tmp);
//		$cnt = 0;
//		foreach($_tmp as $k=>$v){
//			echo ($cnt ? ',' : '' )."[$k,$v[pid],'".addslashes($v['title'])."',".(empty($v['unsel']) ? 0 : 1) . ']';
//			$cnt++;
//		}	
//	}else{
//		$ccidsarr = cls_catalog::uccidsarr(0,$chid,$framein,1,1);
//		cls_catalog::uccidstop($ccidsarr);
//		foreach($ccidsarr as $k => $v)echo "[$k,$v[pid],'".addslashes($v['title'])."'".(empty($v['unsel']) ? '' : ',1') . '],';
//	}		
//	echo ']';
	break;
    
### �ù��ܽӿ��Ѿ��Ƶ���_08_M_Ajax_Coid_Base::__toString();
case 'coid':
//	$framein = empty($framein) ? 0 : 1;
//	$chid = empty($chid) ? 0 : max(0,intval($chid));
//	$coid = empty($coid) ? 0 : max(0,intval($coid));
//	empty($varname) || empty($coid) && exit();
//	$ccidsarr = cls_catalog::uccidsarr($coid,$chid,$framein,1,1);
//	cls_catalog::uccidstop($ccidsarr);
//	header("Content-Type: text/javascript");
//	echo "var $varname=[";
//	foreach($ccidsarr as $k => $v)echo "[$k,$v[pid],'".addslashes($v['title'])."'".(empty($v['unsel']) ? '' : ',1') . '],';
//	echo ']';
	break;
    
### �ù��ܽӿ��Ѿ��Ƶ���_08_M_Ajax_Cacc_Base::__toString();
case 'cacc':
//	if(!empty($type) && !empty($ename)){
//		header("Content-Type: text/javascript");
//		echo "var $varname=[";
//		$arr = cacc_arr($type,empty($tpid) ? 0 : intval($tpid),$ename);
//		// for ����ĸ���� add Letter
//		foreach($arr as $k=>$v){
//			if($v['level']==0 && $v['letter']){
//				$arr[$k]['title'] = $v['letter'].' '.$v['title']; //,"$v[title]"
//			}
//		}
//		cls_catalog::uccidstop($arr);
//		foreach($arr as $k => $v) echo "[$k,$v[pid],'".addslashes($v['title'])."',".(empty($v['unsel']) ? 0 : 1) . '],';
//		echo ']';
//	}
	break;
    
### �ù��ܽӿ��Ѿ��Ƶ���_08_M_Ajax_Check_Member_Info_Base::__toString();
case 'checkEmail': // ���Email�Ƿ��ظ� ... ��̨���ӻ�Ա �ȵط�ʹ��(ǰ̨ע��,�ɿ���һ��ʹ�����)
//	$val = empty($val) ? '' : cls_string::iconv("utf-8",$mcharset,$val); 
//	$re = cls_userinfo::CheckSysField($val,'email');
//	if($re['error']) mexit($re['error']);
//	else mexit('');
	break;
    
### �ù��ܽӿ��Ѿ��Ƶ���_08_M_Ajax_Check_Member_Info_Base::__toString();
case 'checkUser': // ����Ա�Ƿ��ظ� ... ��̨���ӻ�Ա �ȵط�ʹ��(ǰ̨ע��,�ɿ���һ��ʹ�����)
//	$val = empty($val) ? '' : cls_string::iconv("utf-8",$mcharset,$val); 
//	$re = cls_userinfo::CheckSysField($val,'mname');
//	if($re['error']) mexit($re['error']);
//	else mexit('');
	break;
    
### �ù��ܽӿ��Ѿ��Ƶ���_08_M_Ajax_Check_Paid_Base::__toString();
case 'check_paid': // ����λpaid�����Ƿ�Ϸ�
//	$msg = cls_PushArea::CheckNewID(@$paid);
//	mexit($msg);	
	break;
    
### �ù��ܽӿ��Ѿ��Ƶ���_08_M_Ajax_Check_Fcaid_Base::__toString();
case 'check_fcaid': // ��������fcaid�����Ƿ�Ϸ�
//	$msg = cls_fcatalog::CheckNewID(@$fcaid);
//	mexit($msg);	
	break;
    
### �ù��ܽӿ��Ѿ��Ƶ���_08_M_Ajax_Check_Fieldname_Base::__toString();
case 'check_fieldname': // �ֶα�ʶ�Ƿ�Ϸ�
//	$msg = cls_fieldconfig::CheckNewID(@$sourcetype,@$sourceid,@$fieldname);
//	mexit($msg);	
	break;
    
### �ù��ܽӿ��Ѿ��Ƶ���_08_M_Ajax_CheckUnique_Base::__toString();
case 'checkUnique': // ���ݻ�Ա��֤����id,mchid,��֤�ֶ� �Ƿ��ظ����������ǿɼ����ĵ�
//	$val = empty($val) ? '' : $val;
//	$oldval = empty($oldval) ? '' : $oldval;
//	$mctid = empty($mctid) ? 0 : max(0,intval($mctid));
//	$mchid = empty($mchid) ? 0 : max(0,intval($mchid));
//	$mctypes = cls_cache::Read('mctypes');
//	$mfields = cls_cache::Read('mfields',$mchid); 
//	$field = $mctypes[$mctid]['field']; 
//	if(!isset($mctypes[$mctid]) || !isset($mfields[$field])){
//		$msg = '��������';
//	}else{
//		$sql = "SELECT mid FROM {$tblprefix}".$mfields[$field]['tbl']." WHERE $field='$val'";
//		$mid = $db->result_one($sql);
//		$msg = $mid ? 'Exists' : 'OK';
//	}
//	//echo $msg;
//	if(empty($method)){ //��js��֤
//		cls_message::ajax_info(array('msg'=>$msg));
//	}else{ //ʹ��validator.js��֤
//		if($oldval && $msg=='Exists' && $oldval==$val) $msg = "";	
//		elseif($msg=='Exists') $msg = "�����Ѿ����ڣ�";	
//		elseif($msg=='OK') $msg = "";
//		mexit($msg);	
//	}
	break;
    
### �ù��ܽӿ��Ѿ��Ƶ���_08_M_Ajax_Memcert_Base::__toString();
case 'memcert':
//	$mctypes = cls_cache::Read('mctypes');
//	$msg = isset($mctypes[$mctid]['msg']) ? $mctypes[$mctid]['msg'] : '����ȷ����Ϊ%s��';
//	$info = array();
//	$mobile = empty($mobile) ? "" : $mobile;
//	if($option == 'msgcode'){
//		if(strlen($mobile)<10){
//			$info = array(
//				'time' => 0,
//				'text' => '�ֻ������ʽ����'
//			);
//		}elseif(preg_match("/^\d{3,4}[-]?\d{7,8}$/", $mobile)){
//			$msgcode = cls_string::Random(6, 1);
//			if(empty($sms_cfg_api) || ($sms_cfg_api == '(close)')){
//				$info = array(
//					'time' => -1,
//					'text' => 'ϵͳû�����ö��Žӿ�ƽ̨!'
//				);
//			}else{
//				@list($inittime, $initcode) = maddslashes(explode("\t", @authcode($m_cookie['08cms_msgcode'],'DECODE')),1);
//				if(($timestamp - $inittime) > 60){
//
//					$msg = str_replace('%s', $msgcode, $msg);
//
//					$sms = new cls_sms();
//					$msg = $sms->sendSMS($mobile,$msg,'ctel');
//
//					if($msg[0]==1){
//						msetcookie('08cms_msgcode', authcode("$timestamp\t$msgcode", 'ENCODE'));
//					}else{
//						$info = array(
//							'time' => -1,
//							'text' => '������Ϣʧ�ܣ�����ϵ����Ա��'
//						);
//					}
//				}else{
//					$info = array(
//						'time' => 1,
//						'text' => '�벻Ҫ�ظ��ύ���ȴ�ϵͳ��Ӧ'
//					);
//				}
//			}
//		}else{
//			$info = array(
//				'time' => 0,
//				'text' => '�ֻ������ʽ����'
//			);
//		}
//	}
//	cls_message::ajax_info($info);
	break;
    
### �ù��ܽӿ��Ѿ��Ƶ���_08_M_Ajax_Dirname_Base::__toString();
case 'dirname'://
//	if(empty($value)){
//		cls_message::ajax_info(-1);
//	}else{
//		$value = strtolower(trim($value));
//		in_array($value,cls_cache::Read('cn_dirnames')) && cls_message::ajax_info(1);
//	}
//	cls_message::ajax_info(0);
	break;
    
### �ù��ܽӿ��Ѿ��Ƶ���_08_M_Ajax_Mdirname_Base::__toString();
case 'mdirname':
//	if(empty($value)){
//		cls_message::ajax_info(-1);
//	}else{
//		$value = strtolower(trim($value));
//		$db->result_one("SELECT 1 FROM {$tblprefix}members WHERE mspacepath='$value'") && cls_message::ajax_info(1);
//	}
//	cls_message::ajax_info(0);
	break;
    
### �ù��ܽӿ��Ѿ��Ƶ���_08_M_Ajax_Frnamesame_Base::__toString();
case 'frnamesame':
//	if(empty($value)){
//		cls_message::ajax_info(-1);
//	}else{
//		$value = strtolower(trim($value));
//		$db->result_one("SELECT COUNT(*) FROM {$tblprefix}fragments WHERE ename='$value'") && cls_message::ajax_info(1);
//	}
//	cls_message::ajax_info(0);
	break;
    
### �ù��ܽӿ��Ѿ��Ƶ���_08_M_Ajax_Check_Sitemaps_Repeat_Base::__toString();
case 'check_sitemaps_repeat':
//	if(empty($value)){
//		cls_message::ajax_info(-1);
//	}else{
//		$value = addslashes($value);
//		$sql = "SELECT 1 FROM {$tblprefix}sitemaps WHERE ename='$value'";
//		$db->fetch_one($sql) && cls_message::ajax_info(1);
//	}
//	cls_message::ajax_info(0);
	break;
    
### �ù��ܽӿ��Ѿ��Ƶ���_08_M_Ajax_Regcode_Base::__toString();
case 'regcode':
//	usleep(200000);#��ͣ200���룬��ע����������������ƽ�...
//	header("content-type: text/html; charset=$mcharset");
//	empty($verify) && $verify = '08cms_regcode';
//    $msg = (!empty($js) ? 'var msg = "' : '');
//	list($inittime, $initcode) = @maddslashes(explode("\t", authcode($m_cookie[$verify], 'DECODE')), 1);
//    $msg .= ($timestamp - $inittime) > 1800 || strtolower($initcode) != strtolower($regcode) ? '��֤�����' : '';
//    #$msg .= var_export($m_cookie, true);
//    $msg .= (!empty($js) ? '";' : '');
//	mexit($msg);
    break;
    
### �ù��ܽӿ��Ѿ��Ƶ���_08_M_Ajax_Floor_Base::__toString();
case 'floor':
//	$v = explode(':', $querydata);
//	(preg_match('/^m?(?:comment|reply)s$/', $v[0]) && preg_match('/^\w+(,\w+)*$/', $v[1]) && preg_match('/^\d+(,\d+)*$/', $v[2])) || exit();
//
//	preg_match('/\bcid\b/', $v[1]) || $v[1] .= ',cid';
//
//	$querydata = array($v[0] => array());
//	$point = &$querydata[$v[0]];
//	$query = $db->query("SELECT $v[1] FROM $tblprefix$v[0] WHERE cid IN ($v[2])");
//	while($row = $db->fetch_array($query)){
//		$point[$row['cid']] = $row;
//		unset($point[$row['cid']]['cid']);
//	}
//	echo empty($callback) ? jsonEncode($querydata, 1) : $callback . '(' . jsonEncode($querydata, 1) . ')';
	break;
    
### �ù��ܽӿ��Ѿ��Ƶ���_08_M_Ajax_Mysource_Base::__toString();
case 'mysource':
//	header("content-type:text/html;charset=$mcharset");
//	$str = '<div class="coolbg4" onmousedown="aListSetMoving.Move(\'mysource\',event)">[<a href="'.$cms_abs.'tools/edit_source.php?" onclick="HideObj(\'mysource\');removeObj(\'mysource\');return floatwin(\'editmysource\',this,400,400);">����</a>]&nbsp;[<a href="javascript:void(0)" onclick="javascript:HideObj(\'mysource\');">�ر�</a>]</div><div class="wsselect">';
//	$mysource = cls_cache::cacRead('mysource');
//	foreach($mysource as $s){
//		$str .= "<a href=\"javascript:void(0)\" onclick=\"javascript:PutSource('$s')\">$s</a> | ";
//	}
//	$str .= "</div><div class='coolbg5'>&nbsp;</div>";
//	echo $str;
	break;
    
### �ù��ܽӿ��Ѿ��Ƶ���_08_M_Ajax_Myauthor_Base::__toString();
case 'myauthor':
//	header("content-type:text/html;charset=$mcharset");
//	$str = '<div class="coolbg4" onmousedown="aListSetMoving.Move(\'myauthord\',event)">[<a href="'.$cms_abs.'tools/edit_author.php?" onclick="HideObj(\'myauthor\');removeObj(\'myauthor\');return floatwin(\'editmyauthor\',this,400,400);">����</a>]&nbsp;[<a href="javascript:void(0)" onclick="javascript:HideObj(\'myauthor\');">�ر�</a>]</div><div class="wsselect">';
//	$myauthor = cls_cache::cacRead('myauthor');
//	foreach($myauthor as $s){
//		$str .= "<a href=\"javascript:void(0)\" onclick=\"javascript:PutAuthor('$s')\">$s</a> | ";
//	}
//	$str .= "</div><div class='coolbg5'>&nbsp;</div>";
//	echo $str;
	break;
    
### �ù��ܽӿ��Ѿ��Ƶ���_08_M_Ajax_Mykeyword_Base::__toString();
case 'mykeyword':
//	header("content-type:text/html;charset=$mcharset");
//	$str = '<div class="coolbg4" onmousedown="aListSetMoving.Move(\'mykeyword\',event)">[<a href="javascript:void(0)" onclick="javascript:HideObj(\'mykeyword\');">�ر�</a>]</div><div class="wsselect">';
//	$query = $db->query("SELECT sword FROM {$tblprefix}wordlinks limit 0,100");
//	while($s = $db->fetch_row($query)){
//		$str .= "<a href=\"javascript:void(0)\" onclick=\"javascript:PutKeyword('$s[0]')\">$s[0]</a> | ";
//	}
//	$str .= "</div><div class='coolbg5'>&nbsp;</div>";
//	echo $str;
	break;
    
### �ù��ܽӿ��Ѿ��Ƶ���_08_M_Ajax_Save_Tag_Cache_Base::__toString();
case 'save_tag_cache' :
//    if($re = $curuser->NoBackFunc('tpl')) cls_message::show($re);
//    $fn = trim($fn);
//    if(in_array(true, array(empty($createrange), empty($fn)))) {
//        exit('����ѡ�����ݣ�');
//    }
//    if(!is_dir(_08_TEMP_TAG_CACHE)) {
//        if(false == @mkdir(_08_TEMP_TAG_CACHE, 0777)) {
//            die('��������Ŀ¼ʧ�ܣ�');
//        }
//    }
//    try {
//        // ��ճ���һ��ʱ��Ļ����ļ�
//        $iterator = new DirectoryIterator(_08_TEMP_TAG_CACHE);
//        $_file = _08_FilesystemFile::getInstance();
//        foreach ($iterator as $file)
//        {
//            if(@$iterator->isFile($file) && ((time() - $iterator->getMTime()) >= 86400)) {
//                $_file->delFile($iterator->getPathname());
//            }
//        }
//    } catch (RuntimeException $e) {
//        die($e->getMessage());
//    }
//
//    $createrange = (array)cls_TagAdmin::CodeToTagArray($createrange);
//	cls_Array::array_stripslashes($createrange);//�������ݿ⣬��ת��ȡ��
//	
//    // ���浱ǰѡ���ı��������ļ�
//    cls_CacheFile::cacSave($createrange, $fn, _08_TEMP_TAG_CACHE);
    break;
    
### �ù��ܽӿ��Ѿ��Ƶ���_08_M_Ajax_Show_Bank_Base::__toString();
case 'show_bank' :
    #exit(_08_Loader::import('images:common:bank:index', array(), '.html'));
    break;
break;

    
### �ù��ܽӿ��Ѿ��Ƶ���_08_M_Ajax_Check_Mtagename_Base::__toString();
case 'check_mtagename' :
//include_once M_ROOT . _08_ADMIN . DS . 'mtags' . DS . '_taginit.php';
//   $val = empty($val) ? '' : cls_string::iconv("utf-8",$mcharset,$val);
//   $older = empty($older) ? '' : cls_string::iconv("utf-8",$mcharset,$older);
//   if($val == $older) exit;
//   $mtags = load_mtags($tag);
//   $flag = false;
//   foreach($mtags as $k => $v){ 
//      if($v['ename'] === $val){ 
//	  	  $flag = true; break;
//	  }
//   }
//   $flag && exit($val."�Ѿ�����");
    break;
break;
    
### �ù��ܽӿ��Ѿ��Ƶ���_08_M_Ajax_Check_Mtagtemplate_Base::__toString();
case 'check_mtagtemplate' :
//include_once M_ROOT . _08_ADMIN . DS . 'mtags' . DS . '_taginit.php';
//   $val = empty($val) ? '' : cls_string::iconv("utf-8",$mcharset,$val);
//   $val = trim($val);
//   //!preg_match('/^[a-zA-Z]{1}[a-zA-Z0-9-_.]+(\.html|\.htm)$/',$val) && exit('��ʽ����ȷ');
//   $older = empty($older) ? '' : cls_string::iconv("utf-8",$mcharset,$older);
//   if($val == $older) exit;
//   if(is_file( cls_tpl::TemplateTypeDir('tpl').$val)) exit($val.'�Ѿ����ڣ�');
break;
    
### �ù��ܽӿ��Ѿ��Ƶ���_08_M_Ajax_Get_Regcode_Base::__toString();
# ��ȡ��֤��
case 'get_regcode' :
//	$verify = empty($verify) ? '' : trim($verify);
//    $regtype = empty($regtype) ? '' : trim($regtype);
//	$inputName = empty($input_name) ? '' : trim($input_name);
//	$formName = empty($form_name) ? '' : trim($form_name);
//	$class = empty($class) ? '' : trim($class);
//	$inputString = empty($input_string) ? '' : trim($input_string);
//    if ( $regtype )
//    {
//        if ( @in_array($regtype,explode(',',$cms_regcode)) )
//        {
//            exit(_08_HTML::getCode($verify, $formName, $class, $inputName, $inputString));
//        }
//    }
break;
# ��ȡ��ǩ
### �ù��ܽӿ��Ѿ��Ƶ���_08_M_Ajax_Get_Tag_Base::__toString();
case 'get_tag' :
//	# ���οͳ�ʼ����ǰ��Ա���趨δ��Ч????????????
//	
//	$_DataFormat = '';
//	if(!empty($data_format)){
//		switch(strtolower($data_format)){
//			case 'js':
//				$_DataFormat = 'get_tag_js';
//			break;
//		}
//	}
//    cls_JsTag::Create(array('DataFormat' => $_DataFormat,'DynamicReturn'=>true));
break;
#��ȡ���
### �ù��ܽӿ��Ѿ��Ƶ���_08_M_Ajax_Get_Adv_Base::__toString();
case 'get_adv' ://
//    if ( !empty($fcaids) )
//    {
//        $fcaids = array_filter(explode(',', $fcaids));
//        if ( empty($params) )
//        {
//            $params = array();
//        }
//        else
//        {
//        	$params = json_decode(str_replace("'", "\"", stripslashes($params)), true);
//        }
//		
//		$contents = '';
//		$advSplit = 0;
//		foreach($fcaids as $fcaid){
//			$contents .= $advSplit ? '<!--_08_ADV_SPILT-->' : '';
//			$_nParams = empty($params[$fcaid]) ? array() : $params[$fcaid];
//			$_nParams['fcaid'] = $fcaid;
//			$_nParams['DynamicReturn'] = true;
//			$contents .= cls_AdvTag::Create($_nParams);
//			++$advSplit;
//		}
//        
//        if ( isset($format) )
//        {
//            switch ( $format )
//            {
//               case 'script':
//               case 'json':  
//                    if ( false === stripos($mcharset, 'UTF') )
//                    {
//                        $contents = mb_convert_encoding($contents, 'UTF-8', $mcharset);
//                    }
//                    $contents = json_encode($contents);
//                    if ( $contents )
//                    {
//                        $contents = 'var _08adv_data_ = ' . $contents . ';';  
//                    }
//                    else
//                    {
//                    	$contents = 'var _08adv_data_ = {};';
//                    }
//               break;
//            }
//        }
//        
//        exit($contents);
//    }
//    
break;
}


# �����̨����൥������Ĺ���ڵ�չʾ(ajax����)
//function OneBackMenuBlock($UrlsArray = array()){
//	$curuser = cls_UserMain::CurUser();
//	$output = '';
//	if($UrlsArray && $curuser->isadmin()){
//		foreach($UrlsArray as $k => $v){
//			$output .= "['".addslashes($k)."','".addslashes($v)."'],";
//		}
//		$output = "[$output]";
//	}
//	return $output;
//}
