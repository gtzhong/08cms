<?PHP
/**
* [������Ա] ��ǩ������
* ��cls_Parse����ģ����Ʋ���ֱ�ӽӴ���ǩ������
*/

defined('M_COM') || exit('No Permission');
abstract class cls_Tag_MemberBase extends cls_TagParse{
	
	
	protected function TagReSult(){
		$Nowid = intval(empty($this->tag['id']) ? cls_Parse::Get('_a.mid') : $this->tag['id']);
		if($Nowid == '-1') $Nowid = self::$curuser->info['mid'];
		if(!empty($this->tag['arid'])){
			if(!$Nowid) $this->TagThrowException("��ָ����ػ�ԱID");
			if(!($abrel = cls_cache::Read('abrel',$this->tag['arid']))) $this->TagThrowException("��ָ����ȷ�ĺϼ���Ŀarid");
			if($abrel['tbl']){
				$Nowid = self::$db->result_one("SELECT pid FROM ".self::$tblprefix.$abrel['tbl']." WHERE inid='$Nowid'");
			}else $Nowid = self::$db->result_one("SELECT pid".$this->tag['arid']." FROM ".self::$tblprefix.($abrel['source'] ? 'members' : 'archives')." WHERE ".($abrel['source'] ? 'mid' : 'aid')."='$Nowid'");
			if(!$Nowid) $this->TagThrowException("δ�ҵ�������Ļ�Ա");
		}
		
		$auser = new cls_userinfo;
		$auser->activeuser($Nowid,empty($this->tag['detail']) ? 0 : 1,intval(@$this->tag['ttl']));
		if(@$auser->info['checked'] != 1){ # δ���Ա���ο�����
			$ReturnArray = cls_userinfo::nouser_info();
		}elseif(!empty($this->tag['chids']) && $this->tag['chids']!=$auser->info['mchid']){ //ָ����Աģ��,������������,���οʹ���
			$ReturnArray = cls_userinfo::nouser_info();
		}else{
			$ReturnArray = $auser->info;
		}
		unset($auser);
		
		$ReturnArray = $this->TagOneRecord($ReturnArray);
		return $ReturnArray;//���ܳ����ο�����
	}
	
	
	# ���ؽ���еĵ�����¼�Ĵ���
	protected function TagOneRecord($OneRecord){
		cls_UserMain::Parse($OneRecord);
		return $OneRecord;
	}
	
	
}
