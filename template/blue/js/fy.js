$.getScript(tplurl+"js/jq.cookie.js",function () {
    if (typeof(caid) == 'undefined') return false;
    var cookieName = "list_"+caid,nid,N = 10;//����cookie����������¼������
    HistoryRecord();
    //��¼���������ķ�Դ
    function HistoryRecord() {
        var historyp=null;
        nid = aid;
        if (nid == null || nid == "") return;
        //�ж��Ƿ����cookie
    	var opt = { expires: 60*60*24*30, path: '/' };
        if ($.cookie(cookieName) == null) $.cookie(cookieName, nid, opt);
        else{
            historyp = $.cookie(cookieName);
            var pArray = historyp.split(',');
            historyp = nid;
            var count = 0;
            for (var i = 0; i < pArray.length; i++) {
                if (pArray[i] != nid) {
                    historyp = historyp + "," + pArray[i];
                    count++;
                    if (count == N - 1) {
                        break;
                    }
                }
            }
            //�޸�cookie��ֵ
            $.cookie(cookieName, historyp, opt);
        }
    }
    //
    getBrowseFy();
})

function getBrowseFy() {
    $.getScript(CMS_ABS + uri2MVC("ajax=fangyuan&caid="+caid+"&aids="+$.cookie('list_'+caid+'')),function (){
        var rs   = fangyuan
        ,len     = rs.length
        ,newhtml = ''
        ,dw      = caid ==3?'��': 'Ԫ/��';

        for(var i=0;i<len;i++) {
            newhtml += "<li>"
                    +       "<span class='td1'><a href='" + rs[i]['arcurl'] + "' target='_blank'>" + rs[i]['subject'] + "</a></span><span class='td2'>"+rs[i]['mj']+"m&sup2;</span><span class='td3 fco'>" + (rs[i]['zj']!=0 ? rs[i]['zj']+dw:'����')+"</span>"
                    +   "</li>";
        }

        if (newhtml) $('#list_'+caid).after('<div class="coltit1"><h3 class="tit1">���������ķ���</h3></div><ul class="tlist2 bd-gray p10">'+newhtml+'</ul>');
    })
}

// ����ͼ
if(typeof(jsonData)!='undefined'){
    var options = {
        chart: {
            renderTo: 'container',
            type: "line"
        },
        //3���ߵ���ɫ
        colors:["#5689D6", "#BF5A2F", "#62AB00"],
        title: {
            text: jsonData.title,
            style:{
                color: '#666666',
                fontSize: '12px',
                fontFamily: 'arial'
            }
        },
        subtitle: {
            text: ""
        },

        xAxis: {
            categories: jsonData.month_s,
            tickmarkPlacement: "on",
            reversed:true,
            labels: {
                style: {
                    fontSize: "14px",
                    fontFamily: "Microsoft YaHei"
                },
                y: 25
            }
        },
        yAxis: {
            title: "",
            gridLineColor: "#D9D9D9",
            opposite: true,
            labels: {
                formatter: function() {
                    if (this.value == 0) {
                        return "����"
                    } else {
                        return this.value + "Ԫ"
                    }
                },
                style: {
                    fontSize: "14px",
                    fontFamily: "Microsoft YaHei"
                },
                y: 3
            }//,
            // min: f.setMin
        },
        
        tooltip: {
            crosshairs: true,
            shared: true,
            borderWidth:0,
            formatter: function() {
                var s = '<small>'+this.points[0].key+'</small>';
                $.each(this.points, function(i, point) {
                    s += '<br/>'+ point.series.name +': '+ point.y;
                });
                return s;
            }
        },
        //��������
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: "pointer"
            }
        },
        //�ڵ㸡����
        legend: {
            enabled: false
        },
        series: seriesData
    };
    $('#zst').length&&$('#zst').highcharts(options);
}

$('#tab-tit').on('fixed', function () {
    $(this).width(1200).find('.tab-info').css('display', 'block');
}).on('unfixed', function () {
    $(this).find('.tab-info').css('display', 'none');
})

// ������
//��Ϣ������»����(����: ������/�����ܶ�/�������·�)
function getMonthMoney1(lilv,total,month){
    var lilv_month = lilv / 12;//������
    return total * lilv_month * Math.pow(1 + lilv_month, month) / ( Math.pow(1 + lilv_month, month) -1 );
}

var zj = $('#zj').text()*1;
$('#sf').html(Math.round(zj*.3*100)/100);
$('#yg').html(Math.round(getMonthMoney1(0.0515, zj*.7, 240)*1000000)/100);

