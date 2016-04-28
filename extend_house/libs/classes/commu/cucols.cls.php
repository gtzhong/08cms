<?php
defined('M_COM') || exit('No Permission');
class cls_cucols extends cls_cucolsbase{
	
	//�۸�����-����
	protected function type_trendarea($key = '',$mode = 0,$data = array()){
		$cfg = &$this->cfgs[$key];	
		if($mode){//�����б���������
			$this->titles[$key] = '����';
		}else{ 
			$ccid1 = $data[$key];
			$coclasses1 = cls_cache::Read('coclasses',1);
			$re = isset($coclasses1[$ccid1]) ? $coclasses1[$ccid1]['title'] : (empty($ccid1) ? '(��վ)' : $ccid1);
			return $re;
		}
	}
	
	//���� Ӷ��(�ɽ�����)״̬�²���
	protected function user_fxyongjin($mode = 0,$data = array()){
		$key = substr(__FUNCTION__,5);
		$cfg = &$this->cfgs[$key];
		isset($cfg['view']) || $cfg['view'] = 'S';
		if($mode){//�����б���������
			$cfg['title'] = 'Ӷ��(Ԫ)';
			$this->titles[$key] = $this->top_title($key,$cfg);
		}else{//�����б�������	
			$status = $data['status'];
			$okayj = $status ==3 ? $data['okayj'] : '<span style="color:#999">0</span>';
			return "$okayj";
		}
	}
	
	//���� ¥������s
	protected function user_fxlpnames($mode = 0,$data = array()){
		$key = substr(__FUNCTION__,5);
		$cfg = &$this->cfgs[$key];
		isset($cfg['view']) || $cfg['view'] = 'S';
		if($mode){//�����б���������
			$cfg['title'] = '�Ƽ�¥��';
			$this->titles[$key] = $this->top_title($key,$cfg);
		}else{//�����б�������
			$aids = $data['aids'];
			$aida = explode(',',$aids);
			$okaid = $data['okaid'];
			$status = $data['status'];
			$slps = ''; 
			foreach($aida as $aid){
				if(empty($aid)) continue;
				$pinfo = $this->getPInfo('a',$aid,1);
				if(!empty($pinfo['lpmc'])){
					$slps .= (empty($slps) ? '' : ' , ').(($status=='3' && $okaid==$aid) ? "<span style='color:#00F'>{$pinfo['lpmc']}</span>" : $pinfo['lpmc']);
				}
			}
			$slps || $slps = str_replace(array('(,',',)'),array('',''),"<span style='color:#999' title='¥��ID'>($aids)</span>");
			return "$slps";
		}
	}
	
	//����ʱ�䷽��(ԭ��ʱ��(dbkey)����offset)
	//fmt:ʱ�䷽���ĸ�ʽ������
	//showEnd:������ʱ�䷽ʽ������ʾ(����ɫ����ʾ<����>), Ĭ��enddate���˷�ʽ��ʾ
	protected function type_udate($key = '',$mode = 0,$data = array()){
		$cfg = &$this->cfgs[$key];	
		$dbkey = $cfg['dbkey'];
		$offset = $cfg['offset'];
		if($mode){//�����б���������
			$arr = array('cucreate' => '���ʱ��',);
			if(empty($cfg['title']) && isset($arr[$key])) $cfg['title'] = $arr[$key];
			$this->titles[$key] = $this->top_title($key,$cfg);
		}else{
			// enddateĬ�ϰ�showEnd��ʽ��ʾ����ʾ<����>, �����ɫ��ʾ�������
			$showEnd = isset($cfg['showEnd']) ? $cfg['showEnd'] : ($dbkey=='enddate' ? 1 : 0);
			$timestamp = TIMESTAMP;
			$null = isset($cfg['empty']) ? $cfg['empty'] : ($showEnd ? '&lt;����&gt;' : '-');
			$fmt = isset($cfg['fmt']) ? $cfg['fmt'] : 'Y-m-d';
			$sval = date($fmt,intval($data[$dbkey]+$offset));
			if($showEnd){
				$cval = date($fmt,$timestamp);
				if($cval>$sval){ $sval = "<span style='color:#FF0000'>$sval</span>"; } //�Ѿ�����:��ɫ
				elseif($cval==$sval){ $sval = "<span style='color:#0000FF'>$sval</span>"; } //�������:��ɫ
			}
			return empty($data[$dbkey]) ? $null : $sval;
		}
	}
		
	//ѡ��mid     ����¥�̶����б�
	protected function user_selectmid($mode = 0,$data = array()){
		$key = substr(__FUNCTION__,5);
		$cfg = &$this->cfgs[$key];
		if(empty($cfg['width'])) $cfg['width'] = 30;
		isset($cfg['view']) || $cfg['view'] = 'S';
		if($mode){//�����б���������
			if(empty($cfg["title"])) $cfg['title'] = "<input class=\"checkbox\" type=\"checkbox\" name=\"chkall\" onclick=\"checkall(this.form,'selectid','chkall')\">";
			$this->titles[$key] = $this->top_title($key,$cfg);
		}else{//�����б�������
			return "<input class=\"checkbox\" type=\"checkbox\" name=\"selectid[$data[mid]]\" value=\"$data[mid]\">";
		}
	}
	
	protected function user_xingbie($mode = 0,$data = array()){
		$key = substr(__FUNCTION__,5);
		$cfg = &$this->cfgs[$key];
		if(empty($cfg['width'])) $cfg['width'] = 30;
		isset($cfg['view']) || $cfg['view'] = 'S';
		if($mode){//�����б���������
			$this->titles[$key] =  $this->top_title($key,$cfg);
		}else{
			$re = empty($data[$key]) ? "����":($data[$key] == 1? "��":"Ů");			
			return $re;
		}
	}
    //��Դ�ٱ����б��еľٱ�����
    protected function user_leixing($mode = 0,$data = array()){
		$key = substr(__FUNCTION__,5);
		$cfg = &$this->cfgs[$key];
        $info = cls_cache::Read("cufields",4);
        $lxstr = $info['leixing']['innertext'];
		if(empty($cfg['width'])) $cfg['width'] = 30;
		isset($cfg['view']) || $cfg['view'] = 'S';
		if($mode){//�����б���������
			$this->titles[$key] =  $this->top_title($key,$cfg);
		}else{
			$re = '';
            if(!empty($lxstr)){
                $lxarr = explode("\n",$lxstr);
                $arr = array();
			    foreach($lxarr as $v){
					$temparr = explode('=',str_replace(array("\r","\n"),'',$v));
					$temparr[1] = isset($temparr[1]) ? $temparr[1] : $temparr[0];
					$arr[$temparr[0]] = $temparr[1];
				}
                $re = $arr[$data[$key]];
            }
			return $re;
		}
	}
 
    /**
     * ί�з�Դ�е�С��������ʾ����Ҫ�����Ƿ������С��������ʱС����
     */
    protected function user_ex_subject($mode = 0,$data = array()){
		$key = substr(__FUNCTION__,5);
		$cfg = &$this->cfgs[$key];
		if($mode){//�����б���������
			empty($cfg['side']) && $cfg['side'] = 'L';
			!isset($cfg['view']) && $cfg['view'] = 'S';
			if(empty($cfg['title'])) $cfg['title'] = '����';
			$this->titles[$key] = $this->top_title($key,$cfg);
		}else{//�����б�������
            if(!empty($data[$key])){
    			$re = (!empty($data['thumb']) ? '<font style="color:red">ͼ&nbsp;</font>' : '');
    			$addno = empty($cfg['addno']) ? 0 : max(0,intval($cfg['addno']));
    			$url = '';
    			if(empty($cfg['url'])){
    				if(!empty($cfg['mc'])){  //��Ա�ռ�    
    					cls_ArcMain::Url($data,-1);
    					$url = $data['marcurl'];
    				}
    				else $url = cls_ArcMain::Url($data,$addno);
    			}elseif($cfg['url'] == '#'){  // ����Ҫurl����
    				if(!empty($data['color'])) $re .= "<span style=\"color:{$data['color']}\">";
    				$len = empty($cfg['len']) ? 40 : $cfg['len'];
    				if(!empty($data['thumb'])) $len -= 4;
    				$re .= htmlspecialchars(cls_string::CutStr($data['subject'],$len))."</span>";
    				return $re;
    			}else $url = key_replace($cfg['url'],$data); //�����Զ���url��ʽ
    			$re .= "<a ".(isset($cfg['aclass']) ? "class='$cfg[aclass]'" : "class='scol_subject'")." href=\"$url\" target=\"_blank\"";
    			
    			if(!empty($data['color'])) $re .= " style=\"color:{$data['color']}\"";
    			
    			$len = empty($cfg['len']) ? 40 : $cfg['len'];
    			if(!empty($data['thumb'])) $len -= 4;
    			$re .= " title=\"".htmlspecialchars($data[$key])."\">".htmlspecialchars(cls_string::CutStr($data[$key],$len))."</a>";
            }else{
                $re = "<font color='#999'>(��С������)</font>";          
            }
			return $re;
		}
	}
    
    /**
     * ί�з�Դ�е�С��������ʾ����Ҫ�����Ƿ������С��������ʱС����
     * $data['cu_chid']  ��Դ��sql��ѯʱ cu.chid as cu_chid
     */
    protected function user_wtlx($mode = 0,$data = array()){
		$key = substr(__FUNCTION__,5);
		$cfg = &$this->cfgs[$key];
		if(empty($cfg['width'])) $cfg['width'] = 30;
		isset($cfg['view']) || $cfg['view'] = 'S';    
		if($mode){//�����б���������
            empty($cfg['side']) && $cfg['side'] = 'C';
			if(empty($cfg['title'])) $cfg['title'] = '����';
			$this->titles[$key] = $this->top_title($key,$cfg);
		}else{//�����б�������
            $re = $data['cu_chid']==2?'����':'����';
			return $re;
		}
	}
    
    /**
     * ��Ʒ�����б��еĴ���״̬
     */
	protected function user_state($mode = 0,$data = array()){
		$key = substr(__FUNCTION__,5);
		$cfg = &$this->cfgs[$key];
		if(empty($cfg['width'])) $cfg['width'] = 30;
		isset($cfg['view']) || $cfg['view'] = 'S';
		if($mode){//�����б���������
			$this->titles[$key] =  $this->top_title($key,$cfg);
		}else{
			$re = empty($data[$key]) ? "δ����":"�Ѵ���";			
			return $re;
		}
	}

    /**
     * �ʴ�����
     */
	protected function user_ask_type($mode = 0,$data = array()){
		$key = substr(__FUNCTION__,5);
		$cfg = &$this->cfgs[$key];
		if(empty($cfg['width'])) $cfg['width'] = 30;
		isset($cfg['view']) || $cfg['view'] = 'S';
		if($mode){//�����б���������
			$this->titles[$key] =  $this->top_title($key,$cfg);
		}else{
			$re = (empty($data['toaid']) && empty($data['tocid']) ? '�ش�' : (!empty($data['toaid']) ? '����' : ($data['mid'] == $data['twmid'] ? '׷��' : '����')));		
			return $re;
		}
	}

    /**
     * ��Դί���е�ί��״̬
     */
	protected function user_entrusted_state($mode = 0,$data = array()){
		$key = substr(__FUNCTION__,5);
		$cfg = &$this->cfgs[$key];
		if(empty($cfg['width'])) $cfg['width'] = 30;
		isset($cfg['view']) || $cfg['view'] = 'S';
		if($mode){//�����б���������
            if(empty($cfg['title'])) $cfg['title'] = 'ί��״̬';
        	$this->titles[$key] =  $this->top_title($key,$cfg);
		}else{
            $statusstr = '';
            switch($data['jjrstatus']){
                case 1:
                    $statusstr = "<font color=\"#FF00FF\">�Ѿܾ�ί��</font>";
                    break;
                case 2:
                    $statusstr = "<font color=\"#006600\">�ѽ���ί��</font>";
                    break;
                default:
                    $statusstr = "<font color=\"#FF0000\">�ȴ�����</font>"; 
                    break;                
            }
			return $statusstr;
		}
	}

    /**
     * ��Դί���е�С��ͼ
     */
	protected function user_xqimg($mode = 0,$data = array()){
		$key = substr(__FUNCTION__,5);
		$cfg = &$this->cfgs[$key];
        $mconfigs = cls_cache::Read('mconfigs');
        $cms_abs  = $mconfigs['cms_abs'];
  
		if(empty($cfg['width'])) $cfg['width'] = 30;
		isset($cfg['view']) || $cfg['view'] = 'S';
		if($mode){//�����б���������
            if(empty($cfg['title'])) $cfg['title'] = 'С��ͼƬ';
        	$this->titles[$key] =  $this->top_title($key,$cfg);
		}else{         
			$row = $this->db->select('thumb')->from('#__archives15 a')
              ->where("a.aid = $data[pid]")
              ->limit(1)        
              ->exec()->fetch();
            $imgpath = empty($row['thumb'])?'images/common/nopic.gif':trim($row['thumb']);            
            return "<img width=\"125\" height=\"75\" src=".$cms_abs.$imgpath.">";
		}
	}


    /**
     * ��Դί���еĲ鿴��Ϣ
     */
	protected function user_connectinfo($mode = 0,$data = array()){
		$key = substr(__FUNCTION__,5);
		$cfg = &$this->cfgs[$key];
        $mconfigs = cls_cache::Read('mconfigs');
        $cms_abs  = $mconfigs['cms_abs'];
  
		if(empty($cfg['width'])) $cfg['width'] = 30;
		isset($cfg['view']) || $cfg['view'] = 'S';
		if($mode){//�����б���������
            if(empty($cfg['title'])) $cfg['title'] = '��Ϣ';
        	$this->titles[$key] =  $this->top_title($key,$cfg);
		}else{
            $re = "<a onclick=\"return floatwin('open_viewweituo',this)\" href=\"?action=delegations&cid=$data[cid]\">".($data['owerstatus'] == 0 && $data['jjrstatus'] == 0 ? "�鿴��Ϣ������":"�鿴��Ϣ")."</a>";            
            return $re;
		}
	}
    
     /**
     * �����˻�Ա���ģ����ҵ����Ե�  ����������ʾ
     */
	protected function user_mname($mode = 0,$data = array()){
		$key = substr(__FUNCTION__,5);
		$cfg = &$this->cfgs[$key];
        $mconfigs = cls_cache::Read('mconfigs');
        $cms_abs  = $mconfigs['cms_abs'];
  
		if(empty($cfg['width'])) $cfg['width'] = 30;
		isset($cfg['view']) || $cfg['view'] = 'S';
		if($mode){//�����б���������
            if(empty($cfg['title'])) $cfg['title'] = '������';
        	$this->titles[$key] =  $this->top_title($key,$cfg);
		}else{
		    $url = cls_Mspace::IndexUrl($data);
			$mnamestr = $data['mid'] ? "<a href=\"$url\" target=\"_blank\">$data[mname]</a>" : $data['mname'];
            return $mnamestr;
		}
	}
    
    
   	/**
	 *��Ŀ����
	 */
	protected function user_caid($mode = 0,$data = array()){
		$key = substr(__FUNCTION__,5);
		$cfg = &$this->cfgs[$key];
		if($mode){
			if(empty($cfg['title'])) $cfg['title'] = '��Ŀ';
			$this->titles[$key] =  $this->top_title($key,$cfg);
		}else{
			$re = ($catalog = cls_cache::Read('catalog',$data['caid'])) ? $catalog['title'] : '';
			$re || $re = isset($cfg['empty']) ? $cfg['empty'] : '-';
			return $re;
		}
	}

}
