<?php
defined('M_COM') || exit('No Permission');
class cls_culist extends cls_culistbase{
	
	function s_footer_area($fix=''){
		$coc1 = array('0'=>array('title'=>'(��վ)')) + cls_cache::Read('coclasses',1);
		$links = '';
		foreach($coc1 as $ccid=>$v){ 
			$title = $v['title'];
			if(cls_env::GetG('area')==$ccid) $title = "<b>$title</b>";
			$links .= "<a href='$fix&area=$ccid'>$title</a> &nbsp; ";
		}
		$links .= "<a href='$fix&fill_sites=1'>[��ȫ��������]</a> &nbsp; ";
		echo $links;
		tabfooter();
		unset($this->oS);

		
	}
	
	/**
	*����¥�̶����б���������ݴ���               
	*/
	function sv_o_all_lpdy($cfg=array()){
		$ofm = @$GLOBALS[$this->A['ofm']];
		$selectid = @$GLOBALS['selectid'];
		$rs = $this->m_db_array();//�ٴ����Ʒ�Χ���Է�����Ȩ�޽��в���
		if($ofm && $selectid && $rs){
			$actcu = &$this->oO->actcu;
			foreach($rs as $r){ 
				if(!in_array($r['mid'],$selectid)) continue;
				$actcu = $r['mid'];        
				if(!empty($ofm['del_lpdy'])){//ɾ���򲻼�����������
					$this->sv_o_one('del_lpdy');
					continue;
				}
				foreach($ofm as $key => $v){ 
					$this->sv_o_one($key);
				}
				//$auser->updatedb();
			}
		}
	}
	
	function s_footer_ex($url,$orther=array()){
		global $authkey;
		$where_str = '';
		if(!empty($this->oS->wheres)){
			foreach($this->oS->wheres as $k => $v){
				$where_str .= " AND $v";
			}
		}		
		//��������������ɵ�sql��������Ҫ�����sql��ɲ���
		if(!empty($orther) && !empty($orther['url'])){
			$where_str .= " AND $orther[url]";
		}
		$where_str = urlencode(trim($where_str));
        $p = md5($where_str.$authkey);//���۸ļ��ܲ���,���ݲ������ж�$where_str+$authkey���ܺ���ַ�����$p�Ƿ�һ��

		$html = "<a style=\"float:right;text-decoration:none;\" onclick=\"return floatwin('open_arcdetail',this)\" href=\"".$url."&q=".$where_str."&p=".$p."\"><input class='excel_button'  type=\"button\" value=\"EXCEL����\"></a>";
		if(empty($this->A['MoreSet'])){
			echo strbutton('bfilter','ɸѡ');
			echo $html;
		}else{
			echo $html;
			echo "</div></div>";//�߼�����β
		}
		tabfooter();
		unset($this->oS);
	}
    
    //�ʴ��ͷ����ʾ
    function m_header_ex($answertype,$entry,$extend_str,$filterstr,$aid){
        $temparr = array(1=>'����Ĵ�',3=>'����Ĳ���',2=>'�𰸵Ĳ�����׷��');
		$str = '';
		foreach($temparr as $k=>$v){
			$str .= $k == $answertype ? ' - <font color="red">'.$v.'</font>' : " - <a href=\"?entry=$entry$extend_str&aid=$aid$filterstr&answertype=$k\">$v</a>";
		}
		$str = substr($str,3);  
		$exstr = $aid ? " &nbsp; <a href='?entry=extend$extend_str'>ȫ����&gt;&gt;</a>" : '';     
		$this->m_header($str.$exstr);
    }
}
