
function favorites(aid){
	if(aid == '' || !/^\d+$/.test(aid)){
		alert('��������');
		return false;
	}
	var aj = new Ajax();
	$.getScript(CMS_ABS + uri2MVC("ajax=sc_wenda&aid="+aid),function(msg){
		switch(data){
			case(1):
        		$.jqModal.tip('��ָ���ղض���','warn')
				break;
			case(2):
        		$.jqModal.tip('���ȵ�¼��Ա','warn')
				break;
			case(3):
        		$.jqModal.tip('��ǰ���ܹر�','warn')
				break;
			case(4):
        		$.jqModal.tip('��û�й�עȨ��','warn')
				break;
			case(5):
        		$.jqModal.tip('�ף����Ѿ��ղ���','warn')
				break;
			case(6):
        		$.jqModal.tip('�ղسɹ�','succeed')
				break;
		}
	});
	return false;
}
function chk_supplementary(form){
	var e = form.elements;
	if(e['added'].checked){
		if(e['fmdata[content]'].value == ''){
			alert('�������ⲻ��Ϊ�գ�');
			e['fmdata[content]'].focus();
			return false;
		}
	}
	if(e['addreward'].checked){
		if(e['rewardpoints'].value > parseInt(document.getElementById('jifens').innerHTML)){
			alert('��û���㹻�Ļ���׷�����ͣ�');
			return false;
		}
	}
	return true;
}