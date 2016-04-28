<?PHP
/**
* [��Ŀ�б�] ��ǩ�����࣬ʵ������Ŀ�ڵ��б���Ҫ�����Ż�һ��?????????
* ��cls_Parse����ģ����Ʋ���ֱ�ӽӴ���ǩ������
*/

defined('M_COM') || exit('No Permission');
abstract class cls_Tag_CatalogsBase extends cls_TagParse{
	
	protected $_ListCoid = 0;		# ����ǩ���б���ϵID���������Ŀ����Ϊ0
	protected $_cotypes = array();	# ��ϵ���ϣ���Ҫ�ظ�ʹ��
	
	
	# �������ݽ��
	protected function TagReSult(){
		return $this->TagResultBySql();
	}
	
	# ��ʼ����ǰ��ǩ
	protected function _TagInit(){
		$this->_ListCoid = $this->tag['listby'] == 'ca' ? 0 :  intval(str_replace('co','',$this->tag['listby']));
		$this->_cotypes= cls_cache::Read('cotypes');
	}
	
	# ���ؽ���еĵ�����¼�Ĵ���
	protected function TagOneRecord($OneRecord){
			
		$midarr = $this->_ListCoid ? array("ccid{$this->_ListCoid}" => $OneRecord['ccid']) : array('caid' => $OneRecord['caid']);
		if($this->_ListCoid && !empty($this->tag['cainherit'])){
			if(is_numeric($this->tag['cainherit'])){
				$midarr['caid'] = $this->tag['cainherit'];
			}elseif(cls_Parse::Get('_a.caid')) $midarr['caid'] = (int)cls_Parse::Get('_a.caid');
		}
		
		$cotypes = cls_cache::Read('cotypes');
		foreach($cotypes as $k => $v){
			if($v['sortable'] && !isset($midarr["ccid$k"]) && !empty($this->tag['coinherit'.$k])){
				if(is_numeric($this->tag['coinherit'.$k])){
					$midarr['ccid'.$k] = $this->tag['coinherit'.$k];
				}elseif(cls_Parse::Get('_a.ccid'.$k)) $midarr['ccid'.$k] = (int)cls_Parse::Get('_a.ccid'.$k);
			}
		}

		$cnstr = cls_cnode::cnstr($midarr);
		foreach($midarr as $k => $v){
			$coid = $k == 'caid' ? 0 : intval(str_replace('ccid','',$k));
			if($item = $coid ? cls_cache::Read('coclass',$coid,$v) : cls_cache::Read('catalog',$v)){
				$OneRecord[$coid ? "ccid$coid" : 'caid'] = $v;
				$OneRecord[$coid ? 'ccid'.$coid.'title' : 'catalog'] = $item['title'];
			}
		}
		$cnode = cls_node::cnodearr($cnstr,defined('IN_MOBILE'));
		cls_node::re_cnode($OneRecord,$cnstr,$cnode);
		
		return $OneRecord;
	}
	
	# ȡ��Ĭ�ϵ������ִ�
	protected function TagDefaultOrderStr(){
		return ' ORDER BY trueorder ASC';
	}
	
	
	# ����ָ������Ŀ������Ŀ���õ��뼤��id���������һ��ϵ�����з���id
	protected function idsbyrel($tid,$coid = 0){
		
		$ReturnArray = array();
		$cnrels = cls_cache::Read('cnrels');
		if(!($cnrel = &$cnrels[$tid])) return $ReturnArray;
		$reverse = 0;
		$nvar = $coid;
		if(in_array($coid,array($cnrel['coid'],$cnrel['coid1']))){
			if($coid == $cnrel['coid']){
				$reverse = 1;//�����ϵ
				$nvar = $cnrel['coid1'];
			}else $nvar = $cnrel['coid'];
		}else return $ReturnArray;
		if(!($nid = (int)cls_Parse::Get($nvar ? "_a.ccid$nvar" : '_a.caid'))) return $ReturnArray;
	
		if($reverse){
			foreach($cnrel['cfgs'] as $k => $v){
				$v = empty($v) ? array() : array_filter(explode(',',$v));
				in_array($nid,$v) && $ReturnArray[] = $k;
			}
		}else $ReturnArray = empty($cnrel['cfgs'][$nid]) ? array() : array_filter(explode(',',$cnrel['cfgs'][$nid]));
		return $ReturnArray;
	}
	
	# ���ݱ�ǩ����ƴ��sqlstr���õ�SQL����Ҫ����(select��from��where)
	protected function CreateTagSqlBaseStr(){

		$sourcestr = @$this->tag[$this->_ListCoid ? "cosource{$this->_ListCoid}" : 'casource'];

		$sqlselect = "SELECT *";
		$sqlfrom = " FROM ".self::$tblprefix.($this->_ListCoid ? "coclass{$this->_ListCoid}" : 'catalogs').$this->ForceIndexSql();
		$sqlwhere = $this->TagHandWherestr();
		$sqlwhere = $sqlwhere ? " AND $sqlwhere" : '';
		
		if(empty($sourcestr)){ # ���ж�����Ŀ
			$sqlwhere .= " AND level=0";
		}elseif($sourcestr == 1){ # �ֶ�ѡ����Ŀid
			if($ids = array_filter(explode(',',@$this->tag[$this->_ListCoid ? 'ccids'.$this->_ListCoid : 'caids']))){
				$sqlwhere .= ' AND '.($this->_ListCoid ? 'ccid ' : 'caid ').multi_str($ids);
			}else $this->TagThrowException("��Ҫ�ֶ�ѡ����Ŀid");
		}elseif($sourcestr == 2){//������Ŀ����������Ŀ
			if($actid = (int)cls_Parse::Get($this->_ListCoid ? "_a.ccid{$this->_ListCoid}" : '_a.caid')){
				$sqlwhere .= " AND pid=$actid";
			}else $this->TagThrowException("�޷�ȡ�ü�����Ŀid");
		}elseif($sourcestr == 4){ # һ����Ŀ
			$sqlwhere .= " AND level=1";
			$sqlwhere .= $this->_ListCoid ? " AND coid={$this->_ListCoid}" : '';
		}elseif($sourcestr == 5){ # ������Ŀ
			$sqlwhere .= " AND level=2";
			$sqlwhere .= $this->_ListCoid ? " AND coid={$this->_ListCoid}" : '';
		}elseif($sourcestr < 0){ # ������Ŀ
			if($ids = $this->idsbyrel(abs($sourcestr),$this->_ListCoid)){
				$sqlwhere .= ' AND '.($this->_ListCoid ? 'ccid ' : 'caid ').multi_str($ids);
			}else  $this->TagThrowException("δ�ҵ���������Ŀid");
		}
		$sqlwhere = ' WHERE '.substr($sqlwhere.' AND closed=0',5);
		$sqlstr = $sqlselect.$sqlfrom.$sqlwhere;
		
		return $sqlstr;
	}
	
	
}
