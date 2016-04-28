function weituotab(cid){
	var li = $('weituoli' + cid),lis = li.parentNode.getElementsByTagName('LI');
	for(var i in lis) lis[i].className = '';
	li.className = 'act';
	var wtobj = $('weituo' + cid);
	var divs = wtobj.parentNode.getElementsByTagName('DIV');
	for(var i in divs)	if(divs[i].id && divs[i].id.indexOf('weituo') !== -1)	divs[i].style.display = divs[i].id == 'weituo' + cid ? '' : 'none';
}
function delWeituo(cid){
	var li = $('weituoli' + cid),ul = li.parentNode,div = $('weituo' + cid),pdiv = div.parentNode;
	ajax.get($cms_abs  + uri2MVC("ajax=delweituo&cid="+cid),function(result){
		if(result == 'SUCCEED'){
			ul.removeChild(li);
			pdiv.removeChild(div);
			var unode = ul.childNodes
			for(var i = 0;i < unode.length;i++) if(unode[i].tagName == 'LI'){
				unode[i].onclick();break;	
			}
		}else alert(result);
	},1);
}
function cancelWeituo(wid,cid){
	ajax.get($cms_abs + uri2MVC("ajax=cancelweituo&wid="+wid),function(result){
		if(result == 'SUCCEED'){
			var trobj = $('tr'+wid);
			trobj.parentNode.removeChild(trobj);
			$('hweituonum' + cid).innerHTML = parseInt($('hweituonum' + cid).innerHTML)-1; 
			$('nweituonum' + cid).innerHTML = parseInt($('nweituonum' + cid).innerHTML)+1; 
			if(parseInt($('nweituonum' + cid).innerHTML) > 0)document.getElementById('wtcontinue').style.display = 'block';
		}else alert(result);
	},1)
}
function modifyPrice(cid){
	if($('text'+cid)){cancelModify(cid);return;}
	var span = $('price'+cid),price = parseFloat(span.innerHTML);
	span.innerHTML = '<input id="text' + cid + '" value="'+price+'" type="text"><div><a  href="javascript:confirmModify(' + cid + ')">\u786e\u5b9a</a> <a href="javascript:cancelModify(' + cid + ',' + price + ')">\u53d6\u6d88</a></div>';
}
function cancelModify(cid,price){
	$('price'+cid).innerHTML = price;	
}
function confirmModify(cid){
	var price = parseFloat($('text' + cid).value);
	ajax.get($cms_abs + uri2MVC("ajax=modifyprice&cid=" + cid + "&zj=" + price),function(result){
		if(result == 'SUCCEED') $('price'+cid).innerHTML = price;
		else alert(result);
	},1);
}


