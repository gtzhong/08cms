$().ready(function(){
	var options = {max : 5,value:0,min : 1,step: 1,image: $tplurl + 'newimages/star.gif',title_format : function(value) {return value+'��';},after_click : function(ret,id) {$(id).val(ret.number);}}
	$('#iservice').rater(options,'#service');
	$('#iprice').rater(options,'#price');
	$('#idesign').rater(options,'#design');
	$('#iprocess').rater(options,'#process');
	$('#iafterSale').rater(options,'#afterSale');
	$("#commentForm").submit(function(){
		if( $("#service").val()==0||
			$("#price").val()==0||
			$("#design").val()==0||
			$("#process").val()==0||
			$("#afterSale").val()==0){
			alert("������ѡ������");
			return false;
		}
		if($("#cmtcontent").val()==""){
			alert("����д��������");
			$("#cmtcontent").focus();
			return false;
		}
		return true;
	});
	
	$(".close").click(
		function (){
			$(this).parent().fadeOut();
		}
	); 
	$(".reply").toggle(
			function () {
				$(this).parent().nextAll('.replyContent').fadeIn();
			},
			function (){
				$(this).parent().nextAll('.replyContent').fadeOut();
			}
	); 

	
});
