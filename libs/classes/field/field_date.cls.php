<?php
/* 
** ��ͬ���͵��ֶε����ã�ʹ�÷�������
** ��cls_fieldconfig��ͬ����������չ���� ��public static function ex_demo()
*/
!defined('M_COM') && exit('No Permission');
class cls_field_date extends cls_fieldconfig{
	
	# ��֮��ͬ�����ֶ���ϱ༭����
    public static function _fm_custom_region(){
		
		self::_fm_notnull();
		self::_fm_guide();
		self::_fm_mode();
		self::_fm_vdefault();
		self::_fm_min_max();
		self::_fm_search();
		self::_fm_cfgs();
	}
	# ����֮��ͬ�����ֶε����ݴ���
    public static function _sv_custom_region(){
		foreach(array('vdefault','min','max') as $key){
			self::$newField[$key] = trim(self::$fmdata[$key]);
			self::$newField[$key] = (self::$newField[$key] && (cls_string::isDate(self::$newField[$key]) || cls_string::isDate(self::$newField[$key], 1))) ? strtotime(self::$newField[$key]) : '';
		}
	}
	# ��֮���ڸ�ʽ
    protected static function _fm_mode(){
		$Value = self::$isNew ? 0 : self::$oldField['mode'];
		trbasic('���ڸ�ʽ','',makeradio('fmdata[mode]',array(0 => '��������', 1 => '����ʱ��'),$Value),'');
	}
	# ��֮Ĭ������ֵ
    protected static function _fm_vdefault(){
		$Value = empty(self::$oldField['vdefault']) ? '' : date(empty(self::$oldField['mode']) ? 'Y-m-d' : 'Y-m-d H:i:s',self::$oldField['vdefault']);
		trbasic('Ĭ������ֵ','', '<input type="text" id="fmdata[vdefault]" name="fmdata[vdefault]" value="'.$Value.'" onfocus="DateControl({format:\'fmdata[mode]\'})" class="Wdate" style="width:152px" rule="text" mode="date" />','');
	}	
	# ��֮���뷶Χ
    protected static function _fm_min_max(){
		$ValueMin = empty(self::$oldField['min']) ? '' : date(empty(self::$oldField['mode']) ? 'Y-m-d' : 'Y-m-d H:i:s',self::$oldField['min']);
		$ValueMax = empty(self::$oldField['max']) ? '' : date(empty(self::$oldField['mode']) ? 'Y-m-d' : 'Y-m-d H:i:s',self::$oldField['max']);
		trbasic('�������ڷ�Χ����','',	'<input type="text" id="fmdata[min]" name="fmdata[min]" value="'.$ValueMin.'" onfocus="DateControl({format:\'fmdata[mode]\'})" class="Wdate" style="width:152px" rule="text" mode="date" /> - ' .
										'<input type="text" id="fmdata[max]" name="fmdata[max]" value="'.$ValueMax.'" onfocus="DateControl({format:\'fmdata[mode]\'})" class="Wdate" style="width:152px" rule="text" mode="date" />','');
	}	
	
	
}
