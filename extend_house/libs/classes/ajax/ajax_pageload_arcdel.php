<?php
/**
 * �ο�ͨ���ֻ���֤��ɾ��֮ǰ��������Ϣ
 *
 * @example   ������URL��index.php?/ajax/delweituo/cid/...
 * @author    icms <icms@foxmail.com>
 * @copyright Copyright (C) 2008 - 2014 08CMS, Inc. All rights reserved.
 */

defined('_08CMS_APP_EXEC') || exit('No Permission');

class _08_M_Ajax_pageload_arcdel extends _08_M_Ajax_pageload_Base
{
    public function __toString()
    {
		
		$code = empty($this->_get['code']) ? '' : $this->_get['code'];
		$tel = empty($this->_get['tel']) ? '' : $this->_get['tel'];
		
		@$pass = smscode_pass('arcxdel',$code,$tel);
		if(!$pass){
			return array('error'=>'checkError', 'message'=>"��֤�����");
		}
		
		//��ʼ����ģ��da����
		$this->_initDa(); 
        //����ģ�溯��
        include_once(cls_tpl::TemplateTypeDir('function').'utags.fun.php');
		//����sql����
		$this->order = 'a.aid DESC'; 
		$res = array();
		$arr = array('2'=>'11','3'=>'16','9'=>'24','10'=>'25');
		foreach($arr as $chid=>$tbid){
			$this->mcfgs[1] = $chid;
			$this->_getSql(); 
			foreach($this->sqlarr as $k){
				$$k = $this->$k;
			} 
			//ȫ��sql�����
			$sql = "SELECT $select FROM $from WHERE $where ORDER BY $order LIMIT $limit";
			$result = $this->_getData($sql); //print_r($result);
			$res = array_merge($res,$result); 
		}
        return $res; 

        
	}

}