$.getScript(tplurl+'js/ll.js');
function exc_zuhe(fmobj,v){
	//var fmobj=document.calc1;
	if (fmobj.name=="calc1"){
		if (v==3){
			document.getElementById('calc1_zuhe').style.display='block';
			document.getElementById('calc22').style.display='none';
			fmobj.jisuan_radio[1].checked = true;
			exc_js(fmobj,2);
		}else{
			document.getElementById('calc1_zuhe').style.display='none';
			document.getElementById('calc22').style.display='block';
		}
	}else{
		if (v==3){
			document.getElementById('calc2_zuhe').style.display='block';
			document.getElementById('calc22').style.display='none';
			fmobj.jisuan_radio[1].checked = true;
			exc_js(fmobj,2);
		}else{
			document.getElementById('calc2_zuhe').style.display='none';
			document.getElementById('calc22').style.display='block';
		}
	}
}
function exc_js(fmobj,v){
	var div1=document.getElementById("divr1");
	var div2=document.getElementById("divr2");
	if(fmobj.htype[0].checked == true){
		div1.style.display="block";
		div2.style.display="none";
	}else{
		div2.style.display="block";
		div1.style.display="none";
	}
	if (fmobj.name=="calc1"){
		if (v==1){
			document.getElementById('calc1_js_div1').style.display='block';
			document.getElementById('calc1_js_div2').style.display='none';
			document.getElementById('calc1_zuhe').style.display='none';
			fmobj.type.value=1;
		}else{
			document.getElementById('calc1_js_div1').style.display='none';
			document.getElementById('calc1_js_div2').style.display='block';
		}
	}else{
		if (v==1){
			document.getElementById('calc2_js_div1').style.display='block';
			document.getElementById('calc2_js_div2').style.display='none';
			document.getElementById('calc2_zuhe').style.display='none';
			fmobj.type.value=1;
		}else{
			document.getElementById('calc2_js_div1').style.display='none';
			document.getElementById('calc2_js_div2').style.display='block';
		}
	}
}
function formReset(fmobj){
	//var fmobj=document.calc1;
	if (fmobj.name=="calc1"){
		document.getElementById('calc1_js_div1').style.display='block';
		document.getElementById('calc1_js_div2').style.display='none';
		document.getElementById('calc1_zuhe').style.display='none';
		document.getElementById('calc1_benjin').style.display='none';
	}else{
		document.getElementById('calc2_js_div1').style.display='block';
		document.getElementById('calc2_js_div2').style.display='none';
		document.getElementById('calc2_zuhe').style.display='none';
		document.getElementById('calc2_benxi').style.display='none';
	}
}

//��֤�Ƿ�Ϊ����
function reg_Num(str){
	if (str.length==0){return false;}
	var Letters = "1234567890.";

	for (i=0;i<str.length;i++){
		var CheckChar = str.charAt(i);
		if (Letters.indexOf(CheckChar) == -1){return false;}
	}
	return true;
}

//�õ�����
function getlilv(lilv_class,type,years){
	var lilv_class = parseInt(lilv_class);
    if (years<=5){
		 return lilv_array[lilv_class][type][5];
	}else{
		return lilv_array[lilv_class][type][10];
	}
}

//���𻹿���»����(����: ������ / �����ܶ� / �������·� / ���ǰ��0��length-1)
function getMonthMoney2(lilv,total,month,cur_month){
	var lilv_month = lilv / 12;//������
	//return total * lilv_month * Math.pow(1 + lilv_month, month) / ( Math.pow(1 + lilv_month, month) -1 );
	var benjin_money = total/month;
	return (total - benjin_money * cur_month) * lilv_month + benjin_money;

}

function showR(data){
	var div1=document.getElementById("divr1");
	var div2=document.getElementById("divr2");
	if(data==1){
		div1.style.display="block";
		div2.style.display="none";
	}else{
		div2.style.display="block";
		div1.style.display="none";
	}
}

//��Ϣ������»����(����: ������/�����ܶ�/�������·�)
function getMonthMoney1(lilv,total,month){
	var lilv_month = lilv / 12;//������
	return total * lilv_month * Math.pow(1 + lilv_month, month) / ( Math.pow(1 + lilv_month, month) -1 );
}

function ext_total(fmobj){
	//var fmobj=document.calc1;
	//������»�����������
	while ((k=fmobj.month_money2.length-1)>=0){
		fmobj.month_money2.options.remove(k);
	}
	var years = fmobj.years.value;
	var month = fmobj.years.value * 12;

	month1.innerHTML = month+"(��)";
	month2.innerHTML = month+"(��)";
	if (fmobj.type.value == 3 ){
		//--  ����ʹ���(����ʹ���ļ��㣬ֻ����ҵ�����͹����������йأ��Ͱ������ܶ�����޹�)
			if (!reg_Num(fmobj.total_sy.value)){alert("����ʹ�������д�̴�����");fmobj.total_sy.focus();return false;}
			if (!reg_Num(fmobj.total_gjj.value)){alert("����ʹ�������д���������");fmobj.total_gjj.focus();return false;}
			if (fmobj.total_sy.value==null){fmobj.total_sy.value=0;}
			if (fmobj.total_gjj.value==null){fmobj.total_gjj.value=0;}
			var total_sy = fmobj.total_sy.value*10000;
			var total_gjj = fmobj.total_gjj.value*10000;
			fangkuan_total1.innerHTML = "��";//�����ܶ�
			fangkuan_total2.innerHTML = "��";//�����ܶ�
			money_first1.innerHTML = 0;//���ڸ���
			money_first2.innerHTML = 0;//���ڸ���

			//�����ܶ�
			var total_sy = parseInt(fmobj.total_sy.value*10000);
			var total_gjj = parseInt(fmobj.total_gjj.value*10000);
			var daikuan_total = total_sy + total_gjj;
			daikuan_total1.innerHTML = Math.round(daikuan_total);
			daikuan_total2.innerHTML = Math.round(daikuan_total);

			//�»���
			var lilv_sd = getlilv(fmobj.lilv.value,1, years);//�õ��̴�����
			var lilv_gjj = getlilv(fmobj.lilv.value,2, years);//�õ�����������

			//1.���𻹿�
				//�»���
				var all_total2 = 0;
				var month_money2 = "";
				for(j=0;j<month;j++) {
					//���ú�������: �����»����
					huankuan = getMonthMoney2(lilv_sd,total_sy,month,j) + getMonthMoney2(lilv_gjj,total_gjj,month,j);
					all_total2 += huankuan;
					huankuan = Math.round(huankuan*100)/100;
					//fmobj.month_money2.options[j] = new Option( (j+1) +"��," + huankuan + "(Ԫ)", huankuan);
					month_money2 += (j+1) +"��," + huankuan + "(Ԫ)\n";
				}
				_month_money2.value = month_money2;
				//�����ܶ�
				_all_total2.innerHTML = Math.round(all_total2*100)/100;
				//֧����Ϣ��
				accrual2.innerHTML = Math.round( (all_total2 - daikuan_total) *100)/100;


			//2.��Ϣ����
				//�¾�����
				var month_money1 = getMonthMoney1(lilv_sd,total_sy,month) + getMonthMoney1(lilv_gjj,total_gjj,month);//���ú�������
				_month_money1.innerHTML = Math.round(month_money1*100)/100 + "(Ԫ)";
				//�����ܶ�
				var all_total1 = month_money1 * month;
				_all_total1.innerHTML = Math.round(all_total1*100)/100;
				//֧����Ϣ��
				accrual1.innerHTML = Math.round( (all_total1 - daikuan_total) *100)/100;

	}else{
		//--  ��ҵ������������
			var lilv = getlilv(fmobj.lilv.value,fmobj.type.value, fmobj.years.value);//�õ�����
			if (fmobj.jisuan_radio[0].checked == true){
				//------------ ���ݵ����������
				if (!reg_Num(fmobj.price.value)){alert("����д����");fmobj.price.focus();return false;}
				if (!reg_Num(fmobj.sqm.value)){alert("����д���");fmobj.sqm.focus();return false;}

				//�����ܶ�
				var fangkuan_total = fmobj.price.value * fmobj.sqm.value;
				fangkuan_total1.innerHTML = fangkuan_total;
				fangkuan_total2.innerHTML = fangkuan_total;
				//�����ܶ�
				var daikuan_total = (fmobj.price.value * fmobj.sqm.value) * (fmobj.anjie.value/10);
				daikuan_total1.innerHTML = Math.round(daikuan_total);
				daikuan_total2.innerHTML = Math.round(daikuan_total);
				//���ڸ���
				var money_first = fangkuan_total - daikuan_total;
				money_first1.innerHTML = Math.round(money_first);
				money_first2.innerHTML = Math.round(money_first);
			}else{
				//------------ ���ݴ����ܶ����
				if (!reg_Num(fmobj.daikuan_total000.value)){alert("����д�����ܶ�");fmobj.daikuan_total000.focus();return false;}

				//�����ܶ�
				fangkuan_total1.innerHTML = "��";
				fangkuan_total2.innerHTML = "��";
				//�����ܶ�
				//var daikuan_total = fmobj.daikuan_total000.value;
				var daikuan_total = fmobj.daikuan_total000.value*10000;
				daikuan_total1.innerHTML = Math.round(daikuan_total);
				daikuan_total2.innerHTML = Math.round(daikuan_total);
				//���ڸ���
				money_first1.innerHTML = 0;
				money_first2.innerHTML = 0;
			}
			//1.���𻹿�
				//�»���
				var all_total2 = 0;
				var month_money2 = "";
				for(j=0;j<month;j++) {
					//���ú�������: �����»����
					huankuan = getMonthMoney2(lilv,daikuan_total,month,j);
					all_total2 += huankuan;
					huankuan = Math.round(huankuan*100)/100;
					//fmobj.month_money2.options[j] = new Option( (j+1) +"��," + huankuan + "(Ԫ)", huankuan);
					month_money2 += (j+1) +"��," + huankuan + "(Ԫ)\n";
				}
				_month_money2.value = month_money2;
				//�����ܶ�
				_all_total2.innerHTML = Math.round(all_total2*100)/100;
				//֧����Ϣ��
				accrual2.innerHTML = Math.round( (all_total2 - daikuan_total) *100)/100;


			//2.��Ϣ����
				//�¾�����
				var month_money1 = getMonthMoney1(lilv,daikuan_total,month);//���ú�������
				_month_money1.innerHTML = Math.round(month_money1*100)/100 + "(Ԫ)";
				//�����ܶ�
				var all_total1 = month_money1 * month;
				_all_total1.innerHTML = Math.round(all_total1*100)/100;
				//֧����Ϣ��
				accrual1.innerHTML = Math.round( (all_total1 - daikuan_total) *100)/100;

	}
}


//��ǰ���L����
function play(fm){
	var tqhdjsq = fm||document.tqhdjsq;
  if (tqhdjsq.dkzws.value==''){
       alert('����������ܶ�');
       return false;
  }else dkzys=parseFloat(tqhdjsq.dkzws.value)*10000;

  if(tqhdjsq.tqhkfs[1].checked && tqhdjsq.tqhkws.value==''){
    alert('�����벿����ǰ������');
    return false;
   }
  s_yhkqs=parseInt(tqhdjsq.yhkqs.value);

  //������

	if(tqhdjsq.dklx[0].checked){
		if (s_yhkqs>60){
			dklv = getlilv(tqhdjsq.dklv_class.value,2,10)/12; //�������������5������4.23%
		}else{
			dklv = getlilv(tqhdjsq.dklv_class.value,2,3)/12;  //�������������5��(��)����3.78%
		}
	}
	if(tqhdjsq.dklx[1].checked){
		if (s_yhkqs>60){
			dklv=getlilv(tqhdjsq.dklv_class.value,1,10)/12; //��ҵ�Դ�������5������5.31%
		}else{
			dklv=getlilv(tqhdjsq.dklv_class.value,1,3)/12; //��ҵ�Դ�������5��(��)����4.95%
		}
	}

  //�ѻ���������
  yhdkqs=(parseInt(tqhdjsq.tqhksjn.value)*12+parseInt(tqhdjsq.tqhksjy.value))-(parseInt(tqhdjsq.yhksjn.value)*12 + parseInt(tqhdjsq.yhksjy.value));

  if(yhdkqs<0 || yhdkqs>s_yhkqs){
    alert('Ԥ����ǰ����ʱ�����һ�λ���ʱ����ì�ܣ����ʵ');
    return false;
   }

  yhk=dkzys*(dklv*Math.pow((1+dklv),s_yhkqs))/(Math.pow((1+dklv),s_yhkqs)-1);
  yhkjssj=Math.floor((parseInt(tqhdjsq.yhksjn.value)*12+parseInt(tqhdjsq.yhksjy.value)+s_yhkqs-2)/12)+'��'+((parseInt(tqhdjsq.yhksjn.value)*12+parseInt(tqhdjsq.yhksjy.value)+s_yhkqs-2)%12+1)+'��';
  yhdkys=yhk*yhdkqs;

  yhlxs=0;
  yhbjs=0;
  for(i=1;i<=yhdkqs;i++){
     yhlxs=yhlxs+(dkzys-yhbjs)*dklv;
     yhbjs=yhbjs+yhk-(dkzys-yhbjs)*dklv;
   }

  remark='';
  if(tqhdjsq.tqhkfs[1].checked){
    tqhkys=parseInt(tqhdjsq.tqhkws.value)*10000;
     if(tqhkys+yhk>=(dkzys-yhbjs)*(1+dklv)){
         remark='������ǰ��������㹻������Ƿ���';
     }else{
	        yhbjs=yhbjs+yhk;
            byhk=yhk+tqhkys;
			if(tqhdjsq.clfs[0].checked){
			  yhbjs_temp=yhbjs+tqhkys;
              for(xdkqs=0;yhbjs_temp<=dkzys;xdkqs++) yhbjs_temp=yhbjs_temp+yhk-(dkzys-yhbjs_temp)*dklv;
			  xdkqs=xdkqs-1;
              xyhk=(dkzys-yhbjs-tqhkys)*(dklv*Math.pow((1+dklv),xdkqs))/(Math.pow((1+dklv),xdkqs)-1);
              jslx=yhk*s_yhkqs-yhdkys-byhk-xyhk*xdkqs;
			  xdkjssj=Math.floor((parseInt(tqhdjsq.tqhksjn.value)*12+parseInt(tqhdjsq.tqhksjy.value)+xdkqs-2)/12)+'��'+((parseInt(tqhdjsq.tqhksjn.value)*12+parseInt(tqhdjsq.tqhksjy.value)+xdkqs-2)%12+1)+'��'; 
             }else{
		       xyhk=(dkzys-yhbjs-tqhkys)*(dklv*Math.pow((1+dklv),(s_yhkqs-yhdkqs)))/(Math.pow((1+dklv),(s_yhkqs-yhdkqs))-1);
               jslx=yhk*s_yhkqs-yhdkys-byhk-xyhk*(s_yhkqs-yhdkqs);
			   xdkjssj=yhkjssj;
			  }
       }
   }

  if(tqhdjsq.tqhkfs[0].checked || remark!=''){
    byhk=(dkzys-yhbjs)*(1+dklv);
    xyhk=0;
    jslx=yhk*s_yhkqs-yhdkys-byhk;
    xdkjssj=tqhdjsq.tqhksjn.value+'��'+tqhdjsq.tqhksjy.value+'��';
	}

  ykhke.innerHTML=Math.round(yhk*100)/100;
  yhkze.innerHTML=Math.round(yhdkys*100)/100;
  yhlxe.innerHTML=Math.round(yhlxs*100)/100;
  gyyihke.innerHTML=Math.round(byhk*100)/100;
  xyqyhke.innerHTML=Math.round(xyhk*100)/100;
  jslxzc.innerHTML=Math.round(jslx*100)/100;
  yzhhkq.innerHTML=yhkjssj;
  xdzhhkq.innerHTML=xdkjssj;
  jsjgts.innerHTML=remark;
}