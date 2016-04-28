function compute(){
	if(document.getElementById("totalPrice").value==""){
		alert("�������ܽ��");
		return false;
	}
	if(document.getElementById("buildArea").value==""){
		alert("�����뽨�����");
		return false;
	}
	if(document.getElementById("evaluationPrice").value==""){
		alert("�����������");
		return false;
	}
		
	employeeBuyer();
	employeeSaler();
	loanDan();
	loanPing();
	taxYin();
	taxGet();

	taxQI();
	taxArea();
	feeAA();
	feeBB();
	areaTurn();
	cost();
	caTotal();
}
function computeLoan(form){
	sv('daikuan_total000',v('buyerPay'));
	sv('total_sy',v('buyerPay'));
	sv('total_gjj',0);
	ext_total(form);
}
//�ܼ��޸ĺ���Ӧ�ĸĶ�����
function countPrice(){
	countAverage(0);
	employeeBuyer();
	employeeSaler();
	loanDan();
	loanPing();
	taxYin();
	taxGet();

	taxQI();
	taxArea();
	c();
	
}

//���� ��� ���ĺ���Ӧ�ĸĶ�����
function onChangeHouseType(type){
	
	//taxArea2(type);
	
	//taxGetType(type);
	taxGet();
	taxTradeType(type);
	cost();
	//compute()
	
}

//�����������Ķ�����Ӧ�ĸĶ�����
function onChangeAboutPrice(price){
	
	loanDan();

	loanPing();
	taxYin()
	taxGet();
	taxQI();
	taxTrade();
	//compute()
}



//������º�Ҫ���������
function areaChange(area){
	countAverage();	
	cost();
	c();
}

//�����跿�����º�Ҫ���������
function buyerLoanChange(){
	if(dev('buyerPay')>dev('totalPrice')){sv('buyerPay',dev('totalPrice'));return true;}
	
	sv('daikuan_total000',v("buyerPay"));
	daikuanYin();
	loanDan();
	feeCC();
	taxTrade();
	
	//compute()
}

//���׷�
function cost(){
	var hType=v("houseType");
	var area=v("buildArea");
	if(hType!=4)
		sv("jiao_yi",(area*6).toFixed(2));
	else 
		sv("jiao_yi",(area*12).toFixed(2));
		
	c();
}

//����ӡ��˰
function daikuanYin(price){
	daikuanYin();
	c();
}

function daikuanYin(){
	var price=dev("daikuan_total000");
	//if(price>=1000)
	price = price*0.00005;
	document.getElementById("daikuan_yinhua").value=price.toFixed(2);
}


//���س��ý�
function areaTurn(area){
areaTurn();
}

function areaTurn(){
	var area=document.getElementById("buildArea").value;
	var buildarea = document.getElementById("buildArea").value;
	var ckdj=v("ckdj");
	var tddj=document.getElementById("tddj").value;
	var price = (tddj*buildarea)+(ckdj*area);	
	document.getElementById("area_turn").value=price;
	c();
}

//��������˰

function taxGet(){
/*	
5������ͨסլ(<144ƽ��)��1%(ȫ��)��20%(���)��
5������ͨסլ(<144ƽ��)��1%(ȫ��)��20%(���),Ψһס������
5���ڷ���ͨסլ(>144ƽ��)��1.5%(ȫ��)��20%(���)��
5�������ͨסլ(>144ƽ��)��1.5%(ȫ��)��20%(���),Ψһס��������
���÷���1%(ȫ��)��20%(���)��
2009��12��31��ǰ�����С��144��ͨסլ��˰����32��

<option value="1" selected>��ͨסլ(���С��90ƽ��)</option>
          <option value="2" >��ͨסլ(�������90ƽ����С��144ƽ��)</option>
          <option value="3">����ͨסլ(�������144ƽ��)</option>
          <option value="4">���÷�����סլ��</option>
          <option value="5">����</option>
		  
		  <option value="1" selected>2������</option>
          <option value="2" >2�꣭5��</option>
          <option value="3">5������</option>
*/
	var totalPrice = getPrice("totalPrice");
	if(dev('evaluationPrice')>totalPrice)totalPrice=dev('evaluationPrice');
	var saler_price = getPrice("saler_price");
	var price="";
	//var p=v("grsdcq");
	var f=v("wyzf");
	
	var year=v("houseYear");
	var type=getSelect("houseType");
	if(year<=2 && type<=2){//5������ͨסլ(<144ƽ��)��1%(ȫ��)��20%(���)��
			price=totalPrice*0.01;
	}else if(year>=3 && type<=2){//5������ͨסլ(<144ƽ��)��1%(ȫ��)��20%(���),Ψһס������
			price=totalPrice*0.01;
		if(f==1)price=0;
	}else if(year<=2 && type==3){//5���ڷ���ͨסլ(>144ƽ��)��1.5%(ȫ��)��20%(���)��
			price=totalPrice*0.015;
	}else if(year>=3 && type==3){//5�������ͨסլ(>144ƽ��)��1.5%(ȫ��)��20%(���),Ψһס��������
			price=totalPrice*0.015;
		if(f==1)price=0;
	}else if(type==4){//���÷���1%(ȫ��)��20%(���)��
			price=totalPrice*0.01;
	}
	//if(type<=2)//2009��12��31��ǰ�����С��144��ͨסլ��˰����32��
		//price=price*0.68
	sv("tax_get",price.toFixed(2));
	
	c();
}

function taxGetType(type){
 taxGet();
}

//�������
function countAverage(buildArea){

	var totalPrice = getPrice("totalPrice");
	
	var buildArea2 = document.getElementById("buildArea").value;
	
	document.getElementById("averagePrice").value=(totalPrice/buildArea2).toFixed(2);
	c();
		
}

//���㶨�����
function payforDeposit(){
	
	var totalPrice = getPrice("totalPrice");
	
	var ag_paySum = getPrice("ag_paySum");
	
	document.getElementById("ag_payBalance").value=(totalPrice-ag_paySum).toFixed(2);
		
	payforFirst();
}

//�����׸������
function payforFirst(){
	
	var totalPrice = getPrice("totalPrice");
	
	var payfor_first = getPrice("payfor_first");
	
	var ag_payBalance = getPrice("ag_payBalance");
	
	if(ag_payBalance.length>0 && payfor_first.length>0){
		
		document.getElementById("surplus_first").value=(ag_payBalance-payfor_first).toFixed(2);
	
	}else if(totalPrice.length>0 && payfor_first.length>0){
		
		document.getElementById("surplus_first").value=(totalPrice-payfor_first).toFixed(2);
	} 
}

//֧�����
function payfor(id) {
	
	var totalPrice = getPrice("totalPrice");

	var payfor_first = getPrice("payfor_first");
	
	var ag_paySum = getPrice("ag_paySum");
	
	var price = totalPrice-payfor_first-ag_paySum;
	
	if(id>0){
		for (i=1;i<=id;i++) {    
			var payfor = document.getElementById("payfor"+i).value;
			
			payfor = payfor.replaceAll(",","");
			
			price = price-payfor;

		}
	}
	
	document.getElementById("surplus"+id).value=price.toFixed(2);
}



//�������Ӷ��
function employeeBuyer(){
	
	var size = getSelect("buyerProportion");
	
	var totalPrice = getPrice("totalPrice");
	
	var price = totalPrice*size;
		
	document.getElementById("buyerBrokerage").value=price.toFixed(2);
	c();
	
}

//��������Ӷ��
function employeeSaler(){
	
	var size = getSelect("sellerProportion");
	
	var totalPrice = getPrice("totalPrice");
	
	var price = totalPrice*size;
		
	document.getElementById("sellerBrokerage").value=price.toFixed(2);
	c();
	
}

//�������
function loanDan(){
	
	/*2010�����ߣ�ɾ������
	var size = getSelect("guaranteeProportion");
	
	var buyerPayPrice = getPrice("buyerPay");
	
	var price = buyerPayPrice*size;
		
	document.getElementById("loanGuarantee").value=price.toFixed(2);
	
	c();*/
	
}

//����������
function loanPing(){
	
	var size = getSelect("evaluationProportion");
	
	var evaluationPrice = getPrice("evaluationPrice");
	
	var price = evaluationPrice*size;
		
	document.getElementById("loanEvaluation").value=price.toFixed(2);
	
	c();
	
}

//������˰
function taxQI(){
/*
��ͨסլ(<90ƽ�ף��״ι���)��1%��
��ͨסլ(<90ƽ�ף����״ι���)��1.5%��
��ͨסլ(90��144ƽ��)��1.5%�� 
����ͨסլ(>144ƽ��)��3%��
���÷���3%��
<option value="1" selected>��ͨסլ(���С��90ƽ��)</option>
          <option value="2" >��ͨסլ(�������90ƽ����С��144ƽ��)</option>
          <option value="3">����ͨסլ(�������144ƽ��)</option>
          <option value="4">���÷�����סլ��</option>
          <option value="5">����</option>
		  
		  <option value="1" selected>2������</option>
          <option value="2" >2�꣭5��</option>
          <option value="3">5������</option>
*/
	
	var totalPrice = getPrice("totalPrice");
	if(dev('evaluationPrice')>totalPrice)totalPrice=dev('evaluationPrice');
	var type=getSelect("houseType");
	var year=getSelect("houseYear");		
	var f = v("scgf");		
	var price="";
	if(type<=1){
		if(f==1)
			price=totalPrice*0.01;
		else 
			price=totalPrice*0.015;
	}else if(type==2){
		price=totalPrice*0.015;
	}else if(type==3){
		price=totalPrice*0.03;
	}else if(type==4){
		price=totalPrice*0.03;
	}
	sv("tax_qi",price.toFixed(2));
	c();
	
}

//������ֵ˰
function taxArea(){
	
	var totalPrice = getPrice("totalPrice");
	
	var tdzjbl = document.getElementById("tdzjbl").value;

	var price = totalPrice*tdzjbl;

	document.getElementById("tax_area").value=price.toFixed(2);
	c();
	
}
//����Ȩ������
function feeAA(){
	var price= document.getElementById("syqgbj").value*10;	
	document.getElementById("feeA").value=price.toFixed(2);
	c();
}
//����֤������
function feeBB(){
	var price= document.getElementById("tdzsyj").value*20;	
	document.getElementById("feeB").value=price.toFixed(2);
	c();
}

//����Ȩ֤���ã��д��
function feeCC(){
	if(getPrice("buyerPay")>0)
		sv("feeC",80);
	else
		sv("feeC",0);
	
	c();
}
//Ӫҵ˰
function taxTrade(){
/*
2������ͨסլ(<144ƽ��)��5.6%(���)��
2������ͨסլ(<144ƽ��)���ޣ�
2���ڷ���ͨסլ(>144ƽ��)��5.6%(ȫ��)��
2�������ͨסլ(>144ƽ��)��5.6%(���)��
���÷���5.6%(���)��
2009��12��31��ǰ�����С��144�O��ͨסլӪҵ˰����80����

<option value="1" selected>��ͨסլ(���С��90ƽ��)</option>
          <option value="2" >��ͨסլ(�������90ƽ����С��144ƽ��)</option>
          <option value="3">����ͨסլ(�������144ƽ��)</option>
          <option value="4">���÷�����סլ��</option>
          <option value="5">����</option>
		  
          <option value="2" >5�꼰����</option>
          <option value="3">5������</option>
*/

	var totalPrice = getPrice("totalPrice");	
	if(dev('evaluationPrice')>totalPrice)totalPrice=dev('evaluationPrice');
	var saler_price = getPrice("saler_price");	
	var type = getSelect("houseType");
	var year=getSelect("houseYear");	
	var price = "";
	if(year<=2 && type<=2)//5������ͨסլ(<144ƽ��)��5.6%(���)��
		price=(totalPrice-saler_price)*0.056;
	else if(year>2 && type<=2)//5������ͨסլ(<144ƽ��)���ޣ�
		price=0;
	else if(year<=2 && type==3)//5���ڷ���ͨסլ(>144ƽ��)��5.6%(ȫ��)��
		price=totalPrice*0.056;
	else if(year>2 && type==3)//5�������ͨסլ(>144ƽ��)��5.6%(���)��
		price=(totalPrice-saler_price)*0.056;
	else if(type==4)//���÷���5.6%(���)��
		price=(totalPrice-saler_price)*0.056;
	
	
	//if(type<=2)//2009��12��31��ǰ�����С��144�O��ͨסլӪҵ˰����80����
		//price=price*0.2;	
	
	sv("tax_trade",price.toFixed(2));
	
	c();

}
//ӡ��˰
function taxYin(){
	var totalPrice = getPrice("totalPrice");
	if(dev('evaluationPrice')>totalPrice)totalPrice=dev('evaluationPrice');
	var price = totalPrice*0.001;	
	document.getElementById("tax_yin").value=price.toFixed(2);
	c();
}

function taxTradeType(type){
		taxTrade();	
}

function taxArea2(size){
	
	var totalPrice = getPrice("totalPrice");
	
	if(size==1 || size==2){

		document.getElementById("tax_area").value="0";
	}else{
		var price = totalPrice*0.01;

		document.getElementById("tax_area").value=price.toFixed(2);
	}
}

