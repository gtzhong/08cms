<?
(!defined('M_COM') || !defined('M_ADMIN')) && exit('No Permission');
aheader();
if($re = $curuser->NoBackFunc('affix')) cls_message::show($re);
include_once M_ROOT."include/database.fun.php";
$objcron=new cls_cron();
if($action == 'cronedit'){
	backnav('otherset','misc');
	if(!submitcheck('submitcron')  && !submitcheck('bsubmitcron')){
		tabheader('�ƻ�����'."&nbsp; &nbsp; >><a onclick=\"return floatwin('open_channeledit',this)\" href=\"?entry=misc&action=cronadd\" onclick=\"return floatwin('open_inarchive',this)\">���Ӽƻ�����</a>",'cronedit',"?entry=misc&action=cronedit",7);
		$cy_arr = array("<input class=\"checkbox\" type=\"checkbox\" name=\"chkall\" onclick=\"checkall(this.form, 'selectid','chkall')\">",array('��������','txtL'),'����','ʱ��','�ϴ�ִ��ʱ��','�´�ִ��ʱ��','ɾ?','����');
		trcategory($cy_arr);
		$query = $db->query("SELECT * FROM {$tblprefix}cron ORDER BY type DESC,cronid");
		$itemstr = '';
		while($row = $db->fetch_array($query)){
			$filenamestr=str_replace(array('..', '/', '\\'), array('', '', ''), $row['filename']);
			$selectstr = "<input class=\"checkbox\" type=\"checkbox\"".($row['type']=='system' ? " disabled=\"disabled\"" : " name=\"delete[$row[cronid]]\" value=\"$row[cronid]\" onclick=\"deltip(this,$no_deepmode)\"").">";
			$namestr ="<input type=\"text\" value=\"$row[name]\" name=\"namenew[$row[cronid]]\" size=\"25\">";
			$availablestr = $row['available'] ? "<input type=\"checkbox\" checked=\"checked\"  value=\"1\" name=\"availablenew[$row[cronid]]\">" : "<input type=\"checkbox\" class=\"checkbox\" ".((empty($filenamestr) || $row['available']) ? "disabled=\"disabled\"" : "")." value=\"1\" name=\"availablenew[$row[cronid]]\">";
			
			$typestr = $row['type'] == 'system' ? '����' : '��չ';
			$lastrunstr = $row['lastrun'] ? date('Y-m-d H:i',$row['lastrun']) : '-';
			$nextrunstr = $row['nextrun'] ? date('Y-m-d H:i',$row['nextrun']) : '-';
			if($row['day'] > 0 && $row['day'] < 32) {
				$row['time'] = 'ÿ��'.$row['day'].'��';
			} elseif($row['weekday'] >= 0 && $row['weekday'] < 7) {
				switch($row['weekday']){
					case 0:
						$weekstr = '��';
						break;
					case 1:
						$weekstr = 'һ';
						break;
					case 2:
						$weekstr = '��';
						break;
					case 3:
						$weekstr = '��';
						break;
					case 4:
						$weekstr = '��';
						break;
					case 5:
						$weekstr = '��';
						break;
					case 6:
						$weekstr = '��';
						break;			
				}
				$row['time'] = 'ÿ��'.$weekstr;
			} elseif($row['hour'] >= 0 && $row['hour'] < 24) {
				$row['time'] = 'ÿ��';
			} else{
				$row['time'] = 'ÿСʱ';
			}
			if(!in_array($row['hour'], array(-1, ''))) {
				foreach($row['hour'] = explode("\t", $row['hour']) as $k => $v) {
					$row['hour'][$k] = sprintf('%02d', $v);
				}
				$row['hour'] = implode(',', $row['hour']);
				$row['time'] .= $row['hour'].'ʱ';
			}
			
				
			#$row['time'] .= $row['hour'] >= 0 && $row['hour'] < 24 ? sprintf('%02d', $row['hour']).'ʱ' : '';
			
			
			
			if(!in_array($row['minute'], array(-1, ''))) {
				$row['time'] .= '';
				foreach($row['minute'] = explode("\t", $row['minute']) as $k => $v) {
					$row['minute'][$k] = sprintf('%02d', $v);
				}
				$row['minute'] = implode(',', $row['minute']);
				$row['time'] .= $row['minute'].'��';
			} else {
				$row['time'] .= '00'.'��';
			}
			
			$timestr = $row['time'];

			$adminstr = '';
			$adminstr .= "<a href=\"?entry=misc&action=cronrun&cronid=$row[cronid]\" onclick=\"return floatwin('open_inarchive',this)\">ִ��</a>&nbsp; ";
			$adminstr .= "<a href=\"?entry=misc&action=crondetail&cronid=$row[cronid]\" onclick=\"return floatwin('open_inarchive',this)\">�༭</a>";
			$adminstr .= "&nbsp; <a href=\"?entry=misc&action=runTest&file=$filenamestr\" target='_blank'>����</a>";
			$itemstr .= "<tr class=\"txt\">";
			$itemstr .= "<td class=\"txtC w40\" ><input class=\"checkbox\" type=\"checkbox\" name=\"selectid[$row[cronid]]\" value=\"$row[cronid]\"></td>\n";
			$itemstr .= "<td class=\"txtL\">$namestr<br/>$filenamestr ($typestr)</td>\n";
			$itemstr .= "<td class=\"txtC\">$availablestr</td>\n";
			#$itemstr .= "<td class=\"txtC\">$typestr</td>\n";
			$itemstr .= "<td class=\"txtC w100\">$timestr</td>\n";	
			$itemstr .= "<td class=\"txtC\">$lastrunstr</td>\n";
			$itemstr .= "<td class=\"txtC\">$nextrunstr</td>\n";
			$itemstr .= "<td class=\"txtC w40\" >$selectstr</td><td class=\"txtR\">$adminstr</td>\n";
			$itemstr .= "</tr>\n";
		}
		echo $itemstr;
		tabfooter();
		tabfooter('bsubmitcron','ִ�мƻ�����','&nbsp; &nbsp;<input class="button" type="submit" name="submitcron" value="�ύ" >');
		a_guide('misc');		
	}elseif(submitcheck('submitcron')){
		if(!empty($delete) && deep_allow($no_deepmode)){
			foreach($delete as $k){
				$db->query("DELETE FROM {$tblprefix}cron WHERE cronid='$k' AND type='user'");
				unset($delete[$k]);
			}
		}
		if(!empty($namenew)){
			foreach($namenew as $k=>$v){
				$db->query("UPDATE {$tblprefix}cron set name='$v' WHERE cronid='$k'");
			}
		}
		if(!empty($availablenew)){
			$query=$db->query("SELECT cronid FROM {$tblprefix}cron");
			while($row=$db->fetch_array($query)){
				if(empty($availablenew[$row['cronid']])){
					$db->query("UPDATE {$tblprefix}cron set available='0' WHERE cronid='$row[cronid]'");
				}else{
					$db->query("UPDATE {$tblprefix}cron set available='1' WHERE cronid='$row[cronid]'");
				}
			}		
		}else{
			$db->query("UPDATE {$tblprefix}cron set available='0'");
		}
		cls_message::show('�ƻ�������³ɹ�', "?entry=misc&action=cronedit");
	}elseif(submitcheck('bsubmitcron')){		
		//һ��ִ�мƻ�����		
		if(empty($selectid)) cls_message::show('��ѡ��Ҫִ�еļƻ�����',axaction(1,M_REFERER));
		$_is_available_arr = array();
		$_is_available_sql = $db->query("SELECT cronid FROM {$tblprefix}cron WHERE available ='1'");
		while($r = $db->fetch_array($_is_available_sql)){
			$_is_available_arr[] = $r['cronid'];
		}			
		foreach($selectid as $m=>$n){
			if(in_array($n,$_is_available_arr))$objcron->run($n);
		}		
		cls_message::show('�ƻ�����ִ�гɹ�', "?entry=misc&action=cronedit");
	}
}elseif($action == 'crondetail'){
	if(!($cron = $db->fetch_one("SELECT * FROM {$tblprefix}cron WHERE cronid='$cronid'"))) cls_message::show('��������');
	if(!submitcheck('miscedit')){	
		$cronminute = str_replace("\t", ',', $cron['minute']);
		$hours = str_replace("\t", ',', $cron['hour']);
		$weekdayselect=$dayselect=$hourselect="<option value=\"-1\">*</option>";
		for($i = 0; $i <= 6; $i++) {
			switch($i){
					case 0:
						$weekstr = '��';
						break;
					case 1:
						$weekstr = 'һ';
						break;
					case 2:
						$weekstr = '��';
						break;
					case 3:
						$weekstr = '��';
						break;
					case 4:
						$weekstr = '��';
						break;
					case 5:
						$weekstr = '��';
						break;
					case 6:
						$weekstr = '��';
						break;			
				}
			$weekdayselect .= "<option value=\"$i\" ".($cron['weekday'] == $i ? 'selected' : '').">".$weekstr."</option>";
		}

		for($i = 1; $i <= 31; $i++) {
			$dayselect .= "<option value=\"$i\" ".($cron['day'] == $i ? 'selected' : '').">$i ��</option>";
		}

		for($i = 0; $i <= 23; $i++) {
			$hourselect .= "<option value=\"$i\" ".($cron['hour'] == $i ? 'selected' : '').">$i ʱ</option>";
		}
		
		tabheader('�༭�ƻ�����','miscedit',"?entry=misc&action=crondetail&cronid=$cronid");
		trbasic('��������','namenew',$cron['name'],'text',array('guide'=>'������������ƣ��������������Ŀ�ġ�'));
		trbasic('ÿ��','weekdaynew',$weekdayselect,'select',array('guide'=>'�������ڼ�ִ�б�����"*"Ϊ�����ƣ������ûḲ�������"��"�趨'));
		trbasic('ÿ��','daynew',$dayselect,'select',array('guide'=>'������һ��ִ�б�����"*"Ϊ������'));
		trbasic('Сʱ','hournew',$hours,'text',array('guide'=>'������ЩСʱִ�б�����ʱ����24Сʱ�ơ������������ 12 ��Сʱֵ�����ֵ֮���ð�Ƕ���","����������Ϊ������'));
		trbasic('����','minutenew',$cronminute,'text',array('guide'=>'������Щ����ִ�б���������������� 12 ������ֵ�����ֵ֮���ð�Ƕ���","����������Ϊ������'));
		trbasic('����ű�','filenamenew',$cron['filename'],'text',array('guide'=>'���ñ������ִ�г����ļ������������·�������ĳ���ű�ͳһ�����'.$objcron->getPath(0).'Ŀ¼�У���չ����ű�ͳһ����'.$objcron->getPath(1).'Ŀ¼�С�'));
		tabfooter('miscedit');
		a_guide('alangdetail');		
	}else{
		$filenamenew = $filenamenew ? $filenamenew : '';
		$cron=array();
		if(empty($filenamenew) || !$objcron->isFile($filenamenew)) cls_message::show('����ִ���ļ������ڻ��������������������д��',axaction(6,'?entry=misc&action=crondetail&cronid=$cronid'));
		$minutenew=str_replace(',',"\t",$minutenew);
		$hournew=str_replace(',',"\t",$hournew);
		empty($hournew) && $hournew = '-1';
        $db->update( '#__cron', 
            array(
                'name'=>trim($namenew),
                'weekday'=>$weekdaynew,
                'day'=>$daynew,
                'hour'=>$hournew,
                'minute'=>$minutenew,
                'filename'=>trim($filenamenew),
                'nextrun'=>$timestamp,
            )
        )->where("cronid = $cronid")->exec();
		adminlog('�༭��̨�ƻ���������');
		$objcron->run($cronid);
		cls_message::show('�ƻ�������³ɹ�',axaction(6,'?entry=misc&action=cronedit'));	
	}
}elseif($action == 'cronadd'){
	if(!submitcheck('cronadd')){
		$weekdayselect=$dayselect=$hourselect="<option value=\"-1\">*</option>";
		for($i = 0; $i <= 6; $i++) {
			switch($i){
					case 0:
						$weekstr = '��';
						break;
					case 1:
						$weekstr = 'һ';
						break;
					case 2:
						$weekstr = '��';
						break;
					case 3:
						$weekstr = '��';
						break;
					case 4:
						$weekstr = '��';
						break;
					case 5:
						$weekstr = '��';
						break;
					case 6:
						$weekstr = '��';
						break;			
				}
			$weekdayselect .= "<option value=\"$i\" >$weekstr</option>";
		}
        
		for($i = 1; $i <= 31; $i++) {
			$dayselect .= "<option value=\"$i\" >$i ��</option>";
		}

		for($i = 0; $i <= 23; $i++) {
			$hourselect .= "<option value=\"$i\" >$i ʱ</option>";
		}
        
		tabheader('�������','cronadd','?entry=misc&action=cronadd');
        trbasic('��������','nameadd'); 
		trbasic('ÿ��','weekdayadd',$weekdayselect,'select',array('guide'=>'�������ڼ�ִ�б�����"*"Ϊ�����ƣ������ûḲ�������"��"�趨'));
		trbasic('ÿ��','dayadd',$dayselect,'select',array('guide'=>'������һ��ִ�б�����"*"Ϊ������'));
		trbasic('Сʱ','houradd','','text',array('guide'=>'������ЩСʱִ�б�����ʱ����24Сʱ�ơ������������ 12 ��Сʱֵ�����ֵ֮���ð�Ƕ���","����������Ϊ������'));
		trbasic('����','minuteadd','','text',array('guide'=>'������Щ����ִ�б���������������� 12 ������ֵ�����ֵ֮���ð�Ƕ���","����������Ϊ������'));
		trbasic('����ű�','filenameadd','','text',array('guide'=>'���ñ������ִ�г����ļ������������·�������ĳ���ű�ͳһ�����'.$objcron->getPath(0).'Ŀ¼�У���չ����ű�ͳһ����'.$objcron->getPath(1).'Ŀ¼�С�'));
	
		tabfooter('cronadd','���');
	}else{
		$filenameadd = trim($filenameadd);
		if(empty($filenameadd) || !$objcron->isFile($filenameadd)) cls_message::show('����ִ���ļ������ڣ���������д��',M_REFERER);
        $minuteadd=str_replace(',',"\t",$minuteadd);
		$houradd=str_replace(',',"\t",$houradd);
		empty($houradd) && $houradd = '-1';
        $type = _08_EXTEND_DIR == 'extend_sample' ? 'system' : 'user';
        $db->insert( '#__cron', 
            array(
                'name'=>$nameadd,
                'weekday'=>$weekdayadd,
                'day'=>$dayadd,
                'hour'=>$houradd,
                'minute'=>$minuteadd,
                'filename'=>$filenameadd,
                'available'=>0,
                'type'=>$type,
                'nextrun'=>$timestamp,
            )
        )->exec();
		$objcron->run($db->insert_id());
		adminlog('��Ӽƻ�����');
		cls_message::show('�ƻ�������ӳɹ�',axaction(6,'?entry=misc&action=cronedit'));	
	}

}elseif($action == 'cronrun'){
    $cronid = max(0,intval($cronid));
    $ret = $objcron->run($cronid);
    $msg = $ret ? '�ƻ�����ִ�гɹ�' : '<span style="color:red;">�ƻ�����ûִ��</span>';
	cls_message::show($msg,axaction(6,'?entry=misc&action=cronedit'));
}elseif($action == 'runTest'){
    $filenamestr=str_replace(array('..', '/', '\\'), array('', '', ''), $file);    
	$cronfile = $objcron->isFile($filenamestr);    
    if($cronfile) {
        include(M_ROOT.$objcron->getPath(0).'exec.cron.php'); 
        include $cronfile;
    }else{
       cls_message::show("$filenamestr �ƻ�����ű�������!"); 
    }
    $fileClass = trim($file);       
    $fileClass = 'cron_' . str_replace(strrchr($fileClass,'.'),'',$fileClass);
    new $fileClass();	
	cls_message::show("$file:�ƻ�����ִ�гɹ�.");
}

?>