<?PHP
/**
* [��Ա�б�] ��ǩ������
* ��cls_Parse����ģ����Ʋ���ֱ�ӽӴ���ǩ������
*/

defined('M_COM') || exit('No Permission');
abstract class cls_Tag_MembersBase extends cls_TagParse{
		
	# �������ݽ��
	protected function TagReSult(){
		if($this->tag['tclass'] == 'members'){
			return $this->TagResultBySql();
		}else{
			$func = 'TagReSult_'.$this->tag['tclass'];
			return $this->$func();
		}
	}
	
	# ���ؽ���еĵ�����¼�Ĵ���
	protected function TagOneRecord($OneRecord){
		cls_UserMain::Parse($OneRecord,true);
		return $OneRecord;
	}
		
	# �ĵ�����ͳ��(acount)��ǩ�����ݷ���
	protected function TagReSult_mcount(){
		$ReturnArray = array('counts' => 0);
		if($sqlstr = $this->TagSqlStr(true)){
			$ReturnArray['counts'] = self::$db->result_one($sqlstr,intval(@$this->tag['ttl']));
		}
		return $ReturnArray;
	}
	
	# ȡ��Ĭ�ϵ������ִ�
	protected function TagDefaultOrderStr(){
		return ' ORDER BY m.mid DESC';
	}
	
	# ���ݱ�ǩ����ƴ��sqlstr���õ�SQL����Ҫ����(select��from��where)
	protected function CreateTagSqlBaseStr(){
		
		$sqlselect = "SELECT m.*,s.*";
		$sqlfrom = " FROM ".self::$tblprefix."members m".$this->ForceIndexSql('m').
				   " INNER JOIN ".self::$tblprefix."members_sub s".$this->ForceIndexSql('s')." ON s.mid=m.mid";
		$sqlwhere = $this->TagHandWherestr();
		$sqlwhere = $sqlwhere ? " AND $sqlwhere" : '';
		if(!empty($this->tag['mode'])){
			$NowID = empty($this->tag['id']) ? cls_Parse::Get('_a.mid') : $this->tag['id'];
			if(!($NowID = (int)$NowID)) $this->TagThrowException("δָ����Ч�Ĺ���ID");
			if($this->tag['mode'] == 'in'){
				if(!$abrel = cls_cache::Read('abrel',@$this->tag['arid'])) $this->TagThrowException("δָ����Ч�ĺϼ���Ŀ");
				if($abrel['tbl']){
					$sqlfrom = " FROM ".self::$tblprefix."$abrel[tbl] b".$this->ForceIndexSql('b').
					" INNER JOIN ".self::$tblprefix."members m".$this->ForceIndexSql('m')." ON m.mid=b.inid".
					" INNER JOIN ".self::$tblprefix."members_sub s".$this->ForceIndexSql('s')." ON s.mid=m.mid";
					$sqlselect .= ",b.*";
					$sqlwhere .= " AND b.pid='".$NowID."' AND b.arid='".$this->tag['arid']."'";
				}else $sqlwhere .= " AND m.pid".$this->tag['arid']."='".$NowID."'";
			}elseif($this->tag['mode'] == 'belong'){
				if(!$abrel = cls_cache::Read('abrel',@$this->tag['arid'])) $this->TagThrowException("δָ����Ч�ĺϼ���Ŀ");
				if($abrel['tbl']){
					$sqlfrom = " FROM ".self::$tblprefix."$abrel[tbl] b".$this->ForceIndexSql('b').
					" INNER JOIN ".self::$tblprefix."members m".$this->ForceIndexSql('m')." ON m.mid=b.pid".
					" INNER JOIN ".self::$tblprefix."members_sub s".$this->ForceIndexSql('s')." ON s.mid=m.mid";
					$sqlselect .= ",b.*";
					$sqlwhere .= " AND b.inid='".$NowID."' AND b.arid='".$this->tag['arid']."'";
				}else{
					if(!($pid = self::$db->result_one("SELECT mid FROM ".self::$tblprefix."members WHERE pid".$this->tag['arid']."='".$NowID."'"))){
						$this->TagThrowException("δ�ҵ������ϼ�id");
					}
					$sqlwhere .= " AND m.mid='$pid'";
				}
			}
		}
		$grouptypes = cls_cache::Read('grouptypes');
		foreach($grouptypes as $k => $v){
			if(!empty($this->tag['ugid'.$k])){
				$sqlwhere .= " AND m.grouptype$k='".(int)$this->tag['ugid'.$k]."'";
			}
		}
		if(!empty($this->tag['chsource'])){
			$NowMchid = 0;
			if($this->tag['chsource'] == 1){
				$NowMchid = (int)cls_Parse::Get('_a.chid');
				if(empty($NowMchid)) $this->TagThrowException("�޷������Աģ��id");
				$sqlwhere .= " AND m.mchid='".$NowMchid."'";
			}elseif($this->tag['chsource'] == 2){
				if(empty($this->tag['chids'])) $this->TagThrowException("��Ҫ���û�Աģ��id");
				$tchids = explode(',',$this->tag['chids']);
				$sqlwhere .= " AND m.mchid ".multi_str($tchids);
				if(count($tchids) == 1) $NowMchid = $tchids[0];
			}
			if(!empty($this->tag['detail']) && $NowMchid && cls_cache::Read('mchannel',$NowMchid)){
				$sqlfrom .= " INNER JOIN ".self::$tblprefix."members_$NowMchid c".$this->ForceIndexSql('c')." ON c.mid=m.mid";
				$sqlselect .= ",c.*";
			}
		}
		$sqlwhere .= " AND m.checked=1";
		if(!empty($this->tag['ids'])) $sqlwhere .= cls_DbOther::str_fromids($this->tag['ids'],'m.mid');
		$sqlwhere = $sqlwhere ? ' WHERE '.substr($sqlwhere,5) : '';
		$sqlstr = $sqlselect.$sqlfrom.$sqlwhere;
		return $sqlstr;
	}	
}
