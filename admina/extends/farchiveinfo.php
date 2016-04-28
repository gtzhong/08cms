<?
(!defined('M_COM') || !defined('M_ADMIN')) && exit('No Permission');

$aid = empty($aid) ? 0 : max(0,intval($aid));
$detail = empty($detail) ? 0 : max(0,intval($detail));
$arc = new cls_farcedit;
$arc->set_aid($aid);

if($arc->aid && $detail){
	$tplname = cls_tpl::CommonTplname('farchive',$arc->archive['fcaid'],'arctpl'); 
	if($tplname){ 
		$vurl = cls_url::view_farcurl($arc->aid,$arc->archive['arcurl']);
		header("location:$vurl");
		die();
	}else{
		//cls_message::show("��������-{$arc->archive['fcaid']}-δ����ģ��");
		//������Ĵ���鿴Ч����
	}
}

aheader();
!$arc->aid && cls_message::show('��ָ����ȷ����ϢID');

if(!$detail){
	tabheader($arc->archive['subject'].' �ĸ�����Ϣ');
	trbasic('����','',$arc->archive['vieworder'],'');
	trbasic('����','',$arc->archive['mname'],'');
	trbasic('���ʱ��','',$arc->archive['createdate'] ? date('Y-m-d H:i:s',$arc->archive['createdate']) : '','');
	trbasic('����ʱ��','',$arc->archive['updatedate'] ? date('Y-m-d H:i:s',$arc->archive['updatedate']) : '','');
	trbasic('��ʼʱ��','',$arc->archive['startdate'] ? date('Y-m-d H:i:s',$arc->archive['startdate']) : '','');
	trbasic('����ʱ��','',$arc->archive['enddate'] ? date('Y-m-d H:i:s',$arc->archive['enddate']) : '','');
	tabfooter();
}else{
	$chid = $arc->chid;
	$fields = cls_cache::Read('ffields',$chid); //print_r($fields);
	$a_field = new cls_field;
	tabheader('����������Ϣ');
	$subject_table = 'farchives';
	foreach($fields as $k => $v){
		$flag = 1;
		$val = isset($arc->archive[$k]) ? $arc->archive[$k] : '';
		//$cms_abs = 'http://192.168.1.20/auto/'; //����
		if($k=='subject'){
			$color = $arc->archive['color']; //echo "$k,$color";
			if(strlen($color)>0) $val = "<span style='color:$color;'>$val</span>";
		}elseif($v['datatype']=='multitext'){
			if($val){
				$val = "<textarea rows='10' cols='64' style='width:640px; height:120px;'>$val</textarea>";
			}else{
				//$val = "<textarea rows='10' cols='64' style='width:980px; height:60px;'>(null)$v[datatype]</textarea>";
				$flag = 0;
			}
		}elseif($v['datatype']=='htmltext'){
			$val = "<div style='width:640px; height:120px; border:1px solid #CCC'>$val</div>";
		}elseif($v['datatype']=='image'){
			if($val){
				$val = view_checkurl($val);
				$val = '<a href="'.$val.'" target="_blank"><img src="'.$val.'" width="980" height="720" onload="javascript:setImgSize(this,980,720);" /></a>';
			}else{
				$flag = 0;
			}
		}elseif($v['datatype']=='flash'){
			if($val){
				$val = view_checkurl($val);
				$val = '<embed wmode="transparent" src="'.$val.'" quality="high" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" width=480 height=240></embed>';
			}else{
				$flag = 0;
			}
		}elseif(strstr(',cacc,select,mselect,', ','.$v['datatype'])){
			$a_field->init($v,isset($arc->archive[$k]) ? $arc->archive[$k] : '');
			$a_field->trfield('fmdata');
			$flag = 0;
		}elseif($v['datatype']=='----'){ // ����Ҫ���������
		}else{
			if($val){
				;
			}else{
				$flag = 0;
			}
		}
		if($flag) trbasic($v['cname'],'',$val,''); 
	}
	unset($a_field);
	tabfooter('');
}

/*

<option value="text">�����ı�</option>
<option value="multitext">�����ı�</option>
<option value="htmltext">Html�ı�</option>
<option value="image">��ͼ</option>
<option value="images">ͼ��</option>
<option value="flash">Flash</option>
<option value="flashs">Flash��</option>
<option value="media">��Ƶ</option>
<option value="medias">��Ƶ��</option>
<option value="file">��������</option>
<option value="files">�������</option>
<option value="select">����ѡ��</option>
<option value="mselect">����ѡ��</option>
<option value="cacc">��Ŀѡ��</option>
<option value="date">����(ʱ���)</option>
<option value="int">����</option>
<option value="float">С��</option>
<option value="map">��ͼ</option>
<option value="vote">ͶƱ</option>
<option value="texts">�ı���</option>

*/

?>
