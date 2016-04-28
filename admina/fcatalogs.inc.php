<?php
(!defined('M_COM') || !defined('M_ADMIN')) && exit('No Permission');
aheader();
if($re = $curuser->NoBackFunc('freeinfo')) cls_message::show($re);
foreach(array('currencys','grouptypes','mtpls','permissions','cotypes',) as $k) $$k = cls_cache::Read($k);
$fchidsarr = cls_fchannel::fchidsarr();
$fcatalogs = cls_fcatalog::InitialInfoArray();
$pfcaid = cls_fcatalog::InitID(@$pfcaid);//ָ�����������ڹ���
$pfcaid_suffix = $pfcaid ? "&pfcaid=$pfcaid" : '';
if($action == 'fcatalogsedit'){
	backnav('fchannel','coclass');
	empty($fchidsarr) && cls_message::show('�붨�帽����Ϣģ��');
	if(!submitcheck('bfcatalogsedit')){
		#��������ƻ�
		cls_Currency::clearCurrency();
		# ��������ർ��
	    $pfcatalogs = cls_fcatalog::InitialInfoArray('');
		if(empty($pfcatalogs[$pfcaid])) $pfcaid = '';
        $pfacatalogsarr = array();
        $pfcatalogsarr[] = !$pfcaid ? "<b>-��������-</b>" : "<a href=\"?entry={$entry}&action={$action}\">-��������-</a>";
        foreach($pfcatalogs as $v){
            $pfcatalogsarr[] = $pfcaid == $v['fcaid'] ? "<b>{$v['title']}</b>" : "<a href=\"?entry={$entry}&action={$action}&pfcaid={$v['fcaid']}\">{$v['title']}</a>";
        }
        echo tab_list($pfcatalogsarr,9,0);
		
		$TitleStr = "����������� &nbsp; &nbsp;>><a href=\"?entry=$entry&action=fcatalogadd$pfcaid_suffix\" onclick=\"return floatwin('open_fcatalogdetail',this)\">��ӷ���</a>";
		tabheader($TitleStr,'fcatalogsedit',"?entry=$entry&action=$action$pfcaid_suffix",'7');
		$CategoryArray = array('���',"<input class=\"checkbox\" type=\"checkbox\" name=\"chkall\" onclick=\"checkall(this.form, 'selectid', 'chkall')\">",'Ψһ��ʶ|L','��������|L','����˵��|L','����','���','ģ��','ɾ��',);
		if(!$pfcaid) $CategoryArray[] = '����';
		$CategoryArray[] = '����';
		$CategoryArray[] = '����';
		trcategory($CategoryArray);
		
        $nfcatalogs = cls_fcatalog::InitialInfoArray($pfcaid);
		$No = 0;
		foreach($nfcatalogs as $k => $v){
			echo "<tr class=\"txt\">\n".
				"<td class=\"txtC w30\">".++$No."</td>\n".
				"<td class=\"txtC w30\"><input class=\"checkbox\" type=\"checkbox\" name=\"selectid[$k]\" value=\"$k\"></td>\n".
				"<td class=\"txtL\">$k</td>\n".
				"<td class=\"txtL\"><input type=\"text\" name=\"fmdata[$k][title]\" value=\"".mhtmlspecialchars($v['title'])."\" size=\"20\" maxlength=\"30\"></td>\n".
				"<td class=\"txtL\"><input type=\"text\" name=\"fmdata[$k][content]\" value=\"".mhtmlspecialchars($v['content'])."\" size=\"40\"></td>\n".
				"<td class=\"txtC w50\"><input type=\"text\" name=\"fmdata[$k][vieworder]\" value=\"$v[vieworder]\" size=\"2\"></td>\n".
				"<td class=\"txtC w40\">".(empty($v['ftype']) ? '-' : 'Y')."</td>\n".
				"<td class=\"txtC w100\">".mhtmlspecialchars(cls_fchannel::Config($v['chid'],'cname'))."</td>\n".
				"<td class=\"txtC w30\"><a onclick=\"return deltip(this,$no_deepmode)\" href=\"?entry=$entry&action=fcatalogdel&fcaid=$k$pfcaid_suffix\">ɾ��</a></td>\n";
				if(!$pfcaid) echo "<td class=\"txtC w30\"><a href=\"?entry=$entry&action=fcatalogsedit&pfcaid=$k\">����</a></td>\n";
			echo "<td class=\"txtC w30\"><a href=\"?entry=$entry&action=fcatalogdetail&fcaid=$k$pfcaid_suffix\" onclick=\"return floatwin('open_fcatalogdetail',this)\">����</a></td>\n";
			echo "<td class=\"txtC w30\"><a href=\"?entry=$entry&action=fcatalogcopy&fcaid=$k$pfcaid_suffix\" onclick=\"return floatwin('open_fcatalogdetail',this)\">����</a></td>\n";
			echo "</tr>";
		}
		tabfooter();

		tabheader('��������');
		$s_arr = array();
		$s_arr['deleteforce'] = 'ǿ��ɾ��(�����༰�������ĸ���)';
		if($s_arr){
			$soperatestr = '';$i = 1;
			foreach($s_arr as $k => $v){
				$soperatestr .= "<input class=\"checkbox\" type=\"checkbox\" name=\"arcdeal[$k]\" value=\"1\"".($k=='deleteforce'?' onclick="deltip()"':'').">$v &nbsp;";
				if(!($i % 5)) $soperatestr .= '<br>';
				$i ++;
			}
			trbasic('ѡ�������Ŀ','',$soperatestr,'',array('guide' => '�����ز�������ɾ����ѡ���ࡢ�����ӷ��ࡢ�������з����������ĸ�����Ϣ'));
		}
		trbasic("<input class=\"checkbox\" type=\"checkbox\" name=\"arcdeal[ftype]\" value=\"1\">&nbsp;��������",'',makeradio('arcftype',array('Ĭ������', '�������'),0),'',array('guide' => '���÷���Ϊ�������'));
		tabfooter('bfcatalogsedit');
		a_guide('fcatalogsedit');
	}else{
		if(!empty($selectid)){
			if(!empty($arcdeal['deleteforce'])){
				foreach($selectid as $k){
					cls_fcatalog::DeleteOne($k,1);
					unset($fmdata[$k]);
				}
			}elseif(!empty($arcdeal['ftype'])){
				cls_fcatalog::SetFtype(empty($arcftype) ? 0 : 1,$selectid);
			}
		}

		if(!empty($fmdata)){
			foreach($fmdata as $k => $v){
				$v['title'] = $v['title'] ? $v['title'] : $fcatalogs[$k]['title'];
				$v['content'] = trim($v['content']);
				$v['vieworder'] = max(0,intval($v['vieworder']));
				
				cls_fcatalog::ModifyOneConfig($k,$v);
			}
		}

		adminlog('�༭������������б�');
		cls_message::show('����༭���', "?entry=$entry&action=$action$pfcaid_suffix");
	}
}elseif($action =='fcatalogadd'){
	echo _08_HTML::Title('��Ӹ�������');
	if(!submitcheck('bsubmit')){
		tabheader('��Ӹ�������','fcatalogadd',"?entry=$entry&action=$action$pfcaid_suffix",2,0,1);
		trbasic('��������','fmdata[title]','','text',array('validate'=>makesubmitstr('fmdata[title]',1,0,4,30)));
		
		$na = array(
			'validate'=>' offset="1"' . makesubmitstr('fmdata[fcaid]',1,'tagtype',3,30),
			'guide' => '�涨��ʽ��ͷ�ַ�Ϊ��ĸ�������ַ�ֻ��Ϊ"��ĸ�����֡�_"��',
		);
		trbasic('Ӣ��Ψһ��ʶ','fmdata[fcaid]','','text',$na);
		$ajaxURL = $cms_abs . _08_Http_Request::uri2MVC('ajax=check_fcaid&fcaid=%1');
		echo _08_HTML::AjaxCheckInput('fmdata[fcaid]', $ajaxURL);
		
		trbasic('����ģ��','fmdata[chid]',makeoption($fchidsarr),'select');
		
        $arr = array(0 => '��������');
        foreach($fcatalogs as $k => $v) $v['pid'] || $arr[$k] = $v['title'];
		trbasic('��������','fmdata[pid]',makeoption($arr,$pfcaid),'select');
		
		cls_fcatalog::fAreaCoType(); //�������� 
		
		tabfooter('bsubmit');
	}else{
		$fcaid = cls_fcatalog::ModifyOneConfig($fmdata['fcaid'],$fmdata,true);
		if($fcaid){
			adminlog('��Ӹ�������');
			cls_message::show('����������ӳɹ�����Դ˷��������ϸ���á�', "?entry=$entry&action=fcatalogdetail&fcaid=$fcaid$pfcaid_suffix");
		}else{
			cls_message::show('����������Ӳ��ɹ���');
		}
	}
}elseif($action =='fcatalogcopy' && $fcaid){
	if(!($fcatalog = cls_fcatalog::InitialOneInfo($fcaid))) cls_message::show('��ָ����ȷ�ĸ������ࡣ');
	echo _08_HTML::Title("���Ƹ�������-{$fcatalog['title']}");
	if(!submitcheck('bsubmit')){
		tabheader("���Ƹ������� - {$fcatalog['title']}",'fcatalogadd',"?entry=$entry&action=$action&fcaid=$fcaid$pfcaid_suffix",2,0,1);
		trbasic('��������','fmdata[title]',$fcatalog['title'].'(����)','text',array('validate'=>makesubmitstr('fmdata[title]',1,0,4,30)));
		
		$na = array(
			'validate'=>' offset="1"' . makesubmitstr('fmdata[fcaid]',1,'tagtype',0,30),
			'guide' => '�涨��ʽ��ͷ�ַ�Ϊ��ĸ�������ַ�ֻ��Ϊ"��ĸ�����֡�_"��',
		);
		trbasic('Ӣ��Ψһ��ʶ','fmdata[fcaid]',$fcatalog['fcaid'].'_cp','text',$na);
		$ajaxURL = $cms_abs . _08_Http_Request::uri2MVC('ajax=check_fcaid&fcaid=%1');
		echo _08_HTML::AjaxCheckInput('fmdata[fcaid]', $ajaxURL);
		
        $arr = array(0 => '��������');
        foreach($fcatalogs as $k => $v) $v['pid'] || $arr[$k] = $v['title'];
		trbasic('��������','fmdata[pid]',makeoption($arr,$fcatalog['pid']),'select');
		
        cls_fcatalog::fAreaCoType(@$fcatalog['farea']); //�������� 
		
		tabfooter('bsubmit');
	}else{
		foreach(array('title','fcaid','pid') as $k){
			$fcatalog[$k] = @$fmdata[$k];
		}
		$nowID = cls_fcatalog::ModifyOneConfig($fmdata['fcaid'],$fcatalog,true);
		if($nowID){
			$fcfg = cls_fcatalog::InitialOneInfo($fcaid); 
			if(!empty($fcfg['ftype'])){
				try{
					_08_Advertising::AdvTagCopy($fcaid,$nowID); # ���ƹ��λģ���ǩ
				}catch(Exception $e){
					cls_message::show($e->getMessage());
				}
			}
			adminlog('���Ƹ�������');
			cls_message::show('�������ิ�Ƴɹ���',axaction(6,"?entry=$entry&action=fcatalogsedit$pfcaid_suffix"));
		}else{
			cls_message::show('�������ิ�Ʋ��ɹ���');
		}
	}
}elseif($action =='fcatalogdetail' && $fcaid){
	if(!($fcatalog = cls_fcatalog::InitialOneInfo($fcaid))) cls_message::show('��ָ����ȷ�ĸ������ࡣ');
	echo _08_HTML::Title("������������-{$fcatalog['title']}");
	if(!submitcheck('bfcatalogdetail')){
		tabheader("������������&nbsp;&nbsp;[$fcatalog[title]]",'fcatalogdetail',"?entry=$entry&action=$action&fcaid=$fcaid$pfcaid_suffix",2,0,1);
		trbasic('Ӣ��Ψһ��ʶ','',$fcatalog['fcaid'],'');
		trbasic('����ģ��','',cls_fchannel::Config($fcatalog['chid'],'cname'),'');
		if(!cls_fcatalog::InitialInfoArray($fcaid)){
			$arr = array(0 => '��������');
			foreach($fcatalogs as $k => $v){
				if(empty($v['pid']) && ($k != $fcaid)){
					$arr[$k] = $v['title'];
				}
			}
			trbasic('��������','fmdata[pid]',makeoption($arr,$fcatalog['pid']),'select');
		}
		
        cls_fcatalog::fAreaCoType(@$fcatalog['farea']); //��������
		
		setPermBar('����Ȩ������', 'fmdata[apmid]', @$fcatalog['apmid'], 'fadd', 'open', '');
        trbasic('��������','fmdata[ftype]',makeoption(array('Ĭ������', '�������'),$fcatalog['ftype']),'select');
		trbasic('��Ϣ�Զ����','fmdata[autocheck]',$fcatalog['autocheck'],'radio');
		trbasic('������ʱ������','fmdata[nodurat]',$fcatalog['nodurat'],'radio');
		trbasic('��̬�����ʽ','fmdata[customurl]',$fcatalog['customurl'],'text',array('guide'=>'����ΪϵͳĬ��{$infodir}/a-{$aid}-{$page}.html��{$infodir}������Ŀ¼��{$y}�� {$m}�� {$d}�� {$aid}����id {$page}��ҳҳ�� ����֮�佨���÷ָ���_��-���ӡ�','w'=>50));
		trbasic('����˵��','fmdata[content]',$fcatalog['content'],'text',array('guide'=>'��ע�����ڵ�ģ��,��ǩ,��ʾ��ַ����Ϣ','w'=>50));
		tabfooter('bfcatalogdetail');
		a_guide('fcatalogdetail');
	}else{
		cls_fcatalog::ModifyOneConfig($fcaid,$fmdata,false);
		adminlog('��ϸ�޸ĸ�����Ϣ');
		cls_message::show('�����������', axaction(6,"?entry=$entry&action=fcatalogsedit$pfcaid_suffix"));
	}

}elseif($action == 'fcatalogdel' && $fcaid) {	
	backnav('fchannel','coclass');
	deep_allow($no_deepmode);
	$reurl = "?entry=$entry&action=fcatalogsedit$pfcaid_suffix";
	if(empty($confirm)){
		$message = "ɾ�����ָܻ���ȷ��ɾ����ѡ��Ŀ?<br><br>";
		$message .= "ȷ������>><a href='?entry=$entry&action=$action&fcaid=$fcaid$pfcaid_suffix&confirm=ok'>ɾ��</a><br>";
		$message .= "��������>><a href='$reurl'>����</a>";
		cls_message::show($message);
	}
	if($re = cls_fcatalog::DeleteOne($fcaid)) cls_message::show($re, $reurl);
	adminlog('ɾ����������');
	cls_message::show('����ɾ�����', $reurl);
}else cls_message::show('������ļ�����');

