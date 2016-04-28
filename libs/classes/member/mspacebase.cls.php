<?php
/* 
** ��Ա�ռ�ר�õķ���(Ҳ�������������ָ����Ա�ķ���)����Mspace.cls.php�Ļ���
** ע�⣺Ϊ���ڻ�����ʹ����չ�ľ�̬�������ڻ�����ʹ�ã���չ��::method�����ʹ�ã�self::method������֧����չ��
*/
!defined('M_COM') && exit('No Permission');
class cls_Mspacebase{
	static protected $UcalssesArray = array(); # �ݴ治ͬ��Ա�ĸ��˷������ϣ��Ա�����
	static protected $McatalogsArray = array(); # �ݴ治ͬ�����Ŀռ���Ŀ���飬�Ա�����
	
	/**
	 * ��Ա�ռ���Ŀҳurl
	 *
	 * @param  array	$info		ָ����Ա��������Ϣ����
	 * @param  array	$params		ָ���ĸ������ԣ���mcaid(�ռ���Ŀ)��addno(����ҳ)��ucid(�ռ���Ŀ�ڵĸ��˷���)
	 * @param  bool		$dforce		ǿ�Ʒ��ض�̬��ʽ
	 * @return string      			���ػ�Ա�ռ�url
	 */
	public static function IndexUrl($info,$params = array(),$dforce = false){//$dforceǿ�ƶ�̬
		if(!$info['mid']) return '';
		if(!$dforce && array_diff_key($params,array('mid' => '','mcaid' => '','ucid' => '','addno' => '',))) $dforce = true;
		if(!$dforce && (empty($info['mspacepath']) || empty($info['msrefreshdate']))) $dforce = true; #δ���þ�̬Ŀ¼��δ���ɾ�̬
		$mindex = MspaceIndexFormat($info,$params,$dforce,1); 
		$mindex = $dforce ? cls_env::mconfig('cms_abs').$mindex : cls_url::view_url($mindex); //��̬ҳ��Ҫ������
		return $mindex; // cls_url::view_url(MspaceIndexFormat($info,$params,$dforce,1));
	}
	
	/**
	 * ��Ա�ռ��Ƿ�����̬
	 *
	 * @param  array	$info		ָ����Ա��������Ϣ����
	 * @return string      			���ز��������ɾ�̬��ԭ���������ɾ�̬ʱ����false
	 */
	public static function AllowStatic($info){
		if(empty($info['mid'])) return 'δָ����Ա';
		if(empty($info['mspacepath'])) return "��Ա{$info['mid']}δ���ÿռ侲̬Ŀ¼";
		$mspacepmid = cls_env::mconfig('mspacepmid');
		if(!$mspacepmid || cls_Permission::noPmReason($info,$mspacepmid)){ # �ռ侲̬Ȩ��
			return "��Ա{$info['mid']}û�����ɾ�̬�ռ��Ȩ��";
		}
		return false;
	}
	
	# ��Ա�ռ���ؿռ�����Ա���ϣ���ֱ����ģ������ԭʼ��ǩ����
	# $ttl�������ڣ���λ:��
	public static function LoadMember($mid = 0, $ttl = 60, $ischeck=1){
		global $db,$tblprefix;
		$re = array();
		if(!($mid = max(0,intval($mid)))) return $re;
		$checkstr = $ischeck ? "AND m.checked=1" : "";
		if($re = $db->fetch_one("SELECT m.*,s.* FROM {$tblprefix}members m INNER JOIN {$tblprefix}members_sub s ON s.mid=m.mid WHERE m.mid='$mid' $checkstr",$ttl)){
			if($InfoChannel = $db->fetch_one("SELECT * FROM {$tblprefix}members_{$re['mchid']} WHERE mid='$mid'",$ttl)){
				$re = array_merge($re,$InfoChannel);
			}
			$re['mspacehome'] = cls_Mspace::IndexUrl($re);
			cls_url::arr_tag2atm($re,'m'); # ת��html�ֶ��ڵĸ���url
		}
		return $re;
	}
	
	# ȡ�û�Ա�ռ���ĵ�����ҳģ��
	# $mtcid�ռ�ģ�巽��id��$chid�ĵ�ģ��id��$addno����ҳid
    public static function ArchiveTplname($mtcid = 0,$chid = 0,$addno = 0){
		if(!$mtcid) return '';
		$arctpls = cls_mtconfig::Config($mtcid,'arctpls');
		$addno = max(0,intval($addno));
		$chid = max(0,intval($chid));
		$type = $addno ? "ex$addno" : 'archive';
		return empty($arctpls[$type][$chid]) ? '' : $arctpls[$type][$chid];
    }
	
	# ȡ�û�Ա�ռ����Ŀҳģ��
	# $mtcid�ռ�ģ�巽��id��$Params:��Ҫ����mcaid,addno��ֵ
    public static function IndexTplname($mtcid = 0,$Params = array()){
		if(!($mtcid = max(0,intval($mtcid)))) return '';
		$_msTpls = cls_mtconfig::Config($mtcid,'setting');
		if(empty($Params['mcaid'])){ # ��ҳ
			$tplname = @$_msTpls[0]['index'];
		}else{ # ��Ŀҳ
			$tplname = @$_msTpls[$Params['mcaid']][empty($Params['addno']) ? 'index' : 'list'];
		}
		return $tplname ? $tplname : '';
    }
	
	# ��ȡָ����Ա�ĸ��˷�������
	# $ttl�������ڣ���λ:��
	public static function LoadUclasses($mid = 0,$ttl = 60){
		if(!($mid = max(0,intval($mid)))) return array();
		if(isset(self::$UcalssesArray[$mid])){
			return self::$UcalssesArray[$mid];
		}else{
			global $db,$tblprefix;
			$re = array();
			$na = $db->ex_fetch_array("SELECT * FROM {$tblprefix}uclasses WHERE mid='$mid' ORDER BY vieworder",$ttl);
			foreach($na as $v){
				$re[$v['ucid']] = $v;
			}
			self::$UcalssesArray[$mid] = $re;
			return $re;
		}
	}
	
	# ��ȡָ��ģ�巽���Ŀռ���Ŀ����
	public static function LoadMcatalogs($mtcid = 0){
		if(!($mtcid = max(0,intval($mtcid)))) return array();
		if(isset(self::$McatalogsArray[$mtcid])){
			return self::$McatalogsArray[$mtcid];
		}else{
			$re = array();
			if($_msTpls = cls_mtconfig::Config($mtcid,'setting')){
				if($mcatalogs = cls_mcatalog::Config()){
					$re = array_intersect_key($mcatalogs,$_msTpls);
				}
			}
			self::$McatalogsArray[$mtcid] = $re;
			return $re;
		}
	}
	
	# �ռ���Ŀҳ�в��������ԭʼ��ǩ���õ���������
	# $infoΪ�ռ�����Ա���ϣ�$Params����mcaid,ucid,addno��ҳ�����
	public static function IndexAddParseInfo($info = array(),$Params=array()){
		if(empty($info['mid'])) return array();
		$nowMcatalogs = cls_Mspace::LoadMcatalogs($info['mtcid']);
		if(!empty($Params['ucid'])){//��������
			$nowUclasses = cls_Mspace::LoadUclasses($info['mid']);
			if(!empty($nowUclasses[$Params['ucid']])){
				$re = $nowUclasses[$Params['ucid']];
				$re['mcatalog'] = @$nowMcatalogs[$re['mcaid']]['title'];
				$re['uclass'] = $re['title'];
			}
		}elseif(!empty($Params['mcaid'])){
			if(!empty($nowMcatalogs[$Params['mcaid']])){
				$re = $nowMcatalogs[$Params['mcaid']];
				$re['mcatalog'] = $re['title'];
				$re['uclass'] = '';
			}
		}else{
			$re = array('mcatalog' => '','uclass' => '',);
		}
		foreach(array(0,1) as $k){
			$Params['addno'] = $k; 
			$re['indexurl'.($k ? $k : '')] = cls_Mspace::IndexUrl($info,$Params);
		}
		return $re;
	}
	
	# ����(����)ָ����Ա($mid)�ľ�̬�ռ�
	public static function ToStatic($mid = 0){
		if(!($info = cls_Mspace::LoadMember($mid))) return 'δָ����Ա';
		if($re = cls_Mspace::AllowStatic($info)) return $re; # ָ����Ա�Ƿ��������ɾ�̬�ռ�
		
		$arr = array();
		
		# ���ɿռ���ҳ
		$arr[] = cls_MspaceIndex::Create(array('mid' => $mid,'inStatic' => true));
		
		# ���ɿռ���Ŀҳ
		$nowMcatalogs = cls_Mspace::LoadMcatalogs($info['mtcid']);
		foreach($nowMcatalogs as $k => $v){
			if(!empty($v['dirname'])){
				foreach(array(0,1) as $x){
					$arr[] = cls_MspaceIndex::Create(array('mid' => $mid,'mcaid' => $k,'ucid' => 0,'addno' => $x,'inStatic' => true));	
				}
			}
		}
		
		# ����ÿ����Ŀ�µĸ��˷���ҳ
		$nowUclasses = cls_Mspace::LoadUclasses($mid);
		foreach($nowUclasses as $k => $v){
			if(!empty($nowMcatalogs[$v['mcaid']]['dirname'])){
				foreach(array(0,1) as $x){
					$arr[] = cls_MspaceIndex::Create(array('mid' => $mid,'mcaid' => $v['mcaid'],'ucid' => $k,'addno' => $x,'inStatic' => true));	
				}
			}
		}
		
		# ͳ��������Ϣ
		$num = 0;$size = 0;$time = 0;
		foreach($arr as $k => $v){
			if(empty($v['error'])){
				$num += $v['num'];
				$size += $v['size'];
				$time += $v['time'];
			}
		}
		return "������ $num ���ĵ���$size �ֽڣ���ʱ $time ��";
	}
	
	
	
}
