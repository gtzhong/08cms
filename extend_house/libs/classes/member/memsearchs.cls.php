<?php
class cls_memsearchs extends cls_memsearchsbase{
    //��������ʧЧ���߼�������/VIPװ�޹�˾/VIPƷ���̼ң�
   	protected function user_gtype_enddate(){
		$key = substr(__FUNCTION__,5);
		$cfg = &$this->cfgs[$key];
		$this->init_item($key,'int+',1);
        $_field = 'grouptype'.$cfg['groupnum'].'date';
		if($this->nvalue[$key]){//����Դ���wherestr
			global $timestamp;
			$this->wheres[$key] = $cfg['pre'].$_field.">='".$timestamp."'".' AND '.$cfg['pre'].$_field."<='".($timestamp + 86400 * $this->nvalue[$key])."'";
		}
		if(empty($cfg['hidden'])){
			$title = empty($cfg['title']) ? "����ʧЧ" : $cfg['title'];
			$this->htmls[$key] = $this->input_text($key,$this->nvalue[$key],'ע��ʱ��',2).$title;
		}else $this->htmls[$key] = $this->input_hidden($key,$this->nvalue[$key]);
	}
}
