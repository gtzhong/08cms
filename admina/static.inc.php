<?php
(!defined('M_COM') || !defined('M_ADMIN')) && exit('No Permission');
aheader();
if($re = $curuser->NoBackFunc('static')) cls_message::show($re);
foreach(array('cotypes','channels','currencys','permissions','cntpls','mcntpls','catalogs','mtpls','cnodes','splitbls','static_process','grouptypes') as $k) $$k = cls_cache::Read($k);
$transtr = empty($transtr) ? '' : $transtr;
if($action == 'index'){
	backnav('static','index');
	if(!submitcheck('bstatic') && !submitcheck('bclear')){
		tabheader('��ҳ��̬','staticindex',"?entry=$entry&action=$action");
		$ptypearr = array('i' => 'ϵͳ��ҳ','m' => '��ԱƵ����ҳ');
		trbasic('ѡ����ҳ����','',makecheckbox('ptypes[]',$ptypearr,array('i','m')),'');
		$indexfile = M_ROOT.cls_url::m_parseurl(idx_format(),array('page' => 1));
		$str = ($fm = @filemtime($indexfile)) ? '��̬���£�'.date('Y-m-d H:i',$fm) : '��δ���ɾ�̬';
		$str .= " &nbsp;<a href=\"$cms_abs\" target=\"_blank\">>>���</a>";
		trbasic('ϵͳ��ҳ״̬','',$str,'');
		$indexfile = M_ROOT.cls_url::m_parseurl(cls_node::mcn_format(),array('page' => 1));
		$str = ($fm = @filemtime($indexfile)) ? '��̬���£�'.date('Y-m-d H:i',$fm) : '��δ���ɾ�̬';
		$str .= " &nbsp;<a href=\"$memberurl\" target=\"_blank\">>>���</a> &nbsp;";
		trbasic('��ԱƵ����ҳ','',$str,'');
		tabfooter();
		echo "<input class=\"button\" type=\"submit\" name=\"bstatic\" value=\"���ɾ�̬\"> &nbsp; &nbsp;";
		echo "<input class=\"button\" type=\"submit\" name=\"bclear\" value=\"�����̬\"> &nbsp; &nbsp;";
	}elseif(submitcheck('bstatic')){
		if(empty($ptypes)) cls_message::show('��ѡ����ҳ���ͣ�',"?entry=$entry&action=$action");
		$msg = '';
		if(in_array('i',$ptypes)) $msg .= '<br>ϵͳ��ҳ��'.cls_CnodePage::Create(array('inStatic' => true));
		if(in_array('m',$ptypes)) $msg .= '<br>��ԱƵ����ҳ��'.cls_McnodePage::Create(array('inStatic' => true));
		adminlog('������ҳ��̬');
		cls_message::show($msg ? $msg : 'δִ���κβ�����',"?entry=$entry&action=$action");
	}elseif(submitcheck('bclear')){
		if(empty($ptypes)) cls_message::show('��ѡ����ҳ���ͣ�',"?entry=$entry&action=$action");
		$msg = '';
		if(in_array('i',$ptypes)) $msg .= '<br>ϵͳ��ҳ��'.cls_cnode::UnStaticIndex();
		if(in_array('m',$ptypes)) $msg .= '<br>��ԱƵ����ҳ��'.cls_mcnode::UnStaticIndex();
		adminlog('�����ҳ��̬');
		cls_message::show($msg ? $msg : 'δִ���κβ�����',"?entry=$entry&action=$action");
	}
}elseif($action == 'archives') {
	backnav('static','archives');
	if(empty($enablestatic)) cls_message::show('��̬ģʽδ����');
	$nsplitbls = array();
	foreach($splitbls as $k => $v) empty($v['nostatic']) && $nsplitbls[$k] = $v;
	if(!($stid = empty($stid) ? first_id($nsplitbls) : $stid) || empty($nsplitbls[$stid])) cls_message::show('��ָ���ĵ�����');
	$pagefrom = empty($pagefrom) ? 0 : max(0,intval($pagefrom));
	$pageto = empty($pageto) ? 0 : max(0,intval($pageto));
	$aidfrom = empty($aidfrom) ? 0 : max(0,intval($aidfrom));
	$aidto = empty($aidto) ? 0 : max(0,intval($aidto));
	$debugmode = empty($debugmode) ? 0 : 1;
	$numperpic = empty($numperpic) ? 20 : min(50,max(10,intval($numperpic)));
	$caid = empty($caid) ? '0' : $caid;
	$kpmode = empty($kpmode) ? '0' : $kpmode;
	$indays = empty($indays) ? 0 : max(0,intval($indays));
	$outdays = empty($outdays) ? 0 : max(0,intval($outdays));

	$ntbl = atbl($stid,1);
	$fromsql = "FROM {$tblprefix}$ntbl a";
	$wheresql = "WHERE a.checked='1'";
	if(!empty($caid)){
		if($cnsql = cnsql(0,sonbycoid($caid),'a.')) $wheresql .= " AND $cnsql";
	}
	$indays && $wheresql .= " AND a.createdate>'".($timestamp - 86400 * $indays)."'";
	$outdays && $wheresql .= " AND a.createdate<'".($timestamp - 86400 * $outdays)."'";
	$aidfrom && $wheresql .= " AND a.aid>='$aidfrom'";
	$aidto && $wheresql .= " AND a.aid<='$aidto'";
	$filterstr = '';
	foreach(array('kpmode','pagefrom','pageto','aidfrom','aidto','debugmode','numperpic','caid','stid','indays','outdays',) as $k){
		$filterstr .= "&$k=".rawurlencode($$k);
	}
	$_total = $db->result_one("SELECT count(*) $fromsql $wheresql");
	$_pics = @ceil($_total / $numperpic);
	if(!submitcheck('bsubmit')){
		tabheader("ɸѡ�ĵ�&nbsp; &nbsp; >><a href=\"?entry=$entry&action=archivesurl\" onclick=\"return floatwin('open_staticurl',this)\">�����޸�����</a>",'archives',"?entry=$entry&action=$action");
		$stidsarr = array();foreach($nsplitbls as $k => $v) $stidsarr[$k] = $v['cname'];
		trbasic('�ĵ�����','',makeradio('stid',$stidsarr,$stid,8),'');
		trrange('�ĵ�id��Χ',array('aidfrom',$aidfrom,'',' - ',8),array('aidto',$aidto,'','',8),'text');
		trrange('�������',array('outdays',empty($outdays) ? '' : $outdays,'','&nbsp; ��ǰ'.'&nbsp; -&nbsp; ',5),array('indays',empty($indays) ? '' : $indays,'','&nbsp; ����',5));
		tr_cns('������Ŀ','caid',array('value' => $caid,'framein' => 1,));
		trbasic('�ĵ�����/����','numperpic',$numperpic,'text',array('guide' => '��ѡ��Χ10-50��������Ϊ20�������ν��Զ�����ִ�С�','w' => 5));
		tabfooter();
		echo "<input class=\"button\" type=\"submit\" name=\"bfilter\" value=\"ɸѡ\"> &nbsp; &nbsp;";

		tabheader("����ҳ��̬ (��{$_total}�� {$_pics}��)");
		$kpmodearr = array('1' => '����ԭURL','0' => '���¹������URL');
		trbasic('URL����ʽ','',makeradio('kpmode',$kpmodearr,$kpmode),'',array('guide' => '��Ӫ��ϵͳ���鱣��ԭURL(��֮ǰδ��̬���ĵ���ʹ���¹���)��������ϵͳ����ʹ�ð��¹������'));
		trrange('���η�Χѡ��<br>�� '.$_pics.' ��',array('pagefrom',$pagefrom ? $pagefrom : '','',' - ',5),array('pageto',$pageto ? $pageto : '','','',5),'text','����ʾ�����ӵ�2������5��������Ϊ���ޡ�');
		trbasic('���öϵ����','debugmode',$debugmode,'radio',array('guide' => '����鿴ÿ���εľ�̬����״���������β��Զ�����ִ��'));

		tabfooter('bsubmit','ִ��');
	#	a_guide('staticarchives');

	}else{
		$npage = empty($npage) ? 1 : $npage;
		if(empty($pages)) $pages = $_pics;
		if(empty($pages)) cls_message::show('��ѡ���ĵ�',"?entry=$entry&action=$action$filterstr");
		if($pagefrom && $pageto && $pageto < $pagefrom) cls_message::show('ҳ�뷶Χ��������',"?entry=$entry&action=$action$filterstr");
		$pagefrom && $npage = max($npage,$pagefrom);
		$pageto && $pages = min($pages,$pageto);
		$npage = min($npage,$pages);

		$selectid = array();
		$fromstr = empty($fromid) ? "" : "a.aid<$fromid";
		$offsetfrom = empty($fromid) ? ($pagefrom ? ($pagefrom-1)*$numperpic : 0) : 0;
		$nwheresql = !$wheresql ? ($fromstr ? "WHERE $fromstr" : "") : ($wheresql.($fromstr ? " AND " : "").$fromstr);
		$query = $db->query("SELECT aid $fromsql $nwheresql ORDER BY a.aid DESC LIMIT $offsetfrom,$numperpic");
		while($item = $db->fetch_array($query)) $selectid[] = $item['aid'];

		$initno = ($npage - 1) * $numperpic;
		$str = "<br><b>�ĵ�����ҳ��̬:</b> $numperpic/�� �� $npage �� �� $pages �� &nbsp;>><a href=\"?entry=$entry&action=$action$filterstr\">����</a>";

		$nextpage  = $npage + 1;
		$nexturl = "?entry=$entry&action=$action$filterstr$transtr&pages=$pages&npage=$nextpage&bsubmit=1&fromid=".min($selectid);
		$thisurl = "?entry=$entry&action=$action$filterstr$transtr&pages=$pages&npage=$npage&bsubmit=1&fromid=".(empty($fromid) ? 0 : $fromid);
		if($debugmode){
			$str .= " &nbsp;>><a href=\"$thisurl\">����</a>";
			if($nextpage <= $pages) $str .= " &nbsp;>><a href=\"$nexturl\">��һ��</a>";
		}
		static_process('body',$str);

		$arc = new cls_arcedit;
		foreach($selectid as $aid){
			$initno ++;
			$arc->set_aid($aid,array('au'=>0));
			for($k = 0;$k <= @$arc->arc_tpl['addnum'];$k++){
				$re = $arc->tostatic($k,$kpmode);
				static_process('msg',str_pad($k ? '' : $initno,$k ? 10+strlen($initno) : 10,'.').'aid:'.str_pad($aid,10,'.').'��'.$k.'.....'.$re);
			}
		}
		unset($arc);

		if($debugmode){
			exit();
		}else{
			if($nextpage <= $pages) static_process('jump',$nexturl);
			static_process('hide');
			adminlog('�ĵ���̬����','�ĵ��б�������');
			cls_message::show('��̬�������',"?entry=$entry&action=$action$filterstr");
		}
	}
}elseif($action == 'archivesurl') {
	echo "<title>�����޸��ĵ���̬����</title>";
	if(empty($enablestatic)) cls_message::show('��̬ģʽδ����');
	$stidsarr = array();
	foreach($splitbls as $k => $v){
		if(empty($v['nostatic'])) $stidsarr[$k] = $v['cname'];
	}
	if(empty($stidsarr)) cls_message::show('���е��ĵ����Ͷ�����Ҫ��̬');	
	
	if(!submitcheck('bsubmit')){
		tabheader("�����޸��ĵ���̬����",'archives',"?entry=$entry&action=$action");
		trbasic('ѡ���ĵ�����<br><input class="checkbox" type="checkbox" name="chkall" onclick="checkall(this.form,\'stids\',\'chkall\')">ȫѡ','',makecheckbox('stids[]',$stidsarr,array_keys($stidsarr),8),'');
		trbasic('URL�޸���ʽ','',makeradio('kpmode',array(0 => '����ԭURL',1 => '���¹������URL')),'');
		tabfooter('bsubmit','ִ��');
		a_guide('<li>ͨ��ϵͳ�¿�����̬ģʽʱ����δ������̬������£�ǰ̨������Ҳ����ļ������(400����)
		<li>��ͨ�������޸����ӣ�ʹǰ̨�ɰ���̬URL��������ҳ��',true);
	}else{
		if(!empty($stids)){
			if(!is_array($stids)){
				$stids = explode(',',$stids);
			}
			$stids = array_filter($stids);
		}
		if(empty($stids)) cls_message::show('�����޸���ϡ�',"?entry=$entry&action=$action");
		$page = empty($page) ? 1 : max(1,intval($page));
		$kpmode = empty($kpmode) ? 0 : 1;
		$ostids = $stids;
		
		if($stid = array_shift($stids)){
			$_keepid = _archive_url($stid,$page,$kpmode);
		}
		if($_keepid){//������ͬ�����ĵ�($stid���ֲ���)
			$stids = $ostids;
			$page ++;
		}else{//�л�����һ�������ĵ�($stid��Ҫ�仯)
			$page = 1;
		}
		if(empty($stids)){
			cls_message::show('�����޸���ϡ�',"?entry=$entry&action=$action");
		}else{
			$num = count($stids);
			$stids = implode(',',$stids);
			cls_message::show("����ִ�� {$splitbls[$stid]['cname']} �ĵ� <b>{$page}</b> ҳ��<br>���� <b>{$num}</b> �����͵��ĵ���Ҫ�޸��������ĵȴ���","?entry=$entry&action=$action&stids=$stids&page=$page&kpmode=$kpmode&bsubmit=1");
		}
	}
}elseif($action == 'cnodes'){
	backnav('static','cnodes');
	if(empty($enablestatic)) cls_message::show('��̬ģʽδ����');
	$pagefrom = empty($pagefrom) ? 0 : max(0,intval($pagefrom));
	$pageto = empty($pageto) ? 0 : max(0,intval($pageto));
	$debugmode = empty($debugmode) ? 0 : 1;
	$numperpic = empty($numperpic) ? 20 : min(50,max(10,intval($numperpic)));
	$caid = !isset($caid)? '0' : max(-1,intval($caid));
	$cnlevel = max(0,intval(@$cnlevel));
	$tid = !isset($tid)? 0 : max(0, intval($tid));
	$viewdetail = empty($viewdetail) ? '0' : $viewdetail;

	$fromsql = "FROM {$tblprefix}cnodes";
	$wheresql = " WHERE closed=0";
	$cnlevel && $wheresql .= " AND cnlevel='$cnlevel'";
	$tid && $wheresql .= " AND tid='$tid'";
	if(!empty($caid)){
		if($caid == -1){
			$wheresql .= " AND caid<>0";
		}else $wheresql .= " AND caid ".multi_str(sonbycoid($caid));
	}
	$filterstr = '';
	foreach(array('pagefrom','pageto','debugmode','numperpic','caid','cnlevel','tid','viewdetail',) as $k) $filterstr .= "&$k=".rawurlencode($$k);
	foreach($cotypes as $k => $v){
		if($v['sortable']){
			${"ccid$k"} = isset(${"ccid$k"}) ? max(-1,intval(${"ccid$k"})) : 0;
			if(!empty(${"ccid$k"})){
				if(${"ccid$k"} == -1){
					$wheresql .= " AND ccid$k<>0";
				}else{
					$wheresql .= " AND ccid$k ".multi_str(sonbycoid(${"ccid$k"},$k));
				}
				${"ccid$k"} && $filterstr .= "&ccid$k=".${"ccid$k"};
			}
		}
	}
	$_total = $db->result_one("SELECT count(*) $fromsql $wheresql");
	$_pics = @ceil($_total / $numperpic);
	if(!submitcheck('bsubmit')){
		tabheader("ɸѡ��Ŀ�ڵ�".viewcheck(array('name' => 'viewdetail','value' =>$viewdetail,'body' =>$actionid.'tbodyfilter',))."��������&nbsp; &nbsp; >><a href=\"?entry=$entry&action=cnodesurl\" onclick=\"return floatwin('open_staticurl',this)\">�����޸�����</a>",'archives',"?entry=$entry&action=$action");
		$arr = array('0' => '����',);foreach($cntpls as $k => $v) $arr[$k] = $v['cname'];
		trbasic('�ڵ�����','tid',makeoption($arr,$tid),'select');
		trbasic('��Ŀ','caid',makeoption(array('0' => '����','-1' => 'ȫ��') + cls_catalog::ccidsarr(0),$caid),'select');
		trbasic('�ڵ㽻��','cnlevel',makeoption(array('0'=>'����','1'=>'���ؽڵ�','2'=>'˫�ؽ���','3'=>'���ؽ���','4'=>'���ؽ���'),$cnlevel),'select');
		echo "<tbody id=\"{$actionid}tbodyfilter\" style=\"display:".($viewdetail ? '' : 'none')."\">";
		foreach($cotypes as $k => $v){
			if($v['sortable']) trbasic($v['cname'],"ccid$k",makeoption(array('0' => '����','-1' => 'ȫ��') + cls_catalog::ccidsarr($k),${"ccid$k"}),'select');
		}
		echo "</tbody>";
		trbasic('�ڵ�����/����','numperpic',$numperpic,'text',array('guide' => '��ѡ��Χ10-50��ϵͳĬ��Ϊ20��','w' => 5));
		tabfooter();
		echo "<input class=\"button\" type=\"submit\" name=\"bfilter\" value=\"ɸѡ\"> &nbsp; &nbsp;";

		tabheader("��Ŀ�ڵ�ҳ��̬  ({$_total}�� {$_pics}��)");
		trrange('���η�Χѡ��<br>�� '.$_pics.' ��',array('pagefrom',$pagefrom ? $pagefrom : '','',' - ',5),array('pageto',$pageto ? $pageto : '','','',5),'text','����ʾ�����ӵ�2������5��������Ϊ���ޡ�');
		trbasic('���öϵ����','debugmode',$debugmode,'radio',array('guide' => '����鿴ÿ���εľ�̬����״���������β��Զ�����ִ��'));
		tabfooter('bsubmit','ִ��');
#		a_guide('staticcnotes');
	}else{
		$npage = empty($npage) ? 1 : $npage;
		if(empty($pages)) $pages = @ceil($_total / $numperpic);
		if(empty($pages)) cls_message::show('��ѡ����Ŀ�ڵ�',"?entry=$entry&action=$action$filterstr");
		if($pagefrom && $pageto && $pageto < $pagefrom) cls_message::show('ҳ�뷶Χ��������',"?entry=$entry&action=$action$filterstr");
		$pagefrom && $npage = max($npage,$pagefrom);
		$pageto && $pages = min($pages,$pageto);
		$npage = min($npage,$pages);

		$cnstrarr = $selectid = array();
		$fromstr = empty($fromid) ? "" : "cnid<$fromid";
		$offsetfrom = empty($fromid) ? ($pagefrom ? ($pagefrom-1)*$numperpic : 0) : 0;
		$nwheresql = !$wheresql ? ($fromstr ? "WHERE $fromstr" : "") : ($wheresql.($fromstr ? " AND " : "").$fromstr);
		$query = $db->query("SELECT cnid,ename,tid $fromsql $nwheresql ORDER BY cnid DESC LIMIT $offsetfrom,$numperpic");
		while($item = $db->fetch_array($query)){
			$selectid[] = $item['cnid'];
			$cnstrarr[$item['ename']] = $item['tid'];
		}

		$initno = ($npage - 1) * $numperpic;
		$str = "<br><b>��Ŀ�ڵ�ҳ��̬:</b> $numperpic/�� �� $npage �� �� $pages �� &nbsp;>><a href=\"?entry=$entry&action=$action$filterstr\">����</a>";
		$nextpage  = $npage + 1;
		$nexturl = "?entry=$entry&action=$action$filterstr$transtr&pages=$pages&npage=$nextpage&bsubmit=1&fromid=".min($selectid);
		$thisurl = "?entry=$entry&action=$action$filterstr$transtr&pages=$pages&npage=$npage&bsubmit=1&fromid=".(empty($fromid) ? 0 : $fromid);
		if($debugmode){
			$str .= " &nbsp;>><a href=\"$thisurl\">����</a>";
			if($nextpage <= $pages) $str .= " &nbsp;>><a href=\"$nexturl\">��һ��</a>";
		}
		static_process('body',$str);

		foreach($cnstrarr as $cnstr => $v){
			if(!empty($cntpls[$v])){
				$addnum = empty($cntpls[$v]['addnum']) ? 0 : $cntpls[$v]['addnum'];
				for($k = 0;$k <= $addnum;$k++){
					$re = cls_CnodePage::Create(array('cnstr' => $cnstr,'addno' => $k,'inStatic' => true));
					static_process('msg',str_pad($k ? '' : $initno,$k ? 10+strlen($initno) : 10,'.').str_pad($cnstr,40,'.').'��'.$k.'.....'.$re);
				}
				$initno ++;
			}
		}
		if($debugmode){
			exit();
		}else{
			if($nextpage <= $pages) static_process('jump',$nexturl);
			static_process('hide');
			adminlog('��Ŀ�ڵ㾲̬����','�ڵ��б�������');
			cls_message::show('��Ŀ�ڵ�������',"?entry=$entry&action=$action$filterstr");
		}
	}
}elseif($action == 'cnodesurl') {
	echo "<title>�����޸��ڵ㾲̬����</title>";
	if(empty($enablestatic)) cls_message::show('��̬ģʽδ����');
	
	if(!submitcheck('bsubmit')){
		tabheader("�����޸��ڵ㾲̬����",'archives',"?entry=$entry&action=$action");
		trbasic('URL�޸���ʽ','',makeradio('kpmode',array(0 => '��ȫȱ��ҳ��',1 => 'ȫ��ҳ����д')),'');
		tabfooter('bsubmit','ִ��');
		a_guide('<li>ͨ��ϵͳ�¿�����̬ģʽʱ����δ������̬������£�ǰ̨������Ҳ����ļ������(400����)
		<li>��ͨ�������޸����ӣ�ʹǰ̨�ɰ���̬URL��������ҳ��',true);
	}else{
		$page = empty($page) ? 1 : max(1,intval($page));
		$kpmode = empty($kpmode) ? 0 : 1;
		$_continue = _nodes_url('cnodes',$page,$kpmode);
		
		if($_continue){//������һҳ
			$page ++;
			cls_message::show("����ִ�е� <b>$page</b> ҳ�������ĵȴ���","?entry=$entry&action=$action&page=$page&kpmode=$kpmode&bsubmit=1");
		}else{//ȫ�����
			cls_message::show('�����޸���ϡ�',"?entry=$entry&action=$action");
		}
	}
}elseif($action == 'mcnodes'){
	backnav('static','mcnodes');
	if(empty($enablestatic)) cls_message::show('��̬ģʽδ����');
	$pagefrom = empty($pagefrom) ? 0 : max(0,intval($pagefrom));
	$pageto = empty($pageto) ? 0 : max(0,intval($pageto));
	$debugmode = empty($debugmode) ? 0 : 1;
	$numperpic = min(500,max(20,intval(@$numperpic)));
	$mcnvar = trim(@$mcnvar);
	$tid = !isset($tid)? 0 : max(0, intval($tid));

	$fromsql = "FROM {$tblprefix}mcnodes";
	$wheresql = "WHERE closed=0";
	$mcnvar && $wheresql .= " AND mcnvar='$mcnvar'";
	$tid && $wheresql .= " AND tid='$tid'";

	$filterstr = '';
	foreach(array('pagefrom','pageto','debugmode','numperpic','mcnvar',) as $k) $filterstr .= "&$k=".rawurlencode($$k);
	$_total = $db->result_one("SELECT count(*) $fromsql $wheresql");
	$_pics = @ceil($_total / $numperpic);
	if(!submitcheck('bsubmit')){
		tabheader("ɸѡ��Ա�ڵ�&nbsp; &nbsp; >><a href=\"?entry=$entry&action=mcnodesurl\" onclick=\"return floatwin('open_staticurl',this)\">�����޸�����</a>",'archives',"?entry=$entry&action=$action");
		$arr = array('0' => '����',);foreach($mcntpls as $k => $v) $arr[$k] = $v['cname'];
		trbasic('�ڵ�����','tid',makeoption($arr,$tid),'select');
		$mcnvars = array('' => 'ȫ������','caid' => '��Ŀ');
		foreach($cotypes as $k => $v) !$v['self_reg'] && $mcnvars['ccid'.$k] = $v['cname'];
		foreach($grouptypes as $k => $v) !$v['issystem'] && $mcnvars['ugid'.$k] = $v['cname'];
		$mcnvars['mcnid'] = '�Զ���ڵ�';
		trbasic('�ڵ�����','mcnvar',makeoption($mcnvars,$mcnvar),'select');
		trbasic('�ڵ�����/����','numperpic',$numperpic,'text',array('guide' => '��ѡ��Χ10-50��ϵͳĬ��Ϊ20��','w' => 5));
		tabfooter();
		echo "<input class=\"button\" type=\"submit\" name=\"bfilter\" value=\"ɸѡ\"><br />";

		tabheader("��Ա�ڵ�ҳ��̬  ({$_total}�� {$_pics}��)");
		trrange('���η�Χѡ��<br>�� '.$_pics.' ��',array('pagefrom',$pagefrom ? $pagefrom : '','',' - ',5),array('pageto',$pageto ? $pageto : '','','',5),'text','����ʾ�����ӵ�2������5��������Ϊ���ޡ�');
		trbasic('���öϵ����','debugmode',$debugmode,'radio',array('guide' => '����鿴ÿ���εľ�̬����״���������β��Զ�����ִ��'));
		tabfooter('bsubmit','����ִ��');
	#	a_guide('staticmcnodes');
	}else{
		$npage = empty($npage) ? 1 : $npage;
		if(empty($pages)) $pages = @ceil($_total / $numperpic);
		if(empty($pages)) cls_message::show('��ѡ��ڵ�',"?entry=$entry&action=$action$filterstr");
		if($pagefrom && $pageto && $pageto < $pagefrom) cls_message::show('ҳ�뷶Χ��������',"?entry=$entry&action=$action$filterstr");
		$pagefrom && $npage = max($npage,$pagefrom);
		$pageto && $pages = min($pages,$pageto);
		$npage = min($npage,$pages);

		$selectid = $cnstrarr = array();
		$fromstr = empty($fromid) ? "" : "cnid<$fromid";
		$offsetfrom = empty($fromid) ? ($pagefrom ? ($pagefrom-1)*$numperpic : 0) : 0;
		$nwheresql = !$wheresql ? ($fromstr ? "WHERE $fromstr" : "") : ($wheresql.($fromstr ? " AND " : "").$fromstr);
		$query = $db->query("SELECT cnid,ename,tid $fromsql $nwheresql ORDER BY cnid DESC LIMIT $offsetfrom,$numperpic");
		while($item = $db->fetch_array($query)){
			$selectid[] = $item['cnid'];
			$cnstrarr[$item['ename']] = $item['tid'];
		}

		$initno = ($npage - 1) * $numperpic;
		$str = "<br><b>��Ա�ڵ�ҳ��̬:</b> $numperpic/�� �� $npage �� �� $pages �� &nbsp;>><a href=\"?entry=$entry&action=$action$filterstr\">����</a>";

		$nextpage  = $npage + 1;
		$nexturl = "?entry=$entry&action=$action$filterstr$transtr&pages=$pages&npage=$nextpage&bsubmit=1&fromid=".min($selectid);
		$thisurl = "?entry=$entry&action=$action$filterstr$transtr&pages=$pages&npage=$npage&bsubmit=1&fromid=".(empty($fromid) ? 0 : $fromid);
		if($debugmode){
			$str .= " &nbsp;>><a href=\"$thisurl\">����</a>";
			if($nextpage <= $pages) $str .= " &nbsp;>><a href=\"$nexturl\">����</a>";
		}
		static_process('body',$str);
		foreach($cnstrarr as $cnstr => $v){
			if(!empty($mcntpls[$v])){
				$addnum = empty($mcntpls[$v]['addnum']) ? 0 : $mcntpls[$v]['addnum'];
				
				for($k = 0;$k <= $addnum;$k++){
					$re = cls_McnodePage::Create(array('cnstr' => $cnstr,'addno' => $k,'inStatic' => true));
					static_process('msg',str_pad($k ? '' : $initno,$k ? 10+strlen($initno) : 10,'.').str_pad($cnstr,40,'.').'��'.$k.'.....'.$re);
				}
				$initno ++;
			}
		}
		if($debugmode){
			exit();
		}else{
			if($nextpage <= $pages) static_process('jump',$nexturl);
			static_process('hide');
			adminlog('��Ա�ڵ㾲̬����','�ڵ��б�������');
			cls_message::show('��Ա�ڵ�������',"?entry=$entry&action=$action$filterstr");
		}
	}
}elseif($action == 'mcnodesurl') {
	echo "<title>�����޸���Ա�ڵ㾲̬����</title>";
	if(empty($enablestatic)) cls_message::show('��̬ģʽδ����');
	
	if(!submitcheck('bsubmit')){
		tabheader("�����޸���Ա�ڵ㾲̬����",'archives',"?entry=$entry&action=$action");
		trbasic('URL�޸���ʽ','',makeradio('kpmode',array(0 => '��ȫȱ��ҳ��',1 => 'ȫ��ҳ����д')),'');
		tabfooter('bsubmit','ִ��');
		a_guide('<li>ͨ��ϵͳ�¿�����̬ģʽʱ����δ������̬������£�ǰ̨������Ҳ����ļ������(400����)
		<li>��ͨ�������޸����ӣ�ʹǰ̨�ɰ���̬URL��������ҳ��',true);
	}else{
		
		$page = empty($page) ? 1 : max(1,intval($page));
		$kpmode = empty($kpmode) ? 0 : 1;
		$_continue = _nodes_url('mcnodes',$page,$kpmode);
		
		if($_continue){//������һҳ
			$page ++;
			cls_message::show("����ִ�е� <b>$page</b> ҳ�������ĵȴ���","?entry=$entry&action=$action&page=$page&kpmode=$kpmode&bsubmit=1");
		}else{//ȫ�����
			cls_message::show('�����޸���ϡ�',"?entry=$entry&action=$action");
		}
	}
}elseif($action == 'cfstatic'){
	backnav('static','cfstatic');
	$mconfigs = cls_cache::Read('mconfigs');
	if(!submitcheck('bmconfigs')){
		tabheader('��̬�ۺ�����','cfstatic',"?entry=$entry&action=$action");
		trbasic('�Ƿ����þ�̬','mconfigsnew[enablestatic]',$mconfigs['enablestatic'],'radio',array('guide' => '������̬���뽫ǰ̨ҳ�����ɾ�̬������޸����ӣ������������Ӵ򲻿������<br>�л�ģʽ֮��������վ������������λ,ʹ������ϢURL���л���ľ�̬ģʽͬ��'));
		$tnstr = "<input type=\"text\" size=\"25\" id=\"mconfigsnew[cnhtmldir]\" name=\"mconfigsnew[cnhtmldir]\" value=\"$mconfigs[cnhtmldir]\">&nbsp;
				<input class=\"checkbox\" type=\"checkbox\" name=\"mconfigsnew[disable_htmldir]\" id=\"mconfigsnew[disable_htmldir]\" value=\"1\"".(empty($mconfigs['disable_htmldir']) ? '' : ' checked').">�����ô�·��";
		trbasic('��Ŀ�ڵ㼰�ĵ���̬��·��','',$tnstr,$mconfigs['cnhtmldir']);
		trbasic('��ҳ�뾲̬ʱ����ҳ��','mconfigsnew[liststaticnum]',$mconfigs['liststaticnum']);
		trbasic('�����ļ�����url������','mconfigsnew[hiddensinurl]',empty($mconfigs['hiddensinurl']) ? '' : $mconfigs['hiddensinurl'],'text',array('guide'=>'<span style="color:#F00">�ļ������ķ���ǰ��</span>������ļ���֮���ö��ŷָ�������ȫվֻʹ�ö�̬�������벻Ҫ����index.php','w'=>50));
		tabfooter();

		tabheader('ϵͳ��ҳ����');
		trbasic('վ����ҳ��̬�ļ���','mconfigsnew[homedefault]',$mconfigs['homedefault']);
		trbasic('վ����ҳ��̬��������','mconfigsnew[indexcircle]',$mconfigs['indexcircle'],'text',array('guide' => '��λ:����'));
		trbasic('��̬���µ���ͣʱ��','mconfigsnew[indexnostatic]',empty($mconfigs['indexnostatic']) ? '' : $mconfigs['indexnostatic'],'text',array('guide'=>'���ʸ߷�ʱ����ͣ�������£����Ի��������ѹ��,��ʽ�磺8-12,13,18-22����ģ�����ģʽ����Ч','w'=>50));
		tabfooter();

		tabheader('��Ŀ�ڵ�����');
		for($i = 0;$i <= $cn_max_addno;$i ++){
			$pvar = $i ? '����ҳ'.$i : '��ҳ';
			$configstr = '��̬�����ʽ'."<input type=\"text\" size=\"25\" id=\"mconfigsnew[cn_urls][$i]\" name=\"mconfigsnew[cn_urls][$i]\" value=\"".@$cn_urls[$i]."\">";
			$configstr .= " &nbsp;��̬��������(����)<input type=\"text\" size=\"5\" id=\"mconfigsnew[cn_periods][$i]\" name=\"mconfigsnew[cn_periods][$i]\" value=\"".@$cn_periods[$i]."\">";
			trbasic('��Ŀ�ڵ�'.$pvar.'����','',$configstr,'',array('guide'=>!$i ? '����ΪĬ�ϸ�ʽ��{$cndir}ϵͳĬ�ϱ���·����{$page}��ҳҳ�룬����֮�佨����Ϸָ���_��-���ӡ�': ''));
		}
		trbasic('��̬���µ���ͣʱ��','mconfigsnew[cn_nostatic]',empty($mconfigs['cn_nostatic']) ? '' : $mconfigs['cn_nostatic'],'text',array('guide'=>'���ʸ߷�ʱ����ͣ�������£����Ի��������ѹ��,��ʽ�磺8-12,13,18-22����ģ�����ģʽ����Ч','w'=>50));
		tabfooter();

		tabheader('�ĵ�ҳ������');
		trbasic('�ĵ�ҳ��̬�����ʽ','mconfigsnew[arccustomurl]',empty($mconfigs['arccustomurl']) ? '' : $mconfigs['arccustomurl'],'text',array('guide'=>'����ΪĬ�ϸ�ʽ��{$topdir}������Ŀ·����{$cadir}������Ŀ·����{$y}�� {$m}�� {$d}�� {$h}ʱ {$i}�� {$s}�� {$chid}ģ��id  {$aid}�ĵ�id {$page}��ҳҳ�� {$addno}����ҳid��id֮�佨���÷ָ���_��-���ӡ�','w'=>50));
		trbasic('�ĵ�ҳ��̬��������','mconfigsnew[archivecircle]',$mconfigs['archivecircle'],'text',array('guide' => '��λ:����'));
		trbasic('��̬���µ���ͣʱ��','mconfigsnew[arc_nostatic]',empty($mconfigs['arc_nostatic']) ? '' : $mconfigs['arc_nostatic'],'text',array('guide'=>'���ʸ߷�ʱ����ͣ�������£����Ի��������ѹ��,��ʽ�磺8-12,13,18-22','w'=>50));
		tabfooter();

		tabheader('����ҳ������');
		trbasic('������Ϣ������ҳ��̬·��','mconfigsnew[infohtmldir]',$mconfigs['infohtmldir']);
		trbasic('��ԱƵ���ڵ㾲̬��������','mconfigsnew[mcnindexcircle]',$mconfigs['mcnindexcircle'],'text',array('guide' => '��λ:����'));
		trbasic('��Ա�ڵ㾲̬���µ���ͣʱ��','mconfigsnew[mcn_nostatic]',empty($mconfigs['mcn_nostatic']) ? '' : $mconfigs['mcn_nostatic'],'text',array('guide'=>'���ʸ߷�ʱ����ͣ�������£����Ի��������ѹ��,��ʽ�磺8-12,13,18-22','w'=>50));
		tabfooter();

		tabheader('��Ա�ռ�����');
        setPermBar('���»�Ա�������ɾ�̬�ռ�','mconfigsnew[mspacepmid]',@$mconfigs['mspacepmid'], 'other', array(0=>'ȫ��������'), '����Ч���ƾ�̬�ռ�����(����5000Ϊ��)��');
        trbasic('�ռ���Ŀҳ��̬�����ʽ','mconfigsnew[ms_customurl]',empty($mconfigs['ms_customurl']) ? '' : $mconfigs['ms_customurl'],'text',array('guide'=>'����ΪĬ�ϸ�ʽ����̬�̶������ڻ�Ա���õľ�̬Ŀ¼��{$cadir}�ռ���ĿĿ¼��{$ucdir}���˷���Ŀ¼��{$page}��ҳҳ�� {$addno}����ҳid������֮�佨����_��-�ָ���','w'=>50));
		trbasic('��̬�ռ䱻����������','mconfigsnew[mspacecircle]',empty($mconfigs['mspacecircle']) ? '' : $mconfigs['mspacecircle'],'text',array('guide'=>'��λ�����ӡ�����Ϊ���Զ����£���ȫ�ɻ�Ա�ֶ����¡�������Ϊ1440�����ա�'));
		trbasic('��̬���µ���ͣʱ��','mconfigsnew[ms_nostatic]',empty($mconfigs['ms_nostatic']) ? '' : $mconfigs['ms_nostatic'],'text',array('guide'=>'���ʸ߷�ʱ����ͣ�������£����Ի��������ѹ��,��ʽ�磺8-12,13,18-22','w'=>50));
		tabfooter('bmconfigs');
		a_guide('cfstatic');

	}else{
		foreach(array('cnhtmldir','infohtmldir',) as $var){
			$mconfigsnew[$var] = strtolower($mconfigsnew[$var]);
			if($mconfigsnew[$var] == $mconfigs[$var]) continue;
			if(!$mconfigsnew[$var] || preg_match("/[^a-z_0-9]+/",$mconfigsnew[$var])){
				$mconfigsnew[$var] = $mconfigs[$var];
				continue;
			}
			if($mconfigs[$var] && is_dir(M_ROOT.$mconfigs[$var])){
				if(is_dir(M_ROOT.$mconfigsnew[$var])){ 
					$_msg = "�޸�δ�ɹ���Ŀ¼[{$mconfigsnew[$var]}]�Ѿ����ڣ�<br>��ʹ������Ŀ¼ �� �ֶ����ƶ���ɾ����Ŀ¼��";
					cls_message::show($_msg,"?entry=$entry&action=$action");
				}
				if(!rename(M_ROOT.$mconfigs[$var],M_ROOT.$mconfigsnew[$var])) $mconfigsnew[$var] = $mconfigs[$var];
			}else mmkdir(M_ROOT.$mconfigsnew[$var],0);
		}
		$mconfigsnew['homedefault'] = trim(strip_tags($mconfigsnew['homedefault']));
		$mconfigsnew['arccustomurl'] = preg_replace("/^\/+/",'',trim($mconfigsnew['arccustomurl']));
		$mconfigsnew['cn_urls'] = empty($mconfigsnew['cn_urls']) ? '' : implode(',',$mconfigsnew['cn_urls']);
		$mconfigsnew['cn_periods'] = empty($mconfigsnew['cn_periods']) ? '' : implode(',',$mconfigsnew['cn_periods']);
		$mconfigsnew['disable_htmldir'] = empty($mconfigsnew['disable_htmldir']) ? 0 : 1;
		$mconfigsnew['msgforwordtime'] = max(0,intval(@$mconfigsnew['msgforwordtime']));
		$mconfigsnew['indexcircle'] = max(0,intval($mconfigsnew['indexcircle']));
		$mconfigsnew['archivecircle'] = max(0,intval($mconfigsnew['archivecircle']));
		$mconfigsnew['liststaticnum'] = max(0,intval($mconfigsnew['liststaticnum']));
		$mconfigsnew['mspacecircle'] = max(0,intval($mconfigsnew['mspacecircle']));
		saveconfig('static');
		adminlog('��վ����','ϵͳ��̬����');
		cls_message::show('��վ�������',"?entry=$entry&action=$action");
	}
}

function _archive_url($stid,$page = 1,$kpmode = 0){
	global $db,$tblprefix;
	$pics = 500;
	$query = $db->query("SELECT aid,chid FROM {$tblprefix}".atbl($stid,1)." WHERE checked='1' ORDER BY aid DESC LIMIT ".($pics * ($page - 1)).",$pics",'SILENT');
	$i = 0;
	$arc = new cls_arcedit;
	while($r = $db->fetch_array($query)){
		if($arc->set_aid($r['aid'],array('au'=>0,'chid'=>$r['chid'],))){
			$arc->set_arcurl($kpmode);
		}
		$i ++;
	}
	return $i < $pics ? false : true;
}

function _nodes_url($type,$page = 1,$kpmode = 0){
	global $db,$tblprefix;
	$pics = 500;
	$i = 0;
	if(!in_array($type,array('cnodes','mcnodes',))) return false;
	$query = $db->query("SELECT cnid,ename FROM {$tblprefix}$type WHERE closed=0 ORDER BY cnid LIMIT ".($pics * ($page - 1)).",$pics",'SILENT');
	$ClassName = $type == 'cnodes' ? 'cls_cnode' : 'cls_mcnode';
	while($r = $db->fetch_array($query)){
		if($type == 'cnodes'){
			cls_cnode::BlankStaticUrl($r['ename'],$kpmode);
		}else{
			cls_mcnode::BlankStaticUrl($r['ename'],$kpmode);
		}
		$i ++;
	}
	return $i < $pics ? false : true;
}

function static_process($op = 'msg', $param = ''){
	switch($op){
	case 'msg':
		echo '<script type="text/javascript">showmessage("', addslashes($param), '");</script>';
		break;
	case 'hide':
		echo '<script type="text/javascript">hide_progress("progressdiv");</script>';
		break;
	case 'jump':
		echo '<script type="text/javascript">redirect("', $param, '");</script>';
		break;
	case 'body':
		ob_implicit_flush();
?>
<div id="progressdiv" style="text-align:left">
	<div><?=$param?></div>
	<div id="progressbody" style="width:100%;height:500px;margin-top:20px;white-space:nowrap;overflow:auto;border:solid 1px #ddd"></div>
</div>
<script type="text/javascript">
var progressbody = document.getElementById('progressbody'), progressdiv = progressbody.parentNode, proressflag = progressbody.firstChild;
progressbody.style.height = document.body.clientHeight - progressbody.offsetTop - 10 + 'px';

function showmessage(message) {
	var div = document.createElement('DIV');
	div.appendChild(document.createTextNode(message));
	if(proressflag){
		progressbody.insertBefore(div, proressflag);
	}else{
		progressbody.appendChild(div);
	}
	proressflag = div;
}

function hide_progress(id) {
	document.getElementById(id).style.display = 'none';
}
</script>
<?php
		break;
	}
	ob_flush();
}
