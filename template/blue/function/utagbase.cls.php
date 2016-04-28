<?php

// ģ���Զ�������������(����)
class cls_usobase{
	
	static $init_cfg = array(); //ģ���ʼ����
	static $fpath = ''; //�ɲ���ntype��õ�·�����, ntype��һ�ַ�
	static $fdata = ''; //�ɲ���ntype��õ��������ͱ��, ntype�ڶ��ַ�
	static $nodes = array(); //�ڵ�����
	static $rids_cfg = array(); //����������
	static $rids_data = array(); //����������
	static $rids_for = array();
	static $urls = array(); //����urls: cnstr,filterstr,filsearch,filorder��
	static $addno = '0'; //����ҳ
	static $addstr = ''; //����ҳstr
	static $csfull = ''; //�ű�����·���磺http://192.168.1.11/auto/mspace/index.php?
	
	/* ��ʼ��-�����, cfg�������£�
	  ntype    : ������ĸab��ʽ����, a��b�������, ca��ʡ��Ϊc, mm��ʡ��Ϊm, c:��Ŀ�ڵ�/m:��ԱƵ��/oa�ֻ���Ŀ/om:�ֻ���Ա/cm��Ŀ�ڵ��µĻ�Ա,��������˵��
	             a:�ڵ����ͼ�·�� --- c:��Ŀ�ڵ�[root],  m:��Ա�ڵ�[member],  o:�ֻ��ڵ�[mobile],  s:��Ա�ռ�[mspace]<��δ��>
				 b:�б���������   --- m:��Ա[member],    a:�ĵ�[archive],     u:����[commu]<δ��>, c:��Ŀ[class]<???��δ��>
	  chid     : ��Ա���ĵ�ģ��:chid,mchid
	  nodes    : ��ʾ�ڵ�����, ��Ŀ:array('caid'), ��Ŀ+ccid8:array('caid','caid,ccid8'), ugid33+ccid20:array('ugid33','ccid20')
	             ��Ŀ����дΪ0, ��ϵ��ֻд���֣��磺array('0','0,1','0,2','0,12','0,17','0,18','0,1,17'),
	  orderbys : ���õ������ֶ�, ��: array('mid','jian','msclicks','authentication'),
	  rids     : ������Ŀ, ��: array(1,2)
	  filparas : �����и����������Ŀ, ��: 'carsfullname'
	  addno    : Ĭ���õ�ǰҳaddno, �д˲�����˲�������,����ҳ�������������ӵ���ҳȥ
	  cnstr:   : ָ������cnstr,     ��:caid=33��ҳ�������������ӵ���ҳȥ;
	  filterstr: ָ������filterstr, <��δ��>
	  csname   : ����ļ���index.php�ɲ�ָ��
	  usword   : �ؼ���searchword�������
	*/
	static function init($cfgs){
		cls_uso::$init_cfg = $cfgs;
		$ntype = isset($cfgs['ntype']) ? $cfgs['ntype'] : 'c'; 
		if($ntype=='c') $ntype = 'ca'; //����֮ǰ
		if($ntype=='m') $ntype = 'mm'; //����֮ǰ
		cls_uso::$fpath = substr($ntype,0,1);
		cls_uso::$fdata = substr($ntype,-1); 
		$nodes = empty($cfgs['nodes']) ? array('-1') : $cfgs['nodes'];
		foreach($nodes as $k1=>$v1){ //������Ŀ/��ϵ������
			$arr2 = explode(',',$v1);
			foreach($arr2 as $k2=>$v2){
				$vx = empty($v2) ? 'caid' : (is_numeric($v2) ? "ccid$v2" : $v2);
				$arr2[$k2] = $vx;
			}
			$nodes[$k1] = implode(',',$arr2);
		}
		cls_uso::$nodes = $nodes;
		cls_uso::$rids_cfg = cls_uso::$rids_data = cls_uso::$urls = array(); //addno,addstr��init_urls()�г�ʼ��
		cls_uso::$csfull = '';
		if(isset($cfgs['orderbys'])) cls_usql::order_bys(); //�����ֶμ��
		cls_uso::init_rids(); //��Ŀ�������
		cls_uso::init_urls(); //urls��ʼ��
	}
	
	// ���������ϵ
	static function init_rids(){
		if(empty(cls_uso::$init_cfg['rids'])) return;
		$rids = cls_uso::$init_cfg['rids'];
		$rarr = array(); cls_uso::$rids_for[] = -1; //Ĭ��һ��,����in_array()�Ƚ�
		foreach($rids as $rid){
			$cnrel = cls_cache::Read('cnrel', $rid);
			if(empty($cnrel)) return;
			$coid = $cnrel['coid'];
			$coid1 = $cnrel['coid1'];
			cls_uso::$rids_cfg[$coid1] = $coid;
			foreach($cnrel['cfgs'] as $k=>$v){
				$rarr["ccid{$coid}_$k"] = $v; //��ʽ�磺ccid20_1296=11,22,33, ������Ϊ��������ö���
			}
		} 
		cls_uso::$rids_data = $rarr;
		// ���������? ����δʵ��; ��Ҫʱ�ٿ���
	}
	
	// �����ʼ���õ�url
	static function init_urls(){
		$_da = cls_Parse::Get('_da'); 
		foreach(array('cnstr','filterstr') as $k){
			$$k = cls_uso::$urls[$k] = isset(cls_uso::$init_cfg[$k]) ? cls_uso::$init_cfg[$k] : (isset($_da[$k]) ? $_da[$k] : '');
		} 
		if(!empty($filterstr)){ //�������Ŀ; &ccid8=0&ccid23=0&ccid11=1246&ccid22=0&ccid20=0
			//$filterstr = preg_replace("/&(\w*)=[0]{1}/", '', $filterstr); //? &iscorp=0(���̼�,����)Ҫ����
			$filterstr = preg_replace("/&(ccid\d+)=[0]{1}/", '', $filterstr); //? &ccid999=0(����ϵ)Ҫȥ��
			$filterstr = preg_replace("/&(\w*)=&/", '&', $filterstr);
			cls_uso::$urls['filterstr'] = $filterstr;
		} 
		$filorder = "orderby|ordermode|page";
		$filparas = empty(cls_uso::$init_cfg['usword']) ? "$filorder|searchword" : $filorder; 
		if(!empty(cls_uso::$init_cfg['filparas'])) $filparas .= "|".cls_uso::$init_cfg['filparas']."";
		$prego = "/&(?:$filorder)=[^&]*|\b(?:$filorder)=[^&]*&?/"; //[filterstr]ȥ��orderby|ordermode|page����
		$pregs = "/&(?:$filparas)=[^&]*|\b(?:$filparas)=[^&]*&?/"; //[filterstr]ȥ��order��ؼ������ؼ�������
		cls_uso::$urls['filorder'] = cls_uso::$urls['cnstr'].(empty($filterstr) ? '' : preg_replace($prego, '', $filterstr)); 
		cls_uso::$urls['filsearch'] = cls_uso::$urls['cnstr'].(empty($filterstr) ? '' : preg_replace($pregs, '', $filterstr)); 
		cls_uso::$urls['fullurl'] = cls_uso::$urls['cnstr'].$filterstr;
		cls_uso::$addno = $addno = empty(cls_uso::$init_cfg['addno']) ? (isset($_da['addno']) ? $_da['addno'] : 0) : cls_uso::$init_cfg['addno']; 
		cls_uso::$addstr = empty($addno) ? '' : "&addno=$addno"; 
	}

	// ���ڵ�
	static function node_check($cnstr){
		if(defined('IN_MOBILE')) return false;
		parse_str($cnstr,$a); 
		$b = $c = array();
		foreach($a as $k=>$v){
			if($k=='addno') continue;
			if($k=='caid'){
				$key = '0';
			}elseif(substr($k,0,4)=='ccid'){
				$key = substr($k,4);
			}elseif($k=='ugid'){
				$key = $k;
			}else{ //�ֶ�,���ǽڵ�
				return false;	
			}
			$b[$key] = $v;
			$c[$k] = $v;
		}
		ksort($b,SORT_NUMERIC); 
		$keys = array_keys($b); 
		$cnkey = ''; $cnstr = ''; 
		foreach($keys as $k){
			$key = empty($k) ? 'caid' : (is_numeric($k) ? "ccid$k" : $k);
			$cnkey .= (empty($cnkey) ? '' : ',').$key;
			$cnstr .= (empty($cnstr) ? '' : '&')."$key=".@$c[$key];
		} //print_r($cnkey); print_r(cls_uso::$nodes);
		if(in_array($cnkey,cls_uso::$nodes)){  
			if(cls_uso::$fpath=='m'){
				$node = cls_node::mcnodearr($cnstr); 
				$url = $node["mcnurl".(empty(cls_uso::$addno) ? '' : cls_uso::$addno)];
			}elseif(in_array(cls_uso::$fpath,array('c','o'))){
				$node = cls_node::cnodearr($cnstr, defined('IN_MOBILE') ? 1 : 0);
				$url = @$node["indexurl".(empty(cls_uso::$addno) ? '' : cls_uso::$addno)];
			}
			return $url;
		}else{
			return false;		
		}
	}
	
	/* ������ʽ������html��������Ҫ�������Ÿ�
	  $tpl ����ģ��
	  $by Ҫ������ֶ�
	  $class ��ʽ���� �磺array('��ǰ����','��ǰ����','δѡ����ʽ')
	  $class['defmode']=1, Ĭ�ϵ�ordermode=1������Ĭ��ordermode=0
	*/
	static function order_tpl($tpl, $by, $classes){ //$orderby, $ordermode, 
		$orderby = cls_Parse::Get('_da.orderby');
		$ordermode = cls_Parse::Get('_da.ordermode');
		if($by!=$orderby){
			$defmode = empty($class['defmode']) ? '' : '&ordermode=1';
		}elseif($ordermode){
			$defmode = '';
		}else{
			$defmode = '&ordermode=1';
		}
		$url = cls_uso::$urls['filorder'].cls_uso::$addstr; 
		$url = cls_uso::format_url("$url&orderby=$by$defmode",1);
		$class = @$classes[$by == $orderby ? ($ordermode ? '1' : 0) : 2];
		$str = str_replace(array('(url)','(class)'),array($url,$class),$tpl);
		return $str;
	}
	
	/* ������ʽ������html��������Ҫ�������Ÿ�
	  $title ��ʾ����
	  $by Ҫ������ֶ�
	  $orderby ��ǰ���ڵ������ֶ�
	  $ordermode ��ǰ������ʽ
	  $class ��ʽ���� �磺array('��ǰ����','��ǰ����','δѡ����ʽ')
	  $class['defmode']=1, Ĭ�ϵ�ordermode=1������Ĭ��ordermode=0
	*/
	static function order_set($title, $by, $orderby, $ordermode, $class){
		$url = cls_uso::$urls['filorder'].cls_uso::$addstr; 
		if($by!=$orderby){
			$defmode = empty($class['defmode']) ? '' : '&ordermode=1';
		}elseif($ordermode){
			$defmode = '';
		}else{
			$defmode = '&ordermode=1';
		}
		$url = cls_uso::format_url("$url&orderby=$by$defmode",1);
		$str = '<a rel="nofollow" class="' . @$class[$by == $orderby ? ($ordermode ? '1' : 0) : 2] . "\" href='$url'>$title</a>";
		return $str;
	}
	
	// ���в���[�����ǰ]���URL
	static function pick_urls($cfgs = array()){ 
		$_k = 'searchword'; $_kv = cls_Parse::Get("_da.searchword");
		if(!isset($cfgs[$_k]) && !empty($_kv)) $cfgs[$_k] = $_kv;
		$paras = cls_uso::$urls['filorder'];                   
		parse_str($paras,$a); 
		$cache = array(); 
		foreach($a as $key=>$v){
			if(empty($v)) continue;
			if($key=='searchmode') continue; //����֮ǰ
			$key = cls_string::ParamFormat($key); // preg_replace('/[^\w]/', '', $key);
			if(isset($cfgs[$key])){ //��������Ƚ϶�...��cfgs�мӲ���һ��ʵ��, ֧��{key},{v}ռλ��
				$title = $cfgs[$key];
				$title = str_replace(array('{key}','{v}'),array($key,$v),$title);
			}else{
				$chid = (cls_uso::$fdata=='m' ? 'm' : '').cls_uso::$init_cfg['chid'];
				$fkey = preg_match('/^ccid\d{1,6}$/i',$key) ? substr($key,4) : $key;
				$title = cls_uview::field_value($v, $fkey, $chid); //, $null='-'
			}
			if($key=='letter') $title = $v;
			$usearch = preg_replace("/&(?:$key)=[^&]*|\b(?:$key)=[^&]*&?/", '', $paras); 
			if($cnstr = cls_uso::node_check($usearch)){ 
				$url = $cnstr;
			}else{ 
				$url = cls_uso::format_url("$usearch".cls_uso::$addstr);
			}
			$cache[$key] = array(
				'title' => mhtmlspecialchars($title),
				'url' => $url, 
			);		
		}
		return $cache;
	}
	
	// �ų����URL
	// key   : �ų���key(s), ��:orderby|ordermode
	// exstr : ���ӵ�url, return:ֱ�ӷ���, fsale=2:���Ӳ���, ��:�Զ��жϽڵ�
	static function extra_url($key,$exstr=''){
		if(is_numeric($key)) $key = "ccid$key"; 
		// ���������ϵ, �磺cls_uso::extra_url(1) -=> ��������</a> ͬʱ����[��Ȧ]
		if(!empty(cls_uso::$rids_cfg) && substr($key,0,4)=='ccid'){
			$coid = intval(substr($key,4)); 
			if(in_array($coid,cls_uso::$rids_cfg)){ 
				foreach(cls_uso::$rids_cfg as $k=>$v){
					if($v==$coid){
						$key = "$key|ccid$k"; 
						break;	
					}
				}
			}
		} //ע��,��[���������ϵ]���������Ŀ����,û������,��Ҫ���ٲ�������
		$usearch = preg_replace("/&(?:$key)=[^&]*|\b(?:$key)=[^&]*&?/", '', cls_uso::$urls['filsearch']);
		if($exstr=='return'){ //ֱ�ӷ���,����һ����װ,����addno
			$url = cls_uso::format_url($usearch,1);
		}elseif(!empty($exstr)){ //���Ӳ���,��addno,ע�⸽�Ӳ����󻹿��ܳ�Ϊ�ڵ�
			$tmp = $usearch.$exstr.cls_uso::$addstr; 
			$cnstr = cls_uso::node_check($tmp); 
			$url = $cnstr ? $cnstr : cls_uso::format_url($tmp,1);
		}elseif($cnstr = cls_uso::node_check("$usearch")){ //&$key=$k
			$url = $cnstr;
		}else{
			$url = cls_uso::format_url("$usearch".cls_uso::$addstr);
		}
		return $url;	
	}
	
	// ���urlģ�棬��js����
	// return : ?caid=33&ccid11=1247&ccid20=1296&ccid1=[08cms_user_ccid], ����()
	static function extmp_url($key,$tpl='[08cms_user_ccid]'){
		$val = cls_Parse::Get("_da.$key");
		$usearch = preg_replace("/&(?:$key)=[^&]*|\b(?:$key)=[^&]*&?/", '', cls_uso::$urls['filsearch']);
		$url = cls_uso::format_url("$usearch&$key=$tpl".cls_uso::$addstr);
		return $url;
	}
	
	// url��ʽ��, ����$cms_abs,$mobiledir,member/,mspace/��·����en_virtual(),
	static function format_url($str,$dynamic=0){
		if(defined('IN_MOBILE')) $dynamic=1;
		if(!defined('UN_VIRTURE_URL')) $dynamic=1;//����ҳ�ö�̬
		if(empty(cls_uso::$csfull)){
			$cms_abs = cls_env::mconfig('cms_abs');
			if(cls_uso::$fpath=='o'){
				$mobiledir = cls_env::mconfig('mobiledir');
                $cspath = "$mobiledir/";
			}elseif(cls_uso::$fpath=='m'){
				$memberdir = cls_env::mconfig('memberdir');
                $cspath = "$memberdir/";
			}elseif(cls_uso::$fpath=='s'){
				$mspacedir = cls_env::mconfig('mspacedir');
                $cspath = "$mspacedir/";
			}else{
				$cspath = "";	
			}
			// ����index.php��ڵ�Ҫָ����ڣ�������$_SERVER['SCRIPT_NAME'],�����ں�̨���ɾ�̬�����Ϊadmina.php
			$csname = empty(cls_uso::$init_cfg['csname']) ? 'index.php' : cls_uso::$init_cfg['csname']; 
			cls_uso::$csfull = "{$cms_abs}{$cspath}$csname?";
		} 
		$csfull = (strstr($str,'http://') ? '' : cls_uso::$csfull)."$str"; 
		$csfull = str_replace('?&','?',$csfull); 
		//$csfull = cls_env::repGlobalURL($csfull); //�ú��Ĵ���
		$fkw = array('searchword=','orderby=','ordermode='); //��Щ�ؼ��֣�������α��̬
		$fkn = 0; foreach($fkw as $k) strstr($str,$k) && $fkn++;
		if($dynamic || $fkn){
			return $csfull;
		}else{ //cls_url::en_virtual($url);cls_url::view_url($url);
			return cls_url::en_virtual($csfull);	
		}
	}
	
	// �ֶ���Ŀurl
	static function field_urls($key){ 
		$field = cls_cache::Read((cls_uso::$fdata=='m' ? 'm' : '').'field', cls_uso::$init_cfg['chid'], $key);
		$arr = cls_field::options($field);
		$cache = array(); 
		$usearch = preg_replace("/&(?:$key)=[^&]*|\b(?:$key)=[^&]*&?/", '', cls_uso::$urls['filsearch']);
		foreach($arr as $k => $v){
			if(empty($k)) continue;
			$cache[$k] = array(
				'title' => $v,
				'url' => cls_uso::format_url("$usearch&$key=$k".cls_uso::$addstr),
			);
		}
		return $cache;
	}
	
	/* ��ϵ�ڵ� url
	  $coid ��ϵID��0Ϊ��Ŀ
	  $pid �������࣬Ĭ��Ϊ����; -1Ϊ����
	*/
	static function caco_urls($coid, $pid=0, $ext=''){ 
		$_da = cls_Parse::Get('_da'); 
		$key = empty($coid) ? 'caid' : "ccid$coid"; 
		$caco = $cbak = empty($coid) ? cls_cache::Read('catalogs') : cls_cache::Read('coclasses', $coid); 
		// cls_uso::$rids_cfg[$coid1] = $coid; // $rids_cfg[2]=1; ��Ȧ<=-����
		// $rarr["ccid{$coid}_$k"] = $v; // ccid20_1296=11,22,33
		if(isset(cls_uso::$rids_cfg[$coid])){ // ��[��Ȧ]Ҫ������� $rids_cfg[2]=1; ��Ȧ<=-����
			$rpcoid = cls_uso::$rids_cfg[$coid];  
			$rpkey = empty($rpcoid) ? 'caid' : "ccid$rpcoid"; 
			$rpid = @$_da[$rpkey]; // ������[ccid1]��ֵ
			$relids = ','.@cls_uso::$rids_data["ccid{$rpcoid}_$rpid"].','; 
			foreach($caco as $k=>$v){
				if(!strstr($relids,",$k,")){
					unset($caco[$k]);
				}
			}
		}else{  
			if($pid!==-1){ //����
				foreach($caco as $k=>$v){ 
					if($pid!=$v['pid']){
						unset($caco[$k]);
					}
				}
			}
		} //print_r(cls_uso::$rids_cfg); //Array ( [2] => 1 [14] => 3 )
		cls_uso::caco_url_ext($caco, $coid, $pid, $ext); // ��ϵͳ��չ����: �������ô����? Ϊ����ô����? 
		$cache = array(); 
		$clrkey = $key; // (�����Ҫ)���������ϵ, �磺ѡ[����]����[��Ȧ]
		if(!empty(cls_uso::$rids_cfg) && in_array($coid,cls_uso::$rids_cfg)){
			foreach(cls_uso::$rids_cfg as $k=>$v){
				if($v==$coid){
					$clrkey = "$key|ccid$k"; 
					break;	
				}
			}
		} //ע��,��[���������ϵ]���������Ŀ����,û������,��Ҫ���ٲ�������
		$usearch = preg_replace("/&(?:$clrkey)=[^&]*|\b(?:$clrkey)=[^&]*&?/", '', cls_uso::$urls['filsearch']);
		foreach($caco as $k=>$v){ 
			if($cnstr = cls_uso::node_check("$usearch&$key=$k")){ 
				$url = $cnstr; 
			}else{ 
				$url = cls_uso::format_url("$usearch&$key=$k".cls_uso::$addstr);
			}
			$cache[$k] = array(
				'title' => $v['title'],
				'url' => $url, 
			);	
		}
		return $cache;
	}
	
	// ��ϵͳ��չ����: �������ô����? Ϊ����ô����? �Ƿ��·�����Ƽ����?
	static function caco_url_ext(&$caco, $coid, $pid=0, $ext=''){
		
	}

	/*
	���������Ľڵ���HTML����Ҫ�������Ÿ�
		$title ��ʾ����
		$field ��Ŀ����ϵID���ֶ�����
		$value ��ǰֵ
		$rid ��ϵID����δʵ��
	*/
	static function fliter_html($title, $field, $value, $rid = 0){
		if(is_numeric($field)){
			$rows = cls_uso::caco_urls($field, $rid);
			$field = $field ? "ccid$field" : 'caid';
		}else{
			$rows = cls_uso::field_urls($field);
		}
		$current = $value ? '' : ' class="current"';
		$dhmtl = "\n<dl><dt>{$title}��</dt><dd><ul>";
		$dhmtl .= "\n<li$current><a href='".cls_uso::extra_url($field)."'>����</a></li>";
		foreach($rows as $k => $v){
			$current = $k == $value ? ' class="current"' : '';
			$dhmtl .= "\n<li$current><a href=\"$v[url]\">$v[title]</a></li>";
		}
		$dhmtl .= "\n</ul></dd></dl>";
		echo $dhmtl;	
	}

}

// ģ���Զ���Ԫ����ʾ(����)
class cls_uviewbase{
	// �ֶ�ֵ��Ӧ�ı���ֵ
	// $ids: ԭֵ, ������[,]�ŷֿ�,Ҳ����[tab��]
	// $field: 0/caid-��Ŀ, ����-��ϵ, �ַ���-�ֶ�
	// $chid: 2-�ĵ�ģ��2, m2-��Աģ��2, cu9-����ģ��9 
	// $null: Ϊ��ʱ�ķ���ֵ; ��Ϊ����<span class='(value)'>(title)</span>����ʾģ��
	// Demo : cls_uview::field_value($tslp, 'tslp', 4, "<span class='ts_(value)'>(title)</span>");
	//   -=>  <span class='ts_2'>С����Ͷ�ʵز�</span><span class='ts_3'>�����ز�</span><span class='ts_4'>���εز�</span>
	static function field_value($ids, $field=0, $chid=0, $null=''){
		if(empty($ids)) return $null;
		//$ids = is_array($ids) ? implode(',',$ids) : $ids; echo $ids;
		$ids = explode(',', str_replace(array(", ","\t",",,"),',',$ids)); 
		if(empty($field) || $field=='caid'){
			$arr = cls_cache::Read('catalogs'); 
		}elseif(is_numeric($field)){
			$arr = cls_cache::Read('coclasses', $field); 
		}else{ //�ֶ�
			if(preg_match('/^cu\d{1,6}$/i',$chid)){ //��Աģ���ֶ�
				$chid = str_replace(array('cu','CU'),'',$chid); 
				$field = cls_cache::Read('cufield', $chid, $field);
            }elseif(preg_match('/^m\d{1,6}$/i',$chid)){ //��Աģ���ֶ�
				$chid = str_replace(array('M','m'),'',$chid); 
				$field = cls_cache::Read('mfield', $chid, $field); 
			}else{
				$_da = cls_Parse::Get('_da');
				$chid = (empty($chid) && !empty($_da['chid'])) ? $_da['chid'] : $chid;
				$field = cls_cache::Read('field', $chid, $field); 
			} 
			$arr = cls_field::options($field); 
		} 
		$re = '';
		if(strpos($null,'</') && strpos($null,'>')){ //����<span class='(value)'>(title)</span>����ʾģ��
			$tpl = $null;
			$null = '';
		}else{
			$tpl = '';
		}
		foreach($ids as $k){
			if(isset($arr[$k])){ 
				$v = $arr[$k]; 
				$title = is_array($v) ? $v['title'] : $v;
				if($tpl){
					$itm = str_replace(array('(value)','(title)'),array($k,$title),$tpl);
					$re .= $itm;	
				}else{
					$re .= (empty($re) ? '' : ', ').$title;
				}
			}
		}
		return empty($re) ? $null : $re;
	}		
	
	// ����ͳ�� : $cuid : ����ID(42=�Ź�����)
	// aid,$ext=array('checked'=>1,'tocid'=>0,),
	static function commu_count($cuid,$aid=0,$ext=array()){
		$db = _08_factory::getDBO();
		$tblprefix = cls_env::getBaseIncConfigs('tblprefix');
		$_da = cls_Parse::Get('_da');
		$aid = empty($aid) ? 0 : max(0,intval($aid));
		$commu = cls_cache::Read('commu',$cuid);
		$sql = "SELECT COUNT(*) AS count FROM {$tblprefix}$commu[tbl] WHERE 1=1 ";
		$aid && $sql .= " AND aid='$aid' ";
		if(!empty($ext)){
			foreach($ext as $k=>$v){
				if(strstr('<>',substr($v,0,1))){
					// ����>=6, <=6, >6, <6 �ȸ�ʽ
					$v = "'$v'"; $v = str_replace(array("'>=","'<=","'>","'<",),array(">='","<='",">'","<'",),$v);
					$sql .= " AND $k$v ";	
				}else{
					$sql .= " AND $k='$v' ";	
				}
			}
		}
		$cnt = $db->result_one($sql); if(!$cnt) $cnt = 0;
		return $cnt;
	}
	
	// �ı���ҳ������
	// $option: ��ʾģ��,��Ϊoption�����html����, ����:[$url]','[$n]','[$title]','[$css]'ռλ��
	//          cls_utag::TextMpNav('option');
	//          cls_utag::TextMpNav('<li><a href="[$url]" title="[$title]" [$css]>��[$n]ҳ��[$title]</a></li>','class="act"');
	// $nowcss: ��ǰҳcss����:'class="act"'��Ĭ�ϣ�
	// $elsecss:����ҳcss����:'class="gray"'��Ĭ��Ϊ�գ�
	static function TextMpNav($tpl='option',$nowcss='',$elsecss=''){
		$mp = cls_Parse::Get('_mp'); 
		if($tpl=='option'){
			$nowcss || $nowcss = 'selected="selected"';
			$tpl = '<option value="[$url]" [$css]>��[$n]ҳ��[$title]</option>';
		}else{
			$nowcss || $nowcss = 'class="act"';	
		}
		$subject = cls_Parse::Get('_da.subject'); 
		$re = ''; 
		foreach($mp['titles'] as $k => $v){ //echo "\n:::$k"; if($k==$mp['mppage']) echo " --- $k) ";
			$title = $v ? $v : $subject;
			$url = $mp['mpurls'][$k];
			$icss = $k==$mp['mppage'] ? $nowcss : $elsecss; //nowpage,mppage
			$istr = str_replace(array('[$url]','[$n]','[$title]','[$css]'),array($url,$k,$title,$icss),$tpl);
			$re .= "\n$istr";
		}
		return $re;
	}
	
	// ��ҳ��ǩ�У����۵Ƚ�����ʾ[-N¥-]
	// rowid:$v['sn_row']
	//       cls_utag::CommuFloor($v['sn_row']);
	static function CommuFloor($rowid=0){
		$mp = cls_Parse::Get('_mp'); 
		$floor = $mp['mpacount']-($mp['mppage']-1)*$mp['limits']-($rowid-1);
		// nowpage/mppage
		return $floor;
	}
	
	// ģ������ʾ�ֶ�html�� ��Ҫ����: include_once M_ROOT."./include/adminm.fun.php";
	static function form_item($cfg,$val='',$fmdata='fmdata'){
		$a_field = new cls_field;
		$a_field->init($cfg,$val); 
		$varr = $a_field->varr('fmdata','addtitle');
		unset($a_field); 
		return @$varr['frmcell'];
	}
	
	// ģ������ʾͼƬ�ϴ���button
	static function form_btn_file($cfg,$val='',$custom=array()){
		$field = cls_field::getDecorator($cfg,$val);			
		$field->trfield('fmdata',$custom);			
	}
	//��Ŀ����option����
	static function form_opt_coid($coid,$val=''){
		$dt_arr = cls_catalog::uccidsarr($coid);
		$opts = umakeoption($dt_arr,$val);
		return $opts;
	}
	
	//�ֶα���option����
	static function form_opt_field($cfg,$val=''){
		$opts = cls_uview::form_item($cfg,$val);
		return strip_tags($opts,'<option>');
    }
	
	//����ͼ�� die(cls_uview::deskIcon()); (��die�������ģʽ�����<!-- tplname : xxx.html -->)
	static function deskIcon($head=1){	
		$hostname = str_replace(array(' '),'',cls_env::mconfig('hostname')); //�ļ��������������ַ�???
		$hostname = cls_string::iconv(cls_env::getBaseIncConfigs('mcharset'),'gbk',$hostname);
		// Windows��,�ļ�������gbk; ����ĳЩie�汾�»�������
		$cms_abs = cls_env::mconfig('cms_abs');
		$Shortcut = "[InternetShortcut]";
		$Shortcut .= "\nURL={$cms_abs}";
		$Shortcut .= "\nIDList=";
		$Shortcut .= "\nIconFile={$cms_abs}favicon.ico";
		$Shortcut .= "\nIconIndex=100";
		$Shortcut .= "\n[{000214A0-0000-0000-C000-000000000046}]";
		$Shortcut .= "\nProp3=19,2";
		if($head){
			header("Content-type: application/octet-stream"); 
			header("Content-Disposition: attachment; filename=$hostname.url;");  
		}
		return $Shortcut;
    }
		
}

// ģ����sql(����)
class cls_usqlbase{
	
	// ������õ�orderby�ֶΣ�
	static function order_bys($ordbys=array()){
		$orderby = cls_Parse::Get('_da.orderby');
		$order_bys = empty($ordbys) ? @cls_uso::$init_cfg['orderbys'] : $ordbys;
		if($orderby && $order_bys){
			if(!in_array($orderby,$order_bys)) cls_Parse::Message("[$orderby]�����������!");	
		}
	}
	
	/* �����ǩ�б������orderstr
	  $fixarr   : ָ��ǰ׺
	  $deforder : Ĭ������
	  $rearray  : ��������,extract(cls_uso::order_str());
	  $_ajda    : ����ģ���е��ã���ajax��������cls_Parse::Get��������������_da��
	*/
	static function order_str($rearray='0',$deforder='',$fixarr=array(),$_ajda=array()){ //,$cfgs=array()
		foreach(array('orderby','ordermode') as $k){ 
			$$k = isset($_ajda[$k]) ? $_ajda[$k] :  cls_Parse::Get("_da.$k"); 
		}
		//if(!empty($cfgs)) extract($cfgs);
		$fdata = isset($_ajda['aj_fdata']) ? $_ajda['aj_fdata'] : cls_uso::$fdata;
		$deffix = $fdata=='m' ? 'm.' : 'a.'; //��ʱֻ�����ĵ���Ա
		if($orderby){
			$nowfix = isset($fixarr[$orderby]) ? $fixarr[$orderby] : $deffix; 
			$orderstr = (in_array($orderby,array('aid','mid')) ? $deffix : '').$orderby;
			$orderstr = $orderstr.($ordermode ? '' : ' DESC');
		}else{
			$orderstr = $deforder;	
		}
		if($rearray){ //��������
			$re = array(); 
			foreach(array('orderby','ordermode','orderstr') as $k) $re[$k] = $$k;
			return $re;
		}else{
			return $orderstr;	
		}
	}
	
	/*/ ����wherestr, ����field�Ѿ�ָ�� 
	//  $cfgs = array( // ��ʽ: array('field:','key','op','fmt'),
		
			array('subject,address','searchword','like','str'), // -=> ��Ϊ array('subject,address'),
			array('leixing','0','=','int'),                     // -=> ��Ϊ array('leixing'),
			array('company',0,'like'),                          // -=> ��Ϊ array('company'),
			array('caid','caidx1','in','0'),                    // -=> caid IN(1,2,3)
			array('ccid4','ccid4','atuo',4),                    // -=> �Զ���ϵ��a.mj>0 AND a.mj<50
			array('ccid1','ccid1','in',1),                      // -=> array('ccid1',0,'in',1), // ccid1 IN(26,146,148,1...7,4303,4308) 
			array('mianccid1','ccid1','inlike','1'),            // -=> CONCAT(',',mianccid1,',') LIKE '%\t$ccid1\t%' OR (...)
			array('fromaddress','ccid20'), 
			array('grouptype37','ugid37'),
			array('company','searchword'),
			array('ccid61',0,'auto'),                            //-=> ������ϵ

		)
		field:�ֶ���, �� subject,address �� subject �� leixing ��, �ɴ�ǰ׺
		key:url����,  �� searchword �� 0 �� '', ���Ϊ�������ֶ���ͬ����searchword
		op:����,      �� like �� auto(�Զ���ϵ) �� = �� < �� <= �� inlike(��ϵ��ѡ) �� mso1(��ѡ�ֶ�) ��, ���Ϊ����Ϊlike��=
		fmt:��������, �� int �� str �� 0 �� coid ��, ���opΪin,inlikeʱ,fmtΪ0��coid, ���Ϊ����[op]ΪlikeʱfmtΪstr
		$exstr        �� "leixing IN(0,1)"
		$_ajda        ����ģ���е��ã���ajax��������cls_Parse::Get��������������_da��
	*/
	static function where_str($cfgs=array(array('subject')),$exstr='',$_ajda=array()){
		$re = ''; //$this->_get['ids']
		foreach($cfgs as $cfg){ 
			if(!$fields = @$cfg[0]) continue;
			$flag = in_array($fields,array('subject','company')) || strstr($fields,','); //�����ж�
			if(!$key = @$cfg[1]){
				if($flag) $key = $fields=='company' ? 'company' : 'searchword';
				else $key = $fields;
			} 
			$val = isset($_ajda[$key]) ? $_ajda[$key] : cls_Parse::Get("_da.$key"); if(!$val) continue;   
			$op = empty($cfg[2]) ? ($flag ? 'like' : '=') : $cfg[2]; 
			$fmt = empty($cfg[3]) ? (($flag || $op=='like') ? 'str' : 'int') : $cfg[3]; 
			$val = $fmt=='int' ? intval($val) : $val; //$val��_da���Ѿ�ת���\'
			$field_arr = explode(',',$fields);  
			$istr = ''; $ior = 0; //or���
			foreach($field_arr as $field){ 
				if($op=='like'){ //�ؼ���
					$itmp = "$field ".sqlkw($val);
				}elseif($op=='auto'){ // �Զ�������ϵ
					$itmp = cnsql($cfg[3],$val); 
				}elseif(in_array($op,array('>','>=','<','<='))){ //���ֱȽ�
					$itmp = "$field$op'$val'";
				}elseif(in_array($op,array('notnull','isnull'))){ // field!='' �� field=''
					$itmp = "$field".($_iop=='isnull' ? "=" : "!=")."''";
				}elseif($op=='in'){ //����,�Է���; ��sonbycoid()����Ŀ/��Ŀ, caid IN(sonbycoid($caidx1))
					$fmt = empty($cfg[3]) ? 0 : $cfg[3]; 
					$ids = sonbycoid($val, $fmt, 1); 
					if($ids){
						$itmp = "$field IN(".implode(',',$ids).")";
					}else{
						$itmp = '';	
					}
				}elseif($op=='inlike'){ //��ѡ�ֶ�,�������ӷ���, CONCAT(',',mianccid1,',') LIKE '%\t$ccid1\t%' OR (...)
					$fmt = empty($cfg[3]) ? 0 : $cfg[3]; 
					$ids = sonbycoid($val, $fmt, 1); $itmp = ''; 
					if($ids){
						foreach($ids as $id){
							$itmp .= (empty($itmp) ? '' : ' OR ')."CONCAT(',',$field,',') LIKE '%,$id,%'";	
						}
					}
					if(strstr($itmp,"' OR CONCAT('")) $ior = 1;
				}elseif(in_array($op,array('mso1'))){ //��ѡ�ֶ���1��([tab��]�ֿ�)
					$itmp = "CONCAT('\t',$field,'\t') LIKE '%\t$val\t%'";
				}else{ //���������?! 
					$itmp = "$field='$val'";
				}
				if($itmp && $istr){
					$istr .= " OR $itmp"; //��������ֶ�,count($field_arr)>1
					$ior = 1; //or���
				}elseif($itmp){
					$istr = "$itmp";	
				}
			}
			$istr = $ior ? "($istr)" : $istr; //��orҪ������
			if($istr){
				$re .= (empty($re) ? '' : ' AND ')."$istr";
			}
		}
		if($exstr){
			$re .= (empty($re) ? '' : ' AND ')."$exstr";
		}
		return $re;
	}
	
	// �ĵ� ��ͨ�б�/�����б� ������� ��sql:
	static function sql_arc($extcond='',$skip=array()){
		$_da = cls_Parse::Get('_da'); //print_r($_da);
		$whrstr = $extcond ? " AND $extcond" : "";
		if(!empty($_da['wherearr'])){
			$whrarr = $_da['wherearr'];	
			foreach(array('checked','caid','chid') as $k) unset($whrarr[$k]); //�̶�����
			if(!empty($skip)) foreach($skip as $k) unset($whrarr[$k]); //�Զ������
			if(!empty($whrarr)){
				foreach($whrarr as $k=>$v){
					if(substr($k,0,4)=='ccid') continue; //��ϵ����
					$whrstr .= " AND $v";
				}
			}
		} 
		if($whrstr) $whrstr = substr($whrstr,5); 
		return $whrstr;
	}
	
	// �ĵ� ��ͨ�б�/�����б� ������� ��sql:
	static function sql_mem($extcond='',$skip=array()){
		$_da = cls_Parse::Get('_da');
		$whrstr = $extcond ? " AND $extcond" : "";
		if(!empty($_da['wherearr'])){
			$whrarr = $_da['wherearr'];	
			foreach(array('mchid') as $k) unset($whrarr[$k]); //�̶�����,'caid','checked',
			if(!empty($skip)) foreach($skip as $k) unset($whrarr[$k]); //�Զ������
			if(!empty($whrarr)){
				foreach($whrarr as $k=>$v){
					//if(substr($k,0,4)=='ccid') continue; //��ϵ����
					$whrstr .= " AND $v";
				}
			}
		} 
		if($whrstr) $whrstr = substr($whrstr,5); //echo "<br>1.$whrstr";
		return $whrstr;
	}
	
}
