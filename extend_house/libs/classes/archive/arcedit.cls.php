<?php
class cls_arcedit extends cls_arcbase{
	
	/**
	 * ɾ����չ������ �ϼ�������
	 *
	 * @param  int $isdelbad ���� �Ƿ� �ۻ���ɾ��
	 */
	function arc_delete($isdelbad=0){
		global $db,$tblprefix;
		if(empty($this->aid)) return false;
		
		/********** ��չ����Start ***************/
		$chid = $this->archive['chid'];
		$aid = $this->aid;
		
		// ɾ��¥��-ͬʱɾ�� : �·��Ź�,¥�����,����
		// ���� ɾ���ϼ���ϵǰ
		// ?? �ɹ���������¥��,��ɾ��?! (δ����)
		if($chid == 4){
			$exit = new cls_arcedit;
			$re = $db->query("DELETE FROM {$tblprefix}housesrecords WHERE aid='$aid' ");
			foreach(array(5,7,11) as $h){ //; 2,3,
				$query = $db->query("SELECT aid FROM {$tblprefix}".atbl($h)." WHERE pid3='$aid'");
				while($r = $db->fetch_array($query)){
					$exit->set_aid($r['aid'],array('chid'=>$h));
					$exit->arc_delete(0);
				}
			}
			// ɾ����Ӧ����(113)
			$query = $db->query("SELECT aid FROM {$tblprefix}".atbl(113)." WHERE pid33= '$aid'");
			while($r = $db->fetch_array($query)){
				$exit->set_aid($r['aid'],array('chid'=>113));
				$exit->arc_delete(0);
			}
			// chid=107(�ؼ۷�)
			$query = $db->query("SELECT inid FROM {$tblprefix}aalbums WHERE pid='$aid'");
			while($r = $db->fetch_array($query)){
				$exit->set_aid($r['inid'],array('chid'=>107));
				$exit->arc_delete(0);
			}
			unset($exit);
		}
		// chid=121(��ԴͼƬ)
		if(in_array($chid,array(2,3))){
			$exit = new cls_arcedit;
			$query = $db->query("SELECT aid FROM {$tblprefix}".atbl(121)." WHERE pid38= '$aid'");
			while($r = $db->fetch_array($query)){
				$exit->set_aid($r['aid'],array('chid'=>121));
				$exit->arc_delete(0);
			}
			unset($exit);
		}
		
		//ɾ���ϼ���ϵ
		$abrels = cls_cache::Read('abrels');
		foreach($abrels as $k=>$abrel){
		if($abrel['available']){
			if(empty($abrel['tchids']) || empty($abrel['schids'])) continue; 
			if(empty($abrel['tbl'])){
				if(in_array($chid,@$abrel['tchids'])){
					foreach($abrel['schids'] as $ch){
						if($ntbl = atbl($ch)){
							$sql = "UPDATE {$tblprefix}".atbl($ch)." SET pid$k='0',inorder$k='0',incheck$k='0' WHERE pid$k='$aid' ";
							$query = $db->query($sql);
						}
					}
				}
			}elseif(in_array($chid,@$abrel['schids'])){
				$query = $db->query("DELETE FROM {$tblprefix}$abrel[tbl] WHERE inid='$aid' ");
			}elseif(in_array($chid,@$abrel['tchids'])){
				$query = $db->query("DELETE FROM {$tblprefix}$abrel[tbl] WHERE pid='$aid' ");
			}
		}}
		
		//ɾ������
		$commus = cls_cache::Read('commus');
		foreach($commus as $k => $v){
			// 49,50:�����Ƽ���Ϣ,Ӷ�������Ϣ(û��aid),��Ҫ�Ļ���������
			if(in_array($k,array(5,10,11,31,32,34,40,42,49,50))) continue; //��Щû���ĵ�����,10-��˾�ʽ�,40-��վ����
			$db->query("DELETE FROM {$tblprefix}$v[tbl] WHERE aid='$aid'",'SILENT');
		}
		// �����Ƽ���Ϣ .... (�ɽ�����)״̬�����ύ����
		#$v = $commus[49]; 
		#$db->query("DELETE FROM {$tblprefix}$v[tbl] WHERE aids LIKE '%,$aid,%'",'SILENT');
		// Ӷ�������Ϣ,��ɾ��..???
		
		/********** ��չ����End ***************/
		
		$this->_arc_delete($isdelbad);
		return true;
	}
	
	function arcadd($chid = 0,$caid = 0,$aid = 0){//���һ���ĵ�
		if($aid = parent::arcadd($chid,$caid,$aid)){
			if($chid == 2){
				$this->auser->updatefield('aczfys',@$this->auser->info['aczfys'] + 1,'members_sub');
			}elseif($chid == 3){
				$this->auser->updatefield('aesfys',@$this->auser->info['aesfys'] + 1,'members_sub');
			}elseif($chid == 9){
				$this->auser->updatefield('aqgs',@$this->auser->info['aqgs'] + 1,'members_sub');
			}elseif($chid == 10){
				$this->auser->updatefield('aqzs',@$this->auser->info['aqzs'] + 1,'members_sub');
			}
			$this->auser->updatedb();
		}
		return $aid;
	}	
}