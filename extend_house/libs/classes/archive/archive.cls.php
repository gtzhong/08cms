<?php
class cls_archive extends cls_archivebase{

	//��ͼ����Ϊ��ͼ
	function fm_phpSetImgtype(&$fields){ 
		$fythumb = $fields['thumb'];
		$fythumb['cname'] = '��ԴͼƬ';  
		$fythumb['ename'] = 'fythumb';   
		$fythumb['datatype'] = 'images'; 
		$fythumb['issearch'] = '1'; //ͼƬ����2:0-�ر�,1-����
		$fythumb['imgComment'] = 'ռλ'; //title_for_prop2
		$fythumb['min'] = '0';
		$fythumb['max'] = '2';
		$fythumb['guide'] = '';
		$fields['fythumb'] = $fythumb;
	}
	
	//����ͼƬ���
	function fm_jsSetImgtype($filed='thumb'){ 
		$fcfg = cls_cache::Read('field',121,'lx');
		$farr = cls_field::options($fcfg); 
		$opts = str_replace(array("\r","\n",'"'),array('','',"'"),makeoption($farr)); //print_r($opts);
		$sels = "<select id='(kid)' onchange='fytu_setvalue(this)'><option value=''>-��ѡ��-</option>$opts</select>";
		?>
		<style type="text/css">div.item_link{ display:none; }</style>
		<script type='text/javascript'>
		var s_tmp = "<?php echo $sels; ?>";
		function _$( domID ) {
			var _domObject = parent.document.getElementById(domID);
			if ( _domObject == null ){ _domObject = document.getElementById(domID); } 
			return _domObject;
		}
		function fytu_timerscan(){ 
			$("[id='imgbox_fmdata[<?php echo $filed; ?>]']").find('.progressWrapper').each(function(index, element) {
				var k0 = $(this).attr('id').toString().replace('SWFUpload_0_fmdata[<?php echo $filed; ?>]','');
				var kid = 'img_box_setopt_'+k0; 
				if(!_$(kid)){ 
					setTimeout("fytu_addselect('"+k0+"','"+kid+"')",300);
				}else{ 
					$(this).find('.item_box').show(); //��ʱ��᲻��ʾ???
				} //console.log($('#'+kid));
			}); // clear ???
			fytu_setvalue();
			setTimeout('fytu_timerscan()',500);
		}
		function fytu_addselect(k0,kid){ 
			var img = _$('SWFUpload_0_fmdata[<?php echo $filed; ?>]'+k0+'_img'); 
			if(img && $(img).attr('src').indexOf('loading.gif')<=1){ //�ȴ��������ִ��
				var kc = $(_$('SWFUpload_0_fmdata[<?php echo $filed; ?>]'+k0)).find('div:first');
				//$(kc).before(s_tmp.replace('(kid)',kid)); //.before, append
				//$(kc).html(s_tmp.replace('(kid)',kid) + $(kc).html());
				//$(kc).children('a').before(s_tmp.replace('(kid)',kid)); //ok
				//$(kc).children('span').after(s_tmp.replace('(kid)',kid));
				var _1stinput = $(kc).find('.item_input')[0]; 
				$(_1stinput).before(s_tmp.replace('(kid)',kid));
				$('div.progressWrapper').removeClass('h110'); //ȥ���߶�����,���ݰ������ȷ��
				$('div.progressWrapper').removeClass('h130'); 
			}
		}
		//function fytu_delselect(e){ }
		function fytu_setvalue(e){ 
			var uploadID = '_08_upload_fmdata[<?php echo $filed; ?>]'; 
			var boxID = 'imgbox_fmdata[<?php echo $filed; ?>]'; 
			var currentBox, _value = '', _src, textareaValue,textareaLink;
			for(var i = 0; i < _$(boxID).childNodes.length; ++i){	
				currentBox = _$(boxID).childNodes[i].childNodes[0];
				if ( currentBox != undefined ){
					textareaValue = currentBox.lastChild.previousSibling.childNodes[1].value;
					textareaLink = currentBox.lastChild.childNodes[1].value = $(currentBox).find('select:first').val(); //currentBox.lastChild.childNodes[1].value;          
					if( _value ){ _value += '\n'; } //console.log(textareaLink);
					var itm_box = currentBox.getElementsByTagName('img')[0]; //��getElementsByTagName�ɱ����м����Ԫ�غ����ʧ��
					_src = itm_box.getAttribute('src') ? itm_box.getAttribute('src') : itm_box.getAttribute('value');
					_sValue = (textareaValue) ? ('|' + textareaValue) : ''; // && textareaValue != originalValue
					_sLink = (textareaLink) ? ('|' + textareaLink) : ''; // && textareaLink != swfu.customSettings.imgsCom
					_sValue = (!_sValue && _sLink) ? '|' : _sValue; //���_sValueΪ��,_sLink��Ϊ��,����Ҫ��|�ֿ�
					_value += _src + _sValue + _sLink;
				}
			}
			$('div.item_link').hide(); 
			_$(uploadID).value = decodeURIComponent(_value);
		} 
		fytu_timerscan(); $(document).ready(function(){  }); 
		</script> 
		<?php
	}
	

		
	// ��Ա����,����ؼ۷�,ѡ����¥��
	function fm_mylpSelect(){
		$db = _08_factory::getDBO();
		$curuser = cls_UserMain::CurUser();
		$mid = $curuser->info['mid']; 
		// ids
   		$row = $db->select('loupan')->from('#__members_13')
        ->where("mid = $mid")->exec()->fetch(); 
		$row = empty($row) ? '' : $row['loupan'];
    	$a = explode(',', $row);
		$s = '0';
		foreach($a as $k){
			$k = intval($k);
			if($k) $s .= ",$k";
		}
		// options
		$db->select('aid,subject')->from("#__archives15")
		->where("aid IN ($s)")
		->limit(100)->exec(); 
		$re = '<option value="">-��ѡ��¥��-</option>';
		while($row=$db->fetch()){
			$re .= "\n<option value='$row[aid]'>".$row['subject']."</option>";	
		}
		trbasic('<span style="color:red">*</span> ����¥��','',makeselect("{$this->fmdata}[pid]",$re,'rule="must"'),'');
	}
	
	/**
	* �����ϼ� ��չ
	* ¥�̹��� ��Ƶ,�����̵�
	*
	* @param    int    $pid    �ϼ���Ŀid
	* @param    int    $chid   ��Դģ��id
	* @param    string $title  �ϼ���Ŀ��
	*/
	function fm_relalbum($pid='0',$chid=0, $title='�����ϼ�'){
		global $db,$tblprefix;
		$rid = isset($this->predata["pid$pid"]) ? $this->predata["pid$pid"] : 0;
		$subject = $rid ? $db->result_one("SELECT subject FROM {$tblprefix}".atbl($chid)." WHERE aid='$rid'") : '';
		$hidpid = "<input type=\"hidden\" id=\"pid{$pid}\" name=\"pid{$pid}\" value=\"$rid\"/>";
		$relbtn = "<input type=\"button\" value=\"����$title\" onclick=\"return floatwin('open_arcexit_{$pid}','?entry=extend&extend=rel_lp1&chid=$chid')\">";
		$clrbtn = "<input type=\"button\" value=\"�������\" onclick=\"document.getElementById('pid{$pid}').value='';document.getElementById('pid{$pid}text').innerHTML = '';\">";
        $hidname = '';
        $pid == 6 && $hidname = "<input id='".$this->fmdata."[kfsname]' name='".$this->fmdata."[kfsname]' type='hidden' value='".$subject."'>";
		trbasic($title,'',"$hidname<span id=\"pid{$pid}text\">$subject</span> $hidpid $relbtn $clrbtn",'');
	}
	
	// ���¥���Ƿ��ظ�: (js)
	// 0: ¥�̱�
	// 5: ¥�̱�+��ʱС����
	function fm_lpExist($leixing=5){
		$leixing = empty($leixing) ? 0 : $leixing;
		echo '<script type="text/javascript">';
		echo 'window._08cms_validator && _08cms_validator.init("ajax","fmdata[subject]",{url:CMS_ABS+"'._08_Http_Request::uri2MVC("ajax=lpexist&leixing=$leixing&lpname=%1").'"})';
		echo '</script>';
	}
	
	// ���޷�ʽ���: (2��һ���) (array('fkfs','zlfs')); // ���޷�ʽ,���ʽ
	function fm_czumode($cfields=array()){
		$cfields = empty($cfields) ? array('zlfs','fkfs') : $cfields;
		$a_field = new cls_field; $str = ''; $pfix = $this->fmdata;
		foreach($cfields as $f){
			if(isset($this->fields[$f])){
				$this->fields[$f]['mode'] = '0';
				$cfg = $this->fields[$f];
				$a_field->init($cfg,isset($this->predata[$f]) ? $this->predata[$f] : '');
				$varr = $a_field->varr($this->fmdata);
				$_opt0 = "<option value='0'>-".$this->fields[$f]['cname']."-</option>";
				$varr['frmcell'] = str_replace("<select name=\"{$pfix}[$f]\">","<select name=\"{$pfix}[$f]\">$_opt0",$varr['frmcell']);
				$str .= $varr['frmcell'].'&nbsp; ';
				$this->fields_did[] = $f;
			}
		}
		unset($a_field);
		$str && trbasic('���޷�ʽ','',$str,''); 
	}
	
	// ¥��/¥�����: (3��һ���) ?¥��
	function fm_clouceng(){
		if(isset($this->fields['szlc']) && isset($this->fields['zlc'])){
			$str = "��<input type=\"text\" value='".@$this->predata['szlc']."' name=\"{$this->fmdata}[szlc]\" id=\"{$this->fmdata}[szlc]\" size= \"2\">�� ";
			$str .= " &nbsp; ��<input type=\"text\" value='".@$this->predata['zlc']."' name=\"{$this->fmdata}[zlc]\" id=\"{$this->fmdata}[zlc]\" size= \"2\">�� ";
			trbasic('¥��','',$str,'',array('guide'=>'������������-x��ʾ���µ�x�㡣'));
			$this->fields_did[] = 'szlc';
			$this->fields_did[] = 'zlc';
		}else{
			$cfields = array('szlc','zlc');
			$a_field = new cls_field; 
			foreach($cfields as $f){
			if(isset($this->fields[$f])){
				$cfg = $this->fields[$f]; 
				$a_field->init($cfg,isset($this->predata[$f]) ? $this->predata[$f] : '');
				$a_field->isadd = $this->isadd;
				$a_field->trfield($this->fmdata);
				$this->fields_did[] = $f;
			}	}
		}
	}

	// ������Ϣ: (3��һ���,������������,������Ϣ��guide,������Ϣ�Զ����ػ�Ա����)
	function fm_cfanddong($fields=array()){
		$curuser = cls_UserMain::CurUser();
		if($this->mc&&in_array($this->chid,array(2,3,117,118,119,120))){
			trbasic('������������','',makeradio('sendtype',array('1'=>'��������վǰ̨','0'=>'�����̨�ֿ�'),1),'');
		}
		if(($curuser->info['mchid']!=2)&&$this->mc){ // ����������,�Ǿ���������
			$this->fields_did[] = 'fdname';
			$this->fields_did[] = 'fdtel';
			$this->fields_did[] = 'fdnote';
			//return;
		}
		$cfields = empty($fields) ? array('lxdh','xingming') : $fields;
		$a_field = new cls_field; $str = '';
		foreach($cfields as $f){
			$cfg = $this->fields[$f]; 
			if($this->mc && $this->isadd){
				$cfg['guide'] = ' [<a id="user_info_link" href="?action=memberinfo" onclick="return showInfo(this.id,this.href)">��������</a>] ע:'.$cfg['cname'].' �����Զ����ػ�Ա���ϡ�';
			}
			$a_field->init($cfg,isset($this->predata[$f]) ? $this->predata[$f] : '');
			$a_field->isadd = $this->isadd;
			$a_field->trfield($this->fmdata);
			$this->fields_did[] = $f;
		}
		$u_dianhua = @$curuser->info['lxdh']; //echo 'xx'; print_r($curuser);
		$u_xingming = @$curuser->info['xingming'];
		if(!$u_xingming) $u_xingming = $curuser->info['mname'];
		if($this->mc && $this->isadd){
			echo "<script type='text/javascript'>
				var xingming = \$id('fmdata[".$fields[1]."]'),lxdh=\$id('fmdata[".$fields[0]."]');
				if(xingming) xingming.value = '$u_xingming';
				if(lxdh) lxdh.value = '$u_dianhua';
			</script>";	
		}
		unset($a_field);
	}

	// ����/�������: (n��һ���) fwjg-���ݽṹ,zxcd-װ�޳̶�,cx-����,fl-����
	function fm_ctypes($cfields=array()){
		$cfields = empty($cfields) ? array('fwjg','zxcd','cx','fl') : $cfields;
		$a_field = new cls_field; $str = ''; $pfix = $this->fmdata;
		foreach($cfields as $f){
			if(isset($this->fields[$f])){
				$this->fields[$f]['mode'] = '0';
				$cfg = $this->fields[$f];
				$a_field->init($cfg,isset($this->predata[$f]) ? $this->predata[$f] : '');
				$varr = $a_field->varr($this->fmdata);
				$_opt0 = "<option value='0'>-".$this->fields[$f]['cname']."-</option>";
				$varr['frmcell'] = str_replace("<select name=\"{$pfix}[$f]\">","<select name=\"{$pfix}[$f]\">$_opt0",$varr['frmcell']);
				if($f=='fl'){ // �������ʱ,����Ĭ����ʾ-����, �����[0=����]���ȥ��������ʾ-����-
					$varr['frmcell'] = str_replace("<option value=\"0\" selected=\"selected\">����</option>","",$varr['frmcell']);
					$this->isadd && $varr['frmcell'] = str_replace("selected=\"selected\"","",$varr['frmcell']);
				}
				$str .= $varr['frmcell'].'&nbsp; ';
				$this->fields_did[] = $f;
			}
		}
		unset($a_field);
		trbasic('����/����','',$str,''); 
	}

	// �������: (5��һ���) $h!=0���޳��Զ���䷿Դ���⹦��
	function fm_chuxing($cfields=array(),$h=0){
		$cfields = empty($cfields) ? array('shi','ting','wei','chu','yangtai') : $cfields;
		$a_field = new cls_field; $str = ''; $pfix = $this->fmdata;
		foreach($cfields as $f){
			if(isset($this->fields[$f])){
				$this->fields[$f]['mode'] = '0';
				$cfg = $this->fields[$f];
				$a_field->init($cfg,isset($this->predata[$f]) ? $this->predata[$f] : '');
				$varr = $a_field->varr($this->fmdata);
				if($h==0) $varr['frmcell'] = str_replace("<select ","<select id=\"{$pfix}[$f]\" onchange='auto_fillx()' ",$varr['frmcell']);
				$str .= $varr['frmcell'].'&nbsp; ';
				$this->fields_did[] = $f;
			}
		}
		unset($a_field);
		trbasic('����','',$str,''); 
	}
	
	// ����-��Ȧ (2��һ���,�жϲ���)
	function fm_rccid1(){
		$fcdisabled2 = cls_env::mconfig('fcdisabled2'); 
		if(!empty($fcdisabled2)){
			parent::fm_ccid(1);
		}else{
			relCcids(1,2,1,1,$this->fmdata,@$this->arc->archive['ccid1'],@$this->arc->archive['ccid2']);	
		}
		//$this->fm_resetCoids(array(1,2));
		resetCoids($this->coids, array(1,2));
	}
	// ����-վ�� (2��һ���,�жϲ���)
	function fm_rccid3(){
		$mconfigs = cls_cache::Read('mconfigs');
		$fcdisabled3 = $mconfigs['fcdisabled3'];
		if(empty($fcdisabled3)) relCcids(3,14,2,0,$this->fmdata,@$this->arc->archive['ccid3'],@$this->arc->archive['ccid14']);
		//$this->fm_resetCoids(array(3,14));
		resetCoids($this->coids, array(3,14));
	}
	
	// ��չ��js,��������-ȫѡ
	function fm_fyext($chid=3, $mc=1){
		$curuser = cls_UserMain::CurUser();
		echo "<script type='text/javascript'>
		var str = \"<br><input class='checkbox' type='checkbox' name='chkallfwpt' onclick=\\\"checkall(this.form, 'fmdata[fwpt]', 'chkallfwpt')\\\">ȫѡ\";
		var tmp_fwpts = document.getElementsByName('fmdata[fwpt][]')[1];
		if(tmp_fwpts){ //��Щģ���޴�dom����
			var tmp_td = tmp_fwpts.parentNode.parentNode.parentNode.getElementsByTagName('td')[0];
			tmp_td.innerHTML += str; //alert(tmp_tr);
		}
		var auto_fields = 'shi|ting|wei'.split('|');
		var auto_fnames = '��|��|��'.split('|');
		var isadd = '{$this->isadd}';

		//���� :�Զ���䷿Դ����
		function auto_fillx(){
			var tmp0 = \$id('fmdata[lpmc]').value,tmpx='';
			for(i=0;i<auto_fields.length;i++){
				var fid = auto_fields[i];
				var elm = \$id('fmdata['+fid+']');  
				if(elm && elm.value!='100'){
					tmpx += elm.value + auto_fnames[i]; 
				}
			}
			tmp0 += ' ' + tmpx;
			elm = \$id('fmdata[mj]'); 
			if(elm && elm.value>'0'){
				tmp0 += ' ' + elm.value + '�O';
			}
			var asubj = \$id('fmdata[subject]');
			if(asubj.value.length==0 || isadd=='1') asubj.value = tmp0; 
		} //  new Array(); С������ 3��2��1�� 157�O [�������ӻ���Ϊ����ִ������Զ���д����]
		</script>"; 
	}
	
	// ��Դ-С������,ѡ��
	//$mc:1Ϊ��Ա���ģ�0Ϊ��̨
	//$is_no_addxq; 1  Ϊǰ̨��ע�ᷢ����Դ
	function fm_clpmc($mc=1,$is_no_addxq=0){
		global $db, $tblprefix, $ckpre, $handlekey, $cms_top, $mcharset, $ckdomain, $ckpath;
		$pid3 = @$this->arc->archive['pid3']; //echo ",$pid3,"; //print_r($this->arc);
		$lpmc = @$this->arc->archive['lpmc'];
        
        if ( false === stripos($mcharset, 'UTF') )
        {
            $this->arc->archive['lpmc'] = cls_string::iconv($mcharset, 'UTF-8', @$this->arc->archive['lpmc']);
        }
        if ( !empty($ckdomain) )
        {
            # ȥ�����￪ͷ�ĵ�
            $cms_top = substr($ckdomain, 1);
        }
        # �򿪴���ʱ���³�ʼ��CK���ID�����ƣ���������ýű�ʱ��̳���ȥ
        msetcookie('fyid' . $handlekey, @$this->arc->archive['pid3']);
        msetcookie('lpmc' . $handlekey, urlencode(@$this->arc->archive['lpmc']));
        
        
		$mc = $this->mc;
		$add = $this->isadd;
		trhidden($this->fmdata.'[pid3]',$pid3);	
		
		trbasic('<font color="red"> * </font>С������',$this->fmdata.'[lpmc]',$lpmc,'text',array('w'=>60,'validate'=>' rule="text" must="1" mode="" rev="С������"','guide'=>'��������С�����ƻ�С����ַ��������'));
		//$mc_dir=MC_DIR;
		?>
		<script type="text/javascript">
		var fmdata = '<?php echo $this->fmdata; ?>';
		function createobj(element,type,value,id){
			var e = document.createElement(element);
			e.type = type;
			e.value = value;
			e.id = id;
			return e;
		}
		function set_select(obj,value,dochange){
			if(obj==null) return;
			for(var j=0;j<obj.options.length;j++){
				if(obj.options[j].value == value){
					obj.options[j].selected = true;	
					if(dochange && obj.onchange)obj.onchange();
				}	
			}
		}

        
        
		var lpmc = $id(fmdata+'[lpmc]');
		lpmc.setAttribute('autocomplete','off');
		var divout = document.createElement('DIV');
		var pid3 = document.getElementsByName('fmdata[pid3]')[0];
		with(divout.style){position = 	'relative';left = 0+'px';top = 0+'px';zIndex = 100;}
		var showdiv = "	<div style=\"border: 1px solid rgb(102, 102, 102); position: absolute; z-index: 1000; overflow-y: scroll; height: 300px; width: 500px; background-color: rgb(255, 255, 255);display:none;\" id=\"SuggestionDiv\"></div><iframe frameborder=\"0\" style=\"border: 0px solid rgb(102, 102, 102); position: absolute; z-index: 100; overflow-y: scroll; height: 300px; width: 500px; background-color: rgb(255, 255, 255);display:none;\"></iframe>";
		divout.innerHTML = showdiv;
		lpmc.parentNode.insertBefore(divout,lpmc.nextSibling);
		var divin = $id('SuggestionDiv');
		var aj=Ajax("HTML","loading");

        
		lpmc.onkeyup = function(){
            var ccid1 = $id('fmdata[ccid1]') ? $id('fmdata[ccid1]') : document.getElementsByName('fmdata[ccid1]')[0];
            var ccid2 = $id('fmdata[ccid2]');
            var ccid3 = $id('fmdata[ccid3]');
            var ccid14 = $id('fmdata[ccid14]');
            var address =$id('fmdata[address]');
            var dt = $id('fmdata[dt]');
            //��ѡ��С������ɾ��С�����ƣ���֮ǰ�Զ���ֵ����Щѡ�ȫ�����
            if(lpmc.value == ''){
                $id('fmdata[subject]').value = '';
                set_select(ccid1,0,1);
				set_select(ccid2,0,0);
				set_select(ccid3,0,1);
				set_select(ccid14,0,0);
                address.value = dt.value = '';
            }
            var urlpara = lpmc.value == ' ' ? '&keywords='+encodeURIComponent(lpmc.value) :'&keywords='+encodeURIComponent(lpmc.value.replace(/(^\s*)|(\s*$)/g,''));
			var urlfull = CMS_ABS + uri2MVC('ajax=ajaxloupan'+urlpara);	
			aj.post(urlfull,'',function(re){
				eval("var s = "+re+";"); 
				divin.style.display = '';
				divin.nextSibling.style.display = '';
				var str="<table width=\"480px\" cellspacing=\"0\" cellpadding=\"4\" border=\"0\" bgcolor=\"#ffffff\" class=\"search_select\" id=\"Suggestion\" style=\"top: -1px;\";><tbody><tr><td height=\"16\" align=\"center\" style=\"color: rgb(153, 153, 153); padding-left: 3px; background-repeat: repeat-x; background-position: center center;\" >����ѡ��Դ����С����û�к���С�������رգ�</td><td><a style=\"cursor:pointer;text-decoration:none; color:red;\" onclick=\"closediv()\">�ر�</a></td></tr>"
				for(i=0;i<s.length;i++){
                  str+="<tr onclick=\"sendaid("+i+")\" style=\"cursor:pointer\"><td index=\"1\" style=\"padding: 5px; color: rgb(51, 51, 51);\" ><span style=\"color: rgb(0, " + (s[i].aid==0 ? '080':'101') + ", 181);width:150px; display:block; float:left;\">"+s[i].subject+ (s[i].aid==0 ? '(��ʱС��)':'') + "</span><span style=\"display:block; float:left;width:280px; white-space:nowrap;overflow:hidden;\">��ַ��"+s[i].address+"</span></td></tr>";
				}
				<?php if(empty($is_no_addxq) && $mc){ ?>
				if(s.length <= 3){
					str += "<tr onclick=\"addlpinfo()\" style=\"cursor:pointer\"><td index=\"1\" style=\"padding: 5px; color: rgb(51, 51, 51);\" ><span style=\"color: rgb(0, 101, 181);width:150px; display:block; float:left;\">�����С����Ϣ</span></td></tr>";	
				}
				<?php } ?>
				str+="</tbody></table>";
				divin.innerHTML = str;
				
			function sendaid(i){
				pid3.value = s[i].aid;
				lpmc.value = s[i].subject;		
				set_select(ccid1,s[i].ccid1,1);
				set_select(ccid2,s[i].ccid2,0);
				set_select(ccid3,s[i].ccid3,1);
				set_select(ccid14,s[i].ccid14,0);
				var thumb = document.getElementsByName('fmdata[thumb]')[0];
				var dt = $id('fmdata[dt]');
				var address = $id('fmdata[address]');
				if(thumb)   thumb.value = s[i].thumb;
				if(dt)      dt.value =s[i].dt;
				if(address) address.value = s[i].address;
				setcookie('<?php echo $ckpre;?>fyid<?php echo $handlekey;?>', s[i].aid<?php echo (empty($cms_top) ? '' : ", null, '{$ckpath}', '.{$cms_top}'");?>);
				setcookie('<?php echo $ckpre;?>lpmc<?php echo $handlekey;?>', encodeURIComponent(s[i].subject)<?php echo (empty($cms_top) ? '' : ", null, '{$ckpath}', '.{$cms_top}'");?>);
				divin.style.display="none";
				divin.nextSibling.style.display = 'none';
				lpmc.onfocus(); //û�����,���Ϊ��״̬��ѡһ��С��,ѡȡ����֤��ʾ������ʧ
				auto_fillx(); 
			}
			window.sendaid=sendaid;	
			});
		}
        
        /**
         * �����ʱС���󣬰�������ϸ�ֵ����Ӧ�ķ�Դ����ҳ��
         */
		function sendaid2(vpid3,vname,vccid1,vccid2,vaddress,vdt){
			var pid3 = document.getElementsByName('fmdata[pid3]')[0];
			var ccid1 = $id('fmdata[ccid1]') ? $id('fmdata[ccid1]') : document.getElementsByName('fmdata[ccid1]')[0];
			var ccid2 = $id('fmdata[ccid2]') ? $id('fmdata[ccid2]') : document.getElementsByName('fmdata[ccid2]')[0];
			var address =$id('fmdata[address]');
			var dt = $id('fmdata[dt]');
				pid3.value = vpid3;
				lpmc.value = vname;
				set_select(ccid1,vccid1,1);
				set_select(ccid2,vccid2,0);	
				address.value = vaddress;
				dt.value = vdt;
		} 
		function closediv(){
			divin.nextSibling.style.display = 'none';
			divin.style.display="none";
		}
		function addlpinfo(){
			top.win = document.CWindow_wid; 
			return floatwin('open_addlp','?action=lpadd');
		}
		</script>
		<?
		$this->fields_did[] = 'lpmc';
	}	
	
	// �ܼۣ����ۣ����	���
	// ���ܼۡ�����ֶζ�����ʱ�����ؼ��㵥��JS
	function fm_cprice(){
		$flist = array('zj','mj','dj');
		$js = 0;
		foreach($flist as $k){ 
			if(isset($this->fields[$k])){
				$this->fm_field($k);
				$js++;
			}
			$this->fields_did[] = $k;
		}
		echo "<script type='text/javascript'>
				var input_id = '".$this->fmdata."';
				var mj = \$id(input_id + '[mj]'); ";
		if($js==3){
			echo "
				var zj = \$id(input_id + '[zj]');
				var dj = \$id(input_id + '[dj]');
				dj.readOnly = true;			
				zj.onkeyup = mj.onkeyup =function(){
					if(zj.value!='' && mj.value!='' && mj.value!='0'){
						val = (parseFloat(zj.value) * 10000 / parseFloat(mj.value)).toFixed(0);
						if(!isNaN(val)) dj.value = val;
					}
					auto_fillx();"; 
		}else{
			echo "
				mj.onkeyup =function(){
					auto_fillx();";
		}
		echo "}</script>";
	}
	
	// �ܱ�---�Զ�����[¥��/С��],������չ
	function sv_zhoubian($fmdata,$aid,$chid=8){
		ex_zhoubian($aid,$chid,@$fmdata['dt']);
	}
	
	// ��Դ,������չ����:���ʷ�Դ,��Դ��ͼ(���ڱ���ϼ�,ͼƬ֮��)
	function sv_fyext($fmdata,$chid=3){
		$curuser = cls_UserMain::CurUser();
		$c_upload = cls_upload::OneInstance();
		$db = _08_factory::getDBO();
		$tblprefix = cls_env::getBaseIncConfigs('tblprefix');
		$aid = $this->aid; //$chid = $this->chid;
		$sql = "SELECT count(*) FROM {$tblprefix}".atbl(121)." WHERE pid38='$aid' ";
		$imageNum = $db->result_one($sql);
		$imageNum = $imageNum ? $imageNum : 0; //ͼƬ����
		$wordNum  = 0;//������ռ�ַ�����
		$wordContent = 0;//�ų�ͼƬ�󴿺��ֵ�����
		$updarr = array();
		// ���ʷ�Դ����Դ��������4��������ͼ + ���� + ���� + ¥��+ 30�������ϵķ�Դ����
		if($this->isadd && $this->mc && in_array($chid,array(2,3))){
            $goodHouse = 1;//���ʷ�Դ��ʶ
            foreach(array('content','cx','fl','szlc','zlc') as $k){
                empty($fmdata[$k]) && $goodHouse = 0;
                if($k == 'content'){           
                    $content = htmlspecialchars_decode($fmdata['content']);//���Ѿ�ʵ�廯��htmlת����ͨ�ַ�
                    $imageNum += substr_count($content,"<img");//��ȡͼƬ����
                    $wordContent = strip_tags($content);//ȫ��ȥ��html����
                    $wordNum  = strlen($wordContent);//��ȡû��html����Ĵ������ַ�������                    
                    ($imageNum <4 || $wordNum < 60) && $goodHouse = 0;
                }
            }
			if($goodHouse){ //���ʷ�Դ
           		$updarr['goodhouse'] = 1; //$this->arc->updatefield('goodhouse',1);
				$curuser->basedeal('',1,1,'���ʷ�Դ����',1);
			}
		}
		$imageNum && $updarr['imgnum'] = $imageNum; // ��Դ��ͼ
		if(!empty($updarr)){ //����һ�����
			$db->update('#__'.atbl($chid), $updarr)->where("aid = $aid")->exec();	
		} //����$this->arc->updatefield()�������Ѿ�ʹ����sv_update()
		#exfy_imgnum($chid,$aid,$fmdata['content']); 
	}
	
	// ����ʱ��-��չ
	// 2,3,9,10,108 ģ��(��Ա����,�οͷ���)ʹ�ã���$oA->sv_update()֮ǰ����, 
	function sv_enddate($days=0){
		if(!$this->isadd) return;
		$_arc = $this->arc->archive;
		if(in_array($_arc['chid'],array(2,3,9,10,108,117,118,119,120))){
			if($_arc['chid']==108){ //��Ƹ
				$mconfigs = cls_cache::Read('mconfigs');
				$zpvalid = $mconfigs['zpvalid'];
				$days = empty($zpvalid) ? 30 : max(1,intval($zpvalid));
			}else{ // ��Դ/����
				$exconfigs = cls_cache::cacRead('exconfigs',_08_EXTEND_SYSCACHE_PATH);
				$_cfg = $exconfigs['fanyuan']; //����
				$_key = in_array($_arc['chid'],array(2,3,117,118,119,120)) ? 'fyvalid' : 'xqvalid';
				$this->arc->arcuser(); 
				$_gid = @$this->arc->auser->info['grouptype14']; 
				$_gid = empty($_gid) ? 0 : $_gid;
				$days = empty($_cfg[$_gid][$_key]) ? 30 : max(1,intval($_cfg[$_gid][$_key]));
			}
			$this->arc->setend($days);
		}
	}
	
		
	//����ʱ�䣺Ĭ��Ϊ�գ�����ʾ����Ϊ��
	function fm_enddate($title=''){
	    $title = empty($title)?'����ʱ��':$title;
		trbasic($title,$this->fmdata."[enddate]",isset($this->predata['enddate']) ? (empty($this->predata['enddate'])?'':date('Y-m-d',$this->predata['enddate'])) : '','calendar',array('guide'=>'���޽���ʱ��������','validate'=>makesubmitstr($this->fmdata."[enddate]",0,0,0,0,'date')));
	}
	
	
	/**¥���ĵ�ҳ��̬�����ʽ(������ʾ)
	*��ѡ����£�����ȡ��̨�ľ�̬�����ʽ��Ȼ���{aid}�滻��¥��ƴ��(Ҫע�����:�����ȡ���ľ�̬�����ʽ��������aid
	*��̶�Ϊ{$topdir}/{$y}{$m}/{$_pinyin}/{$addno}-{$page}.html)	
	*/
	function fm_customurl(){	
		$mconfigs = cls_cache::Read('mconfigs');
		$customurl = strstr($mconfigs['arccustomurl'],'{$aid}')?$mconfigs['arccustomurl']:"{$topdir}/{$y}{$m}/{$aid}/{$addno}-{$page}.html";
		$customurl = str_replace('{$aid}','{$_pinyin}',$customurl);
		if(!$this->isadd){			
			echo "<tr style=\"display:none;\"><td></td><td><input type=\"hidden\" name=\"fmdata[subject_pinyin]\" id=\"fmdata[subject_pinyin]\" value=\"".(cls_string::Pinyin($this->predata['subject']))."\"></td></tr>";
		}
		trbasic('�ĵ�ҳ��̬�����ʽ',"fmdata[customurl]",isset($this->predata['customurl']) ? $this->predata['customurl'] : '','text',array('guide'=>'����ΪĬ�ϸ�ʽ��{$topdir}������ĿĿ¼��{$cadir}������ĿĿ¼��{$y}�� {$m}�� {$d}�� {$h}ʱ {$i}�� {$s}�� {$chid}ģ��id  {$aid}�ĵ�id {$page}��ҳҳ�� {$addno}����ҳid��id֮�佨���÷ָ���_��-���ӡ�','w'=>50));	
		?>
        
		<script type="text/javascript">
		jQuery(document).ready(function() {
		  var customurl = jQuery("input[id='fmdata[customurl]']");
		  var url_val = '<?php echo $customurl;?>';
		  customurl.after("<input type='checkbox' id='is_pinyin' name='is_pinyin' style='margin-left:30px;'>");
		  jQuery("#is_pinyin").after("<span>��¥�����Ƶ�ƴ����Ϊ��̬��ʽ��һ����</span>");
		  jQuery("#is_pinyin").click(function(){
			  if(this.checked == true){			
				  customurl.val(url_val);
			  }else{
				  customurl.val('');
			  }
		  })	
		});
		</script>
		<?php
	}
	
	/**	
 	* ¥���ĵ�ҳ��̬�����ʽ(�������)
	* @param  string $fmdata['subject_pinyin']��ԭ����ת����ƴ��
 	*/	
	function sv_customurl(){
		global $timestamp;
		$fmdata = &$GLOBALS[$this->fmdata];
		$ename = 'customurl';
		$_pinyin = cls_string::Pinyin(trim($fmdata['subject']));//����ת��ƴ��

		if($this->isadd){
			$_url_str = str_replace('{$_pinyin}',$_pinyin,$fmdata[$ename]);
		}else{
			//�жϱ����Ƿ�ı�
			if($_pinyin == $fmdata['subject_pinyin']){
				$_url_str = str_replace('{$_pinyin}',$_pinyin,$fmdata[$ename]);
			}else{
				$_url_str = str_replace($fmdata['subject_pinyin'],$_pinyin,$fmdata[$ename]);				
			}
		}		
		$this->predata['nokeep'] = $this->arc->updatefield($ename,trim($_url_str));		
	}
    
    /**
     * ��Ѷ�ϼ���¥��
     *  
     */
    public function fm_info_to_building(){
        $caid = $this->predata['caid'];
        $catalog = cls_cache::Read('catalog',$caid);       
        $mconfigs = cls_cache::Read("mconfigs");      
        if($catalog['hejilanmu']){          
      		trbasic('<font color="red"> * </font>¥������',$this->fmdata.'[lpmc]','','text',array('w'=>60,'validate'=>' rule="text" must="1" mode="" rev="¥������"','guide'=>'��������¥�����ƽ�������'));
            trhidden($this->fmdata.'[lpaid]','');
            ?>
                <script type="text/javascript">
                    var cms_abs = '<?php echo $mconfigs['cms_abs'];?>';
                    var lpmc = '<?php echo $this->fmdata?>' + '[lpmc]';
                    var lpmc = $(document.getElementById(lpmc));
                    var lpaid = '<?php echo $this->fmdata?>' + '[lpaid]';
                    var lpaid = $(document.getElementById(lpaid));
                    var divout = document.createElement('DIV');  
                    with(divout.style){position = 	'relative';left = 0+'px';top = 0+'px';zIndex = 100;}
                    var divout = $(divout);
              		var showdiv = "	<div style=\"border: 1px solid rgb(102, 102, 102); position: absolute; z-index: 1000; overflow-y: scroll; height: 300px; width: 500px; background-color: rgb(255, 255, 255);display:none;\" id=\"showdiv\"></div>";              		
                    divout.html(showdiv);                                   
		            
               
                    divout.insertAfter(lpmc); 
                    lpmc.keyup(function(){
                        jQuery.getScript(cms_abs + uri2MVC('ajax=search_choice&chid=4&val='+lpmc.val()),function(){
                            var str = '<table width=\"480px\" cellspacing=\"0\" cellpadding=\"4\" border=\"0\" bgcolor=\"#ffffff\" class=\"search_select\" id=\"Suggestion\" style=\"top: -1px;\";><tbody><tr><td><a onclick=\"closeDiv()\">�ر�</a></td></tr>';
                            
                            for(var i=0; i<data.length;i++){
                                str+="<tr onclick=\"setinfo("+data[i].aid+",'" + data[i].subject + "')\" style=\"cursor:pointer\"><td index=\"1\" style=\"padding: 5px; color: rgb(51, 51, 51);\" ><span style=\"color: rgb(0, 101, 181);width:150px; display:block; float:left;\">"+data[i].subject+"</span><span style=\"display:block; float:left;width:280px; white-space:nowrap;overflow:hidden;\">��ַ��"+data[i].address+"</span></td></tr>";
                            }
                            str += '</tbody></table>';                          
                            
                            jQuery('#showdiv').html(str);
                            jQuery('#showdiv').css("display","block");
                           
                        });                 
  
                        
                    })
                    
                    function setinfo(aid,subject){
                        lpmc.val(subject);
                        lpaid.val(aid);
                        jQuery('#showdiv').css("display","none");  
                    }
                    
                    function closeDiv(){                        
                         jQuery('#showdiv').css("display","none");
                    }
             
                  
                </script>
            <?php
        }       
    }
    
    /**
     * ��Ѷ�ϼ���¥��
     *  
     */    
    public function sv_info_to_building(){
        $catalog = cls_cache::Read('catalog',1);
        $mconfigs = cls_cache::Read("mconfigs");  
        $db = _08_factory::getDBO();   
        if($catalog['hejilanmu']){
            $fmdata = &$GLOBALS[$this->fmdata];
            if(!empty($fmdata['lpaid'])){
                //ͨ��¥��aid����Ƿ����¥��               
                $fmdata['lpmc'] = trim($fmdata['lpmc']);
                $row = $db->select('COUNT(*) as num')->from('#__archives15 a')
                  ->where("a.aid = $fmdata[lpaid] ") 
                  ->exec()->fetch();
                if(!empty($row['num'])){
                    $db->insert( '#__aalbums', 
                        array(
                            'arid ' => 1, 
                            'inid ' => $this->aid, 
                            'pid' => $fmdata['lpaid'], 
                            'incheck ' => 1,                             
                        )
                    )->exec();
       
                }
                
                
            }
        }
        
        
    }
    
    /**
     * ����ʱ����趨�Զ���ֵ������˵��
       Ҫȷ�Ͽ������ڣ�����˵���ֶ�������
       �������˵�������ݣ��򲻸ı�
     */
    public function fm_kp_info(){
        $fields = cls_cache::Read('fields',4);
        if(!$fields['kpsj']['available'] || !$fields['kprq']['available']){
            return false;
        }
      ?>
      <script type="text/javascript">
        var kpsj_value = '<?php echo isset($this->predata['kpsj']) ? (empty($this->predata['kpsj'])?'':date('Y-m-d',$this->predata['kpsj'])) : ''?>';
        var kpsj_id = '<?php echo $this->fmdata?>' + '[kpsj]';
        var kprq_id = '<?php echo $this->fmdata?>' + '[kprq]';
        var kpsj = $(document.getElementById(kpsj_id));
        var kprq = $(document.getElementById(kprq_id));
        //�ȸ�����ʱ�丳ֵ�������ö�ʱ��������ֵ��ҳ�������õ�ֵ�Ƿ�ͬ����ȡ����
        setInterval(function(event){
            if(kpsj_value != kpsj.val()){                
                kpsj_value = kpsj.val();
                var kprqarr = kpsj_value.split('-');
                if(!kprq.val()){//�������˵�������ݣ��򲻸ı�
                    kprq.val(kprqarr[0]+'��'+kprqarr[1]+'��'+kprqarr[2]+'��');
                }           
            }
        },100);
      </script>
      <?php
    } 
    
    
  /**
   * ¥�̼۸�༭��ת����
   */
  public function fm_dj_edit_url(){
    	trbasic('�۸�༭','',"<a onclick=\"return floatwin('open_arcdj',this)\" href=\"?entry=extend&amp;extend=jiagearchive&aid=".$this->predata['aid']."&isnew=1\" class=\"scol_url_pub\"><font color='blue'>>>�༭¥�̼۸�</font></a>",'html22',array('guide'=>'��˿��Խ���¥�̼۸�༭ҳ��'));
  }
  
  /**
   * ¥�̷�����ʾ����¥��
   */
  
   public function lpfx_to_building(){
        $caid = $this->predata['caid'];
        $catalog = cls_cache::Read('catalog',$caid);       
        $mconfigs = cls_cache::Read("mconfigs"); 
        $lpmc = empty($this->arc->archive['lpmc'])?'':$this->arc->archive['lpmc'];
		$this->fields_did[] = 'lpmc';   
		if(empty($this->isadd)){
			trbasic('<font color="red"> * </font>¥������','',$lpmc,'');
			return;
		}
        $pid33 = empty($this->arc->archive['pid33'])?'0':$this->arc->archive['pid33'];
		trbasic('<font color="red"> * </font>¥������',$this->fmdata.'[lpmc]',$lpmc,'text',array('w'=>60,'validate'=>' rule="text" must="1" mode="" rev="¥������" autocomplete="off"','guide'=>'��������¥�����ƽ�������'));
		trhidden($this->fmdata.'[pid33]',$pid33);
		?>
			<script type="text/javascript">
				var cms_abs = '<?php echo $mconfigs['cms_abs'];?>';
				var lpmc = $($id('<?php echo $this->fmdata?>' + '[lpmc]'));
				var lpaid = $($id('<?php echo $this->fmdata?>' + '[pid33]'));
				var lpmcbid = '0', lpmcstr = '';
				var divout = document.createElement('DIV');  
				with(divout.style){position = 	'relative';left = 0+'px';top = 0+'px';zIndex = 100;}
				var divout = $(divout);
				var showdiv = "	<div style=\"border: 1px solid rgb(102, 102, 102); position: absolute; z-index: 1000; overflow-y: scroll; height: 300px; width: 500px; background-color: rgb(255, 255, 255);display:none;\" id=\"showdiv\"></div>";              		
				divout.html(showdiv);
				divout.insertAfter(lpmc); 
				
				lpmc.keyup(function(){
					if(lpmcstr===lpmc.val().toString()){ 
						jQuery(lpaid).val(lpmcbid);
						//console.log('11:'+lpmcstr+'::'+lpmc.val());
					}else{ //�ı��˾ͱ�0
						jQuery(lpaid).val(0);
						//console.log('12:'+lpmcstr+'::'+lpmc.val());	
					} // AND a.aid NOT IN(select pid33 FROM {$tblprefix}".atbl(113).")
					jQuery.getScript(cms_abs + uri2MVC('ajax=pagepick_loupan&aj_model=a,4,1&aj_thumb=thumb,120,90&aj_pagesize=50&aj_pagenum=1&leixing=1&isfenxiao=1&searchword='+encodeURIComponent(lpmc.val())+'&rescript=data'),function(){
						var str = '<table width=\"480px\" cellspacing=\"0\" cellpadding=\"4\" border=\"0\" bgcolor=\"#ffffff\" class=\"search_select\" id=\"Suggestion\" style=\"top: -1px;\";><tbody><tr><td><a onclick=\"closeDiv()\">�ر�</a></td></tr>';
						for(var i=0; i<data.length;i++){
							str+="<tr onclick=\"setinfo("+data[i].aid+",'" + data[i].subject + "','"+ data[i].ccid1 +"','"+ data[i].kprq +"','"+ data[i].kpsjdate +"')\" style=\"cursor:pointer\"><td index=\"1\" style=\"padding: 5px; color: rgb(51, 51, 51);\" ><span style=\"color: rgb(0, 101, 181);width:150px; display:block; float:left;\">"+data[i].subject+"</span><span style=\"display:block; float:left;width:280px; white-space:nowrap;overflow:hidden;\">��ַ��"+data[i].address+"</span></td></tr>";
						}
						str += '</tbody></table>';                          
						
						jQuery('#showdiv').html(str);
						jQuery('#showdiv').css("display","block");
						//lpmc.autocomplete = "off";
					   
					});                   
				})
				
				function setinfo(aid,subject,ccid1,kprq,kpsjdate){
					var kpsm = $id('fmdata[kprq]');
					lpmc.val(subject); 
					lpaid.val(aid);
					lpmc[0].onfocus();                       
					jQuery(kpsm).val(kprq);
					jQuery('#showdiv').css("display","none");  
					lpmcstr = subject;
					lpmcbid = aid; 
					$("select[name='fmdata[ccid1]'] option[value='"+ccid1+"']").attr("selected", true);
				}         
	
				//�ر�������        
				function closeDiv(){                        
					 jQuery('#showdiv').css("display","none");
					 //if(jQuery(lpaid).val()=='0') jQuery(lpmc).val('');
				}

			</script>
		<?php
	}


    // ��ҵ¥�̳��۳���-¥������,ѡ��
    //$mc:1Ϊ��Ա���ģ�0Ϊ��̨
    //$is_no_addxq; 1  Ϊǰ̨��ע�ᷢ����Դ
    function fm_ulpmc($mc=1,$is_no_addxq=0){
        global $db, $tblprefix, $ckpre, $handlekey, $cms_top, $mcharset, $ckdomain, $ckpath;
        $pid36 = @$this->arc->archive['pid36']; //echo ",$pid3,"; //print_r($this->arc);
        $lpmc = @$this->arc->archive['lpmc'];

        if ( false === stripos($mcharset, 'UTF') )
        {
            $this->arc->archive['lpmc'] = cls_string::iconv($mcharset, 'UTF-8', @$this->arc->archive['lpmc']);
        }
        if ( !empty($ckdomain) )
        {
            # ȥ�����￪ͷ�ĵ�
            $cms_top = substr($ckdomain, 1);
        }
        # �򿪴���ʱ���³�ʼ��CK���ID�����ƣ���������ýű�ʱ��̳���ȥ
        msetcookie('fyid' . $handlekey, @$this->arc->archive['pid3']);
        msetcookie('lpmc' . $handlekey, urlencode(@$this->arc->archive['lpmc']));


        $mc = $this->mc;
        $add = $this->isadd;
        trhidden($this->fmdata.'[pid36]',$pid36);

        trbasic('<font color="red"> * </font>¥������',$this->fmdata.'[lpmc]',$lpmc,'text',array('w'=>60,'validate'=>' rule="text" must="1" mode="" rev="¥������"','guide'=>'��������¥�����ƻ�¥�̵�ַ��������'));
        //$mc_dir=MC_DIR;
        ?>
        <script type="text/javascript">
            var fmdata = '<?php echo $this->fmdata; ?>';

            //����:
            function createobj(element,type,value,id){
                var e = document.createElement(element);
                e.type = type;
                e.value = value;
                e.id = id;
                return e;
            }

            //����:
            function set_select(obj,value,dochange){
                if(obj==null) return;
                for(var j=0;j<obj.options.length;j++){
                    if(obj.options[j].value == value){
                        obj.options[j].selected = true;
                        if(dochange && obj.onchange)obj.onchange();
                    }
                }
            }

            var lpmc = $id(fmdata+'[lpmc]');//<input type="text" size="60" id="fmdata[lpmc]" name="fmdata[lpmc]" value="" rule="text" must="1" mode="" rev="¥������" autocomplete="off">
            lpmc.setAttribute('autocomplete','off');
            var divout = document.createElement('DIV');
            var pid36 = document.getElementsByName('fmdata[pid36]')[0];
            with(divout.style){position = 	'relative';left = 0+'px';top = 0+'px';zIndex = 100;}
            var showdiv = "	<div style=\"border: 1px solid rgb(102, 102, 102); position: absolute; z-index: 1000; overflow-y: scroll; height: 300px; width: 500px; background-color: rgb(255, 255, 255);display:none;\" id=\"SuggestionDiv\"></div><iframe frameborder=\"0\" style=\"border: 0px solid rgb(102, 102, 102); position: absolute; z-index: 100; overflow-y: scroll; height: 300px; width: 500px; background-color: rgb(255, 255, 255);display:none;\"></iframe>";
            divout.innerHTML = showdiv;
            lpmc.parentNode.insertBefore(divout,lpmc.nextSibling);
            var divin = $id('SuggestionDiv');
            var aj=Ajax("HTML","loading");


            lpmc.onkeyup = function(){
                var chid = '<?php echo(in_array($this->predata['caid'],array(613,614)) ? 115 : 116); ?>';
                var title = chid == '115' ? 'д��¥' : '����';
                var ccid1 = $id('fmdata[ccid1]') ? $id('fmdata[ccid1]') : document.getElementsByName('fmdata[ccid1]')[0];
                var ccid2 = $id('fmdata[ccid2]');
                var ccidarr = ["ccid46", "ccid47", "ccid48", "ccid49"];
                var address =$id('fmdata[address]');
                var dt = $id('fmdata[dt]');
                //��ѡ��С������ɾ��С�����ƣ���֮ǰ�Զ���ֵ����Щѡ�ȫ�����
                if(lpmc.value == ''){//¥������input������value����
                    $id('fmdata[subject]').value = '';
                    set_select(ccid1,0,1);
                    set_select(ccid2,0,0);
                    for(var j = 0,ccida,ccidb,ccidc; j<ccidarr.length;j++){
                        if(ccida = $("input[id^='_fmdata["+ccidarr[j]+"]']")) ccida.removeAttr('checked');
                        if(ccidb = $("input[name='fmdata["+ccidarr[j]+"]']")) ccidb.val('');
                        if(ccidc = $("input[id^='fmdata["+ccidarr[j]+"]']")) ccidc.removeAttr('checked');
                    }
                    address.value = dt.value = '';
                }
                var urlpara = lpmc.value == ' ' ? '&keywords='+encodeURIComponent(lpmc.value) :'&keywords='+encodeURIComponent(lpmc.value.replace(/(^\s*)|(\s*$)/g,''));
                var urlfull = CMS_ABS + uri2MVC('ajax=ajaxbus_loupan&chid='+chid+urlpara);
                aj.post(urlfull,'',function(re){
                    eval("var s = "+re+";");
                    divin.style.display = '';
                    divin.nextSibling.style.display = '';
                    var str="<table width=\"480px\" cellspacing=\"0\" cellpadding=\"4\" border=\"0\" bgcolor=\"#ffffff\" class=\"search_select\" id=\"Suggestion\" style=\"top: -1px;\";><tbody><tr><td height=\"16\" align=\"center\" style=\"color: rgb(153, 153, 153); padding-left: 3px; background-repeat: repeat-x; background-position: center center;\" >����ѡ��"+title+"����¥�̣�û�к���¥�������ر�ֱ����д��</td><td><a style=\"cursor:pointer;text-decoration:none; color:red;\" onclick=\"closediv()\">�ر�</a></td></tr>"
                    for(i=0;i<s.length;i++){
                        str+="<tr onclick=\"sendaid("+i+")\" style=\"cursor:pointer\"><td index=\"1\" style=\"padding: 5px; color: rgb(51, 51, 51);\" ><span style=\"color: rgb(0, 101, 181);width:150px; display:block; float:left;\">"+s[i].subject+"</span><span style=\"display:block; float:left;width:280px; white-space:nowrap;overflow:hidden;\">��ַ��"+s[i].address+"</span></td></tr>";
                    }
                    str+="</tbody></table>";
                    divin.innerHTML = str;

                    function sendaid(i){
                        pid36.value = s[i].aid;
                        lpmc.value = s[i].subject;
                        //console.log($id('_fmdata[ccid49]'));alert('info');

                        set_select(ccid1,s[i].ccid1,1);
                        set_select(ccid2,s[i].ccid2,0);

                        for(var j = 0; j<ccidarr.length;j++){
                            var kccid = ccidarr[j], vccid;
                            try{
                                eval("vccid = s[i]."+kccid);
                            }catch(ex){ continue; }
                            if(!vccid) { continue; }

                            var accid = vccid.split(',');

                            for(var k = 0; k<accid.length;k++){
                                var v2ccid = accid[k]; if(v2ccid.length==0) { continue; }
                                var occid1 = $id('_fmdata['+kccid+']'+v2ccid); //.checked = true;
                                var occid2 = $id('fmdata['+kccid+']'+v2ccid); //.checked = true;
                                if (occid1) occid1.checked = true;
                                if (occid2){
                                    occid2.checked = true;
                                    $("input[name='fmdata[ccid49]']").val(accid);
                                }
                            }
                        }
                        //console.log('::'); return;

                        var thumb = document.getElementsByName('fmdata[thumb]')[0];
                        var dt = $id('fmdata[dt]');
                        var address = $id('fmdata[address]');
                        if(thumb)   thumb.value = s[i].thumb;
                        if(dt)      dt.value =s[i].dt;
                        if(address) address.value = s[i].address;
                        setcookie('<?php echo $ckpre;?>fyid<?php echo $handlekey;?>', s[i].aid<?php echo (empty($cms_top) ? '' : ", null, '{$ckpath}', '.{$cms_top}'");?>);
                        setcookie('<?php echo $ckpre;?>lpmc<?php echo $handlekey;?>', encodeURIComponent(s[i].subject)<?php echo (empty($cms_top) ? '' : ", null, '{$ckpath}', '.{$cms_top}'");?>);
                        divin.style.display="none";
                        divin.nextSibling.style.display = 'none';
                        lpmc.onfocus(); //û�����,���Ϊ��״̬��ѡһ��С��,ѡȡ����֤��ʾ������ʧ
                        //auto_fillx();
                    }
                    window.sendaid=sendaid;
                });
            }

            //����:�ر�¥���б��
            function closediv(){
                divin.nextSibling.style.display = 'none';
                divin.style.display="none";
            }
        </script>
        <?
        $this->fields_did[] = 'lpmc';
    }


}
