<?
(!defined('M_COM') || !defined('M_ADMIN')) && exit('No Permission');
aheader();
if($re = $curuser->NoBackFunc('project')) cls_message::show($re);
$localfiles = cls_cache::Read('localfiles');
$ftypearr = array(
				'image' => 'ͼƬ',
				'flash' => 'Flash',
				'media' => '��Ƶ',
				'file' => '����',
				);
backnav('project','localfile');
if($action == 'localfilesedit'){
	tabheader('�����ϴ�����','','','5');
	trcategory(array('���','��������','ȫ����������','�������ϴ�������','����'));
	$no = 0;
    foreach($ftypearr as $k => $tpname){	
        $extnames = $localnames = '';
        if(isset($localfiles[$k])){ 
            $localfile = $localfiles[$k];
    		foreach($localfile as $ext => $v){
    			$extnames .= $ext.'&nbsp;&nbsp;';
    			!empty($v['islocal']) && $localnames .= $ext.'&nbsp;&nbsp;';
    		} 
        }
		$no ++;
		echo "<tr class=\"txt\">".
			"<td class=\"txtC w30\">$no</td>\n".
			"<td class=\"txtC w60\">$tpname</td>\n".
			"<td class=\"txtL\">$extnames</td>\n".
			"<td class=\"txtL\">$localnames</td>\n".
			"<td class=\"txtC w40\"><a href=\"?entry=localfiles&action=localfiledetail&ftype=$k\">����</a></td></tr>\n";
	}
	tabfooter();
	a_guide('localfilesedit');
}elseif($action == 'localfiledetail' && $ftype){
	$localfile = empty($localfiles[$ftype]) ? array() : $localfiles[$ftype];
    if(!submitcheck('bfilesedit') && !submitcheck('bfilesadd')){
		tabheader('�����ϴ��ļ�����'.'&nbsp;-&nbsp; '.$ftypearr[$ftype],'filesedit',"?entry=localfiles&action=localfiledetail&ftype=$ftype",'6');
		trcategory(array('<input class="checkbox" type="checkbox" name="chkall" onclick="deltip(this,0,checkall,this.form,\'delete\')">ɾ?','�ļ���չ��','��������','�������ϴ�','����ϴ�����(K)','��С�ϴ�����(K)'));
        foreach($localfile as $k => $rmfile){
			echo "<tr class=\"txt\">".
				"<td class=\"txtC w40\"><input class=\"checkbox\" type=\"checkbox\" name=\"delete[$k]\" value=\"$k\" onclick=\"deltip()\">\n".
				"<td class=\"txtC w80\">$k</td>\n".
				"<td class=\"txtC\">$ftypearr[$ftype]</td>\n".
				"<td class=\"txtC\"><input class=\"checkbox\" type=\"checkbox\" name=\"rmfilesnew[$k][islocal]\" value=\"1\"".(empty($rmfile['islocal']) ? "" : " checked").">\n".
				"<td class=\"txtC\"><input type=\"text\" size=\"10\" name=\"rmfilesnew[$k][maxsize]\" value=\"$rmfile[maxsize]\"></td>\n".
				"<td class=\"txtC\"><input type=\"text\" size=\"10\" name=\"rmfilesnew[$k][minisize]\" value=\"$rmfile[minisize]\"></td></tr>\n";
		}
		tabfooter('bfilesedit');

		tabheader('����ļ�����','filesadd',"?entry=localfiles&action=localfiledetail&ftype=$ftype");
		trbasic('�ļ�����(������չ��,���ŷָ��������)','extnamestr');
		tabfooter('bfilesadd','���');
		a_guide('localfiledetail');

	}elseif(submitcheck('bfilesadd')){
		$extnames = array_unique(array_filter(explode(',',strtolower($extnamestr))));
		if($extnames){
			foreach($extnames as $extname){
				if(preg_match("/[^a-zA-Z0-9]+/",$extname) || in_array($extname,array_keys($localfile))) continue;
				$db->query("INSERT INTO {$tblprefix}localfiles SET lfid=".auto_insert_id('localfiles').",ftype='$ftype',extname='$extname'");
			}
			cls_CacheFile::Update('localfiles');
		}
		adminlog('�༭�����ϴ�����','����ļ�����');
		cls_message::show('�ļ�����������',"?entry=localfiles&action=localfiledetail&ftype=$ftype");
	}elseif(submitcheck('bfilesedit')){
		if(!empty($delete)){
			foreach($delete as $id) {
				$db->query("DELETE FROM {$tblprefix}localfiles WHERE extname='$id'");
				unset($rmfilesnew[$id]);
			}
		}
		if(!empty($rmfilesnew)){
			foreach($rmfilesnew as $id => $rmfilenew) {
				$rmfilenew['islocal'] = empty($rmfilenew['islocal']) ? 0 : $rmfilenew['islocal'];
				$rmfilenew['maxsize'] = max(0,intval($rmfilenew['maxsize']));
				$rmfilenew['minisize'] = max(0,intval($rmfilenew['minisize']));
				$db->query("UPDATE {$tblprefix}localfiles SET
						islocal='$rmfilenew[islocal]',
						maxsize='$rmfilenew[maxsize]',
						minisize='$rmfilenew[minisize]'
						WHERE extname='$id'");
			}
		}
		cls_CacheFile::Update('localfiles');
		adminlog('�༭�����ϴ�����','�޸��ļ�����');
		cls_message::show('�ļ����ͱ༭���',"?entry=localfiles&action=localfiledetail&ftype=$ftype");
	}
}
?>