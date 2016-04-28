<?PHP
/**
* [������Ŀ�ڵ�] ��ǩ������
* ��cls_Parse����ģ����Ʋ���ֱ�ӽӴ���ǩ������
*/

defined('M_COM') || exit('No Permission');
abstract class cls_Tag_CnodeBase extends cls_TagParse{
	
	protected function TagReSult(){
		$ListCoid = $this->tag['listby'] == 'ca' ? 0 : intval(str_replace('co','',$this->tag['listby']));
		$ListID = $ListCoid ? @$this->tag['cosource'.$ListCoid] : @$this->tag['casource'];
		$ListIDVar = $ListCoid ? "ccid$ListCoid" : 'caid';
		
		if(!empty($ListID)){ # ת��Ϊ������ĳ��(level)��ĿID
			$NowID = is_numeric($ListID) ? $ListID : (int)cls_Parse::Get("_a.$ListIDVar");
			if($NowID && !empty($this->tag['level'])){
				$narr = $ListCoid ? cls_cache::Read('coclasses',$ListCoid) : cls_cache::Read('catalogs');
				$NowID = cls_catalog::cn_upid($NowID,$narr,$this->tag['level'] - 1);
				unset($narr);
			}
		}
		if(empty($NowID)) $this->TagThrowException("δ�ҵ�ָ������ĿID");
		
		$midarr[$ListIDVar] = $NowID;
		if($ListCoid && !empty($this->tag['casource'])){
			if(is_numeric($this->tag['casource'])){
				$midarr['caid'] = $this->tag['casource'];
			}elseif(cls_Parse::Get('_a.caid')) $midarr['caid'] = (int)cls_Parse::Get('_a.caid');
		}
		$cotypes = cls_cache::Read('cotypes');
		foreach($cotypes as $k => $v){
			if($v['sortable'] && !isset($midarr["ccid$k"]) && !empty($this->tag['cosource'.$k])){
				if(is_numeric($this->tag['cosource'.$k])){
					$midarr['ccid'.$k] = $this->tag['cosource'.$k];
				}elseif(cls_Parse::Get("_a.ccid$k")) $midarr['ccid'.$k] = (int)cls_Parse::Get("_a.ccid$k");
			}
		}
		
		$cnstr = cls_cnode::cnstr($midarr);
		$ReturnArray = cls_node::cn_parse($cnstr,$ListCoid);
		$cnode = cls_node::cnodearr($cnstr,defined('IN_MOBILE'));
		cls_node::re_cnode($ReturnArray,$cnstr,$cnode);
		return $ReturnArray;
	}
}
