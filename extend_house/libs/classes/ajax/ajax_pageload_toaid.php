<?php
/**
 * �����б����ظ���
 *
 * @example   ������URL��index.php?/ajax/pageload_rems/aj_model/cu,1/aid/542753/aj_pagesize/5/aj_pagenum/4/domain/192.168.1.11/
 * @author    Peace@08cms.com
 * @copyright Copyright (C) 2008 - 2014 08CMS, Inc. All rights reserved.
 */

defined('_08CMS_APP_EXEC') || exit('No Permission');
class _08_M_Ajax_pageload_toaid extends _08_M_Ajax_pageload_Base{
    
	private $tplurl = ''; //ģ��Ŀ¼
	
	public function __toString(){
		//��ʼ����ģ��da����
		$this->_initDa(array('aid'));
		if(!in_array($this->mcfgs[1],array(101,999))) die('Error::cuid='.$this->mcfgs[1]);
		//����sql����
		$this->_getSql();
		//��ʼ��ģ��Ŀ¼
		//$this->tplurl = cls_tpl::TemplateTypeDir();
		$btags = cls_cache::Read('btags'); 
		$this->tplurl = $btags['tplurl'];  
		//������(����)
		$result = $this->_cuList($this->_ajda['aid']);
        return $result;
    }
	
	public function _cuList($aid=0){
		foreach($this->sqlarr as $k){
			$$k = $this->$k;
		} 
		//��չwhere����
		$where .= " AND aid='$aid'";  
		//ȫ��sql�����
		$order = $this->_getOrder(array('cid'),'c.aid DESC');
		$sql = "SELECT $select FROM $from WHERE $where ORDER BY $order LIMIT $limit";
		$result = $this->_getData($sql); //echo "\n<br>$sql;\n<br><br>";
		//��չresult�������
		foreach($result as $k=>$r){
			$r['content'] = str_replace(array('{:',':}'),array("<img src=".$this->tplurl."newscommon/images/face/",".gif>"),$r['content']); 
			$r['content'] = cls_url::tag2atm($r['content'],1);
			$result[$k] = $r; 	
		}
		return $result;
	}
	
}