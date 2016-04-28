<?php
/**
* �й���Ŀ(coid=0)�����Ĵ�����
* ����Ϊcls_catalog�Ļ���
* �Ժ�滮�����ֳ�cls_CatalogConfig��Ϊ��̨��Ŀ����ר�ã��򻯵�ǰ���࣬����ǰ̨���á�
** ע�⣺Ϊ���ڻ�����ʹ����չ�ľ�̬�������ڻ�����ʹ�ã���չ��::method�����ʹ�ã�self::method������֧����չ��
*/
class cls_catalogbase{
	
	# ��ȡ���ã�ͨ���Ի���ķ�ʽ����ȡ
	# �����ȡ��ȫ���������飬ָ��ID�����ã�ָ��ID��KEY������
    public static function Config($coid = 0,$ccid = 0,$Key = ''){
		$coid = (int)$coid;
		$re = $coid ? cls_cache::Read('coclasses',$coid) : cls_cache::Read('catalogs');
		if($ccid){
			$ccid = (int)$ccid;
			$re = isset($re[$ccid]) ? $re[$ccid] : array();
			if($Key){
				$re = isset($re[$Key]) ? $re[$Key] : '';
			}
		}
		return $re;
    }
	public static function Table($coid = 0,$NoPre = false){
		$coid = (int)$coid;
		return ($NoPre ? '' : '#__').($coid ? "coclass$coid" : 'catalogs');
	}
	
	# ���� ID=>���� ���б����飬��ԴΪ��Ŀ�������������
	public static function ccidsarrFromArray(array $SourceArray,$chid = 0,$nospace = 0){
		$re = array();
		foreach($SourceArray as $k => $v){
			if(!$chid || in_array($chid,explode(',',$v['chids']))){
				if(!$nospace){
					$v['title'] = str_repeat('&nbsp; &nbsp; ',$v['level']).$v['title'];
				}
				$re[$k] = $v['title'];
			}
		}
		return $re;
	}
	
	# ���ͷ��ൽָ������λ
	public static function push($coid,$ccid,$paid){
		if($Config = cls_catalog::Config($coid,$ccid)){
			return cls_pusher::push($Config,$paid);
		}else return false;
	}
	
	# ���� ID=>���� ���б����飬��ԴΪָ������ϵID
	public static function ccidsarr($coid = 0,$chid = 0,$nospace = 0){
		$coid = (int)$coid;
		if($coid){
			$cotypes = cls_cache::Read('cotypes');
			if(empty($cotypes[$coid])) return array();
			if($cotypes[$coid]['self_reg']) $chid = 0;
		}
		$SourceArray = cls_catalog::Config($coid);
		return cls_catalog::ccidsarrFromArray($SourceArray,$chid,$nospace);
	}
	
	public static function Key($coid = 0){
		$coid = (int)$coid;
		return $coid ? 'ccid' : 'caid';
	}
	
	# ���»���
	public static function UpdateCache($coid = 0){
		$coid = (int)$coid;
		$CacheArray = cls_catalog::CacheArray($coid);
		$cndirnames = $cnsonids = array();
		foreach($CacheArray as $k => $v){
			if(empty($k) || (!empty($v['pid']) && empty($CacheArray[$v['pid']]))){//�����౻�رջ����ݿ��ֶ�ɾ��
				unset($CacheArray[$k]);
				continue;
			}
			$TopID = cls_catalog::cn_upid($k,$CacheArray);
			$cndirnames[$k] = array('s' => $v['dirname']);
			if($TopID != $k) $cndirnames[$k]['p'] = $CacheArray[$TopID]['dirname'];
			if(!empty($v['customurl'])) $cndirnames[$k]['u'] = $v['customurl'];
			if($pids = cls_catalog::PccidsByAarry($k,$CacheArray)){
				foreach($pids as $x){
					$cnsonids[$x][] = $k;
				}
			}
		}
		foreach($cnsonids as $k => $v){
			$cnsonids[$k] = implode(',',$v);
		}
		cls_CacheFile::Save($cnsonids,"cnsonids$coid");
		cls_CacheFile::Save($cndirnames,"cndirnames$coid");
		cls_CacheFile::Save(cls_catalog::DirnameArray(),"cn_dirnames");
		cls_CacheFile::Save($CacheArray,$coid ? "coclasses$coid" : 'catalogs');
		
		
	}
	# �����ݿ������ɻ�������Ҫ������
	public static function CacheArray($coid = 0){
		$coid = (int)$coid;
		$CacheArray = array();
		$db = _08_factory::getDBO();
		$db->select('*')->from(cls_catalog::Table($coid))->where(array('closed' => 0))->order('trueorder,'.cls_catalog::Key($coid))->exec();
		$UnsetVars = cls_catalog::_CacheUnsetVars($coid);
		while($r = $db->fetch()){
			if($coid){
				cls_CacheFile::ArrayAction($r,'conditions','unserialize');
			}
			
			if(!empty($UnsetVars['Del'])){
				foreach($UnsetVars['Del'] as $z){
					unset($r[$z]);
				}
			}
			if(!empty($UnsetVars['DelEmpty'])){
				foreach($UnsetVars['DelEmpty'] as $z){
					if(empty($r[$z])) unset($r[$z]);
				}
			}
			cls_url::arr_tag2atm($r,$coid ? 'cc' : 'ca');
			$CacheArray[$r[cls_catalog::Key($coid)]] = $r;
		}
		return $CacheArray;
	}
	
	# ��̬���������飬ֱ�����Գ�ʼ����Դ(�����ݿ�/����)�����ں�̨����ʵʱ�ǻ������
	# �����ӽṹ��������
	public static function InitialInfoArray($coid = 0,$IncludeClosed = 0,$orderby = 'trueorder'){
		$coid = (int)$coid;
		$re = array();
		$db = _08_factory::getDBO();
		$db->select('*')->from(cls_catalog::Table($coid));
		if(!$IncludeClosed) $db->where(array('closed' => 0));
		$db->order($orderby.','.cls_catalog::Key($coid))->exec();
		$TrueOrderArray = array();
		$NeedOrder = $orderby != 'trueorder' ? true : false; # ���ɻ���ʱǿ����������
		while($r = $db->fetch()){
			if($coid){
				cls_CacheFile::ArrayAction($r,'conditions','unserialize');
			}			
			
			$re[$r[cls_catalog::Key($coid)]] = $r;
			
			# �����Ƿ���Ҫ������������Ѿ��ź���trueorder��Ψһ�ģ�������Ҫ��������
			if(!$NeedOrder){
				if(in_array($r['trueorder'],$TrueOrderArray)){
					$NeedOrder = true;
				}else $TrueOrderArray[] = $r['trueorder'];
			}
		}
		
		if($NeedOrder){ # ��Ҫ��������
			$re = cls_catalog::OrderArrayByPid($re,0);
		}
		return $re;
	}
	# ��̬�ĵ������ϣ�ֱ���Գ�ʼ����Դ(�����ݿ�/����)�����ں�̨����ʵʱ�ǻ������
	public static function InitialOneInfo($coid = 0,$id = 0){
		if(!($id = (int)$id)) return array();
		$coid = (int)$coid;
		$db = _08_factory::getDBO();
		$re = $db->select('*')->from(cls_catalog::Table($coid))->where(array(cls_catalog::Key($coid) => $id))->exec()->fetch();
		return $re ? $re : array();
	}
	/**
	 * ����������$SourceArray�е�����ID�������¼�Ƕ�׵Ĺ�ϵ(pid)�����������򣬷�������������ID����/��ϸ��������
	 * ע�⣺�������ȽϷ���Դ(���ܰ��������϶��ѭ����ݹ�)����ע��ʹ�ó��ϣ�ͨ�����ڷǻ��淽ʽ�����ݴ���
	 *
	 * @param  array  $SourceArray  	��Դ���飬��Ҫ��pid����ʾ���ӽṹ������
	 * @param  int/string    $Pid				ָ����id,ͨ��0-�Ӷ�����ʼ
	 * @param  int    $OnlyReturnID		Ϊ1ʱ��������ID���飬���򷵻���ϸ��������
	 * @return array  $OrderIDs  		���ؽ������
	 */
	public static function OrderArrayByPid(array $SourceArray,$Pid = 0,$OnlyReturnID = 0){
		$OrderArray = array();
		foreach($SourceArray as $k => $v){
			if($v['pid'] == $Pid){
				if($OnlyReturnID){
					$OrderArray[] = $k;
				}else{
					$OrderArray[$k] = $v;
				}
				if($re = cls_catalog::OrderArrayByPid($SourceArray,$k,$OnlyReturnID)){
					foreach($re as $_k => $_v){
						if($OnlyReturnID){
							$OrderArray[] = $_v;
						}else{
							$OrderArray[$_k] = $_v;
						}
					}
				}
			}
		}
		return $OrderArray;
	}
	
	/**
	 * ��$SourceArray��ȡ��$Pid�����ӷ���ID��
	 * ע�⣺�������ȽϷ���Դ(���ܰ��������϶��ѭ����ݹ�)����ע��ʹ�ó��ϣ�ͨ�����ڷǻ��淽ʽ�����ݴ���
	 *
	 * @param  array  $SourceArray  ��Դ���飬��������pid��¼�˸�id����
	 * @param  int    $Pid		    ָ���ĸ�ID
	 * @return array  				���ذ�������ID���������ӷ���ID(�ݹ�)
	 */
	public static function cnsonids($Pid,$SourceArray){
		if(!$Pid) return array();
		return array_merge(array($Pid),cls_catalog::OrderArrayByPid($SourceArray,$Pid,1));
	}
    /**
     * ȡ����Ŀ������ϵ��Ŀ��ռ�õ�dirname(��̬·��)����
     * 
     * @return array  				������Ŀ������ϵ��Ŀ��ռ�õ�dirname����
     */ 
	public static function DirnameArray(){
        $db = _08_factory::getDBO();
        $tblprefix = cls_envBase::getBaseIncConfigs('tblprefix');
		$cotypes = cls_cache::Read('cotypes');
		$vars = array_keys($cotypes);
		$vars[] = 0;
		$ret = array();
		foreach($vars as $k => $v){
			$query = $db->query("SELECT dirname FROM $tblprefix".($k ? "coclass$k" : "catalogs"),'SILENT');
			while($r = @$db->fetch_array($query)) in_array($r['dirname'],$ret) || $ret[] = $r['dirname'];
		}
		return $ret;
	}

	/**
	 * ˵����ͨ���Զ�������ϵ ��sql�Ӿ�
	 * Demo: self_sqlstr(5,180,'a.')  -=>  ((a.zj >= '1000' and a.zj <= '2000'))
	 *
	 * @param  int     $coid   ��ϵ��ĿID
	 * @param  int     $ccids  ��ϵID
	 * @param  string  $pre    sql�ֶ�ǰ׺,��a.
	 * @return string  $sqlstr ��ɺ��sql�Ӿ�
	 */
	public static function SelfClassSql($coid,$ccids,$pre = ''){
		global $timestamp;
		$sqlstr = '';
		if(empty($ccids)) return $sqlstr;
		if(!is_array($ccids)) $ccids = array($ccids);
		$multi = 0;
		foreach($ccids as $ccid){
			$sqlstr1 = '';
			if(!($coclass = cls_cache::Read('coclass',$coid,$ccid)) || empty($coclass['conditions'])) continue;
			foreach(array('createdate','clicks','prices',) as $var){
				if(isset($coclass['conditions'][$var.'from'])) $sqlstr1 .= ($sqlstr1 ? ' AND ' : '').$pre.$var.">='".$coclass['conditions'][$var.'from']."'";
				if(isset($coclass['conditions'][$var.'to'])) $sqlstr1 .= ($sqlstr1 ? ' AND ' : '').$pre.$var."<'".$coclass['conditions'][$var.'to']."'";
			}
			if(isset($coclass['conditions']['indays'])) $sqlstr1 .= ($sqlstr1 ? ' AND ' : '').$pre."createdate>='".($timestamp - 86400 * $coclass['conditions']['indays'])."'";
			if(isset($coclass['conditions']['outdays'])) $sqlstr1 .= ($sqlstr1 ? ' AND ' : '').$pre."createdate<'".($timestamp - 86400 * $coclass['conditions']['outdays'])."'";
			if(isset($coclass['conditions']['sqlstr'])){
				$coclass['conditions']['sqlstr'] = stripslashes(str_replace('{$pre}',$pre,$coclass['conditions']['sqlstr']));
				$sqlstr1 .= ($sqlstr1 ? ' AND ' : '').'('.$coclass['conditions']['sqlstr'].')';
			}
			($sqlstr1 && $sqlstr) && $multi = 1;
			$sqlstr1 && $sqlstr .= ($sqlstr ? ' OR ' : '').'('.$sqlstr1.')';
		}
		$multi && $sqlstr = '('.$sqlstr.')';
		return $sqlstr;
	}
    /**
     * ׷��ָ������������ϼ�id
     * 
     * @param int		$ccid		ָ������id
     * @param string	$coid		ָ����ϵid������Ŀ��Ϊ0
     * @param string	$self		�Ƿ����ָ��id����
     * @return array  				�������и�id���飬�������϶���
     */ 
	public static function Pccids($ccid = 0,$coid = 0,$self = 0){
		$re = array();
		if(!$ccid) return $re;
		if($arr = cls_catalog::Config($coid)){
			$ccid0 = $ccid;
			for($i = @$arr[$ccid0]['level']; $i > 0; $i--) $re[] = $ccid = $arr[$ccid]['pid'];
			count($re) > 1 && $re = array_reverse($re);
			if($self == 1) $re[] = $ccid0;
		}
		return $re;
	}
	
    /**
     * ��д��Ŀ���tureorder�����ֶ�
     * 
     * @param string	$coid		ָ����ϵid������Ŀ��Ϊ0
     */ 
	public static function DbTrueOrder($coid=0){
		$coid = (int)$coid;
		$na = cls_catalog::InitialInfoArray($coid,1,'vieworder');
		$db = _08_factory::getDBO();
		$i = 0;
		foreach($na as $k => $v){
			if($v['trueorder'] != $i){
				$db->update(cls_catalog::Table($coid),array('trueorder' => $i))->where(array(cls_catalog::Key($coid) => $k))->exec();
			}
			$i ++;
		}
	}
	
    /**
     * ָ��id�������ϼ�id��ͨ�������ԭʼ�����ȡ��
     * 
     * @param int		$ccid		ָ������id
     * @param array		$cnArray	��Ŀ��ĳ��ϵ����Ļ�������
     * @return array  				�������и�id���飬�������¶���
     */ 
	public static function PccidsByAarry($ccid,$cnArray = array()){
		$re = array();
		while(isset($cnArray[$ccid]['pid'])){
			$re[] = $ccid = $cnArray[$ccid]['pid'];
		}
		return $re;
	}
		
	/**
	 * ȡ��ָ����Ŀ�������¼�id(���¼�)
	 *
	 * @param int $nowid ��ǰ��Ŀid
	 * @param int $coid ��ϵid��0ָ��Ŀ
	 * @return array ����$nowid�������¼���Ŀ
	 */
	public static function son_ccids($nowid,$coid = 0){
		$re = array();
		if(!$nowid) return $re;
		if($sonids = sonbycoid($nowid,$coid,0)){
			$na = cls_catalog::Config($coid);
			foreach($sonids as $k){
				if(@$na[$k]['pid'] == $nowid) $re[] = $k;
			}
		}
		return $re;
	}
	
	/**
	 * ȡ��ĳ����Ŀ��ָ����(level)���ϼ���Ŀ
	 *
	 * @param int $nowid ��ǰ��Ŀid
	 * @param int $coid ��ϵid��0ָ��Ŀ
	 * @param int $level �ڼ�����0ָ����
	 * @return int ���ص�$level�����ϼ���Ŀid
	 */
	public static function p_ccid($nowid,$coid = 0,$level = 0){
		if(!$nowid) return 0;
		if(!($na = cls_catalog::Config($coid))) return 0;
		return cls_catalog::cn_upid($nowid,$na,$level);
	}
	
	/**
	 * ˵��������ָ���ڼ���(level)�ĸ�id��
	 *
	 * @param  int      $id     
	 * @param  array    &$arr   
	 * @param  int      $level����level=0(����)����ʾ����ָ��id�Ķ�����id����id����Ϊ����ʱ������id����
	 * @return int      ---      
	 */
	public static function cn_upid($id,&$arr,$level=0){
		if(empty($arr[$id])) return 0;
		return $arr[$id]['level'] < $level ? 0 : (empty($arr[$id]['pid']) || $arr[$id]['level'] == $level ? $id : cls_catalog::cn_upid($arr[$id]['pid'],$arr,$level));
	}

	/**
	 * ��ȡ��ϵ���ƻ�ͼ��
	 *
	 * @param  int    $id        ��ϵid
	 * @param  bool   $mode      �Ƿ�Ϊ��ѡ��ʽ
	 * @param  array  $sarr      ��ϵ����
	 * @param  int    $num       �����ٸ�
	 * @param  bool   $showmode  �Ƿ�Ϊͼ��
	 * @return strin  $ret       ��ϵ���ƻ�ͼ��
	 */
	public static function cnstitle($id,$mode,$sarr,$num=0,$showmode=0){
		if(!$id || !$sarr) return '';
		if(!$mode && !$showmode) return @$sarr[$id]['title'];
		$ids = array_filter(explode(',',$id));
		$ret = '';$i = 0;
		foreach($ids as $k){
			if($num && $num >= $i) break;
			$ret .= $showmode ? '<img src="'.@$sarr[$k]['icon'].'" title="'.@$sarr[$k]['title'].'" width="20" height="20" />' : ','.@$sarr[$k]['title'];
		}
		return $showmode ? $ret : substr($ret,1);
	}
	
	// ������ֻ��һ��ѡ��ʱ�����ض�����𣬿���ѡһ��select��
	// ��û����������������������ѡ,�����Ҳ��ѡ��
	// һ������ _08cms.fields.linkage()���༶����...
	public static function uccidstop(&$arr){
		if(empty($arr)) return;
		$f0 = 0; $f1 = 0;
		foreach($arr as $k=>$v){ 
			if(empty($v['pid'])) $f0++;
			if($v['pid']>0) $f1++;
			if($f0>1) break;
		} 
		$pid = 0; 
		if($f0==1 && $f1>0){  //ֻ�и���������Һ��������
			foreach($arr as $k=>$v){
				if(empty($v['pid'])){ 
					if(empty($v['unsel'])) break; //�����ѡ�������������������ˡ�
					unset($arr[$k]);
					$pid = $k;
				}else{ 
					if($pid && $v['pid']==$pid){ 
						$arr[$k]['pid'] = 0;
					}
				}
			}
		} //print_r($arr);
	}
	
	/**
	 * ��ȡ������������ϵ/��Ŀ����
	 *
	 * @param  int    $coid     ��ϵ��Ŀid
	 * @param  int    $chid     ģ��ID
	 * @param  int    $framein  �����ṹ�Է���
	 * @param  int    $nospace  �Ƿ�ӿո�'&nbsp; '
	 * @param  int    $viewp    0-����catahidden�����Ч��Ŀ��1-��Ҫpid���ϣ��������Ч��Ŀ����Ϊunsel,-1��ȫ�����Ч��Ŀ
	 * @param  int    $id       ָ������ĿIDֵ������ϵIDֵ��20121204 ����
	 * @return array  $caccnt   ���ص����飬������id��
	 */
	public static function uccidsarr($coid,$chid = 0,$framein = 0,$nospace = 0,$viewp = 0,$id=0){
		global $catahidden;
		$cotypes = cls_cache::Read('cotypes');
		$rets = array();
		if($coid && empty($cotypes[$coid])) return $rets;
		if($id){
			$idsr = sonbycoid($id ,!$coid?0:$coid,1);
			#$idsr = sonbycoid($id ,!$coid?0:$coid,0); //�ҳ�ָ����Ŀ����ϵ��������Ŀ������ϵ(��������ID)
			//��ȡ��ID�ĳ�ʼ����
			#$r = cls_cache::Read('catalog',$id,'');
			#$level = $r['level'];
		}
	
		$arr = cls_catalog::Config($coid);
		foreach($arr as $k => $v){
			$ccprefix = '';
			if($id){
				#$levtemp = $level;   //ָ����Ŀ�ĳ�ʼ����
				if(in_array($k,$idsr)){
					if(isset($v['letter']) && $coid && $v['pid']==0 && $v['letter']){
						$ccprefix = $v['letter'].' ';
					}
					$rets[$k]['title'] = ($nospace ? '' : str_repeat('&nbsp; ',$v['level'])).$ccprefix.$v['title'];
					$ids = !empty($v['chids']) ? explode(',',$v['chids']) : array();
					if(($chid && !in_array($chid,$ids)) || (!$framein && $v['isframe'])){//����ѡ����Ŀ
						if((!$catahidden && $viewp != -1) || $viewp == 1){
							$rets[$k]['unsel'] = 1;
						}else unset($rets[$k]);
					}
					if($viewp == 1){   //��ʾpid
						 $rets[$k]['pid'] = $v['pid'];
						 //��ָ��ID��pid��ֵΪ0
						 $rets[$id]['pid'] = 0;
					}
					#$rets[$k]['pid'] = $v['pid'];
					//��ʼ����+1 ָ����һ�����𣬽���ID��Ϊ0
					#$rets[$k]['level'] = $v['level'];
					#if($rets[$k]['level'] == ($levtemp+1)) $rets[$k]['pid'] = 0;
					//��ָ�������pid��ֵΪ0
	
				}
			}else{
				if(isset($v['letter']) && $coid && $v['pid']==0 && $v['letter']){
					$ccprefix = $v['letter'].' ';
				}
				$rets[$k]['title'] = ($nospace ? '' : str_repeat('&nbsp; ',$v['level'])).$ccprefix.$v['title'];
				$ids = !empty($v['chids']) ? explode(',',$v['chids']) : array();
				if(($chid && !in_array($chid,$ids)) || (!$framein && $v['isframe'])){//����ѡ����Ŀ
					if((!$catahidden && $viewp != -1) || $viewp == 1){
						$rets[$k]['unsel'] = 1;
					}else unset($rets[$k]);
				}
				if($viewp == 1) $rets[$k]['pid'] = $v['pid'];
			}
		}
		return $rets;
	}
	# ����������Ҫ�ų����ֶ�
	protected static function _CacheUnsetVars($coid = 0){
		$coid = (int)$coid;
		$UnsetVars = array();
		
		# ɾ��Ϊ�յ�ֵ
		if($coid){
			$UnsetVars['DelEmpty'] = array('groups','trueorder','closed','conditions',);
		}else{
			$UnsetVars['DelEmpty'] = array('trueorder','closed','dpmid','ftaxcp',);
		}
		
		# �����Ƿ�Ϊ�գ�ǿ��ɾ��
		$UnsetVars['Del'] = array();
		
		return $UnsetVars;
	}
		
}
