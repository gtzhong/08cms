<?php
/**
 * ������ʾ�б��У���������ظ��ࡱ�����ظ��ཻ������
 *
 * @example   ������URL��index.php?/ajax/load_more_content/aid/...
 * @author    lyq <692378514@163.com>
 * @copyright Copyright (C) 2008 - 2014 08CMS, Inc. All rights reserved.
 */

defined('_08CMS_APP_EXEC') || exit('No Permission');
class _08_M_Ajax_Load_More_Content extends _08_Models_Base
{
	//���ڲ��ҵ��ֶ�����
	private $field_arr;

	public function __construct(){
		parent::__construct();
		$this->field_arr = array();
	}

    public function __toString()
    {
		$mcharset = $this->_mcharset;
		$db = $this->_db;
		$tblprefix = $this->_tblprefix;
		//�ĵ�AID
		$aid  = empty($this->_get['aid']) ? 0 : max(1,intval($this->_get['aid']));
		//����CUID
		$cuid  = empty($this->_get['cuid']) ? 0 : max(1,intval($this->_get['cuid']));
		//��Ŀ
		$caid = empty($this->_get['caid']) ? 0 : max(1,intval($this->_get['caid']));
		//�ֶ����
		$fieldstr = empty($this->_get['fieldstr']) ? '' : trim($this->_get['fieldstr']);
		$field_arr = explode(',',$fieldstr);
		$this->field_arr = $field_arr;
		//ǰһ��������ȡ�������һ�����ݵ�cid
		$last_cid  = empty($this->_get['last_cid']) ? 0 : max(1,intval($this->_get['last_cid']));

		//�����ѹر�
		$commu = cls_cache::Read('commu',$cuid);
		if(empty($commu)|| !$commu['available']) return 'var data= "�����ѹر�";';

		//ʵ�����ĵ�
		$arc = new cls_arcedit;
		$arc->set_aid($aid);

		//��ָ����ȷ���ĵ�ID
		if(!$arc->aid || !$arc->archive['checked'] || !in_array($arc->archive['chid'],$commu['chids'])) return 'var data= "��ָ����ȷ���ĵ�ID";';

		//����
		$select_str = $this->select_str($commu['tbl']);

		$from_str = " FROM {$tblprefix}".$commu['tbl']." cu INNER JOIN {$tblprefix}".$arc->tbl." a ON cu.aid=a.aid ";

		//����str
		$where_str = " WHERE cu.aid = '$aid' AND cu.tocid='0' AND cu.checked=1";
		if(!empty($last_cid)){
			$where_str .= "  AND cu.cid < '$last_cid' ";
		}

		//����
		$order_str = " ORDER BY cu.cid  DESC ";

		//��������
		$data = array();
		$sql = $db->query("SELECT $select_str  $from_str   $where_str  $order_str limit 10 ");
        $i = 0;
		while($row = $db->fetch_array($sql)){//����
			if(isset($row['createdate']) && !empty($row['createdate'])){
				 $row['createdate'] = date("Y-m-d H:i:s",$row['createdate']);
			}
			$data[$i]['pl'] = $row;
			$hf_sql = $db->query("SELECT $select_str FROM {$tblprefix}".$commu['tbl']." cu WHERE cu.checked=1 AND cu.tocid=".$row['cid']." ORDER BY cid  DESC ");
			while($rows = $db->fetch_array($hf_sql)){//�ظ�
				if(isset($rows['createdate']) && !empty($rows['createdate'])){
					 $rows['createdate'] = date("Y-m-d H:i:s",$rows['createdate']);
				}
				$data[$i]['hf'][] = $rows;
			}
            $i ++;
		}
		if(!empty($data)){
			$data = cls_string::iconv($mcharset, "UTF-8", $data);
			return 'var data = ' . json_encode($data) . ';';
		}else{//��������
			return 'var data = "��������";';
		}

	}

	// ȡ�����ݱ���ֶ���Ϣ
	private function fields($tbl){
		$fields = array();
		$sql = $this->db->query("show full fields from $tbl");
		while($row=$this->db->fetch_assoc($sql)){
			$fields[] = $row['Field'];
		}
		return $fields;
	}

	//��sql
	private function select_str($tbl){
		if(!empty($this->fieldstr)){//ָ�������ֶ�
			//�����ֶ�str
			$select_str = ' cu.cid';
			//��ȡ���ݱ��ֶ�
			$fields = $this->fields($tbl);
			$field_arr = $this->fieldstr;
			foreach($field_arr as $k){
				//�Դ��ݽ����������ֶν���ɸѡ����ֹ�ø�url���ݹ������ֶα���
				if(array_key_exists($k,$fields)){
					$select_str .= ', cu.'.$k;
				}
			}
		}else{//��ָ���ֶ������ȫ��
			$select_str = ' cu.* ';
		}
		return $select_str;
	}



}