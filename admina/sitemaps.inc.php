<?
(!defined('M_COM') || !defined('M_ADMIN')) && exit('No Permission');
aheader();
if($re = $curuser->NoBackFunc('sitemap')) cls_message::show($re);
foreach(array('catalogs','cotypes','channels',) as $k) $$k = cls_cache::Read($k);
$objcron=new cls_cron();
if($action == 'sitemapsedit'){
	$sitemaps = fetch_arr();
	if(!submitcheck('bsitemapsedit')){
		tabheader("Sitemapҳ�����&nbsp; &nbsp; >><a href=\"?entry=sitemaps&action=sitemapsadd\" onclick=\"return floatwin('open_sitemapsadd',this)\">���</a>",'sitemapsedit',"?entry=sitemaps&action=sitemapsedit",'8');
		trcategory(array('����',array('Sitemap����','txtL'),array('��̬��������','txtL'),array('XML��������','txtL'),'����','ɾ?','��ϸ','����'));
		foreach($sitemaps as $ename => $sitemap){
			$d_url = "sitemap.php?map=$ename";
			echo "<tr class=\"txt\">".
				"<td class=\"txtC w30\"><input class=\"checkbox\" type=\"checkbox\" name=\"sitemapsnew[$ename][available]\" value=\"1\"".(empty($sitemap['available']) ? '' : ' checked')."></td>\n".
				"<td class=\"txtL\">".mhtmlspecialchars($sitemap['cname'])."</td>\n".
				"<td class=\"txtL\"><a target=\"_blank\" href=\"".cls_url::view_url($d_url)."\">{$d_url}</a></td>\n".
				"<td class=\"txtL\"><a target=\"_blank\" href=\"".cls_url::view_url($sitemap['xml_url'])."\">".cls_url::view_url($sitemap['xml_url'])."</a></td>\n".
				"<td class=\"txtC w40\"><input type=\"text\" size=\"4\" maxlength=\"4\" name=\"sitemapsnew[$ename][vieworder]\" value=\"$sitemap[vieworder]\"></td>\n".
				"<td class=\"txtC w40\"><input class=\"checkbox\" type=\"checkbox\"".(empty($sitemap['issystem']) ? " name=\"delete[$ename]\" value=\"1\" onclick=\"deltip(this,$no_deepmode)\"" : ' disabled').">\n".
				"<td class=\"txtC w30\"><a href=\"?entry=sitemaps&action=sitemapdetail&ename=$ename\" onclick=\"return floatwin('open_sitemapdetail',this)\">����</a></td>\n".
				"<td class=\"txtC w30\"><a href=\"?entry=sitemaps&action=sitemapcreate&ename=$ename\">����</a></td></tr>\n";
		}
		tabfooter('bsitemapsedit');
		
		$note = "- �˲��������ڡ��ٶ��������͡������� <a href='http://zhanzhang.baidu.com/linksubmit/index' style='color:blue;' target='_blank'>���� �ٶ��������� �ӿ�</a>��";
		$note .= "ÿ���ɼƻ���������ָ����xml�����ϵ�ַ�� <br>- ʾ����http://data.zz.baidu.com/urls?site=www.example.com&token=edk7yc4rEZP9pDQD��";
		tabheader('��������','sitemapsedit',"?entry=sitemaps&action=sitemapsedit&api=1");
		trbasic('�������ͽӿڵ�ַ','',"<label for='mconfigsnew[baidu_push_api]'></label><input type='text' id='mconfigsnew[baidu_push_api]' name='mconfigsnew[baidu_push_api]' value='".@$mconfigs['baidu_push_api']."' style='width:320px'>",'',array('guide'=>$note));
		trbasic('�������ͽű�����','',"<label for='mconfigsnew[baidu_push_name]'></label><input type='text' id='mconfigsnew[baidu_push_name]' name='mconfigsnew[baidu_push_name]' value='".@$mconfigs['baidu_push_name']."' style='width:200px'>",'',array('guide'=>"�˲�����<a href='?entry=misc&action=cronedit&isframe=1' style='color:blue;' target='_blank'>�ƻ�����</a>������������͵Ľű����Ʊ���һ�£����磺baidu_mob_push.php��"));
		trbasic('������������','','','',array('guide'=>" <a onclick=\"return floatwin('open_inarchive',this)\" href='?entry=sitemaps&action=sitemapsedit&push_now=1&bsitemapsedit=�ύ' style='color:blue;'>ִ��</a><p>˵�����������ú�'�������ͽӿڵ�ַ'��'�������ͽű�����'</p>"));
		tabfooter('bsitemapsedit');
		a_guide('sitemapsedit');
	}else{
		
		$baidu_push_name = $mconfigs['baidu_push_name'];
		
		if($action == 'sitemapsedit' && @$api){ //�����������ͽӿڲ���
			
			$new_push_name = $mconfigsnew['baidu_push_name'];
			if(empty($new_push_name) || !$objcron->isFile($new_push_name)) cls_message::show('����ִ���ļ������ڻ�������������"�������ͽű�����"��','?entry=sitemaps&action=sitemapsedit');
			saveconfig('site');
			if($new_push_name != $baidu_push_name){ //����������ͽű����Ƹı䣬��Ӧ�޸ļƻ�����ű�����
				$db->query("update {$tblprefix}cron set filename='$new_push_name' where filename='$baidu_push_name'");
			}
 
			cls_message::show('�ӿ��������',M_REFERER);	
						    			    		    
		}elseif($action == 'sitemapsedit' && @$push_now){//������������
			
		    if(empty($mconfigs['baidu_push_api'])){
		    	cls_message::show('�������������ͽӿڵ�ַ', axaction(6,'?entry=sitemaps&action=sitemapsedit'));
		    }
		    
		    if(empty($baidu_push_name) || !$objcron->isFile($baidu_push_name)) cls_message::show('����ִ���ļ������ڻ�������������"�������ͽű�����"��',axaction(6,'?entry=sitemaps&action=sitemapsedit'));
		    
		    $cronid = $db->result_one("select cronid from {$tblprefix}cron where filename='$baidu_push_name'");
		    if(empty($cronid)){
		    	cls_message::show('�������ͼƻ����񲻴���', axaction(6,'?entry=sitemaps&action=sitemapsedit'));
		    }else{
		    		
		    	cls_SitemapPage::Create(array('map' => 'baidu_mob_push','inStatic' => true));

		    	$cronid = max(0,intval($cronid));
		    	$ret = $objcron->run($cronid);
		    	$msg = $ret ? '�ƻ�����ִ�гɹ�' : '<span style="color:red;">�ƻ�����ûִ��</span>';
		    	cls_message::show($msg,axaction(6,'?entry=sitemaps&action=sitemapsedit'));
		    }
		
		}else{ // Sitemapҳ����� 
			if(!empty($delete) && deep_allow($no_deepmode)){
				foreach($delete as $k=>$v){
					$db->query("DELETE FROM {$tblprefix}sitemaps WHERE ename='$k'");
				}
			}

			foreach($sitemapsnew as $ename => $v){
				$v['available'] = empty($v['available']) ? 0 : $v['available'];
				$db->query("UPDATE {$tblprefix}sitemaps SET available='$v[available]',vieworder='$v[vieworder]' WHERE ename='$ename'");
			}
			cls_CacheFile::Update('sitemaps');
			unset($delete);
			unset($sitemapsnew);
			cls_message::show('Sitemap�޸����', "?entry=sitemaps&action=sitemapsedit");
		}
	}
}elseif($action == 'sitemapdetail' && $ename){
	$sitemap = fetch_one($ename);
	empty($sitemap) && cls_message::show('��ָ����ȷ��Sitemap', '?entry=sitemaps&action=sitemapsedit');
	if(!submitcheck('bsitemapdetail')){
		tabheader('Sitemap����','sitemapdetail','?entry=sitemaps&action=sitemapdetail&ename='.$ename);

		trhidden('ename',$sitemap['ename']);
		trbasic('* Sitemap����','sitemapnew[cname]',$sitemap['cname']);
		trbasic('* �Ƿ�����','sitemapnew[available]',isset($sitemap['available']) ? $sitemap['available'] : 0,'radio');
		trbasic('* XML�ļ���','sitemapnew[xml_url]',$sitemap['xml_url'],'text',array('guide' => 'XML�ļ���,�磺example.xml'));
		trbasic('ģ���ļ�','sitemapnew[tpl]',makeoption(cls_mtpl::mtplsarr('xml'),$sitemap['tpl']),'select',array('guide' => cls_mtpl::mtplGuide('xml')));
		trbasic('����','sitemapnew[ttl]',$sitemap['ttl'],'text',array('guide' => '�������ڣ���λ��Сʱ'));
		trbasic('����','sitemapnew[vieworder]',$sitemap['vieworder'],'text',array('guide' => '�б�����'));

		tabfooter('bsitemapdetail','�޸�');
		a_guide('sitemapdetail');
	}else{
		$ename || cls_message::show('ȱ��Sitemap��ʶ',M_REFERER);
		$sitemapnew['cname'] = trim(strip_tags($sitemapnew['cname']));
		$sitemapnew['cname'] || cls_message::show('ȱ��Sitemap����',M_REFERER);
		if($re = _08_FilesystemFile::CheckFileName($sitemapnew['xml_url'],'xml')) cls_message::show($re,M_REFERER);
		$sitemapnew['available'] = empty($sitemapnew['available']) ? 0 : 1;
		$sitemapnew['vieworder'] = empty($sitemapnew['vieworder']) ? 0 : max(0,intval($sitemapnew['vieworder']));
		$sitemapnew['tpl'] = empty($sitemapnew['tpl']) ? '' : $sitemapnew['tpl'];
		$sitemapnew['ttl'] = empty($sitemapnew['ttl']) ? 0 : max(0,intval($sitemapnew['ttl']));

		$db->query("UPDATE {$tblprefix}sitemaps SET cname='$sitemapnew[cname]',xml_url='$sitemapnew[xml_url]',available='$sitemapnew[available]',vieworder='$sitemapnew[vieworder]',tpl='$sitemapnew[tpl]',ttl='$sitemapnew[ttl]' WHERE ename='$ename'");
		cls_CacheFile::Update('sitemaps');
		adminlog('����Sitemap');
		cls_message::show('Sitemap�������',axaction(6,"?entry=sitemaps&action=sitemapsedit"));
	}

}elseif($action == 'sitemapcreate' && $ename){
	$re = cls_SitemapPage::Create(array('map' => $ename,'inStatic' => true));
	cls_message::show($re, '?entry=sitemaps&action=sitemapsedit');
} elseif($action == 'sitemapsinfo' && $ename) {
	$sitemap = fetch_one($ename);
	empty($sitemap) && cls_message::show('��ָ����ȷ��Sitemap');
	tabheader("$sitemap[cname] ������Ϣ");
	trbasic('��̬��������','',cls_url::view_url($sitemap['d_url']),'');
	trbasic('XML��������','',cls_url::view_url($sitemap['xml_url']),'');
	tabfooter();
	a_guide('sitemapsinfo');
} elseif($action == 'sitemapsadd') {
	if(!submitcheck('bgsitemapsadd')){
		tabheader('Sitemap���','sitemapsadd',"?entry=sitemaps&action=sitemapsadd");
		trbasic('* Sitemap����','sitemapsadd[cname]');
		trbasic('* Sitemap��ʶ','','<input type="text" value="" name="sitemapsadd[ename]" id="sitemapsadd[ename]" size="25">&nbsp;&nbsp;<input type="button" value="�������" onclick="check_repeat(\'sitemapsadd[ename]\',\'check_sitemaps_repeat\');">','');
		trbasic('* XML�ļ���','sitemapsadd[xml_url]','','text',array('guide' => 'XML�ļ���,�磺example.xml'));
		trbasic('ģ���ļ�','sitemapsadd[tpl]',makeoption(cls_mtpl::mtplsarr('xml')),'select',array('guide' => cls_mtpl::mtplGuide('xml')));
		trbasic('����','sitemapsadd[ttl]','0','text',array('guide' => '�������ڣ���λ��Сʱ'));
		trbasic('����','sitemapsadd[vieworder]','0','text',array('guide' => '�б�����'));
		tabfooter('bgsitemapsadd','���');
		a_guide('sitemapsadd');
	}else{
		$sitemapsadd['cname'] = trim(strip_tags($sitemapsadd['cname']));
		$sitemapsadd['ename'] || cls_message::show('ȱ��Sitemap��ʶ',M_REFERER);
		$sitemapsadd['cname'] || cls_message::show('ȱ��Sitemap����',M_REFERER);
		if($re = _08_FilesystemFile::CheckFileName($sitemapsadd['xml_url'],'xml')) cls_message::show($re,M_REFERER);
		//$sitemapsadd['tpl'] || cls_message::show('ȱ��ģ���ļ�',M_REFERER);
		$sitemapsadd['vieworder'] = empty($sitemapsadd['vieworder']) ? 0 : max(0,intval($sitemapsadd['vieworder']));
		$sitemapsadd['ttl'] = empty($sitemapsadd['ttl']) ? 0 : max(0,intval($sitemapsadd['ttl']));
		$db->query("INSERT INTO {$tblprefix}sitemaps SET ename='$sitemapsadd[ename]',cname='$sitemapsadd[cname]',xml_url='$sitemapsadd[xml_url]',available='1',vieworder='$sitemapsadd[vieworder]',tpl='$sitemapsadd[tpl]',ttl='$sitemapsadd[ttl]'");
		unset($sitemapsadd);
		adminlog('���Sitemap');
		cls_message::show('Sitemap������',axaction(6,"?entry=sitemaps&action=sitemapsedit"));
	}
}
function fetch_arr(){
	global $db,$tblprefix;
	$sitemaps = array();
	$query = $db->query("SELECT * FROM {$tblprefix}sitemaps ORDER BY vieworder");
	while($sitemap = $db->fetch_array($query)){
		$sitemaps[$sitemap['ename']] = $sitemap;
	}
	return $sitemaps;
}
function fetch_one($ename){
	global $db,$tblprefix;
	$sitemap = $db->fetch_one("SELECT * FROM {$tblprefix}sitemaps WHERE ename='$ename'");
	return $sitemap;
}
?>
