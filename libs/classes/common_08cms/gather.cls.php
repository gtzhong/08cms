<?php
(!defined('M_COM') || !defined('M_ADMIN')) && exit('No Permission');
#set_time_limit(0);
@include_once _08_INCLUDE_PATH.'http.cls.php';
@include_once _08_INCLUDE_PATH.'linkparse.cls.php';
@include_once _08_EXTEND_LIBS_PATH.'functions'.DS.'custom.fun.php';
class cls_gather{
	var $gsid = 0;
	var $gmission = array();
	var $fields = array();
	var $oconfigs = array();
	var $urlarr = array();
	var $mpcontent = '';
	var $mplinks = array();
	function __construct(){
		$this->cls_gather();
	}
	function cls_gather(){
	}
	function init(){
		$this->gsid = 0;
		$this->gmission = array();
		$this->fields = array();
		$this->oconfigs = array();
		$this->urlarr = array();
		$this->mpcontent = '';
		$this->mplinks = array();
	}
	function set_mission($gsid){
		if(!($this->gmission = cls_cache::Read('gmission',$gsid,''))) return false;
		$this->gsid = $gsid;
		unset($this->gmission['fsettings'],$this->gmission['dvalues']);//���ֶ���Ϣ�����������Ϣ�ļ���Ϣ
		return true;
	}
	function gather_fields(){
		$gmid = $this->gmission['gmid'];
		$gmodel = cls_cache::Read('gmodel',$gmid,'');
		$gfields = $gmodel['gfields'];
		$chid = $gmodel['chid'];
		$fields = cls_cache::Read('fields',$chid);
        $cotypes = cls_cache::Read('cotypes');
        $cfields = array('caid'=>array('datatype'=>'select','cname'=>'��Ŀ'));
        foreach($cotypes as $k=>$v){
            $cfields['ccid'.$k]['datatype'] = $v['asmode'] ? 'mselect' : 'select';
            $cfields['ccid'.$k]['cname'] = $v['cname'];
        }
        $fields = $cfields + $fields + array('jumpurl'=>array('datatype'=>'text','cname'=>'��תURL'),'createdate'=>array('datatype'=>'text','cname'=>'���ʱ��'),'mname'=>array('datatype'=>'text','cname'=>'��Ա����'));;
		$gmission = cls_cache::Read('gmission',$this->gsid,'');
		$fsettings = $gmission['fsettings'];
		foreach($fields as $k => $v){
			if(isset($gfields[$k]) && isset($fsettings[$k])){
				$this->fields[$k] = $v + $fsettings[$k];
				$this->fields[$k]['islink'] = $gfields[$k];
				//$this->fields[$k]['rpid'] = empty($this->fields[$k]['rpid']) ? 0 : $this->fields[$k]['rpid'];
                $this->fields[$k]['rpid'] = empty($fsettings[$k]['rpid'])?0:$fsettings[$k]['rpid'];
				$this->fields[$k]['jumpfile'] = empty($this->fields[$k]['jumpfile']) ? '' : $this->fields[$k]['jumpfile'];
			}
		}
		unset($fields,$gmodel,$gfields,$gmission,$fsettings);
	}
	function output_configs(){
		$gmission = cls_cache::Read('gmission',$this->gsid,'');
		$this->oconfigs = $gmission['dvalues'];
		unset($gmission);
	}
	function fetch_surls(){
		$surls = array();
		$this->gmission['uurls'] && $surls = array_filter(explode("\n",$this->gmission['uurls']));
		if($this->gmission['uregular'] && strpos($this->gmission['uregular'],'(*)')>1){
			for($i = $this->gmission['ufromnum'];$i <= $this->gmission['utonum'];$i++){
				$surls[] = str_replace("(*)",$i,$this->gmission['uregular']);
			}
		}
		$this->gmission['udesc'] && krsort($surls);
		return $surls;
	}
	/**  $type ׷����ַ����  1=��׷����ַ1  2=�� ׷����ַ2  Ĭ��1
	**/
	function fetch_addurl($surl,$pattern,$reflink,$type=1){
		if(empty($surl) || empty($pattern)) return '';
		$html = $this->onepage($surl);
		if($type=1){
			$addurl = $this->fetch_detail($pattern,$html,$this->gmission['umode1']);
		}else if($type=2){
			$addurl = $this->fetch_detail($pattern,$html,$this->gmission['umode2']);
		}else $addurl = $this->fetch_detail($pattern,$html);
		$addurl = fillurl($addurl,$reflink);
		if($type == 1){    //׷��ҳ1
			if($this->gmission['uinclude1'] && !preg_match('#'.$this->gmission['uinclude1'].'#i',$addurl)) $addurl=''; //��Ҫ�������ַ�
			if($this->gmission['uforbid1'] && preg_match('#'.$this->gmission['uforbid1'].'#i',$addurl)) $addurl=''; //��ֹ�������ַ�
		}
		else{              //׷��ҳ2
			if($this->gmission['uinclude2'] && !preg_match('#'.$this->gmission['uinclude2'].'#i',$addurl)) $addurl=''; //��Ҫ�������ַ�
			if($this->gmission['uforbid2'] && preg_match('#'.$this->gmission['uforbid2'].'#i',$addurl)) $addurl='';//��ֹ�������ַ�
		}
		unset($html);
		return $addurl;
	}

	function fetch_gurls($surl,$istest=0){//
		global $db,$tblprefix,$timestamp,$progress;
		$c_upload = cls_upload::OneInstance();
		if(empty($surl) || !($html = $this->onepage($surl)))return false;//Դ��ַ�����ڻ��޷���ȡ��ҳ��
		$this->gmission['uregion'] && $html = $this->fetch_detail($this->gmission['uregion'],$html);//ȡ����ʼ��Ч��Χ
		if(!($urlregions = @explode($this->gmission['uspilit'],$html))) return false;//����url����
		if($this->gmission['udesc']) krsort($urlregions);//�ɼ�˳��
		unset($html);
		if(!$istest){//���ݲɼ�ʱ��Ҫ����ַ�б�ҳ�вɼ�������Ԥѡ�ɼ�
			$ufields = array();
			empty($this->fields) && $this->gather_fields();//���������еĲɼ��ֶ�
			foreach($this->fields as $k => $v){
				if($v['frompage'] == 1) $ufields[] = $k;
			}
		}
		$linkcount = 0;
		$rets = array();//���Եķ�������
		foreach($urlregions as $urlregion){//����ÿ��url��������
			if($istest && count($rets) >= 10) break;//ֻ����10����ַ
			$c_upload->init();
			$this->clean_blank($urlregion);
			if(!$gurl = $this->fetch_detail($this->gmission['uurltag'],$urlregion)) continue;//�޷���ȡ����ҳ��url
			$gurl = fillurl($gurl,!empty($this->gmission['ubase']) ? $this->gmission['ubase'] : $surl);
			if($this->gmission['uinclude'] && !preg_match('#'.$this->gmission['uinclude'].'#i',$gurl)) continue;//��Ҫ�������ַ�
			if($this->gmission['uforbid'] && preg_match('#'.$this->gmission['uforbid'].'#i',$gurl)) continue;//��ֹ�������ַ�

			$refresh = false;
			if(!$istest && $row = $db->fetch_one("SELECT guid,abover FROM {$tblprefix}gurls WHERE gurl='".addslashes($gurl)."'")){//������Ѵ��ڵ���ַ
				if(!$this->gmission['sonid'] || $row['abover']) continue;//���������ϼ�����ᣬ���Թ�����ַ
				$refresh = true;
				$guid = $row['guid'];
			}
			$utitle = $this->fetch_detail($this->gmission['utitletag'],$urlregion);
			$utitle = !$utitle ? '���ⲻ��': addslashes(strip_tags($utitle));
			$gurl1 = $this->fetch_addurl($gurl,$this->gmission['uurltag1'],!empty($this->gmission['ubase0']) ? $this->gmission['ubase0'] : $gurl,1);
			$gurl2 = $this->fetch_addurl($gurl1,$this->gmission['uurltag2'],!empty($this->gmission['ubase1']) ? $this->gmission['ubase1'] : $gurl1,2);
			$linkcount++;
			if(!$istest){//�ǲ���״̬����Ҫ�ɼ��б��е�����
				if(!$refresh){
					$contents = array();
					foreach($ufields as $v) $contents[$v] = $this->common_field($v,$urlregion,$gurl);
					$db->query("INSERT INTO {$tblprefix}gurls SET
					gurl='$gurl',
					gurl1='$gurl1',
					gurl2='$gurl2',
					utitle='$utitle',
					contents='".addslashes(serialize($contents))."',
					ufids='".implode(',',$c_upload->ufids)."',
					adddate='$timestamp',
					gsid='".$this->gsid."'");
					$guid = $db->insert_id();
					$progress && $progress->linkcount($linkcount);
				}
				if($this->gmission['sonid'] && $guid) $this->fetch_son_gurls($this->gmission['sonid'],$guid,$gurl,$gurl1,$gurl2,0);//�ɼ��ϼ��е���ַ�б�
			}else{//����״̬
				$rets[$gurl] = array(
					'utitle' => $utitle,
					'gurl'	 => $gurl,
					'gurl1'	 => $gurl1,
					'gurl2'	 => $gurl2
				);
#				$this->gmission['sonid'] && $rets = $rets + $this->fetch_son_gurls($this->gmission['sonid'],0,$gurl,$gurl1,$gurl2,1);
			}
		}
		unset($ufields,$urlregions,$urlregion,$contents);
		if($istest) return $rets;
	}
	function fetch_son_gurls($gsid=0,$guid=0,$url0='',$url1='',$url2='',$istest=0){//�ɼ�����Ժϼ��ڵ���ַ�б�
		global $db,$tblprefix,$timestamp,$progress;
		$c_upload = cls_upload::OneInstance();
		$rets = array();
		if(!$gsid) return $rets;
		$ng = new cls_gather;
		$ng->set_mission($gsid);
		$gmission = &$ng->gmission;
		$surl = ${'url'.$gmission['ufrompage']};//�ɼ���ַ�б��Դurl
		if(!$gmission['pid'] || !$surl || !($html = $ng->onepage($surl))) return $rets;//����������������ַԴurl�����ڻ�Դurlҳ��ɲ�������
		$html = $ng->fetch_detail($ng->gmission['uregion'],$html);//��ʼֵ��Χ
		$urlregions = explode($ng->gmission['uspilit'],$html);//�ָ���ǲ��
		if($ng->gmission['udesc']) krsort($urlregions);//�ɼ�˳��
		unset($html);
		$ufields = array();
		empty($ng->fields) && $ng->gather_fields();
		foreach($ng->fields as $k => $v) $v['frompage'] == 1 && $ufields[] = $k;
		$linkcount = 0;
		$ubase = $this->gmission['ubase' . $gmission['ufrompage']];
		foreach($urlregions as $urlregion){//ÿ��url����
			$c_upload->init();
			$ng->clean_blank($urlregion);
			if(!$gurl = $ng->fetch_detail($ng->gmission['uurltag'],$urlregion)) continue;//urlģӡ
			$gurl = fillurl($gurl,$ubase ? $ubase : $surl);//��ȫurl
			if($ng->gmission['uinclude'] && !preg_match('#'.$ng->gmission['uinclude'].'#i',$gurl)) continue;
			if($ng->gmission['uforbid'] && preg_match('#'.$ng->gmission['uforbid'].'#i',$gurl)) continue;

			if($db->result_one("SELECT COUNT(*) FROM {$tblprefix}gurls WHERE gurl='".addslashes($gurl)."'")) continue;//������Ѵ��ڵ���ַ
			$utitle = $ng->fetch_detail($ng->gmission['utitletag'],$urlregion);//����
			$utitle = !$utitle ? '���ⲻ��': strip_tags($utitle);
			$gurl1 = $ng->fetch_addurl($gurl,$ng->gmission['uurltag1'],$gmission['ubase0'] ? $gmission['ubase0'] : $gurl,1);//׷��ҳ1
			$gurl2 = $ng->fetch_addurl($gurl1,$ng->gmission['uurltag2'],$gmission['ubase1'] ? $gmission['ubase1'] : $gurl1,2);//׷��ҳ2
			$linkcount++;
			$contents = array();
			if(!$istest){
				foreach($ufields as $v) $contents[$v] = $ng->common_field($v,$urlregion,$gurl);//��Ҫ���б�ҳ�вɼ������ݣ��ڲɼ���ַ��ͬʱ�ɼ�����
			}
			if($istest){//�ϼ���Ҫ�������������ַ�г�����
				$rets[$gurl]['utitle'] = $utitle;
				$rets[$gurl]['gurl'] = $gurl;
				$rets[$gurl]['gurl1'] = $gurl1;
				$rets[$gurl]['gurl2'] = $gurl2;
				$rets[$gurl]['son'] = 1;
			}else{//����ַ�����ݴ������ݿ���
				$db->query("INSERT INTO {$tblprefix}gurls SET
				pid='$guid',
				gurl='$gurl',
				gurl1='$gurl1',
				gurl2='$gurl2',
				utitle='$utitle',
				contents='".addslashes(serialize($contents))."',
				ufids='".implode(',',$c_upload->ufids)."',
				adddate='$timestamp',
				gsid='".$ng->gsid."'");
			}
		}
		$progress && $progress->linkcount($linkcount);
		unset($ng,$urlregions,$urlregion,$ufields,$contents);
		return $rets;
	}
	function gather_sonid($pid=0,$gsid=0){//�ɼ��ϼ��е�δ�ɼ���Ŀ
		global $db,$tblprefix,$timestamp;
		if(!$pid || !$gsid) return;
		$ng = new cls_gather;
		$ng->set_mission($gsid);
		$ng->gather_fields();//���з����ɼ�����
		if(empty($ng->fields)) return;
		$query = $db->query("SELECT guid FROM {$tblprefix}gurls WHERE gsid='$gsid' AND gatherdate='0' AND pid='$pid' ORDER BY guid ASC");
		while($row = $db->fetch_array($query)){
			$ng->gather_guid($row['guid'],0);
		}
		unset($ng);
	}
	function gather_guid($guid=0,$istest=0,$item=0){//ֻ�ɼ�δ������
		global $db,$tblprefix,$timestamp,$progress;
		$c_upload = cls_upload::OneInstance();
		if((!$guid || !($item = $db->fetch_one("SELECT * FROM {$tblprefix}gurls WHERE guid='$guid'"))) && !$item) return false;
		if(empty($item['gatherdate'])){//δ������
			$contents = empty($item['contents']) ? array() : unserialize($item['contents']);
			unset($item['contents']);
			if(empty($this->fields)) $this->gather_fields();
			if(empty($this->fields)) return false;
			$fields0 = $fields2 = $fields3 = array();
			foreach($this->fields as $k => $v){
				if($v['frompage'] == '0'){
					$fields0[] = $k;
				}elseif($v['frompage'] == '2' && $item['gurl1']){
					$fields2[] = $k;
				}elseif($v['frompage'] == '3' && $item['gurl2']){
					$fields3[] = $k;
				}
			}
			$c_upload->init();
			if(!empty($fields0)){
				$html = $this->onepage($item['gurl']);
				foreach($fields0 as $k) $contents[$k] = $istest && !$html ? false : $this->one_content($k,$html,$item['gurl'],0);
			}
			if(!empty($fields2)){
				$html = $this->onepage($item['gurl1']);
				foreach($fields2 as $k) $contents[$k] = $istest && !$html ? false : $this->one_content($k,$html,$item['gurl1'],1);
			}
			if(!empty($fields3)){
				$html = $this->onepage($item['gurl2']);
				foreach($fields3 as $k) $contents[$k] = $istest && !$html ? false : $this->one_content($k,$html,$item['gurl2'],2);
			}
			if(!$istest){
				$item['ufids'] .= ($item['ufids'] && $c_upload->ufids ? ',' : '').implode(',',$c_upload->ufids);
				$db->query("UPDATE {$tblprefix}gurls SET
							contents = '".addslashes(serialize($contents))."',
							ufids = '$item[ufids]',
							gatherdate = '$timestamp'
							WHERE guid='$guid'");
			}
			$progress && $progress->content(1);
		}
		if(!$istest && $this->gmission['sonid'] && !$item['abover']) $this->gather_sonid($guid,$this->gmission['sonid']);//�ǲ���ʱ,�ɼ��ϼ��е���ַ����
		return $istest ? $contents : true;
	}
	function output_sonid($pid=0,$gsid=0){//���ϼ��е�δ�ɼ���Ŀ���
		global $db,$tblprefix,$timestamp;
		if(!$pid || !$gsid) return;
		$ng = new cls_gather;
		$ng->set_mission($gsid);
		$ng->output_configs();//�ȷ����Ƿ��������������
		if(empty($ng->oconfigs)) return;
		$query = $db->query("SELECT guid FROM {$tblprefix}gurls WHERE gsid='$gsid' AND outputdate='0' AND gatherdate<>'0' AND pid='$pid' ORDER BY guid ASC");
		while($row = $db->fetch_array($query)) $ng->output_guid($row['guid']);
		unset($ng);
	}
	function output_guid($guid=0){//��ֹ�ظ����,δ���ϼ���Ҫ������ڵ�����
		global $db,$tblprefix,$timestamp,$progress;
		if(!$guid || !($item = $db->fetch_one("SELECT * FROM {$tblprefix}gurls WHERE guid='$guid' AND gatherdate<>'0'"))) return false;
		$c_upload = cls_upload::OneInstance();
		$curuser = cls_UserMain::CurUser();
		if(!$item['outputdate']){
			$archivenew = empty($item['contents']) ? array() : unserialize($item['contents']);
			unset($item['contents']);
			empty($this->fields) && $this->gather_fields();
			empty($this->oconfigs) && $this->output_configs();
			if(empty($this->fields) || empty($this->oconfigs)) return false;
			if(!empty($this->oconfigs['musts'])){
				$mustsarr = explode(',',$this->oconfigs['musts']);
				foreach($mustsarr as $k){
					if(empty($archivenew[$k])) return false;//ȱ�ٱ����ֶ����ݣ������ֹ
				}
			}
			$gmodels = cls_cache::Read('gmodels');
			$gmid = $this->gmission['gmid'];
			$chid = $gmodels[$gmid]['chid'];
			$fields = cls_cache::Read('fields',$chid);

			$c_upload->init();
			$arc = new cls_arcedit;
			if($aid = $item['aid']){
				if(!$arc->set_aid($aid,array('chid'=>$chid,'ch'=>1)) && !$arc->arcadd($chid,@$this->oconfigs['caid'],$aid))return false;
			}else{
				$catalogs = cls_cache::Read('catalogs');
				if(empty($catalogs[$archivenew['caid']])) $archivenew['caid'] = '';
				if(!($aid = $arc->arcadd($chid,empty($archivenew['caid']) ? @$this->oconfigs['caid'] : $archivenew['caid']))) return false;
			}
			$cotypes = cls_cache::Read('cotypes');
			foreach($cotypes as $k => $v){
				if(!empty($archivenew["ccid$k"])){
					$newccid = array_filter(explode(',',$archivenew["ccid$k"]));
					foreach($newccid as $c) if(!$coclass = cls_cache::Read('coclasses',$k,$c)) unset($newccid[$c]);
					$archivenew["ccid$k"] = implode(',',$newccid);
				}
				isset($this->oconfigs["ccid$k"]) && $arc->arc_ccid(empty($archivenew["ccid$k"]) ? $this->oconfigs["ccid$k"] : $archivenew["ccid$k"],$k);
			}
			foreach($fields as $k => $v){
				if(empty($archivenew[$k]) && isset($this->oconfigs[$k])) $archivenew[$k] = $this->oconfigs[$k];
				if(isset($archivenew[$k])){
					$archivenew[$k] = addslashes($archivenew[$k]);
					$arc->updatefield($k,$archivenew[$k],$v['tbl']);
					if($arr = multi_val_arr($archivenew[$k],$v)) foreach($arr as $x => $y) $arc->updatefield($k.'_'.$x,$y,$v['tbl']);
				}
			}
			//�����Ա
			$u = new cls_userbase;
			$mnamearr = empty($this->oconfigs['mname']) ? array() : explode(',',$this->oconfigs['mname']);
			if(!empty($archivenew['mname'])) $u->activeuserbyname($archivenew['mname']);
			if(!empty($mnamearr) && empty($u->info['mid']))	$u->activeuserbyname($mnamearr[array_rand($mnamearr)]);
			if(!empty($u->info['mid'])){
				$arc->updatefield('mid',$u->info['mid']);
				$arc->updatefield('mname',$u->info['mname']);
			}
			//����ʱ��
			$archivenew['createdate'] = str_replace(array('��','��','��'),array('-','-',''),@$archivenew['createdate']);
			$archivenew['createdate'] = strtotime($archivenew['createdate']) ? strtotime($archivenew['createdate']) : $timestamp;
			$arc->updatefield('createdate',$archivenew['createdate']);
			$arc->updatefield('initdate',$archivenew['createdate']);
			//������תURL
			$arc->updatefield('jumpurl',empty($archivenew['jumpurl']) ? '' : $archivenew['jumpurl']);
			$arc->auto();
			$arc->autocheck();
			$arc->updatedb();
			
			$abrels = cls_cache::Read('abrels');
			if(!empty($item['pid']) && !empty($this->oconfigs['arid']) && isset($abrels[$this->oconfigs['arid']])){
				if($pid = $db->result_one("SELECT aid FROM {$tblprefix}gurls WHERE guid='$item[pid]'")) $arc->set_album($pid,$this->oconfigs['arid']);
			}

			$ufids = $c_upload->ufids + explode(',',$item['ufids']);
			empty($ufids) || $db->query("UPDATE {$tblprefix}userfiles SET aid=$aid WHERE ufid ".multi_str($ufids));

			$db->query("UPDATE {$tblprefix}gurls SET aid='$aid',outputdate='$timestamp',contents='',ufids='' WHERE guid='$guid'");
			$progress && $progress->output(1);
		}
		if($this->gmission['sonid'] && !$item['abover']) $this->output_sonid($guid,$this->gmission['sonid']);//���ϼ��е��������
		unset($arc,$fields,$field,$item,$archivenew);
		return true;
	}
	function one_content($fname,&$html,$reflink,$reindex){
		$content = '';
		if($fname != $this->gmission['mpfield']){
			$url = empty($this->gmission['ubase' . $reindex]) ? '' : $this->gmission['ubase' . $reindex];
			$content = $this->common_field($fname,$html,$url ? $url : $reflink);
		}else{
			$this->mpfield($fname,$html,$reflink,$reindex);
			$content = $this->mpcontent;
		}
		$this->redeal_content($fname,$content);
		return $content;
	}
	function redeal_content($fname,&$content){//�Բ�ͬ���͵��ֶ���һ���ٴ�������
		if($content == '') return;
		empty($this->fields) && $this->gather_fields();
		if(!$field = $this->fields[$fname]) return;
		if(in_array($field['datatype'],array('htmltext','text','select','mselect'))){
			$content = trim($content);
		}elseif(in_array($field['datatype'],array('int','date'))){
			$content = intval($content);
		}elseif($field['datatype'] == 'float'){
			$content = floatval($content);
		}elseif($field['datatype'] == 'multitext'){
			$content = mnl2br(trim($content));
		}
	}
	function mpfield($fname,&$html,$reflink,$reindex,$step=0){
		if(!$html) return '';
		empty($this->fields) && $this->gather_fields();
		if(!$field = $this->fields[$fname]) return;
		if(!$step){
			$this->mpcontent = '';
			$this->mplinks = array();
		}
		$baseurl = $this->gmission['ubase' . $reindex];
		$baseurl || $baseurl = $reflink;
		if($mparea = $this->fetch_detail($this->gmission['mptag'],$html)){
			$mplinks = array_unique(array_merge(array($reflink),$this->searchlinks($mparea,$baseurl)));
		}else $mplinks = array($reflink);
		if(!$this->gmission['mpmode']){//������ҳ����//ͬ����Ҫ����$step
			foreach($mplinks as $mplink){
				$step ++;
				if($this->gmission['mpinclude'] && !preg_match('#'.$this->gmission['mpinclude'].'#i',$mplink)) continue;
				if($this->gmission['mpforbid'] && preg_match('#'.$this->gmission['mpforbid'].'#i',$mplink)) continue;
				if(in_array($mplink,$this->mplinks)) continue;//�ظ���ҳ��
				if(!$mphtml = $this->onepage($mplink)) continue;
				if(!in_array($field['datatype'],array('images','files','flashs','medias'))){
					$this->mpcontent .= ($this->mpcontent ? '[##]' : '').$this->common_field($fname,$mphtml,$baseurl);
				}else{
					$contentarr = ($this->mpcontent && is_array(unserialize($this->mpcontent))) ? unserialize($this->mpcontent) : array();
					$contentarr = array_merge($contentarr,unserialize($this->common_field($fname,$mphtml,$baseurl)));
					$this->mpcontent = serialize($contentarr);
					unset($contentarr);
				}
				$this->mplinks[] = $mplink;
			}
		}else{
			if($step > 20) return;
			$continue = 0;
			foreach($mplinks as $mplink){
				if($this->gmission['mpinclude'] && !preg_match('#'.$this->gmission['mpinclude'].'#i',$mplink)) continue;
				if($this->gmission['mpforbid'] && preg_match('#'.$this->gmission['mpforbid'].'#i',$mplink)) continue;
				if(in_array($mplink,$this->mplinks)) continue;
				if(!$mphtml = $this->onepage($mplink)) continue;
				if(!in_array($field['datatype'],array('images','files','flashs','medias'))){
					$this->mpcontent .= ($this->mpcontent ? '[##]' : '').$this->common_field($fname,$mphtml,$baseurl);
				}else{
					$contentarr = ($this->mpcontent && is_array(unserialize($this->mpcontent))) ? unserialize($this->mpcontent) : array();
					$contentarr = array_merge($contentarr,unserialize($this->common_field($fname,$mphtml,$baseurl)));
					$this->mpcontent = serialize($contentarr);
					unset($contentarr);
				}
				$continue = 1;
				$this->mplinks[] = $mplink;
				$nexturl = $mplink;
				$step ++;
			}
			if($continue){
				if(!$mphtml = $this->onepage($nexturl)) return;
				$this->mpfield($fname,$mphtml,$nexturl,$reindex,$step);
			}
		}
		unset($mphtml,$mplinks);
	}
	function common_field($fname,&$html,$reflink){//��ǰ���񣬵�ǰurl�����
		if($html == '') return '';
		empty($this->fields) && $this->gather_fields();
		if((!$field = $this->fields[$fname]) || empty($field['ftag'])) return '';
		$linkparse = new linkparse;
		if(!in_array($field['datatype'],array('images','files','flashs','medias'))){
			$content = $this->fetch_detail($field['ftag'],$html);
			$this->c_replace($field['fromreplace'],$field['toreplace'],$content);
			$this->clearhtml($field['clearhtml'],$content);
			$linkparse->setsource($content,$reflink,$field['rpid'],@$field['wmid'],$field['jumpfile']);
			if(!$field['islink']){
				$linkparse->handlelinks();
				$content = $linkparse->html;
			}else{
				$content = $linkparse->handlelink($content);
				if(in_array(mextension($content),array('jpg','gif','png','jpeg','bmp'))){
					$imageinfo = @getimagesize(cls_url::view_url($content));
					!empty($imageinfo) && ($content .= '#'.$imageinfo[0].'#'.$imageinfo[1]);
				}
			}
		}else{
			$content = $this->fetch_detail($field['ftag'],$html);
			$fregions = explode($field['splittag'],$content);
			$furls = array();
			$linkparse->setsource('',$reflink,$field['rpid'],$field['wmid'],$field['jumpfile']);
			foreach($fregions as $fregion){
				$urlarr = array();
				$this->clean_blank($fregion);
				if(!$furl = $this->fetch_detail($field['remotetag'],$fregion)) continue;
				$furl = $linkparse->handlelink($furl);
				$urlarr['remote'] = $furl;
				if($field['datatype'] == 'images'){
					$imageinfo = @getimagesize(cls_url::view_url($furl));
					!empty($imageinfo[0]) && ($urlarr['width'] = $imageinfo[0]);
					!empty($imageinfo[1]) && ($urlarr['height'] = $imageinfo[1]);
				}
				$urlarr['title'] = $this->fetch_detail($field['titletag'],$fregion);
				$furls[] = $urlarr;
			}
			$content = serialize($furls);
		}
		!empty($field['func']) && $this->func_deal($field['func'],$content);
		unset($linkparse,$urlarr,$furls);
		return $content;
	}
	function func_deal($funcstr,&$content){
		if(empty($funcstr) || empty($content) || !in_str('(*)',$funcstr)) return;
		$funcname = substr($funcstr,0,strpos($funcstr,'('));
		if(empty($funcname) || !function_exists($funcname)) return;
		$content = str_replace( "'","\'", $content); //����ƥ���ַ����е����ŵ��������ת��
		$funcstr = str_replace('(*)',"'".$content."'",$funcstr);//��ƥ����ַ�������php�ַ������ӵ����ţ�
		@eval("\$result = $funcstr;");
		$content = $result;
		unset($result);
	}
	function onepage($url){
		global $mcharset,$progress;
		$timeout = $this->gmission['timeout'] ? $this->gmission['timeout'] : 0xffff;
        // ���������&ampת��&�ַ�
        $url = htmlspecialchars_decode($url);
		if($this->gmission['mcookies']){
			$m_http = new http;
			$m_http->timeout = $timeout;
			$m_http->setCookies($this->gmission['mcookies']);
			$html = $m_http->fetchtext($url);
			unset($m_http);
		}else $html = html_get_contents("compress.zlib://".$url,$timeout);//urlǰ�����ǰ׺��compress.zlib:// ��Ϊ�˷�ֹ�ļ�����gzipѹ�����֮�󣬵��»�ȡ����ҳ������Ϊ���롣��ǰ׺�����ļ��Ƿ񾭹�gzipѹ�����������������С�
		$html = cls_string::iconv($this->gmission['mcharset'],$mcharset,$html);
		$this->clean_blank($html);
		$progress && $progress->pagecount(1);
		return $html;
	}
	function c_replace(&$fromreplace,&$toreplace,&$content){
		if(!$fromreplace || !$content) return;
		$fromarr = explode("(|)",$fromreplace);
		$toarr = explode("(|)",$toreplace);
		foreach($fromarr as $k => $fromtag){
			$totag = isset($toarr[$k]) ? $toarr[$k] : '';
			$tags = explode('(*)',$fromtag);
			if(count($tags) > 1 && ($tags[0] || $tags[1])){
				$stag = $this->regencode($tags[0]);
				$etag = $this->regencode($tags[1]);
				$content=preg_replace("/".$stag."(.*?)".$etag."/is",$totag,$content);
			}else $content = str_replace($fromtag,$totag,$content);
		}
	}
	   /** @param $umode ģӡƥ��ģʽ 
					  1 ��ȫƥ��
					  0 ����ȫƥ�� 
	**/
	function fetch_detail($tagstr,&$html,$umode=0){
		if(!$tagstr) return '';#static $debug = 0;$debug++;if($debug > 1)exit;
		$this->clean_blank($tagstr);
		$pos = strpos($tagstr, '(*)');
		if(!$pos || $pos + 3 == strlen($pos)) return '';//echo "\n/" . $this->regencode($tagstr) . "/is\n";exit();
		if(!preg_match('/' . $this->regencode($tagstr) . '/is', $html, $matches)) return '';
		#var_dump($matches);
		if($umode == 1){
			$fetchstr = &$matches[0];
		}else{
			$fetchstr = &$matches[1];
		}
		$this->clean_blank($fetchstr);
		unset($html,$tagstr,$matches);
		return $fetchstr;
	}
	function searchlinks($html,$reflink){
		$links = array();
		$aregions = array();

		$regex = "/<a(.+?)href[ ]*=[ |'|\"]*(.+?)[ |'|\"]+/is";
		if(preg_match_all($regex,$html,$matches)){
			$aregions = array_unique($matches[2]);
			foreach($aregions as $aregion){
				$aregion = fillurl($aregion,$reflink);
				$links[] = $aregion;
			}
		}
		return $links;
	}
	function clean_blank(&$str){
		$str=preg_replace("/([\r\n|\r|\n]*)/is","",$str);
		$str=preg_replace("/>([\s]*)</is","><",$str);
		$str=preg_replace("/^([ ]*)/is","",$str);
		$str=preg_replace("/([ ]*)$/is","",$str);
	}
	function regencode($str){
		$search  = array("\\",'"',".","[", "]","(", ")","?","+","*","^","{","}","$","|","/","\(\?\)","\(\*\)");
		$replace = array("\\\\",'\"',"\.","\[","\]","\(","\)","\?","\+","\*","\^","\{","\}","\$","\|","\/",".*?","(.*?)");
		return str_replace($search,$replace,$str);
	}
	function clearhtml(&$serial,&$str){
		if(!$serial || !$str) return;
		$ids = array_filter(explode(',',$serial));
		$search = array(
					  "/<a[^>]*?>(.*?)<\/a>/is",
					  "/<br[^>]*?>/i",
					  "/<table[^>]*?>([\s\S]*?)<\/table>/i",
					  "/<tr[^>]*?>([\s\S]*?)<\/tr>/i",
					  "/<td[^>]*?>([\s\S]*?)<\/td>/i",
					  "/<p[^>]*?>([\s\S]*?)<\/p>/i",
					  "/<font[^>]*?>([\s\S]*?)<\/font>/i",
					  "/<div[^>]*?>([\s\S]*?)<\/div>/i",
					  "/<span[^>]*?>([\s\S]*?)<\/span>/i",
					  "/<tbody[^>]*?>([\s\S]*?)<\/tbody>/i",
					  "/<([\/]?)b>/i",
					  "/<img[^>]*?>/i",
					  "/&nbsp;/i",
					  "/<script[^>]*?>([\w\W]*?)<\/script>/i",
					  );
		$replace = array(
					   "\\1",
					   "",
					   "\\1",
					   "\\1",
					   "\\1",
					   "\\1",
					   "\\1",
					   "\\1",
					   "\\1",
					   "\\1",
					   "",
					   "",
					   "",
					   "\\1",
					   );
		foreach($ids as $id) $str = preg_replace($search[$id-1],$replace[$id-1],$str);
	}
}
function fillurl($surl,$refhref,$basehref=''){//$refhref���Բ��յ���ȫ��ַ
	$surl = trim($surl);
	$refhref = trim($refhref);
	$basehref = trim($basehref);
	if($surl == '') return '';

	if($basehref){
		$preurl = strtolower(substr($surl,0,6));
		if(in_array($preurl,array('http:/','ftp://','mms://','rtsp:/','thunde','emule:','ed2k:/'))){
			return  $surl;
		}else{
			return $basehref.'/'.$surl;
		}
	}

	$urlparses = @parse_url($refhref);
	$homeurl = $urlparses['host'];
	$baseurlpath = $homeurl.$urlparses['path'];
	$baseurlpath = preg_replace("/\/([^\/]*)\.(.*)$/","/",$baseurlpath);
	$baseurlpath = preg_replace("/\/$/","",$baseurlpath);

	$i = $pathstep = 0;
	$dstr = $pstr = $okurl = '';
	$surl = (strpos($surl,"#") > 0) ? substr($surl,0,strpos($surl,"#")) : $surl;
	if($surl[0]=="/"){//����http�ľ�����ַ
		$okurl = "http://".$homeurl.$surl;
	}elseif($surl[0] == "."){//�����ַ
		if(strlen($surl) <= 1){
			return "";
		}elseif($surl[1] == "/"){
			$okurl = "http://".$baseurlpath."/".substr($surl,2,strlen($surl)-2);
		}else{
			$urls = explode("/",$surl);
			foreach($urls as $u){
				if($u == ".."){
					$pathstep++;
				}elseif($i < count($urls) - 1){
					$dstr .= $urls[$i]."/";
				}else{
					$dstr .= $urls[$i];
				}
				$i++;
			}
			$urls = explode("/",$baseurlpath);
			if(count($urls) <= $pathstep){
				return "http://".$baseurlpath.'/'.$dstr;
			}else{
				$pstr = "http://";
				for($i = 0;$i < count($urls)-$pathstep;$i++){
					$pstr .= $urls[$i]."/";
				}
				$okurl = $pstr.$dstr;
			}
		}
	}else{
		$preurl = strtolower(substr($surl,0,6));
		if(strlen($surl)<7){
			$okurl = "http://".$baseurlpath."/".$surl;
		}elseif(in_array($preurl,array('http:/','ftp://','mms://','rtsp:/','thunde','emule:','ed2k:/'))){
			$okurl = $surl;
		}else $okurl = "http://".$baseurlpath."/".$surl;
	}

	$preurl = strtolower(substr($okurl,0,6));
	if(in_array($preurl,array('ftp://','mms://','rtsp:/','thunde','emule:','ed2k:/'))){
		return $okurl;
	}else{
		$okurl = preg_replace("/^(http:\/\/)/","",$okurl);
		$okurl = preg_replace("/\/{1,}/","/",$okurl);
		return "http://".$okurl;
	}
}

?>
