<?PHP
(!defined('M_COM') || !defined('M_ADMIN')) && exit('No Permission');
aheader();
if($re = $curuser->NoBackFunc('farchive')) cls_message::show($re);

$arc = new cls_farcedit;
if(!($aid = $arc->set_aid(@$aid))) cls_message::show('��ָ����ȷ����ϢID');
if($re = $curuser->NoBackPmByTypeid($arc->archive['fcaid'],'fcaid')) cls_message::show($re);# ��ǰ��������ĺ�̨����Ȩ��
$fields = cls_fcatalog::Field($arc->archive['fcaid']);

if(!submitcheck('bsubmit')) {
	$a_field = new cls_field;
	tabheader($arc->catalog['title'].'-������Ϣ','farchivedetail',"?entry=$entry$extend_str&aid=$aid",2,1,1);
	$subject_table = 'farchives';
	
	foreach($fields as $k => $v){
		$a_field->init($v,isset($arc->archive[$k]) ? $arc->archive[$k] : '');
		$a_field->trfield('fmdata');
	}
	unset($a_field);
	cls_fcatalog::areaShow($arc->archive['fcaid'], $arc->archive['farea']); //ѡ�����
	if(empty($arc->catalog['nodurat'])){
		trbasic('��ʼ����',"fmdata[startdate]",$arc->archive['startdate'] ? date('Y-m-d',$arc->archive['startdate']) : '','calendar',array('guide'=>'��������������','validate'=>makesubmitstr("fmdata[startdate]",0,0,0,0,'date')));
		trbasic('��������',"fmdata[enddate]",$arc->archive['enddate'] ? date('Y-m-d',$arc->archive['enddate']) : '','calendar',array('guide'=>'��������������','validate'=>makesubmitstr("fmdata[enddate]",0,0,0,0,'date')));
	}
    if(!in_array((int)$arc->catalog['ftype'], array(1))) {
    	trbasic('��̬�����ʽ','fmdata[customurl]',$arc->archive['customurl'],'text',array('guide'=>'���հ����ڷ������á���:{$infodir}/{$aid}-{$page}.html��{$infodir}������Ŀ¼��{$y}�� {$m}�� {$d}�� {$aid}����id {$page}��ҳҳ�롣','w'=>50));
    }
	editColor($arc->archive['color']); //���ñ�����ɫ
	tabfooter('bsubmit');
	a_guide('farchivedetail');
}else{
	if(empty($arc->catalog['nodurat'])){
		foreach(array('startdate','enddate') as $var){
			$fmdata[$var] = trim($fmdata[$var]);
			$fmdata[$var] = !cls_string::isDate($fmdata[$var]) ? 0 : strtotime($fmdata[$var]);
			$arc->updatefield($var,max(0,intval($fmdata[$var])));
		}
	}
	if(isset($fmdata['customurl'])){
		$fmdata['customurl'] = preg_replace("/^\/+/",'',trim($fmdata['customurl']));
		$arc->updatefield('customurl',$fmdata['customurl']);
	}
	$c_upload = cls_upload::OneInstance();
	$a_field = new cls_field;
	foreach($fields as $k => $v){
		if(isset($fmdata[$k])){
			$a_field->init($v,isset($arc->archive[$k]) ? $arc->archive[$k] : '');
			$a_field->deal('fmdata','cls_message::show',axaction(2,M_REFERER));
			$arc->updatefield($k,$a_field->newvalue,$v['tbl']);
			if($arr = multi_val_arr($a_field->newvalue,$v)) foreach($arr as $x => $y) $arc->updatefield($k.'_'.$x,$y,$v['tbl']);
		}
	}
	unset($a_field);
	
	$areas = @$fmdata['farea']; //������� 
	$arc->updatefield('farea', $areas ,'farchives');
		
	$arc->arccolor();
	$arc->updatedb();
	$c_upload->closure(1,$aid,'farchives');
	$c_upload->saveuptotal(1);
	adminlog('��ϸ�޸ĸ�����Ϣ');
	cls_message::show('������Ϣ�༭���',axaction(6,M_REFERER));

}