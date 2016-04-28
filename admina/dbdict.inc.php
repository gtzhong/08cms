<?
(!defined('M_COM') || !defined('M_ADMIN')) && exit('No Permission');
if(@$action !== 'dictExpert') aheader();
if($re = $curuser->NoBackFunc('database')) cls_message::show($re);
$dbfields = cls_cache::Read('dbfields');
$db2 = clone $db;

if(empty($action)){
	$dbtable = empty($dbtable) ? 'catalogs' : $dbtable;
	if(!submitcheck('bdbdict')){
		backnav('data','dbdict');

		$dbtables = array('' => '��ѡ�����ݱ�');
		$tablists = cls_DbOther::tabLists();
		$dbtable = isset($tablists[$dbtable]) ? $dbtable : 'catalogs';
		$filterbox = 'ѡ�����ݱ�'.'&nbsp; &nbsp;';
		$filterbox .= "<select style=\"vertical-align: middle;\" name=\"dbtable\" onchange=\"redirect('?entry=dbdict&dbtable=' + this.options[this.selectedIndex].value);\">";
		foreach($tablists as $tab=>$r){
			$filterbox .= "<option value='$tab'".($dbtable == $tab ? ' selected' : '').">$tblprefix$tab --- $r[Comment] ($r[Rows])</option>";	
		}
		$filterbox .= "</select>";
		$lnk1 = "<a href='?entry=dbdict&action=dictCheck' onclick=\"return floatwin('open_dicCheck2',this,480,560)\">���&gt;&gt;</a>";
		$lnk2 = "<a href='?entry=dbdict&action=dictExpert' target='_blank'>����&gt;&gt;</a>";
		$exp = "<span style='float:right'> $lnk1 &nbsp; $lnk2 &nbsp; </span>";
		tabheader($exp.$filterbox);
		tabfooter();
		
		$tblfields = cls_DbOther::dictComment($dbtable);
		
		tabheader('���ݿ��ֶ��б�'.'&nbsp; -&nbsp; '.$dbtable,'dbdict',"?entry=dbdict&dbtable=$dbtable",5);
		trcategory(array('���','�ֶ�����','�ֶ�����','�����滻','�ֶα�ע'));

		$i = 1;
		foreach($tblfields as $k => $v){ 

			echo "<tr>".
				"<td class=\"txtC w30\">$i</td>\n".
				"<td class=\"txtL\"><b>$k</b></td>\n".
				"<td class=\"txtL\">$v->Type</td>\n".
				"<td class=\"txtC\">".($v->Key=='PRI'?'':"<a href=\"?entry=dbdict&action=dbreplace&dbtable=$dbtable&dbfield=$k\">&gt;&gt;".'�滻'."</a>")."</td>\n".
				"<td class=\"txtC\"><input type=\"text\" size=\"30\" name=\"dbfieldsnew[$dbtable][$k] xname=\"$k\" value=\"$v->Comment\" ></td>\n".
				"</tr>";
			$i ++;
		}
		tabfooter('bdbdict','�޸�');
		a_guide('dbfieldsremark');
	}else{
		if(!empty($dbfieldsnew)){
			foreach($dbfieldsnew as $k => $v){
				if(!empty($v)){
					foreach($v as $k1 => $v1){
						if(empty($v1)){
							$db2->query("DELETE FROM {$tblprefix}dbfields WHERE ddtable='$k' AND ddfield='$k1'");
						}else{
							if(!isset($dbfields[$k.'_'.$k1])){
								$db2->query("INSERT INTO {$tblprefix}dbfields SET ddtable='$k',ddfield='$k1',ddcomment='$v1'");
							}else $db2->query("UPDATE {$tblprefix}dbfields SET ddcomment='$v1' WHERE ddtable='$k' AND ddfield='$k1'");
						}
					}
				}
				
			}
		}
		cls_CacheFile::Update('dbfields');
		cls_message::show('���ݿ��ֶα�ע�޸����',"?entry=dbdict&dbtable=$dbtable");

	}

}elseif($action == 'dbreplace'){
	if(empty($dbtable)) cls_message::show('��ָ����ȷ�����ݱ�');
	if(empty($dbfield)) cls_message::show('��ָ����ȷ���ֶΡ�');
	if(!submitcheck('bdbreplace')){
		$mode0arr = array(0 => '����',1 => '����');
		tabheader('�ֶ������滻����','dbreplace',"?entry=dbdict&action=$action&dbtable=$dbtable&dbfield=$dbfield",2);
		trbasic('��ǰ���ݱ�','',$dbtable,'');
		trbasic('��ǰ�ֶ�','',$dbfield,'');
		trbasic('����ģʽ'.'&nbsp; [<a href="http://dev.mysql.com/doc/refman/5.1/zh/regexp.html" target="_blank">'.'�������'.'</a>]','mode',makeradio('mode',$mode0arr,0),'');
		trbasic('�����ı�','rpstring','','textarea');
		trbasic('�滻�ı�','tostring','','textarea');
		trbasic('WHERE���������ִ�','where','','text',array('guide'=>'��Ҫ��WHERE','w'=>50));
		tabfooter('bdbreplace','��ʼ�滻');
		a_guide('dbreplace');
	}else{
		if(!isset($mode)||!$rpstring||!$tostring)cls_message::show('����ģʽ,�����ı����滻�ı�����Ϊ��',M_REFERER);
		$rs=$db2->query("SHOW COLUMNS FROM $dbtable",'SILENT');
		unset($key);
		while($row=$db2->fetch_array($rs))
			if('PRI'==$row['Key']){
				$key=$row['Field'];
				break;
			}
		if(1==$mode){
			if(!isset($key))cls_message::show('���ݱ�û������',M_REFERER);
			if($dbfield == $key)cls_message::show('�벻Ҫ�������������ֶΡ�',M_REFERER);
			$rpstring=stripslashes($rpstring);
			$tostring=stripslashes($tostring);
			$where=$where?" and $where":'';
			$rs=$db2->query("select `$key`,`$dbfield` from `$dbtable` where `{$tblprefix}$dbfield` REGEXP '".str_replace(array("\\","'"),array("\\\\","\\'"),$rpstring)."'$where");
			$count=$db2->num_rows($rs);
			if(0==$count)cls_message::show('û���ҵ����������ļ�¼��',M_REFERER);
			$replace=0;
			while($row=$db2->fetch_array($rs))
				if($db2->query("update `$dbtable` set `$dbfield` = '".addslashes(preg_replace($rpstring,$tostring,$row[$dbfield]))."' where `$key` = '".addslashes($row[$key])."'")) $replace++;
			cls_message::show('����'.$count.'����¼���ɹ��滻'.$replace.'����¼��',M_REFERER);
		}else{
			if(isset($key)&&$dbfield == $key)cls_message::show('�벻Ҫ�������������ֶΡ�',M_REFERER);
			$where = $where ? " where $where" : '';
			$db2->query("update `$dbtable` set `$dbfield`=replace(`$dbfield`,'$rpstring','$tostring')$where");
			cls_message::show('�ɹ��滻'.$db2->affected_rows().'����¼��',M_REFERER);
		}
	}
	
}elseif($action=='dictExpert' || $action=='dictCheck'){ // ���,����
	$tdoc = ftplDoc();
	$ttab = ftplTab();
	$tablists = cls_DbOther::tabLists();
	$slist = $tlist = ''; 
	$clist = '<tr><td>���ݱ�</td><td>�ֶ�</td></tr>'; $n=0;
	foreach($tablists as $tab=>$r){ //echo "\n<br>$tab,";
		$t1 = $ttab; $ra='';
		$tblfields = cls_DbOther::dictComment($tab);
		if($action=='dictCheck' && empty($r['Comment'])){
			$clist .= "<tr><td>$tab</td><td>------</td></tr>\n";
		}
		foreach($tblfields as $fk=>$fv){ //Field Type Collation Null Key Default Extra Privileges Comment
			if($action=='dictCheck'){
				$t3 = substr($fk,0,3); //pid
				$t4 = substr($fk,0,4); //stat,ccid
				$t7 = substr($fk,0,7); //inorder,incheck
				if($t3=='pid' || in_array($t4,array('stat','ccid')) || in_array($t7,array('inorder','incheck'))) continue;
				empty($fv->Comment) && $clist .= "<tr><td>$tab</td><td>$fk</td></tr>\n";
			}else{
				$ra .= "<tr><td>$fv->Field</td><td>$fv->Comment</td><td>$fv->Type</td><td>$fv->Null</td><td>$fv->Key</td><td>$fv->Default</td></tr>\n";
			}
		} //if($n==1) print_r($fv);
		if($action!='dictCheck'){
			$slist .= str_replace(array('{fields}','{tabid}','{tabname}'),array($ra,$tab,$r['Comment']),$t1);
			$tlist .= "<a href='#$tab'>".($r['Comment'] ? $r['Comment'] : $tab)."</a>\n"; //$n++; if($n>20) break;
		}
	}
	if($action=='dictCheck'){
		$clist || $clist = '<tr><td>(�޼�¼)</td></tr>\n';
		echo "<div class='itemtitle'><h3>������� [���ݱ� : �ֶ�] û��ע��</h3></div><table class='tb tb2 bdbot'>$clist</table>";
	}else{
		$str = str_replace(array('{tablists}','{tabmap}','{tabcnt}','{sysname}'),array($slist,$tlist,count($tablists),cls_env::mconfig('hostname')),$tdoc);
		header("Content-Type:text/html;CharSet=$mcharset");
		header("Content-Disposition:attachment;Filename=dict-".date('Y-md-Hi',$timestamp).".html");
		die($str);
	}
}

function ftplTab(){
	return "<a name='{tabid}'></a>
<table border='0' align='center' cellpadding='5' cellspacing='1' class='tab'>
<tr class='title'><td colspan='6'><a href='#' class='r'>[Top]</a>{tabid}[{tabname}]</td></tr>
<tr class='head'><td width='20%'>Field</td><td width='25%'>Memo</td><td>Type</td><td width='10%'>Null</td><td width='10%'>Key</td><td width='10%'>Default</td></tr>
{fields}</table>";
}

function ftplDoc(){
	return '
<!DOCTYPE html><html xmlns="http://www.w3.org/1999/xhtml"><head>
<meta http-equiv="Content-Type" content="text/html; charset={mcharset}" />
<title>({sysname})���ݿ�ʵ�</title>
<style type="text/css">
body, td, th { font-size: 12px; }
a:link, a:visited { text-decoration: none; }
td.itm { padding-left:8px; }
td.itm a { width: 125px; height: 15; font-style: normal; float: left; overflow: hidden; border: 1px solid #CCC; white-space: nowrap; word-break: keep-all; padding: 3px; margin: 3px; }
a.r { float: right; }
table.tab { width: 720px; background: #69C; border: 1px solid #069; margin: 12px auto 1px auto; }
table tr td { background: #FFF; }
table tr.title td { font-weight: bold; background: #FFF; }
table tr.head td { font-weight: bold; background: #669; color: #FFF; }
table tr.bgFFF td { background-color: #FFF; }
table tr.bgCCC td { background-color: #F0F0F0; }
</style></head><body>
<table border="0" align="center" cellpadding="5" cellspacing="1" class="tab">
<tr class="title">
  <td colspan="6"><a href="?" class="r">����{tabcnt}��[ˢ��]</a>({sysname})���ݿ�ʵ�</td>
</tr>
<tr bgcolor="#FFFFFF">
  <td colspan="6" class="itm">
{tabmap}<a href="#~remark~">[��ע]</a>
  </td>
</tr>
</table>
{tablists}
<a name="~remark~"></a>
<table border="0" align="center" cellpadding="5" cellspacing="1" class="tab">
  <tr class="title"><td colspan="3"><a href="#" style="float:right">[Top]</a>[���ݿ�ʵ䱸ע]</td></tr>
  <tr class="head"><td width="20%">��Ŀ</td><td width="25%">�ֶ�:����</td><td>��ע</td></tr>
  <tr><td>��ϵ���</td><td>ccid*,<br>ccid*date����</td><td>*Ϊ���֣��ο���̨��<br>��վ�ܹ� &gt;&gt; ��Ŀ����</td></tr>
  <tr><td>ͳ�����</td><td>stat1,stat2,stat3... ��<br>stat_1,stat_2,stat_3...         </td><td>����ĵ�&lt�ƻ�����-ͳ���ֶ�.txt&gt������ͳ�ƽ���,�ϼ�,�ĵ������ȣ�<br>���ݸ�ϵͳ���壬��صı��У�archives*,coclass*,members_sub,</td></tr>
  <tr><td>��ͼ���</td><td>map,map_0,map_1��<br>ditu,ditu_0,dutu_1��<br>dt,dt_0,dt_1 </td><td>��Ӧ��ͼ���꼰�侭��,γ��</td></tr>
  <tr><td>�ϼ����</td><td>pid*:�ϼ���Ŀ<br>inorder*:����˳��<br>incheck*:�������</td><td>*Ϊ���֣��ο���̨��<br>��վ�ܹ� &gt;&gt; ��չ�ܹ� &gt;&gt; �ϼ���Ŀ����</td></tr>
</table>
</body></html>';	
}

?>
