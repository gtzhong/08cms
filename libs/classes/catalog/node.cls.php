<?php
/**
* �й���Ŀ�ڵ㡢��Ա�ڵ�Ĵ�����
* 
*/
defined('M_COM') || exit('No Permission');
class cls_node{

	/**
	 * ���ݽڵ�$cnstr�����ؽڵ�����
	 * Demo: cnode_cname('caid=95&ccid1=93')  -=>  ����=>�Ҿ�
	 *
	 * @param  string  $cnstr ����
	 * @return string  $ret   ���صĽڵ�����
	 */
	public static function cnode_cname($cnstr){
		parse_str($cnstr,$idsarr);
		$ret = '';
		foreach($idsarr as $k => $v){
			$item = $k == 'caid' ? cls_cache::Read('catalog',$v) : cls_cache::Read('coclass',str_replace('ccid','',$k),$v);
			$ret .= ($ret ? '=>' : '').@$item['title'];
		}
		unset($item,$idsarr);
		return $ret;
	}
	
	
	/**
	 * ���ݽڵ��ִ������ػ�Ա�ڵ���Ϣ
	 * Demo: read_mcnode('ccid1=210')
	 *
	 * @param  string  $cnstr �ڵ��ִ�
	 * @return array   $ret   ���صĽڵ���Ϣ(�������ƣ�ģ��������Ϣ)
	 */
	public static function read_mcnode($cnstr){
		$arr = cls_cache::Read('mcnodes');
		if(empty($arr[$cnstr])) return array();
		$ret = $arr[$cnstr];
		unset($arr);
		return LoadMcnodeConfig($ret);
	}
	
	/**
	 * ���ݻ�Ա�ڵ���Ϣ���õ��ڵ��ִ�
	 *
	 * @param  array   $temparr ��Ա�ڵ���Ϣ
	 * @return string  $cnstr   �ڵ��ִ�
	 */
	public static function mcnstr($temparr){
		$cotypes = cls_cache::Read('cotypes');
		$grouptypes = cls_cache::Read('grouptypes');
		$vararr = array('caid','mcnid');
		foreach($cotypes as $k => $v) !$v['self_reg'] && $vararr[] = 'ccid'.$k;
		foreach($grouptypes as $k => $v) !$v['issystem'] && $vararr[] = 'ugid'.$k;
		$cnstr = '';
		foreach($temparr as $k => $v){
			if(in_array($k,$vararr) && $v = max(0,intval($v))){
				$cnstr = $k.'='.$v;
				break;
			}
		}
		return $cnstr;
	}
	
	/**
	 * ���ݽڵ��ִ������ؽڵ���Ϣ
	 *
	 * @param   string  $cnstr   �ڵ��ִ�
	 * @param  int		$NodeMode  �Ƿ��ֻ��ڵ�
	 * @return  array   $cnode 		�ڵ���Ϣ
	 */
	public static function cnodearr($cnstr,$NodeMode = 0){
		if(!($cnode = self::read_cnode($cnstr,$NodeMode))) return array();
		cls_url::view_cnurl($cnstr,$cnode);
		return $cnode;
	}
	
	/**
	 * ���ݻ�Ա�ڵ��ִ������ؽڵ���Ϣ
	 *
	 * @param  string  $cnstr  �ڵ�key��(ccid1=210)
	 * @return array   $cnode  �ڵ���Ϣ
	 */
	public static function mcnodearr($cnstr){ 
		if(!($cnode = self::read_mcnode($cnstr))){
			return array();
		}
		$cnode['cname'] = $cnode['alias'];
		cls_url::view_mcnurl($cnstr,$cnode);
		return $cnode;
	}
	
	/**
	 * ���ݽڵ��ִ������ؽڵ���Ϣ
	 * Demo: cls_node::read_cnode('ccid1=210')
	 *
	 * @param  string  $cnstr �ڵ��ִ�
	 * @return array   $ret   ���صĽڵ���Ϣ(�������ƣ�ģ��������Ϣ)
	 */
	public static function read_cnode($cnstr,$NodeMode = 0){
		if(!$cnstr) return array();
		$na = cls_cache::Read($NodeMode ? 'o_cnodes' : 'cnodes');
		if(empty($na[$cnstr])) return array();
		$re = $na[$cnstr];
		$re['nodemode'] = $NodeMode;//���Ƿ��ֻ��ڵ���Ϊ�ڵ��ڵı��
		return LoadCnodeConfig($re);
	}
	
	/**
	 * �õ���Ŀ�ڵ����ɾ�̬���ļ���ʽ(���ϵͳ��Ŀ¼)����ʽ��Ψһ����{page}(ҳ��)������ҳʱ����
	 *
	 * @param  string  $cnstr  �ڵ��ִ�
	 * @param  int     $addno  ����ҳ
	 * @return array   &$cnode �ڵ�������Ϣ
	 * @return string  ��Ŀ�ڵ����ɾ�̬���ļ���ʽ
	 */
	public static function cn_format($cnstr,$addno,&$cnode){//��{$page}�Ľڵ��ļ�(���ϵͳ��Ŀ¼)
		global $cn_urls;
		if(!$cnstr || !$cnode || !empty($cnode['NodeMode'])) return '';
		if(!isset($cnode['_cf'])){
			$cndirarr = CnodeFormatDirArray($cnstr);
			for($i = 0;$i <= @$cnode['addnum'];$i ++){
				$u = empty($cnode['cfgs'][$i]['url']) ? (empty($cn_urls[$i]) ? '{$cndir}/index'.($i ? $i : '').'_{$page}.html' : $cn_urls[$i]) : $cnode['cfgs'][$i]['url'];
				$cnode['_cf'][$i] = cls_url::m_parseurl($u,$cndirarr);
			}
		}
		return isset($cnode['_cf'][$addno]) ? $cnode['_cf'][$addno] : '';
	}
	
	/**
	 * ���ݻ�Ա�ڵ��ִ����õ��ڵ�����(�������Զ���ڵ�)
	 *
	 * @param  string  $cnstr  �ڵ��ִ�
	 * @return string  $title  �ڵ�����
	 */
	public static function mcnode_cname($cnstr){
		$arr = explode('=',$cnstr);
		if(!($mcnvar = trim(@$arr[0])) || !($mcnid = max(0,intval(@$arr[1]))) || ($mcnvar == 'mcnid')) return '';
		if($mcnvar == 'caid'){
			$tvar = 'title';
			$narr = cls_cache::Read('catalogs');
		}elseif(in_str('ccid',$mcnvar)){
			$tvar = 'title';
			$narr = cls_cache::Read('coclasses',str_replace('ccid','',$mcnvar));
		}elseif(in_str('ugid',$mcnvar)){
			$tvar = 'cname';
			$narr = cls_cache::Read('usergroups',str_replace('ugid','',$mcnvar));
		}
		return $narr[$mcnid][$tvar];
	}


	/**
	 * ˵����
	 *
	 * @param  string  $cnstr  �ڵ��ִ�
	 * @param  int     $addno  ����ҳ
	 * @return ---     ---     ---
	 */
	public static function mcn_format($cnstr = '',$addno = 0){//��{$page}�Ľڵ��ļ�(���ϵͳ��Ŀ¼)
		global $memberdir,$homedefault;
		if(!$cnstr) return $memberdir.'/'.$homedefault;
		$cnode = self::read_mcnode($cnstr);
		return $memberdir.'/'.cls_url::m_parseurl(empty($cnode['cfgs'][$addno]['url']) ? '{$cndir}/index'.($addno ? $addno : '').'_{$page}.html' : $cnode['cfgs'][$addno]['url'],array('cndir' => mcn_dir($cnstr),));
	}
	
	/**
	 * ȡ�����й�����Ŀ��ID�����⣬��ͨ��$listbyָ������һ����Ŀȡ������ȫ����
	 * ��caid=2&ccid1=5Ϊ����$listbyΪ-1ʱ�������һ����Ŀ(ccid1=5)Ϊ��ȫ���ϣ�0����Ŀ(caid=2)Ϊ��ȫ���ϣ�1�����(ccid1=5)Ϊ��ȫ����
	 *
	 * @param  string  $cnstr   �ڵ��ִ�
	 * @param  int     $listby  ������Ϣѡ�� -1�����һ����ĿΪ��ȫ����,0ָ������Ŀ��x(����)��ĳ��ϵ(x)�еķ���Ϊ��ȫ����
	 * @return array   $re      
	 */
	public static function cn_parse($cnstr,$listby=-1){
		parse_str($cnstr,$idsarr);
		$num = count($idsarr);
		$re = array();
		$i = 0;
		foreach($idsarr as $k => $v){
			$i ++;
			$coid = $k == 'caid' ? 0 : intval(str_replace('ccid','',$k));
			if($item = $coid ? cls_cache::Read('coclass',$coid,$v) : cls_cache::Read('catalog',$v)){
				$re[$coid ? "ccid$coid" : 'caid'] = $v;//id
				$re[$coid ? 'ccid'.$coid.'title' : 'catalog'] = $item['title'];//����
				if((($listby == -1) && $i == $num) || (($listby >=0) && $listby == $coid)){//��ȫ����
					$re += $item;
				}
			}
		}
		return $re;
	}
	
	/**
	 * �г���Ա�ڵ����� ����/��ϵ/��ϵ ��Ŀ�����Ϣ
	 *
	 * @param  string  $cnstr   �ڵ��ִ�
	 * @return array   $ret     ����/��ϵ/��ϵ �����Ϣ
	 */
	public static function m_cnparse($cnstr){//�õ���ʼ������
		$var = array_map('trim',explode('=',$cnstr));
		$ret = array($var[0] => $var[1]);
		if($var[0] == 'mcnid'){
		}elseif($var[0] == 'caid'){
			$ret += cls_cache::Read('catalog',$var[1],0);
		}elseif(in_str('ccid',$var[0])){
			$ret += cls_cache::Read('coclass',str_replace('ccid','',$var[0]),$var[1]);
		}elseif(in_str('ugid',$var[0])){
			$ret += cls_cache::Read('usergroup',str_replace('ugid','',$var[0]),$var[1]);
		}
		if(empty($ret['cname'])) $ret['cname'] = @$ret['title'];
		return $ret;
	}
	
	/**
	 * ����Ŀ�ڵ��е����ֵ���ݸ�������������$item
	 *
	 * @param  array   &$item  �����������飬��ǰ̨Ϊԭʼ��ʶ��������Դ
	 * @param  string  $cnstr  �ڵ��ִ�
	 * @param  array   &$cnode �ڵ�������Ϣ����ǰ��Ҫ�Ѿ����ɽڵ������ҳ��url
	 * @return ---     ---     ---
	 */
	public static function re_cnode(&$item,$cnstr,&$cnode){
		if(!isset($cnode['indexurl'])) cls_url::view_cnurl($cnstr,$cnode);
		for($i = 0;$i <= @$cnode['addnum'];$i ++) $item['indexurl'.($i ? $i : '')] = $cnode['indexurl'.($i ? $i : '')];
		$item['alias'] = empty($cnode['alias']) ? @$item['title'] : $cnode['alias'];
		$item['rss'] = cls_url::view_url('rss.php'.($cnstr ? "?$cnstr" : ''),FALSE);
	}

	function AddOneCnode($cnstr,$tid = 0,$oldupdate = 0,$NodeMode = 0){//$NodeModeΪ1����ڵ㣬1Ϊ�ֻ��ڵ�
		global $cn_max_addno,$db,$tblprefix,$timestamp;
		$tbl = $NodeMode ? 'o_cnodes' : 'cnodes';
		$NodeArray = cls_cache::Read($tbl);
		if(!$cnstr) return false;
		if(empty($NodeArray[$cnstr])){
			parse_str($cnstr,$arr);
			if(!$arr) return false;
			$sqlstr = "ename='$cnstr',cnlevel='".count($arr)."',tid='$tid'";
			foreach($arr as $k => $v) $sqlstr .= ",$k='$v'";
			if(!$NodeMode){
				$needstatics = '';for($i = 0;$i <= $cn_max_addno;$i ++) $needstatics .= $timestamp.',';
				$sqlstr .= ",needstatics='$needstatics'";
			}
			$db->query("INSERT INTO {$tblprefix}$tbl SET $sqlstr",'SILENT');
		}elseif($oldupdate) $db->query("UPDATE {$tblprefix}$tbl SET tid='$tid' WHERE ename='$cnstr' AND keeptid=0");
		return true;
	}

}
