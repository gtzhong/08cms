//��ȡȷ����
var mobObj=$("[name='fmdata[lxdh]']"),codeObj=$("#msgcode");
//�Ƿ����ֻ���Ѷ���
$.getJSON(CMS_ABS + uri2MVC("ajax=sms_msend&mod="+isOpenMob+"&act=init&datatype=json"), function(info){
	$("#stloading").hide();
	if(info.error=='close'){
		$("#sendtophone").hide();
		$("#closephtip").show();
	}else{
		$("#sendtophone").show();
	}
});
function sendverCode(os){
	$.getJSON(CMS_ABS + uri2MVC("ajax=sms_msend&mod="+isOpenMob+"&act=code&tel="+mobObj.val()+"&datatype=json"), function(info){
		if(info.error){
			$.jqModal.tip(info.message,'error');
		}else{
			countdown(os);
			mobObj.prop("readonly","readonly");
			$.jqModal.tip('�ѷ��ͣ�1���Ӻ�����»�ȡ','succeed');
			$("#stampinfo").val(info.stamp);
		}
	});
}

var stime;
function countdown(senconds){
	if(senconds>0){
	    senconds--;
		$("#vcode").html('<span class="fcr" id="getminut">60</span>������»�ȡ').prop("disabled","disabled").css("cursor","no-drop");
		if(senconds<10) senconds='0'+senconds;
		$("#getminut").html(senconds);
		stime=setTimeout("countdown("+senconds+")",1000);
		//$("#subsmsbnt").prop("disabled","").removeClass('graybtn');
	}else{
		$("#vcode").html("�����ȡȷ����").prop("disabled","").css("cursor","pointer");
		mobObj.prop("readonly","");
		//$("#subsmsbnt").prop("disabled","disabled");
	}
}
// mobObj.focus(function(){
// 	if(codeObj.next().hasClass("pass")){
// 		$.jqModal.tip('�ɹ�ͨ���ֻ���֤��������������룬��ˢ��ҳ����д��','succeed');
// 		$("#vcode").remove();
// 		mobObj.prop("readonly","readonly");
// 	}
// });