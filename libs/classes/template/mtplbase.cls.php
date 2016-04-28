<?php
/* 
** ����ģ���ķ������ܣ���mtpl.cls.php�Ļ���
** ע�⣺Ϊ���ڻ�����ʹ����չ�ľ�̬�������ڻ�����ʹ�ã���չ��::method�����ʹ�ã�self::method������֧����չ��
*/
!defined('M_COM') && exit('No Permission');
abstract class cls_mtplbase{
	
	# ȡ�ó���ģ���ķ�������
	# $NodeMode=1Ϊ�ֻ���
	public static function ClassArray($NodeMode = 0){
		$ClassArray = array(
			'index' => 'վ����ҳ',
			'cindex' => '��Ŀ�ڵ�',
			'archive' => '�ĵ�ģ��',
			'freeinfo' => '������Ϣ',
			'marchive' => '��Ա���',
			'space' => '��Ա�ռ�',
			'special' => '����ҳ��',
			'other' => '����ģ��',
			'xml' => 'RSS/SiteMap',
		);
		if($NodeMode){
			foreach(array('marchive','space','other','xml') as $k){
				unset($ClassArray[$k]);
			}
		}
		return $ClassArray;
	}
	
	
	# ����ģ����ָ����
	# $ismobile : �Ƿ�Ϊ�ֻ�ģ��
	public static function mtplGuide($Class = 'index',$OnlyUrl = false,$ismobile = 0){
		$ClassArray = cls_mtpl::ClassArray();
		$re = '';
		if(!empty($ClassArray[$Class])){
			$re = ">>";
			$re .= "<a href=\"?entry=".($ismobile ? 'o_' : '')."mtpls&action=mtplsedit&tpclass=$Class&isframe=1\" target=\"_08cms_mtpl\">";
			$re .= $OnlyUrl ? "ģ���" : ("����ģ���-".$ClassArray[$Class]);
			$re .= "</a>";
		}
		return $re;
	}
	
	
	
	/**
	 * ȡ�ó���ģ����в�ͬ����ģ���ѡ������
	 *
	 * @param  string $tpclass 	ģ������
	 * @param  int $chid 		�ĵ�ģ��chid�����ڵ��ĵ�ָ��ģ��ʱ�����ض�ģ���йص�ģ��
	 * @return array			����ģ������
	 */
	public static function mtplsarr($tpclass = 'archive',$chid = 0){
		$mtpls = cls_cache::Read('mtpls');
		$re = array();
		if(empty($mtpls)) return $re;
		foreach($mtpls as $k => $v) {
			if($v['tpclass'] == $tpclass){
				if(!$chid || $chid == @$v['chid']){
					$re[$k] = $v['cname'].' '.$k;
				}
			}
		}
		return $re;
	}
	
	/**
	 * ȡ���ֻ�ģ����в�ͬ����ģ���ѡ������
	 *
	 * @param  string $tpclass 	ģ������
	 * @return array			����ģ������
	 */
	public static function o_mtplsarr($tpclass = 'archive'){
		$o_mtpls = cls_cache::Read('o_mtpls');
		$re = array();
		if(empty($o_mtpls)) return $re;
		foreach($o_mtpls as $k => $v) {
			if($v['tpclass'] == $tpclass){
				$re[$k] = $v['cname'].' '.$k;
			}
		}
		return $re;
	}
	
	
}
