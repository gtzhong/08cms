<?php
(!defined('M_COM') || !defined('M_ADMIN')) && exit('No Permission');
aheader();
if($re = $curuser->NoBackFunc('database')) cls_message::show($re);
/*
��ʾ:����: ?entry=dbstkeys&action=output&aext=1��������ʾ�����¶Ա��ļ�
��ϵͳ�������԰�ʱ�����°�ǰ�������������Ա��ļ�������ã�
��װ/����:�������汾��������,��Ҫ����ȫ���������ݿ�����½��С�
*/
backnav('data','dbstkeys');
$cacpath = _08_EXTEND_DIR.DS._08_CACHE_DIR.DS._08_SYSCACHE_DIR.DS.'dbst_keys.cac.php';
if(empty($action)) $action = 'compare';
if(empty($aext)) $aext = '';
$acts = array('compare'=>'��׼�����Ա�','output'=>'������������','noprikey'=>'�����������б�');
//'getsql'=>'���ɲ�ȫ������sql',
$amsg = '��׼�����Ա�'; $alnk = '';
if(!empty($aext)){
	foreach($acts as $k=>$v){
		if($k==$action){
			$alnk .= "<span style='color:#f00'>$v</span>";
			$amsg = $v;
		}else{
			$alnk .= "<a href='?entry=dbstkeys&action=$k&aext=1'>$v</a>";
		}
		$alnk = "<span style='float:right'>$alnk</span>";
	}
}else{
	$alnk = "";
}

$db2 = clone $db;
tabheader(" �����Աȹ��� --- &nbsp; $alnk &nbsp; $amsg &nbsp; "); //,'cfdebug',"?entry=$entry&action=$action"
if($action == 'compare'){
	
	if(!(is_file(M_ROOT.$cacpath))){
		echo "\n<tr><td class=\"txtC\"><span style='color:red;'>(��׼�����ļ�������)</span>\n</td></tr>";	
	}else{

		$a1 = cls_cache::exRead('dbst_keys'); 
		$a2 = dbIndexs($dbname); //
		
		echo "\n<tr><td width='480'>";
		echo arrShow($a1,$a2,'[��׼����]����,��[��ǰ���ݿ�] ����������','[��׼����]��[��ǰ���ݿ�]��ͬ','��ǰ���ݿ�');
		echo arrShow($a2,$a1,'[��ǰ���ݿ�]����,��[��׼����] ����������','[��ǰ���ݿ�]��[��׼����]��ͬ','��׼����');
		//echo arrShow($pub0,'[��׼����/��ǰ���ݿ�] ��������','[��׼����]��[��ǰ���ݿ�] ��ȫ��ͬ');
		
		$str = ''; //$i=0;
		$str .= "\n<tr><td colspan=3><div class='conlist1'>��׼���� ---- (".count($a1).")�� \n</div></td></tr>";
		$str .= "<th class=\"txtC\" width='28%'>���ݱ�</th>
				 <th class=\"txtC\" width='28%'>��������</th>
				 <th class=\"txtC\" >�����ֶ�</th>";
		foreach($a1 as $k1=>$v1){
			$item = explode('~', $k1);
			$str .= "\n<tr><td>$item[0]</td><td>$item[1]</td><td class=\"txtL\">$v1</td></tr>";	
		}
		echo "<table class=' tb tb2 bdbot'>$str</table>";
		
		echo "\n</td></tr>";

	}

}elseif($action == 'output'){
		
	echo "\n<tr><td class=\"txtC w200\" valign='top'><b>��ǰ��������</b></td>\n";
	echo "<td class=\"txtL\" style='line-height:120%;'><pre>(�ɸ����������ݵ�{$cacpath}�ļ�,�ֶ�����[��׼����]�ԱȻ���)\n"; 

	$a2 = dbIndexs($dbname);
	foreach($a2 as $k=>$v){
		echo "\n'$k'=>'$v',"; 
	}

	echo "\n</pre><br>\n(�ɸ��Ƶ�{$cacpath}�ļ�,�ֶ�����[��׼����]�ԱȻ���)</td></tr>";
	
}elseif($action == 'noprikey'){ //
	
	echo "\n<tr><td class=\"txtC w200\" valign='top'><b>���������ı�</b></td>\n";
	echo "<td class=\"txtL\">"; 
	
	$query = $db2->query("SHOW TABLES FROM $dbname", 'SILENT');
	$index = array(); $sql = '';
	while($v = $db2->fetch_row($query)){ 
	  $ind = $db2->query("SHOW index FROM $v[0]", 'SILENT');
	  $flag = 0; if(!$ind) continue;
	  while($t = @$db2->fetch_array($ind)){
		 	 $flag++;
	  }
	  if($flag<1){ 
	  	echo "\n$v[0]<br>"; 
		$fields = dbFields($v[0]); //print_r($fields);
		$sql .= "\n ALTER TABLE `$v[0]` ADD PRIMARY KEY ( `$fields[0]` ) <br>";
	  }
	}
	
	echo "\n</td></tr>";
	
	echo "\n<tr><td class=\"txtC w200\" valign='top'><b>��ȫ������sql</b></td>\n";
	echo "<td class=\"txtL\">\n$sql\n
	<br><span class='tips1'>������Ҫ, �ɸ���sql�ֶ�ִ��</span>
	</td></tr>";
		
} //echo "\n</pre><br>\n(���Ƶ�{$cacpath}�ļ�,�ֶ�����[��׼����]�ԱȻ���)</td></tr>";

tabfooter(''); 
a_guide('dbstkeys');


// arr���
function arrShow($a1,$a2,$title,$tnull,$cobj){
	
	$str = ''; $i=0;
	$str .= "\n<tr><td colspan=3><div class='conlist1'>$title\n</div></td></tr>";
	$str .= "<th class=\"txtC\" width='28%'>���ݱ�</th>
			 <th class=\"txtC\" width='28%'>��������</th>
			 <th class=\"txtC\" >�����ֶ�</th>";
	foreach($a1 as $k1=>$v1){
		$istr = '';
		$item = explode('~', $k1);
		if(!isset($a2[$k1])){
			$i++;
			$istr .= "<td>$item[0]</td><td>$item[1]</td><td class=\"txtL\"><span style='color:#f00;'>[{$cobj}�޴���]</span> $v1</td>";	
		}elseif($a2[$k1]!=$v1){
			$i++;
			$istr .= "<td>$item[0]</td><td>$item[1]</td><td class=\"txtL\">$v1 <br> <span style='color:#f00;'>[{$cobj}ֵΪ]</span> ".$a2[$k1]."</td>";	
		}
		$istr && $str .= "\n<tr>$istr</tr>";
	}
	if($i==0) $str .= "\n<tr><td colspan=3><span style='color:#f00;'>$tnull</span>\n</td></tr>";
	return "<table class=' tb tb2 bdbot'>$str</table>\n";
	
}

// db����
function dbIndexs($db){
	global $db2;
	$query = $db2->query("SHOW TABLES FROM $db", 'SILENT');
	$index = array(); //$indstr = "";
	while($v = $db2->fetch_row($query)){ 
	  $ind = $db2->query("SHOW index FROM $v[0]", 'SILENT');
	  while($t = @$db2->fetch_array($ind)){
		  $key = "$t[Table]~$t[Key_name]";
		  if(isset($index[$key])){
			  $index[$key] .= ",$t[Column_name]";
		  }else{
			  $index[$key] = "$t[Column_name]";
		  }
	} }
	return $index;
}
// db�ֶ�
function dbFields($tab){
	global $db2;
	$cols = array();
	$fields = $db2->query("show full fields from $tab", 'SILENT');
	if($fields){
	while($row = @$db2->fetch_array($fields)){
		  $cols[] = "$row[Field]"; // $v[0] : $row[Field] : $row[Type]
	} }
	return $cols;
}


?>