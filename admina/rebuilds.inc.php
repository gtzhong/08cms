<?php
(!defined('M_COM') || !defined('M_ADMIN')) && exit('No Permission');
aheader();
if(empty($action)){
	backnav('rebuilds','system');
	if($re = $curuser->NoBackFunc('affix')) cls_message::show($re);
	if(!submitcheck('brebuilds')){
		tabheader('ˢ��ϵͳ����',$actionid.'rebuilds',"?entry=rebuilds",2);
		trbasic('�Ż��������','',"<input class=\"checkbox\" type=\"checkbox\" name=\"arcdeal[excache]\" value=\"1\" checked>�Ż��������",'',array('guide' => '���ֶ��޸Ĺ������ļ�����Ҫ���Ż�������²���ʹ������Ч��'));
		trbasic('ϵͳ���û���','',"<input class=\"checkbox\" type=\"checkbox\" name=\"arcdeal[based]\" value=\"1\" checked>��������&nbsp; <input class=\"checkbox\" type=\"checkbox\" name=\"arcdeal[cnode]\" value=\"1\">��Ŀ�ڵ�",'',array('guide' => '���ϻ���Ϊ��װʱϵͳ���ã����ǳ������⣬һ�㲻��Ҫ�ֶ����¡�'));
		trbasic('ģ��ҳ�滺��','',"<input class=\"checkbox\" type=\"checkbox\" name=\"arcdeal[common]\" value=\"1\" checked>ǰ̨ģ��&nbsp; <input class=\"checkbox\" type=\"checkbox\" name=\"arcdeal[mcenter]\" value=\"1\" checked>��Ա����ģ��",'',array('guide' => '�ڵ���״̬�ر�ʱ����Ҫ�ֶ����¸û��棬����ʹģ����ʶ���޸���Ч��<br>ģ�建����ģ��ͨ��ϵͳ���ͺ��PHP�ļ�(ʵ������ģ��Ч�����ļ�)��λ��dynamic/tplcache��'));
		trbasic('��滺��','',"<input class=\"checkbox\" type=\"checkbox\" name=\"arcdeal[adv]\" value=\"1\" checked>��滺��",'',array('guide' => 'ѡ��ˢ�¹�滺���ϵͳ������ˢ�����еĹ�滺�棬�����ѡ�򰴹��λ��������ʱ���Զ�����'));
		tabfooter('brebuilds');
		a_guide('rebuildcache');
	}else{
		if(!empty($arcdeal['excache'])){
			$m_excache->clear();
		}
		if(!empty($arcdeal['based'])){
			$mconfigs = cls_cache::Read('mconfigs');
			cls_CacheFile::ReBuild("cnodes,o_cnodes,mcnodes");
		}
		if(!empty($arcdeal['cnode'])){
			cls_CacheFile::Update("cnodes");
			cls_CacheFile::Update("o_cnodes");
			cls_CacheFile::Update("mcnodes");
		}
		if(!empty($arcdeal['common'])){
			clear_dir(cls_Parse::TplCacheDirFile(''));
		}
		
		if(!empty($arcdeal['mcenter'])){
			clear_dir(cls_Parse::TplCacheDirFile('',1));
		}
	
		if(!empty($arcdeal['adv'])) {
		    _08_Advertising::cheanAllCache();
        }
		cls_message::show('ϵͳ���������ɣ�', "?entry=$entry");#��Ϊ�������ӻ������ѭ��
	}
}elseif($action == 'pagecache'){
	backnav('rebuilds','pagecache');
	if($re = $curuser->NoBackFunc('affix')) cls_message::show($re);
	$pctypes = array(
		1 => '��Ŀ�ڵ�|index.php',
		2 => '�ĵ�ҳ|archive.php',
		3 => '����ҳ|info.php',
		4 => '����ҳ|search.php',
		5 => '��Ա�ڵ�|member/index.php',
		6 => '��Ա����|member/search.php',
		7 => '�ռ���Ŀ|mspace/index.php',
		8 => '�ռ��ĵ�|mspace/archive.php',
		9 => 'js����|tools/js.php',
		);
	$pc_records = cls_cache::Read('pc_records');
	if(!submitcheck('bsubmit')){
		tabheader("����ҳ�滺�� &nbsp;<input class=\"checkbox\" type=\"checkbox\" name=\"chkall\" onclick=\"checkall(this.form,'typeids','chkall')\">ȫѡ",$actionid.'rebuilds',"?entry=$entry&action=$action",2);
		foreach($pctypes as $k => $v){
			!empty($pc_records[$k]) && $v .= ' &nbsp;[�ϴ�����:'.date('Y-m-d H:i',$pc_records[$k]).']';
			trbasic("<input class=\"checkbox\" type=\"checkbox\" name=\"typeids[]\" value=\"$k\">",'',$v,'',array('guide' => "�����ļ�����Ŀ¼:dynamic/htmlcac/$k/",));
		}
		tabfooter('bsubmit');
		a_guide('clearpagecache');
	}else{
		if(empty($typeids) && !empty($typeidstr)) $typeids = array_filter(explode(',',$typeidstr));
		if(empty($typeids)) cls_message::show('��ѡ����Ҫ����Ļ�������');
		$typeid = array_shift($typeids);
		if(isset($pctypes[$typeid])){
			clear_dir(cls_cache::HtmlcacDir($typeid),true);
			$pc_records[$typeid] = $timestamp;
			cls_CacheFile::Save($pc_records,'pc_records');
		}

		if(empty($typeids)){
			cls_message::show('ҳ�滺��������ɣ�',"?entry=$entry&action=$action");
		}else{
			$typeidstr = implode(',',$typeids);
			cls_message::show("����".count($typeids)."���������ĵȴ�...","?entry=$entry&action=$action&typeidstr=$typeidstr&bsubmit=1");
		}
	}
}elseif($action == 'backup'){
	backnav('rebuilds','backup');
	if($re = $curuser->NoBackFunc('affix')) cls_message::show($re);
	@mmkdir(M_ROOT."dynamic/cathe_backup/",0);
	$templatedir = cls_env::GetG('templatedir');
	$extend_dir = cls_env::getBaseIncConfigs('_08_extend_dir'); //cls_env::GetG('templatedir');
	$dirkey = empty($dirkey) ? 'tpl_cache' : $dirkey;
	$tabback = array(
		'mconfig'       => array('dynamic/cache/mconfigs.cac.php','��������'),
		'syscache'      => array("$extend_dir/dynamic/syscache",'ϵͳ����'),
		'tpl_config'    => array("template/$templatedir/config",'ģ�滺��'),
		'tpl_tag'       => array("template/$templatedir/tag",'ģ���ǩ'),
		'tpl_tpl'       => array("template/$templatedir/tpl",'ģ���ļ�'),
		'tpl_function'  => array("template/$templatedir/function",'ģ�溯��'),
	);
	if(!isset($tabback[$dirkey])) $dirkey = 'tpl_config';
	$sarr = array();
	foreach($tabback as $k=>$v){
		$title = " title='��Ӧ�ļ�/Ŀ¼: {$v[0]}'";
		$arr[] = $dirkey == $k ? "<b $title>{$v[1]}({$k})</b>" : "<a href=\"?entry={$entry}&action={$action}&dirkey={$k}\" $title>{$v[1]}({$k})</a>";
	}
	echo tab_list($arr,7,0);
	if(!submitcheck('bsubmit')){
		$selector = "<input class=\"checkbox\" type=\"checkbox\" name=\"chkall\" onclick=\"checkall(this.form,'backups','chkall')\">ȫѡ";
		tabheader("���汸���б� &nbsp;$selector",$actionid.'cachebackup',"?entry=$entry&action=$action&dirkey=$dirkey",2);
		$path = str_replace('/',DS,'dynamic/cathe_backup/');
		$dir = new DirectoryIterator(M_ROOT.$path);
		$num = 0;
		foreach($dir as $it){
			if($it->isDir() && !$it->isDot()){
				$dirname = $it->getFileName();
				$addtime = date('Y-m-d H:i:s',$it->getCTime()); //  title='����ʱ��:{$addtime}'
				if(preg_match("/^{$dirkey}[0-9\_]{15}$/",$dirname)){ //syscache_2014_0709_1658
					trbasic("<input class=\"checkbox\" type=\"checkbox\" name=\"backups[]\" value=\"$dirname\">",'',$tabback[$dirkey][1].'����: '.$dirname,'',array('guide' => "����Ŀ¼:dynamic/cathe_backup/$dirname/",));
					$num++;
				}
			}
		}
		$cathedir = $tabback[$dirkey][1]."($dirkey): ��Ӧ�ļ�/Ŀ¼: {$tabback[$dirkey][0]}";
		if($num){
			trbasic("",'',"��{$num}�����ݣ�<a href='?entry=$entry&action=$action&dirkey=$dirkey&do=back&bsubmit=backup' class='cBlue'>&gt;&gt;�����һ������</a>��",'',array('guide' => $cathedir,));	
			tabfooter('bsubmit','ɾ����ѡ');
		}else{
			trbasic("",'',$tabback[$dirkey][1]." ���ޱ��ݣ�<a href='?entry=$entry&action=$action&dirkey=$dirkey&do=back&bsubmit=backup' class='cBlue'>&gt;&gt;�������һ������</a>��",'',array('guide' => $cathedir,));	
			tabfooter('','ɾ����ѡ');
		}
		a_guide('backupcache');
	}else{
		if($bsubmit=='backup'){
			$actname = '���ݻ���';
			$newdir = "{$dirkey}".date('_Y_md_Hi'); 
			$fulldir = "dynamic/cathe_backup/$newdir/";
			if(!is_dir(M_ROOT.$fulldir)){ 
				@mmkdir(M_ROOT.$fulldir,0);
			}else{
				cls_message::show('����Ƶ����һ���Ӻ��ڲ�����',"?entry=$entry&action=$action&dirkey=$dirkey");
			}
			$path = str_replace('/',DS,$tabback[$dirkey][0]);
			if(is_file($path)){ 
				copy($path,M_ROOT.$fulldir.basename($path));
			}else{
				$iterator = new DirectoryIterator(M_ROOT.$path);
				foreach($iterator as $it){
					 if($it->isFile()) {
						 $filename = $it->getFileName();
						 $fullname = M_ROOT.$fulldir.$filename;
						 copy($it->getPathname(),$fullname);
					}
				}
			}
			$dores = ' �����ɹ���';
		}else{
			$actname = 'ɾ������'; $n=0;
			$backups = empty($backups) ? array() : $backups;
			$fso = _08_FilesystemFile::getInstance();
			foreach($backups as $v){
				$fso->cleanPathFile("dynamic/cathe_backup/$v/");
				@rmdir(M_ROOT."dynamic/cathe_backup/$v/");
				$n++;
			}
			//cleanPathFile($path, $exts = '', $traversal = false)
			$dores = $n ? ' �����ɹ���' : ' ����ʧ�ܣ�';
		}
		cls_message::show($actname.$dores,"?entry=$entry&action=$action&dirkey=$dirkey");
	}
}



?>