
//���ʷ���
miniBlogShare('#zoom');
	//���ʷ���
	function miniBlogShare (con) {
	     //ѡ������
	    var funGetSelectTxt = function() {
	    	var str=null;
	    	if(document.selection) str=document.selection.createRange().text ;
	    	else str=document.getSelection();
	        return str.toString();
	    };
	    //ѡ�����ֺ���ʾ΢��ͼ��
		//ָ��λ��פ��ڵ�
		$('<img id="imgSinaShare" class="img_share" title="��ѡ�����ݷ�������΢��" src="'+tplurl+'images/sina_share.gif" /><img id="imgQqShare" class="img_share" title="��ѡ�����ݷ�����Ѷ΢��" src="'+tplurl+'images/tt_share.png" />').appendTo('body');
		//Ĭ����ʽ
		$('.img_share').css({display : 'none', position : 'absolute', cursor : 'pointer'});
	    $(con).on('mouseup',function(e) {
	        if (e.target.id == 'imgSinaShare' || e.target.id == 'imgQqShare') {return; }
	        e = e || window.event;
	        var sh = window.pageYOffset || document.documentElement.scrollTop || document.body.scrollTop || 0,
	            left = (e.clientX - 40 < 0) ? e.clientX + 20 : e.clientX - 40,
	            top = (e.clientY - 40 < 0) ? e.clientY + sh + 20 : e.clientY + sh - 40;
	        if (funGetSelectTxt()) {
	            $('#imgSinaShare').css({display : 'inline', left : left, top : top });
	            $('#imgQqShare').css({display : 'inline', left : left + 30, top : top });
	        } else {
	            $('#imgSinaShare')[0].style.display='none';
	            $('#imgQqShare')[0].style.display='none';
	        }
	    });
	    //�������΢��
	    $('#imgSinaShare').on('click',function() {
	        var txt = funGetSelectTxt(), title = $('title').html();
	        txt&&window.open('http://v.t.sina.com.cn/share/share.php?title=' + txt + ' �D�D ת���ԣ�' + title + '&url=' + window.location.href);
	    });
	     //�����Ѷ΢��
	    $('#imgQqShare').on('click',function() {
	        var txt = funGetSelectTxt(), title = $('title').html();
	        txt&&window.open('http://v.t.qq.com/share/share.php?title=' + encodeURIComponent(txt + ' �D�D ת���ԣ�' + title) + '&url=' + window.location.href);
	    });
	};