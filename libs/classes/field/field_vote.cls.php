<?php
/* 
** ��ͬ���͵��ֶε����ã�ʹ�÷�������
** ���cls_fieldconfig��ͬ����������չ���� ��public static function ex_demo()
*/
!defined('M_COM') && exit('No Permission');
class cls_field_vote extends cls_fieldconfig{
	
	# ��֮��ͬ�����ֶ���ϱ༭����
    public static function _fm_custom_region(){
		self::_fm_notnull();
		self::_fm_guide();
		self::_fm_min();
		self::_fm_max();
		self::_fm_nohtml();
		self::_fm_mode();
		self::_fm_length();
		self::_fm_cfgs();
	}
	# ����֮��ͬ�����ֶε����ݴ���
    public static function _sv_custom_region(){
		
		# ���������Ӽ���ͶƱ,ÿ��ͶƱ��༸��ѡ��
		foreach(array('min','max') as $key){
			self::$newField[$key] = max(1,intval(self::$fmdata[$key]));
		}
		
	}
	# ��֮���������Ӽ���ͶƱ
    protected static function _fm_min(){
		$Value = empty(self::$oldField['min']) ? 1 : self::$oldField['min'];
		trbasic('���������Ӽ���ͶƱ','fmdata[min]',$Value,'text', array('validate' => makesubmitstr('fmdata[min]',1,'int',1,50,'int')));
	}
	# ��֮ÿ��ͶƱ��༸��ѡ��
    protected static function _fm_max(){
		$Value = empty(self::$oldField['max']) ? 1 : self::$oldField['max'];
		trbasic('ÿ��ͶƱ��༸��ѡ��','fmdata[max]',$Value,'text', array('validate' => makesubmitstr('fmdata[max]',1,'int',1,20,'int')));
	}
	# ��֮��ֹ�ο�ͶƱ
    protected static function _fm_nohtml(){
		$Value = self::$isNew ? 0 : self::$oldField['nohtml'];
		trbasic('��ֹ�ο�ͶƱ','fmdata[nohtml]',$Value,'radio');
	}
	# ��֮�����ظ�ͶƱ
    protected static function _fm_mode(){
		$Value = empty(self::$oldField['mode']) ? 0 : 1;
		trbasic('�����ظ�ͶƱ','fmdata[mode]',$Value,'radio');
	}
	# ��֮�ظ�ͶƱʱ����(����)
    protected static function _fm_length(){
		$Value = self::$isNew ? '' : self::$oldField['length'];
		trbasic('�ظ�ͶƱʱ����(����)','fmdata[length]',$Value,'text', array('validate' => makesubmitstr('fmdata[length]',0,'int',0,300,'int')));
	}
	

}
