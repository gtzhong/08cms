<?PHP
/**
* [�ܹ������б�] ��ǩ������
* ��cls_Parse����ģ����Ʋ���ֱ�ӽӴ���ǩ������
*/

defined('M_COM') || exit('No Permission');
abstract class cls_Tag_FromidsBase extends cls_TagParse{
	
	# �������ݽ��
	protected function TagReSult(){
		$ReturnArray = array();
		$limits = $this->TagInitLimits();
		
		$lb = empty($this->tag['listby']) ? 0 : intval($this->tag['listby']);
		$i = 1;
		if(!$lb){
			$arr = cls_channel::Config();
		}elseif($lb == 1){
			$arr = cls_cache::Read('mchannels');
		}elseif($lb == 2){
			$arr = array();
		}elseif($lb > 10){
			$arr = cls_cache::Read('usergroups',$lb-10);
		}
		foreach($arr as $k => $v){
			if($i > $limits) break;
			if(empty($this->tag['source'.$lb]) || in_array($k,explode(',',@$this->tag['ids'.$lb]))){
				if(!$lb){
					$ReturnArray[] = array('chid' => $k,'cname' => $v['cname'],'sn_row' => $i,);
				}elseif($lb == 1){
					$ReturnArray[] = array('mchid' => $k,'title' => $v['cname'],'sn_row' => $i,);
				}elseif($lb == 2){
				}elseif($lb > 10){
					$key = $lb-10;
					$ReturnArray[] = array('grouptype'.$key => $k,'cname' => $v['cname'],'sn_row' => $i,) + cls_node::mcnodearr('ugid'.$key.'='.$k);
				}
				$i ++;
			}
		}
		return $ReturnArray;
	}
	
	
}
