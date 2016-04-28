<?php
/**
 * �����б����ظ���
 *
 * @example   ������URL��index.php?/ajax/pageload_rems/aj_model/cu,1/aid/542753/aj_pagesize/5/aj_pagenum/4/domain/192.168.1.11/
 * @author    Peace@08cms.com
 * @copyright Copyright (C) 2008 - 2014 08CMS, Inc. All rights reserved.
 */

defined('_08CMS_APP_EXEC') || exit('No Permission');
class _08_M_Ajax_pageload_rems extends _08_M_Ajax_pageload_Base{
    
	private $tplurl = ''; //ģ��Ŀ¼
	
	public function __toString(){
		//��ʼ����ģ��da����
		$this->_initDa(array('aid','toaid'));
		if(!in_array($this->mcfgs[1],array(1,48))) die('Error::cuid='.$this->mcfgs[1]);
		//����sql����
		$this->_getSql();
		//��ʼ��ģ��Ŀ¼
		//$this->tplurl = cls_tpl::TemplateTypeDir();
		$btags = cls_cache::Read('btags'); 
		$this->tplurl = $btags['tplurl'];  
		//������
		$result = $this->_cuList($this->_ajda['aid']);
		//���۵Ļظ�
		foreach($result as $k=>$r){
			$result[$k]['subitems'] = $this->_cuList(0,$r['cid']);
		} //print_r($result);
        return $result; 
    }
	
	public function _cuList($aid=0,$tocid=0){
		foreach($this->sqlarr as $k){
			$$k = $this->$k;
		} 
		//��չwhere����
		if($aid){
			$where .= " AND aid='$aid' AND tocid='0'"; 
		}else{
			$where .= " AND tocid='$tocid'";
			$limit = 999;	
		} //echo "\n<br>2.$where;\n<br>"; 
		//ȫ��sql�����
		$sql = "SELECT $select FROM $from WHERE $where ORDER BY $order LIMIT $limit";
		$result = $this->_getData($sql); //echo "\n<br>$sql;\n<br><br>";
		//��չresult�������
		foreach($result as $k=>$r){
			$result[$k] = $r; 	
		}
		return $result;
	}
	
}