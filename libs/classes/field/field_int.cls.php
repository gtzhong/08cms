<?php
/* 
** ��ͬ���͵��ֶε����ã�ʹ�÷�������
** ���cls_fieldconfig��ͬ����������չ���� ��public static function ex_demo()
*/
!defined('M_COM') && exit('No Permission');
class cls_field_int extends cls_fieldconfig{
	
	# ��֮��ͬ�����ֶ���ϱ༭����
    public static function _fm_custom_region(){
		
		self::_fm_notnull();
		self::_fm_guide();
		self::_fm_vdefault();
		self::_fm_min_max();
		self::_fm_regular();
		self::_fm_search();
		self::_fm_cfgs();
		
	}
	# ����֮��ͬ�����ֶε����ݴ���
    public static function _sv_custom_region(){
		foreach(array('vdefault','min','max') as $key){
			self::$newField[$key] = trim(self::$fmdata[$key]);
			if(self::$newField[$key] != '') self::$newField[$key] = intval(self::$newField[$key]);
		}
	}
	# ��֮Ĭ������ֵ
    protected static function _fm_vdefault(){
		$Value = self::$isNew ? '' : self::$oldField['vdefault'];
		trbasic('Ĭ������ֵ','fmdata[vdefault]',$Value,'text',array('validate'=>makesubmitstr('fmdata[vdefault]',0,'int',0,11)));
	}
	# ��֮����ֵ��Χ����
    protected static function _fm_min_max(){
		$ValueMin = empty(self::$oldField['min']) ? '' : self::$oldField['min'];
		$ValueMax = empty(self::$oldField['max']) ? '' : self::$oldField['max'];
		trrange('����ֵ��Χ����', array('fmdata[min]',$ValueMin,'','&nbsp; -&nbsp; ',5, 'validate' => makesubmitstr('fmdata[min]',0,'int',0,11)),
								array('fmdata[max]',$ValueMax,'','',5, 'validate' => makesubmitstr('fmdata[max]',0,'int',0,11)));
	}
	
		
}
