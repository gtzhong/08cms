<?php
/* 
** ���û��йصĻ����������ܣ���cls_UserMain�Ļ���
** �ܹ���ͼ���ṹ�������Ӧ��ģ���г��ã�ͨ���Ծ�̬��������
** ע�⣺Ϊ���ڻ�����ʹ����չ�ľ�̬�������ڻ�����ʹ�ã���չ��::method�����ʹ�ã�self::method������֧����չ��
*/
!defined('M_COM') && exit('No Permission');
class cls_UserMainBase{
	
	protected static $CurUser = NULL;		# ��ǰ��Աʵ��
	
	# ���ػ�Ա��Ϣ�е�δ��֤�ֶ���Ϣ
	public static function CurUser(){
		if(empty(self::$CurUser)){
			self::$CurUser = new cls_userinfo();
			self::$CurUser->currentuser();
		}
		return self::$CurUser;
	}
	
	# ���ػ�Ա��Ϣ�е�δ��֤�ֶ���Ϣ
	public static function HiddenUncheckCertField(&$info){
		if(empty($info) || !is_array($info) || empty($info['mid'])) return;
		$mctypes = cls_cache::Read('mctypes');
		foreach($mctypes as $k => $v){
			if($v['available'] && !empty($v['field']) && !empty($info[$v['field']]) && empty($info["mctid$k"]) && strstr(",$v[mchids],",",".$info['mchid'].",")){
				$info[$v['field']] = '';
			}
		}
	}
	
	/**
	 * ��Ա������ǰ̨ģ������д����ݿ�����ж�������Ҫ׷�Ӵ��������
	 *
	 * @param  array     &$info			��Ա��������
	 * @param  bool      $inList		�Ƿ����б��У����б��л��һЩ��������,�˲��������ã��ݷ�������ݾɰ汾
	 * @return NULL   ---       --- 
	 */
	function Parse(&$info,$inList = false){	
		#if(defined('IN_MOBILE') && !$inList) cls_atm::arr_image2mobile($info,'m');//��<!cmsurl>ת��֮ǰִ�У������ֻ�����html��ͼƬ��С
		defined('IN_MOBILE') || cls_url::arr_tag2atm($info,'m');
		$info['mspacehome'] = cls_Mspace::IndexUrl($info);
		cls_UserMain::HiddenUncheckCertField($info);
		$grouptypes = cls_cache::Read('grouptypes');
		foreach($grouptypes as $k => $v){
			$info['grouptype'.$k.'name'] = '';
			if(!empty($info['grouptype'.$k])){
				$usergroups = cls_cache::Read('usergroups',$k);
				$info['grouptype'.$k.'name'] = $usergroups[$info['grouptype'.$k]]['cname'];
			}
		}
	}
}
