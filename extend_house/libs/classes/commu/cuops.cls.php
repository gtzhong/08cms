<?php
defined('M_COM') || exit('No Permission');
class cls_cuops extends cls_cuopsbase{
	//����¥�̶��ĵ�ɾ��
	protected function user_del_lpdy($mode = 0){
		$key = substr(__FUNCTION__,5);
		if(!$this->mc && !allow_op('mdel')) return $this->del_item($key);
		$cfg = &$this->cfgs[$key];
		if(!$mode){//��ʼ��
			$cfg['bool'] = 1;
		}elseif($mode == 1){//��ʾ
			if(empty($cfg['title'])) $cfg['title'] = 'ɾ������';
			return $this->input_checkbox($key,$cfg['title'],1,'onclick="deltip()"');
		}elseif($mode == 2){//����		
			$this->db->delete($this->table())->where('mid='.$this->actcu['cid'])->exec();
		}
	}
	
	//����¥�̶��ĵķ����ʼ�
	protected function user_send_email($mode = 0){
		$key = substr(__FUNCTION__,5);
		if(!$this->mc && !allow_op('mdel')) return $this->del_item($key);
		$cfg = &$this->cfgs[$key];
		if(!$mode){//��ʼ��
			$cfg['bool'] = 1;
		}elseif($mode == 1){//��ʾ
			if(empty($cfg['title'])) $cfg['title'] = '�����ʼ�';
			return $this->input_checkbox($key,$cfg['title'],1);
		}elseif($mode == 2){//����		
			$timestamp = TIMESTAMP; 
			$tblprefix = cls_env::getBaseIncConfigs('tblprefix');	
			$mid = 	$this->actcu;
			$content = '';
			$modearr = array('new' => '�·���̬','old' => '���ַ�Դ','rent' => '���ⷿԴ',);
			$query = $this->db->query("SELECT cu.*,cu.createdate AS ucreatedate,a.initdate,a.caid,a.chid,a.customurl,a.nowurl,a.subject FROM {$tblprefix}commu_gz cu INNER JOIN {$tblprefix}".atbl(4)." a ON a.aid=cu.aid WHERE cu.mid='$mid'");
			while($r = $this->db->fetch_array($query)){
				cls_ArcMain::Url($r,-1);
				$content .= "\n[$r[subject]]";
				foreach($modearr as $k => $v){
					$url = $k == 'new' ? $r['arcurl'] : ($k == 'old' ? $r['arcurl8'].'&fang=mai' : $r['arcurl8'].'&fang=zhu');
					$r[$k] && $content .= "&nbsp; >><a href=\"$url\" target=\"_blank\">$v</a> ";
				}
			}
			if($content){ 
				$au = new cls_userinfo;
				$au->activeuser($mid);
				if(@mailto($au->info['email'],'dingyue_subject','dingyue_content',array('mid' => $mid,'mname' => $au->info['mname'],'content' => $content))){
					$this->db->query("UPDATE {$tblprefix}commu_gz SET senddate='$timestamp' WHERE mid='$mid'");		
				}
			}
		}
	}
    
    
    
   	//�����ʴ��ɾ��
	protected function user_deleteAnswer($mode = 0){
		$key = substr(__FUNCTION__,5);
		if(!$this->mc && !allow_op('mdel')) return $this->del_item($key);
		$cfg = &$this->cfgs[$key];
		if(!$mode){//��ʼ��
			$cfg['bool'] = 1;
		}elseif($mode == 1){//��ʾ
			if(empty($cfg['title'])) $cfg['title'] = 'ɾ��';
			return $this->input_checkbox($key,$cfg['title'],1,'onclick="deltip()"');
		}elseif($mode == 2){//����	
            $answertype = $cfg['answertype'];
            $chid = 106;
            $aid = $this->actcu['aid'];
            $cid = $this->actcu['cid'];   
            $tblprefix = cls_env::getBaseIncConfigs('tblprefix');          
		    if($answertype == 1){
				$this->db->query("UPDATE {$tblprefix}".atbl($chid)." set stat0=stat0-1 WHERE aid='$aid'");       
                $this->db->delete('#__commu_answers')->where("tocid = $cid")->exec();
			}
            $this->db->delete('#__commu_answers')->where("cid = $cid")->exec();
			//���Ҹ������Ƿ�����Ѵ𰸣�û�Ļ�����������Ϊδ����
            $bestAnswer = $this->db->select('COUNT(*) as num')->from('#__commu_answers')
              ->where("aid = $aid")
              ->_and("isanswer=1")
              ->exec()->fetch();
			if(empty($bestAnswer)){
                $this->db->update('#__archives22', array('ccid35' => '3035','answercid' => 0))->where("aid = $aid")->_and("ccid35=3036")->exec();
			}
		}
	}

    
   	//�����ʴ�����
	protected function user_checkAnswer($mode = 0){
		$key = substr(__FUNCTION__,5);
		if(!$this->mc && !allow_op('mdel')) return $this->del_item($key);
		$cfg = &$this->cfgs[$key];
		if(!$mode){//��ʼ��
			$cfg['bool'] = 1;
		}elseif($mode == 1){//��ʾ
			if(empty($cfg['title'])) $cfg['title'] = '���';
			return $this->input_checkbox($key,$cfg['title'],1);
		}elseif($mode == 2){//����		
            $answertype = $cfg['answertype'];
            $chid = 106;
            $aid = $this->actcu['aid'];
            $cid = $this->actcu['cid']; 
            $tblprefix = cls_env::getBaseIncConfigs('tblprefix');      
            if(empty($this->actcu['checked'])){
                $answertype == 1 && $this->db->query("UPDATE {$tblprefix}".atbl($chid)." set stat0=stat0+1 WHERE aid=$aid");
                $this->db->update('#__commu_answers', array('checked' => '1'))->where("cid = $cid")->exec();
            }	
		}
	}    

   	//�����ʴ�𰸽���
	protected function user_uncheckAnswer($mode = 0){
		$key = substr(__FUNCTION__,5);
		if(!$this->mc && !allow_op('mdel')) return $this->del_item($key);
		$cfg = &$this->cfgs[$key];
		if(!$mode){//��ʼ��
			$cfg['bool'] = 1;
		}elseif($mode == 1){//��ʾ
			if(empty($cfg['title'])) $cfg['title'] = '����';
			return $this->input_checkbox($key,$cfg['title'],1);
		}elseif($mode == 2){//����		
            $answertype = $cfg['answertype'];
            $chid = 106;
            $aid = $this->actcu['aid'];
            $cid = $this->actcu['cid'];         
            $tblprefix = cls_env::getBaseIncConfigs('tblprefix'); 
            $isBestAnswer = $this->actcu['isanswer']; 
            $isBestAnswer && cls_message::show('��Ѵ𰸲��ܽ���ֻ��ɾ����',axaction(1,M_REFERER));
            if(!empty($this->actcu['checked'])){
                $answertype == 1 && $this->db->query("UPDATE {$tblprefix}".atbl($chid)." set stat0=stat0-1 WHERE aid=$aid");
			    $this->db->update('#__commu_answers', array('checked' => '0'))->where("cid = $cid")->exec();
            }   
		}
	} 
    /*
     * ¥���������Ⱥ������
     * */
	protected function user_issms($mode = 0){
		$key = substr(__FUNCTION__,5);
		$cfg = &$this->cfgs[$key];
		$sms = new cls_sms();
		if(empty($this->cucfgs['issmshout']) || $sms->isClosed()) return;
		if(!$mode){//��ʼ��
		}elseif($mode == 1){//��ʾ
			trbasic('���Ͷ���&nbsp;<input type="checkbox" value="1" name="arcdeal[issms]" class="checkbox">','arcissms','','textarea',array('w'=>240,'h' => 80,'guide'=>'�ڴ������������,������180�֣����������70�����ڣ�����70���ַ���Լ��ÿ70�ֿ�һ�����ŷ���'));
		}elseif($mode == 2){//����		  
		   $smscon = $GLOBALS[$this->A['opre'].$key];
		   if($smscon== '') cls_message::show('�������ݲ���Ϊ��',axaction(1,M_REFERER));
		   $_tel = $this->actcu['sjhm'];
		   $msg = $sms->sendSMS($_tel,$smscon,'sadm');
		//cls_message::show((empty($msg) ? "���Žӿ�δ��" : ($msg[0] == 1 ? '֪ͨ�����ѷ���': '֪ͨ����δ����')),axaction(1,M_REFERER));
		}
	}
   	//����������Ѵ�
	protected function user_isanswer($mode = 0){
		$key = substr(__FUNCTION__,5);
		if(!$this->mc && !allow_op('mdel')) return $this->del_item($key);
		$cfg = &$this->cfgs[$key];
		if(!$mode){//��ʼ��
			$cfg['bool'] = 1;
		}elseif($mode == 1){//��ʾ
			if(empty($cfg['title'])) $cfg['title'] = '��Ѵ�';
			return $this->input_checkbox($key,$cfg['title'],1);
		}elseif($mode == 2){//����
            $aid = $this->actcu['aid'];
            $cid = $this->actcu['cid'];  
            $bestAnswer = $this->db->select('COUNT(*) as num')->from('#__commu_answers')
              ->where("aid = $aid")
              ->_and("isanswer=1")
              ->exec()->fetch();
            empty($this->actcu['checked']) && cls_message::show('������ˣ��ٽ�����Ѵ𰸲�����',axaction(1,M_REFERER));
            if(empty($bestAnswer['num'])){
                $this->db->update('#__commu_answers', array('isanswer' => '1'))->where("cid = $cid")->exec();
                $this->db->update('#__archives22', array('ccid35' => '3036','answercid' => $cid))->where("aid = $aid")->exec();        		
            }
		}
	}
    
   	//����ȡ����Ѵ�
	protected function user_noanswer($mode = 0){
		$key = substr(__FUNCTION__,5);
		if(!$this->mc && !allow_op('mdel')) return $this->del_item($key);
		$cfg = &$this->cfgs[$key];
		if(!$mode){//��ʼ��
			$cfg['bool'] = 1;
		}elseif($mode == 1){//��ʾ
			if(empty($cfg['title'])) $cfg['title'] = 'ȡ�����';
			return $this->input_checkbox($key,$cfg['title'],1);
		}elseif($mode == 2){//����	    
            $aid = $this->actcu['aid'];
            $cid = $this->actcu['cid'];  	
            $this->db->update('#__commu_answers', array('isanswer' => '0'))->where("cid = $cid")->exec();
            $this->db->update('#__archives22', array('ccid35' => '3035','answercid' => 0))->where("aid = $aid")->_and("ccid35=3036")->exec(); 
        }
	}  
    
   	//���ھٱ��𰸵�ɾ���������
	protected function user_deleteVicious($mode = 0){
		$key = substr(__FUNCTION__,5);
		if(!$this->mc && !allow_op('mdel')) return $this->del_item($key);
		$cfg = &$this->cfgs[$key];
		if(!$mode){//��ʼ��
			$cfg['bool'] = 1;
		}elseif($mode == 1){//��ʾ
			if(empty($cfg['title'])) $cfg['title'] = 'ɾ������';
			return $this->input_checkbox($key,$cfg['title'],1);
		}elseif($mode == 2){//����      
            $commu = cls_cache::Read('commu', $this->A['cuid']);
            $cid = $this->actcu['cid'];  	
            $mid = $this->actcu['mid'];
            $user = new cls_userinfo;
            if(!empty($mid)){
                $user->activeuser($mid);
                if(!$user->isadmin() && !empty($commu['ccurrency'])) $user->updatecrids(array(1=>-max(0,$commu['ccurrency'])),1,$commu['cname'].'�Ƕ�����Ϣ����������Ϣ��');
            }           
            $this->db->delete('#__commu_jbask')->where("cid = $cid")->exec();
            unset($user);
        }
	} 
    	

}
