<?
$chid = 4;//ָ��chid
$caid = 2;
cls_env::SetG('chid',$chid);
cls_env::SetG('caid',$caid);

$oA = new cls_archive();
$isadd = $oA->isadd;
$oA->top_head();//�ļ�ͷ��
$oA->read_data();

/* ����ǰ�Ĵ���ļ���,�ڲ��ֶ��ƴ����У���ֱ��ʹ���������� */
$chid = &$oA->chid;
$arc = &$oA->arc;
$channel = &$oA->channel; 

$ftemp = &$oA->fields; //cls_cache::Read('fields',$chid);
$fields = array(); 
foreach($ftemp as $k => $field){
	if($field['cname']){
		$field['cname'] = str_replace('¥��','С��',$field['cname']);
		$fields[$k] = $field; 
	}
} 
$oA->fields = $fields;
#-----------------

if(!submitcheck('bsubmit')){
	
	if($isadd){//��Ӳ���Ҫ
		$oA->fm_pre_cns();
	}
	//$oA->fm_allow(); //������ǰ��Ա��Ȩ��
	
	$oA->fm_header("���С��","?action=$action");
	$oA->fm_fields(array('subject')); //����
	$oA->fm_rccid1(); // 1,2,����
	//$oA->fm_rccid3(); // 3,14,����
	$oA->fm_fields(array('address','dt')); //¥�̵�ַ,��ͼ
	$oA->fm_regcode('arctemp15');
	$oA->fm_footer('bsubmit');
	$oA->fm_lpExist();
	$oA->fm_guide_bm('','0');
	
}else{
	if($isadd){
		$oA->sv_regcode('arctemp15');
	} //$oA->sv_allow();
	
	// �Ž���ʱ��, �����ĵ��ദ��ʽ
	$tabtmp = "{$tblprefix}arctemp15";
	$fieldu = array('subject','mid','mname','address','createdate','dt','ggdt','dt_0','dt_1','ccid1','ccid2');
	
	$fmdata['mid'] = $curuser->info['mid'];
	$fmdata['mname'] = $curuser->info['mname']; 
	$fmdata['createdate'] = $timestamp;
	
	$sql = "INSERT INTO $tabtmp VALUES(NULL";
	foreach($fieldu as $k){ 
		if(isset($fmdata[$k])){
			$sql .= ",'$fmdata[$k]'";
		}else{
			$sql .= ",''";
		}
	}
	$sql .= ")"; 
	
	$db->query($sql);
	echo "
	<script type='text/javascript'>
	window.parent.sendaid2(0,'$fmdata[subject]','$fmdata[ccid1]','$fmdata[ccid2]','$fmdata[address]','$fmdata[dt]');
	window.parent.divin.style.display='none';
	window.parent.divin.nextSibling.style.display='none';
	</script>
	";
	cls_message::show('С��������',axaction(2,M_REFERER));
}

/*
<script> ֮ǰ�Ĵ��룬�����֪����ɶ?
CWindow.getWindow(document.CWindow_wid).beforeclose = function(){
	var popener = top.win ?  CWindow.getWindow(top.win)._data.area.CONTENT.dom.childNodes[0].contentWindow : parent;
	var regcode = popener.document.getElementById('regcode');
	if(regcode!=null)regcode.parentNode.getElementsByTagName('IMG')[0].src +=1;
};
</script>
*/

?>