// search
var $sCate = $('#s-cate');
$sCate.mouseenter(function () {
    $(this).addClass('s-cate-hover');
})
.mouseleave(function() {
    $(this).removeClass('s-cate-hover');
})
.on('click', 'li', function () {
    var $this = $(this);
    var sOpt = $this.data('param');
    var fm = $sCate.closest('form')[0];

    $this.addClass('act').siblings('li').removeClass('act');
    $sCate.removeClass('s-cate-hover').find('.s-tit').html($this.html());
    $sCate.next('.placeholder').find('label').html(sOpt.searchword);
    fm.caid.value = sOpt.caid;
    fm.addno.value = sOpt.addno;
    fm.searchword.title = fm.searchword.placeholder = sOpt.searchword;
})
.find('.act').trigger('click');
// ��������
var $fixed = $('.fixed')
, $navDt = $fixed.find('.nav-dt')
, isKeep = $navDt.hasClass('keep');

$fixed.length &&
$fixed
.on('scrollUp', function () {
    $fixed.removeClass('fixed-fixed-down')
})
.on('scrollDown', function () {
    $fixed.addClass('fixed-fixed-down')
})
.on('fixed', function () {
    $fixed.addClass('fixed-fixed');
    isKeep && $navDt.removeClass('keep');
})
.on('unfixed', function () {
    $fixed.removeClass('fixed-fixed');
    isKeep && $navDt.addClass('keep');
})
.on('click', '.close', function () {
    $fixed.removeClass('fixed-fixed-down').off('scrollUp scrollDown');
})
.jqFixed($fixed.data());
// ���������б�
var $navHover = $('.nav').find('.hover');
$('.nav')
.on('mouseenter', 'li:not(".keep")', function () {
    var $this = $(this);
    if ($this.is($navHover)) return;
    $this.addClass('hover')
    $navHover.removeClass('hover')
})
.on('mouseleave', 'li:not(".keep")', function () {
    var $this = $(this);
    if ($this.is($navHover)) return;
    $this.removeClass('hover')
    $navHover.addClass('hover')
})
// ����
$('.condition')
.on('mouseenter', 'dd', function () {
    $(this).addClass('hover')
})
.on('mouseleave', 'dd', function () {
    $(this).removeClass('hover')
})

// ��¼
$.getScript(CMS_ABS + uri2MVC('ajax=is_login&varname=test&datatype=js'),function() {
    test.user_info.mid != 0&&setLoginTpl(test.user_info);
})
/**
 * ��Ա�˳�
 * @return {[type]}
 */
function logout() {
    $.getScript(CMS_ABS + 'login.php?action=logout&datatype=js&varname=logoutInfo', function(){
        $.jqModal.tip(logoutInfo.message,logoutInfo.error?'error':'succeed');
        setLoginTpl();
        test.user_info.mid = 0;
    })
    return false;
}

/**
 * ��Ա��¼
 * @return {[type]}
 */
function _08Login(obj) {
    var $obj = {
        'cmslogin' : obj.cmslogin.value
        , 'username' : obj.username.value
    }
    obj.cmslogin.value = '��¼��...';
    obj.username.value = encodeURIComponent(obj.username.value);
    $.getScript(CMS_ABS + uri2MVC('ajax=check_login&varname=test&datatype=js/'+$(obj).serialize()), function(){
        if( typeof(test.error) == 'undefined' || typeof(test.message) == 'undefined' ){
            $.jqModal.tip('���������ظ�ʽ����','error');
        }else if(test.error){
            $.jqModal.tip(test.error,'error');
        } else{
            $(obj).closest('.modal').jqModal('hide');
            test.user_info.mid != 0 && setLoginTpl(test.user_info);
            $(obj).jqValidate('resetForm');
            if (obj.regcode) $(obj.regcode).next().attr('src',function() {return this.src+1});
        }
        obj.cmslogin.value = $obj['cmslogin'];

    })
    obj.username.value = $obj['username'];
    return false;
}


/**
 * ��¼ģ��
 * @return {[text]} ģ������
 */
function setLoginTpl(o) {
    var __html = o ?
    '<span class="login-info">����,' + (o.qq_nickname||o.mname) + '<br>\
        <a href="'+ CMS_ABS +'adminm.php" ><i class="ico08 mr5">&#xe658;</i>����</a> &nbsp;&nbsp;\
        <a onclick="return logout();" href="'+ CMS_ABS +'login.php?action=logout" ><i class="ico08 mr5">&#xe762;</i>�˳�</a>\
    </span>' :
    '<a class="log-btn" onclick="$(\'#login-wrap\').jqModal(\'show\'); return false;" href="'+ CMS_ABS +'login.php" target="_self"> <i class="ico08 mr5">&#xf007;</i>��½</a>\
    <a class="log-btn" href="'+ CMS_ABS +'register.php" > <i class="ico08 mr5">&#xf14b;</i>ע��</a>';

    $('#userLogin').html(__html);
}

// ΢�ŵ�¼
$('#ico-login').click(function() {
    $('.wrap-pc,.wrap-wx').toggle();
    $(this).toggleClass('ico-pc');
    this.title = $($(this).hasClass('ico-pc') ? '.wrap-pc' : '.wrap-wx').data('title');
    if ($(this).hasClass('ico-pc')) {
        $('.wrap-wx').trigger('load-wx');
    }
});
// ΢�Ŷ�ά��
$('.wrap-wx').on('load-wx', function (e) {
    var oWxImg = $(e.target).find('img')[0];
    oWxImg.src = tplurl + 'images/blank.gif';
    $.getScript(CMS_ABS + uri2MVC('ajax=is_login&getsid=1&datatype=js&varname=data'), function() {
        if (data.getsid) {
            oWxImg.src = CMS_ABS + uri2MVC('weixin=show_qrcode&scene_id=' + data.getsid + '&expire_seconds=600');
        }
    });
})

$('#wx-tag-tip').hover(function() {
    $('#wx-login-tip').fadeToggle(300);
});

$('#wx-refresh').click(function () {
    $('.wrap-wx').trigger('load-wx');
})

!function ($) {
    !('placeholder' in document.createElement('input')) &&
    $('input[placeholder], textarea[placeholder]').each(function(){
        var $el = $(this);
        var _pla = $('<label class="placeholder">' + $el.attr('placeholder') + '</label>').insertBefore(this).css({
                display : !this.value ? 'block' : 'none'
            })
            .click(function () {
                $el.trigger('focus');
            })

        $el.on('input propertychange change', function () {
            _pla[0].style.display = !this.value ? 'block' : 'none';
        })
    })

    var aVcodes = vcodes.split(',');
    $('.reg-wrap').each(function() {
        var $regWrap = $(this), regcode = $regWrap.data('regcode');
        if ($.inArray(regcode, aVcodes) >= 0) {
            var $regInput = $regWrap.show().find('input[name="regcode"]')
                            .attr({'data-init': '��������֤��', 'data-type': '*', 'data-offset': 1});

            $regInput.wrap('<div class="txt-wrap"></div>')
            var codeName = regcode + '_img' + ($regWrap.data('alias') || '');

            if ($regInput.hasClass('ajaxurl')) {
                $regInput
                .attr({
                    'data-url': CMS_ABS + 'index.php?/ajax/regcode/verify/' + codeName + '/datatype/json/domain/' + document.domain
                })
                .on('ajaxDone', function (e,res,fun) {
                    if (res == '��֤�����') fun('error', '��֤�����!');
                    else fun('pass');
                })
            };
            $('<span class="lbl">\
                <img class="regcode-img" name="regcode-img" src="'+ CMS_ABS +'tools/regcode.php?verify='+ codeName +'&t=" />\
                <input type="hidden" name="verify" value="'+ codeName +'"/>\
                <span class="msg">��һ��</span>\
            </span>')
            .insertBefore($regInput.parent())
            .on('click', function() {
                $(this).find('img')[0].src += 1;
            })
        }
        else $regWrap.append('<input type="hidden" name="verify" value=""/>');
    })
}(jQuery)

// hover
$('.hover-list').length && $('.hover-list').each(function(){$(this).on('mouseover','li',function(){$(this).addClass('hover').siblings().removeClass('hover');})})
$('.hover-list1').length && $('.hover-list1').each(function(){$(this).on('mouseover','li',function(){$(this).addClass('hover');}).on('mouseout','li',function(){$(this).removeClass('hover');})})

// ����¥����Ϣ���ֻ�
function sendLpInfo() {
    console.log('������ܻ�û��');
    alert('������ܻ�û��');
}

/**
 * @param oForm ������
 * @param fmTit ���ύ�ɹ�����ʾtitle
 * @param iswin �����Ƿ��Ե�������ʽ���֣�����д1����֮Ĭ�ϲ���
 * @returns {boolean}
 */

function fyCummus(fm,fmTit,iswin) {
    var cuid=fm.cuid.value;
    var ajaxscpit=(cuid==8||cuid==35||cuid==45)?'cutgbaoming': ( cuid==3 ? 'loupanduanx':'cuajaxpost');
    var fmbtn = $(fm).find('[type="submit"]')[0];
    var btnTxt = fmbtn.value;
    fmbtn.value = '�ύ��...'
    $.getJSON(CMS_ABS + uri2MVC('ajax='+ajaxscpit+'/' + $(fm).serialize() +'/datatype=json') + '&callback=?',function(d) {
        if (!d.error) {
            $.jqModal.tip(fmTit?fmTit+'�ɹ���':d.message,'succeed');
            // ����з��͵��ֻ�,��Ҫ�����ֻ�
            if (cuid == 3 && fm['fmdata[dyfl]5'] && fm['fmdata[dyfl]5'].checked) sendLpInfo();
            // ����
            fmregcode(fm,iswin);
        }else{
            if(d.error=="��û�д˽����Ĳ���Ȩ��!"){
                $('#login-wrap').jqModal('show')
            }else{
                $.jqModal.tip(d.error,'warn');
                if(fm['regcode-img']) fm['regcode-img'].src += 1;
            }
        };
        fmbtn.value = btnTxt;
    });
    return false;
}

function fmregcode(fm,iswin){
    if (iswin) $(fm).closest('.modal').jqModal('hide');
    $(fm).jqValidate('resetForm');
    if(fm['regcode-img']) fm['regcode-img'].src += 1;
}
//�ĵ��ղ����Ա�ղ�ajax
/**
 * @param id--��Ϊ���ֻ�Աmid���ĵ�aid(����)
 * @param typeVal--��Ϊ�������ͣ��ĵ��ղ�(Ĭ�ϲ���)�������ղ�(typeValΪm)
 * @param cuid--����cuid(Ĭ��Ϊ6)
 * @param other--new�·���̬\old�������ַ�\rent�������⣨����������ղ�¥�̣�
 */
function publicCollect(id,typeVal,cuid,other){
    if(test.user_info.mid == 0) {
        var $popLog = $('#login-wrap');
        $popLog.jqModal($popLog.data());
        return false;
    }
    if(typeVal){
        var urlbase='ajax=cuajaxpost&cuid=11&cutype=m&tomid='+id+'&aj_func=Favor&pfield=tomid';
    }else{
        var dyohObj=other?'&'+other+'=1':'',dycuid=cuid?cuid:'6';
        var csfile = cuid=='7' ? 'cuscloupan' : 'cuajaxpost';
        var urlbase='ajax='+csfile+'&cuid='+dycuid+'&cutype=a&aid='+id+''+dyohObj+'&aj_func=Favor&pfield=aid';
    }
    $.getJSON(CMS_ABS + uri2MVC(urlbase+"&datatype=json"), function(info){
        if(info.result=='OK'){
            $.jqModal.tip('�ղ���ӳɹ���','succeed');
        }else if(info.result=='Repeat'){
            $.jqModal.tip('�����ظ��ղ�','error');
        }
    });
}

/**
 * ��ʽ��ʱ��
 * @return {[type]} 2014-10-10 10:10:10
 */
function getLocalTime(nS,T) {
    var myDate = new Date(parseInt(nS) * 1000);
    var myDateStr = myDate.getFullYear() + '-' + (myDate.getMonth() + 1) + '-' + (myDate.getDate() < 10 ? '0' : '') + myDate.getDate();
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
        return myDateStr + ' ' + myDate.getHours() + ':' + myDate.getMinutes() + ':' + myDate.getSeconds();
    }else{
        return myDateStr ;
    }
}


