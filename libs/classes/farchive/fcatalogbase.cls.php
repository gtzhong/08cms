<?php
/* 
** ��������ķ�������
** ���ô�����ģ��Ŀ¼��Ӧ�û���������Դ��ͬһ��,����Դ��ȡ�����ļ���������չ����(memcached)�ж�ȡ��
** ע�⣺Ϊ���ڻ�����ʹ����չ�ľ�̬�������ڻ�����ʹ�ã���չ��::method�����ʹ�ã�self::method������֧����չ����
*/
!defined('M_COM') && exit('No Permission');
class cls_fcatalogbase{
	
	# ��ȡ���ã�ͨ���Ի���ķ�ʽ����ȡ
	# �����ȡ��ȫ���������飬ָ��ID�����ã�ָ��ID��KEY������
    public static function Config($fcaid = '',$Key = ''){
		$re = cls_cache::Read(cls_fcatalog::CacheName());
		if($fcaid){
			$fcaid = cls_fcatalog::InitID($fcaid);
			$re = isset($re[$fcaid]) ? $re[$fcaid] : array();
			if($Key){
				$re = isset($re[$Key]) ? $re[$Key] : '';
			}
		}
		return $re;
    }
	
	# �����������ȡ�ֶ�����
    public static function Field($fcaid = '',$FieldName = ''){
		$chid = cls_fcatalog::Config($fcaid,'chid');
		$re = cls_fchannel::Field($chid,$FieldName);
		return $re;
		
    }
	
	# �����༭/����/��������,��ʾ����ѡ��Multi-Select --- ��ʱ��������
    public static function areaShow($fcaid, $Values='', $re='Edit', $FormVar='fmdata[farea]', $Title='��������'){
		$cfg = cls_fcatalog::Config($fcaid); 
		if(empty($cfg['farea'])) return;
		$DataStr = '';  $DataArr = array('0' => array('title' => $Title ));
		$coclasses =  cls_catalog::Config((int)$cfg['farea']);
		foreach($coclasses as $k => $v){
			if(!empty($v['level'])) continue;
			$DataStr .= "[$k,$v[pid],'".addslashes($v['title'])."',".(empty($v['unsel']) ? 0 : 1) . '],';
			$DataArr[$k] = $v;
		}
		if($re=='Search'){ //����
			return "<select style=\"vertical-align: middle;\" id=\"$FormVar\" name=\"$FormVar\">".umakeoption($DataArr,$Values)."</select>";
		}elseif($re=='Sets'){ //��������
			$opMod = "<select id=\"mode_$FormVar\" name=\"mode_$FormVar\" style=\"vertical-align: middle;\">".makeoption(array(0 => '����',1 => '׷��',2 => '�Ƴ�',),1)."</select> &nbsp;";
			$opOpt = "<script>var data = [$DataStr];\n_08cms.fields.linkage('$FormVar', data, '$Values',20);</script>";
			trbasic("<input class=\"checkbox\" type=\"checkbox\" name=\"arcdeal[farea]\" value=\"1\">&nbsp;$Title",'arcfarea',$opMod.$opOpt,'');
		}else{ //Edit
			$validator = "<input type=\"hidden\" vid=\"$FormVar\" rule='must' />";
			$linkage = "<script>var data = [$DataStr];\n_08cms.fields.linkage('$FormVar', data, '$Values',20);</script>$validator";
			trbasic("<span style='color:#F00'>*</span> $Title",'',$linkage, '');	
		}
	}
	
	# ��ID���г�ʼ��ʽ��
    public static function InitID($fcaid = ''){
		$fcaid = empty($fcaid) ? '' : trim(strtolower($fcaid));
		return cls_string::ParamFormat($fcaid);
	}
	
	/**
     * ��ȡ�µ�fcaid
     * ���ڼ���֮ǰ�������ݵ�fcaid
     * 
     * @param  int    $fcaid ����ɵ�fcaid
     * @return string        �����µ�fcaid
     * 
     */
    public static function getNewFcaid( $fcaid )
    {
		if(is_numeric($fcaid)){
			return 'fcatalog' . (int)$fcaid;
		}else{
			return cls_string::ParamFormat($fcaid);
		}
    }
	
	# ��������
    public static function CacheName(){
		return 'fcatalogs';
    }
	
	# ���� ID=>���� ���б�����
	public static function fcaidsarr($chid = 0){
		$CacheArray = cls_cache::Read(cls_fcatalog::CacheName());
		$narr = array();
		foreach($CacheArray as $k => $v) if(!$chid || $chid == $v['chid']) $narr[$k] = $v['title']."($k)";
		return $narr;
	}
	
	# ��ʾ [��������]����Ŀ�б�Selectѡ��
	public static function fAreaCoType($val=''){
        $key = 'farea';
		$arr = array(0 => '����������');
		$cotypes = cls_cache::Read('cotypes'); 
        foreach($cotypes as $k => $v){ 
			if(empty($v['self_reg'])) $arr[$k] = "($k) - ".$v['cname'];
		}
		trbasic('����������ϵ',"fmdata[$key]",makeoption($arr,$val),'select');	
	}
	
	# ����¶����fcaid�Ƿ�Ϸ�
	public static function CheckNewID($fcaid = ''){
		if(!($fcaid = cls_fcatalog::InitID($fcaid))) return 'Ψһ��ʶ����Ϊ��';
		if(!preg_match("/[a-z]+\w+/",$fcaid)) return 'ͷ�ַ�ӦΪ��ĸ�������ַ�ӦΪ��ĸ�����ֻ�_';
		if(cls_fcatalog::InitialOneInfo($fcaid)) return 'ָ����Ψһ��ʶ��ռ��';
		return '';
	}
	
	# ���»��棬���ֶλ��������ṩ��cls_CacheFileʹ��
	public static function UpdateCache(){
		cls_fcatalog::SaveInitialCache();
	}
	
	# ����ģ���е���ȫ����Դ���൱�ڸ������ݱ�
	public static function SaveInitialCache($CacheArray = ''){
		if(!is_array($CacheArray)){ # ���Դ������������
			$CacheArray = cls_fcatalog::InitialInfoArray();
		}
		
		cls_Array::_array_multisort($CacheArray,'vieworder',true);# ��vieworder��������
		$CacheArray = cls_catalog::OrderArrayByPid($CacheArray,''); # ��pidΪ�ṹ��������
		
		cls_CacheFile::Save($CacheArray,cls_fcatalog::CacheName());
	}
	
	# ��̬���������飬ֱ�����Գ�ʼ����Դ(�����ݿ�/����)�����ں�̨����ʵʱ�ǻ������
	# pidΪ-1ʱ�������Ƹ�����
	public static function InitialInfoArray($pid = -1){
		
		$CacheArray = cls_cache::Read(cls_fcatalog::CacheName(),'','',1);
		if($pid != -1){
			$pid = cls_fcatalog::InitID($pid);
			foreach($CacheArray as $k => $v){
				if($v['pid'] != $pid) unset($CacheArray[$k]);
			}
		}
		return $CacheArray;
	}
	
	# ��̬�ĵ������ϣ�ֱ���Գ�ʼ����Դ(�����ݿ�/����)�����ں�̨����ʵʱ�ǻ������
	public static function InitialOneInfo($id){
		
		$id = cls_fcatalog::InitID($id);
		$CacheArray = cls_fcatalog::InitialInfoArray();
		return empty($CacheArray[$id]) ? array() : $CacheArray[$id];
		
	}
	# ���������һ�����õ���ʼ����Դ
	public static function ModifyOneConfig($nowID,$newConfig = array(),$isNew = false){
		
		$nowID = cls_fcatalog::InitID($nowID);
		if($isNew){
			$newConfig['title'] = trim(strip_tags(@$newConfig['title']));
			if(!$newConfig['title']) cls_message::show('�������ϲ���ȫ');
			if($re = cls_fcatalog::CheckNewID($nowID)) cls_message::show($re);
			$oldConfig = cls_fcatalog::_OneBlankInfo($nowID);
		}else{
			if(!($oldConfig = cls_fcatalog::InitialOneInfo($nowID))) cls_message::show('��ָ����ȷ�ĸ������ࡣ');
			$nowID = $oldConfig['fcaid'];
		}	
		
		# ��ʽ������
		if(isset($newConfig['pid'])){
			$newConfig['pid'] = cls_fcatalog::InitID($newConfig['pid']);
			if(!cls_fcatalog::InitialOneInfo($newConfig['pid'])) $newConfig['pid'] = '';
		}
		if(isset($newConfig['apmid'])){
			$newConfig['apmid'] = empty($newConfig['apmid']) ? 0 : (int)$newConfig['apmid'];
		}
		if(isset($newConfig['customurl'])){
			$newConfig['customurl'] = preg_replace("/^\/+/",'',trim($newConfig['customurl']));
		}
		
		# ��ֵ
		$InitConfig = cls_fcatalog::_OneBlankInfo($nowID); # ��ȫ�����ýṹ
		foreach($InitConfig as $k => $v){
			if(in_array($k,array('fcaid'))) continue;
			if(isset($newConfig[$k])){ # ����ֵ
				$oldConfig[$k] = $newConfig[$k];
			}elseif(!isset($oldConfig[$k])){ # �²����ֶ�
				$oldConfig[$k] = $v;
			}
		}		
		
		# ����
		$CacheArray = cls_fcatalog::InitialInfoArray();
		$CacheArray[$nowID] = $oldConfig;
		cls_fcatalog::SaveInitialCache($CacheArray);
		
		return $nowID;
		
	}
	
	public static function SetFtype($ftype = 0,array $IDs){
		$CacheArray = cls_fcatalog::InitialInfoArray();
		$ftype = empty($ftype) ? 0 : 1;
		foreach($IDs as $ID){
			if(!empty($CacheArray[$ID])){
				$CacheArray[$ID]['checked'] = 1;
				$CacheArray[$ID]['ftype'] = $ftype;
			}
		}
		cls_fcatalog::SaveInitialCache($CacheArray);
	}
	
	public static function DeleteOne($fcaid,$ForceDelete = 0){
		global $db,$tblprefix;
		
		$fcaid = cls_fcatalog::InitID($fcaid);
		if(!$fcaid || !($fcatalog = cls_fcatalog::InitialOneInfo($fcaid))) return '��ָ����ȷ�ĸ������ࡣ';
		if($ForceDelete){//ǿ��ɾ���������µ����и������ӷ���
			
			# ɾ������
			if($pInfoArray = cls_fcatalog::InitialInfoArray($fcaid)){
				foreach($pInfoArray as $k => $v){
					cls_fcatalog::DeleteOne($k,$ForceDelete);
				}
			}
			
			# ɾ����ǰ�����ڵĸ���
			$arc = new cls_farcedit;
			$query = $db->query("SELECT aid FROM {$tblprefix}farchives WHERE fcaid='$fcaid'");
			while($r = $db->fetch_array($query)){
				$arc->set_aid($r['aid']);
				$arc->arc_delete();
			}
		}else{
			if($pInfoArray = cls_fcatalog::InitialInfoArray($fcaid)){
				return '����ɾ�������ڵ��ӷ��ࡣ';
			}
			if($db->result_one("SELECT COUNT(*) FROM {$tblprefix}farchives WHERE fcaid='$fcaid'")){
				return '����ɾ�������ڵĸ�����';
			}
		}
		
		# ɾ����滺�棬ģ���ǩ��
		_08_Advertising::DelOneAdv($fcaid);
		
		# ɾ��������������
		$CacheArray = cls_fcatalog::InitialInfoArray();
		unset($CacheArray[$fcaid]);
		cls_fcatalog::SaveInitialCache($CacheArray);
	}
	
	# �����̨�����չ���˵�����ʾ
	public static function BackMenuCode(){
		$curuser = cls_UserMain::CurUser();
		$a_fcaids = $curuser->aPermissions('fcaids');
		
		//�������ࣺ����ʹ�ù���ڵ����ã�ǿ�Ƹ����������չʾ�ڵ�
		$fcatalogs = cls_cache::Read('fcatalogs');
		$fcaids = array_keys($fcatalogs);
		if(!in_array('-1',$a_fcaids)){//�����ɫȨ������
			$fcaids = array_intersect($fcaids,$a_fcaids);//��Ч�Ľڵ�
			$v_fcaids = $fcaids;//��Ҫչʾ�Ľڵ�
			foreach($fcaids as $v) if(!empty($fcatalogs[$v]['pid'])) $v_fcaids[] = $fcatalogs[$v]['pid'];//��Ч�ڵ���ϼ��ڵ���Ҫչʾ����
			$v_fcaids = array_unique($v_fcaids);
		}else $v_fcaids = $fcaids;
		
		$na = array();
		if(!$curuser->NoBackFunc('freeinfo')){ # �����ܹ�����Ȩ��
			$na[0] = array('title' => '�����ܹ�','level' => 0,'active' => 1,);
		}
		foreach($fcatalogs as $k => $v){
			if(!in_array($k,$v_fcaids)) continue;
			$na[$k] = array('title' => $v['title'],'level' => $v['pid'] ? 1 : 0,'active' => in_array($k,$fcaids) && $v['pid'] ? 1 : 0,);
		}
		return ViewBackMenu($na,1);
	}
	
	# �����̨����൥������Ĺ���ڵ�չʾ(ajax����)
	public static function BackMenuBlock($fcaid){
		$UrlsArray = cls_fcatalog::BackMenuBlockUrls($fcaid);
		return _08_M_Ajax_Block_Base::getInstance()->OneBackMenuBlock($UrlsArray);
	}
	
	
	# �����̨����൥������Ĺ���ڵ�url���飬���Ը�����Ҫ��Ӧ��ϵͳ������չ
	protected static function BackMenuBlockUrls($fcaid){
		$UrlsArray = array();
		$fcaid = cls_fcatalog::InitID($fcaid);
		if(!$fcaid){
			$UrlsArray['��������'] = "?entry=fcatalogs&action=fcatalogsedit";
			$UrlsArray['����ģ��'] = "?entry=fchannels&action=fchannelsedit";
		}elseif($fcatalog = cls_cache::Read('fcatalog',$fcaid)){
			if(!empty($fcatalog['pid'])){
				$suffix = $fcaid ? "&fcaid=$fcaid" : '';
				$TypeTitle = empty($fcatalog['ftype']) ? '����' : '���';
				$UrlsArray[$TypeTitle.'����'] = "?entry=extend&extend=farchives$suffix";
				$UrlsArray[$TypeTitle.'���'] = "?entry=extend&extend=farchiveadd$suffix";
				if(!empty($fcatalog['ftype'])){
					$UrlsArray['���ģ��'] = "?entry=extend&extend=adv_management&src_type=other$suffix";
				}
			}
		}
		return $UrlsArray;
	}
	
	# һ���½���¼�ĳ�ʼ������
	protected static function _OneBlankInfo($ID = 0){
		return array(
			'fcaid' => cls_fcatalog::InitID($ID),
			'title' => '',
			'pid' => '0',
			'vieworder' => '0',
			'chid' => '1',
			'autocheck' => '0',
			'apmid' => '0',
			'nodurat' => '0',
			'customurl' => '',
			'content' => '',
			'ftype' => '0',
			'farea' => '0',
			'params' => '',
			'checked' => '1',
		);
	}
	
	
}
