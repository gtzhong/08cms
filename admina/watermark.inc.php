<?
(!defined('M_COM') || !defined('M_ADMIN')) && exit('No Permission');
aheader();
if($re = $curuser->NoBackFunc('project')) cls_message::show($re);
$channels = cls_cache::Read('channels');
if($action == 'watermarkedit'){
	backnav('project','watermark');

	if(!submitcheck('watermarkadd') && !submitcheck('watermarkedit')){
		$watermarks = array();
		$query = $db->query("SELECT * FROM {$tblprefix}watermarks");
		tabheader('ˮӡ��������','watermarkedit','?entry=watermark&action=watermarkedit','5');
		trcategory(array('ID','��������|L','�Ƿ����','��������','ˮӡ����|L','ɾ��','�༭'));
		while($watermark = $db->fetch_array($query)){
			$watermark['issystemstr'] = empty($watermark['issystem']) ? '�Զ�' : 'ϵͳ';
			$k = $watermark['wmid'];
			switch($watermark['watermarktype']){
				case '0':
					$watermark['watermarktype']='GIFͼƬˮӡ';
					break;
				case '1':
					$watermark['watermarktype']='PNGͼƬˮӡ';
					break;
				case '2':
					$watermark['watermarktype']='����ˮӡ';
					break;
			}
			echo "<tr class=\"txt\">".
				"<td class=\"txtC w40\">$k</td>\n".
				"<td class=\"txtL\"><input type=\"text\" size=\"30\" name=\"watermarksnew[$k][cname]\" value=\"$watermark[cname]\"".(!empty($watermark['issystem']) ? " unselectable=\"on\"" : "")."></td>\n".
				"<td class=\"txtC\"><input type=\"checkbox\" class=\"checkbox\" name=\"watermarksnewable[$k]\" value=\"1\" ".($watermark['Available'] ? "checked=\"checked\"" : "")."></input></td>\n".
				"<td class=\"txtC w80\">$watermark[issystemstr]</td>\n".
				"<td class=\"txtL\">$watermark[watermarktype]</td>\n".
				"<td class=\"txtC w40\"><input class=\"checkbox\" type=\"checkbox\"".(!empty($watermark['issystem']) ? ' disabled' : " name=\"delete[$k]\" value=\"$k\" onclick=\"deltip(this,$no_deepmode)\"")."></td>\n".
				"<td class=\"txtC w40\"><a onclick=\"return floatwin('open_channeledit',this)\" href=\"?entry=watermark&action=watermarkdetail&wmid=$k\">".'����'."</a></td></tr>\n";
		}

		tabfooter('watermarkedit','�޸�');

		tabheader('���ˮӡ����','watermarkadd','?entry=watermark&action=watermarkedit');
		$watermarktypearr = array('0' => 'GIFͼƬˮӡ','1' => 'PNGͼƬˮӡ','2'=>'����ˮӡ');
		trbasic('ˮӡ����','addwatermark[watermarktype]',makeoption($watermarktypearr,$watermark['watermarktype']),'select');
		trbasic('��������','addwatermark[cname]');
		tabfooter('watermarkadd','���');
		a_guide('watermarkedit');
	}elseif(submitcheck('watermarkedit')) {
		if(!empty($delete) && deep_allow($no_deepmode)){
			foreach($delete as $k){
				$db->query("DELETE FROM {$tblprefix}watermarks WHERE wmid=$k");
				unset($watermarksnew[$k]);
			}
		}
		foreach($watermarksnew as $k => $watermarknew){
			if(empty($watermarks[$k]['issystem'])){
				$watermarknew['cname'] = empty($watermarknew['cname']) ? $watermarks[$k]['cname'] : $watermarknew['cname'];
				$db->query("UPDATE {$tblprefix}watermarks SET cname='$watermarknew[cname]' WHERE wmid=$k");
			}
		}
		if(!empty($watermarksnewable)){
			$query=$db->query("SELECT wmid FROM {$tblprefix}watermarks");
			while($row=$db->fetch_array($query)){
				if(!empty($watermarksnewable[$row['wmid']])){
					$db->query("UPDATE {$tblprefix}watermarks set Available='1' WHERE wmid='$row[wmid]'");
				}else{
					$db->query("UPDATE {$tblprefix}watermarks set Available='0' WHERE wmid='$row[wmid]'");
				}
			}
		}else{
			$db->query("UPDATE {$tblprefix}watermarks set available='0'");
		}

		cls_CacheFile::Update('watermarks');
		adminlog('�༭ˮӡ����','�༭�����б�');
		cls_message::show('�����޸����', '?entry=watermark&action=watermarkedit');
	}
	elseif(submitcheck('watermarkadd')) {
		if(!$addwatermark['cname']) {
			cls_message::show('��������missiong', '?entry=watermark&action=watermarkedit');
		}
		$db->query("INSERT INTO {$tblprefix}watermarks SET wmid=".auto_insert_id('watermarks').",cname='$addwatermark[cname]',watermarktype='$addwatermark[watermarktype]'");
		cls_CacheFile::Update('watermarks');
		adminlog('���ˮӡ����','�༭�����б�');
		cls_message::show('����������', '?entry=watermark&action=watermarkedit');
	}
}
if($action =='watermarkdetail' && $wmid){
	$setwatermark = $db->fetch_one("SELECT * FROM {$tblprefix}watermarks WHERE wmid='".$wmid."'");
	if(empty($setwatermark)) cls_message::show('��������',axaction(2,'?entry=watermark&action=watermarkedit'));

	if(!submitcheck('setmarkedit')){
		$waterfontpath = M_ROOT.'images/common/';
		$opendir=@opendir($waterfontpath);
		$fontfile = array('0'=>'��ѡ��');
		while($entry=readdir($opendir)){
			if($entry != '.' && $entry != '..' && preg_match('/\s*\.[ttf|TTF]/',$entry))	$fontfile[$entry]=$entry;
		}
		tabheader('����ˮӡ'.'&nbsp; - &nbsp;'.$setwatermark['cname'],'setmarkedit',"?entry=watermark&action=watermarkdetail&wmid=$wmid",6);
		$arr = array(1 => '����',2 => '����',3 => '����',4 => '����',5 => '����',6 => '����',7 => '����',8 => '����',9 => '����',);
		$starr = empty($setwatermark['watermarkstatus']) ? array() : explode(',',$setwatermark['watermarkstatus']);
		trbasic('���ˮӡλ��','',makecheckbox('setwatermarknew[watermarkstatus][]',$arr,$starr,3),'',array('guide' => '�����ѡ��3��λ�ã���ѡ��ǰˮӡ��������Ч��'));
		trbasic('�������¿�ȵ�ͼƬ��ˮӡ','setwatermarknew[watermarkminwidth]',$setwatermark['watermarkminwidth'],'text',array('guide'=>'��λ��px�������벻С��100������'));
		trbasic('�������¸߶ȵ�ͼƬ��ˮӡ','setwatermarknew[watermarkminheight]',$setwatermark['watermarkminheight'],'text',array('guide'=>'��λ��px�������벻С��100������'));
		trbasic('ˮӡͼƬ��ˮƽ�߾�','setwatermarknew[watermarkoffsetx]',$setwatermark['watermarkoffsetx'],'text',array('guide'=>'��λ��px��������5��100������'));
		trbasic('ˮӡͼƬ�Ĵ�ֱ�߾�','setwatermarknew[watermarkoffsety]',$setwatermark['watermarkoffsety'],'text',array('guide'=>'��λ��px��������5��100������'));
		if($setwatermark['watermarktype']!='2'){
			trbasic('ͼƬˮӡ�ں϶�','setwatermarknew[watermarktrans]',$setwatermark['watermarktrans'],'text',array('guide'=>'���� GIF ����ˮӡͼƬ��ԭʼͼƬ���ں϶ȣ���ΧΪ 1��100 ����������ֵԽ��ˮӡͼƬ͸����Խ�͡�PNG ����ˮӡ����������͸��Ч������������á���������Ҫ����ˮӡ���ܺ����Ч'));
			trbasic('JPEGͼƬˮӡ������','setwatermarknew[watermarkquality]',$setwatermark['watermarkquality'],'text',array('guide'=>'���� JPEG ���͵�ͼƬ�������ˮӡ���������������ΧΪ 0��100 ����������ֵԽ����ͼƬЧ��Խ�ã����ߴ�ҲԽ�󡣱�������Ҫ����ˮӡ���ܺ����Ч'));
		}else{
			trbasic('�ı�ˮӡ����','setwatermarknew[watermarktext]',$setwatermark['watermarktext']);
			trbasic('�ı�ˮӡ����','setwatermarknew[waterfontfile]',makeoption($fontfile,$setwatermark['waterfontfile']),'select');
			trbasic('�ı�ˮӡ�����С','setwatermarknew[watermarkfontsize]',$setwatermark['watermarkfontsize'],'text',array('guide'=>'������ͼƬ������Ĵ�С��'));
			trbasic('�ı�ˮӡ��ʾ�Ƕ�','setwatermarknew[watermarkangle]',$setwatermark['watermarkangle'],'text',array('guide'=>'������ͼƬ�趨λ����ʾ�ĽǶȡ�'));
			trbasic('�ı�ˮӡ������ɫ','setwatermarknew[watermarkcolor]','<div style="position:relative;"><input type="text" value="'.$setwatermark['watermarkcolor'].'" name="setwatermarknew[watermarkcolor]" id="setwatermarknew[watermarkcolor]" size="25">&nbsp;&nbsp;<input type="button" id="colorbtn" style="width:40px; height:21px;"><div id="colordiv" style="position: absolute; z-index: 301; left: 380px; top: 180px; display: none;"><iframe id="c_frame" name="c_frame" scrolling="no" height="186" width="166"></iframe></div></div>','',array('guide'=>'���� 16 ������ɫ�����ı�ˮӡ������ɫ'));
echo <<<END
<!--?>-->
<script>
var colortxt = document.getElementById('setwatermarknew[watermarkcolor]');
var colorbtn = document.getElementById('colorbtn');
colorbtn.style.background = colortxt.value;
var colordiv = document.getElementById('colordiv');
var cf = document.getElementById('c_frame');
cf.onmouseout = function(){
	colordiv.style.display = 'none';
}
colorbtn.onclick = function(){
	colordiv.style.display ='' ;
	colordiv.style.left = 177 + 'px';
	colordiv.style.top = -185 + 'px';
	c_frame.location = './images/common/getcolor.htm?setwatermarknew[watermarkcolor]';
}
</script>
END;
#<?
		}
		tabfooter('setmarkedit','�޸�');
		a_guide('watermarkdetail');
	}else{
		$setwatermarknew['watermarkstatus'] = empty($setwatermarknew['watermarkstatus']) ? '' : implode(',',$setwatermarknew['watermarkstatus']);
		$setwatermarknew['watermarkminwidth']=max(100,$setwatermarknew['watermarkminwidth']);
		$setwatermarknew['watermarkminheight']=max(100,$setwatermarknew['watermarkminheight']);
		$setwatermarknew['watermarkoffsetx']=max(5,min(100,intval($setwatermarknew['watermarkoffsetx'])));
		$setwatermarknew['watermarkoffsety']=max(5,min(100,intval($setwatermarknew['watermarkoffsety'])));


		$updatestr = '';
		$updatestr.="watermarkminwidth='$setwatermarknew[watermarkminwidth]',";
		$updatestr.="watermarkminheight='$setwatermarknew[watermarkminheight]',";
		$updatestr.="watermarkoffsetx='$setwatermarknew[watermarkoffsetx]',";
		$updatestr.="watermarkoffsety='$setwatermarknew[watermarkoffsety]',";
		if($setwatermark['watermarktype']=='2'){
			empty($setwatermarknew['watermarkangle']) && $setwatermarknew['watermarkangle']=min(100,$setwatermarknew['watermarkangle']);
			$updatestr.="watermarkangle='$setwatermarknew[watermarkangle]',";
		}else{
			$setwatermarknew['watermarktrans']=min(100,$setwatermarknew['watermarktrans']);
			$updatestr.="watermarktrans='$setwatermarknew[watermarktrans]',";
			$setwatermarknew['watermarkquality']=min(100,$setwatermarknew['watermarkquality']);
			$updatestr.="watermarkquality='$setwatermarknew[watermarkquality]',";
		}
		!empty($setwatermarknew['watermarktext']) &&  $updatestr.="watermarktext='$setwatermarknew[watermarktext]',";
		$updatestr.= (!empty($setwatermarknew['waterfontfile']) ? "waterfontfile='$setwatermarknew[waterfontfile]'," : 'waterfontfile=0,');
		!empty($setwatermarknew['watermarkfontsize']) && $updatestr.="watermarkfontsize='$setwatermarknew[watermarkfontsize]',";

		!empty($setwatermarknew['watermarkcolor']) &&  $updatestr.="watermarkcolor='$setwatermarknew[watermarkcolor]',";
		$updatestr.="watermarkstatus='$setwatermarknew[watermarkstatus]'";
		$db->query("UPDATE {$tblprefix}watermarks SET $updatestr WHERE wmid='$wmid'");
		cls_CacheFile::Update('watermarks');
		adminlog('�޸�ˮӡ����','�޸�ˮӡ����');
		cls_message::show('�޸�ˮӡ�ɹ�','?entry=watermark&action=watermarkdetail&wmid='.$wmid);
	}
}
?>
