<?php
/* 
** ��ͬ���͵��ֶε����ã�ʹ�÷�������
** ���cls_fieldconfig��ͬ����������չ���� ��public static function ex_demo()
*/
!defined('M_COM') && exit('No Permission');
class cls_field_media extends cls_fieldconfig{
	
	# ��֮��ͬ�����ֶ���ϱ༭����
    public static function _fm_custom_region(){
		
		self::_fm_notnull();
		self::_fm_guide();
		self::_fm_mode();
		self::_fm_rpid();
		self::_fm_cfgs();
		
	}
	# ����֮��ͬ�����ֶε����ݴ���
    public static function _sv_custom_region(){
		self::$newField['mode'] = empty(self::$fmdata['mode']) ? 0 : 1;
	}
	# ��֮���ؼ���ʾ�������б�
    protected static function _fm_mode(){
		$Value = empty(self::$oldField['mode']) ? 0 : 1;
		trbasic('���ؼ���ʾ�������б�','fmdata[mode]',$Value,'radio');
	}
}
