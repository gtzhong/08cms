<?
(!defined('M_COM') || !defined('M_ADMIN')) && exit('No Permission');
$deal || $deal = 'search';
$deal == 'gtype' || backnav('channelex',$deal);
// $channel = fetch_one($chid)
if($chid==2) $coarr = array(1,2,3,14,5,6,34,44); //����
if($chid==3) $coarr = array(1,2,3,14,4,6,34,43); //���ַ�
if($chid==4) $coarr = array(1,2,3,14,12,17,18); //�·�
if($chid==11) $coarr = array(1,6,12); //����
if($chid==107) $coarr = array(1,2,3,14,6,18); //�ؼ۷�
if($deal == 'search'){
	$fields = cls_cache::Read('fields',$chid);	
	$na = array(); $nb = array();
	 
	foreach($cotypes as $k => $v){ //��ϵ
		in_array($k,$coarr) && $na[$k] = $v['cname'];
	}
	
	foreach($fields as $k => $v){ //ģ���ֶ�		
		in_array($v['datatype'],array('select','mselect')) && $na[$k] = $v['cname'];
	}
	
	$_all_select_fields = $na;
	
	if(!empty($channel['cfgs']['sf_order'])){		
		$_p_arr = explode(',',$channel['cfgs']['sf_order']);
		$_new_arr = array();
		foreach($_p_arr as $k => $v){
			if(isset($na[$v]) && !empty($na[$v])){
				$_new_arr[$v]=$na[$v];
			}
		}
		$na = $_new_arr;
	}	
	//�¼������ֶ�
	if($_diff_fields = array_diff($_all_select_fields,$na)) {
		foreach($_diff_fields as $k => $v){
			$na[$k] = $v;
		}
	}

	if(!submitcheck('bchanneldetail')){	
		echo "<form id=\"channeldetail\" class=\" validator\" action=\"?entry=$entry&action=$action&deal=$deal&chid=$chid\" method=\"post\" name=\"channeldetail\">";
		tabheader_e();
		
		echo "<tr style=\"background:#F1F7FD;\"><td align=\"left\" style=\"border-bottom:1px dotted #AAD6F6;  color:#134D9D; font-weight:bold;\">����</td><td align=\"left\" style=\"border-bottom:1px dotted #AAD6F6;  color:#134D9D; font-weight:bold;\">".$channel['cname']."- ����ѡ��</td></tr>";
		echo '<input type="hidden" name="fmext[ordstr]" id="fmext[ordstr]" value="'.$ordstr.'">'; //trhidden('fmext[ordstr]',$ordstr);
		$_paixv_id = 1;
		foreach($na as $k => $v){
			eval("\$searchfields = ".$channels[$chid]['searchfields'].'; ');
			$val = empty($searchfields[$k]) ? '' : $searchfields[$k];
			$flagKey = "\n<span style='display:none'>$k</span>"; 			
			$_paixv_input = "<input type=\"text\" name=\"fmext[ordstr][$k]\" value=\"".$_paixv_id."\" style=\"width:50px; float:left;\">";
			
			trbasic("$flagKey $_paixv_input $k-$v <input class=\"checkbox\" type=\"checkbox\" name=\"fmdata[$k][0]\" value=\"1\"".($val ? ' checked' : '').(in_array($k,array(1,2)) ? ' disabled' : '').">","fmdata[$k][1]",$val ? $val : $v,'text',array('width' => '40%','validate'=>makesubmitstr("fmdata[$k][1]",1,0,2,30),));

			$_paixv_id++;			
		}		
		tabfooter('bchanneldetail');
		a_guide('&nbsp;&nbsp;&nbsp;1.���������ĳ������ѡ���ô�ڱ�ģ�͵Ķ�Ӧ�ֶε������������棬�ѡ�����������������һ����Ϊ��ȷ���������Ƿ�Χ������<br/>&nbsp;&nbsp;&nbsp;2.���ĳ�ֶα�����������ѡ��������ĵ�ģ���Ѿ��Ѹ��ֶ�ɾ��������򿪱�ҳ�������ύһ�����ݡ�',1);		
	}else{
		asort($fmext['ordstr']);//������Ԫ�ذ�ֵ��������
		$ordarr = $fmext['ordstr'];
		$_new_sf_order = implode(',',array_keys($fmext['ordstr']));			
		$searchfields = array();
		foreach($ordarr as $k => $v){
			if(in_array($k,array(1,2))){
				$searchfields[$k] = empty($fmdata[$k][1]) ? $na[$k] : $fmdata[$k][1];
			}elseif(!empty($fmdata[$k][0]) && !empty($fmdata[$k][1])) $searchfields[$k] = $fmdata[$k][1];
		}
		$searchfields = $searchfields ? addslashes(var_export($searchfields,TRUE)) : '';		
		
		if(empty($channel['cfgs'])){
			$_sf_order = addslashes(var_export(array('sf_order'=>$_new_sf_order),TRUE));
			$_sf_order = empty($_sf_order) ? '' : trim($_sf_order);
			$_cfgs = varexp2arr($_sf_order);
			$_cfgs = !empty($_cfgs) ? addslashes(var_export($_cfgs,TRUE)) : '';				
			$db->query("UPDATE {$tblprefix}channels SET searchfields='$searchfields',cfgs='$_cfgs',cfgs0='$_sf_order' WHERE chid='$chid'");
		}else{
			$channel['cfgs']['sf_order'] = $_new_sf_order;	
			$_new_cfgs0 = addslashes(var_export($channel['cfgs'],TRUE));
			$_new_cfgs0 = trim($_new_cfgs0);
			$_new_cfgs = varexp2arr($_new_cfgs0);
			$_new_cfgs = addslashes(var_export($_new_cfgs,TRUE));			
			$db->query("UPDATE {$tblprefix}channels SET searchfields='$searchfields',cfgs='$_new_cfgs',cfgs0='$_new_cfgs0' WHERE chid='$chid'");
		}	
		cls_CacheFile::Update('channels');
		adminlog('�༭�ĵ�ģ��-'.$channel['cname']);
		cls_message::show('ģ�ͱ༭���!',"?entry=$entry&action=$action&deal=$deal&chid=$chid");
	}
}elseif($deal == 'other'){
	if(!submitcheck('bchanneldetail')){
		tabheader($channel['cname'].' - �߼���չ����','channeldetail',"?entry=$entry&action=$action&deal=$deal&chid=$chid");
		trbasic('���ò�������'.($channel['cfgs0'] && !$channel['cfgs'] ? '<br>�����ʽ���������!' : ''),'channelnew[cfgs0]',empty($channel['cfgs']) ? (empty($channel['cfgs0']) ? '' : $channel['cfgs0']) : var_export($channel['cfgs'],1),'textarea',array('w' => 500,'h' => 300,'guide'=>'��array()���룬����������Ҫ��php�淶'));
		trbasic('����˵��','channelnew[content]',$channel['content'],'textarea',array('w' => 500,'h' => 300,));
		tabfooter('bchanneldetail');
	}else{
		$channelnew['cfgs0'] = empty($channelnew['cfgs0']) ? '' : trim($channelnew['cfgs0']);
		$channelnew['cfgs'] = varexp2arr($channelnew['cfgs0']);
		$channelnew['content'] = empty($channelnew['content']) ? '' : trim($channelnew['content']);
		$channelnew['cfgs'] = !empty($channelnew['cfgs']) ? addslashes(var_export($channelnew['cfgs'],TRUE)) : '';
		$db->query("UPDATE {$tblprefix}channels SET
					content='$channelnew[content]',
					cfgs0='$channelnew[cfgs0]',
					cfgs='$channelnew[cfgs]'
					WHERE chid='$chid'");
		cls_CacheFile::Update('channels');
		adminlog('�༭�ĵ�ģ��-'.$channel['cname']);
		cls_message::show('ģ�ͱ༭���!',"?entry=$entry&action=$action&deal=$deal&chid=$chid");
	}	
}else mexit('��������');

?>
