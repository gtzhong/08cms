<?php
class cls_arcops extends cls_arcopsbase{
	
	
	// limit:�޶�(��),days�ϼ�����(��),	
	// 2,3,9,10,108 ģ�ͣ�days��ȡ��̨���ò���
	protected function user_valid($mode = 0){
		global $timestamp; 
		$key = substr(__FUNCTION__,5);
		$cfg = &$this->cfgs[$key];
		if(!$mode){//��ʼ��
			$cfg['bool'] = 1;
		}elseif($mode == 1){//��ʾ
			if(empty($cfg['title'])) $cfg['title'] = '�ϼ�';
			return $this->input_checkbox($key,$cfg['title'],1);
		}elseif($mode == 2){//����
			//$chid = $this->arc->
			//print_r($this->arc);
			if(empty($cfg['days'])){
				$days = -1; //Ĭ�ϣ����ã�
				$_arc = $this->arc->archive;
				if(in_array($_arc['chid'],array(2,3,9,10,108))){
					if($_arc['chid']==108){ //��Ƹ
						$mconfigs = cls_cache::Read('mconfigs');
						$zpvalid = $mconfigs['zpvalid'];
						$days = empty($zpvalid) ? 30 : max(1,intval($zpvalid));
					}else{ // ��Դ/����
						$exconfigs = cls_cache::cacRead('exconfigs',_08_EXTEND_SYSCACHE_PATH);
						$_cfg = $exconfigs['fanyuan']; //����
						$_key = in_array($_arc['chid'],array(2,3)) ? 'fyvalid' : 'xqvalid';
						$this->arc->arcuser(); 
						$_gid = @$this->arc->auser->info['grouptype14']; 
						$_gid = empty($_gid) ? 0 : $_gid;
						$days = empty($_cfg[$_gid][$_key]) ? 30 : max(1,intval($_cfg[$_gid][$_key]));
					}
				}
				//die($chid); //  echo $_gid;
			}else{
				$days = max(1,intval($cfg['days']));
			}
			//$days = empty($cfg['days']) ? -1 : max(1,intval($cfg['days']));
			$this->arc->setend($days);
		}
	}
	
	protected function user_leixing($mode = 0){
		$chid = $this->A['chid'];
		$key = substr(__FUNCTION__,5);
		$cfg = &$this->cfgs[$key];
		if(!$mode){//��ʼ��
		}elseif($mode == 1){//��ʾ
			$na = array('0'=>'¥����С��','1'=>'¥��','2'=>'С��');
		 	trbasic('����¥��С������&nbsp;<input type="checkbox" value="1" name="arcdeal[leixing]" class="checkbox">','','<select style="vertical-align: middle;" name="arcleixing">'.makeoption($na).'</select>','');
		}elseif($mode == 2){//����
			$this->arc->updatefield('leixing',$GLOBALS[$this->A['opre'].$key],"archives_$chid");
		}
	}
	/*
     * ���͹�˾�¾����˵ķ�Դת��
     * */
    protected function user_transfer($mode = 0){
		$namearr=$this->cfgs['transfer']['namearr'];
		if(count($namearr)<3) return false;
		if($mname=cls_env::GetG('mname')){ unset($namearr[$mname]);}else{return false;}
		$key = substr(__FUNCTION__,5);
		$cfg = &$this->cfgs[$key];
		if(!$mode){//��ʼ��
		}elseif($mode == 1){//��ʾ
		    unset($namearr[0]); //ɾ����һ��ѡ��[0=-������-]
		 	trbasic('��Դת����&nbsp;<input type="checkbox" value="1" name="arcdeal[transfer]" class="checkbox">','','<select style="vertical-align: middle;" name="arctransfer">'.makeoption($namearr).'</select>','');	
		}elseif($mode == 2){//����
			$mid = $GLOBALS[$this->A['opre'].$key];
			if(empty($mid) || !isset($namearr[$mid])){ //Ϊ�ջ�mid���ھ��ù�˾�²�ִ��
				return;
			}
			$this->arc->updatefield('mid',$mid);
			$this->arc->updatefield('mname',$namearr[$mid]);
		}
	}
    /*
     * ���û���Ϊ��������
     * */
    protected function user_maintype($mode = 0){
        $key = substr(__FUNCTION__,5);
        //if(!$this->mc && !allow_op('acheck')) return $this->del_item($key);

        $cfg = &$this->cfgs[$key];
        if(!$mode){//��ʼ��
            $cfg['bool'] = 1;
        }elseif($mode == 1){//��ʾ
            if(empty($cfg['title'])) $cfg['title'] = '������������';
            return $this->input_checkbox($key,$cfg['title'],1);
        }elseif($mode == 2){//����
            if(empty($this->arc->aid)) return;
            if($this->arc->updatefield('zlhx',1)){
                $curuser = cls_UserMain::CurUser();
                $this->arc->updatefield('editorid',$curuser->info['mid']);
                $this->arc->updatefield('editor',$curuser->info['mname']);
                //$this->arc->updatedb();
            }
        }
    }

    /*
     * ȡ������Ϊ��������
     * */
    protected function user_unmaintype($mode = 0){
        $key = substr(__FUNCTION__,5);
        $cfg = &$this->cfgs[$key];
        if(!$mode){//��ʼ��
            $cfg['bool'] = 1;
        }elseif($mode == 1){//��ʾ
            if(empty($cfg['title'])) $cfg['title'] = 'ȡ����������';
            return $this->input_checkbox($key,$cfg['title'],1);
        }elseif($mode == 2){//����
            if($this->is_item('check')) return false;//����checkͬʱִ��
            if(empty($this->arc->aid)) return;
            if($this->arc->updatefield('zlhx',0)){
                $curuser = cls_UserMain::CurUser();
                $this->arc->updatefield('editorid',$curuser->info['mid']);
                $this->arc->updatefield('editor',$curuser->info['mname']);
            }
        }
    }


    //��չ�����е����Ƽ�����Ϊ��ͨ��Ա�����ķ�Դ��Ϣ�����Թ�
	protected function one_item($key,$mode = 0){
		global $db,$tblprefix;
		$curuser = cls_UserMain::CurUser();
		$re = $this->call_method("user_$key",array($mode));//���Ʒ���
		if($re == 'undefined'){
			if('ccid' == substr($key,0,4)){
				// (2014-10-14:������ҵ�ز�ʱȷ��:�����Ƽ�:����Ա����ȥ����)
				if(substr($key,4) == 19 && $mode == 2) {
					$_mchid = $db->result_one("SELECT mchid FROM {$tblprefix}members WHERE mid = ".$this->arc->archive['mid']);			
					if($_mchid == 1) return;
				}				
				$re = $this->type_ccid($key,$mode);
			}elseif('push' == substr($key,0,4)){
				$re = $this->type_push($key,$mode);
			}
		}
		return $re;
	}

//    ���:����¥������״̬�����ָ��������Ϊ9999
    protected function type_ccid($key,$mode = 0){

        $cotypes = cls_cache::Read('cotypes');
        if(!($coid = max(0,intval(str_replace('ccid','',$key)))) || empty($cotypes[$coid]) || !in_array($coid,$this->A['coids']) || $cotypes[$coid]['self_reg']) return $this->del_item($key);

        $cfg = &$this->cfgs[$key];
        if(!$mode){//��ʼ��
        }elseif($mode == 1){//��ʾ
            $v = $cotypes[$coid];
            if(empty($cfg['title'])) $cfg['title'] = "����{$v['cname']}";
            isset($v['asmode']) ||  $v['asmode']='';
            isset($v['emode']) || $v['emode']='';
            $na = array('coid' => $coid,'chid' => $this->A['chid'],'max' => $v['asmode'],'emode' => $v['emode'],'evarname' => "{$this->A['opre']}{$key}date",);
            foreach($cfg as $k => $v){//���±�������ͨ��$cfg����
                if(in_array($k,array('chid','addstr','max','emode','ids','guide',))) $na[$k] = $v;
            }
            $na['addstr'] = '-ȡ��-';
            tr_cns($this->input_checkbox($key,$cfg['title']),"{$this->A['opre']}$key",$na,1);
        }elseif($mode == 2){//����
            if(!isset($cfg['limit'])||empty($GLOBALS[$this->A['opre'].$key])){
                $this->arc->set_ccid(@$GLOBALS["mode_".$this->A['opre'].$key],$GLOBALS[$this->A['opre'].$key],$coid,@$GLOBALS[$this->A['opre'].$key.'date']);
                if($key == 'ccid18' && $GLOBALS[$this->A['opre'].$key] == 198){
                    global $db,$tblprefix;
                    $aid = $this->arc->archive['aid'];
                    //$GLOBALS[$this->A['opre'].$key];die;
                    $db->query("UPDATE {$tblprefix}archives15 SET `vieworder` = 9999 WHERE aid = $aid");
                }
            }else{ //��ϵ�޶����
                $do = 1;
                if(!isset($this->recnt['reccids'][$coid])){
                    $this->recnt['reccids'][$coid]['title'] = $cfg['title'];
                    $this->recnt['reccids'][$coid]['do'] = 0; //������
                    $this->recnt['reccids'][$coid]['skip'] = 0; //������
                }
                if($this->recnt['reccids'][$coid]['do']>=$cfg['limit']){ //��������
                    $this->recnt['reccids'][$coid]['skip']++;
                    return false;
                }

                if($do){
                    $this->recnt['reccids'][$coid]['do']++;
                    $this->arc->set_ccid(@$GLOBALS["mode_".$this->A['opre'].$key],$GLOBALS[$this->A['opre'].$key],$coid,@$GLOBALS[$this->A['opre'].$key.'date']);
                }
            }
        }
    }
}

?>
