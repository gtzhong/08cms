<?php
/**
 * ��ҳ�����ɾ�̬�����йص�һЩ��������
 */
defined('M_COM') || exit('No Permission');
abstract class cls_StaticBase{
	
	# �Ƿ�Ϊ�Զ����¾�̬����ͣʱ��	
	# PausePeriod�������̨���õĲ�ͬ����ҳ��ľ�̬��ͣʱ�Σ���ʽ��"10-12,15,20-22"��ʾÿ�յ�Сʱ��
	public static function InParsePeriod($PausePeriod = ''){
		if($PausePeriod && $na = explode(',',$PausePeriod)){
			$nh = date('G',TIMESTAMP);
			foreach($na as $k){
				if(strpos($k,'-') !== FALSE){
					$xy = explode('-',$k);
					if(($nh >= $xy[0]) && ($nh <= $xy[1])) return true;
				}elseif($k == $nh) return true;
			}
		}
		return false;
	}
	
}
