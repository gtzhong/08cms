
// ��������
var commentOpt = {
    // ��������
    loadOpt : {
        'ajax'           : 'pageload_rems'
        , 'aj_model'     : 'cu,1' //ģ����Ϣ(a-�ĵ�/m-��Ա/cu-����/co-��Ŀ,3,1-ģ�ͱ�; ��:a,3,1)
        , 'orderby'      : 'cid' //�����ֶ�
        , 'ordermode'    : 0
        , 'aj_pagenum'   : 1
        , 'datatype'     : 'json'
        , 'aj_check'     : 1
        , 'aj_pagesize'  : 10
        , 'aid' : ''
    } ,
    // ֧������
    zcOpt : {
        'ajax'       : 'cuajaxpost'
        , 'cuid'     : '1'
        , 'aj_func'  : 'Vote'
        , 'fix'      : 'opt'
        , 'no'       : '1'
        , 'cutype'   : 'a'
        , 'datatype' : 'json'
    }
    , commentWrap : '#comment'
    , formWrap    : '.comment-form'
    , dataWrap    : '#comment-list'
    , ajax        : 'pageload_rems'
    , loadMore    : '#load-more'
    , autoCheck   : 1
    , template    : function(hf) {

        function getHf(_d) {
            if(!_d) return '';
            var H = '';
            for (var i = 0; i < _d.length; i++) {
                H += getHfTpl(_d[i]);
            };
            return H;
        }
        // �ظ�ģ��
        function getHfTpl(hf) {
            return '<div class="hfcon">'
                +       '<div class="plcon-hd">'
                +           '<span class="colorbl">'+hf.mname+'</span>&nbsp;' + getLocalTime(hf.createdate) + '&nbsp;�ظ�'
                +       '</div>'
                +       '<div class="plcon-bd">'+ showFace(hf.content) +'</div>'
                + '</div>';
        }
        var _len = this.subitems && this.subitems.length || 0;
        return !hf ? '<div class="pl-box">'
                    + '    <div class="plcon" data-zc="' + this.cid + '">'
                    + '        <div class="plcon-hd">'
                    + '            <span class="plcon-hd-tip">'+ this.louceng +'</span><span class="colorbl">' + this.mname + '</span>&nbsp;' + getLocalTime(this.createdate, 1)
                    + '        </div>'
                    + '        <div class="plcon-bd">'
                    +              showFace(this.content)
                    + '            <div class="zc_btn">'
                    + '                <a class="zc"><i class="ico08">&#xf089;</i>֧��<em>(' + (this.opt1?this.opt1:0) + ')</em></a> <a class="wyhf"><i class="ico08">&#xe629;</i>�ظ�</a>'
                    + '            </div>'
                    + '        </div>'
                    + '    </div>'
                    +      (_len ? getHf(this.subitems) : '')
                    + '</div>'
                : getHfTpl(this);
    }
}
// �ϲ�����
var newOpt = typeof cmtOptCustom != 'undefined' ? $.extend(true, {}, commentOpt, cmtOptCustom) : commentOpt;

function comment($, newOpt) {
    var louceng = 1;
    // ��������
    // ���ʱ����
    var loadMore = function () {
        var _param = newOpt.loadOpt;
        var $loadMore = $(newOpt.loadMore);
        $loadMore.css('display','block').addClass('load-more-ing').click(function() {
            loadMore();
        });     

        $.getJSON(CMS_ABS + uri2MVC(_param) + '&callback=?',function(d){
            if (d.length) {
                var html = '';
                $.each(d, function(i, obj) {
                    obj.louceng = louceng++ + '¥';
                    html += newOpt.template.call(obj);
                });
                $(newOpt.dataWrap).append(html);
            }
            $loadMore.removeClass('load-more-ing').css('display', d.length < _param.aj_pagesize?'none':'block');
            _param.aj_pagenum++;
        })
    }

    loadMore();

    $(newOpt.commentWrap)
    // ����ظ�
    .on('click', '.wyhf', function() {
        var $this = $(this);
        if ($this.hasClass('active')) {
            $hfFm.fadeToggle();  
        }
        else {
            $(newOpt.commentWrap).find('.wyhf').removeClass('active');
            var $plcon = $this.addClass('active').closest('[data-zc]');
            $hfFm.insertAfter($plcon).css('display', 'none').fadeIn().find('input[name="tocid"]').val($plcon.data('zc'));
        }
        
        if (!$hfFm.hasClass('inited')) {
            pageInit($hfFm);
            $hfFm.addClass('inited')
        }; 
       return false;
    })
    // ֧��
    .on('click','.zc',function() {
        var $zcbtn = $(this);
        newOpt.zcOpt.tocid = $zcbtn.closest('[data-zc]').data('zc');
        if (!$zcbtn.text().indexOf('��֧��')) return false;
        $.getJSON(CMS_ABS + uri2MVC(newOpt.zcOpt) + '&callback=?',function(d) {
            var now = parseInt($zcbtn.find('em').html().match(/\d+/));
            if (d.result == 'OK') now++;
            $zcbtn.html('��֧��<em>('+ now +')</em>').css({color:'#999'});
            if (d.result == 'OK') $('<i class="tip">+1</i>').appendTo($zcbtn).animate({top: -40,opacity:'hide'},800);
        });
    })

    //����
    $('.btn-face').append(
        $('<ul class="bqface">' + getSmiles() + '</ul>')
        .on('click','li',function() {
            $(this).closest('form').find('textarea')[0].value += '{:face'+this.value+':}';
        })
    )
    .mouseover(function() {$(this).addClass('hover');})
    .mouseout(function() {$(this).removeClass('hover')});
}

var $hfFm = $(newOpt.formWrap).clone().css('display','none').appendTo('body').find('.reg-wrap').data('alias', 'reply').end();
$(function () {
    comment(jQuery, newOpt);
})
// ����
var plFinished = 1;
function add_pl(fm){    
    if (plFinished == 0) return false;
    plFinished = 0;
    var btnHTML = fm.bsubmit.innerHTML;
    fm.bsubmit.innerHTML = '�ύ��...';
    $.getJSON(CMS_ABS + uri2MVC("ajax=cuajaxpost/"+$(fm).serialize()) + '&callback=?',function(d){

        if(!d.error){
            if (!fm.tocid.value) {
                //����
                d.cu_data.louceng = '���ڸոա�����';
                newOpt.autoCheck&&$(newOpt.dataWrap).prepend(newOpt.template.call(d.cu_data)).find('.pl-box').eq(0).fadeIn();
            } else{
                // �ظ�
                $(newOpt.template.call(d.cu_data,1)).insertAfter(fm.parentNode);
            };
            $.jqModal.tip(d.message,'succeed');
            $(fm).jqValidate('resetForm');
        }else{
            $.jqModal.tip(d.error,'info');
        }

        plFinished = 1
        fm.bsubmit.innerHTML = btnHTML;
    });

    $(fm).on('resetForm', function () {
        fm['regcode-img'].src+=1; 
    })
    return false;
}

/**
 * ���ֱ���ת����ͼƬ
 * @param  {[type]} content Ҫת��������
 * @return {[type]}
 */
function showFace(content){
    return content.replace(/\{\:face(\d+?)\:\}/g,"<img src='"+tplurl+"images/face/face$1.gif'/>");;
}

/**
 * ��ʼ������
 * @return {[type]} �����ַ���
 */
function getSmiles() {
    var faces='';
    for (var i = 1; i < 16; i++) {
        faces += "<li value='"+i+"'><img src='"+tplurl+"images/face/face"+i+".gif'></li>";
    }
    return faces;
}