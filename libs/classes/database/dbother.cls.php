<?php
/**
* �й����ݿ⼰��ѯ��ص�һЩ�����㼯
* 
*/
class cls_DbOther{
	
	// һ�����ע��(���ݿ�ʵ���) ����:afields(�ܹ�) > dbfields���� > nowfields > init������ > $excom
	public static function dictComment($tab=''){
		$db = _08_factory::getDBO();
		$tblprefix = cls_env::getBaseIncConfigs('tblprefix');
		$nowfields = $db->getTableColumns($tblprefix.$tab, 0); 
		static $dbfields,$dafields; // ���ʹ�����Ч��? cls_cache::Read�Ѿ�����?!
		if(empty($dbfields)) $dbfields = cls_cache::Read('dbfields');
		if(empty($dafields)){
			$query = $db->query("SELECT * FROM {$tblprefix}afields");
			while($r = $db->fetch_array($query)){
				$dafields[$r['tbl']][$r['ename']] = $r['cname'];
			}
		}
		$excom = array(
			'aid'=>'�ĵ�id','cid'=>'����id','mid'=>'��Աid','mname'=>'��Ա��','chid'=>'�ĵ�ģ��id','mchid'=>'��Աģ��id',
			'createdate'=>'���ʱ��','checked'=>'�Ƿ����','ucid'=>'��������','cuid'=>'������Ŀ',
			'tmoid'=>'�����߻�Աid','tomname'=>'�����߻�Ա��','ip'=>'IP��ַ',
			'tocid'=>'��������ID','reply'=>'�ظ�','replydate'=>'�ظ�ʱ��',
			'pushid'=>'����ID','paid'=>'����λid','color'=>'��ɫ','copyid'=>'����ID','frommid'=>'������Դid',
			'abstract'=>'ժҪ','thumb'=>'����ͼ','content'=>'����','author'=>'����',
			
		);
		if(substr($tab,0,5)=='push_' || substr($tab,0,10)=='farchives_'){
			if(substr($tab,0,5)=='push_'){
				$pfcfg = cls_cache::Read('pafields',$tab);
			}else{
				$pfcfg = cls_FieldConfig::InitialFieldArray('fchannel',str_replace('farchives_','',$tab));	
			}
			foreach($pfcfg as $k1=>$r){
				$k1 = $r['ename'];
				if(isset($nowfields[$k1])){
					$t = &$nowfields[$k1];
					$t->Comment = $r['cname'];
				}
			}
		}
		foreach($nowfields as $k1=>$v){ 
			$k2 = $tab.'_'.$k1;
			$tcom = isset($dafields[$tab][$k1]) ? $dafields[$tab][$k1] : (isset($dbfields[$k2]) ? $dbfields[$k2] : $v->Comment);
			if(empty($tcom)){ //��initȡ����
				if(substr($tab,0,8)=='archives'&&isset($dbfields["init_archives_$k1"])){
					$tcom = $dbfields["init_archives_$k1"];
				}elseif(substr($tab,0,7)=='coclass'&&isset($dbfields["init_coclass_$k1"])){
					$tcom = $dbfields["init_coclass_$k1"];
				}elseif(substr($tab,0,5)=='push_'&&isset($dbfields["init_push_$k1"])){
					$tcom = $dbfields["init_push_$k1"];
				}
			}
			if(empty($tcom) && isset($excom[$k1])){
				$tcom = $excom[$k1];  //��excomȡ����
			}
			if($tcom != $v->Comment){ //$nowfields->$k1['Comment'] = $tcom;
				$t = &$nowfields[$k1];
				$t->Comment = $tcom;
			}
		}
		return $nowfields;
	}
	
	// ��ȡ���ݱ��б�, ����$db->getTableList(), ��ò�������
	public static function tabLists(){
		$db = _08_factory::getDBO();
		$tblprefix = cls_env::getBaseIncConfigs('tblprefix');
		$query = $db->query("SHOW TABLE STATUS"); //SHOW TABLES FROM $dbname
		$arr = array();
		while($r=$db->fetch_array($query)) { 
			$tab = $r['Name'];
			$len = strlen($tblprefix);
			if(substr($tab,0,$len)!==$tblprefix) continue; //��������ϵͳ�ı�,��Ҫ
			$tab = substr($tab,$len);
			$arr[$tab] = $r; // Name,Engine,Rows,Data_length,Collation,Comment,Auto_increment(Max+1) 
		}
		return $arr;
	}

    /**
     * �õ�ָ������ֶ�������
     * 
     * @param string $tbls		���������ƣ�������Զ��ŷָ�
     * @param int $Total		Ϊtrueʱ���������ֶ���������,����ֻ������������
     * @
     * @return array			�������ֶ�����ɵ�����
     * @static
     * @since 1.0
     */ 
	public static function ColumnNames($tbls = '',$Total = false){
		$db = _08_factory::getDBO();
        $tblprefix = cls_envBase::getBaseIncConfigs('tblprefix');
		$rets = array();
		if($tbls && is_array($x = explode(',',$tbls))){
			foreach($x as $v){
				$query = $db->query("SHOW COLUMNS FROM {$tblprefix}$v",'SILENT');
				while($r = $db->fetch_array($query)){
					if($Total){
						$rets[$r['Field']] = $r;
					}else{
						$rets[] = $r['Field'];
					}
				}
			}
			if(empty($Total)) $rets = array_unique($rets);
		}
		return $rets;
	}
	
    /**
     * ͨ�����ݱ�õ����ɻ�������Ҫ��ԭʼ��������
     * 
     * @param array $cachecfg		���ɻ����������ã������(extend_sample)dynamic/syscache/cachedos.cac.php��
     * 
     * @return array				�������ɻ�������Ҫ��ԭʼ��������
     * @static
     * @since 1.0
     */ 
	public static function CacheArray($cachecfg = array()){//$cachecfg = array(tbl,key,fieldstr,where,orderby,unserialize,explode,unset,varexport,merge,)
		$db = _08_factory::getDBO();
        $tblprefix = cls_envBase::getBaseIncConfigs('tblprefix');
		$rets = array();
		if(empty($cachecfg['tbl']) || empty($cachecfg['key'])) return $rets;
		empty($cachecfg['fieldstr']) && $cachecfg['fieldstr'] = '*';
		$sqlstr = "SELECT $cachecfg[fieldstr] FROM {$tblprefix}$cachecfg[tbl]".(empty($cachecfg['where']) ? '' : " WHERE $cachecfg[where]").(empty($cachecfg['orderby']) ? '' : " ORDER BY $cachecfg[orderby]");
		$query = $db->query($sqlstr);
		while($r = $db->fetch_array($query)){
			if(!empty($cachecfg['unserialize']) && is_array($x = array_filter(explode(',',$cachecfg['unserialize'])))){
				foreach($x as $v) cls_CacheFile::ArrayAction($r,$v,'unserialize');
			}
			if(!empty($cachecfg['explode']) && is_array($x = array_filter(explode(',',$cachecfg['explode'])))){
				foreach($x as $v) cls_CacheFile::ArrayAction($r,$v,'explode');
			}
			if(!empty($cachecfg['unset']) && is_array($x = array_filter(explode(',',$cachecfg['unset'])))){
				foreach($x as $v) unset($r[$v]);
			}
			if(!empty($cachecfg['varexport']) && is_array($x = array_filter(explode(',',$cachecfg['varexport'])))){
				foreach($x as $v) cls_CacheFile::ArrayAction($r,$v,'varexport');
			}
			if(!empty($cachecfg['merge']) && is_array($x = array_filter(explode(',',$cachecfg['merge'])))){
				foreach($x as $v) cls_CacheFile::ArrayAction($r,$v,'extract');
			}
			$rets[$r[$cachecfg['key']]] = $r;
		}
		return $rets;
	}
	
    /**
     * �õ���id�ִ��Ĳ�ѯ�ִ�
     * 
     * @param string $ids		���id�Զ��ŷָ����ִ�
     * @param string $idvar		��ѯ���ֶ���(����������)
     * 
     * @return string			SQL�ִ�
     * @static
     * @since 1.0
     */ 
	public static function str_fromids($ids,$idvar){
		if($ids && $idvar && ($ids = array_unique(array_filter(explode(',',$ids))))){
			return " AND $idvar ".multi_str($ids);
		}else return '';
	}

    /**
     * ͳһ�޸��ĵ������ࡢ���͵ĸ��ֱ�(��init��ʼ��)���ݽṹ������ӣ��޸ģ�ɾ���ֶΣ������������ݵȣ�ͨ�����ڿ���������ϵͳ
     * 
     * @param string $sqlstr		SQL��䣬ʹ��{TABLE}��Ϊ����ͨ�������ALTER TABLE {TABLE} DROP xxx
     * @param string $type			�ֱ����ͣ�archive(�ĵ�),push(����),coclass(����)
     * 
     * @static
     */ 
	public static function BatchAlterTable($sqlstr,$type = 'archive'){
		$db = _08_factory::getDBO();
        $tblprefix = cls_envBase::getBaseIncConfigs('tblprefix');
		if(!$sqlstr || !in_str('{TABLE}',$sqlstr)) return;
		switch($type){
			case 'archive':
				$tblarr = array('init_archives');
				$query = $db->query("SELECT stid FROM {$tblprefix}splitbls");
				while($r = $db->fetch_array($query)) $tblarr[] = 'archives'.$r['stid'];
			break;
			case 'push':
				$tblarr = array('init_push');
				$pushareas = cls_PushArea::InitialInfoArray();
				foreach($pushareas as $k => $v){
					$tblarr[] = cls_PushArea::ContentTable($k);
				}
			break;
			case 'coclass':
				$tblarr = array('init_coclass');
				$query = $db->query("SELECT coid FROM {$tblprefix}cotypes");
				while($r = $db->fetch_array($query)) $tblarr[] = 'coclass'.$r['coid'];
			break;
		}
		if(!empty($tblarr)){
			foreach($tblarr as $tbl){
				$db->query(str_replace('{TABLE}',"{$tblprefix}$tbl",$sqlstr),'SILENT');
			} 
		}
	}
	
    /**
	 * ��ȡIN,NOT IN�Ӿ��е�IDs, ���ڹ����б��ʹ��
	 * (ע���Ȳ�ѯ��NOT IN()��IDs, ��ֱ����SELECT�Ӿ䣬ƽ��Ҫ����10������	)
	 * �������ֵ�ID,��aid,cid��, �����������, �����д�����
     * 
     * @param string $key      IDs����Դ�ֶ�,�磺inid
     * @param string $from     FROM�Ӿ�, ����FROM����,�磺{$tblprefix}{$abrel['tbl']}
     * @param string $where    WHERE����, �磺pid='{$this->A['pid']}'
	 * @param string $re       ���ر��: ids-�����ִ�, arr-��������
	 
     * @return int $ids    �磺121,8,5,300�������Ϸ���0����array(121,8,5,300)�������Ϸ���array()��
     */
	public static function SubSql_InIds($key, $from, $where, $re='ids'){
		$db = _08_factory::getDBO();
        $tblprefix = cls_envBase::getBaseIncConfigs('tblprefix');
		$sql = "SELECT DISTINCT $key AS id FROM {$tblprefix}$from".(empty($where) ? "" : " WHERE $where");
		$query = $db->query($sql); $a = array();
		while($r = $db->fetch_array($query)){ 
			$a[] = $r['id'];
		}
		if($re=='ids'){
			return empty($a) ? '0' : implode(',',$a);
		}else{
			return $a;	
		}
	}
	
    /**
     * �ĵ��޶�ͳ�ƣ���Ҫ���ڻ�Ա���� �޶�ͳ�ƣ�Ҳ�����������ط�ͳ���ĵ�
     * 
     * @param int $chid     �ĵ�ģ��,�磺2
     * @param int $field    �ֶ�,�磺refreshdate, Ϊ�ձ�ʾ���ֶ�����
     * @param int $days     ʱ����������,valid-��Ч��,exp-���ڵ�,0-����,����n-(n+1)���ڵ�,[=1]-ֱ�Ӽ�����(��������),
	                        // !!! ���$days���溬��url�ȴ��ݵĲ�ȷ��������������Ԥ�ȴ��� !!! 
     * @param int $mid      ��ԱID,�ձ�ʾ��ǰ��Ա,-1��ʾ�����ֻ�Ա(���л�Ա),
     * 
     * @static
     */
	public static function ArcLimitCount($chid, $field='refreshdate', $days='0', $mid=''){
		global $timestamp;
		$db = _08_factory::getDBO();
        $tblprefix = cls_envBase::getBaseIncConfigs('tblprefix');
		$curuser = cls_UserMain::CurUser();
		$mid = empty($mid) ? $curuser->info['mid'] : $mid;
		$chid = intval($chid);
		$field = cls_string::ParamFormat($field);
		$sql = ""; 
		$sql_ref = ($field=='refreshdate' ? "initdate<>refreshdate" : " "); //ˢ�¶�������
		if($mid==-1) $mid = ''; //-1��ʾ�����ֻ�Ա(����)
		if($days==='valid'){ // ��Ч��
			$sqlt = " AND (enddate=0 OR enddate>'$timestamp') ";
		}elseif($days==='exp'){ // ���ڵ�
			$sqlt = " AND (enddate>0 AND enddate<'$timestamp') ";
		}elseif(empty($days)){ // ��ʾ����, mktime(0,0,0)
			$sqlt = " AND $field>'".(mktime(0,0,0))."' $sql_ref";
		}elseif(intval($days)>0){ // ($days+1)���ڵ�, mktime(0,0,0)
			$sqlt = " AND $field>'".(mktime(0,0,0)-$days*86400)."' $sql_ref";
		}elseif(strstr($days,'=') || strstr($days,'>') || strstr($days,'<')){ // ֱ�Ӽ�����,������������κ�����...
			$sqlt = " AND $field $days ";
		}else{ //
			$sqlt = " ";	
		}
		$sql .= " SELECT COUNT(*) FROM {$tblprefix}".atbl($chid)." ";
		$sql .= " WHERE ".(empty($mid) ? "1=1" : "mid='$mid'")." ";
		$field && $sql .= $sqlt;
		$re = $db->result_one($sql); //echo "$mid,$sql<br>";
		return empty($re) ? 0 : $re;
	}
	
    /**
     * ��ͼ�в������ܱߵĲ�ѯ�Ӵ�
     * 
     * @param int $x,$y     	����Ŀ�������
     * @param int $diff    		ָ����Χ����λΪkm���
     * @param int $mode     	����ģʽ��0��������1��ʵ�ʾ���//???
     * @param string $fname		��ѯ���ֶ��������������ǰ׺��
     * 
     * @return string			SQL�ִ�
     * 
     * @static
     */
	public static function MapSql($x,$y,$diff,$mode,$fname){		
		if(!$diff) return '';
		$mode = empty($mode) ? 0 : 1;
		$x = floatval($x);
		$y = floatval($y);
		$dfx = $dfy = $diff = abs(floatval($diff));
		if($mode == 1){
			$radius = 6378.137;//km
			$dfx = $diff / (2 * $radius * M_PI) * 360;
			$dfy = $diff / (2 * $radius * M_PI * cos(deg2rad($x))) * 360;
		}
        // if($dfx>30 || $dfy>60) return ''; //������̫��,�������ֵ��1/3,����Ϊ����������,������������ (???)
        // γ�� <-90 �� >90 δ����(��Ŀǰ�����ͼ��,һ��Ҳ���ᶨλ����������ĵ�Ϊ�е�)
		$re = $fname.'_0>='.($x - $dfx).' AND '.$fname.'_0<='.($x + $dfx);
        // ����(������߽�)
        $dmin = $y - $dfy; $dmax = $y + $dfy; //*
        if($dmin<-180){ 
            $re .= " AND ( ({$fname}_1>=".($y - $dfy + 360)." AND {$fname}_1<=180) OR ({$fname}_1>=-180 AND {$fname}_1<=".($y + $dfy).") )";
        }elseif($dmax>180){
            $re .= " AND ( ({$fname}_1>=".($y - $dfy)." AND {$fname}_1<=180) OR ({$fname}_1>=-180 AND {$fname}_1<=".($y + $dfy - 360).") )"; 
        }else{
            $re .= " AND {$fname}_1>=".($y - $dfy)." AND {$fname}_1<=".($y + $dfy)."";
        }//*/
		// ���������磺map_0>=-0.5 AND map_0<=1.5 AND map_1>=179.5 AND map_1<=-178.5 Ϊ����
        #$re .= ' AND '.$fname.'_1>='.($y - $dfy < -180 ? $y - $dfy + 360 : $y - $dfy).' AND '.$fname.'_1<='.($y + $dfy > 180 ? $y + $dfy - 360 : $y + $dfy);
		return $re;
	}
		
	public static function DropField($tbl,$ename,$datatype){
		$db = _08_factory::getDBO();
        $tblprefix = cls_envBase::getBaseIncConfigs('tblprefix');
		if(!$tbl || !$ename || !$datatype) return;
		$db->query("ALTER TABLE {$tblprefix}$tbl DROP $ename",'SILENT'); 
		if($datatype == 'map'){
			$db->query("ALTER TABLE {$tblprefix}$tbl DROP {$ename}_0",'SILENT'); 
			$db->query("ALTER TABLE {$tblprefix}$tbl DROP {$ename}_1",'SILENT'); 
		}
		return;
	}
	
	public static function AlterFieldSelectMode($nmode,$omode,$fname,$tbl){//��Ҫ������ϵ�����Ի���Ҫ���Ƕ������ĵ�������в���
		$db = _08_factory::getDBO();
        $tblprefix = cls_envBase::getBaseIncConfigs('tblprefix');
		if(!$fname || !$tbl || $nmode == $omode) return false;
		if($nmode xor $omode){
			$ntbls = m_tblarr($tbl);
			foreach($ntbls as $tbl){
				$omode && $db->query("UPDATE {$tblprefix}$tbl SET $fname= SUBSTRING_INDEX(TRIM(LEADING ',' FROM $fname),',',1) WHERE $fname<>''",'SILENT');
				$db->query("ALTER TABLE {$tblprefix}$tbl CHANGE $fname $fname ".($nmode ? "varchar(255) NOT NULL default ''" : "smallint(6) unsigned NOT NULL default 0"),'SILENT');
				if($nmode){
					$db->query("UPDATE {$tblprefix}$tbl SET $fname= '' WHERE $fname='0'",'SILENT');
					$db->query("UPDATE {$tblprefix}$tbl SET $fname= CONCAT(',',$fname,',') WHERE $fname<>''",'SILENT');
				}
			}
		}
		return true;
	}
	public static function AddField($tbl,$ename,$datatype,$str){
		global $db,$tblprefix;
		$db->query("ALTER TABLE {$tblprefix}$tbl ADD $ename $str",'SILENT');
		if($datatype == 'map'){
			$db->query("ALTER TABLE {$tblprefix}$tbl ADD {$ename}_0 double NOT NULL default '0'",'SILENT');
			$db->query("ALTER TABLE {$tblprefix}$tbl ADD {$ename}_1 double NOT NULL default '0'",'SILENT');
		}
		return;
	}
	
}
