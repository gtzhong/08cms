<?php
!defined('M_COM') && exit('No Permission');
class cron_lp_zs extends cron_exec{    
	public function __construct(){
		parent::__construct();
		$this->main();
	}
	public function main(){
		
		$sql = "UPDATE {$this->tblprefix}".atbl(4)." SET lpczsl='0', czzdj='0', czzgj='0', czpjj='0', lpesfsl='0', csjdj='0', csjgz='0', cspjj='0'";
		$this->db->query($sql);
		
		$timestamp = TIMESTAMP; 
		//'lpczsl'] = $r['z'];//¥�̳��ⷿԴ����
		//'czzdj'] = $r['d'];//������ͼ�
		//'czzgj'] = $r['g'];//������߼�
		//'czpjj'] = round($r['p'],2);//����ƽ����
		$sqlin = "SELECT pid3,COUNT(*) AS z,MIN(zj) AS d,MAX(zj) AS g,ROUND(AVG(zj),2) AS p FROM {$this->tblprefix}".atbl(2)." 
				WHERE checked=1 AND (enddate=0 OR enddate>$timestamp) GROUP BY pid3";
		$sql = "UPDATE {$this->tblprefix}".atbl(4)." a,($sqlin) t
				SET a.lpczsl=t.z, a.czzdj=t.d, a.czzgj=t.g, a.czpjj=t.p
				WHERE a.aid=t.pid3";
		#echo $sql; 
		$this->db->query($sql);
		
		//'lpesfsl'] = $r['z'];//¥�̳��۷�Դ����
		//'csjdj'] = $r['d'];//������ͼ�
		//'csjgz'] = $r['g'];//������߼�
		//'cspjj'] = round($r['p'],2);//����ƽ����
		// ? ����ƽ���� �� dj, zj ? 
		$sqlin = "SELECT pid3,COUNT(*) AS z,MIN(zj) AS d,MAX(zj) AS g,ROUND(AVG(dj),2) AS p FROM {$this->tblprefix}".atbl(3)." 
				WHERE checked=1 AND (enddate=0 OR enddate>$timestamp) GROUP BY pid3";
		$sql = "UPDATE {$this->tblprefix}".atbl(4)." a,($sqlin) t
				SET a.lpesfsl=t.z, a.csjdj=t.d, a.csjgz=t.g, a.cspjj=t.p
				WHERE a.aid=t.pid3";
		#echo $sql; 
		$this->db->query($sql);
		#echo '<br>'.(microtime(1)-$t1);
		
	}	
}
