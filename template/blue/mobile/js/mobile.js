// uri2MVC
function uri2MVC(uri,addFileName)
{var _split='/';if(!_08_ROUTE_ENTRANCE)
{var _08_ROUTE_ENTRANCE='index.php?/';}
(addFileName==undefined)&&(addFileName=true);var _uri='';if(typeof uri=='string')
{_uri=uri.replace(/&/g,_split).replace(/=/g,_split);}
else
{for(var i in uri)
{_uri+=(i+_split+uri[i]+_split);}}
var _endstr=_uri.charAt(_uri.length-1);if(_endstr==_split)
{_uri=_uri.substr(0,_uri.length-1);}
var newURI=addFileName?_08_ROUTE_ENTRANCE+_uri:_uri;if(!/domain/i.test(newURI))
{newURI+=(_split+'domain'+_split+document.domain);}
return newURI;}

// ��ʼ��
$(function() {
	Jingle.launch({
		// appType : 'muti' ,
        showPageLoading : true
	});
	//����ͼ��ת��
	$('.comment-list').html(function(a,b) {
	    	return showFace(b);
	    })

	$('body').on('click','#mapinfo_js',function(event) {
		event.preventDefault();
		$('#up_refresh_article').trigger('srl');
		/* Act on the event */
	});

});

$('section').one('pageshow',function() {
	var $header = $(this).find('header');

	$(this).find('article').andSelf().each(function() {
		var _this = this, str = _this.getAttribute('data-btn');
		str&&$.each(str.split(','), function(a,b) {
			$(_this).find('#'+b).show();
		})
	});

	if($header.find('#back').css('display')=='none') $('#logo').css('display','block');
	var $nav = $header.find('nav'),
		windW=$(window).width()>640?640:$(window).width(),
		titH=windW-$nav.eq(0).width()-$nav.eq(1).width()-10;
	this.title&&$header.find('.title').html(this.title).css({
		marginLeft:$nav.eq(0).width()
		,width:titH
	});
	//�����Ƭ��תʱtitle����������
	$header.css('position','fixed');


	if(this.getAttribute('data-footer')!='false') $(this).find('footer').removeClass('dn');
})


// ����
$('body').on('click', '.menu',function(e){
	J.popup({
		elId : 'tpl_popup_menu'
		, pos : 'top-second'
	})
	return false;
})

if(typeof(opt)!='undefined'){
	if ($(opt.wrap).parent().hasClass('active')) {//������ɺ�ǰ��section����ʼ��
		pullRefresh(opt,this.id);
	}else{
		$(opt.wrap).parent().one('pageshow',function() {//section��ʾʱ�ų�ʼ��
			pullRefresh(opt, this.id);
		})
	}
};

//���¹���������������
$(window).on('scroll', function() {
    var scrollh=$(document).height(),
        bua = navigator.userAgent.toLowerCase(),
        winH = $(window);
    if (bua.indexOf('iphone') != -1 || bua.indexOf('ios') != -1) {
        scrollh = scrollh - 140;
    } else {
        scrollh = scrollh - 80;
    }
    if ((winH.scrollTop() + winH.height()) >= scrollh) {
		$('section.active').trigger('srl');
    }
})

// ����ˢ�� �ĵ�
function pullRefresh(o, id) {
	var refreshOpt = {
	    _param : {
	        'aj_model'    : 'a,4,4', //ģ����Ϣ(a-�ĵ�/m-��Ա/cu-����/co-��Ŀ,3,1-ģ�ͱ�; ��:a,3,1)
	        'aj_check'    : 1 ,     //�Ƿ����(0/1������)
	        'aj_pagenum'  : 2 , //��ǰ��ҳ(����,Ĭ��2)
			'aj_pagesize'  : 10 ,
	        'datatype'    : 'json',
			'ordermode'    : 0
	    },
		filterUrl : '' ,
		ajax : 'pageload' ,
		type : 'pullUp'
	}
	var sid = id || 'index_section';
	var _opt = $.extend(true, {}, refreshOpt, o);
 	var isFinished = 1;

 	if (_opt._param.aj_pagenum == 1) {
		getDefData();
 	};

	// ������ҳ��ײ�ʱ���Զ����ظ���
	$('#' + sid).off('srl');
    $('#' + sid).on('srl', function() {
        if (isFinished) {
        		if(!isFinished) return;
        		isFinished = 0;
        		getDefData();
    		}
    })

	J.Refresh( _opt.wrap, _opt.type, function(){
	    if(!isFinished) return;
	    isFinished = 0;
		getDefData(this);
	})

	function getDefData(scroll) {
		$('#upinfo_js span').parent().show().end().eq(0).removeClass('icon-e61c').addClass('icon-e982');
		$.getJSON(CMS_ABS + uri2MVC('ajax='+_opt.ajax+'/' + $.param(_opt._param).replace(/\+/g,"%20") + _opt.filterUrl +'&callback=?'), function(data){
				var _html = '';
				if (data.length) {
					$.each(data,function(a,b) {
						_html += _opt.template.call(b);
					})
					$(_opt.dataWrap).append(showFace(_html));
		        	_opt._param.aj_pagenum > 1&&J.showToast('���سɹ�','toast top');
	    			setTimeout(function () {
		        		$('#upinfo_js span').eq(0).removeClass('icon-e982').addClass('icon-e61c');
						_opt._param.aj_pagenum++;
	    			}, 100);
		            isFinished = 1;
        			_opt._param.aj_pagesize > data.length&&$(_opt.wrap).find('.refresh-container').hide();
				}else{
			        	_opt._param.aj_pagenum > 1&&J.showToast('û������','error top');
			        	// ����ǵ�һҳ��û������,����ʾû������
			        	if(_opt._param.aj_pagenum==1){
			        		_html ='<li class="noinfo">~ ����������� ~</li>';
			        		$(_opt.dataWrap).append(showFace(_html));
					}
		    			setTimeout(function () {
		    					$(_opt.wrap).find('.refresh-container').hide();
			        		// _opt._param.aj_pagenum > 1?scroll.refresh():J.Scroll(_opt.wrap);
		    			}, 100);
				};

			})
	}


}

/**
 * [noLogInfo ��Ա����û�е�½ʱ���б���Ϣ��ʾ]
 * @return {[type]} [description]
 */
function noLogInfo(ement){
	var _html ='<li class="noinfo">~ ���¼��鿴�����Ϣ ~</li>';
	$(ement).find('ul.list').append(_html);
}

//popup
function popupExt(args) {
    var tgt = (typeof (args) == 'object' ? args.href.replace(args.baseURI,'') : args).replace('#','');

    J.popup({
		elId           : tgt
		, pos          : 'center'
		, url          : tgt.indexOf('//') > 0 ? tgt : null
		, showCloseBtn : 1
	})

    return false;
}

// loupan
//����ͼ
	// body...
typeof(_data)!='undefined'&&renderLine();
function renderLine(){
    //��������canvas��С
    var wh = {
			height : $(window).height()/2<200?200:$(window).height()/2,
			width : $('#section_container').width() - 20
		};
     $('#line_canvas').attr({width:wh.width, height:wh.height});
    var dataMin = Math.min.apply(null,_data.datasets[0].data)
    var _start = dataMin < 1000 ? 0 : dataMin - 1000;
    var data = _data;
    var line = new JChart.Line(data,{
        id : 'line_canvas' ,
        smooth : false ,
        fill : false ,
        scale : {
        		step : 5,//(�̶ȵĸ���)
			stepValue : 500,//(ÿ�����̶���֮��Ĳ�ֵ)
			start : _start //(��ʼ�̶�ֵ)
        },
        datasetShowNumber : 6
    });
    line.on('click.point',function(d,i,j){
        // J.alert(data.labels[i],d);
        setTimeout(function() {
        	J.popup({
	            html: '<div style="padding:10px;font-size: 20px;font-weight: 600;color:#E74C3C "><span class="f-peter-river">'+data.labels[i].replace('-','��')+'��</span><br>'+d+'Ԫ/m&sup2;</div>',
	            pos : 'center'
	        })
        },300);
        return false;
    });
    line.draw();
}
//���ñ���
function resetFm(f) {
	$(f).find('input[type="text"],textarea').val('').eq(0).focus();
	resetReg(f);
}

function resetReg(f){
	if(f.regcode) {
		f.regcode.value = '';
		f.regcodeimg.src+=1;
	}
}
// ����
var plFinished = 1;
function add_pl(fm){
	// cmtOpt._param.aid = fm.aid;
	if (plFinished == 0) return false;
	plFinished = 0;
	var btnHTML = fm.bsubmit.innerHTML;
	fm.bsubmit.innerHTML = '�����ύ...';
	$.getJSON(CMS_ABS + uri2MVC("ajax=cuajaxpost/"+$(fm).serialize()+"/jsoncallback=?"),function(d){

		if(!d.error){
			if (!fm.tocid||!fm.tocid.value) {
				//����
				d.cu_data.louceng = '���ڸոա�����';
				opt.autoCheck&&$(opt.dataWrap).prepend(opt.template.call(d.cu_data));
				// ��һ�γɹ�������~��������~��ʾ
				$(opt.dataWrap).children('.noinfo').hide();

			} else{
				// �ظ�
				$(opt.template.call(d.cu_data,1)).insertAfter(fm.parentNode);
				fm.style.display = 'none';
			};
        	J.showToast(d.message,'success top');
        	// J.Scroll(opt.wrap);

			// ����
			resetFm(fm);
		}else{
        	J.showToast(d.error,'info top');
			// ����
			resetReg(fm);
		}
		plFinished = 1
		fm.bsubmit.innerHTML = btnHTML;
	});
	return false;
}
// �ظ�
var $hfFm;

$('#comment-list').on('tap','.hf-btn',function(e) {
	if($hfFm){
		if (!$(this.parentNode).next('form').length) {
			$hfFm.insertAfter(this.parentNode).hide();
		};
		$hfFm.toggle().find('input[name="tocid"]').val($(this).attr('data-cid'));

	}else{
		$hfFm = $('#commu1').clone().insertAfter(this.parentNode).css({padding: 10, backgroundColor: '#EEE', borderRadius: 5, marginTop: 10});
		$hfFm.find('#regcodeimg').attr('src', CMS_ABS +'tools/regcode.php?verify=commu1_img1&t='+ parseInt(new Date().getTime() / 1e3)).tap(function() {
			this.src += 1;
		})
		.next()[0].value = 'commu1_img1';
	}
	$hfFm.find('input[name="tocid"]').val($(this).attr('data-cid'));
	e.stopPropagation();
})
/**
 * ���ֱ���ת����ͼƬ
 * @param  {[type]} content Ҫת��������
 * @return {[type]}
 */
function showFace(content){
	return content.replace(/\{\:face(\d+?)\:\}/g,"<img src='"+tplurl+"images/face/face$1.gif'/>");;
}


/**
 * ��ʽ��ʱ��
 * @return {[type]} 2014-10-10 10:10:10
 */
function getLocalTime(nS,T) {
	var myDate = new Date(parseInt(nS) * 1000);
	if (T==1) {
		var _nS = parseInt(new Date().getTime()/1000) - nS;
		switch(true){
			case _nS < 60:
				return '�Ÿո�';
			break;
			case _nS < 1800:
				return Math.floor(_nS / 60) + '����ǰ'
			break;
			case _nS < 3600:
				return '��Сʱǰ';
			break;
			case _nS < 86400:
				return Math.floor(_nS / 3660) + 'Сʱǰ';
			break;
			case _nS < 86400 * 30:
				return Math.floor(_nS / 86400) + '��ǰ';
			break;
			default :
				return Math.floor(_nS / 86400 / 30) + '����ǰ';
			break;
		}
	}else if(T==2){
	   	return myDate.getFullYear()+'-'+(myDate.getMonth()+1)+'-'+(myDate.getDate()<10&&'0'||'')+myDate.getDate()+' '+myDate.getHours()+':'+myDate.getMinutes()+':'+myDate.getSeconds();
	}else{
		return myDate.getFullYear()+'-'+(myDate.getMonth()+1)+'-'+(myDate.getDate()<10&&'0'||'')+myDate.getDate();
	}
}

//�ĵ��ղ����Ա�ղ�ajax
/**
 * @param id--��Ϊ���ֻ�Աmid���ĵ�aid(����)
 * @param typeVal--��Ϊ�������ͣ��ĵ��ղ�(Ĭ�ϲ���)�������ղ�(typeValΪm)
 * @param cuid--����cuid(Ĭ��Ϊ6)
 * @param other--new�·�\old�������ַ�\rent�������⣨����������ղ�¥�̣�
 */
function publicCollect(id,typeVal,cuid,other){
	isLogin();
	if(typeVal){
		var urlbase='ajax=cuajaxpost&cuid=11&cutype=m&tomid='+id+'&aj_func=Favor&pfield=tomid';
	}else{
		var dyohObj=other?'&'+other+'=1':'',dycuid=cuid?cuid:'6';
		var csfile = cuid=='7' ? 'cuscloupan' : 'cuajaxpost';
   		var urlbase='ajax='+csfile+'&cuid='+dycuid+'&cutype=a&aid='+id+''+dyohObj+'&aj_func=Favor&pfield=aid';
	}
	$.getJSON(CMS_ABS + uri2MVC(urlbase+"&datatype=json"), function(info){
		if(info.result=='OK'){
        	J.showToast('�ղسɹ���','success top');
		}else if(info.result=='Repeat'){
        	J.showToast('�����ظ��ղ�','info top');
		}
	});
	return false;
}
// ���ύ
function fyCummus(fm,t) {
	var btnHTML = fm.bsubmit.innerHTML;
	fm.bsubmit.innerHTML = '�ύ��...';

	var getCuid = fm.cuid.value;
	var ajaxscpit = (getCuid==8||getCuid==45)?'cutgbaoming':'cuajaxpost';
    $.getJSON(CMS_ABS + uri2MVC('ajax='+ajaxscpit+'/' + $(fm).serialize() +'/datatype=json/jsoncallback=?'),function(d) {
		if (!d.error) {
            J.showToast(t?t+'�ɹ���':d.message,'success top');
            J.closePopup();
			// ����
			resetFm(fm);
        }else{
        	J.showToast(d.error,'info top');
			resetReg(fm);
        	if(d.error=="��û�д˽����Ĳ���Ȩ�ޡ�"){
            	J.closePopup();
            	isLogin();
        	}
        };
		fm.bsubmit.innerHTML = btnHTML;
    });
    return false;
}
// ��֤��
function loadRegcode(args) {
	var $reg = $('form').find('input[name="regcode"]');
	args = args.split(',');
	$reg.each(function() {
		var fmId = $(this).closest('form')[0].id;
		if($.inArray(fmId,args)>=0){
				$('<img class="regcode-img" id="regcodeimg" src="'+ CMS_ABS +'tools/regcode.php?verify='+ fmId +'_img&t='+parseInt(new Date().getTime() / 1e3)+'" /><input type="hidden" name="verify" value="'+ fmId +'_img"/>').insertAfter(this)
				.tap(function() {
					this.src += 1;
				});
		}else{
			$(fmId == 'archive_fy'?this.parentNode:this).remove();
		}

	});
}

loadRegcode(vcodes);
// gotop
// var vendor = (function() {
// 	var ds  = document.createElement('div').style,
// 	vendors = 't,webkitT,MozT,msT,OT'.split(','),
// 	i       = 0,
// 	l       = vendors.length;

// 	for ( ; i < l; i++ ) {
// 		if ( vendors[i] + 'ransform' in ds ) {
// 			return vendors[i].substr(0, vendors[i].length - 1);
// 		}
// 	}
// 	return false;
// })()

// $('#gotop').tap(function() {
// 	var _S = $('section.active').find('article.active').children();
// 	_S.last().children().add(_S[0]).css((vendor && '-' + vendor + '-')+'transform', 'translate(0, 0) scale(1) translateZ(0)');
// 	return false;
// });


/***
��ҳ-���ظ��������
***/
function morePage(o){
    o.aj_page++;
    if(o.aj_page>o.aj_pmax) {
        J.showToast('û������','error top');
        $(loadopt.moreObj).hide();
        return false
    };
    $.get(o.url+'&page='+o.aj_page+'&inajax=1&domain='+document.domain,function(html){
        $(o.loadObj).append(html);
        //ͼƬ��ʱ����
		$(".detail-img img").length&&$(".detail-img img").each(function(){
			imgLoad(this, function() {
		        J.Scroll("#up_refresh_article");
		    });
		});
        setTimeout(function () {
        	J.Scroll(o.scrollObj)
        },500);
    });
}


//ͼƬ��ʱ����
// $(".detail-img img").length&&$(".detail-img img").each(function(){
// 	imgLoad(this, function() {
//         J.Scroll("#up_refresh_article");
//     });
// });
function imgLoad(img,callback) {
    var timer = setInterval(function() {
        if (img.complete) {
            callback(img)
            clearInterval(timer)
        }
    }, 50)
}
/**
 * @class _08cms.multiStore
 * @author Peace@08cms.com
 * @�ο�: http://www.cnblogs.com/zjcn/archive/2012/07/03/2575026.html#comboWrap
 * Demo: _08cms.locStore.setGroup('Xmkd_chid2','542476',10);
 */

function multiStore(flag){ // local,session
	this.parFlag = flag=='session' ? 'sessionStorage' : 'localStorage';
	this.parStore = flag=='session' ? window.sessionStorage : window.localStorage;
	// �Ƿ�֧��localStorage/sessionStorage
	this.ready = function(){
		return (this.parFlag in window) && (window[this.parFlag] !== null);
	};
	// ��չ : ������ñ���mnum��key(����������ʷ��¼)
	this.setGroup = function(keyid,nowkey,mnum){
		if(nowkey.length==0) return;
		if(!mnum) mnum = 10;
		var oldkeys = this.get(keyid);
		if(!oldkeys){
			var keystr = nowkey;
		}else{
			var oldarr = oldkeys.split(',');
			var keystr = nowkey; unum = 1;
			for(var i=0;i<oldarr.length;i++){
				if(oldarr[i]==nowkey || oldarr[i].length==0) continue;
				if(unum<mnum){
					keystr += ','+oldarr[i];
					unum++;
				}else{
					break;
				}
			}
		}
		keystr = keystr.replace(/[^0-9A-Za-z_\.\-\:\,\|\;]/g,''); // setGroup�����ַ����� \=\)\(\]\[  ����ascii��
		this.set(keyid,keystr);
	};
	// ����ֵ
	this.set = function(key, value){
		//��iPhone/iPad����ʱ����setItem()ʱ����ֹ����QUOTA_EXCEEDED_ERR������ʱһ����setItem֮ǰ����removeItem()��ok��
		if( this.get(key) !== null )
			this.remove(key);
		this.parStore.setItem(key, value);
	};
	// ��ȡֵ ��ѯ�����ڵ�keyʱ���е����������undefined������ͳһ����null
	this.get = function(key){
		var v = this.parStore.getItem(key);
		return v === undefined ? null : v;
	};
	this.each = function(fn){
		var n = this.parStore.length, i = 0, fn = fn || function(){}, key;
		for(; i<n; i++){
			key = this.parStore.key(i);
			if( fn.call(this, key, this.get(key)) === false )
				break;
			//������ݱ�ɾ�������ܳ��Ⱥ�������ͬ������
			if( this.parStore.length < n ){
				n --;
				i --;
			}
		}
	};
	this.remove = function(key){
		this.parStore.removeItem(key);
	}
	this.clear = function(){
		this.parStore.clear();
	};

}
var _08cms = {};
_08cms.locStore = new multiStore('local');
_08cms.sesStore = new multiStore('session');

/**
 * ��Ա��¼
 */
var loginfo = {};
loginfo.user_info = {}

// �Ƿ��¼
function isLogin() {
	$.getJSON(CMS_ABS + uri2MVC('ajax=is_login&datatype=json'),function(d) {
		loginfo = d;
		loginfo.user_info.mid != 0&&setLoginTpl(loginfo.user_info);

		if (loginfo&&loginfo.user_info.mid == 0) {
			popupExt('#tpl_popup_login');
			return false;
		};
	})
}
/**
 * ��Ա�˳�
 * @return {[type]}
 */
function logout() {
	J.showMask('�����˳�...');
	$.getScript(CMS_ABS + 'login.php?action=logout&datatype=js&varname=test', function(){
    	J.showToast(test.message,'success top');
  		setLoginTpl();
  		loginfo&&(loginfo.user_info.mid = 0);
		J.hideMask();
    })
	return false;
}

/**
 * ��¼ģ��
 * @return {[text]} ģ������
 */
function setLoginTpl(o) {
	var __html = o?'<div class="grid">'
				+ '    <div class="col-0">'
				+ '        <div class="mem-img">'
				+ '            <img src="'+o.image+'"  height="71" width="71"/>'
				+ '        </div>'
				+ '    </div>'
				+ '    <div class="col-1">'
				+ '        <div class="grid fz14 p10">'
				+ '            <div class="col-1">��ӭ,'+(o.qq_nickname||o.mname)+'</div>'
				+ '            <div class="col-0"><a onclick="logout()" class="block"><i class="icon icon-e762 left f-pomegranate"></i>�˳�</a></div>'
				+ '        </div>'
				+ '        <div class="fz14 p5">'
				+ '            <a href="'+CMS_ABS+'info.php?fid=125" class="button alizarin small block"><i class="icon-e641"></i>�޸�����</a>'
				+ '        </div>'
				+ '    </div>'
				+ '</div>'
				: '<div class="grid">'
				+ '    <div class="col-0">'
				+ '        <div class="mem-img">'
				+ '            <i class="icon-e63e"></i>'
				+ '        </div>'
				+ '    </div>'
				+ '    <div class="col-1">'
				+ '        <div class="col-1 p5">'
				+ '            <button class="small block" onclick="return isLogin();" ><i class="icon-e603"></i>��Ա��¼</button>'
				+ '        </div>'
				+ '        <div class="grid fz14">'
				+ '            <div class="col-1 p5"><a class="button carrot small block"><i class="icon-f059"></i>��������</a></div>'
				+ '            <div class="col-1 p5"><a href="'+mobileurl+'register.php" class="button alizarin small block"><i class="icon-f059"></i>��Աע��</a></div>'
				+ '        </div>'
				+ '    </div>'
				+ '</div>';

	$('#userLogin').html(__html);
}
/**
 * ��Ա��¼
 * @return {[type]}
 */
function _08Login(fm) {
	var btnHTML = fm.bsubmit.innerHTML;
	fm.bsubmit.innerHTML = '��¼��...'
	$.getScript(CMS_ABS + uri2MVC('ajax=check_login/' + $(fm).serialize() + '/datatype/js/varname=d/jsoncallback=?'), function(){
		loginfo = d ;
      	if( typeof(d.error) == 'undefined' || typeof(d.message) == 'undefined' ){
        	J.showToast('���������ظ�ʽ����','error top');
		}else if( d.error ){
        	J.showToast(d.error,'error top');
			resetReg(fm);
		}else{
			J.closePopup();
        	J.showToast('��¼�ɹ�','success top');
			d.user_info.mid != 0&&setLoginTpl(d.user_info);

			// ����
			resetFm(fm);
		}
		fm.bsubmit.innerHTML = btnHTML;
    })
    return false;
}

//tel�绰����
$('a[href^="tel:"]').click(function() {
	var mobile=$(this).attr('href'),
	    aTel = mobile.replace('-','').replace('ת',':').split(':');
 	var doTel=function(){
	 	if (typeof(uexCall)!='undefined') {
	 		uexCall.dial(aTel[1]);
	 	}else{
			location.href = 'tel:'+aTel[1];
		}
	}

 	if(aTel.length == 2) doTel();
 	else if(aTel.length == 3) {
 			location.href = 'tel:' + aTel[1] + ',' + aTel[2];
 	}
	return false;
});

//���ص�����
function goTop(acceleration, time) {
    acceleration = acceleration || 0.1;
    time = time || 16;
   var y=$(window).scrollTop(),
       speed = 1 + acceleration;
    $(window).scrollTop(Math.floor(y/speed));
    if ( y>0) {
        var invokeFunction = "goTop(" + acceleration + ", " + time + ")";
        window.setTimeout(invokeFunction, time);
    }
}
$("#gotop").on('click',  function(event) {
    event.preventDefault();
    goTop();
});
$(window).on('scroll',  function(event) {
    event.preventDefault();
    var stph=$(window).scrollTop();
    if(stph<120){
         $("#gotop").hide();
    }else{
       $("#gotop").show();
    }

});


//last
window.jQuery = window.Zepto;