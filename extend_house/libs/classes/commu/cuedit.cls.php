<?php
defined('M_COM') || exit('No Permission');
class cls_cuedit extends cls_cueditbase
{
    //�۸����Ƶ�ǰ�µĲο���
    public function fm_reference_price($field, $chid, $info = array()){
        if (empty($info) || !in_array($chid, array(
            2,
            3,
            4)))
            return;
        $db = _08_factory::getDBO();
        $tblprefix = cls_env::getBaseIncConfigs('tblprefix');
        if (date('m', $info['month']) == date('m')) {
            //�۸�ͳ��(ȫ����¥�̣����ۣ�/���ַ����ܼۣ�/���⣨���ۣ����м۸�ͳ�ƣ���ƽ��ֵ)
            $reference_price = $db->result_one("SELECT AVG($field) AS price FROM {$tblprefix}" .
                atbl($chid) . " WHERE $field > 0 ");
            $reference_price = $chid == 3 ? round($reference_price, 2) : round($reference_price);
            trbasic('�ο�', 'fmdata[clicks]', $reference_price, 'text', array('guide' =>
                    '���¼۸�ο�ֵ', 'w' => '20'));
        }
    }
    
    /**
     * �������ί��,����ί�й���һ���������ֿ���ʾ�ۼ�(zj)�ֶΡ�
     **/
	public function user_zj($a,$b){
        global $chid;
	    if($b=='fm' && $this->fields[$a]['type']=='cu' && $this->fields[$a]['tpid']==36 && $chid==2){	      
	       trbasic(($this->fields[$a]['notnull']?'<font color="red">':'').' * </font>���', $this->fmpre.'['.$a.']', $this->predata[$a], 'text',array('addstr'=>'<font class="gray">Ԫ/��</font>','w'=>'20'));
	    }else{
	       return 'undefined';
	    }
	}

    //��¥�����۽��лظ�
    public function fm_replay($info = array()){
        if (empty($info))
            return;
        trbasic('��������', 'comment', $info['comment'], 'textarea');
        trhidden('fmdata[aid]', $info['aid']);
        trhidden('fmdata[tocid]', $info['cid']);
        trbasic('�ظ�', 'fmdata[reply]', '', 'textarea');
    }

    //����¥�����۵Ļظ�
    public function sv_replay(){
        global $onlineip;
        $this->sv_set_fmdata(); //����$this->fmdata�е�ֵ
        $fmdata = $this->fmdata;
        if (empty($fmdata))
            return;
        $db = _08_factory::getDBO();
        $tblprefix = cls_env::getBaseIncConfigs('tblprefix');
        $timestamp = TIMESTAMP;
        $curuser = cls_UserMain::CurUser();
        $mid = $curuser->info['mid'];
        $mname = $curuser->info['mname'];
        $db->insert("{$tblprefix}commu_lppl",
            'tocid, aid, mid, mname, createdate, checked, ip, cuid, reply', array(
            $fmdata['tocid'],
            $fmdata['aid'],
            $mid,
            $mname,
            $timestamp,
            1,
            $onlineip,
            2,
            $fmdata['reply']))->exec();
        $this->sv_finish();
    }

    //�·��Ź���еĶ�������
    public function fm_dghx($aid=0){
        $db = _08_factory::getDBO();
        $tblprefix = cls_env::getBaseIncConfigs('tblprefix');
        $chid = 11;
        $str = "";
        if($this->isadd){
            $aid = empty($aid)?0:max(1,intval($aid));
        }else{
            $info = $this->predata;
            $aid = $info['aid'];
        }
       	$fromsql = "FROM {$tblprefix}".atbl($chid)." a INNER JOIN {$tblprefix}aalbums b ON b.inid=a.aid"; 
    	$wheresql = "WHERE b.pid='$aid' "; 
        $db->select('a.aid,a.subject')->from("{$tblprefix}" . atbl($chid) . " a ")->innerJoin("{$tblprefix}aalbums b")->
            _on('b.inid=a.aid')->where(array('b.pid' => $aid))->exec();
        $num = 0;        
        while($r = $db->fetch()) {
            $checked = '';
            $num ++;    
            if(!$this->isadd)$checked = preg_match("/^($r[aid])$|^($r[aid])\t|\t($r[aid])\t|\t($r[aid])$/", $info['dghx']) ? 'checked="checked"' : '';
            $str .= "<input type='checkbox' name='fmdata[dghx][]' value='$r[aid]' id='dghx_$r[aid]' $checked />$r[subject]\n";
            if($num%5==0)$str .= "<br/>";
        } 
		$str || $str = "(���޻���)";
        trbasic('��������', '', $str, '');
    }

    /**
     * ��̨ί�з�Դ����������ʾί������
     */
    public function fm_wt_info($cid){
        $db = _08_factory::getDBO();
        $tblprefix = cls_env::getBaseIncConfigs('tblprefix');
        $db->select('a.owerstatus,a.jjrstatus,a.weituodate,m.mname ')->from("{$tblprefix}weituos a ")->innerJoin("{$tblprefix}members m")->_on('a.tmid=m.mid')->where(array('a.cid' => $cid))->exec();
  		$cy_arr[] = '��ί�з�';	
		$cy_arr[] = 'ί��״̬';
		$cy_arr[] = 'ί��ʱ��';
		trcategory($cy_arr);
        while($r = $db->fetch()){
 			$fover = $r['owerstatus'];
			$fsget = $r['jjrstatus'];
			if($fover == 1){
				$statusstr = '��ҵ��ȡ��';
			}elseif($fover == 2){
				$statusstr = '�ɹ�ί��';	
			}elseif($fsget == 1){
				$statusstr = "�Ѿܾ�";
			}elseif($fsget == 2){
				$statusstr = '����ί��';
			}else{
				$statusstr = '������';
			}
			$mname = $r['mname'];
			$time = date('Y-m-d H:i:s',$r['weituodate']);
			echo "<tr>\n";
			echo "<td class=\"txtC\">$mname</td>\n";		
			echo "<td class=\"txtC\">$statusstr</td>\n";
			echo "<td class=\"txtC\">$time</td>\n";
			echo "</tr>\n";
        }
    }
    
    //��װ>>��Ʒ>>�޸Ľű��е�״̬��ʾ
    public function fm_state(){
        $info = $this->predata;
        $select_arr =  array('0'=>'δ����','1'=>'�Ѵ���');
        trbasic('����״̬','',makeradio('fmdata[state]',$select_arr,$info['state']),'');
    }
    
    //��װ>>��Ʒ>>�޸Ľű��е�״̬�޸�
   	function sv_state($cfg = array()){	
		$this->sv_set_fmdata();//����$this->fmdata�е�ֵ
        //�޸�ֻ���޸�״̬�������Ĳ����޸ģ�������¸�ֵthis->fmdata      
        $this->db->update($this->table(), array('state' => $this->fmdata['state']))->where('cid='.$this->cid)->exec();
		$this->sv_finish($cfg);//����ʱ��Ҫ�����񣬰���������¼���ɹ���ʾ��
		
	}
    
  /**
   * ¥�̼۸�༭��ת����
   */
  public function fm_dj_edit_url(){
    	trbasic('�۸�༭','',"<a onclick=\"return floatwin('open_arcdj',this)\" href=\"?entry=extend&amp;extend=jiagearchive&aid=".$this->predata['aid']."&isnew=1\" class=\"scol_url_pub\"><font color='blue'>>>�༭¥�̼۸�</font></a>",'html22',array('guide'=>'��˿��Խ���¥�̼۸�༭ҳ��'));
  }

	//���� Ӷ����ȡ���
	public function fma_fxyongjin($field){
		$s = $this->predata['status'];
		if($s!='3') return;
		$a = array('yjbase'=>'Ӷ����ȡ','yjextra'=>'�ϼ���ȡ');
		$v = $this->predata[$field];
		$re = empty($v) ? '<span style="">δ��ȡ</span>' : '<span style="color:#00F">����ȡ</span>';
		trbasic($a[$field],'',$re,'');
	}

	//���� ¥�������б�
	public function fma_fxlpnames($exfenxiao,$mode='0'){
		$aids = $this->predata['aids']; $aida = explode(',',$aids);
		$ayjs = $this->predata['ayjs']; $ayja = explode(',',$ayjs); 
		$slps = '<table border="0"><tr><td>��������</td><td>Ӷ��(Ԫ)</td><td>¥������</td><td>ȷ����</td></tr>'; $no = 0;
		$yjhid = '<input name="fmdata[okayj]" id="fmdata[okayj]" type="hidden" value="'.(empty($this->predata['okayj']) ? '' : $this->predata['okayj']).'" rule="text" must="1">';
		foreach($aida as $k=>$aid){
			if(empty($aid)) continue;
			$pinfo = $this->getPInfo('a',$aid,1);
			if(!empty($pinfo['lpmc'])){
				$no++; 
				if(count($aida)==3){ // ֻ��һ����Ч��id
					$checked = 'checked';
					$yjhid = str_replace('value=""','value="'.$ayja[$k].'"',$yjhid);
				}else{
					$checked = $this->predata['okaid']==$aid ? 'checked' : '';	
				}
				$yjadd = $no==1 ? $yjhid : '';
				$slps .= "<tr><td> {$pinfo['subject']} </td><td> {$ayja[$k]} </td><td>".$pinfo['lpmc']." </td><td><label onClick=\"fillYj('$ayja[$k]')\"><input class='radio' type='radio' name='fmdata[okaid]' id='_fmdata[okaid]{$no}' $checked value='{$aid}'>ȷ��</label>$yjadd</td></tr>"; 
			}
		}
		trbasic('¥�̼�Ӷ��','',"$slps</table><script>function fillYj(yj){\$id('fmdata[okayj]').value=yj;}</script>",'');
		if($this->predata['status']=='3'){
			trbasic('ȷ��ʱ��','',date('Y-m-d H:i',$this->predata['oktime']),'');
		}
	}
	
   // �����Ƽ����: ��¼�ľ�����,������,�Ƽ�����
   public function fm_fenxiao_check($exfenxiao){
		$curuser = cls_UserMain::CurUser(); $curuser->detail_data();
		$db = $this->db;
		if(empty($curuser->info['mid']) || $curuser->info['mchid']!=2){ 
			$this->message('�Ƽ��ͻ� ���¼Ϊ�����ˣ�');
		}elseif(!empty($curuser->info['blacklist'])){
			$this->message('���ѱ����������, �������Ƽ��ͻ���<br>');
		}
		$mid = $curuser->info['mid'];
		$cnt = $db->select('COUNT(*)')->from($this->table())
			->where(array('mid'=>$mid))->exec()->fetch(); // ->_and(array('createdate'=>'1370270040'))
		$cnt = $cnt['COUNT(*)']; //var_dump($cnt); echo "{$exfenxiao['pnum']}";
		if(intval($cnt)>=intval($exfenxiao['pnum'])){
			$this->message('�Ƽ��ͻ��Ѵﵽ����['.$exfenxiao['pnum'].'], �������Ƽ��ͻ���<br>');
		} 
		return $cnt;
   }

   public function sv_fenxiao_satus($exfenxiao){
		$fmdata = $this->fmdata;
		$db = $this->db;
		if($fmdata['status']=='3'){ //ȷ��ʱ��,//�ѳɽ�(Ԥ��) ����(�ɱ༭)
			$this->sv_excom('oktime',TIMESTAMP); 
			#$db->query("UPDATE {$tblprefix}".atbl(113)." SET yds = yds + 1 WHERE aid='{$fmdata['okayj']}'");
			$db->update('#__'.atbl(113), "yds=yds+1")->where("aid={$fmdata['okayj']}")->exec();
		}
		if($fmdata['status']=='4'){ //��Ч�ͻ����ֶ�����������
			$mid = $this->predata['mid'];
			$cnt = $db->select('COUNT(*)')->from($this->table())
				->where(array('mid'=>$mid))->_and(array('status'=>4))->exec()->fetch(); // ->_and(array('createdate'=>'1370270040'))
			$cnt = $cnt['COUNT(*)']; // var_dump($cnt); echo "{$exfenxiao['pnum']}";
			//echo "$cnt,".$exfenxiao['unvnum'];
			if(intval($cnt)>=intval($exfenxiao['unvnum'])){
				$db->update('#__members_2', array('blacklist' => 1))->where("mid = $mid")->exec();
			} 
		}
   }

   public function sv_fenxiao_check($exfenxiao){
		$fmdata = $this->fmdata;
		//���õ绰�����Ƿ�����Ч�û�
		$dianhua = $fmdata['dianhua']; if(empty($dianhua)) $this->message('�ύ����[��ϵ�绰]����',M_REFERER);
		$this->db->select('*')->from($this->table())->where(array('dianhua'=>$fmdata['dianhua']));
		$dhchk = $this->db->_and(array('createdate'=>TIMESTAMP+$exfenxiao['vtime']*86400),'<')->exec()->fetch();
		if($dhchk) $this->message("�Ƽ�ʧ�ܣ�<br>��ϵ�绰[{$dianhua}]<br>��{$exfenxiao['vtime']}�����Ѿ������Ƽ�����<br>",M_REFERER);
		#var_dump($dhchk); print_r($dhchk); die('xxx');
		//��������Դ�Ƿ���Ч
		$aida = explode(',',$fmdata['aids']);
		$said = ''; $sayj = '';
		foreach($aida as $aid){
			if(empty($aid)) continue;
			$pinfo = $this->getPInfo('a',$aid,1);
			if(!empty($pinfo['yj'])){
				$said .= (empty($said) ? '' : ',').$aid; 
				$sayj .= (empty($sayj) ? '' : ',').$pinfo['yj'];
			}
		}
		if(empty($said) || empty($sayj)) $this->message('�ύ����[¥������]����',M_REFERER);
		return array('said'=>$said,'sayj'=>$sayj);
   }

}
