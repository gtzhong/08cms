{{each list}} 
	<p style="font-size:16px;font-weight:bold;">{{$value.subject}} <img src="{{$value.thumb}}" width=100 height=100/></p>
    <p>物业类型：<span style="color:red;">{{$value.ccid12title}}</span></p> 
    <p>销售状态：<span style="color:red;">{{$value.ccid18title}}</span></p> 
    <p>开盘时间 ：<span style="color:red;">{{$value.kprq}}</span></p>
    <p>容积率 ：<span style="color:red;">{{$value.yjl}}</span></p>
     <p>绿化率 ：<span style="color:red;">{{$value.lhl}}</span></p>
    <p>销售电话 ：<span style="color:red;">{{$value.tel}}</span></p>
    <p>销售地址 ：<span style="color:red;">{{$value.sldz}}</span></p>
    <p>占地面积 ：<span style="color:red;">{{$value.jzmj}}</span></p>
    <p>楼盘地址 ：<span style="color:red;">{{$value.address}}</span></p>
    <p>物业地址 ：<span style="color:red;">{{$value.wydz}}</span></p>
    <p>相册 ：
    <div >
    {{each $value.xc as xclist xcindex}}
    <img src="{{xclist}}" width=100 height=100 />    
    {{/each}}
    </div>
    </p>
    <br/>
{{/each}}