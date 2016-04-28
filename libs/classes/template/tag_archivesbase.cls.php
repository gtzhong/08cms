<?PHP
/**
* [�ĵ��б�/�ĵ�����ͳ��] ��ǩ�����࣬�̳�cls_TagParse
* ���������׳�����ʱ�޷�չʾ���⣬�д���������???
*/



defined('M_COM') || exit('No Permission');
abstract class cls_Tag_ArchivesBase extends cls_TagParse{
	
	# �������ݽ��
	protected function TagReSult(){
		if($this->tag['tclass'] == 'archives'){
			return $this->TagResultBySql();
		}else{
			$func = 'TagReSult_'.$this->tag['tclass'];
			return $this->$func();
		}
	}
	
	# ���ؽ���еĵ�����¼�Ĵ���
	protected function TagOneRecord($OneRecord){
		$OneRecord['nodemode'] = defined('IN_MOBILE');//�����ֻ����־?????????????????
		cls_ArcMain::Parse($OneRecord,TRUE);
		return $OneRecord;
	}
		
	# �ĵ�����ͳ��(acount)��ǩ�����ݷ���
	protected function TagReSult_acount(){
		$ReturnArray = array('counts' => 0);
		if($sqlstr = $this->TagSqlStr(true)){
			$ReturnArray['counts'] = self::$db->result_one($sqlstr,intval(@$this->tag['ttl']));
		}
		return $ReturnArray;
	}
	
	# ȡ��Ĭ�ϵ������ִ�
	protected function TagDefaultOrderStr(){
		return ' ORDER BY a.aid DESC';
	}
	
	
	# ���ݱ�ǩ����ƴ��sqlstr���õ�SQL����Ҫ����(select��from��where)
	protected function CreateTagSqlBaseStr(){
		
		$sqlselect = "SELECT a.*";
		$sqlwhere = $this->TagHandWherestr();
		$sqlwhere = $sqlwhere ? " AND $sqlwhere" : '';
		
		# �����ĵ�ģ��ID������ѡ��chsource
		$NowChid = 0; # ��ǰָ�����ĵ�ģ��id�������Ƕ��ID�ִ�(,�ָ�)
		$SingleChid = 0; # �Ƿ�ָ���˵���chid�����chidָ���˶��ID���˱�����Ϊ0
		if(!empty($this->tag['chsource'])){ 
			if($this->tag['chsource'] == 1){ # �����ĵ�ģ��ID(ֻ�����ID)
				if(!($NowChid = (int)cls_Parse::Get('_a.chid'))) $this->TagThrowException("�޷�ȡ�ü�����ĵ�ģ��id");
				$sqlwhere .= " AND a.chid='$NowChid'";
				$SingleChid = $NowChid;
			}elseif($this->tag['chsource'] == 2){ # �ֶ�ָ���ĵ�ģ��ID
				if(empty($this->tag['chids'])){
					$this->TagThrowException("��Ҫ�ֶ�ָ��chids");	
				}else{
					$NowChidArray = explode(',',$this->tag['chids']);
					$sqlwhere .= " AND a.chid ".multi_str($NowChidArray);
					$NowChid = $NowChidArray[0];
					if(count($NowChidArray) == 1) $SingleChid = $NowChid;
				}
			}
		}
		if(empty($NowChid)) $this->TagThrowException("��Ҫָ���ĵ�ģ��id");
		
		if(!($ntbl = atbl($NowChid))) $this->TagThrowException("��ָ����ȷ���ĵ�ģ��id");
		$NowStid = cls_channel::Config($NowChid,'stid');
		$sqlfrom = " FROM ".self::$tblprefix."$ntbl a".$this->ForceIndexSql('a');

		if(!empty($this->tag['mode'])){
			$this->tag['id'] = empty($this->tag['id']) ? (int)cls_Parse::Get('_a.aid') : (int)$this->tag['id'];
			if(empty($this->tag['id'])) $this->TagThrowException("δָ�����id");
			if($this->tag['mode'] == 'in'){
				if(!$abrel = cls_cache::Read('abrel',@$this->tag['arid'])) $this->TagThrowException("δָ����Ч�ĺϼ���Ŀ");
				if($abrel['tbl']){
					$sqlfrom = " FROM ".self::$tblprefix."$abrel[tbl] b".$this->ForceIndexSql('b')." INNER JOIN ".self::$tblprefix."$ntbl a".$this->ForceIndexSql('a')." ON a.aid=b.inid";
					$sqlselect .= ",b.*";
					$sqlwhere .= " AND b.pid='".$this->tag['id']."' AND b.arid='".$this->tag['arid']."'";
				}else $sqlwhere .= " AND a.pid".$this->tag['arid']."='".$this->tag['id']."'";
			}elseif($this->tag['mode'] == 'belong'){
				if(!$abrel = cls_cache::Read('abrel',@$this->tag['arid']))  $this->TagThrowException("δָ����Ч�ĺϼ���Ŀ");
				if($abrel['tbl']){
					$sqlfrom = " FROM ".self::$tblprefix."$abrel[tbl] b".$this->ForceIndexSql('b')." INNER JOIN ".self::$tblprefix."$ntbl a".$this->ForceIndexSql('a')." ON a.aid=b.pid";
					$sqlselect .= ",b.*";
					$sqlwhere .= " AND b.inid='".$this->tag['id']."' AND b.arid='".$this->tag['arid']."'";
				}else{
					if(!($_ntbl = atbl($this->tag['id'],2)) || !($pid = self::$db->result_one("SELECT pid".$this->tag['arid']." FROM ".self::$tblprefix."$_ntbl WHERE aid='".$this->tag['id']."'"))){
						$this->TagThrowException("δ�ҵ��ϼ�id");
					}
					$sqlwhere .= " AND a.aid='$pid'";
				}
			}elseif($this->tag['mode'] == 'relate'){
				if(!($_ntbl = atbl($this->tag['id'],2)) || !($r = self::$db->fetch_one("SELECT keywords,relatedaid FROM ".self::$tblprefix."$_ntbl WHERE aid='".$this->tag['id']."'"))){
					$this->TagThrowException("δ�ҵ��ϼ�id");//????					
				}
				if(!empty($r['relatedaid'])){
					if(!($arr = array_unique(explode(',',$r['relatedaid'])))){
						$this->TagThrowException("δ���ù�����aid");
					}
					$sqlwhere .= " AND a.aid ".multi_str($arr);
				}elseif(!empty($r['keywords'])){
					$arr = array_unique(explode(',',$r['keywords']));
					$i = 0;
					$keywordstr = '';
					foreach($arr as $str){
						$keywordstr .= ($keywordstr ? ' OR ' : '')."a.keywords LIKE '%".addcslashes($str,'%_')."%'";
						$i ++;
						if($i > 5) break;
					}
					if(!$keywordstr){
						$this->TagThrowException("δ�ҵ���صĹؼ���");
					}
					$sqlwhere .= " AND a.aid!='".(int)cls_Parse::Get('_a.aid')."' AND ($keywordstr)";
				}else  $this->TagThrowException("��������ô���");
			}
		}
		
		# ������Ŀɸѡ
		if(!empty($this->tag['casource'])){
			$caidArray = array();
			if($this->tag['casource'] == '1'){//�ֶ�ָ��caid�����caidsָ��Ϊ�գ���SQL����
				$caidArray = array_filter(explode(',',@$this->tag['caids']));
				if(empty($caidArray)) $this->TagThrowException("��Ҫ����caids");
			}elseif($this->tag['casource'] == '2'){ # ʹ�ü���caid�����δ�ҿ������������Ӱ��sqlwhere
				if($NowCaid = (int)cls_Parse::Get('_a.caid')) $caidArray[] = $NowCaid;
			}
			if($caidArray && !empty($this->tag['caidson'])){
				$_sons = array();
				foreach($caidArray as $k) $_sons = array_merge($_sons,sonbycoid($k,0));
				$caidArray = array_unique($_sons);
				unset($_sons);
			}
			$caidArray && $sqlwhere .= " AND a.caid ".multi_str($caidArray);
		}
		
		# ������Ч��ϵ�ķ���ɸѡ
		$cotypes = cls_cache::Read('cotypes');
		$splitbls = cls_cache::Read('splitbls');
		foreach($cotypes as $k => $v){
			if(in_array($k,$splitbls[$NowStid]['coids'])){//�ų�ʹ����Ч��ϵ���sql
				$ccidArray = array();
				if(!empty($this->tag['cosource'.$k])){
					if($this->tag['cosource'.$k] == '1'){ # �ֶ�ָ�������ccids$kָ��Ϊ�գ���SQL����
						$ccidArray = array_filter(explode(',',@$this->tag['ccids'.$k]));
						if(empty($ccidArray)) $this->TagThrowException("��Ҫ����ccids$k");
					}elseif($this->tag['cosource'.$k] == '2'){ # ʹ�ü���ccid$k
						if($NowCcid = (int)cls_Parse::Get('_a.ccid'.$k)) $ccidArray[] = $NowCcid;
					}
					if($ccidArray && !empty($this->tag['ccidson'.$k])){
						$_sons = array();
						foreach($ccidArray as $y) $_sons = array_merge($_sons,sonbycoid($y,$k));
						$ccidArray = array_unique($_sons);
						unset($_sons);
					}
					if($ccidArray && $str = cnsql($k,$ccidArray,'a.')) $sqlwhere .= ' AND '.$str;
				}
			}
		}
		
		# ��Ҫģ�ͱ����Ϣ
		if(!empty($this->tag['detail']) && $SingleChid && cls_channel::Config($SingleChid)){
			$sqlfrom .= " INNER JOIN ".self::$tblprefix."archives_$SingleChid c ON c.aid=a.aid";
			$sqlselect .= ",c.*";
		}
		if(!empty($this->tag['ids'])){
			$sqlwhere .= cls_DbOther::str_fromids($this->tag['ids'],'a.aid');
		}
		
		# �����ų���ģ��id
		if(!empty($this->tag['nochids'])){
			if($nochids = explode(',',$this->tag['nochids'])){
				$sqlwhere .= " AND a.chid ".multi_str($nochids,1);
			}
		}
		$sqlwhere .= " AND a.checked=1";
		
		# ��ʾ�����Ա�������ĵ�
		if(!empty($this->tag['space'])){
			if($NowMid = (int)cls_Parse::Get('_a.mid')){
				$sqlwhere .= " AND a.mid='".$NowMid."'";
			}else{
				$this->TagThrowException("�޷�����mid");
			}
		}
		
		# ָ������Ļ�Ա�ռ�ĸ��˷���
		if(!empty($this->tag['ucsource'])){
			if($NowUcid = (int)cls_Parse::Get('_a.ucid')){
				$sqlwhere .= " AND a.ucid='".$NowUcid."'";
			}
		}
		
		# ֻ��ʾ��Ч���ڵ��ĵ�
		if(!empty($this->tag['validperiod'])){
			$sqlwhere .= " AND (a.enddate=0 OR a.enddate>'".self::$timestamp."')";
		}
		$sqlwhere = $sqlwhere ? ' WHERE '.substr($sqlwhere,5) : '';
		$sqlstr = $sqlselect.$sqlfrom.$sqlwhere;
		
		return $sqlstr;
	}
	
	
}
