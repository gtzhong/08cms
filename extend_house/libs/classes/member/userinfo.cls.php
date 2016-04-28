<?php
class cls_userinfo extends cls_userbase{
	function exit_comp(){//��ǰ��Ա�˳���˾
		global $db,$tblprefix;
		$arid = 4;
		if(!$this->info['mid']) return false;
		$db->query("UPDATE {$tblprefix}members SET pid$arid='0',inorder$arid=0,incheck$arid='0' WHERE mid={$this->info['mid']}");
		return true;
	}
	function ag2comp($pid=0,$arr = array()){//��ǰ��Ա���빫˾�������
		global $db, $tblprefix;
		$arid = 4;$schid = 2;$tchid = 3;
		if(!$pid || !$this->info['mid'] || $pid == $this->info['mid'] || $this->info['mchid'] != $schid) return false;
		if(!($abrel = cls_cache::Read('abrel', $arid)) || empty($abrel['available'])) return false;
		$pu = new cls_userinfo;
		$pu->activeuser($pid);
		if(!$pu->info['mid'] || !$pu->info['checked'] || $pu->info['mchid'] != $tchid) return false;
		$db->query("UPDATE {$tblprefix}members SET pid$arid='$pid',inorder$arid=0,incheck$arid=0 WHERE mid={$this->info['mid']}");
		return true;
	}
	function updatecrids($crids=array(),$updatedb=0,$remark='',$mode=0){//modeΪ1��ʾΪ�ֶ����			
		global $db,$tblprefix,$timestamp;
		$curuser = cls_UserMain::CurUser();
		$currencys = cls_cache::Read('currencys');
		if(empty($this->info['mid'])) return;
		if(empty($crids) || !is_array($crids)) return;
		
		foreach($crids as $k => $v){
			if(!$v || ($k && empty($currencys[$k]))) continue;
			if($this->info['mchid'] != 2 && $k == 2) continue;
			
			$this->updatefield("currency$k",$this->info["currency$k"] + $v);
			$db->query("INSERT INTO {$tblprefix}currency$k SET
					value='$v',
					mid='".$this->info['mid']."',
					mname='".$this->info['mname']."',
					fromid='".$curuser->info['mid']."',
					fromname='".$curuser->info['mname']."',
					createdate='$timestamp',
					mode='$mode',
					remark='".($remark ? $remark : '����ԭ��')."'");
		}
		$this->autogroup();
		$updatedb && $this->updatedb();
	}
	
	function delete(){
		
		global $db,$tblprefix;
        if(!$this->info['mid'] || $this->info['isfounder']) return false;
		$mid = $this->info['mid']; 
		
		/*  === ע�� ==========================================
		���� 1,2 ���֣���Ҫ����Ӧ��ϵͳ �����������
		======================================================= */
		
		// 1. ɾ��-�ĵ� ( --- ����Ӧ��ϵͳ��Ҫ���� )
		// 2-����,3-���ַ�,9-����,10-��,101,���ʦ,102-װ�ް���,103-��Ʒ,104-��˾��̬,106-�ʴ�,108-��Ƹ
		$chids = array(2,3,9,10,101,102,103,104,106,108); //һ��Ҫɾ������,115,116,117,118,119,120
		// 5-�Ź��,7-¥�����,11-����,13-������,(ԭ��,��¥���������,һ�㲻ɾ)
	
		$_channel = cls_cache::Read("channels");
		$arc = new cls_arcedit;
		foreach($chids as $chidx){
			if(empty($_channel[$chidx])) continue;//ģ�Ͳ���������������ֹ�ͻ�ɾ����ĳ���ĵ�ģ�ͱ�
			$query = $db->query("SELECT aid FROM {$tblprefix}".atbl($chidx)." WHERE mid='$mid' AND chid='$chidx'");			
			while($r = $db->fetch_array($query)){
				$arc->set_aid($r['aid'],$chidx);
				$arc->arc_delete();		
			}
		}	
		
		// 2. ɾ��-���� ( --- ���յĽ�����Ӧ��ϵͳ��Ҫ���� )	
		$cuids = array(5,11,31,32,34); //11-commu_dsc-�����ղ�
		foreach($cuids as $v){
			if($commu = cls_cache::Read('commu',$v)){
				$db->query("DELETE FROM {$tblprefix}$commu[tbl] WHERE tomid='$mid'",'UNBUFFERED');
			}	
		}

		// 3. ɾ��-���� ( --- ���з����Ľ��� )
		$commus = cls_cache::Read('commus');
		foreach($commus as $k => $v){
			if(in_array($k,array(50))) continue; //50-Ӷ�������Ϣ(��ɾ��...) 49-�����Ƽ���Ϣ
			$db->query("DELETE FROM {$tblprefix}$v[tbl] WHERE mid='$mid'",'UNBUFFERED');
		}
		
		// *. ����/����
		//����/֧����־ - ���� cms_currency0,cms_currency1 
		//??? �ʰ� - ��Ѵ𰸣�����--- û�����⴦�� - ���Ƶ��Ƿ�Ҫ���⴦��
		
		$this->_delete();
		return true;
		
	}
}