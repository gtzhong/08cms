
/**
 * ���ݻ�Ա��֤����id,mchid,��֤�ֶ� �Ƿ��ظ����������ǿɼ����ĵ�
 * eid:����ID
 * mctid:��֤����ID
 * mchid:��Աģ��ID
 * esend:�ύ��ťelement
 * ehidden:��չ�����element
 * msg:(����ʱ)�Զ�����Ϣ
 */
function checkUnique(eid,mctid,mchid,esend,ehidden,msg){
	var eform = $id(eid);
	aj = new Ajax('XML');
	aj.get(CMS_ABS+ uri2MVC({'ajax' : 'checkUnique', 'mctid' : mctid, 'mchid' : mchid, 'val' : eform.value, '__rnd' : (new Date).getTime()}), function(info){
		if(info.msg=='Exists'){ //�ظ�,��ʾ
			alert(msg + '\n\n���޸ĺ������');
			esend.disabled = true;
			try{ehidden.style.display = 'none';}catch(ex){}
		}else if(info.msg=='OK'){ //���ظ�,����ʾ
			esend.disabled = false;
			try{ehidden.style.display = '';}catch(ex){}
		}else{ // ������Ϣ����ʾ����
			alert(info.msg);
		} 
	});
}

/**
 * ��js����tools/ajax.php?action=memcert ������֤��Ĺ��ú���
 * ĳЩ�ĵ�,����,������Ա����ɾ����ֻ��vip�ȸ߼���Ա�ſ�ɾ��
 * @param  string mob ���� �ֻ�����ı���ID��
 * @return string mctid ���� �ֻ���֤����ID �� ����ģ�����������õ�ģ��ID��[register]��ͨ�����ID����ajax.php���ҵ���ص�[��������ģ��]
 
 * @return null
 */
function sendCerCode(mobid,mctid,repid){
	var mob = $id(mobid);
	var aj, tmp, step = 1;
	if(mob.value.length<10) return alert('�ֻ������ʽ����');
	if(!mob.value.match(/^\d{3,4}[-]?\d{7,8}$/))return alert('�ֻ������ʽ����');
	
	// check �ֻ������ظ�
	try{
		var mchk = mob.nextSibling;
		while (mchk.nodeType != 1 ) {
			mchk = mchk.nextSibling;
		} 
		if(mchk.className){
			if(mchk.className.indexOf('warn')>0) return alert('�ֻ������ظ�,�����...');
		}
	}catch(ex){}
	//console.log(mchk.className); return alert('11�ֻ������ظ�');
	
	var ckname = ((typeof($ckpre)=="undefined") ? '_fix_sendCerCode_' : $ckpre)+'_'+mobid.replace('[','').replace(']','')+'_'+mctid;
	var ckval = parseInt(getcookie(ckname)); //console.log(ckname+':'+ckval);
	if(ckval>0){
		return alert('�벻Ҫ�ظ��ύ�������ĵȴ���');
	}
	
	aj = new Ajax('XML');
	aj.get(CMS_ABS + uri2MVC('ajax=memcert&datatype=xml&mctid='+mctid+'&option=msgcode&mobile='+mob.value+'&__rnd='+(new Date).getTime()), function(info){
		
		if(!info.text){
			var now = new Date(); var nowTime = now.getTime();
			setcookie(ckname, 12321, 60*1000);
			alert('ȷ�����ѷ��͵����ֻ�����ע����ա�');
			if(repid) sendDelay(repid);
		}else{ //������Ϣ
			alert(info.text);
		}
		
	});

}

// sendDelay��ʱ���ã�
// (ids)ID�淶��id:ԭʼID,id_rep:�滻��ID,id_rep_in�滻ID�ڵļ���, html�������£�
// <a id="tel_code" href="javascript:" onclick="sendCerCode('$varname','$mctid');">��������ȷ���롿</a>
// <a id="tel_code_rep" style="color:#CCC; display:none"><span id="tel_code_rep_in">60</span>������»�ȡ</a> 
function sendDelay(id){ //alert('xxx');    
	org = $id(id);
	rep = $id(id+'_rep');
	rin = $id(id+'_rep_in');
	sec = parseInt(rin.innerHTML);
	if(sec>0){ 
		org.style.display = 'none';
		rep.style.display = '';
		rin.innerHTML--;
		setTimeout("sendDelay('"+id+"')",1000);
	}else{
		rin.innerHTML = 60; //������ʱ����
		org.style.display = '';
		rep.style.display = 'none'; 
	}    
}
