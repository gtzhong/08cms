function doSubmit(url){
	var form=document.getElementById("calculator");
	de(form.totalPrice);
	de(form.buyerPay);
	de(form.sellerPay);
	de(form.sellerBalance);
	de(form.evaluationPrice);
	de(form.total_sy);
	de(form.total_gjj);
	de(form.month_money1);
	de(form.all_total1);
	de(form.accrual1);
	de(form.daikuan_total1);
	
	 for(var i=0;i<form.paySum.length;i++){
		de(form.paySum[i]);
	}
	for(var i=0;i<form.ag_paySum.length;i++){
		de(form.ag_paySum[i]);
	}
	for(var i=0;i<form.ag_payBalance.length;i++){
		de(form.ag_payBalance[i]);
	}
	form.action=url;
	form.target="_blank";
	form.submit();
	
}
function oc(id){
	var tt=document.getElementById(id);
	if(tt.style.display=="none"){
		tt.style.display="block";
		document.getElementById("icon"+id).src= $aaaatpl+"images/up_tb.gif";
	}
	else{
		tt.style.display="none";
		document.getElementById("icon"+id).src= $aaaatpl+"images/down_tb.gif";
	}
}
//====================================================

//��ȡȫ��
function getTotalPrice(){
	var totalPrice = dev("totalPrice");
	if(v('evaluationPrice')>totalPrice)totalPrice=dev('evaluationPrice');
	return totalPrice;
}
function getMarginPrice(){
	var marginPrice = getTotalPrice()-dev('saler_price');
	if(marginPrice<0)marginPrice=0;
	return marginPrice;
}
//��ȡָ���۸�
function getPrice(id){
	var price = document.getElementById(id).value;
	price = price.replaceAll(",","");
	return price;
	
}



//��ȡ�����˵�ֵ
function getSelect(id){
	//var size = document.getElementById(id).options[document.getElementById("select").options.selectedIndex].value
	var size= document.getElementById(id).value
	return size;
}




function c(){caTotal()}
function caTotal(){
	var payName=document.getElementsByName("payName");
	var costType=document.getElementsByName("costType");
	var paySum=document.getElementsByName("paySum");
	var payer=document.getElementsByName("payer");
	
	var cost4B=0;
	var cost4S=0;
	var cost4A=0;
	var cost4O=0;
	var cost4T=0;
	
	var cost3B=0;
	var cost3S=0;
	var cost3A=0;
	var cost3O=0;
	var cost3T=0;
	
	var cost2B=0;
	var cost2S=0;
	var cost2A=0;
	var cost2O=0;
	var cost2T=0;
	
	var cost1B=0;
	var cost1S=0;
	var cost1A=0;
	var cost1O=0;
	var cost1T=0;
	
	var costBT=0;
	var costST=0;
	var costAT=0;
	var costOT=0;
	var costTT=0;
	
	for(var i=0;i<payName.length;i++){
		
		if(costType[i].value==4){
			if(payer[i].value=="1-��"){
				cost4B+=parseFloat(paySum[i].value);
			}else if(payer[i].value=="2-����"){
				cost4S+=parseFloat(paySum[i].value);
			}else if(payer[i].value=="3-�н�"){
				cost4A+=parseFloat(paySum[i].value);
			}else if(payer[i].value=="0-����"){
				cost4O+=parseFloat(paySum[i].value);
			}else if(payer[i].value=="4-˫������"){
				cost4B+=(parseFloat(paySum[i].value)/2);
				cost4S+=(parseFloat(paySum[i].value)/2);
			}	
			cost4T+=parseFloat(paySum[i].value);
		}	
		if(costType[i].value==3){
			if(payer[i].value=="1-��"){
				cost3B+=parseFloat(paySum[i].value);
			}else if(payer[i].value=="2-����"){
				cost3S+=parseFloat(paySum[i].value);
			}else if(payer[i].value=="3-�н�"){
				cost3A+=parseFloat(paySum[i].value);
			}else if(payer[i].value=="0-����"){
				cost3O+=parseFloat(paySum[i].value);
			}else if(payer[i].value=="4-˫������"){
				cost3B+=(parseFloat(paySum[i].value)/2);
				cost3S+=(parseFloat(paySum[i].value)/2);
			}	
			cost3T+=parseFloat(paySum[i].value);
		}	
		if(costType[i].value==2){
			if(payer[i].value=="1-��"){
				cost2B+=parseFloat(paySum[i].value);
			}else if(payer[i].value=="2-����"){
				cost2S+=parseFloat(paySum[i].value);
			}else if(payer[i].value=="3-�н�"){
				cost2A+=parseFloat(paySum[i].value);
			}else if(payer[i].value=="0-����"){
				cost2O+=parseFloat(paySum[i].value);
			}else if(payer[i].value=="4-˫������"){
				cost2B+=(parseFloat(paySum[i].value)/2);
				cost2S+=(parseFloat(paySum[i].value)/2);
			}	
			cost2T+=parseFloat(paySum[i].value);
		}	
		
		if(costType[i].value==1){
			if(payer[i].value=="1-��"){
				cost1B+=parseFloat(paySum[i].value);
			}else if(payer[i].value=="2-����"){
				cost1S+=parseFloat(paySum[i].value);
			}else if(payer[i].value=="3-�н�"){
				cost1A+=parseFloat(paySum[i].value);
			}else if(payer[i].value=="0-����"){
				cost1O+=parseFloat(paySum[i].value);
			}else if(payer[i].value=="4-˫������"){
				cost1B+=(parseFloat(paySum[i].value)/2);
				cost1S+=(parseFloat(paySum[i].value)/2);
			}
			cost1T+=parseFloat(paySum[i].value);
		}			
			
	}
	
	o("st04").style.display="block";
	o("st03").style.display="block";
	o("st02").style.display="block";
	o("st01").style.display="block";
	
	sh("cost4B",fmt(cost4B.toFixed(2)));
	sh("cost4S",fmt(cost4S.toFixed(2)));
	//sh("cost4A",fmt(cost4A.toFixed(2)));
	//sh("cost4O",fmt(cost4O.toFixed(2)));
	sh("cost4T",fmt(cost4T.toFixed(2)));
	
	
	sh("cost3B",fmt(cost3B.toFixed(2)));
	sh("cost3S",fmt(cost3S.toFixed(2)));
	//sh("cost3A",fmt(cost3A.toFixed(2)));
	//sh("cost3O",fmt(cost3O.toFixed(2)));
	sh("cost3T",fmt(cost3T.toFixed(2)));
	
	sh("cost2B",fmt(cost2B.toFixed(2)));
	sh("cost2S",fmt(cost2S.toFixed(2)));
	//sh("cost2A",fmt(cost2A.toFixed(2)));
	//sh("cost2O",fmt(cost2O.toFixed(2)));
	sh("cost2T",fmt(cost2T.toFixed(2)));
	
	sh("cost1B",fmt(cost1B.toFixed(2)));
	sh("cost1S",fmt(cost1S.toFixed(2)));
	//sh("cost1A",fmt(cost1A.toFixed(2)));
	//sh("cost1O",fmt(cost1O.toFixed(2)));
	sh("cost1T",fmt(cost1T.toFixed(2)));
	
	costBT=cost4B+cost3B+cost2B+cost1B;
	costST=cost4S+cost3S+cost2S+cost1S;
	costAT=cost4A+cost3A+cost2A+cost1A;
	costOT=cost4O+cost3O+cost2O+cost1O;
	costTT=cost4T+cost3T+cost2T+cost1T;
	sh("costBT",fmt(costBT.toFixed(2)));
	sh("costST",fmt(costST.toFixed(2)));
	sh("costAT",fmt(costAT.toFixed(2)));
	sh("costOT",fmt(costOT.toFixed(2)));
	sh("costTT",fmt(costTT.toFixed(2)));
	
	
	//alert("=====>"+cost4B);
}

String.prototype.replaceAll = function (AFindText,ARepText){
    raRegExp = new RegExp(AFindText,"g");
    return this.replace(raRegExp,ARepText);
}

function setValue(value,id){
	value = value.toFixed(2);
	document.getElementById(id).value=value;
}








function check_time(time){
	time = time.replace("��","");
	if(time<1990 || time>2009){
		alert("��������ȷ���");
		document.getElementById("build_time").value="";
		return false;
	}
}




function getID(){
	var zf=document.getElementById("zf_cs");	
	var dt=new Date();
	var dts=dt.toGMTString()+dt.getMilliseconds();
	dts=dts.replace(/:/g,'');
	dts=dts.replace(/ /g,'');
	dts=dts.replace(/,/g,'');
	return dts;
}
function zf_new(){
	var p=document.getElementById("zf_cs");	
	var dts=getID();
	var c="<table width='810' border='0' align='center' cellpadding='4' cellspacing='2' id="+dts+"><tr><td width='112'><input name='payName' type='text' value='����'><span class='text_14'><input type='hidden' name='costType' value='2'/><input type='hidden' name='payNameID' value='0'/></span></td><td width='467'>&nbsp;</td><td width='92'><span class='text_14'><input name='paySum' type='text' id='paySum'  size='20'></span></td><td width='84'><span class='text_14'><select name='payer'><option value='1-��'>��</option><option value='2-����'>����</option><option value='3-�н�'>�н�</option><option value='0-����'>����</option></select></span></td><td><input name='payee' type='text' id='payee' value='����'><input name='payeeID' type='hidden' id='payeeID' value='0'></td><td width='5%'><a href=javascript:zf_del('"+dts+"')>��</a></td></tr></table>";
	p.innerHTML+=c;
}
function zf_del(cid){
	var p=document.getElementById("zf_cs");	
	var c=document.getElementById(cid);
	p.removeChild(c);
	
}
function ht_new(){
	var p=document.getElementById("ht_cs");	
	var dts=getID();
	var c="<table width='810' border='0' align='center' cellpadding='4' cellspacing='2' id="+dts+"><tr><td width='165'><input type='text' name='ag_payName' id='payName' style='width:100px'><span class='text_14'><input type='hidden' name='ag_payNameID' value='0' /></span></td><td width='167'><span class='text_14'><input name='ag_paySum' type='text'  size='20' onBlur='javascript:payforDeposit();' /></span></td><td width='153'><span class='text_14'><input name='ag_payBalance' type='text' class='input_bg' id='surplus_first2' size='20' /></span></td><td width='138'><span class='text_14'><select name='ag_payer'><option value='1-��'>��</option><option value='2-����'>����</option><option value='3-�н�'>�н�</option><option value='0-����'>����</option></select></span></td><td><a href=javascript:ht_del('"+dts+"')>��</a></td></tr></table>";
	p.innerHTML+=c;
}
function ht_del(cid){
	var p=document.getElementById("ht_cs");	
	var c=document.getElementById(cid);
	p.removeChild(c);
	
}
function dk_new(){
	var p=document.getElementById("dk_cs");	
	var dts=getID();
	var c="<table width='810' border='0' align='center' cellpadding='4' cellspacing='2' id="+dts+"><tr><td width='177'><input name='payName' type='text' value='����'><span class='text_14'><input type='hidden' name='costType' value='2'/><input type='hidden' name='payNameID' value='0'/></span></td><td width='185'>&nbsp;</td><td width='105'><span class='text_14'><input name='paySum' type='text' id='paySum'  size='20'></span></td><td width='58'><span class='text_14'><select name='payer'><option value='1-��'>��</option><option value='2-����'>����</option><option value='3-�н�'>�н�</option><option value='0-����'>����</option></select></span></td><td><input name='payee' type='text' id='payee' value='����'><input name='payeeID' type='hidden' id='payeeID' value='0'></td><td width='5%'><a href=javascript:dk_del('"+dts+"')>��</a></td></tr></table>";
	p.innerHTML+=c;
}
function dk_del(cid){
	var p=document.getElementById("dk_cs");	
	var c=document.getElementById(cid);
	p.removeChild(c);
	
}
function dev(value){
	if(value!='')
	return parseFloat(value.replaceAll(',',''));
}

function v(id){
	return document.getElementById(id).value;
}
function o(id){
	return document.getElementById(id);	
}
function sv(id,value){
	document.getElementById(id).value=value;	
}
function sh(id,value){
	document.getElementById(id).innerHTML=value;	
}
function ven(v){
	if(v!='')
	return parseFloat(v).toLocaleString();
}
function vde(v){
	if(v!='')
	return parseFloat(v.value.replaceAll(',',''));
}
function dev(id){
	return parseFloat(v(id).replaceAll(',',''));
}
function fmt(v){
	if(isNaN(v))
		return '-';
	else 
		return v;
}

function CNB(c,v){
	//v=parseFloat(v.replaceAll(',',''));
	//document.getElementById(c).style.display="block";
	sh(c,toCNB(v));
}
function toCNB(Num){
        for(i=Num.length-1;i>=0;i--) 
        { 
		 Num = Num.replace(",","")//�滻tomoney()�еġ�,�� 
		 Num = Num.replace(" ","")//�滻tomoney()�еĿո� 
        } 
    
        Num = Num.replace("��","")//�滻�����ܳ��ֵģ��ַ� 
        if(isNaN(Num))    
        { 
     //��֤������ַ��Ƿ�Ϊ���� 
     alert("����Сд����Ƿ���ȷ"); 
     return; 
        } 
        //---�ַ�������ϣ���ʼת����ת������ǰ�������ֱַ�ת��---// 
        part = String(Num).split("."); 
        newchar = "";    
        //С����ǰ����ת�� 
        for(i=part[0].length-1;i>=0;i--) 
        { 
         if(part[0].length > 10){ alert("������");return "";}//����������ʰ�ڵ�λ����ʾ 
     tmpnewchar = "" 
     perchar = part[0].charAt(i); 
     switch(perchar){ 
     case "0": tmpnewchar="��" + tmpnewchar ;break; 
     case "1": tmpnewchar="Ҽ" + tmpnewchar ;break; 
     case "2": tmpnewchar="��" + tmpnewchar ;break; 
     case "3": tmpnewchar="��" + tmpnewchar ;break; 
     case "4": tmpnewchar="��" + tmpnewchar ;break; 
     case "5": tmpnewchar="��" + tmpnewchar ;break; 
     case "6": tmpnewchar="½" + tmpnewchar ;break; 
     case "7": tmpnewchar="��" + tmpnewchar ;break; 
     case "8": tmpnewchar="��" + tmpnewchar ;break; 
     case "9": tmpnewchar="��" + tmpnewchar ;break; 
         } 
         switch(part[0].length-i-1) 
    { 
     case 0: tmpnewchar = tmpnewchar +"Ԫ" ;break; 
     case 1: if(perchar!=0)tmpnewchar= tmpnewchar +"ʰ" ;break; 
     case 2: if(perchar!=0)tmpnewchar= tmpnewchar +"��" ;break; 
     case 3: if(perchar!=0)tmpnewchar= tmpnewchar +"Ǫ" ;break;    
     case 4: tmpnewchar= tmpnewchar +"��" ;break; 
     case 5: if(perchar!=0)tmpnewchar= tmpnewchar +"ʰ" ;break; 
     case 6: if(perchar!=0)tmpnewchar= tmpnewchar +"��" ;break; 
     case 7: if(perchar!=0)tmpnewchar= tmpnewchar +"Ǫ" ;break; 
     case 8: tmpnewchar= tmpnewchar +"��" ;break; 
     case 9: tmpnewchar= tmpnewchar +"ʰ" ;break; 
         } 
         newchar = tmpnewchar + newchar; 
        } 
        //С����֮�����ת�� 
        if(Num.indexOf(".")!=-1) 
        { 
         if(part[1].length > 2) 
         { 
        alert("С����֮��ֻ�ܱ�����λ,ϵͳ���Զ��ض�"); 
        part[1] = part[1].substr(0,2) 
     } 
         for(i=0;i<part[1].length;i++) 
         { 
        tmpnewchar = "" 
        perchar = part[1].charAt(i) 
        switch(perchar){ 
        case "0": tmpnewchar="��" + tmpnewchar ;break; 
        case "1": tmpnewchar="Ҽ" + tmpnewchar ;break; 
        case "2": tmpnewchar="��" + tmpnewchar ;break; 
        case "3": tmpnewchar="��" + tmpnewchar ;break; 
        case "4": tmpnewchar="��" + tmpnewchar ;break; 
        case "5": tmpnewchar="��" + tmpnewchar ;break; 
        case "6": tmpnewchar="½" + tmpnewchar ;break; 
        case "7": tmpnewchar="��" + tmpnewchar ;break; 
        case "8": tmpnewchar="��" + tmpnewchar ;break; 
        case "9": tmpnewchar="��" + tmpnewchar ;break; 
     } 
     if(i==0)tmpnewchar =tmpnewchar + "��"; 
     if(i==1)tmpnewchar = tmpnewchar + "��"; 
     newchar = newchar + tmpnewchar; 
         } 
        } 
        //�滻�������ú��� 
        while(newchar.search("����") != -1) 
            newchar = newchar.replace("����", "��"); 
        newchar = newchar.replace("����", "��"); 
        newchar = newchar.replace("����", "��"); 
        newchar = newchar.replace("����", "��");    
        newchar = newchar.replace("��Ԫ", "Ԫ"); 
        newchar = newchar.replace("���", ""); 
        newchar = newchar.replace("���", ""); 
    
        if (newchar.charAt(newchar.length-1) == "Ԫ" || newchar.charAt(newchar.length-1) == "��") 
         newchar = newchar+"��" 
		 
        return newchar; 
		
}