/* ckeditor��� ��Ʒ���� */
if(typeof($) == 'undefined') function $(id){return document.getElementById(id);}
// ���ѡ����
function addSelect(forms){
    var len = forms.elements.length;
    var e, ids = getcookie('ids'), arcstr = getcookie('arcstr');
    for(var i = 0; i < len; ++i) {
        e = forms.elements[i];
        if(e.checked && e.name.indexOf('selectid') >= 0){
            if(ids.indexOf(e.value) < 0) {
                ids += ',' + e.value;
                arcstr += encodeURIComponent(',' + $('arc'+e.value).innerHTML);
    			$('show_select').innerHTML +=
                    '<span id="ss">&nbsp;&nbsp;<input type="checkbox" value="' + e.value +
                    '" name="checkeds[]" checked="checked" onclick="closed(this);" id="checkeds'+
                    e.value+'" title="'+$('arc'+e.value).innerHTML+'"/><label for="checkeds'+e.value+'" title="����ر�ѡ��">' + $('arc'+e.value).innerHTML +
                    '</label>';
            }
		}
    }
    setcookie('ids', ids);
    setcookie('arcstr', arcstr);
    return false;
}

/**
 * �ر�ѡ�еĽڵ�
 * @param object obj input����
 */
function closed(obj) {
    var ids = getcookie('ids'), arcstr = getcookie('arcstr');
    ids = ids.replace(','+obj.value, '');
    arcstr = arcstr.replace(','+obj.title, '');
    obj.parentNode.removeChild(obj.nextSibling);
    obj.parentNode.removeChild(obj);
    setcookie('ids', ids);
    setcookie('arcstr', arcstr);
}

// ��ʼ��ѡ����
function init() {
    setcookie("ids", "");
    setcookie("arcstr", "");
}
/* ckeditor��� ��Ʒ���� end */