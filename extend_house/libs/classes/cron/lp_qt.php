<?php
!defined('M_COM') && exit('No Permission');
class cron_lp_qt extends cron_exec{    
	public function __construct(){
		parent::__construct();
		$this->main();
	}
	public function main(){
		
  
		$this->db->query("UPDATE {$this->tblprefix}archives15 SET ayss=0,adps=0,lpczsl=0,lpesfsl=0");
		
		
		//¥���ڽ�������ͳ�ƣ�����(ayss),¥�̹�ע��ӡ��ayxs
		$commu = cls_cache::Read('commu',3);
		$sqlin = "SELECT aid,COUNT(*) AS z FROM {$this->tblprefix}$commu[tbl] GROUP BY aid";
		$sql = "UPDATE {$this->tblprefix}".atbl(4)." a,($sqlin) t
				SET a.ayss=t.z WHERE a.aid=t.aid";
		#echo $sql; 
		$this->db->query($sql);
		
		
		//¥���ڽ�������ͳ�ƣ�����
		//�ο͵�ҳ�㣿mname != '' AND mid != '' 
		$commu = cls_cache::Read('commu',48); 
		$sqlin = "SELECT aid,COUNT(*) AS z FROM {$this->tblprefix}$commu[tbl] WHERE tocid = '0' and mname != '' GROUP BY aid";
		$sql = "UPDATE {$this->tblprefix}".atbl(4)." a,($sqlin) t
				SET a.adps=t.z WHERE a.aid=t.aid";
		#echo $sql; 
		$this->db->query($sql);
		
		
		//¥���ںϼ�����ͳ�ƣ��ʴ� 
		//$commu = cls_cache::Read('commu',2);
		$sqlin = "SELECT b.pid AS pid, COUNT(*) AS z FROM {$this->tblprefix}".atbl(106)." a INNER JOIN {$this->tblprefix}aalbums b ON b.inid=a.aid WHERE b.arid='1' GROUP BY b.pid";
		$sql = "UPDATE {$this->tblprefix}".atbl(4)." a,($sqlin) t
				SET a.awds=t.z WHERE a.aid=t.pid";
		$this->db->query($sql);
		
		//¥�̺ϼ���
		//lpczsl ��¥���ڳ��ⷿԴ����x
		//lpesfsl ��¥���ڶ��ַ�Դ����x
		$chids = array('lpczsl'=>'2','lpesfsl'=>'3');
		foreach($chids as $k => $v){
		$sqlin = "SELECT a.pid3 AS pid, COUNT(*) AS z FROM {$this->tblprefix}".atbl($v)." a  GROUP BY a.pid3";
		$sql = "UPDATE {$this->tblprefix}".atbl(4)." a,($sqlin) t
				SET a.".$k."=t.z WHERE a.aid=t.pid";
		$this->db->query($sql);
		}
		
	}	
}

