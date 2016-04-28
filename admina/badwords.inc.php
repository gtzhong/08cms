<?
(!defined('M_COM') || !defined('M_ADMIN')) && exit('No Permission');
if(empty($action)){
	$page = !empty($page) ? max(1, intval($page)) : 1;
	aheader();
	if($re = $curuser->NoBackFunc('other')) cls_message::show($re);
	if(!submitcheck('bbadwordsadd') && !submitcheck('bbadwordsedit') || submitcheck('bbadwordsearch')){
		tabheader("��Ӳ�����&nbsp; &nbsp;>><a id=\"nobatch\" onclick=\"batch(0)\" href=\"#\">�������</a>&nbsp; &nbsp;>><a id=\"isbatch\" onclick=\"batch(1)\" href=\"#\">�������</a>",'badwordsadd','?entry=badwords');
		trhidden('isbatch', 0);
		trbasic('������','badwordadd[wsearch]');
		trbasic('�滻��','badwordadd[wreplace]');
		trbasic('�������������','badwordadd[batch]','','textarea',array('guide'=>'��ʽ��������=�滻�� ÿ��һ����¼'));
		tabfooter('bbadwordsadd','���');        
		tabheader("����������&nbsp; &nbsp;>>&nbsp;<input type=\"text\" name=\"bwsearch\" value=\"".(empty($bwsearch)?'':$bwsearch)."\" style=\"vertical-align:middle\" /> <input class=\"btn\" type=\"submit\" name=\"bbadwordsearch\" value=\"����\" />",'bbadwordsearch','?entry=badwords');
        echo '</table></form><div class="blank3"></div>';
        
		tabheader("�����ʹ���&nbsp; &nbsp;>><a onclick=\"return floatwin('open_$entry',this)\" href=\"?entry=$entry&action=bwimport\">����</a>&nbsp; &nbsp;>><a href=\"?entry=$entry&action=bwexport\">����</a>",'badwordsedit','?entry=badwords','3');
		
        trcategory(array('<input class="checkbox" type="checkbox" name="chkall" onclick="deltip(this,0,checkall,this.form)">ɾ?','������','�滻��'));
        if(!empty($bwsearch)){
            $where = 'WHERE';
            $where .= " wsearch like '%$bwsearch%' ";            
        }else{
            if(isset($where)) unset($where);
        }
		$pagetmp = $page;
		do{
			$query = $db->query("SELECT * FROM {$tblprefix}badwords ".(isset($where) ? $where : '')." ORDER BY bwid DESC LIMIT ".(($pagetmp - 1) * $atpp).",$atpp");
			$pagetmp--;
		} while(!$db->num_rows($query) && $pagetmp);

		while($badword = $db->fetch_array($query)){
			echo "<tr class=\"txt\">".
				"<td class=\"txtC w60\"><input class=\"checkbox\" type=\"checkbox\" name=\"delete[$badword[bwid]]\" value=\"$badword[bwid]\" onclick=\"deltip()\"></td>\n".
				"<td class=\"txtC\"><input type=\"text\" size=\"40\" name=\"badwordsnew[$badword[bwid]][wsearch]\" value=\"$badword[wsearch]\"></td>\n".
				"<td class=\"txtC\"><input type=\"text\" size=\"40\" name=\"badwordsnew[$badword[bwid]][wreplace]\" value=\"$badword[wreplace]\"></td></tr>\n";
		}
		$counts = $db->result_one("SELECT count(*) FROM {$tblprefix}badwords " . (isset($where) ? $where : ''));        
		$multi = multi($counts, $atpp, $page, "?entry=$entry".(empty($bwsearch) ? '' : '&bwsearch='.$bwsearch));
		tabfooter('bbadwordsedit','�޸�');
		echo $multi;

		a_guide('badwords');
		echo <<<SCRIPT
<script type="text/javascript">
	var dom1 = document.getElementById("badwordadd[wsearch]").parentNode.parentNode;
	var dom1sibling = get_nextsibling(dom1);
	var dom2 = document.getElementById("badwordadd[batch]").parentNode.parentNode;
		dom2.style.display="none";
	var dom3 = document.getElementsByName("isbatch")[0];

	var isbatch = document.getElementById("isbatch");
	var nobatch = document.getElementById("nobatch");
	nobatch.style.color="#134D9D";

	function batch(v){
		if(v!=0){
			dom1.style.display="none";
			dom1sibling.style.display="none";
			dom2.style.display="table-row";
			dom3.value = 1;
			isbatch.style.color="#134D9D";
			nobatch.style.color="#333333";
		} else {
			dom1.style.display="table-row";
			dom1sibling.style.display="table-row";
			dom2.style.display="none";
			dom3.value = 0;
			isbatch.style.color="#333333";
			nobatch.style.color="#134D9D";
		}
	}
	function get_nextsibling(n) {
		var x=n.nextSibling;
		while (x.nodeType!=1){
			x=x.nextSibling;
		}
		return x;
	}
</script>
SCRIPT;
	}elseif(submitcheck('bbadwordsadd')){
		if($isbatch==0){
			if(!trim($badwordadd['wsearch'])) {
				cls_message::show('���ϲ���ȫ', '?entry=badwords');
			}
			if(trim($badwordadd['wsearch']) == trim($badwordadd['wreplace'])) {
				cls_message::show('���������滻����ͬ', '?entry=badwords');
			}
			$badwordadd['wsearch'] = trim($badwordadd['wsearch']);
			$badwordadd['wreplace'] = trim($badwordadd['wreplace']);
			isRepeat($badwordadd['wsearch']);
			$db->query("INSERT INTO {$tblprefix}badwords SET
						wsearch='$badwordadd[wsearch]',
						wreplace='$badwordadd[wreplace]'
						");
		} else {
			if(!trim($badwordadd['batch'])) {
				cls_message::show('���ϲ���ȫ', '?entry=badwords');
			}
			$batch = explode(PHP_EOL, $badwordadd['batch']);
			foreach($batch as $v){
				$i = explode('=', $v);
				if(count($i)!=2) continue;
				$badwordadd['wsearch'] = trim($i[0]);
				$badwordadd['wreplace'] = trim($i[1]);
				if(isRepeat($i[0],1)) continue; 
				$db->query("INSERT INTO {$tblprefix}badwords SET
							wsearch='$badwordadd[wsearch]',
							wreplace='$badwordadd[wreplace]'
							");
			}
		}

		adminlog('��Ӳ�����');
		cls_CacheFile::Update('badwords');
		cls_message::show('������������', '?entry=badwords');

	}elseif(submitcheck('bbadwordsedit')){
		if(isset($delete)){
			foreach($delete as $k){
				$db->query("DELETE FROM {$tblprefix}badwords WHERE bwid=$k");
				unset($badwordsnew[$k]);
			}
		}
		if(isset($badwordsnew)){
			foreach($badwordsnew as $bwid => $badwordnew){
				if($badwordnew['wsearch'] && ($badwordnew['wsearch'] != $badwordnew['wreplace'])){
					$db->query("UPDATE {$tblprefix}badwords SET
								wsearch='$badwordnew[wsearch]',
								wreplace='$badwordnew[wreplace]'
								WHERE bwid=$bwid");
				}
			}
		}
		adminlog('�༭�����ʹ����б�');
		cls_CacheFile::Update('badwords');
		cls_message::show('�������޸����', '?entry=badwords');
	}
} elseif($action=='bwexport') {
	@ini_set('url_rewriter.tags','');
	header('Content-Type: text/plain');
	header('Expires: ' . gmdate('D, d M Y H:i:s') . ' GMT');
	header('Content-Disposition: attachment; filename="badwords.txt"');

	$query = $db->query("SELECT * FROM {$tblprefix}badwords");

	while($row = $db->fetch_row($query)){
		echo $row[1].'='.$row[2].PHP_EOL;
	}
	mexit();
} elseif($action=='bwimport') {
	aheader();
	if(!submitcheck('import')){
		tabheader("���벻����",'batchadd',"?entry=$entry&action=$action",2,1);
		trbasic('�ϴ��ļ�','','<input type="file" name="badwords">','',array('guide'=>'��ʽ��������=�滻�� ÿ��һ����¼'));
		tabfooter('import','�ύ');
	} else {
		$badwords = '';
		if($_FILES['badwords']['error']==UPLOAD_ERR_OK && $_FILES['badwords']['size']!=0 && !empty($_FILES['badwords']['tmp_name'])){
			is_file($_FILES['badwords']['tmp_name']) || die("�޷������ļ���{$_FILES['badwords']['tmp_name']}");

			$badwords = addslashes(trim(file_get_contents($_FILES['badwords']['tmp_name'])));
			$badwords = preg_split('/[\s]+/', $badwords);            
			foreach($badwords as $row){
				$i = explode('=', $row);
				if(isRepeat($i[0],1)) continue;             
				$db->query("INSERT INTO {$tblprefix}badwords (wsearch, wreplace) VALUES ('$i[0]', '$i[1]')");
			}
			cls_CacheFile::Update('badwords');
			cls_message::show('�����ʵ������', axaction(6, M_REFERER));
		}
		cls_message::show('�ύ����Ϣ����');
	}
}
function isRepeat($wsearch,$isbatch=0){
	global $db,$tblprefix;
	$isRepeat = $db->result_one("SELECT 1 FROM {$tblprefix}badwords WHERE wsearch='$wsearch'");
    if($isbatch){        
        return (bool) $isRepeat;
    }else{
        $isRepeat && cls_message::show("�������ظ��������� $wsearch �Ѵ���", axaction(6, M_REFERER));    
    } 
}
?>
