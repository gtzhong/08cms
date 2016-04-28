/**************��ͼ���沼�ֳ�ʼ��**************/

//--------------------��ͼ��ʼ������--------------------------------------------------------------------------
//�����ͳ�ʼ����ͼ by louis
function initMap() {
		changescreenWandH();//��ͼ���ֽ������
		createMap(); //������ͼ
		setMapEvent(); //���õ�ͼ�¼�
		addMapControl();//���ͼ��ӿؼ�
}
//������ͼ���� by louis
function createMap() {
		window.map = new BMap.Map("divMap");
		var point = new BMap.Point(mapInfo.px, mapInfo.py); //����һ�����ĵ�����
		map.centerAndZoom(point, mapInfo.initZoom);
		window.map = map; //��map�����洢��ȫ��
		/*
		map.addEventListener("movestart", function () {            		
				
		});	
		map.addEventListener("moveend", function () { 
		
		});	
		map.addEventListener("zoomstart", function () {

		});
		map.addEventListener("zoomend", function () {

		});
		*/
	}
	
//��ͼ�¼����ú��� by louis
function setMapEvent() {
	map.enableDragging(); //���õ�ͼ��ק�¼���Ĭ������(�ɲ�д)
	map.enableScrollWheelZoom(); //���õ�ͼ���ַŴ���С
	map.enableDoubleClickZoom(); //�������˫���Ŵ�Ĭ������(�ɲ�д)
	map.enableKeyboard(); //���ü����������Ҽ��ƶ���ͼ
	map.setDraggingCursor('hand');//������ק��ͼʱ�����ָ����ʽΪ����
}

//��ͼ�ؼ���Ӻ��� by louis
function addMapControl() {
	//���ͼ��������ſؼ�
	var ctrl_nav = new BMap.NavigationControl({
		anchor: BMAP_ANCHOR_TOP_LEFT,
		type: BMAP_NAVIGATION_CONTROL_LARGE
	});
	map.addControl(ctrl_nav);
	//���ͼ���������ͼ�ؼ�
	var ctrl_ove = new BMap.OverviewMapControl({
		anchor: BMAP_ANCHOR_BOTTOM_RIGHT,
		isOpen: 1
	});
	map.addControl(ctrl_ove);
	//���ͼ����ӱ����߿ؼ�
	var ctrl_sca = new BMap.ScaleControl({
		anchor: BMAP_ANCHOR_BOTTOM_LEFT
	});
	map.addControl(ctrl_sca);
}

/*****************************�����Զ��帲����Ĺ��캯��**********************************/
function SquareOverlay(center,length,html,zIndex,purpose,projcode,px,py,projname,address,addresslong){
	this._center = center; 
	this._length = length;
	this._html = html;
	this._zIndex = zIndex;
	this._purpose = purpose;
	this._projcode = projcode;
	this._px = px;
	this._py = py;
	this._projname = projname;
	this._address = address;
	this._addresslong = addresslong;
 }
// �̳�API��BMap.Overlay
 SquareOverlay.prototype = new BMap.Overlay(); 
 // ʵ�ֳ�ʼ������
 SquareOverlay.prototype.initialize = function(map){
	// ����map����ʵ��
	 this._map = map;
	 var that = this;   
	 // ����divԪ�أ���Ϊ�Զ��帲��������� 
	 var div = document.createElement("div");   
	 div.style.position = "absolute";
	 div.style.zIndex =this._zIndex; 
	 div.setAttribute("id",that._projcode+"_container"); 
	 // ���Ը��ݲ�������Ԫ�����   
	  div.innerHTML = this._html;
	 // ��div��ӵ������������� 
	 map.getPanes().markerPane.appendChild(div); 
	 // ����divʵ��  
	 this._div = div;
	 return div;
  } 
  // ʵ�ֻ��Ʒ���
  SquareOverlay.prototype.draw = function(){
	// ���ݵ�������ת��Ϊ�������꣬�����ø�����  
	var position = this._map.pointToOverlayPixel(this._center);
   this._div.style.left = position.x - 12   + "px"; 
   this._div.style.top = position.y - 30  + "px"; 
  }
  SquareOverlay.prototype.show = function()
	{   if (this._div){   
		this._div.style.display = "";   } 
		  
	} 
  SquareOverlay.prototype.hide = function()
	{   if (this._div){    
	  this._div.style.display = "none";   }  
	}
  SquareOverlay.prototype.changehtml = function(html){  
   if (this._div){   
	   this._div.innerHTML= html; 
		}
   }
  SquareOverlay.prototype.addEventListener = function(event,fun){ 
	 this._div['on'+event] = fun;
   }

//��̬�ж���ͼ�����ֿ��
function changescreenWandH() {
	//��̬�ж��Ҳ��ͼ�ĸ߶�
	$('#boxfooter').show();
	var rightbarheight = $(window).height() - $('#boxhead').height()-$('#boxfooter').height(); //parseInt(document.body.clientHeight)-topbarheight;

	$("#divMap").css({
		"height": rightbarheight
	});
	//��̬�ж�����б�ĸ߶�
	$("#resultcontainer").css({
		"height": rightbarheight - $('#leftwrapperTips').height()
	});
}