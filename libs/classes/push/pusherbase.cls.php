<?php
/*
** ����ִ���ࣺ�������͹��򣬴���Դ���ݱ�����(����)��Ϣ���������ݱ�
** ���ڵ����ֶεĲ�����δִ��updatedb������updatedb��Ҫ����ִ��
** ע�⣺Ϊ���ڻ�����ʹ����չ�ľ�̬�������ڻ�����ʹ�ã���չ��::method�����ʹ�ã�self::method������֧����չ����
** 
*/ 

class cls_pusherbase{
	protected static $area = array();//����λ����
	protected static $fields = array();//�����ֶ�����
	protected static $updates = array();//���ݱ���µ��ݴ�ֵ����Ծ����ĳ������

	# ��Ҫչʾһ������λʱ�������������е�����չʾ������(��url������url����)
	public static function ViewOneInfo($info = array()){
		if(empty($info)) return $info;
		if(!empty($info['url'])){
			$info['url'] = cls_url::view_url($info['url'],false); # ��ȫurl
		} 
		cls_url::arr_tag2atm($info,'pa');
		return $info;
	}

	# ��ʼ������λ����
	public static function SetArea($paid){
		if(!self::$area || self::$area['paid'] != $paid){
			if(!($pusharea = cls_PushArea::Config($paid))) return false;
			self::$area = $pusharea;
		}
		return true;
	}
	
	//��ָ����Դ��Ϣ���͵�ָ������λ
	//loadtype : 0.�ֶ�����, 11.�ֶ����, 21.�Զ�����
	public static function push($info,$paid,$loadtype=0){
		if(!cls_pusher::SetArea($paid)) return false;
		if(!cls_pusher::_PushCheck($info)) return false;
		if(!cls_pusher::_SetFields() || !self::$fields[$paid]) return false;
		$info = cls_pusher::_DealSourceInfo($info);
		foreach(self::$fields[$paid] as $k => $v){//ˢ�������Ͷ��ֶ����õ�Ҫ��᲻һ�����Ƿ���Ҫ����?????
			if(cls_pusher::_push_field($v,$info)){//��׽��������Ϣ
				cls_pusher::rollback();
				return false;
			}
		}
		cls_pusher::setEnddate($info);
		if($loadtype) cls_pusher::onedbfield('loadtype',$loadtype);
		cls_pusher::updatedb(0,cls_pusher::_GetFromid($info));
		return true;
	}
	
	//��ĳ���Ƽ���Ϣ����Դ����
	public static function Refresh($pushid,$paid){
		if(!cls_pusher::SetArea($paid)) return false;
		if(!($push = cls_pusher::oneinfo($pushid,$paid))) return false;
		if($push['norefresh']) return false;
		if(!cls_pusher::_SetFields() || !self::$fields[$paid]) return false;
		if(!($fromid = (int)$push['fromid']) || !($info = cls_pusher::_OneFromInfo($fromid,$paid))) return false;
		$info = cls_pusher::_DealSourceInfo($info);
		foreach(self::$fields[$paid] as $k => $v){
			if(cls_pusher::_push_field($v,$info,1)) continue;//��׽��������Ϣ
		}
		cls_pusher::setEnddate($info);
		cls_pusher::updatedb($pushid);
		return true;
	}
	
	//ָ��ĳ���Ƽ�λһ��������Դ
	//���ظ�������
	public static function RefreshPaid($paid){
		global $db,$tblprefix,$timestamp;
		if(!cls_pusher::SetArea($paid)) return 0;
		$query = $db->query("SELECT pushid FROM {$tblprefix}".cls_pusher::tbl($paid)." WHERE fromid<>0 AND checked=1 AND startdate<'$timestamp' AND (enddate='0' OR enddate>'$timestamp') AND norefresh=0");
		$i = 0;
		while($r = $db->fetch_array($query)){
			if(cls_pusher::Refresh($r['pushid'],$paid)) $i++;
		}
		return $i;
	}
	
	//���õ�������:
	public static function setEnddate($info){
		$from = empty(self::$area['enddate_from']) ? 0 : self::$area['enddate_from'];
		if(!empty($info[$from]) && is_numeric($info[$from])){ //&& $info[$from]>TIMESTAMP
			cls_pusher::onedbfield('enddate',$info[$from]); 
		}
	}
	
	public static function HaveNewToday($paid){
		global $db,$tblprefix;
		if(!cls_pusher::SetArea($paid)) return false;
		$num = $db->result_one("SELECT COUNT(*) FROM {$tblprefix}".cls_pusher::tbl($paid)." WHERE createdate>'".(mktime(0,0,0))."'",0,'SILENT');
		return $num ? true : false;
	}
	
	public static function AddCopy($pushid,$toclassid,$paid){//��������Ϣ��ָ���ķ��������ӹ�����
		global $db,$tblprefix;
		if(!cls_pusher::SetArea($paid)) return 'δָ������λ';
		if(!($toclassid = empty($toclassid) ? 0 : max(0,intval($toclassid)))) return '��ָ����Ҫ�����ķ���';//ע�⣺�˴�δ�ټ�֤����id
		if(!($copyspace = self::$area['copyspace'])) return 'δָ���������';
		if(!($push = cls_pusher::oneinfo($pushid,$paid))) return '��ָ����ȷ��������Ϣ';
		$copyid = $push['copyid'];
		$classid = 'classid'.self::$area['copyspace'];
		if($db->result_one("SELECT COUNT(*) FROM {$tblprefix}".cls_pusher::tbl($paid)." WHERE copyid='$copyid' AND $classid='$toclassid'")) return '���������е�ǰ���͵Ĺ���';
		$sqlstr = "copyid='$copyid',$classid='$toclassid'";
		foreach($push as $k => $v){
			if(!in_array($k,array('pushid',$classid,'copyid'))) $sqlstr .= ",$k='".addslashes($v)."'";
		}
		$db->query("INSERT INTO {$tblprefix}".cls_pusher::tbl($paid)." SET $sqlstr");
	}
	
	public static function DelCopy($pushid,$toclassid,$paid){//��ĳ������ɾ��
		global $db,$tblprefix;
		if(!cls_pusher::SetArea($paid)) return 'δָ������λ';
		if(!($toclassid = empty($toclassid) ? 0 : max(0,intval($toclassid)))) return '��ָ����Ҫ�����ķ���';//ע�⣺�˴�δ�ټ�֤����id
		if(!($copyspace = self::$area['copyspace'])) return 'δָ���������';
		if(!($push = cls_pusher::oneinfo($pushid,$paid))) return '��ָ����ȷ��������Ϣ';
		$copyid = $push['copyid'];
		$classid = 'classid'.self::$area['copyspace'];
		$db->query("DELETE FROM {$tblprefix}".cls_pusher::tbl($paid)." WHERE copyid='$copyid' AND $classid='$toclassid' AND pushid<>'$pushid'");
	}
	
	public static function paidsarr($type,$typeid = 0,$smallid = 0){//ȡ���Ƽ�λ�б�����
		$pushareas = cls_PushArea::Config();
		$re = array();
		foreach($pushareas as $k =>$v){
			if(($type == $v['sourcetype']) && ($typeid == $v['sourceid'])){
				if($smallid && ($type == 'archives') && $ids = cls_pusher::_AllSmallIds($k)){
					if(!in_array($smallid,$ids)) continue;
				}
				$re[$k] = $v['cname'];
			}
		}
		return $re;
	}
	
	//��Դ�Ƿ���Ҫģ���ֶ�
	public static function SourceNeedAdv($paid){
		if(!cls_pusher::SetArea($paid)) return false;
		return @self::$area['sourceadv'] ? true : false;
	}
	
	//�༭�����ֶ����ݣ��������������Ϣ
	//���Դ������������������$field
	public static function onefield($field = array(),$nvalue = '',$ovalue = ''){
		$c_upload = cls_upload::OneInstance();
		if(!cls_pusher::_FieldCfgOk($field)) return '�����ֶ�'.@$field['cname'];
		$a_field = new cls_field;
		$a_field->init($field,$ovalue);
		$nvalue = $a_field->DealByValue($nvalue,'');//�����������Ϣ
		if($a_field->error) return $a_field->error;//��׽������Ϣ
		unset($a_field);
		if($field['ename'] == 'url'){ # ��url�����url����
			$nvalue = cls_url::save_url($nvalue);
		}
		cls_pusher::onedbfield($field['ename'],$nvalue);
		if($arr = multi_val_arr($nvalue,$field)) foreach($arr as $x => $y) cls_pusher::onedbfield($field['ename'].'_'.$x,$y);
		if($field['ename'] == 'subject'){
			cls_pusher::_SetColor();
		}
		return;
	}
	
	//����һ�����ݱ��ֶ�
	public static function onedbfield($ename,$nvalue,$ovalue = '__new'){//ovalue�����ݿ���ֱ�Ӷ�����δaddslash��ֵ
		if($ovalue == '__new' || $ovalue != stripslashes($nvalue)){//���˵���Ч���ֶ�
			self::$updates[$ename] = $nvalue;
			return true;
		}else return false;
	}
	
	//��ʽ������ֵ
	public static function orderformat($value,$paid,$ename = 'vieworder'){
		if(!cls_pusher::SetArea($paid)) return $value;
		if(!$value) $value = 500;
		$value = min(500,max(1,intval($value)));
		if($value <500){
			$value = min(self::$area['maxorderno'],$value);
		}
		return $value;
	}
	
	public static function updatedb($pushid = 0,$fromid = 0){//��Ե�����Ϣ�Ĳ���
		//pushid����ǰ������Ϣid��0Ϊ�ֶ���ӻ���������
		//fromid��������Դ��Ϣ��id����������ʱ��Ҫ����
		global $db,$tblprefix,$timestamp;
		if(!self::$area) return false;//δ��ʼ������λ
		$curuser = cls_UserMain::CurUser();
		$sqlstr = '';foreach(self::$updates as $k => $v) $sqlstr .= ($sqlstr ? "," : "").$k."='".$v."'";
		if($sqlstr && $paid = @self::$area['paid']){
			if($pushid){//�����������ͼ�¼
				$db->query("UPDATE {$tblprefix}".cls_pusher::tbl($paid)." SET $sqlstr WHERE pushid='$pushid'");
				cls_pusher::_updatecopy($pushid,self::$updates);
			}else{
				$sqlstr .= ",paid='$paid',fromid='$fromid',mid='{$curuser->info['mid']}',mname='{$curuser->info['mname']}',createdate='$timestamp',checked=1";
				$db->query("INSERT INTO {$tblprefix}".cls_pusher::tbl($paid)." SET $sqlstr");
				if($pushid = $db->insert_id()){
					$db->query("UPDATE {$tblprefix}".cls_pusher::tbl($paid)." SET copyid='$pushid' WHERE pushid='$pushid'");
					if($curuser->pmautocheck(self::$area['autocheck'])) cls_pusher::_OrderFirst($pushid,$paid); # ��������Ϣ�Զ��ö�
				}
			}
		}
		self::$updates = array();
		return $pushid;
	}
	public static function rollback(){
		self::$updates = array();
	}
	
	/*��������λ��Ŀ�����
	*@param $classid  ��Ŀ�������IDֵ
	*       $key    ��Ŀ����ϵ�ֶ���
 	*       $paid   ����λid
	*/
	public static function setclassid($pushid,$classid,$key,$paid){
		if(!cls_pusher::SetArea($paid)) return false;
		if(!cls_pusher::_SetFields() || !self::$fields[$paid]) return false;
		if(isset(self::$fields[$paid][$key])){
			if($re = cls_pusher::onefield(self::$fields[$paid][$key],isset($classid) ? $classid : '')){//��׽������Ϣ
				cls_pusher::rollback();
				return $re;
			}
		}
	}
	
	//ָ��ĳ���Ƽ�λ��������
	public static function ORefreshPaid($paid){
		if(!cls_pusher::SetArea($paid)) return false;
		$orderspace = self::$area['orderspace'];
		switch($orderspace){
			case 0:
				cls_pusher::ORefresh($paid);
			break;
			case 1:
				if($arr = cls_pusher::_fetch_classids(1,$paid)){
					foreach($arr as $k) cls_pusher::ORefresh($paid,$k,0);
				}
			break;
			case 2:
				if($arr = cls_pusher::_fetch_classids(2,$paid)){
					foreach($arr as $k) cls_pusher::ORefresh($paid,0,$k);
				}
			break;
			case 3:
				$arr1 = cls_pusher::_fetch_classids(1,$paid);
				$arr2 = cls_pusher::_fetch_classids(2,$paid);
				foreach($arr1 as $k1){
					foreach($arr2 as $k2){
						cls_pusher::ORefresh($paid,$k1,$k2);
					}
				}
			break;
		}
	}
	
	# ��ĳ����Ϣ�ŵ���ǰ����ռ����λ(ͬʱ��Ҫ�������ǹ�λ��Ϣ����һλ)
	protected static function _OrderFirst($pushid = 0,$paid = 0){
		global $db,$tblprefix,$timestamp;
		if(!cls_pusher::SetArea($paid)) return false;
		$orderspace = self::$area['orderspace'];
		$maxorderno = (int)self::$area['maxorderno'] ? (int)self::$area['maxorderno'] : 10;
		$maxorderno = min(50,$maxorderno);
		
		if(!($push = cls_pusher::oneinfo($pushid,$paid))) return false;
		if(!$push['checked']) return false;
		if($push['startdate'] > $timestamp) return false;
		if($push['enddate'] && $push['enddate'] < $timestamp) return false;
		# ��������ռ�
		$spacestr = '';
		switch($orderspace){
			case 1:
				$spacestr .= " AND classid1='".(int)$push['classid1']."'";
			break;
			case 2:
				$spacestr .= " AND classid2='".(int)$push['classid2']."'";
			break;
			case 3:
				$spacestr .= " AND classid1='".(int)$push['classid1']."'";
				$spacestr .= " AND classid2='".(int)$push['classid2']."'";
			break;
		}
		
		# ����ǰ��Ϣ�ŵ��ǹ�λ�ĵ�һλ
		$_wherestr = "WHERE fixedorder=500 AND vieworder<500 $spacestr 
		AND pushid<>'$pushid' 
		AND checked=1 
		AND startdate<'$timestamp' 
		AND (enddate='0' OR enddate>'$timestamp') 
		ORDER BY trueorder,pushid DESC";
		$NowFirstNo = (int)$db->result_one("SELECT trueorder FROM {$tblprefix}".cls_pusher::tbl($paid)." $_wherestr LIMIT 0,1");
		if(empty($NowFirstNo)) $NowFirstNo = 1;
		$db->query("UPDATE {$tblprefix}".cls_pusher::tbl($paid)." SET trueorder=$NowFirstNo,vieworder=$NowFirstNo WHERE pushid='$pushid'");
		# ����ǰ����ռ��ڵ���Ч������Ϣס��Ųһλ
		$db->query("UPDATE {$tblprefix}".cls_pusher::tbl($paid)." SET trueorder=trueorder+1,vieworder=vieworder+1 $_wherestr LIMIT $maxorderno");
		return true;
	}
	
	
	//����ض�������ռ����Ϣ��������
	public static function ORefresh($paid,$classid1 = 0,$classid2 = 0){
		global $db,$tblprefix,$timestamp;
		if(!cls_pusher::SetArea($paid)) return false;
		$orderspace = self::$area['orderspace'];
		$maxorderno = (int)self::$area['maxorderno'] ? (int)self::$area['maxorderno'] : 10;
		$maxorderno = min(50,$maxorderno);
		switch($orderspace){
			case 0:
				if($classid1 || $classid2) return false;
			break;
			case 1:
				if(!$classid1 || $classid2) return false;
			break;
			case 2:
				if($classid1 || !$classid2) return false;
			break;
			case 3:
				if(!$classid1 || !$classid2) return false;
			break;
		}
		$spacestr = '';
		$classid1 && $spacestr .= " AND classid1='$classid1'";
		$classid2 && $spacestr .= " AND classid2='$classid2'";
		
		$va = $fa = array();$i = 0;
		$sqlstr = "SELECT pushid,vieworder,fixedorder FROM {$tblprefix}".cls_pusher::tbl($paid)." WHERE checked=1 AND startdate<'$timestamp' AND (enddate='0' OR enddate>'$timestamp')";
		$spacestr && $sqlstr .= $spacestr;
		$sqlstr .= " ORDER BY fixedorder,vieworder,pushid DESC";
		$sqlstr .= " LIMIT 0,$maxorderno";
		$query = $db->query($sqlstr);
		while($r = $db->fetch_array($query)){
			if($r['fixedorder'] <> 500){
				$fa[$r['pushid']] = (int)$r['fixedorder'];
			}else{
				$va[$r['pushid']] = ++$i;
			}
		}
		$va = CombineOrderArray($va,$fa);
		$str = '';
		foreach($va as $k => $v){
			$str .= ",($k,$v)";
		}
		if($str = substr($str,1)){
			$db->query("UPDATE {$tblprefix}".cls_pusher::tbl($paid)." SET trueorder=500".($spacestr ? ' WHERE '.substr($spacestr,5) : ''));
			$db->query("INSERT INTO {$tblprefix}".cls_pusher::tbl($paid)." (pushid,trueorder) VALUES $str ON DUPLICATE KEY UPDATE trueorder = VALUES(trueorder)");
			$db->query("UPDATE {$tblprefix}".cls_pusher::tbl($paid)." SET vieworder=trueorder WHERE pushid IN (".implode(',',array_keys($va)).")");
		}
		return true;
	}
	
	public static function delete($pushid,$paid){//ɾ��ĳ���Ƽ���Ϣ
		global $db,$tblprefix;
		if(!cls_pusher::SetArea($paid)) return false;
		if(!($push = cls_pusher::oneinfo($pushid,self::$area['paid']))) return false;
		$ids = array($pushid);
		if($copyinfos = cls_pusher::copyinfos($push,self::$area['paid'])){//ͬʱɾ����������������������
			foreach($copyinfos as $k => $v) $ids[] = $k;
		}
		$db->query("DELETE FROM {$tblprefix}".cls_pusher::tbl($paid)." WHERE pushid ".multi_str($ids));
		return true;
	}
	
	//���ĳ���Ƽ���Ϣ
	public static function check($info){
		return cls_pusher::onedbfield('checked',1,@$info['checked']);
	}
	
	//����ĳ���Ƽ���Ϣ
	public static function uncheck($info){
		return cls_pusher::onedbfield('checked',0,@$info['checked']);
	}
	
	public static function oneinfo($pushid,$paid,$isView = false){//��ȡĳ���Ƽ���Ϣ
		global $db,$tblprefix;
		$pushid = empty($pushid) ? 0 : max(0,intval($pushid));
		if(!cls_pusher::SetArea($paid) || !$pushid) return false;
		$re = $db->fetch_one("SELECT * FROM {$tblprefix}".cls_pusher::tbl($paid)." WHERE pushid='$pushid'");
		if($isView) $re = cls_pusher::ViewOneInfo($re);
		return $re;
	}
	public static function copyinfos($info = array(),$paid = 0){//��ȡĳ���Ƽ���Ϣ�����и���
		global $db,$tblprefix;
		if(!$paid || !cls_pusher::SetArea($paid) || !$info) return false;
		if(!($copyid = $info['copyid']) || !($pushid = $info['pushid'])) return false;
		$re = array();
		$query = $db->query("SELECT * FROM {$tblprefix}".cls_pusher::tbl($paid)." WHERE copyid='$copyid' AND pushid<>'$pushid' ORDER BY pushid");
		while($r = $db->fetch_array($query)) $re[$r['pushid']] = $r;
		return $re;
	}
	public static function copynum($info = array(),$paid = 0){//��ȡĳ���Ƽ���Ϣ�ĸ�������
		global $db,$tblprefix;
		if(!$paid || !cls_pusher::SetArea($paid) || !$info) return false;
		if(!($copyid = $info['copyid']) || !($pushid = $info['pushid'])) return false;
		$re = $db->result_one("SELECT COUNT(*) FROM {$tblprefix}".cls_pusher::tbl($paid)." WHERE copyid='$copyid' AND pushid<>'$pushid'");
		return $re ? $re : 0;
	}
	
	public static function tbl($paid){//��ȡĳ���Ƽ�λ����������
		return cls_PushArea::ContentTable($paid);
	}
	
	public static function AllTitle($paid,$noid = 0,$admin_url = 0){//��ȡĳ���Ƽ�λ����������
		if(!cls_pusher::SetArea($paid)) return false;
		$pushtypes = cls_cache::Read('pushtypes');
		$re = $pushtypes[self::$area['ptid']]['title'].'>';
		$re .= $admin_url ? "<a href='?entry=extend&extend=pushs&paid=$paid' onclick=\"return floatwin('open_arcdetail',this)\" title='�����������λ'>".self::$area['cname']."</a>" : self::$area['cname'];
		if(!$noid) $re .= "($paid)";
		return $re;
	}
	
	//������Ϣ�ĳ�ʼ������
	//�����ﲻ�ų��Ѽ��ص�id
	public static function InitWhere($paid,$pre = ''){
		if(!cls_pusher::SetArea($paid)) return false;
		$re = '';
		switch(self::$area['sourcetype']){
			case 'archives':
				$re .= "{$pre}checked=1";//����
				if(!empty(self::$area['smallids'])){  
					if($ids = cls_pusher::_AllSmallIds(self::$area['paid'])){
						$re .= " AND {$pre}caid IN (".implode(',',$ids).")";//����
					}
				}
			break;
			case 'members':
				$re .= "{$pre}checked=1";//����
			break;
			case 'catalogs':
				$re .= "{$pre}closed=0";//�ر�
			break;
			case 'commus':
				$re .= "{$pre}checked=1";//����
			break;
			default:return false;
		}
		if($Sql = cls_pusher::AddSourceSql($paid,$pre)){
			$re .= ' AND '.$Sql;
		}
		return $re;
	}
	
	# ���ӵ��Զ���Sql
	public static function AddSourceSql($paid,$pre = ''){
		$re = '';
		if(cls_pusher::SetArea($paid)){
			if($sql = @self::$area['sourcesql']){//������sql
				if(preg_match("/^return\b/i",$sql)) $sql = @eval($sql);//ͨ����������
				if($sql = key_replace($sql,array('pre' => $pre,'timestamp' => cls_env::GetG('timestamp')))){
					$re = $sql;
				}
			}
		}
		return $re;
	}
	
	
	public static function InitFrom($paid,$pre = ''){
		global $tblprefix;
		if(!cls_pusher::SetArea($paid)) return false;
		$re = '';
		switch(self::$area['sourcetype']){
			case 'archives':
				if(!($tbl = atbl(self::$area['sourceid']))) return false;
				$re .= cls_env::GetG('tblprefix').$tbl;
			break;
			case 'members':
				$re .= cls_env::GetG('tblprefix')."members";
			break;
			case 'catalogs':
				$re .= cls_env::GetG('tblprefix').cls_catalog::Table(self::$area['sourceid'],true);
			break;
			case 'commus':
				$re .= cls_env::GetG('tblprefix').cls_commu::ContentTable(self::$area['sourceid']);
			break;
			default:return false;
		}
		if($re && $pre){
			$re .= ' '.substr($pre,0,-1);
		}
		return $re;
	}
	protected static function _InSourceSql($info){//������Դ�Ƿ����
		global $db,$tblprefix;
		if(!($paid = self::$area['paid'])) return false;
		if(!($SourceSql = cls_pusher::AddSourceSql($paid))) return true;
		if(!($FromTable = cls_pusher::InitFrom($paid))) return false;
		if(!($IDKey = cls_pusher::_IDKey($paid))) return false;
		if(!($Fromid = cls_pusher::_GetFromid($info))) return false;
		$re = $db->result_one("SELECT COUNT(*) FROM $FromTable WHERE $IDKey='$Fromid' AND $SourceSql");
		return $re ? true : false;
	}
	
	//ɾ����Դid��ͬʱɾ����ص�������Ϣ
	//$sourcetype : archives �� members �� commus �� catalogs �� 
	//$sourceid : �ֱ��Ӧ�ĵ�ģ��id, ��Աģ��id, ����ģ��id, ��ϵ��Ŀid����Ŀ(0); ��������Ŀһ��Ҫ���˲���
	public static function DelelteByFromid($fromid=0, $sourcetype='archives', $sourceid=-1){
		global $db,$tblprefix;
		if(!($fromid = max(0,intval($fromid)))) return;
		$sourceid = is_numeric($sourceid) ? $sourceid : -1;
		$sourceid = max(-1,intval($sourceid));
		$pushareas = cls_PushArea::Config();
		foreach($pushareas as $k => $v){
			if($sourcetype == $v['sourcetype']){ 
				if(in_array($v['sourcetype'],array('commus','catalogs')) && intval($v['sourceid'])!=$sourceid ) continue; //��������Ŀһ��Ҫ����sourceid���
				if(in_array($v['sourcetype'],array('archives','members')) && $sourceid>0 && intval($v['sourceid'])!=$sourceid ) continue; //���Բ�Ҫ����,������sourceid�����Ч��
				$db->query("DELETE FROM {$tblprefix}".cls_pusher::tbl($k)." WHERE fromid='$fromid'", 'SILENT');
			}	
		}
	}
	
	# ֻ���������͵ļ�飬�������ݲ���Ҫ�˼��
	protected static function _PushCheck($info){//���ͼ��
		global $timestamp;
		if(!self::$area) return false;//δ��ʼ������λ
		if(!cls_pusher::_GetFromid($info)) return false;//��Դ�����ڣ������Ͳ���Ӧ
		switch(self::$area['sourcetype']){
			case 'archives':
				if(self::$area['sourceid'] != $info['chid']) return false;//����id��Ҫ��Ӧ
				if(!$info['checked']) return false;//δ���
				$ids = cls_pusher::_AllSmallIds(self::$area['paid']);
				if($ids && !in_array($info['caid'],$ids)) return false;
				#if($info['enddate'] && $info['enddate'] < $timestamp) return false;//���ڵ�
			break;
			case 'members':
				if(self::$area['sourceid'] != $info['mchid']) return false;//����id��Ҫ��Ӧ
				if(!$info['checked']) return false;//δ���
			break;
			case 'catalogs':
				if(!empty($info['closed'])) return false;//�رյ�
			break;
			case 'commus':
			break;
			default:return false;
		}
		if(!cls_pusher::_InSourceSql($info)) return false;//�����ڸ���SQL��Χ�ڵ�
		if(cls_pusher::_SourceExist($info)) return false;//�Ѿ����͹��ģ���Ϊ�漰��ѯ���������ź�
		//������sql���ƵĴ���
		return true;
	}
	
	
	
	//��������е�ָ����Ŀ������Ŀ
	protected static function _AllSmallIds($paid){//���ͼ��
		if(!cls_pusher::SetArea($paid) || !self::$area['smallids']) return array();
		$smallids = array_filter(explode(',',self::$area['smallids']));
		if(!self::$area['smallson'] || self::$area['sourcetype'] != 'archives'){
			return $smallids;
		}else{
			$re = array();
			foreach($smallids as $id) $re = array_merge($re,sonbycoid($id,0,1));
			return $re;
		}
	}
	
	//ͨ��fromid�õ�һ����Դ���ݵ���Ϣ
	protected static function _OneFromInfo($fromid,$paid){//���ͼ��
		global $db,$tblprefix;
		if(!$paid || !cls_pusher::SetArea($paid) || !$fromid) return false;
		if(!($IDKey = cls_pusher::_IDKey($paid))) return false;
		switch(self::$area['sourcetype']){
			case 'archives':
				if(!($ntbl = atbl(self::$area['sourceid']))) return false;
				$sqlstr = "SELECT * FROM {$tblprefix}$ntbl a";
				if(cls_pusher::SourceNeedAdv($paid)) $sqlstr .= " INNER JOIN {$tblprefix}archives_".self::$area['sourceid']." c ON a.$IDKey=c.$IDKey";
				$sqlstr .= " WHERE a.$IDKey='$fromid'";
			break;
			case 'members':
				$sqlstr = "SELECT * FROM {$tblprefix}members m INNER JOIN {$tblprefix}members_sub s ON s.$IDKey=m.$IDKey";
				if(cls_pusher::SourceNeedAdv($paid)) $sqlstr .= " INNER JOIN {$tblprefix}members_".self::$area['sourceid']." c ON c.$IDKey=m.$IDKey";
				$sqlstr .= " WHERE m.$IDKey='$fromid'";
			break;
			case 'catalogs':
				if(!($ntbl = cls_catalog::Table(self::$area['sourceid'],true))) return false;
				$sqlstr = "SELECT * FROM {$tblprefix}$ntbl WHERE $IDKey='$fromid'";
			break;
			case 'commus':
				if(!($ntbl = cls_commu::ContentTable(self::$area['sourceid']))) return false;
				$sqlstr = "SELECT * FROM {$tblprefix}$ntbl WHERE $IDKey='$fromid'";
			break;
			default:return false;
		}
		$re = $db->fetch_one($sqlstr);
		return $re ? $re : false;
	}
	
	# ��Դ��Ϣ�е�id��key
	protected static function _IDKey($paid){
		if(!$paid || !cls_pusher::SetArea($paid)) return '';
		$KeyArray = array(
			'archives' => 'aid',
			'members' => 'mid',
			'catalogs' => cls_catalog::Key(self::$area['sourceid']),
			'commus' => 'cid',
		);
		return isset($KeyArray[self::$area['sourcetype']]) ? $KeyArray[self::$area['sourcetype']] : '';
	}
	
	//�ӹ���Դ����
	protected static function _DealSourceInfo($info){
		if(!self::$area) return $info;//δ��ʼ������λ
		switch(self::$area['sourcetype']){
			case 'archives':
				if(!isset($info['arcurl'])){
					if(!empty(self::$area['sourcefields']['url']['nodemode'])){
						$info['nodemode'] = 1;//�����ֻ�����
					}
					cls_ArcMain::Url($info,-1);
				}
			
			break;
			case 'members':
				if(!isset($info['mspacehome'])) $info['mspacehome'] = cls_Mspace::IndexUrl($info);
			break;
			case 'catalogs':
			break;
			case 'commus':
			break;
			default:return false;
		}
		return maddslashes($info,1);
	}
	protected static function _SetFields(){//���õ�ǰ�������ֶ�����
		if(self::$area){
			isset(self::$fields[self::$area['paid']]) or self::$fields[self::$area['paid']]=array();
			if(!self::$fields[self::$area['paid']]){
				if(!self::$fields[self::$area['paid']] = cls_PushArea::Field(self::$area['paid'])) return false;
			}
		}else return false;
		return true;
	}
	
	protected static function _OneSourceValue($rule,$info = array()){//ȡ��һ���ֶε���Դֵ
		if(!$rule || !$info) return false;
		$re = key_replace($rule,$info); # ����ǰ���ϴ���{xxxx}ռλ��
		if(preg_match("/^return\b/i",$re)){//ͨ����������
			$re = @eval($re);
		}elseif(preg_match("/\[cnode::(.+?)\]/i",$re)){//��Ŀ�ڵ�
			$re = preg_replace("/\[cnode::(.+?)::(\d+)::(\d)\]/ies","cls_cnode::url('\\1','\\2','\\3')",$re);
		}elseif(preg_match("/\[mcnode::(.+?)\]/i",$re)){//��ԱƵ���ڵ�
			$re = preg_replace("/\[mcnode::(.+?)::(\d+)\]/ies","cls_mcnode::url('\\1','\\2')",$re);
		}
		return $re;
	}
	
	//����һ���ֶε����ͣ����س�����Ϣ
	//�����ˢ�£���Ҫ�ų�������ϵ�ĸ���
	protected static function _push_field($field = array(),$info = array(),$isrefresh = 0){
		if(!cls_pusher::_FieldCfgOk($field)) return '�����ֶΣ�'.@$field['cname'];
		if($isrefresh){
			if(empty(self::$area['sourcefields'][$field['ename']]['refresh'])) return @$field['cname'].'������Ҫ����Դ����';
			if(!empty(self::$area['copyspace']) && $field['ename'] == 'classid'.self::$area['copyspace']){//����ʱ��Ҫ���¹�����ϵ
				return @$field['cname'].'��������಻����Դ����';
			}
		}
		if(($from = @self::$area['sourcefields'][$field['ename']]['from']) && ($value = cls_pusher::_OneSourceValue($from,$info))){
			return cls_pusher::onefield($field,$value);
		}elseif($field['notnull']) return $field['cname'].'������Ϊ��';//�����ֶ�δ�����ֶ���Դ���򣬻���Դ�������ô��󣬷���false;
		return;
	}
	
	
	//У��һ���ֶ������Ƿ�Ϸ�
	protected static function _FieldCfgOk($field = array()){
		if(!($paid = self::$area['paid'])) return false;
		if(!$field || @$field['type'] != 'pa' || @$field['tpid'] != $paid) return false;
		return true;
	}
	
	//���ñ�����ɫ//������װ�����⣬�����Ľ�?????????
	protected static function _SetColor(){
		global $color;
		if($color){
			cls_pusher::onedbfield('color',$color == '#' ? '' : $color);
		}
	}
	
	protected static function _SourceExist($info){//������Դ�Ƿ����
		global $db,$tblprefix;
		if(!($paid = self::$area['paid'])) return true;//???�����Ѵ���
		$fromid = cls_pusher::_GetFromid($info);
		$re = $db->result_one("SELECT COUNT(*) FROM {$tblprefix}".cls_pusher::tbl($paid)." WHERE fromid='$fromid'");
		return $re ? true : false;
	}
	
	//��һ����Դ�еõ�fromid
	protected static function _GetFromid($info){
		if(!empty(self::$area)){
			if(!($IDKey = cls_pusher::_IDKey(self::$area['paid']))) return 0;
			$fromid = @$info[$IDKey];
		}
		return empty($fromid) ? 0 : $fromid;
	}
	
	//�����pushid�ĸ���ͬʱ���µ�����
	protected static function _updatecopy($pushid = 0,$updates = array()){
		global $db,$tblprefix;
		if(!self::$area) return false;//δ��ʼ������λ
		if(empty(self::$area['copyspace'])) return false;
		if(empty($updates)) return false;
		if(!($push = cls_pusher::oneinfo($pushid,self::$area['paid']))) return false;
		if(!($copyinfos = cls_pusher::copyinfos($push,self::$area['paid']))) return false;
		$classid = 'classid'.self::$area['copyspace'];
		$paid = self::$area['paid'];
		
		$sqlstr = '';
		foreach($updates as $k => $v){
			if(!in_array($k,array($classid))){
				 $sqlstr .= ",$k='$v'";
			}
		}
		if($sqlstr = substr($sqlstr,1)){
			if($ids = array_keys($copyinfos)){
				$db->query("UPDATE {$tblprefix}".cls_pusher::tbl($paid)." SET $sqlstr WHERE pushid ".multi_str($ids));
			}
		}
		return true;
	}
	
	//��ȡ�Ƽ���Ϣ��ĳ����ϵ�б�ʹ�ù��ķ���id
	protected static function _fetch_classids($coid = 1,$paid = 0){
		global $db,$tblprefix,$timestamp;
		if(!in_array($coid,array(1,2))) return false;
		if(!cls_pusher::SetArea($paid)) return false;
		$re = array();
		$query = $db->query("SELECT DISTINCT(classid$coid) FROM {$tblprefix}".cls_pusher::tbl($paid)." WHERE checked=1 AND startdate<'$timestamp' AND (enddate='0' OR enddate>'$timestamp')");
		while($r = $db->fetch_array($query)){
			$re[] = $r["classid$coid"];
		}
		return $re;
	}
	
	
}
