<?php
/* 
** ��ͬ���͵��ֶε����ã�ʹ�÷�������
** ���cls_fieldconfig��ͬ����������չ���� ��public static function ex_demo()
*/
!defined('M_COM') && exit('No Permission');
class cls_field_multitext extends cls_fieldconfig{
	
	# ��֮��ͬ�����ֶ���ϱ༭����
    public static function _fm_custom_region(){
		self::_fm_notnull();
		self::_fm_guide();
		self::_fm_mode();
		self::_fm_min_max();
		self::_fm_nohtml();
		self::_fm_rpid();
		self::_fm_filter();
		self::_fm_wmid();
		self::_fm_cfgs();
	}
	# ����֮��ͬ�����ֶε����ݴ���
    public static function _sv_custom_region(){
		self::$newField['mode'] = empty(self::$fmdata['mode']) ? 0 : 1;
		foreach(array('min','max') as $key){
			self::$newField[$key] = max(0,intval(self::$fmdata[$key]));
			self::$newField[$key] = empty(self::$newField[$key]) ? '' : self::$newField[$key];
		}
	}
	# ��֮���ؼ�ģʽ
    protected static function _fm_mode(){
		$Value = empty(self::$oldField['mode']) ? 0 : 1;
		trbasic('���ؼ�ģʽ','',makeradio('fmdata[mode]',array(0 => '����ߴ�',1 => '�Ӵ�ߴ�'),$Value),'');
	}
	# ��֮����ֵ�ֽڳ�������
    protected static function _fm_min_max(){
		$ValueMin = empty(self::$oldField['min']) ? '' : self::$oldField['min'];
		$ValueMax = empty(self::$oldField['max']) ? '' : self::$oldField['max'];
		trrange('����ֵ�ֽڳ�������', array('fmdata[min]',$ValueMin,'','&nbsp; -&nbsp; ',5, 'validate' => makesubmitstr('fmdata[min]',0,'int')),
								array('fmdata[max]',$ValueMax,'','',5, 'validate' => makesubmitstr('fmdata[max]',0,'int')));
	}
	# ��֮�ύǰ����
    protected static function _fm_filter(){
		$Value = self::$isNew ? 0 : self::$oldField['filter'];
    	trbasic('�ύǰ����','fmdata[filter]',makeoption(array(0=>'������', 1=>'�����Թ���HTML'),$Value),'select');
	}
	
}
