<?PHP
/**
* [�����ĵ��б�] ��ǩ������
* ��cls_Parse����ģ����Ʋ���ֱ�ӽӴ���ǩ������
*/

defined('M_COM') || exit('No Permission');
abstract class cls_Tag_ArchiveBase extends cls_TagParse{
	
	
	protected function TagReSult(){
		$Nowid = intval(empty($this->tag['id']) ? cls_Parse::Get('_a.aid') : $this->tag['id']);
		if($Nowid && !empty($this->tag['arid'])){
			if(!$abrel = cls_cache::Read('abrel',(int)$this->tag['arid'])){
				$this->TagThrowException("��ָ����ȷ�ĺϼ���Ŀarid");	
			}
			if(!empty($abrel['tbl'])){
				$Nowid = self::$db->result_one("SELECT pid FROM ".self::$tblprefix.$abrel['tbl']." WHERE inid='$Nowid'");
			}elseif(!empty($abrel['source'])){
				$Nowid = self::$db->result_one("SELECT pid".$this->tag['arid']." FROM ".self::$tblprefix."members WHERE mid='$Nowid'");
			}elseif($ntbl = atbl($Nowid,2)){
				$Nowid = self::$db->result_one("SELECT pid".$this->tag['arid']." FROM ".self::$tblprefix."$ntbl WHERE aid='$Nowid'");
			}else $Nowid = 0;
		}
		if(!$Nowid) $this->TagThrowException("δ��ָ���򼤻��id");	
		
		$arc = new cls_arcedit;
		if(!$arc->set_aid($Nowid,array('chid'=>intval(@$this->tag['chid']),'ch'=>@$this->tag['detail'],'au'=>0,'nodemode'=>defined('IN_MOBILE'),'ttl'=>intval(@$this->tag['ttl']),))){
			$this->TagThrowException("δ�ҵ�ָ�����ĵ�");	
		}
		$ReturnArray = $arc->archive;
		unset($arc);
		
		$ReturnArray = $this->TagOneRecord($ReturnArray); # ���ؽ���ĵ�����¼����
		return $ReturnArray;
	}
	
	
	# ���ؽ���еĵ�����¼�Ĵ���
	protected function TagOneRecord($OneRecord){
		cls_ArcMain::Parse($OneRecord);
		return $OneRecord;
	}
	
	
}
