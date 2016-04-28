var _08map
, aZB = []
, isFinished1 = 1
, zb = {
    'icon-e608' : '����'
    , 'icon-e6f9' : 'ѧУ'
    , 'icon-f0fe' : 'ҽԺ'
    , 'icon-e630' : '����'
    , 'icon-f07a' : '����'
    , 'icon-e611' : '¥��'
};

$(dtopt.mapWrap).css({
    width : $(window).width()
    , height : $(window).height()
})
// ���ص�ͼ
createMap(dtopt);
// ��һҳ
$('#tip').on('click', 'a', function() {
    dtopt._param.aj_pagenum++;
    createMap(dtopt);
})
// �ܱ�
$('#zb').on('click', 'a', function() {
    var t = this.className;
    var local = new BMap.LocalSearch(_08map, {
        /*renderOptions:{map: _08map}
        ,*/onSearchComplete:function(d) {
            $.each(aZB, function(a, b) {
                _08map.removeOverlay(b);
            })
            if (typeof(d)=='undefined') return false;
            $.each(d._pois, function(a, b) {
                // ����Զ��帲����
                addZB(b,t);
            })
        }
    });
    local.searchInBounds(zb[t], _08map.getBounds());
    $(this).toggleClass('active').siblings('a').removeClass('active');
    return false;
}).on('click', 'span', function() {
    $(this).parent().andSelf().toggleClass('active');
});

function addZB(info,type) {
    var myIcon = new BMap.Icon(tplurl+'mobile/images/dian.png', new BMap.Size(17,24), {imageSize:new BMap.Size(21, 30)});
    var marker = new BMap.Marker(new BMap.Point(info.point.lng,info.point.lat), {title:type});
    var _b ={
        dt_1:info.point.lng
        ,dt_0:info.point.lat
        ,subject:info.title
        ,address:info.address
        ,type:type
        ,classN:'zb-item'
    }
    var v = new SquareOverlay(_b);
    aZB.push(v);
    _08map.addOverlay(v);
    marker.addEventListener("click", function(){this.openInfoWindow(new BMap.InfoWindow(info.title));});
    return marker;
}


function createMap(opt1) {
    // �ٶȵ�ͼAPI����
    _08map = new BMap.Map(opt1.mapWrap.replace('#',''));
    _08map.centerAndZoom(new BMap.Point(opt1.defDt[1],opt1.defDt[0]), opt1.zoom);
    _08map.addControl(new BMap.ZoomControl());

    if(!isFinished1) return;
    isFinished1 = 0;
    $(_08map.getPanes().markerPane).parent().andSelf().parent().andSelf().css({
        width:'100%'
        , height:'100%'
    });

    $.getJSON(CMS_ABS + uri2MVC('ajax='+opt1.ajax+'/' + $.param(opt1._param).replace(/\+/g,"%20") + opt1.filterUrl +'&callback=?'), function(data){
        if (data.length) {
            $.each(data, function(a, b) {
                b.classN = "item";
                // ����Զ��帲����
                 _08map.addOverlay(new SquareOverlay(b,opt1));
            })
            $('#tip').html('ÿҳ'+opt1._param.aj_pagesize+'������ǰ��'+opt1._param.aj_pagenum+'ҳ<a>[��һҳ]</a>');
            isFinished1 = 1;
        }else{
            J.showToast('û������','info');
        };
    })
};

// �����Զ��帲����Ĺ��캯��
function SquareOverlay(o,o1){
    this.o = o;
    this.o1 = o1;
}
// �̳�API��BMap.Overlay
SquareOverlay.prototype = new BMap.Overlay();

// ʵ�ֳ�ʼ������
SquareOverlay.prototype.initialize = function(map){
    var _d = this.o;
    var _opt = this.o1;
    // ����map����ʵ��
    this._map = map;
    // ����divԪ�أ���Ϊ�Զ��帲���������
    var div = document.createElement("div");
    if (!_d.type) {
        // ���Ը��ݲ�������Ԫ�����
        $(div).addClass(_d.classN+' '+_d.classN+'-'+_d.ccid18)
        .on('click', function() {
            map.centerAndZoom(new BMap.Point(_d.dt_1,_d.dt_0),_opt.zoom);
            itemClick.call(_d);
        })
        div.innerHTML = this.o1.dttemplate.call(_d);
    }else{
        $(div).addClass(_d.classN+' '+_d.type)
        .on('click', function() {
            J.popup({
                pos : 'bottom'
                , html : '<div style="padding:10px">'+_d.subject+'<br/>'+_d.address+'</div>'
                , showCloseBtn : 1
            })
        });
    };
    // ��div��ӵ�������������
    map.getPanes().markerPane.appendChild(div);
    // ����divʵ��
    this._div = div;
    this._width = $(div).width()-5;
    this._height = $(div).height()+6;
    // ��Ҫ��divԪ����Ϊ�����ķ���ֵ�������øø������show��
    // hide���������߶Ը���������Ƴ�ʱ��API����������Ԫ�ء�
    return div;
}

// ʵ�ֻ��Ʒ���
SquareOverlay.prototype.draw = function(){
var _d = this.o;
// ���ݵ�������ת��Ϊ�������꣬�����ø�����
   var position = this._map.pointToOverlayPixel({lng: _d.dt_1, lat: _d.dt_0});

   this._div.style.left = position.x - this._width / 2 + "px";
   this._div.style.top = position.y - this._height / 2 + "px";
}

