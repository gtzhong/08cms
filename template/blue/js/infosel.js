//�����ֻ��Ų�ѯ��Ϣ
function phonePost(fm){
	var numObj=mobObj.val(),
	urlbase={
	'ajax': 'pageload_arcdel', 
	'aj_model'     : 'a,2,3,9,10',
	'aj_pagenum'   : 1,
	'aj_pagesize'  : 20,
	'aj_ainfo'  : 1,
	'aj_nodemode': 0,
	'aj_whrfields'  : 'lxdh,%3D,'+numObj,
	'code'  : codeObj.val(),
	'tel'  : mobObj.val(),
	'datatype'     : 'json',
	'jsoncallback' : '?'
	}
	$("#Step01").hide();
	$("#selloading").show();
	$("#getnum").html(numObj);
	$.getJSON(CMS_ABS + uri2MVC(urlbase),function(info){
		$("#selloading").hide();
		if(info.length>0){
			for(var i=0;i<info.length;i++){
				$("#insertinfo").append(readData(info[i]));
			}
			$("#showinfo").show();
		}else{
			$("#noselinfo").show().html('<div class="noinfo">�ܱ�Ǹ������<span>"'+numObj+'"</span>�����ѯ�����Ϣ,��<a href="'+CMS_ABS+'info.php?fid=121">���²�ѯ</a></div>');
	  	}
	});
	timeOut(1800);
	return false;
}
//��ѯ����
function readData(o){
	var tpl= '<li>'
			+ '<input type="checkbox" name="pinfo" id="sel'+o.aid+'" value="'+o.aid+'"/>'
			+ '<label for="sel'+o.aid+'"><a href="'+o.arcurl+'"  target="_blank" title="'+o.subject+'">['+o.catalog+']'+o.subject+'<span>['+getLocalTime(o.createdate)+']</span></a></label>'
			+ '</li>';
	return tpl;
}

//�Ƿ�ȫѡ��Ϣ
$("#allsel").click(function(){
	if($(this).is(":checked"))
	$("#insertinfo input").prop("checked","checked");
	else
	$("#insertinfo input").prop("checked","");
});

//ȷ��ɾ����Ϣ
function delphinfo(){
	var tck='',checkObj=$("#insertinfo :checked");
	for(var i=0; i<checkObj.length; i++){
		tck+=','+checkObj.eq(i).val();
	}
	var delurl={
	'ajax': 'sms_arcdel', 
	'mod'     : 'arcxdel', 
	'act'   : 'send',
	'code'  : codeObj.val(),
	'tel'  : mobObj.val(),
	'ids': tck.substr(1),
	'datatype'     : 'json',
	'jsoncallback' : '?'
	}
	$.getJSON(CMS_ABS+uri2MVC(delurl),function(info){
		if(info.error){
			$.jqModal.tip(info.message,'error');
		}else{
			$("#insertinfo :checked").parent().remove();
			$.jqModal.tip(info.message,'succeed');
			if($("#insertinfo li").length==0){
				$.cookie($ckpre+'smscode_'+delurl.mod, null, { expires: -1, path:'/' }); // ɾ��cookie	
				$.jqModal.tip('��ɾ���ú��������Ϣ���������ز�ѯҳ','warn');
				window.location.href=CMS_ABS+"info.php?fid=121";
			}
		}
	});
}

//����ͣ��ʱ��
function timeOut(senconds){
	senconds--;
	setTimeout("timeOut("+senconds+")",1000);
	if(senconds==0) {
		$.jqModal.tip('��Ǹ����ͣ��ʱ��������������ز�ѯҳ','warn');
		window.location.href=CMS_ABS+"info.php?fid=121";
		}
	}
