<?php
defined('M_COM') || exit('No Permission');
class cls_cuschs extends cls_cuschsbase{
	
	// ¥�̷����Ƽ��ͻ� �б� - �Ƽ�״̬
	protected function user_status(){
		$key = substr(__FUNCTION__,5);
		
		$field = $this->fields[$key]; 
		if(!$field || !in_array($field['datatype'],array('select','mselect','cacc',))) return $this->del_item($key);
		$cfg = &$this->cfgs[$key];
		$a_field = new cls_field;
		$field['issearch'] = 1;//ǿ��Ϊ�������ֶ�
		$val = isset($GLOBALS[$key]) ? $GLOBALS[$key] : '-1'; 
		$a_field->init($field,$val);
		$a_field->deal_search($cfg['pre']);
		if(!empty($a_field->ft)) $this->filters += $a_field->ft;
		if(!empty($a_field->searchstr)) $this->wheres[$key] = $a_field->searchstr;
		unset($a_field);
		if(empty($cfg['hidden'])){
			$sarr = cls_field::options_simple($field,array('blank' => '&nbsp; &nbsp; '));
			$title = empty($cfg['title']) ? "-{$field['cname']}-" : $cfg['title'];
			$this->htmls[$key] = $this->input_select($key,array('' => $title) + $sarr,$val);
		}else $this->htmls[$key] = $this->input_hidden($key,@$GLOBALS[$key]);
	}
	
    protected function user_leixing(){
		$key = substr(__FUNCTION__,5);
		$cfg = &$this->cfgs[$key];
		$this->init_item($key,'int',1);   
		if($this->nvalue[$key]){//����Դ���wherestr	
			$this->wheres[$key] = $cfg['pre']."leixing = '".$this->nvalue[$key]."'";
		}
        if(empty($cfg['hidden'])){
            $info = cls_cache::Read("cufields",4);
            $lxstr = $info['leixing']['innertext'];
            if(!empty($lxstr)){
                $lxarr = explode("\n",$lxstr);
                $arr = array();
			    foreach($lxarr as $v){
					$temparr = explode('=',str_replace(array("\r","\n"),'',$v));
					$temparr[1] = isset($temparr[1]) ? $temparr[1] : $temparr[0];
					$arr[$temparr[0]] = $temparr[1];
				}
            }
            $title = empty($cfg['title']) ? "�ٱ�����-" : $cfg['title'];
    		$this->htmls[$key] = $this->input_select($key,$arr,$this->nvalue[$key]);
		}else{
		    $this->htmls[$key] = $this->input_hidden($key,$this->nvalue[$key]); 
		}
    }
    
    protected function user_state(){
		$key = substr(__FUNCTION__,5);
		$cfg = &$this->cfgs[$key];
		$this->init_item($key,'int',1);       
		if($this->nvalue[$key] != -1 ){//����Դ���wherestr	
  	         $this->wheres[$key] = $cfg['pre']."state = '".$this->nvalue[$key]."'";
		}
        if(empty($cfg['hidden'])){
            $arr = array(-1=>'����״̬',0=>'δ����',1=>'�Ѵ���');           
    		$this->htmls[$key] = $this->input_select($key,$arr,$this->nvalue[$key]);
		}else{
		    $this->htmls[$key] = $this->input_hidden($key,$this->nvalue[$key]); 
		}
    }      
    
    //ί�з�Դ��ί��״̬
    protected function user_jjrstatus(){
		$key = substr(__FUNCTION__,5);
		$cfg = &$this->cfgs[$key];
		$this->init_item($key,'int',1);       
		if($this->nvalue[$key] != -1 && $this->nvalue[$key] != 0){//����Դ���wherestr
            if($this->nvalue[$key] == 3){
                $this->wheres[$key] = $cfg['pre']."owerstatus = '0' AND ".$cfg['pre']."jjrstatus = '0'";
            }else{
                $this->wheres[$key] = $cfg['pre']."jjrstatus = '".$this->nvalue[$key]."'";
            }
		}
        if(empty($cfg['hidden'])){
            $arr = array(-1=>'ί��״̬',1=>'�Ѿܾ�ί��',2=>'�ѽ���ί��',3=>'�ȴ�����');           
    		$this->htmls[$key] = $this->input_select($key,$arr,$this->nvalue[$key]);
		}else{
		    $this->htmls[$key] = $this->input_hidden($key,$this->nvalue[$key]); 
		}
    } 
	  
    //¥�̰��������
    protected function user_lpdyfl($mode = 0,$data = array()){
		$key = substr(__FUNCTION__,5);
		$cfg = &$this->cfgs[$key];
		$this->init_item($key,'int',1);       	
		if($this->nvalue[$key] !== 0){
			if($this->nvalue[$key] == 1) $this->wheres[$key] = $cfg['pre']."dyfl LIKE "."'".$this->nvalue[$key]."\t%'";			  
		    if($this->nvalue[$key] == 5) $this->wheres[$key] = $cfg['pre']."dyfl LIKE "."'%\t".$this->nvalue[$key]."'";
			if($this->nvalue[$key] !== 1 && $this->nvalue[$key] !== 5)$this->wheres[$key] = $cfg['pre']."dyfl LIKE "."'%\t".$this->nvalue[$key]."\t%'";
		 }
        if(empty($cfg['hidden'])){
            $arr = array(0=>'-�������-',1=>'���֪ͨ',2=>'�Ż�֪ͨ',3=>'����֪ͨ',4=>'���¶�̬',5=>'���͵��ֻ�');           
    		$this->htmls[$key] = $this->input_select($key,$arr,$this->nvalue[$key]);
		}else{
		    $this->htmls[$key] = $this->input_hidden($key,$this->nvalue[$key]); 
		}
	}
	
    /**
     * ���������Ƿ�����ʵ�Ľ��������籨����Ϣ����̨��������ӣ����Ǻ�̨��ӵĶ��Ǽ���Ϣ�� 
     */
    protected function user_istrue(){
		$key = substr(__FUNCTION__,5);
		$cfg = &$this->cfgs[$key];
		$this->init_item($key,'int',1);         
		if($this->nvalue[$key] != -1 ){//����Դ���wherestr	
  	         $this->wheres[$key] = $cfg['pre']."istrue = '".$this->nvalue[$key]."'";
		}   
        $arr = array(-1=>'--�����Ϣ--',0=>'�����Ϣ',1=>'��ʵ��Ϣ');           
		$this->htmls[$key] = $this->input_select($key,$arr,$this->nvalue[$key]);
    }      
}
