<?php
/*
* ����λ����
*
*/
(!defined('M_COM') || !defined('M_ADMIN')) && exit('No Permission');
aheader();
if($re = $curuser->NoBackFunc('pusharea')) cls_message::show($re);
include_once M_ROOT."include/fields.fun.php";

$pushtypes = cls_pushtype::InitialInfoArray();
if(empty($pushtypes)) cls_message::show('����������λ����',"?entry=pushtypes");
$ptid = isset($ptid) ? (int)$ptid : 0;

if(empty($action)){
	backnav('pushareas','pusharea');
	
	if(empty($pushtypes[$ptid])) $ptid = 0;
	$pushareas = cls_pusharea::InitialInfoArray($ptid);
	
	if(!submitcheck('bsubmit')){
		
		$area_arr = array();
		$area_arr[] = empty($ptid) ? "<b>-ȫ������-</b>" : "<a href=\"?entry={$entry}\">-ȫ������-</a>";
		foreach($pushtypes as $v){
			$area_arr[] = $ptid == $v['ptid'] ? "<b>{$v['title']}</b>" : "<a href=\"?entry={$entry}&ptid={$v['ptid']}\">{$v['title']}</a>";
		}
		echo tab_list($area_arr,9,0);
		
		$TitleStr = "����λ���� &nbsp; &nbsp;>><a href=\"?entry=$entry&action=pushareaadd&ptid={$ptid}\" onclick=\"return floatwin('open_pushareaedit',this)\">���</a>";
		tabheader($TitleStr,'pusharea',"?entry=$entry&ptid=$ptid",'7');

		$CategoryArray = array("<input class=\"checkbox\" type=\"checkbox\" name=\"chkall\" onclick=\"checkall(this.form, 'selectid', 'chkall')\">",);
		if(!$ptid) $CategoryArray[] = '��������|L';
		$CategoryArray = array_merge($CategoryArray,array('����λ|L','PAID|L','���ݱ�|L','������Դ|L','����','����','ɾ��','����','����','�ֶ�'));
		trcategory($CategoryArray);
		
		$oldTypeID = 0;
		foreach($pushareas as $k => $v){
			echo "<tr class=\"txt\">\n";
			echo "<td class=\"txtC w30\"><input class=\"checkbox\" type=\"checkbox\" name=\"selectid[$k]\" value=\"$k\"></td>\n";
			if(!$ptid){
				echo "<td class=\"txtL\">".($oldTypeID == $v['ptid'] ? '' : mhtmlspecialchars(@$pushtypes[$v['ptid']]['title']))."</td>\n";
			}
			echo "<td class=\"txtL\"><input type=\"text\" name=\"fmdata[$k][cname]\" value=\"$v[cname]\"></td>\n";
			echo "<td class=\"txtL\">$k</td>\n";
			echo "<td class=\"txtL\">$k</td>\n";
			echo "<td class=\"txtL\">".cls_pusharea::SourceIDTitle($v['sourcetype'],$v['sourceid'])."</td>\n";
			echo "<td class=\"txtC w50\"><input type=\"text\" name=\"fmdata[$k][vieworder]\" value=\"$v[vieworder]\" size=\"4\" maxlength=\"4\"></td>\n";
			echo "<td class=\"txtC w30\">".($v['available'] ? 'Y' : '-')."</td>\n";
			echo "<td class=\"txtC w30\"><a onclick=\"return deltip(this,$no_deepmode)\" href=\"?entry=$entry&action=pushareadel&paid=$k&ptid=$ptid\">ɾ��</a></td>\n";
			echo "<td class=\"txtC w30\"><a href=\"?entry=$entry&action=pushareacopy&paid=$k\" onclick=\"return floatwin('open_pushareaedit',this)\">����</a></td>\n";
			echo "<td class=\"txtC w30\"><a href=\"?entry=$entry&action=pushareadetail&paid=$k\" onclick=\"return floatwin('open_pushareaedit',this)\">����</a></td>\n";
			echo "<td class=\"txtC w30\"><a href=\"?entry=$entry&action=pushareafields&paid=$k\" onclick=\"return floatwin('open_pushareaedit',this)\">�ֶ�</a></td>\n";
			echo "</tr>";
			$oldTypeID = $v['ptid'];
		}
		
		tabfooter();

		tabheader('��������');
		$s_arr = array();
		$s_arr['available'] = '����';
		$s_arr['unavailable'] = '������';
		$s_arr['deleteforce'] = 'ǿ��ɾ��(������λ�������ͼ�¼)';
		if($s_arr){
			$soperatestr = '';$i = 1;
			foreach($s_arr as $k => $v){
				$soperatestr .= "<input class=\"checkbox\" type=\"checkbox\" name=\"arcdeal[$k]\" value=\"1\"".($k=='deleteforce'?' onclick="deltip()"':'').">$v &nbsp;";
				if(!($i % 5)) $soperatestr .= '<br>';
				$i ++;
			}
			trbasic('ѡ�������Ŀ','',$soperatestr,'',array('guide' => '����ǿ��ɾ�������ز�������ɾ��ָ������λ����������Ϣ'));
		}
		// �ѷ��������ͻ�,ȫ������λ�����ڵ�һ�����ࣻ����������$ptid�ж�һ�²���Ĭ��ѡ��,�ɼ���һ�������
		$ptid && trbasic("<input class=\"checkbox\" type=\"checkbox\" name=\"arcdeal[ptid]\" value=\"1\">&nbsp;��������λ����",'arcptid',makeoption(cls_pushtype::ptidsarr(),$ptid),'select');
		tabfooter('bsubmit');
		a_guide('pushareaedit');
	}else{
		if(!empty($selectid)){
			if(!empty($arcdeal['deleteforce'])){
				foreach($selectid as $k){
					cls_pusharea::DeleteOne($k,true);
					unset($fmdata[$k]);
				}
			}else{
				$_ModifyParams = array();
				if(!empty($arcdeal['ptid'])){
					$_ModifyParams['ptid'] = $arcptid;
				}
				if(!empty($arcdeal['available'])){
					$_ModifyParams['available'] = 1;
				}elseif(!empty($arcdeal['unavailable'])){
					$_ModifyParams['available'] = 0;
				}
				if($_ModifyParams){
					foreach($selectid as $k){
						cls_pusharea::ModifyOneConfig($k,$_ModifyParams);
					}
				}
			}
		}
		
		if(!empty($fmdata)){
			foreach($fmdata as $k => $v){
				cls_pusharea::ModifyOneConfig($k,$v);
			}
		}
		adminlog('�༭����λ�б�');
		cls_message::show('����λ�༭���',"?entry=$entry&ptid=$ptid");
	}

}elseif($action == 'pushareaadd'){
	echo _08_HTML::Title("�������λ");
	deep_allow($no_deepmode);
	if(!submitcheck('bsubmit')){
		
		$ptid = isset($ptid) ? (int)$ptid : 0;
		if(empty($pushtypes[$ptid])) $ptid = 0;
		
		tabheader('�������λ',$action,"?entry=$entry&action=$action",2,0,1);
		trbasic('����λ����','fmdata[cname]','','text',array('validate'=>makesubmitstr('fmdata[cname]',1,0,4,32)));
		
		$na = array(
			'validate'=>' offset="1"' . makesubmitstr('fmdata[paid]',1,'tagtype',0,30),
			'guide' => '�涨��ʽ��push_�ַ���ֻ�ܰ���"��ĸ����_"��ϵͳ������Ψһ��ʶ���������������ݱ�',
		);
		trbasic('Ӣ��Ψһ��ʶ','fmdata[paid]','push_***','text',$na);
		$ajaxURL = $cms_abs . _08_Http_Request::uri2MVC('ajax=check_paid&paid=%1');
		echo _08_HTML::AjaxCheckInput('fmdata[paid]', $ajaxURL);

		trbasic('����λ����','fmdata[ptid]',makeoption(cls_pushtype::ptidsarr(),$ptid),'select',array('guide'=>'�ں�̨���������λ���з������'));
		trbasic('����������Դ','fmdata[_pushsource]',makeoption(cls_pusharea::SourceIDArray()),'select',array('guide'=>'�������ú󲻿��޸�'));
		tabfooter();
		
		tabheader('���ͷ�������');
		$OptionArray = array(0 => '�ֶ����',-1 => '��Ŀ(0)') + cls_cotype::coidsarr(1,1);
		$guide = '��������Ϣ�ķ���������ã�ָ�������ѡ����Դ����Ϊ��Ŀ����ϵ���ֶ���ӷ�����������ú󲻿ɸ��ġ�';
		trbasic('���ͷ���1ѡ������','fmdata[classoption1]',makeoption($OptionArray),'select',array('guide'=>$guide));
		trbasic('���ͷ���2ѡ������','fmdata[classoption2]',makeoption($OptionArray),'select',array('guide'=>$guide));
		tabfooter('bsubmit','���');
		
		a_guide('pushareadetail');
	}else{
		
		# ��������Դ��ѡ�����ǰ�ڴ���
		if(!($fmdata['_pushsource'] = trim(strip_tags($fmdata['_pushsource'])))) cls_message::show('������������Դ');
		list($fmdata['sourcetype'],$fmdata['sourceid']) = explode('_',$fmdata['_pushsource']);
		unset($fmdata['_pushsource']);
		
		# ��������λ���������ݱ������ݼ�¼
		if($paid = cls_pusharea::ModifyOneConfig($fmdata['paid'],$fmdata,true)){
			$db->query("ALTER TABLE {$tblprefix}{$fmdata['paid']} COMMENT='{$fmdata['cname']}(�Ƽ�λ)��'");
			adminlog('�������λ-'.$fmdata['cname']);
			cls_message::show('����λ��ӳɹ�����Դ�����λ������ϸ���á�',"?entry=$entry&action=pushareadetail&paid=$paid");
		}else cls_message::show('����λ��Ӳ��ɹ���');
	}

}elseif($action == 'pushareacopy'){
	echo _08_HTML::Title("��������λ");
	deep_allow($no_deepmode);
	if(!($pusharea = cls_pusharea::InitialOneInfo($paid))) cls_message::show('��ָ����ȷ������λ');
	if(!submitcheck('bsubmit')){
		
		tabheader("��������λ - {$pusharea['cname']}",$action,"?entry=$entry&action=$action&paid=$paid",2,0,1);
		trbasic('����λ����','fmdata[cname]',$pusharea['cname'].'(����)','text',array('validate'=>makesubmitstr('fmdata[cname]',1,0,4,32)));
		
		$na = array(
			'validate'=>' offset="1"' . makesubmitstr('fmdata[paid]',1,'tagtype',0,30),
			'guide' => '�涨��ʽ��push_�ַ���ֻ�ܰ���"��ĸ����_"��ϵͳ������Ψһ��ʶ���������������ݱ�',
		);
		trbasic('Ӣ��Ψһ��ʶ','fmdata[paid]',$pusharea['paid'].'_cp','text',$na);
		$ajaxURL = $cms_abs . _08_Http_Request::uri2MVC('ajax=check_paid&paid=%1');
		echo _08_HTML::AjaxCheckInput('fmdata[paid]', $ajaxURL);
		#echo _08_HTML::AjaxCheckInput('fmdata[paid]',"{$cms_abs}tools/ajax.php?action=check_paid&paid=%1");

		trbasic('����λ����','fmdata[ptid]',makeoption(cls_pushtype::ptidsarr(),$pusharea['ptid']),'select',array('guide'=>'�ں�̨���������λ���з������'));
		tabfooter('bsubmit');
		
		a_guide('pushareadetail');
	}else{
		$newConfig = array();
		foreach(array('cname','ptid') as $k){
			$newConfig[$k] = @$fmdata[$k];
		}
		
		try {
			# ��������λ���������ݱ������ݼ�¼
			$nowID = cls_pusharea::CopyOneConfig($paid,@$fmdata['paid'],$newConfig);
		} catch (Exception $e){
			cls_message::show('����λ����ʧ�ܣ�'.$e->getMessage());
		}
		adminlog('��������λ');
		cls_message::show('����λ���Ƴɹ���',axaction(6,"?entry=$entry"));
	}

}elseif($action == 'pushareadetail'){
	if(!($pusharea = cls_pusharea::InitialOneInfo($paid))) cls_message::show('��ָ����ȷ������λ');
	echo _08_HTML::Title("����λ-{$pusharea['cname']}");
	if(!submitcheck('bsubmit')){
		tabheader('����λ����',$action,"?entry=$entry&action=$action&paid=$paid",2,0,1);
		trbasic('����λ����','fmdata[cname]',$pusharea['cname'],'text',array('validate'=>makesubmitstr('fmdata[cname]',1,0,4,32)));
		trbasic('����λ����','fmdata[ptid]',makeoption(cls_pushtype::ptidsarr(),$pusharea['ptid']),'select',array('guide'=>'�ں�̨���������λ���з������'));
		setPermBar('�������Զ��ö�', 'fmdata[autocheck]', @$pusharea['autocheck'], 'chk', $soext=array(0=>'���Զ��ö�', 1=>'ȫ���Զ��ö�','check'=>1), 'ѡ��Ȩ�޷������򷽰��л�Ա�����͵���Ϣ�Զ��ŵ���λ������������������Ϣ�ĺ��档');
        trbasic('�������ֵ','fmdata[maxorderno]',$pusharea['maxorderno'],'text',array('validate'=>makesubmitstr('fmdata[maxorderno]',1,'int',1,2),'w'=>3,'guide' => '������Ϊ������λǰ̨չʾ�б����Ϣ���������Դ󣬴�ֵ����Ϊ��λ�������÷�Χ'));
		tabfooter();
		
		tabheader('�߼����� - ������Դ');
		trbasic('����������Դ','',cls_pusharea::SourceIDTitle($pusharea['sourcetype'],$pusharea['sourceid']),'');
		if($pusharea['sourcetype'] == 'archives'){
			tr_cns('����������Ŀ<br>'.OneCheckBox('fmdata[smallson]','������Ŀ',$pusharea['smallson']),'fmdata[smallids]',array('value'=>$pusharea['smallids'],'chid'=>$pusharea['sourceid'],'framein'=>1,'notip'=>1,'max'=>10));
		}
		if(in_array($pusharea['sourcetype'],array('archives','members','commus'))){
			trbasic('�������ʱ�Զ�����','fmdata[autopush]',@$pusharea['autopush'],'radio',array('guide'=>'Ĭ��Ϊ��ѡ�������������ʱ�Զ����ͷ������������ϡ�'));
			trbasic('��ֹ�ֶ����','fmdata[forbid_useradd]',@$pusharea['forbid_useradd'],'radio',array('guide'=>'Ĭ��Ϊ�񣬿���[�Զ�����]���ʹ�ã�ѡ������û���ֶ������ڡ�'));
		}
		if(in_array($pusharea['sourcetype'],array('archives','members',))){
			trbasic('��Ҫʹ��ģ�ͱ���Ϣ','fmdata[sourceadv]',$pusharea['sourceadv'],'radio',array('guide'=>'Ĭ��Ϊ��ֻ��Ҫ�������л�ȡ��Ϣ��'));
		}
		if(in_array($pusharea['sourcetype'],array('archives','members',))){ 
			trbasic('����������Դ�ֶ�','fmdata[enddate_from]',makeoption(cls_pusharea::DateFieldArray($pusharea['sourcetype'],$pusharea['sourceid']),@$pusharea['enddate_from']),'select',array('guide'=>'Ĭ��Ϊ�գ����ú�[������Ϣ]��[ͬ����Դ]ʱ�����õ��ֶλ�ȡ���ϡ�'));
		}
		$guide = '������Դ��Χ���� {pre}x=4 AND {pre}y>5 �ĸ�ʽ����׷��SQL';
		if(in_array($pusharea['sourcetype'],array('archives','members',))) $guide .= '�������ֶ�ֻ��������';
		$guide .= "<br>��ʹ��ϵͳ��ȫ�ֱ������磺{timestamp} ������� \$timestamp";
		$guide .= "<br>��ͨ�� return ������(); ���ط����ֶ���ʽ��SQL���������Զ��嵽"._08_EXTEND_DIR.DS._08_LIBS_DIR.DS.'functions'.DS.'custom.fun.php';
		
		$createurl = "<br>>><a href=\"?entry=liststr&action={$pusharea['sourcetype']}&pushmode=1&typeid={$pusharea['sourceid']}\" target=\"_blank\">�����ִ�</a>";
		trbasic("���ӹ���SQL{$createurl}",'fmdata[sourcesql]',$pusharea['sourcesql'],'text',array('guide'=>$guide,'w'=>40));
		tabfooter();
		
		tabheader('�߼����� - ������'.viewcheck(array('name' => 'viewdetail_1','value' =>0,'body' =>$actionid.'tbodyfilter_1',)).' &nbsp;��ʾ��ϸ');
		echo "<tbody id=\"{$actionid}tbodyfilter_1\" style=\"display:none\">";
		$fields = cls_PushArea::Field($paid);
		$na = array();foreach(array(1,2) as $k) if(!empty($fields["classid$k"])) $na[$k] = "����$k-{$fields["classid$k"]['cname']}(classid$k)";
		trbasic('�Է��໮������ռ�','fmdata[orderspace]',makeoption(array(0 => '�����ֿռ�') + $na + array(3 => '�������ַ������'),$pusharea['orderspace']),'select',array('guide' => '������Ϣ�Ե�������򽻲���Ϸ�����Ϊ����ռ��������'));
		trbasic('�����������','fmdata[copyspace]',makeoption(array(0 => '�����ù������') + $na,$pusharea['copyspace']),'select',array('guide' => '��������ĳ������Ϣ����������ࡣ'));
		echo "</tbody>";
		tabfooter();
		
		tabheader('�߼����� - ������չ'.viewcheck(array('name' => 'viewdetail_2','value' =>0,'body' =>$actionid.'tbodyfilter_2',)).' &nbsp;��ʾ��ϸ');
		echo "<tbody id=\"{$actionid}tbodyfilter_2\" style=\"display:none\">";
		trbasic("���͹�����չ�ű�",'fmdata[script_admin]',$pusharea['script_admin'],'text',array('guide'=>'������ʹ��ϵͳ���õ�ͨ�ýű�pushs_com.php��λ��admina/extend/','w'=>20));
		trbasic("����������չ�ű�",'fmdata[script_detail]',$pusharea['script_detail'],'text',array('guide'=>'������ʹ��ϵͳ���õ�ͨ�ýű�push_com.php��λ��admina/extend/','w'=>20));
		trbasic("���ͼ�����չ�ű�",'fmdata[script_load]',$pusharea['script_load'],'text',array('guide'=>"������ʹ��ϵͳ���õ�ͨ�ýű�push_load_{$pusharea['sourcetype']}.php��λ��admina/extend/",'w'=>20));
		echo "</tbody>";
		tabfooter('bsubmit');
		a_guide('pushareadetail');
	}else{
		cls_pusharea::ModifyOneConfig($paid,$fmdata);
		adminlog('�༭����λ-'.$pusharea['cname']);
		cls_message::show('����λ�༭���!',axaction(6,"?entry=$entry"));
	}
}elseif($action == 'pushareadel'){
	backnav('pushareas','pusharea');
	deep_allow($no_deepmode);
	if(!submitcheck('confirm')){
		$message = "ɾ�����ָܻ���ȷ��ɾ����ѡ��Ŀ?<br><br>";
		$message .= "ȷ������>><a href='?entry=$entry&action=$action&paid=$paid&confirm=ok&ptid=$ptid'>ɾ��</a><br>";
		$message .= "��������>><a href='?entry=$entry'>����</a>";
		cls_message::show($message);
	}
	if($re = cls_pusharea::DeleteOne($paid)) cls_message::show($re);
	adminlog('ɾ������λ');
	cls_message::show('����λɾ�����',"?entry=$entry&ptid=$ptid");
}elseif($action == 'pushareafields' && $paid){
	if(!($pusharea = cls_pusharea::InitialOneInfo($paid))) cls_message::show('��ָ����ȷ������λ');
	$fields = cls_fieldconfig::InitialFieldArray('pusharea',$paid);
	if(!submitcheck('bsubmit') && !submitcheck('brules')){
		tabheader($pusharea['cname']."-�ֶι��� &nbsp; &nbsp;>><a href=\"?entry=$entry&action=fieldone&paid=$paid\" onclick=\"return floatwin('open_fielddetail',this)\">����ֶ�</a>",'pushareadetail',"?entry=$entry&action=$action&paid=$paid");
		trcategory(array('��Ч','�ֶ�����|L','����','�ֶα�ʶ|L','���ݱ�|L','�ֶ�����','ɾ��','�༭'));
		foreach($fields as $k => $v){
		echo "<tr class=\"txt\">\n".
			"<td class=\"txtC w40\"><input class=\"checkbox\" type=\"checkbox\" name=\"fieldsnew[$k][available]\" value=\"1\"".($v['available'] ? ' checked' : '').(!empty($v['issystem']) ? ' disabled' : '')."></td>\n".
			"<td class=\"txtL\"><input type=\"text\" size=\"25\" name=\"fieldsnew[$k][cname]\" value=\"".mhtmlspecialchars($v['cname'])."\"></td>\n".
			"<td class=\"txtC w60\"><input type=\"text\" size=\"4\" name=\"fieldsnew[$k][vieworder]\" value=\"$v[vieworder]\"></td>\n".
			"<td class=\"txtL\">".mhtmlspecialchars($k)."</td>\n".
			"<td class=\"txtL\">".$v['tbl']."</td>\n".
			"<td class=\"txtC w100\">".cls_fieldconfig::datatype($v['datatype'])."</td>\n".
			"<td class=\"txtC w40\"><input class=\"checkbox\" type=\"checkbox\"".(empty($v['iscustom']) ? ' disabled' : " name=\"delete[$k]\" value=\"$k\" onclick=\"deltip(this,$no_deepmode)\"")."></td>\n".
			"<td class=\"txtC w50\"><a href=\"?entry=$entry&action=fieldone&paid=$paid&fieldname=$k\" onclick=\"return floatwin('open_fielddetail',this)\">����</a></td>\n".
			"</tr>";
		}
		tabfooter('bsubmit');
		
		tabheader('�����ֶι���','pusharearules',"?entry=$entry&action=$action&paid=$paid");
		$sfields = array('' => '����������Դ�ֶ�') + cls_pusharea::SourceFieldArray($pusharea['sourcetype'],$pusharea['sourceid']);
		$sfs = $pusharea['sourcefields'];
		$guide = "{�ֶ�}�������ݣ��ɶ���ֶ���ϣ���return ������('{�ֶ�1}','{�ֶ�2}');ͨ���������ؽ��<br>�������Զ��嵽"._08_EXTEND_DIR.DS._08_LIBS_DIR.DS.'functions'.DS.'custom.fun.php';
		foreach($fields as $k => $v){
			if(empty($v['available'])) continue;
			$str = "<br><input class=\"checkbox\" type=\"checkbox\" name=\"fmfields[$k][refresh]\" value=\"1\"".(empty($sfs[$k]['refresh']) ? '' : ' checked')."> ��Ҫ��������";
			if(in_array($pusharea['sourcetype'],array('archives')) && ($k == 'url')){
				$str .= " &nbsp;<input class=\"checkbox\" type=\"checkbox\" name=\"fmfields[$k][nodemode]\" value=\"1\"".(empty($sfs[$k]['nodemode']) ? '' : ' checked')."> �����ֻ���";
			}
			trbasic("[{$v['cname']}] ����Դֵ","fmfields[$k][from]",@$sfs[$k]['from'],'text',array('addstr'=>$str,'guide' => $guide,'ops' => $sfields,'w' => 40));
		}
		tabfooter('brules');
	}elseif(submitcheck('bsubmit')){
		if(!empty($delete) && deep_allow($no_deepmode)){
			$deleteds = cls_fieldconfig::DeleteField('pusharea',$paid,$delete);
			foreach($deleteds as $k){
				unset($fieldsnew[$k]);
			}
		}
		if(!empty($fieldsnew)){
			foreach($fieldsnew as $k => $v){
				$v['cname'] = trim(strip_tags($v['cname']));
				$v['cname'] = !$v['cname'] ? $fields[$k]['cname'] : $v['cname'];
				$v['available'] = empty($v['available']) && !$fields[$k]['issystem'] ? 0 : 1;
				$v['vieworder'] = max(0,intval($v['vieworder']));
				cls_fieldconfig::ModifyOneConfig('pusharea',$paid,$v,$k);
			}
		}
		cls_fieldconfig::UpdateCache('pusharea',$paid);
		
		adminlog('�༭����λ'.$pusharea['cname'].'�ֶ��б�');
		cls_message::show('����λ�ֶα༭��ɡ�',"?entry=$entry&action=$action&paid=$paid");
	}elseif(submitcheck('brules')){
		cls_pusharea::ModifyOneConfig($paid,array('sourcefields' => $fmfields));
		adminlog('�༭����λ-'.$pusharea['cname']);
		cls_message::show('����λ�༭���!',M_REFERER);
	}
}elseif($action == 'fieldone'){
	cls_FieldConfig::EditOne('pusharea',@$paid,@$fieldname);
}elseif($action == 'repair'){
	backnav('pushareas','repair');
	$pushareas = cls_pusharea::InitialInfoArray(0);
	if(!submitcheck('bsubmit')){
		$TitleStr = "��Ҫ�޸������ͱ�";
		tabheader($TitleStr,'pusharea',"?entry=$entry&action=$action",'7');

		$CategoryArray = array("<input class=\"checkbox\" type=\"checkbox\" name=\"chkall\" onclick=\"checkall(this.form, 'selectid', 'chkall')\">",);
		$CategoryArray = array_merge($CategoryArray,array('����λ|L','���ݱ�|L','״��˵��|L'));
		trcategory($CategoryArray);
		
		foreach($pushareas as $k => $v){
			if($CheckError = cls_PushArea::CheckTable($k)){
				$View['select'] = "<input class=\"checkbox\" type=\"checkbox\" name=\"selectid[$k]\" value=\"$k\">";
				$View['cname'] = mhtmlspecialchars(@$pushtypes[$v['ptid']]['title']).'|<b>'.mhtmlspecialchars($v['cname']).'</b>';
				$View['contenttable'] = $k;
				$View['state'] = $CheckError ? $CheckError : 'ok';
				
				echo "<tr class=\"txt\">\n";
				echo "<td class=\"txtC w30\">{$View['select']}</td>\n";
				echo "<td class=\"txtL w200\">{$View['cname']}</td>\n";
				echo "<td class=\"txtL w60\">{$View['contenttable']}</td>\n";
				echo "<td class=\"txtL\">{$View['state']}</td>\n";
				echo "</tr>";
			
			
			}
		}
		
		tabfooter('bsubmit','�޸�');
	}else{
		if(!empty($selectid)){
			foreach($selectid as $k){
				cls_PushArea::RepairTable($k);
			}
		}
		adminlog('�޸�����λ���ݱ�');
		cls_message::show('����λ���ݱ��޸����!',M_REFERER);
	
	}
}