<?php
class cls_arcsearchs extends cls_arcsearchsbase{
	protected function user_mj(){ //���
		$key = substr(__FUNCTION__,5); 
		$this->user_other_fields($key);
	}
	
	protected function user_zj(){ //�۸�
		$key = substr(__FUNCTION__,5); 
		$this->user_other_fields($key);
	}
		
	protected function user_szlc(){ //¥��
		$key = substr(__FUNCTION__,5);
		$this->user_other_fields($key);
	}	
	
	protected function user_other_fields($key){ //��� //�۸� //¥��
		$keyfr = "{$key}fr"; $keyto = "{$key}to"; 
		$cfg = &$this->cfgs[$key];
		$this->init_item($keyfr,'int',1);
		$this->init_item($keyto,'int',1);
		if($this->nvalue[$keyfr]){
			$this->wheres[$keyfr] = (empty($cfg['pre'])?"a.":$cfg['pre']).$key.">='".$this->nvalue[$keyfr]."' ";
		}
		if($this->nvalue[$keyto]){
			$this->wheres[$keyto] = (empty($cfg['pre'])?"a.":$cfg['pre']).$key."<='".$this->nvalue[$keyto]."' ";
		} 
		$_keys_title = '';
		switch($key){
			case 'mj':
				$_keys_title = '���';				
			break;
			case 'szlc':
				$_keys_title = '¥��';				
			break;
			default:
				$_keys_title = '�۸�';				
			break;
		}
		if(empty($cfg['hidden'])){
			$html = $_keys_title."<input class=\"text\" name=\"".$key."fr\" type=\"text\" value=\"".$this->nvalue[$keyfr]."\" size=\"2\" style=\"vertical-align: middle;\">";
			$html .= "~<input class=\"text\" name=\"".$key."to\" type=\"text\" value=\"".$this->nvalue[$keyto]."\" size=\"2\" style=\"vertical-align: middle;\">";
			$this->htmls[$key] = $html;
		}
	}	
	
	
	
	
	//ɸѡ����/�н�ķ�Դ��Ϣ
	protected function user_mchid(){
		global $tblprefix;
		$key = substr(__FUNCTION__,5);
		$cfg = &$this->cfgs[$key];
		$this->init_item($key,'int-1',1);
		if($this->nvalue[$key] != -1){//����Դ���wherestr			
			if($this->nvalue[$key] == 1){
				$this->wheres[$key] = "({$cfg['pre']}mid IN(SELECT mid FROM {$tblprefix}members_1) OR {$cfg['pre']}mid='0')";
			}elseif($this->nvalue[$key] == 2){
				$this->wheres[$key] = "{$cfg['pre']}mid IN(SELECT mid FROM {$tblprefix}members_2)";
			}
		}
		if(empty($cfg['hidden'])){
			$title = empty($cfg['title']) ? "-��Ա����-" : $cfg['title'];
			$this->htmls[$key] = $this->input_select($key,array('-1' => $title,'1' => '����','2' => '�н�',),$this->nvalue[$key]);
		}else $this->htmls[$key] = $this->input_hidden($key,$this->nvalue[$key]);
	}
	
	
	//ɸѡ��Ա/��������Ա��������Ƹ��Ϣ
	protected function user_isfounder(){
		global $tblprefix;
		$key = substr(__FUNCTION__,5);
		$cfg = &$this->cfgs[$key];
		$this->init_item($key,'int-1',1);
		if($this->nvalue[$key] != -1){//����Դ���wherestr			
			if($this->nvalue[$key] == 1){
				$this->wheres[$key] = " (b.grouptype2 != '0' or b.isfounder = '1') ";
			}elseif($this->nvalue[$key] == 2){
				$this->wheres[$key] = " b.grouptype2 = '0' AND b.isfounder = '0'  ";
			}
		}
		if(empty($cfg['hidden'])){
			$title = empty($cfg['title']) ? "-��Ա����-" : $cfg['title'];
			$this->htmls[$key] = $this->input_select($key,array('-1' => $title,'1' => '����Ա','2' => '��Ա',),$this->nvalue[$key]);
		}else $this->htmls[$key] = $this->input_hidden($key,$this->nvalue[$key]);
	}
	
	//ɸѡ�ѱ�ԤԼ�ķ�Դ��Ϣ
	protected function user_yuyue(){
		$key = substr(__FUNCTION__,5);
		$cfg = &$this->cfgs[$key];
		$this->init_item($key,'int-1');
		if(empty($cfg['hidden'])){
			$title = empty($cfg['title']) ? "-ԤԼ-" : $cfg['title'];
			$this->htmls[$key] = $this->input_select($key,array('-1' => $title,'0' => 'δԼ','1' => '��Լ',),$this->nvalue[$key]);
		}else $this->htmls[$key] = $this->input_hidden($key,$this->nvalue[$key]);
	}
	
	protected function user_orderby_e(){//���Դ���$cfg['options']
		$key = substr(__FUNCTION__,5);
		$cfg = &$this->cfgs[$key];
		$this->init_item($key,'int',1);
		if(empty($cfg['options'])){
			$title = empty($cfg['title']) ? "-����ʽ-" : $cfg['title'];
			$cfg['options'] = array(
				0 => array($title,$this->A['orderby']),
				1 => array('�������',$cfg['pre'].'clicks DESC'),
				2 => array('��ˢ��ʱ��',$cfg['pre'].'refreshdate DESC'),
				3 => array('�����ʱ��',$cfg['pre'].'createdate DESC'),
				4 => array('��ָ������',$cfg['pre'].'ccid41 DESC,a.vieworder,a.aid DESC'),
			);
		}
		$sarr = array();
		foreach($cfg['options'] as $k => $v){
			$sarr[$k] = $v[0];
			if($this->nvalue[$key] == $k) $this->orderby = $v[1];
		}
		if(empty($cfg['hidden'])){
			$this->htmls[$key] = $this->input_select($key,$sarr,$this->nvalue[$key]);
		}else $this->htmls[$key] = $this->input_hidden($key,$this->nvalue[$key]);
	}

	
	
}
