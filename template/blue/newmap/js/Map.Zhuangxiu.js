var Conditions,//ɸѡ����
    projectMarkers=[],//Markers
    projectInfo={},//�б���Ϣ
    districtMarkers=[],//����Markers
    districtAreaInfo=[],//����������Ϣ����
    historyProjectInfo={},//������ʷ����
    historyProjectMarkers=[],//������ʷMarkers
    districtAreaMarkers=[];//��������Marker
	
	//��̬�ж���ͼ�����ֿ��
function changescreenWandH() {
    //��̬�ж��Ҳ��ͼ�ĸ߶�
    var rightbarheight = $(window).height() - $('#boxhead').height()-$('#boxfooter').height(); //parseInt(document.body.clientHeight)-topbarheight;   
    $("#mapouterdiv").css({
        "height": rightbarheight
    });
    $("#divMap").css({
        "height": rightbarheight
    });
    //��̬�ж�����б�ĸ߶�     
    $("#leftwrapper").css({
        "height": rightbarheight
    });
    var lconnr1Height = $(window).height() - $('#search_result').offset().top;
    $('.lconnr1').css({height:lconnr1Height});
}

$(function(){
    initMap();//��ʼ��ͼ����
	//��̬�����������߱仯����ʼ�������ֿ��
    $(window).resize(function () {
        changescreenWandH();
    });
    //ģ���ʼ��
    var Control= new MapInitControl();
        Control.Init();   
});

/***************************ģ���ͼ������*******************************/


function MapInitControl(){
    //¥�̳�ʼɸѡ���� by louis
    function InitConditions(){
     var url = CMS_ABS + uri2MVC("ajax/newmap/entry/ConditionData/mode/mchid_11");
	 $.ajax({
	type:'get',
	async:false,
	cache:false,
	url:url,
	dataType:'json',			
	success:function(data){
		Conditions = data;
    	InitDistrictControl();
	}
	});  	
    }
//keyword ���� by louis
window.SearchByKeyword = function (){
            var value = $("#keyword").val();
            if(value==mapInfo.defaultKeyword || value==''){alert('������С����������');return;}
            searchHouseInfo.keyword = value;searchInfo.keyword = value;
            searchHouseInfo.district = '';searchInfo.district = '';
            changeConditionTipsDiv();              
			getProjectPoint();
            showProjectData(0,10);
}
//�����ؼ� louis
    function InitDistrictControl(){
		var content = Conditions.district;
		if(content==undefined || !content.text) return;
		var ddText = '',ddValue = '';
		$("#search_cond_select_div").append('<div id="divDistrict" style="width:100%;"></div><div class="blank10"></div>');
		var container = $("#divDistrict");
		var ul = $('<ul id="ulDistrict" class="ulDistrict">');
		var contentLength = content.text.length;
		ul.append('<li class="act"><a selecttype="district" district="0"> ���� </a></dt>');		
		for(var i = 0; i < contentLength; i++){
			ddText = content.text[i];
			ddValue = content.value[i];
			ul.append('<li><a selecttype="district" district="'+ddValue+'">' + ddText + '</a></li>');
		} 
        
		//�����Ĵ�������
        ul.find("li").bind("click", function () {
            $(this).siblings().removeClass('act').end().addClass('act');
            var district = $(this).find("a").attr("district");
            if(searchHouseInfo.district!=district){
               searchInfo.district = $(this).find("a").html();
			   searchHouseInfo.district = district;
			   searchHouseInfo.projpageindex = 1;
               $("#spnDistrictTitle").html(searchInfo.district).attr("district",searchHouseInfo.district);	
               changeConditionTipsDiv();              
			   getProjectPoint();
               showProjectData(0,10);
            }	
        });		
		//����Ч��		
		container.append(ul); 
    }

  
//ɸѡ������ʾ
function changeConditionTipsDiv() {
    var conditionDivShow = false;
    var html = "";
    $("#conditionDiv_tip").empty();
    //keyword
    if (searchHouseInfo.keyword != "") {
        $('#keyword').val(searchHouseInfo.keyword);
    } else {
        $('#keyword').val('');
    }
	//����
	if (searchHouseInfo.district != "") {
        //html = '<a class="xzjg" name="cleardistrict">' + searchInfo.district + '</a>';
        //$("#conditionDiv_tip").append(html);
        conditionDivShow = true;
        $('a[name="cleardistrict"]').bind("click", function () {
            map.centerAndZoom(new BMap.Point(mapInfo.px, mapInfo.py), mapInfo.initZoom);
            searchInfo.district = "";searchHouseInfo.district = "";
            changeConditionTipsDiv();
            getProjectPoint();
            showProjectData(0,10);
           
        });
    } else {
        $("#spnDistrictTitle").html("����");
    } 
	
    if (!conditionDivShow) {
        $("#conditionDiv").hide();
        //��̬�ж�����б�ĸ߶�
        leftbarheight = $(window).height() - 180;
        $("#resultcontainer").css({
            "height": leftbarheight
        });
    } else {
        $("#conditionDiv").show();
        //��̬�ж�����б�ĸ߶�
        leftbarheight = $(window).height() - 180 - $("#conditionDiv").height() - 11;
        $("#resultcontainer").css({
            "height": leftbarheight
        });
    }
}

//����¥��������ʾ
function getDistrictsPoint(){
    var url = CMS_ABS + uri2MVC("ajax/newmap/entry/DistrictPoint/type/"+mapInfo.maptype+'/');
    $.getJSON(url,function(data){
        if(data.project && 0<data.project.length){
            var project=data.project;  
            for(var i=0;i<project.length;i++){
                 var html='<div class="qp00" district="'+project[i].index+'" districtname="'+project[i].name+'"><a class="noatag"><div class="s1"><em><i class="arrow"></i>'+ project[i].name +'<span>|'+project[i].count+'��</span></em></div></a></div>'; 
                 var point = new BMap.Point(project[i].px,project[i].py);   
                 var mySquare = new SquareOverlay(point, 100,html,1,"","",project[i].px,project[i].py,project[i].name,"","");
                 map.addOverlay(mySquare);
                 mySquare.addEventListener("mouseover", function (){
					 $(this).find("div").first().addClass("qp01");
					 this.style.zIndex =100;
                 });
				 mySquare.addEventListener("mouseout", function (){
					 $(this).find("div").first().removeClass("qp01");
					 this.style.zIndex =-1;
                 });
                 //�������	 
                 mySquare.addEventListener("click", function (){
					 var districtname=$(this).find("div").first().attr("districtname");
					 var district=$(this).find("div").first().attr("district");
					 $("#spnDistrictTitle").html(districtname).attr("district",district);
					 searchHouseInfo.district = district;searchInfo.district = districtname;
                     searchHouseInfo.keyword = '';searchInfo.keyword = '';
					 changeConditionTipsDiv();
                     getHousePoint();
                 });
                 districtMarkers.push(mySquare);		 
              }
              //showHouseData();//��ʼ��չʾ���House��Ϣ�б�
              getHousePoint();
        }   
    });
}

//�����Ϣ�б�չʾ��ʽ
function changeListShow() {
        $("#projListDiv").hide();
        $("#house_transitListDiv").show();
        $("#houseListDiv").show();
}

//100�·�С��
function setMoreProjStatus(allcount){
    if(allcount>mapInfo.ViewVolume){
        if(searchHouseInfo.projpageindex<Math.ceil(allcount/mapInfo.ViewVolume)){
            $("#projturndiv").show();
            $("#closeprojturndiv").show();
            $("#lakuangdiv").css({top:45});
            $("#ViewVolume").html(mapInfo.ViewVolume);
            $("#change100proj").html("��һ��");
            $("#closeprojturndiv").bind("click",function (){
            $("#projturndiv").hide();
            $("#closeprojturndiv").hide();
           // $("#lakuangdiv").css({top:15});
            });
            $("#change100proj").unbind().bind("click",function(){
                searchHouseInfo.projpageindex=searchHouseInfo.projpageindex+1;
                getProjectPoint();
				showProjectData(0,10);
            });
        }else{
            $("#projturndiv").show();
            $("#closeprojturndiv").show();
            $("#lakuangdiv").css({top:45});
            $("#change100proj").html("����");
            $("#closeprojturndiv").bind("click",function (){
            $("#projturndiv").hide();
            $("#closeprojturndiv").hide();
            //$("#lakuangdiv").css({top:15});
            });
            $("#change100proj").unbind().bind("click",function(){
                searchHouseInfo.projpageindex=1;
                getProjectPoint();
				showProjectData(0,10);
            });
        }
    }else{
        $("#projturndiv").hide();
        $("#closeprojturndiv").hide();
        //$("#lakuangdiv").css({top:15});
    }
}



//�����Ϣ�б�
function showProjectData(start,end) {
	setMoreProjStatus(projectInfo.allcount);//����100��С��	
    projectMarkers = [];
    if(projectInfo.allcount>0){
        $('#no_search_result').hide();
        $('#have_search_result').show();
        var project = projectInfo.project;        
        var lcon = '';len = project.length;
        for(var i=0;i<len;i++){
            //if(i>=start&&i<end){
                //����б�
                lcon +='<div class="seajgtd" markerid="'+i+'" projcode="'+project[i].projcode+'"><div class="fll" style="padding-right: 5px;"></div><ul><li><strong class="orange">'+project[i].projname+'</strong></li><li>��ϵ�绰��<strong class="orange">'+project[i].tel+'</strong></li><li>��ַ��'+project[i].address+'</li></ul></div>';
                //�ұߵ�ͼչʾ
                var html='<div class="qp00" projcode="'+project[i].projcode+'" markerid='+i+' projname="'+project[i].projname+'" ><a class="noatag"><div class="s1"><em><i class="arrow"></i>'+ project[i].projname +'</em></div></a></div>'; 
                var point = new BMap.Point(project[i].px,project[i].py);
                var mySquare = new SquareOverlay(point,100,html,1,project[i].product,project[i].projcode,project[i].px,project[i].py,project[i].projname,project[i].address,project[i].addresslong);
                
                map.addOverlay(mySquare);
                var overrideMouseOut=function (){
                     $(this).find("div").first().removeClass("qp01");
                     this.style.zIndex =-1;
                };
                var overrideMouseOver=function (){
                     $(this).find("div").first().addClass("qp01");
                     this.style.zIndex =100;
                };
                mySquare.addEventListener("mouseover", overrideMouseOver);
                mySquare.addEventListener("mouseout", overrideMouseOut);
			/*
            }else{
                //�ұߵ�ͼչʾ
                var html = '';
                var html = '<div class="smallmarker" markerid='+i+' projcode="'+project[i].projcode+'" projname="'+project[i].projname+'"><a class="noatag"><div class="sopenk" style="display: none;" >'+project[i].projname+'</div><div class="sqipo" onmouseover="this.className=\'sqipoa\'" onmouseout="this.className=\'sqipo\'"></div></a></div>';
                var point = new BMap.Point(project[i].px,project[i].py);
                var mySquare = new SquareOverlay(point,100,html,1,project[i].purpose,project[i].projcode,project[i].px,project[i].py,project[i].projname,project[i].address,project[i].addresslong);
                var overrideMouseOver = function(){
                    $(this).find('.sopenk').show();
                }
                var overrideMouseOut = function(){
                    $(this).find('.sopenk').hide();
                }
                map.addOverlay(mySquare);
                mySquare.addEventListener("mouseover", overrideMouseOver);
                mySquare.addEventListener("mouseout", overrideMouseOut);
            }
			*/
                //����б�                
                $("#house_result_wrap").html(lcon);
                $("#search_result .seajgtd").bind('mouseover',function(){
                    var projcode = $(this).addClass('active bj').attr('projcode');
                    $('#'+projcode+'_container').css('z-index',100).find('div').first().addClass('qp01');
                }).bind('mouseout',function(){
                    var projcode = $(this).removeClass('active bj').attr('projcode');
                    var obj = $('#'+projcode+'_container');
                    obj.css('z-index',1).find('div').first().removeClass('qp01'); 
                }).bind('click',function(){
                    var markerid = $(this).attr('markerid');
                    var projcode = $(this).attr('projcode');
                    var project = projectInfo['project'][markerid];
                    var html = '<div class="openbox"><div class="openboxnr"><div class="title"><div class="close"><a onclick="closeMapInfoDiv();"><img src="'+$tplurl+'newmap/images/close.gif" width="10" height="10"></a></div><a href="'+project.url+'" target="_blank"><strong id="view_now_hs">'+project.projname+'</strong></a></div><div class="openboxnr01"><div class="sl"><a target="_blank" href="'+project.url+'"><img src="'+project.img+'" alt="'+project.projname+'" width="200" height="140"></a></div><div class="sr"><ul><li>��ϵ�ˣ�'+project.conactor+'</li><li>��ϵ�绰��'+project.tel+'</li><li><span>��˾��վ��'+project.net+'</span></li><li>��ַ��'+project.address+'</li><li><a href="'+project.url+'">�鿴�̼�����</a></li></ul></div><div class="clear"></div></div></div><div class="jt"></div></div>';
                    $('#maptip').html(html).show();
                    map.panTo(new BMap.Point(project.px, project.py));
                    setHistoryCookie(projcode);
                });
                changeListShow();
                //�ұߵ�ͼչʾ
                var overrideClick = function(){
                                    var markerid = $(this).find('div').first().attr('markerid');
                                    var project = projectInfo['project'][markerid];
                                    var html = '<div class="openbox"><div class="openboxnr"><div class="title"><div class="close"><a onclick="closeMapInfoDiv();"><img src="'+$tplurl+'newmap/images/close.gif" width="10" height="10"></a></div><a href="'+project.url+'" target="_blank"><strong id="view_now_hs">'+project.projname+'</strong></a></div><div class="openboxnr01"><div class="sl"><a target="_blank" href="'+project.url+'"><img src="'+project.img+'" alt="'+project.projname+'" width="200" height="140"></a></div><div class="sr"><ul><li>��ϵ�ˣ�'+project.conactor+'</li><li>��ϵ�绰��'+project.tel+'</li><li><span>��˾��վ��'+project.net+'</span></li><li>��ַ��'+project.address+'</li><li><a href="'+project.url+'">�鿴�̼�����</a></li></ul></div><div class="clear"></div></div></div><div class="jt"></div></div>';
                                    $('#maptip').html(html).show();
                                    var projcode = $(this).find('div').first().attr('projcode');
                                    setHistoryCookie(projcode);                                   
                                }
                mySquare.addEventListener("click", overrideClick);       
                projectMarkers.push(mySquare); 
        }
   }else{
        $('#have_search_result').hide();
        $('#no_search_result').show();
   }
   changescreenWandH();
}

function setHistoryCookie(projcode){//console.log($.cookie('08MapHistory'));
    if(null == $.cookie('08MapHistory')) $.cookie('08MapHistory',',');
    if($.cookie('08MapHistory').indexOf(','+projcode+',') > -1)  return;
    if($.cookie('08MapHistory').match(/,/g).length >= 11){
        $.cookie('08MapHistory',$.cookie('08MapHistory').replace(/,\d+,$/,','));  
    }
    $.cookie('08MapHistory',','+projcode+$.cookie('08MapHistory'));  
}

function deletehHistoryCookie(projcode){
    if(projcode){
        if($.cookie('08MapHistory').indexOf(','+projcode+',') > -1){
            var reg = new RegExp(','+projcode+',','g');
            $.cookie('08MapHistory',$.cookie('08MapHistory').replace(reg,','));
            $('#total_history_count').html($('#total_history_count').html() - 1);
        }
    }else{        
        $.cookie('08MapHistory',null);
        $('#total_history_count').html(0);  
    }
}

//���¥������
function removeProjectData(){
    for(var i=0;i<projectMarkers.length;i++){map.removeOverlay(projectMarkers[i]);}
    projectMarkers=[];
    projectInfo = {};
}

//���������ʷ¥������
function removehistoryProjectData(markerid){
    if(markerid){
        map.removeOverlay(historyProjectMarkers[markerid]);
        historyProjectMarkers[markerid] = null;
        historyProjectInfo.project[markerid] = null;
    }else{
        for(var i=0;i<historyProjectMarkers.length;i++){map.removeOverlay(historyProjectMarkers[i]);}
        historyProjectMarkers=[];
        historyProjectInfo = {};
    }

    
}


//¥������
function getProjectPoint(){
    removeProjectData();
    var bounds = map.getBounds(); 
	var sw = bounds.getSouthWest();
	var ne = bounds.getNorthEast();
    searchHouseInfo.x1=sw.lng;
    searchHouseInfo.y1=sw.lat;
    searchHouseInfo.x2=ne.lng;
    searchHouseInfo.y2=ne.lat;
	var urlParam = 'type/'+escape(mapInfo.maptype)+'/district/'+escape(searchHouseInfo.district)+'/x1/'+ escape(searchHouseInfo.x1) + '/x2/' + escape(searchHouseInfo.x2) + '/y1/' + escape(searchHouseInfo.y1) + '/y2/' + escape(searchHouseInfo.y2) + '/page/' + escape(searchHouseInfo.projpageindex)+'/keyword/'+escape(searchHouseInfo.keyword)+'/';
	var url = CMS_ABS + uri2MVC('ajax/newmap/entry/CommunityPointData/'+urlParam);
    $.ajax({
        type:'get',
        async:false,
        cache:false,
        url:url,
        dataType:'json',
        beforeSend: function(){
          $('#total_count').html('Ŭ��������...');
        },
        success:function(data){
		       projectInfo = data;
               $("#total_count").html('���ҵ�<em>'+(projectInfo.allcount?projectInfo.allcount:0)+'</em>���̼�');
        }
    });
}

//������ʷ¥������
function getHistoryProjectPoint(HistoryProject){
    removeProjectData();
    var bounds = map.getBounds(); 
	var sw = bounds.getSouthWest();
	var ne = bounds.getNorthEast();
    searchHouseInfo.x1=sw.lng;
    searchHouseInfo.y1=sw.lat;
    searchHouseInfo.x2=ne.lng;
    searchHouseInfo.y2=ne.lat;
	var urlParam = 'type/'+escape(mapInfo.maptype)+'/HistoryProject/'+escape(HistoryProject)+'/';
	var url = CMS_ABS + uri2MVC('ajax/newmap/entry/history/'+urlParam);
    $.ajax({
        type:'get',
        async:false,
        cache:false,
        url:url,
        dataType:'json',
        success:function(data){
               if(data){
                historyProjectInfo = data;
                $("#total_history_count").html(data.allcount);
               }    
        }
    });
}

//�����¼���
function sort(){
        $('.lstitle').children().bind('click',function(){
                $('.lstitle').children().removeClass('s1').addClass('s2');
                $(this).removeClass('s2').addClass('s1');
                if(this.id!='sort_default'){
                    var valueString = $(this).attr('value');
                    var lastWord = valueString.substr(-1,1);
                    var nowValue = valueString.substr(0,valueString.length-1);
                    if(lastWord=='0'){
                        nowValue += '1';
                        $(this).find('img').first().attr('src',$tplurl+'newmap/images/icon05b.gif');
                    }else{
                        nowValue += '0';
                        $(this).find('img').first().attr('src',$tplurl+'newmap/images/icon05a.gif');
                    }
                    $(this).siblings().each(function(i){                        
                            var siblingValueString = $(this).attr('value');
                                if(0!==i){
                                    var siblingValueString = siblingValueString.substr(0,siblingValueString.length-1); 
                                    $(this).attr('value',siblingValueString+'0');
                                    $(this).find('img').first().attr('src',$tplurl+'newmap/images/icon05.gif');
                                }
                        });
                    $(this).attr('value',nowValue);                    
                    $(this).parent().attr('value',nowValue);
                    searchHouseInfo.order = searchInfo.order = nowValue;
                }else{
                    var siblings= $(this).siblings();
                        siblings.each(function(i){
                            var valueString = $(this).attr('value');                            
                            var nowValue = valueString.substr(0,valueString.length-1); 
                            $(this).attr('value',nowValue+'0');
                            $(this).find('img').first().attr('src',$tplurl+'newmap/images/icon05.gif');
                        });
                    $(this).parent().attr('value','0-0');
                     searchHouseInfo.order = searchInfo.order = '0-0';
                }
                  getProjectPoint();
                  showProjectData(0,10);        
            });
}

//������ʷ������ʾ
function historyProjectShow(){
    if(historyProjectInfo.allcount>0){
        var con = '';
        var html = '';
        var project = historyProjectInfo.project;
        var len = project.length;
        historyProjectMarkers = [];
        for(var i=0;i<len;i++){
            con += '<div class="lhistory" markerid="'+i+'" projcode="'+project[i].projcode+'" onmouseover="this.className=\'lhistory bj\';" onmouseout="this.className=\'lhistory\';"><span><img src="'+$tplurl+'newmap/images/close.gif" alt="ɾ" width="10" height="10"></span><div><strong class="limitawid orange">'+project[i].projname+'</strong></div></div>';
            var html='<div class="qp00" projcode="'+project[i].projcode+'" markerid='+i+' projname="'+project[i].projname+'" ><a class="noatag"><div class="s1"><em><i class="arrow"></i>'+ project[i].projname +'</em></div></a></div>'; 
            var point = new BMap.Point(project[i].px,project[i].py);
            var mySquare = new SquareOverlay(point,100,html,1,project[i].purpose,project[i].projcode,project[i].px,project[i].py,project[i].projname,project[i].address,project[i].addresslong);
            map.addOverlay(mySquare);
            var overrideMouseOut=function (){
                     $(this).find("div").first().removeClass("qp01");                	 
                     this.style.zIndex =-1;
                };
            var overrideMouseOver=function (){
                     $(this).find("div").first().addClass("qp01");
                     this.style.zIndex =100;
                };
            var overrideClick = function(){
                                    var markerid = $(this).find('div').first().attr('markerid');
                                    var project = historyProjectInfo['project'][markerid];
                                    var html = '<div class="openbox"><div class="openboxnr"><div class="title"><div class="close"><a onclick="closeMapInfoDiv();"><img src="'+$tplurl+'newmap/images/close.gif" width="10" height="10"></a></div><a href="'+project.url+'" target="_blank"><strong id="view_now_hs">'+project.projname+'</strong></a></div><div class="openboxnr01"><div class="sl"><a target="_blank" href="'+project.url+'"><img src="'+project.img+'" alt="'+project.projname+'" width="200" height="140"></a></div><div class="sr"><ul><li>��ϵ�ˣ�'+project.conactor+'</li><li>��ϵ�绰��'+project.tel+'</li><li>��˾��վ��<span>'+project.net+'</span></li><li>��ַ��'+project.address+'</li><li><a href="'+project.url+'">�鿴�̼�����</a></li></ul></div><div class="clear"></div></div></div><div class="jt"></div></div>';
                                    $('#maptip').html(html).show();
                                    var projcode = $(this).find('div').first().attr('projcode');                                  
                                }                         
                mySquare.addEventListener("mouseover", overrideMouseOver);
                mySquare.addEventListener("mouseout", overrideMouseOut); 
                mySquare.addEventListener("click", overrideClick);
         historyProjectMarkers.push(mySquare);          
        }
        $('#browsing_history').html(con);        
        $('#browsing_history .lhistory').bind('mouseover',function(){
            var projcode = $(this).attr('projcode');
            $('#'+projcode+'_container').css('z-index',100).find('div').first().addClass('qp01');    
        }).bind('mouseout',function(){
            var projcode = $(this).attr('projcode');
            var obj = $('#'+projcode+'_container');
            obj.css('z-index',1).find('div').first().removeClass('qp01'); 
        }).bind('click',function(){
            var markerid = $(this).attr('markerid');
            var project = historyProjectInfo['project'][markerid];
            var html = '<div class="openbox"><div class="openboxnr"><div class="title"><div class="close"><a onclick="closeMapInfoDiv();"><img src="'+$tplurl+'newmap/images/close.gif" width="10" height="10"></a></div><a href="'+project.url+'" target="_blank"><strong id="view_now_hs">'+project.projname+'</strong></a></div><div class="openboxnr01"><div class="sl"><a target="_blank" href="'+project.url+'"><img src="'+project.img+'" alt="'+project.projname+'" width="200" height="140"></a></div><div class="sr"><ul><li>��ϵ�ˣ�'+project.conactor+'</li><li>��ϵ�绰��'+project.tel+'</li><li>��˾��վ��<span>'+project.net+'</span></li><li>��ַ��'+project.address+'</li><li><a href="'+project.url+'">�鿴�̼�����</a></li></ul></div><div class="clear"></div></div></div><div class="jt"></div></div>';
                    $('#maptip').html(html).show();
            map.panTo(new BMap.Point(project.px, project.py));
                     
        });
        $('#browsing_history .lhistory span').bind('click',function(event){
            event.stopPropagation();
            var obj = $(this).parent().hide();
            var projcode = obj.attr('projcode');
            var markerid = obj.attr('markerid');         
            deletehHistoryCookie(projcode);
            removehistoryProjectData(markerid);
            $('#maptip').hide();
        });
        $('#clearAllHistoryProject').bind('click',function(){            
            deletehHistoryCookie();
            $('#browsing_history').children().hide();
            removehistoryProjectData();
            $('#maptip').hide();
        });
    }
}

//���������������ʷ�л�����
function menuChange(){
    var divs = $('#ltitle1').find('div');
        divs.each(function(i){
            $(this).bind('click',function(){
                $(this).removeClass('s2').addClass('s1');
                var siblings = $(this).parent().siblings();
                    siblings.each(function(){
                        $(this).find('div').removeClass('s1').addClass('s2');
                    });
                if(0==i){
                    $('#historyshow').hide();
                    $('#searchResultShow').show();
                    getProjectPoint();
                    showProjectData(0,10);
                    $('#maptip').hide();
                }else if(1==i){
                    removeProjectData();//���¥������
                    $('#searchResultShow').hide();
                    $('#historyshow').show();                    
                    if(null!==$.cookie('08MapHistory')){                        
                        getHistoryProjectPoint($.cookie('08MapHistory'));//��ȡ��ʷ¥������
                        historyProjectShow();//������ʷ������ʾ
                        $('#maptip').hide();
                    }
                }
            });
        });
    
}

//ģ���ʼ�� by louis
function Init(){
    InitConditions();
     getProjectPoint();
     showProjectData(0,10);
     sort();//������
     menuChange();//�������,������ʷ�л�
}
    return {Init:Init};
}

function closeMapInfoDiv() {
    $("#maptip").fadeOut(500);         
}


