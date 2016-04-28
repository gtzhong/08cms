<?php
class cls_arccols extends cls_arccolsbase{
	
	// ��Դ-��ʾ��Ա����
	protected function user_xingming($mode = 0,$data = array()){
		global $db,$tblprefix;
		$curuser = cls_UserMain::CurUser();
		$key  = 'xingming';
		$cfg = &$this->cfgs[$key];	
		if($mode){//�����б���������
			$this->titles[$key] = $this->top_title($key,$cfg);
		}else{
			$userid = $data['mid'];
			$uname = $data['mname'];
			if($userid==='0'){
				$str = "<span style='color:#CCC'>(�ο�)</span>";
			}elseif($xingming=$db->result_one("SELECT xingming FROM {$tblprefix}members_sub WHERE mid='$userid'")){
				$str = "<span title='$uname'>$xingming</span>";;	
			}else{
				$str = "<span style='color:#999'>$uname</span>";
			}
			return $str;
		}
	}
	//��Դ-��ʾС�����Ʋ�ʶ��δ����С��
   protected function user_ulpmc($mode = 0,$data = array()){
		$curuser = cls_UserMain::CurUser();
		$key  = 'ulpmc';
		$cfg = &$this->cfgs[$key];	
		if($mode){
			$this->titles[$key] = $this->top_title($key,$cfg);
		}else{
			$lpmcstr = htmlspecialchars($data['lpmc']);
			$arc = new cls_arcedit;
			$arc->set_aid($data['pid3'],array('au'=>0,'ch'=>0));
			$pinfo = $arc->archive;
			$pinfo && cls_ArcMain::Parse($pinfo);
			$str = empty($pinfo['arcurl7']) ? "<span style='color:#999' title='δ����С��'>$lpmcstr</span>" : "<a href=\"$pinfo[arcurl7]\" target=\"_blank\" title='$lpmcstr'>".$lpmcstr."</a>";
			unset($arc);
			return $str;
		}
	}
	

	// ��Դ-��ʾ��Ա����
	protected function user_mchid($mode = 0,$data = array()){
		global $db,$tblprefix;
		$curuser = cls_UserMain::CurUser();
		$key  = substr(__FUNCTION__,5);
		$cfg = &$this->cfgs[$key];	
		if($mode){//�����б���������
			$this->titles[$key] = $this->top_title($key,$cfg);
		}else{
			return $data['mchid'] == 1 ? '����' : '�н�';
		}
	}
	
	
	//edit_self������URL����������ÿ��ϵͳ����Ա���ģ����ɿ�����
	//winsize-���ڴ�С����:��500,300,��url��ʹ��
	//aclass-<a>��ʽ
	protected function user_editself($mode = 0,$data = array()){
		$curuser = cls_UserMain::CurUser();
		$key  = 'editself'; //$keyc = 'cyuyue';
		$cfg = &$this->cfgs[$key];	
		if($mode){//�����б���������
			$this->titles[$key] = $this->top_title($key,$cfg);
		}else{
			$addstr = empty($cfg['umode']) ? "onclick=\"return floatwin('open_arc$key',this".(empty($cfg['winsize']) ? '' : ','.$cfg['winsize']).")\"" : ($cfg['umode'] == 1 ? "target=\"_blank\"" : '');
			$userid = $data['mid'];
			if($userid==$curuser->info['mid']){
				$str = "<a ".(isset($cfg['aclass']) ? "class='$cfg[aclass]'" : "class='scol_url_pub'")." href=\"".key_replace($cfg['url'],$data)."\" {$addstr}>".(empty($cfg['mtitle']) ? (isset($data[$key]) ? $data[$key] : $key) : key_replace($cfg['mtitle'],$data))."</a>";	
			}else{
				$str = "-";
			}
			return $str;
		}
	}
	
	//�ο�arccolsbase.cls.php:type_ccid : ��д
	//��Ի�Ա����,��Դ �ö� �����Ӵ���
	//����:[$checkurl = 1;]��[$checkurl && ]����Ϊ��չ���֣�����ע�Ᵽ����arccolsbase.cls.php�ļ�һ��
	protected function type_ccid($key = '',$mode = 0,$data = array()){ 
		global $timestamp;
		$chid = $this->A['chid'];
		$cotypes = cls_cache::Read('cotypes');
		if($chid==7&&$key=='ccid1'){ // ¥�����-ccid1��������ģ��(¥��)
			$coid = max(0,intval(str_replace('ccid','',$key)));
		}else{
			if(!($coid = max(0,intval(str_replace('ccid','',$key)))) || empty($cotypes[$coid]) || !in_array($coid,$this->A['coids']) || $cotypes[$coid]['self_reg']) return $this->del_item($key);
		}
		$cfg = &$this->cfgs[$key];
		$cfg['coid'] = $coid;
		if($mode){
			if(empty($cfg['title'])) $cfg['title'] = $cotypes[$coid]['cname'];
			$this->titles[$key] =  $this->top_title($key,$cfg);
		}else{
			$color = empty($cfg['color']) ? '' : $cfg['color'];
			$icon = empty($cfg['icon']) ? 0 : 1;
			$num = empty($cfg['num']) ? 0 : $cfg['num'];
			isset($cotypes[$coid]['asmode']) || $cotypes[$coid]['asmode']='';
			$re = cls_catalog::cnstitle(@$data[$key],$cotypes[$coid]['asmode'],cls_cache::Read('coclasses',$coid),$num,$icon);
			$re || $re = isset($cfg['empty']) ? $cfg['empty'] : '-';
			$addstr = empty($cfg['umode']) ? "onclick=\"return floatwin('open_arc$key',this".(empty($cfg['winsize']) ? '' : ','.$cfg['winsize']).")\"".($color ? " style=\"color:$color\"" : "") : ($cfg['umode'] == 1 ? "target=\"_blank\"" : '').($color ? " style=\"color:$color\"" : "");			
			$checkurl = 1; //��չ����
			if($this->mc && in_array($key,array('ccid9'))){ 		
				if($key=='ccid9'){
					$re = empty($data[$key]) ? "<span class='gray_t'>����</span>" : "<b>�ö�</b>"; 
				}
			}
			$checkurl && isset($cfg['url']) && $re = "<a ".(isset($cfg['aclass']) ? "class='$cfg[aclass]'" : "class='scol_url_pub'")." href=\"".key_replace($cfg['url'],$data)."\" {$addstr}>$re</a>";
			return $re;
		}
	}
	
	// ��Դ-ԤԼ����
	protected function user_cyuyue($mode = 0,$data = array()){
		$key  = 'yuyue'; $keyc = 'cyuyue';
		$cfg = &$this->cfgs[$keyc];
		if($mode){//�����б���������
			$this->titles[$keyc] = $this->top_title($keyc,$cfg);
		}else{
			$addstr = empty($cfg['umode']) ? "onclick=\"return floatwin('open_arc$key',this".(empty($cfg['winsize']) ? '' : ','.$cfg['winsize']).")\"" : ($cfg['umode'] == 1 ? "target=\"_blank\"" : '');
			$ctitle = ($data['yuyue'] == '1' ? "<span style=\"color:red;\"><b>��Լ</b></span>" : "ԤԼ");
			return "<a ".(isset($cfg['aclass']) ? "class='$cfg[aclass]'" : "class='scol_url_pub'")." href=\"".key_replace($cfg['url'],$data)."\" {$addstr}>$ctitle</a>";
		}
	}
	
	// ��Դ-���� ��������ʾ, ����,����,���,����,����
	protected function user_csubject($mode = 0,$data = array()){	
		$key  = 'subject'; $keyc = 'csubject';
		$cfg = &$this->cfgs[$keyc];
		if($mode){//�����б���������
			$cfg['side'] = 'L';
			if(empty($cfg['title'])) $cfg['title'] = '����';
			$this->titles[$keyc] = $this->top_title($keyc,$cfg);
			foreach(array('zlfs','fwjg','cx') as $k){
				$cfg[$k.'_fields'] = cls_cache::Read('field',$this->A['chid'],$k);
			}
		}else{//�����б�������
			$url = cls_url::view_arcurl($data,0);
			$re = "<a ".(isset($cfg['aclass']) ? "class='$cfg[aclass]'" : "class='scol_subject'")." href=\"$url\" target=\"_blank\"";
			$len = empty($cfg['len']) ? 40 : $cfg['len'];
			$subject = htmlspecialchars(cls_string::CutStr($data['subject'],$len));
			if(!empty($data['thumb'])) $len -= 4;
			$re .= " title=\"".htmlspecialchars($data['subject'])."\">$subject</a>";
			$lpmcstr = htmlspecialchars($data['lpmc']);
			$lpmcack = (strlen($data['lpmc'])>0?$data['lpmc']:'-');
			$arc = new cls_arcedit;
			$arc->set_aid($data['pid3'],array('au'=>0,'ch'=>0));
			$pinfo = $arc->archive;
			$pinfo && cls_ArcMain::Parse($pinfo);
			$re .= empty($pinfo['arcurl7']) ? "<br><span style='color:#999' title='δ����С��'>".$lpmcack.'</span>' : "<br><a "." href=\"$pinfo[arcurl7]\"". "target=\"_blank\"". " title=\"".$lpmcstr."\">".$lpmcack."</a>";
			unset($arc);
			$huxingstr = $data['shi'].'��/'.substr($data['ting'],-1,1).'��/'.substr($data['wei'],-1,1).'��';
			$lc = (empty($data['szlc']) ? '' : $data['szlc']."��").(empty($data['zlc']) ? '' : '��'.$data['zlc']."��"); 
			$huxingstr .= empty($lc) ? '' : ",".$lc;
			$mj = $data['mj'].'M&sup2;'; //����/���
			$fangling = empty($data['fl'])?'':$data['fl'].'��';
			$vals = ''; // ���޷�ʽ,���ݽṹ,����
			foreach(array('zlfs','fwjg','cx') as $k){
			if(isset($cfg[$k.'_fields']['innertext'])){
				$a = select_arr($cfg[$k.'_fields']['innertext'],0); 
				$val = empty($data[$k])?'':$a[$data[$k]];
				$vals .= empty($val) ? '' : ",$val";
			}}
            if (in_array($this->A['chid'],array(2,3))){
			    return "$re<br/>$huxingstr,$mj".(empty($fangling) ? '' : ",$fangling")."".$vals;
            }else{
                return "$re<br/>$mj".(empty($fangling) ? '' : ",$fangling")."".$vals;
            }
		}
	}
	
	//close����,������ʾ �ʴ������״̬��ʾ	������/�رգ�
	//cfgs�����ڵ�ajax�����ж��Ƿ�����ajax�ı�״̬
	protected function type_close($key = '',$mode = 0,$data = array()){	
		static $_js;
		$mconfigs = cls_cache::Read('mconfigs');
        $cms_abs = $mconfigs['cms_abs'];	
		$cfg = &$this->cfgs[$key];
		if($mode){//�����б���������
				$this->titles[$key] = $this->top_title($key,$cfg);
		}else{
			if(isset($cfg['ajax'])){
				if(empty($_js)){
					$_js = 1;
				?>
				<script type="text/javascript">
					function setanswer(obj){
					var aj = new Ajax('HTML');
					aj.get(obj.href,function(s){
						if(s == 'succeed'){				
							obj.innerHTML = obj.innerHTML == '�ر�' ? '����' : '�ر�';
							obj.style.color = obj.style.color == 'red' ? '#333333' : 'red';
							obj.href = obj.href.indexOf('type') != -1 ? obj.href.replace('&type=1','') : obj.href + '&type=1';
						}else{
							alert(s);	
						}
					},1);
					return false;	
				}
				</script>
				<?php	
				}					
				return $data['close']?'<a style="color:red" onclick="return setanswer(this)" href="'.$cms_abs._08_Http_Request::uri2MVC("ajax=setanswer&type=1&aid=$data[aid]").'" title="����رջ�������">�ر�</a>':'<a onclick="return setanswer(this);" href="'.$cms_abs._08_Http_Request::uri2MVC("ajax=setanswer&aid=$data[aid]").'" title="����رջ�������">����</a>';				
			}else{	
				return $data['close']?'�ر�':'����';				
			}
		}
	}

	/**
     * count������������ʾĳ���ϼ����߽��������ͳ��
       �ϼ�
       oL->m_additem('azbs',array('type'=>'ucount','title'=>'�ܱ�','url'=>"?entry=extend&extend=peitaos_pid&pid={aid}",'func'=>'gethjnum','arid'=>'1','chid'=>8,'width'=>28,));
       ����		
	   $oL->m_additem('ayxs',array('type'=>'ucount','title'=>'ӡ��','url'=>"?entry=extend&extend=yinxiangheji&aid={aid}",'func'=>'getjhnum','cuid'=>'44','width'=>28,));
     */
    
	protected function type_ucount($key = '',$mode = 0,$data = array()){
		$mconfigs = cls_cache::Read('mconfigs');
        $cms_abs = $mconfigs['cms_abs'];
		$cfg = &$this->cfgs[$key];
		if($mode){//�����б���������
			$this->titles[$key] = $this->top_title($key,$cfg);
		}else{
			$v = 0; $f = empty($cfg['func']) ? '' : "ex_$cfg[func]"; 
			if($f){ // getjhnum($aid=0,$cuid=0), gethjnum($aid=0,$chid,$arid = 0)
				$v = $f=="ex_getjhnum" ? $f($data['aid'],$cfg['cuid']) : $f($data['aid'],$cfg['chid'],$cfg['arid']);
			}
			$_html = "<a  onclick=\"return floatwin('open_arcexit',this)\"  href=\"".key_replace($cfg['url'],$data)."\">".(empty($v)?'[0]':"[".$v."]")."</a>";
			return $_html;
		}	
	}
	
	//count������������ʾĳ���ϼ����߽��������ͳ��
	protected function type_ShowContent($key = '',$mode = 0,$data = array()){
		$mconfigs = cls_cache::Read('mconfigs');
        $cms_abs = $mconfigs['cms_abs'];	
		$cfg = &$this->cfgs[$key];
		if($mode){//�����б���������
			$this->titles[$key] = $this->top_title($key,$cfg);
		}else{			
			if($data['toaid']==0&&$data['tocid']==0) $type = '����Ĵ�';
			if($data['toaid']==0&&$data['tocid']>0)  $type = '����Ĳ���';
			if($data['toaid']>0 &&$data['tocid']==0) $type = '�𰸵Ĳ���';		
			return $type;
		}	
	}
	
	//ѡ��cid
	protected function type_cid($key = '',$mode = 0,$data = array()){		
		$mconfigs = cls_cache::Read('mconfigs');
        $cms_abs = $mconfigs['cms_abs'];
		$cfg = &$this->cfgs[$key];
		if(empty($cfg['width'])) $cfg['width'] = 30;
		if(!isset($cfg['view'])) $cfg['view'] = 'S';
		if($mode){//�����б���������
			if(empty($cfg["title"])) $cfg['title'] = "<input class=\"checkbox\" type=\"checkbox\" name=\"chkall\" onclick=\"checkall(this.form,'selectid','chkall')\">";
			$this->titles[$key] = $this->top_title($key,$cfg);
		}else{//�����б�������		
			return "<input class=\"checkbox\" type=\"checkbox\" name=\"selectid[$data[cid]]\" value=\"$data[cid]\">";
		}
	}
    
        
    /**     
     * ��ʾ¥�̵ı����Լ����ӣ�������ᡢ�����б��У�
     */
	public function type_lpname($key = '',$mode = 0,$data = array()){
	    $db = _08_factory::getDBO();
        $tblprefix = cls_env::getBaseIncConfigs('tblprefix');
		$mconfigs = cls_cache::Read('mconfigs');
        $cms_abs = $mconfigs['cms_abs'];
		$cfg = &$this->cfgs[$key];
		if($mode){//�����б���������
			$this->titles[$key] = $this->top_title($key,$cfg);
		}else{ //print_r();
            $x_chid = cls_env::GetG('x_chid');
            $x_chid = in_array($x_chid,array(4,115,116)) ? $x_chid : 4; 
		    $pkey = empty($cfg['pidkey']) ? 'lpaid' : $cfg['pidkey'];
			$data = $db->fetch_one("SELECT ".substr(aurl_fields(),1)." from {$tblprefix}".atbl($x_chid)." a WHERE a.aid = '".$data[$pkey]."'");
            if(!empty($data)){
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
    			$re .= " title=\"".htmlspecialchars($data['subject'])."\">".htmlspecialchars(cls_string::CutStr($data['subject'],$len))."</a>";
    			return $re;
            }else{
                return '-';
            }
		}	
	}

    /*
     * ��������ͼ��ͻ����ʾ
     * */
    //cfgs[onclick]��Ϊ��ʱ������js
    public function user_thumb($mode = 0,$data = array()){
        $key = substr(__FUNCTION__,5);
        $cfg = &$this->cfgs[$key];
        if(!$key) return;
        global $cms_abs;
        if($mode){//�����б���������
            $this->titles[$key] = $this->top_title($key,$cfg);
        }else{
            $thumb = view_checkurl($data[$key]);
            $cfg['onclick'] = isset($cfg['onclick']) ? $cfg['onclick'] : 1;
            $_onclick = empty($cfg['onclick'])?'':"onclick=\"_img_affect_checkbox($data[aid]);\"";
			$re = "<img src=\"".$thumb."\" style=\"float:left;display:block; \" width=\"".$cfg['width']."\"  height=\"".$cfg['height']."\" ".$_onclick.">";
			if(!empty($data['zlhx'])) $re .= "<span></span>";
			return $re;
        }
    }

}
