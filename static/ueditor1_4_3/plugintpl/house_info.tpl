{{each list}} 
	<p style="font-size:16px;font-weight:bold;">{{$value.subject}} <img src="{{$value.thumb}}" width=100 height=100/></p>
    <p>��ҵ���ͣ�<span style="color:red;">{{$value.ccid12title}}</span></p> 
    <p>����״̬��<span style="color:red;">{{$value.ccid18title}}</span></p> 
    <p>����ʱ�� ��<span style="color:red;">{{$value.kprq}}</span></p>
    <p>�ݻ��� ��<span style="color:red;">{{$value.yjl}}</span></p>
     <p>�̻��� ��<span style="color:red;">{{$value.lhl}}</span></p>
    <p>���۵绰 ��<span style="color:red;">{{$value.tel}}</span></p>
    <p>���۵�ַ ��<span style="color:red;">{{$value.sldz}}</span></p>
    <p>ռ����� ��<span style="color:red;">{{$value.jzmj}}</span></p>
    <p>¥�̵�ַ ��<span style="color:red;">{{$value.address}}</span></p>
    <p>��ҵ��ַ ��<span style="color:red;">{{$value.wydz}}</span></p>
    <p>��� ��
    <div >
    {{each $value.xc as xclist xcindex}}
    <img src="{{xclist}}" width=100 height=100 />    
    {{/each}}
    </div>
    </p>
    <br/>
{{/each}}