<?php
/* 
** ���ĵ��йصĻ����������ܣ���cls_ArcMain�Ļ���
** �ܹ���ͼ���ṹ�������Ӧ��ģ���г��ã�ͨ���Ծ�̬��������
** ע�⣺Ϊ���ڻ�����ʹ����չ�ľ�̬�������ڻ�����ʹ�ã���չ��::method�����ʹ�ã�self::method������֧����չ��
*/
!defined('M_COM') && exit('No Permission');
class cls_ArcMainBase{
	
	
	/**
	 * ��ȡ�ĵ�����ҳ��url
	 * 
	 * @param  array 	$archive �ĵ����������飬aid,chid,caid,initdate,customurl,nowurl,jumpurlһ������$archive��,(��ѡ)nodemode�ֻ�����
	 * @param  int		$addno     -1Ϊȫ��ҳ��(����Ա�ռ�����ҳ),�����أ����򷵻�ָ������ҳ��URL
	 */
	public static function Url(&$archive,$addno = 0){
		$arc_tpl = cls_tpl::arc_tpl($archive['chid'],$archive['caid'],!empty($archive['nodemode']));
		
		if($addno == -1){
			$AddnoArray = array();
			for($i = 0;$i <= @$arc_tpl['addnum'];$i ++){
				$AddnoArray[] = $i;
			}
		}else{
			$AddnoArray = array((int)$addno);
		}
		
		if(!empty($archive['jumpurl'])){ # ��תUrl
			foreach($AddnoArray as $k){
				$archive['arcurl'.($k ? $k : '')] =  cls_url::view_url($archive['jumpurl'],false);
			}
			if($addno == -1) $archive['marcurl'] = cls_url::view_url($archive['jumpurl'],false);
		}elseif(!empty($archive['nodemode'])){ # �ֻ���
            $get = cls_env::_GET('is_weixin');
			foreach($AddnoArray as $k){
			    $key = 'arcurl'.($k ? $k : '');
				$archive[$key] = cls_url::view_url(cls_env::mconfig('mobiledir')."/archive.php?aid=$archive[aid]".($k ? "&addno=$k" : ''));
                if (!empty($get['is_weixin']))
                {
                    $archive[$key] .= "&is_weixin=1";
                }
			}
		}else{ # ����Url�����ڶ���̬
			$archive = ArchiveStaticFormat($archive);
			foreach($AddnoArray as $k){
				if(isset($archive['arcurl'.($k ? $k : '')])) continue; # �����ظ�ִ��
				if(empty($arc_tpl['cfg'][$k]['static']) ? cls_env::mconfig('enablestatic') : 0){ # ��̬Url
					if($archive['nowurl']){
						$archive['arcurl'.($k ? $k : '')] = cls_url::view_url(cls_url::m_parseurl($archive['nowurl'],array('addno' => arc_addno($k,@$arc_tpl['cfg'][$k]['addno']),'page' => 1,)));
					}else $archive['arcurl'.($k ? $k : '')] = '#';
				}else{ # ��̬Url
					$archive['arcurl'.($k ? $k : '')] = cls_url::view_url(cls_url::en_virtual("archive.php?aid=$archive[aid]".($k ? "&addno=$k" : ''),@$arc_tpl['cfg'][$k]['novu']));
				}
			}
			if(!empty($archive['mid']) && $addno == -1){
				$archive['marcurl'] = cls_url::view_url(cls_env::mconfig('mspaceurl').cls_url::en_virtual("archive.php?mid=".$archive['mid']."&aid=".$archive['aid']));
			}
		}
		return $addno == -1 ? true : $archive['arcurl'.($addno ? $addno : '')];
	}
	
	/**
	 * �ĵ���ģ������д����ݿ�����ж�������Ҫ׷�Ӵ��������
	 *
	 * @param  array     &$archive		�ĵ���������
	 * @param  bool      $inList		�Ƿ����б��У����б��л��һЩ��������
	 * @return NULL   ---       --- 
	 */
	function Parse(&$archive,$inList = false){
		cls_ArcMain::Url($archive,-1);	
		#if(!empty($archive['nodemode']) && !$inList) cls_atm::arr_image2mobile($archive);//��<!cmsurl>ת��֮ǰִ�У������ֻ�����html��ͼƬ��С
		if(empty($archive['nodemode'])) cls_url::arr_tag2atm($archive);//pc����ǰ����html��<!cmsurl>ת������
		$cotypes = cls_cache::Read('cotypes');
		$catalogs = cls_cache::Read('catalogs');
		$archive['catalog'] = $catalogs[$archive['caid']]['title'];
		foreach($cotypes as $k => $v){
			if(isset($archive["ccid$k"])){
				$archive['ccid'.$k.'title'] = empty($archive["ccid$k"]) ? '' : cls_catalog::cnstitle($archive["ccid$k"],$v['asmode'],cls_cache::Read('coclasses',$k));
			}		
		}
	}
	
	/**
	 * ��ǰ��Ա�Ƿ���Ȩ�����������ĵ��еĸ���//�鸽����ֵ�����˷�Χ
	 *
	 * @param  int     $archive		�ĵ���������
	 * @return bool    --- 			�Ƿ���Ȩ����������
	 */
	function AllowDown($archive){//��ǰ��Ա�Ƿ���Ȩ�����������ĵ��еĸ���//�鸽����ֵ�����˷�Χ
		$curuser = cls_env::GetG('curuser');
		if($curuser->isadmin()) return true;
		if($curuser->info['mid'] && $curuser->info['mid'] == $archive['mid']) return true;//�����߱���
		$pmid = 0;
		if(empty($archive['dpmid'])){
			return true;//���ĵ���ȫ����
		}elseif($archive['dpmid'] == -1){//�̳���ĿȨ��
			$catalog = cls_cache::Read('catalog',$archive['caid']);
			if(!empty($catalog['dpmid'])) $pmid = $catalog['dpmid'];
			unset($catalog);
		}else $pmid = $archive['dpmid'];//���ĵ����õ�Ȩ�޷���
		return $curuser->pmbypmid($pmid);
	}
	
}
