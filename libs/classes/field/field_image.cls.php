<?php
/* 
** ��ͬ���͵��ֶε����ã�ʹ�÷�������
** ���cls_fieldconfig��ͬ����������չ���� ��public static function ex_demo()
*/
!defined('M_COM') && exit('No Permission');
class cls_field_image extends cls_fieldconfig{
	
	# ��֮��ͬ�����ֶ���ϱ༭����
    public static function _fm_custom_region(){
		
		self::_fm_notnull();
		self::_fm_guide();
		self::_fm_rpid();
		self::_fm_wmid();
        parent::_fm_autoCompression();
		self::_fm_cfgs();
	}
}
